<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap justify-between items-center gap-3">
            <div class="flex flex-wrap items-center gap-3">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Presupuesto') }}</h2>
                @if ($estimate->document_sn)
                    <x-document-badge :sn="$estimate->document_sn" />
                @endif
                @if ($estimate->vehicle?->plate)
                    <span class="text-sm text-gray-500">{{ $estimate->vehicle->plate }}</span>
                @endif
            </div>
            <div class="flex flex-wrap gap-2">
                @can('update', $estimate)
                    @if (!$estimate->is_final)
                        <a href="{{ route('estimates.edit', $estimate) }}" class="btn btn-secondary">Editar</a>
                    @endif
                @endcan
                @can('returnToDraft', $estimate)
                    @if (!in_array($estimate->status, ['draft', 'finalized']) && !$estimate->is_final)
                        <form method="POST" action="{{ route('estimates.return-to-draft', $estimate) }}">
                            @csrf
                            <button type="submit" class="btn btn-secondary">Reabrir</button>
                        </form>
                    @endif
                @endcan

                @can('sendToInsurance', $estimate)
                    @if ($estimate->status === 'draft')
                        <form method="POST" action="{{ route('estimates.send-to-insurance', $estimate) }}">
                            @csrf
                            <button type="submit" class="btn btn-primary">Enviar a seguro</button>
                        </form>
                    @endif
                @endcan
                @can('sendToClient', $estimate)
                    @if ($estimate->status === 'draft')
                        <form method="POST" action="{{ route('estimates.send-to-client', $estimate) }}">
                            @csrf
                            <button type="submit" class="btn btn-primary">Enviar a cliente</button>
                        </form>
                    @endif
                @endcan

                @can('approveInsurance', $estimate)
                    @if ($estimate->status === 'sent_insurance')
                        <form method="POST" action="{{ route('estimates.approve-insurance', $estimate) }}">
                            @csrf
                            <button type="submit" class="btn btn-primary">Aprobar seguro</button>
                        </form>
                    @endif
                    @if ($estimate->status === 'sent_insurance')
                        <form method="POST" action="{{ route('estimates.reject-insurance', $estimate) }}">
                            @csrf
                            <button type="submit" class="btn btn-danger">Rechazar seguro</button>
                        </form>
                    @endif
                @endcan

                @can('approveClient', $estimate)
                    @if ($estimate->status === 'sent_client')
                        <form method="POST" action="{{ route('estimates.approve-client', $estimate) }}">
                            @csrf
                            <button type="submit" class="btn btn-primary">Aprobar cliente</button>
                        </form>
                    @endif
                    @if ($estimate->status === 'sent_client')
                        <form method="POST" action="{{ route('estimates.reject-client', $estimate) }}">
                            @csrf
                            <button type="submit" class="btn btn-danger">Rechazar cliente</button>
                        </form>
                    @endif
                @endcan

                @can('startRepair', $estimate)
                    @if (in_array($estimate->status, ['approved_insurance', 'approved_client']))
                        <form method="POST" action="{{ route('estimates.start-repair', $estimate) }}">
                            @csrf
                            <button type="submit" class="btn btn-primary">Iniciar reparación</button>
                        </form>
                    @endif
                @endcan
                @can('finalize', $estimate)
                    @if ($estimate->status === 'in_repair')
                        <form method="POST" action="{{ route('estimates.finalize', $estimate) }}">
                            @csrf
                            <button type="submit" class="btn btn-primary">Finalizar</button>
                        </form>
                    @endif
                @endcan
            </div>
        </div>
    </x-slot>

    @php
        $statusColors = [
            'draft' => 'bg-gray-100 text-gray-800',
            'sent_insurance' => 'bg-yellow-100 text-yellow-800',
            'approved_insurance' => 'bg-blue-100 text-blue-800',
            'rejected_insurance' => 'bg-red-100 text-red-800',
            'sent_client' => 'bg-yellow-100 text-yellow-800',
            'approved_client' => 'bg-blue-100 text-blue-800',
            'rejected_client' => 'bg-red-100 text-red-800',
            'in_repair' => 'bg-indigo-100 text-indigo-800',
            'finalized' => 'bg-green-100 text-green-800',
        ];
    @endphp

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="mb-4 px-4 py-3 bg-green-50 border border-green-200 text-green-700 rounded-lg text-sm">{{ session('success') }}</div>
            @endif

            <div class="mb-4 flex items-center gap-2">
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-sm font-medium {{ $statusColors[$estimate->status] ?? 'bg-gray-100 text-gray-800' }}">
                    {{ $estimate->status_label }}
                </span>
            </div>

            {{-- Cabecera --}}
            <div class="card mb-4">
                <div class="p-6">
                    <div class="grid grid-cols-2 lg:grid-cols-3 gap-4 text-sm">
                        <div><p class="text-xs text-gray-500">Vehículo</p><p class="font-medium">{{ $estimate->vehicle?->plate ?? '-' }}</p></div>
                        <div><p class="text-xs text-gray-500">Cliente</p><p class="font-medium">{{ $estimate->client?->display_name ?? '-' }}</p></div>
                        <div><p class="text-xs text-gray-500">Aseguradora</p><p class="font-medium">{{ $estimate->insuranceCompany?->display_name ?? '-' }}</p></div>
                        <div><p class="text-xs text-gray-500">Nº Siniestro</p><p class="font-medium">{{ $estimate->claim_number ?? '-' }}</p></div>
                        <div><p class="text-xs text-gray-500">Asesor</p><p class="font-medium">{{ $estimate->advisor?->name ?? '-' }}</p></div>
                        <div><p class="text-xs text-gray-500">Días de trabajo</p><p class="font-medium">{{ $estimate->work_days ?? '-' }}</p></div>
                        <div><p class="text-xs text-gray-500">Moneda</p><p class="font-medium">{{ $estimate->currency }}</p></div>
                        <div><p class="text-xs text-gray-500">Tipo de cambio</p><p class="font-medium">{{ $estimate->exchange_rate }}</p></div>
                        <div><p class="text-xs text-gray-500">Tarifa hora hombre</p><p class="font-medium">{{ number_format((float) $estimate->hourly_rate, 2) }}</p></div>
                        <div><p class="text-xs text-gray-500">Tarifa paño</p><p class="font-medium">{{ number_format((float) $estimate->panel_rate, 2) }}</p></div>
                    </div>

                    @if ($estimate->comments)
                        <div class="mt-4"><p class="text-xs text-gray-500">Comentarios</p><p class="mt-1 text-sm whitespace-pre-line">{{ $estimate->comments }}</p></div>
                    @endif
                </div>
            </div>

            {{-- Ítems agrupados (vista del cliente) --}}
            <div class="card mb-4 overflow-hidden">
                <div class="p-6">
                    @php
                        $renderGroup = function ($groups, $headers) use (&$renderGroup) {
                            foreach ($groups as $group) {
                                echo '<h4 class="text-sm font-semibold text-gray-700 mb-2 mt-4">' . e($group['category']) . '</h4>';
                                echo '<div class="overflow-x-auto"><table class="min-w-full divide-y divide-gray-200 text-sm">';
                                echo '<thead><tr class="bg-gray-50">';
                                foreach ($headers as $h) {
                                    echo '<th class="px-3 py-2 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">' . e($h) . '</th>';
                                }
                                echo '</tr></thead><tbody class="divide-y divide-gray-200">';
                                foreach ($group['items'] as $item) {
                                    echo '<tr>';
                                    echo '<td class="px-3 py-2 font-medium">' . e($item->description ?: ($item->service?->name ?? $item->part?->name ?? '-')) . '</td>';
                                    echo '<td class="px-3 py-2 text-right">' . number_format((float) $item->quantity, 2) . '</td>';
                                    echo '<td class="px-3 py-2 text-right">' . number_format((float) $item->unit_price, 4) . '</td>';
                                    echo '<td class="px-3 py-2 text-right">' . number_format((float) $item->discount_amount, 2) . '</td>';
                                    echo '<td class="px-3 py-2 text-right">' . number_format((float) $item->net_line, 2) . '</td>';
                                    echo '<td class="px-3 py-2 text-right">' . number_format((float) $item->iva_line, 2) . '</td>';
                                    echo '<td class="px-3 py-2 text-right font-semibold">' . number_format((float) $item->total_line, 2) . '</td>';
                                    echo '</tr>';
                                }
                                echo '</tbody></table></div>';
                            }
                        };
                        $headers = ['Descripción', 'Cant.', 'P. Unitario', 'Descuento', 'Neto', 'IGV', 'Total'];
                    @endphp

                    @if (count($grouped['services']))
                        <h3 class="text-lg font-semibold text-gray-800 mb-2">Servicios</h3>
                        @php $renderGroup($grouped['services'], $headers); @endphp
                    @endif

                    @if (count($grouped['parts_sale']))
                        <h3 class="text-lg font-semibold text-gray-800 mb-2 mt-6">Repuestos a vender</h3>
                        @php $renderGroup($grouped['parts_sale'], $headers); @endphp
                    @endif

                    @if (count($grouped['parts_ins']))
                        <h3 class="text-lg font-semibold text-gray-800 mb-2 mt-6">Repuestos de la compañía de seguros</h3>
                        @php $renderGroup($grouped['parts_ins'], $headers); @endphp
                    @endif

                    @if (!count($grouped['services']) && !count($grouped['parts_sale']) && !count($grouped['parts_ins']))
                        <p class="text-sm text-gray-500">No hay ítems registrados.</p>
                    @endif
                </div>
            </div>

            {{-- Resumen de cálculos (desglose SUNAT) --}}
            @php
                $linesDiscount = (float) $estimate->items()->sum('discount_amount');
                $globalDiscount = (float) $estimate->discounts()
                    ->where('source', 'global')->where('applied_to', 'subtotal')->sum('amount');
                $franchise = (float) $estimate->discounts()
                    ->whereIn('source', ['insurance', 'promotion', 'other'])
                    ->where('applied_to', 'subtotal')->sum('amount');
            @endphp
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                <div class="md:col-span-2"></div>
                <div class="card">
                    <div class="p-5 space-y-2 text-sm">
                        <div class="flex justify-between"><span class="text-gray-500">Valor Bruto</span><span class="font-medium">{{ number_format((float) $estimate->subtotal, 2) }}</span></div>
                        <div class="flex justify-between"><span class="text-gray-500">Descuentos por ítem</span><span class="font-medium text-red-600">- {{ number_format($linesDiscount, 2) }}</span></div>
                        <div class="flex justify-between"><span class="text-gray-500">Descuento global</span><span class="font-medium text-red-600">- {{ number_format($globalDiscount, 2) }}</span></div>
                        <div class="flex justify-between"><span class="text-gray-500">Franquicia</span><span class="font-medium text-red-600">- {{ number_format($franchise, 2) }}</span></div>
                        <div class="flex justify-between border-t border-gray-100 pt-2"><span class="font-medium text-gray-700">Valor Venta (Base Imponible)</span><span class="font-medium">{{ number_format((float) $estimate->taxable_base, 2) }}</span></div>
                        <div class="flex justify-between"><span class="text-gray-500">IGV ({{ round($estimate->iva > 0 ? (($estimate->iva / $estimate->taxable_base) * 100) : 0, 0) }}%)</span><span class="font-medium">{{ number_format((float) $estimate->iva, 2) }}</span></div>
                        <div class="flex justify-between border-t border-gray-200 pt-2 text-base"><span class="font-semibold">Total a Pagar</span><span class="font-semibold text-gray-900">{{ number_format((float) $estimate->total, 2) }}</span></div>
                    </div>
                </div>
            </div>

            {{-- Historial --}}
            @if ($estimate->statusHistory->isNotEmpty())
                <div class="card">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">Historial de estados</h3>
                        <ul class="space-y-2 text-sm">
                            @foreach ($estimate->statusHistory as $history)
                                <li class="flex flex-wrap items-center gap-2">
                                    <span class="text-gray-500">{{ $history->created_at?->format('d/m/Y H:i') }}</span>
                                    <span class="font-medium">{{ \App\Models\Estimate::STATUS_LABELS[$history->from_status] ?? $history->from_status }} → {{ \App\Models\Estimate::STATUS_LABELS[$history->to_status] ?? $history->to_status }}</span>
                                    @if ($history->user)
                                        <span class="text-gray-400">por {{ $history->user->name }}</span>
                                    @endif
                                    @if ($history->comments)
                                        <span class="text-gray-600">— {{ $history->comments }}</span>
                                    @endif
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>