# Memoria del proyecto — Gestor de Tareas

> Archivo de memoria activo para Claude Code. Se actualiza con cada sesión de trabajo.
> Última actualización: 2026-05-16

---

## Usuario

- **Nombre:** Carlos Pleitez
- **Email:** cpleitez.2024@gmail.com
- **Rol:** Desarrollador principal / propietario del proyecto
- **Estilo de trabajo:** prefiere trabajar paso a paso (máx. 3 pasos a la vez), necesita confirmación antes de crear/eliminar archivos o métodos, respuestas en español, concisas y sin emojis innecesarios.

---

## Stack técnico

- **Backend:** PHP 8.1, Laravel 10, Jetstream (Livewire), Sanctum, Fortify
- **Autorización:** Spatie Laravel Permission — roles: `superadmin` (bypass total via `Gate::before`), `administrar`, `gestionar`, `tienda`
- **Frontend:** Blade + Tailwind 3 + Bootstrap/jQuery heredado en `public/app-assets/`
- **Base de datos:** MySQL (Laragon local)
- **Tests:** PHPUnit 10 contra la BD configurada en `.env`
- **Linter:** Laravel Pint
- **Email:** SendGrid vía `s-ichikawa/laravel-sendgrid-driver`
- **Assets:** Vite compila `resources/css|js`; assets pesados en `public/app-assets/`

---

## Dominio del proyecto

- **Solicitud** = tabla `atenciones` + hijas: `recepciones`, `actividades`, `ordenes`, `detalles`. Aparecen como tarjetas en el Kanban.
- **Tableros Kanban:** Recibida → En progreso → Resuelta
- **Recepción** = copia de solicitud (`recepciones`, PK propia). Flujo: `cliente → receptor → operador`
- **Usuario propietario** = `recepciones.destino_user_id` (relación `usuarioDestino()` en el modelo)
- **Trazas/tracking:** Solicitud, Revisión, Verificación física, Descarga del Stock, Entrega del producto
- **Impulso** = avance que mueve una solicitud entre tableros
- **Tarea** = parte integral procesada por un participante

---

## Rama activa

- **Rama:** `seguridad`
- **Trabajo activo:** módulo de seguridad — componentes Blade en `resources/views/components/security/` documentados en `docs/componentes-seguridad.md`

---

## Reglas de colaboración (resumen operativo)

1. Solo probar en la pestaña del navegador activa cuando el usuario lo pida explícitamente.
2. No modificar código que no se haya pedido expresamente.
3. Confirmar antes de crear, eliminar archivos, métodos o funciones nuevas.
4. Máximo 3 pasos a la vez; esperar confirmación antes de avanzar.
5. Nunca hardcodear datos en el frontend — los datos de prueba vienen de seeders.
6. Usar `console.log` para debug en frontend; `Log::` en controladores.
7. No agregar features no solicitadas — hacer sugerencia y esperar decisión.
8. Código limpio, sin artificios, reutilizando servicios y estilos existentes.
9. Responder siempre en español.

---

## Versión actual

`config/app.php` → `version`: `1.5.x` (usar `php artisan version:update patch|minor|major`)

---

## Notas de sesiones anteriores

<!-- Agregar aquí notas relevantes de cada sesión -->
- 2026-05-16: Se creó este archivo de memoria a petición del usuario. La rama activa es `seguridad`.
