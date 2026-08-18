<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Nuevo Vehículo') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="POST" action="{{ route('vehicles.store') }}" id="vehicle-form">
                        @csrf

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            {{-- Cliente (Tom Select) --}}
                            <div class="md:col-span-2">
                                <label for="client_id" class="block text-sm font-medium text-gray-700">Cliente *</label>
                                <select id="client_id" name="client_id" required
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    <option value="">Buscar cliente...</option>
                                </select>
                                @error('client_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                                <p class="mt-1 text-xs text-gray-500">
                                    <a href="{{ route('clients.create') }}" class="text-blue-600 hover:underline">+ Crear nuevo cliente</a>
                                </p>
                            </div>

                            {{-- Placa --}}
                            <div>
                                <label for="plate" class="block text-sm font-medium text-gray-700">Placa *</label>
                                <input type="text" id="plate" name="plate" value="{{ old('plate') }}" required
                                       placeholder="ABC-123"
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 uppercase">
                                @error('plate') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>

                            {{-- Tipo de carrocería --}}
                            <div>
                                <label for="body_type" class="block text-sm font-medium text-gray-700">Tipo de Carrocería *</label>
                                <select id="body_type" name="body_type" required
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    <option value="">Seleccionar...</option>
                                    <option value="sedan" @selected(old('body_type') == 'sedan')>Sedán</option>
                                    <option value="suv" @selected(old('body_type') == 'suv')>SUV</option>
                                    <option value="pickup" @selected(old('body_type') == 'pickup')>Pickup</option>
                                    <option value="camioneta" @selected(old('body_type') == 'camioneta')>Camioneta</option>
                                    <option value="camion" @selected(old('body_type') == 'camion')>Camión</option>
                                    <option value="moto" @selected(old('body_type') == 'moto')>Moto</option>
                                </select>
                                @error('body_type') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>

                            {{-- Marca --}}
                            <div>
                                <label for="brand" class="block text-sm font-medium text-gray-700">Marca *</label>
                                <input type="text" id="brand" name="brand" value="{{ old('brand') }}" required
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                @error('brand') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>

                            {{-- Modelo --}}
                            <div>
                                <label for="model" class="block text-sm font-medium text-gray-700">Modelo *</label>
                                <input type="text" id="model" name="model" value="{{ old('model') }}" required
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                @error('model') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>

                            {{-- Año --}}
                            <div>
                                <label for="year" class="block text-sm font-medium text-gray-700">Año</label>
                                <input type="number" id="year" name="year" value="{{ old('year') }}" min="1900" max="{{ date('Y') + 1 }}"
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            </div>

                            {{-- Color --}}
                            <div>
                                <label for="color" class="block text-sm font-medium text-gray-700">Color</label>
                                <input type="text" id="color" name="color" value="{{ old('color') }}"
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            </div>

                            {{-- VIN --}}
                            <div>
                                <label for="vin" class="block text-sm font-medium text-gray-700">VIN (Número de Serie)</label>
                                <input type="text" id="vin" name="vin" value="{{ old('vin') }}"
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            </div>

                            {{-- Número de motor --}}
                            <div>
                                <label for="engine_number" class="block text-sm font-medium text-gray-700">Número de Motor</label>
                                <input type="text" id="engine_number" name="engine_number" value="{{ old('engine_number') }}"
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            </div>

                            {{-- Establecimiento --}}
                            <div>
                                <label for="establishment_id" class="block text-sm font-medium text-gray-700">Establecimiento *</label>
                                <select id="establishment_id" name="establishment_id" required
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    @foreach ($establishments as $est)
                                        <option value="{{ $est->id }}" @selected(old('establishment_id', 1) == $est->id)>{{ $est->name }}</option>
                                    @endforeach
                                </select>
                                @error('establishment_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        {{-- Contactos --}}
                        <div class="mt-8">
                            <div class="flex justify-between items-center mb-4">
                                <h3 class="text-lg font-semibold text-gray-900">Contactos del Vehículo</h3>
                                <button type="button" id="add-contact"
                                        class="inline-flex items-center px-3 py-1.5 bg-green-600 text-white text-xs font-semibold rounded-md hover:bg-green-700">
                                    + Agregar Contacto
                                </button>
                            </div>
                            <p class="text-sm text-gray-500 mb-4">Máximo 3 contactos (Aprobador, Chofer, Operador).</p>

                            <div id="contacts-container" class="space-y-4"></div>
                        </div>

                        <div class="mt-6 flex gap-2">
                            <button type="submit"
                                    class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                Guardar
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

    @push('scripts')
    <script>
        // Tom Select para seleccionar cliente
        new TomSelect('#client_id', {
            valueField: 'id',
            labelField: 'business_name',
            searchField: ['business_name', 'document_number'],
            placeholder: 'Buscar cliente por nombre o documento...',
            load: function(query, callback) {
                if (!query.length) return callback();
                fetch(`{{ route('api.clients.search') }}?q=${encodeURIComponent(query)}&limit=20`)
                    .then(r => r.json())
                    .then(data => callback(data))
                    .catch(() => callback());
            },
            render: {
                option: function(item, escape) {
                    return `<div>
                        <div class="font-medium">${escape(item.business_name)}</div>
                        <div class="text-xs text-gray-500">${escape(item.document_type)}: ${escape(item.document_number)}</div>
                    </div>`;
                },
                item: function(item, escape) {
                    return `<div class="font-medium">${escape(item.business_name)}</div>`;
                }
            }
        });

        // Contactos dinámicos
        let contactCount = 0;
        const maxContacts = 3;
        const contactsContainer = document.getElementById('contacts-container');
        const addContactBtn = document.getElementById('add-contact');

        const contactTypes = [
            { value: 'approver', label: 'Aprobador' },
            { value: 'driver', label: 'Chofer' },
            { value: 'operator', label: 'Operador' }
        ];

        function renderContacts() {
            addContactBtn.classList.toggle('hidden', contactCount >= maxContacts);
        }

        addContactBtn.addEventListener('click', () => {
            if (contactCount >= maxContacts) return;
            contactCount++;

            const index = contactCount - 1;
            const div = document.createElement('div');
            div.className = 'p-4 border border-gray-200 rounded-lg bg-gray-50';
            div.innerHTML = `
                <div class="flex justify-between items-center mb-3">
                    <span class="text-sm font-semibold text-gray-700">Contacto #${contactCount}</span>
                    <button type="button" class="remove-contact text-red-600 hover:text-red-800 text-sm">Eliminar</button>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Tipo *</label>
                        <select name="contacts[${index}][type]" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="">Seleccionar...</option>
                            ${contactTypes.map(t => `<option value="${t.value}">${t.label}</option>`).join('')}
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Nombre *</label>
                        <input type="text" name="contacts[${index}][name]" required
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Teléfono</label>
                        <input type="text" name="contacts[${index}][phone]"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Email</label>
                        <input type="email" name="contacts[${index}][email]"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>
                    <div class="company-field hidden">
                        <label class="block text-sm font-medium text-gray-700">Empresa (solo operador)</label>
                        <input type="text" name="contacts[${index}][company_name]"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>
                </div>
            `;

            // Mostrar campo empresa solo si el tipo es operador
            const typeSelect = div.querySelector('select[name="contacts[' + index + '][type]"]');
            const companyField = div.querySelector('.company-field');

            typeSelect.addEventListener('change', () => {
                companyField.classList.toggle('hidden', typeSelect.value !== 'operator');
                if (typeSelect.value !== 'operator') {
                    companyField.querySelector('input').value = '';
                }
            });

            div.querySelector('.remove-contact').addEventListener('click', () => {
                div.remove();
                contactCount--;
                renderContacts();
            });

            contactsContainer.appendChild(div);
            renderContacts();
        });

        renderContacts();
    </script>
    @endpush
</x-app-layout>