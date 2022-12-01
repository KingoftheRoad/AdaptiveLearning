@extends('backend.layouts.app')
    @section('content')
		<div class="wrapper d-flex align-items-stretch sm-deskbord-main-sec">
        @include('backend.layouts.sidebar')
	      <div id="content" class="pl-2 pb-5">
            @include('backend.layouts.header')
            @if (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            @if($errors->any())
                {{ implode('', $errors->all('<div>:message</div>')) }}
            @endif
            
            <div class="sm-right-detail-sec pl-5 pr-5">
				<div class="container-fluid">
					<div class="row">
						<div class="col-md-12">
							<div class="sec-title">
								<h2 class="mb-4 main-title">{{__('languages.assign_credit_points')}}</h2>
							</div>
							<hr class="blue-line">
						</div>
					</div>
					<div class="sm-add-user-sec card">
						<div class="select-option-sec pb-5 card-body">
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
                            <form method="POST" id="manualAssignCreditPoint" action="{{ route('manual-assign-credit-point') }}" novalidate>
                                @csrf
                                <div class="student-grade-class-section row">
                                    <div class="form-grade-heading col-lg-3">
                                        <label>{{__('languages.question_generators_menu.grade-classes')}}</label>
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
                                                    <input type="checkbox" name="classes[{{$grade->id}}][]" value="{{$classes->id}}" class="question-generator-class-chkbox" data-label="{{$grade->name}}{{$classes->name}}">
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
                                <div class="form-group student_peer_group_section mt-3 row">
                                    <div class="student_peer_group_heading col-lg-3">
                                        <label>{{__('languages.question_generators_menu.student_peer_groups')}}</label>
                                    </div>
                                    <div class="student_peer_group_option col-lg-3">
                                        <select class="form-control select-option" data-show-subtext="true" data-live-search="true" name="peerGroupIds[]" id="question-generator-peer-group-options"  multiple>
                                            @if($PeerGroupList)
                                                @foreach($PeerGroupList as $peerGroup)
                                                    <option value="{{$peerGroup->id}}" data-label="{{$peerGroup->PeerGroupName}}">{{$peerGroup->PeerGroupName}}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group student_peer_group_section mt-3 student-list"></div>

                                <div class="form-group student_peer_group_section mt-3 row">
                                    <div class="col-md-3">
                                        <label>{{__('languages.no_of_credit_point')}}</label>
                                    </div>
                                    <div class="col-md-3">
                                        <input type="text" name="number_of_credit_point" id="number_of_credit_point" class="form-control" placeholder="{{__('languages.no_of_credit_point')}}" />
                                    </div>
                                </div>
                                <div class="form-group mt-3">
                                    <button type="button"  onclick="manualAssignCreditPoint();" name="submit" value="submit" id="manualAssignCreditPointSubmit" class="btn-search ">{{ __('languages.submit') }}</button>
                                </div>
                            </form>
						</div>
					</div>
				</div>
			</div>
	      </div>
		</div>
        @include('backend.layouts.footer')
        <script>
            var ClassIds = [];
            var GradeIds = [];
            var fromSubmit=0;
            $(document).ready(function(){
                /**
                * USE : On click event click on the grade checkbox
                */
                $(document).on('click', '.question-generator-grade-chkbox', function(){
                    //$('.question-generator-class-chkbox').prop('checked',false);
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

                
            });

            /**
            * USE : On change event peer group select
            */
            $(document).on('change', '.student_peer_group_option select', function(){
                $(this).find('option:selected').each(function(){
                    $("#cover-spin").show();
                    var peerGroupId=$(this).attr('value');
                    var dataLabel=$(this).attr('data-label');
                    $(".group_"+peerGroupId).remove();
                    $.ajax({
                        url: BASE_URL + '/get-students-list-checkbox',
                        type: 'GET',
                        data: {
                            'peerGroupId': peerGroupId,
                            'dataType':'peer_group'
                        },
                        success: function(response) {
                            if(response.data){
                                $('.student-list').append('<div class="row border mb-2 rounded group_'+peerGroupId+'"><div class="col-md-2 mt-1">'+dataLabel+'</div><div class="col-md-10 mt-1"><div class="row">'+response.data+'</div></div></div>');
                            }
                            $("#cover-spin").hide();
                        },
                        error: function(response) {
                            $("#cover-spin").hide();
                            ErrorHandlingMessage(response);
                        }
                    });
                });
                $(this).find('option:not(:selected)').each(function(){
                     var peerGroupId=$(this).attr('value');
                    var dataLabel=$(this).attr('data-label');
                    $(".group_"+peerGroupId).remove();
                });
                    /*
                });*/
            });
            /**
             * USE : Get the student list based on select grades and classes
             * Trigger : on select the grades and class
             * Return data : All the student list based on select grade and classes
             */
            function getStudents(gradeIds, classIds){

                $('.student-list').prop('disabled',false);
                $('.student-list').html('');
                $("#cover-spin").show();
                if(gradeIds.length==0 && classIds.length==0)
                {
                    $("#cover-spin").hide();
                    return null;
                }
                $.each(classIds, function (key,classId) {
                    var dataLabel=$(".question-generator-class-chkbox[value="+classId+"]").attr('data-label');
                    var dataA=new Array();
                    dataA.push(classId);
                    $.ajax({
                        url: BASE_URL + '/get-students-list-checkbox',
                        type: 'GET',
                        data: {
                            'gradeIds': gradeIds,
                            'classIds': dataA,
                            'dataType':'grade-class'
                        },
                        success: function(response) {
                            if(response.data){
                                $('.student-list').append('<div class="row border mb-2 rounded"><div class="col-md-2 mt-1">'+dataLabel+'</div><div class="col-md-10 mt-1"><div class="row">'+response.data+'</div></div></div>');
                            }
                            $("#cover-spin").hide();
                        },
                        error: function(response) {
                            ErrorHandlingMessage(response);
                        }
                    });
                });
                $('.student_peer_group_option selectoption:selected').each(function(){
                    var peerGroupId=$(this).attr('value');
                    var dataLabel=$(this).attr('data-label');
                    $(".group_"+peerGroupId).remove();
                    $.ajax({
                        url: BASE_URL + '/get-students-list-checkbox',
                        type: 'GET',
                        data: {
                            'peerGroupId': peerGroupId,
                            'dataType':'peer_group'
                        },
                        success: function(response) {
                            $("#cover-spin").hide();
                            if(response.data){
                                $('.student-list').append('<div class="row border mb-2 rounded group_'+peerGroupId+'"><div class="col-md-2 mt-1">'+dataLabel+'</div><div class="col-md-10 mt-1"><div class="row">'+response.data+'</div></div></div>');
                            }

                            $("#cover-spin").hide();
                        },
                        error: function(response) {
                            ErrorHandlingMessage(response);
                        }
                    });
                });
            }
            // form submit
                function manualAssignCreditPoint(){
                    /*if(fromSubmit==0)
                    {*/
                        $('label.error').remove();
                        var formIsValid=0;
                        $(document).find('[name="peerGroupIds[]"]').each(function(){
                            var element = $(this).closest('.form-group').css('display');
                            if($.trim($(this).val()) == '' && element != 'none'){
                                var label = $(this).closest('.form-group').find('label:eq(0)').text();
                                formIsValid++;
                            }
                        });
                        if($(document).find('.question-generator-class-chkbox:checked').length==0)
                        {
                            formIsValid++;
                        }
                        if(formIsValid==2)
                        {

                            $(document).find('[name="peerGroupIds[]"]').parent().append('<label class="error w-100">Please Select Grade Class Or Peer Group</label>');
                            $(document).find('.question-generator-class-chkbox').closest('.form-grade-select-section').append('<label class="error w-100">Please Select Grade Class Or Peer Group</label>');

                            return false;
                        }
                        if($(document).find('#number_of_credit_point').val()=='')
                        {
                            $(document).find('#number_of_credit_point').parent().append('<label class="error w-100">Please Add Credit Point</label>');
                            //return null;
                            formIsValid++;

                            return false;
                        }
                        if(formIsValid>=0 && formIsValid<2)
                        {

                            if($(document).find('.student-list .form-check-input:checked').length==0)
                            {
                                $(document).find('.student-list').append('<label class="error w-100">Please Select Students</label>');

                                return false;
                            }
                            else
                            {
                                
                                $.confirm({
                                    title: "sure want to add credit points" + "?",
                                    content: CONFIRMATION,
                                    autoClose: "Cancellation|8000",
                                    buttons: {
                                        deleteSubject: {
                                            text: "Assign Credit Point",
                                            action: function () {
                                                //$("#cover-spin").show();
                                                fromSubmit=1;
                                                console.log($('#manualAssignCreditPoint').val());
                                                //$("#manualAssignCreditPoint").submit();
                                                //$( "#manualAssignCreditPoint" )[0].submit();  
                                                $("#manualAssignCreditPoint")[0].dispatchEvent(new Event('submit')); 
                                                return true;

                                            },
                                        },
                                        Cancellation: function () {},
                                    },
                                });
                            }
                        }
                        else
                        {
                            return false;
                        }
                        return false;
                    //}
                }
        </script>
@endsection