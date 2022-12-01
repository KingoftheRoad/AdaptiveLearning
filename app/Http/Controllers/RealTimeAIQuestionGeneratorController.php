<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;   
use App\Traits\Common;
use App\Models\GradeSchoolMappings;
use App\Models\GradeClassMapping;
use App\Models\ClassPromotionHistory;
use App\Constants\DbConstant As cn;
use Illuminate\Support\Facades\Hash;
use Exception;
use Illuminate\Support\Facades\Validator;
use App\Models\Grades;
use App\Models\Exam;
use App\Models\PreConfigurationDiffiltyLevel;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Traits\ResponseFormat;
use App\Models\StrandUnitsObjectivesMappings;
use App\Models\Question;
use App\Models\TeachersClassSubjectAssign;
use App\Models\Strands;
use App\Models\LearningsUnits;
use App\Models\LearningsObjectives;
use App\Models\ExamConfigurationsDetails;
use App\Http\Services\AIApiService;
use App\Helpers\Helper;
use DB;
use App\Models\ExamSchoolMapping;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\CronJobController;
use App\Models\AttemptExams;
use App\Models\UploadDocuments;

class RealTimeAIQuestionGeneratorController extends Controller
{
    use common, ResponseFormat;
    protected $currentUserSchoolId, $repeated_rate_config, $CronJobController;
    
    public function __construct(){
        $this->AIApiService = new AIApiService();
        $this->CronJobController = new CronJobController;
        
        // Store global variable into current user schhol id
        $this->currentUserSchoolId = null;
        $this->repeated_rate_config = Helper::getGlobalConfiguration('repeated_rate') ?? 0.5 ;
        $this->middleware(function ($request, $next) {
            $this->currentUserSchoolId = Auth::user()->{cn::USERS_SCHOOL_ID_COL};
            return $next($request);
        });
    }

    /**
     * USE : Landing Page on Create Self-Learning Testing Zone
     */
    public function CreateSelfLearningTest(Request $request){
        if($request->isMethod('get')){
            $difficultyLevels = PreConfigurationDiffiltyLevel::all();
            $RequiredQuestionPerSkill = [];
            $RequiredQuestionPerSkill = [
                'minimum_question_per_skill' => $this->getGlobalConfiguration('no_of_questions_per_learning_skills'),
                'maximum_question_per_skill' => $this->getGlobalConfiguration('max_no_question_per_learning_objectives')
            ];
            // Get Strand List
            $strandsList = Strands::all();
            $learningObjectivesConfiguration = array();
            if(!empty($strandsList)){
                $LearningUnits = LearningsUnits::where(cn::LEARNING_UNITS_STRANDID_COL, $strandsList[0]->{cn::STRANDS_ID_COL})->get();
                if(!empty($LearningUnits)){
                    // $LearningObjectives = LearningsObjectives::whereIn(cn::LEARNING_OBJECTIVES_LEARNING_UNITID_COL, $LearningUnits->pluck(cn::LEARNING_OBJECTIVES_ID_COL))->get();
                    $LearningObjectives = LearningsObjectives::IsAvailableQuestion()->whereIn(cn::LEARNING_OBJECTIVES_LEARNING_UNITID_COL, $LearningUnits->pluck(cn::LEARNING_OBJECTIVES_ID_COL))->get();
                }
            }
            return view('backend.student.real_time_generate_question.create_self_learning_test',compact('difficultyLevels','strandsList','LearningUnits','LearningObjectives','RequiredQuestionPerSkill','learningObjectivesConfiguration'));
        }
    }

