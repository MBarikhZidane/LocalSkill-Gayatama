<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_number',
        'brief',
        'scheduled_date',
        'customer_id',
        'provider_id',
        'service_id',
        'price',
        'platform_fee',
        'total_amount',
        'status',
        'started_at',
        'completed_at',
    ];

    public static function statusLabel(string $status): string
    {
        return $status === 'submitted' ? 'Awaiting Confirmation' : ucwords(str_replace('_', ' ', $status));
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function provider(): BelongsTo
    {
        return $this->belongsTo(User::class, 'provider_id');
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function serviceRequest(): BelongsTo
    {
        return $this->belongsTo(ServiceRequest::class);
    }

    public function payment(): HasOne
    {
        return $this->hasOne(Payment::class);
    }

    public function conservation(): HasOne
    {
        return $this->hasOne(Conservation::class, 'order_id');
    }
}
