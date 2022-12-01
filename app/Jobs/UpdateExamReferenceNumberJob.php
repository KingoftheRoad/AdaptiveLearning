<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Exam;
use App\Constants\DbConstant As cn;
use Log;
use App\Traits\Common;

class UpdateExamReferenceNumberJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, Common;
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
        ini_set('max_execution_time', -1);
        Log::info('Job Start Update Test/Exercise Reference Number');
        $MaxTestingUniqueId = 10000000001;
        $MaxSelfLearningUniqueId = 10000000001;
        $MaxExerciseUniqueId = 10000000001;
        $MaxTestingZoneUniqueId= 10000000001;
        $updatedData = true;
        $ExamData = Exam::All();
        if(!empty($ExamData)){
            foreach($ExamData as $exam){
                switch($exam->{cn::EXAM_TYPE_COLS}){
                    case 1 :
                        if($exam->{cn::EXAM_TABLE_SELF_LEARNING_TEST_TYPE_COL} == 1){
                            $updatedData =Exam::find($exam->{cn::EXAM_TABLE_ID_COLS})->update([cn::EXAM_REFERENCE_NO_COL => 'S'.$MaxSelfLearningUniqueId++]);
                            break;
                        }else{
                            $updatedData = Exam::find($exam->{cn::EXAM_TABLE_ID_COLS})->update([cn::EXAM_REFERENCE_NO_COL => 'Z'.$MaxTestingZoneUniqueId++]);
                            break;
                        }
                    case 2 :
                        $updatedData = Exam::find($exam->{cn::EXAM_TABLE_ID_COLS})->update([cn::EXAM_REFERENCE_NO_COL => 'E'.$MaxExerciseUniqueId++]);
                        break;
                    case 3 :
                        $updatedData = Exam::find($exam->{cn::EXAM_TABLE_ID_COLS})->update([cn::EXAM_REFERENCE_NO_COL => 'T'.$MaxTestingUniqueId++]);
                        break;
                }
                if(empty($updatedData) || $updatedData == false){
                    Log::info('Job Failed : Test/Exercise Update Reference Number');
                    break;
                }
            }
        }
        Log::info('Job Complete: Test/Exercise Update Reference Number');
    }
}
