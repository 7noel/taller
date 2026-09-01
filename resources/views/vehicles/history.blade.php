<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">Historial · {{ $vehicle->plate }}</h2>
                <p class="text-sm text-gray-500">
                    {{ $vehicle->vehicleModel?->brand?->name }} {{ $vehicle->vehicleModel?->name }} {{ $vehicle->year ? '· ' . $vehicle->year : '' }}
                </p>
            </div>
            <a href="{{ url()->previous() }}" class="btn btn-secondary">Volver</a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            @php
                $lastCheckIn = $checkIns->first();
                $checkColors = ['draft' => 'bg-gray-100 text-gray-600', 'pending_approval' => 'bg-amber-50 text-amber-700', 'approved' => 'bg-green-50 text-green-700', 'rejected' => 'bg-red-50 text-red-700', 'closed' => 'bg-gray-100 text-gray-600'];
            @endphp

            {{-- Resumen de fechas clave --}}
            <div class="mb-6 grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="card p-4">
                    <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Revisión técnica</p>
                    <p class="mt-1 text-lg font-semibold text-gray-800">{{ $vehicle->technical_review_date?->format('d/m/Y') ?? '—' }}</p>
                </div>
                <div class="card p-4">
                    <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Próximo mantenimiento</p>
                    <p class="mt-1 text-lg font-semibold text-gray-800">{{ $vehicle->next_maintenance_date?->format('d/m/Y') ?? '—' }}</p>
                    <p class="text-xs text-gray-500">
                        {{ $vehicle->maintenance_source === 'manual' ? 'Ajustada manualmente' : 'Calculada automáticamente' }}
                    </p>
                </div>
                <div class="card p-4">
                    <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Última visita</p>
                    <p class="mt-1 text-lg font-semibold text-gray-800">{{ $lastCheckIn?->created_at?->format('d/m/Y') ?? '—' }}</p>
                </div>
            </div>

            {{-- Ingresos --}}
            <div class="card overflow-hidden mb-6">
                <div class="border-b border-gray-200 px-4 py-3">
                    <h3 class="font-semibold text-gray-800">Ingresos (check-ins)</h3>
                </div>
                <div class="p-4 sm:p-5">
                    @if ($checkIns->isEmpty())
                        <p class="text-sm text-gray-500">Sin ingresos registrados.</p>
                    @else
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 text-sm">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-3 py-2 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Documento</th>
                                        <th class="px-3 py-2 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Fecha</th>
                                        <th class="px-3 py-2 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Servicio</th>
                                        <th class="px-3 py-2 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Km</th>
                                        <th class="px-3 py-2 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Estado</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200 bg-white">
                                    @foreach ($checkIns as $ci)
                                        <tr class="hover:bg-blue-50/50">
                                            <td class="px-3 py-2">
                                                <a href="{{ route('check-ins.show', $ci) }}" title="Ver ingreso">
                                                    <x-document-badge :sn="$ci->document_sn" :label="\App\Models\CheckIn::SERVICE_TYPES[$ci->service_type] ?? $ci->service_type" />
                                                </a>
                                            </td>
                                            <td class="px-3 py-2 text-gray-800">{{ $ci->created_at?->format('d/m/Y') }}</td>
                                            <td class="px-3 py-2">{{ \App\Models\CheckIn::SERVICE_TYPES[$ci->service_type] ?? $ci->service_type }}</td>
                                            <td class="px-3 py-2">{{ $ci->mileage ? number_format($ci->mileage) . ' km' : '—' }}</td>
                                            <td class="px-3 py-2">
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $checkColors[$ci->status] ?? 'bg-gray-100 text-gray-600' }}">
                                                    {{ \App\Models\CheckIn::STATUS_LABELS[$ci->status] ?? $ci->status }}
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Presupuestos --}}
            <div class="card overflow-hidden">
                <div class="border-b border-gray-200 px-4 py-3">
                    <h3 class="font-semibold text-gray-800">Presupuestos</h3>
                </div>
                <div class="p-4 sm:p-5">
                    @if ($estimates->isEmpty())
                        <p class="text-sm text-gray-500">Sin presupuestos registrados.</p>
                    @else
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 text-sm">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-3 py-2 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Documento</th>
                                        <th class="px-3 py-2 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Fecha</th>
                                        <th class="px-3 py-2 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Tipo</th>
                                        <th class="px-3 py-2 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Cliente</th>
                                        <th class="px-3 py-2 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Total</th>
                                        <th class="px-3 py-2 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Estado</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200 bg-white">
                                    @foreach ($estimates as $est)
                                        <tr class="hover:bg-blue-50/50">
                                            <td class="px-3 py-2">
                                                <a href="{{ route('estimates.show', $est) }}" title="Ver presupuesto">
                                                    <x-document-badge :sn="$est->document_sn" :label="$est->status_label" />
                                                </a>
                                            </td>
                                            <td class="px-3 py-2 text-gray-800">{{ $est->created_at?->format('d/m/Y') }}</td>
                                            <td class="px-3 py-2">{{ \App\Models\CheckIn::SERVICE_TYPES[$est->service_type] ?? $est->service_type ?? '—' }}</td>
                                            <td class="px-3 py-2">{{ $est->client?->display_name ?? '—' }}</td>
                                            <td class="px-3 py-2 text-right">{{ $est->total ? 'S/ ' . number_format($est->total, 2) : '—' }}</td>
                                            <td class="px-3 py-2">
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-600">
                                                    {{ $est->status_label }}
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
