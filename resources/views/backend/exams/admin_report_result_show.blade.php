
{{-- <div class="wrapper d-flex align-items-stretch sm-deskbord-main-sec"> --}}
    @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif
    <div class="sm-right-detail-sec">
        <div class="container-fluid">
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
                                            <p class="sm-title bold">{{__('languages.result.q_no')}}: {{$loop->iteration}}</p>
                                                <div class="sm-que pl-2">
                                                    <p><?php echo $question->{'question_en'}; ?></p>
                                                </div>
                                                @if(isset($question->answers->{'answer1_'.$AttemptExamData->language}))
                                                    <div class="sm-ans pl-2 pb-2">
                                                    <input type="radio" name="ans_que_{{$question->id}}" value="1" class="radio mr-2" <?php if(isset($AnswerNumber[key($AnswerNumber)]) && $AnswerNumber[key($AnswerNumber)]->answer == 1){ echo 'checked';} ?> disabled>
                                                        <div class="answer-title mr-2 <?php if($question->answers->{'correct_answer_'.$AttemptExamData->language} == 1){ echo 'correct-answer';}else{ echo 'incorrect-answer';} ?>">A</div>
                                                        <div class="progress">
                                                            <div class="progress-bar @if($question->answers->{'correct_answer_'.$AttemptExamData->language} === 1) ans-correct @else ans-incorrect @endif" role="progressbar"  aria-valuenow="{{$percentageOfAnswer[$question->id][1]}}" aria-valuemin="0" aria-valuemax="100" style="width:{{$percentageOfAnswer[$question->id][1]}}%">
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
                                                    <div class="answer-title mr-2 @if($question->answers->{'correct_answer_'.$AttemptExamData->language} === 2) correct-answer @else incorrect-answer @endif">B</div>
                                                    <div class="progress">
                                                        <div class="progress-bar @if($question->answers->{'correct_answer_'.$AttemptExamData->language} === 2) ans-correct @else ans-incorrect @endif" role="progressbar"  aria-valuenow="{{$percentageOfAnswer[$question->id][2]}}" aria-valuemin="0" aria-valuemax="100" style="width:{{$percentageOfAnswer[$question->id][2]}}%">
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
                                                    <div class="answer-title mr-2 @if($question->answers->{'correct_answer_'.$AttemptExamData->language} === 3) correct-answer @else incorrect-answer @endif">C</div>
                                                        <div class="progress">
                                                            <div class="progress-bar @if($question->answers->{'correct_answer_'.$AttemptExamData->language} === 3) ans-correct @else ans-incorrect @endif" role="progressbar"  aria-valuenow="{{$percentageOfAnswer[$question->id][3]}}" aria-valuemin="0" aria-valuemax="100" style="width:{{$percentageOfAnswer[$question->id][3]}}%">
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
                                                    <div class="answer-title mr-2 @if($question->answers->{'correct_answer_'.$AttemptExamData->language} === 4) correct-answer @else incorrect-answer @endif">D</div>
                                                    <div class="progress">
                                                        <div class="progress-bar @if($question->answers->{'correct_answer_'.$AttemptExamData->language} === 4) ans-correct @else ans-incorrect @endif" role="progressbar"  aria-valuemin="0" aria-valuemax="100" style="width:{{$percentageOfAnswer[$question->id][4]}}%">
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
                                        <div class="sm-answer pl-5 pt-2">
                                            <?php if(isset($AnswerNumber[key($AnswerNumber)]) && $AnswerNumber[key($AnswerNumber)]->answer == $question->answers->{'correct_answer_'.$AttemptExamData->language}){ ?>
                                                <span class="badge badge-success">{{__('languages.result.correct_answer')}}</span>
                                            <?php }else{ ?>
                                            <span class="badge badge-danger">{{__('languages.result.incorrect_answer')}}</span>
                                            <?php
                                                $nodeId=0;
                                                if(isset($AnswerNumber[key($AnswerNumber)]))
                                                {
                                                    $nodeId = $question->answers->{'answer'.$AnswerNumber[key($AnswerNumber)]->answer.'_node_relation_id_'.$AttemptExamData->language};
                                                    if(empty($nodeId)){
                                                        $nodeId = App\Helpers\Helper::getWeaknessNodeId($question->id, $AnswerNumber[key($AnswerNumber)]->answer);
                                                    }
                                                }
                                            ?>
                                            <h6 class="mt-3"><b>{{__('languages.report.weakness')}}:</b>
                                                @if($AttemptExamData->language=='ch')
                                                    @if($nodeId!=0 && isset($nodeWeaknessListCh[$nodeId]))
                                                        @php
                                                            $WeaknessList[]=$nodeWeaknessListCh[$nodeId];
                                                            $WeaknessListWithId[$nodeId]=$nodeWeaknessListCh[$nodeId];
                                                        @endphp
                                                        {{ $nodeWeaknessListCh[$nodeId]}}
                                                    @endif
                                                @else
                                                    @if($nodeId!=0 && isset($nodeWeaknessList[$nodeId]))
                                                        @php
                                                            $WeaknessList[]=$nodeWeaknessList[$nodeId];
                                                            $WeaknessListWithId[$nodeId]=$nodeWeaknessList[$nodeId];
                                                        @endphp                                    
                                                        {{ $nodeWeaknessList[$nodeId]}}
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
                    
                        @foreach ($AllWeakness as $WeaknessKey => $WeaknessNof)
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
                                        <h6 class="text-dark"><b><i class="fa fa-plus mr-2"></i>{{__('languages.report.key_improvement_points')}}</b></h6 >
                                    </button>
                                    </h5>
                                </div>
                                <div id="collapseImprovement" class="collapse" aria-labelledby="heading" data-parent="#accordionImprovement">
                                    <ul class="list-unstyled ml-5">
                                        @if($KeyImprovementData!="")
                                            {!! $KeyImprovementData !!}
                                        @else
                                            <li>{{__('languages.report.no_key_improvement_available')}}</li>
                                        @endif
                                    </ul>
                                </div>
                            </div>
                        </div> 
                        {{-- <div class="student-key-improvement">
                            <h6 class="mt-3 pl-3 key-improvement-title text-dark">{{__('languages.report.key_improvement_points')}}</h6>
                            <ul class="list-unstyled ml-5">
                                @if($KeyImprovementData!="")
                                    <a href="{{route('getStudentExamList')}}">{!! $KeyImprovementData !!}</a>
                                @else
                                    <li>{{__('languages.report.no_key_improvement_availble')}}</li>
                                @endif
                            </ul>
                        </div> --}}
                
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
                                {{-- <div class="row all-information-sec">
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
                                </div> --}}
                                {{-- <div class="row all-information-sec">
                                    <div class="col-lg-3 col-md-4 col-sm-12">
                                        <label>{{__('languages.result.test_title')}} : {{ $ExamData->title }}</label>
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
                                        <label>{{__('languages.result.number_of_questions')}} : {{  $total_question }}</label>
                                    </div>
                                    <div class="col-lg-3 col-md-4 col-sm-12">
                                        <label>{{__('languages.result.number_of_correct_answers')}} : {{ $total_correct_answer }}</label>
                                    </div>
                                    <div class="col-lg-3 col-md-4 col-sm-12">
                                        <label>{{__('languages.result.number_of_incorrect_answers')}} : {{ $total_incorrect_answer }}</label>
                                    </div>
                                    <div class="col-lg-3 col-md-4 col-sm-12">
                                        <label>{{__('languages.result.test_time_taken')}} : {{ $AttemptExamData->exam_taking_timing }}</label>
                                    </div>
                                    @php $accuracy = 0; @endphp
                                    <div class="col-lg-3 col-md-4 col-sm-12">
                                        <label>{{__('languages.report.accuracy')}} : 
                                        @if(!empty($total_question))
                                        @php
                                        $accuracy = round((($total_correct_answer / $total_question) * 100), 2); @endphp
                                        {{ $accuracy }}%
                                        @endif
                                        </label>
                                    </div>
                                    <div class="col-lg-3 col-md-4 col-sm-12">
                                        <label>{{__('languages.report.ability')}} :<?php echo App\Helpers\Helper::getAbility($accuracy); ?></label>
                                    </div>
                                </div> --}}
                                {{-- </hr> --}}
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
                                                <p class="sm-title bold">{{__('languages.result.q_no')}}: {{++$i}}</p>
                                                    <div class="sm-que pl-2">
                                                        <p><?php echo $question->{'question_en'}; ?></p>
                                                    </div>
                                                    @if(isset($question->answers->{'answer1_'.$AttemptExamData->language}))
                                                    <div class="sm-ans pl-2 pb-2">
                                                        <input type="radio" name="ans_que_{{$question->id}}" value="1" class="radio mr-2" <?php if(isset($AnswerNumber[key($AnswerNumber)]) && $AnswerNumber[key($AnswerNumber)]->answer == 1){ echo 'checked';} ?> disabled>
                                                        <div class="answer-title mr-2 <?php if($question->answers->{'correct_answer_'.$AttemptExamData->language} == 1){ echo 'correct-answer';}else{ echo 'incorrect-answer';} ?>">A</div>
                                                        <div class="progress">
                                                            <div class="progress-bar @if($question->answers->{'correct_answer_'.$AttemptExamData->language} === 1) ans-correct @else ans-incorrect @endif" role="progressbar"  aria-valuenow="{{$exams['percentageOfAnswer'][$question->id][1]}}" aria-valuemin="0" aria-valuemax="100" style="width:{{$exams['percentageOfAnswer'][$question->id][1]}}%">
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
                                                        <div class="answer-title mr-2 @if($question->answers->{'correct_answer_'.$AttemptExamData->language} === 2) correct-answer @else incorrect-answer @endif">B</div>
                                                        <div class="progress">
                                                            <div class="progress-bar @if($question->answers->{'correct_answer_'.$AttemptExamData->language} === 2) ans-correct @else ans-incorrect @endif" role="progressbar"  aria-valuenow="{{$exams['percentageOfAnswer'][$question->id][2]}}" aria-valuemin="0" aria-valuemax="100" style="width:{{$exams['percentageOfAnswer'][$question->id][2]}}%">
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
                                                        <div class="answer-title mr-2 @if($question->answers->{'correct_answer_'.$AttemptExamData->language} === 3) correct-answer @else incorrect-answer @endif">C</div>
                                                        <div class="progress">
                                                            <div class="progress-bar @if($question->answers->{'correct_answer_'.$AttemptExamData->language} === 3) ans-correct @else ans-incorrect @endif" role="progressbar"  aria-valuenow="{{$exams['percentageOfAnswer'][$question->id][3]}}" aria-valuemin="0" aria-valuemax="100" style="width:{{$exams['percentageOfAnswer'][$question->id][3]}}%">
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
                                                        <div class="answer-title mr-2 @if($question->answers->{'correct_answer_'.$AttemptExamData->language} === 4) correct-answer @else incorrect-answer @endif">D</div>
                                                        <div class="progress">
                                                            <div class="progress-bar @if($question->answers->{'correct_answer_'.$AttemptExamData->language} === 4) ans-correct @else ans-incorrect @endif" role="progressbar"  aria-valuenow="{{$exams['percentageOfAnswer'][$question->id][4]}}" aria-valuemin="0" aria-valuemax="100" style="width:{{$exams['percentageOfAnswer'][$question->id][4]}}%">
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
                                                <div class="sm-answer pl-5 pt-2">
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
                                                        @if($AttemptExamData->language=='ch')
                                                            @if($nodeId!=0 && isset($nodeWeaknessListCh[$nodeId]))
                                                                @php
                                                                    $WeaknessList[]=$nodeWeaknessListCh[$nodeId];
                                                                    $WeaknessListWithId[$nodeId]=$nodeWeaknessListCh[$nodeId];
                                                                @endphp                                    
                                                                {{ $nodeWeaknessListCh[$nodeId]}}
                                                            @endif
                                                        @else
                                                            @if($nodeId!=0 && isset($nodeWeaknessList[$nodeId]))
                                                                @php
                                                                    $WeaknessList[]=$nodeWeaknessList[$nodeId];
                                                                    $WeaknessListWithId[$nodeId]=$nodeWeaknessList[$nodeId];
                                                                @endphp                                    
                                                                {{ $nodeWeaknessList[$nodeId]}}
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
                            $KeyImprovementData='';
                            $KeyWeaknessData='';
                            $checkImprovement=1; 
                        @endphp
                        @foreach ($AllWeakness as $WeaknessKey => $WeaknessNof)
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
                                        <h6 class="text-dark"><b><i class="fa fa-plus mr-2"></i>{{__('languages.report.key_improvement_points')}}</b></h6 >
                                    </button>
                                    </h5>
                                </div>
                                <div id="collapseImprovement" class="collapse" aria-labelledby="heading" data-parent="#accordionImprovement">
                                    <ul class="list-unstyled ml-5">
                                        @if($KeyImprovementData!="")
                                            {!! $KeyImprovementData !!}
                                        @else
                                        <li>{{__('languages.report.no_key_improvement_available')}}</li>
                                        @endif
                                    </ul>
                                </div>
                            </div>
                        </div> 

                        {{-- <div class="student-key-improvement">
                            <h6 class="mt-3 pl-3 key-improvement-title text-dark">{{__('languages.report.key_improvement_points')}}</h6>
                            <ul class="list-unstyled ml-5">
                                @if($KeyImprovementData!="")
                                    {!!$KeyImprovementData!!}
                                @else
                                    <li>{{__('languages.report.no_key_improvement_availble')}}</li>
                                @endif
                            </ul>
                        </div> --}}

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
{{-- </div> --}}
