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

        /* Indicador de estado: mantener punto en amarillo y pulso (anillo) en rojo */
        .security-status-indicator .pulse-dot {
            animation: pulse-red 2s infinite;
        }

        @keyframes pulse-red {
            0% {
                box-shadow: 0 0 0 0 rgba(220, 53, 69, 0.7);
                /* rojo bootstrap */
            }

            70% {
                box-shadow: 0 0 0 10px rgba(220, 53, 69, 0);
            }

            100% {
                box-shadow: 0 0 0 0 rgba(220, 53, 69, 0);
            }
        }
    </style>

    <!-- BEGIN: Security Dashboard CSS -->
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/css/security-dashboard.css') }}">
    <!-- END: Security Dashboard CSS -->
@stop

@section('contenedor')
    <div class="container-fluid" data-risk-distribution="{{ trim(json_encode($risk_distribution ?? [])) }}"
        data-threats-by-country="{{ trim(json_encode($threats_by_country ?? [])) }}">

        <div class="row">
            <div class="col-12">
                <div class="card mb-0">
                    <div class="card-body p-0">
                        <div class="row align-items-center justify-content-center">
                            <div class="col-md-11">
                                <h6 class="m-0 align-items-center" style="display: flex; align-items: center;">
                                    <i class="bx bxs-check-shield me-3 text-dark"
                                        style="padding-left: 0rem !important; padding-right: 0.2rem !important; font-size: 2rem;"></i>
                                    EVENTOS DE SEGURIDAD
                                </h6>
                            </div>
                            <div class="col-md-1 text-center">
                                <div class="d-flex justify-content-end align-items-center">
                                    <div class="security-status-indicator">
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
                                                                                                                                                                                                                                                                                                    ======================================== -->
        <div class="row mt-1">
            <div class="col-12">
                <div class="card" style="margin-bottom: 0rem;">
                    <div class="card-header">
                        <span class="card-title" style="font-size: 0.875rem; font-weight: 500;">Filtros de Búsqueda</span>
                    </div>
                    <div class="card-body">
                        <form id="filterForm" class="row g-3">
                            <div class="col-md-3">
                                <label for="ip_filter" class="form-label">Dirección IP</label>
                                <input type="text" class="form-control" id="ip_filter" placeholder="192.168.1.1">
                            </div>
                            <div class="col-md-3">
                                <label for="category_filter" class="form-label">Categoría</label>
                                <input type="text" class="form-control" id="category_filter" list="category_options"
                                    placeholder="Inyección SQL, XSS, Comandos...">
                                <datalist id="category_options">
                                    <option value="Inyección SQL"></option>
                                    <option value="Ataque XSS"></option>
                                    <option value="Travesía de Ruta"></option>
                                    <option value="Inyección de Comandos"></option>
                                    <option value="Actividad Sospechosa"></option>
                                </datalist>
                            </div>
                            <div class="col-md-3">
                                <label for="risk_filter" class="form-label">Nivel de Riesgo</label>
                                <input type="text" class="form-control" id="risk_filter" list="risk_options"
                                    placeholder="Crítico, Alto, Medio">
                                <datalist id="risk_options">
                                    <option value="Crítico"></option>
                                    <option value="Alto"></option>
                                    <option value="Medio"></option>
                                </datalist>
                            </div>
                            <div class="col-md-3">
                                <label for="date_filter" class="form-label">Fecha</label>
                                <input type="date" class="form-control" id="date_filter" value="{{ date('Y-m-d') }}">
                            </div>
                            <div class="col-12 d-flex justify-content-end mt-1">
                                <button type="button" class="btn btn-outline-secondary" onclick="clearFilters()">
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
        <div class="row mt-1">
            <div class="col-12">
                <div class="card" style="margin-bottom: 0rem;">
                    <div class="card-header">
                        <span class="card-title" style="font-size: 0.875rem; font-weight: 500;">Eventos de Seguridad</span>
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
                                        <th>Resultado</th>
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
    <!-- Mostrar mensajes de error con Toastr -->
    @if (isset($error_message))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                if (typeof toastr !== 'undefined') {
                    toastr.error("{{ $error_message }}", 'Error de Eventos', {
                        timeOut: 8000,
                        extendedTimeOut: 2000,
                        closeButton: true,
                        progressBar: true
                    });
                }
            });
        </script>
    @endif

    <script>
        let currentPage = 1;
        let eventsPerPage = 25;
        let allEvents = [];
        let categoryDropdownArmed = false;
        let riskDropdownArmed = false;

        // Datos reales enviados desde el controlador
        const serverEvents = @json($events ?? []);

        document.addEventListener('DOMContentLoaded', function() {
            // La fecha ya está establecida en el HTML por Laravel
            // No sobrescribir con JavaScript para evitar problemas de zona horaria

            loadEvents();
            setupEventListeners();

            // Limpiar automáticamente al abrir el desplegable (clic en triángulo) para categoría y riesgo
            attachClearOnDropdownClick('category_filter');
            attachClearOnDropdownClick('risk_filter');
        });

        function setupEventListeners() {
            // Filtrado en tiempo real para IP
            document.getElementById('ip_filter').addEventListener('input', function() {
                currentPage = 1;
                loadEvents();
            });

            // Categoría: aplicar filtro sólo cuando se seleccione un item del datalist
            const categoryInputEl = document.getElementById('category_filter');
            const handleCategoryCommit = function() {
                const committed = categoryDropdownArmed || isValueInDatalist(categoryInputEl);
                if (!committed) return;
                categoryDropdownArmed = false; // consumir el armado
                currentPage = 1;
                loadEvents();
            };
            categoryInputEl.addEventListener('input', handleCategoryCommit);
            categoryInputEl.addEventListener('change', handleCategoryCommit);

            // Riesgo: aplicar filtro sólo cuando se seleccione un item del datalist
            const riskInputEl = document.getElementById('risk_filter');
            const handleRiskCommit = function() {
                const committed = riskDropdownArmed || isValueInDatalist(riskInputEl);
                if (!committed) return;
                riskDropdownArmed = false; // consumir el armado
                currentPage = 1;
                loadEvents();
            };
            riskInputEl.addEventListener('input', handleRiskCommit);
            riskInputEl.addEventListener('change', handleRiskCommit);

            document.getElementById('date_filter').addEventListener('change', function() {
                currentPage = 1;
                loadEvents();
            });
        }

        // Detectar clic en el área del triángulo del input con datalist y limpiar el control
        function attachClearOnDropdownClick(inputId) {
            const input = document.getElementById(inputId);
            if (!input) return;

            input.addEventListener('mousedown', function(e) {
                const rect = input.getBoundingClientRect();
                const clickFromRight = rect.right - e.clientX;
                // Umbral aproximado del área del icono (flecha) ~ 24px
                if (clickFromRight <= 24) {
                    input.value = '';
                    if (inputId === 'category_filter') {
                        // armar para que el siguiente input dispare filtrado (selección de item)
                        categoryDropdownArmed = true;
                    } else if (inputId === 'risk_filter') {
                        // armar para que el siguiente input dispare filtrado (selección de item)
                        riskDropdownArmed = true;
                    } else {
                        currentPage = 1;
                        loadEvents();
                    }
                }
            });
        }

        // Verifica si el valor actual del input coincide exactamente con alguna opción del datalist
        function isValueInDatalist(inputEl) {
            const listId = inputEl.getAttribute('list');
            if (!listId) return false;
            const dataList = document.getElementById(listId);
            if (!dataList) return false;
            const val = inputEl.value.trim().toLowerCase();
            if (!val) return false;
            for (let i = 0; i < dataList.options.length; i++) {
                const optVal = (dataList.options[i].value || '').trim().toLowerCase();
                if (optVal === val) return true;
            }
            return false;
        }

        function loadEvents() {
            // Obtener filtros aplicados
            const ipFilter = document.getElementById('ip_filter').value.trim().toLowerCase();
            // Normalizar categoría desde texto visible a clave interna
            const categoryInput = document.getElementById('category_filter').value.trim().toLowerCase();
            const categoryMap = {
                'inyección sql': 'sql_injection',
                'inyeccion sql': 'sql_injection',
                'ataque xss': 'xss_attack',
                'travesía de ruta': 'path_traversal',
                'travesia de ruta': 'path_traversal',
                'inyección de comandos': 'command_injection',
                'inyeccion de comandos': 'command_injection',
                'actividad sospechosa': 'suspicious_activity',
                // Permitir también claves directas
                'sql_injection': 'sql_injection',
                'xss_attack': 'xss_attack',
                'path_traversal': 'path_traversal',
                'command_injection': 'command_injection',
                'suspicious_activity': 'suspicious_activity'
            };
            const categoryFilter = categoryMap[categoryInput] || '';
            const riskInput = document.getElementById('risk_filter').value.trim().toLowerCase();
            const riskMap = {
                'crítico': 'critical',
                'critico': 'critical',
                'alto': 'high',
                'medio': 'medium',
                // claves directas
                'critical': 'critical',
                'high': 'high',
                'medium': 'medium'
            };
            const riskFilter = riskMap[riskInput] || '';
            const dateFilter = document.getElementById('date_filter').value;

            // Usar los datos reales del servidor
            if (Array.isArray(serverEvents) && serverEvents.length > 0) {
                // Aplicar filtros
                allEvents = serverEvents.filter(event => {
                    // Filtro por IP
                    const eventIp = (event && event.ip_address ? String(event.ip_address) : '').toLowerCase();
                    if (ipFilter && !eventIp.includes(ipFilter)) {
                        return false;
                    }

                    // Filtro por categoría
                    const eventCategory = (event && event.category ? String(event.category) : '');
                    if (categoryFilter && eventCategory !== categoryFilter) {
                        return false;
                    }

                    // Filtro por nivel de riesgo
                    const eventRisk = (event && event.risk_level ? String(event.risk_level) : '');
                    if (riskFilter && eventRisk !== riskFilter) {
                        return false;
                    }

                    // Filtro por fecha (aplicar SOLO si no hay otros filtros activos)
                    const hasActiveNonDateFilter = !!(ipFilter || categoryFilter || riskFilter);
                    if (dateFilter && !hasActiveNonDateFilter) {
                        const d = new Date(event.created_at);
                        const y = d.getFullYear();
                        const m = String(d.getMonth() + 1).padStart(2, '0');
                        const da = String(d.getDate()).padStart(2, '0');
                        const eventDateStr = `${y}-${m}-${da}`;
                        if (eventDateStr !== dateFilter) {
                            return false;
                        }
                    }

                    return true;
                });
            } else {
                // Si no hay datos del servidor, mostrar mensaje
                allEvents = [];
                showNoDataMessage();
                return;
            }

            // Solo mostrar loading si no hay filtros activos
            if (!ipFilter && !categoryFilter && !riskFilter && !dateFilter) {
                showLoadingState();
            }

            displayEvents();
            updatePagination();

            // Actualizar indicadores de conteo
            document.getElementById('total-count').textContent = allEvents.length;
        }

        function displayEvents() {
            const startIndex = (currentPage - 1) * eventsPerPage;
            const endIndex = startIndex + eventsPerPage;
            const pageEvents = allEvents.slice(startIndex, endIndex);

            const tbody = document.getElementById('eventsTableBody');
            tbody.innerHTML = '';

            if (pageEvents.length === 0) {
                showNoDataMessage();
                return;
            }

            pageEvents.forEach(event => {
                const row = createEventRow(event);
                tbody.appendChild(row);
            });

            updateDisplayInfo(startIndex, endIndex);
        }

        function createEventRow(event) {
            const row = document.createElement('tr');
            row.className = `event-row ${event.risk_level}`;

            // Normalización de resultado: cubrir estados legados
            const rawOutcome = (event.outcome || event.status || '').toString().toLowerCase();
            const outcomeMap = {
                'blocked': 'blocked',
                'exploited': 'exploited',
                'attempted': 'attempted',
                // Estados legados mapeados
                'monitored': 'attempted',
                'open': 'attempted',
                'investigando': 'attempted',
                'nuevo': 'attempted'
            };
            const outcome = outcomeMap[rawOutcome] || 'unknown';

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
                    ${formatRisk(event.risk_level)}
                </span>
            </td>
            <td>${formatDate(event.created_at)}</td>
            <td><i class="fas fa-globe me-1"></i>${event.country}</td>
            <td>
                <div class="d-flex justify-content-end">
                <span class="badge bg-${
                    outcome === 'blocked' ? 'success' :
                    (outcome === 'exploited' ? 'danger' :
                    (outcome === 'attempted' ? 'warning' : 'secondary'))
                }">
                    ${
                        outcome === 'blocked' ? 'Bloqueado' :
                        (outcome === 'exploited' ? 'Explotado' :
                        (outcome === 'attempted' ? 'Intento' : 'Desconocido'))
                    }
                </span>
                </div>
            </td>
        `;

            return row;
        }

        function formatCategory(category) {
            const categories = {
                'sql_injection': 'Inyección SQL',
                'xss_attack': 'Ataque XSS',
                'path_traversal': 'Travesía de Ruta',
                'command_injection': 'Inyección de Comandos',
                'brute_force': 'Fuerza Bruta',
                'suspicious_activity': 'Actividad Sospechosa',
                'rate_limit_exceeded': 'Límite Excedido',
                'malware_detected': 'Malware Detectado',
                'phishing_attempt': 'Intento de Phishing',
                'ddos_attack': 'Ataque DDoS',
                'unknown': 'Desconocido'
            };
            return categories[category] || category;
        }

        function formatRisk(risk) {
            const key = (risk || '').toString().toLowerCase();
            const map = {
                'critical': 'Crítico',
                'high': 'Alto',
                'medium': 'Medio'
            };
            return map[key] || (risk ?? 'Desconocido');
        }

        function formatDate(dateString) {
            const date = new Date(dateString);
            return new Intl.DateTimeFormat('es-ES', {
                day: '2-digit',
                month: '2-digit',
                year: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            }).format(date);
        }

        function getRiskBadgeColor(score) {
            if (score >= 80) return 'danger';
            if (score >= 60) return 'warning';
            if (score >= 40) return 'info';
            return 'info';
        }

        function updateDisplayInfo(start, end) {
            const elStart = document.getElementById('showing-start');
            const elEnd = document.getElementById('showing-end');
            const elTotal = document.getElementById('total-count');
            const elTotalEvents = document.getElementById('total-events');

            if (elStart) elStart.textContent = start + 1;
            if (elEnd) elEnd.textContent = Math.min(end, allEvents.length);
            if (elTotal) elTotal.textContent = allEvents.length;
            if (elTotalEvents) elTotalEvents.textContent = `Total: ${allEvents.length}`;
        }

        function updatePagination() {
            const totalPages = Math.ceil(allEvents.length / eventsPerPage);
            const pagination = document.getElementById('pagination');
            pagination.innerHTML = '';

            if (totalPages <= 1) {
                return;
            }

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

        function showNoDataMessage() {
            const tbody = document.getElementById('eventsTableBody');
            tbody.innerHTML = `
            <tr>
                <td colspan="7" class="text-center py-4">
                    <i class="fas fa-info-circle text-muted fa-2x mb-3"></i>
                    <p class="text-muted">No hay eventos de seguridad disponibles</p>
                    <small class="text-muted">Los eventos se cargarán desde la base de datos</small>
                </td>
            </tr>
        `;

            // Actualizar contadores
            document.getElementById('showing-start').textContent = '0';
            document.getElementById('showing-end').textContent = '0';
            document.getElementById('total-count').textContent = '0';
            document.getElementById('total-events').textContent = 'Total: 0';
        }

        function clearFilters() {
            // Limpiar todos los filtros excepto la fecha
            document.getElementById('ip_filter').value = '';
            document.getElementById('category_filter').value = '';
            document.getElementById('risk_filter').value = '';
            // Mantener la fecha actual
            document.getElementById('date_filter').value = new Date().toISOString().split('T')[0];

            currentPage = 1;
            // Restaurar datos originales sin filtros
            allEvents = [...serverEvents];
            displayEvents();
            updatePagination();
        }

        function refreshEvents() {
            currentPage = 1;
            // Recargar la página para obtener datos frescos del servidor
            window.location.reload();
        }
    </script>

    <!-- BEGIN: Application JavaScript -->
    <script src="{{ asset('app-assets/js/security-dashboard.js') }}"></script>
    <!-- END: Application JavaScript -->
@stop
