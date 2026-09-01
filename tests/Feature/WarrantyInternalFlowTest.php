<?php

namespace Tests\Feature;

use App\Models\DocumentSeries;
use App\Models\DocumentType;
use App\Models\Establishment;
use App\Models\Estimate;
use App\Models\EstimateItem;
use App\Models\Party;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\WorkOrder;
use App\Models\WorkOrderInternalExpense;
use App\Services\EstimateService;
use App\Services\Facturacion\InvoiceService;
use App\Services\WorkOrderCostService;
use App\Services\WorkOrderService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use InvalidArgumentException;
use RuntimeException;
use Tests\TestCase;

/**
 * Flujo de garantías, gastos internos del taller y siniestros por
 * responsabilidad propia: flags is_chargeable/liability, reapertura de OT,
 * exclusión de facturación y reflejo en el resumen de costos.
 */
class WarrantyInternalFlowTest extends TestCase
{
    use RefreshDatabase;

    private EstimateService $estimates;
    private WorkOrderService $workOrders;
    private Establishment $establishment;
    private User $user;
    private static int $counter = 0;

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

        // El servicio de numeración requiere la serie PRE01 para asignar document_number.
        $documentType = DocumentType::create([
            'code' => 'PRE',
            'name' => 'Presupuesto',
            'is_electronic' => false,
            'is_active' => true,
        ]);
        DocumentSeries::create([
            'establishment_id' => $this->establishment->id,
            'document_type_id' => $documentType->id,
            'prefix_serie' => 'PRE01',
            'current_number' => 0,
            'number_source' => 'LOCAL',
            'status' => true,
        ]);

