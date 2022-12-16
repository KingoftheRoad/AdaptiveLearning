<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Constants\DbConstant as cn;
use App\Traits\Common;
use App\Traits\ResponseFormat;
use App\Exceptions\CustomException;
use App\Models\StrandUnitsObjectivesMappings;
use App\Models\Subjects;
use App\Models\Strands;
use App\Models\User;
use App\Models\LearningsUnits;
use App\Models\LearningsObjectives;
use App\Models\Question;
use App\Models\GradeSchoolMappings;
use App\Models\PreConfigurationDiffiltyLevel;
use App\Models\Grades;
use App\Models\GradeClassMapping;
use App\Models\AttemptExams;
use App\Models\Exam;
use App\Models\ClassPromotionHistory;
use App\Models\AuditLogs;
use App\Models\ClassSubjectMapping;
use App\Models\ExamCreditPointRulesMapping;
use App\Models\ExamGradeClassMappingModel;
use App\Models\ExamSchoolMapping;
use App\Models\IntelligentTutorVideos;
use App\Models\UploadDocuments;
use App\Models\UserCreditPoints;
use App\Models\UserCreditPointHistory;
use App\Models\MyTeachingReport;
use App\Models\PeerGroup;
use App\Models\ExamConfigurationsDetails;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Hash;
use App\Http\Services\AIApiService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\SubjectSchoolMappings;
use App\Models\TeachersClassSubjectAssign;
use App\Models\PeerGroupMember;
use App\Models\CurriculumYearStudentMappings;
use App\Helpers\Helper;
use App\Models\AttemptExamStudentMapping;

class CommonController extends Controller
{
    // Load Common Traits
    use Common, ResponseFormat;

    protected $AIApiService;

    public function __construct()
    {
        $this->AIApiService = new AIApiService();
    }

    //Update Class Id 
    public function updateClassIdAsClassName(Request $request){
        ini_set('max_execution_time', -1);
        $UsersData = User::All();
        if(!empty($UsersData)){
            foreach($UsersData as $user){
                $classId = $user->{cn::USERS_CLASS_ID_COL};
                User::where(cn::USERS_ID_COL,$user->{cn::USERS_ID_COL})->update([cn::USERS_CLASS_ID_COL => $classId]);
            }
        }
    }

    // Check Email Exists
    public function CheckEmailExists(Request $request){
        if(isset($request->uid) && $request->uid!=''){
            if(User::where(cn::USERS_EMAIL_COL,$request->email)->where(cn::USERS_ID_COL,'!=',$request->uid)->exists()){
                return $this->sendResponse(true);
            }
        }else if(User::where(cn::USERS_EMAIL_COL,$request->email)->exists()){
            return $this->sendResponse(true);
        }else{
            return $this->sendResponse(false);
        }
    }

    public function getSubjectFromGrades(Request $request){
        try{
            if($this->isSchoolLogin()){
                $subjectIds = SubjectSchoolMappings::where(cn::SUBJECT_MAPPING_SCHOOL_ID_COL,$this->isSchoolLogin())->pluck(cn::SUBJECT_MAPPING_SUBJECT_ID_COL);
            }else if($this->isTeacherLogin()){
                $subjectIds = SubjectSchoolMappings::where(cn::SUBJECT_MAPPING_SCHOOL_ID_COL,$this->isTeacherLogin())->pluck(cn::SUBJECT_MAPPING_SUBJECT_ID_COL);
            }else{
                $subjectIds =   StrandUnitsObjectivesMappings::where(cn::OBJECTIVES_MAPPINGS_GRADE_ID_COL,$request->grade_id)
                                ->pluck(cn::OBJECTIVES_MAPPINGS_SUBJECT_ID_COL);
            }
            if(!empty($subjectIds)){
                $subjectIds = array_unique($subjectIds->toArray());
                $subjects = Subjects::whereIn(cn::SUBJECTS_ID_COL, $subjectIds)->get();
                return $this->sendResponse($subjects);
            }
        }catch(\Exception $ex){
            return $this->sendError($ex->getMessage(), 404);
        }
    }

    public function getStrandsFromSubject(Request $request){
        try{
            $SubjectsData = Subjects::where(cn::SUBJECTS_CODE_COL,cn::CODEMATHEMATICS)->first();
            $strandsIds = StrandUnitsObjectivesMappings::where(cn::OBJECTIVES_MAPPINGS_GRADE_ID_COL,$request->grade_id)
                            ->where(cn::OBJECTIVES_MAPPINGS_SUBJECT_ID_COL,$SubjectsData->{cn::SUBJECTS_ID_COL})
                            ->pluck(cn::OBJECTIVES_MAPPINGS_STRAND_ID_COL);
            if(!empty($strandsIds)){
                $strandsIds = array_unique($strandsIds->toArray());
                $strands = Strands::whereIn(cn::STRANDS_ID_COL, $strandsIds)->get();
                return $this->sendResponse($strands);
            }
        }catch(\Exception $ex){
            return $this->sendError($ex->getMessage(), 404);
        }
    }    
    
