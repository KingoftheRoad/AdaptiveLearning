<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Exam;
use App\Models\UserCreditPoints;
use App\Models\UserCreditPointHistory;
use App\Models\GlobalConfiguration;
use App\Constants\DbConstant As cn;
use Log;
use App\Helpers\Helper;
use App\Traits\Common;

class UpdateExamUniqueIdJob implements ShouldQueue
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
        Log::info('Job Start Update Exam Unique Id');
        $MaxTestingUniqueId = 10000000001;
        $MaxSelfLearningUniqueId = 10000000001;
        $MaxExerciseUniqueId = 10000000001;
        $MaxTestingZoneUniqueId= 10000000001;
        $updatedData = true;
        $ExamData = Exam::All();
        if(!empty($ExamData)){
            foreach($ExamData as $exam){
                Switch($exam->exam_type){
                    case 1 :
                        if($exam->self_learning_test_type == 1){
                            $updatedData =Exam::find($exam->id)->update(['exam_unique_id' => 'S'.$MaxSelfLearningUniqueId++]);
                            break;
                        }else{
                            $updatedData = Exam::find($exam->id)->update(['exam_unique_id' => 'Z'.$MaxTestingZoneUniqueId++]);
                            break;
                        }
                    case 2 :
                        $updatedData = Exam::find($exam->id)->update(['exam_unique_id' => 'E'.$MaxExerciseUniqueId++]);
                        break;
                    case 3 :
                        $updatedData = Exam::find($exam->id)->update(['exam_unique_id' => 'T'.$MaxTestingUniqueId++]);
                        break;
                }
                if(empty($updatedData) || $updatedData == false){
                    Log::info('Job Exam Unique Id Something Want To Wrong');
                    break;
                }
            }
        }
        Log::info('Job Complete Update Exam Unique Id');
    }
}
