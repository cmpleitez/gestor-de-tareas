@extends('dashboard')

@section('css')
<style>
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

    /* Estilos para niveles bajos eliminados */

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

    /* Estilos para niveles bajos eliminados */
</style>

<!-- BEGIN: Security Dashboard CSS -->
<link rel="stylesheet" type="text/css" href="{{ asset('app-assets/css/security-dashboard.css') }}">
<!-- END: Security Dashboard CSS -->
@stop

@section('contenedor')
<div class="container-fluid" data-risk-distribution="{{ trim(json_encode($risk_distribution ?? [])) }}"
    data-threats-by-country="{{ trim(json_encode($threats_by_country ?? [])) }}">
    <!-- ========================================
                                                                                                                HEADER DE EVENTOS DE SEGURIDAD
                                                                                                                ======================================== -->
    <x-security.dashboard-header title="Eventos de Seguridad"
        subtitle="Monitoreo y análisis detallado de eventos de seguridad en tiempo real" status="MONITORANDO"
        status_color="info" />

    <!-- ========================================
                                                                                                                FILTROS Y CONTROLES
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
                    <form id="filterForm" class="row g-3">
                        <div class="col-md-3">
                            <label for="ip_filter" class="form-label">Dirección IP</label>
                            <input type="text" class="form-control" id="ip_filter" placeholder="192.168.1.1">
                        </div>
                        <div class="col-md-3">
                            <label for="category_filter" class="form-label">Categoría</label>
                            <select class="form-select" id="category_filter">
                                <option value="">Todas las categorías</option>
                                <option value="suspicious_activity">Actividad Sospechosa</option>
                                <option value="brute_force">Fuerza Bruta</option>
                                <option value="malware">Malware</option>
                                <option value="ddos">DDoS</option>
                                <option value="phishing">Phishing</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="risk_filter" class="form-label">Nivel de Riesgo</label>
                            <select class="form-select" id="risk_filter">
                                <option value="">Todos los niveles</option>
                                <option value="critical">Crítico</option>
                                <option value="high">Alto</option>
                                <option value="medium">Medio</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="date_filter" class="form-label">Fecha</label>
                            <input type="date" class="form-control" id="date_filter">
                        </div>
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary me-2">
                                <i class="fas fa-search me-1"></i>Filtrar
                            </button>
                            <button type="button" class="btn btn-secondary" onclick="clearFilters()">
                                <i class="fas fa-times me-1"></i>Limpiar
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- ========================================
                                                                                                                TABLA DE EVENTOS
                                                                                                                ======================================== -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-list me-2"></i>
                        Eventos de Seguridad
                    </h6>
                    <div class="d-flex align-items-center">
                        <span class="badge bg-secondary me-3" id="total-events">Total: 0</span>
                        <div class="btn-group" role="group">
                            <button type="button" class="btn btn-outline-primary btn-sm" onclick="refreshEvents()">
                                <i class="fas fa-sync-alt"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover" id="eventsTable">
                            <thead class="table-light">
                                <tr>
                                    <th>IP</th>
                                    <th>Categoría</th>
                                    <th>Score</th>
                                    <th>Riesgo</th>
                                    <th>Fecha</th>
                                    <th>País</th>
                                    <th>Estado</th>
                                </tr>
                            </thead>
                            <tbody id="eventsTableBody">
                                <!-- Los eventos se cargarán dinámicamente -->
                            </tbody>
                        </table>
                    </div>

                    <!-- Paginación -->
                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <div class="text-muted">
                            Mostrando <span id="showing-start">0</span> a <span id="showing-end">0</span> de <span
                                id="total-count">0</span> eventos
                        </div>
                        <nav aria-label="Navegación de eventos">
                            <ul class="pagination pagination-sm mb-0" id="pagination">
                                <!-- La paginación se generará dinámicamente -->
                            </ul>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@stop

