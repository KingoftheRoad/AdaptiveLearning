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
					<div class="row">
						<div class="col-md-12">
							<div class="sec-title">
								<h2 class="mb-4 main-title">{{__('languages.test.test_detail')}}</h2>
								<div class="btn-sec">
									<a href="javascript:void(0);" class="btn-back dark-blue-btn btn btn-primary mb-4" id="backButton">{{__('languages.back')}}</a>							

									<!-- Start for Create new Test/Exercise -->
									@if(in_array('exam_management_create', $permissions) && App\Helpers\Helper::isAdmin())
									<a href="{{ route('super-admin.generate-questions') }}" class="dark-blue-btn btn btn-primary mb-4">{{__('languages.question_generators')}}</a>
									@endif
									<!-- End for Create new Test/Exercise -->

									<!-- Start for Question wizard for proofed reading -->
									<a href="{{ route('question-wizard.proof-reading-question') }}" class="dark-blue-btn btn btn-primary mb-4">{{__('languages.inspect_mode')}}</a>
									<!-- For Question wizard for proofed reading -->
								</div>
							</div>
							<hr class="blue-line">
						</div>
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
							<div class="select-lng pt-2 pb-2 col-lg-2 col-md-4">                            
								<select name="current_curriculum_year"  id="current_curriculum_year" class="form-control select-option curriculum-select-option exam-search">
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
							<div class="question-bank-sec test-list-mains restrict-overflow">
								<table class="exam-list-table display table-responsive" style="width:100%">
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
                                            <!-- <th>@sortablelink('time_duration',__('languages.time_duration_hh_mm_ss'))</th> -->
											<th>{{__('languages.created_user')}}</th>
											<th>@sortablelink('status',__('languages.status'))</th>
											<th>{{__('languages.test.update_status')}}</th>											
											<th>{{__('languages.action')}}</th>
							        	</tr>
							    	</thead>
							    	<tbody class="scroll-pane">
                                        @if(!empty($examList))
										@foreach($examList as $exam)
										<tr>
											<td>
												<input type="checkbox" name="examids" class="checkbox exam-id" value="{{$exam->id}}">
											</td>
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
												<span class="badge badge-info end_date_pointer change_end_date_of_exam" data-examid="{{$exam->id}}" 
													examEndDate="{{date('d/m/Y',strtotime($exam->to_date))}}" refrence_no="{{$exam->reference_no}}"
													title="{{$exam->title}}" dateType="EndDate"
													>{{__('languages.change_exam_date')}}
												</span>
											</td>
                                            {{-- <td>
												{{!empty($exam->result_date) ? date('d/m/Y',strtotime($exam->result_date)) : 'After Submit'}}
											</td> --}}
											<td>
												{{ date('d/m/Y',strtotime($exam->result_date))}}
												<span class="badge badge-info end_date_pointer change_end_date_of_exam" data-examid="{{$exam->id}}" 
													examResultDate="{{date('d/m/Y',strtotime($exam->result_date))}}" refrence_no="{{$exam->reference_no}}"
													title="{{$exam->title}}" dateType="ResultDate"
													>{{__('languages.change_exam_date')}}
												</span>
											</td>
                                            <!-- <td>{{ App\Helpers\Helper::secondToTime($exam->time_duration) ?? 'Unlimited Time'}}</td> -->
											<td>
												{{ ucwords(str_replace('_',' ',$exam->created_by_user)) }}
											</td>
											<td class="exams_status_badge_{{$exam->id}}">
												@if($exam->created_by_user == 'teacher' || $exam->created_by_user == 'school_admin' || $exam->created_by_user == 'principal')
													@php
														if(isset($exam->ExamSchoolMapping[0])){
															$ExamStatus=$exam->ExamSchoolMapping[0]->status;
														}else{
															$ExamStatus = $exam->status;
														}
													@endphp
												@else
													<?php $ExamStatus = $exam->status; ?>
												@endif
												@if($ExamStatus == 'active')
													<span class="badge badge-success">{{__('languages.active')}}</span>
												@elseif($ExamStatus == 'draft')
													<span class="badge badge-warning">{{__('languages.draft')}}</span>
												@elseif($ExamStatus == 'complete')
													<span class="badge badge-info">{{__('languages.complete')}}</span>
												@elseif($ExamStatus == 'publish')
													<span class="badge badge-success">{{__('languages.publish')}}</span>
												@else
													<span class="badge badge-danger">{{__('languages.inactive')}}</span>
												@endif
											</td>
											<td>
												@if($exam->created_by == Auth::user()->id)
												{{-- <select name="exam_status" id="update_exam_status" class="update_exam_status" data-examid="{{$exam->id}}" {{ $exam->status == 'inactive' ? 'disabled' : ''}} {{ $exam->exam_type == 1 ? 'disabled' : ''}} > --}}
													<select name="exam_status" id="update_exam_status" class="update_exam_status" data-examid="{{$exam->id}}"  {{ $exam->exam_type == 1 ? 'disabled' : ''}} >
													<option value="">{{__('languages.select_status')}}</option>
													<option value="publish" {{$exam->status == 'publish' ? 'selected' : ''}}>{{__('languages.publish')}}</option>
													<option value="inactive" {{$exam->status == 'inactive' ? 'selected' : ''}}>{{__('languages.inactive')}}</option>
												</select>
												@endif
											</td>
                                            <td class="edit-class">
											@if($exam->exam_type !=1)
												@if($exam->status != 'publish')
													@if($exam->status == 'draft' && $exam->created_by == Auth::user()->id)
														@if(in_array('exam_management_update', $permissions))
														<a href="{{ route('super-admin.generate-questions-edit', $exam->id) }}" class="btn-edit" title="{{__('languages.edit_test_details')}}">
															<i class="fa fa-pencil" aria-hidden="true"></i>
														</a>
														@endif
													@endif

													<!-- @if (in_array('exam_management_delete', $permissions))
														@if($exam->created_by == Auth::user()->id)
														<a href="javascript:void(0);" class="pl-2 btn-delete" id="deleteExam" data-id="{{$exam->id}}" title="{{__('languages.delete_test')}}">
															<i class="fa fa-trash" aria-hidden="true"></i>
														</a>
														@endif
													@endif -->												
												@endif
											@endif

											<!-- If Exams Is Publish then display view student result icon -->
											@if($exam->status == 'publish')
												@if($exam->created_by == Auth::user()->id)
													<i class="fa fa-graduation-cap add-more-schools" aria-hidden="true" title="{{__('languages.add_schools')}}" data-id="{{$exam->id}}"></i>
												@endif
												
												@if(in_array('result_management_update', $permissions) && ($exam->use_of_mode != 2))
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
												<a href="{{$previewUrl}}" class="pl-2 btn-delete" id="configExam" data-id="{{$exam->id}}" title="{{__('languages.config')}}">
													<i class="fa fa-gear" aria-hidden="true"></i>
												</a>
											@endif										
                                            </td>
										</tr>
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

		<!-- Add More School -->
		<div class="modal fade" id="addMoreSchoolModel" tabindex="-1" role="dialog" aria-labelledby="nodeModalLabel" aria-hidden="true" data-backdrop="static">
			<div class="modal-dialog" role="document">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title">{{__('languages.select_school')}}</h5>
						<button type="button" class="close closeAddMoreSchoolModal" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button>
					</div>
					<div class="modal-body add-More-Schools-modal-body">
						<form method="POST" action="{{route('add-more-schools')}}" class="AddMoreSchools" id="AddMoreSchools">
							@CSRF
							@method("POST")
							<input type="hidden" name="examId" id="examId" value =""/>
							<div class="row">
								<div class="select-lng pt-2 pb-4 col-lg-8 col-md-8 col-sm-8">   
									<label>{{__('languages.select_school')}}</label>                         
									<select name="school[]"  id="add-schools" class="form-control select-option" multiple>
									</select>
									<span id="school-error"></span>
								</div>
							</div>
							<div class="row">
								<div calss="col-lg-3 col-md-3 col-sm-3">
									<button type="submit" class="btn btn-search add-more-school-btn">{{__('languages.add_schools')}}</button>
								</div>
							</div>
						</form>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-secondary closeAddMoreSchoolModal" data-dismiss="modal">{{__('languages.test.close')}}</button>
					</div>
				</div>
			</div>
		</div>
		
		<script>
		document.getElementById('pagination').onchange = function() {
			window.location = "{!! $examList->url(1) !!}&items=" + this.value;
		};
		</script>
		@include('backend.layouts.footer')
		<script type="text/javascript">
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
				/**
				 * USE : Update Exam status 'publish' or 'inactive'
				 * **/
				$(document).on("change", ".update_exam_status", function () {
					$("#cover-spin").show();
					if (this.value != "") {
						$examid = $(this).attr("data-examid");
						$status = this.value;
						$.ajax({
							url: BASE_URL + "/exam/status/update",
							type: "POST",
							data: {
								_token: $('meta[name="csrf-token"]').attr("content"),
								exam_id: $examid,
								status: $status,
							},
							success: function (response) {
								$("#cover-spin").hide();
								var data = JSON.parse(JSON.stringify(response));
								if(data.status === "success"){
									toastr.success(data.message);
									// Update status badge html for selecting based on options
									if ($status == "publish") {
										$(".exams_status_badge_" + $examid).html('<span class="badge badge-success">' +$status.toLowerCase().replace(/\b[a-z]/g,function (letter) {return letter.toUpperCase();}) +"</span>");
									}else{
										$(".exams_status_badge_" + $examid).html('<span class="badge badge-danger">' +$status.toLowerCase().replace(/\b[a-z]/g,function (letter) {return letter.toUpperCase();}) +"</span>");
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

				/***
				* USE : ADD MORE SCHOOLs IN QUESTION GENERATOR EXAMS 
				*/
				$(document).on('click','.add-more-schools',function(){
					$("#cover-spin").show();
					var examId = $(this).data('id');
					$.ajax({
						url: BASE_URL + '/get-schools',
						type: 'get',
						data: {
							'examId': examId,
						},
						success: function(response) {
							var data = JSON.parse(
												JSON.stringify(response)
											);
							if(data.data){
								$.each(data.data,function(key, school){
									$('#add-schools').append('<option value=' + school.id + '>' + school.school_name + '</option>'); 
									$('#add-schools').multiselect('rebuild');
								});
								$("#examId").val(examId);
								$("#addMoreSchoolModel").modal('show');
								$("#cover-spin").hide();
							}
						},
						error: function(response) {
							ErrorHandlingMessage(response);
						}
					});
				});
			});
		</script>
@endsection