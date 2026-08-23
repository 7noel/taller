<?php

namespace App\Services;

use App\Models\Vehicle;
use Illuminate\Support\Facades\Auth;

class VehicleService
{
    protected BrandService $brandService;
    protected VehicleModelService $modelService;

    public function __construct(BrandService $brandService, VehicleModelService $modelService)
    {
        $this->brandService = $brandService;
        $this->modelService = $modelService;
    }

    public function create(array $data): Vehicle
    {
        $data = $this->normalizeVehicleData($data);
        $data['created_by'] = Auth::id();
        $data['updated_by'] = Auth::id();

        $relationships = $data['relationships'] ?? [];
        unset($data['relationships']);

        $vehicle = Vehicle::create($data);

        if (! empty($relationships)) {
            $this->syncRelationships($vehicle, $relationships);
        }

        return $vehicle;
    }

    public function update(Vehicle $vehicle, array $data): Vehicle
    {
        $data = $this->normalizeVehicleData($data);
        $data['updated_by'] = Auth::id();

        $relationships = $data['relationships'] ?? null;
        unset($data['relationships']);

        $vehicle->update($data);

        if ($relationships !== null) {
            $this->syncRelationships($vehicle, $relationships);
        }

        return $vehicle;
    }

    public function delete(Vehicle $vehicle): bool
    {
        $vehicle->relationships()->delete();

        return $vehicle->delete();
    }

    public function createFromSunarp(array $data): Vehicle
    {
        $brandName = $data['brand'] ?? null;
        $modelName = $data['model'] ?? null;

        if ($brandName && $modelName) {
            $brand = $this->brandService->findOrCreateBrand($brandName);
            $model = $this->modelService->findOrCreateModel($brand->id, $modelName);
            $data['model_id'] = $model->id;
        }

        unset($data['brand'], $data['model']);

        return $this->create($data);
    }

    protected function normalizeVehicleData(array $data): array
    {
        $data['plate'] = strtoupper($data['plate'] ?? '');
        $data['vin'] = isset($data['vin']) ? strtoupper($data['vin']) : null;
        $data['engine_number'] = isset($data['engine_number']) ? strtoupper($data['engine_number']) : null;
        $data['color'] = isset($data['color']) ? strtoupper($data['color']) : null;

        return $data;
    }

    protected function syncRelationships(Vehicle $vehicle, array $relationships): void
    {
        // Sincronización inteligente: preserva los ids existentes, restaura
        // soft-deleted del mismo (vehicle_id, party_id, role) y elimina (soft)
        // solo las relaciones que ya no vienen del formulario.
        $existing = $vehicle->relationships()->withTrashed()->get()->keyBy(fn ($rel) => $rel->party_id.'-'.$rel->role);

        $incomingKeys = [];

        foreach ($relationships as $relationship) {
            $key = $relationship['party_id'].'-'.$relationship['role'];
            $incomingKeys[] = $key;

            $data = [
                'is_primary_commercial' => $relationship['is_primary_commercial'] ?? false,
                'notes' => $relationship['notes'] ?? null,
                'updated_by' => Auth::id(),
            ];

            $rel = $existing->get($key);

            if ($rel && $rel->trashed()) {
                // Existía antes (soft-deleted): restaurar la misma fila para no violar el índice único
                $rel->restore();
                $rel->update($data);
            } elseif ($rel) {
                // Ya está activa: solo actualizar campos
                $rel->update($data);
            } else {
                // Nueva relación
                $vehicle->relationships()->create([
                    'party_id' => $relationship['party_id'],
                    'role' => $relationship['role'],
                    'is_primary_commercial' => $data['is_primary_commercial'],
                    'notes' => $data['notes'],
                    'created_by' => Auth::id(),
                    'updated_by' => Auth::id(),
                ]);
            }
        }

        // Eliminar (soft) las relaciones activas que ya no vienen del formulario
        foreach ($existing as $rel) {
            if (! $rel->trashed() && ! in_array($rel->party_id.'-'.$rel->role, $incomingKeys, true)) {
                $rel->delete();
            }
        }
    }
}