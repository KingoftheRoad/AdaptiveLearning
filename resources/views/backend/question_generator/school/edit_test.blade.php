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
                            <h2 class="mb-4 main-title">{{__('languages.question_generators')}}</h2>
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
                        background-color:<?php echo str_replace(';','', App\Helpers\Helper::getRoleBasedMenuActiveColor()); ?> !important;
                    }
                    .bg-pink {
                        background-color:<?php echo $color;?> !important;
                        border-color:<?php echo $color; ?> !important;
                    }
                    .sm-deskbord-main-sec .tab-content-wrap .nav-pills .nav-link.active {
                        background-color: <?php echo $color; ?> !important;
                        color: #000;
                        font-family: inherit;
                    }
                </style>
                @php
                $fieldDisabled='';
                if($exam->use_of_mode == 1 && empty($exam->parent_exam_id) && $exam->created_by_user == 'super_admin'){
                    $fieldDisabled='disabled';
                }
                @endphp
                <form name="question-generator" id="question-generator" action="{{ route('school.generate-questions-edit',$exam->id) }}" method="POST">
                    @csrf
                    <div class="sm-add-user-sec card">
                        <div class="select-option-sec pb-5 card-body">
                            <div id="wizard">
                                <div class="question-generator-option-headings mb-3">
                                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 pl-0 pr-0">
                                        <ul class="form-tab">
                                            <li class="step-headings section-step1 admin-tab tab_active" data-tabid="1">1. {{__('languages.question_generators_menu.configuration')}}</li>
                                            <li class="step-headings section-step2 admin-tab" data-tabid="2">2.{{__('languages.question_generators_menu.assigned_to_classes_and_peer_group')}}</li>
                                            <li class="step-headings section-step3 admin-tab" data-tabid="3">3. {{__('languages.question_generators_menu.select_learning_objectives')}}</li>
                                            <li class="step-headings section-step4 admin-tab" data-tabid="4">4. {{__('languages.question_generators_menu.review_of_questions')}}</li>
                                        </ul>
                                    </div>
                                </div>
                                <section class="form-steps step1">
                                    <div class="form-row">

                                        <div class="form-group col-md-6 mb-50">
                                            <label class="text-bold-600">{{__('languages.question_generators_menu.test_mode')}}</label>
                                            <select name="test_type" class="form-control select-option" id="test_type" {{ $fieldDisabled }} >
                                                <option value="2" @if($exam->exam_type == 2) selected @endif>{{__('languages.exercise')}}</option> 
                                                <option value="3" @if($exam->exam_type == 3) selected @endif>{{__('languages.test_text')}}</option>
                                            </select>
                                        </div>

                                        <div class="form-group col-md-6 mb-50">
                                            <label class="text-bold-600">{{__('languages.question_generators_menu.title')}}</label>
                                            <input type="text" name="title" value="{{ $exam->title }}" class="form-control" placeholder="{{__('languages.question_generators_menu.enter_title')}}" required {{ $fieldDisabled }} >
                                        </div>

                                        <div class="form-group col-md-6 mb-50">
                                            <label>{{ __('languages.question_generators_menu.start_date') }}</label>
                                            <div class="input-group date">
                                                <input type="text" class="form-control date-picker" name="start_date" value="{{ date('d/m/Y', strtotime($exam->from_date)) }}" placeholder="{{__('languages.question_generators_menu.start_date')}}" autocomplete="off" {{ $fieldDisabled }} >
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
                                            <label for="id_end_time">{{ __('languages.question_generators_menu.start_time') }}</label>
                                            <div class="input-group date">
                                                <select name="start_time" class="form-control select-option" id="test_start_time" {{ $fieldDisabled }} >
                                                    <option value="">{{__('languages.question_generators_menu.select_test_start_time')}}</option>
                                                    @if(isset($timeSlots) && !empty($timeSlots))
                                                        @foreach($timeSlots as $timeSlotKey => $time)
                                                            <option @if($exam->start_time==$time) selected @endif value="{{$time}}">{{$time}}</option>
                                                        @endforeach
                                                    @endif
                                                </select>
                                            </div>
                                            <span id="end-date-error"></span>
                                            @if($errors->has('start_time'))<span class="validation_error">{{ $errors->first('start_time') }}</span>@endif
                                        </div>

                                        <div class="form-group col-md-6 mb-50">
                                            <label>{{ __('languages.question_generators_menu.end_date') }}</label>
                                            <div class="input-group date">
                                                {{-- <input type="text" class="form-control date-picker" name="end_date" value="{{ date('d/m/Y', strtotime($exam->to_date)) }}" placeholder="{{__('languages.question_generators_menu.end_date')}}" autocomplete="off" {{ $fieldDisabled }} > --}}
                                                <input type="text" class="form-control end-date-picker" name="end_date" value="{{ date('d/m/Y', strtotime($exam->to_date)) }}" placeholder="{{__('languages.question_generators_menu.end_date')}}" autocomplete="off" {{ $fieldDisabled }} >
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
                                            <label for="id_end_time">{{ __('languages.question_generators_menu.end_time') }}</label>
                                            <div class="input-group date">
                                                <select name="end_time" class="form-control select-option" id="test_end_time" {{ $fieldDisabled }} >
                                                    <option value="">{{__('languages.question_generators_menu.select_test_end_time')}}</option>
                                                    @if(isset($timeSlots) && !empty($timeSlots))
                                                        @foreach($timeSlots as $timeSlotKey => $time)
                                                            <option @if($exam->end_time==$time) selected @endif value="{{$time}}">{{$time}}</option>
                                                        @endforeach
                                                    @endif
                                                </select>
                                            </div>
                                            <span id="end-date-error"></span>
                                            @if($errors->has('end_time'))<span class="validation_error">{{ $errors->first('end_time') }}</span>@endif
                                        </div>

                                        <div class="form-group col-md-6 mb-50">
                                            <label class="text-bold-600">{{__('languages.question_generators_menu.report_date')}}</label>
                                            <select name="report_date" class="form-control select-option" id="select-report-date" {{ $fieldDisabled }} >
                                                <option value="end_date" @if($exam->report_type == 'end_date') selected @endif>{{__('languages.question_generators_menu.end_date')}}</option>
                                                <option value="after_submit" @if($exam->report_type == 'after_submit') selected @endif >{{__('languages.question_generators_menu.after_submit')}}</option>
                                                <option value="custom_date" @if($exam->report_type == 'custom_date') selected @endif >{{__('languages.question_generators_menu.custom_date')}}</option>
                                            </select>
                                        </div>

                                        <div class="form-group col-md-6 mb-50 custom_date" style="display: none;">
                                            <label>{{__('languages.question_generators_menu.report_custom_date')}}</label>
                                            <div class="input-group date">
                                                <input type="text" class="form-control date-picker" name="custom_date" value="{{ date('d/m/Y', strtotime($exam->result_date)) }}" placeholder="{{__('languages.question_generators_menu.end_date')}}" autocomplete="off" {{ $fieldDisabled }} >
                                                <div class="input-group-addon input-group-append">
                                                    <div class="input-group-text">
                                                        <i class="glyphicon glyphicon-calendar fa fa-calendar"></i>
                                                    </div>
                                                </div>
                                            </div>
                                            <span id="end-date-error"></span>
                                            @if($errors->has('custom_date'))<span class="validation_error">{{ $errors->first('custom_date') }}</span>@endif
                                        </div>

                                        <!-- <div class="form-group col-md-6 mb-50" id="no_of_trials_per_question_section" style="display:none;">
                                            <label class="text-bold-600">{{__('languages.question_generators_menu.no_of_trials_per_question')}}</label>
                                            <select name="no_of_trials_per_question" class="form-control select-option" id="select-no-of-per-trials-question" {{ $fieldDisabled }} >
                                                <option value="1" @if($exam->no_of_trials_per_question==1) selected @endif >1</option> 
                                                <option value="2" @if($exam->no_of_trials_per_question==2) selected @endif >2</option>
                                            </select>
                                        </div> -->

                                        <div class="form-group col-md-6 mb-50">
                                            <label>{{__('languages.difficulty_mode')}}</label>
                                            <select name="difficulty_mode" class="form-control select-option" id="difficulty_mode" {{ $fieldDisabled }}  @if($exam->difficulty_mode=='auto') disabled @endif>
                                                <option value="manual" @if($exam->difficulty_mode=='manual') selected @endif >{{__('languages.manual')}}</option>
                                                <option value="auto" @if($exam->difficulty_mode=='auto') selected @endif >{{__('languages.question_generators_menu.auto_fit')}}</option>
                                            </select>
                                        </div>
                                        <div class="form-group col-md-6 mb-50">
                                            <label>{{__('languages.questions.difficulty_level')}}</label>
                                            <select name="difficulty_lvl[]" class="form-control select-option" id="difficulty_lvl" multiple {{ $fieldDisabled }}  @if($exam->difficulty_mode=='auto') disabled @endif>
                                                @if(!empty($difficultyLevels))
                                                @php
                                                $examDifficultyLevels = ($exam->difficulty_levels) ? explode(',',$exam->difficulty_levels) : [];
                                                @endphp
                                                @foreach($difficultyLevels as $difficultyLevel)
                                                <option value="{{$difficultyLevel->difficulty_level}}" @if(in_array($difficultyLevel->difficulty_level,$examDifficultyLevels)) selected @endif>{{$difficultyLevel->difficulty_level_name}}</option>
                                                @endforeach
                                                @endif								
                                            </select>
                                            <span name="err_difficulty_level"></span>
                                        </div>

                                        <!-- <div class="form-group col-md-6 mb-50" id="display_hints_section" style="display:none;">
                                            <label>{{__('languages.question_generators_menu.display_hints')}}</label>
                                            <select name="display_hints" class="form-control select-option" id="select-display-hints" {{ $fieldDisabled }} >
                                                <option value="no" @if($exam->display_hints=='no') selected @endif>{{__('languages.question_generators_menu.no')}}</option>
                                                <option value="yes"  @if($exam->display_hints=='yes') selected @endif >{{__('languages.question_generators_menu.yes')}}</option>
                                            </select>
                                        </div> -->

                                        <!-- <div class="form-group col-md-6 mb-50">
                                            <label>{{__('languages.question_generators_menu.display_full_solution_in_report')}}</label>
                                            <select name="display_full_solution" class="form-control select-option" id="select-display-full-solutions" {{ $fieldDisabled }} >
                                                <option value="yes" @if($exam->display_full_solution=='yes') selected @endif >{{__('languages.question_generators_menu.yes')}}</option>
                                                <option value="no" @if($exam->display_full_solution=='no') selected @endif >{{__('languages.question_generators_menu.no')}}</option>
                                            </select>
                                        </div>

                                        <div class="form-group col-md-6 mb-50">
                                            <label>{{__('languages.question_generators_menu.display_pr_answer_hints')}}</label>
                                            <select name="display_pr_answer_hints" class="form-control select-option" id="select-display-pr-answer-hints" {{ $fieldDisabled }} >
                                                <option value="yes"  @if($exam->display_pr_answer_hints=='yes') selected @endif >{{__('languages.question_generators_menu.yes')}}</option>
                                                <option value="no"  @if($exam->display_pr_answer_hints=='no') selected @endif >{{__('languages.question_generators_menu.no')}}</option>
                                            </select>
                                        </div>

                                        <div class="form-group col-md-6 mb-50">
                                            <label>{{__('languages.question_generators_menu.randomize_answer')}} {{__('languages.question_generators_menu.means_different_for_every_student')}}</label>
                                            <select name="randomize_answer" class="form-control select-option" id="select-randomize-answers" {{ $fieldDisabled }} >
                                                <option value="yes" @if($exam->randomize_answer=='yes') selected @endif >{{__('languages.question_generators_menu.yes')}}</option>
                                                <option value="no" @if($exam->randomize_answer=='no') selected @endif >{{__('languages.question_generators_menu.no')}}</option>
                                            </select>
                                        </div>

                                        <div class="form-group col-md-6 mb-50">
                                            <label>{{__('languages.question_generators_menu.randomize_order')}} {{__('languages.question_generators_menu.means_different_for_every_student')}}</label>
                                            <select name="randomize_order" class="form-control select-option" id="select-randomize-order" {{ $fieldDisabled }} >
                                                <option value="yes" @if($exam->randomize_order=='yes') selected @endif >{{__('languages.question_generators_menu.yes')}}</option>
                                                <option value="no" @if($exam->randomize_order=='no') selected @endif >{{__('languages.question_generators_menu.no')}}</option>
                                            </select>
                                        </div> -->

                                        <div class="col-md-12" id="credit-point-rules-section" @if($exam->exam_type == 2) style="display:block;" @else style="display:none;" @endif>
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
                                                                    @if(isset($examCreditPointRulesData['submission_on_time']) && $examCreditPointRulesData['submission_on_time'] == 'yes')
                                                                        checked
                                                                    @elseif(empty($examCreditPointRulesData['submission_on_time']) && App\Helpers\Helper::getGlobalConfiguration("submission_on_time") == 'yes')
                                                                        checked
                                                                    @endif
                                                                    ><span>{{__('languages.question_generators_menu.yes')}}</span>
                                                                    <input type="radio" name="submission_on_time" value="no"
                                                                    @if(isset($examCreditPointRulesData['submission_on_time']) &&  $examCreditPointRulesData['submission_on_time'] == 'no')
                                                                        checked
                                                                    @elseif(empty($examCreditPointRulesData['submission_on_time']) && App\Helpers\Helper::getGlobalConfiguration("submission_on_time") == 'no')
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
                                                                    @if(isset($examCreditPointRulesData['credit_points_of_accuracy']) &&  $examCreditPointRulesData['credit_points_of_accuracy'] == 'yes')
                                                                        checked
                                                                    @elseif(empty($examCreditPointRulesData['credit_points_of_accuracy']) && App\Helpers\Helper::getGlobalConfiguration("credit_points_of_accuracy") == 'yes')
                                                                        checked
                                                                    @endif
                                                                    >{{__('languages.question_generators_menu.yes')}}
                                                                    <input type="radio" name="credit_points_of_accuracy" value="no"
                                                                    @if(isset($examCreditPointRulesData['credit_points_of_accuracy']) &&  $examCreditPointRulesData['credit_points_of_accuracy'] == 'no')
                                                                        checked
                                                                    @elseif(empty($examCreditPointRulesData['credit_points_of_accuracy']) && App\Helpers\Helper::getGlobalConfiguration("credit_points_of_accuracy") == 'no' )
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
                                                                    @if(isset($examCreditPointRulesData['credit_points_of_normalized_ability']) &&  $examCreditPointRulesData['credit_points_of_normalized_ability'] == 'yes')
                                                                        checked
                                                                    @elseif(empty($examCreditPointRulesData['credit_points_of_normalized_ability']) && App\Helpers\Helper::getGlobalConfiguration("credit_points_of_normalized_ability") == 'yes')
                                                                        checked
                                                                    @endif
                                                                    >{{__('languages.question_generators_menu.yes')}}
                                                                    <input type="radio" name="credit_points_of_normalized_ability" value="no" 
                                                                    @if(isset($examCreditPointRulesData['credit_points_of_normalized_ability']) &&  $examCreditPointRulesData['credit_points_of_normalized_ability'] == 'no')
                                                                        checked
                                                                    @elseif(empty($examCreditPointRulesData['credit_points_of_normalized_ability']) && App\Helpers\Helper::getGlobalConfiguration("credit_points_of_normalized_ability") == 'no' )
                                                                        checked
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
                                    <div class="form-row select-data">
                                        <div class="sm-btn-sec form-row">
                                            <div class="form-group mb-50 btn-sec">
                                                <button type="button" class="blue-btn btn btn-primary next-button next_btn_step_1" data-stepid="1">{{__('languages.question_generators_menu.next')}}</button>                                                
                                            </div>
                                        </div>
                                    </div>
                                </section>

                                @php
                                $existingSchoolIds = ($exam->school_id) ? explode(',',$exam->school_id) : [];
                                
                                $QuestionGeneratorController = new App\Http\Controllers\QuestionGeneratorController;
                                $existingPeerGroupIds = $QuestionGeneratorController->GetCurrentSchoolAssignedTestPeerGroups($exam->id);

                                if(isset($existingPeerGroupIds) && !empty($existingPeerGroupIds)){
                                    $existingStudentIds = [];
                                    $studentGradeData = [];
                                    $studentClassData = [];
                                }else{
                                    $existingStudentIds = $QuestionGeneratorController->GetCurrentSchoolAssignedStudentsList($exam->id);
                                }
                                $studentClassData = ($examClassIds) ? $examClassIds : [];
                                @endphp
                                <section class="form-steps step2" style="display:none">
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
                                                                <input type="checkbox"  name="grades[]" value="{{$grade->id}}"  @if(in_array($grade->id,$studentGradeData)) checked @endif class="question-generator-grade-chkbox">{{$grade->name}}
                                                            </div>
                                                        </div>
                                                        @if(!empty($grade->classes))
                                                        <div class="form-grade-sub-option">
                                                            <div class="form-grade-sub-single-option">
                                                                @foreach($grade->classes as $classes)
                                                                <input type="checkbox" name="classes[{{$grade->id}}][]" value="{{$classes->id}}"   data-label="{{$grade->name}}{{$classes->name}}" class="question-generator-class-chkbox" @if(in_array($classes->id,$studentClassData)) checked @endif>
                                                                <label>{{$grade->name}}{{$classes->name}}</label>
                                                                @if(in_array($classes->id,$studentClassData))
                                                                    <input type="hidden" name="oldClasses[{{$grade->id}}][]" value="{{$classes->id}}">
                                                                @endif
                                                                @endforeach
                                                            </div>
                                                        </div>
                                                        @endif
                                                    </div>
                                                    @endforeach
                                                    @endif
                                                </div>
                                            </div>

                                            <div class="grade-class-date-time-list clearfix clearfix float-left"></div>
                                            <div class="form-group student_list_section mt-3 row">
                                                <div class="student_list_heading col-lg-3">
                                                    <label>{{__('languages.question_generators_menu.select_individual_students')}}</label>
                                                </div>
                                                <div class="student_list_option col-lg-3">
                                                    @if(isset($StudentList) && !empty($StudentList))
                                                    <select name="studentIds[]" class="form-control select-option" id="question-generator-student-id" multiple @if(!empty($existingPeerGroupIds)) disabled @endif @if(isset($StudentList) && empty($StudentList)) disabled @endif>
                                                    @foreach($StudentList as $student)
                                                        <option value="{{$student->id}}"  @if(in_array($student->id,$existingStudentIds)) selected="selected" @endif >
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
                                                    <label>{{__('languages.question_generators_menu.student_peer_groups')}}</label>
                                                </div>
                                                <div class="student_peer_group_option col-lg-3">
                                                    <select class="select-option form-control" data-show-subtext="true" data-live-search="true" name="peerGroupIds[]" id="question-generator-peer-group-options" multiple @if(!empty($existingStudentIds)) disabled @endif>
                                                        @if($PeerGroupList)
                                                            @foreach($PeerGroupList as $peerGroup)
                                                                <option value="{{$peerGroup->id}}"  @if(in_array($peerGroup->id,$existingPeerGroupIds)) selected @endif  data-label="{{$peerGroup->PeerGroupName}}">{{$peerGroup->PeerGroupName}}</option>
                                                            @endforeach
                                                        @endif
                                                    </select>
                                                </div>

                                                <div class="col-md-12 group-date-time-list mt-3"></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-row select-data">
                                        <div class="sm-btn-sec form-row">
                                            <div class="form-group mb-50 btn-sec">
                                                <button type="button" class="blue-btn btn btn-primary previous-button previous_btn_step_2" data-stepid="2">{{__('languages.question_generators_menu.previous')}}</button>
                                                <button type="button" class="blue-btn btn btn-primary next-button next_btn_step_2" data-stepid="2">{{__('languages.question_generators_menu.next')}}</button>
                                            </div>
                                        </div>
                                    </div>
                                </section>

                                <section class="form-steps step3" style="display:none;">
                                    <input type="hidden" name="questionIds" value="" id="questionIds">
                                    <div class="form-row">
                                        <div class="form-group col-md-6 mb-50">
                                            <label>{{__('languages.upload_document.strands')}}</label>
                                            <select name="strand_id[]" class="form-control select-option" id="strand_id" multiple {{ $fieldDisabled }} >
                                                @if(isset($strandsList) && !empty($strandsList))
                                                    @foreach ($strandsList as $strandKey => $strand)
                                                        <option value="{{ $strand->id }}" @if(in_array($strand->id,$SelectedStrands)) selected @endif>{{ $strand->{'name_'.app()->getLocale()} }}</option>
                                                    @endforeach
                                                @else
                                                    <option value="">{{__('languages.no_strands_available')}}</option>
                                                @endif
                                            </select>
                                        </div>
                                        <div class="form-group col-md-6 mb-50">
                                            <label>{{__('languages.upload_document.learning_units')}}</label>
                                            <select name="learning_unit_id[]" class="form-control select-option" id="learning_unit" multiple {{ $fieldDisabled }} >
                                                @if(isset($LearningUnits) && !empty($LearningUnits))
                                                    @foreach($LearningUnits as $learningUnitKey => $learningUnit)
                                                        <option value="{{ $learningUnit->id }}" @if(in_array($learningUnit->id,$SelectedLearningUnit)) selected @endif>{{ $learningUnit->{'name_'.app()->getLocale()} }}</option>
                                                    @endforeach
                                                @else
                                                    <option value="">{{__('languages.no_learning_units_available')}}</option>
                                                @endif
                                            </select>
                                            <label class="error learning_unit_error_msg"></label>
                                        </div>
                                        <div class="form-group col-md-6 mb-50">
                                            <label class="text-bold-600">{{__('languages.question_generators_menu.total_no_of_questions')}}</label>
                                            <input type="text" name="total_no_of_questions" id="total_no_of_questions" value="{{ sizeof(explode(',',$exam->question_ids)) }}" class="form-control" placeholder="Total number of questions" required readonly>
                                        </div>
                                    </div>
                                    <hr class="blue-line">
                                    <div class="form-row question-info">
                                        <p>{{__('languages.question_generators_menu.maximum_no_of_question_per_objective')}}: <strong>{{$RequiredQuestionPerSkill['maximum_question_per_skill']}}</strong></p>
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
                                            <input type="checkbox" name="all_learning_objective_checkbox" value="" class="all_learning_objective_checkbox" checked {{ $fieldDisabled }}> Select All
                                        </div>
                                        @foreach($LearningObjectives as $learningObjectivesKey => $learningObjectives)
                                        @php
                                            $noOfQuestionPerLearningObjective=App\Helpers\Helper::getNoOfQuestionPerLearningObjective($learningObjectives->learning_unit_id,$learningObjectives->id);
                                            $existingDifficultySelectd = ($exam->difficulty_levels) ? explode(',',$exam->difficulty_levels) : [];
                                            if(isset($learningObjectivesConfiguration[$learningObjectives->learning_unit_id]['learning_objective'][$learningObjectives->id]['learning_objectives_difficulty_level'])) {
                                                $existingDifficultySelectd = $learningObjectivesConfiguration[$learningObjectives->learning_unit_id]['learning_objective'][$learningObjectives->id]['learning_objectives_difficulty_level'];
                                            }
                                            $get_no_of_question_learning_objectives=$noOfQuestionPerLearningObjective;
                                            if(isset($learningObjectivesConfiguration[$learningObjectives->learning_unit_id]['learning_objective'][$learningObjectives->id]['get_no_of_question_learning_objectives'])) {
                                                $get_no_of_question_learning_objectives = $learningObjectivesConfiguration[$learningObjectives->learning_unit_id]['learning_objective'][$learningObjectives->id]['get_no_of_question_learning_objectives'];
                                            }
                                        @endphp
                                        <div class="selected-learning-objectives-difficulty">
                                            @if($fieldDisabled == 'disabled')
                                                @if(isset($learningObjectivesConfiguration[$learningObjectives->learning_unit_id]['learning_objective'][$learningObjectives->id]))
                                                    <input type="checkbox" name="learning_unit[{{$learningObjectives->learning_unit_id}}][learning_objective][{{ $learningObjectives->id }}]" value="{{ $learningObjectives->id }}" class="learning_objective_checkbox" @if(isset($learningObjectivesConfiguration[$learningObjectives->learning_unit_id]['learning_objective'][$learningObjectives->id])) checked @endif  {{ $fieldDisabled }} >
                                                    <label>{{ $learningObjectives->foci_number }} {{ $learningObjectives->{'title_'.app()->getLocale()} }}</label>
                                                    <select name="learning_unit[{{$learningObjectives->learning_unit_id}}][learning_objective][{{ $learningObjectives->id }}][learning_objectives_difficulty_level][]" class="form-control select-option learning_objectives_difficulty_level" multiple  {{ $fieldDisabled }} {{ ($exam->difficulty_mode=='auto' ? 'disabled' : '') }}>
                                                        <option value="1" @if(in_array(1,$existingDifficultySelectd)) selected @endif >1</option>
                                                        <option value="2" @if(in_array(2,$existingDifficultySelectd)) selected @endif >2</option>
                                                        <option value="3" @if(in_array(3,$existingDifficultySelectd)) selected @endif >3</option>
                                                        <option value="4" @if(in_array(4,$existingDifficultySelectd)) selected @endif >4</option>
                                                        <option value="5" @if(in_array(5,$existingDifficultySelectd)) selected @endif >5</option>
                                                    </select>
                                                    <input type="text" name="learning_unit[{{$learningObjectives->learning_unit_id}}][learning_objective][{{ $learningObjectives->id }}][get_no_of_question_learning_objectives]" value="{{ $get_no_of_question_learning_objectives }}" class="get_no_of_question_learning_objectives"  min="{{ $noOfQuestionPerLearningObjective }}" max="{{$RequiredQuestionPerSkill['maximum_question_per_skill']}}" {{ $fieldDisabled }} >
                                                @endif
                                            @else
                                                <input type="checkbox" name="learning_unit[{{$learningObjectives->learning_unit_id}}][learning_objective][{{ $learningObjectives->id }}]" value="{{ $learningObjectives->id }}" class="learning_objective_checkbox" @if(isset($learningObjectivesConfiguration[$learningObjectives->learning_unit_id]['learning_objective'][$learningObjectives->id])) checked @endif  {{ $fieldDisabled }} >
                                                <label>{{ $learningObjectives->foci_number }} {{ $learningObjectives->{'title_'.app()->getLocale()} }}</label>
                                                <select name="learning_unit[{{$learningObjectives->learning_unit_id}}][learning_objective][{{ $learningObjectives->id }}][learning_objectives_difficulty_level][]" class="form-control select-option learning_objectives_difficulty_level" multiple  {{ $fieldDisabled }} {{ ($exam->difficulty_mode=='auto' ? 'disabled' : '') }}>
                                                    <option value="1" @if(in_array(1,$existingDifficultySelectd)) selected @endif >1</option>
                                                    <option value="2" @if(in_array(2,$existingDifficultySelectd)) selected @endif >2</option>
                                                    <option value="3" @if(in_array(3,$existingDifficultySelectd)) selected @endif >3</option>
                                                    <option value="4" @if(in_array(4,$existingDifficultySelectd)) selected @endif >4</option>
                                                    <option value="5" @if(in_array(5,$existingDifficultySelectd)) selected @endif >5</option>
                                                </select>
                                                <input type="text" name="learning_unit[{{$learningObjectives->learning_unit_id}}][learning_objective][{{ $learningObjectives->id }}][get_no_of_question_learning_objectives]" value="{{ $get_no_of_question_learning_objectives }}" class="get_no_of_question_learning_objectives"  min="{{ $noOfQuestionPerLearningObjective }}" max="{{$RequiredQuestionPerSkill['maximum_question_per_skill']}}" {{ $fieldDisabled }} >
                                            @endif
                                        </div>
                                        @endforeach
                                        @endif
                                    </div>
                                    <div class="form-row select-data">
                                        <div class="sm-btn-sec form-row">
                                            <div class="form-group mb-50 btn-sec">
                                                <button type="button" class="blue-btn btn btn-primary previous-button previous_btn_step_3" data-stepid="3">{{__('languages.question_generators_menu.previous')}}</button>
                                                <button type="button" class="blue-btn btn btn-primary next-button next_btn_step_3" data-stepid="3">{{__('languages.question_generators_menu.next')}}</button>                                                
                                            </div>
                                        </div>
                                    </div>
                                </section>
                                
                                <section class="form-steps step4" style="display:none;">
                                    <div class="d-flex review-question-main tab-content-wrap w-100">
                                        {!! $questionListHtml !!}
                                    </div>
                                    <div class="form-row select-data float-left mt-2 clearfix">
                                        <div class="sm-btn-sec form-row">
                                            <div class="form-group mb-50 btn-sec">
                                                <button type="button" class="blue-btn btn btn-primary previous-button previous_btn_step_4" data-stepid="4">{{__('languages.question_generators_menu.previous')}}</button>
                                                <button type="submit" name="save_as_draft" value="save_as_draft" class="blue-btn btn btn-primary" data-stepid="4" value="save_as_draft">{{__('languages.question_generators_menu.save_as_draft')}}</button>
                                                <button type="submit" name="save_and_publish" value="save_and_publish" class="blue-btn btn btn-primary" data-stepid="4" value="save_and_publish">{{__('languages.question_generators_menu.save_and_publish')}}</button>
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
<!-- Want a hint Modal -->
    <div class="modal fade" id="WantAHintModal" tabindex="-1" aria-labelledby="WantAHintModal" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
            </div>
        </div>
    </div>
