@php $voucher = $voucher ?? new \App\Models\ServiceVoucher(); @endphp

<div class="card">
    <div class="p-6">
        <form method="POST" action="{{ $action }}" class="space-y-6">
            @csrf
            @method($method ?? 'POST')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Proveedor <span class="text-red-500">*</span></label>
                    <select id="provider_id" name="provider_id" placeholder="Buscar proveedor (RUC o nombre)..."></select>
                    @error('provider_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Orden de Trabajo <span class="text-red-500">*</span></label>
                    <select id="work_order_id" name="work_order_id" placeholder="Buscar OT (serie o placa)..."></select>
                    @error('work_order_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Fecha de ejecución <span class="text-red-500">*</span></label>
                    <input type="date" name="execution_date" value="{{ old('execution_date', $voucher->execution_date?->format('Y-m-d') ?? now()->format('Y-m-d')) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    @error('execution_date') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Monto acordado (sin IGV) <span class="text-red-500">*</span></label>
                    <input type="number" step="0.01" min="0" name="agreed_amount" id="agreed_amount" value="{{ old('agreed_amount', $voucher->agreed_amount) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
                    @error('agreed_amount') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Descuento (sin IGV)</label>
                    <input type="number" step="0.01" min="0" name="discount_applied" id="discount_applied" value="{{ old('discount_applied', $voucher->discount_applied) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Moneda <span class="text-red-500">*</span></label>
                    <select name="currency" id="currency" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="PEN" {{ old('currency', $voucher->currency ?? 'PEN') === 'PEN' ? 'selected' : '' }}>Soles (PEN)</option>
                        <option value="USD" {{ old('currency', $voucher->currency ?? 'PEN') === 'USD' ? 'selected' : '' }}>Dólares (USD)</option>
                    </select>
                </div>
                <div id="exchange-rate-field">
                    <label class="block text-sm font-medium text-gray-700">Tipo de cambio (S/ por US$)</label>
                    <input type="number" step="0.0001" min="0" name="exchange_rate" id="exchange_rate" value="{{ old('exchange_rate', $voucher->exchange_rate ?? 1) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <p class="mt-1 text-xs text-gray-500">Snapshot: soles por 1 dólar (PEN = 1). Se usa para normalizar el costo a soles.</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Tasa IGV (%)</label>
                    <input type="number" step="0.0001" min="0" max="1" name="igv_rate" id="igv_rate" value="{{ old('igv_rate', $voucher->igv_rate ?? $igvRate) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Tasa de detracción (%)</label>
                    <input type="number" step="0.0001" min="0" max="1" name="detraction_rate" id="detraction_rate" value="{{ old('detraction_rate', $voucher->detraction_rate ?? $detractionRate) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>
                <div class="md:col-span-2">
                    <label class="inline-flex items-center gap-2 text-sm text-gray-700">
                        <input type="checkbox" id="no-taxes" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        Pago directo sin IGV ni detracción (maestro pintor / planchador, sin comprobante)
                    </label>
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700">Descripción del servicio <span class="text-red-500">*</span></label>
                    <textarea name="description" rows="2" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" required>{{ old('description', $voucher->description) }}</textarea>
                    @error('description') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="rounded-lg bg-gray-50 border border-gray-200 p-4">
                <h3 class="text-sm font-semibold text-gray-700 mb-2">Resumen del comprobante</h3>
                <dl class="grid grid-cols-2 md:grid-cols-3 gap-3 text-sm">
                    <div><dt class="text-gray-500">Base (sin IGV)</dt><dd id="preview-base" class="font-medium">S/ 0.00</dd></div>
                    <div><dt class="text-gray-500">IGV</dt><dd id="preview-igv" class="font-medium">S/ 0.00</dd></div>
                    <div><dt class="text-gray-500">Total con IGV</dt><dd id="preview-total" class="font-medium">S/ 0.00</dd></div>
                    <div><dt class="text-gray-500">Detracción</dt><dd id="preview-detraction" class="font-medium text-amber-700">S/ 0.00</dd></div>
                    <div><dt class="text-gray-500">Total a pagar</dt><dd id="preview-payable" class="font-semibold text-blue-700">S/ 0.00</dd></div>
                </dl>
            </div>

            <div class="flex justify-end gap-3">
                <a href="{{ route('service-vouchers.index') }}" class="btn btn-secondary">Cancelar</a>
                <button type="submit" class="btn btn-primary" data-loading-text="Guardando...">{{ $submitLabel }}</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    const r2 = (n) => Math.round((n + Number.EPSILON) * 100) / 100;
    const currencySymbol = () => document.getElementById('currency').value === 'USD' ? 'US$ ' : 'S/ ';
    const fmt = (n) => currencySymbol() + r2(n).toFixed(2);

    const providerSelect = new TomSelect('#provider_id', {
        valueField: 'id', labelField: 'display_name', searchField: ['display_name', 'document_number'],
        maxItems: 1, create: false, closeAfterSelect: true, copyClassesToDropdown: false, dropdownParent: 'body',
        load: function (query, callback) {
            fetch("{{ route('api.parties.suppliers') }}?q=" + encodeURIComponent(query))
                .then(r => r.json()).then(d => callback(Array.isArray(d) ? d : [])).catch(() => callback());
        },
        onItemAdd: function () { this.blur(); this.close(); },
        onDropdownOpen: function () {
            if (this.items.length) { this.setTextValue(''); const i = this.input; if (i.setSelectionRange) i.setSelectionRange(0, 0); }
        },
        render: {
            option: (item, escape) => `<div class="flex justify-between gap-2"><span>${escape(item.display_name)}</span><span class="text-xs text-gray-400">${escape(item.document_number || '')}</span></div>`,
            item: (item, escape) => `<div>${escape(item.display_name)}</div>`
        }
    });

    const workOrderSelect = new TomSelect('#work_order_id', {
        valueField: 'id', labelField: 'document_sn', searchField: ['document_sn', 'plate'],
        maxItems: 1, create: false, closeAfterSelect: true, copyClassesToDropdown: false, dropdownParent: 'body',
        load: function (query, callback) {
            fetch("{{ route('api.work-orders.search') }}?q=" + encodeURIComponent(query) + '&limit=20')
                .then(r => r.json()).then(d => callback(Array.isArray(d) ? d : [])).catch(() => callback());
        },
        onItemAdd: function () { this.blur(); this.close(); },
        onDropdownOpen: function () {
            if (this.items.length) { this.setTextValue(''); const i = this.input; if (i.setSelectionRange) i.setSelectionRange(0, 0); }
        },
        render: {
            option: (item, escape) => `<div class="flex justify-between gap-2"><span>${escape(item.document_sn)}</span><span class="text-xs text-gray-400">${escape((item.vehicle && item.vehicle.plate) || '')}</span></div>`,
            item: (item, escape) => `<div>${escape(item.document_sn)} ${escape((item.vehicle && item.vehicle.plate) || '')}</div>`
        }
    });
    @if ($voucher->exists)
        providerSelect.addOption({ id: {{ $voucher->provider_id }}, display_name: @json($voucher->provider?->display_name), document_number: @json($voucher->provider?->document_number) });
        providerSelect.addItem({{ $voucher->provider_id }});
        workOrderSelect.addOption({ id: {{ $voucher->work_order_id }}, document_sn: @json($voucher->workOrder?->document_sn), vehicle: { plate: @json($voucher->workOrder?->vehicle?->plate) } });
        workOrderSelect.addItem({{ $voucher->work_order_id }});
    @elseif (!empty($preselectedWorkOrder))
        workOrderSelect.addOption({ id: {{ $preselectedWorkOrder->id }}, document_sn: @json($preselectedWorkOrder->document_sn), vehicle: { plate: @json($preselectedWorkOrder->vehicle?->plate) } });
        workOrderSelect.addItem({{ $preselectedWorkOrder->id }});
    @endif

    function updatePreview() {
        const agreed = parseFloat(document.getElementById('agreed_amount').value) || 0;
        const discount = parseFloat(document.getElementById('discount_applied').value) || 0;
        const igvRate = parseFloat(document.getElementById('igv_rate').value) || 0;
        const detractionRate = parseFloat(document.getElementById('detraction_rate').value) || 0;
        const base = r2(agreed - discount);
        const igv = r2(base * igvRate);
        const total = r2(base + igv);
        const detraction = r2(total * detractionRate);
        const payable = r2(total - detraction);
        document.getElementById('preview-base').textContent = fmt(base);
        document.getElementById('preview-igv').textContent = fmt(igv);
        document.getElementById('preview-total').textContent = fmt(total);
        document.getElementById('preview-detraction').textContent = fmt(detraction);
        document.getElementById('preview-payable').textContent = fmt(payable);
    }

    // Pago directo sin IGV ni detracción (maestro pintor / planchador).
    const noTaxes = document.getElementById('no-taxes');
    const igvInput = document.getElementById('igv_rate');
    const detractionInput = document.getElementById('detraction_rate');
    noTaxes.addEventListener('change', function () {
        if (this.checked) {
            this.dataset.prevIgv = igvInput.value;
            this.dataset.prevDetraction = detractionInput.value;
            igvInput.value = 0;
            detractionInput.value = 0;
            igvInput.disabled = true;
            detractionInput.disabled = true;
        } else {
            if (this.dataset.prevIgv !== undefined) igvInput.value = this.dataset.prevIgv;
            if (this.dataset.prevDetraction !== undefined) detractionInput.value = this.dataset.prevDetraction;
            igvInput.disabled = false;
            detractionInput.disabled = false;
        }
        updatePreview();
    });

    // Moneda: el tipo de cambio es snapshot solo cuando el vale es en USD.
    const currencySelect = document.getElementById('currency');
    const exchangeInput = document.getElementById('exchange_rate');
    function syncCurrency() {
        const isUsd = currencySelect.value === 'USD';
        document.getElementById('exchange-rate-field').classList.toggle('opacity-50', !isUsd);
        exchangeInput.disabled = !isUsd;
        if (!isUsd) exchangeInput.value = 1;
        updatePreview();
    }
    currencySelect.addEventListener('change', syncCurrency);
    syncCurrency();

    ['agreed_amount', 'discount_applied', 'igv_rate', 'detraction_rate'].forEach(id => {
        document.getElementById(id).addEventListener('input', updatePreview);
    });
    updatePreview();
</script>
@endpush