    /**
     * USE : Generate Question for self-learning Test
     */
    public function GenerateQuestionSelfLearningTest(Request $request){
        if(isset($request)){
            if(!isset($request->learning_unit)){
                return $this->sendError('Please select learning objectives', 404);
            }
            $examLanguage = (isset($request->language)) ? $request->language : 'en';
            $difficultyLevels = PreConfigurationDiffiltyLevel::all();
            $result = array();
            $minimumQuestionPerSkill = Helper::getGlobalConfiguration('no_of_questions_per_learning_skills') ?? 2 ;
            $learningUnitArray = array();
            $coded_questions_list_all = array();
            $coded_questions_list = array();
            $difficulty_lvl = $request->difficulty_lvl;
            $selected_levels = array();
            if(isset($difficulty_lvl) && !empty($difficulty_lvl)){
                foreach($difficulty_lvl as $difficulty_value){
                    $selected_levels[] = $difficulty_value-1;
                }
            }
            $no_of_questions = 10;
            if(isset($request->total_no_of_questions) && !empty($request->total_no_of_questions)){
                $no_of_questions = $request->total_no_of_questions;
            }

            if($request->self_learning_test_type==1){
                $QuestionType = array(2,3);
            }else{
                $QuestionType = array(1);
            }

            $MainSkillArray = array();
            if(isset($request->learning_unit) && !empty($request->learning_unit)){
                foreach($request->learning_unit as $learningUnitId => $learningUnitData){
                    $learningObjectiveQuestionArray = array();
                    if(isset($learningUnitData['learning_objective']) && !empty($learningUnitData['learning_objective'])){
                        foreach($learningUnitData['learning_objective'] as $LearningObjectiveId => $data){
                            $objective_mapping_id = StrandUnitsObjectivesMappings::whereIn('strand_id',$request->strand_id)
                                                        ->where(cn::OBJECTIVES_MAPPINGS_LEARNING_UNIT_ID_COL,$learningUnitId)
                                                        ->where(cn::OBJECTIVES_MAPPINGS_LEARNING_OBJECTIVES_ID_COL,$LearningObjectiveId)
                                                        ->pluck(cn::OBJECTIVES_MAPPINGS_ID_COL)->toArray();
                            $QuestionSkill = Question::with('PreConfigurationDifficultyLevel')
                                                ->whereIn(cn::QUESTION_OBJECTIVE_MAPPING_ID_COL,$objective_mapping_id)
                                                //->where(cn::QUESTION_QUESTION_TYPE_COL,$request->test_type)
                                                ->whereIn(cn::QUESTION_QUESTION_TYPE_COL,$QuestionType)
                                                ->groupBy(cn::QUESTION_E_COL)
                                                ->pluck(cn::QUESTION_E_COL)
                                                ->toArray();
                            if(isset($QuestionSkill) && !empty($QuestionSkill)){
                                $no_of_questions = $data['get_no_of_question_learning_objectives'];
                                $qLoop = 0;
                                $qSize = 0;
                                while($qLoop <= $no_of_questions){
                                    foreach($QuestionSkill as $skillName){
                                        $MainSkillArray[] =  array(
                                            'qloop' => $qLoop,
                                            'learning_unit_id' => $learningUnitId,
                                            'learning_objective_id' => $LearningObjectiveId,
                                            'objective_mapping_ids' => $objective_mapping_id,
                                            'learning_objective_skill' => $skillName
                                        );
                                        $qSize++;
                                    }
                                    if($qSize >= $no_of_questions){
                                        break;
                                    }
                                    $qLoop++;
                                }
                            }
                        }
                    }
                }
            }

            if(isset($MainSkillArray) && !empty($MainSkillArray)){
                foreach($MainSkillArray as $currentSkillArrayId => $skillArray){
                    $questionArray = Question::with('PreConfigurationDifficultyLevel')
                                        ->whereIn(cn::QUESTION_OBJECTIVE_MAPPING_ID_COL,$skillArray['objective_mapping_ids'])
                                        //->where(cn::QUESTION_QUESTION_TYPE_COL,$request->test_type)
                                        ->whereIn(cn::QUESTION_QUESTION_TYPE_COL,$QuestionType)
                                        ->where(cn::QUESTION_E_COL,$skillArray['learning_objective_skill'])
                                        ->get()->toArray();
                    if(!empty($questionArray)){
                        foreach($questionArray as $question_key => $question_value){
                            $countNoOfAnswer = $this->CountNoOfAnswerByQuestionId($question_value['id']);
                            $coded_questions_list[] = array(
                                                        $question_value[cn::QUESTION_NAMING_STRUCTURE_CODE_COL],
                                                        floatval($question_value['pre_configuration_difficulty_level']['title']),
                                                        0,
                                                        $countNoOfAnswer
                                                    );
                        }
                    }
                    if(isset($coded_questions_list) && !empty($coded_questions_list)){
                        $assigned_questions_list = [];
                        $result_list = [];
                        $requestPayload =   new \Illuminate\Http\Request();
                        $studentAbilities = Auth::user()->{cn::USERS_OVERALL_ABILITY_COL} ?? 0;
                        $requestPayload =   $requestPayload->replace([
                                                'initial_ability'           => floatval($studentAbilities),
                                                'assigned_questions_list'   => $assigned_questions_list,
                                                'result_list'               => $result_list,
                                                'coded_questions_list'      => $coded_questions_list,
                                                'repeated_rate'             => $this->repeated_rate_config
                                            ]);
                        $response = $this->AIApiService->Real_Time_Assign_Question_N_Estimate_Ability($requestPayload);
                        if(isset($response) && !empty($response)){
                            $assigned_questions_list[] = $response[0];
                            $responseQuestionCodes = ($response[0][0]);
                            $Question = Question::with(['answers','PreConfigurationDifficultyLevel','objectiveMapping'])
                                            ->where(cn::QUESTION_NAMING_STRUCTURE_CODE_COL,$responseQuestionCodes)
                                            ->first();
                            if(isset($Question) && !empty($Question)){
                                unset($MainSkillArray[$currentSkillArrayId]);
                                $countMainSkillArray = count($MainSkillArray);
                                $encodedMainSkillArray = json_encode($MainSkillArray);
                                $assigned_questions_list = json_encode($assigned_questions_list);
                                $result_list = json_encode($result_list);
                                $QuestionNo = 1;
                                $QuestionResponse['question_html'] = (string)View::make('backend.student.real_time_generate_question.question_html',compact('request','Question','examLanguage','assigned_questions_list','encodedMainSkillArray','result_list','QuestionNo','countMainSkillArray'));
                                return $this->sendResponse($QuestionResponse);exit;
                            }
                        }
                    }
                }
            }
        }
    }

    /**
     * USE : Check student result
     */
    public function CheckStudentAnswerResult($QuestionId,$SelectedAnswer){
        $QuestionAnswerDetail = Question::where(cn::QUESTION_TABLE_ID_COL,$QuestionId)->with('answers')->first();
        if(isset($QuestionAnswerDetail)){
            if($QuestionAnswerDetail->answers->{'correct_answer_en'} == $SelectedAnswer){
                $isAnswer = true;
            }else{
                $isAnswer = false;
            }
        }
        return $isAnswer;
    }

