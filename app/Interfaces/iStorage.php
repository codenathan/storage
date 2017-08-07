<?php

namespace App\Interfaces;

use App\Core\Model;

interface iStorage{

    /**
     * @return null | Model
     */
    public function getModel();

    public function setModel(Model $model);

    public function index();

    public function save($data);

    public function find($data);

    public function delete($data);

}