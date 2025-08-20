@extends('dashboard')

@section('contenedor')
<div class="container-fluid">
    <!-- Header de Reportes -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card bg-gradient-info text-white">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h1 class="mb-2">
                                <i class="fas fa-chart-bar me-3"></i>
                                Reportes de Seguridad
                            </h1>
                            <p class="mb-0 fs-5">
                                Análisis detallado y reportes del sistema de monitoreo de seguridad
                            </p>
                        </div>
                        <div class="col-md-4 text-end">
                            <div class="d-flex justify-content-end align-items-center">
                                <div class="me-4">
                                    <div class="fs-6 opacity-75">Última Actualización</div>
                                    <div class="fs-4 fw-bold" id="last-update">Hace 5 min</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtros de Reportes -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <label for="report-period" class="form-label">Período</label>
                            <select class="form-select" id="report-period">
                                <option value="24h">Últimas 24 horas</option>
                                <option value="7d" selected>Últimos 7 días</option>
                                <option value="30d">Últimos 30 días</option>
                                <option value="90d">Últimos 90 días</option>
                                <option value="custom">Personalizado</option>
                            </select>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="report-type" class="form-label">Tipo de Reporte</label>
                            <select class="form-select" id="report-type">
                                <option value="overview" selected>Vista General</option>
                                <option value="threats">Amenazas</option>
                                <option value="ips">Análisis de IPs</option>
                                <option value="anomalies">Anomalías</option>
                                <option value="incidents">Incidentes</option>
                            </select>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="risk-level" class="form-label">Nivel de Riesgo</label>
                            <select class="form-select" id="risk-level">
                                <option value="all" selected>Todos</option>
                                <option value="critical">Crítico</option>
                                <option value="high">Alto</option>
                                <option value="medium">Medio</option>
                                <option value="low">Bajo</option>
                            </select>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="action-type" class="form-label">Acción Tomada</label>
                            <select class="form-select" id="action-type">
                                <option value="all" selected>Todas</option>
                                <option value="block">Bloqueo</option>
                                <option value="challenge">Desafío</option>
                                <option value="monitor">Monitoreo</option>
                                <option value="rate_limit">Rate Limit</option>
                            </select>
                        </div>
                    </div>
                    <div class="row" id="custom-date-range" style="display: none;">
                        <div class="col-md-3 mb-3">
                            <label for="start-date" class="form-label">Fecha Inicio</label>
                            <input type="date" class="form-control" id="start-date">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="end-date" class="form-label">Fecha Fin</label>
                            <input type="date" class="form-control" id="end-date">
                        </div>
                        <div class="col-md-6 mb-3 d-flex align-items-end">
                            <button type="button" class="btn btn-primary me-2" onclick="generateReport()">
                                <i class="fas fa-sync-alt me-2"></i>
                                Generar Reporte
                            </button>
                            <button type="button" class="btn btn-outline-secondary" onclick="exportReport()">
                                <i class="fas fa-download me-2"></i>
                                Exportar
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Métricas Principales -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total de Eventos
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="total-events">2,847</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                Amenazas Críticas
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="critical-threats">23</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-exclamation-triangle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                IPs Bloqueadas
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="blocked-ips">156</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-ban fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Tasa de Prevención
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="prevention-rate">98.7%</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-shield-alt fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Gráficos de Análisis -->
    <div class="row mb-4">
        <div class="col-xl-8 col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-chart-line me-2"></i>
                        Evolución de Amenazas en el Tiempo
                    </h6>
                    <div class="dropdown no-arrow">
                        <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-bs-toggle="dropdown">
                            <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in">
                            <a class="dropdown-item" href="#" onclick="downloadChart('threats-evolution')">Descargar</a>
                            <a class="dropdown-item" href="#" onclick="printChart('threats-evolution')">Imprimir</a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <canvas id="threats-evolution-chart" width="400" height="200"></canvas>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-chart-pie me-2"></i>
                        Distribución por Tipo de Amenaza
                    </h6>
                </div>
                <div class="card-body">
                    <canvas id="threat-types-chart" width="400" height="200"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Gráficos Adicionales -->
    <div class="row mb-4">
        <div class="col-xl-6 col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-globe me-2"></i>
                        Top 10 Países por Amenazas
                    </h6>
                </div>
                <div class="card-body">
                    <canvas id="countries-chart" width="400" height="200"></canvas>
                </div>
            </div>
        </div>

        <div class="col-xl-6 col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-clock me-2"></i>
                        Actividad por Hora del Día
                    </h6>
                </div>
                <div class="card-body">
                    <canvas id="hourly-activity-chart" width="400" height="200"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabla de Eventos Recientes -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-list me-2"></i>
                        Eventos Recientes
                    </h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" id="recent-events-table">
                            <thead>
                                <tr>
                                    <th>Timestamp</th>
                                    <th>IP</th>
                                    <th>Tipo</th>
                                    <th>Riesgo</th>
                                    <th>Acción</th>
                                    <th>Detalles</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>2024-01-15 14:32:15</td>
                                    <td>203.0.113.45</td>
                                    <td>SQL Injection</td>
                                    <td><span class="badge bg-danger">Crítico</span></td>
                                    <td><span class="badge bg-danger">Bloqueado</span></td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-primary" onclick="showEventDetails(1)">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>2024-01-15 14:28:42</td>
                                    <td>185.199.108.154</td>
                                    <td>Brute Force</td>
                                    <td><span class="badge bg-warning">Alto</span></td>
                                    <td><span class="badge bg-warning">Rate Limit</span></td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-primary" onclick="showEventDetails(2)">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>2024-01-15 14:25:18</td>
                                    <td>192.168.1.100</td>
                                    <td>Anomalía</td>
                                    <td><span class="badge bg-info">Medio</span></td>
                                    <td><span class="badge bg-info">Monitoreo</span></td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-primary" onclick="showEventDetails(3)">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Resumen de Rendimiento -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-tachometer-alt me-2"></i>
                        Resumen de Rendimiento del Sistema
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <div class="text-center">
                                <div class="h4 text-primary mb-2">99.2%</div>
                                <div class="text-muted">Uptime del Sistema</div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="text-center">
                                <div class="h4 text-success mb-2">45ms</div>
                                <div class="text-muted">Latencia Promedio</div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="text-center">
                                <div class="h4 text-info mb-2">1,247</div>
                                <div class="text-muted">Requests/segundo</div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="text-center">
                                <div class="h4 text-warning mb-2">0.03%</div>
                                <div class="text-muted">Falsos Positivos</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Detalles del Evento -->
