/* ========================================
           DASHBOARD DE SEGURIDAD - VERSIÓN FINAL
           ======================================== */

// Variables globales para los gráficos
let riskChart = null;
let countryChart = null;

// Función principal de inicialización
document.addEventListener('DOMContentLoaded', function () {
    console.log('🚀 Dashboard de seguridad iniciando...');

    // Verificar que Chart.js esté disponible
    if (typeof Chart === 'undefined') {
        console.error('❌ Chart.js no está disponible');
        return;
    }

    console.log('✅ Chart.js disponible');

    // Limpiar cualquier gráfico existente
    cleanupExistingCharts();

    // Inicializar dashboard
    initializeDashboard();
});

function cleanupExistingCharts() {
    console.log('🧹 LIMPIEZA ULTRA-AGRESIVA de gráficos...');

    // 1. Destruir gráficos locales
    if (riskChart && typeof riskChart.destroy === 'function') {
        try {
            riskChart.destroy();
            console.log('✅ Gráfico de riesgo destruido');
        } catch (error) {
            console.warn('⚠️ Error destruyendo gráfico de riesgo:', error);
        }
        riskChart = null;
    }

    if (countryChart && typeof countryChart.destroy === 'function') {
        try {
            countryChart.destroy();
            console.log('✅ Gráfico de países destruido');
        } catch (error) {
            console.warn('⚠️ Error destruyendo gráfico de países:', error);
        }
        countryChart = null;
    }

    // 2. LIMPIEZA ULTRA-AGRESIVA - Destruir TODOS los gráficos de Chart.js
    try {
        // Método 1: Chart.instances
        if (Chart.instances && Chart.instances.length > 0) {
            console.log('🗑️ Destruyendo', Chart.instances.length, 'instancias de Chart.js');
            Chart.instances.forEach((chart, index) => {
                try {
                    if (chart && typeof chart.destroy === 'function') {
                        chart.destroy();
                        console.log(`✅ Instancia ${index} destruida`);
                    }
                } catch (error) {
                    console.warn(`⚠️ Error destruyendo instancia ${index}:`, error);
                }
            });
        }

        // Método 2: Chart.Chart.instances (versiones más nuevas)
        if (Chart.Chart && Chart.Chart.instances && Chart.Chart.instances.length > 0) {
            console.log('🗑️ Destruyendo', Chart.Chart.instances.length, 'instancias de Chart.Chart');
            Chart.Chart.instances.forEach((chart, index) => {
                try {
                    if (chart && typeof chart.destroy === 'function') {
                        chart.destroy();
                        console.log(`✅ Chart.Chart instancia ${index} destruida`);
                    }
                } catch (error) {
                    console.warn(`⚠️ Error destruyendo Chart.Chart instancia ${index}:`, error);
                }
            });
        }

        // Método 3: Chart.registry.controllers (versiones más nuevas)
        if (Chart.registry && Chart.registry.controllers && Chart.registry.controllers.size > 0) {
            console.log('🗑️ Destruyendo', Chart.registry.controllers.size, 'controladores del registro');
            Chart.registry.controllers.forEach((controller, id) => {
                try {
                    if (controller && typeof controller.destroy === 'function') {
                        controller.destroy();
                        console.log(`✅ Controlador ${id} destruido`);
                    }
                } catch (error) {
                    console.warn(`⚠️ Error destruyendo controlador ${id}:`, error);
                }
            });
        }

        // Método 4: Limpiar registros internos
        if (typeof Chart.registry.clear === 'function') {
            try {
                Chart.registry.clear();
                console.log('✅ Registro de Chart.js limpiado');
            } catch (error) {
                console.warn('⚠️ Error limpiando registro:', error);
            }
        }

        // Método 5: Buscar y destruir TODOS los canvas del DOM
        const allCanvases = document.querySelectorAll('canvas');
        console.log('🔍 Encontrados', allCanvases.length, 'canvas en el DOM');

        allCanvases.forEach((canvas, index) => {
            try {
                // Limpiar el contexto del canvas
                const ctx = canvas.getContext('2d');
                if (ctx) {
                    ctx.clearRect(0, 0, canvas.width, canvas.height);
                    console.log(`✅ Canvas ${index} limpiado`);
                }
            } catch (error) {
                console.warn(`⚠️ Error limpiando canvas ${index}:`, error);
            }
        });

        // Método 6: Forzar limpieza de memoria
        if (window.gc) {
            try {
                window.gc();
                console.log('✅ Garbage collector forzado');
            } catch (error) {
                console.log('ℹ️ Garbage collector no disponible');
            }
        }

    } catch (error) {
        console.error('❌ Error en limpieza ultra-agresiva:', error);
    }

    console.log('✅ LIMPIEZA ULTRA-AGRESIVA completada');
}

