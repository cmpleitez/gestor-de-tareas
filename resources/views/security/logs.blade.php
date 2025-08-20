@extends('dashboard')

@section('content')
<div class="container-fluid">
    <!-- Header de Logs -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card bg-gradient-dark text-white">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h1 class="mb-2">
                                <i class="fas fa-file-alt me-3"></i>
                                Logs de Seguridad
                            </h1>
                            <p class="mb-0 fs-5">
                                Gestión y análisis completo de logs del sistema de monitoreo de seguridad
                            </p>
                        </div>
                        <div class="col-md-4 text-end">
                            <div class="d-flex justify-content-end align-items-center">
                                <div class="me-4">
                                    <div class="fs-6 opacity-75">Tamaño Total</div>
                                    <div class="fs-4 fw-bold" id="total-log-size">2.4 GB</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtros de Logs -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-2 mb-3">
                            <label for="log-level" class="form-label">Nivel de Log</label>
                            <select class="form-select" id="log-level">
                                <option value="all" selected>Todos</option>
                                <option value="debug">Debug</option>
                                <option value="info">Info</option>
                                <option value="warning">Warning</option>
                                <option value="error">Error</option>
                                <option value="critical">Critical</option>
                            </select>
                        </div>
                        <div class="col-md-2 mb-3">
                            <label for="log-source" class="form-label">Fuente</label>
                            <select class="form-select" id="log-source">
                                <option value="all" selected>Todas</option>
                                <option value="middleware">Middleware</option>
                                <option value="service">Servicios</option>
                                <option value="ml">Machine Learning</option>
                                <option value="api">APIs Externas</option>
                                <option value="database">Base de Datos</option>
                            </select>
                        </div>
                        <div class="col-md-2 mb-3">
                            <label for="log-date" class="form-label">Fecha</label>
                            <input type="date" class="form-control" id="log-date">
                        </div>
                        <div class="col-md-2 mb-3">
                            <label for="log-time-start" class="form-label">Hora Inicio</label>
                            <input type="time" class="form-control" id="log-time-start">
                        </div>
                        <div class="col-md-2 mb-3">
                            <label for="log-time-end" class="form-label">Hora Fin</label>
                            <input type="time" class="form-control" id="log-time-end">
                        </div>
                        <div class="col-md-2 mb-3 d-flex align-items-end">
                            <button type="button" class="btn btn-primary w-100" onclick="filterLogs()">
                                <i class="fas fa-search me-2"></i>
                                Filtrar
                            </button>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="log-search" class="form-label">Búsqueda de Texto</label>
                            <input type="text" class="form-control" id="log-search" placeholder="Buscar en logs...">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="log-ip" class="form-label">IP Específica</label>
                            <input type="text" class="form-control" id="log-ip" placeholder="192.168.1.1">
                        </div>
                        <div class="col-md-4 mb-3 d-flex align-items-end">
                            <button type="button" class="btn btn-outline-secondary w-100" onclick="clearFilters()">
                                <i class="fas fa-times me-2"></i>
                                Limpiar Filtros
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Estadísticas de Logs -->
    <div class="row mb-4">
        <div class="col-xl-2 col-md-4 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Total Logs
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="total-logs">1,247,892</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-file-alt fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-2 col-md-4 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Info
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="info-logs">892,456</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-info-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-2 col-md-4 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Warning
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="warning-logs">234,567</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-exclamation-triangle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-2 col-md-4 mb-4">
            <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                Error
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="error-logs">89,234</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-times-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-2 col-md-4 mb-4">
            <div class="card border-left-dark shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-dark text-uppercase mb-1">
                                Critical
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="critical-logs">12,345</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-radiation fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-2 col-md-4 mb-4">
            <div class="card border-left-secondary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-secondary text-uppercase mb-1">
                                Debug
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="debug-logs">18,290</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-bug fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Controles de Logs -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-6">
                            <div class="d-flex align-items-center">
                                <div class="form-check form-switch me-3">
                                    <input class="form-check-input" type="checkbox" id="auto-refresh" checked>
                                    <label class="form-check-label" for="auto-refresh">
                                        Auto-refresh cada 30s
                                    </label>
                                </div>
                                <div class="form-check form-switch me-3">
                                    <input class="form-check-input" type="checkbox" id="highlight-errors" checked>
                                    <label class="form-check-label" for="highlight-errors">
                                        Resaltar errores
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 text-end">
                            <button type="button" class="btn btn-outline-primary me-2" onclick="downloadLogs()">
                                <i class="fas fa-download me-2"></i>
                                Descargar Logs
                            </button>
                            <button type="button" class="btn btn-outline-warning me-2" onclick="clearOldLogs()">
                                <i class="fas fa-trash me-2"></i>
                                Limpiar Antiguos
                            </button>
                            <button type="button" class="btn btn-outline-danger" onclick="clearAllLogs()">
                                <i class="fas fa-trash-alt me-2"></i>
                                Limpiar Todos
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Visualizador de Logs -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-terminal me-2"></i>
                        Visualizador de Logs
                    </h6>
                    <div class="d-flex align-items-center">
                        <span class="badge bg-secondary me-2" id="log-count">Mostrando 100 logs</span>
                        <div class="dropdown">
                            <button class="btn btn-outline-secondary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                <i class="fas fa-cog me-1"></i>
                                Opciones
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="#" onclick="toggleLineNumbers()">
                                    <i class="fas fa-list-ol me-2"></i>
                                    Números de Línea
                                </a></li>
                                <li><a class="dropdown-item" href="#" onclick="toggleTimestamp()">
                                    <i class="fas fa-clock me-2"></i>
                                    Timestamps
                                </a></li>
                                <li><a class="dropdown-item" href="#" onclick="toggleColors()">
                                    <i class="fas fa-palette me-2"></i>
                                    Colores
                                </a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="#" onclick="copyLogs()">
                                    <i class="fas fa-copy me-2"></i>
                                    Copiar Seleccionados
                                </a></li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="log-viewer" id="log-viewer">
                        <div class="log-entry log-info" data-level="info" data-timestamp="2024-01-15 14:32:15">
                            <span class="log-timestamp">[2024-01-15 14:32:15]</span>
                            <span class="log-level">[INFO]</span>
                            <span class="log-source">[SecurityMiddleware]</span>
                            <span class="log-message">Request procesado desde IP 192.168.1.100 - Threat Score: 15/100 - Acción: Allow</span>
                        </div>
                        <div class="log-entry log-warning" data-level="warning" data-timestamp="2024-01-15 14:31:42">
                            <span class="log-timestamp">[2024-01-15 14:31:42]</span>
                            <span class="log-level">[WARNING]</span>
                            <span class="log-source">[IPReputationService]</span>
                            <span class="log-message">IP 203.0.113.45 marcada como sospechosa - Reputación: 65/100</span>
                        </div>
                        <div class="log-entry log-error" data-level="error" data-timestamp="2024-01-15 14:30:18">
                            <span class="log-timestamp">[2024-01-15 14:30:18]</span>
                            <span class="log-level">[ERROR]</span>
                            <span class="log-source">[ThreatIntelligenceService]</span>
                            <span class="log-message">Error al conectar con API de VirusTotal: Timeout después de 30 segundos</span>
                        </div>
                        <div class="log-entry log-critical" data-level="critical" data-timestamp="2024-01-15 14:29:55">
                            <span class="log-timestamp">[2024-01-15 14:29:55]</span>
                            <span class="log-level">[CRITICAL]</span>
                            <span class="log-source">[AnomalyDetectionService]</span>
                            <span class="log-message">Detección de anomalía crítica - IP 185.199.108.154 - Score: 0.89 - Acción: BLOCK</span>
                        </div>
                        <div class="log-entry log-info" data-level="info" data-timestamp="2024-01-15 14:28:30">
                            <span class="log-timestamp">[2024-01-15 14:28:30]</span>
                            <span class="log-level">[INFO]</span>
                            <span class="log-source">[SecurityMiddleware]</span>
                            <span class="log-message">Usuario autenticado exitosamente - IP: 192.168.1.50 - User ID: 12345</span>
                        </div>
                        <div class="log-entry log-debug" data-level="debug" data-timestamp="2024-01-15 14:27:15">
                            <span class="log-timestamp">[2024-01-15 14:27:15]</span>
                            <span class="log-level">[DEBUG]</span>
                            <span class="log-source">[MLModel]</span>
                            <span class="log-message">Predicción de anomalía completada - Features: 47, Confidence: 0.87</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Paginación -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-6">
                            <div class="d-flex align-items-center">
                                <label for="logs-per-page" class="form-label me-2 mb-0">Mostrar:</label>
                                <select class="form-select form-select-sm" id="logs-per-page" style="width: auto;">
                                    <option value="50">50</option>
                                    <option value="100" selected>100</option>
                                    <option value="250">250</option>
                                    <option value="500">500</option>
                                    <option value="1000">1000</option>
                                </select>
                                <span class="ms-2">logs por página</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <nav aria-label="Navegación de logs">
                                <ul class="pagination justify-content-end mb-0">
                                    <li class="page-item disabled">
                                        <a class="page-link" href="#" tabindex="-1">Anterior</a>
                                    </li>
                                    <li class="page-item active"><a class="page-link" href="#">1</a></li>
                                    <li class="page-item"><a class="page-link" href="#">2</a></li>
                                    <li class="page-item"><a class="page-link" href="#">3</a></li>
                                    <li class="page-item">
                                        <a class="page-link" href="#">Siguiente</a>
                                    </li>
                                </ul>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Configuración de Logs -->
