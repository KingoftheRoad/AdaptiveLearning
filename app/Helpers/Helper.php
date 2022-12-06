<?php

namespace App\Helpers;

use App\Models\Exam;
use App\Models\Role;
use App\Models\User;
use App\Models\Settings;
use App\Models\Grades;
use App\Models\GradeClassMapping;
use App\Models\PreConfigurationDiffiltyLevel;
use App\Constants\DbConstant As cn;
use DB;
use Illuminate\Support\Facades\Crypt;
use App\Models\Nodes;
use App\Models\Question;
use Illuminate\Support\Facades\Auth;
use App\Models\AttemptExams;
use App\Models\GlobalConfiguration;
use App\Models\StrandUnitsObjectivesMappings;
use App\Models\UserCreditPointHistory;
use App\Models\AttemptExamStudentMapping;
use App\Models\CurriculumYear;
use App\Models\CurriculumYearStudentMappings;
use Illuminate\Support\Facades\Session;
use App\Models\RemainderUpdateSchoolYearData;
use URL;
use Cookie;
use Carbon\Carbon;

class Helper{

    /**
     * USE : Get Field data by selected year
     */
    public static function GetCurriculumDataById($UserId, $Id, $FieldName=''){
        $CurriculumYearData = array();
        if(isset($FieldName) && !empty($FieldName)){
            $CurriculumYearData = CurriculumYearStudentMappings::select($FieldName)
                                    ->where([
                                        cn::CURRICULUM_YEAR_STUDENT_MAPPING_CURRICULUM_YEAR_ID_COL => $Id,
                                        cn::CURRICULUM_YEAR_STUDENT_MAPPING_USER_ID_COL => $UserId
                                    ])
                                    ->first();
            if(isset($CurriculumYearData) && !empty($CurriculumYearData)){                
                return $CurriculumYearData->$FieldName;
            }
        }else{
            $CurriculumYearData = CurriculumYearStudentMappings::where(cn::CURRICULUM_YEAR_STUDENT_MAPPING_CURRICULUM_YEAR_ID_COL,$id)->first();
            return $CurriculumYearData;
        }
    }
    
    // if permission not assign and try to forcefully to enter that time Redirect on Role based on Dashboard
    public static function redirectRoleBasedDashboard($user){
        switch(Auth::user()->{cn::USERS_ROLE_ID_COL}){
            case 1://Super Admin
                $redirectUrl =  URL::to('/super-admin/dashboard');
                break;
            case 2:  // Teacher
                $redirectUrl =  URL::to('/teacher/dashboard');
                break;
            case 3:  // Student
                $redirectUrl =  URL::to('/student/dashboard');
                break;
            case 4:   // Parent
                $redirectUrl =  URL::to('/parent/dashboard');
                break;
            case 5:    // School
                $redirectUrl =  URL::to('/schools/dashboard');
                break;
            case 6:  // External resource
                $redirectUrl = URL::to('/external_resource/dashboard');
                break;
            case 7:  // Principal
                $redirectUrl =  URL::to('/principal/dashboard');
                break;
            default:
                $redirectUrl =  URL::to('/');
        } 
        return $redirectUrl ?? '';
    }

    public static function getRoleBasedMenuActiveColor(){
        switch (Auth::user()->{cn::USERS_ROLE_ID_COL}) {
            case 1:
                $menuActiveColor = '#8687fd;';
                break;
            case 2:  // Teacher
                $menuActiveColor = '#ef8787;';
                break;
            case 3:  // Student
                $menuActiveColor = '#a3ad07;';
                break;
            case 4:   // Parent
                $menuActiveColor = '#57bd65;';
                break;
            case 5:    // School
                $menuActiveColor = '#57bd65;';
                break;
            case 6:  // External resource
                $menuActiveColor = '#57bd65;';
                break;
            case 7:  // Principal
                $menuActiveColor = '#46a59b;';
                break;
            default:
                $menuActiveColor = '#8687fd;';
        }
        return $menuActiveColor ?? '';
    }

    public static function isAdmin(){
        if(Auth::check() && Auth::user()->{cn::USERS_ROLE_ID_COL} == cn::SUPERADMIN_ROLE_ID){
            return true;
        }
        return false;
    }

    public static function isPrincipalLogin(){
        if(Auth::check() && Auth::user()->{cn::USERS_ROLE_ID_COL} == cn::PRINCIPAL_ROLE_ID){
            return true;
        }
        return false;
    }
    public static function isExternalUserLogin(){
        if(Auth::check() && Auth::user()->{cn::USERS_ROLE_ID_COL} == cn::EXTERNAL_RESOURCE_ROLE_ID){
            return true;
        }
        return false;
    }

    /**
     * USE : Check current logged user is school
     * Return : true | false
     */
    public static function isSchoolLogin(){
        if(Auth::check() && Auth::user()->{cn::USERS_ROLE_ID_COL} == cn::SCHOOL_ROLE_ID){
            return Auth::user()->{cn::USERS_SCHOOL_ID_COL};
        }
        return false;
    }

    /**
     * USE : Check current logged user is school
     * Return : true | false
     */
    public static function isTeacherLogin(){
        if(Auth::check() && Auth::user()->{cn::USERS_ROLE_ID_COL} == cn::TEACHER_ROLE_ID){
            return Auth::user()->{cn::USERS_SCHOOL_ID_COL};
        }
        return false;
    }

