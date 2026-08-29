<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WorkOrderSatisfactionSurvey extends Model
{
    protected $fillable = [
        'work_order_id',
        'form_template_id',
        'answers',
        'respondent_name',
        'respondent_phone',
        'ip_address',
        'responded_at',
    ];

    protected $casts = [
        'answers' => 'array',
        'responded_at' => 'datetime',
    ];

    public function workOrder(): BelongsTo
    {
        return $this->belongsTo(WorkOrder::class);
    }

    public function template(): BelongsTo
    {
        return $this->belongsTo(FormTemplate::class, 'form_template_id');
    }
}
