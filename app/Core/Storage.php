<?php namespace App\Core;

use Carbon\Carbon;

abstract class Storage{

    public $data =  array();

    /**
     * @var Model
     */
    public $model;

    public $properties = array();

    public $model_name;

    private $models_namespace = '\App\Models\\';

    public function __construct(Model $model)
    {
        $this->model = $model;
        $this->properties = $this->model->properties();
        $this->model_name = $this->model->getModelName();
        return $this;
    }


    protected function returnSuccessResponse($data = null){
        if(is_null($data)) $data = [];
        if(!is_array($data)) $data = [$data];
        return (new ApiResponse(true,$data,ApiResponse::HTTP_OK));
    }

    protected function returnNotFoundResponse(array $data = null){
        return (new ApiResponse(false,$data,ApiResponse::HTTP_NOT_FOUND));
    }
    /**
     * @return Model
     */
    public function getInstanceOfNewModel(){

        $model =  $this->models_namespace.ucwords(strtolower($this->model_name));

        return new $model;
    }

    public function returnUnauthorizedDelete(){
        $delete = ['auth' => 'This is an authorized delete '];
        return (new ApiResponse(false,null,ApiResponse::UNAUTHORIZED,$delete));
    }


    protected static function  sendConnectionErrorResponse($error = null){
        if(is_null($error)) $error = [];
        if(!is_array($error)) $error = [$error];
        return new ApiResponse(false,null,ApiResponse::SERVICE_UNAVAILABLE,$error);
    }

    protected function returnErrorResponse($error = null){
        if(is_null($error)) $error = [];
        if(!is_array($error)) $error = [$error];

        return (new ApiResponse(false,null,ApiResponse::HTTP_BAD_REQUEST,$error));
    }


    public function setCreateFields(){
        $now = Carbon::now()->toDateTimeString();
        $this->model->created_at    = $now;
        $this->model->updated_at    = $now;
        $this->model->hash          = bin2hex(random_bytes(32));

    }

    public function setUpdateFields(){
        $now = Carbon::now()->toDateTimeString();
        $this->model->updated_at    = $now;
        $this->model->hash          = bin2hex(random_bytes(32));
    }

}