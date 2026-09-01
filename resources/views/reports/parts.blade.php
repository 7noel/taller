<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap justify-between items-center gap-3">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Repuestos Utilizados</h2>
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
            <form method="GET" action="{{ route('reports.parts') }}" class="card p-4 mb-4">
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-3 items-end">
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
                    <div>
                        <label for="brand_id" class="block text-xs font-semibold uppercase tracking-wide text-gray-500 mb-1">Marca de vehículo</label>
                        <select name="brand_id" id="brand_id" class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="">Todas</option>
                            @foreach ($brands as $b)
                                <option value="{{ $b->id }}" @selected(request('brand_id') == $b->id)>{{ $b->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex items-center gap-2 col-span-2 md:col-span-1">
                        <button type="submit" class="btn btn-primary">Filtrar</button>
                        <a href="{{ route('reports.parts') }}" class="btn btn-secondary">Limpiar</a>
                    </div>
                </div>
            </form>

            <div id="report-kpis" class="grid grid-cols-2 lg:grid-cols-4 gap-3 mb-4"></div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mb-4">
                <div class="card p-4 sm:p-5 lg:col-span-2">
                    <h3 class="mb-2 text-sm font-semibold text-gray-700">Top 12 repuestos por unidades</h3>
                    <div id="chart-top"></div>
                </div>
                <div class="card p-4 sm:p-5">
                    <h3 class="mb-2 text-sm font-semibold text-gray-700">Unidades por categoría</h3>
                    <div id="chart-categories"></div>
                </div>
                <div class="card p-4 sm:p-5 lg:col-span-3">
                    <h3 class="mb-2 text-sm font-semibold text-gray-700">Unidades por marca de vehículo</h3>
                    <div id="chart-brands"></div>
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
                placeholder: 'No hay líneas de repuestos para los filtros seleccionados',
                pagination: true,
                paginationSize: 10,
                paginationSizeSelector: [10, 25, 50],
                columns: [
                    { title: 'Repuesto', field: 'name' },
                    { title: 'Categoría', field: 'category', width: 140 },
                    { title: '# Veces', field: 'lines', width: 80, hozAlign: 'right', sorter: 'number' },
                    { title: 'Unidades', field: 'units', width: 90, hozAlign: 'right', sorter: 'number' },
                    { title: 'Costo', field: 'cost', width: 100, hozAlign: 'right', sorter: 'number', formatter: (cell) => money(cell.getValue()) },
                    { title: 'Venta', field: 'sale', width: 100, hozAlign: 'right', sorter: 'number', formatter: (cell) => money(cell.getValue()) },
                ],
            });

            async function loadReport() {
                const res = await fetch("{{ route('api.reports.parts') }}?" + buildQuery(), { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
                const data = await res.json();

                renderKpis(data.kpis);

                renderChart('chart-top', Object.assign(baseBar(), {
                    colors: ['#2563eb'],
                    series: [{ name: 'Unidades', data: data.series.top.map(t => t.units) }],
                    xaxis: { categories: data.series.top.map(t => t.name) },
                }));
                renderChart('chart-categories', Object.assign(baseDonut(), {
                    labels: data.series.categories.map(c => c.name),
                    series: data.series.categories.map(c => c.units),
                }));
                renderChart('chart-brands', Object.assign(baseBar(), {
                    colors: ['#10b981'],
                    series: [{ name: 'Unidades', data: data.series.brands.map(b => b.units) }],
                    xaxis: { categories: data.series.brands.map(b => b.name) },
                }));

                table.setData(data.rows);
            }

            loadReport();

            document.getElementById('btn-export').addEventListener('click', () => table.download('csv', 'repuestos-utilizados.csv'));
            document.getElementById('btn-print').addEventListener('click', () => window.print());
        </script>
    @endpush
</x-app-layout>