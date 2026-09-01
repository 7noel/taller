<?php

namespace Tests\Feature;

use App\Models\CheckIn;
use App\Models\DocumentSeries;
use App\Models\DocumentType;
use App\Models\Establishment;
use App\Models\Estimate;
use App\Models\Party;
use App\Models\User;
use App\Models\Vehicle;
use App\Services\EstimateService;
use App\Services\KanbanService;
use App\Services\WorkOrderService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Verifica el mapeo estado → columna del tablero Kanban:
 * - Una sola tarjeta por documento, siempre en la etapa vigente.
 * - El check-in aprobado pasa a Presupuesto y se oculta al crear presupuestos.
 * - El presupuesto aprobado por el cliente sale del tablero (va a la OT).
 * - La OT entregada sale del tablero; la entregada con pendientes queda en Reparación.
 */
class KanbanBoardTest extends TestCase
{
    use RefreshDatabase;

    private Establishment $establishment;
    private User $user;

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

        $this->user = User::factory()->create(['establishment_id' => $this->establishment->id]);
        $this->actingAs($this->user);

        $this->createDocumentSeries('PRE', 'Presupuesto', 'PRE01');
        $this->createDocumentSeries('OT', 'Orden de Trabajo', 'OT01');
    }

    private function createDocumentSeries(string $code, string $name, string $prefix): void
    {
        $documentType = DocumentType::create([
            'code' => $code,
            'name' => $name,
            'is_electronic' => false,
            'is_active' => true,
        ]);

        DocumentSeries::create([
            'establishment_id' => $this->establishment->id,
            'document_type_id' => $documentType->id,
            'prefix_serie' => $prefix,
            'current_number' => 0,
            'number_source' => 'LOCAL',
            'status' => true,
        ]);
    }

    private function makeCheckIn(Vehicle $vehicle, Party $client, string $status = 'approved'): CheckIn
    {
        return CheckIn::create([
            'vehicle_id' => $vehicle->id,
            'client_id' => $client->id,
            'establishment_id' => $this->establishment->id,
            'service_type' => 'preventivo',
            'status' => $status,
            'created_by' => $this->user->id,
        ]);
    }

    private function makeEstimate(Vehicle $vehicle, Party $client, string $status, ?CheckIn $checkIn = null): Estimate
    {
        $estimate = app(EstimateService::class)->create([
            'vehicle_id' => $vehicle->id,
            'client_id' => $client->id,
            'service_type' => 'preventivo',
            'establishment_id' => $this->establishment->id,
            'check_in_id' => $checkIn?->id,
            'items' => [
                ['description' => 'Mano de obra', 'quantity' => 1, 'unit_price' => 100],
            ],
        ]);

        $estimate->update(['status' => $status]);

        return $estimate->fresh();
    }

    private function cardsIn(string $column): array
    {
        foreach ($this->board()['columns'] as $col) {
            if ($col['key'] === $column) {
                return $col['cards']->toArray();
            }
        }

        return [];
    }

    private function board(): array
    {
        return app(KanbanService::class)->board($this->user);
    }

    private function assertNotOnBoard(string $type, int $id): void
    {
        foreach ($this->board()['columns'] as $col) {
            foreach ($col['cards'] as $card) {
                $this->assertFalse(
                    $card['type'] === $type && (int) $card['id'] === (int) $id,
                    "Card {$type}#{$id} should not be on the board."
                );
            }
        }
    }

    public function test_approved_check_in_without_estimates_moves_to_presupuesto(): void
    {
        $vehicle = Vehicle::factory()->create();
        $client = Party::factory()->create();
        $checkIn = $this->makeCheckIn($vehicle, $client, 'approved');

        $cards = $this->cardsIn('presupuesto');
        $this->assertCount(1, $cards);
        $this->assertSame('check_in', $cards[0]['type']);
        $this->assertSame((int) $checkIn->id, (int) $cards[0]['id']);
    }

    public function test_approved_check_in_is_hidden_once_an_estimate_exists(): void
    {
        $vehicle = Vehicle::factory()->create();
        $client = Party::factory()->create();
        $checkIn = $this->makeCheckIn($vehicle, $client, 'approved');
        $this->makeEstimate($vehicle, $client, 'draft', $checkIn);

        $this->assertNotOnBoard('check_in', $checkIn->id);
    }

    public function test_draft_estimate_in_presupuesto_and_approved_client_not_on_board(): void
    {
        $vehicle = Vehicle::factory()->create();
        $client = Party::factory()->create();
        $checkIn = $this->makeCheckIn($vehicle, $client, 'approved');

        $draft = $this->makeEstimate($vehicle, $client, 'draft', $checkIn);
        $approved = $this->makeEstimate($vehicle, $client, 'approved_client', $checkIn);

        $cards = $this->cardsIn('presupuesto');
        $this->assertCount(1, $cards);
        $this->assertSame('estimate', $cards[0]['type']);
        $this->assertSame((int) $draft->id, (int) $cards[0]['id']);

        $this->assertNotOnBoard('estimate', $approved->id);
    }

    public function test_sent_client_estimate_is_in_aprobacion(): void
    {
        $vehicle = Vehicle::factory()->create();
        $client = Party::factory()->create();
        $checkIn = $this->makeCheckIn($vehicle, $client, 'approved');
        $sent = $this->makeEstimate($vehicle, $client, 'sent_client', $checkIn);

        $cards = $this->cardsIn('aprobacion');
        $this->assertCount(1, $cards);
        $this->assertSame('estimate', $cards[0]['type']);
        $this->assertSame((int) $sent->id, (int) $cards[0]['id']);
    }

    public function test_delivered_pending_stays_in_reparacion_and_delivered_leaves_board(): void
    {
        $vehicle = Vehicle::factory()->create();
        $client = Party::factory()->create();
        $checkIn = $this->makeCheckIn($vehicle, $client, 'approved');
        $estimate = $this->makeEstimate($vehicle, $client, 'approved_client', $checkIn);

        $service = app(WorkOrderService::class);
        $workOrder = $service->createFromEstimates(collect([$estimate]));
        $workOrder = $service->changeStatus($workOrder, 'in_progress');
        $workOrder = $service->changeStatus($workOrder, 'delivered_pending');

        $cards = $this->cardsIn('reparacion');
        $this->assertCount(1, $cards);
        $this->assertSame('work_order', $cards[0]['type']);
        $this->assertSame((int) $workOrder->id, (int) $cards[0]['id']);

        $workOrder = $service->changeStatus($workOrder, 'in_progress');
        $workOrder = $service->changeStatus($workOrder, 'quality_control');
        $workOrder = $service->changeStatus($workOrder, 'ready_for_delivery');
        $workOrder = $service->changeStatus($workOrder, 'delivered');

        $this->assertNotOnBoard('work_order', $workOrder->id);
    }
}
