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
								{{-- <div class="tab-study study-self-learn">
									<a href="#self_learning" class="test-tab active" id="tab-self-learning" data-id="self_learning">{{__('languages.self_learning')}}</a>
								</div> --}}
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
													$total_correct_answers=$examArray['attempt_exams'][0]['total_correct_answers'];
													$question_id_size=$examArray['question_ids'];
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

<!-- My study Configuration Popup -->
<!-- <div class="modal fade" id="my-study-config" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<form method="post">
				<div class="modal-header">
					<h4 class="modal-title w-100">{{__('languages.my_study_configuration')}}</h4>
					<button type="submit" class="btn btn-primary float-right">{{__('languages.submit')}}</button>
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				</div>
				<div class="modal-body">
					@csrf
					@if($roleId==2 && $student_id!="" && $grade_id!="")
					<input type="hidden" name="grade_id" value="{{ $grade_id }}">
					<input type="hidden" name="student_id" value="{{ $student_id }}">
					@endif
					<div class="row m-0">
						<div class="col-md-12 pl-4">
							<input type="checkbox" id="AllTabs"> <label class="ml-1">{{__('languages.all')}}</label>
						</div>
						<div class="col-md-12 categories-main-list">
							@if(!empty($studyFocusTreeOption))
							{!! $studyFocusTreeOption !!}
							@endif
						</div>
						<div class="col-md-4 categories-progess-list" style="display: none;">
							@if(!empty($studyFocusTreeOption))
							{!! $studyFocusTreeOption !!}
							@endif
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">{{__('languages.close')}}</button>
					<button type="submit" class="btn btn-primary">{{__('languages.submit')}}</button>
				</div>
			</div>
		</form>
	</div>
</div> -->
<!-- My study Configuration Popup -->

<!-- Play Video Popup -->
<div class="modal fade" id="videoModal" tabindex="-1"  data-keyboard="false" aria-labelledby="videoModalLabel"  aria-hidden="true" data-backdrop="static">
	<div class="modal-dialog modal-dialog-centered">
		<div class="modal-content">
			<div class="modal-body embed-responsive embed-responsive-16by9">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close" style="position: absolute;top: 0;right: 0;background-color: white;height: 30px;width: 30px;z-index: 9;opacity: 1;border-radius: 50%;padding-bottom: 4px;">
					<span aria-hidden="true">&times;</span>
				</button>
				<iframe class="embed-responsive-item" src="" id="videoDis" frameborder="0" allowtransparency="true" allowfullscreen ></iframe>
			</div>
		</div>
	</div>
</div>

<!-- Start Play Video Popup -->
<div class="modal fade" id="imgModal" tabindex="-1" aria-labelledby="imgModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered">
		<div class="modal-content">
			<div class="modal-body">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close" style="position: absolute;top: 0;right: 0;background-color: white;height: 30px;width: 30px;z-index: 9;opacity: 1;border-radius: 50%;padding-bottom: 4px;">
					<span aria-hidden="true">&times;</span>
				</button>
				<img id="imgDis"  style="width: 100%;height: 100%;" src="">
			</div>
		</div>
	</div>
</div>
<!-- End Play Video Popup -->

