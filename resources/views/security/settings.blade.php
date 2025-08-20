@extends('dashboard')

@section('content')
<div class="container-fluid">
    <!-- Header de Configuración -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card bg-gradient-secondary text-white">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h1 class="mb-2">
                                <i class="fas fa-cogs me-3"></i>
                                Configuración de Seguridad
                            </h1>
                            <p class="mb-0 fs-5">
                                Gestión completa de la configuración del sistema de monitoreo de seguridad
                            </p>
                        </div>
                        <div class="col-md-4 text-end">
                            <div class="d-flex justify-content-end align-items-center">
                                <div class="me-4">
                                    <div class="fs-6 opacity-75">Estado</div>
                                    <div class="fs-4 fw-bold">
                                        <span class="badge bg-success fs-6">ACTIVO</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Navegación de Configuración -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-body">
                    <ul class="nav nav-tabs" id="securityTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="general-tab" data-bs-toggle="tab" data-bs-target="#general" type="button" role="tab">
                                <i class="fas fa-cog me-2"></i>General
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="ip-analysis-tab" data-bs-toggle="tab" data-bs-target="#ip-analysis" type="button" role="tab">
                                <i class="fas fa-globe me-2"></i>Análisis de IP
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="threat-intelligence-tab" data-bs-toggle="tab" data-bs-target="#threat-intelligence" type="button" role="tab">
                                <i class="fas fa-brain me-2"></i>Inteligencia de Amenazas
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="anomaly-detection-tab" data-bs-toggle="tab" data-bs-target="#anomaly-detection" type="button" role="tab">
                                <i class="fas fa-chart-line me-2"></i>Detección de Anomalías
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="actions-tab" data-bs-toggle="tab" data-bs-target="#actions" type="button" role="tab">
                                <i class="fas fa-shield-alt me-2"></i>Acciones de Seguridad
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="alerts-tab" data-bs-toggle="tab" data-bs-target="#alerts" type="button" role="tab">
                                <i class="fas fa-bell me-2"></i>Alertas
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="lists-tab" data-bs-toggle="tab" data-bs-target="#lists" type="button" role="tab">
                                <i class="fas fa-list me-2"></i>Listas
                            </button>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Contenido de las Pestañas -->
    <div class="tab-content" id="securityTabsContent">
        <!-- Pestaña General -->
        <div class="tab-pane fade show active" id="general" role="tabpanel">
            <div class="row">
                <div class="col-12">
                    <div class="card shadow">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">
                                <i class="fas fa-cog me-2"></i>
                                Configuración General del Sistema
                            </h6>
                        </div>
                        <div class="card-body">
                            <form id="general-settings-form">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="security-enabled" class="form-label">Sistema de Seguridad</label>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="security-enabled" checked>
                                            <label class="form-check-label" for="security-enabled">
                                                Habilitar monitoreo de seguridad
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="debug-mode" class="form-label">Modo Debug</label>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="debug-mode">
                                            <label class="form-check-label" for="debug-mode">
                                                Habilitar modo debug
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="log-level" class="form-label">Nivel de Log</label>
                                        <select class="form-select" id="log-level">
                                            <option value="debug">Debug</option>
                                            <option value="info" selected>Info</option>
                                            <option value="warning">Warning</option>
                                            <option value="error">Error</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="cache-driver" class="form-label">Driver de Caché</label>
                                        <select class="form-select" id="cache-driver">
                                            <option value="redis" selected>Redis</option>
                                            <option value="memcached">Memcached</option>
                                            <option value="file">File</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-12">
                                        <button type="button" class="btn btn-primary" onclick="saveGeneralSettings()">
                                            <i class="fas fa-save me-2"></i>
                                            Guardar Configuración General
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pestaña Análisis de IP -->
        <div class="tab-pane fade" id="ip-analysis" role="tabpanel">
            <div class="row">
                <div class="col-12">
                    <div class="card shadow">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">
                                <i class="fas fa-globe me-2"></i>
                                Configuración de Análisis de IP
                            </h6>
                        </div>
                        <div class="card-body">
                            <form id="ip-analysis-form">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="ip-analysis-enabled" class="form-label">Análisis de IP</label>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="ip-analysis-enabled" checked>
                                            <label class="form-check-label" for="ip-analysis-enabled">
                                                Habilitar análisis de IP
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="geolocation-enabled" class="form-label">Geolocalización</label>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="geolocation-enabled" checked>
                                            <label class="form-check-label" for="geolocation-enabled">
                                                Habilitar geolocalización
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="cache-duration" class="form-label">Duración de Caché (horas)</label>
                                        <input type="number" class="form-control" id="cache-duration" value="1" min="0.1" step="0.1">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="max-cache-size" class="form-label">Tamaño Máximo de Caché</label>
                                        <input type="number" class="form-control" id="max-cache-size" value="10000" min="1000">
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-12">
                                        <h6>Umbrales de Riesgo</h6>
                                        <div class="row">
                                            <div class="col-md-2 mb-3">
                                                <label for="threshold-critical" class="form-label">Crítico</label>
                                                <input type="number" class="form-control" id="threshold-critical" value="80" min="0" max="100">
                                            </div>
                                            <div class="col-md-2 mb-3">
                                                <label for="threshold-high" class="form-label">Alto</label>
                                                <input type="number" class="form-control" id="threshold-high" value="60" min="0" max="100">
                                            </div>
                                            <div class="col-md-2 mb-3">
                                                <label for="threshold-medium" class="form-label">Medio</label>
                                                <input type="number" class="form-control" id="threshold-medium" value="40" min="0" max="100">
                                            </div>
                                            <div class="col-md-2 mb-3">
                                                <label for="threshold-low" class="form-label">Bajo</label>
                                                <input type="number" class="form-control" id="threshold-low" value="20" min="0" max="100">
                                            </div>
                                            <div class="col-md-2 mb-3">
                                                <label for="threshold-minimal" class="form-label">Mínimo</label>
                                                <input type="number" class="form-control" id="threshold-minimal" value="0" min="0" max="100">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-12">
                                        <button type="button" class="btn btn-primary" onclick="saveIPAnalysisSettings()">
                                            <i class="fas fa-save me-2"></i>
                                            Guardar Configuración de IP
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pestaña Inteligencia de Amenazas -->
        <div class="tab-pane fade" id="threat-intelligence" role="tabpanel">
            <div class="row">
                <div class="col-12">
                    <div class="card shadow">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">
                                <i class="fas fa-brain me-2"></i>
                                Configuración de Inteligencia de Amenazas
                            </h6>
                        </div>
                        <div class="card-body">
                            <form id="threat-intelligence-form">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="threat-intelligence-enabled" class="form-label">Inteligencia de Amenazas</label>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="threat-intelligence-enabled" checked>
                                            <label class="form-check-label" for="threat-intelligence-enabled">
                                                Habilitar inteligencia de amenazas
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="ml-enabled" class="form-label">Machine Learning</label>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="ml-enabled" checked>
                                            <label class="form-check-label" for="ml-enabled">
                                                Habilitar ML para correlación
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="update-frequency" class="form-label">Frecuencia de Actualización (horas)</label>
                                        <input type="number" class="form-control" id="update-frequency" value="1" min="0.5" step="0.5">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="correlation-threshold" class="form-label">Umbral de Correlación</label>
                                        <input type="number" class="form-control" id="correlation-threshold" value="0.7" min="0" max="1" step="0.1">
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-12">
                                        <h6>Fuentes de Datos</h6>
                                        <div class="row">
                                            <div class="col-md-3 mb-3">
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input" type="checkbox" id="source-abuseipdb" checked>
                                                    <label class="form-check-label" for="source-abuseipdb">
                                                        AbuseIPDB
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="col-md-3 mb-3">
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input" type="checkbox" id="source-virustotal" checked>
                                                    <label class="form-check-label" for="source-virustotal">
                                                        VirusTotal
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="col-md-3 mb-3">
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input" type="checkbox" id="source-ipqualityscore" checked>
                                                    <label class="form-check-label" for="source-ipqualityscore">
                                                        IPQualityScore
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="col-md-3 mb-3">
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input" type="checkbox" id="source-ipapi" checked>
                                                    <label class="form-check-label" for="source-ipapi">
                                                        IP-API
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-12">
                                        <button type="button" class="btn btn-primary" onclick="saveThreatIntelligenceSettings()">
                                            <i class="fas fa-save me-2"></i>
                                            Guardar Configuración de Amenazas
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pestaña Detección de Anomalías -->
        <div class="tab-pane fade" id="anomaly-detection" role="tabpanel">
            <div class="row">
                <div class="col-12">
                    <div class="card shadow">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">
                                <i class="fas fa-chart-line me-2"></i>
                                Configuración de Detección de Anomalías
                            </h6>
                        </div>
                        <div class="card-body">
                            <form id="anomaly-detection-form">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="anomaly-detection-enabled" class="form-label">Detección de Anomalías</label>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="anomaly-detection-enabled" checked>
                                            <label class="form-check-label" for="anomaly-detection-enabled">
                                                Habilitar detección de anomalías
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="anomaly-threshold" class="form-label">Umbral de Anomalía</label>
                                        <input type="number" class="form-control" id="anomaly-threshold" value="0.6" min="0" max="1" step="0.1">
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-12">
                                        <h6>Modelos de Machine Learning</h6>
                                        <div class="row">
                                            <div class="col-md-4 mb-3">
                                                <div class="card">
                                                    <div class="card-body">
                                                        <h6>Isolation Forest</h6>
                                                        <div class="form-check form-switch">
                                                            <input class="form-check-input" type="checkbox" id="ml-isolation-forest" checked>
                                                            <label class="form-check-label" for="ml-isolation-forest">
                                                                Habilitado
                                                            </label>
                                                        </div>
                                                        <div class="mt-2">
                                                            <label for="if-contamination" class="form-label">Contaminación</label>
                                                            <input type="number" class="form-control form-control-sm" id="if-contamination" value="0.1" min="0" max="1" step="0.01">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4 mb-3">
                                                <div class="card">
                                                    <div class="card-body">
                                                        <h6>One-Class SVM</h6>
                                                        <div class="form-check form-switch">
                                                            <input class="form-check-input" type="checkbox" id="ml-one-class-svm" checked>
                                                            <label class="form-check-label" for="ml-one-class-svm">
                                                                Habilitado
                                                            </label>
                                                        </div>
                                                        <div class="mt-2">
                                                            <label for="svm-nu" class="form-label">Nu</label>
                                                            <input type="number" class="form-control form-control-sm" id="svm-nu" value="0.1" min="0" max="1" step="0.01">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4 mb-3">
                                                <div class="card">
                                                    <div class="card-body">
                                                        <h6>Local Outlier Factor</h6>
                                                        <div class="form-check form-switch">
                                                            <input class="form-check-input" type="checkbox" id="ml-local-outlier-factor" checked>
                                                            <label class="form-check-label" for="ml-local-outlier-factor">
                                                                Habilitado
                                                            </label>
                                                        </div>
                                                        <div class="mt-2">
                                                            <label for="lof-neighbors" class="form-label">Vecinos</label>
                                                            <input type="number" class="form-control form-control-sm" id="lof-neighbors" value="20" min="5" max="100">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-12">
                                        <button type="button" class="btn btn-primary" onclick="saveAnomalyDetectionSettings()">
                                            <i class="fas fa-save me-2"></i>
                                            Guardar Configuración de Anomalías
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pestaña Acciones de Seguridad -->
        <div class="tab-pane fade" id="actions" role="tabpanel">
            <div class="row">
                <div class="col-12">
                    <div class="card shadow">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">
                                <i class="fas fa-shield-alt me-2"></i>
                                Configuración de Acciones de Seguridad
                            </h6>
                        </div>
                        <div class="card-body">
                            <form id="security-actions-form">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="auto-block-enabled" class="form-label">Bloqueo Automático</label>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="auto-block-enabled" checked>
                                            <label class="form-check-label" for="auto-block-enabled">
                                                Habilitar bloqueo automático
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="auto-block-threshold" class="form-label">Umbral de Bloqueo</label>
                                        <input type="number" class="form-control" id="auto-block-threshold" value="90" min="0" max="100">
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="challenge-enabled" class="form-label">Desafío de Seguridad</label>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="challenge-enabled" checked>
                                            <label class="form-check-label" for="challenge-enabled">
                                                Habilitar desafíos de seguridad
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="challenge-threshold" class="form-label">Umbral de Desafío</label>
                                        <input type="number" class="form-control" id="challenge-threshold" value="70" min="0" max="100">
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="rate-limiting-enabled" class="form-label">Rate Limiting</label>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="rate-limiting-enabled" checked>
                                            <label class="form-check-label" for="rate-limiting-enabled">
                                                Habilitar rate limiting
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="max-requests" class="form-label">Máximo de Requests</label>
                                        <input type="number" class="form-control" id="max-requests" value="100" min="10" max="1000">
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-12">
                                        <button type="button" class="btn btn-primary" onclick="saveSecurityActionsSettings()">
                                            <i class="fas fa-save me-2"></i>
                                            Guardar Configuración de Acciones
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pestaña Alertas -->
        <div class="tab-pane fade" id="alerts" role="tabpanel">
            <div class="row">
                <div class="col-12">
                    <div class="card shadow">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">
                                <i class="fas fa-bell me-2"></i>
                                Configuración de Alertas y Notificaciones
                            </h6>
                        </div>
                        <div class="card-body">
                            <form id="alerts-form">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="alerts-enabled" class="form-label">Sistema de Alertas</label>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="alerts-enabled" checked>
                                            <label class="form-check-label" for="alerts-enabled">
                                                Habilitar sistema de alertas
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="alert-cooldown" class="form-label">Tiempo de Espera (minutos)</label>
                                        <input type="number" class="form-control" id="alert-cooldown" value="5" min="1" max="60">
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-12">
                                        <h6>Canales de Notificación</h6>
                                        <div class="row">
                                            <div class="col-md-4 mb-3">
                                                <div class="card">
                                                    <div class="card-body">
                                                        <h6>Email</h6>
                                                        <div class="form-check form-switch">
                                                            <input class="form-check-input" type="checkbox" id="email-enabled" checked>
                                                            <label class="form-check-label" for="email-enabled">
                                                                Habilitado
                                                            </label>
                                                        </div>
                                                        <div class="mt-2">
                                                            <label for="email-recipients" class="form-label">Destinatarios</label>
                                                            <input type="text" class="form-control form-control-sm" id="email-recipients" value="admin@example.com" placeholder="email1@domain.com, email2@domain.com">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4 mb-3">
                                                <div class="card">
                                                    <div class="card-body">
                                                        <h6>Slack</h6>
                                                        <div class="form-check form-switch">
                                                            <input class="form-check-input" type="checkbox" id="slack-enabled">
                                                            <label class="form-check-label" for="slack-enabled">
                                                                Habilitado
                                                            </label>
                                                        </div>
                                                        <div class="mt-2">
                                                            <label for="slack-webhook" class="form-label">Webhook URL</label>
                                                            <input type="url" class="form-control form-control-sm" id="slack-webhook" placeholder="https://hooks.slack.com/...">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4 mb-3">
                                                <div class="card">
                                                    <div class="card-body">
                                                        <h6>Webhook</h6>
                                                        <div class="form-check form-switch">
                                                            <input class="form-check-input" type="checkbox" id="webhook-enabled">
                                                            <label class="form-check-label" for="webhook-enabled">
                                                                Habilitado
                                                            </label>
                                                        </div>
                                                        <div class="mt-2">
                                                            <label for="webhook-url" class="form-label">URL del Webhook</label>
                                                            <input type="url" class="form-control form-control-sm" id="webhook-url" placeholder="https://api.example.com/webhook">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-12">
                                        <button type="button" class="btn btn-primary" onclick="saveAlertsSettings()">
                                            <i class="fas fa-save me-2"></i>
                                            Guardar Configuración de Alertas
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pestaña Listas -->
        <div class="tab-pane fade" id="lists" role="tabpanel">
            <div class="row">
                <div class="col-12">
                    <div class="card shadow">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">
                                <i class="fas fa-list me-2"></i>
                                Gestión de Listas de IPs
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <h6>Whitelist</h6>
                                    <div class="form-check form-switch mb-3">
                                        <input class="form-check-input" type="checkbox" id="whitelist-enabled" checked>
                                        <label class="form-check-label" for="whitelist-enabled">
                                            Habilitar whitelist
                                        </label>
                                    </div>
                                    <div class="mb-3">
                                        <label for="whitelist-ips" class="form-label">IPs en Whitelist</label>
                                        <textarea class="form-control" id="whitelist-ips" rows="4" placeholder="127.0.0.1&#10;::1&#10;192.168.1.100"></textarea>
                                        <small class="form-text text-muted">Una IP por línea</small>
                                    </div>
                                    <div class="mb-3">
                                        <label for="whitelist-networks" class="form-label">Redes en Whitelist</label>
                                        <textarea class="form-control" id="whitelist-networks" rows="3" placeholder="10.0.0.0/8&#10;172.16.0.0/12&#10;192.168.0.0/16"></textarea>
                                        <small class="form-text text-muted">Una red por línea (formato CIDR)</small>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <h6>Blacklist</h6>
                                    <div class="form-check form-switch mb-3">
                                        <input class="form-check-input" type="checkbox" id="blacklist-enabled" checked>
                                        <label class="form-check-label" for="blacklist-enabled">
                                            Habilitar blacklist
                                        </label>
                                    </div>
                                    <div class="mb-3">
                                        <label for="blacklist-ips" class="form-label">IPs en Blacklist</label>
                                        <textarea class="form-control" id="blacklist-ips" rows="4" placeholder="203.0.113.10&#10;185.199.108.154"></textarea>
                                        <small class="form-text text-muted">Una IP por línea</small>
                                    </div>
                                    <div class="mb-3">
                                        <label for="blacklist-networks" class="form-label">Redes en Blacklist</label>
                                        <textarea class="form-control" id="blacklist-networks" rows="3" placeholder="192.0.2.0/24"></textarea>
                                        <small class="form-text text-muted">Una red por línea (formato CIDR)</small>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <button type="button" class="btn btn-primary" onclick="saveListsSettings()">
                                        <i class="fas fa-save me-2"></i>
                                        Guardar Configuración de Listas
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('styles')
<style>
.card { margin-bottom: 1.5rem; }
.nav-tabs .nav-link { color: #6c757d; }
.nav-tabs .nav-link.active { color: #495057; font-weight: 600; }
.form-check-input:checked { background-color: #0d6efd; border-color: #0d6efd; }
</style>
@endpush

@push('scripts')
<script>
// Inicialización
document.addEventListener('DOMContentLoaded', function() {
    loadCurrentSettings();
});

function loadCurrentSettings() {
    // Cargar configuración actual desde el servidor
    // Por ahora usamos valores por defecto
    console.log('Cargando configuración actual...');
}

function saveGeneralSettings() {
    const settings = {
        security_enabled: document.getElementById('security-enabled').checked,
        debug_mode: document.getElementById('debug-mode').checked,
        log_level: document.getElementById('log-level').value,
        cache_driver: document.getElementById('cache-driver').value
    };
    
    console.log('Guardando configuración general:', settings);
    showNotification('Configuración general guardada exitosamente', 'success');
}

function saveIPAnalysisSettings() {
    const settings = {
        enabled: document.getElementById('ip-analysis-enabled').checked,
        geolocation_enabled: document.getElementById('geolocation-enabled').checked,
        cache_duration: document.getElementById('cache-duration').value,
        max_cache_size: document.getElementById('max-cache-size').value,
        thresholds: {
            critical: document.getElementById('threshold-critical').value,
            high: document.getElementById('threshold-high').value,
            medium: document.getElementById('threshold-medium').value,
            low: document.getElementById('threshold-low').value,
            minimal: document.getElementById('threshold-minimal').value
        }
    };
    
    console.log('Guardando configuración de análisis de IP:', settings);
    showNotification('Configuración de IP guardada exitosamente', 'success');
}

function saveThreatIntelligenceSettings() {
    const settings = {
        enabled: document.getElementById('threat-intelligence-enabled').checked,
        ml_enabled: document.getElementById('ml-enabled').checked,
        update_frequency: document.getElementById('update-frequency').value,
        correlation_threshold: document.getElementById('correlation-threshold').value,
        sources: {
            abuseipdb: document.getElementById('source-abuseipdb').checked,
            virustotal: document.getElementById('source-virustotal').checked,
            ipqualityscore: document.getElementById('source-ipqualityscore').checked,
            ipapi: document.getElementById('source-ipapi').checked
        }
    };
    
    console.log('Guardando configuración de inteligencia de amenazas:', settings);
    showNotification('Configuración de amenazas guardada exitosamente', 'success');
}

function saveAnomalyDetectionSettings() {
    const settings = {
        enabled: document.getElementById('anomaly-detection-enabled').checked,
        threshold: document.getElementById('anomaly-threshold').value,
        models: {
            isolation_forest: {
                enabled: document.getElementById('ml-isolation-forest').checked,
                contamination: document.getElementById('if-contamination').value
            },
            one_class_svm: {
                enabled: document.getElementById('ml-one-class-svm').checked,
                nu: document.getElementById('svm-nu').value
            },
            local_outlier_factor: {
                enabled: document.getElementById('ml-local-outlier-factor').checked,
                n_neighbors: document.getElementById('lof-neighbors').value
            }
        }
    };
    
    console.log('Guardando configuración de detección de anomalías:', settings);
    showNotification('Configuración de anomalías guardada exitosamente', 'success');
}

function saveSecurityActionsSettings() {
    const settings = {
        auto_block: {
            enabled: document.getElementById('auto-block-enabled').checked,
            threshold: document.getElementById('auto-block-threshold').value
        },
        challenge: {
            enabled: document.getElementById('challenge-enabled').checked,
            threshold: document.getElementById('challenge-threshold').value
        },
        rate_limiting: {
            enabled: document.getElementById('rate-limiting-enabled').checked,
            max_requests: document.getElementById('max-requests').value
        }
    };
    
    console.log('Guardando configuración de acciones de seguridad:', settings);
    showNotification('Configuración de acciones guardada exitosamente', 'success');
}

function saveAlertsSettings() {
    const settings = {
        enabled: document.getElementById('alerts-enabled').checked,
        cooldown: document.getElementById('alert-cooldown').value,
        channels: {
            email: {
                enabled: document.getElementById('email-enabled').checked,
                recipients: document.getElementById('email-recipients').value
            },
            slack: {
                enabled: document.getElementById('slack-enabled').checked,
                webhook_url: document.getElementById('slack-webhook').value
            },
            webhook: {
                enabled: document.getElementById('webhook-enabled').checked,
                url: document.getElementById('webhook-url').value
            }
        }
    };
    
    console.log('Guardando configuración de alertas:', settings);
    showNotification('Configuración de alertas guardada exitosamente', 'success');
}

function saveListsSettings() {
    const settings = {
        whitelist: {
            enabled: document.getElementById('whitelist-enabled').checked,
            ips: document.getElementById('whitelist-ips').value.split('\n').filter(ip => ip.trim()),
            networks: document.getElementById('whitelist-networks').value.split('\n').filter(net => net.trim())
        },
        blacklist: {
            enabled: document.getElementById('blacklist-enabled').checked,
            ips: document.getElementById('blacklist-ips').value.split('\n').filter(ip => ip.trim()),
            networks: document.getElementById('blacklist-networks').value.split('\n').filter(net => net.trim())
        }
    };
    
    console.log('Guardando configuración de listas:', settings);
    showNotification('Configuración de listas guardada exitosamente', 'success');
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
