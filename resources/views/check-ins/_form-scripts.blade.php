@push('scripts')
<script>
(function () {
    'use strict';

    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
    const isEdit = {{ $isEdit ? 'true' : 'false' }};
    const checkInId = {{ $checkIn->id ?? 'null' }};
    const initialVehicleId = "{{ old('vehicle_id', $checkIn->vehicle_id ?? '') }}";
    const mockupPath = "{{ asset('images/mockups') }}";

    let currentVehicle = null; // último vehículo cargado (para editar placa)

    const ICON_PLUS = '<svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>';
    const ICON_PENCIL = '<svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>';

    function refreshNewVehicleButton() {
        const btn = document.getElementById('btn-new-vehicle');
        if (!btn) return;
        if (currentVehicle) {
            btn.innerHTML = ICON_PENCIL + ' Editar placa';
        } else {
            btn.innerHTML = ICON_PLUS + ' Nueva placa';
        }
    }

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
                .then(data => {
                    // Placa exacta (6-7 alfanuméricos): auto-seleccionar si existe
                    const q = query.trim().toUpperCase().replace(/[^A-Z0-9]/g, '');
                    if (q.length >= 6 && q.length <= 7) {
                        const exact = data.find(v => String(v.plate).toUpperCase() === q);
                        if (exact) {
                            this.addOption(exact);
                            this.setValue(exact.id, true);
                            this.blur();
                            this.close();
                            callback([]);
                            return;
                        }
                    }
                    callback(data);
                })
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

    // Convierte el payload de /api/check-ins/contacts a filas del componente
    function buildRelationshipRows(contacts) {
        const docLabels = { '1': 'DNI', '6': 'RUC', '4': 'CEX', '7': 'PAS', 'A': 'Cédula Diplomática' };
        const roles = ['owner', 'approver', 'driver', 'operator'];
        const rows = [];
        roles.forEach(role => {
            const c = contacts && contacts[role];
            if (!c) return;
            const name = c.business_name || [c.name, c.last_name].filter(Boolean).join(' ') || '';
            rows.push({
                party_id: c.party_id,
                party_label: name || '--',
                doc_label: docLabels[c.document_type] || c.document_type || '',
                doc_number: c.document_number || '',
                party_mobile: c.mobile || c.phone || '',
                party_phone: c.phone || '',
                party_email: c.email || '',
                role: role,
                is_primary_commercial: 0,
                notes: '',
            });
        });
        return rows;
    }

    // Al seleccionar un vehículo en el buscador, cargar todos sus datos
    vehicleSelect.on('change', function () {
        loadVehicleData(this.getValue());
    });

    async function loadVehicleData(vehicleId) {
        if (!vehicleId) {
            ['vehicle_brand', 'vehicle_model', 'vehicle_year', 'vehicle_color', 'vehicle_vin', 'vehicle_body_type'].forEach(id => {
                const el = document.getElementById(id);
                if (el) el.value = '';
            });
            const reviewEl2 = document.getElementById('vehicle_technical_review_date');
            if (reviewEl2) reviewEl2.value = '';
            const ownerInput = document.getElementById('owner_id');
            if (ownerInput) ownerInput.value = '';
            currentVehicle = null;
            refreshNewVehicleButton();
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
            currentVehicle = v;

            document.getElementById('vehicle_body_type').value = v.body_type || '--';
            const reviewEl = document.getElementById('vehicle_technical_review_date');
            if (reviewEl) reviewEl.value = v.technical_review_date || '';

            // Cargar/recargar aseguradoras al seleccionar el vehículo
            loadInsuranceCompanies();
            refreshNewVehicleButton();

            // Contactos del vehículo
            const contactsRes = await fetch(`/api/check-ins/contacts?vehicle_id=${encodeURIComponent(vehicleId)}`);
            const contacts = await contactsRes.json();

            // Propietario: guardar client_id oculto (la fila vive en el componente de contactos)
            const owner = contacts.owner;
            const ownerInput = document.getElementById('owner_id');
            if (ownerInput) ownerInput.value = owner ? (owner.party_id || '') : '';

            // Actualizar el componente de contactos del vehículo con las relaciones
            if (window.VehicleRelationships && window.VehicleRelationships.ci) {
                window.VehicleRelationships.ci.setRows(buildRelationshipRows(contacts));
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

    function loadInsuranceCompanies() {
        fetch('/api/check-ins/insurance-companies')
            .then(r => r.json())
            .then(data => {
                // Preservar la selección previa (p. ej. al recargar en edición)
                const previous = insuranceSelect.getValue();
                insuranceSelect.clearOptions();
                data.forEach(c => insuranceSelect.addOption({ id: c.id, business_name: c.business_name, document_number: c.document_number }));
                if (previous) insuranceSelect.setValue(previous, true);
            })
            .catch(() => { /* noop */ });
    }

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
    const damageIcons = {
        'scratch': '<svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>',
        'dent': '<svg class="h-3.5 w-3.5" fill="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="6"/></svg>',
        'crack': '<svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v4m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/></svg>'
    };
    const typeLabels = { 'scratch': 'Rayón', 'dent': 'Abolladura', 'crack': 'Quiñe' };

    function renderDamageCount() {
        const rows = damageList.querySelectorAll('.damage-row');
        damageCount.textContent = rows.length;
    }

    function addDamageRow(damage) {
        const index = damageList.querySelectorAll('.damage-row').length;
        const row = document.createElement('div');
        const key = damage.key || 'new_' + Date.now() + '_' + index;
        row.className = 'damage-row bg-gray-50 border border-gray-200 rounded-lg p-3 flex flex-wrap items-center gap-3';
        row.dataset.key = key;
        const colorClass = damage.damage_type === 'scratch' ? 'bg-green-100 text-green-800' : (damage.damage_type === 'dent' ? 'bg-red-100 text-red-800' : 'bg-blue-100 text-blue-800');
        row.innerHTML = `
            <input type="hidden" name="damages[${index}][id]" value="${damage.id || ''}">
            <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-medium ${colorClass}">${typeLabels[damage.damage_type] || damage.damage_type}</span>
            ${damage.pos_x != null && damage.pos_y != null ? `<span class="text-xs text-gray-500">X: ${damage.pos_x}% Y: ${damage.pos_y}%</span>` : ''}
            <input type="hidden" name="damages[${index}][damage_type]" value="${damage.damage_type}">
            <input type="hidden" name="damages[${index}][side]" value="${damage.side}">
            <input type="hidden" name="damages[${index}][pos_x]" value="${damage.pos_x ?? ''}">
            <input type="hidden" name="damages[${index}][pos_y]" value="${damage.pos_y ?? ''}">
            <input type="text" name="damages[${index}][notes]" value="${damage.notes || ''}" placeholder="Nota..." class="flex-1 min-w-[120px] rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-xs">
            <button type="button" class="damage-remove text-red-600 hover:text-red-800 text-xs font-medium inline-flex items-center gap-1">
                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                Eliminar
            </button>
        `;
        damageList.appendChild(row);
        renderDamageCount();
        return key;
    }

    function appendMarker(posX, posY, type, key) {
        const marker = document.createElement('div');
        marker.className = 'damage-marker absolute rounded-full flex items-center justify-center text-white text-xs font-bold';
        marker.style.left = posX + '%';
        marker.style.top = posY + '%';
        marker.style.transform = 'translate(-50%, -50%)';
        marker.style.width = '22px';
        marker.style.height = '22px';
        marker.style.background = damageColors[type];
        marker.innerHTML = damageIcons[type];
        if (key) marker.dataset.key = key;
        markersLayer.appendChild(marker);
    }

    // Reenumera los índices name="damages[N][...]" tras agregar/eliminar filas
    function reindexDamageRows() {
        damageList.querySelectorAll('.damage-row').forEach((row, index) => {
            row.querySelectorAll('input[name^="damages["]').forEach(input => {
                input.setAttribute('name', input.getAttribute('name').replace(/^damages\[\d+\]/, `damages[${index}]`));
            });
        });
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
            const key = row.dataset.key;
            if (posX && posY && type) {
                appendMarker(parseInt(posX, 10), parseInt(posY, 10), type, key);
            }
        });
    }

    mockupImg.addEventListener('click', function (e) {
        const rect = mockupImg.getBoundingClientRect();
        const posX = Math.round(((e.clientX - rect.left) / rect.width) * 100);
        const posY = Math.round(((e.clientY - rect.top) / rect.height) * 100);
        const key = addDamageRow({ damage_type: selectedDamageType, side: 'front', pos_x: posX, pos_y: posY, notes: '' });
        appendMarker(posX, posY, selectedDamageType, key);
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
        const row = btn.closest('.damage-row');
        if (!row) return;
        const key = row.dataset.key;
        if (key) {
            const marker = markersLayer.querySelector(`.damage-marker[data-key="${CSS.escape(key)}"]`);
            if (marker) marker.remove();
        }
        row.remove();
        renderDamageCount();
        reindexDamageRows();
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
            <button type="button" class="photo-delete absolute top-1 right-1 bg-red-600 text-white rounded-full w-6 h-6 hidden group-hover:flex items-center justify-center" title="Eliminar">
                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
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

    // ===== 6. Modal: Nueva / Editar placa =====
    // El modal maneja el guardado (quick-store / quick-update) y emite "vehicle-saved".
    document.getElementById('btn-new-vehicle')?.addEventListener('click', function () {
        if (typeof window.openVehicleModal !== 'function') {
            console.error('[form-scripts] window.openVehicleModal no esta definido. Revisa el script de check-ins/_vehicle_modal.');
            return;
        }
        if (currentVehicle) {
            // Modo edicion: pre-rellenar todos los datos del vehiculo seleccionado
            window.openVehicleModal(currentVehicle);
        } else {
            // Modo nueva placa: pre-rellenar la placa escrita en el buscador
            const typed = vehicleSelect.input ? vehicleSelect.input.value.trim() : '';
            window.openVehicleModal(typed);
        }
    });

    // Tras guardar (nueva o edición) en el modal, integrar el vehículo en el formulario
    document.addEventListener('vehicle-saved', function (e) {
        const data = e.detail;
        if (!data || !data.id) return;
        vehicleSelect.addOption(data);
        vehicleSelect.setValue(data.id, true);
        loadVehicleData(data.id);
        refreshNewVehicleButton();
    });

    // Sincronizar client_id (owner_id) con la fila "Propietario" del componente ci
    if (window.VehicleRelationships && window.VehicleRelationships.ci) {
        window.VehicleRelationships.ci.onRowsChanged(function (rows) {
            const ownerRow = rows.find(r => r.role === 'owner');
            const ownerInput = document.getElementById('owner_id');
            if (ownerInput) ownerInput.value = ownerRow ? (ownerRow.party_id || '') : '';
        });
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
