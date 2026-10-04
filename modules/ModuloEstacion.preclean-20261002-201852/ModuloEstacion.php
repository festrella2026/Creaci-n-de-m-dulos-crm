<?php

include_once 'modules/Vtiger/CRMEntity.php';

class ModuloEstacion extends Vtiger_CRMEntity {
	var $table_name = 'vtiger_moduloestacion';
	var $table_index = 'moduloestacionid';
	var $customFieldTable = array('vtiger_moduloestacioncf', 'moduloestacionid');
	var $tab_name = array('vtiger_crmentity', 'vtiger_moduloestacion', 'vtiger_moduloestacioncf');
	var $tab_name_index = array(
		'vtiger_crmentity' => 'crmid',
		'vtiger_moduloestacion' => 'moduloestacionid',
		'vtiger_moduloestacioncf' => 'moduloestacionid'
	);
	var $list_fields = array(
		'Nombre' => array('moduloestacion', 'moduloestacion_name'),
		'Cuenta / Estación' => array('moduloestacion', 'account_id'),
		'Módulo' => array('moduloestacion', 'modulo_id'),
		'Estado' => array('moduloestacion', 'estado_instalacion'),
		'Fecha de instalación' => array('moduloestacion', 'fecha_instalacion'),
		'Assigned To' => array('crmentity', 'smownerid')
	);
	var $list_fields_name = array(
		'Nombre' => 'moduloestacion_name',
		'Cuenta / Estación' => 'account_id',
		'Módulo' => 'modulo_id',
		'Estado' => 'estado_instalacion',
		'Fecha de instalación' => 'fecha_instalacion',
		'Assigned To' => 'assigned_user_id'
	);
	var $list_link_field = 'moduloestacion_name';
	var $search_fields = array(
		'Nombre' => array('moduloestacion', 'moduloestacion_name'),
		'Cuenta / Estación' => array('moduloestacion', 'account_id'),
		'Módulo' => array('moduloestacion', 'modulo_id')
	);
	var $search_fields_name = array(
		'Nombre' => 'moduloestacion_name',
		'Cuenta / Estación' => 'account_id',
		'Módulo' => 'modulo_id'
	);
	var $popup_fields = array('moduloestacion_name');
	var $def_basicsearch_col = 'moduloestacion_name';
	var $def_detailview_recname = 'moduloestacion_name';
	var $mandatory_fields = array('moduloestacion_name', 'account_id', 'modulo_id', 'estado_instalacion', 'assigned_user_id');
	var $default_order_by = 'moduloestacion_name';
	var $default_sort_order = 'ASC';
}