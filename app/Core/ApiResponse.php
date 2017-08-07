<?php
namespace App\Core;

class ApiResponse{

    const HTTP_OK = 200;
    const HTTP_METHOD_NOT_ALLOWED = 405;
    const HTTP_NOT_FOUND = 404;

    /**
     * @var boolean
     */
    public $success = false;

    /**
     * @var null
     */
    public $response = null;

    /**
     * @var int
     */
    public $status_code = 404;


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

        return \json_encode($this);
    }

}