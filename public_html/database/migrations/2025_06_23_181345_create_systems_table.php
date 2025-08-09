<?php

use App\Models\Customer;
use App\Models\Manufacturer;
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
        Schema::create('systems', function (Blueprint $table) {
            $table->id();
            $table->string('system_id')->unique();
            $table->string('customer_name');
            $table->string('customer_type'); // residential, commercial
            $table->string('manufacturer'); // solaredge, enphase
            $table->string('status')->default('active'); // active, warning, critical, offline
            $table->string('location');
            $table->float('capacity')->nullable(); // kW
            $table->timestamp('install_date')->nullable();
            $table->timestamp('last_seen')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('systems');
    }
};
