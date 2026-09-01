<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div class="flex items-center gap-3">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ $dispatch->document_sn ?? 'Borrador' }}</h2>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                    {{ match($dispatch->status) { 'accepted' => 'bg-green-50 text-green-700', 'rejected' => 'bg-red-50 text-red-700', 'voided' => 'bg-gray-200 text-gray-500', 'draft' => 'bg-gray-100 text-gray-600', default => 'bg-blue-50 text-blue-700' } }}">
                    {{ $dispatch->status_label }}
                </span>
            </div>
            <a href="{{ route('dispatches.index') }}" class="btn btn-secondary">Volver</a>
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
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">{{ $dispatch->type_label }}</h3>
                    <dl class="grid grid-cols-2 gap-4 text-sm">
                        <div><dt class="text-gray-500">Destinatario</dt><dd class="font-medium text-gray-800">{{ $dispatch->party?->display_name ?? '—' }}</dd></div>
                        <div><dt class="text-gray-500">Motivo</dt><dd>{{ $dispatch->motivo_traslado_label }}</dd></div>
                        <div><dt class="text-gray-500">Fecha de traslado</dt><dd>{{ $dispatch->fecha_de_traslado?->format('d/m/Y') }}</dd></div>
                        <div><dt class="text-gray-500">Transporte</dt><dd>{{ $dispatch->modo_transporte === '01' ? 'Público' : 'Privado' }}</dd></div>
                        <div><dt class="text-gray-500">Partida</dt><dd>{{ $dispatch->punto_partida_direccion }} ({{ $dispatch->punto_partida_ubigeo }})</dd></div>
                        <div><dt class="text-gray-500">Llegada</dt><dd>{{ $dispatch->punto_llegada_direccion }} ({{ $dispatch->punto_llegada_ubigeo }})</dd></div>
                        <div><dt class="text-gray-500">Placa</dt><dd>{{ $dispatch->vehiculo_placa ?? '—' }}</dd></div>
                        <div><dt class="text-gray-500">Peso / Bultos</dt><dd>{{ $dispatch->peso_total }} {{ $dispatch->unidad_peso }} / {{ $dispatch->numero_de_bultos }}</dd></div>
                        @if ($dispatch->invoice)<div><dt class="text-gray-500">Factura</dt><dd>{{ $dispatch->invoice->document_sn }}</dd></div>@endif
                    </dl>

                    <div class="mt-6 overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 text-sm">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-3 py-2 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Descripción</th>
                                    <th class="px-3 py-2 text-right text-xs font-semibold uppercase tracking-wider text-gray-500">Cant.</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach ($dispatch->items as $item)
                                    <tr><td class="px-3 py-2 text-gray-800">{{ $item->description }}</td><td class="px-3 py-2 text-right text-gray-600">{{ $item->quantity }}</td></tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="space-y-4">
                    @if ($dispatch->status === 'draft')
                        <div class="card p-5">
                            <h4 class="font-semibold text-gray-800 mb-3">Emisión</h4>
                            <form method="POST" action="{{ route('dispatches.emit', $dispatch) }}">
                                @csrf
                                <button type="submit" class="btn btn-primary w-full" data-loading-text="Emitiendo...">Emitir guía</button>
                            </form>
                        </div>
                    @else
                        <div class="card p-5">
                            <h4 class="font-semibold text-gray-800 mb-3">Respuesta del proveedor</h4>
                            <dl class="text-sm space-y-2">
                                @if ($dispatch->accepted_by_sunat !== null)
                                    <div class="flex justify-between"><dt class="text-gray-500">SUNAT</dt><dd class="{{ $dispatch->accepted_by_sunat ? 'text-green-600' : 'text-red-600' }} font-medium">{{ $dispatch->accepted_by_sunat ? 'Aceptada' : 'Rechazada' }}</dd></div>
                                @endif
                                @if ($dispatch->sunat_description)<div class="flex justify-between gap-2"><dt class="text-gray-500">Detalle</dt><dd class="text-right">{{ $dispatch->sunat_description }}</dd></div>@endif
                                @if ($dispatch->enlace_pdf)<div><a href="{{ $dispatch->enlace_pdf }}" target="_blank" class="text-blue-600 hover:underline">Ver PDF</a></div>@endif
                                @if ($dispatch->enlace_xml)<div><a href="{{ $dispatch->enlace_xml }}" target="_blank" class="text-blue-600 hover:underline">Descargar XML</a></div>@endif
                            </dl>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

