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

    private static Builder $_Builder;

    public static function table($name): Builder
    {
        self::$_Builder = new Builder();
        self::$_Builder->table($name);

        return self::$_Builder;
    }
}