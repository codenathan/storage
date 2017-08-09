<?php

namespace App\Core;

use App\Interfaces\iStorage;

class Api{

    //TODO : get request headers for post

    public $storage;

    public $model;

    public $model_name;

    public function __construct(iStorage $storage)
    {
        $this->storage = $storage;
        $this->model = $storage->model;

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
        $this->validate([]);
    }

    public function show(){
        return $this->storage->find();
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

        return false;

    }

}