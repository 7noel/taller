<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Marcas de Vehículos</h2>
            <a href="{{ route('brands.create') }}" class="btn btn-primary">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                </svg>
                Nueva marca
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="mb-4 px-4 py-3 bg-green-50 border border-green-200 text-green-700 rounded-lg text-sm">{{ session('success') }}</div>
            @endif
            @if (session('warning'))
                <div class="mb-4 px-4 py-3 bg-amber-50 border border-amber-200 text-amber-700 rounded-lg text-sm">
                    @foreach ((array) session('warning') as $warning)
                        <p>{{ $warning }}</p>
                    @endforeach
                </div>
            @endif
            @if (session('error'))
                <div class="mb-4 px-4 py-3 bg-red-50 border border-red-200 text-red-700 rounded-lg text-sm">{{ session('error') }}</div>
            @endif

            <div class="card overflow-hidden">
                <div class="p-4 sm:p-5">
                    <div class="mb-3">
                        <div class="relative max-w-md">
                            <svg class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                            <input type="text" id="brand-search" placeholder="Buscar marca o modelo..." class="search-input">
                        </div>
                    </div>
                    <div id="brand-table" class="data-table"></div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        const baseUrl = "{{ route('brands.index') }}";

        function modelsBadge(cell) {
            const n = cell.getValue() || 0;
            const color = n === 0 ? 'bg-gray-100 text-gray-600' : 'bg-blue-50 text-blue-700';
            return `<span class="inline-flex px-2 py-0.5 rounded-full text-xs font-medium ${color}">${n} ${n === 1 ? 'modelo' : 'modelos'}</span>`;
        }

        const table = new Tabulator('#brand-table', {
            ajaxURL: "{{ $searchUrl }}&limit=100",
            layout: 'fitColumns',
            responsiveLayout: 'collapse',
            placeholder: 'No hay marcas registradas',
            height: 'auto',
            columns: [
                { title: 'Marca', field: 'name', minWidth: 180 },
                { title: 'Modelos', field: 'models_count', width: 130, hozAlign: 'center', formatter: modelsBadge },
                { title: 'Vehículos', field: 'vehicles_count', width: 100, hozAlign: 'center', formatter: cell => cell.getValue() ?? 0 },
                {
                    title: 'Acciones',
                    field: 'id',
                    width: 110,
                    hozAlign: 'center',
                    headerSort: false,
                    formatter: function(cell) {
                        const id = cell.getData().id;
                        const name = String(cell.getData().name ?? '').replace(/&/g, '&amp;').replace(/"/g, '&quot;').replace(/</g, '&lt;');
                        return `<div class="flex gap-2 justify-center">
                            <a href="${baseUrl}/${id}/edit" title="Editar marca y sus modelos" class="btn-icon btn-icon-amber">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                            </a>
                            <form method="POST" action="${baseUrl}/${id}" class="inline" data-confirm="¿Eliminar la marca ${name} y todos sus modelos?">
                                @csrf @method('DELETE')
                                <button type="submit" title="Eliminar" class="btn-icon btn-icon-red">
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

        document.getElementById('brand-search').addEventListener('input', function(e) {
            table.setData("{{ $searchUrl }}&q=" + encodeURIComponent(e.target.value) + "&limit=100");
        });
    </script>
    @endpush
</x-app-layout>
