<?php
namespace Core\Http;
/****************************************
 **** Class developers: ******************
 **** Mojtaba Zadehgi - ********************
 **** Email: Mojtaba.Zadehgi@yahoo.com *****
 **** 2021  *****************************
 ***************************************/
class Redirector{
    public function back($status = 302)
    {
        http_response_code($status);
        $url= $_SERVER['HTTP_REFERER'] ?? $_SERVER['HTTP_HOST'];
       return new static(header('Location: ' . $url));
    }

    public function with($key, $value = null)
    {
        $key = is_array($key) ? $key : [$key => $value];

        foreach ($key as $k => $v) {
            session()->flash($k, $v);
        }
        return new static();
    }

    public function route($name, $parameters = [])
    {
        $url=route($name,$parameters);
        return new static(header('Location: ' . $url));
    }

    public function withErrors(){

    }

}