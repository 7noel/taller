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
            'client_id' => ['required', 'exists:clients,id'],
            'brand' => ['required', 'string', 'max:100'],
            'model' => ['required', 'string', 'max:100'],
            'body_type' => ['required', Rule::in(['sedan', 'suv', 'pickup', 'camioneta', 'camion', 'moto'])],
            'color' => ['nullable', 'string', 'max:50'],
            'vin' => ['nullable', 'string', 'max:50'],
            'engine_number' => ['nullable', 'string', 'max:50'],
            'year' => ['nullable', 'integer', 'min:1900', 'max:' . (date('Y') + 1)],
            'establishment_id' => ['required', 'exists:establishments,id'],
            'contacts' => ['nullable', 'array', 'max:3'],
            'contacts.*.type' => ['required_with:contacts', Rule::in(['approver', 'driver', 'operator'])],
            'contacts.*.name' => ['required_with:contacts', 'string', 'max:255'],
            'contacts.*.phone' => ['nullable', 'string', 'max:20'],
            'contacts.*.email' => ['nullable', 'email', 'max:255'],
            'contacts.*.company_name' => ['nullable', 'string', 'max:255'],
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
            'client_id.required' => 'Debe seleccionar un cliente.',
            'brand.required' => 'La marca es obligatoria.',
            'model.required' => 'El modelo es obligatorio.',
            'body_type.required' => 'El tipo de carrocería es obligatorio.',
            'contacts.*.name.required' => 'El nombre del contacto es obligatorio.',
        ];
    }
}