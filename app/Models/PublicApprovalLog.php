<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Registro de auditoría de aprobaciones/rechazos de inventarios y presupuestos,
 * tanto de usuarios internos como de clientes vía el portal público.
 */
class PublicApprovalLog extends Model
{
    protected $fillable = [
        'vehicle_id',
        'action',
        'entity_type',
        'entity_id',
        'actor_type',
        'actor_user_id',
        'actor_recipient',
        'actor_phone',
        'reason',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'action' => 'string',
        'actor_type' => 'string',
    ];

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function actorUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'actor_user_id');
    }
}
