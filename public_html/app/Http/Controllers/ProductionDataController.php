<?php

namespace App\Http\Controllers;

use App\Models\System;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Http\Request;

class ProductionDataController extends Controller
{
    use HasFactory;

    protected $fillable = [
        'system_id',
        'date',
        'energy_today',
        'energy_yesterday',
        'power_current',
        'efficiency',
    ];

    protected $casts = [
        'date' => 'datetime',
        'energy_today' => 'float',
        'energy_yesterday' => 'float',
        'power_current' => 'float',
        'efficiency' => 'float',
    ];

    public function system()
    {
        return $this->belongsTo(System::class);
    }
}