    public static function FindRoleByUserId($UserId){
        $UserData = User::find($UserId);        
        if(!empty($UserData)){
            switch($UserData->{cn::USERS_ROLE_ID_COL}){
                case cn::SUPERADMIN_ROLE_ID:
                    $roleType = 'Super Admin';
                    break;
                case cn::TEACHER_ROLE_ID:
                    $roleType = 'Teacher';
                    break;
                case cn::STUDENT_ROLE_ID:
                    $roleType = 'Student';
                    break;
                case cn::PARENT_ROLE_ID:
                    $roleType = 'Parent';
                    break;
                case cn::SCHOOL_ROLE_ID:
                    $roleType = 'School';
                    break;
                case cn::PRINCIPAL_ROLE_ID:
                    $roleType = 'Principal';
                    break;
                case cn::EXTERNAL_RESOURCE_ROLE_ID:
                    $roleType = 'External Resource';
                    break;
            }
            return $roleType;
        }
        if(Auth::check() && Auth::user()->{cn::USERS_ROLE_ID_COL} == cn::TEACHER_ROLE_ID){
            return Auth::user()->{cn::USERS_SCHOOL_ID_COL};
        }
        return false;
    }

    public static function getExamNames($examId){
        $TestName = '';
        $Exams = Exam::find($examId)->first();
        if(!empty($Exams)){
            $TestName = $Exams->title;
        }
        return $TestName;
    }

    /**
     * USE : Get Permission by user id
     */
    public static function getPermissions($user_id){
        $permissions = User::where(cn::USERS_ID_COL,$user_id)->with('roles')->get();
        foreach($permissions as $permission){
            return explode(',',$permission->roles->permission);
        }
    }

    /**
     * USE : Get Curriculum year list by student id
     */
    public static function getCurriculumYearList($UserId){
        $CurriculumYearList = [];
        $CurriculumYearStudentMappings = CurriculumYearStudentMappings::where([
                                            cn::CURRICULUM_YEAR_STUDENT_MAPPING_USER_ID_COL => $UserId,
                                            cn::CURRICULUM_YEAR_STUDENT_MAPPING_STATUS_COL => 1 // 1 = Active
                                        ])
                                        ->get()
                                        ->pluck(cn::CURRICULUM_YEAR_STUDENT_MAPPING_CURRICULUM_YEAR_ID_COL)
                                        ->toArray();
        if(isset($CurriculumYearStudentMappings) && !empty($CurriculumYearStudentMappings)){
            $CurriculumYearList = CurriculumYear::whereIn(cn::CURRICULUM_YEAR_ID_COL,$CurriculumYearStudentMappings)
                                    ->orderBy(cn::CURRICULUM_YEAR_ID_COL,'desc')
                                    ->get();
        }
        return $CurriculumYearList;
    }

    public static function getLevelNameBasedOnLanguage($difficulty_level){
        $levelName = '';
        if(!empty($difficulty_level)){
            $PreConfigurationDiffiltyLevel = PreConfigurationDiffiltyLevel::where(cn::PRE_CONFIGURE_DIFFICULTY_DIFFICULTY_LEVEL_COL,$difficulty_level)->first();
            if(isset($PreConfigurationDiffiltyLevel) && !empty($PreConfigurationDiffiltyLevel)){
                $levelName = $PreConfigurationDiffiltyLevel['difficulty_level_name_'.app()->getLocale()];
            }
        }
        return $levelName;
    }

    public static function getSettingData(){
        $siteSetting = array();
        $siteSetting = Settings::latest()->first();
        return $siteSetting;
    }

    /**
     * USE : Decrypt Data
     */
    public static function decrypt($string){
        return base64_decode($string);
    }

    /**
     * USE : Get All Nodes name which is available in Array
     */
    public static function getNodesName($nodeIds){
        if(!empty($nodeIds)){
            $NodeIds = explode(',',$nodeIds);
            $nodes = Nodes::whereIn(cn::NODES_NODE_ID_COL,$NodeIds)->get()->pluck(cn::NODES_NODEID_COL);
            return implode(',',$nodes->toArray());
        }
    }

    /**
     * USE : Get Node node by id
     */
    public static function getNodeNameById($nodeId){
        if($nodeId){
            $nodes = Nodes::find($nodeId);
            if($nodes){
                return $nodes->{cn::NODES_NODEID_COL};
            }
        }
    }

    //Get Single class Name on Grade selected for particular student 
    public static function getSingleClassName($id){
        $className = '';
        $ClassData = GradeClassMapping::where([
                        cn::GRADE_CLASS_MAPPING_CURRICULUM_YEAR_ID_COL => Self::GetCurriculumYear(),
                        cn::GRADE_CLASS_MAPPING_ID_COL => $id
                    ])
                    ->first();
        if(!empty($ClassData)){
            $className = strtoupper($ClassData->{cn::GRADE_CLASS_MAPPING_NAME_COL});
        }
        return $className;
    } 
    
    //on User to Select Class Name 
    public static function getClassNames($class_ids){
        $className = array();
        $classId = array();
        if(!empty($class_ids)){
            $classId = explode(',',$class_ids);
        }
        $ClassData = GradeClassMapping::where(cn::GRADE_CLASS_MAPPING_CURRICULUM_YEAR_ID_COL,Self::GetCurriculumYear())
                    ->whereIn(cn::GRADE_CLASS_MAPPING_ID_COL,$classId)->get();
        if(!empty($ClassData)){
            foreach($ClassData as $class){
                array_push($className,$class->{cn::GRADE_CLASS_MAPPING_NAME_COL});
            }
            $className = implode(',',$className);
        }
        return $className;
    }

