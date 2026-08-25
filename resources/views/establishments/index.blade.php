@php
    $establishmentsData = $establishments->map(fn ($e) => [
        'id' => $e->id,
        'code' => $e->code,
        'name' => $e->name,
        'address' => $e->address,
        'phone' => $e->phone,
        'email' => $e->email,
        'series_count' => $e->document_series_count,
    ])->values();
@endphp

<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Establecimientos') }}</h2>
            @can('crear establecimientos')
                <a href="{{ route('establishments.create') }}" class="btn btn-primary">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                    </svg>
                    Crear Establecimiento
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

            <div class="card overflow-hidden">
                <div class="p-4 sm:p-5">
                    <div id="establishment-table" class="data-table"></div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
    const table = new Tabulator('#establishment-table', {
        layout: 'fitColumns',
        responsiveLayout: 'collapse',
        placeholder: 'No hay establecimientos registrados',
        height: 'auto',
        data: @json($establishmentsData),
        columns: [
            { title: 'Código', field: 'code', width: 90, hozAlign: 'center' },
            { title: 'Nombre', field: 'name', minWidth: 160 },
            { title: 'Dirección', field: 'address', minWidth: 180 },
            { title: 'Teléfono', field: 'phone', width: 120 },
            { title: 'Email', field: 'email', minWidth: 160 },
            { title: 'Series', field: 'series_count', width: 80, hozAlign: 'center' },
            {
                title: 'Acciones',
                field: 'id',
                width: 220,
                hozAlign: 'center',
                formatter: function(cell) {
                    const id = cell.getData().id;
                    return `<div class="flex gap-2 justify-center">
                        <a href="/establishments/${id}/series" title="Ver series" class="btn-icon btn-icon-blue">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                        </a>
                        <a href="/establishments/${id}/edit" title="Editar establecimiento" class="btn-icon btn-icon-amber">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        </a>
                        <form method="POST" action="/establishments/${id}" class="inline" data-confirm="¿Eliminar este establecimiento?">
                            @csrf @method('DELETE')
                            <button type="submit" title="Eliminar establecimiento" class="btn-icon btn-icon-red">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </button>
                        </form>
                    </div>`;
                }
            }
        ]
    });
    </script>
    @endpush
</x-app-layout>