# Recordatorios automáticos por WhatsApp — Diseño

**Fecha:** 01 de septiembre de 2026
**Estado:** Aprobado (decisiones confirmadas con el usuario)

## Objetivo

Enviar recordatorios automáticos por WhatsApp (Evolution API) a clientes y asesores según reglas configurables (días de anticipación, hitos, hora de envío), con control por tipo de recordatorio y auditoría/idempotencia de envíos.

## Decisiones clave

- **D1 — Configuración global:** la configuración de recordatorios vive en `company_settings` (política de negocio global). Las credenciales de WhatsApp ya se resuelven por establecimiento con fallback a company (`WhatsAppService::resolveCredentials`), por lo que no se duplica.
- **D2 — Toggles por tipo:** cada tipo de recordatorio tiene su propio interruptor `reminder_<tipo>_enabled` + campos de timing. La empresa decide qué recordatorios están activos (ej. SOAT sí para una empresa, no para otra). Tipos futuros (SOAT, cita, cobranza, etc.) agregarán sus propios toggles.
- **D3 — Motor programado:** un comando `reminders:process` agendado cada 30 minutos (`->withSchedule()` en `bootstrap/app.php`, `withoutOverlapping`). La hora de envío configurada se evalúa dentro del comando → cambiable sin tocar cron.
- **D4 — Idempotencia por log:** tabla `reminder_logs` con unique `(type, target_type, target_id, trigger_date)`. Cada hito de autoparte es un `trigger_date` distinto → un mensaje por hito, sin spam ni duplicados si el comando corre dos veces.
- **D5 — Destinatarios:** cliente → `vehicle.owner.party` (mobile o phone). Asesor de autopartes → `estimates.advisor_id` con fallback a `part_orders.created_by`; asesor de presupuestos → `estimates.advisor_id`.
- **D6 — Envío por cola:** se reutiliza el job `SendWhatsAppMessage` (se le agrega un parámetro opcional `reminderLogId` para actualizar el estado del log). El log se crea `pending` antes de despachar; el job lo pasa a `sent`/`failed`. Un log `failed` no bloquea el reintento (la siguiente corrida crea uno nuevo para el mismo `trigger_date`).

## Reglas (fase 1)

| Regla | Destinatario | Disparo | Condición |
|---|---|---|---|
| `technical_review` | Cliente (dueño del vehículo) | `reminder_technical_review_days` (10) antes de `vehicles.technical_review_date` | `technical_review_date` no nulo; trigger == hoy |
| `maintenance` | Cliente | `reminder_maintenance_days` (7) antes de `vehicles.next_maintenance_date` | `next_maintenance_date` no nulo; trigger == hoy |
| `part_order` | Asesor (`estimates.advisor_id` → `created_by`) | Hitos `reminder_part_milestones` (`25,20,17,15,10,5`) días antes de `part_orders.expected_delivery` | `status ∈ ordered, in_transit`; cada hito == hoy |
| `estimate` | Asesor (`estimates.advisor_id`) | Cada `reminder_estimate_every_days` (3) días desde `last_sent_at`/`created_at` | `status ∈ sent_insurance, sent_client`; días espera % N == 0 |

## Configuración (`company_settings`)

- `reminder_enabled` bool true — switch maestro (pausa global).
- `reminder_hour` string `09:00` — hora de envío (se envía en la primera corrida del scheduler con hora ≥ configurada).
- `reminder_technical_review_enabled` bool true / `reminder_technical_review_days` int 10.
- `reminder_maintenance_enabled` bool true / `reminder_maintenance_days` int 7.
- `reminder_part_order_enabled` bool true / `reminder_part_milestones` string `25,20,17,15,10,5`.
- `reminder_estimate_enabled` bool true / `reminder_estimate_every_days` int 3.

## Modelo de datos — `reminder_logs`

`type` (40), `target_type` (40: vehicle|part_order|estimate), `target_id`, `trigger_date` (date), `recipient_type` (client|advisor), `phone` (30 nullable), `recipient_name` nullable, `message` text nullable, `status` (pending|sent|failed), `error` text nullable, `sent_at` nullable, timestamps. Unique `(type, target_type, target_id, trigger_date)`; índice `(type, status)` y `(trigger_date)`.

## Mensajes (`NotificationService`)

Eventos nuevos: `reminder_technical_review`, `reminder_maintenance`, `reminder_part_order`, `reminder_estimate`. Contenido en español con placa, fecha y días restantes; sin enlace de portal (a diferencia de los eventos del flujo).

## UI

- Pestaña "Recordatorios" en `company-settings/edit` (patrón de la pestaña "Mantenimiento"): switch maestro + hora + una tarjeta por tipo con su switch y campos de timing.
- Badge "WhatsApp" en el panel `reminders/index` que indica si el recordatorio de hoy ya fue despachado (log pending/sent).

## Fuera de alcance (fase 2+)

Recordatorio de cita 24 h, SOAT (requiere `soat_expiration` en vehículo), cobranza de facturas, OT en espera de repuestos, encuesta automatizada, entrega.
