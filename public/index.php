<?php

require __DIR__.'/../vendor/autoload.php';


$root = realpath(__DIR__.'/../');


/*
*
* CONSTANTS;
* */
defined('DS')          or define('DS',DIRECTORY_SEPARATOR);
defined('STORE_DEBUG') or define('STORE_DEBUG', true);
defined('STORE_VIEWS') or define('STORE_VIEWS', $root.DS.'views');
defined('STORE_CACHE') or define('STORE_CACHE', $root.DS.'storage'.DS.'cache');


if(STORE_DEBUG){
    ini_set('display_startup_errors',1);
    ini_set('display_errors',1);
    error_reporting(-1);
}

//$storage = new \App\Services\DatabaseStorage();
$storage = new \App\Services\FileStorage();

$app = (new \App\Handler($storage))->handle();

if($app instanceof \App\Core\ApiResponse){
   return $app->output();
}




