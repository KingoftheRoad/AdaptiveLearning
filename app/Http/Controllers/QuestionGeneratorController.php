<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Constants\DbConstant as cn;
use App\Http\Services\AIApiService;
use App\Traits\Common;
use App\Traits\ResponseFormat;
use App\Exceptions\CustomException;
use App\Models\PreConfigurationDiffiltyLevel;
use App\Models\Strands;
use App\Models\LearningsUnits;
use App\Models\LearningsObjectives;
use App\Models\StrandUnitsObjectivesMappings;
use App\Models\Question;
use App\Models\Answer;
use App\Models\TeachersClassSubjectAssign;
use App\Models\GradeClassMapping;
use App\Models\Grades;
use App\Models\User;
use App\Models\PeerGroup;
use App\Models\Exam;
use App\Models\School;
use App\Models\GradeSchoolMappings;
use App\Models\PeerGroupMember;
use App\Models\ExamGradeClassMappingModel;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use App\Models\ExamSchoolMapping;
use Carbon\Carbon;
use App\Helpers\Helper;
use App\Http\Services\TeacherGradesClassService;
use App\Models\ExamCreditPointRulesMapping;

class QuestionGeneratorController extends Controller {

    // Load Common Traits
    use Common, ResponseFormat;

    protected $AIApiService;
    protected $currentUserSchoolId;
    protected $currentUserId;
    protected $MinimumQuestionPerSkill;
    protected $MaximumQuestionPerObjectives;
    protected $ExamSchoolMapping, $ExamGradeClassMappingModel;
    protected $Exam;
    protected $TeacherGradesClassService;
    protected $GradeClassMapping;
    protected $PeerGroup;
    protected $DefaultStudentOverAllAbility;

    public function __construct(){
        $this->AIApiService = new AIApiService();
        $this->MinimumQuestionPerSkill = $this->getGlobalConfiguration('no_of_questions_per_learning_skills') ?? 2;
        $this->MaximumQuestionPerObjectives = $this->getGlobalConfiguration('max_no_question_per_learning_objectives') ?? 5;
        $this->DefaultStudentOverAllAbility = 0.1;
        $this->ExamSchoolMapping = new ExamSchoolMapping;
        $this->Exam = new Exam;
        $this->ExamGradeClassMappingModel = new ExamGradeClassMappingModel;
        $this->TeacherGradesClassService = new TeacherGradesClassService;
        $this->GradeClassMapping = new GradeClassMapping;
        $this->PeerGroup = new PeerGroup;

        // Store global variable into current user school id
        $this->currentUserSchoolId = null;
        $this->middleware(function ($request, $next) {
            $this->currentUserSchoolId = Auth::user()->{cn::USERS_SCHOOL_ID_COL};
            $this->currentUserId = Auth::user()->{cn::USERS_ID_COL};
            return $next($request);
        });
    }

    public function GetCurrentSchoolAssignedTestPeerGroups($ExamId){
        $ExamData = $this->Exam->find($ExamId);
        $GetAssignedPeerGroupsIds = [];
        $GetAssignedPeerGroupsIds = $this->ExamGradeClassMappingModel->where([
                                        cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_EXAM_ID_COL => $ExamId,
                                        cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_SCHOOL_ID_COL => Auth::user()->{cn::USERS_SCHOOL_ID_COL}
                                    ])
                                    ->whereIn(cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_PEER_GROUP_ID_COL,explode(',',$ExamData->{cn::EXAM_TABLE_PEER_GROUP_IDS_COL}))
                                    ->pluck(cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_PEER_GROUP_ID_COL);
        if(!empty($GetAssignedPeerGroupsIds)){
            $GetAssignedPeerGroupsIds = $GetAssignedPeerGroupsIds->toArray();
        }
        return $GetAssignedPeerGroupsIds;
    }

    /**
     * USE : Get current school assigned student ids
     */
    public function GetCurrentSchoolAssignedStudentsList($ExamId){
        $GetAssignedStudentIds = [];
        $ExamGradeClassMappingModel = $this->ExamGradeClassMappingModel->where([
            cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_EXAM_ID_COL => $ExamId,
            cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_SCHOOL_ID_COL => Auth::user()->{cn::USERS_SCHOOL_ID_COL}
        ])
        ->pluck(cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_STUDENT_IDS_COL);
        if(!empty($ExamGradeClassMappingModel)){
            foreach($ExamGradeClassMappingModel as $students){
                if(!empty($students)){
                    $GetAssignedStudentIds[] = explode(',',$students);
                }
            }
        }
        if(!empty($GetAssignedStudentIds)){
            $GetAssignedStudentIds = $this->array_flatten($GetAssignedStudentIds);
        }
        return $GetAssignedStudentIds;
    }

    /**
     * USE : superAdminGenerateTestQuestion
     */
    public function superAdminGenerateTestQuestion(Request $request){
        $difficultyLevels = PreConfigurationDiffiltyLevel::all();
        $strandsList = [];
        $LearningUnits = [];
        $LearningObjectives = [];
        $PeerGroupList = [];
        $schoolList = [];
        $RequiredQuestionPerSkill = [];
        $RequiredQuestionPerSkill = [
            'minimum_question_per_skill' => $this->MinimumQuestionPerSkill,
            'maximum_question_per_skill' => $this->MaximumQuestionPerObjectives
        ];
        $Schools = School::all();
        // Get Time slot
        $timeSlots = $this->getTimeSlot();
        $strandsList = Strands::all();
        if(!empty($strandsList)){
            $LearningUnits = LearningsUnits::where(cn::LEARNING_UNITS_STRANDID_COL, $strandsList[0]->{cn::STRANDS_ID_COL})->get();
            if(!empty($LearningUnits)){
                // $LearningObjectives = LearningsObjectives::whereIn(cn::LEARNING_OBJECTIVES_LEARNING_UNITID_COL, $LearningUnits->pluck(cn::LEARNING_OBJECTIVES_ID_COL))->get();
                $LearningObjectives = LearningsObjectives::IsAvailableQuestion()->whereIn(cn::LEARNING_OBJECTIVES_LEARNING_UNITID_COL, $LearningUnits->pluck(cn::LEARNING_OBJECTIVES_ID_COL))->get();
            }
        }

        // Get the school list
        $schoolList = School::where(cn::SCHOOL_SCHOOL_STATUS,'active')->get();
        if($request->isMethod('post')){
            $timeDuration = null;
            $questionIds = '';
            $difficulty_lvl = '';
            $schoolIds='';
            $learningObjectivesConfigurations = '';
            if(!isset($request->use_of_modes)){
                return back()->with('error_msg', __('languages.select_mode'));
            }

            if(isset($request->title) && $request->title == ""){
                return back()->with('error_msg', __('languages.problem_was_occur_please_try_again'));
            }

            if(isset($request->schoolIds) && !empty($request->schoolIds)){
                $schoolIds = implode(',',$request->schoolIds);
            }else{
                return back()->with('error_msg', __('languages.no_available_school'));
            }

            if(isset($request->qIndex) && !empty($request->qIndex)){
                $questionIds = implode(',',$request->qIndex);
            }else{
                return back()->with('error_msg', __('languages.test_template_management.question_not_found_add_question'));
            }

            if(isset($request->difficulty_lvl) && !empty($request->difficulty_lvl)){
                $difficulty_lvl = implode(',',$request->difficulty_lvl);
            }

            if(isset($request->learning_unit) && !empty($request->learning_unit)){
                $learningObjectivesConfigurations=json_encode($request->learning_unit);
            }

            $report_date = $this->DateConvertToYMD($request->end_date);
            if($request->report_date == 'after_submit'){
                $report_date = Carbon::now();
            }else{
                $report_date = $this->DateConvertToYMD($request->custom_date);
            }
            $examData = [
                cn::EXAM_TYPE_COLS                              => $request->test_type,
                cn::EXAM_REFERENCE_NO_COL                       => $this->GetMaxReferenceNumberExam($request->test_type),
                cn::EXAM_TABLE_USE_OF_MODE_COLS                 => $request->use_of_modes,
                cn::EXAM_TABLE_TITLE_COLS                       => $request->title,
                cn::EXAM_TABLE_FROM_DATE_COLS                   => $this->DateConvertToYMD($request->start_date),
                cn::EXAM_TABLE_TO_DATE_COLS                     => $this->DateConvertToYMD($request->end_date),
                cn::EXAM_TABLE_RESULT_DATE_COLS                 => $report_date,
                cn::EXAM_TABLE_PUBLISH_DATE_COL                 => $this->DateConvertToYMD($request->start_date),
                cn::EXAM_TABLE_START_TIME_COL                   => $request->start_time,
                cn::EXAM_TABLE_END_TIME_COL                     => $request->end_time,
                cn::EXAM_TABLE_REPORT_TYPE_COLS                 => $request->report_date,
                cn::EXAM_TABLE_TIME_DURATIONS_COLS              => $timeDuration,
                cn::EXAM_TABLE_QUESTION_IDS_COL                 => $questionIds,
                cn::EXAM_TABLE_SCHOOL_COLS                      => $schoolIds,
                cn::EXAM_TABLE_IS_UNLIMITED                     => ($request->test_type == 1) ? 1 : 0,
                cn::EXAM_TABLE_SELF_LEARNING_TEST_TYPE_COL      => null,
                cn::EXAM_TABLE_NO_OF_TRIALS_PER_QUESTIONS_COL   => $request->no_of_trials_per_question,
                cn::EXAM_TABLE_DIFFICULTY_MODE_COL              => $request->difficulty_mode,
                cn::EXAM_TABLE_DIFFICULTY_LEVELS_COL            => $difficulty_lvl,
                cn::EXAM_TABLE_IS_DISPLAY_HINTS_COL             => $request->display_hints,
                cn::EXAM_TABLE_IS_DISPLAY_FULL_SOLUTIONS_COL    => $request->display_full_solution,
                cn::EXAM_TABLE_IS_DISPLAY_PER_ANSWER_HINTS_COL  => $request->display_pr_answer_hints,
                cn::EXAM_TABLE_IS_RANDOMIZED_ANSWERS_COL        => $request->randomize_answer,
                cn::EXAM_TABLE_IS_RANDOMIZED_ORDER_COL          => $request->randomize_order,
                cn::EXAM_TABLE_LEARNING_OBJECTIVES_CONFIGURATIONS_COL => $learningObjectivesConfigurations,
                cn::EXAM_TABLE_CREATED_BY_COL                   => $this->LoggedUserId(),
                cn::EXAM_TABLE_CREATED_BY_USER_COL              => 'super_admin',
                cn::EXAM_TABLE_STATUS_COLS                      => ($request->has('save_and_publish')) ? 'publish' : 'draft'
            ];
            $this->StoreAuditLogFunction($examData,'Exam',cn::EXAM_TABLE_ID_COLS,'','Create Exam',cn::EXAM_TABLE_NAME,'');
            $exams = Exam::create($examData);
            if($exams){
                if($request->use_of_modes == 1){
                    // Create Exam and school Mapping
                    $this->CreateExamSchoolMapping($request->schoolIds,$exams->{cn::EXAM_TABLE_ID_COLS}, $request);
                }
                if($request->use_of_modes == 2){ // If use of mode 2 then we have to create 1 main entry and create sub child entry
                    $parentExamId = $exams->{cn::EXAM_TABLE_ID_COLS};
                    if(isset($request->schoolIds) && !empty($request->schoolIds)){
                        foreach($request->schoolIds as $schoolId){
                            $userId = User::where([
                                        cn::USERS_ROLE_ID_COL => cn::SCHOOL_ROLE_ID,
                                        cn::USERS_SCHOOL_ID_COL => $schoolId
                                    ])->first();
                            if(!empty($userId)){
                                $examData[cn::EXAM_REFERENCE_NO_COL] = $this->GetMaxReferenceNumberExam($request->test_type);
                                $examData[cn::EXAM_TABLE_PARENT_EXAM_ID_COLS] = $parentExamId;
                                $examData[cn::EXAM_TABLE_SCHOOL_COLS] = $schoolId;
                                $examData[cn::EXAM_TABLE_CREATED_BY_COL] = $userId->{cn::USERS_ID_COL};
                                $examData[cn::EXAM_TABLE_CREATED_BY_USER_COL] = 'super_admin';
                                $exams = Exam::create($examData);
                                if($exams){
                                    $this->CreateExamSchoolMapping(array($schoolId),$exams->{cn::EXAM_TABLE_ID_COLS}, $request);
                                }
                            }
                        }
                    }
                }
                return redirect()->route('question-wizard')->with('success_msg', __('languages.exam_added_successfully'));
            }else{
                return back()->with('error_msg', __('languages.problem_was_occur_please_try_again'));
            }
        }
        return view('backend.question_generator.admin.create_test',compact('schoolList','difficultyLevels','strandsList','LearningUnits','LearningObjectives','timeSlots','RequiredQuestionPerSkill'));
    }

    /**
     * USE : Super Admin Question Generator Edit
     */
    public function superAdminGenerateTestQuestionEdit(Request $request,$id){
        $exam = Exam::find($id);
        if(!empty($exam)){
            $difficultyLevels = PreConfigurationDiffiltyLevel::all();
            $strandsList = [];
            $LearningUnits = [];
            $LearningObjectives = [];
            $PeerGroupList = [];
            $schoolList = [];
            $RequiredQuestionPerSkill = [];

            $RequiredQuestionPerSkill = [
                'minimum_question_per_skill' => $this->MinimumQuestionPerSkill,
                'maximum_question_per_skill' => $this->MaximumQuestionPerObjectives
            ];

            $Schools = School::all();
            // Get Time slot
            $timeSlots = $this->getTimeSlot();
            $SelectedStrands = array();
            $SelectedLearningUnit = array();
            $strandsList = Strands::all();
            $learningObjectivesConfiguration=array();
            if(isset($exam->learning_objectives_configuration) && !empty($exam->learning_objectives_configuration)){
                $learningObjectivesConfiguration = json_decode($exam->learning_objectives_configuration,true);
                $SelectedLearningUnit = array_keys($learningObjectivesConfiguration);
                $learning_objectives_configuration = array_keys($learningObjectivesConfiguration);
                $LearningUnitsData = LearningsUnits::whereIn(cn::LEARNING_UNITS_ID_COL,$learning_objectives_configuration)->pluck(cn::LEARNING_UNITS_STRANDID_COL)->toArray();
                $SelectedStrands = array_unique($LearningUnitsData);
            }
            $questionDataArray = array();
            if(isset($exam->question_ids) && !empty($exam->question_ids)){
                $questionDataArray = Question::with(['answers','PreConfigurationDifficultyLevel','objectiveMapping'])->whereIn(cn::QUESTION_TABLE_ID_COL,explode(',',$exam->question_ids))->get()->toArray();
            }
            if(!empty($SelectedStrands)){
                $LearningUnits = LearningsUnits::whereIn(cn::LEARNING_UNITS_STRANDID_COL, $SelectedStrands)->get();
                if(!empty($LearningUnits)){
                    // $LearningObjectives = LearningsObjectives::whereIn(cn::LEARNING_OBJECTIVES_LEARNING_UNITID_COL, $SelectedLearningUnit)->get();
                    $LearningObjectives = LearningsObjectives::IsAvailableQuestion()->whereIn(cn::LEARNING_OBJECTIVES_LEARNING_UNITID_COL, $SelectedLearningUnit)->get();
                }
            }

            // Get the school list
            $schoolList = School::where(cn::SCHOOL_SCHOOL_STATUS,'active')->get();
            if($request->isMethod('post')){
                $timeDuration = null;
                $questionIds = '';
                $difficulty_lvl = '';
                $schoolIds = '';
                $learningObjectivesConfigurations = '';
                if(isset($request->title) && $request->title == ""){
                    return back()->with('error_msg', __('languages.problem_was_occur_please_try_again'));
                }

                if(isset($request->schoolIds) && !empty($request->schoolIds)){
                    $schoolIds = implode(',',$request->schoolIds);
                }else{
                    return back()->with('error_msg', __('languages.no_available_school'));
                }

                if(isset($request->qIndex) && !empty($request->qIndex)){
                    $questionIds = implode(',',$request->qIndex);
                }else{
                    return back()->with('error_msg', __('languages.test_template_management.question_not_found_add_question'));
                }

                if(isset($request->difficulty_lvl) && !empty($request->difficulty_lvl)){
                    $difficulty_lvl = implode(',',$request->difficulty_lvl);
                }
                
                if(isset($request->learning_unit) && !empty($request->learning_unit)){
                    $learningObjectivesConfigurations = json_encode($request->learning_unit);
                }

                $report_date = $this->DateConvertToYMD($request->end_date);
                if($request->report_date == 'after_submit'){
                    $report_date = Carbon::now();
                }else{
                    $report_date = $this->DateConvertToYMD($request->custom_date);
                }

                $examData = [
                    cn::EXAM_TYPE_COLS =>$request->test_type,
                    cn::EXAM_TABLE_TITLE_COLS => $request->title,
                    cn::EXAM_TABLE_FROM_DATE_COLS => $this->DateConvertToYMD($request->start_date),
                    cn::EXAM_TABLE_TO_DATE_COLS => $this->DateConvertToYMD($request->end_date),
                    cn::EXAM_TABLE_RESULT_DATE_COLS => $report_date,
                    cn::EXAM_TABLE_PUBLISH_DATE_COL => $this->DateConvertToYMD($request->start_date),
                    cn::EXAM_TABLE_START_TIME_COL => $request->start_time,
                    cn::EXAM_TABLE_END_TIME_COL => $request->end_time,
                    cn::EXAM_TABLE_REPORT_TYPE_COLS => $request->report_date,
                    cn::EXAM_TABLE_TIME_DURATIONS_COLS => $timeDuration,
                    cn::EXAM_TABLE_QUESTION_IDS_COL => $questionIds,
                    cn::EXAM_TABLE_SCHOOL_COLS => $schoolIds,
                    cn::EXAM_TABLE_IS_UNLIMITED => ($request->test_type == 1) ? 1 : 0,
                    cn::EXAM_TABLE_SELF_LEARNING_TEST_TYPE_COL => null,
                    cn::EXAM_TABLE_NO_OF_TRIALS_PER_QUESTIONS_COL => $request->no_of_trials_per_question,
                    cn::EXAM_TABLE_DIFFICULTY_MODE_COL => $request->difficulty_mode,
                    cn::EXAM_TABLE_DIFFICULTY_LEVELS_COL => $difficulty_lvl,
                    cn::EXAM_TABLE_IS_DISPLAY_HINTS_COL => $request->display_hints,
                    cn::EXAM_TABLE_IS_DISPLAY_FULL_SOLUTIONS_COL => $request->display_full_solution,
                    cn::EXAM_TABLE_IS_DISPLAY_PER_ANSWER_HINTS_COL => $request->display_pr_answer_hints,
                    cn::EXAM_TABLE_IS_RANDOMIZED_ANSWERS_COL => $request->randomize_answer,
                    cn::EXAM_TABLE_IS_RANDOMIZED_ORDER_COL => $request->randomize_order,
                    cn::EXAM_TABLE_LEARNING_OBJECTIVES_CONFIGURATIONS_COL => $learningObjectivesConfigurations,
                    cn::EXAM_TABLE_STATUS_COLS => ($request->has('save_and_publish')) ? 'publish' : 'draft'
                ];
                $this->StoreAuditLogFunction($examData,'Exam',cn::EXAM_TABLE_ID_COLS,$id,'Update Exam',cn::EXAM_TABLE_NAME,'');
                $exams = Exam::find($id)->update($examData);
                if($exams){
                    if($exam->{cn::EXAM_TABLE_USE_OF_MODE_COLS} == 1){
                        // Create Exam and school Mapping
                        $this->UpdateExamSchoolMapping($request->schoolIds,$id, $request); // $id = Exam Id
                    }

                    if($exam->{cn::EXAM_TABLE_USE_OF_MODE_COLS} == 2){
                        $parentExamId = $exam->{cn::EXAM_TABLE_ID_COLS};
                        if(isset($request->schoolIds) && !empty($request->schoolIds)){
                            $differenceSchoolId = array_merge(array_diff(explode(',',$exam->{cn::EXAM_TABLE_SCHOOL_COLS}), $request->schoolIds), array_diff($request->schoolIds, explode(',',$exam->{cn::EXAM_TABLE_SCHOOL_COLS})));
                            if(isset($differenceSchoolId) && !empty($differenceSchoolId)){
                                foreach($differenceSchoolId as $schoolId){
                                    $userId = User::where([cn::USERS_ROLE_ID_COL=>cn::SCHOOL_ROLE_ID,cn::USERS_SCHOOL_ID_COL => $schoolId])->first();
                                    if(!empty($userId)){
                                        if(Exam::where(cn::EXAM_TABLE_PARENT_EXAM_ID_COLS,$parentExamId)->where(cn::EXAM_TABLE_SCHOOL_COLS,$schoolId)->doesntExist()){
                                            $examData[cn::EXAM_TABLE_USE_OF_MODE_COLS] = 2;
                                            $examData[cn::EXAM_TABLE_PARENT_EXAM_ID_COLS] = $parentExamId;
                                            $examData[cn::EXAM_TABLE_SCHOOL_COLS] = $schoolId;
                                            $examData[cn::EXAM_TABLE_CREATED_BY_COL] = $userId->{cn::USERS_ID_COL};
                                            $examData[cn::EXAM_TABLE_CREATED_BY_USER_COL] = 'super_admin';
                                            $exams = Exam::create($examData);
                                            $this->UpdateExamSchoolMapping(array($schoolId),$exams->id, $request);
                                        }else{
                                            $existingExam = Exam::where(cn::EXAM_TABLE_PARENT_EXAM_ID_COLS,$parentExamId)->where(cn::EXAM_TABLE_SCHOOL_COLS,$schoolId)->first();
                                            $delete = Exam::find($existingExam->{cn::EXAM_TABLE_ID_COLS})->delete();
                                            $this->ExamSchoolMapping->where([cn::EXAM_SCHOOL_MAPPING_EXAM_ID_COL => $existingExam->id, cn::EXAM_SCHOOL_MAPPING_SCHOOL_ID_COL => $schoolId])->delete();
                                        }
                                    }
                                }
                            }else{
                                // Get existing school ids
                                $existingSchoolIds = (isset($exam->{cn::EXAM_TABLE_SCHOOL_COLS})) ? explode(',',$exam->{cn::EXAM_TABLE_SCHOOL_COLS}) : [];
                                foreach($existingSchoolIds as $schoolId){
                                    $userId = User::where([cn::USERS_ROLE_ID_COL=>cn::SCHOOL_ROLE_ID,cn::USERS_SCHOOL_ID_COL => $schoolId])->first();
                                    if(!empty($userId)){
                                        $examData[cn::EXAM_TABLE_USE_OF_MODE_COLS] = 2;
                                        $examData[cn::EXAM_TABLE_PARENT_EXAM_ID_COLS] = $parentExamId;
                                        $examData[cn::EXAM_TABLE_SCHOOL_COLS] = $schoolId;
                                        $examData[cn::EXAM_TABLE_CREATED_BY_COL] = $userId->{cn::USERS_ID_COL};
                                        $examData[cn::EXAM_TABLE_CREATED_BY_USER_COL] = 'super_admin';
                                        $exams = Exam::where(cn::EXAM_TABLE_SCHOOL_COLS,$schoolId)->where(cn::EXAM_TABLE_PARENT_EXAM_ID_COLS,$id)->update($examData);
                                    }
                                }
                            }
                            // $examData[cn::EXAM_TABLE_SCHOOL_COLS] = implode(',',$request->schoolIds);
                            // $examData[cn::EXAM_TABLE_CREATED_BY_COL] = Auth::user()->{cn::USERS_ID_COL};
                            // $examData[cn::EXAM_TABLE_PARENT_EXAM_ID_COLS] = NULL;
                            // $exams = Exam::where([
                            //     cn::EXAM_TABLE_PARENT_EXAM_ID_COLS => $id,
                            //     cn::EXAM_TABLE_USE_OF_MODE_COLS => 2,
                            //     cn::EXAM_TABLE_PARENT_EXAM_ID_COLS => NULL
                            // ])->update($examData);
                        }
                    }
                    return redirect()->route('question-wizard')->with('success_msg', __('languages.exam_updated_successfully'));
                }else{
                    return back()->with('error_msg', __('languages.problem_was_occur_please_try_again'));
                }
            }
            return view('backend.question_generator.admin.edit_test',compact('schoolList','difficultyLevels','strandsList','LearningUnits','LearningObjectives','timeSlots',
            'RequiredQuestionPerSkill','exam','learningObjectivesConfiguration','SelectedStrands','SelectedLearningUnit','questionDataArray'));
        }else{
            return back()->with('error_msg', __('languages.data_not_found'));
        }
    }

