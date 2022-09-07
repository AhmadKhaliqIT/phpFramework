<?php
namespace Core\Auth;
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



use Core\Router\RouterBase;
use Exception;

class Auth{

    private static array $Guards=[];
    public  static string $_current_middleware_guard;
    private static array $_config_guards_configs=[];

    public static function init()
    {
        $config_g = config('Auth.guards');
        foreach ($config_g as $config)
        {
            self::$_config_guards_configs[$config['name']] = (object)$config;
        }
    }


    public static function user(): object
    {
        $backtrace = debug_backtrace();
        $GuardName = null;
        if (isset($backtrace[1]["object"]))
        {
            $GuardName = $backtrace[1]["object"]->middlewareStorage('AuthGuardName');
            self::$_current_middleware_guard = $GuardName;
        }
        else if (!empty(self::$_current_middleware_guard))
        {
            $GuardName = self::$_current_middleware_guard;
        }


        if (is_null($GuardName) or empty($GuardName))
            die('Error: Undefined Guard name. please user Auth::guard("name")->user() instead');
        
        //die();
        
//        print_r(self::$_current_middleware_guard);
//        echo "\n";
//        print_r(self::$Guards);

        if (!array_key_exists($GuardName,self::$Guards))
            die('Error: Guard is not defined!');

        return self::$Guards[$GuardName]->user();
    }


    public static function guard($guard): object
    {
        if (empty(self::$_config_guards_configs))
            self::init();

        if (!array_key_exists($guard,self::$_config_guards_configs))
            //die('Error: Guard '.$guard . ' does not exist!');
        throw new Exception('Error: Guard '.$guard . ' does not exist!');

        if (!array_key_exists($guard,self::$Guards))
            self::$Guards[$guard] = new Guard($guard,self::$_config_guards_configs[$guard]->table,self::$_config_guards_configs[$guard]->login_route);

        return self::$Guards[$guard];
    }
}