
		<div class="wrapper d-flex align-items-stretch sm-deskbord-main-sec w-100">
	        <div id="content">
            @if (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
            @endif
            <div class="sm-right-detail-sec">
				<div class="remove-teacher-question-list-padding">
                    <!-- Start Student List -->

					<div class="sm-add-user-sec card">
						<div class="select-option-sec pb-2 card-body">
                            <div class="col-sm-3 col-md-3 col-lg-3">
                                @php
                                $bg_correct_color='background-color:'.App\Helpers\Helper::getGlobalConfiguration('question_correct_color');
                                $bg_incorrect_color='background-color:'.App\Helpers\Helper::getGlobalConfiguration('question_incorrect_color');
                                @endphp
                                @if(!empty($difficultyLevels))
                                    @php $i=1; $difficultyColor= []; @endphp
                                    @foreach($difficultyLevels as $difficultyLevel)
                                        @php $difficultyColor['Level'.$i] = $difficultyLevel->difficulty_level_color;  $i+=1;@endphp
                                    @endforeach
                                @endif
                            </div>
                            
                            @if(!empty($question_list))
                                @foreach($question_list as $key => $question)
                                    <div class="row">
                                        <input type="hidden" name="qIndex[]" value="{{ $question->id }}" />
                                        <div class="sm-que-option pl-3">
                                            <p class="sm-title bold">{{__('languages.result.q_no')}}: {{$loop->iteration}} {{__('languages.question_code')}} : {{ $question->naming_structure_code }}
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
                                                        <span class="ml-5">{{$LevelName}}</span> <span class="dot-color" style="background-color:{{ $difficultyColor['Level2']; }};border-radius: 50%;display: inline-block;top: 5px;position: relative;"></span>
                                                    @endif
                                                @elseif($question->dificulaty_level == 3)
                                                    @if(isset($difficultyColor['Level3']) && !empty($difficultyColor['Level3']))
                                                        <span class="ml-5">{{$LevelName}}</span> <span class="dot-color" style="background-color:{{ $difficultyColor['Level3']; }};border-radius: 50%;display: inline-block;top: 5px;position: relative;"></span>
                                                    @endif
                                                @elseif($question->dificulaty_level == 4)
                                                    @if(isset($difficultyColor['Level4']) && !empty($difficultyColor['Level4']))
                                                        <span class="ml-5">{{$LevelName}}</span> <span class="dot-color" style="background-color:{{ $difficultyColor['Level4']; }};border-radius: 50%;display: inline-block;top: 5px;position: relative;"></span>
                                                    @endif
                                                @elseif($question->dificulaty_level == 5)
                                                    @if(isset($difficultyColor['Level5']) && !empty($difficultyColor['Level5']))
                                                        <span class="ml-5">{{$LevelName}}</span> <span class="dot-color" style="background-color:{{ $difficultyColor['Level5']; }};border-radius: 50%;display: inline-block;top: 5px;position: relative;"></span>
                                                    @endif
                                                @endif
                                                @php
                                                    $normalized_difficulty=\App\Helpers\Helper::getNormalizedAbility($question->PreConfigurationDifficultyLevel->title);
                                                @endphp
                                                <span class="ml-5">{{__('languages.difficulty')}}:
                                                    @if(isset($question->PreConfigurationDifficultyLevel->title) && $question->PreConfigurationDifficultyLevel->title!="")
                                                    {{-- {{round($question->PreConfigurationDifficultyLevel->title,2)}} ({{$normalized_difficulty}}%) --}}
                                                    {{-- {{round($question->PreConfigurationDifficultyLevel->title,2)}} ({{App\Helpers\Helper::GetShortPercentage($normalized_difficulty)}}%) / --}}
                                                    {{App\Helpers\Helper::GetShortPercentage($normalized_difficulty)}}
                                                    @endif
                                                </span>
                                            </p>
                                            <div class="sm-que pl-2">
                                                <p><?php echo $question->{'question_'.app()->getLocale()}; ?></p>
                                            </div>
                                            @php
                                                if(!empty($question->answers)){
                                                    $answer = [
                                                                $question->answers->{'answer1_'.app()->getLocale()},
                                                                $question->answers->{'answer2_'.app()->getLocale()},
                                                                $question->answers->{'answer3_'.app()->getLocale()},
                                                                $question->answers->{'answer4_'.app()->getLocale()},
                                                            ];
                                                    shuffle($answer);
                                                }
                                                $correctAnswerValue = 'answer'.$question->answers->{'correct_answer_'.app()->getLocale()}.'_'.app()->getLocale();
                                                $correctAnswer = $question->answers->{$correctAnswerValue};
                                            @endphp
                                            @if(isset($answer[0]))
                                            <div class="sm-ans pl-2 pb-2">
                                                <input type="radio" name="ans_que_{{$question->id}}" value="1" class="radio mr-2" <?php if($answer[0]==$correctAnswer){ echo 'checked';} ?> disabled>
                                                <div class="answer-title mr-2 <?php if($answer[0]==$correctAnswer){ echo 'correct-answer';}else{ echo 'incorrect-answer';} ?>" style="@if($answer[0]==$correctAnswer){{$bg_correct_color}}@else{{$bg_incorrect_color}} @endif" >A</div>
                                                {{-- <div class="answer-title mr-2 view-report-answer-title">1</div> --}}
                                                <div class="progress">
                                                    <div class="progress-bar @if($correctAnswer == $answer[0]) ans-correct @else ans-incorrect @endif" role="progressbar"   aria-valuemin="0" aria-valuemax="100" style="@if($correctAnswer == $answer[0]){{$bg_correct_color}}@else{{$bg_incorrect_color}} @endif;width:100%">
                                                        <div class="anser-detail pl-2 overlapping-text-option-prevent">
                                                            <?php 
                                                                echo $answer[0];
                                                            ?>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            @endif

                                            @if(isset($answer[1]))
                                            <div class="sm-ans pl-2 pb-2">
                                                <input type="radio" name="ans_que_{{$question->id}}" value="2" class="radio mr-2" <?php if($answer[1]==$correctAnswer){ echo 'checked';} ?> disabled>
                                                <div class="answer-title mr-2 <?php if($answer[1]==$correctAnswer){ echo 'correct-answer';}else{ echo 'incorrect-answer';} ?>" style="@if($answer[1]==$correctAnswer){{$bg_correct_color}}@else{{$bg_incorrect_color}} @endif" >B</div>
                                                {{-- <div class="answer-title mr-2 view-report-answer-title">2</div> --}}
                                                <div class="progress">
                                                    <div class="progress-bar @if($correctAnswer ==$answer[1]) ans-correct @else ans-incorrect @endif" role="progressbar"   aria-valuemin="0" aria-valuemax="100" style="@if($correctAnswer == $answer[1]){{$bg_correct_color}}@else{{$bg_incorrect_color}} @endif;width:100%">
                                                        <div class="anser-detail pl-2 overlapping-text-option-prevent">
                                                            <?php 
                                                                echo $answer[1];
                                                            ?>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            @endif

                                            @if(isset($answer[2]))
                                            <div class="sm-ans pl-2 pb-2">
                                                <input type="radio" name="ans_que_{{$question->id}}" value="3" class="radio mr-2" <?php if($answer[2]==$correctAnswer){ echo 'checked';} ?> disabled>
                                                <div class="answer-title mr-2 <?php if($answer[2]==$correctAnswer){ echo 'correct-answer';}else{ echo 'incorrect-answer';} ?>" style="@if($answer[2]==$correctAnswer){{$bg_correct_color}}@else{{$bg_incorrect_color}} @endif" >C</div>
                                                {{-- <div class="answer-title mr-2 view-report-answer-title">3</div> --}}
                                                <div class="progress">
                                                    <div class="progress-bar @if($correctAnswer == $answer[2]) ans-correct @else ans-incorrect @endif" role="progressbar"   aria-valuemin="0" aria-valuemax="100" style="@if($correctAnswer == $answer[2]){{$bg_correct_color}}@else{{$bg_incorrect_color}} @endif;width:100%">
                                                        <div class="anser-detail pl-2 overlapping-text-option-prevent">
                                                            <?php 
                                                                echo $answer[2];
                                                            ?>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            @endif

                                            @if(isset($answer[3]))
                                            <div class="sm-ans pl-2 pb-2">
                                                <input type="radio" name="ans_que_{{$question->id}}" value="4" class="radio mr-2" <?php if($answer[3]==$correctAnswer){ echo 'checked';} ?> disabled>
                                                <div class="answer-title mr-2 <?php if($answer[3]==$correctAnswer){ echo 'correct-answer';}else{ echo 'incorrect-answer';} ?>" style="@if($answer[3]==$correctAnswer){{$bg_correct_color}}@else{{$bg_incorrect_color}} @endif" >D</div>
                                                {{-- <div class="answer-title mr-2 view-report-answer-title">4</div> --}}
                                                <div class="progress">
                                                    <div class="progress-bar @if($correctAnswer == $answer[3]) ans-correct @else ans-incorrect @endif" role="progressbar"   aria-valuemin="0" aria-valuemax="100" style="@if($correctAnswer == $answer[3]){{$bg_correct_color}}@else{{$bg_incorrect_color}} @endif;width:100%">
                                                        <div class="anser-detail pl-2 overlapping-text-option-prevent">
                                                            <?php 
                                                                echo $answer[3];
                                                            ?>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            @endif
                                        </div>
                                </div>
                                @endforeach
                            @endif
                        </div> 
                    </div>
                </div>
            </div>
        </div>
    </div>