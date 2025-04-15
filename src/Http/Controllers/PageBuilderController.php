<?php

declare(strict_types=1);

namespace Thinhnx\LaravelPageBuilder\Http\Controllers;

use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PageBuilderController extends Controller
{
    public function renderBlock(Request $request): View|JsonResponse
    {
        $request->validate(['type' => ['required', 'exists:' . config('page-builder.tables.block') . ',type']]);

        $type    = strtolower($request->get('type'));
        $content = $request->get('content') ?: null;

        if ($request->ajax()) {
            return $this->respondWithArray([
                'data' => base64_encode(view("page-builder::blocks.$type", compact('content'))->render()),
            ]);
        }

        return view("page-builder::blocks.$type", compact('content'));
    }

    public function preview(Request $request): View|JsonResponse
    {
        $request->validate([
            'locale'   => ['required', 'in:' . implode(',', config('page-builder.locales'))],
            'blocks'   => ['nullable', 'array'],
            'blocks.*' => ['array'],
            'context'  => ['nullable', 'array'],
        ]);

        $locale  = $request->get('locale');
        $blocks  = $request->get('blocks', []);
        $context = $request->get('context', []);

        if ($request->ajax()) {
            return $this->respondWithArray([
                'data' => base64_encode(
                    view('page-builder::preview', compact('locale', 'blocks', 'context'))->render()
                ),
            ]);
        }

        return view('page-builder::preview', compact('locale', 'blocks', 'context'));
    }
}