    public function getLearningUnitFromStrands(Request $request){
        try{
            $SubjectsData = Subjects::where(cn::SUBJECTS_CODE_COL,cn::CODEMATHEMATICS)->first();
            $learningUnitsIds = StrandUnitsObjectivesMappings::where(cn::OBJECTIVES_MAPPINGS_GRADE_ID_COL,$request->grade_id)
                            ->where(cn::OBJECTIVES_MAPPINGS_SUBJECT_ID_COL,$SubjectsData->{cn::SUBJECTS_ID_COL})
                            //->where(cn::OBJECTIVES_MAPPINGS_SUBJECT_ID_COL,$request->subject_id)
                            ->where(cn::OBJECTIVES_MAPPINGS_STRAND_ID_COL,$request->strand_id)
                            ->pluck(cn::OBJECTIVES_MAPPINGS_LEARNING_UNIT_ID_COL);
            if(!empty($learningUnitsIds)){
                $learningUnitsIds = array_unique($learningUnitsIds->toArray());
                $LearningUnits = LearningsUnits::whereIn(cn::LEARNING_UNITS_ID_COL, $learningUnitsIds)->get();
                return $this->sendResponse($LearningUnits);
            }
        }catch(\Exception $ex){
            return $this->sendError($ex->getMessage(), 404);
        }
    }

    public function getMultiLearningUnitFromStrands(Request $request){
        try{
            if(!empty($request->strand_id)){
                $learningUnit = LearningsUnits::whereIn(cn::LEARNING_UNITS_STRANDID_COL,$request->strand_id)->get();
                if(!empty($learningUnit)){
                    return $this->sendResponse($learningUnit);
                }
            }
        }catch(\Exception $ex){
            return $this->sendError($ex->getMessage(), 404);
        }
    }

    /**
     * USE : Get Learning Units list based on selected multiple strands ids
     */
    public function getLearningUnitFromMultipleStrands(Request $request){
        try{
            $learningUnitsIds = StrandUnitsObjectivesMappings::whereIn(cn::OBJECTIVES_MAPPINGS_STRAND_ID_COL,$request->strands_ids)
                ->where(function ($query) use ($request){
                    if(isset($request->grade_id) && !empty($request->grade_id)){
                        $query->where(cn::OBJECTIVES_MAPPINGS_GRADE_ID_COL,$request->grade_id);
                    }
                    if(isset($request->subject_id) && !empty($request->subject_id)){
                        $query->where(cn::OBJECTIVES_MAPPINGS_SUBJECT_ID_COL,$request->subject_id);
                    }
                })
                ->pluck(cn::OBJECTIVES_MAPPINGS_LEARNING_UNIT_ID_COL);
            if(!empty($learningUnitsIds)){
                $learningUnitsIds = array_unique($learningUnitsIds->toArray());
                $LearningUnits = LearningsUnits::whereIn(cn::LEARNING_UNITS_ID_COL, $learningUnitsIds)->get();
                return $this->sendResponse($LearningUnits);
            }
        }catch(\Exception $ex){
            return $this->sendError($ex->getMessage(), 404);
        }
    }

    public function getLearningObjectivesFromLearningUnits(Request $request){
        try{
            $SubjectsData = Subjects::where(cn::SUBJECTS_CODE_COL,cn::CODEMATHEMATICS)->first();
            $learningObjectivesIds = StrandUnitsObjectivesMappings::where(cn::OBJECTIVES_MAPPINGS_GRADE_ID_COL,$request->grade_id)
                            //->where(cn::OBJECTIVES_MAPPINGS_SUBJECT_ID_COL,$request->subject_id)
                            ->where(cn::OBJECTIVES_MAPPINGS_SUBJECT_ID_COL,$SubjectsData->{cn::SUBJECTS_ID_COL})
                            ->where(cn::OBJECTIVES_MAPPINGS_STRAND_ID_COL,$request->strand_id)
                            ->where(cn::OBJECTIVES_MAPPINGS_LEARNING_UNIT_ID_COL,$request->learning_unit_id)
                            ->pluck(cn::OBJECTIVES_MAPPINGS_LEARNING_OBJECTIVES_ID_COL);
            if(!empty($learningObjectivesIds)){
                $learningObjectivesIds = array_unique($learningObjectivesIds->toArray());
                // $LearningObjectives = LearningsObjectives::whereIn(cn::LEARNING_OBJECTIVES_ID_COL, $learningObjectivesIds)->get();
                $LearningObjectives = LearningsObjectives::IsAvailableQuestion()->whereIn(cn::LEARNING_OBJECTIVES_ID_COL, $learningObjectivesIds)->get();
                return $this->sendResponse($LearningObjectives);
            }
        }catch(\Exception $ex){
            return $this->sendError($ex->getMessage(), 404);
        }
    }

    public function getMultiLearningObjectivesFromLearningUnits(Request $request){
        $learningObjectives = LearningsObjectives::whereIn(cn::LEARNING_OBJECTIVES_LEARNING_UNITID_COL,$request->learning_unit_id)->get();
        if(!empty($learningObjectives)){
            return $this->sendResponse($learningObjectives);
        }
    }

    /**
     * USE : Get Learning objectives based on multiple selected Learning units
     */
    public function getLearningObjectivesFromMultipleLearningUnits(Request $request){
        try{
            $learningObjectivesIds = StrandUnitsObjectivesMappings::whereIn(cn::OBJECTIVES_MAPPINGS_STRAND_ID_COL,$request->strand_id)
                ->whereIn(cn::OBJECTIVES_MAPPINGS_LEARNING_UNIT_ID_COL,$request->learning_unit_id)
                ->where(function ($query) use ($request){
                    if(isset($request->grade_id) && !empty($request->grade_id)){
                        $query->where(cn::OBJECTIVES_MAPPINGS_GRADE_ID_COL,$request->grade_id);
                    }
                    if(isset($request->subject_id) && !empty($request->subject_id)){
                        $query->where(cn::OBJECTIVES_MAPPINGS_SUBJECT_ID_COL,$request->subject_id);
                    }
                })
                ->pluck(cn::OBJECTIVES_MAPPINGS_LEARNING_OBJECTIVES_ID_COL);
            if(!empty($learningObjectivesIds)){
                $learningObjectivesIds = array_unique($learningObjectivesIds->toArray());
                // $LearningObjectives = LearningsObjectives::whereIn(cn::LEARNING_OBJECTIVES_ID_COL, $learningObjectivesIds)->get();
                $LearningObjectives = LearningsObjectives::IsAvailableQuestion()->whereIn(cn::LEARNING_OBJECTIVES_ID_COL, $learningObjectivesIds)->get();
                return $this->sendResponse($LearningObjectives);
            }
        }catch(\Exception $ex){
            return $this->sendError($ex->getMessage(), 404);
        }
    }

