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
        Schema::table('data_penerimas', function (Blueprint $table) {
            $table->text('catatan_foto_sudut_depan')->nullable()->after('status_foto_sudut_depan');
            $table->text('catatan_foto_sudut_belakang')->nullable()->after('status_foto_sudut_belakang');
            $table->text('catatan_foto_bagian_dalam')->nullable()->after('status_foto_bagian_dalam');
            $table->text('catatan_foto_sudut_kiri')->nullable()->after('status_foto_sudut_kiri');
            $table->text('catatan_foto_sudut_kanan')->nullable()->after('status_foto_sudut_kanan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('data_penerimas', function (Blueprint $table) {
            $table->dropColumn([
                'catatan_foto_sudut_depan',
                'catatan_foto_sudut_belakang',
                'catatan_foto_bagian_dalam',
                'catatan_foto_sudut_kiri',
                'catatan_foto_sudut_kanan'
            ]);
        });
    }
};
