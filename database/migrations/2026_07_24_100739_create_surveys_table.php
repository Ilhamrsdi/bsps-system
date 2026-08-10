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
        Schema::create('surveys', function (Blueprint $table) {
            $table->id();

            // Relasi ke Data Mingguan / Kegiatan Lapangan
            $table->foreignId('data_mingguan_id')->nullable()->constrained('data_mingguans')->nullOnDelete();
            $table->string('nama_kegiatan')->nullable();

            // 1. Data Petugas & Waktu Survei
            $table->string('nama_petugas_1');
            $table->string('nama_petugas_2')->nullable();
            $table->date('tanggal_survei');
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();

            // 2. Data Pemohon
            $table->string('nama_pemohon');
            $table->string('nik_pemohon')->nullable();
            $table->text('alamat_pemohon');

            // 3. Data Bangunan Gedung
            $table->string('jenis_bangunan');
            $table->string('fungsi_bangunan');
            $table->unsignedInteger('jumlah_lantai')->default(1);
            $table->double('tinggi_bangunan')->nullable();
            $table->double('luas_bangunan')->nullable();
            $table->double('luas_tanah')->nullable();
            $table->string('status_hak_tanah')->nullable();

            // 4. Lokasi & Koordinat GPS
            $table->string('kecamatan');
            $table->string('desa_kelurahan');
            $table->string('nama_jalan');
            $table->text('alamat_lokasi');
            $table->string('latitude');
            $table->string('longitude');

            // 5. Daftar Simak Pemeriksaan Lapangan & Foto Bukti
            // Item 1: Persyaratan Administratif
            $table->string('item_admin')->nullable(); // Sesuai / Tidak Sesuai
            $table->string('catatan_admin')->nullable();
            $table->string('foto_admin_1')->nullable();
            $table->string('foto_admin_2')->nullable();
            $table->string('foto_admin_3')->nullable();

            // Item 2a: Fungsi Bangunan Gedung
            $table->string('item_fungsi')->nullable();
            $table->string('catatan_fungsi')->nullable();
            $table->string('foto_fungsi_1')->nullable();
            $table->string('foto_fungsi_2')->nullable();
            $table->string('foto_fungsi_3')->nullable();

            // Item 2b: Peruntukan
            $table->string('item_peruntukan')->nullable();
            $table->string('catatan_peruntukan')->nullable();
            $table->string('foto_peruntukan_1')->nullable();
            $table->string('foto_peruntukan_2')->nullable();
            $table->string('foto_peruntukan_3')->nullable();

            // Item 2c: Tata Bangunan
            $table->string('item_tata')->nullable();
            $table->string('catatan_tata')->nullable();
            $table->string('foto_tata_1')->nullable();
            $table->string('foto_tata_2')->nullable();
            $table->string('foto_tata_3')->nullable();

            // Item 2d: Kelaikan Fungsi Bangunan
            $table->string('item_kelaikan')->nullable();
            $table->string('catatan_kelaikan')->nullable();
            $table->string('foto_kelaikan_1')->nullable();
            $table->string('foto_kelaikan_2')->nullable();
            $table->string('foto_kelaikan_3')->nullable();

            // Data Sempadan & Catatan umum
            $table->double('garis_sempadan_tritis')->nullable();
            $table->double('jarak_as_jalan')->nullable();
            $table->double('pelanggaran_sempadan')->nullable();
            $table->text('catatan_survei')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('surveys');
    }
};
