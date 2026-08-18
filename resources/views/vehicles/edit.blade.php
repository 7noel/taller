<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Editar Vehículo') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="POST" action="{{ route('vehicles.update', $vehicle) }}" id="vehicle-form">
                        @csrf
                        @method('PUT')

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            {{-- Placa --}}
                            <div>
                                <label for="plate" class="block text-sm font-medium text-gray-700">Placa *</label>
                                <input type="text" id="plate" name="plate" value="{{ old('plate', $vehicle->plate) }}" required
                                       placeholder="ABC-123"
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 uppercase">
                                @error('plate') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>

                            {{-- Tipo de carrocería --}}
                            <div>
                                <label for="body_type" class="block text-sm font-medium text-gray-700">Tipo de Carrocería</label>
                                <select id="body_type" name="body_type"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    <option value="">Seleccionar...</option>
                                    <option value="sedan" @selected(old('body_type', $vehicle->body_type) == 'sedan')>Sedán</option>
                                    <option value="suv" @selected(old('body_type', $vehicle->body_type) == 'suv')>SUV</option>
                                    <option value="pickup" @selected(old('body_type', $vehicle->body_type) == 'pickup')>Pickup</option>
                                    <option value="camioneta" @selected(old('body_type', $vehicle->body_type) == 'camioneta')>Camioneta</option>
                                    <option value="camion" @selected(old('body_type', $vehicle->body_type) == 'camion')>Camión</option>
                                    <option value="moto" @selected(old('body_type', $vehicle->body_type) == 'moto')>Moto</option>
                                </select>
                                @error('body_type') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>

                            {{-- Marca --}}
                            <div>
                                <label for="brand" class="block text-sm font-medium text-gray-700">Marca *</label>
                                <input type="text" id="brand" name="brand" value="{{ old('brand', $vehicle->brand) }}" required
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                @error('brand') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>

                            {{-- Modelo --}}
                            <div>
                                <label for="model" class="block text-sm font-medium text-gray-700">Modelo *</label>
                                <input type="text" id="model" name="model" value="{{ old('model', $vehicle->model) }}" required
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                @error('model') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>

                            {{-- Año --}}
                            <div>
                                <label for="year" class="block text-sm font-medium text-gray-700">Año</label>
                                <input type="number" id="year" name="year" value="{{ old('year', $vehicle->year) }}" min="1900" max="{{ date('Y') + 1 }}"
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            </div>

                            {{-- Color --}}
                            <div>
                                <label for="color" class="block text-sm font-medium text-gray-700">Color</label>
                                <input type="text" id="color" name="color" value="{{ old('color', $vehicle->color) }}"
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            </div>

                            {{-- VIN --}}
                            <div>
                                <label for="vin" class="block text-sm font-medium text-gray-700">VIN (Número de Serie)</label>
                                <input type="text" id="vin" name="vin" value="{{ old('vin', $vehicle->vin) }}"
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            </div>

                            {{-- Número de motor --}}
                            <div>
                                <label for="engine_number" class="block text-sm font-medium text-gray-700">Número de Motor</label>
                                <input type="text" id="engine_number" name="engine_number" value="{{ old('engine_number', $vehicle->engine_number) }}"
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            </div>

                            {{-- Fecha próxima revisión técnica --}}
                            <div>
                                <label for="next_technical_review_date" class="block text-sm font-medium text-gray-700">Próxima Revisión Técnica</label>
                                <input type="date" id="next_technical_review_date" name="next_technical_review_date" value="{{ old('next_technical_review_date', $vehicle->next_technical_review_date?->format('Y-m-d')) }}"
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            </div>

                            {{-- Días aviso revisión --}}
                            <div>
                                <label for="technical_review_reminder_days" class="block text-sm font-medium text-gray-700">Días Aviso Revisión</label>
                                <input type="number" id="technical_review_reminder_days" name="technical_review_reminder_days" value="{{ old('technical_review_reminder_days', $vehicle->technical_review_reminder_days) }}" min="1" max="90"
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            </div>

                            {{-- Establecimiento --}}
                            <div>
                                <label for="establishment_id" class="block text-sm font-medium text-gray-700">Establecimiento *</label>
                                <select id="establishment_id" name="establishment_id" required
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    @foreach ($establishments as $est)
                                        <option value="{{ $est->id }}" @selected(old('establishment_id', $vehicle->establishment_id) == $est->id)>{{ $est->name }}</option>
                                    @endforeach
                                </select>
                                @error('establishment_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        {{-- Relaciones (Parties) --}}
                        <div class="mt-8">
                            <div class="flex justify-between items-center mb-4">
                                <h3 class="text-lg font-semibold text-gray-900">Relaciones (Parties)</h3>
                                <div class="flex gap-2">
                                    <button type="button" id="add-relationship"
                                            class="inline-flex items-center px-3 py-1.5 bg-green-600 text-white text-xs font-semibold rounded-md hover:bg-green-700">
                                        + Agregar Relación
                                    </button>
                                    <button type="button" id="open-party-modal"
                                            class="inline-flex items-center px-3 py-1.5 bg-indigo-600 text-white text-xs font-semibold rounded-md hover:bg-indigo-700">
                                        + Nueva Party
                                    </button>
                                </div>
                            </div>
                            <p class="text-sm text-gray-500 mb-4">Relacione parties (propietario, chofer, aprobador, aseguradora, etc.). Solo una puede ser el contacto comercial principal.</p>

                            <div id="relationships-container" class="space-y-4">
                                @foreach($vehicle->relationships as $relationship)
                                    {{-- Se genera cada relación con JS a partir de los datos --}}
                                @endforeach
                            </div>
                        </div>

                        <div class="mt-6 flex gap-2">
                            <button type="submit"
                                    class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                Actualizar
                            </button>
                            <a href="{{ route('vehicles.index') }}"
                               class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                Cancelar
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal: Nueva Party --}}
    <div id="party-modal" class="fixed inset-0 z-50 hidden overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" onclick="closePartyModal()"></div>
            <div class="relative bg-white rounded-lg shadow-xl max-w-lg w-full p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">Crear Nueva Party</h3>
                    <button type="button" onclick="closePartyModal()" class="text-gray-400 hover:text-gray-600">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <div id="party-modal-error" class="hidden mb-4 px-4 py-3 bg-red-100 border border-red-400 text-red-700 rounded text-sm"></div>

                <div class="grid grid-cols-1 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Tipo *</label>
                        <select id="modal-party-type" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="person">Persona</option>
                            <option value="company">Empresa</option>
                        </select>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Tipo Documento *</label>
                            <select id="modal-party-document-type" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="DNI">DNI</option>
                                <option value="RUC">RUC</option>
                                <option value="PAS">Pasaporte</option>
                                <option value="CEX">Carné Extranjería</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Número *</label>
                            <input type="text" id="modal-party-document-number" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>
                    </div>
                    <div id="modal-person-fields" class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Nombres</label>
                            <input type="text" id="modal-party-first-name" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Apellidos</label>
                            <input type="text" id="modal-party-last-name" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>
                    </div>
                    <div id="modal-company-fields" class="hidden">
                        <label class="block text-sm font-medium text-gray-700">Razón Social</label>
                        <input type="text" id="modal-party-business-name" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Email</label>
                        <input type="email" id="modal-party-email" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Celular</label>
                        <input type="text" id="modal-party-mobile" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>
                </div>

                <div class="mt-6 flex justify-end gap-2">
                    <button type="button" onclick="closePartyModal()"
                            class="inline-flex items-center px-4 py-2 bg-gray-200 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300">
                        Cancelar
                    </button>
                    <button type="button" id="save-party-btn"
                            class="inline-flex items-center px-4 py-2 bg-blue-600 rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700">
                        Guardar Party
                    </button>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        const relationshipRoles = [
            { value: 'owner', label: 'Propietario' },
            { value: 'driver', label: 'Chofer' },
            { value: 'approver', label: 'Aprobador' },
            { value: 'operator', label: 'Operador' },
            { value: 'billing', label: 'Facturación' },
            { value: 'insurance_company', label: 'Compañía de Seguros' },
            { value: 'emergency_contact', label: 'Contacto de Emergencia' },
            { value: 'other', label: 'Otro' }
        ];

        let relationshipCount = 0;
        const relationshipsContainer = document.getElementById('relationships-container');

        // ----- Relaciones dinámicas -----
        function addRelationshipRow(party = null, role = '', isPrimary = false, notes = '', shouldFocus = false) {
            relationshipCount++;
            const index = relationshipCount;
            const div = document.createElement('div');
            div.className = 'p-4 border border-gray-200 rounded-lg bg-gray-50 relationship-item';
            div.setAttribute('data-index', index);

            div.innerHTML = `
                <div class="flex justify-between items-center mb-3">
                    <span class="text-sm font-semibold text-gray-700">Relación ${index}</span>
                    <button type="button" class="remove-relationship text-red-600 hover:text-red-800 text-sm">Eliminar</button>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700">Party (buscar o seleccionar) *</label>
                        <select name="relationships[${index}][party_id]" class="relationship-party-select"
                                data-placeholder="Buscar party por nombre o documento..."
                                ${party ? `data-selected-id="${party.id}" data-selected-label="${party.display_name}"` : ''} required>
                            ${party ? `<option value="${party.id}" selected>${party.display_name}</option>` : ''}
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Rol *</label>
                        <select name="relationships[${index}][role]" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
                            <option value="">Seleccionar...</option>
                            ${relationshipRoles.map(r => `<option value="${r.value}" ${role === r.value ? 'selected' : ''}>${r.label}</option>`).join('')}
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Notas</label>
                        <input type="text" name="relationships[${index}][notes]" value="${notes}" placeholder="Opcional"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>
                    <div class="md:col-span-2 flex items-center gap-2">
                        <input type="checkbox" name="relationships[${index}][is_primary_commercial]" value="1" class="primary-commercial-checkbox rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500" ${isPrimary ? 'checked' : ''}>
                        <label class="text-sm font-medium text-gray-700">Contacto comercial principal</label>
                    </div>
                </div>
            `;

            relationshipsContainer.appendChild(div);

            // Tom Select para la party
            new TomSelect(div.querySelector('.relationship-party-select'), {
                valueField: 'id',
                labelField: 'display_name',
                searchField: ['display_name', 'document_number'],
                placeholder: 'Buscar party por nombre o documento...',
                create: false,
                load: function(query, callback) {
                    if (!query.length) return callback();
                    fetch(`{{ route('api.parties.search') }}?q=${encodeURIComponent(query)}&limit=20`)
                        .then(r => r.json())
                        .then(data => callback(data))
                        .catch(() => callback());
                },
                render: {
                    option: function(item, escape) {
                        return `<div>
                            <div class="font-medium">${escape(item.display_name)}</div>
                            <div class="text-xs text-gray-500">${escape(item.document_type)}: ${escape(item.document_number)}</div>
                        </div>`;
                    },
                    item: function(item, escape) {
                        return `<div class="font-medium">${escape(item.display_name)}</div>`;
                    }
                }
            });

            // Checkbox primary comercial: solo uno puede estar marcado
            const checkbox = div.querySelector('.primary-commercial-checkbox');
            checkbox.addEventListener('change', function() {
                if (this.checked) {
                    document.querySelectorAll('.primary-commercial-checkbox').forEach(cb => {
                        if (cb !== this) cb.checked = false;
                    });
                }
            });

            // Eliminar relación
            div.querySelector('.remove-relationship').addEventListener('click', () => {
                div.remove();
                reindexRelationships();
            });
        }

        function reindexRelationships() {
            const items = document.querySelectorAll('#relationships-container .relationship-item');
            items.forEach((item, idx) => {
                const n = idx + 1;
                item.querySelectorAll('select, input[type="text"], input[type="checkbox"]').forEach(field => {
                    const name = field.name;
                    if (name) {
                        field.name = name.replace(/relationships\[\d+\]/, `relationships[${n}]`);
                    }
                });
            });
        }

        // ----- Cargar relaciones existentes -----
        const existingRelationships = @json($vehicle->relationships->map(fn ($rel) => [
            'party_id' => $rel->party_id,
            'display_name' => $rel->party?->display_name,
            'role' => $rel->role,
            'is_primary_commercial' => (bool) $rel->is_primary_commercial,
            'notes' => $rel->notes,
        ]));

        existingRelationships.forEach(rel => {
            addRelationshipRow(
                { id: rel.party_id, display_name: rel.display_name },
                rel.role,
                rel.is_primary_commercial,
                rel.notes || ''
            );
        });

        document.getElementById('add-relationship').addEventListener('click', () => addRelationshipRow());

        // ----- Modal Nueva Party -----
        const partyModal = document.getElementById('party-modal');
        const modalType = document.getElementById('modal-party-type');
        const modalPersonFields = document.getElementById('modal-person-fields');
        const modalCompanyFields = document.getElementById('modal-company-fields');

        function openPartyModal() {
            partyModal.classList.remove('hidden');
            document.getElementById('party-modal-error').classList.add('hidden');
        }

        function closePartyModal() {
            partyModal.classList.add('hidden');
        }

        document.getElementById('open-party-modal').addEventListener('click', openPartyModal);

        modalType.addEventListener('change', function() {
            const isPerson = this.value === 'person';
            modalPersonFields.classList.toggle('hidden', !isPerson);
            modalCompanyFields.classList.toggle('hidden', isPerson);
        });

        document.getElementById('save-party-btn').addEventListener('click', function() {
            const type = modalType.value;
            const data = {
                type: type,
                document_type: document.getElementById('modal-party-document-type').value,
                document_number: document.getElementById('modal-party-document-number').value,
                first_name: document.getElementById('modal-party-first-name').value,
                last_name: document.getElementById('modal-party-last-name').value,
                business_name: document.getElementById('modal-party-business-name').value,
                email: document.getElementById('modal-party-email').value,
                mobile: document.getElementById('modal-party-mobile').value
            };

            fetch(`{{ route('api.parties.quick-store') }}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                },
                body: JSON.stringify(data)
            })
            .then(response => response.json().then(json => ({ ok: response.ok, json })))
            .then(({ ok, json }) => {
                if (!ok) {
                    const errorBox = document.getElementById('party-modal-error');
                    errorBox.innerHTML = json.errors ? Object.values(json.errors).flat().join('<br>') : (json.message || 'Error al crear la party.');
                    errorBox.classList.remove('hidden');
                    return;
                }
                addRelationshipRow({ id: json.id, display_name: json.display_name }, '', false, '');
                closePartyModal();
                document.getElementById('modal-party-document-number').value = '';
                document.getElementById('modal-party-first-name').value = '';
                document.getElementById('modal-party-last-name').value = '';
                document.getElementById('modal-party-business-name').value = '';
                document.getElementById('modal-party-email').value = '';
                document.getElementById('modal-party-mobile').value = '';
            })
            .catch(() => {
                document.getElementById('party-modal-error').textContent = 'Error de conexión al crear la party.';
                document.getElementById('party-modal-error').classList.remove('hidden');
            });
        });
    </script>
    @endpush
</x-app-layout>