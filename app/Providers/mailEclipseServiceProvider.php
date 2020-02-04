<?php

namespace App\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class mailEclipseServiceProvider extends ServiceProvider
{
    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot()
    {
        Route::middlewareGroup('maileclipse', config('maileclipse.middlewares', []));

        $this->loadTranslationsFrom(base_path().'/resources/lang', 'maileclipse');
        $this->loadViewsFrom(base_path().'/resources/views', 'maileclipse');
        $this->registerRoutes();

        // Publishing is only necessary when using the CLI.
        if ($this->app->runningInConsole()) {
            $this->bootForConsole();
        }
    }

    /**
     * Register the package routes.
     *
     * @return void
     */
    private function registerRoutes()
    {
        Route::group($this->routeConfiguration(), function () {
            $routes_path = base_path().'/routes/mailable/routes.php';
            $this->loadRoutesFrom($routes_path);
        });
    }

    /**
     * Get the Telescope route group configuration array.
     *
     * @return array
     */
    private function routeConfiguration()
    {
        return [
            'namespace' => 'App\Http\Controllers\Mailable',
            'prefix' => config('maileclipse.path'),
            'middleware' => 'maileclipse',
        ];
    }

    /**
     * Register any package services.
     *
     * @return void
     */
    public function register()
    {
        $config_path = base_path().'/config/maileclipse.php';
        $this->mergeConfigFrom($config_path, 'maileclipse');

        // Register the service the package provides.
        $this->app->singleton('maileclipse', function ($app) {
            return new mailEclipse;
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['maileclipse'];
    }

    /**
     * Console-specific booting.
     *
     * @return void
     */
    protected function bootForConsole()
    {
        // Publishing the configuration file.
        $base_dir = base_path();
        $this->publishes([
            base_path().'/config/maileclipse.php' => config_path('maileclipse.php'),
        ], 'maileclipse.config');

        $this->publishes([
                base_path().'/public' => public_path('vendor/maileclipse'),
            ], 'public');

        $this->publishes([
                base_path().'/resources/views/templates' => $this->app->resourcePath('views/vendor/maileclipse/templates'),
            ], 'maileclipse.templates');
    }
}
