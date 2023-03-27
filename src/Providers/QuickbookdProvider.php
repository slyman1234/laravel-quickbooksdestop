<?php

namespace Sylvester\Quickbooks\Providers;

use Illuminate\Support\ServiceProvider;

class QuickbookdProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        //

        $this->publishes([
            __DIR__.'/../Config/quickbooks.php' => config_path('quickbooks.php'),
        ], 'config');

        $this->loadRoutesFrom(__DIR__.'/../routes/web.php');
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {

        $this->mergeConfigFrom(
            __DIR__.'/../Config/quickbooks.php', 'quickbooks'
        );

        $this->mergeConfigFrom(
            __DIR__.'/../Config/maps.php', 'quickbooks'
        );

        $this->app->make('Sylvester\Quickbooks\Controllers\QuickbooksdController');

       
    }
}