<?php

namespace Tests\Feature;

use App\Models\Appointment;
use App\Models\Brand;
use App\Models\CheckIn;
use App\Models\DocumentSeries;
use App\Models\DocumentType;
use App\Models\Establishment;
use App\Models\FollowUp;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\VehicleModel;
use App\Services\AppointmentService;
use App\Services\CheckInService;
use Illuminate\Foundation\Testing\DatabaseTruncation;
use Illuminate\Foundation\Testing\RefreshDatabaseState;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AppointmentTest extends TestCase
{
    use DatabaseTruncation;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        // La DB de testing ya está migrada (ver README del proyecto). Evita que
        // DatabaseTruncation ejecute `migrate:fresh` en la primera corrida
        // (lento en entornos Windows); solo trunca las tablas.
        RefreshDatabaseState::$migrated = true;
    }

    public static function tearDownAfterClass(): void
    {
        RefreshDatabaseState::$migrated = false;

        parent::tearDownAfterClass();
    }

    private Vehicle $vehicle;
    private Establishment $establishment;

    protected function setUp(): void
    {
        parent::setUp();

        $brand = Brand::create(['name' => 'TOYOTA']);
        $model = VehicleModel::create(['brand_id' => $brand->id, 'name' => 'COROLLA']);

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

        // El servicio asigna el número IV01 al crear check-ins.
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
    }

    protected function createUserWithPermissions(array $permissions): User
    {
        $user = User::factory()->create(['establishment_id' => $this->establishment->id]);
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }
        $role = Role::firstOrCreate(['name' => 'Appointment Test Role']);
        $role->syncPermissions($permissions);
        $user->assignRole($role);

        return $user;
    }

    protected function createAppointment(string $when, string $status = 'confirmed'): Appointment
    {
        return Appointment::create([
            'establishment_id' => $this->establishment->id,
            'vehicle_id' => $this->vehicle->id,
            'scheduled_at' => $when,
            'status' => $status,
        ]);
    }

    public function test_checkin_associates_same_day_appointment(): void
    {
        $user = $this->createUserWithPermissions(['crear inventarios']);
        $this->actingAs($user);

        $appointment = $this->createAppointment(now()->setTime(14, 30)->format('Y-m-d H:i:s'), 'confirmed');

        $checkIn = app(CheckInService::class)->create([
            'vehicle_id' => $this->vehicle->id,
            'service_type' => 'preventivo',
        ]);

        $this->assertSame($checkIn->id, $appointment->fresh()->check_in_id);
        $this->assertSame('completed', $appointment->fresh()->status);
        $this->assertInstanceOf(Appointment::class, $checkIn->appointment_associated);
    }

    public function test_checkin_does_not_associate_appointment_on_other_day(): void
    {
        $user = $this->createUserWithPermissions(['crear inventarios']);
        $this->actingAs($user);

        $appointment = $this->createAppointment(now()->addDay()->setTime(10, 0)->format('Y-m-d H:i:s'), 'scheduled');

        $checkIn = app(CheckInService::class)->create([
            'vehicle_id' => $this->vehicle->id,
            'service_type' => 'preventivo',
        ]);

        $this->assertNull($appointment->fresh()->check_in_id);
        $this->assertSame('scheduled', $appointment->fresh()->status);
        $this->assertNull($checkIn->appointment_associated);
    }

    public function test_unlink_restores_appointment_to_confirmed(): void
    {
        $user = $this->createUserWithPermissions(['crear inventarios']);
        $this->actingAs($user);

        $appointment = $this->createAppointment(now()->setTime(14, 30)->format('Y-m-d H:i:s'), 'confirmed');

        $checkIn = app(CheckInService::class)->create([
            'vehicle_id' => $this->vehicle->id,
            'service_type' => 'preventivo',
        ]);

        app(AppointmentService::class)->unlink($appointment->fresh());

        $this->assertNull($appointment->fresh()->check_in_id);
        $this->assertSame('confirmed', $appointment->fresh()->status);
        $this->assertSame($checkIn->id, CheckIn::find($checkIn->id)->id);
    }

    public function test_vehicle_info_returns_today_and_others(): void
    {
        $user = $this->createUserWithPermissions(['ver citas']);
        $this->actingAs($user);

        $today = $this->createAppointment(now()->setTime(14, 30)->format('Y-m-d H:i:s'), 'scheduled');
        $other = $this->createAppointment(now()->addDay()->setTime(10, 0)->format('Y-m-d H:i:s'), 'scheduled');

        $info = app(AppointmentService::class)->vehicleInfo($this->vehicle);

        $this->assertSame($today->id, $info['today']['id']);
        $this->assertCount(1, $info['others']);
        $this->assertSame($other->id, $info['others'][0]['id']);
    }

    public function test_admin_can_create_and_list_appointments(): void
    {
        $user = $this->createUserWithPermissions(['ver citas', 'crear citas', 'editar citas']);
        $this->actingAs($user);

        $this->post(route('appointments.store'), [
            'vehicle_id' => $this->vehicle->id,
            'scheduled_date' => now()->format('Y-m-d'),
            'scheduled_time' => '10:30',
            'contact_name' => 'Juan Pérez',
            'contact_phone' => '987654321',
            'service_type' => 'preventivo',
            'reason' => 'Cambio de aceite',
        ])->assertRedirect(route('appointments.index'));

        $this->assertDatabaseHas('appointments', [
            'contact_name' => 'Juan Pérez',
            'service_type' => 'preventivo',
            'status' => 'scheduled',
        ]);

        $this->get(route('appointments.index'))->assertOk();
        $this->get(route('appointments.create'))->assertOk();
        $this->getJson(route('api.appointments.search'))->assertJsonCount(1);

        $appointment = Appointment::first();
        $this->get(route('appointments.show', $appointment))->assertOk();
        $this->get(route('appointments.edit', $appointment))->assertOk();
    }

    public function test_admin_can_create_follow_up(): void
    {
        $user = $this->createUserWithPermissions(['ver seguimientos', 'crear seguimientos']);
        $this->actingAs($user);

        $this->post(route('follow-ups.store'), [
            'vehicle_id' => $this->vehicle->id,
            'date' => now()->format('Y-m-d'),
            'type' => 'call',
            'notes' => 'Llamada de seguimiento',
            'next_action_date' => now()->addDays(3)->format('Y-m-d'),
        ])->assertSessionHasNoErrors();

        $this->assertDatabaseHas('follow_ups', [
            'vehicle_id' => $this->vehicle->id,
            'type' => 'call',
            'done' => false,
        ]);

        $this->get(route('follow-ups.index'))->assertOk();
    }

    public function test_completed_appointment_is_not_offered_again(): void
    {
        $user = $this->createUserWithPermissions(['crear inventarios']);
        $this->actingAs($user);

        $this->createAppointment(now()->setTime(14, 30)->format('Y-m-d H:i:s'), 'confirmed');

        $first = app(CheckInService::class)->create([
            'vehicle_id' => $this->vehicle->id,
            'service_type' => 'preventivo',
        ]);

        // Segundo ingreso el mismo día: la cita ya está asociada al primero.
        $second = app(CheckInService::class)->create([
            'vehicle_id' => $this->vehicle->id,
            'service_type' => 'siniestro',
        ]);

        $this->assertSame($first->id, Appointment::first()->check_in_id);
        $this->assertNull($second->appointment_associated);
    }

    public function test_follow_up_requires_party_or_vehicle(): void
    {
        $user = $this->createUserWithPermissions(['crear seguimientos']);
        $this->actingAs($user);

        $this->post(route('follow-ups.store'), [
            'date' => now()->format('Y-m-d'),
            'type' => 'call',
        ])->assertSessionHasErrors('party_id');

        $this->assertDatabaseCount('follow_ups', 0);
    }
}
