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

use Core\FileSystem\FileSystem;
use Exception;
use JetBrains\PhpStorm\Pure;
use SodiumException;

class Session
{
    protected string $_StoragePath = BASE_PATH.'/cache/sessions/';
    protected string $_SessionID = '';
    protected int $_UserID;
    protected string $_UserAgent = '';
    protected int $_LastActivity = 0;
    protected int $_LifeTimeSeconds = 3600 * 4;
    //protected FileSystem $_File;
    //protected bool $_started = false;
    protected array $_ITEMS = [];

    /**
     * @throws Exception
     */
    public function __construct(int $LifeTimeSeconds=null)
    {
        //$this->_File = $File;
        if (!is_null($LifeTimeSeconds))
            $this->_LifeTimeSeconds = $LifeTimeSeconds;

        //$this->_started = true;

        $id_inside_cookie = Cookie::get('SahandSID');

        if ($id_inside_cookie === false) {
            $this->create();
        }
        else {
            $this->_SessionID = $id_inside_cookie;
            $this->loadSession();
        }

        if (! $this->has('_token')) {
            $this->regenerateToken();
        }


    }

    public function __destruct() {
        $this->ageFlashData();
        $this->saveToFile();
    }


    /**
     * @throws SodiumException
     * @throws Exception
     */
    private function create(): void
    {
        $unique_id_generated = false;
        while (!$unique_id_generated) {
            $this->_SessionID = $this->generateString(40);
            if (!FileSystem::exists($this->_StoragePath . $this->_SessionID))
                $unique_id_generated = true;
        }

        $this->_UserID = $this->getUserId();
        $this->_UserAgent = $_SERVER['HTTP_USER_AGENT'];
        $this->_LastActivity = 0;
        $this->_ITEMS = [];


        $this->saveToFile();
        Cookie::set('SahandSID', $this->_SessionID);
    }

    private function saveToFile(): void
    {
        FileSystem::put($this->_StoragePath . $this->_SessionID, $this->prepareToStore());
    }

    private function prepareToStore(): string
    {
        $session = (object)[
            '_SessionID' => $this->_SessionID,
            '_UserID' => $this->_UserID,
            '_UserAgent' => $this->_UserAgent,
            '_LastActivity' => time(),
            '_ITEMS' => $this->_ITEMS
        ];
        return serialize($session);
    }

    /**
     * @throws Exception
     */
    private function loadSession(): void
    {
        $CreateFlag = true;
        if (FileSystem::exists($this->_StoragePath . $this->_SessionID)) {
            $contents = FileSystem::get($this->_StoragePath . $this->_SessionID);
            $session = @unserialize($contents);
            if (($session !== false) and isset($session->_UserAgent) and !is_null($session->_UserAgent) && $session->_UserAgent === $_SERVER['HTTP_USER_AGENT'] and $session->_LastActivity > (time() - $this->_LifeTimeSeconds))
            {
                $this->_UserID = $session->_UserID;
                $this->_UserAgent = $session->_UserAgent;
                $this->_ITEMS = (array)$session->_ITEMS;
                $CreateFlag = false;
            }
        }

        if ($CreateFlag)
            $this->create();
    }

    private function getUserId(): int
    {
        return -1; /* todo get from auth */
    }


    /**
     * @throws Exception
     */
    private function generateString($length = 10): string
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyz';
        $charactersLength = 36; //stolen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[random_int(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    public function put(string|array $key, string|array $value = null): void
    {
        if (! is_array($key)) {
            $key = [$key => $value];
        }

        foreach ($key as $arrayKey => $arrayValue) {
            $this->_ITEMS[$arrayKey] = $arrayValue;
        }

        //print_r($this->_ITEMS3);

    }

    public function get($key, $default = null): string|array|null
    {
        if (!array_key_exists($key,$this->_ITEMS))
            return $default;

        return $this->_ITEMS[$key];
    }

    public function flash(string $key, $value = true): void
    {
        $this->put($key, $value);
        $this->push('_flash_new', $key);
        $this->removeFromOldFlashData([$key]);
    }

    public function ageFlashData(): void
    {
        $this->forget($this->get('_flash_old', []));
        $this->put('_flash_old', $this->get('_flash_new', []));
        $this->put('_flash_new', []);
    }

    protected function removeFromOldFlashData(array $keys): void
    {
        $this->put('_flash_old', array_diff($this->get('_flash_old', []), $keys));
    }

    /*The push method may be used to push a new value onto a session value that is an array*/
    public function push($key, $value): void
    {
        $array = $this->get($key, []);
        if (is_array($array))
        {
            $array[] = $value;
            $this->put($key, $array);
        }
    }

    public function remove($key)
    {
        return $this->pull($key);
    }

    /**
     * Checks if a key exists.
     *
     * @param  string|array  $key
     * @return bool
     */
    public function exists(string $key): bool
    {
        return array_key_exists($key,$this->_ITEMS);
    }

    /**
     * Checks if a key is present and not null.
     *
     * @param string $key
     * @return bool
     */
    public function has(string $key):bool
    {
        return $this->exists($key) and !is_null($this->_ITEMS[$key]) and !empty($this->_ITEMS[$key]);
    }



    public function pull(string $key, string $default=null)
    {
        if (!$this->exists($key))
            return $default;
        $value = $this->_ITEMS[$key];
        unset($this->_ITEMS[$key]);
        return $value;
    }

    public function increment($key, $amount = 1)
    {
        $this->put($key, $value = $this->get($key, 0) + $amount);
        return $value;
    }

    public function decrement($key, $amount = 1)
    {
        return $this->increment($key, $amount * -1);
    }

    /* 'name'  OR  ['name', 'status'] */
    public function forget(string|array $keys): void
    {
        if (is_string($keys))
            $keys = [$keys];

        foreach ($keys as $key)
            unset($this->_ITEMS[$key]);
    }

    public function all(): array
    {
        return $this->_ITEMS;
    }


    /**
     * Get the CSRF token value.
     *
     * @return string
     */
    public function token():string
    {
        return $this->get('_token');
    }

    /**
     * Regenerate the CSRF token value.
     *
     * @return void
     * @throws Exception
     */
    public function regenerateToken():void
    {
        $this->put('_token', $this->generateString(40));
    }




    public function gc()
    {
        foreach (glob($this->_StoragePath."*") as $file) {
            /*** if file is old then delete it ***/
            if(time() - filectime($file) > $this->_LifeTimeSeconds){
                unlink($file);
            }
        }
    }


}