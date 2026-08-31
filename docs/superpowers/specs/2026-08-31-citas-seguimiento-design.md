# Diseño del módulo: Citas y Seguimiento

> Fecha: 2026-08-31 · Estado: Aprobado por el usuario (diseño consolidado en conversación)

## Objetivo

Agendar citas de ingreso de vehículos al taller, vincularlas automáticamente con el inventario (check-in) **solo cuando el vehículo ingresa el mismo día calendario de la cita**, mostrar indicadores claros cuando no corresponde asociar, y llevar un registro simple de seguimientos (llamadas/visitas/WhatsApp) con próxima acción.

## Alcance

- CRUD de citas con vehículo registrado (placa existente o creación rápida) + persona/contacto.
- Asociación cita ↔ check-in (regla de mismo día) + indicadores en ambos lados.
- CRUD simple de seguimientos.
- Permisos, navegación, y tests de la regla de negocio.

**Fuera de alcance (siguiente iteración):** recordatorios automáticos por WhatsApp, calendario visual de mes, agendamiento por portal público.

## Decisiones clave

### D1. Captura de datos (aprobado por el usuario)

- **Vehículo**: Tom Select por placa (`/api/vehicles/search`) + creación rápida con el modal existente `check-ins/_vehicle_modal.blade.php` (incluye SUNARP). La cita guarda `vehicle_id` (nunca texto libre de placa).
- **Persona**: Tom Select de contactos del vehículo (`VehicleRelationship` con su `Party`) o `Party` cliente. Además, snapshot editable en la cita (`contact_name`, `contact_phone`, `contact_email`) que se autocompleta al elegir la persona y queda libre para casos telefónicos.
- Los campos de contacto **no son obligatorios** (llamada telefónica donde solo se pide la placa).

### D2. Estados de la cita

| valor | label | badge |
|---|---|---|
| `scheduled` | Agendada | azul (info) |
| `confirmed` | Confirmada | verde (éxito) |
| `cancelled` | Cancelada | rojo (error) |
| `completed` | Realizada | gris (neutro) |

Solo `scheduled` y `confirmed` son elegibles para asociación. `cancelled` nunca.

### D3. Regla de asociación cita ↔ check-in

- Al **crear** un check-in (`CheckInService::create`), si `vehicle_id` está presente se llama a `AppointmentService::associateForCheckIn($checkIn)`:
  1. Busca la primera cita con `vehicle_id` = check-in, `status IN (scheduled, confirmed)`, `check_in_id IS NULL` y `DATE(scheduled_at) = DATE(now())`.
  2. Si existe → `check_in_id = $checkIn->id`, `status = completed`. Devuelve la cita.
- **Nunca** se ejecuta en `update` del check-in.
- `appointments.check_in_id` es **unique nullable** (una cita puede tener como máximo un ingreso).
- Desasociar (acción manual `unlink`) deja `check_in_id = NULL` y devuelve la cita a `confirmed`.
- Indicadores:
  - **Formulario de ingreso**: al seleccionar el vehículo, fetch a `api/appointments/vehicle-info/{vehicle}` → banner azul "Cita de hoy HH:MM – contacto (teléfono). Se asociará al guardar." o banner ámbar "Tiene cita para el DD/MM HH:MM (otro día). No se asociará al ingreso."
  - **Listado/detalle de cita**: badge con el `document_sn` del ingreso (`IV01-000123`, enlazable) o aviso "El vehículo ingresó sin esta cita".

### D4. Seguimientos

`follow_ups` con `party_id`/`vehicle_id` (al menos uno), `date`, `type` (`call|whatsapp|email|visit`), `notes`, `next_action_date`, `done` + `done_at`. Listado compacto con filtro de pendientes.

## Modelo de datos

### Tabla `appointments`

