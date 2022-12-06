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
                            <h2 class="mb-4 main-title">{{__('languages.question_generators_menu.question_generators')}}</h2>
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
                <form name="question-generator" id="question-generator" action="{{ route('super-admin.generate-questions-edit',$exam->id) }}" method="POST">
                    @csrf
                    <div class="sm-add-user-sec card">
                        <div class="select-option-sec pb-5 card-body">
                            <div id="wizard">
                                <div class="question-generator-option-headings mb-3">
                                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 pl-0 pr-0">
                                        <ul class="form-tab">
                                            <li class="step-headings section-step1 admin-tab tab_active" data-tabid="1">1. {{__('languages.question_generators_menu.configuration')}}</li>
                                            <li class="step-headings section-step2 admin-tab" data-tabid="2">2. {{__('languages.question_generators_menu.to_schools')}}</li>
                                            <li class="step-headings section-step3 admin-tab" data-tabid="3">3. {{__('languages.question_generators_menu.select_learning_objectives')}}</li>
                                            <li class="step-headings section-step4 admin-tab" data-tabid="4">4. {{__('languages.question_generators_menu.review_of_questions')}}</li>
                                        </ul>
                                    </div>
                                </div>
                                <section class="form-steps step1">
                                    <div class="form-row">
                                        <div class="form-group col-md-6 mb-50">
                                            <label class="text-bold-600">{{__('languages.question_generators_menu.use_of_mode')}}</label>
                                            <select name="use_of_modes" class="form-control select-option" id="use_of_modes" disabled="disabled">
                                                <option value="">{{__('languages.question_generators_menu.select_use_of_mode')}}</option>
                                                <option value="1" @if($exam->use_of_mode == 1) selected @endif>{{__('languages.question_generators_menu.as_a_test_exercise')}}</option> 
                                                <option value="2" @if($exam->use_of_mode == 2) selected @endif>{{__('languages.question_generators_menu.as_a_collection_of_questions')}}</option>
                                            </select>
                                        </div>

                                        <div class="form-group col-md-6 mb-50">
                                            <label class="text-bold-600">{{__('languages.question_generators_menu.test_mode')}}</label>
                                            <select name="test_type" class="form-control select-option" id="test_type">
                                                <option value="2" @if($exam->exam_type == 2) selected @endif>{{__('languages.excercise')}}</option> 
                                                <option value="3" @if($exam->exam_type == 3) selected @endif>{{__('languages.test_text')}}</option>
                                            </select>
                                        </div>

                                        <div class="form-group col-md-12 mb-50">
                                            <label class="text-bold-600">{{__('languages.question_generators_menu.title')}}</label>
                                            <input type="text" name="title" value="{{ $exam->title }}" class="form-control" placeholder="{{__('languages.question_generators_menu.enter_title')}}" required>
                                        </div>

                                        <div class="form-group col-md-6 mb-50">
                                            <label>{{ __('languages.question_generators_menu.start_date') }}</label>
                                            <div class="input-group date">
                                                <input type="text" class="form-control date-picker" name="start_date" value="{{ date('d/m/Y', strtotime($exam->from_date)) }}" placeholder="{{__('languages.question_generators_menu.select_start_date')}}" autocomplete="off">
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
                                                <select name="start_time" class="form-control select-option" id="test_start_time">
                                                    <option value="">{{__('languages.question_generators_menu.select_test_start_time')}}</option>
                                                    @if(isset($timeSlots) && !empty($timeSlots))
                                                        @foreach($timeSlots as $timeSlotKey => $time)
                                                            <option @if($exam->start_time == $time) selected @endif value="{{$time}}">{{$time}}</option>
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
                                                {{-- <input type="text" class="form-control date-picker" name="end_date" value="{{ date('d/m/Y', strtotime($exam->to_date)) }}" placeholder="{{ __('languages.question_generators_menu.select_end_date') }}" autocomplete="off"> --}}
                                                <input type="text" class="form-control end-date-picker" name="end_date" value="{{ date('d/m/Y', strtotime($exam->to_date)) }}" placeholder="{{ __('languages.question_generators_menu.select_end_date') }}" autocomplete="off">
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
                                                <select name="end_time" class="form-control select-option" id="test_end_time">
                                                    <option value="">{{__('languages.question_generators_menu.select_test_end_time')}}</option>
                                                    @if(isset($timeSlots) && !empty($timeSlots))
                                                        @foreach($timeSlots as $timeSlotKey => $time)
                                                            <option @if($exam->end_time == $time) selected @endif value="{{$time}}">{{$time}}</option>
                                                        @endforeach
                                                    @endif
                                                </select>
                                            </div>
                                            <span id="end-date-error"></span>
                                            @if($errors->has('end_time'))<span class="validation_error">{{ $errors->first('end_time') }}</span>@endif
                                        </div>

                                        <div class="form-group col-md-6 mb-50">
                                            <label class="text-bold-600">{{ __('languages.question_generators_menu.report_date') }}</label>
                                            <select name="report_date" class="form-control select-option" id="select-report-date">
                                                <option value="end_date" @if($exam->report_type == 'end_date') selected @endif>{{__('languages.question_generators_menu.end_date')}}</option>
                                                <option value="after_submit" @if($exam->report_type == 'after_submit') selected @endif >{{__('languages.question_generators_menu.after_submit')}}</option>
                                                <option value="custom_date" @if($exam->report_type == 'custom_date') selected @endif >{{__('languages.question_generators_menu.custom_date')}}</option>
                                            </select>
                                        </div>

                                        <div class="form-group col-md-6 mb-50 custom_date" style="display: none;">
                                            <label>{{__('languages.question_generators_menu.report_custom_date')}}</label>
                                            <div class="input-group date">
                                                <input type="text" class="form-control date-picker" name="custom_date" value="{{ date('d/m/Y', strtotime($exam->result_date)) }}" placeholder="{{__('languages.question_generators_menu.report_custom_date')}}" autocomplete="off">
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
                                            <select name="no_of_trials_per_question" class="form-control select-option" id="select-no-of-per-trials-question">
                                                <option value="1" @if($exam->no_of_trials_per_question==1) selected @endif >1</option> 
                                                <option value="2" @if($exam->no_of_trials_per_question==2) selected @endif >2</option>
                                            </select>
                                        </div> -->

                                        <div class="form-group col-md-6 mb-50">
                                            <label>{{__('languages.difficulty_mode')}}</label>
                                            <select name="difficulty_mode" class="form-control select-option" id="difficulty_mode">
                                                <option value="manual" @if($exam->difficulty_mode=='manual') selected @endif >{{__('languages.manual')}}</option>
                                                <!-- <option value="auto">Auto Fit</option> -->
                                            </select>
                                        </div>

                                        <div class="form-group col-md-6 mb-50">
                                            <label>{{__('languages.questions.difficulty_level')}}</label>
                                            <select name="difficulty_lvl[]" class="form-control select-option" id="difficulty_lvl" multiple>
                                                @if(!empty($difficultyLevels))
                                                @php
                                                $examDifficultyLevels = ($exam->difficulty_levels) ? explode(',',$exam->difficulty_levels) : [];
                                                @endphp
                                                @foreach($difficultyLevels as $difficultyLevel)
                                                <option value="{{$difficultyLevel->difficulty_level}}" @if(in_array($difficultyLevel->difficulty_level,$examDifficultyLevels)) selected @endif>{{ $difficultyLevel->{'difficulty_level_name_'.app()->getLocale()} }}</option>
                                                @endforeach
                                                @endif								
                                            </select>
                                            <span name="err_difficulty_level"></span>
                                        </div>

                                        <!-- <div class="form-group col-md-6 mb-50" id="display_hints_section" style="display:none;">
                                            <label>{{__('languages.question_generators_menu.display_hints')}}</label>
                                            <select name="display_hints" class="form-control select-option" id="select-display-hints">
                                                <option value="no" @if($exam->display_hints == 'no') selected @endif>{{__('languages.question_generators_menu.no')}}</option>
                                                <option value="yes"  @if($exam->display_hints == 'yes') selected @endif >{{__('languages.question_generators_menu.yes')}}</option>
                                            </select>
                                        </div> -->

                                        <!-- <div class="form-group col-md-6 mb-50">
                                            <label>{{__('languages.question_generators_menu.display_full_solution_in_report')}}</label>
                                            <select name="display_full_solution" class="form-control select-option" id="select-display-full-solutions">
                                                <option value="yes" @if($exam->display_full_solution == 'yes') selected @endif >{{__('languages.question_generators_menu.yes')}}</option>
                                                <option value="no" @if($exam->display_full_solution == 'no') selected @endif >{{__('languages.question_generators_menu.no')}}</option>
                                            </select>
                                        </div> -->

                                        <!-- <div class="form-group col-md-6 mb-50">
                                            <label>{{__('languages.question_generators_menu.display_pr_answer_hints')}}</label>
                                            <select name="display_pr_answer_hints" class="form-control select-option" id="select-display-pr-answer-hints">
                                                <option value="yes"  @if($exam->display_pr_answer_hints == 'yes') selected @endif >{{__('languages.question_generators_menu.yes')}}</option>
                                                <option value="no"  @if($exam->display_pr_answer_hints == 'no') selected @endif >{{__('languages.question_generators_menu.no')}}</option>
                                            </select>
                                        </div> -->

                                        <!-- <div class="form-group col-md-6 mb-50">
                                            <label>{{__('languages.question_generators_menu.randomize_answer')}} {{__('languages.question_generators_menu.means_different_for_every_student')}}</label>
                                            <select name="randomize_answer" class="form-control select-option" id="select-randomize-answers">
                                                <option value="yes" @if($exam->randomize_answer == 'yes') selected @endif >{{__('languages.question_generators_menu.yes')}}</option>
                                                <option value="no" @if($exam->randomize_answer == 'no') selected @endif >{{__('languages.question_generators_menu.no')}}</option>
                                            </select>
                                        </div> -->

                                        <!-- <div class="form-group col-md-6 mb-50">
                                            <label>{{__('languages.question_generators_menu.randomize_order')}} {{__('languages.question_generators_menu.means_different_for_every_student')}}</label>
                                            <select name="randomize_order" class="form-control select-option" id="select-randomize-order">
                                                <option value="yes" @if($exam->randomize_order == 'yes') selected @endif >{{__('languages.question_generators_menu.yes')}}</option>
                                                <option value="no" @if($exam->randomize_order == 'no') selected @endif >{{__('languages.question_generators_menu.no')}}</option>
                                            </select>
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
                                @php
                                $existingSchoolIds = ($exam->school_id) ? explode(',',$exam->school_id) : [];
                                @endphp
                                <section class="form-steps step2" style="display:none;">
                                    <div class="form-group col-md-6 mb-50">
                                        <select name="schoolIds[]" class="form-control select-option" id="school-select-option" multiple>
                                            @if(isset($schoolList) && !empty($schoolList))
                                                <label>Select School</label>
                                                @foreach($schoolList as $school)
                                                <option @if(in_array($school->id,$existingSchoolIds)) selected @endif value="{{$school->id}}">
                                                    @if(app()->getLocale() == 'en')
                                                    {{$school->DecryptSchoolNameEn}}
                                                    @else
                                                    {{$school->DecryptSchoolNameCh}}
                                                    @endif
                                                </option>
                                                @endforeach
                                            @else
                                                <option value="">{{ __('No any school available') }}</option>
                                            @endif
                                        </select>
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
                                            <select name="strand_id[]" class="form-control select-option" id="strand_id" multiple>
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
                                            <select name="learning_unit_id[]" class="form-control select-option" id="learning_unit" multiple>
                                                @if(isset($LearningUnits) && !empty($LearningUnits))
                                                    @foreach ($LearningUnits as $learningUnitKey => $learningUnit)
                                                        <option value="{{$learningUnit->id}}" @if(in_array($learningUnit->id,$SelectedLearningUnit)) selected @endif>{{ $learningUnit->{'name_'.app()->getLocale()} }}</option>
                                                    @endforeach
                                                @else
                                                    <option value="">{{__('languages.no_learning_units_available')}}</option>
                                                @endif
                                            </select>
                                            <label class="error learning_unit_error_msg"></label>
                                        </div>
                                        <div class="form-group col-md-6 mb-50">
                                            <label class="text-bold-600">{{__('languages.question_generators_menu.total_no_of_questions')}}</label>
                                            <input type="text" name="total_no_of_questions" id="total_no_of_questions" value="{{$exam->total_no_of_questions}}" class="form-control" placeholder="{{__('languages.question_generators_menu.total_no_of_questions')}}" required readonly>
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
                                            <input type="checkbox" name="all_learning_objective_checkbox" value="" class="all_learning_objective_checkbox" checked> {{__('Select All')}}
                                        </div>
                                        @foreach ($LearningObjectives as $learningObjectivesKey => $learningObjectives)
                                        
                                        @php
                                            $noOfQuestionPerLearningObjective=App\Helpers\Helper::getNoOfQuestionPerLearningObjective($learningObjectives->learning_unit_id,$learningObjectives->id);
                                            
                                            $existingDifficultySelectd = ($exam->difficulty_levels) ? explode(',',$exam->difficulty_levels) : [];
                                            if(isset($learningObjectivesConfiguration[$learningObjectives->learning_unit_id]['learning_objective'][$learningObjectives->id]['learning_objectives_difficulty_level'])) 
                                            {
                                                $existingDifficultySelectd=$learningObjectivesConfiguration[$learningObjectives->learning_unit_id]['learning_objective'][$learningObjectives->id]['learning_objectives_difficulty_level'];
                                            }

                                            $get_no_of_question_learning_objectives=$noOfQuestionPerLearningObjective;
                                            if(isset($learningObjectivesConfiguration[$learningObjectives->learning_unit_id]['learning_objective'][$learningObjectives->id]['get_no_of_question_learning_objectives'])) {
                                                $get_no_of_question_learning_objectives = $learningObjectivesConfiguration[$learningObjectives->learning_unit_id]['learning_objective'][$learningObjectives->id]['get_no_of_question_learning_objectives'];
                                            }
                                        @endphp

                                        <div class="selected-learning-objectives-difficulty">
                                            <input type="checkbox" name="learning_unit[{{$learningObjectives->learning_unit_id}}][learning_objective][{{ $learningObjectives->id }}]" value="{{ $learningObjectives->id }}" class="learning_objective_checkbox" @if(isset($learningObjectivesConfiguration[$learningObjectives->learning_unit_id]['learning_objective'][$learningObjectives->id])) checked @endif>
                                            <label>{{ $learningObjectives->foci_number }} {{ $learningObjectives->{'title_'.app()->getLocale()} }}</label>
                                            <select name="learning_unit[{{$learningObjectives->learning_unit_id}}][learning_objective][{{ $learningObjectives->id }}][learning_objectives_difficulty_level][]" class="form-control select-option learning_objectives_difficulty_level" multiple>
                                                <option value="1" @if(in_array(1,$existingDifficultySelectd)) selected @endif >1</option>
                                                <option value="2" @if(in_array(2,$existingDifficultySelectd)) selected @endif >2</option>
                                                <option value="3" @if(in_array(3,$existingDifficultySelectd)) selected @endif >3</option>
                                                <option value="4" @if(in_array(4,$existingDifficultySelectd)) selected @endif >4</option>
                                                <option value="5" @if(in_array(5,$existingDifficultySelectd)) selected @endif >5</option>
                                            </select>
                                            <input type="test" name="learning_unit[{{$learningObjectives->learning_unit_id}}][learning_objective][{{ $learningObjectives->id }}][get_no_of_question_learning_objectives]" value="{{ $get_no_of_question_learning_objectives }}" class="get_no_of_question_learning_objectives" min="{{ $noOfQuestionPerLearningObjective }}" max="{{$RequiredQuestionPerSkill['maximum_question_per_skill']}}">
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
                                @php
                                    $questionTabList='';
                                    $questionTabDataList='';
                                    $qIndex=1;
                                @endphp
                                @if(isset($questionDataArray) && !empty($questionDataArray))
                                    @foreach($questionDataArray as $questionData)
                                        @php
                                            $tab_active = '';
                                            $tab_active_contact = '';
                                            $aria_selected = 'false';
                                            if($qIndex == 1){
                                                $tab_active = 'active';
                                                $aria_selected = 'true';
                                            }
                                            $questionTitle=$questionData['question_'.app()->getLocale()];
                                            $difficultyLevelName=$questionData['pre_configuration_difficulty_level']['difficulty_level_name_'.app()->getLocale()];
                                            $answer1Title=$questionData['answers']['answer1_'.app()->getLocale()];
                                            $answer2Title=$questionData['answers']['answer2_'.app()->getLocale()];
                                            $answer3Title=$questionData['answers']['answer3_'.app()->getLocale()];
                                            $answer4Title=$questionData['answers']['answer4_'.app()->getLocale()];
                                        
                                            $questionTabList.='<li class="nav-item">
                                                <input type="hidden" name="qIndex[]" value="'.$questionData['id'].'" />
                                                <a class="nav-link '.$tab_active.'" id="pills-contact-tab-'.$qIndex.'" data-toggle="pill" href="#pills-contact-'.$qIndex.'" role="tab" aria-controls="pills-contact-'.$qIndex.'" aria-selected="'.$aria_selected.'">Q'.$qIndex.'</a>
                                            </li>';

                                            $questionTabDataList.='<div class="tab-pane fade '.$tab_active_contact.'" id="pills-contact-'.$qIndex.'" role="tabpanel" aria-labelledby="pills-contact-tab-'.$qIndex.'">
                                                <div class="d-flex pb-3">
                                                <div class="question-content pl-2">
                                                    <div class="row">
                                                        <div class="col-md-6"><b>'.__('languages.upload_document.strands').'</b> : <span class="q-strand-name" data-q-strand-id="'.$questionData['objective_mapping']['strand_id'].'">'.$questionData['objective_mapping']['strandName'].'</span></div>
                                                        <div class="col-md-6"><b>'.__('languages.upload_document.learning_units').'</b> : <span class="q-learning-units-name" data-q-learning-units-id="'.$questionData['objective_mapping']['learning_unit_id'].'">'.$questionData['objective_mapping']['learningUnitsName'].'</span></div>
                                                        <div class="col-md-6"><b>'.__('languages.learning_objectives').'</b> : <span class="q-learning-objectives-title" data-q-learning-objectives-id="'.$questionData['objective_mapping']['learning_objectives_id'].'">'.$questionData['objective_mapping']['learningObjectivesTitle'].'</span></div>
                                                        <div class="col-md-6"><b>'.__('languages.questions.difficulty_level').'</b> : <span class="q-difficulty-level" data-q-difficulty-level-id="'.$questionData['dificulaty_level'].'">'.$difficultyLevelName.'</span></div>
                                                    </div>
                                                    <div class="question-heading">
                                                        <p class="question-title review-question-title">'. __('languages.questions.question').':</p>
                                                    </div>
                                                    <div class="question-answer-content pl-2">
                                                        <div class="question_content">
                                                            <label for="question-content" class="pl-3">'.$questionTitle.'</label>
                                                        </div>
                                                        <div class="answer-content">
                                                            <div class="answer-review">
                                                                <span class="answer-detail-number">1</span>
                                                                <div class="review-answer-detail">'.$answer1Title.'</div>
                                                            </div>
                                                            <div class="answer-review">
                                                                <span class="answer-detail-number">2</span>
                                                                <div class="review-answer-detail">'.$answer2Title.'</div>
                                                            </div>
                                                            <div class="answer-review">
                                                                <span class="answer-detail-number">3</span>
                                                                <div class="review-answer-detail">'.$answer3Title.'</div>
                                                            </div>
                                                            <div class="answer-review">
                                                                <span class="answer-detail-number">4</span>
                                                                <div class="review-answer-detail">'.$answer4Title.'</div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="d-flex align-items-center float-left mt-5">
                                                        <button type="button" class="btn-search bg-pink want_a_hint" question-id="'.$questionData['id'].'">'.__('languages.my_studies.want_a_hint').'</button>
                                                    </div>
                                                </div>
                                                </div>
                                            </div>';

                                            $qIndex++;
                                        @endphp
                                    @endforeach
                                @endif
                                <section class="form-steps step4" style="display:none;">
                                    <div class="d-flex review-question-main tab-content-wrap">
                                        <div class="review-question-left-section">
                                            <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist" aria-orientation="vertical">
                                                {!! $questionTabList !!}
                                            </ul>
                                            <div class="tab-content review-question-tab" id="pills-tabContent">
                                                {!! $questionTabDataList !!}
                                            </div>
                                        </div>
                                        <div class="review-question-right-section">
                                            <div class="refresh-question-options">
                                                <div class="refresh-question-selection">
                                                    <label>{{__('languages.upload_document.strands')}}</label>
                                                    <select name="refresh_question_strand_id[]" class="form-control select-option" id="refresh-question-strand-id" multiple>
                                                        @if(isset($strandsList) && !empty($strandsList))
                                                            @foreach ($strandsList as $strandKey => $strand)
                                                                <option value="{{$strand->id}}" @if($strandKey == 0) selected @endif>{{$strand->{'name_'.app()->getLocale()} }}</option>
                                                            @endforeach
                                                        @else
                                                            <option value="">{{__('languages.no_strands_available')}}</option>
                                                        @endif
                                                    </select>
                                                </div>
                                                <div class="refresh-question-selection">
                                                    <label>{{__('languages.upload_document.learning_units')}}</label>
                                                    <select name="refresh_question_learning_unit_id[]" class="form-control select-option" id="refresh-question-learning-unit" multiple>
                                                        @if(isset($LearningUnits) && !empty($LearningUnits))
                                                            @foreach ($LearningUnits as $learningUnitKey => $learningUnit)
                                                                <option value="{{$learningUnit->id}}" @if($learningUnitKey == 0) selected @endif>{{ $learningUnit->{'name_'.app()->getLocale()} }}</option>
                                                            @endforeach
                                                        @else
                                                            <option value="">{{__('languages.no_learning_units_available')}}</option>
                                                        @endif
                                                    </select>
                                                </div>
                                                <div class="refresh-question-selection">
                                                    <label>{{__('languages.upload_document.learning_objectives')}}</label>
                                                    <select name="refresh_question_learning_objectives_id[]" class="form-control select-option" id="refresh-question-learning-objectives" multiple>
                                                        @if(isset($LearningObjectives) && !empty($LearningObjectives))
                                                            @foreach ($LearningObjectives as $learningObjectivesKey => $learningObjectives)
                                                                <option value="{{ $learningObjectives->id }}" @if($learningObjectivesKey == 0) selected @endif>{{ $learningObjectives->foci_number }} {{ $learningObjectives->{'title_'.app()->getLocale()} }}</option>
                                                            @endforeach
                                                        @else
                                                            <option value="">{{__('languages.no_learning_objectives_available')}}</option>
                                                        @endif
                                                    </select>
                                                </div>
                                                <div class="refresh-question-selection">
                                                    <label>{{__('languages.questions.difficulty_level')}}</label>
                                                    <select name="refresh_question_difficulty_level[]" class="form-control select-option" id="refresh_question_difficulty_level" multiple>
                                                        @if(!empty($difficultyLevels))
                                                        @foreach($difficultyLevels as $difficultyLevel)
                                                        <option value="{{$difficultyLevel->difficulty_level}}" @if($difficultyLevel->difficulty_level == 2) selected @endif>{{$difficultyLevel->difficulty_level_name}}</option>
                                                        @endforeach
                                                        @endif								
                                                    </select>
                                                </div>
                                                <div class="refresh-question-btn">
                                                    <button type="button" class="btn-search bg-pink ml-1 refresh-question-data">{{__('languages.question_generators_menu.refresh_question')}}</button>
                                                </div>
                                            </div>
                                            <hr class="blue-line refresh-question-options-blue-line">
                                            <div class="refresh-question" style="display:none">
                                                <input type="hidden" id="refresh-question-id">
                                                <div class="tab-content">
                                                    <div class="pb-3">
                                                        <div class="question-strand-detail pl-3">
                                                            <label><b>{{__('languages.upload_document.strands')}} :</b> </label><span>some text</span>
                                                        </div>
                                                        <div class="question-learning-unit-detail">
                                                            <label><b>{{__('languages.upload_document.learning_units')}} :</b> </label><span>some text</span>
                                                        </div>
                                                        <div class="question-learning-objectives-detail pl-3">
                                                            <label><b>{{ __('languages.learning_objectives') }} :</b> </label><span>some text</span>
                                                        </div>
                                                        <div class="question-difficulty-detail">
                                                            <label><b>{{__('languages.questions.difficulty_level')}} :</b> </label><span>Level 1</span>
                                                        </div>
                                                        <div class="question-heading pl-3">
                                                            <p class="question-title review-question-title">{{ __('languages.questions.question')}} :</p>
                                                        </div>
                                                        <div class="question-answer-content pl-3">
                                                            <div class="question_content">
                                                                <label for="question-content" class="pl-3">xyz question</label>
                                                            </div>
                                                            <div class="answer-content">
                                                                <div class="answer-review">
                                                                    <span class="answer-detail-number">1</span>
                                                                    <div class="review-answer-detail">
                                                                        <p>option a</p>
                                                                    </div>
                                                                </div>
                                                                <div class="answer-review">
                                                                    <span class="answer-detail-number">2</span>
                                                                    <div class="review-answer-detail">
                                                                        <p>option b</p>
                                                                    </div>
                                                                </div>
                                                                <div class="answer-review">
                                                                    <span class="answer-detail-number">3</span>
                                                                    <div class="review-answer-detail">
                                                                        <p>option c</p>
                                                                    </div>
                                                                </div>
                                                                <div class="answer-review">
                                                                    <span class="answer-detail-number">4</span>
                                                                    <div class="review-answer-detail">
                                                                        <p>option d</p>
                                                                    </div>
                                                                </div>
                                                                <div class="float-left mt-3 w-100">
                                                                    <button type="button" class="btn-search bg-pink want_a_hint">{{__('languages.my_studies.want_a_hint')}}</button>
                                                                </div>
                                                                <div class="replace-question-button">
                                                                    <button type="button" class="btn-search bg-pink ml-1 replace-question">{{__('languages.question_generators_menu.replace_question')}}</button>
                                                                    <button type="button" class="btn-search bg-pink ml-1 add-question">{{__('languages.add_questions')}}</button>
                                                                </div>
                                                            </div>
                                                        </div>  
                                                    </div>
                                                </div>
                                            </div>
                                            <hr class="blue-line refresh-question-options-blue-line">
                                        </div>
                                        <div class="btn_group mb-3 review-question-position-button">
                                            <button type="button" class="btn-search bg-pink btn-up"><i class="fa fa-arrow-up mr-1" aria-hidden="true"></i>{{__('languages.question_generators_menu.up')}}</button>
                                            <button type="button" class="btn-search bg-pink  btn-down"><i class="fa fa-arrow-down mr-1" aria-hidden="true"></i>{{__('languages.question_generators_menu.down')}}</button>
                                            <button type="button" class="btn-search bg-pink set-top"><i class="fa fa-arrow-up" aria-hidden="true"></i><i class="fa fa-arrow-up mr-1" aria-hidden="true"></i>{{__('languages.question_generators_menu.set_top')}}</button>
                                            <button type="button" class="btn-search set-bottom bg-pink"><i class="fa fa-arrow-down" aria-hidden="true"></i><i class="fa fa-arrow-down mr-1" aria-hidden="true"></i>{{__('languages.question_generators_menu.set_bottom')}}</button>
                                            <button type="button" class="btn-search btn-remove-tab set-bottom bg-pink"><i class="fa fa-trash  mr-1" aria-hidden="true"></i>{{__('languages.question_generators_menu.remove')}}</button>
                                        </div>
                                    </div>
                                    <div class="form-row select-data float-left">
                                        <div class="sm-btn-sec form-row">
                                            <div class="form-group mb-50 btn-sec">
                                                <button type="button" class="blue-btn btn btn-primary previous-button previous_btn_step_4" data-stepid="4">{{__('languages.question_generators_menu.previous')}}</button>
                                                <!-- <button type="submit" class="blue-btn btn btn-primary" data-stepid="4">{{__('languages.question_generators_menu.submit')}}</button> -->
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
            if(currentStep == 3){
                getLearningObjectivesOptionList();
            }
        }
        else
        {
            console.log(currentStep);
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
            // Set default difficulty level for selected first steps
            setDefaultDifficultyLevels();
            $(".learning_objective_checkbox").parent().find('select').multiselect('disable');
            $(".learning_objective_checkbox:checked").parent().find('select').multiselect('enable');
        }
        else
        {
            $(".learning_objective_checkbox").parent().find('select').multiselect('disable');
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
		if ($(this).is(":checked")) {
            $(".learning_objective_checkbox").each(function () {
                $(this).prop('checked', true);
            });
		} else {
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
    $('.form-steps.step4 .tab-content-wrap .error').remove();
    $("#cover-spin").show();
    var formData=$( "#question-generator" ).serialize()
    $.ajax({
        url: BASE_URL + '/question-generator/get-questions-id-learning-objectives-admin',
        type: 'POST',
        /*data: {
            '_token': $('meta[name="csrf-token"]').attr('content'),
            'strands_ids': $('#strand_id').val(),
            'learning_unit_id': $('#learning_unit').val(),
            'learning_objectives_id': $('#learning_objectives').val(),
            'question_type' : $('#test_type').val(),
            'dificulty_level' : $('#difficulty_lvl').val(),
            'difficulty_mode' : $('#difficulty_mode').val()
        },*/
        data:formData+'&exam_id='+{{$exam->id}},
        success: function(response) {
            var response = JSON.parse(JSON.stringify(response));
            if(response.data){
                var qLength=Object.keys(response.data.questionIds).length;
                var total_no_of_questions=parseInt($("#total_no_of_questions").val());

                if(qLength<total_no_of_questions)
                {
                    toastr.warning("Not enough questions into some objectives");
                }
                // Set input hidden into question ids
                $('#questionIds').val(response.data.questionIds);

                var tab_left='';
                var tab_right='';
                var qIndex=1;
                $.each(response.data.question_list, function(K,Q) {
                    var tab_active='';
                    var tab_active_contact='';
                    if(qIndex == 1){
                        tab_active='active';
                        tab_active_contact='show active';
                    }
                    var questionTitle=eval('Q.question_'+currentLanguage);
                    var difficultyLevelName=eval('Q.pre_configuration_difficulty_level.difficulty_level_name_'+currentLanguage);
                    var answer1Title=eval('Q.answers.answer1_'+currentLanguage);
                    var answer2Title=eval('Q.answers.answer2_'+currentLanguage);
                    var answer3Title=eval('Q.answers.answer3_'+currentLanguage);
                    var answer4Title=eval('Q.answers.answer4_'+currentLanguage);
                    tab_left+='<li class="nav-item">\
                        <input type="hidden" name="qIndex[]" value="'+Q.id+'" />\
                        <a class="nav-link '+tab_active+'" id="pills-contact-tab-'+qIndex+'" data-toggle="pill" href="#pills-contact-'+qIndex+'" role="tab" aria-controls="pills-contact-'+qIndex+'" aria-selected="false">Q'+qIndex+'</a>\
                    </li>';
                    tab_right+='<div class="tab-pane fade '+tab_active_contact+'" id="pills-contact-'+qIndex+'" role="tabpanel" aria-labelledby="pills-contact-tab-'+qIndex+'">\
                        <div class="d-flex pb-3">\
                        <div class="question-content pl-2">\
                            <div class="row">\
                                <div class="col-md-6"><b>{{__('languages.upload_document.strands')}}</b> : <span class="q-strand-name" data-q-strand-id="'+Q.objective_mapping.strand_id+'">'+Q.objective_mapping.strandName+'</span></div>\
                                <div class="col-md-6"><b>{{__('languages.upload_document.learning_units')}}</b> : <span class="q-learning-units-name" data-q-learning-units-id="'+Q.objective_mapping.learning_unit_id+'">'+Q.objective_mapping.learningUnitsName+'</span></div>\
                                <div class="col-md-6"><b>{{ __('languages.learning_objectives') }}</b> : <span class="q-learning-objectives-title" data-q-learning-objectives-id="'+Q.objective_mapping.learning_objectives_id+'">'+Q.objective_mapping.learningObjectivesTitle+'</span></div>\
                                <div class="col-md-6"><b>{{__('languages.questions.difficulty_level')}}</b> : <span class="q-difficulty-level" data-q-difficulty-level-id="'+Q.dificulaty_level+'">'+difficultyLevelName+'</span></div>\
                            </div>\
                            <div class="question-heading">\
                                <p class="question-title review-question-title">{{ __('languages.questions.question')}}:</p>\
                            </div>\
                            <div class="question-answer-content pl-2">\
                                <div class="question_content">\
                                    <label for="question-content" class="pl-3">'+questionTitle+'</label>\
                                </div>\
                                <div class="answer-content">\
                                    <div class="answer-review">\
                                        <span class="answer-detail-number">1</span>\
                                        <div class="review-answer-detail">'+answer1Title+'</div>\
                                    </div>\
                                    <div class="answer-review">\
                                        <span class="answer-detail-number">2</span>\
                                        <div class="review-answer-detail">'+answer2Title+'</div>\
                                    </div>\
                                    <div class="answer-review">\
                                        <span class="answer-detail-number">3</span>\
                                        <div class="review-answer-detail">'+answer3Title+'</div>\
                                    </div>\
                                    <div class="answer-review">\
                                        <span class="answer-detail-number">4</span>\
                                        <div class="review-answer-detail">'+answer4Title+'</div>\
                                    </div>\
                                </div>\
                            </div>\
                            <div class="d-flex align-items-center float-left mt-5">\
                                <button type="button" class="btn-search bg-pink want_a_hint" question-id="'+Q.id+'">{{__('languages.my_studies.want_a_hint')}}</button>\
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
                                $("#pills-tab li:eq(0) a").click();
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
            else
            {
                
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
        var refresh_question_strand_id = $('#refresh-question-strand-id').val();
        var refresh_question_learning_unit = $('#refresh-question-learning-unit').val();
        var refresh_question_learning_objectives = $('#refresh-question-learning-objectives').val();
        var refresh_question_difficulty_level = $('#refresh_question_difficulty_level').val();
        var refresh_question_difficulty_mode = $('#difficulty_mode').val();
        var refresh_question_test_type = $('#test_type').val();
        var old_question_ids = [];
        $('input[name="qIndex[]"]').each(function () {
            old_question_ids.push($(this).val());
        });
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
                    'test_type':refresh_question_test_type,
                    'old_question_ids':old_question_ids
                },
                success: function(response) {
                    if(response.data.question_list.length!=0){
                        var Q = response.data.question_list[0];
                        var questionTitle = eval('Q.question_'+currentLanguage);
                        var answer1Title = eval('Q.answers.answer1_'+currentLanguage);
                        var answer2Title = eval('Q.answers.answer2_'+currentLanguage);
                        var answer3Title = eval('Q.answers.answer3_'+currentLanguage);
                        var answer4Title = eval('Q.answers.answer4_'+currentLanguage);
                        var difficultyLevelName = eval('Q.pre_configuration_difficulty_level.difficulty_level_name_'+currentLanguage);

                        $("#refresh-question-id").val(Q.id);
                        $(".refresh-question .question-strand-detail span").attr('data-q-strand-id',Q.objective_mapping.strand_id);
                        $(".refresh-question .question-strand-detail span").text(Q.objective_mapping.strandName);

                        $(".refresh-question .question-learning-unit-detail span").attr('data-q-learning-units-id',Q.objective_mapping.learning_unit_id);
                        $(".refresh-question .question-learning-unit-detail span").text(Q.objective_mapping.learningUnitsName);

                        $(".refresh-question .question-learning-objectives-detail span").attr('data-q-learning-objectives-id',Q.objective_mapping.learning_objectives_id);
                        $(".refresh-question .question-learning-objectives-detail span").text(Q.objective_mapping.learningObjectivesTitle);

                        $(".refresh-question .question-difficulty-detail span").attr('data-q-difficulty-level-id',Q.dificulaty_level);
                        $(".refresh-question .question-difficulty-detail span").text(difficultyLevelName);

                        $(".refresh-question .question-heading p").text('Question:');

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
            toastr.error("Select Refresh Question Configuration");
        }
    });
    // this click to get Refresh Question and Replace Question in active Question 
    $(document).on("click",".replace-question",function () {
        var strandName = $(".refresh-question .question-strand-detail span").text();
        var learningUnitsName = $(".refresh-question .question-learning-unit-detail span").text();
        var learningObjectivesTitle = $(".refresh-question .question-learning-objectives-detail span").text();
        var difficulty_level_name_en = $(".refresh-question .question-difficulty-detail span").text();
        var question_en = $(".refresh-question .question_content label").html();
        var answer1_en = $(".refresh-question .answer-content .answer-review:eq(0) .review-answer-detail").html();
        var answer2_en = $(".refresh-question .answer-content .answer-review:eq(1) .review-answer-detail").html();
        var answer3_en = $(".refresh-question .answer-content .answer-review:eq(2) .review-answer-detail").html();
        var answer4_en = $(".refresh-question .answer-content .answer-review:eq(3) .review-answer-detail").html();
        var refresh_question_id = $("#refresh-question-id").val();
        if($("#pills-tab li input[value="+refresh_question_id+"]").length==0){
            $(".review-question-tab .tab-pane.show.active .q-strand-name").attr('data-q-strand-id',StrandId);
            $(".review-question-tab .tab-pane.show.active .q-strand-name").text(strandName);
            $(".review-question-tab .tab-pane.show.active .q-learning-units-name").attr('data-q-learning-units-id',learningUnitsId);
            $(".review-question-tab .tab-pane.show.active .q-learning-units-name").text(learningUnitsName);
            $(".review-question-tab .tab-pane.show.active .q-learning-objectives-title").attr('data-q-learning-objectives-id',learningObjectivesId);
            $(".review-question-tab .tab-pane.show.active .q-learning-objectives-title").text(learningObjectivesTitle);
            $(".review-question-tab .tab-pane.show.active .q-difficulty-level").attr('data-q-difficulty-level-id',difficulty_level);
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
        var StrandId = $(".refresh-question .question-strand-detail span").attr('data-q-strand-id');
        var strandName = $(".refresh-question .question-strand-detail span").text();
        var learningUnitsId = $(".refresh-question .question-learning-unit-detail span").attr('data-q-learning-units-id');
        var learningUnitsName = $(".refresh-question .question-learning-unit-detail span").text();
        var learningObjectivesId = $(".refresh-question .question-learning-objectives-detail span").attr('data-q-learning-objectives-id');
        var learningObjectivesTitle = $(".refresh-question .question-learning-objectives-detail span").text();
        var difficulty_level = $(".refresh-question .question-difficulty-detail span").attr('data-q-difficulty-level-id');
        var difficulty_level_name_en = $(".refresh-question .question-difficulty-detail span").text();
        var question_en = $(".refresh-question .question_content label").html();
        var answer1_en = $(".refresh-question .answer-content .answer-review:eq(0) .review-answer-detail").html();
        var answer2_en = $(".refresh-question .answer-content .answer-review:eq(1) .review-answer-detail").html();
        var answer3_en = $(".refresh-question .answer-content .answer-review:eq(2) .review-answer-detail").html();
        var answer4_en = $(".refresh-question .answer-content .answer-review:eq(3) .review-answer-detail").html();
        var refresh_question_id = $("#refresh-question-id").val();
        if($("#pills-tab li input[value="+refresh_question_id+"]").length == 0){
            var tab_left = '';
            var tab_right = '';
            var tab_active = '';
            var tab_active_contact = '';

            var getIndex = $("#pills-tab li").last().find('a').attr('aria-controls');
            var qIndex = parseInt(getIndex.replace('pills-contact-',''));
            qIndex = qIndex+1;

            tab_left+='<li class="nav-item">\
                <input type="hidden" name="qIndex[]" value="'+refresh_question_id+'" />\
                <a class="nav-link '+tab_active+'" id="pills-contact-tab-'+qIndex+'" data-toggle="pill" href="#pills-contact-'+qIndex+'" role="tab" aria-controls="pills-contact-'+qIndex+'" aria-selected="false">Q'+qIndex+'</a>\
            </li>';
            tab_right+='<div class="tab-pane fade '+tab_active_contact+'" id="pills-contact-'+qIndex+'" role="tabpanel" aria-labelledby="pills-contact-tab-'+qIndex+'">\
                <div class="d-flex pb-3">\
                <div class="question-content pl-2">\
                    <div class="row">\
                        <div class="col-md-6"><b>Strand</b> : <span class="q-strand-name" data-q-strand-id="'+StrandId+'">'+strandName+'</span></div>\
                        <div class="col-md-6"><b>Learning Units</b> : <span class="q-learning-units-name" data-q-learning-units-id="'+learningUnitsId+'">'+learningUnitsName+'</span></div>\
                        <div class="col-md-6"><b>Learning Objectives</b> : <span class="q-learning-objectives-title" data-q-learning-objectives-id="'+learningObjectivesId+'">'+learningObjectivesTitle+'</span></div>\
                        <div class="col-md-6"><b>Difficulty Level</b> : <span class="q-difficulty-level" data-q-difficulty-level-id="'+difficulty_level+'">'+difficulty_level_name_en+'</span></div>\
                    </div>\
                    <div class="question-heading">\
                        <p class="question-title review-question-title">{{ __('languages.questions.question')}}:</p>\
                    </div>\
                    <div class="question-answer-content pl-2">\
                        <div class="question_content">\
                            <label for="question-content" class="pl-3">'+question_en+'</label>\
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
        }
        else
        {
            toastr.error("This Question Already Exits");
        }
            
    });

    // this click to get Strands, Learning Units, Learning Objectives and Difficulty Level set in Refresh Question
    $(document).on("click","#pills-tab li a",function () {
        var getIndex = $(this).attr('aria-controls');
        var qIndex = parseInt(getIndex.replace('pills-contact-',''));
        var qId = qIndex;
        var q_strand_id = $("#pills-tabContent #pills-contact-"+qId).find('.q-strand-name').attr('data-q-strand-id');
        var q_learning_units_id = $("#pills-tabContent #pills-contact-"+qId).find('.q-learning-units-name').attr('data-q-learning-units-id');
        var q_learning_objectives_id = $("#pills-tabContent #pills-contact-"+qId).find('.q-learning-objectives-title').attr('data-q-learning-objectives-id');
        var q_difficulty_level_id = $("#pills-tabContent #pills-contact-"+qId).find('.q-difficulty-level').attr('data-q-difficulty-level-id');

        $("#refresh-question-strand-id").val(q_strand_id).multiselect('rebuild');
        $("#refresh-question-learning-unit").val(q_learning_units_id).multiselect('rebuild');
        $("#refresh-question-learning-objectives").val(q_learning_objectives_id).multiselect('rebuild');
        $("#refresh_question_difficulty_level").val(q_difficulty_level_id).multiselect('rebuild');
        console.log(q_difficulty_level_id);
    });
})

function checkValidation(currentStep) {
    var formIsValid = 0;
    $('.form-steps.step'+currentStep+' label.error').remove();
    switch('step_'+currentStep){
        case 'step_1':
            $('.form-steps.step'+currentStep).find('[name=use_of_modes],[name=test_type],[name=title],[name=start_date],[name=end_date],[name="difficulty_lvl[]"]').each(function(){
                var element = $(this).closest('.form-group').css('display');
                if($.trim($(this).val()) == '' && element != 'none' ){
                    var label = $(this).closest('.form-group').find('label:eq(0)').text();
                    $(this).closest('.form-group').append('<label class="error">Please enter '+label+'</label>');
                    formIsValid++;
                }
            });
            break;
        case 'step_2':
            $('.form-steps.step'+currentStep).find('[name="schoolIds[]"]').each(function(){
                var element = $(this).closest('.form-group').css('display');
                if($.trim($(this).val()) == '' && element != 'none'){
                    var label = $(this).closest('.form-group').find('label:eq(0)').text();
                    $(this).closest('.form-group').append('<label class="error">Please select Schools</label>');
                    formIsValid++;
                }
            });
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
        $(".learning_objective_checkbox").parent().find('.get_no_of_question_learning_objectives').prop('disabled',false);
        $(".learning_objective_checkbox:not(:checked)").parent().find('select,.get_no_of_question_learning_objectives').prop('disabled',true);
        $(".learning_objective_checkbox:checked").parent().find('select').multiselect('enable');
        $(".learning_objective_checkbox:not(:checked)").parent().find('select').multiselect('disable');
         total_no_of_questions();
        $("#pills-tab li:eq(1) a").click();
        $("#pills-tab li:eq(0) a").click();
        total_no_of_questions();
    })

    function total_no_of_questions() {
        $("#total_no_of_questions").val(0);
        if($('.get_no_of_question_learning_objectives:not(:disabled)').length!=0)
        {
         var total_data = $('.get_no_of_question_learning_objectives:not(:disabled)').map((_,el) => el.value).get();
            var total_data_sum = total_data.reduce((x, y) => parseInt(x) + parseInt(y));
            $("#total_no_of_questions").val(total_data_sum);
        }
    }
</script>
@include('backend.layouts.footer')
@endsection