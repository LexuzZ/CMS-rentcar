<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->time('check_out_time')->nullable()->after('check_in_time');
            $table->decimal('check_out_latitude', 10, 7)->nullable()->after('longitude');
            $table->decimal('check_out_longitude', 10, 7)->nullable()->after('check_out_latitude');
            $table->decimal('check_out_distance_meters', 8, 2)->nullable()->after('distance_meters');
        });
    }

    public function down(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->dropColumn([
                'check_out_time',
                'check_out_latitude',
                'check_out_longitude',
                'check_out_distance_meters',
            ]);
        });
    }
};
