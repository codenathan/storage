<?php

namespace  App\Services;

use App\Core\Storage;
use App\Interfaces\iStorage;

class DatabaseStorage extends Storage implements iStorage{


    /**
     * @var null | string | \PDO
     */
    public static $instance;

    public $statement;

    public static function getInstance(){

        if(is_null(self::$instance)){
            try{
                self::$instance = new \PDO("mysql:host=".DB_HOST.';port='.DB_PORT.';dbname='.DB_NAME, DB_USER, DB_PASS);
                self::$instance->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_SILENT);
                self::$instance->query('SET NAMES utf8');
                self::$instance->query('SET CHARACTER SET utf8');
            }catch(\PDOException $error) {
                echo $error->getMessage();

            }
        }

        return self::$instance;
    }


    public function index()
    {
        $query = "SELECT * FROM ".$this->model->getModelName();
        $this->statement = self::getInstance()->prepare($query);
        $this->statement->execute();
    }

    public function save($create = false)
    {
        $properties = $this->model->properties();
        unset($properties['ID']);

        if($create){

            $question_marks = array_map(function($val) { return "?"; }, $properties);

            $query = "INSERT INTO (".implode(",",$properties).") VALUES (".implode(",",$question_marks)." ) ";
            $this->statement = self::getInstance()->prepare($query);

            $i = 1;
            foreach($properties as $property){
                $this->statement->bindParam($i,$this->model->{$property});
            }

            $this->statement->execute();


        }else{

            $query =  "UPDATE ".$this->model->getModelName()." SET "." "." WHERE ID = (:ID)";
            $this->statement = self::getInstance()->prepare($query);
            $this->statement->bindParam(':ID',$this->model->ID,\PDO::PARAM_INT);
            $this->statement->execute();
        }

    }

    public function find()
    {
        $query = "SELECT * FROM ".$this->model->getModelName()." WHERE ID = (:ID)";
        $this->statement = self::getInstance()->prepare($query);
        $this->statement->bindParam(':ID',$this->model->ID,\PDO::PARAM_INT);
        $this->statement->execute();
        $this->statement->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function delete()
    {
        $query = "DELETE FROM ".$this->model->getModelName()." WHERE ID = (:ID)";
        $this->statement = self::getInstance()->prepare($query);
        $this->statement->bindParam(':ID',$this->model->ID,\PDO::PARAM_INT);
        $this->statement->execute();
    }




}