@extends('dashboard')
@section('css')
    <style>
        .border-left-primary {
            border-left: 0.25rem solid #4e73df !important;
        }

        .border-left-danger {
            border-left: 0.25rem solid #e74a3b !important;
        }

        .border-left-warning {
            border-left: 0.25rem solid #f6c23e !important;
        }

        .border-left-info {
            border-left: 0.25rem solid #36b9cc !important;
        }

        .border-left-success {
            border-left: 0.25rem solid #1cc88a !important;
        }

        .risk-badge {
            font-size: 0.75rem;
            padding: 0.25rem 0.5rem;
        }

        .event-row {
            cursor: pointer;
            transition: background-color 0.2s;
        }

        .event-row:hover {
            background-color: #f8f9fc;
        }

        .event-row.critical {
            border-left: 4px solid #e74a3b;
        }

        .event-row.high {
            border-left: 4px solid #f6c23e;
        }

        .event-row.medium {
            border-left: 4px solid #fd7e14;
        }

        .event-row.low {
            border-left: 4px solid #20c9a6;
        }

        .event-row.minimal {
            border-left: 4px solid #1cc88a;
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
@stop

@section('contenedor')
    <div class="container-fluid">
        <!-- ========================================
                            HEADER DE EVENTOS DE SEGURIDAD
                            ======================================== -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card bg-gradient-info text-white">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <h1 class="mb-2">
                                    <i class="fas fa-exclamation-triangle me-3"></i>
                                    Eventos de Seguridad
                                </h1>
                                <p class="mb-0 fs-5">
                                    Monitoreo detallado de todos los eventos de seguridad detectados por el sistema
                                </p>
                            </div>
                            <div class="col-md-4 text-end">
                                <div class="d-flex justify-content-end align-items-center">
                                    <div class="me-4">
                                        <div class="fs-6 opacity-75">Total de Eventos</div>
                                        <div class="fs-4 fw-bold" id="total-events-count">
                                            <i class="fas fa-spinner fa-spin"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ========================================
                            FILTROS Y CONTROLES DE BÚSQUEDA
                            ======================================== -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card shadow">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="fas fa-filter me-2"></i>
                            Filtros y Búsqueda
                        </h6>
                    </div>
                    <div class="card-body">
                        <form id="events-filter-form">
                            <div class="row">
                                <!-- Filtro por IP -->
                                <div class="col-md-3 mb-3">
                                    <label for="filter-ip" class="form-label">Dirección IP</label>
                                    <input type="text" class="form-control" id="filter-ip" placeholder="192.168.1.100">
                                </div>

                                <!-- Filtro por Nivel de Riesgo -->
                                <div class="col-md-2 mb-3">
                                    <label for="filter-risk-level" class="form-label">Nivel de Riesgo</label>
                                    <select class="form-select" id="filter-risk-level">
                                        <option value="">Todos</option>
                                        <option value="critical">Crítico</option>
                                        <option value="high">Alto</option>
                                        <option value="medium">Medio</option>
                                        <option value="low">Bajo</option>
                                        <option value="minimal">Mínimo</option>
                                    </select>
                                </div>

                                <!-- Filtro por Categoría -->
                                <div class="col-md-2 mb-3">
                                    <label for="filter-category" class="form-label">Categoría</label>
                                    <select class="form-select" id="filter-category">
                                        <option value="">Todas</option>
                                        <option value="malware">Malware</option>
                                        <option value="phishing">Phishing</option>
                                        <option value="ddos">DDoS</option>
                                        <option value="scanning">Escaneo</option>
                                        <option value="injection">Inyección</option>
                                        <option value="brute_force">Fuerza Bruta</option>
                                    </select>
                                </div>

                                <!-- Filtro por Fecha -->
                                <div class="col-md-2 mb-3">
                                    <label for="filter-date" class="form-label">Fecha</label>
                                    <select class="form-select" id="filter-date">
                                        <option value="24h">Últimas 24h</option>
                                        <option value="7d" selected>Últimos 7 días</option>
                                        <option value="30d">Últimos 30 días</option>
                                        <option value="90d">Últimos 90 días</option>
                                        <option value="custom">Personalizado</option>
                                    </select>
                                </div>

                                <!-- Botones de Acción -->
                                <div class="col-md-3 mb-3 d-flex align-items-end">
                                    <button type="button" class="btn btn-primary me-2" onclick="applyFilters()">
                                        <i class="fas fa-search me-2"></i>
                                        Filtrar
                                    </button>
                                    <button type="button" class="btn btn-outline-secondary" onclick="clearFilters()">
                                        <i class="fas fa-times me-2"></i>
                                        Limpiar
                                    </button>
                                </div>
                            </div>

                            <!-- Filtros Avanzados (Colapsables) -->
                            <div class="row mt-3">
                                <div class="col-12">
                                    <button class="btn btn-link" type="button" data-bs-toggle="collapse"
                                        data-bs-target="#advanced-filters">
                                        <i class="fas fa-chevron-down me-2"></i>
                                        Filtros Avanzados
                                    </button>
                                </div>
                            </div>

                            <div class="collapse" id="advanced-filters">
                                <div class="row mt-3">
                                    <!-- Filtro por País -->
                                    <div class="col-md-3 mb-3">
                                        <label for="filter-country" class="form-label">País</label>
                                        <select class="form-select" id="filter-country">
                                            <option value="">Todos</option>
                                            <option value="US">Estados Unidos</option>
                                            <option value="CN">China</option>
                                            <option value="RU">Rusia</option>
                                            <option value="DE">Alemania</option>
                                            <option value="GB">Reino Unido</option>
                                        </select>
                                    </div>

                                    <!-- Filtro por Acción Tomada -->
                                    <div class="col-md-3 mb-3">
                                        <label for="filter-action" class="form-label">Acción Tomada</label>
                                        <select class="form-select" id="filter-action">
                                            <option value="">Todas</option>
                                            <option value="allow">Permitir</option>
                                            <option value="block">Bloquear</option>
                                            <option value="challenge">Desafío</option>
                                            <option value="monitor">Monitorear</option>
                                            <option value="rate_limit">Rate Limit</option>
                                        </select>
                                    </div>

                                    <!-- Filtro por Score -->
                                    <div class="col-md-3 mb-3">
                                        <label for="filter-score-min" class="form-label">Score Mínimo</label>
                                        <input type="number" class="form-control" id="filter-score-min" min="0"
                                            max="100" placeholder="0">
                                    </div>

                                    <div class="col-md-3 mb-3">
                                        <label for="filter-score-max" class="form-label">Score Máximo</label>
                                        <input type="number" class="form-control" id="filter-score-max" min="0"
                                            max="100" placeholder="100">
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- ========================================
                            ESTADÍSTICAS RÁPIDAS
                            ======================================== -->
        <div class="row mb-4">
            <div class="col-md-3 mb-3">
                <div class="card border-left-danger shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                    Eventos Críticos
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800" id="critical-events-count">0</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-exclamation-triangle fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3 mb-3">
                <div class="card border-left-warning shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                    Eventos Altos
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800" id="high-events-count">0</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-exclamation-circle fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3 mb-3">
                <div class="card border-left-info shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                    IPs Únicas
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800" id="unique-ips-count">0</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-globe fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3 mb-3">
                <div class="card border-left-success shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                    Resueltos
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800" id="resolved-events-count">0</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ========================================
                            TABLA DE EVENTOS DE SEGURIDAD
                            ======================================== -->
        <div class="row">
            <div class="col-12">
                <div class="card shadow mb-4">
                    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="fas fa-list me-2"></i>
                            Lista de Eventos
                        </h6>
                        <div class="d-flex">
                            <button class="btn btn-outline-primary btn-sm me-2" onclick="exportEvents()">
                                <i class="fas fa-download me-2"></i>
                                Exportar
                            </button>
                            <button class="btn btn-outline-success btn-sm" onclick="refreshEvents()">
                                <i class="fas fa-sync-alt me-2"></i>
                                Actualizar
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered" id="security-events-table" width="100%"
                                cellspacing="0">
                                <thead>
                                    <tr>
                                        <th>IP</th>
                                        <th>Score</th>
                                        <th>Riesgo</th>
                                        <th>Categoría</th>
                                        <th>Acción</th>
                                        <th>Fecha</th>
                                        <th>Estado</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody id="events-table-body">
                                    <tr>
                                        <td colspan="8" class="text-center py-4">
                                            <i class="fas fa-spinner fa-spin fa-2x text-gray-400"></i>
                                            <p class="mt-2 text-gray-500">Cargando eventos...</p>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- Paginación -->
                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <div class="text-muted">
                                Mostrando <span id="showing-start">0</span> a <span id="showing-end">0</span> de <span
                                    id="showing-total">0</span> eventos
                            </div>
                            <nav aria-label="Navegación de eventos">
                                <ul class="pagination mb-0" id="events-pagination">
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

    <!-- Modal de Detalles del Evento -->
    <div class="modal fade" id="eventDetailsModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-info-circle text-info me-2"></i>
                        Detalles del Evento de Seguridad
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="event-details-content">
                    <!-- Contenido se cargará dinámicamente -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    <button type="button" class="btn btn-primary" onclick="takeActionOnEvent()">
                        <i class="fas fa-tools me-2"></i>
                        Tomar Acción
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de Acción sobre Evento -->
    <div class="modal fade" id="eventActionModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-tools text-warning me-2"></i>
                        Acción sobre Evento
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="event-action-form">
                        <div class="mb-3">
                            <label for="action-type" class="form-label">Tipo de Acción</label>
                            <select class="form-select" id="action-type" required>
                                <option value="">Seleccione una acción</option>
                                <option value="block">Bloquear IP</option>
                                <option value="whitelist">Agregar a Whitelist</option>
                                <option value="investigate">Marcar para Investigación</option>
                                <option value="resolve">Marcar como Resuelto</option>
                                <option value="escalate">Escalar a Equipo</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="action-notes" class="form-label">Notas</label>
                            <textarea class="form-control" id="action-notes" rows="3" placeholder="Describa la acción a tomar..."
                                required></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="action-priority" class="form-label">Prioridad</label>
                            <select class="form-select" id="action-priority">
                                <option value="low">Baja</option>
                                <option value="medium" selected>Media</option>
                                <option value="high">Alta</option>
                                <option value="urgent">Urgente</option>
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" onclick="executeEventAction()">
                        <i class="fas fa-check me-2"></i>
                        Ejecutar Acción
                    </button>
                </div>
            </div>
        </div>
    </div>
@stop

@section('js')
    <script>
        // Variables globales
        let currentPage = 1;
        let eventsPerPage = 25;
        let totalEvents = 0;
        let currentFilters = {};

        // Inicialización
        document.addEventListener('DOMContentLoaded', function() {
            loadEvents();
            loadEventStats();
        });

        function loadEvents(page = 1) {
            currentPage = page;

            // Simular carga de eventos (en producción esto vendría de una API)
            const tableBody = document.getElementById('events-table-body');

            // Mostrar loading
            tableBody.innerHTML = `
                <tr>
                    <td colspan="8" class="text-center py-4">
                        <i class="fas fa-spinner fa-spin fa-2x text-gray-400"></i>
                        <p class="mt-2 text-gray-500">Cargando eventos...</p>
                    </td>
                </tr>`;
            // Cargar eventos reales desde el servidor
            setTimeout(() => {
                loadRealEvents();
            }, 1000);
        }

        function loadRealEvents() {
            // Cargar eventos reales desde el servidor
            fetch('/security/events/data')
                .then(response => response.json())
                .then(data => {
                    if (data.events && data.events.length > 0) {
                        renderEventsTable(data.events);
                    } else {
                        renderEmptyState();
                    }
                    updatePagination();
                })
                .catch(error => {
                    console.error('Error cargando eventos:', error);
                    renderEmptyState();
                });
        }

        function getRiskLevel(score) {
            if (score >= 80) return 'critical';
            if (score >= 60) return 'high';
            if (score >= 40) return 'medium';
            if (score >= 20) return 'low';
            return 'minimal';
        }

        function renderEventsTable(events) {
            const tableBody = document.getElementById('events-table-body');
            const startIndex = (currentPage - 1) * eventsPerPage;
            const endIndex = startIndex + eventsPerPage;
            const pageEvents = events.slice(startIndex, endIndex);
            tableBody.innerHTML = '';
            pageEvents.forEach(event => {
                const row = document.createElement('tr');
                row.className = `event-row ${event.risk_level}`;
                row.onclick = () => showEventDetails(event);
                row.innerHTML = `
                    <td>
                        <div class="d-flex align-items-center">
                            <div class="me-2">
                                <i class="fas fa-globe text-muted"></i>
                            </div>
                            <div>
                                <strong>${event.ip}</strong>
                                <br><small class="text-muted">${event.city}, ${event.country}</small>
                            </div>
                        </div>
                    </td>
                    <td>
                        <div class="d-flex align-items-center">
                            <div class="me-2">
                                <strong>${event.score}</strong>
                            </div>
                            <div class="score-indicator">
                                <div class="score-fill ${event.risk_level}" style="width: ${event.score}%"></div>
                            </div>
                        </div>
                    </td>
                    <td>
                        <span class="badge risk-badge bg-${getRiskBadgeColor(event.risk_level)}">
                            ${event.risk_level.toUpperCase()}
                        </span>
                    </td>
                    <td>
                        <span class="badge bg-secondary">${event.category}</span>
                    </td>
                    <td>
                        <span class="badge bg-${getActionBadgeColor(event.action)}">${event.action}</span>
                    </td>
                    <td>
                        <small>${formatDate(event.date)}</small>
                    </td>
                    <td>
                        <span class="badge bg-${getStatusBadgeColor(event.status)}">${event.status}</span>
                    </td>
                    <td>
                        <div class="btn-group btn-group-sm">
                            <button class="btn btn-outline-primary" onclick="event.stopPropagation(); showEventDetails(${JSON.stringify(event).replace(/"/g, '&quot;')})">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button class="btn btn-outline-warning" onclick="event.stopPropagation(); showEventActionModal(${JSON.stringify(event).replace(/"/g, '&quot;')})">
                                <i class="fas fa-tools"></i>
                            </button>
                        </div>
                    </td>`;
                tableBody.appendChild(row);
            });

            totalEvents = events.length;
            updateShowingInfo();
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

        function getActionBadgeColor(action) {
            const colors = {
                'block': 'danger',
                'challenge': 'warning',
                'monitor': 'info',
                'allow': 'success',
                'rate_limit': 'secondary'
            };
            return colors[action] || 'secondary';
        }

        function getStatusBadgeColor(status) {
            const colors = {
                'open': 'danger',
                'investigating': 'warning',
                'resolved': 'success',
                'escalated': 'info'
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

        function updateShowingInfo() {
            const start = (currentPage - 1) * eventsPerPage + 1;
            const end = Math.min(currentPage * eventsPerPage, totalEvents);

            document.getElementById('showing-start').textContent = start;
            document.getElementById('showing-end').textContent = end;
            document.getElementById('showing-total').textContent = totalEvents;
        }

        function updatePagination() {
            const totalPages = Math.ceil(totalEvents / eventsPerPage);
            const pagination = document.getElementById('events-pagination');
            pagination.innerHTML = '';
            // Botón anterior
            const prevLi = document.createElement('li');
            prevLi.className = `page-item ${currentPage === 1 ? 'disabled' : ''}`;
            prevLi.innerHTML = `
                <a class="page-link" href="#" onclick="loadEvents(${currentPage - 1})">
                    <i class="fas fa-chevron-left"></i>
                </a>`;
            pagination.appendChild(prevLi);
            // Páginas numeradas
            for (let i = 1; i <= totalPages; i++) {
                if (i === 1 || i === totalPages || (i >= currentPage - 2 && i <= currentPage + 2)) {
                    const li = document.createElement('li');
                    li.className = `page-item ${i === currentPage ? 'active' : ''}`;
                    li.innerHTML = `<a class="page-link" href="#" onclick="loadEvents(${i})">${i}</a>`;
                    pagination.appendChild(li);
                } else if (i === currentPage - 3 || i === currentPage + 3) {
                    const li = document.createElement('li');
                    li.className = 'page-item disabled';
                    li.innerHTML = '<span class="page-link">...</span>';
                    pagination.appendChild(li);
                }
            }
            // Botón siguiente
            const nextLi = document.createElement('li');
            nextLi.className = `page-item ${currentPage === totalPages ? 'disabled' : ''}`;
            nextLi.innerHTML = `
                <a class="page-link" href="#" onclick="loadEvents(${currentPage + 1})">
                    <i class="fas fa-chevron-right"></i>
                </a>`;
            pagination.appendChild(nextLi);
        }

        function loadEventStats() {
            // Simular carga de estadísticas
            setTimeout(() => {
                document.getElementById('critical-events-count').textContent = '15';
                document.getElementById('high-events-count').textContent = '28';
                document.getElementById('unique-ips-count').textContent = '42';
                document.getElementById('resolved-events-count').textContent = '35';
                document.getElementById('total-events-count').textContent = '1,247';
            }, 500);
        }

        function applyFilters() {
            currentFilters = {
                ip: document.getElementById('filter-ip').value,
                riskLevel: document.getElementById('filter-risk-level').value,
                category: document.getElementById('filter-category').value,
                date: document.getElementById('filter-date').value,
                country: document.getElementById('filter-country').value,
                action: document.getElementById('filter-action').value,
                scoreMin: document.getElementById('filter-score-min').value,
                scoreMax: document.getElementById('filter-score-max').value
            };

            // Aplicar filtros y recargar eventos
            loadEvents(1);
        }

        function clearFilters() {
            document.getElementById('events-filter-form').reset();
            currentFilters = {};
            loadEvents(1);
        }

        function refreshEvents() {
            loadEvents(currentPage);
            loadEventStats();
        }

        function exportEvents() {
            // Aquí iría la lógica para exportar eventos
            console.log('Exportando eventos...');
            showNotification('Exportación iniciada', 'info');
        }

        function showEventDetails(event) {
            const modal = new bootstrap.Modal(document.getElementById('eventDetailsModal'));
            const content = document.getElementById('event-details-content');
            content.innerHTML = `
                <div class="row">
                    <div class="col-md-6">
                        <h6>Información Básica</h6>
                        <table class="table table-sm">
                            <tr><td><strong>IP:</strong></td><td>${event.ip}</td></tr>
                            <tr><td><strong>Score:</strong></td><td>${event.score}/100</td></tr>
                            <tr><td><strong>Nivel de Riesgo:</strong></td><td><span class="badge bg-${getRiskBadgeColor(event.risk_level)}">${event.risk_level.toUpperCase()}</span></td></tr>
                            <tr><td><strong>Categoría:</strong></td><td>${event.category}</td></tr>
                            <tr><td><strong>Acción:</strong></td><td>${event.action}</td></tr>
                            <tr><td><strong>Estado:</strong></td><td><span class="badge bg-${getStatusBadgeColor(event.status)}">${event.status}</span></td></tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <h6>Información Geográfica</h6>
                        <table class="table table-sm">
                            <tr><td><strong>País:</strong></td><td>${event.country}</td></tr>
                            <tr><td><strong>Ciudad:</strong></td><td>${event.city}</td></tr>
                            <tr><td><strong>Fecha:</strong></td><td>${formatDate(event.date)}</td></tr>
                        </table>
                        
                        <h6 class="mt-3">Razón</h6>
                        <p class="text-muted">${event.reason}</p>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-12">
                        <h6>Detalles Técnicos</h6>
                        <div class="alert alert-info">
                            <small>
                                <strong>User Agent:</strong> Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36<br>
                                <strong>Request URI:</strong> /admin/login<br>
                                <strong>Method:</strong> POST<br>
                                <strong>Headers:</strong> Content-Type: application/x-www-form-urlencoded
                            </small>
                        </div>
                    </div>
                </div>`;
            modal.show();
        }

        function showEventActionModal(event) {
            const modal = new bootstrap.Modal(document.getElementById('eventActionModal'));
            modal.show();
        }

        function takeActionOnEvent() {
            // Cerrar modal de detalles
            bootstrap.Modal.getInstance(document.getElementById('eventDetailsModal')).hide();

            // Mostrar modal de acción
            showEventActionModal();
        }

        function executeEventAction() {
            const actionType = document.getElementById('action-type').value;
            const notes = document.getElementById('action-notes').value;
            const priority = document.getElementById('action-priority').value;

            if (!actionType || !notes) {
                alert('Por favor complete todos los campos requeridos.');
                return;
            }

            // Aquí iría la lógica para ejecutar la acción
            console.log(`Ejecutando acción: ${actionType}, Notas: ${notes}, Prioridad: ${priority}`);

            // Cerrar modal
            bootstrap.Modal.getInstance(document.getElementById('eventActionModal')).hide();

            // Mostrar notificación
            showNotification('Acción ejecutada exitosamente', 'success');

            // Recargar eventos
            loadEvents(currentPage);
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

        function renderEmptyState() {
            const tableBody = document.getElementById('events-table-body');
            tableBody.innerHTML = `
                <tr>
                    <td colspan="8" class="text-center py-4">
                        <i class="fas fa-info-circle fa-2x text-gray-400"></i>
                        <p class="mt-2 text-gray-500">No hay eventos de seguridad</p>
                        <small class="text-muted">La tabla está vacía o no se encontraron eventos</small>
                    </td>
                </tr>`;

            // Actualizar información de paginación
            totalEvents = 0;
            updateShowingInfo();
        }
    </script>
@stop
