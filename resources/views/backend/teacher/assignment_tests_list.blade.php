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
										<tbody class="scroll-pane">
											@if(isset($data['exerciseExam']) && !empty($data['exerciseExam']))
											@foreach($data['exerciseExam'] as $grades_class =>  $exerciseExams)
												@php $examArray = $exerciseExams->toArray(); @endphp
												@if(isset($examArray) && !empty($examArray))
													@foreach($examArray as $exerciseExam)
														@php
															$studentidlist = [];
															if(isset($gradeClassAvailableStudents) && !empty($gradeClassAvailableStudents)){
																$studentidlist = $gradeClassAvailableStudents[$grades_class];
															}
															if(isset($studentidlist) && !empty($studentidlist)){
																$examStudant = explode(',',$exerciseExam['student_ids']);
																$teacherStudent = array_intersect($studentidlist,$examStudant);
																$student_id_size = sizeof($teacherStudent);
																$student_id_in_comm = implode(',',$teacherStudent);
															}
														@endphp
														<tr @if($data['exerciseExam']) class='exercise-exam' @endif>
															<td><input type="checkbox" value="{{ $exerciseExam['id'] }}" name="exerciseExam[]" class="checkbox"></td>
															<td>{{ date('d/m/Y H:i:s',strtotime($exerciseExam['created_at'])) }}</td>
															<td>{{ $exerciseExam['title'] }}</td>
															<td>{{ $grades_class }}</td>
															<td>{{ count($teacherStudent); }}</td>
															@php																
																$accuracy = App\Helpers\Helper::getAccuracyAllStudent($exerciseExam['id'], $student_id_in_comm);
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
																		echo '<div class="progress student-progress-report" data-examid="'.$exerciseExam['id'].'"  data-studentids="'.$student_id_in_comm.'">
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
															<td class="study-status-progressbar-td">
																<div class="progress">
																	@php 
																	if($progress['Struggling'] != 0) {
																		echo '<div class="progress-bar p-0" data-toggle="tooltip" data-placement="top" title="'.$progress['Struggling'].'%" style="width:'.$progress['Struggling'].'%;background-color: '. App\Helpers\Helper::getGlobalConfiguration('struggling_color').';">'.$progress['Struggling'].'%'.'</div>';
																	}
																	if($progress['Beginning'] != 0) {
																		echo '<div class="progress-bar p-0" data-toggle="tooltip" data-placement="top" title="'.$progress['Beginning'].'%" style="width:'.$progress['Beginning'].'%;background-color: '. App\Helpers\Helper::getGlobalConfiguration('beginning_color').';">'.$progress['Beginning'].'%'.'</div>';
																	}
																	if($progress['Approaching'] != 0) {
																		echo '<div class="progress-bar p-0" data-toggle="tooltip" data-placement="top" title="'.$progress['Approaching'].'%" style="width:'.$progress['Approaching'].'%;background-color: '. App\Helpers\Helper::getGlobalConfiguration('approaching_color').';">'.$progress['Approaching'].'%'.'</div>';
																	}
																	if($progress['Proficient'] != 0) {
																		echo '<div class="progress-bar p-0" data-toggle="tooltip" data-placement="top" title="'.$progress['Proficient'].'%" style="width:'.$progress['Proficient'].'%;background-color: '. App\Helpers\Helper::getGlobalConfiguration('proficient_color').';">'.$progress['Proficient'].'%'.'</div>';
																	}
																	if($progress['Advanced'] != 0) {
																		echo '<div class="progress-bar p-0" data-toggle="tooltip" data-placement="top" title="'.$progress['Advanced'].'%" style="width:'.$progress['Advanced'].'%;background-color: '. App\Helpers\Helper::getGlobalConfiguration('advanced_color').';">'.$progress['Advanced'].'%'.'</div>';
																	}
																	if($progress['InComplete'] != 0) {
																		echo '<div class="progress-bar p-0" data-toggle="tooltip" data-placement="top" title="'.$progress['InComplete'].'%" style="width:'.$progress['InComplete'].'%;background-color: '. App\Helpers\Helper::getGlobalConfiguration('incomplete_color').';">'.$progress['InComplete'].'%'.'</div>';
																	}
																	@endphp
																</div>
															</td>
															@php
																$progressQuestions = App\Helpers\Helper::getQuestionDifficultiesLevelPercent($exerciseExam['id'],$student_id_in_comm);
															@endphp
															<td class="question-difficulty-level-td">
																<div class="progress" >
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
																<a href="{{ route('report.class-test-reports.correct-incorrect-answer', ['exam_id' => $exerciseExam['id'], 'filter' => 'filter']) }}" title="Class Performance Report"><i class="fa fa-bar-chart" aria-hidden="true"></i></a>
																<a href="javascript:void(0);" title="Class Ability Analysis" class="getClassAbilityAnalysisReport" data-examid="{{$exerciseExam['id']}}" data-studentids="{{$exerciseExam['student_ids']}}">
																	<i class="fa fa-bar-chart" aria-hidden="true"></i>
																</a>
																<a href="javascript:void(0);" title="Test Difficulty Analysis" class="getTestDifficultyAnalysisReport" data-examid="{{$exerciseExam['id']}}">
																	<i class="fa fa-bar-chart" aria-hidden="true"></i>
																</a>
																@php
																	if(isset($grades_class) && !empty($grades_class)){
																		$gradesClass=explode('-',$grades_class);
																	}
																@endphp
																<a href="javascript:void(0);" class="exam_info ml-2" data-examid="{{$exerciseExam['id']}}" data-grade-id="{{ $gradesClass[0] }}"><i class="fa fa-gear" aria-hidden="true"></i></a>
																<a href="javascript:void(0);" class="exam_questions-info ml-2" data-examid="{{$exerciseExam['id']}}"><i class="fa fa-book" aria-hidden="true"></i></a>
															</td>
														</tr>
													@endforeach
												@endif
											@endforeach
											@endif
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
															$studentidlist = [];
															if(isset($gradeClassAvailableStudents) && !empty($gradeClassAvailableStudents)){
																$studentidlist = $gradeClassAvailableStudents[$grades_class];
															}

															if(isset($studentidlist) && !empty($studentidlist)){
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
																		$attempt_exams_size = sizeof($testExam['attempt_exams']);
																		if($student_id_size){
																			$attempt_exams_pr = round(($attempt_exams_size/$student_id_size)*100);
																		}else{
																			$attempt_exams_pr = 0;
																		}
																		
																		if($attempt_exams_pr > 100){
																			$attempt_exams_pr = 100;	
																		}
																		echo '<div class="progress student-progress-report" data-examid="'.$testExam['id'].'"  data-studentids="'.$student_id_in_comm.'">
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
															<td class="study-status-progressbar-td">
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
															<td class="question-difficulty-level-td">
																<div class="progress" style="height: 1rem">
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
																<a href="{{ route('report.class-test-reports.correct-incorrect-answer', ['exam_id' => $testExam['id'], 'filter' => 'filter']) }}" title="Class Performance Report"><i class="fa fa-bar-chart" aria-hidden="true"></i></a>
																<a href="javascript:void(0);" title="Class Ability Analysis" class="getClassAbilityAnalysisReport" data-examid="{{$testExam['id']}}" data-studentids="{{$testExam['student_ids']}}">
																	<i class="fa fa-bar-chart" aria-hidden="true"></i>
																</a>
																<a href="javascript:void(0);" title="Test Difficulty Analysis" class="getTestDifficultyAnalysisReport" data-examid="{{$testExam['id']}}">
																	<i class="fa fa-bar-chart" aria-hidden="true"></i>
																</a>
																<a href="javascript:void(0);" class="exam_questions-info ml-2" data-examid="{{$testExam['id']}}"><i class="fa fa-book" aria-hidden="true"></i></a>
															</td>
														</tr>
														@endforeach
												@endif
											@endforeach
											@endif
										</tbody>
									</table>
								</div>
							</div>
	                  	</div>
	               	</div>
	               	{{-- <div class="col-lg-3 col-sm-12">
	               		<div class="sm-vedio-pdf-doc-sec">
							<div class="sec-title">
								<h3>{{__('languages.upload_document.document_list')}}</h3>
							</div>
						</div>
	               	</div> --}}
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

<!-- Start Student create self learning test Popup -->
<div class="modal" id="studentCreateSelfLearningTestModal" tabindex="-1" aria-labelledby="studentCreateSelfLearningTestModal" aria-hidden="true" data-backdrop="static">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<form class="student-generate-test-form" method="get" id="student-generate-test-form">
				<div class="modal-header">
					<h4 class="modal-title w-100">{{__('languages.generate_self_learning')}} {{__('languages.excercise')}} & {{__('languages.generate_self_learning')}} {{__('languages.test_text')}}</h4>
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				</div>
				<div class="modal-body">
					<input type="hidden" name="grade_id" value="{{ Auth::user()->grade_id }}" id="grade-id">
					<input type="hidden" name="subject_id" value="1" id="subject-id">
					<input type="hidden" name="question_ids" value="" id="question-ids">
					<input type="hidden" name="self_learning_test_type" value="" id="self_learning_test_type">
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
								<option value="{{$difficultyLevel->difficulty_level}}">{{$difficultyLevel->difficulty_level_name}}</option>
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
					<button type="button" class="close class-ability-analysis-report-close-pop" data-dismiss="modal" aria-hidden="true">&times;</button>
				</div>
				<div class="modal-body">
					<div class="row pb-3">
						<div class="col-md-4 text-center">
							<button type="button" class="btn btn-primary class-ability-graph-btn" data-graphtype="my-class">{{__('languages.my_class.my_classes')}}</button>
						</div>
						<div class="col-md-4 text-center">
							<button type="button" class="btn btn-primary class-ability-graph-btn" data-graphtype="my-school">{{__('languages.my_school')}}</button>
						</div>
						<div class="col-md-4 text-center">
							<button type="button" class="btn btn-primary class-ability-graph-btn" data-graphtype="all-school">{{__('languages.all_schools')}}</button>
						</div>
					</div>
					<div class="row">
						<img src="" id="class-ability-analysis-report-image" class="img-fluid">
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default class-ability-analysis-report-close-pop" data-dismiss="modal">{{__('languages.close')}}</button>
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


<!-- Start list of Student Progress Report Popup -->
<div class="modal" id="modal-student-progress-report" tabindex="-1" aria-labelledby="test-difficulty-analysis-report" aria-hidden="true" data-backdrop="static">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<form method="post">
				<div class="modal-header">
					<h4 class="modal-title w-100">{{__('languages.progress_report')}}</h4>
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				</div>
				<div class="modal-body">
					<table id="student-list" class="display" style="width:100%">
	                    <thead>
	                        <tr>
	                            <th class="first-head"><span>{{__('languages.name')}}</span></th>
								<th class="first-head"><span>{{__('languages.class_student_number')}}</span></th>
	                            <th class="sec-head selec-opt"><span>{{__('languages.email')}}</span></th>
	                            <th class="selec-head">{{__('languages.report.exam_status')}}</th>
	                        </tr>
	                    </thead>
	                    <tbody class="scroll-pane">
		                </tbody>
		            </table>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">{{__('languages.close')}}</button>
				</div>
			</form>
		</div>
	</div>
</div>
<!-- End list of Student Progress Report Popup -->

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
	//Default remember tab selected into student panel and teacher panel
	$('.test-tab').removeClass('active');
	$('.tab-pane').removeClass('active');
	if($.cookie("PreviousTab")){
		$('#tab-'+$.cookie("PreviousTab")).addClass('active');
		$('#'+$.cookie("PreviousTab")).addClass('active');
	}else{
		$('#tab-self-learning').addClass('active');
		$('#self_learning').addClass('active');
	}
	
	/*
	This change display document in exam id
	*/
	var listExamIdDoc = new Array();
	$.each($(".main-my-study input[type=checkbox]"), function() {
		if($(this).val()!='on'){
			listExamIdDoc.push($(this).val());
		}
	});

	$(document).on('change', '#AllTabs', function() {
		if($(this).prop('checked')){
			$(".categories-main-list .categories-list input[type=checkbox]").prop('checked',true);
		}else{
			$(".categories-main-list .categories-list input[type=checkbox]").prop('checked',false);
		}
	});
});
</script>
<script type="text/javascript">
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
						toastr.error('Data not found');
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
						toastr.error('Data not found');
					}
					$("#cover-spin").hide();
				},
				error: function(response) {
					ErrorHandlingMessage(response);
				}
			});
		}
	});
	// get student progress report
	$(document).on('click', '.student-progress-report', function(e) {
		$("#cover-spin").show();
		$examId = $(this).attr('data-examid');
		$studentIds = $(this).attr('data-studentids');
		if($examId && $studentIds){
			$.ajax({
				url: BASE_URL + '/myteaching/student-progress-report',
				type: 'post',
				data : {
					'_token': $('meta[name="csrf-token"]').attr('content'),
					'examid' : $examId,
					'studentIds' : $studentIds
				},
				success: function(response) {
					if(response.data.length != 0){
                        $("#student-list").DataTable().destroy();
						$("#student-list tbody").html(response.data);
                        $("#student-list").DataTable({
                            order: [[0, "desc"]],
                        });
						$("#modal-student-progress-report").modal('show');
					}else{
						toastr.error('Data not found');
					}
					$("#cover-spin").hide();
				},
				error: function(response) {
					ErrorHandlingMessage(response);
				}
			});
		}
	});
});
</script>
@endsection