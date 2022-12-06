<?php

namespace App\Traits;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
trait AIApi
{
    public function postCallToAIApi($request, $endpoint, $method=''){
        $headers = [];
        $headers = [
            'Content-type' => 'application/json',
        ];
        $requestData = $request->all();
        $requestBody = json_encode($requestData, true);
        //echo '<pre>';print_r($requestBody);die;
        //Log::info('AI-API | Request Payload | ' . $requestBody);
        $client = new \GuzzleHttp\Client(['headers' => $headers,'verify' => false]);
        try {
            if(empty($method)){
                $method = 'POST';
            }
            $response = $client->request($method, $endpoint, ['body' => $requestBody]);
            $content = json_decode($response->getBody(), true);
            if(!empty($content)){
                return $content;
            }else{
                return $response->getStatusCode();
            }
        } catch (\GuzzleHttp\Exception\ClientException $ex) {
            $responseBody = $ex->getResponse()->getBody()->getContents();
            $code = $ex->getResponse()->getStatusCode();
            if ($code == '409') {
                Log::error('AI-API | Request Payload| ' . $responseBody);
            } else {
                throw new \Exception($responseBody);
            }
        }
    }

    public function StudentAnalysis($request, $endpoint, $method=''){
        $headers = [];
        $headers = [
            'Content-type' => 'application/json',
        ];
        $requestData = $request->all();
        $requestBody = json_encode($requestData, true);
        $client = new \GuzzleHttp\Client(['headers' => $headers,'verify' => false]);
        if(empty($method)){
            $method = 'POST';
        }
        $response = $client->request($method, $endpoint, ['body' => $requestBody]);
        $content = json_decode($response->getBody(), true);
        echo '<img src="data:image/png;base64,' . $content . '">';
    }
}