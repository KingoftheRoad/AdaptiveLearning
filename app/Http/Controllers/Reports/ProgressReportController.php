<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Traits\ResponseFormat;
use App\Traits\Common;
use App\Helpers\Helper;
use App\Http\Services\AIApiService;
use App\Models\Exam;
use App\Models\User;
use App\Models\GradeClassMapping;
use App\Models\Grades;
use App\Models\StrandUnitsObjectivesMappings;
use App\Models\Question;
use App\Models\Strands;
use App\Models\LearningsUnits;
use App\Models\LearningsObjectives;
use App\Models\AttemptExams;
use App\Models\TeachersClassSubjectAssign;
use App\Models\PreConfigurationDiffiltyLevel;
use App\Models\GradeSchoolMappings;
use App\Constants\DbConstant As cn;
use Illuminate\Support\Facades\DB;

class ProgressReportController extends Controller
{
    use Common, ResponseFormat;

    public function __construct(){
        $this->AIApiService = new AIApiService();
    }

    /**
     * USE : Progress report learning objectives display into teacher panel
     */
    public function TeacherProgressReportLearningObjective(Request $request){
        try{
            ini_set('max_execution_time', -1);
            if(isset($request->isFilter)){
                $isFilter = true;
            }else{
                $isFilter = false;
            }
            $progressReportArray = array();
            $LearningsUnitsLbl = array();
            $grade_id = array();
            $GradeClassListData = array();
            $class_type_id = array();
            $GradesList = array();
            $teachersClassList = array();
            $currentLang = ucwords(app()->getLocale());
            $schoolId = Auth::user()->{cn::USERS_SCHOOL_ID_COL};
            $roleId = Auth::user()->{cn::USERS_ROLE_ID_COL};
            $reportLearningType = "";

            if(isset($request->reportLearningType) && !empty($request->reportLearningType)){
                $reportLearningType = $request->reportLearningType;
            }

            if(isset($request->grade_id) && !empty($request->grade_id)){
                $grade_id = $request->grade_id;
            }

            // Get pre-configured data for the questions
            $PreConfigurationDifficultyLevel = array();
            $PreConfigurationDiffiltyLevelData = PreConfigurationDiffiltyLevel::where(cn::PRE_CONFIGURE_DIFFICULTY_CURRICULUM_YEAR_ID_COL,$this->GetCurriculumYear())->get()->toArray();
            if(isset($PreConfigurationDiffiltyLevelData)){
                $PreConfigurationDifficultyLevel = array_column($PreConfigurationDiffiltyLevelData,cn::PRE_CONFIGURE_DIFFICULTY_TITLE_COL,cn::PRE_CONFIGURE_DIFFICULTY_DIFFICULTY_LEVEL_COL);
            }

            $teacherClassSubjectAssign =    TeachersClassSubjectAssign::where([
                                                cn::TEACHER_CLASS_SUBJECT_CURRICULUM_YEAR_ID_COL => $this->GetCurriculumYear(),
                                                cn::TEACHER_CLASS_SUBJECT_TEACHER_ID_COL => Auth()->user()->{cn::USERS_ID_COL},
                                                cn::TEACHER_CLASS_SUBJECT_SCHOOL_ID_COL => $schoolId
                                            ])->get();
            $gradeArray = array();
            $classArray = array();
            foreach($teacherClassSubjectAssign as $teacherGrades){
                // Store teacher grades into array
                $gradeData = Grades::find($teacherGrades['class_id']);
                if(isset($request->grade_id) && !empty($request->grade_id)){
                    $gradeArray = $request->grade_id;
                }else{
                    $gradeArray[] = $gradeData->id;
                }
                $GradesList[] = array(
                                    'id' => $gradeData->id,
                                    'name' => $gradeData->name
                                );
                
            }

            $teacherGradesFirst = $teacherClassSubjectAssign[0];
            if(isset($request->grade_id) && !empty($request->grade_id)){
                $teacherClassSubjectAssignNew = TeachersClassSubjectAssign::where([
                                                    cn::TEACHER_CLASS_SUBJECT_CURRICULUM_YEAR_ID_COL => $this->GetCurriculumYear(),
                                                    cn::TEACHER_CLASS_SUBJECT_TEACHER_ID_COL => Auth()->user()->{cn::USERS_ID_COL},
                                                    cn::TEACHER_CLASS_SUBJECT_CLASS_ID_COL => $request->grade_id,
                                                    cn::TEACHER_CLASS_SUBJECT_SCHOOL_ID_COL => $schoolId
                                                ])->get();
                $teacherGradesFirst = $teacherClassSubjectAssignNew[0];
            }
            if(!empty($teacherGradesFirst['class_name_id'])){
                $teachersClass = explode(',',$teacherGradesFirst['class_name_id']);
                $gradeData = Grades::find($teacherGradesFirst['class_id']);
                if(!empty($teachersClass)){
                    $GradeClassData =   GradeClassMapping::whereIn(cn::GRADE_CLASS_MAPPING_ID_COL,$teachersClass)
                                        ->where([
                                            cn::GRADE_CLASS_MAPPING_CURRICULUM_YEAR_ID_COL => $this->GetCurriculumYear(),
                                            cn::GRADE_CLASS_MAPPING_SCHOOL_ID_COL => $schoolId,
                                            cn::GRADE_CLASS_MAPPING_GRADE_ID_COL => $teacherGradesFirst['class_id']
                                        ])
                                        ->get();
                    foreach($GradeClassData as $gradeClasss){
                        if(isset($request->class_type_id) && !empty($request->class_type_id)){
                            $classArray = $request->class_type_id;
                        }else{
                            $classArray[] = $gradeClasss->id;
                        }
                        $teachersClassList[] = array(
                            'class_id' => $gradeClasss->id,
                            'class_name' => $gradeData->name.$gradeClasss->name
                        );
                    }
                }
            }

            // Get Color Codes Array
            $ColorCodes = Helper::GetColorCodes();

            // Get Strands data
            if(isset($request->learningReportStrand)){
                $strandData = Strands::all();
                $strand = Strands::whereIn(cn::STRANDS_ID_COL,$request->learningReportStrand)->first();
                $strandDataLbl = Strands::whereIn(cn::STRANDS_ID_COL,$request->learningReportStrand)->pluck('name_'.app()->getLocale(),cn::STRANDS_ID_COL)->toArray();
            }else{
                $strandData = Strands::all();
                $strand = Strands::first();
                $strandDataLbl = Strands::pluck('name_'.app()->getLocale(),cn::STRANDS_ID_COL)->toArray();
            }
            $LearningsUnitsLbl = LearningsUnits::pluck('name_'.app()->getLocale(),cn::LEARNING_UNITS_ID_COL)->toArray();
            $strandId = $strand->{cn::STRANDS_ID_COL};
            $LearningUnits = LearningsUnits::where(cn::LEARNING_UNITS_STRANDID_COL, $strand->{cn::STRANDS_ID_COL})->get();
            $learningUnitsIds = LearningsUnits::where(cn::LEARNING_UNITS_STRANDID_COL, $strand->{cn::STRANDS_ID_COL})->pluck(cn::LEARNING_UNITS_ID_COL)->toArray();
            // $learningObjectivesList = LearningsObjectives::where(cn::LEARNING_OBJECTIVES_LEARNING_UNITID_COL,$learningUnitsIds[0])->get();
            $learningObjectivesList = LearningsObjectives::IsAvailableQuestion()->where(cn::LEARNING_OBJECTIVES_LEARNING_UNITID_COL,$learningUnitsIds[0])->get();
            if(isset($request->learning_unit_id) && !empty($request->learning_unit_id)){
                $learningUnitsIds = LearningsUnits::where(cn::LEARNING_UNITS_STRANDID_COL, $strand->{cn::STRANDS_ID_COL})->where(cn::LEARNING_UNITS_ID_COL, $request->learning_unit_id)->pluck(cn::LEARNING_UNITS_ID_COL)->toArray();
                // $learningObjectivesList = LearningsObjectives::where(cn::LEARNING_OBJECTIVES_LEARNING_UNITID_COL,$learningUnitsIds[0])->get();
                $learningObjectivesList = LearningsObjectives::IsAvailableQuestion()->where(cn::LEARNING_OBJECTIVES_LEARNING_UNITID_COL,$learningUnitsIds[0])->get();
            }

            if(isset($gradeArray)){
                $gradeid = $gradeArray[0];
            }
            
            if(isset($classArray)){
                $classid = $classArray[0];
            }
            
            if($isFilter){
                if(!empty($learningUnitsIds)){                
                    $learningUnitsId = $learningUnitsIds[0];
                    // $learningObjectivesIds = LearningsObjectives::where(cn::LEARNING_OBJECTIVES_LEARNING_UNITID_COL, $learningUnitsId)->pluck(cn::LEARNING_OBJECTIVES_ID_COL)->toArray();
                    // $LearningsObjectivesLbl = LearningsObjectives::where(cn::LEARNING_OBJECTIVES_LEARNING_UNITID_COL, $learningUnitsId)->pluck('title_'.app()->getLocale(),cn::LEARNING_OBJECTIVES_ID_COL)->toArray();
                    $learningObjectivesIds = LearningsObjectives::IsAvailableQuestion()->where(cn::LEARNING_OBJECTIVES_LEARNING_UNITID_COL, $learningUnitsId)->pluck(cn::LEARNING_OBJECTIVES_ID_COL)->toArray();
                    $LearningsObjectivesLbl = LearningsObjectives::IsAvailableQuestion()->where(cn::LEARNING_OBJECTIVES_LEARNING_UNITID_COL, $learningUnitsId)->pluck('title_'.app()->getLocale(),cn::LEARNING_OBJECTIVES_ID_COL)->toArray();
                    if(!empty($learningObjectivesIds)){
                        $no_of_learning_objectives = count($learningObjectivesIds);
                        if(isset($gradeArray)){
                            $gradeid = $gradeArray[0];
                            $gradeData = Grades::find($gradeid);
                            $GradeClassData =   GradeClassMapping::where([
                                                    cn::GRADE_CLASS_MAPPING_GRADE_ID_COL => $gradeid,
                                                    cn::GRADE_CLASS_MAPPING_CURRICULUM_YEAR_ID_COL => $this->GetCurriculumYear(),
                                                    cn::GRADE_CLASS_MAPPING_SCHOOL_ID_COL => $schoolId
                                                ])
                                                ->whereIn(cn::GRADE_CLASS_MAPPING_ID_COL,$classArray)
                                                ->get();
                            if(isset($GradeClassData) && !empty($GradeClassData)){
                                $gradeClasss = $GradeClassData[0];
                                // $studentList =  User::where(cn::USERS_GRADE_ID_COL,$gradeid)
                                //                 ->where(cn::USERS_CLASS_ID_COL,$gradeClasss->id)
                                //                 ->where(cn::USERS_SCHOOL_ID_COL,$schoolId)
                                //                 ->where(cn::USERS_ROLE_ID_COL,cn::STUDENT_ROLE_ID)
                                //                 ->get();
                                $studentList =  User::where([
                                                    cn::USERS_ROLE_ID_COL =>cn::STUDENT_ROLE_ID,
                                                    cn::USERS_SCHOOL_ID_COL => $schoolId
                                                ])
                                                ->get()
                                                ->where('CurriculumYearGradeId',$gradeid)
                                                ->where('CurriculumYearClassId',$gradeClasss->id);
                                if(isset($studentList) && !empty($studentList)){
                                    foreach($studentList as $student){
                                        $countNoOfMasteredLearningObjectives = 0;
                                        $progressReportArray[$strandId][$learningUnitsId][$gradeData->name.'-'.$gradeClasss->name][$student->id]['student_data'][] = $student->toArray();
                                        foreach($learningObjectivesIds as $learningObjectivesId){
                                            $progressReportArray[$strandId][$learningUnitsId][$gradeData->name.'-'.$gradeClasss->name][$student->id]['no_of_learning_objectives'] = count($learningObjectivesIds);
                                            $StudentLearningObjectiveAbility = 0;
                                            $countLearningObjectivesQuestion = 0;
                                            $learningObjectivesData = LearningsObjectives::find($learningObjectivesId);
                                            $StrandUnitsObjectivesMappingsId =  StrandUnitsObjectivesMappings::where([
                                                                                    cn::OBJECTIVES_MAPPINGS_STRAND_ID_COL => $strandId,
                                                                                    cn::OBJECTIVES_MAPPINGS_LEARNING_UNIT_ID_COL => $learningUnitsId,
                                                                                    cn::OBJECTIVES_MAPPINGS_LEARNING_OBJECTIVES_ID_COL => $learningObjectivesId
                                                                                ])
                                                                                ->pluck(cn::OBJECTIVES_MAPPINGS_ID_COL)->toArray();
                                            if(isset($StrandUnitsObjectivesMappingsId) && !empty($StrandUnitsObjectivesMappingsId)){
                                                $QuestionsList = Question::with('answers')
                                                                    ->whereIn(cn::QUESTION_OBJECTIVE_MAPPING_ID_COL,$StrandUnitsObjectivesMappingsId)
                                                                    ->orderBy(cn::QUESTION_TABLE_ID_COL)
                                                                    ->get()
                                                                    ->toArray();
                                                if(isset($QuestionsList) && !empty($QuestionsList)){
                                                    $QuestionsDataList = array_column($QuestionsList,cn::QUESTION_TABLE_ID_COL);
                                                    $stud_id = $student->id;
                                                    $StudentAttemptedExamIds = $this->GetStudentAttemptedExamIds($stud_id) ?? [];
                                                    
                                                    $ExamList = Exam::with(['attempt_exams' => fn($query) => $query->where(cn::ATTEMPT_EXAMS_STUDENT_STUDENT_ID, $stud_id)])
                                                                ->whereHas('attempt_exams', function($q) use($stud_id){
                                                                    $q->where(cn::ATTEMPT_EXAMS_STUDENT_STUDENT_ID, '=', $stud_id);
                                                                })
                                                                ->where(function ($query) use ($reportLearningType){
                                                                    if(isset($reportLearningType) && $reportLearningType == 1){  // $reportLearningType == 1 = 'Self-Learning Test
                                                                        $query->where(cn::EXAM_TYPE_COLS,1)  // 1 = test_type = 'self-Learning'
                                                                        ->where(cn::EXAM_TABLE_SELF_LEARNING_TEST_TYPE_COL,2);  // 2 = self_learning_test_type = 'test'
                                                                    }
                                                                    if(isset($reportLearningType) && $reportLearningType == 3){  // $reportLearningType == 3 = 'Test Only'
                                                                        $query->where(cn::EXAM_TYPE_COLS,3);  // 3 = test type = 'Test Only'
                                                                    }
                                                                    if(empty($reportLearningType)){
                                                                        $query->where(cn::EXAM_TYPE_COLS,3)
                                                                        ->orWhere(function ($q1) {
                                                                            $q1->where(cn::EXAM_TABLE_SELF_LEARNING_TEST_TYPE_COL,2)->where(cn::EXAM_TYPE_COLS,1);
                                                                        });
                                                                    }
                                                                })
                                                                ->where(cn::EXAM_CURRICULUM_YEAR_ID_COL,$this->GetCurriculumYear())
                                                                ->whereIn(cn::EXAM_TABLE_ID_COLS,$StudentAttemptedExamIds)
                                                                ->orderBy(cn::EXAM_TABLE_ID_COLS,'DESC')
                                                                ->get()
                                                                ->toArray();                                                    
                                                    if(isset($ExamList) && !empty($ExamList)){
                                                        $StudentLearningObjectiveAbility = 0;
                                                        $ApiRequestData = array();
                                                        foreach($ExamList as $ExamData){
                                                            if($countLearningObjectivesQuestion > $this->getGlobalConfiguration('question_window_size_of_learning_objective')){
                                                                break;
                                                            }
                                                            // Check No of maximum question per learning object is higher then then we will consider only latest questions for the number of question set into global configurations
                                                            if($countLearningObjectivesQuestion < $this->getGlobalConfiguration('question_window_size_of_learning_objective')){
                                                                if(isset($ExamData['attempt_exams'][0][cn::ATTEMPT_EXAMS_QUESTION_ANSWER_COL])){
                                                                    if(isset($ExamData['attempt_exams'][0][cn::ATTEMPT_EXAMS_QUESTION_ANSWER_COL])){
                                                                        $filterAttemptQuestionAnswer = json_decode($ExamData['attempt_exams'][0][cn::ATTEMPT_EXAMS_QUESTION_ANSWER_COL],true);
                                                                    }
                                                                }
                                                                foreach($filterAttemptQuestionAnswer as $filterAttemptQuestionAnswerKey => $filterAttemptQuestionAnswerValue){
                                                                    if(in_array($filterAttemptQuestionAnswerValue['question_id'],$QuestionsDataList)){
                                                                        $countLearningObjectivesQuestion += count(array_intersect(explode(',',$ExamData['question_ids']),$QuestionsDataList));
                                                                        $QuestionsDataListFinal[] = $filterAttemptQuestionAnswerValue['question_id'];
                                                                        $QuestionList = Question::with('answers')->where(cn::QUESTION_TABLE_ID_COL,$filterAttemptQuestionAnswerValue['question_id'])->get()->toArray();
                                                                        if(isset($PreConfigurationDifficultyLevel) && !empty($PreConfigurationDifficultyLevel) && isset($PreConfigurationDifficultyLevel[$QuestionList[0][cn::QUESTION_DIFFICULTY_LEVEL_COL]]) && !empty($PreConfigurationDifficultyLevel[$QuestionList[0][cn::QUESTION_DIFFICULTY_LEVEL_COL]])){
                                                                            $ApiRequestData['difficulty_list'][] = number_format($PreConfigurationDifficultyLevel[$QuestionList[0][cn::QUESTION_DIFFICULTY_LEVEL_COL]], 4, '.', '');
                                                                        }else{
                                                                            $ApiRequestData['difficulty_list'][] = 0;
                                                                        }
                                                                        $AnswerCount = 0;
                                                                        for($ans = 1; $ans <= 4; $ans++){
                                                                            if(trim($QuestionList[0]['answers']['answer'.$ans.'_en']) != ""){
                                                                                $AnswerCount++;
                                                                            }
                                                                        }
                                                                        $ApiRequestData['num_of_ans_list'][] = $AnswerCount;
                                                                        if($filterAttemptQuestionAnswerValue['answer'] == $QuestionList[0]['answers']['correct_answer_'.$ExamData['attempt_exams'][0]['language']]){
                                                                            $ApiRequestData['questions_results'][] = true;
                                                                        }else{
                                                                            $ApiRequestData['questions_results'][] = false;
                                                                        }
                                                                    }
                                                                }
                                                            }
                                                        }

                                                        $Ability = 0;
                                                        if(isset($ApiRequestData) && !empty($ApiRequestData)){
                                                            $requestPayload = new \Illuminate\Http\Request();
                                                            $requestPayload = $requestPayload->replace([
                                                                'questions_results'=> array(
                                                                    $ApiRequestData['questions_results']
                                                                ),
                                                                'num_of_ans_list' => $ApiRequestData['num_of_ans_list'],
                                                                'difficulty_list' => array_map('floatval', $ApiRequestData['difficulty_list']),
                                                                'max_student_num' => 1
                                                            ]);
                                                            $data = $this->AIApiService->getStudentProgressReport($requestPayload);
                                                            if(isset($data) && !empty($data) && isset($data[0]) && !empty($data[0])){
                                                                $Ability = $data[0];
                                                            }
                                                        }

                                                        // Check No of minimum question per learning object is less then then we will display "N/A" Learning progress report
                                                        if($countLearningObjectivesQuestion > $this->getGlobalConfiguration('min_no_question_per_study_progress')){
                                                            $StudentLearningObjectiveAbility = $Ability ?? 0;
                                                            // Store array into student ability                                                                
                                                            $progressReportArray[$strandId][$learningUnitsId][$gradeData->name.'-'.$gradeClasss->name][$student->id]['report_data'][] = array(
                                                                'learning_objective_number' => $learningObjectivesData->foci_number,
                                                                'LearningsObjectives' => $learningObjectivesData->foci_number.' '.$this->setLearningObjectivesTitle($LearningsObjectivesLbl[$learningObjectivesId]),
                                                                'ability' => $StudentLearningObjectiveAbility,
                                                                'normalizedAbility' => Helper::getNormalizedAbility($StudentLearningObjectiveAbility),
                                                                'ShortNormalizedAbility' => Helper::getShortNormalizedAbility($StudentLearningObjectiveAbility),
                                                                'studystatus' => Helper::getAbilityType($StudentLearningObjectiveAbility),
                                                                'studyStatusColor' => Helper::getGlobalConfiguration(Helper::getAbilityType($StudentLearningObjectiveAbility))
                                                            );
                                                        }else{
                                                            // Store array into student ability                                                                
                                                            $progressReportArray[$strandId][$learningUnitsId][$gradeData->name.'-'.$gradeClasss->name][$student->id]['report_data'][] = array(
                                                                'learning_objective_number' => $learningObjectivesData->foci_number,
                                                                'LearningsObjectives' => $learningObjectivesData->foci_number.' '.$this->setLearningObjectivesTitle($LearningsObjectivesLbl[$learningObjectivesId]),
                                                                'ability' => $StudentLearningObjectiveAbility,
                                                                'normalizedAbility' => $StudentLearningObjectiveAbility,
                                                                'ShortNormalizedAbility' => $StudentLearningObjectiveAbility,
                                                                'studystatus' => Helper::getAbilityType($StudentLearningObjectiveAbility),
                                                                'studyStatusColor' => Helper::getGlobalConfiguration(Helper::getAbilityType($StudentLearningObjectiveAbility))
                                                            );
                                                        }

                                                        
                                                    }else{
                                                        $progressReportArray[$strandId][$learningUnitsId][$gradeData->name.'-'.$gradeClasss->name][$student->id]['report_data'][] = array(
                                                            'learning_objective_number' => $learningObjectivesData->foci_number,
                                                            'LearningsObjectives' => $learningObjectivesData->foci_number.' '.$this->setLearningObjectivesTitle($LearningsObjectivesLbl[$learningObjectivesId]),
                                                            'ability' => $StudentLearningObjectiveAbility,
                                                            'normalizedAbility' => $StudentLearningObjectiveAbility,
                                                            'ShortNormalizedAbility' => $StudentLearningObjectiveAbility,
                                                            'studystatus' => Helper::getAbilityType($StudentLearningObjectiveAbility),
                                                            'studyStatusColor' => Helper::getGlobalConfiguration('incomplete_color')
                                                        );
                                                    }
                                                }else{
                                                    $progressReportArray[$strandId][$learningUnitsId][$gradeData->name.'-'.$gradeClasss->name][$student->id]['report_data'][] = array(
                                                        'learning_objective_number' => $learningObjectivesData->foci_number,
                                                        'LearningsObjectives' => $learningObjectivesData->foci_number.' '.$this->setLearningObjectivesTitle($LearningsObjectivesLbl[$learningObjectivesId]),
                                                        'ability' => $StudentLearningObjectiveAbility,
                                                        'normalizedAbility' => $StudentLearningObjectiveAbility,
                                                        'ShortNormalizedAbility' => $StudentLearningObjectiveAbility,
                                                        'studystatus' => Helper::getAbilityType($StudentLearningObjectiveAbility),
                                                        'studyStatusColor' => Helper::getGlobalConfiguration('incomplete_color')
                                                    );
                                                }
                                            }
                                            // Count No of mastered learning objectives
                                            if($this->CheckLearningObjectivesMastered($StudentLearningObjectiveAbility)){
                                                $countNoOfMasteredLearningObjectives++;
                                            }
                                        }
                                        
                                        //  Set Master objectives details for the students
                                        $progressReportArray[$strandId][$learningUnitsId][$gradeData->name.'-'.$gradeClasss->name][$student->id]['master_objectives'] = array(
                                                                                                                                                                            'no_of_learning_objectives' => $no_of_learning_objectives,
                                                                                                                                                                            'count_accomplished_learning_objectives' => $countNoOfMasteredLearningObjectives,
                                                                                                                                                                            'count_not_accomplished_learning_objectives' => ($no_of_learning_objectives - $countNoOfMasteredLearningObjectives),
                                                                                                                                                                            'accomplished_percentage' => round((($countNoOfMasteredLearningObjectives / $no_of_learning_objectives) * 100),1),
                                                                                                                                                                            'not_accomplished_percentage' => (100 - round((($countNoOfMasteredLearningObjectives / $no_of_learning_objectives) * 100),1))
                                                                                                                                                                        );
                                    }
                                }
                            }
                        }
                    }
                }
            }
            return view('backend.reports.progress_report.teacher.learning_objective_report',compact('strandData','GradesList','grade_id','teachersClassList','reportLearningType','progressReportArray','strandDataLbl','LearningsUnitsLbl','learningObjectivesList','LearningUnits','gradeid','classid','ColorCodes'));
        }catch(\Exception $exception) {
            return back()->withError($exception->getMessage())->withInput();
        }
    }

