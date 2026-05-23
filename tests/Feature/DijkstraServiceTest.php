<?php

use App\Models\Jalan;
use App\Models\TitikJalan;
use App\Services\DijkstraService;

test('dijkstra service prefers osm road graph over manual straight edges', function () {
    $manualStart = TitikJalan::factory()->create([
        'latitude' => -8.1000,
        'longitude' => 113.7300,
        'source' => 'manual',
    ]);
    $manualEnd = TitikJalan::factory()->create([
        'latitude' => -8.1000,
        'longitude' => 113.7330,
        'source' => 'manual',
    ]);

    Jalan::factory()->create([
        'titik_awal_id' => $manualStart->id,
        'titik_akhir_id' => $manualEnd->id,
        'jarak' => 0.1,
        'source' => 'manual',
    ]);

    $osmStart = TitikJalan::factory()->create([
        'osm_node_id' => 1001,
        'latitude' => -8.1000,
        'longitude' => 113.7300,
        'source' => 'osm',
    ]);
    $osmMiddle = TitikJalan::factory()->create([
        'osm_node_id' => 1002,
        'latitude' => -8.1010,
        'longitude' => 113.7310,
        'source' => 'osm',
    ]);
    $osmEnd = TitikJalan::factory()->create([
        'osm_node_id' => 1003,
        'latitude' => -8.1000,
        'longitude' => 113.7330,
        'source' => 'osm',
    ]);

    Jalan::factory()->create([
        'osm_way_id' => 7001,
        'osm_segment_index' => 0,
        'titik_awal_id' => $osmStart->id,
        'titik_akhir_id' => $osmMiddle->id,
        'jarak' => 1.0,
        'source' => 'osm',
    ]);
    Jalan::factory()->create([
        'osm_way_id' => 7001,
        'osm_segment_index' => 1,
        'titik_awal_id' => $osmMiddle->id,
        'titik_akhir_id' => $osmEnd->id,
        'jarak' => 1.0,
        'source' => 'osm',
    ]);

    $routes = app(DijkstraService::class)->findKShortestPaths(
        -8.1000,
        113.7300,
        -8.1000,
        113.7330,
        1,
    );

    expect($routes)->toHaveCount(1);
    expect($routes[0]['distance'])->toBe(2.0);
    expect($routes[0]['path'])->toHaveCount(3);
});

test('dijkstra service smart snaps to node connected to main network and skips isolated nodes', function () {
    // Isolated network (A <-> B)
    $isolatedA = TitikJalan::factory()->create([
        'latitude' => -8.1001,
        'longitude' => 113.7301,
        'source' => 'osm',
    ]);
    $isolatedB = TitikJalan::factory()->create([
        'latitude' => -8.1002,
        'longitude' => 113.7302,
        'source' => 'osm',
    ]);
    Jalan::factory()->create([
        'titik_awal_id' => $isolatedA->id,
        'titik_akhir_id' => $isolatedB->id,
        'jarak' => 0.05,
        'source' => 'osm',
    ]);

    // Main network (C <-> D <-> E)
    $mainC = TitikJalan::factory()->create([
        'latitude' => -8.1005,
        'longitude' => 113.7305,
        'source' => 'osm',
    ]);
    $mainD = TitikJalan::factory()->create([
        'latitude' => -8.1010,
        'longitude' => 113.7310,
        'source' => 'osm',
    ]);
    $mainE = TitikJalan::factory()->create([
        'latitude' => -8.1015,
        'longitude' => 113.7315,
        'source' => 'osm',
    ]);

    $mainNodes = [$mainC, $mainD, $mainE];
    for ($i = 0; $i < 32; $i++) {
        $mainNodes[] = TitikJalan::factory()->create([
            'latitude' => -8.1020 + ($i * 0.001),
            'longitude' => 113.7320 + ($i * 0.001),
            'source' => 'osm',
        ]);
    }
    // Connect them in a chain
    for ($i = 0; $i < 34; $i++) {
        Jalan::factory()->create([
            'titik_awal_id' => $mainNodes[$i]->id,
            'titik_akhir_id' => $mainNodes[$i + 1]->id,
            'jarak' => 0.1,
            'source' => 'osm',
        ]);
    }

    // Now, we request a route starting near isolatedA, ending at mainNodes[9].
    // The closest start node by distance is isolatedA, but it is in a disconnected island of size 2.
    // The smart snapping should ignore it and snap to mainC!
    $routes = app(DijkstraService::class)->findKShortestPaths(
        -8.1001,
        113.7301,
        -8.1020 + (6 * 0.001),
        113.7320 + (6 * 0.001),
        1,
    );

    expect($routes)->not->toBeEmpty();
    expect($routes[0]['path'][0]['lat'])->toBe((float) $mainC->latitude);
});
