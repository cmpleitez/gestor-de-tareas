@extends('dashboard')

@section('css')
<!-- BEGIN: Security Dashboard CSS -->
<link rel="stylesheet" type="text/css" href="{{ asset('app-assets/css/security-dashboard.css') }}">
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
<div class="container-fluid" data-risk-distribution="{{ trim(json_encode($risk_distribution ?? [])) }}"
    data-threats-by-country="{{ trim(json_encode($threats_by_country ?? [])) }}">
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
                            <div class="d-flex align-items-center">
                                <span class="badge bg-light text-dark me-2">REGISTRANDO</span>
                                <div class="spinner-border spinner-border-sm text-white" role="status">
                                    <span class="visually-hidden">Cargando...</span>
                                </div>
                            </div>
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
                    <form id="logFilterForm" class="row g-3">
                        <div class="col-md-2">
                            <label for="log-level" class="form-label">Nivel de Log</label>
                            <select class="form-select" id="log-level">
                                <option value="all" selected>Todos</option>
                                <option value="critical">Critical</option>
                                <option value="error">Error</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label for="log-source" class="form-label">Fuente</label>
                            <select class="form-select" id="log-source">
                                <option value="all" selected>Todas</option>
                                <option value="security">Security</option>
                                <option value="firewall">Firewall</option>
                                <option value="ids">IDS</option>
                                <option value="monitoring">Monitoring</option>
                                <option value="database">Database</option>
                                <option value="network">Network</option>
                                <option value="backup">Backup</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label for="log-date" class="form-label">Fecha</label>
                            <input type="date" class="form-control" id="log-date">
                        </div>
                        <div class="col-md-2">
                            <label for="log-time-start" class="form-label">Hora Inicio</label>
                            <input type="time" class="form-control" id="log-time-start">
                        </div>
                        <div class="col-md-2">
                            <label for="log-time-end" class="form-label">Hora Fin</label>
                            <input type="time" class="form-control" id="log-time-end">
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-search me-2"></i>
                                Filtrar
                            </button>
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
<script>
    let currentLogs = [];
    let filteredLogs = [];
    let currentLogPage = 1;
    let logsPerPage = 25;
    let totalLogs = 0;

    // Datos reales enviados desde el controlador
    const serverLogs = @json($logs ?? []);
    const serverPagination = @json($pagination ?? []);
    
    // DEBUG TEMPORAL - ELIMINAR DESPUÉS
    console.log('Logs recibidos del servidor:', {
        logs: serverLogs,
        pagination: serverPagination,
        logsCount: serverLogs ? serverLogs.length : 0,
        sampleLog: serverLogs && serverLogs.length > 0 ? serverLogs[0] : null
    });

    document.addEventListener('DOMContentLoaded', function() {
        loadLogs();
        setupEventListeners();
    });

    function setupEventListeners() {
        document.getElementById('logFilterForm').addEventListener('submit', function(e) {
            e.preventDefault();
            filterLogs();
        });

        // Filtros en tiempo real
        document.getElementById('log-search').addEventListener('input', filterLogs);
        document.getElementById('log-level').addEventListener('change', filterLogs);
        document.getElementById('log-source').addEventListener('change', filterLogs);
    }

    function loadLogs() {
        console.log('loadLogs() ejecutándose...');
        console.log('serverLogs:', serverLogs);
        console.log('serverPagination:', serverPagination);
        
        showLoadingState();

        // Usar los datos reales del servidor
        if (serverLogs && serverLogs.length > 0) {
            console.log('Usando logs del servidor:', serverLogs.length);
            currentLogs = serverLogs;
            filteredLogs = [...currentLogs];
            totalLogs = serverPagination.total || currentLogs.length;
            console.log('Logs cargados:', currentLogs.length);
        } else {
            console.log('No hay logs del servidor, usando array vacío');
            // Si no hay datos del servidor, mostrar mensaje
            currentLogs = [];
            filteredLogs = [];
            totalLogs = 0;
        }

        console.log('Llamando a displayLogs(1)...');
        displayLogs(1);
    }



        function displayLogs(page = 1) {
            console.log('displayLogs() ejecutándose con página:', page);
            console.log('filteredLogs:', filteredLogs);
            console.log('filteredLogs.length:', filteredLogs.length);
            
            const viewer = document.getElementById('logViewer');
            if (!viewer) {
                console.error('Elemento logViewer no encontrado');
                return;
            }
            
            console.log('Elemento logViewer encontrado, limpiando contenido...');
            viewer.innerHTML = '';

            if (filteredLogs.length === 0) {
                console.log('No hay logs filtrados, mostrando mensaje de no datos');
                viewer.innerHTML = `
                    <div class="text-center py-4">
                        <i class="fas fa-info-circle fa-2x text-gray-400"></i>
                        <p class="mt-2 text-gray-500">No se encontraron logs con los filtros aplicados</p>
                    </div>
                `;
                updateLogsPagination();
                return;
            }

            console.log('Mostrando logs filtrados...');
            // Mostrar todos los logs filtrados
            filteredLogs.forEach((log, index) => {
                console.log(`Creando entrada para log ${index}:`, log);
                const logEntry = createLogEntry(log);
                viewer.appendChild(logEntry);
            });

            // Actualizar paginación
            currentLogPage = page;
            totalLogs = filteredLogs.length;
            console.log('Llamando a updateLogsPagination()...');
            updateLogsPagination();
            console.log('Llamando a updateLogsShowingInfo()...');
            updateLogsShowingInfo();
        }

        function createLogEntry(log) {
            const entry = document.createElement('div');
            entry.className = `log-entry ${log.level}`;

            const timestamp = log.timestamp || formatTimestamp(new Date());
            const levelClass = `log-level ${log.level}`;
            const levelText = (log.level || 'info').toUpperCase();

            entry.innerHTML = `
                <span class="log-timestamp">[${timestamp}]</span>
                <span class="${levelClass}">[${levelText}]</span>
                <span class="log-source">[${log.source || 'system'}]</span>
                <span class="log-message">${log.message || 'Sin mensaje'}</span>
                ${log.ip ? `<small class="text-muted ms-2">IP: ${log.ip}</small>` : ''}
                ${log.user_id ? `<small class="text-muted ms-2">User: ${log.user_id}</small>` : ''}
            `;

            return entry;
        }

        function formatTimestamp(date) {
            return new Intl.DateTimeFormat('es-ES', {
                day: '2-digit',
                month: '2-digit',
                year: 'numeric',
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit'
            }).format(date);
        }

        function filterLogs() {
            const level = document.getElementById('log-level').value;
            const source = document.getElementById('log-source').value;
            const search = document.getElementById('log-search').value.toLowerCase();
            const ip = document.getElementById('log-ip').value.toLowerCase();

            filteredLogs = currentLogs.filter(log => {
                // Filtro por nivel
                if (level !== 'all' && log.level !== level) return false;

                // Filtro por fuente
                if (source !== 'all' && log.source !== source) return false;

                // Filtro por búsqueda de texto
                if (search && !log.message.toLowerCase().includes(search)) return false;

                // Filtro por IP
                if (ip && !log.ip.toLowerCase().includes(ip)) return false;

                return true;
            });

            // Resetear a la primera página al filtrar
            currentLogPage = 1;
            displayLogs(1);
        }

        function clearFilters() {
            document.getElementById('logFilterForm').reset();
            document.getElementById('log-search').value = '';
            document.getElementById('log-ip').value = '';

            filteredLogs = [...currentLogs];
            // Resetear a la primera página al limpiar filtros
            currentLogPage = 1;
            displayLogs(1);
        }

        function refreshLogs() {
            loadLogs();
        }

        function downloadLogs() {
            if (filteredLogs.length === 0) {
                alert('No hay logs para descargar');
                return;
            }

            // Crear contenido del archivo
            let content = 'Logs de Seguridad\n';
            content += '==================\n\n';

            filteredLogs.forEach(log => {
                const timestamp = formatTimestamp(log.timestamp);
                content += `[${timestamp}] [${log.level.toUpperCase()}] [${log.source}] ${log.message}`;
                if (log.ip) content += ` IP: ${log.ip}`;
                if (log.user_id) content += ` User: ${log.user_id}`;
                content += '\n';
            });

            // Crear y descargar archivo
            const blob = new Blob([content], {
                type: 'text/plain'
            });
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = `security_logs_${new Date().toISOString().split('T')[0]}.txt`;
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
            window.URL.revokeObjectURL(url);
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
</script>

<!-- BEGIN: Application JavaScript -->
<script src="{{ asset('app-assets/js/security-dashboard.js') }}"></script>
<!-- END: Application JavaScript -->
@stop