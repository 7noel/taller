<?php

namespace App\Services\Facturacion;

use App\Models\CompanySetting;
use App\Models\Dispatch;
use App\Models\Invoice;
use Illuminate\Support\Facades\Http;

/**
 * Proveedor NUBEFACT.
 *
 * Envía un JSON vía POST a una RUTA única por cliente con el token en el
 * header Authorization (sin "Bearer"). Documentación local:
 * "NUBEFACT DOC API JSON V1.md" (operaciones generar/consultar/anular
 * comprobante y guía de remisión).
 */
class NubefactProvider implements FacturadorProviderInterface
{
    public function emitInvoice(Invoice $invoice): array
    {
        $payload = $this->buildInvoicePayload($invoice);
        $response = $this->post($payload);

        return $this->normalizeInvoiceResponse($response);
    }

    public function queryInvoice(Invoice $invoice): array
    {
        $payload = [
            'operacion' => 'consultar_comprobante',
            'tipo_de_comprobante' => $this->tipoComprobante($invoice->document_type_code),
            'serie' => $invoice->document_serie,
            'numero' => (int) $invoice->document_number,
        ];

        return $this->post($payload);
    }

    public function voidInvoice(Invoice $invoice, string $reason): array
    {
        $payload = [
            'operacion' => 'generar_anulacion',
            'tipo_de_comprobante' => $this->tipoComprobante($invoice->document_type_code),
            'serie' => $invoice->document_serie,
            'numero' => (int) $invoice->document_number,
            'motivo' => mb_substr($reason, 0, 100),
        ];

        return $this->post($payload);
    }

    public function emitDispatch(Dispatch $dispatch): array
    {
        $payload = $this->buildDispatchPayload($dispatch);
        $response = $this->post($payload);

        return $this->normalizeDispatchResponse($response);
    }

    public function queryDispatch(Dispatch $dispatch): array
    {
        $payload = [
            'operacion' => 'consultar_guia',
            'tipo_de_comprobante' => 7,
            'serie' => $dispatch->document_serie,
            'numero' => (int) $dispatch->document_number,
        ];

        return $this->post($payload);
    }


    // ---------------------------------------------------------------
    // Construcción de payloads
    // ---------------------------------------------------------------

    protected function buildInvoicePayload(Invoice $invoice): array
    {
        $party = $invoice->party;

        return [
            'operacion' => 'generar_comprobante',
            'tipo_de_comprobante' => $this->tipoComprobante($invoice->document_type_code),
            'serie' => $invoice->document_serie,
            'numero' => (string) ($invoice->document_number ?? ''),
            'sunat_transaction' => (string) ($invoice->sunat_transaction ?? 1),
            'cliente_tipo_de_documento' => (string) ($party?->document_type ?? ''),
            'cliente_numero_de_documento' => (string) ($party?->document_number ?? ''),
            'cliente_denominacion' => mb_substr($party?->display_name ?? '', 0, 100),
            'cliente_direccion' => mb_substr($party?->address ?? '', 0, 100),
            'cliente_email' => $party?->email ?? '',
            'cliente_email_1' => '',
            'cliente_email_2' => '',
            'fecha_de_emision' => $invoice->invoice_date?->format('d-m-Y') ?? now()->format('d-m-Y'),
            'fecha_de_vencimiento' => '',
            'moneda' => $invoice->currency === 'USD' ? '2' : '1',
            'tipo_de_cambio' => $invoice->exchange_rate ? (string) $invoice->exchange_rate : '',
            'porcentaje_de_igv' => number_format($this->igvRate(), 2),
            'descuento_global' => $this->number($invoice->globalDiscountAmount()),
            'total_descuento' => $this->number($invoice->discount),
            'total_anticipo' => $this->number($invoice->total_advances),
            'total_gravada' => $this->number($invoice->taxable_base),
            'total_inafecta' => '',
            'total_exonerada' => '',
            'total_igv' => $this->number($invoice->iva),
            'total_gratuita' => '',
            'total_otros_cargos' => '',
            'total' => $this->number($invoice->total),
            'percepcion_tipo' => '',
            'percepcion_base_imponible' => '',
            'total_percepcion' => '',
            'total_incluido_percepcion' => '',
            'detraccion' => 'false',
            'observaciones' => $invoice->observations ?? '',
            'documento_que_se_modifica_tipo' => $this->tipoComprobante($invoice->documento_que_se_modifica_tipo),
            'documento_que_se_modifica_serie' => (string) ($invoice->documento_que_se_modifica_serie ?? ''),
            'documento_que_se_modifica_numero' => (string) ($invoice->documento_que_se_modifica_numero ?? ''),
            'tipo_de_nota_de_credito' => $invoice->document_type_code === Invoice::DOC_CREDIT_NOTE ? (string) ($invoice->tipo_de_nota ?? '') : '',
            'tipo_de_nota_de_debito' => $invoice->document_type_code === Invoice::DOC_DEBIT_NOTE ? (string) ($invoice->tipo_de_nota ?? '') : '',
            'enviar_automaticamente_a_la_sunat' => $invoice->enviar_automaticamente_a_la_sunat ? 'true' : 'false',
            'enviar_automaticamente_al_cliente' => $invoice->enviar_automaticamente_al_cliente ? 'true' : 'false',
            'codigo_unico' => '',
            'condiciones_de_pago' => '',
            'medio_de_pago' => '',
            'placa_vehiculo' => $invoice->vehicle?->plate ?? '',
            'orden_compra_servicio' => '',
            'tabla_personalizada_codigo' => '',
            'formato_de_pdf' => '',
            'items' => $this->buildItems($invoice),
        ];
    }

