<?php

require_once 'vtlib/Vtiger/Module.php';
require_once 'vtlib/Vtiger/Event.php';

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

	private static function addField($module, $block, $name, $label, $uitype, $columnType, $dataType, $relatedModules = null, $defaultValue = null) {
		$field = Vtiger_Field::getInstance($name, $module);
		if (!$field) {
			$field = new Vtiger_Field();
			$field->name = $name;
			$field->label = $label;
			$field->uitype = $uitype;
			$field->column = $name;
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
	}

	private static function ensureInstanceModule() {
		$entityClass = 'modules/ModuloEstacion/ModuloEstacion.php';
		$entityDirectory = dirname($entityClass);
		if (!is_dir($entityDirectory) && !mkdir($entityDirectory, 0755, true)) {
			throw new Exception('No se pudo crear el directorio de ModuloEstacion.');
		}
		if (!copy('modules/Modulos/ModuloEstacion.php', $entityClass)) {
			throw new Exception('No se pudo instalar la clase de ModuloEstacion.');
		}

		$module = Vtiger_Module::getInstance('ModuloEstacion');
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
		$instanceStatus = self::addField($module, $informationBlock, 'estado', 'Estado', 15, 'VARCHAR(50)', 'V~M', null, 'Pendiente');
		self::ensurePicklistValues($instanceStatus, array('Pendiente', 'Instalado', 'Activo', 'Inactivo', 'Cancelado'));

		self::addField($module, $detailsBlock, 'fecha_prevista_instalacion', 'Fecha prevista de instalación', 5, 'DATE', 'D~O');
		self::addField($module, $detailsBlock, 'fecha_instalacion', 'Fecha de instalación', 5, 'DATE', 'D~O');
		self::addField($module, $detailsBlock, 'fecha_activacion', 'Fecha de activación', 5, 'DATE', 'D~O');
		self::addField($module, $detailsBlock, 'responsable_tecnico', 'Responsable técnico', 1, 'VARCHAR(100)', 'V~O');
		self::addField($module, $detailsBlock, 'departamento_responsable', 'Departamento responsable', 1, 'VARCHAR(100)', 'V~O');
		$testStatus = self::addField($module, $detailsBlock, 'estado_pruebas', 'Estado de pruebas', 15, 'VARCHAR(50)', 'V~O');
		self::ensurePicklistValues($testStatus, array('Pendiente', 'En pruebas', 'Aprobado', 'Rechazado', 'No aplica'));
		self::addField($module, $detailsBlock, 'version_modelo', 'Versión / Modelo', 1, 'VARCHAR(100)', 'V~O');
		self::addField($module, $notesBlock, 'configuracion', 'Configuración', 21, 'TEXT', 'V~O');
		self::addField($module, $notesBlock, 'novedades', 'Novedades', 21, 'TEXT', 'V~O');
		self::addField($module, $notesBlock, 'observaciones', 'Observaciones', 21, 'TEXT', 'V~O');

		self::addSystemField($module, $informationBlock, 'assigned_user_id', 'Assigned To', 'smownerid', 53, 'V~M');
		self::addSystemField($module, $informationBlock, 'createdtime', 'Created Time', 'createdtime', 70, 'T~O');
		self::addSystemField($module, $informationBlock, 'modifiedtime', 'Modified Time', 'modifiedtime', 70, 'T~O');
		$nameField = self::addField($module, $informationBlock, 'moduloestacion_name', 'Nombre', 2, 'VARCHAR(255)', 'V~M');
		$module->setEntityIdentifier($nameField);
		$module->setDefaultSharing();
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
		$filter->addField(Vtiger_Field::getInstance('estado', $module), 2);
		$filter->addField(Vtiger_Field::getInstance('fecha_instalacion', $module), 3);

		Vtiger_Module::fireEvent('ModuloEstacion', Vtiger_Module::EVENT_MODULE_POSTINSTALL);
		return array($module, $accountField);
	}

	public static function install() {
		$catalogModule = Vtiger_Module::getInstance('Modulos');
		$accountsModule = Vtiger_Module::getInstance('Accounts');
		if (!$catalogModule || !$accountsModule) {
			throw new Exception('Se requieren los módulos Accounts y Modulos para instalar ModuloEstacion.');
		}

		self::ensureCatalogFields($catalogModule);
		list($instanceModule) = self::ensureInstanceModule();

		$accountsModule->unsetRelatedList($catalogModule, 'Modulos');
	}
}