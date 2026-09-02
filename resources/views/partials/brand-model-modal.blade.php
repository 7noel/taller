{{--
    Modal reutilizable "Nueva marca / Nuevo modelo".

    Uso:
        @include('partials.brand-model-modal')
        ... y junto a los selects de Marca/Modelo del formulario:
        <button type="button" data-bmm-open="brand">Nueva marca</button>
        <button type="button" data-bmm-open="model">Nuevo modelo</button>

    El partial expone window.BrandModelModal y detecta los botones [data-bmm-open]
    dentro del formulario, resolviendo los selects con [data-bmm-brand] / [data-bmm-model].

    Comportamiento:
    - Modo "brand": pide nombre de marca (obligatorio) y nombre del modelo (obligatorio).
      Crea ambos vía /api/brands/find-or-create + /api/models/find-or-create.
    - Modo "model": requiere marca ya seleccionada; crea solo el modelo.
    - Nombres siempre en MAYÚSCULAS (transformación en vivo + respaldo del servidor).
    - Verificación de duplicados: si el nombre ya existe se reutiliza (find-or-create)
      y se avisa en el modal ("ya existía y fue seleccionado").
    - Emite el evento 'brand-model-created' con detail { brand: {id,name,existed}|null, model: {id,name,existed} }
      para que cada formulario seleccione la marca/modelo creados.
--}}
<div id="brandModelModal" class="fixed inset-0 bg-gray-900/60 hidden items-center justify-center z-[70] p-4">
    <div class="bg-white rounded-lg shadow-xl max-w-md w-full p-6">
        <div class="flex items-start justify-between gap-3">
            <h3 id="bmm-title" class="text-lg font-semibold text-gray-800">Nueva marca</h3>
            <button type="button" id="bmm-close-x" class="shrink-0 p-1 rounded-md text-gray-400 hover:text-gray-600 transition" title="Cerrar">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <div id="bmm-brand-field" class="mt-4">
            <label for="bmm-brand-name" class="block text-sm font-medium text-gray-700">Nombre de la marca <span class="text-red-500">*</span></label>
            <input type="text" id="bmm-brand-name" maxlength="120" placeholder="EJ. TOYOTA" autocomplete="off"
                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
        </div>

        <p id="bmm-brand-context" class="mt-4 hidden text-sm text-gray-600">
            Se agregará el modelo a la marca <span id="bmm-brand-context-name" class="font-semibold"></span>.
        </p>

        <div class="mt-4">
            <label for="bmm-model-name" class="block text-sm font-medium text-gray-700">Nombre del modelo <span class="text-red-500">*</span></label>
            <input type="text" id="bmm-model-name" maxlength="120" placeholder="EJ. COROLLA" autocomplete="off"
                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
        </div>

        <p id="bmm-error" class="mt-3 hidden text-sm text-red-600"></p>
        <p id="bmm-feedback" class="mt-3 hidden text-sm text-green-700 bg-green-50 border border-green-200 rounded-lg px-3 py-2"></p>

        <div class="mt-6 flex gap-2 justify-end">
            <button type="button" id="bmm-cancel" class="btn btn-secondary">Cancelar</button>
            <button type="button" id="bmm-save" class="btn btn-primary">Guardar</button>
        </div>
    </div>
</div>

