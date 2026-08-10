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
            $table->string('tempat_lahir')->nullable()->after('nama');
            $table->date('tanggal_lahir')->nullable()->after('tempat_lahir');
            $table->string('luas_tanah')->nullable()->after('jenis_kepemilikan_lahan');
            $table->string('telah_ditempati_selama')->nullable()->after('status_tanah');
            $table->string('penghasilan')->nullable()->after('telah_ditempati_selama');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('data_penerimas', function (Blueprint $table) {
            $table->dropColumn([
                'tempat_lahir',
                'tanggal_lahir',
                'luas_tanah',
                'status_tanah',
                'telah_ditempati_selama',
                'penghasilan'
            ]);
        });
    }
};
