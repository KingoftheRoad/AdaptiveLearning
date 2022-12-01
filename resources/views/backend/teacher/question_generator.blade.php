@extends('backend.layouts.app')
@section('content')
<div class="wrapper d-flex align-items-stretch sm-deskbord-main-sec">
    @include('backend.layouts.sidebar')
    <div id="content" class="pl-2 pb-5">
        @include('backend.layouts.header')
        @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
        @endif
        <div class="sm-right-detail-sec pl-5 pr-5">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-12">
                        <div class="sec-title">
                            <h2 class="mb-4 main-title">{{__('Question Generators')}}</h2>
                        </div>
                        <hr class="blue-line">
                    </div>
                </div>
                 @if(session()->has('success_msg'))
                        <div class="alert alert-success">
                    {{ session()->get('success_msg') }}
                </div>
                @endif
                @if(session()->has('error_msg'))
                <div class="alert alert-danger">
                    {{ session()->get('error_msg') }}
                </div>
                @endif
                <form name="question-generator" id="question-generator" action="{{ route('generate-questions') }}" method="POST">
                    @csrf
                    <div class="sm-add-user-sec card">
                        <div class="select-option-sec pb-5 card-body">
                            <div id="wizard">
                                <div class="question-generator-option-headings mb-3">
                                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 pl-0 pr-0">
                                        <ul class="form-tab">
                                            <li class="step-headings section-step1 tab_active">1. {{__('Configuration')}}</li>
                                            <li class="step-headings section-step2">2.{{__('Assign to Classes/Peers/Individual Students')}}</li>
                                            <li class="step-headings section-step3">3. {{__('Select Learning Objectives')}}</li>
                                            <li class="step-headings section-step4">4. {{__('Review of Questions')}}</li>
                                        </ul>
                                    </div>
                                </div>
                                <section class="form-steps step1">
                                    <div class="form-row">
                                        <div class="form-group col-md-6 mb-50">
                                            <label class="text-bold-600" for="test_type">Test Mode</label>
                                            <select name="test_type" class="form-control select-option" id="test_type">
                                                <option value="2" @if(request()->get('type') == 2) selected @endif>Excercise</option> 
                                                <option value="3" @if(request()->get('type') == 3) selected @endif>Test</option>
                                            </select>
                                        </div>

                                        <div class="form-group col-md-6 mb-50">
                                            <label class="text-bold-600" for="test_type">Title</label>
                                            <input type="text" name="title" value="" class="form-control" placeholder="Enter title" required>
                                        </div>

                                        <div class="form-group col-md-6 mb-50">
                                            <label>{{ __('Start Date') }}</label>
                                            <div class="input-group date">
                                                <input type="text" class="form-control date-picker" name="start_date" value="{{date('d/m/Y')}}" placeholder="{{__('Start Date')}}" autocomplete="off">
                                                <div class="input-group-addon input-group-append">
                                                    <div class="input-group-text">
                                                        <i class="glyphicon glyphicon-calendar fa fa-calendar"></i>
                                                    </div>
                                                </div>
                                            </div>
                                            <span id="start-date-error"></span>
                                            @if($errors->has('from_date'))<span class="validation_error">{{ $errors->first('from_date') }}</span>@endif
                                        </div>

                                        <div class="form-group col-md-6 mb-50">
                                            <label>{{ __('End Date') }}</label>
                                            <div class="input-group date">
                                                <input type="text" class="form-control date-picker" name="end_date" value="{{date('d/m/Y')}}" placeholder="{{__('End Date')}}" autocomplete="off">
                                                <div class="input-group-addon input-group-append">
                                                    <div class="input-group-text">
                                                        <i class="glyphicon glyphicon-calendar fa fa-calendar"></i>
                                                    </div>
                                                </div>
                                            </div>
                                            <span id="end-date-error"></span>
                                            @if($errors->has('from_date'))<span class="validation_error">{{ $errors->first('from_date') }}</span>@endif
                                        </div>

                                        <div class="form-group col-md-6 mb-50">
                                            <label for="id_end_time">{{ __('Start Time') }}</label>
                                            <div class="input-group date">
                                                <select name="start_time" class="form-control select-option" id="test_start_time">
                                                    <option value="">Select Test Start Time</option>
                                                    @if(isset($timeSlots) && !empty($timeSlots))
                                                        @foreach($timeSlots as $timeSlotKey => $time)
                                                            <option value="{{$timeSlotKey}}">{{$time}}</option>
                                                        @endforeach
                                                    @endif
                                                </select>
                                            </div>
                                            <span id="end-date-error"></span>
                                            @if($errors->has('start_time'))<span class="validation_error">{{ $errors->first('start_time') }}</span>@endif
                                        </div>

                                        <div class="form-group col-md-6 mb-50">
                                            <label for="id_end_time">{{ __('End Time') }}</label>
                                            <div class="input-group date">
                                                <select name="end_time" class="form-control select-option" id="test_end_time">
                                                    <option value="">Select Test End Time</option>
                                                    @if(isset($timeSlots) && !empty($timeSlots))
                                                        @foreach($timeSlots as $timeSlotKey => $time)
                                                            <option value="{{$timeSlotKey}}">{{$time}}</option>
                                                        @endforeach
                                                    @endif
                                                </select>
                                            </div>
                                            <span id="end-date-error"></span>
                                            @if($errors->has('end_time'))<span class="validation_error">{{ $errors->first('end_time') }}</span>@endif
                                        </div>

                                        <!-- <div class="time-section">
                                            <div class="start-time-sec mt-3">
                                                <div class="start-time-heading">
                                                    <label>Start Time</label>
                                                </div>
                                                <div class="start-time-text-content">
                                                    <input type="text" value="" name="start_time" class="datepicker" id="start_time">
                                                </div>
                                            </div>
                                            <div class="end-time-sec mt-3">
                                                <div class="end-time-heading">
                                                    <label>End Time</label>
                                                </div>
                                                <div class="end-time-text-content">
                                                    <input type="text" value="" name="end_time" class="datepicker" id="end_time">
                                                </div>
                                            </div>
                                        </div> -->

                                        <div class="form-group col-md-6 mb-50">
                                            <label class="text-bold-600">Report Date</label>
                                            <select name="report_date" class="form-control select-option" id="select-report-date">
                                                <option value="end_date">End Date</option>
                                                <option value="after_submit" selected>After Submit</option>
                                                <option value="custom_date">Custom Date</option>
                                            </select>
                                        </div>
                                        <div class="form-group col-md-6 mb-50 custom_date" style="display: none;">
                                            <label>Report Custom Date</label>
                                            <div class="input-group date">
                                                <input type="text" class="form-control date-picker" name="custom_date" value="{{date('d/m/Y')}}" placeholder="{{__('End Date')}}" autocomplete="off">
                                                <div class="input-group-addon input-group-append">
                                                    <div class="input-group-text">
                                                        <i class="glyphicon glyphicon-calendar fa fa-calendar"></i>
                                                    </div>
                                                </div>
                                            </div>
                                            <span id="end-date-error"></span>
                                            @if($errors->has('custom_date'))<span class="validation_error">{{ $errors->first('custom_date') }}</span>@endif
                                        </div>
                                        <div class="form-group col-md-6 mb-50" id="no_of_trials_per_question_section" style="display:none;">
                                            <label class="text-bold-600">No Of Trials Per Question</label>
                                            <select name="no_of_trials_per_question" class="form-control select-option" id="select-no-of-per-trials-question">
                                                <option value="1" selected>1</option> 
                                                <option value="2">2</option>
                                                <option value="3">3</option>
                                                <option value="4">4</option>
                                            </select>
                                        </div>

                                        <div class="form-group col-md-6 mb-50">
                                            <label>{{__('languages.difficulty_mode')}}</label>
                                            <select name="difficulty_mode" class="form-control select-option" id="difficulty_mode">
                                                <option value="manual">{{__('languages.manual')}}</option>
                                                <option value="auto">Auto Fit</option>
                                            </select>
                                        </div>

                                        <div class="form-group col-md-6 mb-50">
                                            <label>{{__('languages.questions.difficulty_level')}}</label>
                                            <select name="difficulty_lvl[]" class="form-control select-option" id="difficulty_lvl" multiple>
                                                @if(!empty($difficultyLevels))
                                                @foreach($difficultyLevels as $difficultyLevel)
                                                <option value="{{$difficultyLevel->difficulty_level}}" @if($difficultyLevel->difficulty_level == 2) selected @endif>{{$difficultyLevel->difficulty_level_name}}</option>
                                                @endforeach
                                                @endif								
                                            </select>
                                            <span name="err_difficulty_level"></span>
                                        </div>

                                        <div class="form-group col-md-6 mb-50" id="display_hints_section" style="display:none;">
                                            <label>Display Hints</label>
                                            <select name="display_hints" class="form-control select-option" id="select-display-hints">
                                                <option value="no">No</option>
                                                <option value="yes" selected>Yes</option>
                                            </select>
                                        </div>

                                        <div class="form-group col-md-6 mb-50">
                                            <label>Display Full Solution in Report</label>
                                            <select name="display_full_solution" class="form-control select-option" id="select-display-full-solutions">
                                                <option value="yes" selected>Yes</option>
                                                <option value="no">No</option>
                                            </select>
                                        </div>

                                        <div class="form-group col-md-6 mb-50" style="display:none">
                                            <label>Display pr answer Hints</label>
                                            <select name="display_pr_answer_hints" class="form-control select-option" id="select-display-pr-answer-hints">
                                                <option value="yes" selected>Yes</option>
                                                <option value="no">No</option>
                                            </select>
                                        </div>

                                        <div class="form-group col-md-6 mb-50">
                                            <label>Randomize Answer (Means different for every student)</label>
                                            <select name="randomize_answer" class="form-control select-option" id="select-randomize-answers">
                                                <option value="yes">Yes</option>
                                                <option value="no">No</option>
                                            </select>
                                        </div>

                                        <div class="form-group col-md-6 mb-50">
                                            <label>Randomize Order (Means different for every student)</label>
                                            <select name="randomize_order" class="form-control select-option" id="select-randomize-order">
                                                <option value="yes">Yes</option>
                                                <option value="no">No</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-row select-data">
                                        <div class="sm-btn-sec form-row">
                                            <div class="form-group mb-50 btn-sec">
                                                <!-- <button type="button" class="blue-btn btn btn-primary disabled previous-button previous_btn_step_1" data-stepid="1" disabled>Previous</button> -->
                                                <button type="button" class="blue-btn btn btn-primary next-button next_btn_step_1" data-stepid="1">Next</button>                                                
                                            </div>
                                        </div>
                                    </div>
                                </section>

                                <section class="form-steps step2" style="display:none;">
                                    <div class="form-row">
                                        <div class="form-grade-heading">
                                            <label>Target Students</label>
                                        </div>
                                    </div>
                                    <div class="form-row">
                                        <div class="form-grade-section">
                                            <div class="student-grade-class-section row">
                                                <div class="form-grade-heading col-lg-3">
                                                    <label>Grade/Classes</label>
                                                </div>
                                                <div class="form-grade-select-section col-lg-9">
                                                    @if(!empty($GradeClassData))
                                                    @foreach($GradeClassData as $grade)
                                                    <div class="form-grade-select">
                                                        <div class="form-grade-option">
                                                            <div class="form-grade-single-option">
                                                                <input type="checkbox" name="grades[]" value="{{$grade->id}}" class="question-generator-grade-chkbox">{{$grade->name}}
                                                            </div>
                                                        </div>
                                                        @if(!empty($grade->classes))
                                                        <div class="form-grade-sub-option">
                                                            <div class="form-grade-sub-single-option">
                                                                @foreach($grade->classes as $classes)
                                                                <input type="checkbox" name="classes[]" value="{{$classes->id}}" class="question-generator-class-chkbox">
                                                                <label>{{$grade->name}}{{$classes->name}}</label>
                                                                @endforeach
                                                            </div>
                                                        </div>
                                                        @endif
                                                    </div>
                                                    @endforeach
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="form-group student_list_section mt-3 row">
                                                <div class="student_list_heading col-lg-3">
                                                    <label>Select Individual Students</label>
                                                </div>
                                                <div class="student_list_option col-lg-9">
                                                    @if(isset($StudentList) && !empty($StudentList))
                                                    <select name="studentIds[]" class="form-control select-option" id="question-generator-student-id" multiple>
                                                    @foreach($StudentList as $student)
                                                        <option value="{{$student->id}}">
                                                            @if(app()->getLocale() == 'en') {{$student->DecryptNameEn}}  @else {{$student->DecryptNameCh}}  @endif
                                                            @if($student->class_student_number) ({{$student->class_student_number}}) @endif
                                                        </option>
                                                    @endforeach
                                                    </select>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="form-group student_peer_group_section mt-3 row">
                                                <div class="student_peer_group_heading col-lg-3">
                                                    <label>Student Peer Groups</label>
                                                </div>
                                                <div class="student_peer_group_option col-lg-9">
                                                    <select class="selectpicker form-control" data-show-subtext="true" data-live-search="true" name="peerGroupIds[]" id="peer-group-options">
                                                        <option value="">{{__('Select Peer Groups')}}</option>
                                                        @if($PeerGroupList)
                                                            @foreach($PeerGroupList as $peerGroup)
                                                                <option value="{{$peerGroup->id}}">{{$peerGroup->PeerGroupName}}</option>
                                                            @endforeach
                                                        @endif
                                                    </select>
                                                </div>
                                            </div>
                                            <!-- <div class="no-of-trials-section mt-3">
                                                <div class="no-of-trials-heading">
                                                    <label>No. of Trials</label>
                                                </div>
                                                <div class="no-of-trials-text-content">
                                                    <div class="no-of-trials-option">
                                                        <input type="radio" value="" name="no. of trials">
                                                        <label>xyz</label>
                                                        <input type="text" value="" name="no. of trials">
                                                        <input type="radio" value="" name="no. of trials">
                                                        <label>abc</label>
                                                    </div>
                                                </div>
                                            </div>                   -->
                                        </div>
                                    </div>
                                    <div class="form-row select-data">
                                        <div class="sm-btn-sec form-row">
                                            <div class="form-group mb-50 btn-sec">
                                                <button type="button" class="blue-btn btn btn-primary previous-button previous_btn_step_2" data-stepid="2">Previous</button>
                                                <button type="button" class="blue-btn btn btn-primary next-button next_btn_step_2" data-stepid="2">Next</button>
                                            </div>
                                        </div>
                                    </div>
                                </section>

                                <section class="form-steps step3" style="display:none;">
                                    <input type="hidden" name="questionIds" value="" id="questionIds">
                                    <div class="form-row">
                                        <div class="form-group col-md-6 mb-50">
                                            <label>{{__('languages.upload_document.strands')}}</label>
                                            <select name="strand_id[]" class="form-control select-option" id="strand_id" multiple>
                                                @if(isset($strandsList) && !empty($strandsList))
                                                    @foreach ($strandsList as $strandKey => $strand)
                                                        <option value="{{ $strand->id }}" <?php if($strandKey == 0){echo 'selected';}?>>{{ $strand->{'name_'.app()->getLocale()} }}</option>
                                                    @endforeach
                                                @else
                                                    <option value="">{{__('languages.no_strands_available')}}</option>
                                                @endif
                                            </select>
                                        </div>
                                        <div class="form-group col-md-6 mb-50">
                                            <label>{{__('languages.upload_document.learning_units')}}</label>
                                            <select name="learning_unit_id[]" class="form-control select-option" id="learning_unit" multiple>
                                                @if(isset($LearningUnits) && !empty($LearningUnits))
                                                    @foreach ($LearningUnits as $learningUnitKey => $learningUnit)
                                                        <option value="{{ $learningUnit->id }}" selected>{{ $learningUnit->{'name_'.app()->getLocale()} }}</option>
                                                    @endforeach
                                                @else
                                                    <option value="">{{__('languages.no_learning_units_available')}}</option>
                                                @endif
                                            </select>
                                        </div>
                                        <div class="form-group col-md-6 mb-50">
                                            <label>{{__('languages.upload_document.learning_objectives')}}</label>
                                            <select name="learning_objectives_id[]" class="form-control select-option" id="learning_objectives" multiple>
                                                @if(isset($LearningObjectives) && !empty($LearningObjectives))
                                                    @foreach ($LearningObjectives as $learningObjectivesKey => $learningObjectives)
                                                        <option value="{{ $learningObjectives->id }}" selected>{{ $learningObjectives->foci_number }} {{ $learningObjectives->{'title_'.app()->getLocale()} }}</option>
                                                    @endforeach
                                                @else
                                                    <option value="">{{__('languages.no_learning_objectives_available')}}</option>
                                                @endif
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-row select-data">
                                        <div class="sm-btn-sec form-row">
                                            <div class="form-group mb-50 btn-sec">
                                                <button type="button" class="blue-btn btn btn-primary previous-button previous_btn_step_3" data-stepid="3">Previous</button>
                                                <button type="button" class="blue-btn btn btn-primary next-button next_btn_step_3" data-stepid="3">Next</button>                                                
                                            </div>
                                        </div>
                                    </div>
                                </section>

                                <section class="form-steps step4" style="display:none;">
                                    <div class="d-flex tab-content-wrap">
                                        <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist" aria-orientation="vertical">
                                            <li class="nav-item">
                                                <a class="nav-link active" id="pills-home-tab" data-toggle="pill" href="#pills-home" role="tab" aria-controls="pills-home" aria-selected="true">Q3</a>
                                            </li>
                                            <li class="nav-item">
                                                <a class="nav-link" id="pills-profile-tab" data-toggle="pill" href="#pills-profile" role="tab" aria-controls="pills-profile" aria-selected="false">Q2</a>
                                            </li>
                                            <li class="nav-item">
                                                <a class="nav-link" id="pills-contact-tab" data-toggle="pill" href="#pills-contact" role="tab" aria-controls="pills-contact" aria-selected="false">Q1</a>
                                            </li>
                                        </ul>
                                        <div class="tab-content" id="pills-tabContent">
                                            <div class="tab-pane fade show active" id="pills-home" role="tabpanel" aria-labelledby="pills-home-tab">Q3 Content</div>
                                            <div class="tab-pane fade" id="pills-profile" role="tabpanel" aria-labelledby="pills-profile-tab">Q2 Content</div>
                                            <div class="tab-pane fade" id="pills-contact" role="tabpanel" aria-labelledby="pills-contact-tab">
                                                <div class="d-flex pb-3">
                                                <p class="question-title">
                                                    Q1
                                                </p>
                                                <div class="question-content pl-2">
                                                    <div class="d-flex pb-3">
                                                        <select class="form-control" name="cars" id="cars">
                                                            <option value="volvo">Volvo</option>
                                                            <option value="saab">Saab</option>
                                                            <option value="mercedes">Mercedes</option>
                                                            <option value="audi">Audi</option>
                                                        </select>
                                                        <select class="form-control ml-2" name="cars" id="cars">
                                                            <option value="volvo">Volvo</option>
                                                            <option value="saab">Saab</option>
                                                            <option value="mercedes">Mercedes</option>
                                                            <option value="audi">Audi</option>
                                                        </select>
                                                        <input type="text" id="fname" class="form-control ml-2" name="fname">
                                                    </div>
                                                    <div class="answer-content">
                                                        <div>
                                                            <input type="radio" id="age1" name="age" value="30">
                                                            <label for="age1">A</label><br>
                                                            <input type="radio" id="age2" name="age" value="60">
                                                            <label for="age2">B</label><br>  
                                                            <input type="radio" id="age3" name="age" value="100">
                                                            <label for="age3">C</label><br>
                                                        </div>
                                                        <div class="d-flex align-items-center">
                                                            <span class="mr-1">Text</span>
                                                            <button type="button" class="btn-search bg-pink">Large button</button>
                                                            <button type="button" class="btn-search bg-pink ml-1">Large button</button>
                                                        </div>
                                                    </div>
                                                </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="btn_group mb-3">
                                        <div class="d-flex mb-2 justify-content-center">
                                            <button type="button" class="btn-search bg-pink btn-up"><i class="fa fa-arrow-up mr-1" aria-hidden="true"></i>up</button>
                                            <button type="button" class="btn-search ml-1 bg-pink  btn-down"><i class="fa fa-arrow-down mr-1" aria-hidden="true"></i>down</button>
                                        </div>
                                        <div class="d-flex justify-content-center">
                                            <button type="button" class="btn-search bg-pink set-top"><i class="fa fa-arrow-up" aria-hidden="true"></i><i class="fa fa-arrow-up mr-1" aria-hidden="true"></i>Set Top</button>
                                            <button type="button" class="btn-search ml-1 set-bottom bg-pink"><i class="fa fa-arrow-down" aria-hidden="true"></i><i class="fa fa-arrow-down mr-1" aria-hidden="true"></i>Set Bottom</button>
                                        </div>
                                        <div class="d-flex justify-content-center">
                                            <button type="button" class="btn-search mt-2 btn-remove-tab set-bottom bg-pink w-25"><i class="fa fa-trash  mr-1" aria-hidden="true"></i>Remove</button>
                                        </div>
                                    </div>
                                    <div class="form-row select-data">
                                        <div class="sm-btn-sec form-row">
                                            <div class="form-group mb-50 btn-sec">
                                                <button type="button" class="blue-btn btn btn-primary previous-button previous_btn_step_4" data-stepid="4">Previous</button>
                                                <button type="submit" class="blue-btn btn btn-primary" data-stepid="4">Submit</button>                                                
                                            </div>
                                        </div>
                                    </div>
                                </section>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<script>
