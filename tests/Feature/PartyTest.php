<?php

namespace Tests\Feature;

use App\Models\Party;
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

        $role = Role::firstOrCreate(['name' => 'Test Role']);
        $role->syncPermissions($permissions);

        $user->assignRole($role);

        return $user;
    }

    public function test_index_requires_ver_parties_permission(): void
    {
        $user = $this->createUserWithPermissions(['ver parties']);

        $response = $this->actingAs($user)->get(route('parties.index'));

        $response->assertOk();
        $response->assertViewIs('parties.index');
    }

    public function test_unauthorized_user_cannot_access_parties(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('parties.index'));

        $response->assertForbidden();
    }

    public function test_party_can_be_created(): void
    {
        $user = $this->createUserWithPermissions(['ver parties', 'crear parties']);

        $response = $this->actingAs($user)->post(route('parties.store'), [
            'type' => 'person',
            'document_type' => 'DNI',
            'document_number' => '12345678',
            'first_name' => 'Juan',
            'last_name' => 'Pérez',
            'email' => 'juan@example.com',
            'phone' => '011234567',
            'mobile' => '987654321',
            'receive_promotions' => true,
        ]);

        $response->assertRedirect(route('parties.index'));

        $this->assertDatabaseHas('parties', [
            'document_number' => '12345678',
            'first_name' => 'Juan',
            'type' => 'person',
        ]);
    }

    public function test_party_validation_fails_without_document_number(): void
    {
        $user = $this->createUserWithPermissions(['crear parties']);

        $response = $this->actingAs($user)->post(route('parties.store'), [
            'type' => 'person',
            'document_type' => 'DNI',
            'first_name' => 'Juan',
            'last_name' => 'Pérez',
        ]);

        $response->assertSessionHasErrors('document_number');
    }

    public function test_party_can_be_updated(): void
    {
        $user = $this->createUserWithPermissions(['ver parties', 'editar parties']);

        $party = Party::factory()->create();

        $response = $this->actingAs($user)->put(route('parties.update', $party), [
            'type' => 'person',
            'document_type' => 'DNI',
            'document_number' => $party->document_number,
            'first_name' => 'María',
            'last_name' => 'López',
        ]);

        $response->assertRedirect(route('parties.index'));

        $this->assertDatabaseHas('parties', [
            'id' => $party->id,
            'first_name' => 'María',
        ]);
    }

    public function test_party_can_be_deleted(): void
    {
        $user = $this->createUserWithPermissions(['ver parties', 'eliminar parties']);

        $party = Party::factory()->create();

        $response = $this->actingAs($user)->delete(route('parties.destroy', $party));

        $response->assertRedirect(route('parties.index'));

        $this->assertSoftDeleted('parties', ['id' => $party->id]);
    }
}