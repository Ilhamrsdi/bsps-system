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
            if (!Schema::hasColumn('data_penerimas', 'indikator_lantai')) {
                $table->string('indikator_lantai')->nullable()->after('foto_sudut_kanan');
            }
            if (!Schema::hasColumn('data_penerimas', 'indikator_pondasi')) {
                $table->string('indikator_pondasi')->nullable()->after('indikator_lantai');
            }
            if (!Schema::hasColumn('data_penerimas', 'indikator_dinding')) {
                $table->string('indikator_dinding')->nullable()->after('indikator_pondasi');
            }
            if (!Schema::hasColumn('data_penerimas', 'indikator_struktur')) {
                $table->string('indikator_struktur')->nullable()->after('indikator_dinding');
            }
            if (!Schema::hasColumn('data_penerimas', 'indikator_atap')) {
                $table->string('indikator_atap')->nullable()->after('indikator_struktur');
            }
            if (!Schema::hasColumn('data_penerimas', 'indikator_penghasilan')) {
                $table->string('indikator_penghasilan')->nullable()->after('indikator_atap');
            }
            if (!Schema::hasColumn('data_penerimas', 'status_kelayakan')) {
                $table->string('status_kelayakan')->nullable()->after('indikator_penghasilan');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('data_penerimas', function (Blueprint $table) {
            $colsToDrop = [];
            foreach (['indikator_lantai', 'indikator_pondasi', 'indikator_dinding', 'indikator_struktur', 'indikator_atap', 'indikator_penghasilan', 'status_kelayakan'] as $col) {
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
