# Bitácora de desarrollo - Sistema de Taller Mecánico

## Fecha de inicio: 17 de agosto de 2026

### 📌 Sesión 1: Configuración inicial del proyecto
- **Fecha**: 17 de agosto de 2026
- **Tarea**: Configuración inicial de Laravel, base de datos, migraciones y seeders.
- **Detalles**:
  - Proyecto creado en `C:\laragon\www\taller` con Laravel 12.
  - Base de datos `taller` configurada (MySQL, usuario root, sin contraseña).
  - Migraciones ejecutadas para tablas: `ubigeos`, `establishments`, `users` (extendida), `roles`, `permissions`, etc.
  - Seeders ejecutados: `UbigeoSeeder`, `RolePermissionSeeder`, `EstablishmentSeeder`, `UserSeeder`.
  - Usuario administrador creado: `admin@taller.com` / `password`.
  - Layout base con Tailwind, Tabulator, ApexCharts y Tom Select configurado por CDN.
  - Autenticación básica con Laravel Breeze (adaptada a CDN).
- **Próximos pasos**: Desarrollar el módulo de Parties y Vehículos con el nuevo modelo de relaciones.

### 📌 Sesión 2 (sustituida): Primer esquema de Clientes/Vehículos
- **Fecha**: 18 de agosto de 2026
- **Detalle**: El primer esquema con `clients` y `vehicle_contacts` fue **reemplazado en la Sesión 3** por el nuevo modelo de "Partes Interesadas" (Parties) con `vehicle_relationships`. Los archivos del esquema anterior se eliminaron.

### 📌 Sesión 3: Módulo de Parties y Vehículos (nuevo modelo de relaciones)
- **Fecha**: 18 de agosto de 2026
- **Tarea**: Desarrollo del módulo de Parties (personas/empresas) y Vehículos con el nuevo modelo de "Partes Interesadas" y "Relaciones de Vehículo", reemplazando al modelo anterior de clients/vehicle_contacts.
- **Detalles**:
  - **Instalación**: `spatie/laravel-activitylog` para auditoría (versión compatible con PHP 8.2).
  - **Migraciones nuevas**: `parties` (type, document_type, document_number, first_name/last_name/business_name, ubigeo, tarifas aseguradora, receive_promotions, FK establishment/ubigeo, created_by/updated_by, SoftDeletes), `vehicle_relationships` (vehicle_id, party_id, role enum owner/driver/approver/operator/billing/insurance_company/emergency_contact/other, is_primary_commercial, notes, SoftDeletes), `party_contacts` (contactos de una party, is_primary, SoftDeletes), `activity_log` (3 migraciones del paquete).
  - **Vehículos actualizados**: sin `client_id`, con `next_technical_review_date`, `technical_review_reminder_days` (default 15), `body_type` nullable.
  - **Modelos**: `Party`, `Vehicle` (actualizado), `VehicleRelationship`, `PartyContact` — todos con SoftDeletes y trait `LogsActivity` para auditoría.
  - **Factories**: `PartyFactory` (person/company/insuranceCompany), `VehicleFactory` (actualizada), `VehicleRelationshipFactory`, `PartyContactFactory`.
  - **Seeders**: `InsuranceCompanySeeder` (Rímac, Pacífico, Mapfre, La Positiva, Protecta, Interseguro con RUC y tarifas), `PartySeeder` (10 parties de ejemplo), `VehicleSeeder` (5 vehículos), `VehicleRelationshipSeeder` (14 relaciones; 5 vehículos con contacto comercial principal = true).
  - **Permisos**: `ver/crear/editar/eliminar parties` y `ver/crear/editar/eliminar vehículos`. Administrador tiene todos; Asesor ver/crear/editar.
  - **Form Requests**: `PartyRequest` (validación condicional persona/empresa), `VehicleRequest` (placa única + validación de `relationships.*`).
  - **Policies**: `PartyPolicy`, `VehiclePolicy` registradas en `AppServiceProvider`.
  - **Services**: `PartyService`, `VehicleService` (con sincronización de `vehicle_relationships` y auditoría created_by/updated_by).
  - **Controladores**: `PartyController` (resource + search AJAX + quickStore + provincias/distritos ubigeo), `VehicleController` (resource + search incluyendo propietario).
  - **Rutas**: `Route::resource('parties')`, `Route::resource('vehicles')`, `api/parties/search`, `api/parties/quick-store`, `api/vehicles/search`, `api/ubigeo/provincias|distritos` — bajo middleware `auth` + `verified`.
  - **Vistas**: `parties/index` (Tabulator), `parties/create|edit` (formulario 2 columnas, toggle persona/empresa, cascada ubigeo, tarifas aseguradora), `parties/show`; `vehicles/index` (con Propietario vía relación), `vehicles/create|edit` (sección de relaciones dinámicas con Tom Select, modal para crear party nueva vía AJAX, checkbox único "contacto comercial principal"), `vehicles/show` (badges de rol y contacto principal).
  - **Navegación**: enlace "Parties" reemplaza a "Clientes".
  - **Pruebas**: `tests/Feature/PartyTest` (6 pruebas), `tests/Feature/VehicleTest` (4 pruebas), `tests/Unit/PartyServiceTest` (3), `tests/Unit/VehicleServiceTest` (3) — **10 pruebas del módulo pasan (27 assertions)**.
