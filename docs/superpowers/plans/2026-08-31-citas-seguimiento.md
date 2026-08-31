# Módulo Citas y Seguimiento — Plan de Implementación

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) o superpowers:executing-plans para implementar este plan tarea por tarea. Pasos con checkbox (`- [ ]`).

**Goal:** Agendar citas de ingreso, asociarlas automáticamente al check-in del mismo día (con indicadores), y registrar seguimientos con próxima acción.

**Architecture:** Módulo Laravel estándar del proyecto (migraciones → modelos → services → Form Requests → policies → controllers → rutas → vistas). La asociación se dispara desde `CheckInService::create` vía `AppointmentService::associateForCheckIn()` (regla de mismo día calendario, enforced en servidor). Indicadores vía endpoint `api/appointments/vehicle-info/{vehicle}` consumido por JS del formulario de check-in.

**Tech Stack:** Laravel 12, MySQL, spatie/laravel-permission, spatie/laravel-activitylog, Tailwind (CDN), Tabulator, Tom Select.

**Spec:** `docs/superpowers/specs/2026-08-31-citas-seguimiento-design.md`

## Constraints

- SoftDeletes + LogsActivity en ambos modelos; `created_by`/`updated_by` (patrón `CheckIn`).
- Permisos spatie: `ver|crear|editar|eliminar citas`, `ver|crear|editar|eliminar seguimientos`. Admin = `Permission::all()`. Asesor = ver/crear/editar (sin eliminar).
- `appointments.check_in_id` unique nullable. Estados: `scheduled|confirmed|cancelled|completed` con labels `Agendada|Confirmada|Cancelada|Realizada`.
- Regla de asociación: solo en **create** del check-in, `status IN (scheduled,confirmed)`, `check_in_id IS NULL`, `DATE(scheduled_at)=DATE(now())`.
- Frontend: reglas 07/11/12 (app shell, badges, Tabulator con `fitColumns`+`responsiveLayout:'collapse'`, `.search-input`, flash `green-50`, Tom Select selección única estricta con handlers `item_add`/`dropdown_open`).
- `@json()` solo con variables, nunca expresiones con comas.
- Formularios con `@csrf`, `data-confirm` para destructivos (el guard global cubre CSRF/anti-doble envío).
- Rutas dentro del grupo `['auth','verified']` de `routes/web.php`.

---

## Task 1: Migraciones `appointments` y `follow_ups`

**Files:**
- Create: `database/migrations/2026_08_31_000001_create_appointments_table.php`
- Create: `database/migrations/2026_08_31_000002_create_follow_ups_table.php`

- [ ] **Step 1:** Escribir la migración de `appointments` (columnas del spec §Modelo de datos; `check_in_id` FK unique nullable, `scheduled_at` datetime, `status` default `scheduled`, soft deletes).
- [ ] **Step 2:** Escribir la migración de `follow_ups` (`date` date, `type` default `call`, `done` boolean default false, `done_at` nullable, soft deletes).
- [ ] **Step 3:** Ejecutar `php artisan migrate` y verificar `php artisan migrate:status` (ambas aplicadas).
- [ ] **Step 4:** Commit `feat(citas): migraciones appointments y follow_ups`.

## Task 2: Modelos `Appointment` y `FollowUp`

**Files:**
- Create: `app/Models/Appointment.php`
- Create: `app/Models/FollowUp.php`

- [ ] **Step 1:** `Appointment` — fillable completo (spec), casts (`scheduled_at` datetime), `STATUS_LABELS` + `STATUS_BADGES`, `getStatusLabelAttribute`, `getServiceTypeLabelAttribute`, `getScheduledAtDisplayAttribute` (d/m/Y H:i), relaciones: `vehicle` (withTrashed), `party`, `advisor` (User), `checkIn` (withTrashed), `creator`, `updater`, `establishment`; LogsActivity con `logOnly` de columnas clave.
- [ ] **Step 2:** `FollowUp` — fillable, casts (`date`/`next_action_date` date, `done` boolean, `done_at` datetime), `TYPE_LABELS`, `getTypeLabelAttribute`, relaciones `party`, `vehicle`, `creator`, `updater`; LogsActivity.
- [ ] **Step 3:** `php -l` sobre ambos y commit `feat(citas): modelos Appointment y FollowUp`.

## Task 3: Form Requests `AppointmentRequest` y `FollowUpRequest`

**Files:**
- Create: `app/Http/Requests/AppointmentRequest.php`
- Create: `app/Http/Requests/FollowUpRequest.php`

