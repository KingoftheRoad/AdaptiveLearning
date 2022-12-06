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
						<div class="col-md-12 col-lg-12 col-sm-12 sec-title student-test-list-cls">
							<h2 class="mb-2 main-title">{{__('languages.sidebar.my_study')}}</h2>
						</div>
						<div class="col-md-12 col-lg-12 col-sm-12 test-color-info" style="display:none;">
							<div class="exercise-clr">
								<div class="first-clr"></div>
								<p>{{__('languages.my_studies.exercise')}}</p>
							</div>
							<div class="test-exam-clr">
								<div class="second-clr"></div>
								<p>{{__('languages.my_studies.test')}}</p>
							</div>
						</div>
						<div class="sec-title back-button-margin">
							<a href="javascript:void(0);" class="btn-back" id="backButton">{{__('languages.back')}}</a>
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
				<div class="row study_status_colors" >
					<div class="study_status_colors-sec">
						<strong>{{__('languages.study_status')}}:</strong>
					</div>
					<div class="study_status_colors-sec">
						<span class="dot-color" style="background-color: {{ App\Helpers\Helper::getGlobalConfiguration('struggling_color')}};border-radius: 50%;display: inline-block;"></span>
						<label>{{__('languages.struggling')}}</label>
					</div>
					<div class="study_status_colors-sec">
						<span class="dot-color" style="background-color: {{ App\Helpers\Helper::getGlobalConfiguration('beginning_color')}};border-radius: 50%;display: inline-block;"></span>
						<label>{{__('languages.beginning')}}</label>
					</div>
					<div class="study_status_colors-sec">
						<span class="dot-color" style="background-color: {{ App\Helpers\Helper::getGlobalConfiguration('approaching_color')}};border-radius: 50%;display: inline-block;"></span>
						<label>{{__('languages.approaching')}}</label>
					</div>
					<div class="study_status_colors-sec">
						<span class="dot-color" style="background-color: {{ App\Helpers\Helper::getGlobalConfiguration('proficient_color')}};border-radius: 50%;display: inline-block;"></span>
						<label>{{__('languages.proficient')}}</label>
					</div>
					<div class="study_status_colors-sec">
						<span class="dot-color" style="background-color: {{ App\Helpers\Helper::getGlobalConfiguration('advanced_color')}};border-radius: 50%;display: inline-block;"></span>
						<label>{{__('languages.advanced')}}</label>
					</div>
					<div class="study_status_colors-sec">
						<span class="dot-color" style="background-color: {{ App\Helpers\Helper::getGlobalConfiguration('incomplete_color')}};border-radius: 50%;display: inline-block;"></span>
						<label>{{__('languages.incomplete')}}</label>
					</div>
				</div>

				<div class="row question_difficulty_level_colors">
					<div class="question_difficulty_level_colors_sec">
						<strong>{{__('languages.question_difficulty_levels')}}:</strong>
					</div>
					@if(!empty($difficultyLevels))
						@foreach($difficultyLevels as $difficultLevel)
						<div class="question_difficulty_level_colors_sec">
							<span class="dot-color" style="background-color: {{$difficultLevel->difficulty_level_color}};border-radius: 50%;display: inline-block;"></span>
							<label>{{$difficultLevel->{'difficulty_level_name_'.app()->getLocale()} }}</label>
						</div>
						@endforeach
					@endif
				</div>

				<!-- new structure -->
	            <div class="row study-learning-tab">
					<div class="col-lg-12 col-md-12 col-sm-12">
						<div class="study-learning-inner">
							<div class="col-lg-9 col-md-9 col-sm-12">
								<div class="tab-study study-exercise">
									<a href="#exercise" class="test-tab" id="tab-exercise" data-id="exercise">{{__('languages.exercise')}}</a>
								</div>
								<div class="tab-study study-test">
									<a href="#test" class="test-tab" id="tab-test" data-id="test">{{__('languages.test_text')}}</a>
								</div>
							</div>
							<div class="col-lg-3 col-md-3 col-sm-12">
								<div class="study-setting">
									<!-- <a href="#" class="setting-button" id="my-study-config-btn"><i class="fa fa-cogs"></i></a> -->
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="row main-my-study">
	               	<div class="col-lg-12 col-md-12 col-sm-12">
	                  	<div id="mystudytable" class="my-study-table">
	                     	<div class="tab-content">
								<div role="tabpanel" class="tab-pane" id="exercise">
									<table id="exercise-table">
										<thead>
											<tr>
												<th class="selec-opt">{{__('languages.publish_date_time')}}</th>
												<th>{{__('languages.report.start_date')}}</th>
												<th>{{__('languages.report.end_date')}}</th>
												<th>{{__('languages.reference_number')}}</th>
												<th>{{__('languages.title')}}</th>
												<th>{{__('languages.average_accuracy')}}</th>
												<th align="center">{{__('languages.study_status')}}</th>
												<th>{{__('languages.status')}}</th>
												<th>{{__('languages.question_difficulties')}}</th>
												<th>{{__('languages.action')}}</th>
											</tr>
										</thead>
										@if(isset($data['exerciseExam']) && !empty($data['exerciseExam']))
										<tbody class="scroll-pane">
											@foreach($data['exerciseExam'] as $exerciseExam)
											@php $examArray = $exerciseExam->toArray(); 
											@endphp
											<tr @if($data['exerciseExam']) class='exercise-exam' @endif>
												<td>{{date('d/m/Y H:i:s',strtotime($exerciseExam->publish_date)) }}</td>
												<td>{{date('d/m/Y',strtotime($examArray['exam_school_grade_class'][0]['start_date'])) }} {{ !empty($examArray['exam_school_grade_class'][0]['start_time']) ? $examArray['exam_school_grade_class'][0]['start_time'] : '00:00:00' }}</td>
												<td>{{date('d/m/Y',strtotime($examArray['exam_school_grade_class'][0]['end_date'])) }} {{ !empty($examArray['exam_school_grade_class'][0]['end_time']) ?  $examArray['exam_school_grade_class'][0]['end_time'] : '00:00:00' }}</td>
												<td>{{$exerciseExam->reference_no}}</td>
												<td>{{$exerciseExam->title}}</td>
												@if(isset($examArray['attempt_exams']) && in_array(Auth::id(),array_column($examArray['attempt_exams'],'student_id')))
													@php
													$accuracy = App\Helpers\Helper::getAccuracy($exerciseExam->id, Auth::id());
													$ability  = $examArray['attempt_exams'][0]['student_ability'] ?? 0;
													$accuracy_type  = App\Helpers\Helper::getAbilityType($ability);
													$abilityPr = App\Helpers\Helper::getNormalizedAbility($ability);
													@endphp
													<td>
														@php
														$total_correct_answers = $examArray['attempt_exams'][0]['total_correct_answers'];
														$question_id_size = $examArray['question_ids'];
														if($question_id_size != ""){
															$question_id_size=sizeof(explode(',',$question_id_size));
														}
														echo '<div class="progress"><div class="progress-bar" role="progressbar" data-toggle="tooltip" data-placement="top" title="'.$accuracy.'% ('.$total_correct_answers.'/'.$question_id_size.')" style="width: '.$accuracy.'%;display: -webkit-box !important;display: -ms-flexbox !important;display: flex !important;" aria-valuenow="'.$accuracy.'" aria-valuemin="0" aria-valuemax="100">'.$accuracy.'%</div></div>';
														@endphp
													</td>
													<td align="center">
														<span class="dot-color" data-toggle="tooltip" data-placement="top"  title="{{round($ability,2)}} ({{$abilityPr}}%) "  style="border-radius: 50%;display: inline-block;position: relative;background-color: {{ App\Helpers\Helper::getGlobalConfiguration($accuracy_type)}};"></span>
													</td>
												@else
													<td align="center">-----</td>
													<td align="center">-----</td>
												@endif
												<td>
													@if((isset($examArray['attempt_exams']) && !in_array(Auth::id(),array_column($examArray['attempt_exams'],'student_id'))))
													<span class="badge badge-warning">{{__('languages.test.pending')}}</span>
													@else
													<span class="badge badge-success">{{__('languages.test.complete')}}</span>
													@endif
												</td>
												<td>
													@php
														$progressQuestions = App\Helpers\Helper::getQuestionDifficultiesLevelPercent($exerciseExam->id,Auth::id());
													@endphp
													<div class="progress">
														@php
														
														if($progressQuestions['Level1'] !=0) {
															echo '<div class="progress-bar p-1" data-toggle="tooltip" data-placement="top" title="'.$progressQuestions['Level1'].'%" style="width:'.$progressQuestions['Level1'].'%;background-color: '.$progressQuestions['Level1_color'].';">'.$progressQuestions['Level1'].'%'.'</div>';																
														}

														if($progressQuestions['Level2'] !=0) {
															echo '<div class="progress-bar p-1" data-toggle="tooltip" data-placement="top" title="'.$progressQuestions['Level2'].'%" style="width:'.$progressQuestions['Level2'].'%;background-color: '.$progressQuestions['Level2_color'].';">'.$progressQuestions['Level2'].'%'.'</div>';																
														}

														if($progressQuestions['Level3'] !=0) {
															echo '<div class="progress-bar p-1" data-toggle="tooltip" data-placement="top" title="'.$progressQuestions['Level3'].'%" style="width:'.$progressQuestions['Level3'].'%;background-color: '.$progressQuestions['Level3_color'].';">'.$progressQuestions['Level3'].'%'.'</div>';																
														}
														
														if($progressQuestions['Level4'] !=0) {
															echo '<div class="progress-bar p-1" data-toggle="tooltip" data-placement="top" title="'.$progressQuestions['Level4'].'%" style="width:'.$progressQuestions['Level4'].'%;background-color: '.$progressQuestions['Level4_color'].';">'.$progressQuestions['Level4'].'%'.'</div>';																
														}
														
														if($progressQuestions['Level5'] !=0) {
															echo '<div class="progress-bar p-1" data-toggle="tooltip" data-placement="top" title="'.$progressQuestions['Level5'].'%" style="width:'.$progressQuestions['Level5'].'%;background-color: '.$progressQuestions['Level5_color'].';">'.$progressQuestions['Level5'].'%'.'</div>';																
														}
														@endphp
													</div>
												</td>
												<td class="btn-edit">
													@if(in_array('attempt_exam_update', $permissions))
														@if(
															(isset($examArray['attempt_exams'])
															&& !in_array(Auth::id(),array_column($examArray['attempt_exams'],'student_id')))
															&& $exerciseExam->status == 'publish'
															&& 
															(isset($exerciseExam->ExamGradeClassConfigurations->start_date)
																&& isset($exerciseExam->ExamGradeClassConfigurations->start_time)
																&& isset($exerciseExam->ExamGradeClassConfigurations->end_date)
																&& isset($exerciseExam->ExamGradeClassConfigurations->end_time)
																&& date('Y-m-d H:i:s',strtotime($exerciseExam->ExamGradeClassConfigurations->start_date.''.$exerciseExam->ExamGradeClassConfigurations->start_time)) <= date('Y-m-d H:i:s')
																&& date('Y-m-d H:i:s',strtotime($exerciseExam->ExamGradeClassConfigurations->end_date.''.$exerciseExam->ExamGradeClassConfigurations->end_time)) >= date('Y-m-d H:i:s')
																
																|| 

																isset($exerciseExam->ExamGradeClassConfigurations->start_date)
																&& isset($exerciseExam->ExamGradeClassConfigurations->end_date)
																&& date('Y-m-d',strtotime($exerciseExam->ExamGradeClassConfigurations->start_date)) <= date('Y-m-d')
																&& date('Y-m-d',strtotime($exerciseExam->ExamGradeClassConfigurations->end_date)) >= date('Y-m-d')
															)
															&& (App\Helpers\Helper::CheckExamStudentMapping($exerciseExam->id) == false)
														)
														<a href="{{ route('studentAttemptExam', $exerciseExam->id) }}" class="" title="{{__('languages.test_text')}}">
															<i class="fa fa-book" aria-hidden="true"></i>
														</a>
														@endif
													@endif
													@if (in_array('result_management_read', $permissions))
														@if((isset($examArray['attempt_exams']) && in_array(Auth::id(),array_column($examArray['attempt_exams'],'student_id'))) && ($examArray['status'] == "publish") && date('Y-m-d',strtotime($examArray['result_date'])) <= date('Y-m-d'))
														<a href="{{route('exams.result',['examid' => $exerciseExam->id, 'studentid' => Auth::user()->id])}}" class="view-result-btn" title="{{__('languages.result_text')}}">
															<i class="fa fa-eye" aria-hidden="true" ></i>
														</a>
														@endif
													@endif

													@if((isset($examArray['attempt_exams']) && in_array(Auth::id(),array_column($examArray['attempt_exams'],'student_id'))) && ($examArray['status'] == "publish") && date('Y-m-d',strtotime($examArray['result_date'])) <= date('Y-m-d'))
														{{-- Test Difficulty Analysis Link --}}
														<a href="javascript:void(0);" title="{{__('languages.difficulty_analysis')}}" class="getTestDifficultyAnalysisReport" data-examid="{{$exerciseExam->id}}">
															<i class="fa fa-bar-chart" aria-hidden="true"></i>
														</a>
													@endif
												</td>
											</tr>
											@endforeach
											@endif
										</tbody>
									</table>
								</div>

								<div role="tabpanel" class="tab-pane" id="test">
									<table id="test-table">
										<thead>
											<tr>
												<th class="selec-opt">{{__('languages.publish_date_time')}}</th>
												<th>{{__('languages.report.start_date')}}</th>
												<th>{{__('languages.report.end_date')}}</th>
												<th>{{__('languages.reference_number')}}</th>
												<th>{{__('languages.title')}}</th>
												<th>{{__('languages.average_accuracy')}}</th>
												<th>{{__('languages.study_status')}}</th>
												<th>{{__('languages.status')}}</th>
												<th>{{__('languages.question_difficulties')}}</th>
												<th>{{__('languages.action')}}</th>
											</tr>
										</thead>
										<tbody class="scroll-pane">
											@if(isset($data['testExam']) && !empty($data['testExam']))
											@foreach($data['testExam'] as $testExam)
											@php $examArray = $testExam->toArray(); @endphp
											<tr>
												<td>{{date('d/m/Y H:i:s',strtotime($testExam->publish_date)) }}</td>
												<td>{{date('d/m/Y',strtotime($examArray['exam_school_grade_class'][0]['start_date'])) }} {{ !empty($examArray['exam_school_grade_class'][0]['start_time']) ? $examArray['exam_school_grade_class'][0]['start_time'] : '00:00:00' }}</td>
												<td>{{date('d/m/Y',strtotime($examArray['exam_school_grade_class'][0]['end_date'])) }} {{ !empty($examArray['exam_school_grade_class'][0]['end_time']) ?  $examArray['exam_school_grade_class'][0]['end_time'] : '00:00:00' }}</td>
												<td>{{$testExam->reference_no}}</td>
												<td>{{$testExam->title}}</td>
												@if(isset($examArray['attempt_exams']) && in_array(Auth::id(),array_column($examArray['attempt_exams'],'student_id')))
													@php
													$accuracy = App\Helpers\Helper::getAccuracy($testExam->id, Auth::id());
													$ability = $examArray['attempt_exams'][0]['student_ability'] ?? 0;
													$accuracy_type = App\Helpers\Helper::getAbilityType($ability);
													$abilityPr = App\Helpers\Helper::getNormalizedAbility($ability);
													@endphp
													<td>
														@php
														$total_correct_answers = $examArray['attempt_exams'][0]['total_correct_answers'];
														$question_id_size = $examArray['question_ids'];
														if($question_id_size != ""){
															$question_id_size = sizeof(explode(',',$question_id_size));
														}
														echo '<div class="progress"><div class="progress-bar" role="progressbar" data-toggle="tooltip" data-placement="top" title="'.$accuracy.'% ('.$total_correct_answers.'/'.$question_id_size.')" style="width: '.$accuracy.'%;display: -webkit-box !important;display: -ms-flexbox !important;display: flex !important;" aria-valuenow="'.$accuracy.'" aria-valuemin="0" aria-valuemax="100">'.$accuracy.'%</div></div>';
														@endphp
													</td>
													<td align="center">
														<span class="dot-color" data-toggle="tooltip" data-placement="top"  title="{{round($ability,2)}} ({{$abilityPr}}%)" style="background-color: {{App\Helpers\Helper::getGlobalConfiguration($accuracy_type)}};border-radius: 50%;display: inline-block;"></span>
													</td>
												@else
													<td align="center">-----</td>
													<td align="center">-----</td>
												@endif
												<td>
													@if((isset($examArray['attempt_exams']) && !in_array(Auth::id(),array_column($examArray['attempt_exams'],'student_id'))))
													<span class="badge badge-warning">{{__('languages.test.pending')}}</span>
													@else
													<span class="badge badge-success">{{__('languages.test.complete')}}</span>
													@endif
												</td>
												<td>
													@php
													$progressQuestions = App\Helpers\Helper::getQuestionDifficultiesLevelPercent($testExam->id,Auth::id());
													@endphp
													<div class="progress" style="height:1rem">
														@php
														
														if($progressQuestions['Level1'] !=0) {
															echo '<div class="progress-bar p-1" data-toggle="tooltip" data-placement="top" title="'.$progressQuestions['Level1'].'%" style="width:'.$progressQuestions['Level1'].'%;background-color: '.$progressQuestions['Level1_color'].';">'.$progressQuestions['Level1'].'%'.'</div>';																
														}

														if($progressQuestions['Level2'] !=0) {
															echo '<div class="progress-bar p-1" data-toggle="tooltip" data-placement="top" title="'.$progressQuestions['Level2'].'%" style="width:'.$progressQuestions['Level2'].'%;background-color: '.$progressQuestions['Level2_color'].';">'.$progressQuestions['Level2'].'%'.'</div>';																
														}

														if($progressQuestions['Level3'] !=0) {
															echo '<div class="progress-bar p-1" data-toggle="tooltip" data-placement="top" title="'.$progressQuestions['Level3'].'%" style="width:'.$progressQuestions['Level3'].'%;background-color: '.$progressQuestions['Level3_color'].';">'.$progressQuestions['Level3'].'%'.'</div>';																
														}
														
														if($progressQuestions['Level4'] !=0) {
															echo '<div class="progress-bar p-1" data-toggle="tooltip" data-placement="top" title="'.$progressQuestions['Level4'].'%" style="width:'.$progressQuestions['Level4'].'%;background-color: '.$progressQuestions['Level4_color'].';">'.$progressQuestions['Level4'].'%'.'</div>';																
														}
														
														if($progressQuestions['Level5'] !=0) {
															echo '<div class="progress-bar p-1" data-toggle="tooltip" data-placement="top" title="'.$progressQuestions['Level5'].'%" style="width:'.$progressQuestions['Level5'].'%;background-color: '.$progressQuestions['Level5_color'].';">'.$progressQuestions['Level5'].'%'.'</div>';																
														}
														@endphp
													</div>
												</td>
												<td class="btn-edit">
													@if(in_array('attempt_exam_update', $permissions))
														@if(
															!isset($examArray['attempt_exams']) 
															|| (isset($examArray['attempt_exams']) && !in_array(Auth::id(),array_column($examArray['attempt_exams'],'student_id'))) 
															&& $testExam->status == 'publish'
															&& 
															(isset($testExam->ExamGradeClassConfigurations->start_date)
																&& isset($testExam->ExamGradeClassConfigurations->start_time)
																&& isset($testExam->ExamGradeClassConfigurations->end_date)
																&& isset($testExam->ExamGradeClassConfigurations->end_time)
																&& date('Y-m-d H:i:s',strtotime($testExam->ExamGradeClassConfigurations->start_date.''.$testExam->ExamGradeClassConfigurations->start_time)) <= date('Y-m-d H:i:s')
																&& date('Y-m-d H:i:s',strtotime($testExam->ExamGradeClassConfigurations->end_date.''.$testExam->ExamGradeClassConfigurations->end_time)) >= date('Y-m-d H:i:s')
																
																|| 

																isset($testExam->ExamGradeClassConfigurations->start_date)
																&& isset($testExam->ExamGradeClassConfigurations->end_date)
																&& date('Y-m-d',strtotime($testExam->ExamGradeClassConfigurations->start_date)) <= date('Y-m-d')
																&& date('Y-m-d',strtotime($testExam->ExamGradeClassConfigurations->end_date)) >= date('Y-m-d')
															)
															&& (App\Helpers\Helper::CheckExamStudentMapping($testExam->id) == false)
														)
														<a href="{{ route('studentAttemptExam', $testExam->id) }}" class="" title="{{__('languages.test_text')}}">
															<i class="fa fa-book" aria-hidden="true"></i>
														</a>
														@endif
													@endif
													@if (in_array('result_management_read', $permissions))	
														@if((isset($examArray['attempt_exams']) && in_array(Auth::id(),array_column($examArray['attempt_exams'],'student_id'))) && ($examArray['status'] == "publish") && date('Y-m-d',strtotime($examArray['result_date'])) <= date('Y-m-d'))
														<a href="{{route('exams.result',['examid' => $testExam->id, 'studentid' => Auth::user()->id])}}" class="view-result-btn" title="{{__('languages.result_text')}}">
															<i class="fa fa-eye" aria-hidden="true" ></i>
														</a>
														@endif
													@endif
													@if((isset($examArray['attempt_exams']) && in_array(Auth::id(),array_column($examArray['attempt_exams'],'student_id'))) && ($examArray['status'] == "publish") && date('Y-m-d',strtotime($examArray['result_date'])) <= date('Y-m-d'))
														{{-- Test Difficulty Analysis Link --}}
														<a href="javascript:void(0);" title="{{__('languages.difficulty_analysis')}}" class="getTestDifficultyAnalysisReport" data-examid="{{$testExam->id}}">
															<i class="fa fa-bar-chart" aria-hidden="true"></i>
														</a>
													@endif
												</td>
											</tr>
											@endforeach
											@endif
										</tbody>
									</table>
								</div>
							</div>
	                  	</div>
	               	</div>
	            </div>
         </div>
      </div>
   </div>
