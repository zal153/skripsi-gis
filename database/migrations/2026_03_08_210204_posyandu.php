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
        Schema::create('desa', function (Blueprint $table) {
            $table->id();
            $table->string('nama_desa');
            $table->timestamps();
        });

        Schema::create('posyandu', function (Blueprint $table) {
            $table->id();
            $table->foreignId('desa_id')->constrained('desa')->cascadeOnDelete();
            $table->string('nama_posyandu');
            $table->string('alamat');
            $table->decimal('latitude', 10, 8);
            $table->decimal('longitude', 11, 8);
            $table->string('status');
            $table->string('keterangan')->nullable();
            $table->timestamps();
        });

        Schema::create('titik_jalan', function (Blueprint $table) {
            $table->id();
            $table->string('nama_titik');
            $table->decimal('latitude', 10, 8);
            $table->decimal('longitude', 11, 8);
            $table->timestamps();
        });

        Schema::create('jalan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('titik_awal_id')->constrained('titik_jalan')->cascadeOnDelete();
            $table->foreignId('titik_akhir_id')->constrained('titik_jalan')->cascadeOnDelete();
            $table->decimal('jarak', 8, 3);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jalan');
        Schema::dropIfExists('titik_jalan');
        Schema::dropIfExists('posyandu');
        Schema::dropIfExists('desa');
    }
};
