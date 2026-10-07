# Servicios 1.0.0 - Procedimiento de verificación

Este documento describe la verificación mínima recomendada antes de dar el módulo por validado.

## 1. Verificación de instalación

### Paso 1
- Importar el ZIP desde `Settings > Module Management > Import New Module`.
- Confirmar que no aparecen errores de PHP ni de importación.

### Paso 2
- Verificar en `Ventas` que existe `Servicios`.
- Verificar que existe `ServiciosEstacion`.
- Revisar si el módulo root quedó asociado al menú de `Sales`.

## 2. Verificación del catálogo

- Abrir `Ventas > Servicios`.
- Confirmar que aparecen los 12 servicios del catálogo inicial.
- Confirmar que el campo `Código` es único por servicio.
- Confirmar que `Nombre` y `Código` se guardan correctamente.

## 3. Verificación de integridad

### Criterio 1: no duplicados por cuenta y servicio
- Crear una instancia con una cuenta y un servicio.
- Intentar crear la misma combinación otra vez.
- Confirmar que se rechaza la creación con una excepción o mensaje de validación.

### Criterio 2: nombre automático
- Crear una instancia desde una cuenta.
- Confirmar que el campo `Identificador de instalación` no se muestra durante el alta.
- Guardar la instancia y confirmar que el identificador se genera y se muestra como `Cuenta - Servicio`.

### Criterio 3: campos dependiendo del servicio
- Seleccionar `PINPAD` no aplica porque este módulo no usa ese catálogo; aquí se prueba cada servicio del listado.
- Elegir `NOTICES` y comprobar que aparecen los campos de notificaciones.
- Elegir `PAY FUELCONTROL-DATAFAST` y comprobar que aparecen los campos específicos de más de un servicio.
- Confirmar que los campos de otros servicios quedan ocultos.

## 4. Verificación de relación con Accounts

- Abrir una cuenta existente.
- Revisar la lista relacionada de servicios de la estación.
- Crear una nueva asociación de servicio a esa cuenta.
- Confirmar que el módulo queda relacionado correctamente.

## 5. Verificación de filtros e informes

- Crear un filtro simple por `Cuenta`, `Servicio` y `Estado de instalación`.
- Verificar que los campos aparecen en listas.
- Confirmar que los informes de base se pueden construir con relaciones de cuenta y servicio.

## 6. Verificación de seguridad

- Confirmar que no se almacenan secretos ni tokens en texto plano.
- Confirmar que los campos sensibles se utilizan como indicadores o banderas, no como credenciales.

## 7. Lista de errores posibles y corrección

### Error: "Invalid file provided for module import"
- Revisar el orden de los archivos dentro del ZIP, no solo su contenido.
- En la validación de Vtiger 8.4, el último archivo `languages/en_us/*.php` debe coincidir con el `<name>` del módulo en `manifest.xml`.
- Para este paquete, `languages/en_us/Servicios.php` debe quedar después de `languages/en_us/ServiciosEstacion.php`.
- Comprobar el ZIP final después de empaquetar; ordenar los archivos alfabéticamente puede dejar el idioma del módulo raíz antes del idioma del módulo relacionado.
- Si falla la validación, volver a descargar el ZIP corregido y cargarlo como módulo nuevo. El ZIP de instalación limpia actual deja `languages/en_us/Servicios.php` al final del directorio central del ZIP.

### Error: no aparece el módulo en el menú
- Revisar que la módulo `Servicios` se haya guardado correctamente.
- Confirmar que el `manifest.xml` define `parent>Sales`.

### Error: no se crea `ServiciosEstacion`
- Revisar `module.postinstall` del módulo raíz.
- Confirmar que `ServiciosEstacionInstaller::install()` se llama.

### Error: duplicados permitidos
- Revisar la validación del manejador de eventos.
- Confirmar que la query excluye el propio registro al editar.

### Error: no se ocultan los campos específicos
- Revisar el JS condicional de [docs/.package-build/Servicios-1.0.0-Limpia/layouts/v7/modules/ServiciosEstacion/resources/Edit.js](docs/.package-build/Servicios-1.0.0-Limpia/layouts/v7/modules/ServiciosEstacion/resources/Edit.js).
- Confirmar que la selección de `servicio_id` actualiza los campos visibles y que al limpiar la referencia se ocultan.
- Confirmar que el nombre elegido, por ejemplo `PAY FUELCONTROL-DATAFAST`, coincide con una clave del mapa de campos y que solo se ocultan las celdas de esos campos, no la fila completa.

## 8. Corrección aplicada: columnas truncadas

### Causa
- Al intentar agregar una instalación de servicio, Vtiger devolvía `El registro al que intenta acceder no existe`.
- La revisión encontró 14 campos activos de `ServiciosEstacion` cuyo `vtiger_field.columnname` había quedado truncado al límite de 30 caracteres.
- Algunos campos distintos de Datafast y Medianet compartían el mismo nombre truncado, por lo que la consulta de recuperación de Vtiger apuntaba a columnas inexistentes o ambiguas.

### Cambio realizado
- Se asignaron nombres físicos cortos y únicos a las 14 columnas afectadas (`svc_pay_*` y `svc_vpn_cantidad_activas`), conservando sin cambios los nombres lógicos de los campos y sus etiquetas.
- Se renombraron las columnas físicas de `vtiger_serviciosestacion` y se actualizó `vtiger_field.columnname` para que ambos esquemas coincidan.
- Los 14 campos no tenían valores no nulos al momento de la migración; las operaciones `ALTER TABLE ... CHANGE COLUMN` preservaron sus valores existentes.
- Se incorporó al instalador una migración idempotente para que instalaciones futuras asignen los nombres compatibles y para que una actualización pueda reparar el mapeo existente.

### Pruebas ejecutadas
- Auditoría posterior: 44 campos activos de `ServiciosEstacion`, 0 columnas físicas faltantes y 0 mapeos duplicados.
- Prueba con el flujo normal de Vtiger: se creó y recuperó una instalación temporal del servicio `NOTICES` para una cuenta válida; la operación pasó. Se eliminó únicamente el registro temporal después de la prueba.
- Estado posterior: `vtiger_serviciosestacion` quedó sin filas de prueba.
- ZIP `Servicios-1.0.0-INSTALACION-LIMPIA.zip`: estructura/CRC válida, 12 entradas idénticas al staging y todos los archivos PHP incluidos pasaron `php -l`.

### Datos preexistentes observados
- Antes de la prueba se encontraron 17 filas huérfanas en `vtiger_crmentity` con `setype='ServiciosEstacion'`, sin fila correspondiente en `vtiger_serviciosestacion`. No se modificaron ni eliminaron como parte de esta corrección.

## Conclusión

Si estas pruebas pasan, la implementación queda lista para validación funcional en staging y preparación para producción.

Se recomienda no ejecutar en producción sin un backup previo y sin una prueba real en entorno controlado.
