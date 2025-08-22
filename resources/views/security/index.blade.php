@extends('dashboard')

@php
    // Variables por defecto para evitar conflictos de sintaxis con @json
    $defaultRiskData = [0, 0, 0, 0, 0];
    $defaultCountryData = [];
@endphp

@section('css')

@stop

@section('contenedor')

    <div class="container-fluid" data-risk-distribution="{{ trim(json_encode($risk_distribution ?? $defaultRiskData)) }}"
        data-threats-by-country="{{ trim(json_encode($threats_by_country ?? $defaultCountryData)) }}">
        <!-- ========================================
                                                                                                                                                                                                                                                HEADER DEL DASHBOARD DE SEGURIDAD
                                                                                                                                                                                                                                                ======================================== -->
        <x-security.dashboard-header />

        <!-- ========================================
                                                                                                                                                                                                                                                MÉTRICAS PRINCIPALES - KPIs DE SEGURIDAD
                                                                                                                                                                                                                                                ======================================== -->
        <div class="row mb-4">
            <x-security.metric-card title="Eventos (24h)" :value="$metrics['events_24h'] ?? 0" icon="fas fa-shield-alt" color="primary" />

            <x-security.metric-card title="Amenazas Críticas (24h)" :value="$metrics['critical_threats_24h'] ?? 0" icon="fas fa-exclamation-triangle"
                color="danger" />

            <x-security.metric-card title="Amenazas Altas (24h)" :value="$metrics['high_threats_24h'] ?? 0" icon="fas fa-exclamation-circle"
                color="warning" />

            <x-security.metric-card title="IPs Únicas (24h)" :value="$metrics['unique_ips_24h'] ?? 0" icon="fas fa-globe" color="info" />

            <x-security.metric-card title="Score Promedio (24h)" :value="round($metrics['total_threat_score_24h'] ?? 0, 1)" icon="fas fa-chart-line"
                color="success" />
        </div>

        <!-- GRÁFICOS Y ANÁLISIS VISUALES -->
        <div class="row mb-4">
            <!-- Gráfico de Eventos por Nivel de Riesgo -->
            <div class="col-xl-8 col-lg-7">
                <x-security.chart-card title="Distribución de Eventos por Nivel de Riesgo" icon="fas fa-chart-pie"
                    chart_id="riskDistributionChart" :show_dropdown="true" :dropdown_items="[
                        ['label' => 'Últimos 7 días', 'onclick' => 'updateChart(\'7d\')'],
                        ['label' => 'Últimos 30 días', 'onclick' => 'updateChart(\'30d\')'],
                        ['label' => 'Últimos 90 días', 'onclick' => 'updateChart(\'90d\')'],
                    ]" />
            </div>

            <!-- Gráfico de Amenazas por País -->
            <div class="col-xl-4 col-lg-5">
                <x-security.chart-card title="Amenazas por País" icon="fas fa-globe" chart_id="threatsByCountryChart"
                    chart_height="250px" />
            </div>
        </div>

        <!-- ACTIVIDAD EN TIEMPO REAL -->
        <div class="row mb-4">
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
