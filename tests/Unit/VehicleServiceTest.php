<?php

namespace Tests\Unit;

use App\Models\Party;
use App\Models\User;
use App\Models\Vehicle;
use App\Services\VehicleService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VehicleServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_create_sets_created_by_and_relationships(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $owner = Party::factory()->create();

        $service = new VehicleService();
        $vehicle = $service->create([
            'plate' => 'ABC123',
            'brand' => 'Toyota',
            'model' => 'Corolla',
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
        $user = User::factory()->create();
        $this->actingAs($user);

        $vehicle = Vehicle::factory()->create();
        $owner = Party::factory()->create();

        $service = new VehicleService();
        $service->update($vehicle, [
            'plate' => $vehicle->plate,
            'brand' => 'Honda',
            'model' => 'Civic',
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
        $user = User::factory()->create();
        $this->actingAs($user);

        $vehicle = Vehicle::factory()->create();
        $owner = Party::factory()->create();

        $service = new VehicleService();
        $service->update($vehicle, [
            'plate' => $vehicle->plate,
            'brand' => $vehicle->brand,
            'model' => $vehicle->model,
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