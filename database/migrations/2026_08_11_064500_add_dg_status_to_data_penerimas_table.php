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
            if (!Schema::hasColumn('data_penerimas', 'dg_status')) {
                $table->string('dg_status', 30)->nullable()->index()->after('status');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('data_penerimas', function (Blueprint $table) {
            if (Schema::hasColumn('data_penerimas', 'dg_status')) {
                $table->dropColumn('dg_status');
            }
        });
    }
};
