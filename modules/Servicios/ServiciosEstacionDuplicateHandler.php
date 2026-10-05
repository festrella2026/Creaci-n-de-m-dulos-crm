<?php

require_once 'include/events/VTEventHandler.inc';

class ServiciosEstacionDuplicateHandler extends VTEventHandler {
    public function handleEvent($eventName, $entityData) {
        $moduleName = $entityData->getModuleName();
        if ($moduleName === 'Servicios') {
            self::validateCatalog($eventName, $entityData);
            return;
        }

        if ($moduleName !== 'ServiciosEstacion') {
            return;
        }

        if ($eventName !== 'vtiger.entity.beforesave') {
            return;
        }

        $data = $entityData->getData();
        $accountId = (int)$data['account_id'];
        $serviceId = (int)$data['servicio_id'];
        if (!$accountId || !$serviceId) {
            return;
        }

        global $adb;
        $recordId = (int)$entityData->getId();

        $labels = $adb->pquery(
            'SELECT account.accountname, service.servicios_name'
            . ' FROM vtiger_account AS account'
            . ' INNER JOIN vtiger_crmentity AS account_entity ON account_entity.crmid=account.accountid AND account_entity.deleted=0'
            . ' INNER JOIN vtiger_servicios AS service ON service.serviciosid=?'
            . ' INNER JOIN vtiger_crmentity AS service_entity ON service_entity.crmid=service.serviciosid AND service_entity.deleted=0'
            . ' WHERE account.accountid=?',
            array($serviceId, $accountId)
        );

        if ($labels && $adb->num_rows($labels)) {
            $accountName = $adb->query_result($labels, 0, 'accountname');
            $serviceName = $adb->query_result($labels, 0, 'servicios_name');
            $entityData->focus->column_fields['serviciosestacion_name'] = $accountName . ' - ' . $serviceName;
        }

        $query = 'SELECT 1 FROM vtiger_serviciosestacion AS instance'
            . ' INNER JOIN vtiger_crmentity AS entity ON entity.crmid=instance.serviciosestacionid'
            . ' WHERE entity.deleted=0 AND instance.account_id=? AND instance.servicio_id=?';
        $params = array($accountId, $serviceId);
        if ($recordId) {
            $query .= ' AND instance.serviciosestacionid<>?';
            $params[] = $recordId;
        }

        $result = $adb->pquery($query, $params);
        if ($result && $adb->num_rows($result)) {
            throw new Exception('Este servicio ya está asociado a esta cuenta.');
        }
    }

    public static function register($catalogModule, $instanceModule) {
        foreach (array('vtiger.entity.beforesave') as $eventName) {
            Vtiger_Event::register(
                $catalogModule,
                $eventName,
                'ServiciosEstacionDuplicateHandler',
                'modules/Servicios/ServiciosEstacionDuplicateHandler.php'
            );
            Vtiger_Event::register(
                $instanceModule,
                $eventName,
                'ServiciosEstacionDuplicateHandler',
                'modules/Servicios/ServiciosEstacionDuplicateHandler.php'
            );
        }
    }

    private static function validateCatalog($eventName, $entityData) {
        if ($eventName !== 'vtiger.entity.beforesave') {
            return;
        }

        global $adb;
        $recordId = (int)$entityData->getId();
        $data = $entityData->getData();
        $name = trim((string)$data['servicios_name']);
        $code = trim((string)$data['codigo']);

        if ($name !== '') {
            $duplicates = $adb->pquery(
                'SELECT catalog.serviciosid FROM vtiger_servicios AS catalog'
                . ' INNER JOIN vtiger_crmentity AS entity ON entity.crmid=catalog.serviciosid AND entity.deleted=0'
                . ' WHERE UPPER(TRIM(catalog.servicios_name))=? AND catalog.serviciosid<>?',
                array(strtoupper($name), $recordId)
            );
            if ($duplicates && $adb->num_rows($duplicates)) {
                throw new Exception('Ya existe este servicio en el catálogo.');
            }
        }

        if ($code !== '') {
            $duplicates = $adb->pquery(
                'SELECT catalog.serviciosid FROM vtiger_servicios AS catalog'
                . ' INNER JOIN vtiger_crmentity AS entity ON entity.crmid=catalog.serviciosid AND entity.deleted=0'
                . ' WHERE UPPER(TRIM(catalog.codigo))=? AND catalog.serviciosid<>?',
                array(strtoupper($code), $recordId)
            );
            if ($duplicates && $adb->num_rows($duplicates)) {
                throw new Exception('Ya existe un servicio con este código.');
            }
        }
    }

