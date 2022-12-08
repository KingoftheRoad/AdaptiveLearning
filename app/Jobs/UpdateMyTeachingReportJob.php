<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Constants\DbConstant As cn;
use App\Http\Controllers\Reports\AlpAiGraphController;
use App\Models\Exam;
use App\Models\GradeClassMapping;
use App\Models\GradeSchoolMappings;
use App\Models\User;
use App\Models\MyTeachingReport;
use App\Models\AttemptExams;
use App\Models\PeerGroup;
use Log;
use App\Helpers\Helper;
use App\Models\ExamSchoolMapping;
use App\Models\ExamGradeClassMappingModel;
use App\Traits\Common;

class UpdateMyTeachingReportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, Common;

    // protected $AlpAiGraphController, $Exam, $GradeSchoolMappings,
    //           $GradeClassMapping, $User, $AttemptExams, $MyTeachingReport, $PeerGroup,
    //           $ExamSchoolMapping;

    /**
     * Create a new job instance.
     *
     * @return void
     */

    public function __construct()
    {
        
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        //Log::info('Job Start UpdateMyTeachingReports');
        $this->AlpAiGraphController = new AlpAiGraphController();
        ini_set('max_execution_time', -1);
        $ExamIds = [];
        $ExamIds =  ExamSchoolMapping::where(cn::EXAM_SCHOOL_MAPPING_CURRICULUM_YEAR_ID_COL,Helper::getGlobalConfiguration('current_curriculum_year'))
                    ->whereIn(cn::EXAM_SCHOOL_MAPPING_STATUS_COL,['draft','publish'])
                    ->pluck(cn::EXAM_SCHOOL_MAPPING_EXAM_ID_COL);
        if(!empty($ExamIds)){
            $ExamIds = $ExamIds->toArray();
        }
        // Find Exam list
        $ExamList = Exam::whereIn(cn::EXAM_TABLE_ID_COLS,$ExamIds)
                    ->orWhere(cn::EXAM_TABLE_CREATED_BY_USER_COL,'student')
                    ->orderBy(cn::EXAM_TABLE_ID_COLS,'DESC')
                    ->get();
        if($ExamList->isNotEmpty()){
            Log::info('Job Start UpdateMyTeachingReports');
            foreach($ExamList as $ExamKey => $ExamData){
                if($ExamData->{cn::EXAM_TABLE_USE_OF_MODE_COLS} == 1 || empty($ExamData->{cn::EXAM_TABLE_USE_OF_MODE_COLS}) || ($ExamData->{cn::EXAM_TABLE_USE_OF_MODE_COLS} === 2 && !empty($ExamData->{cn::EXAM_TABLE_PARENT_EXAM_ID_COLS}))){
                    $SchoolIds = ($ExamData->{cn::EXAM_TABLE_SCHOOL_COLS} && !empty($ExamData->{cn::EXAM_TABLE_SCHOOL_COLS})) ? explode(',',$ExamData->{cn::EXAM_TABLE_SCHOOL_COLS}) : [];
                    if(isset($SchoolIds) && !empty($SchoolIds)){
                        foreach($SchoolIds as $SchoolId){
                            $SchoolGrades = GradeSchoolMappings::with('grades')->where(cn::GRADES_MAPPING_SCHOOL_ID_COL,$SchoolId)->get();
                            if($SchoolGrades->isNotEmpty()){
                                foreach($SchoolGrades as $Grade){
                                    $SchoolClass = GradeClassMapping::where([cn::GRADE_CLASS_MAPPING_SCHOOL_ID_COL => $SchoolId,cn::GRADE_CLASS_MAPPING_GRADE_ID_COL => $Grade->grades->id])->get();
                                    if($SchoolClass->isNotEmpty()){
                                        foreach($SchoolClass as $ClassKey => $Class){
                                            // $StudentList = User::where(cn::USERS_SCHOOL_ID_COL,$SchoolId)->where(cn::USERS_ROLE_ID_COL,cn::STUDENT_ROLE_ID)->where(cn::USERS_GRADE_ID_COL,$Grade->grades->id)->where(cn::USERS_CLASS_ID_COL,$Class->id)->get();
                                            $StudentList = User::where(cn::USERS_ROLE_ID_COL,cn::STUDENT_ROLE_ID)
                                                            ->where(cn::USERS_ID_COL,$this->curriculum_year_mapping_student_ids($Grade->grades->id,$Class->id,$SchoolId, Helper::getGlobalConfiguration('current_curriculum_year')))
                                                            ->get();
                                            $StudentIds = $StudentList->pluck(cn::USERS_ID_COL);
                                            if($StudentList->isNotEmpty()){
                                                $GetCurrentGradeClassStudent = ExamGradeClassMappingModel::where([
                                                                                cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_CURRICULUM_YEAR_ID_COL => $ExamData->{cn::EXAM_CURRICULUM_YEAR_ID_COL},
                                                                                cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_EXAM_ID_COL => $ExamData->{cn::EXAM_TABLE_ID_COLS},
                                                                                cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_SCHOOL_ID_COL => $SchoolId,
                                                                                cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_GRADE_ID_COL => $Grade->grades->id,
                                                                                cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_CLASS_ID_COL => $Class->id
                                                                                ])
                                                                                ->whereNotNull([
                                                                                    cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_GRADE_ID_COL,
                                                                                    cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_CLASS_ID_COL
                                                                                ])
                                                                                ->pluck(cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_STUDENT_IDS_COL);
                                                $GradeClassStudents = [];
                                                if($GetCurrentGradeClassStudent->isNotEmpty()){
                                                    $GradeClassStudents = explode(',',$GetCurrentGradeClassStudent->toArray()[0]);
                                                }else{
                                                    $GetCurrentGradeClassStudent = ExamGradeClassMappingModel::where([
                                                                                        cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_CURRICULUM_YEAR_ID_COL => $ExamData->{cn::EXAM_CURRICULUM_YEAR_ID_COL},
                                                                                        cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_EXAM_ID_COL => $ExamData->{cn::EXAM_TABLE_ID_COLS},
                                                                                        cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_SCHOOL_ID_COL => $SchoolId
                                                                                    ])
                                                                                    ->whereNotNull(cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_PEER_GROUP_ID_COL)
                                                                                    ->pluck(cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_STUDENT_IDS_COL);
                                                    if($GetCurrentGradeClassStudent->isNotEmpty()){
                                                        $GroupStudentIds = explode(',',implode(',',$GetCurrentGradeClassStudent->toArray()));
                                                        // $AssignedStudentIds = User::where([
                                                        //                             cn::USERS_SCHOOL_ID_COL => $SchoolId,
                                                        //                             cn::USERS_GRADE_ID_COL => $Grade->grades->id,
                                                        //                             cn::USERS_CLASS_ID_COL => $Class->id,
                                                        //                             cn::USERS_ROLE_ID_COL => cn::STUDENT_ROLE_ID
                                                        //                         ])
                                                        //                         ->whereIn(cn::USERS_ID_COL,$GroupStudentIds)
                                                        //                         ->pluck(cn::USERS_ID_COL);
                                                        $AssignedStudentIds = User::where(cn::USERS_ROLE_ID_COL,cn::STUDENT_ROLE_ID)
                                                                                ->where(cn::USERS_ID_COL,$this->curriculum_year_mapping_student_ids($Grade->grades->id,$Class->id,$SchoolId, Helper::getGlobalConfiguration('current_curriculum_year')))
                                                                                ->whereIn(cn::USERS_ID_COL,$GroupStudentIds)
                                                                                ->pluck(cn::USERS_ID_COL);
                                                        $GradeClassStudents = $AssignedStudentIds->toArray();
                                                    }
                                                }

                                                if($ExamData->{cn::EXAM_TABLE_CREATED_BY_USER_COL} == 'student'){
                                                    $GradeClassStudents = explode(',',$ExamData->{cn::EXAM_TABLE_STUDENT_IDS_COL});
                                                }

                                                $CurrentClassStudent = array_intersect($StudentIds->toArray(),$GradeClassStudents);
                                                $ClassStudentComaSeparated = implode(',',$CurrentClassStudent);
                                                $NoOfStudentAssignedExam = count($CurrentClassStudent) ?? 0;
                                                if($NoOfStudentAssignedExam){
                                                    $MyTeaching = array();
                                                    $AttemptedStudentExam = AttemptExams::where(cn::ATTEMPT_EXAMS_EXAM_ID,$ExamData->id)->whereIn(cn::ATTEMPT_EXAMS_STUDENT_STUDENT_ID,$StudentIds)->get();
                                                    $ClassStudentProgress = [
                                                        'progress_percentage' => 0,
                                                        'progress_tooltip' => '0%'.' '.'(0/'.sizeof($CurrentClassStudent).')'
                                                    ];
                                                    $ClassStudentAverageAccuracy = [
                                                        'average_accuracy' => 0,
                                                        'average_accuracy_tooltip' => '0% (0/0)'
                                                    ];

                                                    // Find the report type
                                                    $MyTeaching['report_type'] = ($ExamData->{cn::EXAM_TYPE_COLS} == 2 || $ExamData->{cn::EXAM_TYPE_COLS} == 3) ? 'assignment_test' : 'self_learning';

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
                                                    $MyTeaching['study_type'] = $StudyType;

                                                    // Store school id
                                                    $MyTeaching['school_id'] = $SchoolId;

                                                    // Store Exam id
                                                    $MyTeaching['exam_id'] = $ExamData->{cn::EXAM_TABLE_ID_COLS};

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
                                                        $AverageAccuracy = Helper::getAccuracyAllStudent($ExamData->{cn::EXAM_TABLE_ID_COLS}, $ClassStudentComaSeparated);
                                                        $QuestionAnsweredCorrectly = Helper::getAverageNoOfQuestionAnsweredCorrectly($ExamData->{cn::EXAM_TABLE_ID_COLS},$ClassStudentComaSeparated);
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
                                                    $ClassStudentStudyStatus = $this->AlpAiGraphController->getProgressDetailList($ExamData->{cn::EXAM_TABLE_ID_COLS},$ClassStudentComaSeparated);
                                                        
                                                    // Store Class Student Study status
                                                    $MyTeaching['study_status'] = json_encode($ClassStudentStudyStatus);

                                                    // Find Question difficulties
                                                    $QuestionDifficulties = Helper::getQuestionDifficultiesLevelPercent($ExamData->{cn::EXAM_TABLE_ID_COLS},$ClassStudentComaSeparated);
                                                    // Store Class Student Question difficulty
                                                    $MyTeaching['questions_difficulties'] = json_encode($QuestionDifficulties);

                                                    // Store Date time field
                                                    $MyTeaching['date_time'] = date('Y-m-d H:i:s',strtotime($ExamData->{cn::EXAM_TABLE_CREATED_AT}));

                                                    if(isset($MyTeaching) && !empty($MyTeaching)){
                                                        $ExistingRecord =   MyTeachingReport::where([cn::TEACHING_REPORT_REPORT_TYPE_COL => $MyTeaching['report_type'],
                                                                                cn::TEACHING_REPORT_CURRICULUM_YEAR_ID_COL => $ExamData->{cn::EXAM_CURRICULUM_YEAR_ID_COL},
                                                                                cn::TEACHING_REPORT_STUDY_TYPE_COL => $MyTeaching['study_type'],
                                                                                cn::TEACHING_REPORT_SCHOOL_ID_COL => $MyTeaching['school_id'],
                                                                                cn::TEACHING_REPORT_EXAM_ID_COL => $MyTeaching['exam_id'],
                                                                                cn::TEACHING_REPORT_GRADE_ID_COL => $MyTeaching['grade_id'],
                                                                                cn::TEACHING_REPORT_CLASS_ID_COL => $MyTeaching['class_id'],
                                                                                cn::TEACHING_REPORT_GRADE_WITH_CLASS_COL => $MyTeaching['grade_with_class'],                                               
                                                                            ])->first();
                                                        if(!empty($ExistingRecord)){
                                                            MyTeachingReport::find($ExistingRecord->{cn::TEACHING_REPORT_ID_COL})->update($MyTeaching);
                                                        }else{
                                                            $MyTeaching[cn::TEACHING_REPORT_CURRICULUM_YEAR_ID_COL]  = $ExamData->{cn::EXAM_CURRICULUM_YEAR_ID_COL};
                                                            MyTeachingReport::Create($MyTeaching);
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                            }


                            if(isset($ExamData->{cn::EXAM_TABLE_PEER_GROUP_IDS_COL}) && !empty($ExamData->{cn::EXAM_TABLE_PEER_GROUP_IDS_COL})){
                                $PeerGroupIds = ExamGradeClassMappingModel::where([
                                                        cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_CURRICULUM_YEAR_ID_COL => $ExamData->{cn::EXAM_CURRICULUM_YEAR_ID_COL},
                                                        cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_EXAM_ID_COL => $ExamData->{cn::EXAM_TABLE_ID_COLS},
                                                        cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_SCHOOL_ID_COL => $SchoolId
                                                    ])
                                                    ->whereNotNull(cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_PEER_GROUP_ID_COL)
                                                    ->pluck(cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_PEER_GROUP_ID_COL);
                                if(!empty($PeerGroupIds)){
                                    $PeerGroupIds = $PeerGroupIds->toArray();
                                    foreach($PeerGroupIds as $PeerGroupKey => $PeerGroupId){
                                        $MyTeaching = array();
                                        $PeerGroupData = PeerGroup::with('Members')->where(cn::PEER_GROUP_ID_COL,$PeerGroupId)->where(cn::PEER_GROUP_SCHOOL_ID_COL,$SchoolId)->first();
                                        if(!empty($PeerGroupData)){
                                            $PeerGroupMemberIds = $PeerGroupData->Members->pluck(cn::PEER_GROUP_MEMBERS_MEMBER_ID_COL)->toArray();
                                            $CurrentClassStudent = array_intersect($PeerGroupMemberIds,explode(',',$ExamData->{cn::EXAM_TABLE_STUDENT_IDS_COL}));
                                            $ClassStudentComaSeparated = implode(',',$CurrentClassStudent);
                                            $NoOfStudentAssignedExam = count($CurrentClassStudent) ?? 0;
                                            if($NoOfStudentAssignedExam){
                                                $AttemptedStudentExam = AttemptExams::where(cn::ATTEMPT_EXAMS_EXAM_ID,$ExamData->{cn::EXAM_TABLE_ID_COLS})->whereIn(cn::ATTEMPT_EXAMS_STUDENT_STUDENT_ID,$PeerGroupMemberIds)->get();
                                                $ClassStudentProgress = [
                                                    'progress_percentage' => 0,
                                                    'progress_tooltip' => '0%'.' '.'(0/'.sizeof($CurrentClassStudent).')'
                                                ];
                                                $ClassStudentAverageAccuracy = [
                                                    'average_accuracy' => 0,
                                                    'average_accuracy_tooltip' => '0% (0/0)'
                                                ];

                                                // Find the report type
                                                $MyTeaching['report_type'] = ($ExamData->{cn::EXAM_TYPE_COLS} == 2 || $ExamData->{cn::EXAM_TYPE_COLS} == 3) ? 'assignment_test' : 'self_learning';

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
                                                $MyTeaching['study_type'] = $StudyType;

                                                // Store school id
                                                $MyTeaching['school_id'] = $SchoolId;

                                                // Store Exam id
                                                $MyTeaching['exam_id'] = $ExamData->{cn::EXAM_TABLE_ID_COLS};

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
                                                    $AverageAccuracy = Helper::getAccuracyAllStudent($ExamData->{cn::EXAM_TABLE_ID_COLS}, $ClassStudentComaSeparated);
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
                                                $ClassStudentStudyStatus = $this->AlpAiGraphController->getProgressDetailList($ExamData->{cn::EXAM_TABLE_ID_COLS},$ClassStudentComaSeparated);

                                                // Store Class Student Study status
                                                $MyTeaching['study_status'] = json_encode($ClassStudentStudyStatus);

                                                // Find Question difficulties
                                                $QuestionDifficulties = Helper::getQuestionDifficultiesLevelPercent($ExamData->{cn::EXAM_TABLE_ID_COLS},$ClassStudentComaSeparated);

                                                // Store Class Student Question difficulty
                                                $MyTeaching['questions_difficulties'] = json_encode($QuestionDifficulties);

                                                // Store Date time field
                                                $MyTeaching['date_time'] = date('Y-m-d H:i:s',strtotime($ExamData->{cn::EXAM_TABLE_CREATED_AT}));

                                                if(isset($MyTeaching) && !empty($MyTeaching)){
                                                    $ExistingRecord = MyTeachingReport::where([cn::TEACHING_REPORT_REPORT_TYPE_COL => $MyTeaching['report_type'],
                                                    cn::TEACHING_REPORT_STUDY_TYPE_COL => $MyTeaching['study_type'],
                                                    cn::TEACHING_REPORT_SCHOOL_ID_COL => $MyTeaching['school_id'],
                                                    cn::TEACHING_REPORT_EXAM_ID_COL => $MyTeaching['exam_id'],
                                                    cn::TEACHING_REPORT_PEER_GROUP_ID => $MyTeaching['peer_group_id'],
                                                    ])->first();
                                                    if(!empty($ExistingRecord)){
                                                        MyTeachingReport::find($ExistingRecord->{cn::TEACHING_REPORT_ID_COL})->update($MyTeaching);
                                                    }else{
                                                        $MyTeaching[cn::TEACHING_REPORT_CURRICULUM_YEAR_ID_COL]  = $ExamData->{cn::EXAM_CURRICULUM_YEAR_ID_COL};
                                                        MyTeachingReport::Create($MyTeaching);
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

                // Status update for
                Exam::find($ExamData->id)->update([cn::EXAM_TABLE_IS_TEACHING_REPORT_SYNC =>'false']);
            }
            Log::info('Job Stop UpdateMyTeachingReports');
        }
    }
}
