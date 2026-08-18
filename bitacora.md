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

### 📝 Nota sobre la bitácora
A partir de ahora, esta bitácora se actualizará automáticamente por el asistente (DeepSeek) en cada hito importante del desarrollo. Los registros incluirán fecha, tarea realizada, decisiones tomadas y próximos pasos.

---