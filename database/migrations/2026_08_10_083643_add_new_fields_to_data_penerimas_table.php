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
            if (!Schema::hasColumn('data_penerimas', 'user_id')) {
                $table->foreignId('user_id')->nullable()->after('id')->constrained('users')->nullOnDelete();
            }
            if (!Schema::hasColumn('data_penerimas', 'tempat_lahir')) {
                $table->string('tempat_lahir')->nullable()->after('nama');
            }
            if (!Schema::hasColumn('data_penerimas', 'tanggal_lahir')) {
                $table->date('tanggal_lahir')->nullable()->after('tempat_lahir');
            }
            if (!Schema::hasColumn('data_penerimas', 'luas_tanah')) {
                $table->string('luas_tanah')->nullable()->after('jenis_kepemilikan_lahan');
            }
            if (!Schema::hasColumn('data_penerimas', 'status_tanah')) {
                $table->string('status_tanah')->nullable()->after('luas_tanah');
            }
            if (!Schema::hasColumn('data_penerimas', 'telah_ditempati_selama')) {
                $table->string('telah_ditempati_selama')->nullable()->after('status_tanah');
            }
            if (!Schema::hasColumn('data_penerimas', 'penghasilan')) {
                $table->string('penghasilan')->nullable()->after('telah_ditempati_selama');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('data_penerimas', function (Blueprint $table) {
            if (Schema::hasColumn('data_penerimas', 'user_id')) {
                $table->dropForeign(['user_id']);
                $table->dropColumn('user_id');
            }

            $colsToDrop = [];
            foreach (['tempat_lahir', 'tanggal_lahir', 'luas_tanah', 'status_tanah', 'telah_ditempati_selama', 'penghasilan'] as $col) {
                if (Schema::hasColumn('data_penerimas', $col)) {
                    $colsToDrop[] = $col;
                }
            }
            if (!empty($colsToDrop)) {
                $table->dropColumn($colsToDrop);
            }
        });
    }
};
