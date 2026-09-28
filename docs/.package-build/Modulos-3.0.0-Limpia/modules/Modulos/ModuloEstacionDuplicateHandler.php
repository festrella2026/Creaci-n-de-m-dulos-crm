<?php

require_once 'include/events/VTEventHandler.inc';

class ModuloEstacionDuplicateHandler extends VTEventHandler {
	public function handleEvent($eventName, $entityData) {
		$moduleName = $entityData->getModuleName();
		if ($moduleName === 'Accounts' && in_array($eventName, array('vtiger.entity.aftersave', 'vtiger.entity.afterrestore'), true)) {
			self::ensurePumpForAccount((int)$entityData->getId());
			return;
		}
		if ($moduleName === 'Modulos') {
			self::protectCatalog($eventName, $entityData);
			return;
		}

		if ($moduleName !== 'ModuloEstacion') {
			return;
		}

		if ($eventName === 'vtiger.entity.beforedelete') {
			self::rejectPumpDeletion($entityData);
			return;
		}
		if ($eventName !== 'vtiger.entity.beforesave') {
			return;
		}

		$data = $entityData->getData();
		$accountId = (int)$data['account_id'];
		$moduleId = (int)$data['modulo_id'];
		if (!$accountId || !$moduleId) {
			return;
		}

		global $adb;
		$pumpId = self::getPumpCatalogId();
		$recordId = (int)$entityData->getId();
		if ($recordId && $pumpId) {
			$previous = $adb->pquery(
				'SELECT account_id, modulo_id FROM vtiger_moduloestacion WHERE moduloestacionid=?',
				array($recordId)
			);
			if ($previous && $adb->num_rows($previous)) {
				$previousModuleId = (int)$adb->query_result($previous, 0, 'modulo_id');
				$previousAccountId = (int)$adb->query_result($previous, 0, 'account_id');
				if ($previousModuleId === $pumpId && ($moduleId !== $pumpId || $accountId !== $previousAccountId)) {
					throw new Exception('PUMP es obligatorio para todas las estaciones y no se puede desvincular.');
				}
			}
		}

		$labels = $adb->pquery(
			'SELECT account.accountname, module.modulos_name'
			. ' FROM vtiger_account AS account'
			. ' INNER JOIN vtiger_crmentity AS account_entity ON account_entity.crmid=account.accountid AND account_entity.deleted=0'
			. ' INNER JOIN vtiger_modulos AS module ON module.modulosid=?'
			. ' INNER JOIN vtiger_crmentity AS module_entity ON module_entity.crmid=module.modulosid AND module_entity.deleted=0'
			. ' WHERE account.accountid=?',
			array($moduleId, $accountId)
		);
		if ($labels && $adb->num_rows($labels)) {
			$accountName = $adb->query_result($labels, 0, 'accountname');
			$moduleName = $adb->query_result($labels, 0, 'modulos_name');
			$entityData->focus->column_fields['moduloestacion_name'] = $accountName . ' - ' . $moduleName;
			if (strtoupper(trim($moduleName)) === 'PINPAD' && isset($data['pinpad_cantidad_equipos']) && $data['pinpad_cantidad_equipos'] !== '' && $data['pinpad_cantidad_equipos'] !== null) {
				$pinpadCount = (int)$data['pinpad_cantidad_equipos'];
				if ($pinpadCount < 2 || $pinpadCount > 10) {
					throw new Exception('La cantidad de equipos PINPAD debe estar entre 2 y 10.');
				}
			}
		}

		$query = 'SELECT 1 FROM vtiger_moduloestacion AS instance'
			. ' INNER JOIN vtiger_crmentity AS entity ON entity.crmid=instance.moduloestacionid'
			. ' WHERE entity.deleted=0 AND instance.account_id=? AND instance.modulo_id=?';
		$params = array($accountId, $moduleId);
		if ($recordId) {
			$query .= ' AND instance.moduloestacionid<>?';
			$params[] = $recordId;
		}

		$result = $adb->pquery($query, $params);
		if ($result && $adb->num_rows($result)) {
			throw new Exception('Este módulo ya está asociado a esta estación.');
		}
	}

