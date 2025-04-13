<?php

declare(strict_types=1);

namespace Thinhnx\LaravelPageBuilder\Tests\Unit;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Thinhnx\LaravelPageBuilder\Tests\TestCase;

class InstallPageBuilderCommandTest extends TestCase
{
    public function test_the_fresh_install_command()
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

        $this->assertFalse(File::exists(config_path('page-builder.php')));

        $command = $this->artisan('page-builder:install');

        $command->expectsConfirmation('Do you want to run migrations?');
        $command->expectsConfirmation('Do you want to run the seeders?');
        $command->execute();

        $this->assertTrue(File::exists(config_path('page-builder.php')));
    }
}
