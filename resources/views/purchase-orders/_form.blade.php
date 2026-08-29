@php
    $po = $po ?? null;
    $poItemsData = $po?->items->map(fn ($i) => [
        'id' => $i->id,
        'part_id' => $i->part_id,
        'part_name' => $i->part?->name,
        'sku' => $i->part?->sku,
        'quantity' => $i->quantity,
        'unit_cost' => $i->unit_cost,
        'uom' => $i->uom ?? $i->part?->uom,
    ]) ?? [];
@endphp

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-4">
    <div>
        <label class="block text-sm font-medium text-gray-700">Proveedor <span class="text-red-500">*</span></label>
        <select id="provider_id" name="provider_id" placeholder="Buscar proveedor..."></select>
        @error('provider_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700">Almacén destino</label>
        <select name="warehouse_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
            <option value="">—</option>
            @foreach ($warehouses as $w)
                <option value="{{ $w->id }}" @selected(old('warehouse_id', $po->warehouse_id ?? '') == $w->id)>{{ $w->name }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700">Moneda <span class="text-red-500">*</span></label>
        <select name="currency" id="po-currency" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
            <option value="PEN" @selected(old('currency', $po->currency ?? 'PEN') === 'PEN')>Soles (PEN)</option>
            <option value="USD" @selected(old('currency', $po->currency ?? 'PEN') === 'USD')>Dólares (USD)</option>
        </select>
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700">Fecha de pedido</label>
        <input type="date" name="order_date" value="{{ old('order_date', $po && $po->order_date ? $po->order_date->format('Y-m-d') : '') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700">Entrega estimada</label>
        <input type="date" name="expected_delivery" value="{{ old('expected_delivery', $po && $po->expected_delivery ? $po->expected_delivery->format('Y-m-d') : '') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
        @error('expected_delivery') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>
    <div id="po-exchange-wrap" class="{{ old('currency', $po->currency ?? 'PEN') === 'USD' ? '' : 'hidden' }}">
        <label class="block text-sm font-medium text-gray-700">Tipo de cambio <span class="text-red-500">*</span></label>
        <input type="number" step="0.0001" name="exchange_rate" value="{{ old('exchange_rate', $po->exchange_rate ?? '') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
    </div>
    <div class="lg:col-span-3">
        <label class="block text-sm font-medium text-gray-700">Notas</label>
        <textarea name="notes" rows="2" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('notes', $po->notes ?? '') }}</textarea>
    </div>
</div>

{{-- Ítems --}}
<div class="card p-4 sm:p-5 mb-4">
    <div class="flex items-center justify-between mb-3">
        <h3 class="text-sm font-semibold text-gray-800">Repuestos a comprar</h3>
        <button type="button" id="btn-add-line" class="btn btn-secondary">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
            Agregar línea
        </button>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="text-xs font-semibold uppercase tracking-wider text-gray-500 border-b border-gray-200">
                    <th class="py-2 pr-2 text-left min-w-[220px]">Repuesto <span class="text-red-500">*</span></th>
                    <th class="py-2 pr-2 text-left w-24">Cantidad</th>
                    <th class="py-2 pr-2 text-left w-32">Costo unit.</th>
                    <th class="py-2 pr-2 text-left w-20">U/M</th>
                    <th class="py-2 w-10"></th>
                </tr>
            </thead>
            <tbody id="po-items-tbody"></tbody>
        </table>
    </div>
    <p id="po-total" class="mt-3 text-right text-sm font-semibold text-gray-800">Total: S/ 0.00</p>
</div>