    // Get Ability
    public static function getAbility($accuracy){
        if(!empty($accuracy)){
            $convertedAccuracy = ($accuracy / 100);
            return exp($convertedAccuracy) / (1 + exp($convertedAccuracy));
        }else{
            return $accuracy;
        }
    }

    public static function getNormalizedAbility($ability){
        $ability = exp($ability) / (1 + exp($ability)) * 100;
        return round($ability,2);
    }

    /**
     * USE : Get Short Normalized ability
     * Ex. Ability = 55.2 then this function after calculation return 5.52
     */
    public static function getShortNormalizedAbility($ability){
        $ability = ((\App\Helpers\Helper::getNormalizedAbility($ability) / 100) * 10);
        return round($ability);
    }


    // Get Accuracy Details
    public static function getAccuracy($examId='', $studentId){
        $accuracy = 0;
        $total_correct_answers = 0;
        $examData = Exam::find($examId);
        $totalQuestion = count(explode(',',$examData->{cn::EXAM_TABLE_QUESTION_IDS_COL}));
        $attemptedStudentExams = AttemptExams::where(cn::ATTEMPT_EXAMS_STUDENT_STUDENT_ID,$studentId)->where(cn::ATTEMPT_EXAMS_EXAM_ID,$examId)->first();
        if(isset($attemptedStudentExams) && !empty($attemptedStudentExams)){
            $total_correct_answers += $attemptedStudentExams->{cn::ATTEMPT_EXAMS_TOTAL_CORRECT_ANSWERS};
            if(!empty($totalQuestion) && !empty($total_correct_answers)){
                $accuracy = round(($total_correct_answers / $totalQuestion * 100), 2);
            }
        }
        return $accuracy;
    }

    // Get All student combine accuracy
    public static function getAccuracyAllStudent($examId, $studentIds){
        $accuracy = 0;
        $total_correct_answers = 0;
        $totalCountQuestion = 0;
        $countAttemptedStudent = 0;
        $totalQuestion = 0;
        $students = explode(',',$studentIds);
        $examData = Exam::find($examId);
        $totalQuestion = explode(',',$examData->{cn::EXAM_TABLE_QUESTION_IDS_COL});
        $attemptedStudentExams = AttemptExams::whereIn(cn::ATTEMPT_EXAMS_STUDENT_STUDENT_ID,$students)->where(cn::ATTEMPT_EXAMS_EXAM_ID,$examId)->get();
        if(isset($attemptedStudentExams) && !empty($attemptedStudentExams)){
            $countAttemptedStudent = count($attemptedStudentExams);
            if(!empty($countAttemptedStudent)){
                $totalCountQuestion = count($totalQuestion) * (int)$countAttemptedStudent;
            }
            foreach($attemptedStudentExams as $attemptStudent){
                $total_correct_answers += $attemptStudent->{cn::ATTEMPT_EXAMS_TOTAL_CORRECT_ANSWERS};
            }

            if(!empty($totalCountQuestion) && !empty($total_correct_answers)){
                $accuracy = round(($total_correct_answers / $totalCountQuestion * 100), 2);
            }
        }
        return $accuracy;
    }

    // Get Created By Admin name of Sub-Admin
    public static function getSubAdminCreatedByAdminName($id){
        $createdByAdmin = User::find($id);
        if(!empty($createdByAdmin)){
            return $createdByAdmin->{cn::USERS_NAME_COL};
        }
        return '';
    }

    public static function getWeaknessNodeId($questionId, $answerNumber){
        $answers_node_id_check = 0;
        if(!empty($questionId)){
            $questionDetail = Question::find($questionId);
            $arrayOfQuestionCode = explode('-',$questionDetail->{cn::QUESTION_QUESTION_CODE_COL});
            unset($arrayOfQuestionCode[count($arrayOfQuestionCode)-1]);
            $newQuestionCode = implode('-',$arrayOfQuestionCode);
            $newQuestionData = Question::with('answers')->where(cn::QUESTION_QUESTION_CODE_COL,$newQuestionCode)->first();
            if(isset($newQuestionData->answers) && !empty($newQuestionData->answers)){
                $answers_node_id_check = $newQuestionData->answers->{'answer'.$answerNumber.'_node_relation_id_en'};
            }
            return $answers_node_id_check;
        }
    }

     /**
     * Get the Grade Name 
     */
    public static function getGradeName($id){
        $grade = Grades::where(cn::GRADES_ID_COL,$id)->first();
        return $grade->{cn::GRADES_NAME_COL};
    }

     /**
     * USE : Get Global Configuration value using key name
     */
    public static function getGlobalConfiguration($key){
        $configuration = GlobalConfiguration::where(cn::GLOBAL_CONFIGURATION_KEY_COL,$key)->first();
        if(isset($configuration) && !empty($configuration)){
            return $configuration->{cn::GLOBAL_CONFIGURATION_VALUE_COL} ?? null;
        }
        return null;
    }


