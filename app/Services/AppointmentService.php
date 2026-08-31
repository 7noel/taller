<?php

namespace App\Services;

use App\Models\Appointment;
use App\Models\CheckIn;
use App\Models\Vehicle;
use Illuminate\Support\Facades\Auth;

class AppointmentService
{
    /**
     * Crea una cita. scheduled_at se arma desde scheduled_date + scheduled_time.
     */
    public function create(array $data): Appointment
    {
        $data['scheduled_at'] = $this->buildScheduledAt($data);
        $data['status'] = $data['status'] ?? 'scheduled';
        $data['establishment_id'] = $data['establishment_id'] ?? Auth::user()?->establishment_id;
        $data['created_by'] = Auth::id();
        $data['updated_by'] = Auth::id();

        return Appointment::create($data);
    }

    /**
     * Actualiza una cita manteniendo su estado (salvo que se indique lo contrario).
     */
    public function update(Appointment $appointment, array $data): Appointment
    {
        $data['scheduled_at'] = $this->buildScheduledAt($data, $appointment);
        $data['updated_by'] = Auth::id();

        $appointment->update($data);

        return $appointment->fresh();
    }

    /**
     * Confirma una cita agendada.
     */
    public function confirm(Appointment $appointment): Appointment
    {
        if ($appointment->status !== 'scheduled') {
            return $appointment->fresh();
        }

        $appointment->update([
            'status' => 'confirmed',
            'updated_by' => Auth::id(),
        ]);

        return $appointment->fresh();
    }

    /**
     * Cancela una cita que no esté realizada.
     */
    public function cancel(Appointment $appointment): Appointment
    {
        if ($appointment->status === 'completed') {
            return $appointment->fresh();
        }

        $appointment->update([
            'status' => 'cancelled',
            'updated_by' => Auth::id(),
        ]);

        return $appointment->fresh();
    }

    /**
     * Desasocia la cita de su ingreso: la cita vuelve a "confirmada".
     */
    public function unlink(Appointment $appointment): Appointment
    {
        if ($appointment->status !== 'completed' || ! $appointment->check_in_id) {
            return $appointment->fresh();
        }

        $appointment->update([
            'check_in_id' => null,
            'status' => 'confirmed',
            'updated_by' => Auth::id(),
        ]);

        return $appointment->fresh();
    }

    /**
     * Regla de negocio: al crear un check-in, se asocia la cita del mismo vehículo
     * que caiga el MISMO DÍA CALENDARIO, que esté agendada/confirmada y que aún
     * no tenga ingreso asociado. La cita pasa a "completed".
     */
    public function associateForCheckIn(CheckIn $checkIn): ?Appointment
    {
        if (! $checkIn->vehicle_id) {
            return null;
        }

        $day = $checkIn->created_at?->toDateString() ?? now()->toDateString();

        $appointment = Appointment::query()
            ->where('vehicle_id', $checkIn->vehicle_id)
            ->whereNull('check_in_id')
            ->whereIn('status', ['scheduled', 'confirmed'])
            ->whereDate('scheduled_at', $day)
            ->orderBy('scheduled_at')
            ->first();

        if (! $appointment) {
            return null;
        }

        $appointment->update([
            'check_in_id' => $checkIn->id,
            'status' => 'completed',
            'updated_by' => $checkIn->created_by ?? Auth::id(),
        ]);

        return $appointment->fresh();
    }

    /**
     * Información de citas pendientes de un vehículo para los indicadores del
     * formulario de ingreso: cita de hoy (se asociará) y citas en otras fechas
     * (no se asociarán).
     *
     * @return array{today: ?array, others: array}
     */
    public function vehicleInfo(Vehicle $vehicle): array
    {
        $base = Appointment::query()
            ->where('vehicle_id', $vehicle->id)
            ->whereNull('check_in_id')
            ->whereIn('status', ['scheduled', 'confirmed']);

        $today = (clone $base)
            ->whereDate('scheduled_at', now()->toDateString())
            ->orderBy('scheduled_at')
            ->first();

        $others = (clone $base)
            ->whereDate('scheduled_at', '!=', now()->toDateString())
            ->where('scheduled_at', '>=', now()->startOfDay())
            ->orderBy('scheduled_at')
            ->limit(5)
            ->get();

        return [
            'today' => $today ? $this->toArray($today) : null,
            'others' => $others->map(fn ($a) => $this->toArray($a))->values()->all(),
        ];
    }

    protected function toArray(Appointment $appointment): array
    {
        return [
            'id' => $appointment->id,
            'scheduled_at' => $appointment->scheduled_at?->format('Y-m-d H:i'),
            'scheduled_date' => $appointment->scheduled_at?->format('d/m/Y'),
            'time' => $appointment->scheduled_at?->format('H:i'),
            'status' => $appointment->status,
            'status_label' => $appointment->status_label,
            'contact_name' => $appointment->contact_name,
            'contact_phone' => $appointment->contact_phone,
            'service_type' => $appointment->service_type,
            'service_type_label' => $appointment->service_type_label,
            'reason' => $appointment->reason,
        ];
    }

    protected function buildScheduledAt(array $data, ?Appointment $current = null): string
    {
        $date = $data['scheduled_date'] ?? ($current?->scheduled_at?->format('Y-m-d'));
        $time = $data['scheduled_time'] ?? ($current?->scheduled_at?->format('H:i'));

        if (! $date || ! $time) {
            throw new \InvalidArgumentException('La fecha y hora de la cita son obligatorias.');
        }

        return "{$date} {$time}:00";
    }
}
