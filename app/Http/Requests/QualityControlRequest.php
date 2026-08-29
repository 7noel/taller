<?php

namespace App\Http\Requests;

use App\Models\WorkOrderQualityControl;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class QualityControlRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'result' => ['required', 'string', Rule::in([WorkOrderQualityControl::RESULT_APPROVED, WorkOrderQualityControl::RESULT_REJECTED])],
            'rejection_reason' => ['required_if:result,rejected', 'nullable', 'string', Rule::in(array_keys(WorkOrderQualityControl::REJECTION_REASONS))],
            'rejection_details' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'result.required' => 'Debe indicar si aprueba o rechaza el control de calidad.',
            'result.in' => 'Resultado de control de calidad inválido.',
            'rejection_reason.required_if' => 'Debe indicar la causa del rechazo.',
            'rejection_reason.in' => 'La causa del rechazo no es válida.',
            'rejection_details.max' => 'El detalle del rechazo no puede superar los 1000 caracteres.',
        ];
    }
}