    /**
     * USE : Send test email
     */
    public function testEmail(){
        $emaildata = array(
            'email' => 'manoj.prajapati@anblicks.com',
            'name' => 'Manoj Prajapati'
        );
        $this->sendEmail('welcome-email',$emaildata);
        dd('send mail successfully !!');
    }

    /**
     * USE : Get subject code by subject id
     */
    public function getSubjectCodeById($id = null){
        try {
            $Subject = Subjects::find($id);
            if(!empty($Subject)){
                return $this->sendResponse(['subject_code' => $Subject->{cn::SUBJECTS_CODE_COL} ?? 'NA']);
            }
        } catch (\Exception $ex) {
            return $this->sendError($ex->getMessage(), 404);
        }
    }

    /**
     * USE : Get count question by question code
     */
    public function countQuestionByMapping(Request $request){
        try {
            $count = 0;
            $learningObjectivesIds = StrandUnitsObjectivesMappings::where(cn::OBJECTIVES_MAPPINGS_GRADE_ID_COL,$request->grade_id)
                            ->where(cn::OBJECTIVES_MAPPINGS_SUBJECT_ID_COL,$request->subject_id)
                            ->where(cn::OBJECTIVES_MAPPINGS_STRAND_ID_COL,$request->strand_id)
                            ->where(cn::OBJECTIVES_MAPPINGS_LEARNING_UNIT_ID_COL,$request->learning_unit_id)
                            ->where(cn::OBJECTIVES_MAPPINGS_LEARNING_OBJECTIVES_ID_COL,$request->learning_objectives_id)
                            ->pluck(cn::OBJECTIVES_MAPPINGS_ID_COL);
            if(!empty($learningObjectivesIds)){
                $count = Question::whereIn(cn::QUESTION_OBJECTIVE_MAPPING_ID_COL, $learningObjectivesIds->toArray())->where(cn::QUESTION_G_COL,strtolower($request->field_g))->count();
            }
            return $this->sendResponse(['count' => ($count +1)]);
        } catch (\Exception $ex) {
            return $this->sendError($ex->getMessage(), 404);
        }
    }

    /**
     * USE : Set Sidebar sesstion class
     */
    public function setSidebarSessionClass(Request $request){
        try {
            if($request->sidebar_option == 'sidebar-open'){
                Session::put('sidebar_option','sidebar-open');
                Session::put('sidebar', 'inactive');
            }
            if($request->sidebar_option == 'sidebar-close'){
                Session::put('sidebar_option','sidebar-close');
                Session::put('sidebar', 'active');
            }
            return $this->sendResponse([]);
        } catch (\Exception $ex) {
            return $this->sendError($ex->getMessage(), 404);
        }
    }

    public function passwordCreate(Request $request){
        echo Hash::make('527326');
    }

    public function getDifficultyValue($difficultyLevel){
        if(isset($difficultyLevel) && !empty($difficultyLevel)){
            switch($difficultyLevel){
                case 1:
                    $difficultyValue = 'Low';
                    break;
                case 2:
                    $difficultyValue = 'Medium';
                    break;
                case 3:
                    $difficultyValue = 'Difficult';
                    break;
                case 4:
                    $difficultyValue = 'Tough';
                    break;
                default:
                    $difficultyValue = null;
                    break;
            }
            return $this->sendResponse($difficultyValue);
        }
    }

    public function getGradesBySchool(Request $request){
        $gradeName = '';
        $GradeData = GradeSchoolMappings::with('grades')->where(cn::GRADES_MAPPING_SCHOOL_ID_COL,$request->schoolid)->get();
        if(!empty($GradeData)){
            $gradeName .= '<option value="">Select Grade</option>';
            foreach($GradeData as $grade){
                $gradeName .= '<option value="'.$grade->id.'" >'.$grade->grades->name.'</option>';
            }
        }
        return $this->sendResponse($gradeName);
    }
    /*
        This function to used to get Difficulty Value in pre_configured_difficulty table 
    */
    public function getAiDifficultyValue($difficultyLevel){
        if(isset($difficultyLevel) && !empty($difficultyLevel)){
            $PreConfigurationDifficultyLevelData = PreConfigurationDiffiltyLevel::where(cn::AI_CALCULATED_DIFFICULTY_DIFFICULTY_LEVEL_COL,$difficultyLevel)->get()->toArray();
            if(isset($PreConfigurationDifficultyLevelData) && !empty($PreConfigurationDifficultyLevelData)){
                return $this->sendResponse($PreConfigurationDifficultyLevelData[0]);
            }
        }
        return $this->sendResponse([]);
    }

