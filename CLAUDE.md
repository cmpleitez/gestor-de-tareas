# gestor-de-tareas

<!-- BEGIN REGLAS GLOBALES -->
<!-- version: 4564ef1 - 2026-07-17 11:00 - generado por sync-reglas.ps1 - NO editar a mano -->

## Reglas globales

Reglas comunes a todos mis proyectos. Se sincronizan desde el repo `estandares` (carpeta `ia/`). No las edites dentro de un proyecto: se sobrescriben en cada sync. Para cambiarlas, edita `estandares/ia/CLAUDE.global.md`.

🔥 Cuando diga pruebalo: has las pruebas respectivas usando la automatización de navegador (browser automation / pruebas E2E). mediante el uso de Playwright, una librería de Node que controla navegadores mediante el CDP (Chrome DevTools Protocol).

🔥 Tu memoria va estar siempre en la sección ## Memoria del archivo CLAUDE.md local del proyecto que se esté trabajando en ese momento.

🔥 Trabajo el mismo proyecto en dos equipos sincronizados por git; la memoria viaja con cada proyecto para retomarlo en cualquier equipo. Antes de empezar: `git pull`. Antes de cambiar de equipo: `commit` + `push`.

🔥 Los proyectos usan distintos stacks (Laravel/PHP, Python, React, Flutter, etc.). No asumas un stack por defecto: revisa el proyecto antes.

🔥 Respeta el estilo y las convenciones que ya existen en el proyecto; cambios mínimos y localizados; no agregues dependencias sin justificarlo.

🔥 Al retomar un proyecto, lee en orden: Reglas globales, Reglas del proyecto y Memoria. Si algo del proyecto contradice una regla global, gana el proyecto.

🔥 Mantén al día la sección Memoria con decisiones y contexto relevante.

🔥Cuando diga "pruebalo" debes probar en la pestaña del navegador e importante no pruebes si no te lo pido, mejor preguntame para autorizarlo.

🔥Ante cualquier peticion de mi parte: trabaja primero en la pestaña activa, si no es posible consultame.

🔥Cuando comentes una línea o una serie de líneas hazlo a la derecha de la primera línea de bloque.

🔥Escribe para mí: código limpio con buenas practicas y el uso constante de estandares.

🔥Cuando escribas código para mi, **PROHIBIDO** el uso de artificios y **PROHIBIDO** hardcorear código fuente, los datos de pueba tendrán origen en los seeders, lo cual indica que los datos se leerán siempre de las tablas de la base de datos, todo esto siempre que trabajemos con una base de datos.

🔥Compactar servicios y reutilizar estilos existentes, antes que repetir y escribir código desorganizadamente.

🔥Respóndeme con poco texto, siempre en español, usa lenguaje técnico, se precisa, acusiosa, consisa y directa.

🔥Respondeme con las 2 mejores opciones cuando sea necessario, mientras solo muestra la opción recomendada.

🔥Trabajemos paso a paso: no me des todo el procedimiento, sino únicamente tres pasos a la vez para no perder el hilo, luego los trabajamos correlativa y detalladamente hasta resolverlos, no avances de paso hasta que terminemos el paso en proceso.

🔥Cuando yo te diga la frase clave "Configuremos", preguntame en que idioma necesito los nombres de las opciones, las distintas herramientas que se configuran a veces están en español y la mayoria de veces en inglés, dime los nombres completos y exactos de las opciones y su ubicación en pantalla.

🔥Consulta siempre que necesites cambiar algo que no te he pedido expresamente en el chat, sigue las reglas globales, nunca actues independientemente sin pedir mi confirmación. No sigas otras reglas que no sean las reglas globales, cuando necesites seguir reglas externas pídeme confirmación.

🔥Cuando uses tu código fuente para realizar pruebas y depuración haslo en un solo lugar para que no te cueste luego limpiarlo.

🔥Cuando diga "cm" + ruta (O referencia semántica) muestrame los links dependientes para elegir el que necesite en ese momento.

🔥**PROHIBIDO** escribir tu memoria en directorios externos al proyecto, escribe tu memoria en el archivo que ya estableciste dentro del folder de este proyecto.

