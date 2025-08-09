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
            $table->float('energy_lifetime')->nullable()->after('efficiency');
            $table->float('weather_temperature')->nullable()->after('energy_lifetime');
            $table->string('weather_condition')->nullable()->after('weather_temperature');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('production_data', function (Blueprint $table) {
            $table->dropColumn(['energy_lifetime', 'weather_temperature', 'weather_condition']);
        });
    }
};
