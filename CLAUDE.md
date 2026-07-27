# gestor-de-tareas

<!-- BEGIN REGLAS GLOBALES -->
<!-- version: 5a92816 - 2026-07-27 09:03 - generado por sync-reglas.ps1 - NO editar a mano -->

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

🔥Cuando diga "cm" + ruta (O referencia semántica) muestrame máximo dos links que tengan referencia a lo solicitado

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
- **`uso_interno`: ELIMINADO del código** (2026-07-25, commit `79f659cd`). Era el switch interno(`1`)/externo(`0`); el modo interno se descartó por mala práctica. Hoy el flujo externo con paso del operador es el **único** y no hay condicional: el paso de confirmación física del operador es incondicional. No reintroducir el parámetro. Ver detalle en Memoria.
- **El `admin` no participa en el gestor de tareas** (Kanban). Se le excluye el permiso `ver-solicitudes` vía `$puertas_gestor` en `UserSeeder`; su `users.role_id` sigue siendo `1` (rol `admin`), que no es un papel del flujo. Razón: es un participante demasiado distante del proceso y nadie sabría cuándo se le asignan solicitudes.
- **Tres controles independientes (decisión firme: NO fusionarlos).** Cada uno responde una pregunta distinta y tiene distinta volatilidad; unirlos obligaría a redeployar el seeder cada vez que cambia una persona:
  | Control | Almacenamiento | Responde |
  |---|---|---|
  | **Acceso** | Spatie (`model_has_roles`, `role_has_permissions`) | ¿Entra a la ruta/pantalla? |
  | **Gestión / trazabilidad** | `users.role_id` → `User::mainRole()` | ¿Qué papel juega en el flujo? |
  | **Reparto** | pivote `tarea_user` (`/accounts/tareas-edit/{user}`) | ¿Qué tareas se le instancian? |

  **Invariante que sostiene la convivencia:** *la ruta autoriza con Spatie · la vista decide con `role_id` · el reparto lo decide `tarea_user`*. En el Kanban acceso y gestión conviven mezclados a propósito (hay rutas protegidas); no invertir los ejes.
- **El rol `admin` es excluyente, no exclusivo.** Puede haber **varios administradores** (lo exige la auditoría), pero: (a) *Acceso:* quien es `admin` **no puede tener ningún otro rol Spatie** — así Spatie no mezcla los accesos que viven dentro del gestor de tareas; (b) *Gestión:* `role_id` solo admite papeles del flujo (cliente, receptor, operador), y al marcar `admin` el servidor **impone `role_id = 1`**, que es el marcador de "no participa en el flujo". Sin esa imposición un administrador entraría a los sorteos de participantes (`TiendaController:313`, `RecepcionController:125`) sin tener `ver-solicitudes`, y la solicitud nacería muerta. Blindado en `UserController::rolesEdit/rolesUpdate` y en `roles-edit.blade.php` (checkboxes excluyentes + `<select>` inhabilitado). - **La cuenta `admin` (`username = 'admin'`) es la cuenta de rescate (break-glass)** por si los administradores titular y suplente pierden su acceso. Es **inmutable desde `/accounts` para cualquier actor**: los 6 métodos que la tocan (`update`, `rolesUpdate`, `equiposUpdate`, `tareasUpdate`, `activate`, `destroy`) la rechazan con el guard de una sola condición `$user->username === 'admin'`. Solo se modifica por seeder o BD. Ocultar su fila en `index()` es aislamiento de navegación, no autorización por objeto: la protección real está en el endpoint.
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

**Lo "híbrido" (⚠️ HISTÓRICO — superado el 2026-07-26, ver entrada de esa fecha; el parámetro ya no existe en código ni en el seeder):** la app sirve clientes externos e internos (empleado comprando en su propia empresa). El parámetro `Uso interno` es más amplio: `false(0)` = empresa trabaja con clientes externos que se registran ellos mismos en la BD (requisito para ser atendidos); `true(1)` = solo clientes internos que representan a los externos (ya registrados). El modo `true` se consideró mala práctica y quedó **desactivado**: **se usa SIEMPRE `uso_interno = false (0)`**. En el código, `if ($uso_interno == 0)` activa el paso de confirmación física de stock del operador (requisito en `revisarCarrito`, reset en `corregirCarrito`) — o sea el modo real incluye el paso del operador. Como es de facto constante, cualquier test futuro va solo contra `uso_interno = 0`.

