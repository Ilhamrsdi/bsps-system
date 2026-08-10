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
            $table->enum('status_foto_sudut_depan', ['layak', 'tidak layak'])->nullable()->after('foto_sudut_depan');
            $table->enum('status_foto_sudut_belakang', ['layak', 'tidak layak'])->nullable()->after('foto_sudut_belakang');
            $table->enum('status_foto_bagian_dalam', ['layak', 'tidak layak'])->nullable()->after('foto_bagian_dalam');
            $table->enum('status_foto_sudut_kiri', ['layak', 'tidak layak'])->nullable()->after('foto_sudut_kiri');
            $table->enum('status_foto_sudut_kanan', ['layak', 'tidak layak'])->nullable()->after('foto_sudut_kanan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('data_penerimas', function (Blueprint $table) {
            $table->dropColumn([
                'status_foto_sudut_depan',
                'status_foto_sudut_belakang',
                'status_foto_bagian_dalam',
                'status_foto_sudut_kiri',
                'status_foto_sudut_kanan'
            ]);
        });
    }
};
