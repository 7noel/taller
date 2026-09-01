<x-app-layout>
    @php
        $statusColors = [
            'open' => 'bg-gray-100 text-gray-800',
            'in_progress' => 'bg-blue-100 text-blue-800',
            'waiting_parts' => 'bg-yellow-100 text-yellow-800',
            'quality_control' => 'bg-purple-100 text-purple-800',
            'ready_for_delivery' => 'bg-teal-100 text-teal-800',
            'delivered' => 'bg-green-100 text-green-800',
            'delivered_pending' => 'bg-orange-100 text-orange-800',
            'closed' => 'bg-gray-100 text-gray-500',
        ];
        // Botones de transición según el estado actual de la OT.
        $transitionButtons = [
            'open' => [['in_progress', 'Iniciar progreso']],
            'in_progress' => [['waiting_parts', 'Espera de repuestos'], ['quality_control', 'Control de calidad'], ['delivered_pending', 'Entregar con pendientes']],
            'waiting_parts' => [['in_progress', 'Reanudar trabajo'], ['delivered_pending', 'Entregar con pendientes']],
            'quality_control' => [],
            'ready_for_delivery' => [['delivered', 'Entregar'], ['delivered_pending', 'Entregar con pendientes']],
            'delivered' => [['closed', 'Cerrar']],
            'delivered_pending' => [['in_progress', 'Reanudar trabajo'], ['delivered', 'Entrega final']],
            'closed' => [],
        ];
    @endphp

    <x-slot name="header">
        <div class="flex flex-wrap justify-between items-center gap-3">
            <div class="flex flex-wrap items-center gap-3">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Orden de Trabajo') }}</h2>
                @if ($workOrder->document_sn)
                    <x-document-badge :sn="$workOrder->document_sn" />
                @endif
                @if ($workOrder->vehicle?->plate)
                    <span class="text-sm text-gray-500">{{ $workOrder->vehicle->plate }}</span>
                @endif
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusColors[$workOrder->status] ?? 'bg-gray-100 text-gray-800' }}">
                    {{ $workOrder->status_label }}
                </span>
            </div>
            <div class="flex flex-wrap gap-2">
                @can('changeStatus', $workOrder)
                    @foreach ($transitionButtons[$workOrder->status] ?? [] as [$to, $label])
                        <form method="POST" action="{{ route('work-orders.transition', $workOrder) }}">
                            @csrf
                            <input type="hidden" name="status" value="{{ $to }}">
                            <button type="submit" class="btn btn-primary">{{ $label }}</button>
                        </form>
                    @endforeach
                    @if ($workOrder->status === 'delivered_pending')
                        <a href="{{ route('check-ins.create', ['work_order_id' => $workOrder->id]) }}" class="btn btn-secondary" title="Registrar la visita del vehículo y retomar la OT automáticamente">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 3h4a2 2 0 012 2v14a2 2 0 01-2 2h-4M10 17l5-5-5-5M15 12H3"/></svg>
                            Registrar reingreso
                        </a>
                    @endif
                    @if ($workOrder->status === 'quality_control')
                        <a href="{{ route('work-orders.quality-control', $workOrder) }}" class="btn btn-primary">Realizar control de calidad</a>
                    @endif
                    @if (in_array($workOrder->status, ['in_progress', 'waiting_parts'], true) && auth()->user()->can('crear movimientos'))
                        <button type="button" id="btn-stock-exit" class="btn btn-secondary">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                            Salida de repuestos
                        </button>
                    @endif
                @endcan
                @if (in_array($workOrder->status, ['ready_for_delivery', 'delivered'], true) && !empty($readyLink))
                    @if ($workOrder->status === 'ready_for_delivery')
                        <button type="button" data-whatsapp-open="ready" data-whatsapp-message="{{ $readyMessage }}" class="btn btn-secondary">
                            <svg class="h-4 w-4 shrink-0" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2a10 10 0 00-8.6 15.1L2 22l5-1.3A10 10 0 1012 2zm5.2 14.2c-.2.6-1.2 1.2-1.7 1.2-.4.1-1 .1-1.6-.1-.4-.1-.9-.3-1.5-.6-2.6-1.1-4.3-3.8-4.4-4-.1-.2-1.1-1.4-1.1-2.7s.7-1.9.9-2.1c.2-.3.5-.3.7-.3h.5c.2 0 .4-.1.6.4.2.6.7 2 .8 2.1.1.1.1.3 0 .5-.1.2-.1.3-.3.5l-.4.5c-.1.1-.3.3-.1.5.2.3.8 1.3 1.7 2.1 1.2 1.1 2.1 1.4 2.4 1.5.3.1.5.1.7-.1l1-1.2c.2-.3.4-.2.7-.1l2 .9c.3.2.5.3.6.4.1.2.1.7-.1 1.3z"/></svg>
                            Avisar al cliente (WhatsApp)
                        </button>
                        <button type="button" data-copy-message="{{ $readyMessage }}" title="Copiar todo el mensaje para el cliente" class="btn btn-secondary">Copiar mensaje</button>
                    @else
                        <button type="button" data-whatsapp-open="survey" data-whatsapp-message="{{ $surveyMessage }}" class="btn btn-secondary">
                            <svg class="h-4 w-4 shrink-0" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2a10 10 0 00-8.6 15.1L2 22l5-1.3A10 10 0 1012 2zm5.2 14.2c-.2.6-1.2 1.2-1.7 1.2-.4.1-1 .1-1.6-.1-.4-.1-.9-.3-1.5-.6-2.6-1.1-4.3-3.8-4.4-4-.1-.2-1.1-1.4-1.1-2.7s.7-1.9.9-2.1c.2-.3.5-.3.7-.3h.5c.2 0 .4-.1.6.4.2.6.7 2 .8 2.1.1.1.1.3 0 .5-.1.2-.1.3-.3.5l-.4.5c-.1.1-.3.3-.1.5.2.3.8 1.3 1.7 2.1 1.2 1.1 2.1 1.4 2.4 1.5.3.1.5.1.7-.1l1-1.2c.2-.3.4-.2.7-.1l2 .9c.3.2.5.3.6.4.1.2.1.7-.1 1.3z"/></svg>
                            Enviar encuesta (WhatsApp)
                        </button>
                        <button type="button" data-copy-message="{{ $surveyMessage }}" title="Copiar todo el mensaje para el cliente" class="btn btn-secondary">Copiar mensaje</button>
                    @endif
                @endif
                @if ($workOrder->status === 'closed' && auth()->user()->can('changeStatus', $workOrder))
                    <form method="POST" action="{{ route('work-orders.reopen', $workOrder) }}" data-confirm="¿Reabrir esta OT? Se usará para registrar una garantía o un siniestro del vehículo.">
                        @csrf
                        <input type="hidden" name="reason" value="Reapertura por garantía o siniestro">
                        <button type="submit" class="btn btn-secondary" title="El vehículo regresó por garantía o siniestro">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                            Reabrir OT
                        </button>
                    </form>
                @endif
                @can('delete', $workOrder)
                    <form method="POST" action="{{ route('work-orders.destroy', $workOrder) }}" data-confirm="¿Eliminar esta orden de trabajo? Los presupuestos volverán a estado aprobado.">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-danger">Eliminar</button>
                    </form>
                @endcan
            </div>
        </div>
    </x-slot>
    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="mb-4 px-4 py-3 bg-green-50 border border-green-200 text-green-700 rounded-lg text-sm">{{ session('success') }}</div>
            @endif
            @if ($errors->any())
                <div class="mb-4 px-4 py-3 bg-red-50 border border-red-200 text-red-700 rounded-lg text-sm">{{ $errors->first() }}</div>
            @endif

            @if (!$workOrder->is_final)
                <div class="mb-4 px-4 py-3 bg-blue-50 border border-blue-200 text-blue-700 rounded-lg text-sm">
                    <strong>Próxima acción:</strong> {{ $workOrder->next_action }}
                </div>
            @endif

            {{-- Información general --}}
            <div class="card mb-4">
                <div class="p-4 sm:p-5">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 text-sm">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Vehículo</p>
                            <p class="font-medium text-gray-800">
                                {{ $workOrder->vehicle?->plate }}
                                <span class="text-gray-500">· {{ $workOrder->vehicle?->vehicleModel?->brand?->name }} {{ $workOrder->vehicle?->vehicleModel?->name }} ({{ $workOrder->vehicle?->year }})</span>
                            </p>
                            @if ($workOrder->vehicle?->vin)
                                <p class="text-xs text-gray-500">VIN: {{ $workOrder->vehicle->vin }}</p>
                            @endif
                        </div>
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Cliente</p>
                            <p class="font-medium text-gray-800">{{ $workOrder->client?->display_name }}</p>
                            @if ($workOrder->client?->document_number)
                                <p class="text-xs text-gray-500">{{ $workOrder->client->document_number }}</p>
                            @endif
                        </div>
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Fechas</p>
                            <p class="text-gray-800">
                                Inicio: {{ $workOrder->start_date?->format('d/m/Y') ?? '—' }}
                                · Fin estimado: {{ $workOrder->estimated_end_date?->format('d/m/Y') ?? '—' }}
                            </p>
                        </div>
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Establecimiento</p>
                            <p class="text-gray-800">{{ $workOrder->establishment?->name }}</p>
                        </div>
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Total (presupuestos vinculados)</p>
                            <p class="font-semibold text-gray-800">{{ $costSummary['display_currency'] === 'USD' ? 'US$' : 'S/' }} {{ number_format($costSummary['income'], 2) }}</p>
                        </div>
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Creada por</p>
                            <p class="text-gray-800">{{ $workOrder->creator?->name }} · {{ $workOrder->created_at?->format('d/m/Y H:i') }}</p>
                        </div>
                    </div>
                    @if ($workOrder->notes)
                        <div class="mt-4 pt-4 border-t border-gray-100">
                            <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Notas</p>
                            <p class="text-sm text-gray-700 whitespace-pre-line">{{ $workOrder->notes }}</p>
                        </div>
                    @endif
                </div>
            </div>
            {{-- Presupuestos de la OT --}}
            <div class="card mb-4">
                <div class="p-4 sm:p-5">
                    <div class="flex flex-wrap justify-between items-center gap-3 mb-3">
                        <h3 class="font-semibold text-sm text-gray-800 uppercase tracking-wider">Presupuestos vinculados ({{ $workOrder->estimates->count() }})</h3>
                        @can('attachEstimate', $workOrder)
                            @if ($availableEstimates->isNotEmpty() && !$workOrder->is_final)
                                <form method="POST" action="{{ route('work-orders.attach-estimate', $workOrder) }}" class="flex flex-wrap gap-2 items-center">
                                    @csrf
                                    <select name="estimate_id" required class="rounded-md border-gray-300 text-sm">
                                        <option value="">Presupuesto aprobado...</option>
                                        @foreach ($availableEstimates as $estimate)
                                            <option value="{{ $estimate->id }}">{{ $estimate->document_sn }} · S/ {{ number_format((float) $estimate->total, 2) }}</option>
                                        @endforeach
                                    </select>
                                    <button type="submit" class="btn btn-secondary">Anexar presupuesto</button>
                                </form>
                            @endif
                        @endcan
                    </div>

                    @if ($workOrder->estimates->isEmpty())
                        <p class="text-sm text-gray-500">Esta orden de trabajo no tiene presupuestos vinculados.</p>
                    @else
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 text-sm">
                                <thead>
                                    <tr class="bg-gray-50">
                                        <th class="px-3 py-2 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Presupuesto</th>
                                        <th class="px-3 py-2 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Tipo</th>
                                        <th class="px-3 py-2 text-right text-xs font-semibold uppercase tracking-wider text-gray-500">Total</th>
                                        <th class="px-3 py-2 text-center text-xs font-semibold uppercase tracking-wider text-gray-500">Estado</th>
                                        <th class="px-3 py-2 text-center text-xs font-semibold uppercase tracking-wider text-gray-500">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    @foreach ($workOrder->estimates as $estimate)
                                        <tr class="hover:bg-blue-50/50">
                                            <td class="px-3 py-2 font-medium text-blue-600">
                                                <a href="{{ route('estimates.show', $estimate) }}">{{ $estimate->document_sn }}</a>
                                            </td>
                                            <td class="px-3 py-2">
                                                <span class="text-gray-600">{{ $estimate->service_type_label }}</span>
                                                @if ($estimate->is_garantia)
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-800 ml-1" title="Garantía del presupuesto {{ $estimate->warrantyOf?->document_sn }}">
                                                        Garantía de {{ $estimate->warrantyOf?->document_sn ?? '—' }}
                                                    </span>
                                                @endif
                                                @if ($estimate->is_chargeable === false)
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-600 ml-1">No facturable</span>
                                                @endif
                                                @if ($estimate->liability === 'workshop' && !$estimate->is_garantia)
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-700 ml-1">Resp. taller</span>
                                                @endif
                                            </td>
                                            <td class="px-3 py-2 text-right text-gray-800">S/ {{ number_format((float) $estimate->total, 2) }}</td>
                                            <td class="px-3 py-2 text-center">
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">En reparación</span>
                                            </td>
                                            <td class="px-3 py-2 text-center">
                                                <div class="flex gap-2 justify-center">
                                                    <a href="{{ route('estimates.show', $estimate) }}" title="Ver presupuesto" class="btn-icon btn-icon-blue">
                                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                                    </a>
                                                    @can('attachEstimate', $workOrder)
                                                        @if ($estimate->is_chargeable !== false)
                                                            <a href="{{ route('estimates.create', ['warranty_of' => $estimate->id, 'work_order_id' => $workOrder->id]) }}" title="Registrar garantía de este presupuesto" class="btn-icon btn-icon-amber">
                                                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                                                            </a>
                                                        @endif
                                                        <form method="POST" action="{{ route('work-orders.detach-estimate', [$workOrder, $estimate]) }}" data-confirm="¿Desvincular el presupuesto {{ $estimate->document_sn }}? Volverá a estado aprobado.">
                                                            @csrf @method('DELETE')
                                                            <button type="submit" title="Desvincular presupuesto" class="btn-icon btn-icon-amber">
                                                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 5l7 7-7 7M5 12h15"/></svg>
                                                            </button>
                                                        </form>
                                                    @endcan
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
            {{-- Costos y utilidad de la OT --}}
            <div class="card mb-4">
                <div class="p-4 sm:p-5">
                    <div class="flex flex-wrap justify-between items-center gap-3 mb-3">
                        <h3 class="font-semibold text-sm text-gray-800 uppercase tracking-wider">Costos y utilidad</h3>
                        @can('crear vales de servicio')
                            <a href="{{ route('service-vouchers.create', ['work_order_id' => $workOrder->id]) }}" class="btn btn-secondary" title="Registrar planchado, pintura u otro servicio a un tercero">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                                Nuevo vale (tercero)
                            </a>
                        @endcan
                    </div>

                    @php $costSym = $costSummary['display_currency'] === 'USD' ? 'US$' : 'S/'; @endphp

                    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 mb-4">
                        <div class="rounded-lg bg-gray-50 border border-gray-200 p-3">
                            <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Ingresos (presupuestos)</p>
                            <p class="text-lg font-bold text-gray-900">{{ $costSym }} {{ number_format($costSummary['income'], 2) }}</p>
                            <p class="text-xs text-gray-500">S/ {{ number_format($costSummary['income_pen'], 2) }} en PEN</p>
                        </div>
                        <div class="rounded-lg bg-gray-50 border border-gray-200 p-3">
                            <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Costos</p>
                            <p class="text-lg font-bold text-gray-900">{{ $costSym }} {{ number_format($costSummary['total_cost'], 2) }}</p>
                            <p class="text-xs text-gray-500">S/ {{ number_format($costSummary['total_cost_pen'], 2) }} en PEN</p>
                        </div>
                        <div class="rounded-lg border p-3 {{ $costSummary['profit'] >= 0 ? 'bg-green-50 border-green-200' : 'bg-red-50 border-red-200' }}">
                            <p class="text-xs font-semibold uppercase tracking-wider {{ $costSummary['profit'] >= 0 ? 'text-green-700' : 'text-red-700' }}">Utilidad estimada</p>
                            <p class="text-lg font-bold {{ $costSummary['profit'] >= 0 ? 'text-green-700' : 'text-red-700' }}">{{ $costSym }} {{ number_format($costSummary['profit'], 2) }}</p>
                            <p class="text-xs {{ $costSummary['profit'] >= 0 ? 'text-green-600' : 'text-red-600' }}">S/ {{ number_format($costSummary['profit_pen'], 2) }} en PEN</p>
                        </div>
                        <div class="rounded-lg bg-blue-50 border border-blue-200 p-3">
                            <p class="text-xs font-semibold uppercase tracking-wider text-blue-700">Margen</p>
                            <p class="text-lg font-bold text-blue-700">{{ number_format($costSummary['margin'], 1) }}%</p>
                            <p class="text-xs text-blue-600">Sobre ingresos (PEN)</p>
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 text-sm">
                            <thead>
                                <tr class="bg-gray-50">
                                    <th class="px-3 py-2 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Componente</th>
                                    <th class="px-3 py-2 text-right text-xs font-semibold uppercase tracking-wider text-gray-500">Costo ({{ $costSummary['display_currency'] }})</th>
                                    <th class="px-3 py-2 text-right text-xs font-semibold uppercase tracking-wider text-gray-500">Equivalente PEN</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach ($costSummary['components'] as $component)
                                    <tr class="hover:bg-blue-50/50">
                                        <td class="px-3 py-2 text-gray-800">
                                            {{ $component['label'] }}
                                            <span class="text-xs text-gray-500">({{ $component['count'] }})</span>
                                            @if ($component['mixed_currency'])
                                                <span class="ml-1 inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-800">monedas mixtas</span>
                                            @endif
                                        </td>
                                        <td class="px-3 py-2 text-right font-medium text-gray-800">{{ $costSym }} {{ number_format($component['amount_display'], 2) }}</td>
                                        <td class="px-3 py-2 text-right text-gray-500">S/ {{ number_format($component['amount_pen'], 2) }}</td>
                                    </tr>
                                @endforeach
                                <tr class="bg-gray-50">
                                    <td class="px-3 py-2 font-semibold text-gray-800">Costo total</td>
                                    <td class="px-3 py-2 text-right font-bold text-gray-900">{{ $costSym }} {{ number_format($costSummary['total_cost'], 2) }}</td>
                                    <td class="px-3 py-2 text-right font-semibold text-gray-700">S/ {{ number_format($costSummary['total_cost_pen'], 2) }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <p class="mt-3 text-xs text-gray-500">
                        Moneda de visualización: {{ $costSummary['display_currency'] }} (T.C. snapshot {{ number_format($costSummary['display_rate'], 4) }} · soles por 1 dólar).
                        Cada costo se registra en su moneda original y se normaliza a soles (PEN) para el cálculo de utilidad.
                    </p>
                </div>
            </div>

            {{-- Gastos internos (responsabilidad del taller) --}}
            <div class="card mb-4">
                <div class="p-4 sm:p-5">
                    <div class="flex flex-wrap justify-between items-center gap-3 mb-3">
                        <h3 class="font-semibold text-sm text-gray-800 uppercase tracking-wider">Gastos internos ({{ $workOrder->internalExpenses->count() }})</h3>
                        <span class="text-xs text-gray-500">Errores asumidos por el taller: arañazos, repuestos malogrados, etc.</span>
                    </div>

                    @if ($workOrder->internalExpenses->isNotEmpty())
                        <div class="overflow-x-auto mb-4">
                            <table class="min-w-full divide-y divide-gray-200 text-sm">
                                <thead>
                                    <tr class="bg-gray-50">
                                        <th class="px-3 py-2 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Tipo</th>
                                        <th class="px-3 py-2 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Detalle</th>
                                        <th class="px-3 py-2 text-right text-xs font-semibold uppercase tracking-wider text-gray-500">Monto</th>
                                        <th class="px-3 py-2 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Responsable</th>
                                        <th class="px-3 py-2 text-center text-xs font-semibold uppercase tracking-wider text-gray-500">Fecha</th>
                                        <th class="px-3 py-2 text-center text-xs font-semibold uppercase tracking-wider text-gray-500"></th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    @foreach ($workOrder->internalExpenses as $expense)
                                        <tr class="hover:bg-blue-50/50">
                                            <td class="px-3 py-2"><span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-700">{{ $expense->type_label }}</span></td>
                                            <td class="px-3 py-2 text-gray-700">{{ $expense->description ?: '—' }}</td>
                                            <td class="px-3 py-2 text-right font-medium text-gray-800">{{ $expense->currency === 'USD' ? 'US$' : 'S/' }} {{ number_format((float) $expense->amount, 2) }}</td>
                                            <td class="px-3 py-2 text-gray-600">{{ $expense->responsible?->name ?? '—' }}</td>
                                            <td class="px-3 py-2 text-center text-gray-600">{{ $expense->occurred_at?->format('d/m/Y') }}</td>
                                            <td class="px-3 py-2 text-center">
                                                @can('update', $workOrder)
                                                    <form method="POST" action="{{ route('work-orders.internal-expenses.destroy', [$workOrder, $expense]) }}" data-confirm="¿Eliminar este gasto interno?">
                                                        @csrf @method('DELETE')
                                                        <button type="submit" title="Eliminar gasto interno" class="btn-icon btn-icon-red">
                                                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                        </button>
                                                    </form>
                                                @endcan
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-sm text-gray-500 mb-4">No hay gastos internos registrados.</p>
                    @endif

                    @can('update', $workOrder)
                        <form method="POST" action="{{ route('work-orders.internal-expenses.store', $workOrder) }}" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-3 items-end border-t border-gray-100 pt-4">
                            @csrf
                            <div>
                                <label class="block text-xs font-medium text-gray-700">Tipo <span class="text-red-500">*</span></label>
                                <select name="type" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                                    @foreach (\App\Models\WorkOrderInternalExpense::TYPES as $key => $label)
                                        <option value="{{ $key }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-700">Detalle</label>
                                <input type="text" name="description" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-700">Monto <span class="text-red-500">*</span></label>
                                <input type="number" step="0.01" min="0" name="amount" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm text-right">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-700">Responsable (técnico)</label>
                                <select name="responsible_user_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                                    <option value="">—</option>
                                    @foreach ($technicians as $tech)
                                        <option value="{{ $tech->id }}">{{ $tech->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-700">Fecha</label>
                                <input type="date" name="occurred_at" value="{{ now()->toDateString() }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                            </div>
                            <div class="lg:col-span-5 flex justify-end">
                                <button type="submit" class="btn btn-secondary">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                                    Registrar gasto interno
                                </button>
                            </div>
                        </form>
                    @endcan
                </div>
            </div>

            {{-- Servicios tercerizados (vales CST01) --}}
            <div class="card mb-4">
                <div class="p-4 sm:p-5">
                    <div class="flex flex-wrap justify-between items-center gap-3 mb-3">
                        <h3 class="font-semibold text-sm text-gray-800 uppercase tracking-wider">Servicios tercerizados ({{ $workOrder->serviceVouchers->count() }})</h3>
                        @can('crear vales de servicio')
                            <a href="{{ route('service-vouchers.create', ['work_order_id' => $workOrder->id]) }}" class="btn btn-secondary" title="Registrar planchado, pintura u otro servicio a un tercero">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                                Nuevo vale
                            </a>
                        @endcan
                    </div>

                    @if ($workOrder->serviceVouchers->isEmpty())
                        <p class="text-sm text-gray-500">Sin vales registrados. Usa esta sección para asignar planchado, pintura u otros trabajos a maestros o proveedores externos.</p>
                    @else
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 text-sm">
                                <thead>
                                    <tr class="bg-gray-50">
                                        <th class="px-3 py-2 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Documento</th>
                                        <th class="px-3 py-2 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Proveedor</th>
                                        <th class="px-3 py-2 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Descripción</th>
                                        <th class="px-3 py-2 text-right text-xs font-semibold uppercase tracking-wider text-gray-500">Base (sin IGV)</th>
                                        <th class="px-3 py-2 text-center text-xs font-semibold uppercase tracking-wider text-gray-500">Estado</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    @foreach ($workOrder->serviceVouchers as $voucher)
                                        @php
                                            $voucherColors = [
                                                'pending' => 'bg-amber-100 text-amber-800',
                                                'completed' => 'bg-blue-100 text-blue-800',
                                                'liquidated' => 'bg-green-100 text-green-800',
                                            ];
                                            $vSym = ($voucher->currency ?? 'PEN') === 'USD' ? 'US$' : 'S/';
                                        @endphp
                                        <tr class="hover:bg-blue-50/50">
                                            <td class="px-3 py-2">
                                                <a href="{{ route('service-vouchers.show', $voucher) }}" class="font-mono text-blue-600 hover:text-blue-800">{{ $voucher->document_sn }}</a>
                                            </td>
                                            <td class="px-3 py-2 text-gray-700">{{ $voucher->provider?->display_name }}</td>
                                            <td class="px-3 py-2 text-gray-600 max-w-xs truncate" title="{{ $voucher->description }}">{{ $voucher->description }}</td>
                                            <td class="px-3 py-2 text-right text-gray-800">{{ $vSym }} {{ number_format($voucher->base_amount, 2) }}</td>
                                            <td class="px-3 py-2 text-center">
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $voucherColors[$voucher->status] ?? 'bg-gray-100 text-gray-800' }}">{{ \App\Models\ServiceVoucher::STATUS_LABELS[$voucher->status] ?? $voucher->status }}</span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Subetapas y técnicos --}}
            <div class="card mb-4">
                <div class="p-4 sm:p-5">
                    <div class="flex flex-wrap justify-between items-center gap-3 mb-3">
                        <h3 class="font-semibold text-sm text-gray-800 uppercase tracking-wider">Subetapas y técnicos ({{ $workOrder->assignments->count() }})</h3>
                    </div>

                    @can('manageAssignments', $workOrder)
                        @if (!$workOrder->is_final)
                            <form method="POST" action="{{ route('work-orders.assignments.store', $workOrder) }}" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-6 gap-2 mb-4 items-end">
                                @csrf
                                <div class="lg:col-span-2">
                                    <label class="block text-xs font-medium text-gray-700">Subetapa <span class="text-red-500">*</span></label>
                                    <select name="substage_id" required class="mt-1 w-full rounded-md border-gray-300 text-sm">
                                        <option value="">Seleccionar...</option>
                                        @foreach ($substages as $substage)
                                            <option value="{{ $substage->id }}">{{ $substage->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-700">Técnico</label>
                                    <select name="user_id" class="mt-1 w-full rounded-md border-gray-300 text-sm">
                                        <option value="">—</option>
                                        @foreach ($technicians as $technician)
                                            <option value="{{ $technician->id }}">{{ $technician->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-700">Horas</label>
                                    <input type="number" name="hours" min="0" step="0.25" value="0" class="mt-1 w-full rounded-md border-gray-300 text-sm">
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-700">Costo</label>
                                    <input type="number" name="cost" min="0" step="0.01" value="0" class="mt-1 w-full rounded-md border-gray-300 text-sm">
                                </div>
                                <div>
                                    <button type="submit" class="btn btn-primary w-full">Agregar</button>
                                </div>
                            </form>
                        @endif
                    @endcan

                    @if ($workOrder->assignments->isEmpty())
                        <p class="text-sm text-gray-500">No hay asignaciones registradas.</p>
                    @else
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 text-sm">
                                <thead>
                                    <tr class="bg-gray-50">
                                        <th class="px-3 py-2 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Subetapa</th>
                                        <th class="px-3 py-2 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Técnico</th>
                                        <th class="px-3 py-2 text-right text-xs font-semibold uppercase tracking-wider text-gray-500">Horas</th>
                                        <th class="px-3 py-2 text-right text-xs font-semibold uppercase tracking-wider text-gray-500">Costo</th>
                                        <th class="px-3 py-2 text-center text-xs font-semibold uppercase tracking-wider text-gray-500">Estado</th>
                                        <th class="px-3 py-2 text-center text-xs font-semibold uppercase tracking-wider text-gray-500">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    @foreach ($workOrder->assignments as $assignment)
                                        @php
                                            $assignmentColors = [
                                                'pending' => 'bg-gray-100 text-gray-800',
                                                'in_progress' => 'bg-blue-100 text-blue-800',
                                                'done' => 'bg-green-100 text-green-800',
                                            ];
                                        @endphp
                                        <tr class="hover:bg-blue-50/50">
                                            <td class="px-3 py-2 font-medium text-gray-800">{{ $assignment->substage?->name }}</td>
                                            <td class="px-3 py-2 text-gray-600">{{ $assignment->user?->name ?? '—' }}</td>
                                            <td class="px-3 py-2 text-right text-gray-800">{{ number_format((float) $assignment->hours, 2) }}</td>
                                            <td class="px-3 py-2 text-right text-gray-800">S/ {{ number_format((float) $assignment->cost, 2) }}</td>
                                            <td class="px-3 py-2 text-center">
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $assignmentColors[$assignment->status] ?? 'bg-gray-100 text-gray-800' }}">{{ $assignment->status_label }}</span>
                                            </td>
                                            <td class="px-3 py-2 text-center">
                                                <div class="flex gap-1 justify-center">
                                                    @can('manageAssignments', $workOrder)
                                                        @foreach (\App\Models\WorkOrderAssignment::TRANSITIONS[$assignment->status] ?? [] as $next)
                                                            <form method="POST" action="{{ route('work-orders.assignments.status', [$workOrder, $assignment]) }}">
                                                                @csrf
                                                                <input type="hidden" name="status" value="{{ $next }}">
                                                                <button type="submit" class="px-2 py-1 text-xs font-medium rounded-md border border-gray-300 text-gray-700 hover:bg-gray-50" title="Cambiar a {{ \App\Models\WorkOrderAssignment::STATUS_LABELS[$next] }}">
                                                                    {{ \App\Models\WorkOrderAssignment::STATUS_LABELS[$next] }}
                                                                </button>
                                                            </form>
                                                        @endforeach
                                                        <form method="POST" action="{{ route('work-orders.assignments.destroy', [$workOrder, $assignment]) }}" data-confirm="¿Eliminar esta asignación?">
                                                            @csrf @method('DELETE')
                                                            <button type="submit" title="Eliminar asignación" class="btn-icon btn-icon-red">
                                                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                            </button>
                                                        </form>
                                                    @endcan
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Control de calidad (historial) --}}
            @if ($workOrder->qualityControls->isNotEmpty())
                <div class="card mb-4">
                    <div class="p-4 sm:p-5">
                        <h3 class="font-semibold text-sm text-gray-800 uppercase tracking-wider mb-3">Control de calidad ({{ $workOrder->qualityControls->count() }})</h3>
                        <ul class="divide-y divide-gray-100 text-sm">
                            @foreach ($workOrder->qualityControls as $qc)
                                <li class="py-2 flex flex-wrap items-center justify-between gap-2">
                                    <div>
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $qc->result === 'approved' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                            {{ $qc->result_label }}
                                        </span>
                                        @if ($qc->result === 'rejected' && $qc->rejection_reason_label)
                                            <span class="ml-2 text-gray-700">Causa: {{ $qc->rejection_reason_label }}</span>
                                        @endif
                                    </div>
                                    <span class="text-xs text-gray-500">
                                        {{ $qc->reviewer?->name ?? '—' }} · {{ $qc->reviewed_at?->format('d/m/Y H:i') }}
                                    </span>
                                </li>
                                @if ($qc->rejection_details)
                                    <li class="pb-2 text-xs text-gray-500">Detalle: {{ $qc->rejection_details }}</li>
                                @endif
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif

            {{-- Visitas (check-ins) vinculados --}}
            <div class="card mb-4">
                <div class="p-4 sm:p-5">
                    <h3 class="font-semibold text-sm text-gray-800 uppercase tracking-wider mb-3">Visitas del vehículo ({{ $workOrder->checkIns->count() }})</h3>
                    @if ($workOrder->checkIns->isEmpty())
                        <p class="text-sm text-gray-500">Esta OT se generó sin inventario asociado (presupuesto directo).</p>
                    @else
                        <ul class="divide-y divide-gray-100 text-sm">
                            @foreach ($workOrder->checkIns as $checkIn)
                                <li class="flex items-center justify-between py-2">
                                    <div>
                                        <a href="{{ route('check-ins.show', $checkIn) }}" class="font-medium text-blue-600 hover:text-blue-800">{{ $checkIn->document_sn }}</a>
                                        <span class="text-gray-500"> · {{ $checkIn->created_at?->format('d/m/Y') }} · {{ $checkIn->service_type_label }}</span>
                                    </div>
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-700">{{ $checkIn->status_label }}</span>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>

            {{-- Historial de estados --}}
            <x-status-history :subject="$workOrder" />
        </div>
    </div>

    {{-- Modal: salida de repuestos (NSA1 motivo 10) --}}
    @if (in_array($workOrder->status, ['in_progress', 'waiting_parts'], true) && auth()->user()->can('crear movimientos'))
        <div id="stock-exit-modal" class="hidden fixed inset-0 z-50 overflow-y-auto" aria-hidden="true" role="dialog" aria-modal="true">
            <div class="fixed inset-0 bg-gray-900/50" data-close-stock-exit></div>
            <div class="relative min-h-screen flex items-center justify-center p-4">
                <div class="relative w-full max-w-md rounded-lg bg-white shadow-xl">
                    <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-800">Salida de repuestos</h3>
                        <button type="button" data-close-stock-exit class="text-gray-400 hover:text-gray-600" title="Cerrar">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>
                    <form method="POST" action="{{ route('work-orders.stock-exit', $workOrder) }}" class="px-6 py-5 space-y-4" id="stock-exit-form">
                        @csrf
                        <p class="text-xs text-gray-500">Se emitirá una <strong>NSA1 (Guía de Salida)</strong> con motivo <strong>10 · Salida a producción</strong> vinculada a la OT {{ $workOrder->document_sn }}.</p>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Repuesto <span class="text-red-500">*</span></label>
                            <select id="exit-part" name="part_id" placeholder="Buscar repuesto..."></select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Almacén <span class="text-red-500">*</span></label>
                            <select id="exit-warehouse" name="warehouse_id" placeholder="Buscar almacén..."></select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Cantidad <span class="text-red-500">*</span></label>
                            <input type="number" step="0.01" min="0" name="quantity" value="1" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-right">
                        </div>
                        <div class="flex justify-end gap-2 pt-2">
                            <button type="button" data-close-stock-exit class="btn btn-secondary">Cancelar</button>
                            <button type="submit" id="stock-exit-submit" class="btn btn-primary">Registrar salida</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        @push('scripts')
        <script>
            const seModal = document.getElementById('stock-exit-modal');
            const seOpenBtn = document.getElementById('btn-stock-exit');
            function seOpen() { seModal.classList.remove('hidden'); document.body.classList.add('overflow-hidden'); }
            function seClose() { seModal.classList.add('hidden'); document.body.classList.remove('overflow-hidden'); }
            seOpenBtn.addEventListener('click', seOpen);
            document.querySelectorAll('[data-close-stock-exit]').forEach(b => b.addEventListener('click', seClose));

            if (window.TomSelect) {
                new TomSelect('#exit-part', {
                    valueField: 'id', labelField: 'name', searchField: ['name', 'sku', 'barcode'],
                    maxItems: 1, closeAfterSelect: true, create: false, copyClassesToDropdown: false, dropdownParent: 'body',
                    load: (q, cb) => fetch(`{{ route('api.parts.search') }}?q=${encodeURIComponent(q)}`).then(r => r.json()).then(cb).catch(() => cb())
                });
                new TomSelect('#exit-warehouse', {
                    valueField: 'id', labelField: 'name', searchField: ['name', 'code'],
                    maxItems: 1, closeAfterSelect: true, create: false, copyClassesToDropdown: false, dropdownParent: 'body',
                    load: (q, cb) => fetch(`{{ route('api.warehouses.search') }}?q=${encodeURIComponent(q)}`).then(r => r.json()).then(cb).catch(() => cb())
                });
            }

            let seSaving = false;
            document.getElementById('stock-exit-form').addEventListener('submit', function (e) {
                if (seSaving) { e.preventDefault(); return; }
                seSaving = true;
                const btn = document.getElementById('stock-exit-submit');
                btn.disabled = true;
                btn.textContent = 'Registrando...';
            });
        </script>
        @endpush
    @endif

    @include('partials.whatsapp-modal')
    @include('partials.whatsapp-modal-scripts', [
        'actionUrl' => $actionUrl ?? '',
        'recipientsUrl' => $recipientsUrl ?? '',
        'initialMessage' => $readyMessage ?? '',
    ])
</x-app-layout>
