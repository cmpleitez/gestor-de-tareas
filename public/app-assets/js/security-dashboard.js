/* ========================================
           DASHBOARD DE SEGURIDAD - VERSIÓN FINAL
           ======================================== */

// Variables globales para los gráficos
let riskChart = null;
let countryChart = null;

// Función principal de inicialización
document.addEventListener('DOMContentLoaded', function () {
    // Verificar que Chart.js esté disponible
    if (typeof Chart === 'undefined') {
        return;
    }

    // Limpiar cualquier gráfico existente
    cleanupExistingCharts();

    // Inicializar dashboard
    initializeDashboard();
});

function cleanupExistingCharts() {
    // Verificar que Chart.js esté disponible
    if (typeof Chart === 'undefined') {
        return;
    }

    // 1. Destruir gráficos locales
    if (riskChart && typeof riskChart.destroy === 'function') {
        try {
            riskChart.destroy();
        } catch (error) {
            // Error silencioso
        }
        riskChart = null;
    }

    if (countryChart && typeof countryChart.destroy === 'function') {
        try {
            countryChart.destroy();
        } catch (error) {
            // Error silencioso
        }
        countryChart = null;
    }

    // 2. LIMPIEZA ULTRA-AGRESIVA - Destruir TODOS los gráficos de Chart.js
    try {
        // Método 1: Chart.instances
        if (Chart.instances && Array.isArray(Chart.instances) && Chart.instances.length > 0) {
            Chart.instances.forEach((chart, index) => {
                try {
                    if (chart && typeof chart.destroy === 'function') {
                        chart.destroy();
                    }
                } catch (error) {
                    // Error silencioso
                }
            });
        }

        // Método 2: Chart.Chart.instances (versiones más nuevas)
        if (Chart.Chart && Chart.Chart.instances && Array.isArray(Chart.Chart.instances) && Chart.Chart.instances.length > 0) {
            Chart.Chart.instances.forEach((chart, index) => {
                try {
                    if (chart && typeof chart.destroy === 'function') {
                        chart.destroy();
                    }
                } catch (error) {
                    // Error silencioso
                }
            });
        }

        // Método 3: Chart.registry.controllers (versiones más nuevas)
        if (Chart.registry && Chart.registry.controllers && typeof Chart.registry.controllers.forEach === 'function' && Chart.registry.controllers.size > 0) {
            Chart.registry.controllers.forEach((controller, id) => {
                try {
                    if (controller && typeof controller.destroy === 'function') {
                        controller.destroy();
                    }
                } catch (error) {
                    // Error silencioso
                }
            });
        }

        // Método 4: Limpiar registros internos
        if (Chart.registry && typeof Chart.registry.clear === 'function') {
            try {
                Chart.registry.clear();
            } catch (error) {
                // Error silencioso
            }
        }

        // Método 5: Buscar y destruir TODOS los canvas del DOM
        const allCanvases = document.querySelectorAll('canvas');

        allCanvases.forEach((canvas, index) => {
            try {
                // Limpiar el contexto del canvas
                const ctx = canvas.getContext('2d');
                if (ctx) {
                    ctx.clearRect(0, 0, canvas.width, canvas.height);
                }
            } catch (error) {
                // Error silencioso
            }
        });

        // Método 6: Forzar limpieza de memoria
        if (window.gc) {
            try {
                window.gc();
            } catch (error) {
                // Garbage collector no disponible
            }
        }

    } catch (error) {
        // Error silencioso en limpieza
    }
}

function initializeDashboard() {
    // Buscar los canvas
    const riskCanvas = document.getElementById('riskDistributionChart');
    const countryCanvas = document.getElementById('threatsByCountryChart');

    // Crear gráficos solo si los canvas existen
    if (riskCanvas) {
        // Delay para asegurar que la limpieza se complete
        setTimeout(() => {
            createRiskChart(riskCanvas);
        }, 100);
    }

    if (countryCanvas) {
        // Delay para asegurar que la limpieza se complete
        setTimeout(() => {
            createCountryChart(countryCanvas);
        }, 200);
    }

    // Cargar datos del servidor después de crear los gráficos
    setTimeout(() => {
        loadServerData();
    }, 300);
}

