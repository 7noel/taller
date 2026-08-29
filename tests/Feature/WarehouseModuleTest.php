<?php

namespace Tests\Feature;

use App\Models\DocumentSeries;
use App\Models\DocumentType;
use App\Models\Establishment;
use App\Models\InventoryGuide;
use App\Models\InventoryMovementReason;
use App\Models\Part;
use App\Models\Party;
use App\Models\PurchaseOrder;
use App\Models\User;
use App\Models\Warehouse;
use App\Models\WarehouseStock;
use App\Services\InventoryGuideService;
use App\Services\PurchaseOrderService;
use App\Services\StockService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use InvalidArgumentException;
use Tests\TestCase;

class WarehouseModuleTest extends TestCase
{
    use RefreshDatabase;

    private Establishment $establishment;
    private User $user;
    private Warehouse $warehouseA;
    private Warehouse $warehouseB;
    private Part $part;
    private Party $provider;

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

        $this->createDocumentSeries('OC', 'Orden de Compra', 'OC01');
        $this->createDocumentSeries('U2', 'Guía de Ingreso Establecimiento', 'NIA1');
        $this->createDocumentSeries('U3', 'Guía de Salida Establecimiento', 'NSA1');
        $this->createDocumentSeries('U4', 'Guía de Transferencia Establecimiento', 'NTA1');

        foreach ([
            ['02', 'Compra nacional', 'input'],
            ['10', 'Salida a producción', 'output'],
            ['11', 'Salida por transferencia entre almacenes', 'output'],
            ['21', 'Entrada por transferencia entre almacenes', 'input'],
            ['28', 'Ajuste por diferencia de inventario', 'output'],
        ] as [$code, $name, $type]) {
            InventoryMovementReason::create(['code' => $code, 'name' => $name, 'type' => $type]);
        }

        $this->warehouseA = Warehouse::create(['name' => 'Almacén A', 'code' => 'ALM-A', 'establishment_id' => $this->establishment->id, 'is_active' => true]);
        $this->warehouseB = Warehouse::create(['name' => 'Almacén B', 'code' => 'ALM-B', 'establishment_id' => $this->establishment->id, 'is_active' => true]);

        $this->part = Part::create([
            'name' => 'Filtro de aceite',
            'sku' => 'FO-001',
            'uom' => 'NIU',
            'min_stock' => 5,
            'cost_price' => 25,
            'sell_price' => 45,
            'cost_currency' => 'PEN',
            'currency' => 'PEN',
            'is_active' => true,
        ]);

