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
    placeholder: SELECT_DIFFICULTY_LEVEL,
    includeSelectAllOption: true,
    enableFiltering: true,
}
$(function (){
    $("#strand_id,#learning_unit").multiselect({
        enableHTML: true,
        templates: {
            filter: '<li class="multiselect-item multiselect-filter"><div class="input-group mb-3"><div class="input-group-prepend"><span class="input-group-text"><i class="fa fa-search"></i></span></div><input class="form-control multiselect-search" type="text" /></div></li>',
            filterClearBtn:
                '<span class="input-group-btn"><button class="btn btn-default multiselect-clear-filter" type="button"><i class="fa fa-times"></i></button></span>',
        },
        columns: 1,
        placeholder: SELECT_STRAND,
        includeSelectAllOption: true,
        enableFiltering: true,
    });

    $(document).on('change', '.get_no_of_question_learning_objectives', function(e) {
        var minimum_question_per_skill_single = parseInt($(this).attr('min'));
        var maximum_question_per_skill_single = parseInt($(this).attr('max'));
        var noOfQuestionEntered = parseInt(e.target.value);
        if(noOfQuestionEntered != ""){
            if(noOfQuestionEntered >= minimum_question_per_skill_single && noOfQuestionEntered <= maximum_question_per_skill_single) {
            }else{
                toastr.error("Minimum question per skill required is :"+minimum_question_per_skill_single+" Maximum question per skill required is :"+maximum_question_per_skill_single);
                $(this).val('');
            }
        }
    });

    $(document).on('click', '.generate-self-learning', function() {
        var currentStep = $(this).attr('data-stepid');
        if(checkValidation(currentStep) && currentStep == 1){
            getLearningObjectivesOptionList();
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
        $("#cover-spin").show();
        var classNameLearningUnit = '#learning_unit';
        if($(this).attr('id') == 'refresh-question-strand-id'){
            classNameLearningUnit = '#refresh-question-learning-unit';
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
	 * USE : Get Multiple Learning units based on multiple learning units ids
	 * **/
	$(document).on('change', '#learning_unit', function() {
        $("#cover-spin").show();
        $('.learning_unit_error_msg').text('');
        $('.selection-learning-objectives-section').html('');
		$strandIds = $('#strand_id').val();
		$learningUnitIds = $('#learning_unit').val();
		if($learningUnitIds != ""){
            $('.generate-self-learning').prop('disabled', false);
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
                                            html += '<input type="text" name="learning_unit['+this.learning_unit_id+'][learning_objective]['+this.id+'][get_no_of_question_learning_objectives]" value="'+data.data.getNoOfQuestionPerLearningObjective[this.id]+'" class="get_no_of_question_learning_objectives" min="'+data.data.getNoOfQuestionPerLearningObjective[this.id]+'" max="'+maximum_question_per_skill+'">\
                                        </div>';
							});
                            $('.selection-learning-objectives-section').html(html);
						}else{
							$('.selection-learning-objectives-section').html(LEARNING_OBJECTIVES_NOT_AVAILABLE);
						}
					}else{
						$('.selection-learning-objectives-section').html(LEARNING_OBJECTIVES_NOT_AVAILABLE);
					}
                    total_no_of_questions();
                    $("#cover-spin").hide();
				},
				error: function(response) {
					ErrorHandlingMessage(response);
				}
			});
		}else{
            $('.generate-self-learning').prop('disabled', true);
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
</script>

<script type="text/javascript">
    var second = 0;
    var PerQuestionSecond=0;
    var timer = '';
    var PerQuestionTimer = '';
    var QuestionSecondInterval = '';
    function examTimer() {
        var myTimer = setInterval(myClock, 1000);
        function myClock(){
            timer = secondsTimeSpanToHMS(++second);
            $('#ExamTimer').text(timer);
        }
    }

    function secondsTimeSpanToHMS(second) {
        var h = Math.floor(second / 3600); //Get whole hoursecond
        second -= h * 3600;
        var m = Math.floor(second / 60); //Get remaining minutesecond
        second -= m * 60;
        return h + ":" + (m < 10 ? '0' + m : m) + ":" + (second < 10 ? '0' + second : second); //zero padding on minutes and seconds
    }

    $(document).ready(function () {
        $(".learning_objective_checkbox").parent().find('.get_no_of_question_learning_objectives').prop('disabled',false);
        $(".learning_objective_checkbox:not(:checked)").parent().find('select,.get_no_of_question_learning_objectives').prop('disabled',true);
        $(".learning_objective_checkbox:checked").parent().find('select').multiselect('enable');
        $(".learning_objective_checkbox:not(:checked)").parent().find('select').multiselect('disable');
        total_no_of_questions();

        $(document).on("change",".get_no_of_question_learning_objectives",function () {
            if($.trim($(this).val())=="" || $.trim($(this).val())==0){
                var minimum_question_per_skill = $(this).attr('min');
                $(this).val(minimum_question_per_skill);
            }
            total_no_of_questions();
        });

        // USE : Fire event after selection answer
        $(document).on("click","#nextquestion",function () {
            $("#cover-spin").show();
            $('#current_question_taking_timing').val(PerQuestionTimer);
            var formData = $("#attempt-exams").serialize();
            clearInterval(QuestionSecondInterval);
            $.ajax({
                url: BASE_URL + '/generate-question/self-learning/test/next-question',
                type: 'POST',
                data:formData,
                success: function(response) {
                    var response = JSON.parse(JSON.stringify(response));
                    if(response.status === 'success'){
                        $("#cover-spin").hide();
                        $('#nextquestionarea').html(response.data.question_html);
                        MathJax.Hub.Queue(["Typeset",MathJax.Hub]);
                        
                        // Set new per question timer
                        PerQuestionSecond = 0;
                        PerQuestionTimer = '';
                        QuestionSecondInterval = setInterval(function (){
                            PerQuestionTimer = secondsTimeSpanToHMS(++PerQuestionSecond);
                        }, 1000);
                        
                        //$("#question-generator button[type=submit]").prop('disabled',true);
                        //toastr.success(SELF_LEARNING_CREATED_SUCCESSFULLY);
                        //window.location.replace(BASE_URL+'/'+response.data.redirectUrl);
                    }else{
                        $("#cover-spin").hide();
                        toastr.error(response.message);
                    }
                },
                error: function(response) {
                    ErrorHandlingMessage(response);
                }
            });
        });


        // USE : Complete all question then store test into databse
        $(document).on("click","#submit-self-learning-test",function () {
            $("#cover-spin").show();
            $(this).prop('disabled',true);
            $('#current_question_taking_timing').val(PerQuestionTimer);
            $('#exam_taking_timing').val(timer);
            var formData = $("#attempt-exams").serialize();
            $.ajax({
                url: BASE_URL + '/self-learning/test/save',
                type: 'POST',
                data:formData,
                success: function(response) {
                    var response = JSON.parse(JSON.stringify(response));                
                    if(response.status === 'success'){
                        $("#cover-spin").hide();
                        toastr.success('Self Learning Test Submitted Successfully');
                        window.location.replace(BASE_URL+'/'+response.data.redirectUrl);
                    }else{
                        $("#cover-spin").hide();
                        toastr.error(response.message);
                    }
                },
                error: function(response) {
                    ErrorHandlingMessage(response);
                }
            });
        });

        /**
         * USE Check validation before click on the next button
         */
        $(document).on("change",".checkanswer",function () {
            if($(".checkanswer:checked").length != 0){
                $('#nextquestion,#submit-self-learning-test').prop('disabled',false);
            }
        });

        /**
         * USE : Get selected language wise display question content
         */
        $(document).on("change","#student-select-attempt-exam-language",function () {
            $("#cover-spin").show();
            var formData = $("#attempt-exams").serialize();
            $.ajax({
                url: BASE_URL + '/generate-question/self-learning/test/change-language',
                type: 'POST',
                data:formData,
                success: function(response) {
                    var response = JSON.parse(JSON.stringify(response));                
                    if(response.status === 'success'){
                        $("#cover-spin").hide();
                        $('#nextquestionarea').html(response.data.question_html);
                        MathJax.Hub.Queue(["Typeset",MathJax.Hub]);
                        
                        // Set new per question timer
                        PerQuestionSecond = 0;
                        PerQuestionTimer = '';
                        QuestionSecondInterval = setInterval(function (){
                            PerQuestionTimer = secondsTimeSpanToHMS(++PerQuestionSecond);
                        }, 1000);
                        
                        //$("#question-generator button[type=submit]").prop('disabled',true);
                        //toastr.success(SELF_LEARNING_CREATED_SUCCESSFULLY);
                        //window.location.replace(BASE_URL+'/'+response.data.redirectUrl);
                    }else{
                        $("#cover-spin").hide();
                        toastr.error(response.message);
                    }
                },
                error: function(response) {
                    ErrorHandlingMessage(response);
                }
            });
        });
    });

    function total_no_of_questions() {
        $("#total_no_of_questions").val(0);
        if($('.get_no_of_question_learning_objectives:not(:disabled)').length != 0){
            var total_data = $('.get_no_of_question_learning_objectives:not(:disabled)').map((_,el) => el.value).get();
            var total_data_sum = total_data.reduce((x, y) => parseInt(x) + parseInt(y));
            $("#total_no_of_questions").val(total_data_sum);
        }
    }

    function checkValidation(currentStep) {
        return true;
    }

    /**
     * USE : Get the questions from AIAPi
     */
    function getLearningObjectivesOptionList(){
        $("#cover-spin").show();
        var formData = $("#question-generator").serialize();
        $.ajax({
            url: BASE_URL + '/generate-question/self-learning/test',
            type: 'POST',
            data:formData,
            success: function(response) {
                var response = JSON.parse(JSON.stringify(response));                
                if(response.status === 'success'){
                    examTimer();
                    $("#cover-spin").hide();
                    $('#self-learning-config-section').html(response.data.question_html);
                    MathJax.Hub.Queue(["Typeset",MathJax.Hub]);

                    QuestionSecondInterval = setInterval(function (){
                        PerQuestionTimer = secondsTimeSpanToHMS(++PerQuestionSecond);
                    }, 1000);

                    //$("#question-generator button[type=submit]").prop('disabled',true);
                    //toastr.success(SELF_LEARNING_CREATED_SUCCESSFULLY);
                    //window.location.replace(BASE_URL+'/'+response.data.redirectUrl);
                }else{
                    $("#cover-spin").hide();
                    toastr.error(response.message);
                }
            },
            error: function(response) {
                ErrorHandlingMessage(response);
            }
        });
    }    
</script>