- **Decisiones**:
  - Las parties reemplazan completamente a `clients`; los vehículos se relacionan mediante `vehicle_relationships` (muchos roles posibles por vehículo).
  - `is_primary_commercial` solo puede estar marcada en una relación por vehículo (enforced en service y en JS).
  - `display_name` en `Party` retorna razón social (empresa) o nombre completo (persona).
  - Se ejecutó `migrate:fresh --seed` para aplicar el nuevo esquema.
- **Commits**: `54ae111` (eliminación esquema anterior), `4775981` (esquema parties/vehicle_relationships/party_contacts + activitylog), `2490422` (factories y seeders), `5878e73` (requests/policies/services/controllers/rutas), `51ddfb7` (vistas), `413eb65` (pruebas), `e626d9b` (bitácora).
- **Próximos pasos**: Desarrollar el módulo de Inventario vehicular (ingreso, checklist, daños, fotos).

### 📌 Corrección: Vista de detalle de parte (vehículos relacionados)
- **Fecha**: 18 de agosto de 2026
- **Tarea**: Se corrigió `parties/show.blade.php` para mostrar los vehículos relacionados de forma legible y profesional.
- **Detalles**:
  - Tabla con placa (enlace a detalle), marca, modelo, año, color, rol y si es contacto comercial principal.
  - Badges de colores según rol (owner, driver, approver, operator, otros).
  - Se usa `$vehicle->pivot->role` y `$vehicle->pivot->is_primary_commercial`.
  - `PartyController::show` carga `vehicles.vehicleModel.brand`.
  - Manejo de valores nulos.

### 📌 Sesión 9: Gestión de sesiones, perfil de usuario y renovación automática
- **Fecha**: 18 de agosto de 2026
- **Detalles**:
  - `navigation.blade.php` reescrito con dropdown vanilla JS/Tailwind (Mi Perfil, Cerrar sesión).
  - Vista `profile/edit.blade.php` unificada (nombre, email, teléfono, contraseña).
  - `ProfileController::update` valida y actualiza con `Hash::make`.
  - Ruta `GET /api/keep-alive` (auth).
  - Interceptor 419 (recarga) y keep-alive cada 5 min en `app.blade.php`.

### 📌 Sesión 8: Ajustes búsqueda DNI/RUC (ubigeo desde BD) y UI
- **Fecha**: 18 de agosto de 2026
- **Detalles**:
  - `getRuc` usa código `ubigeo` para obtener departamento/provincia/distrito desde la tabla local `ubigeos` (fallback JSON).
  - `autoFillUbigeo` JS: cascada secuencial departamento → provincia → distrito (por `ubigeo_code` exacto). Copiado a `public/js/`.
  - Vistas create/edit: select documento junto al número; tarifas aseguradora en `(USD)`.