    /**
     * Use : If Admin can test assign to schools
     */
    public function CreateExamSchoolMapping($SchoolIds, $examId, $request){
        if(isset($SchoolIds) && !empty($SchoolIds)){
            $ExamStatus = $this->GetExamStatus($request, $examId);
            foreach($SchoolIds as $SchoolId){
                if($this->ExamSchoolMapping->where([cn::EXAM_SCHOOL_MAPPING_SCHOOL_ID_COL => $SchoolId,cn::EXAM_SCHOOL_MAPPING_EXAM_ID_COL => $examId])->exists()){
                    $this->ExamSchoolMapping->where([cn::EXAM_SCHOOL_MAPPING_SCHOOL_ID_COL => $SchoolId,cn::EXAM_SCHOOL_MAPPING_EXAM_ID_COL => $examId])
                    ->Update([
                        cn::EXAM_SCHOOL_MAPPING_SCHOOL_ID_COL => $SchoolId,
                        cn::EXAM_SCHOOL_MAPPING_EXAM_ID_COL => $examId,
                        cn::EXAM_SCHOOL_MAPPING_STATUS_COL => $ExamStatus
                    ]);
                }else{
                    $this->ExamSchoolMapping->create([
                        cn::EXAM_SCHOOL_MAPPING_SCHOOL_ID_COL => $SchoolId,
                        cn::EXAM_SCHOOL_MAPPING_EXAM_ID_COL => $examId,
                        cn::EXAM_SCHOOL_MAPPING_STATUS_COL => $ExamStatus
                    ]);
                }
            }
        }
    }

    /**
     * Use : If Admin can Add / Delete assign to test
     */
    public function UpdateExamSchoolMapping($SchoolIds, $examId, $request){
        if(isset($SchoolIds) && !empty($SchoolIds)){
            $ExamStatus = $this->GetExamStatus($request, $examId);
            if(!empty($SchoolIds)){
                foreach($SchoolIds as $SchoolId){
                    $ExamSchoolMappingData = $this->ExamSchoolMapping->where([cn::EXAM_SCHOOL_MAPPING_SCHOOL_ID_COL => $SchoolId,cn::EXAM_SCHOOL_MAPPING_EXAM_ID_COL => $examId])->withTrashed()->first();
                    if(!empty($ExamSchoolMappingData)){
                        $this->ExamSchoolMapping->where(cn::EXAM_SCHOOL_MAPPING_ID_COL,$ExamSchoolMappingData->id)->withTrashed()
                        ->Update([
                            cn::EXAM_SCHOOL_MAPPING_SCHOOL_ID_COL => $SchoolId,
                            cn::EXAM_SCHOOL_MAPPING_EXAM_ID_COL => $examId,
                            cn::EXAM_SCHOOL_MAPPING_STATUS_COL => $ExamStatus,
                            cn::DELETED_AT_COL => null
                        ]);
                    }else{
                        $this->ExamSchoolMapping->create([
                            cn::EXAM_SCHOOL_MAPPING_SCHOOL_ID_COL => $SchoolId,
                            cn::EXAM_SCHOOL_MAPPING_EXAM_ID_COL => $examId,
                            cn::EXAM_SCHOOL_MAPPING_STATUS_COL => $ExamStatus
                        ]);
                    }
                }
            }
        }
    }

    /**
     * USE : schoolGenerateTestQuestion add
     */
    public function schoolGenerateTestQuestion(Request $request){
        $difficultyLevels = PreConfigurationDiffiltyLevel::all();
        $strandsList = [];
        $LearningUnits = [];
        $LearningObjectives = [];
        $PeerGroupList = [];
        $schoolList = [];
        $RequiredQuestionPerSkill = [];
        $RequiredQuestionPerSkill = [
            'minimum_question_per_skill' => $this->MinimumQuestionPerSkill,
            'maximum_question_per_skill' => $this->MaximumQuestionPerObjectives
        ];
        $Schools = School::all();
        // Get Time slot
        $timeSlots = $this->getTimeSlot();
        $strandsSelectd = array();
        $strandsList = Strands::all();
        $learningObjectivesConfiguration = array();
        if(!empty($strandsList)){
            $LearningUnits = LearningsUnits::where(cn::LEARNING_UNITS_STRANDID_COL, $strandsList[0]->{cn::STRANDS_ID_COL})->get();
            if(!empty($LearningUnits)){
                // $LearningObjectives = LearningsObjectives::whereIn(cn::LEARNING_OBJECTIVES_LEARNING_UNITID_COL, $LearningUnits->pluck(cn::LEARNING_OBJECTIVES_ID_COL))->get();
                $LearningObjectives = LearningsObjectives::IsAvailableQuestion()->whereIn(cn::LEARNING_OBJECTIVES_LEARNING_UNITID_COL, $LearningUnits->pluck(cn::LEARNING_OBJECTIVES_ID_COL))->get();
            }
        }
        if($this->isTeacherLogin()){
            $schoolId = $this->isTeacherLogin();
            // Get Teachers Grades
            $gradesList = TeachersClassSubjectAssign::with('getClass')->where([cn::TEACHER_CLASS_SUBJECT_TEACHER_ID_COL => Auth()->user()->{cn::USERS_ID_COL}])->get()->unique(cn::TEACHER_CLASS_SUBJECT_CLASS_ID_COL);
            $gradeid = TeachersClassSubjectAssign::where([cn::TEACHER_CLASS_SUBJECT_TEACHER_ID_COL => Auth()->user()->{cn::USERS_ID_COL}])->pluck(cn::TEACHER_CLASS_SUBJECT_CLASS_ID_COL)->toArray();
            $gradeClass = TeachersClassSubjectAssign::where([cn::TEACHER_CLASS_SUBJECT_TEACHER_ID_COL => Auth()->user()->{cn::USERS_ID_COL}])->pluck(cn::TEACHER_CLASS_SUBJECT_CLASS_NAME_ID_COL)->toArray();
            if(isset($gradeClass) && !empty($gradeClass)){
                $gradeClass = implode(',', $gradeClass);
                $gradeClassId = explode(',',$gradeClass);
            }
            if(empty($gradeClassId)){
                return redirect('question-wizard')->with('error_msg', __('languages.grade_and_class_not_assign'));
            }
            $GradeClassData = Grades::with(['classes' => fn($query) => $query->whereIn(cn::GRADE_CLASS_MAPPING_ID_COL,$gradeClassId)->where([cn::GRADE_CLASS_MAPPING_SCHOOL_ID_COL => $this->isTeacherLogin()])])->whereIn('id',$gradeid)->get();

            // Get Peer Group List
            $PeerGroupList = PeerGroup::where([cn::PEER_GROUP_CREATED_BY_USER_ID_COL => Auth()->user()->{cn::USERS_ID_COL}, cn::PEER_GROUP_STATUS_COL => '1'])->get();

            // get student list
            $StudentList = User::whereIn(cn::USERS_GRADE_ID_COL,$gradeid)
                        ->whereIn(cn::USERS_CLASS_ID_COL,$gradeClassId)
                        ->where([cn::USERS_ROLE_ID_COL => 3,cn::USERS_STATUS_COL => 'active'])
                        ->get();
        }
        if($this->isSchoolLogin() || $this->isPrincipalLogin()){
            if($this->isSchoolLogin()){
                $schoolId = $this->isSchoolLogin();
            }
            if($this->isPrincipalLogin()){
                $schoolId = $this->isPrincipalLogin();
            }                
            $GradeMapping = GradeSchoolMappings::with('grades')->where(cn::GRADES_MAPPING_SCHOOL_ID_COL,$schoolId)->get()->pluck(cn::GRADES_MAPPING_GRADE_ID_COL);
            $gradeClass = GradeClassMapping::where(cn::GRADE_CLASS_MAPPING_SCHOOL_ID_COL,$schoolId)->whereIn(cn::GRADE_CLASS_MAPPING_GRADE_ID_COL,$GradeMapping)->pluck(cn::GRADE_CLASS_MAPPING_ID_COL)->toArray();
            if(isset($gradeClass) && !empty($gradeClass)){
                $gradeClass = implode(',', $gradeClass);
                $gradeClassId = explode(',',$gradeClass);
            }
            $GradeClassData = Grades::with(['classes' => fn($query) => $query->whereIn(cn::GRADE_CLASS_MAPPING_ID_COL,$gradeClassId)->where([cn::GRADE_CLASS_MAPPING_SCHOOL_ID_COL => $schoolId])])->whereIn(cn::GRADES_ID_COL,$GradeMapping)->get();
            
            // get student list
            $StudentList = User::where(cn::USERS_SCHOOL_ID_COL,Auth::user()->{cn::USERS_SCHOOL_ID_COL})->where(cn::USERS_ROLE_ID_COL,'=',cn::STUDENT_ROLE_ID)->with('grades')->get();

            // Get Peer Group List
            $PeerGroupList = PeerGroup::where([cn::PEER_GROUP_SCHOOL_ID_COL => $schoolId, cn::PEER_GROUP_STATUS_COL => '1'])->get();
        }

        // Get the school list
        $schoolList = School::where(cn::SCHOOL_SCHOOL_STATUS,'active')->get();
        if($request->isMethod('post')){
            $timeDuration = null;
            $questionIds = '';
            $studentIds = '';
            $difficulty_lvl = '';
            $schoolId = '';
            if($this->isSchoolLogin()){
                $schoolId = $this->isSchoolLogin();
            }
            if($this->isPrincipalLogin()){
                $schoolId = $this->isPrincipalLogin();
            }
            if($this->isTeacherLogin()){
                $schoolId = $this->isTeacherLogin();
            }
            $learningObjectivesConfigurations = '';
            if(isset($request->title) && $request->title == ""){
                return back()->with('error_msg', __('languages.problem_was_occur_please_try_again'));
            }
            if(isset($request->qIndex) && !empty($request->qIndex)){
                $questionIds = implode(',',$request->qIndex);
            }else{
                return back()->with('error_msg', __('languages.test_template_management.question_not_found_add_question'));
            }

            if(isset($request->difficulty_lvl) && !empty($request->difficulty_lvl)){
                $difficulty_lvl = implode(',',$request->difficulty_lvl);
            }

            if(isset($request->learning_unit) && !empty($request->learning_unit)){
                $learningObjectivesConfigurations=json_encode($request->learning_unit);
            }
            $report_date = $this->DateConvertToYMD($request->end_date);
            if($request->report_date == 'after_submit'){
                $report_date = Carbon::now();
            }else{
                $report_date = $this->DateConvertToYMD($request->custom_date);
            }

            // If School Admin / Teacher selected peer group
            $peerGroupIds = '';
            if(isset($request->peerGroupIds) && !empty($request->peerGroupIds)){
                $peerGroupIds = implode(',',$request->peerGroupIds);
                $PeerGroupList = PeerGroupMember::whereIn(cn::PEER_GROUP_MEMBERS_PEER_GROUP_ID_COL,$request->peerGroupIds)->pluck(cn::PEER_GROUP_MEMBERS_MEMBER_ID_COL)->toArray();
                if(isset($PeerGroupList) && !empty($PeerGroupList)){
                    $studentIds = implode(',',array_unique($PeerGroupList));
                }else{
                    $peerGroupIds = '';
                }
            }
            // If School Admin / Teacher selected Grade-Class
            if(isset($request->studentIds) && !empty($request->studentIds)){
                $studentIds = implode(',',$request->studentIds);
            }

            $examData = [
                cn::EXAM_TYPE_COLS =>$request->test_type,
                cn::EXAM_REFERENCE_NO_COL => $this->GetMaxReferenceNumberExam($request->test_type),
                cn::EXAM_TABLE_USE_OF_MODE_COLS => 1,
                cn::EXAM_TABLE_TITLE_COLS => $request->title,
                cn::EXAM_TABLE_FROM_DATE_COLS => $this->DateConvertToYMD($request->start_date),
                cn::EXAM_TABLE_TO_DATE_COLS => $this->DateConvertToYMD($request->end_date),
                cn::EXAM_TABLE_RESULT_DATE_COLS => $report_date,
                cn::EXAM_TABLE_PUBLISH_DATE_COL => $this->DateConvertToYMD($request->start_date),
                cn::EXAM_TABLE_START_TIME_COL => $request->start_time,
                cn::EXAM_TABLE_END_TIME_COL => $request->end_time,
                cn::EXAM_TABLE_REPORT_TYPE_COLS => $request->report_date,
                cn::EXAM_TABLE_TIME_DURATIONS_COLS => $timeDuration,
                cn::EXAM_TABLE_QUESTION_IDS_COL => $questionIds,
                cn::EXAM_TABLE_STUDENT_IDS_COL => $studentIds,
                cn::EXAM_TABLE_PEER_GROUP_IDS_COL => $peerGroupIds,
                cn::EXAM_TABLE_SCHOOL_COLS => $schoolId,
                cn::EXAM_TABLE_IS_UNLIMITED => ($request->test_type == 1) ? 1 : 0,
                cn::EXAM_TABLE_SELF_LEARNING_TEST_TYPE_COL => null,
                cn::EXAM_TABLE_NO_OF_TRIALS_PER_QUESTIONS_COL => $request->no_of_trials_per_question,
                cn::EXAM_TABLE_DIFFICULTY_MODE_COL => $request->difficulty_mode,
                cn::EXAM_TABLE_DIFFICULTY_LEVELS_COL => $difficulty_lvl,
                cn::EXAM_TABLE_IS_DISPLAY_HINTS_COL => $request->display_hints,
                cn::EXAM_TABLE_IS_DISPLAY_FULL_SOLUTIONS_COL => $request->display_full_solution,
                cn::EXAM_TABLE_IS_DISPLAY_PER_ANSWER_HINTS_COL => $request->display_pr_answer_hints,
                cn::EXAM_TABLE_IS_RANDOMIZED_ANSWERS_COL => $request->randomize_answer,
                cn::EXAM_TABLE_IS_RANDOMIZED_ORDER_COL => $request->randomize_order,
                cn::EXAM_TABLE_LEARNING_OBJECTIVES_CONFIGURATIONS_COL => $learningObjectivesConfigurations,
                cn::EXAM_TABLE_CREATED_BY_COL => $this->LoggedUserId(),
                //cn::EXAM_TABLE_STATUS_COLS => 'publish',
                cn::EXAM_TABLE_STATUS_COLS => ($request->has('save_and_publish')) ? 'publish' : 'draft',
                cn::EXAM_TABLE_CREATED_BY_USER_COL => $this->findCreatedByUserType()
            ];
            //$this->StoreAuditLogFunction($examData,'Exam',cn::EXAM_TABLE_ID_COLS,'','Create Exam',cn::EXAM_TABLE_NAME,'');
            $exams = Exam::create($examData);
            if($exams){
                if(isset($request->submission_on_time) && !empty($request->submission_on_time)){
                    $submission_on_time = $request->submission_on_time;
                    $examCreditPointRulesMappingData = [
                        cn::EXAM_CREDIT_POINT_RULES_MAPPING_EXAM_ID_COL => $exams->{cn::EXAM_TABLE_ID_COLS},
                        cn::EXAM_CREDIT_POINT_RULES_MAPPING_SCHOOL_ID_COL => $schoolId,
                        cn::EXAM_CREDIT_POINT_RULES_MAPPING_CREDIT_POINT_RULES_COL => 'submission_on_time',
                        cn::EXAM_CREDIT_POINT_RULES_MAPPING_RULES_VALUE_COL => $submission_on_time,
                    ];
                    ExamCreditPointRulesMapping::create($examCreditPointRulesMappingData);
                }

                if(isset($request->credit_points_of_accuracy) && !empty($request->credit_points_of_accuracy)){
                    $credit_points_of_accuracy = $request->credit_points_of_accuracy;
                    $examCreditPointRulesMappingData = [
                        cn::EXAM_CREDIT_POINT_RULES_MAPPING_EXAM_ID_COL => $exams->{cn::EXAM_TABLE_ID_COLS},
                        cn::EXAM_CREDIT_POINT_RULES_MAPPING_SCHOOL_ID_COL => $schoolId,
                        cn::EXAM_CREDIT_POINT_RULES_MAPPING_CREDIT_POINT_RULES_COL => 'credit_points_of_accuracy',
                        cn::EXAM_CREDIT_POINT_RULES_MAPPING_RULES_VALUE_COL => $credit_points_of_accuracy,
                    ];
                    ExamCreditPointRulesMapping::create($examCreditPointRulesMappingData);
                }

                if(isset($request->credit_points_of_normalized_ability) && !empty($request->credit_points_of_normalized_ability)){
                    $credit_points_of_normalized_ability = $request->credit_points_of_normalized_ability;
                    $examCreditPointRulesMappingData = [
                        cn::EXAM_CREDIT_POINT_RULES_MAPPING_EXAM_ID_COL => $exams->{cn::EXAM_TABLE_ID_COLS},
                        cn::EXAM_CREDIT_POINT_RULES_MAPPING_SCHOOL_ID_COL => $schoolId,
                        cn::EXAM_CREDIT_POINT_RULES_MAPPING_CREDIT_POINT_RULES_COL => 'credit_points_of_normalized_ability',
                        cn::EXAM_CREDIT_POINT_RULES_MAPPING_RULES_VALUE_COL => $credit_points_of_normalized_ability,
                    ];
                    ExamCreditPointRulesMapping::create($examCreditPointRulesMappingData);
                }
                
                if(isset($request->classes) && !empty($request->classes)){
                    foreach ($request->classes as $gradeId => $classIds) {
                        foreach ($classIds as $classId) {
                            $studentIdsArray = User::where([
                                                    cn::USERS_SCHOOL_ID_COL => Auth::user()->{cn::USERS_SCHOOL_ID_COL},
                                                    cn::USERS_ROLE_ID_COL => cn::STUDENT_ROLE_ID,
                                                    cn::USERS_GRADE_ID_COL => $gradeId,
                                                    cn::USERS_CLASS_ID_COL => $classId
                                                ])->pluck(cn::USERS_ID_COL)->toArray();
                            $examStudentIdsComm = '';
                            $examStudentIds = array_intersect($request->studentIds,$studentIdsArray);
                            if(isset($examStudentIds) && !empty($examStudentIds)){
                                $examStudentIdsComm = implode(',',$examStudentIds);
                            }
                            $startDate = '';
                            if(isset($request->generator_class_start_date[$gradeId][$classId])){
                                $startDate = $this->DateConvertToYMD($request->generator_class_start_date[$gradeId][$classId]);
                            }
                            $endDate = '';
                            if(isset($request->generator_class_end_date[$gradeId][$classId])){
                                $endDate = $this->DateConvertToYMD($request->generator_class_end_date[$gradeId][$classId]);
                            }
                            $startTime = ($request->start_time !='') ? $request->start_time.':00' : '';
                            if(isset($request->generator_class_start_time[$gradeId][$classId])){
                                $startTime = $request->generator_class_start_time[$gradeId][$classId].':00';
                            }
                            $endTime = ($request->end_time !='') ? $request->end_time.':00' : '';
                            if(isset($request->generator_class_end_time[$gradeId][$classId])){
                                $endTime = $request->generator_class_end_time[$gradeId][$classId].':00';
                            }
                            $examGradeClassData = [
                                cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_SCHOOL_ID_COL => $schoolId,
                                cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_GRADE_ID_COL => $gradeId,
                                cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_CLASS_ID_COL => $classId,
                                cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_EXAM_ID_COL => $exams->id,
                                cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_STUDENT_IDS_COL => $examStudentIdsComm,
                                cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_START_TIME_COL => $startTime,
                                cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_END_TIME_COL => $endTime,
                                cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_START_DATE_COL => $startDate,
                                cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_END_DATE_COL => $endDate,
                                cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_STATUS_COL => ($request->has('save_and_publish')) ? 'publish' : 'draft'
                            ];
                            ExamGradeClassMappingModel::create($examGradeClassData);
                            $this->StoreAuditLogFunction($examGradeClassData,'ExamGradeClassMappingModel',cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_ID_COL,'','Create Exam Grade Class Mapping',cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_TABLE,'');
                        }
                    }
                }

                if(isset($request->peerGroupIds) && !empty($request->peerGroupIds)){
                    // Assign test in peer groups
                    $this->AssignTestToPeerGroups($exams->{cn::EXAM_TABLE_ID_COLS}, $request->peerGroupIds, $request);                    
                }

                $this->CreateExamSchoolMapping(array($schoolId),$exams->{cn::EXAM_TABLE_ID_COLS}, $request);
                return redirect()->route('question-wizard')->with('success_msg', __('languages.exam_added_successfully'));
            }else{
                return back()->with('error_msg', __('languages.problem_was_occur_please_try_again'));
            }
        }
        return view('backend.question_generator.school.create_test',compact('schoolList','difficultyLevels','strandsList','LearningUnits','LearningObjectives','timeSlots','RequiredQuestionPerSkill','learningObjectivesConfiguration','GradeClassData','StudentList','PeerGroupList'));
    }

