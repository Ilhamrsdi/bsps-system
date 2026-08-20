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
            if (!Schema::hasColumn('data_penerimas', 'is_mypkp')) {
                $table->boolean('is_mypkp')->default(false)->index()->after('status_kelayakan');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('data_penerimas', function (Blueprint $table) {
            if (Schema::hasColumn('data_penerimas', 'is_mypkp')) {
                $table->dropColumn('is_mypkp');
            }
        });
    }
};