### 📌 Sesión 7: Tipo de documento con códigos SUNAT + búsqueda API corregida
- **Fecha**: 18 de agosto de 2026
- **Tarea**: Eliminar el campo `type` de `parties` (BD), usar códigos SUNAT en `document_type` (1=DNI, 6=RUC, 4=CEX, 7=PAS, A=Céd. Diplomática), y corregir la consulta DNI/RUC a la API `dniruc.apisperu.com`.
- **Detalles**:
  - **Migración**: elimina columna `type`; `document_type` pasa de ENUM a `string(10)` (idempotente, convierte datos existentes DNI/RUC/PAS/CEX a 1/6/7/4).
  - **`ReniecSunatService`**: URL corregida a `https://dniruc.apisperu.com/api/v1` con `?token=` (query param). DNI valida 8 dígitos (permite cero inicial); RUC 11 dígitos. DNI devuelve `last_name` (apellidos) primero y `first_name` (nombres). RUC limpia dirección quitando sufijo " depto provincia distrito".
  - **`Party`**: `display_name` = `razón social` si existe, si no `apellidos nombre`; nuevo accessor `document_type_label`.
  - **`PartyService::normalizeData`**: RUC (`document_type === '6'`) limpia `first/last_name`; otros limpian `business_name`.
  - **Vistas `create/edit`**: campo "Tipo" eliminado; select de documento con códigos SUNAT; form toggle persona/empresa según `document_type`; botón "🔍 Buscar" habilitado solo para DNI(1)/RUC(6).
  - **JS `party-helper.js`**: envía `1`/`6`; valida 8/11 dígitos; autocompleta apellidos en `last_name`; botón toggle por tipo de documento.
  - **Seeders/factory/tests**: actualizados a códigos SUNAT sin `type`.
  - **Verificación**: `migrate` OK, `db:seed` OK (9 seeders), tests **41 passed (99 assertions)**.
- **Próximos pasos**: Módulo de Inventario vehicular (ingreso, checklist, daños, fotos).

### 📌 Sesión 6: Consulta DNI/RUC (apisperu.com) y Tipo de Cambio SUNAT
- **Fecha**: 18 de agosto de 2026
- **Tarea**: Integración de consulta de DNI/RUC vía API `apisperu.com` con autocompletado de formularios de Parties, y servicio de tipo de cambio SUNAT.
- **Detalles**:
  - **`ReniecSunatService`**: métodos `getDni($dni)` y `getRuc($ruc)` usando `Http` facade con token de apisperu.com. Devuelve datos normalizados en mayúsculas o `null` en error.
  - **`SunatExchangeService`**: métodos `getTipoCambio($fecha)` y `getTipoCambioMes($year, $month)` usando la API de `apis.net.pe`.
  - **Endpoints**: `POST /api/party/search-by-document` y `GET /api/tipo-cambio` (protegidos por `auth` + `Gate`).
  - **`resources/js/party-helper.js`** (también copiado a `public/js/`): JS vanilla con `fetch`, autocompleta `first_name`/`last_name` (DNI) o `business_name`/`address`/ubigeo (RUC) en mayúsculas.
  - **Vistas `parties/create` y `edit`**: botón "🔍 Buscar" junto a `document_number`, consulta automática al perder el foco (blur) con 8 dígitos (DNI) o 11 (RUC).
  - Los tokens se mantienen en el backend (`env`), nunca expuestos al frontend.
- **Próximos pasos**: Módulo de Inventario vehicular (ingreso, checklist, daños, fotos).

### 📌 Sesión 5: OCR Sunarp mejorado (Tesseract.js v5) en formularios de vehículos
- **Fecha**: 18 de agosto de 2026
- **Tarea**: Implementación completa de OCR para capturas de Sunarp en formularios de vehículos (crear y editar).
- **Detalles**:
  - **Tesseract.js v5** cargado desde CDN en `layouts/app.blade.php` (antes era v4 local solo en create).
  - **Partial compartido** `vehicles/_sunarp_modal.blade.php` reutilizado en `create` y `edit`: previsualización de imagen, `capture="environment"` para cámara móvil, panel de resultados y enlace a `https://sede.sunarp.gob.pe/`.
  - **Endpoints API**: `POST /api/brands/find-or-create` y `POST /api/models/find-or-create` crean marca/modelo automáticamente (mayúsculas) vía `BrandService` y `VehicleModelService`.
  - **Bug corregido**: select de marca en `edit.blade.php` no tenía `name="brand_id"`.
  - **Mutadores en `Vehicle`**: placa, VIN, motor y color en mayúsculas. `VehicleService` normaliza `color`.
  - **Tests**: **41 passed (99 assertions)**.
