<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Nueva Guía de Inventario') }}</h2>
            <a href="{{ route('inventory-guides.index') }}" class="btn btn-secondary">Volver</a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            @if ($errors->any())
                <div class="mb-4 px-4 py-3 bg-red-50 border border-red-200 text-red-700 rounded-lg text-sm">
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('inventory-guides.store') }}" class="card p-6">
                @csrf

                {{-- Tipo --}}
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Tipo de guía <span class="text-red-500">*</span></label>
                        <select id="guide_type" name="guide_type" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="input" @selected(old('guide_type', 'input') === 'input')>NIA1 · Ingreso a almacén</option>
                            <option value="output" @selected(old('guide_type') === 'output')>NSA1 · Salida de almacén</option>
                            <option value="transfer" @selected(old('guide_type') === 'transfer')>NTA1 · Transferencia entre almacenes</option>
                            <option value="adjustment" @selected(old('guide_type') === 'adjustment')>Ajuste de stock (motivo 28)</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Fecha de movimiento</label>
                        <input type="date" name="movement_date" value="{{ old('movement_date', now()->toDateString()) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>
                </div>

                {{-- Motivo --}}
                <div id="reason-input-wrap" class="mb-4">
                    <label class="block text-sm font-medium text-gray-700">Motivo (ingreso) <span class="text-red-500">*</span></label>
                    <select name="movement_reason_code" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">— Seleccione —</option>
                        @foreach ($inputReasons as $r)
                            <option value="{{ $r->code }}" @selected(old('movement_reason_code') === $r->code)>{{ $r->code }} · {{ $r->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div id="reason-output-wrap" class="hidden mb-4">
                    <label class="block text-sm font-medium text-gray-700">Motivo (salida) <span class="text-red-500">*</span></label>
                    <select name="movement_reason_code" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">— Seleccione —</option>
                        @foreach ($outputReasons as $r)
                            <option value="{{ $r->code }}" @selected(old('movement_reason_code') === $r->code)>{{ $r->code }} · {{ $r->name }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Almacenes --}}
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                    <div id="warehouse-origin-wrap" class="hidden">
                        <label class="block text-sm font-medium text-gray-700">Almacén de origen <span class="text-red-500">*</span></label>
                        <select name="origin_warehouse_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="">—</option>
                            @foreach ($warehouses as $w)
                                <option value="{{ $w->id }}" @selected(old('origin_warehouse_id') == $w->id)>{{ $w->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div id="warehouse-destination-wrap" class="hidden">
                        <label class="block text-sm font-medium text-gray-700">Almacén de destino <span class="text-red-500">*</span></label>
                        <select name="destination_warehouse_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="">—</option>
                            @foreach ($warehouses as $w)
                                <option value="{{ $w->id }}" @selected(old('destination_warehouse_id') == $w->id)>{{ $w->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div id="warehouse-adjustment-wrap" class="hidden">
                        <label class="block text-sm font-medium text-gray-700">Almacén <span class="text-red-500">*</span></label>
                        <select name="warehouse_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="">—</option>
                            @foreach ($warehouses as $w)
                                <option value="{{ $w->id }}" @selected(old('warehouse_id') == $w->id)>{{ $w->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div id="provider-wrap" class="hidden">
                        <label class="block text-sm font-medium text-gray-700">Proveedor</label>
                        <select id="provider_id" name="provider_id" placeholder="Buscar proveedor..."></select>
                    </div>
                    <div id="invoice-wrap" class="hidden">
                        <label class="block text-sm font-medium text-gray-700">Factura / Guía del proveedor</label>
                        <div class="grid grid-cols-2 gap-2 mt-1">
                            <input type="text" name="provider_invoice" placeholder="Factura" value="{{ old('provider_invoice') }}" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" maxlength="30">
                            <input type="text" name="provider_guide" placeholder="Guía" value="{{ old('provider_guide') }}" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" maxlength="30">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Notas</label>
                        <input type="text" name="notes" value="{{ old('notes') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" maxlength="255">
                    </div>
                </div>

                {{-- Ítems --}}
                <div class="card p-4 sm:p-5 mb-4">
                    <div class="flex items-center justify-between mb-3">
                        <h3 class="text-sm font-semibold text-gray-800">Repuestos</h3>
                        <button type="button" id="btn-add-line" class="btn btn-secondary">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                            Agregar línea
                        </button>
                    </div>
                    <p id="adjustment-hint" class="hidden text-xs text-amber-600 mb-2">En ajustes, use cantidad positiva (entrada) o negativa (salida).</p>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="text-xs font-semibold uppercase tracking-wider text-gray-500 border-b border-gray-200">
                                    <th class="py-2 pr-2 text-left min-w-[220px]">Repuesto <span class="text-red-500">*</span></th>
                                    <th class="py-2 pr-2 text-left w-28">Cantidad</th>
                                    <th class="py-2 pr-2 text-left w-32" id="th-unit-cost">Costo unit.</th>
                                    <th class="py-2 w-10"></th>
                                </tr>
                            </thead>
                            <tbody id="guide-items-tbody"></tbody>
                        </table>
                    </div>
                </div>

                <div class="mt-6 flex justify-end">
                    <button type="submit" class="btn btn-primary">Emitir guía</button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
    <script>
        const tbody = document.getElementById('guide-items-tbody');
    let lineIndex = 0;
    let providerTs = null;

    function initPartSelect(tr) {
        const sel = tr.querySelector('.g-part-select');
        new TomSelect(sel, {
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
            }
        });
    }

    function addLine() {
        const idx = lineIndex++;
        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td class="py-2 pr-2"><select name="items[${idx}][part_id]" class="g-part-select w-full" placeholder="Buscar repuesto..."></select></td>
            <td class="py-2 pr-2"><input type="number" step="0.01" name="items[${idx}][quantity]" value="1" class="g-qty w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-right"></td>
            <td class="py-2 pr-2"><input type="number" step="0.01" min="0" name="items[${idx}][unit_cost]" value="0" class="g-cost w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-right"></td>
            <td class="py-2 text-center">
                <button type="button" class="g-remove text-red-500 hover:text-red-700" title="Quitar línea">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </td>
        `;
        tbody.appendChild(tr);
        initPartSelect(tr);
        tr.querySelector('.g-remove').addEventListener('click', () => tr.remove());
    }

    document.getElementById('btn-add-line').addEventListener('click', addLine);
    addLine();

    // Toggle de secciones según el tipo de guía
    const typeSel = document.getElementById('guide_type');
    const reasonInputWrap = document.getElementById('reason-input-wrap');
    const reasonOutputWrap = document.getElementById('reason-output-wrap');
    const originWrap = document.getElementById('warehouse-origin-wrap');
    const destWrap = document.getElementById('warehouse-destination-wrap');
    const adjWrap = document.getElementById('warehouse-adjustment-wrap');
    const providerWrap = document.getElementById('provider-wrap');
    const invoiceWrap = document.getElementById('invoice-wrap');
    const adjustmentHint = document.getElementById('adjustment-hint');
    const thUnitCost = document.getElementById('th-unit-cost');

    function applyType() {
        const type = typeSel.value;
        reasonInputWrap.classList.toggle('hidden', type !== 'input');
        reasonOutputWrap.classList.toggle('hidden', type !== 'output');
        originWrap.classList.toggle('hidden', !['output', 'transfer'].includes(type));
        destWrap.classList.toggle('hidden', !['input', 'transfer'].includes(type));
        adjWrap.classList.toggle('hidden', type !== 'adjustment');
        providerWrap.classList.toggle('hidden', type !== 'input');
        invoiceWrap.classList.toggle('hidden', type !== 'input');
        adjustmentHint.classList.toggle('hidden', type !== 'adjustment');
        thUnitCost.classList.toggle('hidden', ['output', 'transfer'].includes(type));
        document.querySelectorAll('.g-cost').forEach(i => i.classList.toggle('hidden', ['output', 'transfer'].includes(type)));
    }
    typeSel.addEventListener('change', applyType);
    applyType();

    // Proveedor (solo ingreso)
    if (window.TomSelect) {
        providerTs = new TomSelect('#provider_id', {
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
    }
    </script>
    @endpush
</x-app-layout>
