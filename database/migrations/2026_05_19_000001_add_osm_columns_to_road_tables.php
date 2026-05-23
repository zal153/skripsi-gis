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
        Schema::table('titik_jalan', function (Blueprint $table) {
            $table->unsignedBigInteger('osm_node_id')->nullable()->unique()->after('id');
            $table->string('source')->default('manual')->index()->after('longitude');
        });

        Schema::table('jalan', function (Blueprint $table) {
            $table->unsignedBigInteger('osm_way_id')->nullable()->index()->after('id');
            $table->unsignedInteger('osm_segment_index')->nullable()->after('osm_way_id');
            $table->string('source')->default('manual')->index()->after('jarak');

            $table->unique([
                'osm_way_id',
                'osm_segment_index',
                'titik_awal_id',
                'titik_akhir_id',
            ], 'jalan_osm_segment_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('jalan', function (Blueprint $table) {
            $table->dropUnique('jalan_osm_segment_unique');
            $table->dropColumn(['osm_way_id', 'osm_segment_index', 'source']);
        });

        Schema::table('titik_jalan', function (Blueprint $table) {
            $table->dropUnique(['osm_node_id']);
            $table->dropColumn(['osm_node_id', 'source']);
        });
    }
};
