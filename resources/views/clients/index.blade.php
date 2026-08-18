<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Clientes') }}
            </h2>
            <a href="{{ route('clients.create') }}"
               class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                + Crear Cliente
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="mb-4 px-4 py-3 bg-green-100 border border-green-400 text-green-700 rounded">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="mb-4">
                        <input type="text" id="client-search"
                               placeholder="Buscar por nombre o documento..."
                               class="w-full sm:w-96 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>
                    <div id="client-table"></div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        const table = new Tabulator('#client-table', {
            ajaxURL: "{{ route('api.clients.search') }}?limit=100",
            layout: 'fitColumns',
            responsiveLayout: 'collapse',
            placeholder: 'No hay clientes registrados',
            pagination: false,
            height: 'auto',
            columns: [
                { title: 'ID', field: 'id', width: 60, hozAlign: 'right' },
                { title: 'Razón Social', field: 'business_name', minWidth: 200 },
                { title: 'Documento', field: 'document_number', width: 130 },
                { title: 'Teléfono', field: 'phone', width: 120 },
                { title: 'Email', field: 'email', minWidth: 180 },
                {
                    title: 'Acciones',
                    field: 'actions',
                    width: 180,
                    hozAlign: 'center',
                    formatter: function(cell) {
                        const id = cell.getData().id;
                        return `
                            <div class="flex gap-2 justify-center">
                                <a href="/clients/${id}" class="text-blue-600 hover:text-blue-800">Ver</a>
                                <a href="/clients/${id}/edit" class="text-yellow-600 hover:text-yellow-800">Editar</a>
                                <form method="POST" action="/clients/${id}" class="inline" onsubmit="return confirm('¿Eliminar este cliente?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-800">Eliminar</button>
                                </form>
                            </div>
                        `;
                    }
                }
            ]
        });

        document.getElementById('client-search').addEventListener('input', function(e) {
            const term = e.target.value;
            table.setData("{{ route('api.clients.search') }}?q=" + encodeURIComponent(term) + "&limit=100");
        });
    </script>
    @endpush
</x-app-layout>