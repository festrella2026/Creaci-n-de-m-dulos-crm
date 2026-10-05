<?php

require_once 'vtlib/Vtiger/Module.php';
require_once 'vtlib/Vtiger/Event.php';
require_once 'modules/Servicios/ServiciosEstacionDuplicateHandler.php';

class ServiciosEstacionInstaller {
    private static function ensurePicklistValues($field, $values) {
        global $adb;
        $table = 'vtiger_' . $field->name;
        if (!Vtiger_Utils::CheckTable($table)) {
            $field->setPicklistValues($values);
            return;
        }

        $missingValues = array();
        foreach ($values as $value) {
            $result = $adb->pquery("SELECT 1 FROM $table WHERE $field->name=?", array($value));
            if (!$result || !$adb->num_rows($result)) {
                $missingValues[] = $value;
            }
        }
        if ($missingValues) {
            $field->setPicklistValues($missingValues);
        }
    }

    private static function addField($module, $block, $name, $label, $uitype, $columnType, $dataType, $relatedModules = null, $defaultValue = null, $columnName = null) {
        $field = Vtiger_Field::getInstance($name, $module);
        if (!$field) {
            $field = new Vtiger_Field();
            $field->name = $name;
            $field->label = $label;
            $field->uitype = $uitype;
            $field->column = $columnName ? $columnName : $name;
            $field->columntype = $columnType;
            $field->typeofdata = $dataType;
            $field->presence = 2;
            if ($defaultValue !== null) {
                $field->defaultvalue = $defaultValue;
            }
            $block->addField($field);
        }

        if ($relatedModules) {
            $field->setRelatedModules($relatedModules);
        }

        return $field;
    }

    private static function addSystemField($module, $block, $name, $label, $column, $uitype, $dataType) {
        $field = Vtiger_Field::getInstance($name, $module);
        if (!$field) {
            $field = new Vtiger_Field();
            $field->name = $name;
            $field->label = $label;
            $field->table = 'vtiger_crmentity';
            $field->column = $column;
            $field->uitype = $uitype;
            $field->typeofdata = $dataType;
            $field->presence = 2;
            if ($uitype === 70) {
                $field->displaytype = 2;
            }
            $block->addField($field);
        }
        return $field;
    }

    private static function ensureCatalogFields($module) {
        $block = Vtiger_Block::getInstance('LBL_SERVICIOS_INFORMATION', $module);
        if (!$block) {
            $block = new Vtiger_Block();
            $block->label = 'LBL_SERVICIOS_INFORMATION';
            $module->addBlock($block);
        }

        self::addField($module, $block, 'codigo', 'Código', 1, 'VARCHAR(64)', 'V~O');
        self::addField($module, $block, 'tipo', 'Tipo', 1, 'VARCHAR(100)', 'V~O');
        self::addField($module, $block, 'descripcion', 'Descripción', 21, 'TEXT', 'V~O');
        $statusField = self::addField($module, $block, 'estado', 'Estado', 15, 'VARCHAR(50)', 'V~M', null, 'no instalado');
        self::ensurePicklistValues($statusField, array('instalado', 'no instalado'));
        self::addField($module, $block, 'obligatorio', 'Obligatorio', 56, 'INT(1)', 'C~O');
        $departamentoField = self::addField($module, $block, 'departamento_responsable', 'Departamento responsable', 15, 'VARCHAR(100)', 'V~O');
        self::ensurePicklistValues($departamentoField, array('Operaciones', 'Técnica', 'Comercial', 'Finanzas', 'Soporte'));
        self::addField($module, $block, 'requiere_certificacion', 'Requiere certificación', 56, 'INT(1)', 'C~O');
        self::addField($module, $block, 'observaciones', 'Observaciones', 21, 'TEXT', 'V~O');

        $filter = Vtiger_Filter::getInstance('All', $module);
        if ($filter) {
            $filter->delete();
        }
        $filter = new Vtiger_Filter();
        $filter->name = 'All';
        $filter->isdefault = true;
        $module->addFilter($filter);
        $filter->addField(Vtiger_Field::getInstance('servicios_name', $module), 0);
        $filter->addField(Vtiger_Field::getInstance('codigo', $module), 1);
        $filter->addField(Vtiger_Field::getInstance('tipo', $module), 2);
        $filter->addField(Vtiger_Field::getInstance('estado', $module), 3);
        $filter->addField(Vtiger_Field::getInstance('obligatorio', $module), 4);
    }

