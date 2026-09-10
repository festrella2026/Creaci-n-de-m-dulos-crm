<?php

require_once 'vtlib/Vtiger/Module.php';

$moduleName = 'Modulo';
$entityFieldLabel = 'Nombre';

if (Vtiger_Module::getInstance($moduleName)) {
    echo "El módulo {$moduleName} ya existe.\n";
    exit(0);
}

$module = new Vtiger_Module();
$module->name = $moduleName;
$module->label = $moduleName;
$module->parent = 'Tools';
$module->save();

$module->initTables();

$block = new Vtiger_Block();
$block->label = 'LBL_' . strtoupper($moduleName) . '_INFORMATION';
$module->addBlock($block);

$field = new Vtiger_Field();
$field->name = 'nombre';
$field->label = $entityFieldLabel;
$field->uitype = 2;
$field->column = 'nombre';
$field->columntype = 'VARCHAR(255)';
$field->typeofdata = 'V~M';
$block->addField($field);

$module->setEntityIdentifier($field);

$assignedTo = new Vtiger_Field();
$assignedTo->name = 'assigned_user_id';
$assignedTo->label = 'Assigned To';
$assignedTo->table = 'vtiger_crmentity';
$assignedTo->column = 'smownerid';
$assignedTo->uitype = 53;
$assignedTo->typeofdata = 'V~M';
$block->addField($assignedTo);

$module->setDefaultSharing();
$module->enableTools(array('Import', 'Export', 'Merge'));
$module->initWebservice();

$filter = new Vtiger_Filter();
$filter->name = 'All';
$filter->isdefault = true;
$module->addFilter($filter);
$filter->addField($field)->addField($assignedTo, 1);

echo "Módulo {$moduleName} creado correctamente.\n";
