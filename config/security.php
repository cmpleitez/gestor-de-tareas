<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Configuración del Sistema de Seguridad Compactado
    |--------------------------------------------------------------------------
    |
    | Configuración optimizada para uso moderado en Forge/DigitalOcean
    | Deshabilita servicios pesados no utilizados para reducir consumo de recursos
    |
     */

    // ========================================
    // SERVICIOS HABILITADOS (Solo los esenciales)
    // ========================================
    'enabled_services' => [
        'simple_security' => true, // Servicio básico de seguridad
        'cache_management' => true, // Gestión de cache optimizada
        'event_logging' => true, // Logging básico de eventos
        'ip_blocking' => true, // Bloqueo básico de IPs
    ],

    // ========================================
    // SERVICIOS DESHABILITADOS (Pesados, no utilizados)
    // ========================================
    'disabled_services' => [
        'anomaly_detection' => true, // Machine Learning - NO USADO
        'threat_intelligence' => true, // Correlación compleja - NO USADO
        'ip_reputation' => true, // Múltiples fuentes externas - NO USADO
        'machine_learning' => true, // Algoritmos ML pesados - NO USADO
    ],

    // ========================================
    // CONFIGURACIÓN DE CACHE OPTIMIZADA
    // ========================================
    'cache' => [
        'default_duration' => 3600, // 1 hora por defecto
        'ip_risk_duration' => 7200, // 2 horas para riesgo de IP
        'cleanup_interval' => 21600, // 6 horas para limpieza
        'max_cache_size' => '100MB', // Límite máximo de cache
    ],

    // ========================================
    // LIMPIEZA AUTOMÁTICA
    // ========================================
    'cleanup' => [
        'events_retention_days' => 30, // Mantener eventos solo 30 días
        'cache_cleanup_hours' => 6, // Limpiar cache cada 6 horas
        'logs_retention_days' => 15, // Mantener logs solo 15 días
    ],

    // ========================================
    // MONITOREO DE RECURSOS
    // ========================================
    'resource_monitoring' => [
        'enabled' => true,
        'check_interval_minutes' => 60, // Verificar cada hora
        'max_memory_usage' => '80%', // Alerta si supera 80%
        'max_disk_usage' => '85%', // Alerta si supera 85%
    ],
];
