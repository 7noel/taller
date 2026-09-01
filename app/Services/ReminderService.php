<?php

namespace App\Services;

use App\Jobs\SendWhatsAppMessage;
use App\Models\CompanySetting;
use App\Models\Establishment;
use App\Models\Estimate;
use App\Models\PartOrder;
use App\Models\Party;
use App\Models\ReminderLog;
use App\Models\User;
use App\Models\Vehicle;
use Carbon\Carbon;

class ReminderService
{
    public const TYPE_TECHNICAL_REVIEW = 'technical_review';
    public const TYPE_MAINTENANCE = 'maintenance';
    public const TYPE_PART_ORDER = 'part_order';
    public const TYPE_ESTIMATE = 'estimate';

    public const DEFAULT_MILESTONES = [25, 20, 17, 15, 10, 5];

    public function __construct(
        protected NotificationService $messages,
    ) {
    }

    /**
     * Procesa los recordatorios automáticos vencidos hoy y despacha los envíos.
     *
     * Guardas globales: settings existente + switch maestro + hora configurada.
     * Cada regla evalúa su propio toggle (reminder_<tipo>_enabled).
     * La idempotencia la garantiza la unique (type, target_type, target_id,
     * trigger_date) de reminder_logs; los logs 'failed' no bloquean el reintento.
     *
     * @return array{sent:int, dry_run:bool, disabled:array}
     */
    public function process(bool $dryRun = false): array
    {
        $settings = CompanySetting::get();

        $result = ['sent' => 0, 'dry_run' => $dryRun, 'disabled' => []];

        if (! $settings || ! $settings->reminder_enabled) {
            return $result;
        }

        if (! $this->isSendTime($settings->reminder_hour)) {
            return $result;
        }

        if ($settings->reminder_technical_review_enabled) {
            $result['sent'] += $this->sendTechnicalReview((int) ($settings->reminder_technical_review_days ?: 10), $dryRun);
        } else {
            $result['disabled'][] = self::TYPE_TECHNICAL_REVIEW;
        }

        if ($settings->reminder_maintenance_enabled) {
            $result['sent'] += $this->sendMaintenance((int) ($settings->reminder_maintenance_days ?: 7), $dryRun);
        } else {
            $result['disabled'][] = self::TYPE_MAINTENANCE;
        }

        if ($settings->reminder_part_order_enabled) {
            $result['sent'] += $this->sendPartOrderReminders($settings->reminder_part_milestones, $dryRun);
        } else {
            $result['disabled'][] = self::TYPE_PART_ORDER;
        }

        if ($settings->reminder_estimate_enabled) {
            $result['sent'] += $this->sendEstimateReminders((int) ($settings->reminder_estimate_every_days ?: 3), $dryRun);
        } else {
            $result['disabled'][] = self::TYPE_ESTIMATE;
        }

        return $result;
    }

    /**
     * Revisión técnica → cliente (dueño del vehículo), N días antes del vencimiento.
     */
    protected function sendTechnicalReview(int $days, bool $dryRun): int
    {
        $sent = 0;

        Vehicle::query()
            ->with(['owner.party'])
            ->whereNotNull('technical_review_date')
            ->get()
            ->each(function (Vehicle $vehicle) use ($days, $dryRun, &$sent) {
                $due = $vehicle->technical_review_date;
                $triggerDate = $due->copy()->subDays($days);

                if (! $triggerDate->isToday()) {
                    return;
                }

                $party = $vehicle->owner?->party;
                $phone = $this->partyPhone($party);

                if (! $phone) {
                    return;
                }

                $message = $this->messages->buildMessage('reminder_technical_review', [
                    'recipient' => $party->display_name,
                    'plate' => $vehicle->plate,
                    'due_date' => $due->format('d/m/Y'),
                ]);

                if ($this->alreadyHandled(self::TYPE_TECHNICAL_REVIEW, 'vehicle', $vehicle->id, $triggerDate)) {
                    return;
                }

                $sent++;
                if ($dryRun) {
                    return;
                }

                $this->dispatchReminder(
                    self::TYPE_TECHNICAL_REVIEW, 'vehicle', $vehicle->id, $triggerDate,
                    'client', $phone, $party->display_name, $message,
                    $this->defaultEstablishment()
                );
            });

        return $sent;
    }

