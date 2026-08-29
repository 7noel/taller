<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class InventoryGuideRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'guide_type' => ['required', Rule::in(['input', 'output', 'transfer', 'adjustment'])],
            'movement_reason_code' => ['nullable', 'exists:inventory_movement_reasons,code', 'required_if:guide_type,input,output'],
            'origin_warehouse_id' => ['nullable', 'exists:warehouses,id', 'required_if:guide_type,output,transfer'],
            'destination_warehouse_id' => ['nullable', 'exists:warehouses,id', 'required_if:guide_type,input,transfer'],
            'warehouse_id' => ['nullable', 'exists:warehouses,id', 'required_if:guide_type,adjustment'],
            'provider_id' => ['nullable', 'exists:parties,id'],
            'work_order_id' => ['nullable', 'exists:work_orders,id'],
            'purchase_order_id' => ['nullable', 'exists:purchase_orders,id'],
            'provider_invoice' => ['nullable', 'string', 'max:30'],
            'provider_guide' => ['nullable', 'string', 'max:30'],
            'movement_date' => ['nullable', 'date'],
            'currency' => ['nullable', Rule::in(['PEN', 'USD'])],
            'exchange_rate' => ['nullable', 'numeric', 'gt:0'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.part_id' => ['required', 'exists:parts,id'],
            'items.*.quantity' => ['required', 'numeric'],
            'items.*.unit_cost' => ['nullable', 'numeric', 'min:0'],
        ];
    }

    public function messages(): array
    {
        return [
            'guide_type.required' => 'Seleccione el tipo de guía.',
            'movement_reason_code.required_if' => 'Seleccione el motivo del movimiento.',
            'origin_warehouse_id.required_if' => 'Indique el almacén de origen.',
            'destination_warehouse_id.required_if' => 'Indique el almacén de destino.',
            'warehouse_id.required_if' => 'Indique el almacén del ajuste.',
            'items.required' => 'Agregue al menos un repuesto.',
            'items.min' => 'Agregue al menos un repuesto.',
            'items.*.part_id.required' => 'Seleccione el repuesto en cada línea.',
        ];
    }
}
