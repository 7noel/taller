{{-- =============================================================
     Modal reutilizable: Buscar / Registrar / Editar contacto (Party)
     - Un solo panel (sin pestañas). Campo de búsqueda inteligente:
       * 8 dígitos  → busca local por DNI; si no existe, consulta RENIEC
       * 11 dígitos → busca local por RUC; si no existe, consulta SUNAT
       * texto      → dropdown propio por nombre (sin Tom Select)
     - Búsqueda en vivo (debounce 350ms, sin botón).
     - Si existe: carga los datos, permite editarlos (botón "Actualizar y agregar")
       + icono ↗ junto al documento para ver la ficha completa. El título
       cambia a "Editar contacto" (sin banner).
     - Rol "Compañía de seguros": SOLO permite seleccionar aseguradoras ya
       registradas (is_insurance_company=true). No crea ni renombra; solo
       permite editar datos de contacto (celular/teléfono/email/dirección).
     - Compacto: Rol + principal en un row; Celular/Tel/Email en grid-3;
       "Datos fiscales" colapsados y opcionales.
     - API: window.ContactModal.open({ roles, showPrimary, onSelect })
       onSelect(party, { mode: 'exists'|'created'|'updated', role })
     ============================================================= --}}

<div id="contactModal" class="fixed inset-0 bg-gray-900 bg-opacity-50 hidden items-center justify-center z-50 p-4">
    <div class="bg-white rounded-lg shadow-xl max-w-lg w-full p-6 max-h-screen overflow-y-auto">
        <h3 id="cm-title" class="text-xl font-bold mb-1 text-gray-800">Buscar / Registrar contacto</h3>
        <p class="text-sm text-gray-500 mb-4">Busca un contacto existente o regístralo si no existe.</p>

        {{-- Banner de error general (validación inline) --}}
        <div id="cm-error" class="hidden mb-4 rounded-md bg-red-50 border border-red-200 px-4 py-3 text-sm text-red-700"></div>

        <div class="grid grid-cols-1 gap-4">
            {{-- Fila 1: Rol + Contacto comercial principal (mismo row) --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 items-end">
                <div>
                    <label for="cm-role" class="block text-sm font-medium text-gray-700">Rol *</label>
                    <select id="cm-role" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">Seleccionar...</option>
                    </select>
                </div>
                <div class="pb-1" id="cm-primary-wrap">
                    <label class="inline-flex items-center gap-2 text-sm font-medium text-gray-700 cursor-pointer select-none">
                        <input type="checkbox" id="cm-primary" class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500">
                        Contacto comercial principal
                    </label>
                </div>
            </div>

            {{-- Fila 2: Búsqueda única inteligente (autocompletado en vivo, sin botón) --}}
            <div>
                <label for="cm-search" class="block text-sm font-medium text-gray-700">Buscar contacto</label>
                <div class="relative">
                    <input type="text" id="cm-search" placeholder="Escribe DNI, RUC o nombre..." autocomplete="off"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    {{-- Dropdown de resultados por nombre (oculto) --}}
                    <div id="cm-search-results" class="hidden absolute z-20 mt-1 w-full bg-white border border-gray-200 rounded-md shadow-lg max-h-48 overflow-y-auto"></div>
                </div>
                <p class="mt-1 text-xs text-gray-500">Busca por DNI, RUC o nombre. Si el número no está en tu agenda, se consultará en RENIEC/SUNAT automáticamente.</p>
            </div>

            {{-- Fila 3: Documento (+ icono ↗ para ver ficha solo si existe) --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="cm-doc-type" class="block text-sm font-medium text-gray-700">Tipo de documento *</label>
                    <select id="cm-doc-type" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="1">DNI</option>
                        <option value="6">RUC</option>
                        <option value="4">Carné de Extranjería</option>
                        <option value="7">Pasaporte</option>
                        <option value="A">Cédula Diplomática</option>
                    </select>
                    <p id="cm-doc-type-error" class="mt-1 text-xs text-red-600 hidden"></p>
                </div>
                <div>
                    <label for="cm-doc-number" class="block text-sm font-medium text-gray-700">Número de documento <span id="cm-doc-number-req" class="text-red-500 hidden">*</span></label>
                    <div class="flex gap-2 items-stretch">
                        <input type="text" id="cm-doc-number" maxlength="15" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 uppercase">
                        <a id="cm-external-link" href="#" target="_blank" rel="noopener" title="Ver ficha completa"
                           class="hidden mt-1 shrink-0 inline-flex items-center px-2.5 bg-gray-100 border border-gray-200 text-gray-600 rounded-md text-sm hover:bg-gray-200 hover:text-gray-900 transition">↗</a>
                    </div>
                    <p id="cm-doc-number-error" class="mt-1 text-xs text-red-600 hidden"></p>
                </div>
            </div>

            {{-- Fila 4: Persona / Empresa --}}
            <div id="cm-person-fields" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="cm-first-name" class="block text-sm font-medium text-gray-700">Nombres <span class="text-red-500">*</span></label>
                    <input type="text" id="cm-first-name" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <p id="cm-first-name-error" class="mt-1 text-xs text-red-600 hidden"></p>
                </div>
                <div>
                    <label for="cm-last-name" class="block text-sm font-medium text-gray-700">Apellidos</label>
                    <input type="text" id="cm-last-name" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>
            </div>
            <div id="cm-company-field" class="hidden">
                <label for="cm-business-name" class="block text-sm font-medium text-gray-700">Razón social *</label>
                <input type="text" id="cm-business-name" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                <p id="cm-business-name-error" class="mt-1 text-xs text-red-600 hidden"></p>
            </div>

            {{-- Fila 5: Celular / Teléfono / Email (grid-3, compacto) --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label for="cm-mobile" class="block text-sm font-medium text-gray-700">Celular *</label>
                    <input type="text" id="cm-mobile" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <p id="cm-mobile-error" class="mt-1 text-xs text-red-600 hidden"></p>
                </div>
                <div>
                    <label for="cm-phone" class="block text-sm font-medium text-gray-700">Teléfono</label>
                    <input type="text" id="cm-phone" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>
                <div>
                    <label for="cm-email" class="block text-sm font-medium text-gray-700">Email</label>
                    <input type="email" id="cm-email" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>
            </div>

            {{-- Fila 6: Datos fiscales opcionales (colapsado) --}}
            <div>
                <button type="button" id="cm-billing-toggle" class="inline-flex items-center gap-1 text-sm font-medium text-blue-600 hover:text-blue-800">
                    <svg class="w-4 h-4 transition-transform" id="cm-billing-chevron" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                    Datos fiscales (opcional)
                </button>
                <div id="cm-billing-fields" class="hidden mt-3">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label for="cm-departamento" class="block text-sm font-medium text-gray-700">Departamento</label>
                            <select id="cm-departamento" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"><option value="">Seleccionar...</option></select>
                        </div>
                        <div>
                            <label for="cm-provincia" class="block text-sm font-medium text-gray-700">Provincia</label>
                            <select id="cm-provincia" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" disabled><option value="">Seleccione departamento...</option></select>
                        </div>
                        <div>
                            <label for="cm-distrito" class="block text-sm font-medium text-gray-700">Distrito</label>
                            <select id="cm-distrito" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" disabled><option value="">Seleccione provincia...</option></select>
                        </div>
                    </div>
                    <div class="mt-3">
                        <label for="cm-address" class="block text-sm font-medium text-gray-700">Dirección</label>
                        <input type="text" id="cm-address" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" maxlength="255">
                    </div>
                </div>
            </div>

            {{-- Fila 7: Notas --}}
            <div>
                <label for="cm-notes" class="block text-sm font-medium text-gray-700">Notas</label>
                <input type="text" id="cm-notes" maxlength="1000" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" placeholder="Observaciones (opcional)">
            </div>
        </div>

        <div class="mt-6 flex gap-2 justify-end">
            <button type="button" id="cm-cancel" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300">Cancelar</button>
            <button type="button" id="cm-save" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700">Guardar y agregar</button>
        </div>
    </div>
</div>

@push('scripts')
<script>
(function () {
    'use strict';

    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
    const documentTypeLabels = { '1': 'DNI', '6': 'RUC', '4': 'CEX', '7': 'PAS', 'A': 'Cédula Diplomática' };
    const insuranceRole = 'insurance_company';

    const modal = document.getElementById('contactModal');
    const titleEl = document.getElementById('cm-title');
    const errBox = document.getElementById('cm-error');
    const externalLink = document.getElementById('cm-external-link');

    const roleSelect = document.getElementById('cm-role');
    const primaryWrap = document.getElementById('cm-primary-wrap');
    const chkPrimary = document.getElementById('cm-primary');

    const searchInput = document.getElementById('cm-search');
    const searchResults = document.getElementById('cm-search-results');

    const docType = document.getElementById('cm-doc-type');
    const docNumber = document.getElementById('cm-doc-number');
    const docNumberReq = document.getElementById('cm-doc-number-req');
    const firstName = document.getElementById('cm-first-name');
    const lastName = document.getElementById('cm-last-name');
    const businessName = document.getElementById('cm-business-name');
    const mobile = document.getElementById('cm-mobile');
    const phone = document.getElementById('cm-phone');
    const email = document.getElementById('cm-email');
    const billingToggle = document.getElementById('cm-billing-toggle');
    const billingChevron = document.getElementById('cm-billing-chevron');
    const billingFields = document.getElementById('cm-billing-fields');
    const departamento = document.getElementById('cm-departamento');
    const provincia = document.getElementById('cm-provincia');
    const distrito = document.getElementById('cm-distrito');
    const address = document.getElementById('cm-address');
    const notes = document.getElementById('cm-notes');
    const personFields = document.getElementById('cm-person-fields');
    const companyField = document.getElementById('cm-company-field');
    const btnSave = document.getElementById('cm-save');
    const btnCancel = document.getElementById('cm-cancel');

    let config = null;
    let existingParty = null;      // Party cargado por búsqueda (modo edición de existente)
    let state = { mode: 'new' };   // 'new' | 'exists'
    let debounceTimer = null;

    // ===================== Utilidades =====================
    function showError(msg) {
        if (!msg) { errBox.classList.add('hidden'); errBox.textContent = ''; return; }
        errBox.textContent = msg;
        errBox.classList.remove('hidden');
    }

    function setFieldError(id, msg) {
        const el = document.getElementById(id);
        const errorEl = document.getElementById(id + '-error');
        if (!el) return;
        if (msg) {
            if (errorEl) { errorEl.textContent = msg; errorEl.classList.remove('hidden'); }
            el.classList.add('border-red-500');
        } else {
            if (errorEl) { errorEl.textContent = ''; errorEl.classList.add('hidden'); }
            el.classList.remove('border-red-500');
        }
    }

    function clearAllErrors() {
        ['cm-doc-number', 'cm-first-name', 'cm-business-name', 'cm-mobile'].forEach(id => setFieldError(id, ''));
        showError('');
    }

    function setReadonly(el, readonly) {
        el.readOnly = readonly;
        el.classList.toggle('bg-gray-100', readonly);
        el.classList.toggle('cursor-not-allowed', readonly);
    }

    function showExistsState(party) {
        existingParty = party;
        state = { mode: 'exists' };
        setReadonly(docType, false);
        setReadonly(docNumber, false);
        setReadonly(businessName, false);
        titleEl.textContent = 'Editar contacto';
        externalLink.href = `/parties/${party.id}`;
        externalLink.classList.remove('hidden');
        btnSave.textContent = 'Actualizar y agregar';
    }

    function resetState() {
        existingParty = null;
        state = { mode: 'new' };
        setReadonly(docType, false);
        setReadonly(docNumber, false);
        setReadonly(businessName, false);
        titleEl.textContent = 'Buscar / Registrar contacto';
        externalLink.classList.add('hidden');
        externalLink.href = '#';
        btnSave.textContent = 'Guardar y agregar';
    }

    // ===================== Campos dinámicos según rol =====================
    function docNumberRequired(role) {
        return role === 'owner' || role === 'billing';
    }

    function applyRoleDefaults(role) {
        docNumberReq.classList.toggle('hidden', !docNumberRequired(role));
        if (role === insuranceRole) {
            docType.value = '6';
            // Solo-selección: documento y razón social son de solo lectura
            setReadonly(docType, true);
            setReadonly(docNumber, true);
            setReadonly(businessName, true);
        } else if (role !== 'billing' && !existingParty) {
            docType.value = '1';
        }
        togglePersonCompany();
    }

    function togglePersonCompany() {
        const isCompany = docType.value === '6';
        personFields.classList.toggle('hidden', isCompany);
        companyField.classList.toggle('hidden', !isCompany);
    }

    // ===================== Cascada Ubigeo (opcional) =====================
    async function loadDepartamentos() {
        try {
            const data = await (await fetch('/api/ubigeo/departamentos')).json();
            departamento.innerHTML = '<option value="">Seleccionar...</option>';
            data.forEach(d => departamento.add(new Option(d, d)));
        } catch (e) { /* noop */ }
    }
    async function loadProvincias() {
        provincia.disabled = true; distrito.disabled = true;
        distrito.innerHTML = '<option value="">Seleccione provincia...</option>';
        if (!departamento.value) { provincia.innerHTML = '<option value="">Seleccione departamento...</option>'; return; }
        const data = await (await fetch(`/api/ubigeo/provincias?departamento=${encodeURIComponent(departamento.value)}`)).json();
        provincia.innerHTML = '<option value="">Seleccionar...</option>';
        data.forEach(p => provincia.add(new Option(p, p)));
        provincia.disabled = false;
    }
    async function loadDistritos() {
        distrito.disabled = true;
        if (!provincia.value) { distrito.innerHTML = '<option value="">Seleccione provincia...</option>'; return; }
        const data = await (await fetch(`/api/ubigeo/distritos?departamento=${encodeURIComponent(departamento.value)}&provincia=${encodeURIComponent(provincia.value)}`)).json();
        distrito.innerHTML = '<option value="">Seleccionar...</option>';
        data.forEach(d => distrito.add(new Option(d.distrito, d.code)));
        distrito.disabled = false;
    }

    async function selectUbigeoByCode(code) {
        if (!code) return;
        try {
            const data = await (await fetch(`/api/ubigeo/resolve?code=${encodeURIComponent(code)}`)).json();
            await loadDepartamentos();
            departamento.value = data.departamento;
            await loadProvincias();
            provincia.value = data.provincia;
            await loadDistritos();
            distrito.value = code;
        } catch (e) { /* noop */ }
    }

    // ===================== Búsqueda única inteligente (en vivo) =====================
    function closeResults() { searchResults.classList.add('hidden'); searchResults.innerHTML = ''; }

    function isInsuranceOnly() {
        return roleSelect.value === insuranceRole;
    }

    function fillForm(party) {
        // En modo compañía de seguros se ignora silenciosamente cualquier party no aseguradora
        if (isInsuranceOnly() && !party.is_insurance_company) {
            return;
        }
        showExistsState(party);
        docType.value = party.document_type || '1';
        docNumber.value = party.document_number || '';
        firstName.value = party.first_name || '';
        lastName.value = party.last_name || '';
        businessName.value = party.business_name || '';
        mobile.value = party.mobile || '';
        phone.value = party.phone || '';
        email.value = party.email || '';
        address.value = party.address || '';
        if (party.ubigeo_code) { billingFields.classList.remove('hidden'); billingChevron.classList.add('rotate-180'); selectUbigeoByCode(party.ubigeo_code); }
        togglePersonCompany();
        applyRoleDefaults(roleSelect.value);
    }

    function clearForm() {
        resetState();
        docNumber.value = '';
        firstName.value = '';
        lastName.value = '';
        businessName.value = '';
        mobile.value = '';
        phone.value = '';
        email.value = '';
        address.value = '';
        departamento.value = ''; provincia.value = ''; distrito.value = '';
        provincia.disabled = true; distrito.disabled = true;
        billingFields.classList.add('hidden'); billingChevron.classList.remove('rotate-180');
        togglePersonCompany();
        applyRoleDefaults(roleSelect.value);
    }

    function guessDocType(value) {
        const digits = value.replace(/\D/g, '');
        return digits.length === 11 ? '6' : '1';
    }

    function runNameSearch(q) {
        let url = `/api/parties/search?q=${encodeURIComponent(q)}&limit=8`;
        if (isInsuranceOnly()) url += '&is_insurance_company=1';
        fetch(url)
            .then(r => r.json())
            .then(data => {
                // Defensa determinística: en modo compañía solo se muestran aseguradoras
                if (isInsuranceOnly()) {
                    data = data.filter(p => p.is_insurance_company);
                }
                if (data.length === 0) {
                    searchResults.innerHTML = '<div class="px-3 py-2 text-sm text-gray-500">' + (isInsuranceOnly()
                        ? 'No se encontró una compañía de seguros. Puedes crearla desde el módulo Clientes.'
                        : 'No se encontró en la agenda. Regístralo manualmente.') + '</div>';
                    searchResults.classList.remove('hidden');
                    return;
                }
                searchResults.innerHTML = data.map(p => `
                    <button type="button" data-id="${p.id}" class="cm-result block w-full text-left px-3 py-2 hover:bg-gray-50 text-sm">
                        <span class="font-medium text-gray-900">${escapeHtml(p.display_name)}</span>
                        <span class="block text-xs text-gray-500">${documentTypeLabels[p.document_type] || p.document_type || ''} ${p.document_number || ''}${p.mobile ? ' · ' + p.mobile : ''}</span>
                    </button>`).join('');
                searchResults.classList.remove('hidden');
            })
            .catch(() => showError('Error al buscar por nombre.'));
    }

    function runDocSearch(dt, digits) {
        docType.value = dt;
        docNumber.value = digits;
        let url = `/api/parties/search?document_type=${dt}&document_number=${digits}`;
        if (isInsuranceOnly()) url += '&is_insurance_company=1';
        fetch(url)
            .then(r => r.json())
            .then(data => {
                if (data[0]) {
                    if (!isInsuranceOnly() || data[0].is_insurance_company) { fillForm(data[0]); }
                    return;
                }
                if (isInsuranceOnly()) {
                    return; // silencioso: si no hay aseguradora con ese RUC, no aparece
                }
                // No existe local → consultar RENIEC/SUNAT automáticamente
                showError(`Consultando ${dt === '1' ? 'RENIEC' : 'SUNAT'}...`);
                return fetch('/api/party/search-by-document', {
                    method: 'POST', headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                    body: JSON.stringify({ document_type: dt, document_number: digits }),
                })
                .then(res => res.json().catch(() => ({})))
                .then(extData => {
                    if (extData && (extData.first_name || extData.business_name)) {
                        if (dt === '1') { firstName.value = extData.first_name || ''; lastName.value = extData.last_name || ''; }
                        else {
                            businessName.value = extData.business_name || '';
                            if (extData.ubigeo_code) {
                                billingFields.classList.remove('hidden');
                                billingChevron.classList.add('rotate-180');
                                address.value = extData.address || '';
                                selectUbigeoByCode(extData.ubigeo_code);
                            }
                        }
                        showError(`Datos de ${dt === '1' ? 'RENIEC' : 'SUNAT'}: verifica y guarda.`);
                    } else {
                        showError(`No se encontró el ${dt === '1' ? 'DNI' : 'RUC'}. Completa los datos para registrarlo.`);
                    }
                })
                .catch(() => showError('No se pudo consultar RENIEC/SUNAT. Regístralo manualmente.'));
            })
            .catch(() => showError('Error al buscar el documento.'));
    }

    function runSearch() {
        const q = searchInput.value.trim();
        if (!q) { closeResults(); clearAllErrors(); return; }

        const digits = q.replace(/\D/g, '');

        // Caso 1: es un documento (8 DNI / 11 RUC)
        if ((digits.length === 8 || digits.length === 11) && /^\d+$/.test(q)) {
            closeResults();
            clearAllErrors();
            if (!isInsuranceOnly() || digits.length === 11) {
                runDocSearch(guessDocType(q), digits);
            }
            // En modo compañía, un DNI de 8 dígitos simplemente no produce resultado
            return;
        }

        // Caso 2: búsqueda por nombre → dropdown en vivo
        clearAllErrors();
        if (q.length >= 3) {
            runNameSearch(q);
        } else {
            closeResults();
        }
    }

    document.getElementById('cm-search-results').addEventListener('click', function (e) {
        const btn = e.target.closest('.cm-result');
        if (!btn) return;
        const id = btn.dataset.id;
        searchInput.value = '';
        closeResults();
        fetch(`/api/parties/search?id=${encodeURIComponent(id)}`).then(r => r.json()).then(data => {
            if (!data[0]) return;
            if (isInsuranceOnly() && !data[0].is_insurance_company) {
                return; // silencioso: no aparece en la lista
            }
            fillForm(data[0]);
        });
    });

    searchInput.addEventListener('input', function () {
        clearTimeout(debounceTimer);
        const q = this.value.trim();
        if (!q) { closeResults(); clearAllErrors(); return; }
        debounceTimer = setTimeout(runSearch, 350);
    });
    searchInput.addEventListener('keydown', function (e) {
        if (e.key === 'Enter') { e.preventDefault(); clearTimeout(debounceTimer); runSearch(); }
        if (e.key === 'Escape') closeResults();
    });
    searchInput.addEventListener('blur', function () { setTimeout(closeResults, 150); });

    function escapeHtml(str) {
        return String(str ?? '').replace(/[&<>"']/g, m => ({ '&': '&', '<': '<', '>': '>', '"': '"', "'": '&#039;' }[m]));
    }

    // ===================== Botón Guardar =====================
    btnSave.addEventListener('click', async function () {
        showError('');
        clearAllErrors();

        const role = roleSelect.value;
        const isCompany = docType.value === '6';
        const isInsOnly = isInsuranceOnly();
        const docNumberVal = docNumber.value.trim();

        let valid = true;
        if (!role) { showError('Seleccione un rol.'); valid = false; }
        if (!isInsOnly) {
            // Nº documento obligatorio solo en propietario/facturación
            if (docNumberRequired(role) && !docNumberVal) { setFieldError('cm-doc-number', 'Ingrese el número de documento.'); valid = false; }
            if (isCompany && !businessName.value.trim()) { setFieldError('cm-business-name', 'Ingrese la razón social.'); valid = false; }
            if (!isCompany && !firstName.value.trim() && !lastName.value.trim()) { setFieldError('cm-first-name', 'Ingrese nombres o apellidos.'); valid = false; }
        }
        if (!mobile.value.trim()) { setFieldError('cm-mobile', 'Ingrese el celular.'); valid = false; }
        if (!valid) return;

        if (isInsOnly && !existingParty) {
            showError('Seleccione primero una compañía de seguros existente. No se puede crear una nueva aquí.');
            return;
        }

        const payload = {
            role: role,
            document_type: isInsOnly ? undefined : docType.value,
            document_number: isInsOnly ? undefined : docNumberVal,
            first_name: isInsOnly ? undefined : (isCompany ? null : firstName.value.trim()),
            last_name: isInsOnly ? undefined : (isCompany ? null : lastName.value.trim()),
            business_name: isInsOnly ? undefined : (isCompany ? businessName.value.trim() : null),
            email: email.value.trim() || null,
            phone: phone.value.trim() || null,
            mobile: mobile.value.trim() || null,
            ubigeo_code: distrito.value || null,
            address: address.value.trim() || null,
            is_insurance_company: isInsOnly ? 1 : null,
        };

        const btn = btnSave;
        btn.disabled = true;
        btn.textContent = existingParty ? 'Guardando...' : 'Registrando...';
        try {
            const url = existingParty ? `/api/parties/${existingParty.id}/quick-update` : '/api/parties/quick-store';
            const method = existingParty ? 'PUT' : 'POST';
            const res = await fetch(url, {
                method: method,
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                body: JSON.stringify(payload),
            });
            const data = await res.json().catch(() => ({}));
            if (!res.ok) {
                showError(data.errors ? Object.values(data.errors).flat().join(' ') : (data.message || 'No se pudo guardar el contacto.'));
                return;
            }

            const mode = existingParty ? 'updated' : (state.mode === 'exists' ? 'exists' : 'created');
            if (config && typeof config.onSelect === 'function') {
                config.onSelect(data, { mode: mode, role: role, is_primary_commercial: chkPrimary.checked, notes: notes.value.trim() });
            }
            closeModal();
        } catch (e) {
            showError('Error de conexión al guardar el contacto.');
        } finally {
            btn.disabled = false;
            btn.textContent = existingParty ? 'Actualizar y agregar' : 'Guardar y agregar';
        }
    });

    // ===================== Modal open / close =====================
    function openModal(cfg) {
        config = cfg || {};
        state = { mode: 'new' };
        existingParty = null;

        // Roles configurables
        roleSelect.innerHTML = '<option value="">Seleccionar...</option>';
        (config.roles || ['owner', 'driver', 'approver', 'operator', 'billing', 'insurance_company', 'emergency_contact', 'other']).forEach(r => {
            roleSelect.add(new Option(config.roleLabels?.[r] || r, r));
        });
        roleSelect.value = '';
        primaryWrap.classList.toggle('hidden', config.showPrimary === false);

        chkPrimary.checked = false;
        notes.value = '';
        searchInput.value = '';
        closeResults();
        clearForm();

        // Modo edición: prellenar con un contacto existente
        if (config.initialParty) {
            roleSelect.value = config.initialRole || '';
            fillForm(config.initialParty);
            chkPrimary.checked = !!config.initialPrimary;
            notes.value = config.initialNotes || '';
        }

        modal.classList.remove('hidden');
        modal.classList.add('flex');
        setTimeout(() => searchInput.focus(), 50);
    }

    function closeModal() {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }

    btnCancel.addEventListener('click', closeModal);
    modal.addEventListener('click', function (e) { if (e.target === modal) closeModal(); });
    document.addEventListener('keydown', function (e) { if (e.key === 'Escape' && !modal.classList.contains('hidden')) { closeModal(); closeResults(); } });
    docType.addEventListener('change', togglePersonCompany);
    roleSelect.addEventListener('change', function () {
        // Al cambiar de rol, limpiar búsqueda y resultados residuales del modo anterior
        searchInput.value = '';
        closeResults();
        clearForm();
        applyRoleDefaults(this.value);
    });

    billingToggle.addEventListener('click', function () {
        const willShow = billingFields.classList.contains('hidden');
        billingFields.classList.toggle('hidden', !willShow);
        billingChevron.classList.toggle('rotate-180', willShow);
    });

    departamento.addEventListener('change', loadProvincias);
    provincia.addEventListener('change', loadDistritos);
    loadDepartamentos();

    window.ContactModal = { open: openModal };
})();
</script>
@endpush