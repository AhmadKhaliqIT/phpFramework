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
    private array $Current_Methods_stack;
    private array $except_methods=[];
    private string $Current_Guard_name;

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
                $this->Current_Methods_stack[] = strtolower($method[1]);
            }
        }
        return $this;
    }

    public function guard($guardName): Auth
    {
        $backtrace = debug_backtrace();
        $backtrace[1]["object"]->middlewareStorage('AuthGuardName',$guardName);
        $this->Current_Guard_name = $guardName;
        //echo ((AuthBass::$_current_middleware_guard ?? 'notDefined') .' - '. $guardName);
        //AuthBass::$_current_middleware_guard = $guardName;
        return $this;
    }


    public function except(array|string $methods): Auth
    {
        if (is_string($methods))
            $methods = [$methods];

        $this->except_methods = array_merge($this->except_methods,$methods);
        $this->except_methods = array_map('strtolower', array_unique($this->except_methods));
        return $this;
    }

    public function makeSafe()
    {

        if ($this->in_array_any($this->Current_Methods_stack,$this->except_methods))
            return true;

        if(AuthBass::Guard($this->Current_Guard_name)->check())
            return true;
        
        AuthBass::Guard($this->Current_Guard_name)->redirect_to_login_form();
        die('please login');
    }

    function in_array_any($needles, $haystack): bool
    {
        return !empty(array_intersect($needles, $haystack));
    }

}
