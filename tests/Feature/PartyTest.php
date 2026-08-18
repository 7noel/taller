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
}