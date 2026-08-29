<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Órdenes de Compra') }}</h2>
            <a href="{{ route('purchase-orders.create') }}" class="btn btn-primary">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                </svg>
                Nueva OC
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="mb-4 px-4 py-3 bg-green-50 border border-green-200 text-green-700 rounded-lg text-sm">{{ session('success') }}</div>
            @endif
            @if ($errors->any())
                <div class="mb-4 px-4 py-3 bg-red-50 border border-red-200 text-red-700 rounded-lg text-sm">{{ $errors->first() }}</div>
            @endif

            <div class="card overflow-hidden">
                <div class="p-4 sm:p-5">
                    <div class="flex flex-col sm:flex-row sm:items-center gap-3 mb-3">
                        <div class="relative max-w-md flex-1">
                            <svg class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                            <input type="text" id="po-search" placeholder="Buscar por documento o proveedor..." class="search-input">
                        </div>
                        <select id="po-status" class="rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                            <option value="">Todos los estados</option>
                            <option value="draft">Borrador</option>
                            <option value="ordered">Pedido</option>
                            <option value="received">Recibida</option>
                            <option value="cancelled">Anulada</option>
                        </select>
                    </div>
                    <div id="po-table" class="data-table"></div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        const statusBadge = (cell) => {
            const v = cell.getValue();
            const map = {
                draft: ['Borrador', 'bg-gray-100 text-gray-600'],
                ordered: ['Pedido', 'bg-blue-50 text-blue-700'],
                received: ['Recibida', 'bg-green-50 text-green-700'],
                cancelled: ['Anulada', 'bg-red-50 text-red-700'],
            };
            const [label, cls] = map[v] || [v, 'bg-gray-100 text-gray-600'];
            return `<span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium ${cls}">${label}</span>`;
        };

        const table = new Tabulator('#po-table', {
            ajaxURL: "{{ route('api.purchase-orders.search') }}?limit=100",
            layout: 'fitColumns',
            responsiveLayout: 'collapse',
            placeholder: 'No hay órdenes de compra registradas',
            height: 'auto',
            columns: [
                { title: 'Documento', field: 'document_sn', minWidth: 150, formatter: (cell) => `<span class="font-mono font-semibold text-gray-800">${cell.getValue() || '—'}</span>` },
                { title: 'Proveedor', field: 'provider', minWidth: 180 },
                { title: 'Almacén', field: 'warehouse', width: 130 },
                { title: 'Fecha', field: 'order_date', width: 110 },
                { title: 'Estado', field: 'status', width: 110, hozAlign: 'center', formatter: statusBadge },
                { title: 'Total', field: 'total', width: 110, hozAlign: 'right', formatter: cell => `${cell.getValue()} ${cell.getData().currency}` },
                { title: '', field: 'id', width: 70, hozAlign: 'center', headerSort: false,
                  formatter: cell => `<a href="{{ url('purchase-orders') }}/${cell.getValue()}" class="btn-icon btn-icon-blue" title="Ver"><svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0zm2.5-1.5a7.5 7.5 0 010 3 7.5 7.5 0 01-15 0 7.5 7.5 0 0115-3z"/></svg></a>` }
            ]
        });

        document.getElementById('po-search').addEventListener('input', (e) => {
            table.setData(`{{ route('api.purchase-orders.search') }}?q=${encodeURIComponent(e.target.value)}&limit=100`);
        });
        document.getElementById('po-status').addEventListener('change', (e) => {
            table.setData(`{{ route('api.purchase-orders.search') }}?status=${encodeURIComponent(e.target.value)}&limit=100`);
        });
    </script>
    @endpush
</x-app-layout>
