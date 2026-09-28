# Modulos 2.0.0: instalacion en produccion

> Esta versión fue reemplazada por [Modulos 2.1.0](Modulos-2.1.0-INSTALACION.md). Para instalar campos específicos y la regla obligatoria de PUMP, use únicamente `Modulos-2.1.0.zip`.

Este paquete actualiza el modulo `Modulos` existente y crea `ModuloEstacion`. Está pensado para importar como **actualización** en una instalación que ya tiene `Modulos`; no instalarlo como un módulo nuevo.

## Incluido

- `Modulos` queda como catálogo, con campos de código, categoría, descripción, estado de catálogo y requisito de configuración.
- `ModuloEstacion` guarda la cuenta, el elemento de catálogo, estado, fechas, responsable, pruebas, versión/modelo, configuración, novedades y observaciones.
- El lookup de Cuenta crea la lista relacionada **Módulos** dentro de Accounts. El catálogo deja de aparecer como related list en Accounts.
- El menú principal no recibe `ModuloEstacion`; los registros siguen disponibles como entidad vtlib para lookups, permisos y Reports.
- Un evento backend rechaza asociaciones duplicadas activas Cuenta + Módulo y genera el nombre de la instancia.

## Requisitos y respaldo

- Vtiger CRM 8.4.x con soporte de eventos (`vtiger_eventhandlers`).
- Hacer backup completo de base de datos y archivos antes de importar.
- Probar primero el ZIP en una copia de staging de la misma versión y con datos representativos.

## Instalacion

1. Subir `Modulos-2.0.0.zip` en **Settings > Module Management > Import New Module**.
2. El importador debe reconocer `Modulos` como existente y ofrecer la acción de actualización. Seleccionar **Update**.
3. Esperar la confirmación; el evento `module.postupdate` crea los campos y `ModuloEstacion` de forma idempotente.
4. Revisar **Accounts > una cuenta > Módulos**. Añadir dos instancias en cuentas distintas, usando el mismo registro del catálogo.
5. Intentar añadir el mismo módulo a la misma cuenta otra vez; el guardado debe rechazarse con `Este módulo ya está asociado a esta estación.`
6. Revisar **Modulos** y confirmar que contiene una sola fila por tipo de catálogo. Revisar el módulo en Reports.

## Importante sobre datos existentes

La actualización oculta el campo heredado `estado` del catálogo, pero conserva sus valores y no transforma registros antiguos en instancias. Antes de producción, revisar si las filas actuales de `Modulos` son tipos de catálogo o instalaciones por cuenta: el paquete no puede deducir ni migrar esa semántica automáticamente.

No se agrega un índice SQL único sobre Cuenta + Módulo. Vtiger elimina registros lógicamente en `vtiger_crmentity`, mientras que los campos de relación viven en la tabla del módulo; un índice único simple bloquearía crear de nuevo una asociación tras borrar lógicamente la anterior. La protección activa está en el backend en `vtiger.entity.beforesave`.