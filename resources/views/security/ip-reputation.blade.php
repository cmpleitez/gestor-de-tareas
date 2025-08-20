@extends('dashboard')

@section('content')
<div class="container-fluid">
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
                                Análisis avanzado de reputación de direcciones IP con Machine Learning y fuentes externas
                            </p>
                        </div>
                        <div class="col-md-4 text-end">
                            <div class="d-flex justify-content-end align-items-center">
                                <div class="me-4">
                                    <div class="fs-6 opacity-75">IPs Monitoreadas</div>
                                    <div class="fs-4 fw-bold" id="monitored-ips-count">
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

    <!-- Métricas de Reputación -->
    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                IPs Limpias
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="clean-ips-count">0</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
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
                                IPs Sospechosas
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="suspicious-ips-count">0</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-exclamation-triangle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-3">
            <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                IPs Maliciosas
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="malicious-ips-count">0</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-skull-crossbones fa-2x text-gray-300"></i>
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
                                Score Promedio
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="avg-score">0</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-chart-line fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Gráficos de Análisis -->
    <div class="row mb-4">
        <div class="col-xl-6 col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-chart-pie me-2"></i>
                        Distribución por Nivel de Riesgo
                    </h6>
                </div>
                <div class="card-body">
                    <div class="chart-area">
                        <canvas id="riskDistributionChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-6 col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-globe me-2"></i>
                        IPs por País
                    </h6>
                </div>
                <div class="card-body">
                    <div class="chart-area">
                        <canvas id="countryDistributionChart"></canvas>
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
                                    <option value="minimal">Mínimo</option>
                                    <option value="low">Bajo</option>
                                    <option value="medium">Medio</option>
                                    <option value="high">Alto</option>
                                    <option value="critical">Crítico</option>
                                </select>
                            </div>

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

                            <div class="col-md-3 mb-3 d-flex align-items-end">
                                <button type="button" class="btn btn-primary me-2" onclick="applyIPFilters()">
                                    <i class="fas fa-search me-2"></i>
                                    Filtrar
                                </button>
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
                        Base de Datos de Reputación
                    </h6>
                    <div class="d-flex">
                        <button class="btn btn-outline-info btn-sm me-2" onclick="updateIPReputation()">
                            <i class="fas fa-sync-alt me-2"></i>
                            Actualizar
                        </button>
                        <button class="btn btn-outline-success btn-sm me-2" onclick="exportIPReputation()">
                            <i class="fas fa-download me-2"></i>
                            Exportar
                        </button>
                        <button class="btn btn-outline-warning btn-sm" onclick="showAddIPModal()">
                            <i class="fas fa-plus me-2"></i>
                            Agregar IP
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
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody id="ip-reputation-table-body">
                                <tr>
                                    <td colspan="8" class="text-center py-4">
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
                            Mostrando <span id="ips-showing-start">0</span> a <span id="ips-showing-end">0</span> de <span id="ips-showing-total">0</span> IPs
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

