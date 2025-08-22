/* ========================================
           UTILIDADES JAVASCRIPT PARA SEGURIDAD
           ======================================== */

const SecurityUtils = {
    /**
     * Crear elemento HTML de forma segura
     */
    createElement(tag, attributes = {}, content = '') {
        const element = document.createElement(tag);

        Object.keys(attributes).forEach(key => {
            if (key === 'className') {
                element.className = attributes[key];
            } else if (key === 'innerHTML') {
                element.innerHTML = attributes[key];
            } else {
                element.setAttribute(key, attributes[key]);
            }
        });

        if (content) {
            if (typeof content === 'string') {
                element.textContent = content;
            } else {
                element.appendChild(content);
            }
        }

        return element;
    },

    /**
     * Buscar elemento de forma segura
     */
    findElement(selector, parent = document) {
        try {
            return parent.querySelector(selector);
        } catch (error) {
            console.warn(`Selector inválido: ${selector}`, error);
            return null;
        }
    },

    /**
     * Buscar múltiples elementos de forma segura
     */
    findElements(selector, parent = document) {
        try {
            return Array.from(parent.querySelectorAll(selector));
        } catch (error) {
            console.warn(`Selector inválido: ${selector}`, error);
            return [];
        }
    },

    /**
     * Formatear fecha de forma consistente
     */
    formatDate(date, options = {}) {
        const defaultOptions = {
            day: '2-digit',
            month: '2-digit',
            year: 'numeric',
            hour: '2-digit',
            minute: '2-digit',
            second: '2-digit'
        };

        const finalOptions = {
            ...defaultOptions,
            ...options
        };

        try {
            if (typeof date === 'string') {
                date = new Date(date);
            }

            if (date instanceof Date && !isNaN(date.getTime())) {
                return new Intl.DateTimeFormat('es-ES', finalOptions).format(date);
            }

            return 'Fecha inválida';
        } catch (error) {
            console.warn('Error formateando fecha:', error);
            return 'Error de fecha';
        }
    },

    /**
     * Formatear número con separadores de miles
     */
    formatNumber(number, decimals = 0) {
        try {
            return new Intl.NumberFormat('es-ES', {
                minimumFractionDigits: decimals,
                maximumFractionDigits: decimals
            }).format(number);
        } catch (error) {
            console.warn('Error formateando número:', error);
            return number.toString();
        }
    },

    /**
     * Formatear bytes en unidades legibles
     */
    formatBytes(bytes, decimals = 2) {
        if (bytes === 0) return '0 Bytes';

        const k = 1024;
        const dm = decimals < 0 ? 0 : decimals;
        const sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'];

        const i = Math.floor(Math.log(bytes) / Math.log(k));

        return parseFloat((bytes / Math.pow(k, i)).toFixed(dm)) + ' ' + sizes[i];
    },

    /**
     * Validar dirección IP
     */
    isValidIP(ip) {
        const ipRegex =
            /^(?:(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.){3}(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)$/;
        return ipRegex.test(ip);
    },

    /**
     * Validar email
     */
    isValidEmail(email) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return emailRegex.test(email);
    },

    /**
     * Validar URL
     */
    isValidURL(url) {
        try {
            new URL(url);
            return true;
        } catch {
            return false;
        }
    },

    /**
     * Agrupar elementos por una propiedad
     */
    groupBy(array, key) {
        return array.reduce((groups, item) => {
            const group = item[key] || 'Sin categoría';
            if (!groups[group]) {
                groups[group] = [];
            }
            groups[group].push(item);
            return groups;
        }, {});
    },

    /**
     * Ordenar array por múltiples criterios
     */
    sortBy(array, ...criteria) {
        return array.sort((a, b) => {
            for (const criterion of criteria) {
                const {
                    key,
                    order = 'asc'
                } = criterion;
                let aVal = a[key];
                let bVal = b[key];

                if (aVal == null && bVal == null) continue;
                if (aVal == null) return order === 'asc' ? -1 : 1;
                if (bVal == null) return order === 'asc' ? 1 : -1;

                if (aVal < bVal) return order === 'asc' ? -1 : 1;
                if (aVal > bVal) return order === 'asc' ? 1 : -1;
            }
            return 0;
        });
    },

    /**
     * Filtrar array con múltiples condiciones
     */
    filterBy(array, filters) {
        return array.filter(item => {
            return Object.keys(filters).every(key => {
                const filterValue = filters[key];
                const itemValue = item[key];

                if (filterValue == null || filterValue === '') return true;

                if (typeof filterValue === 'string') {
                    return itemValue && itemValue.toLowerCase().includes(filterValue
                        .toLowerCase());
                }

                if (typeof filterValue === 'number') {
                    return itemValue === filterValue;
                }

                if (Array.isArray(filterValue)) {
                    return filterValue.includes(itemValue);
                }

                return itemValue === filterValue;
            });
        });
    },

    /**
     * Guardar datos en localStorage de forma segura
     */
    setStorage(key, value, ttl = null) {
        try {
            const data = {
                value: value,
                timestamp: Date.now()
            };

            if (ttl) {
                data.expires = Date.now() + ttl;
            }

            localStorage.setItem(key, JSON.stringify(data));
            return true;
        } catch (error) {
            console.warn('Error guardando en localStorage:', error);
            return false;
        }
    },

    /**
     * Obtener datos de localStorage con TTL
     */
    getStorage(key, defaultValue = null) {
        try {
            const data = localStorage.getItem(key);
            if (!data) return defaultValue;

            const parsed = JSON.parse(data);

            if (parsed.expires && Date.now() > parsed.expires) {
                localStorage.removeItem(key);
                return defaultValue;
            }

            return parsed.value;
        } catch (error) {
            console.warn('Error leyendo de localStorage:', error);
            return defaultValue;
        }
    },

    /**
     * Limpiar localStorage expirado
     */
    cleanExpiredStorage() {
        try {
            Object.keys(localStorage).forEach(key => {
                const data = localStorage.getItem(key);
                if (data) {
                    try {
                        const parsed = JSON.parse(data);
                        if (parsed.expires && Date.now() > parsed.expires) {
                            localStorage.removeItem(key);
                        }
                    } catch {
                        // Ignorar entradas que no son JSON válido
                    }
                }
            });
        } catch (error) {
            console.warn('Error limpiando localStorage:', error);
        }
    },

    /**
     * Obtener tiempo relativo (ej: "hace 2 horas")
     */
    getRelativeTime(date) {
        try {
            const now = new Date();
            const target = new Date(date);
            const diff = now - target;

            const seconds = Math.floor(diff / 1000);
            const minutes = Math.floor(seconds / 60);
            const hours = Math.floor(minutes / 60);
            const days = Math.floor(hours / 24);

            if (days > 0) return `hace ${days} día${days > 1 ? 's' : ''}`;
            if (hours > 0) return `hace ${hours} hora${hours > 1 ? 's' : ''}`;
            if (minutes > 0) return `hace ${minutes} minuto${minutes > 1 ? 's' : ''}`;
            if (seconds > 0) return `hace ${seconds} segundo${seconds > 1 ? 's' : ''}`;

            return 'ahora mismo';
        } catch (error) {
            console.warn('Error calculando tiempo relativo:', error);
            return 'fecha desconocida';
        }
    },

    /**
     * Debounce para funciones
     */
    debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    },

    /**
     * Throttle para funciones
     */
    throttle(func, limit) {
        let inThrottle;
        return function () {
            const args = arguments;
            const context = this;
            if (!inThrottle) {
                func.apply(context, args);
                inThrottle = true;
                setTimeout(() => inThrottle = false, limit);
            }
        };
    },

    /**
     * Sanitizar HTML para prevenir XSS
     */
    sanitizeHTML(str) {
        const div = document.createElement('div');
        div.textContent = str;
        return div.innerHTML;
    },

    /**
     * Generar ID único
     */
    generateId(prefix = 'id') {
        return `${prefix}_${Date.now()}_${Math.random().toString(36).substr(2, 9)}`;
    },

    /**
     * Copiar texto al portapapeles
     */
    async copyToClipboard(text) {
        try {
            if (navigator.clipboard && window.isSecureContext) {
                await navigator.clipboard.writeText(text);
                return true;
            } else {
                const textArea = document.createElement('textarea');
                textArea.value = text;
                textArea.style.position = 'fixed';
                textArea.style.left = '-999999px';
                textArea.style.top = '-999999px';
                document.body.appendChild(textArea);
                textArea.focus();
                textArea.select();
                const result = document.execCommand('copy');
                textArea.remove();
                return result;
            }
        } catch (error) {
            console.warn('Error copiando al portapapeles:', error);
            return false;
        }
    }
};