    /**
     * USE : Update Question generator flow for "School Admin | Teacher | Principal"
     */
    public function schoolGenerateTestQuestionEdit(Request $request,$id){
        $exam = Exam::find($id);
        if(!empty($exam)){
            $GradeClassData = array();
            $StudentList = array();
            $examGradeIds = $exam->examSchoolGradeClass()->whereNotNull(cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_GRADE_ID_COL)->pluck(cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_GRADE_ID_COL)->toArray();
            $examClassIds = $exam->examSchoolGradeClass()->pluck(cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_CLASS_ID_COL)->toArray();
            if(isset($examGradeIds) && !empty($examGradeIds)){
                $examStartTime = json_encode($exam->examSchoolGradeClass()->pluck(cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_START_TIME_COL,cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_CLASS_ID_COL)->toArray());
                $examEndTime = json_encode($exam->examSchoolGradeClass()->pluck(cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_END_TIME_COL,cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_CLASS_ID_COL)->toArray());
                $examStartDate = json_encode($exam->examSchoolGradeClass()->pluck(cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_START_DATE_COL,cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_CLASS_ID_COL)->toArray());
                $examEndDate = json_encode($exam->examSchoolGradeClass()->pluck(cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_END_DATE_COL,cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_CLASS_ID_COL)->toArray());
            }else{
                $examStartTime = json_encode($exam->examSchoolGradeClass()->pluck(cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_START_TIME_COL,cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_PEER_GROUP_ID_COL)->toArray());
                $examEndTime = json_encode($exam->examSchoolGradeClass()->pluck(cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_END_TIME_COL,cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_PEER_GROUP_ID_COL)->toArray());
                $examStartDate = json_encode($exam->examSchoolGradeClass()->pluck(cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_START_DATE_COL,cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_PEER_GROUP_ID_COL)->toArray());
                $examEndDate = json_encode($exam->examSchoolGradeClass()->pluck(cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_END_DATE_COL,cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_PEER_GROUP_ID_COL)->toArray());
            }
            $difficultyLevels = PreConfigurationDiffiltyLevel::all();
            $strandsList = [];
            $LearningUnits = [];
            $LearningObjectives = [];
            $PeerGroupList = [];
            $schoolList = [];
            $RequiredQuestionPerSkill = [];
            $RequiredQuestionPerSkill = [
                'minimum_question_per_skill' => $this->MinimumQuestionPerSkill,
                'maximum_question_per_skill' => $this->MaximumQuestionPerObjectives
            ];
            $Schools = School::all();

            // Get Time slot
            $timeSlots = $this->getTimeSlot();
            $strandsSelected = array();
            $SelectedLearningUnit = array();
            $strandsList = Strands::all();
            $learningObjectivesConfiguration = array();
            if(isset($exam->learning_objectives_configuration) && !empty($exam->learning_objectives_configuration)){
                $learningObjectivesConfiguration = json_decode($exam->learning_objectives_configuration,true);
                $SelectedLearningUnit = array_keys($learningObjectivesConfiguration);
                $learning_objectives_configuration = array_keys($learningObjectivesConfiguration);
                $LearningUnitsData = LearningsUnits::whereIn(cn::LEARNING_UNITS_ID_COL,$learning_objectives_configuration)->pluck(cn::LEARNING_UNITS_STRANDID_COL)->toArray();
                $SelectedStrands = array_unique($LearningUnitsData);
            }
            $questionDataArray = array();
            if(isset($exam->question_ids) && !empty($exam->question_ids)){
                $questionDataArray = Question::with(['answers','PreConfigurationDifficultyLevel','objectiveMapping'])->whereIn(cn::QUESTION_TABLE_ID_COL,explode(',',$exam->question_ids))->get()->toArray();
            }
            if(!empty($SelectedStrands)){
                $LearningUnits = LearningsUnits::whereIn(cn::LEARNING_UNITS_STRANDID_COL, $SelectedStrands)->get();
                if(!empty($LearningUnits)){
                    // $LearningObjectives = LearningsObjectives::whereIn(cn::LEARNING_OBJECTIVES_LEARNING_UNITID_COL, $SelectedLearningUnit)->get();
                    $LearningObjectives = LearningsObjectives::IsAvailableQuestion()->whereIn(cn::LEARNING_OBJECTIVES_LEARNING_UNITID_COL, $SelectedLearningUnit)->get();
                }
            }
            if($this->isTeacherLogin()){
                $schoolId = $this->isTeacherLogin();
            }
            if($this->isSchoolLogin()){
                $schoolId = $this->isSchoolLogin();
            }
            if($this->isPrincipalLogin()){
                $schoolId = $this->isPrincipalLogin();
            }

            $examCreditPointRulesData = $exam->examCreditPointRules()->where('school_id',$schoolId)->pluck('rules_value','credit_point_rules')->toArray();
            $studentGradeData = array();
            $studentClassData = array();
            if(isset($exam->student_ids) && !empty($exam->student_ids)){
                $studentData = User::whereIn(cn::USERS_ID_COL,explode(',', $exam->student_ids))
                                ->where([
                                    cn::USERS_ROLE_ID_COL => CN::STUDENT_ROLE_ID,
                                    cn::USERS_SCHOOL_ID_COL => $schoolId,
                                    cn::USERS_STATUS_COL => 'active'
                                ]);
                $studentGradeData = $studentData->pluck(cn::USERS_GRADE_ID_COL)->toArray();
                $studentClassData = $studentData->pluck(cn::USERS_CLASS_ID_COL)->toArray();
            }

            if($this->isTeacherLogin()){
                $schoolId = $this->isTeacherLogin();
                $TeacherGradeClassData = $this->TeacherGradesClassService->getTeacherAssignedGradesClass(Auth::user()->{cn::USERS_SCHOOL_ID_COL}, Auth::user()->{cn::USERS_ID_COL});
                if(!empty($TeacherGradeClassData)){
                    $gradeClassId = $TeacherGradeClassData['grades'] ?? [];
                    $GradeClassData = Grades::with(['classes' => fn($query) => $query->whereIn(cn::GRADE_CLASS_MAPPING_ID_COL,$TeacherGradeClassData['class'])
                                        ->where([cn::GRADE_CLASS_MAPPING_SCHOOL_ID_COL => $this->isTeacherLogin()])])
                                        ->whereIn(cn::GRADES_ID_COL,$TeacherGradeClassData['grades'])
                                        ->get();
                }

                // get student list
                $StudentList = User::whereIn(cn::USERS_GRADE_ID_COL,$TeacherGradeClassData['grades'])
                            ->whereIn(cn::USERS_CLASS_ID_COL,$TeacherGradeClassData['class'])
                            ->where([cn::USERS_ROLE_ID_COL => 3,cn::USERS_STATUS_COL => 'active'])
                            ->get();
                // Get Peer Group List
                $PeerGroupList = PeerGroup::where([
                                    cn::PEER_GROUP_CREATED_BY_USER_ID_COL => Auth()->user()->id,
                                    cn::PEER_GROUP_STATUS_COL => '1'
                                ])->get();
            }

            if($this->isSchoolLogin() || $this->isPrincipalLogin()){    
                if($this->isSchoolLogin()){
                    $schoolId = $this->isSchoolLogin();
                }
                if($this->isPrincipalLogin()){
                    $schoolId = $this->isPrincipalLogin();
                }

                $GradeMapping = GradeSchoolMappings::with('grades')->where(cn::GRADES_MAPPING_SCHOOL_ID_COL,$schoolId)->get()->pluck(cn::GRADES_MAPPING_GRADE_ID_COL);
                $gradeClass = GradeClassMapping::where(cn::GRADE_CLASS_MAPPING_SCHOOL_ID_COL,$schoolId)->whereIn(cn::GRADE_CLASS_MAPPING_GRADE_ID_COL,$GradeMapping)->pluck(cn::GRADE_CLASS_MAPPING_ID_COL)->toArray();
                if(isset($gradeClass) && !empty($gradeClass)){
                    $gradeClass = implode(',', $gradeClass);
                    $gradeClassId = explode(',',$gradeClass);
                }
                $GradeClassData = Grades::with(['classes' => fn($query) => $query->whereIn(cn::GRADE_CLASS_MAPPING_ID_COL,$gradeClassId)->where([cn::GRADE_CLASS_MAPPING_SCHOOL_ID_COL => $schoolId])])->whereIn(cn::GRADES_ID_COL,$GradeMapping)->get();
                
                // get student list
                $StudentList = User::where(cn::USERS_SCHOOL_ID_COL,Auth::user()->{cn::USERS_SCHOOL_ID_COL})->where(cn::USERS_ROLE_ID_COL,'=',cn::STUDENT_ROLE_ID)->with('grades')->get();

                // Get Peer Group List
                $PeerGroupList = PeerGroup::where([cn::PEER_GROUP_SCHOOL_ID_COL => $schoolId, cn::PEER_GROUP_STATUS_COL => '1'])->get();
            }

            $questionListHtml = '';
            //if($exam->{cn::EXAM_TABLE_USE_OF_MODE_COLS} == 1 && empty($exam->{cn::EXAM_TABLE_PARENT_EXAM_ID_COLS}) && sizeOf(explode(',',$exam->{cn::EXAM_TABLE_SCHOOL_COLS})) != 1){
            if($exam->{cn::EXAM_TABLE_USE_OF_MODE_COLS} == 1 && empty($exam->{cn::EXAM_TABLE_PARENT_EXAM_ID_COLS})){
                $questionListHtml = '';
                $difficultyLevels = PreConfigurationDiffiltyLevel::all();
                $question_list = Question::with(['answers','PreConfigurationDifficultyLevel','objectiveMapping'])
                                    ->whereIn(cn::QUESTION_TABLE_ID_COL,explode(',',$exam->{cn::EXAM_TABLE_QUESTION_IDS_COL}))
                                    ->get();
                $questionListHtml = (string)View::make('backend.question_generator.school.question_list_preview',compact('question_list','difficultyLevels'));
            }

            if($request->isMethod('post')){                
                $timeDuration = null;
                $questionIds = '';
                $studentIds = '';
                $difficulty_lvl = '';

                if(isset($request->difficulty_lvl) && !empty($request->difficulty_lvl)){
                    $difficulty_lvl = implode(',',$request->difficulty_lvl);
                }
                $learningObjectivesConfigurations = '';

                // If use type mode is  then question changed based on selected test
                if($exam->{cn::EXAM_TABLE_USE_OF_MODE_COLS} == 2){
                    if(isset($request->title) && $request->title == ""){
                        return back()->with('error_msg', __('languages.problem_was_occur_please_try_again'));
                    }

                    if(isset($request->qIndex) && !empty($request->qIndex)){
                        $questionIds = implode(',',$request->qIndex);
                    }else{
                        return back()->with('error_msg', __('languages.test_template_management.question_not_found_add_question'));
                    }
                }

                // If use type mode is 1 then question not change
                if($exam->{cn::EXAM_TABLE_USE_OF_MODE_COLS} == 1){
                    $questionIds = ($exam->{cn::EXAM_TABLE_QUESTION_IDS_COL}) ? $exam->{cn::EXAM_TABLE_QUESTION_IDS_COL} : '';
                }
                
                $oldPeerGroupStudentIds='';
                $peerGroupIds = '';
                if(isset($request->peerGroupIds) && !empty($request->peerGroupIds)){
                    $peerGroupIds = implode(',',$request->peerGroupIds);
                    $PeerGroupList = PeerGroupMember::whereIn(cn::PEER_GROUP_MEMBERS_PEER_GROUP_ID_COL,$request->peerGroupIds)->pluck(cn::PEER_GROUP_MEMBERS_MEMBER_ID_COL)->toArray();
                    if(isset($PeerGroupList) && !empty($PeerGroupList)){
                        $studentIds = implode(',',$PeerGroupList);
                    }else{
                        $peerGroupIds = '';
                    }

                    $oldPeerGroupData = ExamGradeClassMappingModel::where([
                        cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_SCHOOL_ID_COL => $schoolId,
                        cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_EXAM_ID_COL => $id
                    ])
                    ->whereNotNull(cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_GRADE_ID_COL);
                    $oldPeerGroupDataStudIds = $oldPeerGroupData->pluck(cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_STUDENT_IDS_COL)->toArray();
                    $oldPeerGroupData->delete();
                    if(isset($oldPeerGroupDataStudIds) && !empty($oldPeerGroupDataStudIds)){
                        $oldPeerGroupStudentIds = implode(',',$oldPeerGroupDataStudIds);
                    }
                }
                
                if($peerGroupIds == ''){
                    if(isset($request->studentIds) && !empty($request->studentIds)){
                        $studentIds = implode(',',$request->studentIds);
                    }
                }

                if($exam->{cn::EXAM_TABLE_PEER_GROUP_IDS_COL} != "" && isset($request->classes) && !empty($request->classes)){
                    $oldPeerGroupIds = explode(',',$exam->{cn::EXAM_TABLE_PEER_GROUP_IDS_COL});
                    $oldPeerGroupData = ExamGradeClassMappingModel::where([
                                    cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_SCHOOL_ID_COL => $schoolId,
                                    cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_EXAM_ID_COL => $id
                                ])
                                ->whereIn(cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_PEER_GROUP_ID_COL, $oldPeerGroupIds);
                    $oldPeerGroupDataStudIds = $oldPeerGroupData->pluck(cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_STUDENT_IDS_COL)->toArray();
                    $oldPeerGroupData->delete();
                    if(isset($oldPeerGroupDataStudIds) && !empty($oldPeerGroupDataStudIds)){
                        $oldPeerGroupStudentIds = implode(',',$oldPeerGroupDataStudIds);
                    }
                }

                if($exam->{cn::EXAM_TABLE_USE_OF_MODE_COLS} == 1 && empty($exam->{cn::EXAM_TABLE_PARENT_EXAM_ID_COLS}) && $exam->{cn::EXAM_TABLE_CREATED_BY_USER_COL} == 'super_admin'){
                    $studentIdsArray = $exam->{cn::EXAM_TABLE_STUDENT_IDS_COL};
                    if($studentIdsArray != ''){
                        $studentIdsArray = explode(',',$studentIdsArray);
                        if($oldPeerGroupStudentIds != ""){
                            $oldStudentIds = explode(',',$oldPeerGroupStudentIds);
                            $studentIdsArray = array_diff($studentIdsArray,$oldStudentIds);
                            $studentIdsArray = implode(',',$studentIdsArray);
                        }
                    }
                    $examData = [cn::EXAM_TABLE_STUDENT_IDS_COL=>$studentIdsArray];
                    $exams = Exam::find($id)->update($examData);
                }else{
                    if(isset($request->learning_unit) && !empty($request->learning_unit)){
                        $learningObjectivesConfigurations = json_encode($request->learning_unit);
                    }
                    if($exam->{cn::EXAM_TABLE_CREATED_BY_USER_COL} == 'super_admin'){
                        $examStudentIdsComm = '';
                        $examQuestionIdsComm = '';
                        $parentExams = Exam::find($exam->{cn::EXAM_TABLE_PARENT_EXAM_ID_COLS});
                        $studentIdsArray = $parentExams->{cn::EXAM_TABLE_STUDENT_IDS_COL};
                        $questionIdsArray = $parentExams->{cn::EXAM_TABLE_QUESTION_IDS_COL};
                        if($studentIdsArray != ''){
                            $studentIdsArray = explode(',',$studentIdsArray);
                            $newStudentIds = explode(',',$studentIds);
                            if($oldPeerGroupStudentIds != ""){
                                $oldStudentIds = explode(',',$oldPeerGroupStudentIds);
                                $newStudentIds = array_diff($newStudentIds,$oldStudentIds);
                            }
                            $examStudentIds = array_unique(array_merge($newStudentIds,$studentIdsArray));
                            if(isset($examStudentIds) && !empty($examStudentIds)){
                                $examStudentIdsComm = implode(',',$examStudentIds);
                            }
                        }else{
                            $examStudentIdsComm = $studentIds;
                        }

                        if($questionIdsArray != ''){
                            $questionIdsArray = explode(',',$questionIdsArray);
                            $newQuestionIds = explode(',',$questionIds);
                            $examQuestionIds = array_unique(array_merge($newQuestionIds,$questionIdsArray));
                            if(isset($examQuestionIds) && !empty($examQuestionIds)){
                                $examQuestionIdsComm = implode(',',$examQuestionIds);
                            }
                        }else{
                            $examQuestionIdsComm = $questionIds;
                        }
                        $parentExams->{cn::EXAM_TABLE_QUESTION_IDS_COL} = $examQuestionIdsComm;
                        $parentExams->{cn::EXAM_TABLE_STUDENT_IDS_COL} = $examStudentIdsComm;
                        $parentExams->update();
                    }
    
                    $report_date = ($request->end_date) ? $this->DateConvertToYMD($request->end_date) : $exam->result_date;
                    if($request->report_date == 'after_submit'){
                        $report_date = Carbon::now();
                    }else{
                        $report_date = ($request->custom_date) ? $this->DateConvertToYMD($request->custom_date) : $exam->result_date;
                    }

                    if($this->isTeacherLogin()){
                        $schoolId = $this->isTeacherLogin();
                    }
                    if($this->isSchoolLogin()){
                        $schoolId = $this->isSchoolLogin();
                    }
                    if($this->isPrincipalLogin()){
                        $schoolId = $this->isPrincipalLogin();
                    }
                    $start_date = $request->start_date ?? $exam->from_date;
                    $end_date = $request->end_date ?? $exam->to_date;
                    $start_time = $request->start_time ?? $exam->start_time;
                    $end_time = $request->end_time ?? $exam->end_time;

                    // Find exams data
                    $exams = Exam::find($id);
                    if(!empty($exams)){
                        if(isset($request->test_type)){
                            $exams->{cn::EXAM_TYPE_COLS} = $request->test_type;
                        }
                        $exams->{cn::EXAM_TABLE_USE_OF_MODE_COLS} = $exam->use_of_mode;
                        if(isset($request->title)){
                            $exams->{cn::EXAM_TABLE_TITLE_COLS} = $request->title;
                        }
                        if(isset($request->start_date)){
                            $exams->{cn::EXAM_TABLE_FROM_DATE_COLS} = $this->DateConvertToYMD($request->start_date);
                            $exams->{cn::EXAM_TABLE_PUBLISH_DATE_COL} = $this->DateConvertToYMD($request->start_date);
                        }
                        if(isset($request->end_date)){
                            $exams->{cn::EXAM_TABLE_TO_DATE_COLS} = $this->DateConvertToYMD($request->end_date);
                        }
                        if(isset($report_date)){
                            $exams->{cn::EXAM_TABLE_RESULT_DATE_COLS} = $report_date;
                        }
                        if(isset($request->start_time)){
                            $exams->{cn::EXAM_TABLE_START_TIME_COL} = $request->start_time;
                        }
                        if(isset($request->end_time)){
                            $exams->{cn::EXAM_TABLE_END_TIME_COL} = $request->end_time;
                        }
                        if(isset($request->report_date)){
                            $exams->{cn::EXAM_TABLE_REPORT_TYPE_COLS} = $request->report_date;
                        }
                        if(isset($timeDuration)){
                            $exams->{cn::EXAM_TABLE_TIME_DURATIONS_COLS} = $timeDuration;
                        }
                        $exams->{cn::EXAM_TABLE_QUESTION_IDS_COL} = $questionIds;
                        if(isset($request->test_type)){
                            $exams->{cn::EXAM_TABLE_IS_UNLIMITED} = ($request->test_type == 1) ? 1 : 0;
                        }
                        if(isset($request->no_of_trials_per_question)){
                            $exams->{cn::EXAM_TABLE_NO_OF_TRIALS_PER_QUESTIONS_COL} = $request->no_of_trials_per_question;
                        }
                        if(isset($request->difficulty_mode)){
                            $exams->{cn::EXAM_TABLE_DIFFICULTY_MODE_COL} = $request->difficulty_mode;
                        }
                        if(isset($difficulty_lvl)){
                            $exams->{cn::EXAM_TABLE_DIFFICULTY_LEVELS_COL} = $difficulty_lvl;
                        }
                        $exams->{cn::EXAM_TABLE_STUDENT_IDS_COL} = $studentIds;
                        if(isset($request->display_hints)){
                            $exams->{cn::EXAM_TABLE_IS_DISPLAY_HINTS_COL} = $request->display_hints;
                        }
                        if(isset($request->display_full_solution)){
                            $exams->{cn::EXAM_TABLE_IS_DISPLAY_FULL_SOLUTIONS_COL} = $request->display_full_solution;
                        }
                        if(isset($request->display_pr_answer_hints)){
                            $exams->{cn::EXAM_TABLE_IS_DISPLAY_PER_ANSWER_HINTS_COL} = $request->display_pr_answer_hints;
                        }
                        if(isset($request->randomize_answer)){
                            $exams->{cn::EXAM_TABLE_IS_RANDOMIZED_ANSWERS_COL} = $request->randomize_answer;
                        }
                        if(isset($request->randomize_order)){
                            $exams->{cn::EXAM_TABLE_IS_RANDOMIZED_ORDER_COL} = $request->randomize_order;
                        }
                        if(isset($request->save_and_publish)){
                            $exams->status = ($request->save_and_publish) ? 'publish' : 'draft';
                        }
                        if(isset($learningObjectivesConfigurations)){
                            $exams->{cn::EXAM_TABLE_LEARNING_OBJECTIVES_CONFIGURATIONS_COL} = $learningObjectivesConfigurations;
                        }
                    }
                    $exams->update();
                }
                if($exams){
                    if(isset($request->submission_on_time) && !empty($request->submission_on_time)){
                        $submission_on_time = $request->submission_on_time;
                        $examCreditPointRulesMappingOldData = ExamCreditPointRulesMapping::where(cn::EXAM_CREDIT_POINT_RULES_MAPPING_EXAM_ID_COL,$id)
                        ->where(cn::EXAM_CREDIT_POINT_RULES_MAPPING_SCHOOL_ID_COL,$schoolId)
                        ->where(cn::EXAM_CREDIT_POINT_RULES_MAPPING_CREDIT_POINT_RULES_COL,'submission_on_time')->get()->toArray();
                        if(isset($examCreditPointRulesMappingOldData) && !empty($examCreditPointRulesMappingOldData)){
                            $examCreditPointRulesMappingData = [
                                cn::EXAM_CREDIT_POINT_RULES_MAPPING_RULES_VALUE_COL => $submission_on_time,
                            ];
                            ExamCreditPointRulesMapping::find($examCreditPointRulesMappingOldData[0][cn::EXAM_CREDIT_POINT_RULES_MAPPING_ID_COL])->update($examCreditPointRulesMappingData);
                        }else{
                            $examCreditPointRulesMappingData = [
                                cn::EXAM_CREDIT_POINT_RULES_MAPPING_EXAM_ID_COL => $id,
                                cn::EXAM_CREDIT_POINT_RULES_MAPPING_SCHOOL_ID_COL => $schoolId,
                                cn::EXAM_CREDIT_POINT_RULES_MAPPING_CREDIT_POINT_RULES_COL => 'submission_on_time',
                                cn::EXAM_CREDIT_POINT_RULES_MAPPING_RULES_VALUE_COL => $submission_on_time,
                            ];
                            ExamCreditPointRulesMapping::create($examCreditPointRulesMappingData);
                        }
                    }
                    if(isset($request->credit_points_of_accuracy) && !empty($request->credit_points_of_accuracy)){
                        $credit_points_of_accuracy = $request->credit_points_of_accuracy;
                        $examCreditPointRulesMappingOldData = ExamCreditPointRulesMapping::where(cn::EXAM_CREDIT_POINT_RULES_MAPPING_EXAM_ID_COL,$id)
                        ->where(cn::EXAM_CREDIT_POINT_RULES_MAPPING_SCHOOL_ID_COL,$schoolId)
                        ->where(cn::EXAM_CREDIT_POINT_RULES_MAPPING_CREDIT_POINT_RULES_COL,'credit_points_of_accuracy')->get()->toArray();
                        if(isset($examCreditPointRulesMappingOldData) && !empty($examCreditPointRulesMappingOldData)){
                            $examCreditPointRulesMappingData = [
                                cn::EXAM_CREDIT_POINT_RULES_MAPPING_RULES_VALUE_COL => $credit_points_of_accuracy,
                            ];
                            ExamCreditPointRulesMapping::find($examCreditPointRulesMappingOldData[0][cn::EXAM_CREDIT_POINT_RULES_MAPPING_ID_COL])->update($examCreditPointRulesMappingData);
                        }else{
                            $examCreditPointRulesMappingData = [
                                cn::EXAM_CREDIT_POINT_RULES_MAPPING_EXAM_ID_COL => $id,
                                cn::EXAM_CREDIT_POINT_RULES_MAPPING_SCHOOL_ID_COL => $schoolId,
                                cn::EXAM_CREDIT_POINT_RULES_MAPPING_CREDIT_POINT_RULES_COL => 'credit_points_of_accuracy',
                                cn::EXAM_CREDIT_POINT_RULES_MAPPING_RULES_VALUE_COL => $credit_points_of_accuracy,
                            ];
                            ExamCreditPointRulesMapping::create($examCreditPointRulesMappingData);
                        }
                    }
                    if(isset($request->credit_points_of_normalized_ability) && !empty($request->credit_points_of_normalized_ability)){
                        $credit_points_of_normalized_ability = $request->credit_points_of_normalized_ability;
                        $examCreditPointRulesMappingOldData = ExamCreditPointRulesMapping::where(cn::EXAM_CREDIT_POINT_RULES_MAPPING_EXAM_ID_COL,$id)
                        ->where(cn::EXAM_CREDIT_POINT_RULES_MAPPING_SCHOOL_ID_COL,$schoolId)
                        ->where(cn::EXAM_CREDIT_POINT_RULES_MAPPING_CREDIT_POINT_RULES_COL,'credit_points_of_normalized_ability')->get()->toArray();
                        if(isset($examCreditPointRulesMappingOldData) && !empty($examCreditPointRulesMappingOldData)){
                            $examCreditPointRulesMappingData = [
                                cn::EXAM_CREDIT_POINT_RULES_MAPPING_RULES_VALUE_COL => $credit_points_of_normalized_ability,
                            ];
                            ExamCreditPointRulesMapping::find($examCreditPointRulesMappingOldData[0][cn::EXAM_CREDIT_POINT_RULES_MAPPING_ID_COL])->update($examCreditPointRulesMappingData);
                        }else{
                            $examCreditPointRulesMappingData = [
                                cn::EXAM_CREDIT_POINT_RULES_MAPPING_EXAM_ID_COL => $id,
                                cn::EXAM_CREDIT_POINT_RULES_MAPPING_SCHOOL_ID_COL => $schoolId,
                                cn::EXAM_CREDIT_POINT_RULES_MAPPING_CREDIT_POINT_RULES_COL => 'credit_points_of_normalized_ability',
                                cn::EXAM_CREDIT_POINT_RULES_MAPPING_RULES_VALUE_COL => $credit_points_of_normalized_ability,
                            ];
                            ExamCreditPointRulesMapping::create($examCreditPointRulesMappingData);
                        }
                    }
                    $removeDataId = array();
                    if(isset($request->classes) && !empty($request->classes)){
                        foreach($request->classes as $gradeId => $classIds){
                            foreach($classIds as $classId){
                                $oldDataId = ExamGradeClassMappingModel::where([
                                    cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_SCHOOL_ID_COL => $schoolId,
                                    cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_GRADE_ID_COL => $gradeId,
                                    cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_CLASS_ID_COL => $classId,
                                    cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_EXAM_ID_COL => $id
                                ])->pluck(cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_ID_COL)->toArray();

                                $studentIdsArray = User::where(cn::USERS_SCHOOL_ID_COL,Auth::user()->{cn::USERS_SCHOOL_ID_COL})->where(cn::USERS_ROLE_ID_COL,'=',cn::STUDENT_ROLE_ID)->where(cn::USERS_GRADE_ID_COL,$gradeId)->where(cn::USERS_CLASS_ID_COL,$classId)->pluck(cn::USERS_ID_COL)->toArray();
                                $examStudentIdsComm = '';
                                $examStudentIds = array_intersect(explode(',',$studentIds),$studentIdsArray);
                                if(isset($examStudentIds) && !empty($examStudentIds)){
                                    $examStudentIdsComm = implode(',',$examStudentIds);
                                }

                                $startDate = date('Y-m-d');//$this->DateConvertToYMD($exam->from_date) ?? '';
                                
                                if(isset($request->generator_class_start_date[$gradeId][$classId])){
                                    $startDate = $this->DateConvertToYMD($request->generator_class_start_date[$gradeId][$classId]);
                                }
                                $endDate = date('Y-m-d');//$this->DateConvertToYMD($request->end_date) ?? '';
                                if(isset($request->generator_class_end_date[$gradeId][$classId])){
                                    $endDate = $this->DateConvertToYMD($request->generator_class_end_date[$gradeId][$classId]);
                                }
                                $startTime = ($request->start_time !='') ? $request->start_time.':00' : '';
                                if(isset($request->generator_class_start_time[$gradeId][$classId]) && $request->generator_class_start_time[$gradeId][$classId]!=""){
                                    $startTime = $request->generator_class_start_time[$gradeId][$classId].':00';
                                }

                                $endTime = ($request->end_time !='') ? $request->end_time.':00' : '';
                                if(isset($request->generator_class_end_time[$gradeId][$classId]) && $request->generator_class_end_time[$gradeId][$classId]!=""){
                                    $endTime = $request->generator_class_end_time[$gradeId][$classId].':00';
                                }
                                $examGradeClassMappingData = [
                                    cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_STUDENT_IDS_COL => $examStudentIdsComm ?? null,
                                    cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_START_TIME_COL => $startTime,
                                    cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_END_TIME_COL => $endTime,
                                    cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_START_DATE_COL => $startDate,
                                    cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_END_DATE_COL => $endDate,
                                    'status' => ($request->has('save_and_publish')) ? 'publish' : 'draft'
                                ];
                                if(isset($oldDataId) && !empty($oldDataId)){
                                    $removeDataId[] = $oldDataId[0];
                                    ExamGradeClassMappingModel::where([
                                        cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_SCHOOL_ID_COL => $schoolId,
                                        cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_GRADE_ID_COL => $gradeId,
                                        cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_CLASS_ID_COL => $classId,
                                        cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_EXAM_ID_COL => $id
                                    ])->update($examGradeClassMappingData);
                                }else{
                                    $examGradeClassData = [
                                        cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_SCHOOL_ID_COL => $schoolId,
                                        cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_GRADE_ID_COL => $gradeId,
                                        cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_CLASS_ID_COL => $classId,
                                        cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_EXAM_ID_COL => $id,
                                        cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_STUDENT_IDS_COL => $examStudentIdsComm ?? null,
                                        cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_START_TIME_COL => $startTime,
                                        cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_END_TIME_COL => $endTime,
                                        cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_START_DATE_COL => $startDate,
                                        cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_END_DATE_COL => $endDate,
                                        'status' => ($request->has('save_and_publish')) ? 'publish' : 'draft'
                                    ];
                                    $examGradeClassMapping = ExamGradeClassMappingModel::create($examGradeClassData);
                                    $removeDataId[] = $examGradeClassMapping->{cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_ID_COL};
                                }
                            }
                        }
                    }
                    $removeDataId = array_values(array_filter($removeDataId));
                    if(isset($removeDataId) && !empty($removeDataId)){
                        if($this->isTeacherLogin()){
                            $oldDataId = ExamGradeClassMappingModel::where([
                                cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_SCHOOL_ID_COL => $schoolId,
                                cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_EXAM_ID_COL => $id
                            ])
                            ->whereIn(cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_CLASS_ID_COL,$gradeClassId)
                            ->whereNotIn(cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_ID_COL,$removeDataId)
                            ->pluck(cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_ID_COL)
                            ->toArray();
                        }

                        // Remove some grades and classes
                        if($this->isSchoolLogin() || $this->isPrincipalLogin()){
                            $oldDataId = ExamGradeClassMappingModel::where([
                                cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_SCHOOL_ID_COL => $schoolId,
                                cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_EXAM_ID_COL => $id
                            ])
                            ->whereNotIn(cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_ID_COL,$removeDataId)
                            ->delete();
                        }
                    }

                    // Assign test to peer groups
                    if(isset($request->peerGroupIds) && !empty($request->peerGroupIds)){
                        // Removed existing grades and class
                        if(isset($request->oldClasses) && !empty($request->oldClasses)){
                            $this->RemoveExistingAssignedGradeClassTest($exam->{cn::EXAM_TABLE_ID_COLS}, $request->oldClasses);
                        }

                        // Add Peer Group assign test in ExamGradeClassMappingModel table
                        $this->AssignTestToPeerGroups($exam->{cn::EXAM_TABLE_ID_COLS}, $request->peerGroupIds, $request);
                    }

                    // Update Exam School Mapping Table
                    $this->UpdateExamSchoolMapping(array(Auth::user()->{cn::USERS_SCHOOL_ID_COL}), $exam->{cn::EXAM_TABLE_ID_COLS}, $request);

                    // Update Student & Peer Groups in exam table
                    $this->UpdateStudentIdPeerGroupIdExamTable($exam->{cn::EXAM_TABLE_USE_OF_MODE_COLS},$exam->{cn::EXAM_TABLE_ID_COLS});

                    return redirect()->route('question-wizard')->with('success_msg', __('languages.exam_updated_successfully'));
                }else{
                    return back()->with('error_msg', __('languages.problem_was_occur_please_try_again'));
                }
            }
            return view('backend.question_generator.school.edit_test',compact('schoolList','difficultyLevels','strandsList','LearningUnits',
            'LearningObjectives','timeSlots','RequiredQuestionPerSkill','exam','learningObjectivesConfiguration','SelectedStrands','SelectedLearningUnit',
            'questionDataArray','GradeClassData','StudentList','PeerGroupList','studentGradeData','studentClassData','questionListHtml','examGradeIds',
            'examClassIds','examStartTime','examEndTime','examStartDate','examEndDate','examCreditPointRulesData'));
        }else{
            return back()->with('error_msg', __('languages.data_not_found'));
        }
    }

