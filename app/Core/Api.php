<?php

namespace App\Core;

use App\Interfaces\iStorage;

class Api{

    public $storage;

    /**
     * @var Model
     */
    public $model;

    public $model_name;

    public $validation_errors = array();

    public function __construct(iStorage $storage)
    {
        $this->storage = $storage;
        $this->model = $storage->model;

        return $this;
    }

    /**
     * HTTP Verbs - GET
     */
    public function index(){
       return $this->storage->index();
    }

    /**
     * HTTP Verbs - POST
     */
    public function create(){
        if(!$this->validate(true)){
            return $this->returnNonValidatedResponse();
        }
       return $this->storage->save(true);
    }

    public function show(){
        return $this->storage->find();
    }

    /**
     * HTTP Verbs - PUT / PATCH
     */
    public function update(){
        if(!$this->validate()){
            return $this->returnNonValidatedResponse();
        }
        return $this->storage->save();
    }

    /**
     * HTTP Verbs - DESTROY
     */
    public function delete(){
        return $this->storage->delete();
    }

    private function validate($create = false){

        $validation_rules = $this->model->get_validation();

        $required_list = $this->model->getRequiredFields();

        if(!$create) $required_list[] = 'ID';


        foreach($this->model->properties() as $property){

            if(!isset($validation_rules[$property])) continue;

            $property_value = $this->model->{$property};

            $rule = $validation_rules[$property];

            if(in_array($property,$required_list)){
                if(empty($property_value)|| is_null($property_value)) $this->registerValidationError($property,'required');
            }

            if( ( empty($property_value)|| is_null($property_value) ) && (!in_array($property_value,$required_list)) ) continue;

            if(isset($rule['regex'])){
                $match = preg_match($rule['regex'], $this->model->$property);

                if(!$match) $this->registerValidationError($property,'regex');
            }

            if(isset($rule['conditional_value'])){
                if(!in_array($property_value,$rule['conditional_value'])) $this->registerValidationError($property,'conditional_value');
            }

            if(isset($rule['conditional_map']) && isset($rule['conditional_model'])){
                $conditional_model_data  = $this->model->{$rule['conditional_model']};
                $conditional_map    = $rule['conditional_map'];

                if(array_key_exists($conditional_model_data,$conditional_map)){
                    if(!in_array($property_value,$conditional_map[$conditional_model_data])) $this->registerValidationError($property,'conditional_map',$rule['conditional_model']);


                }
            }



        }



        return count($this->validation_errors) == 0;

    }

    private function registerValidationError($property, $reason,$model = null){

        $reason_meaning = [
            'regex'             => 'This data is incorrectly formatted',
            'conditional_value' => 'This value is incorrect',
            'conditional_map'   => 'This is not data is not consistance with '.$model.'please correct this',
            'required'          => 'This field is required',
        ];

        if(!isset($this->validation_errors[$property])) $this->validation_errors[$property] = [];

        $this->validation_errors[$property][$reason] = $reason_meaning[$reason];
    }

    private function returnNonValidatedResponse(){

        return new ApiResponse(false,null,ApiResponse::HTTP_BAD_REQUEST,$this->validation_errors);
    }

}