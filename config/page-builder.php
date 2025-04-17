<?php

return [
    // default locale when you have one locale
    'default_locale' => 'en',

    // All supported locales
    'locales'        => [
        'en',
        'vi',
    ],

    // Route config
    'route'          => [
        'prefix'     => 'page-builder',
        'as'         => 'page-builder.',
        'middleware' => [],
    ],

    // Name for tables
    'tables'         => [
        'block'             => 'pagebuilder_blocks',
        'block_translation' => 'pagebuilder_block_translations',
        'blockable'         => 'pagebuilder_blockables',
    ],

    // The models are used in the package; you can use your own models, as long as they inherit the models below
    'models'         => [
        'block'             => \Thinhnx\LaravelPageBuilder\Models\Block::class,
        'block_translation' => \Thinhnx\LaravelPageBuilder\Models\BlockTranslation::class,
        'blockable'         => \Thinhnx\LaravelPageBuilder\Models\Blockable::class,
    ],

    // The transformers are used in the package; you can use your own transformers, as long as they inherit the transformers below
    'transformers'   => [
        'block'             => \Thinhnx\LaravelPageBuilder\Transformers\BlockTransformer::class,
        'block_translation' => \Thinhnx\LaravelPageBuilder\Transformers\BlockTranslationTransformer::class,
        'blockable'         => \Thinhnx\LaravelPageBuilder\Transformers\BlockableTransformer::class,
    ],
];
