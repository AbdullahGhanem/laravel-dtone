<?php

namespace Ghanem\Dtone;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class DtoneServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/dtone.php', 'dtone');

        $this->app->bind('ghanem-dtone', function () {
            return new DtoneController;
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../config/dtone.php' => config_path('dtone.php'),
        ], 'config');

        $this->registerWebhookRoute();
    }

    /**
     * Register the webhook route if enabled.
     *
     * @return void
     */
    protected function registerWebhookRoute()
    {
        $path = config('dtone.webhook_path', 'dtone/webhook');
        $middleware = config('dtone.webhook_middleware', []);

        if ($path) {
            Route::post($path, [Http\Controllers\WebhookController::class, 'handle'])
                ->middleware($middleware)
                ->name('dtone.webhook');
        }
    }
}
