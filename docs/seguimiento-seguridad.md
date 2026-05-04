# Seguimiento de hardening de seguridad (rama `seguridad`)

Documento de continuidad entre sesiones. Marcar cada punto como `- [x]` al completarlo.

## Decisión pendiente

- [ ] Elegir cómo cubrir los headers HTTP de seguridad: paquete `bepsvpt/secure-headers` (Composer, autoregistra middleware) **vs** configuración en nginx/Apache durante el deploy. Ningún componente oficial de Laravel/Jetstream/Fortify/Sanctum los provee.

## Faltantes según el estándar Laravel + Jetstream + Fortify + Sanctum

- [ ] Descomentar `\Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class` en `app/Http/Kernel.php:42` (sesión stateful para clientes SPA/Livewire que usen Sanctum).
- [ ] Descomentar `\App\Http\Middleware\TrustHosts::class` en `app/Http/Kernel.php:16` y registrar los hosts válidos para producción.
- [ ] Forzar HTTPS en `App\Providers\AppServiceProvider` con `URL::forceScheme('https')` cuando `app()->environment('production')`.
- [ ] Endurecer cookies de sesión: `SESSION_SECURE_COOKIE=true` y `SESSION_SAME_SITE=strict` en `.env` de producción; revisar `config/session.php` (`secure`, `same_site`, `http_only`).
- [ ] Resolver los headers HTTP de seguridad pendientes (`X-Frame-Options`, `X-Content-Type-Options`, `Strict-Transport-Security`, `Content-Security-Policy`, `Referrer-Policy`, `Permissions-Policy`) según la decisión de arriba.

## Sobrantes / fuera del estándar a eliminar o normalizar

- [ ] Eliminar el recurso `app/Http/Middleware/PerformanceOptimization.php` y sus dependencias y asociados (exceptuando alguna cosa que afecte el funcionamiento del proyecto web app actual: validar primero que ningún `ini_set()` ni `Cache-Control` que aplica esté siendo requerido; si lo está, moverlo a su ubicación correcta — `php.ini`, `config/`, o nginx).
- [ ] Eliminar el bypass global del rol `superadmin` en `app/Providers/AuthServiceProvider.php:25-27` (`Gate::before`). Contradice la pauta de no usar el rol como criterio.
- [ ] Eliminar el doble `can` anidado en `routes/web.php` (grupo `can:administrar|gestionar|tienda` + `can:atómico` por ruta); dejar un único `can` específico por ruta.
- [ ] Eliminar los permisos macro `administrar`, `gestionar`, `tienda` del seeder (`database/seeders/UserSeeder.php`) y de los grupos de rutas, una vez resuelto el punto anterior.
- [ ] Enmascarar las URIs administrativas en español que exponen el modelo: `/user`, `/marca`, `/modelo`, `/tipo`, `/producto`, `/kit`, `/solicitud`, `/parametro`. Mantener los `name()` en español.
- [ ] Unificar la asignación de rol `'Cliente'` → `'cliente'` en `app/Actions/Fortify/CreateNewUser.php:53` (Spatie es case-sensitive y el seeder solo crea `'cliente'`; hoy los registros vía Fortify quedan sin rol válido).
- [ ] Eliminar el permiso huérfano `autorizar` (sembrado en `UserSeeder.php:68` y asignado al rol `admin`, pero sin uso en ninguna ruta).
- [ ] Eliminar o conectar los roles huérfanos `supervisor` y `gestor` (`UserSeeder.php:143-144`), creados sin permisos y sin referencias en código.
- [ ] Reemplazar el filtro frágil `whereNotIn('id', ['1'])` en `app/Http/Controllers/UserController.php:23` por exclusión basada en rol/permiso (no por id literal del superadmin).
- [ ] Eliminar (o prohibir por convención) los aliases Spatie no utilizados en `app/Http/Kernel.php:67-69` (`role`, `permission`, `role_or_permission`); el proyecto solo usa `can:*`.

## Cómo retomar en otro dispositivo

1. `git pull` sobre la rama `seguridad`.
2. Abrir Claude Code en la raíz del repo.
3. La sesión leerá `CLAUDE.md` y este archivo automáticamente; pedir continuar desde el primer punto sin marcar.
