@extends('backend.layouts.app')
@section('content')
<div class="wrapper d-flex align-items-stretch sm-deskbord-main-sec">
    @include('backend.layouts.sidebar')
    <div id="content" class="pl-2 pb-5">
        @include('backend.layouts.header')
        @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
        @endif
        <div class="sm-right-detail-sec pl-5 pr-5" id="self-learning-config-section">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-12">
                        <div class="sec-title">
                            <h2 class="mb-4 main-title">{{$pageTitle ?? ''}}</h2>
                        </div>
                        <div class="sec-title">
                            <a href="javascript:void(0);" class="btn-back" id="backButton">{{__('languages.back')}}</a>
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
                <!-- @php
                    if(Auth::user()->role_id==1){
                        $color = '#A5A6F6';
                    }elseif(Auth::user()->role_id==2){
                        $color = '#f7bfbf';
                    }else if(Auth::user()->role_id==3){
                        $color = '#d8dc41';
                    }else if(Auth::user()->role_id == 7){
                        $color = '#BDE5E1';
                    }else{
                        $color = '#a8e4b0';
                    }
                @endphp -->
                @php
                    if(Auth::user()->role_id == 1){
                        $color = '#A5A6F6';
                    }else if(Auth::user()->role_id==2){
                        $color = '#f7bfbf';
                    }else if(Auth::user()->role_id==3){
                        $color = '#d8dc41';
                    }else if(Auth::user()->role_id == 7){
                        $color = '#BDE5E1';
                    }else{
                        $color = '#a8e4b0';
                    }
                @endphp
                <style type="text/css">
                    .question-generator-option-headings .admin-tab {
                        background-color:<?php echo $color;?> !important;
                    }
                    .question-generator-option-headings li.admin-tab.tab_active {
                        background-color:<?php echo str_replace(';','', App\Helpers\Helper::getRoleBasedMenuActiveColor());?> !important;
                    }
                    .bg-pink {
                        background-color:<?php echo $color;?> !important;
                        border-color:<?php echo $color;?> !important;
                    }
                    .sm-deskbord-main-sec .tab-content-wrap .nav-pills .nav-link.active {
                        background-color: <?php echo $color;?> !important;
                        color: #000;
                        font-family: inherit;
                    }
                </style>
                <form name="question-generator" id="question-generator" action="{{ route('school.generate-questions') }}" method="POST">
                    @csrf
                    <div class="sm-add-user-sec card">
                        <div class="select-option-sec pb-5 card-body">
                            <div id="wizard">
                                <div class="question-generator-option-headings mb-3">
                                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 pl-0 pr-0">
                                        <ul class="form-tab">
                                            <li class="student-self-learning-preview-tab step-headings section-step1 admin-tab tab_active " data-tabid="1">1. {{__('languages.question_generators_menu.configuration')}}</li>
                                            <li class="student-self-learning-preview-tab step-headings section-step2 admin-tab" data-tabid="2">2. {{__('languages.question_generators_menu.select_learning_objectives')}}</li>
                                            <li class="student-self-learning-preview-tab step-headings section-step3 admin-tab" data-tabid="3">3. {{__('languages.question_generators_menu.review_of_questions')}}</li>
                                        </ul>
                                    </div>
                                </div>
                                <section class="form-steps step1">
                                    <div class="form-row">
                                        <!-- <input type="hidden" name="test_type" value="1">
                                        <input type="hidden" name="self_learning_test_type" value="1"> -->
                                        <div class="form-group col-md-6 mb-50">
                                            <label>{{__('languages.difficulty_mode')}}</label>
                                            <select name="difficulty_mode" class="form-control select-option" id="difficulty_mode" disabled>
                                                <option value="manual" @if($LearningObjectiveConfigurations->difficulty_mode == "manual") selected @endif disabled="disabled">{{__('languages.manual')}}</option>
                                                <option value="auto" @if($LearningObjectiveConfigurations->difficulty_mode == "auto") selected @endif disabled="disabled">{{__('languages.question_generators_menu.auto_fit')}}</option>
                                            </select>
                                        </div>

                                        @if($LearningObjectiveConfigurations->difficulty_mode != 'auto')
                                        <div class="form-group col-md-6 mb-50">
                                            <label>{{__('languages.questions.difficulty_level')}}</label>
                                            <select name="difficulty_lvl[]" class="form-control select-option" id="difficulty_lvl" multiple>
                                                @if(isset($difficultyLevels) && !empty($difficultyLevels))
                                                    @foreach($difficultyLevels as $difficultyLevel)
                                                    <option value="{{$difficultyLevel->difficulty_level}}"
                                                    @if(in_array($difficultyLevel->difficulty_level,$LearningObjectiveConfigurations->difficulty_lvl)) selected @endif
                                                    disabled="disabled"
                                                    >{{$difficultyLevel->{'difficulty_level_name_'.app()->getLocale()} }}</option>
                                                    @endforeach
                                                @endif
                                            </select>
                                            <span name="err_difficulty_level"></span>
                                        </div>
                                        @endif
                                    </div>
                                    <div class="form-row select-data">
                                        <div class="sm-btn-sec form-row">
                                            <div class="form-group mb-50 btn-sec">
                                                <button type="button" class="blue-btn btn btn-primary next-button next_btn_step_1" data-stepid="1">{{__('languages.question_generators_menu.next')}}</button>                                                
                                            </div>
                                        </div>
                                    </div>
                                </section>

                                <section class="form-steps step2" style="display:none;">
                                    <input type="hidden" name="questionIds" value="" id="questionIds">
                                    <div class="form-row">
                                        <div class="form-group col-md-6 mb-50">
                                            <label>{{__('languages.upload_document.strands')}}</label>
                                            <select name="strand_id[]" class="form-control select-option" id="strand_id" multiple>
                                                @if(isset($strandsList) && !empty($strandsList))
                                                    @foreach ($strandsList as $strandKey => $strand)
                                                        <option value="{{ $strand->id }}"
                                                        @if(in_array($strand->id,$LearningObjectiveConfigurations->strand_id)) selected @endif
                                                        disabled="disabled"
                                                        >{{ $strand->{'name_'.app()->getLocale()} }}</option>
                                                    @endforeach
                                                @else
                                                    <option value="" disabled="disabled">{{__('languages.no_strands_available')}}</option>
                                                @endif
                                            </select>
                                        </div>
                                        <div class="form-group col-md-6 mb-50">
                                            <label>{{__('languages.upload_document.learning_units')}}</label>
                                            <select name="learning_unit_id[]" class="form-control select-option" id="learning_unit" multiple>
                                                @if(isset($LearningUnits) && !empty($LearningUnits))
                                                    @foreach ($LearningUnits as $learningUnitKey => $learningUnit)
                                                    <option value="{{$learningUnit->id}}"
                                                    @if(in_array($learningUnit->id,$LearningObjectiveConfigurations->learning_unit_id)) selected @endif
                                                    disabled="disabled"
                                                    >{{ $learningUnit->{'name_'.app()->getLocale()} }}</option>
                                                    @endforeach
                                                @else
                                                    <option value="" disabled="disabled">{{__('languages.no_learning_units_available')}}</option>
                                                @endif
                                            </select>
                                            <label class="error learning_unit_error_msg"></label>
                                        </div>
                                        <div class="form-group col-md-6 mb-50">
                                            <label class="text-bold-600">{{__('languages.question_generators_menu.total_no_of_questions')}}</label>
                                            <input type="text" name="total_no_of_questions" value="{{$LearningObjectiveConfigurations->total_no_of_questions}}" class="form-control" placeholder="{{__('languages.question_generators_menu.total_no_of_questions')}}" required readonly>
                                        </div>
                                    </div>
                                    <hr class="blue-line">
                                    <div class="form-row">
                                        <div class="question-generator-objectives-labels">
                                            <label>{{__('languages.question_generators_menu.learning_objectives')}}</label>
                                            @if($LearningObjectiveConfigurations->difficulty_mode != 'auto')
                                            <label>{{__('languages.question_generators_menu.difficulty_level')}}</label>
                                            @endif
                                            <label>{{__('languages.question_generators_menu.no_of_question_per_learning_objectives')}}</label>
                                        </div>
                                    </div>
                                    <div class="form-row selection-learning-objectives-section">
                                    @if(isset($LearningObjectiveConfigurations->learning_unit) && !empty($LearningObjectiveConfigurations->learning_unit))
                                        @foreach($LearningObjectiveConfigurations->learning_unit as $LearningUnitsList)
                                            @foreach($LearningUnitsList as $learningUnitId => $learningObjectivesList)
                                            @foreach($learningObjectivesList as $learningObjectiveId => $learningObjectivesData)
                                            <?php
                                            $FilterObjectivesData = array_filter($LearningObjectives->toArray(), function ($var) use($learningObjectiveId){
                                                if($var['id'] == $learningObjectiveId){
                                                    return $var ?? [];
                                                }
                                            });
                                            $ObjectivesData = array_reduce($FilterObjectivesData, 'array_merge', array());
                                            ?>
                                            <div class="selected-learning-objectives-difficulty">
                                                <input type="checkbox" value="{{$ObjectivesData['id'] }}" class="learning_objective_checkbox" checked disabled>
                                                <label>{{$ObjectivesData['foci_number']}} {{ $ObjectivesData['title_'.app()->getLocale()]}}</label>
                                                @if($LearningObjectiveConfigurations->difficulty_mode != 'auto')
                                                <select class="form-control select-option learning_objectives_difficulty_level" multiple>
                                                    @for($count=1; $count<=5; $count++)
                                                    <option value="{{$count}}"
                                                    @if(in_array($count,$learningObjectivesData->learning_objectives_difficulty_level)) selected @endif
                                                    disabled="disabled"
                                                    >{{$count}}</option>
                                                    @endfor
                                                </select>
                                                @endif
                                                <input type="text" value="{{$learningObjectivesData->get_no_of_question_learning_objectives}}" disabled class="get_no_of_question_learning_objectives">
                                            </div>
                                            @endforeach
                                            @endforeach
                                        @endforeach
                                    @endif
                                    </div>
                                    <div class="form-row select-data">
                                        <div class="sm-btn-sec form-row">
                                            <div class="form-group mb-50 btn-sec">
                                                <!-- <button type="button" class="blue-btn btn btn-primary previous-button previous_btn_step_2" data-stepid="2">{{__('languages.question_generators_menu.previous')}}</button>
                                                <button type="button" class="blue-btn btn btn-primary next-button next_btn_step_2" data-stepid="2">{{ __('languages.question_generators_menu.next') }}</button> -->

                                                <button type="button" class="blue-btn btn btn-primary previous-button previous_btn_step_2" data-stepid="2">{{ __('languages.question_generators_menu.previous') }}</button>
                                                <button type="button" class="blue-btn btn btn-primary next-button next_btn_step_2" data-stepid="2">{{ __('languages.question_generators_menu.next') }}</button>
                                            </div>
                                        </div>
                                    </div>
                                </section>

                                <section class="form-steps step3" style="display:none;">
                                    <div class="d-flex review-question-main tab-content-wrap w-100">
                                    {!!$questionListHtml!!}
                                    </div>
                                    <div class="form-row select-data float-left mt-2 clearfix">
                                        <div class="sm-btn-sec form-row">
                                            <div class="form-group mb-50 btn-sec">
                                                <button type="button" class="blue-btn btn btn-primary previous-button previous_btn_step_3" data-stepid="3">{{ __('languages.question_generators_menu.previous') }}</button>
                                                <a class="blue-btn btn btn-primary" href="javascript:void(0);" id="backButton">{{ __('languages.close') }}</a>
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
    $(document).on('click', '.next-button', function() {
        var currentStep = $(this).attr('data-stepid');
        var nextStep = (parseInt(currentStep)+1);
        $('.form-steps').hide();
        $('.step-headings').removeClass('tab_active');
        $('.section-step'+nextStep).addClass('tab_active');
        $('.step'+nextStep).show();
    });

    $(document).on('click', '.admin-tab', function() {
        var TabId = $(this).attr('data-tabid');
        $('.form-steps').hide();
        $('.step-headings').removeClass('tab_active');
        $('.section-step'+TabId).addClass('tab_active');
        $('.step'+TabId).show();
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
});
</script>
@include('backend.layouts.footer')
@endsection