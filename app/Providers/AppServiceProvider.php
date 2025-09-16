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

    }
}
