<?php
/* بسم الله الرحمن الرحیم */
/****************************************
 **** Class developers: *****************
 **** Ahmad Khaliq   ********************
 **** Email: Ahmad.Khaliq@yahoo.com *****
 **** 2021  *****************************
 ***************************************/


use Core\Blade\Blade;
use Core\Collection\Collection;
use Core\Core;
use Core\Support\Arr;
use JetBrains\PhpStorm\NoReturn;



function include_dir($path)
{
    foreach (glob($path."*.php") as $filename)
    {
        require_once $filename;
    }
}

function base(): string
{
    return BASE_PATH;
}


function public_path($path): string
{
    return PUBLIC_PATH.DIRECTORY_SEPARATOR.$path;
}


function Core(): Core
{
    global $__Core;
    if (!$__Core instanceof Core)
        $__Core = new Core;

    return $__Core;

}

function app($className)
{
    if (!class_exists($className))
        throw new InvalidArgumentException($className . ' does not exist.');

    return new $className;
}

function parseCallback(string $callback): array
{
    if(!str_contains($callback,'@'))
        throw new InvalidArgumentException('Irregular value passed to parseCallback()');

    $keys = explode('@',$callback);
    if(count($keys) !== 2)
        throw new InvalidArgumentException('Irregular value passed to parseCallback()');

    $Class = $keys[0];
    $method = $keys[1];
    return [$Class,$method];
}


function callMethod(string $callback,array $params=[],bool $callMiddleWares = false)
{

    list($Class,$method) = parseCallback($callback);
    $theInstance = new $Class;
    if (!method_exists($theInstance,$method))
        throw new InvalidArgumentException('Method ' . $method . ' does not exist.');

    if ($callMiddleWares)
    {
        $MiddleWares = include base().'/app/kernel.php';
        foreach ($MiddleWares as $MiddleWare)
        {
            $theInstance->middleware($MiddleWare);
        }
    }

    return call_user_func_array([$theInstance,$method],$params);
}


function config($key,$default=null)
{
    if(!str_contains($key,'.'))
        if (is_null($default))
            throw new InvalidArgumentException('Irregular value passed to config()');
        else
            return $default;

    $keys = explode('.',$key);
    if(count($keys) !== 2)
        if (is_null($default))
            throw new InvalidArgumentException('Irregular value passed to config()');
        else
            return $default;

    if(!file_exists(BASE_PATH . '/config/'.$keys[0].'.php'))
        if (is_null($default))
            throw new InvalidArgumentException($keys[0].' file not found.');
        else
            return $default;

    $config_arr = include BASE_PATH . '/config/'.$keys[0].'.php';

    if(!array_key_exists($keys[1],$config_arr))
        if (is_null($default))
            throw new InvalidArgumentException($keys[1].' option not found.');
        else
            return $default;

    return $config_arr[$keys[1]];
}




function collect($value = null)
{
   // return Core()->Collection($value);
    return new Collection($value);
}

function value($value)
{
    return $value instanceof Closure ? $value() : $value;
}

function dd($variable,$die=true){
    $contents = print_r($variable,1);
    echo '<pre>' . htmlspecialchars($contents) . '</pre><br><br>';
    if ($die)
        die();
}

function e($value, $doubleEncode = false): string
{

    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8', $doubleEncode);
}


#[NoReturn] function abort($code, array $headers = [])
{
    http_response_code($code);

    foreach ($headers as $header)
    {
        header($header);
    }
    exit;
}

function auth($guard = null)
{
    if (is_null($guard)) {
        return app(AuthFactory::class);
    }

    return app(AuthFactory::class)->guard($guard);
}





function redirect($to = null, $status = 302, $headers = [], $secure = null)
{

    if (is_null($to)) {
        return Core()->Redirector();
    }

    return app('redirect')->to($to, $status, $headers, $secure);
}



function request($key = null, $default = null)
{
    if (is_null($key)) {
        return app('request');
    }

    if (is_array($key)) {
        return app('request')->only($key);
    }

    $value = app('request')->__get($key);

    return is_null($value) ? value($default) : $value;
}


function response($content = '', $status = 200, array $headers = [])
{


    return new \Core\Http\Response();
}


function route($name, $parameters = [])
{
    return Core()->RouterBase()->getRouteURI($name,$parameters);
}


function cookie($name = null, $value = null, $minutes = 0, $path = null, $domain = null, $secure = false, $httpOnly = true)
{
    $cookie =  Core()->Cookie();

    if (is_null($name)) {
        return $cookie;
    }

    if (!is_null($value))
        return Core()->Cookie()->set($name, $value, $minutes, $path, $domain, $secure, $httpOnly);
    else
        return Core()->Cookie()->get($name);
}

function session($key = null, $default = null)
{
    if (is_null($key)) {
        return  Core()->Session();
    }

    if (is_array($key)) {
        return Core()->Session()->put($key);
    }

    return Core()->Session()->get($key, $default);
}

function csrf_token()
{
    return  Core()->Session()->token();
}


function csrf_field(): string
{
    return '<input type="hidden" name="_token" value="'.csrf_token().'">';
}



function validator(array $data = [], array $rules = [], array $messages = [], array $customAttributes = [])
{

    $factory = app(ValidationFactory::class);

    if (func_num_args() === 0) {
        return $factory;
    }

    return $factory->make($data, $rules, $messages, $customAttributes);
}

function view($view = null, $data = [])
{
    return core()->View()->view($view,$data);
}

/**
 * Call the given Closure with the given value then return the value.
 *
 * @param  mixed  $value
 * @param callable|null $callback
 * @return mixed
 */
function tap(mixed $value, ?callable $callback): mixed
{
    $callback($value);
    return $value;
}

/**
 * Get an item from an array or object using "dot" notation.
 *
 * @param  mixed   $target
 * @param array|string $key
 * @param mixed|null $default
 * @return mixed
 */
function data_get(mixed $target, array|string $key, mixed $default = null): mixed
{
    if (is_null($key)) {
        return $target;
    }

    $key = is_array($key) ? $key : explode('.', $key);

    while (! is_null($segment = array_shift($key))) {
        if ($segment === '*') {
            if ($target instanceof Collection) {
                $target = $target->all();
            } elseif (! is_array($target)) {
                return value($default);
            }

            $result = Arr::pluck($target, $key);

            return in_array('*', $key) ? Arr::collapse($result) : $result;
        }

        if (Arr::accessible($target) && Arr::exists($target, $segment)) {
            $target = $target[$segment];
        } elseif (is_object($target) && isset($target->{$segment})) {
            $target = $target->{$segment};
        } else {
            return value($default);
        }
    }

    return $target;
}