<?php
/**
 * Script robusto para mover un módulo Vtiger a un menú principal.
 * Caso de uso: mover el módulo Modulo al menú Sales.
 */

include_once 'config.inc.php';
include_once 'includes/Loader.php';
include_once 'vtlib/Vtiger/Module.php';
include_once 'vtlib/Vtiger/Menu.php';

echo "Iniciando proceso...\n";
echo "Módulo: Modulo\n";
echo "Menú destino: Sales\n";

if (!class_exists('Vtiger_Module') || !class_exists('Vtiger_Menu')) {
    echo "ERROR: No se cargaron las clases de vtlib. Verifica la configuración del proyecto.\n";
    exit(1);
}

$moduleName = 'Modulo';
$targetParent = 'Sales';

$module = Vtiger_Module::getInstance($moduleName);
if (!$module) {
    echo "ERROR: El módulo {$moduleName} no existe en la metadata de Vtiger.\n";
    exit(1);
}

$menu = Vtiger_Menu::getInstance($targetParent);
if (!$menu) {
    echo "ERROR: El menú {$targetParent} no existe en vtiger_parenttab.\n";
    exit(1);
}

echo "INFO: Módulo encontrado: {$module->name}\n";
echo "INFO: Menú encontrado: {$menu->label}\n";

try {
    Vtiger_Menu::detachModule($module);
    echo "INFO: Asociación previa removida.\n";
} catch (Exception $e) {
    echo "WARN: No se pudo quitar la asociación previa: " . $e->getMessage() . "\n";
}

$module->parent = $targetParent;
$module->save();
$menu->addModule($module);

echo "OK: El módulo {$moduleName} fue movido al menú {$targetParent}.\n";
exit(0);
