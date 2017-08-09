<?php

namespace App;
use App\Core\Api;
use App\Core\Model;

/**
 * This handles the request query string and maps the model / method for API Requests and model/views for all other requests
 *
 * Class Handler
 * @package App
 */
class Handler{


    private $models_namespace = '\App\Models\\';

    /**
     * Contains the request that comes through the query string
     * @var null | string
     */
    public $request;

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
     * Specifies the method name which we need to call
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


    public $data = array();

    const MODEL_NOT_FOUND = 'the data model you are looking for does not exist';


    public function __construct()
    {
        $this->request = isset($_GET['request']) ? $_GET['request'] : null;


        if($this->isApiRequest()) {
            $this->setApiRequestModelMethod();
        }if(is_null($this->request)){
            $this->view = 'index';
            $this->initTemplateEngine();
        }else{
            $this->setRegularRequestModelView();

        }


    }


    /**
     * Setting model/method variables for API Requests
     */
    private function setApiRequestModelMethod(){

        $the_request = $this->returnRequestSplit();

        $model = isset($the_request[1]) && (is_string($the_request[1])) ? ucwords(strtolower($the_request[1])) : null;

        if(!$this->doesModelExist($model)) return;

        $this->initModel($model);

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


    /**
     * Setting model / view for Regular Request
     */
    private function setRegularRequestModelView(){

        $the_request = $this->returnRequestSplit();

        $model = isset($the_request[0]) && (is_string($the_request[0])) ? ucwords(strtolower($the_request[0])) : null;

        if(!$this->doesModelExist($model)) return;

        $this->initModel($model);

        $view = isset($the_request[1]) && (is_string($the_request[1])) ? strtolower($the_request[1]) : null;

        if(!$this->isViewAllowed($view)) return;


        $this->view = strtolower($model).'/'.$view;
        $this->initTemplateEngine();

    }



    /**
     * Process the regular request template / data
     */
    private function initTemplateEngine(){
        $loader = new \Twig_Loader_Filesystem(STORE_VIEWS);
        $this->template = new \Twig_Environment($loader, array('cache' => STORE_CACHE,'debug' => STORE_DEBUG));

        $this->template;
    }


    public function isApiRequest(){
        return substr( $this->request, 0, 4 ) === "api/";
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

    private function isViewAllowed($view){
        return in_array($view,$this->views);
    }


    /**
     * @param $model
     */
    private function initModel($model)
    {
        $class = $this->models_namespace . $model;
        $this->model = new $class;
    }


    /**
     * @return array
     */
    private function returnRequestSplit()
    {
        $the_request = explode('/', $this->request);
        return $the_request;
    }

    public function loadView($data = []){
        $data = $this->addViewHeaderVariables($data);

        $view = $this->view.'.twig';
        if($view != 'errors/404.twig' && !file_exists(STORE_VIEWS.DS.$view)){
            $view = 'errors/501.twig';
        }


       echo $this->template->render($view,$data);
    }

    public function checkifMethodAllowed(){

    }

    private function addViewHeaderVariables(array $data){

        $data['token'] = $_SESSION['token'];

        return $data;
    }

}