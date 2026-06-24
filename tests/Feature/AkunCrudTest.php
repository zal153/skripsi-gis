<?php

use App\Models\User;
use Illuminate\Support\Facades\Route;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);
});

test('can render index page', function () {
    $response = $this->get(route('akun.index'));
    $response->assertStatus(200);
});

test('can render own edit page', function () {
    $response = $this->get(route('akun.edit', $this->user));
    $response->assertStatus(200);
    $response->assertViewHas('akun', $this->user);
});

test('cannot render another users edit page', function () {
    $response = $this->get(route('akun.edit', User::factory()->create()));

    $response->assertForbidden();
});

test('cannot update another users account', function () {
    $akun = User::factory()->create();

    $response = $this->put(route('akun.update', $akun), [
        'name' => 'Nama Tidak Boleh Diubah',
        'email' => 'tidak-boleh-diubah@example.com',
    ]);

    $response->assertForbidden();
    $this->assertDatabaseMissing('users', [
        'id' => $akun->id,
        'name' => 'Nama Tidak Boleh Diubah',
    ]);
});

test('can update own name and email', function () {
    $this->user->update([
        'name' => 'Old Name',
        'email' => 'old@example.com',
    ]);

    $response = $this->put(route('akun.update', $this->user), [
        'name' => 'New Name',
        'email' => 'new@example.com',
    ]);

    $response->assertRedirect(route('akun.index'));

    $this->assertDatabaseHas('users', [
        'id' => $this->user->id,
        'name' => 'New Name',
        'email' => 'new@example.com',
    ]);
});

test('can update own password', function () {
    $this->user->update([
        'password' => 'password123',
    ]);
    $oldPassword = $this->user->password;

    $response = $this->put(route('akun.update', $this->user), [
        'name' => $this->user->name,
        'email' => $this->user->email,
        'current_password' => 'password123',
        'password' => 'newpassword123',
        'password_confirmation' => 'newpassword123',
    ]);

    $response->assertRedirect(route('akun.index'));

    $this->user->refresh();
    $this->assertNotEquals($oldPassword, $this->user->password);
});

test('cannot update email if email is already taken by another user', function () {
    $otherUser = User::factory()->create([
        'email' => 'taken@example.com',
    ]);

    $this->user->update(['email' => 'myemail@example.com']);

    $response = $this->from(route('akun.edit', $this->user))->put(route('akun.update', $this->user), [
        'name' => $this->user->name,
        'email' => 'taken@example.com',
    ]);

    $response->assertRedirect(route('akun.edit', $this->user));
    $response->assertSessionHasErrors(['email']);

    $this->assertDatabaseHas('users', [
        'id' => $this->user->id,
        'email' => 'myemail@example.com',
    ]);
});

test('account deletion route is unavailable', function () {
    $akun = User::factory()->create();

    $response = $this->delete('/akun/'.$akun->id);

    $response->assertMethodNotAllowed();
    expect(Route::has('akun.destroy'))->toBeFalse();

    $this->assertDatabaseHas('users', [
        'id' => $akun->id,
    ]);
});