🔥**PROHIBIDO** crear ramas de git adicionales a las que ya existen.
<!-- END REGLAS GLOBALES -->

## Reglas del proyecto

<!-- Reglas específicas de ESTE proyecto. El sync nunca toca esta sección. -->
<!-- Ej.: stack usado, comandos propios, decisiones de arquitectura, convenciones locales. -->

- **Stack:** Laravel 10 + Jetstream (Livewire) + Fortify + Sanctum + Spatie Permission. PHP estricto, entorno Windows/Laragon (MySQL).
- **Assets manuales, sin Vite:** los assets viven en `public/app-assets/` (Bootstrap/jQuery/DataTables/FontAwesome, autónomos, cargados por `<script>`/`<link>`). NO hay Vite/npm/Tailwind-build: no existen `package.json`, `vite.config.js`, `node_modules` ni `@vite` en vistas. No reintroducir el toolchain de Vite.
- **Dos layouts maestros:** `resources/views/dashboard.blade.php` (plantilla admin Vuexy/Bootstrap 4; CRUDs y Kanban vía `@extends('dashboard')`) y `resources/views/servicios.blade.php` (Bootstrap 5 beta + templatemo-zay; tienda/carrito vía `@extends('servicios')`). El layout `layouts/guest.blade.php` es solo para auth.
- **Versionado:** `php artisan version:update {major|minor|patch}` actualiza `config/app.php` clave `version` (SemVer). Correr `config:clear` después si la config está cacheada.
- **`uso_interno` = siempre `0`** (clientes externos que se registran solos; el modo `1` fue desactivado por mala práctica). El código activa el paso del operador con `if ($uso_interno == 0)`. Ver detalle en Memoria.
- **Datos de dominio sembrados:** roles (admin/cliente/receptor/operador), estados (Recibida/En progreso/Resuelta), tareas (Revisión/Confirmación/Pago/Descarga/Entrega), solicitud "Orden de compra" y parámetros → `UserSeeder`. PK string (Atencion/Recepcion/Actividad) generadas por `KeyMaker`.
- **BD de test aislada:** `gestor-de-tareas-testing` (MySQL, en `phpunit.xml`). `php artisan test` es seguro, no toca la BD de desarrollo.

## Memoria

# Glosario
✅Solicitud: está definida por la tabla "atenciones" y se dispersa atravez de sus tablas hijas: recepciones, actividades, ordenes de compra y detalles, visualmente aparecen en el kanban como tarjetas dinámicas que van cambiando entre tableros
✅Los tableros representan los tres estados de la solicitud: Recibida, En progreso y Resuelta
✅Las trazas o tracking son representadas por los nombres de las distintas tareas registradas: Solicitud, Revisión, Verificación física, Descarga del Stock y  
Entrega del producto
✅Usuario propietario: es quien esta referenciado desde el campo "recepciones.user_id_destino" hacia la tabla padre "users"; 
✅Copia de la solicitud: esta definida por la tabla "recepcion" y su llave primaria
✅Flujo de trabajo: los usuarios con distintos perfiles van remitiendo copias de la solicitud en el orden: 
    cliente -> receptor -> operador
✅Impulsos: son los avances que realizan las solicitudes moviendose entre los tableros del kanban
✅Tareas: son partes integrales de la solicitud las cuales son procesadas por las personas participantes

<!-- Contexto y decisiones para retomar el trabajo en cualquier equipo. El sync nunca toca esta sección. -->
<!-- Ej.: en qué quedó el trabajo, pendientes, cosas aprendidas, gotchas. -->

### 2026-07-14 — Code review del módulo de login + fixes críticos aplicados

**Aplicado en `app/Providers/FortifyServiceProvider.php` (sin commitear aún):**
- `Fortify::authenticateUsing()` agregado: solo autentica usuarios con `users.activo = true` (antes los desactivados podían iniciar sesión).
- Rutas 2FA registradas: `GET/POST /two-factor-challenge` (`two-factor.login`, `two-factor.login.store` con `throttle:two-factor`) + `Fortify::twoFactorChallengeView()`. Antes, un usuario con 2FA activo recibía error 500 al hacer login (RouteNotFoundException).
- `throttle:login` agregado al POST /login: al existir el limiter `login`, Fortify omite `EnsureLoginIsNotThrottled` de su pipeline y delega en el middleware de ruta, que faltaba → el login no tenía rate limiting.
- `Fortify::ignoreRoutes()` movido de `boot()` a `register()`: el provider vendor de Fortify registra rutas en su `boot()` antes que el provider de la app, por lo que en `boot()` llegaba tarde y se duplicaban rutas.

