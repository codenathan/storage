<?php

interface iStorage{

    public function save($data);

    public function find($data);

    public function delete($data);

}