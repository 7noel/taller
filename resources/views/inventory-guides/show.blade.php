<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center gap-3 flex-wrap">
            <div class="flex items-center gap-3">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ $inventoryGuide->type_label }}</h2>
                <x-document-badge :sn="$inventoryGuide->document_sn" :label="$inventoryGuide->document_type_code" />
            </div>
            <div class="flex gap-2">
                <form method="POST" action="{{ route('inventory-guides.destroy', $inventoryGuide) }}" data-confirm="¿Estás seguro de anular la guía {{ $inventoryGuide->document_sn }}? Los movimientos quedarán registrados como anulados.">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Anular guía</button>
                </form>
                <a href="{{ route('inventory-guides.index') }}" class="btn btn-secondary">Volver</a>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="mb-4 px-4 py-3 bg-green-50 border border-green-200 text-green-700 rounded-lg text-sm">{{ session('success') }}</div>
            @endif

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 mb-4">
                <div class="card p-4"><p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Motivo</p><p class="text-sm font-medium text-gray-800 mt-1">{{ $inventoryGuide->movementReason?->name ?? '—' }}</p></div>
                <div class="card p-4"><p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Almacén origen</p><p class="text-sm font-medium text-gray-800 mt-1">{{ $inventoryGuide->originWarehouse?->name ?? '—' }}</p></div>
                <div class="card p-4"><p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Almacén destino</p><p class="text-sm font-medium text-gray-800 mt-1">{{ $inventoryGuide->destinationWarehouse?->name ?? '—' }}</p></div>
                <div class="card p-4"><p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Fecha</p><p class="text-sm font-medium text-gray-800 mt-1">{{ optional($inventoryGuide->movement_date)->format('d/m/Y') }}</p></div>
                @if ($inventoryGuide->provider)
                    <div class="card p-4"><p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Proveedor</p><p class="text-sm font-medium text-gray-800 mt-1">{{ $inventoryGuide->provider->display_name }}</p></div>
                @endif
                @if ($inventoryGuide->purchaseOrder)
                    <div class="card p-4"><p class="text-xs font-semibold uppercase tracking-wider text-gray-500">OC vinculada</p><p class="text-sm font-medium text-gray-800 mt-1"><a href="{{ route('purchase-orders.show', $inventoryGuide->purchaseOrder) }}" class="text-blue-600 hover:underline font-mono">{{ $inventoryGuide->purchaseOrder->document_sn }}</a></p></div>
                @endif
                @if ($inventoryGuide->workOrder)
                    <div class="card p-4"><p class="text-xs font-semibold uppercase tracking-wider text-gray-500">OT vinculada</p><p class="text-sm font-medium text-gray-800 mt-1 font-mono">{{ $inventoryGuide->workOrder->document_sn }}</p></div>
                @endif
                @if ($inventoryGuide->notes)
                    <div class="card p-4 lg:col-span-4"><p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Notas</p><p class="text-sm text-gray-800 mt-1">{{ $inventoryGuide->notes }}</p></div>
                @endif
            </div>

            {{-- Movimientos --}}
            <div class="card overflow-hidden">
                <div class="px-4 sm:px-5 py-3 border-b border-gray-200">
                    <h3 class="text-sm font-semibold text-gray-800">Movimientos</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="text-xs font-semibold uppercase tracking-wider text-gray-500 border-b border-gray-200 bg-gray-50">
                                <th class="px-4 py-2 text-left">Repuesto</th>
                                <th class="px-4 py-2 text-left">SKU</th>
                                <th class="px-4 py-2 text-right">Cantidad</th>
                                <th class="px-4 py-2 text-left">Tipo</th>
                                <th class="px-4 py-2 text-left">Almacén</th>
                                <th class="px-4 py-2 text-right">Costo unit. (S/)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($inventoryGuide->movements as $m)
                                <tr class="border-b border-gray-100">
                                    <td class="px-4 py-2 text-gray-800">{{ $m->part?->name }}</td>
                                    <td class="px-4 py-2 text-gray-500">{{ $m->part?->sku }}</td>
                                    <td class="px-4 py-2 text-right font-medium">{{ $m->quantity }}</td>
                                    <td class="px-4 py-2">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $m->type === 'entry' ? 'bg-green-50 text-green-700' : 'bg-red-50 text-red-700' }}">{{ $m->type_label }}</span>
                                    </td>
                                    <td class="px-4 py-2">{{ $m->warehouse?->name }}</td>
                                    <td class="px-4 py-2 text-right">{{ number_format($m->unit_cost_pen, 2) }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="6" class="px-4 py-4 text-gray-400 text-center">Sin movimientos</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
