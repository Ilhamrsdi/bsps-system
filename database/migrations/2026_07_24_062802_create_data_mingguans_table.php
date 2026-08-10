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
        Schema::create('data_mingguans', function (Blueprint $table) {
            $table->id();
            $table->string('nama_kegiatan');
            $table->string('nama_pemohon')->nullable();
            $table->string('nik_pemohon', 16)->nullable();
            $table->string('lokasi'); // kecamatan
            $table->string('alamat')->nullable();
            $table->date('tanggal'); // tanggal kegiatan
            $table->unsignedTinyInteger('minggu')->nullable(); // nomor minggu (1-52)
            $table->enum('status', ['proses','selesai','menunggu','survei','batal'])->default('menunggu');
            $table->enum('status_bap', ['belum','sudah'])->default('belum');
            $table->string('nilai_kontrak')->nullable();
            $table->string('kontraktor')->nullable();
            $table->text('deskripsi')->nullable();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('data_mingguans');
    }
};
