# Recordatorios automáticos por WhatsApp — Plan de Implementación

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) o superpowers:executing-plans para implementar este plan tarea por tarea. Pasos con checkbox (`- [ ]`).

**Goal:** Enviar recordatorios automáticos por WhatsApp (Evolution API) a clientes y asesores con configuración global por tipo (días de anticipación, hitos, hora) y auditoría idempotente.

**Architecture:** Módulo Laravel estándar del proyecto. Motor `ReminderService::process()` invocado por el comando `reminders:process` (scheduler cada 30 min, `withoutOverlapping`); despacha el job existente `SendWhatsAppMessage` (con `reminderLogId` opcional para auditar). Config en `company_settings` con toggles por tipo. Idempotencia por unique en `reminder_logs`.

**Tech Stack:** Laravel 12, MySQL, Evolution API (job `SendWhatsAppMessage`), Tailwind (CDN), Tabulator.

**Spec:** `docs/superpowers/specs/2026-09-01-recordatorios-whatsapp-design.md`

## Constraints

- Migraciones re-editadas en origen (patrón del proyecto, requiere `migrate:fresh` en dev y testing). Solo `Schema::create`.
- Base de datos en inglés; jobs para WhatsApp; lógica en Services; comandos Artisan en `app/Console/Commands`.
- `SendWhatsAppMessage` mantiene su firma actual; se agrega parámetro opcional `?int $reminderLogId = null` (los 3 call sites existentes no cambian).
- Config en `company_settings` con toggles por tipo + switch maestro + hora configurable.
- Frontend: reglas 07/11/12 (app shell, badges, Tabulator, `.search-input`, flash `green-50`, `@json` solo con variables).
- Formularios con `@csrf`; anti-doble envío cubierto por `form-guard`.
- Rutas: no se agregan rutas nuevas (solo comando + scheduler + panel existente).

---

## Task 1: Documentos (spec + plan)

- [x] **Step 1:** Crear `docs/superpowers/specs/2026-09-01-recordatorios-whatsapp-design.md`.
- [x] **Step 2:** Crear `docs/superpowers/plans/2026-09-01-recordatorios-whatsapp.md`.
- [ ] **Step 3:** Commit `docs(recordatorios): spec y plan de recordatorios WhatsApp`.

## Task 2: Migraciones

**Files:**
- Modify: `database/migrations/2026_08_24_000001_create_company_settings_table.php` (agregar columnas de recordatorios)
- Create: `database/migrations/2026_09_01_000700_create_reminder_logs_table.php`

- [ ] **Step 1:** Agregar a `company_settings`: `reminder_enabled` (bool true), `reminder_hour` (string 5, `09:00`), `reminder_technical_review_enabled` (bool true), `reminder_technical_review_days` (int 10), `reminder_maintenance_enabled` (bool true), `reminder_maintenance_days` (int 7), `reminder_part_order_enabled` (bool true), `reminder_part_milestones` (string 100, `25,20,17,15,10,5`), `reminder_estimate_enabled` (bool true), `reminder_estimate_every_days` (int 3).
- [ ] **Step 2:** Crear `reminder_logs` (spec §Modelo de datos) con unique `(type, target_type, target_id, trigger_date)` e índices `(type,status)` y `(trigger_date)`.
- [ ] **Step 3:** `php -l` sobre ambas y commit `feat(recordatorios): migraciones (settings + reminder_logs)`.

## Task 3: Modelos

**Files:**
- Create: `app/Models/ReminderLog.php`
- Modify: `app/Models/CompanySetting.php` (fillable + casts)

- [ ] **Step 1:** `ReminderLog` — fillable, casts (`trigger_date` date, `sent_at` datetime), `STATUS_*` consts, `getStatusLabelAttribute`.
- [ ] **Step 2:** `CompanySetting` — agregar los 10 campos al fillable; casts boolean/int para los nuevos.
- [ ] **Step 3:** `php -l` y commit `feat(recordatorios): modelos ReminderLog y CompanySetting`.

## Task 4: Servicio de recordatorios + plantillas

**Files:**
- Create: `app/Services/ReminderService.php`
- Modify: `app/Services/NotificationService.php` (eventos `reminder_*`)
- Modify: `app/Jobs/SendWhatsAppMessage.php` (`?int $reminderLogId = null` + actualizar log)

