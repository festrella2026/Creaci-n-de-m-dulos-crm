# Servicios 1.0.0 - Instalación limpia

Este paquete está diseñado para una instancia Vtiger CRM 8.4.x donde aún no existen los módulos personalizados `Servicios` ni `ServiciosEstacion`.

No se recomienda utilizarlo como actualización en una instancia que ya tenga estos módulos.

## Qué instala

- Crea el módulo catálogo `Servicios` y sus tablas/campos base.
- Crea el módulo `ServiciosEstacion`, con relaciones a `Accounts` y `Servicios`.
- Registra el catálogo inicial con los 12 servicios definidos.
- Crea validaciones de integridad para evitar duplicados por cuenta y servicio.
- Añade campos específicos por tipo de servicio, visibles solo cuando corresponde.
- Registra el comportamiento de instalación y la carga inicial mediante `vtlib`.
- Incluye traducciones al español para `es_es` y español mexicano (`es_mx`).

## Requisitos

- Vtiger CRM 8.4.x
- soporte de eventos `vtiger_eventhandlers`
- respaldo completo antes de importar
- entorno de prueba/staging recomendado
- permisos de escritura en la instalación de Vtiger

## Instalación

1. Hacer backup completo de la base y la instalación.
2. Ir a:
   `Settings > Module Management > Import New Module`
3. Cargar el archivo ZIP:
   `docs/Servicios-1.0.0-INSTALACION-LIMPIA.zip`
4. Ejecutar la importación como módulo nuevo.
5. Confirmar que aparece el módulo `Servicios` dentro de `Sales`.
6. Verificar que se crea `ServiciosEstacion` durante la instalación automática.

## Resultado esperado

- En `Ventas > Servicios` aparece el catálogo maestro.
- El catálogo inicial incluye los siguientes servicios:
  - NOTICES
  - SERVICES CLOUD
  - E-INVOICING
  - IISE
  - PAY FUELCONTROL-PAYPHONE
  - PAY FUELCONTROL-DATAFAST
  - PAY FUELCONTROL-MEDIANET
  - PAY FUELCONTROL-DEUNA
  - TUNNEL-WEBHOOK
  - TUNNEL-API REST
  - ARCH
  - VPN CONNECT

## Validación postinstalación

1. Abrir la ficha de una cuenta.
2. Revisar la sección de servicios relacionados.
3. Crear una instancia de `ServiciosEstacion` y seleccionar un servicio del catálogo.
4. Confirmar que solamente aparecen los campos del servicio elegido.
5. Recrear la misma combinación `Cuenta + Servicio` y comprobar que se rechaza el duplicado.
6. Confirmar que el nombre automático de la instalación se genera como `Cuenta - Servicio`.

## Activar español mexicano en una instalación existente

Este ZIP es de instalación limpia; no volver a importarlo como actualización en una instancia que ya tenga `Servicios` o `ServiciosEstacion`. Copiar solamente `languages/es_mx/Servicios.php` y `languages/es_mx/ServiciosEstacion.php` del ZIP a `languages/es_mx/` en el servidor, conservando permisos de lectura para el servidor web. Después cerrar sesión e iniciar sesión nuevamente con el idioma **Español mexicano**.

## Observaciones

- La implementación fue pensada para un paquete único de instalación limpia.
- No se crea una instancia de servicio para todas las cuentas existentes.
- El catálogo es reutilizable y no se duplica por estación.
- No se almacenan secretos, tokens ni credenciales en texto plano.
