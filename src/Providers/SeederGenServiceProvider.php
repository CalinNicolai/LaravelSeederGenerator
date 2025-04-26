<?php

namespace CalinNicolai\Seedergen\Providers;

use CalinNicolai\Seedergen\Console\Commands\GenerateSeederCommand;
use CalinNicolai\Seedergen\Console\Commands\ScanDatabaseCommand;
use CalinNicolai\Seedergen\Services\SeederGeneratorService;
use Illuminate\Support\ServiceProvider;

class SeederGenServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/seedergen.php', 'seedergen'
        );

        $this->mergeConfigFrom(
            __DIR__ . '/../config/field_attributes.php', 'field_attributes'
        );

        $this->app->singleton('seedergen', function ($app) {
            return new SeederGeneratorService();
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->publishes([
            __DIR__ . '/../config/seedergen.php' => config_path('seedergen.php'),
        ], 'config');

        if (config('seedergen.enable_web_interface')) {
            $this->loadRoutesFrom(__DIR__ . '/../Http/routes.php');
            $this->loadViewsFrom(__DIR__ . '/../Resources/views', 'seedergen');
        }

        if ($this->app->runningInConsole()) {
            $this->commands([
                GenerateSeederCommand::class,
                ScanDatabaseCommand::class,
            ]);
        }
    }
}
