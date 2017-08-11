<?php

namespace App\Interfaces;

interface iStorage{

    public function index();

    public function save($create = false);

    public function find();

    public function delete();

}