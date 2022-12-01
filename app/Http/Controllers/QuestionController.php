<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use APP\Constants\DbConstant As cn;
use App\Http\Services\AIApiService;
use App\Http\Repositories\QuestionsRepository;
use App\Traits\Common;
use App\Models\Answer;
use App\Models\MainUploadDocument;
use App\Models\UploadDocuments;
use App\Models\Question;
use App\Models\Grades;
use App\Models\Subjects;
use App\Models\Strands;
use App\Models\LearningsUnits;
use App\Models\LearningsObjectives;
use App\Models\StrandUnitsObjectivesMappings;
use App\Models\PreConfigurationDiffiltyLevel;
use App\Models\Nodes;
use App\Models\School;
use App\Models\Languages;
use App\Models\Exam;
use App\Models\ExamConfigurationsDetails;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\View;
use App\Http\Controllers\StudentController;
use App\Helpers\Helper;

class QuestionController extends Controller
{
    // Load Common Traits
    use Common;

    protected $DefaultStudentOverAllAbility;

    public function __construct(){
        $this->QuestionsRepository = new QuestionsRepository();
        $this->StudentController = new StudentController();
        $this->AIApiService = new AIApiService();
        $this->DefaultStudentOverAllAbility = 0.1;
    }

    public function index(Request $request){
        try{
            //  Laravel Pagination set in Cookie
            //$this->paginationCookie('QuestionList',$request);
           
            if (!in_array('question_bank_read', Helper::getPermissions(Auth::user()->{cn::USERS_ID_COL}))) {
                return  redirect(Helper::redirectRoleBasedDashboard(Auth::user()->{cn::USERS_ID_COL}));
            }
            $items = $request->items ?? 10; //For Pagination

            // $TotalQuestionData = Question::all()->count();
            $gradeList = Grades::all()->unique(cn::GRADES_NAME_COL);
            $nodeList = Nodes::where(cn::NODES_STATUS_COL,'active')->get();
            $PreConfigurationDifficultyLevel = array();
            $PreConfigurationDiffiltyLevelData = PreConfigurationDiffiltyLevel::get()->toArray();
            if(isset($PreConfigurationDiffiltyLevelData)){
                $PreConfigurationDifficultyLevel = array_column($PreConfigurationDiffiltyLevelData,cn::PRE_CONFIGURE_DIFFICULTY_TITLE_COL,cn::PRE_CONFIGURE_DIFFICULTY_DIFFICULTY_LEVEL_COL);
            }
            $QuestionCode = Question::pluck(cn::QUESTION_QUESTION_CODE_COL);
            $QuestionList = $this->QuestionsRepository->getAllQuestionList($items);
            $difficultyLevels = $this->getDifficultyLevel();
            $QuestionTypes = $this->getQuestionTypes();
            $statusList = array(
                ['id' => '1',"name" => 'Publish'],
                ['id' => '0',"name" => 'Save Draft']
            );
            // for filteration code here
            $SearchQuestionQuery = Question::select('*');
            if(isset($request->filter) || !empty(Session::get('QuestionListFilter'))){
                $this->saveAndGetFilterData('QuestionListFilter',$request);
                //filter on Grade
                if(isset($request->grade_id) && !empty($request->grade_id)){
                    $StrandUnitsObjectivesMappingsIds = StrandUnitsObjectivesMappings::where(cn::OBJECTIVES_MAPPINGS_GRADE_ID_COL,$request->grade_id)->pluck(cn::OBJECTIVES_MAPPINGS_ID_COL);
                    if(!empty($StrandUnitsObjectivesMappingsIds)){
                        $SearchQuestionQuery->whereIn(cn::QUESTION_OBJECTIVE_MAPPING_ID_COL,$StrandUnitsObjectivesMappingsIds);
                    }
                }
                // Search by question code
                if(isset($request->question_code) && !empty($request->question_code)){
                    $SearchQuestionQuery->where(cn::QUESTION_NAMING_STRUCTURE_CODE_COL,'like','%'.$request->question_code.'%');
                }
                // Search by difficulty level
                if(isset($request->difficulty_level) && !empty($request->difficulty_level)){
                    $SearchQuestionQuery->where(cn::QUESTION_DIFFICULTY_LEVEL_COL,$request->difficulty_level);
                }

                // Serch By Question Type
                if(isset($request->question_type) && !empty($request->question_type)){
                    $SearchQuestionQuery->whereRaw("find_in_set('".$request->question_type."',question_type)");
                }
                //Search By Status
                if(isset($request->Status)){
                    $SearchQuestionQuery->where(cn::QUESTINON_STATUS_COL,$request->Status);
                }
                if(isset($request->question_approve) && !empty($request->question_approve)){
                    $SearchQuestionQuery->where(cn::QUESTION_IS_APPROVED_COL,$request->question_approve);
                }
                $QuestionList = $SearchQuestionQuery->sortable()->paginate($items);
            }
            // Return all compact parameter
            $CompactArray = ['QuestionCode','QuestionList','difficultyLevels','gradeList','nodeList','statusList','QuestionTypes','items','PreConfigurationDifficultyLevel'];
            return view('backend.question.list',compact($CompactArray));
        } catch (\Exception $exception) {
            return redirect('questions')->withError($exception->getMessage());
        }
    }

