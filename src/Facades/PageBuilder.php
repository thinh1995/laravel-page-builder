<?php

declare(strict_types=1);

namespace Thinhnx\LaravelPageBuilder\Facades;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Facade;
use Thinhnx\LaravelPageBuilder\PageBuilder as PageBuilderSingleton;

/**
 * @method static \Illuminate\Contracts\View\View render(?\Illuminate\Database\Eloquent\Model $model = null, array|string $locales = [])
 * @method static mixed getBlocks()
 * @method static array getBlockItems(array|string $locales, ?Model $model)
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
