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



namespace App\Middlewares;

use Closure;
use Core\Browser\Session;
use Core\Http\Request;
use Core\Auth\Auth as AuthBass;
use Core\Auth\Guard;

class csrf
{
    private string $Current_Method;
    private array $except_methods=[];


    public function handle(Request $request)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST')
        {
            if(!isset($request->_token) or Core()->Session()->token() !== $request->_token)
                abort(403);
        }

    }




}