    public function create(){
        try{
            if(!in_array('question_bank_create', Helper::getPermissions(Auth::user()->{cn::USERS_ID_COL}))) {
                return  redirect(Helper::redirectRoleBasedDashboard(Auth::user()->{cn::USERS_ID_COL}));
            }
            $code = [];
            $subjects = $strands = $LearningUnits = $LearningObjectives = $questionCode = '';
            $Grades = Grades::all();
            $schoolList = School::get();
            $code['grade'] = 4;//$Grades[0]['code'];
            $code['grade_id'] = 4;//$Grades[0]['id'];
            $mainnodeList = Nodes::where(cn::NODES_IS_MAIN_NODE_COL,1)->where(cn::NODES_STATUS_COL,'active')->get();
            $Nodes = new Nodes;
            $NodesList = $Nodes->get_nodelist();
            // Extra changes
            $subjectIds = StrandUnitsObjectivesMappings::where(cn::OBJECTIVES_MAPPINGS_GRADE_ID_COL,4)->groupBy(cn::OBJECTIVES_MAPPINGS_SUBJECT_ID_COL)->pluck(cn::OBJECTIVES_MAPPINGS_SUBJECT_ID_COL);
            if($subjectIds->isNotEmpty()){
                $subjectIds = array_unique($subjectIds->toArray());
                $subjects = Subjects::whereIn(cn::SUBJECTS_ID_COL, $subjectIds)->get();
                $code['subject'] = $subjects[0]['code'];
                $code['subject_id'] = $subjects[0]['id'];
            }
            $strandsIds = StrandUnitsObjectivesMappings::where(cn::OBJECTIVES_MAPPINGS_GRADE_ID_COL,4)
                    ->where(cn::OBJECTIVES_MAPPINGS_SUBJECT_ID_COL,1)
                    ->pluck(cn::OBJECTIVES_MAPPINGS_STRAND_ID_COL);
            if($strandsIds->isNotEmpty()){
                $strandsIds = array_unique($strandsIds->toArray());
                $strands = Strands::whereIn(cn::STRANDS_ID_COL, $strandsIds)->get();
                $code['strand'] = $strands[0]['code'];
                $code['strand_id'] = $strands[0]['id'];
            }
            $learningUnitsIds = StrandUnitsObjectivesMappings::where(cn::OBJECTIVES_MAPPINGS_GRADE_ID_COL,4)
                    ->where(cn::OBJECTIVES_MAPPINGS_SUBJECT_ID_COL,1)
                    ->where(cn::OBJECTIVES_MAPPINGS_STRAND_ID_COL,1)
                    ->pluck(cn::OBJECTIVES_MAPPINGS_LEARNING_UNIT_ID_COL);
            if($learningUnitsIds->isNotEmpty()){
                $learningUnitsIds = array_unique($learningUnitsIds->toArray());
                $LearningUnits = LearningsUnits::whereIn(cn::LEARNING_UNITS_ID_COL, $learningUnitsIds)->get();
                $code['LearningUnit'] = $LearningUnits[0]['code'];
                $code['LearningUnit_id'] = $LearningUnits[0]['id'];
            }
            $learningObjectivesIds = StrandUnitsObjectivesMappings::where(cn::OBJECTIVES_MAPPINGS_GRADE_ID_COL,4)
                    ->where(cn::OBJECTIVES_MAPPINGS_SUBJECT_ID_COL,1)
                    ->where(cn::OBJECTIVES_MAPPINGS_STRAND_ID_COL,1)
                    ->where(cn::OBJECTIVES_MAPPINGS_LEARNING_UNIT_ID_COL,1)
                    ->pluck(cn::OBJECTIVES_MAPPINGS_LEARNING_OBJECTIVES_ID_COL);
            if($learningObjectivesIds->isNotEmpty()){
                $learningObjectivesIds = array_unique($learningObjectivesIds->toArray());
                // $LearningObjectives = LearningsObjectives::whereIn(cn::LEARNING_OBJECTIVES_ID_COL, $learningObjectivesIds)->get();
                $LearningObjectives = LearningsObjectives::IsAvailableQuestion()->whereIn(cn::LEARNING_OBJECTIVES_ID_COL, $learningObjectivesIds)->get();
                $code['LearningObjective'] = $LearningObjectives[0]['code'];
                $code['LearningObjective_id'] = $LearningObjectives[0]['id'];
            }
            $code['g'] = 'F';
            $code['e'] = 1;
            $code['f'] = 1;

            $count = 0;
            if(!empty($code['grade_id']) && !empty($code['subject_id']) && !empty($code['strand_id']) && !empty($code['LearningUnit_id']) && !empty($code['LearningObjective_id'])){
                $MappingIds = StrandUnitsObjectivesMappings::where(cn::OBJECTIVES_MAPPINGS_GRADE_ID_COL,$code['grade_id'])
                            ->where(cn::OBJECTIVES_MAPPINGS_SUBJECT_ID_COL,$code['subject_id'])
                            ->where(cn::OBJECTIVES_MAPPINGS_STRAND_ID_COL,$code['strand_id'])
                            ->where(cn::OBJECTIVES_MAPPINGS_LEARNING_UNIT_ID_COL,$code['LearningUnit_id'])
                            ->where(cn::OBJECTIVES_MAPPINGS_LEARNING_OBJECTIVES_ID_COL,$code['LearningObjective_id'])
                            ->pluck(cn::OBJECTIVES_MAPPINGS_ID_COL);
                if($MappingIds->isNotEmpty()){
                    $count = Question::whereIn(cn::QUESTION_OBJECTIVE_MAPPING_ID_COL, $MappingIds->toArray())->where(cn::QUESTION_G_COL,strtolower('f'))->count();
                }
            }
        
            // Create static question code
            if(!empty($code['grade']) && !empty($code['subject']) && !empty($code['strand'])){
                $questionCode = $this->getQuestionCode($code,$count);
            }
            return view('backend.question.add',compact("Grades","mainnodeList","subjects","strands","LearningUnits","LearningObjectives","questionCode","schoolList","NodesList"));
           
        } catch (\Exception $exception) {
            return back()->withError($exception->getMessage())->withInput();
        }
    }

    /**
     * USE : Store Question
     */
    public function store(Request $request){
        try{
            if(!in_array('question_bank_create', Helper::getPermissions(Auth::user()->{cn::USERS_ID_COL}))) {
                return  redirect(Helper::redirectRoleBasedDashboard(Auth::user()->{cn::USERS_ID_COL}));
            }
            $validator = Validator::make($request->all(), Question::rules($request, 'create'), Question::rulesMessages('create'));
            if ($validator->fails()) {
                $validatorParams = $this->QuestionsRepository->validatorSendParams($request);
                $strands = isset($validatorParams['strands']) ? $validatorParams['strands'] : [];
                $LearningUnits = isset($validatorParams['LearningUnits']) ? $validatorParams['LearningUnits'] : [];
                $subjects = isset($validatorParams['subjects']) ? $validatorParams['subjects'] : [];
                $LearningObjectives = isset($validatorParams['LearningObjectives']) ? $validatorParams['LearningObjectives'] : [];
                return back()->withErrors($validator)->withInput()->with(['subjects' => $subjects,'strands' => $strands,'LearningUnits'=>$LearningUnits,'LearningObjectives'=>$LearningObjectives]);
            }
            
            // Get mapping id by question code
            $AddParamsRequest = ['question_code' => str_replace(" ","",$request->naming_structure_code)];
            $request->merge($AddParamsRequest);
            $objectivesMapping = $this->GetStrandUnitsObjectivesMappingsId($request->question_code);
            if(($objectivesMapping['StrandUnitsObjectivesMappingsId'] == 0 || empty($objectivesMapping['e']) || empty($objectivesMapping['f']) || empty($objectivesMapping['g']) || empty($objectivesMapping['question_type']) || empty($objectivesMapping['dificulaty_level']))){
                return back()->withInput()->with('error_msg', __('languages.invalid_question_code_please_try_using_valid_question_code'));
            }
            $questionPostData = array(
                cn::QUESTION_OBJECTIVE_MAPPING_ID_COL       => $objectivesMapping['StrandUnitsObjectivesMappingsId'] ?? 0,
                cn::QUESTION_QUESTION_CODE_COL              => str_replace(" ","",$request->naming_structure_code),
                cn::QUESTION_NAMING_STRUCTURE_CODE_COL      => str_replace(" ","",$request->naming_structure_code),
                cn::QUESTION_QUESTION_UNIQUE_CODE_COL       => $this->UniqueQuestionCodeGenerate(),
                cn::QUESTION_MARKS_COL                      => 1,
                cn::QUESTION_BANK_UPDATED_BY_COL            => Auth::user()->{cn::USERS_ID_COL},
                cn::QUESTION_BANK_SCHOOL_ID_COL             => 1,
                cn::QUESTION_QUESTION_EN_COL                => $request->question_en,
                cn::QUESTION_QUESTION_CH_COL                => $request->question_ch,
                cn::QUESTION_DIFFICULTY_LEVEL_COL           => $objectivesMapping['dificulaty_level'],
                cn::QUESTINON_STATUS_COL                    => ($request->save_draft) ? 0 : 1,
                cn::QUESTION_QUESTION_TYPE_COL              => $objectivesMapping['question_type'],
                cn::QUESTION_GENERAL_HINTS_EN               => $request->general_hints_en ?? null,
                cn::QUESTION_GENERAL_HINTS_CH               => $request->general_hints_ch ?? null,
                cn::QUESTION_FULL_SOLUTION_EN               => $request->full_solution_en ?? null,
                cn::QUESTION_FULL_SOLUTION_CH               => $request->full_solution_ch ?? null,
                cn::QUESTION_GENERAL_HINTS_VIDEO_ID_EN      => ($request->question_video_id_en) ? $request->question_video_id_en : null,
                cn::QUESTION_GENERAL_HINTS_VIDEO_ID_CH      => ($request->question_video_id_ch) ? $request->question_video_id_ch : null,
                cn::QUESTION_E_COL                          => $objectivesMapping['e'],
                cn::QUESTION_F_COL                          => $objectivesMapping['f'],
                cn::QUESTION_G_COL                          => $objectivesMapping['g'],
                cn::QUESTION_IS_APPROVED_COL                => $request->is_approved
            );
            $question = Question::create($questionPostData);
            if($question){
                $StoreAnswerData = array(
                    cn::ANSWER_QUESTION_ID_COL          => $question->id,
                    cn::ANSWER_ANSWER1_EN_COL           => $request->answer1_en,
                    cn::ANSWER_HINT_ANSWER1_EN_COL      => $request->hint_answer1_en,
                    cn::ANSWER_NODE_HINT_ANSWER1_EN_COL => $request->node_hint_answer1_en,
                    cn::ANSWER1_NODE_RELATION_ID_EN_COL => $request->answer1_node_relation_id_en,
                    cn::ANSWER_ANSWER2_EN_COL           => $request->answer2_en,
                    cn::ANSWER_HINT_ANSWER2_EN_COL      => $request->hint_answer2_en,
                    cn::ANSWER_NODE_HINT_ANSWER2_EN_COL => $request->node_hint_answer2_en,
                    cn::ANSWER2_NODE_RELATION_ID_EN_COL => $request->answer2_node_relation_id_en,
                    cn::ANSWER_ANSWER3_EN_COL           => $request->answer3_en,
                    cn::ANSWER_HINT_ANSWER3_EN_COL      => $request->hint_answer3_en,
                    cn::ANSWER_NODE_HINT_ANSWER3_EN_COL => $request->node_hint_answer3_en,
                    cn::ANSWER3_NODE_RELATION_ID_EN_COL => $request->answer3_node_relation_id_en,
                    cn::ANSWER_ANSWER4_EN_COL           => $request->answer4_en,
                    cn::ANSWER_HINT_ANSWER4_EN_COL      => $request->hint_answer4_en,
                    cn::ANSWER_NODE_HINT_ANSWER4_EN_COL => $request->node_hint_answer4_en,
                    cn::ANSWER4_NODE_RELATION_ID_EN_COL => $request->answer4_node_relation_id_en,
                    cn::ANSWER_ANSWER1_CH_COL           => $request->answer1_ch,
                    cn::ANSWER_HINT_ANSWER1_CH_COL      => $request->hint_answer1_ch,
                    cn::ANSWER_NODE_HINT_ANSWER1_CH_COL => $request->node_hint_answer1_ch,
                    cn::ANSWER1_NODE_RELATION_ID_CH_COL => $request->answer1_node_relation_id_en,
                    cn::ANSWER_ANSWER2_CH_COL           => $request->answer2_ch,
                    cn::ANSWER_HINT_ANSWER2_CH_COL      => $request->hint_answer2_ch,
                    cn::ANSWER_NODE_HINT_ANSWER2_CH_COL => $request->node_hint_answer2_ch,
                    cn::ANSWER2_NODE_RELATION_ID_CH_COL => $request->answer2_node_relation_id_en,
                    cn::ANSWER_ANSWER3_CH_COL           => $request->answer3_ch,
                    cn::ANSWER_HINT_ANSWER3_CH_COL      => $request->hint_answer3_ch,
                    cn::ANSWER_NODE_HINT_ANSWER3_CH_COL => $request->node_hint_answer3_ch,
                    cn::ANSWER3_NODE_RELATION_ID_CH_COL => $request->answer3_node_relation_id_en,
                    cn::ANSWER_ANSWER4_CH_COL           => $request->answer4_ch,
                    cn::ANSWER_HINT_ANSWER4_CH_COL      => $request->hint_answer4_ch,
                    cn::ANSWER_NODE_HINT_ANSWER4_CH_COL => $request->node_hint_answer4_ch,
                    cn::ANSWER4_NODE_RELATION_ID_CH_COL => $request->answer4_node_relation_id_en,
                    cn::ANSWER_CORRECT_ANSWER_EN_COL    => $request->correct_answer_en,
                    cn::ANSWER_CORRECT_ANSWER_CH_COL    => $request->correct_answer_ch,
                );
               
                $result = Answer::create($StoreAnswerData);
                if($result){
                    return redirect('questions')->with('success_msg', __('languages.question_added_successfully'));
                }else{
                    return back()->withInput()->with('error_msg', __('languages.problem_was_occur_please_try_again'));
                }
            }else{
                return back()->withInput()->with('error_msg', __('languages.problem_was_occur_please_try_again'));
            }
        } catch (\Exception $exception) {
            return back()->withError($exception->getMessage())->withInput();
        }
    }

