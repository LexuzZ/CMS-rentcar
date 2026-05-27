<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('car_installments', function (Blueprint $table) {
            $table->id();

            $table->foreignId('car_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->string('nama_leasing')->nullable();

            $table->date('tanggal_mulai');
            $table->date('jatuh_tempo');

            $table->bigInteger('total_hutang')->default(0);
            $table->bigInteger('nominal_cicilan')->default(0);

            $table->integer('tenor')->default(0);
            $table->integer('cicilan_ke')->default(0);

            $table->bigInteger('total_dibayar')->default(0);
            $table->bigInteger('sisa_hutang')->default(0);

            $table->enum('status', [
                'berjalan',
                'lunas',
                'macet'
            ])->default('berjalan');

            $table->text('catatan')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('car_installments');
    }
};
