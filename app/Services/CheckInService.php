<?php

namespace App\Services;

use App\Models\CheckIn;
use App\Models\CheckInChecklistResult;
use App\Models\CheckInDamage;
use App\Models\Party;
use App\Models\VehicleRelationship;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class CheckInService
{
    /**
     * Crea un inventario con checklist, daños y contactos (transaccional).
     */
    public function create(array $data): CheckIn
    {
        $data['created_by'] = Auth::id();
        $data['updated_by'] = Auth::id();
        $data['establishment_id'] = $data['establishment_id'] ?? Auth::user()?->establishment_id;
        $data['status'] = $data['status'] ?? 'draft';

        $checkIn = DB::transaction(function () use ($data) {
            $checkIn = CheckIn::create($data);

            $this->syncChecklist($checkIn, $data['checklist'] ?? []);
            $this->syncDamages($checkIn, $data['damages'] ?? []);
            $this->syncContacts($checkIn, $data);

            return $checkIn;
        });

        return $checkIn->load(['vehicle.vehicleModel.brand', 'client', 'insuranceCompany', 'checklistResults.checklistItem', 'damages', 'photos']);
    }

    /**
     * Actualiza un inventario (transaccional).
     */
    public function update(CheckIn $checkIn, array $data): CheckIn
    {
        $data['updated_by'] = Auth::id();

        DB::transaction(function () use ($checkIn, $data) {
            $checkIn->update($data);

            $this->syncChecklist($checkIn, $data['checklist'] ?? []);
            $this->syncDamages($checkIn, $data['damages'] ?? []);
            $this->syncContacts($checkIn, $data);
        });

        return $checkIn->load(['vehicle.vehicleModel.brand', 'client', 'insuranceCompany', 'checklistResults.checklistItem', 'damages', 'photos']);
    }

    /**
     * Elimina (soft delete) un inventario.
     */
    public function delete(CheckIn $checkIn): bool
    {
        return $checkIn->delete();
    }

    /**
     * Envía a aprobación del cliente.
     */
    public function sendToClient(CheckIn $checkIn): CheckIn
    {
        $checkIn->update([
            'status' => 'pending_approval',
            'updated_by' => Auth::id(),
        ]);

        return $checkIn->fresh();
    }

    /**
     * Aprueba el inventario.
     */
    public function approve(CheckIn $checkIn): CheckIn
    {
        $checkIn->update([
            'status' => 'approved',
            'updated_by' => Auth::id(),
        ]);

        return $checkIn->fresh();
    }

    /**
     * Rechaza el inventario.
     */
    public function reject(CheckIn $checkIn, ?string $reason = null): CheckIn
    {
        $checkIn->update([
            'status' => 'rejected',
            'updated_by' => Auth::id(),
            'observations' => $reason
                ? trim($checkIn->observations . "\n" . 'Rechazo: ' . $reason)
                : $checkIn->observations,
        ]);

        return $checkIn->fresh();
    }

    /**
     * Sube una foto y crea el registro CheckInPhoto.
     */
    public function addPhoto(CheckIn $checkIn, $file, ?string $description = null): \App\Models\CheckInPhoto
    {
        $path = $file->store('check-in-photos', 'public');

        $maxOrder = $checkIn->photos()->max('order') ?? 0;

        return $checkIn->photos()->create([
            'path' => $path,
            'order' => $maxOrder + 1,
            'description' => $description,
        ]);
    }

    /**
     * Elimina una foto del inventario (archivo + BD).
     */
    public function removePhoto(CheckIn $checkIn, int $photoId): bool
    {
        $photo = $checkIn->photos()->findOrFail($photoId);

        Storage::disk('public')->delete($photo->path);

        return $photo->delete();
    }

    /**
     * Sincroniza los resultados del checklist.
     */
    protected function syncChecklist(CheckIn $checkIn, array $checklist): void
    {
        $checkIn->checklistResults()->delete();

        foreach ($checklist as $itemId => $result) {
            if (empty($itemId)) {
                continue;
            }

            $status = $result['status'] ?? null;
            $observations = $result['observations'] ?? null;

            if (!$status && !$observations) {
                continue; // fila vacía
            }

            CheckInChecklistResult::create([
                'check_in_id' => $checkIn->id,
                'checklist_item_id' => $itemId,
                'status' => $status ?: null,
                'observations' => $observations ?: null,
            ]);
        }
    }

    /**
     * Sincroniza los daños del inventario.
     */
    protected function syncDamages(CheckIn $checkIn, array $damages): void
    {
        $checkIn->damages()->delete();

        foreach ($damages as $damage) {
            if (empty($damage['damage_type'])) {
                continue;
            }

            CheckInDamage::create([
                'check_in_id' => $checkIn->id,
                'damage_type' => $damage['damage_type'],
                // El lado se oculta en la UI; se mantiene en BD con default 'front' para uso futuro
                'side' => $damage['side'] ?? 'front',
                'pos_x' => $damage['pos_x'] ?? null,
                'pos_y' => $damage['pos_y'] ?? null,
                'notes' => $damage['notes'] ?? null,
            ]);
        }
    }

    /**
     * Guarda contactos como vehicle_relationships si el usuario lo pidió.
     */
    protected function syncContacts(CheckIn $checkIn, array $data): void
    {
        $saveContacts = !empty($data['save_contacts']);
        $contacts = $data['contacts'] ?? [];

        if (!$saveContacts || empty($checkIn->vehicle_id) || empty($contacts)) {
            return;
        }

        $vehicleId = $checkIn->vehicle_id;
        $types = [
            'approver' => ['name', 'phone', 'email'],
            'driver' => ['name', 'phone', 'email'],
            'operator' => ['company', 'name', 'phone', 'email'],
        ];

        foreach ($types as $role => $fields) {
            $contact = $contacts[$role] ?? [];

            // Determinar si hay datos suficientes para crear/actualizar la relación
            $hasData = false;
            foreach ($fields as $field) {
                if (!empty($contact[$field])) {
                    $hasData = true;
                    break;
                }
            }

            if (!$hasData) {
                continue;
            }

            // Buscar una party existente con ese nombre/correo para vincularla
            $party = $this->findOrCreateContactParty($role, $contact);

            if ($party) {
                VehicleRelationship::updateOrCreate(
                    [
                        'vehicle_id' => $vehicleId,
                        'role' => $role,
                        'party_id' => $party->id,
                    ],
                    [
                        'notes' => $contact['notes'] ?? null,
                        'created_by' => Auth::id(),
                        'updated_by' => Auth::id(),
                    ]
                );
            }
        }
    }

    /**
     * Busca una party por nombre/correo o la crea si no existe.
     */
    protected function findOrCreateContactParty(string $role, array $contact): ?Party
    {
        $name = trim($contact['name'] ?? '');
        $company = trim($contact['company'] ?? '');
        $email = trim($contact['email'] ?? '');
        $phone = trim($contact['phone'] ?? '');

        // Para operador: usar la empresa como business_name
        $isOperator = $role === 'operator';
        $displayName = $isOperator ? ($company ?: $name) : $name;

        if ($displayName === '') {
            return null;
        }

        // Buscar por email o nombre exacto
        $query = Party::query();
        if ($email) {
            $query->where('email', $email);
        } else {
            $query->where(function ($q) use ($displayName, $isOperator) {
                if ($isOperator) {
                    $q->where('business_name', $displayName);
                } else {
                    $q->where('first_name', $displayName)
                        ->orWhere('last_name', $displayName);
                }
            });
        }

        $party = $query->first();
        if ($party) {
            return $party;
        }

        // Crear la party con el tipo documento default (DNI)
        return Party::create([
            'document_type' => '1',
            'document_number' => $this->generateTemporaryDocumentNumber(),
            'first_name' => $isOperator ? null : $name,
            'last_name' => $isOperator ? null : null,
            'business_name' => $isOperator ? $displayName : null,
            'email' => $email ?: null,
            'phone' => $phone ?: null,
            'mobile' => $phone ?: null,
            'created_by' => Auth::id(),
            'updated_by' => Auth::id(),
        ]);
    }

    /**
     * Genera un número de documento temporal para contactos sin documento.
     * Formato: TMP + timestamp compacto.
     */
    protected function generateTemporaryDocumentNumber(): string
    {
        return 'TMP' . now()->format('YmdHis') . random_int(10, 99);
    }
}