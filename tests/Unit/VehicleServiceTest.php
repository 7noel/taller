<?php

namespace Tests\Unit;

use App\Models\Brand;
use App\Models\Party;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\VehicleModel;
use App\Services\VehicleService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VehicleServiceTest extends TestCase
{
    use RefreshDatabase;

    private VehicleModel $model;

    protected function setUp(): void
    {
        parent::setUp();
        $brand = Brand::create(['name' => 'TOYOTA']);
        $this->model = VehicleModel::create(['brand_id' => $brand->id, 'name' => 'COROLLA']);
    }

    private function service(): VehicleService
    {
        return new VehicleService(new \App\Services\BrandService(), new \App\Services\VehicleModelService());
    }

    public function test_create_sets_created_by_and_relationships(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        $owner = Party::factory()->create();

        $vehicle = $this->service()->create([
            'plate' => 'ABC123',
            'brand_id' => $this->model->brand_id,
            'model_id' => $this->model->id,
            'relationships' => [
                ['party_id' => $owner->id, 'role' => 'owner', 'is_primary_commercial' => true, 'notes' => 'Dueño'],
            ],
        ]);

        $this->assertEquals($user->id, $vehicle->created_by);
        $this->assertCount(1, $vehicle->relationships);
    }

    public function test_update_syncs_relationships(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        $vehicle = Vehicle::factory()->create(['brand_id' => $this->model->brand_id, 'model_id' => $this->model->id]);
        $owner = Party::factory()->create();

        $this->service()->update($vehicle, [
            'plate' => $vehicle->plate,
            'brand_id' => $this->model->brand_id,
            'model_id' => $this->model->id,
            'relationships' => [
                ['party_id' => $owner->id, 'role' => 'owner', 'is_primary_commercial' => true],
            ],
        ]);

        $vehicle->refresh();
        $this->assertCount(1, $vehicle->relationships);
        $this->assertEquals($owner->id, $vehicle->relationships->first()->party_id);
    }

    public function test_delete_soft_deletes_relationships(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        $vehicle = Vehicle::factory()->create(['brand_id' => $this->model->brand_id, 'model_id' => $this->model->id]);

        $this->service()->update($vehicle, [
            'plate' => $vehicle->plate,
            'brand_id' => $this->model->brand_id,
            'model_id' => $this->model->id,
            'relationships' => [
                ['party_id' => Party::factory()->create()->id, 'role' => 'owner', 'is_primary_commercial' => true],
            ],
        ]);

        $this->assertTrue($this->service()->delete($vehicle));
        $this->assertSoftDeleted('vehicles', ['id' => $vehicle->id]);
        $this->assertSoftDeleted('vehicle_relationships', ['vehicle_id' => $vehicle->id]);
    }
}