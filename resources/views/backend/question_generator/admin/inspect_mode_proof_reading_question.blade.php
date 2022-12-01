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
                            <h2 class="mb-4 main-title">{{__('languages.test_exercise_inspect_mode')}}</h2>
                        </div>
                        <hr class="blue-line">
                        <a href="javascript:void(0);" class="btn-back dark-blue-btn btn btn-primary mb-4" id="backButton">{{__('languages.back')}}</a>
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
                <form name="question-generator" id="question-generator" class="inspect-mode-question-wizard" action="{{ route('super-admin.generate-questions') }}" method="POST">
                    @csrf
                    <div class="sm-add-user-sec card">
                        <div class="select-option-sec pb-5 card-body">
                            <div id="wizard">
                                <div class="question-generator-option-headings inspect-mode mb-3">
                                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 pl-0 pr-0">
                                        <ul class="form-tab">
                                            <li class="step-headings section-step1 admin-tab tab_active" data-tabid="1">1. {{__('languages.question_generators_menu.configuration')}}</li>
                                            <li class="step-headings section-step2 admin-tab" data-tabid="2">2.{{__('languages.question_generators_menu.select_learning_objectives')}}</li>
                                            <li class="step-headings section-step3 admin-tab" data-tabid="3">3.{{__('languages.question_generators_menu.review_of_questions')}}</li>
                                        </ul>
                                    </div>
                                </div>
                                <section class="form-steps step1">
                                    <div class="form-row">
                                        <div class="form-group col-md-6 mb-50">
                                            <label class="text-bold-600">{{__('languages.question_generators_menu.test_mode')}}</label>
                                            <select name="test_type" class="form-control select-option" id="test_type">
                                                <option value="test" @if(request()->get('test_type') == 'test') selected @endif>{{__('languages.txx')}}</option> 
                                                <option value="exercise" @if(request()->get('test_type') == 'exercise') selected @endif>{{__('languages.exx')}}</option>
                                                <option value="self_learning" @if(request()->get('test_type') == 'self_learning') selected @endif>{{__('languages.sxx')}}</option>
                                                <!-- <option value="testing_zone" @if(request()->get('test_type') == 'testing_zone') selected @endif>{{__('languages.testing_zone')}}</option> -->
                                                <option value="seed" @if(request()->get('test_type') == 'seed') selected @endif>{{__('languages.seed')}}</option>
                                            </select>
                                        </div>

                                        <div class="form-group col-md-6 mb-50">
                                            <label>{{__('languages.difficulty_mode')}}</label>
                                            <select name="difficulty_mode" class="form-control select-option" id="difficulty_mode">
                                                <option value="manual">{{__('languages.manual')}}</option>
                                            </select>
                                        </div>

                                        <div class="form-group col-md-6 mb-50">
                                            <label>{{__('languages.questions.difficulty_level')}}</label>
                                            <select name="difficulty_lvl[]" class="form-control select-option" id="difficulty_lvl" multiple>
                                                @if(!empty($difficultyLevels))
                                                @foreach($difficultyLevels as $difficultyLevel)
                                                <option value="{{$difficultyLevel->difficulty_level}}" @if($difficultyLevel->difficulty_level == 2) selected @endif>{{$difficultyLevel->{'difficulty_level_name_'.app()->getLocale()} }}</option>
                                                @endforeach
                                                @endif								
                                            </select>
                                            <span name="err_difficulty_level"></span>
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
                                            <input type="checkbox" name="all_learning_objective_checkbox" value="" class="all_learning_objective_checkbox" checked> {{__('languages.question_generators_menu.select_all')}}
                                        </div>
                                        @foreach ($LearningObjectives as $learningObjectivesKey => $learningObjectives)

                                        <!-- Get count of total no of question per Learning Objectives -->
                                        @php
                                            $noOfQuestionPerLearningObjective = App\Helpers\Helper::CountAllQuestionPerLearningObjective($learningObjectives->learning_unit_id,$learningObjectives->id);
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
                                            <input type="text" name="learning_unit[{{$learningObjectives->learning_unit_id}}][learning_objective][{{ $learningObjectives->id }}][get_no_of_question_learning_objectives]" value="{{ $noOfQuestionPerLearningObjective }}" class="get_no_of_question_learning_objectives" max="{{$noOfQuestionPerLearningObjective}}">
                                        </div>
                                        @endforeach
                                        @endif
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
                                    <div class="d-flex review-question-main tab-content-wrap">
                                    </div>
                                    <div class="form-row select-data float-left">
                                        <div class="sm-btn-sec form-row">
                                            <div class="form-group mb-50 btn-sec">
                                                <button type="button" class="blue-btn btn btn-primary previous-button previous_btn_step_3" data-stepid="3">{{__('languages.question_generators_menu.previous')}}</button>
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
    var currentLanguage='{{app()->getLocale()}}';
    var multiselectArray={
        nSelectedText: 'Selecciones',
        enableHTML: true,
        templates: {
            filter: '<li class="multiselect-item multiselect-filter"><div class="input-group mb-3"><div class="input-group-prepend"><span class="input-group-text"><i class="fa fa-search"></i></span></div><input class="form-control multiselect-search" type="text" /></div></li>',
            filterClearBtn: '<span class="input-group-btn"><button class="btn btn-default multiselect-clear-filter" type="button"><i class="fa fa-times"></i></button></span>'

        },
        column: 1,
        placeholder: SELECT_DIFFICULTY_LEVEL,
        includeSelectAllOption: true,
        enableFiltering: true,
    }
