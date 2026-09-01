<?php

namespace App\Services\Facturacion;

use App\Models\CompanySetting;
use App\Models\Dispatch;
use App\Models\Invoice;
use Illuminate\Support\Facades\Http;

/**
 * Proveedor FACTURA PERÚ (facturadorsmart.pe).
 *
 * API REST tipo FacturadorPro. Auth: header `Authorization: Bearer <token>`.
 * Colección Postman: "APIsPERU.postman_collection.json".
 * - CPE: POST {url}/api/documents
 * - Guías: POST {url}/api/dispatches
 * - Anulación: POST {url}/api/voided
 */
class FacturaPeruProvider implements FacturadorProviderInterface
{
    public function emitInvoice(Invoice $invoice): array
    {
        $payload = $this->buildInvoicePayload($invoice);
        $response = $this->post('/api/documents', $payload);

        return $this->normalizeInvoiceResponse($response);
    }

    public function queryInvoice(Invoice $invoice): array
    {
        // El listado del facturador permite filtrar por fechas; la consulta
        // puntual se hace por external_id vía /api/documents/search.
        $response = $this->post('/api/documents/search', [
            'external_id' => $invoice->external_id,
        ]);

        return $this->normalizeInvoiceResponse($response['data'] ?? $response);
    }

    public function voidInvoice(Invoice $invoice, string $reason): array
    {
        $response = $this->post('/api/voided', [
            'fecha_de_emision_de_documentos' => $invoice->invoice_date?->format('Y-m-d') ?? now()->format('Y-m-d'),
            'documentos' => [[
                'external_id' => $invoice->external_id,
                'motivo_anulacion' => mb_substr($reason, 0, 100),
            ]],
        ]);

        return $response;
    }

    public function emitDispatch(Dispatch $dispatch): array
    {
        $payload = $this->buildDispatchPayload($dispatch);
        $response = $this->post('/api/dispatches', $payload);

        return $this->normalizeDispatchResponse($response);
    }

    public function queryDispatch(Dispatch $dispatch): array
    {
        $response = $this->post('/api/dispatches/status_ticket', [
            'ticket' => $dispatch->external_id,
        ]);

        return $response;
    }


    // ---------------------------------------------------------------
    // Construcción de payloads
    // ---------------------------------------------------------------

    protected function buildInvoicePayload(Invoice $invoice): array
    {
        $party = $invoice->party;
        $isNote = $invoice->is_note;

        $totals = [
            'total_exportacion' => 0.00,
            'total_operaciones_gravadas' => round((float) $invoice->taxable_base, 2),
            'total_operaciones_inafectas' => 0.00,
            'total_operaciones_exoneradas' => 0.00,
            'total_operaciones_gratuitas' => 0.00,
            'total_igv' => round((float) $invoice->iva, 2),
            'total_impuestos' => round((float) $invoice->iva, 2),
            'total_valor' => round((float) $invoice->subtotal, 2),
            'total_venta' => round((float) $invoice->total, 2),
        ];

        // Con regularización de anticipos se informan los anticipos y el
        // subtotal de venta (total con impuestos antes de descontar anticipos).
        if ($invoice->total_advances > 0) {
            $totals['total_anticipos'] = round((float) $invoice->total_advances, 2);
            $totals['subtotal_venta'] = round((float) $invoice->total + (float) $invoice->total_advances, 2);
        }

        $payload = [
            'serie_documento' => $invoice->document_serie,
            'numero_documento' => '#',
            'fecha_de_emision' => $invoice->invoice_date?->format('Y-m-d') ?? now()->format('Y-m-d'),
            'hora_de_emision' => now()->format('H:i:s'),
            'codigo_tipo_operacion' => '0101',
            'codigo_tipo_documento' => $invoice->document_type_code,
            'codigo_tipo_moneda' => $invoice->currency === 'USD' ? 'USD' : 'PEN',
            'fecha_de_vencimiento' => $invoice->invoice_date?->format('Y-m-d') ?? '',
            'numero_orden_de_compra' => '',
            'datos_del_cliente_o_receptor' => [
                'codigo_tipo_documento_identidad' => (string) ($party?->document_type ?? ''),
                'numero_documento' => (string) ($party?->document_number ?? ''),
                'apellidos_y_nombres_o_razon_social' => mb_substr($party?->display_name ?? '', 0, 100),
                'codigo_pais' => 'PE',
                'ubigeo' => $party?->ubigeo_code ?? '',
                'direccion' => $party?->address ?? '',
                'correo_electronico' => $party?->email ?? '',
                'telefono' => $party?->phone ?? '',
            ],
            'totales' => $totals,
            'items' => $this->buildItems($invoice),
            'informacion_adicional' => $invoice->observations ?? '',
        ];

        if ($invoice->discount > 0) {
            $payload['descuentos'] = $this->buildDiscounts($invoice);
        }

        if ($invoice->total_advances > 0) {
            $payload['anticipos'] = $this->buildAdvances($invoice);
        }

        if ($isNote) {
            $payload['codigo_tipo_nota'] = $invoice->tipo_de_nota ?? '01';
            $payload['motivo_o_sustento_de_nota'] = $invoice->motivo_nota ?? '';
            $payload['documento_afectado'] = [
                'external_id' => $invoice->relatedInvoice?->external_id
                    ?? $invoice->documento_que_se_modifica_serie . '-' . $invoice->documento_que_se_modifica_numero,
            ];
        }

        return $payload;
    }

