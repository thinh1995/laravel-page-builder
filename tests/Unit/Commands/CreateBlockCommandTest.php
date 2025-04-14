<?php

declare(strict_types=1);

namespace Thinhnx\LaravelPageBuilder\Tests\Unit\Commands;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Thinhnx\LaravelPageBuilder\Tests\TestCase;

class CreateBlockCommandTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        $this->refreshApplication();
        $this->cleanFiles();
        parent::setUp();
        $this->initialize();
    }

    public function test_create_new_block()
    {
        $command = $this->artisan('page-builder:block:create');

        foreach (config('page-builder.locales') as $locale) {
            $command->expectsQuestion(
                'Enter a ' . __("page-builder.language.$locale") . ' name for the block?',
                'Slider'
            );
        }

        $command->expectsQuestion('Enter a name for the block type?', 'slider');
        $command->expectsConfirmation('Is this block can contain other blocks?');
        $command->execute();

        $this->assertDatabaseHas(config('page-builder.tables.block'), [
            'type'      => 'slider',
            'is_layout' => false,
        ]);

        $this->assertDatabaseHas(config('page-builder.tables.block_translation'), [
            'name'   => 'Slider',
            'locale' => 'en'
        ]);

        $this->assertDatabaseHas(config('page-builder.tables.block_translation'), [
            'name'   => 'Slider',
            'locale' => 'vi'
        ]);
    }

    public function test_create_new_block_with_existed_type()
    {
        $command = $this->artisan('page-builder:block:create');

        foreach (config('page-builder.locales') as $locale) {
            $command->expectsQuestion(
                'Enter a ' . __("page-builder.language.$locale") . ' name for the block?',
                'Text 2'
            );
        }

        $command->expectsQuestion('Enter a name for the block type?', 'text');
        $command->expectsQuestion('Enter another name for the block type?', 'text2');
        $command->expectsConfirmation('Is this block can contain other blocks?', 'yes');
        $command->execute();
        $command->expectsOutput('This name already exists.');

        $this->assertDatabaseHas(config('page-builder.tables.block'), [
            'type'      => 'text2',
            'is_layout' => true,
        ]);

        $this->assertDatabaseHas(config('page-builder.tables.block_translation'), [
            'name'   => 'Text 2',
            'locale' => 'en'
        ]);

        $this->assertDatabaseHas(config('page-builder.tables.block_translation'), [
            'name'   => 'Text 2',
            'locale' => 'vi'
        ]);
    }
}
