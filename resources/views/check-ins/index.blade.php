<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Inventario Vehicular') }}</h2>
            @can('crear inventarios')
                <a href="{{ route('check-ins.create') }}" class="btn btn-primary">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                    </svg>
                    Nuevo Ingreso
                </a>
            @endcan
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="mb-4 px-4 py-3 bg-green-50 border border-green-200 text-green-700 rounded-lg text-sm">{{ session('success') }}</div>
            @endif

            <div class="card overflow-hidden">
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
                            <button type="button" id="btn-reset" class="btn btn-secondary">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v6h6M20 20v-6h-6M20 10A8 8 0 005.2 6 8 8 0 104 14"/>
                                </svg>
                                Limpiar filtros
                            </button>
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
            { title: 'Documento', field: 'document_sn', width: 150, hozAlign: 'center', headerHozAlign: 'center', formatter: function(cell) {
                const d = cell.getData();
                const label = d.document_sn || '—';
                return `<a href="/check-ins/${d.id}" title="Ver inventario ${label}" class="font-mono font-semibold text-blue-600 hover:text-blue-800">${label}</a>`;
            }},
            { title: 'Placa', field: 'plate', width: 110, hozAlign: 'center', headerHozAlign: 'center', formatter: function(cell) {
                const d = cell.getData();
                return `<span class="text-gray-900">${d.plate || '-'}</span>`;
            }},
            { title: 'Cliente', field: 'client_name', minWidth: 170,
              formatter: function(cell) {
                const d = cell.getData();
                return `<div class="text-gray-900">${d.client_name || '-'}</div>
                        <div class="text-xs text-gray-500">${d.client_document || ''}</div>`;
              }},
            { title: 'Servicio', field: 'service_type', width: 120, hozAlign: 'center', headerHozAlign: 'center' },
            { title: 'Estado', field: 'status_label', width: 180, hozAlign: 'center', headerHozAlign: 'center',
              formatter: function(cell) {
                const d = cell.getData();
                const colors = statusColors[d.status] || 'bg-gray-100 text-gray-800';
                return `<span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-medium ${colors}">${d.status_label}</span>`;
              }},
            { title: 'Acciones', field: 'id', width: 160, hozAlign: 'center', headerHozAlign: 'center',
              formatter: function(cell) {
                const id = cell.getData().id;
                return `<div class="flex gap-2 justify-center">
                    <a href="/check-ins/${id}" title="Ver inventario" class="btn-icon btn-icon-blue">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                    </a>
                    @can('crear presupuestos')
                    ${cell.getData().status === 'approved' ? `<a href="/estimates/create?check_in_id=${id}" title="Crear presupuesto desde el inventario" class="btn-icon btn-icon-blue">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                    </a>` : ''}
                    @endcan
                    @can('editar inventarios')
                    <a href="/check-ins/${id}/edit" title="Editar inventario" class="btn-icon btn-icon-amber">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                    </a>
                    @endcan
                    @can('eliminar inventarios')
                    <form method="POST" action="/check-ins/${id}" class="inline" data-confirm="¿Eliminar este inventario?">
                        @csrf @method('DELETE')
                        <button type="submit" title="Eliminar inventario" class="btn-icon btn-icon-red">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                        </button>
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