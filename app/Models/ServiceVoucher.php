<?php

namespace App\Models;

use App\Models\Concerns\HasStatusHistory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class ServiceVoucher extends Model
{
    use HasFactory;
    use SoftDeletes;
    use LogsActivity;
    use HasStatusHistory;

    public const STATUS_PENDING = 'pending';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_LIQUIDATED = 'liquidated';

    public const STATUS_LABELS = [
        'pending' => 'Pendiente',
        'completed' => 'Completado',
        'liquidated' => 'Liquidado',
    ];

    protected $fillable = [
        'establishment_id',
        'document_series_id',
        'document_type_code',
        'document_serie',
        'document_number',
        'document_sn',
        'work_order_id',
        'provider_id',
        'execution_date',
        'description',
        'agreed_amount',
        'discount_applied',
        'base_amount',
        'igv_rate',
        'igv_amount',
        'total_with_igv',
        'detraction_rate',
        'detraction_amount',
        'total_payable',
        'status',
        'provider_settlement_id',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'document_number' => 'integer',
        'execution_date' => 'date',
        'agreed_amount' => 'float',
        'discount_applied' => 'float',
        'base_amount' => 'float',
        'igv_rate' => 'float',
        'igv_amount' => 'float',
        'total_with_igv' => 'float',
        'detraction_rate' => 'float',
        'detraction_amount' => 'float',
        'total_payable' => 'float',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['status', 'agreed_amount', 'base_amount', 'total_with_igv', 'detraction_amount', 'total_payable', 'document_sn'])
            ->logOnlyDirty();
    }

    public function workOrder(): BelongsTo
    {
        return $this->belongsTo(WorkOrder::class);
    }

    public function provider(): BelongsTo
    {
        return $this->belongsTo(Party::class, 'provider_id');
    }

    public function settlement(): BelongsTo
    {
        return $this->belongsTo(ProviderSettlement::class, 'provider_settlement_id');
    }

    public function documentSeries(): BelongsTo
    {
        return $this->belongsTo(DocumentSeries::class);
    }

    public function establishment(): BelongsTo
    {
        return $this->belongsTo(Establishment::class);
    }

    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function scopeCompleted(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_COMPLETED);
    }

    public function scopeNotLiquidated(Builder $query): Builder
    {
        return $query->whereNull('provider_settlement_id');
    }
}
