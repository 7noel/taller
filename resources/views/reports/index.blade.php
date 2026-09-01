<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap justify-between items-center gap-3">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Centro de Reportes</h2>
        </div>
    </x-slot>

    @php
        $cards = [
            [
                'route' => route('reports.vehicles'),
                'title' => 'Frecuencia de Vehículos',
                'desc' => 'Marca, modelo y año que más ingresan al taller. Base para preparar repuestos y stock por tipo de vehículo.',
                'icon' => 'M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4',
            ],
            [
                'route' => route('reports.advisors'),
                'title' => 'Rentabilidad de Asesores',
                'desc' => 'Ventas, aprobación y utilidad real por asesor: quién genera mejor rentabilidad al taller.',
                'icon' => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z',
            ],
            [
                'route' => route('reports.profitability'),
                'title' => 'Costos y Utilidad',
                'desc' => 'Ingresos, costos por componente (repuestos, mano de obra, terceros) y utilidad de cada orden de trabajo.',
                'icon' => 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z',
            ],
            [
                'route' => route('reports.followups'),
                'title' => 'Seguimientos',
                'desc' => 'Cartera de seguimientos por tipo y asesor, pendientes, vencidos y tasa de cierre.',
                'icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z',
            ],
            [
                'route' => route('reports.revenue'),
                'title' => 'Ingresos y Cobranza',
                'desc' => 'Facturado vs cobrado por mes, saldos pendientes, tipos de comprobante y top clientes.',
                'icon' => 'M3 10h18M7 15h2m4 0h2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2z',
            ],
            [
                'route' => route('reports.parts'),
                'title' => 'Repuestos Utilizados',
                'desc' => 'Los repuestos que más se facturan, por categoría y por marca de vehículo, con costo y valor de venta.',
                'icon' => 'M11 17l-5 5m13-13a3 3 0 11-6 0 3 3 0 016 0zM5 21a2 2 0 100-4 2 2 0 000 4zm12-4a2 2 0 100-4 2 2 0 000 4z',
            ],
        ];
    @endphp

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <p class="mb-4 text-sm text-gray-600">Indicadores y análisis para la toma de decisiones del taller. Los montos se expresan en soles (PEN).</p>

            <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-4">
                @foreach ($cards as $card)
                <a href="{{ $card['route'] }}" class="card p-5 transition-all duration-150 ease-out hover:border-blue-300 hover:shadow group">
                    <div class="flex items-start justify-between">
                        <span class="inline-flex h-10 w-10 items-center justify-center rounded-lg bg-blue-50 text-blue-600">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="{{ $card['icon'] }}"/>
                            </svg>
                        </span>
                        <span class="text-xs font-semibold text-gray-400 transition-colors duration-150 ease-out group-hover:text-blue-600">Ver →</span>
                    </div>
                    <h3 class="mt-3 text-base font-semibold text-gray-900">{{ $card['title'] }}</h3>
                    <p class="mt-1 text-sm text-gray-500 leading-relaxed">{{ $card['desc'] }}</p>
                </a>
                @endforeach
            </div>
        </div>
    </div>
</x-app-layout>