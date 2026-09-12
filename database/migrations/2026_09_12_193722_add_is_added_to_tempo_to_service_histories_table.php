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
        Schema::table('service_histories', function (Blueprint $table) {
            $table->boolean('is_added_to_tempo')
                ->default(false)
                ->after('next_km');
        });
    }

    public function down(): void
    {
        Schema::table('service_histories', function (Blueprint $table) {
            $table->dropColumn('is_added_to_tempo');
        });
    }
};