- [ ] **Step 1:** `NotificationService` — casos `reminder_technical_review`, `reminder_maintenance`, `reminder_part_order`, `reminder_estimate`.
- [ ] **Step 2:** `ReminderService` — `process(bool $dryRun = false)`: guardas (settings, `reminder_enabled`, hora), 4 reglas con filtro por `*_enabled`, resolución de destinatarios, `ReminderLog` pending → dispatch → el job audita.
- [ ] **Step 3:** `SendWhatsAppMessage` — parámetro opcional `reminderLogId`; en `handle()` actualiza log a `sent`/`failed` (y `sent_at`/`error`) antes de lanzar excepción si falla.
- [ ] **Step 4:** `php -l` y commit `feat(recordatorios): ReminderService + plantillas + auditoría en job`.

## Task 5: Comando + scheduler

**Files:**
- Create: `app/Console/Commands/RemindersProcess.php`
- Modify: `bootstrap/app.php` (`withSchedule`)

- [ ] **Step 1:** Comando `reminders:process` con opción `--dry-run` (reporta candidatos sin registrar ni enviar).
- [ ] **Step 2:** `bootstrap/app.php` → `->withSchedule(fn (Schedule $s) => $s->command('reminders:process')->everyThirtyMinutes()->withoutOverlapping()->appendOutputTo(storage_path('logs/reminders.log')))`.
- [ ] **Step 3:** `php artisan reminders:process --dry-run` y `php artisan schedule:list`; commit `feat(recordatorios): comando y scheduler`.

## Task 6: Configuración UI

**Files:**
- Modify: `app/Http/Controllers/CompanySettingController.php` (validación + merge de booleans)
- Modify: `resources/views/company-settings/edit.blade.php` (pestaña "Recordatorios")

- [ ] **Step 1:** Controller — merge de los 5 booleans (`boolean()`) antes de validar; reglas de validación (hora `date_format:H:i`, días `min:0 max:90`, hitos `regex:/^[0-9,\s]+$/`, cada `min:1 max:60`).
- [ ] **Step 2:** Vista — botón `data-tab="tab-reminders"` + panel con switch maestro, hora y tarjeta por tipo (switch + campo de timing).
- [ ] **Step 3:** Verificar render (HTTP 200) y commit `feat(recordatorios): pestaña de configuración`.

## Task 7: Badge WhatsApp en panel de recordatorios

**Files:**
- Modify: `app/Http/Controllers/ReminderController.php` (campo `whatsapp` por fila)
- Modify: `resources/views/reminders/index.blade.php` (columna "WhatsApp" con badge)

- [ ] **Step 1:** Controller — helper `whatsappLog(string $type, int $targetId)` que consulta `reminder_logs` (pending/sent, `trigger_date` hoy) y agrega `whatsapp` (programado/enviado) a las filas de vehículo y presupuesto.
- [ ] **Step 2:** Vista — columna "WhatsApp" con badge (verde "Enviado", azul "Programado", gris "—").
- [ ] **Step 3:** Verificar render y commit `feat(recordatorios): badge WhatsApp en panel`.

## Task 8: Tests

**Files:**
- Create: `tests/Feature/WhatsAppReminderTest.php`

- [ ] **Step 1:** Tests (TDD): revisión técnica dispara; mantenimiento dispara; autopartes por hito (25/20/17/15/10/5) y `received` no dispara; presupuesto por cadencia; hora futura no dispara (`Carbon::setTestNow`); idempotencia (2 corridas → 1 log + 1 push); tipo desactivado no dispara; sin teléfono se omite.
- [ ] **Step 2:** `php artisan migrate:fresh` (dev + testing) y `php artisan test --filter=WhatsAppReminderTest` — PASS.
- [ ] **Step 3:** Commit `feat(recordatorios): tests del motor`.

## Task 9: Verificación final

- [ ] `php -l` sobre archivos nuevos/modificados; `php artisan view:cache`.
- [ ] Suite completa `php artisan test` OK.
- [ ] Prueba manual: `php artisan reminders:process --dry-run` con un vehículo a 10 días de su revisión técnica.
- [ ] Commit final del módulo.

