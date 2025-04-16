<?php

declare(strict_types=1);

namespace Thinhnx\LaravelPageBuilder\Tests\Unit\Controllers;

use Illuminate\Support\Facades\View;
use Thinhnx\LaravelPageBuilder\Tests\TestCase;

class PageBuilderControllerTest extends TestCase
{
    protected function setUp(): void
    {
        $this->refreshApplication();
        $this->cleanFiles();
        parent::setUp();
        $this->initialize();
    }

    public function test_render_block_return_view()
    {
        $response = $this->post(route('page-builder.render-block'), [
            'type'    => 'text',
            'content' => 'test',
        ]);

        $response->assertOk();
        View::shouldReceive('page-builder::blocks.text')->with(['content' => 'test']);
    }

    public function test_render_block_return_json()
    {
        $response = $this->postJson(route('page-builder.render-block'), [
            'type'    => 'text',
            'content' => 'test',
        ]);

        $response->assertOk();
        $this->assertJson(json_encode([
            'data' => base64_encode(view('page-builder::blocks.text', ['content' => 'test'])->render())
        ]));
    }

    public function test_render_block_return_invalid_request()
    {
        $response = $this->post(route('page-builder.render-block'), [
            'type' => 'something',
        ]);

        $response->assertInvalid(['type' => ['The selected type is invalid.']]);
    }

    public function test_preview_return_view()
    {
        $response = $this->post(route('page-builder.preview'), [
            'locale' => 'vi'
        ]);

        $response->assertOk();
        View::shouldReceive('page-builder::preview')->with(['locale' => 'vi']);
    }

    public function test_preview_return_json()
    {
        $response = $this->post(route('page-builder.preview'), [
            'locale' => 'vi'
        ]);

        $response->assertOk();
        $this->assertJson(json_encode([
            'data' => base64_encode(view('page-builder::preview', ['locale' => 'vi', 'blocks' => []])->render())
        ]));
    }

    public function test_preview_return_invalid_request()
    {
        $response = $this->post(route('page-builder.preview'));

        $response->assertInvalid(['locale' => ['The locale field is required.']]);
    }
}
