<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Traits\Common;
use App\Traits\ResponseFormat;
use Illuminate\Support\Facades\Auth;
use App\Constants\DbConstant As cn;
use Exception;
use App\Models\User;
use App\Jobs\UpdateStudentOverAllAbility;
use App\Models\ExamSchoolMapping;
use App\Models\Exam;
use App\Jobs\UpdateMyTeachingReportJob;
use App\Jobs\UpdateMyTeachingTableJob;
use App\Jobs\UpdateUserCreditPointsJob;
use App\Jobs\UpdateQuestionEColumnJob;
use App\Jobs\UpdateExamReferenceNumberJob;
use App\Http\Controllers\Reports\AlpAiGraphController;
use App\Models\GradeClassMapping;
use App\Models\GradeSchoolMappings;
use App\Models\MyTeachingReport;
use App\Models\AttemptExams;
use App\Models\PeerGroup;
use App\Models\Question;
use Log;
use App\Helpers\Helper;
use App\Models\ExamGradeClassMappingModel;
use App\Http\Services\AIApiService;

class CronJobController extends Controller
{
    use Common, ResponseFormat;

    protected $AIApiService;
    
    public function __construct(){
        $this->AIApiService = new AIApiService();
    }

    /**
     * USE : Update My Teaching Table via cron job urls
     *  Update via all records
     */
    public function updateMyTeachingReports(){
        dispatch(new UpdateMyTeachingReportJob())->delay(now()->addSeconds(1));   
    }

    /**
     * USE : Update My Teaching Table after student attemp exam
     *  update via school id and exam id
     */
    public function UpdateMyTeachingTable($schoolId, $examId){
        if(!empty($schoolId) && !empty($examId)){
            dispatch(new UpdateMyTeachingTableJob($schoolId, $examId))->delay(now()->addSeconds(1));
        }
    }

    /**
     * USE : Update All Student Over All Ability
     */
    public function UpdateAllStudentAbility(){
        $Students = User::where(cn::USERS_ROLE_ID_COL,cn::STUDENT_ROLE_ID)->get();
        if(!$Students->isEmpty()){
            foreach($Students as $student){
                dispatch(new UpdateStudentOverAllAbility($student))->delay(now()->addSeconds(1));
            }
        }
    }

    /**
     * USE : Update Single Student Over All Ability
     */
    function UpdateStudentOverAllAbility(){
        dispatch(new UpdateStudentOverAllAbility(Auth::user()))->delay(now()->addSeconds(1));
    }

    /**
     * USE : Remove duplicate assigned student
     */
    public function RemoveDuplicateStudent(){
        $ExamList = Exam::all();
        if(!empty($ExamList)){
            foreach($ExamList as $exam){
                if(isset($exam->student_ids) && !empty($exam->student_ids)){
                    $studentIds = implode(',',array_unique(explode(',',$exam->student_ids)));
                    Exam::find($exam->id)->Update(['student_ids' => $studentIds]);
                }
            }
        }
    }

    /**
     * USE : Assign Credit Point to student via system
     */
    public function UpdateStudentCreditPoints($ExamId, $StudentId){
        if(!empty($ExamId) && !empty($StudentId)){
            $SchoolId = Auth::user()->school_id;
            dispatch(new UpdateUserCreditPointsJob($ExamId, $StudentId, $SchoolId))->delay(now()->addSeconds(1));
        }
    }

    /**
     * USE : Assign credit points manually to students.
     */
    public function AssignCreditPointsManually(){
        $AttemptExams = AttemptExams::get();
        foreach($AttemptExams as $data){
            $SchoolId = Auth::user()->school_id;
            dispatch(new UpdateUserCreditPointsJob($data->exam_id, $data->student_id, $SchoolId))->delay(now()->addSeconds(1));
        }
    }

    /**
     * USE : Update Questions based on question codes
     */
    public function updateQuestionEColumn(){
        dispatch(new UpdateQuestionEColumnJob())->delay(now()->addSeconds(1));
    }

    /**
     * USE : Update Exam Reference Number Cronjob
     */
    public function UpdateExamReferenceNumber(){
        dispatch(new UpdateExamReferenceNumberJob())->delay(now()->addSeconds(1));
        echo "Job Completed Successfully";
    }

