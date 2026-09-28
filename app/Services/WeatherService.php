<?php

namespace App\Services;

use App\Exceptions\WeatherException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class WeatherService
{
    public const DEFAULT_CITY = 'Perth,AU';

    public function getCurrentWeather(string $city = self::DEFAULT_CITY): array
    {
        return Cache::get($this->cacheKey($city)) ?? $this->refreshWeather($city);
    }

    public function refreshWeather(string $city = self::DEFAULT_CITY): array
    {
        $response = Http::timeout(5)
            ->retry(2, 200, throw: false)
            ->get('https://api.openweathermap.org/data/2.5/weather', [
                'q' => $city,
                'appid' => config('services.openweathermap.key'),
                'units' => 'metric',
            ]);

        if ($response->status() === 404) {
            throw new WeatherException('Kota tidak ditemukan.', 404);
        }

        if ($response->failed()) {
            throw new WeatherException('Gagal mengambil data cuaca.', 502);
        }

        $data = $response->json();

        $weather = [
            'city' => $data['name'],
            'country' => $data['sys']['country'] ?? null,
            'temperature' => $data['main']['temp'],
            'feels_like' => $data['main']['feels_like'],
            'humidity' => $data['main']['humidity'],
            'description' => $data['weather'][0]['description'] ?? null,
            'wind_speed' => $data['wind']['speed'] ?? null,
            'updated_at' => now()->toIso8601String(),
        ];

        Cache::put($this->cacheKey($city), $weather, now()->addMinutes(15));

        return $weather;
    }

    private function cacheKey(string $city): string
    {
        return 'weather.'.strtolower($city);
    }
}
