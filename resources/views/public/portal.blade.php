<x-public-layout>
    <div class="max-w-6xl mx-auto px-4 py-6 sm:px-6 lg:px-8">

        {{-- Encabezado --}}
        <div class="mb-6">
            <h1 class="text-xl font-semibold text-gray-800">Portal de seguimiento</h1>
            <p class="text-sm text-gray-500 mt-1">Consulta el estado de tu vehículo y aprueba lo que esté pendiente.</p>
        </div>

        @if (session('success'))
            <div class="mb-4 px-4 py-3 bg-green-50 border border-green-200 text-green-700 rounded-lg text-sm">{{ session('success') }}</div>
        @endif
        @if ($errors->any())
            <div class="mb-4 px-4 py-3 bg-red-50 border border-red-200 text-red-700 rounded-lg text-sm">{{ $errors->first() }}</div>
        @endif

        {{-- Vehículo --}}
        <div class="card mb-6">
            <div class="p-5">
                <div class="text-2xl font-bold text-gray-900 tracking-wider">{{ $vehicle->plate }}</div>
                <div class="text-sm text-gray-500 mt-1">
                    {{ $vehicle->vehicleModel?->brand?->name ?? '—' }} {{ $vehicle->vehicleModel?->name ?? '' }}
                    @if ($vehicle->year) · {{ $vehicle->year }} @endif
                    @if ($vehicle->color) · {{ $vehicle->color }} @endif
                </div>
            </div>
        </div>

        {{-- Pendientes de aprobación --}}
        @if ($pendingCheckIns->isNotEmpty() || $pendingEstimates->isNotEmpty())
            @if ($pendingCheckIns->isNotEmpty())
                <h2 class="text-sm font-semibold text-gray-700 uppercase tracking-wider mb-3">Pendiente de tu aprobación</h2>
                <div class="grid gap-4 md:grid-cols-2">
                @foreach ($pendingCheckIns as $checkIn)
                    <div class="card">
                        <div class="p-5 flex flex-wrap items-center justify-between gap-3">
                            <div>
                                <p class="font-semibold text-gray-800">Inventario {{ $checkIn->document_sn }}</p>
                                <p class="text-sm text-gray-500">{{ $checkIn->created_at?->format('d/m/Y') }} · {{ $checkIn->service_type_label }}</p>
                            </div>
                            <a href="{{ route('public.portal.check-in', [$vehicle->access_token, $checkIn]) }}" class="btn btn-primary">Revisar y aprobar</a>
                        </div>
                    </div>
                @endforeach
                </div>
            @endif

            @if ($pendingEstimates->isNotEmpty())
                @if ($pendingCheckIns->isNotEmpty())
                    <div class="h-6"></div>
                @endif
                <h2 class="text-sm font-semibold text-gray-700 uppercase tracking-wider mb-3">Presupuestos por confirmar</h2>
                <div class="grid gap-4 md:grid-cols-2">
                @foreach ($pendingEstimates as $estimate)
                    <div class="card">
                        <div class="p-5 flex flex-wrap items-center justify-between gap-3">
                            <div>
                                <p class="font-semibold text-gray-800">Presupuesto {{ $estimate->document_sn }}</p>
                                <p class="text-sm text-gray-500">
                                    {{ $estimate->created_at?->format('d/m/Y') }} ·
                                    Total: <span class="font-medium text-gray-800">{{ number_format((float) $estimate->total, 2) }} {{ $estimate->currency }}</span>
                                </p>
                            </div>
                            <a href="{{ route('public.portal.estimate', [$vehicle->access_token, $estimate]) }}" class="btn btn-primary">Ver y aprobar</a>
                        </div>
                    </div>
                @endforeach
                </div>
            @endif
        @else
            <div class="card mb-6">
                <div class="p-5">
                    <p class="text-sm text-gray-600">
                        @if ($activeEstimate && in_array($activeEstimate->status, ['rejected_client', 'rejected_insurance']))
                            Tu presupuesto está en revisión por el taller. Te avisaremos cuando esté corregido.
                        @elseif ($activeEstimate && in_array($activeEstimate->status, ['approved_client', 'approved_insurance', 'in_repair']))
                            Tu presupuesto fue aprobado. Tu vehículo está en reparación.
                        @elseif ($activeCheckIn && $activeCheckIn->status === 'approved')
                            Tu inventario fue aprobado. Cuando el presupuesto esté listo, te lo enviaremos por este mismo enlace.
                        @elseif ($activeCheckIn && $activeCheckIn->status === 'rejected')
                            Tu inventario está en revisión por el taller. Te avisaremos cuando esté corregido.
                        @else
                            No hay servicios en curso para este vehículo.
                        @endif
                    </p>
                </div>
            </div>
        @endif
        {{-- Histórico de servicios --}}
        @if ($history->isNotEmpty())
            <h2 class="text-sm font-semibold text-gray-700 uppercase tracking-wider mb-3 mt-8">Histórico de servicios</h2>
            <div class="card">
                <ul class="divide-y divide-gray-200">
                    @foreach ($history as $item)
                        <li class="p-4 flex flex-wrap items-center justify-between gap-2">
                            <div>
                                <p class="text-sm font-medium text-gray-800">
                                    {{ $item->document_sn ?? 'Inventario #' . $item->id }}
                                    @if ($item->estimates->isNotEmpty())
                                        <span class="text-gray-400">· {{ $item->estimates->map(fn ($e) => $e->document_sn)->implode(', ') }}</span>
                                    @endif
                                </p>
                                <p class="text-xs text-gray-500">{{ $item->created_at?->format('d/m/Y') }} · {{ $item->service_type_label }} · {{ $item->status_label }}</p>
                            </div>
                            @if (in_array($item->status, ['approved', 'rejected', 'closed']))
                                <a href="{{ route('public.portal.check-in', [$vehicle->access_token, $item]) }}" class="text-sm text-blue-600 hover:underline">Ver detalle</a>
                            @endif
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif

        <p class="mt-8 text-center text-xs text-gray-400">Si tienes dudas, contáctanos por WhatsApp o en nuestras instalaciones.</p>
    </div>
</x-public-layout>

