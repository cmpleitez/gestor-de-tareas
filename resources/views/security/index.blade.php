@extends('dashboard')

@php
// Variables por defecto para evitar conflictos de sintaxis con @json
$defaultRiskData = [0, 0, 0, 0, 0];
$defaultCountryData = [];
@endphp

@section('css')
<style>
    .security-status-indicator {
        position: relative;
        width: 60px;
        height: 60px;
    }

    .pulse-dot {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        width: 20px;
        height: 20px;
        border-radius: 50%;
        animation: pulse 2s infinite;
    }

    @keyframes pulse {
        0% {
            box-shadow: 0 0 0 0 rgba(40, 167, 69, 0.7);
        }

        70% {
            box-shadow: 0 0 0 10px rgba(40, 167, 69, 0);
        }

        100% {
            box-shadow: 0 0 0 0 rgba(40, 167, 69, 0);
        }
    }

    .chart-area {
        position: relative;
        height: 300px;
    }

    .chart-pie {
        position: relative;
        height: 250px;
    }

    .recent-event-item {
        border-left: 4px solid #e3e6f0;
        padding: 0.75rem;
        margin-bottom: 0.5rem;
        background-color: #f8f9fc;
        border-radius: 0.35rem;
    }

    .recent-event-item.critical {
        border-left-color: #e74a3b;
        background-color: #fdf2f2;
    }

    .recent-event-item.high {
        border-left-color: #f6c23e;
        background-color: #fdfbf2;
    }

    .recent-event-item.medium {
        border-left-color: #fd7e14;
        background-color: #fdf8f2;
    }

    .recent-event-item.low {
        border-left-color: #20c9a6;
        background-color: #f2fdfb;
    }

    .suspicious-ip-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0.5rem;
        border-bottom: 1px solid #e3e6f0;
    }

    .suspicious-ip-item:last-child {
        border-bottom: none;
    }

    .risk-badge {
        font-size: 0.75rem;
        padding: 0.25rem 0.5rem;
    }
</style>
@stop

