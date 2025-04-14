<?php

return [
    'default_locale' => 'vi',

    'locales' => [
        'vi',
        'en',
    ],

    'route' => [
        'prefix'     => 'page-builder',
        'as'         => 'page-builder.',
        'middleware' => [],
    ],

    'tables' => [
        'block'             => 'pagebuilder_blocks',
        'block_translation' => 'pagebuilder_block_translations',
        'blockable'         => 'pagebuilder_blockables',
    ],

    'models' => [
        'block'             => \Thinhnx\LaravelPageBuilder\Models\Block::class,
        'block_translation' => \Thinhnx\LaravelPageBuilder\Models\BlockTranslation::class,
        'blockable'         => \Thinhnx\LaravelPageBuilder\Models\Blockable::class,
    ],

    'transformers' => [
        'block'             => \Thinhnx\LaravelPageBuilder\Transformers\BlockTransformer::class,
        'block_translation' => \Thinhnx\LaravelPageBuilder\Transformers\BlockTranslationTransformer::class,
        'blockable'         => \Thinhnx\LaravelPageBuilder\Transformers\BlockableTransformer::class,
    ],
];
