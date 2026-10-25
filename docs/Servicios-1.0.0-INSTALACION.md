# Servicios 1.0.0 - Instalación limpia

Este paquete está diseñado para una instancia Vtiger CRM 8.4.x donde aún no existen los módulos personalizados `Servicios` ni `ServiciosEstacion`.

No se recomienda utilizarlo como actualización en una instancia que ya tenga estos módulos.

## Qué instala

- Crea el módulo catálogo `Servicios` y sus tablas/campos base.
- Crea el módulo `ServiciosEstacion`, con relaciones a `Accounts` y `Servicios`.
- Registra el catálogo inicial con los 12 servicios definidos.
- Crea validaciones de integridad para evitar duplicados por cuenta y servicio.
- Añade campos específicos por tipo de servicio, visibles solo cuando corresponde.
- Excluye de `ServiciosEstacion` los campos generales Cantidad de equipos, Versión de equipos, Estado de certificación y Fecha de certificación; se conservan los campos específicos de cada servicio.
- El estado de cada servicio por estación queda limitado a `Instalado` / `No instalado`; los valores anteriores se normalizan.
- Los campos largos de PAY FUELCONTROL y VPN usan nombres físicos cortos y únicos compatibles con el límite de `vtiger_field.columnname`; el instalador migra las columnas existentes conservando sus valores.
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
6. Confirmar que **Identificador de instalación** no aparece en el formulario de alta y que, al abrir el registro guardado, se muestra como `Cuenta - Servicio`.
7. Confirmar que el campo **Estado de instalación** solo ofrece `Instalado` y `No instalado`.

## Agregar un servicio con campos específicos

`Servicios` es el catálogo de tipos de servicio; las instalaciones asociadas a cuentas se guardan en `ServiciosEstacion`. Para agregar un servicio como `MAQUINARIA`:

1. Crear el registro del servicio en **Ventas > Servicios**.
2. Ir a **Configuración > Administración de módulos > Editor de diseño**, elegir `ServiciosEstacion` y agregar allí los campos personalizados. Los campos se guardan en el registro de instalación, no en el registro del catálogo.
3. Si los campos deben mostrarse solo al elegir `MAQUINARIA`, solicitar una actualización de código para incluir sus nombres de campo en el mapa de visibilidad de `layouts/v7/modules/ServiciosEstacion/resources/Edit.js`. El Editor de diseño permite crear y ordenar campos, pero no configura por sí solo esta condición basada en el servicio relacionado.
4. Agregar validaciones en el backend si el nuevo servicio tiene reglas de negocio, y probar creación, edición, cambio y limpieza del servicio.

Un usuario con permisos puede crear el servicio en el catálogo y agregar campos desde el Editor de diseño, pero no puede vincularlos condicionalmente al servicio solo con la interfaz actual. Si se crea un módulo independiente de Vtiger, sus campos sí se configuran directamente en el Editor de diseño de ese módulo y no necesitan este mapa.

## Activar español mexicano en una instalación existente

Este ZIP es de instalación limpia; no volver a importarlo como actualización en una instancia que ya tenga `Servicios` o `ServiciosEstacion`. Copiar solamente `languages/es_mx/Servicios.php` y `languages/es_mx/ServiciosEstacion.php` del ZIP a `languages/es_mx/` en el servidor, conservando permisos de lectura para el servidor web. Después cerrar sesión e iniciar sesión nuevamente con el idioma **Español mexicano**.

## Ocultar campos generales en una instancia existente

Para quitar del formulario `Cantidad de equipos`, `Versión de equipos`, `Estado de certificación` y `Fecha de certificación` sin borrar sus datos históricos, ejecutar una vez en la base de datos:

```sql
UPDATE vtiger_field AS field
INNER JOIN vtiger_tab AS tab ON tab.tabid = field.tabid
SET field.presence = 1
WHERE tab.name = 'ServiciosEstacion'
   AND field.fieldname IN ('cantidad_equipos', 'version_equipos', 'estado_certificacion', 'fecha_certificacion');
```

Después cerrar sesión y volver a entrar. Los campos de certificación específicos de IISE, Datafast y Medianet no se modifican.

## Observaciones

- La implementación fue pensada para un paquete único de instalación limpia.
- No se crea una instancia de servicio para todas las cuentas existentes.
- El catálogo es reutilizable y no se duplica por estación.
- No se almacenan secretos, tokens ni credenciales en texto plano.

## Trabajo realizado en esta sesión

- Se prepararon las traducciones `es_mx` para `Servicios`, `ServiciosEstacion`, `Modulos` y `ModuloEstacion` en `languages/es_mx/`.
- Se corrigió la visibilidad condicional de los campos específicos de `ModuloEstacion`: responde al seleccionar o limpiar el módulo y oculta cada campo sin afectar a otro campo de la misma fila. El cambio también se incorporó al ZIP limpio de Modulos 3.0.
- Se corrigió la visibilidad condicional de `ServiciosEstacion`: responde a los eventos de selección y limpieza de la referencia Servicio, y oculta cada campo específico sin ocultar otros campos de la misma fila. Se incorporó al ZIP limpio de Servicios 1.0.
- Los estados de instalación de Servicios y Modulos se limitaron a `Instalado` y `No instalado`. Los instaladores normalizan valores heredados y eliminan opciones antiguas del picklist. Se actualizaron los paquetes Servicios 1.0 y Modulos 2.1/3.0.
- En `ServiciosEstacion` se dejaron de crear los campos generales Cantidad de equipos, Versión de equipos, Estado de certificación y Fecha de certificación. En instalaciones existentes se ocultan conservando los datos; los campos específicos de certificación por servicio permanecen.
- El guardado de `ServiciosEstacion` ahora rechaza una Cuenta o un Servicio inexistente con un mensaje explícito, en vez de guardar una referencia inválida. El handler se actualizó también en el ZIP limpio de Servicios.
- Se validó la sintaxis PHP de los instaladores y handlers modificados, la validez del manifiesto y el contenido de los ZIP actualizados. No se confirmó mediante consulta el estado de los metadatos en la base activa, ni se realizó una prueba de interfaz dentro de Vtiger.
- Los ZIP de Servicios y Modulos son de instalación limpia salvo Modulos 2.1, que es el paquete de actualización. No importar paquetes limpios como actualización sobre módulos existentes; aplicar en esas instancias los pasos de migración documentados arriba.
