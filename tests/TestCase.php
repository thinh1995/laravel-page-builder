<?php

declare(strict_types=1);

namespace Thinhnx\LaravelPageBuilder\Tests;

use Orchestra\Testbench\TestCase as OrchestraTestCase;
use Thinhnx\LaravelPageBuilder\LaravelPageBuilderProvider;

abstract class TestCase extends OrchestraTestCase
{
    protected function getPackageProviders($app): array
    {
        return [
            LaravelPageBuilderProvider::class,
        ];
    }
}
