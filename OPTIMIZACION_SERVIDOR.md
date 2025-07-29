# 🚀 Optimización del Servidor - Gestor de Tareas

## Problema Identificado
El servidor Nginx ("nginext") presenta errores **502 Bad Gateway** después de aproximadamente una hora de uso con múltiples roles simultáneos. Este error indica problemas de agotamiento de recursos, timeouts o configuraciones inadecuadas.

## 🔧 Soluciones Implementadas

### 1. Configuración de Rendimiento (`config/performance.php`)
- **Límites de memoria**: Aumentado a 1024M
- **Tiempos de ejecución**: 300 segundos para operaciones largas
- **Configuraciones de base de datos**: Timeouts y pool de conexiones optimizados
- **Configuraciones de cache**: TTL y límites de memoria
- **Configuraciones AJAX**: Timeouts y límites de requests simultáneos

### 2. Middleware de Optimización (`app/Http/Middleware/PerformanceOptimization.php`)
- Aplica configuraciones PHP automáticamente
- Optimiza headers de respuesta
- Configura cache para recursos estáticos
- Mejora la seguridad con headers adicionales

### 3. Comando de Monitoreo (`app/Console/Commands/MonitorPerformance.php`)
```bash
# Monitoreo manual
php artisan monitor:performance

# Con logging
php artisan monitor:performance --log

# Con alertas
php artisan monitor:performance --alert
```

### 4. Configuración Nginx Optimizada (`laragon-nginx-config.conf`)
- **Timeouts aumentados**: 300s para operaciones largas
- **Buffers optimizados**: 256k para mejor rendimiento
- **Compresión habilitada**: Gzip para archivos estáticos
- **Logging detallado**: Logs separados para debugging

### 5. Script de Automatización (`optimize-server.ps1`)
- Configura automáticamente Nginx y PHP-FPM
- Actualiza archivo `.env` con configuraciones optimizadas
- Crea tarea programada para monitoreo automático
- Limpia logs antiguos

## 📋 Parámetros Críticos para Prevenir 502

### Nginx
```nginx
# Timeouts
fastcgi_read_timeout 300s;
fastcgi_send_timeout 300s;
fastcgi_connect_timeout 60s;

# Buffers
fastcgi_buffer_size 128k;
fastcgi_buffers 4 256k;
fastcgi_busy_buffers_size 256k;

# Tamaño de archivos
client_max_body_size 256M;
```

### PHP-FPM
```ini
# Procesos
pm.max_children = 50
pm.start_servers = 5
pm.min_spare_servers = 5
pm.max_spare_servers = 35

# Timeouts
request_terminate_timeout = 300s
request_slowlog_timeout = 10s

# Memoria
php_admin_value[memory_limit] = 1024M
php_admin_value[max_execution_time] = 300
```

### Laravel (.env)
```env
# Logging
LOG_LEVEL=warning
LOG_MAX_FILE_SIZE=100
LOG_MAX_FILES=30

# Base de datos
DB_TIMEOUT=60
DB_MAX_CONNECTIONS=100

# Cache
CACHE_DEFAULT_TTL=3600
CACHE_MAX_MEMORY=512M

# AJAX
AJAX_TIMEOUT=30000
AJAX_MAX_CONCURRENT=10
```

## 🚀 Instrucciones de Aplicación

### Opción 1: Automática (Recomendada)
1. **Ejecutar como administrador**:
   ```powershell
   Set-ExecutionPolicy -ExecutionPolicy RemoteSigned -Scope CurrentUser
   .\optimize-server.ps1
   ```

2. **Reiniciar Laragon**:
   - Cerrar Laragon completamente
   - Volver a abrir Laragon
   - Verificar que los servicios inicien correctamente

### Opción 2: Manual
1. **Configurar Nginx**:
   - Copiar contenido de `laragon-nginx-config.conf` a `C:\laragon\etc\nginx\sites-enabled\gestor-de-tareas.conf`

