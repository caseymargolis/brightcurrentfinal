<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ServiceSchedule extends Model
{
    use HasFactory;


    protected $fillable = [
        'system_id',
        'scheduled_date',
        'scheduled_time',
        'service_type',
        'notes',
        'status',
        'assigned_technician',
    ];

    protected $casts = [
        'scheduled_date' => 'datetime',
    ];

    public function system(): BelongsTo
    {
        return $this->belongsTo(System::class);
    }
}
