{{-- JS del modal de WhatsApp + botón "Copiar enlace".
     Requiere variables (opcionales): $actionUrl, $recipientsUrl, $initialMessage. --}}
<script>
(function () {
    'use strict';

    // Botón "Copiar enlace" (cualquier elemento con data-copy-link)
    document.querySelectorAll('[data-copy-link]').forEach(function (btn) {
        btn.addEventListener('click', function () {
            const link = this.dataset.copyLink;
            if (!link) return;
            const done = function () {
                const original = btn.innerHTML;
                btn.innerHTML = '¡Copiado!';
                btn.disabled = true;
                setTimeout(function () { btn.innerHTML = original; btn.disabled = false; }, 1600);
            };
            if (navigator.clipboard && navigator.clipboard.writeText) {
                navigator.clipboard.writeText(link).then(done).catch(done);
            } else {
                done();
            }
        });
    });

    const modal = document.getElementById('whatsapp-modal');
    const form = document.getElementById('whatsapp-form');
    if (!modal || !form) return;

    const actionUrl = @json($actionUrl ?? '');
    const recipientsUrl = @json($recipientsUrl ?? '');
    const initialMessage = @json($initialMessage ?? '');
    const phoneSelect = document.getElementById('whatsapp-phone');
    const nameHidden = document.getElementById('whatsapp-recipient-name');
    const messageField = document.getElementById('whatsapp-message');

    function openModal() {
        modal.classList.remove('hidden');
        modal.setAttribute('aria-hidden', 'false');
        document.body.style.overflow = 'hidden';
    }

    function closeModal() {
        modal.classList.add('hidden');
        modal.setAttribute('aria-hidden', 'true');
        document.body.style.overflow = '';
    }

    document.querySelectorAll('[data-whatsapp-close]').forEach(function (el) {
        el.addEventListener('click', closeModal);
    });

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') closeModal();
    });

    function loadRecipients() {
        if (!recipientsUrl) return;
        fetch(recipientsUrl, {
            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(function (res) { return res.json(); })
        .then(function (data) {
            phoneSelect.innerHTML = '<option value="">Seleccionar destinatario...</option>';
            (Array.isArray(data) ? data : []).forEach(function (r) {
                const opt = document.createElement('option');
                opt.value = r.contact_phone || '';
                opt.dataset.name = r.contact_name || '';
                const phone = r.contact_phone ? ' · ' + r.contact_phone : '';
                opt.textContent = (r.contact_name || 'Sin nombre') + ' (' + (r.role_label || r.role || '') + ')' + phone;
                phoneSelect.appendChild(opt);
            });
        })
        .catch(function () {});
    }

    function syncName() {
        const opt = phoneSelect.selectedOptions[0];
        nameHidden.value = opt ? (opt.dataset.name || '') : '';
    }
    phoneSelect.addEventListener('change', syncName);

    document.querySelectorAll('[data-whatsapp-open]').forEach(function (btn) {
        btn.addEventListener('click', function () {
            form.action = actionUrl;
            form.target = '_self';
            if (messageField) messageField.value = initialMessage;
            syncName();
            openModal();
            loadRecipients();
        });
    });

    // "Abrir WhatsApp" envía de forma SÍNCRONA (dentro del gesto del usuario) con
    // target="_blank": así el navegador no bloquea la pestaña nueva. Esto omite el
    // refresh CSRF del form-guard, pero el token de la página sigue siendo válido.
    document.querySelectorAll('[data-whatsapp-wa]').forEach(function (btn) {
        btn.addEventListener('click', function (e) {
            e.preventDefault();
            const sendMethod = document.getElementById('whatsapp-send-method');
            if (sendMethod) sendMethod.value = 'wa_me';
            form.target = '_blank';
            form.submit();
        });
    });

    // "Enviar por API" setea el método y deja que el form-guard procese el submit
    // (refresh CSRF + anti-doble envío) en la misma pestaña.
    document.querySelectorAll('[data-whatsapp-api]').forEach(function (btn) {
        btn.addEventListener('click', function () {
            const sendMethod = document.getElementById('whatsapp-send-method');
            if (sendMethod) sendMethod.value = 'api';
            form.target = '_self';
        });
    });
})();
</script>
