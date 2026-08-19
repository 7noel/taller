{{-- =============================================================
     Contactos del vehículo (VehicleRelationship)
     - Tom Select: busca parties en api/parties/search
     - Roles en español (VehicleRelationship::roleLabels)
     - Checkbox "Contacto comercial principal" (por fila y al agregar)
     - Lista dinámica con eliminación/re-render
     - Modal "Crear party nueva" -> api/parties/quick-store
     ============================================================= --}}

@php
    $relationshipRoleLabels = \App\Models\VehicleRelationship::roleLabels();

    if (old('relationships')) {
        // Reconstruir después de un error de validación
        $relationshipInitialRows = collect(old('relationships'))->map(function ($rel) {
            return [
                'party_id' => $rel['party_id'] ?? null,
                'party_label' => null, // se completa vía api/parties/search?id=
                'doc_label' => null,
                'doc_number' => null,
                'role' => $rel['role'] ?? null,
                'is_primary_commercial' => !empty($rel['is_primary_commercial']),
                'notes' => $rel['notes'] ?? null,
            ];
        })->values();
    } else {
        $relationshipInitialRows = isset($vehicle) && $vehicle->relationships
            ? $vehicle->relationships->map(function ($rel) {
                return [
                    'party_id' => $rel->party_id,
                    'party_label' => $rel->party?->display_name,
                    'doc_label' => $rel->party?->document_type_label,
                    'doc_number' => $rel->party?->document_number,
                    'role' => $rel->role,
                    'is_primary_commercial' => (bool) $rel->is_primary_commercial,
                    'notes' => $rel->notes,
                ];
            })->values()
            : collect();
    }
@endphp