    /**
     * USE : Update student ids & peer group ids in exams table
     */
    public function UpdateStudentIdPeerGroupIdExamTable($UseOfMode, $ExamId){
        switch($UseOfMode){
            case 1:
                $StudentIds = [];
                $PeerGroupIds = [];
                $ExamData = $this->Exam->find($ExamId);
                if(!empty($ExamData)){
                    if(!empty($ExamData->{cn::EXAM_TABLE_SCHOOL_COLS})){
                        $SchoolIds = explode(',',$ExamData->{cn::EXAM_TABLE_SCHOOL_COLS});
                        $exam_school_grade_class_mapping_data = $this->ExamGradeClassMappingModel->where(cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_EXAM_ID_COL,$ExamId)->whereIn(cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_SCHOOL_ID_COL,$SchoolIds)->get();
                        if(!empty($exam_school_grade_class_mapping_data)){
                            foreach($exam_school_grade_class_mapping_data as $examMapping){
                                if(!empty($examMapping->{cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_STUDENT_IDS_COL})){
                                    $StudentIds[] = explode(',',$examMapping->{cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_STUDENT_IDS_COL});
                                }
                                if(!empty($examMapping->{cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_PEER_GROUP_ID_COL})){
                                    $PeerGroupIds[] = $examMapping->{cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_PEER_GROUP_ID_COL};
                                }
                            }
                        }
                        $StudentIds = $this->array_flatten($StudentIds);
                        if(!empty($StudentIds)){
                            // Update into exam table student ids column
                            $this->Exam->find($ExamId)->Update([cn::EXAM_TABLE_STUDENT_IDS_COL => implode(',',array_unique($StudentIds))]);
                        }
                        // Update peer_group_ids column in exam table
                        if(!empty($PeerGroupIds)){
                            $this->Exam->find($ExamId)->Update([cn::EXAM_TABLE_PEER_GROUP_IDS_COL => implode(',',array_unique($PeerGroupIds))]);
                        }
                    }
                }
                break;

            case 2:
                $ExamData = $this->Exam->find($ExamId);
                $MainExamId = $ExamData->{cn::EXAM_TABLE_PARENT_EXAM_ID_COLS};
                if(!empty($ExamData)){
                    $GetChildExamIds = $this->Exam->where(cn::EXAM_TABLE_PARENT_EXAM_ID_COLS,$MainExamId)->pluck(cn::EXAM_TABLE_ID_COLS);
                    if(!empty($GetChildExamIds)){
                        // Update students_ids and peer_group_ids created by super admin sub exam id
                        foreach($GetChildExamIds as $examId){
                            $exam_school_grade_class_mapping_data = $this->ExamGradeClassMappingModel->where(cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_EXAM_ID_COL,$examId)->get();
                            if(!empty($exam_school_grade_class_mapping_data)){
                                $StudentIds = [];
                                $PeerGroupIds = [];
                                foreach($exam_school_grade_class_mapping_data as $examMapping){
                                    if(!empty($examMapping->{cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_STUDENT_IDS_COL})){
                                        $StudentIds[] = explode(',',$examMapping->{cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_STUDENT_IDS_COL});
                                    }
                                    if(!empty($examMapping->{cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_PEER_GROUP_ID_COL})){
                                        $PeerGroupIds[] = $examMapping->{cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_PEER_GROUP_ID_COL};
                                    }
                                }
                                $StudentIds = $this->array_flatten($StudentIds);
                                if(!empty($StudentIds)){
                                    // Update into exam table student ids column
                                    $this->Exam->find($examId)->Update([cn::EXAM_TABLE_STUDENT_IDS_COL => implode(',',array_unique($StudentIds))]);
                                }
                                // Update peer_group_ids column in exam table
                                if(!empty($PeerGroupIds)){
                                    $this->Exam->find($examId)->Update([cn::EXAM_TABLE_PEER_GROUP_IDS_COL => implode(',',array_unique($PeerGroupIds))]);
                                }
                            }
                        }

                        // Update students_ids and peer_group_ids created by super admin
                        $exam_school_grade_class_mapping_data = '';
                        $exam_school_grade_class_mapping_data = $this->ExamGradeClassMappingModel->whereIn(cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_EXAM_ID_COL,$GetChildExamIds)->get();
                        if(!empty($exam_school_grade_class_mapping_data)){
                            $StudentIds = [];
                            $PeerGroupIds = [];
                            foreach($exam_school_grade_class_mapping_data as $examMapping){
                                if(!empty($examMapping->{cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_STUDENT_IDS_COL})){
                                    $StudentIds[] = explode(',',$examMapping->{cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_STUDENT_IDS_COL});
                                }
                                if(!empty($examMapping->{cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_PEER_GROUP_ID_COL})){
                                    $PeerGroupIds[] = $examMapping->{cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_PEER_GROUP_ID_COL};
                                }
                            }
                            $StudentIds = $this->array_flatten($StudentIds);
                            if(!empty($StudentIds)){
                                // Update into exam table student ids column
                                $this->Exam->find($MainExamId)->Update([cn::EXAM_TABLE_STUDENT_IDS_COL => implode(',',array_unique($StudentIds))]);
                            }
                            // Update peer_group_ids column in exam table
                            if(!empty($PeerGroupIds)){
                                $this->Exam->find($MainExamId)->Update([cn::EXAM_TABLE_PEER_GROUP_IDS_COL => implode(',',array_unique($PeerGroupIds))]);
                            }
                        }
                    }
                }
                break;
        }
    }

    /**
     * USE : If Teacher / School Panel have select peer group then 
     *       existing grade and class remove from this test
     */
    public function RemoveExistingAssignedGradeClassTest($ExamId, $GradeClassArray){
        if(!empty($ExamId) && !empty($GradeClassArray)){
            foreach($GradeClassArray as $GradeId => $ClassArray){
                $this->ExamGradeClassMappingModel->where(cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_EXAM_ID_COL,$ExamId)->where(cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_SCHOOL_ID_COL,Auth::user()->{cn::USERS_SCHOOL_ID_COL})->where(cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_GRADE_ID_COL,$GradeId)->whereIn(cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_CLASS_ID_COL,$ClassArray)->delete();
            }
        }
    }

    /**
     * USE : Assign Test in peer groups
     */
    public function AssignTestToPeerGroups($ExamId, $PeerGroupIds, $request){
        $PostData = [];
        $ExamData = $this->Exam->where(cn::EXAM_TABLE_ID_COLS,$ExamId)->first();
        if(!empty($PeerGroupIds)){
            foreach($PeerGroupIds as $GroupId){
                $PeerGroupStudentIds = $this->FindPeerGroupStudentByGroupId($GroupId);
                $ExistingRecord = $this->ExamGradeClassMappingModel->where([
                    cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_EXAM_ID_COL => $ExamId,
                    cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_SCHOOL_ID_COL => Auth::user()->{cn::USERS_SCHOOL_ID_COL},
                    cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_PEER_GROUP_ID_COL => $GroupId
                ])->first();
                if(!empty($ExistingRecord)){
                    $startDate = '';
                    if(isset($request->generator_group_start_date[$GroupId])){
                        $startDate = $this->DateConvertToYMD($request->generator_group_start_date[$GroupId]);
                    }
                    $endDate = '';
                    if(isset($request->generator_group_end_date[$GroupId])){
                        $endDate = $this->DateConvertToYMD($request->generator_group_end_date[$GroupId]);
                    }
                    $startTime = ($request->start_time !='') ? $request->start_time.':00' : '';
                    if(isset($request->generator_group_start_time[$GroupId])){
                        $startTime = $request->generator_group_start_time[$GroupId].':00';
                    }
                    $endTime = ($request->end_time !='') ? $request->end_time.':00' : '';
                    if(isset($request->generator_group_end_time[$GroupId])){
                        $endTime = $request->generator_group_end_time[$GroupId].':00';
                    }
                    // If record is already exists then we will update record
                    $this->ExamGradeClassMappingModel->find($ExistingRecord->id)->Update([
                        cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_EXAM_ID_COL => $ExamId,
                        cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_SCHOOL_ID_COL => Auth::user()->{cn::USERS_SCHOOL_ID_COL},
                        cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_PEER_GROUP_ID_COL => $GroupId,
                        cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_STUDENT_IDS_COL => ($PeerGroupStudentIds) ? implode(',',$PeerGroupStudentIds) : null,
                        cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_START_DATE_COL => $startDate ?? null,
                        cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_END_DATE_COL => $endDate ?? null,
                        cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_START_TIME_COL => $startTime,
                        cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_END_TIME_COL => $endTime,
                        cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_STATUS_COL => ($request->has('save_and_publish')) ? 'publish' : 'draft',
                        cn::DELETED_AT_COL => null
                    ]);
                }else{
                    $startDate = '';
                    if(isset($request->generator_group_start_date[$GroupId])){
                        $startDate = $this->DateConvertToYMD($request->generator_group_start_date[$GroupId]);
                    }
                    $endDate = '';
                    if(isset($request->generator_group_end_date[$GroupId])){
                        $endDate = $this->DateConvertToYMD($request->generator_group_end_date[$GroupId]);
                    }
                    $startTime = ($request->start_time !='') ? $request->start_time.':00' : '';
                    if(isset($request->generator_group_start_time[$GroupId])){
                        $startTime = $request->generator_group_start_time[$GroupId].':00';
                    }
                    $endTime = ($request->end_time !='') ? $request->end_time.':00' : '';
                    if(isset($request->generator_group_end_time[$GroupId])){
                        $endTime = $request->generator_group_end_time[$GroupId].':00';
                    }

                    // If record is not exists then we will create record
                    $this->ExamGradeClassMappingModel->Create([
                        cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_EXAM_ID_COL => $ExamId,
                        cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_SCHOOL_ID_COL => Auth::user()->{cn::USERS_SCHOOL_ID_COL},
                        cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_PEER_GROUP_ID_COL => $GroupId,
                        cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_STUDENT_IDS_COL => ($PeerGroupStudentIds) ? implode(',',$PeerGroupStudentIds) : null,
                        cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_START_DATE_COL => $startDate ?? null,
                        cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_END_DATE_COL => $endDate ?? null,
                        cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_START_TIME_COL => $startTime,
                        cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_END_TIME_COL => $endTime,
                        cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_STATUS_COL => ($request->has('save_and_publish')) ? 'publish' : 'draft'
                    ]);
                }
            }
        }
    }

    /**
     * USE : Find Peer Group Member by Group Id
     */
    public function FindPeerGroupStudentByGroupId($PeerGroupId){
        $PeerGroupMemberIds = [];
        if(!empty($PeerGroupId)){
            $PeerGroupMemberIds = PeerGroupMember::where(cn::PEER_GROUP_MEMBERS_PEER_GROUP_ID_COL,$PeerGroupId)->where(cn::PEER_GROUP_MEMBERS_STATUS_COL,1)->pluck(cn::PEER_GROUP_MEMBERS_MEMBER_ID_COL);
            if(!empty($PeerGroupMemberIds)){
                $PeerGroupMemberIds = $PeerGroupMemberIds->toArray();
            }
        }
        return $PeerGroupMemberIds;
    }

    /**
     * USE : Get Question Ids from AI Api
     */
    public function getQuestionIdsFromLearningObjectives(Request $request){
        if(isset($request)){
            $difficulty_lvl = $request->dificulty_level;
            $selected_levels = array();
            foreach ($difficulty_lvl as $difficulty_value) {
                $selected_levels[] = $difficulty_value-1;
            }
            $no_of_questions_per_learning_skills = $this->getGlobalConfiguration('no_of_questions_per_learning_skills');
            if(empty($no_of_questions_per_learning_skills)){
                $no_of_questions_per_learning_skills = 2;
            }
            $no_of_questions = 10;
            if(isset($request->no_of_questions) && !empty($request->no_of_questions)){
                $no_of_questions = $request->no_of_questions;
            }
            $GetStrandUnitLearningMappingIds = StrandUnitsObjectivesMappings::whereIn(cn::OBJECTIVES_MAPPINGS_STRAND_ID_COL,$request->strands_ids)
                                                    ->whereIn(cn::OBJECTIVES_MAPPINGS_LEARNING_UNIT_ID_COL,$request->learning_unit_id)
                                                    ->whereIn(cn::OBJECTIVES_MAPPINGS_LEARNING_OBJECTIVES_ID_COL,$request->learning_objectives_id)
                                                    ->get()->pluck(cn::OBJECTIVES_MAPPINGS_ID_COL)->toArray();
            // If the mapping id is available then get the question ids based on mapping ids
            if(sizeof($GetStrandUnitLearningMappingIds) > 0){
                $QuestionIds = $this->generateQuestionsAI($request,$GetStrandUnitLearningMappingIds);
               
                // Call to ALP AI My School Ability Analysis Graph API
                if(isset($QuestionIds) && !empty($QuestionIds)){
                    $requestPayload = new \Illuminate\Http\Request();
                    // call api based on selected mode for aiapi
                    switch($request->difficulty_mode){
                        case 'manual':
                                $requestPayload = $requestPayload->replace([
                                    'selected_levels'       => array_map('floatval', array_unique($selected_levels)),
                                    'coded_questions_list'  => $QuestionIds,
                                    'k'                     => floatval($no_of_questions),
                                    "repeated_rate"         => 0.1
                                ]);
                                $response = $this->AIApiService->Assign_Questions_Manually($requestPayload);
                            break;
                        case 'auto':
                                // Current student get overall abilities
                                $studentAbilities = Auth::user()->{cn::USERS_OVERALL_ABILITY_COL} ?? 0;                                        
                                $requestPayload = $requestPayload->replace([
                                    'students_abilities_list'   => array(floatval($studentAbilities)),
                                    'coded_questions_list'      => $coded_questions_list,
                                    'k'                         => floatval($no_of_questions),
                                    'n'                         => 50,
                                    'repeated_rate'             => 0.1
                                ]);
                                $response = $this->AIApiService->Assign_Questions_AutoMode($requestPayload);
                            break;
                    }
                    if(isset($response) && !empty($response)){
                        $responseQuestionCodes = array_column($response,0);
                        $question_list = Question::with(['answers','PreConfigurationDifficultyLevel','objectiveMapping'])
                                            ->whereIn(cn::QUESTION_NAMING_STRUCTURE_CODE_COL,$responseQuestionCodes)
                                            ->get()->toArray();
                        $question_id_list = Question::whereIn(cn::QUESTION_NAMING_STRUCTURE_CODE_COL,$responseQuestionCodes)
                                                    ->pluck(cn::QUESTION_TABLE_ID_COL)
                                                    ->toArray();
                        if(isset($question_id_list) && !empty($question_id_list)){
                            return $this->sendResponse(array('questionIds'=>$question_id_list,'question_list'=>$question_list));
                        }else{
                            return $this->sendError(__('languages.question_not_found'), 422);
                        }
                    }else{
                        return $this->sendError(__('languages.problem_was_occur_please_try_again'), 422);
                    }
                }else{
                    return $this->sendError(__('languages.problem_was_occur_please_try_again'), 422);
                }
            }else{
                return $this->sendError(__('languages.question_not_found'), 422);
            }
        }
    }

    /**
     * USE : Get the student list based on select grade and class
     * Return : Student list
     */
    public function getStudentListByGradeClass(Request $request){
        $StudentList = [];
        $optionList = '';
        $schoolId = Auth::user()->{cn::USERS_SCHOOL_ID_COL};
        $ClassId = [];
        $isTeacher = 0;
        $gradesListId =array();
        $GradeClassId ='';
        if($this->isTeacherLogin()){
            $gradesListId = TeachersClassSubjectAssign::where([
                                cn::TEACHER_CLASS_SUBJECT_TEACHER_ID_COL => Auth()->user()->{cn::USERS_ID_COL}
                            ])
                            ->pluck(cn::TEACHER_CLASS_SUBJECT_CLASS_ID_COL)
                            ->toArray();
            $GradeClassId = TeachersClassSubjectAssign::where([
                                cn::TEACHER_CLASS_SUBJECT_TEACHER_ID_COL => Auth()->user()->{cn::USERS_ID_COL}
                            ])
                            ->pluck(cn::TEACHER_CLASS_SUBJECT_CLASS_NAME_ID_COL)
                            ->toArray();
            $GradeClassId = implode(',', $GradeClassId);
            $GradeClassId = explode(',',$GradeClassId);
            $isTeacher = 1;
        }
        $studentList = User::where([cn::USERS_SCHOOL_ID_COL=>Auth::user()->{cn::USERS_SCHOOL_ID_COL},cn::USERS_ROLE_ID_COL=>cn::STUDENT_ROLE_ID])
            ->where(function ($query) use ($request,$gradesListId,$GradeClassId,$isTeacher){
                if(!isset($request->gradeIds) && !isset($request->classIds)){
                    $query->whereIn(cn::USERS_GRADE_ID_COL,$request->gradeIds)->whereIn(cn::USERS_CLASS_ID_COL,$request->classIds);
                }else{
                    if(isset($request->gradeIds) && !empty($request->gradeIds)){
                        $query->whereIn(cn::USERS_GRADE_ID_COL,$request->gradeIds);
                    }else if($isTeacher==1){
                        $query->whereIn(cn::USERS_GRADE_ID_COL,$gradesListId);
                    }

                    if(isset($request->classIds) && !empty($request->classIds)){
                        $query->whereIn(cn::USERS_CLASS_ID_COL,$request->classIds);
                    }else if($isTeacher==1){
                        $query->whereIn(cn::USERS_CLASS_ID_COL,$GradeClassId);
                    }
                }
            })->get();
        if(!$studentList->isEmpty()){
            foreach($studentList as $student){
                $optionList .= '<option value="'.$student->id.'">';
                if(app()->getLocale() == 'en'){
                    $optionList .= $student->DecryptNameEn;
                }else{
                    $optionList .= $student->DecryptNameCh;
                }
                if($student->class_student_number){
                    $optionList .= '('.$student->class_student_number.')';
                }
                $optionList .= '</option>';
            }
        }else{
            $optionList .= '<option value="">Students not available</option>';
        }
        return $this->sendResponse([$optionList]);
    }

