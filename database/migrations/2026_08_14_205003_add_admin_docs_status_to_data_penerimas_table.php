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
            $table->string('status_ktp')->nullable()->after('ktp');
            $table->text('catatan_ktp')->nullable()->after('status_ktp');
            
            $table->string('status_kk')->nullable()->after('kk');
            $table->text('catatan_kk')->nullable()->after('status_kk');
            
            $table->string('status_surat_pernyataan')->nullable()->after('surat_pernyataan');
            $table->text('catatan_surat_pernyataan')->nullable()->after('status_surat_pernyataan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('data_penerimas', function (Blueprint $table) {
            $table->dropColumn([
                'status_ktp', 'catatan_ktp',
                'status_kk', 'catatan_kk',
                'status_surat_pernyataan', 'catatan_surat_pernyataan'
            ]);
        });
    }
};
