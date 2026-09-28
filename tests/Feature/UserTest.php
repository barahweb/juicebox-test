<?php

use App\Models\Entity\User;
use Laravel\Sanctum\Sanctum;

it('bisa register user baru dan dapat token', function () {
    $response = $this->postJson('/api/register', [
        'name' => 'Budi',
        'email' => 'budi@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ]);

    $response->assertCreated()
        ->assertJsonStructure(['success', 'message', 'data' => ['access_token', 'token_type']]);

    $this->assertDatabaseHas('users', ['email' => 'budi@example.com']);
});

it('gagal register kalau email sudah dipakai', function () {
    User::factory()->create(['email' => 'budi@example.com']);

    $response = $this->postJson('/api/register', [
        'name' => 'Budi',
        'email' => 'budi@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ]);

    $response->assertUnprocessable()
        ->assertJsonValidationErrors('email');
});

it('bisa login dengan kredensial yang benar', function () {
    User::factory()->create([
        'email' => 'budi@example.com',
        'password' => bcrypt('password123'),
    ]);

    $response = $this->postJson('/api/login', [
        'email' => 'budi@example.com',
        'password' => 'password123',
    ]);

    $response->assertOk()
        ->assertJsonStructure(['success', 'message', 'data' => ['access_token', 'token_type']]);
});

it('gagal login dengan password salah', function () {
    User::factory()->create([
        'email' => 'budi@example.com',
        'password' => bcrypt('password123'),
    ]);

    $response = $this->postJson('/api/login', [
        'email' => 'budi@example.com',
        'password' => 'password-salah',
    ]);

    $response->assertUnauthorized()
        ->assertJson(['success' => false]);
});

it('bisa logout dan token-nya kehapus dari database', function () {
    $user = User::factory()->create();
    $newToken = $user->createToken('test_token');

    $response = $this->withHeader('Authorization', "Bearer {$newToken->plainTextToken}")
        ->postJson('/api/logout');

    $response->assertOk();

    $this->assertDatabaseMissing('personal_access_tokens', ['id' => $newToken->accessToken->id]);
});

it('bisa lihat detail user lewat id', function () {
    Sanctum::actingAs(User::factory()->create());
    $target = User::factory()->create(['name' => 'Target User']);

    $response = $this->getJson("/api/users/{$target->id}");

    $response->assertOk()
        ->assertJsonPath('data.id', $target->id)
        ->assertJsonPath('data.name', 'Target User');
});

it('dapat 404 kalau user id tidak ditemukan', function () {
    Sanctum::actingAs(User::factory()->create());

    $response = $this->getJson('/api/users/999999');

    $response->assertNotFound()
        ->assertJson(['success' => false]);
});

it('tidak bisa lihat detail user tanpa login', function () {
    $target = User::factory()->create();

    $response = $this->getJson("/api/users/{$target->id}");

    $response->assertUnauthorized();
});
