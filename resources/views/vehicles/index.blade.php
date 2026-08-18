<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Vehículos') }}</h2>
            <a href="{{ route('vehicles.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 rounded-md font-semibold text-xs text-white uppercase hover:bg-blue-700">+ Crear Vehículo</a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="mb-4 px-4 py-3 bg-green-100 border border-green-400 text-green-700 rounded">{{ session('success') }}</div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="mb-4">
                        <input type="text" id="vehicle-search" placeholder="Buscar por placa, marca, modelo o propietario..." class="w-full sm:w-96 rounded-md border-gray-300">
                    </div>
                    <div id="vehicle-table"></div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        const table = new Tabulator('#vehicle-table', {
            ajaxURL: "{{ route('api.vehicles.search') }}?limit=100",
            layout: 'fitColumns',
            responsiveLayout: 'collapse',
            placeholder: 'No hay vehículos registrados',
            height: 'auto',
            columns: [
                { title: 'Placa', field: 'plate', width: 100, hozAlign: 'center' },
                { title: 'Marca', field: 'brand', width: 120 },
                { title: 'Modelo', field: 'model', width: 140 },
                { title: 'Propietario', field: 'owner_name', minWidth: 180 },
                { title: 'Año', field: 'year', width: 80, hozAlign: 'center' },
                { title: 'Acciones', field: 'actions', width: 180, hozAlign: 'center',
                  formatter: function(cell) {
                    const id = cell.getData().id;
                    return `<div class="flex gap-2 justify-center">
                        <a href="/vehicles/${id}" class="text-blue-600">Ver</a>
                        <a href="/vehicles/${id}/edit" class="text-yellow-600">Editar</a>
                        <form method="POST" action="/vehicles/${id}" class="inline" onsubmit="return confirm('¿Eliminar este vehículo?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-red-600">Eliminar</button>
                        </form>
                    </div>`;
                  }
                }
            ]
        });

        document.getElementById('vehicle-search').addEventListener('input', function(e) {
            table.setData("{{ route('api.vehicles.search') }}?q=" + encodeURIComponent(e.target.value) + "&limit=100");
        });
    </script>
    @endpush
</x-app-layout>