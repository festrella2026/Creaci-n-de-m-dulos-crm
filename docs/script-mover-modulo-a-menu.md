# Script genérico para mover un módulo a un menú de Vtiger

## 1. Objetivo

Este documento explica cómo mover un módulo creado con `vtlib` desde un menú no deseado, por ejemplo `Tools`, a un menú correcto como `Sales`, usando un script reutilizable que puede adaptarse a otros módulos.

El caso de referencia es:

- módulo: `Modulo`
- menú objetivo: `Sales`
- menú actual: `Tools`

---

## 2. Causa del problema

En Vtiger, la ubicación del módulo en la interfaz se asocia con el valor del campo `parent` del módulo.

La consola oficial de `vtlib` establece el valor por defecto en `Tools`, como se puede ver en:

```php
$moduleInformation['parent'] = 'Tools';
```

Esto hace que el módulo aparezca bajo `Herramientas` si no se redefine correctamente.

---

## 3. Regla de negocio

Para que un módulo aparezca en un área del menú, se debe establecer el `parent` correcto:

- `Sales` => aparece en Ventas
- `Marketing` => aparece en Marketing
- `Support` => aparece en Soporte
- `Tools` => aparece en Herramientas

---

## 4. Script genérico para mover un módulo

### Archivo sugerido

```bash
/var/www/html/vtigercrm/move_module_to_menu.php
```

### Código

```php
<?php
/**
 * Script genérico para mover un módulo a un menú de Vtiger.
 * Ejemplo: mover Modulo a Sales.
 *
 * Uso:
 * - editar los valores $moduleName y $targetParent
 * - ejecutar con PHP desde la raíz del proyecto
 */

include_once 'config.inc.php';
include_once 'includes/Loader.php';
include_once 'vtlib/Vtiger/Module.php';
include_once 'vtlib/Vtiger/Menu.php';

$moduleName = 'Modulo';
$targetParent = 'Sales';

$module = Vtiger_Module::getInstance($moduleName);
if (!$module) {
    echo "ERROR: El módulo {$moduleName} no existe.\n";
    exit(1);
}

// Si el módulo ya está relacionado con otro menú, lo quitamos
Vtiger_Menu::detachModule($module);

// Actualizamos el parent para la nueva ubicación
$module->parent = $targetParent;
$module->save();

$menuTarget = Vtiger_Menu::getInstance($targetParent);
if ($menuTarget) {
    $menuTarget->addModule($module);
    echo "INFO: Módulo {$moduleName} agregado al menú {$targetParent}.\n";
} else {
    echo "ERROR: No existe el menú {$targetParent}.\n";
    exit(1);
}

$appMenuTarget = Vtiger_AppMenu::getInstance($targetParent);
if ($appMenuTarget) {
    $appMenuTarget->addModule($module);
    echo "INFO: Módulo {$moduleName} agregado al AppMenu {$targetParent}.\n";
} else {
    echo "INFO: No se encontró AppMenu para {$targetParent}.\n";
}

echo "OK: proceso completado.\n";
```

---

## 5. Ejemplo concreto: `Modulo` en `Sales`

Para el caso indicado, solo debes cambiar estos valores:

```php
$moduleName = 'Modulo';
$targetParent = 'Sales';
```

Con eso, el módulo se moverá a la sección de Ventas.

---

## 6. Script alternativo si quieres modificar el módulo al crearle

Si aún no está creado, puedes definir el menú en el momento de la creación:

```php
<?php
include_once 'vtlib/Vtiger/Module.php';
include_once 'vtlib/Vtiger/Menu.php';

$module = new Vtiger_Module();
$module->name = 'Modulo';
$module->parent = 'Sales';
$module->save();

$menuSales = Vtiger_Menu::getInstance('Sales');
if ($menuSales) {
    $menuSales->addModule($module);
}
```

Este patrón sirve para cualquier módulo nuevo.

---

## 7. Cómo usarlo con otros módulos

Solo cambia estos dos valores:

```php
$moduleName = 'MiModulo';
$targetParent = 'Sales';
```

Por ejemplo:

```php
$moduleName = 'Proyectos';
$targetParent = 'Support';
```

O:

```php
$moduleName = 'Clientes';
$targetParent = 'Marketing';
```

La lógica es la misma para cualquier módulo.

---

## 8. Recomendaciones importantes

- Verifica que el módulo ya exista antes de moverlo.
- Usa el nombre exacto del módulo según Vtiger (`Modulo`, `Leads`, etc.).
- El `parent` debe corresponder con un valor válido del menú principal.
- Si el módulo ya está agregado a otro menú, conviene `detachModule()` antes de reasignarlo.
- Luego de moverlo, recarga la interfaz del CRM para ver el cambio.

---

## 9. Verificación final

Después de ejecutar el script:

1. entra a Vtiger
2. revisa el menú de Ventas
3. busca el módulo `Modulo`
4. confirma que ya no aparece en Herramientas

Si no se visualiza, revisa:

- permisos del proyecto
- existencia del módulo en base de datos
- existencia del directorio de archivos del módulo
- `parent_tabdata.php` o el menú cargado por Vtiger

---

## 10. Resumen

La clave para que un módulo aparezca en la sección correcta es asignar el valor correcto a `parent` y, si es necesario, reasociarlo al menú objetivo usando `Vtiger_Menu::detachModule()` y `addModule()`.

Con este script puedes reutilizar el patrón para cualquier otro módulo del sistema.