    /**
     * USE : Progress report learning Unit display into teacher panel
     */
    public function TeacherProgressReportLearningUnits(Request $request){
        try{
            ini_set('max_execution_time', -1);
            if(isset($request->isFilter)){
                $isFilter = true;
            }else{
                $isFilter = false;
            }
            $progressReportArray = array();
            $LearningsUnitsLbl = array();
            $grade_id = array();
            $GradeClassListData = array();
            $class_type_id = array();
            $GradesList = array();
            $teachersClassList = array();
            $currentLang = ucwords(app()->getLocale());
            $schoolId = Auth::user()->{cn::USERS_SCHOOL_ID_COL};
            $roleId = Auth::user()->{cn::USERS_ROLE_ID_COL};
            $reportLearningType = "";

            if(isset($request->reportLearningType) && !empty($request->reportLearningType)){
                $reportLearningType = $request->reportLearningType;
            }

            if(isset($request->grade_id) && !empty($request->grade_id)){
                $grade_id = $request->grade_id;
            }

            // Get pre-configured data for the questions
            $PreConfigurationDifficultyLevel = array();
            $PreConfigurationDiffiltyLevelData = PreConfigurationDiffiltyLevel::get()->toArray();
            if(isset($PreConfigurationDiffiltyLevelData)){
                $PreConfigurationDifficultyLevel = array_column($PreConfigurationDiffiltyLevelData,cn::PRE_CONFIGURE_DIFFICULTY_TITLE_COL,cn::PRE_CONFIGURE_DIFFICULTY_DIFFICULTY_LEVEL_COL);
            }

            $teacherClassSubjectAssign =    TeachersClassSubjectAssign::where([
                                                cn::TEACHER_CLASS_SUBJECT_CURRICULUM_YEAR_ID_COL => $this->GetCurriculumYear(),
                                                cn::TEACHER_CLASS_SUBJECT_TEACHER_ID_COL => Auth()->user()->{cn::USERS_ID_COL},
                                                cn::TEACHER_CLASS_SUBJECT_SCHOOL_ID_COL => $schoolId
                                            ])->get();
            $gradeArray = array();
            $classArray = array();
            foreach($teacherClassSubjectAssign as $teacherGrades){
                // Store teacher grades into array
                $gradeData = Grades::find($teacherGrades['class_id']);
                if(isset($request->grade_id) && !empty($request->grade_id)){
                    $gradeArray = $request->grade_id;
                }else{
                    $gradeArray[] = $gradeData->id;
                }
                $GradesList[] = array(
                                    'id' => $gradeData->id,
                                    'name' => $gradeData->name
                                );
            }

            $teacherGradesFirst = $teacherClassSubjectAssign[0];
            if(isset($request->grade_id) && !empty($request->grade_id)){
                $teacherClassSubjectAssignNew = TeachersClassSubjectAssign::where([
                                                    cn::TEACHER_CLASS_SUBJECT_CURRICULUM_YEAR_ID_COL => $this->GetCurriculumYear(),
                                                    cn::TEACHER_CLASS_SUBJECT_TEACHER_ID_COL => Auth()->user()->{cn::USERS_ID_COL},
                                                    cn::TEACHER_CLASS_SUBJECT_CLASS_ID_COL => $request->grade_id,
                                                    cn::TEACHER_CLASS_SUBJECT_SCHOOL_ID_COL => $schoolId
                                                ])->get();
                $teacherGradesFirst = $teacherClassSubjectAssignNew[0];
            }
            if(!empty($teacherGradesFirst['class_name_id'])){
                $teachersClass = explode(',',$teacherGradesFirst['class_name_id']);
                $gradeData = Grades::find($teacherGradesFirst['class_id']);
                if(!empty($teachersClass)){
                    $GradeClassData =   GradeClassMapping::where([
                                            cn::GRADE_CLASS_MAPPING_CURRICULUM_YEAR_ID_COL => $this->GetCurriculumYear(),
                                            cn::GRADE_CLASS_MAPPING_GRADE_ID_COL => $teacherGradesFirst['class_id'],
                                            cn::GRADE_CLASS_MAPPING_SCHOOL_ID_COL => $schoolId
                                        ])
                                        ->whereIn(cn::GRADE_CLASS_MAPPING_ID_COL,$teachersClass)
                                        ->get();
                    foreach($GradeClassData as $gradeClasss){
                        if(isset($request->class_type_id) && !empty($request->class_type_id)){
                            $classArray = $request->class_type_id;
                        }else{
                            $classArray[] = $gradeClasss->id;
                        }
                        $teachersClassList[] = array(
                            'class_id' => $gradeClasss->id,
                            'class_name' => $gradeData->name.$gradeClasss->name
                        );
                    }
                }
            }

            if(isset($gradeArray)){
                $gradeid = $gradeArray[0];
            }
            
            if(isset($classArray)){
                $classid = $classArray[0];
            }

            // Get Color Codes Array
            $ColorCodes = Helper::GetColorCodes();

            // Get Strands data
            $StrandList = Strands::all();
            $LearningUnitsList = LearningsUnits::all();
            $StrandsLearningUnitsList = Strands::with('LearningUnit')->get()->toArray();
            $strandDataLbl = Strands::pluck('name_'.app()->getLocale(),cn::STRANDS_ID_COL)->toArray();
            if(!empty($StrandList)){
                $gradeid = $gradeArray[0];
                $gradeData = Grades::find($gradeid);
                $GradeClassData =   GradeClassMapping::where([
                                        cn::GRADE_CLASS_MAPPING_CURRICULUM_YEAR_ID_COL => $this->GetCurriculumYear(),
                                        cn::GRADE_CLASS_MAPPING_GRADE_ID_COL => $gradeid,
                                        cn::GRADE_CLASS_MAPPING_SCHOOL_ID_COL => $schoolId
                                    ])
                                    ->whereIn(cn::GRADE_CLASS_MAPPING_ID_COL,$classArray)
                                    ->get();
                $gradeClasss = $GradeClassData[0];
                // $studentList =  User::where(cn::USERS_GRADE_ID_COL,$gradeid)
                //                 ->where(cn::USERS_CLASS_ID_COL,$gradeClasss->id)
                //                 ->where(cn::USERS_SCHOOL_ID_COL,$schoolId)
                //                 ->where(cn::USERS_ROLE_ID_COL,cn::STUDENT_ROLE_ID)
                //                 ->get();
                $studentList =  User::where([
                                    cn::USERS_ROLE_ID_COL => cn::STUDENT_ROLE_ID,
                                    cn::USERS_SCHOOL_ID_COL => $schoolId,
                                ])
                                ->get()
                                ->where('CurriculumYearGradeId',$gradeid)
                                ->where('CurriculumYearClassId',$gradeClasss->id);
                foreach($studentList as $student){
                    $progressReportArray[$student->id]['student_data'][] = $student->toArray();
                    $no_of_learning_unit = count($LearningUnitsList);
                    $progressReportArray[$student->id]['no_of_learning_unit'] = $no_of_learning_unit;
                    foreach($StrandList as $strand){
                        $strandId = $strand->id;
                        $LearningUnits = LearningsUnits::where(cn::LEARNING_UNITS_STRANDID_COL, $strand->id)->get();
                        $learningUnitsIds = LearningsUnits::where(cn::LEARNING_UNITS_STRANDID_COL, $strand->id)->pluck(cn::LEARNING_UNITS_ID_COL)->toArray();
                        $LearningsUnitsLbl = LearningsUnits::pluck('name_'.app()->getLocale(),cn::LEARNING_UNITS_ID_COL)->toArray();
                        if(!empty($learningUnitsIds)){
                            if(!empty($learningUnitsIds)){
                                $learningUnitsId = $learningUnitsIds[0];
                                $gradeid = $gradeArray[0];
                                $gradeData = Grades::find($gradeid);
                                $GradeClassData =   GradeClassMapping::where([
                                                        cn::GRADE_CLASS_MAPPING_CURRICULUM_YEAR_ID_COL => $this->GetCurriculumYear(),
                                                        cn::GRADE_CLASS_MAPPING_GRADE_ID_COL => $gradeid,
                                                        cn::GRADE_CLASS_MAPPING_SCHOOL_ID_COL => $schoolId
                                                    ])
                                                    ->whereIn(cn::GRADE_CLASS_MAPPING_ID_COL,$classArray)
                                                    ->get();                                                                        
                                $countNoOfMasteredLearningUnits = 0;
                                foreach($learningUnitsIds as $learningUnitsId){
                                    // $learningObjectivesIds = LearningsObjectives::where(cn::LEARNING_OBJECTIVES_LEARNING_UNITID_COL, $learningUnitsId)->pluck(cn::LEARNING_OBJECTIVES_ID_COL)->toArray();
                                    // $LearningsObjectivesLbl = LearningsObjectives::where(cn::LEARNING_OBJECTIVES_LEARNING_UNITID_COL, $learningUnitsId)->pluck('title_'.app()->getLocale(),cn::LEARNING_OBJECTIVES_ID_COL)->toArray();
                                    $learningObjectivesIds = LearningsObjectives::IsAvailableQuestion()->where(cn::LEARNING_OBJECTIVES_LEARNING_UNITID_COL, $learningUnitsId)->pluck(cn::LEARNING_OBJECTIVES_ID_COL)->toArray();
                                    $LearningsObjectivesLbl = LearningsObjectives::IsAvailableQuestion()->where(cn::LEARNING_OBJECTIVES_LEARNING_UNITID_COL, $learningUnitsId)->pluck('title_'.app()->getLocale(),cn::LEARNING_OBJECTIVES_ID_COL)->toArray();
                                    if(!empty($learningObjectivesIds)){
                                        $no_of_learning_objectives = count($learningObjectivesIds);
                                        $countNoOfMasteredLearningObjectives = 0;
                                        foreach($learningObjectivesIds as $learningObjectivesId){
                                            $StudentLearningObjectiveAbility = 0;
                                            $countLearningObjectivesQuestion = 0;
                                            $learningObjectivesData = LearningsObjectives::find($learningObjectivesId);
                                            $StrandUnitsObjectivesMappingsId = StrandUnitsObjectivesMappings::where(cn::OBJECTIVES_MAPPINGS_STRAND_ID_COL,$strandId)
                                                        ->where(cn::OBJECTIVES_MAPPINGS_LEARNING_UNIT_ID_COL,$learningUnitsId)
                                                        ->where(cn::OBJECTIVES_MAPPINGS_LEARNING_OBJECTIVES_ID_COL,$learningObjectivesId)
                                                        ->pluck(cn::OBJECTIVES_MAPPINGS_ID_COL)->toArray();
                                            if(isset($StrandUnitsObjectivesMappingsId) && !empty($StrandUnitsObjectivesMappingsId)){
                                                $QuestionsList = Question::with('answers')
                                                                    ->whereIn(cn::QUESTION_OBJECTIVE_MAPPING_ID_COL,$StrandUnitsObjectivesMappingsId)
                                                                    ->orderBy(cn::QUESTION_TABLE_ID_COL)
                                                                    ->get()
                                                                    ->toArray();
                                                if(isset($QuestionsList) && !empty($QuestionsList)){
                                                    $QuestionsDataList = array_column($QuestionsList,cn::QUESTION_TABLE_ID_COL);
                                                    $stud_id = $student->id;
                                                    $StudentAttemptedExamIds = $this->GetStudentAttemptedExamIds($stud_id) ?? [];
                                                    
                                                    $ExamList = Exam::with(['attempt_exams' => fn($query) => $query->where(cn::ATTEMPT_EXAMS_STUDENT_STUDENT_ID, $stud_id)])
                                                                ->whereHas('attempt_exams', function($q) use($stud_id){
                                                                    $q->where(cn::ATTEMPT_EXAMS_STUDENT_STUDENT_ID, '=', $stud_id);
                                                                })
                                                                ->where(function ($query) use ($reportLearningType){
                                                                    if(isset($reportLearningType) && $reportLearningType == 1){  // $reportLearningType == 1 = 'Self-Learning Test
                                                                        $query->where(cn::EXAM_TYPE_COLS,1)  // 1 = test_type = 'self-Learning'
                                                                        ->where(cn::EXAM_TABLE_SELF_LEARNING_TEST_TYPE_COL,2);  // 2 = self_learning_test_type = 'test'
                                                                    }
                                                                    if(isset($reportLearningType) && $reportLearningType == 3){  // $reportLearningType == 3 = 'Test Only'
                                                                        $query->where(cn::EXAM_TYPE_COLS,3);  // 3 = test type = 'Test Only'
                                                                    }
                                                                    if(empty($reportLearningType)){
                                                                        $query->where(cn::EXAM_TYPE_COLS,3)
                                                                        ->orWhere(function ($q1) {
                                                                            $q1->where(cn::EXAM_TABLE_SELF_LEARNING_TEST_TYPE_COL,2)->where(cn::EXAM_TYPE_COLS,1);
                                                                        });
                                                                    }
                                                                })
                                                                ->where(cn::EXAM_CURRICULUM_YEAR_ID_COL,$this->GetCurriculumYear())
                                                                ->whereIn(cn::EXAM_TABLE_ID_COLS,$StudentAttemptedExamIds)
                                                                ->orderBy(cn::EXAM_TABLE_ID_COLS,'DESC')
                                                                ->get()
                                                                ->toArray();                                                    
                                                    if(isset($ExamList) && !empty($ExamList)){
                                                        $StudentLearningObjectiveAbility = 0;
                                                        $ApiRequestData = array();
                                                        foreach($ExamList as $ExamData){
                                                            if($countLearningObjectivesQuestion > $this->getGlobalConfiguration('question_window_size_of_learning_objective')){
                                                                break;
                                                            }
                                                            // Check No of maximum question per learning object is higher then then we will consider only latest questions for the number of question set into global configurations
                                                            if($countLearningObjectivesQuestion < $this->getGlobalConfiguration('question_window_size_of_learning_objective')){
                                                                if(isset($ExamData['attempt_exams'][0][cn::ATTEMPT_EXAMS_QUESTION_ANSWER_COL])){
                                                                    if(isset($ExamData['attempt_exams'][0][cn::ATTEMPT_EXAMS_QUESTION_ANSWER_COL])){
                                                                        $filterAttemptQuestionAnswer = json_decode($ExamData['attempt_exams'][0][cn::ATTEMPT_EXAMS_QUESTION_ANSWER_COL],true);
                                                                    }
                                                                }
                                                                foreach($filterAttemptQuestionAnswer as $filterAttemptQuestionAnswerKey => $filterAttemptQuestionAnswerValue){
                                                                    if(in_array($filterAttemptQuestionAnswerValue['question_id'],$QuestionsDataList)){
                                                                        $countLearningObjectivesQuestion += count(array_intersect(explode(',',$ExamData['question_ids']),$QuestionsDataList));
                                                                        $QuestionsDataListFinal[] = $filterAttemptQuestionAnswerValue['question_id'];
                                                                        $QuestionList = Question::with('answers')->where(cn::QUESTION_TABLE_ID_COL,$filterAttemptQuestionAnswerValue['question_id'])->get()->toArray();
                                                                        if(isset($PreConfigurationDifficultyLevel) && !empty($PreConfigurationDifficultyLevel) && isset($PreConfigurationDifficultyLevel[$QuestionList[0][cn::QUESTION_DIFFICULTY_LEVEL_COL]]) && !empty($PreConfigurationDifficultyLevel[$QuestionList[0][cn::QUESTION_DIFFICULTY_LEVEL_COL]])){
                                                                            $ApiRequestData['difficulty_list'][] = number_format($PreConfigurationDifficultyLevel[$QuestionList[0][cn::QUESTION_DIFFICULTY_LEVEL_COL]], 4, '.', '');
                                                                        }else{
                                                                            $ApiRequestData['difficulty_list'][] = 0;
                                                                        }
                                                                        $AnswerCount = 0;
                                                                        for($ans = 1; $ans <= 4; $ans++){
                                                                            if(trim($QuestionList[0]['answers']['answer'.$ans.'_en']) != ""){
                                                                                $AnswerCount++;
                                                                            }
                                                                        }
                                                                        $ApiRequestData['num_of_ans_list'][] = $AnswerCount;
                                                                        if($filterAttemptQuestionAnswerValue['answer'] == $QuestionList[0]['answers']['correct_answer_'.$ExamData['attempt_exams'][0]['language']]){
                                                                            $ApiRequestData['questions_results'][] = true;
                                                                        }else{
                                                                            $ApiRequestData['questions_results'][] = false;
                                                                        }
                                                                    }
                                                                }
                                                            }
                                                        }

                                                        $Ability = 0;
                                                        $StudentLearningObjectiveAbility = 0;
                                                        if(isset($ApiRequestData) && !empty($ApiRequestData)){
                                                            $requestPayload = new \Illuminate\Http\Request();
                                                            $requestPayload = $requestPayload->replace([
                                                                'questions_results'=> array(
                                                                    $ApiRequestData['questions_results']
                                                                ),
                                                                'num_of_ans_list' => $ApiRequestData['num_of_ans_list'],
                                                                'difficulty_list' => array_map('floatval', $ApiRequestData['difficulty_list']),
                                                                'max_student_num' => 1
                                                            ]);
                                                            $data = $this->AIApiService->getStudentProgressReport($requestPayload);
                                                            if(isset($data) && !empty($data) && isset($data[0]) && !empty($data[0])){
                                                                $Ability = $data[0];
                                                            }
                                                        }

                                                        // Check No of minimum question per learning object is less then then we will display "N/A" Learning progress report
                                                        if($countLearningObjectivesQuestion > $this->getGlobalConfiguration('min_no_question_per_study_progress')){
                                                            $StudentLearningObjectiveAbility = $Ability ?? 0;
                                                        }
                                                    }
                                                }
                                            }

                                            // Count No of mastered learning objectives
                                            if($this->CheckLearningObjectivesMastered($StudentLearningObjectiveAbility)){
                                                $countNoOfMasteredLearningObjectives++;
                                            }
                                        }
                                        // Stored Result in array
                                        $progressReportArray[$student->id]['report_data'][] = array(
                                            'no_of_learning_objectives' => $no_of_learning_objectives,
                                            'count_accomplished_learning_objectives' => $countNoOfMasteredLearningObjectives,
                                            'count_not_accomplished_learning_objectives' => ($no_of_learning_objectives - $countNoOfMasteredLearningObjectives),
                                            'accomplished_percentage' => round((($countNoOfMasteredLearningObjectives / $no_of_learning_objectives) * 100),1),
                                            'not_accomplished_percentage' => (100 - round((($countNoOfMasteredLearningObjectives / $no_of_learning_objectives) * 100),1))
                                        );
                                    }
                                }
                            }
                        }
                    }
                }
            }
            return view('backend.reports.progress_report.teacher.learning_unit_report',compact('StrandList','LearningUnitsList','StrandsLearningUnitsList','GradesList','grade_id','teachersClassList','reportLearningType','progressReportArray','strandDataLbl','LearningsUnitsLbl','LearningUnits','gradeid','classid','ColorCodes'));
        }catch(\Exception $exception){
            return back()->withError($exception->getMessage())->withInput();
        }
    }

