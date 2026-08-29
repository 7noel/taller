<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PartRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->route('part')?->id;

        return [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'sku' => ['required', 'string', 'max:100', Rule::unique('parts', 'sku')->ignore($id)],
            'manufacturer_code' => ['nullable', 'string', 'max:100'],
            'barcode' => ['nullable', 'string', 'max:100', Rule::unique('parts', 'barcode')->ignore($id)],
            'part_brand_id' => ['nullable', 'exists:part_brands,id'],
            'part_category_id' => ['nullable', 'exists:part_categories,id'],
            'uom' => ['nullable', 'exists:unit_measures,code'],
            'min_stock' => ['nullable', 'integer', 'min:0'],
            'cost_price' => ['required', 'numeric', 'min:0'],
            'cost_currency' => ['required', Rule::in(['PEN', 'USD'])],
            'sell_price' => ['required', 'numeric', 'min:0'],
            'currency' => ['required', Rule::in(['PEN', 'USD'])],
            'is_active' => ['sometimes', 'boolean'],
            // Inventario inicial (solo al crear)
            'initial_quantity' => ['nullable', 'numeric', 'min:0'],
            'initial_warehouse_id' => ['nullable', 'required_with:initial_quantity', 'exists:warehouses,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'El nombre del repuesto es obligatorio.',
            'sku.required' => 'El código interno (SKU) es obligatorio.',
            'sku.unique' => 'El SKU ya está registrado.',
            'barcode.unique' => 'El código de barras ya está registrado.',
        ];
    }
}