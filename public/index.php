<?php
/* بسم الله الرحمن الرحیم */
/****************************************
 **** Sahand Framework ******************
 **** Core developers: ******************
 **** Ahmad Khaliq - Mojtaba Zadegi *****
 **** Email: Ahmad.Khaliq@yahoo.com *****
 **** Email: mojtaba.zadehgi@gmail.com **
 **** 2021  *****************************
 ***************************************/

define("SAHAND_START", microtime(true));


define('BASE_PATH',dirname(__DIR__, 1).'/Sahand');
const PUBLIC_PATH = __DIR__;
require_once BASE_PATH.'/server.php';

//echo (microtime(true) - SAHAND_START);