    /****
     * USE :Get Question Difficuty Level
    */
    public static function getQuestionDifficultiesLevelPercent($examId, $studentIds){
        $progressBar = ['Level1' => 0,'Level2' => 0,'Level3' => 0,'Level4' => 0,'Level5' => 0];
        $totalQuestion = 0;
        $ExamsIds = explode(',',$examId);
        if(count($ExamsIds) == 1){
            $examData = Exam::find($examId);
            $Questions = explode(',',$examData->{cn::EXAM_TABLE_QUESTION_IDS_COL});
            $totalQuestion = count($Questions);
            foreach($Questions as $key => $question){
                $questionData = Question::withTrashed()->find($question);
                if($questionData){
                    if($questionData->dificulaty_level == 1){
                        $progressBar['Level1'] = $progressBar['Level1'] + 1;
                    }else if($questionData->dificulaty_level == 2){
                        $progressBar['Level2'] = $progressBar['Level2'] + 1;
                    }elseif($questionData->dificulaty_level == 3){
                        $progressBar['Level3'] = $progressBar['Level3'] + 1;
                    }elseif($questionData->dificulaty_level == 4){
                        $progressBar['Level4'] = $progressBar['Level4'] + 1;
                    }elseif($questionData->dificulaty_level == 5){
                        $progressBar['Level5'] = $progressBar['Level5'] + 1;
                    }
                }
            }
        }else{
            foreach($ExamsIds as $exams){
                $examData = Exam::find($exams);
                $Questions = explode(',',$examData->{cn::EXAM_TABLE_QUESTION_IDS_COL});
                $totalQuestion += count($Questions);
                foreach($Questions as $key => $question){
                    $questionData = Question::find($question);
                    if($questionData){
                        if($questionData->dificulaty_level == 1){
                            $progressBar['Level1'] = $progressBar['Level1'] + 1;
                        }else if($questionData->dificulaty_level == 2){
                            $progressBar['Level2'] = $progressBar['Level2'] + 1;
                        }elseif($questionData->dificulaty_level == 3){
                            $progressBar['Level3'] = $progressBar['Level3'] + 1;
                        }elseif($questionData->dificulaty_level == 4){
                            $progressBar['Level4'] = $progressBar['Level4'] + 1;
                        }elseif($questionData->dificulaty_level == 5){
                            $progressBar['Level5'] = $progressBar['Level5'] + 1;
                        }
                    }
                }
            }
        }
        if(!empty($progressBar['Level1'])){
            $progressBar['Level1']= \App\Helpers\Helper::progressCalculationQuestionDifficultyLevel($totalQuestion,$progressBar['Level1']);
            $progressBar['Level1_color'] = PreConfigurationDiffiltyLevel::select(cn::PRE_CONFIGURE_DIFFICULTY_DIFFICULTY_LEVEL_COLOR_COL)->where(cn::PRE_CONFIGURE_DIFFICULTY_DIFFICULTY_LEVEL_COL,1)->first()->toArray()[cn::PRE_CONFIGURE_DIFFICULTY_DIFFICULTY_LEVEL_COLOR_COL];
        }
        if(!empty($progressBar['Level2'])){
            $progressBar['Level2']= \App\Helpers\Helper::progressCalculationQuestionDifficultyLevel($totalQuestion,$progressBar['Level2']);
            $progressBar['Level2_color'] = PreConfigurationDiffiltyLevel::select(cn::PRE_CONFIGURE_DIFFICULTY_DIFFICULTY_LEVEL_COLOR_COL)->where(cn::PRE_CONFIGURE_DIFFICULTY_DIFFICULTY_LEVEL_COL,2)->first()->toArray()[cn::PRE_CONFIGURE_DIFFICULTY_DIFFICULTY_LEVEL_COLOR_COL];
        }
        if(!empty($progressBar['Level3'])){
            $progressBar['Level3']= \App\Helpers\Helper::progressCalculationQuestionDifficultyLevel($totalQuestion,$progressBar['Level3']);
            $progressBar['Level3_color'] = PreConfigurationDiffiltyLevel::select(cn::PRE_CONFIGURE_DIFFICULTY_DIFFICULTY_LEVEL_COLOR_COL)->where(cn::PRE_CONFIGURE_DIFFICULTY_DIFFICULTY_LEVEL_COL,3)->first()->toArray()[cn::PRE_CONFIGURE_DIFFICULTY_DIFFICULTY_LEVEL_COLOR_COL];
        }
        if(!empty($progressBar['Level4'])){
            $progressBar['Level4']= \App\Helpers\Helper::progressCalculationQuestionDifficultyLevel($totalQuestion,$progressBar['Level4']);
            $progressBar['Level4_color'] = PreConfigurationDiffiltyLevel::select(cn::PRE_CONFIGURE_DIFFICULTY_DIFFICULTY_LEVEL_COLOR_COL)->where(cn::PRE_CONFIGURE_DIFFICULTY_DIFFICULTY_LEVEL_COL,4)->first()->toArray()[cn::PRE_CONFIGURE_DIFFICULTY_DIFFICULTY_LEVEL_COLOR_COL];
        }
        if(!empty($progressBar['Level5'])){
            $progressBar['Level5']= \App\Helpers\Helper::progressCalculationQuestionDifficultyLevel($totalQuestion,$progressBar['Level5']);
            $progressBar['Level5_color'] = PreConfigurationDiffiltyLevel::select(cn::PRE_CONFIGURE_DIFFICULTY_DIFFICULTY_LEVEL_COLOR_COL)->where(cn::PRE_CONFIGURE_DIFFICULTY_DIFFICULTY_LEVEL_COL,5)->first()->toArray()[cn::PRE_CONFIGURE_DIFFICULTY_DIFFICULTY_LEVEL_COLOR_COL];
        }
        return $progressBar;
    }

