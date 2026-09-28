# Modulos 2.1.0: catálogo e instalaciones por estación

Este paquete actualiza `Modulos` desde la versión 2.0.0 e instala `ModuloEstacion`. Está preparado para **Import/Update** de una instalación Vtiger 8.4 que ya contiene `Modulos`.

## Cambios incluidos

- `Modulos` sigue siendo catálogo, con código, categoría, descripción, estado del catálogo, requisito de configuración y el indicador `Obligatorio en todas las estaciones`.
- El registro `PUMP` del catálogo queda marcado obligatorio. Si no existe, se crea con código `MOD-PUMP`.
- El backend impide duplicar nombres de tipos de módulo en el catálogo, y no permite borrar, renombrar o desactivar PUMP ni quitarle la marca de obligatorio.
- `ModuloEstacion` registra Cuenta, Módulo, Estado, fechas, responsables, pruebas, versión/modelo, configuración, novedades y observaciones.
- Estado de instalación se limita al picklist `Instalado` / `No instalado`; el valor inicial es `No instalado`. Los registros de estación existentes con el campo heredado `estado` se convierten y ese campo antiguo se oculta.
- Los campos específicos recomendados se agregan como campos tipados. En PINPAD se guardan cantidades de lados, dispensadores e islas por separado para expresar el esquema `X lado - X dispensador - X isla`.
- El formulario de edición muestra campos específicos según el tipo de catálogo seleccionado.
- La instalación crea una instancia PUMP en estado `No instalado` para cada cuenta activa existente; las cuentas nuevas y restauradas reciben esa instancia automáticamente.
- El backend impide duplicar Cuenta + Módulo y evita eliminar o desvincular la instancia PUMP.
- El lookup de Cuenta agrega la lista relacionada **Módulos** a Accounts; la relación directa Accounts → catálogo se elimina. `ModuloEstacion` no se agrega al menú principal.

## Campos específicos

- PINPAD: versión (`II ID`, `PLUS`, `PLUS EXT`), cantidad de equipos, lados, dispensadores e islas.
- TANK: marca de consola, cantidad de tanques y productos por tanque.
- LUBRICANTS: dispositivo (`Manual mediante códigos`, `Automático con lector Barcode`).
- PRINTER: cantidad y marca de dispensadores.
- BARCODE: cantidad.
- RFID HF: cantidad y versión de lectores, lector de escritorio.
- AVI RFID: versión de kit (`VF747`, `VFZ09`), cantidad de antenas, lectores UHF y lectores de escritorio.

Estos campos son opcionales salvo que el negocio defina requisitos adicionales. Los campos compartidos por tipo quedan ocultos en el formulario al seleccionar otro tipo; los valores se conservan si el tipo cambia.

## Instalación

1. Hacer backup completo de archivos y base de datos. Validar primero en staging.
2. Confirmar que Apache puede escribir en `modules/Modulos/`; Vtiger debe poder reemplazar los archivos PHP del paquete.
3. Importar `Modulos-2.1.0.zip` desde **Settings > Module Management > Import New Module**.
4. Seleccionar **Update** para `Modulos` (la versión anterior quedó registrada como `2.0.0`).
5. Tras la confirmación, revisar **Accounts > una cuenta > Módulos**, el catálogo `Modulos` y las instancias PUMP existentes.
6. Crear una cuenta de prueba y verificar que recibe PUMP `No instalado`. Intentar eliminar/desvincular su instancia PUMP; el backend debe impedirlo.
7. Para una misma cuenta, crear PINPAD u otro módulo una vez; reintentar la misma combinación y confirmar el mensaje de duplicado. En otra cuenta debe poder agregarse el mismo tipo de catálogo.
8. Cambiar el tipo de módulo en el formulario y verificar que aparecen los campos propios; revisar los mismos campos en Reports.

## Importante sobre el significado de PUMP

La regla mantiene una asociación PUMP por cada cuenta activa, no afirma que el equipo ya esté físicamente instalado. Por eso el registro se crea como `No instalado`; el personal debe cambiarlo a `Instalado` al completar el trabajo. La instancia PUMP no se puede borrar ni cambiar a otro catálogo.
