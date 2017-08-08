<?php

namespace App;

use App\Core\ApiResponse;
use App\Core\Model;
use App\Interfaces\iStorage;

class Handler{


    private $models_namespace = '\App\Models\\';
    private $interface_namespace = '\App\Interfaces\\';

    public $request;

    /**
     * @var null | \Twig_Environment
     */
    public $template;

    public $model;

    public $method;

    public $views = ['index','create','edit'];

    public $view = 'errors/404';


    const MODEL_NOT_FOUND = 'the data model you are looking for does not exist';

    /**
     * Handler constructor.
     * @param iStorage $storage
     */
    public function __construct()
    {
        $this->request = isset($_GET['request']) ? $_GET['request'] : null;


        if($this->isApiRequest()) {
            $this->mapApiRequest();
        }if(is_null($this->request)){
            $this->view = 'index';
        }else{
            $this->mapRegularRequest();
        }

    }

    private function mapApiRequest(){
        $the_request = $this->returnRequestSplit();

        $model = isset($the_request[1]) && (is_string($the_request[1])) ? ucwords(strtolower($the_request[1])) : null;

        if(!$this->doesModelExist($model)) return;

        $this->initModel($model);

        $method = isset($the_request[2]) && is_string($the_request[2]) ? ucwords(strtolower($the_request[2])) : null;

        if(!$this->doesStorageMethodExist($method)) return;

        $this->method = $method;
    }

    private function mapRegularRequest(){

        $the_request = $this->returnRequestSplit();

        $model = isset($the_request[0]) && (is_string($the_request[0])) ? ucwords(strtolower($the_request[0])) : null;

        if(!$this->doesModelExist($model)) return;

        $this->initModel($model);

        $view = isset($the_request[1]) && (is_string($the_request[1])) ? strtolower($the_request[1]) : null;

        if(!$this->isViewAllowed($view)) return;


        $this->view = strtolower($model).'/'.$view;


    }

    public function handle(){

        if($this->isApiRequest()){
            return $this->handleApiRequest();
        }


        return $this->handleRegularRequest();

    }

    /**
     * @return ApiResponse
     */
    private function handleApiRequest()
    {
        $response = new ApiResponse();

        if ($this->model instanceof Model) {
            $response->status_code = ApiResponse::HTTP_OK;
            $response->error        = [];
            $response->success      = true;
            $response->response     = $this->model;

            //create api bridge to load data

        } else {
            $response->status_code = ApiResponse::HTTP_BAD_REQUEST;
            $response->error[] = self::MODEL_NOT_FOUND;
        }


        return $response;
    }

    private function handleRegularRequest(){
        $loader = new \Twig_Loader_Filesystem(STORE_VIEWS);
        $this->template = new \Twig_Environment($loader, array('cache' => STORE_CACHE,'debug' => STORE_DEBUG));

        return $this->template;
    }


    public function isApiRequest(){
        return substr( $this->request, 0, 4 ) === "api/";
    }


    /**
     * Checks if model exists
     * @param $model string
     * @return bool
     */
    public function doesModelExist($model){

        return class_exists($this->models_namespace.$model);
    }

    private function doesStorageMethodExist($method){
        $storage_interface = $this->interface_namespace.'iStorage';
        $storage_methods = get_class_methods($storage_interface);
        return in_array($method,$storage_methods);
    }

    private function isViewAllowed($view){
        return in_array($view,$this->views);
    }



    /**
     * @param $model
     */
    private function initModel($model)
    {
        $class = $this->models_namespace . $model;
        $this->model = new $class;
    }


    /**
     * @return array
     */
    private function returnRequestSplit()
    {
        $the_request = explode('/', $this->request);
        return $the_request;
    }

    public function loadView(){
        $view = $this->view.'.twig';
        if($view != 'errors/404.twig' && !file_exists(STORE_VIEWS.DS.$view)){
            $view = 'errors/501.twig';
        }
       echo $this->template->render($view);
    }

}