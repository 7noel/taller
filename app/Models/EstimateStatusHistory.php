<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EstimateStatusHistory extends Model
{
    /**
     * La migración crea la tabla en singular (estimate_status_history).
     */
    protected $table = 'estimate_status_history';

    protected $fillable = [
        'estimate_id',
        'from_status',
        'to_status',
        'user_id',
        'comments',
    ];

    public function estimate()
    {
        return $this->belongsTo(Estimate::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}