    /**
     * Mantenimiento preventivo → cliente (dueño del vehículo), N días antes.
     */
    protected function sendMaintenance(int $days, bool $dryRun): int
    {
        $sent = 0;

        Vehicle::query()
            ->with(['owner.party'])
            ->whereNotNull('next_maintenance_date')
            ->get()
            ->each(function (Vehicle $vehicle) use ($days, $dryRun, &$sent) {
                $due = $vehicle->next_maintenance_date;
                $triggerDate = $due->copy()->subDays($days);

                if (! $triggerDate->isToday()) {
                    return;
                }

                $party = $vehicle->owner?->party;
                $phone = $this->partyPhone($party);

                if (! $phone) {
                    return;
                }

                $message = $this->messages->buildMessage('reminder_maintenance', [
                    'recipient' => $party->display_name,
                    'plate' => $vehicle->plate,
                    'due_date' => $due->format('d/m/Y'),
                ]);

                if ($this->alreadyHandled(self::TYPE_MAINTENANCE, 'vehicle', $vehicle->id, $triggerDate)) {
                    return;
                }

                $sent++;
                if ($dryRun) {
                    return;
                }

                $this->dispatchReminder(
                    self::TYPE_MAINTENANCE, 'vehicle', $vehicle->id, $triggerDate,
                    'client', $phone, $party->display_name, $message,
                    $this->defaultEstablishment()
                );
            });

        return $sent;
    }

    /**
     * Autopartes de seguro → asesor (estimates.advisor_id, fallback created_by),
     * en los hitos configurados antes de expected_delivery. Solo pedidos
     * ordered/in_transit (aún no recibidos).
     */
    protected function sendPartOrderReminders(?string $milestones, bool $dryRun): int
    {
        $milestoneDays = $this->parseMilestones($milestones);
        $sent = 0;

        PartOrder::query()
            ->with(['estimate.advisor', 'estimate.vehicle', 'part', 'provider', 'creator'])
            ->whereIn('status', ['ordered', 'in_transit'])
            ->whereNotNull('expected_delivery')
            ->get()
            ->each(function (PartOrder $order) use ($milestoneDays, $dryRun, &$sent) {
                foreach ($milestoneDays as $days) {
                    $triggerDate = $order->expected_delivery->copy()->subDays($days);

                    if (! $triggerDate->isToday()) {
                        continue;
                    }

                    $advisor = $order->estimate?->advisor ?? $order->creator;
                    $phone = $this->userPhone($advisor);

                    if (! $phone) {
                        return;
                    }

                    $remaining = (int) now()->startOfDay()->diffInDays($order->expected_delivery, false);

                    $message = $this->messages->buildMessage('reminder_part_order', [
                        'recipient' => $advisor->name,
                        'part' => $order->part?->name ?? 'Repuesto',
                        'plate' => $order->estimate?->vehicle?->plate ?? '',
                        'estimate_sn' => $order->estimate?->document_sn ?? ('#' . $order->estimate_id),
                        'expected' => $order->expected_delivery->format('d/m/Y'),
                        'remaining' => $remaining,
                        'provider' => $order->provider?->display_name ?? 'la aseguradora',
                    ]);

                    if ($this->alreadyHandled(self::TYPE_PART_ORDER, 'part_order', $order->id, $triggerDate)) {
                        return;
                    }

                    $sent++;
                    if ($dryRun) {
                        return;
                    }

                    $this->dispatchReminder(
                        self::TYPE_PART_ORDER, 'part_order', $order->id, $triggerDate,
                        'advisor', $phone, $advisor->name, $message,
                        $this->resolveEstablishment($order->estimate?->establishment_id)
                    );
                }
            });

        return $sent;
    }

