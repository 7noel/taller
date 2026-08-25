<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Stock') }}</h2>
            <button type="button" id="btn-open-movement" class="btn btn-primary">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                </svg>
                Registrar Movimiento
            </button>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="mb-4 px-4 py-3 bg-green-50 border border-green-200 text-green-700 rounded-lg text-sm">{{ session('success') }}</div>
            @endif
            @if ($errors->any())
                <div class="mb-4 px-4 py-3 bg-red-50 border border-red-200 text-red-700 rounded-lg text-sm">
                    {{ $errors->first() }}
                </div>
            @endif

            <div class="card overflow-hidden">
                <div class="p-4 sm:p-5">
                    <div class="mb-3">
                        <div class="relative max-w-md">
                            <svg class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                            <input type="text" id="stock-search" placeholder="Buscar por repuesto o almacén..." class="search-input">
                        </div>
                    </div>
                    <div id="stock-table" class="data-table"></div>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal para registrar movimiento --}}
    <div id="movement-modal" class="fixed inset-0 z-50 hidden items-center justify-center">
        <div class="absolute inset-0 bg-gray-900/50" data-close-movement></div>
        <div class="relative w-full max-w-lg rounded-lg bg-white shadow-xl">
            <div class="flex items-center justify-between border-b border-gray-200 px-5 py-4">
                <h3 class="text-lg font-semibold text-gray-800">Registrar Movimiento</h3>
                <button type="button" data-close-movement class="text-gray-400 hover:text-gray-600">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <form method="POST" action="{{ route('stock.store') }}" id="movement-form" class="p-5">
                @csrf
                <div class="grid grid-cols-1 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Repuesto <span class="text-red-500">*</span></label>
                        <select id="part_id" name="part_id" placeholder="Buscar repuesto..."></select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Almacén <span class="text-red-500">*</span></label>
                        <select id="warehouse_id" name="warehouse_id" placeholder="Buscar almacén..." class="mt-1 w-full"></select>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Tipo <span class="text-red-500">*</span></label>
                            <select name="type" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="entry">Entrada</option>
                                <option value="exit">Salida</option>
                                <option value="adjustment">Ajuste</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Cantidad <span class="text-red-500">*</span></label>
                            <input type="number" step="0.01" min="0.01" name="quantity" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Moneda <span class="text-red-500">*</span></label>
                            <select name="currency" id="currency" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="PEN">Soles (PEN)</option>
                                <option value="USD">Dólares (USD)</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Costo unitario <span class="text-red-500">*</span></label>
                            <input type="number" step="0.01" min="0" name="unit_cost" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>
                    </div>
                    <div id="exchange-rate-wrap" class="hidden">
                        <label class="block text-sm font-medium text-gray-700">Tipo de cambio <span class="text-red-500">*</span></label>
                        <input type="number" step="0.0001" min="0" name="exchange_rate" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Referencia</label>
                        <input type="text" name="reference" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>
                </div>
                <div class="mt-5 flex justify-end gap-2">
                    <button type="button" data-close-movement class="btn btn-secondary">Cancelar</button>
                    <button type="submit" id="movement-submit" class="btn btn-primary">Guardar</button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
    <script>
        const table = new Tabulator('#stock-table', {
            ajaxURL: "{{ route('api.stock.search') }}?limit=200",
            layout: 'fitColumns',
            responsiveLayout: 'collapse',
            placeholder: 'No hay stock registrado',
            height: 'auto',
            columns: [
                { title: 'Repuesto', field: 'part', minWidth: 180 },
                { title: 'SKU', field: 'sku', width: 110 },
                { title: 'Almacén', field: 'warehouse', width: 150 },
                { title: 'Cantidad', field: 'quantity', width: 100, hozAlign: 'right' },
                { title: 'Costo Promedio (S/)', field: 'average_cost', width: 160, hozAlign: 'right' },
                { title: 'Valor Total (S/)', field: 'total_value', width: 140, hozAlign: 'right' },
            ]
        });

        document.getElementById('stock-search').addEventListener('input', function(e) {
            table.setData("{{ route('api.stock.search') }}?q=" + encodeURIComponent(e.target.value) + "&limit=200");
        });

        // Modal de movimiento
        const modal = document.getElementById('movement-modal');
        const openBtn = document.getElementById('btn-open-movement');
        const closeBtns = document.querySelectorAll('[data-close-movement]');

        function openModal() { modal.classList.remove('hidden'); modal.classList.add('flex'); }
        function closeModal() { modal.classList.add('hidden'); modal.classList.remove('flex'); }

        openBtn.addEventListener('click', openModal);
        closeBtns.forEach(b => b.addEventListener('click', closeModal));

        // Tom Select: repuesto
        if (window.TomSelect) {
            new TomSelect('#part_id', {
                valueField: 'id',
                labelField: 'name',
                searchField: ['name', 'sku'],
                maxItems: 1, closeAfterSelect: true, create: false, copyClassesToDropdown: false,
                load: function (query, callback) {
                    fetch(`{{ route('api.parts.search') }}?q=${encodeURIComponent(query)}`)
                        .then(r => r.json()).then(callback).catch(() => callback());
                }
            });

            new TomSelect('#warehouse_id', {
                valueField: 'id',
                labelField: 'name',
                searchField: ['name', 'code'],
                maxItems: 1, closeAfterSelect: true, create: false, copyClassesToDropdown: false,
                load: function (query, callback) {
                    fetch(`{{ route('api.warehouses.search') }}?q=${encodeURIComponent(query)}`)
                        .then(r => r.json()).then(callback).catch(() => callback());
                }
            });
        }

        // Mostrar tipo de cambio si es USD
        const currencySel = document.getElementById('currency');
        const exchangeWrap = document.getElementById('exchange-rate-wrap');
        currencySel.addEventListener('change', () => exchangeWrap.classList.toggle('hidden', currencySel.value !== 'USD'));

        // Anti-doble envío
        let saving = false;
        const form = document.getElementById('movement-form');
        form.addEventListener('submit', function () {
            if (saving) { event.preventDefault(); return; }
            saving = true;
            const btn = document.getElementById('movement-submit');
            btn.disabled = true;
            btn.textContent = 'Guardando...';
        });
    </script>
    @endpush
</x-app-layout>