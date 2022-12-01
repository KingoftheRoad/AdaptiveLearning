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
					<div class="col-md-12 test-titles">
						<div class="col-md-6 col-lg-6 col-sm-12 sec-title student-test-list-cls">
							@if(auth()->user()->role_id != 3)
							<h2 class="mb-2 main-title">{{__('languages.sidebar.my_teaching')}}</h2>
							@else
							<h2 class="mb-2 main-title">{{__('languages.sidebar.my_study')}}</h2>
							@endif
						</div>
						<div class="col-md-6 col-lg-6 col-sm-12 test-color-info" style="display:none;">
							<div class="exercise-clr">
								<div class="first-clr"></div>
								<p>{{__('languages.my_studies.exercise')}}</p>
							</div>
							<div class="test-exam-clr">
								<div class="second-clr"></div>
								<p>{{__('languages.my_studies.test')}}</p>
							</div>
						</div>
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
				@if($roleId==2)
				<form class="displayStudentStudyForm" id="displayStudentStudyForm" method="POST">
					@csrf
					<input type="hidden" name="school_id" id="student_study_school_id" value="{{ $schoolId }}">
					<div class="row">
						<div class="col-lg-4 col-md-4">
							<div class="select-lng pb-2">
								<label for="users-list-role">{{ __('languages.user_management.grade') }}</label>
								<select class="form-control" data-show-subtext="true" data-live-search="true" name="grade_id[]" id="student_multiple_grade_id" multiple required >
									@if(!empty($gradesList))
									@foreach($gradesList as $grade)
									<option value="{{$grade->getClass->id}}" {{ in_array($grade->getClass->id,$grade_id) ? 'selected' : '' }}>{{ $grade->getClass->name}}</option>
									@endforeach
									@endif
								</select>
							</div>
						</div>
						<div class="col-lg-2 col-md-3">
                            <div class="select-lng pb-2">
                            	<label for="users-list-role">{{ __('languages.class') }}</label>
                                <select name="class_type_id[]" class="form-control" id="classType-select-option" multiple >
                                	@if(!empty($GradeClassListData))
										@foreach($GradeClassListData as $GradeClassId => $GradeClassValue)
										<option value="{{$GradeClassId}}" {{ in_array($GradeClassId,$class_type_id) ? 'selected' : '' }}>{{ $GradeClassValue }}</option>
										@endforeach
									@endif
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-2 col-md-3">
							<div class="select-lng pt-4 pb-2">
								<button type="submit" name="filter" value="filter" class="btn-search mt-2">{{ __('languages.search') }}</button>
							</div>
						</div>
					</div>
				</form>
				<div class="row study_status_colors" >
					<div class="study_status_colors-sec">
						<strong>{{__('languages.study_status')}}:</strong>
					</div>
					<div class="study_status_colors-sec">
						<span class="dot-color" style="background-color: {{ App\Helpers\Helper::getGlobalConfiguration('struggling_color')}};border-radius: 50%;display: inline-block;"></span>
						<span>{{__('languages.struggling')}}</span>
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
					@php
						$value = App\Helpers\Helper::getGlobalConfiguration('question_difficulty_easy');
						$color = json_decode($value);
					@endphp
					<div class="question_difficulty_level_colors_sec">
						<span class="dot-color" style="background-color: {{$color->color}};border-radius: 50%;display: inline-block;"></span>
						<label>{{__('languages.easy')}}</label>
					</div>
					@php
						$value = App\Helpers\Helper::getGlobalConfiguration('question_difficulty_medium');
						$color = json_decode($value);
					@endphp
					<div class="question_difficulty_level_colors_sec">
						<span class="dot-color" style="background-color: {{$color->color}};border-radius: 50%;display: inline-block;"></span>
						<label>{{__('languages.medium')}}</label>
					</div>
					@php
						$value = App\Helpers\Helper::getGlobalConfiguration('question_difficulty_hard');
						$color = json_decode($value);
					@endphp
					<div class="question_difficulty_level_colors_sec">
						<span class="dot-color" style="background-color: {{$color->color}};border-radius: 50%;display: inline-block;"></span>
						<label>{{__('languages.hard')}}</label>
					</div>
				</div>
				@endif
				
				@if(($roleId==2 && $student_id!="") || $roleId==3)
				<!-- new structure -->
	            <div class="row study-learning-tab">
					<div class="col-lg-12 col-md-12 col-sm-12">
						<div class="study-learning-inner">
							<div class="col-lg-9 col-md-9 col-sm-12">
								<div class="tab-study study-self-learn">
									<a href="#self_learning" class="test-tab active" id="tab-self-learning" data-id="self_learning">{{__('languages.self_learning')}}</a>
								</div>
								<div class="tab-study study-exercise">
									<a href="#exercise" class="test-tab" id="tab-exercise" data-id="exercise">{{__('languages.exercise')}}</a>
								</div>
								<div class="tab-study study-test">
									<a href="#test" class="test-tab" id="tab-test" data-id="test">{{__('languages.test_text')}}</a>
								</div>
							</div>
							<div class="col-lg-3 col-md-3 col-sm-12">
								<div class="study-setting">
									<a href="#" class="setting-button" id="my-study-config-btn"><i class="fa fa-cogs"></i></a>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="row main-my-study">
	               	<div class="col-lg-9 col-sm-12">
	                  	<div id="mystudytable" class="my-study-table">
	                     	<div class="tab-content">
	                        	<div role="tabpanel" class="tab-pane active" id="self_learning">
									<table id="self-learning-table">
										<thead>
											<tr>
												<th>
													<input type="checkbox" name="" class="checkbox">
												</th>
												<th class="selec-opt"><span>{{__('languages.date')}} & {{__('languages.time')}}</span></th>
												<th class="first-head"><span>{{__('languages.report.student_name')}}</span></th>
												<th>{{__('languages.grade')}} - {{__('languages.class')}}</th>
												<th>{{__('languages.progress')}}</th>
												<th>{{__('languages.report.accuracy')}}</th>
												<th>{{__('languages.study_status')}}</th>
												<th>{{__('languages.question_difficulties')}}</th>
												<th>{{__('languages.action')}}</th>
											</tr>
										</thead>
										<tbody class="scroll-pane">
											@if(isset($data['selfLearningExam']) && !empty($data['selfLearningExam']))
												@foreach($data['selfLearningExam'] as $grades_class => $selfLearningExams)
													@php
														$examArray = $selfLearningExams->toArray(); 
													@endphp
													@if(isset($examArray) && !empty($examArray))
														@foreach($examArray as $selfLearningExam)
															@php
																$accuracy = App\Helpers\Helper::getAccuracyAllStudent($selfLearningExam['id'], $selfLearningExam['student_ids']);
															@endphp
															<tr>
																<td><input type="checkbox" name="selfLearningExam[]" value="{{ $selfLearningExam['id'] }}" class="checkbox"></td>
																<td>{{ date('d/m/Y H:i:s',strtotime($selfLearningExam['created_at'])) }}</td>
																<td> {{ App\Helpers\Helper::decrypt($selfLearningExam['user']['name_'.app()->getLocale()] ?? '')}}</td>
																<td>{{ $grades_class }}</td>
																<td>
																	@php
																		$student_ids = explode(',',$selfLearningExam['student_ids']);
																		$student_id_size = sizeof($student_ids);
																		$QuestionAnsweredCorrectly = App\Helpers\Helper::getAverageNoOfQuestionAnsweredCorrectly($selfLearningExam['id'], $selfLearningExam['student_ids']);
																	@endphp
																	@if(isset($selfLearningExam['student_ids']))
																		@php
																			$attempt_exams_size = sizeof($selfLearningExam['attempt_exams']);
																			$attempt_exams_pr = round(($attempt_exams_size/$student_id_size)*100);
																			if($attempt_exams_pr > 100){
																				$attempt_exams_pr = 100;
																			}
																			echo '<div class="progress">
																			<div class="progress-bar" role="progressbar" data-toggle="tooltip" data-placement="top" title="'.$attempt_exams_pr.'%'.' '.'('.$attempt_exams_size.'/'. $student_id_size.')"style="width: '.$attempt_exams_pr.'%;display: -webkit-box !important;display: -ms-flexbox !important;display: flex !important;" aria-valuenow="'.$attempt_exams_pr.'" aria-valuemin="0" aria-valuemax="100">'.$attempt_exams_pr.'%'.'</div>
																			</div>';
																		@endphp
																	@endif
																</td>
																<td>
																	@php
																		echo '<div class="progress">
																		<div class="progress-bar" role="progressbar" data-toggle="tooltip" data-placement="top" title="'.$accuracy.'% '.$QuestionAnsweredCorrectly .'" style="width: '.$accuracy.'%;display: -webkit-box !important;display: -ms-flexbox !important;display: flex !important;" aria-valuenow="'.$accuracy.'" aria-valuemin="0" aria-valuemax="100">'.$accuracy.'%</div>
																		</div>';
																	@endphp
																</td>
																@php 
																	$ability =  $selfLearningExam['attempt_exams'][0]['student_ability'] ?? 0;
																	$accuracy_type  = App\Helpers\Helper::getAbilityType($ability);
																	$abilityPr = App\Helpers\Helper::getNormalizedAbility($ability);
																@endphp
																<td align="center">
																	@if(!empty($accuracy_type))
																		<span class="dot-color" data-toggle="tooltip" data-placement="top"  title="{{round($ability,2)}} ({{$abilityPr}}%) "  style="border-radius: 50%;display: inline-block;position: relative;background-color: {{ App\Helpers\Helper::getGlobalConfiguration($accuracy_type)}};"></span>
																	@else
																		-----
																	@endif
																</td>
																@php
																	$progressQuestions = App\Helpers\Helper::getQuestionDifficultiesLevelPercent($selfLearningExam['id'], $selfLearningExam['student_ids']);
																@endphp
																<td>
																	<div class="progress" style="height: 1rem">
																		@php
																			$value = App\Helpers\Helper::getGlobalConfiguration('question_difficulty_easy');
																			$color = json_decode($value);
																			if($progressQuestions['Easy'] !=0) {
																				echo '<div class="progress-bar" data-toggle="tooltip" data-placement="top" title="'.$progressQuestions['Easy'].'%" style="width:'.$progressQuestions['Easy'].'%;background-color: '.$color->color.';">'.$progressQuestions['Easy'].'%'.'</div>';																
																			}
																		@endphp
																		@php
																			$value = App\Helpers\Helper::getGlobalConfiguration('question_difficulty_medium');
																			$color = json_decode($value);
																			if($progressQuestions['Medium'] !=0) {
																				echo '<div class="progress-bar" data-toggle="tooltip" data-placement="top" title="'.$progressQuestions['Medium'].'%" style="width:'.$progressQuestions['Medium'].'%;background-color: '.$color->color.';">'.$progressQuestions['Medium'].'%'.'</div>';																
																			}
																		@endphp
																		@php
																			$value = App\Helpers\Helper::getGlobalConfiguration('question_difficulty_hard');
																			$color = json_decode($value);
																			if($progressQuestions['Hard'] !=0) {
																				echo '<div class="progress-bar" data-toggle="tooltip" data-placement="top" title="'.$progressQuestions['Hard'].'%" style="width:'.$progressQuestions['Hard'].'%;background-color: '.$color->color.';">'.$progressQuestions['Hard'].'%'.'</div>';																
																			}
																		@endphp
																	</div>
																</td>
																<td class="btn-edit">
																	<a href="{{ route('report.class-test-reports.correct-incorrect-answer', ['exam_id' => $selfLearningExam['id'],'filter' => 'filter']) }}" title="Class Performance Report"><i class="fa fa-bar-chart" aria-hidden="true"></i></a>
																	{{-- <a href="javascript:void(0);" title="Class Ability Analysis" class="getClassAbilityAnalysisReport" data-examid="{{$selfLearningExam['id']}}" data-studentids="{{$selfLearningExam['student_ids']}}">
																		<i class="fa fa-bar-chart" aria-hidden="true"></i>
																	</a> --}}
																	<a href="javascript:void(0);" title="Test Difficulty Analysis" class="getTestDifficultyAnalysisReport" data-examid="{{$selfLearningExam['id']}}">
																		<i class="fa fa-bar-chart" aria-hidden="true"></i>
																	</a>
																</td>
															</tr>
														@endforeach
													@endif
												@endforeach
											@endif
											<!-- Is Group Test -->
											@if(isset($GroupTestData['self-learning']))
												@foreach($GroupTestData['self-learning'] as $grades_class => $groupTest)
													@if(isset($groupTest['examDetail']) && $groupTest['examDetail']['status'] == 'publish' && $groupTest['examDetail']['exam_type'] == 1 )
													<tr>
														<td><input type="checkbox" name="selfLearningExam[]"  value="{{ $groupTest['groupTest']['exam_ids'] }}" class="checkbox"></td>
														<td>{{date('d/m/Y H:i:s',strtotime($groupTest['examDetail']['created_at']))}}</td>
														<td>{{ $groupTest['groupTest']['name'] }}</td>
														<td>{{ $grades_class }}</td>
														@php
															$accuracy = App\Helpers\Helper::getAccuracyAllStudent($groupTest['groupTest']['exam_ids'], $groupTest['groupTest']['student_ids']);
														@endphp
														<td>
															@php
																$student_ids=explode(',',$groupTest['groupTest']['student_ids']);
																$student_id_size=sizeof($student_ids);
																$QuestionAnsweredCorrectly = App\Helpers\Helper::getAverageNoOfQuestionAnsweredCorrectly($groupTest['groupTest']['exam_ids'], $groupTest['groupTest']['student_ids']);
															@endphp
															@if(isset($groupTest['examDetail']['attempt_exams']))
																@php
																	$attempt_exams_size = sizeof($groupTest['examDetail']['attempt_exams']);
																	$attempt_exams_pr = round(($attempt_exams_size/$student_id_size)*100);
																	if($attempt_exams_pr > 100){
																		$attempt_exams_pr = 100;	
																	}
																	echo '<div class="progress">
																	<div class="progress-bar" role="progressbar" data-toggle="tooltip" data-placement="top" title="'.$attempt_exams_pr.'%'.' '.'('.$attempt_exams_size.'/'. $student_id_size.')" style="width: '.$attempt_exams_pr.'%;display: -webkit-box !important;display: -ms-flexbox !important;display: flex !important;" aria-valuenow="'.$attempt_exams_pr.'" aria-valuemin="0" aria-valuemax="100">'.$attempt_exams_pr.'%'.'</div>
																	</div>';
																@endphp
															@endif
														</td>

																
														<td>
															@php
																 echo '<div class="progress">
																<div class="progress-bar" role="progressbar"  data-toggle="tooltip" data-placement="top" title="'.$accuracy.'% '.$QuestionAnsweredCorrectly .'"  style="width: '.$accuracy.'%;display: -webkit-box !important;display: -ms-flexbox !important;display: flex !important;" aria-valuenow="'.$accuracy.'" aria-valuemin="0" aria-valuemax="100">'.$accuracy.'%</div>
																</div>';
															@endphp
														</td>
														<td align="center">-----</td>
														@php
															$progressQuestions = App\Helpers\Helper::getQuestionDifficultiesLevelPercent($groupTest['groupTest']['exam_ids'], $groupTest['groupTest']['student_ids']);
														@endphp
														<td>
															<div class="progress" style="height: 1rem">
																@php
																	$value = App\Helpers\Helper::getGlobalConfiguration('question_difficulty_easy');
																	$color = json_decode($value);
																	if($progressQuestions['Easy'] !=0) {
																		echo '<div class="progress-bar" data-toggle="tooltip" data-placement="top" title="'.$progressQuestions['Easy'].'%" style="width:'.$progressQuestions['Easy'].'%;background-color: '.$color->color.';">'.$progressQuestions['Easy'].'%'.'</div>';																
																	}
																@endphp
																@php
																	$value = App\Helpers\Helper::getGlobalConfiguration('question_difficulty_medium');
																	$color = json_decode($value);
																	if($progressQuestions['Medium'] !=0) {
																		echo '<div class="progress-bar" data-toggle="tooltip" data-placement="top" title="'.$progressQuestions['Medium'].'%" style="width:'.$progressQuestions['Medium'].'%;background-color: '.$color->color.';">'.$progressQuestions['Medium'].'%'.'</div>';																
																	}
																@endphp
																@php
																	$value = App\Helpers\Helper::getGlobalConfiguration('question_difficulty_hard');
																	$color = json_decode($value);
																	if($progressQuestions['Hard'] !=0) {
																		echo '<div class="progress-bar" data-toggle="tooltip" data-placement="top" title="'.$progressQuestions['Hard'].'%" style="width:'.$progressQuestions['Hard'].'%;background-color: '.$color->color.';">'.$progressQuestions['Hard'].'%'.'</div>';																
																	}
																@endphp
															</div>
														</td>
														<td class="btn-edit">
															<a href="{{ route('report.class-test-reports.correct-incorrect-answer', ['exam_id' => $groupTest['groupTest']['exam_ids'], 'filter' => 'filter']) }}" title="Class Performance Report"><i class="fa fa-bar-chart" aria-hidden="true"></i></a>
															{{-- <a href="javascript:void(0);" title="Class Ability Analysis" class="getClassAbilityAnalysisReport" data-examid="{{$groupTest['groupTest']['exam_ids']}}" data-studentids="{{$groupTest['groupTest']['student_ids']}}">
																<i class="fa fa-bar-chart" aria-hidden="true"></i>
															</a> --}}
															<a href="javascript:void(0);" title="Test Difficulty Analysis" class="getTestDifficultyAnalysisReport" data-examid="{{$groupTest['groupTest']['exam_ids']}}">
																<i class="fa fa-bar-chart" aria-hidden="true"></i>
															</a>
														</td>
													</tr>
													@endif
												@endforeach
											@endif
											<!-- End Group Test -->
										</tbody>
									</table>
	                        	</div>

								<div role="tabpanel" class="tab-pane" id="exercise">
									<table id="exercise-table">
										<thead>
											<tr>
												<th>
													<input type="checkbox" name="" class="checkbox">
												</th>
												<th class="selec-opt">{{__('languages.date')}} & {{__('languages.time')}}</th>
												<th class="first-head"><span>{{__('languages.title')}}</span></th>
												<th>{{__('languages.grade')}} - {{__('languages.class')}}</th>
												<th class="selec-opt"><span>{{__('languages.no_of_students')}}</span></th>
												<th>{{__('languages.progress')}}</th>
												<th>{{__('languages.average_accuracy')}}</th>
												<th>{{__('languages.study_status')}}</th>
												<th>{{__('languages.question_difficulties')}}</th>
												<th>{{__('languages.action')}}</th>
											</tr>
										</thead>
										@if(isset($data['exerciseExam']) && !empty($data['exerciseExam']))
										<tbody class="scroll-pane">
											@foreach($data['exerciseExam'] as $grades_class =>  $exerciseExams)
											@php $examArray = $exerciseExams->toArray(); @endphp
												@if(isset($examArray) && !empty($examArray))
													@foreach($examArray as $exerciseExam)
														@php
															if(isset($studentidlist) && !empty($studentidlist))
															{
																$examStudant=explode(',',$exerciseExam['student_ids']);
																$teacherStudent=array_intersect($studentidlist,$examStudant);
																$student_id_size=sizeof($teacherStudent);
																$student_id_in_comm=implode(',',$teacherStudent);
															}
														@endphp
														<tr @if($data['exerciseExam']) class='exercise-exam' @endif>
															<td><input type="checkbox" value="{{ $exerciseExam['id'] }}" name="exerciseExam[]" class="checkbox"></td>
															<td>{{ date('d/m/Y H:i:s',strtotime($exerciseExam['created_at'])) }}</td>
															<td>{{ $exerciseExam['title'] }}</td>
															<td>{{ $grades_class }}</td>
															<td>{{ count($teacherStudent); }}</td>
															@php																
																$accuracy = App\Helpers\Helper::getAccuracyAllStudent($exerciseExam['id'], $exerciseExam['student_ids']);
																$QuestionAnsweredCorrectly = App\Helpers\Helper::getAverageNoOfQuestionAnsweredCorrectly($exerciseExam['id'],$student_id_in_comm);
															@endphp
															<td>
																@if(isset($exerciseExam['student_ids']))
																	@php
																		$attempt_exams_size = sizeof($exerciseExam['attempt_exams']);
																		$attempt_exams_pr = round(($attempt_exams_size/$student_id_size)*100);
																		if($attempt_exams_pr > 100){
																			$attempt_exams_pr = 100;	
																		}
																		echo '<div class="progress">
																		  <div class="progress-bar" role="progressbar" data-toggle="tooltip" data-placement="top" title="'.$attempt_exams_pr.'%'.' '.'('.$attempt_exams_size.'/'. $student_id_size.')"style="width: '.$attempt_exams_pr.'%;display: -webkit-box !important;display: -ms-flexbox !important;display: flex !important;" aria-valuenow="'.$attempt_exams_pr.'" aria-valuemin="0" aria-valuemax="100">'.$attempt_exams_pr.'%'.'</div>
																		</div>';
																	@endphp
																@endif
															</td>
															<td>
																@php
																	echo '<div class="progress">
																		  <div class="progress-bar" role="progressbar" data-toggle="tooltip" data-placement="top" title="'.$accuracy.'% '.$QuestionAnsweredCorrectly .'" style="width: '.$accuracy.'%;display: -webkit-box !important;display: -ms-flexbox !important;display: flex !important;" aria-valuenow="'.$accuracy.'" aria-valuemin="0" aria-valuemax="100">'.$accuracy.'%</div>
																		</div>';
																@endphp
															</td>
															@php
																$AlpAiGraphController = new \App\Http\Controllers\Reports\AlpAiGraphController();
																$progress = $AlpAiGraphController->getProgressDetailList($exerciseExam['id'],$student_id_in_comm);
															@endphp
															<td>
																<div class="progress">
																	@php 
																	if($progress['Struggling'] != 0) {
																		echo '<div class="progress-bar p-0" data-toggle="tooltip" data-placement="top" title="'.$progress['Struggling'].'%" style="width:'.$progress['Struggling'].'%;background-color: '. App\Helpers\Helper::getGlobalConfiguration('struggling_color').';">'.$progress['Struggling'].'%'.'</div>';
																	}
																	@endphp
																	@php 
																	if($progress['Beginning'] != 0) {
																		echo '<div class="progress-bar p-0" data-toggle="tooltip" data-placement="top" title="'.$progress['Beginning'].'%" style="width:'.$progress['Beginning'].'%;background-color: '. App\Helpers\Helper::getGlobalConfiguration('beginning_color').';">'.$progress['Beginning'].'%'.'</div>';
																	}
																	@endphp
																	@php 
																	if($progress['Approaching'] != 0) {
																		echo '<div class="progress-bar p-0" data-toggle="tooltip" data-placement="top" title="'.$progress['Approaching'].'%" style="width:'.$progress['Approaching'].'%;background-color: '. App\Helpers\Helper::getGlobalConfiguration('approaching_color').';">'.$progress['Approaching'].'%'.'</div>';
																	}
																	@endphp
																	@php 
																	if($progress['Proficient'] != 0) {
																		echo '<div class="progress-bar p-0" data-toggle="tooltip" data-placement="top" title="'.$progress['Proficient'].'%" style="width:'.$progress['Proficient'].'%;background-color: '. App\Helpers\Helper::getGlobalConfiguration('proficient_color').';">'.$progress['Proficient'].'%'.'</div>';
																	}
																	@endphp
																	@php 
																	if($progress['Advanced'] != 0) {
																		echo '<div class="progress-bar p-0" data-toggle="tooltip" data-placement="top" title="'.$progress['Advanced'].'%" style="width:'.$progress['Advanced'].'%;background-color: '. App\Helpers\Helper::getGlobalConfiguration('advanced_color').';">'.$progress['Advanced'].'%'.'</div>';
																	}
																	@endphp
																	@php 
																	if($progress['InComplete'] != 0) {
																		echo '<div class="progress-bar p-0" data-toggle="tooltip" data-placement="top" title="'.$progress['InComplete'].'%" style="width:'.$progress['InComplete'].'%;background-color: '. App\Helpers\Helper::getGlobalConfiguration('incomplete_color').';">'.$progress['InComplete'].'%'.'</div>';
																	}
																	@endphp
																</div>
															</td>
															@php
																$progressQuestions = App\Helpers\Helper::getQuestionDifficultiesLevelPercent($exerciseExam['id'],$student_id_in_comm);
															@endphp
															<td>
																<div class="progress" >
																	@php
																		$value = App\Helpers\Helper::getGlobalConfiguration('question_difficulty_easy');
																		$color = json_decode($value);
																		if($progressQuestions['Easy'] !=0) {
																			echo '<div class="progress-bar p-1" data-toggle="tooltip" data-placement="top" title="'.$progressQuestions['Easy'].'%" style="width:'.$progressQuestions['Easy'].'%;background-color: '.$color->color.';">'.$progressQuestions['Easy'].'%'.'</div>';																
																		}
																	@endphp
																	@php
																		$value = App\Helpers\Helper::getGlobalConfiguration('question_difficulty_medium');
																		$color = json_decode($value);
																		if($progressQuestions['Medium'] !=0) {
																			echo '<div class="progress-bar p-1" data-toggle="tooltip" data-placement="top" title="'.$progressQuestions['Medium'].'%" style="width:'.$progressQuestions['Medium'].'%;background-color: '.$color->color.';">'.$progressQuestions['Medium'].'%'.'</div>';																
																		}
																	@endphp
																	@php
																		$value = App\Helpers\Helper::getGlobalConfiguration('question_difficulty_hard');
																		$color = json_decode($value);
																		if($progressQuestions['Hard'] !=0) {
																			echo '<div class="progress-bar p-1" data-toggle="tooltip" data-placement="top" title="'.$progressQuestions['Hard'].'%" style="width:'.$progressQuestions['Hard'].'%;background-color: '.$color->color.';">'.$progressQuestions['Hard'].'%'.'</div>';																
																		}
																	@endphp
																</div>
															</td>
															<td class="btn-edit">
																<a href="{{ route('report.class-test-reports.correct-incorrect-answer', ['exam_id' => $exerciseExam['id'], 'filter' => 'filter']) }}" title="Class Performance Report"><i class="fa fa-bar-chart" aria-hidden="true"></i></a>
																<a href="javascript:void(0);" title="Class Ability Analysis" class="getClassAbilityAnalysisReport" data-examid="{{$exerciseExam['id']}}" data-studentids="{{$exerciseExam['student_ids']}}">
																	<i class="fa fa-bar-chart" aria-hidden="true"></i>
																</a>
																<a href="javascript:void(0);" title="Test Difficulty Analysis" class="getTestDifficultyAnalysisReport" data-examid="{{$exerciseExam['id']}}">
																	<i class="fa fa-bar-chart" aria-hidden="true"></i>
																</a>
															</td>
														</tr>
													@endforeach
												@endif
											@endforeach
											@endif
											<!-- Is Group Test -->
											@if(isset($GroupTestData['excercise-test']))
												@foreach($GroupTestData['excercise-test'] as $grades_class => $groupTest)
												@if(isset($groupTest['examDetail']) && $groupTest['examDetail']['status'] == 'publish' && $groupTest['examDetail']['exam_type'] == 2 )

												@php
													if(isset($studentidlist) && !empty($studentidlist))
													{
														$examStudant=explode(',',$groupTest['groupTest']['student_ids']);
														$teacherStudent=array_intersect($studentidlist,$examStudant);
														$student_id_size=sizeof($teacherStudent);
														$student_id_in_comm=implode(',',$teacherStudent);
													}
												@endphp
												<tr @if($data['exerciseExam']) class='exercise-exam' @else class='test-exam' @endif>
													<td><input type="checkbox" value="{{ $groupTest['groupTest']['exam_ids'] }}" name="exerciseExam[]" class="checkbox"></td>
													<td>{{date('d/m/Y H:i:s',strtotime($groupTest['examDetail']['created_at']))}}</td>
													<td>{{ $groupTest['groupTest']['name'] }}</td>
													<td>{{ $grades_class }}</td>
													<td>{{ count($teacherStudent) }}</td>
													<td>
														
														@if(isset($groupTest['examDetail']['attempt_exams']))
															@php
																$attempt_exams_size = sizeof($groupTest['examDetail']['attempt_exams']);
																$attempt_exams_pr = round(($attempt_exams_size/$student_id_size)*100);
																if($attempt_exams_pr>100){
																	$attempt_exams_pr=100;	
																}
																echo '<div class="progress">
																  <div class="progress-bar" role="progressbar" data-toggle="tooltip" data-placement="top" title="'.$attempt_exams_pr.'%'.' '.'('.$attempt_exams_size.'/'. $student_id_size.')" style="width: '.$attempt_exams_pr.'%;display: -webkit-box !important;display: -ms-flexbox !important;display: flex !important;" aria-valuenow="'.$attempt_exams_pr.'" aria-valuemin="0" aria-valuemax="100">'.$attempt_exams_pr.'%'.'</div>
																</div>';
															@endphp
														@endif
													</td>
													@php
														$accuracy = App\Helpers\Helper::getAccuracyAllStudent($groupTest['groupTest']['exam_ids'], $student_id_in_comm);
														$QuestionAnsweredCorrectly = App\Helpers\Helper::getAverageNoOfQuestionAnsweredCorrectly($groupTest['groupTest']['exam_ids'], $student_id_in_comm);
													@endphp
													
													<td>
														@php
															echo '<div class="progress">
																  <div class="progress-bar" role="progressbar"  data-toggle="tooltip" data-placement="top" title="'.$accuracy.'% '.$QuestionAnsweredCorrectly .'"  style="width: '.$accuracy.'%;display: -webkit-box !important;display: -ms-flexbox !important;display: flex !important;" aria-valuenow="'.$accuracy.'" aria-valuemin="0" aria-valuemax="100">'.$accuracy.'%</div>
																</div>';
														@endphp
													</td>
													@php
														$AlpAiGraphController = new \App\Http\Controllers\Reports\AlpAiGraphController();
														$progress = $AlpAiGraphController->getProgressDetailList($groupTest['groupTest']['exam_ids'], $student_id_in_comm);
													@endphp
													<td>
														<div class="progress">
															@php 
															if($progress['Struggling'] !=0) {
																echo '<div class="progress-bar p-1" data-toggle="tooltip" data-placement="top" title="'.$progress['Struggling'].'%" style="width:'.$progress['Struggling'].'%;background-color: '. App\Helpers\Helper::getGlobalConfiguration('struggling_color').';">'.$progress['Struggling'].'%'.'</div>';
															}
															@endphp
															@php 
															if($progress['Beginning'] !=0) {
																echo '<div class="progress-bar p-1" data-toggle="tooltip" data-placement="top" title="'.$progress['Beginning'].'%" style="width:'.$progress['Beginning'].'%;background-color: '. App\Helpers\Helper::getGlobalConfiguration('beginning_color').';">'.$progress['Beginning'].'%'.'</div>';
															}
															@endphp
															@php 
															if($progress['Approaching'] !=0) {
																echo '<div class="progress-bar p-1" data-toggle="tooltip" data-placement="top" title="'.$progress['Approaching'].'%" style="width:'.$progress['Approaching'].'%;background-color: '. App\Helpers\Helper::getGlobalConfiguration('approaching_color').';">'.$progress['Approaching'].'%'.'</div>';
															}
															@endphp
															@php 
															if($progress['Proficient'] !=0) {
																echo '<div class="progress-bar p-1" data-toggle="tooltip" data-placement="top" title="'.$progress['Proficient'].'%" style="width:'.$progress['Proficient'].'%;background-color: '. App\Helpers\Helper::getGlobalConfiguration('proficient_color').';">'.$progress['Proficient'].'%'.'</div>';
															}
															@endphp
															@php 
															if($progress['Advanced'] !=0) {
																echo '<div class="progress-bar p-1" data-toggle="tooltip" data-placement="top" title="'.$progress['Advanced'].'%" style="width:'.$progress['Advanced'].'%;background-color: '. App\Helpers\Helper::getGlobalConfiguration('advanced_color').';">'.$progress['Advanced'].'%'.'</div>';
															}
															@endphp
															@php 
															if($progress['InComplete'] !=0) {
																echo '<div class="progress-bar p-1" data-toggle="tooltip" data-placement="top" title="'.$progress['InComplete'].'%" style="width:'.$progress['InComplete'].'%;background-color: '. App\Helpers\Helper::getGlobalConfiguration('incomplete_color').';">'.$progress['InComplete'].'%'.'</div>';
															}
															@endphp
														</div>
													</td>
													@php
														$progressQuestions = App\Helpers\Helper::getQuestionDifficultiesLevelPercent($groupTest['groupTest']['exam_ids'], $student_id_in_comm);
													@endphp
													<td>
														<div class="progress">
															@php
																$value = App\Helpers\Helper::getGlobalConfiguration('question_difficulty_easy');
																$color = json_decode($value);
																if($progressQuestions['Easy'] !=0) {
																	echo '<div class="progress-bar p-1" data-toggle="tooltip" data-placement="top" title="'.$progressQuestions['Easy'].'%" style="width:'.$progressQuestions['Easy'].'%;background-color: '.$color->color.';">'.$progressQuestions['Easy'].'%'.'</div>';																
																}
															@endphp
															@php
																$value = App\Helpers\Helper::getGlobalConfiguration('question_difficulty_medium');
																$color = json_decode($value);
																if($progressQuestions['Medium'] !=0) {
																	echo '<div class="progress-bar p-1" data-toggle="tooltip" data-placement="top" title="'.$progressQuestions['Medium'].'%" style="width:'.$progressQuestions['Medium'].'%;background-color: '.$color->color.';">'.$progressQuestions['Medium'].'%'.'</div>';																
																}
															@endphp
															@php
																$value = App\Helpers\Helper::getGlobalConfiguration('question_difficulty_hard');
																$color = json_decode($value);
																if($progressQuestions['Hard'] !=0) {
																	echo '<div class="progress-bar p-1" data-toggle="tooltip" data-placement="top" title="'.$progressQuestions['Hard'].'%" style="width:'.$progressQuestions['Hard'].'%;background-color: '.$color->color.';">'.$progressQuestions['Hard'].'%'.'</div>';																
																}
															@endphp
														</div>
													</td>
													<td class="btn-edit">
														<a href="{{ route('report.class-test-reports.correct-incorrect-answer', ['exam_id' => $groupTest['groupTest']['exam_ids'], 'filter' => 'filter']) }}" title="Class Performance Report"><i class="fa fa-bar-chart" aria-hidden="true"></i></a>
														<a href="javascript:void(0);" title="Class Ability Analysis" class="getClassAbilityAnalysisReport" data-examid="{{$groupTest['groupTest']['exam_ids']}}" data-studentids="{{$groupTest['groupTest']['student_ids']}}">
															<i class="fa fa-bar-chart" aria-hidden="true"></i>
														</a>
														<a href="javascript:void(0);" title="Test Difficulty Analysis" class="getTestDifficultyAnalysisReport" data-examid="{{$groupTest['groupTest']['exam_ids']}}">
															<i class="fa fa-bar-chart" aria-hidden="true"></i>
														</a>
													</td>
												</tr>
												@endif
												@endforeach
											@endif
											<!-- End Group Test -->
										</tbody>
									</table>
								</div>

								<div role="tabpanel" class="tab-pane" id="test">
									<table id="test-table">
										<thead>
											<tr>
												<th><input type="checkbox" name="" class="checkbox"></th>
												<th class="selec-opt"><span>{{__('languages.date')}} & {{__('languages.time')}}</span></th>
												<th class="first-head"><span>{{__('languages.title')}}</span></th>
												<th>{{__('languages.grade')}} - {{__('languages.class')}}</th>
												<th>{{__('languages.no_of_students')}}</th>
												<th>{{__('languages.progress')}}</th>
												<th>{{__('languages.average_accuracy')}}</th>
												<th>{{__('languages.study_status')}}</th>
												<th>{{__('languages.question_difficulties')}}</th>
												<th>{{__('languages.action')}}</th>
											</tr>
										</thead>
										<tbody class="scroll-pane">
											@if(isset($data['testExam']) && !empty($data['testExam']))
											@foreach($data['testExam'] as $grades_class => $testExams)
											@php $examArray = $testExams->toArray(); @endphp
												@if(isset($examArray) && !empty($examArray))
													@foreach($examArray as $testExam)

														@php
															if(isset($studentidlist) && !empty($studentidlist))
															{
																$examStudant=explode(',',$testExam['student_ids']);
																$teacherStudent=array_intersect($studentidlist,$examStudant);
																$student_id_size=sizeof($teacherStudent);
																$student_id_in_comm=implode(',',$teacherStudent);
															}
														@endphp
														<tr>
															<td><input type="checkbox" name="testExam[]" value="{{ $testExam['id']}}" class="checkbox"></td>
															<td>{{ date('d/m/Y H:i:s',strtotime($testExam['created_at'])) }}</td>
															<td>{{ $testExam['title'] }}</td>
															<td>{{ $grades_class }}</td>
															<td>{{ $student_id_size }}</td>
															<td>
																@php
																	
																	$QuestionAnsweredCorrectly = App\Helpers\Helper::getAverageNoOfQuestionAnsweredCorrectly($testExam['id'], $student_id_in_comm);
																@endphp
																@if(isset($teacherStudent))
																	@php
																		$attempt_exams_size=sizeof($testExam['attempt_exams']);
																		$attempt_exams_pr=round(($attempt_exams_size/$student_id_size)*100);
																		if($attempt_exams_pr>100)
																		{
																			$attempt_exams_pr=100;	
																		}
																		echo '<div class="progress">
																		<div class="progress-bar" role="progressbar" data-toggle="tooltip" data-placement="top" title="'.$attempt_exams_pr.'%'.' '.'('.$attempt_exams_size.'/'.$student_id_size.')"style="width: '.$attempt_exams_pr.'%;display: -webkit-box !important;display: -ms-flexbox !important;display: flex !important;" aria-valuenow="'.$attempt_exams_pr.'" aria-valuemin="0" aria-valuemax="100">'.$attempt_exams_pr.'%'.'</div>
																		</div>';
																	@endphp
																@endif
															</td>
															@php
																$accuracy = App\Helpers\Helper::getAccuracyAllStudent($testExam['id'],$student_id_in_comm);
																$ability  = App\Helpers\Helper::getAbility($accuracy);
															@endphp
															
															<td>
																@php
																	echo '<div class="progress">
																	<div class="progress-bar" role="progressbar" data-toggle="tooltip" data-placement="top" title="'.$accuracy.'% '.$QuestionAnsweredCorrectly .'" style="width: '.$accuracy.'%;display: -webkit-box !important;display: -ms-flexbox !important;display: flex !important;" aria-valuenow="'.$accuracy.'" aria-valuemin="0" aria-valuemax="100">'.$accuracy.'%</div>
																	</div>';
																@endphp
															</td>
															@php
																$AlpAiGraphController = new \App\Http\Controllers\Reports\AlpAiGraphController();
																$progress = $AlpAiGraphController->getProgressDetailList($testExam['id'],$student_id_in_comm);
															@endphp
															<td>
																<div class="progress" style="height: 1rem">
																	@php 
																	if($progress['Struggling'] != 0) {
																		echo '<div class="progress-bar" data-toggle="tooltip" data-placement="top" title="'.$progress['Struggling'].'%" style="width:'.$progress['Struggling'].'%;background-color: '. App\Helpers\Helper::getGlobalConfiguration('struggling_color').';">'.$progress['Struggling'].'%'.'</div>';
																	}
																	@endphp
																	@php 
																	if($progress['Beginning'] != 0) {
																		echo '<div class="progress-bar" data-toggle="tooltip" data-placement="top" title="'.$progress['Beginning'].'%" style="width:'.$progress['Beginning'].'%;background-color: '. App\Helpers\Helper::getGlobalConfiguration('beginning_color').';">'.$progress['Beginning'].'%'.'</div>';
																	}
																	@endphp
																	@php 
																	if($progress['Approaching'] != 0) {
																		echo '<div class="progress-bar" data-toggle="tooltip" data-placement="top" title="'.$progress['Approaching'].'%" style="width:'.$progress['Approaching'].'%;background-color: '. App\Helpers\Helper::getGlobalConfiguration('approaching_color').';">'.$progress['Approaching'].'%'.'</div>';
																	}
																	@endphp
																	@php 
																	if($progress['Proficient'] != 0) {
																		echo '<div class="progress-bar" data-toggle="tooltip" data-placement="top" title="'.$progress['Proficient'].'%" style="width:'.$progress['Proficient'].'%;background-color: '. App\Helpers\Helper::getGlobalConfiguration('proficient_color').';">'.$progress['Proficient'].'%'.'</div>';
																	}
																	@endphp
																	@php 
																	if($progress['Advanced'] != 0) {
																		echo '<div class="progress-bar" data-toggle="tooltip" data-placement="top" title="'.$progress['Advanced'].'%" style="width:'.$progress['Advanced'].'%;background-color: '. App\Helpers\Helper::getGlobalConfiguration('advanced_color').';">'.$progress['Advanced'].'%'.'</div>';
																	}
																	@endphp
																	@php 
																	if($progress['InComplete'] != 0) {
																		echo '<div class="progress-bar" data-toggle="tooltip" data-placement="top" title="'.$progress['InComplete'].'%" style="width:'.$progress['InComplete'].'%;background-color: '. App\Helpers\Helper::getGlobalConfiguration('incomplete_color').';">'.$progress['InComplete'].'%'.'</div>';
																	}
																	@endphp
																</div>
															</td>
															@php
																$progressQuestions = App\Helpers\Helper::getQuestionDifficultiesLevelPercent($testExam['id'],$student_id_in_comm);
															@endphp
															<td>
																<div class="progress" style="height: 1rem">
																	@php
																		$value = App\Helpers\Helper::getGlobalConfiguration('question_difficulty_easy');
																		$color = json_decode($value);
																		if($progressQuestions['Easy'] !=0) {
																			echo '<div class="progress-bar" data-toggle="tooltip" data-placement="top" title="'.$progressQuestions['Easy'].'%" style="width:'.$progressQuestions['Easy'].'%;background-color: '.$color->color.';">'.$progressQuestions['Easy'].'%'.'</div>';																
																		}
																	@endphp
																	@php
																		$value = App\Helpers\Helper::getGlobalConfiguration('question_difficulty_medium');
																		$color = json_decode($value);
																		if($progressQuestions['Medium'] !=0) {
																			echo '<div class="progress-bar" data-toggle="tooltip" data-placement="top" title="'.$progressQuestions['Medium'].'%" style="width:'.$progressQuestions['Medium'].'%;background-color: '.$color->color.';">'.$progressQuestions['Medium'].'%'.'</div>';																
																		}
																	@endphp
																	@php
																		$value = App\Helpers\Helper::getGlobalConfiguration('question_difficulty_hard');
																		$color = json_decode($value);
																		if($progressQuestions['Hard'] !=0) {
																			echo '<div class="progress-bar" data-toggle="tooltip" data-placement="top" title="'.$progressQuestions['Hard'].'%" style="width:'.$progressQuestions['Hard'].'%;background-color: '.$color->color.';">'.$progressQuestions['Hard'].'%'.'</div>';																
																		}
																	@endphp
																</div>
															</td>
															<td class="btn-edit">
																<a href="{{ route('report.class-test-reports.correct-incorrect-answer', ['exam_id' => $testExam['id'], 'filter' => 'filter']) }}" title="Class Performance Report"><i class="fa fa-bar-chart" aria-hidden="true"></i></a>
																<a href="javascript:void(0);" title="Class Ability Analysis" class="getClassAbilityAnalysisReport" data-examid="{{$testExam['id']}}" data-studentids="{{$testExam['student_ids']}}">
																	<i class="fa fa-bar-chart" aria-hidden="true"></i>
																</a>
																<a href="javascript:void(0);" title="Test Difficulty Analysis" class="getTestDifficultyAnalysisReport" data-examid="{{$testExam['id']}}">
																	<i class="fa fa-bar-chart" aria-hidden="true"></i>
																</a>
															</td>
														</tr>
														@endforeach
												@endif
											@endforeach
											@endif
											<!-- Is Group Test -->
											@if(isset($GroupTestData['test']))
												@foreach($GroupTestData['test'] as $grades_class => $groupTest)
												@if(isset($groupTest['examDetail']) && $groupTest['examDetail']['status'] == 'publish' && $groupTest['examDetail']['exam_type'] == 3 )

												@php
													if(isset($studentidlist) && !empty($studentidlist))
													{
														$examStudant=explode(',',$groupTest['groupTest']['student_ids']);
														$teacherStudent=array_intersect($studentidlist,$examStudant);
														$student_id_size=sizeof($teacherStudent);
														$student_id_in_comm=implode(',',$teacherStudent);
													}
												@endphp
												<tr>
													<td><input type="checkbox" value="{{ $groupTest['groupTest']['exam_ids'] }}" name="testExam[]" class="checkbox"></td>
													<td>{{date('d/m/Y H:i:s',strtotime($groupTest['examDetail']['created_at']))}}</td>
													<td>{{ $groupTest['groupTest']['name'] }}</td>
													<td>{{ $grades_class }}</td>
													<td>{{ $student_id_size }}</td>
													<td>
														@if(isset($groupTest['examDetail']['attempt_exams']))
															@php
																$attempt_exams_size = sizeof($groupTest['examDetail']['attempt_exams']);
																$attempt_exams_pr = round(($attempt_exams_size/$student_id_size)*100);
																if($attempt_exams_pr>100)
																{
																	$attempt_exams_pr=100;	
																}
																echo '<div class="progress">
																<div class="progress-bar" role="progressbar" data-toggle="tooltip" data-placement="top" title="'.$attempt_exams_pr.'%'.' '.'('.$attempt_exams_size.'/'. $student_id_size.')" style="width: '.$attempt_exams_pr.'%;display: -webkit-box !important;display: -ms-flexbox !important;display: flex !important;" aria-valuenow="'.$attempt_exams_pr.'" aria-valuemin="0" aria-valuemax="100">'.$attempt_exams_pr.'%'.'</div>
																</div>';
															@endphp
														@endif
													</td>
													@php
														$accuracy = App\Helpers\Helper::getAccuracyAllStudent($groupTest['groupTest']['exam_ids'], $groupTest['groupTest']['student_ids']);
														$QuestionAnsweredCorrectly = App\Helpers\Helper::getAverageNoOfQuestionAnsweredCorrectly($groupTest['groupTest']['exam_ids'], $groupTest['groupTest']['student_ids']);
													@endphp
													<td>
														@php
															 echo '<div class="progress">
															<div class="progress-bar" role="progressbar"  data-toggle="tooltip" data-placement="top" title="'.$accuracy.'% '.$QuestionAnsweredCorrectly .'"  style="width: '.$accuracy.'%;display: -webkit-box !important;display: -ms-flexbox !important;display: flex !important;" aria-valuenow="'.$accuracy.'" aria-valuemin="0" aria-valuemax="100">'.$accuracy.'%</div>
															</div>';
														@endphp
													</td>
													@php
														$AlpAiGraphController = new \App\Http\Controllers\Reports\AlpAiGraphController();
														$progress = $AlpAiGraphController->getProgressDetailList($groupTest['groupTest']['exam_ids'], $student_id_in_comm);
													@endphp
													<td>
														<div class="progress" style="height: 1rem">
															@php 
															if($progress['Struggling'] != 0) {
																echo '<div class="progress-bar" data-toggle="tooltip" data-placement="top" title="'.$progress['Struggling'].'%" style="width:'.$progress['Struggling'].'%;background-color: '. App\Helpers\Helper::getGlobalConfiguration('struggling_color').';">'.$progress['Struggling'].'%'.'</div>';
															}
															@endphp
															@php 
															if($progress['Beginning'] != 0) {
																echo '<div class="progress-bar" data-toggle="tooltip" data-placement="top" title="'.$progress['Beginning'].'%" style="width:'.$progress['Beginning'].'%;background-color: '. App\Helpers\Helper::getGlobalConfiguration('beginning_color').';">'.$progress['Beginning'].'%'.'</div>';
															}
															@endphp
															@php 
															if($progress['Approaching'] != 0) {
																echo '<div class="progress-bar" data-toggle="tooltip" data-placement="top" title="'.$progress['Approaching'].'%" style="width:'.$progress['Approaching'].'%;background-color: '. App\Helpers\Helper::getGlobalConfiguration('approaching_color').';">'.$progress['Approaching'].'%'.'</div>';
															}
															@endphp
															@php 
															if($progress['Proficient'] != 0) {
																echo '<div class="progress-bar" data-toggle="tooltip" data-placement="top" title="'.$progress['Proficient'].'%" style="width:'.$progress['Proficient'].'%;background-color: '. App\Helpers\Helper::getGlobalConfiguration('proficient_color').';">'.$progress['Proficient'].'%'.'</div>';
															}
															@endphp
															@php 
															if($progress['Advanced'] != 0) {
																echo '<div class="progress-bar" data-toggle="tooltip" data-placement="top" title="'.$progress['Advanced'].'%" style="width:'.$progress['Advanced'].'%;background-color: '. App\Helpers\Helper::getGlobalConfiguration('advanced_color').';">'.$progress['Advanced'].'%'.'</div>';
															}
															@endphp
															@php 
															if($progress['InComplete'] != 0) {
																echo '<div class="progress-bar" data-toggle="tooltip" data-placement="top" title="'.$progress['InComplete'].'%" style="width:'.$progress['InComplete'].'%;background-color: '. App\Helpers\Helper::getGlobalConfiguration('incomplete_color').';">'.$progress['InComplete'].'%'.'</div>';
															}
															@endphp
														</div>
													</td>
													@php
														$progressQuestions = App\Helpers\Helper::getQuestionDifficultiesLevelPercent($groupTest['groupTest']['exam_ids'],$student_id_in_comm);
													@endphp
													<td>
														<div class="progress" style="height: 1rem">
															@php
																$value = App\Helpers\Helper::getGlobalConfiguration('question_difficulty_easy');
																$color = json_decode($value);
																if($progressQuestions['Easy'] !=0) {
																	echo '<div class="progress-bar" data-toggle="tooltip" data-placement="top" title="'.$progressQuestions['Easy'].'%" style="width:'.$progressQuestions['Easy'].'%;background-color: '.$color->color.';">'.$progressQuestions['Easy'].'%'.'</div>';																
																}
															@endphp
															@php
																$value = App\Helpers\Helper::getGlobalConfiguration('question_difficulty_medium');
																$color = json_decode($value);
																if($progressQuestions['Medium'] !=0) {
																	echo '<div class="progress-bar" data-toggle="tooltip" data-placement="top" title="'.$progressQuestions['Medium'].'%" style="width:'.$progressQuestions['Medium'].'%;background-color: '.$color->color.';">'.$progressQuestions['Medium'].'%'.'</div>';																
																}
															@endphp
															@php
																$value = App\Helpers\Helper::getGlobalConfiguration('question_difficulty_hard');
																$color = json_decode($value);
																if($progressQuestions['Hard'] !=0) {
																	echo '<div class="progress-bar" data-toggle="tooltip" data-placement="top" title="'.$progressQuestions['Hard'].'%" style="width:'.$progressQuestions['Hard'].'%;background-color: '.$color->color.';">'.$progressQuestions['Hard'].'%'.'</div>';																
																}
															@endphp
														</div>
													</td>
													<td class="btn-edit">
														<a href="{{ route('report.class-test-reports.correct-incorrect-answer', ['exam_id' => $groupTest['groupTest']['exam_ids'],'filter' => 'filter']) }}" title="Class Performance Report"><i class="fa fa-bar-chart" aria-hidden="true"></i></a>
														<a href="javascript:void(0);" title="Class Ability Analysis" class="getClassAbilityAnalysisReport" data-examid="{{$groupTest['groupTest']['exam_ids']}}" data-studentids="{{$groupTest['groupTest']['student_ids']}}">
															<i class="fa fa-bar-chart" aria-hidden="true"></i>
														</a>
														<a href="javascript:void(0);" title="Test Difficulty Analysis" class="getTestDifficultyAnalysisReport" data-examid="{{$groupTest['groupTest']['exam_ids']}}">
															<i class="fa fa-bar-chart" aria-hidden="true"></i>
														</a>
													</td>
												</tr>
												@endif
												@endforeach
											@endif
											<!-- End Group Test -->
										</tbody>
									</table>
								</div>
							</div>
	                  	</div>
	               	</div>
	               	<div class="col-lg-3 col-sm-12">
	               		<div class="sm-vedio-pdf-doc-sec">
							<div class="sec-title">
								<h3>{{__('languages.upload_document.document_list')}}</h3>
							</div>
						</div>
	               	</div>
	            </div>
	        @endif
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
<div class="modal fade" id="my-study-config" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static">
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
					@if($roleId==2)
					{{-- <input type="hidden" name="grade_id" value="{{ $grade_id }}"> --}}
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
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">{{__('languages.close')}}</button>
					<button type="submit" class="btn btn-primary">{{__('languages.submit')}}</button>
				</div>
			</form>
		</div>
	</div>