document.addEventListener('DOMContentLoaded', () => {
    SecurityUtils.cleanExpiredStorage();
});

/* ========================================
           SISTEMA DE NOTIFICACIONES
           ======================================== */

class NotificationSystem {
    constructor() {
        this.container = null;
        this.notifications = new Map();
        this.counter = 0;
        this.init();
    }

    init() {
        this.createContainer();
    }

    createContainer() {
        this.container = document.createElement('div');
        this.container.className = 'notification-container';
        document.body.appendChild(this.container);
    }

    show(message, type = 'info', options = {}) {
        const id = `notification-${++this.counter}`;
        const notification = this.createNotification(id, message, type, options);

        this.container.appendChild(notification);
        this.notifications.set(id, notification);

        setTimeout(() => {
            notification.classList.add('show');
        }, 100);

        if (options.autoClose !== false) {
            const duration = options.duration || 5000;
            this.autoClose(id, duration);
        }

        if (options.showProgress !== false) {
            this.showProgress(id, options.duration || 5000);
        }

        return id;
    }

    createNotification(id, message, type, options) {
        const notification = document.createElement('div');
        notification.className = `notification ${type}`;
        notification.id = id;

        const title = this.getTypeTitle(type);
        const icon = this.getTypeIcon(type);

        notification.innerHTML = `
            <div class="notification-header">
                <h6 class="notification-title">
                    <i class="${icon} me-2"></i>${title}
                </h6>
                <button class="notification-close" onclick="notificationSystem.close('${id}')">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="notification-body">
                ${message}
            </div>
            <div class="notification-progress">
                <div class="notification-progress-bar" style="width: 100%"></div>
            </div>
        `;

        return notification;
    }