    protected function buildItems(Invoice $invoice): array
    {
        $items = [];

        foreach ($invoice->items as $index => $item) {
            $advance = $item->is_advance_line ? $item->advanceInvoice : null;

            $items[] = [
                'unidad_de_medida' => $item->uom ?: 'ZZ',
                'codigo' => str_pad((string) ($index + 1), 3, '0', STR_PAD_LEFT),
                'descripcion' => mb_substr($item->description, 0, 250),
                'cantidad' => $this->number($item->quantity),
                'valor_unitario' => $this->number($item->unit_price),
                'precio_unitario' => $this->number($item->price),
                'descuento' => $this->number($item->discount),
                'subtotal' => $this->number($item->subtotal),
                'tipo_de_igv' => $this->nubefactIgvType($item->affectation_igv_type),
                'igv' => $this->number($item->igv),
                'total' => $this->number($item->total),
                'anticipo_regularizacion' => $item->is_advance_line ? 'true' : 'false',
                'anticipo_documento_serie' => $advance?->document_serie ?? '',
                'anticipo_documento_numero' => (string) ($advance?->document_number ?? ''),
                'codigo_producto_sunat' => $item->is_advance_line
                    ? ($item->codigo_producto_sunat ?: '20000000')
                    : ($item->codigo_producto_sunat ?: '10000000'),
            ];
        }

        return $items;
    }

    protected function buildDispatchPayload(Dispatch $dispatch): array
    {
        $party = $dispatch->party;

        return [
            'operacion' => 'generar_guia',
            'tipo_de_comprobante' => $dispatch->dispatch_type === Dispatch::TYPE_TRANSPORTISTA ? 8 : 7,
            'serie' => $dispatch->document_serie,
            'numero' => (string) ($dispatch->document_number ?? ''),
            'cliente_tipo_de_documento' => (string) ($party?->document_type ?? ''),
            'cliente_numero_de_documento' => (string) ($party?->document_number ?? ''),
            'cliente_denominacion' => mb_substr($party?->display_name ?? '', 0, 100),
            'cliente_direccion' => mb_substr($party?->address ?? '', 0, 100),
            'cliente_email' => $party?->email ?? '',
            'cliente_email_1' => '',
            'cliente_email_2' => '',
            'fecha_de_emision' => $dispatch->fecha_de_traslado?->format('d-m-Y') ?? now()->format('d-m-Y'),
            'observaciones' => $dispatch->observations ?? '',
            'motivo_de_traslado' => $dispatch->motivo_traslado,
            'peso_bruto_total' => $this->number($dispatch->peso_total),
            'peso_bruto_unidad_de_medida' => $dispatch->unidad_peso ?: 'KGM',
            'numero_de_bultos' => (string) ($dispatch->numero_de_bultos ?? 1),
            'tipo_de_transporte' => $dispatch->modo_transporte ?: '02',
            'fecha_de_inicio_de_traslado' => $dispatch->fecha_de_traslado?->format('d-m-Y'),
            'fecha_de_entrega_al_transportista' => $dispatch->fecha_de_entrega?->format('d-m-Y') ?? '',
            'transportista_documento_tipo' => (string) ($dispatch->transportista_documento_tipo ?? ''),
            'transportista_documento_numero' => (string) ($dispatch->transportista_documento_numero ?? ''),
            'transportista_denominacion' => (string) ($dispatch->transportista_denominacion ?? ''),
            'transportista_placa_numero' => (string) ($dispatch->vehiculo_placa ?? ''),
            'conductor_documento_tipo' => (string) ($dispatch->conductor_documento_tipo ?? ''),
            'conductor_documento_numero' => (string) ($dispatch->conductor_documento_numero ?? ''),
            'conductor_nombre' => (string) ($dispatch->conductor_nombre ?? ''),
            'conductor_apellidos' => (string) ($dispatch->conductor_apellidos ?? ''),
            'conductor_numero_licencia' => (string) ($dispatch->conductor_numero_licencia ?? ''),
            'punto_de_partida_ubigeo' => $dispatch->punto_partida_ubigeo ?? '',
            'punto_de_partida_direccion' => mb_substr($dispatch->punto_partida_direccion ?? '', 0, 100),
            'punto_de_partida_codigo_establecimiento_sunat' => $dispatch->punto_partida_codigo_establecimiento ?: '0000',
            'punto_de_llegada_ubigeo' => $dispatch->punto_llegada_ubigeo ?? '',
            'punto_de_llegada_direccion' => mb_substr($dispatch->punto_llegada_direccion ?? '', 0, 100),
            'punto_de_llegada_codigo_establecimiento_sunat' => $dispatch->punto_llegada_codigo_establecimiento ?: '0000',
            'enviar_automaticamente_al_cliente' => 'false',
            'formato_de_pdf' => '',
            'items' => $dispatch->items->map(fn ($item) => [
                'unidad_de_medida' => $item->uom ?: 'NIU',
                'codigo' => $item->codigo_interno ?: '',
                'descripcion' => mb_substr($item->description, 0, 250),
                'cantidad' => $this->number($item->quantity),
            ])->values()->all(),
            'documento_relacionado' => $dispatch->invoice ? [[
                'tipo' => '01',
                'serie' => $dispatch->invoice->document_serie,
                'numero' => (string) $dispatch->invoice->document_number,
            ]] : [],
        ];
    }

