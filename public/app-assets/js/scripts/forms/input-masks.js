/*=========================================================================================
  File Name: input-masks.js
  Description: Centralized input mask configuration for form fields
  ----------------------------------------------------------------------------------------
  Gestor de Tareas - Input Masks Configuration
==========================================================================================*/

(function(window, document, $) {
  'use strict';

  /**
   * Formatea un número como moneda (USD)
   * Función centralizada para formateo de moneda en visualización
   * 
   * @param {number|string} value - Valor numérico a formatear
   * @returns {string} Valor formateado como moneda (ej: $1,499.99)
   */
  window.formatCurrency = function(value) {
    var num = Number(value || 0);
    try {
      return new Intl.NumberFormat(navigator.language || 'es-ES', {
        style: 'currency',
        currency: 'USD',
        minimumFractionDigits: 2
      }).format(num);
    } catch (e) {
      return num.toLocaleString(undefined, {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
      });
    }
  };

  /**
   * Formatea celdas TD con clase .td-currency
   * Aplica formato de moneda al contenido numérico de las celdas
   * 
   * @param {boolean} forceReformat - Si es true, reformatea incluso si ya fue formateado (útil para DataTables)
   */
  window.formatCurrencyCells = function(forceReformat) {
    $('td.td-currency').each(function() {
      var $cell = $(this);
      // Si forceReformat es true, remover el flag para permitir reformateo
      if (forceReformat) {
        $cell.removeData('currency-formatted');
      }
      // Solo formatear si no ha sido formateado ya (evitar duplicados)
      if (!$cell.data('currency-formatted')) {
        var originalValue = $cell.text().trim();
        // Remover cualquier formato existente para obtener el número puro
        var numericValue = originalValue.replace(/[^0-9.]/g, '');
        if (numericValue && !isNaN(numericValue)) {
          var formattedValue = window.formatCurrency(numericValue);
          $cell.text(formattedValue);
          $cell.data('currency-formatted', true);
        }
      }
    });
  };

  /**
   * Configuración de máscaras de moneda
   * Aplica formato de moneda (USD) a campos con clase .input-currency
   */
  function initCurrencyMasks() {
    if (typeof $.fn.inputmask !== 'undefined') {
      $('.input-currency').each(function() {
        var $input = $(this);
        
        // Solo inicializar si no tiene máscara ya aplicada
        if (!$input.data('inputmask')) {
          $input.inputmask({
            alias: 'currency',
            prefix: '$',
            groupSeparator: ',',
            radixPoint: '.',
            digits: 2,
            rightAlign: false,
            autoUnmask: true,
            removeMaskOnSubmit: true
          });
        }
      });
    }
  }

  /**
   * Limpia valores de máscara antes de enviar formularios
   * Remueve el formato de moneda para enviar solo valores numéricos
   */
  function setupFormSubmitHandlers() {
    $('form').on('submit', function(e) {
      var $form = $(this);
      
      // Limpiar valores de campos con máscara de moneda
      $form.find('.input-currency').each(function() {
        var $input = $(this);
        try {
          // Obtener el valor sin máscara usando la API de Inputmask
          var unmaskedValue = $input.inputmask('unmaskedvalue');
          if (unmaskedValue !== undefined && unmaskedValue !== '') {
            $input.val(unmaskedValue);
          }
        } catch (err) {
          // Si falla, intentar obtener el valor directamente
          var value = $input.val();
          // Remover caracteres no numéricos excepto punto decimal
          value = value.replace(/[^0-9.]/g, '');
          $input.val(value);
        }
      });
    });
  }

  /**
   * Inicialización cuando el documento está listo
   */
  $(document).ready(function() {
    // Inicializar máscaras de moneda para inputs
    initCurrencyMasks();
    
    // Formatear celdas TD con clase .td-currency
    formatCurrencyCells();
    
    // Configurar handlers para envío de formularios
    setupFormSubmitHandlers();
  });

  /**
   * Reinicializar máscaras después de cargar contenido dinámico
   * Útil para contenido cargado vía AJAX
   */
  $(document).on('DOMNodeInserted', function(e) {
    var $target = $(e.target);
    if ($target.find('.input-currency').length > 0 || $target.hasClass('input-currency')) {
      initCurrencyMasks();
    }
    if ($target.find('.td-currency').length > 0 || $target.hasClass('td-currency')) {
      formatCurrencyCells();
    }
  });

})(window, document, jQuery);

