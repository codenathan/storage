<?php

namespace App\Core;

use App\Traits\toJson;

abstract class Model{

    use toJson;

    abstract public function get_validation();

    public function properties(){
        return get_object_vars($this);
    }
}