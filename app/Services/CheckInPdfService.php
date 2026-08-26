<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\CheckIn;
use App\Models\CheckInChecklistItem;
use App\Models\CompanySetting;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Response;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;

/**
 * Genera y sirve el PDF de inventario (check-in).
 *
 * La primera vez que se pide un PDF se genera con Dompdf y se guarda en el
 * disco configurado (config/pdf.php) con un nombre versionado por fingerprint.
 * Mientras nada de lo que se renderiza cambie, las siguientes visitas sirven el
 * archivo guardado sin regenerar.
 */
class CheckInPdfService
{
    /**
     * Sirve el PDF del inventario, generándolo solo si no hay una versión vigente en caché.
     */
    public function serve(CheckIn $checkIn): Response
    {
        $checkIn->load([
            'vehicle.vehicleModel.brand',
            'vehicle.relationships.party',
            'client.ubigeo',
            'insuranceCompany',
            'establishment.ubigeo',
            'creator',
            'checklistResults.checklistItem',
            'damages',
        ]);

        $company = CompanySetting::get();

        $checklistItems = CheckInChecklistItem::query()
            ->where('is_active', true)
            ->orderBy('order')
            ->get();

        $filename = $this->filenameFor($checkIn, $checklistItems);
        $relativePath = $this->pathFor($filename);

        if ($this->isCacheEnabled() && Storage::disk(config('pdf.disk'))->exists($relativePath)) {
            return $this->binaryResponse(Storage::disk(config('pdf.disk'))->get($relativePath), $checkIn);
        }

        $content = Pdf::loadView('check-ins.pdf', compact('checkIn', 'company', 'checklistItems'))
            ->setPaper('a4', 'portrait')
            ->output();

        if ($this->isCacheEnabled()) {
            $this->storeVersion($checkIn, $filename, $content);
        }

        return $this->binaryResponse($content, $checkIn);
    }

    /**
     * Huella digital de todo lo que se renderiza en el PDF.
     *
     * Cambia si se edita el inventario (o sus daños/checklist), los datos de la
     * empresa (incluido logo/favicon), los ítems del checklist, los mockups o
     * la propia plantilla pdf.blade.php (filemtime). Si cambia, el archivo
     * cacheado con el nombre anterior queda obsoleto y se regenera.
     */
    public function fingerprint(CheckIn $checkIn, Collection $checklistItems): string
    {
        $company = CompanySetting::get();

        $vehicle = $checkIn->vehicle;
        $relations = $vehicle?->relationships ?? collect();
        $driver = $relations->first(fn ($r) => $r->role === 'driver');
        $approver = $relations->first(fn ($r) => $r->role === 'approver');

        $payload = [
            'check_in' => $this->attributesOf($checkIn, [
                'id', 'vehicle_id', 'client_id', 'insurance_company_id', 'establishment_id',
                'document_type_code', 'document_serie', 'document_number', 'document_sn',
                'service_type', 'claim_number', 'mileage', 'fuel_level', 'property_card',
                'soat_expiration', 'technical_review_expiration', 'keys_count', 'has_remote_control',
                'client_request', 'observations', 'status', 'created_at', 'updated_at',
            ]),
            'damages' => $checkIn->damages
                ->map(fn (Model $d) => $this->attributesOf($d, ['damage_type', 'pos_x', 'pos_y', 'notes']))
                ->toArray(),
            'checklist_results' => $checkIn->checklistResults
                ->map(fn (Model $r) => $this->attributesOf($r, ['checklist_item_id', 'status', 'observations']))
                ->toArray(),
            'checklist_items' => $checklistItems
                ->map(fn (Model $i) => $this->attributesOf($i, ['id', 'name', 'order']))
                ->toArray(),
            'company' => $company ? $this->attributesOf($company, [
                'ruc', 'razon_social', 'nombre_comercial', 'direccion', 'ubigeo_code',
                'telefono', 'celular', 'email', 'logo_path', 'favicon_path',
            ]) : null,
            'company_updated_at' => $company?->updated_at?->timestamp,
            'logo_mtime' => $this->fileMtime(storage_path('app/public/'.$company?->logo_path)),
            'favicon_mtime' => $this->fileMtime(storage_path('app/public/'.$company?->favicon_path)),
            'vehicle' => $vehicle ? [
                'plate' => $vehicle->plate,
                'brand' => $vehicle->vehicleModel?->brand?->name,
                'model' => $vehicle->vehicleModel?->name,
                'body_type' => $vehicle->body_type,
                'vin' => $vehicle->vin,
                'year' => $vehicle->year,
                'engine_number' => $vehicle->engine_number,
                'color' => $vehicle->color,
            ] : null,
            'client' => $checkIn->client ? $this->attributesOf($checkIn->client, [
                'document_type', 'document_number', 'first_name', 'last_name',
                'business_name', 'address', 'ubigeo_code',
            ]) : null,
            'insurance' => $checkIn->insuranceCompany ? $this->attributesOf($checkIn->insuranceCompany, [
                'business_name', 'first_name', 'last_name',
            ]) : null,
            'driver' => $driver ? ['name' => $driver->party?->display_name, 'mobile' => $driver->party?->mobile] : null,
            'approver' => $approver ? ['name' => $approver->party?->display_name, 'mobile' => $approver->party?->mobile] : null,
            'creator' => $checkIn->creator?->name,
            'establishment' => $checkIn->establishment ? $this->attributesOf($checkIn->establishment, [
                'name', 'address', 'phone', 'celular', 'email', 'ubigeo_code',
            ]) : null,
            'mockups' => $this->mockupMtimes(),
            'template_mtime' => $this->fileMtime(resource_path('views/check-ins/pdf.blade.php')),
        ];

        return md5(serialize($payload));
    }

