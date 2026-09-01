@php
    $appointment = $appointment ?? null;
    $isEdit = !is_null($appointment);
    $scheduledDate = old('scheduled_date', $appointment?->scheduled_at?->format('Y-m-d') ?? now()->format('Y-m-d'));
    $scheduledTime = old('scheduled_time', $appointment?->scheduled_at?->format('H:i') ?? '09:00');

    $initialVehicle = $appointment?->vehicle ? [
        'id' => $appointment->vehicle->id,
        'plate' => $appointment->vehicle->plate,
        'brand' => $appointment->vehicle->vehicleModel?->brand?->name,
        'model' => $appointment->vehicle->vehicleModel?->name,
        'year' => $appointment->vehicle->year,
        'color' => $appointment->vehicle->color,
    ] : null;

    $initialParty = $appointment?->party ? [
        'id' => $appointment->party->id,
        'display_name' => $appointment->party->display_name,
    ] : null;
@endphp

<form method="POST" action="{{ $isEdit ? route('appointments.update', $appointment) : route('appointments.store') }}" class="space-y-6">
    @csrf
    @if ($isEdit)
        @method('PUT')
    @endif

    {{-- ============ SECCIÓN 1: VEHÍCULO ============ --}}
    <div class="border-b border-gray-200 pb-6 mb-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Vehículo</h3>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <div class="flex items-center justify-between gap-3">
                    <label for="vehicle_id" class="block text-sm font-medium text-gray-700">Buscar vehículo por placa</label>
                    <button type="button" id="btn-new-vehicle" class="inline-flex items-center gap-1 text-xs font-medium text-blue-600 hover:underline">
                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                        Nueva placa
                    </button>
                </div>
                <select id="vehicle_id" name="vehicle_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"></select>
                @error('vehicle_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Marca</label>
                <input type="text" id="vehicle_brand" readonly class="mt-1 block w-full rounded-md border-gray-300 bg-gray-50 shadow-sm" placeholder="--">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Modelo</label>
                <input type="text" id="vehicle_model" readonly class="mt-1 block w-full rounded-md border-gray-300 bg-gray-50 shadow-sm" placeholder="--">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Año</label>
                <input type="text" id="vehicle_year" readonly class="mt-1 block w-full rounded-md border-gray-300 bg-gray-50 shadow-sm" placeholder="--">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Color</label>
                <input type="text" id="vehicle_color" readonly class="mt-1 block w-full rounded-md border-gray-300 bg-gray-50 shadow-sm" placeholder="--">
            </div>
        </div>
    </div>

    {{-- ============ SECCIÓN 3: PROGRAMACIÓN ============ --}}
    <div class="border-b border-gray-200 pb-6 mb-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Programación</h3>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label for="scheduled_date" class="block text-sm font-medium text-gray-700">Fecha <span class="text-red-500">*</span></label>
                <input type="date" id="scheduled_date" name="scheduled_date" value="{{ $scheduledDate }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                @error('scheduled_date') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>
            <div>
                <label for="scheduled_time" class="block text-sm font-medium text-gray-700">Hora <span class="text-red-500">*</span></label>
                <input type="time" id="scheduled_time" name="scheduled_time" value="{{ $scheduledTime }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                @error('scheduled_time') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>
            <div>
                <label for="advisor_id" class="block text-sm font-medium text-gray-700">Asesor</label>
                <select id="advisor_id" name="advisor_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"></select>
            </div>
            <div>
                <label for="service_type" class="block text-sm font-medium text-gray-700">Tipo de servicio</label>
                <select id="service_type" name="service_type" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="">Seleccionar...</option>
                    @foreach (\App\Models\CheckIn::SERVICE_TYPES as $value => $label)
                        <option value="{{ $value }}" {{ old('service_type', $appointment?->service_type ?? '') === $value ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="md:col-span-3">
                <label for="reason" class="block text-sm font-medium text-gray-700">Motivo</label>
                <textarea id="reason" name="reason" rows="2" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('reason', $appointment?->reason ?? '') }}</textarea>
            </div>
        </div>
    </div>

    <div class="flex gap-2 justify-end">
        <a href="{{ route('appointments.index') }}" class="btn btn-secondary">Cancelar</a>
        <button type="submit" class="btn btn-primary">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/></svg>
            {{ $isEdit ? 'Actualizar cita' : 'Agendar cita' }}
        </button>
    </div>
</form>

@include('check-ins._vehicle_modal')

@push('scripts')
<script>
(function () {
    'use strict';
    try {
        const initialVehicle = @json($initialVehicle);
        const initialParty = @json($initialParty);

        // ===== Tom Select: vehículo =====
        const vehicleSelect = new TomSelect('#vehicle_id', {
            valueField: 'id',
            labelField: 'plate',
            searchField: ['plate'],
            maxItems: 1,
            closeAfterSelect: true,
            create: false,
            copyClassesToDropdown: false,
            placeholder: 'Buscar por placa...',
            options: initialVehicle ? [initialVehicle] : [],
            items: initialVehicle ? [String(initialVehicle.id)] : [],
            load: function (query, callback) {
                if (query.length < 1) return callback();
                fetch(`/api/vehicles/search?q=${encodeURIComponent(query)}&limit=100`)
                    .then(r => r.json())
                    .then(data => callback(data))
                    .catch(() => callback());
            },
            render: {
                option: function (item, escape) {
                    return `<div><span class="font-medium">${escape(item.plate)}</span>${item.brand ? ` <span class="text-gray-500">· ${escape(item.brand)} ${escape(item.model || '')} ${item.year ? '· ' + escape(String(item.year)) : ''}</span>` : ''}</div>`;
                }
            },
            onItemAdd: function () {
                this.blur();
                this.close();
            },
            onDropdownOpen: function () {
                if (this.items.length) {
                    this.setTextValue('');
                    const input = this.input;
                    if (input && input.setSelectionRange) input.setSelectionRange(0, 0);
                }
            }
        });

        function fillVehicleReadonly(vehicle) {
            document.getElementById('vehicle_brand').value = vehicle?.brand || '';
            document.getElementById('vehicle_model').value = vehicle?.model || '';
            document.getElementById('vehicle_year').value = vehicle?.year || '';
            document.getElementById('vehicle_color').value = vehicle?.color || '';
        }

        function loadVehicleContacts(vehicleId) {
            if (!vehicleId) {
                partySelect.clear(true);
                partySelect.clearOptions();
                return Promise.resolve();
            }
            return fetch(`/api/vehicles/${vehicleId}/recipients`)
                .then(r => r.json())
                .then(contacts => {
                    partySelect.clear(true);
                    partySelect.clearOptions();
                    contacts.forEach(c => {
                        if (!c.party_id) return;
                        partySelect.addOption({
                            id: c.party_id,
                            display_name: `${c.contact_name || ''} — ${c.role_label || ''}`,
                            contact_name: c.contact_name || '',
                            contact_phone: c.contact_phone || '',
                            contact_email: c.contact_email || ''
                        });
                    });
                })
                .catch(() => {});
        }

        // ===== Tom Select: contacto (se llena con los contactos del vehículo) =====
        const partySelect = new TomSelect('#party_id', {
            valueField: 'id',
            labelField: 'display_name',
            searchField: ['display_name'],
            maxItems: 1,
            closeAfterSelect: true,
            create: false,
            copyClassesToDropdown: false,
            placeholder: 'Contacto del vehículo...',
            options: initialParty ? [initialParty] : [],
            items: initialParty ? [String(initialParty.id)] : [],
            onItemAdd: function () {
                this.blur();
                this.close();
            },
            onDropdownOpen: function () {
                if (this.items.length) {
                    this.setTextValue('');
                    const input = this.input;
                    if (input && input.setSelectionRange) input.setSelectionRange(0, 0);
                }
            },
            onChange: function (value) {
                const opt = value ? partySelect.options[value] : null;
                if (opt) {
                    document.getElementById('contact_name').value = opt.contact_name || '';
                    document.getElementById('contact_phone').value = opt.contact_phone || '';
                    document.getElementById('contact_email').value = opt.contact_email || '';
                }
            }
        });

        vehicleSelect.on('change', function (value) {
            const opt = value ? vehicleSelect.options[value] : null;
            fillVehicleReadonly(opt || null);
            loadVehicleContacts(value);
        });

        // ===== Tom Select: asesor =====
        new TomSelect('#advisor_id', {
            valueField: 'id',
            labelField: 'name',
            searchField: ['name', 'email'],
            maxItems: 1,
            closeAfterSelect: true,
            create: false,
            copyClassesToDropdown: false,
            placeholder: 'Asesor responsable...',
            load: function (query, callback) {
                fetch(`/api/users/data?q=${encodeURIComponent(query)}&limit=100`)
                    .then(r => r.json())
                    .then(data => callback(data.map(u => ({ id: u.id, name: `${u.name}${u.roles ? ' · ' + u.roles : ''}` }))))
                    .catch(() => callback());
            },
            onItemAdd: function () {
                this.blur();
                this.close();
            },
            onDropdownOpen: function () {
                if (this.items.length) {
                    this.setTextValue('');
                    const input = this.input;
                    if (input && input.setSelectionRange) input.setSelectionRange(0, 0);
                }
            }
        });

        // ===== Modal "Nueva placa": integrar vehículo guardado =====
        document.addEventListener('vehicle-saved', function (e) {
            const v = e.detail;
            if (!v || !v.id) return;
            vehicleSelect.addOption({
                id: v.id,
                plate: v.plate,
                brand: v.brand,
                model: v.model,
                year: v.year,
                color: v.color
            });
            vehicleSelect.setValue(String(v.id), true);
        });

        // Abrir el modal (el script del modal expone window.openVehicleModal)
        document.getElementById('btn-new-vehicle')?.addEventListener('click', function () {
            if (typeof window.openVehicleModal !== 'function') {
                console.error('[appointment-form] window.openVehicleModal no esta definido. Revisa check-ins/_vehicle_modal.');
                return;
            }
            const selected = vehicleSelect.getValue();
            const typed = vehicleSelect.input ? vehicleSelect.input.value.trim() : '';
            window.openVehicleModal(selected ? vehicleSelect.options[selected] : typed);
        });

        // Precarga de datos readonly cuando ya hay vehículo (edición)
        if (initialVehicle) fillVehicleReadonly(initialVehicle);
        if (initialVehicle) loadVehicleContacts(initialVehicle.id);

        // ===== Prefill desde el panel de recordatorios =====
        // (?vehicle_id=&plate=&party_id=&party_name=&service_type=&reason=&scheduled_date=&contact_name=&contact_phone=)
        const urlParams = new URLSearchParams(window.location.search);
        const prefillPartyId = urlParams.get('party_id');

        function applyPartyPrefill() {
            if (!prefillPartyId || initialParty) return;
            const name = urlParams.get('party_name') || 'Contacto';
            if (!partySelect.options[prefillPartyId]) {
                partySelect.addOption({ id: prefillPartyId, display_name: name });
            }
            partySelect.setValue(prefillPartyId, true);
            ['contact_name', 'contact_phone', 'contact_email'].forEach(function (field) {
                const v = urlParams.get(field);
                if (v) document.getElementById(field).value = v;
            });
        }

        const prefillVehicleId = urlParams.get('vehicle_id');
        if (prefillVehicleId && !initialVehicle) {
            fetch(`/api/vehicles/search?id=${encodeURIComponent(prefillVehicleId)}&limit=1`)
                .then(r => r.json())
                .then(list => {
                    const v = Array.isArray(list) ? list[0] : null;
                    if (!v) return;
                    vehicleSelect.addOption(v);
                    vehicleSelect.setValue(String(v.id), true);
                    fillVehicleReadonly(v);
                    return loadVehicleContacts(String(v.id));
                })
                .then(applyPartyPrefill)
                .catch(applyPartyPrefill);
        } else {
            applyPartyPrefill();
        }

        if (urlParams.get('service_type')) document.getElementById('service_type').value = urlParams.get('service_type');
        if (urlParams.get('reason')) document.getElementById('reason').value = urlParams.get('reason');
        if (urlParams.get('scheduled_date')) document.getElementById('scheduled_date').value = urlParams.get('scheduled_date');
    } catch (error) {
        console.error('[appointment-form] Error inicializando:', error);
    }
})();
</script>
@endpush


