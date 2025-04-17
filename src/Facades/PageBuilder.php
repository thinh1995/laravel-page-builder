<?php

declare(strict_types=1);

namespace Thinhnx\LaravelPageBuilder\Facades;

use Illuminate\Support\Facades\Facade;
use Thinhnx\LaravelPageBuilder\PageBuilder as PageBuilderSingleton;

/**
 * @method static Illuminate\Contracts\View\View render(?Model $model = null)
 */
class PageBuilder extends Facade
{
    /**
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return PageBuilderSingleton::class;
    }
}
