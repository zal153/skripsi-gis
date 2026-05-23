<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * Koordinat telah diverifikasi berdasarkan:
     *  - Konfirmasi pengguna  : Manggis 10–14, 15–20 (Rayap), 19–26 (Darsono),
     *                           31–36 (Biting), 37–41 (Kamal)
     *  - GeoNames ID 7406664  : Krajan Timur Candijati = -8.0993, 113.7625
     *  - Sumber web lain      : Candijati (Manggis 25–30) dikoreksi
     *  - Perlu survei GPS     : Manggis 1–4 (Desa Arjasa – Dusun Calok, Tegal Bago,
     *                           Panji Laras, Bendelan) → koordinat masih estimasi
     *  - Koordinat salah      : Manggis 5–9 (Desa Arjasa) → wajib dikoreksi via
     *                           survei GPS setelah konfirmasi ke Puskesmas
     */
    public function run(): void
    {
        // ── 1. PENGGUNA (Admin) ───────────────────────────────────────────
        DB::table('pengguna')->insert([
            'nama' => 'Admin',
            'email' => 'admin@yahoo.com',
            'password' => Hash::make('password'),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // ── 2. DESA ──────────────────────────────────────────────────────
        // 6 desa di Kecamatan Arjasa, Kabupaten Jember
        $desa = [
            ['nama_desa' => 'Arjasa'],
            ['nama_desa' => 'Kemuning Lor'],
            ['nama_desa' => 'Darsono'],
            ['nama_desa' => 'Candijati'],
            ['nama_desa' => 'Biting'],
            ['nama_desa' => 'Kamal'],
        ];

        foreach ($desa as $item) {
            DB::table('desa')->insert([
                'nama_desa' => $item['nama_desa'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // ── 3. POSYANDU ──────────────────────────────────────────────────
        // Format: [desa_id, nama_posyandu, alamat_dusun, latitude, longitude]
        //
        // Keterangan status koordinat:
        //   ✅ OK  = dikonfirmasi benar (pengguna / GeoNames / web)
        //   ⚠️ EST = estimasi, perlu survei GPS lapangan
        //   ❌ CEK = koordinat lama salah posisi, estimasi sementara
        //
        $posyanduData = [

            // ── Desa Arjasa (desa_id = 1) ──────────────────────────────
            // Sumber alamat: Scribd Data Dasar Posyandu 2020, hal. 58
            // ⚠️ Manggis 1–4 : estimasi berdasarkan letak dusun,
            //                   belum disurvei GPS lapangan
            // ❌ Manggis 5–9 : koordinat lama terbukti salah posisi
            //                   (masuk wilayah desa lain), estimasi sementara
            //                   — wajib dikoreksi setelah survei

            [1, 'Manggis 1', 'Dusun Calok RT 002 RW 001',                   -8.1150, 113.7230], // ⚠️ EST
            [1, 'Manggis 2', 'Dusun Tegal Bago RT 001 RW 007',              -8.1120, 113.7290], // ⚠️ EST
            [1, 'Manggis 3', 'Perum Panji Laras Indah RT 001 RW 004',       -8.1080, 113.7310], // ⚠️ EST
            [1, 'Manggis 4', 'Jl. Rengganis Dusun Bendelan RT 002 RW 002',  -8.1090, 113.7350], // ⚠️ EST
            [1, 'Manggis 5', 'Dusun Krajan RT 002 RW 002',                  -8.1075, 113.7370], // ❌ CEK
            [1, 'Manggis 6', 'Dusun Krajan RT 002 RW 004',                  -8.1085, 113.7380], // ❌ CEK
            [1, 'Manggis 7', 'Dusun Gumitir RT 004 RW 015',                 -8.1000, 113.7370], // ❌ CEK – konfirmasi desa ke Puskesmas
            [1, 'Manggis 8', 'Dusun Gumitir RT 002 RW 015',                 -8.0990, 113.7360], // ❌ CEK – konfirmasi desa ke Puskesmas
            [1, 'Manggis 9', 'Jl. Irian No.25 RT 002 RW 003',               -8.1070, 113.7360], // ❌ CEK

            // ── Desa Kemuning Lor (desa_id = 2) ────────────────────────
            // ✅ Seluruh koordinat dikonfirmasi benar oleh pengguna
            // Sumber alamat: Scribd hal. 58–59

            [2, 'Manggis 10',   'Dusun Krajan RT 002 RW 002',                       -8.12071, 113.72991], // ✅
            [2, 'Manggis 11',   'Dusun Krajan RT 003 RW 001',                       -8.12481, 113.73133], // ✅
            [2, 'Manggis 12',   'Dusun Kopang Kebun RT 001 RW 004',                 -8.11798, 113.71808], // ✅
            [2, 'Manggis 13',   'Dusun Darungan RT 001 RW 005',                     -8.11396, 113.71313], // ✅
            [2, 'Manggis 14',   'Dusun Darungan RT 002 RW 006',                     -8.10569, 113.70704], // ✅
            [2, 'Manggis 15',   'Dusun Rayap RT 003 RW 003',                        -8.08885, 113.69687], // ✅
            [2, 'Manggis 15 A', 'Dusun Rayap RT 003 RW 012',                        -8.09208, 113.69455], // ✅
            [2, 'Manggis 16',   'Jl. Raya Rembangan 3 Dusun Rayap RT 001 RW 013',  -8.08092, 113.69311], // ✅
            [2, 'Manggis 16 A', 'Dusun Rayap RT 003 RW 009',                        -8.07996, 113.69613], // ✅
            [2, 'Manggis 17',   'Dusun Rayap RT 004 RW 013',                        -8.08526, 113.68944], // ✅
            [2, 'Manggis 18',   'Dusun Rayap RT 003 RW 009',                        -8.09814, 113.70323], // ✅
            [2, 'Manggis 42',   'Dusun Rayap RT 003 RW 009',                        -8.08513, 113.69624], // ✅

            // ── Desa Darsono (desa_id = 3) ─────────────────────────────
            // ✅ Seluruh koordinat dikonfirmasi benar oleh pengguna
            // Sumber alamat: Scribd hal. 59

            [3, 'Manggis 19', 'Dusun Kupang Krajan RT 001 RW 001', -8.1179, 113.7339], // ✅
            [3, 'Manggis 20', 'Dusun Kopang Krajan RT 004 RW 001', -8.1110, 113.7255], // ✅
            [3, 'Manggis 21', 'Dusun Padasan RT 001 RW 003',        -8.1037, 113.7174], // ✅
            [3, 'Manggis 22', 'Dusun Padasan RT 004 RW 003',        -8.0975, 113.7135], // ✅
            [3, 'Manggis 23', 'Dusun Teratai RT 003 RW 002',        -8.0916, 113.7093], // ✅
            [3, 'Manggis 24', 'Dusun Gading RT 005 RW 004',         -8.0850, 113.7050], // ✅

            // ── Desa Candijati (desa_id = 4) ───────────────────────────
            // ✅ Koordinat dikoreksi & diverifikasi dari GeoNames dan web
            // Referensi utama: GeoNames ID 7406664 (Mapcarta):
            //   Krajan Timur Candijati = -8.0993, 113.7625
            // Sumber alamat: Scribd hal. 60
            // Desa Candijati: 4 dusun = Krajan Barat, Krajan Timur, Bataan, Sumberjati
            // Batas desa: Utara=Jelbuk, Barat=Kamal, Selatan=Arjasa+Biting, Timur=Sukowiryo

            [4, 'Manggis 25', 'Dusun Krajan Barat RT 004 RW 002',           -8.0978, 113.7540], // ✅ web
            [4, 'Manggis 26', 'Balai Desa Candijati Jl. Diponegoro No.151', -8.0965, 113.7580], // ✅ web
            [4, 'Manggis 27', 'Dusun Bataan RT 004 RW 002',                 -8.0930, 113.7615], // ✅ web (Jl. Diponegoro No.25 Bataan)
            [4, 'Manggis 28', 'Krajan Timur RT 003 RW 002',                 -8.0993, 113.7625], // ✅ GeoNames ID 7406664
            [4, 'Manggis 29', 'Dusun Krajan Timur RT 002 RW 001',           -8.0993, 113.7625], // ✅ GeoNames ID 7406664
            [4, 'Manggis 30', 'Dusun Sumberjati RT 001 RW 001',             -8.0840, 113.7730], // ✅ web (dusun paling utara Candijati)

            // ── Desa Biting (desa_id = 5) ──────────────────────────────
            // ✅ Seluruh koordinat dikonfirmasi benar oleh pengguna
            // Sumber alamat: Scribd hal. 60
            // Catatan: Manggis 36 B dari data seeder lama dipertahankan

            [5, 'Manggis 31',   'Dusun Krajan RT 001 RW 011',                -8.11623, 113.76250], // ✅
            [5, 'Manggis 32',   'Dusun Mojo RT 002 RW 003',                  -8.11433, 113.76604], // ✅
            [5, 'Manggis 33',   'Dusun Tegallo RT 001 RW 006',               -8.11604, 113.76303], // ✅
            [5, 'Manggis 34',   'Jl. Flamboyan RT 002 RW 007',               -8.11256, 113.76984], // ✅
            [5, 'Manggis 35',   'Jl. Flamboyan Dusun Tegallo RT 005 RW 008', -8.11445, 113.76880], // ✅
            [5, 'Manggis 36',   'Dusun Biting Pinggir RT 003 RW 001',        -8.11232, 113.76334], // ✅
            [5, 'Manggis 36 B', 'Dusun Biting Pinggir RT 003 RW 001',        -8.11058, 113.76329], // ✅

            // ── Desa Kamal (desa_id = 6) ───────────────────────────────
            // ✅ Seluruh koordinat dikonfirmasi benar oleh pengguna
            // Sumber alamat: Scribd hal. 60

            [6, 'Manggis 37', 'Dusun Klanceng RT 001 RW 002', -8.1051, 113.7515], // ✅
            [6, 'Manggis 38', 'Dusun Krajan RT 005 RW 004',   -8.1005, 113.7505], // ✅
            [6, 'Manggis 39', 'Dusun Duplang RT 004 RW 005',  -8.0887, 113.7414], // ✅
            [6, 'Manggis 40', 'Dusun Kopang RT 001 RW 007',   -8.1065, 113.7450], // ✅
            [6, 'Manggis 41', 'Dusun Gumitir RT 001 RW 009',  -8.1035, 113.7595], // ✅
        ];

        foreach ($posyanduData as $p) {
            DB::table('posyandu')->insert([
                'desa_id' => $p[0],
                'nama_posyandu' => $p[1],
                'alamat' => $p[2].', Desa '.$desa[$p[0] - 1]['nama_desa'].', Kec. Arjasa, Kab. Jember',
                'latitude' => (string) $p[3],
                'longitude' => (string) $p[4],
                'status' => 'Aktif',
                'keterangan' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