    /**
     * Presupuestos sin aprobación (seguro/cliente) → asesor, cada N días
     * desde last_sent_at (o created_at). Se dispara cuando los días de espera
     * son múltiplo exacto de N.
     */
    protected function sendEstimateReminders(int $everyDays, bool $dryRun): int
    {
        $sent = 0;

        Estimate::query()
            ->with(['advisor', 'vehicle'])
            ->whereIn('status', ['sent_insurance', 'sent_client'])
            ->get()
            ->each(function (Estimate $estimate) use ($everyDays, $dryRun, &$sent) {
                $advisor = $estimate->advisor;
                $phone = $this->userPhone($advisor);

                if (! $phone) {
                    return;
                }

                $anchor = $estimate->last_sent_at?->toDateString() ?? $estimate->created_at?->toDateString();

                if (! $anchor) {
                    return;
                }

                $daysWaiting = (int) abs(now()->startOfDay()->diffInDays(Carbon::parse($anchor)));

                if ($daysWaiting < $everyDays || $daysWaiting % $everyDays !== 0) {
                    return;
                }

                $who = $estimate->status === 'sent_insurance' ? 'la aseguradora' : 'el cliente';

                $message = $this->messages->buildMessage('reminder_estimate', [
                    'recipient' => $advisor->name,
                    'sn' => $estimate->document_sn ?? ('#' . $estimate->id),
                    'plate' => $estimate->vehicle?->plate ?? '',
                    'days' => $daysWaiting,
                    'who' => $who,
                ]);

                $triggerDate = now()->startOfDay();

                if ($this->alreadyHandled(self::TYPE_ESTIMATE, 'estimate', $estimate->id, $triggerDate)) {
                    return;
                }

                $sent++;
                if ($dryRun) {
                    return;
                }

                $this->dispatchReminder(
                    self::TYPE_ESTIMATE, 'estimate', $estimate->id, $triggerDate,
                    'advisor', $phone, $advisor->name, $message,
                    $this->resolveEstablishment($estimate->establishment_id)
                );
            });

        return $sent;
    }

    /**
     * Crea el reminder_log (pending) y despacha el job de WhatsApp. El job
     * actualiza el log a sent/failed al completarse.
     */
    protected function dispatchReminder(
        string $type,
        string $targetType,
        int $targetId,
        Carbon $triggerDate,
        string $recipientType,
        string $phone,
        string $recipientName,
        string $message,
        Establishment $establishment
    ): void {
        $log = ReminderLog::create([
            'type' => $type,
            'target_type' => $targetType,
            'target_id' => $targetId,
            'trigger_date' => $triggerDate->toDateString(),
            'recipient_type' => $recipientType,
            'phone' => $phone,
            'recipient_name' => $recipientName,
            'message' => $message,
            'status' => ReminderLog::STATUS_PENDING,
        ]);

        SendWhatsAppMessage::dispatch($establishment, $phone, $message, $log->id);
    }

    /**
     * ¿Ya existe un recordatorio (no fallido) para esta entidad y fecha de disparo?
     * Los logs fallidos no bloquean: permiten reintento en la siguiente corrida.
     */
    protected function alreadyHandled(string $type, string $targetType, int $targetId, Carbon $triggerDate): bool
    {
        return ReminderLog::query()
            ->where('type', $type)
            ->where('target_type', $targetType)
            ->where('target_id', $targetId)
            ->where('trigger_date', $triggerDate->toDateString())
            ->where('status', '!=', ReminderLog::STATUS_FAILED)
            ->exists();
    }

    /**
     * La hora de envío es configurable; se envía en la primera corrida del
     * scheduler cuya hora sea >= a la configurada (sin tocar el cron).
     */
    protected function isSendTime(?string $hour): bool
    {
        $hour = $hour ?: '09:00';

        return now()->format('H:i') >= $hour;
    }

    protected function parseMilestones(?string $raw): array
    {
        $raw = $raw ?: '25,20,17,15,10,5';

        $milestones = array_values(array_filter(
            array_map('intval', explode(',', $raw)),
            fn (int $days) => $days > 0
        ));

        rsort($milestones);

        return $milestones ?: self::DEFAULT_MILESTONES;
    }

    protected function partyPhone(?Party $party): ?string
    {
        if (! $party) {
            return null;
        }

        $phone = trim((string) ($party->mobile ?: $party->phone));

        return $phone !== '' ? $phone : null;
    }

    protected function userPhone(?User $user): ?string
    {
        if (! $user) {
            return null;
        }

        $phone = trim((string) $user->phone);

        return $phone !== '' ? $phone : null;
    }

    /**
     * Establecimiento del envío: el del contexto (presupuesto) o el primero
     * como respaldo. WhatsAppService resuelve credenciales con fallback a
     * company_settings, por lo que un establecimiento por defecto basta.
     */
    protected function resolveEstablishment(?int $preferredId = null): Establishment
    {
        if ($preferredId) {
            $establishment = Establishment::find($preferredId);

            if ($establishment) {
                return $establishment;
            }
        }

        return $this->defaultEstablishment();
    }

    protected function defaultEstablishment(): Establishment
    {
        return Establishment::query()->orderBy('id')->first() ?? new Establishment();
    }
}
