<?php

declare(strict_types=1);

namespace Thinhnx\LaravelPageBuilder\Transformers;

use League\Fractal\Resource\Collection;
use League\Fractal\TransformerAbstract;
use Thinhnx\LaravelPageBuilder\Models\Block;

class BlockTransformer extends TransformerAbstract
{
    protected array $defaultIncludes = ['translations'];

    /**
     * @param Block $block
     *
     * @return array
     */
    public function transform(Block $block): array
    {
        return [
            'id'         => $block->id,
            'name'       => $block->name,
            'type'       => $block->type,
            'is_layout'  => $block->is_layout,
            'created_at' => (string)$block->created_at,
            'updated_at' => (string)$block->updated_at,
        ];
    }

    /**
     * @param Block $block
     *
     * @return Collection
     */
    public function includeTranslations(Block $block): Collection
    {
        return $this->collection($block->translations, new BlockTranslationTransformer(), 'translations');
    }
}
