<?php

namespace App\Console\Commands;

use App\Models\Jalan;
use App\Models\TitikJalan;
use App\Services\HaversineService;
use Illuminate\Console\Command;

class GenerateJalan extends Command
{
    /**
     * The name and signature of the console command.
     * --radius: Jarak maksimal dua titik bisa dihubungkan (default 3 KM)
     * --limit: Maksimal cabang jalan per titik (biar tidak saling terhubung semua, default 4 cabang terdekat)
     */
    protected $signature = 'jalan:generate {--radius=3 : Radius maksimal dalam km} {--limit=4 : Maksimal relasi per titik}';

    /**
     * The console command description.
     */
    protected $description = 'Membuat data jalan secara otomatis berdasarkan jarak terdekat antar titik';

    /**
     * Execute the console command.
     */
    public function handle(HaversineService $haversine)
    {
        $titikJalan = TitikJalan::all();
        $titikCount = $titikJalan->count();

        if ($titikCount < 2) {
            $this->error('Minimal butuh 2 titik jalan untuk membuat relasi.');

            return;
        }

        $radius = (float) $this->option('radius');
        $limit = (int) $this->option('limit');
        $countJalanDibuat = 0;

        $this->info("Memulai generasi otomatis jalan untuk {$titikCount} titik...");
        $this->getOutput()->progressStart($titikCount);

        // Loop setiap titik untuk dicarikan tetangganya
        foreach ($titikJalan as $titikAwal) {
            $jarakTetangga = [];

            // Hitung jarak ke SEMUA titik lain
            foreach ($titikJalan as $titikAkhir) {
                if ($titikAwal->id === $titikAkhir->id) {
                    continue;
                }

                $jarak = $haversine->distanceInKilometers(
                    (float) $titikAwal->latitude,
                    (float) $titikAwal->longitude,
                    (float) $titikAkhir->latitude,
                    (float) $titikAkhir->longitude
                );

                // Tambahkan kandidat jika masuk dalam radius maksimal
                if ($jarak <= $radius) {
                    $jarakTetangga[$titikAkhir->id] = $jarak;
                }
            }

            // Urutkan dari yang paling dekat
            asort($jarakTetangga);

            // Ambil N tetangga terdekat sesuai limit cabang
            $tetanggaTerdekat = array_slice($jarakTetangga, 0, $limit, true);

            foreach ($tetanggaTerdekat as $titikAkhirId => $jarak) {
                // Cek apakah relasi jalan sudah ada di database (agar tidak duplikat)
                $isDuplicate = Jalan::where('titik_awal_id', $titikAwal->id)
                    ->where('titik_akhir_id', $titikAkhirId)
                    ->exists();

                if (! $isDuplicate) {
                    Jalan::create([
                        'titik_awal_id' => $titikAwal->id,
                        'titik_akhir_id' => $titikAkhirId,
                        'jarak' => round($jarak, 3), // sesuai format di Controller
                    ]);
                    $countJalanDibuat++;

                    // (Opsional) Jika jalannya mau dibikin 2 arah persis, bisa insert titik_akhir_id ke titik_awal_id juga di sini.
                    // Saat ini script membuat 1 relasi (A ke B). Kalau Dijkstra Anda butuh graf Undirected (2 arah mutlak),
                    // ntar dia otomatis kebuat saat giliran titik B mencari tetangga terdekat.
                }
            }

            $this->getOutput()->progressAdvance();
        }

        $this->getOutput()->progressFinish();
        $this->info("\nBerhasil! {$countJalanDibuat} relasi jalan baru telah di-generate secara otomatis.");
    }
}
