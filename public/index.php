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
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
}

//$storage = new \App\Services\DatabaseStorage();
$storage = new \App\Services\FileStorage();

$app = new \App\Handler();

$response = $app->handle();


if($response instanceof \App\Core\ApiResponse) $response->printOutput();

if($app->template instanceof Twig_Environment) $app->loadView();