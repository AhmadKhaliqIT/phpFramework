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



namespace Core\DataTables;
class DataTables{


    public static function of($source)
    {
        return self::make($source);
    }


    public static function make($source)
    {
        $engines  = [
            "query" => "Core\DataTables\QueryDataTable",
            "collection" => "Core\DataTables\CollectionDataTable",
        ];


        $args = func_get_args();


        foreach ($engines as $engine => $class) {
            if (call_user_func_array([$engines[$engine], 'canCreate'], $args)) {
                return call_user_func_array([$engines[$engine], 'create'], $args);
            }
        }

        throw new \Exception('No available engine for ' . get_class($source));
    }

}