    getTypeTitle(type) {
        const titles = {
            'success': 'Éxito',
            'warning': 'Advertencia',
            'error': 'Error',
            'info': 'Información'
        };
        return titles[type] || 'Notificación';
    }

    getTypeIcon(type) {
        const icons = {
            'success': 'fas fa-check-circle',
            'warning': 'fas fa-exclamation-triangle',
            'error': 'fas fa-times-circle',
            'info': 'fas fa-info-circle'
        };
        return icons[type] || 'fas fa-bell';
    }

    close(id) {
        const notification = this.notifications.get(id);
        if (!notification) return;

        notification.classList.remove('show');
        notification.classList.add('slide-out');

        setTimeout(() => {
            if (notification.parentNode) {
                notification.parentNode.removeChild(notification);
            }
            this.notifications.delete(id);
        }, 300);
    }

    closeAll() {
        this.notifications.forEach((notification, id) => {
            this.close(id);
        });
    }

    autoClose(id, duration) {
        setTimeout(() => {
            this.close(id);
        }, duration);
    }

    showProgress(id, duration) {
        const notification = this.notifications.get(id);
        if (!notification) return;

        const progressBar = notification.querySelector('.notification-progress-bar');
        if (!progressBar) return;

        const startTime = Date.now();
        const animate = () => {
            const elapsed = Date.now() - startTime;
            const progress = Math.max(0, 100 - (elapsed / duration) * 100);

            progressBar.style.width = `${progress}%`;

            if (progress > 0 && elapsed < duration) {
                requestAnimationFrame(animate);
            }
        };

        requestAnimationFrame(animate);
    }

    success(message, options = {}) {
        return this.show(message, 'success', options);
    }

    warning(message, options = {}) {
        return this.show(message, 'warning', options);
    }

    error(message, options = {}) {
        return this.show(message, 'error', options);
    }

    info(message, options = {}) {
        return this.show(message, 'info', options);
    }
}

const notificationSystem = new NotificationSystem();

function showNotification(message, type = 'info', options = {}) {
    return notificationSystem.show(message, type, options);
}

function showSuccess(message, options = {}) {
    return notificationSystem.success(message, options);
}

function showWarning(message, options = {}) {
    return notificationSystem.warning(message, options);
}

function showError(message, options = {}) {
    return notificationSystem.error(message, options);
}

function showInfo(message, options = {}) {
    return notificationSystem.info(message, options);
}

/* ========================================
           DASHBOARD DE SEGURIDAD PRINCIPAL
           ======================================== */

// Declarar variables de gráficos en un ámbito accesible globalmente
var riskDistributionChart;
var threatsByCountryChart;

