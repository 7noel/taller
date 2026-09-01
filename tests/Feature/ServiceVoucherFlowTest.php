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
use App\Services\ServiceVoucherService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use RuntimeException;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ServiceVoucherFlowTest extends TestCase
{
    use RefreshDatabase;

    private ServiceVoucherService $service;
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

        $this->service = app(ServiceVoucherService::class);
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

    protected function createUserWithPermissions(array $permissions): User
    {
        $user = User::factory()->create(['establishment_id' => $this->establishment->id]);

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        $role = Role::firstOrCreate(['name' => 'ServiceVoucher Test Role']);
        $role->syncPermissions($permissions);
        $user->assignRole($role);

        return $user;
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

    private function makeProvider(): Party
    {
        return Party::factory()->company()->create(['is_supplier' => true]);
    }

    private function baseData(): array
    {
        return [
            'work_order_id' => $this->makeWorkOrder()->id,
            'provider_id' => $this->makeProvider()->id,
            'execution_date' => '2026-08-01',
            'description' => 'Desabolladura y pintura',
            'agreed_amount' => 1000,
            'discount_applied' => 0,
            'igv_rate' => 0.18,
            'detraction_rate' => 0.12,
        ];
    }

    public function test_creates_voucher_with_cst01_numbering_and_totals(): void
    {
        $voucher = $this->service->create($this->baseData());

        $this->assertEquals('CST01-000001', $voucher->document_sn);
        $this->assertEquals('pending', $voucher->status);
        $this->assertEquals(1000, $voucher->base_amount);
        $this->assertEquals(180, $voucher->igv_amount);
        $this->assertEquals(1180, $voucher->total_with_igv);
        $this->assertEquals(141.6, $voucher->detraction_amount);
        $this->assertEquals(1038.4, $voucher->total_payable);
    }

    public function test_discount_is_applied_before_igv_and_detraction(): void
    {
        $data = array_merge($this->baseData(), ['agreed_amount' => 1000, 'discount_applied' => 100]);
        $voucher = $this->service->create($data);

        $this->assertEquals(900, $voucher->base_amount);
        $this->assertEquals(162, $voucher->igv_amount);
        $this->assertEquals(1062, $voucher->total_with_igv);
        $this->assertEquals(127.44, $voucher->detraction_amount);
        $this->assertEquals(934.56, $voucher->total_payable);
    }
    public function test_complete_marks_voucher_as_completed(): void
    {
        $voucher = $this->service->create($this->baseData());

        $this->service->complete($voucher);

        $this->assertEquals('completed', $voucher->fresh()->status);
        $this->assertDatabaseHas('status_histories', [
            'subject_type' => ServiceVoucher::class,
            'to_status' => 'completed',
        ]);
    }

    public function test_cannot_edit_liquidated_voucher(): void
    {
        $voucher = $this->service->create($this->baseData());
        $voucher->update(['status' => ServiceVoucher::STATUS_LIQUIDATED]);

        $this->expectException(RuntimeException::class);
        $this->service->update($voucher, ['agreed_amount' => 500]);
    }

    public function test_cannot_delete_liquidated_voucher(): void
    {
        $voucher = $this->service->create($this->baseData());
        $voucher->update(['status' => ServiceVoucher::STATUS_LIQUIDATED]);

        $this->expectException(RuntimeException::class);
        $this->service->delete($voucher);
    }

    public function test_store_requires_permission(): void
    {
        $this->actingAs($this->createUserWithPermissions([]));

        $response = $this->post(route('service-vouchers.store'), $this->baseData());

        $response->assertForbidden();
    }

    public function test_store_creates_voucher_with_permission(): void
    {
        $this->actingAs($this->createUserWithPermissions(['crear vales de servicio', 'ver vales de servicio']));

        $response = $this->post(route('service-vouchers.store'), $this->baseData());

        $response->assertRedirect();
        $this->assertDatabaseHas('service_vouchers', ['document_sn' => 'CST01-000001']);
    }



    public function test_voucher_defaults_to_pen_with_rate_one(): void
    {
        $voucher = $this->service->create($this->baseData());

        $this->assertEquals('PEN', $voucher->currency);
        $this->assertEquals(1.0, $voucher->exchange_rate);
    }

    public function test_creates_voucher_in_usd_with_exchange_rate_and_no_taxes(): void
    {
        $data = array_merge($this->baseData(), [
            'currency' => 'USD',
            'exchange_rate' => 3.75,
            'igv_rate' => 0,
            'detraction_rate' => 0,
        ]);

        $voucher = $this->service->create($data);

        $this->assertEquals('USD', $voucher->currency);
        $this->assertEquals(3.75, $voucher->exchange_rate);
        $this->assertEquals(1000, $voucher->base_amount);
        $this->assertEquals(0, $voucher->igv_amount);
        $this->assertEquals(0, $voucher->detraction_amount);
        $this->assertEquals(1000, $voucher->total_payable);
    }

    public function test_update_preserves_currency_snapshot(): void
    {
        $voucher = $this->service->create(array_merge($this->baseData(), [
            'currency' => 'USD',
            'exchange_rate' => 3.75,
        ]));

        $updated = $this->service->update($voucher, ['agreed_amount' => 500]);

        $this->assertEquals('USD', $updated->currency);
        $this->assertEquals(3.75, $updated->exchange_rate);
        $this->assertEquals(500, $updated->base_amount);
    }

}

