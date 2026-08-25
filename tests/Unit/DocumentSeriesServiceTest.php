<?php

namespace Tests\Unit;

use App\Models\CompanySetting;
use App\Models\DocumentSeries;
use App\Models\DocumentType;
use App\Models\Establishment;
use App\Services\DocumentSeriesService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DocumentSeriesServiceTest extends TestCase
{
    use RefreshDatabase;

    private DocumentSeriesService $service;
    private Establishment $establishment;

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

        CompanySetting::create([
            'default_number_source' => 'LOCAL',
            'facturador_provider' => 'local',
        ]);

        $this->service = app(DocumentSeriesService::class);
    }

    public function test_generates_two_series_for_credit_note_and_debit_note(): void
    {
        DocumentType::create(['code' => '07', 'name' => 'Nota de Crédito', 'is_electronic' => true]);
        DocumentType::create(['code' => '08', 'name' => 'Nota de Débito', 'is_electronic' => true]);
        DocumentType::create(['code' => 'IV', 'name' => 'Inventario Vehicular', 'is_electronic' => false]);

        $this->service->generateSeriesForEstablishment($this->establishment->id);

        // '07' (NC) genera 2 series: FTC1 (factura) y BLC1 (boleta)
        $this->assertDatabaseHas('document_series', [
            'establishment_id' => $this->establishment->id,
            'prefix_serie' => 'FTC1',
        ]);
        $this->assertDatabaseHas('document_series', [
            'establishment_id' => $this->establishment->id,
            'prefix_serie' => 'BLC1',
        ]);

        // '08' (ND) genera 2 series: FTD1 (factura) y BLD1 (boleta)
        $this->assertDatabaseHas('document_series', [
            'establishment_id' => $this->establishment->id,
            'prefix_serie' => 'FTD1',
        ]);
        $this->assertDatabaseHas('document_series', [
            'establishment_id' => $this->establishment->id,
            'prefix_serie' => 'BLD1',
        ]);

        // 'IV' genera 1 serie: IV01
        $this->assertDatabaseHas('document_series', [
            'establishment_id' => $this->establishment->id,
            'prefix_serie' => 'IV01',
        ]);

        $this->assertDatabaseCount('document_series', 5);
    }

    public function test_get_next_number_for_specific_prefix(): void
    {
        DocumentType::create(['code' => '07', 'name' => 'Nota de Crédito', 'is_electronic' => true]);
        $this->service->generateSeriesForEstablishment($this->establishment->id);

        $ftc = $this->service->getNextNumber($this->establishment->id, '07', 'FTC1');
        $this->assertEquals(1, $ftc['number']);
        $this->assertEquals('FTC1-000001', $ftc['sn']);
        $this->assertEquals('07', $ftc['document_type_code']);
        $this->assertEquals('FTC1', $ftc['series']->prefix_serie);

        $blc = $this->service->getNextNumber($this->establishment->id, '07', 'BLC1');
        $this->assertEquals(1, $blc['number']);
        $this->assertEquals('BLC1-000001', $blc['sn']);
        $this->assertEquals('07', $blc['document_type_code']);
        $this->assertEquals('BLC1', $blc['series']->prefix_serie);

        $this->assertDatabaseHas('document_series', [
            'prefix_serie' => 'FTC1',
            'current_number' => 1,
        ]);
        $this->assertDatabaseHas('document_series', [
            'prefix_serie' => 'BLC1',
            'current_number' => 1,
        ]);
    }

    public function test_uses_first_active_series_when_prefix_omitted(): void
    {
        DocumentType::create(['code' => '07', 'name' => 'Nota de Crédito', 'is_electronic' => true]);
        $this->service->generateSeriesForEstablishment($this->establishment->id);

        // Sin prefijo debe usar la primera (FTC1)
        $result = $this->service->getNextNumber($this->establishment->id, '07');
        $this->assertEquals(1, $result['number']);
        $this->assertEquals('FTC1-000001', $result['sn']);
    }
}