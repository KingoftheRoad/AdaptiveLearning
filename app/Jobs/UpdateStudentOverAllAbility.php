<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Strands;
use App\Models\LearningsUnits;
use App\Models\LearningsObjectives;
use App\Models\StrandUnitsObjectivesMappings;
use App\Models\PreConfigurationDiffiltyLevel;
use App\Constants\DbConstant As cn;
use Log;
use App\Http\Services\AIApiService;
use App\Helpers\Helper;
use App\Traits\ResponseFormat;
use App\Traits\Common;
use App\Models\User;
use App\Models\Exam;
use App\Models\Question;
use DB;
use Illuminate\Support\Facades\Auth;

class UpdateStudentOverAllAbility implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, common, ResponseFormat;

    protected $Student;
    protected $AIApiService;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(User $Student)
    {
        $this->Student = $Student;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $stud_id = $this->Student->id;
        Log::info('Student Update Overall Ability job start :');
        $currentLang = ucwords(app()->getLocale());
        $strandData = Strands::all();
        $strandDataLbl = Strands::pluck('name_'.app()->getLocale(),cn::STRANDS_ID_COL)->toArray();
        $learningReportStrand = Strands::pluck(cn::STRANDS_ID_COL)->toArray();
        $reportDataArray = array();
        $reportDataAbilityArray = array();
        $LearningsUnitsLbl = array();
        $LearningsObjectivesLbl = array();
        $PreConfigurationDifficultyLevel = array();
        $PreConfigurationDiffiltyLevelData = PreConfigurationDiffiltyLevel::get()->toArray();
        // PreConfigurationDiffiltyLevel::where(cn::PRE_CONFIGURE_DIFFICULTY_CURRICULUM_YEAR_ID_COL,$this->GetCurriculumYear())->get()->toArray();
        if(isset($PreConfigurationDiffiltyLevelData)){
            $PreConfigurationDifficultyLevel = array_column($PreConfigurationDiffiltyLevelData,cn::PRE_CONFIGURE_DIFFICULTY_TITLE_COL,cn::PRE_CONFIGURE_DIFFICULTY_DIFFICULTY_LEVEL_COL);
        }
        $reportLearningType = "";
        $LearningsUnitsLbl = LearningsUnits::pluck('name_'.app()->getLocale(),cn::LEARNING_UNITS_ID_COL)->toArray();
        foreach ($learningReportStrand as $strandId){
            $learningUnitsIds = LearningsUnits::where(cn::LEARNING_UNITS_STRANDID_COL, $strandId)->pluck(cn::LEARNING_UNITS_ID_COL)->toArray();
            if(!empty($learningUnitsIds)){
                foreach($learningUnitsIds as $learningUnitsId){
                    $learningObjectivesIds = LearningsObjectives::IsAvailableQuestion()->where(cn::LEARNING_OBJECTIVES_LEARNING_UNITID_COL, $learningUnitsId)->pluck(cn::LEARNING_OBJECTIVES_ID_COL)->toArray();
                    $LearningsObjectivesLbl = LearningsObjectives::IsAvailableQuestion()->where(cn::LEARNING_OBJECTIVES_LEARNING_UNITID_COL, $learningUnitsId)->pluck('title_'.app()->getLocale(),cn::LEARNING_OBJECTIVES_ID_COL)->toArray();
                    if(!empty($learningObjectivesIds)){
                        $no_of_learning_objectives = count($learningObjectivesIds);
                        $reportDataArray[$strandId][$learningUnitsId]['no_of_learning_objectives'] = count($learningObjectivesIds);
                        $learningObjectivesExamcheck = 0;
                        $noOfPassedLearningObjectives = 0;
                        foreach($learningObjectivesIds as $learningObjectivesId){
                            $accuracyAll = 0;
                            $abilityAll = 0;
                            $learningObjectivesData = LearningsObjectives::find($learningObjectivesId);
                            $StrandUnitsObjectivesMappingsId = StrandUnitsObjectivesMappings::where(cn::OBJECTIVES_MAPPINGS_STRAND_ID_COL,$strandId)
                                                                ->where(cn::OBJECTIVES_MAPPINGS_LEARNING_UNIT_ID_COL,$learningUnitsId)
                                                                ->where(cn::OBJECTIVES_MAPPINGS_LEARNING_OBJECTIVES_ID_COL,$learningObjectivesId)
                                                                ->pluck(cn::OBJECTIVES_MAPPINGS_ID_COL)->toArray();
                            if(isset($StrandUnitsObjectivesMappingsId) && !empty($StrandUnitsObjectivesMappingsId)){
                                $QuestionsList = Question::with('answers')->whereIn(cn::QUESTION_OBJECTIVE_MAPPING_ID_COL,$StrandUnitsObjectivesMappingsId)
                                                    ->orderBy(cn::QUESTION_TABLE_ID_COL)->get()->toArray();
                                if(isset($QuestionsList) && !empty($QuestionsList)){
                                    $QuestionsDataList = array_column($QuestionsList,cn::QUESTION_TABLE_ID_COL);
                                    $ExamList = Exam::with(['attempt_exams' => fn($query) => $query->where('student_id', $stud_id)])
                                                    ->whereHas('attempt_exams', function($q) use($stud_id){
                                                        $q->where('student_id', '=', $stud_id);
                                                    })
                                                    ->where(function ($query) use ($reportLearningType){
                                                        if(empty($reportLearningType)){
                                                            $query->where(cn::EXAM_TYPE_COLS,3)
                                                            ->orWhere(function ($q1) {
                                                                $q1->where(cn::EXAM_TABLE_SELF_LEARNING_TEST_TYPE_COL,2)->where(cn::EXAM_TYPE_COLS,1);
                                                            });
                                                        }
                                                    })
                                                    ->get()->toArray();                                    
                                    if(isset($ExamList) && !empty($ExamList)){
                                        $accuracyData = 0;
                                        $abilityData = 0;
                                        foreach($ExamList as $ExamData){                                            
                                            $accuracy = \App\Helpers\Helper::getAccuracy($ExamData[cn::EXAM_TABLE_ID_COLS], $stud_id);
                                            $accuracyData = ($accuracyData + $accuracy);
                                            if(isset($ExamData['attempt_exams'][0][cn::ATTEMPT_EXAMS_QUESTION_ANSWER_COL])){
                                                if(isset($ExamData['attempt_exams'][0][cn::ATTEMPT_EXAMS_QUESTION_ANSWER_COL])){
                                                    $filterattempQuestionAnswer = json_decode($ExamData['attempt_exams'][0][cn::ATTEMPT_EXAMS_QUESTION_ANSWER_COL],true);
                                                }
                                            }
                                            $exmdata = array();
                                            foreach($filterattempQuestionAnswer as $filterattempQuestionAnswerkey => $filterattempQuestionAnswervalue){
                                                if(in_array($filterattempQuestionAnswervalue['question_id'],$QuestionsDataList)){
                                                    $QuestionsDataListFinal[] = $filterattempQuestionAnswervalue['question_id'];
                                                    $QuestionList = Question::with('answers')->where(cn::QUESTION_TABLE_ID_COL,$filterattempQuestionAnswervalue['question_id'])->get()->toArray();
                                                    if(isset($PreConfigurationDifficultyLevel) && !empty($PreConfigurationDifficultyLevel) && isset($PreConfigurationDifficultyLevel[$QuestionList[0][cn::QUESTION_DIFFICULTY_LEVEL_COL]]) && !empty($PreConfigurationDifficultyLevel[$QuestionList[0][cn::QUESTION_DIFFICULTY_LEVEL_COL]])){
                                                        $exmdata['difficulty_list'][] = number_format($PreConfigurationDifficultyLevel[$QuestionList[0][cn::QUESTION_DIFFICULTY_LEVEL_COL]], 4, '.', '');
                                                    }else{
                                                        $exmdata['difficulty_list'][] = 0;
                                                    }
                                                    $anscount = 0;
                                                    for($ans = 1; $ans <= 4; $ans++){
                                                        if(trim($QuestionList[0]['answers']['answer'.$ans.'_en']) != ""){
                                                            $anscount++;
                                                        }
                                                    }
                                                    $exmdata['num_of_ans_list'][] = $anscount;
                                                    if($filterattempQuestionAnswervalue['answer'] == $QuestionList[0]['answers']['correct_answer_'.$ExamData['attempt_exams'][0]['language']]){
                                                        $exmdata['questions_results'][] = true;
                                                    }else{
                                                        $exmdata['questions_results'][] = false;
                                                    }
                                                }
                                            }

                                            $ability = 0;
                                            if(isset($exmdata) && !empty($exmdata)){
                                                $requestPayload = new \Illuminate\Http\Request();
                                                $requestPayload = $requestPayload->replace([
                                                    'questions_results'=> array(
                                                        $exmdata['questions_results']
                                                    ),
                                                    'num_of_ans_list' => $exmdata['num_of_ans_list'],
                                                    'difficulty_list' => array_map('floatval', $exmdata['difficulty_list']),
                                                    'max_student_num' => 1
                                                ]);
                                                $AIApiService = new AIApiService;
                                                $data = $AIApiService->getStudentProgressReport($requestPayload);
                                                if(isset($data) && !empty($data) && isset($data[0]) && !empty($data[0])){
                                                    $ability = $data[0];
                                                }
                                            }
                                            $abilityData = ($abilityData + $ability);
                                        }

                                        // Get the global configuration values
                                        $pass_ability_level = $this->getGlobalConfiguration('pass_ability_level');
                                        $pass_only_and_or = $this->getGlobalConfiguration('pass_only_and_or');
                                        $pass_accuracy_level = $this->getGlobalConfiguration('pass_accuracy_level');
                                                                                
                                        $ExamListCount = sizeof($ExamList);
                                        $accuracyAll = round($accuracyData/$ExamListCount,1);
                                        $abilityAll = $abilityData/$ExamListCount;
                                                                                
                                        // Store array into student ability
                                        $reportDataAbilityArray[$strandId][$learningUnitsId][] = array(
                                            'learning_objective_number' => $learningObjectivesData->foci_number,
                                            'LearningsObjectives' => $learningObjectivesData->foci_number.' '.$this->setLearningObjectivesTitle($LearningsObjectivesLbl[$learningObjectivesId]),
                                            'ability' => $abilityAll,
                                            'normalizedAbility' => Helper::getNormalizedAbility($abilityAll),
                                            'studystatus' => Helper::getAbilityType($abilityAll),
                                            'studyStatusColor' => Helper::getGlobalConfiguration(Helper::getAbilityType($abilityAll))
                                        );
                                    }else{
                                        // Store array into student ability
                                        $reportDataAbilityArray[$strandId][$learningUnitsId][] = array(
                                            'learning_objective_number' => $learningObjectivesData->foci_number,
                                            'LearningsObjectives' => $learningObjectivesData->foci_number.' '.$this->setLearningObjectivesTitle($LearningsObjectivesLbl[$learningObjectivesId]),
                                            'ability' => $abilityAll,
                                            'normalizedAbility' => Helper::getNormalizedAbility($abilityAll),
                                            'studystatus' => Helper::getAbilityType($abilityAll),
                                            'studyStatusColor' => Helper::getGlobalConfiguration('incomplete_color')
                                        );
                                    }
                                }else{
                                    $reportDataAbilityArray[$strandId][$learningUnitsId][] = array(
                                        'learning_objective_number' => $learningObjectivesData->foci_number,
                                        'LearningsObjectives' => $learningObjectivesData->foci_number.' '.$this->setLearningObjectivesTitle($LearningsObjectivesLbl[$learningObjectivesId]),
                                        'ability' => $abilityAll,
                                        'normalizedAbility' => Helper::getNormalizedAbility($abilityAll),
                                        'studystatus' => Helper::getAbilityType($abilityAll),
                                        'studyStatusColor' => Helper::getGlobalConfiguration('incomplete_color')
                                    );
                                }
                            }
                        }
                    }
                }
            }
        }

        $OverAllAbility = [];
        $countAbilityUnits = 0;
        if(isset($reportDataAbilityArray) && !empty($reportDataAbilityArray)){
            foreach($reportDataAbilityArray as $strands){
                foreach($strands as $learningUnit){
                    foreach($learningUnit as $learningObjectives){
                        if($learningObjectives['ability'] !=""){
                            $countAbilityUnits++;
                            $OverAllAbility[] = $learningObjectives['ability'];
                        }
                    }
                }
            }
        }
        if(!empty($countAbilityUnits)){
            $naturalAbility = (array_sum($OverAllAbility) / $countAbilityUnits);
            if($naturalAbility != ""){
                User::find($stud_id)->Update([cn::USERS_OVERALL_ABILITY_COL => $naturalAbility]);                
                Log::debug('Update Overall Ability Student id: '. $stud_id);
            }
        }
        Log::info('Student Over all ability job End:');
    }
}
