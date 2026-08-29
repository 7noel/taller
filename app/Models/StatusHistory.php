<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class StatusHistory extends Model
{
    protected $fillable = [
        'subject_type',
        'subject_id',
        'from_status',
        'to_status',
        'user_id',
        'actor_type',
        'comments',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    public const ACTOR_LABELS = [
        'internal' => 'Interno',
        'client' => 'Cliente',
        'system' => 'Sistema',
    ];

    public function subject(): MorphTo
    {
        return $this->morphTo();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function getActorLabelAttribute(): string
    {
        return self::ACTOR_LABELS[$this->actor_type] ?? $this->actor_type ?? 'Interno';
    }
}
