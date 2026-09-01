<?php

namespace App\Http\Requests;

use App\Models\CheckIn;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CheckInRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // La autorización se maneja en el controlador con Gate::authorize
    }

    public function rules(): array
    {
        $checkInId = $this->route('check_in')?->id;

        return [
            'vehicle_id' => [
                'required',
                'integer',
                'exists:vehicles,id',
                Rule::unique('check_ins', 'vehicle_id')
                    ->whereNull('deleted_at')
                    // Un inventario vinculado a una OT (ingreso original o reingreso)
                    // no bloquea el registro de una nueva visita física del vehículo.
                    ->whereNull('work_order_id')
                    ->whereIn('status', ['draft', 'pending_approval', 'approved'])
                    ->ignore($checkInId),
            ],
            'client_id' => ['nullable', 'integer', 'exists:parties,id'],
            // Reingreso: OT entregada con pendientes a la que se vincula esta visita.
            'work_order_id' => ['nullable', 'integer', 'exists:work_orders,id'],
            'insurance_company_id' => [
                'nullable',
                'integer',
                'exists:parties,id',
                // Si la aseguradora se envía, debe ser una compañía de seguros
                function ($attribute, $value, $fail) {
                    if ($value && !\App\Models\Party::whereKey($value)->where('is_insurance_company', true)->exists()) {
                        $fail('La aseguradora seleccionada no es válida (debe ser una compañía de seguros).');
                    }
                },
            ],
            'service_type' => ['required', 'string', Rule::in(array_keys(CheckIn::SERVICE_TYPES))],
            'claim_number' => ['nullable', 'string', 'max:100'],
            'mileage' => ['nullable', 'integer', 'min:0', 'max:9999999'],
            'fuel_level' => ['nullable', 'string', Rule::in(array_keys(CheckIn::FUEL_LEVELS))],
            'property_card' => ['nullable', 'string', Rule::in(array_keys(CheckIn::PROPERTY_CARDS))],
            'soat_expiration' => ['nullable', 'date'],
            'technical_review_expiration' => ['nullable', 'date'],
            'keys_count' => ['nullable', 'integer', 'min:0', 'max:10'],
            'has_remote_control' => ['nullable', 'boolean'],
            'client_request' => ['nullable', 'string'],
            'observations' => ['nullable', 'string'],

            // Checklist: array dinámico {checklist_item_id => ['status' => ..., 'observations' => ...]}
            'checklist' => ['nullable', 'array'],
            'checklist.*.status' => ['nullable', Rule::in(['good', 'regular', 'bad', 'not_applicable'])],
            'checklist.*.observations' => ['nullable', 'string', 'max:500'],

            // Daños: array de filas
            'damages' => ['nullable', 'array'],
            'damages.*.id' => ['nullable', 'string', 'max:50'],
            'damages.*.damage_type' => ['required_with:damages.*', Rule::in(['scratch', 'dent', 'crack'])],
            'damages.*.side' => ['required_with:damages.*', Rule::in(['front', 'rear', 'left', 'right', 'top'])],
            'damages.*.pos_x' => ['nullable', 'integer', 'between:0,100'],
            'damages.*.pos_y' => ['nullable', 'integer', 'between:0,100'],
            'damages.*.notes' => ['nullable', 'string', 'max:500'],

            // Contactos del vehículo (guardar como vehicle_relationships)
            // Formato: relationships[] = { party_id, role, notes, is_primary_commercial }
            'relationships' => ['nullable', 'array'],
            'relationships.*.party_id' => ['required_with:relationships.*', 'integer', 'exists:parties,id'],
            'relationships.*.role' => ['required_with:relationships.*', 'string', Rule::in(['owner', 'approver', 'driver', 'operator'])],
            'relationships.*.notes' => ['nullable', 'string', 'max:500'],
            'relationships.*.is_primary_commercial' => ['nullable', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'vehicle_id.required' => 'Seleccione un vehículo.',
            'vehicle_id.exists' => 'El vehículo seleccionado no existe.',
            'vehicle_id.unique' => 'Este vehículo ya tiene un inventario abierto (borrador, en aprobación o aprobado).',
            'service_type.required' => 'El tipo de servicio es obligatorio.',
            'insurance_company_id.exists' => 'La aseguradora seleccionada no existe.',
            'damages.*.damage_type.required_with' => 'Indique el tipo de daño.',
            'damages.*.side.required_with' => 'Indique el lado del daño.',
        ];
    }
}