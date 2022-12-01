<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Exam;
use App\Models\UserCreditPoints;
use App\Models\UserCreditPointHistory;
use App\Models\GlobalConfiguration;
use App\Constants\DbConstant As cn;
use Log;
use App\Helpers\Helper;
use App\Traits\Common;

class UpdateUserCreditPointsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, Common;

    protected $ExamId, $StudentId , $SchoolId;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($ExamId, $StudentId, $SchoolId)
    {
        $this->ExamId = $ExamId;
        $this->StudentId = $StudentId;
        $this->SchoolId = $SchoolId;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        ini_set('max_execution_time', -1);
        Log::info('Job Start Update Student Credit Points');
        $SubmissionCreditPoint = 0;
        $AccuracyCreditPoint = 0;
        $AbilityCreditPoint = 0;
        if(!empty($this->ExamId)){
            $ExamData = Exam::with(['attempt_exams' => fn($query) => $query->where(cn::ATTEMPT_EXAMS_STUDENT_STUDENT_ID, $this->StudentId)])
                        ->with(['examCreditPointRules' => fn($query) => $query->where([
                                cn::EXAM_CREDIT_POINT_RULES_MAPPING_RULES_VALUE_COL => 'yes',
                                cn::EXAM_CREDIT_POINT_RULES_MAPPING_STATUS_COL => 'active',
                                'school_id' => $this->SchoolId
                            ])
                        ])
                        ->where(cn::EXAM_TABLE_ID_COLS,$this->ExamId)
                        ->first();
            if(!empty($ExamData)){
                $StudentAccuracy = Helper::getAccuracy($this->ExamId, $this->StudentId);
                $StudentAbility = $ExamData['attempt_exams'][0][cn::ATTEMPT_EXAMS_STUDENT_ABILITY_COL] ?? 0;
                $StudentNormalizedAbility = Helper::getNormalizedAbility($StudentAbility);
                $GlobalConfiguration = GlobalConfiguration::get()->toArray();

                // For Self Learning
                if($ExamData->{cn::EXAM_TYPE_COLS} == 1){
                    $CreditPointRulesKey = array("credit_points_of_accuracy","credit_points_of_normalized_ability");
                    if(isset($CreditPointRulesKey) && !empty($CreditPointRulesKey)){
                        foreach($CreditPointRulesKey as $RulesKey){
                            // For Self-Learning Exercise
                            if($ExamData->{cn::EXAM_TABLE_SELF_LEARNING_TEST_TYPE_COL} == 1){
                                if(count(explode(",",$ExamData->{cn::EXAM_TABLE_QUESTION_IDS_COL})) >= $GlobalConfiguration[array_search('self_learning_credit_points_for_exercise', array_column($GlobalConfiguration, cn::GLOBAL_CONFIGURATION_KEY_COL))][cn::GLOBAL_CONFIGURATION_VALUE_COL]){
                                    switch($RulesKey){
                                        case "credit_points_of_accuracy":
                                            if(!empty($StudentAccuracy)){
                                                if($StudentAccuracy >= $GlobalConfiguration[array_search('self_learning_exercise_starting_accuracy_to_earn_credit_points', array_column($GlobalConfiguration, cn::GLOBAL_CONFIGURATION_KEY_COL))][cn::GLOBAL_CONFIGURATION_VALUE_COL]){
                                                    $AccuracyCreditPoint += $GlobalConfiguration[array_search('self_learning_exercise_credit_points_earned_for_starting_accuracy', array_column($GlobalConfiguration, cn::GLOBAL_CONFIGURATION_KEY_COL))][cn::GLOBAL_CONFIGURATION_VALUE_COL];

                                                    $self_learning_exercise_number_of_stages_to_earn_extra_credit_point = $GlobalConfiguration[array_search('self_learning_exercise_number_of_stages_to_earn_extra_credit_point', array_column($GlobalConfiguration, cn::GLOBAL_CONFIGURATION_KEY_COL))][cn::GLOBAL_CONFIGURATION_VALUE_COL];
                                                    $startingAccuracyPercentage = $GlobalConfiguration[array_search('self_learning_exercise_starting_accuracy_to_earn_credit_points', array_column($GlobalConfiguration, cn::GLOBAL_CONFIGURATION_KEY_COL))][cn::GLOBAL_CONFIGURATION_VALUE_COL];
                                                    if(!empty($self_learning_exercise_number_of_stages_to_earn_extra_credit_point)){
                                                        $NoOfStagesAccuracyPercentage = ($startingAccuracyPercentage + $GlobalConfiguration[array_search('self_learning_exercise_difference_of_accuracy_between_stages', array_column($GlobalConfiguration, cn::GLOBAL_CONFIGURATION_KEY_COL))][cn::GLOBAL_CONFIGURATION_VALUE_COL]);
                                                        for($i = 1; $i <= $self_learning_exercise_number_of_stages_to_earn_extra_credit_point; $i++){
                                                            if($StudentAccuracy >= $NoOfStagesAccuracyPercentage){
                                                                // Extra Credit Points for Each Stage
                                                                $AccuracyCreditPoint += $GlobalConfiguration[array_search('self_learning_exercise_accuracy_extra_credit_points_for_each_stage', array_column($GlobalConfiguration, cn::GLOBAL_CONFIGURATION_KEY_COL))][cn::GLOBAL_CONFIGURATION_VALUE_COL];
                                                            }
                                                            $NoOfStagesAccuracyPercentage = ($NoOfStagesAccuracyPercentage + $GlobalConfiguration[array_search('self_learning_exercise_difference_of_accuracy_between_stages', array_column($GlobalConfiguration, cn::GLOBAL_CONFIGURATION_KEY_COL))][cn::GLOBAL_CONFIGURATION_VALUE_COL]);
                                                        }
                                                    }
                                                    if(!empty($AccuracyCreditPoint)){
                                                        UserCreditPointHistory::Create([
                                                            cn::USER_CREDIT_POINT_HISTORY_EXAM_ID_COL => $ExamData->{cn::EXAM_TABLE_ID_COLS},
                                                            cn::USER_CREDIT_POINT_HISTORY_USER_ID_COL => $this->StudentId,
                                                            cn::USER_CREDIT_POINT_HISTORY_TEST_TYPE_COL => 'self_learning',
                                                            cn::USER_CREDIT_POINT_HISTORY_SELF_LEARNING_TYPE_COL => 'exercise',
                                                            cn::USER_CREDIT_POINT_HISTORY_CREDIT_POINT_TYPE_COL => 'accuracy_level',
                                                            cn::USER_CREDIT_POINT_HISTORY_NO_OF_CREDIT_POINT_COL => $AccuracyCreditPoint
                                                        ]);
                                                    }
                                                }
                                            }
                                            break;
                                        case "credit_points_of_normalized_ability":
                                                if(!empty($StudentNormalizedAbility)){
                                                    if($StudentNormalizedAbility >= $GlobalConfiguration[array_search('self_learning_exercise_starting_normalized_ability_to_earn_credit_points', array_column($GlobalConfiguration, cn::GLOBAL_CONFIGURATION_KEY_COL))][cn::GLOBAL_CONFIGURATION_VALUE_COL]){
                                                        
                                                        //Starting Normalized Ability (%) to Earn Credit Points
                                                        $AbilityCreditPoint += $GlobalConfiguration[array_search('self_learning_exercise_credit_points_earned_for_starting_normalized_ability', array_column($GlobalConfiguration, cn::GLOBAL_CONFIGURATION_KEY_COL))][cn::GLOBAL_CONFIGURATION_VALUE_COL];

                                                        // Number of Stages to Earn Extra Credit Points
                                                        $self_learning_exercise_number_of_stages_to_earn_extra_credit_points = $GlobalConfiguration[array_search('self_learning_exercise_number_of_stages_to_earn_extra_credit_points', array_column($GlobalConfiguration, cn::GLOBAL_CONFIGURATION_KEY_COL))][cn::GLOBAL_CONFIGURATION_VALUE_COL];
                                                        $startingAbilityPercentage = $GlobalConfiguration[array_search('self_learning_exercise_starting_normalized_ability_to_earn_credit_points', array_column($GlobalConfiguration, cn::GLOBAL_CONFIGURATION_KEY_COL))][cn::GLOBAL_CONFIGURATION_VALUE_COL];

                                                        if(!empty($self_learning_exercise_number_of_stages_to_earn_extra_credit_points)){
                                                            // Difference of Normalized Ability (%) between Stages
                                                            $NoOfStagesAbilityPercentage = ($startingAbilityPercentage + $GlobalConfiguration[array_search('self_learning_exercise_difference_of_normalized_ability_between_stages', array_column($GlobalConfiguration, cn::GLOBAL_CONFIGURATION_KEY_COL))][cn::GLOBAL_CONFIGURATION_VALUE_COL]);

                                                            for($i=1; $i <= $self_learning_exercise_number_of_stages_to_earn_extra_credit_points; $i++){
                                                                if($StudentNormalizedAbility >= $NoOfStagesAbilityPercentage){
                                                                    // Extra Credit Points for Each Stage
                                                                    $AbilityCreditPoint += $GlobalConfiguration[array_search('self_learning_exercise_ability_extra_credit_points_for_each_stage', array_column($GlobalConfiguration, cn::GLOBAL_CONFIGURATION_KEY_COL))][cn::GLOBAL_CONFIGURATION_VALUE_COL];
                                                                }
                                                                $NoOfStagesAbilityPercentage = ($NoOfStagesAbilityPercentage + $GlobalConfiguration[array_search('self_learning_exercise_difference_of_normalized_ability_between_stages', array_column($GlobalConfiguration, cn::GLOBAL_CONFIGURATION_KEY_COL))][cn::GLOBAL_CONFIGURATION_VALUE_COL]);
                                                            }
                                                        }
                                                    }

                                                    if(!empty($AbilityCreditPoint)){
                                                        UserCreditPointHistory::Create([
                                                            cn::USER_CREDIT_POINT_HISTORY_EXAM_ID_COL => $ExamData->{cn::EXAM_TABLE_ID_COLS},
                                                            cn::USER_CREDIT_POINT_HISTORY_USER_ID_COL => $this->StudentId,
                                                            cn::USER_CREDIT_POINT_HISTORY_TEST_TYPE_COL => 'self_learning',
                                                            cn::USER_CREDIT_POINT_HISTORY_SELF_LEARNING_TYPE_COL => 'test',
                                                            cn::USER_CREDIT_POINT_HISTORY_CREDIT_POINT_TYPE_COL => 'ability_level',
                                                            cn::USER_CREDIT_POINT_HISTORY_NO_OF_CREDIT_POINT_COL => $AbilityCreditPoint
                                                        ]);
                                                    }
                                                }
                                                break;
                                    }
                                }
                            }
                            
                            // For Self-Learning Test
                            if($ExamData->{cn::EXAM_TABLE_SELF_LEARNING_TEST_TYPE_COL} == 2){
                                if(count(explode(",",$ExamData->{cn::EXAM_TABLE_QUESTION_IDS_COL})) >= $GlobalConfiguration[array_search('self_learning_credit_points_for_test', array_column($GlobalConfiguration, cn::GLOBAL_CONFIGURATION_KEY_COL))][cn::GLOBAL_CONFIGURATION_VALUE_COL]){
                                    switch($RulesKey){
                                        case "credit_points_of_accuracy":
                                            if(!empty($StudentAccuracy)){
                                                if($StudentAccuracy >= $GlobalConfiguration[array_search('self_learning_test_starting_accuracy_to_earn_credit_points', array_column($GlobalConfiguration, cn::GLOBAL_CONFIGURATION_KEY_COL))][cn::GLOBAL_CONFIGURATION_VALUE_COL]){
                                                    $AccuracyCreditPoint += $GlobalConfiguration[array_search('self_learning_test_credit_points_earned_for_starting_accuracy', array_column($GlobalConfiguration, cn::GLOBAL_CONFIGURATION_KEY_COL))][cn::GLOBAL_CONFIGURATION_VALUE_COL];

                                                    $self_learning_test_number_of_stages_to_earn_extra_credit_point = $GlobalConfiguration[array_search('self_learning_test_number_of_stages_to_earn_extra_credit_point', array_column($GlobalConfiguration, cn::GLOBAL_CONFIGURATION_KEY_COL))][cn::GLOBAL_CONFIGURATION_VALUE_COL];
                                                    $startingAccuracyPercentage = $GlobalConfiguration[array_search('self_learning_test_starting_accuracy_to_earn_credit_points', array_column($GlobalConfiguration, cn::GLOBAL_CONFIGURATION_KEY_COL))][cn::GLOBAL_CONFIGURATION_VALUE_COL];
                                                    if(!empty($self_learning_test_number_of_stages_to_earn_extra_credit_point)){
                                                        $NoOfStagesAccuracyPercentage = ($startingAccuracyPercentage + $GlobalConfiguration[array_search('self_learning_test_difference_of_accuracy_between_stages', array_column($GlobalConfiguration, cn::GLOBAL_CONFIGURATION_KEY_COL))][cn::GLOBAL_CONFIGURATION_VALUE_COL]);
                                                        for($i = 1; $i <= $self_learning_test_number_of_stages_to_earn_extra_credit_point; $i++){
                                                            if($StudentAccuracy >= $NoOfStagesAccuracyPercentage){
                                                                // Extra Credit Points for Each Stage
                                                                $AccuracyCreditPoint += $GlobalConfiguration[array_search('self_learning_test_accuracy_extra_credit_points_for_each_stage', array_column($GlobalConfiguration, cn::GLOBAL_CONFIGURATION_KEY_COL))][cn::GLOBAL_CONFIGURATION_VALUE_COL];
                                                            }
                                                            $NoOfStagesAccuracyPercentage = ($NoOfStagesAccuracyPercentage + $GlobalConfiguration[array_search('self_learning_test_difference_of_accuracy_between_stages', array_column($GlobalConfiguration, cn::GLOBAL_CONFIGURATION_KEY_COL))][cn::GLOBAL_CONFIGURATION_VALUE_COL]);
                                                        }
                                                    }
                                                    if(!empty($AccuracyCreditPoint)){
                                                        UserCreditPointHistory::Create([
                                                            cn::USER_CREDIT_POINT_HISTORY_EXAM_ID_COL => $ExamData->{cn::EXAM_TABLE_ID_COLS},
                                                            cn::USER_CREDIT_POINT_HISTORY_USER_ID_COL => $this->StudentId,
                                                            cn::USER_CREDIT_POINT_HISTORY_TEST_TYPE_COL => 'self_learning',
                                                            cn::USER_CREDIT_POINT_HISTORY_SELF_LEARNING_TYPE_COL => 'test',
                                                            cn::USER_CREDIT_POINT_HISTORY_CREDIT_POINT_TYPE_COL => 'accuracy_level',
                                                            cn::USER_CREDIT_POINT_HISTORY_NO_OF_CREDIT_POINT_COL => $AccuracyCreditPoint
                                                        ]);
                                                    }
                                                }
                                            }
                                            break;
                                        case "credit_points_of_normalized_ability":
                                                if(!empty($StudentNormalizedAbility)){
                                                    if($StudentNormalizedAbility >= $GlobalConfiguration[array_search('self_learning_test_starting_normalized_ability_to_earn_credit_points', array_column($GlobalConfiguration, cn::GLOBAL_CONFIGURATION_KEY_COL))][cn::GLOBAL_CONFIGURATION_VALUE_COL]){
                                                        
                                                        //Starting Normalized Ability (%) to Earn Credit Points
                                                        $AbilityCreditPoint += $GlobalConfiguration[array_search('self_learning_test_credit_points_earned_for_starting_normalized_ability', array_column($GlobalConfiguration, cn::GLOBAL_CONFIGURATION_KEY_COL))][cn::GLOBAL_CONFIGURATION_VALUE_COL];

                                                        // Number of Stages to Earn Extra Credit Points
                                                        $self_learning_test_number_of_stages_to_earn_extra_credit_points = $GlobalConfiguration[array_search('self_learning_test_number_of_stages_to_earn_extra_credit_points', array_column($GlobalConfiguration, cn::GLOBAL_CONFIGURATION_KEY_COL))][cn::GLOBAL_CONFIGURATION_VALUE_COL];
                                                        $startingAbilityPercentage = $GlobalConfiguration[array_search('self_learning_test_starting_normalized_ability_to_earn_credit_points', array_column($GlobalConfiguration, cn::GLOBAL_CONFIGURATION_KEY_COL))][cn::GLOBAL_CONFIGURATION_VALUE_COL];

                                                        if(!empty($self_learning_test_number_of_stages_to_earn_extra_credit_points)){
                                                            // Difference of Normalized Ability (%) between Stages
                                                            $NoOfStagesAbilityPercentage = ($startingAbilityPercentage + $GlobalConfiguration[array_search('self_learning_test_difference_of_normalized_ability_between_stages', array_column($GlobalConfiguration, cn::GLOBAL_CONFIGURATION_KEY_COL))][cn::GLOBAL_CONFIGURATION_VALUE_COL]);

                                                            for($i=1; $i <= $self_learning_test_number_of_stages_to_earn_extra_credit_points; $i++){
                                                                if($StudentNormalizedAbility >= $NoOfStagesAbilityPercentage){
                                                                    // Extra Credit Points for Each Stage
                                                                    $AbilityCreditPoint += $GlobalConfiguration[array_search('self_learning_test_ability_extra_credit_points_for_each_stage', array_column($GlobalConfiguration, cn::GLOBAL_CONFIGURATION_KEY_COL))][cn::GLOBAL_CONFIGURATION_VALUE_COL];
                                                                }
                                                                $NoOfStagesAbilityPercentage = ($NoOfStagesAbilityPercentage + $GlobalConfiguration[array_search('self_learning_test_difference_of_normalized_ability_between_stages', array_column($GlobalConfiguration, cn::GLOBAL_CONFIGURATION_KEY_COL))][cn::GLOBAL_CONFIGURATION_VALUE_COL]);
                                                            }
                                                        }
                                                    }

                                                    if(!empty($AbilityCreditPoint)){
                                                        UserCreditPointHistory::Create([
                                                            cn::USER_CREDIT_POINT_HISTORY_EXAM_ID_COL => $ExamData->{cn::EXAM_TABLE_ID_COLS},
                                                            cn::USER_CREDIT_POINT_HISTORY_USER_ID_COL => $this->StudentId,
                                                            cn::USER_CREDIT_POINT_HISTORY_TEST_TYPE_COL => 'self_learning',
                                                            cn::USER_CREDIT_POINT_HISTORY_SELF_LEARNING_TYPE_COL => 'test',
                                                            cn::USER_CREDIT_POINT_HISTORY_CREDIT_POINT_TYPE_COL => 'ability_level',
                                                            cn::USER_CREDIT_POINT_HISTORY_NO_OF_CREDIT_POINT_COL => $AbilityCreditPoint
                                                        ]);
                                                    }
                                                }
                                                break;
                                    }
                                }
                            }
                        }
                    }
                }

                // For  Assignment Exercise
                if($ExamData->{cn::EXAM_TYPE_COLS} == 2){
                    if(!empty($ExamData->examCreditPointRules)){
                        $CreditPointRulesKey = $ExamData->examCreditPointRules->pluck(cn::EXAM_CREDIT_POINT_RULES_MAPPING_CREDIT_POINT_RULES_COL)->toArray();
                        if(isset($CreditPointRulesKey) && !empty($CreditPointRulesKey)){
                            foreach($CreditPointRulesKey as $RulesKey){
                                switch($RulesKey){
                                    case "submission_on_time":
                                            // Credit Points for Submission on Time
                                            $SubmissionCreditPoint = Helper::getGlobalConfiguration('assignment_credit_points_for_submission_on_time');
                                            if(!empty($SubmissionCreditPoint)){
                                                UserCreditPointHistory::Create([
                                                    cn::USER_CREDIT_POINT_HISTORY_EXAM_ID_COL => $ExamData->{cn::EXAM_TABLE_ID_COLS},
                                                    cn::USER_CREDIT_POINT_HISTORY_USER_ID_COL => $this->StudentId,
                                                    cn::USER_CREDIT_POINT_HISTORY_TEST_TYPE_COL => 'assignment',
                                                    cn::USER_CREDIT_POINT_HISTORY_CREDIT_POINT_TYPE_COL => 'after_submission',
                                                    cn::USER_CREDIT_POINT_HISTORY_NO_OF_CREDIT_POINT_COL => $SubmissionCreditPoint
                                                ]);
                                            }
                                            break;
                                    case "credit_points_of_accuracy":
                                            if(!empty($StudentAccuracy)){
                                                if($StudentAccuracy >= $GlobalConfiguration[array_search('assignment_starting_accuracy_to_earn_credit_points', array_column($GlobalConfiguration, cn::GLOBAL_CONFIGURATION_KEY_COL))][cn::GLOBAL_CONFIGURATION_VALUE_COL]){
                                                    //Starting Accuracy (%) to Earn Credit Points
                                                    $AccuracyCreditPoint += $GlobalConfiguration[array_search('assignment_credit_points_earned_for_starting_accuracy', array_column($GlobalConfiguration, cn::GLOBAL_CONFIGURATION_KEY_COL))][cn::GLOBAL_CONFIGURATION_VALUE_COL];

                                                    // Number of Stages to Earn Extra Credit Point
                                                    $assignment_accuracy_number_of_stages_to_earn_extra_credit_point = $GlobalConfiguration[array_search('assignment_accuracy_number_of_stages_to_earn_extra_credit_point', array_column($GlobalConfiguration, cn::GLOBAL_CONFIGURATION_KEY_COL))][cn::GLOBAL_CONFIGURATION_VALUE_COL];
                                                    $startingAccuracyPercentage = $GlobalConfiguration[array_search('assignment_starting_accuracy_to_earn_credit_points', array_column($GlobalConfiguration, cn::GLOBAL_CONFIGURATION_KEY_COL))][cn::GLOBAL_CONFIGURATION_VALUE_COL];

                                                    if(!empty($assignment_accuracy_number_of_stages_to_earn_extra_credit_point)){
                                                        // Difference of Accuracy (%) between Stages
                                                        $NoOfStagesAccuracyPercentage = ($startingAccuracyPercentage + $GlobalConfiguration[array_search('assignment_difference_of_accuracy_between_stages', array_column($GlobalConfiguration, cn::GLOBAL_CONFIGURATION_KEY_COL))][cn::GLOBAL_CONFIGURATION_VALUE_COL]);

                                                        for($i=1; $i <= $assignment_accuracy_number_of_stages_to_earn_extra_credit_point; $i++){
                                                            if($StudentAccuracy >= $NoOfStagesAccuracyPercentage){
                                                                // Extra Credit Points for Each Stage
                                                                $AccuracyCreditPoint += $GlobalConfiguration[array_search('assignment_accuracy_extra_credit_points_for_each_stage', array_column($GlobalConfiguration, cn::GLOBAL_CONFIGURATION_KEY_COL))][cn::GLOBAL_CONFIGURATION_VALUE_COL];
                                                            }
                                                            $NoOfStagesAccuracyPercentage = ($NoOfStagesAccuracyPercentage + $GlobalConfiguration[array_search('assignment_difference_of_accuracy_between_stages', array_column($GlobalConfiguration, cn::GLOBAL_CONFIGURATION_KEY_COL))][cn::GLOBAL_CONFIGURATION_VALUE_COL]);
                                                        }
                                                    }
                                                }

                                                if(!empty($AccuracyCreditPoint)){
                                                    UserCreditPointHistory::Create([
                                                        cn::USER_CREDIT_POINT_HISTORY_EXAM_ID_COL => $ExamData->{cn::EXAM_TABLE_ID_COLS},
                                                        cn::USER_CREDIT_POINT_HISTORY_USER_ID_COL => $this->StudentId,
                                                        cn::USER_CREDIT_POINT_HISTORY_TEST_TYPE_COL => 'assignment',
                                                        cn::USER_CREDIT_POINT_HISTORY_CREDIT_POINT_TYPE_COL => 'accuracy_level',
                                                        cn::USER_CREDIT_POINT_HISTORY_NO_OF_CREDIT_POINT_COL => $AccuracyCreditPoint
                                                    ]);
                                                }
                                            }
                                            break;
                                    case "credit_points_of_normalized_ability":
                                            if(!empty($StudentNormalizedAbility)){
                                                if($StudentNormalizedAbility >= $GlobalConfiguration[array_search('assignment_starting_normalized_ability_to_earn_credit_points', array_column($GlobalConfiguration, cn::GLOBAL_CONFIGURATION_KEY_COL))][cn::GLOBAL_CONFIGURATION_VALUE_COL]){
                                                    
                                                    //Starting Normalized Ability (%) to Earn Credit Points
                                                    $AbilityCreditPoint += $GlobalConfiguration[array_search('assignment_credit_points_earned_for_starting_normalized_ability', array_column($GlobalConfiguration, cn::GLOBAL_CONFIGURATION_KEY_COL))][cn::GLOBAL_CONFIGURATION_VALUE_COL];

                                                    // Number of Stages to Earn Extra Credit Points
                                                    $assignment_ability_number_of_stages_to_earn_extra_credit_point = $GlobalConfiguration[array_search('assignment_ability_number_of_stages_to_earn_extra_credit_point', array_column($GlobalConfiguration, cn::GLOBAL_CONFIGURATION_KEY_COL))][cn::GLOBAL_CONFIGURATION_VALUE_COL];
                                                    $startingAbilityPercentage = $GlobalConfiguration[array_search('assignment_starting_normalized_ability_to_earn_credit_points', array_column($GlobalConfiguration, cn::GLOBAL_CONFIGURATION_KEY_COL))][cn::GLOBAL_CONFIGURATION_VALUE_COL];

                                                    if(!empty($assignment_ability_number_of_stages_to_earn_extra_credit_point)){
                                                        // Difference of Normalized Ability (%) between Stages
                                                        $NoOfStagesAbilityPercentage = ($startingAbilityPercentage + $GlobalConfiguration[array_search('assignment_difference_of_normalized_ability_between_stages', array_column($GlobalConfiguration, cn::GLOBAL_CONFIGURATION_KEY_COL))][cn::GLOBAL_CONFIGURATION_VALUE_COL]);

                                                        for($i=1; $i <= $assignment_ability_number_of_stages_to_earn_extra_credit_point; $i++){
                                                            if($StudentNormalizedAbility >= $NoOfStagesAbilityPercentage){
                                                                // Extra Credit Points for Each Stage
                                                                $AbilityCreditPoint += $GlobalConfiguration[array_search('assignment_ability_extra_credit_points_for_each_stage', array_column($GlobalConfiguration, cn::GLOBAL_CONFIGURATION_KEY_COL))][cn::GLOBAL_CONFIGURATION_VALUE_COL];
                                                            }
                                                            $NoOfStagesAbilityPercentage = ($NoOfStagesAbilityPercentage + $GlobalConfiguration[array_search('assignment_difference_of_normalized_ability_between_stages', array_column($GlobalConfiguration, cn::GLOBAL_CONFIGURATION_KEY_COL))][cn::GLOBAL_CONFIGURATION_VALUE_COL]);
                                                        }
                                                    }
                                                }

                                                if(!empty($AbilityCreditPoint)){
                                                    UserCreditPointHistory::Create([
                                                        cn::USER_CREDIT_POINT_HISTORY_EXAM_ID_COL => $ExamData->{cn::EXAM_TABLE_ID_COLS},
                                                        cn::USER_CREDIT_POINT_HISTORY_USER_ID_COL => $this->StudentId,
                                                        cn::USER_CREDIT_POINT_HISTORY_TEST_TYPE_COL => 'assignment',
                                                        cn::USER_CREDIT_POINT_HISTORY_CREDIT_POINT_TYPE_COL => 'ability_level',
                                                        cn::USER_CREDIT_POINT_HISTORY_NO_OF_CREDIT_POINT_COL => $AbilityCreditPoint
                                                    ]);
                                                }
                                            }
                                            break;
                                }    
                            }
                        }
                    }
                }
            }

            // Get Student Total credit points 
            $GetStudentTotalCreditPoints = UserCreditPointHistory::where(cn::USER_CREDIT_POINT_HISTORY_USER_ID_COL,$this->StudentId)->sum(cn::USER_CREDIT_POINT_HISTORY_NO_OF_CREDIT_POINT_COL);
            if(!empty($GetStudentTotalCreditPoints)){
                // Update Student Credit Point table
                UserCreditPoints::updateOrCreate(
                    [cn::USER_CREDIT_USER_ID_COL => $this->StudentId],
                    [
                        cn::USER_CREDIT_USER_ID_COL => $this->StudentId,
                        cn::USER_NO_OF_CREDIT_POINTS_COL => $GetStudentTotalCreditPoints
                    ]
                );
            }
        }
        Log::info('Job Complete Update Student Credit Points');
    }
}
