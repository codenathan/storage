<?php namespace App\Traits;


trait toJson{

    public function output(){
        header('Content-Type: application/json');
        return json_encode($this);
    }

    public function printOutput(){
        echo $this->output();
    }
}