    /**
     * USE : Get Selected Record data
     */
    public function edit($id){
        try{
            if(!in_array('question_bank_update', Helper::getPermissions(Auth::user()->{cn::USERS_ID_COL}))) {
               return  redirect(Helper::redirectRoleBasedDashboard(Auth::user()->{cn::USERS_ID_COL}));
            } 
            $question_type_ids = [];
            $ObjectivesMapping = [];
            $Grades = Grades::all();
            $nodeList = Nodes::where(cn::NODES_STATUS_COL,'active')->get();
            $mainnodeList = Nodes::where(cn::NODES_IS_MAIN_NODE_COL,1)->where(cn::NODES_STATUS_COL,'active')->get();
            $nodeWeaknessList = array();
            $nodeWeaknessChList = array();
            $nodeMainIdList = array();
            if (!empty($nodeList)) { 
                $nodeListToArray=$nodeList->toArray();
                $nodeWeaknessList = array_column($nodeListToArray,'weakness_name_en','id');
                $nodeWeaknessChList = array_column($nodeListToArray,'weakness_name_ch','id');
                $nodeMainIdList = array_column($nodeListToArray,'id','id');
            }
            $NodesParent = new Nodes;
            $NodesList = $NodesParent->get_nodelist();
            

            $QuestionData = Question::with('answers')
                            ->where(cn::QUESTION_TABLE_NAME.'.'.cn::QUESTION_TABLE_ID_COL,$id)
                            ->first();
        
            if(isset($QuestionData)){
                $question_type_ids = explode(',',$QuestionData->question_type);
                // $ObjectivesMapping = StrandUnitsObjectivesMappings::where(cn::OBJECTIVES_MAPPINGS_ID_COL,$QuestionData->{cn::QUESTION_OBJECTIVE_MAPPING_ID_COL})->first();
                // $subjectIds = StrandUnitsObjectivesMappings::where(cn::OBJECTIVES_MAPPINGS_GRADE_ID_COL,$ObjectivesMapping->grade_id)->groupBy(cn::OBJECTIVES_MAPPINGS_SUBJECT_ID_COL)->pluck(cn::OBJECTIVES_MAPPINGS_SUBJECT_ID_COL);
                // if(!empty($subjectIds)){
                //     $subjectIds = array_unique($subjectIds->toArray());
                //     $subjects = Subjects::whereIn(cn::SUBJECTS_ID_COL, $subjectIds)->get();
                // }

                // $strandsIds = StrandUnitsObjectivesMappings::where(cn::OBJECTIVES_MAPPINGS_GRADE_ID_COL,$ObjectivesMapping->grade_id)
                //         ->where(cn::OBJECTIVES_MAPPINGS_SUBJECT_ID_COL,$ObjectivesMapping->subject_id)
                //         ->pluck(cn::OBJECTIVES_MAPPINGS_STRAND_ID_COL);
                // if(!empty($strandsIds)){
                //     $strandsIds = array_unique($strandsIds->toArray());
                //     $strands = Strands::whereIn(cn::STRANDS_ID_COL, $strandsIds)->get();
                // }

                // $learningUnitsIds = StrandUnitsObjectivesMappings::where(cn::OBJECTIVES_MAPPINGS_GRADE_ID_COL,$ObjectivesMapping->grade_id)
                //         ->where(cn::OBJECTIVES_MAPPINGS_SUBJECT_ID_COL,$ObjectivesMapping->subject_id)
                //         ->where(cn::OBJECTIVES_MAPPINGS_STRAND_ID_COL,$ObjectivesMapping->strand_id)
                //         ->pluck(cn::OBJECTIVES_MAPPINGS_LEARNING_UNIT_ID_COL);
                // if(!empty($learningUnitsIds)){
                //     $learningUnitsIds = array_unique($learningUnitsIds->toArray());
                //     $LearningUnits = LearningsUnits::whereIn(cn::LEARNING_UNITS_ID_COL, $learningUnitsIds)->get();
                // }

                // $learningObjectivesIds = StrandUnitsObjectivesMappings::where(cn::OBJECTIVES_MAPPINGS_GRADE_ID_COL,$ObjectivesMapping->grade_id)
                //         ->where(cn::OBJECTIVES_MAPPINGS_SUBJECT_ID_COL,$ObjectivesMapping->subject_id)
                //         ->where(cn::OBJECTIVES_MAPPINGS_STRAND_ID_COL,$ObjectivesMapping->strand_id)
                //         ->where(cn::OBJECTIVES_MAPPINGS_LEARNING_UNIT_ID_COL,$ObjectivesMapping->learning_unit_id)
                //         ->pluck(cn::OBJECTIVES_MAPPINGS_LEARNING_OBJECTIVES_ID_COL);
                // if(!empty($learningObjectivesIds)){
                //     $learningObjectivesIds = array_unique($learningObjectivesIds->toArray());
                //     $LearningObjectives = LearningsObjectives::whereIn(cn::LEARNING_OBJECTIVES_ID_COL, $learningObjectivesIds)->get();
                // }
            }

            //return view('backend.question.edit',compact('Grades','subjects','strands','LearningUnits','LearningObjectives','ObjectivesMapping','QuestionData','question_type_ids'));
            return view('backend.question.edit',compact('QuestionData','question_type_ids','nodeWeaknessList','mainnodeList','nodeMainIdList','NodesList','nodeWeaknessChList'));
           
        } catch (\Exception $exception) {
            return back()->withError($exception->getMessage())->withInput();
        }
    }

