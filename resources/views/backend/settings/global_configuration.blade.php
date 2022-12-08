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
								<h2 class="mb-4 main-title">{{__('languages.global_configurations')}}</h2>
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
					<div class="sm-add-user-sec card">
						<div class="select-option-sec pb-5 card-body">
                            <form class="global-configuration-form" id="global-configuration-form" method="post" action="{{ route('global-configuration') }}" enctype='multipart/form-data'>
                                @csrf()
                                @method('PATCH')
                                <div class="form-row select-data">
                                    {{-- <div class="form-group col-md-3 mb-50">
                                        <label class="text-bold-600">{{__('languages.maximum_ability_history')}}</label>
                                        <input type="text" class="form-control" name="maximum_ability_history" value="{{$ConfigurationArray['maximum_ability_history'] ?? ''}}" placeholder="{{__('languages.maximum_ability_history')}}">
                                    </div> --}}
                                    {{-- <div class="form-group col-md-3 mb-50">
                                        <label class="text-bold-600">{{__('languages.minimum_ability_history')}}</label>
                                        <input type="text" class="form-control" name="minimum_ability_history" value="{{$ConfigurationArray['minimum_ability_history'] ?? ''}}" placeholder="{{__('languages.minimum_ability_history')}}">
                                    </div> --}}
                                    <div class="form-group col-md-4 mb-50">
                                        <label class="text-bold-600">{{__('languages.current')}} {{__('languages.curriculum_year')}}</label>
                                        <select name="current_curriculum_year" class="form-control select-option selectpicker" data-show-subtext="true" data-live-search="true" id="current_curriculum_year">
                                            @if(!empty($getCurriculumYear))
                                            <option value="">{{__('languages.current')}} {{__('languages.curriculum_year')}}</option>
                                                @foreach($getCurriculumYear as $curriculumYear)
                                                    <option value="{{$curriculumYear->id}}" @if(isset($ConfigurationArray['current_curriculum_year']) && $ConfigurationArray['current_curriculum_year'] == $curriculumYear->id) selected @endif>{{$curriculumYear->year}}</option>
                                                @endforeach
                                            @else
                                                <option value="">{{__('languages.current')}} {{__('languages.curriculum_year')}}</option>
                                            @endif
                                        </select>
                                    </div>
                                    <div class="form-group col-md-3 mb-50">
                                        <label class="text-bold-600">{{__('languages.maximum_trials_attempt')}}</label>
                                        <input type="text" class="form-control" name="maximum_trials_attempt" value="{{$ConfigurationArray['maximum_trials_attempt'] ?? ''}}" placeholder="{{__('languages.maximum_trials_attempt')}}">
                                    </div>
                                    <div class="form-group col-md-3 mb-50">
                                        <label class="text-bold-600">{{__('languages.default_second_per_question')}}</label>
                                        <input type="text" class="form-control" name="default_second_per_question" value="{{$ConfigurationArray['default_second_per_question'] ?? ''}}" placeholder="{{__('languages.default_second_per_question')}}">
                                    </div>
                                    <div class="form-group col-md-4 mb-50">
                                        <label class="text-bold-600">{{__('languages.difficulty_selection_type')}}</label>
                                        <select name="difficulty_selection_type" class="form-control select-option selectpicker" data-show-subtext="true" data-live-search="true" id="pass_only_and_or">
                                            <option value="1" @if(isset($ConfigurationArray['difficulty_selection_type']) && $ConfigurationArray['difficulty_selection_type'] == '1') selected @endif>1-{{__('languages.predefined')}}-{{__('languages.difficulties')}}</option>
                                            <option value="2" @if(isset($ConfigurationArray['difficulty_selection_type']) && $ConfigurationArray['difficulty_selection_type'] == '2') selected @endif>2-{{__('languages.ai_calculated')}}-{{__('languages.difficulties')}}.</option>
                                        </select>
                                    </div>
                                </div>

                                <fieldset>
                                    <legend class="global-config-title">{{__('languages.questions_per_learning_objective')}} {{__('languages.for_question_generator_use_only')}}</legend> 
                                    <div class="form-row main-config-section">
                                        <div class="row">
                                            <div class="form-group col-md-6 mb-50">
                                                <label class="text-bold-600">{{__('languages.min_no_question_per_learning_skills')}}</label>
                                                <input type="text" class="form-control" name="no_of_questions_per_learning_skills" value="{{$ConfigurationArray['no_of_questions_per_learning_skills'] ?? ''}}" placeholder="{{__('languages.min_no_question_per_learning_skills')}}">
                                            </div>
                                            <div class="form-group col-md-6 mb-50">
                                                <label class="text-bold-600">{{__('languages.max_no_question_per_learning_objectives')}}</label>
                                                <input type="text" class="form-control" name="max_no_question_per_learning_objectives" value="{{$ConfigurationArray['max_no_question_per_learning_objectives'] ?? ''}}" placeholder="{{__('languages.max_no_question_per_learning_objectives')}}">
                                            </div>
                                        </div>
                                    </div>
                                </fieldset>

                                <fieldset>
                                    <legend class="global-config-title">{{__('languages.question_generator_configurations')}}</legend> 
                                    <div class="form-row main-config-section">
                                        <div class="row">
                                            <div class="form-group col-md-4 mb-50">
                                                <label class="text-bold-600">{{__('languages.question_generator_n')}}</label>
                                                <input type="text" class="form-control" name="question_generator_n" value="{{$ConfigurationArray['question_generator_n'] ?? ''}}" placeholder="{{__('languages.question_generator_n')}}">
                                            </div>
                                            <div class="form-group col-md-3 mb-50">
                                                <label class="text-bold-600">{{__('languages.repeated_rate')}}</label>
                                                <input type="text" class="form-control" name="repeated_rate" value="{{$ConfigurationArray['repeated_rate'] ?? ''}}" placeholder="{{__('languages.repeated_rate')}}">
                                            </div>
                                        </div>
                                    </div>
                                </fieldset>

                                <fieldset>
                                    <legend class="global-config-title">{{__('languages.mastered_objectives_colors')}}</legend> 
                                    <div class="form-row main-config-section">
                                        <div class="row">
                                            <div class="form-group col-md-4 mb-50">
                                                <label class="text-bold-600">{{__('languages.accomplished_objectives_colors')}}</label>
                                                <input type="color" id="accomplished_objective" name="accomplished_objective" value="{{$ConfigurationArray['accomplished_objective'] ?? ''}}">
                                                <span class="error-msg "></span>
                                            </div>
                                            <div class="form-group col-md-4 mb-50">
                                                <label class="text-bold-600">{{__('languages.not_accomplished_objectives_colors')}}</label>
                                                <input type="color" id="not_accomplished_objective" name="not_accomplished_objective" value="{{$ConfigurationArray['not_accomplished_objective'] ?? ''}}">
                                                <span class="error-msg "></span>
                                            </div>
                                        </div>
                                    </div>
                                </fieldset>

                                <fieldset>
                                    <legend class="global-config-title">{{__('languages.result_color')}}</legend> 
                                    <div class="form-row main-config-section">
                                        <div class="row">
                                            <div class="form-group col-md-3 mb-50">
                                                <label class="text-bold-600">{{__('languages.correct_questions')}} {{__('languages.color')}}</label>
                                                <input type="color" id="question_correct_color" name="question_correct_color" value="{{$ConfigurationArray['question_correct_color'] ?? ''}}">
                                                <span class="error-msg "></span>
                                            </div>
                                            <div class="form-group col-md-3 mb-50">
                                                <label class="text-bold-600">{{__('languages.incorrect_questions')}} {{__('languages.color')}}</label>
                                                <input type="color" id="question_incorrect_color" name="question_incorrect_color" value="{{$ConfigurationArray['question_incorrect_color'] ?? ''}}">
                                                <span class="error-msg "></span>
                                            </div>
                                        </div>
                                    </div>
                                </fieldset>

                                <!-- Start This is to define the Criteria of the Status "Passed" of a Learning Objectives. -->
                                <fieldset>
                                    <legend class="global-config-title">{{__('languages.criteria_of_the_status_mastered_of_learning_objectives')}}</legend>
                                    <div class="form-row main-config-section">
                                        <div class="row">
                                            {{-- <div class="form-group col-md-4 mb-50">
                                                <label class="text-bold-600">{{__('languages.pass_ability_level')}}</label>
                                                <input type="text" class="form-control" name="pass_ability_level" value="{{$ConfigurationArray['pass_ability_level'] ?? ''}}" placeholder="{{__('languages.pass_ability_level')}}">
                                                <span class="error-msg "></span>
                                            </div>
                                            <div class="form-group col-md-4 mb-50">
                                                <label class="text-bold-600">{{__('languages.pass_only_or_and')}}</label>
                                                <select name="pass_only_and_or" class="form-control select-option selectpicker" data-show-subtext="true" data-live-search="true" id="pass_only_and_or">
                                                    <option value="only" @if(isset($ConfigurationArray['pass_only_and_or']) && $ConfigurationArray['pass_only_and_or'] == 'only') selected @endif>{{__('languages.only')}}</option>
                                                    <option value="and" @if(isset($ConfigurationArray['pass_only_and_or']) && $ConfigurationArray['pass_only_and_or'] == 'and') selected @endif>{{__('languages.and') }}</option>
                                                    <option value="or" @if(isset($ConfigurationArray['pass_only_and_or']) && $ConfigurationArray['pass_only_and_or'] == 'or') selected @endif>{{__('languages.or')}}</option>
                                                </select>
                                                <!-- <input type="text" class="form-control" name="pass_only_and_or" value="{{$ConfigurationArray['pass_only_and_or'] ?? ''}}" placeholder="{{__('languages.pass_accuracy_level')}}">
                                                <span class="error-msg "></span> -->
                                            </div>
                                            <div class="form-group col-md-4 mb-50">
                                                <label class="text-bold-600">{{__('languages.pass_accuracy_level')}}</label>
                                                <input type="text" class="form-control" name="pass_accuracy_level" value="{{$ConfigurationArray['pass_accuracy_level'] ?? ''}}" placeholder="{{__('languages.pass_only_or_and')}}">
                                                <span class="error-msg "></span>
                                            </div> --}}
                                            <div class="form-group col-md-3 mb-50">
                                                <label class="text-bold-600">{{__('languages.study_status')}}</label>
                                                <select name="study_status_master" class="form-control select-option selectpicker" data-show-subtext="true" data-live-search="true" id="study_status_master">
                                                    <option value="">{{__('languages.select_study_status')}}</option>
                                                    <option value="struggling" @if(isset($ConfigurationArray['study_status_master']) && $ConfigurationArray['study_status_master'] == 'struggling') selected @endif>{{__('languages.struggling')}}</option>
                                                    <option value="beginning" @if(isset($ConfigurationArray['study_status_master']) && $ConfigurationArray['study_status_master'] == 'beginning') selected @endif>{{__('languages.beginning')}}</option>
                                                    <option value="approaching" @if(isset($ConfigurationArray['study_status_master']) && $ConfigurationArray['study_status_master'] == 'approaching') selected @endif>{{__('languages.approaching')}}</option>
                                                    <option value="proficient" @if(isset($ConfigurationArray['study_status_master']) && $ConfigurationArray['study_status_master'] == 'proficient') selected @endif>{{__('languages.proficient')}}</option>
                                                    <option value="advanced" @if(isset($ConfigurationArray['study_status_master']) && $ConfigurationArray['study_status_master'] == 'advanced') selected @endif>{{__('languages.advanced')}}</option>
                                                </select>
                                            </div>
                                        </div>
                                        <fieldset>
                                            <legend class="global-config-title">{{__('languages.question_per_learning_objective_in_calculation_of_study_progress')}} {{__('languages.for_progress_report_use_only')}}</legend> 
                                            <div class="form-row main-config-section">
                                                <div class="row">
                                                    <div class="form-group col-md-6 mb-50">
                                                        <label class="text-bold-600">{{__('languages.min_no_of_questions_of_learning_objective')}}</label>
                                                        <input type="text" class="form-control" name="min_no_question_per_study_progress" value="{{$ConfigurationArray['min_no_question_per_study_progress'] ?? ''}}" placeholder="{{__('languages.min_no_question_per_study_progress')}}">
                                                    </div>
                                                    <div class="form-group col-md-6 mb-50">
                                                        <label class="text-bold-600">{{__('languages.question_window_size_of_learning_objective')}}</label>
                                                        <input type="text" class="form-control" name="question_window_size_of_learning_objective" value="{{$ConfigurationArray['question_window_size_of_learning_objective'] ?? ''}}" placeholder="{{__('languages.question_window_size_of_learning_objective')}}">
                                                    </div>
                                                </div>
                                            </div>
                                        </fieldset>
                                    </div>
                                </fieldset>
                                
                                <!-- End This is to define the Criteria of the Status "Passed" of a Learning Objectives. -->

                                {{-- <fieldset>
                                    <legend class="global-config-title">{{__('languages.passing_score_percentage_of_accuracy_configuration')}}</legend> 
                                    <div class="form-row main-config-section">
                                        <div class="row">
                                            <div class="form-group col-md-3 mb-50">
                                                <label class="text-bold-600">{{__('languages.passing_score')}} (%)</label>
                                                <input type="text" class="form-control" name="passing_score_percentage" value="{{$ConfigurationArray['passing_score_percentage'] ?? ''}}" placeholder="{{__('languages.passing_score')}}(%)">
                                            </div>
                                            <div class="form-group col-md-3 mb-50">
                                                <label class="text-bold-600">{{__('languages.passing_score_accuracy')}}</label>
                                                <input type="text" class="form-control" name="passing_score_accuracy" value="{{$ConfigurationArray['passing_score_accuracy'] ?? ''}}" placeholder="{{__('languages.passing_score_accuracy')}}">
                                            </div>
                                        </div>
                                    </div>
                                </fieldset> --}}
                                
                                <fieldset>
                                    <legend class="global-config-title">{{__('languages.study_status_ability_configurations')}}</legend> 
                                    <div class="form-row main-config-section">
                                        <div class="row">
                                            <div class="form-group col-md-2">
                                                <label class="text-bold-600">{{__('languages.struggling')}}</label>
                                            </div>
                                            @php 
                                                $arrayFromTo = [];
                                                if(isset($ConfigurationArray['struggling']) && !empty($ConfigurationArray['struggling'])){
                                                    $arrayFromTo = json_decode($ConfigurationArray['struggling']);
                                                }
                                            @endphp
                                            
                                            <div class="form-group col-md-4">
                                                <label class="text-bold-600">{{__('languages.from')}}</label>
                                                <input type="text" class="form-control" name="struggling_from" placeholder="{{__('languages.from')}}" value="@if($arrayFromTo){{$arrayFromTo->from}}@endif">
                                                <span class="error-msg "></span>
                                            </div>
                                            <div class="form-group col-md-4"> 
                                                <label class="text-bold-600">{{__('languages.to')}}</label>
                                                <input type="text" class="form-control" name="struggling_to" placeholder="{{__('languages.to')}}" value="@if($arrayFromTo){{$arrayFromTo->to}}@endif">
                                                <span class="error-msg "></span>
                                            </div>
                                            <div class="form-group col-md-2">
                                                <label class="text-bold-600">{{__('languages.color')}}</label>
                                                <input type="color" id="struggling_color" name="struggling_color" value="{{$ConfigurationArray['struggling_color'] ?? ''}}">
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="form-group col-md-2">
                                                <label class="text-bold-600">{{__('languages.beginning')}}</label>
                                            </div>
                                            @php
                                                $arrayFromTo = [];
                                                if(isset($ConfigurationArray['beginning']) && !empty($ConfigurationArray['beginning'])){
                                                    $arrayFromTo = json_decode($ConfigurationArray['beginning']);
                                                }
                                            @endphp
                                            <div class="form-group col-md-4">
                                                <label class="text-bold-600">{{__('languages.from')}}</label>
                                                <input type="text" class="form-control " name="beginning_from" placeholder="{{__('languages.from')}}" value="@if($arrayFromTo){{$arrayFromTo->from}}@endif">
                                                <span class="error-msg "></span>
                                            </div>
                                            <div class="form-group col-md-4"> 
                                                <label class="text-bold-600">{{__('languages.to')}}</label>
                                                <input type="text" class="form-control " name="beginning_to" placeholder="{{__('languages.to')}}" value="@if($arrayFromTo){{$arrayFromTo->to}}@endif">
                                                <span class="error-msg "></span>
                                            </div>
                                            <div class="form-group col-md-2">
                                                <label class="text-bold-600">{{__('languages.color')}}</label>
                                                <input type="color" id="beginning_color" name="beginning_color" value="{{$ConfigurationArray['beginning_color'] ?? ''}}">
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="form-group col-md-2">
                                                <label class="text-bold-600">{{__('languages.approaching')}}</label>
                                            </div>
                                            @php
                                                $arrayFromTo = [];
                                                if(isset($ConfigurationArray['approaching']) && !empty($ConfigurationArray['approaching'])){
                                                    $arrayFromTo = json_decode($ConfigurationArray['approaching']);
                                                }
                                            @endphp
                                            <div class="form-group col-md-4">
                                                <label class="text-bold-600">{{__('languages.from')}}</label>
                                                <input type="text" class="form-control " name="approaching_from" placeholder="{{__('languages.from')}}" value="@if($arrayFromTo){{$arrayFromTo->from}}@endif">
                                                <span class="error-msg "></span>
                                            </div>
                                            <div class="form-group col-md-4"> 
                                                <label class="text-bold-600">{{__('languages.to')}}</label>
                                                <input type="text" class="form-control " name="approaching_to" placeholder="{{__('languages.to')}}" value="@if($arrayFromTo){{$arrayFromTo->to}}@endif">
                                                <span class="error-msg "></span>
                                            </div>
                                            <div class="form-group col-md-2"> 
                                                <label class="text-bold-600">{{__('languages.color')}}</label>
                                                <input type="color" id="approaching_color" name="approaching_color" value="{{$ConfigurationArray['approaching_color'] ?? ''}}">
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="form-group col-md-2">
                                                <label class="text-bold-600">{{__('languages.proficient')}}</label>
                                            </div>
                                            @php
                                                $arrayFromTo = [];
                                                if(isset($ConfigurationArray['proficient']) && !empty($ConfigurationArray['proficient'])){
                                                    $arrayFromTo = json_decode($ConfigurationArray['proficient']);
                                                }
                                            @endphp
                                            <div class="form-group col-md-4">
                                                <label class="text-bold-600">{{__('languages.from')}}</label>
                                                <input type="text" class="form-control " name="proficient_from" placeholder="{{__('languages.from')}}" value="@if($arrayFromTo){{$arrayFromTo->from}}@endif">
                                                <span class="error-msg "></span>
                                            </div>
                                            <div class="form-group col-md-4"> 
                                                <label class="text-bold-600">{{__('languages.to')}}</label>
                                                <input type="text" class="form-control " name="proficient_to" placeholder="{{__('languages.to')}}" value="@if($arrayFromTo){{$arrayFromTo->to}}@endif">
                                                <span class="error-msg "></span>
                                            </div>
                                            <div class="form-group col-md-2"> 
                                                <label class="text-bold-600">{{__('languages.color')}}</label>
                                                <input type="color" id="proficient_color" name="proficient_color" value="{{$ConfigurationArray['proficient_color'] ?? ''}}">
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="form-group col-md-2">
                                                <label class="text-bold-600">{{__('languages.advanced')}}</label>
                                            </div>
                                            @php
                                                $arrayFromTo = [];
                                                if(isset($ConfigurationArray['advanced']) && !empty($ConfigurationArray['advanced'])){
                                                    $arrayFromTo = json_decode($ConfigurationArray['advanced']);
                                                }
                                            @endphp
                                            <div class="form-group col-md-4">
                                                <label class="text-bold-600">{{__('languages.from')}}</label>
                                                <input type="text" class="form-control " name="advanced_from" placeholder="{{__('languages.from')}}" value="@if($arrayFromTo){{$arrayFromTo->from}}@endif">
                                                <span class="error-msg "></span>
                                            </div>
                                            <div class="form-group col-md-4"> 
                                                <label class="text-bold-600">{{__('languages.to')}}</label>
                                                <input type="text" class="form-control " name="advanced_to" placeholder="{{__('languages.to')}}" value="@if($arrayFromTo){{$arrayFromTo->to}}@endif">
                                                <span class="error-msg "></span>
                                            </div>
                                            <div class="form-group col-md-2"> 
                                                <label class="text-bold-600">{{__('languages.color')}}</label>
                                                <input type="color" id="advanced_color" name="advanced_color" value="{{$ConfigurationArray['advanced_color'] ?? ''}}">
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="form-group col-md-2">
                                                <label class="text-bold-600">{{__('languages.incomplete')}}</label>
                                            </div>
                                            <div class="form-group col-md-4"></div>
                                            <div class="form-group col-md-4"></div>
                                            <div class="form-group col-md-2"> 
                                                <label class="text-bold-600">{{__('languages.color')}}</label>
                                                <input type="color" id="incomplete_color" name="incomplete_color" value="{{$ConfigurationArray['incomplete_color'] ?? ''}}">
                                            </div>
                                        </div>
                                    </div>
                                </fieldset>

                                {{-- <fieldset>
                                    <legend class="global-config-title">{{__('languages.question_per_learning_objective_in_calculation_of_study_progress')}} {{__('languages.for_progress_report_use_only')}}</legend> 
                                    <div class="form-row main-config-section">
                                        <div class="row">
                                            <div class="form-group col-md-6 mb-50">
                                                <label class="text-bold-600">{{__('languages.min_no_of_questions_of_learning_objective')}}</label>
                                                <input type="text" class="form-control" name="min_no_question_per_study_progress" value="{{$ConfigurationArray['min_no_question_per_study_progress'] ?? ''}}" placeholder="{{__('languages.min_no_question_per_study_progress')}}">
                                            </div>
                                            <div class="form-group col-md-6 mb-50">
                                                <label class="text-bold-600">{{__('languages.question_window_size_of_learning_objective')}}</label>
                                                <input type="text" class="form-control" name="question_window_size_of_learning_objective" value="{{$ConfigurationArray['question_window_size_of_learning_objective'] ?? ''}}" placeholder="{{__('languages.question_window_size_of_learning_objective')}}">
                                            </div>
                                        </div>
                                    </div>
                                </fieldset> --}}

                                <!-- Start credit points system rules configurations -->
                                <fieldset class="credit-points-assign-section">
                                    <legend class="global-config-title">{{__('languages.value_setting_for_credit_options')}}</legend>
                                    <div class="form-row main-config-section">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <h5 class="font-weight-bold">{{__('languages.assignment')}}</h5>
                                                <div class="row" style="margin-left: 24px;">
                                                    <div class="col-md-12">
                                                        <h6 class="float-left font-weight-bold">{{__('languages.credit_points_for_submission_on_time')}}:</h6>  
                                                        <input type="text" name="assignment_credit_points_for_submission_on_time" class="form-control digits w-50 float-left ml-2" placeholder="{{__('languages.enter_credit_points_for_submission')}}" value="{{$ConfigurationArray['assignment_credit_points_for_submission_on_time'] ?? ''}}" style="">
                                                    </div>
                                                    <div class="col-md-12">
                                                        <h6 class="font-weight-bold">{{__('languages.pass_accuracy_level')}}</h6>
                                                        <ol>
                                                            <li class="mb-4" style="list-style: disc;">
                                                                <label  class="float-left">{{__('languages.starting_accuracy_to_earn_credit_points')}}:</label>  
                                                                <input type="text" name="assignment_starting_accuracy_to_earn_credit_points" class="form-control float-left w-50 ml-2" placeholder="{{__('languages.enter_starting_accuracy_to_earn_credit_points')}}" value="{{$ConfigurationArray['assignment_starting_accuracy_to_earn_credit_points'] ?? ''}}">
                                                                <span class="percentage_sign">%</span>
                                                            </li>
                                                            <li class="mb-4" style="list-style: disc;">
                                                                <label class="float-left">{{__('languages.credit_points_earned_for_starting_accuracy')}}:</label>
                                                                <input type="text" name="assignment_credit_points_earned_for_starting_accuracy" class="form-control digits float-left w-50 ml-2" placeholder="{{__('languages.enter_credit_points_earned_for_starting_accuracy')}}" value="{{$ConfigurationArray['assignment_credit_points_earned_for_starting_accuracy'] ?? ''}}">
                                                            </li>
                                                            <li class="mb-4" style="list-style: disc;">
                                                                <label class="float-left">{{__('languages.number_of_stages_to_earn_extra_credit_point')}}:</label>
                                                                <input type="text" name="assignment_accuracy_number_of_stages_to_earn_extra_credit_point" class="form-control digits float-left w-50 ml-2" placeholder="{{__('languages.enter_number_of_stages_to_earn_extra_credit_point')}}" value="{{$ConfigurationArray['assignment_accuracy_number_of_stages_to_earn_extra_credit_point'] ?? ''}}">
                                                            </li>
                                                            <li class="mb-4" style="list-style: disc;">
                                                                <label class="float-left">{{__('languages.difference_of_accuracy_between_stages')}}:</label>
                                                                <input type="text" name="assignment_difference_of_accuracy_between_stages" class="form-control digits float-left w-50 ml-2" placeholder="{{__('languages.enter_difference_of_accuracy_between_stages')}}" value="{{$ConfigurationArray['assignment_difference_of_accuracy_between_stages'] ?? ''}}">
                                                                <span class="percentage_sign">%</span>
                                                            </li>
                                                            <li class="mb-4" style="list-style: disc;">
                                                                <label class="float-left">{{__('languages.extra_credit_points_for_each_stage')}}:</label>
                                                                <input type="text" name="assignment_accuracy_extra_credit_points_for_each_stage" class="form-control digits float-left w-50 ml-2" placeholder="{{__('languages.extra_credit_points_for_each_stage')}}" value="{{$ConfigurationArray['assignment_accuracy_extra_credit_points_for_each_stage'] ?? ''}}">
                                                            </li>
                                                        </ol>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <h6 class="font-weight-bold">{{__('languages.pass_ability_level')}}</h6>
                                                        <ol>
                                                            <li class="mb-4" style="list-style: disc;">
                                                                <label class="float-left">{{__('languages.starting_normalized_ability_to_earn_credit_points')}}:</label>  
                                                                <input type="text" name="assignment_starting_normalized_ability_to_earn_credit_points" class="form-control float-left w-50 ml-2" placeholder="{{__('languages.enter_starting_normalized_ability_to_earn_credit_points')}}" value="{{$ConfigurationArray['assignment_starting_normalized_ability_to_earn_credit_points'] ?? ''}}">
                                                                <span class="percentage_sign">%</span>
                                                            </li>
                                                            <li class="mb-4" style="list-style: disc;">
                                                                <label class="float-left">{{__('languages.credit_points_earned_for_starting_normalized_ability')}}:</label>  
                                                                <input type="text" name="assignment_credit_points_earned_for_starting_normalized_ability" class="form-control digits float-left w-50 ml-2" placeholder="{{__('languages.enter_credit_points_earned_for_starting_normalized_ability')}}" value="{{$ConfigurationArray['assignment_credit_points_earned_for_starting_normalized_ability'] ?? ''}}">
                                                            </li>
                                                            <li class="mb-4" style="list-style: disc;">
                                                                <label class="float-left">{{__('languages.number_of_stages_to_earn_extra_credit_points')}}:</label>  
                                                                <input type="text" name="assignment_ability_number_of_stages_to_earn_extra_credit_point" class="form-control digits  float-left w-50 ml-2" placeholder="{{__('languages.enter_number_of_stages_to_earn_extra_credit_points')}}" value="{{$ConfigurationArray['assignment_ability_number_of_stages_to_earn_extra_credit_point'] ?? ''}}">
                                                            </li>
                                                            <li class="mb-4" style="list-style: disc;">
                                                                <label class="float-left">{{__('languages.difference_of_normalized_ability_between_stages')}}:</label>  
                                                                <input type="text" name="assignment_difference_of_normalized_ability_between_stages" class="form-control digits float-left w-50 ml-2" placeholder="{{__('languages.enter_difference_of_normalized_ability_between_stages')}}" value="{{$ConfigurationArray['assignment_difference_of_normalized_ability_between_stages'] ?? ''}}">
                                                                <span class="percentage_sign">%</span>
                                                            </li>
                                                            <li class="mb-4" style="list-style: disc;">
                                                                <label class="float-left">{{__('languages.extra_credit_points_for_each_stage')}}:</label>  
                                                                <input type="text" name="assignment_ability_extra_credit_points_for_each_stage" class="form-control digits float-left w-50 ml-2" placeholder="{{__('languages.enter_extra_credit_points_for_each_stage')}}" value="{{$ConfigurationArray['assignment_ability_extra_credit_points_for_each_stage'] ?? ''}}">
                                                            </li>
                                                        </ol>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <h5 class="font-weight-bold">{{__('languages.sidebar.self_learning')}}</h5>
                                                <ol>
                                                    <li>
                                                        <h6 class="font-weight-bold">{{__('languages.exercise')}}:</h6>
                                                        <ol>
                                                            <li class="mb-4" style="list-style: disc;">
                                                                <div class="row">
                                                                    <div class="col-md-6">
                                                                        <label>{{__('languages.minimum_of_questions_in_an_exercise_to_enable_credit_earning')}}:</label>  
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <input type="text" name="self_learning_credit_points_for_exercise" class="form-control digits" placeholder="{{__('languages.enter_credit_points_for_exercise')}}" value="{{$ConfigurationArray['self_learning_credit_points_for_exercise'] ?? ''}}">
                                                                    </div>
                                                                </div>
                                                            </li>
                                                            <li class="mb-4" style="position: relative;left: -16px;">
                                                                <div class="row">
                                                                    <div class="col-md-12">
                                                                        <h6 class="font-weight-bold">{{__('languages.pass_accuracy_level')}}</h6>
                                                                    </div>
                                                                </div>
                                                                <ol>
                                                                    <li class="mb-4" style="list-style: circle;">
                                                                        <label  class="float-left">{{__('languages.starting_accuracy_to_earn_credit_points')}}:</label>  
                                                                        <input type="text" name="self_learning_exercise_starting_accuracy_to_earn_credit_points" class="form-control float-left w-50 ml-2" placeholder="{{__('languages.enter_starting_accuracy_to_earn_credit_points')}}" value="{{$ConfigurationArray['self_learning_exercise_starting_accuracy_to_earn_credit_points'] ?? ''}}">
                                                                        <span class="percentage_sign">%</span>
                                                                    </li>
                                                                    <li class="mb-4" style="list-style: circle;">
                                                                        <label class="float-left">{{__('languages.credit_points_earned_for_starting_accuracy')}}:</label>
                                                                        <input type="text" name="self_learning_exercise_credit_points_earned_for_starting_accuracy" class="form-control digits float-left w-50 ml-2" placeholder="{{__('languages.enter_credit_points_earned_for_starting_accuracy')}}" value="{{$ConfigurationArray['self_learning_exercise_credit_points_earned_for_starting_accuracy'] ?? ''}}">
                                                                    </li>
                                                                    <li class="mb-4" style="list-style: circle;">
                                                                        <label class="float-left">{{__('languages.number_of_stages_to_earn_extra_credit_point')}}:</label>
                                                                        <input type="text" name="self_learning_exercise_number_of_stages_to_earn_extra_credit_point" class="form-control digits float-left w-50 ml-2" placeholder="{{__('languages.enter_number_of_stages_to_earn_extra_credit_point')}}" value="{{$ConfigurationArray['self_learning_exercise_number_of_stages_to_earn_extra_credit_point'] ?? ''}}">
                                                                    </li>
                                                                    <li class="mb-4" style="list-style: circle;">
                                                                        <label class="float-left">{{__('languages.difference_of_accuracy_between_stages')}}:</label>
                                                                        <input type="text" name="self_learning_exercise_difference_of_accuracy_between_stages" class="form-control digits float-left w-50 ml-2" placeholder="{{__('languages.enter_difference_of_accuracy_between_stages')}}" value="{{$ConfigurationArray['self_learning_exercise_difference_of_accuracy_between_stages'] ?? ''}}">
                                                                        <span class="percentage_sign">%</span>
                                                                    </li>
                                                                    <li class="mb-4" style="list-style: circle;">
                                                                        <label class="float-left">{{__('languages.extra_credit_points_for_each_stage')}}:</label>
                                                                        <input type="text" name="self_learning_exercise_accuracy_extra_credit_points_for_each_stage" class="form-control digits float-left w-50 ml-2" placeholder="{{__('languages.enter_extra_credit_points_for_each_stage')}}" value="{{$ConfigurationArray['self_learning_exercise_accuracy_extra_credit_points_for_each_stage'] ?? ''}}">
                                                                    </li>
                                                                </ol>
                                                            </li>
                                                            <li class="mb-4" style="position: relative;left: -16px;">
                                                                <div class="row">
                                                                    <div class="col-md-12">
                                                                        <h6 class="font-weight-bold">{{__('languages.pass_ability_level')}}</h6>
                                                                    </div>
                                                                </div>
                                                                <ol>
                                                                    <li class="mb-4" style="list-style: circle;">
                                                                        <label class="float-left">{{__('languages.starting_normalized_ability_to_earn_credit_points')}}:</label>  
                                                                        <input type="text" name="self_learning_exercise_starting_normalized_ability_to_earn_credit_points" class="form-control float-left w-50 ml-2" placeholder="{{__('languages.enter_starting_normalized_ability_to_earn_credit_points')}}" value="{{$ConfigurationArray['self_learning_exercise_starting_normalized_ability_to_earn_credit_points'] ?? ''}}">
                                                                        <span class="percentage_sign">%</span>
                                                                    </li>
                                                                    <li class="mb-4" style="list-style: circle;">
                                                                        <label class="float-left">{{__('languages.credit_points_earned_for_starting_normalized_ability')}}:</label>  
                                                                        <input type="text" name="self_learning_exercise_credit_points_earned_for_starting_normalized_ability" class="form-control digits float-left w-50 ml-2" placeholder="{{__('languages.enter_credit_points_earned_for_starting_normalized_ability')}} " value="{{$ConfigurationArray['self_learning_exercise_credit_points_earned_for_starting_normalized_ability'] ?? ''}}">
                                                                    </li>
                                                                    <li class="mb-4" style="list-style: circle;">
                                                                        <label class="float-left">{{__('languages.number_of_stages_to_earn_extra_credit_points')}}:</label>  
                                                                        <input type="text" name="self_learning_exercise_number_of_stages_to_earn_extra_credit_points" class="form-control digits  float-left w-50 ml-2" placeholder="{{__('languages.enter_number_of_stages_to_earn_extra_credit_points')}} " value="{{$ConfigurationArray['self_learning_exercise_number_of_stages_to_earn_extra_credit_points'] ?? ''}}">
                                                                    </li>
                                                                    <li class="mb-4" style="list-style: circle;">
                                                                        <label class="float-left">{{__('languages.difference_of_normalized_ability_between_stages')}}:</label>  
                                                                        <input type="text" name="self_learning_exercise_difference_of_normalized_ability_between_stages" class="form-control digits float-left w-50 ml-2" placeholder="{{__('languages.enter_difference_of_normalized_ability_between_stages')}} " value="{{$ConfigurationArray['self_learning_exercise_difference_of_normalized_ability_between_stages'] ?? ''}}">
                                                                        <span class="percentage_sign">%</span>
                                                                    </li>
                                                                    <li class="mb-4" style="list-style: circle;">
                                                                        <label class="float-left">{{__('languages.extra_credit_points_for_each_stage')}}:</label>  
                                                                        <input type="text" name="self_learning_exercise_ability_extra_credit_points_for_each_stage" class="form-control digits float-left w-50 ml-2" placeholder="{{__('languages.enter_extra_credit_points_for_each_stage')}} " value="{{$ConfigurationArray['self_learning_exercise_ability_extra_credit_points_for_each_stage'] ?? ''}}">
                                                                    </li>
                                                                </ol>
                                                            </li>
                                                        </ol>
                                                    </li>

                                                    <li>
                                                        <h6 class="font-weight-bold">{{__('languages.my_studies.test')}}:</h6>
                                                        <ol>
                                                            <li class="mb-4" style="list-style: disc;">
                                                                <div class="row">
                                                                    <div class="col-md-6">
                                                                        <label>{{__('languages.minimum_of_questions_in_an_test_to_enable_credit_earning')}}:</label>  
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <input type="text" name="self_learning_credit_points_for_test" class="form-control digits" placeholder="{{__('languages.enter_credit_points_for_test')}}" value="{{$ConfigurationArray['self_learning_credit_points_for_test'] ?? ''}}">
                                                                    </div>
                                                                </div>
                                                            </li>
                                                            <li class="mb-4" style="position: relative;left: -16px;">
                                                                <div class="row">
                                                                    <div class="col-md-12">
                                                                        <h6 class="font-weight-bold">{{__('languages.pass_accuracy_level')}}</h6>
                                                                    </div>
                                                                </div>
                                                                <ol>
                                                                    <li class="mb-4" style="list-style: circle;">
                                                                        <label  class="float-left">{{__('languages.starting_accuracy_to_earn_credit_points')}}:</label>  
                                                                        <input type="text" name="self_learning_test_starting_accuracy_to_earn_credit_points" class="form-control float-left w-50 ml-2" placeholder="{{__('languages.enter_starting_accuracy_to_earn_credit_points')}}" value="{{$ConfigurationArray['self_learning_test_starting_accuracy_to_earn_credit_points'] ?? ''}}">
                                                                        <span class="percentage_sign">%</span>
                                                                    </li>
                                                                    <li class="mb-4" style="list-style: circle;">
                                                                        <label class="float-left">{{__('languages.credit_points_earned_for_starting_accuracy')}}:</label>
                                                                        <input type="text" name="self_learning_test_credit_points_earned_for_starting_accuracy" class="form-control digits float-left w-50 ml-2" placeholder="{{__('languages.enter_credit_points_earned_for_starting_accuracy')}} " value="{{$ConfigurationArray['self_learning_test_credit_points_earned_for_starting_accuracy'] ?? ''}}">
                                                                    </li>
                                                                    <li class="mb-4" style="list-style: circle;">
                                                                        <label class="float-left">{{__('languages.number_of_stages_to_earn_extra_credit_point')}}:</label>
                                                                        <input type="text" name="self_learning_test_number_of_stages_to_earn_extra_credit_point" class="form-control digits float-left w-50 ml-2" placeholder="{{__('languages.enter_number_of_stages_to_earn_extra_credit_point')}} " value="{{$ConfigurationArray['self_learning_test_number_of_stages_to_earn_extra_credit_point'] ?? ''}}">
                                                                    </li>
                                                                    <li class="mb-4" style="list-style: circle;">
                                                                        <label class="float-left">{{__('languages.difference_of_accuracy_between_stages')}}:</label>
                                                                        <input type="text" name="self_learning_test_difference_of_accuracy_between_stages" class="form-control digits float-left w-50 ml-2" placeholder="{{__('languages.enter_difference_of_accuracy_between_stages')}} " value="{{$ConfigurationArray['self_learning_test_difference_of_accuracy_between_stages'] ?? ''}}">
                                                                        <span class="percentage_sign">%</span>
                                                                    </li>
                                                                    <li class="mb-4" style="list-style: circle;">
                                                                        <label class="float-left">{{__('languages.extra_credit_points_for_each_stage')}}:</label>
                                                                        <input type="text" name="self_learning_test_accuracy_extra_credit_points_for_each_stage" class="form-control digits float-left w-50 ml-2" placeholder="{{__('languages.enter_extra_credit_points_for_each_stage')}} " value="{{$ConfigurationArray['self_learning_test_accuracy_extra_credit_points_for_each_stage'] ?? ''}}">
                                                                    </li>
                                                                </ol>
                                                            </li>
                                                            <li class="mb-4" style="position: relative;left: -16px;">
                                                                <div class="row">
                                                                    <div class="col-md-12">
                                                                        <h6 class="font-weight-bold">{{__('languages.pass_ability_level')}}</h6>
                                                                    </div>
                                                                </div>
                                                                <ol>
                                                                    <li class="mb-4" style="list-style: circle;">
                                                                        <label class="float-left">{{__('languages.starting_normalized_ability_to_earn_credit_points')}}:</label>  
                                                                        <input type="text" name="self_learning_test_starting_normalized_ability_to_earn_credit_points" class="form-control float-left w-50 ml-2" placeholder="{{__('languages.enter_starting_normalized_ability_to_earn_credit_points')}}" value="{{$ConfigurationArray['self_learning_test_starting_normalized_ability_to_earn_credit_points'] ?? ''}}">
                                                                        <span class="percentage_sign">%</span>
                                                                    </li>
                                                                    <li class="mb-4" style="list-style: circle;">
                                                                        <label class="float-left">{{__('languages.credit_points_earned_for_starting_normalized_ability')}}:</label>  
                                                                        <input type="text" name="self_learning_test_credit_points_earned_for_starting_normalized_ability" class="form-control digits float-left w-50 ml-2" placeholder="{{__('languages.enter_credit_points_earned_for_starting_normalized_ability')}} " value="{{$ConfigurationArray['self_learning_test_credit_points_earned_for_starting_normalized_ability'] ?? ''}}">
                                                                    </li>
                                                                    <li class="mb-4" style="list-style: circle;">
                                                                        <label class="float-left">{{__('languages.number_of_stages_to_earn_extra_credit_points')}}:</label>  
                                                                        <input type="text" name="self_learning_test_number_of_stages_to_earn_extra_credit_points" class="form-control digits  float-left w-50 ml-2" placeholder="{{__('languages.enter_number_of_stages_to_earn_extra_credit_points')}} " value="{{$ConfigurationArray['self_learning_test_number_of_stages_to_earn_extra_credit_points'] ?? ''}}">
                                                                    </li>
                                                                    <li class="mb-4" style="list-style: circle;">
                                                                        <label class="float-left">{{__('languages.difference_of_normalized_ability_between_stages')}}:</label>  
                                                                        <input type="text" name="self_learning_test_difference_of_normalized_ability_between_stages" class="form-control digits float-left w-50 ml-2" placeholder="{{__('languages.enter_difference_of_normalized_ability_between_stages')}} " value="{{$ConfigurationArray['self_learning_test_difference_of_normalized_ability_between_stages'] ?? ''}}">
                                                                        <span class="percentage_sign">%</span>
                                                                    </li>
                                                                    <li class="mb-4" style="list-style: circle;">
                                                                        <label class="float-left">{{__('languages.extra_credit_points_for_each_stage')}}:</label>  
                                                                        <input type="text" name="self_learning_test_ability_extra_credit_points_for_each_stage" class="form-control digits float-left w-50 ml-2" placeholder="{{__('languages.enter_extra_credit_points_for_each_stage')}} " value="{{$ConfigurationArray['self_learning_test_ability_extra_credit_points_for_each_stage'] ?? ''}}">
                                                                    </li>
                                                                </ol>
                                                            </li>
                                                        </ol>
                                                    </li>
                                                </ol>
                                            </div>

                                            <div class="col-md-12">
                                                <h5 class="font-weight-bold">{{__('languages.default_selection_for_credit_point_rules')}}</h5>
                                                <ol>
                                                    <li>
                                                        <h6 class="font-weight-bold">{{__('languages.assignment')}}:</h6>
                                                        <ol>
                                                            <li class="mb-4" style="list-style: disc;">
                                                                <div class="row">
                                                                    <div class="col-md-12 credit_point_rules_default_option">
                                                                        <label>{{__('languages.submission_on_time')}}:</label>
                                                                        <input type="radio" name="submission_on_time" value="yes"
                                                                        @if(isset($ConfigurationArray["submission_on_time"]) && $ConfigurationArray["submission_on_time"] == 'yes')
                                                                            checked 
                                                                        @endif
                                                                        ><span>{{__('languages.question_generators_menu.yes')}}</span>
                                                                        <input type="radio" name="submission_on_time" value="no"
                                                                        @if(isset($ConfigurationArray["submission_on_time"]) && $ConfigurationArray["submission_on_time"] == 'no') checked 
                                                                        @elseif(empty($ConfigurationArray["submission_on_time"])) checked
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
                                                                        @if(isset($ConfigurationArray["credit_points_of_accuracy"]) && $ConfigurationArray["credit_points_of_accuracy"] == 'yes')
                                                                            checked 
                                                                        @endif
                                                                        >{{__('languages.question_generators_menu.yes')}}
                                                                        <input type="radio" name="credit_points_of_accuracy" value="no"
                                                                        @if(isset($ConfigurationArray["credit_points_of_accuracy"]) && $ConfigurationArray["credit_points_of_accuracy"] == 'no') checked 
                                                                        @elseif(empty($ConfigurationArray["credit_points_of_accuracy"])) checked
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
                                                                        @if(isset($ConfigurationArray["credit_points_of_normalized_ability"]) && $ConfigurationArray["credit_points_of_normalized_ability"] == 'yes')
                                                                            checked 
                                                                        @endif
                                                                        >{{__('languages.question_generators_menu.yes')}}
                                                                        <input type="radio" name="credit_points_of_normalized_ability" value="no" 
                                                                        @if(isset($ConfigurationArray["credit_points_of_normalized_ability"]) && $ConfigurationArray["credit_points_of_normalized_ability"] == 'no') checked 
                                                                        @elseif(empty($ConfigurationArray["credit_points_of_normalized_ability"])) checked
                                                                        @endif
                                                                        >{{__('languages.question_generators_menu.no')}}
                                                                    </div>
                                                                </div>
                                                            </li>
                                                        </ol>
                                                    </li>
                                                </ol>
                                            </div>
                                        </div>
                                    </div>
                                </fieldset>
                                <!-- End credit points system rules configurations -->
                                {{-- Sutom Message For Student Attempt Exam --}}
                                <fieldset>
                                    <legend class="global-config-title">{{__('languages.attempt_exam_notification_message')}}</legend> 
                                    <div class="form-row main-config-section">
                                        <div class="row">
                                            <div class="form-group col-md-6 mb-50">
                                                <label class="text-bold-600">{{__('languages.english')}} {{__('languages.message')}}</label>
                                                <input type="text" name="attempt_exam_restrict_notification_en" class="form-control" placeholder="{{__('languages.english')}} {{__('languages.message')}}" value="{{$ConfigurationArray['attempt_exam_restrict_notification_en'] ?? ''}}">
                                                <span class="error-msg "></span>
                                            </div>
                                            <div class="form-group col-md-6 mb-50">
                                                <label class="text-bold-600">{{__('languages.chinese')}} {{__('languages.message')}}</label>
                                                <input type="text" name="attempt_exam_restrict_notification_ch" class="form-control" placeholder="{{__('languages.chinese')}} {{__('languages.message')}}" value="{{$ConfigurationArray['attempt_exam_restrict_notification_ch'] ?? ''}}">
                                                <span class="error-msg "></span>
                                            </div>
                                        </div>
                                    </div>
                                </fieldset>
                                 {{-- Sutom Message For Student Attempt Exam --}}

                                <div class="form-row select-data">
                                    <div class="sm-btn-sec form-row">
                                        <div class="form-group col-md-6 mb-50 btn-sec">
                                            <button class="blue-btn btn btn-primary mt-4">{{ __('languages.submit') }}</button>
                                        </div>
                                    </div>
                                </div>
                            </form>
						</div>
					</div>
				</div>
			</div>
	      </div>
		</div>
        @include('backend.layouts.footer')
@endsection