@php
    $isEdit = isset($estimate) && $estimate !== null;
    $initialCheckInId = $checkIn->id ?? null;
    $initialVehicleId = old('vehicle_id', $estimate->vehicle_id ?? '');
    $initialClientId = old('client_id', $estimate->client_id ?? '');
    $initialInsuranceId = old('insurance_company_id', $estimate->insurance_company_id ?? '');
    $igvRate = $establishment->igv_rate ?? 0.18;

    $initialItems = $isEdit
        ? $estimate->items->map(fn ($i) => [
            'id' => $i->id,
            'item_type' => $i->item_type,
            'service_id' => $i->service_id,
            'part_id' => $i->part_id,
            'service_category_id' => $i->service_category_id,
            'part_category_id' => $i->part_category_id,
            'description' => $i->description,
            'quantity' => (float) $i->quantity,
            'unit_price' => (float) $i->unit_price,
            'discount_pct' => (float) $i->discount_pct,
            'supply_source' => $i->supply_source ?? 'internal',
            'cost_price' => (float) $i->cost_price,
        ])->values()
        : collect();
@endphp

@push('scripts')
<script>
(function () {
    'use strict';

    const igvRate = {{ (float) $igvRate }};
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
    const initialCheckInId = {{ $initialCheckInId ? (int) $initialCheckInId : 'null' }};
    const initialClientId = {{ $initialClientId ? (int) $initialClientId : 'null' }};
    const initialInsuranceId = {{ $initialInsuranceId ? (int) $initialInsuranceId : 'null' }};
    const initialServiceType = "{{ old('service_type', $estimate->service_type ?? '') }}";
    const initialItems = @json($initialItems);

    const serviceCategories = @json($serviceCategories->map(fn ($c) => ['id' => $c->id, 'name' => $c->name])->values());
    const partCategories = @json($partCategories->map(fn ($c) => ['id' => $c->id, 'name' => $c->name])->values());
    const supplyLabels = { internal: 'Interno', external: 'Externo', insurance: 'Seguro' };

    // =====================================================
    // Helpers
    // =====================================================
    function round2(n) { return Math.round((Number(n) + Number.EPSILON) * 100) / 100; }
    function money(n) { return round2(n || 0).toFixed(2); }
    function num(v) { const n = parseFloat(v); return isNaN(n) ? 0 : n; }
    function escapeHtml(str) {
        if (str === null || str === undefined) return '';
        return String(str).replace(/[&<>"']/g, m => ({ '&': '&', '<': '<', '>': '>', '"': '"', "'": '&#039;' }[m]));
    }
    function catName(list, id) {
        const c = list.find(x => String(x.id) === String(id));
        return c ? escapeHtml(c.name) : '-';
    }

    // =====================================================
    // Tom Select: helper genérico (selección única estricta)
    // =====================================================
    function singleSelect(el, options) {
        const ts = new TomSelect(el, Object.assign({
            valueField: 'id',
            labelField: 'label',
            searchField: ['label', 'sub'],
            create: false,
            maxOptions: 30,
            closeAfterSelect: true,
            maxItems: 1,
            copyClassesToDropdown: false,
            allowEmptyOption: true,
            dropdownParent: 'body',
        }, options));

        ts.on('item_add', function () { ts.blur(); ts.close(); });
        ts.on('dropdown_open', function () {
            if (ts.items.length > 0) { ts.setTextValue(''); ts.input && ts.input.setSelectionRange(0, 0); }
        });
        return ts;
    }

    // =====================================================
    // Cabecera: vehículo, destinatario, aseguradora
    // =====================================================
    const vehicleSelect = singleSelect(document.getElementById('vehicle_id'), {
        placeholder: 'Buscar por placa, marca o modelo...',
        load: function (query, callback) {
            fetch(`/api/vehicles/search?q=${encodeURIComponent(query)}&limit=20`)
                .then(r => r.json())
                .then(data => callback(data.map(v => ({
                    id: v.id, label: v.plate || v.id,
                    sub: [v.brand, v.model, v.year].filter(Boolean).join(' '),
                }))))
                .catch(() => callback());
        },
        render: {
            option: function (item, escape) {
                return `<div class="py-1"><div class="font-medium text-gray-900">${escape(item.label)}</div><div class="text-xs text-gray-500">${escape(item.sub || '')}</div></div>`;
            },
            item: function (item, escape) { return `<div class="font-medium">${escape(item.label)}</div>`; },
        },
    });

    // Destinatario: ahora es un hidden input que se rellena desde el rol elegido.
    const clientInput = document.getElementById('client_id');

    const insuranceSelect = singleSelect(document.getElementById('insurance_company_id'), {
        placeholder: 'Seleccionar aseguradora...',
        load: function (query, callback) {
            fetch(`/api/parties/search?is_insurance_company=1&q=${encodeURIComponent(query)}&limit=20`)
                .then(r => r.json())
                .then(data => callback(data.map(p => ({
                    id: p.id, label: p.business_name || p.display_name, sub: p.document_number,
                    hourly_rate: p.insurance_hourly_rate, panel_rate: p.insurance_panel_rate,
                }))))
                .catch(() => callback());
        },
    });

    insuranceSelect.on('change', function () {
        const value = this.getValue();
        if (!value) return;
        const option = this.options[value];
        const hourlyEl = document.getElementById('hourly_rate');
        const panelEl = document.getElementById('panel_rate');
        if (option && option.hourly_rate > 0 && (!hourlyEl.value || num(hourlyEl.value) === 0)) hourlyEl.value = option.hourly_rate;
        if (option && option.panel_rate > 0 && (!panelEl.value || num(panelEl.value) === 0)) panelEl.value = option.panel_rate;
    });

    // Carga la aseguradora del vehículo (relación rol insurance_company) en el
    // selector de aseguradora. Se usa al seleccionar la placa.
    function setInsuranceByPartyId(partyId) {
        if (!partyId) return;
        // En edición el presupuesto conserva su aseguradora guardada: no pisarla
        // con la asociada al vehículo.
        if (initialInsuranceId) return;
        fetch(`/api/parties/search?id=${encodeURIComponent(partyId)}`)
            .then(r => r.json())
            .then(data => {
                if (!data[0] || !data[0].is_insurance_company) return;
                const p = data[0];
                insuranceSelect.addOption({
                    id: p.id,
                    label: p.business_name || p.display_name,
                    sub: p.document_number,
                    hourly_rate: p.insurance_hourly_rate,
                    panel_rate: p.insurance_panel_rate,
                });
                insuranceSelect.setValue(p.id, true);
                // setValue silencioso no dispara 'change' → aplicar tarifas aquí.
                const hourlyEl = document.getElementById('hourly_rate');
                const panelEl = document.getElementById('panel_rate');
                if (p.insurance_hourly_rate > 0 && (!hourlyEl.value || num(hourlyEl.value) === 0)) hourlyEl.value = p.insurance_hourly_rate;
                if (p.insurance_panel_rate > 0 && (!panelEl.value || num(panelEl.value) === 0)) panelEl.value = p.insurance_panel_rate;
            }).catch(() => {});
    }

    // Rellena el selector de aseguradora con todas las compañías registradas.
    // Se ejecuta al seleccionar la placa para tener el listado completo disponible.
    function loadInsuranceCompanies() {
        return fetch('/api/parties/search?is_insurance_company=1&limit=100')
            .then(r => r.json())
            .then(data => {
                // Preservar la selección previa (p. ej. aseguradora ya elegida o guardada)
                const previous = insuranceSelect.getValue();
                insuranceSelect.clearOptions();
                (data || []).forEach(p => insuranceSelect.addOption({
                    id: p.id,
                    label: p.business_name || p.display_name,
                    sub: p.document_number,
                    hourly_rate: p.insurance_hourly_rate,
                    panel_rate: p.insurance_panel_rate,
                }));
                if (previous) insuranceSelect.setValue(previous, true);
                return data;
            })
            .catch(() => []);
    }

    // =====================================================
    // Contactos del vehículo (miniselector rol → destinatario)
    // =====================================================
    const recipientRoleSelect = document.getElementById('recipient_role');
    let vehicleContacts = []; // [{id, role, role_label, party_id, contact_name, contact_phone, contact_email, party}]
    let currentVehicleId = null;

    const roleLabels = @json(\App\Models\VehicleRelationship::roleLabels());

    function setContactField(id, value) {
        const el = document.getElementById(id);
        if (el) el.value = value || '';
    }

    function clearRecipient() {
        clientInput.value = '';
        setContactField('contact_name', '');
        setContactField('contact_phone', '');
        setContactField('contact_email', '');
    }

    function applyContact(contact) {
        if (!contact || !contact.party) return;
        clientInput.value = contact.party.id || '';
        setContactField('contact_name', contact.contact_name);
        setContactField('contact_phone', contact.contact_phone);
        setContactField('contact_email', contact.contact_email);
    }

    function fillRoleSelect() {
        recipientRoleSelect.innerHTML = '<option value="">— Seleccionar rol —</option>';
        if (vehicleContacts.length === 0) {
            const opt = document.createElement('option');
            opt.value = '';
            opt.textContent = 'Sin contactos — agrega con +';
            recipientRoleSelect.add(opt);
            return;
        }
        vehicleContacts.forEach(c => {
            const opt = document.createElement('option');
            opt.value = c.role;
            opt.textContent = `${c.role_label} — ${c.contact_name || 'Sin nombre'}`;
            recipientRoleSelect.add(opt);
        });
        if (!recipientRoleSelect.value) {
            recipientRoleSelect.value = vehicleContacts[0].role;
        }
    }

    function loadVehicleContacts(vehicleId, preferred, opts) {
        currentVehicleId = vehicleId;
        vehicleContacts = [];
        recipientRoleSelect.innerHTML = '<option value="">— Seleccionar rol —</option>';

        if (!vehicleId) return;

        fetch(`/api/vehicles/${encodeURIComponent(vehicleId)}/recipients`)
            .then(r => r.json())
            .then(data => {
                vehicleContacts = Array.isArray(data) ? data : [];
                fillRoleSelect();

                // Prioridad: 1º contacto recién guardado (preferred: party_id + role),
                // 2º contacto del presupuesto en edición (client_id), 3º aprobador → propietario.
                const preferredContact = preferred?.partyId
                    ? vehicleContacts.find(c => c.party_id && String(c.party_id) === String(preferred.partyId)
                        && (!preferred.role || c.role === preferred.role))
                    : null;
                const savedContact = preferredContact
                    ?? (initialClientId
                        ? vehicleContacts.find(c => c.party_id && String(c.party_id) === String(initialClientId))
                        : null);
                const defaultContact = savedContact
                    ?? vehicleContacts.find(c => c.role === 'approver')
                    ?? vehicleContacts.find(c => c.role === 'owner')
                    ?? vehicleContacts[0];
                // Si el presupuesto tiene un cliente guardado que NO existe entre los
                // contactos del vehículo, no sobrescribir client_id (se preserva el original).
                const preserveSavedClient = initialClientId && !savedContact;
                if (defaultContact && !preserveSavedClient) {
                    recipientRoleSelect.value = defaultContact.role;
                    applyContact(defaultContact);
                }

                // Cargar la aseguradora asociada al vehículo (rol insurance_company):
                // 1º rellenar el listado completo de aseguradoras, 2º seleccionar la
                // compañía relacionada del vehículo si aplica.
                const insuranceContact = vehicleContacts.find(c => c.role === 'insurance_company');
                loadInsuranceCompanies().then(() => {
                    // En edición, restaurar la aseguradora guardada del presupuesto
                    // (ya incluida en el listado recién cargado).
                    if (initialInsuranceId && insuranceSelect.options[initialInsuranceId]) {
                        insuranceSelect.setValue(initialInsuranceId, true);
                        return;
                    }
                    if (insuranceContact?.party_id && (opts?.forceInsurance || !insuranceSelect.getValue())) {
                        setInsuranceByPartyId(insuranceContact.party_id);
                    }
                });
            }).catch(() => {});
    }

    recipientRoleSelect.addEventListener('change', function () {
        const contact = vehicleContacts.find(c => c.role === this.value);
        if (contact) {
            applyContact(contact);
        } else {
            clearRecipient();
        }
    });

    vehicleSelect.on('change', function () {
        const value = this.getValue();
        clearRecipient();
        loadVehicleContacts(value, null, { forceInsurance: true });
    });

    // =====================================================
    // Agregar / editar contacto del vehículo (ContactModal)
    // =====================================================
    function openContactModal(editContact) {
        if (!currentVehicleId) {
            alert('Seleccione primero un vehículo.');
            return;
        }
        const availableRoles = Object.keys(roleLabels);
        const config = {
            roles: availableRoles,
            roleLabels: roleLabels,
            showPrimary: false,
            onSelect: function (party, meta) {
                // Asocia la Party al vehículo con el rol elegido
                fetch(`/api/vehicles/${currentVehicleId}/relationships`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                    body: JSON.stringify({
                        relationship_id: editContact ? editContact.id : null,
                        party_id: party.id,
                        role: meta.role || 'other',
                        is_primary_commercial: !!meta.is_primary_commercial,
                        notes: meta.notes || null,
                    }),
                })
                .then(r => r.json())
                .then(() => {
                    // Recargar seleccionando el contacto recién guardado/actualizado.
                    loadVehicleContacts(currentVehicleId, { partyId: party.id, role: meta.role || null }, { forceInsurance: meta.role === 'insurance_company' });
                })
                .catch(() => alert('No se pudo asociar el contacto al vehículo.'));
            },
        };

        if (editContact) {
            config.initialRole = editContact.role;
            if (editContact.party_id) {
                fetch(`/api/parties/search?id=${encodeURIComponent(editContact.party_id)}`)
                    .then(r => r.json())
                    .then(data => {
                        if (data[0]) {
                            config.initialParty = data[0];
                            window.ContactModal.open(config);
                        }
                    }).catch(() => {});
                return;
            }
        }
        window.ContactModal.open(config);
    }

    document.getElementById('btn-recipient-add').addEventListener('click', function () {
        openContactModal(null);
    });

    document.getElementById('btn-recipient-edit').addEventListener('click', function () {
        const contact = vehicleContacts.find(c => c.role === recipientRoleSelect.value);
        if (!contact || !contact.party_id) {
            // Si no hay contacto seleccionado, permitir agregar directamente
            openContactModal(null);
            return;
        }
        openContactModal(contact);
    });

    // =====================================================
    // Ítems: estado en JS + tabla resumen
    // =====================================================
    let items = initialItems.map(i => ({ ...i }));
    const itemsBody = document.getElementById('items-body');
    let editingIndex = null; // índice en `items` o null para nuevo

    function typeBadge(type) {
        if (type === 'part') return '<span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-medium bg-blue-100 text-blue-800">Repuesto</span>';
        return '<span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-medium bg-green-100 text-green-800">Servicio</span>';
    }

    function rowTotals(item) {
        const subtotal = round2(qty(item) * price(item));
        const discount = round2(subtotal * (pct(item) / 100));
        const net = round2(subtotal - discount);
        const iva = round2(net * igvRate);
        return { subtotal, discount, net, iva, total: round2(net + iva) };
    }
    function qty(i) { return num(i.quantity); }
    function price(i) { return num(i.unit_price); }
    function pct(i) { return Math.min(100, Math.max(0, num(i.discount_pct))); }

    function hiddenInputs(item, index) {
        const escape = escapeHtml;
        return `
            <input type="hidden" name="items[${index}][id]" value="${escape(item.id || '')}">
            <input type="hidden" name="items[${index}][service_id]" value="${escape(item.service_id || '')}">
            <input type="hidden" name="items[${index}][part_id]" value="${escape(item.part_id || '')}">
            <input type="hidden" name="items[${index}][item_type]" value="${escape(item.item_type || 'service')}">
            <input type="hidden" name="items[${index}][service_category_id]" value="${escape(item.service_category_id || '')}">
            <input type="hidden" name="items[${index}][part_category_id]" value="${escape(item.part_category_id || '')}">
            <input type="hidden" name="items[${index}][description]" value="${escape(item.description || '')}">
            <input type="hidden" name="items[${index}][quantity]" value="${item.quantity}">
            <input type="hidden" name="items[${index}][unit_price]" value="${item.unit_price}">
            <input type="hidden" name="items[${index}][discount_pct]" value="${item.discount_pct}">
            <input type="hidden" name="items[${index}][supply_source]" value="${escape(item.supply_source || 'internal')}">
            <input type="hidden" name="items[${index}][cost_price]" value="${item.cost_price || 0}">
        `;
    }

    function renderItems() {
        itemsBody.innerHTML = items.map((item, index) => {
            const t = rowTotals(item);
            const catCell = item.item_type === 'part'
                ? catName(partCategories, item.part_category_id)
                : catName(serviceCategories, item.service_category_id);

            return `
            <tr>
                <td class="px-3 py-2">${typeBadge(item.item_type)}</td>
                <td class="px-3 py-2 font-medium text-gray-900">${escapeHtml(item.description || '')}</td>
                <td class="px-3 py-2 text-gray-600">${catCell}</td>
                <td class="px-3 py-2 text-right text-gray-700">${money(qty(item))}</td>
                <td class="px-3 py-2 text-right text-gray-700">${money(price(item))}</td>
                <td class="px-3 py-2 text-right text-gray-700">${money(pct(item))}%</td>
                <td class="px-3 py-2 text-right font-semibold text-gray-900">${money(t.total)}</td>
                <td class="px-3 py-2 text-center">
                    <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-medium bg-gray-100 text-gray-700">${supplyLabels[item.supply_source] || 'Interno'}</span>
                </td>
                <td class="px-3 py-2">
                    <div class="flex items-center justify-end gap-1">
                        <button type="button" class="btn-icon btn-icon-blue item-edit" data-index="${index}" title="Editar ítem">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        </button>
                        <button type="button" class="btn-icon btn-icon-red item-remove" data-index="${index}" title="Quitar ítem">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>
                    ${hiddenInputs(item, index)}
                </td>
            </tr>`;
        }).join('') || '<tr><td colspan="9" class="px-3 py-8 text-center text-sm text-gray-500">Sin ítems. Haz clic en "Agregar ítem".</td></tr>';
    }

    itemsBody.addEventListener('click', function (e) {
        const editBtn = e.target.closest('.item-edit');
        if (editBtn) {
            openItemModal(parseInt(editBtn.dataset.index, 10));
            return;
        }
        const removeBtn = e.target.closest('.item-remove');
        if (removeBtn) {
            const index = parseInt(removeBtn.dataset.index, 10);
            items.splice(index, 1);
            renderItems();
            calcTotals();
        }
    });

    // =====================================================
    // Cálculo de totales
    // =====================================================
    function calcTotals() {
        let subtotalTotal = 0;
        let linesDiscount = 0;

        items.forEach(item => {
            const r = rowTotals(item);
            subtotalTotal += r.subtotal;
            linesDiscount += r.discount;
        });

        // Descuento global sobre el neto tras descuentos por ítem (criterio SUNAT).
        const netAfterLines = Math.max(0, round2(subtotalTotal - linesDiscount));
        const gType = document.getElementById('global_discount_type').value;
        const gValue = num(document.getElementById('global_discount_value').value);
        let globalDiscount = 0;

        if (gType === 'percentage' && gValue > 0) globalDiscount = round2(netAfterLines * (Math.min(100, gValue) / 100));
        else if (gType === 'fixed' && gValue > 0) globalDiscount = round2(gValue);
        globalDiscount = Math.min(globalDiscount, netAfterLines);

        // Franquicia / descuentos adicionales (por ahora sin UI → 0 on create).
        const franchise = 0;

        const totalDiscount = round2(linesDiscount + globalDiscount + franchise);
        const taxableBase = Math.max(0, round2(subtotalTotal - totalDiscount));
        const iva = round2(taxableBase * igvRate);
        const total = round2(taxableBase + iva);

        document.getElementById('total-subtotal').textContent = money(subtotalTotal);
        document.getElementById('total-lines-discount').textContent = '- ' + money(linesDiscount);
        document.getElementById('total-global-discount').textContent = '- ' + money(globalDiscount);
        document.getElementById('total-franchise').textContent = '- ' + money(franchise);
        document.getElementById('total-taxable').textContent = money(taxableBase);
        document.getElementById('igv-rate-label').textContent = Math.round(igvRate * 100);
        document.getElementById('total-iva').textContent = money(iva);
        document.getElementById('total-total').textContent = money(total);
    }

    // =====================================================
    // Modal de ítems
    // =====================================================
    const modal = document.getElementById('item-modal');
    const modalTitle = document.getElementById('item-modal-title');
    const typeSelect = document.getElementById('item-type');
    const catalogServiceWrap = document.getElementById('item-catalog-service');
    const catalogPartWrap = document.getElementById('item-catalog-part');
    const categoryServiceWrap = document.getElementById('item-category-service');
    const categoryPartWrap = document.getElementById('item-category-part');
    const supplyWrap = document.getElementById('item-supply-wrap');

    const serviceCatSelect = document.getElementById('item-service-category');
    const partCatSelect = document.getElementById('item-part-category');
    serviceCatSelect.innerHTML = serviceCategories.map(c => `<option value="${c.id}">${escapeHtml(c.name)}</option>`).join('');
    partCatSelect.innerHTML = partCategories.map(c => `<option value="${c.id}">${escapeHtml(c.name)}</option>`).join('');

    const modalServiceSelect = singleSelect(document.getElementById('item-service-select'), {
        placeholder: 'Buscar servicio...',
        load: function (query, callback) {
            fetch(`/api/repair-services/search?q=${encodeURIComponent(query)}&limit=20`)
                .then(r => r.json())
                .then(data => callback(data.map(s => ({
                    id: s.id, label: s.name, sub: s.category || '',
                    sell_price: s.sell_price, cost_price: s.cost_price,
                    pricing_type: s.pricing_type, estimated_hours: s.estimated_hours,
                    is_outsourced: s.is_outsourced, service_category_id: s.service_category_id,
                }))))
                .catch(() => callback());
        },
        render: {
            option: function (item, escape) {
                return `<div class="py-1"><div class="font-medium text-gray-900">${escape(item.label)}</div><div class="text-xs text-gray-500">${escape(item.sub || '')}</div></div>`;
            },
            item: function (item, escape) { return `<div class="font-medium">${escape(item.label)}</div>`; },
        },
    });
    modalServiceSelect.on('change', function () {
        const v = this.getValue();
        if (!v) return;
        const s = this.options[v];
        document.getElementById('item-description').value = s.label || '';
        document.getElementById('item-unit-price').value = s.sell_price || 0;
        document.getElementById('item-cost-price').value = s.cost_price || 0;
        document.getElementById('item-supply-source').value = s.is_outsourced ? 'external' : 'internal';
        if (s.service_category_id) serviceCatSelect.value = s.service_category_id;
        if (s.pricing_type === 'time_based' && s.estimated_hours) document.getElementById('item-quantity').value = s.estimated_hours;
    });

    const modalPartSelect = singleSelect(document.getElementById('item-part-select'), {
        placeholder: 'Buscar repuesto...',
        load: function (query, callback) {
            fetch(`/api/parts/search?q=${encodeURIComponent(query)}&limit=20`)
                .then(r => r.json())
                .then(data => callback(data.map(p => ({
                    id: p.id, label: p.name, sub: [p.sku, p.brand].filter(Boolean).join(' '),
                    sell_price: p.sell_price, cost_price: p.cost_price,
                    part_category_id: p.part_category_id,
                }))))
                .catch(() => callback());
        },
        render: {
            option: function (item, escape) {
                return `<div class="py-1"><div class="font-medium text-gray-900">${escape(item.label)}</div><div class="text-xs text-gray-500">${escape(item.sub || '')}</div></div>`;
            },
            item: function (item, escape) { return `<div class="font-medium">${escape(item.label)}</div>`; },
        },
    });
    modalPartSelect.on('change', function () {
        const v = this.getValue();
        if (!v) return;
        const p = this.options[v];
        document.getElementById('item-description').value = p.label || '';
        document.getElementById('item-unit-price').value = p.sell_price || 0;
        document.getElementById('item-cost-price').value = p.cost_price || 0;
        if (p.part_category_id) partCatSelect.value = p.part_category_id;
    });

    function applyTypeVisibility() {
        const type = typeSelect.value;
        catalogServiceWrap.classList.toggle('hidden', type !== 'service');
        catalogPartWrap.classList.toggle('hidden', type !== 'part');
        categoryServiceWrap.classList.toggle('hidden', type !== 'free_service');
        categoryPartWrap.classList.toggle('hidden', type !== 'free_part');
        supplyWrap.classList.toggle('hidden', type === 'service' || type === 'free_service');
    }
    typeSelect.addEventListener('change', applyTypeVisibility);

    function clearModal() {
        editingIndex = null;
        modalTitle.textContent = 'Agregar ítem';
        typeSelect.value = 'service';
        modalServiceSelect.clear();
        modalPartSelect.clear();
        document.getElementById('item-description').value = '';
        document.getElementById('item-quantity').value = 1;
        document.getElementById('item-unit-price').value = 0;
        document.getElementById('item-discount-pct').value = 0;
        document.getElementById('item-supply-source').value = 'internal';
        document.getElementById('item-cost-price').value = 0;
        document.getElementById('item-id').value = '';
        serviceCatSelect.value = serviceCatSelect.options[0]?.value || '';
        partCatSelect.value = partCatSelect.options[0]?.value || '';
        applyTypeVisibility();
    }

    function openItemModal(index) {
        clearModal();
        if (index !== null && index !== undefined && items[index]) {
            const item = items[index];
            editingIndex = index;
            modalTitle.textContent = 'Editar ítem';
            document.getElementById('item-id').value = item.id || '';

            let type = 'free_service';
            if (item.service_id) type = 'service';
            else if (item.part_id) type = 'part';
            else if (item.item_type === 'part') type = 'free_part';
            else type = 'free_service';
            typeSelect.value = type;

            document.getElementById('item-description').value = item.description || '';
            document.getElementById('item-quantity').value = item.quantity;
            document.getElementById('item-unit-price').value = item.unit_price;
            document.getElementById('item-discount-pct').value = item.discount_pct || 0;
            document.getElementById('item-supply-source').value = item.supply_source || 'internal';
            document.getElementById('item-cost-price').value = item.cost_price || 0;
            if (item.service_category_id) serviceCatSelect.value = item.service_category_id;
            if (item.part_category_id) partCatSelect.value = item.part_category_id;
            applyTypeVisibility();

            if (item.service_id) setModalServiceById(item.service_id);
            if (item.part_id) setModalPartById(item.part_id);
        }
        modal.classList.remove('hidden');
        modal.setAttribute('aria-hidden', 'false');
        document.body.classList.add('overflow-hidden');
    }

    function closeModal() {
        modal.classList.add('hidden');
        modal.setAttribute('aria-hidden', 'true');
        document.body.classList.remove('overflow-hidden');
        clearModal();
    }

    function setModalServiceById(id) {
        fetch(`/api/repair-services/search?id=${encodeURIComponent(id)}`)
            .then(r => r.json())
            .then(data => {
                if (!data[0]) return;
                const s = data[0];
                const opt = {
                    id: s.id, label: s.name, sub: s.category || '',
                    sell_price: s.sell_price, cost_price: s.cost_price,
                    pricing_type: s.pricing_type, estimated_hours: s.estimated_hours,
                    is_outsourced: s.is_outsourced, service_category_id: s.service_category_id,
                };
                modalServiceSelect.addOption(opt);
                modalServiceSelect.setValue(s.id, true);
            }).catch(() => {});
    }

    function setModalPartById(id) {
        fetch(`/api/parts/search?id=${encodeURIComponent(id)}`)
            .then(r => r.json())
            .then(data => {
                if (!data[0]) return;
                const p = data[0];
                const opt = {
                    id: p.id, label: p.name, sub: [p.sku, p.brand].filter(Boolean).join(' '),
                    sell_price: p.sell_price, cost_price: p.cost_price,
                    part_category_id: p.part_category_id,
                };
                modalPartSelect.addOption(opt);
                modalPartSelect.setValue(p.id, true);
            }).catch(() => {});
    }

    function saveItem() {
        const type = typeSelect.value;
        const serviceId = type === 'service' ? modalServiceSelect.getValue() : null;
        const partId = type === 'part' ? modalPartSelect.getValue() : null;
        const description = document.getElementById('item-description').value.trim();
        const quantity = num(document.getElementById('item-quantity').value);
        const unitPrice = num(document.getElementById('item-unit-price').value);
        const discountPct = Math.min(100, Math.max(0, num(document.getElementById('item-discount-pct').value)));
        const supplySource = document.getElementById('item-supply-source').value;
        const costPrice = num(document.getElementById('item-cost-price').value);

        // Validación ligera
        if (!serviceId && !partId && !description) {
            alert('Debe seleccionar un servicio, un repuesto o escribir una descripción.');
            return;
        }
        if (quantity <= 0) {
            alert('La cantidad debe ser mayor a cero.');
            return;
        }
        if (unitPrice < 0) {
            alert('El precio unitario no puede ser negativo.');
            return;
        }
        if ((type === 'free_service' && !serviceCatSelect.value) || (type === 'free_part' && !partCatSelect.value)) {
            alert('En ítems libres indique la categoría.');
            return;
        }

        const item = {
            id: document.getElementById('item-id').value || null,
            item_type: (type === 'service' || type === 'free_service') ? 'service' : 'part',
            service_id: serviceId,
            part_id: partId,
            service_category_id: type === 'service'
                ? (modalServiceSelect.options[serviceId]?.service_category_id || null)
                : (type === 'free_service' ? serviceCatSelect.value : null),
            part_category_id: type === 'part'
                ? (modalPartSelect.options[partId]?.part_category_id || null)
                : (type === 'free_part' ? partCatSelect.value : null),
            description: description || null,
            quantity,
            unit_price: unitPrice,
            discount_pct: discountPct,
            supply_source: (type === 'service' || type === 'free_service') ? 'internal' : supplySource,
            cost_price: costPrice,
        };

        if (editingIndex === null) {
            items.push(item);
        } else {
            items[editingIndex] = item;
        }

        renderItems();
        calcTotals();
        closeModal();
    }

    let saving = false;
    const saveBtn = document.getElementById('item-modal-save');
    saveBtn.addEventListener('click', function () {
        if (saving) return;
        saving = true;
        saveBtn.disabled = true;
        saveBtn.textContent = 'Guardando...';
        try {
            saveItem();
        } finally {
            saveBtn.disabled = false;
            saveBtn.textContent = 'Guardar ítem';
            saving = false;
        }
    });

    document.getElementById('btn-add-item').addEventListener('click', function () { openItemModal(null); });
    document.getElementById('item-modal-cancel').addEventListener('click', closeModal);
    document.getElementById('item-modal-close-x').addEventListener('click', closeModal);
    document.getElementById('item-modal-overlay').addEventListener('click', closeModal);
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && !modal.classList.contains('hidden')) closeModal();
    });

    // =====================================================
    // Recalcular al cambiar descuento global
    // =====================================================
    ['global_discount_type', 'global_discount_value'].forEach(id => {
        const el = document.getElementById(id);
        if (el) { el.addEventListener('input', calcTotals); el.addEventListener('change', calcTotals); }
    });

    // =====================================================
    // Precarga desde check-in o valores iniciales
    // =====================================================
    function setVehicleById(id) {
        fetch(`/api/vehicles/search?id=${encodeURIComponent(id)}`)
            .then(r => r.json())
            .then(data => {
                if (!data[0]) return;
                const v = data[0];
                vehicleSelect.addOption({ id: v.id, label: v.plate, sub: '' });
                vehicleSelect.setValue(v.id, true);
                loadVehicleContacts(v.id);
            }).catch(() => {});
    }
    function setPartyById(ts, id) {
        fetch(`/api/parties/search?id=${encodeURIComponent(id)}`)
            .then(r => r.json())
            .then(data => {
                if (!data[0]) return;
                const p = data[0];
                ts.addOption({ id: p.id, label: p.display_name, sub: p.document_number, business_name: p.business_name, hourly_rate: p.insurance_hourly_rate, panel_rate: p.insurance_panel_rate });
                ts.setValue(p.id, true);
            }).catch(() => {});
    }

    function preloadFromCheckIn(id) {
        fetch(`/api/estimates/from-check-in/${encodeURIComponent(id)}`)
            .then(r => r.json())
            .then(data => {
                if (data.vehicle) {
                    vehicleSelect.addOption({ id: data.vehicle.id, label: data.vehicle.plate, sub: '' });
                    vehicleSelect.setValue(data.vehicle.id, true);
                    loadVehicleContacts(data.vehicle.id);
                }
                if (data.insurance_company) {
                    insuranceSelect.addOption({ id: data.insurance_company.id, label: data.insurance_company.business_name, sub: data.insurance_company.document_number });
                    insuranceSelect.setValue(data.insurance_company.id, true);
                }
                if (data.claim_number) document.getElementById('claim_number').value = data.claim_number;
                if (data.hourly_rate) document.getElementById('hourly_rate').value = data.hourly_rate;
                if (data.panel_rate) document.getElementById('panel_rate').value = data.panel_rate;
                if (data.currency) document.getElementById('currency').value = data.currency;
                if (data.service_type) {
                    document.getElementById('service_type').value = data.service_type;
                    setClaimNumberVisibility(data.service_type);
                }
            }).catch(() => {});
    }

    // =====================================================
    // Servicio: mostrar/ocultar Nº Siniestro según el tipo
    // =====================================================
    function setClaimNumberVisibility(serviceType) {
        const claimWrap = document.getElementById('claim-number-wrap');
        if (claimWrap) claimWrap.classList.toggle('hidden', serviceType !== 'siniestro');
    }
    const serviceTypeSelect = document.getElementById('service_type');
    if (serviceTypeSelect) {
        serviceTypeSelect.addEventListener('change', function () {
            setClaimNumberVisibility(this.value);
        });
        // Estado inicial (creación o edición)
        setClaimNumberVisibility(initialServiceType || serviceTypeSelect.value);
    }

    if (initialCheckInId) {
        preloadFromCheckIn(initialCheckInId);
    } else {
        const vId = "{{ $initialVehicleId }}";
        if (vId) setVehicleById(vId);
        if (initialClientId) clientInput.value = initialClientId;
        if (initialInsuranceId) setPartyById(insuranceSelect, initialInsuranceId);
    }

    renderItems();
    calcTotals();
})();
</script>
@endpush