    public function questionCopyAndCreate($id){
        try{
            if(!in_array('question_bank_update', Helper::getPermissions(Auth::user()->{cn::USERS_ID_COL}))) {
               return  redirect(Helper::redirectRoleBasedDashboard(Auth::user()->{cn::USERS_ID_COL}));
            } 
            $question_type_ids = [];
            $ObjectivesMapping = [];
            $Grades = Grades::all();
            $nodeList = Nodes::where(cn::NODES_STATUS_COL,'active')->get();
            $mainnodeList = Nodes::where(cn::NODES_IS_MAIN_NODE_COL,1)->where(cn::NODES_STATUS_COL,'active')->get();
            $nodeWeaknessList = array();
            $nodeWeaknessChList = array();
            $nodeMainIdList = array();
            if (!empty($nodeList)) { 
                $nodeListToArray=$nodeList->toArray();
                $nodeWeaknessList = array_column($nodeListToArray,'weakness_name_en','id');
                $nodeWeaknessChList = array_column($nodeListToArray,'weakness_name_ch','id');
                $nodeMainIdList = array_column($nodeListToArray,'id','id');
            }
            $NodesParent = new Nodes;
            $NodesList = $NodesParent->get_nodelist();
            

            $QuestionData = Question::with('answers')
                            ->where(cn::QUESTION_TABLE_NAME.'.'.cn::QUESTION_TABLE_ID_COL,$id)
                            ->first();
        
            if(isset($QuestionData)){
                $question_type_ids = explode(',',$QuestionData->question_type);
            }

            return view('backend.question.copy_create',compact('QuestionData','question_type_ids','nodeWeaknessList','mainnodeList','nodeMainIdList','NodesList','nodeWeaknessChList'));
           
        } catch (\Exception $exception) {
            return back()->withError($exception->getMessage())->withInput();
        }
    }

    /**
     * USE : Update question details
     */
    public function update(Request $request, $id){
        try{
            if(!in_array('question_bank_update', Helper::getPermissions(Auth::user()->{cn::USERS_ID_COL}))) {
                return  redirect(Helper::redirectRoleBasedDashboard(Auth::user()->{cn::USERS_ID_COL}));
            } 
            $validator = Validator::make($request->all(), Question::rules($request, 'update', $id), Question::rulesMessages('create'));
            if ($validator->fails()) {
                $validatorParams = $this->QuestionsRepository->validatorSendParams($request);
                $strands = isset($validatorParams['strands']) ? $validatorParams['strands'] : [];
                $LearningUnits = isset($validatorParams['LearningUnits']) ? $validatorParams['LearningUnits'] : [];
                $subjects = isset($validatorParams['subjects']) ? $validatorParams['subjects'] : [];
                $LearningObjectives = isset($validatorParams['LearningObjectives']) ? $validatorParams['LearningObjectives'] : [];
                return back()->withErrors($validator)->withInput()->with(['subjects' => $subjects,'strands' => $strands,'LearningUnits'=>$LearningUnits,'LearningObjectives'=>$LearningObjectives]);
            }
            // Update record question answer
            //$result = $this->QuestionsRepository->UpdateQuestionAnswer($request, $id);

            // $objectivesMapping = $this->objectivesMapping($request);
            // if(isset($objectivesMapping) && !empty($objectivesMapping->id)){
            //     $objectivesMapping = $objectivesMapping->id;
            // }

            // Get maaping id by question code
            $AddParamsRequest = ['question_code' => str_replace(" ","",$request->naming_structure_code)];
            $request->merge($AddParamsRequest);
            $objectivesMapping = $this->GetStrandUnitsObjectivesMappingsId($request->question_code);
            if(($objectivesMapping['StrandUnitsObjectivesMappingsId'] == 0 || empty($objectivesMapping['e']) || empty($objectivesMapping['f']) || empty($objectivesMapping['g']) || empty($objectivesMapping['question_type']) || empty($objectivesMapping['dificulaty_level']))){
                return back()->withInput()->with('error_msg', __('languages.invalid_question_code_please_try_using_valid_question_code'));
            }
            // Store Question post data
            $questionPostData = array(
                cn::QUESTION_OBJECTIVE_MAPPING_ID_COL       => $objectivesMapping['StrandUnitsObjectivesMappingsId'] ?? 0,
                cn::QUESTION_QUESTION_CODE_COL              => str_replace(" ","",$request->naming_structure_code),
                cn::QUESTION_NAMING_STRUCTURE_CODE_COL      => str_replace(" ","",$request->naming_structure_code),
                cn::QUESTION_MARKS_COL                      => 1,
                cn::QUESTION_BANK_UPDATED_BY_COL            => Auth::user()->{cn::USERS_ID_COL},
                cn::QUESTION_BANK_SCHOOL_ID_COL             => 1,
                cn::QUESTION_QUESTION_EN_COL                => $request->question_en,
                cn::QUESTION_QUESTION_CH_COL                => $request->question_ch,
                cn::QUESTION_DIFFICULTY_LEVEL_COL           => $objectivesMapping['dificulaty_level'],
                cn::QUESTINON_STATUS_COL                    => ($request->save_draft) ? 0 : 1,
                cn::QUESTION_QUESTION_TYPE_COL              => $objectivesMapping['question_type'],
                cn::QUESTION_GENERAL_HINTS_EN               => $request->general_hints_en ?? null,
                cn::QUESTION_GENERAL_HINTS_CH               => $request->general_hints_ch ?? null,
                cn::QUESTION_GENERAL_HINTS_VIDEO_ID_EN      => ($request->question_video_id_en) ? $request->question_video_id_en : null,
                cn::QUESTION_GENERAL_HINTS_VIDEO_ID_CH      => ($request->question_video_id_ch) ? $request->question_video_id_ch : null,
                cn::QUESTION_FULL_SOLUTION_EN               => $request->full_solution_en ?? null,
                cn::QUESTION_FULL_SOLUTION_CH               => $request->full_solution_ch ?? null,
                cn::QUESTION_E_COL                          => $objectivesMapping['e'],
                cn::QUESTION_F_COL                          => $objectivesMapping['f'],
                cn::QUESTION_G_COL                          => $objectivesMapping['g'],
                cn::QUESTION_IS_APPROVED_COL                => $request->is_approved
            );
            $question = Question::whereId($id)->update($questionPostData);
            if($question){
                $UpdateAnswerData = array(
                    cn::ANSWER_ANSWER1_EN_COL            => $request->answer1_en,
                    cn::ANSWER_HINT_ANSWER1_EN_COL       => $request->hint_answer1_en,
                    cn::ANSWER_NODE_HINT_ANSWER1_EN_COL  => $request->node_hint_answer1_en, 
                    cn::ANSWER1_NODE_RELATION_ID_EN_COL => $request->answer1_node_relation_id_en,

                    cn::ANSWER_ANSWER2_EN_COL            => $request->answer2_en,
                    cn::ANSWER_HINT_ANSWER2_EN_COL       => $request->hint_answer2_en,
                    cn::ANSWER_NODE_HINT_ANSWER2_EN_COL  => $request->node_hint_answer2_en,
                    cn::ANSWER2_NODE_RELATION_ID_EN_COL => $request->answer2_node_relation_id_en,

                    cn::ANSWER_ANSWER3_EN_COL            => $request->answer3_en,
                    cn::ANSWER_HINT_ANSWER3_EN_COL       => $request->hint_answer3_en,
                    cn::ANSWER_NODE_HINT_ANSWER3_EN_COL => $request->node_hint_answer3_en,
                    cn::ANSWER3_NODE_RELATION_ID_EN_COL => $request->answer3_node_relation_id_en,

                    cn::ANSWER_ANSWER4_EN_COL            => $request->answer4_en,
                    cn::ANSWER_HINT_ANSWER4_EN_COL       => $request->hint_answer4_en,
                    cn::ANSWER_NODE_HINT_ANSWER4_EN_COL => $request->node_hint_answer4_en,
                    cn::ANSWER4_NODE_RELATION_ID_EN_COL => $request->answer4_node_relation_id_en,

                    cn::ANSWER_ANSWER1_CH_COL            => $request->answer1_ch,
                    cn::ANSWER_HINT_ANSWER1_CH_COL       => $request->hint_answer1_ch,
                    cn::ANSWER_NODE_HINT_ANSWER1_CH_COL => $request->node_hint_answer1_ch,
                    cn::ANSWER1_NODE_RELATION_ID_CH_COL => $request->answer1_node_relation_id_en,

                    cn::ANSWER_ANSWER2_CH_COL            => $request->answer2_ch,
                    cn::ANSWER_HINT_ANSWER2_CH_COL       => $request->hint_answer2_ch,
                    cn::ANSWER_NODE_HINT_ANSWER2_CH_COL => $request->node_hint_answer2_ch,
                    cn::ANSWER2_NODE_RELATION_ID_CH_COL => $request->answer2_node_relation_id_en,

                    cn::ANSWER_ANSWER3_CH_COL            => $request->answer3_ch,
                    cn::ANSWER_HINT_ANSWER3_CH_COL       => $request->hint_answer3_ch,
                    cn::ANSWER_NODE_HINT_ANSWER3_CH_COL => $request->node_hint_answer3_ch,
                    cn::ANSWER3_NODE_RELATION_ID_CH_COL => $request->answer3_node_relation_id_en,

                    cn::ANSWER_ANSWER4_CH_COL            => $request->answer4_ch,
                    cn::ANSWER_HINT_ANSWER4_CH_COL       => $request->hint_answer4_ch,
                    cn::ANSWER_NODE_HINT_ANSWER4_CH_COL => $request->node_hint_answer4_ch,
                    cn::ANSWER4_NODE_RELATION_ID_CH_COL => $request->answer4_node_relation_id_en,

                    cn::ANSWER_CORRECT_ANSWER_EN_COL     => $request->correct_answer_en,
                    cn::ANSWER_CORRECT_ANSWER_CH_COL     => $request->correct_answer_ch
                );
                $result = Answer::where(cn::ANSWER_QUESTION_ID_COL,$id)->update($UpdateAnswerData);
                if($result){
                    return redirect('questions')->with('success_msg', __('languages.question_updated_successfully'));
                }else{
                    return back()->withInput()->with('error_msg', __('languages.problem_was_occur_please_try_again'));
                }
            }else{
                return back()->withInput()->with('error_msg', __('languages.problem_was_occur_please_try_again'));
            }
        } catch (\Exception $exception) {
            return back()->withError($exception->getMessage())->withInput();
        }
    }

