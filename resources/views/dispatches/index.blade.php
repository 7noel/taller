<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Guías de Remisión') }}</h2>
            <a href="{{ route('dispatches.create') }}" class="btn btn-primary">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                Nueva Guía
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
                    <div class="mb-3 relative max-w-md">
                        <svg class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        <input type="text" id="dispatch-search" placeholder="Buscar por serie o destinatario..." class="search-input">
                    </div>
                    <div id="dispatch-table" class="data-table"></div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        const dispatchBadge = (status, label) => {
            const map = { draft: 'bg-gray-100 text-gray-600', emitted: 'bg-blue-50 text-blue-700', accepted: 'bg-green-50 text-green-700', rejected: 'bg-red-50 text-red-700', voided: 'bg-gray-200 text-gray-500' };
            return `<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${map[status] || 'bg-gray-100 text-gray-600'}">${label}</span>`;
        };

        const dispatchTable = new Tabulator('#dispatch-table', {
            ajaxURL: "{{ route('api.dispatches.search') }}?limit=200",
            layout: 'fitColumns', responsiveLayout: 'collapse', height: 'auto', placeholder: 'No hay guías registradas',
            columns: [
                { title: 'Guía', field: 'document_sn', width: 160, formatter: (cell) => `<a href="/dispatches/${cell.getData().id}" class="font-medium text-blue-600 hover:text-blue-800">${cell.getData().document_sn || 'Borrador'}</a>` },
                { title: 'Tipo', field: 'type_label', width: 130 },
                { title: 'Destinatario', field: 'party_name' },
                { title: 'Motivo', field: 'motivo', width: 160 },
                { title: 'Fecha', field: 'fecha', width: 110 },
                { title: 'Factura', field: 'invoice_sn', width: 130 },
                { title: 'Estado', field: 'status', width: 110, hozAlign: 'center', formatter: (cell) => dispatchBadge(cell.getData().status, cell.getData().status_label) },
                { title: '', field: 'id', width: 60, hozAlign: 'center', headerSort: false, formatter: (cell) => {
                    const d = cell.getData();
                    let html = `<a href="/dispatches/${d.id}" title="Ver guía" class="btn-icon btn-icon-blue"><svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg></a>`;
                    if (d.status === 'draft') {
                        html += `<form method="POST" action="/dispatches/${d.id}/emit" class="inline">@csrf<button type="submit" title="Emitir" class="btn-icon btn-icon-amber"><svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg></button></form>`;
                    }
                    return `<div class="flex gap-1.5 justify-center">${html}</div>`;
                }}
            ]
        });

        document.getElementById('dispatch-search').addEventListener('input', (e) => {
            dispatchTable.setData("{{ route('api.dispatches.search') }}?limit=200&q=" + encodeURIComponent(e.target.value));
        });
    </script>
    @endpush
</x-app-layout>
