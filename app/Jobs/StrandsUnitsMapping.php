<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Strands;
use App\Models\Grades;
use App\Models\LearningsUnits;
use App\Models\LearningsObjectives;
use App\Models\StrandUnitsObjectivesMappings;
use App\Constants\DbConstant As cn;
use Log;

class StrandsUnitsMapping implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 0;
    public $grade;
    public $subject;
    public function __construct($grade,$subject)
    {
        $this->grade = $grade;
        $this->subject = $subject;
    }

    public function handle()
    {
        Log::info('Job start - StrandsUnitsMapping Start');
        $Strands = Strands::all();
        if(!empty($Strands)){
            foreach($Strands as $StrandVal){
                $LearningsUnits = LearningsUnits::where(cn::LEARNING_UNITS_STRANDID_COL,$StrandVal->id)->get();
                if(!empty($LearningsUnits)){
                    foreach($LearningsUnits as $LearningsUnit){
                        // $LearningsObjectives = LearningsObjectives::where(cn::LEARNING_OBJECTIVES_LEARNING_UNITID_COL,$LearningsUnit->id)->get();
                        $LearningsObjectives = LearningsObjectives::IsAvailableQuestion()->where(cn::LEARNING_OBJECTIVES_LEARNING_UNITID_COL,$LearningsUnit->id)->get();
                        foreach($LearningsObjectives as $LearningsObjective){
                            StrandUnitsObjectivesMappings::updateOrCreate([
                                cn::OBJECTIVES_MAPPINGS_GRADE_ID_COL => $this->grade->id,
                                cn::OBJECTIVES_MAPPINGS_SUBJECT_ID_COL => $this->subject->id,
                                cn::OBJECTIVES_MAPPINGS_STRAND_ID_COL => $StrandVal->{cn::STRANDS_ID_COL},
                                cn::OBJECTIVES_MAPPINGS_LEARNING_UNIT_ID_COL => $LearningsUnit->id,
                                cn::OBJECTIVES_MAPPINGS_LEARNING_OBJECTIVES_ID_COL => $LearningsObjective->id
                            ]);
                        }
                    }
                }
            }
        }
            
        Log::info('Job End - StrandsUnitsMapping End');
    }
}
