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
        $blockTranslationVi = BlockTranslation::factory()->create(['locale' => 'vi', 'name' => 'Block mới']);
        $blockTranslationEn = BlockTranslation::factory()->create(
            ['locale' => 'en', 'name' => 'New block', 'block_id' => $blockTranslationVi->block_id]
        );

        $this->assertInstanceOf(BlockTranslation::class, $blockTranslationVi);
        $this->assertEquals('vi', $blockTranslationVi->locale);
        $this->assertEquals('Block mới', $blockTranslationVi->name);
        $this->assertInstanceOf(Block::class, $blockTranslationVi->block);

        $this->assertInstanceOf(BlockTranslation::class, $blockTranslationEn);
        $this->assertEquals('en', $blockTranslationEn->locale);
        $this->assertEquals('New block', $blockTranslationEn->name);
        $this->assertInstanceOf(Block::class, $blockTranslationEn->block);
    }
}
