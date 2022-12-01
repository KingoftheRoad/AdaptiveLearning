<?php

namespace App\Http\Repositories;

use Illuminate\Support\Facades\Auth;
use App\Constants\DbConstant as cn;
use App\Traits\Common;
use App\Traits\ResponseFormat;
use App\Models\Question;
use App\Models\Answer;
use App\Models\Grades;
use App\Models\Subjects;
use App\Models\Strands;
use App\Models\LearningsUnits;
use App\Models\LearningsObjectives;
use App\Models\StrandUnitsObjectivesMappings;
use Exception;
use Log;
use Illuminate\Database\Eloquent\ModelNotFoundException;


class QuestionsRepository
{
    use Common, ResponseFormat;

    /**
     * USE : Get all question list with answer
     */
    public function getAllQuestionList($items){
        try {
            // Default Parameter define
            $QuestionList = [];
            $QuestionList = Question::with('answers')
                            ->with('objectiveMapping')
                            ->sortable()
                            ->orderBy(cn::QUESTION_TABLE_ID_COL,'DESC')
                            ->paginate($items);
            return $QuestionList;
        } catch (\Exception $exception) {
            return back()->withError($exception->getMessage())->withInput();
        }
    }

    /**
     * USE : Store Questions
     */
    public function StoreQuestionAnswer($request){
        try {
            $result = [];
            $objectivesMapping = $objectivesMapping = $this->objectivesMapping($request);
            if(isset($objectivesMapping) && !empty($objectivesMapping->id)){
                $objectivesMappingId = $objectivesMapping->id;
            }

            $question_type_ids_String = implode(',',$request->question_type);
            $questionPostData = array(
                cn::QUESTION_OBJECTIVE_MAPPING_ID_COL       => $objectivesMappingId ?? 0,
                cn::QUESTION_QUESTION_CODE_COL              => $request->question_code,
                cn::QUESTION_QUESTION_UNIQUE_CODE_COL       => $this->UniqueQuestionCodeGenerate(),
                cn::QUESTION_MARKS_COL                      => 1,
                cn::QUESTION_BANK_UPDATED_BY_COL            => Auth::user()->{cn::USERS_ID_COL},
                cn::QUESTION_BANK_SCHOOL_ID_COL             => 1,
                cn::QUESTION_QUESTION_EN_COL                => $request->question_en,
                cn::QUESTION_QUESTION_CH_COL                => $request->question_ch,
                cn::QUESTION_DIFFICULTY_LEVEL_COL           => $request->dificulaty_level,
                cn::QUESTINON_STATUS_COL                    => ($request->save_draft) ? 0 : 1,
                cn::QUESTION_QUESTION_TYPE_COL              => $question_type_ids_String,
                cn::QUESTION_GENERAL_HINTS_EN               => $request->general_hints_en ?? null,
                cn::QUESTION_GENERAL_HINTS_CH               => $request->general_hints_ch ?? null,
                cn::QUESTION_E_COL                          => $request->field_e,
                cn::QUESTION_F_COL                          => $request->field_f,
                cn::QUESTION_G_COL                          =>  $request->field_g
            );
            $question = Question::create($questionPostData);
            if($question){
                $StoreAnswerData = array(
                    cn::ANSWER_QUESTION_ID_COL       => $question->id,
                    cn::ANSWER_ANSWER1_EN_COL        => $request->answer1_en,
                    cn::ANSWER_HINT_ANSWER1_EN_COL   => $request->hint_answer1_en,
                    cn::ANSWER_ANSWER2_EN_COL        => $request->answer2_en,
                    cn::ANSWER_HINT_ANSWER2_EN_COL   => $request->hint_answer2_en,
                    cn::ANSWER_ANSWER3_EN_COL        => $request->answer3_en,
                    cn::ANSWER_HINT_ANSWER3_EN_COL   => $request->hint_answer3_en,
                    cn::ANSWER_ANSWER4_EN_COL        => $request->answer4_en,
                    cn::ANSWER_HINT_ANSWER4_EN_COL   => $request->hint_answer4_en,
                    cn::ANSWER_ANSWER1_CH_COL        => $request->answer1_ch,
                    cn::ANSWER_HINT_ANSWER1_CH_COL   => $request->hint_answer1_ch,
                    cn::ANSWER_ANSWER2_CH_COL        => $request->answer2_ch,
                    cn::ANSWER_HINT_ANSWER2_CH_COL   => $request->hint_answer2_ch,
                    cn::ANSWER_ANSWER3_CH_COL        => $request->answer3_ch,
                    cn::ANSWER_HINT_ANSWER3_CH_COL   => $request->hint_answer3_ch,
                    cn::ANSWER_ANSWER4_CH_COL        => $request->answer4_ch,
                    cn::ANSWER_HINT_ANSWER4_CH_COL   => $request->hint_answer4_ch,
                    cn::ANSWER_HINT_ANSWER4_CH_COL   => $request->hint_answer4_ch,
                    cn::ANSWER_HINT_ANSWER4_CH_COL   => $request->hint_answer4_ch,
                    cn::ANSWER_CORRECT_ANSWER_EN_COL => $request->correct_answer_en,
                    cn::ANSWER_CORRECT_ANSWER_CH_COL => $request->correct_answer_ch
                );
                $result = Answer::create($StoreAnswerData);
            }
            return $result;
        } catch (\Exception $exception) {
            return back()->withError($exception->getMessage())->withInput();
        }
    }