$(function (){
    
    $('#start_time').timepicker({
        'showDuration': true,
        'timeFormat': 'H:i',
        'step': 60
    });

    $('#end_time').timepicker({
        'showDuration': true,
        'timeFormat': 'H:i',
        'step': 60
    });

    // event fire on click next button
    $(document).on('click', '.next-button', function() {
        var currentStep = $(this).attr('data-stepid');
        if(checkValidation(currentStep))
        {
            var nextStep = (parseInt(currentStep) + 1);
            $('.form-steps').hide();
            $('.step-headings').removeClass('tab_active');
            $('.section-step'+nextStep).addClass('tab_active');
            $('.step'+nextStep).show();
            if(currentStep==3)
            {
                getLearningObjectivesOptionList();
            }
        }
        
    });

    // Event fire on click previous button
    $(document).on('click', '.previous-button', function() {
        var currentStep = $(this).attr('data-stepid');
        var previousStep = (parseInt(currentStep) - 1);
        $('.form-steps').hide();
        $('.step-headings').removeClass('tab_active');
        $('.section-step'+previousStep).addClass('tab_active');
        $('.step'+previousStep).show();
    });

    /**
     * USE : Hide and show some option based on change test type
     */
    $(document).on('change', '#test_type', function() {  
        if(this.value == 2){
            $('#no_of_trials_per_question_section').show();
            $('#display_hints_section').show();
            $("#randomize_answer select").val("yes").change();
            $("#randomize_order select").val("yes").change();
            $('#select-randomize-answers').val('no').select2().trigger('change');
            $('#select-randomize-order').val('no').select2().trigger('change');
        }else{
            $('#no_of_trials_per_question_section').hide();
            $('#display_hints_section').hide();
            $("#randomize_answer select").val("no").change();
            $("#randomize_order select").val("no").change();
            $('#select-randomize-answers').val('yes').select2().trigger('change');
            $('#select-randomize-order').val('yes').select2().trigger('change');
        }
    });

    // If type is exercise then no trials option is enabled and otherwise disabled
    if($('#test_type').find(":selected").val() == 2){
        $('#no_of_trials_per_question_section').show();
        $('#display_hints_section').show();
        $("#randomize_answer select").val("yes").change();
        $("#randomize_order select").val("yes").change();
        $('#select-randomize-answers').val('no').select2().trigger('change');
        $('#select-randomize-order').val('no').select2().trigger('change');
    }else{
        $('#no_of_trials_per_question_section').hide();
        $('#display_hints_section').hide();
        $("#randomize_answer select").val("no").change();
        $("#randomize_order select").val("no").change();
        $('#select-randomize-answers').val('yes').select2().trigger('change');
        $('#select-randomize-order').val('yes').select2().trigger('change');
    }

    /**
	 * USE : Get Learning Units from multiple strands
	 * **/
	$(document).on('change', '#strand_id', function() {
		$strandIds = $('#strand_id').val();
		if($strandIds != ""){
			$.ajax({
				url: BASE_URL + '/getLearningUnitFromMultipleStrands',
				type: 'POST',
				data: {
					'_token': $('meta[name="csrf-token"]').attr('content'),
					'grade_id': $('#grade-id').val(),
					'subject_id': $('#subject-id').val(),
					'strands_ids': $strandIds
				},
				success: function(response) {
					$('#learning_unit').html('');
					$("#cover-spin").hide();
					var data = JSON.parse(JSON.stringify(response));
					if(data){
						if(data.data){
							$(data.data).each(function() {
								var option = $('<option />');
								option.attr('value', this.id).text(this["name_"+APP_LANGUAGE]);
								option.attr('selected', 'selected');
								$('#learning_unit').append(option);
							});
						}else{
							$('#learning_unit').html('<option value="">'+LEARNING_UNITS_NOT_AVAILABLE+'</option>');
						}
					}else{
						$('#learning_unit').html('<option value="">'+LEARNING_UNITS_NOT_AVAILABLE+'</option>');
					}
					$('#learning_unit').multiselect("rebuild");
					$('#learning_unit').trigger("change");
				},
				error: function(response) {
					ErrorHandlingMessage(response);
				}
			});
		}else{
			$('#learning_unit, #learning_objectives').html('');
			$('#learning_unit, #learning_objectives').multiselect("rebuild");
		}        
	});

	/**
	 * USE : Get Multiple Learning units based on multiple learning units ids
	 * **/
	$(document).on('change', '#learning_unit', function() {
		$strandIds = $('#strand_id').val();
		$learningUnitIds = $('#learning_unit').val();
		if($learningUnitIds != ""){
			$.ajax({
				url: BASE_URL + '/getLearningObjectivesFromMultipleLearningUnits',
				type: 'POST',
				data: {
					'_token': $('meta[name="csrf-token"]').attr('content'),
					'grade_id': $('#grade-id').val(),
					'subject_id': $('#subject-id').val(),
					'strand_id': $strandIds,
					'learning_unit_id': $learningUnitIds
				},
				success: function(response) {
					$('#learning_objectives').html('');
					$("#cover-spin").hide();
					var data = JSON.parse(JSON.stringify(response));
					if(data){
						if(data.data){
							$(data.data).each(function() {
								var option = $('<option />');
								option.attr('value', this.id).attr('selected','selected').text(this.foci_number + ' ' + this.title);
								$('#learning_objectives').append(option);
							});
						}else{
							$('#learning_objectives').html('<option value="">'+LEARNING_OBJECTIVES_NOT_AVAILABLE+'</option>');
						}
					}else{
						$('#learning_objectives').html('<option value="">'+LEARNING_OBJECTIVES_NOT_AVAILABLE+'</option>');
					}
					$('#learning_objectives').multiselect("rebuild");
                    $('#learning_objectives').trigger("change");
				},
				error: function(response) {
					ErrorHandlingMessage(response);
				}
			});
		}else{
			$('#learning_objectives').html('');
			$('#learning_objectives').multiselect("rebuild");
		}
	});

    /**
     * USE : Hide and show custom report date on change select report date
     */
    $(document).on('change', '#select-report-date', function() { 
        $(".custom_date").hide();
        if($(this).val()=='custom_date')
        {
            $(".custom_date").show();
        }
    });
    $(document).on('change', '#learning_objectives', function() {
        getLearningObjectivesOptionList();
    });

   /**
    * USE : On click event click on the grade checkbox
    */

    $(document).on('click', '.question-generator-grade-chkbox', function(){
        var ClassIds = [];
        $('.question-generator-class-chkbox').each(function(){
            if($(this).is(":checked")) {
                ClassIds.push($(this).val());
            }
        });

        var GradeIds = [];
        $('.question-generator-grade-chkbox').each(function(){
            if($(this).is(":checked")) {
                GradeIds.push($(this).val());
            }
        });
        
        // Function call to get student list
        getStudents(GradeIds,ClassIds);
    });

    /**
    * USE : On click event click on the class checkbox
    */
    $(document).on('click', '.question-generator-class-chkbox', function(){
        var ClassIds = [];
        $('.question-generator-class-chkbox').each(function(){
            if($(this).is(":checked")) {
                ClassIds.push($(this).val());
            }
        });
        var GradeIds = [];
        $('.question-generator-grade-chkbox').each(function(){
            if($(this).is(":checked")) {
                GradeIds.push($(this).val());
            }
        });
        // Function call to get student list
        getStudents(GradeIds,ClassIds);
    });

    /**
     * USE : On change start time event disabled end time before options
     */
    $(document).on('change', '#test_start_time', function(){
        var selectedStartTimeIndex = this.selectedIndex;
        $.each($("#test_end_time option"), function(){
            var endOptionSelectedStartTimeIndex = $(this).index();
            if(endOptionSelectedStartTimeIndex <= selectedStartTimeIndex){
                $(this).attr("disabled", "disabled");
            }else{
                $(this).removeAttr("disabled");
            }
        });
        $('#test_end_time').val('').select2().trigger('change');
    });
});

