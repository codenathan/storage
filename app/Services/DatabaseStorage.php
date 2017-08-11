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

    public $params;


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
            $this->query = "SELECT * FROM {$this->getTableName()}";
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
                $this->params = array_map(function($property) { return ":$property"; }, $properties);


                $this->query = "INSERT INTO {$this->getTableName()}(".implode(",",$properties).") VALUES (".implode(",",$this->params)." )";

                return $this->executeWithParameters(false);
            }catch (\PDOException $error){
                return $this->sendDatabaseErrorResponse($error);
            }


        }else{
            try {

                $this->params = array_map(function($property) { return " $property = :$property "; }, $properties);
                $this->query =  "UPDATE {$this->getTableName()} SET ".implode(",",$this->params)." WHERE ID = (:ID)";
                return $this->executeWithParameters(true);

            }catch (\PDOException $error){
                return $this->sendDatabaseErrorResponse($error);
            }
        }

    }

    private function executeWithParameters($bindID = false){
        $this->statement = self::getInstance()->prepare($this->query);
        foreach($this->params as $property){
            $this->statement->bindParam($property,$this->model->{$property});
        }

        return $this->executeAndFetch($bindID);
    }

    public function find()
    {
        $instance = self::getInstance();
        if(!$instance instanceof \PDO) return $instance;

        try {
            $this->query = "SELECT * FROM {$this->getTableName()} WHERE ID = (:ID)";
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
            $this->query = "DELETE FROM {$this->getTableName()} WHERE ID = (:ID)";
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
            $this->statement->bindParam('ID',$this->model->ID,\PDO::PARAM_INT);
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


    public function getAllData()
    {
        $this->query = "SELECT * FROM {$this->getTableName()}";
        $data = $this->executeAndFetch(false,false,true);

        if(!is_array($data)) $data = [];

        return $data;
    }

    public function runCreateCalcFunctions()
    {
       $this->model->getCreateFunction($this->getAllData());
    }

    private function getTableName(){
        return ucwords($this->model->getModelName());
    }
}