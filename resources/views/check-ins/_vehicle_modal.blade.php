{{-- ===== Modal: Nueva / Editar placa (vehículo) ===== --}}
<div id="vehicleModal" class="fixed inset-0 bg-gray-900 bg-opacity-50 hidden items-center justify-center z-50 p-4">
    <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full p-6 max-h-screen overflow-y-auto">
        <div class="flex items-center justify-between gap-3">
            <div>
                <h3 id="vm-title" class="text-xl font-bold mb-1 text-gray-800">Nueva placa (vehículo)</h3>
                <p class="text-sm text-gray-500">Complete los datos del vehículo. Se creará y se seleccionará automáticamente en el inventario.</p>
            </div>
            <button type="button" id="btnSunarpVehicle" class="shrink-0 inline-flex items-center gap-2 bg-blue-600 font-semibold text-xs text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                OBTENER DATOS DE SUNARP
            </button>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700">Placa *</label>
                <input type="text" id="vm-plate" maxlength="7" placeholder="ABC123" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 uppercase">
            </div>
            <div>
                <div class="flex items-center justify-between gap-2">
                    <label for="vm-brand" class="block text-sm font-medium text-gray-700">Marca <span class="text-red-500">*</span></label>
                    <button type="button" data-bmm-open="brand" class="inline-flex items-center gap-1 text-xs font-medium text-blue-600 hover:underline">
                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                        Nueva marca
                    </button>
                </div>
                <select id="vm-brand" data-bmm-brand class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"></select>
            </div>
            <div>
                <div class="flex items-center justify-between gap-2">
                    <label for="vm-model" class="block text-sm font-medium text-gray-700">Modelo <span class="text-red-500">*</span></label>
                    <button type="button" id="vm-new-model" data-bmm-open="model" disabled class="inline-flex items-center gap-1 text-xs font-medium text-blue-600 hover:underline disabled:opacity-40 disabled:pointer-events-none">
                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                        Nuevo modelo
                    </button>
                </div>
                <select id="vm-model" data-bmm-model class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" disabled></select>
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

@include('partials.sunarp-modal', [
    'sunarpBtnId' => 'btnSunarpVehicle',
    'sunarpModalId' => 'modalSunarpVehicle',
    'sunarpSelectors' => [
        'plate' => '#vm-plate',
        'brand' => '#vm-brand',
        'model' => '#vm-model',
        'year' => '#vm-year',
        'color' => '#vm-color',
        'vin' => '#vm-vin',
        'engine' => '#vm-engine',
    ],
])

{{-- Modal "Nueva marca / Nuevo modelo" (creación rápida de marca/modelo) --}}
@include('partials.brand-model-modal')

