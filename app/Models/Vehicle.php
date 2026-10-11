<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Vehicle extends Model
{
    protected $fillable = [
        'category_id',
        'vehicle_name',
        'slug',
        'brand',
        'model',
        'year',
        'registration_number',
        'description',
        'fuel_type',
        'transmission',
        'seating_capacity',
        'price_per_day',
        'status',
        'color',
        'mileage',
        'engine_cc',
        'air_conditioning',
        'gps',
        'bluetooth',
        'usb_charger',
        'child_seat',
        'total_rentals',
        'average_rating',
    ];

    protected function casts(): array
    {
        return [
            'year' => 'integer',
            'seating_capacity' => 'integer',
            'price_per_day' => 'decimal:2',
            'air_conditioning' => 'boolean',
            'gps' => 'boolean',
            'bluetooth' => 'boolean',
            'usb_charger' => 'boolean',
            'child_seat' => 'boolean',
            'total_rentals' => 'integer',
            'average_rating' => 'decimal:2',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(VehicleCategory::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(VehicleImage::class)->orderBy('is_primary', 'desc')->orderBy('sort_order');
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function maintenanceRecords(): HasMany
    {
        return $this->hasMany(MaintenanceRecord::class);
    }

    public function wishlistedBy(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'wishlists')->withTimestamps();
    }

    public function scopeAvailable($query)
    {
        return $query->where('status', 'available');
    }

    public function scopeActive($query)
    {
        return $query->where('status', '!=', 'inactive');
    }

    public function firstImage(): HasOne
    {
        return $this->hasOne(VehicleImage::class)->orderBy('is_primary', 'desc')->orderBy('sort_order');
    }

    public function getPrimaryImageAttribute(): ?string
    {
        if ($this->relationLoaded('firstImage')) {
            return $this->firstImage?->image;
        }
        return $this->firstImage()->first()?->image;
    }
}
