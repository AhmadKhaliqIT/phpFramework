<?php

namespace App\Controllers;

use Core\Core;
use Exception;

class Controller
{
    /**
     * Register middleware on the controller.
     *
     * @param string $middleware
     * @return mixed
     */
    public function middleware(string $middleware): mixed
    {
        $middlewareClassName = 'App\Middlewares\\'.$middleware;
        $middlewareObject = new $middlewareClassName;
        return $middlewareObject->handle(Core()->Request());
    }

    /**
     * Handle calls to missing methods on the controller.
     *
     * @param string $method
     * @param array $parameters
     * @return void
     *
     * @throws Exception
     */
    public function __call(string $method, array $parameters)
    {
        throw new Exception("Method [{$method}] does not exist on [".get_class($this).'].');
    }
}