    /**
     * USE : Update question & answer
     */
    public function UpdateQuestionAnswer($request, $id){
        try{
            $result = [];
            $objectivesMapping = $this->objectivesMapping($request);
            if(isset($objectivesMapping) && !empty($objectivesMapping->id)){
                $objectivesMappingId = $objectivesMapping->id;
            }
            $question_type_ids_String = implode(',',$request->question_type);
            $questionPostData = array(
                cn::QUESTION_OBJECTIVE_MAPPING_ID_COL   => $objectivesMappingId ?? 0,
                cn::QUESTION_QUESTION_CODE_COL          => $request->question_code,
                cn::QUESTION_QUESTION_UNIQUE_CODE_COL   => $this->UniqueQuestionCodeGenerate(),
                cn::QUESTION_MARKS_COL                  => 1,
                cn::QUESTION_BANK_UPDATED_BY_COL        => Auth::user()->{cn::USERS_ID_COL},
                cn::QUESTION_BANK_SCHOOL_ID_COL         => 1,
                cn::QUESTION_QUESTION_EN_COL            => $request->question_en,
                cn::QUESTION_QUESTION_CH_COL            => $request->question_ch,
                cn::QUESTION_DIFFICULTY_LEVEL_COL       => $request->dificulaty_level,
                cn::QUESTINON_STATUS_COL                => ($request->save_draft) ? 0 : 1,
                cn::QUESTION_QUESTION_TYPE_COL          => $question_type_ids_String,
                cn::QUESTION_GENERAL_HINTS_EN           => $request->general_hints_en ?? null,
                cn::QUESTION_GENERAL_HINTS_CH           => $request->general_hints_ch ?? null,
                cn::QUESTION_E_COL                      => $request->field_e,
                cn::QUESTION_F_COL                      => $request->field_f,
                cn::QUESTION_G_COL                      =>  $request->field_g
            );

            $question = Question::whereId($id)->update($questionPostData);
            if($question){
                $UpdateAnswerData = array(
                    cn::ANSWER_ANSWER1_EN_COL       => $request->answer1_en,
                    cn::ANSWER_HINT_ANSWER1_EN_COL  => $request->hint_answer1_en,
                    cn::ANSWER_ANSWER2_EN_COL       => $request->answer2_en,
                    cn::ANSWER_HINT_ANSWER2_EN_COL  => $request->hint_answer2_en,
                    cn::ANSWER_ANSWER3_EN_COL       => $request->answer3_en,
                    cn::ANSWER_HINT_ANSWER3_EN_COL  => $request->hint_answer3_en,
                    cn::ANSWER_ANSWER4_EN_COL       => $request->answer4_en,
                    cn::ANSWER_HINT_ANSWER4_EN_COL  => $request->hint_answer4_en,
                    cn::ANSWER_ANSWER1_CH_COL       => $request->answer1_ch,
                    cn::ANSWER_HINT_ANSWER1_CH_COL  => $request->hint_answer1_ch,
                    cn::ANSWER_ANSWER2_CH_COL       => $request->answer2_ch,
                    cn::ANSWER_HINT_ANSWER2_CH_COL  => $request->hint_answer2_ch,
                    cn::ANSWER_ANSWER3_CH_COL       => $request->answer3_ch,
                    cn::ANSWER_HINT_ANSWER3_CH_COL  => $request->hint_answer3_ch,
                    cn::ANSWER_ANSWER4_CH_COL       => $request->answer4_ch,
                    cn::ANSWER_HINT_ANSWER4_CH_COL  => $request->hint_answer4_ch,
                    cn::ANSWER_HINT_ANSWER4_CH_COL  => $request->hint_answer4_ch,
                    cn::ANSWER_HINT_ANSWER4_CH_COL  => $request->hint_answer4_ch,
                    cn::ANSWER_CORRECT_ANSWER_EN_COL => $request->correct_answer_en,
                    cn::ANSWER_CORRECT_ANSWER_CH_COL => $request->correct_answer_ch
                );
                $result = Answer::where(cn::ANSWER_QUESTION_ID_COL,$id)->update($UpdateAnswerData);
            }
            return $result;
        } catch (\Exception $exception) {
            return back()->withError($exception->getMessage())->withInput();
        }
    }

