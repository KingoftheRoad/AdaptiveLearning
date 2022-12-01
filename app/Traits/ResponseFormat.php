<?php

namespace App\Traits;

use App\Constants\MailConstants;
use App\Exceptions\CustomException;
use App\Jobs\FulfilmentCustomerUserAlert;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Cookie;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Eloquent\QueryException;
use App\Constants\DbConstant as cn;
use App\Jobs\WelcomeEmailJob;
use App\Jobs\SendMailExamsAssignNotificationJob;

trait ResponseFormat
{
    public function sendResponse($result, $message = "Operation success", $status = "success", $code = "200", $cookie = null)
    {
        $response = [
            'data' => $result,
            'status' => $status,
            'message' => $this->getResponseMessage($message),
            'code' => $code,
            'version' => $this->version(),
        ];

        if (!$cookie) {
            return response()->json($response, 200);
        } else {
            return response()->json($response, 200)->cookie($cookie);
        }
    }

    public $getResponseArray = false;

    /**
     * return error response.
     *
     * @param $error
     * @param $code
     * @param array $errorMessages
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendError($error, $code = 404, $errorMessages = [])
    {
        if (is_object($error) and method_exists($error, 'getMessage')) {
            $error_message = $error->getMessage();
            $handleException = $this->handleException($error);
            if (!empty($handleException['error'])) {
                $error_message = $handleException['error'];
            }
            if (!empty($handleException['code'])) {
                $code = $handleException['code'];
            }
        } else {
            $error_message = $error;
        }
        $response = [
            'status' => 'failed',
            'code' => $code,
            'message' => $this->getResponseMessage($error_message),
            'version' => $this->version(),
        ];
        $response = $this->handleUndefinedArrayKeyException($response);
        if (!empty($errorMessages)) {
            $response['errors'] = $errorMessages;
            if (!empty($response['message']) and is_string($response['message'])) {
                $response['message'] = $response['message'] . ' ' . $this->makeMessageForValidation($errorMessages);
            } else {
                $response['message'] = $this->makeMessageForValidation($errorMessages);
            }
        }
        if ($this->getResponseArray) {
            return $response;
        }
        //ApiRequestLog::APIErrorLogger($response, $code);
        return response()->json($response, $code);
    }

    public function getResponseMessage($error_message)
    {
        $stringFinalString = false;
        if (\Lang::has($error_message)) {
            $stringFinalString = __($error_message);
        } elseif (\Lang::has('rest_api_messages.' . $error_message)) {
            $stringFinalString = __('rest_api_messages.' . $error_message);
        }
        $stringFinalString = str_replace('rest_api_messages.', '', $stringFinalString);
        if (!empty($stringFinalString)) {
            $error_message = $stringFinalString;
        }
        return $error_message;
    }

    public function handleUndefinedArrayKeyException($response)
    {
        if (strpos($response['message'], "Undefined index") !== false) {
            $response['message'] = str_replace('Undefined index: ', '', $response['message']);
            $response['message'] = $response['message'] . ' is required';
            $code = 422;
            $response['code'] = $code;
        }
        return $response;
    }

    public function handleException($exceptionObject)
    {
        $returnValue['error'] = $exceptionObject->getMessage();
        if ($exceptionObject instanceof ModelNotFoundException) {
            $returnValue['error'] = NOT_FOUND;
            $returnValue['code'] = 404;
        }
        if ($exceptionObject instanceof QueryException) {
            if (strpos($exceptionObject->errorInfo[2], "Duplicate entry") !== false) {
                $returnValue['error'] = 'An error occurred, duplicate entry';
                $returnValue['code'] = 409;
            }
        }
        return $returnValue;
    }

    private function version()
    {
        return "2";
    }

    public function makeValidation($request, $rules, $message = [])
    {
        $validationArray = is_array($request) ? $request : $request->all();
        if (!empty($message)) {
            $v = Validator::make($validationArray, $rules, $message);
        } else {
            $v = Validator::make($validationArray, $rules);
        }
        if (!$v->fails()) {
            return true;
        } else {
            $errors_all = $v->errors()->all();
            if (!empty($errors_all)) {
                $sendError = $this->sendError('Validation Occurred ', 422, $errors_all);
                http_response_code(422);
                echo json_encode($sendError->getData());
                header("Content-Type: application/json");
                exit;
            } else {
                return true;
            }
        }
    }

    public function sendMails($bladeName = [], $data = [], $to = '', $subject = '', $files = [], $contents = [], $bcc = '', $cc = '', $body = '')
    {
        try {
            Mail::send($bladeName, $data, function ($message) use ($to, $bcc, $cc, $subject, $body, $files, $contents) {
                $message->to($to);
                $message->subject($subject);

                if (isset($body) && !empty($body)) {
                    $message->setBody($body, 'text/html');
                }
                if (isset($cc) && !empty($cc)) {
                    $message->cc($cc);
                }
                if (isset($bcc) && !empty($bcc)) {
                    $message->bcc($bcc);
                }
                //Mail Attachments
                if (isset($files) && !empty($files)) {
                    foreach ($files as $file) {
                        if (isset($file)) {
                            $message->attach($file);
                        }
                    }
                }
                if (isset($contents) && !empty($contents)) {
                    foreach ($contents as $content) {
                        if (isset($content['content'])) {
                            $message->attachData($content['content'], $content['file_name']);
                        }
                    }
                }
            });
        } catch (\Exception $ex) {
            Log::info('Sending Email Failed Welcome Mail Job : '.$ex->getMessage());
        }
    }

    public function sendEmail($type, $object)
    {
        switch ($type) {
            case 'welcome-email':
                dispatch(new WelcomeEmailJob($object))->delay(now()->addSeconds(2));
                break;
            case 'send-mail-assign-exams-notifications':
                dispatch(new SendMailExamsAssignNotificationJob($object))->delay(now()->addSeconds(2));
                break;
        }
    }
}