<!-- Start Student create self learning test Popup -->
<div class="modal" id="studentCreateSelfLearningTestModal" tabindex="-1" aria-labelledby="studentCreateSelfLearningTestModal" aria-hidden="true" data-backdrop="static">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<form class="student-generate-test-form" method="get" id="student-generate-test-form">
				<div class="modal-header">
					<h4 class="modal-title w-100">{{__('languages.generate_self_learning_test_or_exercise')}}</h4>
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				</div>
				<div class="modal-body">
					<input type="hidden" name="grade_id" value="{{ Auth::user()->grade_id }}" id="grade-id">
					<input type="hidden" name="subject_id" value="1" id="subject-id">
					<input type="hidden" name="question_ids" value="" id="question-ids">
					<div class="form-row">
						<div class="form-group col-md-6 mb-50">
							<label>{{ __('languages.test.test_type') }}</label>
							<select name="self_learning_test_type" class="form-control select-option" id="self_learning_test_type">
								<option value="">{{__('languages.test.select_test_type')}}</option>
								<option value="1">{{__('languages.my_studies.exercise')}}</option>
								<option value="2">{{__('languages.my_studies.test')}}</option>
							</select>
						</div>
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
								<option value="1">{{__('languages.easy')}}</option>
								<option value="2">{{__('languages.medium')}}</option>
								<option value="3">{{__('languages.difficult')}}</option>
								<option value="4">{{__('languages.tough')}}</option>
							</select>
						</div>
						<div class="form-group col-md-6 mb-50">
							<label>{{__('languages.no_of_question')}}</label>
							<input type="text" class="form-control" id="no_of_questions" name="no_of_questions" onkeyup="getTestTimeDuration()" value="" placeholder="{{__('languages.no_of_question')}}">
						</div>
						<div class="form-group col-md-6 mb-50 test_time_duration_section" style=display:none;>
							<label>{{__('languages.test_time_duration')}} ({{__('languages.hh_mm_ss')}})</label>
							<input type="text" class="form-control" id="test_time_duration" name="test_time_duration" value="" placeholder="{{__('languages.hh_mm_ss')}}">
							<span></span>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-primary" id="generate_test">{{__('languages.submit')}}</button>
					<button type="button" class="btn btn-default" data-dismiss="modal">{{__('languages.close')}}</button>
				</div>
			</form>
		</div>
	</div>
</div>
<!-- End Student create self learning test Popup -->

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

@include('backend.layouts.footer')
<script>
/**
* USE : Get time duration for test created by student
**/
function getTestTimeDuration(){
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
		}
	});
}