    protected function buildItems(Invoice $invoice): array
    {
        $items = [];

        foreach ($invoice->items as $index => $item) {
            $items[] = [
                'codigo_interno' => $item->codigo_interno ?: '',
                'descripcion' => mb_substr($item->description, 0, 250),
                'codigo_producto_sunat' => $item->codigo_producto_sunat ?? '',
                'unidad_de_medida' => $item->uom ?: 'NIU',
                'cantidad' => round((float) $item->quantity, 4),
                'valor_unitario' => round((float) $item->unit_price, 2),
                'codigo_tipo_precio' => '01',
                'precio_unitario' => round((float) $item->price, 2),
                'codigo_tipo_afectacion_igv' => $item->affectation_igv_type ?: '10',
                'total_base_igv' => round((float) $item->subtotal, 2),
                'porcentaje_igv' => $this->igvRate(),
                'total_igv' => round((float) $item->igv, 2),
                'total_impuestos' => round((float) $item->igv, 2),
                'total_valor_item' => round((float) $item->subtotal, 2),
                'total_item' => round((float) $item->total, 2),
            ];
        }

        return $items;
    }

    protected function buildDiscounts(Invoice $invoice): array
    {
        $discounts = [];

        foreach ($invoice->discounts as $discount) {
            $base = $discount->base ?? $invoice->subtotal;
            $factor = $discount->factor ?? ($base > 0 ? round($discount->amount / $base, 5) : 0);

            $discounts[] = [
                'codigo' => $discount->code,
                'descripcion' => $discount->description
                    ?: ($discount->code === '04'
                        ? 'Descuentos globales por anticipos gravados que afectan la base imponible del IGV/IVAP'
                        : 'Descuentos globales que afectan la base imponible del IGV/IVAP'),
                'factor' => round((float) $factor, 5),
                'monto' => round((float) $discount->amount, 2),
                'base' => round((float) $base, 2),
            ];
        }

        return $discounts;
    }

    protected function buildAdvances(Invoice $invoice): array
    {
        $advances = [];

        foreach ($invoice->items->where('is_advance_line', true) as $item) {
            $advanceInvoice = $item->advanceInvoice;

            if (! $advanceInvoice) {
                continue;
            }

            $advances[] = [
                'numero' => $advanceInvoice->document_sn ?? $advanceInvoice->document_serie,
                'codigo_tipo_documento' => '02', // factura por anticipos
                'monto' => round((float) $item->subtotal, 2),
                'total' => round((float) $item->total, 2),
            ];
        }

        return $advances;
    }


