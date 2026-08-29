<?php

namespace App\Services;

use App\Models\CheckIn;
use App\Models\CheckInChecklistResult;
use App\Models\CheckInDamage;
use App\Models\Party;
use App\Models\PublicApprovalLog;
use App\Models\VehicleRelationship;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use RuntimeException;

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

        if (empty($data['document_number'])) {
            $this->assignDocumentNumber($data['establishment_id'], $data);
        }

        $checkIn = DB::transaction(function () use ($data) {
            $checkIn = CheckIn::create($data);

            $this->syncChecklist($checkIn, $data['checklist'] ?? []);
            $this->syncDamages($checkIn, $data['damages'] ?? []);
            $this->syncContacts($checkIn, $data);

            return $checkIn;
        });

        return $checkIn->load(['vehicle.vehicleModel.brand', 'client', 'insuranceCompany', 'documentSeries.documentType', 'checklistResults.checklistItem', 'damages', 'photos']);
    }

    /**
     * Asigna la serie IV01 y el siguiente número de documento al inventario.
     */
    protected function assignDocumentNumber(int $establishmentId, array &$data): void
    {
        $result = app(DocumentSeriesService::class)->getNextNumber($establishmentId, 'IV');

        $data['document_series_id'] = $result['series']->id;

        if ($result['number'] === null) {
            throw new RuntimeException('La serie IV01 usa numeración por API. Configure la numeración local o asigne el número manualmente.');
        }

        $data['document_type_code'] = $result['document_type_code'];
        $data['document_serie'] = $result['series']->prefix_serie;
        $data['document_number'] = $result['number'];
        $data['document_sn'] = $result['sn'];
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
        $from = $checkIn->status;

        $checkIn->update([
            'status' => 'pending_approval',
            'updated_by' => Auth::id(),
        ]);

        $checkIn->recordStatusChange('pending_approval', $from);

        return $checkIn->fresh();
    }

    /**
     * Aprueba el inventario (usuario interno: asesor o administrador).
     * Registra quién aprobó en approved_by_user_id y en el log de auditoría.
     */
    public function approve(CheckIn $checkIn): CheckIn
    {
        DB::transaction(function () use ($checkIn) {
            $from = $checkIn->status;

            $checkIn->update([
                'status' => 'approved',
                'approved_by_user_id' => Auth::id(),
                'approved_at' => now(),
                'updated_by' => Auth::id(),
            ]);

            $checkIn->recordStatusChange('approved', $from);
            $this->logApproval($checkIn, 'approved', 'internal', Auth::id());
        });

        return $checkIn->fresh();
    }

    /**
     * Rechaza el inventario (usuario interno).
     */
    public function reject(CheckIn $checkIn, ?string $reason = null): CheckIn
    {
        DB::transaction(function () use ($checkIn, $reason) {
            $from = $checkIn->status;

            $checkIn->update([
                'status' => 'rejected',
                'rejected_by_user_id' => Auth::id(),
                'rejected_at' => now(),
                'rejection_reason' => $reason ?: $checkIn->rejection_reason,
                'observations' => $reason
                    ? trim($checkIn->observations . "\n" . 'Rechazo: ' . $reason)
                    : $checkIn->observations,
                'updated_by' => Auth::id(),
            ]);

            $checkIn->recordStatusChange('rejected', $from, $reason);
            $this->logApproval($checkIn, 'rejected', 'internal', Auth::id(), $reason);
        });

        return $checkIn->fresh();
    }

    /**
     * Cierra el inventario: el vehículo salió del taller (estado terminal).
     * Se usa como acción manual (escape hatch) o automáticamente cuando el
     * presupuesto asociado se finaliza.
     */
    public function close(CheckIn $checkIn): CheckIn
    {
        if (! in_array($checkIn->status, ['approved', 'pending_approval'], true)) {
            throw new RuntimeException('Solo se puede cerrar un inventario aprobado o pendiente de aprobación.');
        }

        $from = $checkIn->status;

        $checkIn->update([
            'status' => 'closed',
            'closed_by' => Auth::id(),
            'closed_at' => now(),
            'updated_by' => Auth::id(),
        ]);

        $checkIn->recordStatusChange('closed', $from, null, 'internal');

        return $checkIn->fresh();
    }

    /**
     * Aprueba el inventario desde el portal del cliente.
     *
     * El snapshot del destinatario (a quién se le envió el enlace por última vez)
     * ya quedó grabado en last_sent_to / last_sent_to_phone al momento del envío
     * y se copia aquí como responsable de la aprobación.
     */
    public function approveByClient(CheckIn $checkIn, string $ip = '', string $userAgent = ''): CheckIn
    {
        if ($checkIn->status !== 'pending_approval') {
            throw new RuntimeException('Este inventario ya no está pendiente de aprobación.');
        }

        DB::transaction(function () use ($checkIn, $ip, $userAgent) {
            $from = $checkIn->status;
            $recipient = $checkIn->last_sent_to ?: $checkIn->client?->display_name;
            $phone = $checkIn->last_sent_to_phone;

            $checkIn->update([
                'status' => 'approved',
                'approved_by_recipient' => $recipient,
                'approved_by_phone' => $phone,
                'approved_at' => now(),
            ]);

            $checkIn->recordStatusChange('approved', $from, null, 'client', null);
            $this->logApproval($checkIn, 'approved', 'portal', null, null, $recipient, $phone, $ip, $userAgent);
        });

        return $checkIn->fresh();
    }

    /**
     * Rechaza el inventario desde el portal del cliente (el motivo es obligatorio).
     */
    public function rejectByClient(CheckIn $checkIn, string $reason, string $ip = '', string $userAgent = ''): CheckIn
    {
        if ($checkIn->status !== 'pending_approval') {
            throw new RuntimeException('Este inventario ya no está pendiente de aprobación.');
        }

        DB::transaction(function () use ($checkIn, $reason, $ip, $userAgent) {
            $from = $checkIn->status;
            $recipient = $checkIn->last_sent_to ?: $checkIn->client?->display_name;
            $phone = $checkIn->last_sent_to_phone;

            $checkIn->update([
                'status' => 'rejected',
                'rejected_by_recipient' => $recipient,
                'rejected_by_phone' => $phone,
                'rejection_reason' => $reason,
                'rejected_at' => now(),
            ]);

            $checkIn->recordStatusChange('rejected', $from, $reason, 'client', null);
            $this->logApproval($checkIn, 'rejected', 'portal', null, $reason, $recipient, $phone, $ip, $userAgent);
        });

        return $checkIn->fresh();
    }

    /**
     * Registra la aprobación/rechazo (interno o portal) en public_approval_logs.
     */
    protected function logApproval(
        CheckIn $checkIn,
        string $action,
        string $actorType,
        ?int $userId = null,
        ?string $reason = null,
        ?string $recipient = null,
        ?string $phone = null,
        string $ip = '',
        string $userAgent = ''
    ): void {
        PublicApprovalLog::create([
            'vehicle_id' => $checkIn->vehicle_id,
            'action' => $action,
            'entity_type' => 'check_in',
            'entity_id' => $checkIn->id,
            'actor_type' => $actorType,
            'actor_user_id' => $userId,
            'actor_recipient' => $recipient,
            'actor_phone' => $phone,
            'reason' => $reason,
            'ip_address' => $ip ?: null,
            'user_agent' => $userAgent ?: null,
        ]);
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
     * Sincroniza los resultados del checklist usando diff/upsert:
     * crea los nuevos, actualiza los modificados y elimina solo los que ya no vienen.
     */
    protected function syncChecklist(CheckIn $checkIn, array $checklist): void
    {
        $existing = $checkIn->checklistResults()->get()->keyBy('checklist_item_id');

        foreach ($checklist as $itemId => $result) {
            if (empty($itemId)) {
                continue;
            }

            $status = $result['status'] ?? null;
            $observations = $result['observations'] ?? null;

            if (!$status && !$observations) {
                // Fila vacía: conservar en BD nada; eliminar el registro previo si existía
                if (isset($existing[$itemId])) {
                    $existing[$itemId]->delete();
                    $existing->forget($itemId);
                }
                continue;
            }

            if (isset($existing[$itemId])) {
                $existing[$itemId]->update([
                    'status' => $status ?: null,
                    'observations' => $observations ?: null,
                ]);
                $existing->forget($itemId);
            } else {
                CheckInChecklistResult::create([
                    'check_in_id' => $checkIn->id,
                    'checklist_item_id' => $itemId,
                    'status' => $status ?: null,
                    'observations' => $observations ?: null,
                ]);
            }
        }

        // Eliminar los registros que ya no vienen en el request
        foreach ($existing as $result) {
            $result->delete();
        }
    }

    /**
     * Sincroniza los daños del inventario usando diff/upsert:
     * crea los nuevos, actualiza los modificados y elimina solo los que ya no vienen.
     */
    protected function syncDamages(CheckIn $checkIn, array $damages): void
    {
        $existing = $checkIn->damages()->get()->keyBy('id');

        foreach ($damages as $damage) {
            if (empty($damage['damage_type'])) {
                continue;
            }

            $id = $damage['id'] ?? null;

            if ($id && isset($existing[$id])) {
                $existing[$id]->update([
                    'damage_type' => $damage['damage_type'],
                    // El lado se oculta en la UI; se mantiene en BD con default 'front' para uso futuro
                    'side' => $damage['side'] ?? 'front',
                    'pos_x' => $damage['pos_x'] ?? null,
                    'pos_y' => $damage['pos_y'] ?? null,
                    'notes' => $damage['notes'] ?? null,
                ]);
                $existing->forget($id);
            } else {
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

        // Eliminar los daños que ya no vienen en el request
        foreach ($existing as $damage) {
            $damage->delete();
        }
    }

    /**
     * Guarda contactos como vehicle_relationships si el usuario lo pidió.
     *
     * Recibe las relaciones ya elegidas por el usuario (party_id + role),
     * en lugar de adivinar por nombre/email. Esto evita contactos duplicados
     * y documentos temporales basura en la agenda.
     */
    protected function syncContacts(CheckIn $checkIn, array $data): void
    {
        $relationships = $data['relationships'] ?? [];

        if (empty($checkIn->vehicle_id) || empty($relationships)) {
            return;
        }

        $vehicleId = $checkIn->vehicle_id;
        $roles = ['owner', 'approver', 'driver', 'operator'];

        foreach ($relationships as $relationship) {
            $role = $relationship['role'] ?? null;
            $partyId = $relationship['party_id'] ?? null;

            if (!in_array($role, $roles, true) || empty($partyId)) {
                continue;
            }

            VehicleRelationship::updateOrCreate(
                [
                    'vehicle_id' => $vehicleId,
                    'role' => $role,
                    'party_id' => $partyId,
                ],
                [
                    'notes' => $relationship['notes'] ?? null,
                    'is_primary_commercial' => !empty($relationship['is_primary_commercial']),
                    'created_by' => Auth::id(),
                    'updated_by' => Auth::id(),
                ]
            );
        }
    }
}