- **Próximos pasos**: Módulo de Inventario vehicular (ingreso, checklist, daños, fotos).

### 📌 Sesión 4: Refactorización parties/vehículos (brands + models + OCR Sunarp)
- **Fecha**: 18 de agosto de 2026
- **Detalle**:
  - `parties` y `vehicles` **sin `establishment_id`** (los establecimientos se asociarán a presupuestos/inventarios/OTs).
  - Nuevas tablas `brands` y `models`; `vehicles` ahora usa `brand_id` y `model_id`, `technical_review_date`, `review_reminder_days`.
  - Modelos `Brand`, `VehicleModel`; servicios `BrandService::findOrCreateBrand()`, `VehicleModelService::findOrCreateModel()`.
  - `BrandModelSeeder` (17 marcas / 68 modelos), seeders idempotentes, permisos y policies de marcas/modelos.
  - Vistas con marca/modelo en cascada, modal OCR **Sunarp (Tesseract.js)** que autocompleta marca, modelo, año, color, VIN y motor.
  - Se eliminó el campo `Establecimiento` de las vistas `create`/`edit` de vehículos y del `VehicleController`.
  - `db:seed` idempotente verificado; tests: **16 passed**.
- **Commits**: `00b48d5`, `51b4b50`, `83899dc`, `0181a02`.
- **Próximos pasos**: Desarrollar el módulo de Inventario vehicular (ingreso, checklist, daños, fotos).

### 📌 Correcciones y mejoras: Inventario Vehicular (check-in)
- **Fecha**: 19 de agosto de 2026
- **Tarea**: Corregir error `Attempt to read property "soat_expiration" on null` en `check-ins/create` y aplicar mejoras de UX solicitadas.
- **Detalles**:
  - **Bug corregido**: en `_form.blade.php` los campos de fechas usaban `$checkIn->soat_expiration?->format(...)` que fallaba en modo creación (cuando `$checkIn` es null). Corregido a `$checkIn?->soat_expiration?->format(...)` (doble null-safe).
  - **Checklist con botones**: se reemplazó el `<select>` por 4 botones circulares (✓ verde = bueno, ▲ ámbar = regular, ✕ rojo = malo, ● negro = no aplica) con estado activo (fondo de color, símbolo blanco) e inactivo (borde + símbolo del color). Se agregó leyenda explicativa. Layout responsive: 1 columna en móvil (nombre arriba, botones a la derecha, nota debajo) y 3 columnas en PC.
  - **Categoría oculta**: se eliminó la columna "Categoría" del checklist tanto en el formulario como en el detalle (se mantiene en BD `check_in_checklist_items.category` para uso futuro).
  - **Lado del vehículo oculto en daños**: se ocultó el select "Lado" en `_damages.blade.php`, se quitaron las referencias al lado en la lista de daños del formulario, en el JS al hacer clic (se fija `side = 'front'`) y en el detalle (`show`). La columna `side` se mantiene en `check_in_damages` con default `'front'` en `CheckInService::syncDamages` (validación ya no exige side) para uso futuro cuando se soporten imágenes por lado.
  - **Mockups JPG**: los usuarios pueden colocar imágenes `{body_type}.jpg` en `public/images/mockups/` (ej. `sedan.jpg`, `suv.jpg`, `pickup.jpg`, `camioneta.jpg`, `camion.jpg`, `moto.jpg`). El sistema busca `.jpg` → `.jpeg` → `.png` → `.svg` en ese orden.
- **Verificación**: `php artisan view:cache` OK (Blade compila sin errores) y las **15 pruebas del módulo CheckIn siguen pasando (40 assertions)**.
- **Próximos pasos**: Módulo de presupuestos.