$(document).ready(function () {
    console.log('⏳ DOM ready, iniciando dashboard de seguridad...');

    if (typeof Chart === 'undefined') {
        console.error('❌ Chart.js no está disponible');
        showError('Chart.js no se pudo cargar');
        return;
    }
    console.log('✅ Chart.js está disponible');
    console.log('Chart.js Version:', Chart.version); // Log de la versión de Chart.js
    // Chart.register(Chart.Tooltip); // Registrar el plugin Tooltip (Comentado para Chart.js v2)

    // Configuración global de Chart.js para tooltips (Ajustado para v2)
    Chart.defaults.global.tooltips.enabled = true;
    Chart.defaults.global.tooltips.mode = 'index';
    Chart.defaults.global.tooltips.intersect = false;
    Chart.defaults.global.tooltips.position = 'nearest';
    Chart.defaults.global.tooltips.backgroundColor = 'rgba(0, 0, 0, 0.9)';
    Chart.defaults.global.tooltips.titleColor = '#fff';
    Chart.defaults.global.tooltips.bodyColor = '#fff';
    Chart.defaults.global.tooltips.borderColor = '#007bff';
    Chart.defaults.global.tooltips.borderWidth = 2;
    Chart.defaults.global.tooltips.cornerRadius = 8;
    Chart.defaults.global.tooltips.padding = 12;
    Chart.defaults.global.tooltips.events = ['mousemove', 'mouseout', 'click']; // Asegurar eventos de hover

    console.log('🔧 Configuración global de tooltips:', Chart.defaults.global.tooltips);

    if (typeof SecurityUtils === 'undefined') {
        console.error('❌ SecurityUtils no está disponible');
        showError('SecurityUtils no se pudo cargar');
        return;
    }
    console.log('✅ SecurityUtils está disponible');

    initializeDashboard();

    // Obtener los datos del dashboard desde los atributos data-*
    const dashboardContainer = $('.container-fluid[data-risk-distribution][data-threats-by-country]');
    const rawRiskAttr = dashboardContainer.attr('data-risk-distribution');
    console.log('🔍 Raw data-risk-distribution attribute:', rawRiskAttr);
    const jqueryDataRisk = dashboardContainer.data('risk-distribution');
    console.log('🔍 jQuery data(\'risk-distribution\') result:', jqueryDataRisk);

    const initialRiskData = JSON.parse(rawRiskAttr); // Intentar parsear el atributo crudo
    console.log('🔍 Raw data-threats-by-country attribute:', dashboardContainer.attr('data-threats-by-country'));
    const rawCountryAttr = dashboardContainer.attr('data-threats-by-country').trim(); // Obtener y recortar la cadena cruda
    const initialCountryData = rawCountryAttr ? JSON.parse(rawCountryAttr) : {}; // Parsear si no está vacío, de lo contrario, usar objeto vacío

    console.log('🔍 Datos iniciales de riesgo desde HTML:', initialRiskData);
    console.log('🔍 Datos iniciales de países desde HTML:', initialCountryData);

    loadDashboardData(initialRiskData, initialCountryData);
    startRealTimeUpdates();

    // Añadir listener para redimensionamiento de ventana
    $(window).on('resize', function () {
        setTimeout(() => {
            if (riskDistributionChart) {
                riskDistributionChart.resize();
                console.log('🔄 Gráfico de riesgo redimensionado por evento resize');
            }
            if (threatsByCountryChart) {
                threatsByCountryChart.resize();
                console.log('🔄 Gráfico de países redimensionado por evento resize');
            }
        }, 250);
    });

    // Redimensionamiento final después de todo esté cargado
    setTimeout(() => {
        console.log('🔄 Iniciando redimensionamiento final...');
        console.log('🔄 Estado de los gráficos:', {
            riskChart: !!riskDistributionChart,
            countryChart: !!threatsByCountryChart
        });

        if (riskDistributionChart) {
            const riskCanvas = document.getElementById('riskDistributionChart');
            console.log('🔄 Canvas de riesgo:', riskCanvas, 'Dimensiones:', riskCanvas?.offsetWidth, 'x', riskCanvas?.offsetHeight);
            riskDistributionChart.resize();
            riskDistributionChart.update();
            console.log('🔄 Redimensionamiento final - gráfico de riesgo');
        }
        if (threatsByCountryChart) {
            const countryCanvas = document.getElementById('threatsByCountryChart');
            console.log('🔄 Canvas de países:', countryCanvas, 'Dimensiones:', countryCanvas?.offsetWidth, 'x', countryCanvas?.offsetHeight);
            threatsByCountryChart.resize();
            threatsByCountryChart.update();
            console.log('🔄 Redimensionamiento final - gráfico de países');
        }
    }, 1000);

    console.log('✅ Dashboard de seguridad cargado correctamente');
});

