<?php

declare(strict_types=1);

namespace Thinhnx\LaravelPageBuilder\Tests\Unit\Transformers;

use League\Fractal\Resource\Item;
use Thinhnx\LaravelPageBuilder\Models\Block;
use Thinhnx\LaravelPageBuilder\Models\Blockable;
use Thinhnx\LaravelPageBuilder\Tests\TestCase;
use Thinhnx\LaravelPageBuilder\Transformers\BlockableTransformer;

class BlockableTransformerTest extends TestCase
{
    protected function setUp(): void
    {
        $this->refreshApplication();
        $this->cleanFiles();
        parent::setUp();
        $this->initialize();
    }

    public function test_output_contains_valid_structure()
    {
        $blockable = Blockable::factory()
                              ->for(Block::factory())
                              ->create();

        $this->assertEquals(
            [
                'id',
                'block_id',
                'blockable_id',
                'blockable_type',
                'parent_id',
                'content',
                'order',
                'column_index',
                'locale',
                'created_at',
                'updated_at'
            ],
            array_keys((new BlockableTransformer())->transform($blockable))
        );
        $this->assertTrue((new BlockableTransformer())->includeBlock($blockable) instanceof Item);
    }

    public function test_data_types()
    {
        $blockable = Blockable::factory()
                              ->for(Block::factory())
                              ->create();

        $transformer = (new BlockableTransformer())->transform($blockable);

        $this->assertIsInt($transformer['id']);
        $this->assertIsInt($transformer['block_id']);
        $this->assertIsInt($transformer['blockable_id']);
        $this->assertIsString($transformer['blockable_type']);
        $this->assertNull($transformer['parent_id']);
        $this->assertIsString($transformer['content']);
        $this->assertIsInt($transformer['order']);
        $this->assertIsInt($transformer['column_index']);
        $this->assertIsString($transformer['locale']);
        $this->assertIsString($transformer['created_at']);
        $this->assertIsString($transformer['updated_at']);
    }
}
