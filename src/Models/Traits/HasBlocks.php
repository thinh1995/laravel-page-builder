<?php

declare(strict_types=1);

namespace Thinhnx\LaravelPageBuilder\Models\Traits;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Thinhnx\LaravelPageBuilder\Facades\PageBuilder;

trait HasBlocks
{
    /**
     * @return void
     */
    public static function bootHasBlocks(): void
    {
        static::deleting(function (Model $model) {
            if (in_array(SoftDeletes::class, class_uses_recursive($model))) {
                if (! $model->forceDeleting) {
                    return;
                }
            }

            $model->blocks()->detach();
        });
    }

    /**
     * @return MorphMany
     */
    public function blockItems(): MorphMany
    {
        return $this->morphMany(config('page-builder.models.blockable'), 'blockable');
    }

    /**
     * @param array       $data
     * @param string|null $locale
     *
     * @return void
     */
    public function syncBlockItems(array $data, ?string $locale): void
    {
        $locale ??= config('page-builder.default_locale');
        $this->whereBlocksByLocale($locale)->detach();
        $this->transformBlockItems($data, $locale);

        foreach ($data as $index => $item) {
            app(config('page-builder.models.blockable'))::create([
                'block_id'       => $item['block_id'],
                'blockable_id'   => $this->id,
                'blockable_type' => self::class,
                'content'        => $item['content'] ?? null,
                'order'          => $index,
                'column_index'   => $item['column_index'] ?? 0,
                'locale'         => $locale,
                'children'       => $item['children'] ?? [],
            ]);
        }

        $this->afterBlockItemsSynced($data, $locale);
    }

    /**
     * @param string|null $locale
     *
     * @return BelongsToMany
     */
    protected function whereBlocksByLocale(?string $locale): BelongsToMany
    {
        $locale ??= config('page-builder.default_locale');

        return $this->blocks()->wherePivot('locale', $locale);
    }

    /**
     * @return MorphToMany
     */
    public function blocks(): MorphToMany
    {
        return $this->morphToMany(config('page-builder.models.block'), 'blockable', 'pagebuilder_blockables')
                    ->withPivot(
                        'content',
                        'column_index',
                        'order',
                        'locale',
                        'parent_id',
                        'blockable_id',
                        'blockable_type'
                    )
                    ->orderByPivot('order')
                    ->withTimestamps();
    }

    /**
     * @param array  $data
     * @param string $locale
     *
     * @return void
     */
    protected function transformBlockItems(array &$data, string $locale): void
    {
        $blocks = app(config('page-builder.models.block'))::all();

        foreach ($data as $index => $item) {
            $this->setFormatItem($data[$index], $blocks->firstWhere('id', $item['block_id']));
            $data[$index]['blockable_id']   = $this->id;
            $data[$index]['blockable_type'] = self::class;
            $data[$index]['locale']         = $locale;

            if (isset($item['children'])) {
                $this->transformBlockItems($data[$index]['children'], $locale);
            }
        }
    }

    /**
     * @param array $data
     * @param Model $block
     *
     * @return void
     */
    public function setFormatItem(array &$data, Model $block): void
    {
    }

    /**
     * @param array|Model $data
     * @param Model       $block
     *
     * @return array|Model
     */
    public function getFormatItem(array|Model $data, Model $block): array|Model
    {
        return $data;
    }

    /**
     * @param array $data
     * @param       $locale
     *
     * @return void
     */
    protected function afterBlockItemsSynced(array $data, $locale): void
    {
    }

    /**
     * @param int         $blockId
     * @param string      $content
     * @param int|null    $order
     * @param array       $children
     * @param int         $columnIndex
     * @param string|null $locale
     *
     * @return void
     */
    public function addBlockItem(
        int $blockId,
        string $content,
        int $order = null,
        array $children = [],
        int $columnIndex = 0,
        ?string $locale = null
    ): void {
        $locale ??= config('page-builder.default_locale');
        $order  ??= $this->whereBlocksByLocale($locale)
                         ->wherePivot('column_index', $columnIndex)
                         ->max('order') + 1;

        $data = [
            [
                'block_id'       => $blockId,
                'blockable_id'   => $this->id,
                'blockable_type' => self::class,
                'content'        => $content,
                'order'          => $order,
                'column_index'   => $columnIndex,
                'locale'         => $locale,
                'children'       => $children,
            ]
        ];

        $this->transformBlockItems($data, $locale);

        app(config('page-builder.models.blockable'))::create($data[0]);

        $this->afterBlockItemAdded($blockId, $content, $order, $children, $columnIndex, $locale);
    }

    /**
     * @param int    $blockId
     * @param string $content
     * @param int    $order
     * @param array  $children
     * @param int    $columnIndex
     * @param string $locale
     *
     * @return void
     */
    protected function afterBlockItemAdded(
        int $blockId,
        string $content,
        int $order,
        array $children,
        int $columnIndex,
        string $locale
    ): void {
    }

    /**
     * @param int    $blockItemId
     * @param string $locale
     *
     * @return void
     */
    public function removeBlockItem(int $blockItemId, string $locale): void
    {
        $this->whereBlocksByLocale($locale)
             ->wherePivot('id', $blockItemId)
             ->detach();

        $this->refresh();

        $this->afterBlockItemRemoved($blockItemId, $locale);
    }

    /**
     * @param int    $blockableId
     * @param string $locale
     *
     * @return void
     */
    protected function afterBlockItemRemoved(int $blockableId, string $locale): void
    {
    }

    /**
     * @param string|array|null $locales
     *
     * @return Collection
     */
    public function getBlockItems(string|array|null $locales = null): Collection
    {
        $blocks = PageBuilder::getBlocks();

        $locales ??= config('page-builder.locales');

        $query = app(config('page-builder.models.blockable'))::query();

        if ($locales) {
            if (is_array($locales)) {
                $query->whereIn('locale', $locales);
            } else {
                $query->where('locale', $locales);
            }
        }

        return $query->where('blockable_type', self::class)
                     ->where('blockable_id', $this->id)
                     ->orderBy('column_index')
                     ->orderBy('order')
                     ->get()
                     ->transform(function ($item) use ($blocks) {
                         return $this->getFormatItem($item, $blocks->firstWhere('id', $item->block_id));
                     })
                     ->toTree();
    }
}