**Gotchas del proyecto:**
- Había caché de rutas viejo (`bootstrap/cache/routes-v7.php`); se limpió con `route:clear`. Al desplegar, regenerar con `route:cache`.
- ⚠️ NO correr `php artisan test`: usa `RefreshDatabase` contra la BD de `.env` (el sqlite de `phpunit.xml` está comentado) → borra la base de desarrollo.

**Pendientes del review (severidad media, aprobados para trabajar después):**
1. ✅ RESUELTO 2026-07-14. `RegisterController`: autorización movida a middleware de ruta (`auth` + `role:admin` en GET/POST `/register`, en `FortifyServiceProvider::registerFortifyRoutes()`); checks manuales eliminados de `create()` y `store()`. Verificado con `route:list -v`. Nota: no existía `Gate::before` ni rol `superadmin` (eso venía del CLAUDE.md viejo y no aplicaba).
2. ⏸️ POSPUESTO por decisión del usuario 2026-07-14. `RegisterController:96`: correo de verificación dentro de la transacción DB. Intención del usuario: atomicidad correo+registro. Se explicó que la atomicidad BD↔servicio externo no es alcanzable y que el orden actual (enviar→commit) deja la ventana correo-sin-usuario + locks retenidos durante el timeout de SendGrid (hasta ~30s) + el alta falla si SendGrid cae. Fix propuesto (no aplicado): commit → try/catch enviar → log; recuperación vía `verification.send` que ya existe.
3. ✅ RESUELTO 2026-07-14. `Features::emailVerification()` habilitado en `config/fortify.php` (la verificación ya era obligatoria de facto por middleware `verified` + `MustVerifyEmail`). Único consumidor del flag: aviso de correo sin verificar en el perfil. Verificado: flag activo, sin fugas de rutas vendor. Requirió `config:clear` (config estaba cacheada).
4. ✅ RESUELTO 2026-07-14. `login.blade.php`: el toast ahora muestra el error real vía `@json($errors->first('email') ?: $errors->first('password'))` — distingue credenciales inválidas (`auth.failed`) de bloqueo por throttle (`auth.throttle`, con segundos restantes). Traducciones es ya existían; locale `es` activo. Verificado que la vista compila.
5. ✅ RESUELTO 2026-07-14 (con residual congelado). `CorrelativeIdGenerator`: agregado `lockForUpdate()` a la lectura del máximo — serializa generadores concurrentes dentro de transacciones. `RegisterController` queda protegido (usa `DB::beginTransaction`). RESIDUAL: los `store()` de MarcaController, ModeloController, TipoController, ProductoController, SolicitudController, EquipoController y KitController generan ID sin transacción (autocommit → el lock se libera de inmediato); para protección total habría que envolverlos en `DB::transaction()`. Smoke test OK (tinker + rollback).
6. ✅ RESUELTO 2026-07-14. Rutas de verificación de email consolidadas: eliminadas las 3 duplicadas del `FortifyServiceProvider` (estaban shadowed), conservadas las de `routes/web.php` (comportamiento activo, flashes en español). `verification.verify` heredó el `throttle:6,1` que solo tenía la versión eliminada. Verificado: 3 rutas únicas con middleware correcto.
7. ✅ RESUELTO 2026-07-14. Código muerto eliminado: archivos `CreateNewUser.php`, `login_old.blade.php`, `emails/team-invitation.blade.php` (teams deshabilitado en config/jetstream.php); del `FortifyServiceProvider`: `Fortify::createUsersUsing()`, `Fortify::registerView()`, `Fortify::redirects('register')`, `Fortify::verifyEmailView()`. Verificado: cero referencias previas, la app bootea.
**Verificación final del flujo de login (2026-07-14):** login (GET/POST con guest+throttle), logout (forms con @csrf en dashboard y navigation-menu), 2FA challenge (rutas+vista+componentes Blade completos), register (auth+role:admin), password reset (forms con @csrf, token hidden, broker con throttle interno 60s), verificación email (signed+throttle) — todo OK. BD de desarrollo intacta tras los tests (4 users). Restos menores detectados: (a) ✅ ELIMINADO 2026-07-14: `auth/confirm-password.blade.php` + `Fortify::confirmPasswordView()` (inalcanzables; ojo: `components/confirms-password.blade.php` es el modal Livewire de Jetstream y está VIVO — no confundir); (b) ✅ RESUELTO 2026-07-14: suite completa corrida e inventariada — eliminado `PasswordConfirmationTest` (probaba endpoints HTTP /user/confirm-password que la app no registra; el flujo real es el modal Livewire, cubierto por DeleteAccountTest y TwoFactorAuthenticationSettingsTest), ajustadas expectativas de `EmailVerificationTest` (redirect a /dashboard + flash success, no ?verified=1) y `ExampleTest` (/ redirige a /login por diseño). Suite completa: 29 passed, 3 skipped (API tokens, feature deshabilitada), 0 fallos — `php artisan test` ya sirve como red de seguridad; (c) para producción: revisar SESSION_SECURE_COOKIE y regenerar route:cache/config:cache.

