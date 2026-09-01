<?php

namespace App\Services\Facturacion;

use App\Models\Dispatch;
use App\Models\Invoice;

/**
 * Contrato común para proveedores de facturación electrónica.
 * Cada proveedor (Nubefact, Factura Perú / facturadorsmart.pe) implementa
 * la emisión, consulta y anulación de comprobantes y guías de remisión.
 *
 * Los métodos reciben el modelo (Invoice/Dispatch) con sus relaciones ya
 * cargadas y devuelven la respuesta normalizada del proveedor.
 */
interface FacturadorProviderInterface
{
    /**
     * Emite un comprobante (factura, boleta, NC o ND) en el proveedor.
     *
     * @return array{external_id?: string|null, serie?: string, numero?: int|string|null,
     *               accepted_by_sunat?: bool|null, sunat_description?: string|null,
     *               sunat_note?: string|null, sunat_responsecode?: string|null,
     *               enlace_pdf?: string|null, enlace_xml?: string|null, enlace_cdr?: string|null,
     *               cadena_qr?: string|null, codigo_hash?: string|null, raw?: array}
     */
    public function emitInvoice(Invoice $invoice): array;

    /**
     * Consulta el estado de un comprobante ya emitido.
     */
    public function queryInvoice(Invoice $invoice): array;

    /**
     * Anula (comunicación de baja) un comprobante emitido.
     */
    public function voidInvoice(Invoice $invoice, string $reason): array;

    /**
     * Emite una guía de remisión (remitente o transportista).
     */
    public function emitDispatch(Dispatch $dispatch): array;

    /**
     * Consulta el estado de una guía de remisión.
     */
    public function queryDispatch(Dispatch $dispatch): array;
}
