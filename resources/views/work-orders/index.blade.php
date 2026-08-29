<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Órdenes de Trabajo') }}</h2>
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
                            <input type="text" id="work-order-search" placeholder="Buscar por serie, placa, marca o cliente..." class="search-input">
                        </div>
                    </div>
                    <div id="work-order-table" class="data-table"></div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        const statusColors = {
            'open': 'bg-gray-100 text-gray-800',
            'in_progress': 'bg-blue-100 text-blue-800',
            'waiting_parts': 'bg-yellow-100 text-yellow-800',
            'quality_control': 'bg-purple-100 text-purple-800',
            'ready_for_delivery': 'bg-teal-100 text-teal-800',
            'delivered': 'bg-green-100 text-green-800',
            'delivered_pending': 'bg-orange-100 text-orange-800',
            'closed': 'bg-gray-100 text-gray-500'
        };
        const statusLabel = (status) => ({
            'open': 'Abierta',
            'in_progress': 'En progreso',
            'waiting_parts': 'En espera de repuestos',
            'quality_control': 'En control de calidad',
            'ready_for_delivery': 'Lista para entrega',
            'delivered': 'Entregada',
            'delivered_pending': 'Entregado con pendientes',
            'closed': 'Cerrada'
        }[status] || status || '');

        const table = new Tabulator('#work-order-table', {
            ajaxURL: "{{ route('api.work-orders.search') }}?limit=100",
            layout: 'fitColumns',
            responsiveLayout: 'collapse',
            placeholder: 'No hay órdenes de trabajo registradas',
            height: 'auto',
            columns: [
                {
                    title: 'OT', field: 'document_sn', width: 130, hozAlign: 'center',
                    formatter: function(cell) {
                        const id = cell.getData().id;
                        return `<a href="/work-orders/${id}" class="font-semibold text-blue-600 hover:text-blue-800">${cell.getValue() || '—'}</a>`;
                    }
                },
                { title: 'Placa', field: 'plate', width: 100, hozAlign: 'center' },
                { title: 'Vehículo', field: 'vehicle_model', width: 140, formatter: function(cell) {
                    const d = cell.getData();
                    return (d.vehicle_brand || '') + ' ' + (d.vehicle_model || '');
                }},
                { title: 'Cliente', field: 'client_name', minWidth: 180 },
                { title: 'Presupuestos', field: 'estimates_count', width: 110, hozAlign: 'center' },
                {
                    title: 'Total', field: 'total', width: 110, hozAlign: 'right',
                    formatter: cell => 'S/ ' + Number(cell.getValue() || 0).toFixed(2)
                },
                {
                    title: 'Estado', field: 'status', width: 150, hozAlign: 'center',
                    formatter: cell => {
                        const s = cell.getValue();
                        return `<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${statusColors[s] || 'bg-gray-100 text-gray-800'}">${statusLabel(s)}</span>`;
                    }
                },
                {
                    title: 'Próxima acción', field: 'next_action', minWidth: 200, headerSort: false,
                    formatter: cell => {
                        const d = cell.getData();
                        if (d.status === 'closed') {
                            return `<span class="text-xs text-gray-400">${d.next_action || '—'}</span>`;
                        }
                        return `<div class="flex items-start gap-1.5 text-xs text-gray-600">
                            <svg class="h-3.5 w-3.5 mt-px shrink-0 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <span>${d.next_action || '—'}</span>
                        </div>`;
                    }
                },
                {
                    title: 'Acciones', field: 'id', width: 120, hozAlign: 'center', headerSort: false,
                    formatter: function(cell) {
                        const id = cell.getData().id;
                        return `<div class="flex gap-2 justify-center">
                            <a href="/work-orders/${id}" title="Ver orden de trabajo" class="btn-icon btn-icon-blue">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                            </a>
                            <form method="POST" action="/work-orders/${id}" class="inline" data-confirm="¿Eliminar esta orden de trabajo? Los presupuestos volverán a estado aprobado.">
                                @csrf @method('DELETE')
                                <button type="submit" title="Eliminar orden de trabajo" class="btn-icon btn-icon-red">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                            </form>
                        </div>`;
                    }
                }
            ]
        });

        document.getElementById('work-order-search').addEventListener('input', function(e) {
            table.setData("{{ route('api.work-orders.search') }}?q=" + encodeURIComponent(e.target.value) + "&limit=100");
        });
    </script>
    @endpush
</x-app-layout>
