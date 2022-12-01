
<input type="hidden" name="encodedMainSkillArray" value="{{$encodedMainSkillArray}}">
<input type="hidden" name="assigned_questions_list" value="{{$assigned_questions_list}}">
<input type="hidden" name="currentQuestion" value="{{$Question->id}}">
<input type="hidden" name="AttemptedQuestionAnswers" value="{{$encodedAttemptedQuestionAnswers ?? ''}}">
<input type="hidden" name="self_learning_test_type" value="2">
<input type="hidden" name="test_type" value="1">
<input type="hidden" name="result_list" value="{{$result_list}}">
<input type="hidden" name="QuestionNo" value="{{$QuestionNo}}">
<input type="hidden" name="exam_taking_timing" value="" id="exam_taking_timing">
<input type="hidden" name="current_question_taking_timing" value="" id="current_question_taking_timing">
@if(!empty($Question))
<div class="row">
    <div class="col-md-12 col-lg-12 col-sm-12 attmp-exam-main">
        <div class="attmp-main-que">
            <h4>Q-{{$QuestionNo}}</h4>
            <div class="attmp-que">
                @php echo $Question->{'question_'.$examLanguage}; @endphp
            </div>
        </div>
        <div class="attmp-main-answer">
            @php
                $random_number_array = range(1,4);
                shuffle($random_number_array );
                $random_number_array = array_slice($random_number_array ,0,4);
            @endphp
            @if(isset($Question->answers->{'answer'.$random_number_array[0].'_'.$examLanguage}))
                <div class="attmp-ans pl-2 pb-2">
                    <input type="radio" name="answer" value="{{$random_number_array[0]}}" class="radio mr-2 checkanswer">
                    <div class="answer-title mr-2">A</div>
                    <div class="progress">
                        <div  role="progressbar">
                            <div class="anser-detail pl-2">
                                <p>@php echo $Question->answers->{'answer'.$random_number_array[0].'_'.$examLanguage}; @endphp</p>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
            @if(isset($Question->answers->{'answer'.$random_number_array[1].'_'.$examLanguage}))
                <div class="attmp-ans pl-2 pb-2">
                    <input type="radio" name="answer" value="{{$random_number_array[1]}}" class="radio mr-2 checkanswer">
                    <div class="answer-title mr-2">B</div>
                    <div class="progress">
                        <div  role="progressbar">
                            <div class="anser-detail pl-2">
                                <p>@php echo $Question->answers->{'answer'.$random_number_array[1].'_'.$examLanguage}; @endphp</p>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
            @if(isset($Question->answers->{'answer'.$random_number_array[2].'_'.$examLanguage}))
                <div class="attmp-ans pl-2 pb-2">
                    <input type="radio" name="answer" value="{{$random_number_array[2]}}" class="radio mr-2 checkanswer">
                    <div class="answer-title mr-2">C</div>
                    <div class="progress">
                        <div  role="progressbar">
                            <div class="anser-detail pl-2">
                                <p>@php echo $Question->answers->{'answer'.$random_number_array[2].'_'.$examLanguage}; @endphp</p>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
            @if(isset($Question->answers->{'answer'.$random_number_array[3].'_'.$examLanguage}))
                <div class="attmp-ans pl-2 pb-2">
                    <input type="radio" name="answer" value="{{$random_number_array[3]}}" class="radio mr-2 checkanswer">
                    <div class="answer-title mr-2">D</div>
                    <div class="progress">
                        <div  role="progressbar">
                            <div class="anser-detail pl-2">
                                <p>@php echo $Question->answers->{'answer'.$random_number_array[3].'_'.$examLanguage}; @endphp</p>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>                        
    </div>
</div>

<div class="row">
    <div class="col-md-12 col-lg-12 col-sm-12 attmp-all-button">
        @if($countMainSkillArray)
        <div class="attmp-next-btn attmp-butns">
            <button type="button" class="btn btn-warning mr-2" id="nextquestion" question-id-next="{{$Question['id']}}" data-text="Next" disabled>{{__('languages.my_studies.next')}}</button>
        </div>
        @else
        <div class="attmp-submit-btn attmp-butns">
            <button type="button" class="btn btn-success mr-2" id="submit-self-learning-test" disabled>{{__('languages.submit')}}</button>
        </div>
        @endif
    </div>
</div> 
@endif
        