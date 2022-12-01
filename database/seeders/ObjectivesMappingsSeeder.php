<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\StrandUnitsObjectivesMappings;
use App\Models\Strands;
use App\Models\Subjects;
use App\Models\Grades;
use App\Models\LearningsUnits;
use App\Models\LearningsObjectives;
use App\Constants\DbConstant as cn;

class ObjectivesMappingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $Grades = Grades::all();
        if(!empty($Grades)){
            foreach($Grades as $GradeVal){
                $Subjects = Subjects::all();
                if(!empty($Subjects)){
                    foreach($Subjects as $SubjectsVal){
                        $Strands = Strands::all();
                        if(!empty($Strands)){
                            foreach($Strands as $StrandVal){
                                $LearningsUnits = LearningsUnits::where(cn::LEARNING_UNITS_STRANDID_COL,$StrandVal->id)->get();
                                if(!empty($LearningsUnits)){
                                    foreach($LearningsUnits as $LearningsUnit){
                                        $LearningsObjectives = LearningsObjectives::where(cn::LEARNING_OBJECTIVES_LEARNING_UNITID_COL,$LearningsUnit->id)->get();
                                        foreach($LearningsObjectives as $LearningsObjective){
                                            StrandUnitsObjectivesMappings::create([
                                                cn::OBJECTIVES_MAPPINGS_GRADE_ID_COL => $GradeVal->{cn::GRADES_ID_COL},
                                                cn::OBJECTIVES_MAPPINGS_SUBJECT_ID_COL => $SubjectsVal->{cn::SUBJECTS_ID_COL},
                                                cn::OBJECTIVES_MAPPINGS_STRAND_ID_COL => $StrandVal->{cn::STRANDS_ID_COL},
                                                cn::OBJECTIVES_MAPPINGS_LEARNING_UNIT_ID_COL => $LearningsUnit->id,
                                                cn::OBJECTIVES_MAPPINGS_LEARNING_OBJECTIVES_ID_COL => $LearningsObjective->id
                                            ]);
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
