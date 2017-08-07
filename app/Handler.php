<?php

namespace App;

use App\Core\ApiResponse;
use App\Core\Model;
use App\Interfaces\iStorage;

class Handler{


    private $models_namespace = '\App\Models\\';
    private $interface_namespace = '\App\Interfaces\\';

    public $request;

    public $storage;

    public $template;

    public $model;

    public $method;

    public $views = ['index','create','edit'];

    public $view = null;


    const MODEL_NOT_FOUND = 'the data model you are looking for does not exist';

    /**
     * Handler constructor.
     * @param iStorage $storage
     */
    public function __construct(iStorage $storage)
    {
        $this->request = $_GET['request'];
        $this->storage = $storage;

        if($this->isApiRequest()) {
            $this->mapApiRequest();
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


    }

    public function handle(){

        if($this->isApiRequest()){
            return $this->handleApiRequest();
        }

        return $this->handleRegularRequest();

    }


    /**
     * Checks if model exists
     * @param $model string
     * @return bool
     */
    public function doesModelExist($model){

       return class_exists($this->models_namespace.$model);
    }


    public function isApiRequest(){
        return substr( $this->request, 0, 4 ) === "api/";
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

        } else {
            $response->status_code = ApiResponse::HTTP_BAD_REQUEST;
            $response->error[] = self::MODEL_NOT_FOUND;
        }


        return $response;
    }

    private function handleRegularRequest(){
        $loader = new \Twig_Loader_Filesystem(STORE_VIEWS);
        $this->template = new \Twig_Environment($loader, array('cache' => STORE_CACHE,'debug' => STORE_DEBUG));
        if($this->model instanceof Model && $this->template){
            var_dump($this->model);
            exit();
          $this->template->load();

        }



        $this->template->load('errors/404.twig');



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
     * @return array
     */
    private function returnRequestSplit()
    {
        $the_request = explode('/', $this->request);
        return $the_request;
    }

    /**
     * @param $model
     */
    private function initModel($model)
    {
        $class = $this->models_namespace . $model;
        $this->model = new $class;
    }

}