**⚠️ HISTÓRICO — REVERTIDO el 2026-07-26 (el seeder ya NO siembra este parámetro).** `UserSeeder.php:184` sembraba `'Uso interno' => valor '0'` (modo real: clientes externos con paso del operador). Como usa `updateOrInsert(['id' => 3], ...)`, re-ejecutar el seeder (`php artisan db:seed --class=UserSeeder`) actualiza el valor en BD existentes sin recrearlas.

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

**✅ RESUELTO el mismo día — regla de visibilidad definitiva de `/accounts` (`UserController::index()`):** el filtro pasó por dos iteraciones y quedó así:

```php
$users = User::with('oficina', 'equipos', 'roles', 'mainRole')
    ->where(function ($cuenta) { // La cuenta de sistema 'admin' solo es visible para sí misma; el resto de cuentas son visibles para todos
        $cuenta->where('username', '!=', 'admin')
            ->orWhere('id', auth()->id());
    })
    ->get();
```

- **Regla única, sin ramas:** se listan todos los usuarios; la única cuenta oculta es la de sistema `username = 'admin'`, y se oculta para todos **salvo para sí misma** (todo usuario ve siempre su propia fila, en cualquier nivel).
- Tener el rol Spatie `admin` ya **no** oculta a nadie: es un dato de acceso normal (caso `cpleitez`, visible para todos).
- `role_id` **no interviene** en la visibilidad — solo trazabilidad. El acceso a la vista lo sigue gobernando el middleware Spatie `role:admin` (`routes/web.php:52`).
- El `where` agrupado en closure es obligatorio (sin él, el `orWhere` escaparía al `WHERE` completo). El literal `'admin'` como identificador de la cuenta de sistema sigue la convención ya usada en el mismo controlador (`rolesEdit`, `rolesUpdate`, `equiposUpdate`, `tareasUpdate`, `destroy`, `activate`).
- Verificado por usuario autenticado: `admin` → los 4; `maragon`/`cpleitez`/`hseldom` → los 3 sin `admin`. Mostrar la propia fila es seguro: la vista ya bloquea auto-eliminación y auto-edición de roles (`index.blade.php:102,116`).

### 2026-07-25 — Saneamiento del estándar de dos ejes (auditoría por pares)

**Regla de arquitectura (patrón de medida para toda la app):**
- **Decisiones de FLUJO** (quién es cliente/receptor/operador en la gestión de tareas: elegir participantes, filtrar tarjetas del Kanban, mostrar el rol) → **`users.role_id` / `User::mainRole()`**.
- **Decisiones de ACCESO** (a qué información y servicios entra el usuario) → **roles Spatie** (`hasRole()`, middleware `role:`, `@can`).

Método de auditoría: por cada punto que toque roles se clasifica primero la decisión (flujo o acceso) y se verifica que esté resuelta con el eje que le corresponde. Lo que cumple no se toca. Se repara de dos en dos, por impacto.

**Clasificaciones ya resueltas (cumplen, NO tocar):**
- Filtrar qué recepciones/tarjetas ve cada usuario en el Kanban = **flujo** (`RecepcionController:76`, `TiendaController:509/531/532`, `carrito.blade.php`, `solicitudes.blade.php` — usan `mainRole`).
- Capa de acceso completa: `routes/web.php` y `FortifyServiceProvider:124` con `role:admin` + `can:*` (Spatie puro). No hay middleware propio que use `role_id`.
- `RecepcionController::asignar():125` (elige operador por `mainRole`) y `TiendaController:315` (elige receptores por `role_id`) = flujo bien resuelto; son el patrón de referencia.

**✅ Par 1 reparado (flujo que se resolvía con Spatie → migrado a `role_id`):** `RecepcionController::operadores()` líneas 234 y 242, y `TiendaController::solicitudes()` línea 515 — los tres armaban el universo de operadores con `whereHas('roles')` mientras `asignar()` lo arma con `whereHas('mainRole')`; la validación de disponibilidad y la asignación real podían consultar conjuntos distintos ("No hay operadores disponibles" tras haber pasado el chequeo). Cambio mínimo: `roles` → `mainRole` en el `whereHas`, resto de cada consulta intacto. Verificado: ambos ejes coinciden hoy en BD (`hseldom`), sin divergencia → cero cambio de comportamiento actual, defecto latente cerrado.