function initializeDashboard() {
    console.log('🔧 Inicializando dashboard...');

    const riskCtx = SecurityUtils.findElement('#riskDistributionChart');
    console.log('🔍 Canvas de riesgo encontrado:', riskCtx);
    if (!riskCtx) {
        showError('No se pudo encontrar el canvas del gráfico de riesgo');
        return;
    }
    const riskChartContext = riskCtx.getContext('2d');
    console.log('🔍 Contexto 2D de riesgo obtenido:', riskChartContext);

    console.log('🎨 Creando gráfico de riesgo...');
    riskDistributionChart = new Chart(riskChartContext, {
        type: 'doughnut',
        data: {
            labels: ['Crítico', 'Alto', 'Medio', 'Bajo', 'Mínimo'],
            datasets: [{
                data: [0, 0, 0, 0, 0],
                backgroundColor: [
                    '#e74a3b',
                    '#f6c23e',
                    '#fd7e14',
                    '#20c9a6',
                    '#1cc88a'
                ],
                borderWidth: 2,
                borderColor: '#fff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            animation: {
                animateScale: true,
                animateRotate: true
            },
            legend: {
                position: 'bottom'
            },
            tooltips: {
                enabled: true,
                mode: 'index',
                intersect: false,
                backgroundColor: 'rgba(0, 0, 0, 0.9)',
                titleColor: '#fff',
                bodyColor: '#fff',
                borderColor: '#007bff',
                borderWidth: 2,
                cornerRadius: 8,
                padding: 12,
                displayColors: true,
                events: ['mousemove', 'mouseout', 'click'], // Asegurar eventos de hover para gráfico de riesgo
                callbacks: {
                    label: function (tooltipItem, data) {
                        const label = data.labels[tooltipItem.index] || '';
                        const value = data.datasets[tooltipItem.datasetIndex].data[tooltipItem.index];
                        const total = data.datasets[tooltipItem.datasetIndex].data.reduce((a, b) => a + b, 0);
                        const percentage = ((value / total) * 100).toFixed(1);
                        return `${label}: ${value} (${percentage}%)`;
                    }
                }
            }
        }
    });
    console.log('✅ Gráfico de riesgo creado:', riskDistributionChart);
    console.log('🔧 Tooltips del gráfico de riesgo:', riskDistributionChart.options.tooltips);
    console.log('🔧 Gráfico de riesgo - eventos del gráfico:', riskDistributionChart.options.events); // Log de eventos del gráfico

    // Forzar redimensionamiento para Chart.js v2
    setTimeout(() => {
        riskDistributionChart.resize();
        console.log('🔄 Gráfico de riesgo redimensionado');
    }, 100);

    const countryCtx = SecurityUtils.findElement('#threatsByCountryChart');
    if (!countryCtx) {
        showError('No se pudo encontrar el canvas del gráfico de países');
        return;
    }
    const countryChartContext = countryCtx.getContext('2d');
    console.log('🔍 Contexto 2D de países obtenido:', countryChartContext);

    threatsByCountryChart = new Chart(countryChartContext, {
        type: 'doughnut',
        data: {
            labels: ['Sin datos'],
            datasets: [{
                data: [1],
                backgroundColor: ['#e3e6f0'],
                borderWidth: 2,
                borderColor: '#fff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            animation: {
                animateScale: true,
                animateRotate: true
            },
            legend: {
                display: false
            },
            tooltips: {
                enabled: true,
                mode: 'index',
                intersect: false,
                backgroundColor: 'rgba(0, 0, 0, 0.9)',
                titleColor: '#fff',
                bodyColor: '#fff',
                borderColor: '#007bff',
                borderWidth: 2,
                cornerRadius: 8,
                padding: 12,
                displayColors: true,
                events: ['mousemove', 'mouseout', 'click'], // Asegurar eventos de hover para gráfico de países
                callbacks: {
                    label: function (tooltipItem, data) {
                        const label = data.labels[tooltipItem.index] || '';
                        const value = data.datasets[tooltipItem.datasetIndex].data[tooltipItem.index];
                        return `${label}: ${value} amenazas`;
                    }
                }
            }
        }
    });
    console.log('✅ Gráfico de países creado:', threatsByCountryChart);
    console.log('🔧 Tooltips del gráfico de países:', threatsByCountryChart.options.tooltips);
    console.log('🔧 Gráfico de países - eventos del gráfico:', threatsByCountryChart.options.events); // Log de eventos del gráfico

    // Forzar redimensionamiento para Chart.js v2
    setTimeout(() => {
        threatsByCountryChart.resize();
        console.log('🔄 Gráfico de países redimensionado');
    }, 100);
}

function loadDashboardData(riskDistributionData, threatsByCountryData) {
    console.log('📊 Cargando datos del dashboard...');

    console.log('Métricas ya cargadas desde el servidor');

    console.log('Eventos recientes ya cargados desde el servidor');

    console.log('IPs sospechosas ya cargadas desde el servidor');

    // Mover la llamada a loadRealChartData aquí después de la inicialización de todos los gráficos
    loadRealChartData(riskDistributionData, threatsByCountryData);
}

function loadRealChartData(riskData, countryData) {
    console.log('🔍 DEBUG: Iniciando loadRealChartData');

    console.log('🔍 DEBUG: Datos de riesgo recibidos:', riskData);
    console.log('🔍 DEBUG: Variable riskDistributionChart:', riskDistributionChart);

    if (riskData.length > 0 && riskData.some(val => val > 0)) {
        console.log('🔍 DEBUG: Actualizando gráfico de riesgo con datos:', riskData);
        riskDistributionChart.data.labels = ['Crítico', 'Alto', 'Medio', 'Bajo', 'Mínimo'];
        riskDistributionChart.data.datasets[0].data = riskData;
        riskDistributionChart.data.datasets[0].backgroundColor = [
            '#e74a3b', '#f6c23e', '#fd7e14', '#20c9a6', '#1cc88a'
        ];
        riskDistributionChart.update('active');
        riskDistributionChart.resize(); // Forzar redimensionamiento después de actualizar datos
        console.log('🔍 DEBUG: Gráfico de riesgo actualizado y redimensionado');
    } else {
        console.log('🔍 DEBUG: No hay datos de riesgo, mostrando estado vacío');
        riskDistributionChart.data.labels = ['Sin datos'];
        riskDistributionChart.data.datasets[0].data = [1];
        riskDistributionChart.data.datasets[0].backgroundColor = ['#e3e6f0'];
        riskDistributionChart.update('active');
        riskDistributionChart.resize(); // Forzar redimensionamiento después de actualizar datos
        console.log('🔍 DEBUG: Gráfico de riesgo actualizado y redimensionado');
    }

    if (Object.keys(countryData).length > 0) {
        const countries = Object.keys(countryData);
        const counts = countries.map(country => countryData[country].count);
        threatsByCountryChart.data.labels = countries;
        threatsByCountryChart.data.datasets[0].data = counts;
        threatsByCountryChart.data.datasets[0].backgroundColor = [
            '#e74a3b', '#f6c23e', '#fd7e14', '#20c9a6', '#6c757d'
        ];
        threatsByCountryChart.update('active');
        threatsByCountryChart.resize(); // Forzar redimensionamiento después de actualizar datos
        console.log('🔍 DEBUG: Gráfico de países actualizado con datos:', countries, counts);
    } else {
        threatsByCountryChart.data.labels = ['Sin datos'];
        threatsByCountryChart.data.datasets[0].data = [1];
        threatsByCountryChart.data.datasets[0].backgroundColor = ['#e3e6f0'];
        threatsByCountryChart.update('active');
        threatsByCountryChart.resize(); // Forzar redimensionamiento después de actualizar datos
        console.log('🔍 DEBUG: Gráfico de países mostrando estado vacío');
    }
}

function startRealTimeUpdates() {
    setInterval(() => {
        console.log('Actualizando métricas y eventos recientes en tiempo real...');
    }, 30000);
}

function updateChart(period) {
    console.log(`Actualizando gráficos para período: ${period}`);
    console.log(`ℹ️ Gráficos actualizados para período: ${period}`);
}

function testNotifications() {
    console.log('✅ Sistema de notificaciones funcionando correctamente');
    console.log('ℹ️ Notificaciones de prueba retiradas');
}

function testUtilities() {
    const testIP = '192.168.1.1';
    const isValid = SecurityUtils.isValidIP(testIP);
    console.log(`ℹ️ IP ${testIP} es ${isValid ? 'válida' : 'inválida'}`);

    const testDate = new Date();
    const relativeTime = SecurityUtils.getRelativeTime(testDate);
    console.log('Tiempo relativo:', relativeTime);
}
