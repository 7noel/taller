<?php

namespace Tests\Feature;

use App\Models\Party;
use App\Models\Ubigeo;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class PartyTest extends TestCase
{
    use RefreshDatabase;

    protected function createUserWithPermissions(array $permissions): User
    {
        $user = User::factory()->create();

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        $role = Role::firstOrCreate(['name' => 'Party Test Role']);
        $role->syncPermissions($permissions);

        $user->assignRole($role);

        return $user;
    }

    private function createParty(): Party
    {
        return Party::factory()->create();
    }

    public function test_index_requires_ver_parties_permission(): void
    {
        $user = $this->createUserWithPermissions(['ver parties']);
        $this->actingAs($user)->get(route('parties.index'))->assertOk()->assertViewIs('parties.index');
    }

    public function test_unauthorized_user_cannot_access_parties(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user)->get(route('parties.index'))->assertForbidden();
    }

    public function test_party_can_be_created(): void
    {
        $user = $this->createUserWithPermissions(['ver parties', 'crear parties']);

        $this->actingAs($user)->post(route('parties.store'), [
            'document_type' => '1', 'document_number' => '12345678',
            'first_name' => 'Juan', 'last_name' => 'Pérez', 'email' => 'juan@example.com',
            'phone' => '011234567', 'mobile' => '987654321', 'receive_promotions' => true,
        ])->assertRedirect(route('parties.index'));

        $this->assertDatabaseHas('parties', ['document_number' => '12345678', 'first_name' => 'Juan']);
    }

    public function test_party_validation_fails_without_document_number(): void
    {
        $user = $this->createUserWithPermissions(['crear parties']);

        $this->actingAs($user)->post(route('parties.store'), [
            'document_type' => '1', 'first_name' => 'Juan', 'last_name' => 'Pérez',
        ])->assertSessionHasErrors('document_number');
    }

    public function test_party_can_be_updated(): void
    {
        $user = $this->createUserWithPermissions(['ver parties', 'editar parties']);
        $party = $this->createParty();

        $this->actingAs($user)->put(route('parties.update', $party), [
            'document_type' => '1', 'document_number' => $party->document_number,
            'first_name' => 'María', 'last_name' => 'López',
        ])->assertRedirect(route('parties.index'));

        $this->assertDatabaseHas('parties', ['id' => $party->id, 'first_name' => 'María']);
    }

    public function test_party_can_be_deleted(): void
    {
        $user = $this->createUserWithPermissions(['ver parties', 'eliminar parties']);
        $party = $this->createParty();

        $this->actingAs($user)->delete(route('parties.destroy', $party))->assertRedirect(route('parties.index'));
        $this->assertSoftDeleted('parties', ['id' => $party->id]);
    }

    public function test_quick_store_requires_mobile_for_rol_driver(): void
    {
        $user = $this->createUserWithPermissions(['crear parties']);

        $this->actingAs($user)->postJson(route('api.parties.quick-store'), [
            'role' => 'driver',
            'document_type' => '1',
            'document_number' => '87654321',
            'first_name' => 'Pedro',
            'last_name' => 'Suárez',
        ])->assertUnprocessable();
    }

    public function test_quick_store_creates_light_contact_without_ubigeo(): void
    {
        $user = $this->createUserWithPermissions(['crear parties']);

        $this->actingAs($user)->postJson(route('api.parties.quick-store'), [
            'role' => 'driver',
            'document_type' => '1',
            'document_number' => '87654322',
            'first_name' => 'Pedro',
            'last_name' => 'Suárez',
            'mobile' => '987654321',
        ])->assertCreated();

        $this->assertDatabaseHas('parties', ['document_number' => '87654322', 'mobile' => '987654321']);
    }

    public function test_quick_store_billing_works_without_fiscal_data(): void
    {
        $user = $this->createUserWithPermissions(['crear parties']);

        $this->actingAs($user)->postJson(route('api.parties.quick-store'), [
            'role' => 'billing',
            'document_type' => '6',
            'document_number' => '20123456789',
            'business_name' => 'Empresa SAC',
            'mobile' => '987654321',
        ])->assertCreated();

        $this->assertDatabaseHas('parties', ['document_number' => '20123456789', 'business_name' => 'Empresa SAC']);
    }

    public function test_quick_store_billing_saves_ubigeo_and_address(): void
    {
        $user = $this->createUserWithPermissions(['crear parties']);

        Ubigeo::create([
            'code' => '150101',
            'departamento' => 'LIMA',
            'provincia' => 'LIMA',
            'distrito' => 'LIMA',
        ]);

        $this->actingAs($user)->postJson(route('api.parties.quick-store'), [
            'role' => 'billing',
            'document_type' => '6',
            'document_number' => '20123456780',
            'business_name' => 'Empresa SAC',
            'mobile' => '987654321',
            'ubigeo_code' => '150101',
            'address' => 'Av. Los Andes 123',
        ])->assertCreated();

        $this->assertDatabaseHas('parties', [
            'document_number' => '20123456780',
            'ubigeo_code' => '150101',
            'address' => 'Av. Los Andes 123',
        ]);
    }

    public function test_quick_store_insurance_company_marks_flag(): void
    {
        $user = $this->createUserWithPermissions(['crear parties']);

        $this->actingAs($user)->postJson(route('api.parties.quick-store'), [
            'role' => 'insurance_company',
            'document_type' => '6',
            'document_number' => '20123456781',
            'business_name' => 'Seguros Perú',
            'mobile' => '987654321',
            'is_insurance_company' => 1,
        ])->assertCreated();

        $this->assertDatabaseHas('parties', ['document_number' => '20123456781', 'is_insurance_company' => 1]);
    }

    public function test_search_filters_only_insurance_companies(): void
    {
        $user = $this->createUserWithPermissions(['ver parties']);
        Party::factory()->company()->create(['document_number' => '20123456782', 'business_name' => 'Seguros Andina', 'is_insurance_company' => true]);
        Party::factory()->company()->create(['document_number' => '20123456783', 'business_name' => 'Transportes Lima', 'is_insurance_company' => false]);

        $this->actingAs($user)
            ->getJson(route('api.parties.search'))
            ->assertJsonCount(2); // los 2 creados

        $response = $this->actingAs($user)->getJson(route('api.parties.search') . '?is_insurance_company=1');
        $response->assertJsonCount(1);
        $response->assertJsonPath('0.business_name', 'Seguros Andina');
        $response->assertJsonPath('0.is_insurance_company', true);
    }

    public function test_search_with_q_and_insurance_filter_excludes_non_insurance(): void
    {
        $user = $this->createUserWithPermissions(['ver parties']);
        // Contacto normal cuyo nombre contiene "juan" (no aseguradora)
        Party::factory()->person()->create(['first_name' => 'Juan', 'last_name' => 'Pérez', 'document_number' => '12345678']);
        // Aseguradora cuyo nombre contiene "juan"
        Party::factory()->company()->create(['business_name' => 'Juan Seguros SAC', 'document_number' => '20123456790', 'is_insurance_company' => true]);

        $response = $this->actingAs($user)->getJson(route('api.parties.search') . '?q=juan&is_insurance_company=1');

        $response->assertOk();
        $response->assertJsonCount(1);
        $response->assertJsonPath('0.business_name', 'Juan Seguros SAC');
        $response->assertJsonPath('0.is_insurance_company', true);
        // El contacto no asegurador no debe aparecer en resultados con filtro de aseguradoras
        $response->assertJsonMissing(['first_name' => 'Juan', 'last_name' => 'Pérez']);
    }

    public function test_quick_store_driver_without_document_number_gets_tmp_document(): void
    {
        $user = $this->createUserWithPermissions(['crear parties']);

        $this->actingAs($user)->postJson(route('api.parties.quick-store'), [
            'role' => 'driver',
            'document_type' => '1',
            'first_name' => 'Pedro',
            'last_name' => 'Suárez',
            'mobile' => '987654322',
        ])->assertCreated();

        $this->assertDatabaseHas('parties', ['first_name' => 'Pedro', 'last_name' => 'Suárez', 'mobile' => '987654322']);
        $this->assertDatabaseMissing('parties', ['first_name' => 'Pedro', 'last_name' => 'Suárez', 'document_number' => null]);

        $tmpDoc = Party::where('first_name', 'Pedro')->where('last_name', 'Suárez')->value('document_number');
        $this->assertNotNull($tmpDoc);
        $this->assertStringStartsWith('TMP', $tmpDoc);
    }

    public function test_quick_store_owner_requires_document_number(): void
    {
        $user = $this->createUserWithPermissions(['crear parties']);

        $this->actingAs($user)->postJson(route('api.parties.quick-store'), [
            'role' => 'owner',
            'document_type' => '1',
            'first_name' => 'María',
            'last_name' => 'López',
            'mobile' => '987654323',
        ])->assertUnprocessable();
    }

    public function test_quick_store_billing_requires_document_number(): void
    {
        $user = $this->createUserWithPermissions(['crear parties']);

        $this->actingAs($user)->postJson(route('api.parties.quick-store'), [
            'role' => 'billing',
            'document_type' => '6',
            'business_name' => 'Empresa SAC',
            'mobile' => '987654324',
        ])->assertUnprocessable();
    }
}
