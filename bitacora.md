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

### 📝 Nota sobre la bitácora
A partir de ahora, esta bitácora se actualizará automáticamente por el asistente (DeepSeek) en cada hito importante del desarrollo. Los registros incluirán fecha, tarea realizada, decisiones tomadas y próximos pasos.

---