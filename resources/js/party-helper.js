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
        if (!data.department || !data.province || !data.district) return;

        if (!data.ubigeo_code) {
            try {
                const distRes = await fetch(`/api/ubigeo/distritos?departamento=${encodeURIComponent(data.department)}&provincia=${encodeURIComponent(data.province)}`);
                if (distRes.ok) {
                    const distritos = await distRes.json();
                    const match = distritos.find(d => d.distrito.toUpperCase() === data.district.toUpperCase());
                    if (match) data.ubigeo_code = match.code;
                }
            } catch (e) { console.error(e); }
        }

        const depSelect = document.querySelector('select[name="departamento"]');
        const provSelect = document.querySelector('select[name="provincia"]');
        const distSelect = document.querySelector('select[name="ubigeo_code"]');

        if (depSelect) {
            selectOption(depSelect, data.department);
            await new Promise(r => setTimeout(r, 100));
        }
        if (provSelect) {
            selectOption(provSelect, data.province);
            await new Promise(r => setTimeout(r, 100));
        }
        if (distSelect) {
            selectOption(distSelect, data.district);
            if (data.ubigeo_code) {
                const opt = [...distSelect.options].find(o => o.value === data.ubigeo_code);
                if (opt) distSelect.value = opt.value;
            }
        }
    }

    async function fillForm(data) {
        if (data.type === 'person') {
            const typeSelect = document.querySelector('select[name="type"]');
            if (typeSelect) {
                selectOption(typeSelect, 'person');
                typeSelect.dispatchEvent(new Event('change'));
            }
            const firstName = document.querySelector('input[name="first_name"]');
            const lastName = document.querySelector('input[name="last_name"]');
            if (firstName && data.first_name) firstName.value = data.first_name.toUpperCase();
            if (lastName && data.last_name) lastName.value = data.last_name.toUpperCase();
        }

        if (data.type === 'company') {
            const typeSelect = document.querySelector('select[name="type"]');
            if (typeSelect) {
                selectOption(typeSelect, 'company');
                typeSelect.dispatchEvent(new Event('change'));
            }
            const businessName = document.querySelector('input[name="business_name"]');
            if (businessName && data.business_name) businessName.value = data.business_name.toUpperCase();

            const address = document.querySelector('textarea[name="address"]');
            if (address && data.address) address.value = data.address.toUpperCase();

            await autoFillUbigeo(data);
        }
    }

    function init(documentNumberInput, options = {}) {
        if (!documentNumberInput) return;

        const form = documentNumberInput.closest('form');
        const typeSelect = form?.querySelector('select[name="document_type"]');

        async function handleSearch() {
            const docType = (typeSelect?.value || options.documentType || '').toUpperCase();
            const docNumber = documentNumberInput.value.trim();

            if (docType === 'DNI' && docNumber.length !== 8) {
                alert('El DNI debe tener 8 dígitos.');
                return;
            }
            if (docType === 'RUC' && docNumber.length !== 11) {
                alert('El RUC debe tener 11 dígitos.');
                return;
            }
            if (!['DNI', 'RUC'].includes(docType)) {
                alert('Seleccione DNI o RUC para consultar automáticamente.');
                return;
            }

            const btn = options.button || document.querySelector('[data-party-search-btn]');
            if (btn) btn.disabled = true;

            try {
                const data = await searchDocument(docType, docNumber);
                await fillForm(data);
                if (options.onSuccess) options.onSuccess(data);
            } catch (error) {
                alert(error.message || 'Error al consultar el documento.');
            } finally {
                if (btn) btn.disabled = false;
            }
        }

        documentNumberInput.addEventListener('blur', function () {
            const docType = (typeSelect?.value || options.documentType || '').toUpperCase();
            const len = documentNumberInput.value.trim().length;
            if ((docType === 'DNI' && len === 8) || (docType === 'RUC' && len === 11)) {
                handleSearch();
            }
        });

        if (options.button) {
            options.button.addEventListener('click', (e) => {
                e.preventDefault();
                handleSearch();
            });
        }

        return { search: handleSearch };
    }

    window.PartyHelper = { init };
})();
