<?php

declare(strict_types=1);

namespace Thinhnx\LaravelPageBuilder\Tests\Unit\Transformers;

use Illuminate\Database\Eloquent\Factories\Sequence;
use League\Fractal\Resource\Collection;
use Thinhnx\LaravelPageBuilder\Models\Block;
use Thinhnx\LaravelPageBuilder\Tests\TestCase;
use Thinhnx\LaravelPageBuilder\Transformers\BlockTransformer;

class BlockTransformerTest extends TestCase
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
        $block = Block::factory()
                      ->hasTranslations(
                          2,
                          new Sequence(
                              ['locale' => 'vi'],
                              ['locale' => 'en'],
                          )
                      )
                      ->create();

        $this->assertEquals(
            ['id', 'name', 'type', 'is_layout', 'created_at', 'updated_at'],
            array_keys((new BlockTransformer())->transform($block))
        );
        $this->assertTrue((new BlockTransformer())->includeTranslations($block) instanceof Collection);
    }

    public function test_data_types()
    {
        $block = Block::factory()
                      ->hasTranslations(
                          2,
                          new Sequence(
                              ['locale' => 'vi'],
                              ['locale' => 'en'],
                          )
                      )
                      ->create();

        $transformer = (new BlockTransformer())->transform($block);

        $this->assertIsInt($transformer['id']);
        $this->assertIsString($transformer['name']);
        $this->assertIsString($transformer['type']);
        $this->assertIsInt($transformer['is_layout']);
        $this->assertIsString($transformer['created_at']);
        $this->assertIsString($transformer['updated_at']);
    }
}
