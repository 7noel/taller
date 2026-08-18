<?php

namespace Tests\Unit;

use App\Models\Brand;
use App\Models\Establishment;
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

    private Establishment $establishment;

    protected function setUp(): void
    {
        parent::setUp();
        $this->establishment = Establishment::create([
            'name' => 'Sede Test', 'address' => 'Av. Test', 'phone' => '999', 'email' => 't@t.com', 'code' => 'TST',
        ]);
    }

    private function createModel(): VehicleModel
    {
        $brand = Brand::create(['name' => 'TOYOTA']);

        return VehicleModel::create(['brand_id' => $brand->id, 'name' => 'COROLLA']);
    }

    public function test_create_sets_created_by_and_relationships(): void
    {
        $user = User::factory()->create(['establishment_id' => $this->establishment->id]);
        $this->actingAs($user);

        $owner = Party::factory()->create(['establishment_id' => $this->establishment->id]);
        $model = $this->createModel();

        $service = new VehicleService(new \App\Services\BrandService(), new \App\Services\VehicleModelService());
        $vehicle = $service->create([
            'plate' => 'ABC123',
            'model_id' => $model->id,
            'establishment_id' => $this->establishment->id,
            'relationships' => [
                ['party_id' => $owner->id, 'role' => 'owner', 'is_primary_commercial' => true, 'notes' => 'Dueño'],
            ],
        ]);

        $this->assertEquals($user->id, $vehicle->created_by);
        $this->assertCount(1, $vehicle->relationships);
    }

    public function test_update_syncs_relationships(): void
    {
        $user = User::factory()->create(['establishment_id' => $this->establishment->id]);
        $this->actingAs($user);

        $model = $this->createModel();
        $vehicle = Vehicle::factory()->create(['model_id' => $model->id, 'establishment_id' => $this->establishment->id]);
        $owner = Party::factory()->create(['establishment_id' => $this->establishment->id]);

        $service = new VehicleService(new \App\Services\BrandService(), new \App\Services\VehicleModelService());
        $service->update($vehicle, [
            'plate' => $vehicle->plate,
            'model_id' => $model->id,
            'establishment_id' => $this->establishment->id,
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
        $user = User::factory()->create(['establishment_id' => $this->establishment->id]);
        $this->actingAs($user);

        $model = $this->createModel();
        $vehicle = Vehicle::factory()->create(['model_id' => $model->id, 'establishment_id' => $this->establishment->id]);
        $owner = Party::factory()->create(['establishment_id' => $this->establishment->id]);

        $service = new VehicleService(new \App\Services\BrandService(), new \App\Services\VehicleModelService());
        $service->update($vehicle, [
            'plate' => $vehicle->plate,
            'model_id' => $model->id,
            'establishment_id' => $this->establishment->id,
            'relationships' => [
                ['party_id' => $owner->id, 'role' => 'owner', 'is_primary_commercial' => true],
            ],
        ]);

        $this->assertTrue($service->delete($vehicle));
        $this->assertSoftDeleted('vehicles', ['id' => $vehicle->id]);
        $this->assertSoftDeleted('vehicle_relationships', ['vehicle_id' => $vehicle->id]);
    }
}