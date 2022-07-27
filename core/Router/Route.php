<?php
namespace Core\Router;
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



class Route{
    private static RouterBase $_RouterBase;

    public static function get($uri,$function){
        self::$_RouterBase = new RouterBase;
        self::$_RouterBase->reserve($uri,$function,'GET');
        return self::$_RouterBase;
    }

    public static function post($uri,$function){
        self::$_RouterBase = new RouterBase;
        self::$_RouterBase->reserve($uri,$function,'POST');
        return self::$_RouterBase;
    }

}




