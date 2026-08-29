<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     */
    public function test_the_application_returns_a_successful_response(): void
    {
        // El home redirige al login (invitado) o al inventario (autenticado).
        $response = $this->get('/');

        $response->assertRedirect();

        // Smoke test real: la página de login responde 200.
        $this->get('/login')->assertOk();
    }
}
