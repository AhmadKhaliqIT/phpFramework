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

class Auth
{
    private string $Current_Method;
    private array $except_methods=[];

    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @return Auth
     */
    public function handle(Request $request): Auth
    {
        /* Find which method is called by Router */
        foreach (debug_backtrace() as $trace)
        {
            if($trace['function']=='callMethod')
            {
                $method = explode('@',$trace['args'][0]);
                $this->Current_Method = strtolower($method[1]);
            }
        }
        return $this;
    }

    public function guard($guardName): Auth
    {
        AuthBass::$_current_middleware_guard = $guardName;
        return $this;
    }


    public function except(...$methods): Auth
    {
        $this->except_methods = array_merge($this->except_methods,$methods);
        $this->except_methods = array_map('strtolower', array_unique($this->except_methods));
        return $this;
    }

    public function makeSafe()
    {
        if (in_array($this->Current_Method,$this->except_methods))
            return true;


        if(AuthBass::Guard(AuthBass::$_current_middleware_guard)->check())
            return true;

        AuthBass::Guard(AuthBass::$_current_middleware_guard)->redirect_to_login_form();
        die('please login');
    }


}
