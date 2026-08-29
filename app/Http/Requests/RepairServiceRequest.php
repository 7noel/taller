<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RepairServiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'service_category_id' => ['nullable', 'exists:service_categories,id'],
            'pricing_type' => ['required', Rule::in(['fixed', 'time_based'])],
            'uom' => ['nullable', 'exists:unit_measures,code'],
            'estimated_hours' => ['nullable', 'numeric', 'min:0'],
            'min_hours' => ['nullable', 'numeric', 'min:0'],
            'sell_price' => ['required', 'numeric', 'min:0'],
            'currency' => ['required', Rule::in(['PEN', 'USD'])],
            'cost_price' => ['required', 'numeric', 'min:0'],
            'cost_currency' => ['required', Rule::in(['PEN', 'USD'])],
            'default_provider_id' => ['nullable', 'exists:parties,id'],
            'is_outsourced' => ['sometimes', 'boolean'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'El nombre del servicio es obligatorio.',
            'pricing_type.required' => 'Seleccione el tipo de cobro.',
            'sell_price.required' => 'El precio de venta es obligatorio.',
            'cost_price.required' => 'El costo es obligatorio.',
        ];
    }
}