@push('scripts')
<script>

    const poItems = @json($poItemsData);

    let lineIndex = 0;
    const tbody = document.getElementById('po-items-tbody');

    function calcTotal() {
        let total = 0;
        tbody.querySelectorAll('tr').forEach(tr => {
            const qty = parseFloat(tr.querySelector('.po-qty')?.value || 0);
            const cost = parseFloat(tr.querySelector('.po-cost')?.value || 0);
            total += qty * cost;
        });
        document.getElementById('po-total').textContent = 'Total: S/ ' + total.toFixed(2);
    }

    function initPartSelect(tr, item) {
        const sel = tr.querySelector('.po-part-select');
        const ts = new TomSelect(sel, {
            valueField: 'id',
            labelField: 'name',
            searchField: ['name', 'sku', 'barcode'],
            maxItems: 1,
            closeAfterSelect: true,
            create: false,
            copyClassesToDropdown: false,
            dropdownParent: 'body',
            load: function (query, callback) {
                fetch(`{{ route('api.parts.search') }}?q=${encodeURIComponent(query)}`)
                    .then(r => r.json()).then(callback).catch(() => callback());
            },
            onChange: function (v) {
                if (!v) return;
                const opt = this.options[v];
                const uom = tr.querySelector('.po-uom');
                if (opt && opt.uom) uom.value = opt.uom;
            }
        });
        if (item && item.part_id) {
            ts.addOption({ id: item.part_id, name: item.part_name, sku: item.sku, uom: item.uom });
            ts.addItem(item.part_id);
        }
    }

    function addLine(item = {}) {
        const idx = lineIndex++;
        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td class="py-2 pr-2">
                <select name="items[${idx}][part_id]" class="po-part-select w-full" placeholder="Buscar repuesto..."></select>
            </td>
            <td class="py-2 pr-2">
                <input type="number" step="0.01" min="0" name="items[${idx}][quantity]" value="${item.quantity || 1}" class="po-qty w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-right">
            </td>
            <td class="py-2 pr-2">
                <input type="number" step="0.01" min="0" name="items[${idx}][unit_cost]" value="${item.unit_cost || 0}" class="po-cost w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-right">
            </td>
            <td class="py-2 pr-2">
                <input type="text" name="items[${idx}][uom]" value="${item.uom || 'NIU'}" readonly class="po-uom w-full rounded-md border-gray-100 bg-gray-50 text-gray-500 text-center">
            </td>
            <td class="py-2 text-center">
                <button type="button" class="po-remove text-red-500 hover:text-red-700" title="Quitar línea">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </td>
        `;
        tbody.appendChild(tr);
        initPartSelect(tr, item);
        tr.querySelector('.po-remove').addEventListener('click', () => { tr.remove(); calcTotal(); });
        tr.querySelector('.po-qty').addEventListener('input', calcTotal);
        tr.querySelector('.po-cost').addEventListener('input', calcTotal);
        calcTotal();
    }

    document.getElementById('btn-add-line').addEventListener('click', () => addLine());

    // Proveedor (Tom Select)
    if (window.TomSelect) {
        const providerTs = new TomSelect('#provider_id', {
            valueField: 'id',
            labelField: 'display_name',
            searchField: ['display_name', 'document_number', 'name'],
            maxItems: 1,
            closeAfterSelect: true,
            create: false,
            copyClassesToDropdown: false,
            dropdownParent: 'body',
            load: function (query, callback) {
                fetch(`{{ route('api.parties.suppliers') }}?q=${encodeURIComponent(query)}`)
                    .then(r => r.json()).then(callback).catch(() => callback());
            }
        });
        @if ($po && $po->provider)
            providerTs.addOption({ id: {{ $po->provider_id }}, display_name: @json($po->provider->display_name) });
            providerTs.addItem('{{ $po->provider_id }}');
        @endif

        // Moneda → tipo de cambio
        const currencySel = document.getElementById('po-currency');
        const exchangeWrap = document.getElementById('po-exchange-wrap');
        currencySel.addEventListener('change', () => exchangeWrap.classList.toggle('hidden', currencySel.value !== 'USD'));
    }

    (poItems.length ? poItems : [{}]).forEach(addLine);
</script>
@endpush
