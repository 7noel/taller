<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Comprobantes') }}</h2>
            <a href="{{ route('invoices.create') }}" class="btn btn-primary">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                </svg>
                Nueva Factura
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="mb-4 px-4 py-3 bg-green-50 border border-green-200 text-green-700 rounded-lg text-sm">
                    {{ session('success') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="mb-4 px-4 py-3 bg-red-50 border border-red-200 text-red-700 rounded-lg text-sm">
                    {{ $errors->first() }}
                </div>
            @endif

            <div class="card overflow-hidden">
                <div class="p-4 sm:p-5">
                    <div class="mb-3 flex flex-col md:flex-row md:items-end gap-3">
                        <div class="relative max-w-md flex-1">
                            <svg class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                            <input type="text" id="invoice-search" placeholder="Buscar por serie, receptor..." class="search-input">
                        </div>
                    </div>
                    <div id="invoice-table" class="data-table"></div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        const invoiceBadge = (status, label) => {
            const map = {
                draft: 'bg-gray-100 text-gray-600',
                pending: 'bg-amber-50 text-amber-700',
                emitted: 'bg-blue-50 text-blue-700',
                accepted: 'bg-green-50 text-green-700',
                rejected: 'bg-red-50 text-red-700',
                voided: 'bg-gray-200 text-gray-500'
            };
            return `<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${map[status] || 'bg-gray-100 text-gray-600'}">${label}</span>`;
        };

        const invoiceTable = new Tabulator('#invoice-table', {
            ajaxURL: "{{ route('api.invoices.search') }}?limit=200",
            layout: 'fitColumns',
            responsiveLayout: 'collapse',
            height: 'auto',
            placeholder: 'No hay comprobantes registrados',
            columns: [
                { title: 'Documento', field: 'document_sn', width: 160, formatter: (cell) => {
                    const d = cell.getData();
                    return `<a href="/invoices/${d.id}" class="font-medium text-blue-600 hover:text-blue-800">${d.document_sn || 'Borrador'}</a>`;
                }},
                { title: 'Tipo', field: 'doc_type_label', width: 110, formatter: (cell) => {
                    const d = cell.getData();
                    return `<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-50 text-indigo-700">${d.doc_type_label}</span>`;
                }},
                { title: 'Clasificación', field: 'type_label', width: 120 },
                { title: 'Receptor', field: 'party_name' },
                { title: 'Fecha', field: 'invoice_date', width: 110 },
                { title: 'Presupuestos', field: 'estimate_sns', width: 150, formatter: 'textarea' },
                { title: 'Total', field: 'total', width: 110, hozAlign: 'right' },
                { title: 'Estado', field: 'status', width: 110, hozAlign: 'center', formatter: (cell) => invoiceBadge(cell.getData().status, cell.getData().status_label) },
                { title: '', field: 'id', width: 60, hozAlign: 'center', headerSort: false,
                    formatter: (cell) => {
                        const d = cell.getData();
                        let html = `<a href="/invoices/${d.id}" title="Ver comprobante" class="btn-icon btn-icon-blue">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                        </a>`;
                        if (d.status === 'draft') {
                            html += `<form method="POST" action="/invoices/${d.id}/emit" class="inline">
                                @csrf
                                <button type="submit" title="Emitir comprobante" class="btn-icon btn-icon-amber">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                                </button>
                            </form>`;
                        }
                        return `<div class="flex gap-1.5 justify-center">${html}</div>`;
                    }
                }
            ]
        });

        document.getElementById('invoice-search').addEventListener('input', (e) => {
            invoiceTable.setData("{{ route('api.invoices.search') }}?limit=200&q=" + encodeURIComponent(e.target.value));
        });
    </script>
    @endpush
</x-app-layout>

