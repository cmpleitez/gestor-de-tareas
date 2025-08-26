@extends('dashboard')

@section('contenedor')
    <div class="container-fluid" data-risk-distribution="{{ trim(json_encode($risk_distribution ?? [])) }}"
        data-threats-by-country="{{ trim(json_encode($threats_by_country ?? [])) }}">
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
                            Evolución de Amenazas (Últimos 3 días)
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
                                        @foreach ($threatTypes ?? [] as $type => $name)
                                            <option value="{{ $type }}">{{ $name }}</option>
                                        @endforeach
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
                                        @foreach ($countries ?? [] as $code => $name)
                                            <option value="{{ $code }}">{{ $name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-3 mb-3 d-flex align-items-end">
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
                            Base de Datos de Amenazas (Últimos 3 días)
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
                    toastr.error("{{ $error_message }}", 'Error de Inteligencia de Amenazas', {
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
        let threatEvolutionChart;
        let currentThreatPage = 1;
        let threatsPerPage = 25;
        let totalThreats = 0;

        // Datos reales enviados desde el controlador
        const serverThreats = {!! json_encode($threats ?? []) !!};
        const serverEvolutionData = {!! json_encode($serverEvolutionData ?? []) !!};

        // Inicialización
        document.addEventListener('DOMContentLoaded', function() {
            initializeThreatCharts();
            loadThreats();
            updateActiveThreatsCount();
            setupFilterEventListeners();
        });

        function setupFilterEventListeners() {
            // Filtrado automático al cambiar selecciones
            document.getElementById('filter-threat-type').addEventListener('change', function() {
                currentThreatPage = 1;
                loadThreats();
            });

            document.getElementById('filter-classification').addEventListener('change', function() {
                currentThreatPage = 1;
                loadThreats();
            });

            document.getElementById('filter-country').addEventListener('change', function() {
                currentThreatPage = 1;
                loadThreats();
            });
        }

        function initializeThreatCharts() {
            // Gráfico de evolución temporal
            const evolutionCtx = document.getElementById('threatEvolutionChart').getContext('2d');
            threatEvolutionChart = new Chart(evolutionCtx, {
                type: 'line',
                data: {
                    labels: serverEvolutionData.dates || [],
                    datasets: [{
                        label: 'Amenazas Críticas',
                        data: serverEvolutionData.critical || [],
                        borderColor: '#e74a3b',
                        backgroundColor: 'rgba(231, 74, 59, 0.1)',
                        tension: 0.4
                    }, {
                        label: 'Amenazas Altas',
                        data: serverEvolutionData.high || [],
                        borderColor: '#f6c23e',
                        backgroundColor: 'rgba(246, 194, 62, 0.1)',
                        tension: 0.4
                    }, {
                        label: 'Amenazas Medias',
                        data: serverEvolutionData.medium || [],
                        borderColor: '#36b9cc',
                        backgroundColor: 'rgba(54, 185, 204, 0.1)',
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

            // Obtener filtros aplicados
            const threatTypeFilter = document.getElementById('filter-threat-type').value;
            const classificationFilter = document.getElementById('filter-classification').value;
            const countryFilter = document.getElementById('filter-country').value;

            // Usar los datos reales del servidor
            if (serverThreats && serverThreats.length > 0) {
                // Aplicar filtros
                let filteredThreats = serverThreats.filter(threat => {
                    // Filtro por tipo de amenaza
                    if (threatTypeFilter && threat.type !== threatTypeFilter) {
                        return false;
                    }

                    // Filtro por clasificación
                    if (classificationFilter && threat.classification !== classificationFilter) {
                        return false;
                    }

                    // Filtro por país
                    if (countryFilter && threat.country !== countryFilter) {
                        return false;
                    }

                    return true;
                });

                renderThreatsTable(filteredThreats);
                updateThreatsPagination();
            } else {
                // Si no hay datos del servidor, mostrar mensaje
                showNoThreatsMessage();
            }
        }

        function renderThreatsTable(threats) {
            const tableBody = document.getElementById('threats-table-body');
            const startIndex = (currentThreatPage - 1) * threatsPerPage;
            const endIndex = startIndex + threatsPerPage;
            const pageThreats = threats.slice(startIndex, endIndex);

            tableBody.innerHTML = '';

            if (pageThreats.length === 0) {
                showNoThreatsMessage();
                return;
            }

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
                            <strong>${threat.ip_address || threat.ip || 'N/A'}</strong>
                            <br><small class="text-muted">${threat.malware_family || threat.malwareFamily || 'N/A'}</small>
                        </div>
                    </div>
                </td>
                <td>
                    <span class="badge bg-secondary threat-badge">${threat.threat_type || threat.type || 'N/A'}</span>
                </td>
                <td>
                    <span class="badge threat-badge bg-${getClassificationBadgeColor(threat.threat_level || threat.classification)}">
                        ${(threat.threat_level || threat.classification || 'N/A').toUpperCase()}
                    </span>
                </td>
                <td>
                    <div class="d-flex align-items-center">
                        <div class="me-2">
                            <strong>${threat.threat_score || threat.score || 'N/A'}</strong>
                        </div>
                        <div class="score-indicator">
                            <div class="score-fill ${threat.threat_level || threat.classification || 'medium'}" style="width: ${threat.threat_score || threat.score || 0}%"></div>
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
                    <span class="badge bg-info">${threat.country || 'N/A'}</span>
                </td>
                <td>
                    <span class="badge bg-${getStatusBadgeColor(threat.status || 'active')}">${threat.status || 'active'}</span>
                </td>
                <td>
                    <small>${formatDate(threat.created_at)}</small>
                </td>
            `;

                tableBody.appendChild(row);
            });

            totalThreats = threats.length;
            updateThreatsShowingInfo();
        }

        function showNoThreatsMessage() {
            const tableBody = document.getElementById('threats-table-body');
            tableBody.innerHTML = `
            <tr>
                <td colspan="8" class="text-center py-4">
                    <i class="fas fa-info-circle text-muted fa-2x mb-3"></i>
                    <p class="text-muted">No hay amenazas de inteligencia disponibles</p>
                    <small class="text-muted">Las amenazas se cargarán desde la base de datos</small>
                </td>
            </tr>
        `;

            // Actualizar contadores
            document.getElementById('threats-showing-start').textContent = '0';
            document.getElementById('threats-showing-end').textContent = '0';
            document.getElementById('threats-showing-total').textContent = '0';
        }

        function updateActiveThreatsCount() {
            const activeThreatsElement = document.getElementById('active-threats-count');
            if (activeThreatsElement) {
                const activeThreats = {!! json_encode($activeThreats ?? 0) !!};
                activeThreatsElement.textContent = activeThreats;
            }
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

        function formatDate(dateString) {
            // Verificar si dateString es válido
            if (!dateString || dateString === 'null' || dateString === 'undefined') {
                return 'N/A';
            }

            try {
                const date = new Date(dateString);

                // Verificar si la fecha es válida
                if (isNaN(date.getTime())) {
                    return 'Fecha inválida';
                }

                return new Intl.DateTimeFormat('es-ES', {
                    day: '2-digit',
                    month: '2-digit',
                    year: 'numeric',
                    hour: '2-digit',
                    minute: '2-digit'
                }).format(date);
            } catch (error) {
                return 'Error de fecha';
            }
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

            if (totalPages <= 1) {
                return;
            }

            // Botón anterior
            const prevLi = document.createElement('li');
            prevLi.className = `page-item ${currentThreatPage === 1 ? 'disabled' : ''}`;
            prevLi.innerHTML = `<a class="page-link" href="#" onclick="loadThreats(${currentThreatPage - 1})">Anterior</a>`;
            pagination.appendChild(prevLi);

            // Páginas numeradas
            const startPage = Math.max(1, currentThreatPage - 2);
            const endPage = Math.min(totalPages, currentThreatPage + 2);

            for (let i = startPage; i <= endPage; i++) {
                const li = document.createElement('li');
                li.className = `page-item ${i === currentThreatPage ? 'active' : ''}`;
                li.innerHTML = `<a class="page-link" href="#" onclick="loadThreats(${i})">${i}</a>`;
                pagination.appendChild(li);
            }

            // Botón siguiente
            const nextLi = document.createElement('li');
            nextLi.className = `page-item ${currentThreatPage === totalPages ? 'disabled' : ''}`;
            nextLi.innerHTML =
                `<a class="page-link" href="#" onclick="loadThreats(${currentThreatPage + 1})">Siguiente</a>`;
            pagination.appendChild(nextLi);
        }



        function clearThreatFilters() {
            document.getElementById('threats-filter-form').reset();
            currentThreatPage = 1;
            // Restaurar datos originales sin filtros
            renderThreatsTable(serverThreats);
            updateThreatsPagination();
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
                        <tr><td><strong>Última Actualización:</strong></td><td>${formatDate(threat.created_at)}</td></tr>
                    </table>
                    
                    <h6 class="mt-3">Descripción</h6>
                    <p class="text-muted">Amenaza detectada por múltiples fuentes de inteligencia</p>
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
                            <strong>Origen Geográfico:</strong><br>
                            <span class="badge bg-secondary">${threat.geographicOrigin || 'N/A'}</span>
                        </div>
                        <div class="col-md-4">
                            <strong>Vectores de Ataque:</strong><br>
                            ${Array.isArray(threat.attackVectors) && threat.attackVectors.length > 0 
                                ? threat.attackVectors.map(v => `<span class="badge bg-warning me-1">${v}</span>`).join('') 
                                : '<span class="badge bg-secondary">N/A</span>'}
                        </div>
                    </div>
                </div>
            </div>
        `;

            modal.show();
        }
    </script>

    <!-- BEGIN: Application JavaScript -->
    <script src="{{ asset('app-assets/js/security-dashboard.js') }}"></script>
    <!-- END: Application JavaScript -->
@stop
