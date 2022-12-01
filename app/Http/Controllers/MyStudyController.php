<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Reports\AlpAiGraphController;
use App\Constants\DbConstant As cn;
use App\Traits\Common;
use App\Traits\ResponseFormat;
use Exception;
use App\Helpers\Helper;
use App\Models\School;
use App\Models\Exam;
use App\Models\GradeClassMapping;
use App\Models\GradeSchoolMappings;
use App\Models\User;
use App\Models\MyStudyReport;
use App\Models\AttemptExams;
use App\Models\TeachersClassSubjectAssign;
use App\Models\PreConfigurationDiffiltyLevel;
use App\Models\Grades;
use App\Models\StrandUnitsObjectivesMappings;
use App\Models\Strands;
use App\Models\LearningsUnits;
use App\Models\LearningsObjectives;
use Log;
use Auth;

class MyStudyController extends Controller
{
    use Common;

    protected $AlpAiGraphController, $Exam, $GradeSchoolMappings, $GradeClassMapping, $User, $AttemptExams, $MyStudyReport;
    public function __construct(){
        $this->AlpAiGraphController = new AlpAiGraphController();
        $this->Exam = new Exam;
        $this->GradeSchoolMappings = new GradeSchoolMappings;
        $this->GradeClassMapping = new GradeClassMapping;
        $this->User = new User;
        $this->AttemptExams = new AttemptExams;
        $this->MyStudyReport = new MyStudyReport;
    }

