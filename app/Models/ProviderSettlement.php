<?php

namespace App\Models;

use App\Models\Concerns\HasStatusHistory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class ProviderSettlement extends Model
{
    use HasFactory;
    use SoftDeletes;
    use LogsActivity;
    use HasStatusHistory;

    public const STATUS_DRAFT = 'draft';
    public const STATUS_APPROVED = 'approved';
    public const STATUS_PAID = 'paid';

    public const STATUS_LABELS = [
        'draft' => 'Borrador',
        'approved' => 'Aprobado',
        'paid' => 'Pagado',
    ];

    protected $fillable = [
        'establishment_id',
        'document_series_id',
        'document_type_code',
        'document_serie',
        'document_number',
        'document_sn',
        'provider_id',
        'period_start',
        'period_end',
        'subtotal',
        'global_discount',
        'discount_reason',
        'base_amount',
        'igv_rate',
        'igv_amount',
        'total_with_igv',
        'detraction_rate',
        'detraction_amount',
        'total_payable',
        'status',
        'approved_by',
        'paid_by',
        'approved_at',
        'paid_at',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'document_number' => 'integer',
        'period_start' => 'date',
        'period_end' => 'date',
        'subtotal' => 'float',
        'global_discount' => 'float',
        'base_amount' => 'float',
        'igv_rate' => 'float',
        'igv_amount' => 'float',
        'total_with_igv' => 'float',
        'detraction_rate' => 'float',
        'detraction_amount' => 'float',
        'total_payable' => 'float',
        'approved_at' => 'datetime',
        'paid_at' => 'datetime',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['status', 'subtotal', 'base_amount', 'total_with_igv', 'detraction_amount', 'total_payable', 'document_sn'])
            ->logOnlyDirty();
    }

    public function provider(): BelongsTo
    {
        return $this->belongsTo(Party::class, 'provider_id');
    }

    public function vouchers(): HasMany
    {
        return $this->hasMany(ServiceVoucher::class, 'provider_settlement_id');
    }

    public function documentSeries(): BelongsTo
    {
        return $this->belongsTo(DocumentSeries::class);
    }

    public function establishment(): BelongsTo
    {
        return $this->belongsTo(Establishment::class);
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function paidBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'paid_by');
    }
}
