<?php

namespace App\Exceptions;

use App\Http\Controllers\CommonController;
use Exception;

class CustomException extends Exception
{
    public $exceptionObjectOrString;
    public $code;
    public $errorMessages;

    /**
     * CustomException constructor.
     * @param $exceptionObjectOrString
     * @param int $code
     * @param array $errorMessages
     */
    public function __construct($exceptionObjectOrString, $code = 404, $errorMessages = [])
    {
        $this->exceptionObjectOrString = $exceptionObjectOrString;
        $this->code = $code;
        $this->errorMessages = $errorMessages;
        $this->run();
    }

    public function run()
    {
        $UserController = (new CommonController());
        $UserController->getResponseArray = true;
        $response = $UserController->sendError($this->exceptionObjectOrString, $this->code, $this->errorMessages);
        $code = $response['code'];
        header("Content-Type: application/json");
        http_response_code($code);
        echo json_encode($response);
        exit;
    }
}
