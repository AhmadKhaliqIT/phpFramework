<?php

namespace App\Middlewares;

use Closure;
use Core\Http\Request;

class Auth
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @return mixed
     */
    public function handle(Request $request): mixed
    {
        echo 'Auth';

        return $request;
    }
}
