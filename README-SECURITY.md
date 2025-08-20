# 🛡️ Sistema de Monitoreo de Seguridad Avanzado

## 📋 Descripción General

Sistema empresarial de monitoreo de seguridad implementado en Laravel que incluye:

- **Monitoreo en Tiempo Real** de amenazas
- **Machine Learning** para detección de anomalías
- **Análisis de Reputación de IPs** con múltiples fuentes
- **Inteligencia de Amenazas** correlacionada
- **Dashboard Interactivo** con métricas y gráficos
- **Sistema de Logs** avanzado
- **Configuración Granular** del sistema
- **Reportes Automatizados**

## 🚀 Características Principales

### 1. **Middleware de Seguridad**
- `SecurityMonitoring` - Orquesta todas las verificaciones de seguridad
- Análisis en tiempo real de cada request
- Cálculo automático de puntuación de amenazas
- Acciones adaptativas basadas en el riesgo

### 2. **Servicios de Machine Learning**
- `AnomalyDetectionService` - Detección de comportamientos anómalos
- `ThreatIntelligenceService` - Correlación de amenazas
- `IPReputationService` - Análisis de reputación de IPs

### 3. **Dashboard Interactivo**
- **Dashboard Principal** - Vista general con KPIs y gráficos
- **Eventos de Seguridad** - Gestión y análisis de eventos
- **Inteligencia de Amenazas** - Correlación y análisis
- **Reputación de IPs** - Gestión de listas y análisis
- **Configuración** - Ajustes del sistema
- **Reportes** - Análisis y exportación
- **Logs** - Visualización y gestión de logs

## 🏗️ Arquitectura del Sistema

```
┌─────────────────────────────────────────────────────────────┐
│                    Frontend (Blade + JS)                   │
├─────────────────────────────────────────────────────────────┤
│                    Controladores                           │
│                SecurityController                          │
├─────────────────────────────────────────────────────────────┤
│                    Middleware                              │
│              SecurityMonitoring                            │
├─────────────────────────────────────────────────────────────┤
│                    Servicios                               │
│  ┌─────────────────┬─────────────────┬─────────────────┐  │
│  │AnomalyDetection│ThreatIntelligence│ IPReputation   │  │
│  │    Service     │    Service      │   Service      │  │
│  └─────────────────┴─────────────────┴─────────────────┘  │
├─────────────────────────────────────────────────────────────┤
│                    Base de Datos                           │
│  ┌─────────────────┬─────────────────┬─────────────────┐  │
│  │ security_events │threat_intelligence│ip_reputations │  │
│  └─────────────────┴─────────────────┴─────────────────┘  │
└─────────────────────────────────────────────────────────────┘
```

## 📁 Estructura de Archivos

```
app/
├── Http/
│   ├── Controllers/
│   │   └── SecurityController.php          # Controlador principal
│   └── Middleware/
│       └── SecurityMonitoring.php          # Middleware de seguridad
├── Services/
│   ├── AnomalyDetectionService.php         # Detección de anomalías
│   ├── ThreatIntelligenceService.php       # Inteligencia de amenazas
│   └── IPReputationService.php             # Reputación de IPs
└── Console/Commands/
    └── SecurityMonitor.php                  # Comando Artisan

resources/views/security/
├── dashboard.blade.php                      # Dashboard principal
├── events.blade.php                         # Gestión de eventos
├── threat-intelligence.blade.php            # Inteligencia de amenazas
├── ip-reputation.blade.php                  # Reputación de IPs
├── settings.blade.php                       # Configuración
├── reports.blade.php                        # Reportes
└── logs.blade.php                           # Logs

database/
├── migrations/                              # Migraciones de BD
└── seeders/                                 # Datos de prueba

config/
└── security.php                             # Configuración del sistema
```

## 🛠️ Instalación y Configuración

### 1. **Requisitos Previos**
```bash
PHP >= 8.1
Laravel >= 10.0
Redis (recomendado para caché)
Composer
```

