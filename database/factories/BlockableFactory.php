<?php

declare(strict_types=1);

namespace Thinhnx\LaravelPageBuilder\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Thinhnx\LaravelPageBuilder\Models\Block;
use Thinhnx\LaravelPageBuilder\Models\Blockable;

class BlockableFactory extends Factory
{
    protected $model = Blockable::class;

    public function definition(): array
    {
        $block = Block::factory()->create();

        return [
            'block_id'       => $block->id,
            'blockable_id'   => $this->faker->numberBetween(1, 10),
            'blockable_type' => 'App\Models\Page',
            'parent_id'      => null,
            'content'        => $this->faker->text,
            'order'          => $this->faker->numberBetween(1, 100),
            'column_index'   => 0,
            'locale'         => $this->faker->randomElement(config('page-builder.locales')),
        ];
    }
}
