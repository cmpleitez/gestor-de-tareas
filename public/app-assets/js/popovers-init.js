// ===== INICIALIZACIÓN DE POPOVERS =====

$(document).ready(function () {
    // Función para inicializar popovers
    function initializePopovers() {
        $('[data-toggle="popover"]').each(function () {
            var $this = $(this);

            // Evitar inicializar popovers ya inicializados
            if ($this.data('bs.popover')) {
                return;
            }

            var content = $this.attr('data-content') || $this.attr('data-title');

            // Determinar el tipo de popover basado en el contenido
            var popoverClass = '';
            if (content.includes('Eliminar') || content.includes('Desactivar')) {
                popoverClass = 'popover-danger';
            } else if (content.includes('Editar') || content.includes('Actualizar')) {
                popoverClass = 'popover-warning';
            } else if (content.includes('Activar') || content.includes('Asignar')) {
                popoverClass = 'popover-success';
            } else if (content.includes('Equipos') || content.includes('Roles')) {
                popoverClass = 'popover-primary';
            }

            $this.popover({
                html: true,
                container: 'body',
                trigger: 'hover',
                template: '<div class="popover ' + popoverClass + '" role="tooltip"><div class="arrow"></div><div class="popover-body"></div></div>'
            });
        });
    }

    // Inicialización inmediata
    initializePopovers();

    // Reinicialización después de DataTables (más rápido)
    setTimeout(initializePopovers, 500);

    // Reinicialización final por si acaso
    setTimeout(initializePopovers, 1000);

    // Reinicialización adicional para elementos que se cargan dinámicamente
    setTimeout(initializePopovers, 2000);
});
