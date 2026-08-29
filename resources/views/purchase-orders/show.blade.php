<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center gap-3 flex-wrap">
            <div class="flex items-center gap-3">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Orden de Compra') }}</h2>
                <x-document-badge :sn="$purchaseOrder->document_sn" :label="$purchaseOrder->document_type_code" />
            </div>
            <div class="flex gap-2">
                @if (! in_array($purchaseOrder->status, ['received', 'cancelled']))
                    <a href="{{ route('purchase-orders.edit', $purchaseOrder) }}" class="btn btn-secondary">Editar</a>
                @endif
                <a href="{{ route('purchase-orders.index') }}" class="btn btn-secondary">Volver</a>
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

            {{-- Datos de la OC --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 mb-4">
                <div class="card p-4"><p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Proveedor</p><p class="text-sm font-medium text-gray-800 mt-1">{{ $purchaseOrder->provider?->display_name ?? '—' }}</p></div>
                <div class="card p-4"><p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Almacén destino</p><p class="text-sm font-medium text-gray-800 mt-1">{{ $purchaseOrder->warehouse?->name ?? '—' }}</p></div>
                <div class="card p-4"><p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Estado</p>
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium mt-1
                        {{ $purchaseOrder->status === 'received' ? 'bg-green-50 text-green-700' : ($purchaseOrder->status === 'cancelled' ? 'bg-red-50 text-red-700' : ($purchaseOrder->status === 'ordered' ? 'bg-blue-50 text-blue-700' : 'bg-gray-100 text-gray-600')) }}">{{ $purchaseOrder->status_label }}</span>
                </div>
                <div class="card p-4"><p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Total</p><p class="text-sm font-semibold text-gray-800 mt-1">{{ $purchaseOrder->total }} {{ $purchaseOrder->currency }}</p></div>
                @if ($purchaseOrder->provider_invoice)
                    <div class="card p-4"><p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Factura del proveedor</p><p class="text-sm font-medium text-gray-800 mt-1">{{ $purchaseOrder->provider_invoice }}</p></div>
                @endif
                @if ($purchaseOrder->provider_guide)
                    <div class="card p-4"><p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Guía del proveedor</p><p class="text-sm font-medium text-gray-800 mt-1">{{ $purchaseOrder->provider_guide }}</p></div>
                @endif
            </div>

            {{-- Ítems --}}
            <div class="card overflow-hidden mb-4">
                <div class="px-4 sm:px-5 py-3 border-b border-gray-200">
                    <h3 class="text-sm font-semibold text-gray-800">Repuestos</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="text-xs font-semibold uppercase tracking-wider text-gray-500 border-b border-gray-200 bg-gray-50">
                                <th class="px-4 py-2 text-left">Repuesto</th>
                                <th class="px-4 py-2 text-left">SKU</th>
                                <th class="px-4 py-2 text-right">Cantidad</th>
                                <th class="px-4 py-2 text-right">Costo unit.</th>
                                <th class="px-4 py-2 text-right">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($purchaseOrder->items as $item)
                                <tr class="border-b border-gray-100">
                                    <td class="px-4 py-2 text-gray-800">{{ $item->part?->name }}</td>
                                    <td class="px-4 py-2 text-gray-500">{{ $item->part?->sku }}</td>
                                    <td class="px-4 py-2 text-right">{{ $item->quantity }} {{ $item->uom }}</td>
                                    <td class="px-4 py-2 text-right">{{ number_format($item->unit_cost, 2) }}</td>
                                    <td class="px-4 py-2 text-right font-medium">{{ number_format($item->total_cost, 2) }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="px-4 py-4 text-gray-400 text-center">Sin ítems</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Recepción --}}
            @if (! in_array($purchaseOrder->status, ['received', 'cancelled']))
                <div class="card p-4 sm:p-5 mb-4">
                    <h3 class="text-sm font-semibold text-gray-800 mb-3">Recibir mercadería</h3>
                    <p class="text-xs text-gray-500 mb-3">Al recibir se emite la guía de ingreso <strong>NIA1</strong> con motivo <strong>02 · Compra nacional</strong> y el stock ingresa al almacén.</p>
                    <form method="POST" action="{{ route('purchase-orders.receive', $purchaseOrder) }}" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-3">
                        @csrf
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Almacén <span class="text-red-500">*</span></label>
                            <select name="warehouse_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
                                @foreach ($warehouses as $w)
                                    <option value="{{ $w->id }}" @selected(old('warehouse_id', $purchaseOrder->warehouse_id) == $w->id)>{{ $w->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Factura proveedor</label>
                            <input type="text" name="provider_invoice" value="{{ old('provider_invoice') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" maxlength="30">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Guía proveedor</label>
                            <input type="text" name="provider_guide" value="{{ old('provider_guide') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" maxlength="30">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Fecha recepción</label>
                            <input type="date" name="received_at" value="{{ old('received_at', now()->toDateString()) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>
                        <div class="flex items-end">
                            <button type="submit" class="btn btn-primary w-full">Recibir y generar NIA1</button>
                        </div>
                    </form>
                </div>

                <form method="POST" action="{{ route('purchase-orders.cancel', $purchaseOrder) }}" class="mb-4 flex justify-end" data-confirm="¿Estás seguro de anular la orden de compra {{ $purchaseOrder->document_sn }}?">
                    @csrf
                    <button type="submit" class="btn btn-danger">Anular OC</button>
                </form>
            @endif

            {{-- Recepciones (guías NIA1) --}}
            @if ($purchaseOrder->inventoryGuides->isNotEmpty())
                <div class="card overflow-hidden">
                    <div class="px-4 sm:px-5 py-3 border-b border-gray-200">
                        <h3 class="text-sm font-semibold text-gray-800">Guías de ingreso generadas</h3>
                    </div>
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="text-xs font-semibold uppercase tracking-wider text-gray-500 border-b border-gray-200 bg-gray-50">
                                <th class="px-4 py-2 text-left">Documento</th>
                                <th class="px-4 py-2 text-left">Motivo</th>
                                <th class="px-4 py-2 text-left">Fecha</th>
                                <th class="px-4 py-2 text-left">Factura / Guía prov.</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($purchaseOrder->inventoryGuides as $guide)
                                <tr class="border-b border-gray-100">
                                    <td class="px-4 py-2"><a href="{{ route('inventory-guides.show', $guide) }}" class="font-mono font-semibold text-blue-600 hover:underline">{{ $guide->document_sn }}</a></td>
                                    <td class="px-4 py-2">{{ $guide->movementReason?->name }}</td>
                                    <td class="px-4 py-2">{{ optional($guide->movement_date)->format('d/m/Y') }}</td>
                                    <td class="px-4 py-2">{{ $guide->provider_invoice }} {{ $guide->provider_guide }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