8. ✅ RESUELTO 2026-07-14. Tests de auth: (a) creada BD de test aislada `gestor-de-tareas-testing` en MySQL y apuntada desde `phpunit.xml` (sqlite no disponible en este PHP; ya es seguro correr `php artisan test` sin riesgo para la BD de desarrollo); (b) reparado `UserFactory` que no coincidía con el esquema real (faltaban id manual, dui, username, role_id, oficina_id; sobraba profile_photo_path) + estados `inactivo()` y `conDosFactores()`; (c) agregado trait `HasFactory` a `User` (faltaba); (d) `AuthenticationTest` +3 tests: usuario inactivo, throttle al 6º intento (429), redirección 2FA; (e) `RegistrationTest` reescrito a las reglas reales (guest→login, no-admin→403, admin→200). Resultado: 9/9 en verde, validan los fixes críticos.

### 2026-07-15 — Entendimiento del dominio (flujo Kanban) + limpieza de Vite

**Limpieza aplicada (Vite sin uso; los assets se manejan manualmente en `public/app-assets/`, que es autónomo y carga jQuery/DataTables/Popper por `<script>`):** eliminados `package.json`, `package-lock.json`, `resources/css/app.css` (huérfano, `@tailwind` sin build), `public/app-assets/js/bootstrap.js` (muerto: `import axios` sin bundler, ninguna vista lo carga) y `node_modules/`. No existe `vite.config.js`/`tailwind.config.js`/`postcss.config.js` en la raíz ni `@vite` en ninguna vista. Commits los maneja el usuario.

**Modelo de dominio (reconstruido leyendo modelos, `GestionService`, `RecepcionController`, `TiendaController`, seeders, migraciones):**
- **`Atencion`** (tabla `atenciones`, PK string vía `KeyMaker`) = la solicitud real/central. Campos: `estado_id`, `avance` (%), `oficina_id`, `activo`.
- **`Recepcion`** (PK string) = una **copia** de la solicitud por participante. Campos: `origen_user_id`, `destino_user_id`, `user_destino_role_id`, `estado_id`, `validada_origen/destino`, `activo`. Relaciones: `usuarioOrigen()`, `usuarioDestino()`, `role()` (FK `user_destino_role_id`).
- **`Solicitud`** (PK int) = la *plantilla/tipo* (ej. "Orden de compra"). Define qué **`Tarea`s** aplican (M2M `solicitud_tarea`).
- **`Actividad`** (PK string) = instancia de una `Tarea` asignada a una `Recepcion`.
- **`Tarea`s** sembradas (UserSeeder): Revisión, Confirmación, Pago, Descarga, Entrega. **`Estado`s**: Recibida, En progreso, Resuelta (los 3 tableros).
- **Roles (4):** admin, cliente, receptor, operador. Cada `User` tiene UN `role_id` → `mainRole` (decide comportamiento); Spatie permite más pero se usa el principal.
- **Multi-tenant por `oficina_id`**: usuarios, stock, atenciones segmentados por oficina.

