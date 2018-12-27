<?php

namespace samkitano\Laralog;

use Illuminate\Support\ServiceProvider;

class LaralogServiceProvider extends ServiceProvider
{
    /**
     * Boot the service
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->registerPublishing();
            $this->registerFacades();
        }
    }

    /**
     * Register the service
     */
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/laralog.php', 'laralog'
        );
    }

    /**
     * Register Publishable items
     */
    protected function registerPublishing()
    {
        $this->publishes([
            __DIR__.'/../config/laralog.php' => config_path('laralog.php')
        ], 'laralog');
    }

    /**
     * Register the Facade
     */
    protected function registerFacades()
    {
        $this->app->singleton('Laralog', function ($app) {
            return new \samkitano\Laralog\Laralog();
        });
    }
}
