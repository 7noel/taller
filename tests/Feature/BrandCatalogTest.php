<?php

namespace Tests\Feature;

use App\Models\Brand;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\VehicleModel;
use Illuminate\Foundation\Testing\DatabaseTruncation;
use Illuminate\Foundation\Testing\RefreshDatabaseState;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class BrandCatalogTest extends TestCase
{
    use DatabaseTruncation;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        // La DB de testing ya está migrada (ver README del proyecto). Evita que
        // DatabaseTruncation ejecute `migrate:fresh` en la primera corrida.
        RefreshDatabaseState::$migrated = true;
    }

    public static function tearDownAfterClass(): void
    {
        RefreshDatabaseState::$migrated = false;

        parent::tearDownAfterClass();
    }

    protected function userWithPermissions(array $permissions): User
    {
        $user = User::factory()->create();
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }
        $role = Role::firstOrCreate(['name' => 'Brand Catalog Test Role']);
        $role->syncPermissions($permissions);
        $user->assignRole($role);

        return $user;
    }

    protected function brandPermissions(): array
    {
        return [
            'ver marcas', 'crear marcas', 'editar marcas', 'eliminar marcas',
            'ver modelos', 'crear modelos', 'editar modelos', 'eliminar modelos',
        ];
    }

    public function test_create_brand_with_models_uppercases_and_redirects_to_edit(): void
    {
        $user = $this->userWithPermissions($this->brandPermissions());
        $this->actingAs($user);

        $response = $this->post(route('brands.store'), [
            'name' => 'toyota',
            'models' => [
                ['name' => 'corolla'],
                ['name' => 'RAV4'],
                ['name' => '   '], // fila vacía -> se descarta
            ],
        ]);

        $brand = Brand::where('name', 'TOYOTA')->firstOrFail();

        $response->assertRedirect(route('brands.edit', $brand));
        $this->assertSame(
            ['COROLLA', 'RAV4'],
            $brand->models()->orderBy('name')->pluck('name')->all()
        );
    }

    public function test_update_syncs_models_with_diff_upsert(): void
    {
        $brand = Brand::create(['name' => 'HONDA']);
        $kept = VehicleModel::create(['brand_id' => $brand->id, 'name' => 'CIVIC']);
        $removed = VehicleModel::create(['brand_id' => $brand->id, 'name' => 'ACCORD']);

        $user = $this->userWithPermissions($this->brandPermissions());
        $this->actingAs($user);

        $response = $this->put(route('brands.update', $brand), [
            'name' => 'honda',
            'models' => [
                ['id' => $kept->id, 'name' => 'CIVIC X'],
                ['name' => 'CRV'],
            ],
        ]);

        $response->assertSessionHas('success');

        // Upsert: el modelo existente conserva su id (solo cambia el nombre).
        $this->assertSame('CIVIC X', $kept->fresh()->name);
        $this->assertNull(VehicleModel::find($removed->id));
        $this->assertNotNull(VehicleModel::withTrashed()->find($removed->id));
        $this->assertSame(
            ['CIVIC X', 'CRV'],
            $brand->fresh()->models()->orderBy('name')->pluck('name')->all()
        );
    }

    public function test_model_in_use_is_kept_when_removed_from_form(): void
    {
        $brand = Brand::create(['name' => 'NISSAN']);
        $model = VehicleModel::create(['brand_id' => $brand->id, 'name' => 'SENTRA']);
        Vehicle::factory()->create([
            'brand_id' => $brand->id,
            'model_id' => $model->id,
            'plate' => 'NIS-001',
        ]);

        $user = $this->userWithPermissions($this->brandPermissions());
        $this->actingAs($user);

        $response = $this->put(route('brands.update', $brand), [
            'name' => 'NISSAN',
            'models' => [],
        ]);

        $response->assertSessionHas('warning');
        $this->assertNotNull(VehicleModel::find($model->id));
    }

    public function test_destroy_is_blocked_when_brand_is_in_use(): void
    {
        $brand = Brand::create(['name' => 'KIA']);
        $model = VehicleModel::create(['brand_id' => $brand->id, 'name' => 'SPORTAGE']);
        Vehicle::factory()->create([
            'brand_id' => $brand->id,
            'model_id' => $model->id,
            'plate' => 'KIA-001',
        ]);

        $user = $this->userWithPermissions($this->brandPermissions());
        $this->actingAs($user);

        $response = $this->delete(route('brands.destroy', $brand));

        $response->assertSessionHas('error');
        $this->assertNotNull(Brand::find($brand->id));
    }

    public function test_destroy_removes_brand_and_its_models_when_unused(): void
    {
        $brand = Brand::create(['name' => 'MAZDA']);
        VehicleModel::create(['brand_id' => $brand->id, 'name' => 'CX-5']);
        VehicleModel::create(['brand_id' => $brand->id, 'name' => '3']);

        $user = $this->userWithPermissions($this->brandPermissions());
        $this->actingAs($user);

        $response = $this->delete(route('brands.destroy', $brand));

        $response->assertSessionHas('success');
        $this->assertNull(Brand::find($brand->id));
        $this->assertSame(0, $brand->models()->count());
        $this->assertSame(2, VehicleModel::withTrashed()->where('brand_id', $brand->id)->count());
    }

    public function test_search_returns_models_and_vehicles_count(): void
    {
        $brand = Brand::create(['name' => 'HYUNDAI']);
        $model = VehicleModel::create(['brand_id' => $brand->id, 'name' => 'TUCSON']);
        Vehicle::factory()->create([
            'brand_id' => $brand->id,
            'model_id' => $model->id,
            'plate' => 'HYU-001',
        ]);
        VehicleModel::create(['brand_id' => $brand->id, 'name' => 'ACCENT']);

        $user = $this->userWithPermissions(['ver marcas']);
        $this->actingAs($user);

        $this->get(route('api.brands.search'))
            ->assertOk()
            ->assertJsonFragment(['name' => 'HYUNDAI', 'models_count' => 2, 'vehicles_count' => 1]);

        $this->get(route('api.brands.search', ['q' => 'tucson']))
            ->assertOk()
            ->assertJsonCount(1);
    }

    public function test_index_create_and_edit_pages_render(): void
    {
        $brand = Brand::create(['name' => 'SUZUKI']);
        VehicleModel::create(['brand_id' => $brand->id, 'name' => 'VITARA']);

        $user = $this->userWithPermissions($this->brandPermissions());
        $this->actingAs($user);

        $this->get(route('brands.index'))->assertOk();
        $this->get(route('brands.create'))->assertOk();
        $this->get(route('brands.edit', $brand))->assertOk()->assertSee('VITARA');
    }
}
