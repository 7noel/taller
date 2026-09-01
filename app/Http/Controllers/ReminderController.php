<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\CheckIn;
use App\Models\Estimate;
use App\Models\FollowUp;
use App\Models\ReminderLog;
use App\Models\Vehicle;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class ReminderController extends Controller
{
    /**
     * Panel de recordatorios (revisión técnica, mantenimiento preventivo y
     * presupuestos en aprobación). Accesible con el permiso "ver seguimientos".
     */
    public function index(): View
    {
        Gate::authorize('viewAny', FollowUp::class);

        return view('reminders.index');
    }

    /**
     * Listado por pestaña para Tabulator: ?tab=technical_review|maintenance|estimates
     */
    public function search(Request $request): JsonResponse
    {
        Gate::authorize('viewAny', FollowUp::class);

        $tab = $request->input('tab', 'technical_review');

        $rows = match ($tab) {
            'maintenance' => $this->maintenanceRows(),
            'estimates' => $this->estimateRows(),
            default => $this->technicalReviewRows(),
        };

        return response()->json($rows);
    }

    /**
     * Vehículos cuya revisión técnica vence pronto o ya venció (ventana por review_reminder_days).
     */
    protected function technicalReviewRows(): array
    {
        return Vehicle::query()
            ->with(['vehicleModel.brand', 'owner.party'])
            ->whereNotNull('technical_review_date')
            ->get()
            ->map(function (Vehicle $vehicle) {
                $daysLeft = (int) now()->startOfDay()->diffInDays($vehicle->technical_review_date, false);

                if ($daysLeft > $vehicle->review_reminder_days || $daysLeft < -$vehicle->review_reminder_days) {
                    return null;
                }

                return $this->vehicleRow($vehicle, $vehicle->technical_review_date, $daysLeft, 'technical_review');
            })
            ->filter()
            ->sortBy('days_left')
            ->values()
            ->all();
    }

    /**
     * Vehículos cuyo próximo mantenimiento preventivo vence pronto o ya venció.
     */
    protected function maintenanceRows(): array
    {
        return Vehicle::query()
            ->with(['vehicleModel.brand', 'owner.party'])
            ->whereNotNull('next_maintenance_date')
            ->get()
            ->map(function (Vehicle $vehicle) {
                $daysLeft = (int) now()->startOfDay()->diffInDays($vehicle->next_maintenance_date, false);

                if ($daysLeft > $vehicle->maintenance_reminder_days || $daysLeft < -$vehicle->maintenance_reminder_days) {
                    return null;
                }

                return $this->vehicleRow($vehicle, $vehicle->next_maintenance_date, $daysLeft, 'maintenance');
            })
            ->filter()
            ->sortBy('days_left')
            ->values()
            ->all();
    }

    /**
     * Presupuestos que esperan aprobación (seguro o cliente) o borradores antiguos sin enviar.
     */
    protected function estimateRows(): array
    {
        $waitingStatuses = ['sent_insurance', 'sent_client'];

        $estimates = Estimate::query()
            ->with(['vehicle.vehicleModel.brand', 'client', 'insuranceCompany'])
            ->where(function ($q) use ($waitingStatuses) {
                $q->whereIn('status', $waitingStatuses)
                    // Borradores sin enviar hace más de 2 días también merecen seguimiento.
                    ->orWhere(function ($q2) {
                        $q2->where('status', 'draft')
                            ->where('updated_at', '<=', now()->subDays(2));
                    });
            })
            ->get();

        return $estimates
            ->map(function (Estimate $estimate) {
                $sentAt = $estimate->last_sent_at ?? $estimate->updated_at;
                $daysWaiting = $sentAt ? (int) $sentAt->diffInDays(now()) : 0;

                return [
                    'id' => $estimate->id,
                    'document_sn' => $estimate->document_sn,
                    'status' => $estimate->status,
                    'status_label' => $estimate->status_label,
                    'service_type' => $estimate->service_type,
                    'service_type_label' => CheckIn::SERVICE_TYPES[$estimate->service_type] ?? $estimate->service_type ?? '—',
                    'plate' => $estimate->vehicle?->plate,
                    'vehicle_label' => $estimate->vehicle
                        ? trim(($estimate->vehicle->vehicleModel?->brand?->name ?? '') . ' ' . ($estimate->vehicle->vehicleModel?->name ?? ''))
                        : '',
                    'client_name' => $estimate->client?->display_name,
                    'insurance_name' => $estimate->insuranceCompany?->display_name,
                    'total' => number_format($estimate->total ?? 0, 2),
                    'days_waiting' => $daysWaiting,
                    'last_sent_at' => $sentAt?->format('d/m/Y'),
                    'has_appointment' => $this->vehicleHasAppointment($estimate->vehicle_id),
                    'vehicle_id' => $estimate->vehicle_id,
                    'party_id' => $estimate->client_id,
                    'reminder_note' => "Seguimiento del presupuesto {$estimate->document_sn} ({$estimate->status_label}): verificar respuesta de " . ($estimate->status === 'sent_insurance' ? 'la aseguradora' : 'el cliente') . '.',
                    'context' => 'Presupuesto ' . ($estimate->document_sn ?? '#' . $estimate->id),
                    'whatsapp' => $this->whatsappLog('estimate', $estimate->id, 'estimate'),
                ];
            })
            ->sortByDesc('days_waiting')
            ->values()
            ->all();
    }

    protected function vehicleRow(Vehicle $vehicle, $dueDate, int $daysLeft, string $type): array
    {
        $ownerParty = $vehicle->owner?->party;
        $dueLabel = $dueDate?->format('d/m/Y') ?? '';

        $reminderNote = $type === 'maintenance'
            ? "Recordatorio: mantenimiento preventivo del vehículo {$vehicle->plate} (próxima visita {$dueLabel})."
            : "Recordatorio: revisión técnica del vehículo {$vehicle->plate} (vence {$dueLabel}).";

        return [
            'id' => $vehicle->id,
            'plate' => $vehicle->plate,
            'vehicle_label' => trim(($vehicle->vehicleModel?->brand?->name ?? '') . ' ' . ($vehicle->vehicleModel?->name ?? '')),
            'due_date' => $dueLabel,
            'days_left' => $daysLeft,
            'contact_name' => $ownerParty?->display_name,
            'contact_phone' => $ownerParty?->mobile ?: $ownerParty?->phone,
            'party_id' => $ownerParty?->id,
            'has_appointment' => $this->vehicleHasAppointment($vehicle->id),
            'type' => $type,
            'manual_source' => $vehicle->maintenance_source === 'manual' && $type === 'maintenance',
            'vehicle_id' => $vehicle->id,
            'reminder_note' => $reminderNote,
            'context' => $type === 'maintenance' ? "Mantenimiento preventivo · {$vehicle->plate}" : "Revisión técnica · {$vehicle->plate}",
            'whatsapp' => $this->whatsappLog($type, $vehicle->id, 'vehicle'),
        ];
    }

    /**
     * Estado del recordatorio automático de WhatsApp para hoy (pending|sent|failed|null).
     */
    protected function whatsappLog(string $type, int $targetId, string $targetType): ?string
    {
        return ReminderLog::query()
            ->where('type', $type)
            ->where('target_type', $targetType)
            ->where('target_id', $targetId)
            ->where('trigger_date', now()->toDateString())
            ->orderByDesc('id')
            ->value('status');
    }

    /**
     * ¿El vehículo ya tiene una cita agendada/confirmada sin ingreso? (evita llamadas duplicadas).
     */
    protected function vehicleHasAppointment(?int $vehicleId): bool
    {
        if (! $vehicleId) {
            return false;
        }

        return Appointment::query()
            ->where('vehicle_id', $vehicleId)
            ->whereNull('check_in_id')
            ->whereIn('status', ['scheduled', 'confirmed'])
            ->exists();
    }
}
