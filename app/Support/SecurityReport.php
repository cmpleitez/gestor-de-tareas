<?php

namespace App\Support;

use App\Models\User;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

/**
 * Reporte de seguridad de SOLO LECTURA.
 * Solo consulta tablas existentes (SELECT/count), lee config()/rutas en runtime y,
 * para SSL, abre un socket TLS de lectura. No escribe, no migra, no ejecuta shell,
 * no toca .env/caché.
 */
class SecurityReport
{
    /** Nº de administradores esperado por diseño: titular + suplente. */
    private const ADMINS_ESPERADOS = 2;

    /** Empaqueta las capas del reporte para la vista. */
    public static function data(): array
    {
        $posture = self::posture();

        return [
            'kpis'    => self::kpis(),
            'posture' => $posture,
            'score'   => [
                'ok'    => count(array_filter($posture, fn ($i) => $i['estado'] === 'ok')),
                'total' => count($posture),
            ],
        ];
    }

    /** KPIs de postura de cuentas/accesos, desde la BD. */
    public static function kpis(): array
    {
        $admins        = User::role('admin')->count();
        $adminsSin2fa  = User::role('admin')->whereNull('two_factor_secret')->count();
        $sinVerificar  = User::whereNull('email_verified_at')->count();
        $adminsExtra   = max(0, $admins - self::ADMINS_ESPERADOS);
        $sinEnrolar    = User::doesntHave('roles')->count();
        $rolesSinPerm  = Role::doesntHave('permissions')->count();
        $permHuerfanos = Permission::doesntHave('roles')->count();

        return [
            [
                'clave' => 'admins_sin_2fa',
                'etiqueta' => 'Administradores sin 2FA',
                'valor' => (string) $adminsSin2fa,
                'detalle' => "de {$admins} administrador(es)",
                'nivel' => $adminsSin2fa === 0 ? 'ok' : 'danger',
            ],
            [
                'clave' => 'sin_verificar',
                'etiqueta' => 'Cuentas sin verificar',
                'valor' => (string) $sinVerificar,
                'detalle' => 'correo no confirmado',
                'nivel' => $sinVerificar === 0 ? 'ok' : 'warn',
            ],
            [
                'clave' => 'admins_adicionales',
                'etiqueta' => 'Admin adicionales',
                'valor' => (string) $adminsExtra,
                'detalle' => 'además de titular y suplente',
                'nivel' => $adminsExtra === 0 ? 'ok' : 'warn',
            ],
            [
                'clave' => 'sin_enrolar',
                'etiqueta' => 'Cuentas sin enrolar',
                'valor' => (string) $sinEnrolar,
                'detalle' => 'sin rol asignado',
                'nivel' => $sinEnrolar === 0 ? 'ok' : 'warn',
            ],
            [
                'clave' => 'roles_sin_permisos',
                'etiqueta' => 'Roles sin permisos',
                'valor' => (string) $rolesSinPerm,
                'detalle' => 'roles sin utilidad',
                'nivel' => $rolesSinPerm === 0 ? 'ok' : 'warn',
            ],
            [
                'clave' => 'permisos_huerfanos',
                'etiqueta' => 'Permisos huérfanos',
                'valor' => (string) $permHuerfanos,
                'detalle' => 'permisos sin rol',
                'nivel' => $permHuerfanos === 0 ? 'ok' : 'warn',
            ],
        ];
    }

