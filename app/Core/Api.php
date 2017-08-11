<?php

namespace App\Core;

use App\Interfaces\iStorage;

class Api{

    public $storage;

    /**
     * @var Model
     */
    public $model;

    public $model_name;

    public $validation_errors = array();

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
        if(!$this->validate(true)){
            return $this->returnNonValidatedResponse();
        }
       return $this->storage->save(true);
    }

    public function show(){
        return $this->storage->find();
    }

    /**
     * HTTP Verbs - PUT / PATCH
     */
    public function update(){
        if(!$this->validate()){
            return $this->returnNonValidatedResponse();
        }
        return $this->storage->save();
    }

    /**
     * HTTP Verbs - DESTROY
     */
    public function delete(){
        return $this->storage->delete();
    }

    private function validate($create = false){

        foreach($this->model->properties() as $property){
            //TODO : Implement validation for models here
        }

        return true;

    }

    private function returnNonValidatedResponse(){
        return new ApiResponse(false,null,ApiResponse::HTTP_BAD_REQUEST,$this->validation_errors);
    }

}