    public function updateStudyReports(Request $request){
        ini_set('max_execution_time', -1);
        $ExamList = $this->Exam->whereIn('status',['publish','active','inactive','complete'])->orderBy(cn::EXAM_TABLE_ID_COLS,'DESC')->get();
        if($ExamList->isNotEmpty()){
            foreach($ExamList as $ExamKey => $ExamData){
                if($ExamData->{cn::EXAM_TABLE_USE_OF_MODE_COLS} == 1 || empty($ExamData->{cn::EXAM_TABLE_USE_OF_MODE_COLS}) || ($ExamData->{cn::EXAM_TABLE_USE_OF_MODE_COLS} === 2 && !empty($ExamData->{cn::EXAM_TABLE_PARENT_EXAM_ID_COLS}))){
                    $SchoolIds = ($ExamData->{cn::EXAM_TABLE_SCHOOL_COLS} && !empty($ExamData->{cn::EXAM_TABLE_SCHOOL_COLS})) ? explode(',',$ExamData->{cn::EXAM_TABLE_SCHOOL_COLS}) : [];
                    if(isset($SchoolIds) && !empty($SchoolIds)){
                        foreach($SchoolIds as $SchoolId){
                            $SchoolGrades = $this->GradeSchoolMappings->with('grades')->where(cn::GRADES_MAPPING_SCHOOL_ID_COL,$SchoolId)->get();
                            if($SchoolGrades->isNotEmpty()){
                                foreach($SchoolGrades as $Grade){
                                    $SchoolClass = $this->GradeClassMapping->where([cn::GRADE_CLASS_MAPPING_SCHOOL_ID_COL => $SchoolId,cn::GRADE_CLASS_MAPPING_GRADE_ID_COL => $Grade->grades->id])->get();
                                    if($SchoolClass->isNotEmpty()){
                                        foreach($SchoolClass as $ClassKey => $Class){
                                            $StudentList = $this->User->where(cn::USERS_SCHOOL_ID_COL,$SchoolId)->where(cn::USERS_ROLE_ID_COL,cn::STUDENT_ROLE_ID)->where(cn::USERS_GRADE_ID_COL,$Grade->grades->id)->where(cn::USERS_CLASS_ID_COL,$Class->id)->get();
                                            $StudentIds = $StudentList->pluck(cn::USERS_ID_COL);
                                            if($StudentList->isNotEmpty()){
                                                $CurrentClassStudent = array_intersect($StudentIds->toArray(),explode(',',$ExamData->{cn::EXAM_TABLE_STUDENT_IDS_COL}));
                                                $ClassStudentComaSeparated = implode(',',$CurrentClassStudent);
                                                $NoOfStudentAssignedExam = count($CurrentClassStudent) ?? 0;
                                                if($NoOfStudentAssignedExam){
                                                    foreach ($StudentIds as $studentId) {
                                                        $MyStudy = array();

                                                        $AttemptedStudentExam = $this->AttemptExams->where(cn::ATTEMPT_EXAMS_EXAM_ID,$ExamData->{cn::EXAM_TABLE_ID_COLS})->where(cn::ATTEMPT_EXAMS_STUDENT_STUDENT_ID,$studentId)->first();
                                                        // Find the study type
                                                        $StudyType = '';
                                                        if($ExamData->{cn::EXAM_TYPE_COLS} == 2){
                                                            $StudyType = 1; // 1 = Exercise
                                                        }elseif($ExamData->{cn::EXAM_TYPE_COLS} == 3){
                                                            $StudyType = 2; // 2 = Test
                                                        }else{
                                                            if($ExamData->{cn::EXAM_TABLE_SELF_LEARNING_TEST_TYPE_COL} == 1){
                                                                $StudyType = 1; // 1 = Exercise
                                                            }else{
                                                                $StudyType = 2; // 2 = Test
                                                            }
                                                        }
                                                        // Find the report type
                                                        $MyStudy['report_type'] = ($ExamData->{cn::EXAM_TYPE_COLS} == 2 || $ExamData->{cn::EXAM_TYPE_COLS} == 3) ? 'assignment_test' : 'self_learning';

                                                        $MyStudy['study_type'] = $StudyType;

                                                        // Store school id
                                                        $MyStudy['school_id'] = $SchoolId;

                                                        // Store Exam id
                                                        $MyStudy['exam_id'] = $ExamData->{cn::EXAM_TABLE_ID_COLS};

                                                        // Store grade id
                                                        $MyStudy['grade_id'] = $Grade->grades->id;

                                                        // Store Class Id
                                                        $MyStudy['class_id'] = $Class->{cn::GRADE_CLASS_MAPPING_ID_COL};
                                                        // Store Student Accuracy
                                                        $MyStudy['accuracy'] = '';

                                                        // Store Student Study Status
                                                        $MyStudy['study_status'] = '';

                                                        // Store Student Questions Difficulties
                                                        $MyStudy['questions_difficulties'] = '';

                                                        // Store Date time field
                                                        $MyStudy['date_time'] = date('Y-m-d H:i:s',strtotime($ExamData->{cn::CREATED_AT_COL}));
                                                        if(!empty($AttemptedStudentExam)){
                                                            $total_correct_answers = $AttemptedStudentExam->{cn::ATTEMPT_EXAMS_TOTAL_CORRECT_ANSWERS};
                                                            $question_id_size = $ExamData->{cn::EXAM_TABLE_QUESTION_IDS_COL};
                                                            if($question_id_size != ""){
                                                                $question_id_size = sizeof(explode(',',$question_id_size));
                                                            }
                                                            
                                                            $accuracy = \App\Helpers\Helper::getAccuracy($ExamData->{cn::EXAM_TABLE_ID_COLS}, $studentId);

                                                            $ability = $AttemptedStudentExam->{cn::ATTEMPT_EXAMS_STUDENT_ABILITY_COL} ?? 0;

                                                            $accuracy_type  = \App\Helpers\Helper::getAbilityType($ability);

                                                            $abilityPr = \App\Helpers\Helper::getNormalizedAbility($ability);
                                                            $accuracyData=array('accuracy'=>$accuracy,'total_correct_answers'=>$total_correct_answers,'question_id_size'=>$question_id_size);
                                                            $studyStatus=array('accuracy_type'=>$accuracy_type,'ability'=>$ability,'abilityPr'=>$abilityPr);
                                                            // Store Student Accuracy
                                                            $MyStudy['average_accuracy'] = json_encode($accuracyData);

                                                            // Store Student Study Status
                                                            $MyStudy['study_status'] = json_encode($studyStatus);

                                                            $progressQuestions = \App\Helpers\Helper::getQuestionDifficultiesLevelPercent($ExamData->id,$studentId);

                                                            // Store Student Questions Difficulties
                                                            $MyStudy['questions_difficulties'] = json_encode($progressQuestions);
                                                        }
                                                        if(isset($MyStudy) && !empty($MyStudy)){
                                                            $ExistingRecord = $this->MyStudyReport->where([cn::STUDY_REPORT_REPORT_TYPE_COL => $MyStudy['report_type'],
                                                            cn::STUDY_REPORT_STUDY_TYPE_COL => $MyStudy['study_type'],
                                                            cn::STUDY_REPORT_SCHOOL_ID_COL => $MyStudy['school_id'],
                                                            cn::STUDY_REPORT_EXAM_ID_COL => $MyStudy['exam_id'],
                                                            cn::STUDY_REPORT_GRADE_ID_COL => $MyStudy['grade_id'],
                                                            cn::STUDY_REPORT_CLASS_ID_COL => $MyStudy['class_id']                     
                                                            ])->first();
                                                            if(!empty($ExistingRecord)){
                                                                $this->MyStudyReport->find($ExistingRecord->{cn::STUDY_REPORT_ID_COL})->update($MyStudy);
                                                            }else{
                                                                $this->MyStudyReport->Create($MyStudy);
                                                            }
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
        echo 'My Study Report Cron Job Run Successfully.';
    }
}
