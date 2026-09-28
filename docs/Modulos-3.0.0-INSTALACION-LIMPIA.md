# Modulos 3.0.0: instalación limpia

Este paquete es para una instancia Vtiger CRM 8.4.x donde **todavía no existen** los módulos personalizados `Modulos` ni `ModuloEstacion`. No usarlo como actualización en una instancia que ya tenga el módulo `Modulos`; para esa situación se debe usar la versión de actualización correspondiente.

## Incluye

- Crea el módulo catálogo `Modulos` y sus tablas/campos a partir de `manifest.xml`.
- Crea `ModuloEstacion`, sus tablas y relaciones Accounts → ModuloEstacion → Modulos.
- Precarga en el catálogo: PUMP, PINPAD, TANK, LUBRICANTS, PROMOS, PRICE TOTEM, FLEET BUS, PRINTER, BARCODE, RFID HF y AVI RFID.
- PUMP queda marcado como obligatorio. Crea una instancia PUMP `No instalado` para cada cuenta existente y para cada cuenta nueva/restaurada. El backend impide borrar o desvincular esa instancia.
- El Estado de instalación solo admite `Instalado` y `No instalado`.
- Incluye los campos específicos por módulo del Excel, visibilidad condicional en el formulario, protección backend contra duplicados Cuenta + Módulo y validación PINPAD de 2 a 10 equipos.
- No instala Servicios ni ServicioEstacion; esos módulos quedan para una etapa independiente.

## Requisitos

- Vtiger CRM 8.4.x y soporte de eventos (`vtiger_eventhandlers`).
- En la base destino no debe existir un registro en `vtiger_tab` con nombre `Modulos` ni `ModuloEstacion`.
- Hacer backup completo y ejecutar primero en un ambiente de staging.
- El usuario del servidor web debe tener permisos de lectura de los archivos del paquete y permisos de escritura en el árbol de instalación de Vtiger.

## Instalación desde cero

1. En Vtiger, ir a **Settings > Module Management > Import New Module**.
2. Cargar `Modulos-3.0.0-INSTALACION-LIMPIA.zip`.
3. Seleccionar **Import / Install New Module**. No seleccionar Update.
4. Esperar que finalice la importación y que el evento `module.postinstall` cree `ModuloEstacion` y las relaciones.
5. Revisar **Ventas > Modulos**. Deben aparecer los once tipos de catálogo; `PUMP` debe figurar como obligatorio.
6. Abrir una cuenta. Debe aparecer la lista relacionada **Módulos** con PUMP en estado `No instalado`.
7. Crear una nueva cuenta y confirmar que también se genere PUMP. Intentar borrarlo o cambiarlo; la operación debe ser rechazada.
8. En una estación, crear PINPAD una vez; comprobar sus campos. Repetir Cuenta + PINPAD y confirmar el rechazo de duplicado. Probar que la misma entrada PINPAD sí se puede crear en otra cuenta.
9. Confirmar filtros e informes de `ModuloEstacion`.

## Catálogo inicial

Los códigos generados son identificadores internos: `MOD-PUMP`, `MOD-PINPAD`, `MOD-TANK`, `MOD-LUBRICANTS`, `MOD-PROMOS`, `MOD-PRICE-TOTEM`, `MOD-FLEET-BUS`, `MOD-PRINTER`, `MOD-BARCODE`, `MOD-RFID-HF` y `MOD-AVI-RFID`. Se pueden ajustar desde el catálogo según la codificación corporativa.
