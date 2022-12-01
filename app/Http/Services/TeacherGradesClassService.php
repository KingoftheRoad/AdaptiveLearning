<?php

namespace App\Http\Services;

use App\Traits\AIApi;
use App\Traits\Common;
use Log;
use App\Constants\DbConstant As cn;
use Illuminate\Support\Facades\Auth;
use App\Models\TeachersClassSubjectAssign;
use App\Models\ExamGradeClassMappingModel;
use App\Models\PeerGroup;
use App\Models\Exam;
use App\Models\PeerGroupMember;
use App\Models\CurriculumYearStudentMappings;

class TeacherGradesClassService
{
    use Common;
    public $TeachersClassSubjectAssign, $ExamGradeClassMappingModel, $PeerGroup, $PeerGroupMember;
    public function __construct(){
        $this->TeachersClassSubjectAssign = new TeachersClassSubjectAssign;
        $this->ExamGradeClassMappingModel = new ExamGradeClassMappingModel;
        $this->PeerGroup = new PeerGroup;
        $this->PeerGroupMember = new PeerGroupMember;
        $this->Exam = new Exam;
    }

    /**
     * USE : Get Teachers assigned grades & Class
     * Return : Teacher assigned grades array & class array
     */
    public function getTeacherAssignedGradesClass($SchoolId,$TeacherId){
        if(!empty($SchoolId) && !empty($TeacherId)){
            $Response = ['grades' => [], 'class' => []];
            $TeachersClassSubjectAssignCollection = $this->TeachersClassSubjectAssign->where([
                                                        cn::TEACHER_CLASS_SUBJECT_CURRICULUM_YEAR_ID_COL => $this->GetCurriculumYear(),
                                                        cn::TEACHER_CLASS_SUBJECT_SCHOOL_ID_COL => $SchoolId,
                                                        cn::TEACHER_CLASS_SUBJECT_TEACHER_ID_COL => $TeacherId
                                                    ])->get();
            // Find Teachers assigned grades
            $TeachersAssignGrades = $TeachersClassSubjectAssignCollection->pluck(cn::TEACHER_CLASS_SUBJECT_CLASS_ID_COL);
            if(!empty($TeachersAssignGrades)){
                $Response['grades'] = $TeachersAssignGrades->toArray();
            }

            // Find teachers assigned classes
            if(!empty($TeachersClassSubjectAssignCollection)){
                $TeachersClass = [];
                if(!empty($TeachersClassSubjectAssignCollection->pluck(cn::TEACHER_CLASS_SUBJECT_CLASS_NAME_ID_COL))){
                    foreach($TeachersClassSubjectAssignCollection->pluck(cn::TEACHER_CLASS_SUBJECT_CLASS_NAME_ID_COL) as $classes){
                        $TeachersClass[] = explode(',',$classes);
                    }
                    $Response['class'] = $this->array_flatten($TeachersClass);
                }
            }
        }
        return $Response;
    }

    /**
     * USE : Get Teachers assigned exam ids
     * Return : Exam id array
     */
    public function getAssignedTeachersExamsIds($SchoolId, $ClassIds = []){
        $ExamIds = [];
        $PeerGroupIds = [];
        
        // Find Peer Group ids created by teachers
        $PeerGroupIds = $this->GetTeachersPeerGroupIds(Auth::user()->{cn::USERS_ID_COL}, Auth::user()->{cn::USERS_SCHOOL_ID_COL});

        $ExamGradeClassData = $this->ExamGradeClassMappingModel->where(cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_SCHOOL_ID_COL,$SchoolId)
                                ->where(cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_CURRICULUM_YEAR_ID_COL,$this->GetCurriculumYear())
                                ->where(function($Query) use($ClassIds, $PeerGroupIds){
                                    $Query->whereIn(cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_CLASS_ID_COL,$ClassIds)
                                    ->orWhereIn(cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_PEER_GROUP_ID_COL,$PeerGroupIds);
                                })
                                ->pluck(cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_EXAM_ID_COL);
        if(!empty($ExamGradeClassData)){
            $ExamIds = $ExamGradeClassData->toArray();
        }
        return $ExamIds;
    }

    /**
     * USE : Get student self learning test ids
     */
    public function GetStudentSelfLearningTestIds($SchoolId, $ClassIds = []){
        $StudentSelfLearningExamIds = [];

        $studentIdsArray = CurriculumYearStudentMappings::where([
            cn::CURRICULUM_YEAR_STUDENT_MAPPING_USER_ID_COL => $this->GetCurriculumYear(),
            cn::CURRICULUM_YEAR_STUDENT_MAPPING_SCHOOL_ID_COL => $SchoolId,
        ])
        ->whereIn(cn::CURRICULUM_YEAR_STUDENT_MAPPING_CLASS_ID_COL,$ClassIds)
        ->pluck(cn::CURRICULUM_YEAR_STUDENT_MAPPING_USER_ID_COL)
        ->toArray();


        $StudentSelfLearningExamIds = $this->Exam->whereHas('user', function($query) use($SchoolId, $ClassIds, $studentIdsArray){
                                        //$query->whereIn('class_id',$ClassIds)
                                        $query->whereIn('id',$studentIdsArray)
                                        ->where([
                                            'role_id' => cn::STUDENT_ROLE_ID,
                                            'school_id' => $SchoolId
                                        ]);
                                    })
                                    ->where(cn::EXAM_CURRICULUM_YEAR_ID_COL,$this->GetCurriculumYear())
                                    ->where('created_by_user','student')
                                    ->pluck('id');
        if($StudentSelfLearningExamIds->isNotEmpty()){
            return $StudentSelfLearningExamIds = $StudentSelfLearningExamIds->toArray();
        }else{
            return array();
        }
    }

    /**
     * USE : Find Teachers Peer Group Ids
     */
    public function GetTeachersPeerGroupIds($TeacherId, $SchoolId){
        $PeerGroupIds = [];
        $PeerGroupIds = PeerGroup::where([
                            cn::PEER_GROUP_CREATED_BY_USER_ID_COL => $TeacherId,
                            cn::PEER_GROUP_SCHOOL_ID_COL => $SchoolId,
                            cn::PEER_GROUP_CURRICULUM_YEAR_ID_COL => $this->GetCurriculumYear()
                        ])->pluck(cn::PEER_GROUP_ID_COL);
        if(!empty($PeerGroupIds)){
            $PeerGroupIds = $PeerGroupIds->toArray();
        }
        return $PeerGroupIds;
    }

    public function GetSchoolBasedPeerGroupIds($SchoolId){
        $PeerGroupIds = [];
        $PeerGroupIds = PeerGroup::where([
            cn::PEER_GROUP_SCHOOL_ID_COL => $SchoolId,
            cn::PEER_GROUP_CURRICULUM_YEAR_ID_COL => $this->GetCurriculumYear()
        ])
        ->pluck(cn::PEER_GROUP_ID_COL);
        if(!empty($PeerGroupIds)){
            $PeerGroupIds = $PeerGroupIds->toArray();
        }
        return $PeerGroupIds;
    }
}