<div class="mt-8 border-t border-gray-200 pt-6">
    <h3 class="text-lg font-semibold text-gray-800">Contactos del vehículo</h3>
    <p class="text-sm text-gray-500 mt-1 mb-4">Propietario, conductores, aprobadores, aseguradora y otros contactos relacionados.</p>

    {{-- Panel de agregado --}}
    <div class="bg-gray-50 rounded-lg border border-gray-200 p-4">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
            <div class="lg:col-span-2">
                <label for="rel-party-id" class="block text-sm font-medium text-gray-700">Contacto *</label>
                <select id="rel-party-id" class="mt-1 block w-full" placeholder="Buscar por nombre o documento..."></select>
                @error('relationships.*.party_id')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="rel-role" class="block text-sm font-medium text-gray-700">Rol *</label>
                <select id="rel-role" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="">Seleccionar...</option>
                    @foreach ($relationshipRoleLabels as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </select>
                @error('relationships.*.role')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="lg:col-span-2">
                <label for="rel-notes" class="block text-sm font-medium text-gray-700">Notas</label>
                <input type="text" id="rel-notes" maxlength="1000"
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                       placeholder="Observaciones (opcional)">
            </div>
        </div>

        <div class="mt-4 flex flex-wrap items-center gap-x-4 gap-y-2">
            <label class="inline-flex items-center gap-2 text-sm font-medium text-gray-700 cursor-pointer select-none">
                <input type="checkbox" id="rel-primary" class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500">
                Contacto comercial principal
            </label>

            <span class="flex-1 hidden lg:block"></span>

            <button type="button" id="rel-btn-add"
                    class="inline-flex items-center px-2 py-1 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                + Agregar
            </button>

            <button type="button" id="rel-btn-new"
                    class="inline-flex items-center px-2 py-1 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                + Crear contacto
            </button>
        </div>
    </div>

    {{-- Lista dinámica de relaciones --}}
    <div id="rel-container" class="mt-4 space-y-3"></div>

    <p id="rel-empty" class="mt-4 text-sm text-gray-500 @if ($relationshipInitialRows->count() > 0) hidden @endif">
        Sin contactos asociados. Busca y agrega el primero.
    </p>
</div>

{{-- =============== Modal: Crear party nueva =============== --}}
<div id="relModalNewParty" class="fixed inset-0 bg-gray-900 bg-opacity-50 hidden items-center justify-center z-50 p-4">
    <div class="bg-white rounded-lg shadow-xl max-w-lg w-full p-6 max-h-screen overflow-y-auto">
        <h3 class="text-xl font-bold mb-1 text-gray-800">Crear nuevo contacto</h3>
        <p class="text-sm text-gray-500 mb-4">Se guardará en la agenda y se asociará al vehículo.</p>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label for="rel-new-doc-type" class="block text-sm font-medium text-gray-700">Tipo de documento *</label>
                <select id="rel-new-doc-type" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="1">DNI</option>
                    <option value="6">RUC</option>
                    <option value="4">Carné de Extranjería</option>
                    <option value="7">Pasaporte</option>
                    <option value="A">Cédula Diplomática</option>
                </select>
            </div>

            <div>
                <label for="rel-new-doc-number" class="block text-sm font-medium text-gray-700">Número de documento *</label>
                <input type="text" id="rel-new-doc-number" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
            </div>

            <div id="rel-new-person" class="md:col-span-2 grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="rel-new-first-name" class="block text-sm font-medium text-gray-700">Nombres</label>
                    <input type="text" id="rel-new-first-name" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>
                <div>
                    <label for="rel-new-last-name" class="block text-sm font-medium text-gray-700">Apellidos</label>
                    <input type="text" id="rel-new-last-name" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>
            </div>

            <div id="rel-new-company" class="md:col-span-2 hidden">
                <label for="rel-new-business-name" class="block text-sm font-medium text-gray-700">Razón social *</label>
                <input type="text" id="rel-new-business-name" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
            </div>

            <div>
                <label for="rel-new-email" class="block text-sm font-medium text-gray-700">Email</label>
                <input type="email" id="rel-new-email" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
            </div>

            <div>
                <label for="rel-new-phone" class="block text-sm font-medium text-gray-700">Teléfono</label>
                <input type="text" id="rel-new-phone" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
            </div>

            <div>
                <label for="rel-new-mobile" class="block text-sm font-medium text-gray-700">Celular</label>
                <input type="text" id="rel-new-mobile" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
            </div>
        </div>

        <div class="mt-6 flex gap-2 justify-end">
            <button type="button" id="rel-new-cancel"
                    class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                Cancelar
            </button>
            <button type="button" id="rel-new-save"
                    class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                Guardar y agregar
            </button>
        </div>
    </div>
</div>

@push('scripts')
<script>
(function () {
    'use strict';

    // ===================== Config =====================
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
    const roleLabels = @json($relationshipRoleLabels);
    const initialRows = @json($relationshipInitialRows);
    const documentTypeLabels = { '1': 'DNI', '6': 'RUC', '4': 'CEX', '7': 'PAS', 'A': 'Cédula Diplomática' };

    const container = document.getElementById('rel-container');
    const emptyMsg = document.getElementById('rel-empty');
    const partySelectEl = document.getElementById('rel-party-id');
    const roleSelect = document.getElementById('rel-role');
    const notesInput = document.getElementById('rel-notes');
    const chkPrimary = document.getElementById('rel-primary');
    const btnAdd = document.getElementById('rel-btn-add');
    const btnNew = document.getElementById('rel-btn-new');

    // Modal
    const modal = document.getElementById('relModalNewParty');
    const btnNewCancel = document.getElementById('rel-new-cancel');
    const btnNewSave = document.getElementById('rel-new-save');
    const newDocType = document.getElementById('rel-new-doc-type');
    const newDocNumber = document.getElementById('rel-new-doc-number');
    const newFirstName = document.getElementById('rel-new-first-name');
    const newLastName = document.getElementById('rel-new-last-name');
    const newBusinessName = document.getElementById('rel-new-business-name');
    const newEmail = document.getElementById('rel-new-email');
    const newPhone = document.getElementById('rel-new-phone');
    const newMobile = document.getElementById('rel-new-mobile');
    const personFields = document.getElementById('rel-new-person');
    const companyFields = document.getElementById('rel-new-company');

    let rows = initialRows.map(r => ({ ...r }));          // estado de filas
    let partyCache = {};                                  // caché para evitar búsquedas repetidas

    // ===================== Helpers =====================
    function escapeHtml(str) {
        if (str === null || str === undefined) return '';
        return String(str).replace(/[&<>"']/g, function (m) {
            switch (m) {
                case '&': return '&' + 'amp;';
                case '<': return '&' + 'lt;';
                case '>': return '&' + 'gt;';
                case '"': return '&' + 'quot;';
                default: return '&#0' + '39;';
            }
        });
    }

    function roleLabel(role) {
        return roleLabels[role] || role || '';
    }

    function rowTemplate(data, index) {
        const primaryBadge = data.is_primary_commercial
            ? '<span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">⭐ Contacto comercial principal</span>'
            : '';

        return `
        <div class="rel-row bg-white border border-gray-200 rounded-lg p-4" data-index="${index}">
            <div class="flex flex-wrap items-start justify-between gap-3">
                <div class="min-w-0">
                    <p class="font-medium text-gray-900">${escapeHtml(data.party_label || `Contacto #${index + 1}`)}</p>
                    <p class="text-sm text-gray-500">
                        ${escapeHtml(data.doc_label || '')}${data.doc_number ? ' ' + escapeHtml(data.doc_number) : ''}
                    </p>
                    <div class="mt-1 flex flex-wrap gap-2 items-center">
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">${escapeHtml(roleLabel(data.role))}</span>
                        ${primaryBadge}
                        ${data.notes ? `<span class="text-xs text-gray-500">${escapeHtml(data.notes)}</span>` : ''}
                    </div>
                </div>
                <button type="button" class="rel-row-remove text-red-600 hover:text-red-800 text-sm font-medium">✕ Eliminar</button>
            </div>
            <div class="mt-3 grid grid-cols-1 md:grid-cols-3 gap-3">
                <input type="hidden" name="relationships[${index}][party_id]" value="${escapeHtml(data.party_id)}">

                <div>
                    <label class="block text-xs font-medium text-gray-600">Rol</label>
                    <select name="relationships[${index}][role]" class="rel-row-role mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                        ${Object.entries(roleLabels).map(([value, label]) =>
                            `<option value="${value}" ${String(value) === String(data.role) ? 'selected' : ''}>${label}</option>`
                        ).join('')}
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-600">Contacto comercial principal</label>
                    <div class="mt-2 flex items-center gap-2">
                        <input type="hidden" name="relationships[${index}][is_primary_commercial]" value="0">
                        <input type="checkbox" value="1" class="rel-row-primary rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500 text-sm" ${data.is_primary_commercial ? 'checked' : ''}>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-600">Notas</label>
                    <input type="text" name="relationships[${index}][notes]" value="${escapeHtml(data.notes || '')}" maxlength="1000"
                           class="rel-row-notes mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                </div>
            </div>
        </div>`;
    }

    function renderRows() {
        container.innerHTML = rows.map((r, i) => rowTemplate(r, i)).join('');
        emptyMsg.classList.toggle('hidden', rows.length > 0);
    }

    // Si las filas vienen de old() sin label, los completa vía api/parties/search?id=
    async function fillMissingPartyLabels() {
        let changed = false;
        for (let i = 0; i < rows.length; i++) {
            if (!rows[i].party_label && rows[i].party_id) {
                try {
                    const data = await fetchPartyById(rows[i].party_id);
                    if (data) {
                        rows[i].party_label = data.display_name;
                        rows[i].doc_label = documentTypeLabels[data.document_type] || data.document_type;
                        rows[i].doc_number = data.document_number;
                        changed = true;
                    }
                } catch (e) { /* mantener placeholder */ }
            }
        }
        if (changed) renderRows();
    }

    async function fetchPartyById(id) {
        if (partyCache[id]) return partyCache[id];
        const res = await fetch(`/api/parties/search?id=${encodeURIComponent(id)}`);
        if (!res.ok) throw new Error('Party no encontrada');
        const data = await res.json();
        if (data[0]) partyCache[data[0].id] = { ...data[0], display_name: data[0].display_name };
        return data[0] || null;
    }

    // ===================== Tom Select (búsqueda de parties) =====================
    const partySelect = new TomSelect(partySelectEl, {
        valueField: 'id',
        labelField: 'display_name',
        searchField: ['display_name', 'document_number'],
        copyClassesToDropdown: false,
        placeholder: 'Buscar por nombre o documento...',
        create: false,
        maxOptions: 20,
        shouldLoad: function (query) { return query.length >= 2; },
        load: function (query, callback) {
            fetch(`/api/parties/search?q=${encodeURIComponent(query)}&limit=20`)
                .then(r => r.json())
                .then(data => {
                    data.forEach(p => { partyCache[p.id] = { ...p, display_name: p.display_name }; });
                    callback(data.map(p => ({
                        id: p.id,
                        display_name: p.display_name,
                        document_type: p.document_type,
                        document_number: p.document_number,
                        phone: p.phone,
                        email: p.email,
                    })));
                })
                .catch(() => callback());
        },
        render: {
            option: function (item, escape) {
                const docLabel = documentTypeLabels[item.document_type] || item.document_type || '';
                return `<div class="py-1">
                    <div class="font-medium text-gray-900">${escape(item.display_name)}</div>
                    <div class="text-xs text-gray-500">
                        ${escape(docLabel)} ${escape(item.document_number || '')}
                        ${item.phone ? ' · ' + escape(item.phone) : ''}
                        ${item.is_insurance_company ? ' · 🏢 Aseguradora' : ''}
                    </div>
                </div>`;
            },
            item: function (item, escape) {
                return `<div class="flex items-center gap-1">${escape(item.display_name)}</div>`;
            },
        },
    });

    // ===================== Agregar fila =====================
    btnAdd.addEventListener('click', function () {
        const partyId = partySelect.getValue();
        const role = roleSelect.value;
        const notes = notesInput.value.trim();

        if (!partyId) { alert('Seleccione un contacto del buscador.'); return; }
        if (!role) { alert('Seleccione un rol.'); return; }

        const exists = rows.some(r => String(r.party_id) === String(partyId) && r.role === role);
        if (exists) {
            alert('Ese contacto ya existe con ese rol. Elija otro rol o elimine la fila duplicada.');
            return;
        }

        // Datos desde caché o desde las opciones de Tom Select
        const cached = partyCache[partyId]
            || (partySelect.options[partyId]?.data)
            || {};

        rows.push({
            party_id: partyId,
            party_label: cached.display_name || '',
            doc_label: documentTypeLabels[cached.document_type] || cached.document_type || '',
            doc_number: cached.document_number || '',
            role: role,
            is_primary_commercial: chkPrimary.checked,
            notes: notes,
        });

        renderRows();
        emptyMsg.classList.add('hidden');

        // Reset panel
        partySelect.clear();
        roleSelect.value = '';
        chkPrimary.checked = false;
        notesInput.value = '';
    });

    // ===================== Eventos de la lista (delegación) =====================
    container.addEventListener('click', function (e) {
        const removeBtn = e.target.closest('.rel-row-remove');
        if (removeBtn) {
            const index = parseInt(removeBtn.closest('.rel-row').dataset.index, 10);
            rows.splice(index, 1);
            renderRows();
            return;
        }

        const checkbox = e.target.closest('.rel-row-primary');
        if (checkbox) {
            const rowEl = checkbox.closest('.rel-row');
            const index = parseInt(rowEl.dataset.index, 10);
            rows[index].is_primary_commercial = checkbox.checked;
            const hidden = rowEl.querySelector('input[type="hidden"][value="0"]');
            if (hidden) hidden.value = checkbox.checked ? '1' : '0';
        }
    });

    container.addEventListener('change', function (e) {
        const rowEl = e.target.closest('.rel-row');
        if (!rowEl) return;
        const index = parseInt(rowEl.dataset.index, 10);

        if (e.target.classList.contains('rel-row-role')) {
            rows[index].role = e.target.value;
        }
    });

    container.addEventListener('input', function (e) {
        const rowEl = e.target.closest('.rel-row');
        if (!rowEl) return;
        const index = parseInt(rowEl.dataset.index, 10);

        if (e.target.classList.contains('rel-row-notes')) {
            rows[index].notes = e.target.value;
        }
    });

    // ===================== Modal: crear party =====================
    function openModal() {
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        document.getElementById('rel-new-doc-number').focus();
    }

    function resetModal() {
        newDocType.value = '1';
        newDocNumber.value = '';
        newFirstName.value = '';
        newLastName.value = '';
        newBusinessName.value = '';
        newEmail.value = '';
        newPhone.value = '';
        newMobile.value = '';
        togglePersonCompany();
    }

    function closeModal() {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        resetModal();
    }

    function togglePersonCompany() {
        const isCompany = newDocType.value === '6';
        personFields.classList.toggle('hidden', isCompany);
        companyFields.classList.toggle('hidden', !isCompany);
    }

    btnNew.addEventListener('click', function () {
        resetModal();
        openModal();
    });

    btnNewCancel.addEventListener('click', closeModal);
    modal.addEventListener('click', function (e) {
        if (e.target === modal) closeModal();
    });
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && !modal.classList.contains('hidden')) closeModal();
    });

    newDocType.addEventListener('change', togglePersonCompany);

    btnNewSave.addEventListener('click', async function () {
        const isCompany = newDocType.value === '6';
        const documentNumber = newDocNumber.value.trim();

        if (!documentNumber) { alert('Ingrese el número de documento.'); return; }
        if (isCompany && !newBusinessName.value.trim()) { alert('Ingrese la razón social.'); return; }
        if (!isCompany && !newFirstName.value.trim() && !newLastName.value.trim()) {
            alert('Ingrese nombres o apellidos.'); return;
        }

        const payload = {
            document_type: newDocType.value,
            document_number: documentNumber,
            first_name: isCompany ? null : newFirstName.value.trim(),
            last_name: isCompany ? null : newLastName.value.trim(),
            business_name: isCompany ? newBusinessName.value.trim() : null,
            email: newEmail.value.trim() || null,
            phone: newPhone.value.trim() || null,
            mobile: newMobile.value.trim() || null,
        };

        btnNewSave.disabled = true;
        btnNewSave.textContent = 'Guardando...';

        try {
            const res = await fetch('/api/parties/quick-store', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                },
                body: JSON.stringify(payload),
            });

            const data = await res.json().catch(() => ({}));

            if (!res.ok) {
                const msg = data.errors
                    ? Object.values(data.errors).flat().join(' ')
                    : (data.message || 'No se pudo guardar el contacto.');
                alert(msg);
                return;
            }

            // Guardar en caché para el buscador
            partyCache[data.id] = {
                ...data,
                display_name: data.display_name,
                document_type: data.document_type,
                document_number: data.document_number,
            };
            partySelect.addOption({
                id: data.id,
                display_name: data.display_name,
                document_type: data.document_type,
                document_number: data.document_number,
            });

            const role = roleSelect.value;
            if (role) {
                // Agregar directo con el rol seleccionado
                const notes = notesInput.value.trim();
                rows.push({
                    party_id: data.id,
                    party_label: data.display_name,
                    doc_label: documentTypeLabels[data.document_type] || data.document_type,
                    doc_number: data.document_number,
                    role: role,
                    is_primary_commercial: chkPrimary.checked,
                    notes: notes,
                });
                renderRows();
                emptyMsg.classList.add('hidden');
                partySelect.clear();
                roleSelect.value = '';
                chkPrimary.checked = false;
                notesInput.value = '';
            } else {
                // Dejar seleccionado en el buscador para que el usuario elija rol y "Agregar"
                partySelect.setValue(data.id);
            }

            closeModal();
        } catch (e) {
            console.error('Error al guardar party:', e);
            alert('Error de conexión al guardar el contacto.');
        } finally {
            btnNewSave.disabled = false;
            btnNewSave.textContent = 'Guardar y agregar';
        }
    });

    // ===================== Init =====================
    renderRows();
    fillMissingPartyLabels();
})();
</script>
@endpush