function createRiskChart(canvas) {
    // VERIFICACIÓN EXTRA: Asegurar que el canvas esté libre
    try {
        // Limpiar el canvas antes de crear el gráfico
        const ctx = canvas.getContext('2d');
        ctx.clearRect(0, 0, canvas.width, canvas.height);

        // Verificar que no haya gráficos activos en este canvas
        if (Chart.instances && Chart.instances.length > 0) {
            cleanupExistingCharts();
        }

        // Crear el gráfico con ID único
        const chartId = 'riskChart_' + Date.now();

        riskChart = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['Crítico', 'Alto', 'Medio'],
                datasets: [{
                    data: [0, 0, 0],
                    backgroundColor: [
                        '#e74a3b', // Crítico - Rojo
                        '#f6c23e', // Alto - Amarillo
                        '#fd7e14'  // Medio - Naranja
                    ],
                    borderWidth: 2,
                    borderColor: '#fff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '50%', // Agujero central más grande para mejor proporción
                plugins: {
                    legend: {
                        display: false // Sin leyenda, solo tooltips
                    },
                    tooltip: {
                        enabled: true,
                        backgroundColor: 'rgba(0,0,0,0.8)',
                        titleColor: '#fff',
                        bodyColor: '#fff',
                        titleFont: {
                            size: 14
                        },
                        bodyFont: {
                            size: 12
                        },
                        callbacks: {
                            label: function (context) {
                                return context.label + ': ' + context.parsed;
                            }
                        }
                    }
                }
            }
        });

    } catch (error) {
        // Si falla, intentar limpiar y recrear
        try {
            cleanupExistingCharts();

            const ctx = canvas.getContext('2d');
            ctx.clearRect(0, 0, canvas.width, canvas.height);

            riskChart = new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: ['Error'],
                    datasets: [{
                        data: [1],
                        backgroundColor: ['#dc3545']
                    }]
                },
                options: {
                    plugins: {
                        legend: {
                            display: false // Sin leyenda
                        }
                    }
                }
            });

        } catch (retryError) {
            // Error en reintento
        }
    }
}

function createCountryChart(canvas) {
    // VERIFICACIÓN EXTRA: Asegurar que el canvas esté libre
    try {
        // Limpiar el canvas antes de crear el gráfico
        const ctx = canvas.getContext('2d');
        ctx.clearRect(0, 0, canvas.width, canvas.height);

        // Verificar que no haya gráficos activos en este canvas
        if (Chart.instances && Chart.instances.length > 0) {
            cleanupExistingCharts();
        }

        // Crear el gráfico con ID único
        const chartId = 'countryChart_' + Date.now();

        countryChart = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['Sin datos'],
                datasets: [{
                    data: [1],
                    backgroundColor: ['#e3e6f0'],
                    borderWidth: 2,
                    borderColor: '#fff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '50%', // Agujero central más grande para mejor proporción
                plugins: {
                    legend: {
                        display: false // Sin leyenda, solo tooltips
                    },
                    tooltip: {
                        enabled: true,
                        backgroundColor: 'rgba(0,0,0,0.8)',
                        titleColor: '#fff',
                        bodyColor: '#fff',
                        titleFont: {
                            size: 14
                        },
                        bodyFont: {
                            size: 12
                        },
                        callbacks: {
                            label: function (context) {
                                return context.label + ': ' + context.parsed + ' amenazas';
                            }
                        }
                    }
                }
            }
        });

    } catch (error) {
        // Si falla, intentar limpiar y recrear
        try {
            cleanupExistingCharts();

            const ctx = canvas.getContext('2d');
            ctx.clearRect(0, 0, canvas.width, canvas.height);

            countryChart = new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: ['Error'],
                    datasets: [{
                        data: [1],
                        backgroundColor: ['#dc3545']
                    }]
                },
                options: {
                    plugins: {
                        legend: {
                            display: false // Sin leyenda
                        }
                    }
                }
            });

        } catch (retryError) {
            // Error en reintento
        }
    }
}

