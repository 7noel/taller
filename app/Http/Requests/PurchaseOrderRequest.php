<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PurchaseOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'provider_id' => ['required', 'exists:parties,id'],
            'warehouse_id' => ['nullable', 'exists:warehouses,id'],
            'order_date' => ['nullable', 'date'],
            'expected_delivery' => ['nullable', 'date', 'after_or_equal:order_date'],
            'status' => ['nullable', Rule::in(['draft', 'ordered', 'cancelled'])],
            'currency' => ['required', Rule::in(['PEN', 'USD'])],
            'exchange_rate' => ['nullable', 'numeric', 'gt:0', 'required_if:currency,USD'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.part_id' => ['required', 'exists:parts,id'],
            'items.*.quantity' => ['required', 'numeric', 'gt:0'],
            'items.*.unit_cost' => ['required', 'numeric', 'min:0'],
            'items.*.uom' => ['nullable', 'string', 'max:5'],
        ];
    }

    public function messages(): array
    {
        return [
            'provider_id.required' => 'Seleccione el proveedor.',
            'items.required' => 'Agregue al menos un repuesto a la orden.',
            'items.min' => 'Agregue al menos un repuesto a la orden.',
            'items.*.part_id.required' => 'Seleccione el repuesto en cada línea.',
            'items.*.quantity.gt' => 'La cantidad debe ser mayor a 0.',
            'items.*.unit_cost.required' => 'Indique el costo unitario en cada línea.',
            'exchange_rate.required_if' => 'El tipo de cambio es obligatorio para compras en USD.',
        ];
    }
}