    /**
     * USE : Progress report learning objectives display into principal panel
     */
    public function PrincipalProgressReportLearningObjective(Request $request){
        try{
            ini_set('max_execution_time', -1);
            if(isset($request->isFilter)){
                $isFilter = true;
            }else{
                $isFilter = false;
            }

            // Get Color Codes Array
            $ColorCodes = Helper::GetColorCodes();

            $progressReportArray = array();
            $LearningsUnitsLbl = array();
            $grade_id = array();
            $GradeClassListData = array();
            $class_type_id = array();
            $GradesList = array();
            $teachersClassList = array();
            $currentLang = ucwords(app()->getLocale());
            $schoolId = Auth::user()->{cn::USERS_SCHOOL_ID_COL};
            $roleId = Auth::user()->{cn::USERS_ROLE_ID_COL};
            $reportLearningType = "";
            $teacherListIds =   User::where([
                                    cn::USERS_ROLE_ID_COL => cn::TEACHER_ROLE_ID,
                                    cn::USERS_SCHOOL_ID_COL => $schoolId
                                ])
                                ->get()
                                ->pluck(cn::USERS_ID_COL);
            if(isset($request->reportLearningType) && !empty($request->reportLearningType)){
                $reportLearningType = $request->reportLearningType;
            }

            if(isset($request->grade_id) && !empty($request->grade_id)){
                $grade_id = $request->grade_id;
            }

            // Get pre-configured data for the questions
            $PreConfigurationDifficultyLevel = array();
            // $PreConfigurationDiffiltyLevelData = PreConfigurationDiffiltyLevel::get()->toArray();
            $PreConfigurationDiffiltyLevelData = PreConfigurationDiffiltyLevel::where(cn::PRE_CONFIGURE_DIFFICULTY_CURRICULUM_YEAR_ID_COL,$this->GetCurriculumYear())->get()->toArray();
            if(isset($PreConfigurationDiffiltyLevelData)){
                $PreConfigurationDifficultyLevel = array_column($PreConfigurationDiffiltyLevelData,cn::PRE_CONFIGURE_DIFFICULTY_TITLE_COL,cn::PRE_CONFIGURE_DIFFICULTY_DIFFICULTY_LEVEL_COL);
            }

            $teacherClassSubjectAssign = GradeSchoolMappings::where([
                                            cn::GRADES_MAPPING_SCHOOL_ID_COL => $schoolId,
                                            cn::GRADES_MAPPING_CURRICULUM_YEAR_ID_COL => $this->GetCurriculumYear()
                                        ])
                                        ->get();
            $gradeArray = array();
            $classArray = array();
            foreach($teacherClassSubjectAssign as $teacherGrades){
                // Store teacher grades into array
                $gradeData = Grades::find($teacherGrades['grade_id']);
                if(isset($request->grade_id) && !empty($request->grade_id)){
                    $gradeArray = $request->grade_id;
                }else{
                    $gradeArray[] = $gradeData->id;
                }
                $GradesList[] = array(
                                    'id' => $gradeData->id,
                                    'name' => $gradeData->name
                                );
            }
            $teacherGradesFirst = $teacherClassSubjectAssign[0];
            if(isset($request->grade_id) && !empty($request->grade_id)){
                $teacherClassSubjectAssignNew = GradeSchoolMappings::where([
                                                        cn::GRADES_MAPPING_SCHOOL_ID_COL => $schoolId,
                                                        cn::GRADES_MAPPING_CURRICULUM_YEAR_ID_COL => $this->GetCurriculumYear()
                                                    ])
                                                ->where(cn::GRADES_MAPPING_GRADE_ID_COL,$request->grade_id)->get();
                if(isset($teacherClassSubjectAssignNew[0]) && !empty($teacherClassSubjectAssignNew[0])){
                    $teacherGradesFirst = $teacherClassSubjectAssignNew[0];
                }
            }
            if(!empty($teacherGradesFirst['grade_id'])){
                $gradeData = Grades::find($teacherGradesFirst['grade_id']);
                if(!empty($gradeData)){
                    $GradeClassData =   GradeClassMapping::where([
                                            cn::GRADE_CLASS_MAPPING_CURRICULUM_YEAR_ID_COL => $this->GetCurriculumYear(),
                                            cn::GRADE_CLASS_MAPPING_GRADE_ID_COL => $teacherGradesFirst['grade_id'],
                                            cn::GRADE_CLASS_MAPPING_SCHOOL_ID_COL => $schoolId
                                        ])
                                        ->get();

                    foreach($GradeClassData as $gradeClasss){
                        if(isset($request->class_type_id) && !empty($request->class_type_id)){
                            $classArray = $request->class_type_id;
                        }else{
                            $classArray[] = $gradeClasss->id;
                        }
                        $teachersClassList[] = array(
                            'class_id' => $gradeClasss->id,
                            'class_name' => $gradeData->name.$gradeClasss->name
                        );
                    }
                }
            }
            // Get Strands data
            if(isset($request->learningReportStrand)){
                $strandData = Strands::all();
                $strand = Strands::whereIn(cn::STRANDS_ID_COL,$request->learningReportStrand)->first();
                $strandDataLbl = Strands::whereIn(cn::STRANDS_ID_COL,$request->learningReportStrand)->pluck('name_'.app()->getLocale(),cn::STRANDS_ID_COL)->toArray();
            }else{
                $strandData = Strands::all();
                $strand = Strands::first();
                $strandDataLbl = Strands::pluck('name_'.app()->getLocale(),cn::STRANDS_ID_COL)->toArray();
            }
            $LearningsUnitsLbl = LearningsUnits::pluck('name_'.app()->getLocale(),cn::LEARNING_UNITS_ID_COL)->toArray();
            $strandId = $strand->id;
            $LearningUnits = LearningsUnits::where(cn::LEARNING_UNITS_STRANDID_COL, $strand->id)->get();
            $learningUnitsIds = LearningsUnits::where(cn::LEARNING_UNITS_STRANDID_COL, $strand->id)->pluck(cn::LEARNING_UNITS_ID_COL)->toArray();
            // $learningObjectivesList = LearningsObjectives::where(cn::LEARNING_OBJECTIVES_LEARNING_UNITID_COL,$learningUnitsIds[0])->get();
            $learningObjectivesList = LearningsObjectives::IsAvailableQuestion()->where(cn::LEARNING_OBJECTIVES_LEARNING_UNITID_COL,$learningUnitsIds[0])->get();
            if(isset($request->learning_unit_id) && !empty($request->learning_unit_id)){
                $learningUnitsIds = LearningsUnits::where(cn::LEARNING_UNITS_STRANDID_COL, $strand->id)->where(cn::LEARNING_UNITS_ID_COL, $request->learning_unit_id)->pluck(cn::LEARNING_UNITS_ID_COL)->toArray();
                // $learningObjectivesList = LearningsObjectives::where(cn::LEARNING_OBJECTIVES_LEARNING_UNITID_COL,$learningUnitsIds[0])->get();
                $learningObjectivesList = LearningsObjectives::IsAvailableQuestion()->where(cn::LEARNING_OBJECTIVES_LEARNING_UNITID_COL,$learningUnitsIds[0])->get();
            }
            if(isset($gradeArray)){
                $gradeid = $gradeArray[0];
            }
            if(isset($classArray)){
                $classid = $classArray[0];
            }
            if($isFilter){
                if(!empty($learningUnitsIds)){                
                    $learningUnitsId = $learningUnitsIds[0];
                    // $learningObjectivesIds = LearningsObjectives::where(cn::LEARNING_OBJECTIVES_LEARNING_UNITID_COL, $learningUnitsId)->pluck(cn::LEARNING_OBJECTIVES_ID_COL)->toArray();
                    // $LearningsObjectivesLbl = LearningsObjectives::where(cn::LEARNING_OBJECTIVES_LEARNING_UNITID_COL, $learningUnitsId)->pluck('title_'.app()->getLocale(),cn::LEARNING_OBJECTIVES_ID_COL)->toArray();
                    $learningObjectivesIds = LearningsObjectives::IsAvailableQuestion()->where(cn::LEARNING_OBJECTIVES_LEARNING_UNITID_COL, $learningUnitsId)->pluck(cn::LEARNING_OBJECTIVES_ID_COL)->toArray();
                    $LearningsObjectivesLbl = LearningsObjectives::IsAvailableQuestion()->where(cn::LEARNING_OBJECTIVES_LEARNING_UNITID_COL, $learningUnitsId)->pluck('title_'.app()->getLocale(),cn::LEARNING_OBJECTIVES_ID_COL)->toArray();
                    if(!empty($learningObjectivesIds)){
                        $no_of_learning_objectives = count($learningObjectivesIds);
                        if(isset($gradeArray)){
                            $gradeid = $gradeArray[0];
                            $gradeData = Grades::find($gradeid);
                            $GradeClassData =   GradeClassMapping::where([
                                                    cn::GRADE_CLASS_MAPPING_CURRICULUM_YEAR_ID_COL => $this->GetCurriculumYear(),
                                                    cn::GRADE_CLASS_MAPPING_GRADE_ID_COL => $gradeid,
                                                    cn::GRADE_CLASS_MAPPING_SCHOOL_ID_COL => $schoolId
                                                ])
                                                ->whereIn(cn::GRADE_CLASS_MAPPING_ID_COL,$classArray)
                                                ->get();
                            if(isset($GradeClassData) && !empty($GradeClassData)){
                                $gradeClasss = $GradeClassData[0];
                                // $studentList =  User::where(cn::USERS_GRADE_ID_COL,$gradeid)
                                //                 ->where(cn::USERS_CLASS_ID_COL,$gradeClasss->id)
                                //                 ->where(cn::USERS_SCHOOL_ID_COL,$schoolId)
                                //                 ->where(cn::USERS_ROLE_ID_COL,cn::STUDENT_ROLE_ID)
                                //                 ->get();
                                $studentList =  User::where([
                                                    cn::USERS_ROLE_ID_COL => cn::STUDENT_ROLE_ID,
                                                    cn::USERS_SCHOOL_ID_COL => $schoolId
                                                ])
                                                ->get()
                                                ->where('CurriculumYearGradeId',$gradeid)
                                                ->where('CurriculumYearClassId',$gradeClasss->id);
                                if(isset($studentList) && !empty($studentList)){
                                    foreach($studentList as $student){
                                        $countNoOfMasteredLearningObjectives = 0;
                                        $progressReportArray[$strandId][$learningUnitsId][$gradeData->name.'-'.$gradeClasss->name][$student->id]['student_data'][] = $student->toArray();
                                        foreach($learningObjectivesIds as $learningObjectivesId){
                                            $progressReportArray[$strandId][$learningUnitsId][$gradeData->name.'-'.$gradeClasss->name][$student->id]['no_of_learning_objectives'] = count($learningObjectivesIds);
                                            $StudentLearningObjectiveAbility = 0;
                                            $countLearningObjectivesQuestion = 0;
                                            $learningObjectivesData = LearningsObjectives::find($learningObjectivesId);
                                            $StrandUnitsObjectivesMappingsId =  StrandUnitsObjectivesMappings::where(cn::OBJECTIVES_MAPPINGS_STRAND_ID_COL,$strandId)
                                                                                ->where(cn::OBJECTIVES_MAPPINGS_LEARNING_UNIT_ID_COL,$learningUnitsId)
                                                                                ->where(cn::OBJECTIVES_MAPPINGS_LEARNING_OBJECTIVES_ID_COL,$learningObjectivesId)
                                                                                ->pluck(cn::OBJECTIVES_MAPPINGS_ID_COL)->toArray();
                                            if(isset($StrandUnitsObjectivesMappingsId) && !empty($StrandUnitsObjectivesMappingsId)){
                                                $QuestionsList = Question::with('answers')
                                                                    ->whereIn(cn::QUESTION_OBJECTIVE_MAPPING_ID_COL,$StrandUnitsObjectivesMappingsId)
                                                                    ->orderBy(cn::QUESTION_TABLE_ID_COL)
                                                                    ->get()
                                                                    ->toArray();
                                                if(isset($QuestionsList) && !empty($QuestionsList)){
                                                    $QuestionsDataList = array_column($QuestionsList,cn::QUESTION_TABLE_ID_COL);
                                                    $stud_id = $student->id;
                                                    $StudentAttemptedExamIds = $this->GetStudentAttemptedExamIds($stud_id) ?? [];
                                                    
                                                    $ExamList = Exam::with(['attempt_exams' => fn($query) => $query->where(cn::ATTEMPT_EXAMS_STUDENT_STUDENT_ID, $stud_id)])
                                                                ->whereHas('attempt_exams', function($q) use($stud_id){
                                                                    $q->where(cn::ATTEMPT_EXAMS_STUDENT_STUDENT_ID, '=', $stud_id);
                                                                })
                                                                ->where(function ($query) use ($reportLearningType){
                                                                    if(isset($reportLearningType) && $reportLearningType == 1){  // $reportLearningType == 1 = 'Self-Learning Test
                                                                        $query->where(cn::EXAM_TYPE_COLS,1)  // 1 = test_type = 'self-Learning'
                                                                        ->where(cn::EXAM_TABLE_SELF_LEARNING_TEST_TYPE_COL,2);  // 2 = self_learning_test_type = 'test'
                                                                    }
                                                                    if(isset($reportLearningType) && $reportLearningType == 3){  // $reportLearningType == 3 = 'Test Only'
                                                                        $query->where(cn::EXAM_TYPE_COLS,3);  // 3 = test type = 'Test Only'
                                                                    }
                                                                    if(empty($reportLearningType)){
                                                                        $query->where(cn::EXAM_TYPE_COLS,3)
                                                                        ->orWhere(function ($q1) {
                                                                            $q1->where(cn::EXAM_TABLE_SELF_LEARNING_TEST_TYPE_COL,2)->where(cn::EXAM_TYPE_COLS,1);
                                                                        });
                                                                    }
                                                                })
                                                                ->where(cn::EXAM_CURRICULUM_YEAR_ID_COL,$this->GetCurriculumYear())
                                                                ->whereIn(cn::EXAM_TABLE_ID_COLS,$StudentAttemptedExamIds)
                                                                ->orderBy(cn::EXAM_TABLE_ID_COLS,'DESC')
                                                                ->get()
                                                                ->toArray();                                                    
                                                    if(isset($ExamList) && !empty($ExamList)){
                                                        $StudentLearningObjectiveAbility = 0;
                                                        $ApiRequestData = array();
                                                        foreach($ExamList as $ExamData){
                                                            if($countLearningObjectivesQuestion > $this->getGlobalConfiguration('question_window_size_of_learning_objective')){
                                                                break;
                                                            }
                                                            // Check No of maximum question per learning object is higher then then we will consider only latest questions for the number of question set into global configurations
                                                            if($countLearningObjectivesQuestion < $this->getGlobalConfiguration('question_window_size_of_learning_objective')){
                                                                if(isset($ExamData['attempt_exams'][0][cn::ATTEMPT_EXAMS_QUESTION_ANSWER_COL])){
                                                                    if(isset($ExamData['attempt_exams'][0][cn::ATTEMPT_EXAMS_QUESTION_ANSWER_COL])){
                                                                        $filterAttemptQuestionAnswer = json_decode($ExamData['attempt_exams'][0][cn::ATTEMPT_EXAMS_QUESTION_ANSWER_COL],true);
                                                                    }
                                                                }
                                                                foreach($filterAttemptQuestionAnswer as $filterAttemptQuestionAnswerKey => $filterAttemptQuestionAnswerValue){
                                                                    if(in_array($filterAttemptQuestionAnswerValue['question_id'],$QuestionsDataList)){
                                                                        $countLearningObjectivesQuestion += count(array_intersect(explode(',',$ExamData['question_ids']),$QuestionsDataList));
                                                                        $QuestionsDataListFinal[] = $filterAttemptQuestionAnswerValue['question_id'];
                                                                        $QuestionList = Question::with('answers')->where(cn::QUESTION_TABLE_ID_COL,$filterAttemptQuestionAnswerValue['question_id'])->get()->toArray();
                                                                        if(isset($PreConfigurationDifficultyLevel) && !empty($PreConfigurationDifficultyLevel) && isset($PreConfigurationDifficultyLevel[$QuestionList[0][cn::QUESTION_DIFFICULTY_LEVEL_COL]]) && !empty($PreConfigurationDifficultyLevel[$QuestionList[0][cn::QUESTION_DIFFICULTY_LEVEL_COL]])){
                                                                            $ApiRequestData['difficulty_list'][] = number_format($PreConfigurationDifficultyLevel[$QuestionList[0][cn::QUESTION_DIFFICULTY_LEVEL_COL]], 4, '.', '');
                                                                        }else{
                                                                            $ApiRequestData['difficulty_list'][] = 0;
                                                                        }
                                                                        $AnswerCount = 0;
                                                                        for($ans = 1; $ans <= 4; $ans++){
                                                                            if(trim($QuestionList[0]['answers']['answer'.$ans.'_en']) != ""){
                                                                                $AnswerCount++;
                                                                            }
                                                                        }
                                                                        $ApiRequestData['num_of_ans_list'][] = $AnswerCount;
                                                                        if($filterAttemptQuestionAnswerValue['answer'] == $QuestionList[0]['answers']['correct_answer_'.$ExamData['attempt_exams'][0]['language']]){
                                                                            $ApiRequestData['questions_results'][] = true;
                                                                        }else{
                                                                            $ApiRequestData['questions_results'][] = false;
                                                                        }
                                                                    }
                                                                }
                                                            }
                                                        }
        
                                                        $Ability = 0;
                                                        if(isset($ApiRequestData) && !empty($ApiRequestData)){
                                                            $requestPayload = new \Illuminate\Http\Request();
                                                            $requestPayload = $requestPayload->replace([
                                                                'questions_results'=> array(
                                                                    $ApiRequestData['questions_results']
                                                                ),
                                                                'num_of_ans_list' => $ApiRequestData['num_of_ans_list'],
                                                                'difficulty_list' => array_map('floatval', $ApiRequestData['difficulty_list']),
                                                                'max_student_num' => 1
                                                            ]);
                                                            $data = $this->AIApiService->getStudentProgressReport($requestPayload);
                                                            if(isset($data) && !empty($data) && isset($data[0]) && !empty($data[0])){
                                                                $Ability = $data[0];
                                                            }
                                                        }
        
                                                        // Check No of minimum question per learning object is less then then we will display "N/A" Learning progress report
                                                        if($countLearningObjectivesQuestion > $this->getGlobalConfiguration('min_no_question_per_study_progress')){
                                                            $StudentLearningObjectiveAbility = $Ability ?? 0;
                                                            // Store array into student ability                                                                
                                                            $progressReportArray[$strandId][$learningUnitsId][$gradeData->name.'-'.$gradeClasss->name][$student->id]['report_data'][] = array(
                                                                'learning_objective_number' => $learningObjectivesData->foci_number,
                                                                'LearningsObjectives' => $learningObjectivesData->foci_number.' '.$this->setLearningObjectivesTitle($LearningsObjectivesLbl[$learningObjectivesId]),
                                                                'ability' => $StudentLearningObjectiveAbility,
                                                                'normalizedAbility' => Helper::getNormalizedAbility($StudentLearningObjectiveAbility),
                                                                'ShortNormalizedAbility' => Helper::getShortNormalizedAbility($StudentLearningObjectiveAbility),
                                                                'studystatus' => Helper::getAbilityType($StudentLearningObjectiveAbility),
                                                                'studyStatusColor' => Helper::getGlobalConfiguration(Helper::getAbilityType($StudentLearningObjectiveAbility))
                                                            );
                                                        }else{
                                                            // Store array into student ability                                                                
                                                            $progressReportArray[$strandId][$learningUnitsId][$gradeData->name.'-'.$gradeClasss->name][$student->id]['report_data'][] = array(
                                                                'learning_objective_number' => $learningObjectivesData->foci_number,
                                                                'LearningsObjectives' => $learningObjectivesData->foci_number.' '.$this->setLearningObjectivesTitle($LearningsObjectivesLbl[$learningObjectivesId]),
                                                                'ability' => $StudentLearningObjectiveAbility,
                                                                'normalizedAbility' => $StudentLearningObjectiveAbility,
                                                                'ShortNormalizedAbility' => $StudentLearningObjectiveAbility,
                                                                'studystatus' => Helper::getAbilityType($StudentLearningObjectiveAbility),
                                                                'studyStatusColor' => Helper::getGlobalConfiguration(Helper::getAbilityType($StudentLearningObjectiveAbility))
                                                            );
                                                        }
        
                                                        
                                                    }else{
                                                        $progressReportArray[$strandId][$learningUnitsId][$gradeData->name.'-'.$gradeClasss->name][$student->id]['report_data'][] = array(
                                                            'learning_objective_number' => $learningObjectivesData->foci_number,
                                                            'LearningsObjectives' => $learningObjectivesData->foci_number.' '.$this->setLearningObjectivesTitle($LearningsObjectivesLbl[$learningObjectivesId]),
                                                            'ability' => $StudentLearningObjectiveAbility,
                                                            'normalizedAbility' => $StudentLearningObjectiveAbility,
                                                            'ShortNormalizedAbility' => $StudentLearningObjectiveAbility,
                                                            'studystatus' => Helper::getAbilityType($StudentLearningObjectiveAbility),
                                                            'studyStatusColor' => Helper::getGlobalConfiguration('incomplete_color')
                                                        );
                                                    }
                                                }else{
                                                    $progressReportArray[$strandId][$learningUnitsId][$gradeData->name.'-'.$gradeClasss->name][$student->id]['report_data'][] = array(
                                                        'learning_objective_number' => $learningObjectivesData->foci_number,
                                                        'LearningsObjectives' => $learningObjectivesData->foci_number.' '.$this->setLearningObjectivesTitle($LearningsObjectivesLbl[$learningObjectivesId]),
                                                        'ability' => $StudentLearningObjectiveAbility,
                                                        'normalizedAbility' => $StudentLearningObjectiveAbility,
                                                        'ShortNormalizedAbility' => $StudentLearningObjectiveAbility,
                                                        'studystatus' => Helper::getAbilityType($StudentLearningObjectiveAbility),
                                                        'studyStatusColor' => Helper::getGlobalConfiguration('incomplete_color')
                                                    );
                                                }
                                            }
                                            // Count No of mastered learning objectives
                                            if($this->CheckLearningObjectivesMastered($StudentLearningObjectiveAbility)){
                                                $countNoOfMasteredLearningObjectives++;
                                            }
                                        }
                                        
                                        //  Set Master objectives details for the students
                                        $progressReportArray[$strandId][$learningUnitsId][$gradeData->name.'-'.$gradeClasss->name][$student->id]['master_objectives'] = array(
                                            'no_of_learning_objectives' => $no_of_learning_objectives,
                                            'count_accomplished_learning_objectives' => $countNoOfMasteredLearningObjectives,
                                            'count_not_accomplished_learning_objectives' => ($no_of_learning_objectives - $countNoOfMasteredLearningObjectives),
                                            'accomplished_percentage' => round((($countNoOfMasteredLearningObjectives / $no_of_learning_objectives) * 100),1),
                                            'not_accomplished_percentage' => (100 - round((($countNoOfMasteredLearningObjectives / $no_of_learning_objectives) * 100),1))
                                        );
                                    }
                                }
                            }
                        }
                    }
                }
            }
            return view('backend.reports.progress_report.principal.learning_objective_report',compact('strandData','GradesList','grade_id','teachersClassList','reportLearningType','progressReportArray','strandDataLbl','LearningsUnitsLbl','learningObjectivesList','LearningUnits','gradeid','classid','schoolId','roleId','ColorCodes'));
        }catch(\Exception $exception){
            return back()->withError($exception->getMessage())->withInput();
        }
    }

