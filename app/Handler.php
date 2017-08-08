<?php

namespace App;

use App\Core\ApiResponse;
use App\Core\Model;

class Handler{


    private $models_namespace = '\App\Models\\';
    private $interface_namespace = '\App\Interfaces\\';

    /**
     * Contains the request that comes through the query string
     * @var null | string
     */
    public $request;

    /**
     * The twig environment container
     * @var null | \Twig_Environment
     */
    public $template;

    /**
     * Container for the model
     * @var null | Model
     */
    public $model;


    /**
     * Specifies the method name which we need to call
     * @var string
     */
    public $method;


    /**
     * A list of allowed views
     * @var array
     */
    public $views = ['index','create','edit'];


    /**
     * This string holds the view that should be rendered
     * @var string
     */
    public $view = 'errors/404';


    const MODEL_NOT_FOUND = 'the data model you are looking for does not exist';


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

    /**
     * The starting point for handling all requests
     * @return ApiResponse
     */
    public function handle(){

        if($this->isApiRequest()){
            return $this->handleApiRequest();
        }


        $this->handleRegularRequest();

    }

    /**
     * Setting required variables for API Requests
     */
    private function mapApiRequest(){
        $the_request = $this->returnRequestSplit();

        $model = isset($the_request[1]) && (is_string($the_request[1])) ? ucwords(strtolower($the_request[1])) : null;

        if(!$this->doesModelExist($model)) return;

        $this->initModel($model);

        $method = isset($the_request[2]) && is_string($the_request[2]) ? ucwords(strtolower($the_request[2])) : null;

        if(!$this->doesStorageMethodExist($method)) return;

        $this->method = $method;
    }


    /**
     * Setting required variables for regular Requests
     */
    private function mapRegularRequest(){

        $the_request = $this->returnRequestSplit();

        $model = isset($the_request[0]) && (is_string($the_request[0])) ? ucwords(strtolower($the_request[0])) : null;

        if(!$this->doesModelExist($model)) return;

        $this->initModel($model);

        $view = isset($the_request[1]) && (is_string($the_request[1])) ? strtolower($the_request[1]) : null;

        if(!$this->isViewAllowed($view)) return;


        $this->view = strtolower($model).'/'.$view;


    }



    /**
     * Process the API Response Data
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

            //TODO : create api bridge to load data

        } else {
            $response->status_code = ApiResponse::HTTP_BAD_REQUEST;
            $response->error[] = self::MODEL_NOT_FOUND;
        }


        return $response;
    }

    /**
     * Process the regular request template / data
     */
    private function handleRegularRequest(){
        $loader = new \Twig_Loader_Filesystem(STORE_VIEWS);
        $this->template = new \Twig_Environment($loader, array('cache' => STORE_CACHE,'debug' => STORE_DEBUG));

        $this->template;
    }


    public function isApiRequest(){
        return substr( $this->request, 0, 4 ) === "api/";
    }


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