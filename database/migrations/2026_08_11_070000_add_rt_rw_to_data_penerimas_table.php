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
            if (!Schema::hasColumn('data_penerimas', 'rt')) {
                $table->string('rt', 10)->nullable()->after('dusun');
            }
            if (!Schema::hasColumn('data_penerimas', 'rw')) {
                $table->string('rw', 10)->nullable()->after('rt');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('data_penerimas', function (Blueprint $table) {
            $cols = [];
            if (Schema::hasColumn('data_penerimas', 'rt')) $cols[] = 'rt';
            if (Schema::hasColumn('data_penerimas', 'rw')) $cols[] = 'rw';
            if (!empty($cols)) $table->dropColumn($cols);
        });
    }
};
