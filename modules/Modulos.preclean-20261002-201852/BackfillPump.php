<?php

if (PHP_SAPI !== 'cli') {
	fwrite(STDERR, "Este proceso solo se puede ejecutar desde CLI.\n");
	exit(1);
}

$rootDirectory = dirname(__DIR__, 2);
chdir($rootDirectory);

function backfillOption($name, $defaultValue) {
	global $argv;
	$prefix = '--' . $name . '=';
	foreach ($argv as $argument) {
		if (strpos($argument, $prefix) === 0) {
			return substr($argument, strlen($prefix));
		}
	}
	return $defaultValue;
}

if (in_array('--help', $argv, true) || in_array('-h', $argv, true)) {
	echo "Uso: php modules/Modulos/BackfillPump.php [--batch-size=20]\n";
	echo "Procesa cuentas activas que aún no tienen su instancia PUMP. Es seguro volver a ejecutarlo.\n";
	exit(0);
}

require_once 'vendor/autoload.php';
require_once 'config.php';
require_once 'include/utils/utils.php';
require_once 'modules/Users/Users.php';
require_once 'modules/Modulos/ModuloEstacionDuplicateHandler.php';

$batchSize = filter_var(backfillOption('batch-size', 20), FILTER_VALIDATE_INT);
if ($batchSize === false || $batchSize < 1 || $batchSize > 200) {
	fwrite(STDERR, "--batch-size debe ser un entero entre 1 y 200.\n");
	exit(1);
}

$lockPath = sys_get_temp_dir() . '/vtiger-modulos-pump-' . md5(realpath($rootDirectory)) . '.lock';
$lockHandle = fopen($lockPath, 'c');
if (!$lockHandle || !flock($lockHandle, LOCK_EX | LOCK_NB)) {
	fwrite(STDERR, "Ya hay otro backfill PUMP en ejecución para esta instalación.\n");
	exit(1);
}

try {
	global $adb, $current_user;
	$adb = PearDatabase::getInstance();
	$current_user = Users::getActiveAdminUser();

	$requiredTables = array('vtiger_account', 'vtiger_modulos', 'vtiger_moduloestacion');
	foreach ($requiredTables as $table) {
		if (!Vtiger_Utils::CheckTable($table)) {
			throw new Exception("No existe $table. Confirme que la instalación del módulo terminó correctamente.");
		}
	}

	$totalResult = $adb->pquery(
		'SELECT COUNT(*) AS total FROM vtiger_account AS account'
		. ' INNER JOIN vtiger_crmentity AS entity ON entity.crmid=account.accountid AND entity.deleted=0',
		array()
	);
	$total = (int)$adb->query_result($totalResult, 0, 'total');
	$processedTotal = 0;
	$lastAccountId = 0;

	echo "Cuentas activas: $total; tamaño de lote: $batchSize.\n";
	while (true) {
		$batch = ModuloEstacionDuplicateHandler::ensurePumpForAccountsBatch($lastAccountId, $batchSize);
		if (!$batch['processed']) {
			break;
		}
		$processedTotal += $batch['processed'];
		$lastAccountId = $batch['last_account_id'];
		echo "Procesadas $processedTotal de $total cuentas (último ID: $lastAccountId).\n";
		if (function_exists('ob_flush')) {
			@ob_flush();
		}
		flush();
	}

	echo "Backfill PUMP completado.\n";
} catch (Throwable $error) {
	fwrite(STDERR, "Backfill PUMP detenido: " . $error->getMessage() . "\n");
	fwrite(STDERR, "Puede corregir la causa y volver a ejecutar el mismo comando; no duplica instancias existentes.\n");
	exit(1);
} finally {
	flock($lockHandle, LOCK_UN);
	fclose($lockHandle);
}