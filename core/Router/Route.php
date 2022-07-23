<?php
namespace Core\Router;
/* بسم الله الرحمن الرحیم */
/****************************************
 **** Class developers: *****************
 **** Ahmad Khaliq   ********************
 **** Email: Ahmad.Khaliq@yahoo.com *****
 **** 2021  *****************************
 ***************************************/


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




