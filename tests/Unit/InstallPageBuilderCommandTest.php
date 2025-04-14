<?php

declare(strict_types=1);

namespace Thinhnx\LaravelPageBuilder\Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Thinhnx\LaravelPageBuilder\Tests\TestCase;

class InstallPageBuilderCommandTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_fresh_install_command()
    {
        $command = $this->artisan('page-builder:install');
        $command->expectsConfirmation('Do you want to run migrations?');
        $command->expectsConfirmation('Do you want to run the seeders?');
        $command->execute();

        $command->expectsOutput('Published migrations');
        $command->expectsOutput('Published seeders');
        $command->expectsOutput('Published config');
        $command->expectsOutput('Published views');
        $command->expectsOutput('Published assets');
        $command->expectsOutput('Published lang');
        $this->assertTrue(File::exists(config_path('page-builder.php')));

        $this->cleanFiles();
    }

    public function test_the_fresh_install_command_with_run_migration_and_seeder()
    {
        $command = $this->artisan('page-builder:install');
        $command->expectsConfirmation('Do you want to run migrations?', 'yes');
        $command->expectsConfirmation('Do you want to run the seeders?', 'yes');
        $command->execute();

        $command->expectsOutput('Published migrations');
        $command->expectsOutput('Published seeders');
        $command->expectsOutput('Published config');
        $command->expectsOutput('Published views');
        $command->expectsOutput('Published assets');
        $command->expectsOutput('Published lang');
        $this->assertTrue(File::exists(config_path('page-builder.php')));

        $this->cleanFiles();
    }

    public function test_migration_files_are_present_users_can_not_overwrite_them()
    {
        $fileNames = [
            date('Y_m_d_His', time()) . '_create_blocks_table.php',
            date('Y_m_d_His', time()) . '_create_block_translations_table.php',
            date('Y_m_d_His', time()) . '_create_blockables_table.php'
        ];

        foreach ($fileNames as $index => $fileName) {
            File::put(database_path("migrations/$fileName"), "test content $index");
            $this->assertTrue(File::exists(database_path("migrations/$fileName")));
        }

        $command = $this->artisan('page-builder:install');
        $command->expectsConfirmation('Do you want to run the seeders?');
        $command->execute();

        $command->expectsOutput('The mirations already exists!');
        $this->assertNotEquals(
            file_get_contents(__DIR__ . '/../../database/migrations/01_create_blocks_table.php.stub'),
            file_get_contents(database_path("migrations/$fileNames[0]"))
        );
        $this->assertNotEquals(
            file_get_contents(__DIR__ . '/../../database/migrations/02_create_block_translations_table.php.stub'),
            file_get_contents(database_path("migrations/$fileNames[1]"))
        );
        $this->assertNotEquals(
            file_get_contents(__DIR__ . '/../../database/migrations/03_create_blockables_table.php.stub'),
            file_get_contents(database_path("migrations/$fileNames[2]"))
        );

        $this->cleanFiles();
    }

    public function test_the_seeder_file_is_present_users_can_choose_to_do_overwrite_it()
    {
        $path = 'database/seeders/PageBuilderTablesSeeder.php';
        File::put(base_path($path), 'test contents');
        $this->assertTrue(File::exists(base_path($path)));

        $command = $this->artisan('page-builder:install');
        $command->expectsConfirmation('Do you want to run migrations?');
        $command->expectsConfirmation("The path [$path] already exists. Do you want to overwrite it?", 'yes');
        $command->execute();

        $this->assertEquals(
            file_get_contents(__DIR__ . '/../../database/seeders/PageBuilderTablesSeeder.php.stub'),
            file_get_contents(base_path($path))
        );

        $this->cleanFiles();
    }

    public function test_the_config_file_is_present_users_can_choose_to_do_overwrite_it()
    {
        $path = 'config/page-builder.php';
        File::put(base_path($path), 'test contents');
        $this->assertTrue(File::exists(base_path($path)));

        $command = $this->artisan('page-builder:install');
        $command->expectsConfirmation('Do you want to run migrations?');
        $command->expectsConfirmation('Do you want to run the seeders?');
        $command->expectsConfirmation("The path [$path] already exists. Do you want to overwrite it?", 'yes');
        $command->execute();

        $this->assertEquals(
            file_get_contents(__DIR__ . '/../../config/page-builder.php'),
            file_get_contents(base_path($path))
        );

        $this->cleanFiles();
    }

    public function test_the_view_files_are_present_users_can_choose_to_do_overwrite_them()
    {
        $path = 'resources/views/vendor/page-builder';
        File::makeDirectory(base_path($path), force: true);
        $this->assertTrue(File::isDirectory(base_path($path)));

        $command = $this->artisan('page-builder:install');
        $command->expectsConfirmation('Do you want to run migrations?');
        $command->expectsConfirmation('Do you want to run the seeders?');
        $command->expectsConfirmation("The path [$path] already exists. Do you want to overwrite it?", 'yes');
        $command->execute();

        $this->assertEquals(
            file_get_contents(__DIR__ . '/../../resources/views/page-builder.blade.php'),
            file_get_contents(base_path($path) . '/page-builder.blade.php')
        );

        $this->cleanFiles();
    }

    public function test_the_assets_files_are_present_users_can_choose_to_do_overwrite_it()
    {
        $path = 'public/packages/thinhnx/page-builder';
        File::makeDirectory(base_path($path), force: true);
        $this->assertTrue(File::exists(base_path($path)));

        $command = $this->artisan('page-builder:install');
        $command->expectsConfirmation('Do you want to run migrations?');
        $command->expectsConfirmation('Do you want to run the seeders?');
        $command->expectsConfirmation("The path [$path] already exists. Do you want to overwrite it?", 'yes');
        $command->execute();

        $this->assertEquals(
            file_get_contents(__DIR__ . '/../../resources/assets/js/page-builder.js'),
            file_get_contents(base_path($path) . '/js/page-builder.js')
        );

        $this->cleanFiles();
    }

    public function test_the_lang_files_are_present_users_can_choose_to_do_overwrite_them()
    {
        $fileNames = [
            'lang/vi/page-builder.php',
            'lang/en/page-builder.php',
        ];

        foreach ($fileNames as $index => $fileName) {
            File::put(base_path($fileName), "test content $index");
            $this->assertTrue(File::exists(base_path($fileName)));
        }

        $command = $this->artisan('page-builder:install');
        $command->expectsConfirmation('Do you want to run migrations?');
        $command->expectsConfirmation('Do you want to run the seeders?');
        $command->expectsConfirmation(
            "The paths: \n - " . implode("\n - ", $fileNames) . "\nalready exist. Do you want to overwrite them?",
            'yes'
        );
        $command->execute();

        $this->assertEquals(
            file_get_contents(__DIR__ . '/../../lang/vi/page-builder.php'),
            file_get_contents(base_path($fileNames[0]))
        );

        $this->assertEquals(
            file_get_contents(__DIR__ . '/../../lang/en/page-builder.php'),
            file_get_contents(base_path($fileNames[1]))
        );

        $this->cleanFiles();
    }

    private function cleanFiles(): void
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
