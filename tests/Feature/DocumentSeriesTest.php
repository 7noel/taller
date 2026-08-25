<?php

namespace Tests\Feature;

use App\Models\CheckIn;
use App\Models\DocumentSeries;
use App\Models\DocumentType;
use App\Models\Establishment;
use App\Models\Party;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class DocumentSeriesTest extends TestCase
{
    use RefreshDatabase;

    private Establishment $establishment;
    private DocumentType $documentType;

    protected function setUp(): void
    {
        parent::setUp();

        $this->establishment = Establishment::create([
            'name' => 'Taller Central',
            'address' => 'Av. Principal 123',
            'phone' => '123456789',
            'email' => 'contacto@taller.com',
            'code' => 'TC001',
        ]);

        $this->documentType = DocumentType::create([
            'code' => 'IV',
            'name' => 'Inventario Vehicular',
            'is_electronic' => false,
            'is_active' => true,
        ]);
    }

    protected function createAdminUser(): User
    {
        $permissions = ['ver series', 'crear series', 'editar series', 'eliminar series'];
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }
        $role = Role::firstOrCreate(['name' => 'Series Test Role']);
        $role->syncPermissions($permissions);

        $user = User::factory()->create(['establishment_id' => $this->establishment->id]);
        $user->assignRole($role);

        return $user;
    }

    public function test_user_can_create_a_series(): void
    {
        $user = $this->createAdminUser();

        $response = $this->actingAs($user)->post(
            route('establishments.series.store', $this->establishment),
            [
                'document_type_id' => $this->documentType->id,
                'prefix_serie' => 'iv01',
                'current_number' => 3,
                'number_source' => 'LOCAL',
                'status' => 1,
            ]
        );

        $response->assertRedirect(route('establishments.series.index', $this->establishment));
        $response->assertSessionHas('success');

        // El prefijo se normaliza a mayúsculas
        $this->assertDatabaseHas('document_series', [
            'establishment_id' => $this->establishment->id,
            'document_type_id' => $this->documentType->id,
            'prefix_serie' => 'IV01',
            'current_number' => 3,
            'number_source' => 'LOCAL',
            'status' => 1,
        ]);
    }

    public function test_user_cannot_create_series_with_duplicate_prefix_for_same_document_type(): void
    {
        $user = $this->createAdminUser();

        DocumentSeries::create([
            'establishment_id' => $this->establishment->id,
            'document_type_id' => $this->documentType->id,
            'prefix_serie' => 'IV01',
            'current_number' => 0,
            'number_source' => 'LOCAL',
            'status' => true,
        ]);

        $response = $this->actingAs($user)->post(
            route('establishments.series.store', $this->establishment),
            [
                'document_type_id' => $this->documentType->id,
                'prefix_serie' => 'iv01',
                'current_number' => 0,
                'number_source' => 'LOCAL',
                'status' => 1,
            ]
        );

        $response->assertSessionHasErrors('prefix_serie');
        $this->assertDatabaseCount('document_series', 1);
    }

    public function test_user_can_update_a_series(): void
    {
        $user = $this->createAdminUser();

        $series = DocumentSeries::create([
            'establishment_id' => $this->establishment->id,
            'document_type_id' => $this->documentType->id,
            'prefix_serie' => 'IV01',
            'current_number' => 0,
            'number_source' => 'LOCAL',
            'status' => true,
        ]);

        $response = $this->actingAs($user)->put(
            route('establishments.series.update', [$this->establishment, $series]),
            [
                'prefix_serie' => 'iv02',
                'current_number' => 5,
                'number_source' => 'API',
                'status' => 0,
            ]
        );

        $response->assertRedirect(route('establishments.series.index', $this->establishment));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('document_series', [
            'id' => $series->id,
            'prefix_serie' => 'IV02',
            'current_number' => 5,
            'number_source' => 'API',
            'status' => 0,
        ]);
    }

    public function test_user_cannot_delete_series_with_associated_check_ins(): void
    {
        $user = $this->createAdminUser();

        $series = DocumentSeries::create([
            'establishment_id' => $this->establishment->id,
            'document_type_id' => $this->documentType->id,
            'prefix_serie' => 'IV01',
            'current_number' => 3,
            'number_source' => 'LOCAL',
            'status' => true,
        ]);

        $vehicle = Vehicle::factory()->create();
        $client = Party::factory()->create();

        CheckIn::create([
            'vehicle_id' => $vehicle->id,
            'client_id' => $client->id,
            'establishment_id' => $this->establishment->id,
            'document_series_id' => $series->id,
            'document_type_code' => 'IV',
            'document_serie' => 'IV01',
            'document_number' => 3,
            'document_sn' => 'IV01-000003',
            'service_type' => 'preventivo',
            'status' => 'draft',
            'created_by' => $user->id,
            'updated_by' => $user->id,
        ]);

        $response = $this->actingAs($user)->delete(
            route('establishments.series.destroy', [$this->establishment, $series])
        );

        $response->assertSessionHas('error');
        $this->assertDatabaseHas('document_series', ['id' => $series->id]);
    }

    public function test_user_can_delete_series_without_associated_documents(): void
    {
        $user = $this->createAdminUser();

        $series = DocumentSeries::create([
            'establishment_id' => $this->establishment->id,
            'document_type_id' => $this->documentType->id,
            'prefix_serie' => 'PRE01',
            'current_number' => 0,
            'number_source' => 'LOCAL',
            'status' => true,
        ]);

        $response = $this->actingAs($user)->delete(
            route('establishments.series.destroy', [$this->establishment, $series])
        );

        $response->assertRedirect(route('establishments.series.index', $this->establishment));
        $response->assertSessionHas('success');
        $this->assertDatabaseMissing('document_series', ['id' => $series->id]);
    }

    public function test_unauthorized_user_cannot_create_series(): void
    {
        $user = User::factory()->create(['establishment_id' => $this->establishment->id]);

        $this->actingAs($user)
            ->post(
                route('establishments.series.store', $this->establishment),
                [
                    'document_type_id' => $this->documentType->id,
                    'prefix_serie' => 'IV01',
                    'current_number' => 0,
                    'number_source' => 'LOCAL',
                    'status' => 1,
                ]
            )
            ->assertForbidden();
    }
}