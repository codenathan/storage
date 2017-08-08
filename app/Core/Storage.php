<?php namespace App\Core;


abstract class Storage{

    /**
     * @var Model
     */
    public $model;

    public function __construct(Model $model)
    {
        $this->model;
    }

}