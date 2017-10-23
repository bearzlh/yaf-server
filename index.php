<?php

require_once('./vendor/autoload.php');

define('APPLICATION_PATH', dirname(__FILE__));

$application = new Yaf_Application( APPLICATION_PATH . "/conf/app.ini");

$application->bootstrap()->run();
?>
