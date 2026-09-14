<?php

chdir(__DIR__);

require_once 'vendor/autoload.php';
require_once 'includes/main/WebUI.php';
require_once 'vtlib/Vtiger/Module.php';

$accounts = Vtiger_Module::getInstance('Accounts');
$modulos = Vtiger_Module::getInstance('Modulos');

if (!$accounts || !$modulos) {
    throw new Exception('Accounts y Modulos deben existir antes de crear la relación.');
}

$accounts->setRelatedList($modulos, 'Modulos', array('ADD', 'SELECT'));

echo "Relación Accounts -> Modulos creada correctamente.\n";
echo "La tabla de relación es vtiger_crmentityrel.\n";
