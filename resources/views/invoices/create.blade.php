<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Nueva Factura') }}</h2>
            <a href="{{ route('invoices.index') }}" class="btn btn-secondary">Volver</a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            @if ($errors->any())
                <div class="mb-4 px-4 py-3 bg-red-50 border border-red-200 text-red-700 rounded-lg text-sm">
                    @foreach ($errors->all() as $error) <p>{{ $error }}</p> @endforeach
                </div>
            @endif

            <div class="card p-6">
                <form method="POST" action="{{ route('invoices.store') }}">
                    @csrf

                    {{-- Origen --}}
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Origen del comprobante <span class="text-red-500">*</span></label>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-2 max-w-2xl">
                            @foreach (['ot' => 'Orden de Trabajo', 'estimate' => 'Presupuestos', 'vehicle' => 'Por Vehículo', 'free' => 'Libre'] as $key => $label)
                                <label class="cursor-pointer">
                                    <input type="radio" name="origin" value="{{ $key }}" {{ ($origin === $key) ? 'checked' : '' }}
                                        onchange="window.toggleOrigin('{{ $key }}')"
                                        class="peer sr-only">
                                    <span class="block text-center text-sm font-medium px-3 py-2 rounded-lg border border-gray-300 text-gray-700 peer-checked:border-blue-600 peer-checked:bg-blue-50 peer-checked:text-blue-700">{{ $label }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    {{-- OT --}}
                    <div id="origin-ot" class="space-y-4">
                        <div>
                            <label for="work_order_id" class="block text-sm font-medium text-gray-700">Orden de Trabajo <span class="text-red-500">*</span></label>
                            <select id="work_order_id" name="work_order_id" class="mt-1 block w-full max-w-xl rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm"></select>
                        </div>
                        <p class="text-xs text-gray-500">La factura agrupará los presupuestos vinculados a la OT. Para facturar solo parte, usa el origen "Presupuestos".</p>
                    </div>

                    {{-- Presupuestos --}}
                    <div id="origin-estimate" class="hidden space-y-4">
                        <div>
                            <label for="estimate_ids" class="block text-sm font-medium text-gray-700">Presupuesto(s) <span class="text-red-500">*</span></label>
                            <select id="estimate_ids" name="estimate_ids[]" multiple class="mt-1 block w-full max-w-xl rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm"></select>
                            <p class="text-xs text-gray-500 mt-1">Puedes seleccionar varios presupuestos para una misma factura (siniestro + ampliaciones, flota, etc.). Todos deben estar en la misma moneda.</p>
                            <button type="button" id="add-related-btn" class="mt-2 btn btn-secondary">+ Agregar presupuestos del mismo vehículo</button>
                            <p id="currency-mix-warning" class="hidden mt-1 text-xs text-red-600"><span id="currency-mix-warning-text"></span></p>
                        </div>
                    </div>

                    {{-- Por Vehículo --}}
                    <div id="origin-vehicle" class="hidden space-y-4">
                        <div>
                            <label for="invoice_vehicle" class="block text-sm font-medium text-gray-700">Vehículo <span class="text-red-500">*</span></label>
                            <select id="invoice_vehicle" class="mt-1 block w-full max-w-xl rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm"></select>
                            <p class="text-xs text-gray-500 mt-1">Al elegir el vehículo se cargarán sus presupuestos facturables (ideal para flotas).</p>
                            <button type="button" id="load-vehicle-estimates-btn" class="mt-2 btn btn-secondary">Cargar presupuestos del vehículo</button>
                        </div>
                    </div>

                    {{-- Libre --}}
                    <div id="origin-free" class="hidden">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Ítems del comprobante <span class="text-red-500">*</span></label>
                        <div class="space-y-2" id="free-items">
                            <div class="grid grid-cols-12 gap-2 items-center">
                                <div class="col-span-5"><input name="items[0][description]" placeholder="Descripción" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm"></div>
                                <div class="col-span-2"><input name="items[0][quantity]" type="number" step="0.01" min="0.01" value="1" placeholder="Cant." class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm"></div>
                                <div class="col-span-2"><input name="items[0][unit_price]" type="number" step="0.01" min="0" value="0" placeholder="Precio" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm"></div>
                                <div class="col-span-2"><select name="items[0][uom]" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm"><option value="NIU">Producto</option><option value="ZZ">Servicio</option></select></div>
                                <div class="col-span-1"><button type="button" onclick="this.closest('.grid').remove()" class="btn-icon btn-icon-red" title="Quitar"><svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg></button></div>
                            </div>
                        </div>
                        <button type="button" onclick="window.addFreeItem()" class="mt-2 btn btn-secondary">+ Agregar ítem</button>
                    </div>

                    <div class="grid md:grid-cols-2 gap-4 mt-6">
                        <div>
                            <label for="invoice_type" class="block text-sm font-medium text-gray-700">Tipo de facturación <span class="text-red-500">*</span></label>
                            <select id="invoice_type" name="invoice_type" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                                <option value="regular">Cierre / Facturación completa</option>
                                <option value="advance">Adelanto</option>
                                <option value="franchise">Franquicia (cliente)</option>
                                <option value="insurance">Aseguradora (total − franquicia)</option>
                                <option value="free">Libre</option>
                            </select>
                        </div>
                        <div>
                            <label for="party_id" class="block text-sm font-medium text-gray-700">Receptor <span class="text-red-500">*</span></label>
                            <select id="party_id" name="party_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm"></select>
                        </div>
                    </div>

                    <div id="advance-amount-wrap" class="hidden grid md:grid-cols-2 gap-4 mt-4">
                        <div>
                            <label for="advance_amount" class="block text-sm font-medium text-gray-700">Monto del adelanto <span class="text-red-500">*</span></label>
                            <input type="number" step="0.01" min="0.01" id="advance_amount" name="advance_amount" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                        </div>
                    </div>

                    <div class="grid md:grid-cols-2 gap-4 mt-4">
                        <div>
                            <label for="invoice_date" class="block text-sm font-medium text-gray-700">Fecha de emisión</label>
                            <input type="date" id="invoice_date" name="invoice_date" value="{{ now()->toDateString() }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                        </div>
                        <div>
                            <label for="observations" class="block text-sm font-medium text-gray-700">Observaciones</label>
                            <input type="text" id="observations" name="observations" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                        </div>
                    </div>

                    <div class="mt-6 flex justify-end">
                        <button type="submit" class="btn btn-primary" data-loading-text="Guardando...">Crear comprobante</button>
                    </div>
                </form>
            </div>
        </div>
    </div>


    @push('scripts')
    <script>
        const invoiceType = document.getElementById('invoice_type');
        const advanceWrap = document.getElementById('advance-amount-wrap');
        const toggleAdvance = () => advanceWrap.classList.toggle('hidden', invoiceType.value !== 'advance');
        invoiceType.addEventListener('change', toggleAdvance);
        toggleAdvance();

        window.toggleOrigin = (key) => {
            ['ot', 'estimate', 'vehicle', 'free'].forEach(k => {
                document.getElementById('origin-' + k).classList.toggle('hidden', k !== key);
            });
        };

        window.addFreeItem = () => {
            const idx = document.querySelectorAll('#free-items .grid').length;
            const div = document.createElement('div');
            div.className = 'grid grid-cols-12 gap-2 items-center';
            div.innerHTML = `
                <div class="col-span-5"><input name="items[${idx}][description]" placeholder="Descripción" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm"></div>
                <div class="col-span-2"><input name="items[${idx}][quantity]" type="number" step="0.01" min="0.01" value="1" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm"></div>
                <div class="col-span-2"><input name="items[${idx}][unit_price]" type="number" step="0.01" min="0" value="0" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm"></div>
                <div class="col-span-2"><select name="items[${idx}][uom]" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm"><option value="NIU">Producto</option><option value="ZZ">Servicio</option></select></div>
                <div class="col-span-1"><button type="button" onclick="this.closest('.grid').remove()" class="btn-icon btn-icon-red" title="Quitar"><svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg></button></div>`;
            document.getElementById('free-items').appendChild(div);
        };

        // Mapa id → moneda de los presupuestos cargados (para validar moneda uniforme).
        const estimateCurrencyMap = {};

        const ts = (el, url, multi, onAdd) => new TomSelect(el, {
            valueField: 'id', labelField: 'text', searchField: ['text', 'document_sn'], maxItems: multi ? 20 : 1, closeAfterSelect: true, create: false, copyClassesToDropdown: false, dropdownParent: 'body',
            load: (q, cb) => {
                fetch(url + '?q=' + encodeURIComponent(q), { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                    .then(r => r.json()).then(d => {
                        const rows = Array.isArray(d) ? d : d.data || [];
                        rows.forEach(r => { if (r.id) estimateCurrencyMap[r.id] = r.currency || 'PEN'; });
                        cb(rows);
                    }).catch(() => cb());
            },
            render: { option: (d, e) => `<div>${d.text || d.document_sn}</div>` },
            onItemAdd: (value, item) => { document.activeElement.blur(); if (onAdd) onAdd(value); },
            onDropdownOpen: () => { document.querySelector(`#${el} + .ts-wrapper input`)?.setSelectionRange(0, 0); }
        });

        let autoSuggesting = false;

        const currencyWarn = document.getElementById('currency-mix-warning');
        const currencyWarnText = document.getElementById('currency-mix-warning-text');
        const showCurrencyWarning = (msg) => {
            if (!currencyWarn) return;
            if (currencyWarnText) currencyWarnText.textContent = msg;
            currencyWarn.classList.remove('hidden');
        };
        const hideCurrencyWarning = () => { if (currencyWarn) currencyWarn.classList.add('hidden'); };

        const addEstimatesToSelect = (rows, select) => {
            const current = select.items.map(String);
            const firstCurrency = current.length ? (estimateCurrencyMap[current[0]] || 'PEN') : null;
            let skipped = 0;
            rows.forEach(r => {
                if (r.id) estimateCurrencyMap[r.id] = r.currency || 'PEN';
                // Solo se auto-agregan presupuestos de la misma moneda que el primero.
                if (firstCurrency && (r.currency || 'PEN') !== firstCurrency) { skipped++; return; }
                if (!current.includes(String(r.id))) {
                    select.addOption({ id: r.id, text: r.text, document_sn: r.text, currency: r.currency || 'PEN' });
                    select.addItem(r.id, true);
                }
            });
            if (skipped > 0) {
                showCurrencyWarning(`Se omitieron ${skipped} presupuesto(s) por estar en una moneda distinta a la seleccionada.`);
            } else {
                hideCurrencyWarning();
            }
        };

        const fetchRelated = (estimateId) => fetch("{{ route('api.estimates.related') }}?estimate_id=" + estimateId, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .then(r => r.json()).catch(() => []);

        // Auto-sugerencia: al elegir el primer presupuesto se preseleccionan los del mismo vehículo/OT.
        // chargeable=1 excluye garantías y ajustes internos (no facturables).
        const estimateSelect = ts('#estimate_ids', "{{ route('api.estimates.search') }}?chargeable=1", true, (value) => {
            if (autoSuggesting || estimateSelect.items.length > 1) return;
            autoSuggesting = true;
            fetchRelated(value).then(rows => {
                if (rows.length) addEstimatesToSelect(rows, estimateSelect);
            }).finally(() => { autoSuggesting = false; });
        });

        // Botón: agregar presupuestos del mismo vehículo/OT que el primero seleccionado.
        document.getElementById('add-related-btn').addEventListener('click', () => {
            const first = estimateSelect.items[0];
            if (!first) { alert('Selecciona primero un presupuesto.'); return; }
            autoSuggesting = true;
            fetchRelated(first).then(rows => addEstimatesToSelect(rows, estimateSelect)).finally(() => { autoSuggesting = false; });
        });

        // Moneda uniforme: bloquea el envío si se mezclan presupuestos PEN y USD.
        const invoiceForm = document.querySelector('form[action*="/invoices"]');
        if (invoiceForm) {
            invoiceForm.addEventListener('submit', function (e) {
                const ids = estimateSelect.items.map(String);
                if (ids.length < 2) { hideCurrencyWarning(); return; }
                const currencies = ids.map(id => estimateCurrencyMap[id] || 'PEN');
                if (new Set(currencies).size > 1) {
                    e.preventDefault();
                    e.stopPropagation();
                    showCurrencyWarning('No se pueden facturar presupuestos en monedas distintas (PEN/USD). Todos deben estar en la misma moneda.');
                } else {
                    hideCurrencyWarning();
                }
            });
        }

        // Origen "Por Vehículo": carga los presupuestos facturables del vehículo.
        const vehicleSelect = ts('#invoice_vehicle', "{{ route('api.vehicles.search') }}", false);
        document.getElementById('load-vehicle-estimates-btn').addEventListener('click', () => {
            const vid = vehicleSelect.getValue();
            if (!vid) { alert('Elige un vehículo.'); return; }
            autoSuggesting = true;
            fetch("{{ route('api.estimates.by-vehicle') }}?vehicle_id=" + vid, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                .then(r => r.json())
                .then(rows => {
                    estimateSelect.clear();
                    if (rows.length) addEstimatesToSelect(rows, estimateSelect);
                    document.querySelector('input[name="origin"][value="estimate"]').checked = true;
                    window.toggleOrigin('estimate');
                })
                .catch(() => {})
                .finally(() => { autoSuggesting = false; });
        });

        ts('#work_order_id', "{{ route('api.work-orders.search') }}", false);
        ts('#party_id', "{{ route('api.invoices.parties') }}", false);
    </script>
    @endpush
</x-app-layout>

