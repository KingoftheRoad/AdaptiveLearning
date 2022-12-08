<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Constants\DbConstant As cn;
use App\Traits\ResponseFormat;
use App\Models\RemainderUpdateSchoolYearData;
use App\Models\User;
use App\Traits\Common;
use Carbon\Carbon;

class SendRemainderUploadStudentNewSchoolCurriculumYearJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, ResponseFormat,Common;

    public function __construct()
    {
        //
    }

    public function handle()
    {
        ini_set('max_execution_time', -1);
        
        if(in_array($this->CurrentDate(),$this->getMondayDates(date('Y'),date('09')))){
            $SchoolIds = RemainderUpdateSchoolYearData::where([
                            cn::REMAINDER_UPDATE_SCHOOL_YEAR_DATA_CURRICULUM_YEAR_ID_COL => $this->GetNextCurriculumYearId(),
                            cn::REMAINDER_UPDATE_SCHOOL_YEAR_DATA_STATUS_COL => 'pending'
                        ])->pluck(cn::REMAINDER_UPDATE_SCHOOL_YEAR_DATA_SCHOOL_ID_COL);
            if(isset($SchoolIds) && !empty($SchoolIds)){
                $SchoolList = User::where([
                                cn::USERS_ROLE_ID_COL => cn::SCHOOL_ROLE_ID
                            ])
                            ->whereIn(cn::USERS_ID_COL,$SchoolIds->toArray())
                            ->get();
                if(isset($SchoolList) && !empty($SchoolList)){
                    foreach($SchoolList as $school){
                        $EmailData = [
                            'school' => $school,
                            'curriculum_year' => (((int)Carbon::now()->format('Y')+1).'-'.((int)(Carbon::now()->format('y'))+2)),
                            'upload_student_url' => env('APP_URL').'student/import/upgrade-school-year',
                            'teacher_class_assignment_url' => env('APP_URL').'teacher-class-subject-assign'
                        ];
                        $sendEmail = $this->sendMails('email.reminder_school_year_upgrade_data', $EmailData, $school->email, $subject='Please be reminded to upload the new student', [], []);
                    }
                }
            }
        }
    }
}