<!-- Want a hint Modal -->
<script>
var currentLanguage='{{app()->getLocale()}}';
var minimum_question_per_skill = parseInt('<?php echo $RequiredQuestionPerSkill['minimum_question_per_skill'];?>');
var maximum_question_per_skill = parseInt('<?php echo $RequiredQuestionPerSkill['maximum_question_per_skill'];?>');
var multiselectArray={
    nSelectedText: 'Selecciones',
    enableHTML: true,
    templates: {
        filter: '<li class="multiselect-item multiselect-filter"><div class="input-group mb-3"><div class="input-group-prepend"><span class="input-group-text"><i class="fa fa-search"></i></span></div><input class="form-control multiselect-search" type="text" /></div></li>',
        filterClearBtn: '<span class="input-group-btn"><button class="btn btn-default multiselect-clear-filter" type="button"><i class="fa fa-times"></i></button></span>'

    },
    column: 1,
    placeholder: "Select difficulty level",
    includeSelectAllOption: true,
    enableFiltering: true,
}

$(function (){
    $(document).on('change', '.get_no_of_question_learning_objectives', function(e) {
        /*var minimum_question_per_skill = parseInt('<?php echo $RequiredQuestionPerSkill['minimum_question_per_skill'];?>');
        var maximum_question_per_skill = parseInt('<?php echo $RequiredQuestionPerSkill['maximum_question_per_skill'];?>');*/
        var minimum_question_per_skill_single = parseInt($(this).attr('min'));
        var maximum_question_per_skill_single = parseInt($(this).attr('max'));
        var noOfQuestionEntered =parseInt(e.target.value);
        if(noOfQuestionEntered !=""){
            if(noOfQuestionEntered >= minimum_question_per_skill_single && noOfQuestionEntered <= maximum_question_per_skill_single) {
            }else{
                toastr.error("Minimum question per skill required is :"+minimum_question_per_skill_single+" Maximum question per skill required is :"+maximum_question_per_skill_single);
                $(this).val('');
            }
        }
    });

    if($('#difficulty_mode').val() == 'manual'){
        // Set default difficulty level for selected first steps
        //setDefaultDifficultyLevels();
    }
    
    /**
     * USE : Set start time picker
     */
    $('#start_time').timepicker({
        'showDuration': true,
        'timeFormat': 'H:i',
        'step': 60
    });

    /**
     * USE : Set End time picker
     */
    $('#end_time').timepicker({
        'showDuration': true,
        'timeFormat': 'H:i',
        'step': 60
    });

    // event fire on click step button
    $(document).on('click', '.step-headings', function() {
        var currentStep = parseInt($(this).attr('data-tabid'))-1;
        if(currentStep!=0)
        {
            $(".form-steps.step"+currentStep+" .next-button").click();
        }
    });

    // event fire on click next button
    $(document).on('click', '.next-button', function() {
        var currentStep = $(this).attr('data-stepid');
        var nextStep = (parseInt(currentStep) + 1);
        if(checkValidation(currentStep)){
            $('.form-steps').hide();
            $('.step-headings').removeClass('tab_active');
            $('.section-step'+nextStep).addClass('tab_active');
            $('.step'+nextStep).show();
            if(currentStep == 3 && $('.form-steps.step1 .error').length==0 && $('.form-steps.step2 .error').length==0){
                @if($fieldDisabled!='disabled')
                    getLearningObjectivesOptionList();
                @endif
            }
        }else{
            setTimeout(function () {
                $('.step-headings').removeClass('tab_active');
                $('.section-step'+currentStep).addClass('tab_active');
                $('.section-step'+currentStep).click();
            },200)
        }
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

    /**
     * USE : Hide and show some option based on change difficulty mode
     */
    $(document).on('change', '#difficulty_mode', function(e) {
        if($(this).val() == 'manual'){
            $('#difficulty_lvl').multiselect('enable');
            // Set default difficulty level for selected first steps
            setDefaultDifficultyLevels();
            $(".learning_objective_checkbox").parent().find('select').multiselect('disable');
            $(".learning_objective_checkbox:checked").parent().find('select').multiselect('enable');
            $(".learning_objective_checkbox").parent().find('.btn-group').css('visibility','unset');
            $(".question-generator-objectives-labels label:eq(1)").show();
        }else{
            // if auto mode then hide selection for difficulty level option
            $("#difficulty_lvl").val('').multiselect("rebuild").multiselect('disable');
            
            $(".learning_objective_checkbox").parent().find('select').multiselect('disable');
            $(".learning_objective_checkbox").parent().find('.btn-group').css('visibility','hidden');
            $(".question-generator-objectives-labels label:eq(1)").hide();
        }
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
            $('#credit-point-rules-section').show();
        }else{
            $('#no_of_trials_per_question_section').hide();
            $('#display_hints_section').hide();
            $("#randomize_answer select").val("no").change();
            $("#randomize_order select").val("no").change();
            $('#select-randomize-answers').val('yes').select2().trigger('change');
            $('#select-randomize-order').val('yes').select2().trigger('change');
            $('#credit-point-rules-section').hide();
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
	$(document).on('change', '#strand_id,#refresh-question-strand-id', function() {
        $("#cover-spin").show();
        var classNameLearningUnit='#learning_unit';
        if($(this).attr('id')=='refresh-question-strand-id')
        {
            classNameLearningUnit='#refresh-question-learning-unit';
        }
		$strandIds = $(this).val();
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
								$(classNameLearningUnit).append(option);
							});
						}else{
							$(classNameLearningUnit).html('<option value="">'+LEARNING_UNITS_NOT_AVAILABLE+'</option>');
						}
					}else{
						$(classNameLearningUnit).html('<option value="">'+LEARNING_UNITS_NOT_AVAILABLE+'</option>');
					}
					$(classNameLearningUnit).multiselect("rebuild");
					$(classNameLearningUnit).trigger("change");
				},
				error: function(response) {
					ErrorHandlingMessage(response);
				}
			});
		}else{
            $("#cover-spin").hide();
			$('#learning_unit, #learning_objectives').html('');
			$('#learning_unit, #learning_objectives').multiselect("rebuild");
		}        
	});

    /**
     * USE : Default select all learning objectives wise difficulty levels
     */
    $(document).on('change', '#difficulty_lvl', function(){
        if($('#difficulty_mode').val() == 'manual'){
            // Set default difficulty level for selected first steps
            setDefaultDifficultyLevels();
        }
    });
    /**
     * USE : On change start Date end time Manage if Start time is Selected
     */
     $(document).on('change', '.date-picker', function(){
        $startDate = $(this).val();
        $endDate = $('.end-date-picker').val();
        $startTime = $('#test_start_time').val();
        if($startTime != ''){
            if($startDate != $endDate){
                var endOptionSelectedStartTimeIndex = $("#test_start_time option").index();
                var selectedStartTimeIndex = $("#test_start_time").selectedIndex;
                $("#test_end_time option").removeAttr("disabled"); 
            }else{
                var selectedStartTimeIndex = $("#test_start_time").prop('selectedIndex');
                $.each($("#test_end_time option"), function(index,element){
                    var endOptionSelectedStartTimeIndex = index;
                    if(endOptionSelectedStartTimeIndex <= selectedStartTimeIndex){
                        $(this).attr("disabled", "disabled");
                    }
                });
            }
        }
    });
	/**
	 * USE : Get Multiple Learning units based on multiple learning units ids
	 * **/
	$(document).on('change', '#learning_unit', function() {
        $("#cover-spin").show();
        $('.learning_unit_error_msg').text('');
        $('.selection-learning-objectives-section').html('');
		$strandIds = $('#strand_id').val();
		$learningUnitIds = $('#learning_unit').val();
		if($learningUnitIds != ""){
			$.ajax({
				url: BASE_URL + '/getLearningObjectivesFromMultipleLearningUnitsInGenerateQuestions',
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
					var data = JSON.parse(JSON.stringify(response));
                    if(data){
                        var html = '';
						if(data.data.LearningObjectives){
                            html += '<div class="selected-learning-objectives-difficulty">\
                                        <input type="checkbox" name="all_learning_objective_checkbox" value="" class="all_learning_objective_checkbox" checked> Select All\
                                    </div>';
							$(data.data.LearningObjectives).each(function() {
                                var learningObjectivesTitle=eval('this.title_'+currentLanguage);
                                html += '<div class="selected-learning-objectives-difficulty">\
                                            <input type="checkbox" name="learning_unit['+this.learning_unit_id+'][learning_objective]['+this.id+']" value="'+this.learning_unit_id+'" class="learning_objective_checkbox" checked>\
                                            <label>'+this.foci_number+' '+learningObjectivesTitle+'</label>';
                                            if($('#difficulty_mode').val() == 'manual'){
                                            html += '<select name="learning_unit['+this.learning_unit_id+'][learning_objective]['+this.id+'][learning_objectives_difficulty_level][]" class="form-control select-option learning_objectives_difficulty_level" multiple>\
                                                        <option value="1">1</option>\
                                                        <option value="2">2</option>\
                                                        <option value="3">3</option>\
                                                        <option value="4">4</option>\
                                                        <option value="5">5</option>\
                                                    </select>';
                                            }
                                            html += '<input type="text" name="learning_unit['+this.learning_unit_id+'][learning_objective]['+this.id+'][get_no_of_question_learning_objectives]" value="'+data.data.getNoOfQuestionPerLearningObjective[this.id]+'" class="get_no_of_question_learning_objectives" min="'+data.data.getNoOfQuestionPerLearningObjective[this.id]+'" max="'+maximum_question_per_skill+'">\
                                        </div>';
							});
                            $('.selection-learning-objectives-section').html(html);
                            if($('#difficulty_mode').val() == 'manual'){
                                // Set default difficulty level for selected first steps
                                setDefaultDifficultyLevels();
                            }
						}else{
							$('.selection-learning-objectives-section').html(LEARNING_OBJECTIVES_NOT_AVAILABLE);
						}
					}else{
						$('.selection-learning-objectives-section').html(LEARNING_OBJECTIVES_NOT_AVAILABLE);
					}
                    $('.learning_objectives_difficulty_level').multiselect(multiselectArray);

                    total_no_of_questions();
                    $("#cover-spin").hide();
				},
				error: function(response) {
					ErrorHandlingMessage(response);
				}
			});
		}else{
            $("#cover-spin").hide();
            $('.learning_unit_error_msg').text('Please select learning objectives');
			$('#learning_objectives').html('');
			$('#learning_objectives').multiselect("rebuild");
            total_no_of_questions();
		}
	});

    /**
     * USE : Hide and show custom report date on change select report date
     */
    $(document).on('change', '#select-report-date', function() { 
        $(".custom_date").hide();
        if($(this).val() == 'custom_date'){
            $(".custom_date").show();
        }
    });

   /**
    * USE : On click event click on the grade checkbox
    */
    $(document).on('click', '.question-generator-grade-chkbox', function(){
        if(!$(this).is(":checked")) {
            $(this).closest('.form-grade-select').find('.question-generator-class-chkbox').prop('checked',false);
        }
        var GradeIds = [];
        $('.question-generator-grade-chkbox').each(function(){
            if($(this).is(":checked")) {
                $(this).closest('.form-grade-select').find('.question-generator-class-chkbox').prop('checked',true);
                GradeIds.push($(this).val());
            }
        });
        var ClassIds = [];
        $('.question-generator-class-chkbox').each(function(){
            if($(this).is(":checked")) {
                ClassIds.push($(this).val());
            }
        });

        // Function call to get student list
        getStudents(GradeIds,ClassIds);
        $(".group-date-time-list").html('');
        setGradeClassDateTimeList();
    });

    /**
    * USE : On click event click on the class checkbox
    */
    $(document).on('click', '.question-generator-class-chkbox', function(){
        var classCheckedLength = $(this).closest('.form-grade-select').find('.question-generator-class-chkbox:checked').length;
        var classLength = $(this).closest('.form-grade-select').find('.question-generator-class-chkbox').length;
        if(classCheckedLength == 0){            
            $(this).closest('.form-grade-select').find('.question-generator-grade-chkbox').prop('checked',false);
        }

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
        $(".group-date-time-list").html('');
        setGradeClassDateTimeList();
    });

    /**
     * USE : On change start time event disabled end time before options
     */
    // $(document).on('change', '#test_start_time', function(){
    //     var selectedStartTimeIndex = this.selectedIndex;
    //     $.each($("#test_end_time option"), function(){
    //         var endOptionSelectedStartTimeIndex = $(this).index();
    //         if(endOptionSelectedStartTimeIndex <= selectedStartTimeIndex){
    //             $(this).attr("disabled", "disabled");
    //         }else{
    //             $(this).removeAttr("disabled");
    //         }
    //     });
    //     $('#test_end_time').val('').select2().trigger('change');
    // });

    /**
     * USE : On click on the select all learning objectives events
     */
    $(document).on("click", ".all_learning_objective_checkbox", function (){
        $("#cover-spin").show();
		if($(this).is(":checked")){
            $(".learning_objective_checkbox").each(function () {
                $(this).prop('checked', true);
            });
		}else{
			$(".learning_objective_checkbox").each(function () {
                $(this).prop('checked', false);
            });
		}
        $(".learning_objective_checkbox").parent().find('.get_no_of_question_learning_objectives').prop('disabled',false);
        $(".learning_objective_checkbox:not(:checked)").parent().find('select,.get_no_of_question_learning_objectives').prop('disabled',true);
        if($('#difficulty_mode').val() == 'manual'){
           $(".learning_objective_checkbox:checked").parent().find('select').multiselect('enable');
        }
        $(".learning_objective_checkbox:not(:checked)").parent().find('select').multiselect('disable');
         total_no_of_questions();
        $("#cover-spin").hide();
	});

    /**
     * USE : On click on the select learning objectives events
     */
    $(document).on("click", ".learning_objective_checkbox", function (){
        $(".learning_objective_checkbox").parent().find('.get_no_of_question_learning_objectives').prop('disabled',false);
        $(".learning_objective_checkbox:not(:checked)").parent().find('select,.get_no_of_question_learning_objectives').prop('disabled',true);
        if($('#difficulty_mode').val() == 'manual'){
            $(".learning_objective_checkbox:checked").parent().find('select').multiselect('enable');
        }
        $(".learning_objective_checkbox:not(:checked)").parent().find('select').multiselect('disable');
        total_no_of_questions();
    });
});

