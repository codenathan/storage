<?php

namespace App\Interfaces;

interface iStorage{

    public function index($model_name);

    public function save($data);

    public function find($data);

    public function delete($data);

}