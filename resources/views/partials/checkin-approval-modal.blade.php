{{-- Modal de confirmación de aprobar/rechazar inventario (backoffice).
     Colores correspondientes: verde para aprobar, rojo para rechazar (con motivo opcional).
     Requiere en la página:
     - Formularios con id "form-approve" y "form-reject" (cada uno con @csrf).
     - En form-reject, un input oculto name="reason" (se llena desde el modal).
     - Botones con data-checkin-approve / data-checkin-reject. --}}
<div id="checkin-approval-modal" class="hidden fixed inset-0 z-50 items-center justify-center p-4" aria-hidden="true">
    <div class="fixed inset-0 bg-gray-500/75" data-checkin-approval-close></div>
    <div class="relative w-full max-w-md rounded-lg bg-white shadow-xl">
        <div class="p-6 text-center">
            <div id="checkin-approval-icon" class="mx-auto mb-4 flex h-12 w-12 items-center justify-center rounded-full bg-green-50 text-green-600">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
            </div>
            <h3 id="checkin-approval-title" class="text-lg font-semibold text-gray-800">¿Aprobar inventario?</h3>
            <p id="checkin-approval-text" class="mt-2 text-sm text-gray-500">Confirmarás el inventario. El cliente podrá continuar con el presupuesto.</p>
            <div id="checkin-approval-reason-wrap" class="hidden mt-4 text-left">
                <label for="checkin-approval-reason" class="block text-sm font-medium text-gray-700">Motivo del rechazo <span class="text-gray-400">(opcional)</span></label>
                <textarea id="checkin-approval-reason" rows="3" placeholder="Indica el motivo del rechazo..." class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 text-sm"></textarea>
            </div>
        </div>
        <div class="px-6 pb-6 flex flex-col-reverse sm:flex-row gap-3 justify-center">
            <button type="button" data-checkin-approval-close class="btn btn-secondary w-full sm:w-auto justify-center">Cancelar</button>
            <button type="button" id="checkin-approval-confirm" class="btn btn-primary w-full sm:w-auto justify-center">Sí, aprobar</button>
        </div>
    </div>
</div>

<script>
(function () {
    'use strict';
    const modal = document.getElementById('checkin-approval-modal');
    if (!modal) return;

    const formApprove = document.getElementById('form-approve');
    const formReject = document.getElementById('form-reject');
    const reason = document.getElementById('checkin-approval-reason');
    const reasonWrap = document.getElementById('checkin-approval-reason-wrap');
    const title = document.getElementById('checkin-approval-title');
    const text = document.getElementById('checkin-approval-text');
    const icon = document.getElementById('checkin-approval-icon');
    const confirmBtn = document.getElementById('checkin-approval-confirm');
    let mode = 'approve';

    const approveIcon = '<svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>';
    const rejectIcon = '<svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>';

    function openModal() {
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        modal.setAttribute('aria-hidden', 'false');
        document.body.style.overflow = 'hidden';
    }
    function closeModal() {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        modal.setAttribute('aria-hidden', 'true');
        document.body.style.overflow = '';
    }

    document.querySelectorAll('[data-checkin-approval-close]').forEach(function (el) {
        el.addEventListener('click', closeModal);
    });
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') closeModal();
    });

    document.querySelectorAll('[data-checkin-approve]').forEach(function (btn) {
        btn.addEventListener('click', function () {
            mode = 'approve';
            if (reasonWrap) reasonWrap.classList.add('hidden');
            if (reason) reason.value = '';
            title.textContent = '¿Aprobar inventario?';
            text.textContent = 'Confirmarás el inventario. El cliente podrá continuar con el presupuesto.';
            confirmBtn.textContent = 'Sí, aprobar';
            confirmBtn.className = 'btn btn-primary w-full sm:w-auto justify-center';
            icon.className = 'mx-auto mb-4 flex h-12 w-12 items-center justify-center rounded-full bg-green-50 text-green-600';
            icon.innerHTML = approveIcon;
            openModal();
        });
    });

    document.querySelectorAll('[data-checkin-reject]').forEach(function (btn) {
        btn.addEventListener('click', function () {
            mode = 'reject';
            if (reasonWrap) reasonWrap.classList.remove('hidden');
            if (reason) reason.value = '';
            title.textContent = '¿Rechazar inventario?';
            text.textContent = 'El inventario quedará rechazado y el cliente podrá revisar tu observación.';
            confirmBtn.textContent = 'Sí, rechazar';
            confirmBtn.className = 'btn btn-danger w-full sm:w-auto justify-center';
            icon.className = 'mx-auto mb-4 flex h-12 w-12 items-center justify-center rounded-full bg-red-50 text-red-600';
            icon.innerHTML = rejectIcon;
            openModal();
        });
    });

    confirmBtn.addEventListener('click', function () {
        if (mode === 'reject' && formReject) {
            const hidden = formReject.querySelector('input[name="reason"]');
            if (hidden) hidden.value = reason ? reason.value.trim() : '';
            formReject.requestSubmit();
            return;
        }
        if (formApprove) formApprove.requestSubmit();
    });
})();
</script>

