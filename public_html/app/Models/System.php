<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class System extends Model
{
    use HasFactory;

    protected $fillable = [
        'system_id',
        'external_system_id', // ID from the external API (Enphase, SolarEdge, Tesla)
        'customer_name',
        'customer_type',
        'manufacturer',
        'status',
        'location',
        'latitude',
        'longitude',
        'capacity',
        'install_date',
        'last_seen',
        'api_enabled',
    ];

    protected $casts = [
        'latitude' => 'float',
        'longitude' => 'float',
        'capacity' => 'float',
        'install_date' => 'datetime',
        'last_seen' => 'datetime',
        'api_enabled' => 'boolean',
    ];

    /**
     * Scope for API-enabled systems
     */
    public function scopeApiEnabled($query)
    {
        return $query->where('api_enabled', true);
    }

    /**
     * Get the latest production data
     */
    public function latestProductionData()
    {
        return $this->hasOne(ProductionData::class)->latest('date');
    }

    /**
     * Get today's production data
     */
    public function todayProductionData()
    {
        return $this->hasOne(ProductionData::class)
                   ->whereDate('date', today());
    }

    public function productionData()
    {
        return $this->hasMany(ProductionData::class);
    }

    public function alerts()
    {
        return $this->hasMany(Alert::class);
    }

    public function serviceSchedules()
    {
        return $this->hasMany(ServiceSchedule::class);
    }

}