    /**
     * USE : Progress report learning Unit display into principal panel
     */
    public function PrincipalProgressReportLearningUnits(Request $request){
        try{
            ini_set('max_execution_time', -1);
            if(isset($request->isFilter)){
                $isFilter = true;
            }else{
                $isFilter = false;
            }

            // Get Color Codes Array
            $ColorCodes = Helper::GetColorCodes();

            $progressReportArray = array();
            $LearningsUnitsLbl = array();
            $grade_id = array();
            $GradeClassListData = array();
            $class_type_id = array();
            $GradesList = array();
            $teachersClassList = array();
            $currentLang = ucwords(app()->getLocale());
            $schoolId = Auth::user()->{cn::USERS_SCHOOL_ID_COL};
            $roleId = Auth::user()->{cn::USERS_ROLE_ID_COL};
            $reportLearningType = "";
            if(isset($request->reportLearningType) && !empty($request->reportLearningType)){
                $reportLearningType = $request->reportLearningType;
            }

            if(isset($request->grade_id) && !empty($request->grade_id)){
                $grade_id = $request->grade_id;
            }

            // Get pre-configured data for the questions
            $PreConfigurationDifficultyLevel = array();
            // $PreConfigurationDiffiltyLevelData = PreConfigurationDiffiltyLevel::get()->toArray();
            $PreConfigurationDiffiltyLevelData = PreConfigurationDiffiltyLevel::where(cn::PRE_CONFIGURE_DIFFICULTY_CURRICULUM_YEAR_ID_COL,$this->GetCurriculumYear())->get()->toArray();
            if(isset($PreConfigurationDiffiltyLevelData)){
                $PreConfigurationDifficultyLevel = array_column($PreConfigurationDiffiltyLevelData,cn::PRE_CONFIGURE_DIFFICULTY_TITLE_COL,cn::PRE_CONFIGURE_DIFFICULTY_DIFFICULTY_LEVEL_COL);
            }

            //$teacherClassSubjectAssign = GradeSchoolMappings::where(cn::GRADES_MAPPING_SCHOOL_ID_COL,$schoolId)->get();
            $principalGradesList = GradeSchoolMappings::where(cn::GRADES_MAPPING_SCHOOL_ID_COL,$schoolId)->get();
            $gradeArray = array();
            $classArray = array();
            foreach($principalGradesList as $grades){
                // Store teacher grades into array
                $gradeData = Grades::find($grades['grade_id']);
                if(isset($request->grade_id) && !empty($request->grade_id)){
                    $gradeArray = $request->grade_id;
                }else{
                    $gradeArray[] = $gradeData->id;
                }
                $GradesList[] = array(
                                    'id' => $gradeData->id,
                                    'name' => $gradeData->name
                                );
            }
            $principalGradesFirst=$principalGradesList[0];
            if(isset($request->grade_id) && !empty($request->grade_id)){
                $teacherClassSubjectAssignNew = GradeSchoolMappings::where([
                                                    cn::GRADES_MAPPING_CURRICULUM_YEAR_ID_COL => $this->GetCurriculumYear(),
                                                    cn::GRADES_MAPPING_SCHOOL_ID_COL => $schoolId,
                                                    cn::GRADES_MAPPING_GRADE_ID_COL => $request->grade_id
                                                ])->get();
                if(isset($teacherClassSubjectAssignNew[0]) && !empty($teacherClassSubjectAssignNew[0])){
                    $principalGradesFirst = $teacherClassSubjectAssignNew[0];
                }
            }
            if(!empty($principalGradesFirst['grade_id'])){
                $gradeData = Grades::find($principalGradesFirst['grade_id']);
                if(!empty($gradeData)){
                    $GradeClassData = GradeClassMapping::where([
                                        cn::GRADE_CLASS_MAPPING_CURRICULUM_YEAR_ID_COL => $this->GetCurriculumYear(),
                                        cn::GRADE_CLASS_MAPPING_GRADE_ID_COL => $principalGradesFirst['grade_id'],
                                        cn::GRADE_CLASS_MAPPING_SCHOOL_ID_COL => $schoolId
                                    ])->get();
                    foreach($GradeClassData as $gradeClasss){
                        if(isset($request->class_type_id) && !empty($request->class_type_id)){
                            $classArray = $request->class_type_id;
                        }else{
                            $classArray[] = $gradeClasss->id;
                        }
                        $principalClassList[] = array(
                            'class_id' => $gradeClasss->id,
                            'class_name' => $gradeData->name.$gradeClasss->name
                        );
                    }
                }
            }
            // Get Strands data
            if(isset($gradeArray)){
                $gradeid = $gradeArray[0];
            }
            if(isset($classArray)){
                $classid = $classArray[0];
            }
            $StrandList = Strands::all();
            $LearningUnitsList = LearningsUnits::all();
            $StrandsLearningUnitsList = Strands::with('LearningUnit')->get()->toArray();
            
            $strandDataLbl = Strands::pluck('name_'.app()->getLocale(),cn::STRANDS_ID_COL)->toArray();
            if(!empty($StrandList)){
                $gradeid = $gradeArray[0];
                $gradeData = Grades::find($gradeid);
                $GradeClassData =   GradeClassMapping::where([
                                        cn::GRADE_CLASS_MAPPING_CURRICULUM_YEAR_ID_COL => $this->GetCurriculumYear(),
                                        cn::GRADE_CLASS_MAPPING_GRADE_ID_COL => $gradeid,
                                        cn::GRADE_CLASS_MAPPING_SCHOOL_ID_COL => $schoolId
                                    ])
                                    ->whereIn(cn::GRADE_CLASS_MAPPING_ID_COL,$classArray)
                                    ->get();
                $gradeClasss = $GradeClassData[0];
                // $studentList =  User::where(cn::USERS_GRADE_ID_COL,$gradeid)
                //                 ->where(cn::USERS_CLASS_ID_COL,$gradeClasss->id)
                //                 ->where(cn::USERS_SCHOOL_ID_COL,$schoolId)
                //                 ->where(cn::USERS_ROLE_ID_COL,cn::STUDENT_ROLE_ID)
                //                 ->get();
                $studentList =  User::where([
                                    cn::USERS_SCHOOL_ID_COL => $schoolId,
                                    cn::USERS_ROLE_ID_COL => cn::STUDENT_ROLE_ID
                                ])
                                ->get()
                                ->where('CurriculumYearGradeId',$gradeid)
                                ->where('CurriculumYearClassId',$gradeClasss->id);
                foreach($studentList as $student){
                    $progressReportArray[$student->id]['student_data'][] = $student->toArray();
                    $no_of_learning_unit = count($LearningUnitsList);
                    $progressReportArray[$student->id]['no_of_learning_unit'] = $no_of_learning_unit;
                    foreach($StrandList as $strand){
                        $strandId = $strand->id;
                        $LearningUnits = LearningsUnits::where(cn::LEARNING_UNITS_STRANDID_COL, $strand->id)->get();
                        $learningUnitsIds = LearningsUnits::where(cn::LEARNING_UNITS_STRANDID_COL, $strand->id)->pluck(cn::LEARNING_UNITS_ID_COL)->toArray();
                        $LearningsUnitsLbl = LearningsUnits::pluck('name_'.app()->getLocale(),cn::LEARNING_UNITS_ID_COL)->toArray();
                        if(!empty($learningUnitsIds)){
                            if(!empty($learningUnitsIds)){
                                $learningUnitsId = $learningUnitsIds[0];
                                $gradeid = $gradeArray[0];
                                $gradeData = Grades::find($gradeid);
                                $GradeClassData =   GradeClassMapping::where(cn::GRADE_CLASS_MAPPING_GRADE_ID_COL,$gradeid)
                                                    ->whereIn(cn::GRADE_CLASS_MAPPING_ID_COL,$classArray)
                                                    ->where(cn::GRADE_CLASS_MAPPING_SCHOOL_ID_COL,$schoolId)
                                                    ->get();                                                                        
                                $countNoOfMasteredLearningUnits = 0;
                                foreach($learningUnitsIds as $learningUnitsId){
                                    // $learningObjectivesIds = LearningsObjectives::where(cn::LEARNING_OBJECTIVES_LEARNING_UNITID_COL, $learningUnitsId)->pluck(cn::LEARNING_OBJECTIVES_ID_COL)->toArray();
                                    // $LearningsObjectivesLbl = LearningsObjectives::where(cn::LEARNING_OBJECTIVES_LEARNING_UNITID_COL, $learningUnitsId)->pluck('title_'.app()->getLocale(),cn::LEARNING_OBJECTIVES_ID_COL)->toArray();
                                    $learningObjectivesIds = LearningsObjectives::IsAvailableQuestion()->where(cn::LEARNING_OBJECTIVES_LEARNING_UNITID_COL, $learningUnitsId)->pluck(cn::LEARNING_OBJECTIVES_ID_COL)->toArray();
                                    $LearningsObjectivesLbl = LearningsObjectives::IsAvailableQuestion()->where(cn::LEARNING_OBJECTIVES_LEARNING_UNITID_COL, $learningUnitsId)->pluck('title_'.app()->getLocale(),cn::LEARNING_OBJECTIVES_ID_COL)->toArray();
                                    if(!empty($learningObjectivesIds)){
                                        $no_of_learning_objectives = count($learningObjectivesIds);
                                        $countNoOfMasteredLearningObjectives = 0;
                                        foreach($learningObjectivesIds as $learningObjectivesId){
                                            $StudentLearningObjectiveAbility = 0;
                                            $countLearningObjectivesQuestion = 0;
                                            $learningObjectivesData = LearningsObjectives::find($learningObjectivesId);
                                            $StrandUnitsObjectivesMappingsId = StrandUnitsObjectivesMappings::where(cn::OBJECTIVES_MAPPINGS_STRAND_ID_COL,$strandId)
                                                        ->where(cn::OBJECTIVES_MAPPINGS_LEARNING_UNIT_ID_COL,$learningUnitsId)
                                                        ->where(cn::OBJECTIVES_MAPPINGS_LEARNING_OBJECTIVES_ID_COL,$learningObjectivesId)
                                                        ->pluck(cn::OBJECTIVES_MAPPINGS_ID_COL)->toArray();
                                            if(isset($StrandUnitsObjectivesMappingsId) && !empty($StrandUnitsObjectivesMappingsId)){
                                                $QuestionsList = Question::with('answers')
                                                                    ->whereIn(cn::QUESTION_OBJECTIVE_MAPPING_ID_COL,$StrandUnitsObjectivesMappingsId)
                                                                    ->orderBy(cn::QUESTION_TABLE_ID_COL)
                                                                    ->get()
                                                                    ->toArray();
                                                if(isset($QuestionsList) && !empty($QuestionsList)){
                                                    $QuestionsDataList = array_column($QuestionsList,cn::QUESTION_TABLE_ID_COL);
                                                    $stud_id = $student->id;
                                                    $StudentAttemptedExamIds = $this->GetStudentAttemptedExamIds($stud_id) ?? [];
                                                    
                                                    $ExamList = Exam::with(['attempt_exams' => fn($query) => $query->where(cn::ATTEMPT_EXAMS_STUDENT_STUDENT_ID, $stud_id)])
                                                                ->whereHas('attempt_exams', function($q) use($stud_id){
                                                                    $q->where(cn::ATTEMPT_EXAMS_STUDENT_STUDENT_ID, $stud_id);
                                                                })
                                                                ->where(function ($query) use ($reportLearningType){
                                                                    if(isset($reportLearningType) && $reportLearningType == 1){  // $reportLearningType == 1 = 'Self-Learning Test
                                                                        $query->where(cn::EXAM_TYPE_COLS,1)  // 1 = test_type = 'self-Learning'
                                                                        ->where(cn::EXAM_TABLE_SELF_LEARNING_TEST_TYPE_COL,2);  // 2 = self_learning_test_type = 'test'
                                                                    }
                                                                    if(isset($reportLearningType) && $reportLearningType == 3){  // $reportLearningType == 3 = 'Test Only'
                                                                        $query->where(cn::EXAM_TYPE_COLS,3);  // 3 = test type = 'Test Only'
                                                                    }
                                                                    if(empty($reportLearningType)){
                                                                        $query->where(cn::EXAM_TYPE_COLS,3)
                                                                        ->orWhere(function ($q1) {
                                                                            $q1->where(cn::EXAM_TABLE_SELF_LEARNING_TEST_TYPE_COL,2)->where(cn::EXAM_TYPE_COLS,1);
                                                                        });
                                                                    }
                                                                })
                                                                ->where(cn::EXAM_CURRICULUM_YEAR_ID_COL,$this->GetCurriculumYear())
                                                                ->whereIn(cn::EXAM_TABLE_ID_COLS,$StudentAttemptedExamIds)
                                                                ->orderBy(cn::EXAM_TABLE_ID_COLS,'DESC')
                                                                ->get()
                                                                ->toArray();                                                    
                                                    if(isset($ExamList) && !empty($ExamList)){
                                                        $StudentLearningObjectiveAbility = 0;
                                                        $ApiRequestData = array();
                                                        foreach($ExamList as $ExamData){
                                                            if($countLearningObjectivesQuestion > $this->getGlobalConfiguration('question_window_size_of_learning_objective')){
                                                                break;
                                                            }
                                                            // Check No of maximum question per learning object is higher then then we will consider only latest questions for the number of question set into global configurations
                                                            if($countLearningObjectivesQuestion < $this->getGlobalConfiguration('question_window_size_of_learning_objective')){
                                                                if(isset($ExamData['attempt_exams'][0][cn::ATTEMPT_EXAMS_QUESTION_ANSWER_COL])){
                                                                    if(isset($ExamData['attempt_exams'][0][cn::ATTEMPT_EXAMS_QUESTION_ANSWER_COL])){
                                                                        $filterAttemptQuestionAnswer = json_decode($ExamData['attempt_exams'][0][cn::ATTEMPT_EXAMS_QUESTION_ANSWER_COL],true);
                                                                    }
                                                                }
                                                                foreach($filterAttemptQuestionAnswer as $filterAttemptQuestionAnswerKey => $filterAttemptQuestionAnswerValue){
                                                                    if(in_array($filterAttemptQuestionAnswerValue['question_id'],$QuestionsDataList)){
                                                                        $countLearningObjectivesQuestion += count(array_intersect(explode(',',$ExamData['question_ids']),$QuestionsDataList));
                                                                        $QuestionsDataListFinal[] = $filterAttemptQuestionAnswerValue['question_id'];
                                                                        $QuestionList = Question::with('answers')->where(cn::QUESTION_TABLE_ID_COL,$filterAttemptQuestionAnswerValue['question_id'])->get()->toArray();
                                                                        if(isset($PreConfigurationDifficultyLevel) && !empty($PreConfigurationDifficultyLevel) && isset($PreConfigurationDifficultyLevel[$QuestionList[0][cn::QUESTION_DIFFICULTY_LEVEL_COL]]) && !empty($PreConfigurationDifficultyLevel[$QuestionList[0][cn::QUESTION_DIFFICULTY_LEVEL_COL]])){
                                                                            $ApiRequestData['difficulty_list'][] = number_format($PreConfigurationDifficultyLevel[$QuestionList[0][cn::QUESTION_DIFFICULTY_LEVEL_COL]], 4, '.', '');
                                                                        }else{
                                                                            $ApiRequestData['difficulty_list'][] = 0;
                                                                        }
                                                                        $AnswerCount = 0;
                                                                        for($ans = 1; $ans <= 4; $ans++){
                                                                            if(trim($QuestionList[0]['answers']['answer'.$ans.'_en']) != ""){
                                                                                $AnswerCount++;
                                                                            }
                                                                        }
                                                                        $ApiRequestData['num_of_ans_list'][] = $AnswerCount;
                                                                        if($filterAttemptQuestionAnswerValue['answer'] == $QuestionList[0]['answers']['correct_answer_'.$ExamData['attempt_exams'][0]['language']]){
                                                                            $ApiRequestData['questions_results'][] = true;
                                                                        }else{
                                                                            $ApiRequestData['questions_results'][] = false;
                                                                        }
                                                                    }
                                                                }
                                                            }
                                                        }

                                                        $Ability = 0;
                                                        $StudentLearningObjectiveAbility = 0;
                                                        if(isset($ApiRequestData) && !empty($ApiRequestData)){
                                                            $requestPayload = new \Illuminate\Http\Request();
                                                            $requestPayload = $requestPayload->replace([
                                                                'questions_results'=> array(
                                                                    $ApiRequestData['questions_results']
                                                                ),
                                                                'num_of_ans_list' => $ApiRequestData['num_of_ans_list'],
                                                                'difficulty_list' => array_map('floatval', $ApiRequestData['difficulty_list']),
                                                                'max_student_num' => 1
                                                            ]);
                                                            $data = $this->AIApiService->getStudentProgressReport($requestPayload);
                                                            if(isset($data) && !empty($data) && isset($data[0]) && !empty($data[0])){
                                                                $Ability = $data[0];
                                                            }
                                                        }

                                                        // Check No of minimum question per learning object is less then then we will display "N/A" Learning progress report
                                                        if($countLearningObjectivesQuestion > $this->getGlobalConfiguration('min_no_question_per_study_progress')){
                                                            $StudentLearningObjectiveAbility = $Ability ?? 0;
                                                        }
                                                    }
                                                }
                                            }

                                            // Count No of mastered learning objectives
                                            if($this->CheckLearningObjectivesMastered($StudentLearningObjectiveAbility)){
                                                $countNoOfMasteredLearningObjectives++;
                                            }
                                        }

                                        // Stored Result in array
                                        $progressReportArray[$student->id]['report_data'][] = array(
                                            'no_of_learning_objectives' => $no_of_learning_objectives,
                                            'count_accomplished_learning_objectives' => $countNoOfMasteredLearningObjectives,
                                            'count_not_accomplished_learning_objectives' => ($no_of_learning_objectives - $countNoOfMasteredLearningObjectives),
                                            'accomplished_percentage' => round((($countNoOfMasteredLearningObjectives / $no_of_learning_objectives) * 100),1),
                                            'not_accomplished_percentage' => (100 - round((($countNoOfMasteredLearningObjectives / $no_of_learning_objectives) * 100),1))
                                        );
                                    }
                                }
                            }
                        }
                    }
                }
            }
            return view('backend.reports.progress_report.principal.learning_unit_report',compact('StrandList','LearningUnitsList','StrandsLearningUnitsList','GradesList','grade_id','principalClassList','reportLearningType','progressReportArray','strandDataLbl','LearningsUnitsLbl','LearningUnits','gradeid','classid','ColorCodes'));
        } catch (\Exception $exception) {
            return back()->withError($exception->getMessage())->withInput();
        }
    }

