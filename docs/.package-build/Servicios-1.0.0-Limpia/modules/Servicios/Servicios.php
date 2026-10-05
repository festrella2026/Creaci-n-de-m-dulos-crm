<?php

include_once 'modules/Vtiger/CRMEntity.php';

class Servicios extends Vtiger_CRMEntity {
    var $table_name = 'vtiger_servicios';
    var $table_index = 'serviciosid';
    var $customFieldTable = array('vtiger_servicioscf', 'serviciosid');
    var $tab_name = array('vtiger_crmentity', 'vtiger_servicios', 'vtiger_servicioscf');
    var $tab_name_index = array(
        'vtiger_crmentity' => 'crmid',
        'vtiger_servicios' => 'serviciosid',
        'vtiger_servicioscf' => 'serviciosid'
    );
    var $list_fields = array(
        'Nombre' => array('servicios', 'servicios_name'),
        'Código' => array('servicios', 'codigo'),
        'Tipo' => array('servicios', 'tipo'),
        'Estado' => array('servicios', 'estado'),
        'Obligatorio' => array('servicios', 'obligatorio'),
        'Assigned To' => array('crmentity', 'smownerid')
    );
    var $list_fields_name = array(
        'Nombre' => 'servicios_name',
        'Código' => 'codigo',
        'Tipo' => 'tipo',
        'Estado' => 'estado',
        'Obligatorio' => 'obligatorio',
        'Assigned To' => 'assigned_user_id'
    );
    var $list_link_field = 'servicios_name';
    var $search_fields = array(
        'Nombre' => array('servicios', 'servicios_name'),
        'Código' => array('servicios', 'codigo'),
        'Tipo' => array('servicios', 'tipo')
    );
    var $search_fields_name = array(
        'Nombre' => 'servicios_name',
        'Código' => 'codigo',
        'Tipo' => 'tipo'
    );
    var $popup_fields = array('servicios_name', 'codigo', 'tipo');
    var $def_basicsearch_col = 'servicios_name';
    var $def_detailview_recname = 'servicios_name';
    var $mandatory_fields = array('servicios_name', 'codigo', 'assigned_user_id');
    var $default_order_by = 'servicios_name';
    var $default_sort_order = 'ASC';

    function vtlib_handler($moduleName, $eventType) {
        if ($eventType !== 'module.postinstall' && $eventType !== 'module.postupdate') {
            return;
        }

        require_once 'modules/Servicios/ServiciosEstacionInstaller.php';
        ServiciosEstacionInstaller::install();
    }
}