    /**
     * USE : Get Question Ids in Admin from AI Api
     */
    public function getQuestionIdsFromLearningObjectivesByAdmin(Request $request){
        if(isset($request)){
            if(isset($request->exam_id) && $request->exam_id!=""){
                $exam = Exam::find($request->exam_id);
                if(!empty($exam) && $exam->learning_objectives_configuration != ""){
                    $learningObjectivesConfigurations = json_encode($request->learning_unit);
                    if($exam->learning_objectives_configuration == $learningObjectivesConfigurations){
                        if($exam->question_ids != ""){
                            $questionIds = explode(',',$exam->question_ids);
                            $question_list = Question::with(['answers','PreConfigurationDifficultyLevel','objectiveMapping'])
                                                ->whereIn(cn::QUESTION_TABLE_ID_COL,$questionIds)
                                                ->orderByRaw('FIELD(id,'.$exam->question_ids.')')
                                                ->get()
                                                ->toArray();
                            $question_id_list = Question::whereIn(cn::QUESTION_TABLE_ID_COL,$questionIds)
                                                        ->pluck(cn::QUESTION_TABLE_ID_COL)
                                                        ->toArray();
                            if(isset($question_id_list) && !empty($question_id_list)){
                                return $this->sendResponse(
                                    array(
                                        'questionIds' => $question_id_list,
                                        'question_list' => $question_list
                                    )
                                );
                            }
                        }
                    }
                }
            }
            $minimumQuestionPerSkill = \App\Helpers\Helper::getGlobalConfiguration('no_of_questions_per_learning_skills') ?? 2 ;
            $learningUnitArray = array();
            if(isset($request->learning_unit) && !empty($request->learning_unit)){
                foreach ($request->learning_unit as $learningUnitId => $learningUnitData) {
                    $learningObjectiveQuestionArray = array();
                    if(isset($learningUnitData['learning_objective']) && !empty($learningUnitData['learning_objective'])){
                        foreach ($learningUnitData['learning_objective'] as $id => $data) {
                            $learningObjectiveSkillQuestionArray = array();
                            if(isset($data['learning_objectives_difficulty_level']) && !empty($data['learning_objectives_difficulty_level']) && isset($data['get_no_of_question_learning_objectives']) && !empty($data['get_no_of_question_learning_objectives'])){
                                $objective_mapping_id = StrandUnitsObjectivesMappings::where(cn::OBJECTIVES_MAPPINGS_LEARNING_UNIT_ID_COL,$learningUnitId)
                                                            ->where(cn::OBJECTIVES_MAPPINGS_LEARNING_OBJECTIVES_ID_COL,$id)
                                                            ->pluck(cn::OBJECTIVES_MAPPINGS_ID_COL)->toArray();
                                $LearningsSkillAll = array_keys($learningUnitData['learning_objective']);
                                $selected_levels = array();
                                foreach($data['learning_objectives_difficulty_level'] as $difficulty_value){
                                    $selected_levels[] = ($difficulty_value - 1);
                                }
                                $learningsObjectivesData = LearningsObjectives::find($id);
                                $LearningsSkill = $learningsObjectivesData->code;
                                $QuestionSkill = Question::with('PreConfigurationDifficultyLevel')
                                                    ->whereIn(cn::QUESTION_OBJECTIVE_MAPPING_ID_COL,$objective_mapping_id)
                                                    //->where(cn::QUESTION_QUESTION_TYPE_COL,$request->test_type)
                                                    ->whereIn(cn::QUESTION_QUESTION_TYPE_COL,[2,3])
                                                    ->groupBy(cn::QUESTION_E_COL)
                                                    ->inRandomOrder()
                                                    ->pluck(cn::QUESTION_E_COL)
                                                    ->toArray();
                                $no_of_questions = $data['get_no_of_question_learning_objectives'];
                                $oldQuestionIds = array();
                                $coded_questions_list = array();
                                $qLoop = 0;
                                $qSize = 0;
                                //while($qLoop <= $no_of_questions){
                                    if(!empty($QuestionSkill)){
                                        foreach($QuestionSkill as $skillKey => $skillName){
                                            $questionArray = Question::with('PreConfigurationDifficultyLevel')
                                                                ->whereNotIn(cn::QUESTION_TABLE_ID_COL,$oldQuestionIds)
                                                                ->where(cn::QUESTION_E_COL,$skillName)
                                                                ->whereIn(cn::QUESTION_OBJECTIVE_MAPPING_ID_COL,$objective_mapping_id)
                                                                //->where(cn::QUESTION_QUESTION_TYPE_COL,$request->test_type)
                                                                ->whereIn(cn::QUESTION_QUESTION_TYPE_COL,[2,3])
                                                                ->whereIn(cn::QUESTION_DIFFICULTY_LEVEL_COL,$data['learning_objectives_difficulty_level'])
                                                                //->limit($minimumQuestionPerSkill)
                                                                ->inRandomOrder()
                                                                ->get()
                                                                ->toArray();
                                            if(!empty($questionArray)){
                                                $coded_questions_list = array();
                                                foreach ($questionArray as $question_key => $question_value) {
                                                    $oldQuestionIds[] = $question_value['id'];
                                                    $coded_questions_list[] = array($question_value[cn::QUESTION_NAMING_STRUCTURE_CODE_COL],floatval($question_value['pre_configuration_difficulty_level']['title']),0);                                                    
                                                }
                                                if(!empty($coded_questions_list)){
                                                    //$coded_questions_list = array_slice($coded_questions_list,0,$no_of_questions);
                                                    if($skillKey==0){
                                                        $learningObjectiveSkillQuestionArray[] = array($selected_levels,$coded_questions_list, floatval(round($no_of_questions/sizeOf($QuestionSkill))),0.01);
                                                    }else{
                                                        $learningObjectiveSkillQuestionArray[] = array($selected_levels,$coded_questions_list, floatval(floor($no_of_questions/sizeOf($QuestionSkill))),0.01);
                                                    }
                                                }
                                            }
                                            // $qSize = sizeof($coded_questions_list);
                                            // if($qSize >= $no_of_questions){
                                            //     break;
                                            // }
                                        }
                                        
                                        // if($qSize >= $no_of_questions){
                                        //     break;
                                        // }
                                    }

                                    //echo '<pre>';print_r($learningObjectiveSkillQuestionArray);die;
                                    //$qLoop++;
                                //}
                                // if(!empty($coded_questions_list)){
                                //     //$coded_questions_list = array_slice($coded_questions_list,0,$no_of_questions);
                                //     $learningObjectiveQuestionArray[] = array($selected_levels,$coded_questions_list,floatval(sizeof($coded_questions_list)),0.01);
                                // }
                                // if(!empty($coded_questions_list)){
                                //     //$coded_questions_list = array_slice($coded_questions_list,0,$no_of_questions);
                                //     $learningObjectiveQuestionArray[] = array($selected_levels,$coded_questions_list,floatval(sizeof($coded_questions_list)),0.01);
                                // }
                            }
                            $learningUnitArray[] = $learningObjectiveSkillQuestionArray;
                        }
                    }
                }
            }

            if(sizeof($learningUnitArray) > 0){
                if(isset($learningUnitArray) && !empty($learningUnitArray)){
                    $requestPayload = new \Illuminate\Http\Request();
                    // call api based on selected mode for aiapi
                    switch($request->difficulty_mode){
                        case 'manual':
                                $requestPayload = $requestPayload->replace([
                                    'learning_units'       => array($learningUnitArray)
                                ]);
                                $response = $this->AIApiService->Assign_Questions_Manually_To_Learning_Units($requestPayload);
                        break;
                    }
                    $responseQuestionCodesArray = array();
                    if(isset($response) && !empty($response)){
                        foreach($response as $learningObjectiveArray){
                            foreach($learningObjectiveArray as $learningSkillArray){
                                foreach($learningSkillArray as $value){
                                    //foreach($value as $questionData){
                                    foreach($value[0] as $questionData){
                                        //$questionDataCodes = array_column($questionData,0);
                                        $questionDataCodes = $questionData[0];
                                        if(isset($questionDataCodes) && !empty($questionDataCodes)){
                                            //$responseQuestionCodesArray = array_merge($responseQuestionCodesArray,$questionDataCodes);
                                            $responseQuestionCodesArray = array_merge($responseQuestionCodesArray,[$questionDataCodes]);
                                        }
                                    }
                                }
                            }
                        }

                        if(isset($responseQuestionCodesArray) && !empty($responseQuestionCodesArray)){
                            $question_list = Question::with(['answers','PreConfigurationDifficultyLevel','objectiveMapping'])->whereIn(cn::QUESTION_NAMING_STRUCTURE_CODE_COL,$responseQuestionCodesArray)->get()->toArray();
                            $question_id_list = Question::whereIn(cn::QUESTION_NAMING_STRUCTURE_CODE_COL,$responseQuestionCodesArray)->inRandomOrder()
                                                        ->pluck(cn::QUESTION_TABLE_ID_COL)
                                                        ->toArray();
                            if(isset($question_id_list) && !empty($question_id_list)){
                                return $this->sendResponse(
                                    array(
                                        'questionIds' => $question_id_list,
                                        'question_list' => $question_list
                                    )
                                );
                            }else{
                                return $this->sendError(__('languages.not_enough_questions_in_that_objective'), 422);
                            }
                        }else{
                            return $this->sendError(__('languages.not_enough_questions_in_that_objective'), 422);
                        }
                    }else{
                        return $this->sendError(__('languages.problem_was_occur_please_try_again'), 422);
                    }
                }else{
                    return $this->sendError(__('languages.problem_was_occur_please_try_again'), 422);
                }
            }else{
                return $this->sendError(__('languages.not_enough_questions_in_that_objective'), 422);
            }
        }
    }

    public function nextLearningsObjectivesSkillQuestion($skill,$LearningsSkillAll,$difficultyLevel,$numberOfQuestion,$learningUnitId){
        // $nextLearningsObjectivesSkill = LearningsObjectives::where(cn::LEARNING_OBJECTIVES_LEARNING_UNITID_COL,$learningUnitId)
        //                                     ->whereNotIn(cn::LEARNING_OBJECTIVES_ID_COL,$LearningsSkillAll)
        //                                     ->where(cn::LEARNING_OBJECTIVES_ID_COL, '>', $skill)
        //                                     ->min(cn::LEARNING_OBJECTIVES_ID_COL);
        $nextLearningsObjectivesSkill = LearningsObjectives::IsAvailableQuestion()
                                            ->where(cn::LEARNING_OBJECTIVES_LEARNING_UNITID_COL,$learningUnitId)
                                            ->whereNotIn(cn::LEARNING_OBJECTIVES_ID_COL,$LearningsSkillAll)
                                            ->where(cn::LEARNING_OBJECTIVES_ID_COL, '>', $skill)
                                            ->min(cn::LEARNING_OBJECTIVES_ID_COL);
        if(isset($nextLearningsObjectivesSkill) && !empty($nextLearningsObjectivesSkill)){
            $questionArray = Question::with('PreConfigurationDifficultyLevel')
                                ->where(cn::QUESTION_E_COL,$nextLearningsObjectivesSkill)
                                ->whereIn(cn::QUESTION_DIFFICULTY_LEVEL_COL,$difficultyLevel)
                                ->limit($numberOfQuestion)
                                ->get()
                                ->toArray();
            if(!empty($questionArray)){
                if(sizeof($questionArray) < $numberOfQuestion){
                    $this->nextLearningsObjectivesSkillQuestion($nextLearningsObjectivesSkill,$LearningsSkillAll,$difficultyLevel,$numberOfQuestion,$learningUnitId);
                }else{
                    $selected_levels = array();
                    foreach($difficultyLevel as $difficulty_value){
                        $selected_levels[] = ($difficulty_value - 1);
                    }
                    $coded_questions_list = array();
                    foreach($questionArray as $question_key => $question_value){
                        $coded_questions_list[] = array($question_value[cn::QUESTION_NAMING_STRUCTURE_CODE_COL],floatval($question_value['pre_configuration_difficulty_level']['title']),0);
                    }
                    $learningObjectiveQuestionArray = array($selected_levels,$coded_questions_list,floatval($numberOfQuestion),0.01);
                    return $learningObjectiveQuestionArray;
                }
            }else{
               $this->nextLearningsObjectivesSkillQuestion($nextLearningsObjectivesSkill,$LearningsSkillAll,$difficultyLevel,$numberOfQuestion,$learningUnitId);
            }
        }else{
            return $nextLearningsObjectivesSkill;
        }
    }

    /**
     * USE : Get new one question selected learning objectives and difficulty level
     * Return : One question
     */
    public function getRefreshQuestion(Request $request){
        if(isset($request)){
            $difficulty_lvl = $request->dificulty_level;
            $selected_levels = array();
            foreach ($difficulty_lvl as $difficulty_value) {
                $selected_levels[] = $difficulty_value-1;
            }
            $no_of_questions_per_learning_skills = $this->getGlobalConfiguration('no_of_questions_per_learning_skills');
            if(empty($no_of_questions_per_learning_skills)){
                $no_of_questions_per_learning_skills = 2;
            }
            $no_of_questions = 1;
            $GetStrandUnitLearningMappingIds = StrandUnitsObjectivesMappings::whereIn(cn::OBJECTIVES_MAPPINGS_STRAND_ID_COL,$request->strands_ids)
                                                    ->whereIn(cn::OBJECTIVES_MAPPINGS_LEARNING_UNIT_ID_COL,$request->learning_unit_id)
                                                    ->whereIn(cn::OBJECTIVES_MAPPINGS_LEARNING_OBJECTIVES_ID_COL,$request->learning_objectives_id)
                                                    ->get()->pluck(cn::OBJECTIVES_MAPPINGS_ID_COL)->toArray();
            // If the mapping id is available then get the question ids based on mapping ids
            if(sizeof($GetStrandUnitLearningMappingIds) > 0){
                $QuestionIds = $this->generateQuestionsAIQenerateQuestions($request,$GetStrandUnitLearningMappingIds);
                // Call to ALP AI My School Ability Analysis Graph API
                if(isset($QuestionIds) && !empty($QuestionIds)){
                    $requestPayload = new \Illuminate\Http\Request();
                    // call api based on selected mode for aiapi
                    switch($request->difficulty_mode){
                        case 'manual':
                                $requestPayload = $requestPayload->replace([
                                    'selected_levels'       => array_map('floatval', array_unique($selected_levels)),
                                    'coded_questions_list'  => $QuestionIds,
                                    'k'                     => floatval($no_of_questions),
                                    "repeated_rate"         => 0.1
                                ]);
                                $response = $this->AIApiService->Assign_Questions_Manually($requestPayload);
                            break;
                        case 'auto':
                                // Current student get overall abilities
                                $studentAbilities = Auth::user()->{cn::USERS_OVERALL_ABILITY_COL} ?? 0;                                        
                                $requestPayload = $requestPayload->replace([
                                    'students_abilities_list'   => array(floatval($studentAbilities)),
                                    'coded_questions_list'      => $coded_questions_list,
                                    'k'                         => floatval($no_of_questions),
                                    'n'                         => 50,
                                    'repeated_rate'             => 0.1
                                ]);
                                $response = $this->AIApiService->Assign_Questions_AutoMode($requestPayload);
                            break;
                    }
                    if(isset($response) && !empty($response)){
                        $responseQuestionCodes = array_column($response[0],0);
                        $question_list = Question::with(['answers','PreConfigurationDifficultyLevel','objectiveMapping'])
                                            ->whereIn(cn::QUESTION_NAMING_STRUCTURE_CODE_COL,$responseQuestionCodes)
                                            ->inRandomOrder();
                        $question_id_list = $question_list->pluck(cn::QUESTION_TABLE_ID_COL)->toArray();
                        $question_list = $question_list->get()->toArray();
                        if(isset($question_id_list) && !empty($question_id_list)){
                            return $this->sendResponse(array('questionIds' => $question_id_list, 'question_list' => $question_list));
                        }else{
                            return $this->sendError(__('languages.questions-not-found'), 422);
                        }
                    }else{
                        return $this->sendError(__('languages.questions-not-found'), 422);
                    }
                }else{
                    return $this->sendError(__('languages.questions-not-found'), 422);
                }
            }else{
                return $this->sendError(__('languages.questions-not-found'), 422);
            }
        }
    }

