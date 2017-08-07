<?php
namespace App\Core;

use App\Traits\toJson;

class ApiResponse{

    use toJson;

    const HTTP_OK                       = 200; // OK - Everything went well
    const HTTP_BAD_REQUEST              = 400; // Bad Request - Server does not understand what we mean
    const HTTP_METHOD_NOT_ALLOWED       = 405; // Method Not Allowed - Trying a get on a post method
    const HTTP_NOT_FOUND                = 404; // NOT FOUND - The request methods do not exist


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
     */
    public function __construct($success = false, $response = null, $status_code = 404)
    {
        $this->success      = $success;
        $this->status_code  = $status_code;
        $this->response     = $response;

    }



}