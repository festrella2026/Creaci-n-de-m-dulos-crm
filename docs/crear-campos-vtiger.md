# Guía para crear campos en un módulo de Vtiger usando vtlib

Esta guía explica de manera simple cómo agregar campos a un módulo custom creado con Vtiger y `vtlib`.

## 1) Requisitos

Antes de crear un campo debes tener:

- El módulo ya creado y registrado en Vtiger.
- El directorio del módulo disponible en `modules/`.
- El archivo principal del módulo existente (por ejemplo: `modules/Modulo/Modulo.php`).
- Permisos de escritura en la carpeta del proyecto.

> Si el módulo no existe o no está en disco, primero crea el módulo. Si aparece el error de archivo restringido, normalmente significa que el módulo quedó registrado en la base de datos pero no se creó físicamente en `modules/`.

---

## 2) Estructura general de un campo

En Vtiger, un campo se crea con la clase `Vtiger_Field` y se agrega a un bloque del módulo.

### Ejemplo base

```php
<?php
include_once 'vtlib/Vtiger/Module.php';
include_once 'vtlib/Vtiger/Block.php';
include_once 'vtlib/Vtiger/Field.php';

$module = Vtiger_Module::getInstance('Modulo');
$block = $module->getBlock('LBL_');

$field = new Vtiger_Field();
$field->name = 'estado';
$field->label = 'Estado';
$field->uitype = 15; // dropdown / picklist
$field->column = 'estado';
$field->columntype = 'VARCHAR(255)';
$field->typeofdata = 'V~O';

$block->addField($field);
$field->setPicklistValues(array('Instalado', 'No instalado'));
```

### Descripción de las propiedades

- `name`: nombre interno del campo, sin espacios, en minúsculas.
- `label`: nombre que se muestra en la interfaz.
- `uitype`: tipo del campo. Para dropdown usa `15`.
- `column`: nombre de la columna en la tabla del módulo.
- `columntype`: tipo de dato SQL.
- `typeofdata`: reglas de validación. `V~O` significa texto requerido opcional.

---

## 3) Cómo identificar el bloque correcto

Normalmente el módulo tiene uno o varios bloques como:

- Información principal
- Detalles adicionales
- Información de sistema

Puedes obtener un bloque por su nombre o por índice.

```php
$block = $module->getBlock('LBL_MODULO_INFORMATION');
```

Si no conoces el nombre exacto, puedes obtener el primer bloque:

```php
$blocks = $module->getBlocks();
$block = $blocks[0];
```

---

## 4) Ejemplo completo para crear un campo dropdown `Estado`

Este ejemplo crea un campo con dos opciones:

- Instalado
- No instalado

```php
<?php
include_once 'vtlib/Vtiger/Module.php';
include_once 'vtlib/Vtiger/Block.php';
include_once 'vtlib/Vtiger/Field.php';

$module = Vtiger_Module::getInstance('Modulo');
$block = $module->getBlock('LBL_MODULO_INFORMATION');

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

### Resultado esperado

En la vista del módulo aparecerá un campo llamado `Estado` con un dropdown que contiene:

- Instalado
- No instalado

---

## 5) Insertar el código en un script

Puedes guardar este código en un archivo PHP dentro del proyecto, por ejemplo:

```bash
/var/www/html/vtigercrm/add_estado_field.php
```

Luego lo ejecutas con PHP:

```bash
php add_estado_field.php
```

Si el módulo ya está en la aplicación, el campo se registrará y quedará disponible en la interfaz.

---

## 6) Verificación rápida

Después de ejecutar el script:

1. Ingresa a Vtiger.
2. Abre el módulo `Modulo`.
3. Crea o edita un registro.
4. Revisa si aparece el campo `Estado`.
5. Confirma que el dropdown tenga las opciones esperadas.

---

## 7) Recomendaciones importantes

- Usa nombres en minúsculas y sin espacios para `name`.
- El `label` puede llevar espacios y mayúsculas.
- El módulo debe existir físicamente en `modules/` y en la DB.
- Si el campo no aparece, revisa permisos, bloque, nombre del módulo y el archivo de módulo principal.
- En Linux, a veces el problema no es Vtiger sino permisos de escritura en `modules/` y `languages/`.

---

## 8) Ejemplo práctico completo de creación de módulo + campo

```php
<?php
include_once 'vtlib/Vtiger/Module.php';
include_once 'vtlib/Vtiger/Block.php';
include_once 'vtlib/Vtiger/Field.php';

$module = new Vtiger_Module();
$module->name = 'Modulo';
$module->save();

$module->initTables();

$block = new Vtiger_Block();
$block->label = 'LBL_MODULO_INFORMATION';
$module->addBlock($block);

$nombre = new Vtiger_Field();
$nombre->name = 'modulo_name';
$nombre->label = 'Nombre';
$nombre->uitype = 2;
$nombre->column = 'modulo_name';
$nombre->columntype = 'VARCHAR(255)';
$nombre->typeofdata = 'V~M';
$block->addField($nombre);

$estado = new Vtiger_Field();
$estado->name = 'estado';
$estado->label = 'Estado';
$estado->uitype = 15;
$estado->column = 'estado';
$estado->columntype = 'VARCHAR(255)';
$estado->typeofdata = 'V~O';
$block->addField($estado);
$estado->setPicklistValues(array('Instalado', 'No instalado'));

$module->setEntityIdentifier($nombre);
$module->setDefaultSharing();
$module->enableTools(array('Import', 'Export'));
$module->initWebservice();
```

---

## 9) Resumen

Para crear un campo en Vtiger usando `vtlib`:

1. Obtener el módulo.
2. Obtener o crear el bloque.
3. Crear un objeto `Vtiger_Field`.
4. Definir propiedades como `name`, `label`, `uitype` y `typeofdata`.
5. Agregar el campo al bloque.
6. Para dropdown: usar `setPicklistValues()`.
7. Revisar que el módulo exista en disco y con permisos correctos.

Si quieres, puedes usar este patrón directamente para crear el campo `Estado` en el módulo `Modulo`.
