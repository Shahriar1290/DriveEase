<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MaintenanceRecord extends Model
{
    protected $fillable = [
        'vehicle_id',
        'maintenance_date',
        'next_maintenance_date',
        'maintenance_type',
        'description',
        'cost',
        'status',
        'mechanic_name',
        'service_center',
    ];

    protected function casts(): array
    {
        return [
            'maintenance_date' => 'date',
            'next_maintenance_date' => 'date',
            'cost' => 'decimal:2',
        ];
    }

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function isScheduled(): bool
    {
        return $this->status === 'scheduled';
    }

    public function isInProgress(): bool
    {
        return $this->status === 'in_progress';
    }
}
