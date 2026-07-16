{{-- Componente para hacer tablas responsive en dispositivos móviles --}}
<style>

    /* =========================================================
       CONTROL MANUAL — Márgenes de la vista madre (.content-body, dashboard L287)
       aplicados SOLO en las vistas de tarjetas (índices admin + equipo/stock).
       Ajusta estos valores para cambiar el respiro de las tarjetas por dispositivo.
       ========================================================= */
    :root {
        --tarjetas-mt-celular: 5rem; /* margen superior en celular */
        --tarjetas-mx-celular: 0.5rem;      /* margen lateral en celular (0 = ancho completo) */
        --tarjetas-mt-tablet: 0;   /* margen superior en tablet */
        --tarjetas-mx-tablet: 0;   /* margen lateral en tablet */
    }

    .table-responsive-mobile {
        overflow-x: auto;
    }

    .table-responsive-mobile table {
        width: 100%;
        border-collapse: collapse;
    }

    .table-responsive-mobile th,
    .table-responsive-mobile td {
        padding: 8px;
        text-align: left;
        border-bottom: 1px solid #ddd;
    }

    /* En móviles, reorganizar tabla en formato vertical */
    @media screen and (max-width: 768px) {

        div.dataTables_wrapper div.dataTables_length, div.dataTables_wrapper div.dataTables_filter, div.dataTables_wrapper div.dataTables_info, div.dataTables_wrapper div.dataTables_paginate {
            margin-top: 10px !important;
            font-size: 12px !important;
            text-align: right !important;
        }        

        html body.navbar-sticky .app-content .content-wrapper {
            padding: 2.8rem 0;
            margin-top: 3rem;
        }

        div.dataTables_wrapper div.dataTables_paginate, div.dataTables_wrapper div.dataTables_info {
            text-align: right;
        }
        
        div.dataTables_wrapper div.dataTables_paginate ul.pagination, div.dataTables_wrapper div.dataTables_info ul.pagination {
            justify-content: right;
        }

        /* Optimizar layout de columnas y cards en móviles para ancho completo */
        .col-12 {
            padding-left: 0 !important;
            padding-right: 0 !important;
        }

        .card {
            margin-bottom: 15px !important;
            border-radius: 0 !important;
        }

        .card-header {
            padding: 15px 12px !important;
        }

        .card-body {
            padding: 0 !important;
        }

        .card-content {
            padding: 0 !important;
        }

        .table-responsive {
            padding: 0 !important;
            margin: 0 !important;
        }

        /* Conversión tabla→tarjetas movida al bloque táctil/responsive consolidado (ver más abajo) */
    }

    /* Optimizaciones adicionales para pantallas muy pequeñas */
    @media screen and (max-width: 576px) {
        html body.navbar-sticky .app-content .content-wrapper {
            margin-top: 3rem;
            padding-top: 2.8rem;
            padding-bottom: 0;
        }

        div.dataTables_wrapper div.dataTables_length, div.dataTables_wrapper div.dataTables_filter, div.dataTables_wrapper div.dataTables_info, div.dataTables_wrapper div.dataTables_paginate {
            margin-top: 10px !important;
            font-size: 12px !important;
            text-align: right !important;
        }

        .card-header {
            padding: 15px 28px !important;
        }

        .col-12 {
            padding-left: 0 !important;
            padding-right: 0 !important;
        }

        .card-body {
            padding: 12px 8px !important;
        }

        .card-title {
            font-size: 18px !important;
            margin-bottom: 8px !important;
        }

        .card-text {
            font-size: 14px !important;
            margin-bottom: 12px !important;
        }
    }

    /* =========================================================
       TARJETAS RESPONSIVE (índices admin)
       Conversión tabla→tarjetas: por ancho hasta 1024px (permite
       probar redimensionando el navegador) y extendida hasta 1366px
       solo en dispositivos táctiles (pointer: coarse) para cubrir la
       tablet horizontal sin afectar monitores de escritorio.
       Grid: 1 col (celular vertical) / 2 (celular horiz + tablet vert) / 3 (tablet horiz).
       ========================================================= */
    @media screen and (max-width: 1024px), (pointer: coarse) and (max-width: 1366px) {
        .table-responsive-mobile table,
        .table-responsive-mobile tbody,
        .table-responsive-mobile th,
        .table-responsive-mobile td,
        .table-responsive-mobile tr {
            display: block;
        }

        .table-responsive-mobile thead tr {
            position: absolute;
            top: -9999px;
            left: -9999px;
        }

        .table-responsive-mobile tbody {
            display: grid;
            gap: 12px;
            grid-template-columns: 1fr; /* celular vertical: 1 tarjeta por fila */
        }

        .table-responsive-mobile tr {
            border: 0.5px solid #ededed;
            border-radius: 8px;
            background: #f8fafc;
            padding: 8px;
            margin: 0;
        }

        .table-responsive-mobile td {
            border: none;
            position: relative;
            padding-left: 50%;
            text-align: left !important;
            margin-bottom: 8px;
        }

        .table-responsive-mobile td:before {
            content: attr(data-label);
            position: absolute;
            left: 8px;
            width: 45%;
            padding-right: 10px;
            white-space: nowrap;
            font-weight: bold;
            color: #333;
            text-align: right;
        }

        .table-responsive-mobile td:last-child {
            margin-bottom: 0;
        }

        .table-responsive-mobile td.text-center,
        .table-responsive-mobile td.text-right {
            text-align: left !important;
        }

        .table-responsive-mobile tfoot {
            display: none !important;
        }
    }

    /* 2 columnas: celular horizontal + tablet vertical */
    @media screen and (min-width: 601px) and (max-width: 1024px),
           (pointer: coarse) and (min-width: 601px) and (max-width: 1366px) {
        .table-responsive-mobile tbody {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    /* 3 columnas: tablet horizontal (solo táctil, escritorio conserva la tabla) */
    @media (pointer: coarse) and (min-width: 1025px) and (max-width: 1366px) {
        .table-responsive-mobile tbody {
            grid-template-columns: repeat(3, 1fr);
        }
    }

    /* Márgenes de la vista madre — celular (vertical y horizontal, < 768px) */
    @media screen and (max-width: 767.98px) {
        .content-body {
            margin-top: var(--tarjetas-mt-celular) !important;
            margin-right: var(--tarjetas-mx-celular) !important;
            margin-left: var(--tarjetas-mx-celular) !important;
        }
    }

    /* Márgenes de la vista madre — tablet (vertical y horizontal) */
    @media screen and (min-width: 768px) and (max-width: 1024px),
           (pointer: coarse) and (min-width: 768px) and (max-width: 1366px) {
        .content-body {
            margin-top: var(--tarjetas-mt-tablet) !important;
            margin-right: var(--tarjetas-mx-tablet) !important;
            margin-left: var(--tarjetas-mx-tablet) !important;
        }
    }
</style>

<script>
    // Componente para hacer tablas responsive en dispositivos móviles
    class OrientationManager {
        constructor() {
            this.init();
        }

    init() {
        this.setupTableResponsive();
    }

    setupTableResponsive() {
            const tables = document.querySelectorAll('table');
            tables.forEach(table => {
                // Agregar clase responsive
                if (!table.classList.contains('table-responsive-mobile')) {
                    table.classList.add('table-responsive-mobile');
                }

                // Agregar data-label a cada celda para mostrar el nombre de la columna
                const headers = table.querySelectorAll('thead th');
                const rows = table.querySelectorAll('tbody tr');

                rows.forEach(row => {
                    const cells = row.querySelectorAll('td');
                    cells.forEach((cell, index) => {
                        if (headers[index]) {
                            const headerText = headers[index].textContent.trim();
                            cell.setAttribute('data-label', headerText);
                        }
                    });
                });
            });
        }
    }

    // Inicializar cuando el DOM esté listo
    document.addEventListener('DOMContentLoaded', () => {
        new OrientationManager();
    });

    // También inicializar para navegación SPA si es necesario
    document.addEventListener('turbo:load', () => {
        new OrientationManager();
    });
</script>