    /** Checklist de postura (config/rutas/SSL), consciente del entorno. */
    public static function posture(): array
    {
        $esProd = config('app.env') === 'production';
        $notaEntorno = $esProd ? null : 'Correcto en local; revisar en producción.';
        $segunEntorno = fn (bool $ok) => $ok ? 'ok' : ($esProd ? 'danger' : 'warn');

        $sameSite = (string) config('session.same_site');
        $sslActivo = self::sslActivo();
        $sslVigente = self::sslVigente($sslActivo['https']);

        $appKey = (string) config('app.key');
        $appKeyOk = str_starts_with($appKey, 'base64:')
            ? strlen((string) base64_decode(substr($appKey, 7), true)) >= 32
            : strlen($appKey) >= 32;

        $corsOrigins = config('cors.allowed_origins'); // null si no se publicó config/cors.php
        $corsComodin = is_array($corsOrigins) && in_array('*', $corsOrigins, true);

        $mailer = (string) config('mail.default');
        $mailHost = (string) config("mail.mailers.{$mailer}.host");
        $smtpOk = $mailHost !== '';

        $dbCifrada = self::dbCifrada();

        return [
            [
                'clave' => 'app_debug',
                'etiqueta' => 'Modo de depuración',
                'actual' => config('app.debug') ? 'activado' : 'desactivado',
                'esperado' => 'desactivado',
                'estado' => $segunEntorno(! config('app.debug')),
                'nota' => config('app.debug') ? trim('APP_DEBUG expone trazas y datos sensibles. ' . ($notaEntorno ?? '')) : null,
            ],
            [
                'clave' => 'app_env',
                'etiqueta' => 'Entorno de producción',
                'actual' => (string) config('app.env'),
                'esperado' => 'production',
                'estado' => $esProd ? 'ok' : 'warn',
                'nota' => $esProd ? null : 'Entorno local (esperado en desarrollo).',
            ],
            [
                'clave' => 'csrf',
                'etiqueta' => 'Protección CSRF',
                'actual' => 'activa',
                'esperado' => 'activa',
                'estado' => 'ok',
                'nota' => 'Provista por el middleware web de Laravel.',
            ],
            [
                'clave' => 'cookie_same_site',
                'etiqueta' => 'Cookies SameSite',
                'actual' => $sameSite !== '' ? $sameSite : 'null',
                'esperado' => 'lax o strict',
                'estado' => in_array($sameSite, ['lax', 'strict'], true) ? 'ok' : 'warn',
                'nota' => in_array($sameSite, ['lax', 'strict'], true) ? null : 'SameSite mitiga CSRF entre sitios.',
            ],
            [
                'clave' => 'app_key',
                'etiqueta' => 'APP_KEY robusta',
                'actual' => $appKeyOk ? 'clave de ≥256 bits' : 'clave débil o ausente',
                'esperado' => 'clave de ≥256 bits',
                'estado' => $appKeyOk ? 'ok' : 'danger',
                'nota' => $appKeyOk ? null : 'APP_KEY cifra sesiones y firma datos; regenerar con php artisan key:generate.',
            ],
            [
                'clave' => 'cors',
                'etiqueta' => 'Uso de comodín en CORS',
                'actual' => $corsOrigins === null ? 'no configurado' : ($corsComodin ? 'comodín (*)' : 'orígenes explícitos'),
                'esperado' => 'orígenes explícitos',
                'estado' => $corsComodin ? 'danger' : 'ok',
                'nota' => $corsComodin ? 'Permitir cualquier origen (*) deja que sitios externos usen la sesión del usuario.' : null,
            ],
            [
                'clave' => 'hsts',
                'etiqueta' => 'HSTS habilitado',
                'actual' => 'no emitido', // el proyecto no define middleware de cabeceras de seguridad
                'esperado' => 'Strict-Transport-Security',
                'estado' => $segunEntorno(false),
                'nota' => 'Laravel no emite HSTS por defecto; en producción añadir el header para forzar HTTPS.',
            ],
            [
                'clave' => 'smtp',
                'etiqueta' => 'SMTP configurado',
                'actual' => $smtpOk ? "{$mailer}: {$mailHost}" : 'sin host',
                'esperado' => 'host configurado',
                'estado' => $smtpOk ? 'ok' : 'warn',
                'nota' => $smtpOk ? null : 'Sin SMTP, la recuperación de contraseña y los avisos no funcionan.',
            ],
            [
                'clave' => 'db_cifrada',
                'etiqueta' => 'Conexión cifrada a BD',
                'actual' => $dbCifrada ? 'cifrada (TLS)' : 'sin cifrar',
                'esperado' => 'cifrada (TLS)',
                'estado' => $dbCifrada ? 'ok' : 'danger',
                'nota' => $dbCifrada ? null : 'Sin cifrado, los datos viajan en claro entre la app y la base de datos.',
            ],
            [
                'clave' => 'ssl_activo',
                'etiqueta' => 'SSL activo',
                'actual' => $sslActivo['actual'],
                'esperado' => 'activo (https)',
                'estado' => $sslActivo['estado'] === 'ok' ? 'ok' : 'danger', // no protegido → grave
                'nota' => $sslActivo['nota'],
            ],
            [
                'clave' => 'ssl_vigente',
                'etiqueta' => 'SSL vigente',
                'actual' => $sslVigente['actual'],
                'esperado' => 'certificado vigente',
                'estado' => $sslVigente['estado'] === 'ok' ? 'ok' : 'danger', // no protegido → grave
                'nota' => $sslVigente['nota'],
            ],
        ];
    }

