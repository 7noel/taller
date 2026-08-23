<?php

namespace Tests\Feature;

use App\Models\Brand;
use App\Models\CheckIn;
use App\Models\CheckInChecklistItem;
use App\Models\Establishment;
use App\Models\Party;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\VehicleModel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class CheckInTest extends TestCase
{
    use RefreshDatabase;

    private Vehicle $vehicle;
    private Establishment $establishment;

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

        $this->vehicle = Vehicle::factory()->create([
            'brand_id' => $brand->id,
            'model_id' => $model->id,
            'plate' => 'ABC123',
        ]);
    }

    protected function createUserWithPermissions(array $permissions): User
    {
        $user = User::factory()->create(['establishment_id' => $this->establishment->id]);
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }
        $role = Role::firstOrCreate(['name' => 'CheckIn Test Role']);
        $role->syncPermissions($permissions);
        $user->assignRole($role);

        return $user;
    }

    protected function validPayload(): array
    {
        return [
            'vehicle_id' => $this->vehicle->id,
            'service_type' => 'preventivo',
            'mileage' => 50000,
            'fuel_level' => 'medio',
            'property_card' => 'fisica',
            'keys_count' => 2,
            'has_remote_control' => 1,
            'client_request' => 'Revisar frenos',
            'observations' => 'Sin observaciones',
            'checklist' => [],
            'damages' => [],
            'save_contacts' => 0,
            'contacts' => [],
        ];
    }

    public function test_check_in_can_be_created_with_checklist_and_damages(): void
    {
        $user = $this->createUserWithPermissions(['ver inventarios', 'crear inventarios']);

        $item1 = CheckInChecklistItem::create(['name' => 'LLANTAS', 'category' => 'EXTERIOR', 'order' => 1]);
        $item2 = CheckInChecklistItem::create(['name' => 'BATERIA', 'category' => 'MOTOR', 'order' => 2]);

        $response = $this->actingAs($user)->post(route('check-ins.store'), array_merge($this->validPayload(), [
            'claim_number' => null,
            'checklist' => [
                $item1->id => ['status' => 'regular', 'observations' => 'Gastadas'],
                $item2->id => ['status' => 'good', 'observations' => ''],
            ],
            'damages' => [
                ['damage_type' => 'dent', 'side' => 'left', 'pos_x' => 30, 'pos_y' => 50, 'notes' => 'Abolladura'],
            ],
        ]));

        $response->assertRedirect(route('check-ins.index'));
        $this->assertDatabaseHas('check_ins', [
            'vehicle_id' => $this->vehicle->id,
            'service_type' => 'preventivo',
            'establishment_id' => $this->establishment->id,
            'status' => 'draft',
        ]);
        $this->assertDatabaseHas('check_in_checklist_results', ['checklist_item_id' => $item1->id, 'status' => 'regular']);
        $this->assertDatabaseHas('check_in_damages', ['damage_type' => 'dent', 'side' => 'left', 'pos_x' => 30]);
    }

    public function test_check_in_requires_vehicle_and_service_type(): void
    {
        $user = $this->createUserWithPermissions(['ver inventarios', 'crear inventarios']);

        $this->actingAs($user)->post(route('check-ins.store'), [])
            ->assertSessionHasErrors(['vehicle_id', 'service_type']);
    }

    public function test_check_in_rejects_duplicate_open_vehicle(): void
    {
        $user = $this->createUserWithPermissions(['ver inventarios', 'crear inventarios']);

        CheckIn::create([
            'vehicle_id' => $this->vehicle->id,
            'establishment_id' => $this->establishment->id,
            'created_by' => $user->id,
            'service_type' => 'preventivo',
            'status' => 'draft',
        ]);

        $this->actingAs($user)->post(route('check-ins.store'), $this->validPayload())
            ->assertSessionHasErrors('vehicle_id');
    }

    public function test_check_in_can_be_approved(): void
    {
        $user = $this->createUserWithPermissions(['ver inventarios', 'aprobar inventarios']);

        $checkIn = CheckIn::create([
            'vehicle_id' => $this->vehicle->id,
            'establishment_id' => $this->establishment->id,
            'created_by' => $user->id,
            'updated_by' => $user->id,
            'service_type' => 'siniestro',
            'status' => 'draft',
        ]);

        $this->actingAs($user)->post(route('check-ins.approve', $checkIn))
            ->assertRedirect();

        $this->assertDatabaseHas('check_ins', ['id' => $checkIn->id, 'status' => 'approved']);
    }

    public function test_check_in_can_be_rejected(): void
    {
        $user = $this->createUserWithPermissions(['ver inventarios', 'aprobar inventarios']);

        $checkIn = CheckIn::create([
            'vehicle_id' => $this->vehicle->id,
            'establishment_id' => $this->establishment->id,
            'created_by' => $user->id,
            'updated_by' => $user->id,
            'service_type' => 'siniestro',
            'status' => 'pending_approval',
        ]);

        $this->actingAs($user)->post(route('check-ins.reject', $checkIn), ['reason' => 'Cliente desistió'])
            ->assertRedirect();

        $this->assertDatabaseHas('check_ins', ['id' => $checkIn->id, 'status' => 'rejected']);
    }

    public function test_check_in_can_be_sent_to_client(): void
    {
        $user = $this->createUserWithPermissions(['ver inventarios', 'editar inventarios']);

        $checkIn = CheckIn::create([
            'vehicle_id' => $this->vehicle->id,
            'establishment_id' => $this->establishment->id,
            'created_by' => $user->id,
            'updated_by' => $user->id,
            'service_type' => 'preventivo',
            'status' => 'draft',
        ]);

        $this->actingAs($user)->post(route('check-ins.send-to-client', $checkIn))
            ->assertRedirect();

        $this->assertDatabaseHas('check_ins', ['id' => $checkIn->id, 'status' => 'pending_approval']);
    }

    public function test_check_in_can_be_updated(): void
    {
        $user = $this->createUserWithPermissions(['ver inventarios', 'editar inventarios']);

        $checkIn = CheckIn::create([
            'vehicle_id' => $this->vehicle->id,
            'establishment_id' => $this->establishment->id,
            'created_by' => $user->id,
            'updated_by' => $user->id,
            'service_type' => 'siniestro',
            'status' => 'draft',
            'mileage' => 10000,
        ]);

        $this->actingAs($user)->put(route('check-ins.update', $checkIn), array_merge($this->validPayload(), [
            'mileage' => 25000,
            'service_type' => 'correctivo',
        ]))->assertRedirect(route('check-ins.index'));

        $this->assertDatabaseHas('check_ins', ['id' => $checkIn->id, 'mileage' => 25000, 'service_type' => 'correctivo']);
    }

    public function test_check_in_can_be_deleted(): void
    {
        $user = $this->createUserWithPermissions(['ver inventarios', 'eliminar inventarios']);

        $checkIn = CheckIn::create([
            'vehicle_id' => $this->vehicle->id,
            'establishment_id' => $this->establishment->id,
            'created_by' => $user->id,
            'updated_by' => $user->id,
            'service_type' => 'preventivo',
            'status' => 'draft',
        ]);

        $this->actingAs($user)->delete(route('check-ins.destroy', $checkIn))
            ->assertRedirect(route('check-ins.index'));

        $this->assertSoftDeleted('check_ins', ['id' => $checkIn->id]);
    }

    public function test_insurance_company_must_be_valid_insurance(): void
    {
        $user = $this->createUserWithPermissions(['ver inventarios', 'crear inventarios']);

        $regularParty = Party::factory()->person()->create();

        $this->actingAs($user)->post(route('check-ins.store'), array_merge($this->validPayload(), [
            'service_type' => 'siniestro',
            'insurance_company_id' => $regularParty->id,
        ]))->assertSessionHasErrors('insurance_company_id');
    }
}