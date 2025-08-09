<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('production_data', function (Blueprint $table) {
            $table->float('weather_humidity')->nullable()->after('weather_condition');
            $table->float('weather_wind_speed')->nullable()->after('weather_humidity');
            $table->string('weather_wind_direction')->nullable()->after('weather_wind_speed');
            $table->float('weather_pressure')->nullable()->after('weather_wind_direction');
            $table->float('weather_uv_index')->nullable()->after('weather_pressure');
            $table->integer('weather_cloud_cover')->nullable()->after('weather_uv_index');
            $table->float('weather_solar_irradiance')->nullable()->after('weather_cloud_cover');
            $table->string('weather_icon_url')->nullable()->after('weather_solar_irradiance');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('production_data', function (Blueprint $table) {
            $table->dropColumn([
                'weather_humidity',
                'weather_wind_speed', 
                'weather_wind_direction',
                'weather_pressure',
                'weather_uv_index',
                'weather_cloud_cover',
                'weather_solar_irradiance',
                'weather_icon_url'
            ]);
        });
    }
};
