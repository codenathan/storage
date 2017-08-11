<?php

namespace App\Interfaces;

interface iStorage{

    public function index();

    public function save($create = false);

    public function find();

    public function delete();

    public function setCreateFields();

    public function setUpdateFields();

    public function getAllData();

    public function runCreateCalcFunctions();

}