<?php

declare(strict_types=1);

namespace Thinhnx\LaravelPageBuilder\Tests;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Orchestra\Testbench\TestCase as OrchestraTestCase;
use Thinhnx\LaravelPageBuilder\LaravelPageBuilderProvider;

abstract class TestCase extends OrchestraTestCase
{
    protected function getPackageProviders($app): array
    {
        return [
            LaravelPageBuilderProvider::class,
        ];
    }

    public function getEnvironmentSetUp($app): void
    {
        include_once __DIR__ . '/../database/seeders/PageBuilderTablesSeeder.php.stub';
    }

    protected function initialize(): void
    {
        File::put(config_path("translatable.php"), "<?php \n return ['locales' => ['vi', 'en']];");

        $command = $this->artisan('page-builder:install');
        $command->expectsConfirmation('Do you want to run migrations?', 'yes');
        $command->expectsConfirmation('Do you want to run the seeders?', 'yes');
        $command->execute();
    }

    protected function cleanFiles(): void
    {
        collect(File::files(database_path('migrations')))->each(function ($file) {
            if (Str::endsWith($file->getFilename(), [
                '_create_blocks_table.php',
                '_create_block_translations_table.php',
                '_create_blockables_table.php',
            ])) {
                unlink($file->getPathname());
            }
        });

        if (File::exists(database_path('seeders/PageBuilderTablesSeeder.php'))) {
            unlink(database_path('seeders/PageBuilderTablesSeeder.php'));
        }

        if (File::exists(config_path('page-builder.php'))) {
            unlink(config_path('page-builder.php'));
        }

        if (File::isDirectory(resource_path('views/vendor/page-builder'))) {
            File::deleteDirectory(resource_path('views/vendor/page-builder'));
        }

        if (File::isDirectory(public_path('packages/thinhnx/page-builder'))) {
            File::deleteDirectory(public_path('packages/thinhnx/page-builder'));
        }

        if (File::exists(lang_path('vi/page-builder.php'))) {
            unlink(lang_path('vi/page-builder.php'));
        }

        if (File::exists(lang_path('en/page-builder.php'))) {
            unlink(lang_path('en/page-builder.php'));
        }
    }
}
