<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Question;
use App\Constants\DbConstant As cn;
use Log;
use App\Traits\Common;

class UpdateQuestionEColumnJob implements ShouldQueue
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
        Log::info('Job Start Update Question table "E" column value');
        $QuestionList = Question::get();
        foreach($QuestionList as $question){
            Log::info('Question Code is : '. $question->question_code);
            $objectivesMapping = $this->GetStrandUnitsObjectivesMappingsId($question->question_code);
            $question = Question::whereId($question->id)->update([
                            cn::QUESTION_OBJECTIVE_MAPPING_ID_COL => $objectivesMapping['StrandUnitsObjectivesMappingsId'] ?? 0,
                            cn::QUESTION_E_COL => $objectivesMapping['e'],
                            cn::QUESTION_F_COL => $objectivesMapping['f'],
                            cn::QUESTION_G_COL => $objectivesMapping['g']
                        ]);
            Log::info('Updated Successfully Question Code is : '. $objectivesMapping['e']);
        }
        Log::info('Job Complete Update Question table "E" column value');
    }
}
