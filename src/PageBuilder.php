<?php

declare(strict_types=1);

namespace Thinhnx\LaravelPageBuilder;

use Illuminate\Contracts\View\Factory as ViewFactory;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
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
        $initialBlocks = $this->getInitialBlocks($locales, $model);

        return $this->viewFactory->make('page-builder::page-builder', [
            'id'            => $id,
            'locales'       => $locales,
            'blocks'        => $blocks,
            'initialBlocks' => $initialBlocks,
        ]);
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

    /**
     * Retrieves the list of blocks, caching the result indefinitely.
     *
     * @return mixed
     */
    private function getBlocks(): mixed
    {
        if (config('page-builder.cache.enabled')) {
            return Cache::rememberForever('pagebuilder_blocks', function () {
                return app(config('page-builder.models.block'))::all();
            });
        }

        return app(config('page-builder.models.block'))::all();
    }

    /**
     * @param array|string $locales
     * @param Model|null   $model
     *
     * @return array
     */
    private function getInitialBlocks(array|string $locales, ?Model $model): array
    {
        $initialBlocks = [];

        if (is_array($locales)) {
            foreach ($locales as $locale) {
                $initialBlocks[$locale] = $model && method_exists($model, 'getBlockItems') ?
                    $model->getBlockItems($locale)->toArray() : [];
            }

            return $initialBlocks;
        }

        $initialBlocks[$locales] = $model && method_exists($model, 'getBlockItems') ?
            $this->transformBlockItems($model->getBlockItems($locales)) : [];

        return $initialBlocks;
    }

    /**
     * @param Collection $items
     *
     * @return array
     */
    private function transformBlockItems(Collection $items): array
    {
        $data = [];

        foreach ($items as $item) {
            $data[] = [
                'id'           => $item->id,
                'content'      => $item->content,
                'order'        => $item->order,
                'column_index' => $item->column_index,
                'locale'       => $item->locale,
                'block_id'     => $item->block_id,
                'type'         => $item->block->type,
                'is_layout'    => $item->block->is_layout,
                'children'     => $this->transformBlockItems($item->children),
            ];
        }

        return $data;
    }
}
