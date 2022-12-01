<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\GlobalConfiguration;
use App\Constants\DbConstant as cn;

class GlobalConfigurationTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = [
            [
                cn::GLOBAL_CONFIGURATION_KEY_COL => 'maximum_ability_history',
                cn::GLOBAL_CONFIGURATION_VALUE_COL => 20,
            ],
            [
                cn::GLOBAL_CONFIGURATION_KEY_COL => 'minimum_ability_history',
                cn::GLOBAL_CONFIGURATION_VALUE_COL => 5,
            ],
            [
                cn::GLOBAL_CONFIGURATION_KEY_COL => 'maximum_trials_attempt',
                cn::GLOBAL_CONFIGURATION_VALUE_COL => 2,
            ],
            [
                cn::GLOBAL_CONFIGURATION_KEY_COL => 'question_difficulty_easy',
                cn::GLOBAL_CONFIGURATION_VALUE_COL => '{"from":"-10000","to":"-0.6931471805599455","color":"#52da62"}',
            ],
            [
                cn::GLOBAL_CONFIGURATION_KEY_COL => 'question_difficulty_medium',
                cn::GLOBAL_CONFIGURATION_VALUE_COL => '{"from":"-0.6931471805599455","to":"0.6931471805599452","color":"#ee1b1b"}',
            ],
            [
                cn::GLOBAL_CONFIGURATION_KEY_COL => 'question_difficulty_hard',
                cn::GLOBAL_CONFIGURATION_VALUE_COL => '{"from":"0.6931471805599452","to":"10,000","color":"#f5ed05"}',
            ],
            [
                cn::GLOBAL_CONFIGURATION_KEY_COL => 'default_second_per_question',
                cn::GLOBAL_CONFIGURATION_VALUE_COL => 180,
            ],
            [
                cn::GLOBAL_CONFIGURATION_KEY_COL => 'struggling_color',
                cn::GLOBAL_CONFIGURATION_VALUE_COL => '#fe4400',
            ],
            [
                cn::GLOBAL_CONFIGURATION_KEY_COL => 'beginning_color',
                cn::GLOBAL_CONFIGURATION_VALUE_COL => '#f38000',
            ],
            [
                cn::GLOBAL_CONFIGURATION_KEY_COL => 'approaching_color',
                cn::GLOBAL_CONFIGURATION_VALUE_COL => '#dcaf01',
            ],
            [
                cn::GLOBAL_CONFIGURATION_KEY_COL => 'proficient_color',
                cn::GLOBAL_CONFIGURATION_VALUE_COL => '#b8d902',
            ],
            [
                cn::GLOBAL_CONFIGURATION_KEY_COL => 'advanced_color',
                cn::GLOBAL_CONFIGURATION_VALUE_COL => '#7eff01',
            ],
            [
                cn::GLOBAL_CONFIGURATION_KEY_COL => 'incomplete_color',
                cn::GLOBAL_CONFIGURATION_VALUE_COL => '#8c8c8c',
            ],
            [
                cn::GLOBAL_CONFIGURATION_KEY_COL => 'struggling',
                cn::GLOBAL_CONFIGURATION_VALUE_COL => '{"from":"-10000","to":"-1.3862943611198906"}',
            ],
            [
                cn::GLOBAL_CONFIGURATION_KEY_COL => 'beginning',
                cn::GLOBAL_CONFIGURATION_VALUE_COL => '{"from":"-1.3862943611198906","to":"-0.4054651081081643"}',
            ],
            [
                cn::GLOBAL_CONFIGURATION_KEY_COL => 'approaching',
                cn::GLOBAL_CONFIGURATION_VALUE_COL => '{"from":"-0.4054651081081643","to":"0.4054651081081643"}',
            ],
            [
                cn::GLOBAL_CONFIGURATION_KEY_COL => 'proficient',
                cn::GLOBAL_CONFIGURATION_VALUE_COL => '{"from":"0.4054651081081643","to":"1.3862943611198908"}',
            ],
            [
                cn::GLOBAL_CONFIGURATION_KEY_COL => 'advanced',
                cn::GLOBAL_CONFIGURATION_VALUE_COL => '{"from":"1.3862943611198908","to":"10,000"}',
            ],
            [
                cn::GLOBAL_CONFIGURATION_KEY_COL => 'passing_score_percentage',
                cn::GLOBAL_CONFIGURATION_VALUE_COL => 60,
            ],
            [
                cn::GLOBAL_CONFIGURATION_KEY_COL => 'passing_score_accuracy',
                cn::GLOBAL_CONFIGURATION_VALUE_COL => 0.4054651081081643,
            ],
            [
                cn::GLOBAL_CONFIGURATION_KEY_COL => 'question_correct_color',
                cn::GLOBAL_CONFIGURATION_VALUE_COL => '#9dd3d7',
            ],
            [
                cn::GLOBAL_CONFIGURATION_KEY_COL => 'question_incorrect_color',
                cn::GLOBAL_CONFIGURATION_VALUE_COL => '#e0a3a3',
            ],
            [
                cn::GLOBAL_CONFIGURATION_KEY_COL => 'question_difficulty_level1',
                cn::GLOBAL_CONFIGURATION_VALUE_COL => '{"from":"-10000","to":"-1.3862943611198906","color":"#fd4e4e"}',
            ],
            [
                cn::GLOBAL_CONFIGURATION_KEY_COL => 'question_difficulty_level2',
                cn::GLOBAL_CONFIGURATION_VALUE_COL => '{"from":"-1.3862943611198906","to":"-0.4054651081081643","color":"#65ec8d"}',
            ],
            [
                cn::GLOBAL_CONFIGURATION_KEY_COL => 'question_difficulty_level3',
                cn::GLOBAL_CONFIGURATION_VALUE_COL => '{"from":"-0.4054651081081643","to":"0.4054651081081643","color":"#7679bc"}',
            ],
            [
                cn::GLOBAL_CONFIGURATION_KEY_COL => 'question_difficulty_level4',
                cn::GLOBAL_CONFIGURATION_VALUE_COL => '{"from":"0.4054651081081643","to":"1.3862943611198908","color":"#eef773"}',
            ],
            [
                cn::GLOBAL_CONFIGURATION_KEY_COL => 'question_difficulty_level5',
                cn::GLOBAL_CONFIGURATION_VALUE_COL => '{"from":"1.3862943611198908","to":"10000","color":"#fd9be3"}',
            ],
            [
                cn::GLOBAL_CONFIGURATION_KEY_COL => 'pass_ability_level',
                cn::GLOBAL_CONFIGURATION_VALUE_COL => 0.5,
            ],
            [
                cn::GLOBAL_CONFIGURATION_KEY_COL => 'pass_only_and_or',
                cn::GLOBAL_CONFIGURATION_VALUE_COL => 0.5,
            ],
            [
                cn::GLOBAL_CONFIGURATION_KEY_COL => 'pass_accuracy_level',
                cn::GLOBAL_CONFIGURATION_VALUE_COL => 0.5,
            ],
            [
                cn::GLOBAL_CONFIGURATION_KEY_COL => 'no_of_questions_per_learning_skills',
                cn::GLOBAL_CONFIGURATION_VALUE_COL => 2,
            ],
            [
                cn::GLOBAL_CONFIGURATION_KEY_COL => 'max_no_of_questions_per_learning_skills',
                cn::GLOBAL_CONFIGURATION_VALUE_COL => 2,
            ],
            [
                cn::GLOBAL_CONFIGURATION_KEY_COL => 'min_no_question_per_learning_objectives',
                cn::GLOBAL_CONFIGURATION_VALUE_COL => 2,
            ],
            [
                cn::GLOBAL_CONFIGURATION_KEY_COL => 'max_no_question_per_learning_objectives',
                cn::GLOBAL_CONFIGURATION_VALUE_COL => 40,
            ],
            [
                cn::GLOBAL_CONFIGURATION_KEY_COL => 'accomplished_objective',
                cn::GLOBAL_CONFIGURATION_VALUE_COL => '#ae31ff',
            ],
            [
                cn::GLOBAL_CONFIGURATION_KEY_COL => 'not_accomplished_objective',
                cn::GLOBAL_CONFIGURATION_VALUE_COL => '#a8a7a7',
            ],
            [
                cn::GLOBAL_CONFIGURATION_KEY_COL => 'difficulty_selection_type',
                cn::GLOBAL_CONFIGURATION_VALUE_COL => 1,
            ],
            [
                cn::GLOBAL_CONFIGURATION_KEY_COL => 'question_generator_n',
                cn::GLOBAL_CONFIGURATION_VALUE_COL => 50,
            ],
            [
                cn::GLOBAL_CONFIGURATION_KEY_COL => 'repeated_rate',
                cn::GLOBAL_CONFIGURATION_VALUE_COL => 0.1,
            ],
            [
                cn::GLOBAL_CONFIGURATION_KEY_COL => 'assignment_credit_points_for_submission_on_time',
                cn::GLOBAL_CONFIGURATION_VALUE_COL => 2,
            ],
            [
                cn::GLOBAL_CONFIGURATION_KEY_COL => 'self_learning_credit_points_for_exercise',
                cn::GLOBAL_CONFIGURATION_VALUE_COL => 40,
            ],
            [
                cn::GLOBAL_CONFIGURATION_KEY_COL => 'self_learning_credit_points_for_test',
                cn::GLOBAL_CONFIGURATION_VALUE_COL => 30,
            ],
            [
                cn::GLOBAL_CONFIGURATION_KEY_COL => 'assignment_starting_accuracy_to_earn_credit_points',
                cn::GLOBAL_CONFIGURATION_VALUE_COL => 50,
            ],
            [
                cn::GLOBAL_CONFIGURATION_KEY_COL => 'assignment_credit_points_earned_for_starting_accuracy',
                cn::GLOBAL_CONFIGURATION_VALUE_COL => 10,
            ],
            [
                cn::GLOBAL_CONFIGURATION_KEY_COL => 'assignment_accuracy_number_of_stages_to_earn_extra_credit_point',
                cn::GLOBAL_CONFIGURATION_VALUE_COL => 3,
            ],
            [
                cn::GLOBAL_CONFIGURATION_KEY_COL => 'assignment_difference_of_accuracy_between_stages',
                cn::GLOBAL_CONFIGURATION_VALUE_COL => 10,
            ],
            [
                cn::GLOBAL_CONFIGURATION_KEY_COL => 'assignment_accuracy_extra_credit_points_for_each_stage',
                cn::GLOBAL_CONFIGURATION_VALUE_COL => 2,
            ],
            [
                cn::GLOBAL_CONFIGURATION_KEY_COL => 'assignment_starting_normalized_ability_to_earn_credit_points',
                cn::GLOBAL_CONFIGURATION_VALUE_COL => 40,
            ],
            [
                cn::GLOBAL_CONFIGURATION_KEY_COL => 'assignment_credit_points_earned_for_starting_normalized_ability',
                cn::GLOBAL_CONFIGURATION_VALUE_COL => 10,
            ],
            [
                cn::GLOBAL_CONFIGURATION_KEY_COL => 'assignment_ability_number_of_stages_to_earn_extra_credit_point',
                cn::GLOBAL_CONFIGURATION_VALUE_COL => 3,
            ],
            [
                cn::GLOBAL_CONFIGURATION_KEY_COL => 'assignment_difference_of_normalized_ability_between_stages',
                cn::GLOBAL_CONFIGURATION_VALUE_COL => 10,
            ],
            [
                cn::GLOBAL_CONFIGURATION_KEY_COL => 'assignment_ability_extra_credit_points_for_each_stage',
                cn::GLOBAL_CONFIGURATION_VALUE_COL => 2,
            ],
            [
                cn::GLOBAL_CONFIGURATION_KEY_COL => 'self_learning_exercise_starting_accuracy_to_earn_credit_points',
                cn::GLOBAL_CONFIGURATION_VALUE_COL => 50,
            ],
            [
                cn::GLOBAL_CONFIGURATION_KEY_COL => 'self_learning_exercise_credit_points_earned_for_starting_accuracy',
                cn::GLOBAL_CONFIGURATION_VALUE_COL => 10,
            ],
            [
                cn::GLOBAL_CONFIGURATION_KEY_COL => 'self_learning_exercise_number_of_stages_to_earn_extra_credit_point',
                cn::GLOBAL_CONFIGURATION_VALUE_COL => 3,
            ],
            [
                cn::GLOBAL_CONFIGURATION_KEY_COL => 'self_learning_exercise_difference_of_accuracy_between_stages',
                cn::GLOBAL_CONFIGURATION_VALUE_COL => 10,
            ],
            [
                cn::GLOBAL_CONFIGURATION_KEY_COL => 'self_learning_exercise_accuracy_extra_credit_points_for_each_stage',
                cn::GLOBAL_CONFIGURATION_VALUE_COL => 2,
            ],
            [
                cn::GLOBAL_CONFIGURATION_KEY_COL => 'self_learning_exercise_starting_normalized_ability_to_earn_credit_points',
                cn::GLOBAL_CONFIGURATION_VALUE_COL => 40,
            ],
            [
                cn::GLOBAL_CONFIGURATION_KEY_COL => 'self_learning_exercise_credit_points_earned_for_starting_normalized_ability',
                cn::GLOBAL_CONFIGURATION_VALUE_COL => 10,
            ],
            [
                cn::GLOBAL_CONFIGURATION_KEY_COL => 'self_learning_exercise_number_of_stages_to_earn_extra_credit_points',
                cn::GLOBAL_CONFIGURATION_VALUE_COL => 3,
            ],
            [
                cn::GLOBAL_CONFIGURATION_KEY_COL => 'self_learning_exercise_difference_of_normalized_ability_between_stages',
                cn::GLOBAL_CONFIGURATION_VALUE_COL => 10,
            ],
            [
                cn::GLOBAL_CONFIGURATION_KEY_COL => 'self_learning_exercise_ability_extra_credit_points_for_each_stage',
                cn::GLOBAL_CONFIGURATION_VALUE_COL => 2,
            ],
            [
                cn::GLOBAL_CONFIGURATION_KEY_COL => 'self_learning_test_starting_accuracy_to_earn_credit_points',
                cn::GLOBAL_CONFIGURATION_VALUE_COL => 50,
            ],
            [
                cn::GLOBAL_CONFIGURATION_KEY_COL => 'self_learning_test_credit_points_earned_for_starting_accuracy',
                cn::GLOBAL_CONFIGURATION_VALUE_COL => 10,
            ],
            [
                cn::GLOBAL_CONFIGURATION_KEY_COL => 'self_learning_test_number_of_stages_to_earn_extra_credit_point',
                cn::GLOBAL_CONFIGURATION_VALUE_COL => 3,
            ],
            [
                cn::GLOBAL_CONFIGURATION_KEY_COL => 'self_learning_test_difference_of_accuracy_between_stages',
                cn::GLOBAL_CONFIGURATION_VALUE_COL => 10,
            ],
            [
                cn::GLOBAL_CONFIGURATION_KEY_COL => 'self_learning_test_accuracy_extra_credit_points_for_each_stage',
                cn::GLOBAL_CONFIGURATION_VALUE_COL => 2,
            ],
            [
                cn::GLOBAL_CONFIGURATION_KEY_COL => 'self_learning_test_starting_normalized_ability_to_earn_credit_points',
                cn::GLOBAL_CONFIGURATION_VALUE_COL => 40,
            ],
            [
                cn::GLOBAL_CONFIGURATION_KEY_COL => 'self_learning_test_credit_points_earned_for_starting_normalized_ability',
                cn::GLOBAL_CONFIGURATION_VALUE_COL => 10,
            ],
            [
                cn::GLOBAL_CONFIGURATION_KEY_COL => 'self_learning_test_number_of_stages_to_earn_extra_credit_points',
                cn::GLOBAL_CONFIGURATION_VALUE_COL => 3,
            ],
            [
                cn::GLOBAL_CONFIGURATION_KEY_COL => 'self_learning_test_difference_of_normalized_ability_between_stages',
                cn::GLOBAL_CONFIGURATION_VALUE_COL => 10,
            ],
            [
                cn::GLOBAL_CONFIGURATION_KEY_COL => 'self_learning_test_ability_extra_credit_points_for_each_stage',
                cn::GLOBAL_CONFIGURATION_VALUE_COL => 2,
            ],
            [
                cn::GLOBAL_CONFIGURATION_KEY_COL => 'submission_on_time',
                cn::GLOBAL_CONFIGURATION_VALUE_COL => 'yes',
            ],
            [
                cn::GLOBAL_CONFIGURATION_KEY_COL => 'credit_points_of_accuracy',
                cn::GLOBAL_CONFIGURATION_VALUE_COL => 'yes',
            ],
            [
                cn::GLOBAL_CONFIGURATION_KEY_COL => 'credit_points_of_normalized_ability',
                cn::GLOBAL_CONFIGURATION_VALUE_COL => 'yes',
            ],
            [
                cn::GLOBAL_CONFIGURATION_KEY_COL => 'study_status_master',
                cn::GLOBAL_CONFIGURATION_VALUE_COL => 'approaching',
            ],
            [
                cn::GLOBAL_CONFIGURATION_KEY_COL => 'min_no_question_per_study_progress',
                cn::GLOBAL_CONFIGURATION_VALUE_COL => 10,
            ],
            [
                cn::GLOBAL_CONFIGURATION_KEY_COL => 'max_no_question_per_study_progress',
                cn::GLOBAL_CONFIGURATION_VALUE_COL => 50,
            ],
            [
                cn::GLOBAL_CONFIGURATION_KEY_COL => 'question_window_size_of_learning_objective',
                cn::GLOBAL_CONFIGURATION_VALUE_COL => 50,
            ],
            [
                cn::GLOBAL_CONFIGURATION_KEY_COL => 'attempt_exam_restrict_notification_en',
                cn::GLOBAL_CONFIGURATION_VALUE_COL => 'Attention: Please do not refresh this page, otherwise you will not be able to complete this test.',
            ],
            [
                cn::GLOBAL_CONFIGURATION_KEY_COL => 'attempt_exam_restrict_notification_ch',
                cn::GLOBAL_CONFIGURATION_VALUE_COL => '注意：請不要刷新此頁面，否則你將無法完成此測試。',
            ],
            [
                cn::GLOBAL_CONFIGURATION_KEY_COL => 'current_curriculum_year',
                cn::GLOBAL_CONFIGURATION_VALUE_COL => 23,
            ],
        ];

        if(!empty($data)){
            foreach($data as $key => $value){
                GlobalConfiguration::updateOrCreate($value);
            }
        }
    }
}
