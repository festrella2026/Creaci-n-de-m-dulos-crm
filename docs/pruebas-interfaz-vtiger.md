# Pruebas funcionales de interfaz - Vtiger CRM

## 1. Objetivo y alcance

Este documento define pruebas manuales para comprobar desde la interfaz que los módulos principales de Vtiger permiten consultar, crear, editar, relacionar y reportar información correctamente. Incluye flujos comunes, módulos estándar representativos, los módulos personalizados de Modulos y Servicios, y el ciclo de creación y exportación de informes.

La cobertura estándar prioriza Accounts (Cuentas), Contacts (Contactos), Leads, Potentials (Oportunidades), Products (Productos), Services (Servicios estándar), Quotes (Cotizaciones), SalesOrder (Órdenes de venta), Invoice (Facturas), Calendar/Events (Calendario/Actividades) y HelpDesk (Casos). Los nombres visibles pueden variar según idioma, versión y configuración.

Quedan fuera de alcance la importación/instalación de paquetes y las comprobaciones por SQL, CLI, logs o código. La programación y el uso compartido de informes son opcionales, según permisos y configuración.

## 2. Registro de ejecución

| Instancia/ambiente | Versión | Idioma | Perfil/usuario | Fecha | Ejecutor |
| --- | --- | --- | --- | --- | --- |
|  |  |  |  |  |  |

Registrar cada caso como **Aprobado**, **Fallido**, **Bloqueado** o **No aplica**. Para un fallo, anotar el dato utilizado, los pasos para reproducirlo, el resultado obtenido y una captura o referencia de evidencia. No incluir contraseñas, tokens ni datos personales reales en las evidencias.

## 3. Precondiciones y datos de prueba

- Ejecutar las pruebas en staging o en un ambiente autorizado que no contenga datos productivos. No borrar ni modificar registros reales.
- Confirmar que el usuario de prueba puede acceder a los módulos y acciones que se van a probar. Registrar cualquier limitación de perfil como **Bloqueado**, no como aprobado.
- Confirmar el idioma y los nombres de menú disponibles. En este documento se usan nombres técnicos y, cuando corresponde, su equivalente habitual en español.
- Preparar datos identificables con un prefijo único, por ejemplo `QA-UI-AAAAMMDD`: una cuenta, un contacto, un lead, un producto/servicio y una oportunidad de prueba.
- Usar únicamente valores ficticios. Para probar campos que representen información sensible, usar marcadores sin valor secreto.
- Preparar una cuenta de prueba distinta para validar que las asociaciones Cuenta + catálogo pueden repetirse en cuentas diferentes.
- Anotar los registros creados para poder eliminarlos al finalizar, siempre que el perfil permita hacerlo y que no se trate de un registro protegido.

### Formato para registrar cada caso

| ID | Estado | Evidencia / observaciones |
| --- | --- | --- |
|  |  |  |

## 4. Pruebas comunes de módulos

Ejecutar los casos CRUD de esta sección en los módulos estándar priorizados de la sección 1, siempre que estén habilitados y el perfil tenga permiso. Repetirlos por módulo y anotar el módulo probado en el registro de ejecución.

### UI-001 - Acceso, lista y navegación

**Pasos**
1. Abrir el módulo desde el menú disponible.
2. Revisar que se muestre la vista de lista y que existan acciones habilitadas para el perfil.
3. Abrir un registro de prueba desde la lista y volver a ella.

**Resultado esperado:** el módulo abre sin error, presenta registros y permite navegar entre lista y detalle conforme a los permisos del usuario.

### UI-002 - Búsqueda y filtros

**Pasos**
1. Buscar un registro de prueba por su nombre o identificador único.
2. Aplicar un filtro disponible que coincida con sus datos.
3. Cambiar el filtro o limpiar la búsqueda.

**Resultado esperado:** se encuentra el registro esperado, el filtro reduce los resultados correctamente y al limpiar la búsqueda se recupera la lista correspondiente al alcance del usuario.

### UI-003 - Crear registro válido

