@php
    $checkIn = $checkIn ?? null;
    $isEdit = !is_null($checkIn);
@endphp

{{-- ============ SECCIÓN 1: VEHÍCULO ============ --}}
<div class="border-b border-gray-200 pb-6 mb-6">
    <h3 class="text-lg font-semibold text-gray-800 mb-4">Vehículo</h3>

    {{-- El propietario vive en la sección de contactos; aquí solo se guarda el client_id oculto --}}
    <input type="hidden" id="owner_id" name="client_id" value="{{ old('client_id', $checkIn->client_id ?? '') }}">

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div>
            <div class="flex items-center justify-between gap-3">
                <label for="vehicle_id" class="block text-sm font-medium text-gray-700">Buscar vehículo por placa *</label>
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
        <div>
            <label class="block text-sm font-medium text-gray-700">VIN</label>
            <input type="text" id="vehicle_vin" readonly class="mt-1 block w-full rounded-md border-gray-300 bg-gray-50 shadow-sm" placeholder="--">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700">Carrocería</label>
            <input type="text" id="vehicle_body_type" readonly class="mt-1 block w-full rounded-md border-gray-300 bg-gray-50 shadow-sm" placeholder="--">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700">Próxima Revisión Técnica</label>
            <input type="date" id="vehicle_technical_review_date" readonly class="mt-1 block w-full rounded-md border-gray-300 bg-gray-50 shadow-sm">
        </div>
    </div>

    {{-- Indicador de cita del vehículo (hoy → se asociará; otro día → no) --}}
    <div id="appointment-banner" class="hidden mt-3"></div>
</div>

{{-- ============ SECCIÓN 2: CONTACTOS DEL VEHÍCULO ============ --}}
<div class="border-b border-gray-200 pb-6 mb-6">
    @php
        // Filas iniciales para el check-in: propietario + relaciones relevantes del vehículo.
        if (old('relationships')) {
            $ciRelationshipRows = collect(old('relationships'))->map(function ($rel) {
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
            $ciRelationshipRows = isset($checkIn) && $checkIn->vehicle && $checkIn->vehicle->relationships
                ? $checkIn->vehicle->relationships
                    ->filter(fn ($rel) => in_array($rel->role, ['owner', 'approver', 'driver', 'operator'], true))
                    ->map(function ($rel) {
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

    @include('partials.vehicle-relationships', [
        'vrPrefix' => 'ci',
        'vrSubmitName' => 'relationships',
        'vrRoles' => ['owner', 'approver', 'driver', 'operator'],
        'vrShowPrimary' => true,
        'vrInitialRows' => $ciRelationshipRows,
        'vrTitle' => 'Contactos del vehículo',
        'vrDescription' => 'Se cargan automáticamente desde las relaciones del vehículo. Puedes agregar, editar o quitar contactos.',
    ])
</div>

{{-- ============ SECCIÓN 3: DATOS DE INGRESO ============ --}}
<div class="border-b border-gray-200 pb-6 mb-6">
    <h3 class="text-lg font-semibold text-gray-800 mb-4">Datos de ingreso</h3>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div>
            <label for="service_type" class="block text-sm font-medium text-gray-700">Servicio *</label>
            <select id="service_type" name="service_type" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                @foreach (\App\Models\CheckIn::SERVICE_TYPES as $value => $label)
                    <option value="{{ $value }}" {{ old('service_type', $checkIn->service_type ?? '') === $value ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
            @error('service_type') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>

        <div id="claim-number-wrap" class="{{ old('service_type', $checkIn->service_type ?? '') === 'siniestro' ? '' : 'hidden' }}">
            <label class="block text-sm font-medium text-gray-700">Nº Siniestro</label>
            <input type="text" name="claim_number" value="{{ old('claim_number', $checkIn->claim_number ?? '') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 uppercase" maxlength="100">
        </div>

        <div>
            <label for="insurance_company_id" class="block text-sm font-medium text-gray-700">Aseguradora</label>
            <select id="insurance_company_id" name="insurance_company_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"></select>
            @error('insurance_company_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700">Kilometraje</label>
            <input type="number" name="mileage" value="{{ old('mileage', $checkIn->mileage ?? '') }}" min="0" max="9999999" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700">Combustible</label>
            <select name="fuel_level" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                <option value="">Seleccionar...</option>
                @foreach (\App\Models\CheckIn::FUEL_LEVELS as $value => $label)
                    <option value="{{ $value }}" {{ old('fuel_level', $checkIn->fuel_level ?? '') === $value ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700">Tarjeta de Propiedad</label>
            <select name="property_card" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                <option value="">Seleccionar...</option>
                @foreach (\App\Models\CheckIn::PROPERTY_CARDS as $value => $label)
                    <option value="{{ $value }}" {{ old('property_card', $checkIn->property_card ?? '') === $value ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700">Fecha SOAT</label>
            <input type="date" name="soat_expiration" value="{{ old('soat_expiration', $checkIn?->soat_expiration?->format('Y-m-d') ?? '') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700">Revisión Técnica</label>
            <input type="date" name="technical_review_expiration" value="{{ old('technical_review_expiration', $checkIn?->technical_review_expiration?->format('Y-m-d') ?? '') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700">Llaves</label>
            <input type="number" name="keys_count" value="{{ old('keys_count', $checkIn->keys_count ?? 0) }}" min="0" max="10" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
        </div>

        <div class="flex items-end pb-1">
            <label class="inline-flex items-center gap-2 text-sm font-medium text-gray-700 cursor-pointer select-none">
                <input type="checkbox" name="has_remote_control" value="1" class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500"
                       {{ old('has_remote_control', $checkIn->has_remote_control ?? false) ? 'checked' : '' }}>
                Control remoto
            </label>
        </div>
    </div>

    <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-medium text-gray-700">Solicitud del cliente</label>
            <textarea name="client_request" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('client_request', $checkIn->client_request ?? '') }}</textarea>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700">Comentarios / Observaciones</label>
            <textarea name="observations" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('observations', $checkIn->observations ?? '') }}</textarea>
        </div>
    </div>
</div>

{{-- ============ SECCIONES 4-6: CHECKLIST, DAÑOS, FOTOS ============ --}}
@include('check-ins._checklist', ['checkIn' => $checkIn, 'checklistItems' => $checklistItems, 'isEdit' => $isEdit])
@include('check-ins._damages', ['checkIn' => $checkIn, 'isEdit' => $isEdit])
@include('check-ins._photos', ['checkIn' => $checkIn, 'isEdit' => $isEdit])

@include('check-ins._vehicle_modal')
@include('partials.contact-modal')

@include('check-ins._form-scripts', ['checkIn' => $checkIn, 'isEdit' => $isEdit])
