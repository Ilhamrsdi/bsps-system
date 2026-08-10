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
        Schema::create('kepala_desas', function (Blueprint $table) {
            $table->id();
            $table->string('kecamatan');
            $table->string('desa_kelurahan');
            $table->string('jabatan')->default('KEPALA DESA');
            $table->string('nama');
            $table->string('nomor_telepon')->nullable();
            $table->timestamps();

            $table->index(['kecamatan', 'desa_kelurahan']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kepala_desas');
    }
};
