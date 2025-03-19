<?php

declare(strict_types=1);

namespace Harryes\SentinelLog\Services;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Cache;

class GeolocationService
{
    protected Client $client;

    public function __construct()
    {
        $this->client = new Client(['base_uri' => 'http://ip-api.com/json/']);
    }

    /**
     * Get geolocation data for an IP address.
     */
    public function getLocation(string $ip): ?array
    {
        if ($ip === '127.0.0.1' || $ip === '::1') {
            return ['country' => 'Local', 'city' => 'Localhost'];
        }

        try {
            $cacheKey = "sentinel_log_geo_{$ip}";
            $data = Cache::remember($cacheKey, 3600, function () use ($ip) {
                $response = $this->client->get($ip);
                return json_decode($response->getBody()->getContents(), true);
            });

            return [
                'country' => $data['country'] ?? null,
                'region' => $data['regionName'] ?? null,
                'city' => $data['city'] ?? null,
                'lat' => $data['lat'] ?? null,
                'lon' => $data['lon'] ?? null,
            ];
        } catch (\Exception $e) {
            return null;
        }
    }
}