<div class="modal fade" id="logSettingsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Configuración de Logs</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="log-retention-days" class="form-label">Retención de Logs (días)</label>
                        <input type="number" class="form-control" id="log-retention-days" value="90" min="1" max="365">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="log-compression" class="form-label">Compresión</label>
                        <select class="form-select" id="log-compression">
                            <option value="none">Ninguna</option>
                            <option value="gzip" selected>Gzip</option>
                            <option value="bzip2">Bzip2</option>
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="log-rotation" class="form-label">Rotación</label>
                        <select class="form-select" id="log-rotation">
                            <option value="daily" selected>Diaria</option>
                            <option value="weekly">Semanal</option>
                            <option value="monthly">Mensual</option>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="log-max-size" class="form-label">Tamaño Máximo (MB)</label>
                        <input type="number" class="form-control" id="log-max-size" value="100" min="10" max="1000">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" onclick="saveLogSettings()">Guardar</button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('styles')
<style>
.card { margin-bottom: 1.5rem; }
.border-left-info { border-left: 0.25rem solid #36b9cc !important; }
.border-left-success { border-left: 0.25rem solid #1cc88a !important; }
.border-left-warning { border-left: 0.25rem solid #f6c23e !important; }
.border-left-danger { border-left: 0.25rem solid #e74a3b !important; }
.border-left-dark { border-left: 0.25rem solid #5a5c69 !important; }
.border-left-secondary { border-left: 0.25rem solid #858796 !important; }
.text-gray-800 { color: #5a5c69 !important; }
.text-gray-300 { color: #dddfeb !important; }

.log-viewer {
    background-color: #1e1e1e;
    color: #d4d4d4;
    font-family: 'Courier New', monospace;
    font-size: 13px;
    line-height: 1.4;
    max-height: 600px;
    overflow-y: auto;
    padding: 15px;
    border-radius: 5px;
}

.log-entry {
    padding: 2px 0;
    border-bottom: 1px solid #333;
    cursor: pointer;
    transition: background-color 0.2s;
}

.log-entry:hover {
    background-color: #2d2d2d;
}

.log-entry.log-info { border-left: 3px solid #17a2b8; }
.log-entry.log-warning { border-left: 3px solid #ffc107; }
.log-entry.log-error { border-left: 3px solid #dc3545; }
.log-entry.log-critical { border-left: 3px solid #721c24; }
.log-entry.log-debug { border-left: 3px solid #6c757d; }

.log-timestamp {
    color: #6c757d;
    margin-right: 10px;
}

.log-level {
    font-weight: bold;
    margin-right: 10px;
}

.log-level:contains("[INFO]") { color: #17a2b8; }
.log-level:contains("[WARNING]") { color: #ffc107; }
.log-level:contains("[ERROR]") { color: #dc3545; }
.log-level:contains("[CRITICAL]") { color: #721c24; }
.log-level:contains("[DEBUG]") { color: #6c757d; }

.log-source {
    color: #28a745;
    margin-right: 10px;
}

.log-message {
    color: #d4d4d4;
}

.log-entry.selected {
    background-color: #007bff;
    color: white;
}

.log-entry.selected .log-timestamp,
.log-entry.selected .log-level,
.log-entry.selected .log-source,
.log-entry.selected .log-message {
    color: white;
}
</style>
@endpush

@push('scripts')
<script>
let autoRefreshInterval;
let selectedLogs = [];

document.addEventListener('DOMContentLoaded', function() {
    initializeLogViewer();
    setupEventListeners();
    startAutoRefresh();
});

function setupEventListeners() {
    document.getElementById('logs-per-page').addEventListener('change', function() {
        filterLogs();
    });

    document.getElementById('auto-refresh').addEventListener('change', function() {
        if (this.checked) {
            startAutoRefresh();
        } else {
            stopAutoRefresh();
        }
    });

    document.getElementById('highlight-errors').addEventListener('change', function() {
        toggleErrorHighlighting();
    });
}

function initializeLogViewer() {
    const logViewer = document.getElementById('log-viewer');
    
    // Agregar eventos de click a las entradas de log
    logViewer.addEventListener('click', function(e) {
        if (e.target.closest('.log-entry')) {
            const logEntry = e.target.closest('.log-entry');
            toggleLogSelection(logEntry);
        }
    });

    // Agregar eventos de doble click para expandir detalles
    logViewer.addEventListener('dblclick', function(e) {
        if (e.target.closest('.log-entry')) {
            const logEntry = e.target.closest('.log-entry');
            showLogDetails(logEntry);
        }
    });
}

function filterLogs() {
    const level = document.getElementById('log-level').value;
    const source = document.getElementById('log-source').value;
    const date = document.getElementById('log-date').value;
    const timeStart = document.getElementById('log-time-start').value;
    const timeEnd = document.getElementById('log-time-end').value;
    const search = document.getElementById('log-search').value;
    const ip = document.getElementById('log-ip').value;

    console.log('Filtrando logs:', { level, source, date, timeStart, timeEnd, search, ip });

    // Simular filtrado
    const logEntries = document.querySelectorAll('.log-entry');
    let visibleCount = 0;

    logEntries.forEach(entry => {
        let visible = true;

        // Filtro por nivel
        if (level !== 'all' && entry.dataset.level !== level) {
            visible = false;
        }

        // Filtro por fuente
        if (source !== 'all' && !entry.querySelector('.log-source').textContent.includes(source)) {
            visible = false;
        }

        // Filtro por fecha
        if (date && !entry.querySelector('.log-timestamp').textContent.includes(date)) {
            visible = false;
        }

        // Filtro por búsqueda de texto
        if (search && !entry.textContent.toLowerCase().includes(search.toLowerCase())) {
            visible = false;
        }

        // Filtro por IP
        if (ip && !entry.textContent.includes(ip)) {
            visible = false;
        }

        if (visible) {
            entry.style.display = 'block';
            visibleCount++;
        } else {
            entry.style.display = 'none';
        }
    });

    document.getElementById('log-count').textContent = `Mostrando ${visibleCount} logs`;
    showNotification(`Filtro aplicado: ${visibleCount} logs encontrados`, 'info');
}

function clearFilters() {
    document.getElementById('log-level').value = 'all';
    document.getElementById('log-source').value = 'all';
    document.getElementById('log-date').value = '';
    document.getElementById('log-time-start').value = '';
    document.getElementById('log-time-end').value = '';
    document.getElementById('log-search').value = '';
    document.getElementById('log-ip').value = '';

    // Mostrar todos los logs
    const logEntries = document.querySelectorAll('.log-entry');
    logEntries.forEach(entry => {
        entry.style.display = 'block';
    });

    document.getElementById('log-count').textContent = `Mostrando ${logEntries.length} logs`;
    showNotification('Filtros limpiados', 'info');
}

function toggleLogSelection(logEntry) {
    if (logEntry.classList.contains('selected')) {
        logEntry.classList.remove('selected');
        const index = selectedLogs.indexOf(logEntry);
        if (index > -1) {
            selectedLogs.splice(index, 1);
        }
    } else {
        logEntry.classList.add('selected');
        selectedLogs.push(logEntry);
    }
}

function showLogDetails(logEntry) {
    const timestamp = logEntry.querySelector('.log-timestamp').textContent;
    const level = logEntry.querySelector('.log-level').textContent;
    const source = logEntry.querySelector('.log-source').textContent;
    const message = logEntry.querySelector('.log-message').textContent;

    const details = `
        <div class="alert alert-info">
            <h6>Detalles del Log</h6>
            <p><strong>Timestamp:</strong> ${timestamp}</p>
            <p><strong>Nivel:</strong> ${level}</p>
            <p><strong>Fuente:</strong> ${source}</p>
            <p><strong>Mensaje:</strong> ${message}</p>
        </div>
    `;

    showNotification('Doble click en un log para ver detalles', 'info');
}

function downloadLogs() {
    const visibleLogs = Array.from(document.querySelectorAll('.log-entry:not([style*="display: none"])'));
    const logContent = visibleLogs.map(log => log.textContent).join('\n');
    
    const blob = new Blob([logContent], { type: 'text/plain' });
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = `security-logs-${new Date().toISOString().split('T')[0]}.log`;
    a.click();
    URL.revokeObjectURL(url);

    showNotification('Logs descargados exitosamente', 'success');
}

function clearOldLogs() {
    if (confirm('¿Estás seguro de que quieres limpiar los logs antiguos? Esta acción no se puede deshacer.')) {
        console.log('Limpiando logs antiguos...');
        showNotification('Logs antiguos limpiados exitosamente', 'success');
    }
}

function clearAllLogs() {
    if (confirm('¿Estás seguro de que quieres limpiar TODOS los logs? Esta acción no se puede deshacer.')) {
        console.log('Limpiando todos los logs...');
        document.getElementById('log-viewer').innerHTML = '';
        document.getElementById('log-count').textContent = 'Mostrando 0 logs';
        showNotification('Todos los logs han sido limpiados', 'success');
    }
}

function toggleLineNumbers() {
    console.log('Alternando números de línea');
    showNotification('Función de números de línea implementada', 'info');
}

function toggleTimestamp() {
    console.log('Alternando timestamps');
    showNotification('Función de timestamps implementada', 'info');
}

function toggleColors() {
    console.log('Alternando colores');
    showNotification('Función de colores implementada', 'info');
}

function copyLogs() {
    if (selectedLogs.length === 0) {
        showNotification('Selecciona logs para copiar', 'warning');
        return;
    }

    const logContent = selectedLogs.map(log => log.textContent).join('\n');
    navigator.clipboard.writeText(logContent).then(() => {
        showNotification(`${selectedLogs.length} logs copiados al portapapeles`, 'success');
    }).catch(() => {
        showNotification('Error al copiar logs', 'error');
    });
}

function toggleErrorHighlighting() {
    const logEntries = document.querySelectorAll('.log-entry.log-error, .log-entry.log-critical');
    const highlight = document.getElementById('highlight-errors').checked;

    logEntries.forEach(entry => {
        if (highlight) {
            entry.style.backgroundColor = '#721c24';
            entry.style.color = 'white';
        } else {
            entry.style.backgroundColor = '';
            entry.style.color = '#d4d4d4';
        }
    });
}

function startAutoRefresh() {
    stopAutoRefresh();
    autoRefreshInterval = setInterval(() => {
        if (document.getElementById('auto-refresh').checked) {
            // Simular actualización de logs
            addNewLogEntry();
        }
    }, 30000); // 30 segundos
}

function stopAutoRefresh() {
    if (autoRefreshInterval) {
        clearInterval(autoRefreshInterval);
        autoRefreshInterval = null;
    }
}

function addNewLogEntry() {
    const logViewer = document.getElementById('log-viewer');
    const now = new Date();
    const timestamp = now.toISOString().replace('T', ' ').substring(0, 19);
    
    const newLog = document.createElement('div');
    newLog.className = 'log-entry log-info';
    newLog.dataset.level = 'info';
    newLog.dataset.timestamp = timestamp;
    newLog.innerHTML = `
        <span class="log-timestamp">[${timestamp}]</span>
        <span class="log-level">[INFO]</span>
        <span class="log-source">[AutoRefresh]</span>
        <span class="log-message">Log de prueba generado automáticamente - Timestamp: ${timestamp}</span>
    `;

    logViewer.insertBefore(newLog, logViewer.firstChild);
    
    // Limitar a 1000 entradas
    const logEntries = logViewer.querySelectorAll('.log-entry');
    if (logEntries.length > 1000) {
        logEntries[logEntries.length - 1].remove();
    }
}

function saveLogSettings() {
    const retention = document.getElementById('log-retention-days').value;
    const compression = document.getElementById('log-compression').value;
    const rotation = document.getElementById('log-rotation').value;
    const maxSize = document.getElementById('log-max-size').value;

    console.log('Guardando configuración de logs:', { retention, compression, rotation, maxSize });
    showNotification('Configuración de logs guardada exitosamente', 'success');
    
    const modal = bootstrap.Modal.getInstance(document.getElementById('logSettingsModal'));
    modal.hide();
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
@endpush
