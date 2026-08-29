<?php

namespace Tests\Feature;

use App\Models\DocumentSeries;
use App\Models\DocumentType;
use App\Models\Establishment;
use App\Models\Party;
use App\Models\ServiceVoucher;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\WorkOrder;
use App\Services\ProviderSettlementService;
use App\Services\ServiceVoucherService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use RuntimeException;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ProviderSettlementFlowTest extends TestCase
{
    use RefreshDatabase;

    private ProviderSettlementService $service;
    private Establishment $establishment;
    private User $user;
    private static int $workOrderCounter = 0;

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

        $this->createDocumentSeries('CST', 'Comprobante de Servicio Tercerizado', 'CST01');
        $this->createDocumentSeries('LST', 'LiquidaciÃ³n de Servicios Tercerizados', 'LST01');

        $this->service = app(ProviderSettlementService::class);
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

    private function makeWorkOrder(): WorkOrder
    {
        $number = ++self::$workOrderCounter;

        return WorkOrder::create([
            'vehicle_id' => Vehicle::factory()->create()->id,
            'client_id' => Party::factory()->create()->id,
            'establishment_id' => $this->establishment->id,
            'document_type_code' => 'OT',
            'document_serie' => 'OT01',
            'document_number' => $number,
            'document_sn' => sprintf('OT01-%06d', $number),
            'status' => 'open',
        ]);
    }

    private function makeCompletedVoucher(Party $provider, float $amount, string $date = '2026-08-05'): ServiceVoucher
    {
        $voucher = app(ServiceVoucherService::class)->create([
            'work_order_id' => $this->makeWorkOrder()->id,
            'provider_id' => $provider->id,
            'execution_date' => $date,
            'description' => 'Servicio tercerizado',
            'agreed_amount' => $amount,
            'discount_applied' => 0,
            'igv_rate' => 0.18,
            'detraction_rate' => 0.12,
        ]);

        app(ServiceVoucherService::class)->complete($voucher);

        return $voucher->fresh();
    }

    private function makeSettlementData(Party $provider, array $voucherIds): array
    {
        return [
            'provider_id' => $provider->id,
            'period_start' => '2026-08-01',
            'period_end' => '2026-08-31',
            'global_discount' => 0,
            'voucher_ids' => $voucherIds,
        ];
    }
    public function test_creates_settlement_with_lst01_and_totals_from_vouchers(): void
    {
        $provider = Party::factory()->company()->create(['is_supplier' => true]);
        $v1 = $this->makeCompletedVoucher($provider, 1000);
        $v2 = $this->makeCompletedVoucher($provider, 500);

        $settlement = $this->service->create($this->makeSettlementData($provider, [$v1->id, $v2->id]));

        $this->assertEquals('LST01-000001', $settlement->document_sn);
        $this->assertEquals('draft', $settlement->status);
        $this->assertEquals(1500, $settlement->subtotal);
        $this->assertEquals(1500, $settlement->base_amount);
        $this->assertEquals(270, $settlement->igv_amount);
        $this->assertEquals(1770, $settlement->total_with_igv);
        $this->assertEquals(212.4, $settlement->detraction_amount);
        $this->assertEquals(1557.6, $settlement->total_payable);
        $this->assertEquals(2, $settlement->vouchers()->count());
        $this->assertEquals($settlement->id, $v1->fresh()->provider_settlement_id);
    }

    public function test_global_discount_is_applied_to_subtotal(): void
    {
        $provider = Party::factory()->company()->create(['is_supplier' => true]);
        $v1 = $this->makeCompletedVoucher($provider, 1000);
        $v2 = $this->makeCompletedVoucher($provider, 500);

        $data = $this->makeSettlementData($provider, [$v1->id, $v2->id]);
        $data['global_discount'] = 300;
        $data['discount_reason'] = 'Ajuste por material no usado';

        $settlement = $this->service->create($data);

        $this->assertEquals(1500, $settlement->subtotal);
        $this->assertEquals(1200, $settlement->base_amount);
        $this->assertEquals(216, $settlement->igv_amount);
        $this->assertEquals(1416, $settlement->total_with_igv);
        $this->assertEquals(169.92, $settlement->detraction_amount);
        $this->assertEquals(1246.08, $settlement->total_payable);
    }

    public function test_approve_then_pay_marks_vouchers_liquidated(): void
    {
        $provider = Party::factory()->company()->create(['is_supplier' => true]);
        $v1 = $this->makeCompletedVoucher($provider, 1000);

        $settlement = $this->service->create($this->makeSettlementData($provider, [$v1->id]));
        $this->service->approve($settlement);

        $this->assertEquals('approved', $settlement->fresh()->status);
        $this->assertNotNull($settlement->fresh()->approved_at);

        $this->service->pay($settlement->fresh());

        $this->assertEquals('paid', $settlement->fresh()->status);
        $this->assertNotNull($settlement->fresh()->paid_at);
        $this->assertEquals('liquidated', $v1->fresh()->status);
    }

    public function test_cannot_pay_draft_settlement(): void
    {
        $provider = Party::factory()->company()->create(['is_supplier' => true]);
        $v1 = $this->makeCompletedVoucher($provider, 1000);
        $settlement = $this->service->create($this->makeSettlementData($provider, [$v1->id]));

        $this->expectException(RuntimeException::class);
        $this->service->pay($settlement);
    }

    public function test_sync_vouchers_detaches_removed_vouchers_and_recalculates(): void
    {
        $provider = Party::factory()->company()->create(['is_supplier' => true]);
        $v1 = $this->makeCompletedVoucher($provider, 1000);
        $v2 = $this->makeCompletedVoucher($provider, 500);
        $settlement = $this->service->create($this->makeSettlementData($provider, [$v1->id, $v2->id]));

        $this->service->syncVouchers($settlement, [$v1->id]);

        $this->assertEquals(1, $settlement->fresh()->vouchers()->count());
        $this->assertNull($v2->fresh()->provider_settlement_id);
        $this->assertEquals(1000, $settlement->fresh()->subtotal);
        $this->assertEquals(1038.4, $settlement->fresh()->total_payable);
    }

    public function test_delete_draft_unlinks_vouchers(): void
    {
        $provider = Party::factory()->company()->create(['is_supplier' => true]);
        $v1 = $this->makeCompletedVoucher($provider, 1000);
        $settlement = $this->service->create($this->makeSettlementData($provider, [$v1->id]));

        $this->service->delete($settlement);

        $this->assertSoftDeleted('provider_settlements', ['id' => $settlement->id]);
        $this->assertNull($v1->fresh()->provider_settlement_id);
    }
    public function test_store_requires_permission(): void
    {
        $user = User::factory()->create(['establishment_id' => $this->establishment->id]);
        $role = Role::firstOrCreate(['name' => 'Settlement NoPerm Role']);
        $user->assignRole($role);
        $this->actingAs($user);

        $provider = Party::factory()->company()->create(['is_supplier' => true]);

        $response = $this->post(route('provider-settlements.store'), $this->makeSettlementData($provider, []));

        $response->assertForbidden();
    }

    public function test_store_creates_settlement_with_permission(): void
    {
        $permissions = ['crear liquidaciones de servicios', 'ver liquidaciones de servicios'];
        $user = User::factory()->create(['establishment_id' => $this->establishment->id]);
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }
        $role = Role::firstOrCreate(['name' => 'Settlement Test Role']);
        $role->syncPermissions($permissions);
        $user->assignRole($role);
        $this->actingAs($user);

        $provider = Party::factory()->company()->create(['is_supplier' => true]);

        $response = $this->post(route('provider-settlements.store'), $this->makeSettlementData($provider, []));

        $response->assertRedirect();
        $this->assertDatabaseHas('provider_settlements', ['document_sn' => 'LST01-000001']);
    }
}



