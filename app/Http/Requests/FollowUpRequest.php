<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class FollowUpRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // La autorización se maneja en el controlador con Gate::authorize
    }

    public function rules(): array
    {
        return [
            'party_id' => ['nullable', 'integer', 'exists:parties,id'],
            'vehicle_id' => ['nullable', 'integer', 'exists:vehicles,id'],
            'date' => ['required', 'date'],
            'type' => ['required', 'string', Rule::in(['call', 'whatsapp', 'email', 'visit'])],
            'notes' => ['nullable', 'string'],
            'next_action_date' => ['nullable', 'date'],
            'done' => ['nullable', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'date.required' => 'La fecha del seguimiento es obligatoria.',
            'type.required' => 'El tipo de seguimiento es obligatorio.',
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            if (! $this->filled('party_id') && ! $this->filled('vehicle_id')) {
                $validator->errors()->add('party_id', 'Indique un cliente o un vehículo para el seguimiento.');
            }
        });
    }
}
