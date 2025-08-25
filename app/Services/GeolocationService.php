<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeolocationService
{
    protected $config = [
        'cache_duration' => 86400, // 24 horas
        'api_timeout' => 5, // 5 segundos
        'max_retries' => 2
    ];

    /**
     * Obtener información geográfica de una IP
     */
    public function getIPGeolocation(string $ip): array
    {
        // Verificar cache primero
        $cacheKey = "ip_geolocation_{$ip}";
        if (Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }

        try {
            // Usar IP-API (gratuita, sin API key)
            $geolocation = $this->queryIPAPI($ip);
            
            if ($geolocation) {
                // Cache por 24 horas
                Cache::put($cacheKey, $geolocation, now()->addHours(24));
                return $geolocation;
            }
        } catch (\Exception $e) {
            Log::error("Error obteniendo geolocalización para IP {$ip}: " . $e->getMessage());
        }

        // Retornar datos por defecto si falla
        return $this->getDefaultGeolocation($ip);
    }

    /**
     * Consultar IP-API para obtener geolocalización
     */
    protected function queryIPAPI(string $ip): ?array
    {
        try {
            $response = Http::timeout($this->config['api_timeout'])
                ->get("http://ip-api.com/json/{$ip}");

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
                        'source' => 'ip-api.com',
                        'timestamp' => now()->toISOString()
                    ];
                }
            }
        } catch (\Exception $e) {
            Log::error("Error consultando IP-API para {$ip}: " . $e->getMessage());
        }

        return null;
    }

    /**
     * Obtener geolocalización por defecto
     */
    protected function getDefaultGeolocation(string $ip): array
    {
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
            'source' => 'default',
            'timestamp' => now()->toISOString()
        ];
    }

    /**
     * Obtener solo el país de una IP
     */
    public function getIPCountry(string $ip): string
    {
        $geolocation = $this->getIPGeolocation($ip);
        return $geolocation['country'] ?? 'Unknown';
    }

    /**
     * Obtener código de país de una IP
     */
    public function getIPCountryCode(string $ip): string
    {
        $geolocation = $this->getIPGeolocation($ip);
        return $geolocation['country_code'] ?? 'XX';
    }

    /**
     * Verificar si una IP es de un país específico
     */
    public function isIPFromCountry(string $ip, string $countryCode): bool
    {
        $geolocation = $this->getIPGeolocation($ip);
        return strtoupper($geolocation['country_code'] ?? '') === strtoupper($countryCode);
    }

    /**
     * Limpiar cache expirado
     */
    public function cleanupExpiredCache(): void
    {
        // El cache se limpia automáticamente por Laravel
        // Este método está aquí para futuras implementaciones
    }
}
