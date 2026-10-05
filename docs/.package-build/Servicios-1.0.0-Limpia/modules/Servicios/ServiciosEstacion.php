<?php

include_once 'modules/Vtiger/CRMEntity.php';

class ServiciosEstacion extends Vtiger_CRMEntity {
    var $table_name = 'vtiger_serviciosestacion';
    var $table_index = 'serviciosestacionid';
    var $customFieldTable = array('vtiger_serviciosestacioncf', 'serviciosestacionid');
    var $tab_name = array('vtiger_crmentity', 'vtiger_serviciosestacion', 'vtiger_serviciosestacioncf');
    var $tab_name_index = array(
        'vtiger_crmentity' => 'crmid',
        'vtiger_serviciosestacion' => 'serviciosestacionid',
        'vtiger_serviciosestacioncf' => 'serviciosestacionid'
    );
    var $list_fields = array(
        'Identificador de instalación' => array('serviciosestacion', 'serviciosestacion_name'),
        'Cuenta' => array('serviciosestacion', 'account_id'),
        'Servicio' => array('serviciosestacion', 'servicio_id'),
        'Estado de instalación' => array('serviciosestacion', 'estado_instalacion'),
        'Fecha de activación' => array('serviciosestacion', 'fecha_activacion'),
        'Assigned To' => array('crmentity', 'smownerid')
    );
    var $list_fields_name = array(
        'Identificador de instalación' => 'serviciosestacion_name',
        'Cuenta' => 'account_id',
        'Servicio' => 'servicio_id',
        'Estado de instalación' => 'estado_instalacion',
        'Fecha de activación' => 'fecha_activacion',
        'Assigned To' => 'assigned_user_id'
    );
    var $list_link_field = 'serviciosestacion_name';
    var $search_fields = array(
        'Identificador de instalación' => array('serviciosestacion', 'serviciosestacion_name'),
        'Cuenta' => array('serviciosestacion', 'account_id'),
        'Servicio' => array('serviciosestacion', 'servicio_id')
    );
    var $search_fields_name = array(
        'Identificador de instalación' => 'serviciosestacion_name',
        'Cuenta' => 'account_id',
        'Servicio' => 'servicio_id'
    );
    var $popup_fields = array('serviciosestacion_name');
    var $def_basicsearch_col = 'serviciosestacion_name';
    var $def_detailview_recname = 'serviciosestacion_name';
    var $mandatory_fields = array('serviciosestacion_name', 'account_id', 'servicio_id', 'estado_instalacion', 'assigned_user_id');
    var $default_order_by = 'serviciosestacion_name';
    var $default_sort_order = 'ASC';
}
