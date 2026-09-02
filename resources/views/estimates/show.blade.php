<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap justify-between items-center gap-3">
            <div class="flex flex-wrap items-center gap-3">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Presupuesto') }}</h2>
                @if ($estimate->document_sn)
                    <x-document-badge :sn="$estimate->document_sn" />
                @endif
                @if ($estimate->is_ampliacion && $estimate->parent)
                    <a href="{{ route('estimates.show', $estimate->parent) }}" title="Ver el presupuesto principal"
                       class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-semibold bg-amber-100 text-amber-800 hover:bg-amber-200">
                        Ampliación de {{ $estimate->parent->document_sn }}
                    </a>
                @endif
                @if ($estimate->is_garantia && $estimate->warrantyOf)
                    <a href="{{ route('estimates.show', $estimate->warrantyOf) }}" title="Ver el presupuesto original"
                       class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-semibold bg-amber-100 text-amber-800 hover:bg-amber-200">
                        Garantía de {{ $estimate->warrantyOf->document_sn }}
                    </a>
                @endif
                @if ($estimate->is_chargeable === false)
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-semibold bg-gray-100 text-gray-600" title="No genera factura ni cobro al cliente">No facturable</span>
                @endif
                @if ($estimate->liability === 'workshop')
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-semibold bg-red-100 text-red-700" title="El gasto lo asume el taller">
                        Resp. taller{{ $estimate->liabilityUser ? ' · ' . $estimate->liabilityUser->name : '' }}
                    </span>
                @endif
                @if (!$estimate->is_garantia && $estimate->warranty_claims_count > 0)
                    <a href="#garantias" class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-semibold bg-blue-100 text-blue-800 hover:bg-blue-200">
                        {{ $estimate->warranty_claims_count }} garantía(s)
                    </a>
                @endif
                @if ($estimate->vehicle?->plate)
                    <span class="text-sm text-gray-500">{{ $estimate->vehicle->plate }}</span>
                @endif
            </div>
            <div class="flex flex-wrap gap-2">
                @if ($publicLink)
                    <button type="button" data-whatsapp-open class="inline-flex items-center px-3 py-1.5 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase hover:bg-gray-50">
                        <svg class="h-4 w-4 mr-1.5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2a10 10 0 00-8.6 15.1L2 22l5-1.3A10 10 0 1012 2zm5.2 14.2c-.2.6-1.2 1.2-1.7 1.2-.4.1-1 .1-1.6-.1-.4-.1-.9-.3-1.5-.6-2.6-1.1-4.3-3.8-4.4-4-.1-.2-1.1-1.4-1.1-2.7s.7-1.9.9-2.1c.2-.3.5-.3.7-.3h.5c.2 0 .4-.1.6.4.2.6.7 2 .8 2.1.1.1.1.3 0 .5-.1.2-.1.3-.3.5l-.4.5c-.1.1-.3.3-.1.5.2.3.8 1.3 1.7 2.1 1.2 1.1 2.1 1.4 2.4 1.5.3.1.5.1.7-.1l1-1.2c.2-.3.4-.2.7-.1l2 .9c.3.2.5.3.6.4.1.2.1.7-.1 1.3z"/></svg>
                        WhatsApp
                    </button>
                    <button type="button" data-copy-link="{{ $publicLink }}" title="Copiar enlace del portal del cliente" class="inline-flex items-center px-3 py-1.5 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase hover:bg-gray-50">
                        <svg class="h-4 w-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13.828 10.172a4 4 0 010 5.656l-3 3a4 4 0 01-5.656-5.656l1.5-1.5m9.314-9.314a4 4 0 00-5.656 0l-1.5 1.5"/></svg>
                        Copiar enlace
                    </button>
                @endif
                @can('create', \App\Models\FollowUp::class)
                    @if ($estimate->vehicle_id || $estimate->client_id)
                        <form method="POST" action="{{ route('follow-ups.store') }}" class="inline">
                            @csrf
                            <input type="hidden" name="estimate_id" value="{{ $estimate->id }}">
                            <input type="hidden" name="vehicle_id" value="{{ $estimate->vehicle_id }}">
                            <input type="hidden" name="party_id" value="{{ $estimate->client_id }}">
                            <input type="hidden" name="date" value="{{ now()->format('Y-m-d') }}">
                            <input type="hidden" name="type" value="call">
                            <input type="hidden" name="notes" value="Seguimiento del presupuesto {{ $estimate->document_sn }} ({{ $estimate->status_label }}).">
                            <button type="submit" class="btn btn-secondary" title="Registrar un seguimiento de este presupuesto">Seguimiento</button>
                        </form>
                    @endif
                @endcan
                @can('update', $estimate)
                    @if (!$estimate->is_final)
                        <a href="{{ route('estimates.edit', $estimate) }}" class="btn btn-secondary">Editar</a>
                    @endif
                @endcan
                @can('create', \App\Models\Estimate::class)
                    @if (!$estimate->is_ampliacion)
                        <a href="{{ route('estimates.create', ['parent_estimate_id' => $estimate->id]) }}" class="btn btn-secondary" title="Crear una ampliación de este presupuesto (misma moneda, misma aseguradora)">Ampliar</a>
                    @endif
                    @if ($estimate->is_chargeable !== false && !$estimate->is_garantia)
                        <a href="{{ route('estimates.create', ['warranty_of' => $estimate->id, 'work_order_id' => $estimate->work_order_id]) }}" class="btn btn-secondary" title="El vehículo regresó por una falla cubierta por garantía: se crea un presupuesto no facturable">Registrar garantía</a>
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
                    @if (in_array($estimate->status, ['draft', 'rejected_insurance']))
                        <form method="POST" action="{{ route('estimates.send-to-insurance', $estimate) }}">
                            @csrf
                            <button type="submit" class="btn btn-primary">Enviar a seguro</button>
                        </form>
                    @endif
                @endcan
                @can('sendToClient', $estimate)
                    @if (in_array($estimate->status, ['approved_insurance', 'rejected_client']) || ($estimate->status === 'draft' && $estimate->service_type !== 'siniestro'))
                        <form method="POST" action="{{ route('estimates.send-to-client', $estimate) }}">
                            @csrf
                            <button type="submit" class="btn btn-primary">Enviar a cliente</button>
                        </form>
                    @endif
                @endcan

                @can('approveInsurance', $estimate)
                    @if ($estimate->status === 'sent_insurance')
                        <form id="form-insurance-approve" method="POST" action="{{ route('estimates.approve-insurance', $estimate) }}">
                            @csrf
                            <input type="hidden" name="date">
                            <button type="button" data-insurance-approve class="btn btn-primary">Aprobar seguro</button>
                        </form>
                        <form id="form-insurance-reject" method="POST" action="{{ route('estimates.reject-insurance', $estimate) }}">
                            @csrf
                            <input type="hidden" name="date">
                            <input type="hidden" name="reason">
                            <button type="button" data-insurance-reject class="btn btn-danger">Rechazar seguro</button>
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

                @can('create', \App\Models\WorkOrder::class)
                    @if (in_array($estimate->status, ['approved_insurance', 'approved_client']) && !$estimate->work_order_id)
                        <form method="POST" action="{{ route('work-orders.store') }}">
                            @csrf
                            <input type="hidden" name="estimate_id" value="{{ $estimate->id }}">
                            <button type="submit" class="btn btn-primary">Generar OT</button>
                        </form>
                    @elseif ($estimate->work_order_id && $estimate->workOrder)
                        <a href="{{ route('work-orders.show', $estimate->workOrder) }}" class="btn btn-secondary">Ver OT {{ $estimate->workOrder->document_sn }}</a>
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
            @if (session('error'))
                <div class="mb-4 px-4 py-3 bg-red-50 border border-red-200 text-red-700 rounded-lg text-sm">{{ session('error') }}</div>
            @endif

            <div class="mb-4 flex flex-wrap items-center gap-2">
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-sm font-medium {{ $statusColors[$estimate->status] ?? 'bg-gray-100 text-gray-800' }}">
                    {{ $estimate->status_label }}
                </span>
                @if ($estimate->insurance_approved_at)
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-sm font-medium bg-blue-50 text-blue-700">
                        Seguro aprobó · {{ $estimate->insurance_approved_at->format('d/m/Y') }}
                    </span>
                @endif
            </div>

            @if ($estimate->status === 'rejected_insurance')
                <div class="mb-4 rounded-lg bg-red-50 border border-red-200 p-4">
                    <div class="flex items-start gap-3">
                        <svg class="h-5 w-5 shrink-0 text-red-600 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/></svg>
                        <div>
                            <p class="text-sm font-semibold text-red-700">Presupuesto rechazado por el seguro</p>
                            @if ($estimate->insurance_rejection_reason)
                                <p class="mt-1 text-sm text-red-700">{{ $estimate->insurance_rejection_reason }}</p>
                            @endif
                            <p class="mt-2 text-xs text-red-600/80">{{ $estimate->insurance_rejection_label }}</p>
                        </div>
                    </div>
                </div>
            @endif

            @if ($estimate->status === 'rejected_client' && $estimate->rejection_reason)
                <div class="mb-4 rounded-lg bg-red-50 border border-red-200 p-4">
                    <div class="flex items-start gap-3">
                        <svg class="h-5 w-5 shrink-0 text-red-600 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/></svg>
                        <div>
                            <p class="text-sm font-semibold text-red-700">Presupuesto rechazado por el cliente</p>
                            <p class="mt-1 text-sm text-red-700">{{ $estimate->rejection_reason }}</p>
                            <p class="mt-2 text-xs text-red-600/80">{{ $estimate->rejected_by_label }}@if ($estimate->rejected_at && $estimate->rejected_by_user_id) · {{ $estimate->rejected_at->format('d/m/Y H:i') }}@endif</p>
                        </div>
                    </div>
                </div>
            @endif

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
                $ordersTotal = (float) $estimate->thirdPartyOrders()->sum('amount_without_iva');
            @endphp
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                <div class="md:col-span-2"></div>
                <div class="card">
                    <div class="p-5 space-y-2 text-sm">
                        <div class="flex justify-between"><span class="text-gray-500">Valor Bruto</span><span class="font-medium">{{ number_format((float) $estimate->subtotal, 2) }}</span></div>
                        <div class="flex justify-between"><span class="text-gray-500">Descuentos por ítem</span><span class="font-medium text-red-600">- {{ number_format($linesDiscount, 2) }}</span></div>
                        <div class="flex justify-between"><span class="text-gray-500">Descuento global</span><span class="font-medium text-red-600">- {{ number_format($globalDiscount, 2) }}</span></div>
                        <div class="flex justify-between"><span class="text-gray-500">Órdenes de compra (para franquicia)</span><span class="font-medium text-gray-600">+ {{ number_format($ordersTotal, 2) }}</span></div>
                        <div class="flex justify-between border-t border-gray-100 pt-2"><span class="font-medium text-gray-700">Valor Venta (Base Imponible)</span><span class="font-medium">{{ number_format((float) $estimate->taxable_base, 2) }}</span></div>
                        <div class="flex justify-between"><span class="text-gray-500">IGV ({{ round($estimate->iva > 0 ? (($estimate->iva / $estimate->taxable_base) * 100) : 0, 0) }}%)</span><span class="font-medium">{{ number_format((float) $estimate->iva, 2) }}</span></div>
                        <div class="flex justify-between border-t border-gray-200 pt-2 text-base"><span class="font-semibold">Total a Pagar</span><span class="font-semibold text-gray-900">{{ number_format((float) $estimate->total, 2) }}</span></div>
                    </div>
                </div>
            </div>

            {{-- Cobros y adelantos --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                <div class="md:col-span-2">
                    <div class="card">
                        <div class="p-5">
                            <h3 class="text-lg font-semibold text-gray-800 mb-3">Cobros y adelantos</h3>
                            @if ($estimate->payments->isNotEmpty())
                                <div class="overflow-x-auto">
                                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                                        <thead class="bg-gray-50">
                                            <tr>
                                                <th class="px-3 py-2 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Fecha</th>
                                                <th class="px-3 py-2 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Monto</th>
                                                <th class="px-3 py-2 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Medio</th>
                                                <th class="px-3 py-2 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Comprobante</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-gray-100">
                                            @foreach ($estimate->payments as $payment)
                                                <tr>
                                                    <td class="px-3 py-2 text-gray-600">{{ $payment->payment_date?->format('d/m/Y') }}</td>
                                                    <td class="px-3 py-2 font-medium text-gray-800">S/ {{ number_format($payment->amount, 2) }}</td>
                                                    <td class="px-3 py-2 text-gray-600">{{ $payment->paymentMethod?->name ?? '—' }}</td>
                                                    <td class="px-3 py-2 text-gray-600">
                                                        @if ($payment->invoice_id)
                                                            <a href="{{ route('invoices.show', $payment->invoice_id) }}" class="text-blue-600 hover:underline">{{ $payment->invoice?->document_sn ?? 'Ver' }}</a>
                                                        @else
                                                            —
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <p class="text-sm text-gray-500">Sin cobros registrados.</p>
                            @endif
                            <div class="mt-3 flex justify-between border-t border-gray-100 pt-3 text-sm">
                                <span class="text-gray-500">Total cobrado</span>
                                <span class="font-semibold text-green-700">S/ {{ number_format($paidTotal, 2) }}</span>
                            </div>
                            <div class="mt-1 flex justify-between text-sm">
                                <span class="text-gray-500">Saldo pendiente</span>
                                <span class="font-semibold text-gray-800">S/ {{ number_format(max(0, (float) $estimate->total - $paidTotal), 2) }}</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card">
                    <div class="p-5">
                        <h3 class="text-sm font-semibold text-gray-800 mb-3">Registrar adelanto</h3>
                        <form method="POST" action="{{ route('estimates.advance', $estimate) }}" class="space-y-2">
                            @csrf
                            <input type="number" name="amount" step="0.01" min="0.01" placeholder="Monto" required class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                            <input type="hidden" name="party_id" value="{{ $estimate->client_id ?? $estimate->insurance_company_id }}">
                            <select name="payment_method_id" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                                <option value="">Medio de pago</option>
                                @foreach ($paymentMethods as $pm)
                                    <option value="{{ $pm->id }}">{{ $pm->name }}</option>
                                @endforeach
                            </select>
                            <input type="text" name="reference" placeholder="Referencia" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                            <button type="submit" class="btn btn-primary w-full" data-loading-text="Guardando...">Cobrar adelanto</button>
                            <p class="text-xs text-gray-500">Genera el cobro y la factura/boleta de adelanto del presupuesto.</p>
                        </form>
                    </div>
                </div>
            </div>


            {{-- Ampliaciones del presupuesto (siniestro + ampliaciones = grupo) --}}
            @if ($estimate->ampliaciones->isNotEmpty())
                <div class="card mb-4">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">Ampliaciones ({{ $estimate->ampliaciones->count() }})</h3>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 text-sm">
                                <thead>
                                    <tr class="bg-gray-50">
                                        <th class="px-3 py-2 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Documento</th>
                                        <th class="px-3 py-2 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Estado</th>
                                        <th class="px-3 py-2 text-right text-xs font-semibold uppercase tracking-wider text-gray-500">Total</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    @foreach ($estimate->ampliaciones as $amp)
                                        <tr>
                                            <td class="px-3 py-2 font-medium">
                                                <a href="{{ route('estimates.show', $amp) }}" class="text-blue-600 hover:text-blue-800">{{ $amp->document_sn }}</a>
                                            </td>
                                            <td class="px-3 py-2">
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-medium bg-gray-100 text-gray-700">{{ $amp->status_label }}</span>
                                            </td>
                                            <td class="px-3 py-2 text-right">{{ number_format((float) $amp->total, 2) }} {{ $amp->currency }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Garantías registradas contra este presupuesto (reparaciones no facturables) --}}
            @if ($estimate->warrantyClaims->isNotEmpty())
                <div class="card mb-4" id="garantias">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">Garantías ({{ $estimate->warrantyClaims->count() }})</h3>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 text-sm">
                                <thead>
                                    <tr class="bg-gray-50">
                                        <th class="px-3 py-2 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Documento</th>
                                        <th class="px-3 py-2 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Estado</th>
                                        <th class="px-3 py-2 text-right text-xs font-semibold uppercase tracking-wider text-gray-500">Total (costo taller)</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    @foreach ($estimate->warrantyClaims as $claim)
                                        <tr>
                                            <td class="px-3 py-2 font-medium">
                                                <a href="{{ route('estimates.show', $claim) }}" class="text-blue-600 hover:text-blue-800">{{ $claim->document_sn }}</a>
                                            </td>
                                            <td class="px-3 py-2">
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-medium bg-gray-100 text-gray-700">{{ $claim->status_label }}</span>
                                                <span class="ml-1 inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-medium bg-amber-100 text-amber-800">No facturable</span>
                                            </td>
                                            <td class="px-3 py-2 text-right">{{ number_format((float) $claim->total, 2) }} {{ $claim->currency }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Órdenes de compra de terceros --}}
            @if ($estimate->thirdPartyOrders->isNotEmpty())
                <div class="card mb-4">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">Órdenes de compra de terceros</h3>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 text-sm">
                                <thead>
                                    <tr class="bg-gray-50">
                                        <th class="px-3 py-2 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Descripción</th>
                                        <th class="px-3 py-2 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Proveedor</th>
                                        <th class="px-3 py-2 text-right text-xs font-semibold uppercase tracking-wider text-gray-500">Monto sin IGV</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    @foreach ($estimate->thirdPartyOrders as $order)
                                        <tr>
                                            <td class="px-3 py-2 font-medium">{{ $order->description }}</td>
                                            <td class="px-3 py-2 text-gray-600">{{ $order->provider_name ?? '-' }}</td>
                                            <td class="px-3 py-2 text-right">{{ number_format((float) $order->amount_without_iva, 2) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr class="bg-gray-50">
                                        <td colspan="2" class="px-3 py-2 text-right font-medium text-gray-700">Total</td>
                                        <td class="px-3 py-2 text-right font-semibold text-gray-900">{{ number_format($ordersTotal, 2) }}</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Franquicia (informativa) --}}
            @if ($estimate->franchise_amount !== null)
                <div class="card mb-4">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-1">Franquicia</h3>
                        <p class="text-sm text-gray-500 mb-4">Informativa: no descuenta del total del presupuesto.</p>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div class="md:col-span-2"></div>
                            <div class="card">
                                <div class="p-5 space-y-2 text-sm">
                                    <div class="flex justify-between"><span class="text-gray-500">Monto mínimo</span><span class="font-medium">{{ number_format((float) $estimate->franchise_minimum_amount, 2) }}</span></div>
                                    <div class="flex justify-between"><span class="text-gray-500">Mínimo sin IGV</span><span class="font-medium">{{ number_format((float) $estimate->franchise_minimum_without_tax, 2) }}</span></div>
                                    <div class="flex justify-between"><span class="text-gray-500">Base (Base Imponible + OC)</span><span class="font-medium">{{ number_format((float) $estimate->franchise_base, 2) }}</span></div>
                                    <div class="flex justify-between"><span class="text-gray-500">% Aplicado</span><span class="font-medium">{{ number_format((float) $estimate->franchise_percentage_applied, 2) }}</span></div>
                                    <div class="flex justify-between border-t border-gray-200 pt-2 text-base"><span class="font-semibold">Franquicia a pagar (sin IGV)</span><span class="font-semibold text-gray-900">{{ number_format((float) $estimate->franchise_amount, 2) }}</span></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Historial de estados --}}
            <x-status-history :subject="$estimate" />
        </div>
    </div>

    @include('estimates._insurance-modal')

    @include('partials.whatsapp-modal')

    @push('scripts')
    @include('partials.whatsapp-modal-scripts', [
        'actionUrl' => $actionUrl ?? '',
        'recipientsUrl' => $recipientsUrl ?? '',
        'initialMessage' => $initialMessage ?? '',
        'defaultRecipientPhone' => $recipient['contact_phone'] ?? '',
    ])
    @endpush
</x-app-layout>