<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProductionData extends Model
{
    use HasFactory;

    protected $fillable = [
        'system_id',
        'date',
        'energy_today',
        'energy_yesterday',
        'power_current',
        'efficiency',
        'energy_lifetime',
        'weather_temperature',
        'weather_condition',
        'weather_humidity',
        'weather_wind_speed',
        'weather_wind_direction',
        'weather_pressure',
        'weather_uv_index',
        'weather_cloud_cover',
        'weather_solar_irradiance',
        'weather_icon_url',
    ];

    protected $casts = [
        'date' => 'datetime',
        'energy_today' => 'float',
        'energy_yesterday' => 'float',
        'power_current' => 'float',
        'efficiency' => 'float',
        'energy_lifetime' => 'float',
        'weather_temperature' => 'float',
        'weather_humidity' => 'float',
        'weather_wind_speed' => 'float',
        'weather_pressure' => 'float',
        'weather_uv_index' => 'float',
        'weather_cloud_cover' => 'integer',
        'weather_solar_irradiance' => 'float',
    ];

    public function system()
    {
        return $this->belongsTo(System::class);
    }

    /**
     * Scope for today's data
     */
    public function scopeToday($query)
    {
        return $query->whereDate('date', today());
    }

    /**
     * Scope for yesterday's data
     */
    public function scopeYesterday($query)
    {
        return $query->whereDate('date', yesterday());
    }
}