| columna | tipo | notas |
|---|---|---|
| `id` | bigint PK | |
| `establishment_id` | FK establishments nullable | default: establecimiento del usuario |
| `vehicle_id` | FK vehicles nullable | |
| `party_id` | FK parties nullable | persona/cliente asociado |
| `advisor_id` | FK users nullable | asesor responsable |
| `contact_name` | varchar(255) nullable | snapshot |
| `contact_phone` | varchar(50) nullable | snapshot |
| `contact_email` | varchar(255) nullable | snapshot |
| `scheduled_at` | datetime | fecha+hora de la cita |
| `service_type` | varchar(30) nullable | `CheckIn::SERVICE_TYPES` |
| `reason` | text nullable | motivo libre |
| `status` | varchar(20) default `scheduled` | D2 |
| `check_in_id` | FK check_ins **unique nullable** | vínculo con ingreso |
| `created_by` / `updated_by` | FK users nullable | |
| `created_at` / `updated_at` | timestamps | |
| `deleted_at` | soft deletes | |

### Tabla `follow_ups`

| columna | tipo | notas |
|---|---|---|
| `id` | bigint PK | |
| `party_id` | FK parties nullable | al menos uno de party/vehicle |
| `vehicle_id` | FK vehicles nullable | |
| `date` | date | fecha del seguimiento |
| `type` | varchar(20) default `call` | `call|whatsapp|email|visit` |
| `notes` | text nullable | |
| `next_action_date` | date nullable | próxima acción |
| `done` | boolean default false | |
| `done_at` | datetime nullable | |
| `created_by` / `updated_by` | FK users nullable | |
| `created_at` / `updated_at` | timestamps | |
| `deleted_at` | soft deletes | |

## Backend

- **Modelos**: `Appointment`, `FollowUp` — SoftDeletes + LogsActivity + creador/actualizador, igual que `CheckIn`.
- **Servicios**: `AppointmentService` (`create`, `update`, `cancel`, `confirm`, `unlink`, `associateForCheckIn`, `vehicleInfo`), `FollowUpService` (CRUD simple + `markDone`).
- **Form Requests**: `AppointmentRequest`, `FollowUpRequest` (patrón `CheckInRequest`: authorize `true`, validación en el controlador con `Gate`).
- **Policies**: `AppointmentPolicy`, `FollowUpPolicy` (patrón `CheckInPolicy`).
- **Controladores**: `AppointmentController` (resource + `vehicleInfo` API + `confirm`/`cancel`/`unlink`), `FollowUpController` (resource).
- **Permisos** (seeder `RolePermissionSeeder`): `ver citas`, `crear citas`, `editar citas`, `eliminar citas`, `ver seguimientos`, `crear seguimientos`, `editar seguimientos`, `eliminar seguimientos`. Admin = `Permission::all()` (automático). Asesor = ver/crear/editar citas y seguimientos (sin eliminar, patrón del módulo de inventarios).
- **Rutas** (grupo `auth` + `verified`): `Route::resource('appointments')`, `Route::resource('follow-ups')`, `GET api/appointments/vehicle-info/{vehicle}`, `POST appointments/{appointment}/confirm|cancel|unlink`.

## Frontend

- `appointments/index`: filtros (desde/hasta fecha + búsqueda) + Tabulator (fecha/hora, placa+vehículo, contacto, teléfono, asesor, tipo servicio, estado badge, ingreso asociado con `document_sn`, acciones ⋮). Reglas 07/11/12.
- `appointments/create|edit` + `_form.blade.php`: Tom Select vehículo (selección única estricta) + botón "Nueva placa" (modal reutilizado), Tom Select contacto del vehículo/cliente (autocompleta snapshot), asesor, fecha + hora, tipo de servicio, motivo. Asteriscos rojos sincronizados con `AppointmentRequest`.
- `follow-ups/index`: Tabulator + modal de creación + acción "marcar como hecho".
- Navegación: nuevo grupo "Citas" (icono calendario) con ítems "Citas" y "Seguimiento", protegidos con `@can('ver citas')` / `@can('ver seguimientos')`.
- Formulario de check-in: banner de cita bajo el selector de placa (JS en `check-ins/_form-scripts.blade.php` o vista create).

## Tests (tests/Feature)

- `AppointmentTest` / `AppointmentAssociationTest`:
  1. Crear cita hoy y check-in del mismo vehículo → cita `completed` + `check_in_id` seteado.
  2. Cita otro día + check-in hoy → NO se asocia.
  3. Cita ya `completed` no se ofrece de nuevo.
  4. `unlink` devuelve la cita a `confirmed`.
  5. `vehicleInfo` devuelve `today` y `others`.
  6. CRUD básico de citas y seguimientos (permisos de admin).

