<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ChangeCurrencyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // La autorización se maneja en el controlador con Gate::authorize
    }

    public function rules(): array
    {
        return [
            'currency' => ['required', 'string', Rule::in(['PEN', 'USD'])],
            'exchange_rate' => ['required', 'numeric', 'min:0.01'],
        ];
    }

    public function messages(): array
    {
        return [
            'currency.required' => 'Seleccione la nueva moneda.',
            'currency.in' => 'La moneda seleccionada no es válida.',
            'exchange_rate.required' => 'Indique el nuevo tipo de cambio.',
            'exchange_rate.numeric' => 'El tipo de cambio debe ser numérico.',
            'exchange_rate.min' => 'El tipo de cambio debe ser mayor a cero.',
        ];
    }
}