@push('scripts')
<script>
(function () {
    'use strict';
    try {

    const modal = document.getElementById('vehicleModal');
    let editingVehicleId = null; // null = nueva placa, id = editar placa
    let saving = false;          // Flag anti-doble envío (evita doble clic ráfaga)

    const brandSelect = document.getElementById('vm-brand');
    const modelSelect = document.getElementById('vm-model');
    const vmNewModelBtn = document.getElementById('vm-new-model');

    // Guardas: si falta algún elemento del modal, loguear en vez de fallar en silencio
    if (!modal || !brandSelect || !modelSelect) {
        console.error('[vehicle-modal] Faltan elementos del modal. vehicleModal:', !!modal, '| vm-brand:', !!brandSelect, '| vm-model:', !!modelSelect);
        return;
    }

    // "Nuevo modelo" solo se habilita cuando hay una marca seleccionada.
    function syncVmNewModelButton() {
        if (vmNewModelBtn) vmNewModelBtn.disabled = !brandSelect.value;
    }

    async function loadVmBrands() {
        try {
            const res = await fetch('/api/brands');
            const data = await res.json();
            const selected = brandSelect.dataset.selected || '';
            brandSelect.innerHTML = '<option value="">Seleccionar marca...</option>';
            data.forEach(b => {
                const opt = new Option(b.name, b.id);
                if (String(b.id) === String(selected)) opt.selected = true;
                brandSelect.add(opt);
            });
            if (selected) loadVmModels(selected);
            syncVmNewModelButton();
        } catch (e) { /* noop */ }
    }

    function loadVmModels(brandId, selectedModelId) {
        modelSelect.innerHTML = '<option value="">Seleccionar modelo...</option>';
        modelSelect.disabled = !brandId;
        syncVmNewModelButton();
        if (!brandId) return;
        fetch(`/api/models?brand_id=${encodeURIComponent(brandId)}`)
            .then(r => r.json())
            .then(data => {
                data.forEach(m => {
                    const opt = new Option(m.name, m.id);
                    if (String(m.id) === String(selectedModelId)) opt.selected = true;
                    modelSelect.add(opt);
                });
                modelSelect.disabled = false;
            })
            .catch(() => { modelSelect.disabled = true; });
    }

    brandSelect.addEventListener('change', function () {
        loadVmModels(this.value);
    });

    // Integra marca/modelo creados con el modal "Nueva marca / Nuevo modelo"
    document.addEventListener('brand-model-created', function (e) {
        const detail = e.detail || {};

        if (detail.brand) {
            const exists = [...brandSelect.options].some(o => String(o.value) === String(detail.brand.id));
            if (!exists) {
                brandSelect.add(new Option(detail.brand.name, detail.brand.id));
            }
            brandSelect.value = detail.brand.id;
        }

        if (detail.model && brandSelect.value) {
            // Recarga modelos desde el servidor (ya incluye el nuevo) y lo selecciona.
            loadVmModels(brandSelect.value, detail.model.id);
        }

        syncVmNewModelButton();
    });

    loadVmBrands();

    // Abrir modal: nueva placa (pre-rellena la placa escrita) o editar placa (pre-rellena todo)
    window.openVehicleModal = function (vehicleOrPlate) {
        closeModalState();

        if (vehicleOrPlate && typeof vehicleOrPlate === 'object' && vehicleOrPlate.id) {
            // MODO EDICIÓN
            editingVehicleId = vehicleOrPlate.id;
            document.getElementById('vm-title').textContent = 'Editar placa (vehículo)';
            document.getElementById('vm-plate').value = vehicleOrPlate.plate || '';
            document.getElementById('vm-color').value = vehicleOrPlate.color || '';
            document.getElementById('vm-year').value = vehicleOrPlate.year || '';
            document.getElementById('vm-vin').value = vehicleOrPlate.vin || '';
            document.getElementById('vm-engine').value = vehicleOrPlate.engine_number || '';
            document.getElementById('vm-body-type').value = vehicleOrPlate.body_type || '';
            document.getElementById('vm-review').value = vehicleOrPlate.technical_review_date || '';

            brandSelect.dataset.selected = vehicleOrPlate.brand_id || '';
            brandSelect.innerHTML = '<option value="">Seleccionar marca...</option>';
            loadVmBrands().then(() => {
                brandSelect.value = vehicleOrPlate.brand_id || '';
                loadVmModels(vehicleOrPlate.brand_id, vehicleOrPlate.model_id);
            });
        } else {
            // MODO NUEVO
            editingVehicleId = null;
            document.getElementById('vm-title').textContent = 'Nueva placa (vehículo)';
            const plate = typeof vehicleOrPlate === 'string' ? vehicleOrPlate : '';
            document.getElementById('vm-plate').value = plate;
        }

        modal.classList.remove('hidden');
        modal.classList.add('flex');
        document.getElementById('vm-plate').focus();
    };

    function closeModalState() {
        document.getElementById('vm-color').value = '';
        document.getElementById('vm-year').value = '';
        document.getElementById('vm-vin').value = '';
        document.getElementById('vm-engine').value = '';
        document.getElementById('vm-body-type').value = '';
        document.getElementById('vm-review').value = '';
        delete brandSelect.dataset.selected;
    }

    document.getElementById('vm-cancel').addEventListener('click', function () {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    });

    document.getElementById('vm-save').addEventListener('click', async function () {
        // Flag anti-doble envío: bloquear reentrada mientras se procesa
        if (saving) return;
        saving = true;

        try {
            await doSave();
        } finally {
            saving = false;
        }
    });

    async function doSave() {
        const plate = document.getElementById('vm-plate').value.trim().toUpperCase().replace(/[^A-Z0-9]/g, '').slice(0, 7);
        const brandId = document.getElementById('vm-brand').value;
        const modelId = document.getElementById('vm-model').value;
        if (!plate || !brandId || !modelId) { alert('Complete placa, marca y modelo.'); return; }

        const btn = document.getElementById('vm-save');
        btn.disabled = true;
        btn.textContent = 'Guardando...';

        const payload = {
            plate,
            brand_id: brandId,
            model_id: modelId,
            color: document.getElementById('vm-color').value.trim().toUpperCase() || null,
            year: document.getElementById('vm-year').value || null,
            vin: document.getElementById('vm-vin').value.trim().toUpperCase() || null,
            engine_number: document.getElementById('vm-engine').value.trim().toUpperCase() || null,
            body_type: document.getElementById('vm-body-type').value || null,
            technical_review_date: document.getElementById('vm-review').value || null,
        };

        try {
            const url = editingVehicleId
                ? `/api/vehicles/${editingVehicleId}/quick-update`
                : '/api/vehicles/quick-store';
            const method = editingVehicleId ? 'PUT' : 'POST';

            const res = await fetch(url, {
                method,
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content, 'Accept': 'application/json' },
                body: JSON.stringify(payload),
            });
            const data = await res.json().catch(() => ({}));
            if (!res.ok) { alert(data.errors ? Object.values(data.errors).flat().join(' ') : 'No se pudo guardar.'); return; }

            // Emitir evento con el vehículo guardado para que el formulario lo integre
            document.dispatchEvent(new CustomEvent('vehicle-saved', { detail: data }));

            modal.classList.add('hidden');
            modal.classList.remove('flex');
        } catch (e) {
            alert('Error al guardar el vehículo.');
        } finally {
            btn.disabled = false;
            btn.textContent = editingVehicleId ? 'Guardar cambios' : 'Guardar y seleccionar';
        }
    }

    } catch (error) {
        console.error('[vehicle-modal] Error inicializando el modal:', error);
    }
})();
</script>
@endpush
