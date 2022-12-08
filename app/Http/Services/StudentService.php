<?php

namespace App\Http\Services;

use App\Traits\AIApi;
use App\Traits\Common;
use Log;
use App\Constants\DbConstant As cn;
use Illuminate\Support\Facades\Auth;
use App\Models\ExamGradeClassMappingModel;
use App\Models\PeerGroup;
use App\Models\PeerGroupMember;
use App\Models\User;

class StudentService {
    use Common;

    public $ExamGradeClassMappingModel, $PeerGroup, $PeerGroupMember, $User;
    public function __construct(){
        $this->ExamGradeClassMappingModel = new ExamGradeClassMappingModel;
        $this->PeerGroup = new PeerGroup;
        $this->PeerGroupMember = new PeerGroupMember;
        $this->User = new User;
    }

    /**
     * USE : Get Student assigned exam id
     */
    public function GetStudentAssignedExamsIds($StudentId){
        $StudentAssignedExamIds = [];
        if(!empty($StudentId)){
            $Student = $this->User->find($StudentId);
            if(!empty($Student)){
                $StudentAssignedPeerGroupIds =    $this->PeerGroupMember->where([
                                                    cn::PEER_GROUP_MEMBERS_CURRICULUM_YEAR_ID_COL => $this->GetCurriculumYear(),
                                                    'member_id' => $Student->id,
                                                    'status' => 1
                                                ])->pluck('peer_group_id');
                if(!empty($StudentAssignedPeerGroupIds)){
                    $StudentAssignedPeerGroupIds = $StudentAssignedPeerGroupIds->toArray();
                }
                $StudentAssignedExamIds =  $this->ExamGradeClassMappingModel->where([
                                                cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_CURRICULUM_YEAR_ID_COL => $this->GetCurriculumYear(),
                                                'school_id' => $Student->school_id,
                                                'status' => 'publish'
                                            ])
                                            ->where(function($query) use($Student, $StudentAssignedPeerGroupIds){
                                                $query->orWhere('grade_id',$Student->CurriculumYearData['grade_id'])
                                                ->orWhere('class_id',$Student->CurriculumYearData['class_id'])
                                                ->orWhereIn('peer_group_id',$StudentAssignedPeerGroupIds);
                                            })
                                            ->pluck('exam_id');
                if(!$StudentAssignedExamIds->isEmpty()){
                    $StudentAssignedExamIds = array_unique($StudentAssignedExamIds->toArray());
                }
            }
        }
        return $StudentAssignedExamIds;
    }
}