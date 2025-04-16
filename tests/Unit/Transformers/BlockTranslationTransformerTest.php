<?php

declare(strict_types=1);

namespace Thinhnx\LaravelPageBuilder\Tests\Unit\Transformers;

use Thinhnx\LaravelPageBuilder\Models\Block;
use Thinhnx\LaravelPageBuilder\Models\BlockTranslation;
use Thinhnx\LaravelPageBuilder\Tests\TestCase;
use Thinhnx\LaravelPageBuilder\Transformers\BlockTranslationTransformer;

class BlockTranslationTransformerTest extends TestCase
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
        $blockTranslation = BlockTranslation::factory()
                                            ->for(Block::factory())
                                            ->create();

        $this->assertEquals(
            ['id', 'block_id', 'locale', 'name', 'description', 'created_at', 'updated_at'],
            array_keys((new BlockTranslationTransformer())->transform($blockTranslation))
        );
    }

    public function test_data_types()
    {
        $blockTranslation = BlockTranslation::factory()
                                            ->for(Block::factory())
                                            ->create();

        $transformer = (new BlockTranslationTransformer())->transform($blockTranslation);

        $this->assertIsInt($transformer['id']);
        $this->assertIsInt($transformer['block_id']);
        $this->assertIsString($transformer['locale']);
        $this->assertIsString($transformer['name']);
        $this->assertIsString($transformer['description']);
        $this->assertIsString($transformer['created_at']);
        $this->assertIsString($transformer['updated_at']);
    }
}
