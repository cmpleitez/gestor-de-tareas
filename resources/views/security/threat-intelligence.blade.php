@extends('dashboard')

@section('contenedor')
    <div class="container-fluid">
        <!-- ========================================
                                        HEADER DE INTELIGENCIA DE AMENAZAS
                                        ======================================== -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card bg-gradient-danger text-white">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <h1 class="mb-2">
                                    <i class="fas fa-brain me-3"></i>
                                    Inteligencia de Amenazas
                                </h1>
                                <p class="mb-0 fs-5">
                                    Análisis avanzado de amenazas con Machine Learning y correlación de datos de múltiples
                                    fuentes
                                </p>
                            </div>
                            <div class="col-md-4 text-end">
                                <div class="d-flex justify-content-end align-items-center">
                                    <div class="me-4">
                                        <div class="fs-6 opacity-75">Amenazas Activas</div>
                                        <div class="fs-4 fw-bold" id="active-threats-count">
                                            <i class="fas fa-spinner fa-spin"></i>
                                        </div>
                                    </div>
                                    <div class="threat-status-indicator">
                                        <div class="pulse-dot bg-warning"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>



        <!-- ========================================
                                        ANÁLISIS VISUAL DE AMENAZAS
                                        ======================================== -->
        <div class="row mb-4">
            <!-- Gráfico de Evolución Temporal -->
            <div class="col-12">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="fas fa-chart-line me-2"></i>
                            Evolución de Amenazas (Últimos 30 días)
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="chart-area">
                            <canvas id="threatEvolutionChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ========================================
                                        FILTROS Y BÚSQUEDA DE AMENAZAS
                                        ======================================== -->
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
                        <form id="threats-filter-form">
                            <div class="row">
                                <div class="col-md-3 mb-3">
                                    <label for="filter-threat-type" class="form-label">Tipo de Amenaza</label>
                                    <select class="form-select" id="filter-threat-type">
                                        <option value="">Todos</option>
                                        <option value="malware">Malware</option>
                                        <option value="phishing">Phishing</option>
                                        <option value="ddos">DDoS</option>
                                        <option value="apt">APT</option>
                                        <option value="ransomware">Ransomware</option>
                                        <option value="botnet">Botnet</option>
                                    </select>
                                </div>

                                <div class="col-md-3 mb-3">
                                    <label for="filter-classification" class="form-label">Clasificación</label>
                                    <select class="form-select" id="filter-classification">
                                        <option value="">Todas</option>
                                        <option value="critical">Crítica</option>
                                        <option value="high">Alta</option>
                                        <option value="medium">Media</option>
                                    </select>
                                </div>

                                <div class="col-md-3 mb-3">
                                    <label for="filter-country" class="form-label">País de Origen</label>
                                    <select class="form-select" id="filter-country">
                                        <option value="">Todos</option>
                                        <option value="RU">Rusia</option>
                                        <option value="CN">China</option>
                                        <option value="KP">Corea del Norte</option>
                                        <option value="IR">Irán</option>
                                        <option value="US">Estados Unidos</option>
                                    </select>
                                </div>

                                <div class="col-md-3 mb-3 d-flex align-items-end">
                                    <button type="button" class="btn btn-primary me-2" onclick="applyThreatFilters()">
                                        <i class="fas fa-search me-2"></i>
                                        Filtrar
                                    </button>
                                    <button type="button" class="btn btn-outline-secondary" onclick="clearThreatFilters()">
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

        <!-- ========================================
                                        TABLA DE AMENAZAS
                                        ======================================== -->
        <div class="row">
            <div class="col-12">
                <div class="card shadow mb-4">
                    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="fas fa-list me-2"></i>
                            Base de Datos de Amenazas
                        </h6>

                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered" id="threats-table" width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th>IP</th>
                                        <th>Tipo</th>
                                        <th>Clasificación</th>
                                        <th>Score</th>
                                        <th>Confianza</th>
                                        <th>País</th>
                                        <th>Estado</th>
                                        <th>Última Actualización</th>
                                    </tr>
                                </thead>
                                <tbody id="threats-table-body">
                                    <tr>
                                        <td colspan="9" class="text-center py-4">
                                            <i class="fas fa-spinner fa-spin fa-2x text-gray-400"></i>
                                            <p class="mt-2 text-gray-500">Cargando amenazas...</p>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- Paginación -->
                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <div class="text-muted">
                                Mostrando <span id="threats-showing-start">0</span> a <span
                                    id="threats-showing-end">0</span> de <span id="threats-showing-total">0</span>
                                amenazas
                            </div>
                            <nav aria-label="Navegación de amenazas">
                                <ul class="pagination mb-0" id="threats-pagination">
                                    <!-- Paginación se generará dinámicamente -->
                                </ul>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
        </div>


    </div>

    <!-- ========================================
                                    MODALES Y COMPONENTES ADICIONALES
                                    ======================================== -->

    <!-- Modal de Detalles de Amenaza -->
    <div class="modal fade" id="threatDetailsModal" tabindex="-1">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-brain text-danger me-2"></i>
                        Detalles de la Amenaza
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="threat-details-content">
                    <!-- Contenido se cargará dinámicamente -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>



