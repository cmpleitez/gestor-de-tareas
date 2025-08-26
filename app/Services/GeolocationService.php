<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;


class GeolocationService
{
    /**
     * Obtener información geográfica de una IP usando múltiples APIs
     */
    public function getGeolocation(string $ip): array
    {
        // Verificar cache primero
        $cacheKey = "geolocation_{$ip}";
        $cached = Cache::get($cacheKey);

        if ($cached) {
            return $cached;
        }

        try {
            // Intentar con ipapi.co (gratuito, confiable)
            $geolocation = $this->getFromIpApi($ip);

            if (!$geolocation) {
                // Fallback a ipinfo.io
                $geolocation = $this->getFromIpInfo($ip);
            }

            if (!$geolocation) {
                // Fallback a datos básicos
                $geolocation = $this->getBasicGeolocation($ip);
            }

            // Cache por 24 horas
            Cache::put($cacheKey, $geolocation, now()->addHours(24));

            return $geolocation;

        } catch (\Exception $e) {
            return $this->getBasicGeolocation($ip);
        }
    }

    /**
     * Obtener geolocalización desde ipapi.co
     */
    private function getFromIpApi(string $ip): ?array
    {
        try {
            $response = Http::timeout(5)->get("http://ip-api.com/json/{$ip}");

            if ($response->successful()) {
                $data = $response->json();

                if ($data['status'] === 'success') {
                    return [
                        'country' => $data['country'] ?? 'Unknown',
                        'country_code' => $data['countryCode'] ?? 'XX',
                        'region' => $data['regionName'] ?? 'Unknown',
                        'city' => $data['city'] ?? 'Unknown',
                        'latitude' => $data['lat'] ?? 0,
                        'longitude' => $data['lon'] ?? 0,
                        'timezone' => $data['timezone'] ?? 'UTC',
                        'isp' => $data['isp'] ?? 'Unknown',
                        'org' => $data['org'] ?? 'Unknown',
                        'as' => $data['as'] ?? 'Unknown',
                        'query' => $ip,
                        'source' => 'ipapi.co',
                        'timestamp' => now()->toISOString()
                    ];
                }
            }
        } catch (\Exception $e) {
            // Error silencioso con ipapi.co
        }

        return null;
    }

    /**
     * Obtener geolocalización desde ipinfo.io (fallback)
     */
    private function getFromIpInfo(string $ip): ?array
    {
        try {
            $response = Http::timeout(5)->get("https://ipinfo.io/{$ip}/json");

            if ($response->successful()) {
                $data = $response->json();

                if (isset($data['country'])) {
                    $location = explode(',', $data['loc'] ?? '0,0');

                    return [
                        'country' => $data['country'] ?? 'Unknown',
                        'country_code' => $data['country'] ?? 'XX',
                        'region' => $data['region'] ?? 'Unknown',
                        'city' => $data['city'] ?? 'Unknown',
                        'latitude' => $location[0] ?? 0,
                        'longitude' => $location[1] ?? 0,
                        'timezone' => $data['timezone'] ?? 'UTC',
                        'isp' => $data['org'] ?? 'Unknown',
                        'org' => $data['org'] ?? 'Unknown',
                        'as' => 'Unknown',
                        'query' => $ip,
                        'source' => 'ipinfo.io',
                        'timestamp' => now()->toISOString()
                    ];
                }
            }
        } catch (\Exception $e) {
            // Error silencioso con ipinfo.io
        }

        return null;
    }

    /**
     * Obtener geolocalización básica (fallback)
     */
    private function getBasicGeolocation(string $ip): array
    {
        // Detectar si es IP privada
        $isPrivate = filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE);

        if (!$isPrivate) {
            return [
                'country' => 'Unknown',
                'country_code' => 'XX',
                'region' => 'Unknown',
                'city' => 'Unknown',
                'latitude' => 0,
                'longitude' => 0,
                'timezone' => 'UTC',
                'isp' => 'Unknown',
                'org' => 'Unknown',
                'as' => 'Unknown',
                'query' => $ip,
                'source' => 'fallback',
                'timestamp' => now()->toISOString()
            ];
        }

        // IP privada
        return [
            'country' => 'Private Network',
            'country_code' => 'PRIVATE',
            'region' => 'Local',
            'city' => 'Local',
            'latitude' => 0,
            'longitude' => 0,
            'timezone' => 'UTC',
            'isp' => 'Private Network',
            'org' => 'Private Network',
            'as' => 'Private',
            'query' => $ip,
            'source' => 'private',
            'timestamp' => now()->toISOString()
        ];
    }

    /**
     * Actualizar geolocalización de amenazas existentes
     */
    public function updateThreatGeolocation(): int
    {
        try {
            $threats = \App\Models\ThreatIntelligence::whereNull('geographic_origin')
                ->orWhere('geographic_origin', '')
                ->take(100) // Procesar en lotes
                ->get();

            $updated = 0;

            foreach ($threats as $threat) {
                $geolocation = $this->getGeolocation($threat->ip_address);

                if ($geolocation) {
                    $threat->update([
                        'country_code' => $geolocation['country_code'],
                        'geographic_origin' => $geolocation['country'],
                        'latitude' => $geolocation['latitude'],
                        'longitude' => $geolocation['longitude'],
                        'timezone' => $geolocation['timezone'],
                        'isp' => $geolocation['isp'],
                        'organization' => $geolocation['org'],
                        'asn' => $geolocation['as']
                    ]);

                    $updated++;
                }

                // Pausa para no sobrecargar las APIs
                usleep(100000); // 0.1 segundos
            }

            return $updated;

        } catch (\Exception $e) {
            return 0;
        }
    }
}
