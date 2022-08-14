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



namespace Core\Database;
class DB {
    private static ?\mysqli $_connection;
    private static Builder $_Builder;

    public static function table($name): Builder
    {
        self::$_Builder = new Builder();
        self::$_Builder->table($name);

        return self::$_Builder;
    }

    private static function getConnection()
    {
        global $_connection;
        if (empty(self::$_connection) or is_null(self::$_connection) or !is_resource(self::$_connection))
            self::$_connection = $_connection;
    }

    public static function beginTransaction()
    {
        self::getConnection();
        self::$_connection->begin_transaction();
    }

    public static function commit()
    {
        self::$_connection->commit();
    }

    public static function rollback()
    {
        self::$_connection->rollback();
    }


}