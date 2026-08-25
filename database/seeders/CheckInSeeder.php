<?php

namespace Database\Seeders;

use App\Models\CheckIn;
use App\Models\CheckInChecklistItem;
use App\Models\CheckInChecklistResult;
use App\Models\CheckInDamage;
use App\Models\Establishment;
use App\Models\Party;
use App\Models\User;
use App\Models\Vehicle;
use App\Services\DocumentSeriesService;
use Illuminate\Database\Seeder;

class CheckInSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::query()->orderBy('id')->first();
        $establishment = Establishment::query()->orderBy('id')->first();
        $vehicles = Vehicle::query()->with(['relationships.party'])->limit(3)->get();

        if (!$user || !$establishment || $vehicles->isEmpty()) {
            $this->command?->warn('CheckInSeeder: No hay usuarios, establecimientos o vehículos. Se omite.');
            return;
        }

        $checklistItems = CheckInChecklistItem::query()->orderBy('order')->get();
        $insuranceCompany = Party::query()->where('is_insurance_company', true)->orderBy('id')->first();
        $seriesService = app(DocumentSeriesService::class);

        foreach ($vehicles as $index => $vehicle) {
            $owner = $vehicle->relationships->first(fn ($r) => $r->role === 'owner')?->party;

            // Asigna serie IV01 y correlativo incremental (IV01-00001, ...)
            $seriesResult = $seriesService->getNextNumber($establishment->id, 'IV');

            $checkIn = CheckIn::create([
                'vehicle_id' => $vehicle->id,
                'client_id' => $owner?->id,
                'insurance_company_id' => $index === 0 ? $insuranceCompany?->id : null,
                'establishment_id' => $establishment->id,
                'document_series_id' => $seriesResult['series']->id,
                'document_type_code' => $seriesResult['document_type_code'],
                'document_serie' => $seriesResult['series']->prefix_serie,
                'document_number' => $seriesResult['number'],
                'document_sn' => $seriesResult['sn'],
                'created_by' => $user->id,
                'updated_by' => $user->id,
                'service_type' => $index === 0 ? 'siniestro' : ($index === 1 ? 'preventivo' : 'correctivo'),
                'claim_number' => $index === 0 ? 'SIN-' . str_pad((string) ($index + 1), 4, '0', STR_PAD_LEFT) : null,
                'mileage' => 50000 + ($index * 15000),
                'fuel_level' => ['reserva', 'cuarto', 'medio'][$index],
                'property_card' => 'fisica',
                'soat_expiration' => now()->addMonths(6)->toDateString(),
                'technical_review_expiration' => now()->addYear()->toDateString(),
                'keys_count' => 1 + $index,
                'has_remote_control' => $index !== 2,
                'client_request' => $index === 0 ? 'Reparar abolladura en puerta delantera izquierda.' : null,
                'observations' => 'Vehículo ingresado sin novedades adicionales.',
                'status' => ['draft', 'pending_approval', 'approved'][$index],
            ]);

            // Checklist results (todos "good" salvo algunos regulares/malos)
            foreach ($checklistItems->take(20) as $item) {
                $status = match (true) {
                    $item->name === 'LLANTAS' && $index === 0 => 'regular',
                    $item->name === 'FARO DELANTERO' && $index === 0 => 'bad',
                    $item->name === 'GATA' && $index === 2 => 'regular',
                    default => 'good',
                };

                CheckInChecklistResult::create([
                    'check_in_id' => $checkIn->id,
                    'checklist_item_id' => $item->id,
                    'status' => $status,
                    'observations' => $status === 'bad' ? 'Faro presenta quebre en el vidrio.' : null,
                ]);
            }

            // Daños de ejemplo
            if ($index === 0) {
                CheckInDamage::create([
                    'check_in_id' => $checkIn->id,
                    'damage_type' => 'dent',
                    'side' => 'left',
                    'pos_x' => 30,
                    'pos_y' => 50,
                    'notes' => 'Abolladura en puerta delantera.',
                ]);
                CheckInDamage::create([
                    'check_in_id' => $checkIn->id,
                    'damage_type' => 'crack',
                    'side' => 'front',
                    'pos_x' => 60,
                    'pos_y' => 45,
                    'notes' => 'Quebre en faro delantero.',
                ]);
            }

            $this->command?->info("CheckIn #{$checkIn->id} creado para vehículo {$vehicle->plate}.");
        }
    }
}