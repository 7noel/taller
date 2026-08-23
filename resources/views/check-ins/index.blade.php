<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Inventario Vehicular') }}</h2>
            @can('crear inventarios')
                <a href="{{ route('check-ins.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 rounded-md font-semibold text-xs text-white uppercase hover:bg-blue-700">+ Nuevo Ingreso</a>
            @endcan
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="mb-4 px-4 py-3 bg-green-100 border border-green-400 text-green-700 rounded">{{ session('success') }}</div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    {{-- Filtros --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4 mb-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Búsqueda</label>
                            <input type="text" id="f-search" placeholder="Placa, marca, cliente..." class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Placa</label>
                            <input type="text" id="f-plate" placeholder="ABC-123" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 uppercase">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Cliente</label>
                            <select id="f-client" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"></select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Estado</label>
                            <select id="f-status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                                <option value="">Todos</option>
                                @foreach (\App\Models\CheckIn::STATUS_LABELS as $value => $label)
                                    <option value="{{ $value }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Fecha de inicio</label>
                            <input type="date" id="f-date-from" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4 mb-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Fecha de fin</label>
                            <input type="date" id="f-date-to" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>
                        <div class="lg:col-span-4 flex items-end">
                            <button type="button" id="btn-reset" class="inline-flex items-center px-3 py-2 bg-gray-200 rounded-md text-xs text-gray-700 uppercase hover:bg-gray-300">Limpiar filtros</button>
                        </div>
                    </div>

                    <div id="check-in-table"></div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
    const API_SEARCH = "{{ route('api.check-ins.search') }}";

    // Tom Select para clientes
    const clientSelect = new TomSelect('#f-client', {
        valueField: 'id',
        labelField: 'display_name',
        searchField: ['display_name', 'document_number'],
        placeholder: 'Buscar cliente...',
        copyClassesToDropdown: false,
        create: false,
        maxOptions: 20,
        closeAfterSelect: true, maxItems: 1,
        shouldLoad: function (query) { return query.length >= 2; },
        load: function (query, callback) {
            fetch(`/api/parties/search?q=${encodeURIComponent(query)}&limit=20`)
                .then(r => r.json())
                .then(data => {
                    callback(data.map(p => ({
                        id: p.id,
                        display_name: p.display_name,
                        document_type: p.document_type,
                        document_number: p.document_number,
                    })));
                })
                .catch(() => callback());
        },
        render: {
            option: function (item, escape) {
                return `<div class="py-1">
                    <div class="font-medium text-gray-900">${escape(item.display_name)}</div>
                    <div class="text-xs text-gray-500">${escape(item.document_number || '')}</div>
                </div>`;
            },
            item: function (item, escape) {
                return `<div>${escape(item.display_name)}</div>`;
            },
        },
    });

    // Single estricto: blur + cerrar dropdown al seleccionar
    clientSelect.on('item_add', function () {
        clientSelect.blur();
        clientSelect.close();
    });
    clientSelect.on('dropdown_open', function () { if (clientSelect.items.length > 0) { clientSelect.setTextValue(''); clientSelect.input && clientSelect.input.setSelectionRange(0, 0); } });

    const statusColors = {
        'draft': 'bg-gray-100 text-gray-800',
        'pending_approval': 'bg-yellow-100 text-yellow-800',
        'approved': 'bg-green-100 text-green-800',
        'rejected': 'bg-red-100 text-red-800',
        'closed': 'bg-blue-100 text-blue-800',
    };

    function buildUrl() {
        const params = new URLSearchParams();
        params.set('limit', 100);

        const q = document.getElementById('f-search').value.trim();
        if (q) params.set('q', q);

        const plate = document.getElementById('f-plate').value.trim().toUpperCase();
        if (plate) params.set('plate', plate);

        const clientId = clientSelect.getValue();
        if (clientId) params.set('client_id', clientId);

        const status = document.getElementById('f-status').value;
        if (status) params.set('status', status);

        const dateFrom = document.getElementById('f-date-from').value;
        if (dateFrom) params.set('date_from', dateFrom);

        const dateTo = document.getElementById('f-date-to').value;
        if (dateTo) params.set('date_to', dateTo);

        return API_SEARCH + '?' + params.toString();
    }

    const table = new Tabulator('#check-in-table', {
        ajaxURL: buildUrl(),
        layout: 'fitColumns',
        responsiveLayout: 'collapse',
        placeholder: 'No hay inventarios registrados',
        height: 'auto',
        columns: [
            { title: 'Placa', field: 'plate', width: 110, hozAlign: 'center', formatter: function(cell) {
                const d = cell.getData();
                return `<a href="/check-ins/${d.id}" class="text-blue-600 font-medium">${d.plate || '-'}</a>`;
            }},
            { title: 'Cliente', field: 'client_name', minWidth: 170,
              formatter: function(cell) {
                const d = cell.getData();
                return `<div class="text-gray-900">${d.client_name || '-'}</div>
                        <div class="text-xs text-gray-500">${d.client_document || ''}</div>`;
              }},
            { title: 'Servicio', field: 'service_type', width: 120, hozAlign: 'center' },
            { title: 'Fecha', field: 'created_at', width: 130, hozAlign: 'center' },
            { title: 'Estado', field: 'status_label', width: 180, hozAlign: 'center',
              formatter: function(cell) {
                const d = cell.getData();
                const colors = statusColors[d.status] || 'bg-gray-100 text-gray-800';
                return `<span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-medium ${colors}">${d.status_label}</span>`;
              }},
            { title: 'Acciones', field: 'actions', width: 180, hozAlign: 'center',
              formatter: function(cell) {
                const id = cell.getData().id;
                return `<div class="flex gap-2 justify-center">
                    <a href="/check-ins/${id}" class="text-blue-600">Ver</a>
                    @can('editar inventarios')<a href="/check-ins/${id}/edit" class="text-yellow-600">Editar</a>@endcan
                    @can('eliminar inventarios')
                    <form method="POST" action="/check-ins/${id}" class="inline" onsubmit="return confirm('¿Eliminar este inventario?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="text-red-600">Eliminar</button>
                    </form>
                    @endcan
                </div>`;
              }},
        ],
    });

    // Re-cargar con filtros
    function refreshTable() {
        table.setData(buildUrl());
    }

    ['f-search', 'f-plate', 'f-status', 'f-date-from', 'f-date-to'].forEach(id => {
        document.getElementById(id).addEventListener('input', refreshTable);
        document.getElementById(id).addEventListener('change', refreshTable);
    });
    clientSelect.on('change', refreshTable);

    document.getElementById('btn-reset').addEventListener('click', function () {
        document.getElementById('f-search').value = '';
        document.getElementById('f-plate').value = '';
        document.getElementById('f-status').value = '';
        document.getElementById('f-date-from').value = '';
        document.getElementById('f-date-to').value = '';
        clientSelect.clear();
        refreshTable();
    });
    </script>
    @endpush
</x-app-layout>