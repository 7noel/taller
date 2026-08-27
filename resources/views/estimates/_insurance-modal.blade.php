{{-- Modal: registrar aprobación/rechazo del SEGURO con fecha (y motivo en el rechazo).
     Requiere en la página:
     - Formularios con id "form-insurance-approve" y "form-insurance-reject" (cada uno con @csrf
       e inputs ocultos name="date" / name="reason").
     - Botones con data-insurance-approve / data-insurance-reject. --}}
<div id="insurance-modal" class="hidden fixed inset-0 z-50 items-center justify-center p-4" aria-hidden="true">
    <div class="fixed inset-0 bg-gray-500/75" data-insurance-close></div>
    <div class="relative w-full max-w-md rounded-lg bg-white shadow-xl">
        <div class="p-6">
            <div id="insurance-icon" class="mx-auto mb-4 flex h-12 w-12 items-center justify-center rounded-full bg-green-50 text-green-600">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
            </div>
            <h3 id="insurance-title" class="text-lg font-semibold text-gray-800 text-center">¿Aprobar presupuesto por el seguro?</h3>
            <p id="insurance-text" class="mt-2 text-sm text-gray-500 text-center">Registrarás la fecha en que la aseguradora aprobó el presupuesto.</p>

            <div class="mt-5">
                <label for="insurance-date" class="block text-sm font-medium text-gray-700">Fecha de la aprobación del seguro <span class="text-red-500">*</span></label>
                <input type="date" id="insurance-date" value="{{ now()->format('Y-m-d') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                <p id="insurance-date-error" class="hidden mt-1 text-xs text-red-600">Indica la fecha.</p>
            </div>

            <div id="insurance-reason-wrap" class="hidden mt-4">
                <label for="insurance-reason" class="block text-sm font-medium text-gray-700">Motivo del rechazo <span class="text-red-500">*</span></label>
                <textarea id="insurance-reason" rows="3" placeholder="Indica el motivo del rechazo del seguro..." class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 text-sm"></textarea>
                <p id="insurance-reason-error" class="hidden mt-1 text-xs text-red-600">Indica el motivo del rechazo.</p>
            </div>
        </div>
        <div class="px-6 pb-6 flex flex-col-reverse sm:flex-row gap-3 justify-center">
            <button type="button" data-insurance-close class="btn btn-secondary w-full sm:w-auto justify-center">Cancelar</button>
            <button type="button" id="insurance-confirm" class="btn btn-primary w-full sm:w-auto justify-center">Sí, aprobar</button>
        </div>
    </div>
</div>

<script>
(function () {
    'use strict';
    const modal = document.getElementById('insurance-modal');
    if (!modal) return;

    const formApprove = document.getElementById('form-insurance-approve');
    const formReject = document.getElementById('form-insurance-reject');
    const dateInput = document.getElementById('insurance-date');
    const reason = document.getElementById('insurance-reason');
    const reasonWrap = document.getElementById('insurance-reason-wrap');
    const dateError = document.getElementById('insurance-date-error');
    const reasonError = document.getElementById('insurance-reason-error');
    const title = document.getElementById('insurance-title');
    const text = document.getElementById('insurance-text');
    const icon = document.getElementById('insurance-icon');
    const confirmBtn = document.getElementById('insurance-confirm');
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

    document.querySelectorAll('[data-insurance-close]').forEach(function (el) {
        el.addEventListener('click', closeModal);
    });
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') closeModal();
    });

    document.querySelectorAll('[data-insurance-approve]').forEach(function (btn) {
        btn.addEventListener('click', function () {
            mode = 'approve';
            if (reasonWrap) reasonWrap.classList.add('hidden');
            if (reason) reason.value = '';
            if (dateError) dateError.classList.add('hidden');
            if (reasonError) reasonError.classList.add('hidden');
            if (dateInput) dateInput.value = dateInput.value || '{{ now()->format('Y-m-d') }}';
            title.textContent = '¿Aprobar presupuesto por el seguro?';
            text.textContent = 'Registrarás la fecha en que la aseguradora aprobó el presupuesto.';
            confirmBtn.textContent = 'Sí, aprobar';
            confirmBtn.className = 'btn btn-primary w-full sm:w-auto justify-center';
            icon.className = 'mx-auto mb-4 flex h-12 w-12 items-center justify-center rounded-full bg-green-50 text-green-600';
            icon.innerHTML = approveIcon;
            openModal();
        });
    });

    document.querySelectorAll('[data-insurance-reject]').forEach(function (btn) {
        btn.addEventListener('click', function () {
            mode = 'reject';
            if (reasonWrap) reasonWrap.classList.remove('hidden');
            if (reason) reason.value = '';
            if (dateError) dateError.classList.add('hidden');
            if (reasonError) reasonError.classList.add('hidden');
            if (dateInput) dateInput.value = dateInput.value || '{{ now()->format('Y-m-d') }}';
            title.textContent = '¿Rechazar presupuesto por el seguro?';
            text.textContent = 'Indica la fecha del rechazo y el motivo; podrás corregir el presupuesto y reenviarlo.';
            confirmBtn.textContent = 'Sí, rechazar';
            confirmBtn.className = 'btn btn-danger w-full sm:w-auto justify-center';
            icon.className = 'mx-auto mb-4 flex h-12 w-12 items-center justify-center rounded-full bg-red-50 text-red-600';
            icon.innerHTML = rejectIcon;
            openModal();
        });
    });

    confirmBtn.addEventListener('click', function () {
        let valid = true;
        const dateValue = dateInput ? dateInput.value.trim() : '';
        if (!dateValue) {
            valid = false;
            if (dateError) dateError.classList.remove('hidden');
        } else if (dateError) {
            dateError.classList.add('hidden');
        }

        const reasonValue = reason ? reason.value.trim() : '';
        if (mode === 'reject' && !reasonValue) {
            valid = false;
            if (reasonError) reasonError.classList.remove('hidden');
        } else if (reasonError) {
            reasonError.classList.add('hidden');
        }

        if (!valid) return;

        if (mode === 'approve' && formApprove) {
            const hiddenDate = formApprove.querySelector('input[name="date"]');
            if (hiddenDate) hiddenDate.value = dateValue;
            formApprove.requestSubmit();
            return;
        }
        if (mode === 'reject' && formReject) {
            const hiddenDate = formReject.querySelector('input[name="date"]');
            const hiddenReason = formReject.querySelector('input[name="reason"]');
            if (hiddenDate) hiddenDate.value = dateValue;
            if (hiddenReason) hiddenReason.value = reasonValue;
            formReject.requestSubmit();
        }
    });
})();
</script>