    /**
     * USE : Update Question Option From A to B In Attempted Exam Update Option.
     */
    public function UpdateStudentSelectedAnswer(){
        ini_set('max_execution_time', -1);
        $questionId= 747;
        $ExamIds = Exam::whereRaw("find_in_set($questionId,question_ids)")->withTrashed()->get()->pluck('id')->toArray();
        $apiData = [];
        if(isset($ExamIds) && !empty($ExamIds)){
            foreach($ExamIds as $ExamId){
                $examDetail = Exam::find($ExamId);
                $AttemptedAnswerData = AttemptExams::where('exam_id',$ExamId)->get();
                if(isset($AttemptedAnswerData) && !empty($AttemptedAnswerData)){
                    foreach($AttemptedAnswerData as $AttemptedAnswer){                                        
                        $questionAnswersData = json_decode($AttemptedAnswer->question_answers,true);                    
                        $AttemptFirstTrialData = json_decode($AttemptedAnswer->attempt_first_trial,true);
                        $AttemptSecondTrialData = json_decode($AttemptedAnswer->attempt_second_trial,true);

                        // Update First-trial column
                        if(isset($AttemptFirstTrialData) && !empty($AttemptFirstTrialData)){
                            foreach($AttemptFirstTrialData as $firstTrialKey => $firstTrialData){
                                if($firstTrialData['answer']==1){
                                    $AttemptFirstTrialData[$firstTrialKey]['answer'] = 2;
                                }
                                if($firstTrialData['answer']==2){
                                    $AttemptFirstTrialData[$firstTrialKey]['answer'] = 1;
                                }
                            }
                        }

                        // Update Second-trial column
                        if(isset($AttemptSecondTrialData) && !empty($AttemptSecondTrialData)){
                            foreach($AttemptSecondTrialData as $secondTrialKey => $secondTrialData){
                                if($secondTrialData['answer']==1){
                                    $AttemptSecondTrialData[$secondTrialKey]['answer'] = 2;
                                }
                                if($secondTrialData['answer']==2){
                                    $AttemptSecondTrialData[$secondTrialKey]['answer'] = 1;
                                }
                            }
                        }

                        //update Question Answer Data
                        if(!empty($questionAnswersData)){
                            $NoOfCorrectAnswers = 0;
                            $NoOfWrongAnswers = 0;
                            foreach($questionAnswersData as $key => $questionAnswer){
                                if($questionAnswer['answer']==1){
                                    $questionAnswersData[$key]['answer'] = 2;
                                }
                                if($questionAnswer['answer']==2){
                                    $questionAnswersData[$key]['answer'] = 1;
                                }
                                // Get Questions Answers and difficulty level
                                $responseData = $this->GetQuestionNumOfAnswerAndDifficultyValue($questionAnswer['question_id']);
                                $apiData['num_of_ans_list'][] = $responseData['noOfAnswers'];
                                $apiData['difficulty_list'][] = $responseData['difficulty_value'];
                                $apiData['max_student_num'] = 1;
                            
                                //For check answer
                                $answer = $questionAnswer['answer'];
                                $QuestionAnswerDetail = Question::where(cn::QUESTION_TABLE_ID_COL,$questionAnswer['question_id'])->with('answers')->first();
                                if(isset($QuestionAnswerDetail)){
                                    // echo $QuestionAnswerDetail->answers->{'correct_answer_'.$questionAnswer['language']} . '' .$answer;die;
                                    if($QuestionAnswerDetail->answers->{'correct_answer_'.$questionAnswer['language']} == $answer){
                                        $NoOfCorrectAnswers = ($NoOfCorrectAnswers + 1);
                                        $apiData['questions_results'][] = true;
                                    }else{
                                        $NoOfWrongAnswers = ($NoOfWrongAnswers + 1);
                                        $apiData['questions_results'][] = false;
                                    }
                                } 
                            }
                            $StudentAbility = '';
                            if(!empty($apiData)){                    
                                // Get the student ability from calling AIApi
                                $StudentAbility = $this->GetAIStudentAbility($apiData);
                            }

                            $PostData = [                    
                                cn::ATTEMPT_EXAMS_QUESTION_ANSWER_COL => (!empty($questionAnswersData)) ? json_encode($questionAnswersData) : null,
                                cn::ATTEMPT_EXAMS_ATTEMPT_FIRST_TRIAL_COL => (!empty($AttemptFirstTrialData)) ? json_encode($AttemptFirstTrialData) : null,
                                cn::ATTEMPT_EXAMS_ATTEMPT_SECOND_TRIAL_COL => (!empty($AttemptSecondTrialData)) ? json_encode($AttemptSecondTrialData) : null,
                                cn::ATTEMPT_EXAMS_TOTAL_CORRECT_ANSWERS => $NoOfCorrectAnswers,
                                cn::ATTEMPT_EXAMS_TOTAL_WRONG_ANSWERS => $NoOfWrongAnswers,
                                cn::ATTEMPT_EXAMS_STUDENT_ABILITY_COL => ($StudentAbility!='') ? $StudentAbility : null
                            ];

                            $Update = AttemptExams::find($AttemptedAnswer->id)->Update($PostData);
                            if($Update){
                                /** Start Update overall ability for the student **/
                                $this->UpdateStudentOverAllAbility();

                                /** Update My Teaching Table Via Cron Job */
                                $userData = User::find($AttemptedAnswer->student_id);
                                $this->UpdateMyTeachingTable($userData->{cn::USERS_SCHOOL_ID_COL}, $ExamId);

                                if($examDetail->exam_type == 2 || ($examDetail->exam_type == 1 && $examDetail->self_learning_test_type == 1)){
                                    /** Update Student Credit Points via cron job */
                                    $this->UpdateStudentCreditPoints($ExamId, $AttemptedAnswer->student_id);
                                }
                            }
                        }
                    }
                }
            }
        }else{
            echo 'No any exams in use this question';
        }
    }

    /***
     * USE : Find the student Ability using AI API
     */
    public function GetAIStudentAbility($apiData){
        $StudentAbility = '';
        $requestPayload = new \Illuminate\Http\Request();
        $requestPayload = $requestPayload->replace([
            'questions_results'=> array($apiData['questions_results']),
            'num_of_ans_list' => $apiData['num_of_ans_list'],
            'difficulty_list' => array_map('floatval', $apiData['difficulty_list']),
            'max_student_num' => 1
        ]);
        $AIApiResponse = $this->AIApiService->getStudentAbility($requestPayload);
        if(isset($AIApiResponse) && !empty($AIApiResponse)){
            $StudentAbility = $AIApiResponse[0];
        }
        return $StudentAbility;
    }
}
