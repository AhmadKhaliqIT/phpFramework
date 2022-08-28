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
use Exception;

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

    public static function select($query): array
    {
        self::getConnection();
        $result = self::$_connection->query($query);
        if(isset(self::$_connection->error) and !empty(self::$_connection->error))
            throw new Exception('Database Error: '.self::$_connection->error);
        $output_result=[];
        if ($result->num_rows > 0)
            while($row = $result->fetch_assoc()) {
                $output_result[] = (object) $row;
            }
        return $output_result;
    }

    public static function statement($query): bool
    {
        self::getConnection();
        $result = self::$_connection->query($query);

        if ($result === TRUE) {
            return true;
        } else {
            throw new Exception('Database Error: '.self::$_connection->error);
        }
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