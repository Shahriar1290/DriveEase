<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Booking extends Model
{
    protected $fillable = [
        'booking_number',
        'user_id',
        'vehicle_id',
        'pickup_date',
        'return_date',
        'total_days',
        'price_per_day',
        'total_cost',
        'discount',
        'tax',
        'final_amount',
        'pickup_location',
        'return_location',
        'special_requests',
        'booking_status',
        'admin_notes',
    ];

    protected function casts(): array
    {
        return [
            'pickup_date' => 'date',
            'return_date' => 'date',
            'total_days' => 'integer',
            'price_per_day' => 'decimal:2',
            'total_cost' => 'decimal:2',
            'discount' => 'decimal:2',
            'tax' => 'decimal:2',
            'final_amount' => 'decimal:2',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function payment(): HasOne
    {
        return $this->hasOne(Payment::class);
    }

    public function review(): HasOne
    {
        return $this->hasOne(Review::class);
    }

    public function isPending(): bool
    {
        return $this->booking_status === 'pending';
    }

    public function isApproved(): bool
    {
        return $this->booking_status === 'approved';
    }

    public function isCompleted(): bool
    {
        return $this->booking_status === 'completed';
    }

    public function isCancelled(): bool
    {
        return $this->booking_status === 'cancelled';
    }

    public function canBeCancelled(): bool
    {
        return in_array($this->booking_status, ['pending', 'approved']);
    }

    public function canBeApproved(): bool
    {
        return $this->booking_status === 'pending';
    }

    public function canBeRejected(): bool
    {
        return $this->booking_status === 'pending';
    }

    public function canBeCompleted(): bool
    {
        return in_array($this->booking_status, ['approved', 'active']);
    }
}
