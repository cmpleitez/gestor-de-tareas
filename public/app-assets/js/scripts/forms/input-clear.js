/*=========================================================================================
  File Name: input-clear.js
  Description: Script para agregar botón de limpiar campo en inputs
  ----------------------------------------------------------------------------------------
  Author: Sistema
==========================================================================================*/

(function (window, document, $) {
    'use strict';

    $(document).ready(function () {
        // Función para inicializar botones de limpiar en campos con data-clear="true"
        function initInputClear() {
            $('input[data-clear="true"]').each(function () {
                var $input = $(this);

                // Si ya tiene el botón, no hacer nada
                if ($input.siblings('.input-clear-btn').length > 0) {
                    return;
                }

                // Si el input no está dentro de un wrapper, envolverlo
                if (!$input.parent().hasClass('input-clear-wrapper')) {
                    $input.wrap('<div class="input-clear-wrapper"></div>');
                }

                // Crear botón de limpiar elegante
                var $btnClear = $('<button class="input-clear-btn" type="button" title="Limpiar campo">' +
                    '<i class="bx bx-x"></i>' +
                    '</button>');

                // Agregar botón después del input
                $input.after($btnClear);

                // Mostrar/ocultar botón según el contenido
                function toggleClearButton() {
                    if ($input.val().length > 0) {
                        $btnClear.fadeIn(150);
                    } else {
                        $btnClear.fadeOut(150);
                    }
                }

                // Verificar al cargar
                toggleClearButton();

                // Verificar al escribir
                $input.on('input keyup paste', function () {
                    toggleClearButton();
                });

                // Limpiar campo al hacer clic
                $btnClear.on('click', function (e) {
                    e.preventDefault();
                    e.stopPropagation();
                    $input.val('').focus();
                    $btnClear.fadeOut(150);
                    // Limpiar clases de error de validación si existen
                    $input.removeClass('is-invalid');
                    $input.parent().siblings('.help-block').remove();
                    // Trigger event para que otras validaciones se actualicen
                    $input.trigger('change');
                });
            });
        }

        // Inicializar
        initInputClear();

        // Reinicializar solo para inputs nuevos agregados dinámicamente usando MutationObserver
        var observer = new MutationObserver(function (mutations) {
            var hasNewInputs = false;
            mutations.forEach(function (mutation) {
                mutation.addedNodes.forEach(function (node) {
                    if (node.nodeType === 1) { // Element node
                        // Verificar si el nodo agregado es un input con data-clear o contiene uno
                        if ($(node).is('input[data-clear="true"]') ||
                            $(node).find('input[data-clear="true"]').length > 0) {
                            hasNewInputs = true;
                        }
                    }
                });
            });
            if (hasNewInputs) {
                initInputClear();
            }
        });

        // Observar cambios en el body
        observer.observe(document.body, {
            childList: true,
            subtree: true
        });
    });

})(window, document, jQuery);

