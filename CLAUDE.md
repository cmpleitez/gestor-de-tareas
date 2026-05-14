# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Reglas del proyecto (`.agent/rules/reglas.md`)

Estas reglas tienen `trigger: always_on` y son obligatorias. Resumen operativo:

⛑️REGLAS:
1. Cuando diga "pruebalo" debes probar en la pestaña del navegador que te dejé activa no crees una nueva pestaña ni lo hagas en otra pestaña, e importante no pruebes si no te lo pido, mejor preguntame para autorizarlo
2. Ante cualquier peticion de mi parte: trabaja primero en la pestaña activa, si no es posible consultame
3. Cuando comentes una línea o una serie de líneas hazlo a la derecha de la primera línea de bloque
4. Escribe para mi código limpio con buenas practicas y el uso constante de estandares
5. Cuando escribas código para mi, no uses de artificios y no hardcorees codigo fuente
6. Compacta servicios y reutilizar estilos existentes, antes de repetir y escribir código desorganizadamente
7. Respondeme con las 2 mejores opciones cuando sea necessario, mientras solo muestra la opcion recomendada se precisa, acusiosa y consisa
8. Trabajemos paso a paso: no me des todo el procedimiento, sino únicamente tres pasos a la vez para no perder el hilo, luego los trabajamos correlativa y detalladamente hasta resolverlos, no avances de paso hasta que terminemos el paso en proceso, dime los nombres completos y exactos de las opciones, su consecutividad y su ubicacion en pantalla.
9. Cuando yo te diga la frase clave "Iniciemos una configuración", preguntame en que idioma necesito los nombres de las opciones, las distintas herramientas que se configuran a veces están en español y la mayoria de veces en inglés.
10. Nunca agregues datos hardcoreados en el frontend, los datos de pueba tendrán origen en los seeders, lo cual indica que los datos se leerán siempre de las tablas de la base de datos.
11. Cuando vayas a crear un archivo, metodo, funcion o similar nuevo(a) o eliminar uno existente debes pedirme confirmación.
12. Consulta siempre que necesites cambiar algo que no te he pedido expresamente en el chat, sigue las reglas del proyecto, nunca actues independientemente sin pedir mi confirmación
13. No sigas otras reglas que no sean las reglas del proyecto, cuando necesites seguir reglas externas pídeme confirmación
14. Cuando hayan comandos "Return" o "dd" en el codigo no lo tomes como error, porque lo he escrito yo para depurar, cuando te pida "pruebalo" entonces pideme que los retire en el caso que no los haya retirado.
15. No modifiques código cuando no te lo he pedido expresa y directamente
16. Respóndeme siempre en español
17. No realices pruebas proactivas: Nunca ejecutes herramientas de navegación o pruebas automáticas por cuenta propia. Entra en fase de VERIFICACIÓN únicamente para documentar el trabajo realizado o cuando yo utilice explícitamente las frases clave de las reglas 1.
18. Usa el console.log en lugar de ensuciar el frontend con funcionalidades de debuggin y usal el "Log::" en el caso de controladores, aparte puede recomendar otras formas de debugging que no ensucien el proyecto
19. No agregues cosas que no te he pedido en su lugar hasme la sugerencia para yo decidir
20. Cuando diga "cm" pideme la ruta donde se va realizar una operación manual, luego que te entrega la ruta dirigeme al controlador y a la vista de esa ruta.
    
💡CONCEPIOS:
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

## Comandos

PHP 8.1 estricto (ver `composer.json` → `platform`). Stack Windows/Laragon.

```bash
# Dependencias
composer install
npm install

# Desarrollo (Vite + Laravel)
php artisan serve
npm run dev

# Producción de assets
npm run build

# Tests (PHPUnit 10, configurado en phpunit.xml)
php artisan test
php artisan test --filter=NombreDelTest        # un test
vendor/bin/phpunit tests/Feature/AuthenticationTest.php

# Linter (Laravel Pint)
vendor/bin/pint
vendor/bin/pint --test                          # solo verificar

# Importar catálogo masivo desde docs/formato-importacion-full.xlsx
php artisan db:importar "NombreOficina"

# Subir versión en config/app.php
php artisan version:update patch                # | minor | major
```

