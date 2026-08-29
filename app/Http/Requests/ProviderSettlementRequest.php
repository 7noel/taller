<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProviderSettlementRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'provider_id' => ['required', 'integer', 'exists:parties,id'],
            'period_start' => ['required', 'date'],
            'period_end' => ['required', 'date', 'after_or_equal:period_start'],
            'global_discount' => ['nullable', 'numeric', 'min:0'],
            'discount_reason' => ['nullable', 'string', 'max:255'],
            'igv_rate' => ['nullable', 'numeric', 'min:0', 'max:1'],
            'detraction_rate' => ['nullable', 'numeric', 'min:0', 'max:1'],
            'voucher_ids' => ['nullable', 'array'],
            'voucher_ids.*' => ['integer', 'exists:service_vouchers,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'provider_id.required' => 'Selecciona el proveedor.',
            'period_start.required' => 'Indica el inicio del período.',
            'period_end.required' => 'Indica el fin del período.',
            'period_end.after_or_equal' => 'El fin del período no puede ser anterior al inicio.',
            'global_discount.min' => 'El descuento no puede ser negativo.',
            'voucher_ids.*.exists' => 'Uno de los comprobantes seleccionados no es válido.',
        ];
    }
}
