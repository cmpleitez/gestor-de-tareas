# Script de optimizacion para Laragon/Nginx
# Ejecutar como administrador

Write-Host "Iniciando optimizacion del servidor Laragon..." -ForegroundColor Green

# Verificar si se ejecuta como administrador
if (-NOT ([Security.Principal.WindowsPrincipal] [Security.Principal.WindowsIdentity]::GetCurrent()).IsInRole([Security.Principal.WindowsBuiltInRole] "Administrator")) {
    Write-Host "Este script debe ejecutarse como administrador" -ForegroundColor Red
    exit 1
}

# Variables de configuracion
$laragonPath = "C:\laragon"
$nginxConfigPath = "$laragonPath\etc\nginx\sites-enabled"
$phpFpmConfigPath = "$laragonPath\bin\php"
$logsPath = "$laragonPath\logs"

# Crear directorios de logs si no existen
Write-Host "Creando directorios de logs..." -ForegroundColor Yellow
if (!(Test-Path "$logsPath\nginx")) {
    New-Item -ItemType Directory -Path "$logsPath\nginx" -Force
}
if (!(Test-Path "$logsPath\php-fpm")) {
    New-Item -ItemType Directory -Path "$logsPath\php-fpm" -Force
}

# Detener servicios
Write-Host "Deteniendo servicios..." -ForegroundColor Yellow
& "$laragonPath\bin\nginx\nginx.exe" -s stop 2>$null
& "$laragonPath\bin\php\php-cgi.exe" -s stop 2>$null

