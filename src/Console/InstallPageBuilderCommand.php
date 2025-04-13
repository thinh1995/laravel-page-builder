<?php

declare(strict_types=1);

namespace Thinhnx\LaravelPageBuilder\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class InstallPageBuilderCommand extends Command
{
    protected $hidden = true;

    protected $signature = 'page-builder:install';

    protected $description = 'Install Page Builder package';

    public function handle(): void
    {
        $this->info('Installing Page Builder...');

        foreach ($this->getStepData() as $step) {
            call_user_func_array(['self', 'runStep'], array_values($step));
        }

        $this->info('Installed Page Builder successfully!!!');
    }

    private function getStepData(): array
    {
        return [
            [
                'tag'           => 'migrations',
                'path'          => [
                    'database/migrations/*_create_blocks_table.php',
                    'database/migrations/*_create_block_translations_table.php',
                    'database/migrations/*_create_blockables_table.php',
                ],
                'path_type'     => 'files',
                'can_overwrite' => false,
            ],
            [
                'tag'           => 'seeders',
                'path'          => 'database/seeders/PageBuilderTablesSeeder.php',
                'path_type'     => 'file',
                'can_overwrite' => true,
            ],
            [
                'tag'           => 'config',
                'path'          => 'config/page-builder.php',
                'path_type'     => 'file',
                'can_overwrite' => true,
            ],
            [
                'tag'           => 'views',
                'path'          => 'resources/views/vendor/page-builder',
                'path_type'     => 'directory',
                'can_overwrite' => true,
            ],
            [
                'tag'           => 'assets',
                'path'          => 'public/packages/thinhnx/page-builder',
                'path_type'     => 'directory',
                'can_overwrite' => true,
            ],
            [
                'tag'           => 'lang',
                'path'          => [
                    'lang/vi/page-builder.php',
                    'lang/en/page-builder.php',
                ],
                'path_type'     => 'files',
                'can_overwrite' => true,
            ],
        ];
    }

    private function runStep(string $tag, array|string $path, string $pathType, bool $canOverwrite): void
    {
        $this->info("Publishing $tag...");

        match ($pathType) {
            'file' => $isExist = $this->fileExist($path),
            'files' => $isExist = $this->filesExist($path),
            'directory' => $isExist = File::isDirectory($path)
        };

        if (! $isExist) {
            $this->publish($tag);
            $this->info("Published $tag.");

            if ($tag === 'migrations') {
                if ($this->confirm('Do you want to run migrations?')) {
                    $this->call('migrate');
                }
            }

            if ($tag === 'seeders') {
                if ($this->confirm('Do you want to run the seeders?')) {
                    $this->call('db:seed --class=PageBuilderTablesSeeder');
                }
            }

            return;
        }

        if ($canOverwrite) {
            $this->shouldOverwrite($path) ?
                $this->publish($tag, true) :
                $this->info("The existing $tag was not overwritten.");

            return;
        }

        $this->info("The $tag already exists!");
    }

    private function fileExist(string $path): bool
    {
        $path     = base_path($path);
        $fileName = basename($path);

        if (str_contains($fileName, '*')) {
            $directory    = str_replace($fileName, '', $path);
            $fileName     = str_replace('*', '', basename($path));
            $existedFiles = collect(File::files($directory))->filter(function ($file) use ($fileName) {
                return Str::endsWith($file->getFilename(), $fileName);
            });

            return $existedFiles->isNotEmpty();
        }

        return File::exists($path);
    }

    private function filesExist(string|array $path): bool
    {
        if (is_array($path)) {
            foreach ($path as $item) {
                if ($this->fileExist($item)) {
                    return true;
                }
            }

            return false;
        }

        return $this->fileExist($path);
    }

    private function publish(string $tag, bool $force = false): void
    {
        $params = [
            '--provider' => 'Thinhnx\LaravelPageBuilder\LaravelPageBuilderProvider',
            '--tag'      => $tag,
            '--force'    => $force,
        ];

        $this->call('vendor:publish', $params);
    }

    private function shouldOverwrite(string|array $path): bool
    {
        $question = is_array($path) ?
            "The paths: \n - " . implode("\n - ", $path) . "\nalready exist. Do you want to overwrite them?" :
            "The path [$path] already exists. Do you want to overwrite it?";

        return $this->confirm($question);
    }
}
