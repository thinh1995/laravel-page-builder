<?php

declare(strict_types=1);

namespace Thinhnx\LaravelPageBuilder\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use League\Fractal\Manager;

class Controller extends BaseController
{
    use AuthorizesRequests;
    use ValidatesRequests;
    use ResponseApiTrait;

    public function __construct(Manager $fractal = null)
    {
        $fractal = $fractal ?: new Manager();
        $this->setFractal($fractal);
    }
}
