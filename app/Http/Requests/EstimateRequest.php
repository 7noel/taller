<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class EstimateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // La autorización se maneja en el controlador con Gate::authorize
    }

    public function rules(): array
    {
        return [
            'check_in_id' => ['nullable', 'integer', 'exists:check_ins,id'],
            'vehicle_id' => ['required', 'integer', 'exists:vehicles,id'],
            'client_id' => ['required', 'integer', 'exists:parties,id'],
            'insurance_company_id' => [
                'nullable',
                'integer',
                'exists:parties,id',
                function ($attribute, $value, $fail) {
                    if ($value && !\App\Models\Party::whereKey($value)->where('is_insurance_company', true)->exists()) {
                        $fail('La aseguradora seleccionada no es válida (debe ser una compañía de seguros).');
                    }
                },
            ],
            'claim_number' => ['nullable', 'string', 'max:100'],
            'parent_estimate_id' => [
                'nullable',
                'integer',
                'exists:estimates,id',
                function ($attribute, $value, $fail) {
                    if (!$value) {
                        return;
                    }

                    $parent = \App\Models\Estimate::withTrashed()->find($value);

                    if (!$parent) {
                        return; // lo cubre la regla exists
                    }

                    if ($parent->parent_estimate_id) {
                        $fail('El presupuesto padre no puede ser a su vez una ampliación (solo se permite un nivel).');
                    }

                    if ((int) $parent->vehicle_id !== (int) $this->input('vehicle_id')) {
                        $fail('La ampliación debe pertenecer al mismo vehículo que el presupuesto padre.');
                    }
                },
            ],
            'service_type' => ['required', 'string', Rule::in(array_keys(\App\Models\CheckIn::SERVICE_TYPES))],
            'advisor_id' => ['nullable', 'integer', 'exists:users,id'],
            'work_days' => ['nullable', 'integer', 'min:0', 'max:999'],
            'contact_name' => ['nullable', 'string', 'max:255'],
            'contact_phone' => ['nullable', 'string', 'max:50'],
            'contact_email' => ['nullable', 'email', 'max:255'],
            'comments' => ['nullable', 'string'],
            'hourly_rate' => ['nullable', 'numeric', 'min:0'],
            'panel_rate' => ['nullable', 'numeric', 'min:0'],
            'currency' => ['nullable', 'string', 'max:3'],
            'exchange_rate' => ['nullable', 'numeric', 'min:0'],
            'global_discount_type' => ['nullable', Rule::in(['percentage', 'fixed'])],
            'global_discount_value' => [
                'nullable',
                'numeric',
                'min:0',
                function ($attribute, $value, $fail) {
                    $type = $this->input('global_discount_type');

                    if (!$type && $value !== null && (float) $value > 0) {
                        $fail('Seleccione el tipo de descuento global antes de indicar un valor.');
                    }

                    if ($type && $value === null) {
                        $fail('Indique el valor del descuento global.');
                    }

                    if ($type === 'percentage' && (float) $value > 100) {
                        $fail('El descuento porcentual no puede superar 100.');
                    }
                },
            ],

            // Ítems del presupuesto
            'items' => ['nullable', 'array'],
            'items.*.id' => ['nullable', 'integer'],
            'items.*.service_id' => [
                'nullable',
                'integer',
                'exists:repair_services,id',
                function ($attribute, $value, $fail) {
                    $index = $this->itemIndex($attribute);
                    if ($value && $this->input("items.{$index}.part_id")) {
                        $fail('Una fila no puede tener servicio y repuesto a la vez.');
                    }
                },
            ],
            'items.*.part_id' => [
                'nullable',
                'integer',
                'exists:parts,id',
                function ($attribute, $value, $fail) {
                    $index = $this->itemIndex($attribute);
                    if ($value && $this->input("items.{$index}.service_id")) {
                        $fail('Una fila no puede tener servicio y repuesto a la vez.');
                    }
                },
            ],
            'items.*.item_type' => ['nullable', Rule::in(['service', 'part'])],
            'items.*.service_category_id' => ['nullable', 'integer', 'exists:service_categories,id'],
            'items.*.part_category_id' => ['nullable', 'integer', 'exists:part_categories,id'],
            'items.*.description' => [
                'nullable', 'string', 'max:500',
                function ($attribute, $value, $fail) {
                    $index = $this->itemIndex($attribute);
                    $serviceId = $this->input("items.{$index}.service_id");
                    $partId = $this->input("items.{$index}.part_id");

                    if (!$serviceId && !$partId) {
                        if (empty($value) || !trim($value)) {
                            $fail('Cada ítem debe tener un servicio, un repuesto o una descripción.');

                            return;
                        }

                        $itemType = $this->input("items.{$index}.item_type");
                        if (!in_array($itemType, ['service', 'part'], true)) {
                            $fail('En ítems libres indique si es Servicio o Repuesto.');
                        }
                        if ($itemType === 'service' && !$this->input("items.{$index}.service_category_id")) {
                            $fail('En ítems libres de tipo Servicio indique la categoría de servicio.');
                        }
                        if ($itemType === 'part' && !$this->input("items.{$index}.part_category_id")) {
                            $fail('En ítems libres de tipo Repuesto indique la categoría de repuesto.');
                        }
                    }
                },
            ],
            'items.*.quantity' => ['nullable', 'numeric', 'min:0'],
            'items.*.unit_price' => ['nullable', 'numeric', 'min:0'],
            'items.*.discount_pct' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'items.*.supply_source' => ['nullable', Rule::in(['internal', 'external', 'insurance'])],
            'items.*.cost_price' => ['nullable', 'numeric', 'min:0'],

            // Órdenes de compra de terceros
            'third_party_orders' => ['nullable', 'array'],
            'third_party_orders.*.id' => ['nullable', 'integer'],
            'third_party_orders.*.description' => ['required_with:third_party_orders.*.amount_without_iva', 'string', 'max:1000'],
            'third_party_orders.*.amount_without_iva' => ['nullable', 'numeric', 'min:0'],
            'third_party_orders.*.provider_name' => ['nullable', 'string', 'max:255'],

            // Franquicia (informativa; no afecta totales del presupuesto)
            'franchise_minimum_amount' => ['nullable', 'numeric', 'min:0'],
            'franchise_percentage' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'franchise_minimum_includes_tax' => ['nullable', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'vehicle_id.required' => 'Seleccione un vehículo.',
            'vehicle_id.exists' => 'El vehículo seleccionado no existe.',
            'client_id.required' => 'Seleccione un cliente.',
            'client_id.exists' => 'El cliente seleccionado no existe.',
            'insurance_company_id.exists' => 'La aseguradora seleccionada no existe.',
            'advisor_id.exists' => 'El asesor seleccionado no existe.',
            'service_type.required' => 'Seleccione el tipo de servicio.',
            'service_type.in' => 'El tipo de servicio no es válido.',
            'global_discount_type.in' => 'El tipo de descuento global no es válido.',
            'global_discount_value.numeric' => 'El valor del descuento global debe ser numérico.',
            'items.*.service_id.exists' => 'El servicio seleccionado no existe.',
            'items.*.part_id.exists' => 'El repuesto seleccionado no existe.',
            'items.*.service_category_id.exists' => 'La categoría de servicio no es válida.',
            'items.*.part_category_id.exists' => 'La categoría de repuesto no es válida.',
            'items.*.supply_source.in' => 'La fuente de suministro no es válida.',
            'items.*.item_type.in' => 'El tipo de ítem no es válido.',
        ];
    }

    protected function prepareForValidation(): void
    {
        $items = $this->input('items', []);

        if (is_array($items)) {
            $normalized = [];
            foreach ($items as $index => $item) {
                $item['quantity'] = (float) ($item['quantity'] ?? 0);
                $item['unit_price'] = (float) ($item['unit_price'] ?? 0);
                $item['discount_pct'] = (float) ($item['discount_pct'] ?? 0);
                $item['cost_price'] = (float) ($item['cost_price'] ?? 0);
                $normalized[$index] = $item;
            }
            $this->merge(['items' => $normalized]);
        }

        $orders = $this->input('third_party_orders', []);

        if (is_array($orders)) {
            $normalized = [];
            foreach ($orders as $index => $order) {
                $order['amount_without_iva'] = (float) ($order['amount_without_iva'] ?? 0);
                $normalized[$index] = $order;
            }
            $this->merge(['third_party_orders' => $normalized]);
        }

        $this->merge([
            'franchise_minimum_amount' => $this->has('franchise_minimum_amount') ? (float) $this->input('franchise_minimum_amount') : null,
            'franchise_percentage' => $this->has('franchise_percentage') ? (float) $this->input('franchise_percentage') : null,
            'franchise_minimum_includes_tax' => $this->boolean('franchise_minimum_includes_tax'),
        ]);
    }

    /**
     * Extrae el índice de la fila desde un atributo como "items.12.service_id".
     */
    protected function itemIndex(string $attribute): ?string
    {
        if (preg_match('/items\.(\d+)\./', $attribute, $matches)) {
            return $matches[1];
        }

        return null;
    }
}