function getYoutubeId(url) {
	const regExp = /^.*(youtu.be\/|v\/|u\/\w\/|embed\/|watch\?v=|&v=)([^#&?]*).*/;
	const match = url.match(regExp);
	return (match && match[2].length === 11) ? match[2] : null;
}

$(function() {
	/**
	 * USE : On change event then display hide & show time textbox
	 * **/
	$(document).on('change', '#self_learning_test_type', function() {
		if(this.value == '2'){ // 2 is 'Test' type
			$('.test_time_duration_section').show();
		}
		if(this.value == '1'){ // 1 is 'Excercise' type
			$('.test_time_duration_section').hide();
		}
	});

	/**
	 * USE : Set input time duration mask validation
	 * **/
	var options = {
		onKeyPress: function (time, e, field, op) {
			if (time.length > 1) return;
			// allow input hours between from 00 to 19
			var hour_pattern = /[0-9]/;
			// allow input hours between from 20 to 23
			var first_char_of_hour = time[0];
			if (first_char_of_hour == '2') hour_pattern = /[0-3]/;
			// overwrite translation
			options.translation['h'] = {pattern: hour_pattern};
			// reset mask
			field.unmask();
			field.mask('Hh:M0:S0', options);
		},
		translation: {
			'H': {
				pattern: /[0-2]/
			},
			'M': {
				pattern: /[0-5]/
			},
			'S':{
				pattern: /[0-5]/
			}
		},
		placeholder: 'HH:MM:SS',
	};
	$('#test_time_duration').mask('H0:M0:S0', options);

	/**
	 * USE : Time duration field is valid or not
	 * **/
	$("#test_time_duration").on("keyup change", function(e) {
		var rg=/^(?:[0-5][0-9]):[0-5][0-9]:[0-5][0-9]$/
		if($(this).val() == '00:00:00'){
			$(this).next('span').html('Invalid Time').removeClass().addClass('invalid_time');
			$('#generate_test').attr('disabled',true);
		}else{
			if(rg.test($(this).val())){
				$(this).next('span').html('Valid Time').removeClass().addClass('valid_time');
				$('#generate_test').removeAttr('disabled');
			}else{
				$(this).next('span').html('Invalid Time').removeClass().addClass('invalid_time');
				$('#generate_test').attr('disabled',true);
			}
		}
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
								option.attr('value', this.id).text(this["name_"+APP_LANGUAGE]);
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
	
	/**
	 *  USE : Check form validation for create student self-learning test/Excercise
	  */
	  $("#student-generate-test-form").validate({
		rules: {
			self_learning_test_type: {
				required: true,
			},
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
			test_time_duration :{
				required :function(element){
					($('#self_learning_test_type').value ==2) ? true : false;
				} 
			}
		},
		messages: {
			self_learning_test_type: {
				required: VALIDATIONS.PLEASE_SELECT_TEST_TYPE,
			},
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
			test_time_duration :{
				required : VALIDATIONS.PLEASE_ENTER_TIME_DURATION,
			}
		}
	});

	/**
	 * USE : Trigger on click student can create own self-learning test
	  */
	  $(document).on('click', '#generate_test', function() {
		if($("#student-generate-test-form").valid()){ // Check form is valid or not
			$("#cover-spin").show();
			$.ajax({
				url: BASE_URL + '/getQuestionsStudentSelfLearningTest',
				type: 'get',
				data: $('#student-generate-test-form').serialize(),
				success: function(response) {
					var data = JSON.parse(JSON.stringify(response));
					$("#cover-spin").hide();
					if (data.status === 'success') {
						window.location.replace(BASE_URL+'/'+data.data.redirectUrl);
					}else {
						toastr.error(data.message);
					}
				},
				error: function(response){
					ErrorHandlingMessage(response);
				}
			});	
		}
	});
		
	$(document).on('click', '.test-tab', function() {
		$('.test-tab').removeClass('active');
		$('.tab-pane').removeClass('active');
		$('#'+$(this).attr('data-id')).addClass('active');
		$(this).addClass('active');
		$('#documentbtn form .active_tab').val($(this).attr('data-id'));
		$.cookie("PreviousTab", $(this).attr('data-id'));
	});

	$(document).on('click', '.video-img-sec', function() {
		var videoSrc = $(this).data( "src" );
		var domain = videoSrc.replace('http://','').replace('https://','').split(/[/?#]/)[0];
		if (videoSrc.indexOf("youtube") != -1) {
			const videoId = getYoutubeId(videoSrc);
			$("#videoDis").attr('src','//www.youtube.com/embed/'+videoId);
		}else if (videoSrc.indexOf("vimeo") != -1) {
			const videoId = getYoutubeId(videoSrc);
			var matches = videoSrc.match(/vimeo.com\/(\d+)/);
			$("#videoDis").attr('src','https://player.vimeo.com/video/'+matches[1]);
		}else if (videoSrc.indexOf("dailymotion") != -1) {
			var m = videoSrc.match(/^.+dailymotion.com\/(video|hub)\/([^_]+)[^#]*(#video=([^_&]+))?/);
			if (m !== null) {
				if(m[4] !== undefined) {
					$("#videoDis").attr('src','https://geo.dailymotion.com/player/x5poh.html?video='+m[4]);
				}
				$("#videoDis").attr('src','https://geo.dailymotion.com/player/x5poh.html?video='+m[2]);
			}
		}else{
			$("#videoDis").attr('src',videoSrc);
		}
	});
	
	$(document).on('click', '.document-img-view', function() {
		var imgSrc = $(this).data( "src" );
		$("#imgDis").attr('src',imgSrc);
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

	// if($("#self_learning.tab-pane > table > tbody > tr").length!=0){
	// 	$(".tab-study.study-self-learn > a").click();
	// }else if($("#exercise.tab-pane > table > tbody > tr").length!=0){
	// 	$(".tab-study.study-exercise > a").click();
	// }else if($("#test.tab-pane > table > tbody > tr").length!=0){
	// 	$(".tab-study.study-test > a").click();
	// }

	/*
	This change display document in exam id
	*/
	var listExamIdDoc = new Array();
	$.each($(".main-my-study input[type=checkbox]"), function() {
		if($(this).val()!='on'){
			listExamIdDoc.push($(this).val());
		}
	});
	
	// if(listExamIdDoc.length!=0){
	// 	$.ajax({
	// 		url: BASE_URL + '/study-documents',
	// 		type: 'POST',
	// 		data : {
	// 			'_token': $('meta[name="csrf-token"]').attr('content'),
	// 			'list_exam_id' : listExamIdDoc,
	// 			@if($roleId==2 && $student_id!="" && $grade_id!="")
	// 			'student_id' : {{ $student_id }},
	// 			@endif
	// 		},
	// 		success: function(response) {
	// 			$(".sm-vedio-pdf-doc-sec").html(response);
	// 		}
	// 	});
	// }else{
	// 	$(".sm-vedio-pdf-doc-sec").html('<div class="sec-title"><h3>Document List</h3></div><p class="text-center">No Any Documents Are Available</p>');
	// }

	$("#videoModal .close").click(function () {
		$("#videoModal #videoDis").attr('src','');
	});

	// $.each($(".categories-main-list input[type=checkbox][name='strands[]']"), function() {
	// 	var listConfigIdList= new Array();
	// 	listConfigIdList.push($(this).val());
	// 	var maindata=$(this);
	// 	$.ajax({
	// 		url: BASE_URL + '/estimate_student_competence_web',
	// 		type: 'POST',
	// 		data : {
	// 			'_token': $('meta[name="csrf-token"]').attr('content'),
	// 			'list_strands_id' : listConfigIdList,
	// 			@if($roleId==2 && $student_id!="" && $grade_id!="")
	// 			'student_id' : {{ $student_id }},
	// 			@endif
	// 		},
	// 		success: function(response) {
	// 			if(response.data.length!=0){
	// 				var mainDataVal=maindata.val();
	// 				var mainDataName=maindata.attr('name');
	// 				if($(".categories-main-list input[type=checkbox][name='"+mainDataName+"'][value="+mainDataVal+"]").length!=0){
	// 					var classAdd='up-50';
	// 					if(response<=49){
	// 						classAdd='down-50';
	// 					}
	// 					var labelData=$(".categories-main-list input[type=checkbox][name='"+mainDataName+"'][value="+mainDataVal+"]").parent();
	// 					labelData.find('.label-percentage:eq(0)').text(response.data[0]+'%').show();
	// 					labelData.find('input[type=range]:eq(0)').val(response.data[0]).attr('class',classAdd).show();
	// 				}
	// 				$("#cover-spin").hide();
	// 			}else{
	// 				var mainDataVal=maindata.val();
	// 				var mainDataName=maindata.attr('name');
	// 				if($(".categories-main-list input[type=checkbox][name='"+mainDataName+"'][value="+mainDataVal+"]").length!=0){
	// 					var responseData=0;
	// 					var classAdd='up-50';
	// 					if(responseData<=49){
	// 						classAdd='down-50';
	// 					}
	// 					var labelData=$(".categories-main-list input[type=checkbox][name='"+mainDataName+"'][value="+mainDataVal+"]").parent();
	// 					labelData.find('.label-percentage:eq(0)').text('N/A').show();
	// 					labelData.find('input[type=range]:eq(0)').val(responseData).attr('class',classAdd).hide();
	// 				}
	// 			}
	// 		}
	// 	});
	// });
	
	// $(document).on('click',".categories-main-list a.collapse-category", function() {
	// 	if($(this).hasClass('open')){
	// 		$(this).parent().find(' > ul > li > input[type=checkbox]').each(function(){
	// 			$("#cover-spin").show();
	// 			var var_data=new Array($(this).val());
	// 			var var_name=$(this).attr('name').replace('[]','');
	// 			var maindata=$(this);
	// 			$.ajax({
	// 				url: BASE_URL + '/estimate_student_competence_web',
	// 				type: 'POST',
	// 				data : {
	// 					'_token': $('meta[name="csrf-token"]').attr('content'),
	// 					[var_name]: var_data,
	// 					@if($roleId==2 && $student_id!="" && $grade_id!="")
	// 					'student_id' : {{ $student_id }},
	// 					@endif
	// 				},
	// 				success: function(response) {
	// 					if(response.data.length!=0){
	// 						var mainDataVal=maindata.val();
	// 						var mainDataName=maindata.attr('name');
	// 						if($(".categories-main-list input[type=checkbox][name='"+mainDataName+"'][value="+mainDataVal+"]").length!=0){
	// 							var classAdd='up-50';
	// 							if(response<=49){
	// 								classAdd='down-50';
	// 							}
	// 							var labelData=$(".categories-main-list input[type=checkbox][name='"+mainDataName+"'][value="+mainDataVal+"]").parent();
	// 							labelData.find('.label-percentage:eq(0)').text(response.data[0]+'%').show();
	// 							labelData.find('input[type=range]:eq(0)').val(response.data[0]).attr('class',classAdd).show();
	// 						}
	// 					}else{
	// 						var mainDataVal=maindata.val();
	// 						var mainDataName=maindata.attr('name');
	// 						if($(".categories-main-list input[type=checkbox][name='"+mainDataName+"'][value="+mainDataVal+"]").length!=0){
	// 							var responseData=0;
	// 							var classAdd='up-50';
	// 							if(responseData<=49){
	// 								classAdd='down-50';
	// 							}
	// 							var labelData=$(".categories-main-list input[type=checkbox][name='"+mainDataName+"'][value="+mainDataVal+"]").parent();
	// 							labelData.find('.label-percentage:eq(0)').text('N/A').show();
	// 							labelData.find('input[type=range]:eq(0)').val(responseData).attr('class',classAdd).hide();
	// 						}
	// 					}
	// 					$("#cover-spin").hide();
	// 				}
	// 			});
	// 		});
	// 	}
	// });
	
	$(document).on('change', '#AllTabs', function() {
		if($(this).prop('checked')){
			$(".categories-main-list .categories-list input[type=checkbox]").prop('checked',true);
		}else{
			$(".categories-main-list .categories-list input[type=checkbox]").prop('checked',false);
		}
	});
	
	// $(document).on('change', ".categories-main-list .categories-list input[type=checkbox]", function() {
	// 	var allchecklen=$(".categories-main-list .categories-list input[type=checkbox][name='strands[]']:checked").length;
	// 	var allunchecklen=$(".categories-main-list .categories-list input[type=checkbox][name='strands[]']").length;
	// 	if(allchecklen==allunchecklen){
	// 		$('#AllTabs').prop('checked',true);
	// 	}else{
	// 		$('#AllTabs').prop('checked',false);
	// 	}
	// });
	
	//$(document).find('.categories-progess-list input').attr('disabled',true);
	/*$(document).find('.categories-progess-list label').each(function () {
		//var numbersdata=getRandomNumber();
		var numbersdata=0;
		var classAdd='up-50';
		if(numbersdata<=49){
			classAdd='down-50';
		}
		$(this).html('<span class="label-text" >'+$(this).text()+'</span><input type="range" id="" min="0" value="'+numbersdata+'" disabled="" max="100" class="'+classAdd+'" style="display:none;"><span  class="label-percentage" style="display:none;">'+numbersdata+'%</span>');
	});*/
	
		/*$(document).on('change', '.categories-main-list input[type=checkbox]:checked', function() {
			var maindata=$(this);
			var listConfigId = new Array();
			$.each($(".categories-main-list input[type=checkbox][name='learning_objectives_id[]']:checked"), function() {
				if($(this).val()!='on')
				{
			  		listConfigId.push($(this).val());
			  	}
			});
			if(listConfigId.length!=0)
			{
				$("#cover-spin").show();
				$.ajax({
	                url: BASE_URL + '/estimate_student_competence_web',
	                type: 'POST',
	                data : {
	                    '_token': $('meta[name="csrf-token"]').attr('content'),
	                    'list_config_id' : listConfigId,
	                    @if($roleId==2 && $student_id!="" && $grade_id!="")
	                    'student_id' : {{ $student_id }},
	                    @endif
	                },
	                success: function(response) {
	                	if(response.data.length!=0)
	                	{
	                		var mainDataVal=maindata.val();
	                		var mainDataName=maindata.attr('name');
	                		if($(".categories-progess-list input[type=checkbox][name='"+mainDataName+"'][value="+mainDataVal+"]").length!=0)
	                		{
	                			var classAdd='up-50';
								if(response<=49)
								{
									classAdd='down-50';
								}
	                			var labelData=$(".categories-progess-list input[type=checkbox][name='"+mainDataName+"'][value="+mainDataVal+"]").parent().find(' > label');
	                			labelData.find('.label-percentage').text(response.data[0]+'%');
	                			labelData.find('input[type=range]').val(response.data[0]).attr('class',classAdd);
	                		}
	                		$("#cover-spin").hide();
	                	}
	                	else
	                	{
	                		$.confirm({
		                        title: 'Data not available',
		                        content: 'This dialog will automatically trigger \'cancel\' in 6 seconds if you don\'t respond.',
		                        autoClose: 'Cancellation|8000',
		                        buttons: {
		                            Cancellation: function() {
		                                $("#cover-spin").hide();
		                            }
		                        }
		                    });
	                	}
	                }
	            });
			}
		});*/
		/*$(document).on('click', '.categories-main-list .categories-list a', function() {
			var input=$(this).parent("li").find('> input');
			var lbl_name=input.attr('name');
			var lbl_value=input.attr('value');
			$(document).find(".categories-progess-list input[name='"+lbl_name+"'][value="+lbl_value+"]").parent("li").find('> a').click();
		});*/
});

function getRandomNumber(){
	return Math.floor(Math.random() * 101);
}
</script>
<script type="text/javascript">
// $.fn.cascadeCheckboxes = function() {
// 	$.fn.checkboxParent = function() {
// 		//to determine if checkbox has parent checkbox element
// 		var checkboxParent = $(this).parent("li").parent("ul").parent("li").find('> input[type="checkbox"]');
// 		return checkboxParent;
// 	};
// 	$.fn.checkboxChildren = function() {
// 		//to determine if checkbox has child checkbox element
// 		var checkboxChildren = $(this).parent("li").find('> .subcategories > li > input[type="checkbox"]');
// 		return checkboxChildren;
// 	};
// 	$.fn.cascadeUp = function() {
// 		var checkboxParent = $(this).checkboxParent();
// 		if ($(this).prop("checked")) {
// 			if (checkboxParent.length) {
// 				//check if all children of the parent are selected - if yes, select the parent
// 				//these will be the siblings of the element which we clicked on
// 				var children = $(checkboxParent).checkboxChildren();
// 				var booleanChildren = $.map(children, function(child, i) {
// 					return $(child).prop("checked");
// 				});
// 				//check if all children are checked
// 				var allChecked = booleanChildren.filter(function(x) {return !x})
// 				//if there are no false elements, parent is selected
// 				if (!allChecked.length) {
// 					$(checkboxParent).prop("checked", true);
// 					$(checkboxParent).cascadeUp();
// 				}
// 			}
// 		} else {
// 			if (checkboxParent.length) {
// 				//if parent is checked, becomes unchecked
// 				$(checkboxParent).prop("checked", false);
// 				$(checkboxParent).cascadeUp();
// 			}
// 		}
// 	};
// 	$.fn.cascadeDown = function() {
// 		var checkboxChildren = $(this).checkboxChildren();
// 		if (checkboxChildren.length) {
// 			checkboxChildren.prop("checked", $(this).prop("checked"));
// 			checkboxChildren.each(function(index) {
// 				$(this).cascadeDown();
// 			});
// 		}
// 	}
// 	$(this).cascadeUp();
// 	$(this).cascadeDown();
// };

// $("input[type=checkbox]:not(:disabled)").on("change", function() {
// 	$(this).cascadeCheckboxes();
// });
// $(".category a").on("click", function(e) {
// 	e.preventDefault();
// 	$(this).parent().find("> .subcategories").slideToggle(function() {
// 		if ($(this).is(":visible")) $(this).css("display", "flex");
// 	});
// });
// $('.collapse-category').on("click", function(){
// 	if($(this).hasClass('close')){
// 		$(this).removeClass('close');
// 		$(this).addClass('open');
// 	}else{
// 		$(this).removeClass('open');
// 		$(this).addClass('close');
// 	}
// });
</script>
@endsection