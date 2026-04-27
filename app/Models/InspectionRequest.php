<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InspectionRequest extends Model
{
    protected $table = 'inspection_requests';

    protected $fillable = [
        'room_id',
        'inspector_id',
        'status', // pending, approved, rejected, resolved
        'severity', // minor, moderate, severe
        'issue_description',
    ];

    protected $casts = [
    ];

    // Relationships
    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    public function inspector(): BelongsTo
    {
        return $this->belongsTo(User::class, 'inspector_id');
    }
}

