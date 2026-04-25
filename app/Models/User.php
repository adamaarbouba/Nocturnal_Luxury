<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'role_id',
        'banned_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function dashboardRoute(): string
    {
        $roleMap = [
            'admin' => 'admin.dashboard',
            'owner' => 'owner.dashboard',
            'receptionist' => 'receptionist.dashboard',
            'staff' => 'staff.dashboard',
            'cleaner' => 'cleaner.dashboard',
            'inspector' => 'inspector.dashboard',
            'guest' => 'guest.dashboard',
        ];

        return $roleMap[$this->role->slug] ?? 'dashboard';
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function ownedHotels(): HasMany
    {
        return $this->hasMany(Hotel::class, 'owner_id');
    }

    public function receptionistAt(): HasOne
    {
        return $this->hasOne(HotelReceptionist::class);
    }

    public function hotelStaffPositions(): HasMany
    {
        return $this->hasMany(HotelStaff::class);
    }

    public function hotels(): BelongsToMany
    {
        return $this->belongsToMany(Hotel::class, 'hotel_staff')
            ->withPivot('role', 'hourly_rate', 'is_available')
            ->withTimestamps();
    }

    public function cleaningLogs(): HasMany
    {
        return $this->hasMany(CleaningLog::class);
    }

    public function inspectionRequests(): HasMany
    {
        return $this->hasMany(InspectionRequest::class, 'inspector_id');
    }

    public function approvedInspections(): HasMany
    {
        return $this->hasMany(InspectionRequest::class, 'approved_by');
    }
}
