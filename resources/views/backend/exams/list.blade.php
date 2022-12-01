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
								<!-- @if(in_array('exam_management_create', $permissions))
									<a href="{{ route('exams.create') }}" class="btn-search white-font">{{ __('languages.test.add_test') }}</a>
								@endif
								@if(in_array('exam_management_delete', $permissions) && !$examList->isEmpty())
									<a href="javascript:void(0);" class="btn-search white-font" id="delete-multiple-exams-btn">{{ __('languages.test.delete_test') }}</a>
								@endif -->
								
								<!-- For Super admin -->
								@if(in_array('exam_management_create', $permissions) && App\Helpers\Helper::isAdmin())
									<a href="{{ route('super-admin.generate-questions') }}" class="btn-search white-font">{{__('languages.question_generators')}}</a>
								@else
									<a href="{{ route('school.generate-questions') }}" class="btn-search white-font">{{__('languages.question_generators')}}</a>
								@endif

								<!-- For Super admin -->
								<!-- <button class="btn-search white-font" data-toggle="modal" data-target="#generateGenerateTestExerciseTestModal">{{__('languages.generate_test_and_exercise')}}</button> -->
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
										<input type="text" class="form-control date-picker" name="from_date" value="{{ (request()->get('from_date')) }}" placeholder="{{__('languages.select_date')}}" autocomplete="off">
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
										<input type="text" class="form-control date-picker" name="to_date" value="{{ (request()->get('to_date'))}}" placeholder="{{__('languages.select_date')}}" autocomplete="off">
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
							<div class="question-bank-sec test-list-mains">
								<table class="exam-list-table display" style="width:100%">
							    	<thead>
							        	<tr>
							          		<th>
										  		<input type="checkbox" name="" class="checkbox" id="group-exam-ids">
											</th>
											<th>@sortablelink('exam_type',__('languages.test.test_type'))</th>
							          		<th>@sortablelink('title',__('languages.test.title'))</th>
											<th>@sortablelink('from_date',__('languages.start_date_time'))</th>
											<th>@sortablelink('to_date',__('languages.end_date_time'))</th>
											<th>@sortablelink('result_date',__('languages.test.result_date'))</th>
											@if(Auth::user()->role_id == 1)
											<th>{{__('languages.update_school_publish_status')}}</th>
											@endif
											<th>@sortablelink('status',__('languages.status'))</th>
											@if(Auth::user()->role_id != 1)
											<th>{{__('languages.test.update_status')}}</th>
											@endif
											
											<th>{{__('languages.action')}}</th>
							        	</tr>
							    	</thead>
							    	<tbody class="scroll-pane">
                                        @if(!empty($examList))
										@foreach($examList as $exam)
										<tr>
											<td><input type="checkbox" name="examids" class="checkbox exam-id" value="{{$exam->id}}"></td>
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
                                            <td>{{$exam->title}}</td>
                                            <td>{{date('d/m/Y',strtotime($exam->from_date))}} {{!empty($exam->start_time) ? $exam->start_time.':00' : '00:00:00'}}</td>
                                            <td>{{date('d/m/Y',strtotime($exam->to_date))}} {{!empty($exam->end_time) ? $exam->end_time.':00' : '00:00:00'}}</td>
                                            <td>{{!empty($exam->result_date) ? date('d/m/Y',strtotime($exam->result_date)) : 'After Submit'}}</td>
                                            @if(Auth::user()->role_id == 1)
											<td>
												<select name="exam_status" id="update_assign_school_status" class="update_assign_school_status" data-examid="{{$exam->id}}" {{ $exam->assign_school_status == 'send_to_school' ? 'disabled' : ''}}>
													{{-- <option value="">{{__('languages.select_status')}}</option> --}}
													<option value="draft" {{$exam->assign_school_status == 'draft' ? 'selected' : ''}}>{{__('languages.draft')}}</option>
													<option value="send_to_school" {{$exam->assign_school_status == 'send_to_school' ? 'selected' : ''}}>{{__('languages.send_to_school')}}</option>
												</select>
											</td>
											@endif
											<td class="exams_status_badge_{{$exam->id}}">
												@if($exam->status == 'active')
													<span class="badge badge-success">{{__('languages.active')}}</span>
												@elseif($exam->status == 'pending')
													<span class="badge badge-warning">{{__('languages.pending')}}</span>
												@elseif($exam->status == 'complete')
													<span class="badge badge-info">{{__('languages.complete')}}</span>
												@elseif($exam->status == 'publish')
													<span class="badge badge-success">{{__('languages.publish')}}</span>
												@else
													<span class="badge badge-danger">{{__('languages.inActive')}}</span>
												@endif
											</td>
											
											@if(Auth::user()->role_id != 1)
											<td>
												<select name="exam_status" id="update_exam_status" class="update_exam_status" data-examid="{{$exam->id}}" data-roleid="{{Auth::user()->role_id}}" {{ $exam->status == 'inactive' ? 'disabled' : ''}} {{ $exam->exam_type == 1 ? 'disabled' : ''}} >
													<option value="">{{__('languages.select_status')}}</option>
													<option value="publish" {{$exam->status == 'publish' ? 'selected' : ''}}>{{__('languages.publish')}}</option>
													<option value="inactive" {{$exam->status == 'inactive' ? 'selected' : ''}}>{{__('languages.inactive')}}</option>
												</select>
											</td>
											@endif
                                            <td class="edit-class">
											@if($exam->exam_type !=1)
												@if(in_array('exam_management_update', $permissions))
													@if(App\Helpers\Helper::isAdmin())
														<i class="fa fa-graduation-cap add-more-schools" aria-hidden="true" title="{{__('languages.add_schools')}}" data-id="{{$exam->id}}"></i>
														@if( $exam->assign_school_status == 'draft')
														<a href="{{ route('super-admin.generate-questions-edit', $exam->id) }}" class="btn-edit" title="{{__('languages.edit')}}">
															<i class="fa fa-pencil" aria-hidden="true"></i>
														</a>
														@endif
													@else
														@if($exam->status != 'publish' && $exam->status != 'inactive')
														<a href="{{ route('school.generate-questions-edit', $exam->id) }}" class="btn-edit" title="{{__('languages.edit')}}">
															<i class="fa fa-pencil" aria-hidden="true"></i>
														</a>
														@endif
													@endif
												@endif
												@if (in_array('exam_management_delete', $permissions))
													<span><i class="fa fa-user add-peer-group" aria-hidden="true" title="{{__('languages.add_students')}}" data-id={{$exam->id}}></i></span>
													<a href="javascript:void(0);" class="pl-2 btn-delete" id="deleteExam" data-id="{{$exam->id}}" title="{{__('languages.delete')}}">
														<i class="fa fa-trash" aria-hidden="true"></i>
													</a>
												@endif
											@endif

											{{--@if($exam->status != 'publish' && $exam->status != 'inactive')
												@if (in_array('assign_test_question_update', $permissions))
													<a href="{{ route('CreateFormExamQuestions', $exam->id) }}" title="{{__('languages.add_questions')}}" class="pl-2">
														<i class="fa fa-book" aria-hidden="true"></i>
													</a>
												@endif
												@if (in_array('assign_test_user_update', $permissions))
													<a href="{{ route('CreateFormExamStudents', $exam->id) }}" data-toggle="tooltip" title="{{__('languages.assign_to_school')}}" class="pl-2">
														<i class="fa fa-users" aria-hidden="true"></i>
													</a>
												@endif
												<!-- @if (in_array('assign_test_group_update', $permissions))
													<a href="{{ route('exams.assign-groups', $exam->id) }}" data-toggle="tooltip" title="{{__('languages.assign_to_groups')}}" class="pl-2">
														<i class="fa fa-users" aria-hidden="true"></i>
													</a>
												@endif -->
											@endif --}}
											<!-- If Exams Is Publish then display view student result icon -->
											@if (in_array('result_management_update', $permissions))
												@if($exam->status == 'publish')
												<a href="{{ route('getListAttemptedExamsStudents', $exam->id) }}" data-toggle="tooltip" title="{{__('languages.result_text')}}" class="pl-2">
													<i class="fa fa-eye" aria-hidden="true"></i>
												</a>
												@endif
											@endif
											<!-- @if (!empty($exam->template_id))
												<a href="javascript:void(0);" class="pl-2 btn-delete" id="templateShow" data-id="{{$exam->template_id}}" title="{{__('languages.template_show')}}">
													<i class="fa fa-eye" aria-hidden="true"></i>
												</a>
											@endif -->
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
		<div class="modal fade" id="addMoreSchoolModel" tabindex="-1" role="dialog" aria-labelledby="nodeModalLabel" aria-hidden="true">
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

		 <!-- Modal -->
		 <div class="modal fade template-modal" id="testTemplateModal" tabindex="-1" role="dialog" aria-labelledby="nodeModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">{{__('languages.test.template_question_list')}}</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body view-template-question"></div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">{{__('languages.test.close')}}</button>
                        </div>
                    </div>
                </div>
            </div>


			<!-- Start Admin Generate Test & Exercise test Popup -->
			<div class="modal" id="generateGenerateTestExerciseTestModal" tabindex="-1" aria-labelledby="generateGenerateTestExerciseTestModal" aria-hidden="true" data-backdrop="static">
				<div class="modal-dialog modal-lg">
					<div class="modal-content">
						<form class="admin-generate-test-form" method="POST" action="{{ route('generate-test-exercise') }}" id="admin-generate-test-form">
							@CSRF
							<div class="modal-header">
								<h4 class="modal-title w-100">{{__('languages.generate_test_exercise')}}</h4>
								<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
							</div>
							<div class="modal-body">
								<input type="hidden" name="grade_id" value="{{ Auth::user()->grade_id }}" id="grade-id">
								<input type="hidden" name="subject_id" value="1" id="subject-id">
								<input type="hidden" name="question_ids" value="" id="question-ids">
								<input type="hidden" name="self_learning_test_type" value="" id="self_learning_test_type">
								<div class="form-row">
									<div class="form-group col-md-6 mb-50">
										<label>{{__('languages.title')}}</label>
										<input type="text" class="form-control" id="title" name="title" value="" placeholder="{{__('languages.title')}}">
									</div>
									<div class="form-group col-md-6 mb-50">
										<label>{{__('languages.exam_type')}}</label>
										<select name="exam_type" class="form-control select-option" id="exam_type" >
											<option value="2">{{__('languages.exercise')}}</option>
											<option value="3">{{__('languages.test_text')}}</option>
										</select>
									</div>
								</div>
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
									</div>
									<div class="form-group col-md-6 mb-50">
										<label>{{__('languages.upload_document.learning_objectives')}}</label>
										<select name="learning_objectives_id[]" class="form-control select-option" id="learning_objectives" multiple>
											@if(isset($LearningObjectives) && !empty($LearningObjectives))
												@foreach ($LearningObjectives as $learningObjectivesKey => $learningObjectives)
													<option value="{{ $learningObjectives->id }}" selected>{{ $learningObjectives->foci_number }} {{ $learningObjectives->{'title_'.app()->getLocale()} }}</option>
												@endforeach
											@else
												<option value="">{{__('languages.no_learning_objectives_available')}}</option>
											@endif
										</select>
									</div>
									<div class="form-group col-md-6 mb-50">
										<label>{{__('languages.difficulty_mode')}}</label>
										<select name="difficulty_mode" class="form-control select-option" id="difficulty_mode">
											<option value="manual">{{__('languages.manual')}}</option>
											<option value="auto" disabled >{{__('languages.auto')}}</option>
										</select>
									</div>
									<div class="form-group col-md-6 mb-50">
										<label>{{__('languages.questions.difficulty_level')}}</label>
										<select name="difficulty_lvl[]" class="form-control select-option" id="difficulty_lvl" multiple>
											@if(!empty($difficultyLevels))
											@foreach($difficultyLevels as $difficultyLevel)
											<option value="{{$difficultyLevel['id']}}">{{$difficultyLevel['name']}}</option>
											@endforeach
											@endif								
										</select>
										<span name="err_difficulty_level"></span>
									</div>
									<div class="form-group col-md-6 mb-50">
										<label>{{__('languages.no_of_question')}}</label>
										<input type="text" class="form-control" id="no_of_questions" name="no_of_questions" onkeyup="getTestTimeDuration()" value="" placeholder="{{__('languages.no_of_question')}}">
									</div>
									<div class="form-group col-md-6 mb-50 test_time_duration_section" style=display:none;>
										<label>{{__('languages.test_time_duration')}} ({{__('languages.hh_mm_ss')}})</label>
										<input type="text" class="form-control mask time" id="test_time_duration" name="test_time_duration" value="" placeholder="{{__('languages.hh_mm_ss')}}" disabled>
										<span></span>
									</div>
								</div>
							</div>
							<div class="modal-footer">
								<button type="submit" class="btn btn-primary" id="generate_test">{{__('languages.submit')}}</button>
								<button type="button" class="btn btn-default" data-dismiss="modal">{{__('languages.close')}}</button>
							</div>
						</form>
					</div>
				</div>
			</div>
			<!-- End Admin Generate Test & Exercise test Popup -->
		<script>
		/*for pagination add this script added by mukesh mahanto*/ 
		document.getElementById('pagination').onchange = function() {
			window.location = "{!! $examList->url(1) !!}&items=" + this.value;
		};
		</script>
		{{-- Start Modal of Select Student and Group --}}
		<div class="modal fade" id="addStudentsInExam" tabindex="-1" role="dialog" aria-labelledby="nodeModalLabel" aria-hidden="true" data-backdrop="static">
			<div class="modal-dialog modal-lg" role="document">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title">{{__('languages.add_student_or_group')}}</h5>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button>
					</div>
					<div class="modal-body assignStudentData">
						
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-primary saveStudents" data-dismiss="modal">{{__('languages.submit')}}</button>
						<button type="button" class="btn btn-secondary closeaddStudentsInExamPopup" data-dismiss="modal">{{__('languages.close')}}</button>
					</div>
				</div>
			</div>
		</div>
		{{-- End Modal of Select Student and Group --}}
		@include('backend.layouts.footer')
		<script type="text/javascript">
			$(document).ready(function () {
				$("#exam_type").change(function(){
					if($(this).val()=='3'){
						$('.test_time_duration_section').show();
					}else{
						$('.test_time_duration_section').hide();
					}
				});
			})

		/***
		* USE : ADD MORE SCHOOLs IN QUESTION GENERATOR EXAMS 
		*/
		$(document).on('click','.add-more-schools',function(){
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
					}
				},
				error: function(response) {
					ErrorHandlingMessage(response);
				}
			});
		});

			/**
			 * USE : Get Learning Units from multiple strands
			 * **/
			$(document).on('change', '#strand_id', function() {
				$strandIds = $('#strand_id').val();
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
										option.attr('value', this.id).text(this.name);
										option.attr('selected', 'selected');
										$('#learning_unit').append(option);
									});
								}else{
									$('#learning_unit').html('<option value="">'+LEARNING_UNITS_NOT_AVAILABLE+'</option>');
								}
							}else{
								$('#learning_unit').html('<option value="">'+LEARNING_UNITS_NOT_AVAILABLE+'</option>');
							}
							$('#learning_unit').multiselect("rebuild");
							$('#learning_unit').trigger("change");
						},
						error: function(response) {
							ErrorHandlingMessage(response);
						}
					});
				}else{
					$('#learning_unit, #learning_objectives').html('');
					$('#learning_unit, #learning_objectives').multiselect("rebuild");
				}
			});

			/**
			 * USE : Get Multiple Learning units based on multiple learning units ids
			 * **/
			$(document).on('change', '#learning_unit', function() {
				$strandIds = $('#strand_id').val();
				$learningUnitIds = $('#learning_unit').val();
				if($learningUnitIds != ""){
					$.ajax({
						url: BASE_URL + '/getLearningObjectivesFromMultipleLearningUnits',
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
							$("#cover-spin").hide();
							var data = JSON.parse(JSON.stringify(response));
							if(data){
								if(data.data){
									$(data.data).each(function() {
										var option = $('<option />');
										option.attr('value', this.id).attr('selected','selected').text(this.foci_number + ' ' + this.title);
										$('#learning_objectives').append(option);
									});
								}else{
									$('#learning_objectives').html('<option value="">'+LEARNING_OBJECTIVES_NOT_AVAILABLE+'</option>');
								}
							}else{
								$('#learning_objectives').html('<option value="">'+LEARNING_OBJECTIVES_NOT_AVAILABLE+'</option>');
							}
							$('#learning_objectives').multiselect("rebuild");
						},
						error: function(response) {
							ErrorHandlingMessage(response);
						}
					});
				}else{
					$('#learning_objectives').html('');
					$('#learning_objectives').multiselect("rebuild");
				}
			});
			$(document).ready(function () {
			/**
			 *  USE : Check form validation for create admin self-learning test/Excercise
			  */
			  $("#admin-generate-test-form").validate({
				ignore: [],
				rules: {
					title:{
						required: true,
					},
					strand_id:{
						required: true,
					},
					learning_unit_id: {
						required: true,
					},
					no_of_questions:{
						required:true,
						number:true,
						valueMustGreterThanZero:true,
					},
					'difficulty_lvl[]':{
						required:true,
					},
					test_time_duration :{
						required :function(element){
							($('#self_learning_test_type').value ==2) ? true : false;
						} 
					}
				},
				messages: {
					title:{
						required: VALIDATIONS.PLEASE_ENTER_TITLE,
					},
					strand_id:{
						required: VALIDATIONS.PLEASE_SELECT_STRAND,
					},
					learning_unit_id: {
						required: VALIDATIONS.PLEASE_SELECT_LEARNING_UNIT,
					},
					learning_objectives_id:{
						required: VALIDATIONS.PLEASE_SELECT_LEARNING_OBJECTIVES,
					},
					no_of_questions:{
						required:VALIDATIONS.PLEASE_ENTER_NO_OF_QUESTIONS,
						number:VALIDATIONS.PLEASE_ENTER_ONLY_NUMERIC_VALUE,
						valueMustGreterThanZero : VALIDATIONS.PLEASE_ENTER_THAN_ZERO_VALUE
					},
					'difficulty_lvl[]':{
						required:VALIDATIONS.PLEASE_SELECT_DIFFICULTY_LEVEL,
					},
					test_time_duration :{
						required : VALIDATIONS.PLEASE_ENTER_TIME_DURATION,
					}
				},
				errorPlacement: function(error, element) {
		                if (element.attr("name") == "difficulty_lvl") {
		                    error.insertAfter('#err_difficulty_level');
		                }else {
		                    error.insertAfter(element);
		                }
		            }
			});
		});
		/**
		* USE : Get time duration for test created by student
		**/
		function getTestTimeDuration(){
			if($('#no_of_questions').val()){
				$("#cover-spin").show();
				$.ajax({
					url: BASE_URL + '/test/getTimeDuration',
					type: 'GET',
					data: {				
						'no_of_questions': $('#no_of_questions').val()
					},
					success: function(response){
						var responseData = JSON.parse(JSON.stringify(response));
						if(responseData){
							if(responseData.data){
								$('#test_time_duration').val(responseData.data);
							}
						}
						$("#cover-spin").hide();
					}
				});
			}else{
				$('#test_time_duration').val('');
			}
		}
		$(document).ready(function () {
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

				$(document).on('click','.saveStudents',function(){

					var examid = $("#examid").val();
					if($(document).find(".student-grade-class-section").length!=0)
					{
						var studentId=$("#question-generator-student-id").val();
						if(studentId.length!=0)
						{
							studentId = studentId.filter(function (el) {
							  return el !== '';
							});
						}

						if($(".question-generator-class-chkbox:checked").length==0)
						{
							toastr.error(VALIDATIONS.PLEASE_SELECT_GRADE_AND_CLASSES);
							return false;
						}
						
						if(studentId.length==0)
						{
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
								if (data.status === "success") {
	                            	toastr.success(data.message);
	                            	$('.assignStudentData').html('');
								} else {
									toastr.error(data.message);
								}
							},
							error: function (response) {
	                        	ErrorHandlingMessage(response);
	                    	},
	                	});
					}
					else
					{
						var groupIds = $('#question-generator-peer-group-options').val();
						if(groupIds[0]==''){
							toastr.error("Please Select Group");
							$('.assignStudentData').html('');
							return false;
						}
						if(groupIds.length ==0){
							toastr.error("Please Select Group");
							$('.assignStudentData').html('');
							return false;
						}
						
						if(groupIds.length !=0){
							$("#cover-spin").show();
							$.ajax({
								url: BASE_URL + "/add-test-late-commerce-student-peer-group",
								type: "POST",
								data: {
									_token: $('meta[name="csrf-token"]').attr(
		                                "content"
		                            ),
									examid: examid,
									groupIds : groupIds
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
					}
					$("#addStudentsInExam").modal("hide");
					$('.assignStudentData').html('');
				});

				$(document).on('click','.closeaddStudentsInExamPopup,.close',function(){
					$(".assignStudentData").html('');
				})
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
                    <input type="text" class="form-control date-picker-stud startDate" id="generatorClassValue_'+generatorClassValue+'" name="generator_class_start_date['+generatorValue+']['+generatorClassValue+']" value="'+mainStartDate+'" placeholder="{{__('languages.question_generators_menu.start_date')}}" autocomplete="off">\
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
                    <select name="generator_class_start_time['+generatorValue+']['+generatorClassValue+']" class="form-control select-option start_time">'+testStartTimeHtml+'</select>\
                </div>\
            </div>\
            <div class="form-group col-md-3 mb-50">\
                <label>{{ __('languages.question_generators_menu.end_date') }}</label>\
                <div class="input-group date">\
                    <input type="text" class="form-control date-picker-stud endDate" name="generator_class_end_date['+generatorValue+']['+generatorClassValue+']" value="'+mainEndDate+'" placeholder="{{__('languages.question_generators_menu.end_date')}}" autocomplete="off">\
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
                    <select name="generator_class_end_time['+generatorValue+']['+generatorClassValue+']" class="form-control select-option end_time">'+testEndTimeHtml+'</select>\
                </div>\
            </div>\
        </div></div><div class="col-md-12"><hr></div></div>';
        return dataHtmlData;
    }
		</script>
@endsection