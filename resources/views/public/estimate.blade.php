<x-public-layout>
    <div class="max-w-6xl mx-auto px-4 py-6 sm:px-6 lg:px-8">

        <div class="flex items-center justify-between gap-3 mb-5">
            <a href="{{ route('public.portal', $vehicle->access_token) }}" class="inline-flex items-center gap-1.5 text-sm font-medium text-blue-600 hover:text-blue-700 transition-colors duration-150">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Mis servicios
            </a>
            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold {{ $estimate->status === 'sent_client' ? 'bg-amber-50 text-amber-700' : 'bg-blue-50 text-blue-700' }}">
                {{ $estimate->status_label }}
            </span>
        </div>
        <h1 class="text-xl font-semibold text-gray-800 mb-1">Presupuesto {{ $estimate->document_sn ?? '' }}</h1>
        <p class="text-sm text-gray-500 mb-5">{{ $vehicle->plate }} · {{ $estimate->created_at?->format('d/m/Y') }}</p>

        <div class="mb-5">
            @include('partials.flow-stepper', ['activeIndex' => 1])
        </div>

        @if (session('success'))
            <div class="mb-4 px-4 py-3 bg-green-50 border border-green-200 text-green-700 rounded-lg text-sm">{{ session('success') }}</div>
        @endif
        @if ($errors->any())
            <div class="mb-4 px-4 py-3 bg-red-50 border border-red-200 text-red-700 rounded-lg text-sm">{{ $errors->first() }}</div>
        @endif

        <div class="lg:grid lg:grid-cols-3 lg:gap-6">
            <div class="lg:col-span-2 min-w-0">

        {{-- Detalle --}}
        <div class="card mb-4">
            <div class="p-5">
                <h2 class="text-sm font-semibold text-gray-700 uppercase tracking-wider mb-3">Detalle</h2>

                @php
                    $groups = [
                        'Servicios' => $grouped['services'],
                        'Repuestos' => $grouped['parts_sale'],
                        'Repuestos (seguro)' => $grouped['parts_ins'],
                    ];
                    $hasAny = collect($groups)->flatten(1)->isNotEmpty();
                @endphp

                @if (!$hasAny)
                    <p class="text-sm text-gray-500">Sin ítems registrados.</p>
                @else
                    @foreach ($groups as $title => $list)
                        @if (count($list))
                            <div class="mt-4 first:mt-0">
                                <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">{{ $title }}</h3>
                                @foreach ($list as $group)
                                    @if (!empty($group['category']))
                                        <p class="text-xs font-medium text-gray-400 mt-2 mb-1">{{ $group['category'] }}</p>
                                    @endif
                                    <div class="rounded-lg border border-gray-200 overflow-hidden">
                                        <table class="min-w-full divide-y divide-gray-200 text-sm">
                                            <tbody class="divide-y divide-gray-100">
                                                @foreach ($group['items'] as $item)
                                                    <tr>
                                                        <td class="px-3 py-2">
                                                            <span class="text-gray-800">{{ $item->description }}</span>
                                                            <span class="text-gray-400 text-xs"> × {{ $item->quantity }}</span>
                                                        </td>
                                                        <td class="px-3 py-2 text-right text-gray-700 whitespace-nowrap">{{ number_format((float) $item->total_line, 2) }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    @endforeach
                @endif
            </div>
        </div>

        {{-- Totales --}}
        <div class="card mb-4">
            <div class="p-5 space-y-2 text-sm">
                <div class="flex justify-between"><span class="text-gray-500">Subtotal</span><span class="font-medium">{{ number_format((float) $estimate->subtotal, 2) }} {{ $estimate->currency }}</span></div>
                @if ((float) $estimate->discount > 0)
                    <div class="flex justify-between"><span class="text-gray-500">Descuento</span><span class="font-medium text-red-600">-{{ number_format((float) $estimate->discount, 2) }}</span></div>
                @endif
                <div class="flex justify-between"><span class="text-gray-500">Base imponible</span><span class="font-medium">{{ number_format((float) $estimate->taxable_base, 2) }}</span></div>
                <div class="flex justify-between"><span class="text-gray-500">IGV ({{ number_format(($estimate->establishment?->igv_rate ?? 0.18) * 100, 2) }}%)</span><span class="font-medium">{{ number_format((float) $estimate->iva, 2) }}</span></div>
                <div class="flex justify-between border-t border-gray-200 pt-2 text-base"><span class="font-semibold text-gray-800">Total</span><span class="font-bold text-gray-900">{{ number_format((float) $estimate->total, 2) }} {{ $estimate->currency }}</span></div>
                @if ($estimate->work_days)
                    <p class="text-xs text-gray-500">Tiempo estimado de trabajo: {{ $estimate->work_days }} día(s).</p>
                @endif
            </div>
        </div>
            </div>

            <aside class="mt-6 lg:mt-0">
                <div class="lg:sticky lg:top-6 space-y-4">

        {{-- Aprobar / Rechazar --}}
        @if ($estimate->status === 'sent_client')
            <div class="card border-2 border-blue-200">
                <div class="p-5">
                    <h2 class="text-base font-semibold text-gray-800 mb-1">¿Apruebas el presupuesto?</h2>
                    <p class="text-sm text-gray-500 mb-4">Si hay algo que corregir, indícalo y lo revisaremos antes de continuar.</p>

                    <form id="form-approve" method="POST" action="{{ route('public.portal.estimate.approve', [$vehicle->access_token, $estimate]) }}">@csrf</form>
                    <form id="form-reject" method="POST" action="{{ route('public.portal.estimate.reject', [$vehicle->access_token, $estimate]) }}">@csrf</form>

                    <textarea id="reject-reason" name="reason" form="form-reject" rows="2" required placeholder="Si no estás de acuerdo, indica el motivo..." class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"></textarea>
                    <p id="reject-reason-error" class="hidden mt-1 text-xs text-red-600">Indica el motivo del rechazo para poder continuar.</p>

                    <div class="mt-3 grid gap-3 sm:grid-cols-2">
                        <button type="button" data-approve-open class="btn btn-primary w-full justify-center py-2.5">Sí, aprobar presupuesto</button>
                        <button type="button" data-reject-open class="btn btn-danger w-full justify-center py-2.5">No estoy de acuerdo</button>
                    </div>
                </div>
            </div>
        @elseif ($estimate->rejection_reason)
            <div class="card">
                <div class="p-5">
                    <div class="rounded-lg bg-red-50 border border-red-200 text-red-700 p-4">
                        <p class="font-medium text-sm">Motivo del rechazo</p>
                        <p class="text-sm mt-1">{{ $estimate->rejection_reason }}</p>
                    </div>
                </div>
            </div>
        @else
            <div class="card">
                <div class="p-5">
                    <p class="text-sm font-semibold text-gray-800">{{ $estimate->status_label }}</p>
                    <p class="text-xs text-gray-500 mt-1">Te avisaremos por este mismo enlace cuando haya novedades.</p>
                </div>
            </div>
        @endif

        {{-- Datos rápidos --}}
        <div class="card">
            <div class="p-5 text-sm">
                <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3">Datos del presupuesto</h3>
                <dl class="space-y-2">
                    <div class="flex justify-between gap-3"><dt class="text-gray-500">Total</dt><dd class="font-medium text-gray-900">{{ number_format((float) $estimate->total, 2) }} {{ $estimate->currency }}</dd></div>
                    <div class="flex justify-between gap-3"><dt class="text-gray-500">Documento</dt><dd class="font-medium text-gray-900">{{ $estimate->document_sn ?? '—' }}</dd></div>
                    <div class="flex justify-between gap-3"><dt class="text-gray-500">Tiempo</dt><dd class="font-medium text-gray-900 text-right">{{ $estimate->work_days ? $estimate->work_days . ' día(s)' : '—' }}</dd></div>
                </dl>
            </div>
        </div>

                </div>
            </aside>
        </div>

        @include('partials.approval-confirm-modal', ['entityName' => 'presupuesto'])

        <p class="mt-8 text-center text-xs text-gray-400">Si tienes dudas, contáctanos por WhatsApp o en nuestras instalaciones.</p>
    </div>
</x-public-layout>

