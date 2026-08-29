<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class WorkOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'check_in_id' => ['nullable', 'integer', 'exists:check_ins,id'],
            'estimate_id' => ['nullable', 'integer', 'exists:estimates,id'],
            'start_date' => ['nullable', 'date'],
            'estimated_end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'check_in_id.exists' => 'El inventario seleccionado no existe.',
            'estimate_id.exists' => 'El presupuesto seleccionado no existe.',
            'estimated_end_date.after_or_equal' => 'La fecha estimada de entrega no puede ser anterior al inicio.',
            'notes.max' => 'Las notas no pueden superar los 1000 caracteres.',
        ];
    }
}