### 📌 Módulo de Inventario Vehicular
- **Fecha**: 19 de agosto de 2026
- **Tarea**: Implementación del módulo de inventario (check-in) con checklist, daños (mockup simple), fotos y flujo de estados.
- **Detalles**:
  - **Migraciones y modelos**: `check_ins`, `check_in_checklist_items`, `check_in_checklist_results`, `check_in_damages`, `check_in_photos`.
    - `CheckIn` con relaciones: Vehicle, Party (client_id / insurance_company_id), Establishment, User (created_by/updated_by), hasMany checklistResults/damages/photos. SoftDeletes + LogsActivity.
    - Catálogo de checklist con categorías (EXTERIOR, MOTOR, INTERIOR, HERRAMIENTAS/EMERGENCIA).
  - **Controladores, servicios, políticas**:
    - `CheckInController`: CRUD completo + `approve()`, `reject()`, `sendToClient()`, `search()` (API Tabulator), `contacts()` (relaciones del vehículo), `uploadPhoto()`, `destroyPhoto()`, `insuranceCompanies()`.
    - `CheckInService`: lógica transaccional (sync checklist, daños, contactos como vehicle_relationships), flujo de estados.
    - `CheckInPolicy` con permisos `ver/crear/editar/eliminar/aprobar inventarios` (Administrador: todos; Asesor: ver/crear/editar).
    - `CheckInRequest` con validaciones (vehículo no duplicado en estado abierto, aseguradora debe ser compañía de seguros, checklist/daños dinámicos).
  - **Vistas**: listado con Tabulator (filtros placa/cliente/estado/rango de fechas), formulario con secciones (vehículo/propietario con autocompletado por placa, contactos del vehículo, datos de ingreso, checklist con "ver solo regulares y malos", daños con mockup clickeable y coordenadas %, fotos con subida AJAX), detalle con pestañas (General, Checklist, Daños, Fotos) y botones de estado.
  - **Seeders**: `ChecklistItemsSeeder` lee del CSV real (`C:\Users\Noel\Downloads\checklist_details.csv`) con parser que sanea la línea partida "PALANCA DE GATA" y fallback manual; `CheckInSeeder` crea 3 inventarios de ejemplo.
  - **Mockups**: SVGs placeholder en `public/images/mockups/` (sedan, suv, pickup, camioneta, camion, moto). El sistema busca `{body_type}.jpg` luego `.svg` y si no existe muestra mensaje con dropdowns.
  - **Almacenamiento**: `php artisan storage:link` creado para fotos en `storage/app/public/check-in-photos/`.
  - **Rutas**: `Route::resource('check-ins')` + POST approve/reject/send-to-client + API search/contacts/insurance-companies/photos.
  - **Navegación**: enlace "Inventario" agregado al menú principal.
  - **Pruebas**: `tests/Feature/CheckInTest` (9 pruebas), `tests/Unit/CheckInServiceTest` (6) — **15 pruebas del módulo pasan (40 assertions)**. Suite completa: 55 passed, 1 fail preexistente de `ProfileTest` (Laravel Breeze estándar, sin relación con el módulo).
- **Próximos pasos**: Módulo de presupuestos.

### 📌 Auditoría y estandarización de autocompletados (selección única)
- **Fecha**: 22 de agosto de 2026
- **Tarea**: Auditoría integral de todos los componentes de autocompletado (Tom Select) del frontend y corrección de la confusión visual de "selección múltiple simultánea" en el modal "Buscar / Registrar contacto" y demás dropdowns de búsqueda.
- **Diagnóstico (causa raíz)**:
  1. **CSS**: en `resources/views/layouts/app.blade.php`, los estados `:hover` y `.active` de las opciones del dropdown de Tom Select usaban exactamente el mismo estilo (`bg-blue-50 text-blue-800`). Al pasar el cursor sobre una opción mientras otra estaba activa (cursor/teclado), ambas se veían igual → apariencia de selección múltiple simultánea.
  2. **JS**: ningún Tom Select definía `closeAfterSelect: true` ni `maxItems: 1`. El dropdown permanecía abierto tras seleccionar y el contenedor no forzaba el modo "single" → reforzaba la confusión visual.
