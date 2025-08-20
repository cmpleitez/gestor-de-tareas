<?php

namespace App\Services\MachineLearning;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class GeographicRiskAssessment
{
    protected $config;

    public function __construct()
    {
        $this->config = [
            'high_risk_countries' => ['XX', 'YY', 'ZZ'], // Códigos de países de alto riesgo
            'medium_risk_countries' => ['AA', 'BB', 'CC'], // Códigos de países de riesgo medio
            'risk_weights' => [
                'country_risk' => 0.4,
                'region_risk' => 0.3,
                'isp_risk' => 0.2,
                'timezone_risk' => 0.1
            ]
        ];
    }

    /**
     * Evaluar riesgo geográfico de una IP
     */
    public function assessRisk(string $ip): array
    {
        try {
            $cacheKey = "geographic_risk_{$ip}";
            
            if (Cache::has($cacheKey)) {
                return Cache::get($cacheKey);
            }
            
            // Obtener información geográfica de la IP
            $geoInfo = $this->getGeographicInfo($ip);
            
            // Evaluar riesgo por país
            $countryRisk = $this->assessCountryRisk($geoInfo);
            
            // Evaluar riesgo por región
            $regionRisk = $this->assessRegionRisk($geoInfo);
            
            // Evaluar riesgo por ISP
            $ispRisk = $this->assessISPRisk($geoInfo);
            
            // Evaluar riesgo por zona horaria
            $timezoneRisk = $this->assessTimezoneRisk($geoInfo);
            
            // Calcular riesgo total
            $totalRisk = $this->calculateTotalRisk([
                'country' => $countryRisk,
                'region' => $regionRisk,
                'isp' => $ispRisk,
                'timezone' => $timezoneRisk
            ]);
            
            // Clasificar nivel de riesgo
            $riskLevel = $this->classifyRiskLevel($totalRisk);
            
            $result = [
                'ip' => $ip,
                'geographic_info' => $geoInfo,
                'risk_components' => [
                    'country' => $countryRisk,
                    'region' => $regionRisk,
                    'isp' => $ispRisk,
                    'timezone' => $timezoneRisk
                ],
                'total_risk' => $totalRisk,
                'risk_level' => $riskLevel,
                'confidence' => $this->calculateConfidence($geoInfo),
                'recommendations' => $this->generateRecommendations($riskLevel, $totalRisk),
                'last_updated' => now()->toISOString()
            ];
            
            // Cache por 1 hora
            Cache::put($cacheKey, $result, now()->addHour());
            
            return $result;
            
        } catch (\Exception $e) {
            Log::error('Geographic Risk Assessment Error', [
                'error' => $e->getMessage(),
                'ip' => $ip
            ]);
            
            return $this->getDefaultRiskResult($ip);
        }
    }

    /**
     * Obtener información geográfica de la IP
     */
    protected function getGeographicInfo(string $ip): array
    {
        // Simulación de datos geográficos
        $countries = ['US', 'CN', 'RU', 'DE', 'GB', 'FR', 'JP', 'BR', 'IN', 'CA'];
        $regions = ['North America', 'Asia', 'Europe', 'South America', 'Africa'];
        $isps = ['Comcast', 'China Telecom', 'Rostelecom', 'Deutsche Telekom', 'BT'];
        $timezones = ['UTC-8', 'UTC+8', 'UTC+3', 'UTC+1', 'UTC+0'];
        
        return [
            'country_code' => $countries[array_rand($countries)],
            'country_name' => 'Sample Country',
            'region' => $regions[array_rand($regions)],
            'city' => 'Sample City',
            'isp' => $isps[array_rand($isps)],
            'timezone' => $timezones[array_rand($timezones)],
            'latitude' => rand(-90, 90),
            'longitude' => rand(-180, 180)
        ];
    }

    /**
     * Evaluar riesgo por país
     */
    protected function assessCountryRisk(array $geoInfo): float
    {
        $countryCode = $geoInfo['country_code'] ?? '';
        
        if (in_array($countryCode, $this->config['high_risk_countries'])) {
            return 90;
        } elseif (in_array($countryCode, $this->config['medium_risk_countries'])) {
            return 60;
        }
        
        // Evaluación basada en factores específicos del país
        $riskFactors = [
            'US' => 20, // Bajo riesgo
            'CN' => 70, // Alto riesgo
            'RU' => 80, // Alto riesgo
            'DE' => 30, // Bajo riesgo
            'GB' => 25, // Bajo riesgo
            'FR' => 30, // Bajo riesgo
            'JP' => 20, // Bajo riesgo
            'BR' => 50, // Riesgo medio
            'IN' => 60, // Riesgo medio-alto
            'CA' => 20  // Bajo riesgo
        ];
        
        return $riskFactors[$countryCode] ?? 50;
    }

    /**
     * Evaluar riesgo por región
     */
    protected function assessRegionRisk(array $geoInfo): float
    {
        $region = $geoInfo['region'] ?? '';
        
        $regionRisks = [
            'North America' => 25,
            'Europe' => 30,
            'Asia' => 65,
            'South America' => 55,
            'Africa' => 70,
            'Oceania' => 35
        ];
        
        return $regionRisks[$region] ?? 50;
    }

