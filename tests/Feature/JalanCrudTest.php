<?php

use App\Models\Jalan;
use App\Models\TitikJalan;
use App\Models\User;
use App\Services\HaversineService;

beforeEach(function () {
    $this->actingAs(User::factory()->create());
});

test('can store jalan with valid payload', function () {
    $titikAwal = TitikJalan::factory()->create([
        'latitude' => -8.105,
        'longitude' => 113.74,
    ]);
    $titikAkhir = TitikJalan::factory()->create([
        'latitude' => -8.101,
        'longitude' => 113.731,
    ]);

    $expectedDistance = round(
        app(HaversineService::class)->distanceInKilometers(
            (float) $titikAwal->latitude,
            (float) $titikAwal->longitude,
            (float) $titikAkhir->latitude,
            (float) $titikAkhir->longitude,
        ),
        3,
    );

    $response = $this->post(route('jalan.store'), [
        'titik_awal_id' => $titikAwal->id,
        'titik_akhir_id' => $titikAkhir->id,
    ]);

    $response->assertRedirect(route('jalan.index'));

    $this->assertDatabaseHas('jalan', [
        'titik_awal_id' => $titikAwal->id,
        'titik_akhir_id' => $titikAkhir->id,
        'jarak' => $expectedDistance,
    ]);
});

test('cannot store jalan when titik awal and titik akhir are the same', function () {
    $titik = TitikJalan::factory()->create();

    $response = $this->from(route('jalan.create'))->post(route('jalan.store'), [
        'titik_awal_id' => $titik->id,
        'titik_akhir_id' => $titik->id,
    ]);

    $response->assertRedirect(route('jalan.create'));
    $response->assertSessionHasErrors(['titik_awal_id', 'titik_akhir_id']);

    $this->assertDatabaseCount('jalan', 0);
});

test('can update jalan with valid payload', function () {
    $jalan = Jalan::factory()->create();
    $newTitikAwal = TitikJalan::factory()->create([
        'latitude' => -8.11,
        'longitude' => 113.751,
    ]);
    $newTitikAkhir = TitikJalan::factory()->create([
        'latitude' => -8.097,
        'longitude' => 113.725,
    ]);

    $expectedDistance = round(
        app(HaversineService::class)->distanceInKilometers(
            (float) $newTitikAwal->latitude,
            (float) $newTitikAwal->longitude,
            (float) $newTitikAkhir->latitude,
            (float) $newTitikAkhir->longitude,
        ),
        3,
    );

    $response = $this->put(route('jalan.update', $jalan), [
        'titik_awal_id' => $newTitikAwal->id,
        'titik_akhir_id' => $newTitikAkhir->id,
    ]);

    $response->assertRedirect(route('jalan.index'));

    $this->assertDatabaseHas('jalan', [
        'id' => $jalan->id,
        'titik_awal_id' => $newTitikAwal->id,
        'titik_akhir_id' => $newTitikAkhir->id,
        'jarak' => $expectedDistance,
    ]);
});

test('can delete jalan', function () {
    $jalan = Jalan::factory()->create();

    $response = $this->delete(route('jalan.destroy', $jalan));

    $response->assertRedirect(route('jalan.index'));

    $this->assertDatabaseMissing('jalan', [
        'id' => $jalan->id,
    ]);
});
