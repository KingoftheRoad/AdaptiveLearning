<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Http\Request;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Log;
use App\Constants\DbConstant As cn;
use App\Traits\Common;
use App\Models\Exam;
use App\Models\User;
use App\Models\Question;
use App\Models\AttemptExams;
use App\Models\Answer;
use App\Http\Services\AIApiService;
use App\Http\Controllers\CronJobController;

class UpdateAttemptExamsTableJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, Common;

    protected $AIApiService, $CronJobController;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        ini_set('max_execution_time', -1);
        $this->AIApiService = new AIApiService();
        $this->CronJobController = new CronJobController;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // $exams = AttemptExams::pluck('exam_id')->unique()->toArray();
        // if(isset($exams) && !empty($exams)){
        //     foreach($exams as $examId){
                $examId = 32;
                $examDetail = Exam::find($examId);
                $AttemptExams = AttemptExams::where([cn::ATTEMPT_EXAMS_EXAM_ID => $examId])->get();
                if(isset($AttemptExams) && !empty($AttemptExams)){
                    foreach($AttemptExams as $AttemptExams){
                        $apiData = [];
                        if(isset($AttemptExams->question_answers) && !empty($AttemptExams->question_answers)){
                            $NoOfCorrectAnswers = 0;
                            $NoOfWrongAnswers = 0;
                            foreach (json_decode($AttemptExams->question_answers) as $key => $value) {
                                $QuestionId = $value->question_id;
                
                                // Get Questions Answers and difficulty level
                                $responseData = $this->GetQuestionNumOfAnswerAndDifficultyValue($value->question_id);
                                $apiData['num_of_ans_list'][] = $responseData['noOfAnswers'];
                                $apiData['difficulty_list'][] = $responseData['difficulty_value'];
                                $apiData['max_student_num'] = 1;
            
                                $answer = $value->answer;
                                $QuestionAnswerDetail = Question::where(cn::QUESTION_TABLE_ID_COL,$QuestionId)->with('answers')->first();
                                if(isset($QuestionAnswerDetail)){
                                    if($QuestionAnswerDetail->answers->{'correct_answer_'.$value->language} == $answer){
                                        $NoOfCorrectAnswers = ($NoOfCorrectAnswers + 1);
                                        $apiData['questions_results'][] = true;
                                    }else{
                                        $NoOfWrongAnswers = ($NoOfWrongAnswers + 1);
                                        $apiData['questions_results'][] = false;
                                    }
                                }
                            }
                        }
                        $StudentAbility = '';
                        if(!empty($apiData)){
                            Log::info('Attempt Id :'.$AttemptExams->id);
                            Log::info('Student Id :'.$AttemptExams->student_id);
                            // Get the student ability from calling AIApi
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
                                Log::info('Student Ability :'.$StudentAbility);
                            }
                        }
                        $update = AttemptExams::find($AttemptExams->id)->Update([
                                    cn::ATTEMPT_EXAMS_TOTAL_CORRECT_ANSWERS => $NoOfCorrectAnswers,
                                    cn::ATTEMPT_EXAMS_TOTAL_WRONG_ANSWERS => $NoOfWrongAnswers,
                                    cn::ATTEMPT_EXAMS_STUDENT_ABILITY_COL => ($StudentAbility!='') ? $StudentAbility : null
                                ]);
                        if($update){
                            /** Start Update overall ability for the student **/
                           // $this->CronJobController->UpdateStudentOverAllAbility();
                           $studentData = User::find($AttemptExams->{cn::ATTEMPT_EXAMS_STUDENT_STUDENT_ID});
                           $this->CronJobController->UpdateStudentOverAllAbilityNew($studentData);

                            /** Update My Teaching Table Via Cron Job */
                            $this->CronJobController->UpdateMyTeachingTable($studentData->{cn::USERS_SCHOOL_ID_COL}, $examId);

                            if($examDetail->exam_type == 2 || ($examDetail->exam_type == 1 && $examDetail->self_learning_test_type == 1)){
                                /** Update Student Credit Points via cron job */
                                $this->CronJobController->UpdateStudentCreditPoints($examId, $AttemptExams->{cn::ATTEMPT_EXAMS_STUDENT_STUDENT_ID});
                            }
                        }
                    }
                }
        //     }
        // }
    }
}
