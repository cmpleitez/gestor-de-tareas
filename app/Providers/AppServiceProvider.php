<?php
namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Configurar timeouts más largos para evitar 504 Gateway Timeout
        config([
            'mail.mailers.smtp.timeout'            => 60,
            'mail.mailers.postmark.client.timeout' => 60,
            'mail.mailers.mailgun.client.timeout'  => 60,
            'mail.mailers.sendgrid.client.timeout' => 60,
        ]);

        // Configurar charset UTF-8 para evitar caracteres chinos en logs
        mb_internal_encoding('UTF-8');
        mb_http_output('UTF-8');
        mb_regex_encoding('UTF-8');

        // Configurar locale para español
        setlocale(LC_ALL, 'es_ES.UTF-8', 'es_ES', 'es');
    }
}