    /**
     * USE : Get Next Question
     */
    public function GenerateQuestionSelfLearningTestNextQuestion(Request $request){
        if(isset($request->encodedMainSkillArray) && !empty($request->encodedMainSkillArray)){
            $examLanguage = (isset($request->language)) ? $request->language : 'en';
            if($request->self_learning_test_type==1){
                $QuestionType = array(2,3);
            }else{
                $QuestionType = array(1);
            }
            $assigned_questions_list = json_decode($request->assigned_questions_list) ?? [];
            $assignedQuestionCodes = array();
            $assignedQuestionCodes = array_column($assigned_questions_list,0);

            $AttemptedQuestionAnswers = json_decode($request->AttemptedQuestionAnswers);
            $AttemptedQuestionResult = $this->CheckStudentAnswerResult($request->currentQuestion,$request->answer);
            $result_list = json_decode($request->result_list);
            $result_list[] = $AttemptedQuestionResult;
            $AttemptedQuestionAnswers[] = array(
                                            'question_id' => $request->currentQuestion,
                                            'answer' => $request->answer,
                                            'answer_result' => $AttemptedQuestionResult,
                                            'language' => 'en',
                                            'duration_second' => $request->current_question_taking_timing
                                        );
            $MainSkillArray = json_decode($request->encodedMainSkillArray);
            if(isset($MainSkillArray) && !empty($MainSkillArray)){                
                foreach($MainSkillArray as $currentSkillArrayId => $skillArray){
                    $questionArray = Question::with('PreConfigurationDifficultyLevel')
                                        ->whereNotIn(cn::QUESTION_NAMING_STRUCTURE_CODE_COL,$assignedQuestionCodes)
                                        ->whereIn(cn::QUESTION_OBJECTIVE_MAPPING_ID_COL,$skillArray->objective_mapping_ids)
                                        //->where(cn::QUESTION_QUESTION_TYPE_COL,$request->test_type)
                                        ->whereIn(cn::QUESTION_QUESTION_TYPE_COL,$QuestionType)
                                        ->where(cn::QUESTION_E_COL,$skillArray->learning_objective_skill)
                                        ->get()->toArray();
                    if(!empty($questionArray)){
                        foreach($questionArray as $question_key => $question_value){
                            $countNoOfAnswer = $this->CountNoOfAnswerByQuestionId($question_value['id']);
                            $coded_questions_list[] = array(
                                                        $question_value[cn::QUESTION_NAMING_STRUCTURE_CODE_COL],
                                                        floatval($question_value['pre_configuration_difficulty_level']['title']),
                                                        0,
                                                        $countNoOfAnswer
                                                    );
                        }
                    }
                    if(isset($coded_questions_list) && !empty($coded_questions_list)){
                        $requestPayload =   new \Illuminate\Http\Request();
                        $studentAbilities = Auth::user()->{cn::USERS_OVERALL_ABILITY_COL} ?? 0;
                        $requestPayload =   $requestPayload->replace([
                                                'initial_ability'           => floatval($studentAbilities),
                                                'assigned_questions_list'   => $assigned_questions_list,
                                                'result_list'               => $result_list,
                                                'coded_questions_list'      => $coded_questions_list,
                                                'repeated_rate'             => $this->repeated_rate_config
                                            ]);
                        $response = $this->AIApiService->Real_Time_Assign_Question_N_Estimate_Ability($requestPayload);
                        if(isset($response) && !empty($response)){
                            $assigned_questions_list[] = $response[0];
                            $responseQuestionCodes = ($response[0][0]);
                            $Question = Question::with(['answers','PreConfigurationDifficultyLevel','objectiveMapping'])
                                            ->where(cn::QUESTION_NAMING_STRUCTURE_CODE_COL,$responseQuestionCodes)
                                            ->first();
                            if(isset($Question) && !empty($Question)){
                                unset($MainSkillArray->$currentSkillArrayId);
                                $countMainSkillArray = count((array)$MainSkillArray);
                                $encodedMainSkillArray = json_encode($MainSkillArray);
                                $assigned_questions_list = json_encode($assigned_questions_list);
                                $result_list = json_encode($result_list);
                                $encodedAttemptedQuestionAnswers = json_encode($AttemptedQuestionAnswers);
                                $QuestionNo = ($request->QuestionNo+1);
                                $QuestionResponse['question_html'] = (string)View::make('backend.student.real_time_generate_question.next_question_html',compact('request','Question','examLanguage','assigned_questions_list','encodedMainSkillArray','result_list','encodedAttemptedQuestionAnswers','QuestionNo','countMainSkillArray'));
                                return $this->sendResponse($QuestionResponse);exit;
                            }
                        }
                    }
                }
            }
        }
    }

