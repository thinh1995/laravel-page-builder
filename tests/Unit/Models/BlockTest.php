<?php

declare(strict_types=1);

namespace Thinhnx\LaravelPageBuilder\Tests\Unit\Models;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Thinhnx\LaravelPageBuilder\Models\Block;
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
}