    /**
     * USE : Get Question Ids in School from AI Api
     */
    public function getQuestionIdsFromLearningObjectivesBySchool(Request $request){
        if(isset($request)){
            $difficultyLevels = PreConfigurationDiffiltyLevel::all();
            $result = array();
            if(isset($request->exam_id) && $request->exam_id!=""){
                $exam = Exam::find($request->exam_id);
                if(!empty($exam) && $exam->learning_objectives_configuration!=""){
                    $learningObjectivesConfigurations = json_encode($request->learning_unit);
                    if($exam->learning_objectives_configuration == $learningObjectivesConfigurations){
                        if($exam->question_ids!=""){
                            $questionIds = explode(',',$exam->question_ids);
                            $question_list = Question::with(['answers','PreConfigurationDifficultyLevel','objectiveMapping'])
                                                ->whereIn(cn::QUESTION_TABLE_ID_COL,$questionIds)->get();
                            $question_id_list = Question::whereIn(cn::QUESTION_TABLE_ID_COL,$questionIds)->pluck(cn::QUESTION_TABLE_ID_COL)->toArray();
                            if(isset($question_id_list) && !empty($question_id_list)){
                                $result['html'] = (string)View::make('backend.question_generator.school.question_list_preview',compact('question_list','difficultyLevels'));
                                $result['questionIds'] = $question_id_list;
                                return $this->sendResponse($result);
                            }
                        }
                    }
                }
            }
            $minimumQuestionPerSkill = \App\Helpers\Helper::getGlobalConfiguration('no_of_questions_per_learning_skills') ?? 2 ;
            $learningUnitArray = array();
            $coded_questions_list_all = array();
            $difficulty_lvl = $request->difficulty_lvl;
            if(isset($request->difficulty_mode) && $request->difficulty_mode=='auto' && isset($studentIds) && empty($studentIds)){
                return $this->sendError(__('languages.please_select_students'), 422);
            }
            $learningUnitArray = array();
            if(isset($request->learning_unit) && !empty($request->learning_unit)){
                foreach($request->learning_unit as $learningUnitId => $learningUnitData){
                    $learningObjectiveQuestionArray = array();
                    if(isset($learningUnitData['learning_objective']) && !empty($learningUnitData['learning_objective'])){
                        foreach($learningUnitData['learning_objective'] as $id => $data){
                            $learningObjectiveSkillQuestionArray = array();
                            $learningObjectiveQuestionArray = array();
                            $coded_questions_list = array();
                            $oldQuestionIds = array();
                            //if(isset($data['learning_objectives_difficulty_level']) && !empty($data['learning_objectives_difficulty_level']) && isset($data['get_no_of_question_learning_objectives']) && !empty($data['get_no_of_question_learning_objectives']) && $request->difficulty_mode == 'manual'){
                            $objective_mapping_id = StrandUnitsObjectivesMappings::where(cn::OBJECTIVES_MAPPINGS_LEARNING_UNIT_ID_COL,$learningUnitId)
                                                    ->where(cn::OBJECTIVES_MAPPINGS_LEARNING_OBJECTIVES_ID_COL,$id)
                                                    ->pluck(cn::OBJECTIVES_MAPPINGS_ID_COL)
                                                    ->toArray();
                            $LearningsSkillAll = array_keys($learningUnitData['learning_objective']);
                            $selected_levels = array();
                            if($request->difficulty_mode == 'manual' && array_key_exists('learning_objectives_difficulty_level',$data)){
                                foreach($data['learning_objectives_difficulty_level'] as $difficulty_value){
                                    $selected_levels[] = ($difficulty_value - 1);
                                }
                            }
                            $learningsObjectivesData = LearningsObjectives::find($id);
                            $LearningsSkill = $learningsObjectivesData->code;
                            $QuestionSkill = Question::with('PreConfigurationDifficultyLevel')
                                            ->whereIn(cn::QUESTION_OBJECTIVE_MAPPING_ID_COL,$objective_mapping_id)
                                            //->where(cn::QUESTION_QUESTION_TYPE_COL,$request->test_type)
                                            ->whereIn(cn::QUESTION_QUESTION_TYPE_COL,[2,3])
                                            ->inRandomOrder()
                                            ->groupBy(cn::QUESTION_E_COL)
                                            ->pluck(cn::QUESTION_E_COL)
                                            ->toArray();
                            $no_of_questions = $data['get_no_of_question_learning_objectives'];
                            if(!empty($QuestionSkill)){
                                foreach($QuestionSkill as $skillKey => $skillName){
                                    $QuestionQuery = Question::with('PreConfigurationDifficultyLevel')
                                                        ->whereNotIn(cn::QUESTION_TABLE_ID_COL,$oldQuestionIds)
                                                        ->whereIn(cn::QUESTION_OBJECTIVE_MAPPING_ID_COL,$objective_mapping_id)
                                                        //->where(cn::QUESTION_QUESTION_TYPE_COL,$request->test_type)
                                                        ->whereIn(cn::QUESTION_QUESTION_TYPE_COL,[2,3])
                                                        ->where(cn::QUESTION_E_COL,$skillName);
                                    if($request->difficulty_mode == 'manual' && array_key_exists('learning_objectives_difficulty_level',$data)){
                                        $QuestionQuery->whereIn(cn::QUESTION_DIFFICULTY_LEVEL_COL,$data['learning_objectives_difficulty_level']);
                                    }
                                    //->limit($minimumQuestionPerSkill)
                                    $questionArray = $QuestionQuery->inRandomOrder()->get()->toArray();
                                    if(!empty($questionArray)){
                                        $coded_questions_list = array();
                                        foreach ($questionArray as $question_key => $question_value) {
                                            $oldQuestionIds[] = $question_value['id'];
                                            $coded_questions_list[] = array($question_value[cn::QUESTION_NAMING_STRUCTURE_CODE_COL],floatval($question_value['pre_configuration_difficulty_level']['title']),0);                                                    
                                        }
                                        if(!empty($coded_questions_list)){
                                            if($request->difficulty_mode == 'manual'){
                                                if($skillKey==0){
                                                    $learningObjectiveSkillQuestionArray[] = array($selected_levels,$coded_questions_list, floatval(round($no_of_questions/sizeOf($QuestionSkill))),0.01);
                                                }else{
                                                    $learningObjectiveSkillQuestionArray[] = array($selected_levels,$coded_questions_list, floatval(floor($no_of_questions/sizeOf($QuestionSkill))),0.01);
                                                }
                                            }
                                            if($request->difficulty_mode == 'auto'){
                                                if($skillKey==0){
                                                    $learningObjectiveSkillQuestionArray[] = array($coded_questions_list, floatval(round($no_of_questions/sizeOf($QuestionSkill))));
                                                }else{
                                                    $learningObjectiveSkillQuestionArray[] = array($coded_questions_list, floatval(floor($no_of_questions/sizeOf($QuestionSkill))));
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                            $learningUnitArray[] = $learningObjectiveSkillQuestionArray;
                        }
                    }
                }
            }

            if(sizeof($learningUnitArray) > 0){
                if(isset($learningUnitArray) && !empty($learningUnitArray)){
                    $requestPayload = new \Illuminate\Http\Request();
                    // call api based on selected mode for ai-api
                    switch($request->difficulty_mode){
                        case 'manual':
                                $requestPayload =   $requestPayload->replace([
                                                        'learning_units'       => array($learningUnitArray)
                                                    ]);
                                $response = $this->AIApiService->Assign_Questions_Manually_To_Learning_Units($requestPayload);
                            break;
                        case 'auto':
                                $studentIds = [];
                                if(isset($request->peerGroupIds) && !empty($request->peerGroupIds)){
                                    $PeerGroupMembers = PeerGroupMember::whereIn('peer_group_id',$request->peerGroupIds)->where('status',1)->pluck('member_id')->unique();
                                    if($PeerGroupMembers->isNotEmpty()){
                                        $studentIds = $PeerGroupMembers->toArray();
                                    }
                                }else{
                                    $studentIds = $request->studentIds;
                                }
                                $studentAbilities = User::whereIn(cn::USERS_ID_COL,$studentIds)
                                                        ->where([cn::USERS_ROLE_ID_COL => cn::STUDENT_ROLE_ID,cn::USERS_STATUS_COL => 'active'])
                                                        ->pluck(cn::USERS_OVERALL_ABILITY_COL)->toArray();
                                if(isset($studentAbilities) && !empty($studentAbilities)){
                                    foreach($studentAbilities as $abilityKey => $StuAbility){
                                        //  Check student ability is available or not
                                        if($StuAbility==""){
                                            // If ability is not available then we will set default 0 in the request payload
                                            $studentAbilities[$abilityKey] = $this->DefaultStudentOverAllAbility;  // This variable is protected variable used into construct function
                                        }else{
                                            $studentAbilities[$abilityKey] = floatval($StuAbility);
                                        }
                                    }
                                }
                                $requestPayload =   $requestPayload->replace([
                                    'students_abilities_list'   => $studentAbilities,
                                    'learning_units'            => array($learningUnitArray),
                                    'n'                         => 50,
                                    'repeated_rate'             => 0.05

                                ]);
                                $response = $this->AIApiService->Assign_Questions_To_Learning_Units($requestPayload);
                            break;
                    }
                    $responseQuestionCodesArray = array();
                    if(isset($response) && !empty($response)){
                        foreach($response as $learningObjectiveArray){
                            foreach($learningObjectiveArray as $learningSkillArray){
                                foreach($learningSkillArray as $value){
                                    //foreach($value as $questionData){
                                    foreach($value[0] as $questionData){
                                        //$questionDataCodes = array_column($questionData,0);
                                        $questionDataCodes = $questionData[0];
                                        if(isset($questionDataCodes) && !empty($questionDataCodes)){
                                            //$responseQuestionCodesArray = array_merge($responseQuestionCodesArray,$questionDataCodes);
                                            $responseQuestionCodesArray = array_merge($responseQuestionCodesArray,[$questionDataCodes]);
                                        }
                                    }
                                }
                            }
                        }

                        if(isset($responseQuestionCodesArray) && !empty($responseQuestionCodesArray)){
                            $question_list = Question::with(['answers','PreConfigurationDifficultyLevel','objectiveMapping'])->whereIn(cn::QUESTION_NAMING_STRUCTURE_CODE_COL,$responseQuestionCodesArray)->get();
                            $question_id_list = Question::whereIn(cn::QUESTION_NAMING_STRUCTURE_CODE_COL,$responseQuestionCodesArray)->inRandomOrder()
                                                        ->pluck(cn::QUESTION_TABLE_ID_COL)
                                                        ->toArray();
                            if(isset($question_id_list) && !empty($question_id_list)){
                                $result['html'] = (string)View::make('backend.question_generator.school.question_list_preview',compact('question_list','difficultyLevels'));
                                $result['questionIds'] = $question_id_list;
                                return $this->sendResponse($result);
                            }else{
                                return $this->sendError(__('languages.questions-not-found'), 422);
                            }
                        }else{
                            return $this->sendError(__('languages.not_enough_questions_in_that_objective'), 422);
                        }
                    }else{
                        return $this->sendError(__('languages.problem_was_occur_please_try_again'), 422);
                    }
                }
            }else{
                return $this->sendError(__('languages.not_enough_questions_in_that_objective'), 422);
            }
                       

            // if(sizeof($coded_questions_list_all) > 0){
            //     if(isset($coded_questions_list_all) && !empty($coded_questions_list_all)){
            //         $requestPayload = new \Illuminate\Http\Request();
            //         // call api based on selected mode for AIApi
            //         switch($request->difficulty_mode){
            //             case 'manual':
            //                     $requestPayload = $requestPayload->replace([
            //                         'selected_levels'       => $selected_levels,
            //                         'coded_questions_list'  => $coded_questions_list_all,
            //                         //'k'                     => floatval(sizeof($coded_questions_list_all)),
            //                         'k'                     => floatval($no_of_questions),
            //                         "repeated_rate"         => 0.1
            //                     ]);
            //                     $response = $this->AIApiService->Assign_Questions_Manually($requestPayload);
            //                 break;
            //             case 'auto':
            //                     $studentIds = [];
            //                     if(isset($request->peerGroupIds) && !empty($request->peerGroupIds)){
            //                         $PeerGroupMembers = PeerGroupMember::whereIn('peer_group_id',$request->peerGroupIds)->where('status',1)->pluck('member_id')->unique();
            //                         if($PeerGroupMembers->isNotEmpty()){
            //                             $studentIds = $PeerGroupMembers->toArray();
            //                         }
            //                     }else{
            //                         $studentIds = $request->studentIds;
            //                     }
            //                     $studentAbilities = User::whereIn(cn::USERS_ID_COL,$studentIds)
            //                                             ->where([cn::USERS_ROLE_ID_COL => cn::STUDENT_ROLE_ID,cn::USERS_STATUS_COL => 'active'])
            //                                             ->pluck(cn::USERS_OVERALL_ABILITY_COL)->toArray();
            //                     $requestPayload = $requestPayload->replace([
            //                         'students_abilities_list'   => array(floatval($studentAbilities)),
            //                         'coded_questions_list'      => $coded_questions_list_all,
            //                         //'k'                       => floatval(sizeof($coded_questions_list_all)),
            //                         'k'                         => floatval($no_of_questions),
            //                         'n'                         => 50,
            //                         'repeated_rate'             => 0.1
            //                     ]);
            //                     $response = $this->AIApiService->Assign_Questions_AutoMode($requestPayload);
            //                 break;
            //         }
            //         if(isset($response) && !empty($response)){
            //             $responseQuestionCodes = array_column($response[0],0);
            //             $question_list = Question::with(['answers','PreConfigurationDifficultyLevel','objectiveMapping'])->whereIn(cn::QUESTION_NAMING_STRUCTURE_CODE_COL,$responseQuestionCodes)->get();
            //             $question_id_list = Question::whereIn(cn::QUESTION_NAMING_STRUCTURE_CODE_COL,$responseQuestionCodes)->pluck(cn::QUESTION_TABLE_ID_COL)->toArray();
            //             if(isset($question_id_list) && !empty($question_id_list)){
            //                 $result['html'] = (string)View::make('backend.question_generator.school.question_list_preview',compact('question_list','difficultyLevels'));
            //                 $result['questionIds'] = $question_id_list;
            //                 return $this->sendResponse($result);
            //             }else{
            //                 return $this->sendError(__('languages.questions-not-found'), 422);
            //             }
            //         }else{
            //             return $this->sendError(__('languages.problem_was_occur_please_try_again'), 422);
            //         }
            //     }else{
            //         return $this->sendError(__('languages.problem_was_occur_please_try_again'), 422);
            //     }
            // }else{
            //     return $this->sendError(__('languages.not_enough_questions_in_that_objective'), 422);
            // }
        }
    }

    /**
     * USE : Question Generator wizard listing
     */
    public function QuestionWizardList(Request $request){
        if(!in_array('exam_management_read', Helper::getPermissions(Auth::user()->{cn::USERS_ID_COL}))) {
            return  redirect(Helper::redirectRoleBasedDashboard(Auth::user()->{cn::USERS_ID_COL}));
        }
        $items = $request->items ?? 10;
        $examTypes = array(
            ['id'=> 4, 'name' => 'All'],
            ['id'=> 1, 'name' => 'Self-Learning'],
            ['id'=> 2, 'name' => 'Exercise'],
            ['id'=> 3, 'name' => 'Test']
        );
        $statusLists = $this->ExamStatusList();
        $difficultyLevels = $this->getDifficultyLevel();
        switch(Auth::user()->{cn::USERS_ROLE_ID_COL}){
            case 1 : // 1 = Super Admin Role
                $examList = $this->Exam->where(function ($q1){
                                    $q1->where(cn::EXAM_TABLE_STATUS_COLS,'<>','inactive')
                                    ->whereHas('ExamSchoolMapping',function($q2) {
                                        $q2->where(cn::EXAM_SCHOOL_MAPPING_STATUS_COL,'<>','inactive');
                                    });
                                })
                                ->where(function($query){
                                    $query->whereIn(cn::EXAM_TABLE_USE_OF_MODE_COLS,[1,2])
                                    ->whereNull(cn::EXAM_TABLE_PARENT_EXAM_ID_COLS);
                                })
                                ->orWhere(cn::EXAM_TABLE_CREATED_BY_USER_COL,'student')
                                ->where(function ($q){
                                    $q->where(cn::EXAM_TYPE_COLS,'<>',1);
                                })
                                ->orderBy(cn::EXAM_TABLE_ID_COLS,'DESC')
                                ->sortable()
                                ->paginate($items);
                if(isset($request->filter)){
                    // $Query = $this->Exam->select('*');
                    $Query = $this->Exam->with(['ExamSchoolMapping']);
                    if($request->status != 'inactive'){
                        $Query->where(function ($q1){
                            $q1->where(cn::EXAM_TABLE_STATUS_COLS,'<>','inactive')
                            ->whereHas('ExamSchoolMapping',function($q2) {
                                $q2->where(cn::EXAM_SCHOOL_MAPPING_STATUS_COL,'<>','inactive');
                            });
                        });
                    }

                    //search by Exam title
                    if(isset($request->title) && !empty($request->title)){
                        $Query->where(function($q) use($request){
                            $q->where(cn::EXAM_TABLE_TITLE_COLS,'like','%'.$request->title.'%')->orWhere(cn::EXAM_REFERENCE_NO_COL,'like','%'.$request->title.'%');
                        });
                    }
                    
                    //From Date                 
                    if(isset($request->from_date) && !empty($request->from_date)){
                        $from_date = $this->DateConvertToYMD($request->from_date);
                        $Query->whereRaw(cn::EXAM_TABLE_FROM_DATE_COLS." >= '$from_date'");
                    }

                    //To Date
                    if(isset($request->to_date) && !empty($request->to_date)){
                        $to_date = $this->DateConvertToYMD($request->to_date);
                        $Query->whereRaw(cn::EXAM_TABLE_TO_DATE_COLS." <= '$to_date'");
                    }

                    //search by Exam Type
                    if(isset($request->test_type) && !empty($request->test_type)){
                        //Check Exam type Not Equal to All
                        if($request->test_type != 4){
                            if($request->test_type!=1){
                                $Query->whereIn(cn::EXAM_TABLE_USE_OF_MODE_COLS,[1,2])->whereNull(cn::EXAM_TABLE_PARENT_EXAM_ID_COLS);
                            }else{
                                $Query->where(cn::EXAM_TABLE_USE_OF_MODE_COLS,null)->where(cn::EXAM_TABLE_PARENT_EXAM_ID_COLS,null);
                            }
                            $Query->where(cn::EXAM_TYPE_COLS,$request->test_type);
                        }else{
                            $Query->where(cn::EXAM_TYPE_COLS,'<>',1);
                        }
                    }else{
                        // With out self-learning
                        $Query->whereIn(cn::EXAM_TABLE_USE_OF_MODE_COLS,[1,2])->whereNull(cn::EXAM_TABLE_PARENT_EXAM_ID_COLS);
                    }

                    //search by Exam Status
                    if(isset($request->status) && !empty($request->status)){                        
                        $Query->where(function($q) use($request){
                            $q->where(function($q1) use($request){
                                $q1->where(cn::EXAM_TABLE_STATUS_COLS,$request->status)
                                ->whereHas('ExamSchoolMapping',function($q1) use($request){
                                    $q1->where(cn::EXAM_SCHOOL_MAPPING_STATUS_COL,$request->status);
                                })->orWhere(function($q2) use($request){
                                    $q2->orWhere(cn::EXAM_TABLE_STATUS_COLS,$request->status);
                                });
                            });
                        });
                        // ->orWhere(function($q) use($request){
                        //     $q->orWhere(cn::EXAM_TABLE_CREATED_BY_USER_COL,'<>','super_admin')
                        //     ->whereHas('ExamSchoolMapping',function($q1) use($request){
                        //         $q1->where(cn::EXAM_SCHOOL_MAPPING_STATUS_COL,$request->status);
                        //     })
                        //     ->where(cn::EXAM_TYPE_COLS,$request->test_type);
                        // });
                    }
                    
                    $examList = $Query->sortable()->orderBy(cn::EXAM_TABLE_ID_COLS,'DESC')->paginate($items);
                }
                return view('backend/question_generator/admin/question_wizard_list',compact('examList','items','examTypes','statusLists','difficultyLevels'));
                break;
            
            case 5 : //  = School Role
            case 7 ://  = Principal Role
                if($this->isSchoolLogin() || $this->isPrincipalLogin()){
                    $SchoolId = Auth::user()->{cn::USERS_SCHOOL_ID_COL};
                    $GetAssignedExamIds = $this->ExamSchoolMapping->where(cn::EXAM_SCHOOL_MAPPING_SCHOOL_ID_COL,$SchoolId)
                                            ->orderBy(cn::EXAM_SCHOOL_MAPPING_EXAM_ID_COL,'DESC')
                                            ->pluck(cn::EXAM_SCHOOL_MAPPING_EXAM_ID_COL);
                    $examList = $this->Exam->with(['ExamSchoolMapping' => function($q) use($SchoolId){
                                        $q->where(cn::EXAM_SCHOOL_MAPPING_SCHOOL_ID_COL,$SchoolId)
                                        ->where(cn::EXAM_TABLE_STATUS_COLS,'<>','inactive');
                                    }])
                                    ->whereIn(cn::EXAM_TABLE_ID_COLS,$GetAssignedExamIds)
                                    //->where(cn::EXAM_TABLE_STATUS_COLS,'publish')
                                    ->where(cn::EXAM_TYPE_COLS,'<>',1)
                                    ->where(cn::EXAM_TABLE_STATUS_COLS,'<>','inactive')
                                    ->sortable()
                                    ->orderBy(cn::EXAM_TABLE_ID_COLS,'DESC')
                                    ->paginate($items);
                    if(isset($request->filter)){
                        $Query = $this->Exam->select('*')
                                ->with(['ExamSchoolMapping' => function($q) use($SchoolId,$request){
                                    $q->where(cn::EXAM_SCHOOL_MAPPING_SCHOOL_ID_COL,$SchoolId);
                                    if(isset($request->status) && !empty($request->status)){
                                        $q->where(cn::EXAM_SCHOOL_MAPPING_STATUS_COL,$request->status);
                                    }else{
                                        $q->where(cn::EXAM_SCHOOL_MAPPING_STATUS_COL,'<>','inactive');
                                    }
                                }])
                                ->whereIn(cn::EXAM_TABLE_ID_COLS,$GetAssignedExamIds)
                                ->where(cn::EXAM_TYPE_COLS,'<>',1);
                        
                        //search by Exam Type
                        if(isset($request->test_type) && !empty($request->test_type)){
                            //Check Exam type Not Equal to All
                            if($request->test_type != 4){
                                $Query->where(cn::EXAM_TYPE_COLS,$request->test_type);
                            }
                        }

                        // //search by Exam title
                        if(isset($request->title) && !empty($request->title)){
                            $Query->where(cn::EXAM_TABLE_TITLE_COLS,'like','%'.$request->title.'%')->orWhere(cn::EXAM_REFERENCE_NO_COL,'like','%'.$request->title.'%');
                        }

                        // //From Date                 
                        if(isset($request->from_date) && !empty($request->from_date)){
                            $from_date = $this->DateConvertToYMD($request->from_date);
                            $Query->whereRaw(cn::EXAM_TABLE_FROM_DATE_COLS." >= '$from_date'");
                        }
    
                        // //To Date
                        if(isset($request->to_date) && !empty($request->to_date)){
                            $to_date = $this->DateConvertToYMD($request->to_date);
                            $Query->whereRaw(cn::EXAM_TABLE_TO_DATE_COLS." <= '$to_date'");
                        }
                        $examList = $Query->sortable()->orderBy(cn::EXAM_TABLE_ID_COLS,'DESC')->paginate($items);
                    }
                }
                return view('backend/question_generator/school/question_wizard_list',compact('examList','items','examTypes','statusLists','difficultyLevels'));
                break;
            
            case 2 : //  = Teacher Role
                if($this->isTeacherLogin()){
                    $SchoolId = Auth::user()->{cn::USERS_SCHOOL_ID_COL};
                    $GetAssignedExamIds = [];
                    $GetAssignedStudentSelfLearningExamIds = [];
                    // Get Teacher assigned grades and class
                    $TeacherGradeClass = $this->TeacherGradesClassService->getTeacherAssignedGradesClass(Auth::user()->{cn::USERS_SCHOOL_ID_COL}, Auth::user()->{cn::USERS_ID_COL});
                    if(isset($TeacherGradeClass['grades']) && !empty($TeacherGradeClass['grades']) && isset($TeacherGradeClass['class']) && !empty($TeacherGradeClass['class'])){
                        // Get Teacher assigned exam ids
                        $GetAssignedExamIds = $this->TeacherGradesClassService->getAssignedTeachersExamsIds(Auth::user()->{cn::USERS_SCHOOL_ID_COL}, $TeacherGradeClass['class']);
                        $GetAssignedStudentSelfLearningExamIds = $this->TeacherGradesClassService->GetStudentSelfLearningTestIds(Auth::user()->{cn::USERS_SCHOOL_ID_COL}, $TeacherGradeClass['class']);                        
                    }
                    $examList = $this->Exam->with(['ExamSchoolMapping' => function($q) use($SchoolId){
                                        $q->where(cn::EXAM_SCHOOL_MAPPING_SCHOOL_ID_COL,$SchoolId)
                                        ->where(cn::EXAM_TABLE_STATUS_COLS,'<>','inactive');
                                    }])
                                    ->whereIn(cn::EXAM_TABLE_ID_COLS,$GetAssignedExamIds)
                                    ->where(cn::EXAM_TYPE_COLS,'<>',1)
                                    ->where(cn::EXAM_TABLE_STATUS_COLS,'<>','inactive')
                                    ->sortable()
                                    ->orderBy(cn::EXAM_TABLE_ID_COLS,'DESC')
                                    ->paginate($items);
                    if(isset($request->filter)){
                        $Query = $this->Exam->select('*')
                                ->with(['ExamSchoolMapping' => function($q) use($SchoolId,$request){
                                    $q->where(cn::EXAM_SCHOOL_MAPPING_SCHOOL_ID_COL,$SchoolId);
                                    if(isset($request->status) && !empty($request->status)){
                                        $q->where(cn::EXAM_SCHOOL_MAPPING_STATUS_COL,$request->status);
                                    }else{
                                        $q->where(cn::EXAM_SCHOOL_MAPPING_STATUS_COL,'<>','inactive');
                                    }
                                }])
                                ->whereIn(cn::EXAM_TABLE_ID_COLS,$GetAssignedExamIds)
                                ->where(cn::EXAM_TYPE_COLS,'<>',1);
                        //search by Exam Type
                        if(isset($request->test_type) && !empty($request->test_type)){
                            //Check Exam type Not Equal to All
                            if($request->test_type != 4){
                                $Query->where(cn::EXAM_TYPE_COLS,$request->test_type);
                            }
                        }
    
                        //search by Exam title
                        if(isset($request->title) && !empty($request->title)){
                            $Query->where(cn::EXAM_TABLE_TITLE_COLS,'like','%'.$request->title.'%')->orWhere(cn::EXAM_REFERENCE_NO_COL,'like','%'.$request->title.'%');
                        }
    
                        //From Date
                        if(isset($request->from_date) && !empty($request->from_date)){
                            $from_date = $this->DateConvertToYMD($request->from_date);
                            $Query->whereRaw(cn::EXAM_TABLE_FROM_DATE_COLS." >= '$from_date'");
                        }
    
                        //To Date
                        if(isset($request->to_date) && !empty($request->to_date)){
                            $to_date = $this->DateConvertToYMD($request->to_date);
                            $Query->whereRaw(cn::EXAM_TABLE_TO_DATE_COLS." <= '$to_date'");
                        }
                        $examList = $Query->sortable()->orderBy(cn::EXAM_TABLE_ID_COLS,'DESC')->paginate($items);
                    }
                }
                
                return view('backend/question_generator/teachers/question_wizard_list',compact('examList','items','examTypes','statusLists','difficultyLevels'));
                break;
            default:
        }
    }

    /**
     * USE : Update Status for test
     */
    public function ExamStatusUpdate(Request $request){
        $result ='';
        if(empty($request->exam_id)){
            return $this->sendError(__('validation.exam_id_not_found'), 422);
        }
        $ExamData = $this->Exam->find($request->exam_id);
        if(empty($ExamData)){
            return $this->sendError(__('validation.exam_data_not_found'), 422);
        }
        switch(Auth::user()->{cn::USERS_ROLE_ID_COL}){
            case 1: // 1 = Admin
                $result = $this->Exam->find($request->exam_id)->update([cn::EXAM_TABLE_STATUS_COLS => $request->status]);
                if($result){
                    if(!empty($ExamData)){
                        if($ExamData->use_of_mode == 2){
                            $this->Exam->where('parent_exam_id',$request->exam_id)->update([cn::EXAM_TABLE_STATUS_COLS => $request->status]);
                        }
                    }
                    return $this->sendResponse($result, __('languages.status_updated_successfully'));
                }
                break;
            
            // Update status by school admin
            case 5: case 7: case 2 : // 5 = School Admin  & 7 = Principal & 5 = Teacher
                if($this->ExamGradeClassMappingModel->where([
                    cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_SCHOOL_ID_COL => Auth::user()->{cn::USERS_SCHOOL_ID_COL},
                    cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_EXAM_ID_COL => $request->exam_id
                ])->exists()){
                    $Update = $this->ExamSchoolMapping->where([
                        cn::EXAM_SCHOOL_MAPPING_EXAM_ID_COL => $request->exam_id,
                        cn::EXAM_SCHOOL_MAPPING_SCHOOL_ID_COL => Auth::user()->{cn::USERS_SCHOOL_ID_COL}
                        ])->update([cn::EXAM_SCHOOL_MAPPING_STATUS_COL => $request->status]);
                        if($Update){
                            $this->ExamGradeClassMappingModel->where(cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_SCHOOL_ID_COL,Auth::user()->{cn::USERS_SCHOOL_ID_COL})
                            ->where(cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_EXAM_ID_COL,$request->exam_id)
                            ->Update([
                                cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_STATUS_COL => $request->status
                            ]);

                            // Update Exam Table
                            if($ExamData->created_by_user == 'school_admin' || $ExamData->created_by_user == 'principal' || $ExamData->created_by_user == 'teacher'){
                                $this->Exam->find($request->exam_id)->Update(['status' => $request->status]);
                            }
                        }
                    if($Update){
                        return $this->sendResponse($result, __('languages.status_updated_successfully'));
                    }else{
                        return $this->sendError(__('languages.problem_was_occur_please_try_again'), 422);
                    }
                }else{
                    return $this->sendError(__('languages.please_assigned_to_grade_and_class_first'), 422);
                }
                break;
        }
    }

    /**
     * USE : Super admin can add more schools to assign test
     */
    public function addMoreSchools(Request $request){
        $newSchoolIds= '';
        $temp = 1;
        $examData = Exam::find($request->examId);
        if(isset($examData) && !empty($examData)){
            if($examData->use_of_mode == 1){
                $oldSchoolIds = $examData->school_id;
                if(!empty( $oldSchoolIds)){
                    $newSchoolIds = $oldSchoolIds.','.implode(',',$request->school);
                }else{
                    $newSchoolIds = implode(',',$request->school);
                }
                $updateData = Exam::find($request->examId)->update([
                                cn::EXAM_TABLE_SCHOOL_COLS => $newSchoolIds
                            ]);
                if($updateData){
                    $this->CreateExamSchoolMapping($request->school,$request->examId, $request);
                    return redirect('question-wizard')->with('success_msg', __('languages.school_added_successfully'));
                }else{
                    return redirect('question-wizard')->with('error_msg', __('languages.problem_was_occur_please_try_again'));
                }
            }

            if($examData->use_of_mode == 2){
                $oldSchoolIds = $examData->school_id;
                if(!empty( $oldSchoolIds)){
                    $newSchoolIds = $oldSchoolIds.','.implode(',',$request->school);
                }else{
                    $newSchoolIds = implode(',',$request->school);
                }
                $updateData = Exam::find($request->examId)->update([
                    cn::EXAM_TABLE_SCHOOL_COLS => $newSchoolIds
                ]);
                
                if($updateData){
                    foreach($request->school as $schoolId){
                        $userId = User::where([cn::USERS_ROLE_ID_COL=>cn::SCHOOL_ROLE_ID,cn::USERS_SCHOOL_ID_COL =>$schoolId])->first();
                        $ExamData = [
                            cn::EXAM_TABLE_USE_OF_MODE_COLS                  => 2,
                            cn::EXAM_TABLE_PARENT_EXAM_ID_COLS               => $examData->id,
                            cn::EXAM_TYPE_COLS                               => $examData->exam_type,
                            cn::EXAM_TABLE_TITLE_COLS                        => $examData->title,
                            cn::EXAM_TABLE_SCHOOL_COLS                       => $schoolId,
                            cn::EXAM_TABLE_FROM_DATE_COLS                    => $examData->from_date,
                            cn::EXAM_TABLE_TO_DATE_COLS                      => $examData->to_date,
                            cn::EXAM_TABLE_START_TIME_COL                    => $examData->start_time,
                            cn::EXAM_TABLE_END_TIME_COL                      => $examData->end_time,
                            cn::EXAM_TABLE_REPORT_TYPE_COLS                  => $examData->report_type,
                            cn::EXAM_TABLE_RESULT_DATE_COLS                  => $examData->result_date,
                            cn::EXAM_TABLE_PUBLISH_DATE_COL                  => $examData->publish_date,
                            cn::EXAM_TABLE_TIME_DURATIONS_COLS               => $examData->time_duration,
                            cn::EXAM_TABLE_DESCRIPTION_COLS                  => $examData->description,
                            cn::EXAM_TABLE_QUESTION_IDS_COL                  => $examData->question_ids,
                            cn::EXAM_TABLE_STUDENT_IDS_COL                   => $examData->student_ids,
                            cn::EXAM_TABLE_PEER_GROUP_IDS_COL                => $examData->peer_group_ids,
                            cn::EXAM_TABLE_GROUP_IDS_COL                     => $examData->group_ids,
                            cn::EXAM_TABLE_IS_GROUP_TEST_COL                 => $examData->is_group_test,
                            cn::EXAM_TABLE_STATUS_COLS                       => $examData->status,
                            cn::EXAM_TABLE_RESULT_DECLARE_COL                => $examData->result_declare,
                            cn::EXAM_TABLE_IS_UNLIMITED                      => $examData->is_unlimited,
                            cn::EXAM_TABLE_TEMPLATE_ID                       => $examData->template_id,
                            cn::EXAM_TABLE_SELF_LEARNING_TEST_TYPE_COL       => $examData->self_learning_test_type,
                            cn::EXAM_TABLE_NO_OF_TRIALS_PER_QUESTIONS_COL    => $examData->no_of_trials_per_question,
                            cn::EXAM_TABLE_DIFFICULTY_MODE_COL               => $examData->difficulty_mode,
                            cn::EXAM_TABLE_DIFFICULTY_LEVELS_COL             => $examData->difficulty_levels,
                            cn::EXAM_TABLE_IS_DISPLAY_HINTS_COL              => $examData->display_hints,
                            cn::EXAM_TABLE_IS_DISPLAY_FULL_SOLUTIONS_COL     => $examData->display_full_solution,
                            cn::EXAM_TABLE_IS_DISPLAY_PER_ANSWER_HINTS_COL   => $examData->display_pr_answer_hints,
                            cn::EXAM_TABLE_IS_RANDOMIZED_ANSWERS_COL         => $examData->randomize_answer,
                            cn::EXAM_TABLE_IS_RANDOMIZED_ORDER_COL           => $examData->randomize_order,
                            cn::EXAM_TABLE_LEARNING_OBJECTIVES_CONFIGURATIONS_COL => $examData->learning_objectives_configuration,
                            cn::EXAM_TABLE_CREATED_BY_COL                    =>  $userId->id,
                        ];
                        $exams = Exam::create($ExamData);
                        $this->CreateExamSchoolMapping(array($schoolId),$exams->id, $request);
                        if(!$exams){
                            $temp = 0;
                            break;
                        }
                    }
                    if($temp){
                        return redirect('question-wizard')->with('success_msg', __('languages.school_added_successfully'));
                    }else{
                        return redirect('question-wizard')->with('error_msg', __('languages.problem_was_occur_please_try_again'));
                    }
                }else{
                    return redirect('question-wizard')->with('error_msg', __('languages.problem_was_occur_please_try_again'));
                }
            }
        }
    }

    /**
     * USE : Display Exam Configuration Preview
     */
    public function examConfigurationPreview($id){
        $exam = Exam::find($id);
        if(!empty($exam)){
            $GradeClassData = array();
            $StudentList = array();
            $examCreditPointRulesData = array();
            $examGradeIds = $exam->examSchoolGradeClass()->whereNotNull(cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_GRADE_ID_COL)->pluck(cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_GRADE_ID_COL)->toArray();
            $examClassIds = $exam->examSchoolGradeClass()->pluck(cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_CLASS_ID_COL)->toArray();
            
            if(isset($examGradeIds) && !empty($examGradeIds)){
                $examStartTime = json_encode($exam->examSchoolGradeClass()->pluck(cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_START_TIME_COL,cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_CLASS_ID_COL)->toArray());
                $examEndTime = json_encode($exam->examSchoolGradeClass()->pluck(cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_END_TIME_COL,cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_CLASS_ID_COL)->toArray());
                $examStartDate = json_encode($exam->examSchoolGradeClass()->pluck(cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_START_DATE_COL,cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_CLASS_ID_COL)->toArray());
                $examEndDate = json_encode($exam->examSchoolGradeClass()->pluck(cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_END_DATE_COL,cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_CLASS_ID_COL)->toArray());
            }else{
                $examStartTime = json_encode($exam->examSchoolGradeClass()->pluck(cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_START_TIME_COL,cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_PEER_GROUP_ID_COL)->toArray());
                $examEndTime = json_encode($exam->examSchoolGradeClass()->pluck(cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_END_TIME_COL,cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_PEER_GROUP_ID_COL)->toArray());
                $examStartDate = json_encode($exam->examSchoolGradeClass()->pluck(cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_START_DATE_COL,cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_PEER_GROUP_ID_COL)->toArray());
                $examEndDate = json_encode($exam->examSchoolGradeClass()->pluck(cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_END_DATE_COL,cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_PEER_GROUP_ID_COL)->toArray());
            }
            $difficultyLevels = PreConfigurationDiffiltyLevel::all();
            $strandsList = [];
            $LearningUnits = [];
            $LearningObjectives = [];
            $PeerGroupList = [];
            $schoolList = [];
            // Get the school list
            $schoolList = School::where(cn::SCHOOL_SCHOOL_STATUS,'active')->get();
            $RequiredQuestionPerSkill = [];
            $RequiredQuestionPerSkill = [
                'minimum_question_per_skill' => $this->MinimumQuestionPerSkill,
                'maximum_question_per_skill' => $this->MaximumQuestionPerObjectives
            ];
            $Schools = School::all();
            
            $GradeClassData=array();
            $StudentList = array();
            // Get Time slot
            $timeSlots = $this->getTimeSlot();
            $SelectedStrands = array();
            $SelectedLearningUnit = array();
            $strandsList = Strands::all();
            $learningObjectivesConfiguration = array();
            if(isset($exam->learning_objectives_configuration) && !empty($exam->learning_objectives_configuration)){
                $learningObjectivesConfiguration = json_decode($exam->learning_objectives_configuration,true);
                $SelectedLearningUnit = array_keys($learningObjectivesConfiguration);
                $learning_objectives_configuration = array_keys($learningObjectivesConfiguration);
                $LearningUnitsData = LearningsUnits::whereIn(cn::LEARNING_UNITS_ID_COL,$learning_objectives_configuration)->pluck(cn::LEARNING_UNITS_STRANDID_COL)->toArray();
                $SelectedStrands = array_unique($LearningUnitsData);
            }
            $questionDataArray = array();
            if(isset($exam->question_ids) && !empty($exam->question_ids)){
                $questionDataArray = Question::with(['answers','PreConfigurationDifficultyLevel','objectiveMapping'])->whereIn(cn::QUESTION_TABLE_ID_COL,explode(',',$exam->question_ids))->get()->toArray();
            }
            if(!empty($SelectedStrands)){
                $LearningUnits = LearningsUnits::whereIn(cn::LEARNING_UNITS_STRANDID_COL, $SelectedStrands)->get();
                if(!empty($LearningUnits)){
                    //$LearningObjectives = LearningsObjectives::whereIn(cn::LEARNING_OBJECTIVES_LEARNING_UNITID_COL, $LearningUnits->pluck(cn::LEARNING_OBJECTIVES_ID_COL))->get();
                    // $LearningObjectives = LearningsObjectives::whereIn(cn::LEARNING_OBJECTIVES_LEARNING_UNITID_COL, $SelectedLearningUnit)->get();
                    $LearningObjectives = LearningsObjectives::IsAvailableQuestion()->whereIn(cn::LEARNING_OBJECTIVES_LEARNING_UNITID_COL, $SelectedLearningUnit)->get();
                }
            }
            $studentGradeData = array();
            $studentClassData = array();
            if(isset($exam->student_ids) && !empty($exam->student_ids)){
                $studentData = User::whereIn(cn::USERS_ID_COL,explode(',', $exam->student_ids))
                                ->where([
                                    cn::USERS_ROLE_ID_COL => CN::STUDENT_ROLE_ID,
                                    cn::USERS_STATUS_COL => 'active'
                                ]);
                $studentGradeData = $studentData->pluck(cn::USERS_GRADE_ID_COL)->toArray();
                $studentClassData = $studentData->pluck(cn::USERS_CLASS_ID_COL)->toArray();
            }
            if($exam->school_id!=""){
                $school_id = explode(',', $exam->school_id);
                $schoolId = $school_id[0];
                $examCreditPointRulesData = $exam->examCreditPointRules()->where(cn::EXAM_CREDIT_POINT_RULES_MAPPING_SCHOOL_ID_COL,$schoolId)->pluck(cn::EXAM_CREDIT_POINT_RULES_MAPPING_RULES_VALUE_COL,cn::EXAM_CREDIT_POINT_RULES_MAPPING_CREDIT_POINT_RULES_COL)->toArray();
            }
            if($this->isTeacherLogin()){
                $schoolId = $this->isTeacherLogin();
                $TeacherGradeClassData = $this->TeacherGradesClassService->getTeacherAssignedGradesClass(Auth::user()->{cn::USERS_SCHOOL_ID_COL}, Auth::user()->{cn::USERS_ID_COL});
                if(!empty($TeacherGradeClassData)){
                    $gradeClassId = $TeacherGradeClassData['grades'] ?? [];
                    $GradeClassData = Grades::with(['classes' => fn($query) => $query->whereIn(cn::GRADE_CLASS_MAPPING_ID_COL,$TeacherGradeClassData['class'])
                                        ->where([cn::GRADE_CLASS_MAPPING_SCHOOL_ID_COL => $this->isTeacherLogin()])])
                                        ->whereIn(cn::GRADES_ID_COL,$TeacherGradeClassData['grades'])
                                        ->get();
                }

                // get student list
                $StudentList = User::whereIn(cn::USERS_GRADE_ID_COL,$TeacherGradeClassData['grades'])
                            ->whereIn(cn::USERS_CLASS_ID_COL,$TeacherGradeClassData['class'])
                            ->where([cn::USERS_ROLE_ID_COL => 3,cn::USERS_STATUS_COL => 'active'])
                            ->get();
                // Get Peer Group List
                $PeerGroupList = PeerGroup::where([
                                    cn::PEER_GROUP_CREATED_BY_USER_ID_COL => Auth()->user()->id,
                                    cn::PEER_GROUP_STATUS_COL => '1'
                                ])->get();

                $examCreditPointRulesData=$exam->examCreditPointRules()->where(cn::EXAM_CREDIT_POINT_RULES_MAPPING_SCHOOL_ID_COL,$schoolId)->pluck(cn::EXAM_CREDIT_POINT_RULES_MAPPING_RULES_VALUE_COL,cn::EXAM_CREDIT_POINT_RULES_MAPPING_CREDIT_POINT_RULES_COL)->toArray();
            }
            if($this->isSchoolLogin() || $this->isPrincipalLogin()){    
                if($this->isSchoolLogin()){
                    $schoolId = $this->isSchoolLogin();
                }
                if($this->isPrincipalLogin()){
                    $schoolId = $this->isPrincipalLogin();
                }

                $GradeMapping = GradeSchoolMappings::with('grades')->where(cn::GRADES_MAPPING_SCHOOL_ID_COL,$schoolId)->get()->pluck(cn::GRADES_MAPPING_GRADE_ID_COL);
                $gradeClass = GradeClassMapping::where(cn::GRADE_CLASS_MAPPING_SCHOOL_ID_COL,$schoolId)->whereIn(cn::GRADE_CLASS_MAPPING_GRADE_ID_COL,$GradeMapping)->pluck(cn::GRADE_CLASS_MAPPING_ID_COL)->toArray();
                if(isset($gradeClass) && !empty($gradeClass)){
                    $gradeClass = implode(',', $gradeClass);
                    $gradeClassId = explode(',',$gradeClass);
                }
                $GradeClassData = Grades::with(['classes' => fn($query) => $query->whereIn(cn::GRADE_CLASS_MAPPING_ID_COL,$gradeClassId)->where([cn::GRADE_CLASS_MAPPING_SCHOOL_ID_COL => $schoolId])])->whereIn(cn::GRADES_ID_COL,$GradeMapping)->get();
                
                // get student list
                $StudentList = User::where(cn::USERS_SCHOOL_ID_COL,Auth::user()->{cn::USERS_SCHOOL_ID_COL})->where(cn::USERS_ROLE_ID_COL,'=',cn::STUDENT_ROLE_ID)->with('grades')->get();

                // Get Peer Group List
                $PeerGroupList = PeerGroup::where([cn::PEER_GROUP_SCHOOL_ID_COL => $schoolId, cn::PEER_GROUP_STATUS_COL => '1'])->get();
                $examCreditPointRulesData = $exam->examCreditPointRules()->where(cn::EXAM_CREDIT_POINT_RULES_MAPPING_SCHOOL_ID_COL,$schoolId)->pluck(cn::EXAM_CREDIT_POINT_RULES_MAPPING_RULES_VALUE_COL,cn::EXAM_CREDIT_POINT_RULES_MAPPING_CREDIT_POINT_RULES_COL)->toArray();
            }

            $questionListHtml = '';
            
            $questionListHtml = '';
            $difficultyLevels = PreConfigurationDiffiltyLevel::all();
            $question_list = Question::with(['answers','PreConfigurationDifficultyLevel','objectiveMapping'])
                                ->whereIn(cn::QUESTION_TABLE_ID_COL,explode(',',$exam->question_ids))
                                ->get();
            $questionListHtml = (string)View::make('backend.question_generator.school.question_list_preview',compact('question_list','difficultyLevels'));
            return view('backend.question_generator.preview_question_configuration',compact('schoolList','difficultyLevels','strandsList','LearningUnits',
                        'LearningObjectives','timeSlots','RequiredQuestionPerSkill','exam','learningObjectivesConfiguration','SelectedStrands','SelectedLearningUnit',
                        'questionDataArray','GradeClassData','StudentList','PeerGroupList','studentGradeData','studentClassData','questionListHtml','examGradeIds',
                        'examClassIds','examStartTime','examEndTime','examStartDate','examEndDate','examCreditPointRulesData'));
        }else{
            return back()->with('error_msg', __('languages.data_not_found'));
        } 
    }

    /**
     * USE : Get Late commerce student list
     */
    public function GetLateCommerceStudentList(Request $request){
        $peerGroupIds = array();
        $studentIds = array();
        $type = '';
        $getGroupData = '';
        $getStudentsData = '';
        $schoolId = Auth::user()->{cn::USERS_SCHOOL_ID_COL};
        $examId = $request->examid;
        $examData = Exam::find($examId);

        // Get Time slot
        $timeSlots = $this->getTimeSlot();
        if($this->isSchoolLogin() || $this->isPrincipalLogin()){
            if(!empty($examData->{cn::EXAM_TABLE_PEER_GROUP_IDS_COL})){
                $peerGroupIds = explode(',',$examData->{cn::EXAM_TABLE_PEER_GROUP_IDS_COL});
                $getGroupData = PeerGroup::where(cn::PEER_GROUP_SCHOOL_ID_COL,$schoolId)->whereNotIn(cn::PEER_GROUP_ID_COL,$peerGroupIds)->get();
                if($getGroupData->isNotEmpty()){
                    $type = 'peergroup';
                    $html = (string)View::make('backend.question_generator.assign_new_student_group',compact('getGroupData','type','examId','examData','timeSlots'));                    
                    return $this->sendResponse($html);
                }else{
                    return $this->sendError(__('languages.not_any_remaining_groups'), 422);
                }
            }else{
                $oldAvailableClassIds=array();
                $oldRemoveAvailableClassIds=array();
                $oldStudentList=array();
                $StudentList=array();
                $AvailableGradesIds = $this->ExamGradeClassMappingModel->where(cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_SCHOOL_ID_COL,$schoolId)
                                            ->where(cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_EXAM_ID_COL,$examId)
                                            ->where(cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_STATUS_COL,'publish')
                                            ->pluck(cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_GRADE_ID_COL)->toArray();

                $AvailableGradesIds=array_values(array_filter(array_unique($AvailableGradesIds)));
                foreach ($AvailableGradesIds as $gradesIds) {
                    $AvailableClassIds = $this->ExamGradeClassMappingModel->where(cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_SCHOOL_ID_COL,$schoolId)
                                                ->where(cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_EXAM_ID_COL,$examId)
                                                ->where(cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_GRADE_ID_COL,$gradesIds)
                                                ->where(cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_STATUS_COL,'publish')
                                                ->pluck(cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_CLASS_ID_COL)->toArray();
                    foreach ($AvailableClassIds as $classId) {
                        $AvailableStudentIds = $this->ExamGradeClassMappingModel->where(cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_SCHOOL_ID_COL,$schoolId)
                                                ->where(cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_EXAM_ID_COL,$examId)
                                                ->where(cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_GRADE_ID_COL,$gradesIds)
                                                ->where(cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_CLASS_ID_COL,$classId)
                                                ->where(cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_STATUS_COL,'publish')
                                                ->pluck(cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_STUDENT_IDS_COL)->toArray();
                        if(isset($AvailableStudentIds) && !empty($AvailableStudentIds)){
                            $StudentIds=array_values(array_filter(array_unique(array_merge(explode(',',$AvailableStudentIds[0])))));
                            $StudentIdsSize=sizeof($StudentIds);
                            // get student list
                            $getStudentList = User::where(cn::USERS_GRADE_ID_COL,$gradesIds)
                                ->where(cn::USERS_CLASS_ID_COL,$classId)
                                ->where(cn::USERS_SCHOOL_ID_COL,$schoolId)
                                ->where([cn::USERS_ROLE_ID_COL => 3,cn::USERS_STATUS_COL => 'active'])
                                ->count();
                            if($StudentIdsSize != $getStudentList){
                                $oldStudentList = array_values(array_filter(array_unique(array_merge($oldStudentList,$StudentIds))));
                                $oldAvailableClassIds[] = $classId;
                            }else{
                                $oldRemoveAvailableClassIds[] = $classId;
                            }
                        }else{
                            $oldAvailableClassIds[] = $classId;
                        }
                    }
                }
                $GradeMapping = GradeSchoolMappings::with('grades')->where(cn::GRADES_MAPPING_SCHOOL_ID_COL,$schoolId)->get()->pluck(cn::GRADES_MAPPING_GRADE_ID_COL);
                $gradeClass = GradeClassMapping::where(cn::GRADE_CLASS_MAPPING_SCHOOL_ID_COL,$schoolId)->whereIn(cn::GRADE_CLASS_MAPPING_GRADE_ID_COL,$GradeMapping)->pluck(cn::GRADE_CLASS_MAPPING_ID_COL)->toArray();
                $GradeClassData=array();
                if(isset($oldRemoveAvailableClassIds) && !empty($oldRemoveAvailableClassIds)){
                    $gradeClass = array_values(array_diff($gradeClass,$oldRemoveAvailableClassIds));
                }
                if(isset($oldAvailableClassIds) && !empty($oldAvailableClassIds)){
                    $gradeClass = array_merge($gradeClass,$oldAvailableClassIds);
                }
                if(isset($gradeClass) && !empty($gradeClass)){
                    $gradeClass = implode(',', $gradeClass);
                    $gradeClassId = explode(',',$gradeClass);
                }
                if(isset($gradeClassId) && !empty($gradeClassId)){
                    $GradeClassData = Grades::with(['classes' => fn($query) => $query->whereIn(cn::GRADE_CLASS_MAPPING_ID_COL,$gradeClassId)->where([cn::GRADE_CLASS_MAPPING_SCHOOL_ID_COL => $schoolId])])->whereIn(cn::GRADES_ID_COL,$GradeMapping)->get();
                    // get student list
                    $StudentList = User::whereIn(cn::USERS_GRADE_ID_COL,$GradeMapping)
                            ->whereIn(cn::USERS_CLASS_ID_COL,$gradeClassId)
                            ->where([cn::USERS_ROLE_ID_COL => 3,cn::USERS_STATUS_COL => 'active'])
                            ->get();
                    if(isset($oldStudentList) && !empty($oldStudentList)){
                        // get student list
                        $StudentList = User::whereIn(cn::USERS_GRADE_ID_COL,$GradeMapping)
                                ->whereIn(cn::USERS_CLASS_ID_COL,$gradeClassId)
                                ->whereNotIn(cn::USERS_ID_COL,$oldStudentList)
                                ->where([cn::USERS_ROLE_ID_COL => 3,cn::USERS_STATUS_COL => 'active'])
                                ->get();
                    }                    
                }
                if(!empty($examData->{cn::EXAM_TABLE_STUDENT_IDS_COL})){
                    $studentIds = explode(',',$examData->{cn::EXAM_TABLE_STUDENT_IDS_COL});
                    $getStudentsData = User::where([cn::USERS_ROLE_ID_COL => cn::STUDENT_ROLE_ID,cn::USERS_SCHOOL_ID_COL => $schoolId])->get();
                    if(!empty($GradeClassData)){
                        $type = 'grade_class';
                        $html = (string)View::make('backend.question_generator.assign_new_student_group',compact('getGroupData','GradeClassData','StudentList','oldStudentList','type','examId','examData','timeSlots'));                    
                    return $this->sendResponse($html);
                    }else{
                        return $this->SendError(__('languages.all_students_already_assign'),422);
                    }
                }
            }
        }
        if($this->isTeacherLogin()){
            if(!empty($examData->{cn::EXAM_TABLE_PEER_GROUP_IDS_COL})){
                $peerGroupIds = explode(',',$examData->{cn::EXAM_TABLE_PEER_GROUP_IDS_COL});
                $getGroupData = PeerGroup::where(cn::PEER_GROUP_SCHOOL_ID_COL,$schoolId)->where(cn::PEER_GROUP_CREATED_BY_USER_ID_COL,Auth::user()->{cn::USERS_ID_COL})->whereNotIn(cn::PEER_GROUP_ID_COL,$peerGroupIds)->get();
                if($getGroupData->isNotEmpty()){
                    $type = 'peergroup';
                    $html = (string)View::make('backend.question_generator.assign_new_student_group',compact('getGroupData','type','examId','examData','timeSlots'));                    
                    return $this->sendResponse($html);
                }else{
                    return $this->sendError(__('languages.not_any_remaining_groups'), 422);
                }
            }else{
                $currentLoggedSchoolId = $this->isTeacherLogin();
                $TeacherGradeClass = $this->TeacherGradesClassService->getTeacherAssignedGradesClass($currentLoggedSchoolId, Auth::user()->{cn::USERS_ID_COL});
                if(!empty($TeacherGradeClass)){
                    $oldAvailableClassIds = array();
                    $oldRemoveAvailableClassIds = array();
                    $oldStudentList = array();
                    $StudentList = array();
                    $AvailableGradesIds = $this->ExamGradeClassMappingModel->where(cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_SCHOOL_ID_COL,$currentLoggedSchoolId)
                                            ->whereIn(cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_GRADE_ID_COL,$TeacherGradeClass['grades'])
                                            ->where(cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_EXAM_ID_COL,$examId)
                                            ->where(cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_STATUS_COL,'publish')
                                            ->pluck(cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_GRADE_ID_COL)->toArray();
                    $AvailableGradesIds = array_values(array_filter(array_unique($AvailableGradesIds)));
                    foreach ($AvailableGradesIds as $gradesIds) {
                        $AvailableClassIds = $this->ExamGradeClassMappingModel->where(cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_SCHOOL_ID_COL,$currentLoggedSchoolId)
                                            ->whereIn(cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_CLASS_ID_COL,$TeacherGradeClass['class'])
                                            ->where(cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_EXAM_ID_COL,$examId)
                                            ->where(cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_STATUS_COL,'publish')
                                            ->pluck(cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_CLASS_ID_COL)->toArray();
                        foreach ($AvailableClassIds as $classId) {
                            $AvailableStudentIds = $this->ExamGradeClassMappingModel->where(cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_SCHOOL_ID_COL,$schoolId)
                                            ->where(cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_EXAM_ID_COL,$examId)
                                            ->where(cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_GRADE_ID_COL,$gradesIds)
                                            ->where(cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_CLASS_ID_COL,$classId)
                                            ->where(cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_STATUS_COL,'publish')
                                            ->pluck(cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_STUDENT_IDS_COL)->toArray();
                            if(isset($AvailableStudentIds) && !empty($AvailableStudentIds)){
                                $StudentIds = array_values(array_filter(array_unique(array_merge(explode(',',$AvailableStudentIds[0])))));
                                $StudentIdsSize = sizeof($StudentIds);
                                // get student list
                                $getStudentList = User::where(cn::USERS_GRADE_ID_COL,$gradesIds)
                                    ->where(cn::USERS_CLASS_ID_COL,$classId)
                                    ->where(cn::USERS_SCHOOL_ID_COL,$schoolId)
                                    ->where([cn::USERS_ROLE_ID_COL => 3,cn::USERS_STATUS_COL => 'active'])
                                    ->count();
                                if($StudentIdsSize != $getStudentList){
                                    $oldStudentList=array_values(array_filter(array_unique(array_merge($oldStudentList,$StudentIds))));
                                    $oldAvailableClassIds[]=$classId;
                                }else{
                                    $oldRemoveAvailableClassIds[]=$classId;
                                }
                            }else{
                                $oldAvailableClassIds[]=$classId;
                            }
                        }
                    }

                    $GradeMapping = GradeSchoolMappings::with('grades')
                                        ->whereIn(cn::GRADES_MAPPING_GRADE_ID_COL,$TeacherGradeClass['grades'])
                                        ->where(cn::GRADES_MAPPING_SCHOOL_ID_COL,$currentLoggedSchoolId)
                                        ->get()
                                        ->pluck(cn::GRADES_MAPPING_GRADE_ID_COL);
                    $gradeClass = GradeClassMapping::where(cn::GRADE_CLASS_MAPPING_SCHOOL_ID_COL,$currentLoggedSchoolId)
                                    ->whereIn(cn::GRADE_CLASS_MAPPING_ID_COL,$TeacherGradeClass['class'])
                                    ->whereIn(cn::GRADE_CLASS_MAPPING_GRADE_ID_COL,$GradeMapping)
                                    ->pluck(cn::GRADE_CLASS_MAPPING_ID_COL)->toArray();
                    $GradeClassData = array();
                    if(isset($oldRemoveAvailableClassIds) && !empty($oldRemoveAvailableClassIds)){
                        $gradeClass = array_values(array_diff($gradeClass,$oldRemoveAvailableClassIds));
                    }
                    if(isset($oldAvailableClassIds) && !empty($oldAvailableClassIds)){
                        $gradeClass = array_merge($gradeClass,$oldAvailableClassIds);
                    }
                    if(isset($gradeClass) && !empty($gradeClass)){
                        $gradeClass = implode(',', $gradeClass);
                        $gradeClassId = explode(',',$gradeClass);
                    }
                    if(isset($gradeClassId) && !empty($gradeClassId)){
                        $GradeClassData = Grades::with(['classes' => fn($query) => $query->whereIn(cn::GRADE_CLASS_MAPPING_ID_COL,$gradeClassId)->where([cn::GRADE_CLASS_MAPPING_SCHOOL_ID_COL => $currentLoggedSchoolId])])->whereIn(cn::GRADES_ID_COL,$GradeMapping)->get();
                        // get student list
                        $StudentList = User::whereIn(cn::USERS_GRADE_ID_COL,$GradeMapping)
                                ->whereIn(cn::USERS_CLASS_ID_COL,$gradeClassId)
                                ->where([cn::USERS_ROLE_ID_COL => 3,cn::USERS_STATUS_COL => 'active'])
                                ->get();
                    } 
                    if(!empty($GradeClassData)){
                        $type = 'grade_class';
                        $html = (string)View::make('backend.question_generator.assign_new_student_group',compact('GradeClassData','StudentList','oldStudentList','type','examId','examData','timeSlots'));                    
                    return $this->sendResponse($html);
                    }else{
                        return $this->SendError(__('languages.all_students_already_assign'),422);
                    }        
                }
            }
        }
    }

    /**
     * USE : Add late commerce students or peer groups
     */
    public function AddLateCommerceStudentOrPeerGroup(Request $request){
        if(!empty($request->peerGroupIds)){
            $groupIds = $request->peerGroupIds;
            $examId = $request->examid;
            $examData = Exam::find($examId);
            $oldPeerGroupIds = explode(',',$examData->{cn::EXAM_TABLE_PEER_GROUP_IDS_COL});
            $oldPeerGroupStudentIds = explode(',',$examData->{cn::EXAM_TABLE_STUDENT_IDS_COL});
            $peerGroupStudentIds = PeerGroupMember::whereIn(cn::PEER_GROUP_MEMBERS_PEER_GROUP_ID_COL,$groupIds)->pluck(cn::PEER_GROUP_MEMBERS_MEMBER_ID_COL)->toArray();
            $newPeerGroupIds = implode(',',array_unique(array_merge($oldPeerGroupIds,$groupIds)));
            $newPeerGroupStudentIds = implode(',',array_unique(array_merge($oldPeerGroupStudentIds,$peerGroupStudentIds)));
            
            //Update Exam in Student and Group Ids
            if(!empty($examData->{cn::EXAM_TABLE_PARENT_EXAM_ID_COLS})){
                Exam::where(cn::EXAM_TABLE_ID_COLS,$examData->{cn::EXAM_TABLE_PARENT_EXAM_ID_COLS})->update([cn::EXAM_TABLE_PEER_GROUP_IDS_COL => $newPeerGroupIds, cn::EXAM_TABLE_STUDENT_IDS_COL => $newPeerGroupStudentIds]);
                Exam::find($examId)->update([cn::EXAM_TABLE_PEER_GROUP_IDS_COL => $newPeerGroupIds, cn::EXAM_TABLE_STUDENT_IDS_COL => $newPeerGroupStudentIds]);
            }else{
                if(Exam::where(cn::EXAM_TABLE_PARENT_EXAM_ID_COLS,$examData->{cn::EXAM_TABLE_ID_COLS})->Exists()){
                    $schoolID = Auth::user()->{cn::USERS_SCHOOL_ID_COL};
                    Exam::where(cn::EXAM_TABLE_ID_COLS,$examData->{cn::EXAM_TABLE_PARENT_EXAM_ID_COLS})->where(cn::EXAM_TABLE_SCHOOL_COLS,$schoolID)->update([cn::EXAM_TABLE_PEER_GROUP_IDS_COL => $newPeerGroupIds, cn::EXAM_TABLE_STUDENT_IDS_COL => $newPeerGroupStudentIds]);
                    Exam::find($examId)->update([cn::EXAM_TABLE_PEER_GROUP_IDS_COL => $newPeerGroupIds, cn::EXAM_TABLE_STUDENT_IDS_COL => $newPeerGroupStudentIds]);
                }else{
                    Exam::find($examId)->update([cn::EXAM_TABLE_PEER_GROUP_IDS_COL => $newPeerGroupIds, cn::EXAM_TABLE_STUDENT_IDS_COL => $newPeerGroupStudentIds]);
                }
            }
            // Add Peer Group assign test in ExamGradeClassMappingModel table
            $this->AssignTestToPeerGroups($examId, $groupIds, $request);
            return $this->sendResponse([], __('languages.student_or_group_added_successfully'));
        }else{
            $schoolId = Auth::user()->{cn::USERS_SCHOOL_ID_COL};
            $examid = $request->examid;
            $examData = Exam::find($examid);
            $oldStudentIds = explode(',',$examData->student_ids);
            $newStudentIds = implode(',',array_unique(array_merge($oldStudentIds,$request->studentIds)));
            Exam::find($examid)->update(['student_ids' => $newStudentIds]);
            $studentIds='';
            if(isset($request->studentIds) && !empty($request->studentIds)){
                $studentIds = implode(',',$request->studentIds);
            }
            if(isset($request->classes) && !empty($request->classes)){
                foreach($request->classes as $gradeId => $classIds){
                    foreach($classIds as $classId){
                        $oldExamData = ExamGradeClassMappingModel::where([
                            cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_SCHOOL_ID_COL => $schoolId,
                            cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_GRADE_ID_COL => $gradeId,
                            cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_CLASS_ID_COL => $classId,
                            cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_EXAM_ID_COL => $examid
                        ])->first();
                        $oldDataId = ExamGradeClassMappingModel::where([
                            cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_SCHOOL_ID_COL => $schoolId,
                            cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_GRADE_ID_COL => $gradeId,
                            cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_CLASS_ID_COL => $classId,
                            cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_EXAM_ID_COL => $examid
                        ])->pluck(cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_ID_COL)->toArray();
                        $studentIdsArray = User::where(cn::USERS_SCHOOL_ID_COL,Auth::user()->{cn::USERS_SCHOOL_ID_COL})->where(cn::USERS_ROLE_ID_COL,'=',cn::STUDENT_ROLE_ID)->where(cn::USERS_GRADE_ID_COL,$gradeId)->where(cn::USERS_CLASS_ID_COL,$classId)->pluck(cn::USERS_ID_COL)->toArray();
                        $examStudentIdsComm = '';
                        $examStudentIds = array_intersect($request->studentIds,$studentIdsArray);
                        if(isset($examStudentIds) && !empty($examStudentIds)){
                            if(isset($oldExamData) && !empty($oldExamData)){
                                $oldDataStudentIds=$oldExamData->{cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_STUDENT_IDS_COL};
                                $oldDataStudentIds=explode(',',$oldDataStudentIds);
                                $examStudentIds=array_merge($examStudentIds,$oldDataStudentIds);
                            }
                            $examStudentIdsComm = implode(',',$examStudentIds);
                        }
                        $startDate = date('Y-m-d');//$this->DateConvertToYMD($exam->from_date) ?? '';
                        if(isset($request->generator_class_start_date[$gradeId][$classId])){
                            $startDate = $this->DateConvertToYMD($request->generator_class_start_date[$gradeId][$classId]);
                        }
                        $endDate = date('Y-m-d');//$this->DateConvertToYMD($request->end_date) ?? '';
                        if(isset($request->generator_class_end_date[$gradeId][$classId])){
                            $endDate = $this->DateConvertToYMD($request->generator_class_end_date[$gradeId][$classId]);
                        }
                        $startTime = ($request->start_time !='') ? $request->start_time.':00' : '';
                        if(isset($request->generator_class_start_time[$gradeId][$classId]) && $request->generator_class_start_time[$gradeId][$classId]!=""){
                            $startTime = $request->generator_class_start_time[$gradeId][$classId].':00';
                        }

                        $endTime = ($request->end_time !='') ? $request->end_time.':00' : '';
                        if(isset($request->generator_class_end_time[$gradeId][$classId]) && $request->generator_class_end_time[$gradeId][$classId]!=""){
                            $endTime = $request->generator_class_end_time[$gradeId][$classId].':00';
                        }
                        $examGradeClassMappingData = [
                            cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_STUDENT_IDS_COL => $examStudentIdsComm ?? null,
                            cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_START_TIME_COL => $startTime,
                            cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_END_TIME_COL => $endTime,
                            cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_START_DATE_COL => $startDate,
                            cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_END_DATE_COL => $endDate,
                            'status' => ($request->has('save_and_publish')) ? 'publish' : 'draft'
                        ];
                        if(isset($oldDataId) && !empty($oldDataId)){
                            $removeDataId[] = $oldDataId[0];
                            ExamGradeClassMappingModel::where([
                                cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_SCHOOL_ID_COL => $schoolId,
                                cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_GRADE_ID_COL => $gradeId,
                                cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_CLASS_ID_COL => $classId,
                                cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_EXAM_ID_COL => $examid
                            ])->update($examGradeClassMappingData);
                        }else{
                            $examGradeClassData = [
                                cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_SCHOOL_ID_COL => $schoolId,
                                cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_GRADE_ID_COL => $gradeId,
                                cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_CLASS_ID_COL => $classId,
                                cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_EXAM_ID_COL => $examid,
                                cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_STUDENT_IDS_COL => $examStudentIdsComm ?? null,
                                cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_START_TIME_COL => $startTime,
                                cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_END_TIME_COL => $endTime,
                                cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_START_DATE_COL => $startDate,
                                cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_END_DATE_COL => $endDate,
                                cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_STATUS_COL => 'publish'
                            ];
                            $examGradeClassMapping = ExamGradeClassMappingModel::create($examGradeClassData);
                        }
                    }
                }
            }
            return $this->sendResponse([], __('languages.student_or_group_added_successfully'));
        }
    }

    /**
     * USE : Admin can view for proof-reading questions
     */
    public function InspectModeProofReadingQuestions(Request $request){
        $difficultyLevels = PreConfigurationDiffiltyLevel::all();
        $strandsList = [];
        $LearningUnits = [];
        $LearningObjectives = [];

        // Get the strands list
        $strandsList = Strands::all();
        if(!empty($strandsList)){
            $LearningUnits = LearningsUnits::where(cn::LEARNING_UNITS_STRANDID_COL, $strandsList[0]->{cn::STRANDS_ID_COL})->get();
            if(!empty($LearningUnits)){
                $LearningObjectives = LearningsObjectives::IsAvailableQuestion()->whereIn(cn::LEARNING_OBJECTIVES_LEARNING_UNITID_COL, $LearningUnits->pluck(cn::LEARNING_OBJECTIVES_ID_COL))->get();
            }
        }

        // Check methods is GET or POST
        if($request->isMethod('POST')){
            if(isset($request)){
                /**
                 * USE : Find the selected test type based on count available question wise learning objectives
                 */
                if(isset($request->action) && $request->action == 'get_learning_objectives_list'){
                    $learningObjectivesHtml = '';
                    $requestData = $request->all();
                    $testType = $request->test_type;
                    if(!empty($request->learning_unit_id)){
                        $LearningObjectives = LearningsObjectives::IsAvailableQuestion()->whereIn(cn::LEARNING_OBJECTIVES_LEARNING_UNITID_COL, $request->learning_unit_id)->get();
                    }
                    $learningObjectivesHtml = (string)View::make('backend.question_generator.admin.learning_objectives_list',compact('LearningObjectives','testType','requestData'));
                    return $this->sendResponse($learningObjectivesHtml);
                    exit;
                }

                $difficultyLevels = PreConfigurationDiffiltyLevel::all();
                $result = array();
                $learningUnitArray = array();
                $coded_questions_list_all = array();
                $difficulty_lvl = $request->difficulty_lvl;
                if(isset($request->difficulty_mode) && $request->difficulty_mode=='auto' && isset($studentIds) && empty($studentIds)){
                    return $this->sendError(__('languages.please_select_students'), 422);
                }
    
                $QuestionCodeLists = [];
                if(isset($request->learning_unit) && !empty($request->learning_unit)){
                    foreach($request->learning_unit as $learningUnitId => $learningUnitData){
                        if(isset($learningUnitData['learning_objective']) && !empty($learningUnitData['learning_objective'])){
                            foreach($learningUnitData['learning_objective'] as $id => $data){
                                $objective_mapping_id = StrandUnitsObjectivesMappings::where(cn::OBJECTIVES_MAPPINGS_LEARNING_UNIT_ID_COL,$learningUnitId)
                                                        ->where(cn::OBJECTIVES_MAPPINGS_LEARNING_OBJECTIVES_ID_COL,$id)
                                                        ->pluck(cn::OBJECTIVES_MAPPINGS_ID_COL)
                                                        ->toArray();
                                $selected_levels = array();
                                if($request->difficulty_mode == 'manual' && array_key_exists('learning_objectives_difficulty_level',$data)){
                                    $selected_levels = $data['learning_objectives_difficulty_level'];
                                }
                                $QuestionsCodes = Question::with('PreConfigurationDifficultyLevel')
                                                ->whereIn(cn::QUESTION_OBJECTIVE_MAPPING_ID_COL,$objective_mapping_id)
                                                //->where(cn::QUESTION_QUESTION_TYPE_COL,$request->test_type)
                                                //->whereIn(cn::QUESTION_QUESTION_TYPE_COL,[2,3])
                                                ->where(function($query) use($request){
                                                    if($request->test_type == 'test'){
                                                        $query->where(cn::QUESTION_QUESTION_TYPE_COL,3);
                                                    }else if($request->test_type == 'exercise'){
                                                        $query->where(cn::QUESTION_QUESTION_TYPE_COL,2);
                                                    }else if($request->test_type == 'self_learning'){
                                                        $query->where(cn::QUESTION_QUESTION_TYPE_COL,1);
                                                    }else if($request->test_type == 'testing_zone'){
                                                        $query->where(cn::QUESTION_QUESTION_TYPE_COL,1);
                                                    }else if($request->test_type == 'seed'){
                                                        $query->where(cn::QUESTION_QUESTION_TYPE_COL,4);
                                                    }
                                                })
                                                ->whereIn(cn::QUESTION_DIFFICULTY_LEVEL_COL,$selected_levels)
                                                ->limit($data['get_no_of_question_learning_objectives'])
                                                ->orderBy(cn::QUESTION_QUESTION_CODE_COL,'ASC')
                                                ->pluck(cn::QUESTION_QUESTION_CODE_COL)
                                                ->toArray();
                                if(!empty($QuestionsCodes)){
                                    $QuestionCodeLists[] = $QuestionsCodes;
                                }
                            }
                        }
                    }
                }
                    
                if(isset($QuestionCodeLists) && !empty($QuestionCodeLists)){
                    // Convert multi dimentional array to single array
                    $responseQuestionCodesArray = $this->array_flatten($QuestionCodeLists);
                    if(isset($responseQuestionCodesArray) && !empty($responseQuestionCodesArray)){
                        $question_list = Question::with(['answers','PreConfigurationDifficultyLevel','objectiveMapping'])
                                        ->whereIn(cn::QUESTION_QUESTION_CODE_COL,$responseQuestionCodesArray)
                                        ->orderBy(cn::QUESTION_QUESTION_CODE_COL,'ASC')
                                        ->get();
                        $question_id_list = $question_list->pluck(cn::QUESTION_TABLE_ID_COL)->toArray();
                        if(isset($question_id_list) && !empty($question_id_list)){
                            $result['html'] = (string)View::make('backend.question_generator.admin.question_list_inspect_mode',compact('question_list','difficultyLevels'));
                            $result['questionIds'] = $question_id_list;
                            return $this->sendResponse($result);
                        }else{
                            return $this->sendError(__('languages.questions-not-found'), 422);
                        }
                    }else{
                        return $this->sendError(__('languages.not_enough_questions_in_that_objective'), 422);
                    }
                }else{
                    return $this->sendError(__('languages.problem_was_occur_please_try_again'), 422);
                }
            }
        }
        return view('backend.question_generator.admin.inspect_mode_proof_reading_question',compact('difficultyLevels','strandsList','LearningUnits','LearningObjectives'));
    }

    public function ChangeExamEndDate(Request $request){
        $UpdateExamEndDate = '';
        $ExamData = Exam::find($request->ExamId);
        if(!empty($ExamData)){
            switch(Auth::user()->role_id){
                case 1:
                    if($request->ExamType=="EndDate"){
                        if($ExamData->use_of_mode== 1){
                            ExamGradeClassMappingModel::where(cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_EXAM_ID_COL,$request->ExamId)
                                            ->update([cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_END_DATE_COL=>$this->DateConvertToYMD($request->to_date)]);
                            $UpdateExamEndDate = Exam::where(cn::EXAM_TABLE_ID_COLS,$request->ExamId)
                                                ->update([cn::EXAM_TABLE_TO_DATE_COLS=>$this->DateConvertToYMD($request->to_date)]);
                        }else{
                            ExamGradeClassMappingModel::where(cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_EXAM_ID_COL,$request->ExamId)
                            ->update([cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_END_DATE_COL=>$this->DateConvertToYMD($request->to_date)]);
                            $UpdateExamEndDate = Exam::where(cn::EXAM_TABLE_ID_COLS,$request->ExamId)
                                                ->orWhere(cn::EXAM_TABLE_PARENT_EXAM_ID_COLS,$request->ExamId)
                                                ->update([cn::EXAM_TABLE_TO_DATE_COLS=>$this->DateConvertToYMD($request->to_date)]);
                        }
                    }else{
                        if($ExamData->use_of_mode== 1){
                            $UpdateExamEndDate = Exam::where(cn::EXAM_TABLE_ID_COLS,$request->ExamId)
                                                ->update([cn::EXAM_TABLE_RESULT_DATE_COLS=>$this->DateConvertToYMD($request->to_date)]);
                        }else{
                            $UpdateExamEndDate = Exam::where(cn::EXAM_TABLE_ID_COLS,$request->ExamId)
                                                ->orWhere(cn::EXAM_TABLE_PARENT_EXAM_ID_COLS,$request->ExamId)
                                                ->update([cn::EXAM_TABLE_RESULT_DATE_COLS=>$this->DateConvertToYMD($request->to_date)]);
                        }
                    }
                    break;
                case 2:
                   if($request->ExamType=="EndDate"){
                        ExamGradeClassMappingModel::where(cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_EXAM_ID_COL,$request->ExamId)
                        ->where(cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_SCHOOL_ID_COL,Auth::user()->school_id)
                        ->update([cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_END_DATE_COL=>$this->DateConvertToYMD($request->to_date)]);
                        $UpdateExamEndDate = Exam::where(cn::EXAM_TABLE_ID_COLS,$request->ExamId)
                                            ->where(cn::EXAM_TABLE_CREATED_BY_COL,Auth::user()->id)
                                            ->where(cn::EXAM_TABLE_SCHOOL_COLS,Auth::user()->school_id)
                                            ->update([cn::EXAM_TABLE_TO_DATE_COLS=>$this->DateConvertToYMD($request->to_date)]);
                   }else{
                        $UpdateExamEndDate = Exam::where(cn::EXAM_TABLE_ID_COLS,$request->ExamId)
                                            ->where(cn::EXAM_TABLE_CREATED_BY_COL,Auth::user()->id)
                                            ->where(cn::EXAM_TABLE_SCHOOL_COLS,Auth::user()->school_id)
                                            ->update([cn::EXAM_TABLE_RESULT_DATE_COLS=>$this->DateConvertToYMD($request->to_date)]);
                   }
                    break;
                case 5:
                case 7:
                    if($request->ExamType=="EndDate"){
                        ExamGradeClassMappingModel::where(cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_EXAM_ID_COL,$request->ExamId)
                                            ->where(cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_SCHOOL_ID_COL,Auth::user()->school_id)
                                            ->update([cn::EXAM_SCHOOL_GRADE_CLASS_MAPPING_END_DATE_COL=>$this->DateConvertToYMD($request->to_date)]);
                        $UpdateExamEndDate = Exam::where(cn::EXAM_TABLE_ID_COLS,$request->ExamId)
                                            ->where(cn::EXAM_TABLE_SCHOOL_COLS,Auth::user()->school_id)
                                            ->update([cn::EXAM_TABLE_TO_DATE_COLS=>$this->DateConvertToYMD($request->to_date)]);
                    }else{
                        $UpdateExamEndDate = Exam::where(cn::EXAM_TABLE_ID_COLS,$request->ExamId)
                                            ->where(cn::EXAM_TABLE_SCHOOL_COLS,Auth::user()->school_id)
                                            ->update([cn::EXAM_TABLE_RESULT_DATE_COLS=>$this->DateConvertToYMD($request->to_date)]);
                    }
                    break;
            }
            if($UpdateExamEndDate){
                return redirect()->route('question-wizard')->with('success_msg', __('languages.exam_date_updated_successfully'));
            }else{
                return back()->with('error_msg', __('languages.problem_was_occur_please_try_again'));
            }
        }
       
    }
}