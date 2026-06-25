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
        Schema::create('blacklists', function (Blueprint $table) {
            $table->id();
            $table->string('nik', 16)->unique()->index();
            $table->string('nama')->nullable();           // nama penyewa (opsional, untuk referensi)
            $table->string('alasan');                     // alasan diblacklist
            $table->text('catatan')->nullable();          // catatan tambahan detail
            $table->string('blacklisted_by')->nullable(); // nama admin yang memblacklist
            $table->timestamp('blacklisted_at')->useCurrent();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('blacklists');
    }
};
