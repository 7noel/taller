<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Repuestos') }}</h2>
            <a href="{{ route('parts.create') }}" class="btn btn-primary">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                </svg>
                Nuevo Repuesto
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
                            <input type="text" id="part-search" placeholder="Buscar por SKU, nombre, marca..." class="search-input">
                        </div>
                    </div>
                    <div id="part-table" class="data-table"></div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        const table = new Tabulator('#part-table', {
            ajaxURL: "{{ route('api.parts.search') }}?limit=100",
            layout: 'fitColumns',
            responsiveLayout: 'collapse',
            placeholder: 'No hay repuestos registrados',
            height: 'auto',
            columns: [
                { title: 'SKU', field: 'sku', width: 110 },
                { title: 'Nombre', field: 'name', minWidth: 180 },
                { title: 'Marca', field: 'brand', width: 120 },
                { title: 'Categoría', field: 'category', width: 120 },
                { title: 'P. Venta', field: 'sell_price', width: 110, hozAlign: 'right',
                  formatter: cell => `${cell.getValue()} ${cell.getData().currency}` },
                { title: 'Stock', field: 'stock', width: 90, hozAlign: 'right' },
                { title: 'Activo', field: 'is_active', width: 90, hozAlign: 'center',
                  formatter: cell => cell.getValue()
                    ? '<span class="px-2 py-0.5 rounded-full text-xs font-medium bg-green-50 text-green-700">Sí</span>'
                    : '<span class="px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-500">No</span>' },
                {
                    title: 'Acciones', field: 'id', width: 140, hozAlign: 'center', headerSort: false,
                    formatter: function(cell) {
                        const id = cell.getData().id;
                        return `<div class="flex gap-2 justify-center">
                            <a href="/parts/${id}/edit" title="Editar repuesto" class="btn-icon btn-icon-amber">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                            </a>
                            <form method="POST" action="/parts/${id}" class="inline" data-confirm="¿Eliminar este repuesto?">
                                @csrf @method('DELETE')
                                <button type="submit" title="Eliminar repuesto" class="btn-icon btn-icon-red">
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

        document.getElementById('part-search').addEventListener('input', function(e) {
            table.setData("{{ route('api.parts.search') }}?q=" + encodeURIComponent(e.target.value) + "&limit=100");
        });
    </script>
    @endpush
</x-app-layout>