- [ ] **Step 1:** `AppointmentRequest` — `authorize(): true`; reglas: `vehicle_id` nullable exists:vehicles; `party_id` nullable exists:parties; `advisor_id` nullable exists:users; `scheduled_date` required date; `scheduled_time` required `date_format:H:i`; `contact_name` nullable max:255; `contact_phone` nullable max:50; `contact_email` nullable email; `service_type` nullable Rule::in(CheckIn::SERVICE_TYPES); `reason` nullable string; mensajes en español.
- [ ] **Step 2:** `FollowUpRequest` — `party_id`/`vehicle_id` nullable exists con `required_without_all:party_id,vehicle_id`; `date` required date; `type` required Rule::in(call,whatsapp,email,visit); `notes` nullable; `next_action_date` nullable date; `done` nullable boolean.
- [ ] **Step 3:** `php -l` y commit.

## Task 4: Servicios `AppointmentService` y `FollowUpService`

**Files:**
- Create: `app/Services/AppointmentService.php`
- Create: `app/Services/FollowUpService.php`

- [ ] **Step 1:** `AppointmentService`:
  - `create(array $data)` / `update(Appointment $a, array $data)` — `scheduled_at` = `{$scheduled_date} {$scheduled_time}:00`; `establishment_id` default `Auth::user()?->establishment_id`; `created_by/updated_by` = Auth::id().
  - `cancel(Appointment $a)` — `status=cancelled` (solo si no está `completed`); `confirm(Appointment $a)` — `status=confirmed` (solo si `scheduled`).
  - `unlink(Appointment $a)` — `check_in_id=null`, `status=confirmed`.
  - `associateForCheckIn(CheckIn $checkIn): ?Appointment` — regla del spec (solo en create; busca `whereDate('scheduled_at', today)`; setea `check_in_id` + `status=completed`; devuelve la cita o null). Transaccional.
  - `vehicleInfo(Vehicle $vehicle): array` — `['today' => ?cita, 'others' => [citas futuras]]` con `scheduled`/`confirmed` y `check_in_id null`, ordenadas por `scheduled_at`.
- [ ] **Step 2:** `FollowUpService` — `create`, `update`, `delete`, `markDone(FollowUp $f)` (done=true + done_at=now()).
- [ ] **Step 3:** `php -l` y commit `feat(citas): servicios de citas y seguimientos`.

## Task 5: Policies + permisos (seeder)

**Files:**
- Create: `app/Policies/AppointmentPolicy.php`
- Create: `app/Policies/FollowUpPolicy.php`
- Modify: `database/seeders/RolePermissionSeeder.php`

- [ ] **Step 1:** Policies con `viewAny/view/create/update/delete/restore/forceDelete` mapeando a `ver citas`/`crear citas`/`editar citas`/`eliminar citas` (y análogo para seguimientos). Patrón `CheckInPolicy`.
- [ ] **Step 2:** Seeder — agregar los 8 permisos al array `$permissions`; a `$asesor` agregar `ver citas, crear citas, editar citas, ver seguimientos, crear seguimientos, editar seguimientos`.
- [ ] **Step 3:** `php artisan db:seed --class=RolePermissionSeeder` y verificar permisos creados.
- [ ] **Step 4:** Commit.

## Task 6: Controladores + rutas

**Files:**
- Create: `app/Http/Controllers/AppointmentController.php`
- Create: `app/Http/Controllers/FollowUpController.php`
- Modify: `routes/web.php` (imports + rutas en el grupo auth+verified)

- [ ] **Step 1:** `AppointmentController` — resource estándar (index/create/store/show/edit/update/destroy) con `Gate::authorize(...)` y redirects con flash `success`; `confirm`, `cancel`, `unlink` (POST); `vehicleInfo(Vehicle $vehicle)` JSON con `Gate::authorize('viewAny', Appointment::class)`.
- [ ] **Step 2:** `FollowUpController` — resource + `markDone` (POST).
- [ ] **Step 3:** Rutas: `Route::resource('appointments', ...)`, `Route::resource('follow-ups', ...)`, `GET api/appointments/vehicle-info/{vehicle}`, `POST appointments/{appointment}/confirm|cancel|unlink`, `POST follow-ups/{followUp}/done`.
- [ ] **Step 4:** `php artisan route:list --name=appointment` y `--name=follow` para verificar; `php -l`; commit.

## Task 7: Vistas de citas (index, create, edit, _form)

