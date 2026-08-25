<?php

namespace Database\Seeders;

use App\Models\CheckIn;
use App\Models\Establishment;
use App\Models\Part;
use App\Models\RepairService;
use App\Models\User;
use App\Services\EstimateService;
use Illuminate\Database\Seeder;

class EstimateSeeder extends Seeder
{
    public function run(): void
    {
        $establishment = Establishment::query()->first();
        $checkIns = CheckIn::query()->with(['vehicle', 'client', 'insuranceCompany'])->get();
        $services = RepairService::query()->take(6)->get();
        $parts = Part::query()->take(6)->get();
        $advisor = User::role('Asesor')->first() ?? User::query()->first();

        if (!$establishment || $checkIns->isEmpty()) {
            return;
        }

        $datasets = [
            [
                'status' => 'draft',
                'work_days' => 5,
                'contact_name' => null,
                'items' => fn () => [
                    $this->serviceItem($services[0] ?? null, 1, 0, 0),
                    $this->serviceItem($services[1] ?? null, 2, 0, 0),
                ],
            ],
            [
                'status' => 'sent_insurance',
                'work_days' => 4,
                'contact_name' => null,
                'items' => fn () => [
                    $this->serviceItem($services[2] ?? null, 2, 0, 0),
                    $this->partItem($parts[0] ?? null, 1, 0, 'internal'),
                    $this->partItem($parts[1] ?? null, 2, 5, 'external'),
                ],
            ],
            [
                'status' => 'approved_insurance',
                'work_days' => 6,
                'contact_name' => 'Aprobado por seguro',
                'items' => fn () => [
                    $this->serviceItem($services[3] ?? null, 1, 0, 0),
                    $this->serviceItem($services[4] ?? null, 1, 0, 0),
                    $this->partItem($parts[2] ?? null, 1, 0, 'insurance'),
                ],
            ],
            [
                'status' => 'in_repair',
                'work_days' => 7,
                'contact_name' => 'En reparación',
                'items' => fn () => [
                    $this->serviceItem($services[5] ?? null, 1, 0, 0),
                    $this->partItem($parts[3] ?? null, 2, 0, 'internal'),
                    $this->partItem($parts[4] ?? null, 1, 0, 'external'),
                ],
            ],
            [
                'status' => 'finalized',
                'work_days' => 8,
                'contact_name' => 'Finalizado',
                'items' => fn () => [
                    $this->serviceItem($services[0] ?? null, 1, 0, 0),
                    $this->serviceItem($services[1] ?? null, 1, 0, 0),
                    $this->serviceItem($services[2] ?? null, 2, 0, 0),
                    $this->partItem($parts[5] ?? null, 1, 0, 'internal'),
                ],
            ],
        ];

        foreach ($datasets as $index => $dataset) {
            $checkIn = $checkIns[$index % $checkIns->count()];

            $data = [
                'check_in_id' => $checkIn->id,
                'vehicle_id' => $checkIn->vehicle_id,
                'client_id' => $checkIn->client_id,
                'insurance_company_id' => $checkIn->insurance_company_id,
                'claim_number' => $checkIn->claim_number,
                'service_type' => $checkIn->service_type ?? 'siniestro',
                'establishment_id' => $establishment->id,
                'advisor_id' => $advisor?->id,
                'work_days' => $dataset['work_days'],
                'contact_name' => $dataset['contact_name'],
                'contact_phone' => null,
                'contact_email' => null,
                'hourly_rate' => $establishment->default_hourly_rate ?? 0,
                'panel_rate' => $establishment->default_panel_rate ?? 0,
                'currency' => $establishment->base_currency ?? 'PEN',
                'global_discount_type' => 'percentage',
                'global_discount_value' => 5.0,
                'items' => $dataset['items'](),
            ];

            $this->createEstimate($data, $dataset['status']);
        }
    }

    protected function serviceItem(?RepairService $service, float $qty, float $discount, float $optionalPrice = 0): array
    {
        return [
            'service_id' => $service?->id,
            'description' => $service?->name ?? 'Servicio de reparación general',
            'quantity' => $qty,
            'unit_price' => $optionalPrice ?: ($service?->sell_price ?? 120.0),
            'discount_pct' => $discount,
            'supply_source' => 'internal',
            'cost_price' => $service?->cost_price ?? 0,
        ];
    }

    protected function partItem(?Part $part, float $qty, float $discount, string $source): array
    {
        return [
            'part_id' => $part?->id,
            'description' => $part?->name ?? 'Repuesto genérico',
            'quantity' => $qty,
            'unit_price' => $part?->sell_price ?? 80.0,
            'discount_pct' => $discount,
            'supply_source' => $source,
            'cost_price' => $part?->cost_price ?? 0,
        ];
    }

    /**
     * Recorre la cadena de transiciones válidas hasta alcanzar el estado objetivo
     * (ej. draft → sent_insurance → approved_insurance → in_repair → finalized),
     * registrando el historial en cada paso.
     */
    protected function createEstimate(array $data, string $status): void
    {
        $service = app(EstimateService::class);
        $estimate = $service->create($data);

        $path = [
            'draft' => [],
            'sent_insurance' => ['sent_insurance'],
            'approved_insurance' => ['sent_insurance', 'approved_insurance'],
            'in_repair' => ['sent_insurance', 'approved_insurance', 'in_repair'],
            'finalized' => ['sent_insurance', 'approved_insurance', 'in_repair', 'finalized'],
        ];

        $chain = $path[$status] ?? [];

        foreach ($chain as $step) {
            $service->changeStatus($estimate, $step);
        }
    }
}