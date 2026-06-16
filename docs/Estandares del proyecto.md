🟠 Stack:
    PHP 8.1
    MySQL 8.0
    Laravel Framework 10.50.2

🟠 Comando para importar de manera masiva los datos del usuario
    php artisan db:importar "NombreOficina"

🟠 Resolviendo el bloqueo de github con ssh
    1. Habilitar e iniciar ssh-agent	✅
    2. Generar llave SSH ed25519	✅
    3. Agregar llave al agente SSH	✅
    4. Registrar llave pública en GitHub	✅
    5. Verificar autenticación SSH	✅
    6. Cambiar remote de HTTPS → SSH	✅

🟠 Para controlas las sugerencias de la IA en.vscode/settings.json:
{
    "git.autofetch": true,
    "editor.quickSuggestionsDelay": 30000,
    "editor.suggest.showWords": false,
    "editor.suggest.filterGraceful": false,
    "editor.suggest.filterOnType": false,
    "editor.suggest.showSnippets": false,
    "editor.suggest.showStatusBar": false,
    "antigravity.suggestionFrequency": 0,
    "antigravity.suggestionThrottle": 30000
}
Además se revisó: preferences/editor settings/tab user/text editor/suggestions

🟠 https://chat.z.ai/ nueva IA

🟠 La librería que domina el tamaño de letra en el modulo de ventas es:
app-assets/css/custom-zay.css (Línea 38)

🟠 Inicializar la validación frontend en las vistas
@section('js')
<script>
    $("input,select,textarea").not("[type=submit]").jqBootstrapValidation(); //Cuando la seccion js no está vacía se requiere pues cuando está vacía toma la inicialización global en dashboard
    ... (resto del contenido js)...
<script>

🟠 Uso de formato de moneda: public/app-assets/js/helpers.js
<p class="form-control-plaintext mb-0">${formatCurrency(producto.precio)}</p>

🟠 COLORES DE ALERTAS
primary
secondary
success
info
warning
danger

//COLORES DEL PROYECTO GESTOR-DE-TAREAS:
Texto: text-* / text-*-light
Fondo: bg-*
Bordes: border border-*
Botones: btn-*

Texto: text-*-dark
Fondo: bg-*-dark
Bordes: border border-*-dark

Texto: text-*-light
Fondo: bg-*-light
Bordes: border border-*-light

🟠 Soporte Digital Ocean:
    https://docs.digitalocean.com/support/
    https://www.digitalocean.com/company/contact/sales?referrer=pricing/managed-databases
    https://www.digitalocean.com/community/tools/bandwidth?active=%5B%7B%22slug%22%3A%22s-1vcpu-2gb%22%2C%22type%22%3A%22droplet%22%2C%22hours%22%3A722%2C%22consumption%22%3A1500%2C%22nodes%22%3A1%7D%5D&additional=0

🟠 Analisis de la factura digital ocean:
    DigitalOcean:
        ¿Cómo funciona la transferencia gratuita?
        ✅ NO es solo para el mes de gratuidad:
        La transferencia gratuita es PERMANENTE en tu plan
        Cada mes tienes derecho a 1366 GB de transferencia gratuita
        No se acaba después del mes de prueba
        ✅ Es parte de tu plan pagado:
        Tu factura de $6.05 incluye la transferencia gratuita
        No pagas extra por usar hasta 1366 GB mensuales
        Solo pagas si excedes ese límite
        ¿Qué incluye tu factura de $6.05?
        Servicios que pagas:
        Droplet (servidor): ~$15-30/mes
        Base de datos MySQL: ~$15-30/mes
        Otros servicios que tengas activos
        Transferencia de datos:
        0.29 GB usado: GRATIS (está dentro del límite)
        Hasta 1366 GB: GRATIS cada mes
        Solo pagas si excedes 1366 GB
        Conclusión:
            La transferencia gratuita es PERMANENTE y real. Cada mes tienes derecho a 1366 GB sin costo adicional. Tu factura de $6.05 es por los servicios, no por la transferencia de datos.
            Tu módulo de seguridad puede usarse sin límites mensuales de transferencia.


🟠 Convirtiendo imágenes png -> svg
    https://convertio.co/es/download/380b8906ab23e01e9af660b5c737a288eb7029/


