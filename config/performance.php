<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Performance Configuration
    |--------------------------------------------------------------------------
    |
    | Configuraciones específicas para optimizar el rendimiento del servidor
    | y prevenir errores 502 Bad Gateway en Laragon/Nginx
    |
    */

    /*
    |--------------------------------------------------------------------------
    | PHP Configuration Overrides
    |--------------------------------------------------------------------------
    |
    | Configuraciones PHP que se aplicarán para mejorar el rendimiento
    | y prevenir timeouts
    |
    */

    'php' => [
        // Tiempo máximo de ejecución (0 = sin límite)
        'max_execution_time' => env('PHP_MAX_EXECUTION_TIME', 300),
        
        // Límite de memoria (aumentado para operaciones pesadas)
        'memory_limit' => env('PHP_MEMORY_LIMIT', '1024M'),
        
        // Tiempo máximo de entrada
        'max_input_time' => env('PHP_MAX_INPUT_TIME', 300),
        
        // Tamaño máximo de POST
        'post_max_size' => env('PHP_POST_MAX_SIZE', '256M'),
        
        // Tamaño máximo de archivo subido
        'upload_max_filesize' => env('PHP_UPLOAD_MAX_FILESIZE', '256M'),
        
        // Número máximo de archivos subidos
        'max_file_uploads' => env('PHP_MAX_FILE_UPLOADS', 20),
        
        // Tiempo de espera para operaciones de base de datos
        'default_socket_timeout' => env('PHP_DEFAULT_SOCKET_TIMEOUT', 60),
        
        // Configuración de sesión
        'session.gc_maxlifetime' => env('PHP_SESSION_GC_MAXLIFETIME', 1440),
        'session.cookie_lifetime' => env('PHP_SESSION_COOKIE_LIFETIME', 0),
    ],

    /*
    |--------------------------------------------------------------------------
    | Database Configuration
    |--------------------------------------------------------------------------
    |
    | Configuraciones específicas para la base de datos
    |
    */

    'database' => [
        // Tiempo de espera para conexiones de base de datos
        'timeout' => env('DB_TIMEOUT', 60),
        
        // Número máximo de conexiones en el pool
        'max_connections' => env('DB_MAX_CONNECTIONS', 100),
        
        // Tiempo de vida de las conexiones
        'connection_lifetime' => env('DB_CONNECTION_LIFETIME', 3600),
    ],

    /*
    |--------------------------------------------------------------------------
    | Cache Configuration
    |--------------------------------------------------------------------------
    |
    | Configuraciones para optimizar el cache
    |
    */

    'cache' => [
        // Tiempo de vida por defecto del cache
        'default_ttl' => env('CACHE_DEFAULT_TTL', 3600),
        
        // Tamaño máximo del cache en memoria
        'max_memory' => env('CACHE_MAX_MEMORY', '512M'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Queue Configuration
    |--------------------------------------------------------------------------
    |
    | Configuraciones para las colas de trabajo
    |
    */

    'queue' => [
        // Número de workers simultáneos
        'workers' => env('QUEUE_WORKERS', 4),
        
        // Tiempo de espera para jobs
        'timeout' => env('QUEUE_TIMEOUT', 60),
        
        // Tiempo de espera para jobs largos
        'long_timeout' => env('QUEUE_LONG_TIMEOUT', 300),
        
        // Número máximo de intentos
        'max_attempts' => env('QUEUE_MAX_ATTEMPTS', 3),
    ],

    /*
    |--------------------------------------------------------------------------
    | Session Configuration
    |--------------------------------------------------------------------------
    |
    | Configuraciones para optimizar las sesiones
    |
    */

    'session' => [
        // Tiempo de vida de la sesión
        'lifetime' => env('SESSION_LIFETIME', 120),
        
        // Tiempo de expiración de la cookie
        'expire_on_close' => env('SESSION_EXPIRE_ON_CLOSE', false),
        
        // Limpiar sesiones cada X minutos
        'cleanup_interval' => env('SESSION_CLEANUP_INTERVAL', 60),
    ],

    /*
    |--------------------------------------------------------------------------
    | Logging Configuration
    |--------------------------------------------------------------------------
    |
    | Configuraciones para optimizar el logging
    |
    */

    'logging' => [
        // Nivel de log por defecto
        'level' => env('LOG_LEVEL', 'warning'),
        
        // Tamaño máximo del archivo de log (en MB)
        'max_file_size' => env('LOG_MAX_FILE_SIZE', 100),
        
        // Número máximo de archivos de log
        'max_files' => env('LOG_MAX_FILES', 30),
        
        // Comprimir logs antiguos
        'compress_old_logs' => env('LOG_COMPRESS_OLD_LOGS', true),
    ],

    /*
    |--------------------------------------------------------------------------
    | AJAX Configuration
    |--------------------------------------------------------------------------
    |
    | Configuraciones específicas para requests AJAX
    |
    */

    'ajax' => [
        // Tiempo de espera para requests AJAX
        'timeout' => env('AJAX_TIMEOUT', 30000),
        
        // Número máximo de requests simultáneos
        'max_concurrent_requests' => env('AJAX_MAX_CONCURRENT', 10),
        
        // Tiempo de espera entre requests
        'request_delay' => env('AJAX_REQUEST_DELAY', 100),
    ],

    /*
    |--------------------------------------------------------------------------
    | Monitoring Configuration
    |--------------------------------------------------------------------------
    |
    | Configuraciones para monitoreo de rendimiento
    |
    */

    'monitoring' => [
        // Habilitar monitoreo de rendimiento
        'enabled' => env('PERFORMANCE_MONITORING', true),
        
        // Umbral de tiempo de respuesta (en ms)
        'response_time_threshold' => env('RESPONSE_TIME_THRESHOLD', 5000),
        
        // Umbral de uso de memoria (en MB)
        'memory_usage_threshold' => env('MEMORY_USAGE_THRESHOLD', 512),
        
        // Intervalo de monitoreo (en segundos)
        'monitoring_interval' => env('MONITORING_INTERVAL', 60),
    ],
]; 