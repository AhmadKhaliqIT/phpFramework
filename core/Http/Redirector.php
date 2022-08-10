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
        return $this->url($url);
    }

    public function url($url): static
    {
        return new static(header('Location: ' . $url));
    }

    public function withErrors(){

    }

}