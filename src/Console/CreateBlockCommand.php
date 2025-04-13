<?php

declare(strict_types=1);

namespace Thinhnx\LaravelPageBuilder\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class CreateBlockCommand extends Command
{
    protected $signature = 'page-builder:block:create';

    protected $description = 'Create block for page builder.';

    public function handle(): void
    {
        $data = [];

        foreach (config('page-builder.locales') as $locale) {
            $data['translations'][$locale]['name'] = $this->ask(
                'Enter a ' . __("page-builder.language.$locale") . ' name for the block?'
            );
        }

        $data['type'] = $this->ask('Enter a name for the block type?');

        while ($this->isTypeExisted($data['type'])) {
            $this->error('This name already exists.');
            $data['type'] = $this->ask('Enter another name for the block type?');
        }

        $data['is_layout'] = $this->confirm('Is this block can contain other blocks?', false);

        $this->info('Creating the block...');
        DB::transaction(function () use ($data) {
            app(config('page-builder.models.block'))::create($data);
            File::put(resource_path('views/vendor/page-builder/blocks/' . $data['type'] . '.blade.php'), '');
        });
        $this->info('Created the block successfully!!!');
    }

    private function isTypeExisted(string $type): bool
    {
        return app(config('page-builder.models.block'))::where('type', $type)->exists();
    }
}
