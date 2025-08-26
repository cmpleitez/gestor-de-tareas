@extends('dashboard')

@php
    // Variables por defecto para evitar conflictos de sintaxis con @json
    $defaultRiskData = [0, 0, 0]; // Solo 3 niveles: Crítico, Alto, Medio
    $defaultCountryData = [];
@endphp

@section('css')
    <!-- BEGIN: Security Dashboard CSS -->
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/css/security-dashboard.css') }}">
    <!-- END: Security Dashboard CSS -->
@stop

@section('contenedor')

    <div class="container-fluid" data-risk-distribution="{{ trim(json_encode($risk_distribution ?? $defaultRiskData)) }}"
        data-threats-by-country="{{ trim(json_encode($threats_by_country ?? $defaultCountryData)) }}">
        <!-- ========================================
        HEADER DEL DASHBOARD DE SEGURIDAD
        ======================================== -->
        <x-security.dashboard-header />

        <!-- GRÁFICOS Y ANÁLISIS VISUALES -->
        <div class="row">
            <!-- Gráfico de Eventos por Nivel de Riesgo -->
            <div class="col-xl-8 col-lg-7">
                <x-security.chart-card title="Distribución de Eventos por Nivel de Riesgo" icon="fas fa-chart-pie"
                    chart_id="riskDistributionChart" :show_dropdown="true" :dropdown_items="[
                        ['label' => 'Últimos 7 días', 'onclick' => 'updateChart(\'7d\')'],
                        ['label' => 'Últimos 30 días', 'onclick' => 'updateChart(\'30d\')'],
                        ['label' => 'Últimos 90 días', 'onclick' => 'updateChart(\'90d\')'],
                    ]" chart_height="500px" />
            </div>

            <!-- Gráfico de Amenazas por País -->
            <div class="col-xl-4 col-lg-5">
                <x-security.chart-card title="Amenazas por País" icon="fas fa-globe" chart_id="threatsByCountryChart"
                    chart_height="500px" />
            </div>
        </div>

        <!-- ACTIVIDAD EN TIEMPO REAL -->
        <div class="row">
            <!-- Eventos Recientes -->
            <div class="col-xl-6">
                <x-security.recent-events :events="$recent_events ?? collect()" title="Eventos Recientes" icon="fas fa-clock" />
            </div>

            <!-- Top IPs Sospechosas -->
            <div class="col-xl-6">
                <x-security.suspicious-ips :ips="$top_suspicious_ips ?? collect()" title="Top 10 IPs Sospechosas" icon="fas fa-list-ol" />
            </div>
        </div>
    </div>
@stop

@section('js')
    <!-- BEGIN: Application JavaScript -->
    <script src="{{ asset('app-assets/js/security-dashboard.js') }}"></script>

    <!-- Mostrar mensajes de error con Toastr -->
    @if (isset($error_message))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                if (typeof toastr !== 'undefined') {
                    toastr.error("{{ $error_message }}", 'Error de Seguridad', {
                        timeOut: 8000,
                        extendedTimeOut: 2000,
                        closeButton: true,
                        progressBar: true
                    });
                }
            });
        </script>
    @endif
    <!-- END: Application JavaScript -->
@stop