    /**
     * USE : Get Progress Detail in Teacher Panel
     */
    public static function getProgressDetail($students,$studentCompetenceList){
        $progressBar = ['Struggling' => 0,'Beginning' => 0,'Approaching' => 0,'Proficient' => 0,'Advanced' => 0, 'InComplete' => 0];
        
        $strugglingValue    = \App\Helpers\Helper::getStudyStatusValue('struggling');
        $beginningValue     = \App\Helpers\Helper::getStudyStatusValue('beginning');
        $approachingValue   = \App\Helpers\Helper::getStudyStatusValue('approaching');
        $proficientValue    = \App\Helpers\Helper::getStudyStatusValue('proficient');
        $advancedValue      = \App\Helpers\Helper::getStudyStatusValue('advanced');

        if(!empty($studentCompetenceList)){
            foreach ($studentCompetenceList as $studentCompetence) {
                if($studentCompetence >= $strugglingValue['from'] && $studentCompetence <= $strugglingValue['to']){
                    $progressBar['Struggling'] = ($progressBar['Struggling'] + 1);
                }else if($studentCompetence >= $beginningValue['from']  && $studentCompetence <= $beginningValue['to']){
                    $progressBar['Beginning'] = ($progressBar['Beginning'] + 1);
                }else if($studentCompetence >= $approachingValue['from'] && $studentCompetence <=  $approachingValue['to']){
                    $progressBar['Approaching'] = ($progressBar['Approaching'] + 1);
                }else if($studentCompetence >= $proficientValue['from'] && $studentCompetence <= $proficientValue['to']){
                    $progressBar['Proficient'] = ($progressBar['Proficient'] + 1);
                }else if($studentCompetence >= $advancedValue['from'] && $studentCompetence <= $advancedValue['to']){
                    $progressBar['Advanced'] = ($progressBar['Advanced'] + 1);
                }
            }
        }
        
        if(!empty($progressBar['Struggling'])){
            $progressBar['Struggling']= \App\Helpers\Helper::progressCalculation($students,$progressBar['Struggling']);
        }
        if(!empty($progressBar['Beginning'])){
            $progressBar['Beginning']= \App\Helpers\Helper::progressCalculation($students,$progressBar['Beginning']);
        }
        if(!empty($progressBar['Approaching'])){
            $progressBar['Approaching']= \App\Helpers\Helper::progressCalculation($students,$progressBar['Approaching']);
        }
        if(!empty($progressBar['Proficient'])){
            $progressBar['Proficient']= \App\Helpers\Helper::progressCalculation($students,$progressBar['Proficient']);
        }
        if(!empty($progressBar['Advanced'])){
            $progressBar['Advanced']= \App\Helpers\Helper::progressCalculation($students,$progressBar['Advanced']);
        }
        $progressBar['InComplete'] = round(100 - ($progressBar['Struggling'] + $progressBar['Beginning'] + $progressBar['Approaching'] + $progressBar['Proficient'] + $progressBar['Advanced']),2);
        return $progressBar;
    }

    /**
     * USE : Progress Calculation
     */
    public static function progressCalculation($students,$progressCount){
        if($progressCount!=0){        
            return round(($progressCount * 100) / count($students), 2);
        }else{
            return 0;
        }
    }

     /**
     * USE : Progress Calculation of Question Difficulty Levels
     */
    public static function progressCalculationQuestionDifficultyLevel($TotalQuestion,$progressCount){
        if($progressCount!=0){        
            return round(($progressCount * 100) / $TotalQuestion,2);
        }else{
            return 0;
        }
    }

    // Get All student Average No Of Question Answered Correctly
    public static function getAverageNoOfQuestionAnsweredCorrectly($examId, $studentIds){
        $QuestionAnsweredCorrectly = 0;
        $total_correct_answers = 0;
        $totalCountQuestion = 0;
        $countAttemptedStudent = 0;
        $totalQuestion = 0;
        $students = explode(',',$studentIds);
        $examData = Exam::find($examId);
        $totalQuestion = explode(',',$examData->{cn::EXAM_TABLE_QUESTION_IDS_COL});
        $attemptedStudentExams = AttemptExams::whereIn(cn::ATTEMPT_EXAMS_STUDENT_STUDENT_ID,$students)
                                ->where(cn::ATTEMPT_EXAMS_EXAM_ID,$examId)
                                ->get();
        if(isset($attemptedStudentExams) && !empty($attemptedStudentExams)){
            $countAttemptedStudent = count($attemptedStudentExams);
            if(!empty($countAttemptedStudent)){
                $totalCountQuestion = count($totalQuestion) * (int)$countAttemptedStudent;
            }
            foreach($attemptedStudentExams as $attemptStudent){
                $total_correct_answers += $attemptStudent->{cn::ATTEMPT_EXAMS_TOTAL_CORRECT_ANSWERS};
            }

            if($total_correct_answers!=0){
                $QuestionAnsweredCorrectly = round($total_correct_answers / count($students), 2);
            }
            $QuestionAnsweredCorrectly = '('.$QuestionAnsweredCorrectly.'/'.$totalCountQuestion.')';
        }
        return $QuestionAnsweredCorrectly;
    }

     //Get Study Status From To Value
     public static function getStudyStatusValue($key){
        $studyStatus = (array) json_decode(\App\Helpers\Helper::getGlobalConfiguration($key));
        return $studyStatus ?? [];
    }