    /**
     * USE : Progress report learning objectives display into student panel
     */
    public function StudentProgressReportLearningObjective(Request $request, $studentId=0){
        try{
            if($studentId==0){
                $studentData = User::find(Auth::user()->id);
            }else{
                $studentData = User::find($studentId);
            }

            ini_set('max_execution_time', -1);
            $showMenu = false;
            if(isset($request->showMenu) && $request->showMenu==true){
                $showMenu = true;
            }

            if(isset($request->isFilter)){
                $isFilter = true;
            }else{
                $isFilter = false;
            }

            // Get Color Codes Array
            $ColorCodes = Helper::GetColorCodes();

            $currentLang = ucwords(app()->getLocale());
            $strandData = Strands::all();
            $strandDataLbl = Strands::pluck('name_'.app()->getLocale(),cn::STRANDS_ID_COL)->toArray();
            $learningReportStrand = Strands::pluck(cn::STRANDS_ID_COL)->toArray();
            $reportDataArray = array();
            $reportDataAbilityArray = array();
            $LearningsUnitsLbl = array();
            $LearningsObjectivesLbl = array();
            $PreConfigurationDifficultyLevel = array();
            // $PreConfigurationDiffiltyLevelData = PreConfigurationDiffiltyLevel::get()->toArray();
            $PreConfigurationDiffiltyLevelData = PreConfigurationDiffiltyLevel::where(cn::PRE_CONFIGURE_DIFFICULTY_CURRICULUM_YEAR_ID_COL,$this->GetCurriculumYear())->get()->toArray();
            if(isset($PreConfigurationDiffiltyLevelData)){
                $PreConfigurationDifficultyLevel = array_column($PreConfigurationDiffiltyLevelData,cn::PRE_CONFIGURE_DIFFICULTY_TITLE_COL,cn::PRE_CONFIGURE_DIFFICULTY_DIFFICULTY_LEVEL_COL);
            }
            $reportLearningType = "";
            
            if(isset($request->learningReportStrand) && !empty($request->learningReportStrand)){
                $learningReportStrand = $request->learningReportStrand;
                $strandData = Strands::all();
                $strand = Strands::whereIn(cn::STRANDS_ID_COL,$request->learningReportStrand)->first();
            }else{
                $strandData = Strands::all();
                $strand = Strands::first();
                $strandDataLbl = Strands::pluck('name_'.app()->getLocale(),cn::STRANDS_ID_COL)->toArray();
            }
            if(isset($request->reportLearningType) && !empty($request->reportLearningType)){
                $reportLearningType = $request->reportLearningType;
            }

            $LearningsUnitsLbl = LearningsUnits::pluck('name_'.app()->getLocale(),cn::LEARNING_UNITS_ID_COL)->toArray();
            $LearningUnits = LearningsUnits::where(cn::LEARNING_UNITS_STRANDID_COL, $strand->id)->get();
            $learningUnitsIds = LearningsUnits::where(cn::LEARNING_UNITS_STRANDID_COL, $strand->id)->pluck(cn::LEARNING_UNITS_ID_COL)->toArray();
            // $learningObjectivesList = LearningsObjectives::where(cn::LEARNING_OBJECTIVES_LEARNING_UNITID_COL,$learningUnitsIds[0])->get();
            $learningObjectivesList = LearningsObjectives::IsAvailableQuestion()->where(cn::LEARNING_OBJECTIVES_LEARNING_UNITID_COL,$learningUnitsIds[0])->get();

            $strandId = $strand->id;
            $learningUnitsIds = LearningsUnits::where(cn::LEARNING_UNITS_STRANDID_COL, $strandId)->pluck(cn::LEARNING_UNITS_ID_COL)->toArray();
            if(isset($request->learning_unit_id) && !empty($request->learning_unit_id)){
                $learningUnitsIds = LearningsUnits::where(cn::LEARNING_UNITS_STRANDID_COL, $strand->id)->where(cn::LEARNING_UNITS_ID_COL, $request->learning_unit_id)->pluck(cn::LEARNING_UNITS_ID_COL)->toArray();
                // $learningObjectivesList = LearningsObjectives::where(cn::LEARNING_OBJECTIVES_LEARNING_UNITID_COL,$learningUnitsIds[0])->get();
                $learningObjectivesList = LearningsObjectives::IsAvailableQuestion()->where(cn::LEARNING_OBJECTIVES_LEARNING_UNITID_COL,$learningUnitsIds[0])->get();
            }
            if($isFilter){
                if(!empty($learningUnitsIds)){
                    $learningUnitsId=$learningUnitsIds[0];
                    // $learningObjectivesIds = LearningsObjectives::where(cn::LEARNING_OBJECTIVES_LEARNING_UNITID_COL, $learningUnitsId)->pluck(cn::LEARNING_OBJECTIVES_ID_COL)->toArray();
                    // $LearningsObjectivesLbl = LearningsObjectives::where(cn::LEARNING_OBJECTIVES_LEARNING_UNITID_COL, $learningUnitsId)->pluck('title_'.app()->getLocale(),cn::LEARNING_OBJECTIVES_ID_COL)->toArray();
                    $learningObjectivesIds = LearningsObjectives::IsAvailableQuestion()->where(cn::LEARNING_OBJECTIVES_LEARNING_UNITID_COL, $learningUnitsId)->pluck(cn::LEARNING_OBJECTIVES_ID_COL)->toArray();
                    $LearningsObjectivesLbl = LearningsObjectives::IsAvailableQuestion()->where(cn::LEARNING_OBJECTIVES_LEARNING_UNITID_COL, $learningUnitsId)->pluck('title_'.app()->getLocale(),cn::LEARNING_OBJECTIVES_ID_COL)->toArray();
                    if(!empty($learningObjectivesIds)){
                        $no_of_learning_objectives = count($learningObjectivesIds);
                        $reportDataArray[$strandId][$learningUnitsId]['no_of_learning_objectives'] = count($learningObjectivesIds);
                        $learningObjectivesExamcheck = 0;
                        $noOfPassedLearningObjectives = 0;
                        $countNoOfMasteredLearningObjectives = 0;
                        foreach($learningObjectivesIds as $learningObjectivesId){
                            $abilityAll = 0;
                            $StudentLearningObjectiveAbility = 0;
                            $countLearningObjectivesQuestion = 0;
                            $learningObjectivesData = LearningsObjectives::find($learningObjectivesId);
                            $StrandUnitsObjectivesMappingsId = StrandUnitsObjectivesMappings::where(cn::OBJECTIVES_MAPPINGS_STRAND_ID_COL,$strandId)
                                                                ->where(cn::OBJECTIVES_MAPPINGS_LEARNING_UNIT_ID_COL,$learningUnitsId)
                                                                ->where(cn::OBJECTIVES_MAPPINGS_LEARNING_OBJECTIVES_ID_COL,$learningObjectivesId)
                                                                ->pluck(cn::OBJECTIVES_MAPPINGS_ID_COL)->toArray();
                            if(isset($StrandUnitsObjectivesMappingsId) && !empty($StrandUnitsObjectivesMappingsId)){
                                $QuestionsList = Question::with('answers')->whereIn(cn::QUESTION_OBJECTIVE_MAPPING_ID_COL,$StrandUnitsObjectivesMappingsId)
                                                    ->orderBy(cn::QUESTION_TABLE_ID_COL)->get()->toArray();
                                if(isset($QuestionsList) && !empty($QuestionsList)){
                                    $QuestionsDataList = array_column($QuestionsList,cn::QUESTION_TABLE_ID_COL);
                                    $stud_id = $studentData->{cn::USERS_ID_COL};
                                    $StudentAttemptedExamIds = $this->GetStudentAttemptedExamIds($stud_id) ?? [];
                                    $ExamList = Exam::with(['attempt_exams' => fn($query) => $query->where(cn::ATTEMPT_EXAMS_STUDENT_STUDENT_ID, $stud_id)])
                                                    ->whereHas('attempt_exams', function($q) use($stud_id){
                                                        $q->where(cn::ATTEMPT_EXAMS_STUDENT_STUDENT_ID, $stud_id);
                                                    })
                                                    ->where(function ($query) use ($reportLearningType){
                                                        if(isset($reportLearningType) && $reportLearningType == 1){ // $reportLearningType == 1 = 'Self-Learning Test
                                                            $query->where(cn::EXAM_TYPE_COLS,1)  // 1 = test_type = 'self-Learning'
                                                            ->where(cn::EXAM_TABLE_SELF_LEARNING_TEST_TYPE_COL,2);  // 2 = self_learning_test_type = 'test'
                                                        }
                                                        if(isset($reportLearningType) && $reportLearningType == 3){ // $reportLearningType == 3 = 'Test Only'
                                                            $query->where(cn::EXAM_TYPE_COLS,3);  // 3 = test type = 'Test Only'
                                                        }
                                                        if(empty($reportLearningType)){
                                                            $query->where(cn::EXAM_TYPE_COLS,3)
                                                            ->orWhere(function ($q1) {
                                                                $q1->where(cn::EXAM_TABLE_SELF_LEARNING_TEST_TYPE_COL,2)->where(cn::EXAM_TYPE_COLS,1);
                                                            });
                                                        }
                                                    })
                                                    ->where(cn::EXAM_CURRICULUM_YEAR_ID_COL,$this->GetCurriculumYear())
                                                    ->whereIn(cn::EXAM_TABLE_ID_COLS,$StudentAttemptedExamIds)
                                                    ->orderBy(cn::EXAM_TABLE_ID_COLS,'DESC')
                                                    ->get()->toArray();
                                    if(isset($ExamList) && !empty($ExamList)){
                                        $StudentLearningObjectiveAbility = 0;
                                        $ApiRequestData = array();
                                        $accuracyData = 0;
                                        $abilityData = 0;
                                        foreach($ExamList as $ExamData){
                                            if($countLearningObjectivesQuestion > $this->getGlobalConfiguration('question_window_size_of_learning_objective')){
                                                break;
                                            }
                                            // Check No of maximum question per learning object is higher then then we will consider only latest questions for the number of question set into global configurations
                                            if($countLearningObjectivesQuestion < $this->getGlobalConfiguration('question_window_size_of_learning_objective')){
                                                if(isset($ExamData['attempt_exams'][0][cn::ATTEMPT_EXAMS_QUESTION_ANSWER_COL])){
                                                    if(isset($ExamData['attempt_exams'][0][cn::ATTEMPT_EXAMS_QUESTION_ANSWER_COL])){
                                                        $filterAttemptQuestionAnswer = json_decode($ExamData['attempt_exams'][0][cn::ATTEMPT_EXAMS_QUESTION_ANSWER_COL],true);
                                                    }
                                                }
                                                foreach($filterAttemptQuestionAnswer as $filterAttemptQuestionAnswerKey => $filterAttemptQuestionAnswerValue){
                                                    if(in_array($filterAttemptQuestionAnswerValue['question_id'],$QuestionsDataList)){
                                                        $countLearningObjectivesQuestion += count(array_intersect(explode(',',$ExamData['question_ids']),$QuestionsDataList));
                                                        $QuestionsDataListFinal[] = $filterAttemptQuestionAnswerValue['question_id'];
                                                        $QuestionList = Question::with('answers')->where(cn::QUESTION_TABLE_ID_COL,$filterAttemptQuestionAnswerValue['question_id'])->get()->toArray();
                                                        if(isset($PreConfigurationDifficultyLevel) && !empty($PreConfigurationDifficultyLevel) && isset($PreConfigurationDifficultyLevel[$QuestionList[0][cn::QUESTION_DIFFICULTY_LEVEL_COL]]) && !empty($PreConfigurationDifficultyLevel[$QuestionList[0][cn::QUESTION_DIFFICULTY_LEVEL_COL]])){
                                                            $ApiRequestData['difficulty_list'][] = number_format($PreConfigurationDifficultyLevel[$QuestionList[0][cn::QUESTION_DIFFICULTY_LEVEL_COL]], 4, '.', '');
                                                        }else{
                                                            $ApiRequestData['difficulty_list'][] = 0;
                                                        }
                                                        $AnswerCount = 0;
                                                        for($ans = 1; $ans <= 4; $ans++){
                                                            if(trim($QuestionList[0]['answers']['answer'.$ans.'_en']) != ""){
                                                                $AnswerCount++;
                                                            }
                                                        }
                                                        $ApiRequestData['num_of_ans_list'][] = $AnswerCount;
                                                        if($filterAttemptQuestionAnswerValue['answer'] == $QuestionList[0]['answers']['correct_answer_'.$ExamData['attempt_exams'][0]['language']]){
                                                            $ApiRequestData['questions_results'][] = true;
                                                        }else{
                                                            $ApiRequestData['questions_results'][] = false;
                                                        }
                                                    }
                                                }
                                            }
                                        }

                                        $Ability = 0;
                                        if(isset($ApiRequestData) && !empty($ApiRequestData)){
                                            $requestPayload = new \Illuminate\Http\Request();
                                            $requestPayload = $requestPayload->replace([
                                                'questions_results'=> array(
                                                    $ApiRequestData['questions_results']
                                                ),
                                                'num_of_ans_list' => $ApiRequestData['num_of_ans_list'],
                                                'difficulty_list' => array_map('floatval', $ApiRequestData['difficulty_list']),
                                                'max_student_num' => 1
                                            ]);
                                            $data = $this->AIApiService->getStudentProgressReport($requestPayload);
                                            if(isset($data) && !empty($data) && isset($data[0]) && !empty($data[0])){
                                                $Ability = $data[0];
                                            }
                                        }

                                        // Check No of minimum question per learning object is less then then we will display "N/A" Learning progress report
                                        if($countLearningObjectivesQuestion > $this->getGlobalConfiguration('min_no_question_per_study_progress')){
                                            $StudentLearningObjectiveAbility = $Ability ?? 0;
                                            // Store array into student ability
                                            $reportDataAbilityArray[$strandId][$learningUnitsId][] = array(
                                                'learning_objective_number' => $learningObjectivesData->foci_number,
                                                'LearningsObjectives' => $learningObjectivesData->foci_number.' '.$this->setLearningObjectivesTitle($LearningsObjectivesLbl[$learningObjectivesId]),
                                                'ability' => $StudentLearningObjectiveAbility,
                                                'normalizedAbility' => Helper::getNormalizedAbility($StudentLearningObjectiveAbility),
                                                'ShortNormalizedAbility' => Helper::getShortNormalizedAbility($StudentLearningObjectiveAbility),
                                                'studystatus' => Helper::getAbilityType($StudentLearningObjectiveAbility),
                                                'studyStatusColor' => Helper::getGlobalConfiguration(Helper::getAbilityType($StudentLearningObjectiveAbility))
                                            );
                                        }else{
                                            // Store array into student ability
                                            $reportDataAbilityArray[$strandId][$learningUnitsId][] = array(
                                                'learning_objective_number' => $learningObjectivesData->foci_number,
                                                'LearningsObjectives' => $learningObjectivesData->foci_number.' '.$this->setLearningObjectivesTitle($LearningsObjectivesLbl[$learningObjectivesId]),
                                                'ability' => $StudentLearningObjectiveAbility,
                                                'normalizedAbility' => $StudentLearningObjectiveAbility,
                                                'ShortNormalizedAbility' => $StudentLearningObjectiveAbility,
                                                'studystatus' => Helper::getAbilityType($StudentLearningObjectiveAbility),
                                                'studyStatusColor' => Helper::getGlobalConfiguration('incomplete_color')
                                            );
                                        }
                                    }else{
                                        // Store array into student ability
                                        $reportDataAbilityArray[$strandId][$learningUnitsId][] = array(
                                            'learning_objective_number' => $learningObjectivesData->foci_number,
                                            'LearningsObjectives' => $learningObjectivesData->foci_number.' '.$this->setLearningObjectivesTitle($LearningsObjectivesLbl[$learningObjectivesId]),
                                            'ability' => $StudentLearningObjectiveAbility,
                                            'normalizedAbility' => $StudentLearningObjectiveAbility,
                                            'ShortNormalizedAbility' => $StudentLearningObjectiveAbility,
                                            'studystatus' => Helper::getAbilityType($StudentLearningObjectiveAbility),
                                            'studyStatusColor' => Helper::getGlobalConfiguration('incomplete_color')
                                        );
                                    }
                                }else{
                                    $reportDataAbilityArray[$strandId][$learningUnitsId][] = array(
                                        'learning_objective_number' => $learningObjectivesData->foci_number,
                                        'LearningsObjectives' => $learningObjectivesData->foci_number.' '.$this->setLearningObjectivesTitle($LearningsObjectivesLbl[$learningObjectivesId]),
                                        'ability' => $StudentLearningObjectiveAbility,
                                        'normalizedAbility' => $StudentLearningObjectiveAbility,
                                        'ShortNormalizedAbility' => $StudentLearningObjectiveAbility,
                                        'studystatus' => Helper::getAbilityType($StudentLearningObjectiveAbility),
                                        'studyStatusColor' => Helper::getGlobalConfiguration('incomplete_color')
                                    );
                                }
                            }
                            // Count No of mastered learning objectives
                            if($this->CheckLearningObjectivesMastered($StudentLearningObjectiveAbility)){
                                $countNoOfMasteredLearningObjectives++;
                            }
                        }
                        //  Set Master objectives details for the students
                        $reportDataArray[$strandId][$learningUnitsId]['master_objectives'] = array(
                            'no_of_learning_objectives' => $no_of_learning_objectives,
                            'count_accomplished_learning_objectives' => $countNoOfMasteredLearningObjectives,
                            'count_not_accomplished_learning_objectives' => ($no_of_learning_objectives - $countNoOfMasteredLearningObjectives),
                            'accomplished_percentage' => round((($countNoOfMasteredLearningObjectives / $no_of_learning_objectives) * 100),1),
                            'not_accomplished_percentage' => (100 - round((($countNoOfMasteredLearningObjectives / $no_of_learning_objectives) * 100),1))
                        );
                    }
                }
            }
            return view('backend.reports.progress_report.student.learning_objective_report',compact('strandData','strandDataLbl','reportDataArray','LearningsUnitsLbl','LearningsObjectivesLbl','reportDataAbilityArray','learningObjectivesList','LearningUnits','showMenu','ColorCodes','studentId'));
        } catch (\Exception $exception) {
            return back()->withError($exception->getMessage())->withInput();
        }
    }

