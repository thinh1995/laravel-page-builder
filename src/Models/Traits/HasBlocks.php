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

            $model->blockables()->delete();
        });
    }

    public function blockables(): MorphMany
    {
        return $this->morphMany(Blockable::class, 'blockable');
    }

    public function syncBlocks(array $blocksData, string $locale = 'vi'): void
    {
        $this->whereBlocksByLocale($locale)->detach();
        $this->transformBlockables($blocksData);

        foreach ($blocksData as $index => $blockData) {
            Blockable::create([
                'block_id'       => $blockData['block_id'],
                'blockable_id'   => $this->id,
                'blockable_type' => self::class,
                'content'        => $blockData['content'] ?? null,
                'order'          => $index,
                'column_index'   => $blockData['column_index'] ?? 0,
                'locale'         => $locale,
                'children'       => $blockData['children'] ?? [],
            ]);
        }

        $this->afterBlocksSynced($blocksData, $locale);
    }

    protected function whereBlocksByLocale(string $locale = 'vi'): BelongsToMany
    {
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

    protected function transformBlockables(&$blocksData): void
    {
        foreach ($blocksData as $index => $blockData) {
            $this->setFormatBlockData($blockData);
            $blocksData[$index]['blockable_id']   = $this->id;
            $blocksData[$index]['blockable_type'] = self::class;

            if (isset($blockData['children'])) {
                $this->transformBlockables($blocksData[$index]['children']);
            }
        }
    }

    public function setFormatBlockData(&$data): void
    {
    }

    protected function afterBlocksSynced(array $blocksData, $locale): void
    {
    }

    public function addBlock(
        int $blockId,
        string $content,
        int $order = null,
        array $children = [],
        int $columnIndex = 0,
        string $locale = 'vi'
    ): void {
        $order ??= $this->whereBlocksByLocale($locale)->max('order') + 1;
        $this->transformBlockables($children);

        Blockable::create([
            'block_id'       => $blockId,
            'blockable_id'   => $this->id,
            'blockable_type' => self::class,
            'content'        => $content,
            'order'          => $order,
            'column_index'   => $columnIndex,
            'locale'         => $locale,
            'children'       => $children,
        ]);

        $this->afterBlockAdded($blockId, $content, $order, $children, $columnIndex, $locale);
    }

    protected function afterBlockAdded(
        int $blockId,
        string $content,
        int $order,
        array $children,
        int $columnIndex,
        string $locale
    ): void {
    }

    public function removeBlock(int $blockableId, string $locale): void
    {
        $this->whereBlocksByLocale($locale)->detach($blockableId);

        $this->afterBlockRemoved($blockableId, $locale);
    }

    protected function afterBlockRemoved(int $blockableId, string $locale): void
    {
    }

    public function getBlocksByLocale(string $locale = 'vi'): Collection
    {
        return Blockable::with(relations: 'block')
                        ->where('locale', $locale)
                        ->where('blockable_type', self::class)
                        ->where('blockable_id', $this->id)
                        ->orderBy('column_index')
                        ->orderBy('order')
                        ->get()
                        ->transform(function ($item) {
                            $this->getFormatBlockData($item);

                            return $item;
                        })
                        ->toTree();
    }

    public function getFormatBlockData(&$data): void
    {
    }
}