    /**
     * USE : Delete Questions & Answers
     */
    public function destroy($id){
        try{
            if(!in_array('question_bank_delete', Helper::getPermissions(Auth::user()->{cn::USERS_ID_COL}))) {
                return  redirect(Helper::redirectRoleBasedDashboard(Auth::user()->{cn::USERS_ID_COL}));
            } 

            $DeleteAnswer = Answer::where(cn::ANSWER_QUESTION_ID_COL,$id)->delete();
            if($DeleteAnswer){
                $Question = Question::find($id);
                $Question->delete();
                return $this->sendResponse([], __('languages.question_deleted_successfully'));
            }else{
                return $this->sendError(__('languages.please_try_again'), 422);
            }
        }catch (\Exception $exception) {
            return $this->sendError($exception->getMessage(), 404);
        }
    }

    /**
     * USE : Get School Node using id
     */

    public function getSchoolNodes(Request $request){
        try{
            $id = $request->nodeid ;
            if (isset($id)){
                $nodeData = Nodes::find($id);
                return $nodeData ;
            }
            return '';
        }catch (\Exception $exception) {
            return back()->withError($exception->getMessage())->withInput();
        }
    }

    public function getSubNodes(Request $request){
        try{
            $id = $request->nodeid ;
            if (isset($id)){
                $Nodes = new Nodes;
                $NodesList = $Nodes->getChildNodeList('',$id);
                return $NodesList ;
            }
            return '';
        }catch (\Exception $exception) {
            return back()->withError($exception->getMessage())->withInput();
        }
    }

    /**
     * USE : Get video List on question hints popup model
     */
    public function getVideos(Request $request){
        $result = [];
        $nodeId = '';
        $getVideos = '';
        $selectedVideoId = 0;
        $filterFileName = '';

        if(isset($request->selectedVideoId) && !empty($request->selectedVideoId)){
            $selectedVideoId =  $request->selectedVideoId;
        }
        $language_code='en';
        if(isset($request->language) && !empty($request->language))
        {
            $language_code=$request->language;
        }
        $language=Languages::where(cn::LANGUAGES_CODE_COL,$language_code)->get()->toArray();
        if(!empty($request->question_code)){
            $partial_questionCode = explode('-',$request->question_code);
            $nodeId = $partial_questionCode[0].'-'.$partial_questionCode[1].'-'.$partial_questionCode[2].'-'.$partial_questionCode[3];
            $getNodeData = Nodes::where(cn::NODES_NODEID_COL,$nodeId)->first();
        }
        if(isset($getNodeData) && !empty($getNodeData)){
            $Query = MainUploadDocument::with('documentData')->where(cn::MAIN_UPLOAD_DOCUMENT_NODE_ID_COL,$getNodeData->id)->where(cn::MAIN_UPLOAD_DOCUMENT_LANGUAGE_ID,$language[0][cn::LANGUAGES_ID_COL]);
            if(!empty($request->filename)){
                $filterFileName = $request->filename;
                $Query->where(cn::MAIN_UPLOAD_DOCUMENT_FILE_NAME_COL,$request->filename);            
            }
            $getMainUploadDataBasedonNodeData = $Query->pluck(cn::MAIN_UPLOAD_DOCUMENT_ID_COL);
            if(!empty($getMainUploadDataBasedonNodeData)){
                $VideoList = UploadDocuments::whereIn(cn::UPLOAD_DOCUMENTS_FILE_TYPE_COL,$this->getDocumentType('video'))->whereIn(cn::UPLOAD_DOCUMENTS_DOCUMENT_MAPPING_ID,$getMainUploadDataBasedonNodeData)->get();
                if($VideoList->IsNotEmpty()){
                    $result['html'] = (string)View::make('backend.question.video_hint',compact('VideoList','selectedVideoId','filterFileName','language_code'));
                }
            }
        }
        return $this->sendResponse($result);
    }

