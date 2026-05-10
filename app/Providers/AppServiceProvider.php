<?php

namespace App\Providers;

use Illuminate\Support\Facades\URL;
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
        // Khi APP_URL la https (vi du khi chay sau ngrok HTTPS proxy),
        // ep tat ca URL helper (asset, url, route) sinh URL https.
        // Tranh loi mixed content browser block CSS/JS/anh khi load tu may khac.
        if (str_starts_with(config('app.url', ''), 'https://')) {
            URL::forceScheme('https');
        }
    }
}
