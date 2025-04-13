<?php

use Illuminate\Support\Facades\Route;
use Thinhnx\LaravelPageBuilder\Http\Controllers\PageBuilderController;

Route::post('/render-block', [PageBuilderController::class, 'renderBlock'])->name('render-block');
Route::post('/preview', [PageBuilderController::class, 'preview'])->name('preview');