    /**
     * USE : Get student create self learning test questions
     */
    public function getQuestionsStudentSelfLearningTest(Request $request){
        try{
            $response = [];
            // Get the current student grade
            $gradeId = Auth::user()->grade_id;
            $StrandUnitsObjectivesMappings = StrandUnitsObjectivesMappings::Query();
            if(isset($gradeId) && !empty($gradeId)){
                $StrandUnitsObjectivesMappings->where(cn::OBJECTIVES_MAPPINGS_GRADE_ID_COL,$gradeId);
            }
            if(isset($request->strand_id) && !empty($request->strand_id)){
                $StrandUnitsObjectivesMappings->whereIn(cn::OBJECTIVES_MAPPINGS_STRAND_ID_COL,$request->strand_id);
            }
            if(isset($request->learning_unit_id) && !empty($request->learning_unit_id)){
                $StrandUnitsObjectivesMappings->whereIn(cn::OBJECTIVES_MAPPINGS_LEARNING_UNIT_ID_COL,$request->learning_unit_id);
            }
            if(isset($request->learning_objectives_id) && !empty($request->learning_objectives_id)){
                $StrandUnitsObjectivesMappings->whereIn(cn::OBJECTIVES_MAPPINGS_LEARNING_OBJECTIVES_ID_COL,$request->learning_objectives_id);
            }
            if($request->difficulty_mode == 'manual'){
                $difficulty_lvl = $request->difficulty_lvl;
                $selected_levels = array();
                foreach ($difficulty_lvl as $difficulty_value) {
                    //$selected_levels[] = ($difficulty_value - 1);
                    $selected_levels[] = ($difficulty_value);
                }
                rsort($difficulty_lvl);
            }
            
            $objective_mapping_id = $StrandUnitsObjectivesMappings->pluck(cn::OBJECTIVES_MAPPINGS_ID_COL)->toArray();
            $no_of_questions_per_learning_skills = $this->getGlobalConfiguration('no_of_questions_per_learning_skills');
            if(empty($no_of_questions_per_learning_skills)){
                $no_of_questions_per_learning_skills = 2;
            }

            if($request->self_learning_test_type==1){
                $QuestionType = array(2,3);
            }else{
                $QuestionType = array(1);
            }

            $no_of_questions = $request->no_of_questions;
            if(!empty($objective_mapping_id)){
                // $questionId_data_list = Question::where(cn::QUESTION_QUESTION_TYPE_COL,1) // 1 = Self-learning questions
                //                         ->whereIn(cn::QUESTION_OBJECTIVE_MAPPING_ID_COL,$objective_mapping_id)
                //                         ->groupBy(cn::QUESTION_QUESTION_CODE_COL)
                //                         ->pluck(cn::QUESTION_QUESTION_CODE_COL)
                //                         ->toArray();
                
                $questionId_data_list = Question::whereIn(cn::QUESTION_QUESTION_TYPE_COL,$QuestionType) // 1 = Self-learning questions
                                        ->whereIn(cn::QUESTION_OBJECTIVE_MAPPING_ID_COL,$objective_mapping_id)
                                        ->groupBy(cn::QUESTION_QUESTION_CODE_COL)
                                        ->pluck(cn::QUESTION_QUESTION_CODE_COL)
                                        ->toArray();

                $question_code_skills = array();
                foreach($questionId_data_list as $question_code_s){
                    $question_code_exp = explode('-', $question_code_s);
                    $question_code_skills[] = $question_code_exp[0].'-'.$question_code_exp[1].'-'.$question_code_exp[2].'-'.substr($question_code_exp[3],0,2);
                }
                if(isset($question_code_skills) && !empty($question_code_skills)){
                    $question_code_skills = array_unique($question_code_skills);
                    $question_code_skills = array_values($question_code_skills);
                }else{
                    return $this->sendError(__('languages.questions-not-found'), 422);
                }
                $qLoop = 0;
                $question_id_list = '';
                $coded_questions_list = array();
                while($qLoop <= $no_of_questions){
                    foreach($question_code_skills as $question_code){
                        $questionId_data_list = Question::with('PreConfigurationDifficultyLevel')
                                                //->where(cn::QUESTION_QUESTION_TYPE_COL,1) // 1 = Self-learning questions
                                                ->whereIn(cn::QUESTION_QUESTION_TYPE_COL,$QuestionType)
                                                ->where(cn::QUESTION_QUESTION_CODE_COL,'like',$question_code.'%')
                                                ->where(function ($query) use ($question_id_list){
                                                    if($question_id_list != ""){
                                                        $question_id_list_array = explode(',', $question_id_list);
                                                        $query->whereNotIn(cn::QUESTION_TABLE_ID_COL,$question_id_list_array);
                                                    }
                                                })
                                                ->limit($no_of_questions_per_learning_skills);
                        $questionId_list = $questionId_data_list->pluck(cn::QUESTION_TABLE_ID_COL)->toArray();
                        if(isset($questionId_list) && !empty($questionId_list)){
                            if($question_id_list != ""){
                                $question_id_list.= ','.implode(',', $questionId_list);
                            }else{
                                $question_id_list.= implode(',', $questionId_list);
                            }
                        }
                    }
                    $qLoop++;
                }

                if(isset($question_id_list) && !empty(sizeof(explode(',',$question_id_list)))){
                    $questionIds = explode(',',$question_id_list);
                    $question_data = Question::with('PreConfigurationDifficultyLevel')
                                        //->where(cn::QUESTION_QUESTION_TYPE_COL,1) // 1 = Self-learning questions
                                        ->whereIn(cn::QUESTION_QUESTION_TYPE_COL,$QuestionType)
                                        ->whereIn('id',$questionIds)
                                        ->get()->toArray();
                    if(isset($question_data) && !empty($question_data)){
                        foreach($question_data as $question_value){
                            $coded_questions_list[] = array($question_value[cn::QUESTION_NAMING_STRUCTURE_CODE_COL],floatval($question_value['pre_configuration_difficulty_level']['title']),0);
                        }
                    }
                }
                if($question_id_list != "" && sizeof(explode(',',$question_id_list))){
                    // Call to ALP AI My School Ability Analysis Graph API
                    if(isset($coded_questions_list) && !empty($coded_questions_list)){
                        $requestPayload = new \Illuminate\Http\Request();
                        // call api based on selected mode for aiapi
                        switch($request->difficulty_mode){
                            case 'manual':
                                        $requestPayload = $requestPayload->replace([
                                            'selected_levels'       => array_map('floatval', array_unique($selected_levels)),
                                            'coded_questions_list'  => $coded_questions_list,
                                            'k'                     => floatval($no_of_questions),
                                            'repeated_rate'         => floatval($this->getGlobalConfiguration('repeated_rate')) ?? 0.1
                                        ]);
                                        $response = $this->AIApiService->Assign_Questions_Manually($requestPayload);
                                break;
                            case 'auto':
                                        // Current student get overall abilities
                                        $studentAbilities = Auth::user()->{cn::USERS_OVERALL_ABILITY_COL} ?? $this->DefaultStudentOverAllAbility;                                        
                                        $requestPayload = $requestPayload->replace([
                                            'students_abilities_list'   => array(floatval($studentAbilities)),
                                            'coded_questions_list'      => $coded_questions_list,
                                            'k'                         => floatval($no_of_questions),
                                            'n'                         => floatval($this->getGlobalConfiguration('question_generator_n')) ?? 50,
                                            'repeated_rate'             => floatval($this->getGlobalConfiguration('repeated_rate')) ?? 0.1
                                        ]);
                                        $response = $this->AIApiService->Assign_Questions_AutoMode($requestPayload);
                                break;
                        }
                        if(isset($response) && !empty($response)){
                            $responseQuestionCodes = array_column($response[0],0); // Array[0] = assigning questions for the self learning
                            $question_id_list = Question::whereIn(cn::QUESTION_NAMING_STRUCTURE_CODE_COL,$responseQuestionCodes)->limit($no_of_questions)->pluck(cn::QUESTION_TABLE_ID_COL)->toArray();
                            if(isset($question_id_list) && !empty($question_id_list)){
                                $questionId_data_list = implode(',',array_unique($question_id_list));
                                $request = array_merge($request->all(), ['questionIds' => $questionId_data_list]);
                                $response = $this->StudentController->selfExamCreate($request);
                                if(isset($response) && !empty($response)){
                                    return $this->sendResponse($response);
                                }else{
                                    return $this->sendError(__('languages.problem_was_occur_please_try_again'), 422);
                                }
                            }else{
                                return $this->sendError(__('languages.questions-not-found'), 422);
                            }
                        }else{
                            return $this->sendError(__('languages.problem_was_occur_please_try_again'), 422);
                        }
                    }else{
                        return $this->sendError(__('languages.problem_was_occur_please_try_again'), 422);
                    }
                }else{
                    return $this->sendError(__('languages.questions-not-found'), 422);
                }
            }else{
                return $this->sendError(__('languages.questions-not-found'), 422);
            }
        }catch (\Exception $ex) {
            return $this->sendError($ex->getMessage(), 404);
        }
    }

