<?php

namespace Database\Seeders;

use App\Models\Brand;
use App\Models\CheckIn;
use App\Models\Establishment;
use App\Models\FormTemplate;
use App\Models\Party;
use App\Models\Part;
use App\Models\RepairService;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\VehicleModel;
use App\Models\VehicleRelationship;
use App\Models\WorkOrder;
use App\Models\WorkOrderAssignment;
use App\Models\WorkOrderQualityControl;
use App\Models\WorkOrderSatisfactionSurvey;
use App\Models\WorkOrderSubstage;
use App\Services\DocumentSeriesService;
use App\Services\EstimateService;
use App\Services\WorkOrderService;
use Illuminate\Database\Seeder;

class DemoDataSeeder extends Seeder
{
    public const COUNT = 35;

    /**
     * Datos demo enlazados: 35+ partidos, vehículos, contactos, inventarios,
     * presupuestos y órdenes de trabajo en distintos estados (incluyendo
     * quality_control) para probar el flujo completo de control de calidad.
     */
    public function run(): void
    {
        $user = User::query()->orderBy('id')->first();
        $technician = User::role('Técnico')->first() ?? $user;
        $establishment = Establishment::query()->orderBy('id')->first();

        if (! $user || ! $establishment) {
            $this->command?->warn('DemoDataSeeder: No hay usuarios o establecimientos. Se omite.');

            return;
        }

        $services = RepairService::query()->take(5)->get();
        $parts = Part::query()->take(5)->get();
        $substages = WorkOrderSubstage::query()->where('is_active', true)->take(3)->get();
        $brand = Brand::query()->first();
        $model = VehicleModel::query()->where('brand_id', $brand?->id)->first() ?? VehicleModel::query()->first();
        $series = app(DocumentSeriesService::class);
        $estimateService = app(EstimateService::class);
        $woService = app(WorkOrderService::class);
        $qcTemplate = FormTemplate::resolveFor($establishment->id, FormTemplate::TYPE_QUALITY_CONTROL);
        $surveyTemplate = FormTemplate::resolveFor($establishment->id, FormTemplate::TYPE_SATISFACTION_SURVEY);

        $firstNames = ['Juan', 'María', 'Carlos', 'Lucía', 'Pedro', 'Ana', 'Luis', 'Rosa', 'Jorge', 'Claudia', 'Miguel', 'Sofía', 'Raúl', 'Carmen', 'Diego', 'Valeria', 'Fernando', 'Andrea', 'Gustavo', 'Paola'];
        $lastNames = ['Pérez', 'Gómez', 'Ramírez', 'Torres', 'Huamán', 'Flores', 'Vargas', 'Rojas', 'Castillo', 'Quispe', 'Mendoza', 'Salazar', 'Chávez', 'Díaz', 'Córdova', 'Espinoza', 'Navarro', 'Ríos', 'Palacios', 'Medina'];

        // ---- Partidos (clientes) ----
        $partyIds = [];
        for ($i = 0; $i < self::COUNT; $i++) {
            $party = Party::firstOrCreate(
                ['document_number' => '7' . str_pad((string) (70000000 + $i), 8, '0', STR_PAD_LEFT)],
                [
                    'document_type' => '1',
                    'first_name' => $firstNames[$i % 20],
                    'last_name' => $lastNames[$i % 20] . ' ' . $lastNames[($i + 5) % 20],
                    'email' => 'demo' . ($i + 1) . '@example.com',
                    'mobile' => '9' . str_pad((string) (10000000 + $i), 8, '0', STR_PAD_LEFT),
                    'address' => 'Av. Demo ' . ($i + 1) . ', Lima',
                    'ubigeo_code' => '150101',
                ]
            );
            $partyIds[] = $party->id;
        }

        // ---- Vehículos + contactos (owner/approver) ----
        $vehicleIds = [];
        for ($i = 0; $i < self::COUNT; $i++) {
            $vehicle = Vehicle::firstOrCreate(
                ['plate' => 'DEM' . str_pad((string) ($i + 1), 3, '0', STR_PAD_LEFT)],
                [
                    'brand_id' => $brand?->id,
                    'model_id' => $model?->id,
                    'body_type' => 'sedan',
                    'color' => ['Blanco', 'Negro', 'Gris', 'Azul', 'Rojo'][$i % 5],
                    'vin' => 'DEMO' . str_pad((string) ($i + 1), 11, '0', STR_PAD_LEFT),
                    'engine_number' => 'E' . str_pad((string) ($i + 1), 8, '0', STR_PAD_LEFT),
                    'year' => 2015 + ($i % 8),
                    'access_token' => Vehicle::generateAccessToken(),
                    'access_token_created_at' => now(),
                ]
            );
            $vehicleIds[] = $vehicle->id;

            VehicleRelationship::firstOrCreate(
                ['vehicle_id' => $vehicle->id, 'party_id' => $partyIds[$i], 'role' => 'owner'],
                ['is_primary_commercial' => true, 'notes' => 'Cliente demo.']
            );
        }

        // ---- Inventarios (check-ins) aprobados ----
        $checkInIds = [];
        for ($i = 0; $i < self::COUNT; $i++) {
            $res = $series->getNextNumber($establishment->id, 'IV');

            $checkIn = CheckIn::create([
                'vehicle_id' => $vehicleIds[$i],
                'client_id' => $partyIds[$i],
                'insurance_company_id' => null,
                'establishment_id' => $establishment->id,
                'document_series_id' => $res['series']->id,
                'document_type_code' => $res['document_type_code'],
                'document_serie' => $res['series']->prefix_serie,
                'document_number' => $res['number'],
                'document_sn' => $res['sn'],
                'created_by' => $user->id,
                'updated_by' => $user->id,
                'service_type' => ['siniestro', 'preventivo', 'correctivo'][$i % 3],
                'claim_number' => $i % 3 === 0 ? 'SIN-DEMO-' . ($i + 1) : null,
                'mileage' => 30000 + ($i * 1000),
                'fuel_level' => ['reserva', 'cuarto', 'medio'][$i % 3],
                'property_card' => 'fisica',
                'keys_count' => 1 + ($i % 2),
                'has_remote_control' => $i % 2 === 0,
                'client_request' => $i % 4 === 0 ? 'Reparar avería reportada por el cliente.' : null,
                'observations' => 'Vehículo demo ingresado para reparación.',
                'status' => 'approved',
            ]);
            $checkInIds[] = $checkIn->id;
        }

        // ---- Presupuestos aprobados por el cliente ----
        $estimateIds = [];
        for ($i = 0; $i < self::COUNT; $i++) {
            $checkIn = CheckIn::find($checkInIds[$i]);

            $estimate = $estimateService->create([
                'check_in_id' => $checkIn->id,
                'vehicle_id' => $checkIn->vehicle_id,
                'client_id' => $checkIn->client_id,
                'insurance_company_id' => $checkIn->insurance_company_id,
                'claim_number' => $checkIn->claim_number,
                'service_type' => $checkIn->service_type,
                'establishment_id' => $establishment->id,
                'advisor_id' => $user->id,
                'work_days' => 3 + ($i % 5),
                'contact_name' => 'Cliente demo',
                'contact_phone' => null,
                'contact_email' => null,
                'hourly_rate' => $establishment->default_hourly_rate ?? 0,
                'panel_rate' => $establishment->default_panel_rate ?? 0,
                'currency' => $establishment->base_currency ?? 'PEN',
                'items' => [
                    $this->serviceItem($services[0] ?? null),
                    $this->partItem($parts[0] ?? null),
                ],
            ]);

            // draft → sent_client → approved_client (registra historial)
            $estimateService->changeStatus($estimate, 'sent_client');
            $estimateService->changeStatus($estimate, 'approved_client');

            $estimateIds[] = $estimate->id;
        }


        // ---- Órdenes de trabajo en distintos estados ----
        $statuses = ['in_progress', 'quality_control', 'ready_for_delivery', 'delivered', 'closed', 'waiting_parts', 'delivered_pending', 'open'];
        $paths = [
            'open' => [],
            'in_progress' => ['in_progress'],
            'waiting_parts' => ['in_progress', 'waiting_parts'],
            'quality_control' => ['in_progress', 'quality_control'],
            'ready_for_delivery' => ['in_progress', 'quality_control', 'ready_for_delivery'],
            'delivered' => ['in_progress', 'quality_control', 'ready_for_delivery', 'delivered'],
            'delivered_pending' => ['in_progress', 'delivered_pending'],
            'closed' => ['in_progress', 'quality_control', 'ready_for_delivery', 'delivered', 'closed'],
        ];

        foreach ($estimateIds as $i => $estimateId) {
            $status = $statuses[$i % count($statuses)];

            $workOrder = $woService->createFromEstimates(collect([\App\Models\Estimate::find($estimateId)]), ['start_date' => now()->subDays(5)]);

            foreach ($paths[$status] as $step) {
                $workOrder = $woService->changeStatus($workOrder, $step);
            }

            if (in_array($status, ['delivered', 'closed'], true)) {
                $workOrder->update(['delivered_at' => now()->subDay(), 'delivered_by' => $user->id]);
            }

            if (in_array($status, ['ready_for_delivery', 'delivered', 'closed'], true)) {
                $this->createApprovedQc($workOrder, $qcTemplate, $user);
            }

            if (in_array($status, ['in_progress', 'quality_control', 'waiting_parts'], true) && $substages->isNotEmpty()) {
                foreach ($substages as $si => $substage) {
                    WorkOrderAssignment::create([
                        'work_order_id' => $workOrder->id,
                        'substage_id' => $substage->id,
                        'user_id' => $technician->id,
                        'hours' => 2 + $si,
                        'cost' => 50 + ($si * 25),
                        'status' => $status === 'quality_control' ? 'done' : ($si === 0 ? 'done' : 'in_progress'),
                        'created_by' => $user->id,
                        'updated_by' => $user->id,
                    ]);
                }
            }

            if ($status === 'closed' && $surveyTemplate) {
                $answers = $this->surveyAnswers($surveyTemplate);
                $workOrder->satisfactionSurvey()->create([
                    'form_template_id' => $surveyTemplate->id,
                    'answers' => $answers,
                    'respondent_name' => 'Cliente demo',
                    'ip_address' => '127.0.0.1',
                    'responded_at' => now()->subDay(),
                ]);
                $workOrder->update([
                    'survey_sent_at' => now()->subDay(),
                    'survey_sent_to' => 'Cliente demo',
                    'survey_sent_to_phone' => '9' . str_pad((string) (10000000 + $i), 8, '0', STR_PAD_LEFT),
                ]);
            }
        }

        $this->command?->info('DemoDataSeeder: ' . self::COUNT . ' partidos, vehículos, inventarios, presupuestos y OTs creados.');
    }

