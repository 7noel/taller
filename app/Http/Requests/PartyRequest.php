<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PartyRequest extends FormRequest
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
        $partyId = $this->route('party')?->id;
        $type = $this->input('type', 'person');

        return [
            'type' => ['required', Rule::in(['person', 'company'])],
            'document_type' => ['required', Rule::in(['DNI', 'RUC', 'PAS', 'CEX'])],
            'document_number' => [
                'required',
                'string',
                'max:20',
                Rule::unique('parties', 'document_number')->ignore($partyId),
            ],
            'first_name' => ['nullable', 'string', 'max:255'],
            'last_name' => ['nullable', 'string', 'max:255'],
            'business_name' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
            'mobile' => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string'],
            'ubigeo_code' => ['nullable', 'string', 'size:6', 'exists:ubigeos,code'],
            'is_insurance_company' => ['sometimes', 'boolean'],
            'insurance_hourly_rate' => ['nullable', 'numeric', 'min:0'],
            'insurance_panel_rate' => ['nullable', 'numeric', 'min:0'],
            'receive_promotions' => ['sometimes', 'boolean'],
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
            'type.required' => 'El tipo de party es obligatorio.',
            'type.in' => 'El tipo de party no es válido.',
            'document_type.required' => 'El tipo de documento es obligatorio.',
            'document_type.in' => 'El tipo de documento no es válido.',
            'document_number.required' => 'El número de documento es obligatorio.',
            'document_number.unique' => 'El número de documento ya está registrado.',
        ];
    }
}