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




$app = new \App\Handler();


if($app->isApiRequest()) {

    $response = new \App\Core\ApiResponse();

    if(is_string($app->method) && $app->model instanceof \App\Core\Model){


        $storage = new \App\Services\FileStorage($app->model);
        //$storage = new \App\Services\DatabaseStorage($app->model);

        $api = new \App\Core\Api($storage);
        $response = $api->{$app->method}(); //Response will always be an instance of ApiResponse
    }

    $response->printOutput();
}

if($app->template instanceof Twig_Environment) $app->loadView();