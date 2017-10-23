<?php
define('APPLICATION_PATH', realpath(dirname(__FILE__)));

require_once(APPLICATION_PATH . '/vendor/autoload.php');


$application = new \Yaf\Application( APPLICATION_PATH . "/conf/app.ini");

$application->bootstrap()->run();
?>
