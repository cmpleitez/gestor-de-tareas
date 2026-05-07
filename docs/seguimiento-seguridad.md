
# Seguimiento de hardening de seguridad (rama `seguridad`)

Documento de continuidad entre sesiones. Marcar cada punto como `- [x]` al completarlo.

## Decisión pendiente

- [x] Elegir cómo cubrir los headers HTTP de seguridad: paquete `bepsvpt/secure-headers` (Composer, autoregistra middleware) **vs** configuración en nginx/Apache durante el deploy. _Decidido: se delega al servidor nginx en producción. No requiere cambios en el proyecto._

## Faltantes según el estándar Laravel + Jetstream + Fortify + Sanctum

- [x] Descomentar `\Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class` en `app/Http/Kernel.php:42` (sesión stateful para clientes SPA/Livewire que usen Sanctum).
- [x] Descomentar `\App\Http\Middleware\TrustHosts::class` en `app/Http/Kernel.php:16` y registrar los hosts válidos para producción. _Descomentado. `TrustHosts.php` ya tenía `allSubdomainsOfApplicationUrl()` — lee de `APP_URL` en `.env`._
- [x] Forzar HTTPS en `App\Providers\AppServiceProvider` con `URL::forceScheme('https')` cuando `app()->environment('production')`. _Agregado en `boot()` con guard de entorno._
- [x] Endurecer cookies de sesión: `SESSION_SECURE_COOKIE=true` y `SESSION_SAME_SITE=strict` en `.env` de producción; revisar `config/session.php` (`secure`, `same_site`, `http_only`). _`same_site` ahora lee de `.env` con default `lax`; `http_only` ya era `true`; variables agregadas al `.env` local con valores de desarrollo. En producción cambiar a `SESSION_SECURE_COOKIE=true` y `SESSION_SAME_SITE=strict`._
- [~] Resolver los headers HTTP de seguridad pendientes (`X-Frame-Options`, `X-Content-Type-Options`, `Strict-Transport-Security`, `Content-Security-Policy`, `Referrer-Policy`, `Permissions-Policy`) según la decisión de arriba. _No procede en el proyecto: se delega a la configuración nginx del servidor de producción._

## Sobrantes / fuera del estándar a eliminar o normalizar

- [x] Eliminar el recurso `app/Http/Middleware/PerformanceOptimization.php` y sus dependencias y asociados (exceptuando alguna cosa que afecte el funcionamiento del proyecto web app actual: validar primero que ningún `ini_set()` ni `Cache-Control` que aplica esté siendo requerido; si lo está, moverlo a su ubicación correcta — `php.ini`, `config/`, o nginx). _Eliminados: `app/Http/Middleware/PerformanceOptimization.php` y `config/performance.php` (ambos huérfanos, sin lectores)._
- [x] Eliminar el bypass global del rol `superadmin` en `app/Providers/AuthServiceProvider.php:25-27` (`Gate::before`). Contradice la pauta de no usar el rol como criterio. _Eliminado. `superadmin` recibe todos los permisos vía `givePermissionTo(Permission::all())` en el seeder._
- [x] Eliminar el doble `can` anidado en `routes/web.php` (grupo `can:administrar|gestionar|tienda` + `can:atómico` por ruta); dejar un único `can` específico por ruta. _Eliminados los tres grupos externos. Los permisos `ver` y `asignar` se dividieron en `ver-catalogo`/`ver-recepcion` y `asignar-solicitud`/`asignar-recepcion`. Las 5 rutas tienda sin guard recibieron `can:ver-carrito`, `can:enviar-carrito` o `can:ver-tienda`._
- [x] Eliminar los permisos macro `administrar`, `gestionar`, `tienda` del seeder (`database/seeders/UserSeeder.php`) y de los grupos de rutas, una vez resuelto el punto anterior. _Eliminados del array de permisos y de todos los roles._
- [x] Enmascarar las URIs administrativas en español que exponen el modelo: `/user`, `/marca`, `/modelo`, `/tipo`, `/producto`, `/kit`, `/solicitud`, `/parametro`. Mantener los `name()` en español. _Prefijos cambiados a: `/accounts`, `/brands`, `/items`, `/categories`, `/products`, `/bundles`, `/request`, `/settings`._
- [x] Unificar la asignación de rol `'Cliente'` → `'cliente'` en `app/Actions/Fortify/CreateNewUser.php:53` (Spatie es case-sensitive y el seeder solo crea `'cliente'`; hoy los registros vía Fortify quedan sin rol válido). _Corregido en líneas 53 y 56._
- [x] Eliminar el permiso huérfano `autorizar`. _No era huérfano: las vistas usaban `@can('autorizar')` para mostrar el toggle de activación, inconsistente con la ruta que usaba `can:activar`. Solución: reemplazado `@can('autorizar')` → `@can('activar')` en 8 vistas (`equipo`, `parametro`, `marca`, `modelo`, `user`, `tipo`, `kit`, `producto`); eliminado `autorizar` del array de permisos y del rol `admin` en `UserSeeder.php`._
- [x] Eliminar o conectar los roles huérfanos `supervisor` y `gestor` (`UserSeeder.php:143-144`), creados sin permisos y sin referencias en código. _Eliminadas las dos líneas del seeder. Sin impacto en vistas ni rutas._
- [x] Reemplazar el filtro frágil `whereNotIn('id', ['1'])` en `app/Http/Controllers/UserController.php:23` por exclusión basada en rol/permiso (no por id literal del superadmin). _Reemplazado por `whereDoesntHave('roles', fn($q) => $q->where('name', 'superadmin'))`._
- [~] Los aliases Spatie en `app/Http/Kernel.php:67-69` (`role`, `permission`, `role_or_permission`) se conservan intencionalmente: se usarán en una fase próxima para protección de grupos de rutas por rol.

## Nuevos pendientes:

- [x] Definir los grupos de rutas y los roles que los protegen (ej. `role:admin`, `role:operador`).
- [x] Aplicar el middleware `role:` a los grupos correspondientes en `routes/web.php`. Los 8 grupos administrativos (accounts, brands, items, categories, products, bundles, request, settings) usan `role:admin|superadmin`. Lógica del filtro: ambos roles pasan (OR); `superadmin` queda exceptuado por su propio rol, no por el de `admin`; no se le exige tener rol `admin` para acceder.
- [x] Verificar que los aliases Spatie en `Kernel.php` (`role`, `permission`, `role_or_permission`) estén alineados con los roles definidos en el seeder. _Alineados: roles del seeder son `superadmin`, `admin`, `cliente`, `receptor`, `operador`; aliases apuntan a las clases correctas de Spatie._
- [x] Revisar que el comportamiento ante acceso no autorizado (redirect/403) sea el correcto para cada grupo. _Creada vista `errors/403.blade.php` con estilo del proyecto. `Handler.php` captura `UnauthorizedException` de Spatie, registra en log con nombre del usuario, roles y URL intentada, y retorna la vista 403._

## Cómo retomar en otro dispositivo

1. `git pull` sobre la rama `seguridad`.
2. Abrir Claude Code en la raíz del repo.
3. La sesión leerá `CLAUDE.md` y este archivo automáticamente; pedir continuar desde el primer punto sin marcar.
