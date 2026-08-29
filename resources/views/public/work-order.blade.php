<x-public-layout>
    <div class="max-w-6xl mx-auto px-4 py-6 sm:px-6 lg:px-8">

        <div class="flex items-center justify-between gap-3 mb-5">
            <a href="{{ route('public.portal', $vehicle->access_token) }}" class="inline-flex items-center gap-1.5 text-sm font-medium text-blue-600 hover:text-blue-700 transition-colors duration-150">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Mis servicios
            </a>
            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-teal-50 text-teal-700">
                {{ $workOrder->status_label }}
            </span>
        </div>
        <h1 class="text-xl font-semibold text-gray-800 mb-1">Orden de trabajo {{ $workOrder->document_sn ?? '' }}</h1>
        <p class="text-sm text-gray-500 mb-5">{{ $vehicle->plate }} · {{ $workOrder->created_at?->format('d/m/Y') }}</p>

        @if (session('success'))
            <div class="mb-4 px-4 py-3 bg-green-50 border border-green-200 text-green-700 rounded-lg text-sm">{{ session('success') }}</div>
        @endif

        {{-- Vehículo / cliente --}}
        <div class="card mb-4">
            <div class="p-5 grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider text-gray-500 mb-1">Vehículo</p>
                    <p class="font-medium text-gray-800">
                        {{ $vehicle->plate }}
                        <span class="text-gray-500">· {{ $workOrder->vehicle?->vehicleModel?->brand?->name }} {{ $workOrder->vehicle?->vehicleModel?->name }} ({{ $workOrder->vehicle?->year }})</span>
                    </p>
                    @if ($workOrder->vehicle?->vin)
                        <p class="text-xs text-gray-500">VIN: {{ $workOrder->vehicle->vin }}</p>
                    @endif
                </div>
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider text-gray-500 mb-1">Cliente</p>
                    <p class="font-medium text-gray-800">{{ $workOrder->client?->display_name }}</p>
                    @if ($workOrder->establishment)
                        <p class="text-xs text-gray-500">{{ $workOrder->establishment->name }}</p>
                    @endif
                </div>
            </div>
        </div>

        {{-- Trabajos realizados --}}
        <div class="card mb-4">
            <div class="p-5">
                <h2 class="text-sm font-semibold text-gray-700 uppercase tracking-wider mb-3">Trabajos realizados</h2>
                @foreach ($workOrder->estimates as $estimate)
                    <div class="border-t border-gray-100 first:border-t-0 py-3">
                        <div class="flex flex-wrap items-center justify-between gap-2">
                            <p class="font-medium text-gray-800">Presupuesto {{ $estimate->document_sn ?? '' }} <span class="text-gray-500 text-sm">· {{ $estimate->service_type_label ?? $estimate->service_type }}</span></p>
                            <span class="text-sm font-semibold text-gray-800">S/ {{ number_format((float) $estimate->total, 2) }}</span>
                        </div>
                        @if ($estimate->items->isNotEmpty())
                            <ul class="mt-2 space-y-1 text-sm text-gray-600">
                                @foreach ($estimate->items as $item)
                                    <li class="flex justify-between gap-3">
                                        <span>{{ $item->quantity }} × {{ $item->description }}</span>
                                        <span class="text-gray-800">S/ {{ number_format((float) $item->total, 2) }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Control de calidad --}}
        @if ($latestQc && $qcTemplate)
            <div class="card mb-4">
                <div class="p-5">
                    <div class="flex flex-wrap items-center justify-between gap-3 mb-3">
                        <h2 class="text-sm font-semibold text-gray-700 uppercase tracking-wider">Control de calidad</h2>
                        @php
                            $resultBadge = $latestQc->result === 'approved'
                                ? 'bg-green-50 text-green-700 border-green-200'
                                : 'bg-red-50 text-red-700 border-red-200';
                        @endphp
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold border {{ $resultBadge }}">
                            {{ $latestQc->result_label }}
                        </span>
                    </div>

                    @if ($latestQc->result === 'rejected')
                        <div class="mb-3 px-3 py-2 bg-red-50 border border-red-200 text-red-700 rounded-lg text-sm">
                            <strong>Causa:</strong> {{ $latestQc->rejection_reason_label }}
                            @if ($latestQc->rejection_details)
                                <p class="mt-1">{{ $latestQc->rejection_details }}</p>
                            @endif
                        </div>
                    @endif

                    @php $answers = $latestQc->answers ?? []; @endphp
                    <div class="text-sm">
                        @foreach ($qcTemplate->sections as $section)
                            <h3 class="mt-4 text-xs font-semibold uppercase tracking-wider text-gray-500 first:mt-0">{{ $section->name }}</h3>
                            <ul class="mt-2 space-y-1">
                                @foreach ($section->items as $item)
                                    @php
                                        $value = $answers[$item->key] ?? null;
                                        if ($item->type === 'checkbox') {
                                            $display = $value ? 'Sí' : 'No';
                                        } elseif (in_array($item->type, ['select', 'radio'], true)) {
                                            $opt = collect($item->option_list)->firstWhere('value', $value);
                                            $display = $opt['label'] ?? ($value !== null && $value !== '' ? $value : '—');
                                        } else {
                                            $display = ($value !== null && $value !== '') ? $value : '—';
                                        }
                                    @endphp
                                    <li class="flex justify-between gap-3">
                                        <span class="text-gray-600">{{ $item->label }}</span>
                                        <span class="font-medium text-gray-800">{{ $display }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        @endforeach
                    </div>

                    @if ($latestQc->reviewer)
                        <p class="mt-4 text-xs text-gray-500">Revisado por {{ $latestQc->reviewer->name }} · {{ $latestQc->reviewed_at?->format('d/m/Y H:i') }}</p>
                    @endif
                </div>
            </div>
        @endif

        {{-- Encuesta de satisfacción --}}
        <div class="card mb-4">
            <div class="p-5 flex flex-wrap items-center justify-between gap-3">
                <div>
                    <h2 class="text-sm font-semibold text-gray-700 uppercase tracking-wider">Encuesta de satisfacción</h2>
                    <p class="mt-1 text-sm text-gray-500">
                        @if ($workOrder->satisfactionSurvey)
                            Ya respondiste nuestra encuesta. ¡Gracias por tu opinión!
                        @else
                            Cuéntanos cómo fue tu experiencia con nuestro taller.
                        @endif
                    </p>
                </div>
                @unless ($workOrder->satisfactionSurvey)
                    <a href="{{ route('public.work-order.survey', [$vehicle->access_token, $workOrder]) }}" class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700 transition-colors duration-150">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        Responder encuesta
                    </a>
                @endunless
            </div>
        </div>
    </div>
</x-public-layout>

