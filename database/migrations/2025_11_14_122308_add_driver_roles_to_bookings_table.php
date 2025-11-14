<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->foreignId('driver_pengantaran_id')->nullable()->constrained('drivers')->nullOnDelete();
            $table->foreignId('driver_pengembalian_id')->nullable()->constrained('drivers')->nullOnDelete();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn(['driver_pengantaran_id', 'driver_pengembalian_id']);
        });
    }
};
