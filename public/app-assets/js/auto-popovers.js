// ===== AUTO-INICIALIZACIÓN DE POPOVERS =====
// Este script se ejecuta automáticamente cuando se carga la página

(function () {
    'use strict';

    // Función para inicializar popovers
    function initializePopovers() {
        // Verificar que jQuery y Bootstrap estén disponibles
        if (typeof $ === 'undefined' || typeof $.fn.popover === 'undefined') {
            console.log('Esperando jQuery y Bootstrap...');
            setTimeout(initializePopovers, 100);
            return;
        }

        // Buscar elementos con popovers
        var popoverElements = document.querySelectorAll('[data-toggle="popover"]');

        if (popoverElements.length === 0) {
            console.log('No se encontraron elementos con popover');
            return;
        }

        console.log('Inicializando', popoverElements.length, 'popovers...');

        // Inicializar cada popover
        popoverElements.forEach(function (element) {
            var $element = $(element);

            // Evitar inicializar popovers ya inicializados
            if ($element.data('bs.popover')) {
                return;
            }

            var content = element.getAttribute('data-content') || element.getAttribute('data-title');

            // Determinar el tipo de popover basado en el contenido
            var popoverClass = '';
            if (content && (content.includes('Eliminar') || content.includes('Desactivar'))) {
                popoverClass = 'popover-danger';
            } else if (content && (content.includes('Editar') || content.includes('Actualizar'))) {
                popoverClass = 'popover-warning';
            } else if (content && (content.includes('Activar') || content.includes('Asignar'))) {
                popoverClass = 'popover-success';
            } else if (content && (content.includes('Equipos') || content.includes('Roles'))) {
                popoverClass = 'popover-primary';
            }

            try {
                $element.popover({
                    html: true,
                    container: 'body',
                    trigger: 'hover',
                    template: '<div class="popover ' + popoverClass + '" role="tooltip"><div class="arrow"></div><div class="popover-body"></div></div>'
                });

                console.log('Popover inicializado:', content);
            } catch (error) {
                console.error('Error al inicializar popover:', error);
            }
        });
    }

    // Función para verificar si la página está completamente cargada
    function isPageReady() {
        return document.readyState === 'complete' &&
            typeof $ !== 'undefined' &&
            typeof $.fn.popover !== 'undefined';
    }

    // Función principal de inicialización
    function main() {
        if (isPageReady()) {
            // Inicialización inmediata
            initializePopovers();

            // Reinicialización después de DataTables
            setTimeout(initializePopovers, 500);
            setTimeout(initializePopovers, 1000);
            setTimeout(initializePopovers, 2000);

            // Observar cambios en el DOM para elementos que se cargan dinámicamente
            if (typeof MutationObserver !== 'undefined') {
                var observer = new MutationObserver(function (mutations) {
                    mutations.forEach(function (mutation) {
                        if (mutation.type === 'childList' && mutation.addedNodes.length > 0) {
                            setTimeout(initializePopovers, 100);
                        }
                    });
                });

                observer.observe(document.body, {
                    childList: true,
                    subtree: true
                });
            }
        } else {
            // Esperar a que la página esté lista
            setTimeout(main, 100);
        }
    }

    // Iniciar cuando el DOM esté listo
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', main);
    } else {
        main();
    }

    // También iniciar cuando la ventana esté completamente cargada
    window.addEventListener('load', function () {
        setTimeout(initializePopovers, 100);
    });

})();
