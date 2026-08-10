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
        Schema::create('baps', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_bap', 60)->unique();          // BAP-001/PUPR/VII/2026
            $table->foreignId('data_mingguan_id')->constrained('data_mingguans')->onDelete('cascade');
            $table->enum('status', ['draft', 'terbit', 'ttd', 'revisi'])->default('draft');
            $table->text('catatan')->nullable();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('baps');
    }
};
