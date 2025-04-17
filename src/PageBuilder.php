<?php

declare(strict_types=1);

namespace Thinhnx\LaravelPageBuilder;

use Illuminate\Contracts\View\Factory as ViewFactory;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Model;

class PageBuilder
{
    protected $viewFactory;

    public function __construct(ViewFactory $viewFactory)
    {
        $this->viewFactory = $viewFactory;
    }

    /**
     * @param Model|null $model
     * @param array      $locales
     *
     * @return View
     */
    public function render(?Model $model = null, array $locales = []): View
    {
        $locales = empty($locales) ? config('page-builder.locales') : $locales;

        return $this->viewFactory->make('page-builder::page-builder', compact('locales', 'model'));
    }
}