Nota: `phpunit.xml` tiene comentadas las líneas de `DB_CONNECTION=sqlite/:memory:`, así que los tests corren contra la base configurada en `.env`. Si hace falta aislar pruebas, descomentarlas temporalmente.

## Arquitectura

Laravel 10 + Jetstream (Livewire stack) + Sanctum + Fortify + Spatie Permission + Tailwind 3. Frontend con Bootstrap/jQuery heredado en `public/app-assets/` más componentes Tailwind/Blade.

### Dominio (vocabulario obligatorio del proyecto)

Definido en `.agent/rules/reglas.md`. Las relaciones están en `app/Models/`:

- **Solicitud** = registro central, se materializa en la tabla `atenciones` y se dispersa a sus tablas hijas: `recepciones`, `actividades`, `ordenes` y `detalles`. Visualmente son tarjetas en el Kanban.
- **Tableros** = tres estados de la solicitud: Recibida, En progreso, Resuelta.
- **Recepción** = una copia de la solicitud (`recepciones`, PK propia). El **flujo** es `cliente → receptor → operador`; cada paso crea una nueva `Recepcion` con `origen_user_id`/`destino_user_id`.
- **Usuario propietario** de una recepción = `recepciones.destino_user_id` (referenciado como `usuarioDestino()` en el modelo).
- **Trazas/tracking** = nombres de tareas registradas: Solicitud, Revisión, Verificación física, Descarga del Stock, Entrega del producto.
- **Impulso** = avance que mueve una solicitud entre tableros del Kanban.
- **Tarea** = parte integral de la solicitud que procesa una persona participante.

Los nombres de tablas y modelos están en español (`Atencion`, `Recepcion`, `Solicitud`, `Tarea`, `Oficina`, `Producto`, `Kit`, `Stock`, `Movimiento`, etc.). Atencion usa `string` como `keyType`; Recepcion también.

### Autorización

- **Spatie Laravel Permission** (`spatie/laravel-permission`) maneja roles y abilities.
- En `app/Providers/AuthServiceProvider.php` hay un `Gate::before` que da bypass total al rol **`superadmin`**. Tenerlo en cuenta antes de proponer policies.
- Las rutas web están agrupadas por capacidad mediante middleware `can:*`:
  - `can:administrar` → CRUDs de catálogo (user, marca, modelo, tipo, producto, kit, solicitud, parametro). Cada acción interna se protege además con `can:ver|crear|editar|eliminar|activar|asignar`.
  - `can:gestionar` → flujo Kanban en `RecepcionController` (`recepcion.*`): asignar, avanzar estado, orden de compra, stock, pago, entrega, historial.
  - `can:tienda` → módulo `TiendaController` (carrito, solicitudes, kits, items).

### Estructura

