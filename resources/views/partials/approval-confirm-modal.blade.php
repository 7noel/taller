{{-- Modal de confirmación de aprobación/rechazo del portal del cliente.
     Requiere: $entityName ('inventario' | 'presupuesto').
     Espera en la página:
     - Formularios con id "form-approve" y "form-reject" (cada uno con @csrf).
     - Botones con data-approve-open / data-reject-open.
     - Para rechazo: textarea con id "reject-reason" y <p id="reject-reason-error">. --}}
@php
    $approveTitle = $approveTitle ?? '¿Aprobar ' . $entityName . '?';
    $approveText = $approveText ?? 'Confirmarás el ' . $entityName . '. El taller continuará con el siguiente paso.';
    $approveLabel = $approveLabel ?? 'Sí, aprobar ' . $entityName;
    $rejectTitle = $rejectTitle ?? '¿Rechazar ' . $entityName . '?';
    $rejectText = $rejectText ?? 'El ' . $entityName . ' quedará rechazado y el taller revisará tu observación antes de continuar.';
    $rejectLabel = $rejectLabel ?? 'Sí, rechazar ' . $entityName;
@endphp

<div id="approval-modal" class="hidden fixed inset-0 z-50 items-center justify-center p-4" aria-hidden="true">
    <div class="fixed inset-0 bg-gray-500/75" data-approval-close></div>
    <div class="relative w-full max-w-md rounded-lg bg-white shadow-xl">
        <div class="p-6 text-center">
            <div id="approval-modal-icon" class="mx-auto mb-4 flex h-12 w-12 items-center justify-center rounded-full bg-blue-50 text-blue-600">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
            </div>
            <h3 id="approval-modal-title" class="text-lg font-semibold text-gray-800">{{ $approveTitle }}</h3>
            <p id="approval-modal-text" class="mt-2 text-sm text-gray-500">{{ $approveText }}</p>
        </div>
        <div class="px-6 pb-6 flex flex-col-reverse sm:flex-row gap-3">
            <button type="button" data-approval-close class="btn btn-secondary w-full sm:w-auto justify-center">Cancelar</button>
            <button type="button" id="approval-modal-confirm" class="btn btn-primary w-full sm:w-auto justify-center">{{ $approveLabel }}</button>
        </div>
    </div>
</div>

<script>
(function () {
    'use strict';
    const modal = document.getElementById('approval-modal');
    if (!modal) return;

    const formApprove = document.getElementById('form-approve');
    const formReject = document.getElementById('form-reject');
    const reason = document.getElementById('reject-reason');
    const reasonError = document.getElementById('reject-reason-error');
    const title = document.getElementById('approval-modal-title');
    const text = document.getElementById('approval-modal-text');
    const icon = document.getElementById('approval-modal-icon');
    const confirmBtn = document.getElementById('approval-modal-confirm');

    const approveIcon = '<svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>';
    const rejectIcon = '<svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>';
    let mode = 'approve';

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

    document.querySelectorAll('[data-approval-close]').forEach(function (el) {
        el.addEventListener('click', closeModal);
    });
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') closeModal();
    });

    document.querySelectorAll('[data-approve-open]').forEach(function (btn) {
        btn.addEventListener('click', function () {
            mode = 'approve';
            if (reasonError) reasonError.classList.add('hidden');
            title.textContent = @json($approveTitle);
            text.textContent = @json($approveText);
            confirmBtn.textContent = @json($approveLabel);
            confirmBtn.className = 'btn btn-primary w-full sm:w-auto justify-center';
            icon.className = 'mx-auto mb-4 flex h-12 w-12 items-center justify-center rounded-full bg-blue-50 text-blue-600';
            icon.innerHTML = approveIcon;
            openModal();
        });
    });

    document.querySelectorAll('[data-reject-open]').forEach(function (btn) {
        btn.addEventListener('click', function () {
            if (reason && reason.value.trim() === '') {
                if (reasonError) reasonError.classList.remove('hidden');
                if (reason) reason.focus();
                return;
            }
            mode = 'reject';
            if (reasonError) reasonError.classList.add('hidden');
            title.textContent = @json($rejectTitle);
            text.textContent = @json($rejectText);
            confirmBtn.textContent = @json($rejectLabel);
            confirmBtn.className = 'btn btn-danger w-full sm:w-auto justify-center';
            icon.className = 'mx-auto mb-4 flex h-12 w-12 items-center justify-center rounded-full bg-red-50 text-red-600';
            icon.innerHTML = rejectIcon;
            openModal();
        });
    });

    if (reason) {
        reason.addEventListener('input', function () {
            if (reason.value.trim() && reasonError) reasonError.classList.add('hidden');
        });
    }

    confirmBtn.addEventListener('click', function () {
        if (mode === 'reject' && formReject) {
            formReject.requestSubmit();
            return;
        }
        if (formApprove) formApprove.requestSubmit();
    });
})();
</script>
