<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class VehicleRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $vehicleId = $this->route('vehicle')?->id;

        return [
            'plate' => [
                'required',
                'string',
                'max:10',
                'regex:/^[A-Z0-9-]+$/',
                Rule::unique('vehicles', 'plate')->ignore($vehicleId),
            ],
            'brand' => ['required', 'string', 'max:100'],
            'model' => ['required', 'string', 'max:100'],
            'body_type' => ['nullable', 'string', 'max:50'],
            'color' => ['nullable', 'string', 'max:50'],
            'vin' => ['nullable', 'string', 'max:50'],
            'engine_number' => ['nullable', 'string', 'max:50'],
            'year' => ['nullable', 'integer', 'min:1900', 'max:' . (date('Y') + 1)],
            'next_technical_review_date' => ['nullable', 'date'],
            'technical_review_reminder_days' => ['nullable', 'integer', 'min:1', 'max:90'],
            'establishment_id' => ['required', 'exists:establishments,id'],
            'relationships' => ['nullable', 'array'],
            'relationships.*.party_id' => ['required_with:relationships', 'exists:parties,id'],
            'relationships.*.role' => ['required_with:relationships', Rule::in([
                'owner', 'driver', 'approver', 'operator', 'billing', 'insurance_company', 'emergency_contact', 'other',
            ])],
            'relationships.*.is_primary_commercial' => ['sometimes', 'boolean'],
            'relationships.*.notes' => ['nullable', 'string', 'max:1000'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'plate.required' => 'La placa es obligatoria.',
            'plate.unique' => 'La placa ya está registrada.',
            'plate.regex' => 'La placa solo puede contener letras mayúsculas, números y guiones.',
            'brand.required' => 'La marca es obligatoria.',
            'model.required' => 'El modelo es obligatorio.',
            'establishment_id.required' => 'El establecimiento es obligatorio.',
            'relationships.*.party_id.required' => 'Debe seleccionar una party para la relación.',
            'relationships.*.role.required' => 'Debe seleccionar un rol para la relación.',
        ];
    }
}