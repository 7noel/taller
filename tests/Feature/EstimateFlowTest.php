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
}
