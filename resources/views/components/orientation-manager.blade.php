{{-- Componente para hacer tablas responsive en dispositivos móviles --}}
<style>

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

        /* Tabla responsive */
        .table-responsive-mobile table,
        .table-responsive-mobile thead,
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

        .table-responsive-mobile tr {
            border: 0.5px solid #ededed;
            margin-bottom: 10px;
            border-radius: 8px;
            background: #f8fafc;
            padding: 8px;
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

        .table-responsive-mobile td.text-center {
            text-align: left !important;
        }

        .table-responsive-mobile td.text-right {
            text-align: left !important;
        }

        .table-responsive-mobile tfoot {
            display: none !important;
        }
    }

    /* Optimizaciones adicionales para pantallas muy pequeñas */
    @media screen and (max-width: 576px) {
        html body.navbar-sticky .app-content .content-wrapper {
            margin-top: 3rem;
            padding-top: 2.8rem;
            padding-bottom: 0;
        }

        .col-12 {
            padding-left: 0 !important;
            padding-right: 0 !important;
        }

        .card-header {
            padding: 10px 5px !important;
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