    /**
     * USE : Get Objective Mapping data
     */
    public function objectivesMapping($request){
        $objectivesMapping = StrandUnitsObjectivesMappings::where(cn::OBJECTIVES_MAPPINGS_GRADE_ID_COL,$request->grade_id)
                                ->where(cn::OBJECTIVES_MAPPINGS_SUBJECT_ID_COL,$request->subject_id)
                                ->where(cn::OBJECTIVES_MAPPINGS_STRAND_ID_COL, $request->strand_id)
                                ->where(cn::OBJECTIVES_MAPPINGS_LEARNING_UNIT_ID_COL, $request->learning_unit_id)
                                ->where(cn::OBJECTIVES_MAPPINGS_LEARNING_OBJECTIVES_ID_COL,$request->learning_objective_id)
                                ->first();
        return $objectivesMapping;
    }

    /**
     * USE : After validation failed return all parameters
     */
    public function validatorSendParams($request){
        try{
            $result = [];
            $SubjectsData = Subjects::where('code',cn::CODEMATHEMATICS)->first();

            if(isset($request->grade_id)){
                $subjectIds = StrandUnitsObjectivesMappings::where(cn::OBJECTIVES_MAPPINGS_GRADE_ID_COL,$request->grade_id)->pluck(cn::OBJECTIVES_MAPPINGS_SUBJECT_ID_COL);
                if(!empty($subjectIds)){
                    $subjectIds = array_unique($subjectIds->toArray());
                    $result['subjects'] = Subjects::whereIn(cn::SUBJECTS_ID_COL, $subjectIds)->get();
                }
            }

            // if(isset($request->subject_id)){
            //     $strandsIds = StrandUnitsObjectivesMappings::where(cn::OBJECTIVES_MAPPINGS_GRADE_ID_COL,$request->grade_id)
            //             ->where(cn::OBJECTIVES_MAPPINGS_SUBJECT_ID_COL,$request->subject_id)
            //             ->pluck(cn::OBJECTIVES_MAPPINGS_STRAND_ID_COL);
            //     if(!empty($strandsIds)){
            //         $strandsIds = array_unique($strandsIds->toArray());
            //         $result['strands'] = Strands::whereIn(cn::STRANDS_ID_COL, $strandsIds)->get();
            //     }
            // }
            
            $strandsIds = StrandUnitsObjectivesMappings::where(cn::OBJECTIVES_MAPPINGS_GRADE_ID_COL,$request->grade_id)
                    //->where(cn::OBJECTIVES_MAPPINGS_SUBJECT_ID_COL,$request->subject_id)
                    ->where(cn::OBJECTIVES_MAPPINGS_SUBJECT_ID_COL,$SubjectsData->id)
                    ->pluck(cn::OBJECTIVES_MAPPINGS_STRAND_ID_COL);
            if(!empty($strandsIds)){
                $strandsIds = array_unique($strandsIds->toArray());
                $result['strands'] = Strands::whereIn(cn::STRANDS_ID_COL, $strandsIds)->get();
            }

            if(isset($request->strand_id)){
                $learningUnitsIds = StrandUnitsObjectivesMappings::where(cn::OBJECTIVES_MAPPINGS_GRADE_ID_COL,$request->grade_id)
                //->where(cn::OBJECTIVES_MAPPINGS_SUBJECT_ID_COL,$request->subject_id)
                ->where(cn::OBJECTIVES_MAPPINGS_SUBJECT_ID_COL,$SubjectsData->id)
                ->where(cn::OBJECTIVES_MAPPINGS_STRAND_ID_COL,$request->strand_id)
                ->pluck(cn::OBJECTIVES_MAPPINGS_LEARNING_UNIT_ID_COL);
                if(!empty($learningUnitsIds)){
                    $learningUnitsIds = array_unique($learningUnitsIds->toArray());
                    $result['LearningUnits'] = LearningsUnits::whereIn(cn::LEARNING_UNITS_ID_COL, $learningUnitsIds)->get();
                }
            }

            if(isset($request->learning_unit_id)){
                $learningObjectivesIds = StrandUnitsObjectivesMappings::where(cn::OBJECTIVES_MAPPINGS_GRADE_ID_COL,$request->grade_id)
                //->where(cn::OBJECTIVES_MAPPINGS_SUBJECT_ID_COL,$request->subject_id)
                ->where(cn::OBJECTIVES_MAPPINGS_SUBJECT_ID_COL,$SubjectsData->id)
                ->where(cn::OBJECTIVES_MAPPINGS_STRAND_ID_COL,$request->strand_id)
                ->where(cn::OBJECTIVES_MAPPINGS_LEARNING_UNIT_ID_COL,$request->learning_unit_id)
                ->pluck(cn::OBJECTIVES_MAPPINGS_LEARNING_OBJECTIVES_ID_COL);
                if(!empty($learningObjectivesIds)){
                    $learningObjectivesIds = array_unique($learningObjectivesIds->toArray());
                    // $result['LearningObjectives'] = LearningsObjectives::whereIn(cn::LEARNING_OBJECTIVES_ID_COL, $learningObjectivesIds)->get();
                    $result['LearningObjectives'] = LearningsObjectives::IsAvailableQuestion()->whereIn(cn::LEARNING_OBJECTIVES_ID_COL, $learningObjectivesIds)->get();
                }
            }
            return $result;
        } catch (\Exception $exception) {
            return $exception->getMessage();
        }
    }
}