    /**
     * USE : Get All Questions Assign In Exam.
     */
    public function getAllAssignQuestions(Request $request){
       $difficultyLevels = PreConfigurationDiffiltyLevel::all();
       $examId = $request->exam_id;
       $result['html'] = '';
       $Questions = [];
       $ExamData = Exam::find($examId);
       if(!empty($ExamData)){
           if(!empty($ExamData->question_ids)){
               $questionsids = explode(',',$ExamData->question_ids);
               $Questions = Question::with(['answers','PreConfigurationDifficultyLevel'])->whereIn(cn::QUESTION_TABLE_ID_COL,$questionsids)->get();
           }
       }
       
       if(!empty($Questions)){
            foreach($Questions as $questionKey => $question){
                $difficultValue = [
                    'natural_difficulty' => $question->PreConfigurationDifficultyLevel->title ?? '',
                    'normalized_difficulty' => $this->getNormalizedAbility($question->PreConfigurationDifficultyLevel->title)
                ];
                $Questions[$questionKey]['difficultyValue'] = $difficultValue ?? [];
            }
            $result['html'] = (string)View::make('backend.question.question_list_preview',compact('Questions','difficultyLevels'));
       } 
       return $this->sendResponse($result);
    }

    /**
     * USE : Get Question In Exam.
     */
    public function questionPreview(Request $request){
        try{
            $response = [];
            $result['html'] = '';            
            $AddParamsRequest = ['question_code' => str_replace(" ","",$request->naming_structure_code)];
            $request->merge($AddParamsRequest);
            if(isset($request->question_code) && $request->question_code == ""){
                return $this->sendError(__('languages.invalid_question_code_please_try_using_valid_question_code'), 422);
            }
            $questionPostData = array(
                cn::QUESTION_QUESTION_CODE_COL                => str_replace(" ","",$request->naming_structure_code),
                cn::QUESTION_QUESTION_EN_COL                => $request->question_en,
                cn::QUESTION_QUESTION_CH_COL                => $request->question_ch,
                cn::QUESTION_GENERAL_HINTS_EN               => $request->general_hints_en ?? null,
                cn::QUESTION_GENERAL_HINTS_CH               => $request->general_hints_ch ?? null,
                cn::QUESTION_GENERAL_HINTS_VIDEO_ID_EN      => ($request->question_video_id_en) ? $request->question_video_id_en : null,
                cn::QUESTION_GENERAL_HINTS_VIDEO_ID_CH      => ($request->question_video_id_ch) ? $request->question_video_id_ch : null,
            );
            $questionPostData['answers'] = array(
                cn::ANSWER_ANSWER1_EN_COL           => $request->answer1_en,
                cn::ANSWER_ANSWER2_EN_COL           => $request->answer2_en,
                cn::ANSWER_ANSWER3_EN_COL           => $request->answer3_en,
                cn::ANSWER_ANSWER4_EN_COL           => $request->answer4_en,
                cn::ANSWER_ANSWER1_CH_COL           => $request->answer1_ch,
                cn::ANSWER_ANSWER2_CH_COL           => $request->answer2_ch,
                cn::ANSWER_ANSWER3_CH_COL           => $request->answer3_ch,
                cn::ANSWER_ANSWER4_CH_COL           => $request->answer4_ch,
            );
            
            $QuestionData=json_decode(json_encode($questionPostData));            
            $examLanguage='en';
            $question = $QuestionData;
            $UploadDocumentsData = array();
            if($examLanguage == 'en'){
                if(isset($QuestionData->{cn::QUESTION_GENERAL_HINTS_VIDEO_ID_EN}) && $QuestionData->{cn::QUESTION_GENERAL_HINTS_VIDEO_ID_EN}!=""){
                    $UploadDocumentsData = UploadDocuments::find($QuestionData->{cn::QUESTION_GENERAL_HINTS_VIDEO_ID_EN});
                }else{
                    $arrayOfQuestion = explode('-',$QuestionData->{cn::QUESTION_QUESTION_CODE_COL});
                    if(count($arrayOfQuestion) == 8){
                        unset($arrayOfQuestion[count($arrayOfQuestion)-1]);
                        $newQuestionCode = implode('-',$arrayOfQuestion);
                        $newQuestionData = Question::where(cn::QUESTION_QUESTION_CODE_COL,$newQuestionCode)->first();
                        if(isset($newQuestionData->{cn::QUESTION_GENERAL_HINTS_VIDEO_ID_EN}) && !empty($newQuestionData->{cn::QUESTION_GENERAL_HINTS_VIDEO_ID_EN})){
                            $UploadDocumentsData = UploadDocuments::find($newQuestionData->{cn::QUESTION_GENERAL_HINTS_VIDEO_ID_EN});
                        }
                    }
                }
            }else{
                if(isset($QuestionData->{cn::QUESTION_GENERAL_HINTS_VIDEO_ID_CH}) && $QuestionData->{cn::QUESTION_GENERAL_HINTS_VIDEO_ID_CH}!=""){
                    $UploadDocumentsData = UploadDocuments::find($QuestionData->{cn::QUESTION_GENERAL_HINTS_VIDEO_ID_CH});
                }else{
                    $arrayOfQuestion = explode('-',$QuestionData->{cn::QUESTION_QUESTION_CODE_COL});
                    if(count($arrayOfQuestion) == 8){
                        unset($arrayOfQuestion[count($arrayOfQuestion)-1]);
                        $newQuestionCode = implode('-',$arrayOfQuestion);
                        $newQuestionData = Question::where(cn::QUESTION_QUESTION_CODE_COL,$newQuestionCode)->first();
                        if(isset($newQuestionData->{cn::QUESTION_GENERAL_HINTS_VIDEO_ID_CH}) && !empty($newQuestionData->{cn::QUESTION_GENERAL_HINTS_VIDEO_ID_CH})){
                            $UploadDocumentsData = UploadDocuments::find($newQuestionData->{cn::QUESTION_GENERAL_HINTS_VIDEO_ID_CH});
                        }
                    }
                }
            }
            $result['html'] = (string)View::make('backend.question.previewUpdate',compact('question','examLanguage','UploadDocumentsData'));
            return $this->sendResponse($result);
        }catch (\Exception $ex) {
            return $this->sendError($ex->getMessage(), 404);
        }
    }

