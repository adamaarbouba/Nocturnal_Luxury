<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Hotel extends Model
{
    protected $fillable = [
        'name',
        'description',
        'owner_id',
        'phone',
        'email',
        'address',
        'city',
        'country',
        'status',
        'rating',
        'is_verified',
        'default_hourly_wage',
    ];

    protected $casts = [
        'is_verified' => 'boolean',
        'rating' => 'decimal:2',
        'default_hourly_wage' => 'decimal:2',
    ];

    // Relationships
    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function rooms(): HasMany
    {
        return $this->hasMany(Room::class);
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function staff(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'hotel_staff')
            ->withPivot('role', 'hourly_rate', 'is_available')
            ->withTimestamps();
    }

    public function receptionists(): HasMany
    {
        return $this->hasMany(HotelReceptionist::class);
    }
}