    // ---------------------------------------------------------------
    // HTTP y normalización de respuestas
    // ---------------------------------------------------------------

    protected function post(array $payload): array
    {
        $setting = CompanySetting::get();
        $url = $setting?->facturador_api_url ?: '';
        $token = $setting?->facturador_api_key ?: '';

        $response = Http::withHeaders([
            'Authorization' => $token,
            'Content-Type' => 'application/json',
        ])->timeout(60)->post($url, $payload);

        $body = $response->json() ?? [];

        if ($response->failed()) {
            throw new \RuntimeException('NUBEFACT HTTP ' . $response->status() . ': ' . ($body['message'] ?? $response->body()));
        }

        return $body;
    }

    protected function normalizeInvoiceResponse(array $response): array
    {
        return [
            'external_id' => null,
            'serie' => $response['serie'] ?? null,
            'numero' => $response['numero'] ?? null,
            'accepted_by_sunat' => $this->boolOrNull($response['aceptada_por_sunat'] ?? null),
            'sunat_description' => $response['sunat_description'] ?? null,
            'sunat_note' => $response['sunat_note'] ?? null,
            'sunat_responsecode' => $response['sunat_responsecode'] ?? null,
            'enlace_pdf' => $response['enlace_del_pdf'] ?? null,
            'enlace_xml' => $response['enlace_del_xml'] ?? null,
            'enlace_cdr' => $response['enlace_del_cdr'] ?? null,
            'cadena_qr' => $response['cadena_para_codigo_qr'] ?? null,
            'codigo_hash' => $response['codigo_hash'] ?? null,
            'raw' => $response,
        ];
    }

    protected function normalizeDispatchResponse(array $response): array
    {
        return [
            'external_id' => null,
            'serie' => $response['serie'] ?? null,
            'numero' => $response['numero'] ?? null,
            'accepted_by_sunat' => $this->boolOrNull($response['aceptada_por_sunat'] ?? null),
            'sunat_description' => $response['sunat_description'] ?? null,
            'sunat_note' => $response['sunat_note'] ?? null,
            'sunat_responsecode' => $response['sunat_responsecode'] ?? null,
            'enlace_pdf' => $response['enlace_del_pdf'] ?? null,
            'enlace_xml' => $response['enlace_del_xml'] ?? null,
            'enlace_cdr' => $response['enlace_del_cdr'] ?? null,
            'codigo_hash' => $response['codigo_hash'] ?? null,
            'raw' => $response,
        ];
    }

    protected function tipoComprobante(?string $code): ?int
    {
        return match ($code) {
            Invoice::DOC_INVOICE => 1,
            Invoice::DOC_RECEIPT => 2,
            Invoice::DOC_CREDIT_NOTE => 3,
            Invoice::DOC_DEBIT_NOTE => 4,
            default => null,
        };
    }

    protected function nubefactIgvType(string $affectation): string
    {
        return match ($affectation) {
            '20' => '8',  // exonerado
            '30' => '9',  // inafecto
            '17' => '17', // exonerado gratuito
            '37' => '20', // inafecto gratuito
            default => '1', // gravado
        };
    }

    protected function igvRate(): float
    {
        $rate = (float) (CompanySetting::get()?->igv_rate ?? 18.00);

        return $rate <= 1 ? round($rate * 100, 2) : round($rate, 2);
    }

    protected function number(?float $value): string
    {
        if ($value === null || $value === '') {
            return '';
        }

        return number_format((float) $value, 2, '.', '');
    }

    protected function boolOrNull($value): ?bool
    {
        if ($value === null || $value === '') {
            return null;
        }

        return filter_var($value, FILTER_VALIDATE_BOOLEAN);
    }
}
