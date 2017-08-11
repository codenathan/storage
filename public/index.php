<?php
session_start();

if (empty($_SESSION['token'])) {
    $_SESSION['token'] = bin2hex(random_bytes(32));
}


require __DIR__.'/../vendor/autoload.php';


$root = realpath(__DIR__.'/../');


/*
*
* CONSTANTS;
* */
defined('DS')          or define('DS',DIRECTORY_SEPARATOR);
defined('STORE_DEBUG') or define('STORE_DEBUG', true);
defined('STORE_VIEWS') or define('STORE_VIEWS', $root.DS.'views');

defined('STORE_FOLDER') or define('STORE_FOLDER', $root.DS.'storage'.DS);
defined('STORE_CACHE') or define('STORE_CACHE', $root.DS.'storage'.DS.'cache'.DS);
defined('STORE_DATA') or define('STORE_DATA', $root.DS.'storage'.DS.'data'.DS);


/*
 *
 * DATABASE
 * */
defined('DB_HOST')          or define('DB_HOST','localhost');
defined('DB_PORT')          or define('DB_PORT',3306);
defined('DB_USER')          or define('DB_USER','codenathan');
defined('DB_PASS')          or define('DB_PASS','F3!+6p13%q)w$:t');


if(STORE_DEBUG){
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
}


$app = new \App\Handler();


if($app->isApiRequest()) {

    $response = new \App\Core\ApiResponse();

    if(is_string($app->method) && $app->model instanceof \App\Core\Model){

        if(!$app->method_allowed) $response->applyInvalidMethod();
        if($app->request_method != 'get' &&  $app->verified_token == false) $response->applyInvalidToken();

        $storage = new \App\Services\FileStorage($app->model);
        //$storage = new \App\Services\DatabaseStorage($app->model);

        $return = (new \App\Core\Api($storage))->{$app->method}(); // This response should always be an instance of ApiResponse

        if($return instanceof \App\Core\ApiResponse) $response = $return;
    }


    $response->printOutput();
}


if($app->template instanceof Twig_Environment) $app->loadView();