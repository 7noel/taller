{{-- =============================================================
     Modal global de confirmación de acciones destructivas
     - Abrir con formulario: window.ConfirmModal.open(form, { message })
       El form debe tener data-confirm="Mensaje..." (o pasar un mensaje
       opcional en opts.message). Al confirmar marca form.dataset.confirmed='1'
       y llama form.requestSubmit() para volver a pasar por form-guard.
     - Abrir con callback (acciones AJAX / fetch):
       window.ConfirmModal.open(null, { message, confirmLabel, onConfirm })
       Al confirmar ejecuta onConfirm() una sola vez.
     - Prohibido usar onsubmit="return confirm(...)" en el proyecto.
     ============================================================= --}}
<div id="confirmModal" class="fixed inset-0 bg-gray-900 bg-opacity-50 hidden items-center justify-center z-50 p-4">
    <div class="bg-white rounded-lg shadow-xl max-w-md w-full p-6">
        <div class="flex items-start gap-3">
            <div class="shrink-0 inline-flex h-10 w-10 items-center justify-center rounded-full bg-red-50">
                <svg class="h-5 w-5 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v4m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                </svg>
            </div>
            <div class="min-w-0">
                <h3 class="text-lg font-bold text-gray-800">¿Confirmar acción?</h3>
                <p id="confirmModalMessage" class="mt-1 text-sm text-gray-600">¿Estás seguro de realizar esta acción?</p>
            </div>
        </div>

        <div class="mt-6 flex gap-2 justify-end">
            <button type="button" id="confirmModalCancel" class="btn btn-secondary">Cancelar</button>
            <button type="button" id="confirmModalOk" class="btn btn-danger">Eliminar</button>
        </div>
    </div>
</div>

@push('scripts')
<script>
(function () {
    'use strict';

    const modal = document.getElementById('confirmModal');
    const messageEl = document.getElementById('confirmModalMessage');
    const btnOk = document.getElementById('confirmModalOk');
    const btnCancel = document.getElementById('confirmModalCancel');

    let pendingForm = null;
    let pendingCallback = null;

    function open(target, opts) {
        opts = opts || {};
        pendingForm = null;
        pendingCallback = null;

        if (typeof target === 'function') {
            pendingCallback = target;
        } else {
            pendingForm = target;
        }

        const fallback = pendingForm ? (pendingForm?.dataset?.confirm || '¿Estás seguro de realizar esta acción?') : '¿Estás seguro de realizar esta acción?';
        messageEl.textContent = opts.message || fallback;
        btnOk.textContent = opts.confirmLabel || 'Eliminar';
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        setTimeout(() => btnOk.focus(), 50);
    }

    function close() {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        pendingForm = null;
        pendingCallback = null;
    }

    function confirmIt() {
        if (pendingCallback) {
            const cb = pendingCallback;
            pendingCallback = null;
            close();
            cb();
            return;
        }
        if (!pendingForm) {
            close();
            return;
        }
        const form = pendingForm;
        pendingForm = null;
        form.dataset.confirmed = '1';
        close();
        // requestSubmit re-dispara el evento submit → form-guard (CSRF + anti-doble)
        form.requestSubmit();
    }

    btnOk.addEventListener('click', confirmIt);
    btnCancel.addEventListener('click', close);
    modal.addEventListener('click', function (e) { if (e.target === modal) close(); });
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && !modal.classList.contains('hidden')) close();
    });

    window.ConfirmModal = { open: open, close: close };
})();
</script>
@endpush