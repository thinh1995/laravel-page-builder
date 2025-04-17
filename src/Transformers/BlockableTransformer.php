<?php

declare(strict_types=1);

namespace Thinhnx\LaravelPageBuilder\Transformers;

use League\Fractal\Resource\Item;
use League\Fractal\TransformerAbstract;
use Thinhnx\LaravelPageBuilder\Models\Blockable;

class BlockableTransformer extends TransformerAbstract
{
    protected array $availableIncludes = [
        'block',
    ];

    /**
     * @param Blockable $blockable
     *
     * @return array
     */
    public function transform(Blockable $blockable): array
    {
        return [
            'id'             => $blockable->id,
            'block_id'       => $blockable->block_id,
            'blockable_id'   => $blockable->blockable_id,
            'blockable_type' => $blockable->blockable_type,
            'parent_id'      => $blockable->parent_id,
            'content'        => $blockable->content,
            'order'          => $blockable->order,
            'column_index'   => $blockable->column_index,
            'locale'         => $blockable->locale,
            'created_at'     => (string)$blockable->created_at,
            'updated_at'     => (string)$blockable->updated_at,
        ];
    }

    /**
     * @param Blockable $blockable
     *
     * @return Item
     */
    public function includeBlock(Blockable $blockable): Item
    {
        return $this->item($blockable->block, new BlockTransformer(), 'block');
    }
}
