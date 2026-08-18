<?php

namespace Tests\Feature;

use App\Models\Establishment;
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

    protected function setUp(): void
    {
        parent::setUp();

        $this->establishment = Establishment::create([
            'name' => 'Sede Test',
            'address' => 'Av. Test 123',
            'phone' => '999999999',
            'email' => 'test@test.com',
            'code' => 'TST-001',
        ]);
    }

    protected function createUserWithPermissions(array $permissions): User
    {
        $user = User::factory()->create(['establishment_id' => $this->establishment->id]);

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        $role = Role::firstOrCreate(['name' => 'Test Role']);
        $role->syncPermissions($permissions);

        $user->assignRole($role);

        return $user;
    }

    public function test_vehicle_can_be_created_with_relationships(): void
    {
        $user = $this->createUserWithPermissions(['ver vehículos', 'crear vehículos']);

        $owner = Party::factory()->create(['establishment_id' => $this->establishment->id]);
        $driver = Party::factory()->create(['establishment_id' => $this->establishment->id]);

        $response = $this->actingAs($user)->post(route('vehicles.store'), [
            'plate' => 'ABC-123',
            'brand' => 'Toyota',
            'model' => 'Corolla',
            'body_type' => 'sedan',
            'color' => 'Blanco',
            'year' => 2019,
            'technical_review_reminder_days' => 15,
            'establishment_id' => $this->establishment->id,
            'relationships' => [
                [
                    'party_id' => $owner->id,
                    'role' => 'owner',
                    'is_primary_commercial' => 1,
                    'notes' => 'Dueño',
                ],
                [
                    'party_id' => $driver->id,
                    'role' => 'driver',
                    'is_primary_commercial' => 0,
                    'notes' => null,
                ],
            ],
        ]);

        $response->assertRedirect(route('vehicles.index'));

        $this->assertDatabaseHas('vehicles', [
            'plate' => 'ABC-123',
            'brand' => 'Toyota',
        ]);

        $this->assertDatabaseHas('vehicle_relationships', [
            'role' => 'owner',
            'is_primary_commercial' => 1,
        ]);

        $this->assertDatabaseHas('vehicle_relationships', [
            'role' => 'driver',
            'is_primary_commercial' => 0,
        ]);
    }

    public function test_vehicle_requires_unique_plate(): void
    {
        $user = $this->createUserWithPermissions(['crear vehículos']);

        Vehicle::factory()->create([
            'plate' => 'XYZ-999',
            'establishment_id' => $this->establishment->id,
        ]);

        $response = $this->actingAs($user)->post(route('vehicles.store'), [
            'plate' => 'XYZ-999',
            'brand' => 'Nissan',
            'model' => 'Sentra',
            'establishment_id' => $this->establishment->id,
        ]);

        $response->assertSessionHasErrors('plate');
    }

    public function test_vehicle_can_be_updated(): void
    {
        $user = $this->createUserWithPermissions(['ver vehículos', 'editar vehículos']);

        $vehicle = Vehicle::factory()->create(['establishment_id' => $this->establishment->id]);

        $response = $this->actingAs($user)->put(route('vehicles.update', $vehicle), [
            'plate' => $vehicle->plate,
            'brand' => 'Honda',
            'model' => 'Civic',
            'establishment_id' => $this->establishment->id,
        ]);

        $response->assertRedirect(route('vehicles.index'));

        $this->assertDatabaseHas('vehicles', [
            'id' => $vehicle->id,
            'brand' => 'Honda',
        ]);
    }

    public function test_vehicle_can_be_deleted(): void
    {
        $user = $this->createUserWithPermissions(['ver vehículos', 'eliminar vehículos']);

        $vehicle = Vehicle::factory()->create(['establishment_id' => $this->establishment->id]);

        $response = $this->actingAs($user)->delete(route('vehicles.destroy', $vehicle));

        $response->assertRedirect(route('vehicles.index'));

        $this->assertSoftDeleted('vehicles', ['id' => $vehicle->id]);
    }
}