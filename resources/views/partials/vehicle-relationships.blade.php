{{-- =============================================================
     Componente reutilizable: Contactos del vehículo (lista de filas)
     - Busca/registra/edita contactos vía partials/contact-modal.
       IMPORTANTE: este parcial NO incluye el modal; la página que lo
       use debe incluir @include('partials.contact-modal') una sola vez.
     - Emite hidden inputs `{vrSubmitName}[i][...]` para el submit.
     - Params:
       * vrPrefix         (string, default 'rel') prefijo de IDs
       * vrSubmitName     (string, default 'relationships') nombre del array
       * vrRoles          (array) roles visibles. Default: todos.
       * vrShowPrimary    (bool, default true) mostrar checkbox contacto principal
       * vrInitialRows    (collection|array) filas iniciales
       * vrTitle          (string) título
       * vrDescription    (string) subtítulo
     - Expone window.VehicleRelationships[vrPrefix]:
         addRow(party, meta), setRows(rows), getRows(), onRowsChanged(cb)
     ============================================================= --}}

@php
    $vrPrefix = $vrPrefix ?? 'rel';
    $vrSubmitName = $vrSubmitName ?? 'relationships';
    $vrAllRoleLabels = \App\Models\VehicleRelationship::roleLabels();
    $vrRoles = $vrRoles ?? array_keys($vrAllRoleLabels);
    $vrRoleLabels = array_intersect_key($vrAllRoleLabels, array_flip($vrRoles));
    $vrShowPrimary = $vrShowPrimary ?? true;
    $vrInitialRows = $vrInitialRows ?? collect();
    $vrTitle = $vrTitle ?? 'Contactos del vehículo';
    $vrDescription = $vrDescription ?? 'Busca un contacto existente o registra uno nuevo. Los campos cambian según el rol.';
    if ($vrInitialRows instanceof \Illuminate\Support\Collection) {
        $vrInitialRows = $vrInitialRows->values();
    }
@endphp

<div class="mt-4 border-t border-gray-200 pt-6">
    <div class="flex flex-wrap items-center justify-between gap-3 mb-4">
        <div>
            <h3 class="text-lg font-semibold text-gray-800">{{ $vrTitle }}</h3>
            <p class="text-sm text-gray-500 mt-1">{{ $vrDescription }}</p>
        </div>
        <button type="button" id="{{ $vrPrefix }}-btn-add"
                class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
            + Agregar contacto
        </button>
    </div>

    <div id="{{ $vrPrefix }}-container" class="mt-4"></div>

    <p id="{{ $vrPrefix }}-empty" class="mt-4 text-sm text-gray-500 @if ($vrInitialRows->count() > 0) hidden @endif">
        Sin contactos asociados. Haz clic en "＋ Agregar contacto" para buscar o registrar uno.
    </p>
</div>

{{-- Datos para el JS (evita JSON dentro de <script>) --}}
<div id="{{ $vrPrefix }}-data" class="hidden" data-roles='@json($vrRoleLabels)' data-rows='@json($vrInitialRows)'></div>

