<?php

namespace App\Middlewares;

use Core\Http\Request;

class Test
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @return mixed
     */
    public function handle(Request $request): mixed
    {
        //$request->add(['_TOKEN'=>'636346346346346     ']);

        return $request;
    }
}
