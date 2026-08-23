{{-- Contactos del vehículo: reutiliza el componente compartido. El modal ContactModal
     se incluye una sola vez en vehicles/create y vehicles/edit. --}}

@php
    if (old('relationships')) {
        $vrInitialRows = collect(old('relationships'))->map(function ($rel) {
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
        $vrInitialRows = isset($vehicle) && $vehicle->relationships
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

@include('partials.vehicle-relationships', [
    'vrPrefix' => 'rel',
    'vrSubmitName' => 'relationships',
    'vrRoles' => array_keys(\App\Models\VehicleRelationship::roleLabels()),
    'vrShowPrimary' => true,
    'vrInitialRows' => $vrInitialRows,
    'vrTitle' => 'Contactos del vehículo',
    'vrDescription' => 'Busca un contacto existente o registra uno nuevo. Los campos cambian según el rol.',
])