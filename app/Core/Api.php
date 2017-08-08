<?php

namespace App\Core;

use App\Interfaces\iStorage;

class Api{

    public $storage;

    public $model;

    public $model_name;

    public function __construct(iStorage $storage,Model $model = null,$model_name = '')
    {
        $this->storage = $storage;
        $this->model = $model;
    }

    /**
     * HTTP Verbs - GET
     */
    public function index(){

    }

    /**
     * HTTP Verbs - POST
     */
    public function create(){

    }

    public function show(){

    }

    /**
     * HTTP Verbs - PUT / PATCH
     */
    public function update(){

    }

    /**
     * HTTP Verbs - DESTROY
     */
    public function delete(){

    }

}