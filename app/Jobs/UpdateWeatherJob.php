<?php

namespace App\Jobs;

use App\Services\WeatherService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Throwable;

class UpdateWeatherJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(private string $city = WeatherService::DEFAULT_CITY)
    {
    }

    public function handle(WeatherService $weatherService): void
    {
        try {
            $weatherService->refreshWeather($this->city);
        } catch (Throwable $e) {
            Log::error("Gagal memperbarui data cuaca untuk kota {$this->city}: {$e->getMessage()}");
        }
    }
}