# Configurar Nginx
Write-Host "Configurando Nginx..." -ForegroundColor Yellow
$nginxConfig = @"
server {
    listen 80;
    server_name gestor-de-tareas.test;
    root "C:/laragon/www/gestor-de-tareas/public";
    
    client_max_body_size 256M;
    client_body_timeout 300s;
    client_header_timeout 300s;
    
    location ~ \.php$ {
        fastcgi_pass 127.0.0.1:9000;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME `$document_root`$fastcgi_script_name;
        include fastcgi_params;
        
        fastcgi_read_timeout 300s;
        fastcgi_send_timeout 300s;
        fastcgi_connect_timeout 60s;
        fastcgi_buffer_size 128k;
        fastcgi_buffers 4 256k;
        fastcgi_busy_buffers_size 256k;
        fastcgi_temp_file_write_size 256k;
        fastcgi_intercept_errors on;
        
        fastcgi_param PHP_VALUE "memory_limit=1024M`nmax_execution_time=300`nmax_input_time=300";
    }
    
    location ~* \.(css|js|png|jpg|jpeg|gif|ico|svg|woff|woff2|ttf|eot)$ {
        expires 1y;
        add_header Cache-Control "public, immutable";
        access_log off;
    }
    
    location / {
        try_files `$uri `$uri/ /index.php?`$query_string;
        
        add_header X-Frame-Options "SAMEORIGIN" always;
        add_header X-Content-Type-Options "nosniff" always;
        add_header X-XSS-Protection "1; mode=block" always;
        add_header Referrer-Policy "no-referrer-when-downgrade" always;
    }
    
    access_log "C:/laragon/logs/nginx/gestor-de-tareas.access.log";
    error_log "C:/laragon/logs/nginx/gestor-de-tareas.error.log";
    
    gzip on;
    gzip_vary on;
    gzip_min_length 1024;
    gzip_proxied any;
    gzip_comp_level 6;
    gzip_types
        text/plain
        text/css
        text/xml
        text/javascript
        application/json
        application/javascript
        application/xml+rss
        application/atom+xml
        image/svg+xml;
}
"@

$nginxConfig | Out-File -FilePath "$nginxConfigPath\gestor-de-tareas.conf" -Encoding UTF8

# Encontrar la version de PHP instalada
$phpVersions = Get-ChildItem "$phpFpmConfigPath" -Directory | Where-Object { $_.Name -match "php-\d+\.\d+\.\d+" }
if ($phpVersions) {
    $latestPhpVersion = $phpVersions | Sort-Object Name -Descending | Select-Object -First 1
    $phpFpmConfigFile = "$($latestPhpVersion.FullName)\php-fpm.conf"
    
    Write-Host "Configurando PHP-FPM ($($latestPhpVersion.Name))..." -ForegroundColor Yellow
    
    # Crear configuracion PHP-FPM optimizada
    $phpFpmConfig = @"
[global]
pid = C:/laragon/tmp/php-fpm.pid
error_log = C:/laragon/logs/php-fpm/php-fpm.log
daemonize = no

[www]
user = daemon
group = daemon

listen = 127.0.0.1:9000
listen.owner = daemon
listen.group = daemon
listen.mode = 0660

pm = dynamic
pm.max_children = 50
pm.start_servers = 5
pm.min_spare_servers = 5
pm.max_spare_servers = 35
pm.max_requests = 1000

request_terminate_timeout = 300s
request_slowlog_timeout = 10s

php_admin_value[memory_limit] = 1024M
php_admin_value[max_execution_time] = 300
php_admin_value[max_input_time] = 300
php_admin_value[post_max_size] = 256M
php_admin_value[upload_max_filesize] = 256M

slowlog = C:/laragon/logs/php-fpm/php-fpm-slow.log
php_admin_flag[log_errors] = on
php_admin_value[error_log] = C:/laragon/logs/php-fpm/php-fpm-error.log

php_admin_value[session.gc_maxlifetime] = 1440
php_admin_value[session.cookie_lifetime] = 0

php_admin_value[default_socket_timeout] = 60
php_admin_value[mysql.connect_timeout] = 60
php_admin_value[mysqlnd.net_read_timeout] = 60
"@
    
    $phpFpmConfig | Out-File -FilePath $phpFpmConfigFile -Encoding UTF8
}

# Crear archivo .env optimizado si no existe
$envFile = "C:\laragon\www\gestor-de-tareas\.env"
if (Test-Path $envFile) {
    Write-Host "Optimizando archivo .env..." -ForegroundColor Yellow
    
    # Agregar configuraciones de rendimiento al .env
    $envContent = Get-Content $envFile -Raw
    
    $performanceSettings = @"

# Configuraciones de rendimiento para prevenir errores 502
LOG_LEVEL=warning
CACHE_DRIVER=file
SESSION_DRIVER=file
QUEUE_CONNECTION=sync

# Configuraciones de base de datos
DB_TIMEOUT=60
DB_MAX_CONNECTIONS=100
DB_CONNECTION_LIFETIME=3600

# Configuraciones de cache
CACHE_DEFAULT_TTL=3600
CACHE_MAX_MEMORY=512M

# Configuraciones de colas
QUEUE_WORKERS=4
QUEUE_TIMEOUT=60
QUEUE_LONG_TIMEOUT=300
QUEUE_MAX_ATTEMPTS=3

# Configuraciones de sesion
SESSION_LIFETIME=120
SESSION_EXPIRE_ON_CLOSE=false
SESSION_CLEANUP_INTERVAL=60

# Configuraciones de logging
LOG_MAX_FILE_SIZE=100
LOG_MAX_FILES=30
LOG_COMPRESS_OLD_LOGS=true

# Configuraciones AJAX
AJAX_TIMEOUT=30000
AJAX_MAX_CONCURRENT=10
AJAX_REQUEST_DELAY=100

# Configuraciones de monitoreo
PERFORMANCE_MONITORING=true
RESPONSE_TIME_THRESHOLD=5000
MEMORY_USAGE_THRESHOLD=512
MONITORING_INTERVAL=60
"@
    
    if ($envContent -notmatch "LOG_LEVEL=warning") {
        $envContent += $performanceSettings
        $envContent | Out-File -FilePath $envFile -Encoding UTF8
    }
}

# Crear tarea programada para monitoreo
Write-Host "Configurando monitoreo automatico..." -ForegroundColor Yellow
$monitorScript = @"
# Monitoreo automatico de rendimiento
cd "C:\laragon\www\gestor-de-tareas"
php artisan monitor:performance --log
"@

$monitorScript | Out-File -FilePath "$laragonPath\bin\monitor-performance.bat" -Encoding ASCII

# Crear tarea programada (cada 5 minutos)
$taskName = "GestorTareasPerformanceMonitor"
$taskCommand = "schtasks /create /tn `"$taskName`" /tr `"$laragonPath\bin\monitor-performance.bat`" /sc minute /mo 5 /ru SYSTEM /f"
Invoke-Expression $taskCommand

# Limpiar logs antiguos
Write-Host "Limpiando logs antiguos..." -ForegroundColor Yellow
Get-ChildItem "$logsPath" -Recurse -File | Where-Object { $_.LastWriteTime -lt (Get-Date).AddDays(-7) } | Remove-Item -Force

# Reiniciar servicios
Write-Host "Reiniciando servicios..." -ForegroundColor Yellow
Start-Sleep -Seconds 2

# Verificar configuracion de Nginx
Write-Host "Verificando configuracion de Nginx..." -ForegroundColor Green
& "$laragonPath\bin\nginx\nginx.exe" -t

if ($LASTEXITCODE -eq 0) {
    Write-Host "Configuracion de Nginx valida" -ForegroundColor Green
} else {
    Write-Host "Error en configuracion de Nginx" -ForegroundColor Red
}

# Mostrar resumen
Write-Host "`nResumen de optimizaciones aplicadas:" -ForegroundColor Cyan
Write-Host "  Configuracion de Nginx optimizada" -ForegroundColor Green
Write-Host "  Configuracion de PHP-FPM optimizada" -ForegroundColor Green
Write-Host "  Archivo .env actualizado" -ForegroundColor Green
Write-Host "  Monitoreo automatico configurado" -ForegroundColor Green
Write-Host "  Logs antiguos limpiados" -ForegroundColor Green

Write-Host "`nOptimizacion completada. Reinicia Laragon para aplicar los cambios." -ForegroundColor Green
Write-Host "Para monitorear el rendimiento manualmente: php artisan monitor:performance" -ForegroundColor Yellow 