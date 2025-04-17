<?php

declare(strict_types=1);

namespace Thinhnx\LaravelPageBuilder\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Thinhnx\LaravelPageBuilder\Models\Block;

class BlockFactory extends Factory
{
    protected $model = Block::class;

    /**
     * @return array
     */
    public function definition(): array
    {
        return [
            'type'      => $this->faker->word,
            'is_layout' => $this->faker->boolean,
        ];
    }
}
