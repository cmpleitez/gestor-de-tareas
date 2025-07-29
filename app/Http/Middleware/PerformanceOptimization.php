<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PerformanceOptimization
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Aplicar configuraciones de rendimiento PHP
        $this->applyPhpSettings();
        
        // Configurar headers de respuesta para optimizar
        $response = $next($request);
        
        // Agregar headers de optimización
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
        $response->headers->set('X-XSS-Protection', '1; mode=block');
        
        // Configurar cache para recursos estáticos
        if ($this->isStaticResource($request)) {
            $response->headers->set('Cache-Control', 'public, max-age=31536000');
        }
        
        return $response;
    }
    
    /**
     * Aplicar configuraciones PHP para optimizar rendimiento
     */
    private function applyPhpSettings(): void
    {
        // Configuraciones críticas para prevenir timeouts
        ini_set('max_execution_time', config('performance.php.max_execution_time', 300));
        ini_set('memory_limit', config('performance.php.memory_limit', '1024M'));
        ini_set('max_input_time', config('performance.php.max_input_time', 300));
        ini_set('default_socket_timeout', config('performance.php.default_socket_timeout', 60));
        
        // Configuraciones de sesión
        ini_set('session.gc_maxlifetime', config('performance.php.session.gc_maxlifetime', 1440));
        ini_set('session.cookie_lifetime', config('performance.php.session.cookie_lifetime', 0));
        
        // Configuraciones de upload
        ini_set('post_max_size', config('performance.php.post_max_size', '256M'));
        ini_set('upload_max_filesize', config('performance.php.upload_max_filesize', '256M'));
        ini_set('max_file_uploads', config('performance.php.max_file_uploads', 20));
    }
    
    /**
     * Verificar si la request es para un recurso estático
     */
    private function isStaticResource(Request $request): bool
    {
        $path = $request->path();
        $staticExtensions = ['css', 'js', 'png', 'jpg', 'jpeg', 'gif', 'svg', 'ico', 'woff', 'woff2', 'ttf', 'eot'];
        
        foreach ($staticExtensions as $extension) {
            if (str_ends_with($path, '.' . $extension)) {
                return true;
            }
        }
        
        return false;
    }
} 