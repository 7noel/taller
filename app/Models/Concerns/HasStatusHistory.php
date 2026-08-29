<?php

namespace App\Models\Concerns;

use Illuminate\Support\Facades\Auth;

/**
 * Trazabilidad estandarizada de transiciones de estado.
 *
 * Añade la relación polimórfica `statusHistory()` y el helper
 * `recordStatusChange()` a cualquier modelo con columna `status`.
 * Toda transición queda registrada con: estado origen → estado destino,
 * autor (usuario o null si actuó el cliente), tipo de actor
 * (internal | client | system) y comentario opcional.
 */
trait HasStatusHistory
{
    public function statusHistory()
    {
        return $this->morphMany(\App\Models\StatusHistory::class, 'subject')->orderBy('created_at');
    }

    /**
     * Registra un cambio de estado en status_histories.
     *
     * @param  string  $toStatus   Estado destino.
     * @param  string|null  $fromStatus  Estado origen. Si es null usa el valor
     *                                   original del modelo (útil al crear).
     * @param  string|null  $comments    Motivo/observación de la transición.
     * @param  string  $actorType  internal | client | system.
     * @param  int|null  $userId    Autor (null → Auth::id(), o null real en portal).
     */
    public function recordStatusChange(
        string $toStatus,
        ?string $fromStatus = null,
        ?string $comments = null,
        string $actorType = 'internal',
        ?int $userId = null
    ) {
        return $this->statusHistory()->create([
            'from_status' => $fromStatus ?? ($this->wasRecentlyCreated ? null : $this->getOriginal('status')),
            'to_status' => $toStatus,
            'actor_type' => $actorType,
            'user_id' => $userId ?? Auth::id(),
            'comments' => $comments,
        ]);
    }
}
