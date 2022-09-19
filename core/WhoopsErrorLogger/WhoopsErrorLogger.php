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



namespace Core\WhoopsErrorLogger;
use Morilog\Jalali\Jalalian;
use Whoops\Handler\PlainTextHandler;

class WhoopsErrorLogger extends \Whoops\Handler\PrettyPageHandler
{
    private bool $_DEBUG = false;

    public function handle()
    {

        $config_arr = include BASE_PATH . '/config/Framework.php';
        if(is_array($config_arr) and isset($config_arr['debug']) and is_bool($config_arr['debug']))
            $this->_DEBUG = $config_arr['debug'];


        if ($this->_DEBUG)
            parent::handle();

        $plainTextHandler = new PlainTextHandler();
        $plainTextHandler->setException($this->getException());
        $plainTextHandler->setInspector($this->getInspector());
        $error = $plainTextHandler->generateResponse();
        $date = Jalalian::forge('now')->format('Y-m-j');
        $error = '['.$date.' '.date('H:i:s').']'."\n".$error."\n".str_repeat('#',120)."\n";
        file_put_contents(BASE_PATH.'/logs/phpFramework-'.$date.'.log', $error,FILE_APPEND);

        $this->remove_old_logs();

        if (!$this->_DEBUG)
        {
            $html = file_get_contents(BASE_PATH.'/core/Support/error_pages/500.html');
            echo $html;
            http_response_code(500);
            exit();
        }


    }



    private function remove_old_logs()
    {
        $files = glob(BASE_PATH.'/logs/*');
        $now   = time();
        foreach ($files as $file) {
            if (is_file($file)) {
                if ($now - filemtime($file) >= 60 * 60 * 24 * 5) { // 5 days
                    unlink($file);
                }
            }
        }
    }

}