function initializeDashboard() {
    console.log('🔧 Inicializando dashboard...');

    // Buscar los canvas
    const riskCanvas = document.getElementById('riskDistributionChart');
    const countryCanvas = document.getElementById('threatsByCountryChart');

    console.log('🔍 Canvas encontrados:', {
        risk: riskCanvas,
        country: countryCanvas
    });

    // Crear gráficos solo si los canvas existen
    if (riskCanvas) {
        // Delay para asegurar que la limpieza se complete
        setTimeout(() => {
            createRiskChart(riskCanvas);
        }, 100);
    } else {
        console.warn('⚠️ Canvas de riesgo no encontrado');
    }

    if (countryCanvas) {
        // Delay para asegurar que la limpieza se complete
        setTimeout(() => {
            createCountryChart(countryCanvas);
        }, 200);
    } else {
        console.warn('⚠️ Canvas de países no encontrado');
    }

    // Cargar datos del servidor después de crear los gráficos
    setTimeout(() => {
        loadServerData();
    }, 300);
}

function createRiskChart(canvas) {
    console.log('🎨 Creando gráfico de riesgo...');

    // VERIFICACIÓN EXTRA: Asegurar que el canvas esté libre
    try {
        // Limpiar el canvas antes de crear el gráfico
        const ctx = canvas.getContext('2d');
        ctx.clearRect(0, 0, canvas.width, canvas.height);

        // Verificar que no haya gráficos activos en este canvas
        if (Chart.instances && Chart.instances.length > 0) {
            console.log('⚠️ Aún hay instancias activas, forzando limpieza...');
            cleanupExistingCharts();
        }

        // Crear el gráfico con ID único
        const chartId = 'riskChart_' + Date.now();
        console.log('🆔 Creando gráfico con ID:', chartId);

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

        console.log('✅ Gráfico de riesgo creado exitosamente');

    } catch (error) {
        console.error('❌ Error creando gráfico de riesgo:', error);
        console.error('❌ Stack trace:', error.stack);

        // Si falla, intentar limpiar y recrear
        try {
            console.log('🔄 Reintentando creación del gráfico...');
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

            console.log('✅ Gráfico de error creado como fallback');
        } catch (retryError) {
            console.error('❌ Error en reintento:', retryError);
        }
    }
}

function createCountryChart(canvas) {
    console.log('🎨 Creando gráfico de países...');

    // VERIFICACIÓN EXTRA: Asegurar que el canvas esté libre
    try {
        // Limpiar el canvas antes de crear el gráfico
        const ctx = canvas.getContext('2d');
        ctx.clearRect(0, 0, canvas.width, canvas.height);

        // Verificar que no haya gráficos activos en este canvas
        if (Chart.instances && Chart.instances.length > 0) {
            console.log('⚠️ Aún hay instancias activas, forzando limpieza...');
            cleanupExistingCharts();
        }

        // Crear el gráfico con ID único
        const chartId = 'countryChart_' + Date.now();
        console.log('🆔 Creando gráfico con ID:', chartId);

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

        console.log('✅ Gráfico de países creado exitosamente');

    } catch (error) {
        console.error('❌ Error creando gráfico de países:', error);
        console.error('❌ Stack trace:', error.stack);

        // Si falla, intentar limpiar y recrear
        try {
            console.log('🔄 Reintentando creación del gráfico...');
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

            console.log('✅ Gráfico de error creado como fallback');
        } catch (retryError) {
            console.error('❌ Error en reintento:', retryError);
        }
    }
}

