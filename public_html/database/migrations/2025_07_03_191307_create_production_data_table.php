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
        Schema::create('production_data', function (Blueprint $table) {
            $table->id();
            $table->foreignId('system_id')->constrained()->onDelete('cascade');
            $table->timestamp('date');
            $table->float('energy_today')->nullable(); // kWh
            $table->float('energy_yesterday')->nullable(); // kWh
            $table->float('power_current')->nullable(); // kW
            $table->float('efficiency')->nullable(); // percentage
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('production_data');
    }
};
