{{-- ===== Modal: Crear nueva placa (vehículo) ===== --}}
<div id="vehicleModal" class="fixed inset-0 bg-gray-900 bg-opacity-50 hidden items-center justify-center z-50 p-4">
    <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full p-6 max-h-screen overflow-y-auto">
        <h3 class="text-xl font-bold mb-1 text-gray-800">➕ Nueva placa (vehículo)</h3>
        <p class="text-sm text-gray-500 mb-4">Complete los datos del vehículo. Se creará y se seleccionará automáticamente en el inventario.</p>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700">Placa *</label>
                <input type="text" id="vm-plate" maxlength="7" placeholder="ABC123" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 uppercase">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Marca *</label>
                <select id="vm-brand" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"></select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Modelo *</label>
                <select id="vm-model" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" disabled></select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Color</label>
                <input type="text" id="vm-color" maxlength="50" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 uppercase">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Año</label>
                <input type="number" id="vm-year" min="1900" max="{{ date('Y') + 1 }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">VIN</label>
                <input type="text" id="vm-vin" maxlength="20" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 uppercase">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">N. Motor</label>
                <input type="text" id="vm-engine" maxlength="30" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 uppercase">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Carrocería</label>
                <select id="vm-body-type" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="">Seleccionar...</option>
                    <option value="sedan">Sedán</option>
                    <option value="suv">SUV</option>
                    <option value="pickup">Pickup</option>
                    <option value="camioneta">Camioneta</option>
                    <option value="camion">Camión</option>
                    <option value="moto">Moto</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Próxima Revisión Técnica</label>
                <input type="date" id="vm-review" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
            </div>
        </div>

        <div class="mt-6 flex gap-2 justify-end">
            <button type="button" id="vm-cancel" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300">Cancelar</button>
            <button type="button" id="vm-save" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700">Guardar y seleccionar</button>
        </div>
    </div>
</div>