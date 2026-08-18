<?php

namespace Tests\Feature;

use App\Models\Brand;
use App\Models\Party;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\VehicleModel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class VehicleTest extends TestCase
{
    use RefreshDatabase;

    private VehicleModel $model;

    protected function setUp(): void
    {
        parent::setUp();
        $brand = Brand::create(['name' => 'TOYOTA']);
        $this->model = VehicleModel::create(['brand_id' => $brand->id, 'name' => 'COROLLA']);
    }

    protected function createUserWithPermissions(array $permissions): User
    {
        $user = User::factory()->create();
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }
        $role = Role::firstOrCreate(['name' => 'Vehicle Test Role']);
        $role->syncPermissions($permissions);
        $user->assignRole($role);

        return $user;
    }

    public function test_vehicle_can_be_created_with_relationships(): void
    {
        $user = $this->createUserWithPermissions(['ver vehículos', 'crear vehículos']);
        $owner = Party::factory()->create();

        $response = $this->actingAs($user)->post(route('vehicles.store'), [
            'plate' => 'ABC123',
            'brand_id' => $this->model->brand_id,
            'model_id' => $this->model->id,
            'relationships' => [
                ['party_id' => $owner->id, 'role' => 'owner', 'is_primary_commercial' => 1, 'notes' => 'Dueño'],
            ],
        ]);

        $response->assertRedirect(route('vehicles.index'));
        $this->assertDatabaseHas('vehicles', ['plate' => 'ABC123', 'model_id' => $this->model->id]);
        $this->assertDatabaseHas('vehicle_relationships', ['role' => 'owner', 'is_primary_commercial' => 1]);
    }

    public function test_vehicle_requires_unique_plate(): void
    {
        $user = $this->createUserWithPermissions(['crear vehículos']);

        Vehicle::factory()->create(['plate' => 'XYZ999', 'brand_id' => $this->model->brand_id, 'model_id' => $this->model->id]);

        $this->actingAs($user)->post(route('vehicles.store'), [
            'plate' => 'XYZ999', 'brand_id' => $this->model->brand_id, 'model_id' => $this->model->id,
        ])->assertSessionHasErrors('plate');
    }

    public function test_vehicle_can_be_updated(): void
    {
        $user = $this->createUserWithPermissions(['ver vehículos', 'editar vehículos']);
        $vehicle = Vehicle::factory()->create(['brand_id' => $this->model->brand_id, 'model_id' => $this->model->id]);

        $this->actingAs($user)->put(route('vehicles.update', $vehicle), [
            'plate' => $vehicle->plate, 'brand_id' => $this->model->brand_id, 'model_id' => $this->model->id,
        ])->assertRedirect(route('vehicles.index'));

        $this->assertDatabaseHas('vehicles', ['id' => $vehicle->id, 'model_id' => $this->model->id]);
    }

    public function test_vehicle_can_be_deleted(): void
    {
        $user = $this->createUserWithPermissions(['ver vehículos', 'eliminar vehículos']);
        $vehicle = Vehicle::factory()->create(['brand_id' => $this->model->brand_id, 'model_id' => $this->model->id]);

        $this->actingAs($user)->delete(route('vehicles.destroy', $vehicle))->assertRedirect(route('vehicles.index'));
        $this->assertSoftDeleted('vehicles', ['id' => $vehicle->id]);
    }
}