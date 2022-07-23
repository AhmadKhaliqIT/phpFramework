<?php
namespace Core\Database;
/* بسم الله الرحمن الرحیم */
/****************************************
 **** Class developers: *****************
 **** Ahmad Khaliq   ********************
 **** Email: Ahmad.Khaliq@yahoo.com *****
 **** 2021  *****************************
 ***************************************/


class DB {

    private static Builder $_Builder;

    public static function table($name): Builder
    {
        self::$_Builder = new Builder();
        self::$_Builder->table($name);

        return self::$_Builder;
    }
}