</div>
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
<div class="modal fade" id="imgModal" tabindex="-1" aria-labelledby="imgModalLabel" aria-hidden="true" data-backdrop="static">
	<div class="modal-dialog modal-dialog modal-lg">
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

{{-- <script src="{{ asset('charts/ChartNew.js') }}"></script>
<script src="{{ asset('charts/Add-ins/stats.js') }}"></script> --}}

<!-- Start Performance Analysis Popup -->
<div class="modal" id="class-ability-analysis-report" tabindex="-1" aria-labelledby="class-ability-analysis-report" aria-hidden="true" data-backdrop="static">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<form method="post">
				<input type="hidden" name="grade_ids" id="grade_ids" value="">
				<input type="hidden" name="exam_ids" id="exam_ids" value="">
				<input type="hidden" name="student_ids" id="student_ids" value="">
				<div class="modal-header">
					<h4 class="modal-title w-100">{{__('languages.class_ability_analysis')}}</h4>
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				</div>
				<div class="modal-body">
					<div class="row pb-2">
						<div class="col-md-4">
							<button type="button" class="btn btn-primary class-ability-graph-btn" data-graphtype="my-class">{{__('languages.my_class.my_classes')}}</button>
						</div>
						<div class="col-md-4">
							<button type="button" class="btn btn-primary class-ability-graph-btn" data-graphtype="my-school">My School</button>
						</div>
						<div class="col-md-4">
							<button type="button" class="btn btn-primary class-ability-graph-btn" data-graphtype="all-school">All School</button>
						</div>
					</div>
					<div class="row">
						<img src="" id="class-ability-analysis-report-image" class="img-fluid">
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">{{__('languages.close')}}</button>
				</div>
			</form>
		</div>
	</div>
