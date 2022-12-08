@extends('backend.layouts.app')
    @section('content')
		<div class="wrapper d-flex align-items-stretch sm-deskbord-main-sec">
            @include('backend.layouts.sidebar')
	        <div id="content" class="pl-2 pb-5">
            @include('backend.layouts.header')
            @if (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
            @endif
            <div class="sm-right-detail-sec pl-5 pr-5">
				<div class="container-fluid">
					<div class="row">
						<div class="col-md-12">
                            <div class="sec-title">
								<h5 class="mb-4">{{__('languages.result.student_result')}}</h5>
							</div>
						</div>
					</div>
                    <div class="row">
						<div class="col-md-12">
							<div class="sec-title">
                                <a href="{{ route('question-wizard') }}" class="btn-back" >{{__('languages.back')}}</a>
							</div>
							<hr class="blue-line">
						</div>
					</div>
                    <!-- Start Student List -->
					<div class="sm-add-user-sec card">
						<div class="select-option-sec pb-2 card-body">
                            @if(!empty($AttemptExamData) && !empty($AttemptExamData->server_details))
                            @php 
                            $ServerDetails = json_decode($AttemptExamData->server_details);
                            @endphp
                            <div class="row all-information-sec">
                                <div class="col-sm-12 col-md-12 col-lg-12">
                                    <h5>{{__('languages.result.server_details')}}</h5>
                                    <div class="ip-address-main information">
                                        <h5>{{__('languages.result.ip_address')}} </h5>
                                        <p>{{$ServerDetails->IP}}</p>
                                    </div>
                                    <div class="ip-address-main information">
                                        <h5>{{__('languages.result.browser_name')}} </h5>
                                        <p>{{$ServerDetails->Browser}}</p>
                                    </div>
                                    <div class="ip-address-main information">
                                        <h5>{{__('languages.result.request_date_time')}} </h5>
                                        <p><?php echo date('d/m/Y h:i:s',strtotime($ServerDetails->DateTime)); ?></p>
                                    </div>
                                </div>
                            </div>
                            @endif
                            <div class="row all-information-sec">
                                <div class="col-lg-3 col-md-4 col-sm-12">
                                    <label>{{__('languages.result.test_title')}} : {{ $ExamData->title }}</label>
                                </div>
                                <div class="col-lg-3 col-md-4 col-sm-12">
                                    <label>{{__('languages.reference_number')}} : {{ $ExamData->reference_no }}</label>
                                </div>
                                @if(!empty($ExamData->publish_date))
                                <div class="col-lg-3 col-md-4 col-sm-12">
                                    <label>{{__('languages.result.date_of_release')}} : {{date('d/m/Y',strtotime($ExamData->publish_date))}}</label>
                                </div>
                                @endif
                                <div class="col-lg-3 col-md-4 col-sm-12">
                                    <label>{{__('languages.result.start_date')}} : {{date('d/m/Y',strtotime($ExamData->from_date))}}</label>
                                </div>
                                <div class="col-lg-3 col-md-4 col-sm-12">
                                    <label>{{__('languages.result.end_date')}} : {{date('d/m/Y',strtotime($ExamData->to_date))}}</label>
                                </div>
                                <div class="col-lg-3 col-md-4 col-sm-12">
                                    <label>{{__('languages.result.result_date')}} : {{date('d/m/Y',strtotime($ExamData->result_date))}}</label>
                                </div>
                                <div class="col-lg-3 col-md-4 col-sm-12">
                                    <label>{{__('languages.result.number_of_questions')}} : {{ count($Questions) }}</label>
                                </div>
                                <div class="col-lg-3 col-md-4 col-sm-12">
                                    <label>{{__('languages.result.number_of_correct_answers')}} : {{$AttemptExamData->total_correct_answers}}</label>
                                </div>
                                <div class="col-lg-3 col-md-4 col-sm-12">
                                    <label>{{__('languages.result.number_of_incorrect_answers')}} : {{$AttemptExamData->total_wrong_answers}}</label>
                                </div>
                                <div class="col-lg-3 col-md-4 col-sm-12">
                                    <label>{{__('languages.result.test_time_taken')}} : {{ $AttemptExamData->exam_taking_timing }}</label>
                                </div>
                                @php $accuracy = 0; @endphp
                                <div class="col-lg-3 col-md-4 col-sm-12">
                                    <label>{{__('languages.report.accuracy')}} : 
                                        @if(!empty(count($Questions)))
                                        @php $accuracy = round((($AttemptExamData->total_correct_answers / count($Questions)) * 100), 2); @endphp
                                        {{ $accuracy }}%
                                        @endif
                                    </label>
                                </div>
                                <div class="col-lg-3 col-md-4 col-sm-12">
                                    <label>{{__('languages.report.ability')}} : {{(!empty($AttemptExamData->student_ability)) ? App\Helpers\Helper::GetShortPercentage(App\Helpers\Helper::getNormalizedAbility($AttemptExamData->student_ability)) : 0}}</label>
                                </div>
                                <div class="col-lg-3 col-md-4 col-sm-12">
                                    <label>{{__('languages.credit_points')}} : 
                                        {{App\Helpers\Helper::GetCountCrediPointsStudent($AttemptExamData->exam_id,$studentId)}}
                                    </label>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-3 col-md-4 col-sm-12 pb-2">
                                    <button type="button" class="btn btn-success performance_graph" data-studentid="{{$studentId}}" data-examid="{{$ExamData->id}}">{{__('languages.performance_graph')}}</button>
                                </div>
                            </div>
                            <div class="row all-information-sec">
                                <div class="col-sm-3 col-md-3 col-lg-3">
                                    @php
                                    $bg_correct_color='background-color:'.App\Helpers\Helper::getGlobalConfiguration('question_correct_color');
                                    $bg_incorrect_color='background-color:'.App\Helpers\Helper::getGlobalConfiguration('question_incorrect_color');
                                    @endphp
                                    <h5>{{__('languages.questions_by_difficulties')}}</h5>
                                    @if(!empty($difficultyLevels))
                                        @php $i=1; $difficultyColor= []; @endphp
                                        @foreach($difficultyLevels as $difficultyLevel)
                                            @if(!empty($questionDifficultyGraph['Level'.$i]))
                                            <h6>{{__($difficultyLevel->difficulty_level_name)}}</h6>
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
                                <div class="col-sm-9 col-md-9 col-lg-9">
                                    <h5>{{__('languages.speed')}}</h5>
                                    <p><?php echo App\Helpers\Helper::getQuestionPerSpeed($ExamData->id,$studentId); ?> {{__('languages.sec_per_question')}}</p>
                                </div>
                                <div class="col-sm-12 col-md-12 col-lg-12">
                                    <span class="dot-color" style="background-color:{{ App\Helpers\Helper::getGlobalConfiguration('question_correct_color')}};border-radius: 50%;display: inline-block;"></span>
                                    <label>{{__('languages.correct_questions')}}</label>
                                    <span class="dot-color" style="background-color:{{ App\Helpers\Helper::getGlobalConfiguration('question_incorrect_color')}};border-radius: 50%;display: inline-block;"></span>
                                    <label>{{__('languages.incorrect_questions')}}</label>
                                </div>
                            </div>
                            <hr>
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
                                    <div class="row">
                                        <div class="sm-que-option pl-3">
                                            <p class="sm-title bold">{{__('languages.result.q_no')}} {{$loop->iteration}}: {{__('languages.question_code')}} : {{ $question->naming_structure_code }}
                                                {{-- Display Question types and with color code --}}
                                                    @if($question->dificulaty_level == 1)
                                                        @if(isset($difficultyColor['Level1']) && !empty($difficultyColor['Level1']))
                                                            <span class="ml-5"> {{__('Level 1')}}</span> <span class="dot-color" style="background-color:{{ $difficultyColor['Level1']; }};border-radius: 50%;display: inline-block;top: 5px;position: relative;"></span>
                                                        @endif
                                                    @elseif($question->dificulaty_level == 2)
                                                        @if(isset($difficultyColor['Level1']) && !empty($difficultyColor['Level2']))
                                                            <span class="ml-5"> {{__('Level 2')}}</span> <span class="dot-color" style="background-color:{{ $difficultyColor['Level2']; }};border-radius: 50%;display: inline-block;top: 5px;position: relative;"></span>
                                                        @endif
                                                    @elseif($question->dificulaty_level == 3)
                                                        @if(isset($difficultyColor['Level3']) && !empty($difficultyColor['Level3']))
                                                            <span class="ml-5"> {{__('Level 3')}}</span> <span class="dot-color" style="background-color:{{ $difficultyColor['Level3']; }};border-radius: 50%;display: inline-block;top: 5px;position: relative;"></span>
                                                        @endif
                                                    @elseif($question->dificulaty_level == 4)
                                                        @if(isset($difficultyColor['Level4']) && !empty($difficultyColor['Level4']))
                                                            <span class="ml-5"> {{__('Level 4')}}</span> <span class="dot-color" style="background-color:{{ $difficultyColor['Level4']; }};border-radius: 50%;display: inline-block;top: 5px;position: relative;"></span>
                                                        @endif
                                                    @elseif($question->dificulaty_level == 5)
                                                        @if(isset($difficultyColor['Level5']) && !empty($difficultyColor['Level5']))
                                                            <span class="ml-5"> {{__('Level 5')}}</span> <span class="dot-color" style="background-color:{{ $difficultyColor['Level5']; }};border-radius: 50%;display: inline-block;top: 5px;position: relative;"></span>
                                                        @endif
                                                    @endif
                                                <span class="ml-5">{{__('languages.difficulty')}}:
                                                    @if(isset($question->difficultyValue['natural_difficulty']))
                                                    {{App\Helpers\Helper::GetShortPercentage($question->difficultyValue['normalized_difficulty'])}}
                                                    @endif
                                                </span>
                                            </p>
                                            <div class="sm-que pl-2">
                                                <p><?php echo $question->{'question_'.app()->getLocale()}; ?></p>
                                            </div>

                                            @if(isset($question->answers->{'answer1_'.$AttemptExamData->language}))
                                            <div class="sm-ans pl-2 pb-2">
                                                <input type="radio" name="ans_que_{{$question->id}}" value="1" class="radio mr-2" <?php if(isset($AnswerNumber[key($AnswerNumber)]) && $AnswerNumber[key($AnswerNumber)]->answer == 1){ echo 'checked';} ?> disabled>
                                                <div class="answer-title mr-2 <?php if($question->answers->{'correct_answer_'.$AttemptExamData->language} == 1){ echo 'correct-answer';}else{ echo 'incorrect-answer';} ?>" style="@if($question->answers->{'correct_answer_'.$AttemptExamData->language} == 1){{$bg_correct_color}}@else{{$bg_incorrect_color}} @endif" >A</div>
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
                                                <input type="radio" name="ans_que_{{$question->id}}" value="2" class="radio mr-2" <?php if(isset($AnswerNumber[key($AnswerNumber)]) && $AnswerNumber[key($AnswerNumber)]->answer == 2){ echo 'checked';} ?> disabled>
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
                                                <input type="radio" name="ans_que_{{$question->id}}" value="3" class="radio mr-2" <?php if(isset($AnswerNumber[key($AnswerNumber)]) && $AnswerNumber[key($AnswerNumber)]->answer == 3){ echo 'checked';} ?> disabled>
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
                                                <input type="radio" name="ans_que_{{$question->id}}" value="4" class="radio mr-2" <?php if(isset($AnswerNumber[key($AnswerNumber)]) && $AnswerNumber[key($AnswerNumber)]->answer == 4){ echo 'checked';} ?> disabled>
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
                                        <div class="sm-answer pl-5 pt-2">
                                            <span class="badge badge-success getSolutionQuestionImage" data-question-code ="{{$question->naming_structure_code}}">{{__('languages.full_question_solution')}}</span>
                                        <?php if(isset($AnswerNumber[key($AnswerNumber)]) && $AnswerNumber[key($AnswerNumber)]->answer == $question->answers->{'correct_answer_'.$AttemptExamData->language}){ ?>
                                            <span class="badge badge-success">{{__('languages.result.correct_answer')}}</span>
                                            {{-- <span class="badge badge-primary getIntelligentTutorVideos pointer-event" data-question-node ="{{$question->naming_structure_code}}">{{__('languages.intelligent_tutor')}} {{__('languages.videos')}}</span> --}}
                                        <?php }else{ ?>
                                            <span class="badge badge-danger">{{__('languages.result.incorrect_answer')}}</span>
                                            {{-- <span class="badge badge-primary getIntelligentTutorVideos pointer-event" data-question-node ="{{$question->naming_structure_code}}">{{__('languages.intelligent_tutor')}} {{__('languages.videos')}}</span> --}}
                                            <?php
                                            $nodeId=0;
                                            if(isset($AnswerNumber[key($AnswerNumber)])){
                                                $nodeId = $question->answers->{'answer'.$AnswerNumber[key($AnswerNumber)]->answer.'_node_relation_id_'.$AttemptExamData->language};
                                                if(empty($nodeId)){
                                                    $nodeId = App\Helpers\Helper::getWeaknessNodeId($question->id, $AnswerNumber[key($AnswerNumber)]->answer);
                                                }
                                            }
                                        ?>
                                        <h6 class="mt-3"><b>{{__('languages.report.weakness')}}:</b>
                                            @if(app()->getLocale() == 'ch')
                                                @if($nodeId!=0 && isset($nodeWeaknessListCh[$nodeId]))
                                                    @php
                                                    $WeaknessList[]=$nodeWeaknessListCh[$nodeId];
                                                    $WeaknessListWithId[$nodeId]=$nodeWeaknessListCh[$nodeId];
                                                    @endphp
                                                    {{-- <span class="getIntelligentTutorVideos pointer-event" data-question-node ="{{\App\Helpers\Helper::getNodeNameById($nodeId)}}">{{$nodeWeaknessListCh[$nodeId]}}</span> --}}
                                                @endif
                                            @else
                                                @if($nodeId!=0 && isset($nodeWeaknessList[$nodeId]))
                                                    @php
                                                    $WeaknessList[]=$nodeWeaknessList[$nodeId];
                                                    $WeaknessListWithId[$nodeId]=$nodeWeaknessList[$nodeId];
                                                    @endphp                                    
                                                   {{-- <span class="getIntelligentTutorVideos pointer-event" data-question-node ="{{\App\Helpers\Helper::getNodeNameById($nodeId)}}">{{$nodeWeaknessList[$nodeId]}}</span> --}}
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
                            $KeyImprovementData='';
                            $KeyWeaknessData='';
                            $checkImprovement=1; 
                            @endphp
                            
                            @foreach($AllWeakness as $WeaknessKey => $WeaknessNof)
                                @if(isset($WeaknessListWithId[$WeaknessKey]))
                                    @php
                                    if($checkImprovement<=2){
                                        $KeyImprovementData.='<li style="list-style:disc;">'. $WeaknessListWithId[$WeaknessKey].'</li>';
                                        // $KeyImprovementData.='<span class="getIntelligentTutorVideos pointer-event" data-question-node ="'. \App\Helpers\Helper::getNodeNameById($WeaknessKey) .'"><li style="list-style:disc;">'. $WeaknessListWithId[$WeaknessKey].'</li></span>';
                                    }else{
                                        $KeyWeaknessData.='<li style="list-style:disc;">'. $WeaknessListWithId[$WeaknessKey].'</li>';
                                        // $KeyWeaknessData.='<span class="getIntelligentTutorVideos pointer-event" data-question-node ="'. \App\Helpers\Helper::getNodeNameById($WeaknessKey) .'"><li style="list-style:disc;">'. $WeaknessListWithId[$WeaknessKey].'</li></span>';
                                    }
                                    $checkImprovement++;
                                    @endphp
                                @endif
                            @endforeach
                            <div class="student-key-improvement">
                                <h6 class="mt-3 pl-3 key-improvement-title text-dark">{{__('languages.report.key_improvement_points')}}</h6>
                                <ul class="list-unstyled ml-5">
                                    @if($KeyImprovementData!="")
                                    {{-- <a href="{{route('getStudentExamList')}}">{!! $KeyImprovementData !!}</a> --}}
                                    {!! $KeyImprovementData !!}
                                    @else
                                    <li>{{__('languages.report.no_key_improvement_available')}}</li>
                                    @endif
                                </ul>
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
                                            {{-- <a href="{{route('getStudentExamList')}}">{!! $KeyWeaknessData !!}</a> --}}
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
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Start Performance Graph Popup -->
<div class="modal" id="studentPerformanceGraph" tabindex="-1" aria-labelledby="studentPerformanceGraph" aria-hidden="true" data-backdrop="static">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="post">
                <div class="modal-header">
                    <h4 class="modal-title w-100">{{__('languages.student_performance_graph')}}</h4>
                    <button type="button" class="close" onclick="destroyCanvas()" data-dismiss="modal" aria-hidden="true">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="Graph-body">
                        <img src="" id="graph-image" class="img-fluid">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">{{__('languages.close')}}</button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- End Performance Analysis Popup -->

@include('backend.layouts.footer')
<script type="text/javascript">
$(document).ready(function() {
    window.history.pushState(null, "", window.location.href);
    window.onpopstate = function() {
        window.history.pushState(null, "", window.location.href);
    };
});

/**
* USE : Student can check own result using graph
* */
$(function() {
    $(document).on('click', '.performance_graph', function(e) {
        $("#cover-spin").show();
        $examid = $(this).attr('data-examid');
        $studentid = $(this).attr('data-studentid')
        if($examid && $studentid){
            $.ajax({
                url: BASE_URL + '/report/getPerformanceGraphCurrentStudent',
                type: 'post',
                data : {
                    '_token': $('meta[name="csrf-token"]').attr('content'),
                    'exam_id' : $examid,
                    'student_id' : $studentid
                },
                success: function(response) {
                    var ResposnseData = JSON.parse(JSON.stringify(response));
                    if(ResposnseData.data.length != 0){
                        $('#graph-image').attr('src','data:image/jpg;base64,'+ ResposnseData.data);
                        $('#studentPerformanceGraph').modal('show');
                    }else{
                        toastr.error(STUDENT_PERFORMANCE_DATA_NOT_FOUND);
                    }
                    $("#cover-spin").hide();
                },
                error: function(response) {
                    ErrorHandlingMessage(response);
                }
            });
        }else{
            toastr.error(DATA_NOT_FOUND);
            $("#cover-spin").hide();
        }
    });
});
</script>
@endsection