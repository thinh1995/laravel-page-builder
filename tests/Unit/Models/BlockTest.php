<?php

declare(strict_types=1);

namespace Thinhnx\LaravelPageBuilder\Tests\Unit\Models;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Thinhnx\LaravelPageBuilder\Models\Block;
use Thinhnx\LaravelPageBuilder\Models\Blockable;
use Thinhnx\LaravelPageBuilder\Tests\includes\Page;
use Thinhnx\LaravelPageBuilder\Tests\TestCase;

class BlockTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        $this->refreshApplication();
        $this->cleanFiles();
        parent::setUp();
        $this->initialize();
    }

    public function test_create_new_block_model()
    {
        $block = Block::factory()->create([
            'type' => 'new_block'
        ]);

        $this->assertInstanceOf(Block::class, $block);
        $this->assertEquals('new_block', $block->type);
    }

    public function test_model_can_have_multiple_blocks()
    {
        $page  = Page::factory()->create();
        $block = Block::where('type', 'text')->first();

        $page->addBlockItem($block->id, 'test "new block"', 0);
        $page->syncBlockItems([
            [
                'block_id'     => $block->id,
                'content'      => 'test "new block 2"',
                'column_index' => 0,
                'children'     => []
            ],
        ], 'vi');

        $this->assertEquals(2, $page->blocks->count());
        $this->assertEquals(2, $page->blockItems->count());
        $this->assertInstanceOf(Blockable::class, $page->getBlockItemsByLocale('vi')->first());
        $this->assertInstanceOf(Blockable::class, $page->getBlockItemsByLocale('en')->first());

        $page->removeBlockItem($page->getBlockItemsByLocale('vi')->first()->id, 'vi');
        $this->assertEquals(1, $page->blockItems->count());
    }

    public function test_blocks_are_deleted_when_delete_model()
    {
        $page  = Page::factory()->create();
        $block = Block::where('type', 'text')->first();

        $page->addBlockItem($block->id, 'test new block', 0);
        $page->syncBlockItems([
            [
                'block_id'     => $block->id,
                'content'      => 'test new block',
                'column_index' => 0,
                'children'     => []
            ],
        ], 'vi');

        $this->assertEquals(2, $page->blocks->count());
        $this->assertEquals(2, $page->blockItems()->count());
        $this->assertInstanceOf(Blockable::class, $page->getBlockItemsByLocale('vi')->first());
        $this->assertInstanceOf(Blockable::class, $page->getBlockItemsByLocale('en')->first());

        $page->delete();

        $this->assertEquals(0, Blockable::count());
    }
}
