<?php

namespace App\Http\Services;

use App\Traits\AIApi;
use App\Traits\Common;
use Illuminate\Support\Facades\Log;
class AIApiService
{
    use AIApi, Common;

    protected $host = null;
    protected $url = null;
    protected $method = null;

    public function __construct(){
        $this->host = config()->get('aiapi.host');
    }

    public function getStudentAbility($request){
        $this->url = config()->get('aiapi.api.estimate_student_competence.uri');
        $requestUrl = $this->host.$this->url;
        Log::info('AI-API - Request | estimate_student_competence');
        $result = $this->postCallToAIApi($request,$requestUrl);
        return $result;
    }

    public function getStudentProgressReport($request){
        $this->url = config()->get('aiapi.api.estimate_student_competence.uri');
        $requestUrl = $this->host.$this->url;
        Log::info('AI-API - Request | estimate_student_competence');
        $result = $this->postCallToAIApi($request,$requestUrl);
        return $result;
    }

    public function getPloatAnalayzeStudent($request){
        $this->url = config()->get('aiapi.api.Plot_Analyze_Student.uri');
        $requestUrl = $this->host.$this->url;
        Log::info('AI-API - Request | Plot_Analyze_Student');
        $result = $this->postCallToAIApi($request, $requestUrl);
        return $result;
    }

    public function getPloatAnalyzeData($request){
        $this->url = config()->get('aiapi.api.Plot_Analyze_Data.uri');
        $requestUrl = $this->host.$this->url;
        Log::info('AI-API - Request | Plot_Analyze_Data');
        $result = $this->postCallToAIApi($request, $requestUrl);
        return $result;
    }

    public function getPloatAnalyzeTestDifficulty($request){
        $this->url = config()->get('aiapi.api.Plot_Analyze_Test_Difficulty.uri');
        $requestUrl = $this->host.$this->url;
        Log::info('AI-API - Request | Plot_Analyze_Test_Difficulty');
        $result = $this->postCallToAIApi($request, $requestUrl);
        return $result;
    }

    public function getPloatAnalayzeQuestion($request){
        $this->url = config()->get('aiapi.api.Plot_Analyze_Question.uri');
        $requestUrl = $this->host.$this->url;
        Log::info('AI-API - Request | Plot_Analyze_Question');
        $result = $this->postCallToAIApi($request, $requestUrl);
        return $result;
    }

    public function Plot_Analyze_My_Class_Ability($request){
        $this->url = config()->get('aiapi.api.Plot_Analyze_My_Class_Ability.uri');
        $requestUrl = $this->host.$this->url;
        Log::info('AI-API - Request | Plot_Analyze_My_Class_Ability');
        $result = $this->postCallToAIApi($request, $requestUrl);
        return $result;
    }

    public function Plot_Analyze_My_School_Ability($request){
        $this->url = config()->get('aiapi.api.Plot_Analyze_My_School_Ability.uri');
        $requestUrl = $this->host.$this->url;
        Log::info('AI-API - Request | Plot_Analyze_My_School_Ability');
        $result = $this->postCallToAIApi($request, $requestUrl);
        return $result;
    }

    public function Plot_Analyze_All_Schools_Ability($request){
        $this->url = config()->get('aiapi.api.Plot_Analyze_All_Schools_Ability.uri');
        $requestUrl = $this->host.$this->url;
        Log::info('AI-API - Request | Plot_Analyze_All_Schools_Ability');
        $result = $this->postCallToAIApi($request, $requestUrl);
        return $result;
    }

    
    public function getPerformanceAnalysisReport($request){
        $response = [];
        $this->url = config()->get('aiapi.api.SkewNorm_Fit.uri');
        $requestUrl = $this->host.$this->url;
        $result = $this->postCallToAIApi($request,$requestUrl);
        if(isset($result) && !empty($result)){
            $result = json_decode($result, true);
            $max = -3;
            $min = 3;
            $steps = 100;
            $step = ($max - $min) / $steps;
            $result ['x_axis_val']= $this->convertValue(range( $max, $min, $step ));
        }
        return $result;
    }

    public function convertValue($array = []){
        $newArray = [];
        if(!empty($array)){
            foreach($array as $value){
                if(fmod($value, 3) !== 0.000){
                    array_push($newArray,number_format((float)$value,3));
                }else{
                    array_push($newArray,floatval($value));
                }
            }
            return $newArray;
        }
    }

    /**
     * USE : Generate questions for Manual Mode
     */
    public function Assign_Questions_Manually($request){
        $this->url = config()->get('aiapi.api.Assign_Questions_Manually.uri');
        $requestUrl = $this->host.$this->url;
        Log::info('AI-API - Request | Assign_Questions_Manually');
        $result = $this->postCallToAIApi($request, $requestUrl);
        Log::info('AI-API - Response | Assign_Questions_Manually'. json_encode($result));
        return $result;
    }
    
    /**
     * USE : Generate questions for Auto Mode
     */
    public function Assign_Questions_AutoMode($request){
        $this->url = config()->get('aiapi.api.Assign_Questions_AutoMode.uri');
        $requestUrl = $this->host.$this->url;
        $result = $this->postCallToAIApi($request, $requestUrl);
        return $result;
    }

    public function Assign_Questions_To_Learning_Units($request){
        $this->url = config()->get('aiapi.api.Assign_Questions_To_Learning_Units.uri');
        $requestUrl = $this->host.$this->url;
        Log::info('AI-API - Request | Assign_Questions_To_Learning_Units');
        $result = $this->postCallToAIApi($request, $requestUrl);
        return $result;
    }
    
    /**
     * USE : Generate questions for Manually Mode in Admin
     */
    public function Assign_Questions_Manually_To_Learning_Units($request){
        $this->url = config()->get('aiapi.api.Assign_Questions_Manually_To_Learning_Units.uri');
        $requestUrl = $this->host.$this->url;
        Log::info('AI-API - Request | Assign_Questions_Manually_To_Learning_Units');
        $result = $this->postCallToAIApi($request, $requestUrl);
        return $result;
    }

    /**
     * USE : Create Auto Peer Group
     */
    public function Create_Auto_Peer_Groups($request){
        $this->url = config()->get('aiapi.api.Create_Peer_Groups.uri');
        $requestUrl = $this->host.$this->url;
        $this->method = config()->get('aiapi.api.Create_Peer_Groups.method');
        Log::info('AI-API - Request | Create_Peer_Groups');
        $result = $this->postCallToAIApi($request, $requestUrl, $this->method);
        return $result;
    }

    /**
     * USE : Assign real time question to student AUTO mode
     */
    public function Real_Time_Assign_Question_N_Estimate_Ability($request){
        $this->url = config()->get('aiapi.api.Real_Time_Assign_Question_N_Estimate_Ability.uri');
        $requestUrl = $this->host.$this->url;
        $this->method = config()->get('aiapi.api.Real_Time_Assign_Question_N_Estimate_Ability.method');
        Log::info('AI-API - Request | Real_Time_Assign_Question_N_Estimate_Ability');
        $result = $this->postCallToAIApi($request, $requestUrl, $this->method);
        return $result;
    }
}