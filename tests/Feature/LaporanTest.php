<?php

use App\Models\Laporan;
use App\Models\LaporanBalasan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('guest can get reports via api', function () {
    $laporan = Laporan::create([
        'nama_posyandu' => 'Posyandu Indah',
        'alamat' => 'Jl. Indah No. 1',
        'keterangan' => 'Keterangan tambahan',
    ]);

    $response = $this->getJson(route('api.v1.laporan.index'));

    $response->assertStatus(200)
        ->assertJsonStructure([
            'success',
            'message',
            'data' => [
                '*' => [
                    'id',
                    'nama_posyandu',
                    'alamat',
                    'keterangan',
                    'created_at',
                    'time_ago',
                    'balasans',
                ],
            ],
        ])
        ->assertJsonFragment([
            'nama_posyandu' => 'Posyandu Indah',
        ]);
});

test('guest can submit new report via api', function () {
    $response = $this->postJson(route('api.v1.laporan.store'), [
        'nama_posyandu' => 'Posyandu Melati',
        'alamat' => 'Dusun Krajan',
        'keterangan' => 'Buka senin pagi',
    ]);

    $response->assertStatus(201)
        ->assertJsonFragment([
            'nama_posyandu' => 'Posyandu Melati',
        ]);

    $this->assertDatabaseHas('laporans', [
        'nama_posyandu' => 'Posyandu Melati',
    ]);
});

test('guest cannot submit report with missing fields', function () {
    $response = $this->postJson(route('api.v1.laporan.store'), [
        'nama_posyandu' => '',
        'alamat' => '',
    ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['nama_posyandu', 'alamat']);
});

test('unauthenticated user cannot access admin laporan page', function () {
    $response = $this->get(route('laporan.index'));
    $response->assertRedirect('/login');
});

test('admin can access admin laporan page', function () {
    $admin = User::factory()->create();

    $response = $this->actingAs($admin)->get(route('laporan.index'));

    $response->assertStatus(200)
        ->assertViewIs('admin.laporan.index');
});

test('admin can reply to a report', function () {
    $admin = User::factory()->create();
    $laporan = Laporan::create([
        'nama_posyandu' => 'Posyandu Bunga',
        'alamat' => 'Jl. Bunga No. 3',
    ]);

    $response = $this->actingAs($admin)->post(route('laporan.reply', $laporan), [
        'pesan' => 'Kami akan segera survey.',
    ]);

    $response->assertRedirect();
    $this->assertDatabaseHas('laporan_balasans', [
        'laporan_id' => $laporan->id,
        'user_id' => $admin->id,
        'pesan' => 'Kami akan segera survey.',
    ]);
});

test('admin can delete a report', function () {
    $admin = User::factory()->create();
    $laporan = Laporan::create([
        'nama_posyandu' => 'Posyandu Bunga',
        'alamat' => 'Jl. Bunga No. 3',
    ]);

    $response = $this->actingAs($admin)->delete(route('laporan.destroy', $laporan));

    $response->assertRedirect(route('laporan.index'));
    $this->assertDatabaseMissing('laporans', [
        'id' => $laporan->id,
    ]);
});

test('guest can update a report via api', function () {
    $laporan = Laporan::create([
        'nama_posyandu' => 'Posyandu Bunga',
        'alamat' => 'Jl. Bunga No. 3',
        'keterangan' => 'Keterangan awal.',
    ]);

    $response = $this->putJson(route('api.v1.laporan.update', $laporan), [
        'nama_posyandu' => 'Posyandu Mawar',
        'alamat' => 'Jl. Mawar No. 5',
        'keterangan' => 'Keterangan diperbarui.',
    ]);

    $response->assertOk()
        ->assertJsonPath('success', true);

    $this->assertDatabaseHas('laporans', [
        'id' => $laporan->id,
        'nama_posyandu' => 'Posyandu Mawar',
        'alamat' => 'Jl. Mawar No. 5',
        'keterangan' => 'Keterangan diperbarui.',
    ]);
});

test('guest can delete a report via api', function () {
    $laporan = Laporan::create([
        'nama_posyandu' => 'Posyandu Bunga',
        'alamat' => 'Jl. Bunga No. 3',
    ]);

    $response = $this->deleteJson(route('api.v1.laporan.destroy', $laporan));

    $response->assertOk()
        ->assertJsonPath('success', true);

    $this->assertDatabaseMissing('laporans', [
        'id' => $laporan->id,
    ]);
});

test('admin can update a reply', function () {
    $admin = User::factory()->create();
    $laporan = Laporan::create([
        'nama_posyandu' => 'Posyandu Bunga',
        'alamat' => 'Jl. Bunga No. 3',
    ]);

    $reply = LaporanBalasan::create([
        'laporan_id' => $laporan->id,
        'user_id' => $admin->id,
        'pesan' => 'Balasan awal.',
    ]);

    $response = $this->actingAs($admin)->put(route('laporan.reply.update', $reply), [
        'pesan' => 'Balasan diperbarui.',
    ]);

    $response->assertRedirect();
    $this->assertDatabaseHas('laporan_balasans', [
        'id' => $reply->id,
        'pesan' => 'Balasan diperbarui.',
    ]);
});

test('admin can delete a reply', function () {
    $admin = User::factory()->create();
    $laporan = Laporan::create([
        'nama_posyandu' => 'Posyandu Bunga',
        'alamat' => 'Jl. Bunga No. 3',
    ]);

    $reply = LaporanBalasan::create([
        'laporan_id' => $laporan->id,
        'user_id' => $admin->id,
        'pesan' => 'Balasan untuk dihapus.',
    ]);

    $response = $this->actingAs($admin)->delete(route('laporan.reply.destroy', $reply));

    $response->assertRedirect();
    $this->assertDatabaseMissing('laporan_balasans', [
        'id' => $reply->id,
    ]);
});