    /**
     * USE : Store Self learning detail into database
     */
    public function SaveSelfLearningTest(Request $request){
        $AttemptedQuestionAnswers = json_decode($request->AttemptedQuestionAnswers);
        $AttemptedQuestionResult = $this->CheckStudentAnswerResult($request->currentQuestion,$request->answer);
        $result_list = json_decode($request->result_list);
        $AttemptedQuestionAnswers[] = (object) array(
                                        'question_id' => $request->currentQuestion,
                                        'answer' => $request->answer,
                                        'answer_result' => $AttemptedQuestionResult,
                                        'language' => 'en',
                                        'duration_second' => $request->current_question_taking_timing
                                    );
        
        // Get QuestionIds from attempted questions
        $questionIds = implode(",",array_column($AttemptedQuestionAnswers,'question_id'));
        $timeduration = null;
        if($request->self_learning_test_type == 2){
            $TotalTime = 0;
            $QuestionPerSeconds = $this->getGlobalConfiguration('default_second_per_question');
            if(isset($QuestionPerSeconds) && !empty($QuestionPerSeconds) && !empty($questionIds)){
                $totalSeconds = (count(explode(",",$questionIds)) * $QuestionPerSeconds);
                $TotalTime = gmdate("H:i:s", $totalSeconds);
                $timeduration = ($TotalTime) ? $this->timeToSecond($TotalTime): null;
            }
        }

        $examData = [
            cn::EXAM_TYPE_COLS => 1,
            cn::EXAM_REFERENCE_NO_COL => $this->GetMaxReferenceNumberExam(1,$request->self_learning_test_type),
            cn::EXAM_TABLE_TITLE_COLS => $this->createTestTitle(),
            cn::EXAM_TABLE_FROM_DATE_COLS => Carbon::now(),
            cn::EXAM_TABLE_TO_DATE_COLS => Carbon::now(),
            cn::EXAM_TABLE_RESULT_DATE_COLS => Carbon::now(),
            cn::EXAM_TABLE_PUBLISH_DATE_COL => Carbon::now(),
            cn::EXAM_TABLE_TIME_DURATIONS_COLS => $timeduration,
            cn::EXAM_TABLE_QUESTION_IDS_COL => ($questionIds) ?  $questionIds : null,
            cn::EXAM_TABLE_STUDENT_IDS_COL => $this->LoggedUserId(),
            cn::EXAM_TABLE_SCHOOL_COLS => Auth::user()->{cn::USERS_SCHOOL_ID_COL},
            cn::EXAM_TABLE_IS_UNLIMITED => ($request->self_learning_test_type == 1) ? 1 : 0,
            cn::EXAM_TABLE_SELF_LEARNING_TEST_TYPE_COL => $request->self_learning_test_type,
            cn::EXAM_TABLE_CREATED_BY_COL => $this->LoggedUserId(),
            'created_by_user' => 'student',
            cn::EXAM_TABLE_STATUS_COLS => 'publish'
        ];
        $exams = Exam::create($examData);
        if($exams){
            // Create exam school mapping
            ExamSchoolMapping::create(['school_id' => Auth::user()->{cn::USERS_SCHOOL_ID_COL},'exam_id' => $exams->id, 'status' => 'publish']);

            // find student overall ability from AI-API
            if(isset($AttemptedQuestionAnswers) && !empty($AttemptedQuestionAnswers)){
                $NoOfCorrectAnswers = 0;
                $NoOfWrongAnswers = 0;
                foreach ($AttemptedQuestionAnswers as $key => $Question) {
                    $QuestionId = $Question->question_id;
                    $answer = $Question->answer;
                    if($Question->answer_result){
                        $NoOfCorrectAnswers = ($NoOfCorrectAnswers + 1);
                        $apiData['questions_results'][] = true;
                    }else{
                        $NoOfWrongAnswers = ($NoOfWrongAnswers + 1);
                        $apiData['questions_results'][] = false;
                    }

                    // Get Questions Answers and difficulty level
                    $responseData = $this->GetQuestionNumOfAnswerAndDifficultyValue($Question->question_id);
                    $apiData['num_of_ans_list'][] = $responseData['noOfAnswers'];
                    $apiData['difficulty_list'][] = $responseData['difficulty_value'];
                    $apiData['max_student_num'] = 1;

                }
            }

            $StudentAbility = '';
            if(!empty($apiData)){
                // Get the student ability from calling AIApi
                $StudentAbility = $this->GetAIStudentAbility($apiData);
            }

            $PostData = [
                cn::ATTEMPT_EXAMS_EXAM_ID => $exams->id,
                cn::ATTEMPT_EXAMS_STUDENT_STUDENT_ID => $this->LoggedUserId(),
                cn::ATTEMPT_EXAMS_STUDENT_GRADE_ID => Auth::user()->grade_id,
                cn::ATTEMPT_EXAMS_STUDENT_CLASS_ID => Auth::user()->class_id,
                cn::ATTEMPT_EXAMS_LANGUAGE_COL => 'en',
                cn::ATTEMPT_EXAMS_QUESTION_ANSWER_COL => (!empty($AttemptedQuestionAnswers)) ? json_encode($AttemptedQuestionAnswers) : null,
                cn::ATTEMPT_EXAMS_WRONG_ANSWER_COL => '',
                //cn::ATTEMPT_EXAMS_ATTEMPT_FIRST_TRIAL_COL => $request->attempt_first_trial_data_new,
                //cn::ATTEMPT_EXAMS_ATTEMPT_SECOND_TRIAL_COL => $wrong_ans_list_json,
                cn::ATTEMPT_EXAMS_TOTAL_CORRECT_ANSWERS => $NoOfCorrectAnswers,
                cn::ATTEMPT_EXAMS_TOTAL_WRONG_ANSWERS => $NoOfWrongAnswers,
                cn::ATTEMPT_EXAMS_EXAM_TAKING_TIMING => $request->exam_taking_timing,
                cn::ATTEMPT_EXAMS_STUDENT_ABILITY_COL => ($StudentAbility!='') ? $StudentAbility : null,
                cn::ATTEMPT_EXAMS_SERVER_DETAILS_COL => json_encode($this->serverData()) ?? null
            ];
            $save = AttemptExams::create($PostData);
            if($save){
                //Update Column Is_my_teaching_sync
                Exam::find($exams->id)->update([cn::EXAM_TABLE_IS_TEACHING_REPORT_SYNC =>'true']);
                
                /** Start Update overall ability for the student **/
                $this->CronJobController->UpdateStudentOverAllAbility();

                /** Update My Teaching Table Via Cron Job */
                $this->CronJobController->UpdateMyTeachingTable(Auth::user()->{cn::USERS_SCHOOL_ID_COL}, $exams->id);

                /** Update Student Credit Points via cron job */
                $this->CronJobController->UpdateStudentCreditPoints($exams->id, Auth::user()->id);
                
                /** End Update overall ability for the student **/
                $this->StoreAuditLogFunction('','Exams','','','Attempt Exam',cn::EXAM_TABLE_NAME,'');

                $response['redirectUrl'] = 'exams/result/'.$exams->id.'/'.Auth::user()->{cn::USERS_ID_COL};
                return $this->sendResponse($response);exit;
                //return $response;
            }
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

    /**
     * USE : Get Next Question
     */
    public function GenerateQuestionSelfLearningTestChangeLanguage(Request $request){
        if(isset($request->encodedMainSkillArray) && !empty($request->encodedMainSkillArray)){
            $examLanguage = (isset($request->language)) ? $request->language : 'en';
            if($request->self_learning_test_type==1){
                $QuestionType = array(2,3);
            }else{
                $QuestionType = array(1);
            }

            $AttemptedQuestionAnswers = json_decode($request->AttemptedQuestionAnswers);
            $result_list = json_decode($request->result_list);
            $MainSkillArray = json_decode($request->encodedMainSkillArray);
            $Question = Question::with(['answers','PreConfigurationDifficultyLevel','objectiveMapping'])
                            ->where('id',$request->currentQuestion)
                            ->first();
            if(isset($Question) && !empty($Question)){
                $countMainSkillArray = count((array)$MainSkillArray);
                $encodedMainSkillArray = json_encode($MainSkillArray);
                $assigned_questions_list = ($request->assigned_questions_list)? $request->assigned_questions_list : [];
                $result_list = json_encode($result_list);
                $encodedAttemptedQuestionAnswers = json_encode($AttemptedQuestionAnswers);
                $QuestionNo = ($request->QuestionNo);
                $QuestionResponse['question_html'] = (string)View::make('backend.student.real_time_generate_question.next_question_html',compact('request','Question','examLanguage','assigned_questions_list','encodedMainSkillArray','result_list','encodedAttemptedQuestionAnswers','QuestionNo','countMainSkillArray'));
                return $this->sendResponse($QuestionResponse);exit;
            }
        }
    }

    /**
     * USE : Landing Page on Create Self-Learning Exercise
     */
    public function CreateSelfLearningExercise(Request $request){
        $difficultyLevels = PreConfigurationDiffiltyLevel::all();
        $RequiredQuestionPerSkill = [];
        $RequiredQuestionPerSkill = [
            'minimum_question_per_skill' => $this->getGlobalConfiguration('no_of_questions_per_learning_skills'),
            'maximum_question_per_skill' => $this->getGlobalConfiguration('max_no_question_per_learning_objectives')
        ];
        // Get Strand List
        $strandsList = Strands::all();
        $learningObjectivesConfiguration = array();
        if(!empty($strandsList)){
            $LearningUnits = LearningsUnits::where(cn::LEARNING_UNITS_STRANDID_COL, $strandsList[0]->{cn::STRANDS_ID_COL})->get();
            if(!empty($LearningUnits)){
                // $LearningObjectives = LearningsObjectives::whereIn(cn::LEARNING_OBJECTIVES_LEARNING_UNITID_COL, $LearningUnits->pluck(cn::LEARNING_OBJECTIVES_ID_COL))->get();
                $LearningObjectives = LearningsObjectives::IsAvailableQuestion()->whereIn(cn::LEARNING_OBJECTIVES_LEARNING_UNITID_COL, $LearningUnits->pluck(cn::LEARNING_OBJECTIVES_ID_COL))->get();
            }
        }
        return view('backend.student.self_learning.create_self_learning_exercise',compact('difficultyLevels','strandsList','LearningUnits','LearningObjectives','RequiredQuestionPerSkill','learningObjectivesConfiguration'));
    }

    /**
     * USE : Generate Question for self-learning exercise
     */
    public function GenerateQuestionSelfLearningExercise(Request $request){
        if(isset($request)){
            if(!isset($request->learning_unit)){
                return $this->sendError('Please select learning objectives', 404);
            }
            $examLanguage = (isset($request->language)) ? $request->language : 'en';
            $difficultyLevels = PreConfigurationDiffiltyLevel::all();
            $result = array();
            $minimumQuestionPerSkill = Helper::getGlobalConfiguration('no_of_questions_per_learning_skills') ?? 2 ;
            $coded_questions_list = array();            
            $no_of_questions = 10;
            if(isset($request->total_no_of_questions) && !empty($request->total_no_of_questions)){
                $no_of_questions = $request->total_no_of_questions;
            }

            if($request->self_learning_test_type==1){
                $QuestionType = array(2,3);
            }else{
                $QuestionType = array(1);
            }

            $MainSkillArray = array();
            if(isset($request->learning_unit) && !empty($request->learning_unit)){
                foreach($request->learning_unit as $learningUnitId => $learningUnitData){
                    $learningObjectiveQuestionArray = array();
                    if(isset($learningUnitData['learning_objective']) && !empty($learningUnitData['learning_objective'])){
                        foreach($learningUnitData['learning_objective'] as $LearningObjectiveId => $data){
                            $objective_mapping_id = StrandUnitsObjectivesMappings::whereIn('strand_id',$request->strand_id)
                                                        ->where(cn::OBJECTIVES_MAPPINGS_LEARNING_UNIT_ID_COL,$learningUnitId)
                                                        ->where(cn::OBJECTIVES_MAPPINGS_LEARNING_OBJECTIVES_ID_COL,$LearningObjectiveId)
                                                        ->pluck(cn::OBJECTIVES_MAPPINGS_ID_COL)->toArray();
                            $QuestionSkill = Question::with('PreConfigurationDifficultyLevel')
                                                ->whereIn(cn::QUESTION_OBJECTIVE_MAPPING_ID_COL,$objective_mapping_id)
                                                //->where(cn::QUESTION_QUESTION_TYPE_COL,$request->test_type)
                                                ->whereIn(cn::QUESTION_QUESTION_TYPE_COL,$QuestionType)
                                                ->groupBy(cn::QUESTION_E_COL)
                                                ->pluck(cn::QUESTION_E_COL)
                                                ->toArray();
                            if(isset($QuestionSkill) && !empty($QuestionSkill)){
                                $no_of_questions = $data['get_no_of_question_learning_objectives'];
                                $qLoop = 0;
                                $qSize = 0;
                                while($qLoop <= $no_of_questions){
                                    foreach($QuestionSkill as $skillName){
                                        $MainSkillArray[] =  array(
                                            'qloop' => $qLoop,
                                            'learning_unit_id' => $learningUnitId,
                                            'learning_objective_id' => $LearningObjectiveId,
                                            'objective_mapping_ids' => $objective_mapping_id,
                                            'learning_objective_skill' => $skillName
                                        );
                                        $qSize++;
                                    }
                                    if($qSize >= $no_of_questions){
                                        break;
                                    }
                                    $qLoop++;
                                }
                            }
                        }
                    }
                }
            }

            if(isset($MainSkillArray) && !empty($MainSkillArray)){
                foreach($MainSkillArray as $currentSkillArrayId => $skillArray){
                    $questionArray = Question::with('PreConfigurationDifficultyLevel')
                                        ->whereIn(cn::QUESTION_OBJECTIVE_MAPPING_ID_COL,$skillArray['objective_mapping_ids'])
                                        //->where(cn::QUESTION_QUESTION_TYPE_COL,$request->test_type)
                                        ->whereIn(cn::QUESTION_QUESTION_TYPE_COL,$QuestionType)
                                        ->where(cn::QUESTION_E_COL,$skillArray['learning_objective_skill'])
                                        ->get()->toArray();
                    if(!empty($questionArray)){
                        foreach($questionArray as $question_key => $question_value){
                            $countNoOfAnswer = $this->CountNoOfAnswerByQuestionId($question_value['id']);
                            $coded_questions_list[] = array(
                                                        $question_value[cn::QUESTION_NAMING_STRUCTURE_CODE_COL],
                                                        floatval($question_value['pre_configuration_difficulty_level']['title']),
                                                        0,
                                                        $countNoOfAnswer
                                                    );
                        }
                    }
                    if(isset($coded_questions_list) && !empty($coded_questions_list)){
                        $assigned_questions_list = [];
                        $result_list = [];
                        $requestPayload =   new \Illuminate\Http\Request();
                        $studentAbilities = Auth::user()->{cn::USERS_OVERALL_ABILITY_COL} ?? 0;
                        $requestPayload =   $requestPayload->replace([
                                                'initial_ability'           => floatval($studentAbilities),
                                                'assigned_questions_list'   => $assigned_questions_list,
                                                'result_list'               => $result_list,
                                                'coded_questions_list'      => $coded_questions_list,
                                                'repeated_rate'             => $this->repeated_rate_config
                                            ]);
                        $response = $this->AIApiService->Real_Time_Assign_Question_N_Estimate_Ability($requestPayload);
                        if(isset($response) && !empty($response)){
                            $assigned_questions_list[] = $response[0];
                            $responseQuestionCodes = ($response[0][0]);
                            $Question = Question::with(['answers','PreConfigurationDifficultyLevel','objectiveMapping'])
                                            ->where(cn::QUESTION_NAMING_STRUCTURE_CODE_COL,$responseQuestionCodes)
                                            ->first();
                            if(isset($Question) && !empty($Question)){
                                unset($MainSkillArray[$currentSkillArrayId]);
                                $countMainSkillArray = count($MainSkillArray);
                                $encodedMainSkillArray = json_encode($MainSkillArray);
                                $assigned_questions_list = json_encode($assigned_questions_list);
                                $result_list = json_encode($result_list);
                                $QuestionNo = 1;

                                // Get General Hints current question
                                $UploadDocumentsData = $this->GetGeneralHintsData($Question->id,$examLanguage);

                                $QuestionResponse['question_html'] = (string)View::make('backend.student.real_time_generate_question.self_learning_exercise.question_html',compact('request','Question','examLanguage','assigned_questions_list','encodedMainSkillArray','result_list','QuestionNo','countMainSkillArray','UploadDocumentsData'));
                                return $this->sendResponse($QuestionResponse);exit;
                            }
                        }
                    }
                }
            }
        }
    }

    /**
     * USE : Get Next Question
     */
    public function GenerateQuestionSelfLearningExerciseNextQuestion(Request $request){
        if(isset($request->encodedMainSkillArray) && !empty($request->encodedMainSkillArray)){
            $examLanguage = (isset($request->language)) ? $request->language : 'en';
            if($request->self_learning_test_type==1){
                $QuestionType = array(2,3);
            }else{
                $QuestionType = array(1);
            }
            $assigned_questions_list = json_decode($request->assigned_questions_list) ?? [];
            $assignedQuestionCodes = array();
            $assignedQuestionCodes = array_column($assigned_questions_list,0);

            $AttemptedQuestionAnswers = json_decode($request->AttemptedQuestionAnswers);
            $AttemptedQuestionResult = $this->CheckStudentAnswerResult($request->currentQuestion,$request->answer);
            $result_list = json_decode($request->result_list);
            $result_list[] = $AttemptedQuestionResult;
            $AttemptedQuestionAnswers[] = array(
                                            'question_id' => $request->currentQuestion,
                                            'answer' => $request->answer,
                                            'answer_result' => $AttemptedQuestionResult,
                                            'language' => 'en',
                                            'duration_second' => $request->current_question_taking_timing
                                        );
            $MainSkillArray = json_decode($request->encodedMainSkillArray);
            if(isset($MainSkillArray) && !empty($MainSkillArray)){                
                foreach($MainSkillArray as $currentSkillArrayId => $skillArray){
                    $questionArray = Question::with('PreConfigurationDifficultyLevel')
                                        ->whereNotIn(cn::QUESTION_NAMING_STRUCTURE_CODE_COL,$assignedQuestionCodes)
                                        ->whereIn(cn::QUESTION_OBJECTIVE_MAPPING_ID_COL,$skillArray->objective_mapping_ids)
                                        //->where(cn::QUESTION_QUESTION_TYPE_COL,$request->test_type)
                                        ->whereIn(cn::QUESTION_QUESTION_TYPE_COL,$QuestionType)
                                        ->where(cn::QUESTION_E_COL,$skillArray->learning_objective_skill)
                                        ->get()->toArray();
                    if(!empty($questionArray)){
                        foreach($questionArray as $question_key => $question_value){
                            $countNoOfAnswer = $this->CountNoOfAnswerByQuestionId($question_value['id']);
                            $coded_questions_list[] = array(
                                                        $question_value[cn::QUESTION_NAMING_STRUCTURE_CODE_COL],
                                                        floatval($question_value['pre_configuration_difficulty_level']['title']),
                                                        0,
                                                        $countNoOfAnswer
                                                    );
                        }
                    }
                    if(isset($coded_questions_list) && !empty($coded_questions_list)){
                        $requestPayload =   new \Illuminate\Http\Request();
                        $studentAbilities = Auth::user()->{cn::USERS_OVERALL_ABILITY_COL} ?? 0;
                        $requestPayload =   $requestPayload->replace([
                                                'initial_ability'           => floatval($studentAbilities),
                                                'assigned_questions_list'   => $assigned_questions_list,
                                                'result_list'               => $result_list,
                                                'coded_questions_list'      => $coded_questions_list,
                                                'repeated_rate'             => $this->repeated_rate_config
                                            ]);
                        $response = $this->AIApiService->Real_Time_Assign_Question_N_Estimate_Ability($requestPayload);
                        if(isset($response) && !empty($response)){
                            $assigned_questions_list[] = $response[0];
                            $responseQuestionCodes = ($response[0][0]);
                            $Question = Question::with(['answers','PreConfigurationDifficultyLevel','objectiveMapping'])
                                            ->where(cn::QUESTION_NAMING_STRUCTURE_CODE_COL,$responseQuestionCodes)
                                            ->first();
                            if(isset($Question) && !empty($Question)){
                                unset($MainSkillArray->$currentSkillArrayId);
                                $countMainSkillArray = count((array)$MainSkillArray);
                                $encodedMainSkillArray = json_encode($MainSkillArray);
                                $assigned_questions_list = json_encode($assigned_questions_list);
                                $result_list = json_encode($result_list);
                                $encodedAttemptedQuestionAnswers = json_encode($AttemptedQuestionAnswers);

                                // Get General Hints current question
                                $UploadDocumentsData = $this->GetGeneralHintsData($Question->id,$examLanguage);

                                $QuestionNo = ($request->QuestionNo+1);
                                $QuestionResponse['question_html'] = (string)View::make('backend.student.real_time_generate_question.self_learning_exercise.next_question_html',compact('request','Question','examLanguage','assigned_questions_list','encodedMainSkillArray','result_list','encodedAttemptedQuestionAnswers','QuestionNo','countMainSkillArray','UploadDocumentsData'));
                                return $this->sendResponse($QuestionResponse);exit;
                            }
                        }
                    }
                }
            }
        }
    }

    /**
     * USE : Store Self learning detail into database
     */
    public function SaveSelfLearningExercise(Request $request){
        $AttemptedQuestionAnswers = json_decode($request->AttemptedQuestionAnswers);
        $AttemptedQuestionResult = $this->CheckStudentAnswerResult($request->currentQuestion,$request->answer);
        $result_list = json_decode($request->result_list);
        $AttemptedQuestionAnswers[] = (object) array(
                                        'question_id' => $request->currentQuestion,
                                        'answer' => $request->answer,
                                        'answer_result' => $AttemptedQuestionResult,
                                        'language' => 'en',
                                        'duration_second' => $request->current_question_taking_timing
                                    );
        
        // Get QuestionIds from attempted questions
        $questionIds = implode(",",array_column($AttemptedQuestionAnswers,'question_id'));
        $timeduration = null;
        if($request->self_learning_test_type == 2){
            $TotalTime = 0;
            $QuestionPerSeconds = $this->getGlobalConfiguration('default_second_per_question');
            if(isset($QuestionPerSeconds) && !empty($QuestionPerSeconds) && !empty($questionIds)){
                $totalSeconds = (count(explode(",",$questionIds)) * $QuestionPerSeconds);
                $TotalTime = gmdate("H:i:s", $totalSeconds);
                $timeduration = ($TotalTime) ? $this->timeToSecond($TotalTime): null;
            }
        }

        $examData = [
            cn::EXAM_TYPE_COLS => 1,
            
            cn::EXAM_TABLE_TITLE_COLS => $this->createTestTitle(),
            cn::EXAM_TABLE_FROM_DATE_COLS => Carbon::now(),
            cn::EXAM_TABLE_TO_DATE_COLS => Carbon::now(),
            cn::EXAM_TABLE_RESULT_DATE_COLS => Carbon::now(),
            cn::EXAM_TABLE_PUBLISH_DATE_COL => Carbon::now(),
            cn::EXAM_TABLE_TIME_DURATIONS_COLS => $timeduration,
            cn::EXAM_TABLE_QUESTION_IDS_COL => ($questionIds) ?  $questionIds : null,
            cn::EXAM_TABLE_STUDENT_IDS_COL => $this->LoggedUserId(),
            cn::EXAM_TABLE_SCHOOL_COLS => Auth::user()->{cn::USERS_SCHOOL_ID_COL},
            cn::EXAM_TABLE_IS_UNLIMITED => ($request->self_learning_test_type == 1) ? 1 : 0,
            cn::EXAM_TABLE_SELF_LEARNING_TEST_TYPE_COL => $request->self_learning_test_type,
            cn::EXAM_TABLE_CREATED_BY_COL => $this->LoggedUserId(),
            'created_by_user' => 'student',
            cn::EXAM_TABLE_STATUS_COLS => 'publish'
        ];
        $exams = Exam::create($examData);
        if($exams){
            // Create exam school mapping
            ExamSchoolMapping::create(['school_id' => Auth::user()->{cn::USERS_SCHOOL_ID_COL},'exam_id' => $exams->id, 'status' => 'publish']);

            // find student overall ability from AI-API
            if(isset($AttemptedQuestionAnswers) && !empty($AttemptedQuestionAnswers)){
                $NoOfCorrectAnswers = 0;
                $NoOfWrongAnswers = 0;
                foreach ($AttemptedQuestionAnswers as $key => $Question) {
                    $QuestionId = $Question->question_id;
                    $answer = $Question->answer;
                    if($Question->answer_result){
                        $NoOfCorrectAnswers = ($NoOfCorrectAnswers + 1);
                        $apiData['questions_results'][] = true;
                    }else{
                        $NoOfWrongAnswers = ($NoOfWrongAnswers + 1);
                        $apiData['questions_results'][] = false;
                    }

                    // Get Questions Answers and difficulty level
                    $responseData = $this->GetQuestionNumOfAnswerAndDifficultyValue($Question->question_id);
                    $apiData['num_of_ans_list'][] = $responseData['noOfAnswers'];
                    $apiData['difficulty_list'][] = $responseData['difficulty_value'];
                    $apiData['max_student_num'] = 1;

                }
            }

            $StudentAbility = '';
            if(!empty($apiData)){
                // Get the student ability from calling AIApi
                $StudentAbility = $this->GetAIStudentAbility($apiData);
            }

            $PostData = [
                cn::ATTEMPT_EXAMS_EXAM_ID => $exams->id,
                cn::ATTEMPT_EXAMS_STUDENT_STUDENT_ID => $this->LoggedUserId(),
                cn::ATTEMPT_EXAMS_STUDENT_GRADE_ID => Auth::user()->grade_id,
                cn::ATTEMPT_EXAMS_STUDENT_CLASS_ID => Auth::user()->class_id,
                cn::ATTEMPT_EXAMS_LANGUAGE_COL => 'en',
                cn::ATTEMPT_EXAMS_QUESTION_ANSWER_COL => (!empty($AttemptedQuestionAnswers)) ? json_encode($AttemptedQuestionAnswers) : null,
                cn::ATTEMPT_EXAMS_WRONG_ANSWER_COL => '',
                //cn::ATTEMPT_EXAMS_ATTEMPT_FIRST_TRIAL_COL => $request->attempt_first_trial_data_new,
                //cn::ATTEMPT_EXAMS_ATTEMPT_SECOND_TRIAL_COL => $wrong_ans_list_json,
                cn::ATTEMPT_EXAMS_TOTAL_CORRECT_ANSWERS => $NoOfCorrectAnswers,
                cn::ATTEMPT_EXAMS_TOTAL_WRONG_ANSWERS => $NoOfWrongAnswers,
                cn::ATTEMPT_EXAMS_EXAM_TAKING_TIMING => $request->exam_taking_timing,
                cn::ATTEMPT_EXAMS_STUDENT_ABILITY_COL => ($StudentAbility!='') ? $StudentAbility : null,
                cn::ATTEMPT_EXAMS_SERVER_DETAILS_COL => json_encode($this->serverData()) ?? null
            ];
            $save = AttemptExams::create($PostData);
            if($save){
                //Update Column Is_my_teaching_sync
                Exam::find($exams->id)->update([cn::EXAM_TABLE_IS_TEACHING_REPORT_SYNC =>'true']);
                
                /** Start Update overall ability for the student **/
                $this->CronJobController->UpdateStudentOverAllAbility();

                /** Update My Teaching Table Via Cron Job */
                $this->CronJobController->UpdateMyTeachingTable(Auth::user()->{cn::USERS_SCHOOL_ID_COL}, $exams->id);

                /** Update Student Credit Points via cron job */
                $this->CronJobController->UpdateStudentCreditPoints($exams->id, Auth::user()->id);
                
                /** End Update overall ability for the student **/
                $this->StoreAuditLogFunction('','Exams','','','Attempt Exam',cn::EXAM_TABLE_NAME,'');

                $response['redirectUrl'] = 'exams/result/'.$exams->id.'/'.Auth::user()->{cn::USERS_ID_COL};
                return $this->sendResponse($response);exit;
            }
        }
    }

    /**
     * USE : Get Next Question
     */
    public function GenerateQuestionSelfLearningExerciseChangeLanguage(Request $request){
        if(isset($request->encodedMainSkillArray) && !empty($request->encodedMainSkillArray)){
            $examLanguage = (isset($request->language)) ? $request->language : 'en';
            if($request->self_learning_test_type==1){
                $QuestionType = array(2,3);
            }else{
                $QuestionType = array(1);
            }

            $AttemptedQuestionAnswers = json_decode($request->AttemptedQuestionAnswers);
            $result_list = json_decode($request->result_list);
            $MainSkillArray = json_decode($request->encodedMainSkillArray);
            $Question = Question::with(['answers','PreConfigurationDifficultyLevel','objectiveMapping'])
                            ->where('id',$request->currentQuestion)
                            ->first();
            if(isset($Question) && !empty($Question)){
                $countMainSkillArray = count((array)$MainSkillArray);
                $encodedMainSkillArray = json_encode($MainSkillArray);
                $assigned_questions_list = ($request->assigned_questions_list)? $request->assigned_questions_list : [];
                $result_list = json_encode($result_list);
                $encodedAttemptedQuestionAnswers = json_encode($AttemptedQuestionAnswers);
                $QuestionNo = ($request->QuestionNo);

                // Get General Hints current question
                $UploadDocumentsData = $this->GetGeneralHintsData($Question->id,$examLanguage);

                $QuestionResponse['question_html'] = (string)View::make('backend.student.real_time_generate_question.self_learning_exercise.next_question_html',compact('request','Question','examLanguage','assigned_questions_list','encodedMainSkillArray','result_list','encodedAttemptedQuestionAnswers','QuestionNo','countMainSkillArray','UploadDocumentsData'));
                return $this->sendResponse($QuestionResponse);exit;
            }
        }
    }

    /**
     * USE : Get general Hints data by question
     */
    public function GetGeneralHintsData($QuestionId,$language){
        $UploadDocumentsData = array();
        $Question = Question::find($QuestionId);
        if($language == 'en'){
            if(isset($Question->{cn::QUESTION_GENERAL_HINTS_VIDEO_ID_EN}) && $Question->{cn::QUESTION_GENERAL_HINTS_VIDEO_ID_EN}!=""){
                $UploadDocumentsData = UploadDocuments::find($Question->{cn::QUESTION_GENERAL_HINTS_VIDEO_ID_EN});
            }else{
                $arrayOfQuestion = explode('-',$Question->{cn::QUESTION_QUESTION_CODE_COL});
                if(count($arrayOfQuestion) == 8){
                    unset($arrayOfQuestion[count($arrayOfQuestion)-1]);
                    $newQuestionCode = implode('-',$arrayOfQuestion);
                    $newQuestionData = Question::where(cn::QUESTION_QUESTION_CODE_COL,$newQuestionCode)->first();
                    if(isset($newQuestionData->{cn::QUESTION_GENERAL_HINTS_VIDEO_ID_EN}) && !empty($newQuestionData->{cn::QUESTION_GENERAL_HINTS_VIDEO_ID_EN})){
                        $UploadDocumentsData = UploadDocuments::find($newQuestionData->{cn::QUESTION_GENERAL_HINTS_VIDEO_ID_EN});
                    }
                }
            }
        }else{
            if(isset($Question->{cn::QUESTION_GENERAL_HINTS_VIDEO_ID_CH}) && $Question->{cn::QUESTION_GENERAL_HINTS_VIDEO_ID_CH}!=""){
                $UploadDocumentsData = UploadDocuments::find($Question->{cn::QUESTION_GENERAL_HINTS_VIDEO_ID_CH});
            }else{
                $arrayOfQuestion = explode('-',$Question->{cn::QUESTION_QUESTION_CODE_COL});
                if(count($arrayOfQuestion) == 8){
                    unset($arrayOfQuestion[count($arrayOfQuestion)-1]);
                    $newQuestionCode = implode('-',$arrayOfQuestion);
                    $newQuestionData = Question::where(cn::QUESTION_QUESTION_CODE_COL,$newQuestionCode)->first();
                    if(isset($newQuestionData->{cn::QUESTION_GENERAL_HINTS_VIDEO_ID_CH}) && !empty($newQuestionData->{cn::QUESTION_GENERAL_HINTS_VIDEO_ID_CH})){
                        $UploadDocumentsData = UploadDocuments::find($newQuestionData->{cn::QUESTION_GENERAL_HINTS_VIDEO_ID_CH});
                    }
                }
            }
        }
        return $UploadDocumentsData;
    }
}