@stop

@section('css')
    <style>
        .threat-status-indicator {
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
                box-shadow: 0 0 0 0 rgba(246, 194, 62, 0.7);
            }

            70% {
                box-shadow: 0 0 0 10px rgba(246, 194, 62, 0);
            }

            100% {
                box-shadow: 0 0 0 0 rgba(246, 194, 62, 0);
            }
        }

        .chart-area {
            position: relative;
            height: 300px;
        }

        .threat-row {
            cursor: pointer;
            transition: background-color 0.2s;
        }

        .threat-row:hover {
            background-color: #f8f9fc;
        }

        .threat-row.critical {
            border-left: 4px solid #e74a3b;
        }

        .threat-row.high {
            border-left: 4px solid #f6c23e;
        }

        .threat-row.medium {
            border-left: 4px solid #fd7e14;
        }

        .threat-badge {
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
    </style>
@stop

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Variables globales
        let threatEvolutionChart;
        let currentThreatPage = 1;
        let threatsPerPage = 25;
        let totalThreats = 0;

        // Inicialización
        document.addEventListener('DOMContentLoaded', function() {
            initializeThreatCharts();
            loadThreats();
        });

        function initializeThreatCharts() {
            // Gráfico de evolución temporal
            const evolutionCtx = document.getElementById('threatEvolutionChart').getContext('2d');
            threatEvolutionChart = new Chart(evolutionCtx, {
                type: 'line',
                data: {
                    labels: [],
                    datasets: [{
                        label: 'Amenazas Críticas',
                        data: [],
                        borderColor: '#e74a3b',
                        backgroundColor: 'rgba(231, 74, 59, 0.1)',
                        tension: 0.4
                    }, {
                        label: 'Amenazas Altas',
                        data: [],
                        borderColor: '#f6c23e',
                        backgroundColor: 'rgba(246, 194, 62, 0.1)',
                        tension: 0.4
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

        function loadThreats(page = 1) {
            currentThreatPage = page;

            const tableBody = document.getElementById('threats-table-body');

            // Mostrar loading
            tableBody.innerHTML = `
        <tr>
            <td colspan="8" class="text-center py-4">
                <i class="fas fa-spinner fa-spin fa-2x text-gray-400"></i>
                <p class="mt-2 text-gray-500">Cargando amenazas...</p>
            </td>
        </tr>
    `;

            // Simular delay de carga
            setTimeout(() => {
                const threats = generateSampleThreats();
                renderThreatsTable(threats);
                updateThreatsPagination();
                updateThreatCharts(threats);
            }, 1000);
        }

        function generateSampleThreats() {
            const threats = [];
            const ips = ['203.0.113.10', '185.199.108.154', '198.51.100.75', '104.21.92.193', '45.33.12.200'];
            const types = ['malware', 'phishing', 'ddos', 'apt', 'ransomware', 'botnet'];
            const classifications = ['critical', 'high', 'medium'];
            const countries = ['RU', 'CN', 'KP', 'IR', 'US'];
            const statuses = ['active', 'monitoring', 'mitigated', 'investigating'];

            for (let i = 0; i < 50; i++) {
                const score = Math.floor(Math.random() * 100);
                const classification = getThreatClassification(score);

                threats.push({
                    id: i + 1,
                    ip: ips[Math.floor(Math.random() * ips.length)],
                    type: types[Math.floor(Math.random() * types.length)],
                    classification: classification,
                    score: score,
                    confidence: Math.floor(Math.random() * 40) + 60,
                    country: countries[Math.floor(Math.random() * countries.length)],
                    status: statuses[Math.floor(Math.random() * statuses.length)],
                    lastUpdated: new Date(Date.now() - Math.random() * 30 * 24 * 60 * 60 * 1000),
                    description: 'Amenaza detectada por múltiples fuentes de inteligencia',
                    sources: ['abuseipdb', 'virustotal'],
                    malwareFamily: 'Emotet',
                    attackVectors: ['email', 'web', 'network']
                });
            }

            return threats;
        }

        function getThreatClassification(score) {
            if (score >= 80) return 'critical';
            if (score >= 60) return 'high';
            if (score >= 40) return 'medium';
            return 'medium'; // Cambiado de 'low' a 'medium' para mantener solo 3 clasificaciones
        }

        function renderThreatsTable(threats) {
            const tableBody = document.getElementById('threats-table-body');
            const startIndex = (currentThreatPage - 1) * threatsPerPage;
            const endIndex = startIndex + threatsPerPage;
            const pageThreats = threats.slice(startIndex, endIndex);

            tableBody.innerHTML = '';

            pageThreats.forEach(threat => {
                const row = document.createElement('tr');
                row.className = `threat-row ${threat.classification}`;
                row.onclick = () => showThreatDetails(threat);

                row.innerHTML = `
            <td>
                <div class="d-flex align-items-center">
                    <div class="me-2">
                        <i class="fas fa-globe text-muted"></i>
                    </div>
                    <div>
                        <strong>${threat.ip}</strong>
                        <br><small class="text-muted">${threat.malwareFamily || 'N/A'}</small>
                    </div>
                </div>
            </td>
            <td>
                <span class="badge bg-secondary threat-badge">${threat.type}</span>
            </td>
            <td>
                <span class="badge threat-badge bg-${getClassificationBadgeColor(threat.classification)}">
                    ${threat.classification.toUpperCase()}
                </span>
            </td>
            <td>
                <div class="d-flex align-items-center">
                    <div class="me-2">
                        <strong>${threat.score}</strong>
                    </div>
                    <div class="score-indicator">
                        <div class="score-fill ${threat.classification}" style="width: ${threat.score}%"></div>
                    </div>
                </div>
            </td>
            <td>
                <div class="progress" style="height: 20px;">
                    <div class="progress-bar bg-${getConfidenceColor(threat.confidence)}" 
                         style="width: ${threat.confidence}%">
                        ${threat.confidence}%
                    </div>
                </div>
            </td>
            <td>
                <span class="badge bg-info">${threat.country}</span>
            </td>
            <td>
                <span class="badge bg-${getStatusBadgeColor(threat.status)}">${threat.status}</span>
            </td>
            <td>
                <small>${formatDate(threat.lastUpdated)}</small>
            </td>
        `;

                tableBody.appendChild(row);
            });

            totalThreats = threats.length;
            updateThreatsShowingInfo();
        }

        function getClassificationBadgeColor(classification) {
            const colors = {
                'critical': 'danger',
                'high': 'warning',
                'medium': 'warning'
            };
            return colors[classification] || 'secondary';
        }

        function getConfidenceColor(confidence) {
            if (confidence >= 90) return 'success';
            if (confidence >= 75) return 'info';
            if (confidence >= 60) return 'warning';
            return 'danger';
        }

        function getStatusBadgeColor(status) {
            const colors = {
                'active': 'danger',
                'monitoring': 'warning',
                'mitigated': 'success',
                'investigating': 'info'
            };
            return colors[status] || 'secondary';
        }

        function formatDate(date) {
            return new Intl.DateTimeFormat('es-ES', {
                day: '2-digit',
                month: '2-digit',
                year: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            }).format(date);
        }

        function updateThreatsShowingInfo() {
            const start = (currentThreatPage - 1) * threatsPerPage + 1;
            const end = Math.min(currentThreatPage * threatsPerPage, totalThreats);

            document.getElementById('threats-showing-start').textContent = start;
            document.getElementById('threats-showing-end').textContent = end;
            document.getElementById('threats-showing-total').textContent = totalThreats;
        }

        function updateThreatsPagination() {
            const totalPages = Math.ceil(totalThreats / threatsPerPage);
            const pagination = document.getElementById('threats-pagination');

            pagination.innerHTML = '';

            // Botón anterior
            const prevLi = document.createElement('li');
            prevLi.className = `page-item ${currentThreatPage === 1 ? 'disabled' : ''}`;
            prevLi.innerHTML = `
        <a class="page-link" href="#" onclick="loadThreats(${currentThreatPage - 1})">
            <i class="fas fa-chevron-left"></i>
        </a>
    `;
            pagination.appendChild(prevLi);

            // Páginas numeradas
            for (let i = 1; i <= totalPages; i++) {
                if (i === 1 || i === totalPages || (i >= currentThreatPage - 2 && i <= currentThreatPage + 2)) {
                    const li = document.createElement('li');
                    li.className = `page-item ${i === currentThreatPage ? 'active' : ''}`;
                    li.innerHTML = `
                <a class="page-link" href="#" onclick="loadThreats(${i})">${i}</a>
            `;
                    pagination.appendChild(li);
                } else if (i === currentThreatPage - 3 || i === currentThreatPage + 3) {
                    const li = document.createElement('li');
                    li.className = 'page-item disabled';
                    li.innerHTML = '<span class="page-link">...</span>';
                    pagination.appendChild(li);
                }
            }

            // Botón siguiente
            const nextLi = document.createElement('li');
            nextLi.className = `page-item ${currentThreatPage === totalPages ? 'disabled' : ''}`;
            nextLi.innerHTML = `
        <a class="page-link" href="#" onclick="loadThreats(${currentThreatPage + 1})">
            <i class="fas fa-chevron-right"></i>
        </a>
    `;
            pagination.appendChild(nextLi);
        }

        function updateThreatCharts(threats) {
            // Actualizar gráfico de evolución
            const dates = [];
            const criticalData = [];
            const highData = [];

            for (let i = 29; i >= 0; i--) {
                const date = new Date();
                date.setDate(date.getDate() - i);
                dates.push(date.toLocaleDateString('es-ES', {
                    month: 'short',
                    day: 'numeric'
                }));

                const dayThreats = threats.filter(t =>
                    new Date(t.lastUpdated).toDateString() === date.toDateString()
                );

                criticalData.push(dayThreats.filter(t => t.classification === 'critical').length);
                highData.push(dayThreats.filter(t => t.classification === 'high').length);
            }

            threatEvolutionChart.data.labels = dates;
            threatEvolutionChart.data.datasets[0].data = criticalData;
            threatEvolutionChart.data.datasets[1].data = highData;
            threatEvolutionChart.update();
        }





        function applyThreatFilters() {
            // Aquí iría la lógica para aplicar filtros
            console.log('Aplicando filtros de amenazas...');
            loadThreats(1);
        }

        function clearThreatFilters() {
            document.getElementById('threats-filter-form').reset();
            loadThreats(1);
        }



        function showThreatDetails(threat) {
            const modal = new bootstrap.Modal(document.getElementById('threatDetailsModal'));
            const content = document.getElementById('threat-details-content');

            content.innerHTML = `
        <div class="row">
            <div class="col-md-6">
                <h6>Información Básica</h6>
                <table class="table table-sm">
                    <tr><td><strong>IP:</strong></td><td>${threat.ip}</td></tr>
                    <tr><td><strong>Tipo:</strong></td><td><span class="badge bg-secondary">${threat.type}</span></td></tr>
                    <tr><td><strong>Clasificación:</strong></td><td><span class="badge bg-${getClassificationBadgeColor(threat.classification)}">${threat.classification.toUpperCase()}</span></td></tr>
                    <tr><td><strong>Score:</strong></td><td>${threat.score}/100</td></tr>
                    <tr><td><strong>Confianza:</strong></td><td>${threat.confidence}%</td></tr>
                    <tr><td><strong>Estado:</strong></td><td><span class="badge bg-${getStatusBadgeColor(threat.status)}">${threat.status}</span></td></tr>
                </table>
            </div>
            <div class="col-md-6">
                <h6>Información Geográfica</h6>
                <table class="table table-sm">
                    <tr><td><strong>País:</strong></td><td>${threat.country}</td></tr>
                    <tr><td><strong>Última Actualización:</strong></td><td>${formatDate(threat.lastUpdated)}</td></tr>
                </table>
                
                <h6 class="mt-3">Descripción</h6>
                <p class="text-muted">${threat.description}</p>
            </div>
        </div>
        
        <div class="row mt-3">
            <div class="col-12">
                <h6>Detalles Técnicos</h6>
                <div class="row">
                    <div class="col-md-4">
                        <strong>Familia de Malware:</strong><br>
                        <span class="badge bg-info">${threat.malwareFamily || 'N/A'}</span>
                    </div>
                    <div class="col-md-4">
                        <strong>Fuentes:</strong><br>
                        ${threat.sources.map(s => `<span class="badge bg-secondary me-1">${s}</span>`).join('')}
                    </div>
                    <div class="col-md-4">
                        <strong>Vectores de Ataque:</strong><br>
                        ${threat.attackVectors.map(v => `<span class="badge bg-warning me-1">${v}</span>`).join('')}
                    </div>
                </div>
            </div>
        </div>
    `;

            modal.show();
        }
    </script>
@stop