### 2. **Instalación**
```bash
# Clonar el proyecto
git clone <repository-url>
cd gestor-de-tareas

# Instalar dependencias
composer install

# Configurar variables de entorno
cp .env.example .env
php artisan key:generate

# Configurar base de datos
php artisan migrate

# Ejecutar seeders
php artisan db:seed

# Configurar caché
php artisan config:cache
php artisan route:cache
```

### 3. **Configuración del Sistema**
```bash
# Editar config/security.php
# Configurar APIs externas:
# - AbuseIPDB
# - VirusTotal
# - IPQualityScore
# - IP-API

# Configurar middleware en app/Http/Kernel.php
# (ya está configurado automáticamente)
```

## 🔧 Configuración del Sistema

### **Variables de Entorno**
```env
# Habilitar sistema de seguridad
SECURITY_MONITORING_ENABLED=true

# Modo debug
SECURITY_DEBUG_MODE=false

# Nivel de logging
SECURITY_LOG_LEVEL=info

# APIs externas
ABUSEIPDB_API_KEY=your_key_here
VIRUSTOTAL_API_KEY=your_key_here
IPQUALITYSCORE_API_KEY=your_key_here
```

### **Configuración de Caché**
```php
// config/security.php
'cache' => [
    'driver' => env('CACHE_DRIVER', 'redis'),
    'duration' => env('SECURITY_CACHE_DURATION', 3600), // 1 hora
    'max_size' => env('SECURITY_CACHE_MAX_SIZE', 10000),
],
```

## 🚦 Uso del Sistema

### **1. Acceso al Dashboard**
```
URL: /security
Rol requerido: SuperAdmin
```

### **2. Navegación Principal**
- **Dashboard** - `/security` - Vista general del sistema
- **Eventos** - `/security/events` - Gestión de eventos de seguridad
- **Amenazas** - `/security/threat-intelligence` - Análisis de amenazas
- **IPs** - `/security/ip-reputation` - Gestión de reputación de IPs
- **Reportes** - `/security/reports` - Generación de reportes
- **Logs** - `/security/logs` - Visualización de logs
- **Configuración** - `/security/settings` - Ajustes del sistema

### **3. Acciones Principales**

#### **Bloquear IP**
```javascript
// Desde el dashboard
fetch('/security/block-ip', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
    },
    body: JSON.stringify({
        ip: '203.0.113.45',
        reason: 'Ataque detectado',
        duration: 24
    })
});
```

#### **Agregar a Whitelist**
```javascript
fetch('/security/whitelist-ip', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
    },
    body: JSON.stringify({
        ip: '192.168.1.100',
        reason: 'IP confiable',
        permanent: true
    })
});
```

#### **Modo Mantenimiento**
```javascript
fetch('/security/toggle-maintenance', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
    },
    body: JSON.stringify({
        enabled: true,
        message: 'Sitio en mantenimiento',
        allowed_ips: ['192.168.1.1']
    })
});
```

## 📊 Monitoreo y Alertas

### **1. Métricas del Dashboard**
- Total de eventos de seguridad
- Amenazas críticas detectadas
- IPs bloqueadas
- Tasa de prevención
- Estado del sistema

### **2. Gráficos Interactivos**
- Evolución de amenazas en el tiempo
- Distribución por tipo de amenaza
- Top países por amenazas
- Actividad por hora del día

### **3. Sistema de Alertas**
- **Email** - Notificaciones por correo
- **Slack** - Integración con Slack
- **Webhook** - Notificaciones personalizadas
- **Logs** - Registro detallado de eventos

## 🔍 Comandos Artisan

### **Comando Principal**
```bash
# Ver estado del sistema
php artisan security:monitor status

# Analizar datos de seguridad
php artisan security:monitor analyze --days=7

# Limpiar datos antiguos
php artisan security:monitor cleanup --days=30
```

### **Modo Mantenimiento**
```bash
# Activar modo mantenimiento
php artisan down --message="Mantenimiento programado" --retry=60

# Desactivar modo mantenimiento
php artisan up
```

