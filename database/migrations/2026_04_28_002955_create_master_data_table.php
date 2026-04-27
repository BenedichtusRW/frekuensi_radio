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
        Schema::create('master_data', function (Blueprint $table) {
            $table->id();
            $table->string('category', 50); // e.g. stasiun_monitor, kode_negara, kelas_stasiun, administrasi_termonitor
            $table->string('value', 255);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            // Add index for faster querying
            $table->index(['category', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('master_data');
    }
};
