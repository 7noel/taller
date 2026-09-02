<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

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
    }
}
