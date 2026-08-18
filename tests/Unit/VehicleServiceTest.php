<?php

namespace Tests\Unit;

use App\Models\Establishment;
use App\Models\Party;
use App\Models\User;
use App\Models\Vehicle;
use App\Services\VehicleService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VehicleServiceTest extends TestCase
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

    public function test_create_sets_created_by_and_relationships(): void
    {
        $user = User::factory()->create(['establishment_id' => $this->establishment->id]);
        $this->actingAs($user);

        $owner = Party::factory()->create(['establishment_id' => $this->establishment->id]);

        $service = new VehicleService();
        $vehicle = $service->create([
            'plate' => 'ABC-123',
            'brand' => 'Toyota',
            'model' => 'Corolla',
            'establishment_id' => $this->establishment->id,
            'relationships' => [
                [
                    'party_id' => $owner->id,
                    'role' => 'owner',
                    'is_primary_commercial' => true,
                    'notes' => 'Dueño',
                ],
            ],
        ]);

        $this->assertEquals($user->id, $vehicle->created_by);
        $this->assertCount(1, $vehicle->relationships);
        $this->assertEquals('owner', $vehicle->relationships->first()->role);
        $this->assertTrue((bool) $vehicle->relationships->first()->is_primary_commercial);
    }

    public function test_update_syncs_relationships(): void
    {
        $user = User::factory()->create(['establishment_id' => $this->establishment->id]);
        $this->actingAs($user);

        $vehicle = Vehicle::factory()->create(['establishment_id' => $this->establishment->id]);
        $owner = Party::factory()->create(['establishment_id' => $this->establishment->id]);

        $service = new VehicleService();
        $service->update($vehicle, [
            'plate' => $vehicle->plate,
            'brand' => 'Honda',
            'model' => 'Civic',
            'establishment_id' => $this->establishment->id,
            'relationships' => [
                [
                    'party_id' => $owner->id,
                    'role' => 'owner',
                    'is_primary_commercial' => true,
                ],
            ],
        ]);

        $vehicle->refresh();

        $this->assertEquals('Honda', $vehicle->brand);
        $this->assertCount(1, $vehicle->relationships);
        $this->assertEquals($owner->id, $vehicle->relationships->first()->party_id);
    }

    public function test_delete_soft_deletes_relationships(): void
    {
        $user = User::factory()->create(['establishment_id' => $this->establishment->id]);
        $this->actingAs($user);

        $vehicle = Vehicle::factory()->create(['establishment_id' => $this->establishment->id]);
        $owner = Party::factory()->create(['establishment_id' => $this->establishment->id]);

        $service = new VehicleService();
        $service->update($vehicle, [
            'plate' => $vehicle->plate,
            'brand' => $vehicle->brand,
            'model' => $vehicle->model,
            'establishment_id' => $this->establishment->id,
            'relationships' => [
                [
                    'party_id' => $owner->id,
                    'role' => 'owner',
                    'is_primary_commercial' => true,
                ],
            ],
        ]);

        $result = $service->delete($vehicle);

        $this->assertTrue($result);
        $this->assertSoftDeleted('vehicles', ['id' => $vehicle->id]);
        $this->assertSoftDeleted('vehicle_relationships', ['vehicle_id' => $vehicle->id]);
    }
}