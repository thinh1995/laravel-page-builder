<?php

declare(strict_types=1);

namespace Thinhnx\LaravelPageBuilder\Transformers;

use League\Fractal\TransformerAbstract;
use Thinhnx\LaravelPageBuilder\Models\BlockTranslation;

class BlockTranslationTransformer extends TransformerAbstract
{
    /**
     * @param BlockTranslation $blockTranslation
     *
     * @return array
     */
    public function transform(BlockTranslation $blockTranslation): array
    {
        return [
            'id'          => $blockTranslation->id,
            'block_id'    => $blockTranslation->block_id,
            'locale'      => $blockTranslation->locale,
            'name'        => $blockTranslation->name,
            'description' => $blockTranslation->description,
            'created_at'  => (string)$blockTranslation->created_at,
            'updated_at'  => (string)$blockTranslation->updated_at,
        ];
    }
}
