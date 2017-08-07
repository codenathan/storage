<?php

namespace App;


use App\Core\Api;
use App\Core\ApiResponse;
use App\Interfaces\iStorage;

class Handler{


    private $models_namespace = '\App\Models\\';

    public $request;

    public $storage;

    const MODEL_NOT_FOUND = 'the data model you are looking for does not exist';

    public function __construct(iStorage $storage)
    {
        $this->request = $_GET['request'];
        $this->storage = $storage;

    }

    public function handle(){


        if($this->isApiRequest()){
            $response = new ApiResponse();

            $api_request = explode('/',$this->request);

            $model = isset($api_request[1]) && (is_string($api_request[1])) ? ucwords(strtolower($api_request[1])) : null;

            if($this->doesModelExist($model)){


            }else{
                $response->status_code = ApiResponse::HTTP_BAD_REQUEST;
                $response->error[]     = self::MODEL_NOT_FOUND;
            }


            return $response;

        }else{
            'not api request';
        }

        return ;
    }


    /**
     * Checks if model exists
     * @param null $model
     * @return bool
     */
    public function doesModelExist($model = null){

       return class_exists($this->models_namespace.$model);
    }


    public function isApiRequest(){
        return substr( $this->request, 0, 4 ) === "api/";
    }

}