    // Get Ability Type
    public static function getAbilityType($ability){
        $accuracy_type='';

        $strugglingValue = \App\Helpers\Helper::getStudyStatusValue('struggling');
        $beginningValue = \App\Helpers\Helper::getStudyStatusValue('beginning');
        $approachingValue = \App\Helpers\Helper::getStudyStatusValue('approaching');
        $proficientValue = \App\Helpers\Helper::getStudyStatusValue('proficient');
        $advancedValue = \App\Helpers\Helper::getStudyStatusValue('advanced');

        if(!empty($ability)){
            if($ability >= $strugglingValue['from'] && $ability <= $strugglingValue['to']){
                $accuracy_type = 'struggling_color';
            }else if($ability >= $beginningValue['from']  && $ability <= $beginningValue['to']){
                $accuracy_type = 'beginning_color';
            }else if($ability >= $approachingValue['from'] && $ability <=  $approachingValue['to']){
                $accuracy_type = 'approaching_color';
            }else if($ability >= $proficientValue['from'] && $ability <= $proficientValue['to']){
                $accuracy_type = 'proficient_color';
            }else if($ability >= $advancedValue['from'] && $ability <= $advancedValue['to']){
                $accuracy_type = 'advanced_color';
            }
            return $accuracy_type;
        }else{
            return $accuracy_type = 'incomplete_color';
        }
    }

    // time to minutes
    public static function timeToSecond(string $time=""){
        if(isset($time)){
            $arr = explode(':', $time);
            if (count($arr) === 3) {
                $timeArr = explode(':', $time);
                $time_minutes = ($timeArr[0]*60) + ($timeArr[1]) + ($timeArr[2]/60);
                return $time_minutes;
            }
            if (count($arr) === 3) {
                $timeArr = explode(':', $time);
                $time_minutes = ($timeArr[0]*60) + ($timeArr[1]) + ($timeArr[2]/60);
                return $time_minutes;
            }
        }
        return 0;
    }

    // Convert second to Time(H:M:S)
    public static function secondToTime($seconds){
        if($seconds){
            return sprintf('%02d:%02d:%02d', ($seconds/ 3600),($seconds/ 60 % 60), $seconds% 60);
        }
    }

    /**
     * USE : Get Question per speed
     */
    public static function getQuestionPerSpeed($exam_id, $studentId = 0){
        $per_question_time = 0;
        if(empty($studentId)){
            $studentId = Auth::user()->{cn::USERS_ID_COL};
        }
        $exam = Exam::where(cn::EXAM_TABLE_ID_COLS,$exam_id)->whereRaw("find_in_set($studentId,student_ids)")->get()->toArray();
        $attempt_exams = AttemptExams::where(cn::ATTEMPT_EXAMS_EXAM_ID,$exam_id)->where(cn::ATTEMPT_EXAMS_STUDENT_STUDENT_ID,$studentId)->get()->toArray();
        $question_ids = $exam[0][cn::EXAM_TABLE_QUESTION_IDS_COL];
        $exam_taking_timing = $attempt_exams[0][cn::ATTEMPT_EXAMS_EXAM_TAKING_TIMING];
        $exam_taking_timing_second = \App\Helpers\Helper::timeToSecond($exam_taking_timing ?? "00:00:00");
        if($question_ids != "" && !empty($question_ids)){
            $question_ids_size = sizeof(explode(',',$question_ids));
            $per_question_time = round($exam_taking_timing_second/$question_ids_size,2);
        }
        return $per_question_time;
    }

    public static function getDifficultyLevelColors(){
        $difficultyColor = [];
        $difficultyData = PreConfigurationDiffiltyLevel::All();
        if(!empty($difficultyData)){
            foreach($difficultyData as $difficulty){
                $difficultyColor['Level'.$difficulty->difficulty_level] = $difficulty->difficulty_level_color;
            }
        }
        return $difficultyColor;
    }

    /**
     * USE : Get count no of Question by Learning Skills
     */
    public static function getNoOfQuestionPerLearningObjective($learningUnitId,$learningObjectiveId){
        $minimumQuestionPerSkill = \App\Helpers\Helper::getGlobalConfiguration('no_of_questions_per_learning_skills') ?? 2 ;
        $StrandUnitsObjectivesMappingsIds = StrandUnitsObjectivesMappings::where([
                                                cn::OBJECTIVES_MAPPINGS_LEARNING_UNIT_ID_COL => $learningUnitId,
                                                cn::OBJECTIVES_MAPPINGS_LEARNING_OBJECTIVES_ID_COL => $learningObjectiveId
                                            ])->pluck(cn::OBJECTIVES_MAPPINGS_ID_COL)->toArray();
        if(isset($StrandUnitsObjectivesMappingsIds) && !empty($StrandUnitsObjectivesMappingsIds)){
            $QuestionSkill = Question::with('PreConfigurationDifficultyLevel')
                            ->whereIn(cn::QUESTION_OBJECTIVE_MAPPING_ID_COL,$StrandUnitsObjectivesMappingsIds)
                            ->groupBy(cn::QUESTION_E_COL)
                            ->pluck(cn::QUESTION_E_COL)
                            ->count();
            if($QuestionSkill == 0){
                return $minimumQuestionPerSkill;
            }
            return $totalQuestion = ($QuestionSkill * $minimumQuestionPerSkill);
        }else{
            return $minimumQuestionPerSkill;
        }
    }

