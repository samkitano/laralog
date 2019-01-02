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
        $this->registerHelpers();
        $this->registerPublishing();
        $this->registerFacades();
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

    /**
     * Register helper files
     */
    protected function registerHelpers()
    {
        $helper = __DIR__.DIRECTORY_SEPARATOR.'helpers.php';

        if (file_exists($helper)) {
            require_once $helper;
        }
    }
}