<div class="modal fade" id="eventDetailsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detalles del Evento de Seguridad</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="eventDetailsContent">
                <!-- Contenido dinámico -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" onclick="exportEventDetails()">
                    <i class="fas fa-download me-2"></i>
                    Exportar
                </button>
            </div>
        </div>
    </div>
</div>

@stop

@section('css')
<style>
.card { margin-bottom: 1.5rem; }
.border-left-primary { border-left: 0.25rem solid #4e73df !important; }
.border-left-danger { border-left: 0.25rem solid #e74a3b !important; }
.border-left-warning { border-left: 0.25rem solid #f6c23e !important; }
.border-left-success { border-left: 0.25rem solid #1cc88a !important; }
.text-gray-800 { color: #5a5c69 !important; }
.text-gray-300 { color: #dddfeb !important; }
</style>
@stop

@section('js')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
let charts = {};

document.addEventListener('DOMContentLoaded', function() {
    initializeCharts();
    setupEventListeners();
    updateLastUpdate();
});

function setupEventListeners() {
    document.getElementById('report-period').addEventListener('change', function() {
        if (this.value === 'custom') {
            document.getElementById('custom-date-range').style.display = 'block';
        } else {
            document.getElementById('custom-date-range').style.display = 'none';
            generateReport();
        }
    });

    document.getElementById('report-type').addEventListener('change', generateReport);
    document.getElementById('risk-level').addEventListener('change', generateReport);
    document.getElementById('action-type').addEventListener('change', generateReport);
}

function initializeCharts() {
    // Gráfico de evolución de amenazas
    const threatsCtx = document.getElementById('threats-evolution-chart').getContext('2d');
    charts.threatsEvolution = new Chart(threatsCtx, {
        type: 'line',
        data: {
            labels: ['Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb', 'Dom'],
            datasets: [{
                label: 'Amenazas Críticas',
                data: [12, 19, 15, 25, 22, 18, 23],
                borderColor: '#e74a3b',
                backgroundColor: 'rgba(231, 74, 59, 0.1)',
                tension: 0.4
            }, {
                label: 'Amenazas Altas',
                data: [45, 52, 38, 67, 58, 42, 51],
                borderColor: '#f6c23e',
                backgroundColor: 'rgba(246, 194, 62, 0.1)',
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'top' }
            },
            scales: {
                y: { beginAtZero: true }
            }
        }
    });

    // Gráfico de tipos de amenazas
    const typesCtx = document.getElementById('threat-types-chart').getContext('2d');
    charts.threatTypes = new Chart(typesCtx, {
        type: 'doughnut',
        data: {
            labels: ['SQL Injection', 'XSS', 'Brute Force', 'DDoS', 'Anomalías'],
            datasets: [{
                data: [35, 25, 20, 15, 5],
                backgroundColor: ['#e74a3b', '#f6c23e', '#4e73df', '#1cc88a', '#6f42c1']
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'bottom' }
            }
        }
    });

    // Gráfico de países
    const countriesCtx = document.getElementById('countries-chart').getContext('2d');
    charts.countries = new Chart(countriesCtx, {
        type: 'bar',
        data: {
            labels: ['China', 'Rusia', 'EE.UU.', 'Brasil', 'India', 'Corea', 'Irán', 'Pakistán', 'Turquía', 'Vietnam'],
            datasets: [{
                label: 'Amenazas',
                data: [45, 38, 32, 28, 25, 22, 18, 15, 12, 10],
                backgroundColor: '#4e73df'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: { beginAtZero: true }
            }
        }
    });

    // Gráfico de actividad por hora
    const hourlyCtx = document.getElementById('hourly-activity-chart').getContext('2d');
    charts.hourlyActivity = new Chart(hourlyCtx, {
        type: 'line',
        data: {
            labels: ['00:00', '04:00', '08:00', '12:00', '16:00', '20:00'],
            datasets: [{
                label: 'Eventos',
                data: [45, 32, 78, 156, 234, 189],
                borderColor: '#1cc88a',
                backgroundColor: 'rgba(28, 200, 138, 0.1)',
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: { beginAtZero: true }
            }
        }
    });
}

function generateReport() {
    const period = document.getElementById('report-period').value;
    const type = document.getElementById('report-type').value;
    const riskLevel = document.getElementById('risk-level').value;
    const actionType = document.getElementById('action-type').value;

    console.log('Generando reporte:', { period, type, riskLevel, actionType });

    // Simular actualización de datos
    updateMetrics();
    updateCharts();
    
    showNotification('Reporte generado exitosamente', 'success');
}

function updateMetrics() {
    // Simular actualización de métricas
    document.getElementById('total-events').textContent = Math.floor(Math.random() * 5000) + 2000;
    document.getElementById('critical-threats').textContent = Math.floor(Math.random() * 50) + 10;
    document.getElementById('blocked-ips').textContent = Math.floor(Math.random() * 300) + 100;
    document.getElementById('prevention-rate').textContent = (Math.random() * 2 + 97).toFixed(1) + '%';
}

function updateCharts() {
    // Actualizar datos de los gráficos
    if (charts.threatsEvolution) {
        charts.threatsEvolution.data.datasets[0].data = Array.from({length: 7}, () => Math.floor(Math.random() * 30) + 10);
        charts.threatsEvolution.data.datasets[1].data = Array.from({length: 7}, () => Math.floor(Math.random() * 50) + 30);
        charts.threatsEvolution.update();
    }
}

function showEventDetails(eventId) {
    const eventDetails = {
        1: {
            title: 'SQL Injection Detectado',
            description: 'Intento de inyección SQL detectado en el formulario de login',
            details: {
                'IP Address': '203.0.113.45',
                'User Agent': 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
                'Request Method': 'POST',
                'URL': '/login',
                'Payload': "' OR '1'='1",
                'Threat Score': '95/100',
                'Country': 'China',
                'ISP': 'China Telecom',
                'Previous Incidents': '12 en los últimos 30 días'
            }
        },
        2: {
            title: 'Ataque de Fuerza Bruta',
            description: 'Múltiples intentos de login fallidos desde la misma IP',
            details: {
                'IP Address': '185.199.108.154',
                'User Agent': 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36',
                'Request Method': 'POST',
                'URL': '/login',
                'Attempts': '47 en 5 minutos',
                'Threat Score': '78/100',
                'Country': 'Rusia',
                'ISP': 'DigitalOcean',
                'Previous Incidents': '3 en los últimos 7 días'
            }
        },
        3: {
            title: 'Comportamiento Anómalo',
            description: 'Patrón de navegación inusual detectado por ML',
            details: {
                'IP Address': '192.168.1.100',
                'User Agent': 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
                'Request Method': 'GET',
                'Anomaly Score': '0.73',
                'Pattern': 'Navegación rápida entre páginas sin interacción',
                'Threat Score': '42/100',
                'Country': 'Local',
                'ISP': 'Internal Network',
                'Previous Incidents': 'Primera vez'
            }
        }
    };

    const event = eventDetails[eventId];
    if (!event) return;

    let detailsHtml = `
        <h6>${event.title}</h6>
        <p class="text-muted">${event.description}</p>
        <div class="row">
            <div class="col-12">
                <table class="table table-sm">
                    <tbody>
    `;

    Object.entries(event.details).forEach(([key, value]) => {
        detailsHtml += `
            <tr>
                <td><strong>${key}:</strong></td>
                <td>${value}</td>
            </tr>
        `;
    });

    detailsHtml += `
                    </tbody>
                </table>
            </div>
        </div>
    `;

    document.getElementById('eventDetailsContent').innerHTML = detailsHtml;
    
    const modal = new bootstrap.Modal(document.getElementById('eventDetailsModal'));
    modal.show();
}

function exportReport() {
    const period = document.getElementById('report-period').value;
    const type = document.getElementById('report-type').value;
    
    console.log('Exportando reporte:', { period, type });
    showNotification('Reporte exportado exitosamente', 'success');
}

function exportEventDetails() {
    console.log('Exportando detalles del evento');
    showNotification('Detalles exportados exitosamente', 'success');
}

function downloadChart(chartId) {
    console.log('Descargando gráfico:', chartId);
    showNotification('Gráfico descargado exitosamente', 'success');
}

function printChart(chartId) {
    console.log('Imprimiendo gráfico:', chartId);
    showNotification('Gráfico enviado a impresión', 'success');
}

function updateLastUpdate() {
    const now = new Date();
    const timeString = now.toLocaleTimeString('es-ES', { 
        hour: '2-digit', 
        minute: '2-digit' 
    });
    document.getElementById('last-update').textContent = `Hace ${timeString}`;
}

function showNotification(message, type) {
    const toast = document.createElement('div');
    toast.className = `toast align-items-center text-white bg-${type} border-0`;
    toast.setAttribute('role', 'alert');
    toast.innerHTML = `
        <div class="d-flex">
            <div class="toast-body">${message}</div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
    `;
    
    const toastContainer = document.querySelector('.toast-container') || createToastContainer();
    toastContainer.appendChild(toast);
    
    const bsToast = new bootstrap.Toast(toast);
    bsToast.show();
}

function createToastContainer() {
    const container = document.createElement('div');
    container.className = 'toast-container position-fixed top-0 end-0 p-3';
    container.style.zIndex = '1055';
    document.body.appendChild(container);
    return container;
}

// Actualizar cada 5 minutos
setInterval(updateLastUpdate, 300000);
</script>
@stop
