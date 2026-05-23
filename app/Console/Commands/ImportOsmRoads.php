<?php

namespace App\Console\Commands;

use App\Models\Jalan;
use App\Models\TitikJalan;
use App\Services\HaversineService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class ImportOsmRoads extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'osm:import-roads
        {--lat=-8.106989 : Latitude pusat area import}
        {--lng=113.736139 : Longitude pusat area import}
        {--radius=10000 : Radius import dari pusat koordinat dalam meter}
        {--types=residential,tertiary,secondary,unclassified,service,primary,track,living_street,trunk : Tipe highway OSM yang diimport}
        {--replace-osm : Hapus data OSM lama sebelum import ulang}
        {--purge-osm : Hapus data OSM lama tanpa melakukan import}
        {--dry-run : Ambil data Overpass dan tampilkan ringkasan tanpa menyimpan}';

    /**
     * The console command description.
     */
    protected $description = 'Import jaringan jalan dari Overpass API ke tabel titik_jalan dan jalan';

    /**
     * Execute the console command.
     */
    public function handle(HaversineService $haversine): int
    {
        if ($this->option('purge-osm')) {
            $deletedRoads = Jalan::query()->where('source', 'osm')->delete();
            $deletedNodes = TitikJalan::query()->where('source', 'osm')->delete();

            $this->info("Data OSM dihapus: {$deletedNodes} titik jalan, {$deletedRoads} jalan.");

            return self::SUCCESS;
        }

        $latitude = (float) $this->option('lat');
        $longitude = (float) $this->option('lng');
        $radius = (int) $this->option('radius');
        $types = $this->normaliseHighwayTypes((string) $this->option('types'));

        if ($types === []) {
            $this->error('Minimal satu tipe highway harus diisi.');

            return self::FAILURE;
        }

        $query = $this->buildOverpassQuery($latitude, $longitude, $radius, $types);

        $this->info('Mengambil data jalan dari Overpass API...');
        $response = Http::timeout(90)
            ->withUserAgent('skripsi-gis-osm-import/1.0')
            ->get('https://overpass-api.de/api/interpreter', [
                'data' => $query,
            ]);

        if (! $response->successful()) {
            $this->error("Overpass API gagal merespons. Status: {$response->status()}");
            $this->line(substr($response->body(), 0, 500));

            return self::FAILURE;
        }

        $payload = $response->json();
        $elements = $payload['elements'] ?? [];
        [$nodes, $ways] = $this->splitElements($elements);
        $segments = $this->buildSegments($ways, $nodes, $haversine);

        $this->table(['Item', 'Jumlah'], [
            ['OSM nodes', count($nodes)],
            ['OSM ways', count($ways)],
            ['Segmen jalan', count($segments)],
        ]);

        if ($this->option('dry-run')) {
            $this->info('Dry-run selesai. Tidak ada data yang disimpan.');

            return self::SUCCESS;
        }

        if ($this->option('replace-osm')) {
            $this->info('Menghapus data OSM lama...');
            Jalan::query()->where('source', 'osm')->delete();
            TitikJalan::query()->where('source', 'osm')->delete();
        }

        $this->info('Menyimpan titik jalan...');
        $titikIdsByOsmNodeId = [];
        $nodeChunks = array_chunk($nodes, 500, true);
        $this->getOutput()->progressStart(count($nodes));

        foreach ($nodeChunks as $chunk) {
            DB::transaction(function () use ($chunk, &$titikIdsByOsmNodeId): void {
                foreach ($chunk as $osmNodeId => $node) {
                    $titik = TitikJalan::query()->updateOrCreate(
                        ['osm_node_id' => $osmNodeId],
                        [
                            'nama_titik' => 'OSM Node '.$osmNodeId,
                            'latitude' => $node['lat'],
                            'longitude' => $node['lon'],
                            'source' => 'osm',
                        ],
                    );

                    $titikIdsByOsmNodeId[$osmNodeId] = $titik->id;
                }
            });

            $this->getOutput()->progressAdvance(count($chunk));
        }

        $this->getOutput()->progressFinish();

        $this->info('Menyimpan segmen jalan...');
        $segmentChunks = array_chunk($segments, 500);
        $this->getOutput()->progressStart(count($segments));

        foreach ($segmentChunks as $chunk) {
            DB::transaction(function () use ($chunk, $titikIdsByOsmNodeId): void {
                foreach ($chunk as $segment) {
                    $titikAwalId = $titikIdsByOsmNodeId[$segment['from']] ?? null;
                    $titikAkhirId = $titikIdsByOsmNodeId[$segment['to']] ?? null;

                    if ($titikAwalId === null || $titikAkhirId === null || $titikAwalId === $titikAkhirId) {
                        continue;
                    }

                    Jalan::query()->updateOrCreate(
                        [
                            'osm_way_id' => $segment['way_id'],
                            'osm_segment_index' => $segment['segment_index'],
                            'titik_awal_id' => $titikAwalId,
                            'titik_akhir_id' => $titikAkhirId,
                        ],
                        [
                            'jarak' => $segment['distance'],
                            'source' => 'osm',
                        ],
                    );
                }
            });

            $this->getOutput()->progressAdvance(count($chunk));
        }

        $this->getOutput()->progressFinish();
        $this->info('Import OSM selesai.');

        return self::SUCCESS;
    }

    /**
     * @return list<string>
     */
    private function normaliseHighwayTypes(string $types): array
    {
        return collect(explode(',', $types))
            ->map(fn (string $type): string => trim($type))
            ->filter()
            ->unique()
            ->values()
            ->all();
    }

    /**
     * @param  list<string>  $types
     */
    private function buildOverpassQuery(float $latitude, float $longitude, int $radius, array $types): string
    {
        $typePattern = implode('|', array_map(
            fn (string $type): string => preg_quote($type, '/'),
            $types,
        ));

        return <<<OVERPASS
        [out:json][timeout:60];

        (
          way["highway"~"^({$typePattern})$"](around:{$radius},{$latitude},{$longitude});
        );

        (._;>;);
        out body;
        OVERPASS;
    }

    /**
     * @param  array<int, array<string, mixed>>  $elements
     * @return array{0: array<int, array{lat: float, lon: float}>, 1: array<int, array{id: int, nodes: list<int>}>}
     */
    private function splitElements(array $elements): array
    {
        $nodes = [];
        $ways = [];

        foreach ($elements as $element) {
            if (($element['type'] ?? null) === 'node') {
                $nodes[(int) $element['id']] = [
                    'lat' => (float) $element['lat'],
                    'lon' => (float) $element['lon'],
                ];
            }

            if (($element['type'] ?? null) === 'way') {
                $ways[(int) $element['id']] = [
                    'id' => (int) $element['id'],
                    'nodes' => array_map('intval', $element['nodes'] ?? []),
                ];
            }
        }

        return [$nodes, $ways];
    }

    /**
     * @param  array<int, array{id: int, nodes: list<int>}>  $ways
     * @param  array<int, array{lat: float, lon: float}>  $nodes
     * @return list<array{way_id: int, segment_index: int, from: int, to: int, distance: float}>
     */
    private function buildSegments(array $ways, array $nodes, HaversineService $haversine): array
    {
        $segments = [];

        foreach ($ways as $way) {
            $wayNodes = $way['nodes'];

            for ($index = 0; $index < count($wayNodes) - 1; $index++) {
                $fromNodeId = $wayNodes[$index];
                $toNodeId = $wayNodes[$index + 1];
                $fromNode = $nodes[$fromNodeId] ?? null;
                $toNode = $nodes[$toNodeId] ?? null;

                if ($fromNode === null || $toNode === null || $fromNodeId === $toNodeId) {
                    continue;
                }

                $segments[] = [
                    'way_id' => $way['id'],
                    'segment_index' => $index,
                    'from' => $fromNodeId,
                    'to' => $toNodeId,
                    'distance' => round($haversine->distanceInKilometers(
                        $fromNode['lat'],
                        $fromNode['lon'],
                        $toNode['lat'],
                        $toNode['lon'],
                    ), 3),
                ];
            }
        }

        return $segments;
    }
}
