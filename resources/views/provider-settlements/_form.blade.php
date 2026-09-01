@php $settlement = $settlement ?? new \App\Models\ProviderSettlement(); @endphp

<div class="card">
    <div class="p-6">
        <form method="POST" action="{{ $action }}" class="space-y-6">
            @csrf
            @method($method ?? 'POST')

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Proveedor <span class="text-red-500">*</span></label>
                    <select id="provider_id" name="provider_id" placeholder="Buscar proveedor (RUC o nombre)..."></select>
                    @error('provider_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Período inicio <span class="text-red-500">*</span></label>
                    <input type="date" name="period_start" id="period_start" value="{{ old('period_start', $settlement->period_start?->format('Y-m-d')) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    @error('period_start') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Período fin <span class="text-red-500">*</span></label>
                    <input type="date" name="period_end" id="period_end" value="{{ old('period_end', $settlement->period_end?->format('Y-m-d')) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    @error('period_end') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
            </div>

            <div>
                <h3 class="text-sm font-semibold text-gray-700 mb-2">Comprobantes disponibles (completados, sin liquidar)</h3>
                <div id="available-vouchers" class="rounded-lg border border-gray-200 overflow-hidden">
                    <p class="p-4 text-sm text-gray-400">Selecciona un proveedor y un período para cargar los comprobantes.</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Descuento global (sin IGV)</label>
                    <input type="number" step="0.01" min="0" name="global_discount" id="global_discount" value="{{ old('global_discount', $settlement->global_discount) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    @error('global_discount') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
                <div class="md:col-span-1">
                    <label class="block text-sm font-medium text-gray-700">Motivo del descuento</label>
                    <input type="text" name="discount_reason" value="{{ old('discount_reason', $settlement->discount_reason) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Tasa IGV (%)</label>
                    <input type="number" step="0.0001" min="0" max="1" name="igv_rate" id="igv_rate" value="{{ old('igv_rate', $settlement->igv_rate ?? $igvRate) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Tasa de detracción (%)</label>
                    <input type="number" step="0.0001" min="0" max="1" name="detraction_rate" id="detraction_rate" value="{{ old('detraction_rate', $settlement->detraction_rate ?? $detractionRate) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>
            </div>

            <div class="rounded-lg bg-gray-50 border border-gray-200 p-4">
                <h3 class="text-sm font-semibold text-gray-700 mb-2">Resumen de la liquidación</h3>
                <dl class="grid grid-cols-2 md:grid-cols-3 gap-3 text-sm">
                    <div><dt class="text-gray-500">Subtotal (sin IGV)</dt><dd id="preview-subtotal" class="font-medium">S/ 0.00</dd></div>
                    <div><dt class="text-gray-500">Base (sin IGV)</dt><dd id="preview-base" class="font-medium">S/ 0.00</dd></div>
                    <div><dt class="text-gray-500">IGV</dt><dd id="preview-igv" class="font-medium">S/ 0.00</dd></div>
                    <div><dt class="text-gray-500">Total con IGV</dt><dd id="preview-total" class="font-medium">S/ 0.00</dd></div>
                    <div><dt class="text-gray-500">Detracción</dt><dd id="preview-detraction" class="font-medium text-amber-700">S/ 0.00</dd></div>
                    <div><dt class="text-gray-500">Total a pagar</dt><dd id="preview-payable" class="font-semibold text-blue-700">S/ 0.00</dd></div>
                </dl>
            </div>

            <div class="flex justify-end gap-3">
                <a href="{{ route('provider-settlements.index') }}" class="btn btn-secondary">Cancelar</a>
                <button type="submit" class="btn btn-primary" data-loading-text="Guardando...">{{ $submitLabel }}</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    const r2 = (n) => Math.round((n + Number.EPSILON) * 100) / 100;
    const fmt = (n) => 'S/ ' + r2(n).toFixed(2);
    const presetVoucherIds = @json($settlement->exists ? $settlement->vouchers->pluck('id') : []);

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

    @if ($settlement->exists)
        providerSelect.addOption({ id: {{ $settlement->provider_id }}, display_name: @json($settlement->provider?->display_name), document_number: @json($settlement->provider?->document_number) });
        providerSelect.addItem({{ $settlement->provider_id }});
    @endif

    function loadVouchers() {
        const providerId = providerSelect.getValue();
        const from = document.getElementById('period_start').value;
        const to = document.getElementById('period_end').value;
        const box = document.getElementById('available-vouchers');
        if (!providerId) {
            box.innerHTML = '<p class="p-4 text-sm text-gray-400">Selecciona un proveedor para cargar los comprobantes.</p>';
            updatePreview();
            return;
        }
        box.innerHTML = '<p class="p-4 text-sm text-gray-400">Cargando...</p>';
        let url = "{{ route('api.provider-settlements.available-vouchers') }}?provider_id=" + providerId + '&from=' + from + '&to=' + to;
        @if ($settlement->exists)
        url += '&settlement_id={{ $settlement->id }}';
        @endif
        fetch(url)
            .then(r => r.json())
            .then(items => renderVouchers(items))
            .catch(() => { box.innerHTML = '<p class="p-4 text-sm text-red-600">Error al cargar los comprobantes.</p>'; });
    }
    function renderVouchers(items) {
        const box = document.getElementById('available-vouchers');
        if (!items.length) {
            box.innerHTML = '<p class="p-4 text-sm text-gray-400">No hay comprobantes completados sin liquidar en el período.</p>';
            updatePreview();
            return;
        }
        let html = '<table class="w-full text-sm"><thead class="bg-gray-50 text-xs uppercase tracking-wider text-gray-500">'
            + '<tr><th class="px-3 py-2 text-left">Sel.</th><th class="px-3 py-2 text-left">Documento</th><th class="px-3 py-2 text-left">Fecha</th>'
            + '<th class="px-3 py-2 text-left">Placa</th><th class="px-3 py-2 text-left">Descripción</th><th class="px-3 py-2 text-right">Base (sin IGV)</th></tr></thead><tbody>';
        items.forEach(v => {
            const checked = presetVoucherIds.includes(v.id) ? ' checked' : '';
            const vSym = v.currency === 'USD' ? 'US$' : 'S/';
            html += `<tr class="border-t border-gray-100">
                <td class="px-3 py-2"><input type="checkbox" name="voucher_ids[]" value="${v.id}" data-base-pen="${v.base_amount_pen}"${checked} class="voucher-check rounded border-gray-300 text-blue-600 focus:ring-blue-500"></td>
                <td class="px-3 py-2 font-mono text-xs">${v.document_sn}</td>
                <td class="px-3 py-2">${v.execution_date}</td>
                <td class="px-3 py-2">${v.plate || ''}</td>
                <td class="px-3 py-2 text-gray-600">${v.description || ''}</td>
                <td class="px-3 py-2 text-right">${vSym} ${Number(v.base_amount || 0).toFixed(2)}<div class="text-xs text-gray-400">S/ ${Number(v.base_amount_pen || 0).toFixed(2)}</div></td>
            </tr>`;
        });
        html += '</tbody></table>';
        box.innerHTML = html;
        box.querySelectorAll('.voucher-check').forEach(cb => cb.addEventListener('change', updatePreview));
        updatePreview();
    }

    function updatePreview() {
        const checks = Array.from(document.querySelectorAll('.voucher-check:checked'));
        const subtotal = r2(checks.reduce((s, cb) => s + (parseFloat(cb.dataset.basePen) || 0), 0));
        const discount = parseFloat(document.getElementById('global_discount').value) || 0;
        const base = r2(subtotal - discount);
        const igvRate = parseFloat(document.getElementById('igv_rate').value) || 0;
        const detractionRate = parseFloat(document.getElementById('detraction_rate').value) || 0;
        const igv = r2(base * igvRate);
        const total = r2(base + igv);
        const detraction = r2(total * detractionRate);
        const payable = r2(total - detraction);
        document.getElementById('preview-subtotal').textContent = fmt(subtotal);
        document.getElementById('preview-base').textContent = fmt(base);
        document.getElementById('preview-igv').textContent = fmt(igv);
        document.getElementById('preview-total').textContent = fmt(total);
        document.getElementById('preview-detraction').textContent = fmt(detraction);
        document.getElementById('preview-payable').textContent = fmt(payable);
    }

    providerSelect.on('change', loadVouchers);
    ['period_start', 'period_end'].forEach(id => document.getElementById(id).addEventListener('change', loadVouchers));
    ['global_discount', 'igv_rate', 'detraction_rate'].forEach(id => document.getElementById(id).addEventListener('input', updatePreview));
    if (providerSelect.getValue()) loadVouchers();
</script>
@endpush

