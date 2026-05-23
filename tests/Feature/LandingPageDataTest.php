<?php

use App\Models\Desa;
use App\Models\Jalan;
use App\Models\Posyandu;
use App\Models\TitikJalan;

test('landing page loads posyandu and titik jalan data from database', function () {
    $desa = Desa::factory()->create([
        'nama_desa' => 'Arjasa',
    ]);

    $posyandu = Posyandu::factory()->create([
        'desa_id' => $desa->id,
        'nama_posyandu' => 'Posyandu Uji Arjasa',
        'alamat' => 'Kecamatan Arjasa, Jember',
        'latitude' => -8.103,
        'longitude' => 113.735,
    ]);

    $titikAwal = TitikJalan::factory()->create([
        'nama_titik' => 'Simpang Uji 1',
        'latitude' => -8.105,
        'longitude' => 113.74,
        'source' => 'osm',
    ]);

    $titikAkhir = TitikJalan::factory()->create([
        'nama_titik' => 'Simpang Uji 2',
        'latitude' => -8.101,
        'longitude' => 113.731,
        'source' => 'osm',
    ]);

    Jalan::factory()->create([
        'titik_awal_id' => $titikAwal->id,
        'titik_akhir_id' => $titikAkhir->id,
        'jarak' => 1.5,
        'source' => 'osm',
    ]);

    $response = $this->get('/');

    $response->assertOk()
        ->assertSee('Posyandu Uji Arjasa')
        ->assertSee((string) $posyandu->id);
});
