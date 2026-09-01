<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap justify-between items-center gap-3">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Ingresos y Cobranza</h2>
            <div class="flex flex-wrap items-center gap-2">
                <a href="{{ route('reports.index') }}" class="btn btn-secondary">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    Todos los reportes
                </a>
                <button type="button" id="btn-print" class="btn btn-secondary">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4H7v4a2 2 0 002 2z"/></svg>
                    Imprimir
                </button>
                <button type="button" id="btn-export" class="btn btn-primary">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    Exportar CSV
                </button>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <form method="GET" action="{{ route('reports.revenue') }}" class="card p-4 mb-4">
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-3 items-end">
                    <div>
                        <label for="from" class="block text-xs font-semibold uppercase tracking-wide text-gray-500 mb-1">Desde</label>
                        <input type="date" name="from" id="from" value="{{ request('from') }}" class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>
                    <div>
                        <label for="to" class="block text-xs font-semibold uppercase tracking-wide text-gray-500 mb-1">Hasta</label>
                        <input type="date" name="to" id="to" value="{{ request('to') }}" class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>
                    @if ($establishments->count() > 1)
                    <div>
                        <label for="establishment_id" class="block text-xs font-semibold uppercase tracking-wide text-gray-500 mb-1">Establecimiento</label>
                        <select name="establishment_id" id="establishment_id" class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="">Todos</option>
                            @foreach ($establishments as $e)
                                <option value="{{ $e->id }}" @selected(request('establishment_id') == $e->id)>{{ $e->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    @endif
                    <div class="flex items-center gap-2 col-span-2 md:col-span-1">
                        <button type="submit" class="btn btn-primary">Filtrar</button>
                        <a href="{{ route('reports.revenue') }}" class="btn btn-secondary">Limpiar</a>
                    </div>
                </div>
            </form>

            <div id="report-kpis" class="grid grid-cols-2 lg:grid-cols-4 gap-3 mb-4"></div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mb-4">
                <div class="card p-4 sm:p-5 lg:col-span-2">
                    <h3 class="mb-2 text-sm font-semibold text-gray-700">Facturado vs cobrado por mes</h3>
                    <div id="chart-monthly"></div>
                </div>
                <div class="card p-4 sm:p-5">
                    <h3 class="mb-2 text-sm font-semibold text-gray-700">Por tipo de comprobante</h3>
                    <div id="chart-types"></div>
                </div>
                <div class="card p-4 sm:p-5 lg:col-span-3">
                    <h3 class="mb-2 text-sm font-semibold text-gray-700">Top 10 clientes por facturación</h3>
                    <div id="chart-parties"></div>
                </div>
            </div>

            <div class="card overflow-hidden">
                <div class="p-4 sm:p-5">
                    <div id="report-table" class="data-table"></div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        @include('reports._helpers')
        <script>
            const table = new Tabulator('#report-table', {
                layout: 'fitColumns',
                responsiveLayout: 'collapse',
                height: 'auto',
                placeholder: 'No hay comprobantes para los filtros seleccionados',
                pagination: true,
                paginationSize: 10,
                paginationSizeSelector: [10, 25, 50],
                columns: [
                    {
                        title: 'Comprobante', field: 'document_sn', width: 140,
                        formatter: (cell) => {
                            const d = cell.getData();
                            return `${cell.getValue()}<span class="ml-1 text-[10px] font-semibold text-gray-400">· ${d.doc_type}</span>`;
                        },
                    },
                    { title: 'Fecha', field: 'date', width: 95, hozAlign: 'center' },
                    { title: 'Cliente', field: 'party' },
                    { title: 'Tipo', field: 'invoice_type', width: 100 },
                    { title: 'Total', field: 'total', width: 100, hozAlign: 'right', sorter: 'number', formatter: (cell) => money(cell.getValue()) },
                    { title: 'Pagado', field: 'paid', width: 100, hozAlign: 'right', sorter: 'number', formatter: (cell) => money(cell.getValue()) },
                    {
                        title: 'Saldo', field: 'balance', width: 100, hozAlign: 'right', sorter: 'number',
                        formatter: (cell) => {
                            const v = Number(cell.getValue() || 0);
                            return v > 0 ? `<span class="text-red-600">${money(v)}</span>` : money(v);
                        },
                    },
                    {
                        title: 'Estado', field: 'status_label', width: 110,
                        formatter: (cell) => {
                            const s = cell.getData().status;
                            const map = {
                                voided: 'bg-red-50 text-red-700',
                                rejected: 'bg-red-50 text-red-700',
                                draft: 'bg-gray-100 text-gray-600',
                                emitted: 'bg-blue-50 text-blue-700',
                                accepted: 'bg-green-50 text-green-700',
                            };
                            return `<span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-semibold ${map[s] || 'bg-gray-100 text-gray-600'}">${cell.getValue()}</span>`;
                        },
                    },
                ],
            });

            async function loadReport() {
                const res = await fetch("{{ route('api.reports.revenue') }}?" + buildQuery(), { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
                const data = await res.json();

                renderKpis(data.kpis);

                renderChart('chart-monthly', Object.assign(baseLine(), {
                    colors: ['#2563eb', '#10b981'],
                    series: [
                        { name: 'Facturado', data: data.series.monthly.map(m => m.facturado) },
                        { name: 'Cobrado', data: data.series.monthly.map(m => m.cobrado) },
                    ],
                    xaxis: { categories: data.series.monthly.map(m => m.name) },
                    yaxis: { labels: { style: { colors: '#6b7280', fontSize: '11px' }, formatter: (v) => 'S/ ' + Number(v || 0).toLocaleString('es-PE') } },
                }));
                renderChart('chart-types', Object.assign(baseDonut(), {
                    labels: data.series.types.map(t => t.name),
                    series: data.series.types.map(t => t.total),
                    tooltip: { theme: 'light', y: { formatter: (v) => 'S/ ' + Number(v || 0).toLocaleString('es-PE') } },
                }));
                renderChart('chart-parties', Object.assign(baseBar(), {
                    colors: ['#f59e0b'],
                    series: [{ name: 'Facturación', data: data.series.parties.map(p => p.total) }],
                    xaxis: { categories: data.series.parties.map(p => p.name) },
                }));

                table.setData(data.rows);
            }

            loadReport();

            document.getElementById('btn-export').addEventListener('click', () => table.download('csv', 'ingresos-cobranza.csv'));
            document.getElementById('btn-print').addEventListener('click', () => window.print());
        </script>
    @endpush
</x-app-layout>