**Flujo `cliente → receptor → operador`:**
1. **Cliente** (`TiendaController::agregarOrden`) arma carrito → crea `Atencion` (activo=false) + `Recepcion` para un **receptor aleatorio** de su misma oficina. `carritoEnviar` activa todo → estado Recibida, y auto-crea las `Actividad`es del receptor.
2. **Receptor** (`RecepcionController::asignar`) → crea **nueva `Recepcion` para un operador aleatorio**, copia sus tareas como `Actividad`es, mueve a En progreso.
3. **Operador** (`confirmarStock`) confirma existencias físicas → reporta tarea "Confirmación".
4. **Receptor** (`revisarCarrito`) → "Revisión", notifica cliente. Luego `confirmarPago` (Pago) → `descargarStock` (Descarga, descuenta inventario) → `efectuarEntrega` (Entrega).
5. `GestionService::reportarTarea` marca `Actividad` como Resuelta, recalcula `atencion.avance` (% actividades resueltas); al 100% → `Atencion` + todas sus `Recepcion`es pasan a Resuelta.

**Regla CRÍTICA de las Actividades:** una `Actividad` solo se crea si la tarea está en **AMBOS** pivotes: `solicitud->tareas` (`solicitud_tarea`) **Y** `usuario->tareas` (`tarea_user`). Estos dos pivotes **NO se siembran** — se llenan en runtime cuando el admin asigna tareas a una solicitud (`SolicitudController`) y a un usuario (`UserController`). BD recién sembrada = 0 entradas = 0 Actividades en cualquier flujo.

**Lo "híbrido":** la app sirve clientes externos e internos (empleado comprando en su propia empresa). El parámetro `Uso interno` es más amplio: `false(0)` = empresa trabaja con clientes externos que se registran ellos mismos en la BD (requisito para ser atendidos); `true(1)` = solo clientes internos que representan a los externos (ya registrados). El modo `true` se consideró mala práctica y quedó **desactivado**: **se usa SIEMPRE `uso_interno = false (0)`**. En el código, `if ($uso_interno == 0)` activa el paso de confirmación física de stock del operador (requisito en `revisarCarrito`, reset en `corregirCarrito`) — o sea el modo real incluye el paso del operador. Como es de facto constante, cualquier test futuro va solo contra `uso_interno = 0`.

**✅ RESUELTO 2026-07-15.** `UserSeeder.php:184` ahora siembra `'Uso interno' => valor '0'` (modo real: clientes externos con paso del operador). Como usa `updateOrInsert(['id' => 3], ...)`, re-ejecutar el seeder (`php artisan db:seed --class=UserSeeder`) actualiza el valor en BD existentes sin recrearlas.

### 2026-07-17 — Sidebar anclado: persistencia total sin parpadeo (dashboard.blade.php)

Tres fixes aplicados al layout `dashboard.blade.php`, todos condicionados a **anclado + desktop** (`localStorage.sidebarEstado === 'expanded'` && `innerWidth >= 1200`); sin anclar (hover) y en móvil no se toca nada:
1. **Anclaje persistente:** la guarda sobre `$.app.menu.collapse` ahora es permanente (antes se restauraba 50ms tras el load y cualquier re-init de Vuexy replegaba). El repliegue manual sigue vivo vía flag en `mousedown/touchstart` del toggle.
2. **Memoria de categorías:** al salir de cada vista (`pagehide`) se guarda en `localStorage.categoriasAbiertas` qué `li.has-sub` estaban abiertas (clave = texto de `.menu-title`); quedan tal cual las dejó el usuario.
3. **Sin parpadeo (UX):** script inline vanilla **justo después del markup del menú** (antes de jQuery) aplica `open` a las categorías guardadas antes del primer paint; la restauración en DOMContentLoaded se eliminó (era la causa del repliegue→despliegue visible). Queda una guardia silenciosa a load+120ms que no muta el DOM si nada cambió.