@push('scripts')
<script>
(function () {
    'use strict';

    const prefix = {{ Js::from($vrPrefix) }};
    const submitName = {{ Js::from($vrSubmitName) }};
    const showPrimary = {{ $vrShowPrimary ? 'true' : 'false' }};

    const dataEl = document.getElementById(prefix + '-data');
    const roleLabels = JSON.parse(dataEl.dataset.roles || '{}');
    const initialRows = JSON.parse(dataEl.dataset.rows || '[]');
    const documentTypeLabels = { '1': 'DNI', '6': 'RUC', '4': 'CEX', '7': 'PAS', 'A': 'Cédula Diplomática' };

    const container = document.getElementById(prefix + '-container');
    const emptyMsg = document.getElementById(prefix + '-empty');
    const btnAdd = document.getElementById(prefix + '-btn-add');

    let rows = initialRows.map(r => ({ ...r }));
    const changeCallbacks = [];

    function escapeHtml(str) {
        if (str === null || str === undefined) return '';
        return String(str).replace(/[&<>"']/g, m => ({ '&': '&', '<': '<', '>': '>', '"': '"', "'": '&#039;' }[m]));
    }

    function roleLabel(role) { return roleLabels[role] || role || ''; }

    function rowTemplate(data, index) {
        const principal = data.is_primary_commercial
            ? '<span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">⭐ Contacto Principal</span>'
            : '';
        const roleColor = data.role === 'owner' ? 'bg-blue-100 text-blue-800'
            : data.role === 'driver' ? 'bg-green-100 text-green-800'
            : data.role === 'approver' ? 'bg-yellow-100 text-yellow-800'
            : data.role === 'operator' ? 'bg-purple-100 text-purple-800'
            : data.role === 'billing' ? 'bg-red-100 text-red-800'
            : data.role === 'insurance_company' ? 'bg-indigo-100 text-indigo-800'
            : data.role === 'emergency_contact' ? 'bg-orange-100 text-orange-800'
            : 'bg-gray-100 text-gray-700';

        return `
        <div class="rel-row bg-white border border-gray-200 rounded-lg px-2 py-1.5 mb-2 grid grid-cols-1 md:grid-cols-12 gap-2 items-center" data-index="${index}">
            <div class="md:col-span-3 min-w-0">
                <input type="hidden" name="${submitName}[${index}][party_id]" value="${escapeHtml(data.party_id)}">
                <input type="hidden" name="${submitName}[${index}][role]" value="${escapeHtml(data.role || '')}">
                <input type="hidden" name="${submitName}[${index}][is_primary_commercial]" value="${data.is_primary_commercial ? 1 : 0}">
                <input type="hidden" name="${submitName}[${index}][notes]" value="${escapeHtml(data.notes || '')}">
                <p class="font-medium text-gray-900 leading-tight">${escapeHtml(data.party_label || 'Contacto #' + (index + 1))}</p>
                <p class="text-xs text-gray-500">${escapeHtml(data.doc_label || '')}${data.doc_number ? ' ' + escapeHtml(data.doc_number) : ''}</p>
            </div>
            <div class="md:col-span-2 flex-wrap items-center gap-1">
                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium ${roleColor}">${escapeHtml(roleLabel(data.role))}</span>
                ${principal}
            </div>
            <div class="md:col-span-2 text-xs text-gray-600 truncate">${escapeHtml(data.notes || '-')}</div>
            <div class="md:col-span-1 text-xs text-gray-600 truncate">${escapeHtml(data.party_phone || '-')}</div>
            <div class="md:col-span-1 text-xs text-gray-600 truncate">${escapeHtml(data.party_mobile || '-')}</div>
            <div class="md:col-span-2 text-xs text-gray-600 truncate">${escapeHtml(data.party_email || '-')}</div>
            <div class="md:col-span-1 flex items-center justify-end gap-1">
                <button type="button" class="rel-row-edit text-blue-600 hover:text-blue-800 text-sm leading-none" title="Editar contacto">✏️</button>
                <button type="button" class="rel-row-remove text-red-600 hover:text-red-800 text-sm leading-none" title="Quitar del vehículo">🗑️</button>
            </div>
        </div>`;
    }

    function renderRows() {
        const header = rows.length > 0 ? `
            <div class="hidden md:grid grid-cols-12 gap-2 px-2 pb-2 text-xs font-medium text-gray-500 uppercase">
                <div class="md:col-span-3">Contacto</div>
                <div class="md:col-span-2">Rol</div>
                <div class="md:col-span-2">Notas</div>
                <div class="md:col-span-1">Teléfono</div>
                <div class="md:col-span-1">Celular</div>
                <div class="md:col-span-2">Email</div>
                <div class="md:col-span-1 text-right">Acciones</div>
            </div>` : '';
        container.innerHTML = header + rows.map((r, i) => rowTemplate(r, i)).join('');
        emptyMsg.classList.toggle('hidden', rows.length > 0);
        notifyChange();
    }

    function notifyChange() {
        changeCallbacks.forEach(cb => { try { cb(rows.map(r => ({ ...r }))); } catch (e) { console.error(e); } });
    }

    function rolesAllowed() { return Object.keys(roleLabels); }

    btnAdd.addEventListener('click', function () {
        window.ContactModal.open({
            roles: rolesAllowed(),
            roleLabels: roleLabels,
            showPrimary: showPrimary,
            onSelect: function (party, meta) {
                addRow(party, meta);
            },
        });
    });

    function partyToRowData(party, meta, role) {
        const isPrimary = !!meta.is_primary_commercial;
        return {
            party_id: party.id,
            party_label: party.display_name || party.business_name || party.first_name || party.last_name || '',
            doc_label: documentTypeLabels[party.document_type] || party.document_type || '',
            doc_number: party.document_number || '',
            party_mobile: party.mobile || party.phone || '',
            party_phone: party.phone || '',
            party_email: party.email || '',
            role: role,
            is_primary_commercial: isPrimary,
            notes: meta.notes || '',
        };
    }

    function addRow(party, meta) {
        const role = meta.role;
        if (!roleLabels.hasOwnProperty(role)) return;

        if (rows.some(r => String(r.party_id) === String(party.id) && r.role === role)) {
            alert('Ese contacto ya existe con ese rol en el vehículo.');
            return;
        }
        if (role === 'owner' && rows.some(r => r.role === 'owner')) {
            alert('Este vehículo ya tiene un propietario. Solo puede haber uno.');
            return;
        }
        if (meta.is_primary_commercial) {
            rows.forEach(r => r.is_primary_commercial = false);
        }

        rows.push(partyToRowData(party, meta, role));
        renderRows();
        emptyMsg.classList.add('hidden');
    }

    function updateRow(index, party, meta) {
        const role = meta.role;
        if (role === 'owner' && rows.some((r, i) => r.role === 'owner' && i !== index)) {
            alert('Este vehículo ya tiene un propietario. Solo puede haber uno.');
            return;
        }
        if (meta.is_primary_commercial) {
            rows.forEach((r, i) => { if (i !== index) r.is_primary_commercial = false; });
        }

        rows[index] = {
            party_id: party.id || rows[index].party_id,
            party_label: party.display_name || party.business_name || rows[index].party_label,
            doc_label: documentTypeLabels[party.document_type] || party.document_type || rows[index].doc_label,
            doc_number: party.document_number || rows[index].doc_number,
            party_mobile: party.mobile || party.phone || rows[index].party_mobile,
            party_phone: party.phone || rows[index].party_phone,
            party_email: party.email || rows[index].party_email,
            role: role,
            is_primary_commercial: !!meta.is_primary_commercial,
            notes: meta.notes || '',
        };
        renderRows();
    }

    function setRows(newRows) {
        rows = (newRows || []).map(r => ({ ...r }));
        renderRows();
    }

    function getRows() {
        return rows.map(r => ({ ...r }));
    }

    container.addEventListener('click', function (e) {
        const removeBtn = e.target.closest('.rel-row-remove');
        if (removeBtn) {
            const index = parseInt(removeBtn.closest('.rel-row').dataset.index, 10);
            rows.splice(index, 1);
            renderRows();
            return;
        }
        const editBtn = e.target.closest('.rel-row-edit');
        if (editBtn) {
            const index = parseInt(editBtn.closest('.rel-row').dataset.index, 10);
            const row = rows[index];
            if (!row) return;
            fetch(`/api/parties/search?id=${encodeURIComponent(row.party_id)}`)
                .then(r => r.json())
                .then(res => {
                    const party = res[0];
                    if (!party) return;
                    window.ContactModal.open({
                        roles: rolesAllowed(),
                        roleLabels: roleLabels,
                        showPrimary: showPrimary,
                        initialParty: party,
                        initialRole: row.role,
                        initialPrimary: row.is_primary_commercial,
                        initialNotes: row.notes,
                        onSelect: function (partyUpdated, meta) {
                            updateRow(index, partyUpdated, meta);
                        },
                    });
                });
        }
    });

    window.VehicleRelationships = window.VehicleRelationships || {};
    window.VehicleRelationships[prefix] = {
        addRow: addRow,
        setRows: setRows,
        getRows: getRows,
        onRowsChanged: function (cb) { changeCallbacks.push(cb); },
    };

    renderRows();
})();
</script>
@endpush
