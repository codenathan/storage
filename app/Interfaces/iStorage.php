<?php

namespace App\Interfaces;

interface iStorage{

    public function index();

    public function save();

    public function find();

    public function delete();

}