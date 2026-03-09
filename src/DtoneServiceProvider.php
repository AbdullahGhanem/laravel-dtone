<?php

namespace Ghanem\Dtone;

use Ghanem\Dtone\Console\DtoneBalanceCommand;
use Ghanem\Dtone\Console\DtoneCacheClearCommand;
use Ghanem\Dtone\Console\DtoneHealthCommand;
use Ghanem\Dtone\Console\DtoneProductsCommand;
use Ghanem\Dtone\Console\DtoneTransactionCommand;
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
        $this->registerCommands();
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

    /**
     * Register artisan commands.
     *
     * @return void
     */
    protected function registerCommands()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                DtoneBalanceCommand::class,
                DtoneProductsCommand::class,
                DtoneTransactionCommand::class,
                DtoneCacheClearCommand::class,
                DtoneHealthCommand::class,
            ]);
        }
    }
}
