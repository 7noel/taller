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

    public function test_resync_same_relationships_preserves_ids_and_does_not_duplicate(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        $vehicle = Vehicle::factory()->create(['brand_id' => $this->model->brand_id, 'model_id' => $this->model->id]);
        $owner = Party::factory()->create();
        $billing = Party::factory()->create();

        $data = [
            'plate' => $vehicle->plate,
            'brand_id' => $this->model->brand_id,
            'model_id' => $this->model->id,
        ];

        $this->service()->update($vehicle, $data + ['relationships' => [
            ['party_id' => $owner->id, 'role' => 'owner', 'is_primary_commercial' => true],
            ['party_id' => $billing->id, 'role' => 'billing', 'is_primary_commercial' => false],
        ]]);

        $firstIds = $vehicle->relationships()->pluck('id')->sort()->values();

        // Re-guardar con las mismas relaciones no debe generar duplicados (antes lanzaba 500)
        $this->service()->update($vehicle, $data + ['relationships' => [
            ['party_id' => $owner->id, 'role' => 'owner', 'is_primary_commercial' => true],
            ['party_id' => $billing->id, 'role' => 'billing', 'is_primary_commercial' => false],
        ]]);

        $secondIds = $vehicle->relationships()->pluck('id')->sort()->values();

        $this->assertCount(2, $secondIds);
        $this->assertEquals($firstIds->all(), $secondIds->all(), 'Las relaciones re-guardadas deben preservar sus ids.');
        $this->assertSame(2, $vehicle->relationships()->count());
        $this->assertSame(2, $vehicle->relationships()->withTrashed()->count());
    }

    public function test_role_change_updates_existing_row_instead_of_duplicating(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        $vehicle = Vehicle::factory()->create(['brand_id' => $this->model->brand_id, 'model_id' => $this->model->id]);
        $driver = Party::factory()->create();

        $data = [
            'plate' => $vehicle->plate,
            'brand_id' => $this->model->brand_id,
            'model_id' => $this->model->id,
        ];

        $this->service()->update($vehicle, $data + ['relationships' => [
            ['party_id' => $driver->id, 'role' => 'driver', 'is_primary_commercial' => false],
        ]]);

        // El mismo contacto pasa a propietario: la fila se actualiza, no se crea duplicado
        $this->service()->update($vehicle, $data + ['relationships' => [
            ['party_id' => $driver->id, 'role' => 'owner', 'is_primary_commercial' => true, 'notes' => 'Ahora dueño'],
        ]]);

        $this->assertSame(1, $vehicle->relationships()->count());
        $rel = $vehicle->relationships()->first();
        $this->assertEquals('owner', $rel->role);
        $this->assertTrue((bool) $rel->is_primary_commercial);
        $this->assertEquals('Ahora dueño', $rel->notes);
    }

    public function test_removed_relationship_is_soft_deleted(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        $vehicle = Vehicle::factory()->create(['brand_id' => $this->model->brand_id, 'model_id' => $this->model->id]);
        $owner = Party::factory()->create();
        $billing = Party::factory()->create();

        $data = [
            'plate' => $vehicle->plate,
            'brand_id' => $this->model->brand_id,
            'model_id' => $this->model->id,
        ];

        $this->service()->update($vehicle, $data + ['relationships' => [
            ['party_id' => $owner->id, 'role' => 'owner', 'is_primary_commercial' => true],
            ['party_id' => $billing->id, 'role' => 'billing', 'is_primary_commercial' => false],
        ]]);

        // Quitar el billing: solo esa fila queda soft-deleted
        $this->service()->update($vehicle, $data + ['relationships' => [
            ['party_id' => $owner->id, 'role' => 'owner', 'is_primary_commercial' => true],
        ]]);

        $this->assertSame(1, $vehicle->relationships()->count());
        $this->assertSame(2, $vehicle->relationships()->withTrashed()->count());
        $this->assertSoftDeleted('vehicle_relationships', ['vehicle_id' => $vehicle->id, 'party_id' => $billing->id, 'role' => 'billing']);
    }
}
