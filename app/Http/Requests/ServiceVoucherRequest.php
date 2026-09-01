<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ServiceVoucherRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'work_order_id' => ['required', 'integer', 'exists:work_orders,id'],
            'provider_id' => ['required', 'integer', 'exists:parties,id'],
            'execution_date' => ['required', 'date'],
            'description' => ['required', 'string', 'max:2000'],
            'agreed_amount' => ['required', 'numeric', 'min:0.01'],
            'discount_applied' => ['nullable', 'numeric', 'min:0'],
            'currency' => ['nullable', 'string', 'in:PEN,USD'],
            'exchange_rate' => ['nullable', 'numeric', 'gt:0'],
            'igv_rate' => ['nullable', 'numeric', 'min:0', 'max:1'],
            'detraction_rate' => ['nullable', 'numeric', 'min:0', 'max:1'],
        ];
    }

    public function messages(): array
    {
        return [
            'work_order_id.required' => 'Selecciona la orden de trabajo.',
            'work_order_id.exists' => 'La orden de trabajo seleccionada no es válida.',
            'provider_id.required' => 'Selecciona el proveedor del servicio.',
            'provider_id.exists' => 'El proveedor seleccionado no es válido.',
            'execution_date.required' => 'Indica la fecha de ejecución del servicio.',
            'description.required' => 'Describe el servicio tercerizado.',
            'agreed_amount.required' => 'Indica el monto acordado (sin IGV).',
            'agreed_amount.min' => 'El monto debe ser mayor a cero.',
            'discount_applied.min' => 'El descuento no puede ser negativo.',
            'igv_rate.max' => 'La tasa de IGV debe estar entre 0 y 1 (ej. 0.18).',
            'detraction_rate.max' => 'La tasa de detracción debe estar entre 0 y 1 (ej. 0.12).',
        ];
    }
}
