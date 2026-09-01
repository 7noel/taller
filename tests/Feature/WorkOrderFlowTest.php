<?php

namespace Tests\Feature;

use App\Models\CheckIn;
use App\Models\DocumentSeries;
use App\Models\DocumentType;
use App\Models\Establishment;
use App\Models\Estimate;
use App\Models\Party;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\WorkOrder;
use App\Models\WorkOrderSubstage;
use App\Services\CheckInService;
use App\Services\EstimateService;
use App\Services\WorkOrderService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use RuntimeException;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class WorkOrderFlowTest extends TestCase
{
    use RefreshDatabase;

    private WorkOrderService $service;
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

        // Series requeridas por los servicios de numeración (PRE y OT).
        $this->createDocumentSeries('PRE', 'Presupuesto', 'PRE01');
        $this->createDocumentSeries('OT', 'Orden de Trabajo', 'OT01');

        $this->service = app(WorkOrderService::class);
    }

    private function createDocumentSeries(string $code, string $name, string $prefix): void
    {
        $documentType = DocumentType::create([
            'code' => $code,
            'name' => $name,
            'is_electronic' => false,
            'is_active' => true,
        ]);

        DocumentSeries::create([
            'establishment_id' => $this->establishment->id,
            'document_type_id' => $documentType->id,
            'prefix_serie' => $prefix,
            'current_number' => 0,
            'number_source' => 'LOCAL',
            'status' => true,
        ]);
    }

    protected function createUserWithPermissions(array $permissions): User
    {
        $user = User::factory()->create(['establishment_id' => $this->establishment->id]);

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        $role = Role::firstOrCreate(['name' => 'WorkOrder Test Role']);
        $role->syncPermissions($permissions);
        $user->assignRole($role);

        return $user;
    }

    /**
     * Crea un presupuesto aprobado por el cliente (flujo real vía EstimateService).
     */
    private function makeApprovedEstimate(?CheckIn $checkIn = null, ?Vehicle $vehicle = null, ?Party $client = null): Estimate
    {
        $vehicle = $vehicle ?? Vehicle::factory()->create();
        $client = $client ?? Party::factory()->create();

        $estimate = app(EstimateService::class)->create([
            'vehicle_id' => $vehicle->id,
            'client_id' => $client->id,
            'service_type' => 'preventivo',
            'establishment_id' => $this->establishment->id,
            'check_in_id' => $checkIn?->id,
            'items' => [
                ['description' => 'Mano de obra', 'quantity' => 1, 'unit_price' => 100],
            ],
        ]);

        $estimate->update(['status' => 'approved_client']);

        return $estimate->fresh();
    }

    private function makeCheckIn(Vehicle $vehicle, Party $client): CheckIn
    {
        return CheckIn::create([
            'vehicle_id' => $vehicle->id,
            'client_id' => $client->id,
            'establishment_id' => $this->establishment->id,
            'service_type' => 'preventivo',
            'status' => 'approved',
            'created_by' => $this->user->id,
        ]);
    }

    public function test_service_creates_work_order_from_approved_estimate(): void
    {
        $estimate = $this->makeApprovedEstimate();

        $workOrder = $this->service->createFromEstimates(collect([$estimate]));

        $this->assertSame('OT01-000001', $workOrder->document_sn);
        $this->assertSame('open', $workOrder->status);
        $this->assertSame($estimate->vehicle_id, $workOrder->vehicle_id);

        $estimate = $estimate->fresh();
        $this->assertSame('in_repair', $estimate->status);
        $this->assertSame($workOrder->id, $estimate->work_order_id);
        $this->assertDatabaseHas('status_histories', [
            'subject_type' => \App\Models\Estimate::class,
            'subject_id' => $estimate->id,
            'from_status' => 'approved_client',
            'to_status' => 'in_repair',
        ]);
    }

    public function test_service_groups_estimates_of_same_check_in_in_one_work_order(): void
    {
        $vehicle = Vehicle::factory()->create();
        $client = Party::factory()->create();
        $checkIn = $this->makeCheckIn($vehicle, $client);

        $estimateA = $this->makeApprovedEstimate($checkIn, $vehicle, $client);
        $estimateB = $this->makeApprovedEstimate($checkIn, $vehicle, $client);

        $workOrder = $this->service->createFromEstimates(collect([$estimateA, $estimateB]));

        $this->assertSame('OT01-000001', $workOrder->document_sn);
        $this->assertCount(2, $workOrder->estimates);
        $this->assertSame('in_repair', $estimateA->fresh()->status);
        $this->assertSame('in_repair', $estimateB->fresh()->status);
        $this->assertSame($workOrder->id, $checkIn->fresh()->work_order_id);
    }
    public function test_service_rejects_not_approved_estimate(): void
    {
        $vehicle = Vehicle::factory()->create();
        $client = Party::factory()->create();
        $estimate = app(EstimateService::class)->create([
            'vehicle_id' => $vehicle->id,
            'client_id' => $client->id,
            'service_type' => 'preventivo',
            'establishment_id' => $this->establishment->id,
            'items' => [
                ['description' => 'Mano de obra', 'quantity' => 1, 'unit_price' => 100],
            ],
        ]);

        $this->expectException(RuntimeException::class);
        $this->service->createFromEstimates(collect([$estimate]));
    }

    public function test_service_rejects_estimate_already_linked_to_work_order(): void
    {
        $estimate = $this->makeApprovedEstimate();
        $this->service->createFromEstimates(collect([$estimate]));

        $this->expectException(RuntimeException::class);
        $this->service->createFromEstimates(collect([$estimate->fresh()]));
    }

    public function test_service_rejects_estimates_from_different_vehicles(): void
    {
        $estimateA = $this->makeApprovedEstimate();
        $estimateB = $this->makeApprovedEstimate();

        $this->expectException(RuntimeException::class);
        $this->service->createFromEstimates(collect([$estimateA, $estimateB]));
    }

    public function test_service_rejects_empty_estimates(): void
    {
        $this->expectException(RuntimeException::class);
        $this->service->createFromEstimates(collect([]));
    }

    public function test_attach_estimate_to_existing_work_order(): void
    {
        $estimateA = $this->makeApprovedEstimate();
        $workOrder = $this->service->createFromEstimates(collect([$estimateA]));

        // Adicional aprobado del mismo vehículo (mismo chasis, otro cliente de facto).
        $estimateB = $this->makeApprovedEstimate(null, $workOrder->vehicle);

        $this->service->attachEstimate($workOrder, $estimateB);

        $this->assertSame($workOrder->id, $estimateB->fresh()->work_order_id);
        $this->assertSame('in_repair', $estimateB->fresh()->status);
    }

    public function test_attach_rejects_estimate_of_another_vehicle(): void
    {
        $estimateA = $this->makeApprovedEstimate();
        $workOrder = $this->service->createFromEstimates(collect([$estimateA]));

        $estimateB = $this->makeApprovedEstimate();

        $this->expectException(RuntimeException::class);
        $this->service->attachEstimate($workOrder, $estimateB);
    }

    public function test_detach_estimate_reverts_to_approved(): void
    {
        $estimate = $this->makeApprovedEstimate();
        $workOrder = $this->service->createFromEstimates(collect([$estimate]));

        $this->service->detachEstimate($workOrder, $estimate->fresh());

        $fresh = $estimate->fresh();
        $this->assertNull($fresh->work_order_id);
        $this->assertSame('approved_client', $fresh->status);
    }

    public function test_valid_status_transitions_flow(): void
    {
        $estimate = $this->makeApprovedEstimate();
        $workOrder = $this->service->createFromEstimates(collect([$estimate]));

        $this->assertSame('open', $workOrder->status);

        foreach (['in_progress', 'waiting_parts', 'quality_control', 'ready_for_delivery', 'delivered', 'closed'] as $status) {
            $workOrder = $this->service->changeStatus($workOrder, $status);
            $this->assertSame($status, $workOrder->status);
        }

        $this->assertTrue($workOrder->is_final);
    }

    public function test_invalid_status_transition_throws(): void
    {
        $estimate = $this->makeApprovedEstimate();
        $workOrder = $this->service->createFromEstimates(collect([$estimate]));

        // 'open' no permite saltar directo a control de calidad.
        $this->expectException(RuntimeException::class);
        $this->service->changeStatus($workOrder, 'quality_control');
    }

    public function test_delivered_pending_keeps_work_order_open(): void
    {
        $estimate = $this->makeApprovedEstimate();
        $workOrder = $this->service->createFromEstimates(collect([$estimate]));

        $workOrder = $this->service->changeStatus($workOrder, 'in_progress');
        $workOrder = $this->service->changeStatus($workOrder, 'delivered_pending');

        $this->assertSame('delivered_pending', $workOrder->status);
        $this->assertFalse($workOrder->is_final);

        // El vehículo regresa a completar el trabajo pendiente: se retoma la misma OT.
        $workOrder = $this->service->changeStatus($workOrder, 'in_progress');
        $this->assertSame('in_progress', $workOrder->status);
    }
    public function test_delete_reverts_estimates_and_unlinks_check_in(): void
    {
        $vehicle = Vehicle::factory()->create();
        $client = Party::factory()->create();
        $checkIn = $this->makeCheckIn($vehicle, $client);
        $estimate = $this->makeApprovedEstimate($checkIn, $vehicle, $client);

        $workOrder = $this->service->createFromEstimates(collect([$estimate]));
        $this->assertSame($workOrder->id, $checkIn->fresh()->work_order_id);

        $this->assertTrue($this->service->delete($workOrder));

        $fresh = $estimate->fresh();
        $this->assertNull($fresh->work_order_id);
        $this->assertSame('approved_client', $fresh->status);
        $this->assertNull($checkIn->fresh()->work_order_id);
        $this->assertSoftDeleted('work_orders', ['id' => $workOrder->id]);
    }

    public function test_assignment_flow(): void
    {
        $estimate = $this->makeApprovedEstimate();
        $workOrder = $this->service->createFromEstimates(collect([$estimate]));

        $substage = WorkOrderSubstage::create(['name' => 'Mecánica', 'order' => 1]);
        $assignment = $this->service->addAssignment($workOrder, [
            'substage_id' => $substage->id,
            'user_id' => $this->user->id,
            'hours' => 2,
            'cost' => 150,
        ]);

        $this->assertSame('pending', $assignment->status);
        $this->assertSame(2.0, (float) $assignment->hours);
        $this->assertSame(150.0, (float) $assignment->cost);

        $assignment = $this->service->updateAssignmentStatus($workOrder, $assignment, 'in_progress');
        $this->assertSame('in_progress', $assignment->status);

        $assignment = $this->service->updateAssignmentStatus($workOrder, $assignment, 'done');
        $this->assertSame('done', $assignment->status);
    }

    // ===== Rutas HTTP =====

    public function test_store_generates_work_order_from_check_in(): void
    {
        $user = $this->createUserWithPermissions(['crear órdenes de trabajo', 'ver órdenes de trabajo']);
        $this->actingAs($user);

        $vehicle = Vehicle::factory()->create();
        $client = Party::factory()->create();
        $checkIn = $this->makeCheckIn($vehicle, $client);
        $estimate = $this->makeApprovedEstimate($checkIn, $vehicle, $client);

        $response = $this->post(route('work-orders.store'), ['check_in_id' => $checkIn->id]);

        $response->assertRedirect();
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('work_orders', ['document_sn' => 'OT01-000001']);
        $this->assertSame('in_repair', $estimate->fresh()->status);
    }

    public function test_store_generates_work_order_from_estimate(): void
    {
        $user = $this->createUserWithPermissions(['crear órdenes de trabajo', 'ver órdenes de trabajo']);
        $this->actingAs($user);

        $estimate = $this->makeApprovedEstimate();

        $response = $this->post(route('work-orders.store'), ['estimate_id' => $estimate->id]);

        $response->assertRedirect();
        $this->assertDatabaseHas('work_orders', ['document_sn' => 'OT01-000001']);
        $this->assertSame('in_repair', $estimate->fresh()->status);
    }

    public function test_store_requires_permission(): void
    {
        $estimate = $this->makeApprovedEstimate();

        $response = $this->post(route('work-orders.store'), ['estimate_id' => $estimate->id]);

        $response->assertForbidden();
    }

    public function test_attach_estimate_route(): void
    {
        $user = $this->createUserWithPermissions(['crear órdenes de trabajo', 'editar órdenes de trabajo']);
        $this->actingAs($user);

        $estimateA = $this->makeApprovedEstimate();
        $workOrder = $this->service->createFromEstimates(collect([$estimateA]));
        $estimateB = $this->makeApprovedEstimate(null, $workOrder->vehicle);

        $response = $this->post(route('work-orders.attach-estimate', $workOrder), ['estimate_id' => $estimateB->id]);

        $response->assertRedirect();
        $response->assertSessionHas('success');
        $this->assertSame($workOrder->id, $estimateB->fresh()->work_order_id);
    }

    public function test_transition_route(): void
    {
        $user = $this->createUserWithPermissions(['crear órdenes de trabajo', 'editar órdenes de trabajo']);
        $this->actingAs($user);

        $estimate = $this->makeApprovedEstimate();
        $workOrder = $this->service->createFromEstimates(collect([$estimate]));

        $response = $this->post(route('work-orders.transition', $workOrder), ['status' => 'in_progress']);

        $response->assertRedirect();
        $response->assertSessionHas('success');
        $this->assertSame('in_progress', $workOrder->fresh()->status);
    }

    public function test_destroy_route(): void
    {
        $user = $this->createUserWithPermissions(['crear órdenes de trabajo', 'eliminar órdenes de trabajo']);
        $this->actingAs($user);

        $estimate = $this->makeApprovedEstimate();
        $workOrder = $this->service->createFromEstimates(collect([$estimate]));

        $response = $this->delete(route('work-orders.destroy', $workOrder));

        $response->assertRedirect(route('work-orders.index'));
        $response->assertSessionHas('success');
        $this->assertSoftDeleted('work_orders', ['id' => $workOrder->id]);
        $this->assertSame('approved_client', $estimate->fresh()->status);
    }

    public function test_reentry_check_in_links_to_work_order_and_resumes_it(): void
    {
        $this->createDocumentSeries('IV', 'Inventario', 'IV01');

        $vehicle = Vehicle::factory()->create();
        $client = Party::factory()->create();
        $checkIn = $this->makeCheckIn($vehicle, $client);
        $estimate = $this->makeApprovedEstimate($checkIn, $vehicle, $client);

        $workOrder = $this->service->createFromEstimates(collect([$estimate]));
        $workOrder = $this->service->changeStatus($workOrder, 'in_progress');
        $workOrder = $this->service->changeStatus($workOrder, 'delivered_pending');

        // El vehículo regresa: se registra un nuevo inventario vinculado a la OT.
        $reentry = app(CheckInService::class)->create([
            'vehicle_id' => $vehicle->id,
            'client_id' => $client->id,
            'service_type' => 'preventivo',
            'establishment_id' => $this->establishment->id,
            'work_order_id' => $workOrder->id,
        ]);

        $this->assertSame($workOrder->id, $reentry->fresh()->work_order_id);
        $this->assertSame('in_progress', $workOrder->fresh()->status);
        $this->assertDatabaseHas('status_histories', [
            'subject_type' => WorkOrder::class,
            'subject_id' => $workOrder->id,
            'from_status' => 'delivered_pending',
            'to_status' => 'in_progress',
        ]);
    }

    public function test_reentry_rejects_work_order_of_another_vehicle(): void
    {
        $this->createDocumentSeries('IV', 'Inventario', 'IV01');

        $estimate = $this->makeApprovedEstimate();
        $workOrder = $this->service->createFromEstimates(collect([$estimate]));
        $workOrder = $this->service->changeStatus($workOrder, 'in_progress');
        $workOrder = $this->service->changeStatus($workOrder, 'delivered_pending');

        $otherVehicle = Vehicle::factory()->create();

        $this->expectException(RuntimeException::class);
        app(CheckInService::class)->create([
            'vehicle_id' => $otherVehicle->id,
            'client_id' => Party::factory()->create()->id,
            'service_type' => 'preventivo',
            'establishment_id' => $this->establishment->id,
            'work_order_id' => $workOrder->id,
        ]);
    }

    public function test_reentry_options_route_filters_by_vehicle(): void
    {
        $user = $this->createUserWithPermissions(['ver órdenes de trabajo']);
        $this->actingAs($user);

        $estimate = $this->makeApprovedEstimate();
        $workOrder = $this->service->createFromEstimates(collect([$estimate]));
        $workOrder = $this->service->changeStatus($workOrder, 'in_progress');
        $workOrder = $this->service->changeStatus($workOrder, 'delivered_pending');

        $response = $this->getJson(route('api.work-orders.reentry-options', ['vehicle_id' => $workOrder->vehicle_id]));

        $response->assertOk()
            ->assertJsonCount(1)
            ->assertJsonFragment(['id' => $workOrder->id]);
    }
}
