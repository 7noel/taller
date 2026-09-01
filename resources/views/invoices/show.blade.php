<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div class="flex items-center gap-3">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ $invoice->document_sn ?? 'Borrador' }}</h2>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                    {{ match($invoice->status) { 'accepted' => 'bg-green-50 text-green-700', 'rejected' => 'bg-red-50 text-red-700', 'voided' => 'bg-gray-200 text-gray-500', 'draft' => 'bg-gray-100 text-gray-600', default => 'bg-blue-50 text-blue-700' } }}">
                    {{ $invoice->status_label }}
                </span>
            </div>
            <a href="{{ route('invoices.index') }}" class="btn btn-secondary">Volver</a>
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

            <div class="grid lg:grid-cols-3 gap-4">
                <div class="lg:col-span-2 card p-6">
                    <div class="flex justify-between items-start mb-4">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-800">{{ $invoice->doc_type_label }} · {{ $invoice->type_label }}</h3>
                            <p class="text-sm text-gray-500">{{ $invoice->origin_label }}</p>
                        </div>
                        <div class="text-right text-sm">
                            <p class="font-medium text-gray-800">{{ $invoice->party?->display_name }}</p>
                            <p class="text-gray-500">{{ $invoice->party?->document_type_label }} {{ $invoice->party?->document_number }}</p>
                            @if ($invoice->vehicle)
                                <p class="text-gray-500">{{ $invoice->vehicle->plate }} {{ $invoice->vehicle->vehicleModel?->brand?->name }} {{ $invoice->vehicle->vehicleModel?->name }}</p>
                            @endif
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 text-sm">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-3 py-2 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Descripción</th>
                                    <th class="px-3 py-2 text-right text-xs font-semibold uppercase tracking-wider text-gray-500">Cant.</th>
                                    <th class="px-3 py-2 text-right text-xs font-semibold uppercase tracking-wider text-gray-500">P. Unit</th>
                                    <th class="px-3 py-2 text-right text-xs font-semibold uppercase tracking-wider text-gray-500">Desc.</th>
                                    <th class="px-3 py-2 text-right text-xs font-semibold uppercase tracking-wider text-gray-500">IGV</th>
                                    <th class="px-3 py-2 text-right text-xs font-semibold uppercase tracking-wider text-gray-500">Total</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach ($invoice->items as $item)
                                    <tr class="{{ $item->is_advance_line ? 'bg-amber-50/40' : '' }}">
                                        <td class="px-3 py-2 text-gray-800">
                                            {{ $item->description }}
                                            @if ($item->is_advance_line)<span class="ml-1 text-xs text-amber-600">(regularización adelanto)</span>@endif
                                        </td>
                                        <td class="px-3 py-2 text-right text-gray-600">{{ $item->quantity }}</td>
                                        <td class="px-3 py-2 text-right text-gray-600">{{ number_format($item->unit_price, 2) }}</td>
                                        <td class="px-3 py-2 text-right text-gray-600">{{ number_format($item->discount, 2) }}</td>
                                        <td class="px-3 py-2 text-right text-gray-600">{{ number_format($item->igv, 2) }}</td>
                                        <td class="px-3 py-2 text-right font-medium text-gray-800">{{ number_format($item->total, 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                    <div class="px-6 pb-6">
                        <table class="w-full text-sm">
                            <tfoot>
                                <tr><td class="py-1 text-right text-gray-500">Subtotal</td><td class="py-1 text-right">{{ number_format($invoice->subtotal, 2) }}</td></tr>
                                <tr><td class="py-1 text-right text-gray-500">Descuentos</td><td class="py-1 text-right text-red-600">-{{ number_format($invoice->discount, 2) }}</td></tr>
                                <tr><td class="py-1 text-right text-gray-500">Base imponible</td><td class="py-1 text-right">{{ number_format($invoice->taxable_base, 2) }}</td></tr>
                                <tr><td class="py-1 text-right text-gray-500">IGV</td><td class="py-1 text-right">{{ number_format($invoice->iva, 2) }}</td></tr>
                                @if ($invoice->total_advances)
                                <tr><td class="py-1 text-right text-gray-500">Anticipos</td><td class="py-1 text-right text-blue-600">-{{ number_format($invoice->total_advances, 2) }}</td></tr>
                                @endif
                                <tr class="text-base"><td class="py-1 text-right font-semibold text-gray-800">TOTAL</td><td class="py-1 text-right font-bold text-gray-900">{{ number_format($invoice->total, 2) }}</td></tr>
                            </tfoot>
                        </table>
                        @if ($invoice->observations)
                            <p class="mt-4 text-sm text-gray-500">Obs.: {{ $invoice->observations }}</p>
                        @endif
                        @if ($invoice->estimates->isNotEmpty())
                            <div class="mt-4 border-t pt-3">
                                <p class="text-xs font-semibold uppercase tracking-wider text-gray-500 mb-1">Presupuestos vinculados</p>
                                @foreach ($invoice->estimates as $e)
                                    <a href="{{ route('estimates.show', $e) }}" class="text-sm text-blue-600 hover:underline">{{ $e->document_sn }}</a>
                                    @if (!$loop->last), @endif
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>


                {{-- Columna de acciones --}}
                <div class="space-y-4">
                    @if ($invoice->status === 'draft')
                        <div class="card p-5">
                            <h4 class="font-semibold text-gray-800 mb-3">Emisión</h4>
                            <form method="POST" action="{{ route('invoices.emit', $invoice) }}" class="space-y-3">
                                @csrf
                                <button type="submit" class="btn btn-primary w-full" data-loading-text="Emitiendo...">Emitir comprobante</button>
                            </form>
                            <p class="mt-2 text-xs text-gray-500">Se numerará y enviará al proveedor configurado (Nubefact / Factura Perú).</p>
                        </div>
                    @else
                        <div class="card p-5">
                            <h4 class="font-semibold text-gray-800 mb-3">Respuesta del proveedor</h4>
                            <dl class="text-sm space-y-2">
                                @if ($invoice->accepted_by_sunat !== null)
                                    <div class="flex justify-between"><dt class="text-gray-500">SUNAT</dt><dd class="{{ $invoice->accepted_by_sunat ? 'text-green-600' : 'text-red-600' }} font-medium">{{ $invoice->accepted_by_sunat ? 'Aceptada' : 'Observada/Rechazada' }}</dd></div>
                                @endif
                                @if ($invoice->sunat_description)<div class="flex justify-between gap-2"><dt class="text-gray-500">Detalle</dt><dd class="text-right">{{ $invoice->sunat_description }}</dd></div>@endif
                                @if ($invoice->sunat_responsecode)<div class="flex justify-between"><dt class="text-gray-500">Código</dt><dd>{{ $invoice->sunat_responsecode }}</dd></div>@endif
                                @if ($invoice->external_id)<div class="flex justify-between"><dt class="text-gray-500">External ID</dt><dd class="truncate">{{ $invoice->external_id }}</dd></div>@endif
                                @if ($invoice->enlace_pdf)<div><a href="{{ $invoice->enlace_pdf }}" target="_blank" class="text-blue-600 hover:underline">Ver PDF</a></div>@endif
                                @if ($invoice->enlace_xml)<div><a href="{{ $invoice->enlace_xml }}" target="_blank" class="text-blue-600 hover:underline">Descargar XML</a></div>@endif
                                @if ($invoice->enlace_cdr)<div><a href="{{ $invoice->enlace_cdr }}" target="_blank" class="text-blue-600 hover:underline">Descargar CDR</a></div>@endif
                            </dl>
                        </div>
                    @endif

                    @if (! $invoice->is_note && in_array($invoice->status, ['emitted', 'accepted'], true))
                        <div class="card p-5">
                            <h4 class="font-semibold text-gray-800 mb-3">Notas sobre este documento</h4>
                            <form method="POST" action="{{ route('invoices.credit-note', $invoice) }}" class="space-y-2 mb-4">
                                @csrf
                                <input type="text" name="motivo" placeholder="Motivo de la NC" required class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                                <div class="flex gap-2 items-center">
                                    <input type="number" name="amount" step="0.01" min="0.01" value="{{ $invoice->total }}" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                                    <button type="submit" class="btn btn-secondary shrink-0" data-loading-text="...">NC</button>
                                </div>
                            </form>
                            <form method="POST" action="{{ route('invoices.debit-note', $invoice) }}" class="space-y-2">
                                @csrf
                                <input type="text" name="motivo" placeholder="Motivo de la ND" required class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                                <div class="flex gap-2 items-center">
                                    <input type="number" name="amount" step="0.01" min="0.01" value="{{ $invoice->total }}" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                                    <button type="submit" class="btn btn-secondary shrink-0" data-loading-text="...">ND</button>
                                </div>
                            </form>
                        </div>
                    @endif

                    @if (in_array($invoice->status, ['emitted', 'accepted'], true))
                        <div class="card p-5">
                            <h4 class="font-semibold text-gray-800 mb-3">Anulación</h4>
                            <form method="POST" action="{{ route('invoices.void', $invoice) }}" data-confirm="¿Anular este comprobante? Se enviará la comunicación de baja." class="space-y-2">
                                @csrf
                                <input type="text" name="motivo" placeholder="Motivo de anulación" required class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                                <button type="submit" class="btn btn-danger w-full" data-loading-text="Anulando...">Anular comprobante</button>
                            </form>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