    protected function serviceItem(?RepairService $service): array
    {
        return [
            'service_id' => $service?->id,
            'description' => $service?->name ?? 'Servicio de reparación general',
            'quantity' => 1,
            'unit_price' => $service?->sell_price ?? 120.0,
            'discount_pct' => 0,
            'supply_source' => 'internal',
            'cost_price' => $service?->cost_price ?? 0,
        ];
    }

    protected function partItem(?Part $part): array
    {
        return [
            'part_id' => $part?->id,
            'description' => $part?->name ?? 'Repuesto genérico',
            'quantity' => 1,
            'unit_price' => $part?->sell_price ?? 80.0,
            'discount_pct' => 0,
            'supply_source' => 'internal',
            'cost_price' => $part?->cost_price ?? 0,
        ];
    }

    protected function createApprovedQc(WorkOrder $workOrder, ?FormTemplate $template, User $user): void
    {
        if (! $template) {
            return;
        }

        $answers = [];
        foreach ($template->sections as $section) {
            foreach ($section->items as $item) {
                $answers[$item->key] = match ($item->type) {
                    'checkbox' => true,
                    'select' => 'half',
                    'number' => '50000',
                    'radio' => $item->options[0]['value'] ?? '',
                    default => 'Aprobado en revisión demo.',
                };
            }
        }

        WorkOrderQualityControl::create([
            'work_order_id' => $workOrder->id,
            'form_template_id' => $template->id,
            'result' => WorkOrderQualityControl::RESULT_APPROVED,
            'answers' => $answers,
            'reviewed_by' => $user->id,
            'reviewed_at' => now()->subDay(),
            'created_by' => $user->id,
            'updated_by' => $user->id,
        ]);
    }

    protected function surveyAnswers(FormTemplate $template): array
    {
        $answers = [];
        foreach ($template->sections as $section) {
            foreach ($section->items as $item) {
                $answers[$item->key] = $item->options[0]['value'] ?? 'ok';
            }
        }

        return $answers;
    }
}

