<?php

declare(strict_types=1);

namespace Thinhnx\LaravelPageBuilder\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Thinhnx\LaravelPageBuilder\Models\Block;
use Thinhnx\LaravelPageBuilder\Models\BlockTranslation;

class BlockTranslationFactory extends Factory
{
    protected $model = BlockTranslation::class;

    public function definition(): array
    {
        $block = Block::factory()->create();

        return [
            'name'        => $this->faker->name,
            'description' => $this->faker->text,
            'locale'      => $this->faker->randomElement(config('page-builder.locales')),
            'icon'        => $this->faker->imageUrl(200, 200),
            'block_id'    => $block->id,
        ];
    }
}
