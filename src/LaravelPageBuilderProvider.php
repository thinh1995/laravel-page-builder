<?php

declare(strict_types=1);

namespace Thinhnx\LaravelPageBuilder;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Thinhnx\LaravelPageBuilder\Console\CreateBlockCommand;
use Thinhnx\LaravelPageBuilder\Console\InstallPageBuilderCommand;

class LaravelPageBuilderProvider extends ServiceProvider
{
    /**
     * @return void
     */
    public function register(): void
    {
        $this->app->singleton(PageBuilder::class, PageBuilder::class);

        $this->mergeConfigFrom(__DIR__ . '/../config/page-builder.php', 'page-builder');
    }

    /**
     * @return void
     */
    public function boot(): void
    {
        $this->registerRoutes();
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'page-builder');

        if ($this->app->runningInConsole()) {
            $this->commands([
                InstallPageBuilderCommand::class,
                CreateBlockCommand::class,
            ]);

            // Publish the migrations
            $this->publishes([
                __DIR__ . '/../database/migrations/01_create_blocks_table.php.stub'             => database_path(
                    'migrations/' . date('Y_m_d_His', time()) . '_create_blocks_table.php'
                ),
                __DIR__ . '/../database/migrations/02_create_block_translations_table.php.stub' => database_path(
                    'migrations/' . date('Y_m_d_His', time() + 1) . '_create_block_translations_table.php'
                ),
                __DIR__ . '/../database/migrations/03_create_blockables_table.php.stub'         => database_path(
                    'migrations/' . date('Y_m_d_His', time() + 2) . '_create_blockables_table.php'
                ),
            ], 'migrations');

            // Publish the seeders
            $this->publishes([
                __DIR__ . '/../database/seeders/PageBuilderTablesSeeder.php.stub' => database_path(
                    'seeders/PageBuilderTablesSeeder.php'
                ),
            ], 'seeders');

            // Publish the config
            $this->publishes([
                __DIR__ . '/../config/page-builder.php' => config_path('page-builder.php'),
            ], 'config');

            // Publish the assets
            $this->publishes([
                __DIR__ . '/../resources/assets' => public_path('packages/thinhnx/page-builder'),
            ], 'assets');

            // Publish the views
            $this->publishes([
                __DIR__ . '/../resources/views' => resource_path('views/vendor/page-builder'),
            ], 'views');

            // Publish the lang
            $this->publishes([__DIR__ . '/../lang' => lang_path('/')], 'lang');
        }
    }

    /**
     * @return void
     */
    protected function registerRoutes(): void
    {
        Route::group($this->routeConfiguration(), function () {
            $this->loadRoutesFrom(__DIR__ . '/../routes/web.php');
        });
    }

    /**
     * @return array
     */
    protected function routeConfiguration(): array
    {
        return [
            'prefix'     => config('page-builder.route.prefix'),
            'as'         => config('page-builder.route.as'),
            'middleware' => config('page-builder.route.middleware'),
        ];
    }
}
