<?php

use App\Models\Desa;
use App\Models\Posyandu;
use App\Models\User;

test('guest cannot access bulk delete posyandu', function () {
    $response = $this->post(route('posyandu.bulk-destroy'), [
        'ids' => [1, 2],
    ]);

    $response->assertRedirect('/login');
});

test('guest cannot access bulk delete desa', function () {
    $response = $this->post(route('desa.bulk-destroy'), [
        'ids' => [1, 2],
    ]);

    $response->assertRedirect('/login');
});

test('admin can bulk delete posyandu', function () {
    $admin = User::factory()->create();
    $desa = Desa::factory()->create();
    $posyandu1 = Posyandu::factory()->create(['desa_id' => $desa->id]);
    $posyandu2 = Posyandu::factory()->create(['desa_id' => $desa->id]);
    $posyandu3 = Posyandu::factory()->create(['desa_id' => $desa->id]);

    $response = $this->actingAs($admin)->post(route('posyandu.bulk-destroy'), [
        'ids' => [$posyandu1->id, $posyandu2->id],
    ]);

    $response->assertRedirect(route('posyandu.index'));
    $response->assertSessionHasNoErrors();

    $this->assertDatabaseMissing('posyandu', ['id' => $posyandu1->id]);
    $this->assertDatabaseMissing('posyandu', ['id' => $posyandu2->id]);
    $this->assertDatabaseHas('posyandu', ['id' => $posyandu3->id]);
});

test('admin can bulk delete desa', function () {
    $admin = User::factory()->create();
    $desa1 = Desa::factory()->create();
    $desa2 = Desa::factory()->create();
    $desa3 = Desa::factory()->create();

    $response = $this->actingAs($admin)->post(route('desa.bulk-destroy'), [
        'ids' => [$desa1->id, $desa2->id],
    ]);

    $response->assertRedirect(route('desa.index'));
    $response->assertSessionHasNoErrors();

    $this->assertDatabaseMissing('desa', ['id' => $desa1->id]);
    $this->assertDatabaseMissing('desa', ['id' => $desa2->id]);
    $this->assertDatabaseHas('desa', ['id' => $desa3->id]);
});

test('bulk delete with empty ids returns error redirect', function () {
    $admin = User::factory()->create();

    $response = $this->actingAs($admin)->post(route('posyandu.bulk-destroy'), [
        'ids' => [],
    ]);

    $response->assertRedirect(route('posyandu.index'));
});