$(function (){
    /**
     * USE : Event trigger on change no of question per get question textbox
     */
    $(document).on('change', '.get_no_of_question_learning_objectives', function(e) {
        if(isNegative($(this).val())){
            $(this).val(0);
        }
        var maximum_question_per_objectives = parseInt($(this).attr('max'));
        var noOfQuestionEntered =parseInt(e.target.value);
        if(noOfQuestionEntered !=""){
            if(noOfQuestionEntered <= maximum_question_per_objectives) {
            }else{
                toastr.error("Your Selected Learning Objectives have only questions :"+maximum_question_per_objectives);
                $(this).val(maximum_question_per_objectives);
            }
        }
        total_no_of_questions();
    });

    if($('#difficulty_mode').val() == 'manual'){
        // Set default difficulty level for selected first steps
        setDefaultDifficultyLevels();
    }
    
    // event fire on click step button
    $(document).on('click', '.step-headings', function() {
        var currentStep = parseInt($(this).attr('data-tabid'))-1;
        if(currentStep!=0){
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
            if(currentStep == 1){
                ProofReadingQuestions();
            }
            if(currentStep == 2){
                getLearningObjectivesOptionList();
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
            // Set default difficulty level for selected first steps
            setDefaultDifficultyLevels();
            $(".learning_objective_checkbox").parent().find('select').multiselect('disable');
            $(".learning_objective_checkbox:checked").parent().find('select').multiselect('enable');
        }else{
            $(".learning_objective_checkbox").parent().find('select').multiselect('disable');
        }
    });

    /**
	 * USE : Get Learning Units from multiple strands
	 * **/
	$(document).on('change', '#strand_id,#refresh-question-strand-id', function() {
        $("#cover-spin").show();
        var classNameLearningUnit = '#learning_unit';
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
					'learning_unit_id': $learningUnitIds,
                    'test_type' : $('#test_type').find(":selected").val(),
                    'difficulty_level' : $('#difficulty_lvl').val(),
                    'isInspectMode' : true
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
                                            html += '<input type="text" name="learning_unit['+this.learning_unit_id+'][learning_objective]['+this.id+'][get_no_of_question_learning_objectives]" value="'+data.data.getNoOfQuestionPerLearningObjective[this.id]+'" class="get_no_of_question_learning_objectives" max="'+data.data.getNoOfQuestionPerLearningObjective[this.id]+'">\
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
            $('.learning_unit_error_msg').text(VALIDATIONS.PLEASE_SELECT_LEARNING_OBJECTIVES);
			$('#learning_objectives').html('');
			$('#learning_objectives').multiselect("rebuild");
            total_no_of_questions();
		}
	});

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

    /**
     * USE : Trigger on change test type dropdown
     */
    $(document).on("change", "#test_type", function (){
        ProofReadingQuestions();
    });
});

/**
 * This event trigger on change test type and get the second steps html
 */
