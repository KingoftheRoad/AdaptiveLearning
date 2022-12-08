<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use App\Http\Controllers\CommonController;
use Illuminate\Http\Request;
use App\Traits\Common;
use App\Traits\ResponseFormat;
use App\Constants\DbConstant As cn;
use Exception;
use Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use App\Models\School;
use App\Models\Grades;
use App\Models\ClassModel;
use App\Models\Exam;
use App\Models\User;
use App\Models\AttemptExams;
use App\Models\Question;
use App\Models\Answer;
use App\Models\Nodes;
use App\Models\GradeClassMapping;
use App\Models\GradeSchoolMappings;
use App\Models\TeachersClassSubjectAssign;
use App\Models\PreConfigurationDiffiltyLevel;
use App\Models\PeerGroup;
use App\Models\ExamGradeClassMappingModel;
use App\Models\PeerGroupMember;
use App\Helpers\Helper;
use App\Http\Services\TeacherGradesClassService;
use App\Models\ExamSchoolMapping;
use App\Http\Services\AIApiService;
use DB;

class ClassTestReportController extends Controller
{
    use Common, ResponseFormat;

    public $TeacherGradesClassService, $ExamSchoolMapping, $ExamGradeClassMappingModel, $CommonController;
    protected $AIApiService;

    public function __construct(){
        $this->TeacherGradesClassService = new TeacherGradesClassService;
        $this->ExamSchoolMapping = new ExamSchoolMapping;
        $this->ExamGradeClassMappingModel = new ExamGradeClassMappingModel;
        $this->CommonController = new CommonController;
        $this->AIApiService = new AIApiService();
    }

    /**
     * USE : Get Student Performance Graph
     */
    public function getPerformanceGraphCurrentStudent(Request $request){
        $response = [];
        $PreConfigurationDifficultyLevel = array();
        $PreConfigurationDifficultyLevelData = PreConfigurationDiffiltyLevel::get()->toArray();
        if(isset($PreConfigurationDifficultyLevelData)){
            $PreConfigurationDifficultyLevel = array_column($PreConfigurationDifficultyLevelData,cn::PRE_CONFIGURE_DIFFICULTY_TITLE_COL,cn::PRE_CONFIGURE_DIFFICULTY_DIFFICULTY_LEVEL_COL);
        }
        if(!empty($request->student_id) && !empty($request->exam_id)){
            $AttemptExamData = AttemptExams::where(cn::ATTEMPT_EXAMS_STUDENT_STUDENT_ID,$request->student_id)->where(cn::ATTEMPT_EXAMS_EXAM_ID,$request->exam_id)->first();
            $ExamData = Exam::find($request->exam_id);
            if(isset($ExamData) && !empty($ExamData)){
                if(!empty($ExamData->question_ids)){
                    $questionIds = explode(',',$ExamData->question_ids);
                    $QuestionList = Question::with('answers')->whereIn(cn::QUESTION_TABLE_ID_COL,$questionIds)->get();
                    if(isset($QuestionList) && !empty($QuestionList)){
                        foreach($QuestionList as $QuestionKey => $question){
                            $countQuestions = count($QuestionList);
                            $Answerdetail = $question->answers;
                            if(isset($AttemptExamData['question_answers'])){
                                $filterattempQuestionAnswer = array_filter(json_decode($AttemptExamData['question_answers']), function ($var) use($question){
                                    if($var->question_id == $question['id']){
                                        return $var ?? [];
                                    }
                                });
                            }
                            if(isset($filterattempQuestionAnswer) && !empty($filterattempQuestionAnswer)){
                                foreach($filterattempQuestionAnswer as $fanswer){
                                    // Save the question result
                                    if($fanswer->answer == $Answerdetail->{'correct_answer_'.$fanswer->language}){
                                        $response['student_results_list'][] = true;
                                    }else{
                                        $response['student_results_list'][] = false;
                                    }

                                    // Get Questions difficulty Level value
                                    if(isset($PreConfigurationDifficultyLevel) && !empty($PreConfigurationDifficultyLevel) && isset($PreConfigurationDifficultyLevel[$question->{cn::QUESTION_DIFFICULTY_LEVEL_COL}]) && !empty($PreConfigurationDifficultyLevel[$question->{cn::QUESTION_DIFFICULTY_LEVEL_COL}])){
                                        $response['questions_difficulties_list'][] = number_format($PreConfigurationDifficultyLevel[$question->{cn::QUESTION_DIFFICULTY_LEVEL_COL}], 4, '.', '');
                                    }else{
                                        $response['questions_difficulties_list'][] = 0;
                                    }
                                }
                            }
                        }
                    }
                }
            }
            $accuracy = Helper::getAccuracy($request->exam_id, $request->student_id);
            $response['student_ability'] = '';
            if(!empty($accuracy)){
                $getAbility = Helper::getAbility($accuracy);
                $response['student_ability'] = $getAbility;
            }

            // Return result for performance graph
            return $this->sendResponse($response);
        }
    }


    public function getExamGroupGradeClassList(Request $request){
        $Response = [];        
        if($this->isTeacherLogin()){
            $TeacherGradeClass = $this->TeacherGradesClassService->getTeacherAssignedGradesClass(Auth::user()->{cn::USERS_SCHOOL_ID_COL}, Auth::user()->{cn::USERS_ID_COL});
            if(!empty($TeacherGradeClass)){
                $AvailableGradesIds =   $this->ExamGradeClassMappingModel->where([
                                            cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_CURRICULUM_YEAR_ID_COL => $this->GetCurriculumYear(),
                                            cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_EXAM_ID_COL => $request->exam_id,
                                            cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_SCHOOL_ID_COL => Auth::user()->{cn::USERS_SCHOOL_ID_COL},
                                            cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_STATUS_COL => 'publish'
                                        ])
                                        ->whereIn(cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_GRADE_ID_COL,$TeacherGradeClass['grades'])
                                        ->pluck(cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_GRADE_ID_COL)->toArray();
                if(!empty($AvailableGradesIds)){
                    $GradeData = Grades::whereIn(cn::GRADES_ID_COL,$AvailableGradesIds)->get()->toArray();
                    $Response['grades_list'] = $GradeData ?? [];
                }
                $AvailableClassIds =    $this->ExamGradeClassMappingModel->where([
                                            cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_CURRICULUM_YEAR_ID_COL => $this->GetCurriculumYear(),
                                            cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_EXAM_ID_COL => $request->exam_id,
                                            cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_SCHOOL_ID_COL => Auth::user()->{cn::USERS_SCHOOL_ID_COL},
                                            cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_STATUS_COL => 'publish'
                                        ])
                                        ->whereIn(cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_CLASS_ID_COL,$TeacherGradeClass['class'])
                                        ->pluck(cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_CLASS_ID_COL)->toArray();
                if(!empty($AvailableClassIds)){
                    $ClassData =    GradeClassMapping::with('grade')
                                    ->whereIn(cn::GRADE_CLASS_MAPPING_ID_COL,$AvailableClassIds)
                                    ->where([
                                        cn::GRADE_CLASS_MAPPING_CURRICULUM_YEAR_ID_COL => $this->GetCurriculumYear(),
                                        cn::GRADE_CLASS_MAPPING_STATUS_COL => 'active'
                                    ])
                                    ->get()->toArray();
                    $Response['class_list'] = $ClassData ?? [];
                }

                $TeacherPeerGroupIds = $this->TeacherGradesClassService->GetTeachersPeerGroupIds(Auth::user()->{cn::USERS_ID_COL}, Auth::user()->{cn::USERS_SCHOOL_ID_COL});
                $AvailablePeerGroupIds =    $this->ExamGradeClassMappingModel->whereIn(cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_PEER_GROUP_ID_COL,$TeacherPeerGroupIds)
                                            ->where([
                                                cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_CURRICULUM_YEAR_ID_COL => $this->GetCurriculumYear(),
                                                cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_EXAM_ID_COL => $request->exam_id,
                                                cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_SCHOOL_ID_COL => Auth::user()->{cn::USERS_SCHOOL_ID_COL},
                                                cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_STATUS_COL => 'publish'
                                            ])
                                            ->pluck(cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_PEER_GROUP_ID_COL)->toArray();
                if(!empty($AvailablePeerGroupIds)){
                    $PeerGroupData =    PeerGroup::select(cn::PEER_GROUP_ID_COL,cn::PEER_GROUP_GROUP_NAME_COL)
                                        ->where(cn::PEER_GROUP_CURRICULUM_YEAR_ID_COL,$this->GetCurriculumYear())
                                        ->whereIn(cn::PEER_GROUP_ID_COL,$AvailablePeerGroupIds->toArray())
                                        ->get()->toArray();
                    $Response['peer_group_list'] = $PeerGroupData ?? [];
                }                
            }
        }else if($this->isSchoolLogin() || $this->isPrincipalLogin()){
            $peerGroupDataArray = array();
            $examData = Exam::find($request->exam_id);
            if(!empty($examData)){
                if(!empty($examData->peer_group_ids)){
                    $peerGroupIds = explode(',',$examData->peer_group_ids);
                    if(!empty($peerGroupIds)){
                        foreach($peerGroupIds as $peerGroupId){
                            $peerGroup = PeerGroup::find($peerGroupId);
                            $peerGroupData = [
                                'id' => $peerGroupId,
                                'group_name' => $peerGroup->group_name
                            ];
                        }
                    }
                    $peerGroupDataArray = $this->CommonController->getRoleBasedPeerGroupData($peerGroupIds);
                    $Response['peer_group_list'] = $peerGroupDataArray;
                }else{
                    $AvailableGradesIds =   $this->ExamGradeClassMappingModel->where([
                                                cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_CURRICULUM_YEAR_ID_COL => $this->GetCurriculumYear(),
                                                cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_SCHOOL_ID_COL => Auth::user()->{cn::USERS_SCHOOL_ID_COL},
                                                cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_EXAM_ID_COL => $request->exam_id,
                                                cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_STATUS_COL => 'publish'
                                            ])
                                            ->pluck(cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_GRADE_ID_COL);
                    if(!empty($AvailableGradesIds)){
                        $GradeData = Grades::whereIn(cn::GRADES_ID_COL,$AvailableGradesIds)->get()->toArray();
                        $Response['grades_list'] = $GradeData ?? [];
                    }

                    $AvailableClassIds =    $this->ExamGradeClassMappingModel->where([
                                                cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_CURRICULUM_YEAR_ID_COL => $this->GetCurriculumYear(),
                                                cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_SCHOOL_ID_COL => Auth::user()->{cn::USERS_SCHOOL_ID_COL},
                                                cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_EXAM_ID_COL => $request->exam_id,
                                                cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_STATUS_COL => 'publish'
                                            ])
                                            ->pluck(cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_CLASS_ID_COL);
                    if(!empty($AvailableClassIds)){
                        $ClassData = GradeClassMapping::with('grade')
                                    ->whereIn(cn::GRADE_CLASS_MAPPING_ID_COL,$AvailableClassIds)
                                    ->where([
                                        cn::GRADE_CLASS_MAPPING_CURRICULUM_YEAR_ID_COL => $this->GetCurriculumYear(),
                                        cn::GRADE_CLASS_MAPPING_SCHOOL_ID_COL => Auth::user()->{cn::USERS_SCHOOL_ID_COL},
                                        cn::GRADE_CLASS_MAPPING_STATUS_COL => 'active'
                                    ])
                                    ->get()->toArray();
                        $Response['class_list'] = $ClassData ?? [];
                    }
                }
            }
        }else if($this->isAdmin()){
            $peerGroupDataArray = array();
            $examData = Exam::find($request->exam_id);
            if(isset($request->exam_school_id) && !empty($request->exam_school_id)){
                $getExamId = Exam::where(cn::EXAM_TABLE_PARENT_EXAM_ID_COLS,$request->exam_id)
                                ->where(cn::EXAM_TABLE_SCHOOL_COLS,$request->exam_school_id)
                                ->pluck(cn::EXAM_TABLE_ID_COLS)
                                ->toArray();
                if(isset($getExamId) && !empty($getExamId)){
                    $examData = Exam::find($getExamId[0]);
                }
            }
             
            $examId = $examData->{cn::EXAM_TABLE_ID_COLS};
            if(!empty($examData)){
                // if(!empty($examData->{cn::EXAM_TABLE_PARENT_EXAM_ID_COLS}) && $examData->{cn::EXAM_TABLE_CREATED_BY_USER_COL} == 'super_admin'){
                //     $childExamsIds = Exam::where(cn::EXAM_TABLE_PARENT_EXAM_ID_COLS,$examData->{cn::EXAM_TABLE_ID_COLS})->pluck(cn::EXAM_TABLE_ID_COLS)->toArray();
                //     $childSchoolIds = Exam::where(cn::EXAM_TABLE_PARENT_EXAM_ID_COLS,$examData->{cn::EXAM_TABLE_ID_COLS})->pluck(cn::EXAM_TABLE_SCHOOL_COLS)->toArray();
                //     // echo "<pre>";print_r($childSchoolIds);die;
                //     $schoolList = array();
                //     if(isset($childSchoolIds) && !empty($childSchoolIds)){
                //         foreach($childSchoolIds as $key => $value){
                //             $schoolIds = School::where(cn::SCHOOL_ID_COLS,$value)->where(cn::SCHOOL_SCHOOL_STATUS,'active')->first();
                //             if(isset($schoolIds) && !empty($schoolIds)){
                //                 if($schoolIds->school_name_en != ""){
                //                     $school_name = $this->decrypt($schoolIds->school_name_en);
                //                 }else{
                //                     $school_name = $schoolIds->school_name;
                //                 }
                //                 array_push($schoolList,array('id'=>$schoolIds->id,'name'=>$school_name));
                //             }
                //         }
                        
                //     }
                //     $Response['school_List'] = $schoolList;

                //     if(!empty($examData->peer_group_ids)){
                //         $peerGroupIds = explode(',',$examData->peer_group_ids);
                //         if(!empty($peerGroupIds)){
                //             foreach($peerGroupIds as $peerGroupId){
                //                 $peerGroup = PeerGroup::find($peerGroupId);
                //                 $peerGroupData = [
                //                     'id' => $peerGroupId,
                //                     'group_name' => $peerGroup->group_name
                //                 ];
                //             }
                //         }
                //         $peerGroupDataArray = $this->CommonController->getRoleBasedPeerGroupData($peerGroupIds);
                //         $Response['peer_group_list'] = $peerGroupDataArray;
                //     }
                //     $AvailableGradesIds = $this->ExamGradeClassMappingModel->whereIn(cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_EXAM_ID_COL,$childExamsIds)
                //                             ->where([
                //                                 cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_CURRICULUM_YEAR_ID_COL => $this->GetCurriculumYear(),
                //                                 cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_STATUS_COL => 'publish',
                //                                 'school_id' => $schoolList[0]
                //                             ])
                //                             ->pluck(cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_GRADE_ID_COL);
                //     if(!empty($AvailableGradesIds)){
                //         $GradeData = Grades::whereIn(cn::GRADES_ID_COL,$AvailableGradesIds)->get()->toArray();
                //         $Response['grades_list'] = $GradeData ?? [];
                //     }
                    
                //     $AvailableClassIds = $this->ExamGradeClassMappingModel->whereIn(cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_EXAM_ID_COL,$childExamsIds)
                //                             ->where([
                //                                 cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_CURRICULUM_YEAR_ID_COL => $this->GetCurriculumYear(),
                //                                 cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_STATUS_COL => 'publish'
                //                             ])
                //                             ->pluck(cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_CLASS_ID_COL);
                //     if(!empty($AvailableClassIds)){
                //         $ClassData = GradeClassMapping::with('grade')
                //                     ->whereIn(cn::GRADE_CLASS_MAPPING_ID_COL,$AvailableClassIds)
                //                     ->where([
                //                         cn::GRADE_CLASS_MAPPING_CURRICULUM_YEAR_ID_COL => $this->GetCurriculumYear(),
                //                         cn::GRADE_CLASS_MAPPING_STATUS_COL => 'active'
                //                     ])
                //                     ->get()->toArray();
                //         $Response['class_list'] = $ClassData ?? [];
                //     }
                // }else{
                    $schoolList = array();
                    $schoolIds = $examData->{cn::EXAM_TABLE_SCHOOL_COLS};
                    if(isset($schoolIds) && !empty($schoolIds)){
                        $childSchoolIds = explode(',',$schoolIds);
                        if(isset($childSchoolIds) && !empty($childSchoolIds)){
                            foreach($childSchoolIds as $key => $value){
                                $schoolIds = School::where(cn::SCHOOL_ID_COLS,$value)->where(cn::SCHOOL_SCHOOL_STATUS,'active')->first();
                                if(isset($schoolIds) && !empty($schoolIds)){
                                    if($schoolIds->school_name_en != ""){
                                        $school_name = $this->decrypt($schoolIds->school_name_en);
                                    }else{
                                        $school_name = $schoolIds->school_name;
                                    }
                                    array_push($schoolList,array('id'=>$schoolIds->id,'name'=>$school_name));
                                }
                            }
                        }
                    }
                    $Response['school_List'] = $schoolList;

                    $AvailablePeerGroupIds = $this->ExamGradeClassMappingModel->where([
                                                cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_CURRICULUM_YEAR_ID_COL => $this->GetCurriculumYear(),
                                                cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_EXAM_ID_COL => $examId,
                                                cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_STATUS_COL => 'publish'
                                            ])
                                            ->whereNotNull(cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_PEER_GROUP_ID_COL)
                                            ->pluck(cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_PEER_GROUP_ID_COL)
                                            ->toArray();
                    if(isset($request->exam_school_id) && !empty($request->exam_school_id)){
                        $AvailablePeerGroupIds = $this->ExamGradeClassMappingModel->where([
                                                    cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_CURRICULUM_YEAR_ID_COL => $this->GetCurriculumYear(),
                                                    cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_EXAM_ID_COL => $examId,
                                                    cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_STATUS_COL => 'publish',
                                                    cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_SCHOOL_ID_COL => $request->exam_school_id
                                                ])
                                                ->whereNotNull(cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_PEER_GROUP_ID_COL)
                                                ->pluck(cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_PEER_GROUP_ID_COL)->toArray();
                    }
                    if(!empty($AvailablePeerGroupIds)){
                            foreach($AvailablePeerGroupIds as $peerGroupId){
                                $peerGroup = PeerGroup::find($peerGroupId);
                                $peerGroupData = [
                                    'id' => $peerGroupId,
                                    'group_name' => $peerGroup->group_name
                                ];
                            }
                        $peerGroupDataArray = $this->CommonController->getRoleBasedPeerGroupData($AvailablePeerGroupIds);
                        $Response['peer_group_list'] = $peerGroupDataArray;
                    }else{
                        $AvailableGradesIds =   $this->ExamGradeClassMappingModel->where([
                                                    cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_CURRICULUM_YEAR_ID_COL => $this->GetCurriculumYear(),
                                                    cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_EXAM_ID_COL => $examId,
                                                    cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_STATUS_COL => 'publish',
                                                    cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_SCHOOL_ID_COL => (!empty($request->exam_school_id)) ? $request->exam_school_id : $schoolList[0]['id']
                                                ])
                                                ->pluck(cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_GRADE_ID_COL);
                        if(!empty($AvailableGradesIds)){
                            $GradeData = Grades::whereIn(cn::GRADES_ID_COL,$AvailableGradesIds)->get()->toArray();
                            $Response['grades_list'] = $GradeData ?? [];
                        }
                        $AvailableClassIds =    $this->ExamGradeClassMappingModel->where([
                                                    cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_CURRICULUM_YEAR_ID_COL => $this->GetCurriculumYear(),
                                                    cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_EXAM_ID_COL => $examId,
                                                    cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_STATUS_COL => 'publish',
                                                    // 'school_id' => $schoolList[0]['id']
                                                    cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_SCHOOL_ID_COL => (!empty($request->exam_school_id)) ? $request->exam_school_id : $schoolList[0]['id']
                                                ])
                                                ->pluck(cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_CLASS_ID_COL);
                        if(!empty($AvailableClassIds)){
                            $ClassData = GradeClassMapping::with('grade')
                                        ->whereIn(cn::GRADE_CLASS_MAPPING_ID_COL,$AvailableClassIds)
                                        ->where([
                                            cn::GRADE_CLASS_MAPPING_CURRICULUM_YEAR_ID_COL => $this->GetCurriculumYear(),
                                            cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_SCHOOL_ID_COL => (!empty($request->exam_school_id)) ? $request->exam_school_id : $schoolList[0]['id'],
                                            cn::GRADE_CLASS_MAPPING_STATUS_COL => 'active'
                                        ])
                                        ->get()->toArray();                                        
                            $Response['class_list'] = $ClassData ?? [];
                        }
                    //}
                }
            }
        }
        return $this->sendResponse($Response);
    }

