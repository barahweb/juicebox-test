<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\WeatherException;
use App\Http\Requests\WeatherRequest;
use App\Services\WeatherService;

class WeatherController extends ApiController
{
    public function __construct(private WeatherService $weatherService)
    {
    }

    public function index(WeatherRequest $request)
    {
        $validated = $request->validated();

        try {
            $weather = $this->weatherService->getCurrentWeather($validated['city'] ?? WeatherService::DEFAULT_CITY);
        } catch (WeatherException $e) {
            return $this->sendError($e->getMessage(), null, $e->statusCode);
        }

        return $this->sendResponse($weather, 'Data cuaca berhasil diambil.');
    }
}