function loadServerData() {
    console.log('📊 Cargando datos del servidor...');

    // Obtener datos desde los atributos data-* del HTML
    const container = document.querySelector('.container-fluid[data-risk-distribution][data-threats-by-country]');

    if (!container) {
        console.warn('⚠️ Contenedor con datos no encontrado');
        return;
    }

    try {
        // Obtener datos de riesgo
        const riskDataAttr = container.getAttribute('data-risk-distribution');
        console.log('🔍 Raw data-risk-distribution:', riskDataAttr);

        let riskData = [0, 0, 0]; // Solo 3 niveles: Crítico, Alto, Medio
        if (riskDataAttr && riskDataAttr !== 'null' && riskDataAttr !== 'undefined') {
            riskData = JSON.parse(riskDataAttr);
        }

        // Obtener datos de países
        const countryDataAttr = container.getAttribute('data-threats-by-country');
        console.log('🔍 Raw data-threats-by-country:', countryDataAttr);

        let countryData = {};
        if (countryDataAttr && countryDataAttr !== 'null' && countryDataAttr !== 'undefined') {
            countryData = JSON.parse(countryDataAttr);
        }

        console.log('🔍 Datos parseados:', {
            risk: riskData,
            country: countryData
        });

        // Actualizar gráficos con los datos
        updateRiskChart(riskData);
        updateCountryChart(countryData);

        console.log('✅ Datos cargados y gráficos actualizados');

    } catch (error) {
        console.error('❌ Error cargando datos:', error);
        console.error('❌ Stack trace:', error.stack);
    }
}

function updateRiskChart(data) {
    if (!riskChart) {
        console.warn('⚠️ Gráfico de riesgo no disponible');
        return;
    }

    console.log('🔄 Actualizando gráfico de riesgo con datos:', data);

    // Verificar que los datos sean válidos
    if (Array.isArray(data) && data.length === 3) { // Solo 3 niveles: Crítico, Alto, Medio
        riskChart.data.datasets[0].data = data;
        riskChart.update();
        console.log('✅ Gráfico de riesgo actualizado');
    } else {
        console.warn('⚠️ Datos de riesgo inválidos - deben ser 3 niveles:', data);
    }
}

function updateCountryChart(data) {
    if (!countryChart) {
        console.warn('⚠️ Gráfico de países no disponible');
        return;
    }

    console.log('🔄 Actualizando gráfico de países con datos:', data);

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
        console.log('✅ Gráfico de países actualizado');
    } else {
        console.warn('⚠️ Datos de países inválidos o vacíos:', data);
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
    console.log('🚨 ACTIVANDO MODO DE EMERGENCIA - Eliminando cálculos complejos');

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

        console.log('✅ Modo de emergencia activado - Gráficos simples creados');

    } catch (error) {
        console.error('❌ Error en modo de emergencia:', error);
    }
};

// Función para debugging - mostrar estado actual
window.debugSecurityDashboard = function () {
    console.log('🔍 Estado del dashboard:', {
        riskChart: riskChart,
        countryChart: countryChart,
        container: document.querySelector('.container-fluid[data-risk-distribution][data-threats-by-country]'),
        riskData: document.querySelector('.container-fluid[data-risk-distribution][data-threats-by-country]')?.getAttribute('data-risk-distribution'),
        countryData: document.querySelector('.container-fluid[data-risk-distribution][data-threats-by-country]')?.getAttribute('data-threats-by-country')
    });

    console.log('🚨 Si todo falla, usa: window.emergencyMode()');
};

console.log('✅ Script de dashboard de seguridad cargado');
console.log('💡 Usa window.debugSecurityDashboard() para debugging');