    /**
     * USE : Progress report learning unit display into student panel
     */
    public function StudentProgressReportLearningUnits(Request $request,$studentId=0){
        try{
            if($studentId==0){
                $studentData = User::find(Auth::user()->id);
            }else{
                $studentData = User::find($studentId);
            }
            
            ini_set('max_execution_time', -1);
            if(isset($request->isFilter)){
                $isFilter = true;
            }else{
                $isFilter = false;
            }

            // Get Color Codes Array
            $ColorCodes = Helper::GetColorCodes();

            $progressReportArray = array();
            $LearningsUnitsLbl = array();
            $grade_id = array();
            $GradeClassListData = array();
            $class_type_id = array();
            $GradesList = array();
            $teachersClassList = array();
            $currentLang = ucwords(app()->getLocale());
            $schoolId = $studentData->{cn::USERS_SCHOOL_ID_COL};
            $roleId = $studentData->{cn::USERS_ROLE_ID_COL};
            $reportLearningType = "";

            if(isset($request->reportLearningType) && !empty($request->reportLearningType)){
                $reportLearningType = $request->reportLearningType;
            }

            if(isset($request->grade_id) && !empty($request->grade_id)){
                $grade_id = $request->grade_id;
            }

            // Get pre-configured data for the questions
            $PreConfigurationDifficultyLevel = array();
            $PreConfigurationDiffiltyLevelData = PreConfigurationDiffiltyLevel::get()->toArray();
            if(isset($PreConfigurationDiffiltyLevelData)){
                $PreConfigurationDifficultyLevel = array_column($PreConfigurationDiffiltyLevelData,cn::PRE_CONFIGURE_DIFFICULTY_TITLE_COL,cn::PRE_CONFIGURE_DIFFICULTY_DIFFICULTY_LEVEL_COL);
            }
            
            $gradeArray = array();
            $classArray = array();
            //$gradeData = Grades::find($studentData->grade_id);
            $gradeData = Grades::find($studentData->CurriculumYearGradeId);
            
            $GradesList[] = array('id' => $gradeData->id,'name' => $gradeData->name);

            // Get Strands data
            $StrandList = Strands::all();
            $LearningUnitsList = LearningsUnits::all();
            $StrandsLearningUnitsList = Strands::with('LearningUnit')->get()->toArray();
            
            //$strandDataLbl = Strands::pluck('name_'.app()->getLocale(),cn::STRANDS_ID_COL)->toArray();
            if(!empty($StrandList)){
                $studentList =  User::find($studentData->id);
                                
                $progressReportArray[$studentData->id]['student_data'][] = $studentData->toArray();
                $no_of_learning_unit = count($LearningUnitsList);
                $progressReportArray[$studentData->id]['no_of_learning_unit'] = $no_of_learning_unit;
                foreach($StrandList as $strand){
                    $strandId = $strand->id;
                    $LearningUnits = LearningsUnits::where(cn::LEARNING_UNITS_STRANDID_COL, $strand->id)->get();
                    $learningUnitsIds = LearningsUnits::where(cn::LEARNING_UNITS_STRANDID_COL, $strand->id)->pluck(cn::LEARNING_UNITS_ID_COL)->toArray();
                    //$LearningsUnitsLbl = LearningsUnits::pluck('name_'.app()->getLocale(),cn::LEARNING_UNITS_ID_COL)->toArray();
                    if(!empty($learningUnitsIds)){
                        if(!empty($learningUnitsIds)){
                            $learningUnitsId = $learningUnitsIds[0];                                                                       
                            $countNoOfMasteredLearningUnits = 0;
                            foreach($learningUnitsIds as $learningUnitsId){
                                // $learningObjectivesIds = LearningsObjectives::where(cn::LEARNING_OBJECTIVES_LEARNING_UNITID_COL, $learningUnitsId)->pluck(cn::LEARNING_OBJECTIVES_ID_COL)->toArray();
                                // $LearningsObjectivesLbl = LearningsObjectives::where(cn::LEARNING_OBJECTIVES_LEARNING_UNITID_COL, $learningUnitsId)->pluck('title_'.app()->getLocale(),cn::LEARNING_OBJECTIVES_ID_COL)->toArray();
                                $learningObjectivesIds = LearningsObjectives::IsAvailableQuestion()->where(cn::LEARNING_OBJECTIVES_LEARNING_UNITID_COL, $learningUnitsId)->pluck(cn::LEARNING_OBJECTIVES_ID_COL)->toArray();
                                $LearningsObjectivesLbl = LearningsObjectives::IsAvailableQuestion()->where(cn::LEARNING_OBJECTIVES_LEARNING_UNITID_COL, $learningUnitsId)->pluck('title_'.app()->getLocale(),cn::LEARNING_OBJECTIVES_ID_COL)->toArray();
                                if(!empty($learningObjectivesIds)){
                                    $no_of_learning_objectives = count($learningObjectivesIds);
                                    $countNoOfMasteredLearningObjectives = 0;
                                    foreach($learningObjectivesIds as $learningObjectivesId){
                                        $StudentLearningObjectiveAbility = 0;
                                        $countLearningObjectivesQuestion = 0;
                                        $learningObjectivesData = LearningsObjectives::find($learningObjectivesId);
                                        $StrandUnitsObjectivesMappingsId =  StrandUnitsObjectivesMappings::where(cn::OBJECTIVES_MAPPINGS_STRAND_ID_COL,$strandId)
                                                                            ->where(cn::OBJECTIVES_MAPPINGS_LEARNING_UNIT_ID_COL,$learningUnitsId)
                                                                            ->where(cn::OBJECTIVES_MAPPINGS_LEARNING_OBJECTIVES_ID_COL,$learningObjectivesId)
                                                                            ->pluck(cn::OBJECTIVES_MAPPINGS_ID_COL)->toArray();
                                        if(isset($StrandUnitsObjectivesMappingsId) && !empty($StrandUnitsObjectivesMappingsId)){
                                            $QuestionsList = Question::with('answers')
                                                                ->whereIn(cn::QUESTION_OBJECTIVE_MAPPING_ID_COL,$StrandUnitsObjectivesMappingsId)
                                                                ->orderBy(cn::QUESTION_TABLE_ID_COL)
                                                                ->get()
                                                                ->toArray();
                                            if(isset($QuestionsList) && !empty($QuestionsList)){
                                                $QuestionsDataList = array_column($QuestionsList,cn::QUESTION_TABLE_ID_COL);
                                                $stud_id = $studentData->id;
                                                $StudentAttemptedExamIds = $this->GetStudentAttemptedExamIds($stud_id) ?? [];
                                                
                                                $ExamList = Exam::with(['attempt_exams' => fn($query) => $query->where(cn::ATTEMPT_EXAMS_STUDENT_STUDENT_ID, $stud_id)])
                                                            ->whereHas('attempt_exams', function($q) use($stud_id){
                                                                $q->where(cn::ATTEMPT_EXAMS_STUDENT_STUDENT_ID, $stud_id);
                                                            })
                                                            ->where(function ($query) use ($reportLearningType){
                                                                if(isset($reportLearningType) && $reportLearningType == 1){  // $reportLearningType == 1 = 'Self-Learning Test
                                                                    $query->where(cn::EXAM_TYPE_COLS,1)  // 1 = test_type = 'self-Learning'
                                                                    ->where(cn::EXAM_TABLE_SELF_LEARNING_TEST_TYPE_COL,2);  // 2 = self_learning_test_type = 'test'
                                                                }
                                                                if(isset($reportLearningType) && $reportLearningType == 3){  // $reportLearningType == 3 = 'Test Only'
                                                                    $query->where(cn::EXAM_TYPE_COLS,3);  // 3 = test type = 'Test Only'
                                                                }
                                                                if(empty($reportLearningType)){
                                                                    $query->where(cn::EXAM_TYPE_COLS,3)
                                                                    ->orWhere(function ($q1) {
                                                                        $q1->where(cn::EXAM_TABLE_SELF_LEARNING_TEST_TYPE_COL,2)->where(cn::EXAM_TYPE_COLS,1);
                                                                    });
                                                                }
                                                            })
                                                            ->where(cn::EXAM_CURRICULUM_YEAR_ID_COL,$this->GetCurriculumYear())
                                                            ->whereIn(cn::EXAM_TABLE_ID_COLS,$StudentAttemptedExamIds)
                                                            ->orderBy(cn::EXAM_TABLE_ID_COLS,'DESC')
                                                            ->get()
                                                            ->toArray();                                                    
                                                if(isset($ExamList) && !empty($ExamList)){
                                                    $StudentLearningObjectiveAbility = 0;
                                                    $ApiRequestData = array();
                                                    foreach($ExamList as $ExamData){
                                                        if($countLearningObjectivesQuestion > $this->getGlobalConfiguration('question_window_size_of_learning_objective')){
                                                            break;
                                                        }
                                                        // Check No of maximum question per learning object is higher then then we will consider only latest questions for the number of question set into global configurations
                                                        if($countLearningObjectivesQuestion < $this->getGlobalConfiguration('question_window_size_of_learning_objective')){
                                                            if(isset($ExamData['attempt_exams'][0][cn::ATTEMPT_EXAMS_QUESTION_ANSWER_COL])){
                                                                if(isset($ExamData['attempt_exams'][0][cn::ATTEMPT_EXAMS_QUESTION_ANSWER_COL])){
                                                                    $filterAttemptQuestionAnswer = json_decode($ExamData['attempt_exams'][0][cn::ATTEMPT_EXAMS_QUESTION_ANSWER_COL],true);
                                                                }
                                                            }
                                                            foreach($filterAttemptQuestionAnswer as $filterAttemptQuestionAnswerKey => $filterAttemptQuestionAnswerValue){
                                                                if(in_array($filterAttemptQuestionAnswerValue['question_id'],$QuestionsDataList)){
                                                                    $countLearningObjectivesQuestion += count(array_intersect(explode(',',$ExamData['question_ids']),$QuestionsDataList));
                                                                    $QuestionsDataListFinal[] = $filterAttemptQuestionAnswerValue['question_id'];
                                                                    $QuestionList = Question::with('answers')
                                                                                    ->where(cn::QUESTION_TABLE_ID_COL,$filterAttemptQuestionAnswerValue['question_id'])
                                                                                    ->get()->toArray();
                                                                    if(isset($PreConfigurationDifficultyLevel) && !empty($PreConfigurationDifficultyLevel) && isset($PreConfigurationDifficultyLevel[$QuestionList[0][cn::QUESTION_DIFFICULTY_LEVEL_COL]]) && !empty($PreConfigurationDifficultyLevel[$QuestionList[0][cn::QUESTION_DIFFICULTY_LEVEL_COL]])){
                                                                        $ApiRequestData['difficulty_list'][] = number_format($PreConfigurationDifficultyLevel[$QuestionList[0][cn::QUESTION_DIFFICULTY_LEVEL_COL]], 4, '.', '');
                                                                    }else{
                                                                        $ApiRequestData['difficulty_list'][] = 0;
                                                                    }
                                                                    $AnswerCount = 0;
                                                                    for($ans = 1; $ans <= 4; $ans++){
                                                                        if(trim($QuestionList[0]['answers']['answer'.$ans.'_en']) != ""){
                                                                            $AnswerCount++;
                                                                        }
                                                                    }
                                                                    $ApiRequestData['num_of_ans_list'][] = $AnswerCount;
                                                                    if($filterAttemptQuestionAnswerValue['answer'] == $QuestionList[0]['answers']['correct_answer_'.$ExamData['attempt_exams'][0]['language']]){
                                                                        $ApiRequestData['questions_results'][] = true;
                                                                    }else{
                                                                        $ApiRequestData['questions_results'][] = false;
                                                                    }
                                                                }
                                                            }
                                                        }
                                                    }

                                                    $Ability = 0;
                                                    $StudentLearningObjectiveAbility = 0;
                                                    if(isset($ApiRequestData) && !empty($ApiRequestData)){
                                                        $requestPayload = new \Illuminate\Http\Request();
                                                        $requestPayload = $requestPayload->replace([
                                                            'questions_results'=> array(
                                                                $ApiRequestData['questions_results']
                                                            ),
                                                            'num_of_ans_list' => $ApiRequestData['num_of_ans_list'],
                                                            'difficulty_list' => array_map('floatval', $ApiRequestData['difficulty_list']),
                                                            'max_student_num' => 1
                                                        ]);
                                                        $data = $this->AIApiService->getStudentProgressReport($requestPayload);
                                                        if(isset($data) && !empty($data) && isset($data[0]) && !empty($data[0])){
                                                            $Ability = $data[0];
                                                        }
                                                    }

                                                    // Check No of minimum question per learning object is less then then we will display "N/A" Learning progress report
                                                    if($countLearningObjectivesQuestion > $this->getGlobalConfiguration('min_no_question_per_study_progress')){
                                                        $StudentLearningObjectiveAbility = $Ability ?? 0;
                                                    }
                                                }
                                            }
                                        }

                                        // Count No of mastered learning objectives
                                        if($this->CheckLearningObjectivesMastered($StudentLearningObjectiveAbility)){
                                            $countNoOfMasteredLearningObjectives++;
                                        }
                                    }

                                    // Stored Result in array
                                    $progressReportArray[$studentData->id]['report_data'][] = array(
                                        'no_of_learning_objectives' => $no_of_learning_objectives,
                                        'count_accomplished_learning_objectives' => $countNoOfMasteredLearningObjectives,
                                        'count_not_accomplished_learning_objectives' => ($no_of_learning_objectives - $countNoOfMasteredLearningObjectives),
                                        'accomplished_percentage' => round((($countNoOfMasteredLearningObjectives / $no_of_learning_objectives) * 100),1),
                                        'not_accomplished_percentage' => (100 - round((($countNoOfMasteredLearningObjectives / $no_of_learning_objectives) * 100),1))
                                    );
                                }
                            }
                        }
                    }
                }
            }
            return view('backend.reports.progress_report.student.learning_unit_report',compact('StrandList','LearningUnitsList','StrandsLearningUnitsList','GradesList','grade_id','teachersClassList','reportLearningType','progressReportArray','LearningUnits','ColorCodes','studentId'));
        }catch(\Exception $exception){
            return back()->withError($exception->getMessage())->withInput();
        }
    }
}