- **Archivos modificados**:
  - `resources/views/layouts/app.blade.php`: estados separados — `:hover` = gris claro sutil (`bg-gray-100`), `.active` = azul claro (`bg-blue-50`), `.selected` = azul sólido (`bg-blue-600`) con check SVG y subopciones en texto azul claro.
  - `resources/views/vehicles/_relationships.blade.php`: TomSelect del campo "Contacto" (`#rel-party-id`) ahora con `closeAfterSelect: true` y `maxItems: 1`.
  - `resources/views/check-ins/_form-scripts.blade.php`: TomSelect de vehículo (`#vehicle_id`) y aseguradora (`#insurance_company_id`) ahora con `closeAfterSelect: true` y `maxItems: 1`.
  - `resources/views/check-ins/index.blade.php`: TomSelect de filtro de cliente (`#f-client`) ahora con `closeAfterSelect: true` y `maxItems: 1`.
- **Comportamiento nuevo de los autocompletados**:
  - Al hacer clic en una opción: el menú se cierra automáticamente (`closeAfterSelect`).
  - Solo se puede seleccionar UNA opción a la vez (`maxItems: 1`).
  - La opción seleccionada muestra fondo azul sólido con check; el hover sobre otras opciones es solo un resaltado gris temporal.
  - El CSS garantiza que nunca se marquen múltiples elementos simultáneamente.
- **Verificación**: los 4 componentes Tom Select del proyecto (cliente, vehículo, aseguradora, contacto/party) fueron estandarizados. No se alteró la funcionalidad de los formularios (`rel-party-id` sigue alimentando `party_id`; el filtro de cliente sigue refrescando Tabulator).
- **Corrección adicional (single estricto)**: campo "Contacto" se expandía en dos líneas con cursor parpadeante. CSS: altura fija 2.5rem, item plano, input oculto tras seleccionar. JS: blur()+close() en item_add (4 TomSelect) y clear() en type (Contacto).
- **Corrección adicional (cursor same-line al re-buscar)**: `.ts-control` ahora `flex`; input oculto con `visibility` + reaparece en la misma línea; `dropdown_open` con `setTextValue('')` + cursor al inicio en los 4 TomSelect. Estándar en `.clinerules/03-frontend.md`.
- **Próximos pasos**: continuar con el módulo de presupuestos.

### 📌 Sistema de diseño frontend (rediseño de listados con skill impeccable)
- **Fecha**: 23 de agosto de 2026
- **Tarea**: Rediseño profesional y moderno de los listados de **Usuarios, Vehículos, Contactos e Inventario**, y creación de un **sistema de diseño obligatorio** para todas las vistas futuras, aplicando el skill **impeccable** (modo Operate, dirección Restrained).
- **Diagnóstico (causas del aspecto deficiente)**:
  1. Botones del header (acciones primarias) sin jerarquía clara, con estilos ad-hoc en cada vista.
  2. Tablas Tabulator con tema por defecto: cabeceras sin contraste, filas sin hover suave, celdas apretadas, paginación sin color primario.
  3. Botones de acción en columnas con texto plano (Ver/Editar/Eliminar) en lugar de iconos.
  4. Búsqueda sin icono de lupa y con estilos inconsistentes.
  5. Mensajes flash con estilos dispares.
- **Nuevo sistema de diseño** (definido en `resources/views/layouts/app.blade.php` como Tailwind CDN `@layer components`):
  - **Botones**: `.btn` (base), `.btn-primary`, `.btn-secondary`, `.btn-danger` — `inline-flex items-center gap-2 rounded-lg px-4 py-2 text-sm font-semibold`, transición 150ms `ease-out`, `focus-visible:ring-2`, hover/active, disabled.
  - **Botones de icono en tablas**: `.btn-icon` + `.btn-icon-blue` (ver), `.btn-icon-amber` (editar), `.btn-icon-red` (eliminar) — `h-8 w-8`, fondo `*-50`, color `*-600`, hover `*-100`, siempre con `title`.
  - **Cards**: `.card` (`bg-white rounded-lg border border-gray-200 shadow-sm`), reemplaza `bg-white shadow-sm sm:rounded-lg`.
  - **Búsqueda**: `.search-input` con icono de lupa SVG absoluto a la izquierda (`relative max-w-md`).
  - **Tablas Tabulator**: tema global — cabecera `#f8fafc` uppercase 0.75rem, filas con hover `#eff6ff`, bordes `#f1f5f9`, celdas `padding .625rem .75rem`, placeholder `#94a3b8`, paginación con página activa en `#2563eb`.
  - **Micro-interacciones**: hover en filas y botones (150ms), superficies del navegador themeadas (`::selection` = `#bfdbfe`/`#1e3a8a`).
