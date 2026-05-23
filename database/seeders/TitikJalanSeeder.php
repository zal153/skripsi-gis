<?php

namespace Database\Seeders;

use App\Models\Jalan;
use App\Models\TitikJalan;
use App\Services\HaversineService;
use Illuminate\Database\Seeder;

class TitikJalanSeeder extends Seeder
{
    protected HaversineService $haversine;

    public function __construct(HaversineService $haversine)
    {
        $this->haversine = $haversine;
    }

    public function run(): void
    {
        // ── STEP 1: Buat semua titik jalan ──────────────────────────────
        // Koordinat diambil dari Google Maps / observasi lapangan
        $titikJalan = [
            // Persimpangan
            ['nama_titik' => 'Simpang Arjasa - Darsono',    'latitude' => -8.1050,   'longitude' => 113.7300],
            ['nama_titik' => 'Simpang Arjasa - Biting',     'latitude' => -8.1050,   'longitude' => 113.7450],
            ['nama_titik' => 'Simpang Arjasa - Candijati',  'latitude' => -8.1000,   'longitude' => 113.7500],
            ['nama_titik' => 'Simpang Arjasa - Kamal',      'latitude' => -8.1050,   'longitude' => 113.7400],
            ['nama_titik' => 'Simpang Kemuning Lor',        'latitude' => -8.1030,   'longitude' => 113.7100],

            // Lokasi posyandu dijadikan titik jalan juga
            // agar Dijkstra bisa merutekan sampai ke posyandu
            ['nama_titik' => 'Posyandu Manggis 1',          'latitude' => -8.1030,   'longitude' => 113.7350],
            ['nama_titik' => 'Posyandu Manggis 2',          'latitude' => -8.1229,   'longitude' => 113.7409],
            ['nama_titik' => 'Posyandu Manggis 7',          'latitude' => -8.1012,   'longitude' => 113.7231],
            ['nama_titik' => 'Posyandu Manggis 10',         'latitude' => -8.12071,  'longitude' => 113.72991],
        ];

        foreach ($titikJalan as $titik) {
            TitikJalan::create($titik);
        }

        // ── STEP 2: Tentukan koneksi antar titik ────────────────────────
        // Hanya titik yang terhubung LANGSUNG oleh jalan yang didaftarkan
        // Format: ['nama titik awal', 'nama titik akhir']
        // Jarak akan dihitung OTOMATIS menggunakan Haversine
        $koneksi = [
            ['Simpang Arjasa - Darsono',   'Simpang Arjasa - Biting'],
            ['Simpang Arjasa - Biting',    'Simpang Arjasa - Candijati'],
            ['Simpang Arjasa - Biting',    'Simpang Arjasa - Kamal'],
            ['Simpang Arjasa - Darsono',   'Simpang Kemuning Lor'],
            ['Simpang Arjasa - Darsono',   'Posyandu Manggis 1'],
            ['Simpang Arjasa - Darsono',   'Posyandu Manggis 7'],
            ['Simpang Kemuning Lor',       'Posyandu Manggis 10'],
            ['Simpang Arjasa - Biting',    'Posyandu Manggis 2'],
        ];

        // ── STEP 3: Hitung jarak otomatis & simpan ke tabel jalan ───────
        foreach ($koneksi as [$namaTitikAwal, $namaTitikAkhir]) {

            // Ambil data titik dari database
            $titikAwal = TitikJalan::where('nama_titik', $namaTitikAwal)->first();
            $titikAkhir = TitikJalan::where('nama_titik', $namaTitikAkhir)->first();

            if (! $titikAwal || ! $titikAkhir) {
                $this->command->warn("Titik tidak ditemukan: $namaTitikAwal → $namaTitikAkhir");

                continue;
            }

            // Hitung jarak otomatis menggunakan Haversine
            $jarak = $this->resolveJarak(
                (float) $titikAwal->latitude,
                (float) $titikAwal->longitude,
                (float) $titikAkhir->latitude,
                (float) $titikAkhir->longitude
            );

            // Simpan ke tabel jalan
            // Dibuat dua arah agar Dijkstra bisa melewati jalan dari kedua sisi
            Jalan::create([
                'titik_awal_id' => $titikAwal->id,
                'titik_akhir_id' => $titikAkhir->id,
                'jarak' => $jarak,
            ]);

            // Arah balik (B → A)
            Jalan::create([
                'titik_awal_id' => $titikAkhir->id,
                'titik_akhir_id' => $titikAwal->id,
                'jarak' => $jarak, // Jarak sama karena simetris
            ]);

            $this->command->info("$namaTitikAwal → $namaTitikAkhir: {$jarak} km");
        }
    }

    private function resolveJarak(
        float $latitudeAwal,
        float $longitudeAwal,
        float $latitudeAkhir,
        float $longitudeAkhir
    ): float {
        $candidateMethods = [
            'distanceInKilometers',
            'hitungJarak',
            'calculateDistance',
            'distanceInKm',
            'getDistance',
        ];

        foreach ($candidateMethods as $method) {
            if (method_exists($this->haversine, $method)) {
                $jarak = $this->haversine->{$method}(
                    $latitudeAwal,
                    $longitudeAwal,
                    $latitudeAkhir,
                    $longitudeAkhir
                );

                return (float) $jarak;
            }
        }

        throw new \LogicException('Tidak ditemukan method perhitungan jarak pada HaversineService.');
    }
}