	public static function register($catalogModule, $accountsModule, $instanceModule) {
		foreach (array('vtiger.entity.beforesave', 'vtiger.entity.beforedelete') as $eventName) {
			Vtiger_Event::register(
				$catalogModule,
				$eventName,
				'ModuloEstacionDuplicateHandler',
				'modules/Modulos/ModuloEstacionDuplicateHandler.php'
			);
			Vtiger_Event::register(
				$instanceModule,
				$eventName,
				'ModuloEstacionDuplicateHandler',
				'modules/Modulos/ModuloEstacionDuplicateHandler.php'
			);
		}
		foreach (array('vtiger.entity.aftersave', 'vtiger.entity.afterrestore') as $eventName) {
			Vtiger_Event::register(
				$accountsModule,
				$eventName,
				'ModuloEstacionDuplicateHandler',
				'modules/Modulos/ModuloEstacionDuplicateHandler.php'
			);
		}
	}

	private static function protectCatalog($eventName, $entityData) {
		if (!in_array($eventName, array('vtiger.entity.beforesave', 'vtiger.entity.beforedelete'), true)) {
			return;
		}

		global $adb;
		$recordId = (int)$entityData->getId();
		if ($eventName === 'vtiger.entity.beforedelete') {
			if (!$recordId) {
				return;
			}
			$result = $adb->pquery('SELECT modulos_name FROM vtiger_modulos WHERE modulosid=?', array($recordId));
			if ($result && $adb->num_rows($result) && strtoupper(trim($adb->query_result($result, 0, 'modulos_name'))) === 'PUMP') {
				throw new Exception('PUMP es obligatorio para todas las estaciones y no se puede eliminar del catálogo.');
			}
			return;
		}

		$data = $entityData->getData();
		$name = trim((string)$data['modulos_name']);
		if ($name === '') {
			return;
		}
		if ($recordId) {
			$current = $adb->pquery('SELECT modulos_name FROM vtiger_modulos WHERE modulosid=?', array($recordId));
			if ($current && $adb->num_rows($current) && strtoupper(trim($adb->query_result($current, 0, 'modulos_name'))) === 'PUMP' && strtoupper($name) !== 'PUMP') {
				throw new Exception('PUMP es obligatorio para todas las estaciones y no se puede renombrar.');
			}
		}
		if (strtoupper($name) === 'PUMP') {
			$entityData->focus->column_fields['obligatorio_todas_estaciones'] = 1;
			$entityData->focus->column_fields['estado_catalogo'] = 'Activo';
		}

		$duplicates = $adb->pquery(
			'SELECT catalog.modulosid FROM vtiger_modulos AS catalog'
			. ' INNER JOIN vtiger_crmentity AS entity ON entity.crmid=catalog.modulosid AND entity.deleted=0'
			. ' WHERE UPPER(TRIM(catalog.modulos_name))=? AND catalog.modulosid<>?',
			array(strtoupper($name), $recordId)
		);
		if ($duplicates && $adb->num_rows($duplicates)) {
			throw new Exception('Ya existe este tipo de módulo en el catálogo.');
		}
	}

	private static function getPumpCatalogId() {
		global $adb;
		$result = $adb->pquery(
			'SELECT catalog.modulosid FROM vtiger_modulos AS catalog'
			. ' INNER JOIN vtiger_crmentity AS entity ON entity.crmid=catalog.modulosid AND entity.deleted=0'
			. ' WHERE UPPER(TRIM(catalog.modulos_name))=? ORDER BY catalog.modulosid LIMIT 1',
			array('PUMP')
		);
		return $result && $adb->num_rows($result) ? (int)$adb->query_result($result, 0, 'modulosid') : 0;
	}

	public static function ensurePumpCatalogRecord() {
		self::ensureCatalogRecords();
	}

