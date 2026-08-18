<?php

namespace Tests\Unit;

use App\Models\Establishment;
use App\Models\Party;
use App\Models\User;
use App\Services\PartyService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PartyServiceTest extends TestCase
{
    use RefreshDatabase;

    private Establishment $establishment;

    protected function setUp(): void
    {
        parent::setUp();
        $this->establishment = Establishment::create([
            'name' => 'Sede Test', 'address' => 'Av. Test', 'phone' => '999', 'email' => 't@t.com', 'code' => 'TST',
        ]);
    }

    private function createParty(): Party
    {
        return Party::factory()->create(['establishment_id' => $this->establishment->id]);
    }

    public function test_create_sets_created_and_updated_by(): void
    {
        $user = User::factory()->create(['establishment_id' => $this->establishment->id]);
        $this->actingAs($user);

        $party = (new PartyService())->create([
            'type' => 'person',
            'document_type' => 'DNI',
            'document_number' => '12345679',
            'first_name' => 'Ana',
            'last_name' => 'Gómez',
            'establishment_id' => $this->establishment->id,
        ]);

        $this->assertEquals($user->id, $party->created_by);
        $this->assertEquals($user->id, $party->updated_by);
    }

    public function test_create_person_clears_business_name(): void
    {
        $user = User::factory()->create(['establishment_id' => $this->establishment->id]);
        $this->actingAs($user);

        $party = (new PartyService())->create([
            'type' => 'person',
            'document_type' => 'DNI',
            'document_number' => '12345678',
            'first_name' => 'Ana',
            'last_name' => 'Gómez',
            'business_name' => 'Debería quedar null',
            'establishment_id' => $this->establishment->id,
        ]);

        $this->assertNull($party->business_name);
    }

    public function test_delete_soft_deletes_party(): void
    {
        $user = User::factory()->create(['establishment_id' => $this->establishment->id]);
        $this->actingAs($user);

        $party = $this->createParty();

        $this->assertTrue((new PartyService())->delete($party));
        $this->assertSoftDeleted('parties', ['id' => $party->id]);
    }
}