    /**
     * USE : Class test report, correct and incorrect (table version, correct/wrong)
     */
    public function ClassTestResultCorrectIncorrectAnswers(Request $request){
        $isRemainderEnable = false;
        if(isset($request->isLoggedIn) && !empty($request->isLoggedIn)){
            if($this->isSchoolLogin()){
                $isRemainderEnable = Helper::isRemainderEnabledCheck();
            }
        }

        $getClasses = '';
        $GradeList = [];
        $ResultList = [];
        $QuestionAnswerData = [];
        $SchoolList = School::all();
        $studentList = array();
        $PeerGroupList = [];
        $group_id = '';
        $examType = '';
        if($this->isTeacherLogin()){
            $schoolId = $this->isTeacherLogin();
            if(isset($request->group_id) && !empty($request->group_id)){
                $PeerGroupIds = $this->TeacherGradesClassService->GetTeachersPeerGroupIds(Auth::user()->{cn::USERS_ID_COL}, Auth::user()->{cn::USERS_SCHOOL_ID_COL});
                if(!empty($PeerGroupIds)){
                    $ExamPeerGroupIds = ExamGradeClassMappingModel::where([
                                            cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_CURRICULUM_YEAR_ID_COL => $this->GetCurriculumYear(),
                                            cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_EXAM_ID_COL => $request->exam_id,
                                            cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_STATUS_COL => 'publish'
                                        ])
                                        ->whereIn(cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_PEER_GROUP_ID_COL,$PeerGroupIds)
                                        ->pluck(cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_PEER_GROUP_ID_COL);
                    if(!empty($ExamPeerGroupIds)){
                        $PeerGroupList = PeerGroup::whereIn(cn::PEER_GROUP_ID_COL,$ExamPeerGroupIds->toArray())
                                        ->where([
                                            cn::PEER_GROUP_CURRICULUM_YEAR_ID_COL => $this->GetCurriculumYear(),
                                            cn::PEER_GROUP_STATUS_COL => 1
                                        ])->get();
                    }
                    
                    // Find Peer Group Memeber selected by peer group
                    $PeerGroupMemberIds = PeerGroupMember::where([
                                            cn::PEER_GROUP_MEMBERS_CURRICULUM_YEAR_ID_COL => $this->GetCurriculumYear(),
                                            cn::PEER_GROUP_MEMBERS_PEER_GROUP_ID_COL => $request->group_id,
                                            cn::PEER_GROUP_MEMBERS_STATUS_COL => 1
                                        ])
                                        ->pluck(cn::PEER_GROUP_MEMBERS_MEMBER_ID_COL);
                    if(!empty($PeerGroupMemberIds)){
                        $PeerGroupMemberIds = $PeerGroupMemberIds->toArray();
                        $studentList = User::with('grades')
                                        ->whereIn(cn::USERS_ID_COL,$PeerGroupMemberIds)
                                        ->where([
                                            cn::USERS_SCHOOL_ID_COL => Auth::user()->{cn::USERS_SCHOOL_ID_COL},
                                            cn::USERS_ROLE_ID_COL => cn::STUDENT_ROLE_ID
                                        ])
                                        ->pluck(cn::USERS_ID_COL)
                                        ->toArray();
                    }
                }
            }

            if(isset($request->grade_id) && !empty($request->class_type_id)){  
                //get All Teacher Assigned Classes 
                $getClasses = $this->getClassesByRoles();
                
                $schoolId = $this->isTeacherLogin();
                $AvailableGradeIds =    $this->ExamGradeClassMappingModel->where([
                                            cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_CURRICULUM_YEAR_ID_COL => $this->GetCurriculumYear(),
                                            cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_EXAM_ID_COL => $request->exam_id,
                                            cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_SCHOOL_ID_COL => $schoolId,
                                            cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_STATUS_COL => 'publish'
                                        ])
                                        ->pluck(cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_GRADE_ID_COL)->toArray();
                if(!empty($request->grade_id)){
                    $AvailableGradeIds = (is_array($request->grade_id)) ? $request->grade_id : [$request->grade_id];
                }                                        
                // $GradeList =    TeachersClassSubjectAssign::with('getClass')
                //                 ->where([
                //                     cn::TEACHER_CLASS_SUBJECT_CURRICULUM_YEAR_ID_COL => $this->GetCurriculumYear(),
                //                     cn::TEACHER_CLASS_SUBJECT_TEACHER_ID_COL => Auth()->user()->{cn::USERS_ID_COL},
                //                 ])
                //                 ->whereIn( cn::TEACHER_CLASS_SUBJECT_CLASS_ID_COL,$AvailableGradeIds)
                //                 ->pluck(cn::TEACHER_CLASS_SUBJECT_CLASS_ID_COL)->toArray();

                // $GradeList = Grades::whereIn(cn::GRADES_ID_COL,$GradeList)->get();
                $GradeList = Grades::whereIn(cn::GRADES_ID_COL,$AvailableGradeIds)->get();
                $gradeClassId = array();
                $gradesListId = TeachersClassSubjectAssign::where([
                                    cn::TEACHER_CLASS_SUBJECT_CURRICULUM_YEAR_ID_COL => $this->GetCurriculumYear(),
                                    cn::TEACHER_CLASS_SUBJECT_TEACHER_ID_COL => Auth()->user()->{cn::USERS_ID_COL}
                                ])->pluck(cn::TEACHER_CLASS_SUBJECT_CLASS_ID_COL)->toArray();
                $gradeClass =   TeachersClassSubjectAssign::where([
                                    cn::TEACHER_CLASS_SUBJECT_CURRICULUM_YEAR_ID_COL => $this->GetCurriculumYear(),
                                    cn::TEACHER_CLASS_SUBJECT_TEACHER_ID_COL => Auth()->user()->{cn::USERS_ID_COL}
                                ])
                                ->pluck(cn::TEACHER_CLASS_SUBJECT_CLASS_NAME_ID_COL)
                                ->toArray();
                
                if(isset($gradeClass) && !empty($gradeClass)){
                    $gradeClass = implode(',', $gradeClass);
                    $gradeClassId = explode(',',$gradeClass);
                }
                
                // $studentList =  User::whereIn(cn::USERS_GRADE_ID_COL,$gradesListId)
                //                 ->whereIn(cn::USERS_CLASS_ID_COL,$gradeClassId)
                //                 ->where(cn::USERS_SCHOOL_ID_COL,Auth::user()->{cn::USERS_SCHOOL_ID_COL})
                //                 ->where(cn::USERS_ROLE_ID_COL,cn::STUDENT_ROLE_ID)
                //                 ->with('grades')
                //                 ->pluck(cn::USERS_ID_COL)
                //                 ->toArray();

                $studentList =  User::with('grades')
                                ->where([
                                    cn::USERS_SCHOOL_ID_COL => Auth::user()->{cn::USERS_SCHOOL_ID_COL},
                                    cn::USERS_ROLE_ID_COL => cn::STUDENT_ROLE_ID
                                ])
                                ->get()
                                ->whereIn('CurriculumYearGradeId',$gradesListId)
                                ->whereIn('CurriculumYearClassId',$gradeClassId)
                                ->pluck(cn::USERS_ID_COL)
                                ->toArray();
            }
        }
        
        if($this->isSchoolLogin() || $this->isPrincipalLogin()){
            $getClasses = $this->getClassesByRoles();

            $schoolId = '';
            if($this->isSchoolLogin()){
                $schoolId = $this->isSchoolLogin();
            }
            if($this->isPrincipalLogin()){
                $schoolId = $this->isPrincipalLogin();
            }
            $AvailableGradeIds =    $this->ExamGradeClassMappingModel->where([
                                        cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_CURRICULUM_YEAR_ID_COL => $this->GetCurriculumYear(),
                                        cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_EXAM_ID_COL => $request->exam_id,
                                        cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_SCHOOL_ID_COL => $schoolId,
                                        cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_STATUS_COL => 'publish'
                                    ])
                                    ->pluck(cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_GRADE_ID_COL)->toArray();
            if(!empty($request->grade_id)){
                $AvailableGradeIds = (is_array($request->grade_id)) ? $request->grade_id : [$request->grade_id];
            }
            $GradeMapping = GradeSchoolMappings::with('grades')
                            ->where([
                                cn::GRADES_MAPPING_CURRICULUM_YEAR_ID_COL => $this->GetCurriculumYear(),
                                cn::GRADES_MAPPING_SCHOOL_ID_COL => $schoolId
                            ])
                            ->whereIn(cn::GRADES_MAPPING_GRADE_ID_COL,$AvailableGradeIds)
                            ->get()
                            ->pluck(cn::GRADES_MAPPING_GRADE_ID_COL);
            if(!empty($GradeMapping->toArray())){
                $GradeList = Grades::whereIn(cn::GRADES_ID_COL,$GradeMapping->toArray())->get();
            }
            $studentList =  User::where([
                                cn::USERS_ROLE_ID_COL => cn::STUDENT_ROLE_ID,
                                cn::USERS_SCHOOL_ID_COL => Auth::user()->{cn::USERS_SCHOOL_ID_COL}
                            ])                            
                            ->with('grades')
                            ->pluck(cn::USERS_ID_COL)
                            ->toArray();
        }

        $grade_id = '';
        $class_type_id = array();
        $filter = 0;
        $GradeClassListData = array();
        $schoolList = array();
        if($this->isAdmin() && isset($request->exam_id) && !empty($request->exam_id)){

            $examData = Exam::find($request->exam_id);
            if(!empty($examData->{cn::EXAM_TABLE_PARENT_EXAM_ID_COLS}) && $examData->{cn::EXAM_TABLE_CREATED_BY_USER_COL} == 'super_admin'){
                $childExamsIds = Exam::where(cn::EXAM_TABLE_PARENT_EXAM_ID_COLS,$examData->{cn::EXAM_TABLE_ID_COLS})
                                ->pluck(cn::EXAM_TABLE_ID_COLS)->toArray();
                $childSchoolIds = Exam::where(cn::EXAM_TABLE_PARENT_EXAM_ID_COLS,$examData->{cn::EXAM_TABLE_ID_COLS})
                                ->pluck(cn::EXAM_TABLE_SCHOOL_COLS)->toArray();
                $schoolList = array();
                if(isset($childSchoolIds) && !empty($childSchoolIds)){
                    foreach($childSchoolIds as $key => $value){
                        $schoolIds = School::where([
                                        cn::SCHOOL_ID_COLS => $value,
                                        cn::SCHOOL_SCHOOL_STATUS => 'active'
                                    ])->first();
                        if(isset($schoolIds) && !empty($schoolIds)){
                            if($schoolIds->school_name_en != ""){
                                $school_name = $this->decrypt($schoolIds->school_name_en);
                            }else{
                                $school_name = $schoolIds->school_name;
                            }
                            array_push($schoolList,array('id'=>$schoolIds->id,'name'=>$school_name));
                        }
                    }
                }
            }else{
                $schoolList = array();
                $schoolIds = $examData->{cn::EXAM_TABLE_SCHOOL_COLS};
                if(isset($schoolIds) && !empty($schoolIds)){
                    $childSchoolIds = explode(',',$schoolIds);
                    if(isset($childSchoolIds) && !empty($childSchoolIds)){
                        foreach ($childSchoolIds as $key => $value) {
                            $schoolIds = School::where(cn::SCHOOL_ID_COLS,$value)->where(cn::SCHOOL_SCHOOL_STATUS,'active')->first();
                            if(isset($schoolIds) && !empty($schoolIds)){
                                if($schoolIds->school_name_en != ""){
                                    $school_name = $this->decrypt($schoolIds->school_name_en);
                                }else{
                                    $school_name = $schoolIds->school_name;
                                }
                                array_push($schoolList,array('id' => $schoolIds->id,'name' => $school_name));
                            }
                        }
                    }
                }
            }
        }

        if(isset($request->grade_id) && !empty($request->grade_id)){
            $grade_id = $request->grade_id;
            $filter = 1;
            if($this->isSchoolLogin() || $this->isPrincipalLogin()){
                $AvailableClassIds =    $this->ExamGradeClassMappingModel->where([
                                            cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_CURRICULUM_YEAR_ID_COL => $this->GetCurriculumYear(),
                                            cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_EXAM_ID_COL => $request->exam_id,
                                            cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_SCHOOL_ID_COL => $schoolId,
                                            cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_STATUS_COL => 'publish'
                                        ])
                                        ->pluck(cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_CLASS_ID_COL)->toArray();
                if(!empty($request->class_type_id)){
                    $AvailableClassIds = (is_array($request->class_type_id)) ? $request->class_type_id : [$request->class_type_id];
                }
                
                $GradeClassListData = GradeClassMapping::where([
                                        cn::GRADE_CLASS_MAPPING_CURRICULUM_YEAR_ID_COL => $this->GetCurriculumYear(),
                                        cn::GRADE_CLASS_MAPPING_GRADE_ID_COL => $request->grade_id,
                                        cn::GRADE_CLASS_MAPPING_SCHOOL_ID_COL => $schoolId
                                    ])
                                    ->whereIn(cn::GRADE_CLASS_MAPPING_ID_COL,$AvailableClassIds)
                                    ->groupBy(cn::GRADE_CLASS_MAPPING_NAME_COL)
                                    ->pluck(cn::GRADE_CLASS_MAPPING_NAME_COL,cn::GRADE_CLASS_MAPPING_ID_COL)
                                    ->toArray();
                // $studentList = User::where(cn::USERS_GRADE_ID_COL,$request->grade_id)
                //                 ->where(cn::USERS_ROLE_ID_COL,cn::STUDENT_ROLE_ID)
                //                 ->where(cn::USERS_SCHOOL_ID_COL,$schoolId)
                //                 ->pluck(cn::USERS_ID_COL)->toArray();
                $studentList =  User::where([
                                    cn::USERS_ROLE_ID_COL => cn::STUDENT_ROLE_ID,
                                    cn::USERS_SCHOOL_ID_COL => $schoolId
                                ])
                                ->get()
                                ->where('CurriculumYearGradeId',$request->grade_id)
                                ->pluck(cn::USERS_ID_COL)->toArray();
            }

            // if($this->isTeacherLogin()){
            //     $assignTeacherClasses = TeachersClassSubjectAssign::where([
            //                                 cn::TEACHER_CLASS_SUBJECT_CURRICULUM_YEAR_ID_COL => $this->GetCurriculumYear(),
            //                                 cn::TEACHER_CLASS_SUBJECT_SCHOOL_ID_COL => $this->isTeacherLogin(),
            //                                 cn::TEACHER_CLASS_SUBJECT_TEACHER_ID_COL => Auth::user()->{cn::USERS_ID_COL}
            //                             ])->pluck(cn::TEACHER_CLASS_SUBJECT_CLASS_NAME_ID_COL)->toArray();
            //     if(!empty($assignTeacherClasses)){
            //         $AssignClass = implode(',',$assignTeacherClasses);
            //         if(!empty($AssignClass)){
            //             $GradeClassListData = GradeClassMapping::where(cn::GRADE_CLASS_MAPPING_CURRICULUM_YEAR_ID_COL,$this->GetCurriculumYear())
            //                                     ->whereIn(cn::GRADE_CLASS_MAPPING_ID_COL,explode(',',$AssignClass))
            //                                     ->groupBy(cn::GRADE_CLASS_MAPPING_NAME_COL)
            //                                     ->pluck(cn::GRADE_CLASS_MAPPING_NAME_COL,cn::GRADE_CLASS_MAPPING_ID_COL)
            //                                     ->toArray();
            //             // $GradeClassListData = GradeClassMapping::where(cn::GRADE_CLASS_MAPPING_CURRICULUM_YEAR_ID_COL,$this->GetCurriculumYear())
            //             //                         ->whereIn(cn::GRADE_CLASS_MAPPING_ID_COL,explode(',',$AssignClass))
            //             //                         ->groupBy(cn::GRADE_CLASS_MAPPING_NAME_COL)
            //             //                         ->pluck(cn::GRADE_CLASS_MAPPING_NAME_COL,cn::GRADE_CLASS_MAPPING_ID_COL)
            //             //                         ->toArray();
            //         }
            //     }
            //     // $studentList =  User::where([
            //     //                     cn::USERS_ROLE_ID_COL => cn::STUDENT_ROLE_ID,
            //     //                     cn::USERS_GRADE_ID_COL => $request->grade_id,
            //     //                     cn::USERS_SCHOOL_ID_COL => $schoolId
            //     //                 ])->pluck(cn::USERS_ID_COL)->toArray();
            //     $studentList =  User::where(cn::USERS_ID_COL,$this->curriculum_year_mapping_student_ids($request->grade_id,'',$schoolId))
            //                         ->where(cn::USERS_ROLE_ID_COL,cn::STUDENT_ROLE_ID)
            //                         ->pluck(cn::USERS_ID_COL)->toArray();
            // }
            if($this->isTeacherLogin()){

                $TeacherGradeClass = $this->TeacherGradesClassService->getTeacherAssignedGradesClass($schoolId, Auth::user()->{cn::USERS_ID_COL});
                
                $AvailableClassIds =  ExamGradeClassMappingModel::where([
                                                        cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_CURRICULUM_YEAR_ID_COL => $this->GetCurriculumYear(),
                                                        cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_EXAM_ID_COL => $request->exam_id,
                                                        cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_SCHOOL_ID_COL => Auth::user()->{cn::USERS_SCHOOL_ID_COL},
                                                        cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_STATUS_COL => 'publish'
                                                    ])
                                                    ->whereIn(cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_CLASS_ID_COL,$TeacherGradeClass['class'])
                                                    ->pluck(cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_CLASS_ID_COL)->toArray();
                                                    
                if(!empty($request->class_type_id)){
                    $AvailableClassIds = (is_array($request->class_type_id)) ? $request->class_type_id : [$request->class_type_id];
                }
                if(!empty($AvailableGradeIds)){
                    $AvailableGradeIds = (is_array($request->grade_id)) ? $request->grade_id : [$request->grade_id];
                } 
                
                
                $GradeClassListData = GradeClassMapping::where(cn::GRADE_CLASS_MAPPING_CURRICULUM_YEAR_ID_COL,$this->GetCurriculumYear())
                                                        ->whereIn(cn::GRADE_CLASS_MAPPING_ID_COL,$AvailableClassIds)
                                                        ->groupBy(cn::GRADE_CLASS_MAPPING_NAME_COL)
                                                        ->pluck(cn::GRADE_CLASS_MAPPING_NAME_COL,cn::GRADE_CLASS_MAPPING_ID_COL)
                                                        ->toArray();                                        
                $studentList =  User::where(cn::USERS_ID_COL,$this->curriculum_year_mapping_student_ids($request->grade_id,'',$schoolId))
                                    ->where(cn::USERS_ROLE_ID_COL,cn::STUDENT_ROLE_ID)
                                    ->pluck(cn::USERS_ID_COL)->toArray();                                         
            }

            if($this->isAdmin()){
                $AvailableGradesIds =   $this->ExamGradeClassMappingModel->where([
                                            cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_CURRICULUM_YEAR_ID_COL =>$this->GetCurriculumYear(),
                                            cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_EXAM_ID_COL => $request->exam_id,
                                            cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_STATUS_COL => 'publish'
                                        ])
                                        ->pluck(cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_GRADE_ID_COL)->toArray();

                $AvailableClassIds = $this->ExamGradeClassMappingModel->where([
                                        cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_CURRICULUM_YEAR_ID_COL =>$this->GetCurriculumYear(),
                                        cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_EXAM_ID_COL => $request->exam_id,
                                        cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_GRADE_ID_COL => $request->grade_id,
                                        cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_STATUS_COL => 'publish'
                                    ])
                                    ->pluck(cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_CLASS_ID_COL)->toArray();

                $AvailableStudentIds =  $this->ExamGradeClassMappingModel->where([
                                            cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_EXAM_ID_COL => $request->exam_id,
                                            cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_PEER_GROUP_ID_COL => $request->grade_id,
                                            cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_STATUS_COL => 'publish'
                                        ])
                                        ->pluck(cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_STUDENT_IDS_COL)->toArray();
                
                $GradeClassListData =   GradeClassMapping::where(cn::GRADE_CLASS_MAPPING_CURRICULUM_YEAR_ID_COL,$this->GetCurriculumYear())
                                        ->whereIn(cn::GRADE_CLASS_MAPPING_GRADE_ID_COL,$AvailableGradesIds)
                                        ->groupBy(cn::GRADE_CLASS_MAPPING_NAME_COL)
                                        ->pluck(cn::GRADE_CLASS_MAPPING_NAME_COL,cn::GRADE_CLASS_MAPPING_ID_COL)
                                        ->toArray();
                if(isset($AvailableStudentIds) && !empty($AvailableStudentIds)){
                    $AvailableStudentIds = implode(',',$AvailableStudentIds);
                    $studentList = User::where(cn::USERS_ID_COL,explode(',',$AvailableStudentIds))
                                    ->where(cn::USERS_ROLE_ID_COL,cn::STUDENT_ROLE_ID)
                                    ->pluck(cn::USERS_ID_COL)
                                    ->toArray();
                }
            }
        }

        if(isset($request->grade_id) && !empty($request->grade_id) && isset($request->class_type_id) && !empty($request->class_type_id)){
            $filter=1;
            $class_type_id = is_array($request->class_type_id) ? $request->class_type_id : [$request->class_type_id];
            
            $GradeID = is_array($request->grade_id) ? $request->grade_id : [$request->grade_id];
            if($this->isSchoolLogin() || $this->isPrincipalLogin()){
                $AvailableClassIds =    $this->ExamGradeClassMappingModel->where([
                                            cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_CURRICULUM_YEAR_ID_COL => $this->GetCurriculumYear(),
                                            cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_EXAM_ID_COL => $request->exam_id,
                                            cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_SCHOOL_ID_COL => $schoolId,
                                            cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_STATUS_COL => 'publish'
                                        ])
                                        ->whereIn(cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_CLASS_ID_COL,$class_type_id)
                                        ->whereIn(cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_GRADE_ID_COL,$GradeID)
                                        ->pluck(cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_CLASS_ID_COL)->toArray();
                if(!empty($request->class_type_id)){
                    $AvailableClassIds = (is_array($request->class_type_id)) ? $request->class_type_id : [$request->class_type_id];
                }
                if(!empty($AvailableGradeIds)){
                    $AvailableGradeIds = (is_array($request->grade_id)) ? $request->grade_id : [$request->grade_id];
                } 
                $GradeClassMapping = GradeClassMapping::where([
                                        cn::GRADE_CLASS_MAPPING_CURRICULUM_YEAR_ID_COL => $this->GetCurriculumYear(),
                                        cn::GRADE_CLASS_MAPPING_GRADE_ID_COL => $request->grade_id,
                                        cn::GRADE_CLASS_MAPPING_SCHOOL_ID_COL => $schoolId
                                    ])
                                    ->whereIn(cn::GRADE_CLASS_MAPPING_ID_COL,$AvailableClassIds)
                                    ->groupBy(cn::GRADE_CLASS_MAPPING_NAME_COL)
                                    ->pluck(cn::GRADE_CLASS_MAPPING_ID_COL)
                                    ->toArray();
                                    
                // $studentList =  User::where(cn::USERS_GRADE_ID_COL,$request->grade_id)
                //                 ->whereIn(cn::USERS_CLASS_ID_COL,$GradeClassMapping)
                //                 ->where(cn::USERS_ROLE_ID_COL,cn::STUDENT_ROLE_ID)
                //                 ->where(cn::USERS_SCHOOL_ID_COL,$schoolId)
                //                 ->pluck(cn::USERS_ID_COL)->toArray();
                $studentList =  User::where([
                                    cn::USERS_ROLE_ID_COL => cn::STUDENT_ROLE_ID,
                                    cn::USERS_SCHOOL_ID_COL => $schoolId
                                ])
                                ->get()
                                ->where('CurriculumYearGradeId',$request->grade_id)
                                ->whereIn('CurriculumYearClassId',$GradeClassMapping)
                                ->pluck(cn::USERS_ID_COL)->toArray();
            }

            if($this->isTeacherLogin()){
                $GradeClassMapping = GradeClassMapping::where([
                                        cn::GRADE_CLASS_MAPPING_CURRICULUM_YEAR_ID_COL => $this->GetCurriculumYear(),
                                        cn::GRADE_CLASS_MAPPING_GRADE_ID_COL => $request->grade_id,
                                        cn::GRADE_CLASS_MAPPING_SCHOOL_ID_COL => $this->isTeacherLogin()
                                    ])
                                    ->whereIn(cn::GRADE_CLASS_MAPPING_ID_COL,$request->class_type_id)
                                    ->groupBy(cn::GRADE_CLASS_MAPPING_NAME_COL)
                                    ->pluck(cn::GRADE_CLASS_MAPPING_ID_COL)
                                    ->toArray();
                // $studentList =  User::where(cn::USERS_GRADE_ID_COL,$request->grade_id)
                //                 ->whereIn(cn::USERS_CLASS_ID_COL,$GradeClassMapping)
                //                 ->where(cn::USERS_ROLE_ID_COL,cn::STUDENT_ROLE_ID)
                //                 ->where(cn::USERS_SCHOOL_ID_COL,$schoolId)
                //                 ->pluck('id')->toArray();
                $studentList =  User::where([
                                    cn::USERS_ROLE_ID_COL => cn::STUDENT_ROLE_ID,
                                    cn::USERS_SCHOOL_ID_COL => $schoolId
                                ])
                                ->get()
                                ->where('CurriculumYearGradeId',$request->grade_id)
                                ->whereIn('CurriculumYearClassId',$GradeClassMapping)
                                ->pluck(cn::USERS_ID_COL)->toArray();
            }

            if($this->isAdmin()){
                $exam = Exam::find($request->exam_id);
                $childExamsIds = array($request->exam_id);
                if($exam->{cn::EXAM_TABLE_USE_OF_MODE_COLS} == 2 && $exam->{cn::EXAM_TABLE_CREATED_BY_USER_COL} == 'super_admin'){
                    $childExamsIds = Exam::where(cn::EXAM_TABLE_PARENT_EXAM_ID_COLS,$request->exam_id)->pluck(cn::EXAM_TABLE_ID_COLS)->toArray();
                }
                $AvailableGradesIds =   $this->ExamGradeClassMappingModel->where([
                                            cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_CURRICULUM_YEAR_ID_COL => $this->GetCurriculumYear(),
                                            cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_STATUS_COL => 'publish'
                                        ])
                                        ->whereIn(cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_EXAM_ID_COL,$childExamsIds)
                                        ->whereNotNull(cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_GRADE_ID_COL)
                                        ->pluck(cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_GRADE_ID_COL)->toArray();

                $GradeList = Grades::whereIn(cn::GRADES_ID_COL,$AvailableGradesIds)->get();

                $AvailableClassIds =    $this->ExamGradeClassMappingModel->where([
                                            cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_CURRICULUM_YEAR_ID_COL => $this->GetCurriculumYear(),
                                            cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_GRADE_ID_COL => $request->grade_id,
                                            cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_STATUS_COL => 'publish'
                                        ])
                                        ->whereIn(cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_EXAM_ID_COL,$childExamsIds)
                                        ->pluck(cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_CLASS_ID_COL)->toArray();
                                
                $AvailableStudentIds =  $this->ExamGradeClassMappingModel->where([
                                            cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_CURRICULUM_YEAR_ID_COL => $this->GetCurriculumYear(),
                                            cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_GRADE_ID_COL => $request->grade_id,
                                            cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_STATUS_COL => 'publish'
                                        ])
                                        ->whereIn(cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_EXAM_ID_COL,$childExamsIds)
                                        ->whereIn(cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_CLASS_ID_COL,$request->class_type_id)
                                        ->pluck(cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_STUDENT_IDS_COL)->toArray();

                // $GradeClassMapping =    GradeClassMapping::where(cn::GRADE_CLASS_MAPPING_CURRICULUM_YEAR_ID_COL,$this->GetCurriculumYear())
                //                         ->whereIn(cn::GRADE_CLASS_MAPPING_GRADE_ID_COL,$AvailableGradesIds)
                //                         ->whereIn(cn::GRADE_CLASS_MAPPING_ID_COL,$AvailableClassIds)
                //                         ->groupBy(cn::GRADE_CLASS_MAPPING_NAME_COL)
                //                         ->pluck(cn::GRADE_CLASS_MAPPING_ID_COL)
                //                         ->toArray();

                // $GradeClassMappingName = GradeClassMapping::where(cn::GRADE_CLASS_MAPPING_CURRICULUM_YEAR_ID_COL,$this->GetCurriculumYear())
                //                         ->whereIn(cn::GRADE_CLASS_MAPPING_GRADE_ID_COL,$AvailableGradesIds)
                //                         ->whereIn(cn::GRADE_CLASS_MAPPING_ID_COL,$AvailableClassIds)
                //                         ->groupBy(cn::GRADE_CLASS_MAPPING_NAME_COL)
                //                         ->pluck(cn::GRADE_CLASS_MAPPING_NAME_COL)
                //                         ->toArray();

                // $GradeClassListData =   GradeClassMapping::where(cn::GRADE_CLASS_MAPPING_CURRICULUM_YEAR_ID_COL,$this->GetCurriculumYear())
                //                         ->whereIn(cn::GRADE_CLASS_MAPPING_GRADE_ID_COL,$AvailableGradesIds)
                //                         ->whereIn(cn::GRADE_CLASS_MAPPING_ID_COL,$AvailableClassIds)
                //                         ->groupBy(cn::GRADE_CLASS_MAPPING_NAME_COL)
                //                         ->pluck(cn::GRADE_CLASS_MAPPING_NAME_COL,cn::GRADE_CLASS_MAPPING_ID_COL)
                //                         ->toArray();

                $ClassIds = $this->ExamGradeClassMappingModel->with('grade_class_mapping')
                            ->where([
                                cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_CURRICULUM_YEAR_ID_COL => $this->GetCurriculumYear(),
                                cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_GRADE_ID_COL => $request->grade_id,
                                cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_STATUS_COL => 'publish',
                                'school_id' => $request->exam_school_id
                            ])
                            ->whereIn(cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_EXAM_ID_COL,$childExamsIds)
                            ->pluck(cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_CLASS_ID_COL)->toArray();
                                                    //echo '<pre>';print_r($ClassIds);die;

                $GradeClassListData =   GradeClassMapping::where(cn::GRADE_CLASS_MAPPING_CURRICULUM_YEAR_ID_COL,$this->GetCurriculumYear())
                                                        ->whereIn(cn::GRADE_CLASS_MAPPING_ID_COL,$ClassIds)
                                                        ->groupBy(cn::GRADE_CLASS_MAPPING_NAME_COL)
                                                        ->pluck(cn::GRADE_CLASS_MAPPING_NAME_COL,cn::GRADE_CLASS_MAPPING_ID_COL)
                                                        ->toArray();
                if(isset($AvailableStudentIds) && !empty($AvailableStudentIds)){
                    $AvailableStudentIds = implode(',',$AvailableStudentIds);
                    $studentList =  User::whereIn(cn::USERS_ID_COL,explode(',',$AvailableStudentIds))
                                    ->where(cn::USERS_ROLE_ID_COL,cn::STUDENT_ROLE_ID)
                                    ->pluck(cn::USERS_ID_COL)
                                    ->toArray();
                }
            }
        }

        if(isset($request->group_id) && !empty($request->group_id)){
            $group_id = $request->group_id;
            $school_id = Auth::user()->{cn::USERS_SCHOOL_ID_COL};
            $exam_id = $request->exam_id;

            // Find Peer group list for super admin
            if($this->isAdmin()){
                $exam = Exam::find($request->exam_id);
                $childExamsIds = array($request->exam_id);
                if($exam->{cn::EXAM_TABLE_USE_OF_MODE_COLS} == 2 && $exam->{cn::EXAM_TABLE_CREATED_BY_USER_COL} == 'super_admin'){
                    $childExamsIds = Exam::where(cn::EXAM_TABLE_PARENT_EXAM_ID_COLS,$request->exam_id)->pluck(cn::EXAM_TABLE_ID_COLS)->toArray();
                    $PeerGroupIds = ExamGradeClassMappingModel::whereIn(cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_EXAM_ID_COL,$childExamsIds)
                                    ->where([
                                        cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_CURRICULUM_YEAR_ID_COL => $this->GetCurriculumYear(),
                                        cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_STATUS_COL => 'publish'
                                    ])
                                    ->pluck(cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_PEER_GROUP_ID_COL);
                }else{
                    $examId = $request->exam_id;
                    $PeerGroupIds = $this->ExamGradeClassMappingModel->where([
                                        cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_CURRICULUM_YEAR_ID_COL => $this->GetCurriculumYear(),
                                        cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_EXAM_ID_COL => $examId,
                                        cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_STATUS_COL => 'publish'
                                    ])
                                    ->whereNotNull(cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_PEER_GROUP_ID_COL)
                                    ->pluck(cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_PEER_GROUP_ID_COL)->toArray();

                    if(isset($request->exam_school_id) && !empty($request->exam_school_id)){
                        $PeerGroupIds = $this->ExamGradeClassMappingModel->where([
                                            cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_CURRICULUM_YEAR_ID_COL => $this->GetCurriculumYear(),
                                            cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_EXAM_ID_COL => $examId,
                                            cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_SCHOOL_ID_COL => $request->exam_school_id,
                                            cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_STATUS_COL => 'publish'
                                        ])
                                        ->whereNotNull(cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_PEER_GROUP_ID_COL)
                                        ->pluck(cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_PEER_GROUP_ID_COL)->toArray();
                    }
                }
                $PeerGroupList = PeerGroup::whereIn(cn::PEER_GROUP_ID_COL,$PeerGroupIds)
                                ->where([
                                    cn::PEER_GROUP_CURRICULUM_YEAR_ID_COL => $this->GetCurriculumYear(),
                                    cn::PEER_GROUP_STATUS_COL => 1
                                ])->get();
                $PeerGroupStudentIds =  PeerGroupMember::where([
                                            cn::PEER_GROUP_MEMBERS_CURRICULUM_YEAR_ID_COL => $this->GetCurriculumYear(),
                                            cn::PEER_GROUP_MEMBERS_PEER_GROUP_ID_COL => $request->group_id
                                        ])
                                        ->pluck(cn::PEER_GROUP_MEMBERS_MEMBER_ID_COL)->toArray();
                if(isset($PeerGroupStudentIds) && !empty($PeerGroupStudentIds)){
                    if($exam->{cn::EXAM_TABLE_USE_OF_MODE_COLS} == 2 && $exam->{cn::EXAM_TABLE_CREATED_BY_USER_COL} == 'super_admin'){
                        $studentListPeerGroup = User::whereIn(cn::USERS_ID_COL,$PeerGroupStudentIds)
                                                ->where(cn::USERS_ROLE_ID_COL,cn::STUDENT_ROLE_ID)
                                                ->pluck(cn::USERS_ID_COL)->toArray();
                        if(isset($studentList) && !empty($studentList)){
                            $studentList = array_values(array_unique(array_merge($studentList,$studentListPeerGroup)));
                        }
                    }else{
                        $studentList =  User::whereIn(cn::USERS_ID_COL,$PeerGroupStudentIds)
                                        ->where(cn::USERS_ROLE_ID_COL,cn::STUDENT_ROLE_ID)
                                        ->pluck(cn::USERS_ID_COL)
                                        ->toArray();
                    }
                }else{
                    $studentList = array();
                }
            }

            // Find Peer group list for super admin
            if($this->isSchoolLogin() || $this->isPrincipalLogin()){
                if($this->isSchoolLogin()){
                    $schoolId = $this->isSchoolLogin();
                }
                if($this->isPrincipalLogin()){
                    $schoolId = $this->isPrincipalLogin();
                }
                $PeerGroupIds = ExamGradeClassMappingModel::where([
                                    cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_CURRICULUM_YEAR_ID_COL => $this->GetCurriculumYear(),
                                    cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_EXAM_ID_COL => $request->exam_id,
                                    cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_SCHOOL_ID_COL => $schoolId,
                                    cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_STATUS_COL => 'publish'
                                ])
                                ->pluck(cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_PEER_GROUP_ID_COL)->toArray();
                $PeerGroupList = PeerGroup::whereIn(cn::PEER_GROUP_ID_COL,$PeerGroupIds)
                                ->where([
                                    cn::PEER_GROUP_CURRICULUM_YEAR_ID_COL => $this->GetCurriculumYear(),
                                    cn::PEER_GROUP_STATUS_COL => 1
                                ])
                                ->get();
                $PeerGroupStudentIds =  PeerGroupMember::where([
                                            cn::PEER_GROUP_MEMBERS_CURRICULUM_YEAR_ID_COL => $this->GetCurriculumYear(),
                                            cn::PEER_GROUP_MEMBERS_PEER_GROUP_ID_COL => $request->group_id
                                        ])->pluck(cn::PEER_GROUP_MEMBERS_MEMBER_ID_COL)->toArray();
                if(isset($PeerGroupStudentIds) && !empty($PeerGroupStudentIds)){
                    $studentList =  User::whereIn(cn::USERS_ID_COL,$PeerGroupStudentIds)
                                    ->where(cn::USERS_ROLE_ID_COL,cn::STUDENT_ROLE_ID)
                                    ->pluck(cn::USERS_ID_COL)->toArray();
                }else{
                    $studentList = array();
                }
            }
        }

        if($this->isSchoolLogin() || $this->isTeacherLogin() || $this->isPrincipalLogin()){
            $currentLoggedSchoolId = '';
            $findSchoolExamIds = [];
            if($this->isTeacherLogin()){
                $currentLoggedSchoolId = $this->isTeacherLogin();
                $TeacherGradeClass = $this->TeacherGradesClassService->getTeacherAssignedGradesClass($currentLoggedSchoolId, Auth::user()->{cn::USERS_ID_COL});
                if(!empty($TeacherGradeClass)){
                    $AvailableGradesIds =   $this->ExamGradeClassMappingModel->where([
                                                cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_CURRICULUM_YEAR_ID_COL => $this->GetCurriculumYear(),
                                                cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_SCHOOL_ID_COL => $currentLoggedSchoolId,
                                                cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_STATUS_COL =>'publish'
                                            ])
                                            ->whereIn(cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_GRADE_ID_COL,$TeacherGradeClass['grades'])
                                            ->pluck(cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_GRADE_ID_COL)->toArray();                        
                    $AvailableClassIds = $this->ExamGradeClassMappingModel->where([
                                            cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_CURRICULUM_YEAR_ID_COL => $this->GetCurriculumYear(),
                                            cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_SCHOOL_ID_COL => $currentLoggedSchoolId,
                                            cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_STATUS_COL => 'publish'
                                        ])
                                        ->whereIn(cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_CLASS_ID_COL,$TeacherGradeClass['class'])
                                        ->pluck(cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_CLASS_ID_COL)->toArray();  
                    $AvailableGradesClassExamIds =  $this->ExamGradeClassMappingModel->where([
                                                        cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_CURRICULUM_YEAR_ID_COL => $this->GetCurriculumYear(),
                                                        cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_SCHOOL_ID_COL => $currentLoggedSchoolId,
                                                        cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_STATUS_COL=>'publish'
                                                    ])
                                                    ->whereIn(cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_GRADE_ID_COL,$AvailableGradesIds)
                                                    ->whereIn(cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_CLASS_ID_COL,$AvailableClassIds)
                                                    ->pluck(cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_EXAM_ID_COL)->toArray();

                    $TeacherPeerGroupIds = $this->TeacherGradesClassService->GetTeachersPeerGroupIds(Auth::user()->{cn::USERS_ID_COL}, $currentLoggedSchoolId);
                    $AvailablePeerGroupExamIds = $this->ExamGradeClassMappingModel->where([
                                                    cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_CURRICULUM_YEAR_ID_COL => $this->GetCurriculumYear(),
                                                    cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_SCHOOL_ID_COL=>$currentLoggedSchoolId,
                                                    cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_STATUS_COL => 'publish'
                                                ])
                                                ->whereIn(cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_PEER_GROUP_ID_COL,$TeacherPeerGroupIds)
                                                ->pluck(cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_EXAM_ID_COL)->toArray();
                    $examIds = array_merge($AvailableGradesClassExamIds,$AvailablePeerGroupExamIds);
                    $examIds = array_values(array_unique($examIds));
                    $findSchoolExamIds = $this->ExamSchoolMapping->where([
                                            cn::EXAM_SCHOOL_MAPPING_CURRICULUM_YEAR_ID_COL => $this->GetCurriculumYear(),
                                            cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_EXAM_ID_COL => $examIds,
                                            cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_STATUS_COL => 'publish'
                                        ])
                                        ->pluck(cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_EXAM_ID_COL)->toArray();          
                }
            }

            if($this->isSchoolLogin()){
                $currentLoggedSchoolId = $this->isSchoolLogin();
            }
            if($this->isPrincipalLogin()){
                $currentLoggedSchoolId = $this->isPrincipalLogin();
            }
            if(empty($findSchoolExamIds)){
                $findSchoolExamIds = $this->ExamSchoolMapping->where([cn::EXAM_SCHOOL_MAPPING_SCHOOL_ID_COL => Auth::user()->{cn::USERS_SCHOOL_ID_COL}, cn::EXAM_SCHOOL_MAPPING_STATUS_COL => 'publish'])->pluck(cn::EXAM_SCHOOL_MAPPING_EXAM_ID_COL)->toArray();
            }

            // Get Self-Learning Exam ids
            $selfLearningExamIds = [];
            $selfLearningExamIds =  Exam::whereRaw("find_in_set($currentLoggedSchoolId,school_id)")
                                    ->where([
                                        cn::EXAM_CURRICULUM_YEAR_ID_COL => $this->GetCurriculumYear(),
                                        cn::EXAM_TABLE_STATUS_COLS => 'publish'
                                    ])
                                    ->pluck(cn::EXAM_TABLE_ID_COLS)
                                    ->toArray();

            $removeSelfLearningExamIds =    Exam::whereRaw("find_in_set($currentLoggedSchoolId,school_id)")
                                            ->where([
                                                cn::EXAM_CURRICULUM_YEAR_ID_COL => $this->GetCurriculumYear(),
                                                cn::EXAM_TABLE_CREATED_BY_USER_COL => 'super_admin',
                                                cn::EXAM_TABLE_USE_OF_MODE_COLS => 2,
                                                cn::EXAM_TABLE_STATUS_COLS => 'publish'
                                            ])
                                            ->whereNull(cn::EXAM_TABLE_PARENT_EXAM_ID_COLS)
                                            ->pluck(cn::EXAM_TABLE_ID_COLS)->toArray();

            $ExamList = Exam::whereIn(cn::EXAM_TABLE_ID_COLS,array_merge($findSchoolExamIds,$selfLearningExamIds))
                        ->whereNotIn(cn::EXAM_TABLE_ID_COLS,$removeSelfLearningExamIds)
                        ->where([
                            cn::EXAM_CURRICULUM_YEAR_ID_COL => $this->GetCurriculumYear(),
                            cn::EXAM_TABLE_STATUS_COLS => 'publish'
                        ])->get();
        }else{
            $ExamList = Exam::where(function($query){
                            $query->whereIn(cn::EXAM_TABLE_USE_OF_MODE_COLS,[1,2])
                            // ->whereNotNull(cn::EXAM_TABLE_PARENT_EXAM_ID_COLS)
                            ->where(cn::EXAM_CURRICULUM_YEAR_ID_COL,$this->GetCurriculumYear())
                            ->where(cn::EXAM_TABLE_STATUS_COLS,'publish');
                        })
                        ->orWhere(cn::EXAM_TABLE_CREATED_BY_USER_COL,'student')
                        ->orderBy(cn::EXAM_TABLE_ID_COLS,'DESC')
                        ->where([
                            cn::EXAM_CURRICULUM_YEAR_ID_COL => $this->GetCurriculumYear(),
                            cn::EXAM_TABLE_STATUS_COLS => 'publish'
                        ])
                        ->get();
        }        
        $QuestionSkills = [];
        $studentCount = 0;
        $ExamData = '';
        $peerGroupData = collect();
        if(isset($request->filter)){
            if(isset($request->school_id)){
                $ExamData = Exam::where([
                                cn::EXAM_CURRICULUM_YEAR_ID_COL => $this->GetCurriculumYear(),
                                cn::EXAM_TABLE_ID_COLS => $request->exam_id
                            ])
                            ->whereRaw("find_in_set($request->school_id,school_id)")->first();
            }else if($this->isSchoolLogin() || $this->isTeacherLogin() || $this->isPrincipalLogin()){
                $ExamData = Exam::where([
                                cn::EXAM_CURRICULUM_YEAR_ID_COL => $this->GetCurriculumYear(),
                                cn::EXAM_TABLE_ID_COLS => $request->exam_id
                            ])
                            ->whereRaw("find_in_set($currentLoggedSchoolId,school_id)")->first();
            }else{
                if($this->isAdmin()){
                    $getClasses = $this->getClassesByRoles($request->exam_id,$request->exam_school_id,$request->grade_id);
                }
                $ExamData = Exam::find($request->exam_id);
            }
            if(!empty($ExamData)){
                /* For Display a Group in Dialog Box */ 
                if(!empty($ExamData->peer_group_ids)){
                    $peerGroupIDs = explode(',',$ExamData->peer_group_ids);
                    if($this->isTeacherLogin()){
                        $peerGroupData =    PeerGroup::whereIn(cn::PEER_GROUP_ID_COL,$peerGroupIDs)
                                            ->where([
                                                cn::PEER_GROUP_CURRICULUM_YEAR_ID_COL => $this->GetCurriculumYear(),
                                                cn::PEER_GROUP_SCHOOL_ID_COL => Auth::user()->{cn::USERS_SCHOOL_ID_COL},
                                                cn::PEER_GROUP_CREATED_BY_USER_ID_COL => Auth::user()->{cn::USERS_ID_COL}
                                            ])
                                            ->get();
                    }
                    if($this->isSchoolLogin() || $this->isPrincipalLogin()){
                        $peerGroupData = PeerGroup::whereIn(cn::PEER_GROUP_ID_COL,$peerGroupIDs)
                                        ->where([
                                            cn::PEER_GROUP_CURRICULUM_YEAR_ID_COL => $this->GetCurriculumYear(),
                                            cn::PEER_GROUP_SCHOOL_ID_COL => Auth::user()->{cn::USERS_SCHOOL_ID_COL}
                                        ])
                                        ->get();
                    }
                    if($this->isAdmin()){
                        $peerGroupData = PeerGroup::where(cn::PEER_GROUP_CURRICULUM_YEAR_ID_COL,$this->GetCurriculumYear())->whereIn(cn::PEER_GROUP_ID_COL,$peerGroupIDs)->get();
                    }
                }

                if(!empty($ExamData->student_ids)){
                    $studentIds = explode(',',$ExamData->student_ids);
                    if($ExamData->exam_type == 2 || $ExamData->exam_type == 3){
                        if(isset($studentList) && $filter==1){
                            $result = array_intersect($studentIds,$studentList);
                            $studentIds = array_values($result);
                        }else if($this->isSchoolLogin() || $this->isPrincipalLogin()){
                            $result = array_intersect($studentIds,$studentList);
                            $studentIds = array_values($result);
                        }else if($this->isTeacherLogin()){
                            $result = array_intersect($studentIds,$studentList);
                            $studentIds = array_values($result);
                        }else if($this->isAdmin()){
                            $result = array_intersect($studentIds,$studentList);
                            $studentIds = array_values($result);
                        }
                    }
                    $exam = Exam::find($request->exam_id);
                    $examType = $exam->{cn::EXAM_TYPE_COLS};
                    $childExamsIds = array($request->exam_id);
                    if($exam->{cn::EXAM_TABLE_USE_OF_MODE_COLS} == 2 && $exam->{cn::EXAM_TABLE_CREATED_BY_USER_COL} == 'super_admin' && $this->isAdmin()){
                        $childExamsIds = Exam::where(cn::EXAM_TABLE_PARENT_EXAM_ID_COLS,$request->exam_id)->pluck(cn::EXAM_TABLE_ID_COLS)->toArray();
                    }
                    foreach($childExamsIds as $ExamsIds){
                        $ExamData = Exam::find($ExamsIds);
                        foreach($studentIds as $studentKey => $studentId){
                            if($this->isPrincipalLogin() ||$this->isSchoolLogin() || $this->isTeacherLogin() && $this->isSchoolStudent($studentId) || $this->isAdmin()){
                                // Get correct answer detail
                                $AttemptExamData =  AttemptExams::where([
                                                        cn::ATTEMPT_EXAMS_EXAM_ID => $ExamData->{cn::EXAM_TABLE_ID_COLS},
                                                        cn::ATTEMPT_EXAMS_STUDENT_STUDENT_ID => $studentId
                                                    ])->first();
                                if(isset($AttemptExamData) && !empty($AttemptExamData)){
                                    if($this->isAdmin()){
                                        $StudentDetail = User::where([cn::USERS_ID_COL => $studentId,cn::USERS_SCHOOL_ID_COL => $request->exam_school_id])->first();
                                    }else{
                                        $StudentDetail = User::where([cn::USERS_ID_COL => $studentId,cn::USERS_SCHOOL_ID_COL => Auth::user()->school_id])->first();
                                    }
                                    if(isset($StudentDetail) && !empty($StudentDetail)){
                                        $ResultList[$studentKey]['exam_id'] = $ExamData->id;
                                        $ResultList[$studentKey]['student_grade'] = $StudentDetail->CurriculumYearGradeId ?? 0;
                                        $ResultList[$studentKey]['class_student_number'] = $StudentDetail->CurriculumYearData['class_student_number'] ?? 'N/A';
                                        $ResultList[$studentKey]['student_number'] = $StudentDetail->id;
                                        $ResultList[$studentKey]['student_name'] = ($StudentDetail->name_en) ? $this->decrypt($StudentDetail->name_en) : $StudentDetail->name;
                                        $ResultList[$studentKey]['student_status'] = 'Active';
                                        $ResultList[$studentKey]['student_ability'] = $AttemptExamData->{cn::ATTEMPT_EXAMS_STUDENT_ABILITY_COL} ?? 'N/A';
                                        $ResultList[$studentKey]['student_normalize_ability'] = $this->getNormalizedAbility($AttemptExamData->{cn::ATTEMPT_EXAMS_STUDENT_ABILITY_COL}) ?? 'N/A';
                                        //$ResultList[$studentKey]['short_student_normalize_ability'] = Helper::GetShortPercentage($ResultList[$studentKey]['student_normalize_ability']);
                                        $ResultList[$studentKey]['short_student_normalize_ability'] = $ResultList[$studentKey]['student_normalize_ability'];
                                        $ResultList[$studentKey]['student_accuracy'] = Helper::getAccuracy($ExamData->id, $studentId);
                                        //$ResultList[$studentKey]['student_normalize_accuracy'] = Helper::GetShortPercentage($ResultList[$studentKey]['student_accuracy']);
                                        $ResultList[$studentKey]['student_normalize_accuracy'] = $ResultList[$studentKey]['student_accuracy'];
                                        $ResultList[$studentKey]['countStudent'] = (++$studentCount);
                                        $ResultList[$studentKey]['total_correct_answer'] = $AttemptExamData->total_correct_answers;
                                        $ResultList[$studentKey]['exam_status'] = (($AttemptExamData->status) && $AttemptExamData->status == 1) ? 'Complete' : 'Pending';
                                        $ResultList[$studentKey]['completion_time'] = ($AttemptExamData->exam_taking_timing) ? $AttemptExamData->exam_taking_timing : '--';
                                        // $ResultList[$studentKey]['student_ranking'] = $this->getStudentExamRanking($ExamData->id, $studentId);
                                        // $studentRank = $this->getStudentExamRanking($ExamData->id, $studentIds);
                                        // $ResultList[$studentKey]['student_ranking']  = $studentRank[$studentId];
                                        $ResultList[$studentKey]['student_ranking']  = 0;
                                        $ResultList[$studentKey]['ability_ranking']  = 0;
                                        $ResultList[$studentKey]['accuracy_ranking']  = 0;
                                        if(!empty($ExamData->question_ids)){
                                            $questionIds = explode(',',$ExamData->question_ids);
                                            $QuestionList = Question::with('answers')->whereIn(cn::QUESTION_TABLE_ID_COL,$questionIds)->get();
                                            if(isset($QuestionList) && !empty($QuestionList)){
                                                $ResultList[$studentKey]['countQuestions'] = count($QuestionList);
                                                foreach($QuestionList as $questionKey => $question){
                                                    if(isset($question)){
                                                        if(isset($AttemptExamData['question_answers'])){
                                                            $filterattempQuestionAnswer = array_filter(json_decode($AttemptExamData['question_answers']), function ($var) use($question){
                                                                if($var->question_id == $question['id']){
                                                                    return $var ?? [];
                                                                }
                                                            });
                                                        }
                                                    }
                                                
                                                    if(isset($filterattempQuestionAnswer) && !empty($filterattempQuestionAnswer)){
                                                        foreach($filterattempQuestionAnswer as $fanswer){
                                                            if($fanswer->answer == 1){
                                                                if(isset($QuestionAnswerData[$questionKey]['A'])){
                                                                    $QuestionAnswerData[$questionKey]['A'] = ($QuestionAnswerData[$questionKey]['A'] + 1);
                                                                }else{
                                                                    $QuestionAnswerData[$questionKey]['A'] = 1;
                                                                }
                                                            }
                                                            if($fanswer->answer == 2){
                                                                if(isset($QuestionAnswerData[$questionKey]['B'])){
                                                                    $QuestionAnswerData[$questionKey]['B'] = ($QuestionAnswerData[$questionKey]['B'] + 1);
                                                                }else{
                                                                    $QuestionAnswerData[$questionKey]['B'] = 1;
                                                                }
                                                            }
                                                            if($fanswer->answer == 3){
                                                                if(isset($QuestionAnswerData[$questionKey]['C'])){
                                                                    $QuestionAnswerData[$questionKey]['C'] = ($QuestionAnswerData[$questionKey]['C'] + 1);
                                                                }else{
                                                                    $QuestionAnswerData[$questionKey]['C'] = 1;
                                                                }
                                                            }
                                                            if($fanswer->answer == 4){
                                                                if(isset($QuestionAnswerData[$questionKey]['D'])){
                                                                    $QuestionAnswerData[$questionKey]['D'] = ($QuestionAnswerData[$questionKey]['D'] + 1);
                                                                }else{
                                                                    $QuestionAnswerData[$questionKey]['D'] = 1;
                                                                }
                                                            }
                                                            if($fanswer->answer == $question->answers->{'correct_answer_'.$fanswer->language}){
                                                                $ResultList[$studentKey][$questionKey] = array('answer' => 'true','selected_answer' => $fanswer->answer);
                                                            }else{
                                                                $ResultList[$studentKey][$questionKey] = array('answer' => 'false','selected_answer' => $fanswer->answer);
                                                            }
                                                        }
                                                    }else{
                                                        $ResultList[$studentKey][$questionKey] = array('answer' => 'false');
                                                    }
                                                    // Store exams skill array
                                                    $QuestionSkills[$questionKey] = $question->e;
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

        $studentsRanks = [];
        if(!empty($ResultList)){
            // foreach($ResultList as $key => $result){
            //     $studentsRanks[$key] = $result['total_correct_answer'];
            // }
            //array_multisort($studentsRanks, SORT_DESC, $ResultList);

            if(!empty($ResultList)){
                $NaturalOrder = [];
                // Set Natural ordering
                for($i=1; $i <= count($ResultList); $i++){
                    $NaturalOrder[] = $i;
                }
                $StudentAccuracyRank = array_column($ResultList,'student_normalize_accuracy');
                array_multisort($StudentAccuracyRank, SORT_DESC, $ResultList);

                $StudentAbilityRank = array_column($ResultList,'short_student_normalize_ability');
                array_multisort($StudentAbilityRank, SORT_DESC, $ResultList);

                $StudentAnswerCorrectInCorrectRank = array_column($ResultList,'total_correct_answer');
                array_multisort($StudentAnswerCorrectInCorrectRank, SORT_DESC, $ResultList);
                
                foreach($ResultList as $key => $result){
                    if(in_array($result['student_normalize_accuracy'],$StudentAccuracyRank)){
                        $ResultList[$key]['accuracy_ranking'] = (array_search($result['student_normalize_accuracy'], $StudentAccuracyRank) + 1);
                    }

                    if(in_array($result['short_student_normalize_ability'],$StudentAbilityRank)){
                        $ResultList[$key]['ability_ranking'] = (array_search($result['short_student_normalize_ability'], $StudentAbilityRank) + 1);
                    }

                    if(in_array($result['total_correct_answer'],$StudentAnswerCorrectInCorrectRank)){
                        $ResultList[$key]['student_ranking'] = (array_search($result['total_correct_answer'], $StudentAnswerCorrectInCorrectRank) + 1);
                    }
                }
            }
        }

        // Sorting array based on query sting params
        if(isset($_GET['sort_by_type']) && isset($_GET['sort_by_value'])){
            // Sorting based on student names
            if($_GET['sort_by_type'] == 'student_name'){
                if($_GET['sort_by_value'] == 'asc'){
                    usort($ResultList, $this->make_comparer(['student_name', SORT_DESC]));
                }else{
                    usort($ResultList, $this->make_comparer(['student_name', SORT_ASC]));
                }
            }else if($_GET['sort_by_type'] == 'ability_rank'){
                if($_GET['sort_by_value'] == 'asc'){
                    usort($ResultList, $this->make_comparer(['ability_ranking', SORT_DESC]));
                }else{
                    usort($ResultList, $this->make_comparer(['ability_ranking', SORT_ASC]));
                }
            }else if($_GET['sort_by_type'] == 'ability_rank'){
                if($_GET['sort_by_value'] == 'asc'){
                    usort($ResultList, $this->make_comparer(['ability_ranking', SORT_DESC]));
                }else{
                    usort($ResultList, $this->make_comparer(['ability_ranking', SORT_ASC]));
                }
            }else{
                // Sorting based on student ranking
                if($_GET['sort_by_value'] == 'asc'){
                    usort($ResultList, $this->make_comparer(['total_correct_answer', SORT_DESC]));
                }else{
                    usort($ResultList, $this->make_comparer(['total_correct_answer', SORT_ASC]));
                }
            }
        }else{
            usort($ResultList, $this->make_comparer(['student_name', SORT_ASC]));
        }
        return view('backend/reports/class_test_result_correct_incorrect',compact('examType','peerGroupData','getClasses','SchoolList','GradeList','ExamList',
        'ResultList','QuestionSkills','ExamData','studentsRanks','grade_id','class_type_id','group_id','GradeClassListData','QuestionAnswerData',
        'PeerGroupList','schoolList','isRemainderEnable'));
    }

    /**
     * USE : Student Expandable reports in details
     */
    public function AjaxClassTestExpandReportStudent(Request $request){
        $result = [];
        $resultArray = [];
        $wrongAnswerQuestionIds = [];
        $QuestionAnswerWeeknessSkills = [];
        if(isset($request->exam_id) && isset($request->student_id)){
            $student_id = $request->student_id;
            
            //Get all students attempetd set exams
            $AllAttemptedExamsStudents = AttemptExams::where('exam_id',$request->exam_id)->get()->pluck('student_id');

            // Get all students get weekness details
            $QuestionAnswerWeeknessSkills = $this->getStudentsWeaknessDetails($request, $request->exam_id, $AllAttemptedExamsStudents);
            $AttemptExamData = AttemptExams::where(cn::ATTEMPT_EXAMS_STUDENT_STUDENT_ID,$request->student_id)->where(cn::ATTEMPT_EXAMS_EXAM_ID,$request->exam_id)->first();
            $ExamData = Exam::find($request->exam_id);
            if(isset($ExamData) && !empty($ExamData)){
                if(!empty($ExamData->question_ids)){
                    $questionIds = explode(',',$ExamData->question_ids);
                    $QuestionList = Question::with('answers')->whereIn('id',$questionIds)->get();
                    if(isset($QuestionList) && !empty($QuestionList)){
                        foreach($QuestionList as $key => $question){
                            $resultArray[$key]['question'] = $question->question_en ?? '';
                            $resultArray[$key]['countQuestions'] = count($QuestionList);
                            $lan = $AttemptExamData->language;
                            $resultArray[$key]['question_id'] = $question->id;
                            $Answerdetail = $question->answers;
                            $correctAnswerNumber = $Answerdetail->{'correct_answer_'.$lan};                            
                            $resultArray[$key]['correct_answer'] = $Answerdetail->{'answer'.$correctAnswerNumber.'_'.$lan}; 
                            if(isset($AttemptExamData['question_answers'])){
                                $filterattempQuestionAnswer = array_filter(json_decode($AttemptExamData['question_answers']), function ($var) use($question){
                                    if($var->question_id == $question['id']){
                                        return $var ?? [];
                                    }
                                });
                            }
                            $resultArray[$key]['total_correct_answer'] = $AttemptExamData->total_correct_answers;
                            
                            if(isset($filterattempQuestionAnswer) && !empty($filterattempQuestionAnswer)){
                                foreach($filterattempQuestionAnswer as $fanswer){
                                    $AnswerData = Answer::where('question_id',$question->id)->first();
                                    $resultArray[$key]['student_answer'] = $AnswerData->{'answer'.$fanswer->answer.'_'.$fanswer->language};
                                    if($fanswer->answer == $Answerdetail->{'correct_answer_'.$fanswer->language}){
                                        $resultArray[$key]['answer_status'] = 'true';
                                    }else{
                                        $resultArray[$key]['answer_status'] = 'false';
                                    }
                                }
                            }else{
                                $resultArray[$key]['answer_status'] = 'false';                                
                            }
                            $resultArray[$key]['skill'] = $question->e;
                        }
                    }
                }
            }
            $result['html'] = (string)View::make('backend.reports.expanded_class_test_report',compact('resultArray','QuestionAnswerWeeknessSkills','student_id'));
            return $this->sendResponse($result);
        }else{
            return $this->sendError('Exam id & Student id required', 422);
        }
    }

    /**
     * Use : Get all students exams weekness find
     */
    public function getStudentsWeaknessDetails($request, $examId, $getAllStudentsWeaknessDetails){
        $reports = [];
        $nodeList = Nodes::where(cn::NODES_STATUS_COL,'active')->get();
        $nodeWeaknessList = array();
        $nodeWeaknessListCh = array();
        if (!empty($nodeList)) { 
            $nodeListToArray = $nodeList->toArray();
            $nodeWeaknessList = array_column($nodeListToArray,'weakness_name_en','id');
            $nodeWeaknessListCh = array_column($nodeListToArray,'weakness_name_cn','id');
        }
        
        $ExamData = Exam::find($examId);
        $AttemptExamDetails = AttemptExams::where(cn::ATTEMPT_EXAMS_STUDENT_STUDENT_ID,$request->student_id)->where(cn::ATTEMPT_EXAMS_EXAM_ID,$ExamData->id)->first();
        if(isset($AttemptExamDetails) && !empty($AttemptExamDetails)){
            $totalCorrectAnswer = $AttemptExamDetails->total_correct_answers;
            $totalNoOfQuestions = count(explode(',',$ExamData->question_ids));
            $reports[$ExamData->id]['exam_id'] = $ExamData->id;
            $reports[$ExamData->id]['level'] = $ExamData->title;
            $reports[$ExamData->id]['percentage'] = round((($totalCorrectAnswer * 100)/$totalNoOfQuestions),2);
        }else{
            $reports[$ExamData->id]['exam_id'] = $ExamData->id;
            $reports[$ExamData->id]['level'] = $ExamData->title;
            $reports[$ExamData->id]['percentage'] = 0;
        }

        // Find the weakness node(Examid) for current students
        foreach($reports as $value){
            if($value['percentage'] <= 75){
                $examId = $value['exam_id'];
                break;
            }
        }
        
        $QuestionAnswerWeeknessSkills = [];
        if(!empty($examId)){
            $AllWeakness = [];
            if(isset($getAllStudentsWeaknessDetails) && !empty($getAllStudentsWeaknessDetails)){
                foreach($getAllStudentsWeaknessDetails as $studentId){
                    $ExamData = Exam::find($examId);
                    if(!empty($ExamData->question_ids)){
                        $questionIds = explode(',',$ExamData->question_ids);
                        $QuestionList = Question::with('answers')->whereIn('id',$questionIds)->get();
                        if(isset($QuestionList) && !empty($QuestionList)){
                            foreach($QuestionList as $key => $question){
                                $AttemptExamData = AttemptExams::where('student_id',$request->student_id)->where('exam_id',$ExamData->id)->first();
                                $Answerdetail = $question->answers;
                                if(isset($AttemptExamData['question_answers'])){
                                    $filterattempQuestionAnswer = array_filter(json_decode($AttemptExamData['question_answers']), function ($var) use($question){
                                        if($var->question_id == $question['id']){
                                            return $var ?? [];
                                        }
                                    });
                                }
                                if(isset($filterattempQuestionAnswer) && !empty($filterattempQuestionAnswer)){
                                    foreach($filterattempQuestionAnswer as $fanswer){
                                        $AnswerData = Answer::where('question_id',$question->id)->first();
                                        if($fanswer->answer == $Answerdetail->{'correct_answer_'.$fanswer->language}){
                                        }else{
                                            $weaknessId = $AnswerData->{'answer'.$fanswer->answer.'_node_relation_id_'.$fanswer->language};
                                            if($weaknessId != 0 && isset($nodeWeaknessList[$weaknessId])){
                                                $QuestionAnswerWeeknessSkills[$studentId][] = $nodeWeaknessList[$weaknessId];
                                                $AllWeakness[] = strip_tags($nodeWeaknessList[$weaknessId]);
                                            }else{
                                                $arrayOfQuestion = explode('-',$question['question_code']);
                                                if(count($arrayOfQuestion) == 8){
                                                    unset($arrayOfQuestion[count($arrayOfQuestion)-1]);
                                                    $newQuestionCode = implode('-',$arrayOfQuestion);
                                                    $newQuestionData = Question::with('answers')->where('question_code',$newQuestionCode)->first();
                                                    $weaknessId = $newQuestionData->answers->{'answer'.$fanswer->answer.'_node_relation_id_'.$fanswer->language};
                                                    if($weaknessId != 0 && isset($nodeWeaknessList[$weaknessId])){
                                                        $QuestionAnswerWeeknessSkills[$studentId][] = $nodeWeaknessList[$weaknessId];
                                                        $AllWeakness[] = strip_tags($nodeWeaknessList[$weaknessId]);
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
        }
        $finalWeekness = [];
        if(!empty($QuestionAnswerWeeknessSkills) && !empty($AllWeakness)){
            // Set Counts All weakness
            $countAllWeakness = array_count_values($AllWeakness);
            // Get the Current user weakness
            $currentUserWeakness = array_count_values($QuestionAnswerWeeknessSkills[$request->student_id]);
            arsort($currentUserWeakness);
            if(isset($currentUserWeakness) && !empty($currentUserWeakness)){
                $commonWeakness = [];
                foreach($currentUserWeakness as $weaknessName => $userweakness){
                    $commonWeakness[$weaknessName][] = $countAllWeakness[$weaknessName];
                }
                arsort($commonWeakness);
                $finalWeekness = array_slice(array_keys($commonWeakness), 0, sizeof($commonWeakness));
            }
        }
        return $finalWeekness;
    }

    /**
     * USE : Student Class test report, correct and incorrect (table version, correct/wrong)
     */
    public function StudentClassTestResultCorrectIncorrectAnswers(Request $request){
        $ResultList = [];
        $SchoolList = School::all();
        $GradeList = Grades::all();
        $grade_id = '';
        $class_type_id = array();
        $studentList = array();
        $filter = 0;
        $GradeClassListData = array();
        if($this->isStudentLogin()){
            $currentLoggedSchoolId = $this->isStudentLogin();
            $currentUserId = Auth::user()->{cn::USERS_ID_COL};
            $ExamList = Exam::whereRaw("find_in_set($currentUserId,student_ids)")->where('status','publish')->get();
        }
       
        $QuestionSkills = [];
        $studentCount = 0;
        $ExamData = '';
        if(isset($request->filter)){
            if(isset($request->school_id)){
                $ExamData = Exam::where(cn::EXAM_TABLE_ID_COLS,$request->exam_id)->whereRaw("find_in_set($request->school_id,school_id)")->first();
            }else if($this->isStudentLogin()){
                $currentLoggedSchoolId = $this->isStudentLogin();
                $ExamData = Exam::where(cn::EXAM_TABLE_ID_COLS,$request->exam_id)->whereRaw("find_in_set($currentLoggedSchoolId,school_id)")->first();
            }else{
                $ExamData = Exam::find($request->exam_id);
            }

            if(!empty($ExamData)){
                if($this->isStudentLogin()){
                    $studentId = Auth::user()->{cn::USERS_ID_COL};
                    $AttemptExamData = AttemptExams::where('student_id',$studentId)->where('exam_id',$request->exam_id)->first();
                    if(isset($AttemptExamData) && !empty($AttemptExamData)){
                        $StudentDetail = User::find($studentId);
                        $ResultList[$studentId]['exam_id'] = $ExamData->id;
                        $ResultList[$studentId]['student_grade'] = $StudentDetail->grade_id ?? 0;
                        $ResultList[$studentId]['class_student_number'] = $StudentDetail->class_student_number ?? 'N/A';
                        $ResultList[$studentId]['student_number'] = $StudentDetail->id;
                        $ResultList[$studentId]['student_name'] = ($StudentDetail->name_en) ? $this->decrypt($StudentDetail->name_en) : $StudentDetail->name;
                        $ResultList[$studentId]['student_status'] = 'Active';
                        $ResultList[$studentId]['student_ability'] = $AttemptExamData->{cn::ATTEMPT_EXAMS_STUDENT_ABILITY_COL} ?? 'N/A';
                        $ResultList[$studentId]['student_normalize_ability'] = $this->getNormalizedAbility($AttemptExamData->{cn::ATTEMPT_EXAMS_STUDENT_ABILITY_COL}) ?? 'N/A';
                        $ResultList[$studentId]['countStudent'] = (++$studentCount);
                        $ResultList[$studentId]['total_correct_answer'] = $AttemptExamData->total_correct_answers;
                        $ResultList[$studentId]['exam_status'] = (($AttemptExamData->status) && $AttemptExamData->status == 1) ? 'Complete' : 'Pending';
                        $ResultList[$studentId]['completion_time'] = ($AttemptExamData->exam_taking_timing) ? $AttemptExamData->exam_taking_timing : '--';
                        if(!empty($ExamData->student_ids)){
                            $studentRank = $this->getStudentExamRanking($ExamData->id, explode(',',$ExamData->student_ids));
                        }
                        $ResultList[$studentId]['student_ranking'] = $studentRank[$studentId];
                        if(!empty($ExamData->question_ids)){
                            $questionIds = explode(',',$ExamData->question_ids);
                            $QuestionList = Question::with('answers')->whereIn('id',$questionIds)->get();
                            if(isset($QuestionList) && !empty($QuestionList)){
                                $ResultList[$studentId]['countQuestions'] = count($QuestionList);
                                foreach($QuestionList as $questionKey => $question){
                                    if(isset($question)){
                                        if(isset($AttemptExamData['question_answers'])){
                                            $filterattempQuestionAnswer = array_filter(json_decode($AttemptExamData['question_answers']), function ($var) use($question){
                                                if($var->question_id == $question['id']){
                                                    return $var ?? [];
                                                }
                                            });
                                        }
                                    }

                                    if(isset($filterattempQuestionAnswer) && !empty($filterattempQuestionAnswer)){
                                        foreach($filterattempQuestionAnswer as $fanswer){
                                            if($fanswer->answer == $question->answers->{'correct_answer_'.$fanswer->language}){
                                                $ResultList[$studentId][$questionKey] = 'true';
                                            }else{
                                                $ResultList[$studentId][$questionKey] = 'false';
                                            }
                                        }
                                    }else{
                                        $ResultList[$studentId][$questionKey] = 'false';
                                    }
                                    // Store exams skill array
                                    $QuestionSkills[$questionKey] = $question->e;
                                }
                            }
                        }
                    }
                }
            }
        }

        $studentsRanks = [];
        if(!empty($ResultList)){
            foreach($ResultList as $key => $result){
                $studentsRanks[$key] = $result['total_correct_answer'];
            }
            array_multisort($studentsRanks, SORT_DESC, $ResultList);
        }

        // Sorting array based on query sting params
        if(isset($_GET['sort_by_type']) && isset($_GET['sort_by_value'])){
            // Sorting based on student names
            if($_GET['sort_by_type'] == 'student_name'){
                if($_GET['sort_by_value'] == 'asc'){
                    usort($ResultList, $this->make_comparer(['student_name', SORT_DESC]));
                }else{
                    usort($ResultList, $this->make_comparer(['student_name', SORT_ASC]));
                }
            }else{
                // Sorting based on student ranking
                if($_GET['sort_by_value'] == 'asc'){
                    usort($ResultList, $this->make_comparer(['total_correct_answer', SORT_DESC]));
                }else{
                    usort($ResultList, $this->make_comparer(['total_correct_answer', SORT_ASC]));
                }
            }
        }else{
            usort($ResultList, $this->make_comparer(['student_name', SORT_ASC]));
        }
        return view('backend/reports/student_correct_incorrect_report',compact('SchoolList','GradeList','ExamList','ResultList','QuestionSkills','ExamData','studentsRanks','grade_id','class_type_id','GradeClassListData'));
    }

    /**
     * USE : Get the student progress reports
     */
    public function ProgressReport(Request $request){
        try{
            if(isset($request->ExamId) && !empty($request->ExamId)){
                $SchoolId = $request->SchoolId ?? Auth::user()->{cn::USERS_SCHOOL_ID_COL};
                // Check exam is assigning via peer-group
                $ExamGradeClassQuery =  ExamGradeClassMappingModel::select(DB::raw('group_concat(student_ids) as student_ids'))
                                        ->where([
                                            cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_CURRICULUM_YEAR_ID_COL  => $this->GetCurriculumYear(),
                                            cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_EXAM_ID_COL             => $request->ExamId,
                                            cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_SCHOOL_ID_COL           => $SchoolId
                                        ]);
                if(isset($request->PeerGroupId) && !empty($request->PeerGroupId)){
                    $ExamGradeClassQuery->where(cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_PEER_GROUP_ID_COL,$request->PeerGroupId);
                }

                // Check exam is assign via grade or class
                if(isset($request->GradeId) && !empty($request->ClassIds)){
                    $ExamGradeClassQuery->where(cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_GRADE_ID_COL,$request->GradeId)
                    ->whereIn(cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_CLASS_ID_COL,$request->ClassIds);
                }
                // Run query
                $ExamGradeClassData = $ExamGradeClassQuery->pluck(cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_STUDENT_IDS_COL);
                if(isset($ExamGradeClassData) && !empty($ExamGradeClassData)){
                    $StudentIds = explode(',',$ExamGradeClassData[0]);
                }

                $dataTable = '';
                // After getting all the assigned students
                if(isset($StudentIds) && !empty($StudentIds)){
                    foreach($StudentIds as $studentId){
                        // Get correct answer detail
                        $User = User::find($studentId);
                        $AttemptExamData =  AttemptExams::where([
                                                cn::ATTEMPT_EXAMS_EXAM_ID => $request->ExamId,
                                                cn::ATTEMPT_EXAMS_STUDENT_STUDENT_ID => $studentId,
                                            ])->first();
                        if(isset($User) && !empty($User)){
                            $student_name = $this->decrypt($User->{cn::USERS_NAME_EN_COL});
                            $classStudentNumber = ($User->CurriculumYearData[cn::USERS_CLASS_STUDENT_NUMBER]) ?? 'N/A';
                            if(app()->getLocale() == 'ch'){
                                $student_name = mb_convert_encoding($this->decrypt($User->{cn::USERS_NAME_CH_COL}), 'UTF-8', 'UTF-8');
                            }
                            if(isset($AttemptExamData) && !empty($AttemptExamData)){
                                $dataTable.='<tr><td>'.$student_name.'</td><td>'.$classStudentNumber.'</td><td>'.$User->email.'</td><td><span class="badge badge-success">Complete</span></td></tr>';
                            }else{
                                $dataTable.='<tr><td>'.$student_name.'</td><td>'.$classStudentNumber.'</td><td>'.$User->email.'</td><td><span class="badge badge-warning">Pending</span></td></tr>';
                            }
                        }
                    }
                }
                return $this->sendResponse($dataTable);
            }else{
                return $this->sendError('Please select exams', 422);
            }
        }catch(Exception $exception){
            return $this->sendError($exception->getMessage(), 404);
        }
    }

    /**
     * USE : Get the class ability analysis reports
     */
    public function ClassAbilityAnalysisReport(Request $request){
        $response = [];
        $studentAbility = [];
        $studentidlist = array();
        if(isset($request->ExamId) && !empty($request->ExamId)){

            $isGroup = (isset($isGroup) && !empty($isGroup)) ? true : false;

            $schoolId = $request->SchoolId ?? Auth::user()->{cn::USERS_SCHOOL_ID_COL};

            // Check exam is assigning via peer-group
            $ExamGradeClassQuery =  ExamGradeClassMappingModel::select(DB::raw('group_concat(student_ids) as student_ids'))
                                    ->where([
                                        cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_CURRICULUM_YEAR_ID_COL  => $this->GetCurriculumYear(),
                                        cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_EXAM_ID_COL             => $request->ExamId,
                                        cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_SCHOOL_ID_COL           => $schoolId
                                    ]);
            if(isset($request->PeerGroupId) && !empty($request->PeerGroupId)){
                $ExamGradeClassQuery->where(cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_PEER_GROUP_ID_COL,$request->PeerGroupId);
            }

            // Check exam is assign via grade or class
            if(isset($request->GradeId) && !empty($request->ClassIds)){
                $ExamGradeClassQuery->where(cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_GRADE_ID_COL,$request->GradeId)
                ->whereIn(cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_CLASS_ID_COL,$request->ClassIds);
            }
            // Run query
            $ExamGradeClassData = $ExamGradeClassQuery->pluck(cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_STUDENT_IDS_COL);
            if(isset($ExamGradeClassData) && !empty($ExamGradeClassData)){
                $StudentIds = explode(',',$ExamGradeClassData[0]);
            }

            // Get Other students
            if($this->isAdmin() || $this->isSchoolLogin() || $this->isPrincipalLogin()){
                $studentidlist = User::where(cn::USERS_SCHOOL_ID_COL,$schoolId)
                                    ->where(cn::USERS_ROLE_ID_COL,cn::STUDENT_ROLE_ID)
                                    ->pluck(cn::USERS_ID_COL)
                                    ->toArray();
            }

            if($this->isTeacherLogin()){
                $gradesListId = TeachersClassSubjectAssign::where([
                                    cn::TEACHER_CLASS_SUBJECT_CURRICULUM_YEAR_ID_COL => $this->GetCurriculumYear(),
                                    cn::TEACHER_CLASS_SUBJECT_TEACHER_ID_COL => Auth()->user()->{cn::USERS_ID_COL}
                                ])
                                ->pluck(cn::TEACHER_CLASS_SUBJECT_CLASS_ID_COL)
                                ->toArray();
                $gradeClass = TeachersClassSubjectAssign::where([
                                cn::TEACHER_CLASS_SUBJECT_CURRICULUM_YEAR_ID_COL => $this->GetCurriculumYear(),
                                cn::TEACHER_CLASS_SUBJECT_TEACHER_ID_COL => Auth()->user()->{cn::USERS_ID_COL}
                            ])
                            ->pluck(cn::TEACHER_CLASS_SUBJECT_CLASS_NAME_ID_COL)->toArray();
                if(isset($gradeClass) && !empty($gradeClass)){
                    $gradeClass = implode(',', $gradeClass);
                    $gradeClassId = explode(',',$gradeClass);
                }
                $studentidlist = User::where([
                                    cn::USERS_SCHOOL_ID_COL => $schoolId,
                                    cn::USERS_ROLE_ID_COL => cn::STUDENT_ROLE_ID
                                ])
                                ->get()
                                ->whereIn('CurriculumYearGradeId',$gradesListId)
                                ->whereIn('CurriculumYearClassId',$gradeClassId)
                                ->pluck(cn::USERS_ID_COL)
                                ->toArray();
            }

            // Merge Students
            $TeachersStudentIdList = array_intersect($StudentIds,$studentidlist);
            
            // Get Data based on types of graph option select
            switch ($request->graph_type) {
                case 'my-school':
                    // Calculation for my-class graph functionality
                    $examData = Exam::find($request->ExamId);
                    $totalQuestion = explode(',',$examData->question_ids);
                    if(isset($TeachersStudentIdList) && !empty($TeachersStudentIdList)){
                        foreach($TeachersStudentIdList as $studentid){
                            $ExamAttemptData =  AttemptExams::where([
                                                    cn::ATTEMPT_EXAMS_EXAM_ID => $request->ExamId,
                                                    cn::ATTEMPT_EXAMS_STUDENT_STUDENT_ID => $studentid
                                                ])
                                                ->first();
                            if(isset($ExamAttemptData) && !empty($ExamAttemptData)){
                                $studentAbility[] = $ExamAttemptData->student_ability;
                            }
                        }
                    }
                    $dataList2 = array();
                    
                    if(isset($studentidlist) && !empty($studentidlist)){
                        foreach ($studentidlist as $studentid_id) {
                            $ExamAttemptData =  AttemptExams::where(cn::ATTEMPT_EXAMS_EXAM_ID,$request->ExamId)
                                                ->where(cn::ATTEMPT_EXAMS_STUDENT_STUDENT_ID,$studentid_id)
                                                ->first();
                            if(isset($ExamAttemptData) && !empty($ExamAttemptData)){
                                $dataList2[] = $ExamAttemptData->student_ability;
                            }
                        }
                    }

                    // Call to ALP AI My School Ability Analysis Graph API
                    if(isset($studentAbility) && !empty($studentAbility)){
                        $requestPayload = new Request();
                        $requestPayload = $requestPayload->replace([
                            'data_list1' => array_values(array_map('floatval', $studentAbility)),
                            'data_list2' => array_values(array_map('floatval', $dataList2)),
                            "format" => "base64",
                            'labels' => $this->GetAiApiLabels(config()->get('aiapi.api.Plot_Analyze_My_School_Ability.uri'),$isGroup)
                        ]);
                        $response = $this->AIApiService->Plot_Analyze_My_School_Ability($requestPayload);
                    }
                break;
                case 'all-school':
                    // Calculation for my-class graph functionality
                    $examData = Exam::find($request->ExamId);
                    $totalQuestion = explode(',',$examData->question_ids);
                    if(isset($TeachersStudentIdList) && !empty($TeachersStudentIdList)){
                        foreach($TeachersStudentIdList as $studentid){
                            $ExamAttemptData =  AttemptExams::where(cn::ATTEMPT_EXAMS_EXAM_ID,$request->ExamId)
                                                ->where(cn::ATTEMPT_EXAMS_STUDENT_STUDENT_ID,$studentid)
                                                ->first();
                            if(isset($ExamAttemptData) && !empty($ExamAttemptData)){
                                $studentAbility[] = $ExamAttemptData->student_ability;
                            }
                        }
                    }
                    $dataList2 = array();
                    $studentidlist = User::where([
                                        cn::USERS_SCHOOL_ID_COL => $schoolId,
                                        cn::USERS_ROLE_ID_COL => cn::STUDENT_ROLE_ID
                                    ])
                                    ->pluck(cn::USERS_ID_COL)->toArray();
                    if(isset($studentidlist) && !empty($studentidlist)){
                        foreach($studentidlist as $studentid_id){
                            $ExamAttemptData = AttemptExams::where(cn::ATTEMPT_EXAMS_EXAM_ID,$request->ExamId)->where(cn::ATTEMPT_EXAMS_STUDENT_STUDENT_ID,$studentid_id)->first();
                            if(isset($ExamAttemptData) && !empty($ExamAttemptData)){
                                $dataList2[] = $ExamAttemptData->student_ability;
                            }
                        }
                    }

                    $dataList3 = array();
                    $student_id_all = explode(',',$examData->student_ids);
                    if(isset($student_id_all) && !empty($student_id_all)){
                        foreach ($student_id_all as $studentid_id) {
                            $ExamAttemptData =  AttemptExams::where([
                                                    cn::ATTEMPT_EXAMS_EXAM_ID => $request->ExamId,
                                                    cn::ATTEMPT_EXAMS_STUDENT_STUDENT_ID => $studentid_id
                                                ])->first();
                            if(isset($ExamAttemptData) && !empty($ExamAttemptData)){
                                $dataList3[] = $ExamAttemptData->student_ability;
                            }
                        }
                    }

                    // Call to ALP AI My School Ability Analysis Graph API
                    if(isset($studentAbility) && !empty($studentAbility)){
                        $requestPayload = new Request();
                        $requestPayload = $requestPayload->replace([
                            'data_list1' => array_values(array_map('floatval', $studentAbility)),
                            'data_list2' => array_values(array_map('floatval', $dataList2)),
                            'data_list3' => array_values(array_map('floatval', $dataList3)),
                            'labels' => $this->GetAiApiLabels(config()->get('aiapi.api.Plot_Analyze_All_Schools_Ability.uri'), $isGroup),
                            "format" => "base64"
                        ]);
                        $response = $this->AIApiService->Plot_Analyze_All_Schools_Ability($requestPayload);
                    }
                    break;
                default:  // My-class
                    // Calculation for my-class graph functionality
                    $examData = Exam::find($request->ExamId);
                    $totalQuestion = explode(',',$examData->question_ids);
                    if(isset($TeachersStudentIdList) && !empty($TeachersStudentIdList)){
                        foreach($TeachersStudentIdList as $studentid){
                            $ExamAttemptData =  AttemptExams::where([
                                                    cn::ATTEMPT_EXAMS_EXAM_ID => $request->ExamId,
                                                    cn::ATTEMPT_EXAMS_STUDENT_STUDENT_ID => $studentid
                                                ])->first();
                            if(isset($ExamAttemptData) && !empty($ExamAttemptData)){
                                $studentAbility[] = $ExamAttemptData->student_ability;
                            }
                        }
                    }
                    
                    // Call to ALP AI Performance Analysis Graph API
                    if(isset($studentAbility) && !empty($studentAbility)){
                        $requestPayload = new Request();
                        $requestPayload = $requestPayload->replace([
                            'data_list' => array_values(array_map('floatval', $studentAbility)),
                            "format" => "base64",
                            'labels' => $this->GetAiApiLabels(config()->get('aiapi.api.Plot_Analyze_My_Class_Ability.uri'),$isGroup)
                        ]);
                        $response = $this->AIApiService->Plot_Analyze_My_Class_Ability($requestPayload);
                    }
            }
        }
        return $this->sendResponse($response);
    }

    /**
     * USE : Get the test summary reports
     */
    public function TestSummaryReport(Request $request){
        if(isset($request->examId) && !empty($request->examId)){
            $SchoolId = $request->SchoolId ?? Auth::user()->{cn::USERS_SCHOOL_ID_COL};
            // Check exam is assigning via peer-group
            $ExamGradeClassQuery =  ExamGradeClassMappingModel::select(DB::raw('group_concat(student_ids) as student_ids'))
                                    ->where([
                                        cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_CURRICULUM_YEAR_ID_COL  => $this->GetCurriculumYear(),
                                        cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_EXAM_ID_COL             => $request->examId,
                                        cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_SCHOOL_ID_COL           => $SchoolId
                                    ]);
            if(isset($request->PeerGroupId) && !empty($request->PeerGroupId)){
                $ExamGradeClassQuery->where(cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_PEER_GROUP_ID_COL,$request->PeerGroupId);
            }

            // Check exam is assign via grade or class
            if(isset($request->GradeId) && !empty($request->ClassIds)){
                $ExamGradeClassQuery->where(cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_GRADE_ID_COL,$request->GradeId)
                ->whereIn(cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_CLASS_ID_COL,$request->ClassIds);
            }
            // Run query
            $ExamGradeClassData = $ExamGradeClassQuery->pluck(cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_STUDENT_IDS_COL);
            if(isset($ExamGradeClassData) && !empty($ExamGradeClassData)){
                $StudentIds = $ExamGradeClassData[0];
            }
        }

        $requestPayload = new Request();
        $requestPayload = $requestPayload->replace([
            'examId'                => $request->examId,
            'studentIds'            => $StudentIds,
            'classIds'              => $request->ClassIds,
            'groupIds'              => $request->PeerGroupId
        ]);
        $records = $this->getStudentResultSummary($requestPayload);
        $result = array();
        if(!empty( $records)){
            $result['html'] = (string)View::make('backend/teacher/student_result_summary',compact('records'));
            return $this->sendResponse($result);
        }else{
            return $this->SendError(__('languages.no_any_students_are_attempt_exams_please_wait_until_students_can_attempt_this_exams'),422);
        }
    }
}