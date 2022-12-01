<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\CurriculumYear;
use App\Models\User;
use App\Models\Grades;
use App\Models\Subjects;
use App\Models\SubjectSchoolMappings;
use App\Models\GradeSchoolMappings;
use App\Models\GradeClassMapping;
use App\Models\ClassSubjectMapping;
use App\Models\School;
use App\Models\TeachersClassSubjectAssign;
use App\Models\RemainderUpdateSchoolYearData;
use App\Constants\DbConstant As cn;
use Carbon\Carbon;
use Log;

class CloneSchoolDataNextCurriculumYear implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $CurrentCurriculumYearId,$CurrentCurriculumYear,$nextCurriculumYear,$nextCurriculumYearId,$SchoolId;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->CurrentCurriculumYearId = null;
        $this->CurrentCurriculumYear = null;
        $this->nextCurriculumYearId = null;
        $this->nextCurriculumYear = null;
        $this->SchoolId = null;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        ini_set('max_execution_time', -1);

        Log::info('Job Start: Copy current curriculum year data and clone in to next curriculum year school data');

        // Find Next Curriculum Year
        $this->nextCurriculumYear = (((int)Carbon::now()->format('Y')+1).'-'.((int)(Carbon::now()->format('y'))+2));

        // Fins Current Curriculum Year
        $this->CurrentCurriculumYear = ((int)Carbon::now()->format('Y').'-'.((int)(Carbon::now()->format('y'))+1));

        // Get the Current Curriculum Year Id
        $CurriculumYearData = CurriculumYear::where(cn::CURRICULUM_YEAR_YEAR_COL,$this->CurrentCurriculumYear)->first();
        if(CurriculumYear::where(cn::CURRICULUM_YEAR_YEAR_COL,$this->nextCurriculumYear)->doesntExist()){
            $CurriculumYear =   CurriculumYear::Create([
                                    cn::CURRICULUM_YEAR_YEAR_COL => $this->nextCurriculumYear,
                                    cn::CURRICULUM_YEAR_STATUS_COL => 'active'
                                ]);
        }else{
            $CurriculumYear = CurriculumYear::where([
                                cn::CURRICULUM_YEAR_YEAR_COL => $this->nextCurriculumYear,
                                cn::CURRICULUM_YEAR_STATUS_COL => 'active'
                            ])->first();
        }
        // Store Next Curriculum Year Id
        $this->nextCurriculumYearId = $CurriculumYear->{cn::CURRICULUM_YEAR_ID_COL};

        if(isset($CurriculumYearData) && !empty($CurriculumYearData)){
            $this->CurrentCurriculumYearId = $CurriculumYearData->{cn::CURRICULUM_YEAR_ID_COL};

            // Get School List
            $SchoolList = School::where([cn::SCHOOL_SCHOOL_STATUS => 'active'])->get();
            if(!$SchoolList->isEmpty()){
                foreach($SchoolList as $SchoolKey => $School){
                    
                    // Store Current School Id
                    $this->SchoolId = $School->{cn::SCHOOL_ID_COLS};

                    Log::info('Data Clone Start for School Id:'.$School->{cn::SCHOOL_ID_COLS});
                    
                    // Find Default Subject Data
                    $Subject = Subjects::find(1);

                    // Create Subject School Mapping
                    SubjectSchoolMappings::updateOrCreate([
                        cn::SUBJECT_MAPPING_CURRICULUM_YEAR_ID_COL => $this->nextCurriculumYearId,
                        cn::SUBJECT_MAPPING_SCHOOL_ID_COL => $this->SchoolId,
                        cn::SUBJECT_MAPPING_SUBJECT_ID_COL => $Subject->{cn::SUBJECTS_ID_COL},
                        cn::SUBJECT_MAPPING_STATUS_COL => 'active'
                    ],[
                        cn::SUBJECT_MAPPING_CURRICULUM_YEAR_ID_COL => $this->nextCurriculumYearId,
                        cn::SUBJECT_MAPPING_SCHOOL_ID_COL => $this->SchoolId,
                        cn::SUBJECT_MAPPING_SUBJECT_ID_COL => $Subject->{cn::SUBJECTS_ID_COL},
                        cn::SUBJECT_MAPPING_STATUS_COL => 'active'
                    ]);
                    // End Subject School Mapping

                    // Find Available Grade School Mappings
                    $GradeSchoolMappingsIds = GradeSchoolMappings::where([
                                                cn::GRADES_MAPPING_SCHOOL_ID_COL => $this->SchoolId,
                                                cn::GRADES_MAPPING_CURRICULUM_YEAR_ID_COL => $this->CurrentCurriculumYearId,
                                                cn::GRADES_MAPPING_STATUS_COL => 'active'
                                            ])->pluck(cn::GRADES_MAPPING_ID_COL);
                    if(isset($GradeSchoolMappingsIds) && !empty($GradeSchoolMappingsIds)){
                        foreach($GradeSchoolMappingsIds as $GradeSchoolMappingsId){
                            $GradeSchoolMappings = GradeSchoolMappings::find($GradeSchoolMappingsId);
                            if(isset($GradeSchoolMappings) && !empty($GradeSchoolMappings)){
                                GradeSchoolMappings::updateOrCreate([
                                    cn::GRADES_MAPPING_CURRICULUM_YEAR_ID_COL => $this->nextCurriculumYearId,
                                    cn::GRADES_MAPPING_SCHOOL_ID_COL => $this->SchoolId,
                                    cn::GRADES_MAPPING_GRADE_ID_COL => $GradeSchoolMappings->{cn::GRADES_MAPPING_GRADE_ID_COL}
                                ],[
                                    cn::GRADES_MAPPING_CURRICULUM_YEAR_ID_COL => $this->nextCurriculumYearId,
                                    cn::GRADES_MAPPING_SCHOOL_ID_COL => $this->SchoolId,
                                    cn::GRADES_MAPPING_GRADE_ID_COL => $GradeSchoolMappings->{cn::GRADES_MAPPING_GRADE_ID_COL}
                                ]);

                                // Store Class Subject Mappings
                                ClassSubjectMapping::updateOrCreate([
                                    cn::CLASS_SUBJECT_MAPPING_CURRICULUM_YEAR_ID_COL => $this->nextCurriculumYearId,
                                    cn::CLASS_SUBJECT_MAPPING_SUBJECT_ID_COL => $Subject->{cn::SUBJECTS_ID_COL},
                                    cn::CLASS_SUBJECT_MAPPING_CLASS_ID_COL => $GradeSchoolMappings->{cn::GRADES_MAPPING_GRADE_ID_COL},
                                    cn::CLASS_SUBJECT_MAPPING_SCHOOL_ID_COL => $this->SchoolId,
                                    cn::CLASS_SUBJECT_MAPPING_STATUS_COL => 1
                                ],
                                [
                                    cn::CLASS_SUBJECT_MAPPING_CURRICULUM_YEAR_ID_COL => $this->nextCurriculumYearId,
                                    cn::CLASS_SUBJECT_MAPPING_SUBJECT_ID_COL => $Subject->{cn::SUBJECTS_ID_COL},
                                    cn::CLASS_SUBJECT_MAPPING_CLASS_ID_COL => $GradeSchoolMappings->{cn::GRADES_MAPPING_GRADE_ID_COL},
                                    cn::CLASS_SUBJECT_MAPPING_SCHOOL_ID_COL => $this->SchoolId,
                                    cn::CLASS_SUBJECT_MAPPING_STATUS_COL => 1
                                ]);
                            }
                        }
                    }

                    // Start Operation for Grade Class Mapping Data
                    $GradeClassMappingIds = GradeClassMapping::where([
                                                cn::GRADE_CLASS_MAPPING_CURRICULUM_YEAR_ID_COL => $this->CurrentCurriculumYearId,
                                                cn::GRADE_CLASS_MAPPING_SCHOOL_ID_COL => $this->SchoolId,
                                                cn::GRADE_CLASS_MAPPING_STATUS_COL => 'active'
                                            ])->pluck(cn::GRADE_CLASS_MAPPING_ID_COL);
                    if(isset($GradeClassMappingIds) && !empty($GradeClassMappingIds)){
                        foreach($GradeClassMappingIds as $GradeClassMappingId){
                            $GradeClassMappingData = GradeClassMapping::find($GradeClassMappingId);
                            if(isset($GradeClassMappingData) && !empty($GradeClassMappingData)){
                                GradeClassMapping::updateOrCreate([
                                    cn::GRADE_CLASS_MAPPING_CURRICULUM_YEAR_ID_COL => $this->nextCurriculumYearId,
                                    cn::GRADE_CLASS_MAPPING_SCHOOL_ID_COL => $this->SchoolId,
                                    cn::GRADE_CLASS_MAPPING_GRADE_ID_COL => $GradeClassMappingData->{cn::GRADE_CLASS_MAPPING_GRADE_ID_COL},
                                    cn::GRADE_CLASS_MAPPING_NAME_COL => $GradeClassMappingData->{cn::GRADE_CLASS_MAPPING_NAME_COL}
                                ],[
                                    cn::GRADE_CLASS_MAPPING_CURRICULUM_YEAR_ID_COL => $this->nextCurriculumYearId,
                                    cn::GRADE_CLASS_MAPPING_SCHOOL_ID_COL => $this->SchoolId,
                                    cn::GRADE_CLASS_MAPPING_GRADE_ID_COL => $GradeClassMappingData->{cn::GRADE_CLASS_MAPPING_GRADE_ID_COL},
                                    cn::GRADE_CLASS_MAPPING_NAME_COL => $GradeClassMappingData->{cn::GRADE_CLASS_MAPPING_NAME_COL},
                                    cn::GRADE_CLASS_MAPPING_STATUS_COL => 'active'
                                ]);
                            }
                        }
                    }
                    // End Operation for Grade Class Mapping Data

                    
                    // Start Operation for Teacher class subject assign mapping data
                    // $TeachersClassSubjectAssign = TeachersClassSubjectAssign::where([
                    //                                     cn::TEACHER_CLASS_SUBJECT_CURRICULUM_YEAR_ID_COL => $this->CurrentCurriculumYearId,
                    //                                     cn::TEACHER_CLASS_SUBJECT_SCHOOL_ID_COL => $this->SchoolId
                    //                                 ])->get();
                    // if(isset($TeachersClassSubjectAssign) && !empty($TeachersClassSubjectAssign)){
                    //     $TeachersClassSubjectAssignIds = $TeachersClassSubjectAssign->pluck(cn::TEACHER_CLASS_SUBJECT_ID_COL);
                    //     foreach($TeachersClassSubjectAssignIds as $TeachersClassSubjectAssignId){
                    //         $ClassAssignData = $TeachersClassSubjectAssign->find($TeachersClassSubjectAssignId);
                    //         $UserData = User::find($ClassAssignData->{cn::TEACHER_CLASS_SUBJECT_TEACHER_ID_COL});
                    //         if(isset($UserData) && !empty($UserData)){
                    //             if(isset($ClassAssignData) && !empty($ClassAssignData)){
                    //                 $ExistingClassIds = ($ClassAssignData->{cn::TEACHER_CLASS_SUBJECT_CLASS_NAME_ID_COL}) ? explode(',',$ClassAssignData->{cn::TEACHER_CLASS_SUBJECT_CLASS_NAME_ID_COL}) : [];
                    //                 if(!empty($ExistingClassIds)){
                    //                     $ClassNames = GradeClassMapping::where([
                    //                                     cn::GRADE_CLASS_MAPPING_CURRICULUM_YEAR_ID_COL => $this->CurrentCurriculumYearId,
                    //                                     cn::GRADE_CLASS_MAPPING_SCHOOL_ID_COL => $this->SchoolId,
                    //                                     cn::GRADE_CLASS_MAPPING_GRADE_ID_COL => $ClassAssignData->{cn::TEACHER_CLASS_SUBJECT_CLASS_ID_COL}
                    //                                 ])
                    //                                 ->whereIn(cn::GRADE_CLASS_MAPPING_ID_COL,$ExistingClassIds)
                    //                                 ->pluck(cn::GRADE_CLASS_MAPPING_NAME_COL)
                    //                                 ->toArray();
                    //                     $ClassData = GradeClassMapping::where([
                    //                                     cn::GRADE_CLASS_MAPPING_CURRICULUM_YEAR_ID_COL => $this->nextCurriculumYearId,
                    //                                     cn::GRADE_CLASS_MAPPING_SCHOOL_ID_COL => $this->SchoolId,
                    //                                     cn::GRADE_CLASS_MAPPING_GRADE_ID_COL => $ClassAssignData->{cn::TEACHER_CLASS_SUBJECT_CLASS_ID_COL}
                    //                                 ])
                    //                                 ->whereIn(cn::GRADE_CLASS_MAPPING_NAME_COL,$ClassNames)
                    //                                 ->pluck(cn::GRADE_CLASS_MAPPING_ID_COL)
                    //                                 ->toArray();
                    //                     if(isset($ClassData) && !empty($ClassData)){
                    //                         $ClassIds = implode(',',$ClassData);
                    //                         TeachersClassSubjectAssign::updateOrCreate([
                    //                             cn::TEACHER_CLASS_SUBJECT_CURRICULUM_YEAR_ID_COL => $this->nextCurriculumYearId,
                    //                             cn::TEACHER_CLASS_SUBJECT_SCHOOL_ID_COL => $this->SchoolId,
                    //                             cn::TEACHER_CLASS_SUBJECT_TEACHER_ID_COL => $ClassAssignData->{cn::TEACHER_CLASS_SUBJECT_TEACHER_ID_COL},
                    //                             cn::TEACHER_CLASS_SUBJECT_CLASS_ID_COL => $ClassAssignData->{cn::TEACHER_CLASS_SUBJECT_CLASS_ID_COL},
                    //                             cn::TEACHER_CLASS_SUBJECT_SUBJECT_ID_COL => $ClassAssignData->{cn::TEACHER_CLASS_SUBJECT_SUBJECT_ID_COL},
                    //                             cn::TEACHER_CLASS_SUBJECT_CLASS_NAME_ID_COL => ($ClassIds) ? $ClassIds : null
                    //                         ],[
                    //                             cn::TEACHER_CLASS_SUBJECT_CURRICULUM_YEAR_ID_COL => $this->nextCurriculumYearId,
                    //                             cn::TEACHER_CLASS_SUBJECT_SCHOOL_ID_COL => $this->SchoolId,
                    //                             cn::TEACHER_CLASS_SUBJECT_TEACHER_ID_COL => $ClassAssignData->{cn::TEACHER_CLASS_SUBJECT_TEACHER_ID_COL},
                    //                             cn::TEACHER_CLASS_SUBJECT_CLASS_ID_COL => $ClassAssignData->{cn::TEACHER_CLASS_SUBJECT_CLASS_ID_COL},
                    //                             cn::TEACHER_CLASS_SUBJECT_SUBJECT_ID_COL => $ClassAssignData->{cn::TEACHER_CLASS_SUBJECT_SUBJECT_ID_COL},
                    //                             cn::TEACHER_CLASS_SUBJECT_CLASS_NAME_ID_COL => ($ClassIds) ? $ClassIds : null
                    //                         ]);
                    //                     }
                    //                 }
                    //             }
                    //         }else{
                    //             TeachersClassSubjectAssign::where([
                    //                 cn::TEACHER_CLASS_SUBJECT_CURRICULUM_YEAR_ID_COL => $this->nextCurriculumYearId,
                    //                 cn::TEACHER_CLASS_SUBJECT_TEACHER_ID_COL => $ClassAssignData->{cn::TEACHER_CLASS_SUBJECT_TEACHER_ID_COL},
                    //                 cn::TEACHER_CLASS_SUBJECT_ID_COL => $TeachersClassSubjectAssignId
                    //             ])->delete();
                    //         }
                    //     }
                    // }
                    // End Operation for Teacher class subject assign mapping data

                    // Update remainder update school year data table
                    if(RemainderUpdateSchoolYearData::where([
                        cn::REMAINDER_UPDATE_SCHOOL_YEAR_DATA_SCHOOL_ID_COL => $this->SchoolId,
                        cn::REMAINDER_UPDATE_SCHOOL_YEAR_DATA_CURRICULUM_YEAR_ID_COL => $this->nextCurriculumYearId
                    ])->doesntExist()){
                        RemainderUpdateSchoolYearData::Create([
                            cn::REMAINDER_UPDATE_SCHOOL_YEAR_DATA_CURRICULUM_YEAR_ID_COL => $this->nextCurriculumYearId,
                            cn::REMAINDER_UPDATE_SCHOOL_YEAR_DATA_SCHOOL_ID_COL => $this->SchoolId,
                            cn::REMAINDER_UPDATE_SCHOOL_YEAR_DATA_STATUS_COL => 'pending'
                        ]);
                    }
                    Log::info('Data Clone Completed for School Id:'.$School->{cn::SCHOOL_ID_COLS});
                }
            }
        }
        Log::info('Job Complete: Copy current curriculum year data and clone in to next curriculum year school data');
    }
}