@section('js')
<script>
    let currentPage = 1;
        let eventsPerPage = 25;
        let allEvents = [];

        document.addEventListener('DOMContentLoaded', function() {
            loadEvents();
            setupEventListeners();
        });

        function setupEventListeners() {
            document.getElementById('filterForm').addEventListener('submit', function(e) {
                e.preventDefault();
                currentPage = 1;
                loadEvents();
            });
        }

        function loadEvents() {
            // Simular carga de eventos (en producción esto vendría del servidor)
            showLoadingState();

            // Aquí iría la llamada AJAX real
            setTimeout(() => {
                // Datos de ejemplo
                allEvents = generateSampleEvents();
                displayEvents();
                updatePagination();
            }, 1000);
        }

        function generateSampleEvents() {
            const events = [];
            const categories = ['suspicious_activity', 'brute_force', 'malware', 'ddos', 'phishing'];
            const countries = ['Estados Unidos', 'China', 'Rusia', 'Alemania', 'Francia', 'Japón', 'Reino Unido'];

            for (let i = 1; i <= 150; i++) {
                const threatScore = Math.floor(Math.random() * 100) + 1;
                const riskLevel = getRiskLevel(threatScore);

                events.push({
                    id: i,
                    ip_address: `192.168.${Math.floor(Math.random() * 255)}.${Math.floor(Math.random() * 255)}`,
                    category: categories[Math.floor(Math.random() * categories.length)],
                    threat_score: threatScore,
                    risk_level: riskLevel,
                    created_at: new Date(Date.now() - Math.random() * 30 * 24 * 60 * 60 * 1000),
                    country: countries[Math.floor(Math.random() * countries.length)],
                    status: Math.random() > 0.7 ? 'investigando' : 'nuevo'
                });
            }

            return events.sort((a, b) => b.threat_score - a.threat_score);
        }

        function getRiskLevel(score) {
            if (score >= 80) return 'critical';
            if (score >= 60) return 'high';
            if (score >= 40) return 'medium';
            // Solo retornar los 3 niveles principales
            return 'medium';
        }

        function getRiskBadgeColor(score) {
            if (score >= 80) return 'danger';
            if (score >= 60) return 'warning';
            if (score >= 40) return 'info';
            // Solo retornar los 3 niveles principales
            return 'info';
        }

        function displayEvents() {
            const startIndex = (currentPage - 1) * eventsPerPage;
            const endIndex = startIndex + eventsPerPage;
            const pageEvents = allEvents.slice(startIndex, endIndex);

            const tbody = document.getElementById('eventsTableBody');
            tbody.innerHTML = '';

            pageEvents.forEach(event => {
                const row = createEventRow(event);
                tbody.appendChild(row);
            });

            updateDisplayInfo(startIndex, endIndex);
        }

        function createEventRow(event) {
            const row = document.createElement('tr');
            row.className = `event-row ${event.risk_level}`;
            row.onclick = () => showEventDetails(event);

            row.innerHTML = `
                <td><strong>${event.ip_address}</strong></td>
                <td>${formatCategory(event.category)}</td>
                <td>
                    <div class="score-indicator">
                        <div class="score-fill ${event.risk_level}" style="width: ${event.threat_score}%"></div>
                    </div>
                    <small class="text-muted">${event.threat_score}</small>
                </td>
                <td>
                    <span class="badge bg-${getRiskBadgeColor(event.threat_score)}">
                        ${getRiskLevel(event.threat_score)}
                    </span>
                </td>
                <td>${formatDate(event.created_at)}</td>
                <td><i class="fas fa-globe me-1"></i>${event.country}</td>
                <td>
                    <span class="badge bg-${event.status === 'investigando' ? 'warning' : 'info'}">
                        ${event.status === 'investigando' ? 'Investigando' : 'Nuevo'}
                    </span>
                </td>
            `;

            return row;
        }

        function formatCategory(category) {
            const categories = {
                'suspicious_activity': 'Actividad Sospechosa',
                'brute_force': 'Fuerza Bruta',
                'malware': 'Malware',
                'ddos': 'DDoS',
                'phishing': 'Phishing'
            };
            return categories[category] || category;
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

        function updateDisplayInfo(start, end) {
            document.getElementById('showing-start').textContent = start + 1;
            document.getElementById('showing-end').textContent = Math.min(end, allEvents.length);
            document.getElementById('total-count').textContent = allEvents.length;
            document.getElementById('total-events').textContent = `Total: ${allEvents.length}`;
        }

        function updatePagination() {
            const totalPages = Math.ceil(allEvents.length / eventsPerPage);
            const pagination = document.getElementById('pagination');
            pagination.innerHTML = '';

            // Botón anterior
            const prevLi = document.createElement('li');
            prevLi.className = `page-item ${currentPage === 1 ? 'disabled' : ''}`;
            prevLi.innerHTML = `<a class="page-link" href="#" onclick="changePage(${currentPage - 1})">Anterior</a>`;
            pagination.appendChild(prevLi);

            // Páginas numeradas
            const startPage = Math.max(1, currentPage - 2);
            const endPage = Math.min(totalPages, currentPage + 2);

            for (let i = startPage; i <= endPage; i++) {
                const li = document.createElement('li');
                li.className = `page-item ${i === currentPage ? 'active' : ''}`;
                li.innerHTML = `<a class="page-link" href="#" onclick="changePage(${i})">${i}</a>`;
                pagination.appendChild(li);
            }

            // Botón siguiente
            const nextLi = document.createElement('li');
            nextLi.className = `page-item ${currentPage === totalPages ? 'disabled' : ''}`;
            nextLi.innerHTML = `<a class="page-link" href="#" onclick="changePage(${currentPage + 1})">Siguiente</a>`;
            pagination.appendChild(nextLi);
        }

        function changePage(page) {
            if (page < 1 || page > Math.ceil(allEvents.length / eventsPerPage)) return;
            currentPage = page;
            displayEvents();
            updatePagination();
        }

        function showLoadingState() {
            const tbody = document.getElementById('eventsTableBody');
            tbody.innerHTML = `
                <tr>
                    <td colspan="7" class="text-center py-4">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Cargando...</span>
                        </div>
                        <p class="mt-2 text-muted">Cargando eventos...</p>
                    </td>
                </tr>
            `;
        }

        function clearFilters() {
            document.getElementById('filterForm').reset();
            currentPage = 1;
            loadEvents();
        }

        function refreshEvents() {
            currentPage = 1;
            loadEvents();
        }

        function showEventDetails(event) {
            // Aquí se mostraría un modal con detalles del evento
            console.log('Mostrar detalles del evento:', event);
            alert(
                `Detalles del evento:\nIP: ${event.ip_address}\nCategoría: ${formatCategory(event.category)}\nScore: ${event.threat_score}\nRiesgo: ${event.risk_level}`
            );
        }
</script>

<!-- BEGIN: Application JavaScript -->
<script src="{{ asset('app-assets/js/security-dashboard.js') }}"></script>
<!-- END: Application JavaScript -->
@stop