    /**
     * USE : Get count no of Question by Learning Skills
     */
    public static function CountAllQuestionPerLearningObjective($learningUnitId,$learningObjectiveId,$testType='test',$DifficultyLevel=array(2),$noOfQuestion=''){
        $TotalQuestionPerObjectives = 0;
        $StrandUnitsObjectivesMappingsIds = StrandUnitsObjectivesMappings::where([
                                                cn::OBJECTIVES_MAPPINGS_LEARNING_UNIT_ID_COL => $learningUnitId,
                                                cn::OBJECTIVES_MAPPINGS_LEARNING_OBJECTIVES_ID_COL => $learningObjectiveId
                                            ])
                                            ->pluck(cn::OBJECTIVES_MAPPINGS_ID_COL)->toArray();
        if(isset($StrandUnitsObjectivesMappingsIds) && !empty($StrandUnitsObjectivesMappingsIds)){
            $QuestionQuery = Question::with('PreConfigurationDifficultyLevel')
                    ->whereIn(cn::QUESTION_OBJECTIVE_MAPPING_ID_COL,$StrandUnitsObjectivesMappingsIds)
                    ->where(function($query) use($testType){
                        if(isset($testType) && !empty($testType)){
                            if($testType == 'test'){
                                $query->where(cn::QUESTION_QUESTION_TYPE_COL,3);
                            }else if($testType == 'exercise'){
                                $query->where(cn::QUESTION_QUESTION_TYPE_COL,2);
                            }else if($testType == 'self_learning'){
                                $query->where(cn::QUESTION_QUESTION_TYPE_COL,1);
                            }else if($testType == 'testing_zone'){
                                $query->where(cn::QUESTION_QUESTION_TYPE_COL,1);
                            }else if($testType == 'seed'){
                                $query->where(cn::QUESTION_QUESTION_TYPE_COL,4);
                            }
                        }
                    });
            if(isset($DifficultyLevel) && !empty($DifficultyLevel)){
                $QuestionQuery->whereIn(cn::QUESTION_DIFFICULTY_LEVEL_COL,$DifficultyLevel);
            }
            if(isset($noOfQuestion) && !empty($noOfQuestion)){
                $QuestionQuery->limit($noOfQuestion);
            }
            $TotalQuestionPerObjectives = $QuestionQuery->orderBy(cn::QUESTION_QUESTION_CODE_COL,'ASC')->count();
        }
        return $TotalQuestionPerObjectives;
    }

    public static function getAttemptedChildExamResultStudent($examId,$studentId){
        $examData = collect();
        $AttemptedChildExamIds = Exam::where(cn::EXAM_TABLE_PARENT_EXAM_ID_COLS,$examId)->pluck(cn::EXAM_TABLE_ID_COLS)->toArray();
        if(!empty($AttemptedChildExamIds)){
            $examData = Exam::whereIn(cn::EXAM_TABLE_ID_COLS,$AttemptedChildExamIds)
                        ->whereRaw("find_in_set($studentId,student_ids)")
                        ->get();
        }else{
            $examData = Exam::where(cn::EXAM_TABLE_ID_COLS,$examId)->get();
        } 
        return $examData[0]->{cn::EXAM_TABLE_ID_COLS};
    }

    /* Generate Dynamic Color code in RGB Format*/
    public static function RandomColorGenerator(){
        $rgbColor = array();
        //Create a loop.
        foreach(array('r', 'g', 'b') as $color){
            //Generate a random number between 0 and 255.
            $rgbColor[$color] = mt_rand(0, 255);
        }
        return $rgbColor;
    }

    /* Generate Dynamic Color code in RGB Format*/
    public static function getUserCreditPointType($id,$userId,$examId){
        $examData = UserCreditPointHistory::with('getExam')->where(cn::USER_CREDIT_POINT_HISTORY_USER_ID_COL,$userId);
        if($examId != ""){
            $examData->where(cn::USER_CREDIT_POINT_HISTORY_EXAM_ID_COL,$examId);
        }else{
            $examData->where(cn::USER_CREDIT_POINT_HISTORY_ID_COL,$id);
        }
        $examData = $examData->get()->toArray();
        if(isset($examData) && !empty($examData)){
            $examCreditPointHtml = '';
            $examAchieveCreditPoint = 0;
            foreach ($examData as $examValue) {
                $examAchieveCreditPoint = $examAchieveCreditPoint+$examValue['no_of_credit_point'];
                   $examCreditPointHtml .= ucwords(str_replace('_', ' ', $examValue['credit_point_type'])).' : '.$examValue['no_of_credit_point'].'<br>';
            }
            return array(
                'examCreditPointHtml' => $examCreditPointHtml,
                'examAchieveCreditPoint' => $examAchieveCreditPoint
            );
        }
    }

    /* Percentage Display from 1-100 */
    public static function GetShortPercentage($percentage){
        return  round(($percentage / 10));
    }

    /**
     * USE : Get  OLD Short Normalized ability
     * Ex. Ability = 55.2 then this function after calculation return 5.52
     */
    public static function getOldShortNormalizedAbility($ability){
        $ability = ((\App\Helpers\Helper::getNormalizedAbility($ability) / 100) * 10);
        return round($ability,2);
    }

