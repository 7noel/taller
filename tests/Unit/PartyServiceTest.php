<?php

namespace Tests\Unit;

use App\Models\Party;
use App\Models\User;
use App\Services\PartyService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PartyServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_create_sets_created_and_updated_by(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $service = new PartyService();
        $party = $service->create([
            'type' => 'person',
            'document_type' => 'DNI',
            'document_number' => '12345679',
            'first_name' => 'Ana',
            'last_name' => 'Gómez',
        ]);

        $this->assertEquals($user->id, $party->created_by);
        $this->assertEquals($user->id, $party->updated_by);
    }

    public function test_create_person_clears_business_name(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $service = new PartyService();
        $party = $service->create([
            'type' => 'person',
            'document_type' => 'DNI',
            'document_number' => '12345678',
            'first_name' => 'Ana',
            'last_name' => 'Gómez',
            'business_name' => 'Debería quedar null',
        ]);

        $this->assertNull($party->business_name);
    }

    public function test_delete_soft_deletes_party(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $party = Party::factory()->create();

        $service = new PartyService();
        $result = $service->delete($party);

        $this->assertTrue($result);
        $this->assertSoftDeleted('parties', ['id' => $party->id]);
    }
}