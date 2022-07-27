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
    public function handle()
    {

        parent::handle();
        $plainTextHandler = new PlainTextHandler();
        $plainTextHandler->setException($this->getException());
        $plainTextHandler->setInspector($this->getInspector());
        $error = $plainTextHandler->generateResponse();
        $date = Jalalian::forge('now')->format('Y-m-j');
        $error = '['.$date.' '.date('H:i:s').']'."\n".$error."\n".str_repeat('#',120)."\n";
        file_put_contents(BASE_PATH.'/logs/phpFramework-'.$date.'.log', $error,FILE_APPEND);


        $this->remove_old_logs();


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