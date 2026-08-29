<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Comprobante {{ $voucher->document_sn }}</h2>
            <div class="flex gap-2">
                <button onclick="window.print()" class="btn btn-secondary">Imprimir</button>
                @can('editar vales de servicio')
                <a href="{{ route('service-vouchers.edit', $voucher) }}" class="btn btn-secondary">Editar</a>
                @endcan
                @if ($voucher->status === 'pending')
                <form method="POST" action="{{ route('service-vouchers.complete', $voucher) }}" class="inline">
                    @csrf
                    <button type="submit" class="btn btn-primary" data-confirm="¿Marcar el servicio como recibido conforme?">Marcar Completado</button>
                </form>
                @endif
                @can('eliminar vales de servicio')
                <form method="POST" action="{{ route('service-vouchers.destroy', $voucher) }}" class="inline" data-confirm="¿Eliminar este comprobante?">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn btn-danger">Eliminar</button>
                </form>
                @endcan
            </div>
        </div>
    </x-slot>

    @php $company = \App\Models\CompanySetting::get(); @endphp

    <div class="py-6">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="mb-4 px-4 py-3 bg-green-50 border border-green-200 text-green-700 rounded-lg text-sm">{{ session('success') }}</div>
            @endif
            @if ($errors->any())
                <div class="mb-4 px-4 py-3 bg-red-50 border border-red-200 text-red-700 rounded-lg text-sm">{{ $errors->first() }}</div>
            @endif

            <div class="card overflow-hidden">
                <div class="border-b border-gray-200 px-6 py-5 print:hidden">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ match($voucher->status) { 'pending' => 'bg-amber-100 text-amber-800', 'completed' => 'bg-blue-100 text-blue-800', default => 'bg-green-100 text-green-800' } }}">
                        {{ \App\Models\ServiceVoucher::STATUS_LABELS[$voucher->status] ?? $voucher->status }}
                    </span>
                    @if ($voucher->settlement)
                        <span class="ml-2 text-sm text-gray-500">Liquidado en <a href="{{ route('provider-settlements.show', $voucher->settlement) }}" class="font-mono text-blue-600 hover:text-blue-800">{{ $voucher->settlement->document_sn }}</a></span>
                    @endif
                </div>

                <div class="p-6">
                    <div class="flex flex-wrap justify-between items-start gap-4 border-b border-gray-200 pb-4 mb-4">
                        <div>
                            <p class="text-sm font-semibold text-gray-900">{{ $company?->razon_social }}</p>
                            <p class="text-xs text-gray-500">RUC: {{ $company?->ruc }}</p>
                            <p class="text-xs text-gray-500">{{ $company?->direccion }}</p>
                        </div>
                        <div class="text-right">
                            <p class="font-mono text-lg font-bold text-gray-900">CST — {{ $voucher->document_sn }}</p>
                            <p class="text-xs text-gray-500">Fecha: {{ $voucher->execution_date?->format('d/m/Y') }}</p>
                            <p class="text-xs text-gray-500">Serie: {{ $voucher->document_serie }} · Código SUNAT: CST</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wider text-gray-500 mb-1">Proveedor</p>
                            <p class="font-medium text-gray-900">{{ $voucher->provider?->display_name }}</p>
                            <p class="text-sm text-gray-600">{{ $voucher->provider?->document_type }}: {{ $voucher->provider?->document_number }}</p>
                            <p class="text-sm text-gray-600">{{ $voucher->provider?->address }}</p>
                        </div>
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wider text-gray-500 mb-1">Orden de Trabajo</p>
                            <p class="font-mono text-gray-900">{{ $voucher->workOrder?->document_sn }}</p>
                            <p class="text-sm text-gray-600">Placa: {{ $voucher->workOrder?->vehicle?->plate }}</p>
                            <p class="text-sm text-gray-600">{{ $voucher->workOrder?->vehicle?->vehicleModel?->brand?->name }} {{ $voucher->workOrder?->vehicle?->vehicleModel?->name }}</p>
                        </div>
                    </div>

                    <div class="mb-6">
                        <p class="text-xs font-semibold uppercase tracking-wider text-gray-500 mb-1">Descripción del servicio</p>
                        <p class="text-sm text-gray-800 whitespace-pre-line">{{ $voucher->description }}</p>
                    </div>

                    <div class="rounded-lg border border-gray-200 overflow-hidden mb-6">
                        <table class="w-full text-sm">
                            <tbody class="divide-y divide-gray-100">
                                <tr><td class="px-4 py-2 text-gray-600">Base (sin IGV)</td><td class="px-4 py-2 text-right font-medium">S/ {{ number_format($voucher->base_amount, 2) }}</td></tr>
                                @if ($voucher->discount_applied > 0)
                                <tr><td class="px-4 py-2 text-gray-600">Descuento</td><td class="px-4 py-2 text-right text-red-600">- S/ {{ number_format($voucher->discount_applied, 2) }}</td></tr>
                                @endif
                                <tr><td class="px-4 py-2 text-gray-600">IGV ({{ $voucher->igv_rate * 100 }}%)</td><td class="px-4 py-2 text-right">S/ {{ number_format($voucher->igv_amount, 2) }}</td></tr>
                                <tr class="bg-gray-50"><td class="px-4 py-2 font-medium text-gray-800">Total con IGV</td><td class="px-4 py-2 text-right font-semibold">S/ {{ number_format($voucher->total_with_igv, 2) }}</td></tr>
                                <tr><td class="px-4 py-2 text-gray-600">Detracción ({{ $voucher->detraction_rate * 100 }}%)</td><td class="px-4 py-2 text-right text-amber-700">- S/ {{ number_format($voucher->detraction_amount, 2) }}</td></tr>
                                <tr class="bg-blue-50"><td class="px-4 py-2 font-semibold text-blue-800">Total a pagar al proveedor</td><td class="px-4 py-2 text-right font-bold text-blue-800">S/ {{ number_format($voucher->total_payable, 2) }}</td></tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="rounded-lg bg-amber-50 border border-amber-200 p-4 text-sm text-amber-800 mb-6">
                        <p class="font-semibold">Detracción del IGV (SPOT)</p>
                        <p class="mt-1">Depositar <strong>S/ {{ number_format($voucher->detraction_amount, 2) }}</strong> a la cuenta de detracciones del proveedor en el Banco de la Nación. El saldo (<strong>S/ {{ number_format($voucher->total_payable, 2) }}</strong>) se paga directamente al proveedor.
                        @if ($company?->detraccion_account)
                            <br>Cuenta de detracciones (BN): <span class="font-mono">{{ $company->detraccion_account }}</span>
                        @endif</p>
                    </div>

                    @if ($voucher->statusHistory->isNotEmpty())
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wider text-gray-500 mb-2">Historial</p>
                        <ul class="space-y-1 text-xs text-gray-600">
                            @foreach ($voucher->statusHistory as $h)
                            <li><span class="font-medium">{{ \App\Models\ServiceVoucher::STATUS_LABELS[$h->to_status] ?? $h->to_status }}</span> — {{ $h->created_at->format('d/m/Y H:i') }} {{ $h->comments ? '· ' . $h->comments : '' }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
