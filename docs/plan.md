🟠 PAUTAS:
    . Duis validos:
        012345678
        023456783
        123456784
        234567890
        456789012
    . Tasas de refresco: Prueba de 40 minutos de estres
        Nuevas recibidas: 60 segundos
        Actualizar avance: 60 segundos
        Notificaciones limewire: 60 segundos
    . Uso interno en primera fase y uso externo en seguda fase para ambos Mostro y Dobinsons, se cumple que se usa el mismo procedimiento

PROCEDIMIENTO: perfil cpleitez.2024@gmail.com https://miro.com/app/board/uXjVJy6iRzE=/
        ✨. Cliente: Entra a la <Tienda>, selecciona en el catálogo el kit y da clic en <agregar al carrito>
            . Sistema: En respuesta al cliente (<recepcion.user_id_origen>), crea el registro de <Atención>, el detalle de la <Atencion>,
            y <copia solicitud receptor>; pendiente de validar por el mismo (<recepcion.validada_origen = false>)
            y pendiente de validar (<recepcion.validada_destino=false>) por el receptor (<recepcion.user_id_destino>)

        ✨. Cliente:
            . Entra al <formulario del carrito> y agrega o cambia los <kits y sus productos>
            . Sistema: En atención a la orden (<Enviar>), del cliente:
                . Crea a partir del <formulario del carrito> una <copia solicitud receptor>
                y crea un número de <Atención>, una <orden de compra/reserva> con sus <kits> y <productos>
                . Desactiva la <escritura> (<recepcion.validada_origen = true>) para el cliente (<recepcion.user_id_origen>) de la <copia solicitud receptor>
                . Activa la escritura (<recepcion.validada_destino = false>) para el receptor (<recepcion.user_id_destino>) en la <copia solicitud receptor>
                . Establece <copia solicitud receptor> en estado de <Recibida>

        ✨. Receptor: Entra al servicio <Mis ordenes de compra>
            . Sistema: Brinda la vista de tableros <kanvan> con <autorefrescado> en intervalos de N minutos

        ✨. Receptor:
            . Prioriza la solicitud y la arrastra al tablero de En progreso
                . Sistema: Crea <copia solicitud operador>
            . Ubica la solicitud en el tablero de <En progreso> y selecciona la solicitud a revisar <copia solicitud receptor>, dando clic sobre ella
                . Sistema: Muestra en el <sidebar> el número de gestión y las tareas asignadas para dicha solicitud.
            . Corrige la <orden de compra>
                . revisa las unidades solicitadas, los items que conformarán la orden de compra y Revisa <stocks digitales>

        ✨. Operador: Entra al servicio <Mis ordenes de compra>
                . Sistema: Brinda la vista <kanvan> con <autorefrescado> en intervalos de 5 minutos

        ✨. Operador:
            . En el tablero de <recibidas> ubica visualmente la <copia solicitud operador> que va procesar y la arrastra hacia el tablero "En Progreso"
            . Ubica la <copia solicitud operador> en el tablero "En Progreso" y da clic sobre ella
                . Sistema: Muestra en el sidebar las tareas asignadas al operador

        ✨. Operador:
            . Da clic sobre su única tarea
                . Sistema: Abre la edición del carrito mostrando todas las ordenes y sus items
            . Revisa para cada item el stock físico
                . Confirma el stock físico: hay o no hay stock físico
            . Clic al boton Revisar

        ✨. Receptor:
            . Revisa las verificaciones de stock físico en la orden de compra
            . Clic a control Revisar
                . Sistema: Envía un correo al cliente informando que la orden de compra ha sido revisada

        ✨. Cliente:
            . Confirma telefonicamente que la orden esta bien


        ✨. Receptor:
            . Procesa el resto de las las tareas asignadas
            . Sistema:
                . En cuanto el receptor y el operador han realizado todas las tareas, establece el estado de "Resuelta" a todas las copias de la solicitud
                y a su solicitud principal <Atencion>, en consecuencia la solicitud se coloca en el tablero de resueltas.