**Colaterales detectados, registrados y NO reparados (decidir en pares siguientes):**
- `RecepcionController:234-244`: consultas asimétricas — `$operadores` filtra por oficina pero no por `activo`; `$operadores_activos` filtra por `activo` pero **no por oficina** (fuga multi-tenant).
- `TiendaController:315`: receptores del génesis sin filtrar `activo`, mientras `asignar()` sí lo exige.
- `UserController:135-143` (`rolesUpdate`): la regla "cliente no puede tener otro rol a la vez" compara `'Cliente'` con mayúscula contra roles sembrados en minúscula → nunca se cumple. Confirmar empíricamente antes de tocar.

**En cola para el par 2:** ✅ `RecepcionController:357` ya reparado en el commit `79f659cd` (`confirmarStock` notifica receptores por `mainRole`). Pendiente: barrido de `database/seeders/`, `GestionService` y resto de vistas.

### 2026-07-26 — Cierre de la depuración de "Uso interno" + aislamiento del `admin`

**1. `uso_interno` erradicado del código.** El commit `79f659cd` (2026-07-25) eliminó todas las lecturas y ramas condicionales del parámetro en `RecepcionController`, `TiendaController`, `carrito.blade.php`, `solicitudes.blade.php` y `UserSeeder`. Barrido de verificación (2026-07-26): **cero coincidencias** de `uso_interno`/`Uso interno` en `app/`, `resources/`, `routes/`, `database/`. Lógica resultante coherente: el paso de confirmación física del operador es incondicional (`revisarCarrito` exige `stock_fisico_existencias` no nulo) y el polling del Kanban ya no está condicionado. Sin cascada muerta (`obtenerTraza` conserva 4 consumidores).

**Residuo de BD (lo borra el usuario a mano):** el registro `parametros` id=3 `Uso interno` sigue vivo en las BD existentes (`valor=0, activo=1`). Nadie lo lee, pero `ParametroController::index()` hace `Parametro::all()` → aparece en `/settings` con switch y botón editar. El seeder ya no lo recrea. `DELETE FROM parametros WHERE parametro = 'Uso interno';`

**2. ✅ APLICADO — el `admin` fuera del gestor de tareas.** `UserSeeder` línea ~94: el rol `admin` pasó de `givePermissionTo(Permission::all())` a `syncPermissions(Permission::whereNotIn('name', $puertas_gestor)->get())` con `$puertas_gestor = ['ver-solicitudes']`. `syncPermissions` es obligatorio aquí: `givePermissionTo` solo agrega y nunca revocaría el permiso excluido en BD ya sembradas. Verificado tras `db:seed`: admin 27/28 permisos sin `ver-solicitudes`; cliente 10, receptor 20, operador 6 sin cambios; `users.role_id` del admin sigue en `1` y su rol Spatie `admin` intacto. Efecto: `GET tienda/requests` (el Kanban) da 403 y el ítem del sidebar desaparece solo (`@can('ver-solicitudes')` en `dashboard.blade.php:176`).

**Riesgo aceptado conscientemente:** el aislamiento es de *navegación*, no de autorización por objeto. El admin conserva `ver-carrito` (puede abrir `GET /tienda/shop.request`, la vista de detalle con los botones *Confirmar stock* / *Revisar*), `autorefrescar` y los permisos de acción de `recepcion/*`. Ampliar el bloqueo = agregar nombres a `$puertas_gestor`.

