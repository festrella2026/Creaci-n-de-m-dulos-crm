<?php

chdir(__DIR__);

require_once 'vendor/autoload.php';
require_once 'includes/main/WebUI.php';
require_once 'vtlib/Vtiger/Module.php';

$moduleName = 'Modulos';
$entityFieldName = 'modulos_name';
$entityFieldLabel = 'Nombre';

if (Vtiger_Module::getInstance($moduleName)) {
    echo "El módulo {$moduleName} ya existe.\n";
    exit(0);
}

echo "Creando módulo {$moduleName}...\n";

$module = new Vtiger_Module();
$module->name = $moduleName;
$module->label = $moduleName;
$module->parent = 'Sales';
$module->save();
$module->initTables();

$block = new Vtiger_Block();
$block->label = 'LBL_' . strtoupper($moduleName) . '_INFORMATION';
$module->addBlock($block);

$nameField = new Vtiger_Field();
$nameField->name = $entityFieldName;
$nameField->label = $entityFieldLabel;
$nameField->uitype = 2;
$nameField->column = $entityFieldName;
$nameField->columntype = 'VARCHAR(255)';
$nameField->typeofdata = 'V~M';
$block->addField($nameField);
$module->setEntityIdentifier($nameField);

$assignedUserField = new Vtiger_Field();
$assignedUserField->name = 'assigned_user_id';
$assignedUserField->label = 'Assigned To';
$assignedUserField->table = 'vtiger_crmentity';
$assignedUserField->column = 'smownerid';
$assignedUserField->uitype = 53;
$assignedUserField->typeofdata = 'V~M';
$block->addField($assignedUserField);

$createdTimeField = new Vtiger_Field();
$createdTimeField->name = 'createdtime';
$createdTimeField->label = 'Created Time';
$createdTimeField->table = 'vtiger_crmentity';
$createdTimeField->column = 'createdtime';
$createdTimeField->uitype = 70;
$createdTimeField->typeofdata = 'T~O';
$createdTimeField->displaytype = 2;
$block->addField($createdTimeField);

$modifiedTimeField = new Vtiger_Field();
$modifiedTimeField->name = 'modifiedtime';
$modifiedTimeField->label = 'Modified Time';
$modifiedTimeField->table = 'vtiger_crmentity';
$modifiedTimeField->column = 'modifiedtime';
$modifiedTimeField->uitype = 70;
$modifiedTimeField->typeofdata = 'T~O';
$modifiedTimeField->displaytype = 2;
$block->addField($modifiedTimeField);

$statusField = new Vtiger_Field();
$statusField->name = 'estado';
$statusField->label = 'Estado';
$statusField->uitype = 15;
$statusField->columntype = 'VARCHAR(255)';
$statusField->typeofdata = 'V~O';
$statusField->defaultvalue = 'No instalado';
$block->addField($statusField);
$statusField->setPicklistValues(array('No instalado', 'Instalado'));

$filter = new Vtiger_Filter();
$filter->name = 'All';
$filter->isdefault = true;
$module->addFilter($filter);
$filter->addField($nameField)
    ->addField($assignedUserField, 1)
    ->addField($statusField, 2)
    ->addField($createdTimeField, 3);

$module->setDefaultSharing();
$module->enableTools(array('Import', 'Export', 'Merge'));
$module->initWebservice();

$modulePath = 'modules/' . $moduleName;
if (!is_dir($modulePath) && !mkdir($modulePath, 0755, true)) {
    throw new Exception("No se pudo crear {$modulePath}.");
}

$templatePath = 'vtlib/ModuleDir/6.0.0/ModuleName.php';
$moduleFile = file_get_contents($templatePath);
$replacements = array(
    'ModuleName' => $moduleName,
    '<modulename>' => strtolower($moduleName),
    '<entityfieldlabel>' => $entityFieldLabel,
    '<entitycolumn>' => $entityFieldName,
    '<entityfieldname>' => $entityFieldName,
);
$moduleFile = str_replace(array_keys($replacements), array_values($replacements), $moduleFile);
file_put_contents($modulePath . '/' . $moduleName . '.php', $moduleFile);

Settings_MenuEditor_Module_Model::addModuleToApp($module->name, $module->parent);

$accountsModule = Vtiger_Module::getInstance('Accounts');
if (!$accountsModule) {
    throw new Exception('No se encontró el módulo Accounts para crear la relación.');
}

$accountsModule->setRelatedList($module, 'Modulos', array('ADD', 'SELECT'));

echo "Módulo {$moduleName} creado correctamente en Sales.\n";
echo "Archivo: {$modulePath}/{$moduleName}.php\n";
echo "Relación Accounts -> Modulos creada correctamente.\n";
