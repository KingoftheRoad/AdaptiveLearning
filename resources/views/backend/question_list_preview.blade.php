
		<div class="wrapper d-flex align-items-stretch sm-deskbord-main-sec">
	        <div id="content" > {{-- class="pl-2 pb-5"--}}
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
                            
                            @if(!empty($Questions))
                                @foreach($Questions as $key => $question)
                                    <div class="row">
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
                                                <span class="ml-5">{{__('languages.difficulty')}}:
                                                    @if(isset($question->difficultyValue['natural_difficulty']))
                                                    {{round($question->difficultyValue['natural_difficulty'],2)}} ({{$question->difficultyValue['normalized_difficulty']}}%)
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
                                            @endphp
                                            @if(isset($answer[0]))
                                            <div class="sm-ans pl-2 pb-2">
                                                <div class="answer-title mr-2 view-report-answer-title">1</div>
                                                <div class="progress">
                                                    <div >
                                                        <div class="anser-detail pl-2">
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
                                                <div class="answer-title mr-2 view-report-answer-title">2</div>
                                                <div class="progress">
                                                    <div >
                                                        <div class="anser-detail pl-2">
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
                                                <div class="answer-title mr-2 view-report-answer-title">3</div>
                                                <div class="progress">
                                                    <div >
                                                        <div class="anser-detail pl-2">
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
                                                <div class="answer-title mr-2 view-report-answer-title">4</div>
                                                <div class="progress">
                                                    <div >
                                                        <div class="anser-detail pl-2">
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
</div>