        $this->estimates = app(EstimateService::class);
        $this->workOrders = app(WorkOrderService::class);
    }

    private function makeEstimate(array $overrides = []): Estimate
    {
        $vehicle = Vehicle::factory()->create();
        $client = Party::factory()->create();

        return $this->estimates->create(array_merge([
            'vehicle_id' => $vehicle->id,
            'client_id' => $client->id,
            'service_type' => 'siniestro',
            'establishment_id' => $this->establishment->id,
            'items' => [
                ['description' => 'Planchado y pintura', 'quantity' => 1, 'unit_price' => 1000, 'cost_price' => 400],
            ],
        ], $overrides));
    }

    private function makeWorkOrder(string $status = 'in_progress'): WorkOrder
    {
        $number = ++self::$counter;

        return WorkOrder::create([
            'vehicle_id' => Vehicle::factory()->create()->id,
            'client_id' => Party::factory()->create()->id,
            'establishment_id' => $this->establishment->id,
            'document_type_code' => 'OT',
            'document_serie' => 'OT01',
            'document_number' => $number,
            'document_sn' => sprintf('OT01-%06d', $number),
            'status' => $status,
        ]);
    }

    public function test_warranty_estimate_forced_flags_and_status(): void
    {
        $original = $this->makeEstimate(['currency' => 'USD', 'exchange_rate' => 3.5]);
        $original->update(['status' => 'approved_insurance']);

        $warranty = $this->estimates->create([
            'vehicle_id' => $original->vehicle_id,
            'client_id' => $original->client_id,
            'service_type' => 'correctivo', // se sobreescribe a 'garantia'
            'establishment_id' => $this->establishment->id,
            'warranty_of_estimate_id' => $original->id,
            'items' => [
                ['description' => 'Repintado por garantía', 'quantity' => 1, 'unit_price' => 300, 'cost_price' => 120],
            ],
        ]);

        $this->assertSame('garantia', $warranty->service_type);
        $this->assertFalse($warranty->is_chargeable);
        $this->assertSame('workshop', $warranty->liability);
        $this->assertSame('in_repair', $warranty->status);
        $this->assertSame((int) $original->id, (int) $warranty->warranty_of_estimate_id);
        $this->assertTrue($warranty->is_garantia);
        // Moneda y T.C. heredados del presupuesto original.
        $this->assertSame('USD', $warranty->currency);
        $this->assertSame(3.5, (float) $warranty->exchange_rate);
        $this->assertNotNull($warranty->document_sn);
        $this->assertStringStartsWith('PRE01-', $warranty->document_sn);
    }

    public function test_warranty_rejects_different_vehicle(): void
    {
        $original = $this->makeEstimate();

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('mismo vehículo');

        $this->estimates->create([
            'vehicle_id' => Vehicle::factory()->create()->id, // otro vehículo
            'client_id' => $original->client_id,
            'service_type' => 'siniestro',
            'establishment_id' => $this->establishment->id,
            'warranty_of_estimate_id' => $original->id,
        ]);
    }

    public function test_warranty_of_warranty_rejected(): void
    {
        $original = $this->makeEstimate();
        $warranty = $this->estimates->create([
            'vehicle_id' => $original->vehicle_id,
            'client_id' => $original->client_id,
            'service_type' => 'garantia',
            'establishment_id' => $this->establishment->id,
            'warranty_of_estimate_id' => $original->id,
        ]);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('garantía de una garantía');

        $this->estimates->create([
            'vehicle_id' => $original->vehicle_id,
            'client_id' => $original->client_id,
            'service_type' => 'garantia',
            'establishment_id' => $this->establishment->id,
            'warranty_of_estimate_id' => $warranty->id,
        ]);
    }

    public function test_warranty_cannot_enter_client_approval_gate(): void
    {
        $original = $this->makeEstimate();
        $warranty = $this->estimates->create([
            'vehicle_id' => $original->vehicle_id,
            'client_id' => $original->client_id,
            'service_type' => 'garantia',
            'establishment_id' => $this->establishment->id,
            'warranty_of_estimate_id' => $original->id,
        ]);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('no pasa por aprobación');

        // Primero se revierte a borrador (transición válida desde in_repair) y
        // luego se intenta entrar al gate del cliente, que debe estar bloqueado.
        $this->estimates->changeStatus($warranty, 'draft');
        $this->estimates->changeStatus($warranty, 'sent_client');
    }

    public function test_warranty_cannot_be_invoiced(): void
    {
        $original = $this->makeEstimate();
        $warranty = $this->estimates->create([
            'vehicle_id' => $original->vehicle_id,
            'client_id' => $original->client_id,
            'service_type' => 'garantia',
            'establishment_id' => $this->establishment->id,
            'warranty_of_estimate_id' => $original->id,
        ]);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('no facturable');

        app(InvoiceService::class)->createFromEstimates([$warranty->id], [
            'invoice_type' => 'free',
            'party_id' => 999999,
        ]);
    }

    public function test_cost_summary_excludes_warranty_income_and_counts_internal_expenses(): void
    {
        $workOrder = $this->makeWorkOrder();

        // Presupuesto facturable: 1180 PEN de ingreso.
        $chargeable = $this->makeEstimate(['work_order_id' => $workOrder->id]);
        $chargeable->update(['status' => 'approved_client', 'subtotal' => 1000, 'iva' => 180, 'total' => 1180]);

        // Garantía no facturable: total informativo 118, costo de repuesto 200.
        $this->makeEstimate([
            'work_order_id' => $workOrder->id,
            'service_type' => 'garantia',
            'is_chargeable' => false,
            'liability' => 'workshop',
            'subtotal' => 100,
            'iva' => 18,
            'total' => 118,
        ]);
        $warranty = $workOrder->estimates()->where('is_chargeable', false)->firstOrFail();
        EstimateItem::create([
            'estimate_id' => $warranty->id,
            'item_type' => 'part',
            'description' => 'Parachoques dañado por el taller',
            'quantity' => 1,
            'unit_price' => 118,
            'cost_price' => 200,
            'sort_order' => 1,
        ]);

        // Gasto interno asumido por el taller.
        $this->workOrders->addInternalExpense($workOrder, [
            'type' => 'scratch',
            'description' => 'Arañazo en puerta durante prueba de ruta',
            'amount' => 150,
            'currency' => 'PEN',
            'exchange_rate' => 1,
            'responsible_user_id' => $this->user->id,
            'occurred_at' => now()->toDateString(),
        ]);

        $summary = app(WorkOrderCostService::class)->summary($workOrder);

        // El ingreso solo suma el presupuesto facturable (1180), no la garantía.
        $this->assertEquals(1180, $summary['income_pen']);
        $this->assertEquals(1180, $summary['income']);

        // El costo del repuesto de la garantía sí se cuenta (lo absorbe el taller).
        $this->assertEquals(200, $summary['components']['parts']['amount_pen']);

        // Los gastos internos aparecen como componente de costo.
        $this->assertArrayHasKey('internal_expenses', $summary['components']);
        $this->assertEquals(150, $summary['components']['internal_expenses']['amount_pen']);

        $this->assertEquals(350, $summary['total_cost_pen']);
        $this->assertEquals(830, $summary['profit_pen']);
    }

    public function test_internal_expense_lifecycle(): void
    {
        $workOrder = $this->makeWorkOrder();

        $expense = $this->workOrders->addInternalExpense($workOrder, [
            'type' => 'damaged_part',
            'description' => 'Repuesto malogrado al instalarlo',
            'amount' => 80,
            'currency' => 'PEN',
            'exchange_rate' => 1,
        ]);

        $this->assertInstanceOf(WorkOrderInternalExpense::class, $expense);
        $this->assertSame('Repuesto malogrado', $expense->type_label);
        $this->assertSame((int) $workOrder->id, (int) $expense->work_order_id);

        $this->assertTrue($this->workOrders->removeInternalExpense($workOrder, $expense));
        $this->assertNull(WorkOrderInternalExpense::find($expense->id));
    }

    public function test_closed_work_order_can_be_reopened(): void
    {
        $workOrder = $this->makeWorkOrder('closed');

        $this->assertTrue($workOrder->is_final);

        $reopened = $this->workOrders->reopen($workOrder, 'Reapertura por garantía');

        $this->assertSame('open', $reopened->status);
        $this->assertFalse($reopened->is_final);

        $history = $reopened->statusHistory()->where('to_status', 'open')->latest('id')->first();
        $this->assertNotNull($history);
        $this->assertSame('closed', $history->from_status);
    }
}
