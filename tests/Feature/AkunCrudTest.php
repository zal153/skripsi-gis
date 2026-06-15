<?php

use App\Models\User;

beforeEach(function () {
    $this->actingAs(User::factory()->create());
});

test('can render index page', function () {
    $response = $this->get(route('akun.index'));
    $response->assertStatus(200);
});

test('can render edit page', function () {
    $akun = User::factory()->create();
    $response = $this->get(route('akun.edit', $akun));
    $response->assertStatus(200);
    $response->assertViewHas('akun', $akun);
});

test('can update user name and email', function () {
    $akun = User::factory()->create([
        'name' => 'Old Name',
        'email' => 'old@example.com',
    ]);

    $response = $this->put(route('akun.update', $akun), [
        'name' => 'New Name',
        'email' => 'new@example.com',
    ]);

    $response->assertRedirect(route('akun.index'));

    $this->assertDatabaseHas('users', [
        'id' => $akun->id,
        'name' => 'New Name',
        'email' => 'new@example.com',
    ]);
});

test('can update user password', function () {
    $akun = User::factory()->create([
        'password' => 'password123',
    ]);
    $oldPassword = $akun->password;

    $response = $this->put(route('akun.update', $akun), [
        'name' => $akun->name,
        'email' => $akun->email,
        'current_password' => 'password123',
        'password' => 'newpassword123',
        'password_confirmation' => 'newpassword123',
    ]);

    $response->assertRedirect(route('akun.index'));

    $akun->refresh();
    $this->assertNotEquals($oldPassword, $akun->password);
});

test('cannot update email if email is already taken by another user', function () {
    $otherUser = User::factory()->create([
        'email' => 'taken@example.com',
    ]);

    $akun = User::factory()->create([
        'email' => 'myemail@example.com',
    ]);

    $response = $this->from(route('akun.edit', $akun))->put(route('akun.update', $akun), [
        'name' => $akun->name,
        'email' => 'taken@example.com',
    ]);

    $response->assertRedirect(route('akun.edit', $akun));
    $response->assertSessionHasErrors(['email']);

    $this->assertDatabaseHas('users', [
        'id' => $akun->id,
        'email' => 'myemail@example.com',
    ]);
});

test('can delete user', function () {
    $akun = User::factory()->create();

    $response = $this->delete(route('akun.destroy', $akun));

    $response->assertRedirect(route('akun.index'));

    $this->assertDatabaseMissing('users', [
        'id' => $akun->id,
    ]);
});