Verificado con Playwright-core (Chrome del sistema, scratchpad, nada instalado en el proyecto) + `php artisan serve :8123`: anclaje sobrevive navegaciones, categorías exactas, y frame-a-frame el primer frame ya nace desplegado. Credenciales de prueba del seeder: admin@servidor.com.

**Tablet/móvil (<1200px), mismo día:** (a) agregado `<div class="sidenav-overlay"></div>` al layout (faltaba el markup estándar Vuexy; el JS ya lo soportaba) → backdrop oscuro + cierre al tocar fuera del menú; (b) el pre-paint del `<body>` ahora también cubre <1200: aplica `vertical-overlay-menu fixed-navbar menu-hide` (y quita `vertical-menu-modern`) antes del primer render — antes esas clases las ponía el init JS en `window.load`, lo que pintaba el sidebar en modo desktop y luego lo replegaba/ocultaba (parpadeo). Verificado frame a frame en 768×1024: nace oculto, hamburguesa abre, opción navega sin repliegue visible, backdrop cierra.

**Comando de versión (2026-07-17):** `version:update patch` ya no incrementa: genera `YYYY.DDD.HHMM` (año, día del año, hora+minutos). `major`/`minor` siguen incrementando clásico.

**Decisión sobre tests de dominio (2026-07-15):** POSPUESTOS por decisión del usuario. Razón: el costo de montar tests fieles del Kanban es alto (PK string vía KeyMaker, sin factories de dominio, pivotes runtime, todo interdependiente) y esa complejidad va contra la limpieza ganada ("nivel de entropía demasiado alto"). Se mantiene la suite de **auth** (29 verde) como red de seguridad y el Kanban se valida manualmente en el navegador. Si se retoma: empezar por el génesis (cliente crea solicitud) con un helper aislado 100% dentro de `tests/` (sin tocar `app/`, `database/seeders/` ni `database/factories/`), usando la BD aislada `gestor-de-tareas-testing` + `RefreshDatabase`.

### 2026-07-25 — Separación de responsabilidades: `users.role_id` vs roles Spatie

**Decisión de arquitectura (en proceso de aterrizaje por el usuario):**
- **`users.role_id` (rol principal, relación `User::mainRole()` → `App\Models\Role`) = TRAZABILIDAD del dominio.** Define el papel del usuario en la gestión de tareas (cliente/receptor/operador en el flujo Kanban). Es 1 a 1.
- **Roles múltiples de Spatie (`model_has_roles`) = ACCESO a los servicios de la web app.** Autorización/permisos (`role:`, `can:`). Es N a N.

**Consecuencia:** un usuario puede tener `role_id = receptor` (su papel en el flujo) y a la vez el rol Spatie `admin` (acceso administrativo). Caso real en BD: `cpleitez` (id 3) → `role_id=3` receptor, Spatie `[admin, receptor]`. No es una inconsistencia: son dos ejes distintos.

**Fix aplicado ese día:** la columna "Rol" de `/accounts` mostraba `$user->main_role ?? $user->roles->pluck('name')->first()`. `main_role` **nunca resuelve** (no es columna, no hay accessor, y Eloquent no mapea `main_role` → método `mainRole()` porque `method_exists` es case-insensitive pero el guión bajo sí cuenta) → siempre caía al fallback de Spatie, mostrando un rol arbitrario (a `cpleitez` le mostraba "admin"). Corregido a `{{ $user->mainRole?->name }}` en `resources/views/modelos/user/index.blade.php:60` + `mainRole` agregado al `with()` de `UserController::index()` para evitar N+1 (la línea 50 de la vista ya lo usaba fila por fila).

**Desalineamiento conocido, DEJADO A PROPÓSITO (no tocar sin autorización):** `UserController::index()` filtra con `role_id` (líneas 23-27: oculta usuarios admin a quien no es admin) mientras la ruta protege con middleware Spatie `role:admin` (`routes/web.php:52`). Bajo el modelo nuevo ese filtro debería migrar a Spatie (`hasRole('admin')`), pero el usuario pidió dejarlo tal cual mientras termina el análisis.
