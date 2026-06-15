<?php

namespace Database\Seeders;

use App\Models\Laporan;
use App\Models\LaporanBalasan;
use App\Models\User;
use Illuminate\Database\Seeder;

class LaporanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::first();
        $adminId = $admin ? $admin->id : 1;

        // Laporan 1: Sudah dibalas
        $l1 = Laporan::create([
            'nama_posyandu' => 'Posyandu Bougenville 2',
            'alamat' => 'Dusun Krajan RT 01 RW 03, Desa Biting, Kec. Arjasa',
            'keterangan' => 'Posyandu ini aktif setiap hari Sabtu pertama awal bulan, mohon ditambahkan ke sistem.',
        ]);

        LaporanBalasan::create([
            'laporan_id' => $l1->id,
            'user_id' => $adminId,
            'pesan' => 'Terima kasih atas laporannya. Kami sudah memverifikasi data tersebut dan Posyandu Bougenville 2 kini telah terdaftar di sistem.',
        ]);

        // Laporan 2: Belum dibalas
        Laporan::create([
            'nama_posyandu' => 'Posyandu Melati 5',
            'alamat' => 'Dusun Duplang RT 02 RW 04, Desa Kamal, Kec. Arjasa',
            'keterangan' => 'Posyandu Melati 5 baru saja didirikan dekat balai dusun.',
        ]);

        // Laporan 3: Sudah dibalas dengan percakapan (2 balasan)
        $l3 = Laporan::create([
            'nama_posyandu' => 'Posyandu Mawar Indah',
            'alamat' => 'Dusun Kopang RT 03 RW 01, Desa Kemuning Lor, Kec. Arjasa',
            'keterangan' => 'Apakah Posyandu di daerah rembangan ini sudah terdaftar?',
        ]);

        LaporanBalasan::create([
            'laporan_id' => $l3->id,
            'user_id' => $adminId,
            'pesan' => 'Halo. Posyandu Mawar Indah belum terdaftar. Mohon kirimkan koordinat latitude dan longitude jika memilikinya, atau kami akan survey lokasinya.',
        ]);

        LaporanBalasan::create([
            'laporan_id' => $l3->id,
            'user_id' => $adminId,
            'pesan' => 'Update: Lokasi sudah kami survey dan datanya sedang kami proses untuk ditambahkan.',
        ]);
    }
}
