<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Tipos de Cambio') }}</h2>
            <a href="{{ route('dashboard') }}" class="btn btn-secondary">Dashboard</a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="mb-4 px-4 py-3 bg-green-50 border border-green-200 text-green-700 rounded-lg text-sm">{{ session('success') }}</div>
            @endif

            <div class="card overflow-hidden mb-6">
                <div class="p-4 sm:p-5">
                    <h3 class="text-base font-semibold text-gray-800">Registrar tipo de cambio</h3>
                    <p class="text-sm text-gray-500 mt-0.5">El tipo de cambio se expresa en <strong class="font-semibold">soles por 1 dólar</strong> (PEN → 1). Se sugiere automáticamente al crear presupuestos en USD.</p>

                    <form method="POST" action="{{ route('exchange-rates.store') }}" class="mt-4 grid grid-cols-2 md:grid-cols-5 gap-3 max-w-3xl">
                        @csrf
                        <div>
                            <label for="date" class="block text-xs font-medium text-gray-700">Fecha <span class="text-red-500">*</span></label>
                            <input type="date" id="date" name="date" value="{{ now()->toDateString() }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                        </div>
                        <div>
                            <label for="currency" class="block text-xs font-medium text-gray-700">Moneda <span class="text-red-500">*</span></label>
                            <select id="currency" name="currency" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                                <option value="USD" selected>USD · Dólar</option>
                                <option value="PEN">PEN · Sol</option>
                            </select>
                        </div>
                        <div>
                            <label for="buy_rate" class="block text-xs font-medium text-gray-700">Compra <span class="text-red-500">*</span></label>
                            <input type="number" id="buy_rate" name="buy_rate" step="0.0001" min="0" value="3.7000" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm text-right">
                        </div>
                        <div>
                            <label for="sell_rate" class="block text-xs font-medium text-gray-700">Venta <span class="text-red-500">*</span></label>
                            <input type="number" id="sell_rate" name="sell_rate" step="0.0001" min="0" value="3.7500" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm text-right">
                        </div>
                        <div class="flex items-end">
                            <button type="submit" class="btn btn-primary w-full">Guardar</button>
                        </div>
                        <div class="col-span-2 md:col-span-5">
                            <label for="source" class="block text-xs font-medium text-gray-700">Fuente (opcional)</label>
                            <input type="text" id="source" name="source" maxlength="100" placeholder="ej. SUNAT, BCR, banco..." class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                        </div>
                    </form>
                </div>
            </div>

            <div class="card overflow-hidden">
                <div class="p-4 sm:p-5">
                    <h3 class="text-base font-semibold text-gray-800 mb-3">Historial (últimos 200)</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 text-sm">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-3 py-2 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Fecha</th>
                                    <th class="px-3 py-2 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Moneda</th>
                                    <th class="px-3 py-2 text-right text-xs font-semibold uppercase tracking-wider text-gray-500">Compra</th>
                                    <th class="px-3 py-2 text-right text-xs font-semibold uppercase tracking-wider text-gray-500">Venta</th>
                                    <th class="px-3 py-2 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Fuente</th>
                                    <th class="px-3 py-2 text-right text-xs font-semibold uppercase tracking-wider text-gray-500">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @forelse ($rates as $rate)
                                    <tr>
                                        <td class="px-3 py-2 text-gray-800">{{ $rate->date->format('d/m/Y') }}</td>
                                        <td class="px-3 py-2">
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-medium {{ $rate->currency === 'USD' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800' }}">
                                                {{ $rate->currency === 'USD' ? 'USD · Dólar' : 'PEN · Sol' }}
                                            </span>
                                        </td>
                                        <td class="px-3 py-2 text-right text-gray-700">{{ number_format((float) $rate->buy_rate, 4) }}</td>
                                        <td class="px-3 py-2 text-right text-gray-700">{{ number_format((float) $rate->sell_rate, 4) }}</td>
                                        <td class="px-3 py-2 text-gray-600">{{ $rate->source ?: '—' }}</td>
                                        <td class="px-3 py-2 text-right">
                                            <form method="POST" class="inline" action="{{ route('exchange-rates.destroy', $rate) }}"
                                                data-confirm="¿Eliminar el tipo de cambio del {{ $rate->date->format('d/m/Y') }} ({{ $rate->currency }})?">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn-icon btn-icon-red" title="Eliminar">
                                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-3 py-8 text-center text-sm text-gray-500">No hay tipos de cambio registrados.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
