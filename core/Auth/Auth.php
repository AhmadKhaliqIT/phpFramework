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

class Auth{

    public static string $_current_middleware_guard;

    public static function user(): object
    {
        echo self::$_current_middleware_guard.'--';
        return (object)[];
    }

    public static function guard($guard): object
    {

        return (object)[];
    }

}