function loadServerData() {
    // Obtener datos desde los atributos data-* del HTML
    const container = document.querySelector('.container-fluid[data-risk-distribution][data-threats-by-country]');

    if (!container) {
        return;
    }

    try {
        // Obtener datos de riesgo
        const riskDataAttr = container.getAttribute('data-risk-distribution');

        let riskData = [0, 0, 0]; // Solo 3 niveles: Crítico, Alto, Medio
        if (riskDataAttr && riskDataAttr !== 'null' && riskDataAttr !== 'undefined') {
            riskData = JSON.parse(riskDataAttr);
        }

        // Obtener datos de países
        const countryDataAttr = container.getAttribute('data-threats-by-country');

        let countryData = {};
        if (countryDataAttr && countryDataAttr !== 'null' && countryDataAttr !== 'undefined') {
            countryData = JSON.parse(countryDataAttr);
        }

        // Actualizar gráficos con los datos
        updateRiskChart(riskData);
        updateCountryChart(countryData);

    } catch (error) {
        // Error silencioso al cargar datos
    }
}

function updateRiskChart(data) {
    if (!riskChart) {
        return;
    }

    // Verificar que los datos sean válidos
    if (Array.isArray(data) && data.length === 3) { // Solo 3 niveles: Crítico, Alto, Medio
        riskChart.data.datasets[0].data = data;
        riskChart.update();
    }
}

function updateCountryChart(data) {
    if (!countryChart) {
        return;
    }

    // Verificar que los datos sean válidos
    if (data && typeof data === 'object' && Object.keys(data).length > 0) {
        const countries = Object.keys(data);
        const counts = countries.map(country => data[country].count || 0);

        countryChart.data.labels = countries;
        countryChart.data.datasets[0].data = counts;

        // Generar colores dinámicamente
        const colors = ['#e74a3b', '#f6c23e', '#fd7e14', '#20c9a6', '#6c757d', '#17a2b8', '#6f42c1', '#28a745', '#dc3545'];
        countryChart.data.datasets[0].backgroundColor = countries.map((_, index) => colors[index % colors.length]);

        countryChart.update();
    } else {
        // Mostrar estado vacío
        countryChart.data.labels = ['Sin datos'];
        countryChart.data.datasets[0].data = [1];
        countryChart.data.datasets[0].backgroundColor = ['#e3e6f0'];
        countryChart.update();
    }
}

// Función para limpiar gráficos al salir de la página
window.addEventListener('beforeunload', function () {
    cleanupExistingCharts();
});

// Función de emergencia - Si todo falla, eliminar cálculos complejos
window.emergencyMode = function () {
    try {
        // Limpiar todo
        cleanupExistingCharts();

        // Crear gráficos simples sin datos
        const riskCanvas = document.getElementById('riskDistributionChart');
        const countryCanvas = document.getElementById('threatsByCountryChart');

        if (riskCanvas) {
            const ctx = riskCanvas.getContext('2d');
            ctx.clearRect(0, 0, riskCanvas.width, riskCanvas.height);

            // Gráfico simple sin datos
            riskChart = new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: ['Sin datos'],
                    datasets: [{
                        data: [1],
                        backgroundColor: ['#e3e6f0']
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false // Sin leyenda
                        }
                    }
                }
            });
        }

        if (countryCanvas) {
            const ctx = countryCanvas.getContext('2d');
            ctx.clearRect(0, 0, countryCanvas.width, countryCanvas.height);

            // Gráfico simple sin datos
            countryChart = new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: ['Sin datos'],
                    datasets: [{
                        data: [1],
                        backgroundColor: ['#e3e6f0']
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false // Sin leyenda
                        }
                    }
                }
            });
        }

    } catch (error) {
        // Error silencioso en modo de emergencia
    }
};

// Función para debugging - mostrar estado actual
window.debugSecurityDashboard = function () {
    return {
        riskChart: riskChart,
        countryChart: countryChart,
        container: document.querySelector('.container-fluid[data-risk-distribution][data-threats-by-country]'),
        riskData: document.querySelector('.container-fluid[data-risk-distribution][data-threats-by-country]')?.getAttribute('data-risk-distribution'),
        countryData: document.querySelector('.container-fluid[data-risk-distribution][data-threats-by-country]')?.getAttribute('data-threats-by-country')
    };
};