2. **Configurar PHP-FPM**:
   - Encontrar la versión de PHP en `C:\laragon\bin\php\`
   - Copiar configuración PHP-FPM del archivo de ejemplo

3. **Actualizar .env**:
   - Agregar las configuraciones de rendimiento al archivo `.env`

4. **Registrar middleware**:
   - Agregar `PerformanceOptimization` al array `$middleware` en `app/Http/Kernel.php`

## 🔍 Monitoreo y Diagnóstico

### Comando de Monitoreo
```bash
php artisan monitor:performance
```

**Verifica**:
- ✅ Uso de memoria (debe estar < 80%)
- ✅ Tiempo de respuesta de base de datos (< 1000ms)
- ✅ Conexiones activas (< 80% del máximo)
- ✅ Espacio en disco (< 90%)
- ✅ Estado del cache

### Logs Importantes
- **Nginx**: `C:\laragon\logs\nginx\gestor-de-tareas.error.log`
- **PHP-FPM**: `C:\laragon\logs\php-fpm\php-fpm-error.log`
- **Laravel**: `storage\logs\laravel.log`

### Monitoreo Automático
El script configura una tarea programada que ejecuta el monitoreo cada 5 minutos:
- **Tarea**: `GestorTareasPerformanceMonitor`
- **Frecuencia**: Cada 5 minutos
- **Logs**: Guardados automáticamente

## 🛠️ Troubleshooting

### Error 502 Persistente
1. **Verificar logs**:
   ```bash
   tail -f C:\laragon\logs\nginx\gestor-de-tareas.error.log
   tail -f C:\laragon\logs\php-fpm\php-fpm-error.log
   ```

2. **Verificar procesos PHP-FPM**:
   ```bash
   tasklist | findstr php
   ```

3. **Reiniciar servicios**:
   ```bash
   # En Laragon
   Stop All → Start All
   ```

### Alto Uso de Memoria
1. **Reducir workers PHP-FPM**:
   ```ini
   pm.max_children = 30
   pm.start_servers = 3
   ```

2. **Optimizar consultas de base de datos**:
   - Revisar queries lentas
   - Implementar cache de consultas
   - Optimizar índices

### Conexiones de Base de Datos Agotadas
1. **Aumentar límite de conexiones**:
   ```sql
   SET GLOBAL max_connections = 200;
   ```

2. **Implementar pool de conexiones**:
   - Usar Redis para cache
   - Implementar colas de trabajo

## 📊 Métricas de Rendimiento

### Umbrales Recomendados
- **Memoria PHP**: < 80% del límite
- **Tiempo de respuesta DB**: < 1000ms
- **Conexiones activas**: < 80% del máximo
- **Espacio en disco**: < 90%
- **Tiempo de respuesta AJAX**: < 5000ms

### Alertas Automáticas
El sistema detecta automáticamente:
- ⚠️ Uso de memoria crítico (> 80%)
- ⚠️ Respuesta lenta de base de datos (> 1000ms)
- ⚠️ Muchas conexiones activas (> 80%)
- ⚠️ Poco espacio en disco (> 90%)
- ⚠️ Errores de cache

## 🔄 Mantenimiento

### Limpieza Semanal
```bash
# Limpiar logs antiguos
php artisan log:clear

# Limpiar cache
php artisan cache:clear
php artisan config:clear

# Optimizar autoloader
composer dump-autoload --optimize
```

### Monitoreo Continuo
- Revisar logs diariamente
- Ejecutar `monitor:performance` semanalmente
- Verificar métricas de rendimiento mensualmente

## 📞 Soporte

Si persisten los errores 502 después de aplicar estas optimizaciones:

1. **Verificar logs detallados**
2. **Ejecutar diagnóstico completo**: `php artisan monitor:performance --log`
3. **Revisar configuración de Laragon**
4. **Verificar recursos del sistema** (CPU, RAM, disco)

---

**Modelo usado: Claude Sonnet 4**

*Estas optimizaciones están diseñadas específicamente para prevenir errores 502 en entornos Laragon/Nginx con aplicaciones Laravel que manejan múltiples usuarios simultáneos.* 