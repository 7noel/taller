<?php

namespace Tests\Feature;

use App\Models\Party;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class VehicleTest extends TestCase
{
    use RefreshDatabase;

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
        $driver = Party::factory()->create();

        $response = $this->actingAs($user)->post(route('vehicles.store'), [
            'plate' => 'ABC123',
            'brand' => 'Toyota',
            'model' => 'Corolla',
            'body_type' => 'sedan',
            'color' => 'Blanco',
            'year' => 2019,
            'technical_review_reminder_days' => 15,
            'relationships' => [
                ['party_id' => $owner->id, 'role' => 'owner', 'is_primary_commercial' => 1, 'notes' => 'Dueño'],
                ['party_id' => $driver->id, 'role' => 'driver', 'is_primary_commercial' => 0, 'notes' => null],
            ],
        ]);

        $response->assertRedirect(route('vehicles.index'));

        $this->assertDatabaseHas('vehicles', ['plate' => 'ABC123', 'brand' => 'Toyota']);
        $this->assertDatabaseHas('vehicle_relationships', ['role' => 'owner', 'is_primary_commercial' => 1]);
        $this->assertDatabaseHas('vehicle_relationships', ['role' => 'driver', 'is_primary_commercial' => 0]);
    }

    public function test_vehicle_requires_unique_plate(): void
    {
        $user = $this->createUserWithPermissions(['crear vehículos']);

        Vehicle::factory()->create(['plate' => 'XYZ999']);

        $response = $this->actingAs($user)->post(route('vehicles.store'), [
            'plate' => 'XYZ999',
            'brand' => 'Nissan',
            'model' => 'Sentra',
        ]);

        $response->assertSessionHasErrors('plate');
    }

    public function test_vehicle_can_be_updated(): void
    {
        $user = $this->createUserWithPermissions(['ver vehículos', 'editar vehículos']);

        $vehicle = Vehicle::factory()->create();

        $response = $this->actingAs($user)->put(route('vehicles.update', $vehicle), [
            'plate' => $vehicle->plate,
            'brand' => 'Honda',
            'model' => 'Civic',
        ]);

        $response->assertRedirect(route('vehicles.index'));
        $this->assertDatabaseHas('vehicles', ['id' => $vehicle->id, 'brand' => 'Honda']);
    }

    public function test_vehicle_can_be_deleted(): void
    {
        $user = $this->createUserWithPermissions(['ver vehículos', 'eliminar vehículos']);

        $vehicle = Vehicle::factory()->create();

        $response = $this->actingAs($user)->delete(route('vehicles.destroy', $vehicle));

        $response->assertRedirect(route('vehicles.index'));
        $this->assertSoftDeleted('vehicles', ['id' => $vehicle->id]);
    }
}