<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('data_penerimas', function (Blueprint $table) {
            // Tambah kolom surat_pernyataan (foto/pdf) — opsional
            $table->string('surat_pernyataan')->nullable()->after('sertifikat_tanah');
        });
    }

    public function down(): void
    {
        Schema::table('data_penerimas', function (Blueprint $table) {
            $table->dropColumn('surat_pernyataan');
        });
    }
};
