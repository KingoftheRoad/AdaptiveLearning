<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Helpers\Helper;
use App\Models\School;
use App\Models\Exam;
use App\Jobs\UpdateMyTeachingReport;
use App\Models\GradeClassMapping;
use App\Models\GradeSchoolMappings;
use App\Models\User;
use App\Models\MyTeachingReport;
use App\Models\AttemptExams;
use App\Http\Controllers\Reports\AlpAiGraphController;
use Log;
class UpdateMyTeachingReport implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    protected $AlpAiGraphController;

    public function __construct()
    {
        $this->AlpAiGraphController = new AlpAiGraphController();
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Log::info('MyTeaching Report Update Job Start:');

        ini_set('max_execution_time', -1);

        //dispatch(new UpdateMyTeachingReport())->delay(now()->addSeconds(1));
        $ExamList = Exam::where('status','publish')->orderBy('id','DESC')->get();
        $MyTeaching = array();
        if($ExamList->isNotEmpty()){
            foreach($ExamList as $ExamKey => $ExamData){
                if($ExamData->use_of_mode == 1 || empty($ExamData->use_of_mode) || ($ExamData->use_of_mode === 2 && !empty($ExamData->parent_exam_id))){
                    $SchoolIds = ($ExamData->school_id && !empty($ExamData->school_id)) ? explode(',',$ExamData->school_id) : [];
                    if(isset($SchoolIds) && !empty($SchoolIds)){
                        foreach($SchoolIds as $SchoolId){                            
                            $SchoolGrades = GradeSchoolMappings::with('grades')->where('school_id',$SchoolId)->get();
                            if($SchoolGrades->isNotEmpty()){
                                foreach($SchoolGrades as $Grade){
                                    $SchoolClass = GradeClassMapping::where(['school_id' => $SchoolId,'grade_id' => $Grade->grades->id])->get();
                                    if($SchoolClass->isNotEmpty()){
                                        foreach($SchoolClass as $ClassKey => $Class){
                                            $StudentList = User::where('school_id',$SchoolId)->where('role_id',cn::STUDENT_ROLE_ID)->where('grade_id',$Grade->grades->id)->where('class_name',$Class->id)->get();
                                            $StudentIds = $StudentList->pluck('id');
                                            if($StudentList->isNotEmpty()){
                                                $CurrentClassStudent = array_intersect($StudentIds->toArray(),explode(',',$ExamData->student_ids));
                                                $ClassStudentComaSeparated = implode(',',$CurrentClassStudent);
                                                $NoOfStudentAssignedExam = count($CurrentClassStudent) ?? 0;
                                                if($NoOfStudentAssignedExam){
                                                    $AttemptedStudentExam = AttemptExams::where('exam_id',$ExamData->id)->whereIn('student_id',$StudentIds)->get();
                                                    $ClassStudentProgress = [
                                                        'progress_percentage' => 0,
                                                        'progress_tooltip' => '0%'.' '.'(0/'.sizeof($CurrentClassStudent).')'
                                                    ];
                                                    $ClassStudentAverageAccuracy = [
                                                        'average_accuracy' => 0,
                                                        'average_accuracy_tooltip' => '0% (0/0)'
                                                    ];

                                                    // Find the report type
                                                    $MyTeaching[$ExamData->id][$SchoolId][$Grade->grades->name.'-'.$Class->name]['report_type'] = ($ExamData->exam_type == 2 || $ExamData->exam_type == 3) ? 'assignment_test' : 'self_learning';

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
                                                    $MyTeaching[$ExamData->id][$SchoolId][$Grade->grades->name.'-'.$Class->name]['study_type'] = $StudyType;

                                                    // Store school id
                                                    $MyTeaching[$ExamData->id][$SchoolId][$Grade->grades->name.'-'.$Class->name]['school_id'] = $SchoolId;

                                                    // Store Exam id
                                                    $MyTeaching[$ExamData->id][$SchoolId][$Grade->grades->name.'-'.$Class->name]['exam_id'] = $ExamData->id;

                                                    // Store grade id
                                                    $MyTeaching[$ExamData->id][$SchoolId][$Grade->grades->name.'-'.$Class->name]['grade_id'] = $Grade->grades->id;

                                                    // Store Class Id
                                                    $MyTeaching[$ExamData->id][$SchoolId][$Grade->grades->name.'-'.$Class->name]['class_id'] = $Class->id;

                                                    // Store grade name with class name
                                                    $MyTeaching[$ExamData->id][$SchoolId][$Grade->grades->name.'-'.$Class->name]['grade_with_class'] = $Grade->grades->name.'-'.$Class->name;

                                                    // Store count how many student in the assigned this exams
                                                    $MyTeaching[$ExamData->id][$SchoolId][$Grade->grades->name.'-'.$Class->name]['no_of_students'] =  $NoOfStudentAssignedExam;

                                                    // Store current class student ids
                                                    $MyTeaching[$ExamData->id][$SchoolId][$Grade->grades->name.'-'.$Class->name]['student_ids'] =  $ClassStudentComaSeparated ?? null;

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
                                                    $MyTeaching[$ExamData->id][$SchoolId][$Grade->grades->name.'-'.$Class->name]['student_progress'] = json_encode($ClassStudentProgress);

                                                    // Store class student average of accuracy
                                                    $MyTeaching[$ExamData->id][$SchoolId][$Grade->grades->name.'-'.$Class->name]['average_accuracy'] = json_encode($ClassStudentAverageAccuracy);

                                                    // Find Class Student Study status data
                                                    $ClassStudentStudyStatus = $this->AlpAiGraphController->getProgressDetailList($ExamData->id,$ClassStudentComaSeparated);
                                                        
                                                    // Store Class Student Study status
                                                    $MyTeaching[$ExamData->id][$SchoolId][$Grade->grades->name.'-'.$Class->name]['study_status'] = json_encode($ClassStudentStudyStatus);

                                                    // Find Question difficulties
                                                    $QuestionDifficulties = Helper::getQuestionDifficultiesLevelPercent($ExamData->id,$ClassStudentComaSeparated);
                                                    // Store Class Student Question difficulty
                                                    $MyTeaching[$ExamData->id][$SchoolId][$Grade->grades->name.'-'.$Class->name]['questions_difficulties'] = json_encode($QuestionDifficulties);

                                                    // Store Date time field
                                                    $MyTeaching[$ExamData->id][$SchoolId][$Grade->grades->name.'-'.$Class->name]['date_time'] = date('d/m/Y H:i:s',strtotime($ExamData->created_at));
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
            
            $PostData = [];
            // Store My Teaching Record in database table
            if(isset($MyTeaching) && !empty($MyTeaching)){
                foreach($MyTeaching as $MyTeachingExam){
                    foreach($MyTeachingExam as $ExamSchool){
                        foreach($ExamSchool as $SchoolClassReport){
                            $ExistingRecord = MyTeachingReport::where(['report_type' => $SchoolClassReport['report_type'],
                            'study_type' => $SchoolClassReport['study_type'],
                            'school_id' => $SchoolClassReport['school_id'],
                            'exam_id' => $SchoolClassReport['exam_id'],
                            'grade_id' => $SchoolClassReport['grade_id'],
                            'class_id' => $SchoolClassReport['class_id'],
                            'grade_with_class' => $SchoolClassReport['grade_with_class'],
                            ])->first();
                            if(!empty($ExistingRecord)){
                                MyTeachingReport::find($ExistingRecord->id)->update($SchoolClassReport);
                            }else{
                                MyTeachingReport::Create($SchoolClassReport);
                            }
                        }
                    }
                }
            }
        }

        Log::info('MyTeaching Report Update Job End:');
    }
}
