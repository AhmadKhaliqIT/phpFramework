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

    /**
     * @throws Exception
     */
    public static function user(): object
    {
        if (!array_key_exists(self::$_current_middleware_guard,self::$Guards))
            throw new Exception('Guard not Defined!');

        return self::$Guards[self::$_current_middleware_guard]->user();
    }

    public static function guard($guard): object
    {
        if (!array_key_exists($guard,self::$Guards))
            self::$Guards[$guard] = new Guard($guard);

        return self::$Guards[$guard];
    }

}




