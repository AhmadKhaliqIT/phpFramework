<?php
/****************************************
 **** Class developers: *****************
 **** Mojtaba Zadehgi - *****************
 **** Email: Mojtaba.Zadehgi@yahoo.com **
 **** 2021  *****************************
 ***************************************/

namespace Core\Http;
use ArrayObject;
use Core\Support\Contract\Arrayable;
use Core\Support\Contract\Jsonable;
use JsonSerializable;

class Response{

    public $content;

    public function __construct($content = '', $status = 200, $headers = [])
    {
        header_remove('X-Powered-By');
        $this->setStatusCode($status);
        $this->setContent($content);


    }


     public function json($content = [], $statuscode=200): bool|string
     {
         $this->setStatusCode($statuscode);

         if ($content instanceof Jsonable) {
             return $content->toJson();
         } elseif ($content instanceof Arrayable) {
             return json_encode($content->toArray());
         }

         return json_encode($content);
     }


     public function setContent($content)
    {
        if ($this->should_be_json($content)){
            $this->header('Content-Type', 'application/json; charset=utf-8');
            return $this->json($content);
        }

      $this->content= $content;
      return $this;

    }


    public function header(string $header, string $replace )
    {
        header($header .":". $replace);
        return $this;
    }

    public function setStatusCode($statuscode=200)
    {
        http_response_code($statuscode);
    }

    protected function should_be_json($content): bool
    {
        return $content instanceof Arrayable ||
            $content instanceof Jsonable ||
            $content instanceof ArrayObject ||
            $content instanceof JsonSerializable ||
            is_array($content);
    }

    public function render()
    {
        echo $this->content;
        die();
    }


}