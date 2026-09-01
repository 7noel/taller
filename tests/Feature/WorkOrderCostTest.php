<?php

namespace Tests\Feature;

use App\Models\Establishment;
use App\Models\Estimate;
use App\Models\EstimateItem;
use App\Models\Party;
use App\Models\ServiceVoucher;
use App\Models\ThirdPartyOrder;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\WorkOrder;
use App\Models\WorkOrderAssignment;
use App\Models\WorkOrderSubstage;
use App\Services\WorkOrderCostService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WorkOrderCostTest extends TestCase
{
    use RefreshDatabase;

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
    }

    private function makeWorkOrder(): WorkOrder
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
            'status' => 'in_progress',
        ]);
    }

    private function makeEstimate(WorkOrder $workOrder, array $overrides = []): Estimate
    {
        $number = ++self::$counter;

        return Estimate::create(array_merge([
            'vehicle_id' => $workOrder->vehicle_id,
            'client_id' => $workOrder->client_id,
            'establishment_id' => $this->establishment->id,
            'document_type_code' => 'PR',
            'document_serie' => 'PR01',
            'document_number' => $number,
            'document_sn' => sprintf('PR01-%06d', $number),
            'status' => 'approved_client',
            'work_order_id' => $workOrder->id,
            'currency' => 'PEN',
            'exchange_rate' => 1,
            'subtotal' => 1000,
            'iva' => 180,
            'total' => 1180,
        ], $overrides));
    }

    private function makeVoucher(WorkOrder $workOrder, float $base, string $currency = 'PEN', float $rate = 1): ServiceVoucher
    {
        return ServiceVoucher::create([
            'establishment_id' => $this->establishment->id,
            'work_order_id' => $workOrder->id,
            'provider_id' => Party::factory()->company()->create(['is_supplier' => true])->id,
            'execution_date' => '2026-08-01',
            'description' => 'Servicio tercerizado',
            'agreed_amount' => $base,
            'discount_applied' => 0,
            'base_amount' => $base,
            'igv_rate' => 0,
            'igv_amount' => 0,
            'total_with_igv' => $base,
            'detraction_rate' => 0,
            'detraction_amount' => 0,
            'total_payable' => $base,
            'status' => 'completed',
            'currency' => $currency,
            'exchange_rate' => $rate,
        ]);
    }

    public function test_summary_with_pen_estimate_and_mixed_currency_costs(): void
    {
        $workOrder = $this->makeWorkOrder();
        $estimate = $this->makeEstimate($workOrder);

        EstimateItem::create([
            'estimate_id' => $estimate->id,
            'item_type' => 'part',
            'description' => 'Parachoques',
            'quantity' => 2,
            'unit_price' => 150,
            'cost_price' => 100,
            'sort_order' => 1,
        ]);

        ThirdPartyOrder::create([
            'estimate_id' => $estimate->id,
            'description' => 'Cromado a terceros',
            'amount_without_iva' => 200,
            'provider_name' => 'Cromados SAC',
            'currency' => 'PEN',
            'exchange_rate' => 1,
        ]);

        $substage = WorkOrderSubstage::create(['name' => 'Planchado', 'order' => 1]);
        WorkOrderAssignment::create([
            'work_order_id' => $workOrder->id,
            'substage_id' => $substage->id,
            'user_id' => $this->user->id,
            'hours' => 2,
            'cost' => 50,
            'currency' => 'PEN',
            'exchange_rate' => 1,
            'status' => 'done',
        ]);

        $this->makeVoucher($workOrder, 100, 'USD', 3.5); // 350 PEN
        $this->makeVoucher($workOrder, 100, 'PEN', 1);   // 100 PEN

        $summary = app(WorkOrderCostService::class)->summary($workOrder);

        $this->assertEquals('PEN', $summary['display_currency']);
        $this->assertEquals(1180, $summary['income_pen']);
        $this->assertEquals(1180, $summary['income']);

        // 200 (repuestos) + 200 (OC) + 50 (mano de obra) + 350 + 100 (vales) = 900
        $this->assertEquals(200, $summary['components']['parts']['amount_pen']);
        $this->assertEquals(200, $summary['components']['third_party']['amount_pen']);
        $this->assertEquals(50, $summary['components']['assignments']['amount_pen']);
        $this->assertEquals(450, $summary['components']['vouchers']['amount_pen']);
        $this->assertTrue($summary['components']['vouchers']['mixed_currency']);

        $this->assertEquals(900, $summary['total_cost_pen']);
        $this->assertEquals(280, $summary['profit_pen']);
        $this->assertEquals(23.73, $summary['margin']);
    }

    public function test_summary_with_usd_estimate_displays_in_usd(): void
    {
        $workOrder = $this->makeWorkOrder();
        $this->makeEstimate($workOrder, [
            'currency' => 'USD',
            'exchange_rate' => 3.5,
            'subtotal' => 1000,
            'iva' => 0,
            'total' => 1000,
        ]);

        $this->makeVoucher($workOrder, 100, 'PEN', 1); // 100 PEN = 28.57 USD

        $summary = app(WorkOrderCostService::class)->summary($workOrder);

        $this->assertEquals('USD', $summary['display_currency']);
        $this->assertEquals(1000, $summary['income']);
        $this->assertEquals(3500, $summary['income_pen']);
        $this->assertEquals(28.57, $summary['total_cost']);
        $this->assertEquals(100, $summary['total_cost_pen']);
        $this->assertEquals(971.43, $summary['profit']);
        $this->assertEquals(3400, $summary['profit_pen']);
        $this->assertEquals(97.14, $summary['margin']);
    }
}
