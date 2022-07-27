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

declare(strict_types=1);
namespace Core\Crypt;

use Exception;
use RangeException;
use SodiumException;

class Crypt{

    public static function hash(string $input):string
    {
        return password_hash($input,PASSWORD_DEFAULT);
    }

    public static function verify(string $password,string $hash):bool
    {
        return password_verify($password,$hash);
    }

    /**
     * @throws SodiumException
     */
    public static function Encrypt(string $message): string
    {
        return self::EncryptWithKey($message,base64_decode(config('Framework.key')));
    }

    /**
     * @throws Exception
     */
    public static function Decrypt(string $message): string
    {
        return self::DecryptWithKey($message,base64_decode(config('Framework.key')));
    }

    /**
     * Encrypt a message
     *
     * @param string $message - message to encrypt
     * @param string $key - encryption key
     * @return string
     * @throws RangeException
     * @throws SodiumException
     * @throws Exception
     */
    public static  function EncryptWithKey(string $message, string $key): string
    {
        if (mb_strlen($key, '8bit') !== SODIUM_CRYPTO_SECRETBOX_KEYBYTES) {
            throw new RangeException('Key is not the correct size (must be 32 bytes).');
        }
        $nonce = random_bytes(SODIUM_CRYPTO_SECRETBOX_NONCEBYTES);

        $cipher = base64_encode(
            $nonce.
            sodium_crypto_secretbox(
                $message,
                $nonce,
                $key
            )
        );
        sodium_memzero($message);
        sodium_memzero($key);
        return $cipher;
    }

    /**
     * Decrypt a message
     *
     * @param string $encrypted - message encrypted with safeEncrypt()
     * @param string $key - encryption key
     * @return string
     * @throws Exception
     */
    public static function DecryptWithKey(string $encrypted, string $key): string
    {
        $decoded = base64_decode($encrypted);
        $nonce = mb_substr($decoded, 0, SODIUM_CRYPTO_SECRETBOX_NONCEBYTES, '8bit');
        $ciphertext = mb_substr($decoded, SODIUM_CRYPTO_SECRETBOX_NONCEBYTES, null, '8bit');

        $plain = sodium_crypto_secretbox_open(
            $ciphertext,
            $nonce,
            $key
        );
        if (!is_string($plain)) {
            throw new Exception('Invalid MAC');
        }
        sodium_memzero($ciphertext);
        sodium_memzero($key);
        return $plain;
    }

    /**
     * @throws Exception
     */
    public static function createKey($base64 = false): string
    {
        $key = random_bytes(SODIUM_CRYPTO_SECRETBOX_KEYBYTES);
        return ($base64)?base64_encode($key):$key;
    }


}