@push('scripts')
<script>
(function () {
    'use strict';

    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
    const isEdit = {{ $isEdit ? 'true' : 'false' }};
    const checkInId = {{ $checkIn->id ?? 'null' }};
    const initialVehicleId = "{{ old('vehicle_id', $checkIn->vehicle_id ?? '') }}";
    const mockupPath = "{{ asset('images/mockups') }}";

    // =====================================================
    // 1. Tom Select vehículo + autocompletado
    // =====================================================
    const vehicleSelect = new TomSelect('#vehicle_id', {
        valueField: 'id',
        labelField: 'plate',
        searchField: ['plate', 'brand', 'model'],
        copyClassesToDropdown: false,
        placeholder: 'Buscar por placa, marca o modelo...',
        create: false,
        maxOptions: 20,
        closeAfterSelect: true, maxItems: 1,
        noResults: 'No se encontraron resultados...',
        shouldLoad: function (query) { return query.length >= 2; },
        onKeyDown: function (e) {
            // Solo permitir teclas alfanuméricas (letras y dígitos)
            const isPrintable = e.key.length === 1;
            if (isPrintable && !/[A-Za-z0-9]/.test(e.key) && !e.ctrlKey && !e.metaKey) {
                e.preventDefault();
            }
        },
        load: function (query, callback) {
            fetch(`/api/vehicles/search?q=${encodeURIComponent(query)}&limit=20`)
                .then(r => r.json())
                .then(data => callback(data))
                .catch(() => callback());
        },
        render: {
            option: function (item, escape) {
                return `<div class="py-1">
                    <div class="font-medium text-gray-900">${escape(item.plate)}</div>
                    <div class="text-xs text-gray-500">${escape(item.brand || '')} ${escape(item.model || '')} ${escape(item.year || '')}</div>
                </div>`;
            },
            item: function (item, escape) {
                return `<div class="font-medium">${escape(item.plate)}</div>`;
            },
        },
    });

    // Single estricto: blur + cerrar dropdown al seleccionar
    vehicleSelect.on('item_add', function () {
        vehicleSelect.blur();
        vehicleSelect.close();
    });
    vehicleSelect.on('dropdown_open', function () {
        if (vehicleSelect.items.length > 0) {
            vehicleSelect.setTextValue('');
            vehicleSelect.input && vehicleSelect.input.setSelectionRange(0, 0);
        }
    });

    function setInputValue(name, value) {
        const el = document.querySelector(`input[name="${name}"]`);
        if (el && el.value === '') el.value = value || '';
    }

    // Al seleccionar un vehículo en el buscador, cargar todos sus datos
    vehicleSelect.on('change', function () {
        loadVehicleData(this.getValue());
    });

    async function loadVehicleData(vehicleId) {
        if (!vehicleId) {
            ['vehicle_brand', 'vehicle_model', 'vehicle_year', 'vehicle_color', 'vehicle_vin', 'vehicle_body_type',
             'owner_name', 'owner_document', 'owner_phone', 'owner_email'].forEach(id => {
                const el = document.getElementById(id);
                if (el) el.value = '';
            });
            document.getElementById('owner_id').value = '';
            return;
        }

        try {
            const res = await fetch(`/api/vehicles/search?id=${encodeURIComponent(vehicleId)}`);
            const data = await res.json();
            const v = data[0];
            if (!v) return;

            document.getElementById('vehicle_brand').value = v.brand || '--';
            document.getElementById('vehicle_model').value = v.model || '--';
            document.getElementById('vehicle_year').value = v.year || '--';
            document.getElementById('vehicle_color').value = v.color || '--';
            document.getElementById('vehicle_vin').value = v.vin || '--';
            document.getElementById('vehicle_body_type').value = v.body_type || '--';

            // Contactos del vehículo
            const contactsRes = await fetch(`/api/check-ins/contacts?vehicle_id=${encodeURIComponent(vehicleId)}`);
            const contacts = await contactsRes.json();

            // Propietario
            const owner = contacts.owner;
            if (owner) {
                document.getElementById('owner_name').value = owner.business_name || [owner.name, owner.last_name].filter(Boolean).join(' ') || '--';
                document.getElementById('owner_document').value = owner.document_number || '--';
                document.getElementById('owner_phone').value = owner.phone || owner.mobile || '--';
                document.getElementById('owner_email').value = owner.email || '--';
                document.getElementById('owner_id').value = owner.party_id || '';
            } else {
                document.getElementById('owner_name').value = '--';
                document.getElementById('owner_document').value = '--';
                document.getElementById('owner_phone').value = '--';
                document.getElementById('owner_email').value = '--';
                document.getElementById('owner_id').value = '';
            }

            // Llenar contactos (solo si están vacíos)
            if (contacts.approver) {
                const name = contacts.approver.business_name || [contacts.approver.name, contacts.approver.last_name].filter(Boolean).join(' ');
                setInputValue('contacts[approver][name]', name);
                setInputValue('contacts[approver][phone]', contacts.approver.mobile || contacts.approver.phone);
                setInputValue('contacts[approver][email]', contacts.approver.email);
            }
            if (contacts.driver) {
                const name = contacts.driver.business_name || [contacts.driver.name, contacts.driver.last_name].filter(Boolean).join(' ');
                setInputValue('contacts[driver][name]', name);
                setInputValue('contacts[driver][phone]', contacts.driver.mobile || contacts.driver.phone);
                setInputValue('contacts[driver][email]', contacts.driver.email);
            }
            if (contacts.operator) {
                setInputValue('contacts[operator][company]', contacts.operator.business_name);
                setInputValue('contacts[operator][name]', [contacts.operator.name, contacts.operator.last_name].filter(Boolean).join(' '));
                setInputValue('contacts[operator][phone]', contacts.operator.mobile || contacts.operator.phone);
                setInputValue('contacts[operator][email]', contacts.operator.email);
            }

            loadMockup(v.body_type);
        } catch (e) {
            console.error('Error cargando vehículo:', e);
        }
    }

    // =====================================================
    // 2. Tom Select aseguradora
    // =====================================================
    const insuranceSelect = new TomSelect('#insurance_company_id', {
        valueField: 'id',
        labelField: 'business_name',
        searchField: ['business_name', 'document_number'],
        placeholder: 'Seleccionar aseguradora...',
        copyClassesToDropdown: false,
        create: false,
        maxOptions: 20,
        closeAfterSelect: true, maxItems: 1,
        allowEmptyOption: true,
    });

    // Single estricto: blur + cerrar dropdown al seleccionar
    insuranceSelect.on('item_add', function () {
        insuranceSelect.blur();
        insuranceSelect.close();
    });
    insuranceSelect.on('dropdown_open', function () { if (insuranceSelect.items.length > 0) { insuranceSelect.setTextValue(''); insuranceSelect.input && insuranceSelect.input.setSelectionRange(0, 0); } });

    fetch('/api/check-ins/insurance-companies')
        .then(r => r.json())
        .then(data => {
            data.forEach(c => insuranceSelect.addOption({ id: c.id, business_name: c.business_name, document_number: c.document_number }));
            const initial = "{{ old('insurance_company_id', $checkIn->insurance_company_id ?? '') }}";
            if (initial) insuranceSelect.setValue(initial);
        });

    // =====================================================
    // 3. Mockup de daños
    // =====================================================
    const imgWrap = document.getElementById('damage-mockup-image-wrap');
    const noImg = document.getElementById('damage-no-image');
    const mockupImg = document.getElementById('damage-mockup-image');
    const markersLayer = document.getElementById('damage-markers-layer');
    const damageList = document.getElementById('damage-list');
    const damageCount = document.getElementById('damage-count');
    let selectedDamageType = 'scratch';

    const damageColors = { 'scratch': '#10b981', 'dent': '#ef4444', 'crack': '#3b82f6' };
    const damageIcons = { 'scratch': '✕', 'dent': '●', 'crack': '▲' };
    const typeLabels = { 'scratch': 'Rayón', 'dent': 'Abolladura', 'crack': 'Quebre' };

    function renderDamageCount() {
        const rows = damageList.querySelectorAll('.damage-row');
        damageCount.textContent = rows.length;
    }

    function addDamageRow(damage) {
        const index = damageList.querySelectorAll('.damage-row').length;
        const row = document.createElement('div');
        row.className = 'damage-row bg-gray-50 border border-gray-200 rounded-lg p-3 flex flex-wrap items-center gap-3';
        const colorClass = damage.damage_type === 'scratch' ? 'bg-green-100 text-green-800' : (damage.damage_type === 'dent' ? 'bg-red-100 text-red-800' : 'bg-blue-100 text-blue-800');
        row.innerHTML = `
            <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-medium ${colorClass}">${typeLabels[damage.damage_type] || damage.damage_type}</span>
            ${damage.pos_x != null && damage.pos_y != null ? `<span class="text-xs text-gray-500">X: ${damage.pos_x}% Y: ${damage.pos_y}%</span>` : ''}
            <input type="hidden" name="damages[${index}][damage_type]" value="${damage.damage_type}">
            <input type="hidden" name="damages[${index}][side]" value="${damage.side}">
            <input type="hidden" name="damages[${index}][pos_x]" value="${damage.pos_x ?? ''}">
            <input type="hidden" name="damages[${index}][pos_y]" value="${damage.pos_y ?? ''}">
            <input type="text" name="damages[${index}][notes]" value="${damage.notes || ''}" placeholder="Nota..." class="flex-1 min-w-[120px] rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-xs">
            <button type="button" class="damage-remove text-red-600 hover:text-red-800 text-xs font-medium">✕ Eliminar</button>
        `;
        damageList.appendChild(row);
        renderDamageCount();
    }

    function appendMarker(posX, posY, type) {
        const marker = document.createElement('div');
        marker.className = 'damage-marker absolute rounded-full flex items-center justify-center text-white text-xs font-bold';
        marker.style.left = posX + '%';
        marker.style.top = posY + '%';
        marker.style.transform = 'translate(-50%, -50%)';
        marker.style.width = '22px';
        marker.style.height = '22px';
        marker.style.background = damageColors[type];
        marker.textContent = damageIcons[type];
        markersLayer.appendChild(marker);
    }

    function loadMockup(bodyType) {
        if (!bodyType) {
            imgWrap.classList.add('hidden');
            noImg.classList.remove('hidden');
            return;
        }
        const exts = ['jpg', 'jpeg', 'png', 'svg'];
        let idx = 0;
        const tryNext = () => {
            if (idx >= exts.length) {
                imgWrap.classList.add('hidden');
                noImg.classList.remove('hidden');
                return;
            }
            const ext = exts[idx++];
            const img = new Image();
            img.onload = () => {
                mockupImg.src = `${mockupPath}/${bodyType}.${ext}`;
                imgWrap.classList.remove('hidden');
                noImg.classList.add('hidden');
                paintMarkersFromRows();
            };
            img.onerror = tryNext;
            img.src = `${mockupPath}/${bodyType}.${ext}?t=${Date.now()}`;
        };
        tryNext();
    }

    // Pinta los marcadores de los daños ya registrados sobre el mockup (edición)
    function paintMarkersFromRows() {
        markersLayer.innerHTML = '';
        document.querySelectorAll('.damage-row').forEach(row => {
            const posX = row.querySelector('input[name*="[pos_x]"]')?.value;
            const posY = row.querySelector('input[name*="[pos_y]"]')?.value;
            const type = row.querySelector('input[name*="[damage_type]"]')?.value;
            if (posX && posY && type) {
                appendMarker(parseInt(posX, 10), parseInt(posY, 10), type);
            }
        });
    }

    mockupImg.addEventListener('click', function (e) {
        const rect = mockupImg.getBoundingClientRect();
        const posX = Math.round(((e.clientX - rect.left) / rect.width) * 100);
        const posY = Math.round(((e.clientY - rect.top) / rect.height) * 100);
        addDamageRow({ damage_type: selectedDamageType, side: 'front', pos_x: posX, pos_y: posY, notes: '' });
        appendMarker(posX, posY, selectedDamageType);
    });

    document.querySelectorAll('.damage-tool-btn').forEach(btn => {
        btn.addEventListener('click', function () {
            selectedDamageType = this.dataset.type;
            document.querySelectorAll('.damage-tool-btn').forEach(b => b.classList.remove('ring-2', 'ring-offset-2', 'ring-gray-900'));
            this.classList.add('ring-2', 'ring-offset-2', 'ring-gray-900');
        });
    });
    document.querySelector('.damage-tool-btn[data-type="scratch"]')?.classList.add('ring-2', 'ring-offset-2', 'ring-gray-900');

    document.getElementById('btn-damage-undo').addEventListener('click', function () {
        const lastMarker = markersLayer.querySelector('.damage-marker:last-child');
        if (lastMarker) lastMarker.remove();
        const rows = damageList.querySelectorAll('.damage-row');
        const lastRow = rows[rows.length - 1];
        if (lastRow) lastRow.remove();
        renderDamageCount();
    });

    document.getElementById('btn-damage-clear').addEventListener('click', function () {
        markersLayer.innerHTML = '';
        damageList.innerHTML = '';
        renderDamageCount();
    });

    damageList.addEventListener('click', function (e) {
        const btn = e.target.closest('.damage-remove');
        if (!btn) return;
        btn.closest('.damage-row').remove();
        renderDamageCount();
    });

    // =====================================================
    // 4. Checklist: botones de estado + filtro "solo regulares y malos"
    // =====================================================
    const onlyIssues = document.getElementById('only-issues');
    const checklistCards = document.querySelectorAll('.checklist-card');
    const activeClasses = {
        'good': 'bg-green-600 text-white border-transparent',
        'regular': 'bg-amber-500 text-white border-transparent',
        'bad': 'bg-red-600 text-white border-transparent',
        'not_applicable': 'bg-gray-700 text-white border-transparent',
    };
    const idleClasses = {
        'good': 'border-green-500 text-green-500 hover:bg-green-50',
        'regular': 'border-amber-500 text-amber-500 hover:bg-amber-50',
        'bad': 'border-red-500 text-red-500 hover:bg-red-50',
        'not_applicable': 'border-gray-500 text-gray-500 hover:bg-gray-50',
    };

    function resetChecklistBtn(card) {
        card.querySelectorAll('.checklist-btn').forEach(btn => {
            btn.className = 'checklist-btn w-8 h-8 rounded-full border-2 flex items-center justify-center text-sm font-bold transition-colors ' + (idleClasses[btn.dataset.state] || '');
        });
    }

    function setChecklistStatus(card, state) {
        resetChecklistBtn(card);
        const btn = card.querySelector(`.checklist-btn[data-state="${state}"]`);
        if (btn) {
            btn.className = 'checklist-btn w-8 h-8 rounded-full border-2 flex items-center justify-center text-sm font-bold transition-colors ' + (activeClasses[state] || '');
        }
        const input = card.querySelector('.checklist-status-input');
        if (input) input.value = state;
    }

    checklistCards.forEach(card => {
        card.querySelectorAll('.checklist-btn').forEach(btn => {
            btn.addEventListener('click', function () {
                setChecklistStatus(card, this.dataset.state);
                if (onlyIssues.checked) {
                    const state = card.querySelector('.checklist-status-input').value;
                    card.classList.toggle('hidden', state !== 'regular' && state !== 'bad');
                }
            });
        });
    });

    onlyIssues.addEventListener('change', function () {
        checklistCards.forEach(card => {
            const state = card.querySelector('.checklist-status-input')?.value;
            if (this.checked) {
                card.classList.toggle('hidden', state !== 'regular' && state !== 'bad');
            } else {
                card.classList.remove('hidden');
            }
        });
    });

    // Mostrar/ocultar Nº Siniestro según tipo de servicio
    const serviceType = document.getElementById('service_type');
    const claimWrap = document.getElementById('claim-number-wrap');
    serviceType.addEventListener('change', function () {
        claimWrap.classList.toggle('hidden', this.value !== 'siniestro');
    });

    // Deshabilitar inputs de contactos si "save_contacts" no está marcado
    const saveContactsChk = document.getElementById('save_contacts');
    const contactInputs = document.querySelectorAll('input[name^="contacts["]');
    function setContactInputsDisabled(disabled) {
        contactInputs.forEach(input => input.disabled = disabled);
    }
    if (saveContactsChk) {
        saveContactsChk.addEventListener('change', function () {
            setContactInputsDisabled(!this.checked);
        });
        setContactInputsDisabled(!saveContactsChk.checked);
    }

    // =====================================================
    // 5. Fotos
    // =====================================================
    const photoInput = document.getElementById('photo-input');
    const photoPreview = document.getElementById('photo-preview');
    const photoProgress = document.getElementById('photo-progress');

    photoInput.addEventListener('change', function () {
        const files = Array.from(this.files);
        this.value = '';

        if (isEdit && checkInId) {
            files.forEach((file) => {
                photoProgress.classList.remove('hidden');
                const fd = new FormData();
                fd.append('photo', file);
                fetch(`/api/check-ins/${checkInId}/photos`, {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
                    body: fd,
                })
                .then(r => r.json())
                .then(data => {
                    if (data.id) appendPhotoItem({ id: data.id, url: data.url });
                })
                .catch(err => console.error('Error subiendo foto:', err))
                .finally(() => photoProgress.classList.add('hidden'));
            });
        } else {
            files.forEach(file => {
                const reader = new FileReader();
                reader.onload = e => {
                    const div = document.createElement('div');
                    div.className = 'photo-item relative group';
                    div.innerHTML = `<img src="${e.target.result}" class="w-full h-32 object-cover rounded-lg border border-gray-200" alt="Foto">`;
                    photoPreview.appendChild(div);
                };
                reader.readAsDataURL(file);
            });
        }
    });

    function appendPhotoItem(photo) {
        const div = document.createElement('div');
        div.className = 'photo-item relative group';
        div.dataset.id = photo.id;
        div.innerHTML = `
            <img src="${photo.url}" class="w-full h-32 object-cover rounded-lg border border-gray-200" alt="Foto del vehículo">
            <button type="button" class="photo-delete absolute top-1 right-1 bg-red-600 text-white rounded-full w-6 h-6 text-xs hidden group-hover:flex items-center justify-center" title="Eliminar">✕</button>
        `;
        photoPreview.appendChild(div);
    }

    photoPreview.addEventListener('click', function (e) {
        const btn = e.target.closest('.photo-delete');
        if (!btn) return;
        const item = btn.closest('.photo-item');
        const id = item.dataset.id;
        if (id && isEdit && checkInId) {
            fetch(`/api/check-ins/${checkInId}/photos/${id}`, {
                method: 'DELETE',
                headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
            }).finally(() => item.remove());
        } else {
            item.remove();
        }
    });

    // ===== 6. Modal: Nueva placa =====
    const vm = document.getElementById('vehicleModal');
    if (vm) {
        document.getElementById('btn-new-vehicle')?.addEventListener('click', function () {
            vm.classList.remove('hidden'); vm.classList.add('flex');
            document.getElementById('vm-plate').focus();
        });
        document.getElementById('vm-cancel')?.addEventListener('click', function () {
            vm.classList.add('hidden'); vm.classList.remove('flex');
        });
        document.getElementById('vm-save')?.addEventListener('click', async function () {
            const plate = document.getElementById('vm-plate').value.trim().toUpperCase().replace(/[^A-Z0-9]/g, '').slice(0, 7);
            const brandId = document.getElementById('vm-brand').value;
            const modelId = document.getElementById('vm-model').value;
            if (!plate || !brandId || !modelId) { alert('Complete placa, marca y modelo.'); return; }
            const btn = this; btn.disabled = true; btn.textContent = 'Guardando...';
            try {
                const res = await fetch('/api/vehicles/quick-store', {
                    method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
                    body: JSON.stringify({
                        plate, brand_id: brandId, model_id: modelId,
                        color: document.getElementById('vm-color').value.trim().toUpperCase() || null,
                        year: document.getElementById('vm-year').value || null,
                        vin: document.getElementById('vm-vin').value.trim().toUpperCase() || null,
                        engine_number: document.getElementById('vm-engine').value.trim().toUpperCase() || null,
                        body_type: document.getElementById('vm-body-type').value || null,
                        technical_review_date: document.getElementById('vm-review').value || null,
                    }),
                });
                const data = await res.json().catch(() => ({}));
                if (!res.ok) { alert(data.errors ? Object.values(data.errors).flat().join(' ') : 'No se pudo guardar.'); return; }
                vehicleSelect.addOption(data); vehicleSelect.setValue(data.id); loadVehicleData(data.id);
                vm.classList.add('hidden'); vm.classList.remove('flex');
            } catch (e) { alert('Error al guardar el vehículo.'); }
            finally { btn.disabled = false; btn.textContent = 'Guardar y seleccionar'; }
        });
    }

    // ===== 7. Modal de contacto (componente compartido ContactModal) =====
    document.getElementById('btn-new-contact')?.addEventListener('click', function () {
        if (typeof window.ContactModal === 'undefined') return;
        window.ContactModal.open({
            roles: ['owner', 'approver', 'driver', 'operator', 'billing', 'insurance_company', 'emergency_contact', 'other'],
            showPrimary: false,
            onSelect: function (party, meta) {
                assignContactToCheckIn(party, meta.role);
            },
        });
    });

    // Asigna el contacto seleccionado a los campos del inventario según rol
    function assignContactToCheckIn(party, role) {
        const name = party.business_name || [party.first_name, party.last_name].filter(Boolean).join(' ') || party.display_name || '';
        const set = (name, value) => { const el = document.querySelector(`input[name="${name}"]`); if (el) el.value = value || ''; };

        if (role === 'owner') {
            document.getElementById('owner_name').value = name || '--';
            document.getElementById('owner_document').value = party.document_number || '--';
            document.getElementById('owner_phone').value = party.mobile || party.phone || '--';
            document.getElementById('owner_email').value = party.email || '--';
            document.getElementById('owner_id').value = party.id;
            return;
        }
        // Aprobador / Conductor / Operador
        if (role === 'approver' || role === 'driver') {
            set(`contacts[${role}][name]`, name);
            set(`contacts[${role}][phone]`, party.mobile || party.phone);
            set(`contacts[${role}][landline]`, party.phone);
            set(`contacts[${role}][email]`, party.email);
        } else if (role === 'operator') {
            set('contacts[operator][company]', party.business_name);
            set('contacts[operator][name]', party.first_name || party.last_name ? name : party.display_name);
            set('contacts[operator][phone]', party.mobile || party.phone);
            set('contacts[operator][landline]', party.phone);
            set('contacts[operator][email]', party.email);
        } else if (role === 'insurance_company') {
            const opt = { id: party.id, business_name: party.display_name || party.business_name, document_number: party.document_number };
            insuranceSelect.addOption(opt);
            insuranceSelect.setValue(party.id);
        } else {
            alert('Rol "' + role + '" no asignado directamente en el inventario. Se guardó el contacto en la agenda.');
        }
    }

    // =====================================================
    // Init: cargar opción del vehículo existente en edición
    // =====================================================
    async function initVehicleSelect() {
        if (!initialVehicleId) return;
        try {
            const res = await fetch(`/api/vehicles/search?id=${encodeURIComponent(initialVehicleId)}`);
            const data = await res.json();
            if (data[0]) {
                vehicleSelect.addOption(data[0]);       // Registrar la opción en el Tom Select
                vehicleSelect.setValue(data[0].id);     // Ahora sí se mostrará la placa
                loadVehicleData(data[0].id);
            }
        } catch (e) {
            console.error('Error inicializando vehículo:', e);
        }
    }
    initVehicleSelect();
})();
</script>
@endpush
