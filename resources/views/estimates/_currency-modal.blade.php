{{-- =============================================================
     Modal para cambiar la moneda de un presupuesto con ítems cargados
     (solo borrador). Formulario INDEPENDIENTE (POST a estimates.change-currency);
     se incluye FUERA del form principal para no anidar formularios.
     ============================================================= --}}
<div id="currency-modal" class="hidden fixed inset-0 z-50 overflow-y-auto" aria-hidden="true" role="dialog" aria-modal="true" aria-labelledby="currency-modal-title">
    <div class="fixed inset-0 bg-gray-900/50 transition-opacity" id="currency-modal-overlay"></div>

    <div class="relative min-h-screen flex items-center justify-center p-4">
        <div class="relative bg-white rounded-lg shadow-xl w-full max-w-md">
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">
                <h3 id="currency-modal-title" class="text-lg font-semibold text-gray-800">Cambiar moneda</h3>
                <button type="button" id="currency-modal-close-x" class="text-gray-400 hover:text-gray-600 transition" title="Cerrar">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <form method="POST" action="{{ route('estimates.change-currency', $estimate) }}" id="currency-change-form">
                @csrf
                <div class="px-6 py-5 space-y-4">
                    <p class="text-sm text-gray-600">
                        Todos los montos del presupuesto (ítems, órdenes de compra, tarifas, franquicia) se
                        <strong class="font-semibold">convertirán</strong> al nuevo tipo de cambio. Esta acción no se puede deshacer.
                    </p>

                    <div>
                        <label for="currency-new" class="block text-sm font-medium text-gray-700">Nueva moneda <span class="text-red-500">*</span></label>
                        <select id="currency-new" name="currency" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="USD" @selected(($estimate->currency ?? 'PEN') === 'PEN')>Dólares (USD)</option>
                            <option value="PEN" @selected(($estimate->currency ?? 'PEN') === 'USD')>Soles (PEN)</option>
                        </select>
                    </div>

                    <div>
                        <label for="currency-rate" class="block text-sm font-medium text-gray-700">Nuevo tipo de cambio (S/ por US$) <span class="text-red-500">*</span></label>
                        <input type="number" id="currency-rate" name="exchange_rate" step="0.0001" min="0.01" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-right" placeholder="ej. 3.75">
                        <p class="mt-1 text-xs text-gray-500">El T.C. se expresa en soles por 1 dólar (PEN → 1).</p>
                    </div>

                    {{-- Vista previa del nuevo total --}}
                    <div class="px-4 py-3 bg-gray-50 border border-gray-200 rounded-lg text-sm">
                        <div class="flex justify-between"><span class="text-gray-500">Total actual</span><span id="currency-current-total" class="font-medium"></span></div>
                        <div class="flex justify-between mt-1"><span class="text-gray-500">Total en la nueva moneda</span><span id="currency-new-total" class="font-semibold text-gray-900"></span></div>
                    </div>
                </div>

                <div class="flex justify-end gap-2 px-6 py-4 border-t border-gray-200">
                    <button type="button" id="currency-modal-cancel" class="btn btn-secondary">Cancelar</button>
                    <button type="submit" id="currency-modal-save" class="btn btn-primary">Convertir y cambiar</button>
                </div>
            </form>
        </div>
    </div>
</div>