</div>

<!-- Start list of difficulties of the questions in the test Analysis Popup -->
<div class="modal" id="test-difficulty-analysis-report" tabindex="-1" aria-labelledby="test-difficulty-analysis-report" aria-hidden="true" data-backdrop="static">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<form method="post">
				<div class="modal-header">
					<h4 class="modal-title w-100">{{__('languages.question_difficulty_analysis')}}</h4>
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				</div>
				<div class="modal-body Graph-body">
					<img src="" id="test-difficulty-analysis-report-image" class="img-fluid">
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">{{__('languages.close')}}</button>
				</div>
			</form>
		</div>
	</div>
</div>
<!-- End list of difficulties of the questions in the test Analysis Popup -->

<!-- Modal -->
<div class="modal fade" id="student-exam-result" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title" id="myModalLabel">{{__('languages.my_studies.test_result')}}</h4>
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
			</div>
			<div class="modal-body">{{__('languages.my_studies.in_this_section_will_be_displayed_test_result')}}</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">{{__('languages.close')}}</button>
			</div>
		</div>
	</div>
</div>
@include('backend.layouts.footer')

<script>
$(function() {			
	$(document).on('click', '.test-tab', function() {
		$('.test-tab').removeClass('active');
		$('.tab-pane').removeClass('active');
		$('#'+$(this).attr('data-id')).addClass('active');
		$(this).addClass('active');
		$('#documentbtn form .active_tab').val($(this).attr('data-id'));
		$.cookie("PreviousTab", $(this).attr('data-id'));
	});

	//Defalut remember tab selected into student panel and teacher panel
	$('.test-tab').removeClass('active');
	$('.tab-pane').removeClass('active');
	if($.cookie("PreviousTab")){
		$('#tab-'+$.cookie("PreviousTab")).addClass('active');
		$('#'+$.cookie("PreviousTab")).addClass('active');
	}else{
		$('#tab-exercise').addClass('active');
		$('#exercise').addClass('active');
	}
	
	$(document).on('change', '#AllTabs', function() {
		if($(this).prop('checked')){
			$(".categories-main-list .categories-list input[type=checkbox]").prop('checked',true);
		}else{
			$(".categories-main-list .categories-list input[type=checkbox]").prop('checked',false);
		}
	});
});

function getRandomNumber(){
	return Math.floor(Math.random() * 101);
}
</script>
@endsection