</div>
<!-- End Performance Analysis Popup -->

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
function getYoutubeId(url) {
	const regExp = /^.*(youtu.be\/|v\/|u\/\w\/|embed\/|watch\?v=|&v=)([^#&?]*).*/;
	const match = url.match(regExp);
	return (match && match[2].length === 11) ? match[2] : null;
}
$(function() {

	$(document).on('click', '.test-tab', function() {
		$('.test-tab').removeClass('active');
		$('.tab-pane').removeClass('active');
		$('#'+$(this).attr('data-id')).addClass('active');
		$(this).addClass('active');
		$('#documentbtn form .active_tab').val($(this).attr('data-id'));
		$.cookie("PreviousTab", $(this).attr('data-id'));
		// if($(this).attr('data-id') == 'exercise'){
		// 	$('.study_status_colors').show();
		// }
		// else if($(this).attr('data-id') == 'self_learning'){
		// 	$('.study_status_colors').hide();
		// }
		// else{
		// 	$('.study_status_colors').show();
		// }
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
		$('#tab-self-learning').addClass('active');
		$('#self_learning').addClass('active');
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
	if(listExamIdDoc.length!=0){
		$.ajax({
			url: BASE_URL + '/study-documents',
			type: 'POST',
			data : {
				'_token': $('meta[name="csrf-token"]').attr('content'),
				'list_exam_id' : listExamIdDoc,
				@if($roleId==2)
				'student_id' : {{ $student_id }},
				@endif
			},
			success: function(response) {
				$(".sm-vedio-pdf-doc-sec").html(response);
			}
		});
	}else{
		$(".sm-vedio-pdf-doc-sec").html('<div class="sec-title"><h3>'+DOCUMENT_LIST+'</h3></div><p class="text-center">'+NO_ANY_DOCUMENT_AVAILABLE+'</p>');
	}

	$("#videoModal .close").click(function () {
		$("#videoModal #videoDis").attr('src','');
	});

	$.each($(".categories-main-list input[type=checkbox][name='strands[]']"), function() {
		var listConfigIdList= new Array();
		listConfigIdList.push($(this).val());
		var maindata=$(this);
		$.ajax({
			url: BASE_URL + '/estimate_student_competence_web',
			type: 'POST',
			data : {
				'_token': $('meta[name="csrf-token"]').attr('content'),
				'list_strands_id' : listConfigIdList,
				@if($roleId==2)
				'student_id' : {{ $student_id }},
				@endif
			},
			success: function(response) {
				if(response.data.length!=0){
					var mainDataVal=maindata.val();
					var mainDataName=maindata.attr('name');
					if($(".categories-main-list input[type=checkbox][name='"+mainDataName+"'][value="+mainDataVal+"]").length!=0){
						var classAdd='up-50';
						if(response<=49){
							classAdd='down-50';
						}
						var labelData=$(".categories-main-list input[type=checkbox][name='"+mainDataName+"'][value="+mainDataVal+"]").parent();
						labelData.find('.label-percentage:eq(0)').text(response.data[0]+'%').show();
						labelData.find('input[type=range]:eq(0)').val(response.data[0]).attr('class',classAdd).show();
					}
					$("#cover-spin").hide();
				}else{
					var mainDataVal=maindata.val();
					var mainDataName=maindata.attr('name');
					if($(".categories-main-list input[type=checkbox][name='"+mainDataName+"'][value="+mainDataVal+"]").length!=0){
						var responseData=0;
						var classAdd='up-50';
						if(responseData<=49){
							classAdd='down-50';
						}
						var labelData=$(".categories-main-list input[type=checkbox][name='"+mainDataName+"'][value="+mainDataVal+"]").parent();
						labelData.find('.label-percentage:eq(0)').text(responseData+'%').show();
						labelData.find('input[type=range]:eq(0)').val(responseData).attr('class',classAdd).show();
					}
				}
			}
		});
	});
	
	$(document).on('click',".categories-main-list a.collapse-category", function() {
		if($(this).hasClass('open')){
			$(this).parent().find(' > ul > li > input[type=checkbox]').each(function(){
				$("#cover-spin").show();
				var var_data=new Array($(this).val());
				var var_name=$(this).attr('name').replace('[]','');
				var maindata=$(this);
				$.ajax({
					url: BASE_URL + '/estimate_student_competence_web',
					type: 'POST',
					data : {
						'_token': $('meta[name="csrf-token"]').attr('content'),
						[var_name]: var_data,
						@if($roleId==2)
						'student_id' : {{ $student_id }},
						@endif
					},
					success: function(response) {
						if(response.data.length!=0){
							var mainDataVal=maindata.val();
							var mainDataName=maindata.attr('name');
							if($(".categories-main-list input[type=checkbox][name='"+mainDataName+"'][value="+mainDataVal+"]").length!=0){
								var classAdd='up-50';
								if(response<=49){
									classAdd='down-50';
								}
								var labelData=$(".categories-main-list input[type=checkbox][name='"+mainDataName+"'][value="+mainDataVal+"]").parent();
								labelData.find('.label-percentage:eq(0)').text(response.data[0]+'%').show();
								labelData.find('input[type=range]:eq(0)').val(response.data[0]).attr('class',classAdd).show();
							}
						}else{
							var mainDataVal=maindata.val();
							var mainDataName=maindata.attr('name');
							if($(".categories-main-list input[type=checkbox][name='"+mainDataName+"'][value="+mainDataVal+"]").length!=0){
								var responseData=0;
								var classAdd='up-50';
								if(responseData<=49){
									classAdd='down-50';
								}
								var labelData=$(".categories-main-list input[type=checkbox][name='"+mainDataName+"'][value="+mainDataVal+"]").parent();
								labelData.find('.label-percentage:eq(0)').text(responseData+'%').show();
								labelData.find('input[type=range]:eq(0)').val(responseData).attr('class',classAdd).show();
							}
						}
						$("#cover-spin").hide();
					}
				});
			});
		}
	});
	
	$(document).on('change', '#AllTabs', function() {
		if($(this).prop('checked')){
			$(".categories-main-list .categories-list input[type=checkbox]").prop('checked',true);
		}else{
			$(".categories-main-list .categories-list input[type=checkbox]").prop('checked',false);
		}
	});
	
	$(document).on('change', ".categories-main-list .categories-list input[type=checkbox]", function() {
		var allchecklen=$(".categories-main-list .categories-list input[type=checkbox][name='strands[]']:checked").length;
		var allunchecklen=$(".categories-main-list .categories-list input[type=checkbox][name='strands[]']").length;
		if(allchecklen==allunchecklen){
			$('#AllTabs').prop('checked',true);
		}else{
			$('#AllTabs').prop('checked',false);
		}
	});
});

function getRandomNumber(){
	return Math.floor(Math.random() * 101);
}
</script>
<script type="text/javascript">
$.fn.cascadeCheckboxes = function() {
	$.fn.checkboxParent = function() {
		//to determine if checkbox has parent checkbox element
		var checkboxParent = $(this).parent("li").parent("ul").parent("li").find('> input[type="checkbox"]');
		return checkboxParent;
	};
	$.fn.checkboxChildren = function() {
		//to determine if checkbox has child checkbox element
		var checkboxChildren = $(this).parent("li").find('> .subcategories > li > input[type="checkbox"]');
		return checkboxChildren;
	};
	$.fn.cascadeUp = function() {
		var checkboxParent = $(this).checkboxParent();
		if ($(this).prop("checked")) {
			if (checkboxParent.length) {
				//check if all children of the parent are selected - if yes, select the parent
				//these will be the siblings of the element which we clicked on
				var children = $(checkboxParent).checkboxChildren();
				var booleanChildren = $.map(children, function(child, i) {
					return $(child).prop("checked");
				});
				//check if all children are checked
				var allChecked = booleanChildren.filter(function(x) {return !x})
				//if there are no false elements, parent is selected
				if (!allChecked.length) {
					$(checkboxParent).prop("checked", true);
					$(checkboxParent).cascadeUp();
				}
			}
		} else {
			if (checkboxParent.length) {
				//if parent is checked, becomes unchecked
				$(checkboxParent).prop("checked", false);
				$(checkboxParent).cascadeUp();
			}
		}
	};
	$.fn.cascadeDown = function() {
		var checkboxChildren = $(this).checkboxChildren();
		if (checkboxChildren.length) {
			checkboxChildren.prop("checked", $(this).prop("checked"));
			checkboxChildren.each(function(index) {
				$(this).cascadeDown();
			});
		}
	}
	$(this).cascadeUp();
	$(this).cascadeDown();
};

$("input[type=checkbox]:not(:disabled)").on("change", function() {
	$(this).cascadeCheckboxes();
});
$(".category a").on("click", function(e) {
	e.preventDefault();
	$(this).parent().find("> .subcategories").slideToggle(function() {
		if ($(this).is(":visible")) $(this).css("display", "flex");
	});
});
$('.collapse-category').on("click", function(){
	if($(this).hasClass('close')){
		$(this).removeClass('close');
		$(this).addClass('open');
	}else{
		$(this).removeClass('open');
		$(this).addClass('close');
	}
});

$(function() {
	/**
	 * USE : Display on graph Get Class APerformance Analysis
	 * Trigger : On click Performance graph icon into exams list action table
	 * **/
	$(document).on('click', '.getClassAbilityAnalysisReport', function(e) {
		$("#cover-spin").show();
		$('#class-ability-analysis-report').modal('show');
		$studentIds = $(this).attr('data-studentids');
		$examId = $(this).attr('data-examid');
		$('#exam_ids').val($examId);
		$('#student_ids').val($studentIds);
		if($studentIds && $examId){
			$.ajax({
				url: BASE_URL + '/my-teaching/get-class-ability-analysis-report',
				type: 'post',
				data : {
					'_token': $('meta[name="csrf-token"]').attr('content'),
					'examid' : $examId,
					'studentIds' : $studentIds,
					'graph_type' : 'my-class'
				},
				success: function(response) {
					var ResposnseData = JSON.parse(JSON.stringify(response));
					if(ResposnseData.data != 0){
						// Append image src attribute with base64 encode image
						$('#class-ability-analysis-report-image').attr('src','data:image/jpg;base64,'+ ResposnseData.data);
						$('#class-ability-analysis-report').modal('show');
					}else{
						toastr.error(DATA_NOT_FOUND);
					}
					$("#cover-spin").hide();
				},
				error: function(response) {
					ErrorHandlingMessage(response);
				}
			});
		}
	});

	/**
	 * USE : Click on the diffrent button like this 'my-class', 'my-school', 'all-school'
	 * **/
	$(document).on('click', '.class-ability-graph-btn', function(e) {
		$("#cover-spin").show();
		$studentIds = $('#student_ids').val();
		$examId = $('#exam_ids').val();
		if($studentIds && $examId){
			$.ajax({
				url: BASE_URL + '/my-teaching/get-class-ability-analysis-report',
				type: 'post',
				data : {
					'_token': $('meta[name="csrf-token"]').attr('content'),
					'examid' : $examId,
					'studentIds' : $studentIds,
					'graph_type' : $(this).attr('data-graphtype')
				},
				success: function(response) {
					var ResposnseData = JSON.parse(JSON.stringify(response));
					if(ResposnseData.data != 0){
						// Append image src attribute with base64 encode image
						$('#class-ability-analysis-report-image').attr('src','data:image/jpg;base64,'+ ResposnseData.data);
						$('#class-ability-analysis-report').modal('show');
					}else{
						toastr.error(DATA_NOT_FOUND);
					}
					$("#cover-spin").hide();
				},
				error: function(response) {
					ErrorHandlingMessage(response);
				}
			});
		}
	});

	// /**
	//  * USE : Display on graph Get Test Difficulty Analysis Report
	//  * Trigger : On click getTestDifficultyAnalysisReport icon into exams list action table
	//  * **/
	//  $(document).on('click', '.getTestDifficultyAnalysisReport', function(e) {
	// 	$("#cover-spin").show();
	// 	$examId = $(this).attr('data-examid');
	// 	if($examId){
	// 		$.ajax({
	// 			url: BASE_URL + '/my-teaching/get-test-difficulty-analysis-report',
	// 			type: 'post',
	// 			data : {
	// 				'_token': $('meta[name="csrf-token"]').attr('content'),
	// 				'examid' : $examId
	// 			},
	// 			success: function(response) {
	// 				var ResposnseData = JSON.parse(JSON.stringify(response));
	// 				if(ResposnseData.data != 0){
	// 					// Append image src attribute with base64 encode image
	// 					$('#test-difficulty-analysis-report-image').attr('src','data:image/jpg;base64,'+ ResposnseData.data);
	// 					$('#test-difficulty-analysis-report').modal('show');
	// 				}else{
	// 					toastr.error('Data not found');
	// 				}
	// 				$("#cover-spin").hide();
	// 			},
	// 			error: function(response) {
	// 				ErrorHandlingMessage(response);
	// 			}
	// 		});
	// 	}
	// });
});
</script>
@endsection