    /**
     * USE : Get Classlist by grade id
     */
    public function getClassTypeByAdmin(Request $request){
        $GradeClassData ='';
        $html ='';
        if(!empty($request->grade_id)){
            if($this->isSchoolLogin()){
                $GradeClassMapping = GradeClassMapping::where([cn::GRADE_CLASS_MAPPING_GRADE_ID_COL => $request->grade_id,cn::GRADE_CLASS_MAPPING_SCHOOL_ID_COL => $this->isSchoolLogin()])->get();
            }
            if($this->isTeacherLogin()){
                $GradeClassMapping = GradeClassMapping::where([cn::GRADE_CLASS_MAPPING_GRADE_ID_COL => $request->grade_id, cn::GRADE_CLASS_MAPPING_SCHOOL_ID_COL => $this->isTeacherLogin()])->get();
            }
            if($this->isAdmin()){
                $GradeClassMapping = DB::select("SELECT *,GROUP_CONCAT(DISTINCT id SEPARATOR ',') AS class_id
                            FROM grade_class_mapping
                            WHERE grade_id = $request->grade_id
                            GROUP BY name");
                //$GradeClassMapping = GradeClassMapping::where([cn::GRADE_CLASS_MAPPING_GRADE_ID_COL => $request->grade_id])->groupBy(cn::GRADE_CLASS_MAPPING_NAME_COL)->get();
            }
        }

        if(!empty($GradeClassMapping)){
            if($this->isSchoolLogin() || $this->isTeacherLogin()){
                foreach($GradeClassMapping as $class){
                    $GradeList = Grades::find($class->grade_id);
                    $html .= '<option value='.strtoupper($class->id).'>'.$GradeList->name.strtoupper($class->name).'</option>';
                }
            }else{
                foreach($GradeClassMapping as $class){
                    $GradeList = Grades::find($class->grade_id);
                    //$html .= '<option value='.strtoupper($class->id).'>'.$GradeList->name.strtoupper($class->name).'</option>';
                    $html .= '<option value='.strtoupper($class->class_id).'>'.$GradeList->name.strtoupper($class->name).'</option>';
                }
            }
        }
        return $this->sendResponse($html, '');
    }

    /**
     * USE : Get Test time duration calculate by creating the student own test
     */
    public function getTestTimeDuration(Request $request){
        $TotalTime = 0;
        $QuestionPerSeconds = $this->getGlobalConfiguration('default_second_per_question');
        if(isset($QuestionPerSeconds) && !empty($QuestionPerSeconds) && !empty($request->no_of_questions)){
            $totalSeconds = ($request->no_of_questions * $QuestionPerSeconds);
            $TotalTime = gmdate("H:i:s", $totalSeconds);
        }
        return $this->sendResponse($TotalTime);
    }

    // in Attempt Exam Table in json format add language parameter.
    public function addLanguageTypeinJsonFormat(){
        $attemptExams = AttemptExams::all();
        if(!empty($attemptExams)){
            foreach($attemptExams as $attemptExam){
                if(!empty($attemptExam->question_answers)){
                    $question_answer_detail = json_decode($attemptExam->question_answers,true);
                    foreach($question_answer_detail as $key => $Data){
                        $question_answer_detail[$key]['language'] = $attemptExam->{cn::ATTEMPT_EXAMS_LANGUAGE_COL};
                    }
                }
                $encodeData = json_encode($question_answer_detail);
                AttemptExams::find($attemptExam->id)->update([cn::ATTEMPT_EXAMS_QUESTION_ANSWER_COL=> $question_answer_detail]);
            }
        }
    }

    public function selfLearningExamStudentDelete(){
        $examData = Exam::where(cn::EXAM_TYPE_COLS,1)->get();
        if(!empty($examData)){
            foreach($examData as $exam){
                if(count(explode(',',$exam->student_ids)) > 1){
                   $attemtExamData = AttemptExams::where(cn::ATTEMPT_EXAMS_EXAM_ID,$exam->id)->whereIn(cn::ATTEMPT_EXAMS_STUDENT_STUDENT_ID,explode(',',$exam->student_ids))->delete();
                   Exam::where(cn::EXAM_TABLE_ID_COLS,$exam->id)->delete();
                }
            }
        }
    }

    public function getExamInfo($examId){
        $exam_info = ExamConfigurationsDetails::where(cn::EXAM_CONFIGURATIONS_DETAILS_EXAM_ID_COL,$examId)->get()->toArray();
        if(isset($exam_info) && !empty($exam_info)){
            return $this->sendResponse($exam_info[0]);
        }else{
            return $this->sendResponse([]);
        }
    }

    /**
     * USE : Convert multi dimentional array to single array
     */
    public function array_flatten($array) { 
        if (!is_array($array)) { 
          return FALSE; 
        } 
        $result = array(); 
        foreach ($array as $key => $value) { 
            if (is_array($value)) { 
                $result = array_merge($result, $this->array_flatten($value));
            }else{
                $result[$key] = $value;
            }
        }
        return $result;
    }

    /**
     * USE : Get the test list by filter by grade and class
     */
    public function getTestListByGradeAndClass(Request $request){
        $optionlist = '';
        $Query = User::where(cn::USERS_ROLE_ID_COL,cn::STUDENT_ROLE_ID);
        if($this->isSchoolLogin() || $this->isTeacherLogin()){
            $Query->where(cn::USERS_SCHOOL_ID_COL,Auth::user()->{cn::USERS_SCHOOL_ID_COL});
            if(isset($request->classIds) && !empty($request->classIds)){
                // $Query->whereIn(cn::USERS_CLASS_ID_COL,$request->classIds);
                $Query->whereIn(cn::USERS_ID_COL,$this->curriculum_year_mapping_student_ids('',$request->classIds,Auth::user()->{cn::USERS_SCHOOL_ID_COL}));
            }
        }else{
            // If the current login is admin
            if($this->isAdmin()){
                $classIdArray = [];
                if(isset($request->classIds) && !empty($request->classIds)){
                    foreach($request->classIds as $classIdsArray){
                        $classIdArray[] = explode(',',$classIdsArray);
                    }
                    $classIds = $this->array_flatten($classIdArray);
                    // $Query->whereIn(cn::USERS_CLASS_ID_COL,$classIds);
                    $Query->whereIn(cn::USERS_ID_COL,$this->curriculum_year_mapping_student_ids('',$request->classIds,''));
                }
            }
        }
        if(isset($request->gradeId) && !empty($request->gradeId)){
            // $Query->where(cn::USERS_GRADE_ID_COL,$request->gradeId);
            $Query->where(cn::USERS_ID_COL,$this->curriculum_year_mapping_student_ids($request->gradeId,'',''));
        }
        $StudentIds = $Query->get()->pluck(cn::USERS_ID_COL);
        if(!$StudentIds->isEmpty()){
            $ExamQuery = Exam::where(cn::EXAM_TABLE_IS_GROUP_TEST_COL,0);
            if($this->isSchoolLogin() || $this->isTeacherLogin()){
                $schoolId = Auth::user()->{cn::USERS_SCHOOL_ID_COL};
                $ExamQuery->whereRaw("find_in_set($schoolId,school_id)");
            }
            $ExamQuery->where(function ($query) use($StudentIds){
                foreach($StudentIds as $studentId){
                    $query->orWhereRaw("find_in_set($studentId,student_ids)");
                }
            });
            
            $ExamQuery->where(cn::EXAM_TABLE_STATUS_COLS,'publish');
            $ExamList = $ExamQuery->get();

            // set html option list for the test list
            if(!$ExamList->isEmpty()){
                $optionlist .= '<option value="">'.__("languages.select_test").'</option>';
                foreach($ExamList as $exams){
                    $school_id = explode(',',$exams->{cn::EXAM_TABLE_SCHOOL_COLS});
                    if(isset($school_id) && !empty($school_id)){
                        $school_id = $school_id[0];
                    }
                    $optionlist .= '<option value="'.$exams->{cn::EXAM_TABLE_SCHOOL_COLS}.'" data-school-id="'.$school_id.'">'.$exams->title.'</option>';
                }
            }
        }
        if(empty($optionlist)){
            $optionlist .= '<option value="">'.__("languages.no_available_tests").'</option>';
        }
        return $this->sendResponse($optionlist);
    }

    // Check Email Exists
    public function GetUserInfo(Request $request){
        if(isset($request->uid) && $request->uid!=''){
            $userDatalist = User::where(cn::USERS_ID_COL,$request->uid)->get()->toArray();
            if(!empty($userDatalist)){
                $alpChatUserId = $userDatalist[0]['alp_chat_user_id'];
                if($alpChatUserId == ""){
                    $alpChatUserId = $this->generateAlpChatUserId($request->uid);
                }
                $UserData = array(
                                'email' => $userDatalist[0]['email'],
                                'name_en' => \App\Helpers\Helper::decrypt($userDatalist[0]['name_en']),
                                'mobile_no' => \App\Helpers\Helper::decrypt($userDatalist[0]['mobile_no']),
                                'alp_chat_user_id' => $alpChatUserId
                            );
                return $this->sendResponse($UserData);
            }else{
                return $this->sendResponse(false);
            }
        }else{
            return $this->sendResponse(false);
        }
    }

    // Cron Job For a Question math formula load 
    public function GenerateMathFormulaImage(Request $request){
        $questionData = '';
        $questionData = Question::with('answers')->skip(0)->take(200)->get();
        return view('backend.update_question_formula',compact('questionData'));
    }

    /**
     * USE : Check Question code is exists or not
     */
    public function checkQuestionCodeExists(Request $request){
        if($request->questionCode){
            if(!empty($request->QuestionId)){
                if(Question::where(cn::QUESTION_NAMING_STRUCTURE_CODE_COL,$request->questionCode)->whereNotIn(cn::QUESTION_TABLE_ID_COL,[$request->QuestionId])->exists()){
                    return $this->sendResponse(true);
                }else{
                    return $this->sendResponse(false);
                }
            }else{
                if(Question::where(cn::QUESTION_NAMING_STRUCTURE_CODE_COL,$request->questionCode)->exists()){
                    return $this->sendResponse(true);
                }else{
                    return $this->sendResponse(false);
                }
            }
        }
    }

    /**
     * USE : Get Learning objectives based on multiple selected Learning units in Generate Questions with 
     */
    public function getLearningObjectivesFromMultipleLearningUnitsInGenerateQuestions(Request $request){
        try{
            $learningObjectivesIds = StrandUnitsObjectivesMappings::whereIn(cn::OBJECTIVES_MAPPINGS_STRAND_ID_COL,$request->strand_id)
                ->whereIn(cn::OBJECTIVES_MAPPINGS_LEARNING_UNIT_ID_COL,$request->learning_unit_id)
                ->where(function ($query) use ($request){
                    if(isset($request->grade_id) && !empty($request->grade_id)){
                        $query->where(cn::OBJECTIVES_MAPPINGS_GRADE_ID_COL,$request->grade_id);
                    }
                    if(isset($request->subject_id) && !empty($request->subject_id)){
                        $query->where(cn::OBJECTIVES_MAPPINGS_SUBJECT_ID_COL,$request->subject_id);
                    }
                })
                ->pluck(cn::OBJECTIVES_MAPPINGS_LEARNING_OBJECTIVES_ID_COL);
            if(!empty($learningObjectivesIds)){
                $learningObjectivesIds = array_unique($learningObjectivesIds->toArray());
                // $LearningObjectives = LearningsObjectives::whereIn(cn::LEARNING_OBJECTIVES_ID_COL, $learningObjectivesIds)->get();
                $LearningObjectives = LearningsObjectives::IsAvailableQuestion()->whereIn(cn::LEARNING_OBJECTIVES_ID_COL, $learningObjectivesIds)->get();
                $getNoOfQuestionPerLearningObjective = array();
                foreach($LearningObjectives as $dataValue){
                    if(isset($request->isInspectMode) && $request->isInspectMode==true){
                        $getNoOfQuestionPerLearningObjective[$dataValue->id]=\App\Helpers\Helper::CountAllQuestionPerLearningObjective($dataValue->learning_unit_id,$dataValue->id,$request->test_type,$request->difficulty_level);
                    }else{
                        $getNoOfQuestionPerLearningObjective[$dataValue->id]=\App\Helpers\Helper::getNoOfQuestionPerLearningObjective($dataValue->learning_unit_id,$dataValue->id);
                    }
                }
                $data = array(
                            'LearningObjectives' => $LearningObjectives,
                            'getNoOfQuestionPerLearningObjective' => $getNoOfQuestionPerLearningObjective
                        );
                return $this->sendResponse($data);
            }
        }catch(\Exception $ex){
            return $this->sendError($ex->getMessage(), 404);
        }
    }

    public function getRoleBasedPeerGroupData($peerGroupIds){
        $peerGroupDataArray = array();
        $peerGroupData = PeerGroup::select(cn::PEER_GROUP_ID_COL,cn::PEER_GROUP_GROUP_NAME_COL)
                        ->whereIn(cn::PEER_GROUP_ID_COL,$peerGroupIds)
                        ->where(cn::PEER_GROUP_STATUS_COL,1);
        if($this->isAdmin()){
            $peerGroupData = $peerGroupData->get();
        }else if($this->isTeacherLogin()){
            $peerGroupData = $peerGroupData->select(cn::PEER_GROUP_ID_COL,cn::PEER_GROUP_GROUP_NAME_COL)
                            ->where([
                                cn::PEER_GROUP_SCHOOL_ID_COL => Auth::user()->{cn::USERS_SCHOOL_ID_COL},
                                cn::PEER_GROUP_CREATED_BY_USER_ID_COL => Auth::user()->{cn::USERS_ID_COL}
                            ])->get();
        }else if($this->isSchoolLogin() || isPrincipalLogin()){
            $peerGroupData = $peerGroupData->select(cn::PEER_GROUP_ID_COL,cn::PEER_GROUP_GROUP_NAME_COL)
                            ->where([
                                cn::PEER_GROUP_SCHOOL_ID_COL => Auth::user()->{cn::USERS_SCHOOL_ID_COL}
                            ])->get();
        }
        return $peerGroupData;
    }

    // Generate Alp Chat User Id
    public function generateAlpChatUserId($userId){
        $newNumber='+852'.rand(10000000,90000000);
        $oldIds=User::where(cn::USERS_ALP_CHAT_USER_ID_COL,$newNumber)->get()->toArray();
        if(isset($oldIds) && !empty($oldIds)){
            $this->generateAlpChatUserId($userId);
        }else{
            User::find($userId)->update([cn::USERS_ALP_CHAT_USER_ID_COL=>$newNumber]);
            return $newNumber;
        }
    }

    /**
     * USE : Get the student list based on select grade and class
     * Return : Student list
     */
    public function getStudentListByGradeClassGroup(Request $request){
        $StudentList = [];
        $optionList = '';
        $schoolId = Auth::user()->{cn::USERS_SCHOOL_ID_COL};
        $ClassId = [];
        $isTeacher = 0;
        $gradesListId =array();
        $GradeClassId ='';
        $studentListType="GradeClass";
        if($request->dataType=='peer_group')
        {
            if(isset($request->peerGroupId) && !empty($request->peerGroupId)){
                $studentListType="PeerGroup_".$request->peerGroupId;
                // $getStudentFromGroupIds =  PeerGroupMember::where('peer_group_id',$request->peerGroupId)->pluck('member_id')->unique()->toArray();
                $getStudentFromGroupIds =  PeerGroupMember::where([
                                                                    cn::PEER_GROUP_MEMBERS_PEER_GROUP_ID_COL=> $request->peerGroupId,
                                                                    cn::PEER_GROUP_MEMBERS_CURRICULUM_YEAR_ID_COL =>$this->GetCurriculumYear()
                                                                ])->pluck(cn::PEER_GROUP_MEMBERS_MEMBER_ID_COL)->unique()->toArray();
                                                                
                // $studentList = User::where([cn::USERS_SCHOOL_ID_COL=>Auth::user()->{cn::USERS_SCHOOL_ID_COL},cn::USERS_ROLE_ID_COL=>cn::STUDENT_ROLE_ID])->whereIn(cn::USERS_ID_COL,$getStudentFromGroupIds)->get();
                $studentList = User::where([
                                                cn::USERS_SCHOOL_ID_COL=>Auth::user()->{cn::USERS_SCHOOL_ID_COL},
                                                cn::USERS_ROLE_ID_COL=>cn::STUDENT_ROLE_ID,
                                            ])
                                            ->whereIn(cn::USERS_ID_COL,$getStudentFromGroupIds)->get();
            }
        }
        else
        {
            $studentListType="GradeClass_".$request->classIds[0];
            if($this->isTeacherLogin()){
                $gradesListId = TeachersClassSubjectAssign::where([
                                    cn::TEACHER_CLASS_SUBJECT_TEACHER_ID_COL => Auth()->user()->{cn::USERS_ID_COL}
                                ])
                                ->pluck(cn::TEACHER_CLASS_SUBJECT_CLASS_ID_COL)
                                ->toArray();
                $GradeClassId = TeachersClassSubjectAssign::where([
                                    cn::TEACHER_CLASS_SUBJECT_TEACHER_ID_COL => Auth()->user()->{cn::USERS_ID_COL}
                                ])
                                ->pluck(cn::TEACHER_CLASS_SUBJECT_CLASS_NAME_ID_COL)
                                ->toArray();
                $GradeClassId = implode(',', $GradeClassId);
                $GradeClassId = explode(',',$GradeClassId);
                $isTeacher = 1;
            }
            // $studentList = User::where([cn::USERS_SCHOOL_ID_COL=>Auth::user()->{cn::USERS_SCHOOL_ID_COL},cn::USERS_ROLE_ID_COL=>cn::STUDENT_ROLE_ID])
            $studentList = User::where([
                                            cn::USERS_SCHOOL_ID_COL =>  Auth::user()->{cn::USERS_SCHOOL_ID_COL},
                                            cn::USERS_ROLE_ID_COL   =>  cn::STUDENT_ROLE_ID,
                                        ])
            ->where(function ($query) use ($request,$gradesListId,$GradeClassId,$isTeacher){
                if(!isset($request->gradeIds) && !isset($request->classIds)){
                    // $query->whereIn(cn::USERS_GRADE_ID_COL,$request->gradeIds)->whereIn(cn::USERS_CLASS_ID_COL,$request->classIds);
                    $query->whereIn(cn::USERS_ID_COL,$this->curriculum_year_mapping_student_ids($request->gradeIds,$request->classIds,Auth::user()->{cn::USERS_SCHOOL_ID_COL}));
                }else{
                    if(isset($request->gradeIds) && !empty($request->gradeIds)){
                        // $query->whereIn(cn::USERS_GRADE_ID_COL,$request->gradeIds);
                        $query->whereIn(cn::USERS_ID_COL,$this->curriculum_year_mapping_student_ids($request->gradeIds,'',Auth::user()->{cn::USERS_SCHOOL_ID_COL}));
                    }else if($isTeacher==1){
                        // $query->whereIn(cn::USERS_GRADE_ID_COL,$gradesListId);
                        $query->whereIn(cn::USERS_ID_COL,$this->curriculum_year_mapping_student_ids($request->gradesListId,'',Auth::user()->{cn::USERS_SCHOOL_ID_COL}));
                    }

                    if(isset($request->classIds) && !empty($request->classIds)){
                        // $query->whereIn(cn::USERS_CLASS_ID_COL,$request->classIds);
                        $query->whereIn(cn::USERS_ID_COL,$this->curriculum_year_mapping_student_ids('',$request->classIds,''));
                    }else if($isTeacher==1){
                        // $query->whereIn(cn::USERS_CLASS_ID_COL,$GradeClassId);
                        $query->whereIn(cn::USERS_ID_COL,$this->curriculum_year_mapping_student_ids('',$GradeClassId,Auth::user()->{cn::USERS_SCHOOL_ID_COL}));
                    }
                }
            })->get();

        }
        if(!$studentList->isEmpty()){
            foreach($studentList as $student){
                $optionList .= '<div class="col-md-2"><div class="form-group form-check"><input type="checkbox" class="form-check-input" name="student_ids['.$studentListType.']['.$student->id.']" value="'.$student->id.'" id="stu_'.$studentListType.'_'.$student->id.'"/><label class="form-check-label" for="stu_'.$studentListType.'_'.$student->id.'">';
                if(app()->getLocale() == 'en'){
                    $optionList .= $student->DecryptNameEn;
                }else{
                    $optionList .= $student->DecryptNameCh;
                }
                if($student->class_student_number){
                    $optionList .= '('.$student->class_student_number.')';
                }
                $optionList .= '</label></div></div>';
            }
        }else{
            $optionList .= '';
        }
        return $this->sendResponse([$optionList]);
    }

    public function UpdateAttemptedExamStudentMappings(){
        $AttemptExams = AttemptExams::get();
        if(isset($AttemptExams) && !empty($AttemptExams)){
            foreach($AttemptExams as $Data){
                if(AttemptExamStudentMapping::where([cn::ATTEMPT_EXAM_STUDENT_MAPPING_EXAM_ID_COL => $Data->exam_id, cn::ATTEMPT_EXAM_STUDENT_MAPPING_STUDENT_ID_COL => $Data->student_id])->exists()){
                    AttemptExamStudentMapping::find($Data->id)->Update([cn::ATTEMPT_EXAM_STUDENT_MAPPING_EXAM_ID_COL => $Data->exam_id, cn::ATTEMPT_EXAM_STUDENT_MAPPING_STUDENT_ID_COL => $Data->student_id]);
                }else{
                    AttemptExamStudentMapping::Create([cn::ATTEMPT_EXAM_STUDENT_MAPPING_EXAM_ID_COL => $Data->exam_id, cn::ATTEMPT_EXAM_STUDENT_MAPPING_STUDENT_ID_COL => $Data->student_id]);
                }
            }
        }
    }

    public function AjaxSetCurriculumYear(Request $request){
        $this->SetCurriculumYear($request->CurriculumYearId);
    }

    /**
     * USE : Set Null Column of Curriculum YEar Student Mapping in Student Number With in class,Class and permanent Reference number
     */
    public function setClassStudentNumberColumnValue(){
        $curriculumMappingData = CurriculumYearStudentMappings::all();
        if(!empty($curriculumMappingData)){
            foreach($curriculumMappingData as $curriculumMappingDataValue){
                $userData = User::find($curriculumMappingDataValue->user_id);
                CurriculumYearStudentMappings::where(cn::CURRICULUM_YEAR_STUDENT_MAPPING_ID_COL,$curriculumMappingDataValue->id)
                ->update([
                    cn::CURRICULUM_YEAR_STUDENT_NUMBER_WITHIN_CLASS_COL => $userData->student_number_within_class ?? null,
                    cn::CURRICULUM_YEAR_STUDENT_CLASS=> $userData->class ?? null,
                    cn::CURRICULUM_YEAR_CLASS_STUDENT_NUMBER =>$userData->class_student_number ?? null
                ]);
            }
            echo "Successfully Updated...";
        }  
    }

    /**
     * USE : Update in All Table of Curriculum Year Id
     */
    public function UpdateInAllTableCurriculumYearId(){
        GradeSchoolMappings::query()->update([cn::GRADES_MAPPING_CURRICULUM_YEAR_ID_COL => cn::DEFAULT_CURRICULUM_YEAR_ID]);
        GradeClassMapping::query()->update([cn::GRADE_CLASS_MAPPING_CURRICULUM_YEAR_ID_COL => cn::DEFAULT_CURRICULUM_YEAR_ID]);
        ClassPromotionHistory::query()->update([cn::CLASS_PROMOTION_HISTORY_CURRICULUM_YEAR_ID_COL => cn::DEFAULT_CURRICULUM_YEAR_ID]);
        AttemptExams::query()->update([cn::ATTEMPT_EXAMS_CURRICULUM_YEAR_ID_COL => cn::DEFAULT_CURRICULUM_YEAR_ID]);
        AttemptExamStudentMapping::query()->update([cn::ATTEMPT_EXAM_STUDENT_MAPPING_CURRICULUM_YEAR_ID_COL => cn::DEFAULT_CURRICULUM_YEAR_ID]);
        AuditLogs::query()->update([cn::AUDIT_LOGS_CURRICULUM_YEAR_ID_COL => cn::DEFAULT_CURRICULUM_YEAR_ID]);
        ClassSubjectMapping::query()->update([cn::CLASS_SUBJECT_MAPPING_CURRICULUM_YEAR_ID_COL => cn::DEFAULT_CURRICULUM_YEAR_ID]);
        ExamConfigurationsDetails::query()->update([cn::EXAM_CONFIGURATIONS_DETAILS_CURRICULUM_YEAR_ID_COL => cn::DEFAULT_CURRICULUM_YEAR_ID]);
        ExamCreditPointRulesMapping::query()->update([cn::EXAM_CREDIT_POINT_RULES_MAPPING_CURRICULUM_YEAR_ID_COL => cn::DEFAULT_CURRICULUM_YEAR_ID]);
        ExamGradeClassMappingModel::query()->update([cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_CURRICULUM_YEAR_ID_COL => cn::DEFAULT_CURRICULUM_YEAR_ID]);
        ExamSchoolMapping::query()->update([cn::EXAM_SCHOOL_MAPPING_CURRICULUM_YEAR_ID_COL => cn::DEFAULT_CURRICULUM_YEAR_ID]);
        IntelligentTutorVideos::query()->update([cn::INTELLIGENT_TUTOR_VIDEOS_CURRICULUM_YEAR_ID_COL => cn::DEFAULT_CURRICULUM_YEAR_ID]);
        PeerGroup::query()->update([cn::PEER_GROUP_CURRICULUM_YEAR_ID_COL => cn::DEFAULT_CURRICULUM_YEAR_ID]);
        PeerGroupMember::query()->update([cn::PEER_GROUP_MEMBERS_CURRICULUM_YEAR_ID_COL => cn::DEFAULT_CURRICULUM_YEAR_ID]);
        PreConfigurationDiffiltyLevel::query()->update([cn::PRE_CONFIGURE_DIFFICULTY_CURRICULUM_YEAR_ID_COL => cn::DEFAULT_CURRICULUM_YEAR_ID]);
        SubjectSchoolMappings::query()->update([cn::SUBJECT_MAPPING_CURRICULUM_YEAR_ID_COL => cn::DEFAULT_CURRICULUM_YEAR_ID]);
        TeachersClassSubjectAssign::query()->update([cn::TEACHER_CLASS_SUBJECT_CURRICULUM_YEAR_ID_COL => cn::DEFAULT_CURRICULUM_YEAR_ID]);
        MyTeachingReport::query()->update([cn::TEACHING_REPORT_CURRICULUM_YEAR_ID_COL => cn::DEFAULT_CURRICULUM_YEAR_ID]);
        UploadDocuments::query()->update([cn::UPLOAD_DOCUMENTS_CURRICULUM_YEAR_ID_COL => cn::DEFAULT_CURRICULUM_YEAR_ID]);
        UserCreditPointHistory::query()->update([cn::USER_CREDIT_POINT_HISTORY_CURRICULUM_YEAR_ID_COL => cn::DEFAULT_CURRICULUM_YEAR_ID]);
        Exam::query()->Update([cn::EXAM_CURRICULUM_YEAR_ID_COL => cn::DEFAULT_CURRICULUM_YEAR_ID]);
        User::query()->Update([cn::USERS_CURRICULUM_YEAR_ID_COL => cn::DEFAULT_CURRICULUM_YEAR_ID]);

        echo "Successfully Updated.";
    }

}