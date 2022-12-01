    
@if (session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
@endif
<div class="sm-right-detail-sec">
    <div class="container-fluid">
        <div class="row class-test-report-difficulty-graph">
            <div class="class-test-report-difficulty-sec">
                @php
                    $bg_correct_color='background-color:'.\App\Helpers\Helper::getGlobalConfiguration('question_correct_color');
                    $bg_incorrect_color='background-color:'.\App\Helpers\Helper::getGlobalConfiguration('question_incorrect_color');
                @endphp
                <h5>{{__('languages.questions_by_difficulties')}}</h5>

                @if(!empty($questionDifficultyGraph['easy']))
                <h6>{{__('languages.easy')}}</h6>
                <div class="progress question-difficulty-progressbar">
                    @if(!empty($questionDifficultyGraph['easy_correct_percentage']))
                    <div class="progress-bar" style="width:{{$questionDifficultyGraph['easy_correct_percentage']}}%;{{$bg_correct_color}};">
                        {{$questionDifficultyGraph['correct_easy']}}
                    </div>
                    @endif
                    @if(!empty($questionDifficultyGraph['easy_wrong_percentage']))
                    <div class="progress-bar" style="width:{{$questionDifficultyGraph['easy_wrong_percentage']}}%;{{$bg_incorrect_color}};">
                        {{ $questionDifficultyGraph['wrong_easy'] }}
                    </div>
                    @endif
                </div>
                @endif

                @if(!empty($questionDifficultyGraph['medium']))
                <h6>{{__('languages.medium')}}</h6>
                <div class="progress question-difficulty-progressbar">
                    @if(!empty($questionDifficultyGraph['medium_correct_percentage']))
                    <div class="progress-bar" style="width:{{$questionDifficultyGraph['medium_correct_percentage']}}%;{{$bg_correct_color}};">
                        {{$questionDifficultyGraph['correct_medium']}}
                    </div>
                    @endif
                    @if(!empty($questionDifficultyGraph['medium_wrong_percentage']))
                    <div class="progress-bar" style="width:{{$questionDifficultyGraph['medium_wrong_percentage']}}%;{{$bg_incorrect_color}};">
                        {{ $questionDifficultyGraph['wrong_medium'] }}
                    </div>
                    @endif
                </div>
                @endif

                @if(!empty($questionDifficultyGraph['hard']))
                <h6>{{__('languages.hard')}}</h6>
                <div class="progress question-difficulty-progressbar">
                    @if(!empty($questionDifficultyGraph['hard_correct_percentage']))
                    <div class="progress-bar" style="width:{{$questionDifficultyGraph['hard_correct_percentage']}}%;{{$bg_correct_color}};">
                        {{$questionDifficultyGraph['correct_hard']}}
                    </div>
                    @endif
                    @if(!empty($questionDifficultyGraph['hard_wrong_percentage']))
                    <div class="progress-bar" style="width:{{$questionDifficultyGraph['hard_wrong_percentage']}}%;{{$bg_incorrect_color}};">
                        {{ $questionDifficultyGraph['wrong_hard'] }}
                    </div>
                    @endif
                </div>
                @endif
            </div>
            <div class="question-attempt-second-cls">
                <h5>{{__('languages.speed')}}</h5>
                <p>{{$PerQuestionSpeed ?? 0}} {{__('Min/Qn')}}</p>
            </div>
            <div class="question-difficulty-color-cls">
                <span class="dot-color" style="background-color: {{ App\Helpers\Helper::getGlobalConfiguration('question_correct_color')}};border-radius: 50%;display: inline-block;"></span>
                <label>{{__('languages.correct_questions')}}</label>
                <span class="dot-color" style="background-color: {{ App\Helpers\Helper::getGlobalConfiguration('question_incorrect_color')}};border-radius: 50%;display: inline-block;"></span>
                <label>{{__('languages.incorrect_questions')}}</label>
            </div>
        </div>

        <!-- Start Student List -->
        @if($examType == 'singleTest')
            <div class="sm-add-user-sec card">
                <div class="select-option-sec pb-2 card-body">
                    @if(!empty($Questions))
                        @php $UserSelectedAnswers = json_decode($AttemptExamData->question_answers); 
                            $WeaknessList = array();
                            $WeaknessListWithId = array();
                        @endphp
                        @foreach($Questions as $key => $question)
                            @php
                            $AnswerNumber = array_filter($UserSelectedAnswers, function ($var) use($question){
                                if($var->question_id == $question['id']){
                                    return $var;
                                }
                            });
                            @endphp
                            <div class="row">
                                <div class="sm-que-option pl-3">
                                    <p class="sm-title bold">
                                        {{__('languages.result.q_no')}}: {{$loop->iteration}}
                                        {{-- Display Question code --}}
                                        {{__('languages.question_code')}} : {{ $question->naming_structure_code }}

                                        {{-- Display Question types and with color code --}}
                                        @if($question->dificulaty_level == 1)
                                            @php
                                            $question_difficulty_easy=App\Helpers\Helper::getGlobalConfiguration('question_difficulty_easy');
                                            $question_difficulty_easy=json_decode($question_difficulty_easy,true);
                                            @endphp
                                            @if(isset($question_difficulty_easy['color']) && !empty($question_difficulty_easy['color']))
                                            <span class="ml-5"> {{__('languages.easy')}}</span> <span class="dot-color" style="background-color:{{ $question_difficulty_easy['color']; }};border-radius: 50%;display: inline-block;top: 5px;position: relative;"></span>
                                            @endif
                                        @elseif($question->dificulaty_level == 2)
                                            @php
                                            $question_difficulty_medium=App\Helpers\Helper::getGlobalConfiguration('question_difficulty_medium');
                                            $question_difficulty_medium=json_decode($question_difficulty_medium,true);
                                            @endphp
                                            @if(isset($question_difficulty_medium['color']) && !empty($question_difficulty_medium['color']))
                                            <span class="ml-5"> {{__('languages.medium')}}</span> <span class="dot-color" style="background-color:{{ $question_difficulty_medium['color']; }};border-radius: 50%;display: inline-block;top: 5px;position: relative;"></span>
                                            @endif
                                        @elseif($question->dificulaty_level == 3)
                                            @php
                                            $question_difficulty_hard=App\Helpers\Helper::getGlobalConfiguration('question_difficulty_hard');
                                            $question_difficulty_hard=json_decode($question_difficulty_hard,true);
                                            @endphp
                                            @if(isset($question_difficulty_hard['color']) && !empty($question_difficulty_hard['color']))
                                            <span class="ml-5">{{__('languages.hard')}}</span> <span class="dot-color" style="background-color:{{ $question_difficulty_hard['color']; }};border-radius: 50%;display: inline-block;top: 5px;position: relative;"></span>
                                            @endif
                                        @endif
                                        {{-- Display Natural difficulties & Normalized difficulties --}}
                                        <span class="ml-5">{{__('languages.difficulty')}}: {{round($question['difficultyValue']['natural_difficulty'],2)}} ({{$question['difficultyValue']['normalized_difficulty']}}%)</span>
                                    </p>
                                    <div class="sm-que pl-2">
                                        <p><?php echo $question->{'question_en'}; ?></p>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <h6>My Class</h6>
                                            @if(isset($question->answers->{'answer1_'.$AttemptExamData->language}))
                                            <div class="sm-ans pl-2 pb-2">
                                                <input type="radio" name="ans_que_{{$question->id}}" value="1" class="radio mr-2" <?php if(isset($AnswerNumber[key($AnswerNumber)]) && $AnswerNumber[key($AnswerNumber)]->answer == 1){ echo 'checked';} ?> disabled>
                                                <div class="answer-title mr-2 <?php if($question->answers->{'correct_answer_'.$AttemptExamData->language} == 1){ echo 'correct-answer';}else{ echo 'incorrect-answer';} ?>" style="@if($question->answers->{'correct_answer_'.$AttemptExamData->language} == 1){{$bg_correct_color}}@else{{$bg_incorrect_color}} @endif">A</div>
                                                <div class="progress">
                                                    <div class="progress-bar @if($question->answers->{'correct_answer_'.$AttemptExamData->language} === 1) ans-correct @else ans-incorrect @endif" role="progressbar"  aria-valuenow="{{$percentageOfAnswer[$question->id][1]}}" aria-valuemin="0" aria-valuemax="100" style="@if($question->answers->{'correct_answer_'.$AttemptExamData->language} == 1){{$bg_correct_color}}@else{{$bg_incorrect_color}} @endif;width:{{$percentageOfAnswer[$question->id][1]}}%">
                                                        <div class="anser-detail pl-2">
                                                            <?php echo $question->answers->{'answer1_'.$AttemptExamData->language}; ?>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="answer-progress">
                                                    <p class="progress-percentage">{{$percentageOfAnswer[$question->id][1]}}%</p>
                                                </div>
                                            </div>
                                            @endif
                                            
                                            @if(isset($question->answers->{'answer2_'.$AttemptExamData->language}))
                                            <div class="sm-ans pl-2 pb-2">
                                                <input type="radio" name="ans_que_{{$question->id}}" value="2" class="radio mr-2" <?php if(isset($AnswerNumber[key($AnswerNumber)]) && $AnswerNumber[key($AnswerNumber)]->answer == 2){ echo 'checked';} ?> disabled>
                                                <div class="answer-title mr-2 @if($question->answers->{'correct_answer_'.$AttemptExamData->language} === 2) correct-answer @else incorrect-answer @endif" style="@if($question->answers->{'correct_answer_'.$AttemptExamData->language} == 2){{$bg_correct_color}}@else{{$bg_incorrect_color}} @endif">B</div>
                                                <div class="progress">
                                                    <div class="progress-bar @if($question->answers->{'correct_answer_'.$AttemptExamData->language} === 2) ans-correct @else ans-incorrect @endif" role="progressbar"  aria-valuenow="{{$percentageOfAnswer[$question->id][2]}}" aria-valuemin="0" aria-valuemax="100" style="@if($question->answers->{'correct_answer_'.$AttemptExamData->language} == 2){{$bg_correct_color}}@else{{$bg_incorrect_color}} @endif;width:{{$percentageOfAnswer[$question->id][2]}}%">
                                                        <div class="anser-detail pl-2">
                                                            <?php echo $question->answers->{'answer2_'.$AttemptExamData->language}; ?>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="answer-progress">
                                                    <p class="progress-percentage">{{$percentageOfAnswer[$question->id][2]}}%</p>
                                                </div>
                                            </div>
                                            @endif
                                            
                                            @if(isset($question->answers->{'answer3_'.$AttemptExamData->language}))
                                            <div class="sm-ans pl-2 pb-2">
                                                <input type="radio" name="ans_que_{{$question->id}}" value="3" class="radio mr-2" <?php if(isset($AnswerNumber[key($AnswerNumber)]) && $AnswerNumber[key($AnswerNumber)]->answer == 3){ echo 'checked';} ?> disabled>
                                                <div class="answer-title mr-2 @if($question->answers->{'correct_answer_'.$AttemptExamData->language} === 3) correct-answer @else incorrect-answer @endif" style="@if($question->answers->{'correct_answer_'.$AttemptExamData->language} == 3){{$bg_correct_color}}@else{{$bg_incorrect_color}} @endif">C</div>
                                                    <div class="progress">
                                                        <div class="progress-bar @if($question->answers->{'correct_answer_'.$AttemptExamData->language} === 3) ans-correct @else ans-incorrect @endif" role="progressbar"  aria-valuenow="{{$percentageOfAnswer[$question->id][3]}}" aria-valuemin="0" aria-valuemax="100" style="@if($question->answers->{'correct_answer_'.$AttemptExamData->language} == 3){{$bg_correct_color}}@else{{$bg_incorrect_color}} @endif;width:{{$percentageOfAnswer[$question->id][3]}}%">
                                                            <div class="anser-detail pl-2">
                                                                <?php echo $question->answers->{'answer3_'.$AttemptExamData->language}; ?>
                                                            </div>
                                                        </div>
                                                    </div>
                                                <div class="answer-progress">
                                                    <p class="progress-percentage">{{$percentageOfAnswer[$question->id][3]}}%</p>
                                                </div>
                                            </div>
                                            @endif

                                            @if(isset($question->answers->{'answer4_'.$AttemptExamData->language}))
                                                <div class="sm-ans pl-2 pb-2">
                                                    <input type="radio" name="ans_que_{{$question->id}}" value="4" class="radio mr-2" <?php if(isset($AnswerNumber[key($AnswerNumber)]) && $AnswerNumber[key($AnswerNumber)]->answer == 4){ echo 'checked';} ?> disabled>
                                                    <div class="answer-title mr-2 @if($question->answers->{'correct_answer_'.$AttemptExamData->language} === 4) correct-answer @else incorrect-answer @endif" style="@if($question->answers->{'correct_answer_'.$AttemptExamData->language} == 4){{$bg_correct_color}}@else{{$bg_incorrect_color}} @endif">D</div>
                                                    <div class="progress">
                                                        <div class="progress-bar @if($question->answers->{'correct_answer_'.$AttemptExamData->language} === 4) ans-correct @else ans-incorrect @endif" role="progressbar"  aria-valuemin="0" aria-valuemax="100" style="@if($question->answers->{'correct_answer_'.$AttemptExamData->language} == 4){{$bg_correct_color}}@else{{$bg_incorrect_color}} @endif;width:{{$percentageOfAnswer[$question->id][4]}}%">
                                                            <div class="anser-detail pl-2">
                                                                <?php echo $question->answers->{'answer4_'.$AttemptExamData->language}; ?>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="answer-progress">
                                                        <p class="progress-percentage">{{$percentageOfAnswer[$question->id][4]}}%</p>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="col-md-4">
                                            <h6>My School</h6>
                                            @if(isset($question->answers->{'answer1_'.$AttemptExamData->language}))
                                            <div class="sm-ans pl-2 pb-2">
                                                <div class="answer-title mr-2 <?php if($question->answers->{'correct_answer_'.$AttemptExamData->language} == 1){ echo 'correct-answer';}else{ echo 'incorrect-answer';} ?>" style="@if($question->answers->{'correct_answer_'.$AttemptExamData->language} == 1){{$bg_correct_color}}@else{{$bg_incorrect_color}} @endif">A</div>
                                                <div class="progress">
                                                    <div class="progress-bar @if($question->answers->{'correct_answer_'.$AttemptExamData->language} === 1) ans-correct @else ans-incorrect @endif" role="progressbar"  aria-valuenow="{{$percentageOfAnswerSchool[$question->id][1]}}" aria-valuemin="0" aria-valuemax="100" style="@if($question->answers->{'correct_answer_'.$AttemptExamData->language} == 1){{$bg_correct_color}}@else{{$bg_incorrect_color}} @endif;width:{{$percentageOfAnswerSchool[$question->id][1]}}%">
                                                        <div class="anser-detail pl-2">
                                                            <?php echo $question->answers->{'answer1_'.$AttemptExamData->language}; ?>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="answer-progress">
                                                    <p class="progress-percentage">{{$percentageOfAnswerSchool[$question->id][1]}}%</p>
                                                </div>
                                            </div>
                                            @endif
                                            
                                            @if(isset($question->answers->{'answer2_'.$AttemptExamData->language}))
                                            <div class="sm-ans pl-2 pb-2">
                                                <div class="answer-title mr-2 @if($question->answers->{'correct_answer_'.$AttemptExamData->language} === 2) correct-answer @else incorrect-answer @endif" style="@if($question->answers->{'correct_answer_'.$AttemptExamData->language} == 2){{$bg_correct_color}}@else{{$bg_incorrect_color}} @endif">B</div>
                                                <div class="progress">
                                                    <div class="progress-bar @if($question->answers->{'correct_answer_'.$AttemptExamData->language} === 2) ans-correct @else ans-incorrect @endif" role="progressbar"  aria-valuenow="{{$percentageOfAnswerSchool[$question->id][2]}}" aria-valuemin="0" aria-valuemax="100" style="@if($question->answers->{'correct_answer_'.$AttemptExamData->language} == 2){{$bg_correct_color}}@else{{$bg_incorrect_color}} @endif;width:{{$percentageOfAnswerSchool[$question->id][2]}}%">
                                                        <div class="anser-detail pl-2">
                                                            <?php echo $question->answers->{'answer2_'.$AttemptExamData->language}; ?>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="answer-progress">
                                                    <p class="progress-percentage">{{$percentageOfAnswerSchool[$question->id][2]}}%</p>
                                                </div>
                                            </div>
                                            @endif
                                            
                                            @if(isset($question->answers->{'answer3_'.$AttemptExamData->language}))
                                            <div class="sm-ans pl-2 pb-2">
                                                <div class="answer-title mr-2 @if($question->answers->{'correct_answer_'.$AttemptExamData->language} === 3) correct-answer @else incorrect-answer @endif" style="@if($question->answers->{'correct_answer_'.$AttemptExamData->language} == 3){{$bg_correct_color}}@else{{$bg_incorrect_color}} @endif">C</div>
                                                    <div class="progress">
                                                        <div class="progress-bar @if($question->answers->{'correct_answer_'.$AttemptExamData->language} === 3) ans-correct @else ans-incorrect @endif" role="progressbar"  aria-valuenow="{{$percentageOfAnswerSchool[$question->id][3]}}" aria-valuemin="0" aria-valuemax="100" style="@if($question->answers->{'correct_answer_'.$AttemptExamData->language} == 3){{$bg_correct_color}}@else{{$bg_incorrect_color}} @endif;width:{{$percentageOfAnswerSchool[$question->id][3]}}%">
                                                            <div class="anser-detail pl-2">
                                                                <?php echo $question->answers->{'answer3_'.$AttemptExamData->language}; ?>
                                                            </div>
                                                        </div>
                                                    </div>
                                                <div class="answer-progress">
                                                    <p class="progress-percentage">{{$percentageOfAnswerSchool[$question->id][3]}}%</p>
                                                </div>
                                            </div>
                                            @endif

                                            @if(isset($question->answers->{'answer4_'.$AttemptExamData->language}))
                                                <div class="sm-ans pl-2 pb-2">
                                                    <div class="answer-title mr-2 @if($question->answers->{'correct_answer_'.$AttemptExamData->language} === 4) correct-answer @else incorrect-answer @endif" style="@if($question->answers->{'correct_answer_'.$AttemptExamData->language} == 4){{$bg_correct_color}}@else{{$bg_incorrect_color}} @endif">D</div>
                                                    <div class="progress">
                                                        <div class="progress-bar @if($question->answers->{'correct_answer_'.$AttemptExamData->language} === 4) ans-correct @else ans-incorrect @endif" role="progressbar"  aria-valuemin="0" aria-valuemax="100" style="@if($question->answers->{'correct_answer_'.$AttemptExamData->language} == 4){{$bg_correct_color}}@else{{$bg_incorrect_color}} @endif;width:{{$percentageOfAnswerSchool[$question->id][4]}}%">
                                                            <div class="anser-detail pl-2">
                                                                <?php echo $question->answers->{'answer4_'.$AttemptExamData->language}; ?>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="answer-progress">
                                                        <p class="progress-percentage">{{$percentageOfAnswerSchool[$question->id][4]}}%</p>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="col-md-4">
                                            <h6>All Schools</h6>
                                            @if(isset($question->answers->{'answer1_'.$AttemptExamData->language}))
                                            <div class="sm-ans pl-2 pb-2">
                                                <div class="answer-title mr-2 <?php if($question->answers->{'correct_answer_'.$AttemptExamData->language} == 1){ echo 'correct-answer';}else{ echo 'incorrect-answer';} ?>" style="@if($question->answers->{'correct_answer_'.$AttemptExamData->language} == 1){{$bg_correct_color}}@else{{$bg_incorrect_color}} @endif">A</div>
                                                <div class="progress">
                                                    <div class="progress-bar @if($question->answers->{'correct_answer_'.$AttemptExamData->language} === 1) ans-correct @else ans-incorrect @endif" role="progressbar"  aria-valuenow="{{$percentageOfAnswerAllSchool[$question->id][1]}}" aria-valuemin="0" aria-valuemax="100" style="@if($question->answers->{'correct_answer_'.$AttemptExamData->language} == 1){{$bg_correct_color}}@else{{$bg_incorrect_color}} @endif;width:{{$percentageOfAnswerAllSchool[$question->id][1]}}%">
                                                        <div class="anser-detail pl-2">
                                                            <?php echo $question->answers->{'answer1_'.$AttemptExamData->language}; ?>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="answer-progress">
                                                    <p class="progress-percentage">{{$percentageOfAnswerAllSchool[$question->id][1]}}%</p>
                                                </div>
                                            </div>
                                            @endif
                                            
                                            @if(isset($question->answers->{'answer2_'.$AttemptExamData->language}))
                                            <div class="sm-ans pl-2 pb-2">
                                                <div class="answer-title mr-2 @if($question->answers->{'correct_answer_'.$AttemptExamData->language} === 2) correct-answer @else incorrect-answer @endif" style="@if($question->answers->{'correct_answer_'.$AttemptExamData->language} == 2){{$bg_correct_color}}@else{{$bg_incorrect_color}} @endif">B</div>
                                                <div class="progress">
                                                    <div class="progress-bar @if($question->answers->{'correct_answer_'.$AttemptExamData->language} === 2) ans-correct @else ans-incorrect @endif" role="progressbar"  aria-valuenow="{{$percentageOfAnswerAllSchool[$question->id][2]}}" aria-valuemin="0" aria-valuemax="100" style="@if($question->answers->{'correct_answer_'.$AttemptExamData->language} == 2){{$bg_correct_color}}@else{{$bg_incorrect_color}} @endif;width:{{$percentageOfAnswerAllSchool[$question->id][2]}}%">
                                                        <div class="anser-detail pl-2">
                                                            <?php echo $question->answers->{'answer2_'.$AttemptExamData->language}; ?>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="answer-progress">
                                                    <p class="progress-percentage">{{$percentageOfAnswerAllSchool[$question->id][2]}}%</p>
                                                </div>
                                            </div>
                                            @endif
                                            
                                            @if(isset($question->answers->{'answer3_'.$AttemptExamData->language}))
                                            <div class="sm-ans pl-2 pb-2">
                                                <div class="answer-title mr-2 @if($question->answers->{'correct_answer_'.$AttemptExamData->language} === 3) correct-answer @else incorrect-answer @endif" style="@if($question->answers->{'correct_answer_'.$AttemptExamData->language} == 3){{$bg_correct_color}}@else{{$bg_incorrect_color}} @endif">C</div>
                                                    <div class="progress">
                                                        <div class="progress-bar @if($question->answers->{'correct_answer_'.$AttemptExamData->language} === 3) ans-correct @else ans-incorrect @endif" role="progressbar"  aria-valuenow="{{$percentageOfAnswerAllSchool[$question->id][3]}}" aria-valuemin="0" aria-valuemax="100" style="@if($question->answers->{'correct_answer_'.$AttemptExamData->language} == 3){{$bg_correct_color}}@else{{$bg_incorrect_color}} @endif;width:{{$percentageOfAnswerAllSchool[$question->id][3]}}%">
                                                            <div class="anser-detail pl-2">
                                                                <?php echo $question->answers->{'answer3_'.$AttemptExamData->language}; ?>
                                                            </div>
                                                        </div>
                                                    </div>
                                                <div class="answer-progress">
                                                    <p class="progress-percentage">{{$percentageOfAnswerAllSchool[$question->id][3]}}%</p>
                                                </div>
                                            </div>
                                            @endif

                                            @if(isset($question->answers->{'answer4_'.$AttemptExamData->language}))
                                                <div class="sm-ans pl-2 pb-2">
                                                    <div class="answer-title mr-2 @if($question->answers->{'correct_answer_'.$AttemptExamData->language} === 4) correct-answer @else incorrect-answer @endif" style="@if($question->answers->{'correct_answer_'.$AttemptExamData->language} == 4){{$bg_correct_color}}@else{{$bg_incorrect_color}} @endif">D</div>
                                                    <div class="progress">
                                                        <div class="progress-bar @if($question->answers->{'correct_answer_'.$AttemptExamData->language} === 4) ans-correct @else ans-incorrect @endif" role="progressbar"  aria-valuemin="0" aria-valuemax="100" style="@if($question->answers->{'correct_answer_'.$AttemptExamData->language} == 4){{$bg_correct_color}}@else{{$bg_incorrect_color}} @endif;width:{{$percentageOfAnswerAllSchool[$question->id][4]}}%">
                                                            <div class="anser-detail pl-2">
                                                                <?php echo $question->answers->{'answer4_'.$AttemptExamData->language}; ?>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="answer-progress">
                                                        <p class="progress-percentage">{{$percentageOfAnswerAllSchool[$question->id][4]}}%</p>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="sm-answer pl-5 pt-2">
                                    <button type="button" class="btn btn-sm btn-success question_graph" data-graphtype="currentstudent" data-studentid="{{ $AttemptExamData->student_id }}" data-questionid="{{ $question->id }}" data-examid="{{ $AttemptExamData->exam_id }}">
                                        <i class="fa fa-bar-chart" aria-hidden="true"></i>{{ __('languages.question_analysis') }}
                                    </button>

                                    <?php if(isset($AnswerNumber[key($AnswerNumber)]) && $AnswerNumber[key($AnswerNumber)]->answer == $question->answers->{'correct_answer_'.$AttemptExamData->language}){ ?>
                                    <span class="badge badge-success">{{__('languages.result.correct_answer')}}</span>
                                    <?php }else{ ?>
                                    <span class="badge badge-danger">{{__('languages.result.incorrect_answer')}}</span>
                                    <?php
                                    $nodeId=0;
                                    if(isset($AnswerNumber[key($AnswerNumber)])){
                                        $nodeId = $question->answers->{'answer'.$AnswerNumber[key($AnswerNumber)]->answer.'_node_relation_id_'.$AttemptExamData->language};
                                        if(empty($nodeId)){
                                            $nodeId = App\Helpers\Helper::getWeaknessNodeId($question->id, $AnswerNumber[key($AnswerNumber)]->answer);
                                        }
                                    } ?>
                                    <h6 class="mt-3"><b>{{__('languages.report.weakness')}}:</b>
                                        @if($AttemptExamData->language=='ch')
                                            @if($nodeId != 0 && isset($nodeWeaknessListCh[$nodeId]))
                                                @php
                                                $WeaknessList[] = $nodeWeaknessListCh[$nodeId];
                                                $WeaknessListWithId[$nodeId] = $nodeWeaknessListCh[$nodeId];
                                                @endphp
                                                {{$nodeWeaknessListCh[$nodeId]}}
                                            @endif
                                        @else
                                            @if($nodeId!=0 && isset($nodeWeaknessList[$nodeId]))
                                                @php
                                                $WeaknessList[]=$nodeWeaknessList[$nodeId];
                                                $WeaknessListWithId[$nodeId]=$nodeWeaknessList[$nodeId];
                                                @endphp
                                                {{$nodeWeaknessList[$nodeId]}}
                                            @endif
                                        @endif
                                    </h6>
                                    <?php } ?>
                                </div>
                            </div>
                        <hr>
                        @endforeach
                    @endif
                    @php
                        $KeyImprovementData = '';
                        $KeyWeaknessData = '';
                        $checkImprovement = 1; 
                    @endphp

                    @foreach($AllWeakness as $WeaknessKey => $WeaknessNof)
                        @if(isset($WeaknessListWithId[$WeaknessKey]))
                            @php
                                if($checkImprovement<=2){
                                    $KeyImprovementData.='<li style="list-style:disc;">'. $WeaknessListWithId[$WeaknessKey].'</li>';
                                }else{
                                    $KeyWeaknessData.='<li style="list-style:disc;">'. $WeaknessListWithId[$WeaknessKey].'</li>';
                                }
                                $checkImprovement++;
                            @endphp
                        @endif
                    @endforeach
                
                    <div id="accordionImprovement" class="weakness_result_list">
                        <div class="card1">
                            <div class="card-header1" id="heading">
                                <h5 class="mb-0">
                                <button class="btn btn-link collapsed" data-toggle="collapse" data-target="#collapseImprovement" aria-expanded="false" aria-controls="collapse">
                                    <h6 class="text-dark"><b><i class="fa fa-plus mr-2"></i>{{ __('languages.key_improvement_points') }}</b></h6 >
                                </button>
                                </h5>
                            </div>
                            <div id="collapseImprovement" class="collapse" aria-labelledby="heading" data-parent="#accordionImprovement">
                                <ul class="list-unstyled ml-5">
                                    @if($KeyImprovementData!="")
                                        {!! $KeyImprovementData !!}
                                    @else
                                        <li>{{ __('languages.no_key_improvement_point_available') }}</li>
                                    @endif
                                </ul>
                            </div>
                        </div>
                    </div> 

                    <div id="accordion" class="weakness_result_list">
                        <div class="card1">
                            <div class="card-header1" id="headingTwo">
                                <h5 class="mb-0">
                                <button class="btn btn-link collapsed" data-toggle="collapse" data-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                                    <h6 class="text-dark"><b><i class="fa fa-plus mr-2"></i>{{__('languages.report.weakness')}}</b></h6>
                                </button>
                                </h5>
                            </div>
                            <div id="collapseTwo" class="collapse" aria-labelledby="headingTwo" data-parent="#accordion">
                                <ul class="list-unstyled ml-5">
                                    @if($KeyWeaknessData!="")
                                    <a href="{{route('getStudentExamList')}}">{!! $KeyWeaknessData !!}</a>
                                    @else
                                        <li>{{__('languages.report.no_weakness_available')}}</li>
                                    @endif
                                </ul>
                            </div>
                        </div>
                    </div>
                </div> 
            </div>
        @endif
        
        <!-- Start IS Group Test -->
        @if($examType == 'groupTest')
            <div class="sm-add-user-sec card">
                <div class="select-option-sec pb-2 card-body">
                    @if(!empty($data))
                        @php $i = 0; $total_question =0; $total_correct_answer = 0; $total_incorrect_answer = 0; @endphp
                        @foreach($data as $exams)
                            @php 
                                $AttemptExamData = $exams['AttemptExamData'];
                                $ServerDetails = json_decode($AttemptExamData->server_details);
                                $total_question = $total_question + count($exams['Questions']);
                                $total_correct_answer = $total_correct_answer + $AttemptExamData->total_correct_answers;
                                $total_incorrect_answer = $total_incorrect_answer + $AttemptExamData->total_wrong_answers;
                            @endphp
                        @endforeach
                        @php
                        $WeaknessList = array();
                        $WeaknessListWithId = array();
                        @endphp
                        @foreach($data as $exams)
                            @if(!empty($exams['Questions']))
                                @php
                                $AttemptExamData = $exams['AttemptExamData'];
                                $UserSelectedAnswers = json_decode($AttemptExamData->question_answers);
                                @endphp
                                @foreach($exams['Questions'] as $key => $question)
                                    @php
                                    $AnswerNumber = array_filter($UserSelectedAnswers, function ($var) use($question){
                                        if($var->question_id == $question['id']){
                                            return $var;
                                        }
                                    });
                                    @endphp
                                    <div class="row">
                                        <div class="sm-que-option pl-3">
                                            <p class="sm-title bold">
                                                {{__('languages.result.q_no')}}: {{++$i}}
                                                {{-- Display Question code --}}
                                                {{__('languages.question_code')}} : {{ $question->naming_structure_code }}
                                                {{-- Display Question types and with color code --}}
                                                @if($question->dificulaty_level == 1)
                                                    @php
                                                    $question_difficulty_easy=App\Helpers\Helper::getGlobalConfiguration('question_difficulty_easy');
                                                    $question_difficulty_easy=json_decode($question_difficulty_easy,true);
                                                    @endphp
                                                    @if(isset($question_difficulty_easy['color']) && !empty($question_difficulty_easy['color']))
                                                    <span class="ml-5"> {{__('languages.easy')}}</span> <span class="dot-color" style="background-color:{{ $question_difficulty_easy['color']; }};border-radius: 50%;display: inline-block;top: 5px;position: relative;"></span>
                                                    @endif
                                                @elseif($question->dificulaty_level == 2)
                                                    @php
                                                    $question_difficulty_medium=App\Helpers\Helper::getGlobalConfiguration('question_difficulty_medium');
                                                    $question_difficulty_medium=json_decode($question_difficulty_medium,true);
                                                    @endphp
                                                    @if(isset($question_difficulty_medium['color']) && !empty($question_difficulty_medium['color']))
                                                    <span class="ml-5"> {{__('languages.medium')}}</span> <span class="dot-color" style="background-color:{{ $question_difficulty_medium['color']; }};border-radius: 50%;display: inline-block;top: 5px;position: relative;"></span>
                                                    @endif
                                                @elseif($question->dificulaty_level == 3)
                                                    @php
                                                    $question_difficulty_hard=App\Helpers\Helper::getGlobalConfiguration('question_difficulty_hard');
                                                    $question_difficulty_hard=json_decode($question_difficulty_hard,true);
                                                    @endphp
                                                    @if(isset($question_difficulty_hard['color']) && !empty($question_difficulty_hard['color']))
                                                    <span class="ml-5">{{__('languages.hard')}}</span> <span class="dot-color" style="background-color:{{ $question_difficulty_hard['color']; }};border-radius: 50%;display: inline-block;top: 5px;position: relative;"></span>
                                                    @endif
                                                @endif
                                                {{-- Display Natural difficulties & Normalized difficulties --}}
                                                <span class="ml-5">{{__('languages.difficulty')}}: {{round($question['difficultyValue']['natural_difficulty'],2)}} ({{$question['difficultyValue']['normalized_difficulty']}}%)</span>
                                            </p>
                                            <div class="sm-que pl-2">
                                                <p><?php echo $question->{'question_en'}; ?></p>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-4">
                                                    @if(isset($question->answers->{'answer1_'.$AttemptExamData->language}))
                                                    <div class="sm-ans pl-2 pb-2">
                                                        <input type="radio" name="ans_que_{{$question->id}}" value="1" class="radio mr-2" <?php if(isset($AnswerNumber[key($AnswerNumber)]) && $AnswerNumber[key($AnswerNumber)]->answer == 1){ echo 'checked';} ?> disabled>
                                                        <div class="answer-title mr-2 <?php if($question->answers->{'correct_answer_'.$AttemptExamData->language} == 1){ echo 'correct-answer';}else{ echo 'incorrect-answer';} ?>" style="@if($question->answers->{'correct_answer_'.$AttemptExamData->language} == 1){{$bg_correct_color}}@else{{$bg_incorrect_color}} @endif">A</div>
                                                        <div class="progress">
                                                            <div class="progress-bar @if($question->answers->{'correct_answer_'.$AttemptExamData->language} === 1) ans-correct @else ans-incorrect @endif" role="progressbar"  aria-valuenow="{{$exams['percentageOfAnswer'][$question->id][1]}}" aria-valuemin="0" aria-valuemax="100" style="@if($question->answers->{'correct_answer_'.$AttemptExamData->language} == 1){{$bg_correct_color}}@else{{$bg_incorrect_color}} @endif;width:{{$exams['percentageOfAnswer'][$question->id][1]}}%">
                                                                <div class="anser-detail pl-2">
                                                                    <?php echo $question->answers->{'answer1_'.$AttemptExamData->language}; ?>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="answer-progress">
                                                            <p class="progress-percentage">{{$exams['percentageOfAnswer'][$question->id][1]}}%</p>
                                                        </div>
                                                    </div>
                                                    @endif
                                                    
                                                    @if(isset($question->answers->{'answer2_'.$AttemptExamData->language}))
                                                    <div class="sm-ans pl-2 pb-2">
                                                        <input type="radio" name="ans_que_{{$question->id}}" value="2" class="radio mr-2" <?php if(isset($AnswerNumber[key($AnswerNumber)]) && $AnswerNumber[key($AnswerNumber)]->answer == 2){ echo 'checked';} ?> disabled>
                                                        <div class="answer-title mr-2 @if($question->answers->{'correct_answer_'.$AttemptExamData->language} === 2) correct-answer @else incorrect-answer @endif" style="@if($question->answers->{'correct_answer_'.$AttemptExamData->language} == 2){{$bg_correct_color}}@else{{$bg_incorrect_color}} @endif">B</div>
                                                        <div class="progress">
                                                            <div class="progress-bar @if($question->answers->{'correct_answer_'.$AttemptExamData->language} === 2) ans-correct @else ans-incorrect @endif" role="progressbar"  aria-valuenow="{{$exams['percentageOfAnswer'][$question->id][2]}}" aria-valuemin="0" aria-valuemax="100" style="@if($question->answers->{'correct_answer_'.$AttemptExamData->language} == 2){{$bg_correct_color}}@else{{$bg_incorrect_color}} @endif;width:{{$exams['percentageOfAnswer'][$question->id][2]}}%">
                                                                <div class="anser-detail pl-2">
                                                                    <?php echo $question->answers->{'answer2_'.$AttemptExamData->language}; ?>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="answer-progress">
                                                            <p class="progress-percentage">{{$exams['percentageOfAnswer'][$question->id][2]}}%</p>
                                                        </div>
                                                    </div>
                                                    @endif

                                                    @if(isset($question->answers->{'answer3_'.$AttemptExamData->language}))
                                                    <div class="sm-ans pl-2 pb-2">
                                                        <input type="radio" name="ans_que_{{$question->id}}" value="3" class="radio mr-2" <?php if(isset($AnswerNumber[key($AnswerNumber)]) && $AnswerNumber[key($AnswerNumber)]->answer == 3){ echo 'checked';} ?> disabled>
                                                        <div class="answer-title mr-2 @if($question->answers->{'correct_answer_'.$AttemptExamData->language} === 3) correct-answer @else incorrect-answer @endif" style="@if($question->answers->{'correct_answer_'.$AttemptExamData->language} == 3){{$bg_correct_color}}@else{{$bg_incorrect_color}} @endif">C</div>
                                                        <div class="progress">
                                                            <div class="progress-bar @if($question->answers->{'correct_answer_'.$AttemptExamData->language} === 3) ans-correct @else ans-incorrect @endif" role="progressbar"  aria-valuenow="{{$exams['percentageOfAnswer'][$question->id][3]}}" aria-valuemin="0" aria-valuemax="100" style="@if($question->answers->{'correct_answer_'.$AttemptExamData->language} == 3){{$bg_correct_color}}@else{{$bg_incorrect_color}} @endif;width:{{$exams['percentageOfAnswer'][$question->id][3]}}%">
                                                                <div class="anser-detail pl-2">
                                                                    <?php echo $question->answers->{'answer3_'.$AttemptExamData->language}; ?>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="answer-progress">
                                                            <p class="progress-percentage">{{$exams['percentageOfAnswer'][$question->id][3]}}%</p>
                                                        </div>
                                                    </div>
                                                    @endif

                                                    @if(isset($question->answers->{'answer4_'.$AttemptExamData->language}))
                                                    <div class="sm-ans pl-2 pb-2">
                                                        <input type="radio" name="ans_que_{{$question->id}}" value="4" class="radio mr-2" <?php if(isset($AnswerNumber[key($AnswerNumber)]) && $AnswerNumber[key($AnswerNumber)]->answer == 4){ echo 'checked';} ?> disabled>
                                                        <div class="answer-title mr-2 @if($question->answers->{'correct_answer_'.$AttemptExamData->language} === 4) correct-answer @else incorrect-answer @endif" style="@if($question->answers->{'correct_answer_'.$AttemptExamData->language} == 4){{$bg_correct_color}}@else{{$bg_incorrect_color}} @endif">D</div>
                                                        <div class="progress">
                                                            <div class="progress-bar @if($question->answers->{'correct_answer_'.$AttemptExamData->language} === 4) ans-correct @else ans-incorrect @endif" role="progressbar"  aria-valuenow="{{$exams['percentageOfAnswer'][$question->id][4]}}" aria-valuemin="0" aria-valuemax="100" style="@if($question->answers->{'correct_answer_'.$AttemptExamData->language} == 4){{$bg_correct_color}}@else{{$bg_incorrect_color}} @endif;width:{{$exams['percentageOfAnswer'][$question->id][4]}}%">
                                                                <div class="anser-detail pl-2">
                                                                    <?php echo $question->answers->{'answer4_'.$AttemptExamData->language}; ?>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="answer-progress">
                                                            <p class="progress-percentage">{{$exams['percentageOfAnswer'][$question->id][4]}}%</p>
                                                        </div>
                                                    </div>
                                                    @endif
                                                </div>

                                                <div class="col-md-4">
                                                    @if(isset($question->answers->{'answer1_'.$AttemptExamData->language}))
                                                    <div class="sm-ans pl-2 pb-2">
                                                        <div class="answer-title mr-2 <?php if($question->answers->{'correct_answer_'.$AttemptExamData->language} == 1){ echo 'correct-answer';}else{ echo 'incorrect-answer';} ?>" style="@if($question->answers->{'correct_answer_'.$AttemptExamData->language} == 1){{$bg_correct_color}}@else{{$bg_incorrect_color}} @endif">A</div>
                                                        <div class="progress">
                                                            <div class="progress-bar @if($question->answers->{'correct_answer_'.$AttemptExamData->language} === 1) ans-correct @else ans-incorrect @endif" role="progressbar"  aria-valuenow="{{$exams['percentageOfAnswerSchool'][$question->id][1]}}" aria-valuemin="0" aria-valuemax="100" style="@if($question->answers->{'correct_answer_'.$AttemptExamData->language} == 1){{$bg_correct_color}}@else{{$bg_incorrect_color}} @endif;width:{{$exams['percentageOfAnswerSchool'][$question->id][1]}}%">
                                                                <div class="anser-detail pl-2">
                                                                    <?php echo $question->answers->{'answer1_'.$AttemptExamData->language}; ?>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="answer-progress">
                                                            <p class="progress-percentage">{{$exams['percentageOfAnswerSchool'][$question->id][1]}}%</p>
                                                        </div>
                                                    </div>
                                                    @endif
                                                    
                                                    @if(isset($question->answers->{'answer2_'.$AttemptExamData->language}))
                                                    <div class="sm-ans pl-2 pb-2">
                                                        <div class="answer-title mr-2 @if($question->answers->{'correct_answer_'.$AttemptExamData->language} === 2) correct-answer @else incorrect-answer @endif" style="@if($question->answers->{'correct_answer_'.$AttemptExamData->language} == 2){{$bg_correct_color}}@else{{$bg_incorrect_color}} @endif">B</div>
                                                        <div class="progress">
                                                            <div class="progress-bar @if($question->answers->{'correct_answer_'.$AttemptExamData->language} === 2) ans-correct @else ans-incorrect @endif" role="progressbar"  aria-valuenow="{{$exams['percentageOfAnswerSchool'][$question->id][2]}}" aria-valuemin="0" aria-valuemax="100" style="@if($question->answers->{'correct_answer_'.$AttemptExamData->language} == 2){{$bg_correct_color}}@else{{$bg_incorrect_color}} @endif;width:{{$exams['percentageOfAnswerSchool'][$question->id][2]}}%">
                                                                <div class="anser-detail pl-2">
                                                                    <?php echo $question->answers->{'answer2_'.$AttemptExamData->language}; ?>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="answer-progress">
                                                            <p class="progress-percentage">{{$exams['percentageOfAnswerSchool'][$question->id][2]}}%</p>
                                                        </div>
                                                    </div>
                                                    @endif

                                                    @if(isset($question->answers->{'answer3_'.$AttemptExamData->language}))
                                                    <div class="sm-ans pl-2 pb-2">
                                                        <div class="answer-title mr-2 @if($question->answers->{'correct_answer_'.$AttemptExamData->language} === 3) correct-answer @else incorrect-answer @endif" style="@if($question->answers->{'correct_answer_'.$AttemptExamData->language} == 3){{$bg_correct_color}}@else{{$bg_incorrect_color}} @endif">C</div>
                                                        <div class="progress">
                                                            <div class="progress-bar @if($question->answers->{'correct_answer_'.$AttemptExamData->language} === 3) ans-correct @else ans-incorrect @endif" role="progressbar"  aria-valuenow="{{$exams['percentageOfAnswerSchool'][$question->id][3]}}" aria-valuemin="0" aria-valuemax="100" style="@if($question->answers->{'correct_answer_'.$AttemptExamData->language} == 3){{$bg_correct_color}}@else{{$bg_incorrect_color}} @endif;width:{{$exams['percentageOfAnswerSchool'][$question->id][3]}}%">
                                                                <div class="anser-detail pl-2">
                                                                    <?php echo $question->answers->{'answer3_'.$AttemptExamData->language}; ?>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="answer-progress">
                                                            <p class="progress-percentage">{{$exams['percentageOfAnswerSchool'][$question->id][3]}}%</p>
                                                        </div>
                                                    </div>
                                                    @endif

                                                    @if(isset($question->answers->{'answer4_'.$AttemptExamData->language}))
                                                    <div class="sm-ans pl-2 pb-2">
                                                        <div class="answer-title mr-2 @if($question->answers->{'correct_answer_'.$AttemptExamData->language} === 4) correct-answer @else incorrect-answer @endif" style="@if($question->answers->{'correct_answer_'.$AttemptExamData->language} == 4){{$bg_correct_color}}@else{{$bg_incorrect_color}} @endif">D</div>
                                                        <div class="progress">
                                                            <div class="progress-bar @if($question->answers->{'correct_answer_'.$AttemptExamData->language} === 4) ans-correct @else ans-incorrect @endif" role="progressbar"  aria-valuenow="{{$exams['percentageOfAnswerSchool'][$question->id][4]}}" aria-valuemin="0" aria-valuemax="100" style="@if($question->answers->{'correct_answer_'.$AttemptExamData->language} == 4){{$bg_correct_color}}@else{{$bg_incorrect_color}} @endif;width:{{$exams['percentageOfAnswerSchool'][$question->id][4]}}%">
                                                                <div class="anser-detail pl-2">
                                                                    <?php echo $question->answers->{'answer4_'.$AttemptExamData->language}; ?>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="answer-progress">
                                                            <p class="progress-percentage">{{$exams['percentageOfAnswerSchool'][$question->id][4]}}%</p>
                                                        </div>
                                                    </div>
                                                    @endif
                                                </div>
                                                
                                                <div class="col-md-4">
                                                    @if(isset($question->answers->{'answer1_'.$AttemptExamData->language}))
                                                    <div class="sm-ans pl-2 pb-2">
                                                        <div class="answer-title mr-2 <?php if($question->answers->{'correct_answer_'.$AttemptExamData->language} == 1){ echo 'correct-answer';}else{ echo 'incorrect-answer';} ?>" style="@if($question->answers->{'correct_answer_'.$AttemptExamData->language} == 1){{$bg_correct_color}}@else{{$bg_incorrect_color}} @endif">A</div>
                                                        <div class="progress">
                                                            <div class="progress-bar @if($question->answers->{'correct_answer_'.$AttemptExamData->language} === 1) ans-correct @else ans-incorrect @endif" role="progressbar"  aria-valuenow="{{$exams['percentageOfAnswerAllSchool'][$question->id][1]}}" aria-valuemin="0" aria-valuemax="100" style="@if($question->answers->{'correct_answer_'.$AttemptExamData->language} == 1){{$bg_correct_color}}@else{{$bg_incorrect_color}} @endif;width:{{$exams['percentageOfAnswerAllSchool'][$question->id][1]}}%">
                                                                <div class="anser-detail pl-2">
                                                                    <?php echo $question->answers->{'answer1_'.$AttemptExamData->language}; ?>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="answer-progress">
                                                            <p class="progress-percentage">{{$exams['percentageOfAnswerAllSchool'][$question->id][1]}}%</p>
                                                        </div>
                                                    </div>
                                                    @endif
                                                    
                                                    @if(isset($question->answers->{'answer2_'.$AttemptExamData->language}))
                                                    <div class="sm-ans pl-2 pb-2">
                                                        <div class="answer-title mr-2 @if($question->answers->{'correct_answer_'.$AttemptExamData->language} === 2) correct-answer @else incorrect-answer @endif" style="@if($question->answers->{'correct_answer_'.$AttemptExamData->language} == 2){{$bg_correct_color}}@else{{$bg_incorrect_color}} @endif">B</div>
                                                        <div class="progress">
                                                            <div class="progress-bar @if($question->answers->{'correct_answer_'.$AttemptExamData->language} === 2) ans-correct @else ans-incorrect @endif" role="progressbar"  aria-valuenow="{{$exams['percentageOfAnswerAllSchool'][$question->id][2]}}" aria-valuemin="0" aria-valuemax="100" style="@if($question->answers->{'correct_answer_'.$AttemptExamData->language} == 2){{$bg_correct_color}}@else{{$bg_incorrect_color}} @endif;width:{{$exams['percentageOfAnswerAllSchool'][$question->id][2]}}%">
                                                                <div class="anser-detail pl-2">
                                                                    <?php echo $question->answers->{'answer2_'.$AttemptExamData->language}; ?>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="answer-progress">
                                                            <p class="progress-percentage">{{$exams['percentageOfAnswerAllSchool'][$question->id][2]}}%</p>
                                                        </div>
                                                    </div>
                                                    @endif

                                                    @if(isset($question->answers->{'answer3_'.$AttemptExamData->language}))
                                                    <div class="sm-ans pl-2 pb-2">
                                                        <div class="answer-title mr-2 @if($question->answers->{'correct_answer_'.$AttemptExamData->language} === 3) correct-answer @else incorrect-answer @endif" style="@if($question->answers->{'correct_answer_'.$AttemptExamData->language} == 3){{$bg_correct_color}}@else{{$bg_incorrect_color}} @endif">C</div>
                                                        <div class="progress">
                                                            <div class="progress-bar @if($question->answers->{'correct_answer_'.$AttemptExamData->language} === 3) ans-correct @else ans-incorrect @endif" role="progressbar"  aria-valuenow="{{$exams['percentageOfAnswerAllSchool'][$question->id][3]}}" aria-valuemin="0" aria-valuemax="100" style="@if($question->answers->{'correct_answer_'.$AttemptExamData->language} == 3){{$bg_correct_color}}@else{{$bg_incorrect_color}} @endif;width:{{$exams['percentageOfAnswerAllSchool'][$question->id][3]}}%">
                                                                <div class="anser-detail pl-2">
                                                                    <?php echo $question->answers->{'answer3_'.$AttemptExamData->language}; ?>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="answer-progress">
                                                            <p class="progress-percentage">{{$exams['percentageOfAnswerAllSchool'][$question->id][3]}}%</p>
                                                        </div>
                                                    </div>
                                                    @endif

                                                    @if(isset($question->answers->{'answer4_'.$AttemptExamData->language}))
                                                    <div class="sm-ans pl-2 pb-2">
                                                        <div class="answer-title mr-2 @if($question->answers->{'correct_answer_'.$AttemptExamData->language} === 4) correct-answer @else incorrect-answer @endif" style="@if($question->answers->{'correct_answer_'.$AttemptExamData->language} == 4){{$bg_correct_color}}@else{{$bg_incorrect_color}} @endif">D</div>
                                                        <div class="progress">
                                                            <div class="progress-bar @if($question->answers->{'correct_answer_'.$AttemptExamData->language} === 4) ans-correct @else ans-incorrect @endif" role="progressbar"  aria-valuenow="{{$exams['percentageOfAnswerAllSchool'][$question->id][4]}}" aria-valuemin="0" aria-valuemax="100" style="@if($question->answers->{'correct_answer_'.$AttemptExamData->language} == 4){{$bg_correct_color}}@else{{$bg_incorrect_color}} @endif;width:{{$exams['percentageOfAnswerAllSchool'][$question->id][4]}}%">
                                                                <div class="anser-detail pl-2">
                                                                    <?php echo $question->answers->{'answer4_'.$AttemptExamData->language}; ?>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="answer-progress">
                                                            <p class="progress-percentage">{{$exams['percentageOfAnswerAllSchool'][$question->id][4]}}%</p>
                                                        </div>
                                                    </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                        <div class="sm-answer pl-5 pt-2">
                                            <button type="button" class="btn btn-sm btn-success question_graph" data-graphtype="currentstudent" data-studentid="{{ $AttemptExamData->student_id }}" data-questionid="{{ $question->id }}" data-examid="{{ $AttemptExamData->exam_id }}">
                                                <i class="fa fa-bar-chart" aria-hidden="true"></i>{{ __('languages.question_analysis') }}
                                            </button>
                                            <?php if(isset($AnswerNumber[key($AnswerNumber)]) && $AnswerNumber[key($AnswerNumber)]->answer == $question->answers->{'correct_answer_'.$AttemptExamData->language}){ ?>
                                                <span class="badge badge-success">{{__('languages.result.correct_answer')}}</span>
                                            <?php }else{ ?>
                                                <span class="badge badge-danger">{{__('languages.result.incorrect_answer')}}</span>
                                            @php
                                            $nodeId = $question->answers->{'answer'.$AnswerNumber[key($AnswerNumber)]->answer.'_node_relation_id_'.$AttemptExamData->language};
                                            if(empty($nodeId)){
                                                $nodeId = App\Helpers\Helper::getWeaknessNodeId($question->id, $AnswerNumber[key($AnswerNumber)]->answer);
                                            }
                                            @endphp
                                            <h6 class="mt-3"><b>{{__('languages.report.weakness')}}:</b>
                                                @if($AttemptExamData->language == 'ch')
                                                    @if($nodeId != 0 && isset($nodeWeaknessListCh[$nodeId]))
                                                        @php
                                                        $WeaknessList[] = $nodeWeaknessListCh[$nodeId];
                                                        $WeaknessListWithId[$nodeId] = $nodeWeaknessListCh[$nodeId];
                                                        @endphp
                                                        {{$nodeWeaknessListCh[$nodeId]}}
                                                    @endif
                                                @else
                                                    @if($nodeId != 0 && isset($nodeWeaknessList[$nodeId]))
                                                        @php
                                                        $WeaknessList[] = $nodeWeaknessList[$nodeId];
                                                        $WeaknessListWithId[$nodeId]=$nodeWeaknessList[$nodeId];
                                                        @endphp
                                                        {{$nodeWeaknessList[$nodeId]}}
                                                    @endif
                                                @endif
                                            </h6>
                                            <?php } ?>
                                        </div>
                                    </div>
                                    <hr>
                                @endforeach
                            @endif
                        @endforeach
                    @endif
                    @php
                        $KeyImprovementData = '';
                        $KeyWeaknessData = '';
                        $checkImprovement = 1; 
                    @endphp
                    @foreach ($AllWeakness as $WeaknessKey => $WeaknessNof)
                        @if(isset($WeaknessListWithId[$WeaknessKey]))
                            @php
                                if($checkImprovement <= 2){
                                    $KeyImprovementData.='<li style="list-style:disc;">'. $WeaknessListWithId[$WeaknessKey].'</li>';
                                }else{
                                    $KeyWeaknessData.='<li style="list-style:disc;">'. $WeaknessListWithId[$WeaknessKey].'</li>';
                                }
                                $checkImprovement++;
                            @endphp
                        @endif
                    @endforeach
                    <div id="accordionImprovement" class="weakness_result_list">
                        <div class="card1">
                            <div class="card-header1" id="heading">
                                <h5 class="mb-0">
                                <button class="btn btn-link collapsed" data-toggle="collapse" data-target="#collapseImprovement" aria-expanded="false" aria-controls="collapse">
                                    <h6 class="text-dark"><b><i class="fa fa-plus mr-2"></i>{{ __('languages.key_improvement_points') }}</b></h6 >
                                </button>
                                </h5>
                            </div>
                            <div id="collapseImprovement" class="collapse" aria-labelledby="heading" data-parent="#accordionImprovement">
                                <ul class="list-unstyled ml-5">
                                    @if($KeyImprovementData != "")
                                        {!! $KeyImprovementData !!}
                                    @else
                                        <li>{{ __('languages.no_key_improvement_point_available') }}</li>
                                    @endif
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div id="accordion" class="weakness_result_list">
                        <div class="card1">
                            <div class="card-header1" id="headingTwo">
                                <h5 class="mb-0">
                                <button class="btn btn-link collapsed" data-toggle="collapse" data-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                                    <h6 class="text-dark"><b><i class="fa fa-plus mr-2"></i>{{__('languages.report.weakness')}}</b></h6>
                                </button>
                                </h5>
                            </div>
                            <div id="collapseTwo" class="collapse" aria-labelledby="headingTwo" data-parent="#accordion">
                                <ul class="list-unstyled ml-5">
                                    @if($KeyWeaknessData!="")
                                        {!! $KeyWeaknessData !!}
                                    @else
                                        <li>{{__('languages.report.no_weakness_available')}}</li>
                                    @endif
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
        <!-- End IS Group Test -->
    </div>
</div>
