# Modulos 3.0.0: instalación limpia

Este paquete es para una instancia Vtiger CRM 8.4.x donde **todavía no existen** los módulos personalizados `Modulos` ni `ModuloEstacion`. No usarlo como actualización en una instancia que ya tenga el módulo `Modulos`; para esa situación se debe usar la versión de actualización correspondiente.

## Incluye

- Crea el módulo catálogo `Modulos` y sus tablas/campos a partir de `manifest.xml`.
- Crea `ModuloEstacion`, sus tablas y relaciones Accounts → ModuloEstacion → Modulos.
- Precarga en el catálogo: PUMP, PINPAD, TANK, LUBRICANTS, PROMOS, PRICE TOTEM, FLEET BUS, PRINTER, BARCODE, RFID HF y AVI RFID.
- PUMP se precarga como un tipo de catálogo opcional. No se crea automáticamente para ninguna cuenta y puede renombrarse, desactivarse o eliminarse; cada estación puede asociarlo manualmente si lo necesita.
- El Estado de instalación solo admite `Instalado` y `No instalado`.
- Incluye los campos específicos por módulo del Excel, visibilidad condicional en el formulario, protección backend contra duplicados Cuenta + Módulo y validación PINPAD de 2 a 10 equipos.
- No instala Servicios ni ServicioEstacion; esos módulos quedan para una etapa independiente.

## Requisitos

- Vtiger CRM 8.4.x y soporte de eventos (`vtiger_eventhandlers`).
- En la base destino no debe existir un registro en `vtiger_tab` con nombre `Modulos` ni `ModuloEstacion`.
- Hacer backup completo y ejecutar primero en un ambiente de staging.
- El usuario del servidor web debe tener permisos de lectura de los archivos del paquete y permisos de escritura en el árbol de instalación de Vtiger.
- La importación crea la estructura, los campos, las relaciones y el catálogo; no recorre las cuentas existentes ni crea sus PUMP durante la petición web.
- El nombre físico de la columna `avi_rfid_cantidad_lectores_escritorio` se limita al máximo compatible con `vtiger_field.columnname`; el nombre lógico del campo no cambia.
- Si la importación misma agota el tiempo o devuelve un error, no repetirla ni seleccionar **Update** inmediatamente: revisar los logs de PHP/Apache y el estado de `vtiger_tab` antes de limpiar o restaurar el backup.

## Instalación desde cero

1. En Vtiger, ir a **Settings > Module Management > Import New Module**.
2. Cargar `Modulos-3.0.0-INSTALACION-LIMPIA.zip`.
3. Seleccionar **Import / Install New Module**. No seleccionar Update.
4. La importación instala `ModuloEstacion`, sus relaciones y el catálogo; no crea instancias de PUMP automáticamente.
5. Revisar **Ventas > Modulos**. Deben aparecer los once tipos de catálogo; PUMP debe estar desmarcado en **Obligatorio en todas las estaciones**.
6. Abrir una cuenta y agregar manualmente PUMP si corresponde. Confirmar que se puede eliminar la instancia sin afectar la cuenta.
7. En una estación, crear PINPAD una vez; comprobar sus campos. Repetir Cuenta + PINPAD y confirmar el rechazo de duplicado. Probar que la misma entrada PINPAD sí se puede crear en otra cuenta.
8. Confirmar el filtro **All** de `ModuloEstacion`. El ZIP no incluye informes; si se necesitan, crearlos o importarlos por separado desde Reports.

## Catálogo inicial

Los códigos generados son identificadores internos: `MOD-PUMP`, `MOD-PINPAD`, `MOD-TANK`, `MOD-LUBRICANTS`, `MOD-PROMOS`, `MOD-PRICE-TOTEM`, `MOD-FLEET-BUS`, `MOD-PRINTER`, `MOD-BARCODE`, `MOD-RFID-HF` y `MOD-AVI-RFID`. Se pueden ajustar desde el catálogo según la codificación corporativa.
