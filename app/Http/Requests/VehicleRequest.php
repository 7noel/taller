<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class VehicleRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        $id = $this->route('vehicle')?->id;

        return [
            'plate' => ['required', 'string', 'regex:/^[A-Z0-9]{6,7}$/', Rule::unique('vehicles', 'plate')->ignore($id)],
            'brand_id' => ['required', 'exists:brands,id'],
            'model_id' => ['required', 'exists:models,id'],
            'color' => ['nullable', 'string', 'max:50'],
            'vin' => ['nullable', 'string', 'max:50'],
            'engine_number' => ['nullable', 'string', 'max:50'],
            'year' => ['nullable', 'integer', 'min:1900', 'max:' . (date('Y') + 1)],
            'body_type' => ['nullable', 'string', 'max:50'],
            'technical_review_date' => ['nullable', 'date'],
            'review_reminder_days' => ['nullable', 'integer', 'min:1', 'max:90'],
            'relationships' => ['nullable', 'array'],
            'relationships.*.party_id' => ['required_with:relationships', 'exists:parties,id'],
            'relationships.*.role' => ['required_with:relationships', Rule::in(['owner', 'driver', 'approver', 'operator', 'billing', 'insurance_company', 'emergency_contact', 'other'])],
            'relationships.*.is_primary_commercial' => ['sometimes', 'boolean'],
            'relationships.*.notes' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'plate.required' => 'La placa es obligatoria.',
            'plate.regex' => 'La placa debe tener 6 o 7 caracteres, solo letras y números.',
            'plate.unique' => 'La placa ya está registrada.',
            'brand_id.required' => 'Debe seleccionar una marca.',
            'model_id.required' => 'Debe seleccionar un modelo.',
        ];
    }

    public function withValidator(\Illuminate\Contracts\Validation\Validator $validator): void
    {
        $validator->after(function ($validator) {
            $relationships = $this->input('relationships', []);

            if (! is_array($relationships)) {
                return;
            }

            $owners = collect($relationships)->filter(fn ($rel) => ($rel['role'] ?? null) === 'owner');

            if ($owners->count() > 1) {
                $validator->errors()->add('relationships', 'Solo puede haber un propietario por vehículo.');
            }

            $primary = collect($relationships)->filter(fn ($rel) => ! empty($rel['is_primary_commercial'] ?? false));

            if ($primary->count() > 1) {
                $validator->errors()->add('relationships', 'Solo puede haber un contacto comercial principal por vehículo.');
            }
        });
    }
}
