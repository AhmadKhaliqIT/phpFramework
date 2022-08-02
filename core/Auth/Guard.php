<?php
namespace Core\Auth;
/* بسم الله الرحمن الرحیم */

use Core\Crypt\Crypt;
use Core\Database\DB;
use Exception;

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

    private object $Logged_user_DB_Data;
    public string $_guard;
    public string $_table;

    public function __construct($guardName,$guardTable)
    {
        $this->_guard = $guardName;
        $this->_table = $guardTable;
    }


    public function user(): object
    {
        $user_id = Session()->get('Auth_'.$this->_guard,0);
        if (empty($Logged_user_DB_Data))
            try {
                $Logged_user_DB_Data = DB::table($this->_table)->whereId($user_id)->first();
            } catch (Exception $e) {
                die('Error: Auth Could not fetch user from database');
            }
        return $Logged_user_DB_Data;
    }

    public function logout()
    {
        Session()->remove('Auth_'.$this->_guard);
    }

    public function loginUsingId()
    {
        //todo
    }

    /**
     * @throws Exception
     */
    public function attempt(array $loginData): bool
    {
        $password = $loginData['password'];
        unset($loginData['password']);
        $DB_Data = DB::table($this->_table)->where($loginData)->first();
        if (isset($DB_Data->id))
        {
            if (Crypt::verify($password,$DB_Data->password))
            {
                Session()->put('Auth_'.$this->_guard,$DB_Data->id);
                return true;
            }
        }

        return false;
    }

    public function check(): bool
    {
        $auth_sess = Session()->get('Auth_'.$this->_guard,0);
        return ($auth_sess>0);
    }


}




