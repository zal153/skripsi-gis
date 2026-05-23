<?php

use App\Models\Jalan;
use App\Models\TitikJalan;
use Illuminate\Support\Facades\Http;

test('osm import roads command stores overpass nodes and road segments', function () {
    Http::fake([
        'overpass-api.de/*' => Http::response([
            'elements' => [
                [
                    'type' => 'way',
                    'id' => 7001,
                    'nodes' => [1001, 1002, 1003],
                    'tags' => ['highway' => 'residential'],
                ],
                [
                    'type' => 'node',
                    'id' => 1001,
                    'lat' => -8.106989,
                    'lon' => 113.736139,
                ],
                [
                    'type' => 'node',
                    'id' => 1002,
                    'lat' => -8.1065,
                    'lon' => 113.737,
                ],
                [
                    'type' => 'node',
                    'id' => 1003,
                    'lat' => -8.106,
                    'lon' => 113.738,
                ],
            ],
        ]),
    ]);

    $this->artisan('osm:import-roads --radius=10000')
        ->assertSuccessful();

    expect(TitikJalan::query()->where('source', 'osm')->count())->toBe(3);
    expect(Jalan::query()->where('source', 'osm')->count())->toBe(2);

    $this->assertDatabaseHas('titik_jalan', [
        'osm_node_id' => 1001,
        'source' => 'osm',
    ]);

    $this->assertDatabaseHas('jalan', [
        'osm_way_id' => 7001,
        'osm_segment_index' => 0,
        'source' => 'osm',
    ]);
});

test('osm import roads command can purge imported osm data', function () {
    $titikAwal = TitikJalan::factory()->create([
        'osm_node_id' => 1001,
        'source' => 'osm',
    ]);
    $titikAkhir = TitikJalan::factory()->create([
        'osm_node_id' => 1002,
        'source' => 'osm',
    ]);

    Jalan::factory()->create([
        'titik_awal_id' => $titikAwal->id,
        'titik_akhir_id' => $titikAkhir->id,
        'source' => 'osm',
    ]);

    $this->artisan('osm:import-roads --purge-osm')
        ->assertSuccessful();

    expect(TitikJalan::query()->where('source', 'osm')->count())->toBe(0);
    expect(Jalan::query()->where('source', 'osm')->count())->toBe(0);
});
