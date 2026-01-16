<?php

namespace Pioneers\ClickHouse;

use Illuminate\Support\ServiceProvider;
use Pioneers\ClickHouse\Console\CreateDatabase;
use Pioneers\ClickHouse\Console\MakeMigration;
use Pioneers\ClickHouse\Console\Migrate;

class ClickHouseServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->configurePublishing();
        $this->registerCommands();
    }

    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/clickhouse.php', 'clickhouse'
        );
    }

    /**
     * Configure the publishable resources offered by the package.
     */
    protected function configurePublishing(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/clickhouse.php' => $this->app->configPath('clickhouse.php'),
            ], 'clickhouse');
        }
    }

    /**
     * Register the package's commands.
     */
    protected function registerCommands(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                MakeMigration::class,
                Migrate::class,
                CreateDatabase::class,
            ]);
        }
    }
}
