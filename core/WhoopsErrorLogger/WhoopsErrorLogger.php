<?php
namespace Core\WhoopsErrorLogger;
/* بسم الله الرحمن الرحیم */
/****************************************
 **** Class developers: *****************
 **** Ahmad Khaliq   ********************
 **** Email: Ahmad.Khaliq@yahoo.com *****
 **** 2021  *****************************
 ***************************************/


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
        file_put_contents(BASE_PATH.'/logs/sahand-'.$date.'.log', $error,FILE_APPEND);


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