/**
 * USE : Set default difficulty level set in based on selected in first steps
 */
function setDefaultDifficultyLevels(){
    var difficultyLevels = $('#difficulty_lvl').val();
    if(difficultyLevels.length){
        $('.learning_objectives_difficulty_level').each(function(){
            $(this).val(difficultyLevels).change().multiselect(multiselectArray).multiselect('rebuild');
        });
    }
}

/**
 * USE : Get the questions from AIAPi
 */

function getLearningObjectivesOptionList(){
    $('.form-steps.step4 #pills-tab').html('');
    $('.form-steps.step4 #pills-tabContent').html('');
    $(".review-question-main").html('');
    $('.form-steps.step4 .tab-content-wrap .error').remove();
    $("#cover-spin").show();
    var formData=$( "#question-generator" ).serialize()
    $.ajax({
        url: BASE_URL + '/question-generator/get-questions-id-learning-objectives-school',
        type: 'POST',
        data:formData+'&exam_id='+{{$exam->id}},
        success: function(response) {
            $("#question-generator button[type=submit]").prop('disabled',false);
            var response = JSON.parse(JSON.stringify(response));
            if(response.data){
                var qLength = Object.keys(response.data.questionIds).length;
                var total_no_of_questions = parseInt($("#total_no_of_questions").val());
                if(qLength < total_no_of_questions){
                    toastr.warning("Not enough questions into some objectives");
                }
                // Set input hidden into question ids
                $('#questionIds').val(response.data.questionIds);
                $(".review-question-main").html(response.data.html);
                MathJax.Hub.Queue(["Typeset",MathJax.Hub]);
                $('#cover-spin').hide();
            }else{
                $('.form-steps').hide();
                $('.step-headings').removeClass('tab_active');
                $('.section-step3').addClass('tab_active');
                $('.step3').show();
            }
        },
        error: function(response) {
            ErrorHandlingMessage(response);
            $('.form-steps.step4 .tab-content-wrap').append('<label class="error">Please Reselect  Select Question Configuration</label>')
            $("#question-generator button[type=submit]").prop('disabled',true);
            $('.form-steps').hide();
            $('.step-headings').removeClass('tab_active');
            $('.section-step3').addClass('tab_active');
            $('.step3').show();
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
    $('#question-generator-student-id').prop('disabled',false);
    $('#question-generator-student-id').html('');
    if(gradeIds.length==0 && classIds.length==0){
        $('#question-generator-student-id').html('');
        $("#question-generator-student-id").multiselect("rebuild");
        $("#cover-spin").hide();
        return null;
    }
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
                $("#question-generator-student-id").find('option').attr('selected','selected');
                $("#question-generator-student-id").multiselect("rebuild");
            }
        },
        error: function(response) {
            ErrorHandlingMessage(response);
        }
    });
    $("#cover-spin").hide();
}

