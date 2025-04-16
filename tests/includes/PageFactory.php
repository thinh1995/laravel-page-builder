<?php

declare(strict_types=1);

namespace Thinhnx\LaravelPageBuilder\Tests\includes;

use Illuminate\Database\Eloquent\Factories\Factory;

class PageFactory extends Factory
{
    protected $model = Page::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->name,
        ];
    }
}
