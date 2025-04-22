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
        'middleware' => ['web'],
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

    // Cache config
    'cache' => [
        'enabled' => true,
        'time'    => 60 * 60 * 24,
        'keys'    => [
            'blocks' => 'pagebuilder_blocks',
        ],
    ],
];
