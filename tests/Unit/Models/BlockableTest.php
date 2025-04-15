<?php

declare(strict_types=1);

namespace Thinhnx\LaravelPageBuilder\Tests\Unit\Models;

use Thinhnx\LaravelPageBuilder\Models\Block;
use Thinhnx\LaravelPageBuilder\Models\Blockable;
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
        $blockable = Blockable::factory()->create([
            'blockable_id'   => 1,
            'blockable_type' => 'App\Models\Page',
        ]);

        $this->assertInstanceOf(Blockable::class, $blockable);
        $this->assertEquals(1, $blockable->blockable_id);
        $this->assertEquals('App\Models\Page', $blockable->blockable_type);
        $this->assertInstanceOf(Block::class, $blockable->block);
    }
}
