<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('surveys', function (Blueprint $table) {
            // Foto Tampak Depan Bangunan & Lapangan
            $table->string('foto_bangunan')->nullable()->after('catatan_survei');
            // Foto Akses Jalan & Drainase Lokasi
            $table->string('foto_akses')->nullable()->after('foto_bangunan');
        });
    }

    public function down(): void
    {
        Schema::table('surveys', function (Blueprint $table) {
            $table->dropColumn(['foto_bangunan', 'foto_akses']);
        });
    }
};
