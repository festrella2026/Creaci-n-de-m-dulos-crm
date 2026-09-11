# Documento técnico del proyecto Vtiger CRM

## 1. Objetivo del documento

Este documento registra los cambios, diagnósticos, validaciones y trabajos realizados sobre el proyecto Vtiger CRM en este entorno, con el fin de dejar una referencia técnica clara sobre la creación de módulos personalizados, el uso de `vtlib`, la resolución de errores de entorno y la preparación para agregar campos a módulos custom.

---

## 2. Información general del proyecto

- Proyecto base: Vtiger CRM
- Versión del sistema: 8.4.0
- Ruta del proyecto: `/var/www/html/vtigercrm`
- Herramienta principal utilizada para creación y gestión de módulos: `vtlib`
- Módulo de referencia trabajado: `Modulo`
- Campo objetivo para pruebas: `Estado`

---

## 3. Trabajo realizado sobre el módulo `Modulo`

### 3.1 Objetivo

Se buscó crear un módulo personalizado llamado `Modulo` usando la librería `vtlib`, validando tanto el flujo oficial como la creación manual de módulos en Vtiger.

### 3.2 Flujo analizado

Se revisaron las APIs y patrones de Vtiger para creación de módulos, incluyendo:

- `Vtiger_Module`
- `Vtiger_Block`
- `Vtiger_Field`
- `vtlib/tools/console.php`
- `vtlib/ModuleDir/6.0.0/ModuleName.php`

Se validó que la creación de un módulo no se limita a crear un registro en la base de datos, sino que también requiere:

- registrar el módulo en la metadata del sistema
- crear la estructura del módulo en disco
- generar tablas asociadas
- crear bloque(s)
- crear campo(s)
- definir identificador de entidad
- configurar permisos por defecto
- habilitar herramientas e integración webservice

### 3.3 Problemas detectados

#### a) Script de creación manual incompleto

Se detectó que el script inicial no cumplía con el flujo real de creación de módulos de Vtiger. La creación correcta requiere más que un `save()`: también se necesitan operaciones como `initTables()`, `addBlock()`, `addField()`, `setEntityIdentifier()`, entre otras.

#### b) Ejecución directa de `console.php` fallaba

La ejecución directa de `php vtlib/tools/console.php` no funcionaba en este entorno porque no estaba cargado el autoload de Composer. La ejecución correcta se validó con:

```bash
php -r 'require "vendor/autoload.php"; require "vtlib/tools/console.php";'
```

Esto demostró que el problema era de entorno y bootstrap, no de la lógica del módulo.

#### c) Problema de permisos en `modules/`

Se confirmó que la carpeta `modules` estaba bajo propiedad de `www-data` y no era escribible por el usuario actual del shell. Esto causó que el módulo quedara parcialmente registrado en base de datos pero sin creación real del directorio físico y del archivo PHP del módulo.

Esto explica el error típico de Vtiger:

> Sorry! Attempt to access restricted file.

#### d) Módulo con metadatos en base de datos pero sin archivo en disco

Se verificó que el módulo podía aparecer en contexto de base de datos, pero el directorio `modules/Modulo` no existía. Esto es una condición crítica para que Vtiger pueda cargar el módulo correctamente.

---

## 4. Validación de la creación de módulos con vtlib

Se validó el patrón correcto para crear un módulo usando `vtlib`:

```php
$module = new Vtiger_Module();
$module->name = 'Modulo';
$module->save();
$module->initTables();

$block = new Vtiger_Block();
$block->label = 'LBL_MODULO_INFORMATION';
$module->addBlock($block);

$field = new Vtiger_Field();
$field->name = 'modulo_name';
$field->label = 'Nombre';
$field->uitype = 2;
$field->column = 'modulo_name';
$field->columntype = 'VARCHAR(255)';
$field->typeofdata = 'V~M';
$block->addField($field);

$module->setEntityIdentifier($field);
$module->setDefaultSharing();
$module->enableTools(array('Import', 'Export', 'Merge'));
$module->initWebservice();
```