🟠 SEGURIDAD:

    . Antes de activar el servicio externo, se deben asegurar los nombres de las rutas
    . El uso de los permisos muta a usan can aplicando un solo permiso ya no dos anidados y ya no usar el rol, cada permiso es específico y único
    . Enmascarar las rutas


    A) Fuera del estándar

        1. Doble can anidado en todas las rutas administrativas (routes/web.php:51-138). El estándar pide un solo permiso atómico por ruta. Hoy se hace can:administrar en el grupo + can:ver|crear|editar|... en cada
        acción. Mismo patrón en can:gestionar (routes/web.php:141-162) y can:tienda (routes/web.php:165-179).
        2. Permisos macro contradiciendo "cada permiso específico y único". administrar, gestionar y tienda son agrupadores de módulo, no permisos atómicos. El estándar dice no usarlos como capa adicional.
        3. URIs administrativas sin enmascarar — siguen en español y exponen el modelo: /user, /marca, /modelo, /tipo, /producto, /kit, /solicitud, /parametro. Solo recepcion.* y tienda.* cumplen la pauta de
        enmascaramiento (teams, operators, next-step, purchase-order, fix-request, etc.).
        4. Bypass global superadmin vía Gate::before (app/Providers/AuthServiceProvider.php:25-27). El estándar declara "ya no usar el rol" — este bypass usa rol, no permiso, y devuelve true para cualquier ability.
        5. Asignación de rol case-sensitive inconsistente. Actions/Fortify/CreateNewUser.php:53 asigna 'Cliente', Auth/RegisterController.php:86 asigna 'cliente', y UserSeeder.php:100 crea solo 'cliente'. Spatie es
        case-sensitive, así que un camino crea/usa un rol distinto al sembrado.
        6. Permiso autorizar huérfano — sembrado en UserSeeder.php:68 y asignado al rol admin, pero ninguna ruta lo usa (grep can:autorizar = 0).
        7. Roles huérfanos — supervisor y gestor se crean en UserSeeder.php:143-144 sin permisos y sin uso en código.
        8. Comparación whereNotIn('id', ['1']) con string (UserController.php:23) para ocultar al superadmin: filtro frágil, debería excluirse por rol/permiso, no por id literal.
        9. Aliases Spatie registrados pero no usados — role, permission, role_or_permission están en Kernel.php:67-69 y nunca aparecen en rutas; añade superficie de API que el equipo podría usar accidentalmente
        saltándose el patrón can:*.

    B) Faltante o comentado

        1. PerformanceOptimization no está registrado (app/Http/Middleware/PerformanceOptimization.php). Define X-Content-Type-Options, X-Frame-Options: SAMEORIGIN, X-XSS-Protection — no se ejecutan porque el
        middleware no aparece en Kernel.php $middleware, $middlewareGroups ni $middlewareAliases.
        2. Falta Strict-Transport-Security, Content-Security-Policy, Referrer-Policy, Permissions-Policy. Ni siquiera PerformanceOptimization los contempla.
        3. Sanctum stateful comentado — \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class está comentado en app/Http/Kernel.php:42. El grupo api queda sin sesión stateful para SPA/Livewire
        que use Sanctum.
        4. TrustHosts comentado (app/Http/Kernel.php:16). En producción tras proxy/CDN debería estar activo para prevenir Host header injection.
        5. Throttling ausente fuera de auth. Solo verification.send (throttle:6,1) está limitado. Login/register/password-reset usan los defaults de Fortify, pero no hay throttle: en rutas de carrito, asignación,
        descarga de stock ni en endpoints de autorefresco que se disparan cada 15-60 s.
        6. app/Policies/ no existe. Toda la autorización es por can: + Spatie Permission; faltan policies por modelo (AtencionPolicy, RecepcionPolicy, OrdenPolicy) que permitirían validar pertenencia (ej.: que un
        receptor solo vea sus recepciones, que un cliente no acceda a la Atencion de otro).
        7. Sin scoping multi-oficina explícito. oficina_id existe en muchas tablas, pero no hay global scope ni middleware que filtre por la oficina del usuario autenticado. La "corrección de hardcoreo del
        oficina_id" del commit 22042df7 sugiere que es un punto recurrente.
        8. Componentes <x-security.*>  documentados pero no implementados. docs/componentes-seguridad.md describe metric-card, chart-card, recent-events, suspicious-ips, dashboard-header, empty-state, risk-badge,
        notification-system, js-utilities, security-styles. No existe resources/views/components/security/. Solo public/app-assets/css/security-dashboard.css y .js.
        9. Sin dashboard ni controlador de seguridad. No hay SecurityController, ni rutas security.*, ni log/auditoría de eventos de seguridad (intentos fallidos, IPs sospechosas, threat scores) que justificarían
        los componentes anteriores.
        10. Sin tests de autorización del dominio. tests/Feature solo trae los de Jetstream (auth, 2FA, perfil, API tokens). No hay tests que verifiquen que cliente no pueda llegar a administrar, ni que operador no
        pueda confirmar-pago, ni la matriz rol→permiso del seeder.
        11. CSP/Mix de assets externos sin integridad. public/app-assets/ carga jQuery, Bootstrap, DataTables, FontAwesome locales (bien), pero no hay configuración de SRI ni nonce para scripts inline.
        12. Sesión sin endurecer. .env.example usa SESSION_DRIVER=database y SESSION_LIFETIME=120 por defecto; no se ven flags SESSION_SECURE_COOKIE=true, SESSION_SAME_SITE=strict, ni rotación al login.
        13. Recovery codes / 2FA opcional. Fortify lo expone pero no se fuerza para roles privilegiados (superadmin, admin).


🟢 SERVICIOS:
    
    ... Adaprtando el comando de importacion de datos, especificamente el id de la oficina
    
🟡 CLIENTE:

    . Que el cliente brinde la información del dominio
        Pasos básicos para enlazar IP pública con dominio:
        1. Configurar DNS en tu proveedor de dominio
        Ve al panel de tu proveedor de dominio (GoDaddy, Namecheap, etc.)
        Busca la sección "DNS Management" o "Zona DNS"
        Crea un registro A con:
        Nombre: @ (para el dominio principal) o www
        Valor: La IP pública de tu servidor Digital Ocean
        TTL: 300 (5 minutos)
        2. En Laravel Forge
        Ve a tu servidor en Forge
        En la pestaña "Sites"
        Haz clic en "New Site"
        Ingresa tu dominio completo (ej: midominio.com)
        Forge configurará automáticamente Nginx
        3. Verificar configuración
        Espera 5-15 minutos para propagación DNS
        Prueba: ping midominio.com debe mostrar tu IP
        Visita http://midominio.com en navegador
        4. SSL (opcional pero recomendado)
        En Forge, en tu sitio, clic "SSL"
        Activa "Let's Encrypt" para HTTPS gratuito
        Ejemplo registro DNS:
            Tipo: A
            Nombre: @
            Valor: 164.90.123.456
            TTL: 300
        Opción 3: Ambos (recomendado):
            Registro A: @ → IP del servidor
            Registro A: www → IP del servidor

    . revisar si se mantendrá el dui o se retira
