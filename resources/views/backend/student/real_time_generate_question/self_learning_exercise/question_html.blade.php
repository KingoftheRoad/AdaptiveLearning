<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="sec-title">
                <h2 class="mb-4 main-title">{{__('languages.my_studies.questions')}}</h2>
            </div>
            <hr class="blue-line">
        </div>
    </div>
    <form class="attempt-exams" method="post" id="attempt-exams">
        @csrf()
        <div class="row">
            <div class="col-md-12 col-lg-12 col-sm-12 attmp-main-timer">
                <div class="select-lng w-25">
                    <select name="language" class="form-control select-option" id="student-select-attempt-exam-language">
                        <option value="">{{ __('languages.my_studies.select_exam_language') }}</option>
                        <option value="en" @if(request()->get("language") === "en") selected @endif @if(!isset($_GET['language'])) selected @endif>{{ __('languages.english') }}</option>
                        <option value="ch" @if(request()->get("language") === "ch") selected @endif>{{ __('languages.chinese') }}</option>
                    </select>
                    @if($errors->has('language'))
                        <span class="validation_error">{{ $errors->first('language') }}</span>
                    @endif
                </div>
                <div class="exam_timer_section">
                    <div class="attmp-timer-inr">
                        <h5>{{__('languages.my_studies.exam_time')}}: </h5>
                        <p><span id="ExamTimer"></span></p>
                    </div>
                </div>
            </div>
        </div>
        <input type="hidden" name="examConfigurationsData" value="{{$examConfigurationsData}}">
        <div id="nextquestionarea"  class="">
        <input type="hidden" name="encodedMainSkillArray" value="{{$encodedMainSkillArray}}">
        <input type="hidden" name="assigned_questions_list" value="{{$assigned_questions_list}}">
        <input type="hidden" name="currentQuestion" value="{{$Question->id}}">
        <input type="hidden" name="AttemptedQuestionAnswers" value="{{$encodedAttemptedQuestionAnswers ?? ''}}">
        <input type="hidden" name="self_learning_test_type" value="1">
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
                        <button type="button" class="btn btn-success mr-2" id="submit-self-learning-exercise" disabled>{{__('languages.submit')}}</button>
                    </div>
                    @endif
                    
                    <!-- Start General Hints button html -->
                    <div class="">
                        @if(isset($UploadDocumentsData) && !empty($UploadDocumentsData))
                            <button type="button" class="btn btn-danger ml-5 video-img-sec" id="want_a_hint" data-src="{{ $UploadDocumentsData['file_path'] }}" data-toggle="modal" data-target="#WantAHintModal" >{{__('languages.my_studies.want_a_hint')}}</button>
                        @else
                            <button type="button" class="btn btn-danger ml-5" id="want_a_hint" data-toggle="modal" data-target="#WantAHintModal" >{{__('languages.my_studies.want_a_hint')}}</button>
                        @endif
                    </div>
                    <!-- End General Hints button html -->
                </div>
            </div>

            <!-- Start Want Hint Popup Model -->
            @include('backend.student.real_time_generate_question.self_learning_exercise.general_hints_popup')
            <!-- End Want Hint Popup Model -->
        @endif
        </div>
    </form>
    </div>
</div>