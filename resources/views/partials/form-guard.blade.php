{{-- =============================================================
     Guard global de formularios: CSRF refresh + Anti-doble envío
     Incluir en layouts/guest.blade.php y layouts/app.blade.php
     (antes de @stack('scripts') para que aplique a toda la página).

     Regla CSRF: intercepta el submit y renueva el token justo antes
     del envío para evitar errores 419 por expiración de sesión.

     Regla Anti-duplicados: deshabilita el botón submit y usa un flag
     booleano para impedir el doble clic / doble envío. Si el servidor
     devuelve error y el formulario se re-renderiza (validación), el
     flag se limpia y el botón se re-habilita naturalmente.

     Escape hatch: añadir data-prevent-double-submit="false" a un
     formulario para desactivar la protección anti-doble envío.
     ============================================================= --}}
<script>
(function () {
    'use strict';

    const CSRF_URL = '/api/csrf-token';
    const SPINNER_SVG = '<svg class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>';

    /**
     * Renueva el token CSRF llamando al endpoint público /api/csrf-token.
     * Actualiza el meta tag y TODOS los inputs ocultos _token del formulario.
     * Devuelve true si el token se obtuvo; false si la red falló (en cuyo caso
     * se deja el token original para no bloquear el envío).
     */
    async function refreshCsrfToken(form) {
        try {
            const res = await fetch(CSRF_URL, {
                method: 'GET',
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                credentials: 'same-origin',
                cache: 'no-store',
            });
            if (!res.ok) return false;
            const data = await res.json().catch(() => null);
            if (!data || !data.csrf_token) return false;

            const token = data.csrf_token;
            // Actualizar meta global (usado por los fetch de los modales AJAX)
            const meta = document.querySelector('meta[name="csrf-token"]');
            if (meta) meta.setAttribute('content', token);
            // Actualizar inputs ocultos _token del formulario
            form.querySelectorAll('input[name="_token"]').forEach(input => { input.value = token; });
            return true;
        } catch (e) {
            return false;
        }
    }

    /**
     * Marca el formulario como "enviando": flag booleano + botón deshabilitado
     * con spinner y texto de estado.
     */
    function markSubmitting(form) {
        if (form.dataset.preventDoubleSubmit === 'false') return;

        form.dataset.submitting = '1';
        const btn = form.querySelector('button[type="submit"]');
        if (!btn || btn.dataset.originalHtml) return;

        // Si el botón ya está deshabilitado (p. ej. lo deshabilitó otra lógica), respetarlo
        if (btn.disabled) return;

        btn.dataset.originalHtml = btn.innerHTML;
        const shortLabel = (btn.dataset.loadingText || form.dataset.loadingText || '').trim();
        const label = shortLabel || 'Guardando...';
        btn.innerHTML = SPINNER_SVG + '<span>' + label + '</span>';
        btn.disabled = true;
    }

    /**
     * Restaura el formulario (solo si aún está en la página) para permitir reintentos.
     */
    function restoreForm(form) {
        if (!form.isConnected) return;
        delete form.dataset.submitting;
        const btn = form.querySelector('button[type="submit"]');
        if (btn && btn.dataset.originalHtml) {
            btn.innerHTML = btn.dataset.originalHtml;
            delete btn.dataset.originalHtml;
            btn.disabled = false;
        }
    }

    document.addEventListener('submit', function (e) {
        const form = e.target;
        if (!(form instanceof HTMLFormElement)) return;

        // 0) Confirmación global (data-confirm): la acción destructiva se
        //    confirma con el modal ConfirmModal. Si aún no fue confirmada,
        //    abrimos el modal y detenemos el envío. Al confirmar, form-guard
        //    vuelve a entrar con data-confirmed="1" y continúa (CSRF + envío).
        if (form.dataset.confirm && form.dataset.confirmed !== '1') {
            e.preventDefault();
            e.stopImmediatePropagation();
            if (window.ConfirmModal) window.ConfirmModal.open(form);
            return;
        }
        // Limpiar el flag de confirmación tras pasar (por si se reenvía)
        delete form.dataset.confirmed;

        // 1) Anti-doble envío: si ya se está enviando, bloquear reentrada
        if (form.dataset.submitting === '1') {
            e.preventDefault();
            e.stopImmediatePropagation();
            return;
        }

        // 2) Respetar onsubmit inline que canceló (confirm/prompt rechazado)
        if (e.defaultPrevented) return;

        // 3) Escape hatch por formulario
        const protectDouble = form.dataset.preventDoubleSubmit !== 'false';
        if (!protectDouble && form.dataset.submitting !== '1') {
            return;
        }

        // 4) Interceptar el envío para renovar el token CSRF
        e.preventDefault();

        // Marcar como enviándose ANTES del fetch para bloquear doble clic ráfaga
        markSubmitting(form);

        refreshCsrfToken(form).finally(function () {
            // Si el formulario fue removido del DOM (navegación) no reintentar
            if (!form.isConnected) return;
            // Enviar de forma programática (no vuelve a disparar este listener
            // porque form.submit() no dispara el evento submit)
            form.submit();
        });
    }, true);
})();
</script>