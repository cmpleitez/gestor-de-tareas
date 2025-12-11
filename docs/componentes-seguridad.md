# Componentes de Seguridad - Documentación

## 📋 Índice

1. [Componentes Blade](#componentes-blade)
2. [Sistema de Notificaciones](#sistema-de-notificaciones)
3. [Utilidades JavaScript](#utilidades-javascript)
4. [Estilos CSS](#estilos-css)
5. [Ejemplos de Uso](#ejemplos-de-uso)
6. [Mejores Prácticas](#mejores-prácticas)

---

## 🎨 Componentes Blade

### 1. `metric-card.blade.php`
Tarjeta reutilizable para mostrar métricas (KPIs) del dashboard.

**Propiedades:**
- `title`: Título de la métrica
- `value`: Valor numérico
- `icon`: Clase del icono FontAwesome
- `color`: Color del borde izquierdo (primary, danger, warning, info, success)
- `trend`: Valor de tendencia opcional
- `trend_direction`: Dirección de la tendencia (up/down)
- `subtitle`: Subtítulo opcional

**Ejemplo:**
```blade
<x-security.metric-card 
    title="Eventos (24h)"
    :value="150"
    icon="fas fa-shield-alt"
    color="primary"
    trend="+12%"
    trend_direction="up"
/>
```

### 2. `chart-card.blade.php`
Tarjeta para contener gráficos con opciones de menú desplegable.

**Propiedades:**
- `title`: Título del gráfico
- `icon`: Clase del icono
- `chart_id`: ID del canvas del gráfico
- `chart_height`: Altura del gráfico (por defecto: 300px)
- `show_dropdown`: Mostrar menú desplegable
- `dropdown_items`: Array de elementos del menú

**Ejemplo:**
```blade
<x-security.chart-card 
    title="Distribución de Riesgos"
    icon="fas fa-chart-pie"
    chart_id="riskChart"
    :show_dropdown="true"
    :dropdown_items="[
        ['label' => '7 días', 'onclick' => 'updateChart(\'7d\')'],
        ['label' => '30 días', 'onclick' => 'updateChart(\'30d\')']
    ]"
/>
```

### 3. `recent-events.blade.php`
Lista de eventos recientes con formato automático de riesgo.

**Propiedades:**
- `events`: Colección de eventos
- `title`: Título de la sección
- `icon`: Clase del icono
- `max_height`: Altura máxima del contenedor

**Ejemplo:**
```blade
<x-security.recent-events 
    :events="$recent_events"
    title="Eventos Recientes"
    icon="fas fa-clock"
    max_height="400px"
/>
```

### 4. `suspicious-ips.blade.php`
Lista de IPs sospechosas con información de riesgo.

**Propiedades:**
- `ips`: Colección de IPs
- `title`: Título de la sección
- `icon`: Clase del icono

**Ejemplo:**
```blade
<x-security.suspicious-ips 
    :ips="$top_suspicious_ips"
    title="Top 10 IPs Sospechosas"
    icon="fas fa-list-ol"
/>
```

### 5. `dashboard-header.blade.php`
Header del dashboard con estado del sistema y animación de pulso.

**Propiedades:**
- `title`: Título principal
- `subtitle`: Subtítulo descriptivo
- `status`: Estado del sistema
- `status_color`: Color del estado
- `show_pulse`: Mostrar indicador de pulso

**Ejemplo:**
```blade
<x-security.dashboard-header 
    title="Dashboard de Seguridad"
    subtitle="Monitoreo en tiempo real"
    status="OPERATIVO"
    status_color="success"
    :show_pulse="true"
/>
```

### 6. `empty-state.blade.php`
Estado vacío para cuando no hay datos disponibles.

**Propiedades:**
- `icon`: Clase del icono
- `title`: Título del estado vacío
- `message`: Mensaje descriptivo
- `icon_size`: Tamaño del icono
- `icon_color`: Color del icono

**Ejemplo:**
```blade
<x-security.empty-state 
    icon="fas fa-info-circle"
    title="No hay datos"
    message="No se encontraron registros"
/>
```

### 7. `risk-badge.blade.php`
Badge para mostrar el nivel de riesgo de una amenaza.

**Propiedades:**
- `threat_score`: Puntuación de amenaza (0-100)
- `show_score`: Mostrar la puntuación numérica
- `size`: Tamaño del badge (small, normal, large)

**Ejemplo:**
```blade
<x-security.risk-badge 
    :threat_score="85"
    :show_score="true"
    size="normal"
/>
```

---

## 🔔 Sistema de Notificaciones

### Características
- **Tipos**: success, warning, error, info
- **Auto-cierre**: Configurable con TTL
- **Barra de progreso**: Visual del tiempo restante
- **Animaciones**: Entrada y salida suaves
- **Responsive**: Adaptado para móviles
- **Accesibilidad**: ARIA labels y navegación por teclado

### Uso Básico
```javascript
// Notificaciones simples
showSuccess('Operación completada');
showWarning('Atención requerida');
showError('Error en el sistema');
showInfo('Información importante');

// Con opciones personalizadas
showNotification('Mensaje personalizado', 'success', {
    duration: 10000,        // 10 segundos
    autoClose: true,        // Auto-cierre
    showProgress: true      // Mostrar barra de progreso
});
```

### Opciones Disponibles
- `duration`: Duración en milisegundos
- `autoClose`: Habilitar/deshabilitar auto-cierre
- `showProgress`: Mostrar barra de progreso

---

## 🛠️ Utilidades JavaScript

### SecurityUtils

#### Manipulación del DOM
```javascript
// Crear elementos de forma segura
const element = SecurityUtils.createElement('div', {
    className: 'alert alert-info',
    id: 'notification'
}, 'Contenido del elemento');

// Buscar elementos de forma segura
const button = SecurityUtils.findElement('#submit-btn');
const inputs = SecurityUtils.findElements('input[type="text"]');
```

#### Formateo de Datos
```javascript
// Formatear fechas
const formattedDate = SecurityUtils.formatDate(new Date(), {
    day: '2-digit',
    month: 'long',
    year: 'numeric'
});

// Formatear números
const formattedNumber = SecurityUtils.formatNumber(1234567.89, 2);

// Formatear bytes
const formattedBytes = SecurityUtils.formatBytes(1024); // "1 KB"
```

#### Validaciones
```javascript
// Validar IP
const isValidIP = SecurityUtils.isValidIP('192.168.1.1');

// Validar email
const isValidEmail = SecurityUtils.isValidEmail('user@example.com');

// Validar URL
const isValidURL = SecurityUtils.isValidURL('https://example.com');
```

#### Manipulación de Arrays
```javascript
// Agrupar por propiedad
const groupedEvents = SecurityUtils.groupBy(events, 'category');

// Ordenar por múltiples criterios
const sortedEvents = SecurityUtils.sortBy(events, 
    { key: 'threat_score', order: 'desc' },
    { key: 'created_at', order: 'asc' }
);

// Filtrar con múltiples condiciones
const filteredEvents = SecurityUtils.filterBy(events, {
    category: 'malware',
    threat_score: 80
});
```

#### Local Storage
```javascript
// Guardar con TTL
SecurityUtils.setStorage('user_preferences', preferences, 24 * 60 * 60 * 1000); // 24 horas

// Obtener datos
const data = SecurityUtils.getStorage('user_preferences', defaultValue);

// Limpiar expirados
SecurityUtils.cleanExpiredStorage();
```

#### Utilidades de Tiempo
```javascript
// Tiempo relativo
const relativeTime = SecurityUtils.getRelativeTime('2024-01-01T10:00:00Z');

// Debounce
const debouncedSearch = SecurityUtils.debounce(searchFunction, 300);

// Throttle
const throttledScroll = SecurityUtils.throttle(scrollHandler, 100);
```

---

## 🎨 Estilos CSS

### Características
- **Responsive**: Adaptado para todos los dispositivos
- **Animaciones**: Transiciones suaves y efectos hover
- **Temas**: Colores consistentes con el sistema de diseño
- **Accesibilidad**: Contraste adecuado y estados focus

### Clases Principales
```css
/* Indicadores de estado */
.security-status-indicator
.pulse-dot

/* Áreas de gráficos */
.chart-area
.chart-pie

/* Elementos de eventos */
.recent-event-item
.recent-event-item.critical
.recent-event-item.high
.recent-event-item.medium
.recent-event-item.low

/* Elementos de IPs */
.suspicious-ip-item

/* Badges de riesgo */
.risk-badge
```

---

## 📱 Ejemplos de Uso

### Dashboard Completo
```blade
@extends('dashboard')

@section('css')
    <x-security.security-styles />
    <x-security.notification-system />
@stop

@section('contenedor')
    <x-security.dashboard-header 
        title="Mi Dashboard"
        subtitle="Descripción del dashboard"
        status="ACTIVO"
        status_color="success"
    />
    
    <div class="row mb-4">
        <x-security.metric-card 
            title="Total Eventos"
            :value="$totalEvents"
            icon="fas fa-shield-alt"
            color="primary"
        />
        <!-- Más métricas... -->
    </div>
    
    <div class="row mb-4">
        <x-security.chart-card 
            title="Gráfico Principal"
            chart_id="mainChart"
        />
    </div>
    
    <div class="row">
        <x-security.recent-events :events="$events" />
        <x-security.suspicious-ips :ips="$suspiciousIPs" />
    </div>
@stop

@section('js')
    <x-security.js-utilities />
    <script>
        // Tu código JavaScript aquí
        showSuccess('Dashboard cargado correctamente');
    </script>
@stop
```

---

## ✅ Mejores Prácticas

### 1. **Organización de Archivos**
```
resources/views/components/security/
├── metric-card.blade.php
├── chart-card.blade.php
├── recent-events.blade.php
├── suspicious-ips.blade.php
├── dashboard-header.blade.php
├── empty-state.blade.php
├── risk-badge.blade.php
├── notification-system.blade.php
├── js-utilities.blade.php
└── security-styles.blade.php
```

### 2. **Nomenclatura**
- **Componentes**: kebab-case (`metric-card.blade.php`)
- **Propiedades**: camelCase (`threatScore`)
- **Clases CSS**: kebab-case (`.recent-event-item`)
- **Funciones JS**: camelCase (`showNotification`)

### 3. **Accesibilidad**
- Usar `aria-label` para elementos interactivos
- Proporcionar texto alternativo para iconos
- Mantener contraste adecuado
- Soporte para navegación por teclado

### 4. **Performance**
- Lazy loading para componentes pesados
- Debounce en búsquedas en tiempo real
- Throttle en eventos de scroll
- Limpiar event listeners al desmontar

### 5. **Mantenibilidad**
- Documentar todas las propiedades
- Usar tipos consistentes
- Evitar código duplicado
- Seguir principios SOLID

---

## 🚀 Próximos Pasos

1. **Crear más componentes específicos**
   - Tablas de datos
   - Formularios de filtros
   - Modales de confirmación

2. **Implementar temas**
   - Modo oscuro/claro
   - Personalización de colores
   - Variables CSS

3. **Mejorar accesibilidad**
   - Screen readers
   - Navegación por teclado
   - Alto contraste

4. **Optimización de performance**
   - Lazy loading
   - Virtual scrolling
   - Caching inteligente

---

## 📞 Soporte

Para dudas o sugerencias sobre los componentes:
- Revisar la documentación
- Verificar ejemplos de uso
- Consultar el código fuente
- Crear issue en el repositorio
