<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Exam;
use App\Models\AttemptExams;
use App\Constants\DbConstant As cn;
use App\Http\Services\AIApiService;
use App\Traits\ResponseFormat;
use App\Traits\Common;
use App\Helpers\Helper;
use App\Models\PreConfigurationDiffiltyLevel;
use App\Models\TeachersClassSubjectAssign;
use App\Models\Question;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AlpAiGraphController extends Controller
{
    use Common, ResponseFormat;

    protected $AIApiService;

    public function __construct(){
        $this->AIApiService = new AIApiService();
    }

    /**
     * USE : Class performance report for single student result
     */
    public function getPerformanceGraphCurrentStudent(Request $request){
        try {
            $response = [];
            $performanceResult = [];
            $PreConfigurationDifficultyLevel = array();
            // $PreConfigurationDifficultyLevelData = PreConfigurationDiffiltyLevel::get()->toArray();
            $PreConfigurationDifficultyLevelData = PreConfigurationDiffiltyLevel::where(cn::PRE_CONFIGURE_DIFFICULTY_CURRICULUM_YEAR_ID_COL,$this->GetCurriculumYear())->get()->toArray();
            if(isset($PreConfigurationDifficultyLevelData)){
                $PreConfigurationDifficultyLevel = array_column($PreConfigurationDifficultyLevelData,cn::PRE_CONFIGURE_DIFFICULTY_TITLE_COL,cn::PRE_CONFIGURE_DIFFICULTY_DIFFICULTY_LEVEL_COL);
            }
            if(!empty($request->student_id) && !empty($request->exam_id)){
                $AttemptExamData =  AttemptExams::where([
                                        cn::ATTEMPT_EXAMS_EXAM_ID => $request->exam_id,
                                        cn::ATTEMPT_EXAMS_STUDENT_STUDENT_ID => $request->student_id
                                    ])
                                    ->first();
                if(isset($AttemptExamData) && !empty($AttemptExamData)){
                    $performanceResult['student_ability'] = $AttemptExamData->student_ability;
                }
                $ExamData = Exam::find($request->exam_id);
                if(isset($ExamData) && !empty($ExamData)){
                    if(!empty($ExamData->question_ids)){
                        $questionIds = explode(',',$ExamData->question_ids);
                        $QuestionList = Question::with('answers')->whereIn(cn::QUESTION_TABLE_ID_COL,$questionIds)->get();
                        if(isset($QuestionList) && !empty($QuestionList)){
                            foreach($QuestionList as $QuestionKey => $question){
                                $countQuestions = count($QuestionList);
                                $AnswerDetail = $question->answers;
                                if(isset($AttemptExamData['question_answers'])){
                                    $filterAttemptQuestionAnswer = array_filter(json_decode($AttemptExamData['question_answers']), function ($var) use($question){
                                        if($var->question_id == $question['id']){
                                            return $var ?? [];
                                        }
                                    });
                                }
                                if(isset($filterAttemptQuestionAnswer) && !empty($filterAttemptQuestionAnswer)){
                                    foreach($filterAttemptQuestionAnswer as $fanswer){
                                        // Save the question result
                                        if($fanswer->answer == $AnswerDetail->{'correct_answer_'.$fanswer->language}){
                                            $performanceResult['student_results_list'][] = true;
                                        }else{
                                            $performanceResult['student_results_list'][] = false;
                                        }

                                        // Get Questions difficulty Level value
                                        if(isset($PreConfigurationDifficultyLevel) && !empty($PreConfigurationDifficultyLevel) && isset($PreConfigurationDifficultyLevel[$question->{cn::QUESTION_DIFFICULTY_LEVEL_COL}]) && !empty($PreConfigurationDifficultyLevel[$question->{cn::QUESTION_DIFFICULTY_LEVEL_COL}])){
                                            $performanceResult['questions_difficulties_list'][] = number_format($PreConfigurationDifficultyLevel[$question->{cn::QUESTION_DIFFICULTY_LEVEL_COL}], 4, '.', '');
                                        }else{
                                            $performanceResult['questions_difficulties_list'][] = 0;
                                        }
                                    }
                                }
                            }
                        }
                    }
                }

                if(isset($performanceResult) && !empty($performanceResult) && !empty($performanceResult['student_results_list']) && !empty($performanceResult['questions_difficulties_list'])){
                    $requestPayload = new Request();
                    $requestPayload = $requestPayload->replace([
                        'student_ability'               => (float)$performanceResult['student_ability'],
                        'student_results_list'          => $performanceResult['student_results_list'],
                        'questions_difficulties_list'   => array_map('floatval', $performanceResult['questions_difficulties_list']),
                        'labels'                        => $this->GetAiApiLabels(config()->get('aiapi.api.Plot_Analyze_Student.uri')),
                        'format'                        => 'base64'
                    ]);
                    // Call to AI API from AI Server
                    $response = $this->AIApiService->getPloatAnalayzeStudent($requestPayload);
                }
                // Return result for performance graph
                return $this->sendResponse($response);
            }
        } catch (\Exception $ex) {
            return $this->sendError($ex->getMessage(), 404);
        }
    }

    /**
     * USE : In the teacher panel -> "My Teaching" Module click on particular exam wise check Performance analyze graph
     */
    public function getClassAbilityAnalysisReport(Request $request){
        try {
            $isGroup = (isset($request->isGroup) && !empty($request->isGroup)) ? true : false;
            $response = [];
            $studentAbility = [];
            $studentidlist = array();
            $schoolId = Auth::user()->{cn::USERS_SCHOOL_ID_COL};
            if(isset($request->examid)){
                $studentIds = explode(',',$request->studentIds);
                $ExamsIds = explode(',',$request->examid);
                $gradeClassId = array();

                if($this->isTeacherLogin()){
                    $gradesListId = TeachersClassSubjectAssign::where([
                                        cn::TEACHER_CLASS_SUBJECT_CURRICULUM_YEAR_ID_COL => $this->GetCurriculumYear(),
                                        cn::TEACHER_CLASS_SUBJECT_TEACHER_ID_COL => Auth()->user()->{cn::USERS_ID_COL}
                                    ])
                                    ->pluck(cn::TEACHER_CLASS_SUBJECT_CLASS_ID_COL)
                                    ->toArray();
                    $gradeClass = TeachersClassSubjectAssign::where([
                                    cn::TEACHER_CLASS_SUBJECT_CURRICULUM_YEAR_ID_COL => $this->GetCurriculumYear(),
                                    cn::TEACHER_CLASS_SUBJECT_TEACHER_ID_COL => Auth()->user()->{cn::USERS_ID_COL}
                                ])
                                ->pluck(cn::TEACHER_CLASS_SUBJECT_CLASS_NAME_ID_COL)->toArray();
                    if(isset($gradeClass) && !empty($gradeClass)){
                        $gradeClass = implode(',', $gradeClass);
                        $gradeClassId = explode(',',$gradeClass);
                    }
                    // $studentidlist = User::whereIn(cn::USERS_GRADE_ID_COL,$gradesListId)
                    //                 ->whereIn(cn::USERS_CLASS_ID_COL,$gradeClassId)
                    //                 ->where(cn::USERS_SCHOOL_ID_COL,$schoolId)
                    //                 ->where(cn::USERS_ROLE_ID_COL,cn::STUDENT_ROLE_ID)
                    //                 ->pluck(cn::USERS_ID_COL)
                    //                 ->toArray();
                    $studentidlist = User::where([
                                        cn::USERS_SCHOOL_ID_COL => $schoolId,
                                        cn::USERS_ROLE_ID_COL => cn::STUDENT_ROLE_ID
                                    ])
                                    ->get()
                                    ->whereIn('CurriculumYearGradeId',$gradesListId)
                                    ->whereIn('CurriculumYearClassId',$gradeClassId)
                                    ->pluck(cn::USERS_ID_COL)
                                    ->toArray();
                }

                if($this->isPrincipalLogin() || $this->isSchoolLogin()){
                    $studentidlist = User::where(cn::USERS_SCHOOL_ID_COL,$schoolId)
                                        ->where(cn::USERS_ROLE_ID_COL,cn::STUDENT_ROLE_ID)
                                        ->pluck(cn::USERS_ID_COL)
                                        ->toArray();
                }
                
                $TeachersStudentIdList = array_intersect($studentIds,$studentidlist);

                // Get Data based on types of graph option select
                switch ($request->graph_type) {
                    case 'my-school':
                        // Calculation for my-class graph functionality
                        $examData = Exam::find($request->examid);
                        $totalQuestion = explode(',',$examData->question_ids);
                        if(isset($TeachersStudentIdList) && !empty($TeachersStudentIdList)){
                            foreach($TeachersStudentIdList as $studentid){
                                $ExamAttemptData =  AttemptExams::where([
                                                        cn::ATTEMPT_EXAMS_EXAM_ID => $request->examid,
                                                        cn::ATTEMPT_EXAMS_STUDENT_STUDENT_ID => $studentid
                                                    ])
                                                    ->first();
                                if(isset($ExamAttemptData) && !empty($ExamAttemptData)){
                                    $studentAbility[] = $ExamAttemptData->student_ability;
                                }
                            }
                        }
                        $dataList2 = array();
                        
                        if(isset($studentidlist) && !empty($studentidlist)){
                            foreach ($studentidlist as $studentid_id) {
                                $ExamAttemptData =  AttemptExams::where(cn::ATTEMPT_EXAMS_EXAM_ID,$request->examid)
                                                    ->where(cn::ATTEMPT_EXAMS_STUDENT_STUDENT_ID,$studentid_id)
                                                    ->first();
                                if(isset($ExamAttemptData) && !empty($ExamAttemptData)){
                                    $dataList2[] = $ExamAttemptData->student_ability;
                                }
                            }
                        }

                        // Call to ALP AI My School Ability Analysis Graph API
                        if(isset($studentAbility) && !empty($studentAbility)){
                            $requestPayload = new Request();
                            $requestPayload = $requestPayload->replace([
                                'data_list1' => array_values(array_unique(array_map('floatval', $studentAbility))),
                                'data_list2' => array_values(array_unique(array_map('floatval', $dataList2))),
                                "format" => "base64",
                                'labels' => $this->GetAiApiLabels(config()->get('aiapi.api.Plot_Analyze_My_School_Ability.uri'),$isGroup)
                            ]);
                            $response = $this->AIApiService->Plot_Analyze_My_School_Ability($requestPayload);
                        }
                    break;
                    case 'all-school':
                        // Calculation for my-class graph functionality
                        $schoolId = Auth::user()->{cn::USERS_SCHOOL_ID_COL};
                        $examData = Exam::find($request->examid);
                        $totalQuestion = explode(',',$examData->question_ids);
                        if(isset($TeachersStudentIdList) && !empty($TeachersStudentIdList)){
                            foreach($TeachersStudentIdList as $studentid){
                                $ExamAttemptData =  AttemptExams::where(cn::ATTEMPT_EXAMS_EXAM_ID,$request->examid)
                                                    ->where(cn::ATTEMPT_EXAMS_STUDENT_STUDENT_ID,$studentid)
                                                    ->first();
                                if(isset($ExamAttemptData) && !empty($ExamAttemptData)){
                                    $studentAbility[] = $ExamAttemptData->student_ability;
                                }
                            }
                        }
                        $dataList2 = array();
                        $studentidlist = User::where([
                                            cn::USERS_SCHOOL_ID_COL => $schoolId,
                                            cn::USERS_ROLE_ID_COL => cn::STUDENT_ROLE_ID
                                        ])
                                        ->pluck(cn::USERS_ID_COL)->toArray();
                        if(isset($studentidlist) && !empty($studentidlist)){
                            foreach ($studentidlist as $studentid_id) {
                                $ExamAttemptData = AttemptExams::where(cn::ATTEMPT_EXAMS_EXAM_ID,$request->examid)->where(cn::ATTEMPT_EXAMS_STUDENT_STUDENT_ID,$studentid_id)->first();
                                if(isset($ExamAttemptData) && !empty($ExamAttemptData)){
                                    $dataList2[] = $ExamAttemptData->student_ability;
                                }
                            }
                        }

                        $dataList3 = array();
                        $student_id_all = explode(',',$examData->student_ids);
                        if(isset($student_id_all) && !empty($student_id_all)){
                            foreach ($student_id_all as $studentid_id) {
                                $ExamAttemptData = AttemptExams::where(cn::ATTEMPT_EXAMS_EXAM_ID,$request->examid)->where(cn::ATTEMPT_EXAMS_STUDENT_STUDENT_ID,$studentid_id)->first();
                                if(isset($ExamAttemptData) && !empty($ExamAttemptData)){
                                    $dataList3[] = $ExamAttemptData->student_ability;
                                }
                            }
                        }

                        // Call to ALP AI My School Ability Analysis Graph API
                        if(isset($studentAbility) && !empty($studentAbility)){
                            $requestPayload = new Request();
                            $requestPayload = $requestPayload->replace([
                                'data_list1' => array_values(array_unique(array_map('floatval', $studentAbility))),
                                'data_list2' => array_values(array_unique(array_map('floatval', $dataList2))),
                                'data_list3' => array_values(array_unique(array_map('floatval', $dataList3))),
                                'labels' => $this->GetAiApiLabels(config()->get('aiapi.api.Plot_Analyze_All_Schools_Ability.uri'), $isGroup),
                                "format" => "base64"
                            ]);
                            $response = $this->AIApiService->Plot_Analyze_All_Schools_Ability($requestPayload);
                        }
                        break;
                    default:  // My-class
                        // Calculation for my-class graph functionality
                        $examData = Exam::find($request->examid);
                        $totalQuestion = explode(',',$examData->question_ids);
                        if(isset($TeachersStudentIdList) && !empty($TeachersStudentIdList)){
                            foreach($TeachersStudentIdList as $studentid){
                                $ExamAttemptData = AttemptExams::where(cn::ATTEMPT_EXAMS_EXAM_ID,$request->examid)->where(cn::ATTEMPT_EXAMS_STUDENT_STUDENT_ID,$studentid)->first();
                                if(isset($ExamAttemptData) && !empty($ExamAttemptData)){
                                    $studentAbility[] = $ExamAttemptData->student_ability;
                                }
                            }
                        }
                        
                        // Call to ALP AI Performance Analysis Graph API
                        if(isset($studentAbility) && !empty($studentAbility)){
                            $requestPayload = new Request();
                            $requestPayload = $requestPayload->replace([
                                'data_list' => array_values(array_unique(array_map('floatval', $studentAbility))),
                                "format" => "base64",
                                'labels' => $this->GetAiApiLabels(config()->get('aiapi.api.Plot_Analyze_My_Class_Ability.uri'),$isGroup)
                            ]);
                            $response = $this->AIApiService->Plot_Analyze_My_Class_Ability($requestPayload);
                        }
                }
            }
            return $this->sendResponse($response);
        } catch (\Exception $ex) {
            return $this->sendError($ex->getMessage(), 404);
        }
    }

    /**
     * USE : In the teacher panel -> "My Teaching" Module click on particular exam wise check get Test Difficulty Analysis Report
     */
    public function getTestDifficultyAnalysisReport(Request $request){
        try {
            $response = [];
            $difficultyArray = [];
            if(isset($request->examid)){
                // Is single test
                $examData = Exam::find($request->examid);
                $QuestionArray = explode(',',$examData->question_ids);
                if(isset($QuestionArray) && !empty($QuestionArray)){
                    foreach($QuestionArray as $questionId){
                        $QuestionData = Question::with('PreConfigurationDifficultyLevel')->find($questionId);
                        if(isset($QuestionData) && !empty($QuestionData)){
                            $difficultyArray[] = $QuestionData->PreConfigurationDifficultyLevel['title'];
                        }
                    }
                }
                
                // Call to ALP AI Performance Analysis Graph API
                if(isset($difficultyArray) && !empty($difficultyArray)){
                    $requestPayload = new Request();
                    $requestPayload = $requestPayload->replace([
                        'data_list' => array_map('floatval', $difficultyArray),
                        "format" => "base64"
                    ]);
                    $response = $this->AIApiService->getPloatAnalyzeTestDifficulty($requestPayload);
                }
            }
            return $this->sendResponse($response);
        } catch (\Exception $ex) {
            return $this->sendError($ex->getMessage(), 404);
        }
    }

    /**
     * USE : In the Class Performance Module click on particular question wise check Performance analyze graph
     */
    public function getQuestionGraphCurrentStudent(Request $request){
        try {
            $QuestionAnalysisResult = [];
            if(!empty($request->student_id) && !empty($request->exam_id)){
                $PreConfigurationDifficultyLevel = array();
                // $PreConfigurationDifficultyLevelData = PreConfigurationDiffiltyLevel::get()->toArray();
                $PreConfigurationDifficultyLevelData = PreConfigurationDiffiltyLevel::where(cn::PRE_CONFIGURE_DIFFICULTY_CURRICULUM_YEAR_ID_COL,$this->GetCurriculumYear())->get()->toArray();
                if(isset($PreConfigurationDifficultyLevelData)){
                    $PreConfigurationDifficultyLevel = array_column($PreConfigurationDifficultyLevelData,cn::PRE_CONFIGURE_DIFFICULTY_TITLE_COL,cn::PRE_CONFIGURE_DIFFICULTY_DIFFICULTY_LEVEL_COL);
                }
                $arrayOfExams = explode(',',$request->exam_id);
                if(count($arrayOfExams) == 1){
                    $ExamData = Exam::find($request->exam_id);
                    $AttemptExamData = AttemptExams::where(cn::ATTEMPT_EXAMS_EXAM_ID,$request->exam_id)->get()->toArray();
                    $QuestionList = Question::with('answers')->where(cn::QUESTION_TABLE_ID_COL,$request->question_id)->get();
                    foreach($AttemptExamData as $key => $value){
                        if(isset($QuestionList) && !empty($QuestionList)){
                            foreach($QuestionList as $QuestionKey => $question){
                                $QuestionAnalysisResult['question_difficulty'] = $PreConfigurationDifficultyLevel[$question['dificulaty_level']];
                                $countQuestions = count($QuestionList);
                                $Answerdetail = $question->answers;
                                if(isset($value['question_answers'])){
                                    $filterattempQuestionAnswer = array_filter(json_decode($value['question_answers']), function ($var) use($question){
                                        if($var->question_id == $question['id']){
                                            return $var ?? [];
                                        }
                                    });
                                }
                                if(isset($filterattempQuestionAnswer) && !empty($filterattempQuestionAnswer)){
                                    foreach($filterattempQuestionAnswer as $fanswer){
                                        // Save the question result
                                        if($fanswer->answer == $Answerdetail->{'correct_answer_'.$fanswer->language}){
                                            $QuestionAnalysisResult['question_results_list'][] = true;
                                        }else{
                                            $QuestionAnalysisResult['question_results_list'][] = false;
                                        }
                                    }
                                }
                            }
                        }
                        $QuestionAnalysisResult['student_abilities_list'][]  = $value['student_ability'];
                    }
                }
            }
            if(isset($QuestionAnalysisResult) && !empty($QuestionAnalysisResult)){
                $requestPayload = new Request();
                $requestPayload = $requestPayload->replace([
                    'question_difficulty'       => floatval($QuestionAnalysisResult['question_difficulty']),
                    'question_results_list'     => $QuestionAnalysisResult['question_results_list'],
                    'student_abilities_list'    => array_map('floatval', $QuestionAnalysisResult['student_abilities_list']),
                    'format'                    => 'base64',
                    'labels'                    => $this->GetAiApiLabels(config()->get('aiapi.api.Plot_Analyze_Question.uri'))
                ]);
                // Call to AI API from AI Server
                $response = $this->AIApiService->getPloatAnalayzeQuestion($requestPayload);
            }
            return $this->sendResponse($response);
        } catch (\Exception $ex) {
            return $this->sendError($ex->getMessage(), 404);
        }
    }

    /**
     * USE : Get Student Ability API
     */
    public function getStudentAbilityApi($exam_list,$student_id){
        $PreConfigurationDifficultyLevel = array();
        $PreConfigurationDifficultyLevelData = PreConfigurationDiffiltyLevel::get()->toArray();
        if(isset($PreConfigurationDifficultyLevelData)){
            $PreConfigurationDifficultyLevel = array_column($PreConfigurationDifficultyLevelData,cn::PRE_CONFIGURE_DIFFICULTY_TITLE_COL,cn::PRE_CONFIGURE_DIFFICULTY_DIFFICULTY_LEVEL_COL);
        }
        $ExamData = array();
        $AttemptExamData =  Exam::with('attempt_exams')
                            ->whereHas('attempt_exams', function ($query) use ($student_id,$exam_list){
                                $query->whereIn(cn::ATTEMPT_EXAMS_EXAM_ID,$exam_list);
                                $query->where(cn::ATTEMPT_EXAMS_STUDENT_STUDENT_ID,$student_id);
                            })->get()->toArray();
        if(isset($AttemptExamData) && !empty($AttemptExamData)){
            foreach($AttemptExamData as $questionKey => $question){
                if(isset($question['attempt_exams'][0][cn::ATTEMPT_EXAMS_QUESTION_ANSWER_COL])){
                    if(isset($question['attempt_exams'][0][cn::ATTEMPT_EXAMS_QUESTION_ANSWER_COL])){
                        $filterattempQuestionAnswerData = json_decode($question['attempt_exams'][0][cn::ATTEMPT_EXAMS_QUESTION_ANSWER_COL],true);
                        $filterattempQuestionAnswer = array_column($filterattempQuestionAnswerData,'answer','question_id');
                        $filterattempQuestionAnswerLanguage = array_column($filterattempQuestionAnswerData,'language','question_id');
                    }
                }
                $question_ids = explode(',',$question['question_ids']);
                foreach ($question_ids as $question_id) {
                    $QuestionList = Question::with('answers')->where(cn::QUESTION_TABLE_ID_COL,$question_id)->get()->toArray();
                    if(isset($PreConfigurationDifficultyLevel) && !empty($PreConfigurationDifficultyLevel) && isset($PreConfigurationDifficultyLevel[$QuestionList[0][cn::QUESTION_DIFFICULTY_LEVEL_COL]]) && !empty($PreConfigurationDifficultyLevel[$QuestionList[0][cn::QUESTION_DIFFICULTY_LEVEL_COL]])){
                        $ExamData['difficulty_list'][] = number_format($PreConfigurationDifficultyLevel[$QuestionList[0][cn::QUESTION_DIFFICULTY_LEVEL_COL]], 4, '.', '');
                    }else{
                        $ExamData['difficulty_list'][] = 0;
                    }
                    $anscount = 0;
                    for($ans = 1; $ans <= 4; $ans++){
                        if(trim($QuestionList[0]['answers']['answer'.$ans.'_en']) != ""){
                            $anscount++;
                        }
                    }
                    $ExamData['num_of_ans_list'][] = $anscount;
                    if($filterattempQuestionAnswer[$question_id] == $QuestionList[0]['answers']['correct_answer_'.$filterattempQuestionAnswerLanguage[$question_id]]){
                        $ExamData['questions_results'][] = true;
                    }else{
                        $ExamData['questions_results'][] = false;
                    }
                }
            }
        }
        if(isset($ExamData) && !empty($ExamData)){
            $requestPayload = new Request();
            $requestPayload = $requestPayload->replace([
                'questions_results' => array($ExamData['questions_results']),
                'num_of_ans_list'   => $ExamData['num_of_ans_list'],
                'difficulty_list'   => array_map('floatval', $ExamData['difficulty_list']),
                'max_student_num'   => 1
            ]);
            $data = $this->AIApiService->getStudentProgressReport($requestPayload);
            if(isset($data) && !empty($data) && isset($data[0]) && !empty($data[0])){
                return $data[0];
            }else{
                return 0;
            }  
        }
    }

    /**
     * USE : Get Progress Detail in Teacher Panel
     */
    public function getProgressDetailList($examId,$studentIds){
        $PreConfigurationDifficultyLevel = array();
        $PreConfigurationDifficultyLevelData = PreConfigurationDiffiltyLevel::get()->toArray();
        if(isset($PreConfigurationDifficultyLevelData)){
            $PreConfigurationDifficultyLevel = array_column($PreConfigurationDifficultyLevelData,cn::PRE_CONFIGURE_DIFFICULTY_TITLE_COL,cn::PRE_CONFIGURE_DIFFICULTY_DIFFICULTY_LEVEL_COL);
        }
        $ExamsIds = explode(',',$examId);
        $students = explode(',',$studentIds);
        $max_student_num = 0;
        $exmDataList = array();
        if(!empty($ExamsIds)){
            $examData = Exam::find($examId);
            foreach($students as $key => $student){
                $attemptedStudentExams = AttemptExams::where([
                                            cn::ATTEMPT_EXAMS_EXAM_ID => $examId,
                                            cn::ATTEMPT_EXAMS_STUDENT_STUDENT_ID => $student
                                        ])->first();
                if(isset($attemptedStudentExams) && !empty($attemptedStudentExams)){
                    $max_student_num++;
                    if(isset($attemptedStudentExams->{cn::ATTEMPT_EXAMS_QUESTION_ANSWER_COL})){
                        $filterattempQuestionAnswerData = json_decode($attemptedStudentExams->{cn::ATTEMPT_EXAMS_QUESTION_ANSWER_COL},true);
                        $filterattempQuestionAnswer = array_column($filterattempQuestionAnswerData,'answer','question_id');
                        $filterattempQuestionAnswerLanguage = array_column($filterattempQuestionAnswerData,'language','question_id');
                    }
                    $question_ids = explode(',',$examData->question_ids);
                    $exmdata = array();
                    foreach ($question_ids as $question_id) {
                        $QuestionList = Question::with('answers')->where(cn::QUESTION_TABLE_ID_COL,$question_id)->get()->toArray();
                        if($QuestionList){
                            if(isset($PreConfigurationDifficultyLevel) && !empty($PreConfigurationDifficultyLevel) && isset($PreConfigurationDifficultyLevel[$QuestionList[0][cn::QUESTION_DIFFICULTY_LEVEL_COL]]) && !empty($PreConfigurationDifficultyLevel[$QuestionList[0][cn::QUESTION_DIFFICULTY_LEVEL_COL]])){
                                $exmdata['difficulty_list'][] = number_format($PreConfigurationDifficultyLevel[$QuestionList[0][cn::QUESTION_DIFFICULTY_LEVEL_COL]], 4, '.', '');
                            }else{
                                $exmdata['difficulty_list'][] = 0;
                            }
                            $anscount = 0;
                            for($ans = 1; $ans <= 4; $ans++){
                                if(trim($QuestionList[0]['answers']['answer'.$ans.'_en']) != ""){
                                    $anscount++;
                                }
                            }
                            $exmdata['num_of_ans_list'][] = $anscount;
                            if(isset($filterattempQuestionAnswer) && isset($filterattempQuestionAnswerLanguage) && isset($filterattempQuestionAnswerLanguage[$question_id]) && $filterattempQuestionAnswer[$question_id] == $QuestionList[0]['answers']['correct_answer_'.$filterattempQuestionAnswerLanguage[$question_id]]){
                                $exmdata['questions_results'][] = true;
                            }else{
                                $exmdata['questions_results'][] = false;
                            }
                        }
                    }
                    $exmDataList['questions_results'][] = $exmdata['questions_results'] ?? [];
                    $exmDataList['num_of_ans_list'] = $exmdata['num_of_ans_list'] ?? [];
                    $exmDataList['difficulty_list'] = $exmdata['difficulty_list'] ?? [];
                    $exmDataList['max_student_num'] = $max_student_num;
                }
            }
            if(isset($exmDataList) && !empty($exmDataList) && isset($exmDataList['questions_results']) && isset($exmDataList['num_of_ans_list']) && isset($exmDataList['difficulty_list']) && isset($exmDataList['max_student_num'])){
                $requestPayload = new Request();
                $requestPayload = $requestPayload->replace([
                    'questions_results'=> $exmDataList['questions_results'],
                    'num_of_ans_list' => $exmDataList['num_of_ans_list'],
                    'difficulty_list' => array_map('floatval', $exmDataList['difficulty_list']),
                    'max_student_num' => $exmDataList['max_student_num']
                ]);
                $data = $this->AIApiService->getStudentProgressReport($requestPayload);
                if(isset($data) && !empty($data)){
                    return $data = Helper::getProgressDetail($students,$data);
                }else{
                    return 0;
                }
            }
        }
        return array('Struggling' => 0,'Beginning' => 0,'Approaching' => 0,'Proficient' => 0,'Advanced' => 0, 'InComplete' => 100);
    }
}