**3. Residuos abiertos, EN ANÁLISIS del usuario (no tocar sin su decisión):**
- **Código muerto:** `RecepcionController:181-208` `avanzarEstado()` + `routes/web.php:152` (`recepcion.avanzar`). Era la versión degradada del modo interno de `asignar()`: hace la misma transición a "En progreso" y la misma traza, pero **sin** crear la copia-recepción del operador ni sus `Actividad`es (que `asignar():135-156` sí crea). En el modo externo avanzar y asignar son el mismo evento. Depende de `Estado`, `Recepcion` y `obtenerTraza` (vivas por otros consumidores); **nadie depende de él** (0 referencias en rutas, vistas y JS). Si se borra, el permiso `asignar-recepcion` se conserva vía `web.php:151`.
- **Hueco de UX:** `userRole` = `mainRole->name` (`solicitudes.blade.php:172`) pero el grupo del Kanban admite `role:receptor|operador|admin` (`web.php:146`); si el `mainRole` no es receptor ni operador, `updatePosition()` deja `url = null` y las líneas 425-428 devuelven la tarjeta **en silencio, sin toast**. Antes ese caso caía a `recepcion.avanzar`. Con el punto 2 el admin ya no llega a esa pantalla, pero la rama muda sigue en el código para cualquier otro `mainRole`.
- **Preexistente, ajeno a `uso_interno`:** arrastrar una tarjeta a "Recibida" o "Resuelta" también invoca `asignar`, que siempre fija "En progreso", mientras el frontend pinta el badge de la columna destino → desincronización visual.

**4. ✅ APLICADO — endurecimiento de seguridad (3 cambios, 2026-07-26):**

**(a) Autorización por objeto: `App\Http\Middleware\VerificarRecepcionPropia` (alias `recepcion.propia`).** Aplicado al grupo `recepcion/*` completo (`routes/web.php:147`) y a `recepcion.orden-compra` (`web.php:175`, que vive en el grupo TIENDA). Cierra el IDOR: antes cualquier participante del flujo podía operar la recepción de otro enviando un id ajeno. Reglas: resuelve el id desde `route('recepcion')` / `route('recepcion_id')` / `input('recepcion_id')` (soporta que `SubstituteBindings` ya lo haya convertido en modelo); exige ser **participante** (`origen_user_id` u `destino_user_id`) **y** misma `oficina_id`; si vienen ambos ids, exige que `recepcion->atencion_id` coincida con el `atencion_id` recibido; si solo llega `atencion_id` (caso `editarCarrito`), exige participar en alguna recepción de esa atención. Sin `recepcion_id` ni `atencion_id` deja pasar (catálogos, stock, reportes no operan sobre una recepción). Responde JSON 403 para AJAX y `abort(403)` para navegación, y registra un `Log::warning` con ruta e ids.

**Verificado empíricamente** (middleware invocado con los 4 usuarios reales contra la recepción `202600100001`, origen=maragon destino=cpleitez): `cpleitez` y `maragon` → 200 PERMITE; `admin` y `hseldom` → 403 DENIEGA.

⚠️ **Consecuencia a decidir:** el middleware excluye al `admin` de **todo** el gestor (no participa en ninguna recepción). Como `confirmar` (tareas **Pago** y **Entrega**) es un permiso que **solo el admin** tiene, si alguna vez se asignan esas tareas nadie podrá ejecutarlas: el admin tiene el permiso pero el middleware lo bloquea. Hoy no afecta porque `solicitud_tarea` solo contiene Revisión, Confirmación y Descarga. Salidas posibles: dar `confirmar` al receptor (parece lo correcto según el flujo documentado), o exceptuar al admin en el middleware (contradice la decisión de sacarlo del gestor).

**(b) `tienda.agregar-item` de GET a POST** (`web.php:178`). Era el único GET mutante del gestor (crea `Atencion`+`Recepcion`+`Orden`+`Detalle`). Los 3 invocadores en `modelos/kit/tienda.blade.php` pasaron de `href="{{ route(...) }}"` a `href="#" data-url="{{ route(...) }}"` (evita que el navegador pueda prefetchear la URL) y el handler `handleAgregarKit` ahora hace `fetch(url, {method:'POST', headers:{'X-CSRF-TOKEN': ...}})`. Requirió agregar `<meta name="csrf-token">` a `resources/views/servicios.blade.php` (el layout de tienda no lo tenía; `dashboard.blade.php` sí).

