<?php

namespace App\Core;

use App\Traits\toJson;

abstract class Model{

    use toJson;

    public $ID;

    public $created_at;

    public $updated_at;

    public $hash; //for security when updating records

    /**
     * @return array
     */
    abstract public function get_validation();

    abstract public function getRequiredFields();

    /**
     * @return string
     */
    abstract public function getModelName();

    public function properties(){
        return array_keys(get_object_vars($this));
    }
}