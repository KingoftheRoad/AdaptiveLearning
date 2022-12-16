//Dropzone Define Globally
Dropzone.autoDiscover = false;

// On full page load after that loader is hide on the screen
$(window).on("load", function () {
    $(".loader").fadeOut("slow");
});

$(function () {
    OnClickEvent.init();
    Validation.init();
    OnChangeEvent.init();
    OnPageLoadEvent.init();
});
var questionNo = 1;
var attempt_ans = 0;
var question_old_data_new = new Array();
var question_position = new Array();
var wrong_question_old_data_new = new Array();
var attempt_first_trial_data_new = new Array();
var wrong_old_data_new = 0;
var questiontype = 0;
var isReAttempt = true;
if (typeof file_type == "undefined") {
    var file_type = "";
}
// OnPageLoad Jquery Events
OnPageLoadEvent = {
    init: function () {
        /**
         * USE : Deafult math formula update to text to image
         */
        updateMathHtml();
        if ($("#nextquestionarea").length) {
            //updateMathHtmlById("nextquestionarea");
            MathJax.Hub.Queue(["Typeset", MathJax.Hub]);
        }

        //Default remember tab selected into student panel and teacher panel
        $(".test-tab").removeClass("active");
        $(".tab-pane").removeClass("active");
        if ($.cookie("PreviousTab")) {
            $("#tab-" + $.cookie("PreviousTab")).addClass("active");
            $("#" + $.cookie("PreviousTab")).addClass("active");
        } else {
            $("#tab-exercise").addClass("active");
            $("#exercise").addClass("active");
        }

        // On Page load set question bank management display knowledge node section
        if (
            (typeof pageName != "undefined" &&
                pageName == "editQuestionBank") ||
            (typeof isValidation != "undefined" && isValidation == false)
        ) {
            knowledgeNode($("#naming_structure_code").val());
        }

        $(
            ".select-search,#nodeModal #main_node_id,#addNodesForm #sub_node_id,#updateNodesForm #sub_node_id,#nodeModal #parent-node-id,#question_filter_grade,#question_filter_difficulty,#question_filter_question_type,#question_filter_status,#user_filter_school,#user_filter_role,#user_filter_grade,#role,#school_id,#grade_id,#status,#add_student_group_grade,#add_student_group_status,#School,#filter_test_template_difficult_lvl,#filter_test_template_template_type,#template_type,#difficulty_level,#learningReportStrand,#reportLearningType,#pass_only_and_or,#curriculum,#test_type,#select-report-date,#select-no-of-per-trials-question,#difficulty_mode,#select-display-hints,#select-display-full-solutions,#select-display-pr-answer-hints,#select-randomize-answers,#select-randomize-order,#test_start_time,#test_end_time,#use_of_modes,#assignStudentIntoGroup,#leaderboard_type,#learning_tutor_language_id,#is_approved_question,.performance_exam_id,#current_curriculum_year,#curriculum_year"
        ).select2({
            width: "100%",
        });

        $("#peer-group-options").select2({
            width: "30%",
        });

        $(
            "#language_id,#grade-id,#strand-id,#learning-unit,#learning-objectives"
        ).multiselect({
            enableHTML: true,
            templates: {
                filter: '<li class="multiselect-item multiselect-filter"><div class="input-group mb-3"><div class="input-group-prepend"><span class="input-group-text"><i class="fa fa-search"></i></span></div><input class="form-control multiselect-search" type="text" /></div></li>',
                filterClearBtn:
                    '<span class="input-group-btn"><button class="btn btn-default multiselect-clear-filter" type="button"><i class="fa fa-times"></i></button></span>',
            },
            columns: 1,
            placeholder: SELECT_PEER_GROUP,
            search: true,
            selectAll: true,
            includeSelectAllOption: true,
            enableFiltering: true,
        });

        $("#question-generator-peer-group-options").multiselect({
            enableHTML: true,
            templates: {
                filter: '<li class="multiselect-item multiselect-filter"><div class="input-group mb-3"><div class="input-group-prepend"><span class="input-group-text"><i class="fa fa-search"></i></span></div><input class="form-control multiselect-search" type="text" /></div></li>',
                filterClearBtn:
                    '<span class="input-group-btn"><button class="btn btn-default multiselect-clear-filter" type="button"><i class="fa fa-times"></i></button></span>',
            },
            columns: 1,
            placeholder: SELECT_PEER_GROUP,
            search: true,
            selectAll: true,
            includeSelectAllOption: true,
            enableFiltering: true,
        });

        $("#LearningUnits").select2();

        $("#add-schools").multiselect({
            enableHTML: true,
            templates: {
                filter: '<li class="multiselect-item multiselect-filter"><div class="input-group mb-3"><div class="input-group-prepend"><span class="input-group-text"><i class="fa fa-search"></i></span></div><input class="form-control multiselect-search" type="text" /></div></li>',
                filterClearBtn:
                    '<span class="input-group-btn"><button class="btn btn-default multiselect-clear-filter" type="button"><i class="fa fa-times"></i></button></span>',
            },
            column: 1,
            placeholder: SELECT_SCHOOL,
            includeSelectAllOption: true,
            enableFiltering: true,
        });

        $("#refresh-question-strand-id").multiselect({
            enableHTML: true,
            templates: {
                filter: '<li class="multiselect-item multiselect-filter"><div class="input-group mb-3"><div class="input-group-prepend"><span class="input-group-text"><i class="fa fa-search"></i></span></div><input class="form-control multiselect-search" type="text" /></div></li>',
                filterClearBtn:
                    '<span class="input-group-btn"><button class="btn btn-default multiselect-clear-filter" type="button"><i class="fa fa-times"></i></button></span>',
            },
            column: 1,
            placeholder: SELECT_STRAND,
            includeSelectAllOption: true,
            enableFiltering: true,
        });

        $("#refresh-question-learning-unit").multiselect({
            enableHTML: true,
            templates: {
                filter: '<li class="multiselect-item multiselect-filter"><div class="input-group mb-3"><div class="input-group-prepend"><span class="input-group-text"><i class="fa fa-search"></i></span></div><input class="form-control multiselect-search" type="text" /></div></li>',
                filterClearBtn:
                    '<span class="input-group-btn"><button class="btn btn-default multiselect-clear-filter" type="button"><i class="fa fa-times"></i></button></span>',
            },
            column: 1,
            placeholder: SELECT_LEARNING_UNITS,
            includeSelectAllOption: true,
            enableFiltering: true,
        });

        $("#refresh-question-learning-objectives").multiselect({
            enableHTML: true,
            templates: {
                filter: '<li class="multiselect-item multiselect-filter"><div class="input-group mb-3"><div class="input-group-prepend"><span class="input-group-text"><i class="fa fa-search"></i></span></div><input class="form-control multiselect-search" type="text" /></div></li>',
                filterClearBtn:
                    '<span class="input-group-btn"><button class="btn btn-default multiselect-clear-filter" type="button"><i class="fa fa-times"></i></button></span>',
            },
            column: 1,
            placeholder: SELECT_LEARNING_OBJECTIVES,
            includeSelectAllOption: true,
            enableFiltering: true,
        });

        $(".learning_objectives_difficulty_level").multiselect({
            enableHTML: true,
            templates: {
                filter: '<li class="multiselect-item multiselect-filter"><div class="input-group mb-3"><div class="input-group-prepend"><span class="input-group-text"><i class="fa fa-search"></i></span></div><input class="form-control multiselect-search" type="text" /></div></li>',
                filterClearBtn:
                    '<span class="input-group-btn"><button class="btn btn-default multiselect-clear-filter" type="button"><i class="fa fa-times"></i></button></span>',
            },
            column: 1,
            placeholder: SELECT_DIFFICULTY_LEVEL,
            includeSelectAllOption: true,
            enableFiltering: true,
        });

        $("#difficulty_lvl, #refresh_question_difficulty_level").multiselect({
            enableHTML: true,
            templates: {
                filter: '<li class="multiselect-item multiselect-filter"><div class="input-group mb-3"><div class="input-group-prepend"><span class="input-group-text"><i class="fa fa-search"></i></span></div><input class="form-control multiselect-search" type="text" /></div></li>',
                filterClearBtn:
                    '<span class="input-group-btn"><button class="btn btn-default multiselect-clear-filter" type="button"><i class="fa fa-times"></i></button></span>',
            },
            column: 1,
            placeholder: SELECT_DIFFICULTY_LEVEL,
            includeSelectAllOption: true,
            enableFiltering: true,
        });

        $("#subject_id.multiplesubject_id").multiselect({
            enableHTML: true,
            templates: {
                filter: '<li class="multiselect-item multiselect-filter"><div class="input-group mb-3"><div class="input-group-prepend"><span class="input-group-text"><i class="fa fa-search"></i></span></div><input class="form-control multiselect-search" type="text" /></div></li>',
                filterClearBtn:
                    '<span class="input-group-btn"><button class="btn btn-default multiselect-clear-filter" type="button"><i class="fa fa-times"></i></button></span>',
            },
            columns: 1,
            placeholder: SELECT_SUBJECT,
            // search: true,
            // selectAll: true
            includeSelectAllOption: true,
            enableFiltering: true,
        });

        $("#class_ids.multipleclass_ids").multiselect({
            enableHTML: true,
            templates: {
                filter: '<li class="multiselect-item multiselect-filter"><div class="input-group mb-3"><div class="input-group-prepend"><span class="input-group-text"><i class="fa fa-search"></i></span></div><input class="form-control multiselect-search" type="text" /></div></li>',
                filterClearBtn:
                    '<span class="input-group-btn"><button class="btn btn-default multiselect-clear-filter" type="button"><i class="fa fa-times"></i></button></span>',
            },
            columns: 1,
            placeholder: SELECT_CLASS,
            // search: true,
            // selectAll: true
            includeSelectAllOption: true,
            enableFiltering: true,
        });

        $("#school-select-option").multiselect({
            enableHTML: true,
            templates: {
                filter: '<li class="multiselect-item multiselect-filter"><div class="input-group mb-3"><div class="input-group-prepend"><span class="input-group-text"><i class="fa fa-search"></i></span></div><input class="form-control multiselect-search" type="text" /></div></li>',
                filterClearBtn:
                    '<span class="input-group-btn"><button class="btn btn-default multiselect-clear-filter" type="button"><i class="fa fa-times"></i></button></span>',
            },
            columns: 1,
            placeholder: SELECT_SCHOOL,
            // search: true,
            // selectAll: true
            includeSelectAllOption: true,
            enableFiltering: true,
        });

        $("#classType-select-option").multiselect({
            enableHTML: true,
            templates: {
                filter: '<li class="multiselect-item multiselect-filter"><div class="input-group mb-3"><div class="input-group-prepend"><span class="input-group-text"><i class="fa fa-search"></i></span></div><input class="form-control multiselect-search" type="text" /></div></li>',
                filterClearBtn:
                    '<span class="input-group-btn"><button class="btn btn-default multiselect-clear-filter" type="button"><i class="fa fa-times"></i></button></span>',
            },
            columns: 1,
            placeholder: SELECT_CLASS,
            // search: true,
            // selectAll: true
            includeSelectAllOption: true,
            enableFiltering: true,
        });

        $("#question-generator-student-id").multiselect({
            enableHTML: true,
            templates: {
                filter: '<li class="multiselect-item multiselect-filter"><div class="input-group mb-3"><div class="input-group-prepend"><span class="input-group-text"><i class="fa fa-search"></i></span></div><input class="form-control multiselect-search" type="text" /></div></li>',
                filterClearBtn:
                    '<span class="input-group-btn"><button class="btn btn-default multiselect-clear-filter" type="button"><i class="fa fa-times"></i></button></span>',
            },
            columns: 1,
            placeholder: SELECT_CLASS,
            search: true,
            selectAll: true,
            includeSelectAllOption: true,
            enableFiltering: true,
        });

        $("#peer_group_class_select_option").multiselect({
            enableHTML: true,
            templates: {
                filter: '<li class="multiselect-item multiselect-filter"><div class="input-group mb-3"><div class="input-group-prepend"><span class="input-group-text"><i class="fa fa-search"></i></span></div><input class="form-control multiselect-search" type="text" /></div></li>',
                filterClearBtn:
                    '<span class="input-group-btn"><button class="btn btn-default multiselect-clear-filter" type="button"><i class="fa fa-times"></i></button></span>',
            },
            columns: 1,
            placeholder: SELECT_CLASS,
            // search: true,
            // selectAll: true
            includeSelectAllOption: true,
            enableFiltering: true,
        });

        $("#student_multiple_grade_id").multiselect({
            enableHTML: true,
            templates: {
                filter: '<li class="multiselect-item multiselect-filter"><div class="input-group mb-3"><div class="input-group-prepend"><span class="input-group-text"><i class="fa fa-search"></i></span></div><input class="form-control multiselect-search" type="text" /></div></li>',
                filterClearBtn:
                    '<span class="input-group-btn"><button class="btn btn-default multiselect-clear-filter" type="button"><i class="fa fa-times"></i></button></span>',
            },
            columns: 1,
            placeholder: SELECT_CLASS,
            // search: true,
            // selectAll: true
            includeSelectAllOption: true,
            enableFiltering: true,
        });

        $("#other-roles-select-option").multiselect({
            enableHTML: true,
            templates: {
                filter: '<li class="multiselect-item multiselect-filter"><div class="input-group mb-3"><div class="input-group-prepend"><span class="input-group-text"><i class="fa fa-search"></i></span></div><input class="form-control multiselect-search" type="text" /></div></li>',
                filterClearBtn:
                    '<span class="input-group-btn"><button class="btn btn-default multiselect-clear-filter" type="button"><i class="fa fa-times"></i></button></span>',
            },
            columns: 1,
            placeholder: SELECT_SUB_ROLES,
            // search: true,
            // selectAll: true
            includeSelectAllOption: true,
            enableFiltering: true,
        });

        $("#exams-select-option").multiselect({
            enableHTML: true,
            templates: {
                filter: '<li class="multiselect-item multiselect-filter"><div class="input-group mb-3"><div class="input-group-prepend"><span class="input-group-text"><i class="fa fa-search"></i></span></div><input class="form-control multiselect-search" type="text" /></div></li>',
                filterClearBtn:
                    '<span class="input-group-btn"><button class="btn btn-default multiselect-clear-filter" type="button"><i class="fa fa-times"></i></button></span>',
            },
            columns: 1,
            placeholder: SELECT_TEST,
            // search: true,
            // selectAll: true
            includeSelectAllOption: true,
            enableFiltering: true,
        });

        // Using node id multiple in upload document
        $("#doc_node_id").multiselect({
            enableHTML: true,
            templates: {
                filter: '<li class="multiselect-item multiselect-filter"><div class="input-group mb-3"><div class="input-group-prepend"><span class="input-group-text"><i class="fa fa-search"></i></span></div><input class="form-control multiselect-search" type="text" /></div></li>',
                filterClearBtn:
                    '<span class="input-group-btn"><button class="btn btn-default multiselect-clear-filter" type="button"><i class="fa fa-times"></i></button></span>',
            },
            // columns: 1,
            placeholder: SELECT_NODE,
            // search: true,
            // selectAll: true
            includeSelectAllOption: true,
            enableFiltering: true,
        });

        $(".main_node_id_add,#updateNodesForm #main_node_id").multiselect({
            enableHTML: true,
            templates: {
                filter: '<li class="multiselect-item multiselect-filter"><div class="input-group mb-3"><div class="input-group-prepend"><span class="input-group-text"><i class="fa fa-search"></i></span></div><input class="form-control multiselect-search" type="text" /></div></li>',
                filterClearBtn:
                    '<span class="input-group-btn"><button class="btn btn-default multiselect-clear-filter" type="button"><i class="fa fa-times"></i></button></span>',
            },
            columns: 1,
            placeholder: SELECT_PARENT_NODE,
            // search: true,
            // selectAll: true
            includeSelectAllOption: true,
            enableFiltering: true,
        });

        $("#strand_id,#learningReportStrandMuti").multiselect({
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

        $("#learning_unit").multiselect({
            enableHTML: true,
            templates: {
                filter: '<li class="multiselect-item multiselect-filter"><div class="input-group mb-3"><div class="input-group-prepend"><span class="input-group-text"><i class="fa fa-search"></i></span></div><input class="form-control multiselect-search" type="text" /></div></li>',
                filterClearBtn:
                    '<span class="input-group-btn"><button class="btn btn-default multiselect-clear-filter" type="button"><i class="fa fa-times"></i></button></span>',
            },
            columns: 1,
            placeholder: SELECT_LEARNING_UNITS,
            includeSelectAllOption: true,
            enableFiltering: true,
        });

        $("#learning_objectives").multiselect({
            enableHTML: true,
            templates: {
                filter: '<li class="multiselect-item multiselect-filter"><div class="input-group mb-3"><div class="input-group-prepend"><span class="input-group-text"><i class="fa fa-search"></i></span></div><input class="form-control multiselect-search" type="text" /></div></li>',
                filterClearBtn:
                    '<span class="input-group-btn"><button class="btn btn-default multiselect-clear-filter" type="button"><i class="fa fa-times"></i></button></span>',
            },
            columns: 1,
            placeholder: SELECT_LEARNING_OBJECTIVES,
            includeSelectAllOption: true,
            enableFiltering: true,
        });

        $("#finish_of_assignment").multiselect({
            enableHTML: true,
            templates: {
                filter: '<li class="multiselect-item multiselect-filter"><div class="input-group mb-3"><div class="input-group-prepend"><span class="input-group-text"><i class="fa fa-search"></i></span></div><input class="form-control multiselect-search" type="text" /></div></li>',
                filterClearBtn:
                    '<span class="input-group-btn"><button class="btn btn-default multiselect-clear-filter" type="button"><i class="fa fa-times"></i></button></span>',
            },
            columns: 1,
            placeholder: "Select Finish of Assignment",
            includeSelectAllOption: true,
            enableFiltering: true,
        });

        $("#completion_of_self_learning").multiselect({
            enableHTML: true,
            templates: {
                filter: '<li class="multiselect-item multiselect-filter"><div class="input-group mb-3"><div class="input-group-prepend"><span class="input-group-text"><i class="fa fa-search"></i></span></div><input class="form-control multiselect-search" type="text" /></div></li>',
                filterClearBtn:
                    '<span class="input-group-btn"><button class="btn btn-default multiselect-clear-filter" type="button"><i class="fa fa-times"></i></button></span>',
            },
            columns: 1,
            placeholder: "Select Completion of Self-learning",
            includeSelectAllOption: true,
            enableFiltering: true,
        });

        /******* Learning Tutor ************/
        $("#learning_tutor_grade_id").multiselect({
            enableHTML: true,
            templates: {
                filter: '<li class="multiselect-item multiselect-filter"><div class="input-group mb-3"><div class="input-group-prepend"><span class="input-group-text"><i class="fa fa-search"></i></span></div><input class="form-control multiselect-search" type="text"/></div></li>',
                filterClearBtn:
                    '<span class="input-group-btn"><button class="btn btn-default multiselect-clear-filter" type="button"><i class="fa fa-times"></i></button></span>',
            },
            columns: 1,
            filterPlaceholder: SEARCH,
            nonSelectedText: SELECT_GRADE,
            allSelectedText: ALL_SELECTED,
            nSelectedText: SELECTED,
            selectAllText: SELECT_ALL,
            includeSelectAllOption: true,
            enableFiltering: true,
        });

        $("#learning_tutor_strand_id").multiselect({
            enableHTML: true,
            templates: {
                filter: '<li class="multiselect-item multiselect-filter"><div class="input-group mb-3"><div class="input-group-prepend"><span class="input-group-text"><i class="fa fa-search"></i></span></div><input class="form-control multiselect-search" type="text" /></div></li>',
                filterClearBtn:
                    '<span class="input-group-btn"><button class="btn btn-default multiselect-clear-filter" type="button"><i class="fa fa-times"></i></button></span>',
            },
            columns: 1,
            filterPlaceholder: SEARCH,
            nonSelectedText: SELECT_STRAND,
            allSelectedText: ALL_SELECTED,
            nSelectedText: SELECTED,
            selectAllText: SELECT_ALL,
            includeSelectAllOption: true,
            enableFiltering: true,
        });
        $("#learning_tutor_learning_unit").multiselect({
            enableHTML: true,
            templates: {
                filter: '<li class="multiselect-item multiselect-filter"><div class="input-group mb-3"><div class="input-group-prepend"><span class="input-group-text"><i class="fa fa-search"></i></span></div><input class="form-control multiselect-search" type="text" /></div></li>',
                filterClearBtn:
                    '<span class="input-group-btn"><button class="btn btn-default multiselect-clear-filter" type="button"><i class="fa fa-times"></i></button></span>',
            },
            columns: 1,
            filterPlaceholder: SEARCH,
            nonSelectedText: SELECT_LEARNING_UNITS,
            allSelectedText: ALL_SELECTED,
            nSelectedText: SELECTED,
            selectAllText: SELECT_ALL,
            includeSelectAllOption: true,
            enableFiltering: true,
        });
        $("#learning_tutor_learning_objectives").multiselect({
            enableHTML: true,
            templates: {
                filter: '<li class="multiselect-item multiselect-filter"><div class="input-group mb-3"><div class="input-group-prepend"><span class="input-group-text"><i class="fa fa-search"></i></span></div><input class="form-control multiselect-search" type="text" /></div></li>',
                filterClearBtn:
                    '<span class="input-group-btn"><button class="btn btn-default multiselect-clear-filter" type="button"><i class="fa fa-times"></i></button></span>',
            },
            columns: 1,
            filterPlaceholder: SEARCH,
            nonSelectedText: SELECT_LEARNING_OBJECTIVES,
            allSelectedText: ALL_SELECTED,
            nSelectedText: SELECTED,
            selectAllText: SELECT_ALL,
            includeSelectAllOption: true,
            enableFiltering: true,
        });
        $("#filter_learning_tutor_language_id").multiselect({
            enableHTML: true,
            templates: {
                filter: '<li class="multiselect-item multiselect-filter"><div class="input-group mb-3"><div class="input-group-prepend"><span class="input-group-text"><i class="fa fa-search"></i></span></div><input class="form-control multiselect-search" type="text" /></div></li>',
                filterClearBtn:
                    '<span class="input-group-btn"><button class="btn btn-default multiselect-clear-filter" type="button"><i class="fa fa-times"></i></button></span>',
            },
            columns: 1,
            filterPlaceholder: SEARCH,
            nonSelectedText: SELECT_LANGUAGE,
            allSelectedText: ALL_SELECTED,
            nSelectedText: SELECTED,
            selectAllText: SELECT_ALL,
            includeSelectAllOption: true,
            enableFiltering: true,
        });
        /******* Learning Tutor ************/

        // Using date of birth datepicker
        $(".birthdate-date-picker").datepicker({
            dateFormat: "dd/mm/yy",
            maxDate: 0,
            changeMonth: true,
            changeYear: true,
            yearRange: "1950:" + new Date().getFullYear(),
        });

        // Common date picker
        $(".date-picker").datepicker({
            dateFormat: "dd/mm/yy",
            minDate: 0,
            changeMonth: true,
            changeYear: true,
            yearRange: "1950:" + new Date().getFullYear(),
        });

        //filter from date picker
        $(".from-date-picker").datepicker({
            dateFormat: "dd/mm/yy",
            //minDate: 0,
            changeMonth: true,
            changeYear: true,
            yearRange: "1950:" + new Date().getFullYear(),
        });
        //filter to date picker
        $(".to-date-picker").datepicker({
            dateFormat: "dd/mm/yy",
            maxDate: 0,
            changeMonth: true,
            changeYear: true,
            yearRange: "1950:" + new Date().getFullYear(),
        });

        $(".end-date-picker").datepicker({
            minDate: "+1d",
            dateFormat: "dd/mm/yy",
            //maxDate:0,
            changeMonth: true,
            changeYear: true,
            yearRange: "1950:" + new Date().getFullYear(),
        });

        //Change Exam Date From Modal Exam Result Date and End Date
        $(".changeExamDate").datepicker({
            dateFormat: "dd/mm/yy",
            changeMonth: true,
            changeYear: true,
        });
        // Common time picker
        $(".timepicker").timepicker({
            timeFormat: "h:mm p",
            interval: 30,
            minTime: "7:00am",
            maxTime: "8:00pm",
            defaultTime: "7",
            startTime: "07:00",
            dynamic: false,
            dropdown: true,
            scrollbar: true,
        });

        $(".starttimepicker").timepicker({
            timeFormat: "h:mm p",
            minTime: "7:00am",
            maxTime: "8:00pm",
            startTime: "07:00",
            dynamic: false,
            dropdown: true,
            scrollbar: true,
        });

        $(".endtimepicker").timepicker({
            timeFormat: "h:mm p",
            minTime: "7:00am",
            maxTime: "8:00pm",
            startTime: "07:00",
            dynamic: false,
            dropdown: true,
            scrollbar: true,
        });

        // Common date picker
        $(".date-picker-year").datepicker({
            dateFormat: "dd/mm/yy",
            maxDate: 0,
            changeMonth: true,
            changeYear: true,
            yearRange: "1950:" + new Date().getFullYear(),
        });

        /**
         * USE : Set Datatables for tables
         */
        $.fn.dataTable.moment("DD/MM/YYYY HH:mm:ss");
        $("#self-learning-table, #exercise-table, #test-table").DataTable({
            order: [[0, "desc"]],
        });

        // Common datatables script
        // $('#example').DataTable({
        //     "paging": true,
        //     "ordering": true,
        //     dom: 'frtipB',
        //     buttons: [
        //         'copyHtml5',
        //         'excelHtml5',
        //         'csvHtml5',
        //         'pdfHtml5'
        //     ]
        // } );

        // Common datatables script
        $("#example").DataTable({
            paging: true,
            ordering: true,
            dom: "lfrtipB",
            lengthMenu: [
                [10, 25, 50, 100, 250, 500, -1],
                [10, 25, 50, 100, 250, 500, "All"],
            ],
            buttons: ["copyHtml5", "excelHtml5", "csvHtml5", "pdfHtml5"],
        });

        // Common datatables script
        $("#question-DataTable").DataTable({
            paging: true,
            ordering: true,
            columnDefs: [
                {
                    targets: [5], // column index (start from 0)
                    orderable: false, // set orderable false for selected columns
                },
            ],
            dom: "lfrtlipB",
            lengthMenu: [
                [10, 25, 50, 100, 250, 500, -1],
                [10, 25, 50, 100, 250, 500, "All"],
            ],
            buttons: ["copyHtml5", "excelHtml5", "csvHtml5", "pdfHtml5"],
        });

        // Class test report section datatables
        $("#class-test-report-datatable").DataTable({
            dom: "frtipB",
            buttons: ["copy", "csv", "excel", "pdf", "print"],
        });

        //Skill weakness test reports datatables
        $("#group-skill-report-datatable").DataTable({
            dom: "frtipB",
            buttons: ["copy", "csv", "excel", "pdf", "print"],
        });

        // School comaprision reports datatable
        $("#school-comparision-report-datatable").DataTable({
            dom: "frtipB",
            buttons: ["copy", "csv", "excel", "pdf", "print"],
            columnDefs: [
                { width: 150, targets: [0] },
                { width: 200, targets: [2, 3, 4, 5] },
                { width: 150, targets: [1] },
                { width: 600, targets: [6] },
            ],
        });

        $(".child-report-section-detail").css("display", "none");

        // This is used for scroll time fix location of Question type on Question tab
        $(window).scroll(function () {
            if ($(window).scrollTop() >= 300) {
                $(".sm-que-type-difficulty").addClass("fixed-header");
                $(".sm-que-type-difficulty .sm-que-inner").addClass(
                    "visible-title"
                );
            } else {
                $(".sm-que-type-difficulty").removeClass("fixed-header");
                $(".sm-que-type-difficulty .sm-que-inner").removeClass(
                    "visible-title"
                );
            }
        });

        //Add a minus icon to the collapse element that is open by default
        $(".weakness_result_list .collapse.show").each(function () {
            $(this)
                .parent()
                .find(".fa")
                .removeClass("fa-plus")
                .addClass("fa-minus");
        });

        //Toggle plus/minus icon on show/hide of collapse element
        $(".weakness_result_list .collapse")
            .on("shown.bs.collapse", function () {
                $(this)
                    .parent()
                    .find(".fa")
                    .removeClass("fa-plus")
                    .addClass("fa-minus");
            })
            .on("hidden.bs.collapse", function () {
                $(this)
                    .parent()
                    .find(".fa")
                    .removeClass("fa-minus")
                    .addClass("fa-plus");
            });
    },
};

// All Page OnChange Jquery Events
OnChangeEvent = {
    init: function () {
        /**
         * USE : Change event after changing the curriculum Year
         */
        $(document).on("change", "#curriculum_year", function () {
            if (this.value != "") {
                $("#cover-spin").show();
                $.ajax({
                    url: BASE_URL + "/set-curriculum-year",
                    type: "GET",
                    data: {
                        CurriculumYearId: this.value,
                    },
                    success: function (response) {
                        $("#cover-spin").hide();
                        location.reload();
                    },
                });
            }
        });

        /******Learning Tutor  *******/

        // Get Learning Units based on Strand
        $(document).on("change", "#learning_tutor_strand_id", function () {
            $.ajax({
                url: BASE_URL + "/getMultiLearningUnitFromStrands",
                type: "POST",
                data: {
                    _token: $('meta[name="csrf-token"]').attr("content"),
                    grade_id: $("#learning_tutor_grade_id").val(),
                    strand_id: $("#learning_tutor_strand_id").val(),
                },
                success: function (response) {
                    $("#cover-spin").hide();
                    $("#learning_tutor_learning_unit").html("");
                    var data = JSON.parse(JSON.stringify(response));
                    if (data) {
                        $("#learning_tutor_learning_unit").prop(
                            "disabled",
                            false
                        );
                        if (data.data) {
                            $(data.data).each(function () {
                                var option = $("<option />");
                                option
                                    .attr("value", this.id)
                                    .text(this["name_" + APP_LANGUAGE]);
                                $("#learning_tutor_learning_unit").append(
                                    option
                                );
                            });
                        } else {
                            $("#learning_tutor_learning_unit").html(
                                '<option value="">' +
                                    LEARNING_UNITS_NOT_AVAILABLE +
                                    "</option>"
                            );
                        }
                    } else {
                        // $("#learning_tutor_learning_unit").html(
                        //     '<option value="">' +
                        //         LEARNING_UNITS_NOT_AVAILABLE +
                        //         "</option>"
                        // );
                        $("#learning_tutor_learning_unit").prop(
                            "disabled",
                            true
                        );
                        $("#learning_tutor_learning_unit").multiselect(
                            "rebuild"
                        );
                    }
                    $("#learning_tutor_learning_unit").multiselect("rebuild");
                },
                error: function (response) {
                    ErrorHandlingMessage(response);
                },
            });
        });

        // Get Learning Objectives based on Learning Units
        $(document).on("change", "#learning_tutor_learning_unit", function () {
            if (this.value) {
                $.ajax({
                    url:
                        BASE_URL +
                        "/getMultiLearningObjectivesFromLearningUnits",
                    type: "POST",
                    data: {
                        _token: $('meta[name="csrf-token"]').attr("content"),
                        learning_unit_id: $(
                            "#learning_tutor_learning_unit"
                        ).val(),
                    },
                    success: function (response) {
                        $("#cover-spin").hide();
                        $("#learning_tutor_learning_objectives").html("");
                        var data = JSON.parse(JSON.stringify(response));
                        if (data) {
                            $("#learning_tutor_learning_objectives").prop(
                                "disabled",
                                false
                            );
                            if (data.data) {
                                $(data.data).each(function () {
                                    var option = $("<option />");
                                    option
                                        .attr("value", this.id)
                                        .text(
                                            this.foci_number +
                                                " " +
                                                this["title_" + APP_LANGUAGE]
                                        );
                                    $(
                                        "#learning_tutor_learning_objectives"
                                    ).append(option);
                                });
                            } else {
                                $("#learning_tutor_learning_objectives").html(
                                    '<option value="">' +
                                        LEARNING_OBJECTIVES_NOT_AVAILABLE +
                                        "</option>"
                                );
                            }
                        } else {
                            $("#learning_tutor_learning_objectives").html(
                                '<option value="">' +
                                    LEARNING_OBJECTIVES_NOT_AVAILABLE +
                                    "</option>"
                            );
                        }
                        $("#learning_tutor_learning_objectives").multiselect(
                            "rebuild"
                        );
                    },
                    error: function (response) {
                        ErrorHandlingMessage(response);
                    },
                });
            }
        });

        /*** Learning Tutor *****/

        /* Student Leaderboard */
        $(document).on("change", "#leaderboard_type", function () {
            $("#cover-spin").show();
            $.ajax({
                url: BASE_URL + "/filter-leaderboard",
                type: "GET",
                data: {
                    LeaderBoardType: this.value,
                },
                success: function (response) {
                    $("#cover-spin").hide();
                    var data = JSON.parse(JSON.stringify(response));
                    if (data) {
                        $(".leaders").html(data.data);
                    }
                },
                error: function (response) {
                    ErrorHandlingMessage(response);
                },
            });
        });

        /**
         * Modules : Peer Group for teacher panel
         * USE : On change event Peer group grade id
         */
        $(document).on("change", "#peer_group_grade_id", function () {
            $("#student-list-section").html("");
            var SchoolId = $("#school-id").val();
            if (SchoolId == "") {
                var SchoolId = null;
            }
            if (this.value) {
                $("#cover-spin").show();
                $.ajax({
                    url: BASE_URL + "/get-class-type",
                    type: "GET",
                    data: {
                        _token: $('meta[name="csrf-token"]').attr("content"),
                        grade_id: this.value,
                        schoolid: SchoolId,
                    },
                    success: function (response) {
                        $("#cover-spin").hide();
                        var data = JSON.parse(JSON.stringify(response));
                        if (data) {
                            $("#peer_group_class_select_option").html(
                                data.data
                            );
                            $("#peer_group_class_select_option").multiselect(
                                "rebuild"
                            );
                        } else {
                            $("#peer_group_class_select_option").html(
                                '<option value="">' +
                                    CLASS_NOT_AVAILABLE +
                                    "</option>"
                            );
                        }
                    },
                    error: function (response) {
                        ErrorHandlingMessage(response);
                    },
                });
            } else {
                $("#peer_group_class_select_option").html("");
                $("#peer_group_class_select_option").multiselect("reload");
            }
        });

        /**
         * USE : On change grade and class option to get student list
         */
        $(document).on(
            "change",
            "#peer_group_class_select_option",
            function () {
                var classIds = $("#peer_group_class_select_option").val();
                var gradeId = $("#peer_group_grade_id").val();
                if (classIds != "" && gradeId != "") {
                    $("#cover-spin").show();
                    $.ajax({
                        url: BASE_URL + "/get-studentlist-by-grade-class",
                        type: "GET",
                        data: {
                            gradeId: gradeId,
                            classIds: classIds,
                        },
                        success: function (response) {
                            $("#cover-spin").hide();
                            var data = JSON.parse(JSON.stringify(response));
                            if (data) {
                                $("#student-list-section").html(data.data);
                            }
                        },
                        error: function (response) {
                            $("#student-list-section").html("");
                            ErrorHandlingMessage(response);
                        },
                    });
                } else {
                    $("#student-list-section").html("");
                    toastr.error(VALIDATIONS.PLEASE_SELECT_AT_LEAST_ONE_CLASS);
                }
            }
        );

        /**
         * On change event in question page adding new video hints
         */
        $(document).on("change", ".questionVideoHint", function () {
            var language = $(this).attr("data-language");
            $("#question_video_id_" + language).val($(this).val());
        });

        /***
         * on change Language add document in hide and show description en and description ch
         */
        $(document).on("change", "#language_id", function () {
            var language = this.value;
            if (language == 1) {
                // 1 = en language
                $(".description-en-sec").show();
                $(".description-ch-sec").hide();
            } else {
                $(".description-en-sec").hide();
                $(".description-ch-sec").show();
            }
        });

        /**
         * USE : On change Question code textbox event into question bank management
         */
        $(document).on("change", "#naming_structure_code", function () {
            $(".naming_structure_code_error").text("");
            // check question code is already exists or not
            var QuestionId = $("#editQuestionId").val();
            $.ajax({
                url: BASE_URL + "/check-question-code-exists",
                type: "GET",
                data: {
                    questionCode: this.value,
                    QuestionId: QuestionId,
                },
                success: function (response) {
                    var data = JSON.parse(JSON.stringify(response));
                    if (data.data) {
                        $(".naming_structure_code_error").text(
                            VALIDATIONS.QUESTION_CODE_ALREADY_EXISTS
                        );
                    }
                },
            });
            knowledgeNode(this.value);
        });

        // Preview image on change logo image
        $("#logo_image").change(function () {
            let reader = new FileReader();
            reader.onload = (e) => {
                $("#preview-logo-image").attr("src", e.target.result);
            };
            reader.readAsDataURL(this.files[0]);
        });

        // Preview image on change logo image
        $("#fav_icon").change(function () {
            let reader = new FileReader();
            reader.onload = (e) => {
                $("#preview-fav-icon").attr("src", e.target.result);
            };
            reader.readAsDataURL(this.files[0]);
        });

        // Preview image on change profile image
        $("#profile_photo").change(function () {
            let reader = new FileReader();
            reader.onload = (e) => {
                $("#preview-profile-image").attr("src", e.target.result);
            };
            reader.readAsDataURL(this.files[0]);
        });

        // Preview image on change profile image
        $("#teacher_profile_photo").change(function () {
            let reader = new FileReader();
            reader.onload = (e) => {
                $("#preview-profile-image").attr("src", e.target.result);
            };
            reader.readAsDataURL(this.files[0]);
        });

        // Get subjects based on Grades
        $(document).on("change", "#nodes_school_id", function () {
            if (this.value) {
                $("#cover-spin").show();
                $.ajax({
                    url: BASE_URL + "/get-nodelist-by-school",
                    type: "GET",
                    data: {
                        _token: $('meta[name="csrf-token"]').attr("content"),
                        school_id: this.value,
                    },
                    success: function (response) {
                        $("#cover-spin").hide();
                        var data = JSON.parse(JSON.stringify(response));
                        if (data) {
                            $("#main_node_id").html(data.data);
                        } else {
                            $("#main_node_id").html(
                                '<option value="">' +
                                    NODES_NOT_AVAILABLE +
                                    "</option>"
                            );
                        }
                    },
                    error: function (response) {
                        ErrorHandlingMessage(response);
                    },
                });
            }
        });

        // Get Class TypeBased on Grade
        $(document).on("change", "#class_id", function () {
            if (this.value) {
                $("#cover-spin").show();
                $.ajax({
                    url: BASE_URL + "/get-class-type",
                    type: "GET",
                    data: {
                        _token: $('meta[name="csrf-token"]').attr("content"),
                        grade_id: this.value,
                    },
                    success: function (response) {
                        $("#cover-spin").hide();
                        var data = JSON.parse(JSON.stringify(response));
                        if (data) {
                            $("#classType-select-option").html(data.data);
                            $("#classType-select-option").multiselect(
                                "rebuild"
                            );
                        } else {
                            $("#classType-select-option").html(
                                '<option value="">' +
                                    CLASS_NOT_AVAILABLE +
                                    "</option>"
                            );
                        }
                    },
                    error: function (response) {
                        ErrorHandlingMessage(response);
                    },
                });
            }
        });

        //in Exam panel to redirect Student Exam list on grade select get class
        $(document).on("change", "#student_multiple_grade_id", function () {
            var schoolid = $("#student_study_school_id").val();
            if (schoolid == "") {
                var schoolid = null;
            }
            var grade_id = [];
            $("#student_multiple_grade_id option:selected").each(function () {
                grade_id.push($(this).val());
            });

            if (this.value) {
                $("#cover-spin").show();
                $.ajax({
                    url: BASE_URL + "/get-class-type",
                    type: "GET",
                    data: {
                        _token: $('meta[name="csrf-token"]').attr("content"),
                        grade_id: grade_id,
                        schoolid: schoolid,
                    },
                    success: function (response) {
                        $("#cover-spin").hide();
                        var data = JSON.parse(JSON.stringify(response));
                        if (data) {
                            $("#classType-select-option").html(data.data);
                            $("#classType-select-option").multiselect(
                                "rebuild"
                            );
                        } else {
                            $("#classType-select-option").html(
                                '<option value="">' +
                                    CLASS_NOT_AVAILABLE +
                                    "</option>"
                            );
                        }
                    },
                    error: function (response) {
                        ErrorHandlingMessage(response);
                    },
                });
            } else {
                $("#classType-select-option").html("");
                $("#classType-select-option").multiselect("reload");
            }
        });

        //in Exam panel to redirect Student Exam list on grade select get class
        $(document).on("change", "#student_grade_id", function () {
            var schoolid = $("#school-id").val();
            if (schoolid == "") {
                var schoolid = null;
            }
            if (this.value) {
                $("#cover-spin").show();
                $.ajax({
                    url: BASE_URL + "/get-class-type",
                    type: "GET",
                    data: {
                        _token: $('meta[name="csrf-token"]').attr("content"),
                        grade_id: this.value,
                        schoolid: schoolid,
                    },
                    success: function (response) {
                        $("#cover-spin").hide();
                        var data = JSON.parse(JSON.stringify(response));
                        if (data) {
                            $("#classType-select-option").html(data.data);
                            $("#classType-select-option").multiselect(
                                "rebuild"
                            );
                        } else {
                            $("#classType-select-option").html(
                                '<option value="">' +
                                    CLASS_NOT_AVAILABLE +
                                    "</option>"
                            );
                        }
                    },
                    error: function (response) {
                        ErrorHandlingMessage(response);
                    },
                });
            } else {
                $("#classType-select-option").html("");
                $("#classType-select-option").multiselect("reload");
            }
        });

        /**
         * USE : Get Grades by selected schools
         */
        $(document).on("change", "#school-id", function () {
            if (this.value) {
                $("#cover-spin").show();
                $.ajax({
                    url: BASE_URL + "/getGradesBySchool",
                    type: "GET",
                    data: {
                        _token: $('meta[name="csrf-token"]').attr("content"),
                        schoolid: this.value,
                    },
                    success: function (response) {
                        $("#cover-spin").hide();
                        var data = JSON.parse(JSON.stringify(response));
                        if (data) {
                            $("#student_grade_id").html(data.data);
                        } else {
                            $("#student_grade_id").html(
                                '<option value="">' +
                                    GRADE_NOT_AVAILABLE +
                                    "</option>"
                            );
                        }
                    },
                    error: function (response) {
                        ErrorHandlingMessage(response);
                    },
                });
            } else {
                $("#student_grade_id").html(
                    "<option>" + SELECT_GRADE + "</option>"
                );
                $("#classType-select-option").html("");
                $("#classType-select-option,#student_grade_id").multiselect(
                    "reload"
                );
            }
        });

        // Get strands based on subjects
        $(document).on("change", "#grade-id", function () {
            // Update question code
            //GenerateQuestionCode();
            if (this.value) {
                $.ajax({
                    url: BASE_URL + "/getStrandsFromSubject",
                    type: "POST",
                    data: {
                        _token: $('meta[name="csrf-token"]').attr("content"),
                        grade_id: $("#grade-id").val(),
                        subject_id: this.value,
                    },
                    success: function (response) {
                        $("#cover-spin").hide();
                        var data = JSON.parse(JSON.stringify(response));
                        $("#strand-id").show();
                        if (data) {
                            $("#strand-id").html(
                                '<option value="">' +
                                    SELECT_STRAND +
                                    "</option>"
                            );
                            $("#strand-id").prop("disabled", false);
                            if (data.data) {
                                $(data.data).each(function () {
                                    var option = $("<option />");
                                    option
                                        .attr("value", this.id)
                                        .text(this.name);
                                    $("#strand-id").append(option);
                                });
                            } else {
                                $("#strand-id").html(
                                    '<option value="">' +
                                        STRAND_NOT_AVAILABLE +
                                        "</option>"
                                );
                            }
                        } else {
                            $("#strand-id").html(
                                '<option value="">' +
                                    STRAND_NOT_AVAILABLE +
                                    "</option>"
                            );
                        }
                    },
                    error: function (response) {
                        ErrorHandlingMessage(response);
                    },
                });
            }
        });

        // Get Learning Units based on Strand
        $(document).on("change", "#strand-id", function () {
            //Update Question Code
            //GenerateQuestionCode();
            if (this.value) {
                $.ajax({
                    url: BASE_URL + "/getLearningUnitFromStrands",
                    type: "POST",
                    data: {
                        _token: $('meta[name="csrf-token"]').attr("content"),
                        grade_id: $("#grade-id").val(),
                        //subject_id: $("#subject-id").val(),
                        strand_id: this.value,
                    },
                    success: function (response) {
                        $("#cover-spin").hide();
                        var data = JSON.parse(JSON.stringify(response));
                        $("#learning-unit").show();
                        if (data) {
                            $("#learning-unit").html(
                                '<option value="">' +
                                    SELECT_LEARNING_UNITS +
                                    "</option>"
                            );
                            $("#learning-unit").prop("disabled", false);
                            if (data.data) {
                                $(data.data).each(function () {
                                    var option = $("<option />");
                                    option
                                        .attr("value", this.id)
                                        .text(this.name);
                                    $("#learning-unit").append(option);
                                });
                            } else {
                                $("#learning-unit").html(
                                    '<option value="">' +
                                        LEARNING_UNITS_NOT_AVAILABLE +
                                        "</option>"
                                );
                            }
                        } else {
                            $("#learning-unit").html(
                                '<option value="">' +
                                    LEARNING_UNITS_NOT_AVAILABLE +
                                    "</option>"
                            );
                        }
                        //$("#strand-id").multiselect("reload");
                    },
                    error: function (response) {
                        ErrorHandlingMessage(response);
                    },
                });
            }
        });

        // Get Learning Objectives based on Learning Units
        $(document).on("change", "#learning-unit", function () {
            //Update Question Code
            //GenerateQuestionCode();
            if (this.value) {
                $.ajax({
                    url: BASE_URL + "/getLearningObjectivesFromLearningUnits",
                    type: "POST",
                    data: {
                        _token: $('meta[name="csrf-token"]').attr("content"),
                        grade_id: $("#grade-id").val(),
                        //subject_id: $("#subject-id").val(),
                        strand_id: $("#strand-id").val(),
                        learning_unit_id: this.value,
                    },
                    success: function (response) {
                        $("#cover-spin").hide();
                        var data = JSON.parse(JSON.stringify(response));
                        $("#learning-objectives").show();
                        if (data) {
                            $("#learning-objectives").html(
                                '<option value="">' +
                                    SELECT_LEARNING_OBJECTIVES +
                                    "</option>"
                            );
                            $("#learning-objectives").prop("disabled", false);
                            if (data.data) {
                                $(data.data).each(function () {
                                    var option = $("<option />");
                                    option
                                        .attr("value", this.id)
                                        .text(
                                            this.foci_number +
                                                " " +
                                                this.title_en
                                        );
                                    $("#learning-objectives").append(option);
                                });
                            } else {
                                $("#learning-objectives").html(
                                    '<option value="">' +
                                        LEARNING_OBJECTIVES_NOT_AVAILABLE +
                                        "</option>"
                                );
                            }
                        } else {
                            $("#learning-objectives").html(
                                '<option value="">' +
                                    LEARNING_OBJECTIVES_NOT_AVAILABLE +
                                    "</option>"
                            );
                        }
                        //$('#learning-objectives').multiselect("reload");
                    },
                    error: function (response) {
                        ErrorHandlingMessage(response);
                    },
                });
            }
        });

        // Get Learning Objectives based on Learning Units
        $(document).on(
            "change",
            "#refresh-question-learning-unit",
            function () {
                //Update Question Code
                //GenerateQuestionCode();
                if (this.value) {
                    $.ajax({
                        url:
                            BASE_URL +
                            "/getLearningObjectivesFromMultipleLearningUnits",
                        type: "POST",
                        data: {
                            _token: $('meta[name="csrf-token"]').attr(
                                "content"
                            ),
                            grade_id: $("#grade-id").val(),
                            //subject_id: $("#subject-id").val(),
                            strand_id: $("#refresh-question-strand-id").val(),
                            learning_unit_id: $(
                                "#refresh-question-learning-unit"
                            ).val(),
                        },
                        success: function (response) {
                            $("#cover-spin").hide();
                            var data = JSON.parse(JSON.stringify(response));
                            if (data) {
                                $("#refresh-question-learning-objectives").html(
                                    ""
                                );
                                if (data.data) {
                                    $(data.data).each(function () {
                                        var option = $("<option />");
                                        option
                                            .attr("value", this.id)
                                            .text(
                                                this.foci_number +
                                                    " " +
                                                    this.title
                                            );
                                        $(
                                            "#refresh-question-learning-objectives"
                                        ).append(option);
                                    });
                                } else {
                                    $(
                                        "#refresh-question-learning-objectives"
                                    ).html(
                                        '<option value="">' +
                                            LEARNING_OBJECTIVES_NOT_AVAILABLE +
                                            "</option>"
                                    );
                                }
                            } else {
                                $("#refresh-question-learning-objectives").html(
                                    '<option value="">' +
                                        LEARNING_OBJECTIVES_NOT_AVAILABLE +
                                        "</option>"
                                );
                            }
                            $(
                                "#refresh-question-learning-objectives"
                            ).multiselect("rebuild");
                        },
                        error: function (response) {
                            ErrorHandlingMessage(response);
                        },
                    });
                }
            }
        );

        $(document).on("change", "#learning-objectives", function () {
            //Update Question Code
            //GenerateQuestionCode();
        });

        $(document).on("change", "#field_g", function () {
            //Update Question Code
            //GenerateQuestionCode();
        });

        //for a get grade id based on school id in admin add user panel
        $(document).on("change", "#school", function () {
            var get_school_id = $("#school").val();
            $.ajax({
                url: BASE_URL + "/user/grade/" + get_school_id,
                type: "POST",
                data: {
                    _token: $('meta[name="csrf-token"]').attr("content"),
                    school_id: get_school_id,
                },
                success: function (response) {
                    $("#cover-spin").hide();
                    var data = JSON.parse(JSON.stringify(response));
                    if (data) {
                        $("#class").html(
                            '<option value="">' + SELECT_GRADE + "</option>"
                        );
                        $("#class").prop("disabled", false);
                        if (data.data) {
                            $(data.data).each(function () {
                                var option = $("<option />");
                                option
                                    .attr("value", this.grades.id)
                                    .text(this.name);
                                $("#class").append(option);
                            });
                        } else {
                            $("#class").html(
                                '<option value="">' +
                                    GRADE_NOT_AVAILABLE +
                                    "</option>"
                            );
                        }
                    } else {
                        $("#class").html(
                            '<option value="">' +
                                GRADE_NOT_AVAILABLE +
                                "</option>"
                        );
                    }
                },
                error: function (response) {
                    ErrorHandlingMessage(response);
                },
            });
        });

        // Assign grade all student in to student grade table
        $(document).on("change", "#select-all-students", function () {
            $("#cover-spin").show();
            if (this.checked) {
                $(".question-ids").prop("checked", true);
                var student_ids = [];
                $("input:checkbox[name=student_ids]:checked").each(function () {
                    student_ids.push($(this).val());
                });
                $.ajax({
                    url:
                        BASE_URL +
                        "/school/class/addStudents/" +
                        $(this).attr("data-studentid"),
                    type: "POST",
                    data: {
                        _token: $('meta[name="csrf-token"]').attr("content"),
                        student_ids: student_ids,
                        grade_id: $(this).attr("data-studentid"),
                    },
                    success: function (response) {
                        $("#cover-spin").hide();
                        var data = JSON.parse(JSON.stringify(response));
                        if (data.status === "success") {
                            toastr.success(data.message);
                        } else {
                            toastr.error(data.message);
                        }
                    },
                    error: function (response) {
                        ErrorHandlingMessage(response);
                    },
                });
            }
        });

        // Assign single grade in to assign Student table
        $(document).on("change", "#assign_student_id", function () {
            $("#cover-spin").show();
            if ($(this).is(":checked")) {
                // Add Student to groups
                $.ajax({
                    url:
                        BASE_URL +
                        "/school/class/addStudent/" +
                        $(this).attr("data-studentid"),
                    type: "POST",
                    data: {
                        _token: $('meta[name="csrf-token"]').attr("content"),
                        student_ids: [this.value],
                        grade_id: $(this).attr("data-studentid"),
                    },
                    success: function (response) {
                        $("#cover-spin").hide();
                        var data = JSON.parse(JSON.stringify(response));
                        if (data.status === "success") {
                            toastr.success(data.message);
                        } else {
                            toastr.error(data.message);
                        }
                    },
                    error: function (response) {
                        ErrorHandlingMessage(response);
                    },
                });
            }
        });

        /**
         * USE : Update Exam Assign School Status Draft or Send_to_school
         */
        $(document).on("change", "#update_assign_school_status", function () {
            var examid = $(this).data("examid");
            var selectedValue = $(this).val();
            $.confirm({
                title: ARE_YOU_SURE_EXAM_SEND_TO_THE_SCHOOL,
                content: CONFIRMATION,
                autoClose: "Cancellation|8000",
                buttons: {
                    update_assign_school_status: {
                        text: "Update",
                        action: function () {
                            $("#cover-spin").show();
                            $.ajax({
                                url: BASE_URL + "/update-school-assign-status",
                                method: "POST",
                                data: {
                                    _token: $('meta[name="csrf-token"]').attr(
                                        "content"
                                    ),
                                    examid: examid,
                                    status: selectedValue,
                                },
                                success: function (response) {
                                    $("#cover-spin").hide();
                                    var data = JSON.parse(
                                        JSON.stringify(response)
                                    );
                                    if (data.status === "success") {
                                        toastr.success(data.message);
                                        location.reload();
                                    } else {
                                        toastr.error(data.message);
                                    }
                                },
                                error: function (response) {
                                    ErrorHandlingMessage(response);
                                },
                            });
                        },
                    },
                    Cancellation: function () {},
                },
            });
        });

        /**
         * USE : Student can select exam language based on changes list of question answer language
         * **/
        $(document).on(
            "change",
            "#student-select-attempt-exam-language",
            function () {
                if (this.value == "") {
                    toastr.error(PLEASE_SELECT_LANGUAGE);
                    return false;
                }
                var AnswerNameList = [];
                $("input:radio").each(function () {
                    var rname = $(this).attr("name");
                    if ($.inArray(rname, AnswerNameList) === -1)
                        AnswerNameList.push(rname);
                });
                $("#attempt-exams input[name=language]").val($(this).val());
                var question_id = $("#nextquestionarea .checkanswer").attr(
                    "question-id"
                );
                var SelectedAnswersArray = {};
                $.each(AnswerNameList, function (key, questionKey) {
                    SelectedAnswersArray[questionKey] = $(
                        "input[type='radio'][name='" +
                            questionKey +
                            "']:checked"
                    ).val();
                });
                $("#SelectedAnswersArray").val(
                    JSON.stringify(SelectedAnswersArray)
                );
                if ($("#nextquestionarea .checkanswer:checked").length != 0) {
                    var answer = $("#nextquestionarea .checkanswer").val();
                    var questionid = $("#nextquestionarea .checkanswer").attr(
                        "question-id"
                    );
                    var submitid = $("#submitquestion").attr("submit-id");
                    questiontype = $("#nextquestionarea .checkanswer").attr(
                        "question-type"
                    );
                    var language = $(
                        "#attempt-exams input[name=language]"
                    ).val();
                    var checkarns = 0;
                    question_arr = {
                        question_id: questionid,
                        answer: answer,
                        language: language,
                    };
                    if (question_old_data_new.length != 0) {
                        for (
                            var ic = 0;
                            ic < question_old_data_new.length;
                            ic++
                        ) {
                            if (
                                question_old_data_new[ic]["question_id"] ==
                                questionid
                            ) {
                                checkarns++;
                                question_old_data_new[ic] = question_arr;
                            }
                        }
                        if (checkarns == 0) {
                            question_old_data_new.push(question_arr);
                        }
                    } else {
                        question_old_data_new.push(question_arr);
                    }
                }
                $examId = $(this).attr("data-examid");
                if ($examId) {
                    $("#cover-spin").show();
                    $.ajax({
                        url:
                            BASE_URL +
                            "/student/exam/change-language/" +
                            $examId,
                        type: "GET",
                        data: {
                            language: this.value,
                            question_id: question_id,
                            SelectedAnswersArray: SelectedAnswersArray,
                            questionNo: questionNo,
                            question_position: question_position,
                        },
                        success: function (response) {
                            var data = JSON.parse(JSON.stringify(response));
                            if (data.status === "success") {
                                $("#nextquestionarea").html(data.data.html);
                                //updateMathHtml();
                                //updateMathHtmlById("nextquestionarea");
                                MathJax.Hub.Queue(["Typeset", MathJax.Hub]);
                                if (
                                    $("#nextquestionarea .checkanswer:checked")
                                        .length != 0
                                ) {
                                    $("#nextquestion").prop("disabled", false);
                                    if ($("#nextquestion").length == 0) {
                                        $("#submitquestion").prop(
                                            "disabled",
                                            false
                                        );
                                        $("#submitquestion").show();
                                    }
                                }
                            } else {
                                toastr.error(data.message);
                            }
                            $("#cover-spin").hide();
                        },
                        error: function (response) {
                            ErrorHandlingMessage(response);
                        },
                    });
                }
                return false;
            }
        );

        //StudentMannagement Add Edit page Geade on class Change
        // Student Class promotion on change grade id in school panel
        $(document).on("change", "#School-studentgrade-id", function () {
            var schoolid = $("#school-id").val();
            var grade_id = this.value;
            if (grade_id != "") {
                $("#cover-spin").show();
                $.ajax({
                    url: BASE_URL + "/get-class-type",
                    type: "GET",
                    data: {
                        grade_id: this.value,
                        schoolid: schoolid,
                    },
                    success: function (response) {
                        $("#cover-spin").hide();
                        var data = JSON.parse(JSON.stringify(response));
                        if (data) {
                            $("#School-studentclassType-option").html(
                                '<option value="">' +
                                    SELECT_CLASS +
                                    "</option>" +
                                    data.data
                            );
                        } else {
                            $("#School-studentclassType-option").html(
                                '<option value="">' +
                                    CLASS_NOT_AVAILABLE +
                                    "</option>"
                            );
                        }
                    },
                    error: function (response) {
                        ErrorHandlingMessage(response);
                    },
                });
            }
        });

        // import students data check
        $(document).on(
            "change",
            "#importStudents input[name=user_file]",
            function () {
                if (this.value) {
                    $(".error_msg").html("").hide();
                    var fd = new FormData();
                    var files = $(this)[0].files;
                    if (files.length > 0) {
                        fd.append(
                            "_token",
                            $('meta[name="csrf-token"]').attr("content")
                        );
                        fd.append("mode", $("#mode").val());
                        fd.append("user_file", files[0]);
                        fd.append("curriculum_year_id", $("#curriculum").val());
                        // $.ajax({
                        //     url:
                        //         BASE_URL +
                        //         "/school/class/ImportStudentsDataCheck",
                        //     type: "POST",
                        //     data: fd,
                        //     contentType: false,
                        //     processData: false,
                        //     success: function (response) {
                        //         $("#importStudentModal .data_tbl").html(
                        //             response.data.data
                        //         );
                        //         $("#importStudentModal").modal("show");
                        //     },
                        // });
                        $("#cover-spin").show();
                        $.ajax({
                            url: BASE_URL + "/school/class/DuplicateCsvRecords",
                            type: "POST",
                            data: fd,
                            contentType: false,
                            processData: false,
                            success: function (response) {
                                $("#cover-spin").hide();
                                if (!response.data.data) {
                                    $("#cover-spin").show();
                                    $.ajax({
                                        url:
                                            BASE_URL +
                                            "/school/class/ImportStudentsDataCheck",
                                        type: "POST",
                                        data: fd,
                                        contentType: false,
                                        processData: false,
                                        success: function (response) {
                                            $("#cover-spin").hide();
                                            if (response.data.data != "") {
                                                $(
                                                    "#importStudentModal .data_tbl"
                                                ).html(response.data.data);
                                                $("#importStudentModal").modal(
                                                    "show"
                                                );
                                            }else{
                                                toastr.success(STUDENT_IMPORT_CSV_FILE_MESSAGE);
                                            }
                                        },
                                    });
                                } else {
                                    $("#importCsvStudentModal .data_tbl").html(
                                        response.data.data
                                    );
                                    $("#importCsvStudentModal").modal("show");
                                }
                            },
                        });
                    }
                }
            }
        );

        // Student Class promotion on change grade id in school panel
        $(document).on("change", "#class-promotion-grade-id", function () {
            var schoolid = $("#school-id").val();
            var grade_id = this.value;
            var studentIds = [];
            if (grade_id != "") {
                $("#cover-spin").show();
                $.ajax({
                    url: BASE_URL + "/get-class-type",
                    type: "GET",
                    data: {
                        _token: $('meta[name="csrf-token"]').attr("content"),
                        grade_id: this.value,
                        schoolid: schoolid,
                    },
                    success: function (response) {
                        $("#cover-spin").hide();
                        var data = JSON.parse(JSON.stringify(response));
                        if (data) {
                            $("#classType-option").html(
                                '<option value="">' +
                                    SELECT_CLASS +
                                    "</option>" +
                                    data.data
                            );
                        } else {
                            $("#classType-option").html(
                                '<option value="">' +
                                    CLASS_NOT_AVAILABLE +
                                    "</option>"
                            );
                        }
                    },
                    error: function (response) {
                        ErrorHandlingMessage(response);
                    },
                });
            }
        });

        // Student Class promotion on change grade id in school panel
        $(document).on("click", "#class-promotion", function () {
            var schoolid = $("#school-id").val();
            var grade_id = $("#class-promotion-grade-id").val();
            var class_type = $("#classType-option").val();
            var studentIds = [];
            $("input:checkbox[name=student-class-promotion]:checked").each(
                function () {
                    studentIds.push($(this).val());
                }
            );
            // Check grade is empty or not
            if (grade_id == "") {
                toastr.error(PLEASE_SELECT_GRADE_FIRST);
                return false;
            }
            // Check Class Type is empty or not
            if (class_type == "") {
                toastr.error(PLEASE_SELECT_CLASS_FIRST);
                return false;
            }
            // Check Student is empty or not
            if (studentIds == "") {
                toastr.error(PLEASE_SELECT_STUDENT_FIRST);
                return false;
            }
            // Student is available
            $.confirm({
                title: CLASS_PROMOTION + "?",
                content: CONFIRMATION,
                autoClose: "Cancellation|8000",
                buttons: {
                    deleteTeacher: {
                        text: CLASS_PROMOTION,
                        action: function () {
                            $("#cover-spin").show();
                            $.ajax({
                                url: BASE_URL + "/class-promotion",
                                method: "POST",
                                data: {
                                    _token: $('meta[name="csrf-token"]').attr(
                                        "content"
                                    ),
                                    studentIds: studentIds,
                                    grade_id: grade_id,
                                    class_type: class_type,
                                },
                                success: function (response) {
                                    $("#cover-spin").hide();
                                    var data = JSON.parse(
                                        JSON.stringify(response)
                                    );
                                    if (data.status === "success") {
                                        toastr.success(data.message);
                                        location.reload();
                                    } else {
                                        toastr.error(data.message);
                                    }
                                },
                                error: function (response) {
                                    ErrorHandlingMessage(response);
                                },
                            });
                        },
                    },
                    Cancellation: function () {},
                },
            });
        });

        //For check all check box of student in school panel inside student management
        $(document).on(
            "change",
            "#student-class-promotion-check-all",
            function () {
                if (this.checked) {
                    var studentIds = [];
                    $(".student-Ids").prop("checked", true);
                    $(
                        "input:checkbox[name=student-class-promotion]:checked"
                    ).each(function () {
                        studentIds.push($(this).val());
                    });
                } else {
                    // Clear all checkbox multiple student in school panel inside student management
                    var studentIds = [];
                    $(".student-Ids").prop("checked", false);
                    $(
                        "input:checkbox[name=student-class-promotion]:checked"
                    ).each(function () {
                        studentIds.remove($(this).val());
                    });
                    return false;
                }
            }
        );

        $(document).on("change", "#checkbox-all-question", function () {
            if (this.checked) {
                var QuestionIds = [];
                $(".chk-select-question").prop("checked", true);
                $("input:checkbox[name=chk-select-question]:checked").each(
                    function () {
                        QuestionIds.push($(this).val());
                    }
                );
            } else {
                // Clear all checkbox multiple student in school panel inside student management
                var QuestionIds = [];
                $(".chk-select-question").prop("checked", false);
                $("input:checkbox[name=chk-select-question]:checked").each(
                    function () {
                        QuestionIds.remove($(this).val());
                    }
                );
                return false;
            }

            // console.log("QuestionIds", QuestionIds);
        });

        // Student Class promotion on change grade id in school panel
        $(document).on("change", "#question_verification_status", function () {
            var verification_status = this.value;
            var QuestionIds = [];
            $("input:checkbox[name=chk-select-question]:checked").each(
                function () {
                    QuestionIds.push($(this).val());
                }
            );
            // Check Student is empty or not
            if (QuestionIds == "") {
                $(this).val("");
                toastr.error(PLEASE_SELECT_QUESTION_FIRST);
                return false;
            }
            if (verification_status == "") {
                $(this).val("");
                toastr.error(PLEASE_SELECT_VERIFICATION_STATUS);
                return false;
            }
            // Student is available
            $.confirm({
                title: UPDATE_QUESTION_VERIFICATION + "?",
                content: CONFIRMATION,
                autoClose: "Cancellation|8000",
                buttons: {
                    deleteQuestionVerification: {
                        text: UPDATE_QUESTION_VERIFICATION,
                        action: function () {
                            $("#cover-spin").show();
                            $.ajax({
                                url: BASE_URL + "/update-question-verification",
                                method: "POST",
                                data: {
                                    _token: $('meta[name="csrf-token"]').attr(
                                        "content"
                                    ),
                                    QuestionIds: QuestionIds,
                                    verification_status: verification_status,
                                },
                                success: function (response) {
                                    $("#cover-spin").hide();
                                    var data = JSON.parse(
                                        JSON.stringify(response)
                                    );
                                    if (data.status === "success") {
                                        toastr.success(data.message);
                                        location.reload();
                                    } else {
                                        toastr.error(data.message);
                                    }
                                },
                                error: function (response) {
                                    ErrorHandlingMessage(response);
                                },
                            });
                        },
                    },
                    Cancellation: function () {
                        $("#question_verification_status").val("");
                    },
                },
            });
        });

        // Delete Document
        $(document).on("click", "#deleteDocument", function () {
            var dataid = $(this).data("id");
            // var tr = $(this).parent('div').parent('div').parent('div');
            var tr = $(this).closest("tr");
            $.confirm({
                title: DELETE_FILE + "?",
                content: CONFIRMATION,
                autoClose: "Cancellation|8000",
                buttons: {
                    deleteDocument: {
                        text: DELETE_FILE,
                        action: function () {
                            $("#cover-spin").show();
                            $.ajax({
                                url:
                                    BASE_URL +
                                    "/upload-documents/delete/" +
                                    dataid,
                                type: "GET",
                                success: function (response) {
                                    $("#cover-spin").hide();
                                    var data = JSON.parse(
                                        JSON.stringify(response)
                                    );
                                    if (data.status === "success") {
                                        toastr.success(data.message);
                                        // location.reload(true);
                                        tr.fadeOut(500, function () {
                                            $(this).remove();
                                        });
                                    } else {
                                        toastr.error(data.message);
                                    }
                                },
                                error: function (response) {
                                    ErrorHandlingMessage(response);
                                },
                            });
                        },
                    },
                    Cancellation: function () {},
                },
            });
        });

        // Delete Teacher
        $(document).on("click", "#deleteTeacher", function () {
            var dataid = $(this).data("id");
            var tr = $(this).closest("tr");
            $.confirm({
                title: DELETE_TEACHER + "?",
                content: CONFIRMATION,
                autoClose: "Cancellation|8000",
                buttons: {
                    deleteTeacher: {
                        text: DELETE_TEACHER,
                        action: function () {
                            $("#cover-spin").show();
                            $.ajax({
                                url: BASE_URL + "/teacher/delete/" + dataid,
                                type: "GET",
                                success: function (response) {
                                    $("#cover-spin").hide();
                                    var data = JSON.parse(
                                        JSON.stringify(response)
                                    );
                                    if (data.status === "success") {
                                        toastr.success(data.message);
                                        tr.fadeOut(500, function () {
                                            $(this).remove();
                                        });
                                    } else {
                                        toastr.error(data.message);
                                    }
                                },
                                error: function (response) {
                                    ErrorHandlingMessage(response);
                                },
                            });
                        },
                    },
                    Cancellation: function () {},
                },
            });
        });

        // Delete Principal
        $(document).on("click", "#deletePrincipal", function () {
            var dataid = $(this).data("id");
            var tr = $(this).closest("tr");
            $.confirm({
                title: DELETE_PRINCIPAL + "?",
                content: CONFIRMATION,
                autoClose: "Cancellation|8000",
                buttons: {
                    deleteTeacher: {
                        text: DELETE_PRINCIPAL,
                        action: function () {
                            $("#cover-spin").show();
                            $.ajax({
                                url: BASE_URL + "/principal/delete/" + dataid,
                                type: "GET",
                                success: function (response) {
                                    $("#cover-spin").hide();
                                    var data = JSON.parse(
                                        JSON.stringify(response)
                                    );
                                    if (data.status === "success") {
                                        toastr.success(data.message);
                                        tr.fadeOut(500, function () {
                                            $(this).remove();
                                        });
                                    } else {
                                        toastr.error(data.message);
                                    }
                                },
                                error: function (response) {
                                    ErrorHandlingMessage(response);
                                },
                            });
                        },
                    },
                    Cancellation: function () {},
                },
            });
        });

        // change mode
        $(document).on(
            "click",
            "#importStudentModal .data_action",
            function () {
                $("#importStudents #action").val($(this).val());
                $("#importStudents").submit();
            }
        );

        // Delete subject
        $(document).on("click", "#deleteSubject", function () {
            var dataid = $(this).data("id");
            var tr = $(this).closest("tr");
            $.confirm({
                title: DELETE_SUBJECT + "?",
                content: CONFIRMATION,
                autoClose: "Cancellation|8000",
                buttons: {
                    deleteSubject: {
                        text: DELETE_SUBJECT,
                        action: function () {
                            $("#cover-spin").show();
                            $.ajax({
                                url: BASE_URL + "/subject/delete/" + dataid,
                                type: "GET",
                                success: function (response) {
                                    $("#cover-spin").hide();
                                    var data = JSON.parse(
                                        JSON.stringify(response)
                                    );
                                    if (data.status === "success") {
                                        toastr.success(data.message);
                                        tr.fadeOut(500, function () {
                                            $(this).remove();
                                        });
                                    } else {
                                        toastr.error(data.message);
                                    }
                                },
                                error: function (response) {
                                    ErrorHandlingMessage(response);
                                },
                            });
                        },
                    },
                    Cancellation: function () {},
                },
            });
        });

        // Delete Strand
        $(document).on("click", "#deleteStrnad", function () {
            var dataid = $(this).data("id");
            var tr = $(this).closest("tr");
            $.confirm({
                title: DELETE_STRAND + "?",
                content: CONFIRMATION,
                autoClose: "Cancellation|8000",
                buttons: {
                    deleteSubject: {
                        text: DELETE_STRAND,
                        action: function () {
                            $("#cover-spin").show();
                            $.ajax({
                                url: BASE_URL + "/strand/delete/" + dataid,
                                type: "GET",
                                success: function (response) {
                                    $("#cover-spin").hide();
                                    var data = JSON.parse(
                                        JSON.stringify(response)
                                    );
                                    if (data.status === "success") {
                                        toastr.success(data.message);
                                        tr.fadeOut(500, function () {
                                            $(this).remove();
                                        });
                                    } else {
                                        toastr.error(data.message);
                                    }
                                },
                                error: function (response) {
                                    ErrorHandlingMessage(response);
                                },
                            });
                        },
                    },
                    Cancellation: function () {},
                },
            });
        });

        // Delete Learning Objectives
        $(document).on("click", "#deleteLearningObjective", function () {
            var dataid = $(this).data("id");
            var tr = $(this).closest("tr");
            $.confirm({
                title: DELETE_LEARNING_OBJECTIVES + "?",
                content: CONFIRMATION,
                autoClose: "Cancellation|8000",
                buttons: {
                    deleteLearningObjective: {
                        text: DELETE_LEARNING_OBJECTIVES,
                        action: function () {
                            $("#cover-spin").show();
                            $.ajax({
                                url:
                                    BASE_URL +
                                    "/learning-objective/delete/" +
                                    dataid,
                                type: "GET",
                                success: function (response) {
                                    $("#cover-spin").hide();
                                    var data = JSON.parse(
                                        JSON.stringify(response)
                                    );
                                    if (data.status === "success") {
                                        toastr.success(data.message);
                                        tr.fadeOut(500, function () {
                                            $(this).remove();
                                        });
                                    } else {
                                        toastr.error(data.message);
                                    }
                                },
                                error: function (response) {
                                    ErrorHandlingMessage(response);
                                },
                            });
                        },
                    },
                    Cancellation: function () {},
                },
            });
        });

        // Delete class
        $(document).on("click", "#deleteClass", function () {
            var dataid = $(this).data("id");
            var tr = $(this).closest("tr");
            $.confirm({
                title: DELETE_CLASS + "?",
                content: CONFIRMATION,
                autoClose: "Cancellation|8000",
                buttons: {
                    deleteClass: {
                        text: DELETE_CLASS,
                        action: function () {
                            $("#cover-spin").show();
                            $.ajax({
                                url: BASE_URL + "/class/delete/" + dataid,
                                type: "GET",
                                success: function (response) {
                                    $("#cover-spin").hide();
                                    var data = JSON.parse(
                                        JSON.stringify(response)
                                    );
                                    if (data.status === "success") {
                                        toastr.success(data.message);
                                        tr.fadeOut(500, function () {
                                            $(this).remove();
                                        });
                                    } else {
                                        toastr.error(data.message);
                                    }
                                },
                                error: function (response) {
                                    ErrorHandlingMessage(response);
                                },
                            });
                        },
                    },
                    Cancellation: function () {},
                },
            });
        });

        // Delete Ai Calculated Difficulty
        $(document).on("click", "#deleteAiCalculatedDifficulty", function () {
            var dataid = $(this).data("id");
            var tr = $(this).closest("tr");
            $.confirm({
                title: DELETE_AI_CALCULATED_LEVEL + "?",
                content: CONFIRMATION,
                autoClose: "Cancellation|8000",
                buttons: {
                    deleteAiCalculatedDifficulty: {
                        text: DELETE_AI_CALCULATED_LEVEL,
                        action: function () {
                            $("#cover-spin").show();
                            $.ajax({
                                url:
                                    BASE_URL +
                                    "/ai-calculated-difficulty/delete/" +
                                    dataid,
                                type: "GET",
                                success: function (response) {
                                    $("#cover-spin").hide();
                                    var data = JSON.parse(
                                        JSON.stringify(response)
                                    );
                                    if (data.status === "success") {
                                        toastr.success(data.message);
                                        tr.fadeOut(500, function () {
                                            $(this).remove();
                                        });
                                    } else {
                                        toastr.error(data.message);
                                    }
                                },
                                error: function (response) {
                                    ErrorHandlingMessage(response);
                                },
                            });
                        },
                    },
                    Cancellation: function () {},
                },
            });
        });

        // Delete Pre Configure Difficulty
        $(document).on("click", "#deletePreconfigureDifficulty", function () {
            var dataid = $(this).data("id");
            var tr = $(this).closest("tr");
            $.confirm({
                title: DELETE_PRE_CONFIGURE_DIFFICULTY_LEVEL + "?",
                content: CONFIRMATION,
                autoClose: "Cancellation|8000",
                buttons: {
                    deleteAiCalculatedDifficulty: {
                        text: DELETE_PRE_CONFIGURE_DIFFICULTY_LEVEL,
                        action: function () {
                            $("#cover-spin").show();
                            $.ajax({
                                url:
                                    BASE_URL +
                                    "/pre-configure-difficulty/delete/" +
                                    dataid,
                                type: "GET",
                                success: function (response) {
                                    $("#cover-spin").hide();
                                    var data = JSON.parse(
                                        JSON.stringify(response)
                                    );
                                    if (data.status === "success") {
                                        toastr.success(data.message);
                                        tr.fadeOut(500, function () {
                                            $(this).remove();
                                        });
                                    } else {
                                        toastr.error(data.message);
                                    }
                                },
                                error: function (response) {
                                    ErrorHandlingMessage(response);
                                },
                            });
                        },
                    },
                    Cancellation: function () {},
                },
            });
        });

        // Delete assign
        $(document).on("click", "#deleteTeacherClassSubject", function () {
            var dataid = $(this).data("id");
            var tr = $(this).closest("tr");
            $.confirm({
                title: DELETE_ASSIGN + "?",
                content: CONFIRMATION,
                autoClose: "Cancellation|8000",
                buttons: {
                    deleteTeacherClassSubject: {
                        text: DELETE_ASSIGN,
                        action: function () {
                            $("#cover-spin").show();
                            $.ajax({
                                url:
                                    BASE_URL +
                                    "/teacher-class-subject-assign/delete/" +
                                    dataid,
                                type: "GET",
                                success: function (response) {
                                    $("#cover-spin").hide();
                                    var data = JSON.parse(
                                        JSON.stringify(response)
                                    );
                                    if (data.status === "success") {
                                        toastr.success(data.message);
                                        tr.fadeOut(500, function () {
                                            $(this).remove();
                                        });
                                    } else {
                                        toastr.error(data.message);
                                    }
                                },
                                error: function (response) {
                                    ErrorHandlingMessage(response);
                                },
                            });
                        },
                    },
                    Cancellation: function () {},
                },
            });
        });

        // Delete peer group
        $(document).on("click", ".deletePeerGroup", function () {
            var dataid = $(this).data("id");
            var tr = $(this).closest("tr");
            $.confirm({
                title: "Delete Peer Group" + "?",
                content: CONFIRMATION,
                autoClose: "Cancellation|8000",
                buttons: {
                    deleteSubject: {
                        text: "Delete Peer Group",
                        action: function () {
                            $("#cover-spin").show();
                            $.ajax({
                                url: BASE_URL + "/peer-group/" + dataid,
                                type: "DELETE",
                                data: {
                                    _token: $('meta[name="csrf-token"]').attr(
                                        "content"
                                    ),
                                },
                                success: function (response) {
                                    $("#cover-spin").hide();
                                    var data = JSON.parse(
                                        JSON.stringify(response)
                                    );
                                    if (data.status === "success") {
                                        // Delete Group into Firebase
                                        DeleteChatGroup(data.data.ChatGroupId);

                                        toastr.success(data.message);
                                        tr.fadeOut(500, function () {
                                            $(this).remove();
                                        });
                                    } else {
                                        toastr.error(data.message);
                                    }
                                },
                                error: function (response) {
                                    ErrorHandlingMessage(response);
                                },
                            });
                        },
                    },
                    Cancellation: function () {},
                },
            });
        });

        if (typeof isUserPanal !== "undefined") {
            if (isUserPanal == 1) {
                var tmp_role_id = "";
                $("#student_ids.multiplestudent_ids").multiselect({
                    enableHTML: true,
                    templates: {
                        filter: '<li class="multiselect-item multiselect-filter"><div class="input-group mb-3"><div class="input-group-prepend"><span class="input-group-text"><i class="fa fa-search"></i></span></div><input class="form-control multiselect-search" type="text" /></div></li>',
                        filterClearBtn:
                            '<span class="input-group-btn"><button class="btn btn-default multiselect-clear-filter" type="button"><i class="fa fa-times"></i></button></span>',
                    },
                    columns: 1,
                    placeholder: SELECT_STUDENT,
                    search: true,
                    selectAll: false,
                });
                $(".student").hide();

                // On change event usermanagement role dropdowns
                $(document).on("change", "#role", function () {
                    $(".gender").show();
                    $(".student").hide();
                    $("#school_id").prop("disabled", false);
                    $(".studentroll").hide();
                    $("#student_number").prop("required", false);
                    $("#class_number").prop("required", false);
                    $("#grade_id").prop("disabled", true);
                    $("#grade_id").html("");
                    $("#school_id").val("");
                    tmp_role_id = $(this).val();
                    if (tmp_role_id == 5) {
                        // School Role
                        $(".gender").hide();
                        //$("#male").prop( "checked", false );
                        $("#school_id").prop("disabled", true);
                        $("#grade_id").prop("disabled", true);
                    }
                    if (tmp_role_id == "") {
                        $("#school_id").prop("disabled", true);
                        $("#grade_id").prop("disabled", true);
                    }

                    if (tmp_role_id == 2) {
                        // Teacher Role
                        $("#grade_id").prop("disabled", true);
                    }

                    if (tmp_role_id == 6) {
                        // 6 = External resources Role
                        $("#grade_id").prop("disabled", true);
                    }

                    if (tmp_role_id == 3) {
                        // 3 = Student Role
                        $("#student_number").prop("required", true);
                        $("#class_number").prop("required", true);
                        $(".studentroll").show();
                    }

                    if (tmp_role_id == 4 || tmp_role_id == 3) {
                        // 4 = Parents Role and 3 = Student Role
                        if (
                            typeof isUserPanalEdit !== "undefined" &&
                            isUserPanalEdit == 1 &&
                            typeof oldUserData !== "undefined"
                        ) {
                            //$('#school_id').val(oldUserData.school_id).trigger("change");
                            if (typeof oldUserData.school_id !== "undefined") {
                                $("#school_id")
                                    .val(oldUserData.school_id)
                                    .trigger("change");
                            } else if (
                                typeof oldUserData.school !== "undefined"
                            ) {
                                $("#school_id")
                                    .val(oldUserData.school)
                                    .trigger("change");
                            }
                        }

                        /**
                         * USE : On change grade option get student list
                         */
                        $(document).on("change", "#grade_id", function () {
                            var scid = $("#school_id").val();
                            var gid = $(this).val();
                            if (gid != "") {
                                $("#student_ids").html("");
                                $.ajax({
                                    url: BASE_URL + "/getstudentdata",
                                    method: "POST",
                                    data: {
                                        gid: gid,
                                        scid: scid,
                                        _token: $(
                                            'meta[name="csrf-token"]'
                                        ).attr("content"),
                                    },
                                    success: function (response) {
                                        var object = JSON.parse(
                                            JSON.stringify(response)
                                        );
                                        if (tmp_role_id == 4) {
                                            // 4 = Parents Role
                                            $(".student").show();
                                        }
                                        if (!object.data) {
                                            $.each(
                                                object.data,
                                                function (key, value) {
                                                    if (value.name_en != "") {
                                                        $(
                                                            "#student_ids"
                                                        ).append(
                                                            "<option value=" +
                                                                value.id +
                                                                " >" +
                                                                value.name_en +
                                                                "</option>"
                                                        );
                                                    } else {
                                                        $(
                                                            "#student_ids"
                                                        ).append(
                                                            "<option value=" +
                                                                value.id +
                                                                " >" +
                                                                value.name +
                                                                "</option>"
                                                        );
                                                    }
                                                }
                                            );
                                        } else {
                                            $("#student_ids").append(
                                                '<option value="">' +
                                                    NO_STUDENT_AVAILABLE +
                                                    "</option>"
                                            );
                                        }
                                        if (
                                            typeof isUserPanalEdit !==
                                                "undefined" &&
                                            isUserPanalEdit == 1 &&
                                            typeof old_stu_ids !==
                                                "undefined" &&
                                            old_stu_ids != ""
                                        ) {
                                            $(
                                                "#student_ids.multiplestudent_ids"
                                            ).val(old_stu_ids);
                                        }
                                        $(
                                            "#student_ids.multiplestudent_ids"
                                        ).multiselect("reload");
                                    },
                                });
                            }
                        });
                    }
                });

                // On change event School_id in usermanagement module
                $(document).on("change", "#school_id", function () {
                    $("#grade_id").html("");
                    if (
                        tmp_role_id != "2" &&
                        tmp_role_id != "6" &&
                        tmp_role_id != "7"
                    ) {
                        $("#grade_id").prop("disabled", false);
                        var sid = $(this).val();
                        if (sid != "") {
                            $.ajax({
                                url: BASE_URL + "/user/grade/" + sid,
                                type: "POST",
                                data: {
                                    _token: $('meta[name="csrf-token"]').attr(
                                        "content"
                                    ),
                                    school_id: sid,
                                },
                                success: function (response) {
                                    $("#cover-spin").hide();
                                    var data = JSON.parse(
                                        JSON.stringify(response)
                                    );
                                    if (data) {
                                        $("#grade_id").html(
                                            '<option value="">' +
                                                SELECT_GRADE +
                                                "</option>"
                                        );
                                        if (data.data) {
                                            $(data.data).each(function () {
                                                var option = $("<option />");
                                                option
                                                    .attr("value", this.id)
                                                    .text(this.name);
                                                $("#grade_id").append(option);
                                            });
                                        } else {
                                            $("#grade_id").html(
                                                '<option value="">' +
                                                    GRADE_NOT_AVAILABLE +
                                                    "</option>"
                                            );
                                        }
                                    } else {
                                        $("#grade_id").html(
                                            '<option value="">' +
                                                GRADE_NOT_AVAILABLE +
                                                "</option>"
                                        );
                                    }
                                    if (
                                        typeof isUserPanalEdit !==
                                            "undefined" &&
                                        isUserPanalEdit == 1 &&
                                        typeof oldUserData !== "undefined"
                                    ) {
                                        $("#grade_id")
                                            .val(oldUserData.grade_id)
                                            .trigger("change");
                                    }
                                },
                                error: function (response) {
                                    ErrorHandlingMessage(response);
                                },
                            });
                        }
                    }
                });

                if (
                    typeof isUserPanalEdit !== "undefined" &&
                    isUserPanalEdit == 1 &&
                    typeof oldUserData !== "undefined"
                ) {
                    tmp_role_id = oldUserData.role_id;
                    $("#role").trigger("change");
                    if (typeof oldUserData.school_id !== "undefined") {
                        $("#school_id").val(oldUserData.school_id);
                    } else if (typeof oldUserData.school !== "undefined") {
                        $("#school_id").val(oldUserData.school);
                    }
                }
            }
        }
        /**
         * USE : Get Student list in Teacher Panel
         */
        $(document).on(
            "change",
            "#displayStudentStudyForm #grade_id",
            function () {
                var scid = $("#school_id").val();
                $("#student_id").html("");
                var gid = $(this).val();
                if (gid != "") {
                    $("#student_id").html("");
                    $.ajax({
                        url: BASE_URL + "/get_studentdata",
                        method: "POST",
                        data: {
                            gid: gid,
                            scid: scid,
                            _token: $('meta[name="csrf-token"]').attr(
                                "content"
                            ),
                        },
                        success: function (response) {
                            $(".student").show();
                            $("#student_id").html(response);
                        },
                    });
                }
            }
        );
        /**
         * USE : Get nodes list
         */
        $(document).on("change", ".node-id", function () {
            var nodeData = $(this);
            var nodeid = $(this).val();
            if (nodeid != "") {
                $.ajax({
                    url: BASE_URL + "/getSchoolNodes",
                    type: "POST",
                    data: {
                        _token: $('meta[name="csrf-token"]').attr("content"),
                        nodeid: nodeid,
                    },
                    success: function (response) {
                        $(".node-info").show();
                        var currentLang = $(
                            ".sidebar-open .langague-dropdown a.dropdown-toggle"
                        )
                            .text()
                            .trim();
                        var LangCode = "en";
                        var data_ans_id = nodeData.attr("data-ans-id");
                        /* if(currentLang=='Chinese')
                        {
                            LangCode='ch';
                        }*/
                        if (data_ans_id.indexOf("_ch") != -1) {
                            LangCode = "ch";
                        }
                        if (response.node_title_en != "") {
                            if (response.node_title_en != null) {
                                if (LangCode == "ch") {
                                    $("#node-title").text(
                                        response.node_title_ch
                                    );
                                } else {
                                    $("#node-title").text(
                                        response.node_title_en
                                    );
                                }
                            } else {
                                $("#node-title").text(NOT_AVAILABLE);
                            }
                        }
                        if (response.node_description_en != "") {
                            if (response.node_description_en != null) {
                                if (LangCode == "ch") {
                                    $("#node-description").text(
                                        response.node_description_ch
                                    );
                                } else {
                                    $("#node-description").text(
                                        response.node_description_en
                                    );
                                }
                            } else {
                                $("#node-description").text(NOT_AVAILABLE);
                            }
                        }
                        if (response.weakness_name_en != "") {
                            if (response.weakness_name_en != null) {
                                if (LangCode == "ch") {
                                    $("#node-weakness").text(
                                        response.weakness_name_ch
                                    );
                                    $("#node-weakness-en").text(
                                        response.weakness_name_en
                                    );
                                    $("#node-weakness-ch").text(
                                        response.weakness_name_ch
                                    );
                                } else {
                                    $("#node-weakness-ch").text(
                                        response.weakness_name_ch
                                    );
                                    $("#node-weakness-en").text(
                                        response.weakness_name_en
                                    );
                                    $("#node-weakness").text(
                                        response.weakness_name_en
                                    );
                                }
                            } else {
                                $("#node-weakness").text(NOT_AVAILABLE);
                            }
                        }
                    },
                });
            } else {
                $(".node-info").hide();
            }
        });

        /**
         * USE : Get sub nodes list
         */

        $(document).on("change", "#parent-node-id", function () {
            $("#cover-spin").show();
            var nodeid = $(this).val();
            if (nodeid != "") {
                $.ajax({
                    url: BASE_URL + "/getSubNodes",
                    type: "POST",
                    data: {
                        _token: $('meta[name="csrf-token"]').attr("content"),
                        nodeid: nodeid,
                    },
                    success: function (response) {
                        $(".main-node-id").show();
                        if (response) {
                            $("#main_node_id").html(response);
                        } else {
                            $("#main_node_id").html(
                                '<option value="">' +
                                    NODES_NOT_AVAILABLE +
                                    "</option>"
                            );
                        }
                        $("#cover-spin").hide();
                    },
                });
            } else {
                $(".main-node-id").hide();
                $("#cover-spin").hide();
            }
        });

        // All Questions select
        $(document).on("change", "#select-all-question", function () {
            if (this.checked) {
                $(".testquestion input[name='question_ids[]']").prop(
                    "checked",
                    true
                );
            } else {
                $(".testquestion input[name='question_ids[]']").prop(
                    "checked",
                    false
                );
            }
        });

        // All Questions check and uncheck to change to check all
        $(document).on(
            "change",
            ".testquestion input[name='question_ids[]']",
            function () {
                if (
                    $(".testquestion input[name='question_ids[]']:checked")
                        .length ==
                    $(".testquestion input[name='question_ids[]']").length
                ) {
                    $("#select-all-question").prop("checked", true);
                } else {
                    $("#select-all-question").prop("checked", false);
                }
            }
        );

        $(document).on("change", "#q-exam-language", function () {
            if ($(this).val() == "en") {
                $(
                    "#question_data .language_en,#WantAHintModal .language_en"
                ).show();
                $(
                    "#question_data .language_ch,#WantAHintModal .language_ch"
                ).hide();
            } else {
                $(
                    "#question_data .language_en,#WantAHintModal .language_en"
                ).hide();
                $(
                    "#question_data .language_ch,#WantAHintModal .language_ch"
                ).show();
            }
        });
    },
};

function showCoverSpinLoader() {
    $("#cover-spin").show();
}
// All On Page load Jquery Event
OnClickEvent = {
    init: function () {
        /**
         * Trigger : On click reset filter button in intelligent tutor video page
         */
        $(document).on("click", "#reset_filter_btn", function () {
            // Reset Grade filter options
            $(
                "#learning_tutor_grade_id > option, #learning_tutor_strand_id > option, #learning_tutor_learning_unit > option, #learning_tutor_learning_objectives > option, #filter_learning_tutor_language_id > option"
            ).each(function () {
                $(this).prop("selected", true);
            });
            // Refresh and Rebuild all selection options
            $(
                "#learning_tutor_grade_id, #learning_tutor_strand_id, #learning_tutor_learning_unit, #learning_tutor_learning_objectives, #filter_learning_tutor_language_id"
            ).multiselect("rebuild");
        });

        /**
         * USE: open Question Preview Popup Modal in Teacher PAnel
         */
        $(document).on("click", ".exam_questions-info", function () {
            $examid = $(this).data("examid");
            $("#cover-spin").show();
            $.ajax({
                url: BASE_URL + "/getAllAssignQuestions",
                type: "GET",
                data: {
                    exam_id: $examid,
                },
                success: function (response) {
                    var response = JSON.parse(JSON.stringify(response));
                    $(".teacher-question-list-preview-data").html(
                        response.data.html
                    );
                    //updateMathHtml();
                    MathJax.Hub.Queue(["Typeset", MathJax.Hub]);
                    $("#cover-spin").hide();
                    $("#teacher-question-list-preview").modal("show");
                },
                error: function (response) {
                    ErrorHandlingMessage(response);
                },
            });
        });

        /**
         * USE: Change Exam End Date
         */
        $(document).on("click", ".change_end_date_of_exam", function () {
            $("#ExamId").val($(this).data("examid"));
            $(this).attr("dateType") == "EndDate"
                ? $("#examToDate").val($(this).attr("examEndDate"))
                : $("#examToDate").val($(this).attr("examResultDate"));
            $(".test_reference_number").html($(this).attr("refrence_no"));
            $(".test_title").html($(this).attr("title"));
            $(".SetLabelOfChangeDate").html($(this).attr("dateType"));
            // $("#ExamType").val($(this).attr("dateType"));
            $("#dateType").val($(this).attr("dateType"));
            $(this).attr("dateType") != "EndDate"
                ? $(".SetLabelOfChangeDate").html(RESULT_DATE)
                : $(".SetLabelOfChangeDate").html(END_DATE);
            $("#ChangeEndDateModal").modal("show");
        });

        /**
         * USE: In Teacher Panel Display Student Result Summary Report in modal
         */
        $(document).on("click", ".result_summary", function () {
            $examid = $(this).data("examid");
            $studentIds = $(this).data("studentids");
            $("#cover-spin").show();
            $.ajax({
                url: BASE_URL + "/student-result-summary",
                type: "GET",
                data: {
                    examId: $examid,
                    studentIds: $studentIds,
                },
                success: function (response) {
                    var response = JSON.parse(JSON.stringify(response));
                    if (response.status == "success") {
                        $(".student-report-summary-data").html(
                            response.data.html
                        );
                        $("#cover-spin").hide();
                        $("#StudentSummaryReportModal").modal("show");
                    } else {
                        $("#cover-spin").hide();
                        toastr.error(response.data.message);
                    }
                },
                error: function (response) {
                    ErrorHandlingMessage(response);
                },
            });
        });

        /***
         * Use To Remove parent node id from edit Node
         */
        $(document).on("click", ".delete-parent-node", function () {
            var parentId = $(this).attr("data-parentId");
            var currentnodeid = $(this).attr("data-currentNodeId");
            $.confirm({
                title: "Are you sure to remove parent Node?",
                content: CONFIRMATION,
                autoClose: "Cancellation|8000",
                buttons: {
                    deleteTeacher: {
                        text: "remove parent node",
                        action: function () {
                            $("#cover-spin").show();
                            $.ajax({
                                url:
                                    BASE_URL +
                                    "/remove-parent-node/" +
                                    parentId,
                                method: "get",
                                data: {
                                    _token: $('meta[name="csrf-token"]').attr(
                                        "content"
                                    ),
                                    currentNodeId: currentnodeid,
                                },
                                success: function (response) {
                                    var data = JSON.parse(
                                        JSON.stringify(response)
                                    );
                                    if (data.status === "success") {
                                        toastr.success(data.message);
                                        if (data.data.parentnodelist) {
                                            $("#main_node_id").html(
                                                data.data.parentnodelist
                                            );
                                        } else {
                                            $("#main_node_id").html(
                                                '<option value="">' +
                                                    NODES_NOT_AVAILABLE +
                                                    "</option>"
                                            );
                                        }
                                        window.location.reload();
                                        $("#cover-spin").hide();
                                    } else {
                                        $("#cover-spin").hide();
                                        toastr.error(data.message);
                                    }
                                },
                                error: function (response) {
                                    ErrorHandlingMessage(response);
                                },
                            });
                        },
                    },
                    Cancellation: function () {},
                },
            });
        });

        /**
         * USE : Select peer group fot the add or remove peer group
         */
        $(document).on("click", ".select-all-peer-group-student", function () {
            if ($(this).is(":checked")) {
                $(".peer_group_student_id").each(function () {
                    $(this).prop("checked", true);
                });
            } else {
                $(".peer_group_student_id").each(function () {
                    $(this).prop("checked", false);
                });
            }
        });

        /***
         * USE : Open Pop Modal in Full Solution at Exam Result page
         */
        $(document).on("click", ".getSolutionQuestionImage", function () {
            var img_full_solution = $(this).data("question-code");
            var path =
                BASE_URL +
                "/uploads/question_solutions/" +
                GetQuestionSolutionPrefixByLanguage() +
                "-" +
                img_full_solution +
                ".png";
            $("#cover-spin").show();
            if (FileExist(path)) {
                $("#fullSolution-image").attr("src", path);
            } else {
                $("#SolutionImageModal .modal-body").html(
                    QUESTION_SOLUTION_NOT_FOUND
                );
            }
            $("#SolutionImageModal").modal("show");
            $("#cover-spin").hide();
        });

        $(document).on(
            "click",
            ".close-student-report-summary-popup",
            function () {
                $(".student-report-summary-data").html("");
                closePopupModal("StudentSummaryReportModal");
            }
        );

        //Close Popup Modal Add More Schools
        $(document).on("click", ".closeAddMoreSchoolModal", function () {
            $(".add-More-Schools-modal-body").innerHTML = "";
            closePopupModal("addMoreSchoolModel");
        });

        //Close Popup Modal End date or Result Date
        $(document).on("click", ".changeExamResultOrEndDate", function () {
            $(".ChangeEndDateModal-modal-body").innerHTML = "";
            closePopupModal("ChangeEndDateModal");
        });

        //Close Popup Modal close-FileEditModal-modal
        $(document).on("click", ".close-FileEditModal-modal", function () {
            closePopupModal("FileEditModal");
        });

        $(document).on("click", "close-videoPlayer-modal", function () {
            $("#video-stream").attr("src", "");
            closePopupModal("videoPlayer");
        });

        $(document).on("click", ".closeTestUpdateStatusModal", function () {
            closePopupModal("UpdateStatusTestGradesClass");
            $(".update-test-grade-class-modal-body").innerHTML = "";
        });

        // close Popup Modal Export Performance Report
        $(document).on("click", ".closePerformanceReportPopup", function () {
            $(
                ".getCheckedClass,#checkAllClasses,.getCheckedGroup,#checkAllPeerGroup"
            ).prop("checked", false);
            closePopupModal("exportPerformanceReportPopupModal");
        });

        // close Popup Modal Admin Export Performance Report
        $(document).on(
            "click",
            ".closeAdminPerformanceReportPopup",
            function () {
                $(
                    ".selectAllClassSchool,.selectAllGroupSchool,.selectGroup,.selectClass"
                ).prop("checked", false);
                closePopupModal("adminExportPerformanceReportPopupModal");
            }
        );

        //close Pop up Modal of full solution
        $(document).on("click", ".closePop", function () {
            $("#fullSolution-image").attr("src", "");
            closePopupModal("SolutionImageModal");
        });

        //close pop up Modal of Question-list-preview
        $(document).on("click", ".closeQuestionPop", function () {
            $("#teacher-question-list-preview-data").attr("src", "");
            closePopupModal("teacher-question-list-preview");
        });

        //close pop up Modal of school reminder popup
        $(document).on("click", ".closeRemainderPopup", function () {
            var currentUrl = $(location).attr("href");
            currentUrl.split("?")[0];
            window.location = currentUrl.slice(0, currentUrl.indexOf("?"));
            //$("#remainder-upgrade-school-data-popup").hide();
        });

        $(document).on(
            "click",
            ".class-ability-analysis-report-close-pop",
            function () {
                $("#class-ability-analysis-report-image").attr("src", "");
                closePopupModal("class-ability-analysis-report");
            }
        );

        $(document).on("click", ".peer_group_student_id", function () {
            if (
                $(".peer_group_student_id").length ===
                $(".peer_group_student_id:checked").length
            ) {
                $(".select-all-peer-group-student").prop("checked", true);
            } else {
                $(".select-all-peer-group-student").prop("checked", false);
            }
        });

        /**
         * USE : Display on graph Get Test Difficulty Analysis Report
         * Trigger : On click getTestDifficultyAnalysisReport icon into exams list action table
         * **/
        $(document).on(
            "click",
            ".getTestDifficultyAnalysisReport",
            function (e) {
                $("#cover-spin").show();
                $examId = $(this).attr("data-examid");
                if ($examId) {
                    $.ajax({
                        url:
                            BASE_URL +
                            "/my-teaching/get-test-difficulty-analysis-report",
                        type: "post",
                        data: {
                            _token: $('meta[name="csrf-token"]').attr(
                                "content"
                            ),
                            examid: $examId,
                        },
                        success: function (response) {
                            var ResposnseData = JSON.parse(
                                JSON.stringify(response)
                            );
                            if (ResposnseData.data != 0) {
                                // Append image src attribute with base64 encode image
                                $(
                                    "#test-difficulty-analysis-report-image"
                                ).attr(
                                    "src",
                                    "data:image/jpg;base64," +
                                        ResposnseData.data
                                );
                                $("#test-difficulty-analysis-report").modal(
                                    "show"
                                );
                            } else {
                                toastr.error(VALIDATIONS.DATA_NOT_FOUND);
                            }
                            $("#cover-spin").hide();
                        },
                        error: function (response) {
                            ErrorHandlingMessage(response);
                        },
                    });
                }
            }
        );

        /**
         * USE : Student can create own self-learning test
         */
        // $(document).on('click', '#student_create_self_learning_test', function(e) {
        //     $('#studentCreateSelfLearningTestModal').modal('show');
        // });

        $(document).on("click", ".closepopup", function (e) {
            $("#filterFileName").val("");
            $(".video-hints-list").html("");
        });

        /**
         * USE : Get Question Code related Videos in Intelligent-tutor list.
         */
        $(document).on("click", ".getIntelligentTutorVideos", function (e) {
            $("#cover-spin").show();
            $questionNode = $(this).data("question-node");
            $.ajax({
                url: BASE_URL + "/intelligent-tutor",
                type: "get",
                data: {
                    _token: $('meta[name="csrf-token"]').attr("content"),
                    StructureCode: $questionNode,
                },
                success: function (response) {
                    $("#cover-spin").hide();
                    window.location = BASE_URL + "/intelligent-tutor";
                },
                error: function (response) {
                    ErrorHandlingMessage(response);
                },
            });
        });

        /**
         * USE : Add More Url TextBox in Document
         */
        $(document).on("click", "#addMoreDocumentUrl", function (e) {
            $documentUrlHtml = "";
            $documentUrlHtml =
                '<div class="col-md-3 col-sm-2">' +
                '<div class="form-group">' +
                '<input type="text" class="form-control document_urls" name="document_urls[]" placeholder="' +
                ENTER_VIDEO_URL +
                '">' +
                '<a class="removeVideoUrl btn btn-primary btn-sm">X</a>' +
                '</div><div class="alert alert-danger uploadUrl" style="display:none;">' +
                ENTER_VIDEO_URL +
                "</div>" +
                "</div>";
            $("#document-url-cls").append($documentUrlHtml);
        });

        /** USE : Remove Video Url From Html */
        $(document).on("click", ".removeVideoUrl", function (e) {
            $(this).parent("div").parent("div").remove();
        });

        // on Check Add more Admin Div Show
        $('input[name="addAdmins"]').click(function () {
            if ($(this).prop("checked") == true) {
                $(".sub-admin-portion").show();
            } else {
                $(".sub-admin-portion").hide();
            }
        });

        /**
         * USE : Remove Existing Url from on Update in admin panel
         */
        $(document).on("click", ".removeExistingVideoUrl", function (e) {
            var UrlId = $(this).attr("id");
            var FileDiv = $(this).parent("div");
            if (UrlId) {
                $.confirm({
                    title: DELETE_URL + "?",
                    content: CONFIRMATION,
                    autoClose: "Cancellation|8000",
                    buttons: {
                        deleteUser: {
                            text: DELETE_URL,
                            action: function () {
                                $("#cover-spin").show();
                                $.ajax({
                                    url:
                                        BASE_URL +
                                        "/upload-documents/delete/" +
                                        UrlId,
                                    type: "GET",
                                    success: function (response) {
                                        $("#cover-spin").hide();
                                        var data = JSON.parse(
                                            JSON.stringify(response)
                                        );
                                        if (data.status === "success") {
                                            toastr.success(data.message);
                                            FileDiv.remove();
                                        } else {
                                            toastr.error(data.message);
                                        }
                                    },
                                    error: function (response) {
                                        ErrorHandlingMessage(response);
                                    },
                                });
                            },
                        },
                        Cancellation: function () {},
                    },
                });
            } else {
                $(this).parent("div").remove();
            }
        });
        /**
         * USE : Remove Existing file from on Update in admin panel
         */
        $(document).on("click", ".db-remove-image", function (e) {
            var ImageId = $(this).attr("data-id");
            var FileDiv = $(this).parent("div");
            if (ImageId) {
                $.confirm({
                    title: DELETE_FILE + "?",
                    content: CONFIRMATION,
                    autoClose: "Cancellation|8000",
                    buttons: {
                        deleteUser: {
                            text: DELETE_FILE,
                            action: function () {
                                $("#cover-spin").show();
                                $.ajax({
                                    url:
                                        BASE_URL +
                                        "/upload-documents/removefile/" +
                                        ImageId,
                                    type: "GET",
                                    success: function (response) {
                                        $("#cover-spin").hide();
                                        var data = JSON.parse(
                                            JSON.stringify(response)
                                        );
                                        if (data.status === "success") {
                                            toastr.success(data.message);
                                            FileDiv.remove();
                                        } else {
                                            toastr.error(data.message);
                                        }
                                    },
                                    error: function (response) {
                                        ErrorHandlingMessage(response);
                                    },
                                });
                            },
                        },
                        Cancellation: function () {},
                    },
                });
            } else {
                $(this).parent("div").remove();
            }
        });

        /**
         * USE : Add More Sub Admin in Admin Panel from Add new School
         */
        $(document).on("click", "#addMoreAdmin", function (e) {
            $MoreAdminHtml = "";
            $MoreAdminHtml =
                '<div class="add-more-admin row">' +
                '<div class="form-group col-md-3">' +
                '<label class="text-bold-600">' +
                ENGLISH_NAME +
                "</label>" +
                '<input type="text" class="form-control subAdminName" name="subAdminName[]" placeholder="' +
                ENTER_ENGLISH_NAME +
                '">' +
                '<span class="error-msg subadminname_err"></span>' +
                "</div>" +
                '<div class="form-group col-md-3">' +
                '<label class="text-bold-600">' +
                CHINESE_NAME +
                "</label>" +
                '<input type="text" class="form-control subAdminNameCh" name="subAdminNameCh[]" placeholder="' +
                ENTER_CHINESE_NAME +
                '">' +
                '<span class="error-msg subadminnamech_err"></span>' +
                "</div>" +
                '<div class="form-group col-md-3">' +
                '<label class="text-bold-600">' +
                EMAIL +
                "</label>" +
                '<input type="text" class="form-control subAdminEmail" name="subAdminEmail[]" placeholder="' +
                ENTER_EMAIL +
                '" value="">' +
                '<span class="error-msg subadminemail_err"></span>' +
                "</div>" +
                '<div class="form-group col-md-2">' +
                '<label class="text-bold-600">' +
                PASSWORD +
                "</label>" +
                '<input type="password" class="form-control subAdminPassword" name="subAdminPassword[]" placeholder="****" value="">' +
                '<span class="error-msg subadminpassword_err"></span>' +
                "</div>" +
                '<div class="form-group col-md-1 add-admin-remove">' +
                '<a class="removeMoreAdmin btn btn-sm">X</a>' +
                "</div>" +
                "</div>";

            $("#more-admin").append($MoreAdminHtml);
        });
        /** USE : Remove Sub Admin in Admin Panel from Add new School  From Html */
        $(document).on("click", ".removeMoreAdmin", function (e) {
            //$(this).parent('div').parent('div').remove();
            var dataid = $(this).data("id");
            var userDiv = $(this).parent("div").parent("div");
            if (dataid) {
                $.confirm({
                    title: DELETE_USER + "?",
                    content: CONFIRMATION,
                    autoClose: "Cancellation|8000",
                    buttons: {
                        deleteUser: {
                            text: DELETE_USER,
                            action: function () {
                                $("#cover-spin").show();
                                $.ajax({
                                    url: BASE_URL + "/user/delete/" + dataid,
                                    type: "GET",
                                    success: function (response) {
                                        $("#cover-spin").hide();
                                        var data = JSON.parse(
                                            JSON.stringify(response)
                                        );
                                        if (data.status === "success") {
                                            toastr.success(data.message);
                                            userDiv.remove();
                                        } else {
                                            toastr.error(data.message);
                                        }
                                    },
                                    error: function (response) {
                                        ErrorHandlingMessage(response);
                                    },
                                });
                            },
                        },
                        Cancellation: function () {},
                    },
                });
            } else {
                $(this).parent("div").parent("div").remove();
            }
        });

        /**
         * USE : After that student all attempt questions and click on submit button
         */
        $(document).on("click", "#submitquestion", function (e) {
            $("#cover-spin").show();
        });

        /**
         * USE : On click My study configuration icon  in the student panel
         */
        $(document).on("click", "#my-study-config-btn", function (e) {
            $("#my-study-config").modal("show");
        });

        /**
         * USE : Get nodes list based on select schools
         */
        $(document).on("click", ".test-tab", function (e) {
            if ($(this).attr("data-id") == "excerxise-tab") {
                $(".test-color-info").show();
            } else {
                $(".test-color-info").hide();
            }
        });

        /**
         * USE : On click th then class test report sorting sort by student name "ASC OR DESC"
         */
        $(document).on("click", ".sorting_column", function (e) {
            $("#cover-spin").show();
            $sortingValue = $(this).attr("data-sort");
            $dataSortType = $(this).attr("data-sort-type");
            if ($sortingValue == "") {
                $(this).attr("data-sort", "asc");
                if ($dataSortType == "student_name") {
                    $(".student-name-sorting-icon").html(
                        '<i class="fa fa-sort-asc">'
                    );
                }
                if ($dataSortType == "student_rank") {
                    $(".student-rank-sorting-icon").html(
                        '<i class="fa fa-sort-asc">'
                    );
                }
                if ($dataSortType == "ability_rank") {
                    $(".student-ability-rank-sorting-icon").html(
                        '<i class="fa fa-sort-asc">'
                    );
                }
                if ($dataSortType == "accuracy_rank") {
                    $(".student-accuracy-rank-sorting-icon").html(
                        '<i class="fa fa-sort-asc">'
                    );
                }
            }
            if ($sortingValue == "asc") {
                $(this).attr("data-sort", "desc");
                if ($dataSortType == "student_name") {
                    $(".student-name-sorting-icon").html(
                        '<i class="fa fa-sort-desc">'
                    );
                }
                if ($dataSortType == "student_rank") {
                    $(".student-rank-sorting-icon").html(
                        '<i class="fa fa-sort-desc">'
                    );
                }
                if ($dataSortType == "ability_rank") {
                    $(".student-ability-rank-sorting-icon").html(
                        '<i class="fa fa-sort-desc">'
                    );
                }
                if ($dataSortType == "accuracy_rank") {
                    $(".student-accuracy-rank-sorting-icon").html(
                        '<i class="fa fa-sort-desc">'
                    );
                }
            }
            if ($sortingValue == "desc") {
                $(this).attr("data-sort", "asc");
                if ($dataSortType == "student_name") {
                    $(".student-name-sorting-icon").html(
                        '<i class="fa fa-sort-asc">'
                    );
                }
                if ($dataSortType == "student_rank") {
                    $(".student-rank-sorting-icon").html(
                        '<i class="fa fa-sort-asc">'
                    );
                }
                if ($dataSortType == "ability_rank") {
                    $(".student-ability-rank-sorting-icon").html(
                        '<i class="fa fa-sort-asc">'
                    );
                }
                if ($dataSortType == "accuracy_rank") {
                    $(".student-accuracy-rank-sorting-icon").html(
                        '<i class="fa fa-sort-asc">'
                    );
                }
            }

            var oldURL = window.location.href;
            if (history.pushState) {
                var newUrl = updateQueryStringParameter(
                    oldURL,
                    "sort_by_type",
                    $(this).attr("data-sort-type")
                );
                var newUrl1 = updateQueryStringParameter(
                    newUrl,
                    "sort_by_value",
                    $(this).attr("data-sort")
                );
                window.history.pushState({ path: newUrl }, "", newUrl1);
                location.reload();
            }
        });

        // For Back Button Code
        $(document).on("click", "#backButton", function (e) {
            e = e || window.event; // support  for IE8 and lower
            e.preventDefault(); // stop browser from doing native logic
            window.history.back();
        });

        // Sidebar open & Close set session
        $(document).on("click", "#sidebarCollapse", function () {
            if ($("#content").hasClass("sidebar-open")) {
                $("#content").removeClass("sidebar-open");
                $("#content").addClass("sidebar-close");
                $("#sidebar").removeClass("inactive");
                $("#sidebar").addClass("active");
                $.ajax({
                    url: BASE_URL + "/set-sidebar-class",
                    type: "GET",
                    data: {
                        sidebar: "active",
                        sidebar_option: "sidebar-close",
                    },
                    success: function (response) {},
                    error: function (response) {},
                });
            } else {
                $("#content").removeClass("sidebar-close");
                $("#content").addClass("sidebar-open");
                $("#sidebar").removeClass("active");
                $("#sidebar").addClass("inactive");
                $.ajax({
                    url: BASE_URL + "/set-sidebar-class",
                    type: "GET",
                    data: {
                        sidebar: "inactive",
                        sidebar_option: "sidebar-open",
                    },
                    success: function (response) {},
                    error: function (response) {},
                });
            }
        });

        //For a Login Button Click open Login Type Buttons
        // $(document).on('click', '#enter-btn', function() {
        //     $('#enter-login-btn').attr('style','display:none !important');
        //     $('#main-login-btn').attr('style','display:block !important');
        // });

        // $(document).on('click', '.loginType', function() {
        //     $('#login-type').val($(this).attr('data-loginType'));
        // });

        /**
         * USE : Get The student detail report into class-test-report-performance
         */
        // $(document).on("click", ".report-student-name", function () {
        //     $("#cover-spin").show();
        //     $studentId = $(this).attr("data-id");
        //     $examid = $(this).attr("data-examid");
        //     var isGroupId = $(this).attr("data-isgroupid");
        //     if ($(this).hasClass("minus-icon")) {
        //         $(this).removeClass("minus-icon");
        //         $(this).addClass("plus-icon");
        //     } else {
        //         $(this).removeClass("plus-icon");
        //         $(this).addClass("minus-icon");
        //     }
        //     // send ajax to get data
        //     $.ajax({
        //         url:
        //             BASE_URL +
        //             "/exams/ajax/result/" +
        //             $examid +
        //             "/" +
        //             $studentId,
        //         type: "GET",
        //         data: {
        //             _token: $('meta[name="csrf-token"]').attr("content"),
        //             exam_id: $examid,
        //             student_id: $studentId,
        //             isGroupId: isGroupId,
        //         },
        //         success: function (response) {
        //             var response = JSON.parse(JSON.stringify(response));
        //             $(".expand_student_report_student_" + $studentId).html(
        //                 response.data.html
        //             );
        //             //updateMathHtml();
        //             MathJax.Hub.Queue(["Typeset", MathJax.Hub]);
        //             $("#cover-spin").hide();
        //         },
        //         error: function (response) {
        //             ErrorHandlingMessage(response);
        //         },
        //     });
        //     $("#student_" + $studentId).toggleClass("expand-result");
        // });

        /**
         * Trigger Event : On click 'minus-icon' then close sub class test report page
         */
        $(document).on("click", ".minus-icon", function () {
            $(".report-student-name").removeClass("minus-icon");
            $(".report-student-name").addClass("plus-icon");
            $(".child-report-section-detail").removeClass("expand-result");
        });

        /**
         * Trigger Event : On click 'plus-icon' then open sub class test report page
         */
        $(document).on("click", ".plus-icon", function () {
            $("#cover-spin").show();
            $studentId = $(this).attr("data-id");
            $examid = $(this).attr("data-examid");
            var isGroupId = $(this).attr("data-isgroupid");
            $(".report-student-name").removeClass("minus-icon");
            $(".report-student-name").addClass("plus-icon");
            $(".child-report-section-detail").removeClass("expand-result");
            $(this).removeClass("plus-icon").addClass("minus-icon");
            // send ajax to get data
            $.ajax({
                url:
                    BASE_URL +
                    "/exams/ajax/result/" +
                    $examid +
                    "/" +
                    $studentId,
                type: "GET",
                data: {
                    _token: $('meta[name="csrf-token"]').attr("content"),
                    exam_id: $examid,
                    student_id: $studentId,
                    isGroupId: isGroupId,
                },
                success: function (response) {
                    var response = JSON.parse(JSON.stringify(response));
                    $(".expand_student_report_student_" + $studentId).html(
                        response.data.html
                    );
                    //updateMathHtml();
                    MathJax.Hub.Queue(["Typeset", MathJax.Hub]);
                    $("#cover-spin").hide();
                },
                error: function (response) {
                    ErrorHandlingMessage(response);
                },
            });
            $("#student_" + $studentId).toggleClass("expand-result");
        });

        /**
         * USE : Get The Single student detail report into class-test-report-performance
         */
        $(document).on("click", ".report-student-name-result", function () {
            $(".child-report-section-detail").removeClass("expand-result");
            $(".report-student-name-result").removeClass("minus-icon");
            $(".report-student-name-result").addClass("plus-icon");

            $("#cover-spin").show();
            $studentId = $(this).attr("data-id");
            $examid = $(this).attr("data-examid");
            if ($(this).hasClass("minus-icon")) {
                $(this).removeClass("minus-icon");
                $(this).addClass("plus-icon");
            } else {
                $(this).removeClass("plus-icon");
                $(this).addClass("minus-icon");
            }

            // send ajax to get data
            $.ajax({
                url:
                    BASE_URL +
                    "/exams/ajax/result/single-student/" +
                    $examid +
                    "/" +
                    $studentId,
                type: "GET",
                data: {
                    exam_id: $examid,
                    student_id: $studentId,
                },
                success: function (response) {
                    var response = JSON.parse(JSON.stringify(response));
                    $(".expand_student_report_student_" + $studentId).html(
                        response.data.html
                    );
                    //updateMathHtml();
                    MathJax.Hub.Queue(["Typeset", MathJax.Hub]);
                    $("#cover-spin").hide();
                },
                error: function (response) {
                    ErrorHandlingMessage(response);
                },
            });
            $("#student_" + $studentId).toggleClass("expand-result");
        });

        // Logout Function
        $(document).on("click", "#logout", function () {
            $("#cover-spin").show();
            $.ajax({
                url: BASE_URL + "/logout",
                type: "GET",
                success: function (response) {
                    $("#cover-spin").hide();
                    var data = JSON.parse(JSON.stringify(response));
                    if (data.status === "success") {
                        toastr.success(data.message);
                        window.location = data.data.redirectUrl;
                    } else {
                        toastr.error(data.message);
                    }
                },
                error: function (response) {
                    ErrorHandlingMessage(response);
                },
            });
        });

        //Delete Exam
        $(document).on("click", "#deleteExam", function () {
            var dataid = $(this).data("id");
            var tr = $(this).closest("tr");
            $.confirm({
                title: DELETE_EXAM + "?",
                content: CONFIRMATION,
                autoClose: "Cancellation|8000",
                buttons: {
                    deleteExam: {
                        text: DELETE_EXAM,
                        action: function () {
                            $("#cover-spin").show();
                            $.ajax({
                                url: BASE_URL + "/exams/delete/" + dataid,
                                type: "GET",
                                success: function (response) {
                                    $("#cover-spin").hide();
                                    var data = JSON.parse(
                                        JSON.stringify(response)
                                    );
                                    if (data.status === "success") {
                                        toastr.success(data.message);
                                        tr.fadeOut(500, function () {
                                            $(this).remove();
                                        });
                                    } else {
                                        toastr.error(data.message);
                                    }
                                },
                                error: function (response) {
                                    ErrorHandlingMessage(response);
                                },
                            });
                        },
                    },
                    Cancellation: function () {},
                },
            });
        });

        //Delete Role
        $(document).on("click", "#deleteRole", function () {
            var dataid = $(this).data("id");
            var tr = $(this).closest("tr");
            $.confirm({
                title: DELETE_ROLE + "?",
                content: CONFIRMATION,
                autoClose: "Cancellation|8000",
                buttons: {
                    deleteRole: {
                        text: DELETE_ROLE,
                        action: function () {
                            $("#cover-spin").show();
                            $.ajax({
                                url:
                                    BASE_URL +
                                    "/rolesmanagement/delete/" +
                                    dataid,
                                type: "GET",
                                success: function (response) {
                                    $("#cover-spin").hide();
                                    var data = JSON.parse(
                                        JSON.stringify(response)
                                    );
                                    if (data.status === "success") {
                                        toastr.success(data.message);
                                        tr.fadeOut(500, function () {
                                            $(this).remove();
                                        });
                                    } else {
                                        toastr.error(data.message);
                                    }
                                },
                                error: function (response) {
                                    ErrorHandlingMessage(response);
                                },
                            });
                        },
                    },
                    Cancellation: function () {},
                },
            });
        });

        //Delete Module
        $(document).on("click", "#deleteModule", function () {
            var dataid = $(this).data("id");
            var tr = $(this).closest("tr");
            $.confirm({
                title: DELETE_MODULE + "?",
                content: CONFIRMATION,
                autoClose: "Cancellation|8000",
                buttons: {
                    deleteModule: {
                        text: DELETE_MODULE,
                        action: function () {
                            $("#cover-spin").show();
                            $.ajax({
                                url:
                                    BASE_URL +
                                    "/modulesmanagement/delete/" +
                                    dataid,
                                type: "GET",
                                success: function (response) {
                                    $("#cover-spin").hide();
                                    var data = JSON.parse(
                                        JSON.stringify(response)
                                    );
                                    if (data.status === "success") {
                                        toastr.success(data.message);
                                        tr.fadeOut(500, function () {
                                            $(this).remove();
                                        });
                                    } else {
                                        toastr.error(data.message);
                                    }
                                },
                                error: function (response) {
                                    ErrorHandlingMessage(response);
                                },
                            });
                        },
                    },
                    Cancellation: function () {},
                },
            });
        });

        //Delete Learning Units
        $(document).on("click", "#deleteLearning_units", function () {
            var dataid = $(this).data("id");
            var tr = $(this).closest("tr");
            $.confirm({
                title: DELETE_LEARNING_UNITS + "?",
                content: CONFIRMATION,
                autoClose: "Cancellation|8000",
                buttons: {
                    deleteModule: {
                        text: DELETE_LEARNING_UNITS,
                        action: function () {
                            $("#cover-spin").show();
                            $.ajax({
                                url:
                                    BASE_URL +
                                    "/learning_units/delete/" +
                                    dataid,
                                type: "GET",
                                success: function (response) {
                                    $("#cover-spin").hide();
                                    var data = JSON.parse(
                                        JSON.stringify(response)
                                    );
                                    if (data.status === "success") {
                                        toastr.success(data.message);
                                        tr.fadeOut(500, function () {
                                            $(this).remove();
                                        });
                                    } else {
                                        toastr.error(data.message);
                                    }
                                },
                                error: function (response) {
                                    ErrorHandlingMessage(response);
                                },
                            });
                        },
                    },
                    Cancellation: function () {},
                },
            });
        });

        //Delete School
        $(document).on("click", "#deleteSchool", function () {
            var dataid = $(this).data("id");
            var tr = $(this).closest("tr");
            $.confirm({
                title: DELETE_SCHOOL + "?",
                content: CONFIRMATION,
                autoClose: "Cancellation|8000",
                buttons: {
                    deleteSchool: {
                        text: DELETE_SCHOOL,
                        action: function () {
                            $("#cover-spin").show();
                            $.ajax({
                                url: BASE_URL + "/school/delete/" + dataid,
                                type: "GET",
                                success: function (response) {
                                    $("#cover-spin").hide();
                                    var data = JSON.parse(
                                        JSON.stringify(response)
                                    );
                                    if (data.status === "success") {
                                        toastr.success(data.message);
                                        tr.fadeOut(500, function () {
                                            $(this).remove();
                                        });
                                    } else {
                                        toastr.error(data.message);
                                    }
                                },
                                error: function (response) {
                                    ErrorHandlingMessage(response);
                                },
                            });
                        },
                    },
                    Cancellation: function () {},
                },
            });
        });

        //Delete Node
        $(document).on("click", "#deleteNode", function () {
            var dataid = $(this).data("id");
            var tr = $(this).closest("tr");
            $.confirm({
                title: DELETE_NODE + "?",
                content: CONFIRMATION,
                autoClose: "Cancellation|8000",
                buttons: {
                    deleteNode: {
                        text: DELETE_NODE,
                        action: function () {
                            $("#cover-spin").show();
                            $.ajax({
                                url: BASE_URL + "/nodes/delete/" + dataid,
                                type: "GET",
                                success: function (response) {
                                    $("#cover-spin").hide();
                                    var data = JSON.parse(
                                        JSON.stringify(response)
                                    );
                                    if (data.status === "success") {
                                        toastr.success(data.message);
                                        tr.fadeOut(500, function () {
                                            $(this).remove();
                                        });
                                    } else {
                                        toastr.error(data.message);
                                    }
                                },
                                error: function (response) {
                                    ErrorHandlingMessage(response);
                                },
                            });
                        },
                    },
                    Cancellation: function () {},
                },
            });
        });

        //Delete SubAdmin
        $(document).on("click", "#deleteSubAdmin", function () {
            var dataid = $(this).data("id");
            var tr = $(this).closest("tr");
            $.confirm({
                title: DELETE_SUB_ADMIN + "?",
                content: CONFIRMATION,
                autoClose: "Cancellation|8000",
                buttons: {
                    deleteSubAdmin: {
                        text: DELETE_SUB_ADMIN,
                        action: function () {
                            $("#cover-spin").show();
                            $.ajax({
                                url: BASE_URL + "/sub-admin/delete/" + dataid,
                                type: "GET",
                                success: function (response) {
                                    $("#cover-spin").hide();
                                    var data = JSON.parse(
                                        JSON.stringify(response)
                                    );
                                    if (data.status === "success") {
                                        toastr.success(data.message);
                                        tr.fadeOut(500, function () {
                                            $(this).remove();
                                        });
                                    } else {
                                        toastr.error(data.message);
                                    }
                                },
                                error: function (response) {
                                    ErrorHandlingMessage(response);
                                },
                            });
                        },
                    },
                    Cancellation: function () {},
                },
            });
        });

        // Delete Question
        $(document).on("click", "#deleteQuestion", function () {
            var dataid = $(this).data("id");
            var tr = $(this).closest("tr");
            $.confirm({
                title: DELETE_QUESTION + "?",
                content: CONFIRMATION,
                autoClose: "Cancellation|8000",
                buttons: {
                    deleteQuestion: {
                        text: DELETE_QUESTION,
                        action: function () {
                            $("#cover-spin").show();
                            $.ajax({
                                url: BASE_URL + "/question/delete/" + dataid,
                                type: "GET",
                                success: function (response) {
                                    $("#cover-spin").hide();
                                    var data = JSON.parse(
                                        JSON.stringify(response)
                                    );
                                    if (data.status === "success") {
                                        toastr.success(data.message);
                                        tr.fadeOut(500, function () {
                                            $(this).remove();
                                        });
                                    } else {
                                        toastr.error(data.message);
                                    }
                                },
                                error: function (response) {
                                    ErrorHandlingMessage(response);
                                },
                            });
                        },
                    },
                    Cancellation: function () {},
                },
            });
        });

        // Delete Users
        $(document).on("click", "#deleteUser", function () {
            var dataid = $(this).data("id");
            var tr = $(this).closest("tr");
            $.confirm({
                title: DELETE_USER + "?",
                content: CONFIRMATION,
                autoClose: "Cancellation|8000",
                buttons: {
                    deleteUser: {
                        text: DELETE_USER,
                        action: function () {
                            $("#cover-spin").show();
                            $.ajax({
                                url: BASE_URL + "/user/delete/" + dataid,
                                type: "GET",
                                success: function (response) {
                                    $("#cover-spin").hide();
                                    var data = JSON.parse(
                                        JSON.stringify(response)
                                    );
                                    if (data.status === "success") {
                                        toastr.success(data.message);
                                        tr.fadeOut(500, function () {
                                            $(this).remove();
                                        });
                                    } else {
                                        toastr.error(data.message);
                                    }
                                },
                                error: function (response) {
                                    ErrorHandlingMessage(response);
                                },
                            });
                        },
                    },
                    Cancellation: function () {},
                },
            });
        });

        // Check all attempt students exams validation
        $(document).on("click", "#submit-exams", function (event) {
            //Make groups
            var names = [];
            $("input:radio").each(function () {
                var rname = $(this).attr("name");
                if ($.inArray(rname, names) === -1) names.push(rname);
            });

            //do validation for each group
            $status = true;
            $.each(names, function (i, name) {
                $("#" + name)
                    .text("")
                    .removeClass("error-option");
                if ($('input[name="' + name + '"]:checked').length === 0) {
                    $status = false;
                    $("#" + name)
                        .text("Please select answer")
                        .addClass("error-option");
                } else {
                    if ($status) {
                        $status = true;
                    }
                }
            });
            if (!$status) {
                $([document.documentElement, document.body]).animate(
                    {
                        scrollTop: $(".error-option").offset().top,
                    },
                    1000
                );
                return false;
            }
            $("#attempt-exams").submit();
            $("#submit-exams").prop("disabled", true);
        });

        //view result student open on popup
        $(document).on("click", ".view-result-btn", function () {
            $("#cover-spin").show();
        });

        //Delete Student from teacher panel
        $(document).on("click", "#deleteStudent", function () {
            var dataid = $(this).data("id");
            var tr = $(this).closest("tr");
            $.confirm({
                title: DELETE_STUDENT + "?",
                content: CONFIRMATION,
                autoClose: "Cancellation|8000",
                buttons: {
                    deletestudent: {
                        text: DELETE_STUDENT,
                        action: function () {
                            $.ajax({
                                url: BASE_URL + "/Student/delete/" + dataid,
                                type: "GET",
                                data: {
                                    _token: $('meta[name="csrf-token"]').attr(
                                        "content"
                                    ),
                                },
                                success: function (response) {
                                    $("#cover-spin").hide();
                                    var data = JSON.parse(
                                        JSON.stringify(response)
                                    );
                                    if (data.status === "success") {
                                        toastr.success(data.message);
                                        tr.fadeOut(500, function () {
                                            $(this).remove();
                                        });
                                    } else {
                                        toastr.error(data.message);
                                    }
                                },
                                error: function (response) {
                                    ErrorHandlingMessage(response);
                                },
                            });
                        },
                    },
                    Cancellation: function () {},
                },
            });
        });

        $(document).on("click", "#filterReportClassTestResult", function () {
            $examId = $("#exam_id").val();
            if ($examId) {
                $("#class-test-report").submit();
            } else {
                toastr.error(PLEASE_SELECT_TEST);
                return false;
            }
        });

        /**
         * USE : GET THE GROUP DATA IN PERFORMANCE REPORT
         */
        $(document).on("change", ".performance_exam_id", function () {
            $("#cover-spin").show();
            var exam_id = $(this).val();
            var ExamType = $(this).find(":selected").data("examtype");
            if (ExamType == 1) {
                $(".class-performance-class-ability-report").hide();
            } else {
                $(".class-performance-class-ability-report").show();
            }
            $.ajax({
                url: BASE_URL + "/getExamGroupGradeClassList",
                type: "GET",
                data: {
                    exam_id: exam_id,
                },
                success: function (response) {
                    $(".performance_group_id").html("");
                    $("#exam_school_id").html("");
                    $(
                        ".class-performance-group-section,.class-performance-group-section"
                    ).hide();
                    $("#cover-spin").hide();
                    var data = JSON.parse(JSON.stringify(response));
                    if (isAdmin == 1) {
                        // Set Peer Group Dropdown
                        if (
                            data.data.school_List !== undefined &&
                            data.data.school_List.length != 0
                        ) {
                            /*$(".class-performance-grade-section,.class-performance-class-section").hide();
                            $(".class-performance-grade-section select,.class-performance-class-section select").prop('disabled',true);*/
                            $.each(
                                data.data.school_List,
                                function (index, value) {
                                    $("#exam_school_id").append(
                                        "<option value=" +
                                            value.id +
                                            ">" +
                                            value.name +
                                            "</option>"
                                    );
                                }
                            );
                            $(".exam-school-list").show();
                            $(".exam_school_id").change();
                            $(
                                ".class-performance-group-section,.class-performance-group-section"
                            ).hide();
                        } else {
                            // Set Peer Group Dropdown
                            if (data.data.peer_group_list !== undefined) {
                                $(".class-performance-group-section").show();
                                /*$(".class-performance-grade-section,.class-performance-class-section").hide();
                                $(".class-performance-grade-section select,.class-performance-class-section select").prop('disabled',true);*/
                                $(".class-performance-group-section").show();
                                $.each(
                                    data.data.peer_group_list,
                                    function (index, value) {
                                        $(".performance_group_id").append(
                                            "<option value=" +
                                                data.data.peer_group_list[index]
                                                    .id +
                                                ">" +
                                                data.data.peer_group_list[index]
                                                    .group_name +
                                                "</option>"
                                        );
                                    }
                                );
                            } else {
                                $(".class-performance-group-section").hide();
                            }

                            // Set Grade Dropdown in Class performance report
                            if (
                                data.data.grades_list !== undefined &&
                                data.data.grades_list.length != 0
                            ) {
                                $(
                                    ".class-performance-grade-section,.class-performance-class-section"
                                ).show();
                                /*$(".class-performance-group-section").hide();*/
                                $(
                                    ".class-performance-grade-section select,.class-performance-class-section select"
                                ).prop("disabled", false);
                                $(".class-performance-grade-section").show();
                                var GradeList = "";
                                $.each(
                                    data.data.grades_list,
                                    function (index, value) {
                                        GradeList +=
                                            "<option value=" +
                                            data.data.grades_list[index].id +
                                            " class_ids=''>" +
                                            data.data.grades_list[index].name +
                                            "</option>";
                                    }
                                );
                                $("#student_performance_grade_id").html(
                                    GradeList
                                );
                            } else {
                                $(".class-performance-grade-section").hide();
                            }

                            // Set Grade Dropdown in Class performance report
                            if (
                                data.data.grades_list !== undefined &&
                                data.data.class_list.length != 0
                            ) {
                                $(".class-performance-class-section").show();
                                var ClassList = "";
                                $.each(
                                    data.data.class_list,
                                    function (index, value) {
                                        console.log(data.data.class_list);
                                        ClassList +=
                                            "<option value=" +
                                            data.data.class_list[index].id +
                                            ">" +
                                            value.grade.name +
                                            "-" +
                                            data.data.class_list[index].name +
                                            "</option>";
                                        var class_ids = $(
                                            "#student_performance_grade_id"
                                        )
                                            .find(
                                                "option[value=" +
                                                    value.grade_id +
                                                    "]"
                                            )
                                            .attr("class_ids");

                                        if (class_ids != "") {
                                            class_ids =
                                                class_ids +
                                                "," +
                                                data.data.class_list[index].id;
                                        } else {
                                            class_ids =
                                                data.data.class_list[index].id;
                                        }
                                        if (class_ids == "") {
                                            class_ids = "";
                                        }
                                        $("#student_performance_grade_id")
                                            .find(
                                                "option[value=" +
                                                    value.grade_id +
                                                    "]"
                                            )
                                            .attr("class_ids", class_ids);
                                    }
                                );
                                $("#classType-select-option").html(ClassList);
                                $("#classType-select-option").multiselect(
                                    "rebuild"
                                );
                                $("#student_performance_grade_id").change();
                            } else {
                                $(".class-performance-class-section").hide();
                            }
                        }
                    } else {
                        // Set Peer Group Dropdown
                        if (data.data.peer_group_list !== undefined) {
                            if (
                                isAdmin == 1 ||
                                isSchoolLogin == 1 ||
                                isTeacherLogin == 1
                            ) {
                                $(".class-performance-group-section").show();
                                $(
                                    ".class-performance-grade-section,.class-performance-class-section"
                                ).hide();
                                $(
                                    ".class-performance-grade-section select,.class-performance-class-section select"
                                ).prop("disabled", true);
                            }
                            $(".class-performance-group-section").show();
                            $.each(
                                data.data.peer_group_list,
                                function (index, value) {
                                    $(".performance_group_id").append(
                                        "<option value=" +
                                            data.data.peer_group_list[index]
                                                .id +
                                            ">" +
                                            data.data.peer_group_list[index]
                                                .group_name +
                                            "</option>"
                                    );
                                }
                            );
                        } else {
                            $(".class-performance-group-section").hide();
                        }

                        // Set Grade Dropdown in Class performance report
                        if (
                            data.data.grades_list !== undefined &&
                            data.data.grades_list.length != 0
                        ) {
                            if (
                                isAdmin == 1 ||
                                isSchoolLogin == 1 ||
                                isTeacherLogin == 1
                            ) {
                                $(".class-performance-group-section").hide();
                                $(
                                    ".class-performance-grade-section,.class-performance-class-section"
                                ).show();
                                $(
                                    ".class-performance-grade-section select,.class-performance-class-section select"
                                ).prop("disabled", false);
                            }
                            $(".class-performance-grade-section").show();
                            var GradeList = "";
                            $.each(
                                data.data.grades_list,
                                function (index, value) {
                                    GradeList +=
                                        "<option value=" +
                                        data.data.grades_list[index].id +
                                        " class_ids=''>" +
                                        data.data.grades_list[index].name +
                                        "</option>";
                                }
                            );
                            $("#student_performance_grade_id").html(GradeList);
                        } else {
                            $(".class-performance-grade-section").hide();
                        }
                        if (
                            data.data.grades_list !== undefined &&
                            data.data.class_list.length != 0
                        ) {
                            $(".class-performance-class-section").show();
                            var ClassList = "";
                            $.each(
                                data.data.class_list,
                                function (index, value) {
                                    ClassList +=
                                        "<option value=" +
                                        data.data.class_list[index].id +
                                        ">" +
                                        value.grade.name +
                                        "-" +
                                        data.data.class_list[index].name +
                                        "</option>";
                                    var class_ids = $(
                                        "#student_performance_grade_id"
                                    )
                                        .find(
                                            "option[value=" +
                                                value.grade_id +
                                                "]"
                                        )
                                        .attr("class_ids");

                                    if (class_ids != "") {
                                        class_ids =
                                            class_ids +
                                            "," +
                                            data.data.class_list[index].id;
                                    } else {
                                        class_ids =
                                            data.data.class_list[index].id;
                                    }
                                    if (class_ids == "") {
                                        class_ids = "";
                                    }
                                    $("#student_performance_grade_id")
                                        .find(
                                            "option[value=" +
                                                value.grade_id +
                                                "]"
                                        )
                                        .attr("class_ids", class_ids);
                                }
                            );
                            $("#classType-select-option").html(ClassList);
                            $("#classType-select-option").multiselect(
                                "rebuild"
                            );
                            $("#student_performance_grade_id").change();
                        } else {
                            $(".class-performance-class-section").hide();
                        }
                    }
                },
                error: function (response) {
                    ErrorHandlingMessage(response);
                },
            });
        });

        /**
         * USE : GET THE GROUP DATA IN PERFORMANCE REPORT
         */
        $(document).on("change", ".exam_school_id", function () {
            $("#cover-spin").show();
            var exam_school_id = $(this).val();
            var exam_id = $(".performance_exam_id").val();
            $.ajax({
                url: BASE_URL + "/getExamGroupGradeClassList",
                type: "GET",
                data: {
                    exam_id: exam_id,
                    exam_school_id: exam_school_id,
                },
                success: function (response) {
                    $(".performance_group_id").html("");
                    $("#student_performance_grade_id").html("");
                    $("#classType-select-option").html("");
                    $(
                        ".class-performance-group-section,.class-performance-group-section,.class-performance-grade-section,.class-performance-class-section"
                    ).hide();
                    $("#cover-spin").hide();
                    var data = JSON.parse(JSON.stringify(response));
                    if (isAdmin == 1) {
                        // Set Peer Group Dropdown
                        if (
                            data.data.peer_group_list !== undefined &&
                            data.data.peer_group_list.length != 0
                        ) {
                            $(".class-performance-group-section").show();
                            /*$(".class-performance-grade-section,.class-performance-class-section").hide();
                            $(".class-performance-grade-section select,.class-performance-class-section select").prop('disabled',true);*/
                            //$(".class-performance-group-section").show();
                            $.each(
                                data.data.peer_group_list,
                                function (index, value) {
                                    $(".performance_group_id").append(
                                        "<option value=" +
                                            data.data.peer_group_list[index]
                                                .id +
                                            ">" +
                                            data.data.peer_group_list[index]
                                                .group_name +
                                            "</option>"
                                    );
                                }
                            );
                        } else {
                            $(".class-performance-group-section").hide();
                        }

                        // Set Grade Dropdown in Class performance report
                        if (
                            data.data.grades_list !== undefined &&
                            data.data.grades_list.length != 0
                        ) {
                            $(
                                ".class-performance-grade-section,.class-performance-class-section"
                            ).show();
                            /*$(".class-performance-group-section").hide();*/
                            $(
                                ".class-performance-grade-section select,.class-performance-class-section select"
                            ).prop("disabled", false);
                            $(".class-performance-grade-section").show();
                            var GradeList = "";
                            $.each(
                                data.data.grades_list,
                                function (index, value) {
                                    GradeList +=
                                        "<option value=" +
                                        data.data.grades_list[index].id +
                                        " class_ids=''>" +
                                        data.data.grades_list[index].name +
                                        "</option>";
                                }
                            );
                            $("#student_performance_grade_id").html(GradeList);
                        } else {
                            $(".class-performance-grade-section").hide();
                        }

                        // Set Grade Dropdown in Class performance report
                        if (
                            data.data.grades_list !== undefined &&
                            data.data.grades_list.length != 0
                        ) {
                            $(".class-performance-class-section").show();
                            var ClassList = "";
                            $.each(
                                data.data.class_list,
                                function (index, value) {
                                    ClassList +=
                                        "<option value=" +
                                        data.data.class_list[index].id +
                                        ">" +
                                        value.grade.name +
                                        "-" +
                                        data.data.class_list[index].name +
                                        "</option>";
                                    var class_ids = $(
                                        "#student_performance_grade_id"
                                    )
                                        .find(
                                            "option[value=" +
                                                value.grade_id +
                                                "]"
                                        )
                                        .attr("class_ids");

                                    if (class_ids != "") {
                                        class_ids =
                                            class_ids +
                                            "," +
                                            data.data.class_list[index].id;
                                    } else {
                                        class_ids =
                                            data.data.class_list[index].id;
                                    }
                                    if (class_ids == "") {
                                        class_ids = "";
                                    }
                                    $("#student_performance_grade_id")
                                        .find(
                                            "option[value=" +
                                                value.grade_id +
                                                "]"
                                        )
                                        .attr("class_ids", class_ids);
                                }
                            );
                            $("#classType-select-option").html(ClassList);
                            $("#classType-select-option").multiselect(
                                "rebuild"
                            );
                            $("#student_performance_grade_id").change();
                        } else {
                            $(".class-performance-class-section").hide();
                        }
                    }
                },
                error: function (response) {
                    ErrorHandlingMessage(response);
                },
            });
        });

        /**
         * USE : GET THE GRADE DATA TO CLASS IN PERFORMANCE REPORT
         */
        $(document).on("change", "#student_performance_grade_id", function () {
            //$("#classType-select-option option").prop("disabled", false);
            $.ajax({
                url: BASE_URL + "/get-performance-report-class-type",
                type: "GET",
                data: {
                    examId: $("#exam_id").val(),
                    schoolId: $("#exam_school_id").val(),
                    grade_id: $(this).val(),
                },
                success: function (response) {
                    $("#cover-spin").hide();
                    $("#classType-select-option").html(response.data);

                    $("#classType-select-option").multiselect("rebuild");
                },
            });
            if (
                $("#student_performance_grade_id option:selected").length != 0
            ) {
                var classIds = $(
                    "#student_performance_grade_id option:selected"
                ).attr("class_ids");
                if (classIds != "") {
                    classIds = classIds.split(",");
                    $("#classType-select-option").val(classIds);
                }
            }
            // $("#classType-select-option option:not(:checked)").prop(
            //     "disabled",
            //     true
            // );
            $("#classType-select-option").multiselect("rebuild");
        });

        $(document).on("change", "#group-exam-ids", function () {
            if (this.checked) {
                var examIds = [];
                $(".exam-id").prop("checked", true);
                $("input:checkbox[name=examids]:checked").each(function () {
                    examIds.push($(this).val());
                });
            } else {
                // Clear all checkbox multiple exam deletes
                var examIds = [];
                $(".exam-id").prop("checked", false);
                $("input:checkbox[name=examids]:checked").each(function () {
                    examIds.remove($(this).val());
                });
                return false;
            }
        });

        /**
         * USE : Delete multiple exams
         */
        // $(document).on("click", "#delete-multiple-exams-btn", function () {
        //     $("#cover-spin").show();
        //     var examIds = [];
        //     $("input:checkbox[name=examids]:checked").each(function () {
        //         examIds.push($(this).val());
        //     });
        //     // Check examid array is empty or not
        //     if (examIds.length === 0) {
        //         $("#cover-spin").hide();
        //         toastr.error(
        //             PLEASE_SELECT_AT_LEAST_ONE_CHECKBOX_FOR_DELETE_TEST
        //         );
        //         return false;
        //     }
        //     // If array is not empty then delete multiple exams
        //     $.confirm({
        //         title: DELETE_TEST + "?",
        //         content: CONFIRMATION,
        //         autoClose: "Cancellation|8000",
        //         buttons: {
        //             deleteUser: {
        //                 text: DELETE_TEST,
        //                 action: function () {
        //                     $.ajax({
        //                         url: BASE_URL + "/exams/delete/multiple",
        //                         type: "POST",
        //                         data: {
        //                             _token: $('meta[name="csrf-token"]').attr(
        //                                 "content"
        //                             ),
        //                             examIds: examIds,
        //                         },
        //                         success: function (response) {
        //                             $("#cover-spin").hide();
        //                             var data = JSON.parse(
        //                                 JSON.stringify(response)
        //                             );
        //                             if (data.status === "success") {
        //                                 toastr.success(data.message);
        //                                 location.reload();
        //                             } else {
        //                                 toastr.error(data.message);
        //                                 return false;
        //                             }
        //                         },
        //                         error: function (response) {
        //                             ErrorHandlingMessage(response);
        //                         },
        //                     });
        //                 },
        //             },
        //             Cancellation: function () {
        //                 $("#cover-spin").hide();
        //             },
        //         },
        //     });
        // });

        /**
         * USE : Use To Check Answer Is Right Or Not
         */
        var wrong = 0;
        var questionIdArr = [];
        $(document).on("change", ".test-all .checkanswer", function () {
            $(".questionhint").hide();
            $("#nextquestion").prop("disabled", false);
            $("#submitquestion").hide();
            if ($("#nextquestion").length == 0) {
                $("#submitquestion").prop("disabled", false);
                $("#submitquestion").show();
            }
            var answer = $(this).val();
            var questionid = $(this).attr("question-id");
            var submitid = $("#submitquestion").attr("submit-id");
            questiontype = $(this).attr("question-type");
            var language = $("#attempt-exams input[name=language]").val();
            var checkarns = 0;
            question_arr = {
                question_id: questionid,
                answer: answer,
                language: language,
                duration_second: 0,
            };

            // Set Question Position Start
            var question_position_number = [];
            $(this)
                .closest(".attmp-main-answer")
                .find(".checkanswer")
                .each(function () {
                    question_position_number.push($(this).val());
                });
            var questionPositionNumberJson = JSON.stringify(
                question_position_number
            );
            if (question_position.length != 0) {
                checkarnsPo = 0;
                for (var ip = 0; ip < question_position.length; ip++) {
                    if (question_position[ip]["question_id"] == questionid) {
                        checkarnsPo++;
                        question_position_arr = {
                            question_id: questionid,
                            position: questionPositionNumberJson,
                        };
                        question_position[ip] = question_position_arr;
                    }
                }
                if (checkarns == 0) {
                    var question_position_arr = {
                        question_id: questionid,
                        position: questionPositionNumberJson,
                    };
                    question_position.push(question_position_arr);
                }
            } else {
                var question_position_arr = {
                    question_id: questionid,
                    position: questionPositionNumberJson,
                };
                question_position.push(question_position_arr);
            }
            // Set Question Position End

            if (question_old_data_new.length != 0) {
                for (var ic = 0; ic < question_old_data_new.length; ic++) {
                    if (
                        question_old_data_new[ic]["question_id"] == questionid
                    ) {
                        checkarns++;
                        question_arr = {
                            question_id: questionid,
                            answer: answer,
                            language: language,
                            duration_second:
                                question_old_data_new[ic]["duration_second"],
                        };
                        question_old_data_new[ic] = question_arr;
                    }
                }
                if (checkarns == 0) {
                    question_old_data_new.push(question_arr);
                }
            } else {
                question_old_data_new.push(question_arr);
            }
        });

        /**
         * USE : Use Next or Previous Question
         */
        $(document).on(
            "click",
            ".test-all #nextquestion,.test-all #prevquestion",
            function (e) {
                $("#cover-spin").show();
                var is_err = 0;
                var examaction = $(this).attr("data-text");
                if (examaction == "Next") {
                    questiontype = $(".checkanswer:checked").attr(
                        "question-type"
                    );
                    if (questiontype == 2 || questiontype == 1) {
                        var is_err = $(this).attr("iserr");
                    }
                    var currentid = $(this).attr("question-id-next");
                    questionNo = questionNo + 1;
                } else {
                    var currentid = $(this).attr("question-id-prev");
                    questionNo = questionNo - 1;
                }
                var wrong_old_data_new = $("#wrong_ans_id").val();
                var examid = $("input[name=exam_id]").val();
                var language = $("input[name=language]").val();

                // set question duration in second start
                if (examaction == "Next") {
                    var answer = $(".checkanswer:checked").val();
                    var questionid = $(".checkanswer:checked").attr(
                        "question-id"
                    );
                    var submitid = $("#submitquestion").attr("submit-id");
                    var language = $(
                        "#attempt-exams input[name=language]"
                    ).val();

                    if (question_old_data_new.length != 0) {
                        for (
                            var ics = 0;
                            ics < question_old_data_new.length;
                            ics++
                        ) {
                            if (
                                question_old_data_new[ics]["question_id"] ==
                                    questionid &&
                                question_old_data_new[ics]["duration_second"] ==
                                    0
                            ) {
                                question_arr = {
                                    question_id: questionid,
                                    answer: answer,
                                    language: language,
                                    duration_second: $QuestionTimer,
                                };
                                question_old_data_new[ics] = question_arr;
                            }
                        }
                    }
                    QuestionSecond = 0;
                }
                // set question duration in second end

                // send ajax to get data
                $.ajax({
                    url: BASE_URL + "/next-question",
                    type: "POST",
                    data: {
                        _token: $('meta[name="csrf-token"]').attr("content"),
                        currentid: currentid,
                        examid: examid,
                        examaction: examaction,
                        questionNo: questionNo,
                        language: language,
                        wrong_ans: wrong_question_old_data_new,
                        attempt_ans: attempt_ans,
                        // Get Question Position
                        question_position: question_position,
                    },
                    success: function (response) {
                        $("#cover-spin").hide();
                        $("#nextquestionarea").html(response);
                        if (question_old_data_new.length != 0) {
                            for (
                                var icn = 0;
                                icn < question_old_data_new.length;
                                icn++
                            ) {
                                var question_id_check = $(
                                    "#nextquestionarea .checkanswer"
                                ).attr("question-id");
                                if (
                                    question_old_data_new[icn]["question_id"] ==
                                    question_id_check
                                ) {
                                    var answer_ren =
                                        question_old_data_new[icn]["answer"];
                                    $(
                                        "#nextquestionarea .checkanswer[value=" +
                                            answer_ren +
                                            "]"
                                    ).prop("checked", true);
                                    $("#nextquestion").prop("disabled", false);
                                    if ($("#nextquestion").length == 0) {
                                        $("#submitquestion").prop(
                                            "disabled",
                                            false
                                        );
                                        $("#submitquestion").show();
                                    }
                                }
                            }
                        }
                        //updateMathHtml();
                        //updateMathHtmlById("nextquestionarea");

                        MathJax.Hub.Queue(["Typeset", MathJax.Hub]);
                        $("#cover-spin").hide();
                    },
                });
            }
        );

        /**
         * USE : Use Check Answer Question in Personal Exam
         */
        $(document).on("change", ".test-personal .checkanswer", function () {
            $(".questionhint").hide();
            $("#nextquestion").prop("disabled", false);
            $("#submitquestion").hide();
            // $('#submitquestion').prop('disabled', true);
            if ($("#nextquestion").length == 0) {
                $("#submitquestion").prop("disabled", false);
                $("#submitquestion").show();
            }
            var answer = $(this).val();
            var questionid = $(this).attr("question-id");
            var submitid = $("#submitquestion").attr("submit-id");
            questiontype = $(this).attr("question-type");
            var language = $("#attempt-exams input[name=language]").val();
            var checkarns = 0;
            question_arr = {
                question_id: questionid,
                answer: answer,
                language: language,
                duration_second: 0,
            };

            // Set Question Position Start
            var question_position_number = [];
            $(this)
                .closest(".attmp-main-answer")
                .find(".checkanswer")
                .each(function () {
                    question_position_number.push($(this).val());
                });
            var questionPositionNumberJson = JSON.stringify(
                question_position_number
            );
            if (question_position.length != 0) {
                checkarnsPo = 0;
                for (var ip = 0; ip < question_position.length; ip++) {
                    if (question_position[ip]["question_id"] == questionid) {
                        checkarnsPo++;
                        question_position_arr = {
                            question_id: questionid,
                            position: questionPositionNumberJson,
                        };
                        question_position[ip] = question_position_arr;
                    }
                }
                if (checkarns == 0) {
                    var question_position_arr = {
                        question_id: questionid,
                        position: questionPositionNumberJson,
                    };
                    question_position.push(question_position_arr);
                }
            } else {
                var question_position_arr = {
                    question_id: questionid,
                    position: questionPositionNumberJson,
                };
                question_position.push(question_position_arr);
            }
            // Set Question Position End

            if (question_old_data_new.length != 0) {
                for (var ic = 0; ic < question_old_data_new.length; ic++) {
                    if (
                        question_old_data_new[ic]["question_id"] == questionid
                    ) {
                        checkarns++;
                        question_arr = {
                            question_id: questionid,
                            answer: answer,
                            language: language,
                            duration_second:
                                question_old_data_new[ic]["duration_second"],
                        };
                        question_old_data_new[ic] = question_arr;
                    }
                }
                if (checkarns == 0) {
                    question_old_data_new.push(question_arr);
                }
            } else {
                question_old_data_new.push(question_arr);
            }
        });

        /**
         * USE : Use Next or Previous Question in Personal Exam
         */
        $(document).on(
            "click",
            ".test-personal #nextquestion,.test-personal #prevquestion",
            function (e) {
                $("#cover-spin").show();
                var is_err = 0;
                var examaction = $(this).attr("data-text");
                if (examaction == "Next") {
                    questiontype = $(".checkanswer:checked").attr(
                        "question-type"
                    );
                    if (questiontype == 2 || questiontype == 1) {
                        var is_err = $(this).attr("iserr");
                    }
                    var currentid = $(this).attr("question-id-next");
                    questionNo = questionNo + 1;
                } else {
                    var currentid = $(this).attr("question-id-prev");
                    questionNo = questionNo - 1;
                }
                var wrong_old_data_new = $("#wrong_ans_id").val();
                var examid = $("input[name=exam_id]").val();
                var language = $("input[name=language]").val();
                // set question duration in second start
                if (examaction == "Next") {
                    var answer = $(".checkanswer:checked").val();
                    var questionid = $(".checkanswer:checked").attr(
                        "question-id"
                    );
                    var submitid = $("#submitquestion").attr("submit-id");
                    var language = $(
                        "#attempt-exams input[name=language]"
                    ).val();

                    if (question_old_data_new.length != 0) {
                        for (
                            var ics = 0;
                            ics < question_old_data_new.length;
                            ics++
                        ) {
                            if (
                                question_old_data_new[ics]["question_id"] ==
                                    questionid &&
                                question_old_data_new[ics]["duration_second"] ==
                                    0
                            ) {
                                question_arr = {
                                    question_id: questionid,
                                    answer: answer,
                                    language: language,
                                    duration_second: $QuestionTimer,
                                };
                                question_old_data_new[ics] = question_arr;
                            }
                        }
                    }
                    QuestionSecond = 0;
                }
                // set question duration in second end
                // send ajax to get data
                $.ajax({
                    url: BASE_URL + "/next-question",
                    type: "POST",
                    data: {
                        _token: $('meta[name="csrf-token"]').attr("content"),
                        currentid: currentid,
                        examid: examid,
                        examaction: examaction,
                        questionNo: questionNo,
                        language: language,
                        wrong_ans: wrong_question_old_data_new,
                        attempt_ans: attempt_ans,
                        // Get Question Position
                        question_position: question_position,
                    },
                    success: function (response) {
                        $("#nextquestionarea").html(response);
                        if (question_old_data_new.length != 0) {
                            for (
                                var icn = 0;
                                icn < question_old_data_new.length;
                                icn++
                            ) {
                                var question_id_check = $(
                                    "#nextquestionarea .checkanswer"
                                ).attr("question-id");
                                if (
                                    question_old_data_new[icn]["question_id"] ==
                                    question_id_check
                                ) {
                                    var answer_ren =
                                        question_old_data_new[icn]["answer"];
                                    $(
                                        "#nextquestionarea .checkanswer[value=" +
                                            answer_ren +
                                            "]"
                                    ).prop("checked", true);
                                    $("#nextquestion").prop("disabled", false);
                                    if ($("#nextquestion").length == 0) {
                                        $("#submitquestion").prop(
                                            "disabled",
                                            false
                                        );
                                        $("#submitquestion").show();
                                    }
                                }
                            }
                        }
                        //updateMathHtml();
                        //updateMathHtmlById("nextquestionarea");
                        MathJax.Hub.Queue(["Typeset", MathJax.Hub]);
                        $("#cover-spin").hide();
                    },
                });
            }
        );

        //USE : userlist in click on unlock button then open Pop up
        $(document).on("click", ".changeUserPassword", function () {
            $("#changePasswordUserId").val($(this).attr("data-id"));
            $("#changeUserPwd").modal("show");
        });

        //USE :  userlist in click on unlock button then Close Pop up
        $(document).on(
            "click",
            ".close-userChangePassword-popup,.close",
            function () {
                $(".class-ability-graph-btn").attr(
                    "data-classAbilityIsGroup",
                    "false"
                );
                closePopupModal("changeUserPwd");
                $(".changeUserPwd").modal("hide");
            }
        );
    },
};

// All validations
Validation = {
    init: function () {
        Validation.validforms();
    },
    validforms: function () {
        // Validate form for create peer group
        $("#addPeerGroupForm").validate({
            rules: {
                group_name: {
                    required: true,
                },
            },
            messages: {
                group_name: {
                    required: VALIDATIONS.PLEASE_ENTER_PEER_GROUP_NAME,
                },
            },
            submitHandler: function (form) {
                $("#cover-spin").show();
                if (
                    $("#dreamschat_group_id").length != 0 &&
                    $("#dreamschat_group_id").val() != ""
                ) {
                    var groupId = $("#dreamschat_group_id").val();
                    // Check Group Exists or not
                    //var checkGroup=checkGroupExists(groupId);
                    var checkGroup = firebase
                        .database()
                        .ref("data/groups/" + groupId);
                    checkGroup.once("value", function (snapshot) {
                        if (snapshot.exists()) {
                            var GroupAdminData = "";
                            var dreamschat_group_id = $(
                                "#dreamschat_group_id"
                            ).val();
                            var RemoveMemberList = GroupMemberOldList.filter(
                                (x) => GroupMemberList.indexOf(x) === -1
                            );

                            var newMemberList = GroupMemberList.filter(
                                (x) => GroupMemberOldList.indexOf(x) === -1
                            );
                            var adaRef = firebase
                                .database()
                                .ref("data/groups/" + dreamschat_group_id);
                            adaRef.once("value", function (snapshot) {
                                var userarray = snapshot.val().userIds;
                                if (
                                    snapshot.val().grpExitUserIds != undefined
                                ) {
                                    var grpExitUserIds =
                                        snapshot.val().grpExitUserIds;
                                } else {
                                    var grpExitUserIds = [];
                                }
                                var removePromises = [];
                                for (
                                    var ri = 0;
                                    ri < RemoveMemberList.length;
                                    ri++
                                ) {
                                    var grpExitUserIds = [];
                                    var request = $.ajax({
                                        url: BASE_URL + "/get-user-info",
                                        type: "GET",
                                        data: {
                                            uid: RemoveMemberList[ri],
                                        },
                                        success: function (response) {
                                            if (response.length != 0) {
                                                deleteGroupMember(
                                                    response.data,
                                                    grpExitUserIds,
                                                    dreamschat_group_id
                                                );
                                            }
                                        },
                                    });
                                    removePromises.push(request);
                                }
                                $.when
                                    .apply(null, removePromises)
                                    .done(function () {
                                        if (newMemberList.length == 0) {
                                            setTimeout(function () {
                                                form.submit();
                                            }, 1000 * RemoveMemberList.length);
                                        }
                                    });
                                var GroupMemberData =
                                    $("#GroupMemberData").val();
                                if (GroupMemberData != "") {
                                    GroupMemberData =
                                        JSON.parse(GroupMemberData);
                                }
                                var newMemberListLength = newMemberList.length;
                                if (
                                    updateGroupMember(
                                        form,
                                        dreamschat_group_id,
                                        GroupMemberData,
                                        newMemberList,
                                        newMemberListLength,
                                        0
                                    )
                                ) {
                                    form.submit();
                                }
                            });
                        } else {
                            var GroupAdminData = "";
                            var currentuser = "";
                            var searchIDs = [];
                            var promises = [];

                            $.ajax({
                                url: BASE_URL + "/get-user-info",
                                type: "GET",
                                data: {
                                    uid: GroupAdminId,
                                },
                                success: function (response) {
                                    GroupAdminData = response.data;
                                    GroupAdminUser = addUser(GroupAdminData);
                                    searchIDs.push(GroupAdminUser);
                                },
                            });

                            for (
                                var gm = 0;
                                gm < GroupMemberList.length;
                                gm++
                            ) {
                                var checkuid = GroupMemberList[gm];
                                var request = $.ajax({
                                    url: BASE_URL + "/get-user-info",
                                    type: "GET",
                                    data: {
                                        uid: checkuid,
                                    },
                                    success: function (response) {
                                        var userData = response.data;
                                        currentuser = addUser(userData);
                                        searchIDs.push(currentuser);
                                    },
                                });
                                promises.push(request);
                            }

                            $.when.apply(null, promises).done(function () {
                                var new_group_title = $("#group_name").val();
                                var searchIDData = searchIDs.filter(function (
                                    elem,
                                    index,
                                    self
                                ) {
                                    return index === self.indexOf(elem);
                                });
                                var new_group_description = "";
                                var Gdata = {
                                    currentuser: GroupAdminUser,
                                    new_group_title: new_group_title,
                                    searchIDData: searchIDData,
                                    new_group_description:
                                        new_group_description,
                                };
                                if (!new_group_title) {
                                } else if (searchIDs == "") {
                                } else {
                                    addGroup(Gdata);
                                }
                                setTimeout(function () {
                                    form.submit();
                                }, 1000);
                            });
                        }
                    });
                } else {
                    var GroupAdminData = "";
                    var currentuser = "";
                    var searchIDs = [];
                    var promises = [];

                    $.ajax({
                        url: BASE_URL + "/get-user-info",
                        type: "GET",
                        data: {
                            uid: GroupAdminId,
                        },
                        success: function (response) {
                            GroupAdminData = response.data;
                            GroupAdminUser = addUser(GroupAdminData);
                            searchIDs.push(GroupAdminUser);
                        },
                    });

                    for (var gm = 0; gm < GroupMemberList.length; gm++) {
                        var checkuid = GroupMemberList[gm];
                        var request = $.ajax({
                            url: BASE_URL + "/get-user-info",
                            type: "GET",
                            data: {
                                uid: checkuid,
                            },
                            success: function (response) {
                                var userData = response.data;
                                currentuser = addUser(userData);
                                searchIDs.push(currentuser);
                            },
                        });
                        promises.push(request);
                    }

                    $.when.apply(null, promises).done(function () {
                        var new_group_title = $("#group_name").val();
                        var searchIDData = searchIDs.filter(function (
                            elem,
                            index,
                            self
                        ) {
                            return index === self.indexOf(elem);
                        });
                        var new_group_description = "";
                        var Gdata = {
                            currentuser: GroupAdminUser,
                            new_group_title: new_group_title,
                            searchIDData: searchIDData,
                            new_group_description: new_group_description,
                        };
                        if (!new_group_title) {
                        } else if (searchIDs == "") {
                        } else {
                            addGroup(Gdata);
                        }
                        setTimeout(function () {
                            form.submit();
                        }, 1000);
                    });
                }
                return false;
            },
        });

        //Add More School In Modal In Exam Page.
        $("#AddMoreSchools").validate({
            ignore: [],
            rules: {
                "school[]": {
                    required: true,
                },
            },
            messages: {
                "school[]": {
                    required: VALIDATIONS.PLEASE_SELECT_SCHOOL,
                },
            },
            errorPlacement: function (error, element) {
                if (element.attr("name") == "school[]") {
                    error.appendTo("#school-error");
                }
            },
        });

        //Edit Learning-tutor Points Validate
        $("#EditIntelligentTutorForm").validate({
            rules: {
                update_document_title: {
                    required: true,
                },
            },
            messages: {
                update_document_title: {
                    required: VALIDATIONS.PLEASE_ENTER_TITLE,
                },
            },
            errorPlacement: function (error, element) {
                error.insertAfter(element);
            },
        });

        // Change Password from validations
        $("#change-password").validate({
            rules: {
                current_password: {
                    required: true,
                },
                new_password: {
                    required: true,
                    minlength: 6,
                },
                new_confirm_password: {
                    required: true,
                    minlength: 6,
                    equalTo: "#new_password",
                },
                submitHandler: function (form) {
                    $("#cover-spin").show();
                    form.submit();
                },
            },
            messages: {
                current_password: {
                    required: VALIDATIONS.PLEASE_ENTER_YOUR_CURRENT_PASSWORD,
                },
                new_password: {
                    required: VALIDATIONS.PLEASE_ENTER_NEW_PASSWORD,
                    minlength: VALIDATIONS.MINIMUM_SIX_CHARACTER_REQUIRED,
                },
                new_confirm_password: {
                    required: VALIDATIONS.PLEASE_ENTER_CONFIRM_PASSWORD,
                    minlength: VALIDATIONS.MINIMUM_SIX_CHARACTER_REQUIRED,
                    equalTo:
                        VALIDATIONS.NEW_PASSWORD_CONFIRM_PASSWORD_NOT_MATCH,
                },
            },
            submitHandler: function (form) {
                $("#cover-spin").show();
                form.submit();
            },
        });

        //Change Password User Form
        $("#changepasswordform").validate({
            rules: {
                newPassword: {
                    required: true,
                    minlength: 6,
                },
                confirmPassword: {
                    required: true,
                    minlength: 6,
                    equalTo: "#newPassword",
                },
            },
            messages: {
                newPassword: {
                    required: VALIDATIONS.PLEASE_ENTER_NEW_PASSWORD,
                    minlength: VALIDATIONS.MINIMUM_SIX_CHARACTER_REQUIRED,
                },
                confirmPassword: {
                    required: VALIDATIONS.PLEASE_ENTER_CONFIRM_PASSWORD,
                    minlength: VALIDATIONS.MINIMUM_SIX_CHARACTER_REQUIRED,
                    equalTo:
                        VALIDATIONS.NEW_PASSWORD_CONFIRM_PASSWORD_NOT_MATCH,
                },
            },
            submitHandler: function (form) {
                $("#cover-spin").show();
                $.ajax({
                    url: BASE_URL + "/change-user-password",
                    type: "POST",
                    data: {
                        _token: $('meta[name="csrf-token"]').attr("content"),
                        userId: $(".changeUserPassword").data("id"),
                        formData: $("#changepasswordform").serialize(),
                    },
                    success: function (response) {
                        $("#cover-spin").hide();
                        var data = JSON.parse(JSON.stringify(response));
                        if (data.status === "success") {
                            $("#cover-spin").hide();
                            closePopupModal("changeUserPwd");
                            $("#changeUserPwd").modal("hide");
                            toastr.success(data.message);
                        } else {
                            toastr.error(data.message);
                        }
                    },
                    error: function (response) {
                        ErrorHandlingMessage(response);
                    },
                });
            },
        });

        /**
         * USE : Form validation and submit to change password for teacher management
         */
        $("#changepasswordUserFrom").validate({
            rules: {
                newPassword: {
                    required: true,
                    minlength: 6,
                },
                confirmPassword: {
                    required: true,
                    minlength: 6,
                    equalTo: "#newPassword",
                },
            },
            messages: {
                newPassword: {
                    required: VALIDATIONS.PLEASE_ENTER_NEW_PASSWORD,
                    minlength: VALIDATIONS.MINIMUM_SIX_CHARACTER_REQUIRED,
                },
                confirmPassword: {
                    required: VALIDATIONS.PLEASE_ENTER_CONFIRM_PASSWORD,
                    minlength: VALIDATIONS.MINIMUM_SIX_CHARACTER_REQUIRED,
                    equalTo:
                        VALIDATIONS.NEW_PASSWORD_CONFIRM_PASSWORD_NOT_MATCH,
                },
            },
            submitHandler: function (form) {
                $("#cover-spin").show();
                $.ajax({
                    url: BASE_URL + "/change-user-password",
                    type: "POST",
                    data: {
                        _token: $('meta[name="csrf-token"]').attr("content"),
                        formData: $("#changepasswordUserFrom").serialize(),
                    },
                    success: function (response) {
                        $("#cover-spin").hide();
                        var data = JSON.parse(JSON.stringify(response));
                        if (data.status === "success") {
                            $("#cover-spin").hide();
                            closePopupModal("changeUserPwd");
                            $("#changeUserPwd").modal("hide");
                            toastr.success(data.message);
                        } else {
                            toastr.error(data.message);
                        }
                    },
                    error: function (response) {
                        ErrorHandlingMessage(response);
                    },
                });
            },
        });

        // Login form validation
        $("#loginform").validate({
            rules: {
                email: {
                    required: true,
                    email: true,
                },
                password: {
                    required: true,
                },
            },
            messages: {
                email: {
                    required: VALIDATIONS.PLEASE_ENTER_EMAIL,
                    email: VALIDATIONS.PLEASE_ENTER_VALID_EMAIL,
                    remote: "Email not register",
                },
                password: {
                    required: VALIDATIONS.PLEASE_ENTER_PASSWORD,
                },
            },
            submitHandler: function (form) {
                $("#cover-spin").show();
                $.ajax({
                    url: BASE_URL + "/loginCheck",
                    type: "POST",
                    data: $("#loginform").serialize(),
                    success: function (response) {
                        $("#cover-spin").hide();
                        var data = JSON.parse(JSON.stringify(response));
                        if (data.status === "success") {
                            toastr.success(data.message);
                            window.location = data.data.redirectUrl;
                        } else {
                            toastr.error(data.message);
                        }
                    },
                    error: function (response) {
                        ErrorHandlingMessage(response);
                    },
                });
            },
        });

        //Add Strands Form Validation
        $("#addStrandsForm").validate({
            rules: {
                name_en: {
                    required: true,
                },
                name_ch: {
                    required: true,
                },
                code: {
                    required: true,
                },
            },
            messages: {
                name_en: {
                    required: VALIDATIONS.PLEASE_ENTER_ENGLISH_NAME,
                },
                name_ch: {
                    required: VALIDATIONS.PLEASE_ENTER_CHINESE_NAME,
                },
                code: {
                    required: VALIDATIONS.PLEASE_ENTER_CODE,
                },
            },
            errorPlacement: function (error, element) {
                if (element.attr("name") == "status") {
                    error.appendTo("#error-status");
                } else {
                    error.insertAfter(element);
                }
            },
        });

        //EDIT Strands Form Validation
        $("#updateStrandsForm").validate({
            rules: {
                name_en: {
                    required: true,
                },
                name_ch: {
                    required: true,
                },
                code: {
                    required: true,
                },
            },
            messages: {
                name_en: {
                    required: VALIDATIONS.PLEASE_ENTER_ENGLISH_NAME,
                },
                name_ch: {
                    required: VALIDATIONS.PLEASE_ENTER_CHINESE_NAME,
                },
                code: {
                    required: VALIDATIONS.PLEASE_ENTER_CODE,
                },
            },
            errorPlacement: function (error, element) {
                if (element.attr("name") == "status") {
                    error.appendTo("#error-status");
                } else {
                    error.insertAfter(element);
                }
            },
        });

        //Add Learning Objectives Form Validation
        $("#addLearningObjectiveForm").validate({
            rules: {
                foci_number: {
                    required: true,
                },
                learning_unit_id: {
                    required: true,
                },
                title_en: {
                    required: true,
                },
                title_ch: {
                    required: true,
                },
                code: {
                    required: true,
                },
            },
            messages: {
                foci_number: {
                    required: VALIDATIONS.PLEASE_ENTER_FOCI_NUMBER,
                },
                learning_unit_id: {
                    required: VALIDATIONS.PLEASE_SELECT_LEARNING_UNIT,
                },
                title_en: {
                    required: VALIDATIONS.PLEASE_ENTER_ENGLISH_NAME,
                },
                title_ch: {
                    required: VALIDATIONS.PLEASE_ENTER_CHINESE_NAME,
                },
                code: {
                    required: VALIDATIONS.PLEASE_ENTER_CODE,
                },
            },
            errorPlacement: function (error, element) {
                if (element.attr("name") == "status") {
                    error.appendTo("#error-status");
                } else {
                    error.insertAfter(element);
                }
            },
        });

        //Update Learning Objectives Form Validation
        $("#updateLearningObjectiveForm").validate({
            rules: {
                foci_number: {
                    required: true,
                },
                learning_unit_id: {
                    required: true,
                },
                title_en: {
                    required: true,
                },
                title_ch: {
                    required: true,
                },
                code: {
                    required: true,
                },
            },
            messages: {
                foci_number: {
                    required: VALIDATIONS.PLEASE_ENTER_FOCI_NUMBER,
                },
                learning_unit_id: {
                    required: VALIDATIONS.PLEASE_SELECT_LEARNING_UNIT,
                },
                title_en: {
                    required: VALIDATIONS.PLEASE_ENTER_ENGLISH_NAME,
                },
                title_ch: {
                    required: VALIDATIONS.PLEASE_ENTER_CHINESE_NAME,
                },
                code: {
                    required: VALIDATIONS.PLEASE_ENTER_CODE,
                },
            },
            errorPlacement: function (error, element) {
                if (element.attr("name") == "status") {
                    error.appendTo("#error-status");
                } else {
                    error.insertAfter(element);
                }
            },
        });

        //Add Ai Calculated Difficulty Form Validation
        $("#addaiCalculatedForm").validate({
            rules: {
                difficultyLevel: {
                    required: true,
                },
                difficult_value: {
                    required: true,
                },
            },
            messages: {
                difficultyLevel: {
                    required: VALIDATIONS.PLEASE_SELECT_DIFFICULTY_LEVEL,
                    number: VALIDATIONS.PLEASE_ENTER_DIGITS_ONLY,
                },
                difficult_value: {
                    required: VALIDATIONS.PLEASE_ENTER_DIFFICULTY_VALUE,
                },
            },
            errorPlacement: function (error, element) {
                if (element.attr("name") == "status") {
                    error.appendTo("#error-status");
                } else {
                    error.insertAfter(element);
                }
            },
        });

        //edit Ai Calculated Difficulty Form Validation
        $("#editaiCalculatedForm").validate({
            rules: {
                difficultyLevel: {
                    required: true,
                },
                difficult_value: {
                    required: true,
                },
            },
            messages: {
                difficultyLevel: {
                    required: VALIDATIONS.PLEASE_SELECT_DIFFICULTY_LEVEL,
                    number: VALIDATIONS.PLEASE_ENTER_DIGITS_ONLY,
                },
                difficult_value: {
                    required: VALIDATIONS.PLEASE_ENTER_DIFFICULTY_VALUE,
                },
            },
            errorPlacement: function (error, element) {
                if (element.attr("name") == "status") {
                    error.appendTo("#error-status");
                } else {
                    error.insertAfter(element);
                }
            },
        });

        //Add pre Configure Difficulty Form Validation
        $("#addpreconfiguredifficultForm").validate({
            rules: {
                difficulty_level: {
                    required: true,
                },
                title: {
                    required: true,
                    number: true,
                },
                difficult_level_name_en: {
                    required: true,
                },
                difficult_level_name_ch: {
                    required: true,
                },
            },
            messages: {
                difficulty_level: {
                    required: VALIDATIONS.PLEASE_SELECT_DIFFICULTY_LEVEL,
                },
                title: {
                    required: VALIDATIONS.PLEASE_ENTER_DIFFICULTY_VALUE,
                    number: VALIDATIONS.PLEASE_ENTER_DIGITS_ONLY,
                },
                difficult_level_name_en: {
                    required: VALIDATIONS.PLEASE_ENTER_NAME,
                },
                difficult_level_name_ch: {
                    required: VALIDATIONS.PLEASE_ENTER_NAME,
                },
            },
            errorPlacement: function (error, element) {
                if (element.attr("name") == "status") {
                    error.appendTo("#error-status");
                } else {
                    error.insertAfter(element);
                }
            },
        });

        //edit pre Configure Difficulty Form Validation
        $("#editpreconfiguredifficultForm").validate({
            rules: {
                difficulty_level: {
                    required: true,
                },
                title: {
                    required: true,
                    number: true,
                },
                difficult_level_name_en: {
                    required: true,
                },
                difficult_level_name_ch: {
                    required: true,
                },
            },
            messages: {
                difficulty_level: {
                    required: VALIDATIONS.PLEASE_SELECT_DIFFICULTY_LEVEL,
                    number: VALIDATIONS.PLEASE_ENTER_DIGITS_ONLY,
                },
                title: {
                    required: VALIDATIONS.PLEASE_ENTER_DIFFICULTY_VALUE,
                    number: VALIDATIONS.PLEASE_ENTER_DIGITS_ONLY,
                },
                difficult_level_name_en: {
                    required: VALIDATIONS.PLEASE_ENTER_NAME,
                },
                difficult_level_name_ch: {
                    required: VALIDATIONS.PLEASE_ENTER_NAME,
                },
            },
            errorPlacement: function (error, element) {
                if (element.attr("name") == "status") {
                    error.appendTo("#error-status");
                } else {
                    error.insertAfter(element);
                }
            },
        });

        //Add Teacher Subject Assign Validation
        $("#addAssignForm").validate({
            ignore: [],
            rules: {
                teacher_id: {
                    required: true,
                },
                class_id: {
                    required: true,
                },
                "class_type[]": {
                    required: true,
                },
            },
            messages: {
                teacher_id: {
                    required: VALIDATIONS.PLEASE_SELECT_TEACHER,
                },
                class_id: {
                    required: VALIDATIONS.PLEASE_SELECT_GRADE,
                },
                "class_type[]": {
                    required: "Please Select Class Type",
                },
            },
            errorPlacement: function (error, element) {
                if (element.attr("name") == "status") {
                    error.appendTo("#error-status");
                } else {
                    error.insertAfter(element);
                }
            },
        });

        // //Edit Teacher Subject Assign
        // $("#editAssignForm").validate({
        //     ignore: [],
        //     rules: {
        //         teacher_id: {
        //             required: true,
        //         },
        //         class_id: {
        //             required: true,
        //         },
        //     },
        //     messages: {
        //         teacher_id: {
        //             required: VALIDATIONS.PLEASE_SELECT_TEACHER,
        //         },
        //         class_id: {
        //             required: VALIDATIONS.PLEASE_SELECT_GRADE,
        //         },
        //     },
        //     errorPlacement: function (error, element) {
        //         if (element.attr("name") == "status") {
        //             error.appendTo("#error-status");
        //         } else {
        //             error.insertAfter(element);
        //         }
        //     },
        // });

        //Add School Form Validation
        $("#addSchoolsForm").validate({
            rules: {
                school_name: {
                    required: true,
                },
                school_code: {
                    required: true,
                },
                school_name_en: {
                    required: true,
                },
                school_name_ch: {
                    required: true,
                },
                email: {
                    required: true,
                    email: true,
                    checkValidEmail: true,
                },
                password: {
                    required: true,
                    minlength: 6,
                },
                confirm_password: {
                    required: true,
                    minlength: 6,
                    equalTo: "#password",
                },
            },
            messages: {
                school_name: {
                    required: VALIDATIONS.PLEASE_ENTER_SCHOOL_NAME,
                },
                school_code: {
                    required: VALIDATIONS.PLEASE_ENTER_SCHOOL_CODE,
                },
                school_name_en: {
                    required: VALIDATIONS.PLEASE_ENTER_ENGLISH_SCHOOL_NAME,
                },
                school_name_ch: {
                    required: VALIDATIONS.PLEASE_ENTER_CHINESE_SCHOOL_NAME,
                },
                email: {
                    required: VALIDATIONS.PLEASE_ENTER_EMAIL,
                    email: VALIDATIONS.PLEASE_ENTER_VALID_EMAIL,
                },
                password: {
                    required: VALIDATIONS.PLEASE_ENTER_PASSWORD,
                    minlength: VALIDATIONS.MINIMUM_SIX_CHARACTER_REQUIRED,
                },
                confirm_password: {
                    required: VALIDATIONS.PLEASE_ENTER_CONFIRM_PASSWORD,
                    minlength: VALIDATIONS.MINIMUM_SIX_CHARACTER_REQUIRED,
                    equalTo:
                        VALIDATIONS.NEW_PASSWORD_CONFIRM_PASSWORD_NOT_MATCH,
                },
            },
            errorPlacement: function (error, element) {
                if (element.attr("name") == "status") {
                    error.appendTo("#error-status");
                } else {
                    error.insertAfter(element);
                }
            },
            submitHandler: function (form) {
                if ($("#addAdmins").is(":checked")) {
                    var err = checkMultipleSubAdminFieldValidate("add");
                    if (err != 0) {
                        return false;
                    }
                }
                $("#cover-spin").show();
                form.submit();
            },
        });

        //Update School Form Validation
        $("#updateSchoolsForm").validate({
            rules: {
                school_name_en: {
                    required: true,
                },
                school_name_ch: {
                    required: true,
                },
                email: {
                    required: true,
                    email: true,
                    checkValidEmail: true,
                },
                password: {
                    required: true,
                },
            },
            messages: {
                school_name_en: {
                    required: VALIDATIONS.PLEASE_ENTER_ENGLISH_SCHOOL_NAME,
                },
                school_name_ch: {
                    required: VALIDATIONS.PLEASE_ENTER_ENGLISH_SCHOOL_NAME,
                },
                email: {
                    required: VALIDATIONS.PLEASE_ENTER_EMAIL,
                    email: VALIDATIONS.PLEASE_ENTER_VALID_EMAIL,
                },
                password: {
                    required: VALIDATIONS.PLEASE_ENTER_PASSWORD,
                },
            },
            errorPlacement: function (error, element) {
                if (element.attr("name") == "status") {
                    error.appendTo("#error-status");
                } else {
                    error.insertAfter(element);
                }
            },
            submitHandler: function (form) {
                var err = checkMultipleSubAdminFieldValidate("edit");
                if (err != 0) {
                    return false;
                }
                $("#cover-spin").show();
                form.submit();
            },
        });

        //Add Node Form Validation
        $("#addNodesForm").validate({
            rules: {
                node_id: {
                    required: true,
                },
                node_title_en: {
                    required: true,
                },
                node_title_ch: {
                    required: true,
                },
                weakness_name_en: {
                    required: true,
                },
                weakness_name_ch: {
                    required: true,
                },
            },
            messages: {
                node_id: {
                    required: VALIDATIONS.PLEASE_ENTER_NODE_ID,
                },
                node_title_en: {
                    required: VALIDATIONS.PLEASE_ENTER_ENGLISH_TITLE,
                },
                node_title_ch: {
                    required: VALIDATIONS.PLEASE_ENTER_CHINESE_TITLE,
                },
                weakness_name_en: {
                    required: VALIDATIONS.PLEASE_ENTER_ENGLISH_WEAKNESS_NAME,
                },
                weakness_name_ch: {
                    required: VALIDATIONS.PLEASE_ENTER_CHINESE_WEAKNESS_NAME,
                },
            },
            errorPlacement: function (error, element) {
                if (element.attr("name") == "status") {
                    error.appendTo("#error-status");
                } else {
                    error.insertAfter(element);
                }
            },
        });

        //Update Node Form Validation
        $("#updateNodesForm").validate({
            rules: {
                node_id: {
                    required: true,
                },
                node_title_en: {
                    required: true,
                },
                node_title_ch: {
                    required: true,
                },
                weakness_name_en: {
                    required: true,
                },
                weakness_name_ch: {
                    required: true,
                },
            },
            messages: {
                node_id: {
                    required: VALIDATIONS.PLEASE_ENTER_NODE_ID,
                },
                node_title_en: {
                    required: VALIDATIONS.PLEASE_ENTER_ENGLISH_TITLE,
                },
                node_title_ch: {
                    required: VALIDATIONS.PLEASE_ENTER_CHINESE_TITLE,
                },
                weakness_name_en: {
                    required: VALIDATIONS.PLEASE_ENTER_ENGLISH_WEAKNESS_NAME,
                },
                weakness_name_ch: {
                    required: VALIDATIONS.PLEASE_ENTER_CHINESE_WEAKNESS_NAME,
                },
            },
            errorPlacement: function (error, element) {
                if (element.attr("name") == "status") {
                    error.appendTo("#error-status");
                } else {
                    error.insertAfter(element);
                }
            },
        });

        //Add Class Form Validation
        $("#addClassForm").validate({
            ignore: [],
            rules: {
                name: {
                    required: true,
                    number: true,
                },
                "class_type[]": {
                    required: true,
                },
            },
            messages: {
                name: {
                    required: VALIDATIONS.PLEASE_SELECT_GRADE,
                    number: VALIDATIONS.PLEASE_ENTER_ONLY_NUMERIC_VALUE,
                },
                "class_type[]": {
                    required: VALIDATIONS.PLEASE_SELECT_CLASS,
                },
            },
            errorPlacement: function (error, element) {
                if (element.attr("name") == "status") {
                    error.appendTo("#error-status");
                } else {
                    error.insertAfter(element);
                }
            },
            submitHandler: function (form) {
                $("#cover-spin").show();
                form.submit();
            },
        });

        //Edit Class Form Validation
        $("#editClassForm").validate({
            ignore: [],
            rules: {
                name: {
                    required: true,
                    number: true,
                },
                "class_type[]": {
                    required: true,
                },
            },
            messages: {
                name: {
                    required: VALIDATIONS.PLEASE_SELECT_GRADE,
                    number: VALIDATIONS.PLEASE_ENTER_ONLY_NUMERIC_VALUE,
                },
                "class_type[]": {
                    required: VALIDATIONS.PLEASE_SELECT_CLASS,
                },
            },
            errorPlacement: function (error, element) {
                if (element.attr("name") == "status") {
                    error.appendTo("#error-status");
                } else {
                    error.insertAfter(element);
                }
            },
            submitHandler: function (form) {
                $("#cover-spin").show();
                form.submit();
            },
        });

        //Add Subject Form Validation
        $("#addSubjectsForm").validate({
            rules: {
                name: {
                    required: true,
                },
                code: {
                    required: true,
                },
            },
            messages: {
                name: {
                    required: VALIDATIONS.PLEASE_ENTER_SUBJECT_NAME,
                },
                code: {
                    required: VALIDATIONS.PLEASE_ENTER_SUBJECT_CODE,
                },
            },
            errorPlacement: function (error, element) {
                if (element.attr("name") == "status") {
                    error.appendTo("#error-status");
                } else {
                    error.insertAfter(element);
                }
            },
            submitHandler: function (form) {
                $("#cover-spin").show();
                form.submit();
            },
        });

        //Edit Class Form Validation
        $("#editSubjectsForm").validate({
            rules: {
                name: {
                    required: true,
                },
                code: {
                    required: true,
                },
            },
            messages: {
                name: {
                    required: VALIDATIONS.PLEASE_ENTER_SUBJECT_NAME,
                },
                code: {
                    required: VALIDATIONS.PLEASE_ENTER_SUBJECT_CODE,
                },
            },
            errorPlacement: function (error, element) {
                if (element.attr("name") == "status") {
                    error.appendTo("#error-status");
                } else {
                    error.insertAfter(element);
                }
            },
            submitHandler: function (form) {
                $("#cover-spin").show();
                form.submit();
            },
        });

        // Add Student Form validation
        $("#addStudentForm").validate({
            rules: {
                grade_id: {
                    required: true,
                },
                class_id: {
                    required: true,
                },
                student_number: {
                    required: true,
                },
                class_number: {
                    required: true,
                },
                name_en: {
                    required: true,
                },
                name_ch: {
                    required: true,
                },
                mobile_no: {
                    number: true,
                    minlength: 8,
                },
                email: {
                    required: true,
                    email: true,
                    checkValidEmail: true,
                },
                permanent_refrence_number: {
                    required: true,
                },
                password: {
                    required: true,
                    minlength: 6,
                },
                confirm_password: {
                    required: true,
                    minlength: 6,
                    equalTo: "#password",
                },
            },
            messages: {
                grade_id: {
                    required: VALIDATIONS.PLEASE_SELECT_GRADE,
                },
                class_id: {
                    required: VALIDATIONS.PLEASE_SELECT_CLASS,
                },
                student_number: {
                    required: VALIDATIONS.PLEASE_ENTER_STUDENT_NUMBER,
                },
                class_number: {
                    required:
                        VALIDATIONS.PLEASE_ENTER_CLASS_NAME_AND_CLASS_NUMBER,
                },
                name_en: {
                    required: VALIDATIONS.PLEASE_ENTER_ENGLISH_NAME,
                },
                name_ch: {
                    required: VALIDATIONS.PLEASE_ENTER_CHINESE_NAME,
                },
                mobile_no: {
                    number: VALIDATIONS.PLEASE_ENTER_ONLY_NUMERIC_VALUE,
                    minlength: VALIDATIONS.PLEASE_ENTER_MINIMUM_EIGHT_DIGIT,
                },
                email: {
                    required: VALIDATIONS.PLEASE_ENTER_EMAIL,
                    email: VALIDATIONS.PLEASE_ENTER_VALID_EMAIL,
                },
                permanent_refrence_number: {
                    required:
                        VALIDATIONS.PLEASE_ENTER_PERMANENT_REFERENCE_NUMBER,
                },
                password: {
                    required: VALIDATIONS.PLEASE_ENTER_PASSWORD,
                    minlength: VALIDATIONS.MINIMUM_SIX_CHARACTER_REQUIRED,
                },
                confirm_password: {
                    required: VALIDATIONS.PLEASE_ENTER_CONFIRM_PASSWORD,
                    minlength: VALIDATIONS.MINIMUM_SIX_CHARACTER_REQUIRED,
                    equalTo:
                        VALIDATIONS.NEW_PASSWORD_CONFIRM_PASSWORD_NOT_MATCH,
                },
            },
            errorPlacement: function (error, element) {
                if (element.attr("name") == "date_of_birth") {
                    error.appendTo("#error-dateof-birth");
                } else if (element.attr("name") == "gender") {
                    error.appendTo(".gender-select-err");
                } else {
                    error.insertAfter(element);
                }
            },
        });

        // Edit User Form validation
        $("#editStudentForm").validate({
            rules: {
                grade_id: {
                    required: true,
                },
                class_id: {
                    required: true,
                },
                student_number: {
                    required: true,
                },
                class_number: {
                    required: true,
                },
                name_en: {
                    required: true,
                },
                name_ch: {
                    required: true,
                },
                mobile_no: {
                    number: true,
                    minlength: 8,
                },
                email: {
                    required: true,
                    email: true,
                    checkValidEmail: true,
                },
                permanent_refrence_number: {
                    required: true,
                },
            },
            messages: {
                grade_id: {
                    required: VALIDATIONS.PLEASE_SELECT_GRADE,
                },
                class_id: {
                    required: VALIDATIONS.PLEASE_SELECT_CLASS,
                },
                student_number: {
                    required: VALIDATIONS.PLEASE_ENTER_STUDENT_NUMBER,
                },
                class_number: {
                    required:
                        VALIDATIONS.PLEASE_ENTER_CLASS_NAME_AND_CLASS_NUMBER,
                },
                name_en: {
                    required: VALIDATIONS.PLEASE_ENTER_ENGLISH_NAME,
                },
                name_ch: {
                    required: VALIDATIONS.PLEASE_ENTER_CHINESE_NAME,
                },
                mobile_no: {
                    number: VALIDATIONS.PLEASE_ENTER_ONLY_NUMERIC_VALUE,
                    minlength: VALIDATIONS.PLEASE_ENTER_MINIMUM_EIGHT_DIGIT,
                },
                email: {
                    required: VALIDATIONS.PLEASE_ENTER_EMAIL,
                    email: VALIDATIONS.PLEASE_ENTER_VALID_EMAIL,
                },
                permanent_refrence_number: {
                    required:
                        VALIDATIONS.PLEASE_ENTER_PERMANENT_REFERENCE_NUMBER,
                },
            },
            errorPlacement: function (error, element) {
                if (element.attr("name") == "date_of_birth") {
                    error.appendTo("#error-dateof-birth");
                } else if (element.attr("name") == "gender") {
                    error.appendTo(".gender-select-err");
                } else {
                    error.insertAfter(element);
                }
            },
        });

        // Add Teacher Management Form validation
        $("#addTeacherForm").validate({
            rules: {
                name_en: {
                    required: true,
                },
                name_ch: {
                    required: true,
                },
                email: {
                    required: true,
                    email: true,
                    checkValidEmail: true,
                },
                mobile_no: {
                    number: true,
                    minlength: 8,
                },
                password: {
                    required: true,
                    minlength: 6,
                },
                confirm_password: {
                    required: true,
                    minlength: 6,
                    equalTo: "#password",
                },
            },
            messages: {
                name_en: {
                    required: VALIDATIONS.PLEASE_ENTER_ENGLISH_NAME,
                },
                name_ch: {
                    required: VALIDATIONS.PLEASE_ENTER_CHINESE_NAME,
                },
                email: {
                    required: VALIDATIONS.PLEASE_ENTER_EMAIL,
                    email: VALIDATIONS.PLEASE_ENTER_VALID_EMAIL,
                },
                mobile_no: {
                    number: VALIDATIONS.PLEASE_ENTER_ONLY_NUMERIC_VALUE,
                    minlength: VALIDATIONS.PLEASE_ENTER_MINIMUM_EIGHT_DIGIT,
                },
                password: {
                    required: VALIDATIONS.PLEASE_ENTER_PASSWORD,
                    minlength: VALIDATIONS.MINIMUM_SIX_CHARACTER_REQUIRED,
                },
                confirm_password: {
                    required: VALIDATIONS.PLEASE_ENTER_CONFIRM_PASSWORD,
                    minlength: VALIDATIONS.MINIMUM_SIX_CHARACTER_REQUIRED,
                    equalTo:
                        VALIDATIONS.NEW_PASSWORD_CONFIRM_PASSWORD_NOT_MATCH,
                },
            },
            errorPlacement: function (error, element) {
                if (element.attr("name") == "date_of_birth") {
                    error.appendTo("#error-dateof-birth");
                } else if (element.attr("name") == "gender") {
                    error.appendTo(".gender-select-err");
                } else {
                    error.insertAfter(element);
                }
            },
        });

        // Edit Teacher Maangement Form validation
        $("#editTeacherForm").validate({
            rules: {
                name_en: {
                    required: true,
                },
                name_ch: {
                    required: true,
                },
                email: {
                    required: true,
                    email: true,
                    checkValidEmail: true,
                },
                mobile_no: {
                    number: true,
                    minlength: 8,
                },
            },
            messages: {
                name_en: {
                    required: VALIDATIONS.PLEASE_ENTER_ENGLISH_NAME,
                },
                name_ch: {
                    required: VALIDATIONS.PLEASE_ENTER_CHINESE_NAME,
                },
                mobile_no: {
                    number: VALIDATIONS.PLEASE_ENTER_ONLY_NUMERIC_VALUE,
                    minlength: VALIDATIONS.PLEASE_ENTER_MINIMUM_EIGHT_DIGIT,
                },
                email: {
                    required: VALIDATIONS.PLEASE_ENTER_EMAIL,
                    email: VALIDATIONS.PLEASE_ENTER_VALID_EMAIL,
                },
            },
            errorPlacement: function (error, element) {
                if (element.attr("name") == "date_of_birth") {
                    error.appendTo("#error-dateof-birth");
                } else if (element.attr("name") == "gender") {
                    error.appendTo(".gender-select-err");
                } else {
                    error.insertAfter(element);
                }
            },
        });

        // Add Principal Management Form validation
        $("#addPrincipalForm").validate({
            rules: {
                name_en: {
                    required: true,
                },
                name_ch: {
                    required: true,
                },
                email: {
                    required: true,
                    email: true,
                    checkValidEmail: true,
                },
                mobile_no: {
                    number: true,
                    minlength: 8,
                },
                password: {
                    required: true,
                    minlength: 6,
                },
                confirm_password: {
                    required: true,
                    minlength: 6,
                    equalTo: "#password",
                },
            },
            messages: {
                name_en: {
                    required: VALIDATIONS.PLEASE_ENTER_ENGLISH_NAME,
                },
                name_ch: {
                    required: VALIDATIONS.PLEASE_ENTER_CHINESE_NAME,
                },
                email: {
                    required: VALIDATIONS.PLEASE_ENTER_EMAIL,
                    email: VALIDATIONS.PLEASE_ENTER_VALID_EMAIL,
                },
                mobile_no: {
                    number: VALIDATIONS.PLEASE_ENTER_ONLY_NUMERIC_VALUE,
                    minlength: VALIDATIONS.PLEASE_ENTER_MINIMUM_EIGHT_DIGIT,
                },
                password: {
                    required: VALIDATIONS.PLEASE_ENTER_PASSWORD,
                    minlength: VALIDATIONS.MINIMUM_SIX_CHARACTER_REQUIRED,
                },
                confirm_password: {
                    required: VALIDATIONS.PLEASE_ENTER_CONFIRM_PASSWORD,
                    minlength: VALIDATIONS.MINIMUM_SIX_CHARACTER_REQUIRED,
                    equalTo:
                        VALIDATIONS.NEW_PASSWORD_CONFIRM_PASSWORD_NOT_MATCH,
                },
            },
            errorPlacement: function (error, element) {
                if (element.attr("name") == "date_of_birth") {
                    error.appendTo("#error-dateof-birth");
                } else if (element.attr("name") == "gender") {
                    error.appendTo(".gender-select-err");
                } else {
                    error.insertAfter(element);
                }
            },
        });

        // Edit Principal Management Form validation
        $("#editPrincipalForm").validate({
            rules: {
                name_en: {
                    required: true,
                },
                name_ch: {
                    required: true,
                },
                email: {
                    required: true,
                    email: true,
                    checkValidEmail: true,
                },
                mobile_no: {
                    number: true,
                    minlength: 8,
                },
            },
            messages: {
                name_en: {
                    required: VALIDATIONS.PLEASE_ENTER_ENGLISH_NAME,
                },
                name_ch: {
                    required: VALIDATIONS.PLEASE_ENTER_CHINESE_NAME,
                },
                mobile_no: {
                    number: VALIDATIONS.PLEASE_ENTER_ONLY_NUMERIC_VALUE,
                    minlength: VALIDATIONS.PLEASE_ENTER_MINIMUM_EIGHT_DIGIT,
                },
                email: {
                    required: VALIDATIONS.PLEASE_ENTER_EMAIL,
                    email: VALIDATIONS.PLEASE_ENTER_VALID_EMAIL,
                },
            },
            errorPlacement: function (error, element) {
                if (element.attr("name") == "date_of_birth") {
                    error.appendTo("#error-dateof-birth");
                } else if (element.attr("name") == "gender") {
                    error.appendTo(".gender-select-err");
                } else {
                    error.insertAfter(element);
                }
            },
        });

        /** USE : Import User form validation */
        $("#importUsers").validate({
            rules: {
                role: {
                    required: true,
                },
                user_file: {
                    required: true,
                    extension: "csv",
                },
            },
            messages: {
                role: {
                    required: VALIDATIONS.PLEASE_SELECT_IMPORT_USER_ROLE,
                },
                user_file: {
                    required: VALIDATIONS.PLEASE_UPLOAD_CSV_FILE,
                    extension: VALIDATIONS.INVALID_FILE_EXTENSION,
                },
            },
            submitHandler: function (form) {
                $("#cover-spin").show();
                form.submit();
            },
        });

        //For import student
        $("#importStudents").validate({
            rules: {
                mode: {
                    required: true,
                },
                curriculum: {
                    required: true,
                },
                user_file: {
                    required: true,
                    extension: "csv",
                },
            },
            messages: {
                user_file: {
                    required: VALIDATIONS.PLEASE_UPLOAD_CSV_FILE,
                    extension: VALIDATIONS.INVALID_FILE_EXTENSION,
                },
            },
            submitHandler: function (form) {
                $("#cover-spin").show();
                form.submit();
            },
        });

        // Add Sub-admin Form validation
        $("#addSubAdminForm").validate({
            rules: {
                name_en: {
                    required: true,
                },
                name_ch: {
                    required: true,
                },
                email: {
                    required: true,
                    email: true,
                    checkValidEmail: true,
                },
                password: {
                    required: true,
                    minlength: 6,
                },
                confirm_password: {
                    required: true,
                    minlength: 6,
                    equalTo: "#password",
                },
            },
            messages: {
                name_en: {
                    required: VALIDATIONS.PLEASE_ENTER_ENGLISH_NAME,
                },
                name_ch: {
                    required: VALIDATIONS.PLEASE_ENTER_CHINESE_NAME,
                },
                email: {
                    required: VALIDATIONS.PLEASE_ENTER_EMAIL,
                    email: VALIDATIONS.PLEASE_ENTER_VALID_EMAIL,
                },
                password: {
                    required: VALIDATIONS.PLEASE_ENTER_PASSWORD,
                    minlength: VALIDATIONS.MINIMUM_SIX_CHARACTER_REQUIRED,
                },
                confirm_password: {
                    required: VALIDATIONS.PLEASE_ENTER_CONFIRM_PASSWORD,
                    minlength: VALIDATIONS.MINIMUM_SIX_CHARACTER_REQUIRED,
                    equalTo:
                        VALIDATIONS.NEW_PASSWORD_CONFIRM_PASSWORD_NOT_MATCH,
                },
            },
            errorPlacement: function (error, element) {
                if (element.attr("name") == "date_of_birth") {
                    error.appendTo("#error-dateof-birth");
                } else {
                    error.insertAfter(element);
                }
            },
        });

        // Edit Sub-admin Form validation
        $("#editSubAdminForm").validate({
            rules: {
                name_en: {
                    required: true,
                },
                name_ch: {
                    required: true,
                },
                email: {
                    required: true,
                    email: true,
                    checkValidEmail: true,
                },
            },
            messages: {
                name_en: {
                    required: VALIDATIONS.PLEASE_ENTER_ENGLISH_NAME,
                },
                name_ch: {
                    required: VALIDATIONS.PLEASE_ENTER_CHINESE_NAME,
                },
                email: {
                    required: VALIDATIONS.PLEASE_ENTER_EMAIL,
                    email: VALIDATIONS.PLEASE_ENTER_VALID_EMAIL,
                },
            },
            errorPlacement: function (error, element) {
                if (element.attr("name") == "date_of_birth") {
                    error.appendTo("#error-dateof-birth");
                } else {
                    error.insertAfter(element);
                }
            },
        });

        // Add User Form validation
        $("#addUsersForm").validate({
            rules: {
                school: {
                    required: true,
                },
                role: {
                    required: true,
                },
                grade_id: {
                    required: true,
                },
                section: {
                    required: true,
                },
                name_en: {
                    required: true,
                },
                name_ch: {
                    required: true,
                },
                email: {
                    required: true,
                    email: true,
                    checkValidEmail: true,
                },
                mobile_no: {
                    number: true,
                    minlength: 8,
                },
                password: {
                    required: true,
                    minlength: 6,
                },
                confirm_password: {
                    required: true,
                    minlength: 6,
                    equalTo: "#password",
                },
            },
            messages: {
                school: {
                    required: VALIDATIONS.PLEASE_SELECT_SCHOOL,
                },
                role: {
                    required: VALIDATIONS.SELECT_ROLE,
                },
                grade_id: {
                    required: VALIDATIONS.PLEASE_SELECT_GRADE,
                },
                section: {
                    required: VALIDATIONS.PLEASE_SELECT_SECTION,
                },
                name_en: {
                    required: VALIDATIONS.PLEASE_ENTER_ENGLISH_NAME,
                },
                name_ch: {
                    required: VALIDATIONS.PLEASE_ENTER_CHINESE_NAME,
                },
                email: {
                    required: VALIDATIONS.PLEASE_ENTER_EMAIL,
                    email: VALIDATIONS.PLEASE_ENTER_VALID_EMAIL,
                },
                mobile_no: {
                    number: VALIDATIONS.PLEASE_ENTER_VALID_MOBILE_NO,
                    minlength: VALIDATIONS.PLEASE_ENTER_MINIMUM_EIGHT_DIGIT,
                },
                password: {
                    required: VALIDATIONS.PLEASE_ENTER_PASSWORD,
                },
                confirm_password: {
                    required: VALIDATIONS.PLEASE_ENTER_CONFIRM_PASSWORD,
                    minlength: VALIDATIONS.MINIMUM_SIX_CHARACTER_REQUIRED,
                    equalTo:
                        VALIDATIONS.NEW_PASSWORD_CONFIRM_PASSWORD_NOT_MATCH,
                },
            },
            errorPlacement: function (error, element) {
                if (element.attr("name") == "date_of_birth") {
                    error.appendTo("#error-dateof-birth");
                } else if (element.attr("name") == "role") {
                    error.appendTo("#error-role");
                } else if (element.attr("name") == "school") {
                    error.appendTo("#error-school");
                } else if (element.attr("name") == "grade_id") {
                    error.appendTo("#error-grade");
                } else {
                    error.insertAfter(element);
                }
            },
        });

        // Edit User Form validation
        $("#editUsersForm").validate({
            rules: {
                school: {
                    required: true,
                },
                role: {
                    required: true,
                },
                grade_id: {
                    required: true,
                },
                section: {
                    required: true,
                },
                name_en: {
                    required: true,
                },
                name_ch: {
                    required: true,
                },
                email: {
                    required: true,
                    email: true,
                    checkValidEmail: true,
                },
                mobile_no: {
                    number: true,
                    minlength: 8,
                },
            },
            messages: {
                school: {
                    required: VALIDATIONS.PLEASE_SELECT_SCHOOL,
                },
                role: {
                    required: VALIDATIONS.SELECT_ROLE,
                },
                grade_id: {
                    required: VALIDATIONS.PLEASE_SELECT_GRADE,
                },
                section: {
                    required: VALIDATIONS.PLEASE_SELECT_SECTION,
                },
                name_en: {
                    required: VALIDATIONS.PLEASE_ENTER_ENGLISH_NAME,
                },
                name_ch: {
                    required: VALIDATIONS.PLEASE_ENTER_CHINESE_NAME,
                },
                email: {
                    required: VALIDATIONS.PLEASE_ENTER_EMAIL,
                    email: VALIDATIONS.PLEASE_ENTER_VALID_EMAIL,
                },
                mobile_no: {
                    number: VALIDATIONS.PLEASE_ENTER_VALID_MOBILE_NO,
                    minlength: VALIDATIONS.PLEASE_ENTER_MINIMUM_EIGHT_DIGIT,
                },
            },
            errorPlacement: function (error, element) {
                if (element.attr("name") == "date_of_birth") {
                    error.appendTo("#error-dateof-birth");
                } else if (element.attr("name") == "role") {
                    error.appendTo("#error-role");
                } else if (element.attr("name") == "school") {
                    error.appendTo("#error-school");
                } else if (element.attr("name") == "grade_id") {
                    error.appendTo("#error-grade");
                } else {
                    error.insertAfter(element);
                }
            },
        });

        // Add Question form
        $("#addQuestionFrom").validate({
            ignore: [],
            debug: false,
            rules: {
                // grade_id: {
                //     required: true,
                // },
                // subject_id: {
                //     required: true,
                // },
                // strand_id:{
                //     required: true,
                // },
                // learning_unit_id:{
                //     required: true,
                // },
                // learning_objective_id:{
                //     required:true,
                // },
                question_en: {
                    required: function () {
                        CKEDITOR.instances.question_en.updateElement();
                    },
                },
                question_ch: {
                    required: function () {
                        CKEDITOR.instances.question_ch.updateElement();
                    },
                },
                correct_answer_en: {
                    required: function () {
                        CKEDITOR.instances.correct_answer_en.updateElement();
                    },
                },
                correct_answer_ch: {
                    required: function () {
                        CKEDITOR.instances.correct_answer_ch.updateElement();
                    },
                },
                answer1_en: {
                    required: function () {
                        CKEDITOR.instances.answer1_en.updateElement();
                    },
                },
                answer2_en: {
                    required: function () {
                        CKEDITOR.instances.answer2_en.updateElement();
                    },
                },
                // answer3_en: {
                //     required: function()
                //     {
                //      CKEDITOR.instances.answer3_en.updateElement();
                //     }
                // },
                // answer4_en: {
                //     required: function()
                //     {
                //      CKEDITOR.instances.answer4_en.updateElement();
                //     }
                // },
                answer1_ch: {
                    required: function () {
                        CKEDITOR.instances.answer1_ch.updateElement();
                    },
                },
                answer2_ch: {
                    required: function () {
                        CKEDITOR.instances.answer2_ch.updateElement();
                    },
                },
                // answer3_ch: {
                //     required: function()
                //     {
                //      CKEDITOR.instances.answer3_ch.updateElement();
                //     }
                // },
                // answer4_ch: {
                //     required: function()
                //     {
                //      CKEDITOR.instances.answer4_ch.updateElement();
                //     }
                // },
                // question_type: {
                //     required: true,
                // },
                // dificulaty_level: {
                //     required: true,
                // },
                naming_structure_code: {
                    required: true,
                },
            },
            messages: {
                // grade_id: {
                //     required: "Please select Grade.",
                // },
                // subject_id: {
                //     required: "Please select Subject.",
                // },
                // strand_id:{
                //     required: "Please select Strand.",
                // },
                // learning_unit_id:{
                //     required: "Please select Learning Unit.",
                // },
                // learning_objective_id:{
                //     required:"Please select Learning Objective.",
                // },
                question_en: {
                    required: VALIDATIONS.PLEASE_ENTER_QUESTION_OF_ENGLISH,
                },
                question_ch: {
                    required: VALIDATIONS.PLEASE_ENTER_QUESTION_OF_CHINESE,
                },
                correct_answer_en: {
                    required: VALIDATIONS.PLEASE_SELECT_ENGLISH_ANSWER,
                },
                correct_answer_ch: {
                    required: VALIDATIONS.PLEASE_SELECT_CHINESE_ANSWER,
                },
                answer1_en: {
                    required: VALIDATIONS.PLEASE_ENTER_FIRST_ANSWER_OF_ENGLISH,
                },
                answer2_en: {
                    required: VALIDATIONS.PLEASE_ENTER_SECOND_ANSWER_OF_ENGLISH,
                },
                // answer3_en: {
                //     required:  "Please Enter Third Answer of English.",
                // },
                // answer4_en: {
                //     required:  "Please Enter Fourth Answer of English.",
                // },
                answer1_ch: {
                    required: VALIDATIONS.PLEASE_ENTER_FIRST_ANSWER_OF_CHINESE,
                },
                answer2_ch: {
                    required: VALIDATIONS.PLEASE_ENTER_SECOND_ANSWER_OF_CHINESE,
                },
                // answer3_ch: {
                //     required: "Please Enter Third Answer of Chinese.",
                // },
                // answer4_ch: {
                //     required: "Please Enter Fourth Answer of Chinese.",
                // },
                // question_type: {
                //     required: "Please Select Question Type.",
                // },
                // dificulaty_level: {
                //     required: "Please Enter Difficulty Level.",
                // },
                naming_structure_code: {
                    required: VALIDATIONS.PLEASE_ENTER_NAMING_STRUCTURE_CODE,
                },
            },
            errorPlacement: function (error, element) {
                if (element.attr("name") == "dificulaty_level") {
                    error.appendTo("#dificulty-error");
                } else {
                    error.insertAfter(element);
                }
            },
        });

        // edit Question form
        $("#editQuestionFrom").validate({
            ignore: [],
            debug: false,
            rules: {
                // grade_id: {
                //     required: true,
                // },
                // subject_id: {
                //     required: true,
                // },
                // strand_id:{
                //     required: true,
                // },
                // learning_unit_id:{
                //     required: true,
                // },
                // learning_objective_id:{
                //     required:true,
                // },
                question_en: {
                    required: function () {
                        CKEDITOR.instances.question_en.updateElement();
                    },
                },
                question_ch: {
                    required: function () {
                        CKEDITOR.instances.question_ch.updateElement();
                    },
                },
                correct_answer_en: {
                    required: function () {
                        CKEDITOR.instances.correct_answer_en.updateElement();
                    },
                },
                correct_answer_ch: {
                    required: function () {
                        CKEDITOR.instances.correct_answer_ch.updateElement();
                    },
                },
                answer1_en: {
                    required: function () {
                        CKEDITOR.instances.answer1_en.updateElement();
                    },
                },
                answer2_en: {
                    required: function () {
                        CKEDITOR.instances.answer2_en.updateElement();
                    },
                },
                // answer3_en: {
                //     required: function(){
                //      CKEDITOR.instances.answer3_en.updateElement();
                //     }
                // },
                // answer4_en: {
                //     required: function()
                //     {
                //      CKEDITOR.instances.answer4_en.updateElement();
                //     }
                // },
                answer1_ch: {
                    required: function () {
                        CKEDITOR.instances.answer1_ch.updateElement();
                    },
                },
                answer2_ch: {
                    required: function () {
                        CKEDITOR.instances.answer2_ch.updateElement();
                    },
                },
                // answer3_ch: {
                //     required: function()
                //     {
                //      CKEDITOR.instances.answer3_ch.updateElement();
                //     }
                // },
                // answer4_ch: {
                //     required: function()
                //     {
                //      CKEDITOR.instances.answer4_ch.updateElement();
                //     }
                // },
                // question_type: {
                //     required: true,
                // },
                // dificulaty_level: {
                //     required: true,
                // },
                naming_structure_code: {
                    required: true,
                },
            },
            messages: {
                // grade_id: {
                //     required: "Please select Grade.",
                // },
                // subject_id: {
                //     required: "Please select Subject.",
                // },
                // strand_id:{
                //     required: "Please select Strand.",
                // },
                // learning_unit_id:{
                //     required: "Please select Learning Unit.",
                // },
                // learning_objective_id:{
                //     required:"Please select Learning Objective.",
                // },
                question_en: {
                    required: VALIDATIONS.PLEASE_ENTER_QUESTION_OF_ENGLISH,
                },
                question_ch: {
                    required: VALIDATIONS.PLEASE_ENTER_QUESTION_OF_CHINESE,
                },
                correct_answer_en: {
                    required: VALIDATIONS.PLEASE_SELECT_ENGLISH_ANSWER,
                },
                correct_answer_ch: {
                    required: VALIDATIONS.PLEASE_SELECT_CHINESE_ANSWER,
                },
                answer1_en: {
                    required: VALIDATIONS.PLEASE_ENTER_FIRST_ANSWER_OF_ENGLISH,
                },
                answer2_en: {
                    required: VALIDATIONS.PLEASE_ENTER_SECOND_ANSWER_OF_ENGLISH,
                },
                // answer3_en: {
                //     required:  "Please Enter Third Answer of English.",
                // },
                // answer4_en: {
                //     required:  "Please Enter Fourth Answer of English.",
                // },
                answer1_ch: {
                    required: VALIDATIONS.PLEASE_ENTER_FIRST_ANSWER_OF_CHINESE,
                },
                answer2_ch: {
                    required: VALIDATIONS.PLEASE_ENTER_SECOND_ANSWER_OF_CHINESE,
                },
                // answer3_ch: {
                //     required: "Please Enter Third Answer of Chinese.",
                // },
                // answer4_ch: {
                //     required: "Please Enter Fourth Answer of Chinese.",
                // },
                // question_type: {
                //     required: "Please Select Question Type.",
                // },
                // dificulaty_level: {
                //     required: "Please Enter Difficulty Level.",
                // },
                naming_structure_code: {
                    required: VALIDATIONS.PLEASE_ENTER_NAMING_STRUCTURE_CODE,
                },
            },
            errorPlacement: function (error, element) {
                if (element.attr("name") == "dificulaty_level") {
                    error.appendTo("#dificulty-error");
                } else {
                    error.insertAfter(element);
                }
            },
        });

        //Add Role Form Validation
        $("#addRolesForm").validate({
            rules: {
                role_name: {
                    required: true,
                },
            },
            messages: {
                role_name: {
                    required: VALIDATIONS.PLEASE_ENTER_ROLE_NAME,
                },
            },
            errorPlacement: function (error, element) {
                if (element.attr("name") == "status") {
                    error.appendTo("#error-status");
                } else {
                    error.insertAfter(element);
                }
            },
        });

        //Edit Role Form Validation
        $("#editRolesForm").validate({
            rules: {
                role_name: {
                    required: true,
                },
            },
            messages: {
                role_name: {
                    required: VALIDATIONS.PLEASE_ENTER_ROLE_NAME,
                },
            },
            errorPlacement: function (error, element) {
                if (element.attr("name") == "status") {
                    error.appendTo("#error-status");
                } else {
                    error.insertAfter(element);
                }
            },
        });

        //Add Module Form Validation
        $("#addModulesForm").validate({
            rules: {
                module_name: {
                    required: true,
                },
            },
            messages: {
                module_name: {
                    required: VALIDATIONS.PLEASE_ENTER_MODULE_NAME,
                },
            },
            errorPlacement: function (error, element) {
                if (element.attr("name") == "status") {
                    error.appendTo("#error-status");
                } else {
                    error.insertAfter(element);
                }
            },
        });

        //Edit Module Form Validation
        $("#editModulesForm").validate({
            rules: {
                module_name: {
                    required: true,
                },
            },
            messages: {
                module_name: {
                    required: VALIDATIONS.PLEASE_ENTER_MODULE_NAME,
                },
            },
            errorPlacement: function (error, element) {
                if (element.attr("name") == "status") {
                    error.appendTo("#error-status");
                } else {
                    error.insertAfter(element);
                }
            },
        });

        //  Document Validation
        $("#addDocumentFrom").validate({
            ignore: [],
            rules: {
                "node_id[]": {
                    required: true,
                },
                FileName: {
                    required: true,
                },
                "upload[]": {
                    extension: file_type,
                },
            },
            messages: {
                "node_id[]": {
                    required: VALIDATIONS.PLEASE_SELECT_NODE,
                },
                FileName: {
                    required: VALIDATIONS.PLEASE_ENTER_FILE_NAME,
                },
            },
            errorPlacement: function (error, element) {
                if (element.attr("name") == "status") {
                    error.appendTo("#error-status");
                } else if (element.attr("name") == "node_id[]") {
                    error.appendTo(error.insertAfter(".ms-options-wrap"));
                } else {
                    error.insertAfter(element);
                }
            },
            submitHandler: function (form) {
                $(".uploadfiles,.uploadUrl").hide();
                var submitFrom = 0;
                if (
                    $(form).find("input[name='upload[]']").val() == "" &&
                    $(form).find("input[name='document_urls[]']").length != 0
                ) {
                    $(form)
                        .find("input[name='document_urls[]']")
                        .each(function () {
                            if ($(this).val() == "") {
                                submitFrom = 1;
                                $(this)
                                    .parent()
                                    .parent()
                                    .find(".uploadUrl")
                                    .show();
                            } else {
                                $(this)
                                    .parent()
                                    .parent()
                                    .find(".uploadUrl")
                                    .hide();
                            }
                        });
                } else if (
                    $(form).find("input[name='upload[]']").val() == "" &&
                    $(form).find("input[name='document_urls[]']").length == 0
                ) {
                    submitFrom = 1;
                    $(".uploadfiles").show();
                }
                if (submitFrom == 1) {
                } else {
                    form.submit();
                }
            },
        });

        //Intelligent-Tutor Validation
        $("#addIntelligentTutorFrom").validate({
            ignore: [],
            rules: {
                document_title: {
                    required: true,
                },
                "learning_tutor_grade_id[]": {
                    required: true,
                },
                "learning_tutor_strand_id[]": {
                    required: true,
                },
                "learning_tutor_learning_unit[]": {
                    required: true,
                },
                "learning_tutor_learning_objectives[]": {
                    required: true,
                },
                FileName: {
                    required: true,
                },
                //checkValidUrl: true,
                // "upload[]": {
                //     extension: file_type,
                // },
            },
            messages: {
                document_title: {
                    required: VALIDATIONS.PLEASE_ENTER_TITLE,
                },
                "learning_tutor_grade_id[]": {
                    required: VALIDATIONS.PLEASE_SELECT_GRADE,
                },
                "learning_tutor_strand_id[]": {
                    required: VALIDATIONS.PLEASE_SELECT_STRAND,
                },
                "learning_tutor_learning_unit[]": {
                    required: VALIDATIONS.PLEASE_SELECT_LEARNING_UNIT,
                },
                "learning_tutor_learning_objectives[]": {
                    required: VALIDATIONS.PLEASE_SELECT_LEARNING_OBJECTIVES,
                },
                FileName: {
                    required: VALIDATIONS.PLEASE_ENTER_FILE_NAME,
                },
                // "upload[]": {
                //     extension: "Please Enter Valid File",
                // },
            },
            errorPlacement: function (error, element) {
                if (element.attr("name") == "status") {
                    error.appendTo("#error-status");
                } else {
                    error.insertAfter(element);
                }
            },
            submitHandler: function (form) {
                $(".uploadfiles,.uploadUrl").hide();
                var submitFrom = 0;
                if (
                    $(form).find("input[name='upload[]']").val() == "" &&
                    $(form).find("input[name='document_urls[]']").length != 0
                ) {
                    $(form)
                        .find("input[name='document_urls[]']")
                        .each(function () {
                            if ($(this).val() == "") {
                                submitFrom = 1;
                                $(this)
                                    .parent()
                                    .parent()
                                    .find(".uploadUrl")
                                    .show();
                            } else {
                                $(this)
                                    .parent()
                                    .parent()
                                    .find(".uploadUrl")
                                    .hide();
                            }
                        });
                } else if (
                    $(form).find("input[name='upload[]']").val() == "" &&
                    $(form).find("input[name='document_urls[]']").length == 0
                ) {
                    submitFrom = 1;
                    $(".uploadfiles").show();
                }
                if (submitFrom == 1) {
                } else {
                    form.submit();
                }
            },
        });

        //Add change profile Form Validation
        $("#updateProfileForm").validate({
            rules: {
                name_en: {
                    required: true,
                },
                name_ch: {
                    required: true,
                },
                mobile_no: {
                    number: true,
                    minlength: 8,
                },
                profile_photo: {
                    extension: "png|jpe?g|gif",
                },
            },
            messages: {
                name_en: {
                    required: VALIDATIONS.PLEASE_ENTER_ENGLISH_NAME,
                },
                name_ch: {
                    required: VALIDATIONS.PLEASE_ENTER_CHINESE_NAME,
                },
                mobile_no: {
                    number: VALIDATIONS.PLEASE_ENTER_ONLY_NUMERIC_VALUE,
                    minlength: VALIDATIONS.PLEASE_ENTER_MINIMUM_EIGHT_DIGIT,
                },
                profile_photo: {
                    extension:
                        VALIDATIONS.PLEASE_UPLOAD_ONLY_JPEG_JPG_OR_PNG_FILES,
                },
            },
            errorPlacement: function (error, element) {
                if (element.attr("name") == "status") {
                    error.appendTo("#error-status");
                } else {
                    error.insertAfter(element);
                }
            },
        });

        //update teacher profile validation
        $("#updateTeacherProfileForm").validate({
            rules: {
                name_en: {
                    required: true,
                },
                name_ch: {
                    required: true,
                },
                mobile_no: {
                    number: true,
                    minlength: 8,
                },
                profile_photo: {
                    extension: "png|jpe?g|gif",
                },
            },
            messages: {
                name_en: {
                    required: VALIDATIONS.PLEASE_ENTER_ENGLISH_NAME,
                },
                name_ch: {
                    required: VALIDATIONS.PLEASE_ENTER_CHINESE_NAME,
                },
                mobile_no: {
                    number: VALIDATIONS.PLEASE_ENTER_ONLY_NUMERIC_VALUE,
                    minlength: VALIDATIONS.PLEASE_ENTER_MINIMUM_EIGHT_DIGIT,
                },
                profile_photo: {
                    extension:
                        VALIDATIONS.PLEASE_UPLOAD_ONLY_JPEG_JPG_OR_PNG_FILES,
                },
            },
            errorPlacement: function (error, element) {
                if (element.attr("name") == "status") {
                    error.appendTo("#error-status");
                } else {
                    error.insertAfter(element);
                }
            },
        });

        //Add Filteration on grade from teacher panel->myclass  Form Validation
        $("#displayStudentProfileFilterForm").validate({
            rules: {
                grade: {
                    required: true,
                },
            },
            messages: {
                grade: {
                    required: VALIDATIONS.PLEASE_SELECT_GRADE,
                },
            },
            errorPlacement: function (error, element) {
                error.insertAfter(element);
            },
        });

        //Add Filteration on grade from teacher panel-> My Teaching  Form Validation
        $("#displayStudentStudyForm").validate({
            rules: {
                "grade[]": {
                    required: true,
                },
                "class_type_id[]": {
                    required: true,
                },
            },
            messages: {
                grade: {
                    required: VALIDATIONS.PLEASE_SELECT_GRADE,
                },
                class_type_id: {
                    required: VALIDATIONS.PLEASE_SELECT_CLASS,
                },
            },
            errorPlacement: function (error, element) {
                error.insertAfter(element);
            },
        });

        //Add Filteration on Learning Type from student panel->my Learning  Form Validation
        $("#displayStudentActivityForm").validate({
            rules: {
                learning_type: {
                    required: true,
                },
            },
            messages: {
                learning_type: {
                    required: VALIDATIONS.PLEASE_SELECT_LEARNING_TYPE,
                },
            },
            errorPlacement: function (error, element) {
                error.insertAfter(element);
            },
        });

        /**
         * USE : Add Learning Units Form Validation
         * */
        $("#addLearningUnitsForm").validate({
            rules: {
                name: {
                    required: true,
                },
                code: {
                    required: true,
                },
                name_en: {
                    required: true,
                },
                name_ch: {
                    required: true,
                },
                strand_id: {
                    required: true,
                },
            },
            messages: {
                name: {
                    required: VALIDATIONS.PLEASE_ENTER_NAME,
                },
                code: {
                    required: VALIDATIONS.PLEASE_ENTER_CODE,
                },
                name_en: {
                    required: VALIDATIONS.PLEASE_ENTER_ENGLISH_NAME,
                },
                name_ch: {
                    required: VALIDATIONS.PLEASE_ENTER_CHINESE_NAME,
                },
                strand_id: {
                    required: VALIDATIONS.PLEASE_SELECT_STRAND,
                },
            },
            errorPlacement: function (error, element) {
                if (element.attr("name") == "status") {
                    error.appendTo("#error-status");
                } else {
                    error.insertAfter(element);
                }
            },
        });

        /**
         * USE : Edit Learning Units Form Validation
         * */
        $("#editLearningUnitsForm").validate({
            rules: {
                name: {
                    required: true,
                },
                code: {
                    required: true,
                },
                name_en: {
                    required: true,
                },
                name_ch: {
                    required: true,
                },
                strand_id: {
                    required: true,
                },
            },
            messages: {
                name: {
                    required: VALIDATIONS.PLEASE_ENTER_NAME,
                },
                code: {
                    required: VALIDATIONS.PLEASE_ENTER_CODE,
                },
                name_en: {
                    required: VALIDATIONS.PLEASE_ENTER_ENGLISH_NAME,
                },
                name_ch: {
                    required: VALIDATIONS.PLEASE_ENTER_CHINESE_NAME,
                },
                strand_id: {
                    required: VALIDATIONS.PLEASE_SELECT_STRAND,
                },
            },
            errorPlacement: function (error, element) {
                if (element.attr("name") == "status") {
                    error.appendTo("#error-status");
                } else {
                    error.insertAfter(element);
                }
            },
        });

        // Check validation form Global Configurations form in admin panel
        $("#global-configuration-form").validate({
            rules: {
                maximum_ability_history: {
                    // required: true,
                    number: true,
                },
                minimum_ability_history: {
                    // required: true,
                    number: true,
                },
                maximum_trials_attempt: {
                    // required: true,
                    number: true,
                    valueMustGreterThanZero: true,
                },
                question_difficulty_easy_from: {
                    number: true,
                },
                question_difficulty_easy_to: {
                    number: true,
                },
                question_difficulty_medium_from: {
                    number: true,
                },
                question_difficulty_medium_to: {
                    number: true,
                },
                question_difficulty_hard_from: {
                    number: true,
                },
                question_difficulty_hard_to: {
                    number: true,
                },
                struggling_from: {
                    number: true,
                },
                struggling_to: {
                    number: true,
                },
                beginning_from: {
                    number: true,
                },
                beginning_to: {
                    number: true,
                },
                approaching_from: {
                    number: true,
                },
                approaching_to: {
                    number: true,
                },
                proficient_from: {
                    number: true,
                },
                proficient_to: {
                    number: true,
                },
                advanced_from: {
                    number: true,
                },
                advanced_to: {
                    number: true,
                },
                passing_score_percentage: {
                    number: true,
                    min: 1,
                    max: 100,
                },
                passing_score_accuracy: {
                    number: true,
                },
                passing_score_accuracy: {
                    number: true,
                },
                assignment_starting_accuracy_to_earn_credit_points: {
                    number: true,
                },
                assignment_starting_normalized_ability_to_earn_credit_points: {
                    number: true,
                },
                self_learning_exercise_starting_accuracy_to_earn_credit_points:
                    {
                        number: true,
                    },
                self_learning_exercise_starting_normalized_ability_to_earn_credit_points:
                    {
                        number: true,
                    },
                self_learning_test_starting_accuracy_to_earn_credit_points: {
                    number: true,
                },
                self_learning_test_starting_normalized_ability_to_earn_credit_points:
                    {
                        number: true,
                    },
            },
            messages: {
                maximum_ability_history: {
                    // required: VALIDATIONS.REQUIRED_VALUE_MAXIMUM_ABILITY_HISTORY,
                    number: VALIDATIONS.PLEASE_ENTER_ONLY_NUMERIC_VALUE,
                },
                minimum_ability_history: {
                    // required: VALIDATIONS.REQUIRED_VALUE_MAXIMUM_ABILITY_HISTORY,
                    number: VALIDATIONS.PLEASE_ENTER_ONLY_NUMERIC_VALUE,
                },
                maximum_trials_attempt: {
                    // required: VALIDATIONS.REQURED_VALUE_MAXIMUM_TRIAL_ATTEMPTS,
                    number: VALIDATIONS.PLEASE_ENTER_ONLY_NUMERIC_VALUE,
                    valueMustGreterThanZero:
                        VALIDATIONS.PLEASE_ENTER_THAN_ZERO_VALUE,
                },
                question_difficulty_easy_from: {
                    number: VALIDATIONS.PLEASE_ENTER_ONLY_NUMERIC_VALUE,
                },
                question_difficulty_easy_to: {
                    number: VALIDATIONS.PLEASE_ENTER_ONLY_NUMERIC_VALUE,
                },
                question_difficulty_medium_from: {
                    number: VALIDATIONS.PLEASE_ENTER_ONLY_NUMERIC_VALUE,
                },
                question_difficulty_medium_to: {
                    number: VALIDATIONS.PLEASE_ENTER_ONLY_NUMERIC_VALUE,
                },
                question_difficulty_hard_from: {
                    number: VALIDATIONS.PLEASE_ENTER_ONLY_NUMERIC_VALUE,
                },
                question_difficulty_hard_to: {
                    number: VALIDATIONS.PLEASE_ENTER_ONLY_NUMERIC_VALUE,
                },
                struggling_from: {
                    number: VALIDATIONS.PLEASE_ENTER_ONLY_NUMERIC_VALUE,
                },
                struggling_to: {
                    number: VALIDATIONS.PLEASE_ENTER_ONLY_NUMERIC_VALUE,
                },
                beginning_from: {
                    number: VALIDATIONS.PLEASE_ENTER_ONLY_NUMERIC_VALUE,
                },
                beginning_to: {
                    number: VALIDATIONS.PLEASE_ENTER_ONLY_NUMERIC_VALUE,
                },
                approaching_from: {
                    number: VALIDATIONS.PLEASE_ENTER_ONLY_NUMERIC_VALUE,
                },
                approaching_to: {
                    number: VALIDATIONS.PLEASE_ENTER_ONLY_NUMERIC_VALUE,
                },
                proficient_from: {
                    number: VALIDATIONS.PLEASE_ENTER_ONLY_NUMERIC_VALUE,
                },
                proficient_to: {
                    number: VALIDATIONS.PLEASE_ENTER_ONLY_NUMERIC_VALUE,
                },
                advanced_from: {
                    number: VALIDATIONS.PLEASE_ENTER_ONLY_NUMERIC_VALUE,
                },
                advanced_to: {
                    number: VALIDATIONS.PLEASE_ENTER_ONLY_NUMERIC_VALUE,
                },
                passing_score_percentage: {
                    number: VALIDATIONS.PLEASE_ENTER_ONLY_NUMERIC_VALUE,
                    min: VALIDATIONS.PLEASE_ENTER_THAN_ZERO_VALUE,
                    max: VALIDATIONS.MAXIMUM_VALUE_IS_HUNDRED,
                },
                passing_score_accuracy: {
                    number: VALIDATIONS.PLEASE_ENTER_ONLY_NUMERIC_VALUE,
                },
                assignment_starting_accuracy_to_earn_credit_points: {
                    number: VALIDATIONS.PLEASE_ENTER_ONLY_NUMERIC_VALUE,
                },
                assignment_starting_normalized_ability_to_earn_credit_points: {
                    number: VALIDATIONS.PLEASE_ENTER_ONLY_NUMERIC_VALUE,
                },
                self_learning_exercise_starting_accuracy_to_earn_credit_points:
                    {
                        number: VALIDATIONS.PLEASE_ENTER_ONLY_NUMERIC_VALUE,
                    },
                self_learning_exercise_starting_normalized_ability_to_earn_credit_points:
                    {
                        number: VALIDATIONS.PLEASE_ENTER_ONLY_NUMERIC_VALUE,
                    },
                self_learning_test_starting_accuracy_to_earn_credit_points: {
                    number: VALIDATIONS.PLEASE_ENTER_ONLY_NUMERIC_VALUE,
                },
                self_learning_test_starting_normalized_ability_to_earn_credit_points:
                    {
                        number: VALIDATIONS.PLEASE_ENTER_ONLY_NUMERIC_VALUE,
                    },
            },
            errorPlacement: function (error, element) {
                error.insertAfter(element);
            },
            submitHandler: function (form) {
                form.submit();
            },
        });

        // Check Additional methos using validations
        //Value Must be Greter Than Zero
        $.validator.addMethod(
            "valueMustGreterThanZero",
            function (value, element, param) {
                if (value > 0) return true;
                else return false;
            },
            VALIDATIONS.VALUE_MUST_BE_GREATER_THEN_ZERO
        );

        // Check email valid or not
        $.validator.addMethod(
            "checkValidEmail",
            function (value, element, param) {
                return value.match(
                    /^[a-zA-Z0-9_\.%\+\-]+@[a-zA-Z0-9\.\-]+\.[a-zA-Z]{2,}$/
                );
            },
            VALIDATIONS.PLEASE_ENTER_VALID_EMAIL
        );

        $.validator.addMethod(
            "checkValidUrl",
            function (value, element, param) {
                return value.match(
                    /http:\/\/(?:www.)?(?:(vimeo).com\/(.*)|(youtube).com\/watch\?v=(.*?)&)/
                );
            },
            "Please Enter Valid Url!"
        );

        // Check only allowed only sting valid
        $.validator.addMethod(
            "allowedOnlyString",
            function (value, element, param) {
                return value.match(/^[a-zA-Z]+$/);
            },
            VALIDATIONS.ALLOWED_ONLY_CHARACTER_VALUE
        );

        // Number is not allowed additional validations
        $.validator.addMethod(
            "lettersonly",
            function (value, element, param) {
                return this.optional(element) || /^[a-z]+$/i.test(value);
            },
            VALIDATIONS.NUMBER_IS_NOT_PERMITTED
        );
    },
};

// Error Handling Display Message
function ErrorHandlingMessage(response) {
    $("#cover-spin").hide();
    var data = JSON.parse(JSON.stringify(response));
    var errorResponse = data.responseJSON;
    if (errorResponse.status === "failed") {
        toastr.error(errorResponse.message);
    }
}

// Attempt Exams submit
function attempt_exams() {
    var questionspos = "";
    setNoButtonText = "";
    $setYesButtonText = "";
    $setPoptitle = "";
    var language = $("input[name=language]").val();
    $("#cover-spin").show();
    if (questiontype == 2 || questiontype == 1) {
        // Exam type => 2 = Excercise  & 1 = self learning
        if (attempt_ans == 0) {
            if (isReAttempt === false) {
                $("#questions_ans").val(JSON.stringify(question_old_data_new));
                $("#wrong_ans").val(
                    JSON.stringify(wrong_question_old_data_new)
                );
                return true;
            } else {
                $("#attempt_first_trial_data_new").val(
                    JSON.stringify(question_old_data_new)
                );
                $.ajax({
                    url: BASE_URL + "/check-answer",
                    type: "POST",
                    data: {
                        _token: $('meta[name="csrf-token"]').attr("content"),
                        questionid: question_old_data_new,
                    },
                    success: function (response) {
                        if (response.data.length != 0) {
                            $.each(response.data, function (k, v) {
                                questionspos += "Q-" + v.questionNo;
                                if (response.data.length - 1 != k) {
                                    questionspos += ", ";
                                }
                            });
                            if (language == "ch") {
                                questionspos += "<br />" + POPMESSAGE_CH1;
                                $setPoptitle = POPMESSAGETITLE_CH;
                                $setYesButtonText = BUTTONYESTEXTCH;
                                setNoButtonText = BUTTONNOTEXTCH;
                            } else {
                                questionspos += "<br />" + POPMESSAGE_EN1;
                                $setPoptitle = POPMESSAGETITLE_EN;
                                $setYesButtonText = BUTTONYESTEXTEN;
                                setNoButtonText = BUTTONNOTEXTEN;
                            }
                            $.confirm({
                                title: $setPoptitle,
                                content: questionspos,
                                //autoClose: 'Cancellation|8000',
                                buttons: {
                                    TryAgain: {
                                        text: $setYesButtonText,
                                        action: function () {
                                            var examid = $(
                                                "input[name=exam_id]"
                                            ).val();
                                            var language = $(
                                                "input[name=language]"
                                            ).val();
                                            wrong_question_old_data_new =
                                                response.data;
                                            var currentid =
                                                response.data[0]["question_id"];
                                            attempt_ans = 1;
                                            questionNo = 1;
                                            $("#cover-spin").hide();
                                            $.ajax({
                                                url:
                                                    BASE_URL + "/next-question",
                                                type: "POST",
                                                data: {
                                                    _token: $(
                                                        'meta[name="csrf-token"]'
                                                    ).attr("content"),
                                                    currentid: currentid,
                                                    examid: examid,
                                                    examaction: "Next",
                                                    questionNo: questionNo,
                                                    language: language,
                                                    wrong_ans:
                                                        wrong_question_old_data_new,
                                                    attempt_ans: attempt_ans,
                                                    // Get Question Position
                                                    question_position:
                                                        question_position,
                                                    examactionset: "current",
                                                },
                                                success: function (response) {
                                                    $("#nextquestionarea").html(
                                                        response
                                                    );
                                                    if (
                                                        question_old_data_new.length !=
                                                        0
                                                    ) {
                                                        for (
                                                            var icn = 0;
                                                            icn <
                                                            question_old_data_new.length;
                                                            icn++
                                                        ) {
                                                            var question_id_check =
                                                                $(
                                                                    "#nextquestionarea .checkanswer"
                                                                ).attr(
                                                                    "question-id"
                                                                );
                                                            if (
                                                                question_old_data_new[
                                                                    icn
                                                                ][
                                                                    "question_id"
                                                                ] ==
                                                                question_id_check
                                                            ) {
                                                                var answer_ren =
                                                                    question_old_data_new[
                                                                        icn
                                                                    ]["answer"];
                                                                $(
                                                                    "#nextquestionarea .checkanswer[value=" +
                                                                        answer_ren +
                                                                        "]"
                                                                ).prop(
                                                                    "checked",
                                                                    true
                                                                );
                                                                $(
                                                                    "#nextquestion"
                                                                ).prop(
                                                                    "disabled",
                                                                    false
                                                                );
                                                                if (
                                                                    $(
                                                                        "#nextquestion"
                                                                    ).length ==
                                                                    0
                                                                ) {
                                                                    $(
                                                                        "#submitquestion"
                                                                    ).prop(
                                                                        "disabled",
                                                                        false
                                                                    );
                                                                    $(
                                                                        "#submitquestion"
                                                                    ).show();
                                                                }
                                                            }
                                                        }
                                                    }
                                                    //updateMathHtml();
                                                    // updateMathHtmlById(
                                                    //     "nextquestionarea"
                                                    // );
                                                    MathJax.Hub.Queue([
                                                        "Typeset",
                                                        MathJax.Hub,
                                                    ]);
                                                    $("#cover-spin").hide();
                                                },
                                            });
                                        },
                                    },
                                    No: function () {
                                        isReAttempt = false;
                                        $("#questions_ans").val(
                                            JSON.stringify(
                                                question_old_data_new
                                            )
                                        );
                                        $("#wrong_ans").val(
                                            JSON.stringify(
                                                wrong_question_old_data_new
                                            )
                                        );
                                        $("#cover-spin").hide();

                                        $("#AttemptQuestionFeedbackBody").html(
                                            EmojiPopupHtml()
                                        );
                                        $("#isAfterSubmit").val(1);
                                        if ($("#isAfterSubmit").val() == 1) {
                                            $("#AttemptQuestionFeedback").modal(
                                                "show"
                                            );
                                        }
                                        // $("#attempt-exams").submit();
                                        // return true;
                                    },
                                },
                            });
                        } else {
                            isReAttempt = false;
                            $("#questions_ans").val(
                                JSON.stringify(question_old_data_new)
                            );
                            $("#wrong_ans").val(
                                JSON.stringify(wrong_question_old_data_new)
                            );
                            $("#cover-spin").hide();
                            $("#AttemptQuestionFeedbackBody").html(
                                EmojiPopupHtml()
                            );
                            $("#isAfterSubmit").val(1);
                            if ($("#isAfterSubmit").val() == 1) {
                                $("#AttemptQuestionFeedback").modal("show");
                            }
                            // $("#attempt-exams").submit();
                            // return true;
                        }
                    },
                });
            }
        } else {
            $("#questions_ans").val(JSON.stringify(question_old_data_new));
            $("#wrong_ans").val(JSON.stringify(wrong_question_old_data_new));
            $("#cover-spin").hide();
            $("#AttemptQuestionFeedbackBody").html(EmojiPopupHtml());
            $("#isAfterSubmit").val(1);
            if ($("#isAfterSubmit").val() == 1) {
                $("#AttemptQuestionFeedback").modal("show");
                //return true;
            }
        }
    } else {
        $("#questions_ans").val(JSON.stringify(question_old_data_new));
        $("#wrong_ans").val(JSON.stringify(wrong_question_old_data_new));
        $("#cover-spin").hide();
        $("#AttemptQuestionFeedbackBody").html(EmojiPopupHtml());
        $("#isAfterSubmit").val(1);
        if ($("#isAfterSubmit").val() == 1) {
            $("#AttemptQuestionFeedback").modal("show");
        }
        // return true;
    }
    return false;
}

// Attempt Personal Exams  submit
function attempt_personal_exams() {
    var questionspos = "";
    setNoButtonText = "";
    $setYesButtonText = "";
    $setPoptitle = "";
    var language = $("input[name=language]").val();
    if (student_test_type == 1 && self_learning_test_type == 2) {
        $("#questions_ans").val(JSON.stringify(question_old_data_new));
        $("#wrong_ans").val(JSON.stringify(wrong_question_old_data_new));
        return true;
    }

    $("#cover-spin").show();
    if (questiontype == 2 || questiontype == 1) {
        // Exam type => 2 = Excercise  & 1 = self learning
        if (attempt_ans == 0) {
            if (isReAttempt === false) {
                $("#questions_ans").val(JSON.stringify(question_old_data_new));
                $("#wrong_ans").val(
                    JSON.stringify(wrong_question_old_data_new)
                );
                return true;
            } else {
                $("#attempt_first_trial_data_new").val(
                    JSON.stringify(question_old_data_new)
                );
                $.ajax({
                    url: BASE_URL + "/check-answer",
                    type: "POST",
                    data: {
                        _token: $('meta[name="csrf-token"]').attr("content"),
                        questionid: question_old_data_new,
                    },
                    success: function (response) {
                        if (response.data.length != 0) {
                            $.each(response.data, function (k, v) {
                                questionspos += "Q-" + v.questionNo;
                                if (response.data.length - 1 != k) {
                                    questionspos += ", ";
                                }
                            });
                            if (language == "ch") {
                                questionspos += "<br />" + POPMESSAGE_CH1;
                                $setPoptitle = POPMESSAGETITLE_CH;
                                $setYesButtonText = BUTTONYESTEXTCH;
                                setNoButtonText = BUTTONNOTEXTCH;
                            } else {
                                questionspos += "<br />" + POPMESSAGE_EN1;
                                $setPoptitle = POPMESSAGETITLE_EN;
                                $setYesButtonText = BUTTONYESTEXTEN;
                                setNoButtonText = BUTTONNOTEXTEN;
                            }
                            $.confirm({
                                title: $setPoptitle,
                                content: questionspos,
                                //autoClose: 'Cancellation|8000',
                                buttons: {
                                    TryAgain: {
                                        text: $setYesButtonText,
                                        action: function () {
                                            var examid = $(
                                                "input[name=exam_id]"
                                            ).val();
                                            var language = $(
                                                "input[name=language]"
                                            ).val();
                                            wrong_question_old_data_new =
                                                response.data;
                                            var currentid =
                                                response.data[0]["question_id"];
                                            attempt_ans = 1;
                                            questionNo = 1;
                                            $("#cover-spin").hide();
                                            $.ajax({
                                                url:
                                                    BASE_URL + "/next-question",
                                                type: "POST",
                                                data: {
                                                    _token: $(
                                                        'meta[name="csrf-token"]'
                                                    ).attr("content"),
                                                    currentid: currentid,
                                                    examid: examid,
                                                    examaction: "Next",
                                                    questionNo: questionNo,
                                                    language: language,
                                                    wrong_ans:
                                                        wrong_question_old_data_new,
                                                    attempt_ans: attempt_ans,
                                                    // Get Question Position
                                                    question_position:
                                                        question_position,
                                                    examactionset: "current",
                                                },
                                                success: function (response) {
                                                    $("#nextquestionarea").html(
                                                        response
                                                    );
                                                    if (
                                                        question_old_data_new.length !=
                                                        0
                                                    ) {
                                                        for (
                                                            var icn = 0;
                                                            icn <
                                                            question_old_data_new.length;
                                                            icn++
                                                        ) {
                                                            var question_id_check =
                                                                $(
                                                                    "#nextquestionarea .checkanswer"
                                                                ).attr(
                                                                    "question-id"
                                                                );
                                                            if (
                                                                question_old_data_new[
                                                                    icn
                                                                ][
                                                                    "question_id"
                                                                ] ==
                                                                question_id_check
                                                            ) {
                                                                var answer_ren =
                                                                    question_old_data_new[
                                                                        icn
                                                                    ]["answer"];
                                                                $(
                                                                    "#nextquestionarea .checkanswer[value=" +
                                                                        answer_ren +
                                                                        "]"
                                                                ).prop(
                                                                    "checked",
                                                                    true
                                                                );
                                                                $(
                                                                    "#nextquestion"
                                                                ).prop(
                                                                    "disabled",
                                                                    false
                                                                );
                                                                if (
                                                                    $(
                                                                        "#nextquestion"
                                                                    ).length ==
                                                                    0
                                                                ) {
                                                                    $(
                                                                        "#submitquestion"
                                                                    ).prop(
                                                                        "disabled",
                                                                        false
                                                                    );
                                                                    $(
                                                                        "#submitquestion"
                                                                    ).show();
                                                                }
                                                            }
                                                        }
                                                    }
                                                    //updateMathHtml();
                                                    // updateMathHtmlById(
                                                    //     "nextquestionarea"
                                                    // );
                                                    MathJax.Hub.Queue([
                                                        "Typeset",
                                                        MathJax.Hub,
                                                    ]);
                                                    $("#cover-spin").hide();
                                                },
                                            });
                                        },
                                    },
                                    No: function () {
                                        isReAttempt = false;
                                        $("#questions_ans").val(
                                            JSON.stringify(
                                                question_old_data_new
                                            )
                                        );
                                        $("#wrong_ans").val(
                                            JSON.stringify(
                                                wrong_question_old_data_new
                                            )
                                        );
                                        $("#cover-spin").hide();
                                        $("#attempt-exams").submit();
                                        return true;
                                    },
                                },
                            });
                        } else {
                            isReAttempt = false;
                            $("#questions_ans").val(
                                JSON.stringify(question_old_data_new)
                            );
                            $("#wrong_ans").val(
                                JSON.stringify(wrong_question_old_data_new)
                            );
                            $("#cover-spin").hide();
                            $("#attempt-exams").submit();
                            return true;
                        }
                    },
                });
            }
        } else {
            $("#questions_ans").val(JSON.stringify(question_old_data_new));
            $("#wrong_ans").val(JSON.stringify(wrong_question_old_data_new));
            return true;
        }
    } else {
        $("#questions_ans").val(JSON.stringify(question_old_data_new));
        $("#wrong_ans").val(JSON.stringify(wrong_question_old_data_new));
        return true;
    }
    return false;
}

function SetQuestionCodeValue($ArrayOfValue) {
    $questionCodeText = "";
    if ("grades" in $CodeArray) {
        $questionCodeText += $CodeArray["grades"];
    }
    if ("subject_code" in $CodeArray) {
        $questionCodeText += $CodeArray["subject_code"];
    }
    if ("strands" in $CodeArray) {
        $questionCodeText += $CodeArray["strands"];
    }
    if ("learning_units" in $CodeArray) {
        $questionCodeText += $CodeArray["learning_units"];
    }
    if ("learning_objectives" in $CodeArray) {
        $questionCodeText += $CodeArray["learning_objectives"];
        if ("learning_units" in $CodeArray) {
            $questionCodeText += $CodeArray["learning_units"] + "00";
        }
        if ("field_g" in $CodeArray) {
            $questionCodeText += $CodeArray["field_g"];
        }
        if ("question_uniq_number" in $CodeArray) {
            $questionCodeText += $CodeArray["question_uniq_number"];
        }
    }
    $("#Question-Code").val($questionCodeText);
    return $questionCodeText;
}

// Generate Question code
function GenerateQuestionCode() {
    $CodeArray = [];
    // After change Grades
    $gradeId = $("#grade-id").val();
    if ($gradeId) {
        $CodeArray["grades"] = $gradeId + "-";
        SetQuestionCodeValue($CodeArray);
    }
    // After change subjects
    $subject_id = $("#subject-id").val();
    if ($subject_id) {
        $.ajax({
            url: BASE_URL + "/getSubjectCodeById/" + $subject_id,
            type: "GET",
            success: function (subjectResponse) {
                var subjectData = JSON.parse(JSON.stringify(subjectResponse));
                if (subjectData.data.subject_code) {
                    $CodeArray["subject_code"] =
                        subjectData.data.subject_code + "-";
                } else {
                    $CodeArray["subject_code"] = "NA-";
                }
                SetQuestionCodeValue($CodeArray);
            },
            error: function (response) {
                ErrorHandlingMessage(response);
            },
        });
    }

    // After change strands
    $strandId = $("#strand-id").val();
    if ($strandId) {
        if ($strandId == 1) {
            $CodeArray["strands"] = "NA" + "-";
        }
        if ($strandId == 2) {
            $CodeArray["strands"] = "MS" + "-";
        }
        if ($strandId == 3) {
            $CodeArray["strands"] = "DH" + "-";
        }
        SetQuestionCodeValue($CodeArray);
    }

    // After change learning units
    $learningUnitId = $("#learning-unit").val();
    if ($learningUnitId) {
        if ($learningUnitId.length == 1) {
            $CodeArray["learning_units"] = "0" + $learningUnitId;
        } else {
            $CodeArray["learning_units"] = $learningUnitId;
        }
        SetQuestionCodeValue($CodeArray);
    }

    // After change learning objectives
    $learningObjectivesId = $("#learning-objectives").val();
    if ($learningObjectivesId) {
        if ($learningObjectivesId.length == 1) {
            $CodeArray["learning_objectives"] =
                "0" + $learningObjectivesId + "-";
        } else {
            $CodeArray["learning_objectives"] = $learningObjectivesId + "-";
        }
        SetQuestionCodeValue($CodeArray);
    }

    // After change field G
    if ($("#field_g").val()) {
        $CodeArray["field_g"] = $("#field_g").val().toUpperCase();
    }

    // Get question count incremented number
    if ($("#Question-Code").val()) {
        $.ajax({
            url: BASE_URL + "/countQuestionByMapping/",
            type: "POST",
            data: {
                _token: $('meta[name="csrf-token"]').attr("content"),
                grade_id: $("#grade-id").val(),
                subject_id: $("#subject-id").val(),
                strand_id: $("#strand-id").val(),
                learning_unit_id: $("#learning-unit").val(),
                learning_objectives_id: $("#learning-objectives").val(),
                field_g: $("#field_g").val(),
            },
            success: function (subjectResponse) {
                var subjectData = JSON.parse(JSON.stringify(subjectResponse));
                if (subjectData.data.count) {
                    $CodeArray["question_uniq_number"] =
                        "-00" + subjectData.data.count + "-1-00";
                } else {
                    $CodeArray["question_uniq_number"] = "-00-1-00";
                }
                SetQuestionCodeValue($CodeArray);
            },
            error: function (response) {
                ErrorHandlingMessage(response);
            },
        });
    }
}

// Close Popup Modal & reset
function closePopupModal($id) {
    $("#" + $id).on("hidden.bs.modal", function (e) {
        $(this)
            .find("input,textarea,select")
            .val("")
            .end()
            .find("input[type=checkbox], input[type=radio]")
            .prop("checked", "")
            .end();
    });
}

/** USE : Copy Content to english to chinese fields */
function english_to_chinese() {
    // Question copy english to chinese
    CKEDITOR.instances.question_ch.setData(
        CKEDITOR.instances.question_en.getData()
    );

    // Answer english copy to chinese
    CKEDITOR.instances.answer1_ch.setData(
        CKEDITOR.instances.answer1_en.getData()
    );
    CKEDITOR.instances.answer2_ch.setData(
        CKEDITOR.instances.answer2_en.getData()
    );
    CKEDITOR.instances.answer3_ch.setData(
        CKEDITOR.instances.answer3_en.getData()
    );
    CKEDITOR.instances.answer4_ch.setData(
        CKEDITOR.instances.answer4_en.getData()
    );

    // hint answer english to copy chinese
    CKEDITOR.instances.hint_answer1_ch.setData(
        CKEDITOR.instances.hint_answer1_en.getData()
    );
    CKEDITOR.instances.hint_answer2_ch.setData(
        CKEDITOR.instances.hint_answer2_en.getData()
    );
    CKEDITOR.instances.hint_answer3_ch.setData(
        CKEDITOR.instances.hint_answer3_en.getData()
    );
    CKEDITOR.instances.hint_answer4_ch.setData(
        CKEDITOR.instances.hint_answer4_en.getData()
    );

    // Node Hint answer english to copy chinese
    CKEDITOR.instances.node_hint_answer1_ch.setData(
        CKEDITOR.instances.node_hint_answer1_en.getData()
    );
    CKEDITOR.instances.node_hint_answer2_ch.setData(
        CKEDITOR.instances.node_hint_answer2_en.getData()
    );
    CKEDITOR.instances.node_hint_answer3_ch.setData(
        CKEDITOR.instances.node_hint_answer3_en.getData()
    );
    CKEDITOR.instances.node_hint_answer4_ch.setData(
        CKEDITOR.instances.node_hint_answer4_en.getData()
    );

    // General Hints copy english to chinese
    CKEDITOR.instances.general_hints_ch.setData(
        CKEDITOR.instances.general_hints_en.getData()
    );
    // Full Solution English to chinese
    CKEDITOR.instances.full_solution_ch.setData(
        CKEDITOR.instances.full_solution_en.getData()
    );
}

/** USE : Copy Content to chinese to english fields */
function chinese_to_english() {
    CKEDITOR.instances.question_en.setData(
        CKEDITOR.instances.question_ch.getData()
    );

    // Answer chinese copy to english
    CKEDITOR.instances.answer1_en.setData(
        CKEDITOR.instances.answer1_ch.getData()
    );
    CKEDITOR.instances.answer2_en.setData(
        CKEDITOR.instances.answer2_ch.getData()
    );
    CKEDITOR.instances.answer3_en.setData(
        CKEDITOR.instances.answer3_ch.getData()
    );
    CKEDITOR.instances.answer4_en.setData(
        CKEDITOR.instances.answer4_ch.getData()
    );

    // Hint answer chinese to copy english
    CKEDITOR.instances.hint_answer1_en.setData(
        CKEDITOR.instances.hint_answer1_ch.getData()
    );
    CKEDITOR.instances.hint_answer2_en.setData(
        CKEDITOR.instances.hint_answer2_ch.getData()
    );
    CKEDITOR.instances.hint_answer3_en.setData(
        CKEDITOR.instances.hint_answer3_ch.getData()
    );
    CKEDITOR.instances.hint_answer4_en.setData(
        CKEDITOR.instances.hint_answer4_ch.getData()
    );

    // Node Hint answer chinese to copy english
    CKEDITOR.instances.node_hint_answer1_en.setData(
        CKEDITOR.instances.node_hint_answer1_ch.getData()
    );
    CKEDITOR.instances.node_hint_answer2_en.setData(
        CKEDITOR.instances.node_hint_answer2_ch.getData()
    );
    CKEDITOR.instances.node_hint_answer3_en.setData(
        CKEDITOR.instances.node_hint_answer3_ch.getData()
    );
    CKEDITOR.instances.node_hint_answer4_en.setData(
        CKEDITOR.instances.node_hint_answer4_ch.getData()
    );

    // General Hints copy chinese to english
    CKEDITOR.instances.general_hints_en.setData(
        CKEDITOR.instances.general_hints_ch.getData()
    );

    // Full Solution Chinese to english
    CKEDITOR.instances.full_solution_en.setData(
        CKEDITOR.instances.full_solution_ch.getData()
    );
}

function check_ans(ans_id, weakness_id) {
    $("#nodeModal select#main_node_id").val("").trigger("change");
    $(".node-info").hide();
    var ans_id_lbl = $("input[name=" + ans_id + "]").val();
    if (ans_id_lbl != "") {
        $("#nodeModal #main_node_id").val(ans_id_lbl);
        $("#nodeModal #main_node_id").change();
    }
    $("#nodeModal select#main_node_id").attr("data-ans-id", ans_id);
    $("#nodeModal select#main_node_id").attr("data-weakness-id", weakness_id);
}

function node_link() {
    $("#cover-spin").show();
    var data_ans_id = $("#nodeModal select#main_node_id").attr("data-ans-id");
    var data_weakness_id = $("#nodeModal select#main_node_id").attr(
        "data-weakness-id"
    );
    var ans_id_ch = data_ans_id.replace("_en", "_ch");
    var ans_id_en = data_ans_id.replace("_ch", "_en");
    var weakness_id_ch = data_weakness_id.replace("_en", "_ch");
    var weakness_id_en = data_weakness_id.replace("_ch", "_en");
    if (ans_id_en == data_ans_id) {
        $("#" + ans_id_ch).val($("#main_node_id").val());
    } else {
        $("#" + ans_id_en).val($("#main_node_id").val());
    }
    $("#" + data_ans_id).val($("#main_node_id").val());
    $("#" + data_weakness_id).val("");
    if ($("#nodeModal #node-weakness").text() != "Not Available") {
        if (weakness_id_en == data_weakness_id) {
            $("#" + weakness_id_en).val(
                $("#nodeModal #node-weakness-en").text()
            );
            $("#" + weakness_id_ch).val($("#nodeModal #node-weakness").text());
            if ($("#nodeModal #node-weakness-ch").text() != "") {
                $("#" + weakness_id_ch).val(
                    $("#nodeModal #node-weakness-ch").text()
                );
            }
        } else {
            $("#" + weakness_id_en).val(
                $("#nodeModal #node-weakness-ch").text()
            );
            $("#" + weakness_id_ch).val(
                $("#nodeModal #node-weakness-ch").text()
            );
            if ($("#nodeModal #node-weakness-en").text() != "") {
                $("#" + weakness_id_en).val(
                    $("#nodeModal #node-weakness-en").text()
                );
            }
        }
    }
    $("#main_node_id").select2("val", "");
    $(".node-info").hide();
    $("#main_node_id").val("");
    $("#nodeModal").modal("hide");
    $("#cover-spin").hide();
}

/**
 * Open popup question video hints in the question module
 */
function question_video_hints(language) {
    var selectedVideoId = $("#question_video_id_" + language).val();
    var full_question_code = $("#naming_structure_code").val();
    var fileName = $("#filterFileName").val();
    if (full_question_code) {
        $("#cover-spin").show();
        $.ajax({
            url: BASE_URL + "/question/video-hint",
            type: "GET",
            data: {
                _token: $('meta[name="csrf-token"]').attr("content"),
                question_code: full_question_code,
                selectedVideoId: selectedVideoId,
                filename: fileName,
                language: language,
            },
            success: function (response) {
                $("#cover-spin").hide();
                var data = JSON.parse(JSON.stringify(response));
                if (data.data.html) {
                    $(".video-hints-list").html(data.data.html);
                    $("#QuestionVideoHintsModal").modal("show");
                } else {
                    $(".video-hints-list").html("");
                    toastr.error(VALIDATIONS.NO_AVAILABLE_VIDEO);
                }
            },
            error: function (response) {
                ErrorHandlingMessage(response);
            },
        });
    } else {
        toastr.error(VALIDATIONS.PLEASE_ENTER_QUESTION_CODE);
    }
}

/**
 * USE : On click submit button check video hints are selected or not
 */
function VideoHintIsSelectOrNot() {
    var videoId = $("input[name=questionVideoHintID]:checked").val();
    if (typeof videoId == "undefined") {
        toastr.error(VALIDATIONS.PLEASE_SELECT_VIDEO);
    } else {
        $(".video-hints-list").html("");
        $("#question_video_id").val(videoId);
        $(".closepopup").trigger("click");
    }
}

function updateQueryStringParameter(uri, key, value) {
    var re = new RegExp("([?&])" + key + "=.*?(&|$)", "i");
    var separator = uri.indexOf("?") !== -1 ? "&" : "?";
    if (uri.match(re)) {
        return uri.replace(re, "$1" + key + "=" + value + "$2");
    } else {
        return uri + separator + key + "=" + value;
    }
}

/**
 * USE : Check Question code is valid or not
 */
function validateQuestionCode(questionCode) {
    var isValidation = true;
    if (questionCode) {
        var QuestionCodeArray = questionCode.split("-");
        if (QuestionCodeArray.length == 8 || QuestionCodeArray.length == 7) {
            if (
                QuestionCodeArray.length == 8 &&
                typeof QuestionCodeArray[7] != "undefined"
            ) {
                if (QuestionCodeArray[7]) {
                    if (
                        (QuestionCodeArray[7].length == 3 &&
                            QuestionCodeArray[7].substring(0, 1) == "S") ||
                        QuestionCodeArray[7].substring(0, 1) == "E" ||
                        QuestionCodeArray[7].substring(0, 1) == "T"
                    ) {
                        var isValidation = true;
                    } else {
                        var isValidation = false;
                    }
                } else {
                    var isValidation = false;
                }
            }

            // Validation is false then show error message
            if (!isValidation) {
                $(".knowledge-node").text("");
                $(".knwldge-que-code").hide();
                $(".naming_structure_code_error").text(INVALID_QUESTION_CODE);
            }
        } else {
            $(".knowledge-node").text("");
            $(".knwldge-que-code").hide();
            $(".naming_structure_code_error").text(INVALID_QUESTION_CODE);
        }
    }
    return isValidation;
}

function knowledgeNode(questionCode) {
    var knowledgeNode = "";
    if (questionCode) {
        if (validateQuestionCode(questionCode)) {
            var QuestionCodeArray = questionCode.split("-");
            if (
                QuestionCodeArray.length == 7 ||
                QuestionCodeArray.length == 8
            ) {
                //Grade
                if (QuestionCodeArray[0]) {
                    knowledgeNode += "A = " + QuestionCodeArray[0] + ", ";
                }
                // Strands
                if (QuestionCodeArray[1]) {
                    knowledgeNode += "B = " + QuestionCodeArray[1] + ", ";
                }
                // Strands
                if (QuestionCodeArray[2]) {
                    knowledgeNode +=
                        "C = " + QuestionCodeArray[2].substring(0, 2) + " ,";
                    knowledgeNode +=
                        "D = " + QuestionCodeArray[2].substring(2, 4) + " ,";
                }
                // Strands
                if (QuestionCodeArray[3]) {
                    knowledgeNode +=
                        "E = " + QuestionCodeArray[3].substring(0, 2) + ", ";
                    knowledgeNode +=
                        "F = " + QuestionCodeArray[3].substring(2, 4) + ", ";
                    knowledgeNode +=
                        "G = " + QuestionCodeArray[3].substring(5, 4) + ", ";
                }
                if (QuestionCodeArray[4]) {
                    knowledgeNode += "H = " + QuestionCodeArray[4] + ", ";
                }
                // Difficulty Level
                if (QuestionCodeArray[5]) {
                    // Set default value
                    $("input[name='dificulaty_level']").each(function (
                        index,
                        obj
                    ) {
                        // loop all checked items
                        if (this.value == QuestionCodeArray[5]) {
                            $(this).trigger("click");
                        }
                    });
                    // Get Difficulty Level Name from databse
                    $.ajax({
                        url:
                            BASE_URL +
                            "/question-bank/get-difficulty-value/" +
                            QuestionCodeArray[5],
                        type: "GET",
                        async: false,
                        success: function (response) {
                            var data = JSON.parse(JSON.stringify(response));
                            if (data.data) {
                                knowledgeNode +=
                                    data.data +
                                    " = " +
                                    QuestionCodeArray[5] +
                                    ", ";
                            } else {
                                knowledgeNode +=
                                    QuestionCodeArray[5] +
                                    " = No Available Difficulty Level Name, ";
                            }
                        },
                    });

                    // Get Difficulty Value from databse
                    $.ajax({
                        url:
                            BASE_URL +
                            "/question-bank/get-ai-difficulty-value/" +
                            QuestionCodeArray[5],
                        type: "GET",
                        async: false,
                        success: function (response) {
                            var data = JSON.parse(JSON.stringify(response));
                            if (data.data && data.data.length != 0) {
                                $("#dificulty-value").text(data.data.title);
                            } else {
                                $("#dificulty-value").text(
                                    VALIDATIONS.NO_AVAILABLE_DIFFICULTY_VALUE
                                );
                            }
                        },
                    });
                }
                // Set Reserverd Question Number
                if (QuestionCodeArray[6]) {
                    knowledgeNode +=
                        "Reserved = " + QuestionCodeArray[6] + ", ";
                }
                if (QuestionCodeArray.length == 8 && QuestionCodeArray[7]) {
                    $(".question_type").prop("checked", false);
                    if (
                        QuestionCodeArray[7].substring(0, 1) == "S" ||
                        QuestionCodeArray[7].substring(0, 1) == "S"
                    ) {
                        knowledgeNode +=
                            "Self Learning = " +
                            QuestionCodeArray[7].substring(0, 1) +
                            ", ";
                        $("input[name='question_type[]']").each(function (
                            index,
                            obj
                        ) {
                            // loop all checked items
                            if (this.value == 1) {
                                $(this).prop("checked", true);
                            }
                        });
                    }
                    if (
                        QuestionCodeArray[7].substring(0, 1) == "E" ||
                        QuestionCodeArray[7].substring(0, 1) == "e"
                    ) {
                        knowledgeNode +=
                            "Exercise = " +
                            QuestionCodeArray[7].substring(0, 1) +
                            ", ";
                        $("input[name='question_type[]']").each(function (
                            index,
                            obj
                        ) {
                            // loop all checked items
                            if (this.value == 2) {
                                $(this).prop("checked", true);
                            }
                        });
                    }
                    if (
                        QuestionCodeArray[7].substring(0, 1) == "T" ||
                        QuestionCodeArray[7].substring(0, 1) == "t"
                    ) {
                        knowledgeNode +=
                            "Testing = " +
                            QuestionCodeArray[7].substring(0, 1) +
                            ", ";
                        $("input[name='question_type[]']").each(function (
                            index,
                            obj
                        ) {
                            // loop all checked items
                            if (this.value == 3) {
                                $(this).prop("checked", true);
                            }
                        });
                    }
                    // Suffix
                    knowledgeNode +=
                        "Suffix = " +
                        QuestionCodeArray[7].substring(
                            1,
                            QuestionCodeArray[7].length
                        );
                } else {
                    $(".question_type").prop("checked", false);
                    $("input[name='question_type[]']").each(function (
                        index,
                        obj
                    ) {
                        // loop all checked items
                        if (this.value == 4) {
                            $(this).prop("checked", true);
                        }
                    });
                }
                $(".knowledge-node").text(knowledgeNode);
                $(".knwldge-que-code").show();
            }
        }
    }
}

// subadminpassword_err
function checkMultipleSubAdminFieldValidate(type) {
    var nofErr = 0;
    var emailData = [];
    // Check English name is required
    $(".subAdminName").each(function () {
        if (this.value == "") {
            $(this)
                .closest(".form-group")
                .find(".subadminname_err")
                .text(ENGLISH_NAME_REQUIRED);
            nofErr++;
        } else {
            $(this).closest(".form-group").find(".subadminname_err").text("");
        }
    });

    //check Chinese Name required
    $(".subAdminNameCh").each(function () {
        if (this.value == "") {
            $(this)
                .closest(".form-group")
                .find(".subadminnamech_err")
                .text(CHINESE_NAME_REQUIRED);
            nofErr++;
        } else {
            $(this).closest(".form-group").find(".subadminnamech_err").text("");
        }
    });

    // Check email is required
    $(".subAdminEmail").each(function () {
        if (this.value == "") {
            $(this)
                .closest(".form-group")
                .find(".subadminemail_err")
                .text(EMAIL_REQUIRED);
            nofErr++;
        } else {
            // Check email in current form
            if ($.inArray(this.value, emailData) != -1) {
                $(this)
                    .closest(".form-group")
                    .find(".subadminemail_err")
                    .text(EMAIL_ALREADY_EXISTS);
                nofErr++;
            } else {
                // Check email is valid formate or not
                if (
                    !this.value.match(
                        /^[a-zA-Z0-9_\.%\+\-]+@[a-zA-Z0-9\.\-]+\.[a-zA-Z]{2,}$/
                    )
                ) {
                    $(this)
                        .closest(".form-group")
                        .find(".subadminemail_err")
                        .text(PLEASE_ENTER_VALID_EMAIL);
                    nofErr++;
                } else {
                    var uid = "";
                    if (
                        type == "edit" &&
                        $(this)
                            .closest(".add-more-admin")
                            .find("input[name='u_id[]']").length != 0
                    ) {
                        uid = $(this)
                            .closest(".add-more-admin")
                            .find("input[name='u_id[]']")
                            .val();
                    }
                    $.ajax({
                        url: BASE_URL + "/check-email-exists",
                        type: "GET",
                        async: false,
                        context: this,
                        data: {
                            email: this.value,
                            uid: uid,
                        },
                        success: function (response) {
                            var data = JSON.parse(JSON.stringify(response));
                            if (data.data) {
                                $(this)
                                    .closest(".form-group")
                                    .find(".subadminemail_err")
                                    .text(EMAIL_ALREADY_EXISTS);
                                nofErr++;
                            } else {
                                $(this)
                                    .closest(".form-group")
                                    .find(".subadminemail_err")
                                    .text("");
                                emailData.push(this.value);
                            }
                        },
                    });
                }
            }
        }
    });

    // Check Password is required
    $(".subAdminPassword").each(function () {
        if (this.value == "") {
            if (
                type == "edit" &&
                $(this).closest(".add-more-admin").find("input[name='u_id[]']")
                    .length != 0
            ) {
            } else {
                $(this)
                    .closest(".form-group")
                    .find(".subadminpassword_err")
                    .text(PASSWORD_IS_REQUIRED);
                nofErr++;
            }
        } else {
            if (this.value.length >= 8) {
                $(this)
                    .closest(".form-group")
                    .find(".subadminpassword_err")
                    .text(MINIMUM_EIGHT_CHARACTER_REQUIRED);
            } else {
                $(this)
                    .closest(".form-group")
                    .find(".subadminpassword_err")
                    .text("");
            }
        }
    });
    return nofErr;
}

function submitForm($formId) {
    // Call submit() method on <form id='myform'>
    document.getElementById($formId).submit();
}

/**
 * USE : Update Math html
 */
function updateMathHtml() {
    if (
        "com" in window &&
        "wiris" in window.com &&
        "js" in window.com.wiris &&
        "JsPluginViewer" in window.com.wiris.js
    ) {
        com.wiris.js.JsPluginViewer.parseDocument();
        //com.wiris.js.JsPluginViewer.parseElement(domElement, true, function(){})
        // var preview_div = document.getElementById(htmlid);
        // imgSetTitle(preview_div);
    }
}

/**
 * USE : Update math html based on by element id
 */
function updateMathHtmlById(domElement) {
    //com.wiris.js.JsPluginViewer.parseElement(document.getElementById("nextquestionarea"), true, function(){});
    com.wiris.js.JsPluginViewer.parseElement(
        document.getElementById(domElement),
        true,
        function () {}
    );
}

/**
 *  USE : Check File Exists or not.
 */
function FileExist(urlToFile) {
    var xhr = new XMLHttpRequest();
    xhr.open("HEAD", urlToFile, false);
    xhr.send();
    if (xhr.status == "404") {
        return false;
    } else {
        return true;
    }
}

/**
 * USE : Update CKEdiotr Instances
 */
function UpdateCkEditorInstance() {
    CKEDITOR.instances.question_en.updateElement();
    CKEDITOR.instances.question_ch.updateElement();
    CKEDITOR.instances.answer1_en.updateElement();
    CKEDITOR.instances.answer2_en.updateElement();
    CKEDITOR.instances.answer3_en.updateElement();
    CKEDITOR.instances.answer4_en.updateElement();
    CKEDITOR.instances.answer1_ch.updateElement();
    CKEDITOR.instances.answer2_ch.updateElement();
    CKEDITOR.instances.answer3_ch.updateElement();
    CKEDITOR.instances.answer4_ch.updateElement();

    CKEDITOR.instances.hint_answer1_en.updateElement();
    CKEDITOR.instances.node_hint_answer1_en.updateElement();

    CKEDITOR.instances.hint_answer2_en.updateElement();
    CKEDITOR.instances.node_hint_answer2_en.updateElement();

    CKEDITOR.instances.hint_answer3_en.updateElement();
    CKEDITOR.instances.node_hint_answer3_en.updateElement();

    CKEDITOR.instances.hint_answer4_en.updateElement();
    CKEDITOR.instances.node_hint_answer4_en.updateElement();

    CKEDITOR.instances.hint_answer1_ch.updateElement();
    CKEDITOR.instances.node_hint_answer1_ch.updateElement();

    CKEDITOR.instances.hint_answer2_ch.updateElement();
    CKEDITOR.instances.node_hint_answer2_ch.updateElement();

    CKEDITOR.instances.hint_answer3_ch.updateElement();
    CKEDITOR.instances.node_hint_answer3_ch.updateElement();

    CKEDITOR.instances.hint_answer4_ch.updateElement();
    CKEDITOR.instances.node_hint_answer4_ch.updateElement();
}

/**
 * USE : Preview questions
 */
function PreviewQuestion(formId) {
    $("#cover-spin").show();
    // Call function to update ckeditor instance
    UpdateCkEditorInstance();
    var formData = $("#" + formId).serialize();
    formData = formData.replace("&_method=patch", "");
    $.ajax({
        url: BASE_URL + "/question-preview",
        type: "POST",
        data: formData,
        success: function (response) {
            if (response.data) {
                $("#modalPreviewQuestion  .modal-body").html(
                    response.data.html
                );
                $("#modalPreviewQuestion").modal("show");
                $("#cover-spin").hide();
                setTimeout(function () {
                    //updateMathHtmlById("question_data");
                    MathJax.Hub.Queue(["Typeset", MathJax.Hub]);
                }, 500);
            }
        },
        error: function (response) {
            ErrorHandlingMessage(response);
        },
    });
}

/**
 * USE : Preview questions
 */
function PreviewQuestionList(questionId) {
    $("#cover-spin").show();
    // Call function to update ckeditor instance
    // UpdateCkEditorInstance();
    // var formData = $("#" + formId).serialize();
    // formData = formData.replace("&_method=patch", "");
    $.ajax({
        url: BASE_URL + "/question-preview-list",
        type: "GET",
        data: { questionId },
        success: function (response) {
            if (response.data) {
                $("#modalPreviewQuestion  .modal-body").html(
                    response.data.html
                );
                $("#modalPreviewQuestion").modal("show");
                $("#cover-spin").hide();
                setTimeout(function () {
                    //updateMathHtmlById("question_data");
                    MathJax.Hub.Queue(["Typeset", MathJax.Hub]);
                }, 500);
            }
        },
        error: function (response) {
            ErrorHandlingMessage(response);
        },
    });
}

/**
 * USE : Export class performance report
 */
function ExportClassPerformanceReport(examID, classIds, groupIds) {
    $("#cover-spin").show();
    $.ajax({
        url: BASE_URL + "/report/export/performance-report",
        type: "GET",
        data: {
            examId: examID,
            classIds: classIds,
            groupIds: groupIds,
        },
        success: function (response) {
            $("#cover-spin").hide();
            var isHTML = RegExp.prototype.test.bind(/(<([^>]+)>)/i);
            if (!isHTML(response)) {
                var downloadLink = document.createElement("a");
                var fileData = ["\ufeff" + response];

                var blobObject = new Blob(fileData, {
                    type: "text/csv;charset=utf-8;",
                });

                var url = URL.createObjectURL(blobObject);
                downloadLink.href = url;
                downloadLink.download = "ClassPerformanceReport.csv";

                document.body.appendChild(downloadLink);
                downloadLink.click();
                document.body.removeChild(downloadLink);
            }
        },
        error: function (response) {
            ErrorHandlingMessage(response);
        },
    });
}

/**
 * USE : Update Group Member
 */
function updateGroupMember(
    form,
    dreamschat_group_id,
    GroupMemberData,
    newMemberList,
    newMemberListLength,
    rci
) {
    if (GroupMemberData.length != 0) {
        for (var checkUid = 0; checkUid < GroupMemberData.length; checkUid++) {
            if (GroupMemberData[checkUid]["id"] == newMemberList[rci]) {
                var Udata = GroupMemberData[checkUid];
                var response = { data: Udata };
                var userData = response.data;
                var currentusers = "";
                var ref = firebase
                    .database()
                    .ref("data/users")
                    .orderByChild("e-mail")
                    .equalTo(response.data.email);
                ref.once("value", (snapshot) => {
                    if (snapshot.exists()) {
                        snapshotData = Object.keys(snapshot.val());
                        currentusers = snapshotData[0];
                    } else {
                        currentusers = addUser(userData);
                    }
                    setTimeout(function () {
                        var ref = database
                            .ref("data/users")
                            .orderByChild("id")
                            .equalTo(currentusers);
                        ref.once("value", (snapshot) => {
                            if (snapshot.exists()) {
                                snapshotData = Object.keys(snapshot.val());
                                var usersession = snapshotData[0];
                                var group_id = dreamschat_group_id;
                                //added to group
                                firebase
                                    .database()
                                    .ref("data/groups/" + group_id + "/userIds")
                                    .once("value", function (snapshot) {
                                        var searchIDs = snapshot.val();
                                        if (
                                            $.inArray(
                                                usersession,
                                                snapshot.val()
                                            ) !== -1
                                        ) {
                                        } else {
                                            searchIDs.push(usersession);
                                            firebase
                                                .database()
                                                .ref(
                                                    "data/groups/" +
                                                        group_id +
                                                        "/userIds"
                                                )
                                                .set(searchIDs);
                                        }
                                    });
                            } else {
                                var currentusers = addUser(userData);
                                var usersession = currentusers;
                                var group_id = dreamschat_group_id;
                                //added to group
                                firebase
                                    .database()
                                    .ref("data/groups/" + group_id + "/userIds")
                                    .once("value", function (snapshot) {
                                        var searchIDs = snapshot.val();
                                        if (
                                            $.inArray(
                                                usersession,
                                                snapshot.val()
                                            ) !== -1
                                        ) {
                                        } else {
                                            searchIDs.push(usersession);
                                            firebase
                                                .database()
                                                .ref(
                                                    "data/groups/" +
                                                        group_id +
                                                        "/userIds"
                                                )
                                                .set(searchIDs);
                                        }
                                    });
                            }
                        });
                    }, 500);
                });
            }
        }
        setTimeout(function () {
            rci++;
            if (newMemberListLength != rci) {
                updateGroupMember(
                    form,
                    dreamschat_group_id,
                    GroupMemberData,
                    newMemberList,
                    newMemberListLength,
                    rci
                );
            } else {
                form.submit();
            }
        }, 1500);
    }
}

function GetQuestionSolutionPrefixByLanguage() {
    var prefix = "SE";
    if (APP_LANGUAGE == "ch") {
        prefix = "SC";
    }
    return prefix;
}
