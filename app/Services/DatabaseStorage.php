<?php

namespace  App\Services;

use App\Core\ApiResponse;
use App\Core\Storage;
use App\Interfaces\iStorage;

class DatabaseStorage extends Storage implements iStorage{


    /**
     * @var null | string | \PDO
     */
    public static $instance;

    public $query;

    public $statement;

    public $query_errors = array();


    public static function getInstance(){

        if(is_null(self::$instance)){
            try{
                self::$instance = new \PDO("mysql:host=".DB_HOST.';port='.DB_PORT.';dbname='.DB_NAME, DB_USER, DB_PASS);
                self::$instance->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_SILENT);
                self::$instance->query('SET NAMES utf8');
                self::$instance->query('SET CHARACTER SET utf8');
            }catch(\PDOException $e) {
                $error = null;
                if(STORE_DEBUG) $error = $e->getMessage();
                return self::sendConnectionErrorResponse($error);

            }
        }

        return self::$instance;
    }

    public function index()
    {
        try{
            $this->query = "SELECT * FROM ".$this->model->getModelName();
            return $this->executeAndFetch();

        }catch (\PDOException $error){
            return $this->sendDatabaseErrorResponse($error);

        }
    }

    public function save($create = false)
    {
        if($create){
            $this->setCreateFields();
            $this->runCreateCalcFunctions();
        } else{
            $this->setUpdateFields();
        }

        $instance = self::getInstance();
        if(!$instance instanceof \PDO) return $instance;

        $properties = $this->model->properties();
        $key = array_search('ID', $properties);

        unset($properties[$key]);

        if($create){
            try{
                $question_marks = array_map(function($val) { return "?"; }, $properties);

                $query = "INSERT INTO (".implode(",",$properties).") VALUES (".implode(",",$question_marks)." ) ";


                $this->statement = self::getInstance()->prepare($query);

                $i = 1;
                $this->query_errors['query'] = $query;
                $this->query_errors['properties'] = $properties;
                $this->query_errors['questions'] = $question_marks;

                foreach($properties as $property){
                    if(!isset($this->query_errors['model'])){
                        $this->query_errors['model'] = [];
                    }
                    $this->query_errors['model'][$property]  = [
                        $i, $this->model->{$property}
                    ];

                    $this->statement->bindParam($i,$this->model->{$property});
                }

                return $this->testQueryErrors();

                $this->statement->execute();
            }catch (\PDOException $error){
                return $this->sendDatabaseErrorResponse($error);
            }


        }else{
            try {
                $this->query =  "UPDATE ".$this->model->getModelName()." SET "." "." WHERE ID = (:ID)";
                return $this->executeAndFetch(true);
            }catch (\PDOException $error){
                return $this->sendDatabaseErrorResponse($error);
            }
        }

    }

    public function find()
    {
        $instance = self::getInstance();
        if(!$instance instanceof \PDO) return $instance;

        try {
            $this->query = "SELECT * FROM " . $this->model->getModelName() . " WHERE ID = (:ID)";
            return $this->executeAndFetch(true);

            return $this->returnSuccessResponse($data);
        }catch (\PDOException $error){
            return $this->sendDatabaseErrorResponse($error);
        }
    }

    public function delete()
    {
        $instance = self::getInstance();
        if(!$instance instanceof \PDO) return $instance;
        try {
            $this->query = "DELETE FROM ".$this->model->getModelName()." WHERE ID = (:ID)";
            return $this->executeAndFetch(true,true);

        }catch (\PDOException $error){
            return $this->sendDatabaseErrorResponse($error);
        }
    }

    private function executeAndFetch($bind_ID = false,$delete = false, $return_just_data = false){

        $instance = self::getInstance();
        if(!$instance instanceof \PDO) return $instance;

        $this->statement = self::getInstance()->prepare($this->query);

        if($bind_ID){
            $this->statement->bindParam(':ID',$this->model->ID,\PDO::PARAM_INT);
        }

        $this->statement->execute();

        $data = $delete ? true : $this->statement->fetchAll(\PDO::FETCH_ASSOC);

        if($return_just_data) return $data;

        return $this->returnSuccessResponse($data);
    }

    private function sendDatabaseErrorResponse(\PDOException $error){
        $errors = [];

        if(STORE_DEBUG){
            $errors['query']        = $this->query;
            $errors['query_error']  = $error->getMessage();
        }

        return $this->returnErrorResponse($errors);
    }

    private function testQueryErrors(){
        return $this->returnErrorResponse($this->query_errors);
    }


    public function getAllData()
    {
        $this->query = "SELECT * FROM ".$this->model->getModelName();
        $data = $this->executeAndFetch(false,false,true);

        if(!is_array($data)) $data = [];

        return $data;
    }


    public function runCreateCalcFunctions()
    {
       $this->model->getCreateFunction($this->getAllData());
    }
}