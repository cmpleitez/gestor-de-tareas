@extends('dashboard')

@section('css')
    <!-- BEGIN: Security Dashboard CSS -->
    <!-- Archivo CSS externo comentado temporalmente para evitar conflictos -->
    <!-- <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/css/security-dashboard.css') }}"> -->
    <!-- END: Security Dashboard CSS -->

    <style>
        .log-viewer {
            max-height: none !important;
            overflow: visible !important;
            padding: 1rem;
            background-color: #f8f9fa;
            border-radius: 0.375rem;
            border: 1px solid #e9ecef;
        }

        .log-entry {
            padding: 0.5rem;
            margin-bottom: 0.25rem;
            border-radius: 0.25rem;
            background-color: white;
            border-left: 4px solid #6c757d;
            font-family: 'Courier New', monospace;
            font-size: 0.875rem;
            line-height: 1.4;
        }

        .log-entry.critical {
            border-left-color: #dc3545;
            background-color: #f8d7da;
        }

        .log-entry.error {
            border-left-color: #fd7e14;
            background-color: #fff3cd;
        }

        .log-timestamp {
            color: #6c757d;
            font-weight: 600;
        }

        .log-level {
            font-weight: bold;
            margin: 0 0.5rem;
        }

        .log-level.critical {
            color: #dc3545;
        }

        .log-level.error {
            color: #fd7e14;
        }

        .log-source {
            color: #495057;
            font-weight: 500;
            margin: 0 0.5rem;
        }

        .log-message {
            color: #212529;
            margin-left: 0.5rem;
        }
    </style>
@stop

@section('contenedor')
    <div class="container-fluid">
        <!-- ========================================
                                    HEADER DE LOGS DE SEGURIDAD
                                    ======================================== -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card bg-gradient-primary text-white">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col">
                                <h4 class="card-title text-white mb-1">
                                    <i class="fas fa-file-alt me-2"></i>
                                    Logs de Seguridad
                                </h4>
                                <p class="card-text text-white-50 mb-0">
                                    Gestión y análisis completo de logs del sistema de monitoreo de seguridad
                                </p>
                            </div>
                            <div class="col-auto">
                                <!-- Elementos de debug eliminados -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>



        <!-- ========================================
                                    FILTROS DE LOGS
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
                        <form id="logFilterForm" class="row g-3" method="GET" action="{{ route('security.logs') }}">

                            <div class="col-md-2">
                                <label for="log-source" class="form-label">Fuente</label>
                                <select class="form-select" id="log-source">
                                    <option value="all" selected>Todas</option>
                                    <option value="security">Security</option>
                                    <option value="firewall">Firewall</option>
                                    <option value="ids">IDS</option>
                                </select>
                            </div>



                            <div class="col-md-4">
                                <label for="log-search" class="form-label">Búsqueda de Texto</label>
                                <input type="text" class="form-control" id="log-search" placeholder="Buscar en logs...">
                            </div>
                            <div class="col-md-4">
                                <label for="log-ip" class="form-label">IP Específica</label>
                                <input type="text" class="form-control" id="log-ip" placeholder="192.168.1.1">
                            </div>
                            <div class="col-md-4 d-flex align-items-end">
                                <button type="button" class="btn btn-outline-secondary w-100" onclick="clearFilters()">
                                    <i class="fas fa-times me-2"></i>
                                    Limpiar Filtros
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- ========================================
                                    VISUALIZADOR DE LOGS
                                    ======================================== -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card shadow">
                    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="fas fa-file-alt me-2"></i>
                            Visualizador de Logs
                        </h6>
                        <button class="btn btn-success btn-sm" onclick="downloadLogs()">
                            <i class="fas fa-download me-1"></i>
                            Descargar Logs
                        </button>
                    </div>
                    <div class="card-body">
                        <div class="log-viewer" id="logViewer">
                            <!-- Los logs se cargarán dinámicamente -->
                        </div>

                        <!-- Paginación -->
                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <div class="text-muted">
                                Mostrando <span id="logs-showing-start">0</span> a <span id="logs-showing-end">0</span> de
                                <span id="logs-showing-total">0</span> logs
                            </div>
                            <nav aria-label="Navegación de logs">
                                <ul class="pagination mb-0" id="logs-pagination">
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

