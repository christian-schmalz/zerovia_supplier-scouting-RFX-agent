<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class GeocodingService
{
    private const NOMINATIM_URL = 'https://nominatim.openstreetmap.org/search';
    private const CACHE_TTL_SECONDS = 86400 * 30; // 30 days
    private const DEFAULT_LAT = 47.3769; // Zürich
    private const DEFAULT_LNG = 8.5417;

    /**
     * Geocode a location string (city name or postal code) to lat/lng.
     *
     * @return array{lat: float, lng: float}
     */
    public function geocode(string $location): array
    {
        $cacheKey = 'geocode:' . mb_strtolower(trim($location));

        return Cache::remember($cacheKey, self::CACHE_TTL_SECONDS, function () use ($location) {
            return $this->fetchCoordinates($location);
        });
    }

    /**
     * @return array{lat: float, lng: float}
     */
    private function fetchCoordinates(string $location): array
    {
        try {
            $response = Http::timeout(5)
                ->withHeaders(['User-Agent' => 'ZeroviaSupplierScouting/1.0'])
                ->get(self::NOMINATIM_URL, [
                    'q'      => $location,
                    'format' => 'jsonv2',
                    'limit'  => 1,
                ]);

            $data = $response->json();
            if ($response->successful() && !empty($data)) {
                $result = $data[0];

                return [
                    'lat' => (float) $result['lat'],
                    'lng' => (float) $result['lon'],
                ];
            }
        } catch (\Throwable $e) {
            report($e);
        }

        return [
            'lat' => self::DEFAULT_LAT,
            'lng' => self::DEFAULT_LNG,
        ];
    }
}
