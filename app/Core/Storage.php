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
    }


    protected function returnSuccessResponse(array $data){
        return (new ApiResponse(true,$data,ApiResponse::HTTP_OK));
    }

    public function getInstanceOfNewModel(){

        $model =  $this->models_namespace.ucwords(strtolower($this->model_name));

        return new $model;
    }


}