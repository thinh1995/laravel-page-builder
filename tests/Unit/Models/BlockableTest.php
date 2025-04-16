<?php

declare(strict_types=1);

namespace Thinhnx\LaravelPageBuilder\Tests\Unit\Models;

use Thinhnx\LaravelPageBuilder\Models\Block;
use Thinhnx\LaravelPageBuilder\Models\Blockable;
use Thinhnx\LaravelPageBuilder\Tests\includes\Page;
use Thinhnx\LaravelPageBuilder\Tests\TestCase;

class BlockableTest extends TestCase
{
    protected function setUp(): void
    {
        $this->refreshApplication();
        $this->cleanFiles();
        parent::setUp();
        $this->initialize();
    }

    public function test_create_new_blockable_model()
    {
        $blockItem = Blockable::factory()
                              ->for(
                                  Page::factory(),
                                  'blockable',
                              )
                              ->create();

        $this->assertInstanceOf(Blockable::class, $blockItem);
        $this->assertEquals(Page::class, $blockItem->blockable_type);
        $this->assertInstanceOf(Block::class, $blockItem->block);
    }
}