**(c) Los 8 `destroy` de GET a DELETE.** `user`, `marca`, `modelo`, `tipo`, `producto`, `kit`, `kit.destroy-equivalente`, `solicitud`. Eran borrado **físico** por GET (ningún modelo usa `SoftDeletes`): un `<img src=".../brands/destroy/3">` en cualquier página bastaba para borrar con la sesión del admin. Patrón nuevo: los `<a>` pasan a `href="#" data-url="..." class="btn-eliminar ..."` y el componente compartido **`resources/views/components/delete-handler.blade.php`** (incluido con `@include`, igual que `orientation-manager`) construye y envía un form con `_token` + `_method=DELETE`. Incluido en los 6 índices + `kit/edit`. `solicitud.destroy` no tiene UI (ruta sin invocador).

**Nota:** no se agregó diálogo de confirmación al eliminar — no existía antes y no se pidió. Sigue siendo un clic sin retorno sobre un borrado físico.

**Colateral detectado, NO tocado:** `resources/views/modelos/equipo/index.blade.php` (y `create`/`edit`) referencian `route('equipo.destroy')`, `route('equipo.edit')`, etc., pero **no existe ninguna ruta `equipo.*`** en `web.php`; `EquipoController` sí existe y devuelve esas vistas. Renderizar esa vista lanzaría `RouteNotFoundException` → módulo huérfano.

**5. Hallazgos de seguridad pendientes (NO reparados, sin decisión aún):**
- **La UI promete acciones que el backend rechaza:** `dibujarTareas` (`solicitudes.blade.php:637-700`) genera los checkboxes de Pago/Descarga/Entrega **sin `@can`**. Un receptor con la tarea "Pago" asignada vería el control y recibiría 403 al pulsarlo (no tiene `confirmar`). Ligado al punto ⚠️ de la sección 4(a).
- **Defensa en profundidad ausente en los controladores:** el middleware `recepcion.propia` cubre las rutas, pero los métodos siguen confiando en los ids del request. Si algún día se llama a esos métodos desde otra ruta sin el middleware, el IDOR reaparece.

**6. ✅ APLICADO — bloque estándar de log en los métodos que no lo tenían (`RecepcionController`, 2026-07-26).** Patrón de referencia: `descargarStock` (`catch { DB::rollBack(); Log::error('Log:: [Usuario: '.name.'] ...', ['exception' => $e]); return json 500; }`). Reparados 4 métodos que fallaban sin dejar rastro:
- `confirmarPago` y `efectuarEntrega`: eran 6 líneas sin try/catch ni log. Ahora con el bloque completo. **Se les agregó `DB::beginTransaction()`/`commit()`** porque el estándar incluye `rollBack()` y `GestionService::reportarTarea` no maneja transacción propia (toca `Actividad` + `Atencion` + todas las `Recepcion`es de la atención con saves sueltos: un fallo a mitad dejaba el avance inconsistente). Ahora igualan a sus hermanos `confirmarStock`/`revisarCarrito`.
- `editarCarrito`: sin try/catch y con `Atencion::find()` cuyo null explotaba en el `->load()` siguiente. Ahora `findOrFail()` + log + `back()->with('error')` (devuelve vista, no JSON).
- `lecturaTransacciones`: 90 líneas de lectura sin protección; `$item->orden->unidades` desreferencia sin comprobar. Ahora envuelto con log + JSON 500.

Quedan **sin** log a propósito `historialTransacciones` y `createStock`: dos `select` triviales que devuelven vista; un fallo ahí es de infraestructura y Laravel ya lo registra con stack trace. Cobertura actual: 16 de 18 métodos.
- Los demás endpoints del flujo son POST + CSRF: escribir la URL da 405, no son alcanzables por accidente. El CSRF, eso sí, **no** protege del IDOR: un usuario legítimo ya tiene token válido (y el `recepcion_id` viaja en `data-recepcion-id` del DOM, editable con el inspector) — por eso hacía falta 4(a).

### 2026-07-27 — Los tres controles: inventario del Kanban y blindaje de `role_id`

**Decisión:** se mantienen los tres ejes (acceso Spatie / gestión `role_id` / reparto `tarea_user`) y **se conserva la mezcla acceso+gestión dentro del Kanban**. Desarmarla exigiría primero bajar las verificaciones a los controladores (hoy confían en los ids del request) y tocar 14 rutas para *no* cambiar comportamiento: mal negocio. Regla escrita en Reglas del proyecto.