    /**
    * USE : Get Question In Exam.
    */
    public function getQuestionHint($id){
        try{
            $question = Question::with(['answers','PreConfigurationDifficultyLevel'])->where(cn::QUESTION_TABLE_ID_COL,$id)->first();
            if(isset($question) && !empty($question)){
                $examLanguage = app()->getLocale();
                $UploadDocumentsData = array();
                $QuestionData = $question;
                if($examLanguage == 'en'){
                    if(isset($QuestionData->{cn::QUESTION_GENERAL_HINTS_VIDEO_ID_EN}) && $QuestionData->{cn::QUESTION_GENERAL_HINTS_VIDEO_ID_EN}!=""){
                        $UploadDocumentsData = UploadDocuments::find($QuestionData->{cn::QUESTION_GENERAL_HINTS_VIDEO_ID_EN});
                    }else{
                        $arrayOfQuestion = explode('-',$QuestionData->{cn::QUESTION_QUESTION_CODE_COL});
                        if(count($arrayOfQuestion) == 8){
                            unset($arrayOfQuestion[count($arrayOfQuestion)-1]);
                            $newQuestionCode = implode('-',$arrayOfQuestion);
                            $newQuestionData = Question::where(cn::QUESTION_QUESTION_CODE_COL,$newQuestionCode)->first();
                            if(isset($newQuestionData->{cn::QUESTION_GENERAL_HINTS_VIDEO_ID_EN}) && !empty($newQuestionData->{cn::QUESTION_GENERAL_HINTS_VIDEO_ID_EN})){
                                $UploadDocumentsData = UploadDocuments::find($newQuestionData->{cn::QUESTION_GENERAL_HINTS_VIDEO_ID_EN});
                            }
                        }
                    }
                }else{
                    if(isset($QuestionData->{cn::QUESTION_GENERAL_HINTS_VIDEO_ID_CH}) && $QuestionData->{cn::QUESTION_GENERAL_HINTS_VIDEO_ID_CH}!=""){
                        $UploadDocumentsData = UploadDocuments::find($QuestionData->{cn::QUESTION_GENERAL_HINTS_VIDEO_ID_CH});
                    }else{
                        $arrayOfQuestion = explode('-',$QuestionData->{cn::QUESTION_QUESTION_CODE_COL});
                        if(count($arrayOfQuestion) == 8){
                            unset($arrayOfQuestion[count($arrayOfQuestion)-1]);
                            $newQuestionCode = implode('-',$arrayOfQuestion);
                            $newQuestionData = Question::where(cn::QUESTION_QUESTION_CODE_COL,$newQuestionCode)->first();
                            if(isset($newQuestionData->{cn::QUESTION_GENERAL_HINTS_VIDEO_ID_CH}) && !empty($newQuestionData->{cn::QUESTION_GENERAL_HINTS_VIDEO_ID_CH})){
                                $UploadDocumentsData = UploadDocuments::find($newQuestionData->{cn::QUESTION_GENERAL_HINTS_VIDEO_ID_CH});
                            }
                        }
                    }
                }
                $result['html'] = (string)View::make('backend.question.hint',compact('question','examLanguage','UploadDocumentsData'));
                return $this->sendResponse($result);
            }else{
                return $this->sendError($ex->getMessage(), 404);
            }
        }catch (\Exception $ex) {
            return $this->sendError($ex->getMessage(), 404);
        }
    }

    /**
     * USE : Update Question verification status
     */
    public function updateQuestionVerification(Request $request){
        try{
            $Update =   Question::whereIn('id',$request->QuestionIds)->update([
                            'is_approved' => $request->verification_status
                        ]);
            if($Update){
                return $this->sendResponse([], __('languages.question_verification_updated_successfully'));
            }else{
                return $this->sendError($ex->getMessage(), 404);
            }
        }catch (\Exception $ex) {
            return $this->sendError($ex->getMessage(), 404);
        }
    }
    
    public function questionListPreview(Request $request){
        $response = [];
            $result['html'] = '';    
            $questionData  = Question::with('answers')->find($request->questionId);
            $questionPostData = array(
                cn::QUESTION_QUESTION_CODE_COL                => str_replace(" ","",$questionData->naming_structure_code),
                cn::QUESTION_QUESTION_EN_COL                => $questionData->question_en,
                cn::QUESTION_QUESTION_CH_COL                => $questionData->question_ch,
                cn::QUESTION_GENERAL_HINTS_EN               => $questionData->general_hints_en ?? null,
                cn::QUESTION_GENERAL_HINTS_CH               => $questionData->general_hints_ch ?? null,
                cn::QUESTION_GENERAL_HINTS_VIDEO_ID_EN      => ($questionData->question_video_id_en) ? $questionData->question_video_id_en : null,
                cn::QUESTION_GENERAL_HINTS_VIDEO_ID_CH      => ($questionData->question_video_id_ch) ? $questionData->question_video_id_ch : null,
            );
            $questionPostData['answers'] = array(
                cn::ANSWER_ANSWER1_EN_COL           => $questionData->answers->answer1_en,
                cn::ANSWER_ANSWER2_EN_COL           => $questionData->answers->answer2_en,
                cn::ANSWER_ANSWER3_EN_COL           => $questionData->answers->answer3_en,
                cn::ANSWER_ANSWER4_EN_COL           => $questionData->answers->answer4_en,
                cn::ANSWER_ANSWER1_CH_COL           => $questionData->answers->answer1_ch,
                cn::ANSWER_ANSWER2_CH_COL           => $questionData->answers->answer2_ch,
                cn::ANSWER_ANSWER3_CH_COL           => $questionData->answers->answer3_ch,
                cn::ANSWER_ANSWER4_CH_COL           => $questionData->answers->answer4_ch,
            );
             $QuestionData=json_decode(json_encode($questionPostData));            
             $examLanguage='en';
             $question = $QuestionData;
            $UploadDocumentsData = array();
            if($examLanguage == 'en'){
                if(isset($QuestionData->{cn::QUESTION_GENERAL_HINTS_VIDEO_ID_EN}) && $QuestionData->{cn::QUESTION_GENERAL_HINTS_VIDEO_ID_EN}!=""){
                    $UploadDocumentsData = UploadDocuments::find($QuestionData->{cn::QUESTION_GENERAL_HINTS_VIDEO_ID_EN});
                }else{
                    $arrayOfQuestion = explode('-',$QuestionData->{cn::QUESTION_QUESTION_CODE_COL});
                    if(count($arrayOfQuestion) == 8){
                        unset($arrayOfQuestion[count($arrayOfQuestion)-1]);
                        $newQuestionCode = implode('-',$arrayOfQuestion);
                        $newQuestionData = Question::where(cn::QUESTION_QUESTION_CODE_COL,$newQuestionCode)->first();
                        if(isset($newQuestionData->{cn::QUESTION_GENERAL_HINTS_VIDEO_ID_EN}) && !empty($newQuestionData->{cn::QUESTION_GENERAL_HINTS_VIDEO_ID_EN})){
                            $UploadDocumentsData = UploadDocuments::find($newQuestionData->{cn::QUESTION_GENERAL_HINTS_VIDEO_ID_EN});
                        }
                    }
                }
            }else{
                if(isset($QuestionData->{cn::QUESTION_GENERAL_HINTS_VIDEO_ID_CH}) && $QuestionData->{cn::QUESTION_GENERAL_HINTS_VIDEO_ID_CH}!=""){
                    $UploadDocumentsData = UploadDocuments::find($QuestionData->{cn::QUESTION_GENERAL_HINTS_VIDEO_ID_CH});
                }else{
                    $arrayOfQuestion = explode('-',$QuestionData->{cn::QUESTION_QUESTION_CODE_COL});
                    if(count($arrayOfQuestion) == 8){
                        unset($arrayOfQuestion[count($arrayOfQuestion)-1]);
                        $newQuestionCode = implode('-',$arrayOfQuestion);
                        $newQuestionData = Question::where(cn::QUESTION_QUESTION_CODE_COL,$newQuestionCode)->first();
                        if(isset($newQuestionData->{cn::QUESTION_GENERAL_HINTS_VIDEO_ID_CH}) && !empty($newQuestionData->{cn::QUESTION_GENERAL_HINTS_VIDEO_ID_CH})){
                            $UploadDocumentsData = UploadDocuments::find($newQuestionData->{cn::QUESTION_GENERAL_HINTS_VIDEO_ID_CH});
                        }
                    }
                }
            }
            $result['html'] = (string)View::make('backend.question.previewUpdate',compact('question','examLanguage','UploadDocumentsData'));
            return $this->sendResponse($result);
    }
}