/**
 * USE : Get the questions from AIAPi
 */
function getLearningObjectivesOptionList(){

    $('.form-steps.step4 #pills-tab').html('');
    $('.form-steps.step4 #pills-tabContent').html('');
    $('.form-steps.step4 .tab-content-wrap .error').remove();
    $("#cover-spin").show();

    $.ajax({
        url: BASE_URL + '/question-generator/getQuestionIdsFromLearningObjectives',
        type: 'POST',
        data: {
            '_token': $('meta[name="csrf-token"]').attr('content'),
            'strands_ids': $('#strand_id').val(),
            'learning_unit_id': $('#learning_unit').val(),
            'learning_objectives_id': $('#learning_objectives').val(),
            'question_type' : $('#test_type').val(),
            'dificulty_level' : $('#difficulty_lvl').val(),
            'difficulty_mode' : $('#difficulty_mode').val()
        },
        success: function(response) {
            var response = JSON.parse(JSON.stringify(response));
            if(response.data){
                var qLength=Object.keys(response.data.questionIds).length;
                // Set input hidden into question ids
                $('#questionIds').val(response.data.questionIds);

                var tab_left='';
                var tab_right='';
                var qIndex=1;
                $.each(response.data.question_list, function(K,Q) {
                    var tab_active='';
                    var tab_active_contact='';
                    if(qIndex==1)
                    {
                        tab_active='active';
                        tab_active_contact='show active';
                    }
                    tab_left+='<li class="nav-item">\
                        <input type="hidden" name="qIndex[]" value="'+Q.id+'" />\
                        <a class="nav-link '+tab_active+'" id="pills-contact-tab-'+qIndex+'" data-toggle="pill" href="#pills-contact-'+qIndex+'" role="tab" aria-controls="pills-contact-'+qIndex+'" aria-selected="false">Q'+qIndex+'</a>\
                    </li>';
                    tab_right+='<div class="tab-pane fade '+tab_active_contact+'" id="pills-contact-'+qIndex+'" role="tabpanel" aria-labelledby="pills-contact-tab-'+qIndex+'">\
                        <div class="d-flex pb-3">\
                        <p class="question-title">\
                            Q'+qIndex+'\
                        </p>\
                        <div class="question-content pl-2">\
                            <div class="pb-3">\
                                <div class="w-100"><b>Strand</b> : '+Q.objective_mapping.strandName+'</div>\
                                <div class="w-100"><b>Learning Units</b> : '+Q.objective_mapping.learningUnitsName+'</div>\
                                <div class="w-100"><b>Learning Objectives</b> : '+Q.objective_mapping.learningObjectivesTitle+'</div>\
                                <div class="w-100"><b>Difficulty</b> : '+Q.pre_configuration_difficulty_level.title+'</div>\
                            </div>\
                            <p>'+Q.question_en+'</p>\
                            <div class="answer-content">\
                                <div>\
                                    A: \
                                    <label for="age1">'+Q.answers.answer1_en+'</label><br>\
                                    B: \
                                    <label for="age2">'+Q.answers.answer2_en+'</label><br>\
                                    C: \
                                    <label for="age3">'+Q.answers.answer3_en+'</label><br>\
                                    D: \
                                    <label for="age4">'+Q.answers.answer4_en+'</label><br>\
                                </div>\
                                <div class="d-flex align-items-center">\
                                    <span class="mr-1">Text</span>\
                                    <button type="button" class="btn-search bg-pink">Large button</button>\
                                    <button type="button" class="btn-search bg-pink ml-1">Large button</button>\
                                </div>\
                            </div>\
                        </div>\
                        </div>\
                    </div>';
                    if(qIndex==qLength)
                    {
                        $('.form-steps.step4 #pills-tab').html(tab_left);
                        $('.form-steps.step4 #pills-tabContent').html(tab_right);
                        setTimeout(function() {
                            if(tab_right!="")
                            {
                                //updateMathHtmlById("pills-tabContent");
                                MathJax.Hub.Queue(["Typeset",MathJax.Hub]);
                            }
                        },1000);
                        if(tab_left=='')
                        {
                            $('.form-steps.step4 .tab-content-wrap').append('<label class="error">Please Reselect  Select Question Configuration</label>')
                            $("#question-generator button[type=submit]").prop('disabled',true);
                        }
                        else
                        {
                            $("#question-generator button[type=submit]").prop('disabled',false);   
                        }
                    }

                    qIndex++;
                });
                
                $('#cover-spin').hide();
            }
        },
        error: function(response) {
            ErrorHandlingMessage(response);
            $('.form-steps.step4 .tab-content-wrap').append('<label class="error">Please Reselect  Select Question Configuration</label>')
            $("#question-generator button[type=submit]").prop('disabled',true);
        }
    });
}

 /**
 * USE : Get the student list based on select grades and classes
 * Trigger : on select the grades and class
 * Return data : All the student list based on select grade and classes
 */
