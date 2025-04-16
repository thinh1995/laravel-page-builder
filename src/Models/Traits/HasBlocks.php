<?php

declare(strict_types=1);

namespace Thinhnx\LaravelPageBuilder\Models\Traits;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Thinhnx\LaravelPageBuilder\Models\Block;
use Thinhnx\LaravelPageBuilder\Models\Blockable;

trait HasBlocks
{
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

    public function blockItems(): MorphMany
    {
        return $this->morphMany(Blockable::class, 'blockable');
    }

    public function syncBlockItems(array $data, ?string $locale): void
    {
        $locale ??= config('page-builder.default_locale');
        $this->whereBlocksByLocale($locale)->detach();
        $this->transformBlockItems($data);

        foreach ($data as $index => $item) {
            Blockable::create([
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

    protected function whereBlocksByLocale(?string $locale): BelongsToMany
    {
        $locale ??= config('page-builder.default_locale');

        return $this->blocks()->wherePivot('locale', $locale);
    }

    public function blocks(): MorphToMany
    {
        return $this->morphToMany(Block::class, 'blockable', 'pagebuilder_blockables')
                    ->withPivot(
                        'content',
                        'column_index',
                        'order',
                        'locale',
                        'parent_id',
                        'blockable_id',
                        'blockable_type'
                    )
                    ->orderBy('pivot_order')
                    ->withTimestamps();
    }

    protected function transformBlockItems(array &$data): void
    {
        $blocks = app(config('page-builder.models.block'))::all();

        foreach ($data as $index => $item) {
            $this->setFormatItem($data[$index], $blocks->firstWhere('id', $item['block_id']));
            $data[$index]['blockable_id']   = $this->id;
            $data[$index]['blockable_type'] = self::class;

            if (isset($item['children'])) {
                $this->transformBlockItems($data[$index]['children']);
            }
        }
    }

    public function setFormatItem(array &$data, Model $block): void
    {
    }

    public function getFormatItem(array|Model $data, Model $block): array|Model
    {
        return $data;
    }

    protected function afterBlockItemsSynced(array $data, $locale): void
    {
    }

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

        $this->transformBlockItems($data);

        Blockable::create($data[0]);

        $this->afterBlockItemAdded($blockId, $content, $order, $children, $columnIndex, $locale);
    }

    protected function afterBlockItemAdded(
        int $blockId,
        string $content,
        int $order,
        array $children,
        int $columnIndex,
        string $locale
    ): void {
    }

    public function removeBlockItem(int $blockItemId, string $locale): void
    {
        $this->whereBlocksByLocale($locale)
             ->wherePivot('id', $blockItemId)
             ->detach();

        $this->refresh();

        $this->afterBlockItemRemoved($blockItemId, $locale);
    }

    protected function afterBlockItemRemoved(int $blockableId, string $locale): void
    {
    }

    public function getBlockItemsByLocale(?string $locale = null): Collection
    {
        $locale ??= config('page-builder.default_locale');

        return Blockable::with(relations: 'block')
                        ->where('locale', $locale)
                        ->where('blockable_type', self::class)
                        ->where('blockable_id', $this->id)
                        ->orderBy('column_index')
                        ->orderBy('order')
                        ->get()
                        ->transform(function ($item) {
                            return $this->getFormatItem($item, $item->block);
                        })
                        ->toTree();
    }
}
