/**
 * Formatea un número como moneda (Configuración centralizada: USD, en-US).
 * @param {number|string} value - El valor a formatear.
 * @returns {string} El valor formateado.
 */
function formatCurrency(value) {
    // Configuración centralizada
    const currency = 'USD';
    const locale = 'en-US';

    if (value === null || value === undefined || value === '') {
        return '';
    }
    return parseFloat(value).toLocaleString(locale, {
        style: 'currency',
        currency: currency
    });
}
