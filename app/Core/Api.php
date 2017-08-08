<?php

namespace App\Core;

use App\Interfaces\iStorage;

class Api{

    public $storage;

    public $model;

    public $model_name;

    public function __construct(iStorage $storage,Model $model = null,$model_name = null)
    {
        $this->storage = $storage;
        $this->model = $model;
        $this->model_name = $model_name;
    }

    /**
     * HTTP Verbs - GET
     */
    public function index(){
        $this->storage->index($this->model_name);
    }

    /**
     * HTTP Verbs - POST
     */
    public function create(){
        $this->validate($this->model->get_validation());
    }

    public function show(){
        $this->storage->find($this->model->ID);
    }

    /**
     * HTTP Verbs - PUT / PATCH
     */
    public function update(){
        $this->validate($this->model->get_validation());
    }

    /**
     * HTTP Verbs - DESTROY
     */
    public function delete(){

    }

    private function validate(array $validations){

        foreach($this->model->properties() as $property){
            //TODO : Implement validation for models here
        }

    }

}