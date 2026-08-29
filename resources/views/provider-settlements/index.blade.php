<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Liquidaciones de Servicios Tercerizados') }}</h2>
            @can('crear liquidaciones de servicios')
            <a href="{{ route('provider-settlements.create') }}" class="btn btn-primary">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                Nueva Liquidación
            </a>
            @endcan
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="mb-4 px-4 py-3 bg-green-50 border border-green-200 text-green-700 rounded-lg text-sm">{{ session('success') }}</div>
            @endif
            @if (session('error'))
                <div class="mb-4 px-4 py-3 bg-red-50 border border-red-200 text-red-700 rounded-lg text-sm">{{ session('error') }}</div>
            @endif
            @if ($errors->any())
                <div class="mb-4 px-4 py-3 bg-red-50 border border-red-200 text-red-700 rounded-lg text-sm">{{ $errors->first() }}</div>
            @endif

            <div class="card overflow-hidden">
                <div class="p-4 sm:p-5">
                    <div class="mb-3">
                        <div class="relative max-w-md">
                            <svg class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                            <input type="text" id="settlement-search" placeholder="Buscar por serie o proveedor..." class="search-input">
                        </div>
                    </div>
                    <div id="provider-settlement-table" class="data-table"></div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        const statusColors = {
            'draft': 'bg-gray-100 text-gray-800',
            'approved': 'bg-blue-100 text-blue-800',
            'paid': 'bg-green-100 text-green-800'
        };
        const statusLabel = (s) => ({ 'draft': 'Borrador', 'approved': 'Aprobado', 'paid': 'Pagado' }[s] || s || '');
        const money = (v) => 'S/ ' + Number(v || 0).toFixed(2);

        const table = new Tabulator('#provider-settlement-table', {
            ajaxURL: "{{ route('api.provider-settlements.search') }}?limit=100",
            layout: 'fitColumns',
            responsiveLayout: 'collapse',
            placeholder: 'No hay liquidaciones de servicios tercerizados registradas',
            height: 'auto',
            columns: [
                {
                    title: 'Documento', field: 'document_sn', width: 130, hozAlign: 'center',
                    formatter: function(cell) {
                        return `<a href="/provider-settlements/${cell.getData().id}" class="font-semibold text-blue-600 hover:text-blue-800">${cell.getValue()}</a>`;
                    }
                },
                { title: 'Proveedor', field: 'provider_name', minWidth: 160 },
                { title: 'Período', field: 'period', minWidth: 170 },
                { title: 'Vales', field: 'vouchers_count', width: 70, hozAlign: 'center' },
                { title: 'Total c/ IGV', field: 'total_with_igv', width: 110, hozAlign: 'right', formatter: cell => money(cell.getValue()) },
                { title: 'Detracción', field: 'detraction_amount', width: 105, hozAlign: 'right', formatter: cell => money(cell.getValue()) },
                { title: 'Total a pagar', field: 'total_payable', width: 115, hozAlign: 'right', formatter: cell => `<span class="font-semibold">${money(cell.getValue())}</span>` },
                {
                    title: 'Estado', field: 'status', width: 105, hozAlign: 'center',
                    formatter: function(cell) {
                        const s = cell.getValue();
                        return `<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${statusColors[s] || 'bg-gray-100 text-gray-800'}">${statusLabel(s)}</span>`;
                    }
                },
                {
                    title: 'Acciones', field: 'id', width: 120, hozAlign: 'center', headerSort: false,
                    formatter: function(cell) {
                        const d = cell.getData();
                        return `<div class="flex gap-1 justify-center">
                            <a href="/provider-settlements/${d.id}" title="Ver liquidación" class="btn-icon btn-icon-blue">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            </a>
                            <a href="/provider-settlements/${d.id}/edit" title="Editar liquidación" class="btn-icon btn-icon-amber">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            </a>
                            <form method="POST" action="/provider-settlements/${d.id}" class="inline" data-confirm="¿Eliminar esta liquidación? Los comprobantes quedarán disponibles.">
                                @csrf @method('DELETE')
                                <button type="submit" title="Eliminar liquidación" class="btn-icon btn-icon-red">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </form>
                        </div>`;
                    }
                }
            ]
        });

        document.getElementById('settlement-search').addEventListener('input', function(e) {
            table.setData("{{ route('api.provider-settlements.search') }}?q=" + encodeURIComponent(e.target.value) + "&limit=100");
        });
    </script>
    @endpush
</x-app-layout>
