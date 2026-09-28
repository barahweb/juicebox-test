<?php

use App\Models\Entity\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Laravel\Sanctum\Sanctum;

beforeEach(function () {
    Cache::flush();
});

it('bisa ambil data cuaca dari API eksternal (di-mock)', function () {
    Sanctum::actingAs(User::factory()->create());

    Http::fake([
        'api.openweathermap.org/*' => Http::response([
            'name' => 'Jakarta',
            'sys' => ['country' => 'ID'],
            'main' => ['temp' => 28.5, 'feels_like' => 30.1, 'humidity' => 66],
            'weather' => [['description' => 'light rain']],
            'wind' => ['speed' => 3.09],
        ], 200),
    ]);

    $response = $this->getJson('/api/weather');

    $response->assertOk()
        ->assertJson(['success' => true])
        ->assertJsonPath('data.city', 'Jakarta')
        ->assertJsonPath('data.country', 'ID')
        ->assertJsonPath('data.temperature', 28.5)
        ->assertJsonPath('data.description', 'light rain');

    Http::assertSent(function ($request) {
        return str_contains($request->url(), 'api.openweathermap.org')
            && $request['q'] === 'Jakarta';
    });
});

it('bisa ambil data cuaca untuk kota lain lewat query string', function () {
    Sanctum::actingAs(User::factory()->create());

    Http::fake([
        'api.openweathermap.org/*' => Http::response([
            'name' => 'Bandung',
            'sys' => ['country' => 'ID'],
            'main' => ['temp' => 24, 'feels_like' => 25, 'humidity' => 80],
            'weather' => [['description' => 'scattered clouds']],
            'wind' => ['speed' => 1.5],
        ], 200),
    ]);

    $response = $this->getJson('/api/weather?city=Bandung');

    $response->assertOk()->assertJsonPath('data.city', 'Bandung');

    Http::assertSent(fn ($request) => $request['q'] === 'Bandung');
});

it('balikin 404 kalau kota tidak ditemukan', function () {
    Sanctum::actingAs(User::factory()->create());

    Http::fake([
        'api.openweathermap.org/*' => Http::response(['message' => 'city not found'], 404),
    ]);

    $response = $this->getJson('/api/weather?city=KotaGaibXYZ');

    $response->assertNotFound()
        ->assertJson([
            'success' => false,
            'message' => 'Kota tidak ditemukan.',
        ]);
});

it('balikin 502 kalau API cuaca eksternal gagal', function () {
    Sanctum::actingAs(User::factory()->create());

    Http::fake([
        'api.openweathermap.org/*' => Http::response([], 500),
    ]);

    $response = $this->getJson('/api/weather');

    $response->assertStatus(502)
        ->assertJson([
            'success' => false,
            'message' => 'Gagal mengambil data cuaca.',
        ]);
});

it('tidak manggil API eksternal kalau data sudah ada di cache', function () {
    Sanctum::actingAs(User::factory()->create());

    Cache::put('weather.jakarta', [
        'city' => 'Jakarta',
        'country' => 'ID',
        'temperature' => 99,
        'feels_like' => 99,
        'humidity' => 10,
        'description' => 'data dari cache',
        'wind_speed' => 1,
        'updated_at' => now()->toIso8601String(),
    ], now()->addHour());

    Http::fake();

    $response = $this->getJson('/api/weather');

    $response->assertOk()->assertJsonPath('data.temperature', 99);

    Http::assertNothingSent();
});

it('tidak bisa akses endpoint weather tanpa login', function () {
    $response = $this->getJson('/api/weather');

    $response->assertUnauthorized();
});
