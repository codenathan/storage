<?php

namespace App\Core;

use App\Traits\toJson;

abstract class Model{

    use toJson;

    abstract protected function get_validation();

    public function properties(){
        return get_object_vars($this);
    }
}