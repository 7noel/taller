{{-- =============================================================
     Modal para agregar/editar ítems del presupuesto.
     El estado de los ítems vive en JS (_form-scripts.blade.php);
     este modal solo captura los datos y expone open/close.
     ============================================================= --}}
<div id="item-modal" class="hidden fixed inset-0 z-50 overflow-y-auto" aria-hidden="true" role="dialog" aria-modal="true" aria-labelledby="item-modal-title">
    <div class="fixed inset-0 bg-gray-900/50 transition-opacity" id="item-modal-overlay"></div>

    <div class="relative min-h-screen flex items-center justify-center p-4">
        <div class="relative bg-white rounded-lg shadow-xl w-full max-w-lg">
            {{-- Cabecera --}}
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">
                <h3 id="item-modal-title" class="text-lg font-semibold text-gray-800">Agregar ítem</h3>
                <button type="button" id="item-modal-close-x" class="text-gray-400 hover:text-gray-600 transition" title="Cerrar">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            {{-- Cuerpo --}}
            <div class="px-6 py-5 space-y-4">
                {{-- Tipo --}}
                <div>
                    <label for="item-type" class="block text-sm font-medium text-gray-700">Tipo <span class="text-red-500">*</span></label>
                    <select id="item-type" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="service">Servicio</option>
                        <option value="part">Repuesto</option>
                        <option value="free_service">Ítem libre — Servicio</option>
                        <option value="free_part">Ítem libre — Repuesto</option>
                    </select>
                </div>

                {{-- Catálogo (visibles según tipo) --}}
                <div id="item-catalog-service">
                    <label for="item-service-select" class="block text-sm font-medium text-gray-700">Servicio</label>
                    <select id="item-service-select" class="mt-1 block w-full"></select>
                </div>

                <div id="item-catalog-part" class="hidden">
                    <label for="item-part-select" class="block text-sm font-medium text-gray-700">Repuesto</label>
                    <select id="item-part-select" class="mt-1 block w-full"></select>
                </div>

                {{-- Categoría (ítems libres) --}}
                <div id="item-category-service" class="hidden">
                    <label for="item-service-category" class="block text-sm font-medium text-gray-700">Categoría <span class="text-red-500">*</span></label>
                    <select id="item-service-category" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"></select>
                </div>

                <div id="item-category-part" class="hidden">
                    <label for="item-part-category" class="block text-sm font-medium text-gray-700">Categoría <span class="text-red-500">*</span></label>
                    <select id="item-part-category" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"></select>
                </div>

                {{-- Descripción --}}
                <div>
                    <label for="item-description" class="block text-sm font-medium text-gray-700">Descripción <span class="text-red-500">*</span></label>
                    <input type="text" id="item-description" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" maxlength="500">
                </div>

                {{-- Cantidad / precio / descuento --}}
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
                    <div>
                        <label for="item-quantity" class="block text-sm font-medium text-gray-700">Cantidad <span class="text-red-500">*</span></label>
                        <input type="number" id="item-quantity" step="0.01" min="0" value="1" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-right">
                    </div>
                    <div>
                        <label for="item-unit-price" class="block text-sm font-medium text-gray-700">P. Unitario <span class="text-red-500">*</span></label>
                        <input type="number" id="item-unit-price" step="0.0001" min="0" value="0" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-right">
                    </div>
                    <div>
                        <label for="item-discount-pct" class="block text-sm font-medium text-gray-700">Dto. %</label>
                        <input type="number" id="item-discount-pct" step="0.01" min="0" max="100" value="0" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-right">
                    </div>
                </div>

                {{-- Nota de conversión de catálogo (cuando el catálogo está en otra moneda) --}}
                <p id="item-price-note" class="text-xs text-gray-500 hidden"></p>

                {{-- Unidad de medida (SUNAT Catálogo 03) --}}
                <div>
                    <label for="item-uom" class="block text-sm font-medium text-gray-700">Unidad de medida</label>
                    <select id="item-uom" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">—</option>
                        <option value="NIU">NIU · Unidad</option>
                        <option value="PZ">PZ · Pieza</option>
                        <option value="ZZ">ZZ · Servicio</option>
                        <option value="KGM">KGM · Kilogramo</option>
                        <option value="LTR">LTR · Litro</option>
                        <option value="MTR">MTR · Metro</option>
                        <option value="HUR">HUR · Hora</option>
                        <option value="DIA">DIA · Día</option>
                        <option value="SET">SET · Set</option>
                        <option value="C62">C62 · Ciento</option>
                    </select>
                </div>

                {{-- Origen suministro (repuestos) --}}
                <div id="item-supply-wrap">
                    <label for="item-supply-source" class="block text-sm font-medium text-gray-700">Origen del suministro</label>
                    <select id="item-supply-source" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        @foreach (\App\Models\Estimate::SUPPLY_SOURCES as $key => $label)
                            <option value="{{ $key }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Vínculo a catálogo (solo ítems ya guardados) --}}
                <div id="item-link-wrap" class="hidden border-t border-gray-100 pt-4">
                    <div class="flex items-center justify-between mb-2">
                        <label class="text-sm font-medium text-gray-700">Vínculo a catálogo</label>
                        <span id="item-link-status" class="text-xs font-medium"></span>
                    </div>
                    <div id="item-link-picker" class="space-y-2">
                        <select id="item-link-select" class="mt-1 block w-full"></select>
                        <div class="flex items-center justify-between gap-2">
                            <p id="item-link-hint" class="text-xs text-gray-500"></p>
                            <button type="button" id="item-link-save" class="btn btn-secondary shrink-0">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101M10.172 13.828a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>
                                Vincular
                            </button>
                        </div>
                    </div>
                    <div id="item-link-linked" class="hidden flex items-center justify-between gap-2">
                        <span id="item-link-linked-name" class="text-sm text-gray-700"></span>
                        <button type="button" id="item-link-unlink" class="btn btn-secondary shrink-0">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101M10.172 13.828a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>
                                Desvincular
                            </button>
                        </div>
                    </div>
                </div>

                <input type="hidden" id="item-cost-price" value="0">
                <input type="hidden" id="item-id" value="">
            </div>

            {{-- Pie --}}
            <div class="flex justify-end gap-2 px-6 py-4 border-t border-gray-200">
                <button type="button" id="item-modal-cancel" class="btn btn-secondary">Cancelar</button>
                <button type="button" id="item-modal-save" class="btn btn-primary">Guardar ítem</button>
            </div>
        </div>
    </div>
</div>