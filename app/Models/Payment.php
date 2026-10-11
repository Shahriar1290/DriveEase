<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    protected $fillable = [
        'transaction_id',
        'booking_id',
        'amount',
        'payment_method',
        'payment_status',
        'payment_date',
        'receipt_number',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'payment_date' => 'datetime',
        ];
    }

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public function isCompleted(): bool
    {
        return $this->payment_status === 'completed';
    }

    public function isPending(): bool
    {
        return $this->payment_status === 'pending';
    }
}
