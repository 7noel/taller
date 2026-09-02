<?php

namespace App\Services;

use App\Models\Brand;
use App\Models\Vehicle;
use App\Models\VehicleModel;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BrandService
{
    public function findOrCreateBrand(string $name): Brand
    {
        $name = mb_strtoupper(trim($name));

        return Brand::firstOrCreate(
            ['name' => $name],
            ['created_by' => Auth::id()]
        );
    }

    public function create(array $data): Brand
    {
        $userId = Auth::id();

        return DB::transaction(function () use ($data, $userId) {
            $brand = Brand::create([
                'name' => $data['name'],
                'created_by' => $userId,
                'updated_by' => $userId,
            ]);

            $this->syncModels($brand, $data['models'] ?? []);

            return $brand;
        });
    }

    /**
     * Actualiza la marca y sincroniza sus modelos (diff/upsert).
     *
     * @return string[] advertencias (modelos que no se pudieron eliminar)
     */
    public function update(Brand $brand, array $data): array
    {
        $models = $data['models'] ?? null;
        unset($data['models']);
        $data['updated_by'] = Auth::id();

        return DB::transaction(function () use ($brand, $data, $models) {
            $brand->update($data);

            $warnings = [];
            if ($models !== null) {
                $warnings = $this->syncModels($brand, $models);
            }

            return $warnings;
        });
    }

    /**
     * Sincroniza la colección de modelos de una marca con lo que envía el
     * formulario (diferencias/upsert, nunca borrar todo y reinsertar):
     * - filas existentes (con id) -> update de nombre
     * - filas nuevas (sin id) -> create
     * - registros de BD que ya no vienen -> delete (salvo que tengan vehículos)
     *
     * @return string[] advertencias de modelos conservados por estar en uso
     */
    public function syncModels(Brand $brand, array $rows): array
    {
        $warnings = [];
        $userId = Auth::id();
        $existing = $brand->models()->get()->keyBy('id');

        // Normaliza filas del formulario: descarta vacías y nombres duplicados.
        $incoming = [];
        $incomingNames = [];
        foreach ($rows as $row) {
            $name = mb_strtoupper(trim((string) ($row['name'] ?? '')));
            if ($name === '' || in_array($name, $incomingNames, true)) {
                continue;
            }
            $incomingNames[] = $name;
            $id = isset($row['id']) && is_numeric($row['id']) ? (int) $row['id'] : null;
            $incoming[] = ['id' => $id, 'name' => $name];
        }

        // 1) Actualizar solo los modelos existentes que vienen en el formulario.
        foreach ($incoming as $row) {
            if ($row['id'] !== null && $existing->has($row['id'])) {
                $model = $existing->get($row['id']);
                if ($model->name !== $row['name']) {
                    $model->update(['name' => $row['name'], 'updated_by' => $userId]);
                }
            }
        }

        // 2) Crear los modelos nuevos (sin id o con id que no pertenece a la marca).
        $existingNames = $existing->pluck('name')->all();
        foreach ($incoming as $row) {
            if ($row['id'] !== null && $existing->has($row['id'])) {
                continue;
            }
            if (in_array($row['name'], $existingNames, true)) {
                continue;
            }
            VehicleModel::create([
                'brand_id' => $brand->id,
                'name' => $row['name'],
                'created_by' => $userId,
                'updated_by' => $userId,
            ]);
            $existingNames[] = $row['name'];
        }

        // 3) Eliminar los modelos que ya no vienen en el formulario.
        $incomingIds = collect($incoming)
            ->pluck('id')
            ->filter(fn ($id) => $id !== null && $existing->has($id));

        foreach ($existing->keys()->diff($incomingIds) as $id) {
            $model = $existing->get($id);
            if ($model->vehicles()->exists()) {
                $warnings[] = "El modelo {$model->name} no se eliminó porque tiene vehículos asociados.";

                continue;
            }
            $model->delete();
        }

        return $warnings;
    }

    /**
     * Elimina la marca (soft delete) junto con sus modelos.
     * Devuelve false si la marca está en uso por vehículos.
     */
    public function delete(Brand $brand): bool
    {
        if ($this->isInUse($brand)) {
            return false;
        }

        $brand->models()->delete();
        $brand->delete();

        return true;
    }

    public function isInUse(Brand $brand): bool
    {
        if ($brand->vehicles()->exists()) {
            return true;
        }

        return Vehicle::query()
            ->whereIn('model_id', $brand->models()->pluck('id'))
            ->exists();
    }
}