    protected function buildDispatchPayload(Dispatch $dispatch): array
    {
        $party = $dispatch->party;

        return [
            'serie_documento' => $dispatch->document_serie,
            'numero_documento' => '#',
            'fecha_de_emision' => $dispatch->fecha_de_traslado?->format('Y-m-d') ?? now()->format('Y-m-d'),
            'hora_de_emision' => now()->format('H:i:s'),
            'codigo_tipo_documento' => '09',
            'datos_del_emisor' => [
                'codigo_pais' => 'PE',
                'ubigeo' => $dispatch->punto_partida_ubigeo ?? '',
                'direccion' => $dispatch->punto_partida_direccion ?? '',
                'correo_electronico' => '',
                'telefono' => '',
                'codigo_del_domicilio_fiscal' => '0000',
            ],
            'datos_del_cliente_o_receptor' => [
                'codigo_tipo_documento_identidad' => (string) ($party?->document_type ?? ''),
                'numero_documento' => (string) ($party?->document_number ?? ''),
                'apellidos_y_nombres_o_razon_social' => mb_substr($party?->display_name ?? '', 0, 100),
                'nombre_comercial' => '',
                'codigo_pais' => 'PE',
                'ubigeo' => $party?->ubigeo_code ?? '',
                'direccion' => $party?->address ?? '',
                'correo_electronico' => $party?->email ?? '',
                'telefono' => $party?->phone ?? '',
            ],
            'observaciones' => $dispatch->observations ?? '',
            'codigo_modo_transporte' => $dispatch->modo_transporte ?: '02',
            'codigo_motivo_traslado' => $dispatch->motivo_traslado,
            'descripcion_motivo_traslado' => $dispatch->descripcion_motivo_traslado ?? '',
            'fecha_de_traslado' => $dispatch->fecha_de_traslado?->format('Y-m-d') ?? '',
            'codigo_de_puerto' => '',
            'indicador_de_transbordo' => false,
            'unidad_peso_total' => $dispatch->unidad_peso ?: 'KGM',
            'peso_total' => round((float) $dispatch->peso_total, 2),
            'numero_de_bultos' => (int) ($dispatch->numero_de_bultos ?? 1),
            'numero_de_contenedor' => '',
            'direccion_partida' => [
                'ubigeo' => $dispatch->punto_partida_ubigeo ?? '',
                'direccion' => $dispatch->punto_partida_direccion ?? '',
                'codigo_del_domicilio_fiscal' => $dispatch->punto_partida_codigo_establecimiento ?: '0000',
            ],
            'direccion_llegada' => [
                'ubigeo' => $dispatch->punto_llegada_ubigeo ?? '',
                'direccion' => $dispatch->punto_llegada_direccion ?? '',
                'codigo_del_domicilio_fiscal' => $dispatch->punto_llegada_codigo_establecimiento ?: '0000',
            ],
            'chofer' => $dispatch->conductor_documento_numero ? [
                'codigo_tipo_documento_identidad' => (string) ($dispatch->conductor_documento_tipo ?? ''),
                'numero_documento' => (string) $dispatch->conductor_documento_numero,
                'nombres' => trim(($dispatch->conductor_nombre ?? '') . ' ' . ($dispatch->conductor_apellidos ?? '')),
                'numero_licencia' => $dispatch->conductor_numero_licencia ?? '',
                'telefono' => '',
            ] : null,
            'vehiculo' => $dispatch->vehiculo_placa ? [
                'numero_de_placa' => $dispatch->vehiculo_placa,
                'modelo' => $dispatch->vehiculo_modelo ?? '',
                'marca' => $dispatch->vehiculo_marca ?? '',
            ] : null,
            'items' => $dispatch->items->map(fn ($item) => [
                'codigo_interno' => $item->codigo_interno ?: '',
                'cantidad' => round((float) $item->quantity, 4),
            ])->values()->all(),
            'documento_afectado' => $dispatch->invoice ? [
                'serie_documento' => $dispatch->invoice->document_serie,
                'numero_documento' => (string) $dispatch->invoice->document_number,
                'codigo_tipo_documento' => $dispatch->invoice->document_type_code,
            ] : null,
        ];
    }


