<?php

namespace App\Http\Requests;

use App\Models\VehicleModel;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class BrandRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:120', Rule::unique('brands', 'name')->ignore($this->route('brand'))],
            'models' => ['nullable', 'array'],
            'models.*.id' => ['nullable', 'integer', 'exists:models,id'],
            'models.*.name' => ['nullable', 'string', 'max:120'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'El nombre de la marca es obligatorio.',
            'name.max' => 'El nombre de la marca no puede superar los 120 caracteres.',
            'name.unique' => 'Ya existe una marca con ese nombre.',
            'models.*.name.max' => 'El nombre de un modelo no puede superar los 120 caracteres.',
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('name')) {
            $this->merge(['name' => mb_strtoupper(trim((string) $this->input('name')))]);
        }

        // Normaliza los nombres de los modelos (mayúsculas) para evitar duplicados
        // por diferencias de caja (ej. "corolla" vs "COROLLA") y conserva el id.
        if ($this->has('models') && is_array($this->input('models'))) {
            $models = [];
            foreach ($this->input('models') as $row) {
                $id = isset($row['id']) && is_numeric($row['id']) ? (int) $row['id'] : null;
                $name = isset($row['name']) ? mb_strtoupper(trim((string) $row['name'])) : '';
                $models[] = ['id' => $id, 'name' => $name];
            }
            $this->merge(['models' => $models]);
        }
    }

    /**
     * Verifica que ningún modelo esté duplicado: repetido en el mismo formulario
     * o ya existente en la marca (en edición), mostrando un mensaje claro.
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function ($validator) {
            $models = $this->input('models') ?? [];
            $existing = $this->existingModelsByName();
            $seen = [];
            $duplicates = [];

            foreach ($models as $row) {
                $name = isset($row['name']) ? mb_strtoupper(trim((string) $row['name'])) : '';
                if ($name === '') {
                    continue;
                }

                $rowId = isset($row['id']) && is_numeric($row['id']) ? (int) $row['id'] : null;

                if (in_array($name, $seen, true)) {
                    $duplicates[$name] = true;
                }
                $seen[] = $name;

                if (array_key_exists($name, $existing) && $existing[$name] !== $rowId) {
                    $duplicates[$name] = true;
                }
            }

            if ($duplicates !== []) {
                $names = implode('», «', array_map('mb_strtoupper', array_keys($duplicates)));
                $validator->errors()->add(
                    'models',
                    "Modelos duplicados o que ya existen en esta marca: «{$names}». Revisa la lista."
                );
            }
        });
    }

    /**
     * Nombres de modelos existentes de la marca (en edición) con su id: [name => id].
     */
    protected function existingModelsByName(): array
    {
        $brand = $this->route('brand');

        if (! $brand) {
            return [];
        }

        return VehicleModel::where('brand_id', $brand->id)
            ->pluck('id', 'name')
            ->map(fn ($id) => (int) $id)
            ->all();
    }
}