Este flujo fue importante porque refleja la estructura real de Vtiger para registrar módulos de negocio con tablas y campos.

---

## 5. Trabajo de reversión y limpieza

Se realizó una operación de limpieza para deshacer la creación incompleta del módulo `Modulo`.

Se validó que el módulo había quedado parcialmente creado en base de datos y luego fue eliminado mediante la API de `Vtiger_Module` para evitar dejar un estado inconsistente.

La lógica de reversión se basó en:

```php
$module = Vtiger_Module::getInstance('Modulo');
if ($module) {
    $module->delete();
}
```

Se verificaron además las tablas asociadas para confirmar que no quedaran referencias del módulo a nivel de base de datos.

---

## 6. Diagnóstico del problema de permisos

Se comprobó que la causa principal del problema de creación no era Vtiger sino el entorno de Linux/servidor:

- directorio `modules` con permisos limitados
- propietario de la carpeta: `www-data`
- usuario actual sin permisos de escritura

Esto hacía imposible crear el árbol del módulo en el sistema de archivos sin cambiar permisos o ejecutar como usuario correcto.

### Recomendación

En entornos Linux con Vtiger, normalmente se requiere ejecutar la creación como el usuario que tiene permisos sobre `/var/www/html/vtigercrm/modules` o ajustar la propiedad del directorio.

---

## 7. Trabajo realizado para agregar el campo `Estado`

Se definió el patrón para crear un campo dropdown en el módulo `Modulo`.

### Patrón recomendado

```php
$estado = new Vtiger_Field();
$estado->name = 'estado';
$estado->label = 'Estado';
$estado->uitype = 15;
$estado->column = 'estado';
$estado->columntype = 'VARCHAR(255)';
$estado->typeofdata = 'V~O';

$block->addField($estado);
$estado->setPicklistValues(array(
    'Instalado',
    'No instalado'
));
```

### Objetivo

El campo `Estado` debe quedar como una lista desplegable con los valores:

- Instalado
- No instalado

### Observación importante

Para que el campo se muestre correctamente, además de crear el campo, deben cumplirse condiciones de:

- módulo existente
- bloque correcto
- permisos de escritura
- base de datos sincronizada
- archivo del módulo cargado por Vtiger

---

## 8. Documentación generada

Se creó una guía específica para campos en el proyecto en la ruta:

- `docs/crear-campos-vtiger.md`

La guía explica:

- cómo obtener el módulo
- cómo elegir el bloque
- cómo crear un `Vtiger_Field`
- cómo crear un picklist/dropdown
- cómo validar el resultado final

---

## 9. Estado actual del proyecto

### Lo que quedó validado:

- flujo correcto de creación de módulo usando `vtlib`
- uso correcto de `vtlib/tools/console.php` con autoload de Composer
- detección de error por permisos del sistema
- proceso de rollback del módulo parcialmente creado
- patrón correcto para crear campo dropdown `Estado`
- generación de documentación técnica en el proyecto

### Lo que requiere atención si se desea continuar con la creación en vivo:

- corregir permisos de escritura en `modules/`
- ejecutar la creación con el usuario adecuado
- volver a registrar el módulo
- crear el bloque
- crear el campo `Estado` con sus valores

---

## 10. Conclusión

El proyecto quedó documentado con un diagnóstico correcto del flujo de creación de módulos Vtiger y con una solución técnica clara para:

1. crear módulos mediante `vtlib`
2. resolver problemas de bootstrap y entorno
3. identificar errores de archivo restringido
4. limpiar módulos incompletos
5. crear campos tipo dropdown con `Estado`

Este registro sirve como referencia técnica para futuras ampliaciones del sistema o para usuarios que necesiten replicar el proceso sin perder contexto del proyecto.