@push('scripts')
<script>
(function () {
    'use strict';

    // Guard: si el partial ya se inicializó (página que lo incluye dos veces) no duplicar lógica.
    if (window.BrandModelModal) return;

    const modal = document.getElementById('brandModelModal');
    if (!modal) return;

    const title = document.getElementById('bmm-title');
    const brandField = document.getElementById('bmm-brand-field');
    const brandInput = document.getElementById('bmm-brand-name');
    const brandContext = document.getElementById('bmm-brand-context');
    const brandContextName = document.getElementById('bmm-brand-context-name');
    const modelInput = document.getElementById('bmm-model-name');
    const errorBox = document.getElementById('bmm-error');
    const feedbackBox = document.getElementById('bmm-feedback');
    const btnSave = document.getElementById('bmm-save');
    const btnCancel = document.getElementById('bmm-cancel');
    const btnCloseX = document.getElementById('bmm-close-x');

    let mode = 'brand';      // 'brand' => crear marca + modelo | 'model' => crear solo modelo
    let brandId = null;      // marca ya seleccionada en el formulario (modo 'model')
    let brandName = '';
    let saving = false;
    let closeTimer = null;

    function csrfToken() {
        return document.querySelector('meta[name="csrf-token"]')?.content || '';
    }

    function showError(message) {
        errorBox.textContent = message;
        errorBox.classList.remove('hidden');
        feedbackBox.classList.add('hidden');
    }

    function clearMessages() {
        errorBox.classList.add('hidden');
        feedbackBox.classList.add('hidden');
    }

    function showFeedback(message) {
        errorBox.classList.add('hidden');
        feedbackBox.textContent = message;
        feedbackBox.classList.remove('hidden');
    }

    async function postJson(url, payload) {
        const res = await fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken(), // se lee al momento del envío (regla CSRF)
            },
            body: JSON.stringify(payload),
        });
        const data = await res.json().catch(() => ({}));
        if (!res.ok) {
            const msg = data.message
                || (data.errors ? Object.values(data.errors).flat().join(' ') : null)
                || (res.status === 403 ? 'No tienes permiso para realizar esta acción.' : 'No se pudo guardar.');
            throw new Error(msg);
        }
        return data;
    }

    function close() {
        window.clearTimeout(closeTimer);
        closeTimer = null;
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        brandInput.value = '';
        modelInput.value = '';
        clearMessages();
        btnSave.disabled = false;
        btnSave.textContent = 'Guardar';
        brandId = null;
        brandName = '';
        saving = false;
    }

    function open(nextMode, opts) {
        opts = opts || {};
        window.clearTimeout(closeTimer);
        closeTimer = null;
        clearMessages();
        brandInput.value = '';
        modelInput.value = '';
        btnSave.disabled = false;
        btnSave.textContent = 'Guardar';

        mode = nextMode === 'model' ? 'model' : 'brand';

        if (mode === 'model') {
            brandId = opts.brandId || null;
            brandName = (opts.brandName || '').toString().trim();

            if (!brandId) {
                // Botón "Nuevo modelo" sin marca seleccionada: no debe abrir.
                return false;
            }
            title.textContent = 'Nuevo modelo';
            brandField.classList.add('hidden');
            brandContext.classList.remove('hidden');
            brandContextName.textContent = brandName;
            modelInput.focus();
        } else {
            brandId = null;
            title.textContent = 'Nueva marca';
            brandContext.classList.add('hidden');
            brandField.classList.remove('hidden');
            brandInput.focus();
        }

        modal.classList.remove('hidden');
        modal.classList.add('flex');
        return true;
    }

    async function doSave() {
        const brandName = brandInput.value.trim().toUpperCase();
        const modelName = modelInput.value.trim().toUpperCase();

        if (mode === 'brand' && !brandName) {
            showError('Escribe el nombre de la marca.');
            return;
        }
        if (mode === 'model' && !brandId) {
            showError('Primero selecciona una marca en el formulario.');
            return;
        }
        if (!modelName) {
            showError('Escribe el nombre del modelo.');
            return;
        }

        btnSave.disabled = true;
        btnSave.textContent = 'Guardando...';
        clearMessages();

        try {
            let brand = null;

            if (mode === 'brand') {
                brand = await postJson('/api/brands/find-or-create', { name: brandName });
            }

            const model = await postJson('/api/models/find-or-create', {
                brand_id: brandId || brand.id,
                name: modelName,
            });

            document.dispatchEvent(new CustomEvent('brand-model-created', {
                detail: {
                    brand: brand ? { id: brand.id, name: brand.name, existed: brand.existed } : null,
                    model: { id: model.id, name: model.name, existed: model.existed },
                },
            }));

            // Mensaje que confirma la verificación de duplicados (nunca se duplica).
            if (brand && brand.existed && model.existed) {
                showFeedback(`La marca «${brand.name}» y el modelo «${model.name}» ya existían y fueron seleccionados.`);
            } else if (brand && brand.existed) {
                showFeedback(`La marca «${brand.name}» ya existía. Se creó y seleccionó el modelo «${model.name}».`);
            } else if (brand) {
                showFeedback(`Marca «${brand.name}» y modelo «${model.name}» creados y seleccionados.`);
            } else if (model.existed) {
                showFeedback(`El modelo «${model.name}» ya existía y fue seleccionado.`);
            } else {
                showFeedback(`Modelo «${model.name}» creado y seleccionado.`);
            }

            // Cierra solo después de que el usuario pueda leer la confirmación.
            closeTimer = window.setTimeout(close, 1000);
        } catch (err) {
            showError(err.message || 'No se pudo guardar. Intenta de nuevo.');
            btnSave.disabled = false;
            btnSave.textContent = 'Guardar';
        }
    }

    function onInputUpper(e) {
        if (e.target.value === e.target.value.toUpperCase()) return;
        e.target.value = e.target.value.toUpperCase();
    }
    brandInput.addEventListener('input', onInputUpper);
    modelInput.addEventListener('input', onInputUpper);

    btnSave.addEventListener('click', async function () {
        if (saving) return; // flag anti-doble envío
        saving = true;
        try {
            await doSave();
        } finally {
            saving = false;
        }
    });

    btnCancel.addEventListener('click', close);
    btnCloseX.addEventListener('click', close);

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && !modal.classList.contains('hidden')) close();
    });

    modal.addEventListener('click', function (e) {
        if (e.target === modal) close();
    });

    // Detecta los botones "Nueva marca" / "Nuevo modelo" y resuelve los selects
    // de marca/modelo del formulario mediante [data-bmm-brand] / [data-bmm-model].
    document.querySelectorAll('[data-bmm-open]').forEach(function (btn) {
        btn.addEventListener('click', function () {
            const form = btn.closest('form') || document;
            const brandSelect = form.querySelector('[data-bmm-brand]');
            const selectedBrand = brandSelect ? brandSelect.options[brandSelect.selectedIndex] : null;

            open(btn.dataset.bmmOpen, {
                brandId: selectedBrand && selectedBrand.value ? selectedBrand.value : null,
                brandName: selectedBrand ? selectedBrand.textContent : '',
            });
        });
    });

    window.BrandModelModal = { open: open, close: close };
})();
</script>
@endpush
