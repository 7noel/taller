<?php

namespace Tests\Unit;

use App\Models\Brand;
use App\Models\CheckIn;
use App\Models\CheckInChecklistItem;
use App\Models\Establishment;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\VehicleModel;
use App\Services\CheckInService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CheckInServiceTest extends TestCase
{
    use RefreshDatabase;

    private CheckInService $service;
    private User $user;
    private Establishment $establishment;
    private Vehicle $vehicle;
    private CheckInChecklistItem $item1;

    protected function setUp(): void
    {
        parent::setUp();

        $brand = Brand::create(['name' => 'TOYOTA']);
        $model = VehicleModel::create(['brand_id' => $brand->id, 'name' => 'COROLLA']);

        $this->establishment = Establishment::create([
            'name' => 'Taller Central',
            'address' => 'Av. Principal 123',
            'phone' => '123456789',
            'email' => 'contacto@taller.com',
            'code' => 'TC001',
        ]);

        $this->user = User::factory()->create(['establishment_id' => $this->establishment->id]);
        $this->actingAs($this->user);

        $this->vehicle = Vehicle::factory()->create([
            'brand_id' => $brand->id,
            'model_id' => $model->id,
            'plate' => 'ABC123',
        ]);

        $this->item1 = CheckInChecklistItem::create(['name' => 'LLANTAS', 'category' => 'EXTERIOR', 'order' => 1]);

        $this->service = app(CheckInService::class);
    }

    public function test_service_creates_check_in_with_establishment_and_audit(): void
    {
        $checkIn = $this->service->create([
            'vehicle_id' => $this->vehicle->id,
            'service_type' => 'preventivo',
            'checklist' => [
                $this->item1->id => ['status' => 'good', 'observations' => ''],
            ],
            'damages' => [],
            'contacts' => [],
            'save_contacts' => 0,
        ]);

        $this->assertInstanceOf(CheckIn::class, $checkIn);
        $this->assertEquals($this->establishment->id, $checkIn->establishment_id);
        $this->assertEquals($this->user->id, $checkIn->created_by);
        $this->assertEquals($this->user->id, $checkIn->updated_by);
        $this->assertEquals('draft', $checkIn->status);
        $this->assertDatabaseHas('check_in_checklist_results', [
            'check_in_id' => $checkIn->id,
            'checklist_item_id' => $this->item1->id,
            'status' => 'good',
        ]);
    }

    public function test_service_syncs_damages(): void
    {
        $checkIn = $this->service->create([
            'vehicle_id' => $this->vehicle->id,
            'service_type' => 'siniestro',
            'damages' => [
                ['damage_type' => 'dent', 'side' => 'left', 'pos_x' => 30, 'pos_y' => 50, 'notes' => 'Abolladura'],
            ],
            'checklist' => [],
            'contacts' => [],
            'save_contacts' => 0,
        ]);

        $this->assertDatabaseHas('check_in_damages', [
            'check_in_id' => $checkIn->id,
            'damage_type' => 'dent',
            'side' => 'left',
            'pos_x' => 30,
            'pos_y' => 50,
        ]);
    }

    public function test_service_approves_and_rejects(): void
    {
        $user = $this->user;
        $checkIn = $this->service->create([
            'vehicle_id' => $this->vehicle->id,
            'service_type' => 'siniestro',
            'checklist' => [],
            'damages' => [],
            'contacts' => [],
            'save_contacts' => 0,
        ]);

        $this->service->sendToClient($checkIn);
        $this->assertEquals('pending_approval', $checkIn->fresh()->status);

        $this->service->approve($checkIn->fresh());
        $this->assertEquals('approved', $checkIn->fresh()->status);
    }

    public function test_service_reject_adds_reason_to_observations(): void
    {
        $checkIn = $this->service->create([
            'vehicle_id' => $this->vehicle->id,
            'service_type' => 'siniestro',
            'observations' => 'Nota inicial',
            'checklist' => [],
            'damages' => [],
            'contacts' => [],
            'save_contacts' => 0,
        ]);

        $this->service->reject($checkIn, 'Cliente desistió');

        $fresh = $checkIn->fresh();
        $this->assertEquals('rejected', $fresh->status);
        $this->assertStringContainsString('Rechazo: Cliente desistió', $fresh->observations);
    }

    public function test_service_delete_soft_deletes(): void
    {
        $checkIn = $this->service->create([
            'vehicle_id' => $this->vehicle->id,
            'service_type' => 'preventivo',
            'checklist' => [],
            'damages' => [],
            'contacts' => [],
            'save_contacts' => 0,
        ]);

        $this->assertTrue($this->service->delete($checkIn));
        $this->assertSoftDeleted('check_ins', ['id' => $checkIn->id]);
    }

    public function test_service_saves_contacts_when_requested(): void
    {
        $this->service->create([
            'vehicle_id' => $this->vehicle->id,
            'service_type' => 'preventivo',
            'checklist' => [],
            'damages' => [],
            'save_contacts' => 1,
            'contacts' => [
                'approver' => ['name' => 'Juan Pérez', 'phone' => '999888777', 'email' => 'juan@example.com'],
                'driver' => ['name' => 'Ana López', 'phone' => '111222333', 'email' => 'ana@example.com'],
                'operator' => ['company' => 'Grúas Lima', 'name' => '', 'phone' => '444555666', 'email' => 'gruas@example.com'],
            ],
        ]);

        $this->assertDatabaseHas('vehicle_relationships', [
            'vehicle_id' => $this->vehicle->id,
            'role' => 'approver',
        ]);
        $this->assertDatabaseHas('vehicle_relationships', [
            'vehicle_id' => $this->vehicle->id,
            'role' => 'driver',
        ]);
        $this->assertDatabaseHas('vehicle_relationships', [
            'vehicle_id' => $this->vehicle->id,
            'role' => 'operator',
        ]);
    }
}