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




define("FRAMEWORK_START", microtime(true));


define('BASE_PATH',dirname(__DIR__, 1).'/Sahand');
const PUBLIC_PATH = __DIR__;
require_once BASE_PATH.'/server.php';


