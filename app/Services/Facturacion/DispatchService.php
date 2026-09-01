<?php

namespace App\Services\Facturacion;

use App\Models\Dispatch;
use App\Models\Party;
use App\Services\DocumentSeriesService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * Guías de remisión (remitente/transportista): creación del borrador con sus
 * ítems y emisión electrónica vía el proveedor configurado.
 */
class DispatchService
{
    public function __construct(protected DocumentSeriesService $seriesService)
    {
    }

    /**
     * Crea una guía de remisión (borrador). No emite.
     *
     * @param  array  $items  array de filas: description, quantity, uom, codigo_interno
     */
    public function create(array $data, array $items = []): Dispatch
    {
        return DB::transaction(function () use ($data, $items) {
            $party = isset($data['party_id']) ? Party::query()->findOrFail($data['party_id']) : null;

            $dispatch = new Dispatch([
                'establishment_id' => $data['establishment_id'] ?? Auth::user()?->establishment_id,
                'document_type_code' => '09',
                'dispatch_type' => $data['dispatch_type'] ?? Dispatch::TYPE_REMITENTE,
                'party_id' => $party?->id,
                'vehicle_id' => $data['vehicle_id'] ?? null,
                'invoice_id' => $data['invoice_id'] ?? null,
                'motivo_traslado' => $data['motivo_traslado'] ?? '01',
                'descripcion_motivo_traslado' => $data['descripcion_motivo_traslado'] ?? null,
                'modo_transporte' => $data['modo_transporte'] ?? '02',
                'fecha_de_traslado' => $data['fecha_de_traslado'] ?? now()->toDateString(),
                'fecha_de_entrega' => $data['fecha_de_entrega'] ?? null,
                'peso_total' => (float) ($data['peso_total'] ?? 0),
                'unidad_peso' => $data['unidad_peso'] ?? 'KGM',
                'numero_de_bultos' => $data['numero_de_bultos'] ?? null,
                'punto_partida_ubigeo' => $data['punto_partida_ubigeo'] ?? null,
                'punto_partida_direccion' => $data['punto_partida_direccion'] ?? null,
                'punto_partida_codigo_establecimiento' => $data['punto_partida_codigo_establecimiento'] ?? '0000',
                'punto_llegada_ubigeo' => $data['punto_llegada_ubigeo'] ?? null,
                'punto_llegada_direccion' => $data['punto_llegada_direccion'] ?? null,
                'punto_llegada_codigo_establecimiento' => $data['punto_llegada_codigo_establecimiento'] ?? '0000',
                'transportista_documento_tipo' => $data['transportista_documento_tipo'] ?? null,
                'transportista_documento_numero' => $data['transportista_documento_numero'] ?? null,
                'transportista_denominacion' => $data['transportista_denominacion'] ?? null,
                'conductor_documento_tipo' => $data['conductor_documento_tipo'] ?? null,
                'conductor_documento_numero' => $data['conductor_documento_numero'] ?? null,
                'conductor_nombre' => $data['conductor_nombre'] ?? null,
                'conductor_apellidos' => $data['conductor_apellidos'] ?? null,
                'conductor_numero_licencia' => $data['conductor_numero_licencia'] ?? null,
                'vehiculo_placa' => $data['vehiculo_placa'] ?? null,
                'vehiculo_marca' => $data['vehiculo_marca'] ?? null,
                'vehiculo_modelo' => $data['vehiculo_modelo'] ?? null,
                'provider' => $data['provider'] ?? 'nubefact',
                'status' => Dispatch::STATUS_DRAFT,
                'observations' => $data['observations'] ?? null,
                'created_by' => Auth::id(),
                'updated_by' => Auth::id(),
            ]);

            $dispatch->save();
            $this->syncItems($dispatch, $items);

            return $dispatch->fresh('items');
        });
    }

    /**
     * Sincroniza los ítems de la guía (diff/upsert simple).
     */
    public function syncItems(Dispatch $dispatch, array $items): void
    {
        foreach ($items as $sort => $row) {
            if (empty($row['description'])) {
                continue;
            }

            $dispatch->items()->create([
                'codigo_interno' => $row['codigo_interno'] ?? null,
                'description' => $row['description'],
                'quantity' => (float) ($row['quantity'] ?? 1),
                'uom' => $row['uom'] ?? 'NIU',
                'sort_order' => $sort,
            ]);
        }
    }

    /**
     * Emite la guía: asigna serie/número (lock pesimista) y envía al proveedor.
     */
    public function emit(Dispatch $dispatch): Dispatch
    {
        return DB::transaction(function () use ($dispatch) {
            $dispatch->loadMissing(['party', 'items', 'invoice']);

            $this->assignDocumentNumber($dispatch);

            $provider = FacturadorProviderFactory::make();
            $response = $provider->emitDispatch($dispatch);

            $dispatch->external_id = $response['external_id'] ?? null;
            $dispatch->accepted_by_sunat = $response['accepted_by_sunat'] ?? null;
            $dispatch->sunat_description = $response['sunat_description'] ?? null;
            $dispatch->sunat_note = $response['sunat_note'] ?? null;
            $dispatch->sunat_responsecode = $response['sunat_responsecode'] ?? null;
            $dispatch->enlace_pdf = $response['enlace_pdf'] ?? null;
            $dispatch->enlace_xml = $response['enlace_xml'] ?? null;
            $dispatch->enlace_cdr = $response['enlace_cdr'] ?? null;
            $dispatch->codigo_hash = $response['codigo_hash'] ?? null;

            if ($response['serie'] && $dispatch->document_number === null) {
                $dispatch->document_serie = $response['serie'];
                $dispatch->document_number = (int) $response['numero'];
                $dispatch->document_sn = sprintf('%s-%06d', $response['serie'], (int) $response['numero']);
            }

            $dispatch->issued_at = now();
            $dispatch->status = ($dispatch->accepted_by_sunat === false)
                ? Dispatch::STATUS_REJECTED
                : Dispatch::STATUS_EMITTED;

            $dispatch->save();
            $dispatch->recordStatusChange($dispatch->status, Dispatch::STATUS_DRAFT, 'Guía emitida en ' . $dispatch->provider);

            return $dispatch->fresh();
        });
    }

    /**
     * Asigna serie TR01 y correlativo (snapshot de identidad).
     */
    protected function assignDocumentNumber(Dispatch $dispatch): void
    {
        $establishmentId = $dispatch->establishment_id ?? Auth::user()?->establishment_id;

        if (! $establishmentId) {
            throw new \RuntimeException('No hay establecimiento para numerar la guía.');
        }

        $result = $this->seriesService->getNextNumber($establishmentId, '09', $dispatch->document_serie ?: 'TR01');

        $dispatch->document_series_id = $result['series']->id;
        $dispatch->document_serie = $result['series']->prefix_serie;

        if ($result['number'] !== null) {
            $dispatch->document_number = $result['number'];
            $dispatch->document_sn = $result['sn'];
        }
    }
}