    public function filenameFor(CheckIn $checkIn, Collection $checklistItems): string
    {
        return 'inventario-'.$checkIn->id.'-'.$this->fingerprint($checkIn, $checklistItems).'.pdf';
    }

    /**
     * Guarda la versión vigente y borra las anteriores del mismo inventario
     * (evita acumulación de archivos en disco).
     */
    protected function storeVersion(CheckIn $checkIn, string $filename, string $content): void
    {
        $disk = Storage::disk(config('pdf.disk'));
        $directory = config('pdf.directory');

        if (! $disk->exists($directory)) {
            $disk->makeDirectory($directory);
        }
        if (! $disk->exists($directory.'/.gitignore')) {
            $disk->put($directory.'/.gitignore', "*\n!.gitignore\n");
        }

        $relative = $this->pathFor($filename);
        $disk->put($relative, $content);

        $prefix = $directory.'/inventario-'.$checkIn->id.'-';
        foreach ($disk->files($directory) as $file) {
            if (str_starts_with($file, $prefix) && $file !== $relative) {
                $disk->delete($file);
            }
        }
    }

    protected function binaryResponse(string $content, CheckIn $checkIn): Response
    {
        return response($content, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="'.$this->downloadName($checkIn).'"',
            'Cache-Control' => 'no-store, private',
        ]);
    }

    protected function downloadName(CheckIn $checkIn): string
    {
        return 'inventario-'.($checkIn->document_sn ?? $checkIn->id).'.pdf';
    }

    protected function isCacheEnabled(): bool
    {
        return (bool) config('pdf.cache_enabled');
    }

    protected function pathFor(string $filename): string
    {
        return rtrim((string) config('pdf.directory'), '/').'/'.$filename;
    }

    /**
     * Devuelve solo los atributos relevantes del modelo en su forma cruda (DB),
     * determinista para el hash (las fechas quedan como strings de la BD).
     */
    protected function attributesOf(Model $model, array $keys): array
    {
        return array_intersect_key($model->getAttributes(), array_flip($keys));
    }

    protected function mockupMtimes(): array
    {
        $mtimes = [];

        foreach (['sedan', 'suv', 'pickup', 'camion', 'camioneta', 'moto'] as $body) {
            foreach (['jpg', 'jpeg', 'png', 'svg'] as $ext) {
                $mtime = $this->fileMtime(public_path("images/mockups/{$body}.{$ext}"));
                if ($mtime !== null) {
                    $mtimes["{$body}.{$ext}"] = $mtime;
                }
            }
        }

        return $mtimes;
    }

    protected function fileMtime(?string $path): ?int
    {
        if ($path === null || ! is_file($path)) {
            return null;
        }

        $mtime = @filemtime($path);

        return $mtime === false ? null : (int) $mtime;
    }
}
