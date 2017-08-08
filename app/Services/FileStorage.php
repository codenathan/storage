<?php

namespace  App\Services;

use App\Core\Model;
use App\Core\Storage;
use App\Interfaces\iStorage;
use App\Models\User;
use Carbon\Carbon;

class FileStorage extends Storage implements iStorage{


    /**
     * @return \App\Core\ApiResponse
     */
    public function index()
    {
        $files = glob($this->getModelFolder().'*.json');

        foreach($files as $file){
            $read_file = $this->openFile($file);
            $decode_data = (array) json_decode($read_file);
            $this->data[] = $this->mapData($decode_data);
        }

        return $this->returnSuccessResponse($this->data);
    }

    public function save($data)
    {

    }

    public function find($data)
    {
        // TODO: Implement find() method.
    }

    public function delete($data)
    {
        // TODO: Implement delete() method.
    }


    private function getModelFolder(){
        return STORE_DATA.$this->model->getModelName().DS;
    }

    private function saveData(Model $model){
        $json = json_encode($model);
        file_put_contents($this->getModelFolder().$model->ID.'.json',$json);
    }

    private function openFile($file){
        return file_get_contents($file);
    }

    private function mapData($decode_data){

        $model = $this->getInstanceOfNewModel();

        foreach ($this->properties as $property){
            $model->{$property} = isset($decode_data[$property]) ? $decode_data[$property] : null;
        }

        return $model;
    }

    public function testCreate(){
        $user = new User();
        $user->username     = 'shakesnathan';
        $user->ID           = 1;
        $user->title        = 'Mr';
        $user->firstName    = 'Shakes';
        $user->lastName     = 'Nathan';
        $user->gender       = 'M';
        $user->created_at   = Carbon::now()->toDateTimeString();

        $this->saveData($user);
    }



}