    /**
     * Evaluar riesgo por ISP
     */
    protected function assessISPRisk(array $geoInfo): float
    {
        $isp = $geoInfo['isp'] ?? '';
        
        // Evaluación basada en reputación del ISP
        $ispRisks = [
            'Comcast' => 30,
            'China Telecom' => 75,
            'Rostelecom' => 80,
            'Deutsche Telekom' => 25,
            'BT' => 30
        ];
        
        return $ispRisks[$isp] ?? 50;
    }

    /**
     * Evaluar riesgo por zona horaria
     */
    protected function assessTimezoneRisk(array $geoInfo): float
    {
        $timezone = $geoInfo['timezone'] ?? '';
        
        // Evaluación basada en patrones de actividad sospechosa por zona horaria
        $timezoneRisks = [
            'UTC-8' => 40,  // Costa Oeste US
            'UTC+8' => 70,  // Asia Oriental
            'UTC+3' => 75,  // Rusia, Medio Oriente
            'UTC+1' => 35,  // Europa Central
            'UTC+0' => 30   // Reino Unido, Portugal
        ];
        
        return $timezoneRisks[$timezone] ?? 50;
    }

    /**
     * Calcular riesgo total ponderado
     */
    protected function calculateTotalRisk(array $riskComponents): float
    {
        $totalRisk = 0;
        $totalWeight = 0;
        
        foreach ($this->config['risk_weights'] as $component => $weight) {
            if (isset($riskComponents[$component])) {
                $totalRisk += $riskComponents[$component] * $weight;
                $totalWeight += $weight;
            }
        }
        
        if ($totalWeight === 0) return 50;
        
        return $totalRisk / $totalWeight;
    }

    /**
     * Clasificar nivel de riesgo
     */
    protected function classifyRiskLevel(float $totalRisk): string
    {
        if ($totalRisk >= 80) return 'critical';
        if ($totalRisk >= 60) return 'high';
        if ($totalRisk >= 40) return 'medium';
        if ($totalRisk >= 20) return 'low';
        return 'minimal';
    }

    /**
     * Calcular confianza de la evaluación
     */
    protected function calculateConfidence(array $geoInfo): float
    {
        $confidenceFactors = [];
        
        // Confianza basada en completitud de datos
        $dataCompleteness = 0;
        $totalFields = 0;
        
        foreach ($geoInfo as $field => $value) {
            $totalFields++;
            if (!empty($value) || $value === 0) {
                $dataCompleteness++;
            }
        }
        
        if ($totalFields > 0) {
            $confidenceFactors[] = $dataCompleteness / $totalFields;
        }
        
        // Confianza basada en calidad de datos
        if (isset($geoInfo['country_code']) && strlen($geoInfo['country_code']) === 2) {
            $confidenceFactors[] = 0.9;
        } else {
            $confidenceFactors[] = 0.5;
        }
        
        if (isset($geoInfo['latitude']) && isset($geoInfo['longitude'])) {
            if ($geoInfo['latitude'] >= -90 && $geoInfo['latitude'] <= 90 &&
                $geoInfo['longitude'] >= -180 && $geoInfo['longitude'] <= 180) {
                $confidenceFactors[] = 0.9;
            } else {
                $confidenceFactors[] = 0.3;
            }
        }
        
        if (empty($confidenceFactors)) return 0.7;
        
        return array_sum($confidenceFactors) / count($confidenceFactors);
    }

    /**
     * Generar recomendaciones basadas en el riesgo
     */
    protected function generateRecommendations(string $riskLevel, float $totalRisk): array
    {
        $recommendations = [];
        
        switch ($riskLevel) {
            case 'critical':
                $recommendations[] = 'Immediate IP blocking required';
                $recommendations[] = 'Investigate for geographic anomalies';
                $recommendations[] = 'Update firewall rules for this region';
                $recommendations[] = 'Notify security team immediately';
                break;
                
            case 'high':
                $recommendations[] = 'Implement enhanced monitoring';
                $recommendations[] = 'Consider geographic restrictions';
                $recommendations[] = 'Review access patterns from this region';
                $recommendations[] = 'Set up automated alerts';
                break;
                
            case 'medium':
                $recommendations[] = 'Monitor geographic behavior';
                $recommendations[] = 'Log requests from this region';
                $recommendations[] = 'Set up periodic reviews';
                break;
                
            case 'low':
                $recommendations[] = 'Continue normal monitoring';
                $recommendations[] = 'Log for trend analysis';
                break;
                
            default:
                $recommendations[] = 'No immediate action required';
                $recommendations[] = 'Continue standard security practices';
                break;
        }
        
        return $recommendations;
    }

    /**
     * Resultado por defecto en caso de error
     */
    protected function getDefaultRiskResult(string $ip): array
    {
        return [
            'ip' => $ip,
            'geographic_info' => [],
            'risk_components' => [
                'country' => 50,
                'region' => 50,
                'isp' => 50,
                'timezone' => 50
            ],
            'total_risk' => 50,
            'risk_level' => 'unknown',
            'confidence' => 0.5,
            'recommendations' => ['Use fallback assessment', 'Monitor IP behavior'],
            'last_updated' => now()->toISOString()
        ];
    }
}
