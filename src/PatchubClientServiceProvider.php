<?php

namespace Patchub\Client;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use Patchub\Client\View\Components\Bell;

class PatchubClientServiceProvider extends ServiceProvider
{
    /**
     * Enregistre les liaisons dans le conteneur de service (avant que tout soit "démarré").
     */
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/patchub-client.php', 'patchub-client');
    }

    /**
     * Démarre le package une fois que tous les providers sont enregistrés.
     */
    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__.'/../routes/webhook.php');
        $this->loadRoutesFrom(__DIR__.'/../routes/web.php');
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'patchub-client');

        Blade::component('patchub-bell', Bell::class);

        $this->publishes([
            __DIR__.'/../config/patchub-client.php' => config_path('patchub-client.php'),
        ], 'patchub-client-config');

        $this->publishes([
            __DIR__.'/../resources/css/patchub.css' => resource_path('css/patchub.css'),
        ], 'patchub-client-css');

        $this->publishes([
            __DIR__.'/../resources/views/components/bell.blade.php' => resource_path('views/vendor/patchub-client/components/bell.blade.php'),
        ], 'patchub-client-views');
    }
}
