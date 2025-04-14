<?php

declare(strict_types=1);

namespace Thinhnx\LaravelPageBuilder\Tests\Unit\Models;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Thinhnx\LaravelPageBuilder\Models\Block;
use Thinhnx\LaravelPageBuilder\Models\BlockTranslation;
use Thinhnx\LaravelPageBuilder\Tests\TestCase;

class BlockTranslationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        $this->refreshApplication();
        $this->cleanFiles();
        parent::setUp();
        $this->initialize();
    }

    public function test_create_new_block_translation_models()
    {
        $block = Block::factory()->create([
            'type' => 'new_block',
            'vi'   => [
                'name' => 'Block mới',
            ],
            'en'   => [
                'name' => 'New block',
            ],
        ]);

        $this->assertEquals('new_block', $block->type);
        $this->assertInstanceOf(BlockTranslation::class, $block->getTranslation('vi'));
        $this->assertInstanceOf(BlockTranslation::class, $block->getTranslation('en'));
        $this->assertEquals('Block mới', $block->getTranslation('vi')->name);
        $this->assertEquals('New block', $block->getTranslation('en')->name);
    }
}
