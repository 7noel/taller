/**
 * Consulta DNI/RUC desde la API de Reniec/Sunat.
 * Autocompleta los campos del formulario de Parties.
 */
(function () {
    'use strict';

    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;

    async function searchDocument(documentType, documentNumber) {
        const res = await fetch('/api/party/search-by-document', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
            },
            body: JSON.stringify({ document_type: documentType, document_number: documentNumber }),
        });

        if (!res.ok) {
            const data = await res.json().catch(() => ({}));
            throw new Error(data.error || 'No se encontraron datos');
        }

        return res.json();
    }

    function selectOption(select, value) {
        if (!select) return false;
        const opts = [...select.options];
        const found = opts.find(o => o.text.toUpperCase() === String(value).toUpperCase());
        if (found) {
            select.value = found.value;
            select.dispatchEvent(new Event('change'));
            return true;
        }
        return false;
    }

    async function autoFillUbigeo(data) {
        const depSelect = document.querySelector('select[name="departamento"]');
        const provSelect = document.querySelector('select[name="provincia"]');
        const distSelect = document.querySelector('select[name="ubigeo_code"]');
        if (!depSelect || !provSelect || !distSelect) return;

        // 1. Seleccionar departamento
        if (!data.department || !selectOption(depSelect, data.department)) return;

        // 2. Cargar provincias según departamento
        const provincesRes = await fetch(`/api/ubigeo/provincias?departamento=${encodeURIComponent(depSelect.value)}`);
        if (!provincesRes.ok) return;
        const provinces = await provincesRes.json();
        provSelect.innerHTML = '<option value="">Seleccionar...</option>';
        provinces.forEach(p => {
            const opt = document.createElement('option');
            opt.value = p;
            opt.textContent = p;
            provSelect.appendChild(opt);
        });
        provSelect.disabled = false;

        if (!data.province || !selectOption(provSelect, data.province)) return;

        // 3. Cargar distritos según provincia
        const districtsRes = await fetch(`/api/ubigeo/distritos?departamento=${encodeURIComponent(depSelect.value)}&provincia=${encodeURIComponent(provSelect.value)}`);
        if (!districtsRes.ok) return;
        const districts = await districtsRes.json();
        distSelect.innerHTML = '<option value="">Seleccionar...</option>';
        districts.forEach(d => {
            const opt = document.createElement('option');
            opt.value = d.code;
            opt.textContent = d.distrito;
            distSelect.appendChild(opt);
        });
        distSelect.disabled = false;

        // 4. Seleccionar distrito por código ubigeo exacto
        const match = data.ubigeo_code
            ? [...distSelect.options].find(o => o.value === data.ubigeo_code)
            : [...distSelect.options].find(o => o.text.toUpperCase() === (data.district || '').toUpperCase());
        if (match) distSelect.value = match.value;
    }

    async function fillForm(data) {
        // DNI (1): apellidos primero, luego nombre
        if (data.document_type === '1') {
            const lastName = document.querySelector('input[name="last_name"]');
            const firstName = document.querySelector('input[name="first_name"]');
            if (lastName && data.last_name) lastName.value = data.last_name.toUpperCase();
            if (firstName && data.first_name) firstName.value = data.first_name.toUpperCase();
        }

        // RUC (6): empresa
        if (data.document_type === '6') {
            const businessName = document.querySelector('input[name="business_name"]');
            if (businessName && data.business_name) businessName.value = data.business_name.toUpperCase();

            const address = document.querySelector('input[name="address"]');
            if (address && data.address) address.value = data.address.toUpperCase();

            await autoFillUbigeo(data);
        }
    }

    function init(documentNumberInput, options = {}) {
        if (!documentNumberInput) return;

        const form = documentNumberInput.closest('form');
        const typeSelect = form?.querySelector('select[name="document_type"]');
        const btn = options.button || document.querySelector('[data-party-search-btn]');

        function toggleSearchBtn() {
            const docType = (typeSelect?.value || options.documentType || '').trim();
            if (btn) btn.disabled = !['1', '6'].includes(docType);
        }

        async function handleSearch() {
            const docType = (typeSelect?.value || options.documentType || '').trim();
            const docNumber = documentNumberInput.value.trim();

            if (docType === '1') {
                if (!/^\d{8}$/.test(docNumber)) {
                    alert('El DNI debe tener 8 dígitos (puede empezar con cero).');
                    return;
                }
            } else if (docType === '6') {
                if (!/^\d{11}$/.test(docNumber)) {
                    alert('El RUC debe tener 11 dígitos.');
                    return;
                }
            } else {
                alert('Seleccione DNI o RUC para poder buscar.');
                return;
            }

            if (btn) btn.disabled = true;

            try {
                const data = await searchDocument(docType, docNumber);
                await fillForm(data);
                if (options.onSuccess) options.onSuccess(data);
            } catch (error) {
                alert(error.message || 'Error al consultar el documento.');
            } finally {
                if (btn) btn.disabled = false;
                toggleSearchBtn();
            }
        }

        // Al perder el foco, consultar automáticamente
        documentNumberInput.addEventListener('blur', function () {
            const docType = (typeSelect?.value || options.documentType || '').trim();
            const len = documentNumberInput.value.trim().length;
            if ((docType === '1' && len === 8) || (docType === '6' && len === 11)) {
                handleSearch();
            }
        });

        // Toggle del botón según tipo de documento
        typeSelect?.addEventListener('change', toggleSearchBtn);
        toggleSearchBtn();

        if (btn) {
            btn.addEventListener('click', (e) => {
                e.preventDefault();
                handleSearch();
            });
        }

        return { search: handleSearch };
    }

    window.PartyHelper = { init };
})();