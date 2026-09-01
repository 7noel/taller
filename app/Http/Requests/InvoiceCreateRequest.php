<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class InvoiceCreateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $origin = $this->input('origin', Invoice::ORIGIN_OT);
        $types = ['advance', 'franchise', 'insurance', 'regular', 'free'];

        $rules = [
            'origin' => ['required', Rule::in(['ot', 'estimate', 'free'])],
            'party_id' => 'required|exists:parties,id',
            'invoice_type' => ['required', Rule::in($types)],
            'invoice_date' => 'nullable|date',
            'observations' => 'nullable|string|max:1000',
        ];

        if ($origin === 'ot') {
            $rules['work_order_id'] = 'required|exists:work_orders,id';
        }

        if ($origin === 'estimate') {
            $rules['estimate_ids'] = 'required|array|min:1';
            $rules['estimate_ids.*'] = 'integer|exists:estimates,id';
        }

        if ($this->input('invoice_type') === 'advance') {
            $rules['advance_amount'] = 'required|numeric|min:0.01';
        }

        if ($origin === 'free') {
            $rules['items'] = 'required|array|min:1';
            $rules['items.*.description'] = 'required|string|max:250';
            $rules['items.*.quantity'] = 'required|numeric|min:0.01';
            $rules['items.*.unit_price'] = 'required|numeric|min:0';
            $rules['items.*.uom'] = 'nullable|string|max:5';
            $rules['items.*.affectation_igv_type'] = 'nullable|string|max:5';
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'party_id.required' => 'Selecciona el receptor del comprobante.',
            'work_order_id.required' => 'Selecciona la orden de trabajo.',
            'estimate_ids.required' => 'Selecciona al menos un presupuesto.',
            'advance_amount.required' => 'Indica el monto del adelanto.',
            'items.required' => 'Agrega al menos un ítem.',
        ];
    }
}
