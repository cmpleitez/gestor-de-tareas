<?php

return [
    /*
    |--------------------------------------------------------------------------
    | CONFIGURACIÓN DEL SISTEMA DE MONITOREO DE SEGURIDAD
    |--------------------------------------------------------------------------
    |
    | Este archivo contiene la configuración para el sistema avanzado de
    | monitoreo de seguridad con Machine Learning y análisis de amenazas.
    |
    */

    // ========================================
    // CONFIGURACIÓN GENERAL DEL SISTEMA
    // ========================================
    'enabled' => env('SECURITY_MONITORING_ENABLED', true),
    'debug_mode' => env('SECURITY_DEBUG_MODE', false),
    'log_level' => env('SECURITY_LOG_LEVEL', 'info'),

    // ========================================
    // CONFIGURACIÓN DE ANÁLISIS DE IP
    // ========================================
    'ip_analysis' => [
        'enabled' => true,
        'cache_duration' => 3600, // 1 hora en segundos
        'max_cache_size' => 10000, // Máximo número de IPs en caché
        'geolocation_enabled' => true,
        'asn_lookup_enabled' => true,
        'reputation_threshold' => [
            'critical' => 80,
            'high' => 60,
            'medium' => 40,
            'low' => 20,
            'minimal' => 0
        ]
    ],

    // ========================================
    // CONFIGURACIÓN DE INTELIGENCIA DE AMENAZAS
    // ========================================
    'threat_intelligence' => [
        'enabled' => true,
        'sources' => [
            'abuseipdb' => [
                'enabled' => true,
                'api_key' => env('ABUSEIPDB_API_KEY'),
                'base_url' => 'https://api.abuseipdb.com/api/v2',
                'timeout' => 10
            ],
            'virustotal' => [
                'enabled' => true,
                'api_key' => env('VIRUSTOTAL_API_KEY'),
                'base_url' => 'https://www.virustotal.com/vtapi/v2',
                'timeout' => 15
            ],
            'ipqualityscore' => [
                'enabled' => true,
                'api_key' => env('IPQUALITYSCORE_API_KEY'),
                'base_url' => 'https://ipqualityscore.com/api/json',
                'timeout' => 10
            ],
            'ip_api' => [
                'enabled' => true,
                'base_url' => 'http://ip-api.com/json',
                'timeout' => 5
            ]
        ],
        'update_frequency' => 3600, // 1 hora
        'correlation_threshold' => 0.7,
        'ml_enabled' => true
    ],

    // ========================================
    // CONFIGURACIÓN DE DETECCIÓN DE ANOMALÍAS
    // ========================================
    'anomaly_detection' => [
        'enabled' => true,
        'ml_models' => [
            'isolation_forest' => [
                'enabled' => true,
                'contamination' => 0.1,
                'random_state' => 42
            ],
            'one_class_svm' => [
                'enabled' => true,
                'nu' => 0.1,
                'kernel' => 'rbf'
            ],
            'local_outlier_factor' => [
                'enabled' => true,
                'n_neighbors' => 20,
                'contamination' => 0.1
            ]
        ],
        'feature_extraction' => [
            'request_frequency' => true,
            'payload_analysis' => true,
            'user_agent_patterns' => true,
            'geographic_anomalies' => true,
            'timing_patterns' => true
        ],
        'threshold' => 0.6
    ],

    // ========================================
    // CONFIGURACIÓN DE ACCIONES DE SEGURIDAD
    // ========================================
    'security_actions' => [
        'auto_block' => [
            'enabled' => true,
            'threshold' => 90,
            'duration' => 86400, // 24 horas en segundos
            'max_blocks' => 1000
        ],
        'challenge_response' => [
            'enabled' => true,
            'threshold' => 70,
            'captcha_enabled' => true,
            'rate_limiting' => true
        ],
        'enhanced_monitoring' => [
            'enabled' => true,
            'threshold' => 50,
            'log_level' => 'detailed',
            'alert_threshold' => 3
        ],
        'rate_limiting' => [
            'enabled' => true,
            'threshold' => 30,
            'max_requests' => 100,
            'time_window' => 300 // 5 minutos
        ]
    ],

    // ========================================
    // CONFIGURACIÓN DE ALERTAS Y NOTIFICACIONES
    // ========================================
    'alerts' => [
        'enabled' => true,
        'channels' => [
            'email' => [
                'enabled' => true,
                'recipients' => explode(',', env('SECURITY_ALERT_EMAILS', 'admin@example.com')),
                'template' => 'emails.security.alert'
            ],
            'slack' => [
                'enabled' => env('SECURITY_SLACK_ENABLED', false),
                'webhook_url' => env('SECURITY_SLACK_WEBHOOK'),
                'channel' => env('SECURITY_SLACK_CHANNEL', '#security')
            ],
            'webhook' => [
                'enabled' => env('SECURITY_WEBHOOK_ENABLED', false),
                'url' => env('SECURITY_WEBHOOK_URL'),
                'timeout' => 10
            ]
        ],
        'thresholds' => [
            'critical' => 90,
            'high' => 70,
            'medium' => 50,
            'low' => 30
        ],
        'cooldown' => 300 // 5 minutos entre alertas
    ],

    // ========================================
    // CONFIGURACIÓN DE LOGGING Y AUDITORÍA
    // ========================================
    'logging' => [
        'enabled' => true,
        'channels' => [
            'security_events' => [
                'driver' => 'daily',
                'path' => storage_path('logs/security/events.log'),
                'level' => 'info',
                'days' => 30
            ],
            'threat_intelligence' => [
                'driver' => 'daily',
                'path' => storage_path('logs/security/threats.log'),
                'level' => 'info',
                'days' => 90
            ],
            'anomaly_detection' => [
                'driver' => 'daily',
                'path' => storage_path('logs/security/anomalies.log'),
                'level' => 'debug',
                'days' => 30
            ]
        ],
        'retention' => [
            'events' => 90, // días
            'threats' => 365, // días
            'anomalies' => 90 // días
        ]
    ],

    // ========================================
    // CONFIGURACIÓN DE CACHÉ Y PERFORMANCE
    // ========================================
    'cache' => [
        'enabled' => true,
        'driver' => env('SECURITY_CACHE_DRIVER', 'redis'),
        'prefix' => 'security:',
        'ttl' => [
            'ip_reputation' => 3600, // 1 hora
            'threat_intelligence' => 7200, // 2 horas
            'geolocation' => 86400, // 24 horas
            'ml_models' => 86400 // 24 horas
        ]
    ],

    // ========================================
    // CONFIGURACIÓN DE WHITELIST Y BLACKLIST
    // ========================================
    'lists' => [
        'whitelist' => [
            'enabled' => true,
            'ips' => explode(',', env('SECURITY_WHITELIST_IPS', '127.0.0.1,::1')),
            'networks' => explode(',', env('SECURITY_WHITELIST_NETWORKS', '10.0.0.0/8,172.16.0.0/12,192.168.0.0/16')),
            'bypass_security' => true
        ],
        'blacklist' => [
            'enabled' => true,
            'ips' => explode(',', env('SECURITY_BLACKLIST_IPS', '')),
            'networks' => explode(',', env('SECURITY_BLACKLIST_NETWORKS', '')),
            'auto_block' => true
        ]
    ],

    // ========================================
    // CONFIGURACIÓN DE MANTENIMIENTO
    // ========================================
    'maintenance' => [
        'enabled' => env('SECURITY_MAINTENANCE_MODE', false),
        'allowed_ips' => explode(',', env('SECURITY_MAINTENANCE_ALLOWED_IPS', '127.0.0.1')),
        'message' => env('SECURITY_MAINTENANCE_MESSAGE', 'Sistema en mantenimiento por seguridad'),
        'status_code' => 503
    ],

    // ========================================
    // CONFIGURACIÓN DE REPORTES Y DASHBOARD
    // ========================================
    'reports' => [
        'enabled' => true,
        'schedule' => [
            'daily' => '00:00',
            'weekly' => 'monday 00:00',
            'monthly' => 'first day of month 00:00'
        ],
        'retention' => [
            'daily' => 30, // días
            'weekly' => 12, // semanas
            'monthly' => 24 // meses
        ]
    ],

    // ========================================
    // CONFIGURACIÓN DE INTEGRACIÓN
    // ========================================
    'integrations' => [
        'firewall' => [
            'enabled' => env('SECURITY_FIREWALL_INTEGRATION', false),
            'driver' => env('SECURITY_FIREWALL_DRIVER', 'iptables'),
            'auto_update' => true
        ],
        'ids_ips' => [
            'enabled' => env('SECURITY_IDS_INTEGRATION', false),
            'driver' => env('SECURITY_IDS_DRIVER', 'snort'),
            'alert_integration' => true
        ],
        'siem' => [
            'enabled' => env('SECURITY_SIEM_INTEGRATION', false),
            'endpoint' => env('SECURITY_SIEM_ENDPOINT'),
            'format' => 'cef'
        ]
    ]
];