**Pasos**
1. Desde la lista, iniciar la creación de un registro.
2. Completar los campos obligatorios con datos ficticios únicos y guardar.
3. Volver a la lista y abrir el registro creado.

**Resultado esperado:** el registro se guarda sin error, aparece en la lista y muestra los valores ingresados en el detalle.

### UI-004 - Validación de campos obligatorios

**Pasos**
1. Iniciar un registro nuevo.
2. Dejar vacío un campo obligatorio y guardar.
3. Completar el campo faltante y guardar de nuevo.

**Resultado esperado:** la interfaz identifica el campo requerido, no crea un registro incompleto y permite guardar al corregirlo.

### UI-005 - Editar y conservar cambios

**Pasos**
1. Abrir un registro de prueba y seleccionar Editar.
2. Cambiar un campo de texto y, si aplica, un campo de selección.
3. Guardar, volver al detalle y actualizar la vista.

**Resultado esperado:** los cambios se conservan y aparecen en el detalle y en las columnas de lista correspondientes; los demás valores permanecen sin cambios.

### UI-006 - Cancelar sin guardar

**Pasos**
1. Iniciar la creación o edición de un registro de prueba.
2. Cambiar uno o más campos y cancelar o salir sin guardar.
3. Buscar el registro o reabrirlo.

**Resultado esperado:** no se crea el registro cancelado ni se aplican cambios no guardados.

### UI-007 - Relación entre registros

**Pasos**
1. Abrir la cuenta de prueba y revisar su lista relacionada de contactos.
2. Asociar o crear el contacto de prueba desde la cuenta, según las opciones disponibles.
3. Abrir el contacto y comprobar su relación con la cuenta.

**Resultado esperado:** la relación aparece en ambos registros y los enlaces llevan al registro correcto.

### UI-008 - Eliminación controlada (si está permitida)

**Pasos**
1. Elegir únicamente un registro creado para esta ejecución.
2. Eliminarlo desde la interfaz y confirmar la acción.
3. Buscarlo en la lista y revisar el resultado.

**Resultado esperado:** el registro de prueba deja de aparecer en las vistas normales o queda en el estado de eliminación previsto por la configuración. Si la acción no está permitida, confirmar que la interfaz lo impide sin modificar el registro.

### UI-009 - Campos propios del flujo de cada módulo

**Pasos**
1. Crear o editar un registro en el módulo probado.
2. Completar sus campos específicos y referencias disponibles. En Quotes, SalesOrder e Invoice, seleccionar la cuenta/contacto y agregar un producto o servicio cuando el formulario lo permita.
3. Guardar y revisar el detalle y las relaciones.

**Resultado esperado:** los campos específicos aceptan los valores permitidos; las referencias apuntan a los registros elegidos. En documentos de ventas, las líneas e importes visibles corresponden a los productos, cantidades y valores ingresados, de acuerdo con la configuración.

## 5. Pruebas de catálogo y módulos personalizados

Los cuatro módulos personalizados son `Modulos`, `ModuloEstacion`, `Servicios` y `ServiciosEstacion`. En la interfaz pueden aparecer bajo el menú Ventas u otro menú configurado.

### MOD-001 - Catálogo Modulos

**Pasos**
1. Abrir la lista de `Modulos` y localizar los registros iniciales.
2. Comprobar los valores de catálogo disponibles según el ambiente. En una instalación limpia de Modulos 3.0 se esperan 11 tipos, incluido PUMP.
3. Editar un registro de prueba o uno autorizado, cambiar su nombre o código y guardar.
4. Si se permite crear registros, intentar guardar un código ya utilizado y luego uno único.

**Resultado esperado:** el catálogo muestra sus valores; nombre y código se conservan al guardar; se impide un código duplicado si la configuración aplica unicidad. PUMP aparece marcado como obligatorio cuando esa columna está habilitada.

### MOD-002 - PUMP automático y protegido

**Pasos**
1. Crear una cuenta de prueba desde la interfaz.
2. Abrir la cuenta y revisar la lista relacionada de `ModuloEstacion`.
3. Localizar el registro PUMP y revisar su estado.
4. Intentar eliminarlo o quitar su relación desde la interfaz, si se ofrece esa acción.

