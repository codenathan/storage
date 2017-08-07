<?php namespace App\Core;


abstract class Storage{

    public $model;

    /**
     * @return null | Model
     */
    public function getModel()
    {
        // TODO: Implement getModel() method.
    }

    public function setModel(Model $model)
    {
        $this->model = $model;
    }

}