    // ---------------------------------------------------------------
    // HTTP y normalización de respuestas
    // ---------------------------------------------------------------

    protected function post(string $path, array $payload): array
    {
        $setting = CompanySetting::get();
        $baseUrl = rtrim((string) $setting?->facturador_api_url, '/');
        $token = $setting?->facturador_api_key ?: '';

        $response = Http::withToken($token)
            ->acceptJson()
            ->timeout(60)
            ->post($baseUrl . $path, $payload);

        $body = $response->json() ?? [];

        if ($response->failed()) {
            $message = $body['message'] ?? $body['error'] ?? $response->body();

            throw new \RuntimeException('FACTURAPERU HTTP ' . $response->status() . ': ' . $message);
        }

        if (isset($body['success']) && $body['success'] === false) {
            throw new \RuntimeException('FACTURAPERU: ' . ($body['message'] ?? 'Error del proveedor'));
        }

        return $body;
    }

    protected function normalizeInvoiceResponse(array $response): array
    {
        // La respuesta del FacturadorPro suele venir en data[].datos o data.
        $data = $response['data'] ?? $response;
        $document = is_array($data) && isset($data['datos']) ? $data['datos'] : $data;
        $document = is_array($document) && isset($document[0]) ? $document[0] : $document;

        return [
            'external_id' => $document['external_id'] ?? $data['external_id'] ?? null,
            'serie' => $document['series'] ?? $document['serie'] ?? $response['series'] ?? null,
            'numero' => $document['number'] ?? $document['numero'] ?? $response['number'] ?? null,
            'accepted_by_sunat' => $this->boolOrNull($document['state_type_id'] === '05' ? true : ($document['accepted_by_sunat'] ?? null)),
            'sunat_description' => $document['sunat_description'] ?? $document['description'] ?? null,
            'sunat_note' => $document['sunat_note'] ?? null,
            'sunat_responsecode' => $document['sunat_responsecode'] ?? null,
            'enlace_pdf' => $document['download_external_pdf'] ?? $document['external_pdf'] ?? null,
            'enlace_xml' => $document['download_external_xml'] ?? $document['external_xml'] ?? null,
            'enlace_cdr' => $document['download_external_cdr'] ?? $document['external_cdr'] ?? null,
            'cadena_qr' => $document['qr'] ?? null,
            'codigo_hash' => $document['hash'] ?? null,
            'raw' => $response,
        ];
    }

    protected function normalizeDispatchResponse(array $response): array
    {
        $data = $response['data'] ?? $response;
        $document = is_array($data) && isset($data['datos']) ? $data['datos'] : $data;
        $document = is_array($document) && isset($document[0]) ? $document[0] : $document;

        return [
            'external_id' => $document['external_id'] ?? $data['external_id'] ?? null,
            'serie' => $document['series'] ?? $document['serie'] ?? null,
            'numero' => $document['number'] ?? $document['numero'] ?? null,
            'accepted_by_sunat' => $this->boolOrNull($document['state_type_id'] === '05' ? true : null),
            'sunat_description' => $document['sunat_description'] ?? null,
            'sunat_note' => $document['sunat_note'] ?? null,
            'sunat_responsecode' => $document['sunat_responsecode'] ?? null,
            'enlace_pdf' => $document['download_external_pdf'] ?? null,
            'enlace_xml' => $document['download_external_xml'] ?? null,
            'enlace_cdr' => $document['download_external_cdr'] ?? null,
            'codigo_hash' => $document['hash'] ?? null,
            'raw' => $response,
        ];
    }

    protected function igvRate(): float
    {
        $rate = (float) (CompanySetting::get()?->igv_rate ?? 18.00);

        return $rate <= 1 ? round($rate * 100, 2) : round($rate, 2);
    }

    protected function boolOrNull($value): ?bool
    {
        if ($value === null || $value === '') {
            return null;
        }

        return filter_var($value, FILTER_VALIDATE_BOOLEAN);
    }
}

