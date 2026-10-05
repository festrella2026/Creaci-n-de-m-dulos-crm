<?php

include_once 'modules/Vtiger/CRMEntity.php';

class Modulos extends Vtiger_CRMEntity {
	var $table_name = 'vtiger_modulos';
	var $table_index = 'modulosid';
	var $customFieldTable = array('vtiger_moduloscf', 'modulosid');
	var $tab_name = array('vtiger_crmentity', 'vtiger_modulos', 'vtiger_moduloscf');
	var $tab_name_index = array(
		'vtiger_crmentity' => 'crmid',
		'vtiger_modulos' => 'modulosid',
		'vtiger_moduloscf' => 'modulosid'
	);
	var $list_fields = array(
		'Nombre' => array('modulos', 'modulos_name'),
		'Código' => array('modulos', 'codigo'),
		'Categoría' => array('modulos', 'categoria'),
		'Estado catálogo' => array('modulos', 'estado_catalogo'),
		'Obligatorio en todas las estaciones' => array('modulos', 'obligatorio_todas_estaciones'),
		'Assigned To' => array('crmentity', 'smownerid')
	);
	var $list_fields_name = array(
		'Nombre' => 'modulos_name',
		'Código' => 'codigo',
		'Categoría' => 'categoria',
		'Estado catálogo' => 'estado_catalogo',
		'Obligatorio en todas las estaciones' => 'obligatorio_todas_estaciones',
		'Assigned To' => 'assigned_user_id'
	);
	var $list_link_field = 'modulos_name';
	var $search_fields = array(
		'Nombre' => array('modulos', 'modulos_name'),
		'Código' => array('modulos', 'codigo')
	);
	var $search_fields_name = array(
		'Nombre' => 'modulos_name',
		'Código' => 'codigo'
	);
	var $popup_fields = array('modulos_name', 'codigo');
	var $def_basicsearch_col = 'modulos_name';
	var $def_detailview_recname = 'modulos_name';
	var $mandatory_fields = array('modulos_name', 'assigned_user_id');
	var $default_order_by = 'modulos_name';
	var $default_sort_order = 'ASC';

	function vtlib_handler($moduleName, $eventType) {
		if ($eventType !== 'module.postinstall' && $eventType !== 'module.postupdate') {
			return;
		}

		require_once 'modules/Modulos/ModuloEstacionInstaller.php';
		ModuloEstacionInstaller::install();
	}
}