@extends('dashboard')

@section('contenedor')
    <div class="container-fluid" data-risk-distribution="{{ trim(json_encode($risk_distribution ?? [])) }}"
        data-threats-by-country="{{ trim(json_encode($threats_by_country ?? [])) }}">
        <!-- Header de Reputación de IPs -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card bg-gradient-warning text-white">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <h1 class="mb-2">
                                    <i class="fas fa-globe me-3"></i>
                                    Reputación de IPs
                                </h1>
                                <p class="mb-0 fs-5">
                                    Análisis avanzado de reputación de direcciones IP con Machine Learning y fuentes
                                    externas
                                </p>
                            </div>
                            <div class="col-md-4 text-end">
                                <div class="d-flex justify-content-end align-items-center">
                                    <div class="me-4">
                                        <div class="fs-6 opacity-75">IPs Monitoreadas</div>
                                        <div class="fs-4 fw-bold" id="monitored-ips-count">
                                            {{ $totalIPs ?? 0 }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Métricas de Reputación -->
        <!-- Métricas eliminadas - Solo se muestran los gráficos -->

        <!-- Gráficos de Análisis -->
        <div class="row mb-4">
            <div class="col-xl-6 col-lg-7">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="fas fa-chart-pie me-2"></i>
                            Distribución por Nivel de Riesgo (Últimos 3 días)
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="chart-area" style="height: 500px;">
                            <canvas id="riskDistributionChart"></canvas>
                        </div>

                        <!-- Leyenda de colores del gráfico de riesgo -->
                        <div class="mt-3">
                            <h6 class="text-muted mb-2">
                                <i class="fas fa-info-circle me-2"></i>
                                Significado de los Colores:
                            </h6>
                            <div class="row">
                                <div class="col-md-3 mb-2">
                                    <div class="d-flex align-items-center">
                                        <div class="risk-color-indicator"
                                            style="background-color: #fd7e14; width: 20px; height: 20px; border-radius: 50%; margin-right: 8px;">
                                        </div>
                                        <span class="text-muted">Medio (40-59)</span>
                                    </div>
                                </div>
                                <div class="col-md-3 mb-2">
                                    <div class="d-flex align-items-center">
                                        <div class="risk-color-indicator"
                                            style="background-color: #f6c23e; width: 20px; height: 20px; border-radius: 50%; margin-right: 8px;">
                                        </div>
                                        <span class="text-muted">Alto (60-79)</span>
                                    </div>
                                </div>
                                <div class="col-md-3 mb-2">
                                    <div class="d-flex align-items-center">
                                        <div class="risk-color-indicator"
                                            style="background-color: #e74a3b; width: 20px; height: 20px; border-radius: 50%; margin-right: 8px;">
                                        </div>
                                        <span class="text-muted">Crítico (80-100)</span>
                                    </div>
                                </div>
                                <div class="col-md-3 mb-2">
                                    <div class="d-flex align-items-center">
                                        <div class="risk-color-indicator"
                                            style="background-color: #6c757d; width: 20px; height: 20px; border-radius: 50%; margin-right: 8px;">
                                        </div>
                                        <span class="text-muted">Bajo (20-39)</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-6 col-lg-5">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="fas fa-globe me-2"></i>
                            IPs por País (Últimos 3 días)
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="chart-area" style="height: 500px;">
                            <canvas id="countryDistributionChart"></canvas>
                        </div>

                        <!-- Información del gráfico de países -->
                        <div class="mt-3">
                            <h6 class="text-muted mb-2">
                                <i class="fas fa-info-circle me-2"></i>
                                Información del Gráfico:
                            </h6>
                            <p class="text-muted small mb-0">
                                Este gráfico muestra la cantidad de IPs que generaron eventos de seguridad en los últimos 3
                                días,
                                agrupadas por país de origen según los datos de geolocalización disponibles.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filtros de Búsqueda -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card shadow">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="fas fa-filter me-2"></i>
                            Filtros de Búsqueda
                        </h6>
                    </div>
                    <div class="card-body">
                        <form id="ip-filter-form">
                            <div class="row">
                                <div class="col-md-3 mb-3">
                                    <label for="filter-ip" class="form-label">Dirección IP</label>
                                    <input type="text" class="form-control" id="filter-ip" placeholder="192.168.1.100">
                                </div>

                                <div class="col-md-3 mb-3">
                                    <label for="filter-risk-level" class="form-label">Nivel de Riesgo</label>
                                    <select class="form-select" id="filter-risk-level">
                                        <option value="">Todos</option>
                                        <option value="medium">Medio</option>
                                        <option value="high">Alto</option>
                                        <option value="critical">Crítico</option>
                                    </select>
                                </div>

                                <div class="col-md-3 mb-3">
                                    <label for="filter-country" class="form-label">País</label>
                                    <select class="form-select" id="filter-country">
                                        <option value="">Todos</option>
                                        @foreach ($availableCountries ?? [] as $country)
                                            <option value="{{ $country }}">{{ $country }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-3 mb-3 d-flex align-items-end">
                                    <button type="button" class="btn btn-outline-secondary" onclick="clearIPFilters()">
                                        <i class="fas fa-times me-2"></i>
                                        Limpiar
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabla de Reputación de IPs -->
        <div class="row">
            <div class="col-12">
                <div class="card shadow mb-4">
                    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="fas fa-list me-2"></i>
                            Base de Datos de Reputación (Últimos 3 días)
                        </h6>
                        <div class="d-flex">
                            <button class="btn btn-outline-info btn-sm" onclick="updateIPReputation()">
                                <i class="fas fa-sync-alt me-2"></i>
                                Actualizar
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered" id="ip-reputation-table" width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th>IP</th>
                                        <th>Score</th>
                                        <th>Riesgo</th>
                                        <th>País</th>
                                        <th>ISP</th>
                                        <th>Última Actualización</th>
                                        <th>Estado</th>

                                    </tr>
                                </thead>
                                <tbody id="ip-reputation-table-body">
                                    <tr>
                                        <td colspan="7" class="text-center py-4">
                                            <i class="fas fa-spinner fa-spin fa-2x text-gray-400"></i>
                                            <p class="mt-2 text-gray-500">Cargando reputaciones...</p>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- Paginación -->
                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <div class="text-muted">
                                Mostrando <span id="ips-showing-start">0</span> a <span id="ips-showing-end">0</span> de
                                <span id="ips-showing-total">0</span> IPs
                            </div>
                            <nav aria-label="Navegación de IPs">
                                <ul class="pagination mb-0" id="ips-pagination">
                                    <!-- Paginación se generará dinámicamente -->
                                </ul>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>




@stop

@section('css')
    <style>
        .border-left-success {
            border-left: 0.25rem solid #1cc88a !important;
        }

        .border-left-warning {
            border-left: 0.25rem solid #f6c23e !important;
        }

        .border-left-danger {
            border-left: 0.25rem solid #e74a3b !important;
        }

        .border-left-info {
            border-left: 0.25rem solid #36b9cc !important;
        }

        .chart-area {
            position: relative;
            height: 300px;
        }

        .ip-row {
            cursor: pointer;
            transition: background-color 0.2s;
        }

        .ip-row:hover {
            background-color: #f8f9fc;
        }

        .ip-row.critical {
            border-left: 4px solid #e74a3b;
        }

        .ip-row.high {
            border-left: 4px solid #f6c23e;
        }

        .ip-row.medium {
            border-left: 4px solid #fd7e14;
        }

        .ip-row.low {
            border-left: 4px solid #20c9a6;
        }

        .ip-row.minimal {
            border-left: 4px solid #1cc88a;
        }

        .risk-badge {
            font-size: 0.75rem;
            padding: 0.25rem 0.5rem;
        }

        .score-indicator {
            width: 60px;
            height: 8px;
            background-color: #e3e6f0;
            border-radius: 4px;
            overflow: hidden;
        }

        .score-fill {
            height: 100%;
            transition: width 0.3s ease;
        }

        .score-fill.critical {
            background-color: #e74a3b;
        }

        .score-fill.high {
            background-color: #f6c23e;
        }

        .score-fill.medium {
            background-color: #fd7e14;
        }

        .score-fill.low {
            background-color: #20c9a6;
        }

        .score-fill.minimal {
            background-color: #1cc88a;
        }
    </style>

    <!-- BEGIN: Security Dashboard CSS -->
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/css/security-dashboard.css') }}">
    <!-- END: Security Dashboard CSS -->
@stop

@section('js')
    <!-- Mostrar mensajes de error con Toastr -->
    @if (isset($error_message))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                if (typeof toastr !== 'undefined') {
                    toastr.error("{{ $error_message }}", 'Error de Reputación de IPs', {
                        timeOut: 8000,
                        extendedTimeOut: 2000,
                        closeButton: true,
                        progressBar: true
                    });
                }
            });
        </script>
    @endif

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Variables globales
        let riskDistributionChart;
        let countryDistributionChart;
        let currentIPPage = 1;
        let ipsPerPage = 25;
        let totalIPs = 0;

        // Datos reales enviados desde el controlador
        const serverIPReputations = {!! json_encode($ipReputations ?? []) !!};
        const serverRiskDistribution = {!! json_encode($riskDistribution ?? []) !!};
        const serverCountryDistribution = {!! json_encode($countryDistribution ?? []) !!};

        // Inicialización
        document.addEventListener('DOMContentLoaded', function() {
            initializeIPCharts();
            loadIPReputation();
            setupFilterEventListeners();
        });

        function setupFilterEventListeners() {
            // Filtrado en tiempo real para IP
            document.getElementById('filter-ip').addEventListener('input', function() {
                // Aplicar filtros en tiempo real mientras se escribe
                applyIPFilters();
            });

            // Filtrado automático al cambiar selecciones
            document.getElementById('filter-risk-level').addEventListener('change', applyIPFilters);
            document.getElementById('filter-country').addEventListener('change', applyIPFilters);
        }

        function initializeIPCharts() {
            // Gráfico de distribución de riesgo
            const riskCtx = document.getElementById('riskDistributionChart').getContext('2d');
            riskDistributionChart = new Chart(riskCtx, {
                type: 'doughnut',
                data: {
                    labels: ['Medio', 'Alto', 'Crítico'],
                    datasets: [{
                        data: [
                            serverRiskDistribution.medium || 0,
                            serverRiskDistribution.high || 0,
                            serverRiskDistribution.critical || 0
                        ],
                        backgroundColor: ['#fd7e14', '#f6c23e', '#e74a3b'],
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

            // Gráfico de distribución por país
            const countryCtx = document.getElementById('countryDistributionChart').getContext('2d');

            // Preparar datos del gráfico de países
            const countryLabels = Object.keys(serverCountryDistribution).filter(key => key !== 'total');
            const countryData = countryLabels.map(country => serverCountryDistribution[country].count || 0);

            countryDistributionChart = new Chart(countryCtx, {
                type: 'bar',
                data: {
                    labels: countryLabels,
                    datasets: [{
                        label: 'Cantidad de IPs',
                        data: countryData,
                        backgroundColor: ['#4e73df', '#f6c23e', '#e74a3b', '#36b9cc', '#1cc88a', '#fd7e14',
                            '#20c9a6'
                        ]
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        }

        function loadIPReputation(page = 1) {
            currentIPPage = page;

            const tableBody = document.getElementById('ip-reputation-table-body');

            // Mostrar loading
            tableBody.innerHTML = `
            <tr>
                <td colspan="7" class="text-center py-4">
                    <i class="fas fa-spinner fa-spin fa-2x text-gray-400"></i>
                    <p class="mt-2 text-gray-500">Cargando reputaciones...</p>
                </td>
            </tr>
        `;

            // Usar los datos reales del servidor
            if (serverIPReputations && serverIPReputations.length > 0) {
                renderIPsTable(serverIPReputations);
                updateIPsPagination();
                updateIPCharts(serverIPReputations);
            } else {
                // Si no hay datos del servidor, mostrar mensaje
                showNoIPsMessage();
            }
        }



        function renderIPsTable(ips) {
            const tableBody = document.getElementById('ip-reputation-table-body');
            const startIndex = (currentIPPage - 1) * ipsPerPage;
            const endIndex = startIndex + ipsPerPage;
            const pageIPs = ips.slice(startIndex, endIndex);

            tableBody.innerHTML = '';

            pageIPs.forEach(ip => {
                const row = document.createElement('tr');
                row.className = `ip-row ${ip.risk_level}`;


                row.innerHTML = `
            <td>
                <div class="d-flex align-items-center">
                    <div class="me-2">
                        <i class="fas fa-globe text-muted"></i>
                    </div>
                    <div>
                        <strong>${ip.ip}</strong>
                        <br><small class="text-muted">${ip.isp}</small>
                    </div>
                </div>
            </td>
            <td>
                <div class="d-flex align-items-center">
                    <div class="me-2">
                        <strong>${ip.score}</strong>
                    </div>
                    <div class="score-indicator">
                        <div class="score-fill ${ip.risk_level}" style="width: ${ip.score}%"></div>
                    </div>
                </div>
            </td>
            <td>
                <span class="badge risk-badge bg-${getRiskBadgeColor(ip.risk_level)}">
                    ${ip.risk_level.toUpperCase()}
                </span>
            </td>
            <td>
                <span class="badge bg-info">${ip.country}</span>
            </td>
            <td>
                <small>${ip.isp}</small>
            </td>
            <td>
                <small>${formatDate(ip.lastUpdated)}</small>
            </td>
            <td>
                <span class="badge bg-success">${ip.status}</span>
            </td>
            
        `;

                tableBody.appendChild(row);
            });

            totalIPs = ips.length;
            updateIPsShowingInfo();
        }

        function getRiskBadgeColor(riskLevel) {
            const colors = {
                'critical': 'danger',
                'high': 'warning',
                'medium': 'warning',
                'low': 'info',
                'minimal': 'success'
            };
            return colors[riskLevel] || 'secondary';
        }

        function formatDate(date) {
            // Validar que la fecha sea válida
            if (!date || date === 'null' || date === 'undefined' || date === '') {
                return 'N/A';
            }

            try {
                // Si es string, convertir a Date
                let dateObj = date;
                if (typeof date === 'string') {
                    dateObj = new Date(date);
                }

                // Verificar que la fecha sea válida
                if (isNaN(dateObj.getTime())) {
                    return 'Fecha inválida';
                }

                return new Intl.DateTimeFormat('es-ES', {
                    day: '2-digit',
                    month: '2-digit',
                    year: 'numeric',
                    hour: '2-digit',
                    minute: '2-digit'
                }).format(dateObj);
            } catch (error) {
                return 'Error de fecha';
            }
        }

        function updateIPsShowingInfo() {
            const start = (currentIPPage - 1) * ipsPerPage + 1;
            const end = Math.min(currentIPPage * ipsPerPage, totalIPs);

            document.getElementById('ips-showing-start').textContent = start;
            document.getElementById('ips-showing-end').textContent = end;
            document.getElementById('ips-showing-total').textContent = totalIPs;
        }

        function updateIPsPagination() {
            const totalPages = Math.ceil(totalIPs / ipsPerPage);
            const pagination = document.getElementById('ips-pagination');

            pagination.innerHTML = '';

            // Botón anterior
            const prevLi = document.createElement('li');
            prevLi.className = `page-item ${currentIPPage === 1 ? 'disabled' : ''}`;
            prevLi.innerHTML =
                `<a class="page-link" href="#" onclick="loadIPReputation(${currentIPPage - 1})">Anterior</a>`;
            pagination.appendChild(prevLi);

            // Páginas numeradas
            const startPage = Math.max(1, currentIPPage - 2);
            const endPage = Math.min(totalPages, currentIPPage + 2);

            for (let i = startPage; i <= endPage; i++) {
                const li = document.createElement('li');
                li.className = `page-item ${i === currentIPPage ? 'active' : ''}`;
                li.innerHTML = `<a class="page-link" href="#" onclick="loadIPReputation(${i})">${i}</a>`;
                pagination.appendChild(li);
            }

            // Botón siguiente
            const nextLi = document.createElement('li');
            nextLi.className = `page-item ${currentIPPage === totalPages ? 'disabled' : ''}`;
            nextLi.innerHTML =
                `<a class="page-link" href="#" onclick="loadIPReputation(${currentIPPage + 1})">Siguiente</a>`;
            pagination.appendChild(nextLi);
        }

        function updateIPCharts(ips) {
            // Actualizar gráfico de distribución de riesgo
            const riskCounts = {};
            ips.forEach(ip => {
                riskCounts[ip.risk_level] = (riskCounts[ip.risk_level] || 0) + 1;
            });

            riskDistributionChart.data.datasets[0].data = [
                riskCounts.medium || 0,
                riskCounts.high || 0,
                riskCounts.critical || 0
            ];
            riskDistributionChart.update();

            // Actualizar gráfico de distribución por país dinámicamente
            const countryCounts = {};
            ips.forEach(ip => {
                countryCounts[ip.country] = (countryCounts[ip.country] || 0) + 1;
            });

            // Obtener países únicos y sus conteos
            const uniqueCountries = Object.keys(countryCounts);
            const countryData = uniqueCountries.map(country => countryCounts[country]);

            // Actualizar gráfico con datos reales
            countryDistributionChart.data.labels = uniqueCountries;
            countryDistributionChart.data.datasets[0].data = countryData;
            countryDistributionChart.update();
        }

        // Función loadIPStats eliminada - Los datos ahora vienen del servidor

        function applyIPFilters() {
            const ipFilter = document.getElementById('filter-ip').value.trim().toLowerCase();
            const riskFilter = document.getElementById('filter-risk-level').value;
            const countryFilter = document.getElementById('filter-country').value;

            // Filtrar los datos existentes
            let filteredIPs = [...serverIPReputations];

            // Aplicar filtro por IP
            if (ipFilter) {
                filteredIPs = filteredIPs.filter(ip =>
                    ip.ip.toLowerCase().includes(ipFilter)
                );
            }

            // Aplicar filtro por nivel de riesgo
            if (riskFilter) {
                filteredIPs = filteredIPs.filter(ip =>
                    ip.risk_level === riskFilter
                );
            }

            // Aplicar filtro por país
            if (countryFilter) {
                filteredIPs = filteredIPs.filter(ip =>
                    ip.country === countryFilter
                );
            }

            // Renderizar tabla filtrada
            renderIPsTable(filteredIPs);
            updateIPsPagination();
            updateIPCharts(filteredIPs);
        }

        function clearIPFilters() {
            document.getElementById('ip-filter-form').reset();
            // Restaurar datos originales
            renderIPsTable(serverIPReputations);
            updateIPsPagination();
            updateIPCharts(serverIPReputations);
        }

        function updateIPReputation() {
            // Actualizando reputación de IPs
            showNotification('Actualización iniciada', 'info');
        }

        function showNoIPsMessage() {
            const tableBody = document.getElementById('ip-reputation-table-body');
            tableBody.innerHTML = `
            <tr>
                <td colspan="7" class="text-center py-4">
                    <i class="fas fa-info-circle text-muted"></i>
                    <p class="mt-2 text-gray-500">No hay datos de reputación disponibles para mostrar.</p>
                </td>
            </tr>
        `;
            updateIPsShowingInfo();
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
    </script>

    <!-- BEGIN: Application JavaScript -->
    <script src="{{ asset('app-assets/js/security-dashboard.js') }}"></script>
    <!-- END: Application JavaScript -->
@stop