	public static function ensureCatalogRecords() {
		global $adb, $current_user;
		$ownerId = isset($current_user->id) ? (int)$current_user->id : 0;
		if (!$ownerId) {
			$result = $adb->pquery('SELECT id FROM vtiger_users WHERE status=? ORDER BY is_admin DESC, id LIMIT 1', array('Active'));
			$ownerId = $result && $adb->num_rows($result) ? (int)$adb->query_result($result, 0, 'id') : 1;
		}

		$catalog = array(
			array('PUMP', 'MOD-PUMP', 1),
			array('PINPAD', 'MOD-PINPAD', 0),
			array('TANK', 'MOD-TANK', 0),
			array('LUBRICANTS', 'MOD-LUBRICANTS', 0),
			array('PROMOS', 'MOD-PROMOS', 0),
			array('PRICE TOTEM', 'MOD-PRICE-TOTEM', 0),
			array('FLEET BUS', 'MOD-FLEET-BUS', 0),
			array('PRINTER', 'MOD-PRINTER', 0),
			array('BARCODE', 'MOD-BARCODE', 0),
			array('RFID HF', 'MOD-RFID-HF', 0),
			array('AVI RFID', 'MOD-AVI-RFID', 0)
		);

		foreach ($catalog as $catalogItem) {
			$name = $catalogItem[0];
			$existing = $adb->pquery(
				'SELECT catalog.modulosid FROM vtiger_modulos AS catalog'
				. ' INNER JOIN vtiger_crmentity AS entity ON entity.crmid=catalog.modulosid AND entity.deleted=0'
				. ' WHERE UPPER(TRIM(catalog.modulos_name))=? ORDER BY catalog.modulosid LIMIT 1',
				array($name)
			);
			$catalogRecord = CRMEntity::getInstance('Modulos');
			if ($existing && $adb->num_rows($existing)) {
				$catalogId = (int)$adb->query_result($existing, 0, 'modulosid');
				$catalogRecord->retrieve_entity_info($catalogId, 'Modulos');
				$catalogRecord->id = $catalogId;
				$catalogRecord->mode = 'edit';
			} else {
				$catalogRecord->column_fields['modulos_name'] = $name;
				$catalogRecord->column_fields['codigo'] = $catalogItem[1];
				$catalogRecord->column_fields['estado_catalogo'] = 'Activo';
				$catalogRecord->column_fields['assigned_user_id'] = $ownerId;
			}
			$catalogRecord->column_fields['obligatorio_todas_estaciones'] = $catalogItem[2];
			$catalogRecord->save('Modulos');
		}
	}

	private static function ensurePumpForAccount($accountId) {
		if (!$accountId) {
			return;
		}

		global $adb, $current_user;
		$pumpId = self::getPumpCatalogId();
		if (!$pumpId) {
			return;
		}
		$exists = $adb->pquery(
			'SELECT 1 FROM vtiger_moduloestacion AS instance'
			. ' INNER JOIN vtiger_crmentity AS entity ON entity.crmid=instance.moduloestacionid AND entity.deleted=0'
			. ' WHERE instance.account_id=? AND instance.modulo_id=? LIMIT 1',
			array($accountId, $pumpId)
		);
		if ($exists && $adb->num_rows($exists)) {
			return;
		}

		$account = $adb->pquery(
			'SELECT account.accountname, entity.smownerid FROM vtiger_account AS account'
			. ' INNER JOIN vtiger_crmentity AS entity ON entity.crmid=account.accountid AND entity.deleted=0'
			. ' WHERE account.accountid=?',
			array($accountId)
		);
		if (!$account || !$adb->num_rows($account)) {
			return;
		}

		$accountName = $adb->query_result($account, 0, 'accountname');
		$ownerId = (int)$adb->query_result($account, 0, 'smownerid');
		if (!$ownerId && isset($current_user->id)) {
			$ownerId = (int)$current_user->id;
		}
		$instance = CRMEntity::getInstance('ModuloEstacion');
		$instance->column_fields['moduloestacion_name'] = $accountName . ' - PUMP';
		$instance->column_fields['account_id'] = $accountId;
		$instance->column_fields['modulo_id'] = $pumpId;
		$instance->column_fields['estado_instalacion'] = 'No instalado';
		$instance->column_fields['assigned_user_id'] = $ownerId;
		$instance->save('ModuloEstacion');
	}

	public static function ensurePumpForAllAccounts() {
		global $adb;
		$accounts = $adb->pquery(
			'SELECT account.accountid FROM vtiger_account AS account'
			. ' INNER JOIN vtiger_crmentity AS entity ON entity.crmid=account.accountid AND entity.deleted=0',
			array()
		);
		for ($index = 0; $index < $adb->num_rows($accounts); $index++) {
			self::ensurePumpForAccount((int)$adb->query_result($accounts, $index, 'accountid'));
		}
	}

	private static function rejectPumpDeletion($entityData) {
		global $adb;
		$pumpId = self::getPumpCatalogId();
		if (!$pumpId) {
			return;
		}
		$result = $adb->pquery(
			'SELECT modulo_id FROM vtiger_moduloestacion WHERE moduloestacionid=?',
			array((int)$entityData->getId())
		);
		if ($result && $adb->num_rows($result) && (int)$adb->query_result($result, 0, 'modulo_id') === $pumpId) {
			throw new Exception('PUMP es obligatorio para todas las estaciones y no se puede eliminar.');
		}
	}
}