    public static function ensureCatalogRecords() {
        global $adb, $current_user;
        $ownerId = isset($current_user->id) ? (int)$current_user->id : 0;
        if (!$ownerId) {
            $result = $adb->pquery('SELECT id FROM vtiger_users WHERE status=? ORDER BY is_admin DESC, id LIMIT 1', array('Active'));
            $ownerId = $result && $adb->num_rows($result) ? (int)$adb->query_result($result, 0, 'id') : 1;
        }

        $catalog = array(
            array('NOTICES', 'SRV-NOTICES', 'Servicio de notificaciones'),
            array('SERVICES CLOUD', 'SRV-SERVICES-CLOUD', 'Plataforma cloud'),
            array('E-INVOICING', 'SRV-E-INVOICING', 'Facturación electrónica'),
            array('IISE', 'SRV-IISE', 'Sistema contable'),
            array('PAY FUELCONTROL-PAYPHONE', 'SRV-PAY-FUELCONTROL-PAYPHONE', 'Pago payphone'),
            array('PAY FUELCONTROL-DATAFAST', 'SRV-PAY-FUELCONTROL-DATAFAST', 'Pago datafast'),
            array('PAY FUELCONTROL-MEDIANET', 'SRV-PAY-FUELCONTROL-MEDIANET', 'Pago medianet'),
            array('PAY FUELCONTROL-DEUNA', 'SRV-PAY-FUELCONTROL-DEUNA', 'Pago deuna'),
            array('TUNNEL-WEBHOOK', 'SRV-TUNNEL-WEBHOOK', 'Webhook'),
            array('TUNNEL-API REST', 'SRV-TUNNEL-API-REST', 'API REST'),
            array('ARCH', 'SRV-ARCH', 'Archivo y permisos'),
            array('VPN CONNECT', 'SRV-VPN-CONNECT', 'Conexión VPN')
        );

        foreach ($catalog as $item) {
            $name = $item[0];
            $code = $item[1];
            $description = $item[2];

            $existing = $adb->pquery(
                'SELECT catalog.serviciosid FROM vtiger_servicios AS catalog'
                . ' INNER JOIN vtiger_crmentity AS entity ON entity.crmid=catalog.serviciosid AND entity.deleted=0'
                . ' WHERE UPPER(TRIM(catalog.codigo))=? ORDER BY catalog.serviciosid LIMIT 1',
                array(strtoupper($code))
            );

            $record = CRMEntity::getInstance('Servicios');
            if ($existing && $adb->num_rows($existing)) {
                $id = (int)$adb->query_result($existing, 0, 'serviciosid');
                $record->retrieve_entity_info($id, 'Servicios');
                $record->id = $id;
                $record->mode = 'edit';
            } else {
                $record->column_fields['servicios_name'] = $name;
                $record->column_fields['codigo'] = $code;
                $record->column_fields['estado'] = 'no instalado';
                $record->column_fields['assigned_user_id'] = $ownerId;
            }

            $record->column_fields['tipo'] = $name;
            $record->column_fields['descripcion'] = $description;
            $record->column_fields['obligatorio'] = 0;
            $record->save('Servicios');
        }
    }
}
