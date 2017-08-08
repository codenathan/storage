<?php

namespace App\Core;

use App\Traits\toJson;

abstract class Model{

    use toJson;

    public $ID;

    public $created_at;

    abstract public function get_validation();

    /**
     * @return string
     */
    abstract public function getModelName();

    public function properties(){
        return get_object_vars($this);
    }
}