<?php

namespace Eboost\Unoconv;

use Illuminate\Support\ServiceProvider;

class UnoconvServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../config/unoconv.php' => config_path('unoconv.php'),
        ], 'config');

    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/unoconv.php',
            'unoconv'
        );

        $this->app['unoconv'] = $this->app->share(function ($app) {
            return new Unoconv(config('unoconv'), $app['filesystem'], $app['queue']);
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['unoconv'];
    }
}
