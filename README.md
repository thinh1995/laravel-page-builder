# Laravel Page Builder
[![Latest Version on Packagist](https://img.shields.io/packagist/v/thinhnx/laravel-page-builder.svg)](https://packagist.org/packages/thinhnx/laravel-page-builder)
[![Tests](https://github.com/thinh1995/laravel-page-builder/actions/workflows/main.yml/badge.svg)](https://github.com/thinh1995/laravel-page-builder/actions/workflows/php.yml)
[![Test Coverage](https://github.com/thinh1995/laravel-page-builder/blob/master/tests/_reports/badge-coverage.svg)](https://github.com/thinh1995/laravel-page-builder/blob/master/tests/_reports/clover.xml)
[![License](https://img.shields.io/badge/license-mit-blue.svg)](https://github.com/thinh1995/laravel-page-builder/blob/master/LICENSE)
[![Total Downloads](https://img.shields.io/packagist/dt/thinhnx/laravel-page-builder.svg)](https://packagist.org/packages/thinhnx/laravel-page-builder)

A simple Laravel package for creating blocks using drag and drop.

[![DEMO](https://img.youtube.com/vi/Ts-lTfCwK5k/0.jpg)](https://youtu.be/Ts-lTfCwK5k?si=mB0HCpmK144J7u_k)

## Requirements
- PHP >= 8.1
- Laravel >= 10.0

## Installation
### 1. Install via Composer:
```shell
composer require thinhnx/laravel-page-builder
```

### 2. Run install command:
```shell
php artisan page-builder:install
```
Console will confirm you to run migration and run seeder, please type `yes` and press enter.

## Configuration
The configuration file is located at `config/page-builder.php`. 
```php
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

    // The models are used in the package, you can use your own models, as long as they inherit the models below
    'models'         => [
        'block'             => \Thinhnx\LaravelPageBuilder\Models\Block::class,
        'block_translation' => \Thinhnx\LaravelPageBuilder\Models\BlockTranslation::class,
        'blockable'         => \Thinhnx\LaravelPageBuilder\Models\Blockable::class,
    ],

    // The transformers are used in the package, you can use your own transformers, as long as they inherit the transformers below
    'transformers'   => [
        'block'             => \Thinhnx\LaravelPageBuilder\Transformers\BlockTransformer::class,
        'block_translation' => \Thinhnx\LaravelPageBuilder\Transformers\BlockTranslationTransformer::class,
        'blockable'         => \Thinhnx\LaravelPageBuilder\Transformers\BlockableTransformer::class,
    ],
];
```

## Usage
### 1. Apply to a model
Example for a model. You need use `HasBlocks` trait in the model.
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Thinhnx\LaravelPageBuilder\Models\Traits\HasBlocks;

class Page extends Model
{
    use HasBlocks;

    protected $fillable = ['name'];

    protected function setFormatItem(array &$data, Model $block): void
    {
        $data['content'] = $block->type === 'text' ? e($data['content']) : $data['content'];
    }

    public function getFormatItem(array|Model $data, Model $block): array|Model
    {
        $data['content'] = $block->type === 'text' ? htmlspecialchars_decode($data['content']) : $data['content'];

        return $data;
    }
}
```
In the Page model above, I override 2 functions: `setFormatItem()` and `getFormatItem()`. The purpose is to escape when saving and retrieving data. You can rewrite these 2 functions depending on your purpose.

On the create/edit Page screen, you use Facade function `PageBuilder::render()` to render the block editor view. 

Below is an example of the create page screen:
```php
@extends('layouts.app')

@section('content')
    <form id="createForm" action="/pages/create" method="post">
        @csrf
        <div class="mb-3">
            <label class="form-label" for="name">Name<label>
            <input class="form-control" name="name" required />
        </div>
        <div>
            {{ PageBuilder::render() }}
        </div>
    </form>
    <scritp>
        window.formId = 'createForm';
    </script>
@endsection
```
Below is an example of the edit page screen:
```php
@extends('layouts.app')

@section('content')
    <form id="editForm" action="/pages/{{ $page->id }}" method="post">
        @csrf
        @method('PUT')
        <div class="mb-3">
            <label class="form-label" for="name">Name<label>
            <input class="form-control" name="name" required />
        </div>
        <div>
            {{ PageBuilder::render($page) }}
        </div>
    </form>
    <scritp>
        window.formId = 'editForm';
    </script>
@endsection
```

Blocks data will be contained in a hidden input tag named `blocks`.

You use the `syncBlockItems()` function to synchronize new block items and existing block items of the model according to locale. Example:
```php
<?php
$data = json_decode($request->get('blocks'), true);

// For default locale
$page->syncBlockItems($data[config('page-builder.default_locale')]);

// For multiple locales
foreach (config('page-builder.locales') as $locale) {
    $page->syncBlockItems($data[$locale], $locale);
}
```
### 2. Create new block
Currently, I have created 3 blocks:
- Text
- Two Columns
- There Columns

Each block will have its own view file. You can change them in the path `resources/views/vendor/page-builder/blocks`.

You can create a new block with the following command:
```shell
php artisan page-builder:block:create
```
The console screen will ask you to enter the locale name, type and specify if it is a layout type (a layout type can contain blocks). After running the command, a block view will be created at the path `resources/views/vendor/page-builder/blocks/{type}.blade.php`.

When updating a block view, you need to note:
- There must be only one root div tag and it must have the class `block-content`.
- For block where `is_layout` is `false`, you must add the class `block-content` to the tag containing the block's content (example: input, textarea, ...).
- For block where `is_layout` is `true`, you must add the class `sortable-column` and the attribute `data-column="0,1,2,.."` to the tag you want to be a column.

See `text.blade.php`, `layout-2.blade.php` at path `resources/views/vendor/page-builder/blocks` files for more details

### 3. Update views
The package views are located at `resources/views/vendor/page-builder`. You can update them to suit your project.
```
📁 blocks // folder contains block views
  |- 📄 layout-2.blade.php // view for block Two Columns
  |- 📄 layout-3.blade.php // view for block Three Columns
  |- 📄 text.blade.php // view for block Text
  
📁 paritals
  |- 📄 block.blade.php // view for each block in preview
  |- 📄 modal-preview.blade.php // view for modal preview
  
📄 page-builder.blade.php // view for blocks editor
📄 preview.blade.php // view for iframe in modal preview
```

### 4. Update JS and CSS
The package assets are located at `public/packages/thinhnx/page-builder`. You can update them to suit your project.

## Testing
```shell
composer test
```

## Contributing
1. Fork the repository.
2. Create a new branch (git checkout -b feature-branch).
3. Commit your changes (git commit -m 'Add new feature').
4. Push to the branch (git push origin feature-branch).
5. Create a Pull Request.
## License
This package is open-sourced software licensed under the [MIT license](LICENSE).

## Support
If you have any issues, please open an issue on [GitHub](https://github.com/thinh1995/laravel-page-builder/issues).

Love using this package? Consider buying me a coffee to support ongoing development! Every little bit helps keep this project alive.

- [**Buy Me a Coffee**](https://ko-fi.com/lucifer293)
- [**PayPal**](https://www.paypal.com/paypalme/xuanthinhme)
