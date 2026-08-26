{{-- =============================================================
     Modal para agregar/editar órdenes de compra de terceros.
     El estado de las OC vive en JS (_form-scripts.blade.php);
     este modal solo captura los datos y expone open/close.
     ============================================================= --}}
<div id="third-party-order-modal" class="hidden fixed inset-0 z-50 overflow-y-auto" aria-hidden="true" role="dialog" aria-modal="true" aria-labelledby="third-party-order-modal-title">
    <div class="fixed inset-0 bg-gray-900/50 transition-opacity" id="third-party-order-modal-overlay"></div>

    <div class="relative min-h-screen flex items-center justify-center p-4">
        <div class="relative bg-white rounded-lg shadow-xl w-full max-w-lg">
            {{-- Cabecera --}}
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">
                <h3 id="third-party-order-modal-title" class="text-lg font-semibold text-gray-800">Agregar orden de compra</h3>
                <button type="button" id="third-party-order-modal-close-x" class="text-gray-400 hover:text-gray-600 transition" title="Cerrar">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            {{-- Cuerpo --}}
            <div class="px-6 py-5 space-y-4">
                {{-- Descripción --}}
                <div>
                    <label for="third-party-order-description" class="block text-sm font-medium text-gray-700">Descripción <span class="text-red-500">*</span></label>
                    <textarea id="third-party-order-description" rows="2" maxlength="1000" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"></textarea>
                    <p class="text-red-600 text-sm mt-1 hidden" id="third-party-order-description-error"></p>
                </div>

                {{-- Proveedor (opcional) --}}
                <div>
                    <label for="third-party-order-provider" class="block text-sm font-medium text-gray-700">Proveedor</label>
                    <input type="text" id="third-party-order-provider" maxlength="255" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>

                {{-- Monto sin IGV --}}
                <div>
                    <label for="third-party-order-amount" class="block text-sm font-medium text-gray-700">Monto sin IGV <span class="text-red-500">*</span></label>
                    <input type="number" id="third-party-order-amount" step="0.01" min="0" value="0" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-right">
                    <p class="text-red-600 text-sm mt-1 hidden" id="third-party-order-amount-error"></p>
                </div>

                <input type="hidden" id="third-party-order-id" value="">
            </div>

            {{-- Pie --}}
            <div class="flex justify-end gap-2 px-6 py-4 border-t border-gray-200">
                <button type="button" id="third-party-order-modal-cancel" class="btn btn-secondary">Cancelar</button>
                <button type="button" id="third-party-order-modal-save" class="btn btn-primary">Guardar orden</button>
            </div>
        </div>
    </div>
</div>