**Resultado esperado:** la cuenta nueva recibe una instancia PUMP con estado `No instalado`; el sistema impide eliminarla o desvincularla. No probar este caso sobre cuentas reales.

### MOD-003 - Crear ModuloEstacion y validar duplicados

**Pasos**
1. Desde una cuenta de prueba, crear una asociación de módulo distinta de PUMP y seleccionar un valor del catálogo.
2. Guardar y abrir el registro relacionado.
3. Comprobar que `Identificador de instalación` no se solicita durante el alta y que el registro guardado muestra el nombre automático con formato `Cuenta - Módulo`.
4. Intentar crear de nuevo la misma combinación Cuenta + Módulo.
5. Crear esa combinación para una segunda cuenta de prueba.

**Resultado esperado:** se crea la primera asociación; la combinación repetida para la misma cuenta se rechaza con un mensaje visible y comprensible; la misma opción puede asociarse a otra cuenta. El identificador automático corresponde a la cuenta y al módulo seleccionados.

### MOD-004 - Estado y campos condicionales de ModuloEstacion

**Pasos**
1. Crear o editar una asociación y revisar las opciones de Estado de instalación.
2. Seleccionar `PINPAD` y revisar los campos específicos que aparecen.
3. Seleccionar otro módulo con campos configurados, guardar y volver a editar.
4. Cambiar el módulo seleccionado y revisar nuevamente los campos visibles.

**Resultado esperado:** el estado solo permite `Instalado` y `No instalado`; se muestran los campos del módulo elegido y se ocultan los campos específicos de otros módulos. Si el formulario incluye cantidad de equipos para PINPAD, valida el rango configurado de 2 a 10.

### SER-001 - Catálogo Servicios

**Pasos**
1. Abrir la lista de `Servicios` y localizar los servicios iniciales.
2. En una instalación limpia de Servicios 1.0, contrastar el catálogo con los 12 servicios documentados en `docs/Servicios-1.0.0-INSTALACION.md`.
3. Editar un registro autorizado y guardar un cambio de nombre o código.
4. Si se permite crear registros, intentar guardar un código duplicado y luego uno único.

**Resultado esperado:** los nombres y códigos se guardan correctamente; la validación de código único rechaza duplicados cuando está habilitada. Si el catálogo fue adaptado, registrar las diferencias respecto al inventario inicial en vez de asumir que está intacto.

### SER-002 - Crear ServiciosEstacion y validar duplicados

**Pasos**
1. Desde una cuenta de prueba, crear una asociación en `ServiciosEstacion` y elegir un servicio del catálogo.
2. Guardar y abrir el registro relacionado.
3. Comprobar que `Identificador de instalación` no aparece durante el alta y que luego se muestra con formato `Cuenta - Servicio`.
4. Intentar crear de nuevo la misma combinación Cuenta + Servicio.
5. Repetir la asociación del servicio para otra cuenta de prueba.

**Resultado esperado:** se crea la primera asociación; la combinación duplicada para una misma cuenta se rechaza mediante un mensaje visible; el servicio puede asociarse a una cuenta diferente; el identificador automático coincide con los registros elegidos.

### SER-003 - Estado y campos condicionales de ServiciosEstacion

**Pasos**
1. Crear o editar una asociación y comprobar las opciones de Estado de instalación.
2. Seleccionar `NOTICES` y observar los campos mostrados.
3. Seleccionar `PAY FUELCONTROL-DATAFAST` y observar sus campos específicos.
4. Cambiar el servicio o limpiar la selección, si la interfaz lo permite.

**Resultado esperado:** el estado solo permite `Instalado` y `No instalado`; cada selección muestra sus campos específicos y oculta los que no corresponden, incluso al cambiar o limpiar el servicio. Los campos que representan secretos se prueban solo con valores ficticios y no deben contener credenciales reales.

### REL-001 - Listas relacionadas de la cuenta

**Pasos**
1. Abrir la cuenta de prueba y revisar las listas relacionadas de `ModuloEstacion` y `ServiciosEstacion`.
2. Crear una asociación desde cada lista relacionada, si la acción está disponible.
3. Abrir cada asociación y volver a la cuenta.

