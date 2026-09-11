# Manual operativo para crear módulos y campos en Vtiger con vtlib

## 1. Objetivo

Este manual tiene como finalidad dejar una guía reproducible para crear un módulo personalizado en Vtiger CRM usando `vtlib`, así como agregar campos al módulo, con especial atención a los casos reales encontrados en este proyecto.

---

## 2. Requisitos previos

Antes de comenzar, asegúrate de tener lo siguiente:

- Proyecto Vtiger instalado en `/var/www/html/vtigercrm`
- Acceso al servidor con permisos de lectura/escritura sobre el proyecto
- PHP disponible en línea de comandos
- Composer disponible en el proyecto
- Base de datos Vtiger configurada y funcional

### Verificación rápida

```bash
php -v
php -m | grep -i pdo
ls -ld /var/www/html/vtigercrm
ls -ld /var/www/html/vtigercrm/modules
```

> Si la carpeta `modules` no es escribible por tu usuario, habrá que ajustar permisos o ejecutar el proceso como el usuario correcto.

---

## 3. Diagnóstico de permisos

En este proyecto se detectó que la carpeta `modules` tenía permisos limitados y estaba propiedad de `www-data`.

### Comprobación

```bash
ls -ld /var/www/html/vtigercrm/modules
whoami
```

### Síntoma típico

Cuando intentas crear un módulo o un archivo dentro de `modules`, puedes recibir errores como:

```bash
Permission denied
EACCES
```

### Solución

Puedes corregir permisos de esta forma:

```bash
sudo chown -R www-data:www-data /var/www/html/vtigercrm/modules
sudo chmod -R 775 /var/www/html/vtigercrm/modules
```

O si quieres trabajar como el usuario que tiene permisos, ejecuta el proceso con ese usuario.

---

## 4. Cómo crear un módulo con vtlib

### 4.1 Método recomendado: consola oficial

La forma oficial en Vtiger es emplear la consola de `vtlib`:

```bash
cd /var/www/html/vtigercrm
php -r 'require "vendor/autoload.php"; require "vtlib/tools/console.php";'
```

Esto ejecuta la consola de creación de módulos con el mismo bootstrap necesario para Vtiger.

> Importante: ejecutar `php vtlib/tools/console.php` directamente puede fallar si no se carga primero `vendor/autoload.php`.

### 4.2 Flujo esperado del proceso

El proceso de creación de módulo normalmente incluye:

1. crear el módulo
2. guardar la metadata
3. inicializar tablas
4. crear bloques
5. crear campos
6. definir el identificador de la entidad
7. configurar permisos por defecto
8. habilitar herramientas
9. registrar webservices

### 4.3 Ejemplo de creación manual en PHP

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

$module->setEntityIdentifier($nombre);
$module->setDefaultSharing();
$module->enableTools(array('Import', 'Export', 'Merge'));
$module->initWebservice();
```

---

## 5. Cómo crear un campo en un módulo

### 5.1 Concepto general

Los campos se crean con la clase `Vtiger_Field` y se agregan a un bloque del módulo.

### 5.2 Ejemplo básico

```php
<?php
include_once 'vtlib/Vtiger/Module.php';
include_once 'vtlib/Vtiger/Block.php';
include_once 'vtlib/Vtiger/Field.php';

$module = Vtiger_Module::getInstance('Modulo');
$block = $module->getBlock('LBL_MODULO_INFORMATION');

$field = new Vtiger_Field();
$field->name = 'estado';
$field->label = 'Estado';
$field->uitype = 15; // dropdown/picklist
$field->column = 'estado';
$field->columntype = 'VARCHAR(255)';
$field->typeofdata = 'V~O';

$block->addField($field);
$field->setPicklistValues(array('Instalado', 'No instalado'));
```

### 5.3 Propiedades clave

- `name`: nombre técnico del campo
- `label`: nombre visible en la UI
- `uitype`: tipo del campo
- `column`: nombre de la columna SQL
- `columntype`: tipo SQL
- `typeofdata`: validación y obligatoriedad

### 5.4 Tipo dropdown

Para crear un dropdown en Vtiger se usa:

```php
$field->uitype = 15;
$field->setPicklistValues(array('Instalado', 'No instalado'));
```

---

## 6. Procedimiento operativo recomendado

### Paso 1: validar permisos

```bash
ls -ld /var/www/html/vtigercrm/modules
whoami
```

Si no tienes permisos, corrígelos antes de crear archivos o módulos.

### Paso 2: preparar el entorno

```bash
cd /var/www/html/vtigercrm
php -r 'require "vendor/autoload.php";'
```

### Paso 3: crear o validar el módulo

Puedes usar la consola oficial o un script PHP.

Ejemplo:

```bash
php -r 'require "vendor/autoload.php"; require "vtlib/tools/console.php";'
```

### Paso 4: crear bloque y campos

Tras crear el módulo, crea bloques y campos con `Vtiger_Block` y `Vtiger_Field`.

### Paso 5: validar funcionamiento

- entra a Vtiger
- abre el módulo
- prueba crear un registro
- comprueba que se vea el campo `Estado`
- verifica que aparezcan las opciones del dropdown

---

## 7. Plantilla para un módulo con campo Estado

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

---

## 8. Verificación final

Después de ejecutar el script, revisa lo siguiente:

1. El módulo existe en la base de datos.
2. El archivo del módulo existe en `modules/Modulo`.
3. El bloque existe.
4. El campo `Estado` aparece en la interfaz.
5. El dropdown tiene las opciones configuradas.

Si aparece un error de acceso restringido, revisa permisos y la existencia real del directorio del módulo.

---

## 9. Buenas prácticas

- Usa nombres técnicos en minúsculas y sin espacios.
- Usa `label` con formato legible para la interfaz.
- Comprueba permisos antes de crear archivos.
- Valida el módulo en disco y en base de datos.
- Mantén una copia del script de creación para repetir el proceso.

---

## 10. Resumen ejecutivo

Para trabajar con módulos personalizados en Vtiger con `vtlib`:

1. corrige permisos del proyecto
2. usa la consola con autoload de Composer
3. crea el módulo con `Vtiger_Module`
4. crea bloques y campos con `Vtiger_Block` y `Vtiger_Field`
5. para dropdown usa `uitype = 15` y `setPicklistValues()`
6. verifica que el módulo exista físicamente y no solo en la base de datos

Con este flujo puedes crear módulos y campos de forma ordenada y reproducible.
