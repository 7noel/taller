<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Gestión de Usuarios') }}</h2>
            @can('crear usuarios')
                <a href="{{ route('users.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 rounded-md font-semibold text-xs text-white uppercase hover:bg-blue-700">+ Nuevo Usuario</a>
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
                    <div class="mb-4">
                        <input type="text" id="user-search" placeholder="Buscar por nombre, email o teléfono..." class="w-full sm:w-96 rounded-md border-gray-300">
                    </div>
                    <div id="user-table"></div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        const table = new Tabulator('#user-table', {
            ajaxURL: "{{ route('api.users.data') }}?limit=100",
            layout: 'fitColumns',
            responsiveLayout: 'collapse',
            placeholder: 'No hay usuarios registrados',
            height: 'auto',
            columns: [
                { title: 'ID', field: 'id', width: 60, hozAlign: 'center' },
                { title: 'Nombre', field: 'name', width: 180 },
                { title: 'Email', field: 'email', minWidth: 200 },
                { title: 'Teléfono', field: 'phone', width: 130 },
                { title: 'Establecimiento', field: 'establishment', width: 150 },
                { title: 'Roles', field: 'roles', minWidth: 160 },
                {
                    title: 'Acciones',
                    field: 'id',
                    width: 140,
                    hozAlign: 'center',
                    formatter: function(cell) {
                        const id = cell.getData().id;
                        return `<div class="flex gap-2 justify-center">
                            <a href="/users/${id}" title="Ver usuario" class="inline-flex h-8 w-8 items-center justify-center rounded-md bg-blue-100 text-blue-600 hover:bg-blue-200">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                            </a>
                            <a href="/users/${id}/edit" title="Editar usuario" class="inline-flex h-8 w-8 items-center justify-center rounded-md bg-yellow-100 text-yellow-700 hover:bg-yellow-200">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                            </a>
                            <form method="POST" action="/users/${id}" class="inline" onsubmit="return confirm('¿Eliminar este usuario?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" title="Eliminar usuario" class="inline-flex h-8 w-8 items-center justify-center rounded-md bg-red-100 text-red-600 hover:bg-red-200">
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

        document.getElementById('user-search').addEventListener('input', function(e) {
            table.setData("{{ route('api.users.data') }}?q=" + encodeURIComponent(e.target.value) + "&limit=100");
        });
    </script>
    @endpush
</x-app-layout>