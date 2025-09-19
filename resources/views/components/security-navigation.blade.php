<!-- Navegación del Dashboard de Seguridad -->
<div class="security-navigation">
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-gradient-primary text-white">
            <h6 class="mb-0">
                <i class="fas fa-shield-alt me-2"></i>
                Panel de Seguridad
            </h6>
        </div>
        <div class="card-body p-0">
            <nav class="nav flex-column">
                <a class="nav-link {{ request()->routeIs('security.dashboard') ? 'active' : '' }}"
                    href="{{ route('security.dashboard') }}">
                    <i class="fas fa-tachometer-alt me-2"></i>
                    Dashboard
                </a>

                <a class="nav-link {{ request()->routeIs('security.events') ? 'active' : '' }}"
                    href="{{ route('security.events') }}">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Eventos
                </a>

                <a class="nav-link {{ request()->routeIs('security.threat-intelligence') ? 'active' : '' }}"
                    href="{{ route('security.threat-intelligence') }}">
                    <i class="fas fa-brain me-2"></i>
                    Inteligencia de Amenazas
                </a>

                <a class="nav-link {{ request()->routeIs('security.ip-reputation') ? 'active' : '' }}"
                    href="{{ route('security.ip-reputation') }}">
                    <i class="fas fa-globe me-2"></i>
                    Reputación de IPs
                </a>



                <a class="nav-link {{ request()->routeIs('security.logs') ? 'active' : '' }}"
                    href="{{ route('security.logs') }}">
                    <i class="fas fa-file-alt me-2"></i>
                    Logs
                </a>


            </nav>
        </div>
    </div>

    <!-- Estado del Sistema -->
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-gradient-info text-white">
            <h6 class="mb-0">
                <i class="fas fa-info-circle me-2"></i>
                Estado del Sistema
            </h6>
        </div>
        <div class="card-body">
            <div class="d-flex align-items-center mb-2">
                <div class="me-2">
                    <i class="fas fa-circle text-success"></i>
                </div>
                <small>Sistema Activo</small>
            </div>
            <div class="d-flex align-items-center mb-2">
                <div class="me-2">
                    <i class="fas fa-shield-alt text-primary"></i>
                </div>
                <small>Monitoreo Activo</small>
            </div>
            <div class="d-flex align-items-center">
                <div class="me-2">
                    <i class="fas fa-clock text-warning"></i>
                </div>
                <small>Última actualización: <span id="last-update-time">Hace 5 min</span></small>
            </div>
        </div>
    </div>

    <!-- Acciones Rápidas -->
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-gradient-warning text-white">
            <h6 class="mb-0">
                <i class="fas fa-bolt me-2"></i>
                Acciones Rápidas
            </h6>
        </div>
        <div class="card-body">

            <button class="btn btn-outline-success btn-sm w-100 mb-2" onclick="showWhitelistIPModal()">
                <i class="fas fa-check me-2"></i>
                Whitelist IP
            </button>
            <button class="btn btn-outline-warning btn-sm w-100" onclick="showMaintenanceModal()">
                <i class="fas fa-tools me-2"></i>
                Modo Mantenimiento
            </button>
        </div>
    </div>
</div>

<style>
    .security-navigation .nav-link {
        color: #6c757d;
        padding: 0.75rem 1rem;
        border-left: 3px solid transparent;
        transition: all 0.3s ease;
    }

    .security-navigation .nav-link:hover {
        color: #495057;
        background-color: #f8f9fa;
        border-left-color: #dee2e6;
    }

    .security-navigation .nav-link.active {
        color: #007bff;
        background-color: #e3f2fd;
        border-left-color: #007bff;
        font-weight: 600;
    }

    .security-navigation .card-header {
        border-bottom: none;
    }

    .security-navigation .card {
        border: none;
        border-radius: 0.5rem;
    }

    .security-navigation .btn {
        border-radius: 0.375rem;
    }
</style>

<script>
    // Actualizar tiempo de última actualización
    function updateLastUpdateTime() {
        const now = new Date();
        const timeString = now.toLocaleTimeString('es-ES', {
            hour: '2-digit',
            minute: '2-digit'
        });
        document.getElementById('last-update-time').textContent = `Hace ${timeString}`;
    }

    // Actualizar cada 5 minutos
    setInterval(updateLastUpdateTime, 300000);



    function showWhitelistIPModal() {}

    function showMaintenanceModal() {}
</script>
