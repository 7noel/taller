<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StockMovementRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'part_id' => ['required', 'exists:parts,id'],
            'warehouse_id' => ['required', 'exists:warehouses,id'],
            'type' => ['required', Rule::in(['entry', 'exit', 'adjustment'])],
            'quantity' => ['required', 'numeric', 'gt:0'],
            'currency' => ['required', Rule::in(['PEN', 'USD'])],
            'exchange_rate' => ['nullable', 'numeric', 'gt:0', 'required_if:currency,USD'],
            'unit_cost' => ['required', 'numeric', 'min:0'],
            'reference' => ['nullable', 'string', 'max:255'],
            'document_type' => ['nullable', 'string', 'max:255'],
            'document_id' => ['nullable', 'integer'],
        ];
    }

    public function messages(): array
    {
        return [
            'part_id.required' => 'Seleccione un repuesto.',
            'warehouse_id.required' => 'Seleccione un almacén.',
            'type.required' => 'Seleccione el tipo de movimiento.',
            'quantity.required' => 'La cantidad es obligatoria.',
            'quantity.gt' => 'La cantidad debe ser mayor a 0.',
            'unit_cost.required' => 'El costo unitario es obligatorio.',
            'exchange_rate.required_if' => 'El tipo de cambio es obligatorio para movimientos en USD.',
        ];
    }
}