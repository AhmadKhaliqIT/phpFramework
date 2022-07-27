<?php
namespace Core\Auth;
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

class Guard{

    private object $Logged_user_Db_Data;
    public string $_guard;

    public function __construct($guard)
    {
        $this->_guard = $guard;
    }

    public function user(): object
    {
        return (object)['name'=>'user'];
    }

    public function logout()
    {
        Session()->remove('Auth_'.$this->_guard);
    }

    public function loginUsingId()
    {
        //todo
    }

    public function attempt(array $Login_data)
    {
        Session()->put('Auth_'.$this->_guard,$Login_data);
    }

    public function check(): bool
    {
        $auth_sess = Session()->get('Auth_'.$this->_guard,[]);
        return !empty($auth_sess);
    }


}