function ProofReadingQuestions(){
    $("#cover-spin").show();
    var formData = $("#question-generator").serializeArray();
    formData.push({name:"action",value:"get_learning_objectives_list"});
    $.ajax({
        url: BASE_URL + '/question-wizard/proof-reading-question',
        type: 'POST',
        data:formData,
        success: function(response) {
            var response = JSON.parse(JSON.stringify(response));
            $('.selection-learning-objectives-section').html(response.data);
            //setDefaultDifficultyLevels();
            total_no_of_questions();
            $('.learning_objectives_difficulty_level').multiselect(multiselectArray);
            $("#cover-spin").hide();
        },
        error: function(response) {
            ErrorHandlingMessage(response);
        }
    });
}

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
    $('.form-steps.step3 #pills-tab').html('');
    $('.form-steps.step3 #pills-tabContent').html('');
    $('.form-steps.step3 .tab-content-wrap .error').remove();
    $("#cover-spin").show();
    var formData = $("#question-generator").serialize()
    $.ajax({
        url: BASE_URL + '/question-wizard/proof-reading-question',
        type: 'POST',
        data:formData,
        success: function(response) {
            var response = JSON.parse(JSON.stringify(response));
            $("#question-generator button[type=submit]").prop('disabled',false);
            var response = JSON.parse(JSON.stringify(response));
            if(response.data){
                var qLength = Object.keys(response.data.questionIds).length;
                var total_no_of_questions = parseInt($("#total_no_of_questions").val());
                if(qLength < total_no_of_questions){
                    toastr.warning(NOT_ENOUGH_QUESTIONS_INTO_SOME_OBJECTIVES);
                }
                // Set input hidden into question ids
                $('#questionIds').val(response.data.questionIds);
                $(".review-question-main").html(response.data.html);
                MathJax.Hub.Queue(["Typeset",MathJax.Hub]);
                $('#cover-spin').hide();
            }else{
                $('.form-steps').hide();
                $('.step-headings').removeClass('tab_active');
                $('.section-step2').addClass('tab_active');
                $('.step2').show();
            }
        },
        error: function(response) {
            ErrorHandlingMessage(response);
            $('.form-steps.step3 .tab-content-wrap').append('<label class="error">'+PLEASE_RESELECT_QUESTION_CONFIGURATION+'</label>')
            $("#question-generator button[type=submit]").prop('disabled',true);
            $('.form-steps').hide();
            $('.step-headings').removeClass('tab_active');
            $('.section-step2').addClass('tab_active');
            $('.step2').show();
        }
    });
}

function checkValidation(currentStep) {
    var formIsValid = 0;
    $('.form-steps.step'+currentStep+' label.error').remove();
    switch('step_'+currentStep){
        case 'step_1':
            $('.form-steps.step'+currentStep).find('[name=test_type],[name="difficulty_lvl[]"]').each(function(){
                var element = $(this).closest('.form-group').css('display');
                if($.trim($(this).val()) == '' && element != 'none' ){
                    var label = $(this).closest('.form-group').find('label:eq(0)').text();
                    $(this).closest('.form-group').append('<label class="error">'+PLEASE_ENTER+label+'</label>');
                    formIsValid++;
                }
            });
            break;
        case 'step_2':
            break;
        case 'step_3':
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

$(document).ready(function () {
    $(".learning_objective_checkbox").parent().find('.get_no_of_question_learning_objectives').prop('disabled',false);
    $(".learning_objective_checkbox:not(:checked)").parent().find('select,.get_no_of_question_learning_objectives').prop('disabled',true);
    $(".learning_objective_checkbox:checked").parent().find('select').multiselect('enable');
    $(".learning_objective_checkbox:not(:checked)").parent().find('select').multiselect('disable');
        total_no_of_questions();
});

function total_no_of_questions() {
    $("#total_no_of_questions").val(0);
    if($('.get_no_of_question_learning_objectives:not(:disabled)').length != 0){
        var total_data = $('.get_no_of_question_learning_objectives:not(:disabled)').map((_,el) => el.value).get();
        var total_data_sum = total_data.reduce((x, y) => parseInt(x) + parseInt(y));
        $("#total_no_of_questions").val(total_data_sum);
    }
}

/**
 * USE : Check number is negative or positive
 */
function isNegative(num) {
  if (Math.sign(num) === -1) {
    return true;
  }
  return false;
}
</script>
@include('backend.layouts.footer')
@endsection