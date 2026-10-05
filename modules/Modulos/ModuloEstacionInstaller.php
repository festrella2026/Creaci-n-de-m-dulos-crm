<?php

require_once 'vtlib/Vtiger/Module.php';
require_once 'vtlib/Vtiger/Event.php';
require_once 'modules/Modulos/ModuloEstacionDuplicateHandler.php';

class ModuloEstacionInstaller {
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

	private static function replacePicklistValues($field, $values) {
		self::ensurePicklistValues($field, $values);
		global $adb;
		$table = 'vtiger_' . $field->name;
		$idColumn = $field->name . 'id';
		$result = $adb->pquery(
			"SELECT $idColumn, picklist_valueid FROM $table WHERE $field->name NOT IN (?,?)",
			$values
		);
		for ($index = 0; $index < $adb->num_rows($result); $index++) {
			$valueId = $adb->query_result($result, $index, 'picklist_valueid');
			$rowId = $adb->query_result($result, $index, $idColumn);
			$adb->pquery('DELETE FROM vtiger_role2picklist WHERE picklistvalueid=?', array($valueId));
			$adb->pquery("DELETE FROM $table WHERE $idColumn=?", array($rowId));
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

	private static function ensurePhysicalColumnName($tableName, $columnName, $legacyColumnName, $columnType) {
		global $adb;
		$columns = $adb->getColumnNames($tableName);
		if (!in_array($columnName, $columns, true) && in_array($legacyColumnName, $columns, true)) {
			$adb->pquery("ALTER TABLE $tableName CHANGE COLUMN `$legacyColumnName` `$columnName` $columnType NULL", array());
		}
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
		$block = Vtiger_Block::getInstance('LBL_MODULOS_INFORMATION', $module);
		if (!$block) {
			$block = new Vtiger_Block();
			$block->label = 'LBL_MODULOS_INFORMATION';
			$module->addBlock($block);
		}

		self::addField($module, $block, 'codigo', 'Código', 1, 'VARCHAR(64)', 'V~O');
		self::addField($module, $block, 'categoria', 'Categoría', 1, 'VARCHAR(100)', 'V~O');
		self::addField($module, $block, 'descripcion', 'Descripción', 21, 'TEXT', 'V~O');
		$catalogStatus = self::addField($module, $block, 'estado_catalogo', 'Estado catálogo', 15, 'VARCHAR(50)', 'V~M', null, 'Activo');
		self::ensurePicklistValues($catalogStatus, array('Activo', 'Inactivo'));
		self::addField($module, $block, 'requiere_configuracion', 'Requiere configuración', 56, 'INT(1)', 'C~O');
		self::addField($module, $block, 'obligatorio_todas_estaciones', 'Obligatorio en todas las estaciones', 56, 'INT(1)', 'C~O');

		$field = Vtiger_Field::getInstance('estado', $module);
		if ($field) {
			global $adb;
			$adb->pquery('UPDATE vtiger_field SET displaytype=2 WHERE fieldid=?', array($field->id));
		}

		$filter = Vtiger_Filter::getInstance('All', $module);
		if ($filter) {
			$filter->delete();
		}
		$filter = new Vtiger_Filter();
		$filter->name = 'All';
		$filter->isdefault = true;
		$module->addFilter($filter);
		$filter->addField(Vtiger_Field::getInstance('modulos_name', $module), 0);
		$filter->addField(Vtiger_Field::getInstance('codigo', $module), 1);
		$filter->addField(Vtiger_Field::getInstance('categoria', $module), 2);
		$filter->addField(Vtiger_Field::getInstance('estado_catalogo', $module), 3);
		$filter->addField(Vtiger_Field::getInstance('obligatorio_todas_estaciones', $module), 4);
	}

	private static function ensureInstanceModule() {
		$rootDirectory = dirname(__DIR__, 2);
		$entityClass = $rootDirectory . '/modules/ModuloEstacion/ModuloEstacion.php';
		$entityDirectory = dirname($entityClass);
		if (!is_dir($entityDirectory) && !mkdir($entityDirectory, 0755, true)) {
			throw new Exception('No se pudo crear el directorio de ModuloEstacion.');
		}
		$sourceClass = $rootDirectory . '/modules/Modulos/ModuloEstacion.php';
		if (!is_file($entityClass) && !copy($sourceClass, $entityClass)) {
			throw new Exception('No se pudo instalar la clase de ModuloEstacion.');
		}

		$module = Vtiger_Module::getInstance('ModuloEstacion');
		$isNewModule = !$module;
		if (!$module) {
			$module = new Vtiger_Module();
			$module->name = 'ModuloEstacion';
			$module->label = 'ModuloEstacion';
			$module->parent = '';
			$module->save();
			$module->initTables();
		}

		$informationBlock = Vtiger_Block::getInstance('LBL_MODULOESTACION_INFORMATION', $module);
		if (!$informationBlock) {
			$informationBlock = new Vtiger_Block();
			$informationBlock->label = 'LBL_MODULOESTACION_INFORMATION';
			$module->addBlock($informationBlock);
		}
		$detailsBlock = Vtiger_Block::getInstance('LBL_MODULOESTACION_DETAILS', $module);
		if (!$detailsBlock) {
			$detailsBlock = new Vtiger_Block();
			$detailsBlock->label = 'LBL_MODULOESTACION_DETAILS';
			$module->addBlock($detailsBlock);
		}
		$notesBlock = Vtiger_Block::getInstance('LBL_MODULOESTACION_NOTES', $module);
		if (!$notesBlock) {
			$notesBlock = new Vtiger_Block();
			$notesBlock->label = 'LBL_MODULOESTACION_NOTES';
			$module->addBlock($notesBlock);
		}

		$accountField = self::addField($module, $informationBlock, 'account_id', 'Cuenta / Estación', 10, 'INT(19)', 'I~M', array('Accounts' => 'Módulos'));
		self::addField($module, $informationBlock, 'modulo_id', 'Módulo', 10, 'INT(19)', 'I~M', array('Modulos'));
		$instanceStatus = self::addField($module, $informationBlock, 'estado_instalacion', 'Estado', 15, 'VARCHAR(50)', 'V~M', null, 'No instalado');
		self::replacePicklistValues($instanceStatus, array('Instalado', 'No instalado'));

		$legacyStatus = Vtiger_Field::getInstance('estado', $module);
		if ($legacyStatus && $legacyStatus->id != $instanceStatus->id) {
			global $adb;
			$adb->pquery("UPDATE vtiger_moduloestacion SET estado_instalacion=CASE WHEN LOWER(estado) IN ('instalado','activo') THEN 'Instalado' ELSE 'No instalado' END WHERE estado_instalacion IS NULL OR estado_instalacion=''", array());
			$adb->pquery('UPDATE vtiger_field SET displaytype=2 WHERE fieldid=?', array($legacyStatus->id));
		}

		self::addField($module, $detailsBlock, 'fecha_prevista_instalacion', 'Fecha prevista de instalación', 5, 'DATE', 'D~O');
		self::addField($module, $detailsBlock, 'fecha_instalacion', 'Fecha de instalación', 5, 'DATE', 'D~O');
		self::addField($module, $detailsBlock, 'fecha_activacion', 'Fecha de activación', 5, 'DATE', 'D~O');
		self::addField($module, $detailsBlock, 'responsable_tecnico', 'Responsable técnico', 1, 'VARCHAR(100)', 'V~O');
		self::addField($module, $detailsBlock, 'departamento_responsable', 'Departamento responsable', 1, 'VARCHAR(100)', 'V~O');
		$testStatus = self::addField($module, $detailsBlock, 'estado_pruebas', 'Estado de pruebas', 15, 'VARCHAR(50)', 'V~O');
		self::ensurePicklistValues($testStatus, array('Pendiente', 'En pruebas', 'Aprobado', 'Rechazado', 'No aplica'));
		self::addField($module, $detailsBlock, 'version_modelo', 'Versión / Modelo', 1, 'VARCHAR(100)', 'V~O');

		$pinpadVersion = self::addField($module, $detailsBlock, 'pinpad_version', 'Versión PINPAD', 15, 'VARCHAR(50)', 'V~O');
		self::ensurePicklistValues($pinpadVersion, array('II ID', 'PLUS', 'PLUS EXT'));
		foreach (array('lados' => 'Lados', 'dispensadores' => 'Dispensadores', 'islas' => 'Islas') as $key => $label) {
			self::addField($module, $detailsBlock, 'pinpad_cantidad_' . $key, 'PINPAD: Cantidad de ' . $label, 7, 'INT(11)', 'I~O');
		}
		self::addField($module, $detailsBlock, 'pinpad_cantidad_equipos', 'PINPAD: Cantidad de equipos', 7, 'INT(11)', 'I~O');

		self::addField($module, $detailsBlock, 'tank_marca_consola', 'TANK: Marca de consola', 1, 'VARCHAR(100)', 'V~O');
		self::addField($module, $detailsBlock, 'tank_cantidad_tanques', 'TANK: Número de tanques', 7, 'INT(11)', 'I~O');
		self::addField($module, $detailsBlock, 'tank_productos', 'TANK: Productos por tanque', 21, 'TEXT', 'V~O');

		$lubricantsDevice = self::addField($module, $detailsBlock, 'lubricants_dispositivo', 'LUBRICANTS: Dispositivo', 15, 'VARCHAR(80)', 'V~O');
		self::ensurePicklistValues($lubricantsDevice, array('Manual mediante códigos', 'Automático con lector Barcode'));

		self::addField($module, $detailsBlock, 'printer_cantidad', 'PRINTER: Cantidad', 7, 'INT(11)', 'I~O');
		self::addField($module, $detailsBlock, 'printer_marca_dispensadores', 'PRINTER: Marca de dispensadores', 1, 'VARCHAR(100)', 'V~O');
		self::addField($module, $detailsBlock, 'barcode_cantidad', 'BARCODE: Cantidad', 7, 'INT(11)', 'I~O');

		self::addField($module, $detailsBlock, 'rfid_hf_cantidad_lectores', 'RFID HF: Cantidad de lectores', 7, 'INT(11)', 'I~O');
		self::addField($module, $detailsBlock, 'rfid_hf_version_lectores', 'RFID HF: Versión de lectores', 1, 'VARCHAR(100)', 'V~O');
		self::addField($module, $detailsBlock, 'rfid_hf_lector_escritorio', 'RFID HF: Lector de escritorio', 56, 'INT(1)', 'C~O');

		$aviVersion = self::addField($module, $detailsBlock, 'avi_rfid_version_kit', 'AVI RFID: Versión de kit UHF', 15, 'VARCHAR(50)', 'V~O');
		self::ensurePicklistValues($aviVersion, array('VF747', 'VFZ09'));
		self::addField($module, $detailsBlock, 'avi_rfid_cantidad_antenas', 'AVI RFID: Cantidad de antenas', 7, 'INT(11)', 'I~O');
		self::addField($module, $detailsBlock, 'avi_rfid_cantidad_lectores', 'AVI RFID: Cantidad de lectores UHF', 7, 'INT(11)', 'I~O');
		self::addField($module, $detailsBlock, 'avi_rfid_cantidad_lectores_escritorio', 'AVI RFID: Cantidad de lectores de escritorio', 7, 'INT(11)', 'I~O', null, null, 'avi_rfid_cantidad_lectores_esc');
		self::ensurePhysicalColumnName('vtiger_moduloestacion', 'avi_rfid_cantidad_lectores_esc', 'avi_rfid_cantidad_lectores_escritorio', 'INT(11)');
		self::addField($module, $notesBlock, 'configuracion', 'Configuración', 21, 'TEXT', 'V~O');
		self::addField($module, $notesBlock, 'novedades', 'Novedades', 21, 'TEXT', 'V~O');
		self::addField($module, $notesBlock, 'observaciones', 'Observaciones', 21, 'TEXT', 'V~O');

		self::addSystemField($module, $informationBlock, 'assigned_user_id', 'Assigned To', 'smownerid', 53, 'V~M');
		self::addSystemField($module, $informationBlock, 'createdtime', 'Created Time', 'createdtime', 70, 'T~O');
		self::addSystemField($module, $informationBlock, 'modifiedtime', 'Modified Time', 'modifiedtime', 70, 'T~O');
		$nameField = self::addField($module, $informationBlock, 'moduloestacion_name', 'Nombre', 2, 'VARCHAR(255)', 'V~M');
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
			'ModuloEstacionDuplicateHandler',
			'modules/Modulos/ModuloEstacionDuplicateHandler.php'
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
		$filter->addField(Vtiger_Field::getInstance('modulo_id', $module), 1);
		$filter->addField(Vtiger_Field::getInstance('estado_instalacion', $module), 2);
		$filter->addField(Vtiger_Field::getInstance('fecha_instalacion', $module), 3);

		Vtiger_Module::fireEvent('ModuloEstacion', Vtiger_Module::EVENT_MODULE_POSTINSTALL);
		$module->addLink('HEADERSCRIPT', 'ModuloEstacionFields', 'layouts/v7/modules/ModuloEstacion/resources/Edit.js');
		return array($module, $accountField);
	}

	public static function install() {
		$catalogModule = Vtiger_Module::getInstance('Modulos');
		$accountsModule = Vtiger_Module::getInstance('Accounts');
		if (!$catalogModule || !$accountsModule) {
			throw new Exception('Se requieren los módulos Accounts y Modulos para instalar ModuloEstacion.');
		}

		$catalogModule->initWebservice();
		self::ensureCatalogFields($catalogModule);
		list($instanceModule) = self::ensureInstanceModule();

		$accountsModule->unsetRelatedList($catalogModule, 'Modulos');
		ModuloEstacionDuplicateHandler::register($catalogModule, $instanceModule);
		ModuloEstacionDuplicateHandler::ensureCatalogRecords();
	}
}