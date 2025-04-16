<?php

declare(strict_types=1);

namespace Thinhnx\LaravelPageBuilder\Tests\Feature\Routes;

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

    public function test_route_render_block()
    {
        $response = $this->post(route('page-builder.render-block'), [
            'type'    => 'text',
            'content' => 'test content',
        ]);

        $response->assertOk();
        $response->assertSee('test content');
        $response->assertViewIs('page-builder::blocks.text');

        $response = $this->post(route('page-builder.render-block'), [
            'type' => 'layout-2',
        ]);

        $response->assertOk();
        $response->assertViewIs('page-builder::blocks.layout-2');

        $response = $this->post(route('page-builder.render-block'), [
            'type' => 'layout-3',
        ]);

        $response->assertOk();
        $response->assertViewIs('page-builder::blocks.layout-3');
    }

    public function test_route_preview()
    {
        $response = $this->post(route('page-builder.preview'), [
            'locale' => 'vi',
            'blocks' => [
                [
                    'block_id'       => '1',
                    'blockable_id'   => '26',
                    'blockable_type' => 'App\Models\Page',
                    'type'           => 'layout-2',
                    'content'        => '',
                    'column_index'   => 0,
                    'order'          => 0,
                    'children'       => [
                        [
                            'block_id'       => '3',
                            'blockable_id'   => null,
                            'blockable_type' => null,
                            'type'           => 'text',
                            'content'        => 'Test content',
                            'column_index'   => 0,
                            'order'          => 0,
                            'children'       => []
                        ]
                    ]
                ]
            ],
        ]);

        $response->assertOk();
        $response->assertViewIs('page-builder::preview');
        $response->assertViewHas('blocks');
        $response->assertSee('Test content');
    }
}
