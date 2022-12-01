<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\StrandUnitsObjectivesMappings;
use App\Models\Strands;
use App\Models\Subjects;
use App\Models\Grades;
use App\Constants\DbConstant as cn;

class NewObjectivesMappingsSeeder extends Seeder
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
                                $data = [
                                    [
                                        cn::OBJECTIVES_MAPPINGS_GRADE_ID_COL => $GradeVal->{cn::GRADES_ID_COL},
                                        cn::OBJECTIVES_MAPPINGS_SUBJECT_ID_COL => $SubjectsVal->{cn::SUBJECTS_ID_COL},
                                        cn::OBJECTIVES_MAPPINGS_STRAND_ID_COL => $StrandVal->{cn::STRANDS_ID_COL},
                                        cn::OBJECTIVES_MAPPINGS_LEARNING_UNIT_ID_COL => 1,
                                        cn::OBJECTIVES_MAPPINGS_LEARNING_OBJECTIVES_ID_COL => 1
                                    ],
                                    [
                                        cn::OBJECTIVES_MAPPINGS_GRADE_ID_COL => $GradeVal->{cn::GRADES_ID_COL},
                                        cn::OBJECTIVES_MAPPINGS_SUBJECT_ID_COL => $SubjectsVal->{cn::SUBJECTS_ID_COL},
                                        cn::OBJECTIVES_MAPPINGS_STRAND_ID_COL => $StrandVal->{cn::STRANDS_ID_COL},
                                        cn::OBJECTIVES_MAPPINGS_LEARNING_UNIT_ID_COL => 1,
                                        cn::OBJECTIVES_MAPPINGS_LEARNING_OBJECTIVES_ID_COL => 2
                                    ],
                                    [
                                        cn::OBJECTIVES_MAPPINGS_GRADE_ID_COL => $GradeVal->{cn::GRADES_ID_COL},
                                        cn::OBJECTIVES_MAPPINGS_SUBJECT_ID_COL => $SubjectsVal->{cn::SUBJECTS_ID_COL},
                                        cn::OBJECTIVES_MAPPINGS_STRAND_ID_COL => $StrandVal->{cn::STRANDS_ID_COL},
                                        cn::OBJECTIVES_MAPPINGS_LEARNING_UNIT_ID_COL => 1,
                                        cn::OBJECTIVES_MAPPINGS_LEARNING_OBJECTIVES_ID_COL => 3
                                    ],
                                    [
                                        cn::OBJECTIVES_MAPPINGS_GRADE_ID_COL => $GradeVal->{cn::GRADES_ID_COL},
                                        cn::OBJECTIVES_MAPPINGS_SUBJECT_ID_COL => $SubjectsVal->{cn::SUBJECTS_ID_COL},
                                        cn::OBJECTIVES_MAPPINGS_STRAND_ID_COL => $StrandVal->{cn::STRANDS_ID_COL},
                                        cn::OBJECTIVES_MAPPINGS_LEARNING_UNIT_ID_COL => 1,
                                        cn::OBJECTIVES_MAPPINGS_LEARNING_OBJECTIVES_ID_COL => 4
                                    ],
                                    [
                                        cn::OBJECTIVES_MAPPINGS_GRADE_ID_COL => $GradeVal->{cn::GRADES_ID_COL},
                                        cn::OBJECTIVES_MAPPINGS_SUBJECT_ID_COL => $SubjectsVal->{cn::SUBJECTS_ID_COL},
                                        cn::OBJECTIVES_MAPPINGS_STRAND_ID_COL => $StrandVal->{cn::STRANDS_ID_COL},
                                        cn::OBJECTIVES_MAPPINGS_LEARNING_UNIT_ID_COL => 1,
                                        cn::OBJECTIVES_MAPPINGS_LEARNING_OBJECTIVES_ID_COL => 5
                                    ],
                                    [
                                        cn::OBJECTIVES_MAPPINGS_GRADE_ID_COL => $GradeVal->{cn::GRADES_ID_COL},
                                        cn::OBJECTIVES_MAPPINGS_SUBJECT_ID_COL => $SubjectsVal->{cn::SUBJECTS_ID_COL},
                                        cn::OBJECTIVES_MAPPINGS_STRAND_ID_COL => $StrandVal->{cn::STRANDS_ID_COL},
                                        cn::OBJECTIVES_MAPPINGS_LEARNING_UNIT_ID_COL => 1,
                                        cn::OBJECTIVES_MAPPINGS_LEARNING_OBJECTIVES_ID_COL => 6
                                    ],
                                    [
                                        cn::OBJECTIVES_MAPPINGS_GRADE_ID_COL => $GradeVal->{cn::GRADES_ID_COL},
                                        cn::OBJECTIVES_MAPPINGS_SUBJECT_ID_COL => $SubjectsVal->{cn::SUBJECTS_ID_COL},
                                        cn::OBJECTIVES_MAPPINGS_STRAND_ID_COL => $StrandVal->{cn::STRANDS_ID_COL},
                                        cn::OBJECTIVES_MAPPINGS_LEARNING_UNIT_ID_COL => 1,
                                        cn::OBJECTIVES_MAPPINGS_LEARNING_OBJECTIVES_ID_COL => 7
                                    ],
                                    [
                                        cn::OBJECTIVES_MAPPINGS_GRADE_ID_COL => $GradeVal->{cn::GRADES_ID_COL},
                                        cn::OBJECTIVES_MAPPINGS_SUBJECT_ID_COL => $SubjectsVal->{cn::SUBJECTS_ID_COL},
                                        cn::OBJECTIVES_MAPPINGS_STRAND_ID_COL => $StrandVal->{cn::STRANDS_ID_COL},
                                        cn::OBJECTIVES_MAPPINGS_LEARNING_UNIT_ID_COL => 1,
                                        cn::OBJECTIVES_MAPPINGS_LEARNING_OBJECTIVES_ID_COL => 8
                                    ],
                                    [
                                        cn::OBJECTIVES_MAPPINGS_GRADE_ID_COL => $GradeVal->{cn::GRADES_ID_COL},
                                        cn::OBJECTIVES_MAPPINGS_SUBJECT_ID_COL => $SubjectsVal->{cn::SUBJECTS_ID_COL},
                                        cn::OBJECTIVES_MAPPINGS_STRAND_ID_COL => $StrandVal->{cn::STRANDS_ID_COL},
                                        cn::OBJECTIVES_MAPPINGS_LEARNING_UNIT_ID_COL => 1,
                                        cn::OBJECTIVES_MAPPINGS_LEARNING_OBJECTIVES_ID_COL => 9
                                    ],
                                    [
                                        cn::OBJECTIVES_MAPPINGS_GRADE_ID_COL => $GradeVal->{cn::GRADES_ID_COL},
                                        cn::OBJECTIVES_MAPPINGS_SUBJECT_ID_COL => $SubjectsVal->{cn::SUBJECTS_ID_COL},
                                        cn::OBJECTIVES_MAPPINGS_STRAND_ID_COL => $StrandVal->{cn::STRANDS_ID_COL},
                                        cn::OBJECTIVES_MAPPINGS_LEARNING_UNIT_ID_COL => 2,
                                        cn::OBJECTIVES_MAPPINGS_LEARNING_OBJECTIVES_ID_COL => 10
                                    ],
                                    [
                                        cn::OBJECTIVES_MAPPINGS_GRADE_ID_COL => $GradeVal->{cn::GRADES_ID_COL},
                                        cn::OBJECTIVES_MAPPINGS_SUBJECT_ID_COL => $SubjectsVal->{cn::SUBJECTS_ID_COL},
                                        cn::OBJECTIVES_MAPPINGS_STRAND_ID_COL => $StrandVal->{cn::STRANDS_ID_COL},
                                        cn::OBJECTIVES_MAPPINGS_LEARNING_UNIT_ID_COL => 2,
                                        cn::OBJECTIVES_MAPPINGS_LEARNING_OBJECTIVES_ID_COL => 11
                                    ],
                                    [
                                        cn::OBJECTIVES_MAPPINGS_GRADE_ID_COL => $GradeVal->{cn::GRADES_ID_COL},
                                        cn::OBJECTIVES_MAPPINGS_SUBJECT_ID_COL => $SubjectsVal->{cn::SUBJECTS_ID_COL},
                                        cn::OBJECTIVES_MAPPINGS_STRAND_ID_COL => $StrandVal->{cn::STRANDS_ID_COL},
                                        cn::OBJECTIVES_MAPPINGS_LEARNING_UNIT_ID_COL => 2,
                                        cn::OBJECTIVES_MAPPINGS_LEARNING_OBJECTIVES_ID_COL => 12
                                    ],
                                    [
                                        cn::OBJECTIVES_MAPPINGS_GRADE_ID_COL => $GradeVal->{cn::GRADES_ID_COL},
                                        cn::OBJECTIVES_MAPPINGS_SUBJECT_ID_COL => $SubjectsVal->{cn::SUBJECTS_ID_COL},
                                        cn::OBJECTIVES_MAPPINGS_STRAND_ID_COL => $StrandVal->{cn::STRANDS_ID_COL},
                                        cn::OBJECTIVES_MAPPINGS_LEARNING_UNIT_ID_COL => 2,
                                        cn::OBJECTIVES_MAPPINGS_LEARNING_OBJECTIVES_ID_COL => 13
                                    ],
                                    [
                                        cn::OBJECTIVES_MAPPINGS_GRADE_ID_COL => $GradeVal->{cn::GRADES_ID_COL},
                                        cn::OBJECTIVES_MAPPINGS_SUBJECT_ID_COL => $SubjectsVal->{cn::SUBJECTS_ID_COL},
                                        cn::OBJECTIVES_MAPPINGS_STRAND_ID_COL => $StrandVal->{cn::STRANDS_ID_COL},
                                        cn::OBJECTIVES_MAPPINGS_LEARNING_UNIT_ID_COL => 3,
                                        cn::OBJECTIVES_MAPPINGS_LEARNING_OBJECTIVES_ID_COL => 14
                                    ],
                                    [
                                        cn::OBJECTIVES_MAPPINGS_GRADE_ID_COL => $GradeVal->{cn::GRADES_ID_COL},
                                        cn::OBJECTIVES_MAPPINGS_SUBJECT_ID_COL => $SubjectsVal->{cn::SUBJECTS_ID_COL},
                                        cn::OBJECTIVES_MAPPINGS_STRAND_ID_COL => $StrandVal->{cn::STRANDS_ID_COL},
                                        cn::OBJECTIVES_MAPPINGS_LEARNING_UNIT_ID_COL => 3,
                                        cn::OBJECTIVES_MAPPINGS_LEARNING_OBJECTIVES_ID_COL => 15
                                    ],
                                    [
                                        cn::OBJECTIVES_MAPPINGS_GRADE_ID_COL => $GradeVal->{cn::GRADES_ID_COL},
                                        cn::OBJECTIVES_MAPPINGS_SUBJECT_ID_COL => $SubjectsVal->{cn::SUBJECTS_ID_COL},
                                        cn::OBJECTIVES_MAPPINGS_STRAND_ID_COL => $StrandVal->{cn::STRANDS_ID_COL},
                                        cn::OBJECTIVES_MAPPINGS_LEARNING_UNIT_ID_COL => 3,
                                        cn::OBJECTIVES_MAPPINGS_LEARNING_OBJECTIVES_ID_COL => 15
                                    ],
                                    [
                                        cn::OBJECTIVES_MAPPINGS_GRADE_ID_COL => $GradeVal->{cn::GRADES_ID_COL},
                                        cn::OBJECTIVES_MAPPINGS_SUBJECT_ID_COL => $SubjectsVal->{cn::SUBJECTS_ID_COL},
                                        cn::OBJECTIVES_MAPPINGS_STRAND_ID_COL => $StrandVal->{cn::STRANDS_ID_COL},
                                        cn::OBJECTIVES_MAPPINGS_LEARNING_UNIT_ID_COL => 3,
                                        cn::OBJECTIVES_MAPPINGS_LEARNING_OBJECTIVES_ID_COL => 16
                                    ],
                                    [
                                        cn::OBJECTIVES_MAPPINGS_GRADE_ID_COL => $GradeVal->{cn::GRADES_ID_COL},
                                        cn::OBJECTIVES_MAPPINGS_SUBJECT_ID_COL => $SubjectsVal->{cn::SUBJECTS_ID_COL},
                                        cn::OBJECTIVES_MAPPINGS_STRAND_ID_COL => $StrandVal->{cn::STRANDS_ID_COL},
                                        cn::OBJECTIVES_MAPPINGS_LEARNING_UNIT_ID_COL => 3,
                                        cn::OBJECTIVES_MAPPINGS_LEARNING_OBJECTIVES_ID_COL => 17
                                    ],
                                    [
                                        cn::OBJECTIVES_MAPPINGS_GRADE_ID_COL => $GradeVal->{cn::GRADES_ID_COL},
                                        cn::OBJECTIVES_MAPPINGS_SUBJECT_ID_COL => $SubjectsVal->{cn::SUBJECTS_ID_COL},
                                        cn::OBJECTIVES_MAPPINGS_STRAND_ID_COL => $StrandVal->{cn::STRANDS_ID_COL},
                                        cn::OBJECTIVES_MAPPINGS_LEARNING_UNIT_ID_COL => 3,
                                        cn::OBJECTIVES_MAPPINGS_LEARNING_OBJECTIVES_ID_COL => 18
                                    ],
                                    [
                                        cn::OBJECTIVES_MAPPINGS_GRADE_ID_COL => $GradeVal->{cn::GRADES_ID_COL},
                                        cn::OBJECTIVES_MAPPINGS_SUBJECT_ID_COL => $SubjectsVal->{cn::SUBJECTS_ID_COL},
                                        cn::OBJECTIVES_MAPPINGS_STRAND_ID_COL => $StrandVal->{cn::STRANDS_ID_COL},
                                        cn::OBJECTIVES_MAPPINGS_LEARNING_UNIT_ID_COL => 3,
                                        cn::OBJECTIVES_MAPPINGS_LEARNING_OBJECTIVES_ID_COL => 19
                                    ],
                                    [
                                        cn::OBJECTIVES_MAPPINGS_GRADE_ID_COL => $GradeVal->{cn::GRADES_ID_COL},
                                        cn::OBJECTIVES_MAPPINGS_SUBJECT_ID_COL => $SubjectsVal->{cn::SUBJECTS_ID_COL},
                                        cn::OBJECTIVES_MAPPINGS_STRAND_ID_COL => $StrandVal->{cn::STRANDS_ID_COL},
                                        cn::OBJECTIVES_MAPPINGS_LEARNING_UNIT_ID_COL => 3,
                                        cn::OBJECTIVES_MAPPINGS_LEARNING_OBJECTIVES_ID_COL => 20
                                    ],
                                    [
                                        cn::OBJECTIVES_MAPPINGS_GRADE_ID_COL => $GradeVal->{cn::GRADES_ID_COL},
                                        cn::OBJECTIVES_MAPPINGS_SUBJECT_ID_COL => $SubjectsVal->{cn::SUBJECTS_ID_COL},
                                        cn::OBJECTIVES_MAPPINGS_STRAND_ID_COL => $StrandVal->{cn::STRANDS_ID_COL},
                                        cn::OBJECTIVES_MAPPINGS_LEARNING_UNIT_ID_COL => 4,
                                        cn::OBJECTIVES_MAPPINGS_LEARNING_OBJECTIVES_ID_COL => 21
                                    ],
                                    [
                                        cn::OBJECTIVES_MAPPINGS_GRADE_ID_COL => $GradeVal->{cn::GRADES_ID_COL},
                                        cn::OBJECTIVES_MAPPINGS_SUBJECT_ID_COL => $SubjectsVal->{cn::SUBJECTS_ID_COL},
                                        cn::OBJECTIVES_MAPPINGS_STRAND_ID_COL => $StrandVal->{cn::STRANDS_ID_COL},
                                        cn::OBJECTIVES_MAPPINGS_LEARNING_UNIT_ID_COL => 4,
                                        cn::OBJECTIVES_MAPPINGS_LEARNING_OBJECTIVES_ID_COL => 22
                                    ],
                                    [
                                        cn::OBJECTIVES_MAPPINGS_GRADE_ID_COL => $GradeVal->{cn::GRADES_ID_COL},
                                        cn::OBJECTIVES_MAPPINGS_SUBJECT_ID_COL => $SubjectsVal->{cn::SUBJECTS_ID_COL},
                                        cn::OBJECTIVES_MAPPINGS_STRAND_ID_COL => $StrandVal->{cn::STRANDS_ID_COL},
                                        cn::OBJECTIVES_MAPPINGS_LEARNING_UNIT_ID_COL => 4,
                                        cn::OBJECTIVES_MAPPINGS_LEARNING_OBJECTIVES_ID_COL => 23
                                    ],
                                    [
                                        cn::OBJECTIVES_MAPPINGS_GRADE_ID_COL => $GradeVal->{cn::GRADES_ID_COL},
                                        cn::OBJECTIVES_MAPPINGS_SUBJECT_ID_COL => $SubjectsVal->{cn::SUBJECTS_ID_COL},
                                        cn::OBJECTIVES_MAPPINGS_STRAND_ID_COL => $StrandVal->{cn::STRANDS_ID_COL},
                                        cn::OBJECTIVES_MAPPINGS_LEARNING_UNIT_ID_COL => 4,
                                        cn::OBJECTIVES_MAPPINGS_LEARNING_OBJECTIVES_ID_COL => 24
                                    ],
                                    [
                                        cn::OBJECTIVES_MAPPINGS_GRADE_ID_COL => $GradeVal->{cn::GRADES_ID_COL},
                                        cn::OBJECTIVES_MAPPINGS_SUBJECT_ID_COL => $SubjectsVal->{cn::SUBJECTS_ID_COL},
                                        cn::OBJECTIVES_MAPPINGS_STRAND_ID_COL => $StrandVal->{cn::STRANDS_ID_COL},
                                        cn::OBJECTIVES_MAPPINGS_LEARNING_UNIT_ID_COL => 4,
                                        cn::OBJECTIVES_MAPPINGS_LEARNING_OBJECTIVES_ID_COL => 25
                                    ],
                                    [
                                        cn::OBJECTIVES_MAPPINGS_GRADE_ID_COL => $GradeVal->{cn::GRADES_ID_COL},
                                        cn::OBJECTIVES_MAPPINGS_SUBJECT_ID_COL => $SubjectsVal->{cn::SUBJECTS_ID_COL},
                                        cn::OBJECTIVES_MAPPINGS_STRAND_ID_COL => $StrandVal->{cn::STRANDS_ID_COL},
                                        cn::OBJECTIVES_MAPPINGS_LEARNING_UNIT_ID_COL => 5,
                                        cn::OBJECTIVES_MAPPINGS_LEARNING_OBJECTIVES_ID_COL => 26
                                    ],
                                    [
                                        cn::OBJECTIVES_MAPPINGS_GRADE_ID_COL => $GradeVal->{cn::GRADES_ID_COL},
                                        cn::OBJECTIVES_MAPPINGS_SUBJECT_ID_COL => $SubjectsVal->{cn::SUBJECTS_ID_COL},
                                        cn::OBJECTIVES_MAPPINGS_STRAND_ID_COL => $StrandVal->{cn::STRANDS_ID_COL},
                                        cn::OBJECTIVES_MAPPINGS_LEARNING_UNIT_ID_COL => 5,
                                        cn::OBJECTIVES_MAPPINGS_LEARNING_OBJECTIVES_ID_COL => 27
                                    ],
                                    [
                                        cn::OBJECTIVES_MAPPINGS_GRADE_ID_COL => $GradeVal->{cn::GRADES_ID_COL},
                                        cn::OBJECTIVES_MAPPINGS_SUBJECT_ID_COL => $SubjectsVal->{cn::SUBJECTS_ID_COL},
                                        cn::OBJECTIVES_MAPPINGS_STRAND_ID_COL => $StrandVal->{cn::STRANDS_ID_COL},
                                        cn::OBJECTIVES_MAPPINGS_LEARNING_UNIT_ID_COL => 5,
                                        cn::OBJECTIVES_MAPPINGS_LEARNING_OBJECTIVES_ID_COL => 28
                                    ],
                                    [
                                        cn::OBJECTIVES_MAPPINGS_GRADE_ID_COL => $GradeVal->{cn::GRADES_ID_COL},
                                        cn::OBJECTIVES_MAPPINGS_SUBJECT_ID_COL => $SubjectsVal->{cn::SUBJECTS_ID_COL},
                                        cn::OBJECTIVES_MAPPINGS_STRAND_ID_COL => $StrandVal->{cn::STRANDS_ID_COL},
                                        cn::OBJECTIVES_MAPPINGS_LEARNING_UNIT_ID_COL => 5,
                                        cn::OBJECTIVES_MAPPINGS_LEARNING_OBJECTIVES_ID_COL => 29
                                    ],
                                    [
                                        cn::OBJECTIVES_MAPPINGS_GRADE_ID_COL => $GradeVal->{cn::GRADES_ID_COL},
                                        cn::OBJECTIVES_MAPPINGS_SUBJECT_ID_COL => $SubjectsVal->{cn::SUBJECTS_ID_COL},
                                        cn::OBJECTIVES_MAPPINGS_STRAND_ID_COL => $StrandVal->{cn::STRANDS_ID_COL},
                                        cn::OBJECTIVES_MAPPINGS_LEARNING_UNIT_ID_COL => 6,
                                        cn::OBJECTIVES_MAPPINGS_LEARNING_OBJECTIVES_ID_COL => 30
                                    ],
                                    [
                                        cn::OBJECTIVES_MAPPINGS_GRADE_ID_COL => $GradeVal->{cn::GRADES_ID_COL},
                                        cn::OBJECTIVES_MAPPINGS_SUBJECT_ID_COL => $SubjectsVal->{cn::SUBJECTS_ID_COL},
                                        cn::OBJECTIVES_MAPPINGS_STRAND_ID_COL => $StrandVal->{cn::STRANDS_ID_COL},
                                        cn::OBJECTIVES_MAPPINGS_LEARNING_UNIT_ID_COL => 6,
                                        cn::OBJECTIVES_MAPPINGS_LEARNING_OBJECTIVES_ID_COL => 31
                                    ],
                                    [
                                        cn::OBJECTIVES_MAPPINGS_GRADE_ID_COL => $GradeVal->{cn::GRADES_ID_COL},
                                        cn::OBJECTIVES_MAPPINGS_SUBJECT_ID_COL => $SubjectsVal->{cn::SUBJECTS_ID_COL},
                                        cn::OBJECTIVES_MAPPINGS_STRAND_ID_COL => $StrandVal->{cn::STRANDS_ID_COL},
                                        cn::OBJECTIVES_MAPPINGS_LEARNING_UNIT_ID_COL => 6,
                                        cn::OBJECTIVES_MAPPINGS_LEARNING_OBJECTIVES_ID_COL => 32
                                    ],
                                    [
                                        cn::OBJECTIVES_MAPPINGS_GRADE_ID_COL => $GradeVal->{cn::GRADES_ID_COL},
                                        cn::OBJECTIVES_MAPPINGS_SUBJECT_ID_COL => $SubjectsVal->{cn::SUBJECTS_ID_COL},
                                        cn::OBJECTIVES_MAPPINGS_STRAND_ID_COL => $StrandVal->{cn::STRANDS_ID_COL},
                                        cn::OBJECTIVES_MAPPINGS_LEARNING_UNIT_ID_COL => 7,
                                        cn::OBJECTIVES_MAPPINGS_LEARNING_OBJECTIVES_ID_COL => 33
                                    ],
                                    [
                                        cn::OBJECTIVES_MAPPINGS_GRADE_ID_COL => $GradeVal->{cn::GRADES_ID_COL},
                                        cn::OBJECTIVES_MAPPINGS_SUBJECT_ID_COL => $SubjectsVal->{cn::SUBJECTS_ID_COL},
                                        cn::OBJECTIVES_MAPPINGS_STRAND_ID_COL => $StrandVal->{cn::STRANDS_ID_COL},
                                        cn::OBJECTIVES_MAPPINGS_LEARNING_UNIT_ID_COL => 7,
                                        cn::OBJECTIVES_MAPPINGS_LEARNING_OBJECTIVES_ID_COL => 34
                                    ],
                                    [
                                        cn::OBJECTIVES_MAPPINGS_GRADE_ID_COL => $GradeVal->{cn::GRADES_ID_COL},
                                        cn::OBJECTIVES_MAPPINGS_SUBJECT_ID_COL => $SubjectsVal->{cn::SUBJECTS_ID_COL},
                                        cn::OBJECTIVES_MAPPINGS_STRAND_ID_COL => $StrandVal->{cn::STRANDS_ID_COL},
                                        cn::OBJECTIVES_MAPPINGS_LEARNING_UNIT_ID_COL => 7,
                                        cn::OBJECTIVES_MAPPINGS_LEARNING_OBJECTIVES_ID_COL => 35
                                    ],
                                    [
                                        cn::OBJECTIVES_MAPPINGS_GRADE_ID_COL => $GradeVal->{cn::GRADES_ID_COL},
                                        cn::OBJECTIVES_MAPPINGS_SUBJECT_ID_COL => $SubjectsVal->{cn::SUBJECTS_ID_COL},
                                        cn::OBJECTIVES_MAPPINGS_STRAND_ID_COL => $StrandVal->{cn::STRANDS_ID_COL},
                                        cn::OBJECTIVES_MAPPINGS_LEARNING_UNIT_ID_COL => 7,
                                        cn::OBJECTIVES_MAPPINGS_LEARNING_OBJECTIVES_ID_COL => 36
                                    ],
                                    [
                                        cn::OBJECTIVES_MAPPINGS_GRADE_ID_COL => $GradeVal->{cn::GRADES_ID_COL},
                                        cn::OBJECTIVES_MAPPINGS_SUBJECT_ID_COL => $SubjectsVal->{cn::SUBJECTS_ID_COL},
                                        cn::OBJECTIVES_MAPPINGS_STRAND_ID_COL => $StrandVal->{cn::STRANDS_ID_COL},
                                        cn::OBJECTIVES_MAPPINGS_LEARNING_UNIT_ID_COL => 7,
                                        cn::OBJECTIVES_MAPPINGS_LEARNING_OBJECTIVES_ID_COL => 37
                                    ],
                                    [
                                        cn::OBJECTIVES_MAPPINGS_GRADE_ID_COL => $GradeVal->{cn::GRADES_ID_COL},
                                        cn::OBJECTIVES_MAPPINGS_SUBJECT_ID_COL => $SubjectsVal->{cn::SUBJECTS_ID_COL},
                                        cn::OBJECTIVES_MAPPINGS_STRAND_ID_COL => $StrandVal->{cn::STRANDS_ID_COL},
                                        cn::OBJECTIVES_MAPPINGS_LEARNING_UNIT_ID_COL => 7,
                                        cn::OBJECTIVES_MAPPINGS_LEARNING_OBJECTIVES_ID_COL => 38
                                    ],
                                    [
                                        cn::OBJECTIVES_MAPPINGS_GRADE_ID_COL => $GradeVal->{cn::GRADES_ID_COL},
                                        cn::OBJECTIVES_MAPPINGS_SUBJECT_ID_COL => $SubjectsVal->{cn::SUBJECTS_ID_COL},
                                        cn::OBJECTIVES_MAPPINGS_STRAND_ID_COL => $StrandVal->{cn::STRANDS_ID_COL},
                                        cn::OBJECTIVES_MAPPINGS_LEARNING_UNIT_ID_COL => 7,
                                        cn::OBJECTIVES_MAPPINGS_LEARNING_OBJECTIVES_ID_COL => 39
                                    ],
                                    [
                                        cn::OBJECTIVES_MAPPINGS_GRADE_ID_COL => $GradeVal->{cn::GRADES_ID_COL},
                                        cn::OBJECTIVES_MAPPINGS_SUBJECT_ID_COL => $SubjectsVal->{cn::SUBJECTS_ID_COL},
                                        cn::OBJECTIVES_MAPPINGS_STRAND_ID_COL => $StrandVal->{cn::STRANDS_ID_COL},
                                        cn::OBJECTIVES_MAPPINGS_LEARNING_UNIT_ID_COL => 8,
                                        cn::OBJECTIVES_MAPPINGS_LEARNING_OBJECTIVES_ID_COL => 40
                                    ],
                                    [
                                        cn::OBJECTIVES_MAPPINGS_GRADE_ID_COL => $GradeVal->{cn::GRADES_ID_COL},
                                        cn::OBJECTIVES_MAPPINGS_SUBJECT_ID_COL => $SubjectsVal->{cn::SUBJECTS_ID_COL},
                                        cn::OBJECTIVES_MAPPINGS_STRAND_ID_COL => $StrandVal->{cn::STRANDS_ID_COL},
                                        cn::OBJECTIVES_MAPPINGS_LEARNING_UNIT_ID_COL => 8,
                                        cn::OBJECTIVES_MAPPINGS_LEARNING_OBJECTIVES_ID_COL => 41
                                    ],
                                    [
                                        cn::OBJECTIVES_MAPPINGS_GRADE_ID_COL => $GradeVal->{cn::GRADES_ID_COL},
                                        cn::OBJECTIVES_MAPPINGS_SUBJECT_ID_COL => $SubjectsVal->{cn::SUBJECTS_ID_COL},
                                        cn::OBJECTIVES_MAPPINGS_STRAND_ID_COL => $StrandVal->{cn::STRANDS_ID_COL},
                                        cn::OBJECTIVES_MAPPINGS_LEARNING_UNIT_ID_COL => 8,
                                        cn::OBJECTIVES_MAPPINGS_LEARNING_OBJECTIVES_ID_COL => 42
                                    ],
                                    [
                                        cn::OBJECTIVES_MAPPINGS_GRADE_ID_COL => $GradeVal->{cn::GRADES_ID_COL},
                                        cn::OBJECTIVES_MAPPINGS_SUBJECT_ID_COL => $SubjectsVal->{cn::SUBJECTS_ID_COL},
                                        cn::OBJECTIVES_MAPPINGS_STRAND_ID_COL => $StrandVal->{cn::STRANDS_ID_COL},
                                        cn::OBJECTIVES_MAPPINGS_LEARNING_UNIT_ID_COL => 8,
                                        cn::OBJECTIVES_MAPPINGS_LEARNING_OBJECTIVES_ID_COL => 43
                                    ],
                                    [
                                        cn::OBJECTIVES_MAPPINGS_GRADE_ID_COL => $GradeVal->{cn::GRADES_ID_COL},
                                        cn::OBJECTIVES_MAPPINGS_SUBJECT_ID_COL => $SubjectsVal->{cn::SUBJECTS_ID_COL},
                                        cn::OBJECTIVES_MAPPINGS_STRAND_ID_COL => $StrandVal->{cn::STRANDS_ID_COL},
                                        cn::OBJECTIVES_MAPPINGS_LEARNING_UNIT_ID_COL => 8,
                                        cn::OBJECTIVES_MAPPINGS_LEARNING_OBJECTIVES_ID_COL => 44
                                    ],
                                    [
                                        cn::OBJECTIVES_MAPPINGS_GRADE_ID_COL => $GradeVal->{cn::GRADES_ID_COL},
                                        cn::OBJECTIVES_MAPPINGS_SUBJECT_ID_COL => $SubjectsVal->{cn::SUBJECTS_ID_COL},
                                        cn::OBJECTIVES_MAPPINGS_STRAND_ID_COL => $StrandVal->{cn::STRANDS_ID_COL},
                                        cn::OBJECTIVES_MAPPINGS_LEARNING_UNIT_ID_COL => 8,
                                        cn::OBJECTIVES_MAPPINGS_LEARNING_OBJECTIVES_ID_COL => 45
                                    ],
                                    [
                                        cn::OBJECTIVES_MAPPINGS_GRADE_ID_COL => $GradeVal->{cn::GRADES_ID_COL},
                                        cn::OBJECTIVES_MAPPINGS_SUBJECT_ID_COL => $SubjectsVal->{cn::SUBJECTS_ID_COL},
                                        cn::OBJECTIVES_MAPPINGS_STRAND_ID_COL => $StrandVal->{cn::STRANDS_ID_COL},
                                        cn::OBJECTIVES_MAPPINGS_LEARNING_UNIT_ID_COL => 9,
                                        cn::OBJECTIVES_MAPPINGS_LEARNING_OBJECTIVES_ID_COL => 46
                                    ],
                                    [
                                        cn::OBJECTIVES_MAPPINGS_GRADE_ID_COL => $GradeVal->{cn::GRADES_ID_COL},
                                        cn::OBJECTIVES_MAPPINGS_SUBJECT_ID_COL => $SubjectsVal->{cn::SUBJECTS_ID_COL},
                                        cn::OBJECTIVES_MAPPINGS_STRAND_ID_COL => $StrandVal->{cn::STRANDS_ID_COL},
                                        cn::OBJECTIVES_MAPPINGS_LEARNING_UNIT_ID_COL => 9,
                                        cn::OBJECTIVES_MAPPINGS_LEARNING_OBJECTIVES_ID_COL => 47
                                    ],
                                    [
                                        cn::OBJECTIVES_MAPPINGS_GRADE_ID_COL => $GradeVal->{cn::GRADES_ID_COL},
                                        cn::OBJECTIVES_MAPPINGS_SUBJECT_ID_COL => $SubjectsVal->{cn::SUBJECTS_ID_COL},
                                        cn::OBJECTIVES_MAPPINGS_STRAND_ID_COL => $StrandVal->{cn::STRANDS_ID_COL},
                                        cn::OBJECTIVES_MAPPINGS_LEARNING_UNIT_ID_COL => 9,
                                        cn::OBJECTIVES_MAPPINGS_LEARNING_OBJECTIVES_ID_COL => 48
                                    ],
                                    [
                                        cn::OBJECTIVES_MAPPINGS_GRADE_ID_COL => $GradeVal->{cn::GRADES_ID_COL},
                                        cn::OBJECTIVES_MAPPINGS_SUBJECT_ID_COL => $SubjectsVal->{cn::SUBJECTS_ID_COL},
                                        cn::OBJECTIVES_MAPPINGS_STRAND_ID_COL => $StrandVal->{cn::STRANDS_ID_COL},
                                        cn::OBJECTIVES_MAPPINGS_LEARNING_UNIT_ID_COL => 9,
                                        cn::OBJECTIVES_MAPPINGS_LEARNING_OBJECTIVES_ID_COL => 49
                                    ],
                                    [
                                        cn::OBJECTIVES_MAPPINGS_GRADE_ID_COL => $GradeVal->{cn::GRADES_ID_COL},
                                        cn::OBJECTIVES_MAPPINGS_SUBJECT_ID_COL => $SubjectsVal->{cn::SUBJECTS_ID_COL},
                                        cn::OBJECTIVES_MAPPINGS_STRAND_ID_COL => $StrandVal->{cn::STRANDS_ID_COL},
                                        cn::OBJECTIVES_MAPPINGS_LEARNING_UNIT_ID_COL => 10,
                                        cn::OBJECTIVES_MAPPINGS_LEARNING_OBJECTIVES_ID_COL => 50
                                    ],
                                    [
                                        cn::OBJECTIVES_MAPPINGS_GRADE_ID_COL => $GradeVal->{cn::GRADES_ID_COL},
                                        cn::OBJECTIVES_MAPPINGS_SUBJECT_ID_COL => $SubjectsVal->{cn::SUBJECTS_ID_COL},
                                        cn::OBJECTIVES_MAPPINGS_STRAND_ID_COL => $StrandVal->{cn::STRANDS_ID_COL},
                                        cn::OBJECTIVES_MAPPINGS_LEARNING_UNIT_ID_COL => 10,
                                        cn::OBJECTIVES_MAPPINGS_LEARNING_OBJECTIVES_ID_COL => 51
                                    ],
                                    [
                                        cn::OBJECTIVES_MAPPINGS_GRADE_ID_COL => $GradeVal->{cn::GRADES_ID_COL},
                                        cn::OBJECTIVES_MAPPINGS_SUBJECT_ID_COL => $SubjectsVal->{cn::SUBJECTS_ID_COL},
                                        cn::OBJECTIVES_MAPPINGS_STRAND_ID_COL => $StrandVal->{cn::STRANDS_ID_COL},
                                        cn::OBJECTIVES_MAPPINGS_LEARNING_UNIT_ID_COL => 11,
                                        cn::OBJECTIVES_MAPPINGS_LEARNING_OBJECTIVES_ID_COL => 52
                                    ],
                                    [
                                        cn::OBJECTIVES_MAPPINGS_GRADE_ID_COL => $GradeVal->{cn::GRADES_ID_COL},
                                        cn::OBJECTIVES_MAPPINGS_SUBJECT_ID_COL => $SubjectsVal->{cn::SUBJECTS_ID_COL},
                                        cn::OBJECTIVES_MAPPINGS_STRAND_ID_COL => $StrandVal->{cn::STRANDS_ID_COL},
                                        cn::OBJECTIVES_MAPPINGS_LEARNING_UNIT_ID_COL => 11,
                                        cn::OBJECTIVES_MAPPINGS_LEARNING_OBJECTIVES_ID_COL => 53
                                    ],
                                    [
                                        cn::OBJECTIVES_MAPPINGS_GRADE_ID_COL => $GradeVal->{cn::GRADES_ID_COL},
                                        cn::OBJECTIVES_MAPPINGS_SUBJECT_ID_COL => $SubjectsVal->{cn::SUBJECTS_ID_COL},
                                        cn::OBJECTIVES_MAPPINGS_STRAND_ID_COL => $StrandVal->{cn::STRANDS_ID_COL},
                                        cn::OBJECTIVES_MAPPINGS_LEARNING_UNIT_ID_COL => 11,
                                        cn::OBJECTIVES_MAPPINGS_LEARNING_OBJECTIVES_ID_COL => 54
                                    ],
                                    [
                                        cn::OBJECTIVES_MAPPINGS_GRADE_ID_COL => $GradeVal->{cn::GRADES_ID_COL},
                                        cn::OBJECTIVES_MAPPINGS_SUBJECT_ID_COL => $SubjectsVal->{cn::SUBJECTS_ID_COL},
                                        cn::OBJECTIVES_MAPPINGS_STRAND_ID_COL => $StrandVal->{cn::STRANDS_ID_COL},
                                        cn::OBJECTIVES_MAPPINGS_LEARNING_UNIT_ID_COL => 11,
                                        cn::OBJECTIVES_MAPPINGS_LEARNING_OBJECTIVES_ID_COL => 55
                                    ],
                                    [
                                        cn::OBJECTIVES_MAPPINGS_GRADE_ID_COL => $GradeVal->{cn::GRADES_ID_COL},
                                        cn::OBJECTIVES_MAPPINGS_SUBJECT_ID_COL => $SubjectsVal->{cn::SUBJECTS_ID_COL},
                                        cn::OBJECTIVES_MAPPINGS_STRAND_ID_COL => $StrandVal->{cn::STRANDS_ID_COL},
                                        cn::OBJECTIVES_MAPPINGS_LEARNING_UNIT_ID_COL => 11,
                                        cn::OBJECTIVES_MAPPINGS_LEARNING_OBJECTIVES_ID_COL => 56
                                    ],
                                    [
                                        cn::OBJECTIVES_MAPPINGS_GRADE_ID_COL => $GradeVal->{cn::GRADES_ID_COL},
                                        cn::OBJECTIVES_MAPPINGS_SUBJECT_ID_COL => $SubjectsVal->{cn::SUBJECTS_ID_COL},
                                        cn::OBJECTIVES_MAPPINGS_STRAND_ID_COL => $StrandVal->{cn::STRANDS_ID_COL},
                                        cn::OBJECTIVES_MAPPINGS_LEARNING_UNIT_ID_COL => 11,
                                        cn::OBJECTIVES_MAPPINGS_LEARNING_OBJECTIVES_ID_COL => 57
                                    ],
                                    [
                                        cn::OBJECTIVES_MAPPINGS_GRADE_ID_COL => $GradeVal->{cn::GRADES_ID_COL},
                                        cn::OBJECTIVES_MAPPINGS_SUBJECT_ID_COL => $SubjectsVal->{cn::SUBJECTS_ID_COL},
                                        cn::OBJECTIVES_MAPPINGS_STRAND_ID_COL => $StrandVal->{cn::STRANDS_ID_COL},
                                        cn::OBJECTIVES_MAPPINGS_LEARNING_UNIT_ID_COL => 12,
                                        cn::OBJECTIVES_MAPPINGS_LEARNING_OBJECTIVES_ID_COL => 58
                                    ],
                                    [
                                        cn::OBJECTIVES_MAPPINGS_GRADE_ID_COL => $GradeVal->{cn::GRADES_ID_COL},
                                        cn::OBJECTIVES_MAPPINGS_SUBJECT_ID_COL => $SubjectsVal->{cn::SUBJECTS_ID_COL},
                                        cn::OBJECTIVES_MAPPINGS_STRAND_ID_COL => $StrandVal->{cn::STRANDS_ID_COL},
                                        cn::OBJECTIVES_MAPPINGS_LEARNING_UNIT_ID_COL => 12,
                                        cn::OBJECTIVES_MAPPINGS_LEARNING_OBJECTIVES_ID_COL => 59
                                    ],
                                    [
                                        cn::OBJECTIVES_MAPPINGS_GRADE_ID_COL => $GradeVal->{cn::GRADES_ID_COL},
                                        cn::OBJECTIVES_MAPPINGS_SUBJECT_ID_COL => $SubjectsVal->{cn::SUBJECTS_ID_COL},
                                        cn::OBJECTIVES_MAPPINGS_STRAND_ID_COL => $StrandVal->{cn::STRANDS_ID_COL},
                                        cn::OBJECTIVES_MAPPINGS_LEARNING_UNIT_ID_COL => 12,
                                        cn::OBJECTIVES_MAPPINGS_LEARNING_OBJECTIVES_ID_COL => 60
                                    ],
                                    [
                                        cn::OBJECTIVES_MAPPINGS_GRADE_ID_COL => $GradeVal->{cn::GRADES_ID_COL},
                                        cn::OBJECTIVES_MAPPINGS_SUBJECT_ID_COL => $SubjectsVal->{cn::SUBJECTS_ID_COL},
                                        cn::OBJECTIVES_MAPPINGS_STRAND_ID_COL => $StrandVal->{cn::STRANDS_ID_COL},
                                        cn::OBJECTIVES_MAPPINGS_LEARNING_UNIT_ID_COL => 13,
                                        cn::OBJECTIVES_MAPPINGS_LEARNING_OBJECTIVES_ID_COL => 61
                                    ],
                                    [
                                        cn::OBJECTIVES_MAPPINGS_GRADE_ID_COL => $GradeVal->{cn::GRADES_ID_COL},
                                        cn::OBJECTIVES_MAPPINGS_SUBJECT_ID_COL => $SubjectsVal->{cn::SUBJECTS_ID_COL},
                                        cn::OBJECTIVES_MAPPINGS_STRAND_ID_COL => $StrandVal->{cn::STRANDS_ID_COL},
                                        cn::OBJECTIVES_MAPPINGS_LEARNING_UNIT_ID_COL => 13,
                                        cn::OBJECTIVES_MAPPINGS_LEARNING_OBJECTIVES_ID_COL => 62
                                    ],
                                    [
                                        cn::OBJECTIVES_MAPPINGS_GRADE_ID_COL => $GradeVal->{cn::GRADES_ID_COL},
                                        cn::OBJECTIVES_MAPPINGS_SUBJECT_ID_COL => $SubjectsVal->{cn::SUBJECTS_ID_COL},
                                        cn::OBJECTIVES_MAPPINGS_STRAND_ID_COL => $StrandVal->{cn::STRANDS_ID_COL},
                                        cn::OBJECTIVES_MAPPINGS_LEARNING_UNIT_ID_COL => 14,
                                        cn::OBJECTIVES_MAPPINGS_LEARNING_OBJECTIVES_ID_COL => 63
                                    ],
                                    [
                                        cn::OBJECTIVES_MAPPINGS_GRADE_ID_COL => $GradeVal->{cn::GRADES_ID_COL},
                                        cn::OBJECTIVES_MAPPINGS_SUBJECT_ID_COL => $SubjectsVal->{cn::SUBJECTS_ID_COL},
                                        cn::OBJECTIVES_MAPPINGS_STRAND_ID_COL => $StrandVal->{cn::STRANDS_ID_COL},
                                        cn::OBJECTIVES_MAPPINGS_LEARNING_UNIT_ID_COL => 14,
                                        cn::OBJECTIVES_MAPPINGS_LEARNING_OBJECTIVES_ID_COL => 64
                                    ],
                                    [
                                        cn::OBJECTIVES_MAPPINGS_GRADE_ID_COL => $GradeVal->{cn::GRADES_ID_COL},
                                        cn::OBJECTIVES_MAPPINGS_SUBJECT_ID_COL => $SubjectsVal->{cn::SUBJECTS_ID_COL},
                                        cn::OBJECTIVES_MAPPINGS_STRAND_ID_COL => $StrandVal->{cn::STRANDS_ID_COL},
                                        cn::OBJECTIVES_MAPPINGS_LEARNING_UNIT_ID_COL => 14,
                                        cn::OBJECTIVES_MAPPINGS_LEARNING_OBJECTIVES_ID_COL => 65
                                    ],
                                    [
                                        cn::OBJECTIVES_MAPPINGS_GRADE_ID_COL => $GradeVal->{cn::GRADES_ID_COL},
                                        cn::OBJECTIVES_MAPPINGS_SUBJECT_ID_COL => $SubjectsVal->{cn::SUBJECTS_ID_COL},
                                        cn::OBJECTIVES_MAPPINGS_STRAND_ID_COL => $StrandVal->{cn::STRANDS_ID_COL},
                                        cn::OBJECTIVES_MAPPINGS_LEARNING_UNIT_ID_COL => 14,
                                        cn::OBJECTIVES_MAPPINGS_LEARNING_OBJECTIVES_ID_COL => 66
                                    ],
                                    [
                                        cn::OBJECTIVES_MAPPINGS_GRADE_ID_COL => $GradeVal->{cn::GRADES_ID_COL},
                                        cn::OBJECTIVES_MAPPINGS_SUBJECT_ID_COL => $SubjectsVal->{cn::SUBJECTS_ID_COL},
                                        cn::OBJECTIVES_MAPPINGS_STRAND_ID_COL => $StrandVal->{cn::STRANDS_ID_COL},
                                        cn::OBJECTIVES_MAPPINGS_LEARNING_UNIT_ID_COL => 14,
                                        cn::OBJECTIVES_MAPPINGS_LEARNING_OBJECTIVES_ID_COL => 67
                                    ],
                                    [
                                        cn::OBJECTIVES_MAPPINGS_GRADE_ID_COL => $GradeVal->{cn::GRADES_ID_COL},
                                        cn::OBJECTIVES_MAPPINGS_SUBJECT_ID_COL => $SubjectsVal->{cn::SUBJECTS_ID_COL},
                                        cn::OBJECTIVES_MAPPINGS_STRAND_ID_COL => $StrandVal->{cn::STRANDS_ID_COL},
                                        cn::OBJECTIVES_MAPPINGS_LEARNING_UNIT_ID_COL => 14,
                                        cn::OBJECTIVES_MAPPINGS_LEARNING_OBJECTIVES_ID_COL => 68
                                    ],
                                    [
                                        cn::OBJECTIVES_MAPPINGS_GRADE_ID_COL => $GradeVal->{cn::GRADES_ID_COL},
                                        cn::OBJECTIVES_MAPPINGS_SUBJECT_ID_COL => $SubjectsVal->{cn::SUBJECTS_ID_COL},
                                        cn::OBJECTIVES_MAPPINGS_STRAND_ID_COL => $StrandVal->{cn::STRANDS_ID_COL},
                                        cn::OBJECTIVES_MAPPINGS_LEARNING_UNIT_ID_COL => 14,
                                        cn::OBJECTIVES_MAPPINGS_LEARNING_OBJECTIVES_ID_COL => 69
                                    ],
                                    [
                                        cn::OBJECTIVES_MAPPINGS_GRADE_ID_COL => $GradeVal->{cn::GRADES_ID_COL},
                                        cn::OBJECTIVES_MAPPINGS_SUBJECT_ID_COL => $SubjectsVal->{cn::SUBJECTS_ID_COL},
                                        cn::OBJECTIVES_MAPPINGS_STRAND_ID_COL => $StrandVal->{cn::STRANDS_ID_COL},
                                        cn::OBJECTIVES_MAPPINGS_LEARNING_UNIT_ID_COL => 14,
                                        cn::OBJECTIVES_MAPPINGS_LEARNING_OBJECTIVES_ID_COL => 70
                                    ],
                                    [
                                        cn::OBJECTIVES_MAPPINGS_GRADE_ID_COL => $GradeVal->{cn::GRADES_ID_COL},
                                        cn::OBJECTIVES_MAPPINGS_SUBJECT_ID_COL => $SubjectsVal->{cn::SUBJECTS_ID_COL},
                                        cn::OBJECTIVES_MAPPINGS_STRAND_ID_COL => $StrandVal->{cn::STRANDS_ID_COL},
                                        cn::OBJECTIVES_MAPPINGS_LEARNING_UNIT_ID_COL => 14,
                                        cn::OBJECTIVES_MAPPINGS_LEARNING_OBJECTIVES_ID_COL => 71
                                    ],
                                    [
                                        cn::OBJECTIVES_MAPPINGS_GRADE_ID_COL => $GradeVal->{cn::GRADES_ID_COL},
                                        cn::OBJECTIVES_MAPPINGS_SUBJECT_ID_COL => $SubjectsVal->{cn::SUBJECTS_ID_COL},
                                        cn::OBJECTIVES_MAPPINGS_STRAND_ID_COL => $StrandVal->{cn::STRANDS_ID_COL},
                                        cn::OBJECTIVES_MAPPINGS_LEARNING_UNIT_ID_COL => 15,
                                        cn::OBJECTIVES_MAPPINGS_LEARNING_OBJECTIVES_ID_COL => 72
                                    ],
                                    [
                                        cn::OBJECTIVES_MAPPINGS_GRADE_ID_COL => $GradeVal->{cn::GRADES_ID_COL},
                                        cn::OBJECTIVES_MAPPINGS_SUBJECT_ID_COL => $SubjectsVal->{cn::SUBJECTS_ID_COL},
                                        cn::OBJECTIVES_MAPPINGS_STRAND_ID_COL => $StrandVal->{cn::STRANDS_ID_COL},
                                        cn::OBJECTIVES_MAPPINGS_LEARNING_UNIT_ID_COL => 15,
                                        cn::OBJECTIVES_MAPPINGS_LEARNING_OBJECTIVES_ID_COL => 73
                                    ],
                                    [
                                        cn::OBJECTIVES_MAPPINGS_GRADE_ID_COL => $GradeVal->{cn::GRADES_ID_COL},
                                        cn::OBJECTIVES_MAPPINGS_SUBJECT_ID_COL => $SubjectsVal->{cn::SUBJECTS_ID_COL},
                                        cn::OBJECTIVES_MAPPINGS_STRAND_ID_COL => $StrandVal->{cn::STRANDS_ID_COL},
                                        cn::OBJECTIVES_MAPPINGS_LEARNING_UNIT_ID_COL => 15,
                                        cn::OBJECTIVES_MAPPINGS_LEARNING_OBJECTIVES_ID_COL => 74
                                    ],
                                    [
                                        cn::OBJECTIVES_MAPPINGS_GRADE_ID_COL => $GradeVal->{cn::GRADES_ID_COL},
                                        cn::OBJECTIVES_MAPPINGS_SUBJECT_ID_COL => $SubjectsVal->{cn::SUBJECTS_ID_COL},
                                        cn::OBJECTIVES_MAPPINGS_STRAND_ID_COL => $StrandVal->{cn::STRANDS_ID_COL},
                                        cn::OBJECTIVES_MAPPINGS_LEARNING_UNIT_ID_COL => 15,
                                        cn::OBJECTIVES_MAPPINGS_LEARNING_OBJECTIVES_ID_COL => 75
                                    ],
                                    [
                                        cn::OBJECTIVES_MAPPINGS_GRADE_ID_COL => $GradeVal->{cn::GRADES_ID_COL},
                                        cn::OBJECTIVES_MAPPINGS_SUBJECT_ID_COL => $SubjectsVal->{cn::SUBJECTS_ID_COL},
                                        cn::OBJECTIVES_MAPPINGS_STRAND_ID_COL => $StrandVal->{cn::STRANDS_ID_COL},
                                        cn::OBJECTIVES_MAPPINGS_LEARNING_UNIT_ID_COL => 15,
                                        cn::OBJECTIVES_MAPPINGS_LEARNING_OBJECTIVES_ID_COL => 76
                                    ],
                                    [
                                        cn::OBJECTIVES_MAPPINGS_GRADE_ID_COL => $GradeVal->{cn::GRADES_ID_COL},
                                        cn::OBJECTIVES_MAPPINGS_SUBJECT_ID_COL => $SubjectsVal->{cn::SUBJECTS_ID_COL},
                                        cn::OBJECTIVES_MAPPINGS_STRAND_ID_COL => $StrandVal->{cn::STRANDS_ID_COL},
                                        cn::OBJECTIVES_MAPPINGS_LEARNING_UNIT_ID_COL => 16,
                                        cn::OBJECTIVES_MAPPINGS_LEARNING_OBJECTIVES_ID_COL => 77
                                    ],
                                    [
                                        cn::OBJECTIVES_MAPPINGS_GRADE_ID_COL => $GradeVal->{cn::GRADES_ID_COL},
                                        cn::OBJECTIVES_MAPPINGS_SUBJECT_ID_COL => $SubjectsVal->{cn::SUBJECTS_ID_COL},
                                        cn::OBJECTIVES_MAPPINGS_STRAND_ID_COL => $StrandVal->{cn::STRANDS_ID_COL},
                                        cn::OBJECTIVES_MAPPINGS_LEARNING_UNIT_ID_COL => 16,
                                        cn::OBJECTIVES_MAPPINGS_LEARNING_OBJECTIVES_ID_COL => 78
                                    ],
                                    [
                                        cn::OBJECTIVES_MAPPINGS_GRADE_ID_COL => $GradeVal->{cn::GRADES_ID_COL},
                                        cn::OBJECTIVES_MAPPINGS_SUBJECT_ID_COL => $SubjectsVal->{cn::SUBJECTS_ID_COL},
                                        cn::OBJECTIVES_MAPPINGS_STRAND_ID_COL => $StrandVal->{cn::STRANDS_ID_COL},
                                        cn::OBJECTIVES_MAPPINGS_LEARNING_UNIT_ID_COL => 16,
                                        cn::OBJECTIVES_MAPPINGS_LEARNING_OBJECTIVES_ID_COL => 79
                                    ],
                                    [
                                        cn::OBJECTIVES_MAPPINGS_GRADE_ID_COL => $GradeVal->{cn::GRADES_ID_COL},
                                        cn::OBJECTIVES_MAPPINGS_SUBJECT_ID_COL => $SubjectsVal->{cn::SUBJECTS_ID_COL},
                                        cn::OBJECTIVES_MAPPINGS_STRAND_ID_COL => $StrandVal->{cn::STRANDS_ID_COL},
                                        cn::OBJECTIVES_MAPPINGS_LEARNING_UNIT_ID_COL => 16,
                                        cn::OBJECTIVES_MAPPINGS_LEARNING_OBJECTIVES_ID_COL => 80
                                    ],
                                    [
                                        cn::OBJECTIVES_MAPPINGS_GRADE_ID_COL => $GradeVal->{cn::GRADES_ID_COL},
                                        cn::OBJECTIVES_MAPPINGS_SUBJECT_ID_COL => $SubjectsVal->{cn::SUBJECTS_ID_COL},
                                        cn::OBJECTIVES_MAPPINGS_STRAND_ID_COL => $StrandVal->{cn::STRANDS_ID_COL},
                                        cn::OBJECTIVES_MAPPINGS_LEARNING_UNIT_ID_COL => 16,
                                        cn::OBJECTIVES_MAPPINGS_LEARNING_OBJECTIVES_ID_COL => 81
                                    ],
                                    [
                                        cn::OBJECTIVES_MAPPINGS_GRADE_ID_COL => $GradeVal->{cn::GRADES_ID_COL},
                                        cn::OBJECTIVES_MAPPINGS_SUBJECT_ID_COL => $SubjectsVal->{cn::SUBJECTS_ID_COL},
                                        cn::OBJECTIVES_MAPPINGS_STRAND_ID_COL => $StrandVal->{cn::STRANDS_ID_COL},
                                        cn::OBJECTIVES_MAPPINGS_LEARNING_UNIT_ID_COL => 17,
                                        cn::OBJECTIVES_MAPPINGS_LEARNING_OBJECTIVES_ID_COL => 82
                                    ],
                                    [
                                        cn::OBJECTIVES_MAPPINGS_GRADE_ID_COL => $GradeVal->{cn::GRADES_ID_COL},
                                        cn::OBJECTIVES_MAPPINGS_SUBJECT_ID_COL => $SubjectsVal->{cn::SUBJECTS_ID_COL},
                                        cn::OBJECTIVES_MAPPINGS_STRAND_ID_COL => $StrandVal->{cn::STRANDS_ID_COL},
                                        cn::OBJECTIVES_MAPPINGS_LEARNING_UNIT_ID_COL => 17,
                                        cn::OBJECTIVES_MAPPINGS_LEARNING_OBJECTIVES_ID_COL => 83
                                    ],
                                    [
                                        cn::OBJECTIVES_MAPPINGS_GRADE_ID_COL => $GradeVal->{cn::GRADES_ID_COL},
                                        cn::OBJECTIVES_MAPPINGS_SUBJECT_ID_COL => $SubjectsVal->{cn::SUBJECTS_ID_COL},
                                        cn::OBJECTIVES_MAPPINGS_STRAND_ID_COL => $StrandVal->{cn::STRANDS_ID_COL},
                                        cn::OBJECTIVES_MAPPINGS_LEARNING_UNIT_ID_COL => 17,
                                        cn::OBJECTIVES_MAPPINGS_LEARNING_OBJECTIVES_ID_COL => 84
                                    ],
                                    [
                                        cn::OBJECTIVES_MAPPINGS_GRADE_ID_COL => $GradeVal->{cn::GRADES_ID_COL},
                                        cn::OBJECTIVES_MAPPINGS_SUBJECT_ID_COL => $SubjectsVal->{cn::SUBJECTS_ID_COL},
                                        cn::OBJECTIVES_MAPPINGS_STRAND_ID_COL => $StrandVal->{cn::STRANDS_ID_COL},
                                        cn::OBJECTIVES_MAPPINGS_LEARNING_UNIT_ID_COL => 17,
                                        cn::OBJECTIVES_MAPPINGS_LEARNING_OBJECTIVES_ID_COL => 85
                                    ],
                                    [
                                        cn::OBJECTIVES_MAPPINGS_GRADE_ID_COL => $GradeVal->{cn::GRADES_ID_COL},
                                        cn::OBJECTIVES_MAPPINGS_SUBJECT_ID_COL => $SubjectsVal->{cn::SUBJECTS_ID_COL},
                                        cn::OBJECTIVES_MAPPINGS_STRAND_ID_COL => $StrandVal->{cn::STRANDS_ID_COL},
                                        cn::OBJECTIVES_MAPPINGS_LEARNING_UNIT_ID_COL => 17,
                                        cn::OBJECTIVES_MAPPINGS_LEARNING_OBJECTIVES_ID_COL => 86
                                    ],
                                    [
                                        cn::OBJECTIVES_MAPPINGS_GRADE_ID_COL => $GradeVal->{cn::GRADES_ID_COL},
                                        cn::OBJECTIVES_MAPPINGS_SUBJECT_ID_COL => $SubjectsVal->{cn::SUBJECTS_ID_COL},
                                        cn::OBJECTIVES_MAPPINGS_STRAND_ID_COL => $StrandVal->{cn::STRANDS_ID_COL},
                                        cn::OBJECTIVES_MAPPINGS_LEARNING_UNIT_ID_COL => 17,
                                        cn::OBJECTIVES_MAPPINGS_LEARNING_OBJECTIVES_ID_COL => 87
                                    ],
                                    [
                                        cn::OBJECTIVES_MAPPINGS_GRADE_ID_COL => $GradeVal->{cn::GRADES_ID_COL},
                                        cn::OBJECTIVES_MAPPINGS_SUBJECT_ID_COL => $SubjectsVal->{cn::SUBJECTS_ID_COL},
                                        cn::OBJECTIVES_MAPPINGS_STRAND_ID_COL => $StrandVal->{cn::STRANDS_ID_COL},
                                        cn::OBJECTIVES_MAPPINGS_LEARNING_UNIT_ID_COL => 17,
                                        cn::OBJECTIVES_MAPPINGS_LEARNING_OBJECTIVES_ID_COL => 88
                                    ],
                                    [
                                        cn::OBJECTIVES_MAPPINGS_GRADE_ID_COL => $GradeVal->{cn::GRADES_ID_COL},
                                        cn::OBJECTIVES_MAPPINGS_SUBJECT_ID_COL => $SubjectsVal->{cn::SUBJECTS_ID_COL},
                                        cn::OBJECTIVES_MAPPINGS_STRAND_ID_COL => $StrandVal->{cn::STRANDS_ID_COL},
                                        cn::OBJECTIVES_MAPPINGS_LEARNING_UNIT_ID_COL => 18,
                                        cn::OBJECTIVES_MAPPINGS_LEARNING_OBJECTIVES_ID_COL => 89
                                    ],
                                    [
                                        cn::OBJECTIVES_MAPPINGS_GRADE_ID_COL => $GradeVal->{cn::GRADES_ID_COL},
                                        cn::OBJECTIVES_MAPPINGS_SUBJECT_ID_COL => $SubjectsVal->{cn::SUBJECTS_ID_COL},
                                        cn::OBJECTIVES_MAPPINGS_STRAND_ID_COL => $StrandVal->{cn::STRANDS_ID_COL},
                                        cn::OBJECTIVES_MAPPINGS_LEARNING_UNIT_ID_COL => 18,
                                        cn::OBJECTIVES_MAPPINGS_LEARNING_OBJECTIVES_ID_COL => 90
                                    ],
                                    [
                                        cn::OBJECTIVES_MAPPINGS_GRADE_ID_COL => $GradeVal->{cn::GRADES_ID_COL},
                                        cn::OBJECTIVES_MAPPINGS_SUBJECT_ID_COL => $SubjectsVal->{cn::SUBJECTS_ID_COL},
                                        cn::OBJECTIVES_MAPPINGS_STRAND_ID_COL => $StrandVal->{cn::STRANDS_ID_COL},
                                        cn::OBJECTIVES_MAPPINGS_LEARNING_UNIT_ID_COL => 18,
                                        cn::OBJECTIVES_MAPPINGS_LEARNING_OBJECTIVES_ID_COL => 91
                                    ],
                                ];

                                if(!empty($data)){
                                    foreach($data as $key => $value){
                                        StrandUnitsObjectivesMappings::create($value);
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
