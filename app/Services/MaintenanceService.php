<?php

namespace App\Services;

use App\Models\CheckIn;
use App\Models\CompanySetting;
use App\Models\Vehicle;
use Carbon\Carbon;

class MaintenanceService
{
    // Límites de seguridad para la proyección del próximo preventivo.
    public const MIN_DAYS = 30;
    public const MAX_DAYS = 365;

    /**
     * Sincroniza las fechas del vehículo a partir de un check-in:
     * - technical_review_expiration → vehicles.technical_review_date (solo hacia adelante o si vacío).
     * - service_type = preventivo → última visita, kilometraje y próxima fecha
     *   (se recalcula salvo que el taller la haya ajustado manualmente).
     */
    public function syncFromCheckIn(CheckIn $checkIn): void
    {
        $vehicle = $checkIn->vehicle()->withTrashed()->first();

        if (! $vehicle) {
            return;
        }

        $vehicleData = [];

        // SOAT: el valor más reciente gana (nunca retroceder por un ingreso viejo).
        if ($checkIn->soat_expiration) {
            $currentSoat = $vehicle->soat_expiration;

            if (! $currentSoat || $checkIn->soat_expiration->gt($currentSoat)) {
                $vehicleData['soat_expiration'] = $checkIn->soat_expiration->toDateString();
            }
        }

        // Revisión técnica: el valor más reciente gana (nunca retroceder por un ingreso viejo).
        if ($checkIn->technical_review_expiration) {
            $current = $vehicle->technical_review_date;

            if (! $current || $checkIn->technical_review_expiration->gt($current)) {
                $vehicleData['technical_review_date'] = $checkIn->technical_review_expiration->toDateString();
            }
        }

        if ($checkIn->service_type === 'preventivo') {
            $visitDate = $checkIn->created_at?->toDateString() ?? now()->toDateString();

            $vehicleData['last_maintenance_date'] = $visitDate;
            $vehicleData['last_maintenance_mileage'] = $checkIn->mileage;

            // La fecha ajustada manualmente (maintenance_source = 'manual') no se pisa
            // hasta que el vehículo vuelva a ingresar por preventivo.
            if ($vehicle->maintenance_source !== 'manual') {
                $vehicleData['next_maintenance_date'] = $this->calculateNextMaintenanceDate($vehicle, $visitDate);
                $vehicleData['maintenance_source'] = 'calculated';
            }
        }

        if ($vehicleData) {
            $vehicle->update($vehicleData);
        }
    }

    /**
     * Calcula la próxima fecha de mantenimiento preventivo.
     *
     * Regla (configurable en company_settings):
     * - Con 0 o 1 visita preventiva con kilometraje → última visita + maintenance_default_days (120).
     * - Con ≥2 visitas → proyección con el ritmo real de las últimas N visitas
     *   (maintenance_history_visits, default 3): (días / km recorridos) × maintenance_interval_km (5000).
     * - Límites de seguridad: entre MIN_DAYS (30) y MAX_DAYS (365) días.
     */
    public function calculateNextMaintenanceDate(Vehicle $vehicle, string $lastDate): string
    {
        $settings = CompanySetting::get();
        $intervalKm = $settings?->maintenance_interval_km ?: 5000;
        $defaultDays = $settings?->maintenance_default_days ?: 120;
        $historyVisits = max(2, $settings?->maintenance_history_visits ?: 3);

        $last = Carbon::parse($lastDate);

        $visits = CheckIn::query()
            ->where('vehicle_id', $vehicle->id)
            ->where('service_type', 'preventivo')
            ->whereNotNull('mileage')
            ->orderByDesc('created_at')
            ->limit($historyVisits)
            ->get(['created_at', 'mileage'])
            ->sortBy('created_at')
            ->values();

        if ($visits->count() >= 2) {
            $first = $visits->first();
            $lastVisit = $visits->last();

            // Carbon 3 devuelve diffInDays con signo; siempre usar el valor absoluto.
            $days = abs($lastVisit->created_at->diffInDays($first->created_at));
            $km = (int) $lastVisit->mileage - (int) $first->mileage;

            if ($days > 0 && $km > 0) {
                $daysForInterval = (int) round(($days / $km) * $intervalKm);

                return $last->copy()->addDays($this->clamp($daysForInterval))->toDateString();
            }
        }

        return $last->copy()->addDays($this->clamp($defaultDays))->toDateString();
    }

    protected function clamp(int $days): int
    {
        return max(self::MIN_DAYS, min(self::MAX_DAYS, $days));
    }
}
