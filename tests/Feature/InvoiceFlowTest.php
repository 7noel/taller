<?php

namespace Tests\Feature;

use App\Models\CompanySetting;
use App\Models\DocumentSeries;
use App\Models\DocumentType;
use App\Models\Establishment;
use App\Models\Estimate;
use App\Models\EstimateItem;
use App\Models\Invoice;
use App\Models\Party;
use App\Models\User;
use App\Models\Vehicle;
use App\Jobs\EmitInvoiceJob;
use App\Services\Facturacion\DispatchService;
use App\Services\Facturacion\InvoiceService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class InvoiceFlowTest extends TestCase
{
    use RefreshDatabase;

    private Establishment $establishment;
    private User $user;
    private InvoiceService $service;
    private Party $clientRuc;
    private Party $clientDni;

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

        CompanySetting::create([
            'ruc' => '20100000001',
            'razon_social' => 'Taller Mecánico SAC',
            'igv_rate' => 0.18, // la columna guarda la tasa como fracción (decimal 5,4)
            'facturador_provider' => 'nubefact',
            'facturador_api_url' => 'https://nubefact.test/api/v1/demo',
            'facturador_api_key' => 'token-demo',
        ]);

        // Series necesarias para emitir.
        foreach ([
            '01' => ['FTR1'],
            '03' => ['BLT1'],
            '07' => ['FTC1'],
            '08' => ['FTD1'],
            '09' => ['TR01'],
            'PRE' => ['PRE01'],
        ] as $code => $prefixes) {
            $dt = DocumentType::create([
                'code' => $code,
                'name' => "Tipo {$code}",
                'is_electronic' => $code !== 'PRE',
                'is_active' => true,
            ]);

            foreach ($prefixes as $prefix) {
                DocumentSeries::create([
                    'establishment_id' => $this->establishment->id,
                    'document_type_id' => $dt->id,
                    'prefix_serie' => $prefix,
                    'current_number' => 0,
                    'number_source' => 'LOCAL',
                    'status' => true,
                ]);
            }
        }

        $this->service = app(InvoiceService::class);

        $this->clientRuc = Party::create([
            'document_type' => '6', 'document_number' => '20604665966', 'business_name' => 'EMPRESA XYZ S.A.',
            'address' => 'Av. 2 de Mayo 100', 'email' => 'empresa@xyz.com',
        ]);

        $this->clientDni = Party::create([
            'document_type' => '1', 'document_number' => '41784439', 'first_name' => 'Juan', 'last_name' => 'Pérez',
            'address' => 'Jr. Los Olivos 200', 'email' => 'juan@mail.com',
        ]);
    }

    protected function makeEstimate(float $total = 1000, float $discount = 0, ?float $franchise = null, ?Vehicle $vehicle = null, ?int $number = null): Estimate
    {
        $vehicle = $vehicle ?? Vehicle::factory()->create();
        $number = $number ?? Estimate::max('document_number') + 1;

        $estimate = Estimate::create([
            'establishment_id' => $this->establishment->id,
            'vehicle_id' => $vehicle->id,
            'client_id' => $this->clientRuc->id,
            'insurance_company_id' => null,
            'document_type_code' => 'PRE',
            'document_serie' => 'PRE01',
            'document_number' => $number,
            'document_sn' => sprintf('PRE01-%06d', $number),
            'currency' => 'PEN',
            'status' => 'approved_client',
            'subtotal' => $total,
            'discount' => $discount,
            'taxable_base' => $total - $discount,
            'iva' => ($total - $discount) * 0.18,
            'total' => ($total - $discount) * 1.18,
            'franchise_amount' => $franchise,
            'created_by' => $this->user->id,
        ]);

        EstimateItem::create([
            'estimate_id' => $estimate->id,
            'item_type' => 'service',
            'description' => 'Reparación de motor',
            'quantity' => 1,
            'unit_price' => $total,
            'discount_amount' => 0,
            'net_line' => $total,
            'iva_line' => $total * 0.18,
            'total_line' => $total * 1.18,
            'uom' => 'ZZ',
            'sort_order' => 0,
        ]);

        return $estimate;
    }

    public function test_creates_free_invoice_from_items(): void
    {
        $invoice = $this->service->createFree(
            ['party_id' => $this->clientDni->id, 'invoice_date' => now()->toDateString()],
            [['description' => 'Servicio de diagnóstico', 'quantity' => 1, 'unit_price' => 150, 'uom' => 'ZZ']]
        );

        $this->assertSame(Invoice::DOC_RECEIPT, $invoice->document_type_code); // DNI → boleta
        $this->assertSame(Invoice::TYPE_FREE, $invoice->invoice_type);
        $this->assertSame(150.0, $invoice->subtotal);
        $this->assertSame(27.0, $invoice->iva);
        $this->assertSame(177.0, $invoice->total);
        $this->assertCount(1, $invoice->items);
    }

    public function test_ruc_party_gets_invoice_not_receipt(): void
    {
        $invoice = $this->service->createFree(
            ['party_id' => $this->clientRuc->id],
            [['description' => 'Mantenimiento', 'quantity' => 1, 'unit_price' => 100, 'uom' => 'ZZ']]
        );

        $this->assertSame(Invoice::DOC_INVOICE, $invoice->document_type_code);
    }

    public function test_advance_and_closing_regularizes_advances(): void
    {
        $estimate = $this->makeEstimate(1000);

        $advance = $this->service->createFromEstimates(
            [$estimate->id],
            ['invoice_type' => 'advance', 'party_id' => $this->clientRuc->id, 'advance_amount' => 300]
        );

        $this->assertSame(4, $advance->sunat_transaction);
        $this->assertSame(300.0, $advance->total);
        $this->assertCount(1, $advance->items);

        // Cierre: agrupa el servicio + línea de regularización del adelanto.
        $closing = $this->service->createFromEstimates(
            [$estimate->id],
            ['invoice_type' => 'regular', 'party_id' => $this->clientRuc->id]
        );

        $this->assertSame(4, $closing->sunat_transaction);
        $this->assertSame(254.24, round($closing->total_advances, 2)); // 300 sin IGV
        $this->assertSame(1, $closing->items->where('is_advance_line', true)->count());
        $this->assertSame(880.0, round($closing->total, 2)); // 1000*1.18 - 300
        $this->assertSame(1, $closing->estimates()->count());
    }

    public function test_global_discount_is_prorated_when_regularizing_advances(): void
    {
        $estimate = $this->makeEstimate(1000, discount: 100); // descuento global 100

        $this->service->createFromEstimates(
            [$estimate->id],
            ['invoice_type' => 'advance', 'party_id' => $this->clientRuc->id, 'advance_amount' => 300]
        );

        $closing = $this->service->createFromEstimates(
            [$estimate->id],
            ['invoice_type' => 'regular', 'party_id' => $this->clientRuc->id]
        );

        $this->assertSame(0, $closing->discounts->where('code', '02')->count());
        $this->assertGreaterThan(0, $closing->items->first()->discount);
        $this->assertSame(762.0, round($closing->total, 2)); // (1000-100)*1.18 - 300
    }

    public function test_insurance_invoice_is_total_minus_franchise(): void
    {
        $estimate = $this->makeEstimate(2000, franchise: 400);

        $insurance = $this->service->createFromEstimates(
            [$estimate->id],
            ['invoice_type' => 'insurance', 'party_id' => $this->clientRuc->id]
        );

        $this->assertSame(1, $insurance->discounts->where('code', '02')->count());
        $this->assertSame(1960.0, round($insurance->total, 2)); // 2000*1.18 - 400
    }


    public function test_emits_invoice_via_nubefact_and_persists_response(): void
    {
        Http::fake([
            'nubefact.test/*' => Http::response([
                'serie' => 'FTR1',
                'numero' => 1,
                'aceptada_por_sunat' => true,
                'sunat_description' => null,
                'enlace_del_pdf' => 'https://nubefact.test/cpe/abc.pdf',
                'enlace_del_xml' => 'https://nubefact.test/cpe/abc.xml',
                'cadena_para_codigo_qr' => 'QR-STRING',
            ]),
        ]);

        $invoice = $this->service->createFree(
            ['party_id' => $this->clientRuc->id],
            [['description' => 'Reparación', 'quantity' => 1, 'unit_price' => 500, 'uom' => 'ZZ']]
        );

        $emitted = $this->service->emit($invoice);

        $this->assertSame('FTR1-000001', $emitted->document_sn);
        $this->assertSame(Invoice::STATUS_EMITTED, $emitted->status);
        $this->assertTrue($emitted->accepted_by_sunat);
        $this->assertSame('https://nubefact.test/cpe/abc.pdf', $emitted->enlace_pdf);

        Http::assertSent(function ($request) {
            $payload = $request->data();

            return $payload['operacion'] === 'generar_comprobante'
                && $payload['serie'] === 'FTR1'
                && (float) $payload['total'] === 590.0;
        });
    }

    public function test_creates_credit_note_from_invoice(): void
    {
        Http::fake([
            'nubefact.test/*' => Http::response(['serie' => 'FTR1', 'numero' => 1, 'aceptada_por_sunat' => true]),
        ]);

        $invoice = $this->service->createFree(
            ['party_id' => $this->clientRuc->id],
            [['description' => 'Reparación', 'quantity' => 1, 'unit_price' => 500, 'uom' => 'ZZ']]
        );
        $invoice = $this->service->emit($invoice);

        $note = $this->service->createNote($invoice, Invoice::DOC_CREDIT_NOTE, 'Error en la descripción', 118.0);

        $this->assertSame(Invoice::DOC_CREDIT_NOTE, $note->document_type_code);
        $this->assertSame('FTC1', $note->document_serie);
        $this->assertSame('01', $note->documento_que_se_modifica_tipo);
        $this->assertSame('FTR1', $note->documento_que_se_modifica_serie);
        $this->assertSame($invoice->id, $note->related_invoice_id);
        $this->assertSame(118.0, round($note->total, 2));
    }

    public function test_dispatch_service_creates_and_emits(): void
    {
        Http::fake([
            'nubefact.test/*' => Http::response([
                'serie' => 'TR01',
                'numero' => 1,
                'aceptada_por_sunat' => true,
                'enlace_del_pdf' => 'https://nubefact.test/guias/abc.pdf',
            ]),
        ]);

        $service = app(DispatchService::class);
        $dispatch = $service->create([
            'party_id' => $this->clientRuc->id,
            'motivo_traslado' => '01',
            'modo_transporte' => '02',
            'fecha_de_traslado' => now()->toDateString(),
            'punto_partida_direccion' => 'Av. Taller 100',
            'punto_llegada_direccion' => 'Av. Cliente 200',
            'vehiculo_placa' => 'ABC123',
        ], [
            ['description' => 'Vehículo reparado', 'quantity' => 1, 'uom' => 'NIU'],
        ]);

        $this->assertSame('draft', $dispatch->status);
        $this->assertCount(1, $dispatch->items);

        $emitted = $service->emit($dispatch);

        $this->assertSame('TR01-000001', $emitted->document_sn);
        $this->assertSame('emitted', $emitted->status);
    }

    public function test_emit_invoice_job_emits_via_queue(): void
    {
        Http::fake([
            'nubefact.test/*' => Http::response(['serie' => 'FTR1', 'numero' => 1, 'aceptada_por_sunat' => true]),
        ]);

        $invoice = $this->service->createFree(
            ['party_id' => $this->clientRuc->id],
            [['description' => 'Reparación', 'quantity' => 1, 'unit_price' => 500, 'uom' => 'ZZ']]
        );

        // En tests QUEUE_CONNECTION=sync → el job se ejecuta al instante.
        EmitInvoiceJob::dispatchSync($invoice->loadMissing(['party', 'items', 'discounts']));

        $this->assertSame('FTR1-000001', $invoice->fresh()->document_sn);
        $this->assertSame(Invoice::STATUS_EMITTED, $invoice->fresh()->status);
    }

    public function test_multiple_estimates_from_different_vehicles_become_one_invoice(): void
    {
        $estimateA = $this->makeEstimate(500); // vehículo A
        $estimateB = $this->makeEstimate(700); // vehículo B (flota)

        $invoice = $this->service->createFromEstimates(
            [$estimateA->id, $estimateB->id],
            ['invoice_type' => 'regular', 'party_id' => $this->clientRuc->id]
        );

        $this->assertSame(2, $invoice->estimates()->count());
        $this->assertSame(2, $invoice->items()->count());
        // Trazabilidad multi-vehículo: placas en observaciones.
        $this->assertStringContainsString('Placas:', (string) $invoice->observations);
        // Total = 500*1.18 + 700*1.18
        $this->assertSame(1416.0, round($invoice->total, 2));
    }

    public function test_rejects_double_regular_closing(): void
    {
        $estimate = $this->makeEstimate(1000);

        $this->service->createFromEstimates(
            [$estimate->id],
            ['invoice_type' => 'regular', 'party_id' => $this->clientRuc->id]
        );

        $this->expectException(\InvalidArgumentException::class);
        $this->service->createFromEstimates(
            [$estimate->id],
            ['invoice_type' => 'regular', 'party_id' => $this->clientRuc->id]
        );
    }

    public function test_rejects_advance_over_budget(): void
    {
        $estimate = $this->makeEstimate(1000);

        $this->service->createFromEstimates(
            [$estimate->id],
            ['invoice_type' => 'advance', 'party_id' => $this->clientRuc->id, 'advance_amount' => 700]
        );

        $this->expectException(\InvalidArgumentException::class);
        $this->service->createFromEstimates(
            [$estimate->id],
            ['invoice_type' => 'advance', 'party_id' => $this->clientRuc->id, 'advance_amount' => 500]
        ); // 700 + 500 > 1180 (total con IGV)
    }

    public function test_related_endpoint_returns_same_vehicle_estimates(): void
    {
        $user = \App\Models\User::factory()->create(['establishment_id' => $this->establishment->id]);
        \Spatie\Permission\Models\Permission::firstOrCreate(['name' => 'ver presupuestos']);
        $role = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'Invoice Related Role']);
        $role->syncPermissions(['ver presupuestos']);
        $user->assignRole($role);
        $this->actingAs($user);

        $vehicle = \App\Models\Vehicle::factory()->create();
        $estimateA = $this->makeEstimate(500, vehicle: $vehicle);
        $this->makeEstimate(700, vehicle: $vehicle); // mismo vehículo

        $response = $this->getJson(route('api.estimates.related', ['estimate_id' => $estimateA->id]));

        $response->assertOk();
        $this->assertCount(2, $response->json());
        $this->assertArrayHasKey('text', $response->json()[0]);
    }

    public function test_rejects_invoice_with_estimates_in_different_currencies(): void
    {
        $penEstimate = $this->makeEstimate(1000);

        $usdEstimate = $this->makeEstimate(500);
        $usdEstimate->update(['currency' => 'USD']);

        $this->expectException(\InvalidArgumentException::class);
        $this->service->createFromEstimates(
            [$penEstimate->id, $usdEstimate->id],
            ['invoice_type' => 'regular', 'party_id' => $this->clientRuc->id]
        );
    }
}

