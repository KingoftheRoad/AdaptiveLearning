    
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
               
                @if(!empty($difficultyLevels))
                    @php $i=1; $difficultyColor= []; @endphp
                   @foreach($difficultyLevels as $difficultyLevel)
                        @if(!empty($questionDifficultyGraph['Level'.$i]))
                        <h6>{{__($difficultyLevel->{'difficulty_level_name_'.app()->getLocale()})}}</h6>
                        <div class="progress question-difficulty-progressbar">
                            @if(!empty($questionDifficultyGraph['Level'.$i.'_correct_percentage']))
                            <div class="progress-bar" style="width:{{$questionDifficultyGraph['Level'.$i.'_correct_percentage']}}%;{{$bg_correct_color}};">
                                {{$questionDifficultyGraph['correct_Level'.$i]}}
                            </div>
                            @endif
                            @if(!empty($questionDifficultyGraph['Level'.$i.'_wrong_percentage']))
                            <div class="progress-bar" style="width:{{$questionDifficultyGraph['Level'.$i.'_wrong_percentage']}}%;{{$bg_incorrect_color}};">
                                {{ $questionDifficultyGraph['wrong_Level'.$i] }}
                            </div>
                            @endif
                        </div>
                        @endif

                        @php $difficultyColor['Level'.$i] = $difficultyLevel->difficulty_level_color;  $i+=1;@endphp
                    @endforeach
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
            <div class="sm-add-user-sec statistics-result-main card">
                <div class="select-option-sec pb-2 card-body">
                    @if(!empty($Questions))
                        @php
                        $UserSelectedAnswers = [];
                        $WeaknessList = array();
                        $WeaknessListWithId = array();
                        if(isset($AttemptExamData->question_answers) && !empty($AttemptExamData->question_answers)){
                            $UserSelectedAnswers = json_decode($AttemptExamData->question_answers);
                        }
                        @endphp
                        @foreach($Questions as $key => $question)
                            @php
                            $AnswerNumber = array_filter($UserSelectedAnswers, function ($var) use($question){
                                if($var->question_id == $question['id']){
                                    return $var;
                                }
                            });
                            @endphp
                            <div class="row result-statistics">
                                <div class="sm-que-option pl-3">
                                    <p class="sm-title bold">
                                        {{__('languages.result.q_no')}} {{$loop->iteration}}:
                                        {{-- Display Question code --}}
                                        {{__('languages.question_code')}} : {{ $question->naming_structure_code }}
                                        <?php 
                                            $LevelName = \App\Helpers\Helper::getLevelNameBasedOnLanguage($question->dificulaty_level);
                                        ?>
                                        {{-- Display Question types and with color code --}}
                                        @if($question->dificulaty_level == 1)
                                            @if(isset($difficultyColor['Level1']) && !empty($difficultyColor['Level1']))
                                                <span class="ml-5"> {{$LevelName}}</span> <span class="dot-color" style="background-color:{{ $difficultyColor['Level1']; }};border-radius: 50%;display: inline-block;top: 5px;position: relative;"></span>
                                            @endif
                                        @elseif($question->dificulaty_level == 2)
                                            @if(isset($difficultyColor['Level2']) && !empty($difficultyColor['Level2']))
                                                <span class="ml-5"> {{$LevelName}}</span> <span class="dot-color" style="background-color:{{ $difficultyColor['Level2']; }};border-radius: 50%;display: inline-block;top: 5px;position: relative;"></span>
                                            @endif
                                        @elseif($question->dificulaty_level == 3)
                                            @if(isset($difficultyColor['Level3']) && !empty($difficultyColor['Level3']))
                                                <span class="ml-5"> {{$LevelName}}</span> <span class="dot-color" style="background-color:{{ $difficultyColor['Level3']; }};border-radius: 50%;display: inline-block;top: 5px;position: relative;"></span>
                                            @endif
                                        @elseif($question->dificulaty_level == 4)
                                            @if(isset($difficultyColor['Level4']) && !empty($difficultyColor['Level4']))
                                                <span class="ml-5"> {{$LevelName}}</span> <span class="dot-color" style="background-color:{{ $difficultyColor['Level4']; }};border-radius: 50%;display: inline-block;top: 5px;position: relative;"></span>
                                            @endif
                                        @elseif($question->dificulaty_level == 5)
                                            @if(isset($difficultyColor['Level5']) && !empty($difficultyColor['Level5']))
                                                <span class="ml-5"> {{$LevelName}}</span> <span class="dot-color" style="background-color:{{ $difficultyColor['Level5']; }};border-radius: 50%;display: inline-block;top: 5px;position: relative;"></span>
                                            @endif
                                        @endif
                                        {{-- Display Natural difficulties & Normalized difficulties --}}
                                        {{-- <span class="ml-5">{{__('languages.difficulty')}}: {{round($question['difficultyValue']['natural_difficulty'],2)}} ({{$question['difficultyValue']['normalized_difficulty']}}%)</span> --}}
                                        {{-- <span class="ml-5">{{__('languages.difficulty')}}:  --}}
                                            {{-- /{{round($question['difficultyValue']['natural_difficulty'],2)}} ({{App\Helpers\Helper::GetShortPercentage($question['difficultyValue']['normalized_difficulty'])}}%) --}}
                                        {{-- </span> /--}}
                                        <span class="ml-5">{{__('languages.difficulty')}}:
                                            {{App\Helpers\Helper::GetShortPercentage($question['difficultyValue']['normalized_difficulty'])}}
                                        </span>
                                    </p>
                                    <div class="sm-que pl-2">
                                        <p><?php echo $question->{'question_'.app()->getLocale()}; ?></p>
                                    </div>
                                    <div class="row">
                                        @if(Auth::check() && Auth::user()->role_id == 2)
                                            <div class="col-md-4">
                                               {{-- Get Previous Url and then mange my class or my group text in report --}}
                                                @php
                                                    $parsedUrl = parse_url(URL::previous());
                                                    parse_str($parsedUrl['query'], $queryString);
                                                @endphp

                                                @if(!empty($queryString['group_id']))
                                                    <h6>{{__('languages.My Group')}}</h6>
                                                @else
                                                    <h6>{{__('languages.My Class')}}</h6>
                                                @endif
                                                @if(isset($question->answers->{'answer1_'.$AttemptExamData->language}))
                                                <div class="sm-ans pl-2 pb-2">
                                                    <input type="radio" name="ans_que_{{$question->id}}_{{$studentId}}" value="1" class="radio mr-2" <?php if(isset($AnswerNumber[key($AnswerNumber)]) && $AnswerNumber[key($AnswerNumber)]->answer == 1){ echo 'checked';} ?> disabled>
                                                    <div class="answer-title mr-2 <?php if($question->answers->{'correct_answer_'.$AttemptExamData->language} == 1){ echo 'correct-answer';}else{ echo 'incorrect-answer';} ?>" style="@if($question->answers->{'correct_answer_'.$AttemptExamData->language} == 1){{$bg_correct_color}}@else{{$bg_incorrect_color}} @endif">A</div>
                                                    <div class="progress">
                                                        <div class="progress-bar @if($question->answers->{'correct_answer_'.$AttemptExamData->language} === 1) ans-correct @else ans-incorrect @endif" role="progressbar"  aria-valuenow="{{$percentageOfAnswer[$question->id][1]}}" aria-valuemin="0" aria-valuemax="100" style="@if($question->answers->{'correct_answer_'.$AttemptExamData->language} == 1){{$bg_correct_color}}@else{{$bg_incorrect_color}} @endif;width:{{$percentageOfAnswer[$question->id][1]}}%">
                                                            <div class="anser-detail pl-2">
                                                                <?php echo $question->answers->{'answer1_'.app()->getLocale()}; ?>
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
                                                    <input type="radio" name="ans_que_{{$question->id}}_{{$studentId}}" value="2" class="radio mr-2" <?php if(isset($AnswerNumber[key($AnswerNumber)]) && $AnswerNumber[key($AnswerNumber)]->answer == 2){ echo 'checked';} ?> disabled>
                                                    <div class="answer-title mr-2 @if($question->answers->{'correct_answer_'.$AttemptExamData->language} === 2) correct-answer @else incorrect-answer @endif" style="@if($question->answers->{'correct_answer_'.$AttemptExamData->language} == 2){{$bg_correct_color}}@else{{$bg_incorrect_color}} @endif">B</div>
                                                    <div class="progress">
                                                        <div class="progress-bar @if($question->answers->{'correct_answer_'.$AttemptExamData->language} === 2) ans-correct @else ans-incorrect @endif" role="progressbar"  aria-valuenow="{{$percentageOfAnswer[$question->id][2]}}" aria-valuemin="0" aria-valuemax="100" style="@if($question->answers->{'correct_answer_'.$AttemptExamData->language} == 2){{$bg_correct_color}}@else{{$bg_incorrect_color}} @endif;width:{{$percentageOfAnswer[$question->id][2]}}%">
                                                            <div class="anser-detail pl-2">
                                                                <?php echo $question->answers->{'answer2_'.app()->getLocale()}; ?>
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
                                                    <input type="radio" name="ans_que_{{$question->id}}_{{$studentId}}" value="3" class="radio mr-2" <?php if(isset($AnswerNumber[key($AnswerNumber)]) && $AnswerNumber[key($AnswerNumber)]->answer == 3){ echo 'checked';} ?> disabled>
                                                    <div class="answer-title mr-2 @if($question->answers->{'correct_answer_'.$AttemptExamData->language} === 3) correct-answer @else incorrect-answer @endif" style="@if($question->answers->{'correct_answer_'.$AttemptExamData->language} == 3){{$bg_correct_color}}@else{{$bg_incorrect_color}} @endif">C</div>
                                                        <div class="progress">
                                                            <div class="progress-bar @if($question->answers->{'correct_answer_'.$AttemptExamData->language} === 3) ans-correct @else ans-incorrect @endif" role="progressbar"  aria-valuenow="{{$percentageOfAnswer[$question->id][3]}}" aria-valuemin="0" aria-valuemax="100" style="@if($question->answers->{'correct_answer_'.$AttemptExamData->language} == 3){{$bg_correct_color}}@else{{$bg_incorrect_color}} @endif;width:{{$percentageOfAnswer[$question->id][3]}}%">
                                                                <div class="anser-detail pl-2">
                                                                    <?php echo $question->answers->{'answer3_'.app()->getLocale()}; ?>
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
                                                        <input type="radio" name="ans_que_{{$question->id}}_{{$studentId}}" value="4" class="radio mr-2" <?php if(isset($AnswerNumber[key($AnswerNumber)]) && $AnswerNumber[key($AnswerNumber)]->answer == 4){ echo 'checked';} ?> disabled>
                                                        <div class="answer-title mr-2 @if($question->answers->{'correct_answer_'.$AttemptExamData->language} === 4) correct-answer @else incorrect-answer @endif" style="@if($question->answers->{'correct_answer_'.$AttemptExamData->language} == 4){{$bg_correct_color}}@else{{$bg_incorrect_color}} @endif">D</div>
                                                        <div class="progress">
                                                            <div class="progress-bar @if($question->answers->{'correct_answer_'.$AttemptExamData->language} === 4) ans-correct @else ans-incorrect @endif" role="progressbar"  aria-valuemin="0" aria-valuemax="100" style="@if($question->answers->{'correct_answer_'.$AttemptExamData->language} == 4){{$bg_correct_color}}@else{{$bg_incorrect_color}} @endif;width:{{$percentageOfAnswer[$question->id][4]}}%">
                                                                <div class="anser-detail pl-2">
                                                                    <?php echo $question->answers->{'answer4_'.app()->getLocale()}; ?>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="answer-progress">
                                                            <p class="progress-percentage">{{$percentageOfAnswer[$question->id][4]}}%</p>
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                        @endif
                                       
             
                                            @if((Auth::check() && Auth::user()->role_id == 5) || (Auth::check() && Auth::user()->role_id == 7) || (Auth::check() && Auth::user()->role_id == 2))
                                                @php $my_school='col-md-4'; @endphp
                                                @if(Auth::check() && Auth::user()->role_id == 5 || Auth::check() && Auth::user()->role_id == 7)
                                                    @php $my_school='col-md-6'; @endphp
                                                @endif
                                                <div class="{{ $my_school }}">
                                                    <h6>{{__('languages.my_school')}}</h6>
                                                    @if(isset($question->answers->{'answer1_'.$AttemptExamData->language}))
                                                    <div class="sm-ans pl-2 pb-2">
                                                        <div class="answer-title mr-2 <?php if($question->answers->{'correct_answer_'.$AttemptExamData->language} == 1){ echo 'correct-answer';}else{ echo 'incorrect-answer';} ?>" style="@if($question->answers->{'correct_answer_'.$AttemptExamData->language} == 1){{$bg_correct_color}}@else{{$bg_incorrect_color}} @endif">A</div>
                                                        <div class="progress">
                                                            <div class="progress-bar @if($question->answers->{'correct_answer_'.$AttemptExamData->language} === 1) ans-correct @else ans-incorrect @endif" role="progressbar"  aria-valuenow="{{$percentageOfAnswerSchool[$question->id][1]}}" aria-valuemin="0" aria-valuemax="100" style="@if($question->answers->{'correct_answer_'.$AttemptExamData->language} == 1){{$bg_correct_color}}@else{{$bg_incorrect_color}} @endif;width:{{$percentageOfAnswerSchool[$question->id][1]}}%">
                                                                <div class="anser-detail pl-2">
                                                                    <?php echo $question->answers->{'answer1_'.app()->getLocale()}; ?>
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
                                                                    <?php echo $question->answers->{'answer2_'.app()->getLocale()}; ?>
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
                                                                        <?php echo $question->answers->{'answer3_'.app()->getLocale()}; ?>
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
                                                                        <?php echo $question->answers->{'answer4_'.app()->getLocale()}; ?>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="answer-progress">
                                                                <p class="progress-percentage">{{$percentageOfAnswerSchool[$question->id][4]}}%</p>
                                                            </div>
                                                        </div>
                                                    @endif
                                                </div> 
                                            @endif
                                            
                                        @php
                                            $all_school='col-md-4';
                                            if(Auth::check() && Auth::user()->role_id == 1){
                                                $all_school='col-md-12';
                                            }else if(Auth::check() && Auth::user()->role_id == 5 || Auth::check() && Auth::user()->role_id == 7){
                                                $all_school='col-md-6';
                                            }
                                        @endphp
                                        <div class="{{ $all_school }} sfsdf">
                                            <h6>{{__('languages.all_schools')}}</h6>
                                            @if(isset($question->answers->{'answer1_'.$AttemptExamData->language}))
                                            <div class="sm-ans pl-2 pb-2">
                                                @if(Auth::check() && Auth::user()->role_id == 1)
                                                    <input type="radio" name="ans_que_{{$question->id}}_{{$studentId}}" value="1" class="radio mr-2" <?php if(isset($AnswerNumber[key($AnswerNumber)]) && $AnswerNumber[key($AnswerNumber)]->answer == 1){ echo 'checked';} ?> disabled>
                                                @endif
                                                <div class="answer-title mr-2 <?php if($question->answers->{'correct_answer_'.$AttemptExamData->language} == 1){ echo 'correct-answer';}else{ echo 'incorrect-answer';} ?>" style="@if($question->answers->{'correct_answer_'.$AttemptExamData->language} == 1){{$bg_correct_color}}@else{{$bg_incorrect_color}} @endif">A</div>
                                                <div class="progress">
                                                    <div class="progress-bar @if($question->answers->{'correct_answer_'.$AttemptExamData->language} === 1) ans-correct @else ans-incorrect @endif" role="progressbar"  aria-valuenow="{{$percentageOfAnswerAllSchool[$question->id][1]}}" aria-valuemin="0" aria-valuemax="100" style="@if($question->answers->{'correct_answer_'.$AttemptExamData->language} == 1){{$bg_correct_color}}@else{{$bg_incorrect_color}} @endif;width:{{$percentageOfAnswerAllSchool[$question->id][1]}}%">
                                                        <div class="anser-detail pl-2">
                                                            <?php echo $question->answers->{'answer1_'.app()->getLocale()}; ?>
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
                                                @if(Auth::check() && Auth::user()->role_id == 1)
                                                    <input type="radio" name="ans_que_{{$question->id}}_{{$studentId}}" value="2" class="radio mr-2" <?php if(isset($AnswerNumber[key($AnswerNumber)]) && $AnswerNumber[key($AnswerNumber)]->answer == 2){ echo 'checked';} ?> disabled>
                                                @endif
                                                <div class="answer-title mr-2 @if($question->answers->{'correct_answer_'.$AttemptExamData->language} === 2) correct-answer @else incorrect-answer @endif" style="@if($question->answers->{'correct_answer_'.$AttemptExamData->language} == 2){{$bg_correct_color}}@else{{$bg_incorrect_color}} @endif">B</div>
                                                <div class="progress">
                                                    <div class="progress-bar @if($question->answers->{'correct_answer_'.$AttemptExamData->language} === 2) ans-correct @else ans-incorrect @endif" role="progressbar"  aria-valuenow="{{$percentageOfAnswerAllSchool[$question->id][2]}}" aria-valuemin="0" aria-valuemax="100" style="@if($question->answers->{'correct_answer_'.$AttemptExamData->language} == 2){{$bg_correct_color}}@else{{$bg_incorrect_color}} @endif;width:{{$percentageOfAnswerAllSchool[$question->id][2]}}%">
                                                        <div class="anser-detail pl-2">
                                                            <?php echo $question->answers->{'answer2_'.app()->getLocale()}; ?>
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
                                                @if(Auth::check() && Auth::user()->role_id == 1)
                                                    <input type="radio" name="ans_que_{{$question->id}}_{{$studentId}}" value="3" class="radio mr-2" <?php if(isset($AnswerNumber[key($AnswerNumber)]) && $AnswerNumber[key($AnswerNumber)]->answer == 3){ echo 'checked';} ?> disabled>
                                                @endif
                                                <div class="answer-title mr-2 @if($question->answers->{'correct_answer_'.$AttemptExamData->language} === 3) correct-answer @else incorrect-answer @endif" style="@if($question->answers->{'correct_answer_'.$AttemptExamData->language} == 3){{$bg_correct_color}}@else{{$bg_incorrect_color}} @endif">C</div>
                                                    <div class="progress">
                                                        <div class="progress-bar @if($question->answers->{'correct_answer_'.$AttemptExamData->language} === 3) ans-correct @else ans-incorrect @endif" role="progressbar"  aria-valuenow="{{$percentageOfAnswerAllSchool[$question->id][3]}}" aria-valuemin="0" aria-valuemax="100" style="@if($question->answers->{'correct_answer_'.$AttemptExamData->language} == 3){{$bg_correct_color}}@else{{$bg_incorrect_color}} @endif;width:{{$percentageOfAnswerAllSchool[$question->id][3]}}%">
                                                            <div class="anser-detail pl-2">
                                                                <?php echo $question->answers->{'answer3_'.app()->getLocale()}; ?>
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
                                                    @if(Auth::check() && Auth::user()->role_id == 1)
                                                        <input type="radio" name="ans_que_{{$question->id}}_{{$studentId}}" value="4" class="radio mr-2" <?php if(isset($AnswerNumber[key($AnswerNumber)]) && $AnswerNumber[key($AnswerNumber)]->answer == 4){ echo 'checked';} ?> disabled>
                                                    @endif
                                                    <div class="answer-title mr-2 @if($question->answers->{'correct_answer_'.$AttemptExamData->language} === 4) correct-answer @else incorrect-answer @endif" style="@if($question->answers->{'correct_answer_'.$AttemptExamData->language} == 4){{$bg_correct_color}}@else{{$bg_incorrect_color}} @endif">D</div>
                                                    <div class="progress">
                                                        <div class="progress-bar @if($question->answers->{'correct_answer_'.$AttemptExamData->language} === 4) ans-correct @else ans-incorrect @endif" role="progressbar"  aria-valuemin="0" aria-valuemax="100" style="@if($question->answers->{'correct_answer_'.$AttemptExamData->language} == 4){{$bg_correct_color}}@else{{$bg_incorrect_color}} @endif;width:{{$percentageOfAnswerAllSchool[$question->id][4]}}%">
                                                            <div class="anser-detail pl-2">
                                                                <?php echo $question->answers->{'answer4_'.app()->getLocale()}; ?>
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
                                    <span class="badge badge-success getSolutionQuestionImage" data-question-code ="{{$question->naming_structure_code}}">{{__('languages.full_question_solution')}}</span>
                                    <?php }else{ ?>
                                    <span class="badge badge-danger">{{__('languages.result.incorrect_answer')}}</span>
                                    <span class="badge badge-success getSolutionQuestionImage" data-question-code ="{{$question->naming_structure_code}}">{{__('languages.full_question_solution')}}</span>
                                    <?php
                                    $nodeId=0;
                                    if(isset($AnswerNumber[key($AnswerNumber)])){
                                        $nodeId = $question->answers->{'answer'.$AnswerNumber[key($AnswerNumber)]->answer.'_node_relation_id_'.$AttemptExamData->language};
                                        if(empty($nodeId)){
                                            $nodeId = App\Helpers\Helper::getWeaknessNodeId($question->id, $AnswerNumber[key($AnswerNumber)]->answer);
                                        }
                                    } ?>
                                    <h6 class="mt-3"><b>{{__('languages.report.weakness')}}:</b>
                                        @if(app()->getLocale() == 'ch')
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
                
                    <div id="accordionImprovement-{{$studentId}}" class="weakness_result_list">
                        <div class="card1">
                            <div class="card-header1" id="heading">
                                <h5 class="mb-0">
                                <button class="btn btn-link collapsed" data-toggle="collapse" data-target="#collapseImprovement-{{$studentId}}" aria-expanded="false" aria-controls="collapse">
                                    <h6 class="text-dark"><b><i class="fa fa-plus mr-2"></i>{{ __('languages.key_improvement_points') }}</b></h6 >
                                </button>
                                </h5>
                            </div>
                            <div id="collapseImprovement-{{$studentId}}" class="collapse" aria-labelledby="heading" data-parent="#accordionImprovement-{{$studentId}}">
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

                    <div id="accordion-{{$studentId}}" class="weakness_result_list">
                        <div class="card1">
                            <div class="card-header1" id="headingTwo-{{$studentId}}">
                                <h5 class="mb-0">
                                <button class="btn btn-link collapsed" data-toggle="collapse" data-target="#weakness-{{$studentId}}" aria-expanded="false" aria-controls="collapseTwo">
                                    <h6 class="text-dark"><b><i class="fa fa-plus mr-2"></i>{{__('languages.report.weakness')}}</b></h6>
                                </button>
                                </h5>
                            </div>
                            <div id="weakness-{{$studentId}}" class="collapse" aria-labelledby="headingTwo-{{$studentId}}" data-parent="#accordion-{{$studentId}}">
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
                                $UserSelectedAnswers = [];
                                if(isset($AttemptExamData->question_answers) && !empty($AttemptExamData->question_answers)){
                                    $UserSelectedAnswers = json_decode($AttemptExamData->question_answers);
                                }
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
                                                {{__('languages.result.q_no')}} {{++$i}}:
                                                {{-- Display Question code --}}
                                                {{__('languages.question_code')}} : {{ $question->naming_structure_code }}
                                                {{-- Display Question types and with color code --}}
                                                <?php 
                                                    $LevelName = \App\Helpers\Helper::getLevelNameBasedOnLanguage($question->dificulaty_level);
                                                ?>
                                                @if($question->dificulaty_level == 1)
                                                    @if(isset($difficultyColor['Level1']) && !empty($difficultyColor['Level1']))
                                                        <span class="ml-5"> {{$LevelName}}</span> <span class="dot-color" style="background-color:{{ $difficultyColor['Level1']; }};border-radius: 50%;display: inline-block;top: 5px;position: relative;"></span>
                                                    @endif
                                                @elseif($question->dificulaty_level == 2)
                                                    @if(isset($difficultyColor['Level2']) && !empty($difficultyColor['Level2']))
                                                        <span class="ml-5"> {{$LevelName}}</span> <span class="dot-color" style="background-color:{{ $difficultyColor['Level2']; }};border-radius: 50%;display: inline-block;top: 5px;position: relative;"></span>
                                                    @endif
                                                @elseif($question->dificulaty_level == 3)
                                                    @if(isset($difficultyColor['Level3']) && !empty($difficultyColor['Level3']))
                                                        <span class="ml-5"> {{$LevelName}}</span> <span class="dot-color" style="background-color:{{ $difficultyColor['Level3']; }};border-radius: 50%;display: inline-block;top: 5px;position: relative;"></span>
                                                    @endif
                                                @elseif($question->dificulaty_level == 4)
                                                    @if(isset($difficultyColor['Level4']) && !empty($difficultyColor['Level4']))
                                                        <span class="ml-5"> {{$LevelName}}</span> <span class="dot-color" style="background-color:{{ $difficultyColor['Level4']; }};border-radius: 50%;display: inline-block;top: 5px;position: relative;"></span>
                                                    @endif
                                                @elseif($question->dificulaty_level == 5)
                                                    @if(isset($difficultyColor['Level5']) && !empty($difficultyColor['Level5']))
                                                        <span class="ml-5"> {{$LevelName}}</span> <span class="dot-color" style="background-color:{{ $difficultyColor['Level5']; }};border-radius: 50%;display: inline-block;top: 5px;position: relative;"></span>
                                                    @endif
                                                @endif
                                                {{-- Display Natural difficulties & Normalized difficulties --}}
                                                <span class="ml-5">{{__('languages.difficulty')}}: {{round($question['difficultyValue']['natural_difficulty'],2)}} ({{$question['difficultyValue']['normalized_difficulty']}}%)</span>
                                            </p>
                                            <div class="sm-que pl-2">
                                                <p><?php echo $question->{'question_'.app()->getLocale()}; ?></p>
                                            </div>
                                            <div class="row">
                                                    @if(Auth::check() && Auth::user()->role_id == 2)
                                                        <div class="col-md-4">
                                                            <h6>{{__('languages.my_clas')}}</h6>
                                                            @if(isset($question->answers->{'answer1_'.$AttemptExamData->language}))
                                                            <div class="sm-ans pl-2 pb-2">
                                                                <input type="radio" name="ans_que_{{$question->id}}_{{$studentId}}" value="1" class="radio mr-2" <?php if(isset($AnswerNumber[key($AnswerNumber)]) && $AnswerNumber[key($AnswerNumber)]->answer == 1){ echo 'checked';} ?> disabled>
                                                                <div class="answer-title mr-2 <?php if($question->answers->{'correct_answer_'.$AttemptExamData->language} == 1){ echo 'correct-answer';}else{ echo 'incorrect-answer';} ?>" style="@if($question->answers->{'correct_answer_'.$AttemptExamData->language} == 1){{$bg_correct_color}}@else{{$bg_incorrect_color}} @endif">A</div>
                                                                <div class="progress">
                                                                    <div class="progress-bar @if($question->answers->{'correct_answer_'.$AttemptExamData->language} === 1) ans-correct @else ans-incorrect @endif" role="progressbar"  aria-valuenow="{{$exams['percentageOfAnswer'][$question->id][1]}}" aria-valuemin="0" aria-valuemax="100" style="@if($question->answers->{'correct_answer_'.$AttemptExamData->language} == 1){{$bg_correct_color}}@else{{$bg_incorrect_color}} @endif;width:{{$exams['percentageOfAnswer'][$question->id][1]}}%">
                                                                        <div class="anser-detail pl-2">
                                                                            <?php echo $question->answers->{'answer1_'.app()->getLocale()}; ?>
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
                                                                <input type="radio" name="ans_que_{{$question->id}}_{{$studentId}}" value="2" class="radio mr-2" <?php if(isset($AnswerNumber[key($AnswerNumber)]) && $AnswerNumber[key($AnswerNumber)]->answer == 2){ echo 'checked';} ?> disabled>
                                                                <div class="answer-title mr-2 @if($question->answers->{'correct_answer_'.$AttemptExamData->language} === 2) correct-answer @else incorrect-answer @endif" style="@if($question->answers->{'correct_answer_'.$AttemptExamData->language} == 2){{$bg_correct_color}}@else{{$bg_incorrect_color}} @endif">B</div>
                                                                <div class="progress">
                                                                    <div class="progress-bar @if($question->answers->{'correct_answer_'.$AttemptExamData->language} === 2) ans-correct @else ans-incorrect @endif" role="progressbar"  aria-valuenow="{{$exams['percentageOfAnswer'][$question->id][2]}}" aria-valuemin="0" aria-valuemax="100" style="@if($question->answers->{'correct_answer_'.$AttemptExamData->language} == 2){{$bg_correct_color}}@else{{$bg_incorrect_color}} @endif;width:{{$exams['percentageOfAnswer'][$question->id][2]}}%">
                                                                        <div class="anser-detail pl-2">
                                                                            <?php echo $question->answers->{'answer2_'.app()->getLocale()}; ?>
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
                                                                <input type="radio" name="ans_que_{{$question->id}}_{{$studentId}}" value="3" class="radio mr-2" <?php if(isset($AnswerNumber[key($AnswerNumber)]) && $AnswerNumber[key($AnswerNumber)]->answer == 3){ echo 'checked';} ?> disabled>
                                                                <div class="answer-title mr-2 @if($question->answers->{'correct_answer_'.$AttemptExamData->language} === 3) correct-answer @else incorrect-answer @endif" style="@if($question->answers->{'correct_answer_'.$AttemptExamData->language} == 3){{$bg_correct_color}}@else{{$bg_incorrect_color}} @endif">C</div>
                                                                <div class="progress">
                                                                    <div class="progress-bar @if($question->answers->{'correct_answer_'.$AttemptExamData->language} === 3) ans-correct @else ans-incorrect @endif" role="progressbar"  aria-valuenow="{{$exams['percentageOfAnswer'][$question->id][3]}}" aria-valuemin="0" aria-valuemax="100" style="@if($question->answers->{'correct_answer_'.$AttemptExamData->language} == 3){{$bg_correct_color}}@else{{$bg_incorrect_color}} @endif;width:{{$exams['percentageOfAnswer'][$question->id][3]}}%">
                                                                        <div class="anser-detail pl-2">
                                                                            <?php echo $question->answers->{'answer3_'.app()->getLocale()}; ?>
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
                                                                <input type="radio" name="ans_que_{{$question->id}}_{{$studentId}}" value="4" class="radio mr-2" <?php if(isset($AnswerNumber[key($AnswerNumber)]) && $AnswerNumber[key($AnswerNumber)]->answer == 4){ echo 'checked';} ?> disabled>
                                                                <div class="answer-title mr-2 @if($question->answers->{'correct_answer_'.$AttemptExamData->language} === 4) correct-answer @else incorrect-answer @endif" style="@if($question->answers->{'correct_answer_'.$AttemptExamData->language} == 4){{$bg_correct_color}}@else{{$bg_incorrect_color}} @endif">D</div>
                                                                <div class="progress">
                                                                    <div class="progress-bar @if($question->answers->{'correct_answer_'.$AttemptExamData->language} === 4) ans-correct @else ans-incorrect @endif" role="progressbar"  aria-valuenow="{{$exams['percentageOfAnswer'][$question->id][4]}}" aria-valuemin="0" aria-valuemax="100" style="@if($question->answers->{'correct_answer_'.$AttemptExamData->language} == 4){{$bg_correct_color}}@else{{$bg_incorrect_color}} @endif;width:{{$exams['percentageOfAnswer'][$question->id][4]}}%">
                                                                        <div class="anser-detail pl-2">
                                                                            <?php echo $question->answers->{'answer4_'.app()->getLocale()}; ?>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="answer-progress">
                                                                    <p class="progress-percentage">{{$exams['percentageOfAnswer'][$question->id][4]}}%</p>
                                                                </div>
                                                            </div>
                                                            @endif
                                                        </div>
                                                    @endif
                                                    
                                                    @if(Auth::check() && Auth::user()->role_id == 5 || Auth::check() && Auth::user()->role_id == 2)
                                                       @php $my_school='col-md-4'; @endphp
                                                        @if(Auth::check() && Auth::user()->role_id == 5)
                                                           @php  $my_school='col-md-6'; @endphp
                                                        @endif
                                                    
                                                        <div class="{{ $my_school }}">
                                                            <h6>{{__('languages.my_school')}}</h6>
                                                            @if(isset($question->answers->{'answer1_'.$AttemptExamData->language}))
                                                            <div class="sm-ans pl-2 pb-2">
                                                                <div class="answer-title mr-2 <?php if($question->answers->{'correct_answer_'.$AttemptExamData->language} == 1){ echo 'correct-answer';}else{ echo 'incorrect-answer';} ?>" style="@if($question->answers->{'correct_answer_'.$AttemptExamData->language} == 1){{$bg_correct_color}}@else{{$bg_incorrect_color}} @endif">A</div>
                                                                <div class="progress">
                                                                    <div class="progress-bar @if($question->answers->{'correct_answer_'.$AttemptExamData->language} === 1) ans-correct @else ans-incorrect @endif" role="progressbar"  aria-valuenow="{{$exams['percentageOfAnswerSchool'][$question->id][1]}}" aria-valuemin="0" aria-valuemax="100" style="@if($question->answers->{'correct_answer_'.$AttemptExamData->language} == 1){{$bg_correct_color}}@else{{$bg_incorrect_color}} @endif;width:{{$exams['percentageOfAnswerSchool'][$question->id][1]}}%">
                                                                        <div class="anser-detail pl-2">
                                                                            <?php echo $question->answers->{'answer1_'.app()->getLocale()}; ?>
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
                                                                            <?php echo $question->answers->{'answer2_'.app()->getLocale()}; ?>
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
                                                                            <?php echo $question->answers->{'answer3_'.app()->getLocale()}; ?>
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
                                                                            <?php echo $question->answers->{'answer4_'.app()->getLocale()}; ?>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="answer-progress">
                                                                    <p class="progress-percentage">{{$exams['percentageOfAnswerSchool'][$question->id][4]}}%</p>
                                                                </div>
                                                            </div>
                                                            @endif
                                                        </div>
                                                   @endif
                                                    
                                                @php
                                                    $all_school='col-md-4';
                                                    if(Auth::check() && Auth::user()->role_id == 1){
                                                        $all_school='col-md-12';
                                                    }else if(Auth::check() && Auth::user()->role_id == 5){
                                                        $all_school='col-md-6';
                                                    }
                                                @endphp
                                                
                                                <div class="{{ $all_school }}">
                                                    <h6>{{__('languages.all_schools')}}</h6>
                                                    @if(isset($question->answers->{'answer1_'.$AttemptExamData->language}))
                                                    <div class="sm-ans pl-2 pb-2">
                                                        @if(Auth::check() && Auth::user()->role_id == 1)
                                                            <input type="radio" name="ans_que_{{$question->id}}_{{$studentId}}" value="1" class="radio mr-2" <?php if(isset($AnswerNumber[key($AnswerNumber)]) && $AnswerNumber[key($AnswerNumber)]->answer == 1){ echo 'checked';} ?> disabled>
                                                        @endif
                                                        <div class="answer-title mr-2 <?php if($question->answers->{'correct_answer_'.$AttemptExamData->language} == 1){ echo 'correct-answer';}else{ echo 'incorrect-answer';} ?>" style="@if($question->answers->{'correct_answer_'.$AttemptExamData->language} == 1){{$bg_correct_color}}@else{{$bg_incorrect_color}} @endif">A</div>
                                                        <div class="progress">
                                                            <div class="progress-bar @if($question->answers->{'correct_answer_'.$AttemptExamData->language} === 1) ans-correct @else ans-incorrect @endif" role="progressbar"  aria-valuenow="{{$exams['percentageOfAnswerAllSchool'][$question->id][1]}}" aria-valuemin="0" aria-valuemax="100" style="@if($question->answers->{'correct_answer_'.$AttemptExamData->language} == 1){{$bg_correct_color}}@else{{$bg_incorrect_color}} @endif;width:{{$exams['percentageOfAnswerAllSchool'][$question->id][1]}}%">
                                                                <div class="anser-detail pl-2">
                                                                    <?php echo $question->answers->{'answer1_'.app()->getLocale()}; ?>
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
                                                        @if(Auth::check() && Auth::user()->role_id == 1)
                                                            <input type="radio" name="ans_que_{{$question->id}}_{{$studentId}}" value="2" class="radio mr-2" <?php if(isset($AnswerNumber[key($AnswerNumber)]) && $AnswerNumber[key($AnswerNumber)]->answer == 2){ echo 'checked';} ?> disabled>
                                                        @endif
                                                        <div class="answer-title mr-2 @if($question->answers->{'correct_answer_'.$AttemptExamData->language} === 2) correct-answer @else incorrect-answer @endif" style="@if($question->answers->{'correct_answer_'.$AttemptExamData->language} == 2){{$bg_correct_color}}@else{{$bg_incorrect_color}} @endif">B</div>
                                                        <div class="progress">
                                                            <div class="progress-bar @if($question->answers->{'correct_answer_'.$AttemptExamData->language} === 2) ans-correct @else ans-incorrect @endif" role="progressbar"  aria-valuenow="{{$exams['percentageOfAnswerAllSchool'][$question->id][2]}}" aria-valuemin="0" aria-valuemax="100" style="@if($question->answers->{'correct_answer_'.$AttemptExamData->language} == 2){{$bg_correct_color}}@else{{$bg_incorrect_color}} @endif;width:{{$exams['percentageOfAnswerAllSchool'][$question->id][2]}}%">
                                                                <div class="anser-detail pl-2">
                                                                    <?php echo $question->answers->{'answer2_'.app()->getLocale()}; ?>
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
                                                        @if(Auth::check() && Auth::user()->role_id == 1)
                                                            <input type="radio" name="ans_que_{{$question->id}}_{{$studentId}}" value="3" class="radio mr-2" <?php if(isset($AnswerNumber[key($AnswerNumber)]) && $AnswerNumber[key($AnswerNumber)]->answer == 3){ echo 'checked';} ?> disabled>
                                                        @endif
                                                        <div class="answer-title mr-2 @if($question->answers->{'correct_answer_'.$AttemptExamData->language} === 3) correct-answer @else incorrect-answer @endif" style="@if($question->answers->{'correct_answer_'.$AttemptExamData->language} == 3){{$bg_correct_color}}@else{{$bg_incorrect_color}} @endif">C</div>
                                                        <div class="progress">
                                                            <div class="progress-bar @if($question->answers->{'correct_answer_'.$AttemptExamData->language} === 3) ans-correct @else ans-incorrect @endif" role="progressbar"  aria-valuenow="{{$exams['percentageOfAnswerAllSchool'][$question->id][3]}}" aria-valuemin="0" aria-valuemax="100" style="@if($question->answers->{'correct_answer_'.$AttemptExamData->language} == 3){{$bg_correct_color}}@else{{$bg_incorrect_color}} @endif;width:{{$exams['percentageOfAnswerAllSchool'][$question->id][3]}}%">
                                                                <div class="anser-detail pl-2">
                                                                    <?php echo $question->answers->{'answer3_'.app()->getLocale()}; ?>
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
                                                        @if(Auth::check() && Auth::user()->role_id == 1)
                                                            <input type="radio" name="ans_que_{{$question->id}}_{{$studentId}}" value="4" class="radio mr-2" <?php if(isset($AnswerNumber[key($AnswerNumber)]) && $AnswerNumber[key($AnswerNumber)]->answer == 4){ echo 'checked';} ?> disabled>
                                                        @endif
                                                        <div class="answer-title mr-2 @if($question->answers->{'correct_answer_'.$AttemptExamData->language} === 4) correct-answer @else incorrect-answer @endif" style="@if($question->answers->{'correct_answer_'.$AttemptExamData->language} == 4){{$bg_correct_color}}@else{{$bg_incorrect_color}} @endif">D</div>
                                                        <div class="progress">
                                                            <div class="progress-bar @if($question->answers->{'correct_answer_'.$AttemptExamData->language} === 4) ans-correct @else ans-incorrect @endif" role="progressbar"  aria-valuenow="{{$exams['percentageOfAnswerAllSchool'][$question->id][4]}}" aria-valuemin="0" aria-valuemax="100" style="@if($question->answers->{'correct_answer_'.$AttemptExamData->language} == 4){{$bg_correct_color}}@else{{$bg_incorrect_color}} @endif;width:{{$exams['percentageOfAnswerAllSchool'][$question->id][4]}}%">
                                                                <div class="anser-detail pl-2">
                                                                    <?php echo $question->answers->{'answer4_'.app()->getLocale()}; ?>
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
                                            if(isset($AnswerNumber) && !empty($AnswerNumber)){
                                                $nodeId = $question->answers->{'answer'.$AnswerNumber[key($AnswerNumber)]->answer.'_node_relation_id_'.$AttemptExamData->language};
                                                if(empty($nodeId)){
                                                    $nodeId = App\Helpers\Helper::getWeaknessNodeId($question->id, $AnswerNumber[key($AnswerNumber)]->answer);
                                                }
                                            }else{
                                                $nodeId = 0;
                                            }
                                            @endphp
                                            <h6 class="mt-3"><b>{{__('languages.report.weakness')}}:</b>
                                                @if(app()->getLocale() == 'ch')
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
