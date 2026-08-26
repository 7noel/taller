@php
    $isEdit = isset($estimate) && $estimate !== null;
    $items = $isEdit ? ($estimate->items ?? collect()) : collect();
    $priceLabel = ($establishment->prices_include_tax ?? false) ? 'Precio (inc. IGV)' : 'Precio (sin IGV)';
    $inputCls = 'mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500';
@endphp

<input type="hidden" name="check_in_id" id="check_in_id" value="{{ old('check_in_id', $checkIn->id ?? $estimate->check_in_id ?? '') }}">
<input type="hidden" name="establishment_id" id="establishment_id" value="{{ old('establishment_id', $checkIn->establishment_id ?? $estimate->establishment_id ?? $establishment->id ?? '') }}">
<input type="hidden" name="contact_name" id="contact_name" value="{{ old('contact_name', $estimate->contact_name ?? '') }}">

{{-- ===== Cabecera en un solo card (3 columnas, sin subtítulos) ===== --}}
<div class="card p-6">
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-4">

        {{-- Vehículo --}}
        <div>
            <label for="vehicle_id" class="block text-sm font-medium text-gray-700">Vehículo <span class="text-red-500">*</span></label>
            <select id="vehicle_id" name="vehicle_id" class="{{ $inputCls }}"></select>
            @error('vehicle_id')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
        </div>

        {{-- Contacto del vehículo (miniselector) --}}
        <div>
            <div class="flex items-end gap-2">
                <div class="flex-1">
                    <label for="recipient_role" class="block text-sm font-medium text-gray-700">Contacto del vehículo</label>
                    <select id="recipient_role" name="recipient_role" class="{{ $inputCls }}">
                        <option value="">— Seleccionar rol —</option>
                    </select>
                </div>
                <button type="button" id="btn-recipient-add" class="btn-icon btn-icon-blue" title="Agregar contacto al vehículo">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                </button>
                <button type="button" id="btn-recipient-edit" class="btn-icon btn-icon-amber" title="Editar datos del contacto">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                </button>
            </div>
        </div>

        {{-- Destinatario (hidden: se rellena desde el rol seleccionado) --}}
        <input type="hidden" id="client_id" name="client_id" value="{{ old('client_id', $estimate->client_id ?? '') }}">
        @error('client_id')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror

        {{-- Celular / Email (solo lectura: datos del contacto del vehículo) --}}
        <div class="sm:col-span-2 xl:col-span-1">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="contact_phone" class="block text-sm font-medium text-gray-700">Celular</label>
                    <input type="text" id="contact_phone" name="contact_phone" readonly value="{{ old('contact_phone', $estimate->contact_phone ?? '') }}" class="{{ $inputCls }} bg-gray-100">
                </div>
                <div>
                    <label for="contact_email" class="block text-sm font-medium text-gray-700">Email</label>
                    <input type="email" id="contact_email" name="contact_email" readonly value="{{ old('contact_email', $estimate->contact_email ?? '') }}" class="{{ $inputCls }} bg-gray-100">
                </div>
            </div>
        </div>

        {{-- Asesor --}}
        <div>
            <label for="advisor_id" class="block text-sm font-medium text-gray-700">Asesor</label>
            <select id="advisor_id" name="advisor_id" class="{{ $inputCls }}">
                <option value="">Seleccione asesor</option>
                @foreach ($advisors as $advisor)
                    <option value="{{ $advisor->id }}" @selected(old('advisor_id', $estimate->advisor_id ?? '') == $advisor->id)>{{ $advisor->name }}</option>
                @endforeach
            </select>
        </div>

        {{-- Aseguradora --}}
        <div>
            <label for="insurance_company_id" class="block text-sm font-medium text-gray-700">Aseguradora</label>
            <select id="insurance_company_id" name="insurance_company_id" class="{{ $inputCls }}"></select>
            @error('insurance_company_id')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
        </div>

        {{-- Servicio y Días de trabajo --}}
        <div class="sm:col-span-2 xl:col-span-1">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="service_type" class="block text-sm font-medium text-gray-700">Servicio <span class="text-red-500">*</span></label>
                    <select id="service_type" name="service_type" class="{{ $inputCls }}">
                        @foreach (\App\Models\CheckIn::SERVICE_TYPES as $value => $label)
                            <option value="{{ $value }}" @selected(old('service_type', $estimate->service_type ?? '') === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('service_type')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="work_days" class="block text-sm font-medium text-gray-700">Días de trabajo</label>
                    <input type="number" id="work_days" name="work_days" min="0" value="{{ old('work_days', $estimate->work_days ?? '') }}" class="{{ $inputCls }}">
                </div>
                {{-- Nº Siniestro (solo visible cuando el servicio es "siniestro") --}}
                <div id="claim-number-wrap" class="{{ old('service_type', $estimate->service_type ?? '') === 'siniestro' ? '' : 'hidden' }}">
                    <label for="claim_number" class="block text-sm font-medium text-gray-700">Nº Siniestro</label>
                    <input type="text" id="claim_number" name="claim_number" value="{{ old('claim_number', $estimate->claim_number ?? '') }}" class="{{ $inputCls }}">
                </div>
            </div>
        </div>

        {{-- Moneda y Tipo de cambio--}}
        <div class="sm:col-span-2 xl:col-span-1">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="currency" class="block text-sm font-medium text-gray-700">Moneda</label>
                    <select id="currency" name="currency" class="{{ $inputCls }}">
                        <option value="PEN" @selected(old('currency', $estimate->currency ?? '') === 'PEN')>Soles (PEN)</option>
                        <option value="USD" @selected(old('currency', $estimate->currency ?? '') === 'USD')>Dólares (USD)</option>
                    </select>
                </div>
                <div>
                    <label for="exchange_rate" class="block text-sm font-medium text-gray-700">Tipo de cambio</label>
                    <input type="number" id="exchange_rate" name="exchange_rate" step="0.0001" min="0" value="{{ old('exchange_rate', $estimate->exchange_rate ?? 1) }}" class="{{ $inputCls }}">
                </div>
            </div>
        </div>

        {{-- Descuento global tipo y valor--}}
        <div class="sm:col-span-2 xl:col-span-1">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="global_discount_type" class="block text-sm font-medium text-gray-700">Dscto global — Tipo</label>
                    <select id="global_discount_type" name="global_discount_type" class="{{ $inputCls }}">
                        <option value="">Sin descuento</option>
                        <option value="percentage" @selected(old('global_discount_type', $estimate->global_discount_type ?? '') === 'percentage')>Porcentaje (%)</option>
                        <option value="fixed" @selected(old('global_discount_type', $estimate->global_discount_type ?? '') === 'fixed')>Monto fijo</option>
                    </select>
                </div>
                <div>
                    <label for="global_discount_value" class="block text-sm font-medium text-gray-700">Dscto global — Valor</label>
                    <input type="number" id="global_discount_value" name="global_discount_value" step="0.01" min="0" value="{{ old('global_discount_value', $estimate->global_discount_value ?? 0) }}" class="{{ $inputCls }}">
                </div>
            </div>
        </div>

        {{-- Tarifa hora hombre y paño --}}
        <div class="sm:col-span-2 xl:col-span-1">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="hourly_rate" class="block text-sm font-medium text-gray-700">Tarifa hora hombre</label>
                    <input type="number" id="hourly_rate" name="hourly_rate" step="0.01" min="0" value="{{ old('hourly_rate', $estimate->hourly_rate ?? '') }}" class="{{ $inputCls }}">
                </div>
                <div>
                    <label for="panel_rate" class="block text-sm font-medium text-gray-700">Tarifa paño de pintura</label>
                    <input type="number" id="panel_rate" name="panel_rate" step="0.01" min="0" value="{{ old('panel_rate', $estimate->panel_rate ?? '') }}" class="{{ $inputCls }}">
                </div>
            </div>
        </div>

        {{-- Observaciones (ancho completo) --}}
        <div class="sm:col-span-2 xl:col-span-3">
            <label for="comments" class="block text-sm font-medium text-gray-700">Observaciones</label>
            <textarea id="comments" name="comments" rows="3" class="{{ $inputCls }}">{{ old('comments', $estimate->comments ?? '') }}</textarea>
        </div>
    </div>
</div>

{{-- ===== Ítems ===== --}}
<div class="mt-6">
    <div class="flex items-center justify-between mb-3">
        <div>
            <h3 class="text-lg font-semibold text-gray-800">Ítems del presupuesto</h3>
            <p class="text-sm text-gray-500 mt-0.5">{{ $priceLabel }}</p>
        </div>
        <button type="button" id="btn-add-item" class="btn btn-primary">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
            Agregar ítem
        </button>
    </div>

    <div class="card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm" id="items-table">
                <thead>
                    <tr class="bg-gray-50">
                        <th class="px-3 py-2 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Tipo</th>
                        <th class="px-3 py-2 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Descripción</th>
                        <th class="px-3 py-2 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Categoría</th>
                        <th class="px-3 py-2 text-right text-xs font-semibold uppercase tracking-wider text-gray-500">Cant.</th>
                        <th class="px-3 py-2 text-right text-xs font-semibold uppercase tracking-wider text-gray-500">P. Unitario</th>
                        <th class="px-3 py-2 text-right text-xs font-semibold uppercase tracking-wider text-gray-500">Dto. %</th>
                        <th class="px-3 py-2 text-right text-xs font-semibold uppercase tracking-wider text-gray-500">Total</th>
                        <th class="px-3 py-2 text-center text-xs font-semibold uppercase tracking-wider text-gray-500">Origen</th>
                        <th class="px-3 py-2"></th>
                    </tr>
                </thead>
                <tbody id="items-body" class="divide-y divide-gray-100">
                    {{-- Las filas existentes se renderizan desde el estado JS en _form-scripts --}}
                </tbody>
            </table>
        </div>
    </div>

    {{-- Resumen de cálculos en card (desglose SUNAT) --}}
    <div class="mt-4 grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="md:col-span-2"></div>
        <div class="card">
            <div class="p-5 space-y-2 text-sm">
                <div class="flex justify-between"><span class="text-gray-500">Valor Bruto</span><span id="total-subtotal" class="font-medium">0.00</span></div>
                <div class="flex justify-between"><span class="text-gray-500">Descuentos por ítem</span><span id="total-lines-discount" class="font-medium text-red-600">- 0.00</span></div>
                <div class="flex justify-between"><span class="text-gray-500">Descuento global</span><span id="total-global-discount" class="font-medium text-red-600">- 0.00</span></div>
                <div class="flex justify-between"><span class="text-gray-500">Órdenes de compra (para franquicia)</span><span id="total-orders" class="font-medium text-gray-600">+ 0.00</span></div>
                <div class="flex justify-between border-t border-gray-100 pt-2"><span class="font-medium text-gray-700">Valor Venta (Base Imponible)</span><span id="total-taxable" class="font-medium">0.00</span></div>
                <div class="flex justify-between"><span class="text-gray-500">IGV (<span id="igv-rate-label">0</span>%)</span><span id="total-iva" class="font-medium">0.00</span></div>
                <div class="flex justify-between border-t border-gray-200 pt-2 text-base"><span class="font-semibold">Total a Pagar</span><span id="total-total" class="font-semibold text-gray-900">0.00</span></div>
            </div>
        </div>
    </div>
</div>

{{-- Órdenes de compra de terceros --}}
<div class="mt-6">
    <div class="flex items-center justify-between mb-3">
        <div>
            <h3 class="text-lg font-semibold text-gray-800">Órdenes de compra de terceros</h3>
            <p class="text-sm text-gray-500 mt-0.5">Se suman a la base para el cálculo de la franquicia (no afectan el total).</p>
        </div>
        <button type="button" id="btn-add-third-party-order" class="btn btn-primary">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
            Agregar orden
        </button>
    </div>

    <div class="card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm" id="third-party-orders-table">
                <thead>
                    <tr class="bg-gray-50">
                        <th class="px-3 py-2 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Descripción</th>
                        <th class="px-3 py-2 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Proveedor</th>
                        <th class="px-3 py-2 text-right text-xs font-semibold uppercase tracking-wider text-gray-500">Monto sin IGV</th>
                        <th class="px-3 py-2"></th>
                    </tr>
                </thead>
                <tbody id="third-party-orders-body" class="divide-y divide-gray-100">
                    {{-- Las OC existentes se renderizan desde el estado JS en _form-scripts --}}
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Franquicia --}}
<div class="mt-6">
    <div class="card p-6">
        <div>
            <h3 class="text-lg font-semibold text-gray-800">Franquicia</h3>
            <p class="text-sm text-gray-500 mt-0.5">Informativa: no descuenta del total del presupuesto.</p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mt-4">
            <div>
                <label for="franchise_minimum_amount" class="block text-sm font-medium text-gray-700">Monto mínimo</label>
                <input type="number" id="franchise_minimum_amount" name="franchise_minimum_amount" step="0.01" min="0" value="{{ old('franchise_minimum_amount', $estimate->franchise_minimum_amount ?? '') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-right">
            </div>
            <div>
                <label for="franchise_percentage" class="block text-sm font-medium text-gray-700">% Franquicia</label>
                <input type="number" id="franchise_percentage" name="franchise_percentage" step="0.01" min="0" max="100" value="{{ old('franchise_percentage', $estimate->franchise_percentage ?? '') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-right">
            </div>
            <div class="flex items-end">
                <label class="inline-flex items-center gap-2 text-sm font-medium text-gray-700">
                    <input type="checkbox" id="franchise_minimum_includes_tax" name="franchise_minimum_includes_tax" value="1" @checked(old('franchise_minimum_includes_tax', $estimate->franchise_minimum_includes_tax ?? false)) class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                    El monto mínimo incluye IGV
                </label>
            </div>
        </div>

        {{-- Desglose de franquicia (calculado en vivo) --}}
        <div class="mt-4 grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="md:col-span-2"></div>
            <div class="card">
                <div class="p-5 space-y-2 text-sm">
                    <div class="flex justify-between"><span class="text-gray-500">Monto mínimo</span><span id="franchise-minimum-amount" class="font-medium">0.00</span></div>
                    <div class="flex justify-between"><span class="text-gray-500">Mínimo sin IGV</span><span id="franchise-minimum-without-tax" class="font-medium">0.00</span></div>
                    <div class="flex justify-between"><span class="text-gray-500">Base (Base Imponible + OC)</span><span id="franchise-base" class="font-medium">0.00</span></div>
                    <div class="flex justify-between"><span class="text-gray-500">% Aplicado</span><span id="franchise-percentage-applied" class="font-medium">0.00</span></div>
                    <div class="flex justify-between border-t border-gray-200 pt-2 text-base"><span class="font-semibold">Franquicia a pagar (sin IGV)</span><span id="franchise-amount" class="font-semibold text-gray-900">0.00</span></div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Modales --}}
@include('estimates._item-modal')
@include('estimates._third-party-order-modal')
