<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Events\UpdateMyTeachingReportEvent;
use App\Constants\DbConstant As cn;
use App\Http\Controllers\Reports\AlpAiGraphController;
use App\Models\Exam;
use App\Models\GradeClassMapping;
use App\Models\GradeSchoolMappings;
use App\Models\User;
use App\Models\MyTeachingReport;
use App\Models\AttemptExams;
use App\Models\TeachersClassSubjectAssign;
use App\Models\PreConfigurationDiffiltyLevel;
use App\Models\Grades;
use App\Models\StrandUnitsObjectivesMappings;
use App\Models\Strands;
use App\Models\LearningsUnits;
use App\Models\LearningsObjectives;
use App\Models\PeerGroup;
use App\Models\PeerGroupMember;
use Log;
use App\Helpers\Helper;
use App\Models\ExamSchoolMapping;

class UpdateMyTeachingReportListener
{
    protected $AlpAiGraphController, $Exam, $GradeSchoolMappings,
              $GradeClassMapping, $User, $AttemptExams, $MyTeachingReport, $PeerGroup,
              $ExamSchoolMapping;

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        $this->AlpAiGraphController = new AlpAiGraphController();
        $this->Exam = new Exam;
        $this->GradeSchoolMappings = new GradeSchoolMappings;
        $this->GradeClassMapping = new GradeClassMapping;
        $this->User = new User;
        $this->AttemptExams = new AttemptExams;
        $this->MyTeachingReport = new MyTeachingReport;
        $this->PeerGroup = new PeerGroup;
        $this->ExamSchoolMapping = new ExamSchoolMapping;
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle()
    {
        Log::info('Event Start UpdateMyTeachingReports');
        ini_set('max_execution_time', -1);
        // $ExamList = $this->Exam->whereIn('status',['publish','active','inactive','complete'])                        
        //                 ->where(cn::EXAM_TABLE_IS_TEACHING_REPORT_SYNC,'true')
        //                 ->orderBy(cn::EXAM_TABLE_ID_COLS,'DESC')
        //                 ->get();

        $ExamIds = $this->ExamSchoolMapping->whereIn('status',['draft','publish'])->pluck('exam_id');
        if(!empty($ExamIds)){
            $ExamIds = $ExamIds->toArray();
        }
        $ExamList = $this->Exam->whereIn('id',$ExamIds)
                        ->orderBy(cn::EXAM_TABLE_ID_COLS,'DESC')
                        ->get();

        if($ExamList->isNotEmpty()){
            foreach($ExamList as $ExamKey => $ExamData){
                if($ExamData->use_of_mode == 1 || empty($ExamData->use_of_mode) || ($ExamData->use_of_mode === 2 && !empty($ExamData->parent_exam_id))){
                    $SchoolIds = ($ExamData->school_id && !empty($ExamData->school_id)) ? explode(',',$ExamData->school_id) : [];
                    if(isset($SchoolIds) && !empty($SchoolIds)){
                        foreach($SchoolIds as $SchoolId){
                            $SchoolGrades = $this->GradeSchoolMappings->with('grades')->where(cn::GRADES_MAPPING_SCHOOL_ID_COL,$SchoolId)->get();
                            if($SchoolGrades->isNotEmpty()){
                                foreach($SchoolGrades as $Grade){
                                    $SchoolClass = $this->GradeClassMapping->where([cn::GRADE_CLASS_MAPPING_SCHOOL_ID_COL => $SchoolId,cn::GRADE_CLASS_MAPPING_GRADE_ID_COL => $Grade->grades->id])->get();
                                    if($SchoolClass->isNotEmpty()){
                                        foreach($SchoolClass as $ClassKey => $Class){
                                            $StudentList = $this->User->where(cn::USERS_SCHOOL_ID_COL,$SchoolId)->where(cn::USERS_ROLE_ID_COL,cn::STUDENT_ROLE_ID)->where(cn::USERS_GRADE_ID_COL,$Grade->grades->id)->where(cn::USERS_CLASS_COL,$Class->id)->get();
                                            $StudentIds = $StudentList->pluck(cn::USERS_ID_COL);
                                            if($StudentList->isNotEmpty()){
                                                $CurrentClassStudent = array_intersect($StudentIds->toArray(),explode(',',$ExamData->student_ids));
                                                $ClassStudentComaSeparated = implode(',',$CurrentClassStudent);
                                                $NoOfStudentAssignedExam = count($CurrentClassStudent) ?? 0;
                                                if($NoOfStudentAssignedExam){
                                                    $MyTeaching = array();

                                                    $AttemptedStudentExam = $this->AttemptExams->where(cn::ATTEMPT_EXAMS_EXAM_ID,$ExamData->id)->whereIn(cn::ATTEMPT_EXAMS_STUDENT_STUDENT_ID,$StudentIds)->get();
                                                    $ClassStudentProgress = [
                                                        'progress_percentage' => 0,
                                                        'progress_tooltip' => '0%'.' '.'(0/'.sizeof($CurrentClassStudent).')'
                                                    ];
                                                    $ClassStudentAverageAccuracy = [
                                                        'average_accuracy' => 0,
                                                        'average_accuracy_tooltip' => '0% (0/0)'
                                                    ];

                                                    // Find the report type
                                                    $MyTeaching['report_type'] = ($ExamData->exam_type == 2 || $ExamData->exam_type == 3) ? 'assignment_test' : 'self_learning';

                                                    // Find the study type
                                                    $StudyType = '';
                                                    if($ExamData->exam_type == 2){
                                                        $StudyType = 1; // 1 = Exercise
                                                    }elseif($ExamData->exam_type == 3){
                                                        $StudyType = 2; // 2 = Test
                                                    }else{
                                                        if($ExamData->self_learning_test_type == 1){
                                                            $StudyType = 1; // 1 = Exercise
                                                        }else{
                                                            $StudyType = 2; // 2 = Test
                                                        }
                                                    }
                                                    $MyTeaching['study_type'] = $StudyType;

                                                    // Store school id
                                                    $MyTeaching['school_id'] = $SchoolId;

                                                    // Store Exam id
                                                    $MyTeaching['exam_id'] = $ExamData->id;

                                                    // Store grade id
                                                    $MyTeaching['grade_id'] = $Grade->grades->id;

                                                    // Store Class Id
                                                    $MyTeaching['class_id'] = $Class->id;

                                                    // Store grade name with class name
                                                    $MyTeaching['grade_with_class'] = $Grade->grades->name.'-'.$Class->name;

                                                    // Store count how many student in the assigned this exams
                                                    $MyTeaching['no_of_students'] =  $NoOfStudentAssignedExam;

                                                    // Store current class student ids
                                                    $MyTeaching['student_ids'] =  $ClassStudentComaSeparated ?? null;

                                                    if($AttemptedStudentExam->isNotEmpty()){ // check if student attempt exams
                                                        // Store class student progress data
                                                        if(isset($ClassStudentComaSeparated) && !empty($ClassStudentComaSeparated)){
                                                            $attempt_exams_size = sizeof($AttemptedStudentExam);
                                                            $attempt_exams_pr = round(($attempt_exams_size/sizeof($CurrentClassStudent))*100);
                                                            if($attempt_exams_pr > 100){
                                                                $attempt_exams_pr = 100;	
                                                            }
                                                            $ClassStudentProgress = ['progress_percentage' => $attempt_exams_pr,
                                                                                    'progress_tooltip' => $attempt_exams_pr.'%'.' '.'('.$attempt_exams_size.'/'.sizeof($CurrentClassStudent).')'];
                                                        }

                                                        // Find class students accuracy
                                                        $AverageAccuracy = Helper::getAccuracyAllStudent($ExamData->id, $ClassStudentComaSeparated);
                                                        $QuestionAnsweredCorrectly = Helper::getAverageNoOfQuestionAnsweredCorrectly($ExamData->id,$ClassStudentComaSeparated);
                                                        $ClassStudentAverageAccuracy = [
                                                            'average_accuracy' => $AverageAccuracy,
                                                            'average_accuracy_tooltip' => $AverageAccuracy.'% '.$QuestionAnsweredCorrectly
                                                        ];
                                                    }

                                                    // Store Class student progress
                                                    $MyTeaching['student_progress'] = json_encode($ClassStudentProgress);

                                                    // Store class student average of accuracy
                                                    $MyTeaching['average_accuracy'] = json_encode($ClassStudentAverageAccuracy);

                                                    // Find Class Student Study status data
                                                    $ClassStudentStudyStatus = $this->AlpAiGraphController->getProgressDetailList($ExamData->id,$ClassStudentComaSeparated);
                                                        
                                                    // Store Class Student Study status
                                                    $MyTeaching['study_status'] = json_encode($ClassStudentStudyStatus);

                                                    // Find Question difficulties
                                                    $QuestionDifficulties = Helper::getQuestionDifficultiesLevelPercent($ExamData->id,$ClassStudentComaSeparated);
                                                    // Store Class Student Question difficulty
                                                    $MyTeaching['questions_difficulties'] = json_encode($QuestionDifficulties);

                                                    // Store Date time field
                                                    //$MyTeaching['date_time'] = date('d/m/Y H:i:s',strtotime($ExamData->created_at));
                                                    $MyTeaching['date_time'] = date('Y-m-d H:i:s',strtotime($ExamData->created_at));

                                                    if(isset($MyTeaching) && !empty($MyTeaching)){
                                                        $ExistingRecord = $this->MyTeachingReport->where([cn::TEACHING_REPORT_REPORT_TYPE_COL => $MyTeaching['report_type'],
                                                        cn::TEACHING_REPORT_STUDY_TYPE_COL => $MyTeaching['study_type'],
                                                        cn::TEACHING_REPORT_SCHOOL_ID_COL => $MyTeaching['school_id'],
                                                        cn::TEACHING_REPORT_EXAM_ID_COL => $MyTeaching['exam_id'],
                                                        cn::TEACHING_REPORT_GRADE_ID_COL => $MyTeaching['grade_id'],
                                                        cn::TEACHING_REPORT_CLASS_ID_COL => $MyTeaching['class_id'],
                                                        cn::TEACHING_REPORT_GRADE_WITH_CLASS_COL => $MyTeaching['grade_with_class'],                                               
                                                        ])->first();
                                                        if(!empty($ExistingRecord)){
                                                            $this->MyTeachingReport->find($ExistingRecord->id)->update($MyTeaching);
                                                        }else{
                                                            $this->MyTeachingReport->Create($MyTeaching);
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                            }

                            if(isset($ExamData->peer_group_ids) && !empty($ExamData->peer_group_ids)){
                                $PeerGroupIds = explode(',',$ExamData->peer_group_ids);
                                foreach($PeerGroupIds as $PeerGroupKey => $PeerGroupId){
                                    $MyTeaching = array();
                                    $PeerGroupData = $this->PeerGroup->with('Members')->where(cn::PEER_GROUP_ID_COL,$PeerGroupId)->where(cn::PEER_GROUP_SCHOOL_ID_COL,$SchoolId)->first();
                                    if(!empty($PeerGroupData)){
                                        $PeerGroupMemberIds = $PeerGroupData->Members->pluck(cn::PEER_GROUP_MEMBERS_MEMBER_ID_COL)->toArray();
                                        $CurrentClassStudent = array_intersect($PeerGroupMemberIds,explode(',',$ExamData->student_ids));
                                        $ClassStudentComaSeparated = implode(',',$CurrentClassStudent);
                                        $NoOfStudentAssignedExam = count($CurrentClassStudent) ?? 0;
                                        if($NoOfStudentAssignedExam){
                                            $AttemptedStudentExam = $this->AttemptExams->where(cn::ATTEMPT_EXAMS_EXAM_ID,$ExamData->id)->whereIn(cn::ATTEMPT_EXAMS_STUDENT_STUDENT_ID,$PeerGroupMemberIds)->get();
                                            $ClassStudentProgress = [
                                                'progress_percentage' => 0,
                                                'progress_tooltip' => '0%'.' '.'(0/'.sizeof($CurrentClassStudent).')'
                                            ];
                                            $ClassStudentAverageAccuracy = [
                                                'average_accuracy' => 0,
                                                'average_accuracy_tooltip' => '0% (0/0)'
                                            ];

                                            // Find the report type
                                            $MyTeaching['report_type'] = ($ExamData->exam_type == 2 || $ExamData->exam_type == 3) ? 'assignment_test' : 'self_learning';

                                            // Find the study type
                                            $StudyType = '';
                                            if($ExamData->exam_type == 2){
                                                $StudyType = 1; // 1 = Exercise
                                            }elseif($ExamData->exam_type == 3){
                                                $StudyType = 2; // 2 = Test
                                            }else{
                                                if($ExamData->self_learning_test_type == 1){
                                                    $StudyType = 1; // 1 = Exercise
                                                }else{
                                                    $StudyType = 2; // 2 = Test
                                                }
                                            }
                                            $MyTeaching['study_type'] = $StudyType;

                                            // Store school id
                                            $MyTeaching['school_id'] = $SchoolId;

                                            // Store Exam id
                                            $MyTeaching['exam_id'] = $ExamData->id;

                                            // Store grade id
                                            $MyTeaching['peer_group_id'] = $PeerGroupId;

                                            // Store count how many student in the assigned this exams
                                            $MyTeaching['no_of_students'] =  $NoOfStudentAssignedExam;

                                            // Store current class student ids
                                            $MyTeaching['student_ids'] =  $ClassStudentComaSeparated ?? null;

                                            if($AttemptedStudentExam->isNotEmpty()){ // check if student attempt exams
                                                // Store class student progress data
                                                if(isset($ClassStudentComaSeparated) && !empty($ClassStudentComaSeparated)){
                                                    $attempt_exams_size = sizeof($AttemptedStudentExam);
                                                    $attempt_exams_pr = round(($attempt_exams_size/sizeof($CurrentClassStudent))*100);
                                                    if($attempt_exams_pr > 100){
                                                        $attempt_exams_pr = 100;    
                                                    }
                                                    $ClassStudentProgress = ['progress_percentage' => $attempt_exams_pr,
                                                                            'progress_tooltip' => $attempt_exams_pr.'%'.' '.'('.$attempt_exams_size.'/'.sizeof($CurrentClassStudent).')'];
                                                }

                                                // Find class students accuracy
                                                $AverageAccuracy = Helper::getAccuracyAllStudent($ExamData->id, $ClassStudentComaSeparated);
                                                $QuestionAnsweredCorrectly = Helper::getAverageNoOfQuestionAnsweredCorrectly($ExamData->id,$ClassStudentComaSeparated);
                                                $ClassStudentAverageAccuracy = [
                                                    'average_accuracy' => $AverageAccuracy,
                                                    'average_accuracy_tooltip' => $AverageAccuracy.'% '.$QuestionAnsweredCorrectly
                                                ];
                                            }

                                            // Store Class student progress
                                            $MyTeaching['student_progress'] = json_encode($ClassStudentProgress);

                                            // Store class student average of accuracy
                                            $MyTeaching['average_accuracy'] = json_encode($ClassStudentAverageAccuracy);

                                            // Find Class Student Study status data
                                            $ClassStudentStudyStatus = $this->AlpAiGraphController->getProgressDetailList($ExamData->id,$ClassStudentComaSeparated);

                                            // Store Class Student Study status
                                            $MyTeaching['study_status'] = json_encode($ClassStudentStudyStatus);

                                            // Find Question difficulties
                                            $QuestionDifficulties = Helper::getQuestionDifficultiesLevelPercent($ExamData->id,$ClassStudentComaSeparated);

                                            // Store Class Student Question difficulty
                                            $MyTeaching['questions_difficulties'] = json_encode($QuestionDifficulties);

                                            // Store Date time field
                                            $MyTeaching['date_time'] = date('Y-m-d H:i:s',strtotime($ExamData->created_at));

                                            if(isset($MyTeaching) && !empty($MyTeaching)){
                                                $ExistingRecord = MyTeachingReport::where([cn::TEACHING_REPORT_REPORT_TYPE_COL => $MyTeaching['report_type'],
                                                cn::TEACHING_REPORT_STUDY_TYPE_COL => $MyTeaching['study_type'],
                                                cn::TEACHING_REPORT_SCHOOL_ID_COL => $MyTeaching['school_id'],
                                                cn::TEACHING_REPORT_EXAM_ID_COL => $MyTeaching['exam_id'],
                                                cn::TEACHING_REPORT_PEER_GROUP_ID => $MyTeaching['peer_group_id'],
                                                ])->first();
                                                if(!empty($ExistingRecord)){
                                                    $this->MyTeachingReport->find($ExistingRecord->id)->update($MyTeaching);
                                                }else{
                                                    $this->MyTeachingReport->Create($MyTeaching);
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }

                // Status update for
                $this->Exam->find($ExamData->id)->update([cn::EXAM_TABLE_IS_TEACHING_REPORT_SYNC =>'false']);
            }
        }
        Log::info('Event Stop UpdateMyTeachingReports');
        echo 'Update MyTeaching Reports Cron Job Run Successfully';
    }
}