function questionsReindex() {
    var qIndex=1;
    $("#pills-tab li .nav-link").each(function () {
        $(this).text('Q'+qIndex);
        qIndex++;
    })
}
$(document).ready(function () {
    $(".btn-up").click(function () {
        var $current = $("#pills-tab li .nav-link.active").closest('li');
        var $previous = $current.prev('li');
        if($previous.length !== 0){
            $current.insertBefore($previous);
        }
        questionsReindex();
    });
    $(".btn-down").click(function(){
      var $current = $("#pills-tab li .nav-link.active").closest('li');
      var $next = $current.next('li');
      if($next.length !== 0){
        $current.insertAfter($next);
      }
        questionsReindex();
    });
    $(".set-top").click(function () {
        var $current = $("#pills-tab li .nav-link.active").closest('li');
        var $previous = $("#pills-tab li:eq(0)");
        if($previous.length !== 0){
            $current.insertBefore($previous);
        }
        questionsReindex();
    });
    $(".set-bottom").click(function () {
        var $current = $("#pills-tab li .nav-link.active").closest('li');
        var $previous = $("#pills-tab li").last();
        if($previous.length !== 0){
            $current.insertAfter($previous);
        }
        questionsReindex();
    });
    $(".btn-remove-tab").click(function () {
        $("#pills-tab li .nav-link.active").closest('li').remove();
        $("#pills-tabContent .tab-pane.fade.show.active").remove();
        setTimeout(function () {
            $(document).find("#pills-tab li:eq(0) a").click();
        },200);
        
    });
    $(document).on("change",".get_no_of_question_learning_objectives",function () {
        if($.trim($(this).val())=="" || $.trim($(this).val())==0){
            var minimum_question_per_skill = $(this).attr('min');
            $(this).val(minimum_question_per_skill);
        }
       total_no_of_questions();
    });

    $(document).on("click",".want_a_hint",function () {

        $("#cover-spin").show();
        var qId=$(this).attr('question-id');
        $('#WantAHintModal .modal-content').html('');
        $.ajax({
            url: BASE_URL + '/get-question-hint/'+qId,
            type: 'GET',
            success: function(response) {
                $("#cover-spin").hide();
                $('#WantAHintModal .modal-content').html(response.data.html);
                $('#WantAHintModal').modal('show');
            }
        });
    });

    $(document).on("click",".refresh-question-data",function () {
        var refresh_question_strand_id=$('#refresh-question-strand-id').val();
        var refresh_question_learning_unit=$('#refresh-question-learning-unit').val();
        var refresh_question_learning_objectives=$('#refresh-question-learning-objectives').val();
        var refresh_question_difficulty_level=$('#refresh_question_difficulty_level').val();
        var refresh_question_difficulty_mode=$('#difficulty_mode').val();
        var refresh_question_test_type=$('#test_type').val();
        if(refresh_question_strand_id.length!=0 && refresh_question_learning_unit.length!=0 && refresh_question_learning_objectives.length!=0 && refresh_question_difficulty_level.length!=0 && refresh_question_difficulty_mode.length!=0){
            $(".refresh-question").hide();
            $("#cover-spin").show();
            $.ajax({
                url: BASE_URL + '/get-refresh-question',
                type: 'POST',
                data: {
                    '_token': $('meta[name="csrf-token"]').attr('content'),
                    'strands_ids': refresh_question_strand_id,
                    'learning_unit_id': refresh_question_learning_unit,
                    'learning_objectives_id': refresh_question_learning_objectives,
                    'dificulty_level':refresh_question_difficulty_level,
                    'difficulty_mode':refresh_question_difficulty_mode,
                    'test_type':refresh_question_test_type
                },
                success: function(response) {
                    if(response.data.question_list.length!=0){
                        var Q=response.data.question_list[0];

                        var questionTitle=eval('Q.question_'+currentLanguage);
                        var answer1Title=eval('Q.answers.answer1_'+currentLanguage);
                        var answer2Title=eval('Q.answers.answer2_'+currentLanguage);
                        var answer3Title=eval('Q.answers.answer3_'+currentLanguage);
                        var answer4Title=eval('Q.answers.answer4_'+currentLanguage);
                        var difficultyLevelName=eval('Q.pre_configuration_difficulty_level.difficulty_level_name_'+currentLanguage);

                        $("#refresh-question-id").val(Q.id);
                        $(".refresh-question .question-strand-detail span").text(Q.objective_mapping.strandName);
                        $(".refresh-question .question-learning-unit-detail span").text(Q.objective_mapping.learningUnitsName);
                        $(".refresh-question .question-learning-objectives-detail span").text(Q.objective_mapping.learningObjectivesTitle);
                        $(".refresh-question .question-difficulty-detail span").text(difficultyLevelName);
                        $(".refresh-question .question_content label").html(questionTitle);
                        $(".refresh-question .answer-content .answer-review:eq(0) .review-answer-detail p").html(answer1Title);
                        $(".refresh-question .answer-content .answer-review:eq(1) .review-answer-detail p").html(answer2Title);
                        $(".refresh-question .answer-content .answer-review:eq(2) .review-answer-detail p").html(answer3Title);
                        $(".refresh-question .answer-content .answer-review:eq(3) .review-answer-detail p").html(answer4Title);
                        $(".refresh-question .want_a_hint").attr("question-id",Q.id);
                    }
                    $(".refresh-question").show();
                    //updateMathHtmlById("pills-tabContent");
                    MathJax.Hub.Queue(["Typeset",MathJax.Hub]);
                    $("#cover-spin").hide();
                },
                error: function(response) {
                    ErrorHandlingMessage(response);
                }
            });
        }else{
            toastr.error(SELECT_REFRESH_QUESTION_CONFIGURATION);
        }
    });
    // this click to get Refresh Question and Replace Question in active Question 
    $(document).on("click",".replace-question",function () {
        var strandName=$(".refresh-question .question-strand-detail span").text();
        var learningUnitsName=$(".refresh-question .question-learning-unit-detail span").text();
        var learningObjectivesTitle=$(".refresh-question .question-learning-objectives-detail span").text();
        var difficulty_level_name_en=$(".refresh-question .question-difficulty-detail span").text();
        var question_en=$(".refresh-question .question_content label").html();
        var answer1_en=$(".refresh-question .answer-content .answer-review:eq(0) .review-answer-detail").html();
        var answer2_en=$(".refresh-question .answer-content .answer-review:eq(1) .review-answer-detail").html();
        var answer3_en=$(".refresh-question .answer-content .answer-review:eq(2) .review-answer-detail").html();
        var answer4_en=$(".refresh-question .answer-content .answer-review:eq(3) .review-answer-detail").html();
        var refresh_question_id=$("#refresh-question-id").val();
        if($("#pills-tab li input[value="+refresh_question_id+"]").length==0){
            $(".review-question-tab .tab-pane.show.active .q-strand-name").text(strandName);
            $(".review-question-tab .tab-pane.show.active .q-learning-units-name").text(learningUnitsName);
            $(".review-question-tab .tab-pane.show.active .q-learning-objectives-title").text(learningObjectivesTitle);
            $(".review-question-tab .tab-pane.show.active .q-difficulty-level").text(difficulty_level_name_en);

            $(".review-question-tab .tab-pane.show.active .question_content label").html(question_en);
            $(".review-question-tab .tab-pane.show.active .answer-content .answer-review:eq(0) .review-answer-detail").html(answer1_en);
            $(".review-question-tab .tab-pane.show.active .answer-content .answer-review:eq(1) .review-answer-detail").html(answer2_en);
            $(".review-question-tab .tab-pane.show.active .answer-content .answer-review:eq(2) .review-answer-detail").html(answer3_en);
            $(".review-question-tab .tab-pane.show.active .answer-content .answer-review:eq(3) .review-answer-detail").html(answer4_en);
            $("#pills-tab li .nav-link.active").parent().find('input').val(refresh_question_id)
            $(".want_a_hint").attr("question-id",refresh_question_id);
        }else{
            toastr.error("This Question Already Exits");
        }
    });

    // this click to get Refresh Question and add Question in Question list
    $(document).on("click",".add-question",function () {
        var strandName=$(".refresh-question .question-strand-detail span").text();
        var learningUnitsName=$(".refresh-question .question-learning-unit-detail span").text();
        var learningObjectivesTitle=$(".refresh-question .question-learning-objectives-detail span").text();
        var difficulty_level_name_en=$(".refresh-question .question-difficulty-detail span").text();
        var question_en=$(".refresh-question .question_content label").html();
        var answer1_en=$(".refresh-question .answer-content .answer-review:eq(0) .review-answer-detail").html();
        var answer2_en=$(".refresh-question .answer-content .answer-review:eq(1) .review-answer-detail").html();
        var answer3_en=$(".refresh-question .answer-content .answer-review:eq(2) .review-answer-detail").html();
        var answer4_en=$(".refresh-question .answer-content .answer-review:eq(3) .review-answer-detail").html();
        var refresh_question_id=$("#refresh-question-id").val();
        if($("#pills-tab li input[value="+refresh_question_id+"]").length==0){
            var tab_left='';
            var tab_right='';
            var tab_active='';
            var tab_active_contact='';

            var getIndex=$("#pills-tab li").last().find('a').attr('aria-controls');
            var qIndex=parseInt(getIndex.replace('pills-contact-',''));
            qIndex=qIndex+1;

            tab_left+='<li class="nav-item">\
                <input type="hidden" name="qIndex[]" value="'+refresh_question_id+'" />\
                <a class="nav-link '+tab_active+'" id="pills-contact-tab-'+qIndex+'" data-toggle="pill" href="#pills-contact-'+qIndex+'" role="tab" aria-controls="pills-contact-'+qIndex+'" aria-selected="false">Q'+qIndex+'</a>\
            </li>';
            tab_right+='<div class="tab-pane fade '+tab_active_contact+'" id="pills-contact-'+qIndex+'" role="tabpanel" aria-labelledby="pills-contact-tab-'+qIndex+'">\
                <div class="d-flex pb-3">\
                <div class="question-content pl-2">\
                    <div class="row">\
                        <div class="col-md-6"><b>Strand</b> : <span class="q-strand-name">'+strandName+'</span></div>\
                        <div class="col-md-6"><b>Learning Units</b> : <span class="q-learning-units-name">'+learningUnitsName+'</span></div>\
                        <div class="col-md-6"><b>Learning Objectives</b> : <span class="q-learning-objectives-title">'+learningObjectivesTitle+'</span></div>\
                        <div class="col-md-6"><b>Difficulty Level</b> : <span class="q-difficulty-level">'+difficulty_level_name_en+'</span></div>\
                    </div>\
                    <p class="question-title review-question-title">Question:</p>\
                    <div class="question-answer-content pl-2">\
                        <div class="d-flex question_content">\
                            <label for="question-content">'+question_en+'</label>\
                        </div>\
                        <div class="answer-content">\
                            <div class="answer-review">\
                                <span class="answer-detail-number">1</span>\
                                <div class="review-answer-detail">'+answer1_en+'</div>\
                            </div>\
                            <div class="answer-review">\
                                <span class="answer-detail-number">2</span>\
                                <div class="review-answer-detail">'+answer2_en+'</div>\
                            </div>\
                            <div class="answer-review">\
                                <span class="answer-detail-number">3</span>\
                                <div class="review-answer-detail">'+answer3_en+'</div>\
                            </div>\
                            <div class="answer-review">\
                                <span class="answer-detail-number">4</span>\
                                <div class="review-answer-detail">'+answer4_en+'</div>\
                            </div>\
                        </div>\
                    </div>\
                    <div class="d-flex align-items-center float-left mt-5">\
                        <button type="button" class="btn-search bg-pink want_a_hint" question-id="'+refresh_question_id+'">{{__('languages.my_studies.want_a_hint')}}</button>\
                    </div>\
                </div>\
                </div>\
            </div>';
            $('.form-steps.step4 #pills-tab li a.nav-link.active').parent().after(tab_left);
            $('.form-steps.step4 #pills-tabContent .tab-pane.fade.active').after(tab_right);
            questionsReindex();
        }else{
            toastr.error(THIS_QUESTION_ALREADY_EXISTS);
        }
    });

    // this click to get Strands, Learning Units, Learning Objectives and Difficulty Level set in Refresh Question
    $(document).on("click","#pills-tab li a",function () {
        var getIndex=$(this).attr('aria-controls');
        var qIndex=parseInt(getIndex.replace('pills-contact-',''));
        var qId=qIndex;
        var q_strand_id=$("#pills-tabContent #pills-contact-"+qId).find('.q-strand-name').attr('data-q-strand-id');
        var q_learning_units_id=$("#pills-tabContent #pills-contact-"+qId).find('.q-learning-units-name').attr('data-q-learning-units-id');
        var q_learning_objectives_id=$("#pills-tabContent #pills-contact-"+qId).find('.q-learning-objectives-title').attr('data-q-learning-objectives-id');
        var q_difficulty_level_id=$("#pills-tabContent #pills-contact-"+qId).find('.q-difficulty-level').attr('data-q-difficulty-level-id');

        $("#refresh-question-strand-id").val(q_strand_id).multiselect('rebuild');
        $("#refresh-question-learning-unit").val(q_learning_units_id).multiselect('rebuild');
        $("#refresh-question-learning-objectives").val(q_learning_objectives_id).multiselect('rebuild');
        $("#refresh_question_difficulty_level").val(q_difficulty_level_id).multiselect('rebuild');
    });
    //$('.learning_objectives_difficulty_level').multiselect({includeSelectAllOption: true});
})