    private static function ensureInstanceModule() {
        $rootDirectory = dirname(__DIR__, 2);
        $entityClass = $rootDirectory . '/modules/ServiciosEstacion/ServiciosEstacion.php';
        $entityDirectory = dirname($entityClass);
        if (!is_dir($entityDirectory) && !mkdir($entityDirectory, 0755, true)) {
            throw new Exception('No se pudo crear el directorio de ServiciosEstacion.');
        }
        $sourceClass = $rootDirectory . '/modules/Servicios/ServiciosEstacion.php';
        if (!is_file($entityClass) && !copy($sourceClass, $entityClass)) {
            throw new Exception('No se pudo instalar la clase de ServiciosEstacion.');
        }

        $module = Vtiger_Module::getInstance('ServiciosEstacion');
        $isNewModule = !$module;
        if (!$module) {
            $module = new Vtiger_Module();
            $module->name = 'ServiciosEstacion';
            $module->label = 'ServiciosEstacion';
            $module->parent = '';
            $module->save();
            $module->initTables();
        }

        $informationBlock = Vtiger_Block::getInstance('LBL_SERVICIOSESTACION_INFORMATION', $module);
        if (!$informationBlock) {
            $informationBlock = new Vtiger_Block();
            $informationBlock->label = 'LBL_SERVICIOSESTACION_INFORMATION';
            $module->addBlock($informationBlock);
        }
        $detailsBlock = Vtiger_Block::getInstance('LBL_SERVICIOSESTACION_DETAILS', $module);
        if (!$detailsBlock) {
            $detailsBlock = new Vtiger_Block();
            $detailsBlock->label = 'LBL_SERVICIOSESTACION_DETAILS';
            $module->addBlock($detailsBlock);
        }
        $notesBlock = Vtiger_Block::getInstance('LBL_SERVICIOSESTACION_NOTES', $module);
        if (!$notesBlock) {
            $notesBlock = new Vtiger_Block();
            $notesBlock->label = 'LBL_SERVICIOSESTACION_NOTES';
            $module->addBlock($notesBlock);
        }

        $accountField = self::addField($module, $informationBlock, 'account_id', 'Cuenta', 10, 'INT(19)', 'I~M', array('Accounts' => 'Servicios'));
        self::addField($module, $informationBlock, 'servicio_id', 'Servicio', 10, 'INT(19)', 'I~M', array('Servicios'));
        $statusField = self::addField($module, $informationBlock, 'estado_instalacion', 'Estado de instalación', 15, 'VARCHAR(50)', 'V~M', null, 'No instalado');
        self::ensurePicklistValues($statusField, array('No instalado', 'Pendiente', 'En configuración', 'Activo', 'Suspendido', 'Inactivo'));
        self::addField($module, $detailsBlock, 'fecha_activacion', 'Fecha de activación', 5, 'DATE', 'D~O');
        self::addField($module, $detailsBlock, 'fecha_desactivacion', 'Fecha de desactivación', 5, 'DATE', 'D~O');
        $responsibleField = self::addField($module, $detailsBlock, 'responsable', 'Responsable', 53, 'INT(19)', 'I~O', array('Users'));
        self::addField($module, $detailsBlock, 'esquema_instalado', 'Esquema instalado', 1, 'VARCHAR(100)', 'V~O');
        self::addField($module, $detailsBlock, 'cantidad_equipos', 'Cantidad de equipos', 7, 'INT(11)', 'I~O');
        self::addField($module, $detailsBlock, 'version_equipos', 'Versión de equipos', 1, 'VARCHAR(100)', 'V~O');
        $certificationField = self::addField($module, $detailsBlock, 'estado_certificacion', 'Estado de certificación', 15, 'VARCHAR(50)', 'V~O');
        self::ensurePicklistValues($certificationField, array('No aplica', 'Pendiente', 'En proceso', 'Aprobado', 'Rechazado'));
        self::addField($module, $detailsBlock, 'fecha_certificacion', 'Fecha de certificación', 5, 'DATE', 'D~O');
        self::addField($module, $notesBlock, 'observaciones', 'Observaciones', 21, 'TEXT', 'V~O');

        self::addField($module, $detailsBlock, 'notices_correo_configurado', 'NOTICES: Correo configurado', 56, 'INT(1)', 'C~O');
        $noticesStatus = self::addField($module, $detailsBlock, 'notices_estado_envio_correos', 'NOTICES: Estado de envío de correos', 15, 'VARCHAR(50)', 'V~O');
        self::ensurePicklistValues($noticesStatus, array('No enviado', 'En cola', 'Enviado', 'Fallido'));

        $eInvoicingStatus = self::addField($module, $detailsBlock, 'e_invoicing_estado_facturacion', 'E-INVOICING: Estado de facturación electrónica', 15, 'VARCHAR(50)', 'V~O');
        self::ensurePicklistValues($eInvoicingStatus, array('No aplica', 'Pendiente', 'Aprobado', 'Rechazado'));

        self::addField($module, $detailsBlock, 'iise_sistema_contable', 'IISE: Sistema contable', 1, 'VARCHAR(100)', 'V~O');
        $iiseCertification = self::addField($module, $detailsBlock, 'iise_estado_certificacion', 'IISE: Estado de certificación', 15, 'VARCHAR(50)', 'V~O');
        self::ensurePicklistValues($iiseCertification, array('No aplica', 'Pendiente', 'En proceso', 'Aprobado', 'Rechazado'));
        self::addField($module, $detailsBlock, 'iise_credenciales_entregadas', 'IISE: Credenciales entregadas', 56, 'INT(1)', 'C~O');

        self::addField($module, $detailsBlock, 'pay_fuelcontrol_payphone_estado_afiliacion', 'PAY FUELCONTROL-PAYPHONE: Estado de afiliación', 15, 'VARCHAR(50)', 'V~O');
        self::addField($module, $detailsBlock, 'pay_fuelcontrol_datafast_estado_afiliacion', 'PAY FUELCONTROL-DATAFAST: Estado de afiliación', 15, 'VARCHAR(50)', 'V~O');
        self::addField($module, $detailsBlock, 'pay_fuelcontrol_datafast_esquema_instalado', 'PAY FUELCONTROL-DATAFAST: Esquema instalado', 1, 'VARCHAR(100)', 'V~O');
        self::addField($module, $detailsBlock, 'pay_fuelcontrol_datafast_version_equipos', 'PAY FUELCONTROL-DATAFAST: Versión de equipos', 1, 'VARCHAR(100)', 'V~O');
        self::addField($module, $detailsBlock, 'pay_fuelcontrol_datafast_mid', 'PAY FUELCONTROL-DATAFAST: MID', 1, 'VARCHAR(100)', 'V~O');
        self::addField($module, $detailsBlock, 'pay_fuelcontrol_datafast_tid', 'PAY FUELCONTROL-DATAFAST: TID', 1, 'VARCHAR(100)', 'V~O');
        self::addField($module, $detailsBlock, 'pay_fuelcontrol_datafast_estado_certificacion', 'PAY FUELCONTROL-DATAFAST: Estado de certificación', 15, 'VARCHAR(50)', 'V~O');

        self::addField($module, $detailsBlock, 'pay_fuelcontrol_medianet_estado_afiliacion', 'PAY FUELCONTROL-MEDIANET: Estado de afiliación', 15, 'VARCHAR(50)', 'V~O');
        self::addField($module, $detailsBlock, 'pay_fuelcontrol_medianet_esquema_instalado', 'PAY FUELCONTROL-MEDIANET: Esquema instalado', 1, 'VARCHAR(100)', 'V~O');
        self::addField($module, $detailsBlock, 'pay_fuelcontrol_medianet_version_equipos', 'PAY FUELCONTROL-MEDIANET: Versión de equipos', 1, 'VARCHAR(100)', 'V~O');
        self::addField($module, $detailsBlock, 'pay_fuelcontrol_medianet_mid', 'PAY FUELCONTROL-MEDIANET: MID', 1, 'VARCHAR(100)', 'V~O');
        self::addField($module, $detailsBlock, 'pay_fuelcontrol_medianet_tid', 'PAY FUELCONTROL-MEDIANET: TID', 1, 'VARCHAR(100)', 'V~O');
        self::addField($module, $detailsBlock, 'pay_fuelcontrol_medianet_estado_certificacion', 'PAY FUELCONTROL-MEDIANET: Estado de certificación', 15, 'VARCHAR(50)', 'V~O');

        self::addField($module, $detailsBlock, 'pay_fuelcontrol_deuna_esquema_instalado', 'PAY FUELCONTROL-DEUNA: Esquema instalado', 1, 'VARCHAR(100)', 'V~O');
        self::addField($module, $detailsBlock, 'pay_fuelcontrol_deuna_cantidad_equipos', 'PAY FUELCONTROL-DEUNA: Cantidad de equipos', 7, 'INT(11)', 'I~O');
        self::addField($module, $detailsBlock, 'pay_fuelcontrol_deuna_version_equipos', 'PAY FUELCONTROL-DEUNA: Versión de equipos', 1, 'VARCHAR(100)', 'V~O');
        self::addField($module, $detailsBlock, 'pay_fuelcontrol_deuna_puntos_venta', 'PAY FUELCONTROL-DEUNA: Puntos de venta', 7, 'INT(11)', 'I~O');

        self::addField($module, $detailsBlock, 'tunnel_webhook_aplicacion', 'TUNNEL-WEBHOOK: Aplicación que consume', 1, 'VARCHAR(100)', 'V~O');
        self::addField($module, $detailsBlock, 'tunnel_webhook_acceso_webhook', 'TUNNEL-WEBHOOK: Acceso Webhook', 56, 'INT(1)', 'C~O');
        self::addField($module, $detailsBlock, 'tunnel_api_rest_aplicacion', 'TUNNEL-API REST: Aplicación que consume', 1, 'VARCHAR(100)', 'V~O');
        self::addField($module, $detailsBlock, 'tunnel_api_rest_acceso_api', 'TUNNEL-API REST: Acceso API', 56, 'INT(1)', 'C~O');

        self::addField($module, $detailsBlock, 'arch_estado_proceso_permisos', 'ARCH: Estado del proceso de permisos', 15, 'VARCHAR(50)', 'V~O');
        self::addField($module, $detailsBlock, 'vpn_connect_cantidad_vpn_activas', 'VPN CONNECT: Cantidad de VPN activas', 7, 'INT(11)', 'I~O');
        self::addField($module, $detailsBlock, 'vpn_connect_acceso_servidor', 'VPN CONNECT: Acceso al servidor', 56, 'INT(1)', 'C~O');
        self::addField($module, $detailsBlock, 'vpn_connect_acceso_clientes', 'VPN CONNECT: Acceso a clientes', 56, 'INT(1)', 'C~O');

        self::addSystemField($module, $informationBlock, 'assigned_user_id', 'Assigned To', 'smownerid', 53, 'V~M');
        self::addSystemField($module, $informationBlock, 'createdtime', 'Created Time', 'createdtime', 70, 'T~O');
        self::addSystemField($module, $informationBlock, 'modifiedtime', 'Modified Time', 'modifiedtime', 70, 'T~O');
        $nameField = self::addField($module, $informationBlock, 'serviciosestacion_name', 'Identificador de instalación', 2, 'VARCHAR(255)', 'V~M');
        $module->setEntityIdentifier($nameField);
        if ($isNewModule) {
            $module->setDefaultSharing();
        }
        $module->enableTools(array('Import', 'Export'));
        $module->initWebservice();
        if (!Vtiger_Event::hasSupport()) {
            throw new Exception('La instalación no tiene soporte de eventos vtlib; no se puede activar la validación de duplicados.');
        }
        Vtiger_Event::register(
            $module,
            'vtiger.entity.beforesave',
            'ServiciosEstacionDuplicateHandler',
            'modules/Servicios/ServiciosEstacionDuplicateHandler.php'
        );

        $filter = Vtiger_Filter::getInstance('All', $module);
        if ($filter) {
            $filter->delete();
        }
        $filter = new Vtiger_Filter();
        $filter->name = 'All';
        $filter->isdefault = true;
        $module->addFilter($filter);
        $filter->addField(Vtiger_Field::getInstance('account_id', $module), 0);
        $filter->addField(Vtiger_Field::getInstance('servicio_id', $module), 1);
        $filter->addField(Vtiger_Field::getInstance('estado_instalacion', $module), 2);
        $filter->addField(Vtiger_Field::getInstance('fecha_activacion', $module), 3);

        Vtiger_Module::fireEvent('ServiciosEstacion', Vtiger_Module::EVENT_MODULE_POSTINSTALL);
        $module->addLink('HEADERSCRIPT', 'ServiciosEstacionFields', 'layouts/v7/modules/ServiciosEstacion/resources/Edit.js');
        return array($module, $accountField);
    }

    public static function install() {
        $catalogModule = Vtiger_Module::getInstance('Servicios');
        $accountsModule = Vtiger_Module::getInstance('Accounts');
        if (!$catalogModule || !$accountsModule) {
            throw new Exception('Se requieren los módulos Accounts y Servicios para instalar ServiciosEstacion.');
        }

        $catalogModule->initWebservice();
        self::ensureCatalogFields($catalogModule);
        list($instanceModule) = self::ensureInstanceModule();

        $accountsModule->unsetRelatedList($catalogModule, 'Servicios');
        ServiciosEstacionDuplicateHandler::register($catalogModule, $instanceModule);
        ServiciosEstacionDuplicateHandler::ensureCatalogRecords();
    }
}
