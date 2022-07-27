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



namespace Core\Browser;
use Core\Crypt\Crypt;


class Cookie {

    /**
     * @throws \SodiumException
     */
    public static function set($name, $value = null, $expire = null, $path = null, $domain = null, $secure = null, $httponly = true):bool
    {
        if (!is_null($value))
            $value = Crypt::Encrypt($value);
        return setcookie($name, $value, $expire, $path, $domain, $secure, $httponly);
    }

    /**
     * @throws \Exception
     */
    public static function get($name) :string|bool
    {
        if (isset($_COOKIE) && is_array($_COOKIE) && array_key_exists($name, $_COOKIE))
        {
            return Crypt::Decrypt($_COOKIE[$name]);
        }
        return false;
    }

    /**
     * @throws \Exception
     */
    public static function unset($name): bool
    {
        if (self::get($name) != false)
        {
            setcookie($name, "", time() - 3600);
            return true;
        }
        return false;
    }
}