    /**
     * USE : Get color code array
     */
    public static function GetColorCodes(){
        $ColorCode = [
            'accomplished_color' => Self::getGlobalConfiguration('accomplished_objective'),
            'not_accomplished_color' => Self::getGlobalConfiguration('not_accomplished_objective'),
            'struggling_color' => Self::getGlobalConfiguration('struggling_color'),
            'beginning_color' => Self::getGlobalConfiguration('beginning_color'),
            'approaching_color' => Self::getGlobalConfiguration('approaching_color'),
            'proficient_color' => Self::getGlobalConfiguration('proficient_color'),
            'advanced_color' => Self::getGlobalConfiguration('advanced_color'),
            'incomplete_color' => Self::getGlobalConfiguration('incomplete_color')
        ];
        return $ColorCode;         
    }

    public static function GetCountCrediPointsStudent($examId, $studentId){
        $noOfCrediPoint = 0;
        $CreditPointData =  UserCreditPointHistory::where([
                                cn::USER_CREDIT_POINT_HISTORY_CURRICULUM_YEAR_ID_COL => Self::GetCurriculumYear(),
                                cn::USER_CREDIT_POINT_HISTORY_EXAM_ID_COL => $examId,
                                cn::USER_CREDIT_POINT_HISTORY_USER_ID_COL => $studentId
                            ])->get();
        if(!empty($CreditPointData)){
            $noOfCrediPoint = $CreditPointData->sum(cn::USER_CREDIT_POINT_HISTORY_NO_OF_CREDIT_POINT_COL);
        }
        return $noOfCrediPoint;
    }

    public static function getCookie($cookieName){
        $cookieData = Cookie::get($cookieName);
        $value = '';
        if(!empty($cookieData)){
            $value = json_decode($cookieData,true);
        }
        return $value;
    }

    /**
     * USE : Check current logged user is student
     * Return : true | false
     */
    public static function isStudentLogin(){
        if(Auth::check() && Auth::user()->{cn::USERS_ROLE_ID_COL} == cn::STUDENT_ROLE_ID){
            return Auth::user()->{cn::USERS_SCHOOL_ID_COL};
        }
        return false;
    }

    /**
     * USE : Check Exam Student Mapping Table
     */
    public static function CheckExamStudentMapping($ExamId){
        if(AttemptExamStudentMapping::where([
            cn::ATTEMPT_EXAM_STUDENT_MAPPING_CURRICULUM_YEAR_ID_COL => Self::GetCurriculumYear(),
            cn::ATTEMPT_EXAM_STUDENT_MAPPING_EXAM_ID_COL => $ExamId,
            cn::ATTEMPT_EXAM_STUDENT_MAPPING_STUDENT_ID_COL => Auth::user()->{cn::USERS_ID_COL}
        ])->exists()){
            return true;
        }else{
            return false;
        }
    }

    /**
     * USE : Get Particular School All Teacher
     */
    public static function GetAllTeacherOfSchool($schoolId){
        $teacherList =  User::where([
                            cn::USERS_SCHOOL_ID_COL => $schoolId,
                            cn::USERS_ROLE_ID_COL => cn::TEACHER_ROLE_ID
                        ])
                        ->pluck(cn::USERS_ID_COL)
                        ->toArray();
        return $teacherList;
    }

    /**
     * USE : Get Curriculum Year
     */
    public static function GetCurriculumYear(){
        if(empty(Auth::user()->{cn::USERS_CURRICULUM_YEAR_ID_COL})){            
            Self::SetCurriculumYear();
        }        
        return Auth::user()->{cn::USERS_CURRICULUM_YEAR_ID_COL};        
    }

    /**
     * USE : Set  Default curriculum Year
     */
    public static function SetCurriculumYear($CurriculumYearId=''){
        if(isset($CurriculumYearId) && !empty($CurriculumYearId)){
           User::find(Auth::user()->id)->Update([
                cn::USERS_CURRICULUM_YEAR_ID_COL => $CurriculumYearId
            ]);
        }else{
            if(User::where([
                cn::USERS_ID_COL => Auth::user()->id,
                cn::USERS_CURRICULUM_YEAR_ID_COL => null
            ])->exists()){
                User::find(Auth::user()->id)->Update([
                    cn::USERS_CURRICULUM_YEAR_ID_COL => cn::DEFAULT_CURRICULUM_YEAR_ID
                ]);
            }
        }
    }

    /**
	 *USE : Date Formate D/M/Y Format 
	 **/
	public static function dateConvertDDMMYYY($fromseprator,$toseprator,$date){
		$date = str_replace($fromseprator,$toseprator,$date);
		return date('d/m/Y',strtotime($date));
	}

    /**
     * USE : Check school reminder is enabled or not
     * Return : true or false
     */
    public static function isRemainderEnabledCheck(){
        // Find Next Curriculum Year
        $nextCurriculumYear = (((int)Carbon::now()->format('Y')+1).'-'.((int)(Carbon::now()->format('y'))+2));
        $CurriculumYear = CurriculumYear::where([
            cn::CURRICULUM_YEAR_YEAR_COL => $nextCurriculumYear,
            cn::CURRICULUM_YEAR_STATUS_COL => 'active'
        ])->first();
        if(RemainderUpdateSchoolYearData::where([
            cn::REMAINDER_UPDATE_SCHOOL_YEAR_DATA_CURRICULUM_YEAR_ID_COL => $CurriculumYear->{cn::CURRICULUM_YEAR_ID_COL},
            cn::REMAINDER_UPDATE_SCHOOL_YEAR_DATA_SCHOOL_ID_COL => Auth::user()->school_id,
            cn::REMAINDER_UPDATE_SCHOOL_YEAR_DATA_STATUS_COL => 'pending'
        ])->exists()){
            return true;
        }else{
            return false;
        }
    }
}
