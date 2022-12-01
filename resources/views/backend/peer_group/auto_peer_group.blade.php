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
								<h2 class="mb-4 main-title">{{__('languages.peer_group.auto_create_peer_group')}}</h2>
							</div>
                            <div class="sec-title">
                                <a href="javascript:void(0);" class="btn-back" id="backButton">{{__('languages.back')}}</a>
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
                            <form method="POST" name="createAutoPeerGroupForm" id="createAutoPeerGroupForm">
                                <input type="hidden" name="dreamschat_group_id" id="dreamschat_group_id" value="">
                                <div class="form-row">
                                    <div class="form-grade-section">
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
                                                            <input type="checkbox" name="grades[]" value="{{$grade->id}}" class="auto-peer-group-grade-chkbox">{{$grade->name}}
                                                        </div>
                                                    </div>
                                                    @if(!empty($grade->classes))
                                                    <div class="form-grade-sub-option">
                                                        <div class="form-grade-sub-single-option">
                                                            @foreach($grade->classes as $classes)
                                                            <input type="checkbox" name="classes[]" value="{{$classes->id}}" class="auto-peer-group-class-chkbox">
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
                                                <label>{{__('languages.question_generators_menu.select_individual_students')}}</label>
                                            </div>
                                            <div class="student_list_option col-lg-3">
                                                @if(isset($StudentList) && !empty($StudentList))
                                                <select name="studentIds[]" class="form-control select-option" id="auto-peer-group-student-id" multiple>
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
                                                <label>{{__('languages.peer_group.peer_group_type')}}</label>
                                            </div>
                                            <div class="student_peer_group_option col-lg-3">
                                                @if($groupType)
                                                    @foreach($groupType as $peerGroup)
                                                        <div class="form-check form-check-inline">
                                                          <input class="form-check-input" type="radio" name="peer_group_type" id="peer_group_type_{{ $peerGroup['id'] }}" {{ ($peerGroup['id']==0 ? 'checked' : '')  }} value="{{ $peerGroup['id'] }}">
                                                          <label class="form-check-label" for="peer_group_type_{{ $peerGroup['id'] }}">{{ $peerGroup['name'] }}</label>
                                                        </div>
                                                     @endforeach
                                                 @endif
                                            </div>
                                        </div>
                                        <div class="form-group student_peer_group_section mt-3 row">
                                            <div class="student_peer_group_heading col-lg-3">
                                                <label>{{__('languages.peer_group.prefix_group_name')}}</label>
                                            </div>
                                            <div class="col-lg-3">
                                               <input type="text" id="prefix_group_name" name="prefix_group_name" placeholder="Prefix Group Names" class="form-control required" required />
                                            </div>
                                        </div>
                                        <div class="form-group student_peer_group_section mt-3 row">
                                            <div class="student_peer_group_heading col-lg-3">
                                                <label>{{__('languages.peer_group.number_of_group')}}</label>
                                            </div>
                                            <div class="col-lg-3">
                                                <select class="selectpicker form-control" data-show-subtext="true" data-live-search="true" name="no_of_group" id="no_of_group">
                                                    <option value="">{{__('languages.peer_group.select_number_of_group')}}</option>
                                                    @for($i=1;$i<=20;$i++)
                                                        <option value={{$i}}>{{$i}}</option>
                                                    @endfor
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group mt-3 row">
                                            <div class="col-lg-3">
                                                <button type="button" name="submit" value="submit" class="btn-search btn-create-auto-peergroup">{{ __('languages.submit') }}</button>
                                            </div>
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
        <script>
            var ClassIds = [];
            var GradeIds = [];
            $(document).ready(function(){
                $("#auto-peer-group-student-id").multiselect("rebuild");
                $("#peer_group_type").multiselect("rebuild");
            });
            /**
            * USE : On click event click on the grade checkbox
            */
            $(document).on('click', '.auto-peer-group-grade-chkbox', function(){
                if(!$(this).is(":checked")) {
                    $(this).closest('.form-grade-select').find('.auto-peer-group-class-chkbox').prop('checked',false);
                }
                GradeIds = [];
                $('.auto-peer-group-grade-chkbox').each(function(){
                    if($(this).is(":checked")) {
                        $(this).closest('.form-grade-select').find('.auto-peer-group-class-chkbox').prop('checked',true);
                        GradeIds.push($(this).val());
                    }
                });
                ClassIds = [];
                $('.auto-peer-group-class-chkbox').each(function(){
                    if($(this).is(":checked")) {
                        ClassIds.push($(this).val());
                    }
                });

                
                // Function call to get student list
                getStudents(GradeIds,ClassIds);
            });
            function getStudents(gradeIds, classIds){
		    $("#cover-spin").show();
		    $('#auto-peer-group-student-id').html('');
		    if(gradeIds.length==0 && classIds.length==0)
		    {

		        $('#auto-peer-group-student-id').html('');
		        $("#auto-peer-group-student-id").multiselect("rebuild");
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
		                $('#auto-peer-group-student-id').html(response.data);
		                $("#auto-peer-group-student-id").find('option').attr('selected','selected');
		                $("#auto-peer-group-student-id").multiselect("rebuild");
		            }
		        },
		        error: function(response) {
		            ErrorHandlingMessage(response);
		        }
		    });
		    $("#cover-spin").hide();
		}
        /**
        * USE : On click event click on the class checkbox
        */
        $(document).on('click', '.auto-peer-group-class-chkbox', function(){
            var ClassIds = [];
            $('.auto-peer-group-class-chkbox').each(function(){
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
        *   USE : Create Auto Peer Group using Ajax 
        */
        $(document).on('click', '.btn-create-auto-peergroup', function(){

             $('label.error').remove();
            /*$('#createAutoPeerGroupForm').validate({
                rules: {
                    "studentIds[]": {
                        required: true,
                    },
                    peer_group_type:{
                        required: true,
                    },
                    no_of_group: {
                        required: true,
                    },
                },
                messages: {
                    "studentIds[]": {
                        required: "Please Select Student First",
                    },
                    peer_group_type:{
                        required: "Please Select Group Type",
                    },
                    no_of_group: {
                        required: "Please Select Number of Group",
                    },
                },
                submitHandler: function(form) {*/
                    var formIsValid=0;
                    $(document).find('[name="studentIds[]"]').each(function(){
                        var element = $(this).closest('.form-group').css('display');
                        if($.trim($(this).val()) == '' && element != 'none'){
                            var label = $(this).closest('.form-group').find('label:eq(0)').text();
                            $('[name="studentIds[]"]').parent().append('<label class="error">'+VALIDATIONS.PLEASE_SELECT_STUDENTS_OR_PEER_GROUP+' </label>');
                            formIsValid++;
                        }
                    });
                    $(document).find('[name=prefix_group_name],[name=no_of_group]').each(function(){
                        var element = $(this).closest('.form-group').css('display');
                        if($.trim($(this).val()) == '' && element != 'none' ){
                            var label = $(this).closest('.form-group').find('label:eq(0)').text();
                            $(this).parent().append('<label class="error">'+PLEASE_ENTER+label+'</label>');
                            formIsValid++;
                        }
                    });
                    if(formIsValid==0)
                    {
                        $("#cover-spin").show();
                        $.ajax({
                            url: BASE_URL + '/create-auto-peer-group',
                            type: 'POST',
                            data: {
                                _token: $('meta[name="csrf-token"]').attr("content"),
                                formData : $('#createAutoPeerGroupForm').serialize(),
                            },
                            success: function(response) {
                                if(response.data){
                                    if(response.data.length!=0)
                                    {
                                        $.each(response.data, function (k, v) {
                                        console.log(v.id);
                                        var gId=v.id;
                                            var group_name=v.group_name
                                            var GroupAdminData = "";
                                            var currentuser = "";
                                            var searchIDs = [];
                                            var promises = [];

                                            // create group admin start

                                            GroupAdminData = rData={
                                                                alp_chat_user_id:v.group_admin.alp_chat_user_id,
                                                                email:v.group_admin.email,
                                                                mobile_no:v.group_admin.mobile_no,
                                                                name_en:v.group_admin.name_en
                                                                };
                                            GroupAdminUser = addUser(GroupAdminData);
                                            searchIDs.push(GroupAdminUser);

                                            // create group admin end


                                            // add group student start

                                            for (
                                                var gm = 0;
                                                gm < v.student_list.length;
                                                gm++
                                            ) {
                                                var userData = v.student_list[gm];
                                               var rData={
                                                        alp_chat_user_id:userData.alp_chat_user_id,
                                                        email:userData.email,
                                                        mobile_no:userData.mobile_no,
                                                        name_en:userData.name_en,
                                                    };
                                                currentuser = addUser(rData);
                                                searchIDs.push(currentuser);
                                                promises.push(rData);
                                            }

                                            // add group student end

                                            $.when.apply(null, promises).done(function () {
                                                var new_group_title = group_name;
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
                                                    setTimeout(function () {
                                                        addGroup(Gdata);
                                                        var group_id=$("#dreamschat_group_id").val();
                                                        console.log(gId);
                                                        $.ajax({
                                                            url: BASE_URL + '/update-group-id-auto-peer-group/'+gId+'/'+group_id,
                                                            type: 'GET',
                                                            success: function(response) {
                                                                
                                                            }
                                                        });
                                                    },500)
                                                }
                                                
                                            });
                                        });
                                    }
                                   setTimeout(function () {
                                    window.location.replace(BASE_URL+'/\peer-group');
                                    $("#cover-spin").hide();
                                    toastr.success(response.message);
                                   },response.data.length*1000) 
                                   
                                }
                            },
                            error: function(response) {
                                ErrorHandlingMessage(response);
                            }
                        });
                    }
                //}
            //});
    });
        </script>
@endsection