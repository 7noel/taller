<?php

namespace App\Http\Requests;

use App\Models\CheckIn;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AppointmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // La autorización se maneja en el controlador con Gate::authorize
    }

    public function rules(): array
    {
        return [
            'vehicle_id' => ['nullable', 'integer', 'exists:vehicles,id'],
            'party_id' => ['nullable', 'integer', 'exists:parties,id'],
            'advisor_id' => ['nullable', 'integer', 'exists:users,id'],
            'scheduled_date' => ['required', 'date'],
            'scheduled_time' => ['required', 'date_format:H:i'],
            'contact_name' => ['nullable', 'string', 'max:255'],
            'contact_phone' => ['nullable', 'string', 'max:50'],
            'contact_email' => ['nullable', 'email', 'max:255'],
            'service_type' => ['nullable', 'string', Rule::in(array_keys(CheckIn::SERVICE_TYPES))],
            'reason' => ['nullable', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'scheduled_date.required' => 'La fecha de la cita es obligatoria.',
            'scheduled_time.required' => 'La hora de la cita es obligatoria.',
            'scheduled_time.date_format' => 'La hora debe tener el formato HH:MM.',
            'vehicle_id.exists' => 'El vehículo seleccionado no existe.',
            'party_id.exists' => 'El contacto seleccionado no existe.',
            'contact_email.email' => 'El correo no tiene un formato válido.',
        ];
    }
}
