<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/

include_once 'modules/Vtiger/CRMEntity.php';

class Modulos extends Vtiger_CRMEntity {
	var $table_name = 'vtiger_modulos';
	var $table_index= 'modulosid';

	/**
	 * Mandatory table for supporting custom fields.
	 */
	var $customFieldTable = Array('vtiger_moduloscf', 'modulosid');

	/**
	 * Mandatory for Saving, Include tables related to this module.
	 */
	var $tab_name = Array('vtiger_crmentity', 'vtiger_modulos', 'vtiger_moduloscf');

	/**
	 * Mandatory for Saving, Include tablename and tablekey columnname here.
	 */
	var $tab_name_index = Array(
		'vtiger_crmentity' => 'crmid',
		'vtiger_modulos' => 'modulosid',
		'vtiger_moduloscf'=>'modulosid');

	/**
	 * Mandatory for Listing (Related listview)
	 */
	var $list_fields = Array (
		/* Format: Field Label => Array(tablename, columnname) */
		// tablename should not have prefix 'vtiger_'
		'Nombre' => Array('modulos', 'modulos_name'),
		'Assigned To' => Array('crmentity','smownerid')
	);
	var $list_fields_name = Array (
		/* Format: Field Label => fieldname */
		'Nombre' => 'modulos_name',
		'Assigned To' => 'assigned_user_id',
	);

	// Make the field link to detail view
	var $list_link_field = 'modulos_name';

	// For Popup listview and UI type support
	var $search_fields = Array(
		/* Format: Field Label => Array(tablename, columnname) */
		// tablename should not have prefix 'vtiger_'
		'Nombre' => Array('modulos', 'modulos_name'),
		'Assigned To' => Array('vtiger_crmentity','assigned_user_id'),
	);
	var $search_fields_name = Array (
		/* Format: Field Label => fieldname */
		'Nombre' => 'modulos_name',
		'Assigned To' => 'assigned_user_id',
	);

	// For Popup window record selection
	var $popup_fields = Array ('modulos_name');

	// For Alphabetical search
	var $def_basicsearch_col = 'modulos_name';

	// Column value to use on detail view record text display
	var $def_detailview_recname = 'modulos_name';

	// Used when enabling/disabling the mandatory fields for the module.
	// Refers to vtiger_field.fieldname values.
	var $mandatory_fields = Array('modulos_name','assigned_user_id');

	var $default_order_by = 'modulos_name';
	var $default_sort_order='ASC';

	/**
	* Invoked when special actions are performed on the module.
	* @param String Module name
	* @param String Event Type
	*/
	function vtlib_handler($moduleName, $eventType) {
		global $adb;
		include_once 'vtlib/Vtiger/Module.php';
		if ($eventType == 'module.postinstall' || $eventType == 'module.postupdate') {
			$accountsModule = Vtiger_Module::getInstance('Accounts');
			$modulosModule = Vtiger_Module::getInstance('Modulos');

			if (!$accountsModule || !$modulosModule) {
				return;
			}

			$relationResult = $adb->pquery(
				'SELECT relation_id FROM vtiger_relatedlists WHERE tabid=? AND related_tabid=? AND label=?',
				array($accountsModule->getId(), $modulosModule->getId(), 'Modulos')
			);

			if (!$relationResult || !$adb->num_rows($relationResult)) {
				$accountsModule->setRelatedList($modulosModule, 'Modulos', array('ADD', 'SELECT'));
			}
		} elseif ($eventType == 'module.preuninstall') {
			$accountsModule = Vtiger_Module::getInstance('Accounts');
			$modulosModule = Vtiger_Module::getInstance('Modulos');

			if ($accountsModule && $modulosModule) {
				$accountsModule->unsetRelatedList($modulosModule, 'Modulos');
			}
		}
 	}
}