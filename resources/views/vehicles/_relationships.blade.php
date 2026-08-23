{{-- =============================================================
     Contactos del vehículo (VehicleRelationship)
     - Lista de filas con hidden inputs para el submit del formulario.
     - Un botón abre el modal compartido (partials/contact-modal).
     - El callback agrega la fila y gestiona unicidad:
       * 1 solo propietario (owner)
       * 1 solo contacto comercial principal (deselecciona los demás)
     ============================================================= --}}

@php
    $relationshipRoleLabels = \App\Models\VehicleRelationship::roleLabels();

    if (old('relationships')) {
        $relationshipInitialRows = collect(old('relationships'))->map(function ($rel) {
            return [
                'party_id' => $rel['party_id'] ?? null,
                'party_label' => null,
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
                    'party_phone' => $rel->party?->phone,
                    'party_mobile' => $rel->party?->mobile,
                    'party_email' => $rel->party?->email,
                ];
            })->values()
            : collect();
    }
@endphp

<div class="mt-8 border-t border-gray-200 pt-6">
    <div class="flex flex-wrap items-center justify-between gap-3 mb-4">
        <div>
            <h3 class="text-lg font-semibold text-gray-800">Contactos del vehículo</h3>
            <p class="text-sm text-gray-500 mt-1">Busca un contacto existente o registra uno nuevo. Los campos cambian según el rol.</p>
        </div>
        <button type="button" id="rel-btn-add"
                class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
            + Agregar contacto
        </button>
    </div>

    <div id="rel-container" class="mt-4"></div>

    <p id="rel-empty" class="mt-4 text-sm text-gray-500 @if ($relationshipInitialRows->count() > 0) hidden @endif">
        Sin contactos asociados. Haz clic en "＋ Agregar contacto" para buscar o registrar uno.
    </p>
</div>

{{-- Modal reutilizable de contacto (compartido con check-ins) --}}
@include('partials.contact-modal')

{{-- Datos para el JS (evita JSON dentro de <script>) --}}
<div id="rel-data" class="hidden" data-roles='@json($relationshipRoleLabels)' data-rows='@json($relationshipInitialRows)'></div>

@push('scripts')
<script>
(function () {
    'use strict';

    const relData = document.getElementById('rel-data');
    const roleLabels = JSON.parse(relData.dataset.roles);
    const initialRows = JSON.parse(relData.dataset.rows);
    const documentTypeLabels = { '1': 'DNI', '6': 'RUC', '4': 'CEX', '7': 'PAS', 'A': 'Cédula Diplomática' };

    const container = document.getElementById('rel-container');
    const emptyMsg = document.getElementById('rel-empty');
    const btnAdd = document.getElementById('rel-btn-add');

    let rows = initialRows.map(r => ({ ...r }));

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
                <input type="hidden" name="relationships[${index}][party_id]" value="${escapeHtml(data.party_id)}">
                <input type="hidden" name="relationships[${index}][role]" value="${escapeHtml(data.role || '')}">
                <input type="hidden" name="relationships[${index}][is_primary_commercial]" value="${data.is_primary_commercial ? 1 : 0}">
                <input type="hidden" name="relationships[${index}][notes]" value="${escapeHtml(data.notes || '')}">
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
    }

    btnAdd.addEventListener('click', function () {
        window.ContactModal.open({
            roles: Object.keys(roleLabels),
            roleLabels: roleLabels,
            showPrimary: true,
            onSelect: function (party, meta) {
                addRelationshipRow(party, meta);
            },
        });
    });

    function addRelationshipRow(party, meta) {
        const role = meta.role;
        const isPrimary = !!meta.is_primary_commercial;

        if (rows.some(r => String(r.party_id) === String(party.id) && r.role === role)) {
            alert('Ese contacto ya existe con ese rol en el vehículo.');
            return;
        }
        if (role === 'owner' && rows.some(r => r.role === 'owner')) {
            alert('Este vehículo ya tiene un propietario. Solo puede haber uno.');
            return;
        }
        if (isPrimary) {
            rows.forEach(r => r.is_primary_commercial = false);
        }

        rows.push({
            party_id: party.id,
            party_label: party.display_name || party.business_name || '',
            doc_label: documentTypeLabels[party.document_type] || party.document_type || '',
            doc_number: party.document_number || '',
            party_mobile: party.mobile || party.phone || '',
            party_phone: party.phone || '',
            party_email: party.email || '',
            role: role,
            is_primary_commercial: isPrimary,
            notes: meta.notes || '',
        });
        renderRows();
        emptyMsg.classList.add('hidden');
    }

    container.addEventListener('click', function (e) {
        const removeBtn = e.target.closest('.rel-row-remove');
        if (!removeBtn) return;
        const index = parseInt(removeBtn.closest('.rel-row').dataset.index, 10);
        rows.splice(index, 1);
        renderRows();
    });

    renderRows();
})();
</script>
@endpush