@section('contenedor')
<div class="container-fluid">
    <!-- ========================================
                                                                                                                            HEADER DEL DASHBOARD DE SEGURIDAD
                                                                                                                            ======================================== -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card bg-gradient-primary text-white">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h1 class="mb-2">
                                <i class="fas fa-shield-alt me-3"></i>
                                Dashboard de Seguridad
                            </h1>
                            <p class="mb-0 fs-5">
                                Sistema avanzado de monitoreo con Machine Learning y análisis de amenazas en tiempo real
                            </p>
                        </div>
                        <div class="col-md-4 text-end">
                            <div class="d-flex justify-content-end align-items-center">
                                <div class="me-4">
                                    <div class="fs-6 opacity-75">Estado del Sistema</div>
                                    <div class="fs-4 fw-bold">
                                        <span class="badge bg-success fs-6">OPERATIVO</span>
                                    </div>
                                </div>
                                <div class="security-status-indicator">
                                    <div class="pulse-dot bg-success"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ========================================
                                                                                                                            MÉTRICAS PRINCIPALES - KPIs DE SEGURIDAD
                                                                                                                            ======================================== -->
    <div class="row mb-4">
        <!-- Eventos de Seguridad -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Eventos de Seguridad (24h)
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="security-events-count">
                                {{ $securityEventsCount ?? 0 }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-shield-alt fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Amenazas Activas -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                Amenazas Activas
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="active-threats-count">
                                {{ $activeThreatsCount ?? 0 }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-exclamation-triangle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- IPs Bloqueadas -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                IPs Bloqueadas
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="blocked-ips-count">
                                {{ $blockedIPsCount ?? 0 }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-ban fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Score de Seguridad -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Score de Seguridad
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="security-score">
                                <i class="fas fa-spinner fa-spin"></i>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-chart-line fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ========================================
                                                                                                                            GRÁFICOS Y ANÁLISIS VISUALES
                                                                                                                            ======================================== -->
    <div class="row mb-4">
        <!-- Gráfico de Eventos por Nivel de Riesgo -->
        <div class="col-xl-8 col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-chart-pie me-2"></i>
                        Distribución de Eventos por Nivel de Riesgo
                    </h6>
                    <div class="dropdown no-arrow">
                        <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink"
                            data-bs-toggle="dropdown">
                            <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end shadow animated--fade-in">
                            <a class="dropdown-item" href="#" onclick="updateChart('7d')">Últimos 7 días</a>
                            <a class="dropdown-item" href="#" onclick="updateChart('30d')">Últimos 30 días</a>
                            <a class="dropdown-item" href="#" onclick="updateChart('90d')">Últimos 90 días</a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="chart-area">
                        <canvas id="riskLevelChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Gráfico de Amenazas por País -->
        <div class="col-xl-4 col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-globe me-2"></i>
                        Amenazas por País
                    </h6>
                </div>
                <div class="card-body">
                    <div class="chart-pie">
                        <canvas id="threatsByCountryChart"></canvas>
                    </div>
                    <div class="mt-4 text-center small">
                        <span class="me-2">
                            <i class="fas fa-circle text-primary"></i> Críticas
                        </span>
                        <span class="me-2">
                            <i class="fas fa-circle text-danger"></i> Altas
                        </span>
                        <span class="me-2">
                            <i class="fas fa-circle text-warning"></i> Medias
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ========================================
                                                                                                                            ACTIVIDAD EN TIEMPO REAL
                                                                                                                            ======================================== -->
    <div class="row mb-4">
        <!-- Eventos Recientes -->
        <div class="col-xl-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-clock me-2"></i>
                        Eventos Recientes
                    </h6>
                </div>
                <div class="card-body">
                    <div class="recent-events-container" style="max-height: 400px; overflow-y: auto;">
                        <div id="recent-events-list">
                            @if (isset($recentEvents) && $recentEvents->count() > 0)
                            @foreach ($recentEvents as $event)
                            <div
                                class="recent-event-item {{ $event->threat_score >= 80 ? 'critical' : ($event->threat_score >= 60 ? 'high' : ($event->threat_score >= 40 ? 'medium' : 'low')) }}">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <strong>IP {{ $event->ip_address }}</strong> -
                                        {{ $event->category ?? 'Sin categoría' }}
                                        <br><small class="text-muted">{{ $event->created_at->diffForHumans() }}</small>
                                    </div>
                                    <span
                                        class="badge bg-{{ $event->threat_score >= 80 ? 'danger' : ($event->threat_score >= 60 ? 'warning' : ($event->threat_score >= 40 ? 'warning' : 'success')) }} risk-badge">
                                        {{ $event->threat_score >= 80 ? 'Crítico' : ($event->threat_score >= 60 ? 'Alto'
                                        : ($event->threat_score >= 40 ? 'Medio' : 'Bajo')) }}
                                    </span>
                                </div>
                            </div>
                            @endforeach
                            @else
                            <div class="text-center py-4">
                                <i class="fas fa-info-circle fa-2x text-gray-400"></i>
                                <p class="mt-2 text-gray-500">No hay eventos recientes</p>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>



        <!-- Top IPs Sospechosas -->
        <div class="col-xl-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-list-ol me-2"></i>
                        Top 10 IPs Sospechosas
                    </h6>
                </div>

                <div class="card-body">
                    <div id="suspicious-ips-list">
                        @if (isset($suspiciousIPs) && $suspiciousIPs->count() > 0)
                        @foreach ($suspiciousIPs as $ip)
                        <div class="suspicious-ip-item">
                            <div>
                                <strong>{{ $ip->ip_address }}</strong>
                                <br><small class="text-muted">Eventos: {{ $ip->event_count }}</small>
                            </div>
                            <div class="text-end">
                                <div
                                    class="text-{{ $ip->reputation_score >= 80 ? 'danger' : ($ip->reputation_score >= 60 ? 'warning' : 'success') }} fw-bold">
                                    Score: {{ round($ip->reputation_score, 1) }}</div>
                                <span
                                    class="badge bg-{{ $ip->reputation_score >= 80 ? 'danger' : ($ip->reputation_score >= 60 ? 'warning' : 'success') }} risk-badge">
                                    {{ $ip->reputation_score >= 80 ? 'Crítico' : ($ip->reputation_score >= 60 ? 'Alto' :
                                    'Bajo') }}
                                </span>
                            </div>
                        </div>
                        @endforeach
                        @else
                        <div class="text-center py-4">
                            <i class="fas fa-info-circle fa-2x text-gray-400"></i>
                            <p class="mt-2 text-gray-500">No hay IPs sospechosas</p>
                        </div>
                        @endif



                    </div>
                </div>
            </div>
        </div>



    </div>
</div>
@stop

@section('js')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Variables globales para los gráficos
        let riskLevelChart;
        let threatsByCountryChart;

        // Inicialización del dashboard
        document.addEventListener('DOMContentLoaded', function() {
            initializeDashboard();
            loadDashboardData();
            startRealTimeUpdates();
        });

        function initializeDashboard() {
            // Inicializar gráfico de niveles de riesgo
            const riskCtx = document.getElementById('riskLevelChart').getContext('2d');
            riskLevelChart = new Chart(riskCtx, {
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
                    plugins: {
                        legend: {
                            position: 'bottom'
                        }
                    }
                }
            });

            // Inicializar gráfico de amenazas por país
            const countryCtx = document.getElementById('threatsByCountryChart').getContext('2d');
            threatsByCountryChart = new Chart(countryCtx, {
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
                    plugins: {
                        legend: {
                            display: false
                        }
                    }
                }
            });
        }

        function loadDashboardData() {
            // Cargar métricas principales
            loadSecurityMetrics();

            // Cargar eventos recientes
            loadRecentEvents();

            // Cargar IPs sospechosas
            loadSuspiciousIPs();

            // Cargar datos de gráficos
            loadChartData();
        }

        function loadSecurityMetrics() {
            // Los datos ya están cargados desde el servidor
            // Solo actualizar el score de seguridad si es necesario
            const securityScore = calculateSecurityScore();
            document.getElementById('security-score').innerHTML = securityScore + '/100';
        }

        function calculateSecurityScore() {
            // Calcular score basado en los datos disponibles
            const totalEvents = {{ $securityEventsCount ?? 0 }};
            const criticalEvents =
                {{ isset($recentEvents) ? $recentEvents->where('threat_score', '>=', 80)->count() : 0 }};

            if (totalEvents === 0) return 100;

            const score = Math.max(0, 100 - (criticalEvents * 10));
            return Math.round(score);
        }

        function loadRecentEvents() {
            // Los eventos ya están cargados desde el servidor
            // Solo actualizar si es necesario
            console.log('Eventos recientes ya cargados desde el servidor');
        }

        function loadSuspiciousIPs() {
            // Las IPs ya están cargadas desde el servidor
            // Solo actualizar si es necesario
            console.log('IPs sospechosas ya cargadas desde el servidor');
        }

        function loadChartData() {
            // Cargar datos reales del servidor
            loadRealChartData();
        }

        function loadRealChartData() {
            // Actualizar gráfico de niveles de riesgo con datos reales
            const riskData = @json($riskLevelDistribution ? $riskLevelDistribution : $defaultRiskData);
            riskLevelChart.data.datasets[0].data = riskData;
            riskLevelChart.update();

            // Actualizar gráfico de amenazas por país con datos reales
            const countryData = @json($threatsByCountry ? $threatsByCountry : $defaultCountryData);
            if (Object.keys(countryData).length > 0) {
                threatsByCountryChart.data.labels = Object.keys(countryData);
                threatsByCountryChart.data.datasets[0].data = Object.values(countryData);
                threatsByCountryChart.data.datasets[0].backgroundColor = [
                    '#e74a3b', '#f6c23e', '#fd7e14', '#20c9a6', '#6c757d'
                ];
                threatsByCountryChart.update();
            } else {
                // Si no hay datos, mostrar estado "Sin datos"
                threatsByCountryChart.data.labels = ['Sin datos'];
                threatsByCountryChart.data.datasets[0].data = [1];
                threatsByCountryChart.data.datasets[0].backgroundColor = ['#e3e6f0'];
                threatsByCountryChart.update();
            }
        }

        function startRealTimeUpdates() {
            // Actualizar datos cada 30 segundos
            setInterval(() => {
                loadSecurityMetrics();
                loadRecentEvents();
            }, 30000);
        }



        function showWhitelistIPModal() {
            new bootstrap.Modal(document.getElementById('whitelistIPModal')).show();
        }

        function showMaintenanceModal() {
            new bootstrap.Modal(document.getElementById('maintenanceModal')).show();
        }



        function whitelistIP() {
            const ip = document.getElementById('whitelistIPAddress').value;
            const reason = document.getElementById('whitelistReason').value;

            if (!ip || !reason) {
                alert('Por favor complete todos los campos requeridos.');
                return;
            }

            // Aquí iría la lógica para agregar IP a whitelist
            console.log(`Agregando IP a whitelist: ${ip}, Razón: ${reason}`);

            // Cerrar modal
            bootstrap.Modal.getInstance(document.getElementById('whitelistIPModal')).hide();

            // Mostrar notificación
            showNotification('IP agregada a whitelist exitosamente', 'success');
        }

        function enableMaintenanceMode() {
            const message = document.getElementById('maintenanceMessage').value;
            const duration = document.getElementById('maintenanceDuration').value;

            if (!message) {
                alert('Por favor complete el mensaje de mantenimiento.');
                return;
            }

            // Aquí iría la lógica para activar modo mantenimiento
            console.log(`Activando modo mantenimiento: ${message}, Duración: ${duration}`);

            // Cerrar modal
            bootstrap.Modal.getInstance(document.getElementById('maintenanceModal')).hide();

            // Mostrar notificación
            showNotification('Modo mantenimiento activado', 'warning');
        }

        function generateSecurityReport() {
            // Aquí iría la lógica para generar reporte
            console.log('Generando reporte de seguridad...');
            showNotification('Reporte generado exitosamente', 'info');
        }

        function updateChart(period) {
            // Aquí iría la lógica para actualizar gráficos según el período
            console.log(`Actualizando gráficos para período: ${period}`);
            showNotification('Gráficos actualizados', 'info');
        }

        function showNotification(message, type) {
            // Crear notificación toast
            const toast = document.createElement('div');
            toast.className = `toast align-items-center text-white bg-${type} border-0`;
            toast.setAttribute('role', 'alert');
            toast.innerHTML = `
                <div class="d-flex">
                    <div class="toast-body">${message}</div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                </div>`;
            // Agregar al contenedor de toasts
            const toastContainer = document.querySelector('.toast-container') || createToastContainer();
            toastContainer.appendChild(toast);

            // Mostrar toast
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
</script>
@stop