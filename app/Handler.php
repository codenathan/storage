<?php

namespace App;
use App\Core\Api;
use App\Core\Model;

/**
 * This handles the request query string and maps the model / method for API Requests and model/views for all other requests
 * Class Handler
 * @package App
 */
class Handler{


    private $models_namespace = '\App\Models\\';

    /**
     * Contains the request that comes through the query string
     * @var null | string
     */
    public $query_string;

    /*
     * Stores the request method
     * @var null | string
     */
    public $request_method;

    /**
     * The twig environment container
     * @var null | \Twig_Environment
     */
    public $template = null;

    /**
     * Container for the model
     * @var null | Model
     */
    public $model = null;


    /**
     * Specifies the method name which we need to call on the API
     * @var string | null
     */
    public $method = null;


    /**
     * A list of allowed views
     * @var array
     */
    public $views = ['index','create','edit'];


    /**
     * This string holds the view that should be rendered
     * @var string
     */
    public $view = 'errors/404';


    /**
     * If not API Request this will hold the view data
     * @var array
     */
    public $view_data = array();

    /**
     * Property that holds if the method is allowed ( API Request only)
     * @var boolean
     */
    public $method_allowed = false;


    public $token = null;

    public $postData = array();

    /**
     * Property that holds if the method is verified ( API and Non GET Requests)
     * @var boolean
     */
    public $verified_token = false;

    const MODEL_NOT_FOUND = 'the data model you are looking for does not exist';


    public function __construct()
    {
        $this->query_string = isset($_GET['qs']) ? $_GET['qs'] : null;
        $this->token        = isset($_POST['_token']) ? $_POST['_token'] : null;

        if($this->isApiRequest()) {

            $this->setApiRequestModelMethod();
            $this->isMethodAllowed();

            if($this->request_method != 'get') $this->verifyToken();

            if($this->request_method != 'get') {
                $this->postData = $_POST;
                $this->mapData();
            }

        }else{

            // IF not API Request lets render the homepage
            $this->view = 'index';
            $this->initTemplateEngine();
        }


      /*
        if(is_null($this->query_string)){
            $this->view = 'index';
            $this->initTemplateEngine();
        }else{
            $this->setRegularRequestModelView();

        }*/


    }


    /**
     * Setting model/method variables for API Requests
     */
    private function setApiRequestModelMethod(){

        $the_request = $this->returnRequestSplit();

        $model_name = isset($the_request[1]) && (is_string($the_request[1])) && !empty($the_request[1]) ? ucwords(strtolower($the_request[1])) : null;

        if(!$this->doesModelExist($model_name)) return;

        $this->initModel($model_name);

        $model_id = isset($the_request[2]) && is_numeric($the_request[2]) ? $the_request[2] : null;

        if($model_id){
            $this->model->ID = $model_id;

            $show = !isset($the_request[3]) ? true : false;

            $delete = isset($the_request[4]) && $the_request[4] == 'delete' ? true : false;

            $method = $delete ? 'delete' : $show ? 'show' : 'update';


        }else{
            $method = isset($the_request[2]) && is_string($the_request[2]) ? ucwords(strtolower($the_request[2])) : null;
        }


        if(!$this->doesAPiMethodExist($method)) return;

        $this->method = $method;
    }

    /*
    /**
     * Setting model / view for Regular Request

    private function setRegularRequestModelView(){

        $the_request = $this->returnRequestSplit();

        $model_name = isset($the_request[0]) && (is_string($the_request[0])) && !empty($the_request[0]) ? ucwords(strtolower($the_request[0])) : null;

        if(!$this->doesModelExist($model_name)) return;

        $this->initModel($model_name);


        $view = isset($the_request[1]) && (is_string($the_request[1])) && !empty($the_request[1]) ? strtolower($the_request[1]) : 'index';

        if(!$this->isViewAllowed($view)) return;

       if($view == 'edit' && isset($the_request[2]) && is_numeric($the_request[2])) $this->addViewData('_id',$the_request[2]);


        $this->view = strtolower($model_name).'/'.$view;
        $this->initTemplateEngine();


    }
    */

    /**
     * Process the regular request template / data
     */
    private function initTemplateEngine(){
        $loader = new \Twig_Loader_Filesystem(STORE_VIEWS);
        $this->template = new \Twig_Environment($loader, array('cache' => STORE_CACHE,'debug' => STORE_DEBUG));

        $this->template;
    }


    public function isApiRequest(){
        return substr( $this->query_string, 0, 4 ) === "api/";
    }


    public function doesModelExist($model){

        return class_exists($this->models_namespace.$model);
    }

    private function doesAPiMethodExist($method){
        $method = strtolower($method);

        $api_methods = (new \ReflectionClass(Api::class))->getMethods(\ReflectionMethod::IS_PUBLIC);

        $api_method_names = array_column($api_methods,'name');

        unset($api_method_names['__construct']);

        return in_array($method,$api_method_names);
    }

    /*
    private function isViewAllowed($view){
        return in_array($view,$this->views);
    }
    */

    private function initModel($model_name)
    {
        $class = $this->models_namespace . $model_name;
        $this->model = new $class;
    }


    /**
     * @return array
     */
    private function returnRequestSplit()
    {
        $the_request = explode('/', $this->query_string);
        return $the_request;
    }

    public function loadView(){

        $this->addViewHeaderVariables();
        $view = $this->model ? $this->model->getModelName().'.'.$this->view : $this->view;
        $this->addViewData('view', $view);

        $view = $this->view.'.twig';
        if($view != 'errors/404.twig' && !file_exists(STORE_VIEWS.DS.$view)){
            $view = 'errors/501.twig';
        }


       echo $this->template->render($view,$this->view_data);
    }

    /**
     * Checks if the method is allowed
     */
    public function isMethodAllowed(){
        $this->request_method = isset($_POST['_method']) ? $_POST['_method'] : strtolower($_SERVER['REQUEST_METHOD']);

        $method_request_method_mapping = [
            'get'       => ['index','show'],
            'post'      => ['create'],
            'put'       => ['update'],
            'patch'     => ['update'],
            'delete'    => ['delete']
        ];

        if(!in_array($this->request_method,array_keys($method_request_method_mapping))) $this->method_allowed =  false;

        $this->method_allowed = in_array(strtolower($this->method),$method_request_method_mapping[$this->request_method]);

    }

    /**
     * Checks if the data has been tampered with, only required for non get request
     */
    public function verifyToken(){
        $this->verified_token = is_null($this->token) && empty($this->token) ? false : hash_equals($_SESSION['_token'],$this->token);
    }

    private function addViewHeaderVariables(){
        $this->view_data['_token'] = $_SESSION['_token'];
        $this->view_data['STORE_DEBUG'] = STORE_DEBUG;
    }

    private function addViewData($label,$value){
        $this->view_data[$label] = $value;
    }

    private function mapData(){
        foreach ($this->model->properties() as $property){
            $this->model->{$property} = isset($this->postData[$property]) ? $this->postData[$property] : null;
        }
    }



}