<!-- Modales -->
<div class="modal fade" id="ipDetailsModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-globe text-info me-2"></i>
                    Detalles de la IP
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="ip-details-content">
                <!-- Contenido se cargará dinámicamente -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-warning" onclick="investigateIP()">
                    <i class="fas fa-search me-2"></i>
                    Investigar
                </button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="addIPModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-plus text-success me-2"></i>
                    Agregar Nueva IP
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="add-ip-form">
                    <div class="mb-3">
                        <label for="new-ip-address" class="form-label">Dirección IP</label>
                        <input type="text" class="form-control" id="new-ip-address" placeholder="192.168.1.100" required>
                    </div>
                    <div class="mb-3">
                        <label for="new-ip-notes" class="form-label">Notas</label>
                        <textarea class="form-control" id="new-ip-notes" rows="3" placeholder="Información adicional sobre esta IP..."></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-success" onclick="addNewIP()">
                    <i class="fas fa-plus me-2"></i>
                    Agregar IP
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('styles')
<style>
.border-left-success { border-left: 0.25rem solid #1cc88a !important; }
.border-left-warning { border-left: 0.25rem solid #f6c23e !important; }
.border-left-danger { border-left: 0.25rem solid #e74a3b !important; }
.border-left-info { border-left: 0.25rem solid #36b9cc !important; }

.chart-area { position: relative; height: 300px; }

.ip-row { cursor: pointer; transition: background-color 0.2s; }
.ip-row:hover { background-color: #f8f9fc; }
.ip-row.critical { border-left: 4px solid #e74a3b; }
.ip-row.high { border-left: 4px solid #f6c23e; }
.ip-row.medium { border-left: 4px solid #fd7e14; }
.ip-row.low { border-left: 4px solid #20c9a6; }
.ip-row.minimal { border-left: 4px solid #1cc88a; }

.risk-badge { font-size: 0.75rem; padding: 0.25rem 0.5rem; }

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

.score-fill.critical { background-color: #e74a3b; }
.score-fill.high { background-color: #f6c23e; }
.score-fill.medium { background-color: #fd7e14; }
.score-fill.low { background-color: #20c9a6; }
.score-fill.minimal { background-color: #1cc88a; }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Variables globales
let riskDistributionChart;
let countryDistributionChart;
let currentIPPage = 1;
let ipsPerPage = 25;
let totalIPs = 0;

// Inicialización
document.addEventListener('DOMContentLoaded', function() {
    initializeIPCharts();
    loadIPReputation();
    loadIPStats();
});

function initializeIPCharts() {
    // Gráfico de distribución de riesgo
    const riskCtx = document.getElementById('riskDistributionChart').getContext('2d');
    riskDistributionChart = new Chart(riskCtx, {
        type: 'doughnut',
        data: {
            labels: ['Mínimo', 'Bajo', 'Medio', 'Alto', 'Crítico'],
            datasets: [{
                data: [0, 0, 0, 0, 0],
                backgroundColor: ['#1cc88a', '#20c9a6', '#fd7e14', '#f6c23e', '#e74a3b'],
                borderWidth: 2,
                borderColor: '#fff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { position: 'bottom' } }
        }
    });

    // Gráfico de distribución por país
    const countryCtx = document.getElementById('countryDistributionChart').getContext('2d');
    countryDistributionChart = new Chart(countryCtx, {
        type: 'bar',
        data: {
            labels: ['Estados Unidos', 'China', 'Rusia', 'Alemania', 'Reino Unido'],
            datasets: [{
                label: 'Cantidad de IPs',
                data: [0, 0, 0, 0, 0],
                backgroundColor: ['#4e73df', '#f6c23e', '#e74a3b', '#36b9cc', '#1cc88a']
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: { y: { beginAtZero: true } }
        }
    });
}

function loadIPReputation(page = 1) {
    currentIPPage = page;
    
    const tableBody = document.getElementById('ip-reputation-table-body');
    
    // Mostrar loading
    tableBody.innerHTML = `
        <tr>
            <td colspan="8" class="text-center py-4">
                <i class="fas fa-spinner fa-spin fa-2x text-gray-400"></i>
                <p class="mt-2 text-gray-500">Cargando reputaciones...</p>
            </td>
        </tr>
    `;
    
    // Simular delay de carga
    setTimeout(() => {
        const ips = generateSampleIPs();
        renderIPsTable(ips);
        updateIPsPagination();
        updateIPCharts(ips);
    }, 1000);
}

function generateSampleIPs() {
    const ips = [];
    const ipRanges = ['203.0.113', '185.199.108', '198.51.100', '104.21.92', '45.33.12'];
    const countries = ['US', 'CN', 'RU', 'DE', 'GB'];
    const isps = ['Cloudflare', 'GitHub', 'Amazon', 'DigitalOcean', 'Linode'];
    const riskLevels = ['minimal', 'low', 'medium', 'high', 'critical'];
    
    for (let i = 0; i < 50; i++) {
        const score = Math.floor(Math.random() * 100);
        const riskLevel = getRiskLevel(score);
        
        ips.push({
            id: i + 1,
            ip: `${ipRanges[Math.floor(Math.random() * ipRanges.length)]}.${Math.floor(Math.random() * 255)}`,
            score: score,
            risk_level: riskLevel,
            country: countries[Math.floor(Math.random() * countries.length)],
            isp: isps[Math.floor(Math.random() * isps.length)],
            lastUpdated: new Date(Date.now() - Math.random() * 30 * 24 * 60 * 60 * 1000),
            status: 'active',
            totalRequests: Math.floor(Math.random() * 10000),
            threatRequests: Math.floor(Math.random() * 1000),
            benignRequests: Math.floor(Math.random() * 9000)
        });
    }
    
    return ips;
}

function getRiskLevel(score) {
    if (score >= 80) return 'critical';
    if (score >= 60) return 'high';
    if (score >= 40) return 'medium';
    if (score >= 20) return 'low';
    return 'minimal';
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
        row.onclick = () => showIPDetails(ip);
        
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
            <td>
                <div class="btn-group btn-group-sm">
                    <button class="btn btn-outline-primary" onclick="event.stopPropagation(); showIPDetails(${JSON.stringify(ip).replace(/"/g, '&quot;')})">
                        <i class="fas fa-eye"></i>
                    </button>
                    <button class="btn btn-outline-warning" onclick="event.stopPropagation(); investigateIP(${JSON.stringify(ip).replace(/"/g, '&quot;')})">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
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
    return new Intl.DateTimeFormat('es-ES', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    }).format(date);
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
    prevLi.innerHTML = `
        <a class="page-link" href="#" onclick="loadIPReputation(${currentIPPage - 1})">
            <i class="fas fa-chevron-left"></i>
        </a>
    `;
    pagination.appendChild(prevLi);
    
    // Páginas numeradas
    for (let i = 1; i <= totalPages; i++) {
        if (i === 1 || i === totalPages || (i >= currentIPPage - 2 && i <= currentIPPage + 2)) {
            const li = document.createElement('li');
            li.className = `page-item ${i === currentIPPage ? 'active' : ''}`;
            li.innerHTML = `
                <a class="page-link" href="#" onclick="loadIPReputation(${i})">${i}</a>
            `;
            pagination.appendChild(li);
        } else if (i === currentIPPage - 3 || i === currentIPPage + 3) {
            const li = document.createElement('li');
            li.className = 'page-item disabled';
            li.innerHTML = '<span class="page-link">...</span>';
            pagination.appendChild(li);
        }
    }
    
    // Botón siguiente
    const nextLi = document.createElement('li');
    nextLi.className = `page-item ${currentIPPage === totalPages ? 'disabled' : ''}`;
    nextLi.innerHTML = `
        <a class="page-link" href="#" onclick="loadIPReputation(${currentIPPage + 1})">
            <i class="fas fa-chevron-right"></i>
        </a>
    `;
    pagination.appendChild(nextLi);
}

function updateIPCharts(ips) {
    // Actualizar gráfico de distribución de riesgo
    const riskCounts = {};
    ips.forEach(ip => {
        riskCounts[ip.risk_level] = (riskCounts[ip.risk_level] || 0) + 1;
    });
    
    riskDistributionChart.data.datasets[0].data = [
        riskCounts.minimal || 0,
        riskCounts.low || 0,
        riskCounts.medium || 0,
        riskCounts.high || 0,
        riskCounts.critical || 0
    ];
    riskDistributionChart.update();
    
    // Actualizar gráfico de distribución por país
    const countryCounts = {};
    ips.forEach(ip => {
        countryCounts[ip.country] = (countryCounts[ip.country] || 0) + 1;
    });
    
    countryDistributionChart.data.datasets[0].data = [
        countryCounts.US || 0,
        countryCounts.CN || 0,
        countryCounts.RU || 0,
        countryCounts.DE || 0,
        countryCounts.GB || 0
    ];
    countryDistributionChart.update();
}

function loadIPStats() {
    setTimeout(() => {
        document.getElementById('clean-ips-count').textContent = '35';
        document.getElementById('suspicious-ips-count').textContent = '10';
        document.getElementById('malicious-ips-count').textContent = '5';
        document.getElementById('avg-score').textContent = '25';
        document.getElementById('monitored-ips-count').textContent = '50';
    }, 500);
}

function applyIPFilters() {
    console.log('Aplicando filtros de IPs...');
    loadIPReputation(1);
}

function clearIPFilters() {
    document.getElementById('ip-filter-form').reset();
    loadIPReputation(1);
}

function updateIPReputation() {
    console.log('Actualizando reputación de IPs...');
    showNotification('Actualización iniciada', 'info');
}

function exportIPReputation() {
    console.log('Exportando base de datos de reputación...');
    showNotification('Exportación iniciada', 'info');
}

function showAddIPModal() {
    new bootstrap.Modal(document.getElementById('addIPModal')).show();
}

function addNewIP() {
    const ip = document.getElementById('new-ip-address').value;
    const notes = document.getElementById('new-ip-notes').value;
    
    if (!ip) {
        alert('Por favor complete la dirección IP.');
        return;
    }
    
    console.log('Agregando nueva IP:', { ip, notes });
    
    // Cerrar modal
    bootstrap.Modal.getInstance(document.getElementById('addIPModal')).hide();
    
    // Mostrar notificación
    showNotification('IP agregada exitosamente', 'success');
    
    // Recargar IPs
    loadIPReputation(currentIPPage);
}

function showIPDetails(ip) {
    const modal = new bootstrap.Modal(document.getElementById('ipDetailsModal'));
    const content = document.getElementById('ip-details-content');
    
    content.innerHTML = `
        <div class="row">
            <div class="col-md-6">
                <h6>Información Básica</h6>
                <table class="table table-sm">
                    <tr><td><strong>IP:</strong></td><td>${ip.ip}</td></tr>
                    <tr><td><strong>Score:</strong></td><td>${ip.score}/100</td></tr>
                    <tr><td><strong>Nivel de Riesgo:</strong></td><td><span class="badge bg-${getRiskBadgeColor(ip.risk_level)}">${ip.risk_level.toUpperCase()}</span></td></tr>
                    <tr><td><strong>País:</strong></td><td>${ip.country}</td></tr>
                    <tr><td><strong>ISP:</strong></td><td>${ip.isp}</td></tr>
                    <tr><td><strong>Estado:</strong></td><td><span class="badge bg-success">${ip.status}</span></td></tr>
                </table>
            </div>
            <div class="col-md-6">
                <h6>Estadísticas de Tráfico</h6>
                <table class="table table-sm">
                    <tr><td><strong>Total de Requests:</strong></td><td>${ip.totalRequests.toLocaleString()}</td></tr>
                    <tr><td><strong>Requests Maliciosos:</strong></td><td>${ip.threatRequests.toLocaleString()}</td></tr>
                    <tr><td><strong>Requests Legítimos:</strong></td><td>${ip.benignRequests.toLocaleString()}</td></tr>
                    <tr><td><strong>Última Actualización:</strong></td><td>${formatDate(ip.lastUpdated)}</td></tr>
                </table>
            </div>
        </div>
    `;
    
    modal.show();
}

function investigateIP(ip) {
    console.log('Investigando IP:', ip);
    showNotification('Investigación iniciada', 'info');
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