**Inventario: 15 permisos Spatie en la superficie del Kanban** (tablero `tienda/requests`, detalle `shop.request`/`edit-request`, grupo `recepcion/*`), más el middleware `role:receptor|operador|admin` de `web.php:146`:

| Permiso | Roles que lo tienen |
|---|---|
| `ver-solicitudes` (puerta del tablero) | cliente, receptor, operador |
| `autorefrescar` | cliente, receptor, operador, admin |
| `asignar-recepcion` (mover tarjeta) | receptor, operador, admin |
| `ver-recepcion` · `crear-stock` · `descargar-stock` · `ver-reportes` · `corregir-carrito` · `revisar` | receptor, admin |
| `ver-tareas` · `editar-carrito` | receptor, operador, admin |
| `ver-orden` · `retirar-item` | cliente, receptor, admin |
| `confirmar-stock` | operador, admin |
| `confirmar` (Pago/Entrega) | **solo admin** |

**Hallazgo estructural:** los 15 permisos son **función pura del rol** — `givePermissionTo`/`syncPermissions` solo existen en `UserSeeder`, no hay pantalla que asigne permisos por usuario. Preguntar `can('revisar')` es preguntar "¿es receptor?" con dos saltos de tabla. La duplicación es la deuda aceptada conscientemente al mantener los tres ejes.

**Riesgo latente que vigila la regla:** `cpleitez` (flujo=receptor, Spatie=[admin,receptor]) hereda de `admin` permisos que su papel no tiene (`confirmar`, `confirmar-stock`). Hoy no los ve porque el `@if($rol_usuario_actual …)` de `carrito.blade.php:482` los tapa — el eje correcto tapa al incorrecto **por accidente, no por diseño**. Si alguien invierte los ejes (poner `@can` donde va `mainRole`), el bug aflora.

**Incidente que originó el blindaje:** la cuenta `admin` apareció con `role_id = 3` (receptor). `TiendaController:313` elige el receptor del génesis con `where('role_id', …)->random()`, así que en la oficina 2 el sorteo era entre `admin` y `cpleitez`: ~50% de las solicitudes nuevas caían en una cuenta que no puede abrir el Kanban (`ver-solicitudes` revocado) y con 0 habilidades en `tarea_user` → 0 `Actividad`es, avance congelado en 0. **Vía de entrada:** el guard de `UserController` era `$user->username === 'admin' && auth()->user()->username === 'admin'` — bloqueaba al admin editándose a sí mismo pero **permitía que otro usuario editara la cuenta admin**.

**✅ APLICADO (4 cambios):**
1. `rolesEdit()`: dos colecciones separadas — `$roles` (todos, para los checkboxes Spatie) y `$rolesGestion` (`whereNotIn('name', ['admin'])`, para el `<select>` de `role_id`). Antes filtraba solo cuando el admin se editaba a sí mismo.
2. `rolesUpdate()`: validación de servidor (el `<select>` filtrado no impide un POST manipulado) — `role_id` = id del rol `admin` lanza `Exception` y cae en el `catch` existente con su `Log::error`.
3. Guard de la cuenta de sistema en `rolesUpdate`, `equiposUpdate` y `tareasUpdate`: se eliminó la segunda condición; ahora bloquea a **cualquier** actor sobre `username === 'admin'`.
4. `roles-edit.blade.php`: el `<select>` itera `$rolesGestion`; `mainRole->name` → `mainRole?->name`; `toggleRole()` ya no propaga al `role_id` cuando el rol marcado es `admin` (ese JS era el puente que fusionaba los dos ejes en la UI).

5. **Segunda vuelta (mismo día): el rol `admin` pasa a ser EXCLUYENTE.** Por auditoría deben poder existir al menos dos administradores, así que no se reserva a la cuenta de sistema; lo que se impone es que `admin` no coexista con otro rol Spatie. `rolesUpdate()`: rechaza `roles[]` con `admin` + otros; `role_id` deja de ser `required` cuando se marca `admin` (`Rule::requiredIf`) y el servidor **impone `role_id = 1`** en ese caso. `roles-edit.blade.php`: `excluirAdministrador()` desmarca los demás roles al marcar `admin` (y viceversa) e inhabilita el `<select>`; en el render el `<select>` nace `disabled` si el usuario ya es `admin`.