function getStudents(gradeIds, classIds){
    $("#cover-spin").show();
    $('#question-generator-student-id').html('');
    $.ajax({
        url: BASE_URL + '/question-generator/get-students-list',
        type: 'GET',
        data: {
            'gradeIds': gradeIds,
            'classIds': classIds
        },
        success: function(response) {
            $("#cover-spin").hide();
            if(response.data){
                $('#question-generator-student-id').html(response.data);
                $("#question-generator-student-id").multiselect("rebuild");
            }
        },
        error: function(response) {
            ErrorHandlingMessage(response);
        }
    });
}
$(document).ready(function () {
    $(".btn-up").click(function () {
        var $current = $("#pills-tab li .nav-link.active").closest('li');
        var $previous = $current.prev('li');
        if($previous.length !== 0){
            $current.insertBefore($previous);
        }
    });
    $(".btn-down").click(function(){
      var $current = $("#pills-tab li .nav-link.active").closest('li');
      var $next = $current.next('li');
      if($next.length !== 0){
        $current.insertAfter($next);
      }
    });
    $(".set-top").click(function () {
        var $current = $("#pills-tab li .nav-link.active").closest('li');
        var $previous = $("#pills-tab li:eq(0)");
        if($previous.length !== 0){
            $current.insertBefore($previous);
        }
    });
    $(".set-bottom").click(function () {
        var $current = $("#pills-tab li .nav-link.active").closest('li');
        var $previous = $("#pills-tab li").last();
        if($previous.length !== 0){
            $current.insertAfter($previous);
        }
    });
    $(".btn-remove-tab").click(function () {
        $("#pills-tab li .nav-link.active").closest('li').remove();
        $("#pills-tabContent .tab-pane.fade.show.active").remove();
        setTimeout(function () {
            $(document).find("#pills-tab li:eq(0) a").click();
        },200);
        
    });
})
function checkValidation(currentStep) {
    var checkValid=0;
    $('.form-steps.step'+currentStep+' label.error').remove();
    $('.form-steps.step'+currentStep).find('[name=test_type],[name=title],[name=start_date],[name=end_date],[name=start_time],[name=end_time],[name="difficulty_lvl[]"],[name="studentIds[]"],[name="peerGroupIds[]"],[name="strand_id[]"],[name="learning_unit_id[]"],[name="learning_objectives_id[]"]').each(function(){

        var checkValida=$(this).closest('.form-group').css('display');
        if($(this).attr('name')=='start_time' && $.trim($(this).val())!='')
        {
            if($.trim($('[name="end_time"]').val())=='')
            {
                var lbl=$('[name="end_time"]').closest('.form-group').find('label:eq(0)').text();
                $('[name="end_time"]').parent().append('<label class="error">Please enter '+lbl+'</label>');
                checkValid++;
            }
        }
         if($(this).attr('name')=='end_time' && $.trim($(this).val())!='')
        {
            if($.trim($('[name="start_time"]').val())=='')
            {
                var lbl=$('[name="start_time"]').closest('.form-group').find('label:eq(0)').text();
                $('[name="start_time"]').parent().append('<label class="error">Please enter '+lbl+'</label>');
                checkValid++;
            }
        }
        if($(this).attr('name')=='studentIds[]' && $.trim($(this).val())=='')
        {
            if($.trim($('[name="peerGroupIds[]"]').val())=='')
            {
                var lbl=$(this).closest('.form-group').find('label:eq(0)').text();
                $(this).parent().append('<label class="error">Please enter Either student or peer groups</label>');
                checkValid++;
            }
        }

        if($(this).attr('name')=='peerGroupIds[]' && $.trim($(this).val())=='')
        {
            if($.trim($('[name="studentIds[]"]').val())=='')
            {
                var lbl=$(this).closest('.form-group').find('label:eq(0)').text();
                $(this).parent().append('<label class="error w-100">Please enter Either student or peer groups</label>');
                checkValid++;
            }
        }

        if($.trim($(this).val())=='' && checkValida!='none' )
        {
            if($(this).attr('name')!='studentIds[]' && $(this).attr('name')!='peerGroupIds[]' && $(this).attr('name')!='start_time' && $(this).attr('name')!='end_time')
            {
                var lbl=$(this).closest('.form-group').find('label:eq(0)').text();
                $(this).closest('.form-group').append('<label class="error">Please enter '+lbl+'</label>');
                checkValid++;
            }
        }
    });
    if(checkValid==0)
    {
        return true;
    }
    else
    {
        return false;
    }
}
</script>
@include('backend.layouts.footer')  
@endsection