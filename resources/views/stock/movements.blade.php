<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Kardex / Movimientos') }}</h2>
            <a href="{{ route('inventory-guides.create') }}" class="btn btn-primary">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                </svg>
                Nueva guía
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="mb-4 px-4 py-3 bg-green-50 border border-green-200 text-green-700 rounded-lg text-sm">{{ session('success') }}</div>
            @endif

            {{-- Filtros --}}
            <div class="card p-4 sm:p-5 mb-4">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-6 gap-3">
                    <div class="lg:col-span-2">
                        <label class="block text-sm font-medium text-gray-700">Repuesto</label>
                        <select id="filter-part" placeholder="Todos los repuestos..."></select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Almacén</label>
                        <select id="filter-warehouse" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="">Todos</option>
                            @foreach ($warehouses as $w)
                                <option value="{{ $w->id }}">{{ $w->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Tipo</label>
                        <select id="filter-type" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="">Todos</option>
                            <option value="entry">Entrada</option>
                            <option value="exit">Salida</option>
                            <option value="adjustment">Ajuste</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Desde</label>
                        <input type="date" id="filter-from" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Hasta</label>
                        <input type="date" id="filter-to" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>
                </div>
            </div>

            {{-- Resumen del kardex (repuesto + almacén) --}}
            <div id="kardex-summary" class="hidden mb-4">
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <div class="card p-4">
                        <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Saldo inicial</p>
                        <p id="sum-opening" class="text-lg font-semibold text-gray-800 mt-1">0</p>
                    </div>
                    <div class="card p-4">
                        <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Movimientos</p>
                        <p id="sum-count" class="text-lg font-semibold text-gray-800 mt-1">0</p>
                    </div>
                    <div class="card p-4">
                        <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Saldo final</p>
                        <p id="sum-closing" class="text-lg font-semibold text-gray-800 mt-1">0</p>
                    </div>
                </div>
            </div>

            <div class="card overflow-hidden">
                <div class="p-4 sm:p-5">
                    <div id="movements-table" class="data-table"></div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        const typeBadge = (cell) => {
            const v = cell.getValue();
            const map = {
                entry: ['Entrada', 'bg-green-50 text-green-700'],
                exit: ['Salida', 'bg-red-50 text-red-700'],
                adjustment: ['Ajuste', 'bg-amber-50 text-amber-700'],
            };
            const [label, cls] = map[v] || [v, 'bg-gray-100 text-gray-600'];
            return `<span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium ${cls}">${label}</span>`;
        };

        const table = new Tabulator('#movements-table', {
            layout: 'fitColumns',
            responsiveLayout: 'collapse',
            placeholder: 'No hay movimientos registrados',
            height: 'auto',
            columns: [
                { title: 'Fecha', field: 'date', width: 140 },
                { title: 'Documento', field: 'document_sn', width: 130, formatter: cell => cell.getValue() || '<span class="text-gray-400">—</span>' },
                { title: 'Tipo', field: 'type', width: 100, hozAlign: 'center', formatter: typeBadge },
                { title: 'Repuesto', field: 'part', minWidth: 170 },
                { title: 'SKU', field: 'sku', width: 100 },
                { title: 'Almacén', field: 'warehouse', width: 130 },
                { title: 'Cant.', field: 'signed_quantity', width: 90, hozAlign: 'right' },
                { title: 'C. Unit. (S/)', field: 'unit_cost', width: 110, hozAlign: 'right' },
                { title: 'C. Total (S/)', field: 'total_cost', width: 120, hozAlign: 'right' },
                { title: 'Motivo', field: 'reason', minWidth: 180, formatter: cell => {
                    const v = cell.getValue();
                    return v ? (cell.getData().reason_code ? `${cell.getData().reason_code} · ${v}` : v) : '<span class="text-gray-400">—</span>';
                } },
                { title: 'Saldo', field: 'balance', width: 100, hozAlign: 'right', formatter: cell => {
                    if (cell.getValue() !== null && cell.getValue() !== undefined) return cell.getValue();
                    const d = cell.getData();
                    return d.current_balance !== undefined ? d.current_balance : '';
                } },
            ],
        });

        function loadMovements() {
            const params = new URLSearchParams();
            const partId = document.getElementById('filter-part').value;
            const warehouseId = document.getElementById('filter-warehouse').value;
            const type = document.getElementById('filter-type').value;
            const from = document.getElementById('filter-from').value;
            const to = document.getElementById('filter-to').value;
            if (partId) params.set('part_id', partId);
            if (warehouseId) params.set('warehouse_id', warehouseId);
            if (type) params.set('type', type);
            if (from) params.set('from', from);
            if (to) params.set('to', to);

            fetch("{{ route('api.stock.movements') }}?" + params.toString())
                .then(r => r.json())
                .then(data => {
                    const summary = document.getElementById('kardex-summary');
                    if (data.kardex) {
                        summary.classList.remove('hidden');
                        document.getElementById('sum-opening').textContent =
                            data.opening.quantity + ' uds · ' + data.opening.total_value + ' S/';
                        document.getElementById('sum-count').textContent = data.movements.length;
                        document.getElementById('sum-closing').textContent =
                            data.closing.quantity + ' uds · ' + data.closing.total_value + ' S/';
                    } else {
                        summary.classList.add('hidden');
                    }
                    table.setData(data.movements);
                });
        }

        if (window.TomSelect) {
            new TomSelect('#filter-part', {
                valueField: 'id',
                labelField: 'name',
                searchField: ['name', 'sku'],
                maxItems: 1,
                closeAfterSelect: true,
                create: false,
                copyClassesToDropdown: false,
                placeholder: 'Todos los repuestos...',
                load: function (query, callback) {
                    fetch(`{{ route('api.parts.search') }}?q=${encodeURIComponent(query)}&limit=50`)
                        .then(r => r.json()).then(callback).catch(() => callback());
                }
            });
        }

        ['filter-warehouse', 'filter-type', 'filter-from', 'filter-to'].forEach(id => {
            document.getElementById(id).addEventListener('change', loadMovements);
        });
        document.getElementById('filter-part').addEventListener('change', loadMovements);

        loadMovements();
    </script>
    @endpush
</x-app-layout>
