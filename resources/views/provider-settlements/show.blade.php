<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Liquidación {{ $settlement->document_sn }}</h2>
            <div class="flex flex-wrap gap-2">
                <button onclick="window.print()" class="btn btn-secondary">Imprimir</button>
                @if ($settlement->status === 'draft')
                    @can('editar liquidaciones de servicios')
                    <a href="{{ route('provider-settlements.edit', $settlement) }}" class="btn btn-secondary">Editar</a>
                    <form method="POST" action="{{ route('provider-settlements.approve', $settlement) }}" class="inline">
                        @csrf
                        <button type="submit" class="btn btn-primary" data-confirm="¿Aprobar esta liquidación?">Aprobar</button>
                    </form>
                    @endcan
                    @can('eliminar liquidaciones de servicios')
                    <form method="POST" action="{{ route('provider-settlements.destroy', $settlement) }}" class="inline" data-confirm="¿Eliminar esta liquidación? Los comprobantes quedarán disponibles.">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-danger">Eliminar</button>
                    </form>
                    @endcan
                @elseif ($settlement->status === 'approved')
                    @can('editar liquidaciones de servicios')
                    <form method="POST" action="{{ route('provider-settlements.pay', $settlement) }}" class="inline">
                        @csrf
                        <button type="submit" class="btn btn-primary" data-confirm="¿Registrar el pago? Los comprobantes se marcarán como liquidados.">Registrar Pago</button>
                    </form>
                    @endcan
                @endif
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
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ match($settlement->status) { 'draft' => 'bg-gray-100 text-gray-800', 'approved' => 'bg-blue-100 text-blue-800', default => 'bg-green-100 text-green-800' } }}">
                        {{ \App\Models\ProviderSettlement::STATUS_LABELS[$settlement->status] ?? $settlement->status }}
                    </span>
                    @if ($settlement->approved_at)
                        <span class="ml-2 text-sm text-gray-500">Aprobada por {{ $settlement->approvedBy?->name }} · {{ $settlement->approved_at->format('d/m/Y H:i') }}</span>
                    @endif
                    @if ($settlement->paid_at)
                        <span class="ml-2 text-sm text-gray-500">Pagada por {{ $settlement->paidBy?->name }} · {{ $settlement->paid_at->format('d/m/Y H:i') }}</span>
                    @endif
                </div>

                <div class="p-6">
                    <div class="flex flex-wrap justify-between items-start gap-4 border-b border-gray-200 pb-4 mb-4">
                        <div>
                            <p class="text-sm font-semibold text-gray-900">{{ $company?->razon_social }}</p>
                            <p class="text-xs text-gray-500">RUC: {{ $company?->ruc }}</p>
                        </div>
                        <div class="text-right">
                            <p class="font-mono text-lg font-bold text-gray-900">LST — {{ $settlement->document_sn }}</p>
                            <p class="text-xs text-gray-500">Período: {{ $settlement->period_start?->format('d/m/Y') }} — {{ $settlement->period_end?->format('d/m/Y') }}</p>
                            <p class="text-xs text-gray-500">Serie: {{ $settlement->document_serie }} · Código SUNAT: LST</p>
                        </div>
                    </div>

                    <div class="mb-6">
                        <p class="text-xs font-semibold uppercase tracking-wider text-gray-500 mb-1">Proveedor</p>
                        <p class="font-medium text-gray-900">{{ $settlement->provider?->display_name }}</p>
                        <p class="text-sm text-gray-600">{{ $settlement->provider?->document_type }}: {{ $settlement->provider?->document_number }}</p>
                        <p class="text-sm text-gray-600">{{ $settlement->provider?->address }}</p>
                    </div>
                    <div class="rounded-lg border border-gray-200 overflow-hidden mb-6">
                        <table class="w-full text-sm">
                            <thead class="bg-gray-50 text-xs uppercase tracking-wider text-gray-500">
                                <tr>
                                    <th class="px-4 py-2 text-left">Documento</th>
                                    <th class="px-4 py-2 text-left">Fecha</th>
                                    <th class="px-4 py-2 text-left">Placa</th>
                                    <th class="px-4 py-2 text-left">Descripción</th>
                                    <th class="px-4 py-2 text-right">Base (sin IGV)</th>
                                    @if ($settlement->status === 'draft')<th class="px-4 py-2 text-center">Quitar</th>@endif
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @forelse ($settlement->vouchers as $voucher)
                                <tr>
                                    <td class="px-4 py-2"><a href="{{ route('service-vouchers.show', $voucher) }}" class="font-mono text-xs text-blue-600 hover:text-blue-800">{{ $voucher->document_sn }}</a></td>
                                    <td class="px-4 py-2">{{ $voucher->execution_date?->format('d/m/Y') }}</td>
                                    <td class="px-4 py-2">{{ $voucher->workOrder?->vehicle?->plate }}</td>
                                    <td class="px-4 py-2 text-gray-600">{{ \Illuminate\Support\Str::limit($voucher->description, 60) }}</td>
                                    <td class="px-4 py-2 text-right">S/ {{ number_format($voucher->base_amount, 2) }}</td>
                                    @if ($settlement->status === 'draft')
                                    <td class="px-4 py-2 text-center">
                                        <form method="POST" action="{{ route('provider-settlements.vouchers.detach', [$settlement, $voucher]) }}" class="inline" data-confirm="¿Quitar este comprobante de la liquidación?">
                                            @csrf @method('DELETE')
                                            <button type="submit" title="Quitar de la liquidación" class="btn-icon btn-icon-red">
                                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                                            </button>
                                        </form>
                                    </td>
                                    @endif
                                </tr>
                                @empty
                                <tr><td colspan="6" class="px-4 py-4 text-center text-sm text-gray-400">Sin comprobantes vinculados.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="rounded-lg border border-gray-200 overflow-hidden mb-6">
                        <table class="w-full text-sm">
                            <tbody class="divide-y divide-gray-100">
                                <tr><td class="px-4 py-2 text-gray-600">Subtotal comprobantes (sin IGV)</td><td class="px-4 py-2 text-right font-medium">S/ {{ number_format($settlement->subtotal, 2) }}</td></tr>
                                @if ($settlement->global_discount > 0)
                                <tr><td class="px-4 py-2 text-gray-600">Descuento global{{ $settlement->discount_reason ? ' (' . $settlement->discount_reason . ')' : '' }}</td><td class="px-4 py-2 text-right text-red-600">- S/ {{ number_format($settlement->global_discount, 2) }}</td></tr>
                                @endif
                                <tr><td class="px-4 py-2 font-medium text-gray-800">Base (sin IGV)</td><td class="px-4 py-2 text-right font-semibold">S/ {{ number_format($settlement->base_amount, 2) }}</td></tr>
                                <tr><td class="px-4 py-2 text-gray-600">IGV ({{ $settlement->igv_rate * 100 }}%)</td><td class="px-4 py-2 text-right">S/ {{ number_format($settlement->igv_amount, 2) }}</td></tr>
                                <tr class="bg-gray-50"><td class="px-4 py-2 font-medium text-gray-800">Total con IGV</td><td class="px-4 py-2 text-right font-semibold">S/ {{ number_format($settlement->total_with_igv, 2) }}</td></tr>
                                <tr><td class="px-4 py-2 text-gray-600">Detracción ({{ $settlement->detraction_rate * 100 }}%)</td><td class="px-4 py-2 text-right text-amber-700">- S/ {{ number_format($settlement->detraction_amount, 2) }}</td></tr>
                                <tr class="bg-blue-50"><td class="px-4 py-2 font-semibold text-blue-800">Total a pagar al proveedor</td><td class="px-4 py-2 text-right font-bold text-blue-800">S/ {{ number_format($settlement->total_payable, 2) }}</td></tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="rounded-lg bg-amber-50 border border-amber-200 p-4 text-sm text-amber-800 mb-6">
                        <p class="font-semibold">Detracción del IGV (SPOT)</p>
                        <p class="mt-1">Depositar <strong>S/ {{ number_format($settlement->detraction_amount, 2) }}</strong> a la cuenta de detracciones del proveedor en el Banco de la Nación. El saldo (<strong>S/ {{ number_format($settlement->total_payable, 2) }}</strong>) se paga directamente al proveedor.
                        @if ($company?->detraccion_account)
                            <br>Cuenta de detracciones (BN): <span class="font-mono">{{ $company->detraccion_account }}</span>
                        @endif</p>
                    </div>

                    @if ($settlement->statusHistory->isNotEmpty())
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wider text-gray-500 mb-2">Historial</p>
                        <ul class="space-y-1 text-xs text-gray-600">
                            @foreach ($settlement->statusHistory as $h)
                            <li><span class="font-medium">{{ \App\Models\ProviderSettlement::STATUS_LABELS[$h->to_status] ?? $h->to_status }}</span> — {{ $h->created_at->format('d/m/Y H:i') }} {{ $h->comments ? '· ' . $h->comments : '' }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

