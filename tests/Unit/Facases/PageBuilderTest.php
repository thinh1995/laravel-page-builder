<?php

declare(strict_types=1);

namespace Thinhnx\LaravelPageBuilder\Tests\Unit\Facases;

use Illuminate\Contracts\View\View;
use Thinhnx\LaravelPageBuilder\Facades\PageBuilder;
use Thinhnx\LaravelPageBuilder\Tests\TestCase;

class PageBuilderTest extends TestCase
{
    protected function setUp(): void
    {
        $this->refreshApplication();
        $this->cleanFiles();
        parent::setUp();
        $this->initialize();
    }

    public function test_method_render()
    {
        $view = PageBuilder::render();
        $this->assertInstanceOf(View::class, $view);

        $blade = $this->blade($view->render());
        $blade->assertSee('<h3>Page layout</h3>', false);
    }
}
