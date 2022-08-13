<?php
/* بسم الله الرحمن الرحیم */
/**
 * phpFramework
 *
 * @author     Ahmad Khaliq
 * @author     Mojtaba Zadegi
 * @copyright  2022 Ahmad Khaliq
 * @license    https://github.com/AhmadKhaliqIT/phpFramework/blob/main/LICENSE
 * @link       https://github.com/AhmadKhaliqIT/phpFramework/
 */


namespace App\Controllers;

use Core\Core;
use Exception;

class Controller
{

    public array $_Middlewares_storage;

    public function middlewareStorage($key,$new_Value = null)
    {
        if (is_null($new_Value))
            return $this->_Middlewares_storage[$key];

        $this->_Middlewares_storage[$key] = $new_Value;
    }



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
