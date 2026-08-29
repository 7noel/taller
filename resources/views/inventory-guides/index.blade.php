<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Guías de Inventario') }}</h2>
            <a href="{{ route('inventory-guides.create') }}" class="btn btn-primary">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                </svg>
                Nueva guía
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
                    <div class="flex flex-col sm:flex-row sm:items-center gap-3 mb-3">
                        <div class="relative max-w-md flex-1">
                            <svg class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                            <input type="text" id="guide-search" placeholder="Buscar por documento..." class="search-input">
                        </div>
                        <select id="guide-type" class="rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                            <option value="">Todos los tipos</option>
                            <option value="U2">NIA1 · Ingreso</option>
                            <option value="U3">NSA1 · Salida</option>
                            <option value="U4">NTA1 · Transferencia</option>
                        </select>
                        <input type="date" id="guide-from" class="rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                        <input type="date" id="guide-to" class="rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                    </div>
                    <div id="guide-table" class="data-table"></div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        const typeBadge = (cell) => {
            const v = cell.getValue();
            const map = {
                U2: ['NIA1 · Ingreso', 'bg-green-50 text-green-700'],
                U3: ['NSA1 · Salida', 'bg-red-50 text-red-700'],
                U4: ['NTA1 · Transferencia', 'bg-blue-50 text-blue-700'],
            };
            const [label, cls] = map[v] || [v, 'bg-gray-100 text-gray-600'];
            return `<span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium ${cls}">${label}</span>`;
        };

        const table = new Tabulator('#guide-table', {
            ajaxURL: "{{ route('api.inventory-guides.search') }}?limit=100",
            layout: 'fitColumns',
            responsiveLayout: 'collapse',
            placeholder: 'No hay guías registradas',
            height: 'auto',
            columns: [
                { title: 'Documento', field: 'document_sn', minWidth: 150, formatter: (cell) => `<span class="font-mono font-semibold text-gray-800">${cell.getValue()}</span>` },
                { title: 'Tipo', field: 'type_code', width: 130, hozAlign: 'center', formatter: typeBadge },
                { title: 'Motivo', field: 'reason', minWidth: 200 },
                { title: 'Origen', field: 'origin', width: 130 },
                { title: 'Destino', field: 'destination', width: 130 },
                { title: 'Referencia', field: 'ref', width: 140, formatter: cell => cell.getValue() || '—' },
                { title: 'Fecha', field: 'movement_date', width: 100 },
                { title: '', field: 'id', width: 70, hozAlign: 'center', headerSort: false,
                  formatter: cell => `<a href="{{ url('inventory-guides') }}/${cell.getValue()}" class="btn-icon btn-icon-blue" title="Ver"><svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0zm2.5-1.5a7.5 7.5 0 010 3 7.5 7.5 0 01-15 0 7.5 7.5 0 0115-3z"/></svg></a>` }
            ]
        });

        function loadGuides() {
            const params = new URLSearchParams({ limit: 100 });
            const q = document.getElementById('guide-search').value;
            const type = document.getElementById('guide-type').value;
            const from = document.getElementById('guide-from').value;
            const to = document.getElementById('guide-to').value;
            if (q) params.set('q', q);
            if (type) params.set('document_type_code', type);
            if (from) params.set('from', from);
            if (to) params.set('to', to);
            table.setData("{{ route('api.inventory-guides.search') }}?" + params.toString());
        }
        document.getElementById('guide-search').addEventListener('input', loadGuides);
        document.getElementById('guide-type').addEventListener('change', loadGuides);
        document.getElementById('guide-from').addEventListener('change', loadGuides);
        document.getElementById('guide-to').addEventListener('change', loadGuides);
    </script>
    @endpush
</x-app-layout>