Verificado con los 4 casos de `rolesUpdate` (dentro de transacción revertida): `admin+receptor` → rechaza "no puede combinarse"; `receptor` con `role_id=1` → rechaza "no es un papel de la gestión"; `solo admin` sin `role_id` → acepta e impone `role_id=1`; `solo receptor` con `role_id=3` → acepta. Render: `<select>` `disabled` para `admin`, habilitado para `cpleitez`, checkboxes con los 4 roles en ambos. `php artisan test` 29 passed / 3 skipped.

⚠️ **Gotcha del harness de prueba:** al invocar el controlador en bucle desde tinker, el flash `error` de `back()->with()` sobrevive entre iteraciones y hace parecer que un caso válido fue rechazado. Hay que `session()->forget(['error','success'])` antes de cada caso.

6. **La cuenta `admin` es la cuenta de rescate (break-glass): irrompible desde `/accounts`.** Su razón de existir es que si los administradores titular y suplente pierden su acceso —por error de sistema o de usuario— quede una vía de entrada. Para eso tiene que ser inmutable desde la app, y no lo era: `activate()` traía la condición invertida (`$user->username === 'admin' && auth()->user()->username === 'admin'`), que bloqueaba solo el caso inofensivo —el admin desactivándose a sí mismo— y dejaba abierto el peligroso: **otro administrador podía desactivarla**, y `Fortify::authenticateUsing` rechaza el login con `activo = false` → los tres accesos perdidos a la vez. `update()` no tenía guard alguno: cualquier administrador podía cambiarle `name`, `email` y `oficina_id`, y el correo es el canal de recuperación de contraseña.

Ambos métodos reciben ahora el guard de una sola condición (`$user->username === 'admin'`) + `Log::error`, igual que `rolesUpdate`, `equiposUpdate` y `tareasUpdate`. Cobertura completa de la cuenta de sistema: `update`, `rolesUpdate`, `equiposUpdate`, `tareasUpdate`, `activate` y `destroy`. Verificado actuando como un segundo administrador (`cpleitez`) contra la cuenta `admin`, en transacción revertida: ambos BLOQUEAN, `activo` y `email` intactos.

**Distinción que motivó el cierre** (la misma del 2026-07-26 con el IDOR): el filtro de `index()` que oculta la fila `admin` es una cláusula SQL, pero protege la **navegación**, no el objeto. `update` y `activate` son rutas propias con model binding (`accounts/update/{user}`, `accounts/activate/{user}`): se alcanzan por id sin pasar por la lista. La protección tiene que estar en el endpoint.

**Estado de los ejes tras la corrección del usuario** (`admin.role_id` devuelto a `1`, rol Spatie `admin` retirado a `cpleitez`): alineación 1:1 entre acceso y gestión — `admin`(1/[admin]), `maragon`(2/[cliente]), `cpleitez`(3/[receptor]), `hseldom`(4/[operador]). El sorteo del génesis en la oficina 2 devuelve solo `cpleitez`. Hoy solo la cuenta `admin` tiene acceso `role:admin`; se pueden crear más administradores desde `/accounts`, y cada uno quedará con `role_id = 1` (fuera del flujo) y sin ningún otro rol Spatie.

**Pendientes registrados, NO reparados:**
- `confirmar` es un permiso **inalcanzable**: solo lo tiene el rol Spatie `admin`, a quien `recepcion.propia` bloquea. Hoy latente porque `solicitud_tarea` de "Orden de compra" contiene solo Revisión, Confirmación y Descarga. Si entran Pago o Entrega, nadie podrá ejecutarlas. Salida probable: dar `confirmar` al receptor.
- `dibujarTareas` (`solicitudes.blade.php:637-700`) pinta los checkboxes sin `@can` → la UI ofrece acciones que el backend responde con 403.
- **Habilidades sembradas hoy** (`tarea_user`): `cpleitez` → Revisión, Descarga; `hseldom` → Confirmación; `maragon` y `admin` → ninguna. Recordar que la `Actividad` nace solo si la tarea está en `solicitud_tarea` **Y** en `tarea_user`.
