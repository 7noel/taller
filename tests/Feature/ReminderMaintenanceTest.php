<?php

namespace Tests\Feature;

use App\Models\Brand;
use App\Models\CheckIn;
use App\Models\DocumentSeries;
use App\Models\DocumentType;
use App\Models\Establishment;
use App\Models\Estimate;
use App\Models\Party;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\VehicleModel;
use App\Services\CheckInService;
use App\Services\MaintenanceService;
use Illuminate\Foundation\Testing\DatabaseTruncation;
use Illuminate\Foundation\Testing\RefreshDatabaseState;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ReminderMaintenanceTest extends TestCase
{
    use DatabaseTruncation;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        // La DB de testing ya está migrada (patrón del proyecto, ver AppointmentTest).
        RefreshDatabaseState::$migrated = true;
    }

    public static function tearDownAfterClass(): void
    {
        RefreshDatabaseState::$migrated = false;

        parent::tearDownAfterClass();
    }

    private Vehicle $vehicle;
    private Establishment $establishment;
    private User $user;
    private int $brandId;
    private int $modelId;

    protected function setUp(): void
    {
        parent::setUp();

        $brand = Brand::create(['name' => 'TOYOTA']);
        $model = VehicleModel::create(['brand_id' => $brand->id, 'name' => 'COROLLA']);
        $this->brandId = $brand->id;
        $this->modelId = $model->id;

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

        $documentType = DocumentType::create([
            'code' => 'IV',
            'name' => 'Inventario Vehicular',
            'is_electronic' => false,
            'is_active' => true,
        ]);
        DocumentSeries::create([
            'establishment_id' => $this->establishment->id,
            'document_type_id' => $documentType->id,
            'prefix_serie' => 'IV01',
            'current_number' => 0,
            'number_source' => 'LOCAL',
            'status' => true,
        ]);

        $this->user = User::factory()->create(['establishment_id' => $this->establishment->id]);
    }

    protected function createCheckIn(array $overrides = []): CheckIn
    {
        return app(CheckInService::class)->create(array_merge([
            'vehicle_id' => $this->vehicle->id,
            'service_type' => 'preventivo',
            'mileage' => 10000,
        ], $overrides));
    }

    protected function actingUserWithPermissions(array $permissions): User
    {
        $user = User::factory()->create(['establishment_id' => $this->establishment->id]);
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }
        $role = Role::firstOrCreate(['name' => 'Reminder Test Role']);
        $role->syncPermissions($permissions);
        $user->assignRole($role);

        $this->actingAs($user);

        return $user;
    }

    public function test_checkin_preventivo_syncs_review_and_maintenance_to_vehicle(): void
    {
        $this->actingAs($this->user);

        // El factory asigna una revisión técnica aleatoria; partimos sin fecha
        // para validar la sincronización desde el ingreso.
        $this->vehicle->update(['technical_review_date' => null]);

        $this->createCheckIn([
            'technical_review_expiration' => now()->addMonths(6)->toDateString(),
            'mileage' => 45000,
        ]);

        $this->vehicle->refresh();

        // Revisión técnica sincronizada desde el ingreso.
        $this->assertSame(now()->addMonths(6)->toDateString(), $this->vehicle->technical_review_date?->toDateString());

        // Preventivo: última visita + kilometraje.
        $this->assertNotNull($this->vehicle->last_maintenance_date);
        $this->assertSame(45000, $this->vehicle->last_maintenance_mileage);

        // Sin historial suficiente → última visita + maintenance_default_days (120).
        $this->assertNotNull($this->vehicle->next_maintenance_date);
        $this->assertSame(
            now()->addDays(120)->toDateString(),
            $this->vehicle->next_maintenance_date->toDateString()
        );
        $this->assertSame('calculated', $this->vehicle->maintenance_source);
    }

    public function test_next_maintenance_projects_with_history_of_visits(): void
    {
        $service = app(MaintenanceService::class);

        // Visit 1: hace 200 días, 10.000 km. Visit 2: hace 100 días, 20.000 km.
        // created_at se asigna después del create porque no es un campo fillable.
        $v1 = CheckIn::create([
            'vehicle_id' => $this->vehicle->id,
            'establishment_id' => $this->establishment->id,
            'created_by' => $this->user->id,
            'service_type' => 'preventivo',
            'mileage' => 10000,
            'status' => 'draft',
        ]);
        $v1->forceFill(['created_at' => now()->subDays(200)])->save();

        $v2 = CheckIn::create([
            'vehicle_id' => $this->vehicle->id,
            'establishment_id' => $this->establishment->id,
            'created_by' => $this->user->id,
            'service_type' => 'preventivo',
            'mileage' => 20000,
            'status' => 'draft',
        ]);
        $v2->forceFill(['created_at' => now()->subDays(100)])->save();

        // Ritmo real: 100 días por cada 10.000 km → 50 días por cada 5.000 km.
        $next = $service->calculateNextMaintenanceDate($this->vehicle, now()->subDays(100)->toDateString());

        $this->assertSame(now()->subDays(100)->addDays(50)->toDateString(), $next);
    }

    public function test_manual_next_maintenance_is_not_overwritten(): void
    {
        $this->actingAs($this->user);

        $this->vehicle->update([
            'next_maintenance_date' => '2027-01-15',
            'maintenance_source' => 'manual',
        ]);

        $this->createCheckIn(['mileage' => 30000]);

        $this->vehicle->refresh();

        $this->assertSame('2027-01-15', $this->vehicle->next_maintenance_date?->toDateString());
        $this->assertSame('manual', $this->vehicle->maintenance_source);
        $this->assertSame(30000, $this->vehicle->last_maintenance_mileage);
    }

    public function test_technical_review_never_goes_backwards(): void
    {
        $this->actingAs($this->user);

        $this->vehicle->update(['technical_review_date' => now()->addMonths(9)->toDateString()]);

        $this->createCheckIn(['technical_review_expiration' => now()->addMonths(3)->toDateString()]);

        $this->vehicle->refresh();

        $this->assertSame(now()->addMonths(9)->toDateString(), $this->vehicle->technical_review_date?->toDateString());
    }

    public function test_reminders_search_lists_due_vehicles_by_tab(): void
    {
        $this->actingUserWithPermissions(['ver seguimientos']);

        // Dentro de la ventana (15 días).
        $this->vehicle->update([
            'technical_review_date' => now()->addDays(10)->toDateString(),
            'next_maintenance_date' => now()->addDays(7)->toDateString(),
        ]);

        // Fuera de la ventana → no debe aparecer en los listados.
        Vehicle::factory()->create([
            'brand_id' => $this->brandId,
            'model_id' => $this->modelId,
            'plate' => 'ZZZ999',
            'technical_review_date' => now()->addMonths(4)->toDateString(),
            'next_maintenance_date' => now()->addMonths(4)->toDateString(),
        ]);

        $this->getJson(route('api.reminders.search', ['tab' => 'technical_review']))
            ->assertOk()
            ->assertJsonCount(1)
            ->assertJsonFragment(['plate' => 'ABC123']);

        $this->getJson(route('api.reminders.search', ['tab' => 'maintenance']))
            ->assertOk()
            ->assertJsonCount(1)
            ->assertJsonFragment(['plate' => 'ABC123']);

        // La vista del panel también debe renderizar con el permiso.
        $this->get(route('reminders.index'))->assertOk();
    }

    public function test_reminders_index_requires_permission(): void
    {
        $this->actingUserWithPermissions([]);

        $this->get(route('reminders.index'))->assertForbidden();
    }

    public function test_vehicle_history_renders(): void
    {
        $this->actingUserWithPermissions(['ver vehículos']);

        $this->get(route('vehicles.history', $this->vehicle))->assertOk();
    }

    public function test_follow_up_can_reference_an_estimate(): void
    {
        $this->actingUserWithPermissions(['crear seguimientos']);

        $client = Party::create([
            'document_type' => '1',
            'document_number' => '12345678',
            'first_name' => 'Juan',
            'last_name' => 'Pérez',
        ]);

        $estimate = Estimate::create([
            'vehicle_id' => $this->vehicle->id,
            'client_id' => $client->id,
            'establishment_id' => $this->establishment->id,
            'service_type' => 'siniestro',
            'status' => 'sent_insurance',
            'document_sn' => 'PRE01-000001',
            'created_by' => $this->user->id,
            'updated_by' => $this->user->id,
        ]);

        $this->post(route('follow-ups.store'), [
            'vehicle_id' => $this->vehicle->id,
            'estimate_id' => $estimate->id,
            'date' => now()->format('Y-m-d'),
            'type' => 'call',
            'notes' => 'Esperando respuesta de la aseguradora',
        ])->assertSessionHasNoErrors();

        $this->assertDatabaseHas('follow_ups', [
            'vehicle_id' => $this->vehicle->id,
            'estimate_id' => $estimate->id,
        ]);
    }

    public function test_gestor_de_citas_role_has_expected_permissions(): void
    {
        app(\Database\Seeders\RolePermissionSeeder::class)->run();

        $role = Role::findByName('Gestor de Citas');
        $this->assertNotNull($role);
        $this->assertTrue($role->hasPermissionTo('ver seguimientos'));
        $this->assertTrue($role->hasPermissionTo('crear seguimientos'));
        $this->assertTrue($role->hasPermissionTo('ver citas'));
        $this->assertTrue($role->hasPermissionTo('crear citas'));
        $this->assertTrue($role->hasPermissionTo('crear presupuestos'));
        $this->assertTrue($role->hasPermissionTo('ver presupuestos'));
        $this->assertTrue($role->hasPermissionTo('ver vehículos'));
        $this->assertTrue($role->hasPermissionTo('ver parties'));
        $this->assertFalse($role->hasPermissionTo('eliminar citas'));
    }
}
