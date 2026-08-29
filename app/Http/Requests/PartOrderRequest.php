<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PartOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'part_id' => ['required', 'exists:parts,id'],
            'estimate_id' => ['nullable', 'exists:estimates,id'],
            'provider_id' => ['nullable', 'exists:parties,id'],
            'quantity' => ['required', 'numeric', 'gt:0'],
            'ordered_at' => ['nullable', 'date'],
            'expected_delivery' => ['nullable', 'date'],
            'tracking_number' => ['nullable', 'string', 'max:100'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'part_id.required' => 'Seleccione el repuesto.',
            'quantity.gt' => 'La cantidad debe ser mayor a 0.',
        ];
    }
}
