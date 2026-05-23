<?php

use App\Models\TitikJalan;
use App\Models\User;

beforeEach(function () {
    $this->actingAs(User::factory()->create());
});

test('can store titik jalan with valid payload', function () {
    $response = $this->post(route('titik-jalan.store'), [
        'nama_titik' => 'Simpang Baru',
        'latitude' => -8.12345678,
        'longitude' => 113.12345678,
    ]);

    $response->assertRedirect(route('titik-jalan.index'));

    $this->assertDatabaseHas('titik_jalan', [
        'nama_titik' => 'Simpang Baru',
        'latitude' => -8.12345678,
        'longitude' => 113.12345678,
    ]);
});

test('cannot store titik jalan with invalid latitude', function () {
    $response = $this->from(route('titik-jalan.create'))->post(route('titik-jalan.store'), [
        'nama_titik' => 'Simpang Error',
        'latitude' => -100,
        'longitude' => 113.12345678,
    ]);

    $response->assertRedirect(route('titik-jalan.create'));
    $response->assertSessionHasErrors(['latitude']);

    $this->assertDatabaseCount('titik_jalan', 0);
});

test('cannot store titik jalan with duplicate coordinates', function () {
    TitikJalan::factory()->create([
        'latitude' => -8.11111111,
        'longitude' => 113.22222222,
    ]);

    $response = $this->from(route('titik-jalan.create'))->post(route('titik-jalan.store'), [
        'nama_titik' => 'Simpang Duplikat',
        'latitude' => -8.11111111,
        'longitude' => 113.22222222,
    ]);

    $response->assertRedirect(route('titik-jalan.create'));
    $response->assertSessionHasErrors(['latitude']);

    $this->assertDatabaseCount('titik_jalan', 1);
});

test('can update titik jalan with valid payload', function () {
    $titikJalan = TitikJalan::factory()->create();

    $response = $this->put(route('titik-jalan.update', $titikJalan), [
        'nama_titik' => 'Simpang Update',
        'latitude' => -8.23456789,
        'longitude' => 113.23456789,
    ]);

    $response->assertRedirect(route('titik-jalan.index'));

    $this->assertDatabaseHas('titik_jalan', [
        'id' => $titikJalan->id,
        'nama_titik' => 'Simpang Update',
        'latitude' => -8.23456789,
        'longitude' => 113.23456789,
    ]);
});

test('can delete titik jalan', function () {
    $titikJalan = TitikJalan::factory()->create();

    $response = $this->delete(route('titik-jalan.destroy', $titikJalan));

    $response->assertRedirect(route('titik-jalan.index'));

    $this->assertDatabaseMissing('titik_jalan', [
        'id' => $titikJalan->id,
    ]);
});

test('cannot update titik jalan with duplicate coordinates', function () {
    $existing = TitikJalan::factory()->create([
        'latitude' => -8.33333333,
        'longitude' => 113.44444444,
    ]);

    $titikJalan = TitikJalan::factory()->create([
        'latitude' => -8.55555555,
        'longitude' => 113.66666666,
    ]);

    $response = $this->from(route('titik-jalan.edit', $titikJalan))->put(route('titik-jalan.update', $titikJalan), [
        'nama_titik' => $titikJalan->nama_titik,
        'latitude' => $existing->latitude,
        'longitude' => $existing->longitude,
    ]);

    $response->assertRedirect(route('titik-jalan.edit', $titikJalan));
    $response->assertSessionHasErrors(['latitude']);

    $this->assertDatabaseHas('titik_jalan', [
        'id' => $titikJalan->id,
        'latitude' => -8.55555555,
        'longitude' => 113.66666666,
    ]);
});
