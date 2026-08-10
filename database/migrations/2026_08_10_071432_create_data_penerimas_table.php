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
        Schema::create('data_penerimas', function (Blueprint $table) {
            $table->id();
            $table->string('nama')->nullable();
            $table->char('jenis_kelamin', 1)->nullable(); // L/P
            $table->string('no_ktp')->nullable();
            $table->string('no_kk')->nullable();
            $table->text('alamat')->nullable();
            $table->string('desa_kelurahan')->nullable();
            $table->string('kecamatan')->nullable();
            $table->string('kabupaten_kota')->nullable();
            $table->string('provinsi')->nullable();
            $table->string('pengelompokan_desil')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('data_penerimas');
    }
};