**Files:**
- Create: `resources/views/appointments/index.blade.php`
- Create: `resources/views/appointments/create.blade.php`
- Create: `resources/views/appointments/edit.blade.php`
- Create: `resources/views/appointments/_form.blade.php`

- [ ] **Step 1:** `_form.blade.php` — secciones: Vehículo (Tom Select `#vehicle_id` + botón "Nueva placa" que abre `check-ins/_vehicle_modal`), Contacto (Tom Select `#party_id` de contactos del vehículo + snapshot `contact_name/contact_phone/contact_email` autocompletados y editables), Asesor (Tom Select de `api/users/data`), Fecha + Hora, Tipo de servicio (`CheckIn::SERVICE_TYPES`), Motivo. Asteriscos rojos en `scheduled_date` y `scheduled_time` (obligatorios). `@csrf`, botón submit con el guard global.
- [ ] **Step 2:** `index.blade.php` — patrón de listado (header, `py-6`, card `p-4 sm:p-5`, `.search-input`, flash), filtros desde/hasta fecha, Tabulator con columnas: Fecha/Hora, Vehículo, Contacto, Teléfono, Asesor, Tipo servicio, Estado (badge), Ingreso (badge con `document_sn` enlazable o "sin cita"), Acciones (⋮ con ver/confirmar/cancelar/editar/eliminar). `@push('scripts')` con Tabulator y `@json` solo con variables precalculadas.
- [ ] **Step 3:** `create.blade.php` / `edit.blade.php` — wrapper con `<x-slot name="header">` y `@include('appointments._form')`.
- [ ] **Step 4:** Verificar render (HTTP 200) y commit.

## Task 8: Vistas de seguimientos (index)

**Files:**
- Create: `resources/views/follow-ups/index.blade.php`

- [ ] **Step 1:** Tabulator con filtros (pendientes/solo próxima acción) + botón "Nuevo seguimiento" que abre modal con Tom Select vehículo/party, fecha, tipo, notas, próxima acción; acción "Marcar hecho" y eliminar con `data-confirm`.
- [ ] **Step 2:** Commit.

## Task 9: Navegación

**Files:**
- Modify: `resources/views/layouts/navigation.blade.php`

- [ ] **Step 1:** Nuevo grupo `appointments` en `$navGroups` (icono calendario) con items `Citas` (`appointments.index`, can `ver citas`) y `Seguimiento` (`follow-ups.index`, can `ver seguimientos`).
- [ ] **Step 2:** Commit.

## Task 10: Integración check-in (servicio + controlador + banner JS)

**Files:**
- Modify: `app/Services/CheckInService.php`
- Modify: `app/Http/Controllers/CheckInController.php`
- Modify: `resources/views/check-ins/_form-scripts.blade.php` (o vista create)

- [ ] **Step 1:** `CheckInService` — inyectar `AppointmentService` en constructor; en `create()`, tras la transacción, llamar `associateForCheckIn($checkIn)` y exponer el resultado (ej. `$checkIn->appointment_associated`) para que el controlador construya el flash.
- [ ] **Step 2:** `CheckInController::store` — flash dinámico: "Inventario creado. Se asoció la cita de hoy (HH:MM)." o el mensaje estándar.
- [ ] **Step 3:** JS del formulario de check-in — al cambiar `#vehicle_id`, fetch `api/appointments/vehicle-info/{id}`; renderizar banner azul (cita hoy → "se asociará al guardar") o ámbar (cita otro día → "no se asociará"), debajo del selector de placa.
- [ ] **Step 4:** Verificar en navegador y commit.

## Task 11: Tests de la regla de negocio

**Files:**
- Create: `tests/Feature/AppointmentTest.php`

- [ ] **Step 1:** Tests (TDD): asociación mismo día; no-asociación otro día; cita completed no se reasocia; `unlink` restaura `confirmed`; `vehicleInfo` devuelve today/others; CRUD de cita y seguimiento (auth admin).
- [ ] **Step 2:** `php artisan test --filter=AppointmentTest` — todos PASS.
- [ ] **Step 3:** Commit.

## Task 12: Verificación final

- [ ] `php artisan migrate:status`, `php artisan route:list`, `php -l` sobre archivos nuevos/modificados.
- [ ] `php artisan test --filter=AppointmentTest`.
- [ ] Flujo manual: crear cita de hoy → crear check-in del vehículo → verificar badge en cita; crear cita para mañana → check-in hoy → verificar indicador ámbar y no asociación.
- [ ] Commit final del módulo.

