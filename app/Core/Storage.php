<?php namespace App\Core;


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


    protected function returnSuccessResponse($data){
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


}