- `app/Http/Controllers/` — controladores tradicionales por recurso (Producto, Recepcion, Solicitud, Tienda, etc.). Auth en `Auth/`.
- `app/Livewire/` — Livewire 3, actualmente solo `CheckNotifications` para notificaciones.
- `app/Services/` — lógica reutilizable: `GestionService` (participantes/flujo), `StockService`, `ImportCatalogService`, `KeyMaker`/`KeyRipper`, `CorrelativeIdGenerator`, `ImageWeightStabilizer`.
- `app/Actions/Fortify` y `app/Actions/Jetstream` — hooks de Fortify/Jetstream personalizados.
- `database/migrations/` — orden controlado por prefijo numérico (010, 020, …, 190) para tablas base; las migraciones nuevas usan el formato fecha estándar de Laravel.
- `database/seeders/` — `DatabaseSeeder`, `UserSeeder`, `InventarioSeeder`. Los datos de prueba viven aquí (regla del proyecto).
- `resources/views/` — Blade clásico. Carpeta `modelos/` agrupa vistas CRUD por recurso (`equipo`, `kit`, `marca`, `modelo`, `parametro`, `producto`, `recepcion`, `solicitud`, `tipo`, `user`). `reportes/` contiene reportes (historial de transacciones). Componentes Blade en `resources/views/components/` (incluida la suite `security/` documentada en `docs/componentes-seguridad.md`).
- `routes/web.php` — toda la app autenticada bajo `auth:sanctum + jetstream.auth_session + verified` y luego dividida por los tres grupos de capacidad arriba.
- Frontend assets pesados (Bootstrap, DataTables, FontAwesome, plantillas) viven en `public/app-assets/`. Vite solo compila `resources/css|js`.

### Mailing y notificaciones

- Driver SendGrid vía `s-ichikawa/laravel-sendgrid-driver` (configurado por `.env`).
- Tabla `notifications` (Laravel default) y job queue (`jobs` table) presentes — `QUEUE_CONNECTION=sync` en `.env.example`, así que sin worker en local salvo que se cambie.

## Documentación interna útil

- `docs/Estandares del proyecto.md` — convenciones (paleta de colores `text-*` / `bg-*` / `btn-*`, helper de moneda `formatCurrency()` en `public/app-assets/js/helpers.js`, inicialización de validación `jqBootstrapValidation` cuando una vista define su propia sección `js`, técnica para volcar arrays con `Log::info` desde Blade).
- `docs/componentes-seguridad.md` — catálogo de componentes Blade en `resources/views/components/security/` (metric-card, chart-card, recent-events, suspicious-ips, dashboard-header, empty-state, risk-badge, notification-system, js-utilities, security-styles) y utilidades JS bajo `SecurityUtils`. Trabajo activo en la rama `seguridad`.
- `docs/seguimiento.md` — bitácora de avance del proyecto.

## Contexto de sesiones anteriores

### Rama `seguridad` — protección del usuario admin

**Completado:**
- Eliminadas todas las referencias a `superadmin` en `UserController`, `RegisterController` y `routes/web.php` — reemplazadas por `admin`.
- `destroy()` en `UserController`: bloquea eliminación del usuario con rol `admin` antes de cualquier otra validación.
- `resources/views/modelos/user/index.blade.php`: botones del tablero de control del admin ahora se muestran **deshabilitados** (no ocultos) — `pointer-events:none`, borde e ícono en tonos grises/tenues.

**Colores acordados para botones deshabilitados del admin (`index.blade.php` L86-102):**
- Borde botones azules (habilidades/equipos/roles): `border-color:#395e86` inline (5% más claro que `#2c5179`)
- Borde botón eliminar: `border-color:#e44b4b` inline (5% más claro que `#d73e3e`)
- Ícono interior todos: `color:#adb5bd` en el `<i>` (no en el span)

**Pendiente:**
- Switch de activación: deshabilitar visualmente el toggle del admin en la vista
- Respaldo backend: verificar que endpoints `tareas-update`, `equipos-update`, `roles-update` bloqueen acceso al admin vía inyección directa

## Convenciones a respetar

- Recursos en español (`marca`, `modelo`, `tipo`, `producto`, `kit`, `solicitud`, `parametro`); rutas también en español. La excepción son las rutas internas de `recepcion.*` y `tienda.*` cuyas URIs están en inglés (`teams`, `operators`, `next-step`, `purchase-order`, …) pero el `name()` permanece en español.
- Versionado de la app en `config/app.php` clave `version` (actualmente `1.5.x`); usar `php artisan version:update` para mantenerlo.
- No commitear `.env`, `*.sql`, `*.sqlite`, `*.key`, `*.pem` (ver `.gitignore`).
