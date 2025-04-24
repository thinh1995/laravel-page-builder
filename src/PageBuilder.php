<?php

declare(strict_types=1);

namespace Thinhnx\LaravelPageBuilder;

use Illuminate\Contracts\View\Factory as ViewFactory;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class PageBuilder
{
    protected ViewFactory $viewFactory;

    public function __construct(ViewFactory $viewFactory)
    {
        $this->viewFactory = $viewFactory;
    }

    /**
     * @param Model|null   $model
     * @param array|string $locales
     *
     * @return View
     */
    public function render(?Model $model = null, array|string $locales = []): View
    {
        $id            = 'pb-' . Str::random(8);
        $locales       = $this->transformLocales($locales);
        $blocks        = $this->getBlocks();
        $initialBlocks = $this->getBlockItems($locales, $model);

        return $this->viewFactory->make('page-builder::page-builder', [
            'id'            => $id,
            'locales'       => $locales,
            'blocks'        => $blocks,
            'initialBlocks' => $initialBlocks,
        ]);
    }

    /**
     * Retrieves the list of blocks, caching the result indefinitely.
     *
     * @return mixed
     */
    public function getBlocks(): mixed
    {
        if (config('page-builder.cache.enabled')) {
            return Cache::rememberForever('pagebuilder_blocks', function () {
                return app(config('page-builder.models.block'))::with('translations')->get();
            });
        }

        return app(config('page-builder.models.block'))::with('translations')->get();
    }

    /**
     * @param array|string $locales
     * @param Model|null   $model
     *
     * @return array
     */
    public function getBlockItems(array|string $locales, ?Model $model): array
    {
        $initialBlocks = [];

        if (is_array($locales)) {
            foreach ($locales as $locale) {
                $initialBlocks[$locale] = $model && method_exists($model, 'getBlockItems') ?
                    $model->getBlockItems($locale) : [];
            }

            return $initialBlocks;
        }

        $initialBlocks[$locales] = $model && method_exists($model, 'getBlockItems') ?
            $model->getBlockItems($locales) : [];

        return $initialBlocks;
    }

    /**
     * @param array|string|null $locales
     *
     * @return array|string
     */
    private function transformLocales(array|string|null $locales): array|string
    {
        if (! $locales) {
            return config('page-builder.locales');
        }

        if (is_array($locales) && count($locales) === 1) {
            return $locales[0];
        }

        return $locales;
    }
}