- **Vistas rediseñadas** (botones con iconos, card, búsqueda con lupa, acciones con `.btn-icon`):
  - `resources/views/users/index.blade.php`
  - `resources/views/vehicles/index.blade.php`
  - `resources/views/parties/index.blade.php`
  - `resources/views/check-ins/index.blade.php`
- **Reglas permanentes**: creado **`.clinerules/07-diseno-sistema.md`** con paleta exacta, tipografía, radios de borde, sombras, patrones de componentes (botones, inputs, tablas), tono/UX (estados de carga, vacíos y errores) y checklist de verificación obligatorio para toda vista nueva o existente.
- **Verificación**: `php artisan view:cache` exitoso (todas las vistas compilan); funcionalidad de negocio y JavaScript sin cambios.
- **Próximos pasos**: aplicar el sistema de diseño a vistas de formulario (create/edit) si el usuario lo solicita.

### � Seguridad y UX en formularios (CSRF refresh + Anti-doble envío)
- **Fecha**: 23 de agosto de 2026
- **Tarea**: Corrección del error de expiración del token CSRF en Login y prevención de duplicidad de datos por doble clic en todos los formularios del proyecto.
- **Detalles**:
  - **Endpoint público** `GET /api/csrf-token` en `routes/web.php`: devuelve un token CSRF fresco (`csrf_token`). Al tocarlo, Laravel renueva la actividad de la sesión (o crea una nueva si expiró), habilitando que cualquier formulario se pueda enviar aunque la página haya estado inactiva mucho tiempo.
  - **Partial global** `resources/views/partials/form-guard.blade.php` (nuevo):
    - **Regla CSRF**: intercepta el `submit` de cualquier formulario (event delegation en `document` con capture), llama a `GET /api/csrf-token` y actualiza el meta `csrf-token` y los inputs ocultos `_token` justo antes de `form.submit()` programático. Cubre también los formularios generados dinámicamente por Tabulator.
    - **Regla Anti-duplicados**: marca `data-submitting="1"` (flag) antes del fetch para bloquear doble clic ráfaga; deshabilita `button[type="submit"]` y muestra spinner + "Guardando..." (personalizable con `data-loading-text`). Respeta los `onsubmit` inline que cancelan (confirm/prompt rechazado) y permite reintentar tras error de validación.
  - **Layouts protegidos**: el partial se incluyó en `layouts/guest.blade.php` (login, register, reset/forgot/confirm password, verify-email) y en `layouts/app.blade.php` (todos los formularios autenticados: parties, vehicles, check-ins, users, profile, acciones de estado y deletes).
  - **Modales AJAX protegidos** con flag booleano `saving` (bloquea reentrada, re-habilita en `finally`): `partials/contact-modal.blade.php` (quick-store/quick-update de parties) y `check-ins/_vehicle_modal.blade.php` (quick-store/quick-update de vehículos). En ambos, el botón se deshabilita y muestra "Guardando..." durante el fetch.
  - **Verificación**: `php artisan view:cache` OK (todas las vistas compilan); las confirmaciones de delete/estado y el prompt de rechazo siguen funcionando (el guard respeta `e.defaultPrevented`).
  - **Reglas del proyecto**: creado `.clinerules/08-seguridad-forms.md` con las dos reglas obligatorias (CSRF refresh antes del envío + Anti-duplicados con flag booleano y botón deshabilitado), incluida la implementación estándar para formularios tradicionales y modales AJAX, para que todo desarrollo futuro las cumpla sin duplicar lógica.
- **Próximos pasos**: continuar con el módulo de presupuestos.

### �📝 Nota sobre la bitácora
A partir de ahora, esta bitácora se actualizará automáticamente por el asistente (DeepSeek) en cada hito importante del desarrollo. Los registros incluirán fecha, tarea realizada, decisiones tomadas y próximos pasos.

---
