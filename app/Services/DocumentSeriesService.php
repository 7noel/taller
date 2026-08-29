<?php

namespace App\Services;

use App\Models\CheckIn;
use App\Models\CompanySetting;
use App\Models\DocumentSeries;
use App\Models\DocumentType;
use App\Models\Establishment;
use Illuminate\Support\Facades\DB;

class DocumentSeriesService
{
    /**
     * Mapa código de documento → prefijos de serie a generar por establecimiento.
     * Coincide con la tabla series del facturador: '07' (NC) y '08' (ND)
     * generan dos series (factura y boleta).
     */
    protected const PREFIX_MAP = [
        '01' => ['FTR1'],
        '03' => ['BLT1'],
        '07' => ['FTC1', 'BLC1'],
        '08' => ['FTD1', 'BLD1'],
        '09' => ['TR01'],
        '80' => ['NV01'],
        'U2' => ['NIA1'],
        'U3' => ['NSA1'],
        'U4' => ['NTA1'],
        'PRE' => ['PRE01'],
        'OT' => ['OT01'],
        'IV' => ['IV01'],
        'CST' => ['CST01'],
        'LST' => ['LST01'],
        'OC' => ['OC01'],
    ];

    /**
     * Crea todas las series para un establecimiento a partir de los tipos de
     * documento activos. El prefijo se obtiene del mapa y el origen del número
     * se hereda de la configuración global.
     */
    public function generateSeriesForEstablishment(int $establishmentId): void
    {
        $setting = CompanySetting::get();
        $defaultSource = $setting?->default_number_source ?? 'LOCAL';

        $documentTypes = DocumentType::query()
            ->where('is_active', true)
            ->orderBy('id')
            ->get();

        foreach ($documentTypes as $documentType) {
            $prefixes = self::PREFIX_MAP[$documentType->code] ?? [$documentType->code];

            foreach ($prefixes as $prefix) {
                DocumentSeries::firstOrCreate(
                    [
                        'establishment_id' => $establishmentId,
                        'document_type_id' => $documentType->id,
                        'prefix_serie' => $prefix,
                    ],
                    [
                        'current_number' => 0,
                        'number_source' => $defaultSource,
                        'status' => true,
                    ]
                );
            }
        }
    }

    /**
     * Obtiene el siguiente número de documento para un establecimiento y tipo
     * de documento. Usa un lock pesimista para evitar números duplicados bajo
     * concurrencia. Si la serie es tipo API, devuelve null para esperar al API.
     *
     * @param  string|null  $prefix  Prefijo de serie (ej. 'IV01'). Si es null y
     *                              existen varias series del tipo, usa la primera activa.
     * @return array{series: DocumentSeries, number: int|null, sn: string|null, document_type_code: string|null}
     */
    public function getNextNumber(int $establishmentId, string $documentTypeCode, ?string $prefix = null): array
    {
        return DB::transaction(function () use ($establishmentId, $documentTypeCode, $prefix) {
            $series = DocumentSeries::query()
                ->with('documentType')
                ->where('establishment_id', $establishmentId)
                ->whereHas('documentType', fn ($q) => $q->where('code', $documentTypeCode))
                ->when($prefix, fn ($q) => $q->where('prefix_serie', $prefix))
                ->orderBy('id')
                ->lockForUpdate()
                ->first();

            if (!$series || !$series->status) {
                throw new \RuntimeException("Serie no disponible para el documento {$documentTypeCode}.");
            }

            $code = $series->documentType?->code;

            if ($series->number_source === 'API') {
                return ['series' => $series, 'number' => null, 'sn' => null, 'document_type_code' => $code];
            }

            $number = $series->current_number + 1;
            $sn = sprintf('%s-%06d', $series->prefix_serie, $number);

            $series->increment('current_number');

            return [
                'series' => $series->fresh(),
                'number' => $number,
                'sn' => $sn,
                'document_type_code' => $code,
            ];
        });
    }

    /**
     * Actualiza manualmente el prefijo o el número actual de una serie.
     */
    public function updateSeries(DocumentSeries $series, array $data): DocumentSeries
    {
        $series->update([
            'prefix_serie' => strtoupper(trim($data['prefix_serie'] ?? $series->prefix_serie)),
            'current_number' => max(0, (int) ($data['current_number'] ?? $series->current_number)),
            'number_source' => $data['number_source'] ?? $series->number_source,
            'status' => array_key_exists('status', $data) ? (bool) $data['status'] : $series->status,
        ]);

        return $series->fresh();
    }

    /**
     * Crea una serie manualmente para un establecimiento.
     */
    public function createSeries(Establishment $establishment, array $data): DocumentSeries
    {
        return DocumentSeries::create([
            'establishment_id' => $establishment->id,
            'document_type_id' => $data['document_type_id'],
            'prefix_serie' => strtoupper(trim($data['prefix_serie'])),
            'current_number' => max(0, (int) ($data['current_number'] ?? 0)),
            'number_source' => $data['number_source'] ?? 'LOCAL',
            'status' => array_key_exists('status', $data) ? (bool) $data['status'] : true,
        ]);
    }

    /**
     * Verifica si una serie tiene documentos asociados (check-ins).
     */
    public function hasAssociatedDocuments(DocumentSeries $series): bool
    {
        return CheckIn::query()
            ->where('document_series_id', $series->id)
            ->exists();
    }

    /**
     * Elimina una serie. El controlador debe validar antes que no existan
     * documentos asociados, para mostrar un mensaje claro al usuario.
     */
    public function destroy(DocumentSeries $series): void
    {
        $series->delete();
    }
}