    /** ¿La conexión por defecto viaja cifrada? MySQL usa opciones PDO SSL; sqlsrv usa encrypt. */
    private static function dbCifrada(): bool
    {
        $conexion = (string) config('database.default');
        $config = (array) config("database.connections.{$conexion}", []);

        if (in_array($config['encrypt'] ?? null, ['yes', true, 1, '1'], true)) {
            return true;
        }

        $opciones = (array) ($config['options'] ?? []);
        foreach ([\PDO::MYSQL_ATTR_SSL_CA, \PDO::MYSQL_ATTR_SSL_CERT, \PDO::MYSQL_ATTR_SSL_KEY] as $atributo) { // Cualquiera de los tres implica handshake TLS
            if (! empty($opciones[$atributo])) {
                return true;
            }
        }

        return false;
    }

    /** ¿La app sirve por HTTPS? Solo lee config('app.url'), sin conexión de red. */
    private static function sslActivo(): array
    {
        $https = str_starts_with((string) config('app.url'), 'https');
        $esProd = config('app.env') === 'production';

        if (! $https) {
            return [
                'https' => false,
                'actual' => 'no activo (http)',
                'estado' => $esProd ? 'danger' : 'warn',
                'nota' => $esProd ? 'La app no usa HTTPS en producción.' : 'HTTPS no activo en local (revisar en producción).',
            ];
        }

        return ['https' => true, 'actual' => 'activo (https)', 'estado' => 'ok', 'nota' => null];
    }

    /** Vigencia del certificado. Solo tiene sentido si SSL está activo; abre un socket TLS de lectura, con timeout. */
    private static function sslVigente(bool $sslActivo): array
    {
        if (! $sslActivo) {
            return ['actual' => 'no aplica (sin HTTPS)', 'estado' => 'warn', 'nota' => 'No se puede verificar el certificado sin una conexión HTTPS activa.'];
        }

        $url = (string) config('app.url');
        $host = parse_url($url, PHP_URL_HOST);
        $port = parse_url($url, PHP_URL_PORT) ?: 443;

        try {
            $ctx = stream_context_create(['ssl' => ['capture_peer_cert' => true, 'verify_peer' => false, 'verify_peer_name' => false]]);
            $client = @stream_socket_client("ssl://{$host}:{$port}", $errno, $errstr, 3, STREAM_CLIENT_CONNECT, $ctx);

            if (! $client) {
                return ['actual' => 'certificado no verificable', 'estado' => 'warn', 'nota' => "No se pudo leer el certificado ({$errstr})."];
            }

            $params = stream_context_get_params($client);
            $cert = openssl_x509_parse($params['options']['ssl']['peer_certificate']);
            fclose($client);

            $validTo = $cert['validTo_time_t'] ?? null;
            $dias = $validTo ? (int) floor(($validTo - time()) / 86400) : null;

            if ($dias === null) {
                return ['actual' => 'vigencia desconocida', 'estado' => 'warn', 'nota' => null];
            }
            if ($dias <= 0) {
                return ['actual' => 'certificado vencido', 'estado' => 'danger', 'nota' => 'El certificado SSL está vencido.'];
            }
            if ($dias <= 15) {
                return ['actual' => "vence en {$dias} día(s)", 'estado' => 'warn', 'nota' => 'El certificado vence pronto; renovar.'];
            }

            return ['actual' => "vence en {$dias} día(s)", 'estado' => 'ok', 'nota' => null];
        } catch (\Throwable $e) {
            return ['actual' => 'certificado no verificable', 'estado' => 'warn', 'nota' => 'Error verificando el certificado.'];
        }
    }
}
