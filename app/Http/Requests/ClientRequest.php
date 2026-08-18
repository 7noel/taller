<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ClientRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $clientId = $this->route('client')?->id;

        return [
            'document_type' => ['required', Rule::in(['DNI', 'RUC', 'PAS', 'CEX'])],
            'document_number' => [
                'required',
                'string',
                'max:20',
                Rule::unique('clients', 'document_number')->ignore($clientId),
            ],
            'business_name' => ['required', 'string', 'max:255'],
            'ubigeo_code' => ['nullable', 'string', 'size:6', 'exists:ubigeos,code'],
            'address' => ['nullable', 'string'],
            'phone' => ['nullable', 'string', 'max:20'],
            'mobile' => ['nullable', 'string', 'max:20'],
            'email' => ['nullable', 'email', 'max:255'],
            'is_insurance_company' => ['sometimes', 'boolean'],
            'insurance_hourly_rate' => ['nullable', 'numeric', 'min:0'],
            'insurance_panel_rate' => ['nullable', 'numeric', 'min:0'],
            'establishment_id' => ['required', 'exists:establishments,id'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'document_type.required' => 'El tipo de documento es obligatorio.',
            'document_type.in' => 'El tipo de documento no es válido.',
            'document_number.required' => 'El número de documento es obligatorio.',
            'document_number.unique' => 'El número de documento ya está registrado.',
            'business_name.required' => 'La razón social o nombre es obligatorio.',
            'establishment_id.required' => 'El establecimiento es obligatorio.',
        ];
    }
}