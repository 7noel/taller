<?php

namespace Tests\Unit;

use App\Models\Brand;
use App\Models\CheckIn;
use App\Models\CheckInChecklistItem;
use App\Models\Establishment;
use App\Models\Party;
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
        ]);

        $this->assertDatabaseHas('check_in_damages', [
            'check_in_id' => $checkIn->id,
            'damage_type' => 'dent',
            'side' => 'left',
            'pos_x' => 30,
            'pos_y' => 50,
        ]);
    }

    public function test_service_updates_damage_preserving_id(): void
    {
        $checkIn = $this->service->create([
            'vehicle_id' => $this->vehicle->id,
            'service_type' => 'siniestro',
            'damages' => [
                ['damage_type' => 'dent', 'side' => 'left', 'pos_x' => 30, 'pos_y' => 50, 'notes' => 'Abolladura'],
            ],
            'checklist' => [],
        ]);

        $damage = $checkIn->damages()->first();

        $this->service->update($checkIn, [
            'vehicle_id' => $this->vehicle->id,
            'service_type' => 'siniestro',
            'damages' => [
                ['id' => $damage->id, 'damage_type' => 'scratch', 'side' => 'right', 'pos_x' => 40, 'pos_y' => 60, 'notes' => 'Cambió'],
            ],
            'checklist' => [],
        ]);

        $fresh = $checkIn->damages()->first();
        $this->assertEquals($damage->id, $fresh->id, 'El ID del daño debe conservarse al actualizar.');
        $this->assertEquals('scratch', $fresh->damage_type);
        $this->assertEquals('right', $fresh->side);
        $this->assertEquals(40, $fresh->pos_x);
        $this->assertEquals(60, $fresh->pos_y);
        $this->assertDatabaseCount('check_in_damages', 1);
    }

    public function test_service_deletes_damages_not_in_request(): void
    {
        $checkIn = $this->service->create([
            'vehicle_id' => $this->vehicle->id,
            'service_type' => 'siniestro',
            'damages' => [
                ['damage_type' => 'dent', 'side' => 'left', 'pos_x' => 30, 'pos_y' => 50, 'notes' => 'Uno'],
                ['damage_type' => 'crack', 'side' => 'right', 'pos_x' => 70, 'pos_y' => 20, 'notes' => 'Dos'],
            ],
            'checklist' => [],
        ]);

        $toKeep = $checkIn->damages()->where('notes', 'Uno')->first();
        $toRemove = $checkIn->damages()->where('notes', 'Dos')->first();

        $this->service->update($checkIn, [
            'vehicle_id' => $this->vehicle->id,
            'service_type' => 'siniestro',
            'damages' => [
                ['id' => $toKeep->id, 'damage_type' => 'dent', 'side' => 'left', 'pos_x' => 30, 'pos_y' => 50, 'notes' => 'Uno'],
            ],
            'checklist' => [],
        ]);

        $this->assertDatabaseHas('check_in_damages', ['id' => $toKeep->id]);
        $this->assertDatabaseMissing('check_in_damages', ['id' => $toRemove->id]);
        $this->assertDatabaseCount('check_in_damages', 1);
    }

    public function test_service_updates_checklist_preserving_row(): void
    {
        $checkIn = $this->service->create([
            'vehicle_id' => $this->vehicle->id,
            'service_type' => 'preventivo',
            'checklist' => [
                $this->item1->id => ['status' => 'good', 'observations' => 'Ok'],
            ],
            'damages' => [],
        ]);

        $row = $checkIn->checklistResults()->where('checklist_item_id', $this->item1->id)->first();

        $this->service->update($checkIn, [
            'vehicle_id' => $this->vehicle->id,
            'service_type' => 'preventivo',
            'checklist' => [
                $this->item1->id => ['status' => 'bad', 'observations' => 'Requiere cambio'],
            ],
            'damages' => [],
        ]);

        $fresh = $checkIn->checklistResults()->where('checklist_item_id', $this->item1->id)->first();
        $this->assertEquals($row->id, $fresh->id, 'El ID del resultado debe conservarse al actualizar.');
        $this->assertEquals('bad', $fresh->status);
        $this->assertEquals('Requiere cambio', $fresh->observations);
        $this->assertDatabaseCount('check_in_checklist_results', 1);
    }

    public function test_service_deletes_checklist_row_when_field_cleared(): void
    {
        $checkIn = $this->service->create([
            'vehicle_id' => $this->vehicle->id,
            'service_type' => 'preventivo',
            'checklist' => [
                $this->item1->id => ['status' => 'good', 'observations' => 'Ok'],
            ],
            'damages' => [],
        ]);

        $this->service->update($checkIn, [
            'vehicle_id' => $this->vehicle->id,
            'service_type' => 'preventivo',
            'checklist' => [
                $this->item1->id => ['status' => '', 'observations' => ''],
            ],
            'damages' => [],
        ]);

        $this->assertDatabaseMissing('check_in_checklist_results', [
            'check_in_id' => $checkIn->id,
            'checklist_item_id' => $this->item1->id,
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
        ]);

        $this->assertTrue($this->service->delete($checkIn));
        $this->assertSoftDeleted('check_ins', ['id' => $checkIn->id]);
    }

    public function test_service_saves_contacts_when_requested(): void
    {
        $approver = Party::create([
            'document_type' => '1',
            'document_number' => Party::generateTemporaryDocumentNumber(),
            'first_name' => 'Juan',
            'last_name' => 'Pérez',
            'mobile' => '999888777',
            'email' => 'juan@example.com',
        ]);
        $driver = Party::create([
            'document_type' => '1',
            'document_number' => Party::generateTemporaryDocumentNumber(),
            'first_name' => 'Ana',
            'last_name' => 'López',
            'mobile' => '111222333',
            'email' => 'ana@example.com',
        ]);
        $operator = Party::create([
            'document_type' => '6',
            'document_number' => '20123456789',
            'business_name' => 'Grúas Lima',
            'mobile' => '444555666',
            'email' => 'gruas@example.com',
        ]);

        $this->service->create([
            'vehicle_id' => $this->vehicle->id,
            'service_type' => 'preventivo',
            'checklist' => [],
            'damages' => [],
            'relationships' => [
                ['party_id' => $approver->id, 'role' => 'approver', 'notes' => null, 'is_primary_commercial' => 0],
                ['party_id' => $driver->id, 'role' => 'driver', 'notes' => null, 'is_primary_commercial' => 0],
                ['party_id' => $operator->id, 'role' => 'operator', 'notes' => null, 'is_primary_commercial' => 0],
            ],
        ]);

        $this->assertDatabaseHas('vehicle_relationships', [
            'vehicle_id' => $this->vehicle->id,
            'role' => 'approver',
            'party_id' => $approver->id,
        ]);
        $this->assertDatabaseHas('vehicle_relationships', [
            'vehicle_id' => $this->vehicle->id,
            'role' => 'driver',
            'party_id' => $driver->id,
        ]);
        $this->assertDatabaseHas('vehicle_relationships', [
            'vehicle_id' => $this->vehicle->id,
            'role' => 'operator',
            'party_id' => $operator->id,
        ]);
    }
}