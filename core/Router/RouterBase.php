<?php
namespace Core\Router;
/* بسم الله الرحمن الرحیم */
/****************************************
 **** Class developers: *****************
 **** Ahmad Khaliq   ********************
 **** Email: Ahmad.Khaliq@yahoo.com *****
 **** 2021  *****************************
 ***************************************/

use Core\Core;
use Core\Blade\View;
use Core\Http\Response;
use Exception;
use InvalidArgumentException;
use ReflectionMethod;
use function Symfony\Component\Translation\t;


class RouterBase{
    private int $_id;


    public function getRouteURI(string $name,array $params=[]):string
    {
        foreach (RouterStorage::$_Storage as $id=>$route) {
            if ($route['name'] == $name) {
                if (!$this->hasBrackets($route['uri']))
                    return $route['uri'];
                foreach ($params as $param=>$value)
                {
                    $route['uri'] = str_replace('{'.$param.'}',$value,$route['uri']);
                }
                return $route['uri'];
            }
        }
        throw new InvalidArgumentException('Route '.$name . ' does not exist.');
    }

    /**
     * @throws Exception
     */
    public function run()
    {
        $basepath = '/';
        // The basepath never needs a trailing slash
        // Because the trailing slash will be added using the route uris
        $basepath = rtrim($basepath, '/');

        $parsed_url = parse_url($_SERVER['REQUEST_URI']);


        $path = '/';

        // If there is a path available
        if (isset($parsed_url['path'])) {
            $path = rtrim($parsed_url['path'], '/');
        }

        $path = urldecode($path);

        $method = $_SERVER['REQUEST_METHOD'];

        //print_r(RouterStorage::$_Storage);

        $route_match_found = false;


        foreach (RouterStorage::$_Storage as $id=>$route) {
            if (!$this->hasBrackets($route['uri']))
            {
                if ($route['uri'] == $path)
                {
                    $route_match_found = true;
                    $this->call_route_function($id);
                    break;
                }
                continue;
            }


            list($route['uri'],$RouteVariables) = $this->make_brackets_searchable($route['uri']);



            // Check path match
            if (preg_match('#'.$route['uri'].'#u', $path, $matches)) {
                //$path_match_found = true;
                // Cast allowed method to array if it's not one already, then run through all methods
                foreach ((array)$route['method'] as $allowedMethod) {
                    // Check method match
                    if (strtolower($method) === strtolower($allowedMethod)) {
                        array_shift($matches); // Always remove first element. This contains the whole string

                        if ($basepath !== '' && $basepath !== '/') {
                            array_shift($matches); // Remove basepath
                        }

                        $parameters = [];
                        for ($i=0, $iMax = count($RouteVariables); $i< $iMax; $i++)
                        {
                            $parameters[$RouteVariables[$i]] = $matches[$i];
                        }
                        $this->call_route_function($id,$parameters);


                        $route_match_found = true;

                        // Do not check other routes
                        break;
                    }
                }
            }

            // Break the loop if the first found route is a match
            if($route_match_found) {
                break;
            }

        }


        if(!$route_match_found)
            $this->error_page(404);


    }

    private function hasBrackets($uri)
    {
        if (str_contains($uri,'{') and str_contains($uri,'}')) {
            return true;
        }

        return false;
    }

    private function error_page(int $error_number)
    {
        die('error:'.$error_number);
    }


    /**
     * @throws \ReflectionException
     */
    protected static function fromClassMethodString($uses): array
    {
        list($class, $method) = parseCallback($uses);

        if (! method_exists($class, $method) && is_callable($class, $method)) {
            return [];
        }

        return (new ReflectionMethod($class, $method))->getParameters();
    }


    /**
     * @throws Exception
     */
    private function call_route_function(int $id, array $params=[]):void
    {
        //dd($params);

        Core()->Request()->add($params);

        array_unshift($params,Core()->Request());
        if ($id < 0 or !array_key_exists($id,RouterStorage::$_Storage))
            throw new Exception('Route does not exist');


        $route = RouterStorage::$_Storage[$id];
        if ($route['function'] instanceof \Closure)
        {
            if ($this->hasBrackets($route['uri']) and (!is_array($params) or count($params)<=0) or !isset($params[0]) or empty($params[0]))
                throw new InvalidArgumentException('No value passed to method.');

            if($return_value = call_user_func_array($route['function'], $params)) {
                echo $return_value;
            }
        }
        else
        {

            $route['function'] = 'App\Controllers\\'.$route['function'];

            $readyToPassParameters = [];
            $definedMethodParameters = $this->fromClassMethodString($route['function']);
            foreach ($definedMethodParameters as $param) {
                //$param is an instance of ReflectionParameter
                $argName = $param->getName();
                $ClassName = $param->getClass();
                if(!is_null($ClassName))
                {
                    $CoreClassName = Core()->exist($ClassName->name,true);
                    if($CoreClassName !== false)
                    {
                        $readyToPassParameters[$argName] = Core()->getInstanceOf($CoreClassName);
                        continue;
                    }
                }

                if(array_key_exists($argName,$params))
                    $readyToPassParameters[$argName] = $params[$argName];
            }
            $response = callMethod($route['function'],$readyToPassParameters,true);

            if ($response instanceof View){
                echo $response->render();
            }
            else if ($response instanceof Response){
                echo $response->render();
            }
            else
            {
                echo $response;
            }
        }
    }

    private function make_brackets_searchable(string $uri): array
    {
        if (!$this->hasBrackets($uri))
            return [$uri,[]];

        preg_match_all('/{(.*?)}/', $uri, $matches);
        $items = $matches[0];
        if (count($items) <= 0)
            return [$uri,[]];

        foreach ($items as $key=>$value)
        {
            $uri = str_replace($value,'XsahandVarX',$uri);
            $items[$key] = str_replace(['{','}'],'',$value);
        }


        $uri = preg_quote($uri);
        return [str_replace('XsahandVarX','(.*)',$uri),(array)$items];
    }


    public function reserve($uri,$function,$method='GET')
    {
        $this->createThisId();
        $this->addRoute([
            'uri' => $uri,
            'function' => $function,
            'method' => $method,
            'name'  => ''
        ]);
    }


    public function name($name){
        global $_Routes;
        RouterStorage::$_Storage[$this->_id]['name'] = $name;
    }


    private function addRoute($route)
    {
        global $_Routes;
        RouterStorage::$_Storage[$this->_id] = $route;
    }

    private function createThisId()
    {
        $last_id = (int) array_key_last(RouterStorage::$_Storage);
        $this->_id = $last_id+1;
        RouterStorage::$_Storage[$this->_id] = [];
    }

    public function allRoutes(): array
    {
        return RouterStorage::$_Storage;
    }


}


