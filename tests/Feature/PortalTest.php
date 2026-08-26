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
use App\Services\VehicleService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PortalTest extends TestCase
{
    use RefreshDatabase;

    private Establishment $establishment;
    private Vehicle $vehicleA;
    private Vehicle $vehicleB;

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

        $this->vehicleA = Vehicle::create([
            'plate' => 'ABC123',
            'brand_id' => $brand->id,
            'model_id' => $model->id,
            'access_token' => Vehicle::generateAccessToken(),
            'access_token_created_at' => now(),
        ]);

        $this->vehicleB = Vehicle::create([
            'plate' => 'XYZ456',
            'brand_id' => $brand->id,
            'model_id' => $model->id,
            'access_token' => Vehicle::generateAccessToken(),
            'access_token_created_at' => now(),
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
    }

    private function makeCheckIn(Vehicle $vehicle, string $status = 'pending_approval', array $extra = []): CheckIn
    {
        $user = User::factory()->create(['establishment_id' => $this->establishment->id]);

        return CheckIn::create(array_merge([
            'vehicle_id' => $vehicle->id,
            'establishment_id' => $this->establishment->id,
            'created_by' => $user->id,
            'service_type' => 'preventivo',
            'status' => $status,
        ], $extra));
    }

    private function makeEstimate(Vehicle $vehicle, string $status = 'sent_client'): Estimate
    {
        $party = Party::factory()->person()->create();

        return Estimate::create([
            'vehicle_id' => $vehicle->id,
            'client_id' => $party->id,
            'establishment_id' => $this->establishment->id,
            'service_type' => 'preventivo',
            'status' => $status,
            'total' => 1200,
            'currency' => 'PEN',
        ]);
    }

    public function test_portal_home_shows_pending_approvals(): void
    {
        $this->makeCheckIn($this->vehicleA);
        $this->makeEstimate($this->vehicleA);

        $this->get(route('public.portal', $this->vehicleA->access_token))
            ->assertOk()
            ->assertSee('Pendiente de tu aprobación')
            ->assertSee('Presupuestos por confirmar');
    }

    public function test_client_can_approve_check_in(): void
    {
        $checkIn = $this->makeCheckIn($this->vehicleA, 'pending_approval', [
            'last_sent_to' => 'María Pérez',
            'last_sent_to_phone' => '987654321',
        ]);

        $this->post(route('public.portal.check-in.approve', [$this->vehicleA->access_token, $checkIn]))
            ->assertRedirect(route('public.portal', $this->vehicleA->access_token));

        $this->assertDatabaseHas('check_ins', [
            'id' => $checkIn->id,
            'status' => 'approved',
            'approved_by_recipient' => 'María Pérez',
            'approved_by_phone' => '987654321',
        ]);

        $this->assertDatabaseHas('public_approval_logs', [
            'vehicle_id' => $this->vehicleA->id,
            'entity_type' => 'check_in',
            'entity_id' => $checkIn->id,
            'action' => 'approved',
            'actor_type' => 'portal',
        ]);
    }

    public function test_client_reject_check_in_requires_reason(): void
    {
        $checkIn = $this->makeCheckIn($this->vehicleA);

        $this->post(route('public.portal.check-in.reject', [$this->vehicleA->access_token, $checkIn]))
            ->assertSessionHasErrors('reason');
    }

    public function test_client_can_reject_check_in_with_reason(): void
    {
        $checkIn = $this->makeCheckIn($this->vehicleA);

        $this->post(
            route('public.portal.check-in.reject', [$this->vehicleA->access_token, $checkIn]),
            ['reason' => 'Falta revisar la puerta']
        )->assertRedirect(route('public.portal', $this->vehicleA->access_token));

        $this->assertDatabaseHas('check_ins', [
            'id' => $checkIn->id,
            'status' => 'rejected',
            'rejection_reason' => 'Falta revisar la puerta',
        ]);
    }

    public function test_token_of_vehicle_a_cannot_approve_document_of_vehicle_b(): void
    {
        $checkInB = $this->makeCheckIn($this->vehicleB);

        $this->post(route('public.portal.check-in.approve', [$this->vehicleA->access_token, $checkInB]))
            ->assertNotFound();

        $this->assertDatabaseHas('check_ins', ['id' => $checkInB->id, 'status' => 'pending_approval']);
    }

    public function test_double_approval_is_idempotent(): void
    {
        $checkIn = $this->makeCheckIn($this->vehicleA);

        $this->post(route('public.portal.check-in.approve', [$this->vehicleA->access_token, $checkIn]))
            ->assertRedirect();

        // Segundo intento: ya no está pendiente → el servicio rechaza la transición.
        $this->post(route('public.portal.check-in.approve', [$this->vehicleA->access_token, $checkIn]))
            ->assertSessionHasErrors('approval');

        $this->assertDatabaseHas('check_ins', ['id' => $checkIn->id, 'status' => 'approved']);
    }

    public function test_client_can_approve_estimate(): void
    {
        $estimate = $this->makeEstimate($this->vehicleA);
        $estimate->update([
            'last_sent_to' => 'Juan Pérez',
            'last_sent_to_phone' => '912345678',
        ]);

        $this->post(route('public.portal.estimate.approve', [$this->vehicleA->access_token, $estimate]))
            ->assertRedirect(route('public.portal', $this->vehicleA->access_token));

        $this->assertDatabaseHas('estimates', [
            'id' => $estimate->id,
            'status' => 'approved_client',
            'approved_by_recipient' => 'Juan Pérez',
        ]);

        $this->assertDatabaseHas('estimate_status_history', [
            'estimate_id' => $estimate->id,
            'to_status' => 'approved_client',
        ]);
    }

    public function test_invalid_token_returns_404(): void
    {
        $this->get('/c/not-a-valid-token')->assertNotFound();
    }

    public function test_vehicle_created_via_service_has_token(): void
    {
        $brand = Brand::first();
        $model = VehicleModel::first();

        $vehicle = app(VehicleService::class)->create([
            'plate' => 'ZZZ999',
            'brand_id' => $brand->id,
            'model_id' => $model->id,
        ]);

        $this->assertNotNull($vehicle->access_token);
        $this->assertNotNull($vehicle->public_link);
    }

    protected function createUserWithPermissions(array $permissions): User
    {
        $user = User::factory()->create(['establishment_id' => $this->establishment->id]);
        foreach ($permissions as $permission) {
            \Spatie\Permission\Models\Permission::firstOrCreate(['name' => $permission]);
        }
        $role = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'Portal WhatsApp Role']);
        $role->syncPermissions($permissions);
        $user->assignRole($role);

        return $user;
    }

    public function test_whatsapp_wa_me_records_last_sent_and_redirects(): void
    {
        $user = $this->createUserWithPermissions(['ver inventarios']);
        $checkIn = $this->makeCheckIn($this->vehicleA, 'pending_approval');

        $this->actingAs($user)
            ->post(route('check-ins.whatsapp', $checkIn), [
                'recipient_name' => 'María Pérez',
                'phone' => '987654321',
                'message' => 'Hola María',
                'send_method' => 'wa_me',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('check_ins', [
            'id' => $checkIn->id,
            'last_sent_to' => 'María Pérez',
            'last_sent_to_phone' => '987654321',
        ]);
    }

    public function test_whatsapp_api_warns_when_not_configured(): void
    {
        $user = $this->createUserWithPermissions(['ver inventarios']);
        $checkIn = $this->makeCheckIn($this->vehicleA, 'pending_approval');

        // Establecimiento sin credenciales → error claro y NO se encola el job.
        $this->actingAs($user)
            ->post(route('check-ins.whatsapp', $checkIn), [
                'recipient_name' => 'María Pérez',
                'phone' => '987654321',
                'message' => 'Hola María',
                'send_method' => 'api',
            ])
            ->assertSessionHas('error');

        $this->assertDatabaseCount('jobs', 0);
    }
}