@section('js')
    <!-- Mostrar mensajes de error con Toastr -->
    @if (isset($error_message))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                if (typeof toastr !== 'undefined') {
                    toastr.error("{{ $error_message }}", 'Error de Logs de Seguridad', {
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
        let currentLogs = [];
        let filteredLogs = [];
        let currentLogPage = 1;
        let logsPerPage = 25;
        let totalLogs = 0;

        // Datos reales enviados desde el controlador
        @php
            $logsForJS = [];
            if (isset($logs) && is_array($logs)) {
                $logsForJS = $logs;
            }

            $paginationForJS = [];
            if (isset($pagination) && is_array($pagination)) {
                $paginationForJS = $pagination;
            }
        @endphp
        const serverLogs = {!! json_encode($logsForJS) !!};
        const serverPagination = {!! json_encode($paginationForJS) !!};

        // DEBUG: Mostrar datos recibidos del servidor


        // Datos del servidor cargados

        document.addEventListener('DOMContentLoaded', function() {
            loadLogs();
            setupEventListeners();
        });

        function setupEventListeners() {
            // Filtrado automático en tiempo real para búsqueda de texto
            document.getElementById('log-search').addEventListener('input', function() {
                currentLogPage = 1;
                filterLogs();
            });

            // Filtrado automático para IP
            document.getElementById('log-ip').addEventListener('input', function() {
                currentLogPage = 1;
                filterLogs();
            });



            // Filtrado automático para fuente
            document.getElementById('log-source').addEventListener('change', function() {
                currentLogPage = 1;
                filterLogs();
            });
        }

        function loadLogs() {
            showLoadingState();

            // Verificar que serverLogs sea un array válido y no esté vacío
            if (serverLogs && Array.isArray(serverLogs) && serverLogs.length > 0) {
                currentLogs = serverLogs;
                filteredLogs = [...currentLogs];
                totalLogs = serverPagination && serverPagination.total ? serverPagination.total : currentLogs.length;
                // Logs cargados correctamente
            } else {
                // serverLogs no es válido o está vacío
                currentLogs = [];
                filteredLogs = [];
                totalLogs = 0;
            }

            displayLogs(1);
        }



        function displayLogs(page = 1) {
            const viewer = document.getElementById('logViewer');
            if (!viewer) {

                return;
            }

            viewer.innerHTML = '';

            // Verificar si hay logs para mostrar
            if (!filteredLogs || filteredLogs.length === 0) {
                viewer.innerHTML = `
                    <div class="text-center py-4">
                        <i class="fas fa-info-circle fa-2x text-gray-400"></i>
                        <p class="mt-2 text-gray-500">No se encontraron logs para mostrar</p>
                        <small class="text-muted">Verifica que el controlador esté enviando datos correctamente</small>
                    </div>
                `;
                updateLogsPagination();
                return;
            }

            // Mostrar todos los logs filtrados
            filteredLogs.forEach((log, index) => {
                const logEntry = createLogEntry(log);
                viewer.appendChild(logEntry);
            });

            // Actualizar paginación
            currentLogPage = page;
            totalLogs = filteredLogs.length;
            updateLogsPagination();
            updateLogsShowingInfo();


        }

        function createLogEntry(log) {
            const entry = document.createElement('div');
            entry.className = `log-entry`;

            const timestamp = log.timestamp || formatTimestamp(new Date());

            entry.innerHTML = `
                <span class="log-timestamp">[${timestamp}]</span>
                <span class="log-source">[${log.source || 'system'}]</span>
                <span class="log-message">${log.message || 'Sin mensaje'}</span>
                ${log.ip ? `<small class="text-muted ms-2">IP: ${log.ip}</small>` : ''}
                ${log.user_id ? `<small class="text-muted ms-2">User: ${log.user_id}</small>` : ''}
            `;

            return entry;
        }

        function formatTimestamp(date) {
            return date.toLocaleString('es-ES');
        }

        function filterLogs() {
            const search = document.getElementById('log-search').value.trim().toLowerCase();
            const ip = document.getElementById('log-ip').value.trim().toLowerCase();
            const source = document.getElementById('log-source').value;

            filteredLogs = currentLogs.filter(log => {
                // Filtro por búsqueda de texto
                if (search && !log.message.toLowerCase().includes(search)) return false;

                // Filtro por IP
                if (ip && log.ip && !log.ip.toLowerCase().includes(ip)) return false;



                // Filtro por fuente
                if (source && source !== 'all' && log.source !== source) return false;

                return true;
            });

            currentLogPage = 1;
            displayLogs(1);
        }

        function clearFilters() {
            document.getElementById('logFilterForm').reset();
            document.getElementById('log-search').value = '';
            document.getElementById('log-ip').value = '';
            currentLogPage = 1;
            // Restaurar datos originales sin filtros
            filteredLogs = [...currentLogs];
            displayLogs(1);
        }

        function refreshLogs() {
            loadLogs();
        }

        function downloadLogs() {
            alert('Función de descarga temporalmente deshabilitada');
        }



        function updateLogsShowingInfo() {
            const start = (currentLogPage - 1) * logsPerPage + 1;
            const end = Math.min(currentLogPage * logsPerPage, totalLogs);

            document.getElementById('logs-showing-start').textContent = start;
            document.getElementById('logs-showing-end').textContent = end;
            document.getElementById('logs-showing-total').textContent = totalLogs;
        }

        function updateLogsPagination() {
            const totalPages = Math.ceil(totalLogs / logsPerPage);
            const pagination = document.getElementById('logs-pagination');

            pagination.innerHTML = '';

            // Botón anterior
            const prevLi = document.createElement('li');
            prevLi.className = `page-item ${currentLogPage === 1 ? 'disabled' : ''}`;
            prevLi.innerHTML = `<a class="page-link" href="#" onclick="changeLogPage(${currentLogPage - 1})">Anterior</a>`;
            pagination.appendChild(prevLi);

            // Páginas numeradas
            const startPage = Math.max(1, currentLogPage - 2);
            const endPage = Math.min(totalPages, currentLogPage + 2);

            for (let i = startPage; i <= endPage; i++) {
                const li = document.createElement('li');
                li.className = `page-item ${i === currentLogPage ? 'active' : ''}`;
                li.innerHTML = `<a class="page-link" href="#" onclick="changeLogPage(${i})">${i}</a>`;
                pagination.appendChild(li);
            }

            // Botón siguiente
            const nextLi = document.createElement('li');
            nextLi.className = `page-item ${currentLogPage === totalPages ? 'disabled' : ''}`;
            nextLi.innerHTML = `<a class="page-link" href="#" onclick="changeLogPage(${currentLogPage + 1})">Siguiente</a>`;
            pagination.appendChild(nextLi);
        }

        function changeLogPage(page) {
            if (page < 1 || page > Math.ceil(totalLogs / logsPerPage)) {
                return;
            }
            displayLogs(page);
        }

        function showLoadingState() {
            const viewer = document.getElementById('logViewer');
            viewer.innerHTML = `
                <div class="text-center py-4">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Cargando...</span>
                    </div>
                    <p class="mt-2 text-muted">Cargando logs del sistema...</p>
                </div>
            `;
        }

        function debugLogs() {
            // Función de debug deshabilitada para producción
        }



        function getAppliedFilters() {
            const filters = [];
            const source = document.getElementById('log-source').value;
            const search = document.getElementById('log-search').value.trim();
            const ip = document.getElementById('log-ip').value.trim();

            if (source && source !== 'all') filters.push(`Fuente: ${source}`);
            if (search) filters.push(`Búsqueda: "${search}"`);
            if (ip) filters.push(`IP: ${ip}`);

            return filters;
        }
    </script>

    <!-- BEGIN: Application JavaScript -->
    <!-- Archivo JavaScript externo comentado temporalmente para evitar conflictos -->
    <!-- <script src="{{ asset('app-assets/js/security-dashboard.js') }}"></script> -->
    <!-- END: Application JavaScript -->
@stop