**Resultado esperado:** las asociaciones quedan bajo la cuenta seleccionada y pueden consultarse desde la cuenta y desde su propio detalle. Una relación protegida como PUMP no puede desvincularse.

## 6. Pruebas de informes (Reports)

Usar datos de prueba conocidos para poder contrastar los resultados. Los informes pueden estar sujetos a permisos sobre Reports y sobre cada módulo incluido.

### REP-001 - Crear y ejecutar un informe

**Pasos**
1. Abrir `Reports` y comenzar un informe nuevo.
2. Seleccionar un módulo principal accesible, por ejemplo Accounts, y un módulo relacionado, por ejemplo Contacts, si la relación está disponible.
3. Definir un nombre único con prefijo de prueba, elegir columnas identificables y guardar el informe.
4. Ejecutarlo y contrastar las filas con los registros de prueba que cumplen los criterios.

**Resultado esperado:** el informe se guarda y ejecuta; muestra las columnas seleccionadas y filas correspondientes al módulo principal y a la relación elegida. No se incluyen registros fuera del acceso del usuario.

### REP-002 - Filtros y condiciones

**Pasos**
1. Editar el informe de prueba y añadir un filtro por un campo conocido, por ejemplo nombre, estado o cuenta relacionada.
2. Agregar una segunda condición si está disponible y guardar.
3. Ejecutar el informe antes y después de cambiar el valor del filtro.

**Resultado esperado:** los filtros reducen o amplían las filas conforme a las condiciones configuradas; no alteran los registros de origen.

### REP-003 - Editar y volver a ejecutar un informe

**Pasos**
1. Abrir la configuración de un informe creado para la prueba.
2. Cambiar el nombre, una columna o una condición y guardar.
3. Ejecutar de nuevo el informe.

**Resultado esperado:** los cambios persisten y se reflejan en el resultado sin crear una copia inesperada ni modificar los registros fuente.

### REP-004 - Exportar resultados

**Pasos**
1. Ejecutar el informe de prueba y usar las opciones disponibles para exportar CSV y XLS; probar también la vista imprimible si aparece.
2. Abrir los archivos exportados en una aplicación compatible.
3. Comparar encabezados, cantidad de filas y valores con el resultado mostrado en pantalla.

**Resultado esperado:** cada formato solicitado se genera y contiene los campos y filas del informe; la vista imprimible muestra el nombre y los resultados del informe. Registrar como No aplica cualquier formato no habilitado en la instancia.

### REP-005 - Acceso y visibilidad del informe (opcional)

**Pasos**
1. Si el perfil permite compartir informes, configurar acceso para un usuario o grupo de prueba.
2. Iniciar sesión con ese perfil y localizar el informe.
3. Si existe la programación de informes, configurar un envío de prueba solo a una dirección autorizada y verificar la programación visible.

**Resultado esperado:** únicamente los usuarios/grupos autorizados acceden al informe y cualquier programación queda configurada según las opciones seleccionadas. Omitir este caso cuando la función o el perfil no estén habilitados.

## 7. Criterios de salida

- Todos los casos aplicables tienen estado registrado y evidencia suficiente para repetirlos.
- No quedan fallos abiertos en creación, edición, relaciones, reglas de integridad ni resultados de informes; cualquier excepción debe quedar documentada y aceptada por el responsable.
- Los casos bloqueados o no aplicables identifican la causa, como permiso insuficiente, módulo no habilitado o función no configurada.
- Se eliminan únicamente los datos creados para la ejecución y no se intenta eliminar el PUMP protegido.
- No se adjuntan credenciales, secretos, tokens ni datos personales reales a las evidencias.

## 8. Referencias

- [Servicios 1.0.0 - Procedimiento de verificación](Servicios-1.0.0-VERIFICACION.md)
- [Modulos 3.0.0 - Instalación limpia](Modulos-3.0.0-INSTALACION-LIMPIA.md)
- [Servicios 1.0.0 - Instalación limpia](Servicios-1.0.0-INSTALACION.md)