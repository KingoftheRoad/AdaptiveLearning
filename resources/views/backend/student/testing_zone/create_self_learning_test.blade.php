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
                            <h2 class="mb-4 main-title">{{__('languages.self_learning_test')}}</h2>
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
                @php
                    if(Auth::user()->role_id==2){
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
                                            <li class="student-self-learning-tab step-headings section-step1 admin-tab tab_active " data-tabid="1">1. {{__('languages.question_generators_menu.configuration')}}</li>
                                            <li class="student-self-learning-tab step-headings section-step2 admin-tab" data-tabid="2">2. {{__('languages.question_generators_menu.select_learning_objectives')}}</li>
                                        </ul>
                                    </div>
                                </div>
                                <section class="form-steps step1">
                                    <div class="form-row">
                                        <input type="hidden" name="test_type" value="1">
                                        <input type="hidden" name="self_learning_test_type" value="2">

                                        <div class="form-group col-md-6 mb-50">
                                            <label>{{__('languages.difficulty_mode')}}</label>
                                            <select name="difficulty_mode" class="form-control select-option" id="difficulty_mode">
                                                <option value="auto" selected>{{__('languages.question_generators_menu.auto_fit')}}</option>
                                            </select>
                                        </div>

                                        <!-- <div class="form-group col-md-6 mb-50">
                                            <label>{{__('languages.questions.difficulty_level')}}</label>
                                            <select name="difficulty_lvl[]" class="form-control select-option" id="difficulty_lvl" multiple>
                                                @if(!empty($difficultyLevels))
                                                @foreach($difficultyLevels as $difficultyLevel)
                                                <option value="{{$difficultyLevel->difficulty_level}}" @if($difficultyLevel->difficulty_level == 2) selected @endif>{{$difficultyLevel->{'difficulty_level_name_'.app()->getLocale()} }}</option>
                                                @endforeach
                                                @endif								
                                            </select>
                                            <span name="err_difficulty_level"></span>
                                        </div> -->

                                        <div class="form-group col-md-6 mb-50">
                                            <label>{{__('languages.question_generators_menu.randomize_answer')}} {{__('languages.question_generators_menu.means_different_for_every_student')}}</label>
                                            <select name="randomize_answer" class="form-control select-option" id="select-randomize-answers">
                                                <option value="yes">{{__('languages.question_generators_menu.yes')}}</option>
                                                <option value="no">{{__('languages.question_generators_menu.no')}}</option>
                                            </select>
                                        </div>

                                        <div class="form-group col-md-6 mb-50">
                                            <label>{{__('languages.question_generators_menu.randomize_order')}} {{__('languages.question_generators_menu.means_different_for_every_student')}}</label>
                                            <select name="randomize_order" class="form-control select-option" id="select-randomize-order">
                                                <option value="yes">{{__('languages.question_generators_menu.yes')}}</option>
                                                <option value="no">{{__('languages.question_generators_menu.no')}}</option>
                                            </select>
                                        </div>

                                        <!-- <div class="col-md-12">
                                            <h5 class="font-weight-bold">{{__('Credit Point Rules')}}</h5>
                                            <ol>
                                                <li>
                                                    <h6 class="font-weight-bold">{{__('languages.assignment')}}:</h6>
                                                    <ol>
                                                        <li class="mb-4" style="list-style: disc;">
                                                            <div class="row">
                                                                <div class="col-md-12 credit_point_rules_default_option">
                                                                    <label>{{__('languages.submission_on_time')}}:</label>
                                                                    <input type="radio" name="submission_on_time" value="yes"
                                                                    @if(App\Helpers\Helper::getGlobalConfiguration("submission_on_time") == 'yes')
                                                                        checked
                                                                    @endif
                                                                    ><span>{{__('languages.question_generators_menu.yes')}}</span>
                                                                    <input type="radio" name="submission_on_time" value="no"
                                                                    @if(App\Helpers\Helper::getGlobalConfiguration("submission_on_time") == 'no' || empty(App\Helpers\Helper::getGlobalConfiguration("submission_on_time")))
                                                                        checked
                                                                    @endif
                                                                    ><span>{{__('languages.question_generators_menu.no')}}</span>
                                                                </div>
                                                            </div>
                                                        </li>
                                                        <li class="mb-4" style="list-style: disc;">
                                                            <div class="row">
                                                                <div class="col-md-12 credit_point_rules_default_option">
                                                                    <label>{{__('languages.credit_points_of_accuracy')}}:</label>
                                                                    <input type="radio" name="credit_points_of_accuracy" value="yes"
                                                                    @if(App\Helpers\Helper::getGlobalConfiguration("credit_points_of_accuracy") == 'yes')
                                                                        checked
                                                                    @endif
                                                                    >{{__('languages.question_generators_menu.yes')}}
                                                                    <input type="radio" name="credit_points_of_accuracy" value="no"
                                                                    @if(App\Helpers\Helper::getGlobalConfiguration("credit_points_of_accuracy") == 'no' || empty(App\Helpers\Helper::getGlobalConfiguration("credit_points_of_accuracy")))
                                                                        checked
                                                                    @endif
                                                                    >{{__('languages.question_generators_menu.no')}}
                                                                </div>
                                                            </div>
                                                        </li>
                                                        <li class="mb-4" style="list-style: disc;">
                                                            <div class="row">
                                                                <div class="col-md-12 credit_point_rules_default_option">
                                                                    <label>{{__('languages.credit_points_of_normalized_ability')}}:</label>
                                                                    <input type="radio" name="credit_points_of_normalized_ability" value="yes"
                                                                    @if(App\Helpers\Helper::getGlobalConfiguration("credit_points_of_normalized_ability") == 'yes')
                                                                        checked
                                                                    @endif
                                                                    >{{__('languages.question_generators_menu.yes')}}
                                                                    <input type="radio" name="credit_points_of_normalized_ability" value="no" 
                                                                    @if(App\Helpers\Helper::getGlobalConfiguration("credit_points_of_normalized_ability") == 'no' || empty(App\Helpers\Helper::getGlobalConfiguration("credit_points_of_normalized_ability")))
                                                                        checked
                                                                    @endif
                                                                    >{{__('languages.question_generators_menu.no')}}
                                                                </div>
                                                            </div>
                                                        </li>
                                                    </ol>
                                                </li>
                                            </ol>
                                        </div> -->
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
                                            <label class="error learning_unit_error_msg"></label>
                                        </div>
                                        <div class="form-group col-md-6 mb-50">
                                            <label class="text-bold-600">{{__('languages.question_generators_menu.total_no_of_questions')}}</label>
                                            <input type="text" name="total_no_of_questions" id="total_no_of_questions" value="" class="form-control" placeholder="{{__('languages.question_generators_menu.total_no_of_questions')}}" required readonly>
                                        </div>
                                    </div>
                                    <hr class="blue-line">
                                    <div class="form-row question-info">
                                        {{-- <p>Minimum No of Question Per Skill Required : <strong>{{$RequiredQuestionPerSkill['minimum_question_per_skill']}}</strong></p> --}}
                                        <p>{{__('languages.question_generators_menu.maximum_no_of_question_per_objective')}} : <strong>{{$RequiredQuestionPerSkill['maximum_question_per_skill']}}</strong></p>
                                    </div>
                                    <div class="form-row">
                                        <div class="question-generator-objectives-labels">
                                            <label>{{__('languages.question_generators_menu.learning_objectives')}}</label>
                                            <label>{{__('languages.question_generators_menu.difficulty_level')}}</label>
                                            <label>{{__('languages.question_generators_menu.no_of_question_per_learning_objectives')}}</label>
                                        </div>
                                    </div>
                                    <div class="form-row selection-learning-objectives-section">
                                        @if(isset($LearningObjectives) && !empty($LearningObjectives))
                                        <div class="selected-learning-objectives-difficulty">
                                            <input type="checkbox" name="all_learning_objective_checkbox" value="" class="all_learning_objective_checkbox" checked> Select All
                                        </div>
                                        @foreach ($LearningObjectives as $learningObjectivesKey => $learningObjectives)
                                        @php
                                            $noOfQuestionPerLearningObjective=App\Helpers\Helper::getNoOfQuestionPerLearningObjective($learningObjectives->learning_unit_id,$learningObjectives->id);
                                        @endphp
                                        <div class="selected-learning-objectives-difficulty">
                                            <input type="checkbox" name="learning_unit[{{$learningObjectives->learning_unit_id}}][learning_objective][{{ $learningObjectives->id }}]" value="{{ $learningObjectives->id }}" class="learning_objective_checkbox" checked>
                                            <label>{{ $learningObjectives->foci_number }} {{ $learningObjectives->{'title_'.app()->getLocale()} }}</label>
                                            <select name="learning_unit[{{$learningObjectives->learning_unit_id}}][learning_objective][{{ $learningObjectives->id }}][learning_objectives_difficulty_level][]" class="form-control select-option learning_objectives_difficulty_level" multiple>
                                                <option value="1">1</option>
                                                <option value="2">2</option>
                                                <option value="3">3</option>
                                                <option value="4">4</option>
                                                <option value="5">5</option>
                                            </select>
                                            <input type="text" name="learning_unit[{{$learningObjectives->learning_unit_id}}][learning_objective][{{ $learningObjectives->id }}][get_no_of_question_learning_objectives]" value="{{ $noOfQuestionPerLearningObjective }}" class="get_no_of_question_learning_objectives" min="{{ $noOfQuestionPerLearningObjective }}" max="{{$RequiredQuestionPerSkill['maximum_question_per_skill']}}">
                                        </div>
                                        @endforeach
                                        @endif
                                    </div>
                                    <div class="form-row select-data">
                                        <div class="sm-btn-sec form-row">
                                            <div class="form-group mb-50 btn-sec">
                                                <button type="button" class="blue-btn btn btn-primary previous-button previous_btn_step_2" data-stepid="2">{{__('languages.question_generators_menu.previous')}}</button>
                                                <button type="button" class="blue-btn btn btn-primary generate-self-learning" data-stepid="2">{{__('languages.question_generators_menu.submit')}}</button>                                                
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
@include('backend.student.self_learning_js_script')
@include('backend.layouts.footer')
@endsection