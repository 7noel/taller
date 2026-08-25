<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap justify-between items-center gap-3">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Presupuestos') }}</h2>
            <a href="{{ route('estimates.create') }}" class="btn btn-primary">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                </svg>
                Nuevo Presupuesto
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="mb-4 px-4 py-3 bg-green-50 border border-green-200 text-green-700 rounded-lg text-sm">{{ session('success') }}</div>
            @endif

            <div class="card overflow-hidden">
                <div class="p-4 sm:p-5">
                    <div class="mb-3">
                        <div class="relative max-w-md">
                            <svg class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                            <input type="text" id="estimate-search" placeholder="Buscar por documento, placa, cliente o aseguradora..." class="search-input">
                        </div>
                    </div>
                    <div id="estimate-table" class="data-table"></div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        const statusColors = {
            draft: 'bg-gray-100 text-gray-800',
            sent_insurance: 'bg-yellow-100 text-yellow-800',
            approved_insurance: 'bg-blue-100 text-blue-800',
            rejected_insurance: 'bg-red-100 text-red-800',
            sent_client: 'bg-yellow-100 text-yellow-800',
            approved_client: 'bg-blue-100 text-blue-800',
            rejected_client: 'bg-red-100 text-red-800',
            in_repair: 'bg-indigo-100 text-indigo-800',
            finalized: 'bg-green-100 text-green-800',
        };

        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
        const finalStatuses = ['finalized', 'rejected_insurance', 'rejected_client'];

        const table = new Tabulator('#estimate-table', {
            ajaxURL: "{{ route('api.estimates.search') }}?limit=100",
            layout: 'fitColumns',
            responsiveLayout: 'collapse',
            placeholder: 'No hay presupuestos registrados',
            height: 'auto',
            columns: [
                {
                    title: 'Documento',
                    field: 'document_sn',
                    width: 130,
                    formatter: function (cell) {
                        const data = cell.getData();
                        return `<a href="/estimates/${data.id}" class="text-blue-600 hover:text-blue-800 font-medium">${data.document_sn || '-'}</a>`;
                    }
                },
                { title: 'Placa', field: 'plate', width: 80, hozAlign: 'center' },
                { title: 'Cliente', field: 'client_name', minWidth: 150 },
                { title: 'Aseguradora', field: 'insurance_company', width: 130 },
                {
                    title: 'Estado',
                    field: 'status',
                    width: 150,
                    formatter: function (cell) {
                        const data = cell.getData();
                        const color = statusColors[data.status] || 'bg-gray-100 text-gray-800';
                        return `<span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-medium ${color}">${data.status_label}</span>`;
                    }
                },
                {
                    title: 'Valor Bruto',
                    field: 'subtotal',
                    width: 100,
                    hozAlign: 'right',
                    formatter: function (cell) {
                        return 'S/ ' + Number(cell.getValue() || 0).toFixed(2);
                    }
                },
                {
                    title: 'Total',
                    field: 'total',
                    width: 110,
                    hozAlign: 'right',
                    formatter: function (cell) {
                        return 'S/ ' + Number(cell.getValue() || 0).toFixed(2);
                    }
                },
                { title: 'Fecha', field: 'created_at', width: 100, hozAlign: 'center' },
                {
                    title: '',
                    field: 'actions',
                    width: 90,
                    hozAlign: 'center',
                    headerSort: false,
                    formatter: function (cell) {
                        const data = cell.getData();
                        const isFinal = finalStatuses.includes(data.status);
                        const viewBtn = `<a href="/estimates/${data.id}" class="btn-icon btn-icon-blue" title="Ver presupuesto">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                        </a>`;
                        const editBtn = isFinal ? '' : `<a href="/estimates/${data.id}/edit" class="btn-icon btn-icon-amber" title="Editar presupuesto">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        </a>`;
                        const deleteForm = `<form method="POST" action="/estimates/${data.id}" data-confirm="¿Estás seguro de eliminar el presupuesto ${data.document_sn}?" class="inline">
                            <input type="hidden" name="_token" value="${csrfToken}">
                            <input type="hidden" name="_method" value="DELETE">
                            <button type="submit" class="btn-icon btn-icon-red" title="Eliminar presupuesto">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </button>
                        </form>`;
                        return `<div class="flex items-center justify-center gap-1">${viewBtn}${editBtn}${deleteForm}</div>`;
                    }
                },
            ]
        });

        document.getElementById('estimate-search').addEventListener('input', function (e) {
            table.setData("{{ route('api.estimates.search') }}?q=" + encodeURIComponent(e.target.value) + "&limit=100");
        });
    </script>
    @endpush
</x-app-layout>