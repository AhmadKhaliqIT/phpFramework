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
namespace Core;

use InvalidArgumentException;

/**
 * @method RouterBase()
 * @method Collection()
 * @method Crypt()
 * @method Cookie()
 * @method Session()
 * @method FileSystem()
 * @method Arr()
 * @method Redirector()
 * @method Request()
 * @method View()
 */
class Core
{
    protected array $_Core = [
        'RouterBase' => 'Core\Router\RouterBase',
        'Collection' => 'Core\Collection\Collection',
        'Crypt' => 'Core\Crypt\Crypt',
        'Cookie' => 'Core\Browser\Cookie',
        'Session' => 'Core\Browser\Session',
        'FileSystem' => 'Core\FileSystem\FileSystem',
        'Arr' => 'Core\Support\Arr',
        'Redirector' => 'Core\Http\Redirector',
        'Request' => 'Core\Http\Request',
        'View' => 'Core\Blade\View',
    ];
    //protected ?Core $_instance = null;

    public function __construct($name = null)
    {
//        foreach ($this->_Core as $key => $class) {
//            $this->_Core[$key] = (object)[
//                'object' =>  new $class,
//                'namespace' => $class
//            ];
//        }
//        if (!is_null($name))
//            return $this->_Core[$name];

        if (!is_null($name))
            return $this->getInstanceOf($name);

    }


    private function load_class(string $name)
    {
        if (is_object($this->_Core[$name]))
            return;

        $class = $this->_Core[$name];
        $this->_Core[$name] = (object)[
            'object'    => new $class,
            'namespace' => $class
        ];
    }


    public function getInstanceOf($class){
        if (is_string($this->_Core[$class]))
            $this->load_class($class);

        return $this->_Core[$class]->object;
    }

    public function exist($namespace,$return_name = false):bool|string
    {
        foreach ($this->_Core as $key => $object)
        {
            if(is_object($object) and $object->namespace == $namespace)
                return ($return_name)?$key:true;
        }
        return false;
    }


    public function __call($method, $args)
    {

        if (!array_key_exists($method, $this->_Core)) {
            throw new InvalidArgumentException( $method . ' does not exist.');
        }
//        return call_user_func_array(
//            array($this->_instance, $method),
//            $args
//        );

        return $this->getInstanceOf($method);

    }

}