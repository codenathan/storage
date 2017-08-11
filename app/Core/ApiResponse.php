<?php
namespace App\Core;

use App\Traits\toJson;

class ApiResponse{

    use toJson;

    const HTTP_OK                       = 200; // OK - Everything went well
    const HTTP_BAD_REQUEST              = 400; // Bad Request - Server does not understand what we mean
    const UNAUTHORIZED                  = 401; // Unauthorized - Someone has tampered with the data
    const HTTP_METHOD_NOT_ALLOWED       = 405; // Method Not Allowed - Trying a get on a post method
    const HTTP_NOT_FOUND                = 404; // NOT FOUND - The request methods do not exist
    const SERVICE_UNAVAILABLE           = 503; // SERVICE UNAVAILABLE - Error internal


    /**
     * Used to see if the request is success or not
     *
     * @var boolean
     */
    public $success = false;

    /**
     * Response data
     *
     * @var  array
     */
    public $response = [];

    /**
     * The status code as a result of request
     *
     * @var int
     */
    public $status_code = 404;

    public $error       = [];


    /**
     * ApiResponse constructor.
     * @param $success
     * @param $status_code
     * @param $response
     * @param $error
     */
    public function __construct($success = false,array $response = null, $status_code = 404,array $error = null)
    {
        $this->success      = $success;
        $this->status_code  = $status_code;
        $this->response     = $response;
        $this->error        = $error;

    }

    public function applyInvalidMethod(){
        $this->success      = false;
        $this->status_code  = self::HTTP_METHOD_NOT_ALLOWED;
        $this->error['request']        = [ 'You have made an invalid request'];
        $this->response     = [];
        $this->printOutput();
    }

    public function applyInvalidToken(){
        $this->success      = false;
        $this->status_code  = self::UNAUTHORIZED;
        $this->error['auth'] = ['this is an unauthorized request'];
        $this->response     = [];
        $this->printOutput();
    }



}