<?php

namespace App\Core;

use App\Interfaces\iStorage;

class Api{

    //TODO : get request headers for post

    public $storage;

    public $model;

    public $model_name;

    public function __construct(iStorage $storage,Model $model = null,$model_name = null)
    {
        $this->storage = $storage;
        $this->model = $model;
        $this->model_name = $model_name;

        return $this;
    }

    /**
     * HTTP Verbs - GET
     */
    public function index(){
       return $this->storage->index();
    }

    /**
     * HTTP Verbs - POST
     */
    public function create(){
        $this->validate();
    }

    public function show($id){
        $this->storage->find($id);
    }

    /**
     * HTTP Verbs - PUT / PATCH
     */
    public function update(){
      //  $this->validate();
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