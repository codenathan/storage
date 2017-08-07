<?php

namespace App;


use App\Core\Api;
use App\Core\ApiResponse;
use App\Core\Model;
use App\Interfaces\iStorage;

class Handler{


    private $models_namespace = '\App\Models\\';

    public $request;

    public $storage;

    public $template;

    public $model;

    const MODEL_NOT_FOUND = 'the data model you are looking for does not exist';

    /**
     * Handler constructor.
     * @param iStorage $storage
     */
    public function __construct(iStorage $storage)
    {
        $this->request = $_GET['request'];
        $this->storage = $storage;

        $this->mapRequest();

    }

    private function mapRequest(){
        $the_request = explode('/', $this->request);

        $model = isset($the_request[1]) && (is_string($the_request[1])) ? ucwords(strtolower($the_request[1])) : null;

        if($this->doesModelExist($model)){
            $class = $this->models_namespace.$model;
            $this->model = new $class;
        }
    }

    public function handle(){

        if($this->isApiRequest()){
            return $this->handleApiRequest();
        }

        $loader = new \Twig_Loader_Filesystem(STORE_VIEWS);
        $this->template = new \Twig_Environment($loader, array('cache' => STORE_CACHE,'debug' => STORE_DEBUG));

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
            $response->success      = $this->model;

        } else {
            $response->status_code = ApiResponse::HTTP_BAD_REQUEST;
            $response->error[] = self::MODEL_NOT_FOUND;
        }


        return $response;
    }

}