        $this->provider = Party::factory()->company()->create(['is_supplier' => true]);
    }

    private function createDocumentSeries(string $code, string $name, string $prefix): void
    {
        $documentType = DocumentType::create(['code' => $code, 'name' => $name, 'is_electronic' => false, 'is_active' => true]);

        DocumentSeries::create([
            'establishment_id' => $this->establishment->id,
            'document_type_id' => $documentType->id,
            'prefix_serie' => $prefix,
            'current_number' => 0,
            'number_source' => 'LOCAL',
            'status' => true,
        ]);
    }

    public function test_purchase_order_receive_generates_nia1_and_stock_entry(): void
    {
        $po = app(PurchaseOrderService::class)->create([
            'provider_id' => $this->provider->id,
            'warehouse_id' => $this->warehouseA->id,
            'currency' => 'PEN',
            'items' => [['part_id' => $this->part->id, 'quantity' => 10, 'unit_cost' => 25, 'uom' => 'NIU']],
        ]);

        $this->assertEquals('OC01-000001', $po->document_sn);
        $this->assertEquals(250, $po->subtotal);

        $received = app(PurchaseOrderService::class)->receive($po, [
            'warehouse_id' => $this->warehouseA->id,
            'provider_invoice' => 'F001-123',
            'provider_guide' => 'G001-45',
        ]);

        $this->assertEquals('received', $received->status);
        $this->assertEquals('F001-123', $received->provider_invoice);

        $guide = InventoryGuide::where('document_type_code', 'U2')->first();
        $this->assertNotNull($guide);
        $this->assertEquals('NIA1-000001', $guide->document_sn);
        $this->assertEquals('02', $guide->movement_reason_code);
        $this->assertEquals($po->id, $guide->purchase_order_id);

        $stock = WarehouseStock::where('part_id', $this->part->id)->where('warehouse_id', $this->warehouseA->id)->first();
        $this->assertEquals(10, $stock->quantity);
        $this->assertEquals(25, $stock->average_cost);
    }

    public function test_transfer_creates_nta1_with_two_movements(): void
    {
        app(StockService::class)->registerMovement($this->part->id, $this->warehouseA->id, 'entry', 20, 25, 'PEN');

        $guide = app(InventoryGuideService::class)->createTransfer([
            'establishment_id' => $this->establishment->id,
            'origin_warehouse_id' => $this->warehouseA->id,
            'destination_warehouse_id' => $this->warehouseB->id,
            'movement_reason_code' => '21',
            'items' => [['part_id' => $this->part->id, 'quantity' => 5]],
        ]);

        $this->assertEquals('NTA1-000001', $guide->document_sn);
        $this->assertEquals(2, $guide->movements()->count());

        $a = WarehouseStock::where('part_id', $this->part->id)->where('warehouse_id', $this->warehouseA->id)->first();
        $b = WarehouseStock::where('part_id', $this->part->id)->where('warehouse_id', $this->warehouseB->id)->first();
        $this->assertEquals(15, $a->quantity);
        $this->assertEquals(5, $b->quantity);
        $this->assertEquals(25, $b->average_cost);
    }

    public function test_transfer_rejects_insufficient_stock(): void
    {
        app(StockService::class)->registerMovement($this->part->id, $this->warehouseA->id, 'entry', 2, 25, 'PEN');

        $this->expectException(InvalidArgumentException::class);

        app(InventoryGuideService::class)->createTransfer([
            'establishment_id' => $this->establishment->id,
            'origin_warehouse_id' => $this->warehouseA->id,
            'destination_warehouse_id' => $this->warehouseB->id,
            'items' => [['part_id' => $this->part->id, 'quantity' => 5]],
        ]);
    }

    public function test_adjustment_uses_reason_28_and_negative_generates_nsa1(): void
    {
        app(StockService::class)->registerMovement($this->part->id, $this->warehouseA->id, 'entry', 10, 25, 'PEN');

        $guide = app(InventoryGuideService::class)->createAdjustment([
            'establishment_id' => $this->establishment->id,
            'warehouse_id' => $this->warehouseA->id,
            'items' => [['part_id' => $this->part->id, 'quantity' => -2]],
        ]);

        $this->assertEquals('NSA1-000001', $guide->document_sn);
        $this->assertEquals('28', $guide->movement_reason_code);

        $stock = WarehouseStock::where('part_id', $this->part->id)->where('warehouse_id', $this->warehouseA->id)->first();
        $this->assertEquals(8, $stock->quantity);
    }

    public function test_output_guide_with_motivo_10_and_kardex_balance(): void
    {
        app(StockService::class)->registerMovement($this->part->id, $this->warehouseA->id, 'entry', 5, 25, 'PEN');

        $guide = app(InventoryGuideService::class)->createOutput([
            'establishment_id' => $this->establishment->id,
            'movement_reason_code' => '10',
            'origin_warehouse_id' => $this->warehouseA->id,
            'items' => [['part_id' => $this->part->id, 'quantity' => 2]],
        ]);

        $this->assertEquals('NSA1-000001', $guide->document_sn);
        $this->assertEquals('10', $guide->movement_reason_code);

        $kardex = app(StockService::class)->getKardex($this->part->id, $this->warehouseA->id);
        $this->assertEquals(3, $kardex['closing']['quantity']);
        $this->assertEquals(2, $kardex['movements']->count());
    }
}