function checkValidation(currentStep) {
    var formIsValid = 0;
    $('.form-steps.step'+currentStep+' label.error').remove();
    switch('step_'+currentStep){
        case 'step_1':
            var addValid='';
            if($("#difficulty_mode").val()=='manual'){
                addValid=',[name="difficulty_lvl[]"]';
            }
            $('.form-steps.step'+currentStep).find('[name=use_of_modes],[name=test_type],[name=title],[name=start_date],[name=end_date]'+addValid).each(function(){
                var element = $(this).closest('.form-group').css('display');
                if($.trim($(this).val()) == '' && element != 'none' ){
                    var label = $(this).closest('.form-group').find('label:eq(0)').text();
                    $(this).closest('.form-group').append('<label class="error">'+PLEASE_ENTER +label+'</label>');
                    formIsValid++;
                }
            });
            break;
        case 'step_2':
            $('.form-steps.step'+currentStep).find('[name="studentIds[]"]').each(function(){
                var element = $(this).closest('.form-group').css('display');
                if($.trim($(this).val()) == '' && element != 'none'){
                    var label = $(this).closest('.form-group').find('label:eq(0)').text();
                    
                    formIsValid++;
                }
            });
            
            $('.form-steps.step'+currentStep).find('[name="peerGroupIds[]"]').each(function(){
                var element = $(this).closest('.form-group').css('display');
                if($.trim($(this).val()) == '' && element != 'none'){
                    var label = $(this).closest('.form-group').find('label:eq(0)').text();
                    
                    formIsValid++;
                }
            });
            if(formIsValid==2){
                $('.form-steps.step'+currentStep).find('[name="studentIds[]"]').parent().append('<label class="error">'+VALIDATIONS.PLEASE_SELECT_STUDENTS_OR_PEER_GROUP+' </label>');
                $('.form-steps.step'+currentStep).find('[name="peerGroupIds[]"]').parent().append('<label class="error w-100">'+VALIDATIONS.PLEASE_SELECT_STUDENTS_OR_PEER_GROUP+'</label>');
                formIsValid++;
            }else{
                formIsValid=0;
            }
            break;
        case 'step_3':
            break;
        case 'step_4':
            break;
        default:
    }
    // Check the steps all field is completed then next step open
    if(formIsValid == 0){
        return true;
    }else{
        return false;
    }
}
</script>
<script type="text/javascript">
    $(document).ready(function () {
        $('.question-generator-grade-chkbox').each(function(){
            var classCheckedLength=$(this).closest('.form-grade-select').find('.question-generator-class-chkbox:checked').length;
            var classLength=$(this).closest('.form-grade-select').find('.question-generator-class-chkbox').length;
            if(classLength==classCheckedLength && classCheckedLength != 0){
                $(this).prop('checked',true);
            }
        });
        @if($fieldDisabled!='disabled')
            $(".learning_objective_checkbox").parent().find('.get_no_of_question_learning_objectives').prop('disabled',false);
            $(".learning_objective_checkbox:not(:checked)").parent().find('select,.get_no_of_question_learning_objectives').prop('disabled',true);
            $(".learning_objective_checkbox:checked").parent().find('select').multiselect('enable');
            $(".learning_objective_checkbox:not(:checked)").parent().find('select').multiselect('disable');
             total_no_of_questions();
        @else
            $(".learning_objective_checkbox").parent().find('select').multiselect('disable');
        @endif

        @if($exam->difficulty_mode=='auto')
            $(".learning_objective_checkbox").parent().find('select').multiselect('disable');
            $(".learning_objective_checkbox").parent().find('.btn-group').css('visibility','hidden');
            $(".question-generator-objectives-labels label:eq(1)").hide();
        @endif

        $("#pills-tab li:eq(1) a").click();
        $("#pills-tab li:eq(0) a").click();


        $('#question-generator-student-id,.form-grade-select-section').change(function () {
            setTimeout(function () {
                if($("#question-generator-student-id").val().length!=0){
                    $("#question-generator-peer-group-options").multiselect('clearSelection');
                    $("#question-generator-peer-group-options").multiselect({
                        enableHTML: true,
                        templates: {
                            filter: '<li class="multiselect-item multiselect-filter"><div class="input-group mb-3"><div class="input-group-prepend"><span class="input-group-text"><i class="fa fa-search"></i></span></div><input class="form-control multiselect-search" type="text" /></div></li>',
                            filterClearBtn:
                                '<span class="input-group-btn"><button class="btn btn-default multiselect-clear-filter" type="button"><i class="fa fa-times"></i></button></span>',
                        },
                        columns: 1,
                        placeholder: "Select Peer Group",
                        search: true,
                        selectAll: true,
                        includeSelectAllOption: true,
                        enableFiltering: true,
                    });
                    $("#question-generator-peer-group-options").multiselect('disable');
                }else{
                    $("#question-generator-peer-group-options").multiselect('enable');   
                }
            },700)
        });
        $('#question-generator-peer-group-options').change(function () {
            if($(this).val().length!=0){
                $("#question-generator-student-id").multiselect('clearSelection');
                $("#question-generator-student-id").multiselect('disable');
            }else{
                if($('.question-generator-grade-chkbox:checked').length==0)
                {
                    $('#question-generator-student-id').prop('disabled',true);
                    $("#question-generator-student-id").multiselect('disable');
                }
            }
            $(".grade-class-date-time-list").html('');
            setGroupDateTimeList();
        });
        $(document).on('change',".grade-class-date-time-list .startDate",function () {
            var startdata=$(this).val();
            $(this).closest('.form-row').find('.endDate').datepicker('option', 'minDate',startdata);
        });
        $(document).on('change',".grade-class-date-time-list .endDate",function () {
            var enddata=$(this).val();
            $(this).closest('.form-row').find('.startDate').datepicker('option', 'maxDate',enddata);
        });

        $(document).on('change',".grade-class-date-time-list .start_time",function () {
            var selectedStartTimeIndex = this.selectedIndex;
            var selectedEndTimeIndex=$('#test_end_time option[value="'+$('#test_end_time').val()+'"]').index();
            $(this).closest('.form-row').find(".end_time option").each(function(){
                var endOptionSelectedStartTimeIndex = $(this).index();
                if(endOptionSelectedStartTimeIndex <= selectedStartTimeIndex){
                    $(this).attr("disabled", "disabled");
                }else if((endOptionSelectedStartTimeIndex >= selectedEndTimeIndex) && selectedEndTimeIndex>0){
                    $(this).attr("disabled", "disabled");
                }else{
                    $(this).removeAttr("disabled");
                }
            });
        });

        $(document).on('change',".grade-class-date-time-list .end_time",function () {
            var selectedStartTimeIndex = this.selectedIndex;
            var selectedEndTimeIndex=$('#test_start_time option[value="'+$('#test_start_time').val()+'"]').index();
            $(this).closest('.form-row').find(".start_time option").each(function(){
                var endOptionSelectedStartTimeIndex = $(this).index();
                if(endOptionSelectedStartTimeIndex >= selectedStartTimeIndex){
                    $(this).attr("disabled", "disabled");
                }else if((endOptionSelectedStartTimeIndex < selectedEndTimeIndex) && selectedEndTimeIndex>0){
                    $(this).attr("disabled", "disabled");
                }else{
                    $(this).removeAttr("disabled");
                }
            });
        });
        
        $('#question-generator-student-id').change();
        if($('.question-generator-class-chkbox:checked').length != 0){
            $('.group-date-time-list').html('');
            setGradeClassDateTimeList();
        }
        if($('#question-generator-peer-group-options option:selected').length!=0){
            $('.grade-class-date-time-list').html('');
            setGroupDateTimeList();
        }
        $("#test_start_time,#test_end_time,input[name=start_date],input[name=end_date]").change(function () {
            setGradeClassDateTimeList();
            setGroupDateTimeList();
        });
        @if($exam->created_by_user=='super_admin')
            var selectedStartTimeIndex=$('#test_start_time option[value="{{$exam->start_time}}"]').index();
            var selectedEndTimeIndex=$('#test_end_time option[value="{{$exam->end_time}}"]').index();
            $("#test_start_time option").each(function(){
                var endOptionSelectedStartTimeIndex = $(this).index();
                if(endOptionSelectedStartTimeIndex < selectedStartTimeIndex){
                    $(this).attr("disabled", "disabled");
                }else if((endOptionSelectedStartTimeIndex > selectedEndTimeIndex) && selectedEndTimeIndex>0){
                    $(this).attr("disabled", "disabled");
                }else{
                    $(this).removeAttr("disabled");
                }
            });
            $("#test_end_time option").each(function(){
                var endOptionSelectedStartTimeIndex = $(this).index();
                if(endOptionSelectedStartTimeIndex < selectedStartTimeIndex){
                    $(this).attr("disabled", "disabled");
                }else if((endOptionSelectedStartTimeIndex > selectedEndTimeIndex) && selectedEndTimeIndex>0){
                    $(this).attr("disabled", "disabled");
                }else{
                    $(this).removeAttr("disabled");
                }
            });

            $(".date-picker").datepicker({
                dateFormat: "dd/mm/yy",
                minDate:'{{ date('d/m/Y', strtotime($exam->from_date)) }}',
                maxDate:'{{ date('d/m/Y', strtotime($exam->to_date)) }}',
                changeMonth: true,
                changeYear: true,
                yearRange: "1950:" + new Date().getFullYear(),
            });
        @endif
    })

    function total_no_of_questions() {
        $("#total_no_of_questions").val(0);
        if($('.get_no_of_question_learning_objectives:not(:disabled)').length!=0){
         var total_data = $('.get_no_of_question_learning_objectives:not(:disabled)').map((_,el) => el.value).get();
            var total_data_sum = total_data.reduce((x, y) => parseInt(x) + parseInt(y));
            $("#total_no_of_questions").val(total_data_sum);
        }
    }

    function setGradeClassDateTimeList() {
        $(".grade-class-date-time-list").html('');
        var testStartTimeHtml = $('#test_start_time').html();
        var testEndTimeHtml = $('#test_end_time').html();
        var htmlData = '';
        if($('.question-generator-grade-chkbox:checked').length == 0){
            $('#question-generator-student-id').prop('disabled',true);
            $("#question-generator-student-id").multiselect('disable');
        }
        $('.question-generator-grade-chkbox').each(function(){
            var generatorValue = $(this).val();
            if($(this).is(":checked")) {
                var generatorClassChkboxLength = $(this).closest('.form-grade-select').find('.question-generator-class-chkbox:checked').length;
                var generatorClassChkboxAllLength = $(this).closest('.form-grade-select').find('.question-generator-class-chkbox').length;
                if(generatorClassChkboxLength == 0){
                    $('#question-generator-student-id').prop('disabled',true);
                    $("#question-generator-student-id").multiselect('disable');
                    
                    $(this).closest('.form-grade-select').find('.question-generator-class-chkbox').each(function(){
                        var generatorClassValue = $(this).val();
                        htmlData += dateTimeList($(this),generatorValue,generatorClassValue,testStartTimeHtml,testEndTimeHtml);
                    });

                }else{
                    $(this).closest('.form-grade-select').find('.question-generator-class-chkbox:checked').each(function(){
                        var generatorClassValue = $(this).val();
                        htmlData += dateTimeList($(this),generatorValue,generatorClassValue,testStartTimeHtml,testEndTimeHtml);
                    });
                }
            }else{
                $(this).closest('.form-grade-select').find('.question-generator-class-chkbox:checked').each(function(){
                    var generatorClassValue = $(this).val();
                    htmlData += dateTimeList($(this),generatorValue,generatorClassValue,testStartTimeHtml,testEndTimeHtml);
                });
            }
        });

        if(htmlData==''){
            $('.question-generator-class-chkbox:checked').each(function(){
                var generatorValue=$(this).closest('.form-grade-select').find('.question-generator-grade-chkbox').val();
                var generatorClassValue=$(this).val();
                htmlData+=dateTimeList($(this),generatorValue,generatorClassValue,testStartTimeHtml,testEndTimeHtml);
            });
        }

        $(".grade-class-date-time-list").html(htmlData);
        var mainStartDate=$("input[name=start_date]").val();
        var mainEndDate=$("input[name=end_date]").val();

        $(".date-picker-stud").datepicker({
            dateFormat: "dd/mm/yy",
            minDate:mainStartDate,
            maxDate:mainEndDate,
            changeMonth: true,
            changeYear: true,
            yearRange: "1950:" + new Date().getFullYear(),
        });
        var selectedStartTimeIndex=$('#test_start_time option[value="'+$('#test_start_time').val()+'"]').index();
        var selectedEndTimeIndex=$('#test_end_time option[value="'+$('#test_end_time').val()+'"]').index();
        $(".grade-class-date-time-list .end_time option").each(function(){
            var endOptionSelectedStartTimeIndex = $(this).index();
            if(endOptionSelectedStartTimeIndex < selectedStartTimeIndex){
                $(this).attr("disabled", "disabled");
            }else if((endOptionSelectedStartTimeIndex > selectedEndTimeIndex) && selectedEndTimeIndex>0){
                $(this).attr("disabled", "disabled");
            }else{
                $(this).removeAttr("disabled");
            }
        });
        $(".grade-class-date-time-list .start_time option").each(function(){
            var endOptionSelectedStartTimeIndex = $(this).index();
            if(endOptionSelectedStartTimeIndex < selectedStartTimeIndex){
                $(this).attr("disabled", "disabled");
            }else if((endOptionSelectedStartTimeIndex > selectedEndTimeIndex) && selectedEndTimeIndex>0){
                $(this).attr("disabled", "disabled");
            }else{
                $(this).removeAttr("disabled");
            }
        });
        var examStartTime={!! $examStartTime !!};
        $.each(examStartTime, function (k,v) {
            v=v.split(':');
            v=v[0]+':'+v[1];
            $('.cls_'+k).val(v);
        });
        var examEndTime={!! $examEndTime !!};
        $.each(examEndTime, function (k,v) {
            v=v.split(':');
            v=v[0]+':'+v[1];
            $('.clsE_'+k).val(v);
        });
    }

    function setGroupDateTimeList() { 
        $(".group-date-time-list").html('');
        var testStartTimeHtml = $('#test_start_time').html();
        var testEndTimeHtml = $('#test_end_time').html();
        var htmlData = '';
        $('#question-generator-peer-group-options option:selected').each(function(){
            var generatorGroupValue=$(this).attr('value');
            htmlData+=groupDateTimeList($(this),generatorGroupValue,testStartTimeHtml,testEndTimeHtml);
        });

        $(".group-date-time-list").html(htmlData);
        var mainStartDate=$("input[name=start_date]").val();
        var mainEndDate=$("input[name=end_date]").val();

        $(".date-picker-stud").datepicker({
            dateFormat: "dd/mm/yy",
            minDate:mainStartDate,
            maxDate:mainEndDate,
            changeMonth: true,
            changeYear: true,
            yearRange: "1950:" + new Date().getFullYear(),
        });
        var selectedStartTimeIndex=$('#test_start_time option[value="'+$('#test_start_time').val()+'"]').index();
        var selectedEndTimeIndex=$('#test_end_time option[value="'+$('#test_end_time').val()+'"]').index();
        $(".group-date-time-list .end_time option").each(function(){
            var endOptionSelectedStartTimeIndex = $(this).index();
            if(endOptionSelectedStartTimeIndex < selectedStartTimeIndex){
                $(this).attr("disabled", "disabled");
            }else if((endOptionSelectedStartTimeIndex > selectedEndTimeIndex) && selectedEndTimeIndex>0){
                $(this).attr("disabled", "disabled");
            }else{
                $(this).removeAttr("disabled");
            }
        });
        $(".group-date-time-list .start_time option").each(function(){
            var endOptionSelectedStartTimeIndex = $(this).index();
            if(endOptionSelectedStartTimeIndex < selectedStartTimeIndex){
                $(this).attr("disabled", "disabled");
            }else if((endOptionSelectedStartTimeIndex > selectedEndTimeIndex) && selectedEndTimeIndex>0){
                $(this).attr("disabled", "disabled");
            }else{
                $(this).removeAttr("disabled");
            }
        });
        var examStartTime={!! $examStartTime !!};
        $.each(examStartTime, function (k,v) {
            v=v.split(':');
            v=v[0]+':'+v[1];
            $('.cls_'+k).val(v);
        });
        var examEndTime={!! $examEndTime !!};
        $.each(examEndTime, function (k,v) {
            v=v.split(':');
            v=v[0]+':'+v[1];
            $('.clsE_'+k).val(v);
        });
    }
    
    function dateTimeList(E,generatorValue,generatorClassValue,testStartTimeHtml,testEndTimeHtml){
        var examStartTime={!! $examStartTime !!};
        var examEndTime={!! $examEndTime !!};
        var examStartDate={!! $examStartDate !!};
        var examEndDate={!! $examEndDate !!};
        var startTime='';
        if(examStartTime.length!=0 && examStartTime[generatorClassValue]){
            startTime=examStartTime[generatorClassValue];
            startTime=startTime.split(':');
            startTime=startTime[0]+':'+startTime[1];
        }

        var endTime='';
        if(examEndTime.length!=0 && examEndTime[generatorClassValue]){
            endTime=examEndTime[generatorClassValue];
            endTime=endTime.split(':');
            endTime=endTime[0]+':'+endTime[1];
        }
        var mainStartDate=$("input[name=start_date]").val();
        var mainEndDate=$("input[name=end_date]").val();
        var startDate='{{date('d/m/Y')}}';
        if(mainStartDate!=''){
            startDate=mainStartDate;
        }
        if(examStartDate.length!=0 && examStartDate[generatorClassValue]){
            startDateNew=examStartDate[generatorClassValue];
            startDateNew=startDateNew.split(' ');
            if(startDateNew[0]!='0000-00-00'){
                startDate=startDateNew[0].split('-');
                startDate=startDate[2]+'/'+startDate[1]+'/'+startDate[0];
            }
        }
        var endDate='{{date('d/m/Y')}}';
        if(mainEndDate!=''){
            endDate=mainEndDate;
        }
        if(examEndDate.length!=0 && examEndDate[generatorClassValue]){
            endDateNew=examEndDate[generatorClassValue];
            endDateNew=endDateNew.split(' ');
            if(endDateNew[0]!='0000-00-00'){
                endDate=endDateNew[0].split('-');
                endDate=endDate[2]+'/'+endDate[1]+'/'+endDate[0];
            }
        }
        dataHtmlData='<div class="row"><div class="col-md-1"><label>'+E.attr('data-label')+'</label></div><div class="col-md-11"><div class="form-row">\
            <div class="form-group col-md-3 mb-50">\
                <label>{{ __('languages.question_generators_menu.start_date') }}</label>\
                <div class="input-group date">\
                    <input type="text" class="form-control date-picker-stud startDate" id="generatorClassValue_'+generatorClassValue+'" name="generator_class_start_date['+generatorValue+']['+generatorClassValue+']" value="'+startDate+'" placeholder="{{__('languages.question_generators_menu.start_date')}}" autocomplete="off">\
                    <div class="input-group-addon input-group-append">\
                        <div class="input-group-text">\
                            <i class="glyphicon glyphicon-calendar fa fa-calendar"></i>\
                        </div>\
                    </div>\
                </div>\
            </div>\
            <div class="form-group col-md-3 mb-50">\
                <label for="id_end_time">{{ __('languages.question_generators_menu.start_time') }}</label>\
                <div class="input-group date">\
                    <select name="generator_class_start_time['+generatorValue+']['+generatorClassValue+']" class="form-control select-option  start_time cls_'+generatorClassValue+'" >'+testStartTimeHtml+'</select>\
                </div>\
            </div>\
            <div class="form-group col-md-3 mb-50">\
                <label>{{ __('languages.question_generators_menu.end_date') }}</label>\
                <div class="input-group date">\
                    <input type="text" class="form-control date-picker-stud endDate" name="generator_class_end_date['+generatorValue+']['+generatorClassValue+']" value="'+endDate+'" placeholder="{{__('languages.question_generators_menu.end_date')}}" autocomplete="off">\
                    <div class="input-group-addon input-group-append">\
                        <div class="input-group-text">\
                            <i class="glyphicon glyphicon-calendar fa fa-calendar"></i>\
                        </div>\
                    </div>\
                </div>\
            </div>\
            <div class="form-group col-md-3 mb-50">\
                <label for="id_end_time">{{ __('languages.question_generators_menu.end_time') }}</label>\
                <div class="input-group date">\
                    <select name="generator_class_end_time['+generatorValue+']['+generatorClassValue+']" class="form-control select-option end_time clsE_'+generatorClassValue+'" >'+testEndTimeHtml+'</select>\
                </div>\
            </div>\
        </div></div><div class="col-md-12"><hr></div></div>';
        return dataHtmlData;
    }
    function groupDateTimeList(E,generatorGroupValue,testStartTimeHtml,testEndTimeHtml){
        var examStartTime={!! $examStartTime !!};
        var examEndTime={!! $examEndTime !!};
        var examStartDate={!! $examStartDate !!};
        var examEndDate={!! $examEndDate !!};
        var startTime='';
        if(examStartTime.length!=0 && examStartTime[generatorGroupValue]){
            startTime=examStartTime[generatorGroupValue];
            startTime=startTime.split(':');
            startTime=startTime[0]+':'+startTime[1];
        }

        var endTime='';
        if(examEndTime.length!=0 && examEndTime[generatorGroupValue]){
            endTime=examEndTime[generatorGroupValue];
            endTime=endTime.split(':');
            endTime=endTime[0]+':'+endTime[1];
        }
        var mainStartDate=$("input[name=start_date]").val();
        var mainEndDate=$("input[name=end_date]").val();
        var startDate='{{date('d/m/Y')}}';
        if(mainStartDate!=''){
            startDate=mainStartDate;
        }
        if(examStartDate.length!=0 && examStartDate[generatorGroupValue]){
            startDateNew=examStartDate[generatorGroupValue];
            startDateNew=startDateNew.split(' ');
            if(startDateNew[0]!='0000-00-00'){
                startDate=startDateNew[0].split('-');
                startDate=startDate[2]+'/'+startDate[1]+'/'+startDate[0];
            }
        }
        var endDate='{{date('d/m/Y')}}';
        if(mainEndDate!=''){
            endDate=mainEndDate;
        }
        if(examEndDate.length!=0 && examEndDate[generatorGroupValue]){
            endDateNew=examEndDate[generatorGroupValue];
            endDateNew=endDateNew.split(' ');
            if(endDateNew[0]!='0000-00-00'){
                endDate=endDateNew[0].split('-');
                endDate=endDate[2]+'/'+endDate[1]+'/'+endDate[0];
            }
        }
        dataHtmlData='<div class="row"><div class="col-md-1"><label>'+E.attr('data-label')+'</label></div><div class="col-md-11"><div class="form-row">\
            <div class="form-group col-md-3 mb-50">\
                <label>{{ __('languages.question_generators_menu.start_date') }}</label>\
                <div class="input-group date">\
                    <input type="text" class="form-control date-picker-stud startDate" id="generatorGroupValue_'+generatorGroupValue+'" name="generator_group_start_date['+generatorGroupValue+']" value="'+startDate+'" placeholder="{{__('languages.question_generators_menu.start_date')}}" autocomplete="off">\
                    <div class="input-group-addon input-group-append">\
                        <div class="input-group-text">\
                            <i class="glyphicon glyphicon-calendar fa fa-calendar"></i>\
                        </div>\
                    </div>\
                </div>\
            </div>\
            <div class="form-group col-md-3 mb-50">\
                <label for="id_end_time">{{ __('languages.question_generators_menu.start_time') }}</label>\
                <div class="input-group date">\
                    <select name="generator_group_start_time['+generatorGroupValue+']" class="form-control select-option  start_time cls_'+generatorGroupValue+'" >'+testStartTimeHtml+'</select>\
                </div>\
            </div>\
            <div class="form-group col-md-3 mb-50">\
                <label>{{ __('languages.question_generators_menu.end_date') }}</label>\
                <div class="input-group date">\
                    <input type="text" class="form-control date-picker-stud endDate" name="generator_group_end_date['+generatorGroupValue+']" value="'+endDate+'" placeholder="{{__('languages.question_generators_menu.end_date')}}" autocomplete="off">\
                    <div class="input-group-addon input-group-append">\
                        <div class="input-group-text">\
                            <i class="glyphicon glyphicon-calendar fa fa-calendar"></i>\
                        </div>\
                    </div>\
                </div>\
            </div>\
            <div class="form-group col-md-3 mb-50">\
                <label for="id_end_time">{{ __('languages.question_generators_menu.end_time') }}</label>\
                <div class="input-group date">\
                    <select name="generator_group_end_time['+generatorGroupValue+']" class="form-control select-option end_time clsE_'+generatorGroupValue+'" >'+testEndTimeHtml+'</select>\
                </div>\
            </div>\
        </div></div><div class="col-md-12"><hr></div></div>';
        return dataHtmlData;
    }
</script>
@include('backend.layouts.footer')
@endsection