<?php

namespace TrAddress;

use Illuminate\Support\ServiceProvider;

class TrAddressServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->publishes([
            __DIR__.'/database/migrations' => database_path('migrations'),
        ], 'migrations');

        $this->publishes([
            __DIR__.'/database/seeders' => database_path('seeders'),
        ], 'seeders');

        $this->publishes([
            __DIR__.'/../config/traddress.php' => config_path('traddress.php'),
        ], 'traddress-config');
    }

    public function register()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                \TrAddress\Console\Commands\ImportTrAddress::class,
                \TrAddress\Console\Commands\PublishJsonData::class,
            ]);
        }
    }
} 