<?php

require_once 'include/events/VTEventHandler.inc';

class ModuloEstacionDuplicateHandler extends VTEventHandler {
	public function handleEvent($eventName, $entityData) {
		if ($eventName !== 'vtiger.entity.beforesave' || $entityData->getModuleName() !== 'ModuloEstacion') {
			return;
		}

		$data = $entityData->getData();
		$accountId = (int)$data['account_id'];
		$moduleId = (int)$data['modulo_id'];
		if (!$accountId || !$moduleId) {
			return;
		}

		global $adb;
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
		}

		$recordId = (int)$entityData->focus->id;
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
}