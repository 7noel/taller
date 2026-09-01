<?php

namespace Tests\Feature;

use App\Models\DocumentSeries;
use App\Models\DocumentType;
use App\Models\Establishment;
use App\Models\Estimate;
use App\Models\Party;
use App\Models\User;
use App\Models\Vehicle;
use App\Services\EstimateService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use RuntimeException;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class EstimateFlowTest extends TestCase
{
    use RefreshDatabase;

    private EstimateService $service;
    private Establishment $establishment;
    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->establishment = Establishment::create([
            'name' => 'Taller Central',
            'address' => 'Av. Principal 123',
            'phone' => '123456789',
            'email' => 'contacto@taller.com',
            'code' => 'TC001',
        ]);

        $this->user = User::factory()->create(['establishment_id' => $this->establishment->id]);
        $this->actingAs($this->user);

        // El servicio de numeración requiere la serie PRE01 para asignar document_number.
        $documentType = DocumentType::create([
            'code' => 'PRE',
            'name' => 'Presupuesto',
            'is_electronic' => false,
            'is_active' => true,
        ]);
        DocumentSeries::create([
            'establishment_id' => $this->establishment->id,
            'document_type_id' => $documentType->id,
            'prefix_serie' => 'PRE01',
            'current_number' => 0,
            'number_source' => 'LOCAL',
            'status' => true,
        ]);

        $this->service = app(EstimateService::class);
    }

    protected function createUserWithPermissions(array $permissions): User
    {
        $user = User::factory()->create(['establishment_id' => $this->establishment->id]);

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        $role = Role::firstOrCreate(['name' => 'Estimate Test Role']);
        $role->syncPermissions($permissions);
        $user->assignRole($role);

        return $user;
    }

    private function makeEstimate(string $serviceType = 'preventivo', string $status = 'draft'): Estimate
    {
        $vehicle = Vehicle::factory()->create();
        $client = Party::factory()->create();

        $estimate = $this->service->create([
            'vehicle_id' => $vehicle->id,
            'client_id' => $client->id,
            'service_type' => $serviceType,
            'establishment_id' => $this->establishment->id,
            'items' => [
                ['description' => 'Mano de obra', 'quantity' => 1, 'unit_price' => 100],
            ],
        ]);

        $estimate->update(['status' => $status]);

        return $estimate->fresh();
    }

    private function updatePayload(Estimate $estimate): array
    {
        return [
            'vehicle_id' => $estimate->vehicle_id,
            'client_id' => $estimate->client_id,
            'service_type' => $estimate->service_type,
            'establishment_id' => $estimate->establishment_id,
            'items' => [
                ['description' => 'Ítem corregido', 'quantity' => 2, 'unit_price' => 50],
            ],
        ];
    }

    public function test_rejected_estimate_can_be_edited(): void
    {
        foreach (['rejected_insurance', 'rejected_client'] as $status) {
            $estimate = $this->makeEstimate('siniestro', $status);

            $updated = $this->service->update($estimate, $this->updatePayload($estimate));

            $this->assertEquals($status, $updated->status);
            $this->assertSame(2.0, (float) $updated->items()->first()->quantity);
        }
    }

    public function test_finalized_estimate_cannot_be_edited(): void
    {
        $estimate = $this->makeEstimate('preventivo', 'finalized');

        $this->expectException(RuntimeException::class);
        $this->service->update($estimate, $this->updatePayload($estimate));
    }

    public function test_rejected_insurance_can_be_resent_to_insurance(): void
    {
        $estimate = $this->makeEstimate('siniestro', 'rejected_insurance');

        $updated = $this->service->changeStatus($estimate, 'sent_insurance');

        $this->assertEquals('sent_insurance', $updated->status);
    }

    public function test_rejected_client_can_be_resent_to_client(): void
    {
        $estimate = $this->makeEstimate('siniestro', 'rejected_client');

        $updated = $this->service->changeStatus($estimate, 'sent_client');

        $this->assertEquals('sent_client', $updated->status);
    }

    public function test_rejected_states_can_be_reopened_to_draft(): void
    {
        $estimate = $this->makeEstimate('siniestro', 'rejected_insurance');
        $this->assertEquals('draft', $this->service->changeStatus($estimate, 'draft')->status);

        $estimate = $this->makeEstimate('siniestro', 'rejected_client');
        $this->assertEquals('draft', $this->service->changeStatus($estimate, 'draft')->status);
    }

    public function test_insurance_approval_stores_manual_date(): void
    {
        $estimate = $this->makeEstimate('siniestro', 'sent_insurance');

        $updated = $this->service->changeStatus($estimate, 'approved_insurance', null, '2026-08-20');

        $this->assertEquals('approved_insurance', $updated->status);
        $this->assertNotNull($updated->insurance_approved_at);
        $this->assertEquals('2026-08-20', $updated->insurance_approved_at->format('Y-m-d'));
        $this->assertEquals($this->user->id, $updated->insurance_approved_by_user_id);
    }

    public function test_insurance_rejection_stores_date_and_reason(): void
    {
        $estimate = $this->makeEstimate('siniestro', 'sent_insurance');

        $updated = $this->service->changeStatus($estimate, 'rejected_insurance', 'Falta documentación', '2026-08-21');

        $this->assertEquals('rejected_insurance', $updated->status);
        $this->assertEquals('Falta documentación', $updated->insurance_rejection_reason);
        $this->assertEquals('2026-08-21', $updated->insurance_rejected_at->format('Y-m-d'));
    }

    public function test_siniestro_approved_by_insurance_can_be_sent_to_client(): void
    {
        $estimate = $this->makeEstimate('siniestro', 'sent_insurance');
        $estimate = $this->service->changeStatus($estimate, 'approved_insurance', null, '2026-08-20');

        $updated = $this->service->changeStatus($estimate, 'sent_client');

        $this->assertEquals('sent_client', $updated->status);
    }

    // =====================================================
    // Ampliaciones (siniestro + ampliaciones = grupo)
    // =====================================================

    public function test_ampliacion_inherits_currency_and_exchange_rate_from_parent(): void
    {
        $vehicle = Vehicle::factory()->create();
        $client = Party::factory()->create();

        $parent = $this->service->create([
            'vehicle_id' => $vehicle->id,
            'client_id' => $client->id,
            'service_type' => 'siniestro',
            'establishment_id' => $this->establishment->id,
            'currency' => 'USD',
            'exchange_rate' => 3.75,
            'items' => [['description' => 'Mano de obra', 'quantity' => 1, 'unit_price' => 100]],
        ]);

        // Se envía PEN a propósito: la ampliación debe heredar USD del siniestro.
        $ampliacion = $this->service->create([
            'vehicle_id' => $vehicle->id,
            'client_id' => $client->id,
            'service_type' => 'siniestro',
            'establishment_id' => $this->establishment->id,
            'parent_estimate_id' => $parent->id,
            'currency' => 'PEN',
            'exchange_rate' => 1,
            'items' => [['description' => 'Repuesto adicional', 'quantity' => 1, 'unit_price' => 50]],
        ]);

        $this->assertTrue($ampliacion->is_ampliacion);
        $this->assertEquals('USD', $ampliacion->currency);
        $this->assertEquals(3.75, (float) $ampliacion->exchange_rate);
        $this->assertEquals($parent->id, $ampliacion->parent_estimate_id);
    }

    public function test_ampliacion_requires_same_vehicle_as_parent(): void
    {
        $vehicle = Vehicle::factory()->create();
        $client = Party::factory()->create();

        $parent = $this->service->create([
            'vehicle_id' => $vehicle->id,
            'client_id' => $client->id,
            'service_type' => 'siniestro',
            'establishment_id' => $this->establishment->id,
            'items' => [['description' => 'Mano de obra', 'quantity' => 1, 'unit_price' => 100]],
        ]);

        $otherVehicle = Vehicle::factory()->create();

        $this->expectException(RuntimeException::class);
        $this->service->create([
            'vehicle_id' => $otherVehicle->id,
            'client_id' => $client->id,
            'service_type' => 'siniestro',
            'establishment_id' => $this->establishment->id,
            'parent_estimate_id' => $parent->id,
            'items' => [['description' => 'Otro', 'quantity' => 1, 'unit_price' => 10]],
        ]);
    }

    public function test_group_franchise_is_aggregated_on_parent(): void
    {
        $vehicle = Vehicle::factory()->create();
        $client = Party::factory()->create();

        $parent = $this->service->create([
            'vehicle_id' => $vehicle->id,
            'client_id' => $client->id,
            'service_type' => 'siniestro',
            'establishment_id' => $this->establishment->id,
            'franchise_minimum_amount' => 100,
            'franchise_percentage' => 10,
            'items' => [['description' => 'Mano de obra', 'quantity' => 1, 'unit_price' => 100]],
            'third_party_orders' => [
                ['description' => 'Pintura tercero', 'amount_without_iva' => 50],
            ],
        ]);

        $ampliacion = $this->service->create([
            'vehicle_id' => $vehicle->id,
            'client_id' => $client->id,
            'service_type' => 'siniestro',
            'establishment_id' => $this->establishment->id,
            'parent_estimate_id' => $parent->id,
            'items' => [['description' => 'Repuesto adicional', 'quantity' => 2, 'unit_price' => 100]],
            'third_party_orders' => [
                ['description' => 'Chapa tercero', 'amount_without_iva' => 25],
            ],
        ]);

        $parent->refresh();

        // base = 100 (padre) + 50 (OC padre) + 200 (ampliación) + 25 (OC ampliación) = 375
        $this->assertEquals(375.0, round((float) $parent->franchise_base, 2));
        // franquicia = max(100 mínimo, 37.5 aplicado) = 100
        $this->assertEquals(100.0, round((float) $parent->franchise_amount, 2));

        // La ampliación NO lleva franquicia local (vive en el presupuesto principal).
        $ampliacion->refresh();
        $this->assertNull($ampliacion->franchise_amount);
        $this->assertNull($ampliacion->franchise_base);
    }

    public function test_related_billable_includes_group(): void
    {
        $vehicle = Vehicle::factory()->create();
        $client = Party::factory()->create();

        $parent = $this->service->create([
            'vehicle_id' => $vehicle->id,
            'client_id' => $client->id,
            'service_type' => 'siniestro',
            'establishment_id' => $this->establishment->id,
            'items' => [['description' => 'Mano de obra', 'quantity' => 1, 'unit_price' => 100]],
        ]);
        $parent->update(['status' => 'approved_client']);

        $ampliacion = $this->service->create([
            'vehicle_id' => $vehicle->id,
            'client_id' => $client->id,
            'service_type' => 'siniestro',
            'establishment_id' => $this->establishment->id,
            'parent_estimate_id' => $parent->id,
            'items' => [['description' => 'Extra', 'quantity' => 1, 'unit_price' => 50]],
        ]);
        $ampliacion->update(['status' => 'approved_client']);

        $related = $this->service->getRelatedBillable($parent->fresh());
        $ids = array_column($related, 'id');

        $this->assertContains($parent->id, $ids);
        $this->assertContains($ampliacion->id, $ids);
    }

    public function test_client_cannot_approve_siniestro_without_insurance_approval(): void
    {
        $estimate = $this->makeEstimate('siniestro', 'sent_client');

        $this->expectException(RuntimeException::class);
        $this->service->changeStatusByClient($estimate, 'approved_client');
    }

    public function test_client_can_approve_siniestro_after_insurance_approval(): void
    {
        $estimate = $this->makeEstimate('siniestro', 'sent_insurance');
        $estimate = $this->service->changeStatus($estimate, 'approved_insurance', null, '2026-08-20');
        $estimate = $this->service->changeStatus($estimate, 'sent_client');

        $updated = $this->service->changeStatusByClient($estimate, 'approved_client');

        $this->assertEquals('approved_client', $updated->status);
    }

    public function test_siniestro_cannot_be_sent_to_client_from_draft(): void
    {
        $user = $this->createUserWithPermissions(['editar presupuestos', 'aprobar presupuestos']);
        $this->actingAs($user);

        $estimate = $this->makeEstimate('siniestro');

        $response = $this->post(route('estimates.send-to-client', $estimate));

        $response->assertRedirect();
        $this->assertEquals('draft', $estimate->fresh()->status);
        $response->assertSessionHas('error');
    }

    public function test_non_siniestro_can_be_sent_to_client_from_draft(): void
    {
        $user = $this->createUserWithPermissions(['editar presupuestos', 'aprobar presupuestos']);
        $this->actingAs($user);

        $estimate = $this->makeEstimate('preventivo');

        $response = $this->post(route('estimates.send-to-client', $estimate));

        $response->assertRedirect();
        $this->assertEquals('sent_client', $estimate->fresh()->status);
    }

    // =====================================================
    // Moneda: bloqueo, conversión y precios de catálogo
    // =====================================================

    public function test_update_rejects_currency_change_when_items_exist(): void
    {
        $estimate = $this->makeEstimate('preventivo');

        $payload = $this->updatePayload($estimate);
        $payload['currency'] = 'USD';
        $payload['exchange_rate'] = 3.75;

        $this->expectException(RuntimeException::class);
        $this->service->update($estimate, $payload);
    }

    public function test_convert_currency_pen_to_usd_converts_all_amounts(): void
    {
        $vehicle = Vehicle::factory()->create();
        $client = Party::factory()->create();

        $estimate = $this->service->create([
            'vehicle_id' => $vehicle->id,
            'client_id' => $client->id,
            'service_type' => 'siniestro',
            'establishment_id' => $this->establishment->id,
            'hourly_rate' => 60,
            'franchise_minimum_amount' => 100,
            'franchise_percentage' => 10,
            'items' => [
                ['description' => 'Mano de obra', 'quantity' => 1, 'unit_price' => 100],
            ],
            'third_party_orders' => [
                ['description' => 'Pintura tercero', 'amount_without_iva' => 50],
            ],
        ]);

        $converted = $this->service->convertCurrency($estimate, 'USD', 3.75);

        $this->assertEquals('USD', $converted->currency);
        $this->assertEquals(3.75, (float) $converted->exchange_rate);
        // 100 PEN / 3.75 = 26.6667
        $this->assertEquals(26.6667, round((float) $converted->items()->first()->unit_price, 4));
        // 60 PEN / 3.75 = 16.00
        $this->assertEquals(16.0, round((float) $converted->hourly_rate, 2));
        // 50 PEN / 3.75 = 13.3333 → la columna de OC es decimal(12,2): 13.33
        $this->assertEquals(13.33, round((float) $converted->thirdPartyOrders()->first()->amount_without_iva, 2));
        // 100 PEN / 3.75 = 26.6667 → redondeado a 2 en el recálculo de franquicia
        $this->assertEquals(26.67, round((float) $converted->franchise_minimum_amount, 2));
        // Total: subtotal 26.67 + IGV 18% = 31.47
        $this->assertEquals(31.47, round((float) $converted->total, 2));
    }

    public function test_convert_currency_usd_to_pen_multiplies_amounts(): void
    {
        $vehicle = Vehicle::factory()->create();
        $client = Party::factory()->create();

        $estimate = $this->service->create([
            'vehicle_id' => $vehicle->id,
            'client_id' => $client->id,
            'service_type' => 'preventivo',
            'establishment_id' => $this->establishment->id,
            'currency' => 'USD',
            'exchange_rate' => 3.75,
            'items' => [
                ['description' => 'Mano de obra', 'quantity' => 1, 'unit_price' => 10],
            ],
        ]);

        $converted = $this->service->convertCurrency($estimate, 'PEN', 3.75);

        $this->assertEquals('PEN', $converted->currency);
        // 10 USD * 3.75 = 37.50
        $this->assertEquals(37.5, round((float) $converted->items()->first()->unit_price, 4));
        // Total: 37.50 + 18% = 44.25
        $this->assertEquals(44.25, round((float) $converted->total, 2));
    }

    public function test_convert_currency_rejects_non_draft_and_ampliacion(): void
    {
        $estimate = $this->makeEstimate('preventivo', 'sent_client');

        try {
            $this->service->convertCurrency($estimate, 'USD', 3.75);
            $this->fail('Debería rechazar la conversión fuera de borrador.');
        } catch (RuntimeException $e) {
            $this->assertStringContainsString('borrador', $e->getMessage());
        }

        $vehicle = Vehicle::factory()->create();
        $client = Party::factory()->create();
        $parent = $this->service->create([
            'vehicle_id' => $vehicle->id,
            'client_id' => $client->id,
            'service_type' => 'siniestro',
            'establishment_id' => $this->establishment->id,
            'items' => [['description' => 'Mano de obra', 'quantity' => 1, 'unit_price' => 100]],
        ]);
        $ampliacion = $this->service->create([
            'vehicle_id' => $vehicle->id,
            'client_id' => $client->id,
            'service_type' => 'siniestro',
            'establishment_id' => $this->establishment->id,
            'parent_estimate_id' => $parent->id,
            'items' => [['description' => 'Extra', 'quantity' => 1, 'unit_price' => 50]],
        ]);

        $this->expectException(RuntimeException::class);
        $this->service->convertCurrency($ampliacion, 'USD', 3.75);
    }

    public function test_catalog_price_is_converted_to_estimate_currency(): void
    {
        $vehicle = Vehicle::factory()->create();
        $client = Party::factory()->create();

        $part = \App\Models\Part::create([
            'name' => 'Parachoques delantero',
            'sku' => 'PCH-001',
            'sell_price' => 37.5,
            'currency' => 'PEN',
            'cost_price' => 20,
            'cost_currency' => 'PEN',
            'is_active' => true,
        ]);

        $estimate = $this->service->create([
            'vehicle_id' => $vehicle->id,
            'client_id' => $client->id,
            'service_type' => 'siniestro',
            'establishment_id' => $this->establishment->id,
            'currency' => 'USD',
            'exchange_rate' => 3.75,
            'items' => [
                ['part_id' => $part->id, 'quantity' => 1], // sin unit_price: se deriva del catálogo
            ],
        ]);

        // 37.5 PEN / 3.75 = 10.00 USD
        $this->assertEquals(10.0, round((float) $estimate->items()->first()->unit_price, 4));
    }
}