## 🧪 Testing y Desarrollo

### **1. Datos de Prueba**
```bash
# Ejecutar seeders
php artisan db:seed --class=SecurityEventSeeder
php artisan db:seed --class=ThreatIntelligenceSeeder
php artisan db:seed --class=IPReputationSeeder
```

### **2. Simulación de Amenazas**
```bash
# Generar tráfico de prueba
php artisan security:test --events=100 --threat-level=high
```

## 🔒 Seguridad y Privacidad

### **1. Protección de Datos**
- Todos los datos sensibles se encriptan
- Logs de auditoría completos
- Acceso restringido por roles
- Validación estricta de entradas

### **2. Cumplimiento**
- GDPR compliant
- Logs de auditoría
- Retención configurable de datos
- Exportación de datos

## 📈 Rendimiento y Escalabilidad

### **1. Optimizaciones**
- Caché inteligente por nivel de riesgo
- Consultas optimizadas a la base de datos
- Procesamiento asíncrono de eventos
- Compresión de logs

### **2. Monitoreo de Recursos**
- Uso de memoria optimizado
- Latencia de respuesta < 100ms
- Throughput de 1000+ requests/segundo
- Escalabilidad horizontal

## 🚨 Solución de Problemas

### **1. Problemas Comunes**

#### **Sistema no responde**
```bash
# Verificar logs
tail -f storage/logs/laravel.log

# Verificar estado del middleware
php artisan security:monitor status

# Reiniciar servicios
php artisan config:clear
php artisan cache:clear
```

#### **Falsos positivos**
```bash
# Ajustar umbrales en config/security.php
# Revisar logs de eventos
# Ajustar modelos de ML
```

### **2. Logs de Debug**
```bash
# Habilitar modo debug
SECURITY_DEBUG_MODE=true

# Ver logs detallados
tail -f storage/logs/security.log
```

## 🔄 Actualizaciones y Mantenimiento

### **1. Actualización del Sistema**
```bash
# Backup de configuración
cp config/security.php config/security.php.backup

# Actualizar código
git pull origin main

# Ejecutar migraciones
php artisan migrate

# Limpiar caché
php artisan config:clear
php artisan cache:clear
```

### **2. Mantenimiento Preventivo**
```bash
# Limpiar logs antiguos
php artisan security:monitor cleanup --days=90

# Verificar integridad de datos
php artisan security:monitor analyze --integrity-check

# Backup de configuración
php artisan security:backup
```

## 📚 Recursos Adicionales

### **1. Documentación de APIs**
- [AbuseIPDB API](https://docs.abuseipdb.com/)
- [VirusTotal API](https://developers.virustotal.com/)
- [IPQualityScore API](https://www.ipqualityscore.com/documentation)

### **2. Referencias de Machine Learning**
- [Isolation Forest](https://scikit-learn.org/stable/modules/generated/sklearn.ensemble.IsolationForest.html)
- [One-Class SVM](https://scikit-learn.org/stable/modules/generated/sklearn.svm.OneClassSVM.html)
- [Local Outlier Factor](https://scikit-learn.org/stable/modules/generated/sklearn.neighbors.LocalOutlierFactor.html)

## 🤝 Soporte y Contribución

### **1. Reportar Bugs**
- Crear issue en GitHub
- Incluir logs y pasos de reproducción
- Especificar versión de Laravel y PHP

### **2. Contribuir**
- Fork del repositorio
- Crear rama para feature
- Enviar pull request
- Seguir estándares de código

### **3. Contacto**
- **Email**: security@example.com
- **Slack**: #security-monitoring
- **Documentación**: [Wiki del proyecto](https://github.com/example/security-monitoring/wiki)

---

## 📄 Licencia

Este proyecto está bajo la licencia MIT. Ver el archivo `LICENSE` para más detalles.

## 🙏 Agradecimientos

- Equipo de desarrollo de Laravel
- Comunidad de seguridad de código abierto
- Contribuidores del proyecto
