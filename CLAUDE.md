# gestor-de-tareas

<!-- BEGIN REGLAS GLOBALES -->
<!-- version: 13bd67d - 2026-07-14 15:10 - generado por sync-reglas.ps1 - NO editar a mano -->

## Reglas globales

Reglas comunes a todos mis proyectos. Se sincronizan desde el repo `estandares` (carpeta `ia/`). No las edites dentro de un proyecto: se sobrescriben en cada sync. Para cambiarlas, edita `estandares/ia/CLAUDE.global.md`.

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

- 

## Memoria

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
