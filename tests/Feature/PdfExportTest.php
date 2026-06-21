<?php

use App\Models\Desa;
use App\Models\Laporan;
use App\Models\Posyandu;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('guest cannot access posyandu pdf export', function () {
    $response = $this->get(route('posyandu.export-pdf'));

    $response->assertRedirect('/login');
});

test('guest cannot access laporan pdf export', function () {
    $response = $this->get(route('laporan.export-pdf'));

    $response->assertRedirect('/login');
});

test('admin can export posyandu pdf', function () {
    $admin = User::factory()->create();
    $desa = Desa::factory()->create();
    Posyandu::factory()->create(['desa_id' => $desa->id]);

    $response = $this->actingAs($admin)->get(route('posyandu.export-pdf'));

    $response->assertStatus(200);
    $response->assertHeader('content-type', 'application/pdf');
});

test('admin can export laporan pdf', function () {
    $admin = User::factory()->create();
    Laporan::create([
        'nama_posyandu' => 'Manggis 1',
        'alamat' => 'Calok',
    ]);

    $response = $this->actingAs($admin)->get(route('laporan.export-pdf'));

    $response->assertStatus(200);
    $response->assertHeader('content-type', 'application/pdf');
});
