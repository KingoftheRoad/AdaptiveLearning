@extends('backend.layouts.app')
    @section('content')
	@php
	$permissions = [];
	$user_id = auth()->user()->id;
	if($user_id){
		$module_permission = App\Helpers\Helper::getPermissions($user_id);
		if($module_permission && !empty($module_permission)){
			$permissions = $module_permission;
		}
	}else{
		$permissions = [];
	}
	@endphp
    <div class="wrapper d-flex align-items-stretch sm-deskbord-main-sec">
        @include('backend.layouts.sidebar')
	      <div id="content" class="pl-2 pb-5">
            @include('backend.layouts.header')
			<div class="sm-right-detail-sec pl-5 pr-5">
				<div class="container-fluid">
					<div class="col-md-12">
						<div class="sec-title">
							<h2 class="mb-4 main-title">{{__('languages.test.test_detail')}}</h2>
							<div class="btn-sec">
								<a href="javascript:void(0);" class="btn-back dark-blue-btn btn btn-primary mb-4" id="backButton">{{__('languages.back')}}</a>
								@if(in_array('exam_management_create', $permissions))
									{{-- <a href="{{ route('school.generate-questions') }}" class="btn-search white-font">{{__('languages.question_generators')}}</a> --}}
									<a href="{{ route('school.generate-questions') }}" class="btn-back dark-blue-btn btn btn-primary mb-4">{{__('languages.question_generators')}}</a>
								@endif
							</div>
						</div>
						<hr class="blue-line">
					</div>
					@if (session('error'))
					<div class="alert alert-danger">{{ session('error') }}</div>
					@endif
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
					<form class="addAdminExamFilterForm" id="addAdminExamFilterForm" method="get">	
						<div class="row">
							<div class="select-lng pt-5 pb-2 col-lg-2 col-md-4">                            
								<select name="current_curriculum_year"  id="current_curriculum_year" class="form-control select-option exam-search">
									<option value="">{{ __('languages.current') }} {{ __('languages.curriculum_year') }}</option>
									@if(!empty($CurriculumYears))
										@foreach($CurriculumYears as $CurriculumYear)
										<option value="{{$CurriculumYear['id']}}" {{ request()->get('current_curriculum_year') == $CurriculumYear['id'] ? 'selected' : '' }}>{{ $CurriculumYear['year']}}</option>
										@endforeach
									@endif
								</select>
								@if($errors->has('current_curriculum_year'))
									<span class="validation_error">{{ $errors->first('current_curriculum_year') }}</span>
								@endif
							</div>
							<div class="select-lng pt-2 pb-2 col-lg-2 col-md-4">                            
								<select name="test_type"  class="form-control select-option exam-search">
									<option value="">{{ __('languages.test.select_test_type') }}</option>
									@if(!empty($examTypes))
										@foreach($examTypes as $examType)
										<option value="{{$examType['id']}}" {{ request()->get('test_type') == $examType['id'] ? 'selected' : '' }}>{{ $examType['name']}}</option>
										@endforeach
									@endif
								</select>
								@if($errors->has('test_type'))
									<span class="validation_error">{{ $errors->first('test_type') }}</span>
								@endif
							</div>

							<div class="col-lg-2 col-md-3">
								<div class="select-lng pt-2 pb-2">
									<input type="text" class="input-search-box mr-2 exam-search" name="title" value="{{request()->get('title')}}" placeholder="{{__('languages.search_by_test_title')}}">
									@if($errors->has('title'))
										<span class="validation_error">{{ $errors->first('title') }}</span>
									@endif
								</div>
							</div>

							<div class="col-lg-2 col-md-4">
								<div class="select-lng pt-2 pb-2">
									<select class="form-control exam-search" name="status">
										<option value=''>{{ __('languages.test.select_status') }}</option>
										@if(!empty($statusLists))
											@foreach($statusLists as $statusList)
											<option value="{{$statusList['id']}}" {{ request()->get('status') == $statusList['id'] ? 'selected' : '' }}>{{ $statusList['name']}}</option>
											@endforeach
										@endif
									</select>
									@if($errors->has('status'))
										<span class="validation_error">{{ $errors->first('status') }}</span>
									@endif
								</div>
							</div>

							<div class="col-lg-2 col-md-4">
								<div class="select-lng pt-2 pb-2">
									<label for="id_end_time">{{ __('languages.test.from_date') }}</label>
									<div class="test-list-clandr">
										<input type="text" class="form-control from-date-picker" name="from_date" value="{{ (request()->get('from_date')) }}" placeholder="{{__('languages.select_date')}}" autocomplete="off">
										<div class="input-group-addon input-group-append">
											<div class="input-group-text">
												<i class="glyphicon glyphicon-calendar fa fa-calendar"></i>
											</div>
										</div>
									</div>
								</div>
								<span id="from-date-error"></span>
								@if($errors->has('from_date'))<span class="validation_error">{{ $errors->first('from_date') }}</span>@endif
							</div>

							<div class="col-lg-2 col-md-4">
								<div class="select-lng pt-2 pb-2">
									<label for="id_end_time">{{ __('languages.test.to_date') }}</label>
									<div class="test-list-clandr">
										<input type="text" class="form-control to-date-picker" name="to_date" value="{{ (request()->get('to_date'))}}" placeholder="{{__('languages.select_date')}}" autocomplete="off">
										<div class="input-group-addon input-group-append">
											<div class="input-group-text">
												<i class="glyphicon glyphicon-calendar fa fa-calendar"></i>
											</div>
										</div>
									</div>
								</div>
								<span id="from-date-error"></span>
								@if($errors->has('to_date'))<span class="validation_error">{{ $errors->first('to_date') }}</span>@endif
							</div>
							<div class="col-lg-2 col-md-3">
								<div class="select-lng pt-2 pb-2">
									<button type="submit" name="filter" value="filter" class="btn-search exam-search">{{ __('languages.test.search') }}</button>
								</div>
							</div>
						</div>
					</form>
					<div class="row">
						<div class="col-lg-12 col-md-12 col-sm-12">
							<!-- <div id="DataTable" class="question-bank-sec"> -->
							<div class="question-bank-sec test-list-mains restrict-overflow">
								<table class="exam-list-table display" style="width:100%">
							    	<thead>
							        	<tr>
							          		<th>
										  		<input type="checkbox" name="" class="checkbox" id="group-exam-ids">
											</th>
											<th>@sortablelink('curriculum_year_id',__('languages.curriculum_year'))</th>
											<th>@sortablelink('exam_type',__('languages.test.test_type'))</th>
											<th>@sortablelink('reference_no',__('languages.reference_number'))</th>
							          		<th>@sortablelink('title',__('languages.test.title'))</th>
											<th>@sortablelink('from_date',__('languages.start_date_time'))</th>
											<th>@sortablelink('to_date',__('languages.end_date_time'))</th>
											<th>@sortablelink('result_date',__('languages.test.result_date'))</th>
											<th>{{__('languages.created_user')}}</th>
											<th>@sortablelink('status',__('languages.status'))</th>
											<th>{{__('languages.test.update_status')}}</th>
											<th>{{__('languages.action')}}</th>
							        	</tr>
							    	</thead>
							    	<tbody class="scroll-pane">
                                        @if(!empty($examList))
										@foreach($examList as $exam)
										@if(!empty($exam->ExamSchoolMapping[0]))
											<tr>
												<td><input type="checkbox" name="examids" class="checkbox exam-id" value="{{$exam->id}}"></td>
												<td>{{($exam->getCurrentCurriculumYear($exam->curriculum_year_id)) ?? ''}}</td>
												<td>
													@if($exam->exam_type ==1)
														{{__('languages.self_learning')}}
													@elseif($exam->exam_type ==2) 
														{{__('languages.exercise')}}
													@elseif($exam->exam_type ==3) 
														{{__('languages.test_text')}} 
													@else 
														{{__('N/A')}}  
													@endif
												</td>
												<td>{{$exam->reference_no}}</td>
												<td>{{$exam->title}}</td>
												<td>{{date('d/m/Y',strtotime($exam->from_date))}} {{!empty($exam->start_time) ? $exam->start_time.':00' : '00:00:00'}}</td>
												<td>{{date('d/m/Y',strtotime($exam->to_date))}} {{!empty($exam->end_time) ? $exam->end_time.':00' : '00:00:00'}}
													@if($exam->created_by==Auth::user()->id)
														<span class="badge badge-info end_date_pointer change_end_date_of_exam" data-examid="{{$exam->id}}" 
															examEndDate="{{date('d/m/Y',strtotime($exam->to_date))}}" refrence_no="{{$exam->reference_no}}"
															title="{{$exam->title}}" dateType="EndDate"
															>{{__('languages.change_exam_date')}}
														</span>
													@endif
												</td>
												{{-- <td>{{!empty($exam->result_date) ? date('d/m/Y',strtotime($exam->result_date)) : 'After Submit'}}</td> --}}
												<td>
													{{ date('d/m/Y',strtotime($exam->result_date))}}
													@if($exam->created_by==Auth::user()->id)
														<span class="badge badge-info end_date_pointer change_end_date_of_exam" data-examid="{{$exam->id}}" 
															examResultDate="{{date('d/m/Y',strtotime($exam->result_date))}}" refrence_no="{{$exam->reference_no}}"
															title="{{$exam->title}}" dateType="ResultDate"
															>{{__('languages.change_exam_date')}}
														</span>
													@endif
												</td>
												<td>
													{{ ucwords(str_replace('_',' ',$exam->created_by_user)) }}
												</td>
												<td class="exams_status_badge_{{$exam->id}}">
													@if($exam->ExamSchoolMapping[0]['status'] == 'active')
														<span class="badge badge-success">{{__('languages.active')}}</span>
													@elseif($exam->ExamSchoolMapping[0]['status'] == 'draft')
														<span class="badge badge-warning">{{__('languages.draft')}}</span>
													@elseif($exam->ExamSchoolMapping[0]['status'] == 'complete')
														<span class="badge badge-info">{{__('languages.complete')}}</span>
													@elseif($exam->ExamSchoolMapping[0]['status'] == 'publish')
														<span class="badge badge-success">{{__('languages.publish')}}</span>
													@else
														<span class="badge badge-danger">{{__('languages.inactive')}}</span>
													@endif
												</td>
												<td>
													@if($exam->created_by_user == 'teacher')
													{{-- <select name="exam_status" id="update_exam_status" class="update_exam_status" data-examid="{{$exam->id}}" {{ $exam->ExamSchoolMapping[0]['status'] == 'inactive' ? 'disabled' : ''}} {{ $exam->exam_type == 1 ? 'disabled' : ''}} > --}}
														<select name="exam_status" id="update_exam_status" class="update_exam_status" data-examid="{{$exam->id}}"  {{ $exam->exam_type == 1 ? 'disabled' : ''}} >
														<option value="">{{__('languages.select_status')}}</option>
														<option value="publish" {{$exam->ExamSchoolMapping[0]['status'] == 'publish' ? 'selected' : ''}}>{{__('languages.publish')}}</option>
														<option value="inactive" {{$exam->ExamSchoolMapping[0]['status'] == 'inactive' ? 'selected' : ''}}>{{__('languages.inactive')}}</option>
													</select>
													@endif
												</td>
												<td class="edit-class">
												@if($exam->exam_type !=1)
													@if(in_array('exam_management_update', $permissions))
														@if($exam->ExamSchoolMapping[0]['status'] == 'draft' && ($exam->created_by_user == 'teacher'))
														<a href="{{ route('school.generate-questions-edit', $exam->id) }}" class="btn-edit" title="{{__('languages.edit')}}">
															<i class="fa fa-pencil" aria-hidden="true"></i>
														</a>
														@endif
													@endif
													<!-- @if (in_array('exam_management_delete', $permissions))
														<a href="javascript:void(0);" class="pl-2 btn-delete" id="deleteExam" data-id="{{$exam->id}}" title="{{__('languages.delete')}}">
															<i class="fa fa-trash" aria-hidden="true"></i>
														</a>
													@endif -->
												@endif

												<!-- If Exams Is Publish then display view student result icon -->
												@if($exam->ExamSchoolMapping[0]['status'] == 'publish')
													@if($exam->created_by_user == 'teacher')
													<span>
														<i class="fa fa-user add-peer-group" aria-hidden="true" title="{{__('languages.add_students')}}" data-id={{$exam->id}}></i>
													</span>
													@endif
													
													@if(in_array('result_management_update', $permissions))
													<a href="{{ route('getListAttemptedExamsStudents', $exam->id) }}" data-toggle="tooltip" title="{{__('languages.performance_report')}}" class="pl-2">
														<i class="fa fa-eye" aria-hidden="true"></i>
													</a>
													@endif

													@php
													if($exam->exam_type==1){
														$previewUrl = route('self_learning.preview',$exam->id);
													}else{
														$previewUrl = route('exam-configuration-preview',$exam->id);
													}
													@endphp

													@if($exam->created_by_user == 'super_admin' || $exam->created_by_user == 'school_admin' || $exam->created_by_user == 'principal' || $exam->created_by_user == "teacher")
													<a href="{{$previewUrl}}" class="pl-2 btn-delete" id="configExam" data-id="{{$exam->id}}" title="{{__('languages.configure')}}">
														<i class="fa fa-gear" aria-hidden="true"></i>
													</a>
													@endif
												@endif										
												</td>
											</tr>
										@endif
										@endforeach
										@endif
									</tbody>
								</table>
								<div>{{__('languages.showing')}} {{!empty($examList->firstItem()) ? $examList->firstItem() : 0}} {{__('languages.to')}} {{!empty($examList->lastItem()) ? $examList->lastItem() : 0}}
									{{__('languages.of')}}  {{$examList->total()}} {{__('languages.entries')}}
								</div>
								<div class="pagination-data">
									<div class="col-lg-9 col-md-9 pagintn">
										{{$examList->appends(request()->input())->links()}}
									</div>
									<div class="col-lg-3 col-md-3 pagintns">
										<form>
											<label for="pagination">{{__('languages.test.per_page')}}</label>
											<select id="pagination" >
												<option value="10" @if(app('request')->input('items') == 10) selected @endif >10</option>
												<option value="20" @if(app('request')->input('items') == 20) selected @endif >20</option>
												<option value="25" @if(app('request')->input('items') == 25) selected @endif >25</option>
												<option value="30" @if(app('request')->input('items') == 30) selected @endif >30</option>
												<option value="40" @if(app('request')->input('items') == 40) selected @endif >40</option>
												<option value="50" @if(app('request')->input('items') == 50) selected @endif >50</option>
												<option value="{{$examList->total()}}" @if(app('request')->input('items') == $examList->total()) selected @endif >{{__('languages.all')}}</option>
											</select>
										</form>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
	      </div>
		</div>

		{{-- Start Modal of Select Student and Group --}}
		<div class="modal fade" id="addStudentsInExam" tabindex="-1" role="dialog" aria-labelledby="nodeModalLabel" aria-hidden="true" data-backdrop="static">
			<div class="modal-dialog modal-lg" role="document">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title">{{__('languages.question_generators_menu.add_student_or_group')}}</h5>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button>
					</div>
					<div class="modal-body assignStudentData">
						
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-primary saveStudents">{{__('languages.submit')}}</button>
						<button type="button" class="btn btn-secondary closeaddStudentsInExamPopup" data-dismiss="modal">{{__('languages.close')}}</button>
					</div>
				</div>
			</div>
		</div>
		{{-- End Modal of Select Student and Group --}}
		<script>
			document.getElementById('pagination').onchange = function() {
				window.location = "{!! $examList->url(1) !!}&items=" + this.value;
			};

			$(document).ready(function () {
				//Update End Date of Exam
				$("#changeExamEndDateForm").validate({
					rules: {
						to_date: {
							required: true,
						},
					},
					messages: {
						to_date: {
							required: VALIDATIONS.PLEASE_SELECT_END_OF_EXAM_DATE,
						},
					},
					errorPlacement: function (error, element) {
						if (element.attr("name") == "to_date") {
							error.appendTo("#toDate-error");
						}
					},
				});
				$(document).on('click','.add-peer-group',function(){
					
					$(".assignStudentData").html('');
					var examid = $(this).data("id");
					$("#cover-spin").show();
					$.ajax({
						url: BASE_URL + "/get-late-commerce-student-list",
						type: "GET",
						data: {
							examid: examid,
						},
						success: function (response) {
							$("#cover-spin").hide();
							var data = JSON.parse(JSON.stringify(response));
							$(".assignStudentData").append(data.data);
							$(".student-grade-class-section .form-grade-select").each(function(){
								if($(this).find('.question-generator-class-chkbox').length==0)
								{
									$(this).remove();
								}
							});
							$("#question-generator-peer-group-options").multiselect("rebuild");
							$("#addStudentsInExam").modal("show");
						},
						error: function (response) {
                        	ErrorHandlingMessage(response);
                    	},
                	});
				});	

				/**
				 * USE : Add late commerce students or peer groups
				 */
				$(document).on('click','.saveStudents',function(){
					var examid = $("#examid").val();
					if($(document).find(".student-grade-class-section").length != 0){
						var studentId = $("#question-generator-student-id").val();
						if(studentId.length != 0){
							studentId = studentId.filter(function (el) {
							  return el !== '';
							});
						}
						if($(".question-generator-class-chkbox:checked").length == 0){
							toastr.error("Please Select  Grade and Classes");
							return false;
						}
						if(studentId.length == 0){
							toastr.error(VALIDATIONS.PLEASE_SELECT_STUDENT);
							return false;
						}
						$("#cover-spin").show();
						var formData=$( "#add-exma-student-in-grade-class" ).serialize();
						$.ajax({
							url: BASE_URL + "/add-test-late-commerce-student-peer-group",
							type: "POST",
							data: formData,
							success: function (response) {
								$("#cover-spin").hide();
								var data = JSON.parse(JSON.stringify(response));
								if(data.status === "success") {
	                            	toastr.success(data.message);
								}else{
									toastr.error(data.message);
								}
								$("#addStudentsInExam").modal("hide");
								$('.assignStudentData').html('');
							},
							error: function (response) {
	                        	ErrorHandlingMessage(response);
	                    	},
	                	});
					}else{
						var groupIds = $('#question-generator-peer-group-options').val();
						if(groupIds[0]==''){
							toastr.error(VALIDATIONS.PLEASE_SELECT_GROUP);
							$('.assignStudentData').html('');
							return false;
						}
						if(groupIds.length ==0){
							toastr.error(VALIDATIONS.PLEASE_SELECT_GROUP);
							$('.assignStudentData').html('');
							return false;
						}
						$("#cover-spin").show();
						var formData = $("#add-exma-student-in-grade-class" ).serialize();
						$.ajax({
							url: BASE_URL + "/add-test-late-commerce-student-peer-group",
							type: "POST",
							data: formData,
							success: function (response) {
								$("#cover-spin").hide();
								var data = JSON.parse(JSON.stringify(response));
								if(data.status === "success"){
	                            	toastr.success(data.message);
								}else{
									toastr.error(data.message);
								}
								$("#addStudentsInExam").modal("hide");
								$('.assignStudentData').html('');
							},
							error: function (response) {
	                        	ErrorHandlingMessage(response);
	                    	},
	                	});
					}
				});

				$(document).on('click','.closeaddStudentsInExamPopup,.close',function(){
					$(".assignStudentData").html('');
				})

				/**
				 * USE : Update Exam status 'publish' or 'inactive'
				 * **/
				$(document).on("change", ".update_exam_status", function () {
					$("#cover-spin").show();
					if (this.value != "") {
						$examId = $(this).attr("data-examid");
						$status = this.value;
						$.ajax({
							url: BASE_URL + "/exam/status/update",
							type: "POST",
							data: {
								_token: $('meta[name="csrf-token"]').attr("content"),
								exam_id: $examId,
								status: $status,
							},
							success: function (response) {
								$("#cover-spin").hide();
								var data = JSON.parse(JSON.stringify(response));
								if(data.status === "success"){
									toastr.success(data.message);
									// Update status badge html for selecting based on options
									if ($status == "publish") {
										$(".exams_status_badge_" + $examId).html('<span class="badge badge-success">' +$status.toLowerCase().replace(/\b[a-z]/g,function (letter) {return letter.toUpperCase();}) +"</span>");
									}else{
										$(".exams_status_badge_" + $examId).html('<span class="badge badge-danger">' +$status.toLowerCase().replace(/\b[a-z]/g,function (letter) {return letter.toUpperCase();}) +"</span>");
									}
									location.reload();
								}else{
									toastr.error(data.message);
								}
							},
							error: function (response) {
								ErrorHandlingMessage(response);
							},
						});
					}else{
						$("#cover-spin").hide();
					}
				});
			});
		</script>
		<script>
		document.getElementById('pagination').onchange = function() {
			window.location = "{!! $examList->url(1) !!}&items=" + this.value;
		};
		$(document).ready(function () {
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
				        setGradeClassDateTimeList();
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
				        setGradeClassDateTimeList();
				    });
				    $(document).on('change',".grade-class-date-time-list .start_time,.group-date-time-list .start_time",function () {
			            var selectedStartTimeIndex = this.selectedIndex;
			            var selectedEndTimeIndex=$('#test_end_time option[value="'+$('#test_end_time').val()+'"]').index();
			            $(this).closest('.form-row').find(".end_time option").each(function(){
			                var endOptionSelectedStartTimeIndex = $(this).index();
			                if(endOptionSelectedStartTimeIndex <= selectedStartTimeIndex){
			                    $(this).attr("disabled", "disabled");
			                }
			                else if((endOptionSelectedStartTimeIndex >= selectedEndTimeIndex ) && selectedEndTimeIndex>0)
			                {
			                    $(this).attr("disabled", "disabled");
			                }
			                else{
			                    $(this).removeAttr("disabled");
			                }
			            });
			        });

			        $(document).on('change',".grade-class-date-time-list .end_time,.group-date-time-list .end_time",function () {
			            var selectedStartTimeIndex = this.selectedIndex;
			            var selectedEndTimeIndex=$('#test_start_time option[value="'+$('#test_start_time').val()+'"]').index();
			            $(this).closest('.form-row').find(".start_time option").each(function(){
			                var endOptionSelectedStartTimeIndex = $(this).index();
			                if(endOptionSelectedStartTimeIndex >= selectedStartTimeIndex){
			                    $(this).attr("disabled", "disabled");
			                }
			                else if(endOptionSelectedStartTimeIndex < selectedEndTimeIndex)
			                {
			                    $(this).attr("disabled", "disabled");
			                }
			                else{
			                    $(this).removeAttr("disabled");
			                }
			            });
			        });
			        $(document).on('change','#question-generator-peer-group-options',function () {
			            setGroupDateTimeList();
			        });
			});
		 /**
		 * USE : Get the student list based on select grades and classes
		 * Trigger : on select the grades and class
		 * Return data : All the student list based on select grade and classes
		 */
		function getStudents(gradeIds, classIds){
		    $("#cover-spin").show();
		    $('#question-generator-student-id').html('');
		    if(gradeIds.length==0 && classIds.length==0)
		    {

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
		                if($(".assignStudentData #oldStudentList").val()!="")
		                {
		                	var oldStudentList=$(".assignStudentData #oldStudentList").val();
		                	oldStudentList=oldStudentList.split(',');
		                	$.each(oldStudentList,function(key,studentId){
		                		$("#question-generator-student-id").find('option[value='+studentId+']').prop('disabled',true);
		                	});
		                }
		                $("#question-generator-student-id").multiselect("rebuild");
		            }
		        },
		        error: function(response) {
		            ErrorHandlingMessage(response);
		        }
		    });
		    $("#cover-spin").hide();
		}
		function setGradeClassDateTimeList() {
	        $(".grade-class-date-time-list").html('');
	        var testStartTimeHtml=$('#test_start_time').html();
	        var testEndTimeHtml=$('#test_end_time').html();
	        
	        if($('.question-generator-grade-chkbox:checked').length==0)
	        {
	            $('#question-generator-student-id').prop('disabled',true);
	            $("#question-generator-student-id").multiselect('disable');
	        }
	        var htmlData='';
	        $('.question-generator-grade-chkbox').each(function(){
	            var generatorValue=$(this).val();
	            if($(this).is(":checked")) {
	                var generatorClassChkboxLength=$(this).closest('.form-grade-select').find('.question-generator-class-chkbox:checked').length;
	                var generatorClassChkboxAllLength=$(this).closest('.form-grade-select').find('.question-generator-class-chkbox').length;
	                if(generatorClassChkboxLength==0)
	                {
	                    $(this).closest('.form-grade-select').find('.question-generator-class-chkbox').each(function(){
	                        var generatorClassValue=$(this).val();
	                        htmlData+=dateTimeList($(this),generatorValue,generatorClassValue,testStartTimeHtml,testEndTimeHtml);
	                    });

	                }
	                else
	                {
	                    $(this).closest('.form-grade-select').find('.question-generator-class-chkbox:checked').each(function(){
	                        var generatorClassValue=$(this).val();
	                        htmlData+=dateTimeList($(this),generatorValue,generatorClassValue,testStartTimeHtml,testEndTimeHtml);
	                    });
	                }
	            }
	            else
	            {
	                $(this).closest('.form-grade-select').find('.question-generator-class-chkbox:checked').each(function(){
	                        var generatorClassValue=$(this).val();
	                        htmlData+=dateTimeList($(this),generatorValue,generatorClassValue,testStartTimeHtml,testEndTimeHtml);
	                    });
	            }
	        });
	        if(htmlData=='')
	        {
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
	            }
	            else if((endOptionSelectedStartTimeIndex > selectedEndTimeIndex) && selectedEndTimeIndex>0)
	            {
	                $(this).attr("disabled", "disabled");
	            }
	            else{
	                $(this).removeAttr("disabled");
	            }
	        });
	        $(".grade-class-date-time-list .start_time option").each(function(){
	            var endOptionSelectedStartTimeIndex = $(this).index();
	            if(endOptionSelectedStartTimeIndex < selectedStartTimeIndex){
	                $(this).attr("disabled", "disabled");
	            }
	            else if((endOptionSelectedStartTimeIndex > selectedEndTimeIndex) && selectedEndTimeIndex>0)
	            {
	                $(this).attr("disabled", "disabled");
	            }
	            else{
	                $(this).removeAttr("disabled");
	            }
	        });
	        $(".grade-class-date-time-list .start_time").val($('#test_start_time').val());
	        $(".grade-class-date-time-list .end_time").val($('#test_end_time').val());

	        //$('#test_end_time').val('').select2().trigger('change');
	    }
	    function dateTimeList(E,generatorValue,generatorClassValue,testStartTimeHtml,testEndTimeHtml)
	    {
	        var mainStartDate=$("input[name=start_date]").val();
	        var mainEndDate=$("input[name=end_date]").val();
	        dataHtmlData='<div class="row"><div class="col-md-1"><label>'+E.attr('data-label')+'</label></div><div class="col-md-11"><div class="form-row">\
	            <div class="form-group col-md-3 mb-50">\
	                <label>{{ __('languages.question_generators_menu.start_date') }}</label>\
	                <div class="input-group date">\
	                    <input type="text" class="form-control date-picker-stud startDate" id="generatorClassValue_'+generatorClassValue+'" name="generator_class_start_date['+generatorValue+']['+generatorClassValue+']" value="'+mainStartDate+'" placeholder="{{__('languages.question_generators_menu.start_date')}}" autocomplete="off" disabled>\
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
	                    <select name="generator_class_start_time['+generatorValue+']['+generatorClassValue+']" class="form-control select-option start_time" disabled>'+testStartTimeHtml+'</select>\
	                </div>\
	            </div>\
	            <div class="form-group col-md-3 mb-50">\
	                <label>{{ __('languages.question_generators_menu.end_date') }}</label>\
	                <div class="input-group date">\
	                    <input type="text" class="form-control date-picker-stud endDate" name="generator_class_end_date['+generatorValue+']['+generatorClassValue+']" value="'+mainEndDate+'" placeholder="{{__('languages.question_generators_menu.end_date')}}" autocomplete="off" disabled>\
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
	                    <select name="generator_class_end_time['+generatorValue+']['+generatorClassValue+']" class="form-control select-option end_time" disabled>'+testEndTimeHtml+'</select>\
	                </div>\
	            </div>\
	        </div></div><div class="col-md-12"><hr></div></div>';
	        return dataHtmlData;
	    }
	    // Group data time
	    function setGroupDateTimeList() {
	        $(".grade-class-date-time-list").html('');
	        $(".group-date-time-list").html('');
	        var testStartTimeHtml=$('#test_start_time').html();
	        var testEndTimeHtml=$('#test_end_time').html();
	        
	        var htmlData='';
	       
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
	            }
	            else if((endOptionSelectedStartTimeIndex > selectedEndTimeIndex) && selectedEndTimeIndex>0)
	            {
	                $(this).attr("disabled", "disabled");
	            }
	            else{
	                $(this).removeAttr("disabled");
	            }
	        });
	        $(".group-date-time-list .start_time option").each(function(){
	            var endOptionSelectedStartTimeIndex = $(this).index();
	            if(endOptionSelectedStartTimeIndex < selectedStartTimeIndex){
	                $(this).attr("disabled", "disabled");
	            }
	            else if((endOptionSelectedStartTimeIndex > selectedEndTimeIndex) && selectedEndTimeIndex>0)
	            {
	                $(this).attr("disabled", "disabled");
	            }
	            else{
	                $(this).removeAttr("disabled");
	            }
	        });
	        $(".group-date-time-list .start_time").val($('#test_start_time').val());
	        $(".group-date-time-list .end_time").val($('#test_end_time').val());

	        //$('#test_end_time').val('').select2().trigger('change');
	    }
	    //generator Group date and time html
	    function groupDateTimeList(E,generatorGroupValue,testStartTimeHtml,testEndTimeHtml)
	    {
	        var mainStartDate=$("input[name=start_date]").val();
	        var mainEndDate=$("input[name=end_date]").val();
	        dataHtmlData='<div class="row"><div class="col-md-1"><label>'+E.attr('data-label')+'</label></div><div class="col-md-11"><div class="form-row">\
	            <div class="form-group col-md-3 mb-50">\
	                <label>{{ __('languages.question_generators_menu.start_date') }}</label>\
	                <div class="input-group date">\
	                    <input type="text" class="form-control date-picker-stud startDate" id="generatorClassValue_'+generatorGroupValue+'" name="generator_group_start_date['+generatorGroupValue+']" value="'+mainStartDate+'" placeholder="{{__('languages.question_generators_menu.start_date')}}" autocomplete="off" disabled>\
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
	                    <select name="generator_group_start_time['+generatorGroupValue+']" class="form-control select-option start_time" disabled>'+testStartTimeHtml+'</select>\
	                </div>\
	            </div>\
	            <div class="form-group col-md-3 mb-50">\
	                <label>{{ __('languages.question_generators_menu.end_date') }}</label>\
	                <div class="input-group date">\
	                    <input type="text" class="form-control date-picker-stud endDate" name="generator_group_end_date['+generatorGroupValue+']" value="'+mainEndDate+'" placeholder="{{__('languages.question_generators_menu.end_date')}}" autocomplete="off" disabled>\
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
	                    <select name="generator_group_end_time['+generatorGroupValue+']" class="form-control select-option end_time" disabled>'+testEndTimeHtml+'</select>\
	                </div>\
	            </div>\
	        </div></div><div class="col-md-12"><hr></div></div>';
	        return dataHtmlData;
	    }
		</script>
		@include('backend.layouts.footer')

@endsection