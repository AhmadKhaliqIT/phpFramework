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




namespace Core\Http;
use Core\FileSystem\FileSystem;
use Core\Support\Arr;
use finfo;
use JetBrains\PhpStorm\Pure;
use function count;



class Request
{

    public array $request=[];
    public array $files=[];

    public function __construct()
    {

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $request = array_merge($_GET, $_POST);
        } else {
            $request = $_GET;
        }


        $this->request = $request;
        $this->files = $_FILES;

        foreach ($request as $key => $value) {
            $this->{$key} = $value;
        }

    }

    public function getContent()
    {
        return file_get_contents('php://input');
    }


    public function add($key,$value=null): void
    {
        if(!is_array($key)) {
            $key = [$key => $value];
        }


        foreach ($key as $arraykey => $arrayvalue) {
            $this->{$arraykey} = $arrayvalue;
            $this->request[$arraykey]=$arrayvalue;
        }
    }

    public function has($key): bool
    {
        $keys = is_array($key) ? $key : func_get_args();

        $input = $this->all();

        foreach ($keys as $key) {
            if (!array_key_exists($key, $input)) {
                return false;
            }
        }

        return true;
    }

    public function all($keys = null): array
    {
        return array_merge($this->request,$this->files);
    }

    public function isNotEmpty($key): bool
    {
        return $this->request[$key] !== '';
    }

    public function isEmpty($key): bool
    {
        return $this->request[$key] === '';
    }


    public function keys(): array
    {
        return array_keys($this->request);
    }

    public function remove($key): void
    {
        unset($this->request[$key]);
    }

    public function count(): int
    {
        return count($this->request);
    }

    public function hasFile($key): bool
    {
       if (Arr::exists($this->files,$key) and !empty($this->files[$key]['name'])) {
           return true;
       }
        return false;
    }

    public function file($key = null, $default = null)
    {
        return Arr::get($this->files,$key);
    }

    public function input($key = null, $default = null)
    {
        return data_get(
            $this->all(), $key, $default
        );
    }

    public function validate($rules)
    {
        $t = new Validator($this->all(), $rules);
        //dd($t->messages);
    }




}