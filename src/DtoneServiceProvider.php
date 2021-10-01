<?php

namespace Ghanem\Dtone;

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
    }
}
