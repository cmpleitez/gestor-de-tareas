@extends('dashboard')

@section('css')
<!-- BEGIN: Security Dashboard CSS -->
<link rel="stylesheet" type="text/css" href="{{ asset('app-assets/css/security-dashboard.css') }}">
<!-- END: Security Dashboard CSS -->
@stop

@section('contenedor')
<div class="container-fluid" data-risk-distribution="{{ trim(json_encode($risk_distribution ?? [])) }}"
    data-threats-by-country="{{ trim(json_encode($threats_by_country ?? [])) }}">
    <!-- ========================================
                                                                                                                                        HEADER DE LOGS DE SEGURIDAD
                                                                                                                                        ======================================== -->
    <x-security.dashboard-header title="Logs de Seguridad"
        subtitle="Gestión y análisis completo de logs del sistema de monitoreo de seguridad" status="REGISTRANDO"
        status_color="dark" :show_pulse="false" />

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
                                <option value="middleware">Middleware</option>
                                <option value="service">Servicios</option>
                                <option value="database">Base de Datos</option>
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
                    <div class="d-flex align-items-center">
                        <span class="badge bg-secondary me-3" id="log-count">Logs del sistema</span>
                        <div class="btn-group" role="group">
                            <button type="button" class="btn btn-outline-primary btn-sm" onclick="refreshLogs()">
                                <i class="fas fa-sync-alt"></i>
                            </button>
                            <button type="button" class="btn btn-outline-success btn-sm" onclick="downloadLogs()">
                                <i class="fas fa-download"></i>
                            </button>
                        </div>
                    </div>
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
            showLoadingState();

            // Simular carga de logs (en producción esto vendría del servidor)
            setTimeout(() => {
                currentLogs = generateSampleLogs();
                filteredLogs = [...currentLogs];
                displayLogs();
                updateLogCount();
            }, 1000);
        }

        function generateSampleLogs() {
            const logs = [];
            const levels = ['critical', 'error']; // Solo 2 niveles
            const sources = ['middleware', 'service', 'database'];
            const messages = [
                'Usuario autenticado exitosamente',
                'Intento de acceso no autorizado detectado',
                'Solicitud procesada correctamente',
                'Error en la validación de datos',
                'Amenaza de seguridad bloqueada',
                'Conexión a base de datos establecida',
                'Archivo de configuración cargado',
                'Sesión de usuario iniciada',
                'Logout de usuario completado',
                'Verificación de permisos realizada'
            ];

            for (let i = 1; i <= 100; i++) {
                const level = levels[Math.floor(Math.random() * levels.length)];
                const source = sources[Math.floor(Math.random() * sources.length)];
                const message = messages[Math.floor(Math.random() * messages.length)];
                const timestamp = new Date(Date.now() - Math.random() * 24 * 60 * 60 * 1000);
                const ip = `192.168.${Math.floor(Math.random() * 255)}.${Math.floor(Math.random() * 255)}`;

                logs.push({
                    id: i,
                    timestamp: timestamp,
                    level: level,
                    source: source,
                    message: message,
                    ip: ip,
                    user_id: Math.random() > 0.5 ? `user_${Math.floor(Math.random() * 1000)}` : null
                });
            }

            return logs.sort((a, b) => b.timestamp - a.timestamp);
        }

        function displayLogs(page = 1) {
            const viewer = document.getElementById('logViewer');
            viewer.innerHTML = '';

            if (filteredLogs.length === 0) {
                viewer.innerHTML = `
                    <div class="text-center py-4">
                        <i class="fas fa-info-circle fa-2x text-gray-400"></i>
                        <p class="mt-2 text-gray-500">No se encontraron logs con los filtros aplicados</p>
                    </div>
                `;
                updateLogsPagination();
                return;
            }

            // Calcular logs para la página actual
            const startIndex = (page - 1) * logsPerPage;
            const endIndex = startIndex + logsPerPage;
            const pageLogs = filteredLogs.slice(startIndex, endIndex);

            // Mostrar logs de la página actual
            pageLogs.forEach(log => {
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
            entry.className = `log-entry ${log.level}`;

            const timestamp = formatTimestamp(log.timestamp);
            const levelClass = `log-level ${log.level}`;
            const levelText = log.level.toUpperCase();

            entry.innerHTML = `
                <span class="log-timestamp">[${timestamp}]</span>
                <span class="${levelClass}">[${levelText}]</span>
                <span class="log-source">[${log.source}]</span>
                <span class="log-message">${log.message}</span>
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
            updateLogCount();
        }

        function clearFilters() {
            document.getElementById('logFilterForm').reset();
            document.getElementById('log-search').value = '';
            document.getElementById('log-ip').value = '';

            filteredLogs = [...currentLogs];
            // Resetear a la primera página al limpiar filtros
            currentLogPage = 1;
            displayLogs(1);
            updateLogCount();
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

        function updateLogCount() {
            const countElement = document.getElementById('log-count');
            countElement.textContent = `Mostrando ${filteredLogs.length} de ${currentLogs.length} logs`;
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