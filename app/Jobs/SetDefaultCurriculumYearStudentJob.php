<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Constants\DbConstant As cn;
use App\Models\CurriculumYearStudentMappings;
use App\Models\User;
use App\Models\ClassPromotionHistory;

class SetDefaultCurriculumYearStudentJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $StudentList = User::withTrashed()->where(cn::USERS_ROLE_ID_COL,cn::STUDENT_ROLE_ID)->get();
        if(isset($StudentList) && !empty($StudentList)){
            foreach($StudentList as $student){
                CurriculumYearStudentMappings::updateOrCreate([
                    cn::CURRICULUM_YEAR_STUDENT_MAPPING_USER_ID_COL => $student->{cn::USERS_ID_COL},
                    cn::CURRICULUM_YEAR_STUDENT_MAPPING_CURRICULUM_YEAR_ID_COL => cn::DEFAULT_CURRICULUM_YEAR_ID,
                    cn::CURRICULUM_YEAR_STUDENT_MAPPING_SCHOOL_ID_COL => $student->{cn::USERS_SCHOOL_ID_COL},
                    // cn::CURRICULUM_YEAR_STUDENT_MAPPING_GRADE_ID_COL => (!empty($student->{cn::USERS_GRADE_ID_COL})) ? $student->{cn::USERS_GRADE_ID_COL} : null,
                    // cn::CURRICULUM_YEAR_STUDENT_MAPPING_CLASS_ID_COL => (!empty($student->{cn::USERS_CLASS_ID_COL})) ? $student->{cn::USERS_CLASS_ID_COL} : null,
                    cn::CURRICULUM_YEAR_STUDENT_MAPPING_GRADE_ID_COL => (!empty($student->CurriculumYearGradeId)) ? $student->CurriculumYearGradeId : null,
                    cn::CURRICULUM_YEAR_STUDENT_MAPPING_CLASS_ID_COL => (!empty($student->CurriculumYearClassId)) ? $student->CurriculumYearClassId : null,
                    cn::CURRICULUM_YEAR_STUDENT_NUMBER_WITHIN_CLASS_COL => $student->{cn::STUDENT_NUMBER_WITHIN_CLASS} ?? Null,
                    cn::CURRICULUM_YEAR_STUDENT_CLASS => $student->{cn::USERS_CLASS} ?? NUll,
                    cn::CURRICULUM_YEAR_CLASS_STUDENT_NUMBER => $student->{cn::USERS_CLASS_STUDENT_NUMBER} ?? NULL
                ]);

                ClassPromotionHistory::Create([
                    //cn::CLASS_PROMOTION_HISTORY_CURRICULUM_YEAR_ID_COL => $this->GetCurriculumYear(),
                    cn::CLASS_PROMOTION_HISTORY_CURRICULUM_YEAR_ID_COL => 23,
                    cn::CLASS_PROMOTION_HISTORY_SCHOOL_ID_COL => $student->{cn::USERS_SCHOOL_ID_COL},
                    cn::CLASS_PROMOTION_HISTORY_STUDENT_ID_COL => $student->{cn::USERS_ID_COL},
                    cn::CLASS_PROMOTION_HISTORY_CURRENT_GRADE_ID_COL => null,
                    cn::CLASS_PROMOTION_HISTORY_CURRENT_CLASS_ID_COL => null,
                    // cn::CLASS_PROMOTION_HISTORY_PROMOTED_GRADE_ID_COL => (!empty($student->{cn::USERS_GRADE_ID_COL})) ? $student->{cn::USERS_GRADE_ID_COL} : null,
                    // cn::CLASS_PROMOTION_HISTORY_PROMOTED_CLASS_ID_COL => (!empty($student->{cn::USERS_CLASS_ID_COL})) ? $student->{cn::USERS_CLASS_ID_COL} : null,
                    cn::CLASS_PROMOTION_HISTORY_PROMOTED_GRADE_ID_COL => (!empty($student->CurriculumYearGradeId)) ? $student->CurriculumYearGradeId : null,
                    cn::CLASS_PROMOTION_HISTORY_PROMOTED_CLASS_ID_COL => (!empty($student->CurriculumYearClassId)) ? $student->CurriculumYearClassId : null,
                    cn::CLASS_PROMOTION_HISTORY_PROMOTED_BY_USER_ID_COL => 1
                ]);
            }
            echo 'Updated successfully';
        }
    }
}
