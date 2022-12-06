@extends('backend.layouts.app')
    @section('content')	
    <div class="wrapper d-flex align-items-stretch sm-deskbord-main-sec">
        @include('backend.layouts.sidebar')
	      <div id="content" class="pl-2 pb-5">
            @include('backend.layouts.header')
			<div class="sm-right-detail-sec pl-5 pr-5">
				<div class="container-fluid">
					<div class="row">
						<div class="col-md-12">
							<div class="sec-title">
								<h5 class="mb-4">{{__('languages.report.class_performance')}}</h5>
							</div>
							<div class="row">
								<div class="col-md-12">
									<div class="sec-title">
										<a href="javascript:void(0);" class="btn-back" id="backButton">{{__('languages.back')}}</a>
									</div>
								</div>
							</div>
							<hr class="blue-line">
						</div>
					</div>
					@if(session('error'))
					<div class="alert alert-danger">{{ session('error') }}</div>
					@endif
					<div class="row">
						<div class="col-md-9 col-lg-9 class-report-form">
							@php
							$bg_correct_color='background-color:'.App\Helpers\Helper::getGlobalConfiguration('question_correct_color');
							$bg_incorrect_color='background-color:'.App\Helpers\Helper::getGlobalConfiguration('question_incorrect_color');
							@endphp
							<form class="class-test-report" id="class-test-report row" method="get">
								<div class="select-lng pt-2 pb-2 col-lg-4 col-md-4">
									<label>{{ __('languages.select_test') }}</label>
									<select name="exam_id" id="exam_id" class="form-control select-option performance_exam_id">
										<option value="">{{ __('languages.select_test') }}</option>
										@php
											$school_id = '';
											if(App\Helpers\Helper::isSchoolLogin()){
												$school_id = App\Helpers\Helper::isSchoolLogin();
											}
											if(App\Helpers\Helper::isTeacherLogin()){
												$school_id = App\Helpers\Helper::isTeacherLogin();
											}
											if(App\Helpers\Helper::isPrincipalLogin()){
												$school_id = App\Helpers\Helper::isPrincipalLogin();
											}
										@endphp
											@if(!empty($ExamList))
											@foreach($ExamList as $exams)
												<option value="{{$exams->id}}" data-school-id="{{ $school_id }}" {{ request()->get('exam_id') == $exams->id ? 'selected' : '' }}>{{$exams->title}} @if(isset($exams->reference_no)) ({{$exams->reference_no}}) @endif</option>
											@endforeach
											@endif
									</select>
									@if($errors->has('exam_id'))
										<span class="validation_error">{{ $errors->first('exam_id') }}</span>
									@endif
								</div>
								<div class="select-lng pt-2 pb-2 col-lg-2 col-md-2 exam-school-list" @if(isset($schoolList) && empty($schoolList)) style="display:none;" @endif>
									<label>{{__('languages.select_school')}}</label>
									<select name="exam_school_id"  id="exam_school_id" class="form-control select-option exam_school_id">
										@if(!empty($schoolList))
										@foreach($schoolList as $school)
										<option value="{{$school['id']}}" {{ request()->get('exam_school_id') == $school['id'] ? 'selected' : '' }}>{{ $school['name']}}</option>
										@endforeach
										@endif
									</select>
								</div>

								<div class="pt-2 pb-2 col-lg-3 col-md-3 class-performance-grade-section" @if(null ==request()->get('grade_id')) style="display:none;" @endif>
									<div class="select-lng  pb-2">
										<label>{{__('languages.select_grade')}}</label>
										<!-- <label for="users-list-role">{{ __('languages.user_management.grade') }}</label> -->
										<select @php if(empty($grade_id)){ echo 'disabled'; } @endphp class="form-control" data-show-subtext="true" data-live-search="true" name="grade_id" id="student_performance_grade_id" data-school-id = {{$school_id}}>
											<option value="">{{ __('languages.select_grade') }}</option>
											@foreach($GradeList as $grade)
											<option value="{{$grade->id}}" {{ ( $grade->id==request()->get('grade_id') ? 'selected' : '') }}>{{ $grade->name}}</option>
											@endforeach
										</select>
									</div>
								</div>

								<div class="pt-2 pb-2 col-lg-3 col-md-3 class-performance-class-section" @if(null ==request()->get('grade_id')) style="display:none;" @endif>
									<div class="select-lng pb-2">
										<label>{{__('languages.select_class')}}</label>
										<!-- <label for="users-list-role">{{ __('languages.class') }}</label> -->
										{{-- <select @php if(empty($class_type_id)){ echo 'disabled'; } @endphp name="class_type_id[]" class="form-control" id="classType-select-option" multiple> --}}
										<select  name="class_type_id[]" class="form-control" id="classType-select-option" multiple>
											@foreach($GradeClassListData as $GradeClassId => $GradeClassValue)
											<option value="{{$GradeClassId}}" {{ in_array($GradeClassId,$class_type_id) ? 'selected' : '' }}>{{$grade_id}}{{ $GradeClassValue }}</option>
											@endforeach
										</select>
									</div>
								</div>

								<div class="select-lng pt-2 pb-2 col-lg-4 col-md-4 class-performance-group-section" @if(null ==request()->get('group_id')) style="display:none;" @endif>
									<label>{{__('languages.question_generators_menu.select_peer_groups')}}</label>
									<select name="group_id" id="group_id" class="form-control select-option performance_group_id">
										@foreach($PeerGroupList as $PeerGroup)
										<option value="{{$PeerGroup->id}}" {{($PeerGroup->id == $group_id ? 'selected' : '')}}>{{$PeerGroup->group_name}}</option>
										@endforeach
									</select>
								</div>
							
								<div class="col-lg-2 col-md-2">
									<div class="select-lng pt-2 pb-2">
										<label></label>
										<button type="submit" name="filter" value="filter" class="btn-search button-margin-manage" id="filterReportClassTestResult">{{ __('languages.search') }}</button>
									</div>
								</div>
							</form>
						</div>
					</div>
					
					@if(!empty($ResultList))
					<div class="row main-date-sec">
						@if(!empty($ExamData->publish_date))
						<div class="col-lg-3 col-md-3 ">
							<label><b>{{__('languages.report.date_of_release')}}: </b><span> {{!empty($ExamData->publish_date) ? date('d/m/Y H:i:s',strtotime($ExamData->publish_date)) : ''}}</span></label>
						</div>
						@endif
						<div class="col-lg-3 col-md-3">
							<label><b>{{__('languages.report.start_date')}}: </b> <span>{{!empty($ExamData->from_date) ? date('d/m/Y',strtotime($ExamData->from_date)) : ''}}</span></label>
						</div>
						<div class="col-lg-3 col-md-3">
							<label><b>{{__('languages.report.end_date')}}: </b> <span>{{!empty($ExamData->to_date) ? date('d/m/Y',strtotime($ExamData->to_date)): ''}}</span></label>
						</div>
						<div class="col-lg-3 col-md-3">
							<label><b>{{__('languages.report.result_date')}}: </b> <span>{{ !empty($ExamData->result_date) ? date('d/m/Y',strtotime($ExamData->result_date)) : ''}}</span></label>
						</div>
					</div>
					
					<!-- All Reports Menu -->
					<div class="row">
						<div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
							<button class="btn-search class-performance-progress-report w-100">{{__('languages.progress_report')}}</button>
						</div>
						<div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
							<button class="btn-search class-performance-class-ability-report w-100">{{__('languages.class_ability_analysis')}}</button>
						</div>
						<div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
							<button class="btn-search getTestDifficultyAnalysisReport w-100" data-examid="{{$ExamData->id}}">{{__('languages.question_difficulty_analysis')}}</button>
						</div>
						<div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
							<a href="{{route('exam-configuration-preview',$ExamData->id)}}" class="ml-2" title="{{__('languages.config')}}">
								<button class="btn-search w-100">{{__('Exam Configurations')}}</button>
							</a>
						</div>
						<div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
							<button class="btn-search class-performance-student-summary-report w-100" data-examid="{{$ExamData->id}}">{{__('languages.student_summary_report')}}</button>
						</div>
					</div>
					<!-- End All Reports Menu -->
					
					<div class="row correct-incorrect-row mt-2 mb-2">
						<div class="col-md-12 correct-incorrect-col classPerformanceReportRightJustify-export-btn">
							<div class="select-lng">
								<button type="submit" name="filter" value="filter" class="btn-search remove-radius active">{{ __('languages.report.class_performance') }}</button>
							</div>
							{{-- <form id="exam-details-reports" action="{{ route('report.exams.student-test-performance')}}" method="get">
							<input type="hidden" name="details_report_exam_id" id="details_report_exam_id" value="{{ request()->get('exam_id')}}">
							<div class="select-lng">
								<input type="submit" class=" btn-search remove-radius class-test-report-detail-btn" value="{{ __('languages.report.details') }}">
							</div>
							</form> --}}
							<?php if(Auth::user()->role_id == 1){ ?>
							<form class="exam-details-reports"id="exam-details-reports" action="{{ route('report.school-comparisons')}}" method="get">
							<input type="hidden" name="exam_id" id="exam_id" value="{{ request()->get('exam_id')}}">
							<input type="hidden" name="grade_id" value="{{ $grade_id }}">
							@if(isset($class_type_id) && !empty($class_type_id))
								@foreach($class_type_id as $class_type)
									<input type="hidden" name="class_type_id[]" value="{{ $class_type }}">
								@endforeach
							@endif
							<div class="select-lng classPerformanceReportInline">
								<span class=><input type="submit" class=" btn-search remove-radius school-comparison-btn" value="{{ __('languages.report.school_comparison_result') }}"></span>
							</div>
							<?php } ?>
							</form>

							{{-- @if(Auth::user()->role_id != 1) --}}
							<div class="select-lng classPerformanceReportInline classPerformanceReportInline-export-btn pl-2 pb-2">
								<button type="button" name="exportPerformaceReport"  class="btn-search exportPerformanceReportPopup" data-exam_type="{{$examType}}"><i class="fa fa-download" aria-hidden="true"> {{ __('languages.report.export_performance_report') }} </i></button>
							</div>
							{{-- @endif --}}
							
							{{--<form id="group-skill-weekness-reports" action="{{ route('report.groups-skill-weekness')}}" method="get">
							<input type="hidden" name="exam_id" id="exam_id" value="{{ request()->get('exam_id')}}">
							<div class="select-lng">
								<input type="submit" class=" btn-search remove-radius school-comparison-btn" value="{{ __('Skills Weekness') }}">
							</div>--}}
							{{-- </form> --}}
						</div>
					</div>					
					<div class="Top-ScrollBar">
						<div class="scrollbar-top">
						</div>
					</div>
					@endif

					@if(!empty($_GET['exam_id']))
					<div class="Bottom-ScrollBar">
						<div class="scrollbar-bottom">
							<div class="row">
								<div class="col-md-12">
									<div class="question-bank-sec class-test-report-scroll @if(empty($ResultList)) remove-overflow-scroll @endif">
										@if(!empty($ResultList))
										<table id="class-test-report-datatable" class="display" style="width:100%">
											<thead>
												<tr>
													<!-- <th class="first-head"><span>{{__('Class-Student No')}}</span></th> -->
													  <th class="first-head sorting_column" data-sort-type="student_name" data-sort="<?php if(isset($_GET['sort_by_type']) && $_GET['sort_by_type'] == 'student_name'){ echo $_GET['sort_by_value'];}?>">
														<span>{{__('languages.report.student_name')}}</span>
														<span class="student-name-sorting-icon">
														@if(isset($_GET['sort_by_type']) && $_GET['sort_by_type'] == 'student_name')
														<i class="fa fa-sort-{{$_GET['sort_by_value']}}"></i>
														@else
															<i class="fa fa-sort"></i>
														@endif
														</span>
													</th>
													<th>{{ __('languages.class_student_number') }}</th>
													<th>{{ __('languages.performance_graph') }}</th>
													<th class="selec-opt"><span>{{__('languages.report.no_of_correct_answers')}}</span></th>
													<th class="selec-opt"><span>{{__('languages.report.ability')}}</span></th>
													<th class="selec-opt"><span>{{__('languages.report.completion_time')}} ({{__('languages.report.h_m_s')}})</span></th>
													<th class="selec-opt sorting_column" data-sort-type="student_rank" data-sort="<?php if(isset($_GET['sort_by_type']) && $_GET['sort_by_type'] == 'student_rank'){ echo $_GET['sort_by_value'];}?>">
														<span>{{__('languages.ranking_correct_incorrect')}}</span>
														<span class="student-rank-sorting-icon">
														@if(isset($_GET['sort_by_type']) && $_GET['sort_by_type'] == 'student_rank')
														<i class="fa fa-sort-{{$_GET['sort_by_value']}}"></i>
														@else
															<i class="fa fa-sort"></i>
														@endif
														</span>
													</th>
													<th class="selec-opt sorting_column" data-sort-type="accuracy_rank" data-sort="<?php if(isset($_GET['sort_by_type']) && $_GET['sort_by_type'] == 'accuracy_rank'){ echo $_GET['sort_by_value'];}?>">
														<span>{{__('languages.accuracy_rank')}}</span>
														<span class="student-accuracy-rank-sorting-icon">
														@if(isset($_GET['sort_by_type']) && $_GET['sort_by_type'] == 'accuracy_rank')
														<i class="fa fa-sort-{{$_GET['sort_by_value']}}"></i>
														@else
															<i class="fa fa-sort"></i>
														@endif
														</span>
													</th>
													<th class="selec-opt sorting_column" data-sort-type="ability_rank" data-sort="<?php if(isset($_GET['sort_by_type']) && $_GET['sort_by_type'] == 'ability_rank'){ echo $_GET['sort_by_value'];}?>">
														<span>{{__('languages.ability_rank')}}</span>
														<span class="student-ability-rank-sorting-icon">
														@if(isset($_GET['sort_by_type']) && $_GET['sort_by_type'] == 'ability_rank')
														<i class="fa fa-sort-{{$_GET['sort_by_value']}}"></i>
														@else
															<i class="fa fa-sort"></i>
														@endif
														</span>
													</th>
													<?php
													$data = $ResultList[array_key_first($ResultList)];
													for($i=1; $i <= $data['countQuestions']; $i++){
														echo '<th><span>Q-'.$i.'</span></th>';
													}
													?>
												</tr>
											</thead>
											<tbody class="scroll-pane">
												@php $CorrectAnswer = []; @endphp
												@foreach($ResultList as $key => $result)
												<tr class="report-header-tr">
													<!-- <td>{{$result['student_grade'] }}-{{ $result['student_number'] }}</td> -->
													<td class="report-student-name plus-icon" data-id="{{$result['student_number']}}" data-examid="{{$result['exam_id']}}" data-isGroupId="{{request()->get('group_id')}}">
														<a href="javascript:void(0);" style="color:black;">{{ $result['student_name'] }}</a>
													</td>
													<td>{{$result['class_student_number']}}</td>
													<td>
														<a href="javascript:void(0);" title="{{__('languages.performance_graph')}}" class="performance_graph" data-graphtype="currentstudent" data-studentid="{{ $result['student_number'] }}" data-examid="{{$result['exam_id']}}">
															<i class="fa fa-bar-chart" aria-hidden="true"></i>
														</a>
													</td>
													<td>{{ $result['total_correct_answer'] }}/{{$result['countQuestions']}}</td>
													<td>
														@if($result['student_ability'] != 'N/A')
															{{App\Helpers\Helper::GetShortPercentage($result['student_normalize_ability'])}}
														@endif
													</td>
													<td>{{$result['completion_time']}}</td>											
													<td>{{$result['student_ranking']}}</td>
													<td>{{$result['accuracy_ranking']}}</td>
													<td>{{$result['ability_ranking']}}</td>
													@for($i=0; $i < $result['countQuestions']; ++$i)
														@if(isset($result[$i]['answer']) && $result[$i]['answer'] == 'true')
														@php  $CorrectAnswer[$i] = isset($CorrectAnswer[$i]) ? ($CorrectAnswer[$i] + 1) : 1; @endphp
														<td class="reports-result correct-icon" style={{$bg_correct_color}}>
															<span style="visibility: hidden;">{{__("languages.report.correct")}}</span>
															<span class="font-weight-bold">{{$result[$i]['selected_answer']}}</span>
															<i class="fa fa-check" aria-hidden="true"></i>
														</td>
														@elseif(isset($result[$i]['answer']) && $result[$i]['answer'] == 'false')
														<td class="reports-result incorrect-icon" style={{$bg_incorrect_color}}>
															<span style="visibility: hidden;">{{__("languages.report.incorrect")}}</span>
															<span class="font-weight-bold">{{$result[$i]['selected_answer']}}</span>
															<i class="fa fa-times" aria-hidden="true"></i>
														</td>
														@else
														<td class="reports-result">
															<span style="visibility: hidden;">{{__("")}}</span>
															<!-- <i class="fa fa-times" aria-hidden="true"></i> -->
														</td>
														@endif
													@endfor
												</tr>
												@php  $colspan = ($result['countQuestions'] + 8) @endphp
												<tr class="child-report-section-detail" id="student_{{$result['student_number']}}">
													<td colspan="{{$colspan}}" class="child-result-set">
														<div class="section-detail expand_student_report_student_{{$result['student_number']}}" id="report-{{$key}}">
														</div>
													</td>
												</tr>
												@endforeach
												
												<tr>
													<td>{{__('languages.report.number_of_students_answer_correctly')}}</td>
													<td></td>
													<td></td>
													<td></td>
													<td></td>
													<td></td>
													<td></td>
													<td></td>
													<td></td>
													@for($i=0; $i < $result['countQuestions']; ++$i)
														@if(isset($CorrectAnswer[$i]) && !empty($CorrectAnswer[$i]))
															<td class="text-center-table">{{$CorrectAnswer[$i]}}</td>
														@else
															<td class="text-center-table">0</td>
														@endif
													@endfor
												</tr>
												<tr>
													<td>{{__('languages.report.answer_statistics')}}</td>
													<td></td>
													<td></td>
													<td></td>
													<td></td>
													<td></td>
													<td></td>
													<td></td>
													<td></td>
													@for($i=0; $i < $result['countQuestions']; ++$i)
														@if(isset($QuestionAnswerData[$i]) && !empty($QuestionAnswerData[$i]))
															<td class="text-center-table">
																@php
																	$QuestionAnswerSelectA='1:0';
																	if(isset($QuestionAnswerData[$i]['A'])){
																		$QuestionAnswerSelectA='1:'.$QuestionAnswerData[$i]['A']; // 1 == A
																	}
																	$QuestionAnswerSelectB='2:0';
																	if(isset($QuestionAnswerData[$i]['B'])){
																		$QuestionAnswerSelectB='2:'.$QuestionAnswerData[$i]['B']; // 2 == B
																	}
																	$QuestionAnswerSelectC='3:0';
																	if(isset($QuestionAnswerData[$i]['C'])){
																		$QuestionAnswerSelectC='3:'.$QuestionAnswerData[$i]['C']; // 3 == C
																	}
																	$QuestionAnswerSelectD='4:0';
																	if(isset($QuestionAnswerData[$i]['D'])){
																		$QuestionAnswerSelectD='4:'.$QuestionAnswerData[$i]['D']; //4 == D
																	}
																	$QuestionAnswerSelect=$QuestionAnswerSelectA.' '.$QuestionAnswerSelectB.' '.$QuestionAnswerSelectC.' '.$QuestionAnswerSelectD;
																@endphp
																<p class="w-100">{{$QuestionAnswerSelect}}</p>
															</td>
														@else
															<td class="text-center-table">0</td>
														@endif
													@endfor
												</tr>
		
												<tr>
													<td>{{__('languages.report.accuracy')}}</td>
													<td></td>
													<td></td>
													<td></td>
													<td></td>											
													<td></td>
													<td></td>
													<td></td>
													<td></td>
													@for($i=0; $i < $result['countQuestions']; ++$i)
														@if(isset($CorrectAnswer[$i]) && !empty($CorrectAnswer[$i]))
															<!-- <td class="text-center-table">{{ round(((100 * $CorrectAnswer[$i]) / $result['countStudent']), 2); }}%</td> -->
															<td class="text-center-table">{{ round(((100 * $CorrectAnswer[$i]) / count($ResultList)), 2); }}%</td>
														@else
															<td class="text-center-table">0%</td>
														@endif
													@endfor
												</tr>
											  </tbody>
										</table>
										@else
										<p style="text-align: center;">{{__('languages.report.no_data_found')}}</div>
										@endif
									</div>
								</div>
							</div>
						</div>
					</div>
					@endif
				</div>
			</div>
	      </div>
		</div>

		<!-- Start Performance Graph Popup -->
		<div class="modal" id="studentPerformanceGraph" tabindex="-1" aria-labelledby="studentPerformanceGraph" aria-hidden="true" data-backdrop="static">
			<div class="modal-dialog modal-lg">
				<div class="modal-content">
					<form method="post">
						<div class="modal-header">
							<h4 class="modal-title w-100">{{__('languages.student_performance_graph')}}</h4>
							<button type="button" class="close" onclick="destroyCanvas()" data-dismiss="modal" aria-hidden="true">&times;</button>
						</div>
						<div class="modal-body">
							<div class="Graph-body">
								<img src="" id="graph-image" class="img-fluid">
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

		<!-- Start Question Analysis Graph Popup -->
		<div class="modal" id="studentQuestionAnalysisGraph" tabindex="-1" aria-labelledby="studentQuestionAnalysisGraph" aria-hidden="true" data-backdrop="static">
			<div class="modal-dialog modal-lg">
				<div class="modal-content">
					<form method="post">
						<div class="modal-header">
							<h4 class="modal-title w-100">{{__('languages.student_question_analysis_graph')}}</h4>
							<button type="button" class="close" onclick="destroyCanvas()" data-dismiss="modal" aria-hidden="true">&times;</button>
						</div>
						<div class="modal-body">
							<div class="Graph-body">
								<img src="" id="question-graph-image" class="img-fluid">
							</div>
						</div>
						<div class="modal-footer">
							<button type="button" class="btn btn-default" data-dismiss="modal">{{__('languages.close')}}</button>
						</div>
					</form>
				</div>
			</div>
		</div>
		<!-- End Question Analysis Popup -->

		<!-- Start Export Performance Report -->
		<div class="modal" id="exportPerformanceReportPopupModal" tabindex="-1" aria-labelledby="exportPerformanceReportPopupModal" aria-hidden="true" data-backdrop="static">
			<div class="modal-dialog modal-lg">
				<div class="modal-content">
					<form method="post">
						<div class="modal-header">
							<h4 class="modal-title w-100">{{__('languages.report.export_performance_report')}}</h4>
							<button type="button" class="close closePerformanceReportPopup" data-dismiss="modal" aria-hidden="true">&times;</button>
						</div>
						<div class="modal-body">
							@if(isset($getClasses['class']) && !empty($getClasses['class']))
							{{-- @if(isset($class_type_id) && !empty($class_type_id)) --}}
								<div class="form-row">
									<div class="col-md-4 col-lg-4 col-sm-4">
										<label><input type="checkbox" value="" name="checkAllClasses" id="checkAllClasses"/> {{__('languages.all_classes')}}</label>
									</div>
								</div>
								<div class="form-row">
									{{-- @if(isset($getClasses['class']) && !empty($getClasses['class'])) --}}
									{{-- @foreach($getClasses['class'] as $classes)
									<div class="col-md-1 col-lg-1 col-sm-1">
										<label><input type="checkbox" value="{{$classes['classId']}}" name="classNameIds[]" class="getCheckedClass"/> {{$classes['className']}} </label>
									</div>
									@endforeach --}}
									{{-- Old 24-11-2022 In All Grade Class Display so in comment --}}
									@foreach($GradeClassListData as $GradeClassId => $GradeClassValue)
									<div class="col-md-1 col-lg-1 col-sm-1">
										<label><input type="checkbox" value="{{$GradeClassId}}" {{ in_array($GradeClassId,$class_type_id) ? 'selected' : '' }} name="classNameIds[]" class="getCheckedClass"/> {{$grade_id}}{{ $GradeClassValue }}</label>
									</div>
									@endforeach
									{{-- Old 24-11-2022 In All Grade Class Display so in comment --}}
									{{-- New Code 24-111-2022 --}}
									{{-- @foreach($GradeClassListData as  $GradeClassValue)
									<div class="col-md-1 col-lg-1 col-sm-1">
										<label><input type="checkbox" value="{{$GradeClassValue}}" {{ in_array($GradeClassValue,$class_type_id) ? 'selected' : '' }} name="classNameIds[]" class="getCheckedClass"/> {{$grade_id}}{{ App\Helpers\Helper::getSingleClassName($GradeClassValue) }}</label>
									</div>
									@endforeach --}}
									{{-- New Code 24-111-2022 --}}
									{{-- @endif --}}
								</div>
							@endif
							{{-- @if($peerGroupData->isNotEmpty()) --}}
							@if(isset($group_id) && !empty($group_id))
								<div class="form-row">
									<div class="col-md-4 col-lg-4 col-sm-4">
										<label><input type="checkbox" value="" name="checkAllPeerGroup" id="checkAllPeerGroup"/> {{__('All Peer Group')}}</label>
									</div>
								</div>
								<div class="form-row">
									@foreach($peerGroupData as $PeerGroup)
										<div class="col-md-2 col-lg-2 col-sm-2">
											<label><input type="checkbox" value="{{$PeerGroup->id}}" name="groupNameIds[]" class="getCheckedGroup"/> {{$PeerGroup->group_name}} </label>
										</div>
									@endforeach
								</div>
							@endif
						</div>
						<div class="modal-footer">
							<button type="button" name="exportPerformaceReport"  class="btn-search" id="exportPerformanceReport"><i class="fa fa-download" aria-hidden="true"> {{ __('languages.report.export_performance_report') }} </i></button>
							<button type="button" class="btn btn-default closePerformanceReportPopup" data-dismiss="modal">{{__('languages.close')}}</button>
						</div>
					</form>
				</div>
			</div>
		</div>
		<!-- End Export Performance Report -->

		<!-- Start Admin Export Performance Report -->
		@if(Auth::user()->role_id == 1)
			<div class="modal" id="adminExportPerformanceReportPopupModal" tabindex="-1" aria-labelledby="adminExportPerformanceReportPopupModal" aria-hidden="true" data-backdrop="static">
				<div class="modal-dialog modal-lg">
					<div class="modal-content">
						<form method="post">
							<div class="modal-header">
								<h4 class="modal-title w-100">{{__('languages.report.export_performance_report')}}</h4>
								<button type="button" class="close closeAdminPerformanceReportPopup" data-dismiss="modal" aria-hidden="true">&times;</button>
							</div>
							<div class="modal-body">
							@if(isset($getClasses) && !empty($getClasses))
								@foreach($getClasses as $schoolId => $school)
									@if( request()->get('exam_school_id') != null && request()->get('exam_school_id') == $schoolId)
										@if(isset($school['class']) && !empty($school['class']))
											@if(empty($school['group']))
												<p>{{$school['schoolName']}}<p>
												<div class="col-md-4 col-lg-4 col-sm-4">
													<label><input type="checkbox" value="" class="selectAllClassSchool" name="selectAllClassSchool_{{$schoolId}}" id="selectAllClassSchool_{{$schoolId}}" data-school-id="{{$schoolId}}"/> {{__('languages.all_classes')}}</label>
												</div>
												<div class="form-row pl-3">
													@foreach($school['class'] as $class)
													<div class="col-md-1 col-lg-1 col-sm-1">
														<label><input type="checkbox" value="{{$class['classId']}}" name="classNameIds[]" class="selectClass selectSchoolClass_{{$schoolId}}" data-school-id="{{$schoolId}}"/> {{$class['className']}} </label>
													</div>
													@endforeach
												</div>
											@endif
										@endif
										@if(isset($school['group']) && !empty($school['group']))
											@if(empty($class_type_id))
												<p>{{$school['schoolName']}}<p>
												<div class="col-md-4 col-lg-4 col-sm-4">
													<label><input type="checkbox" value="" class="selectAllGroupSchool" name="selectAllGroupSchool_{{$schoolId}}" id="selectAllGroupSchool_{{$schoolId}}" data-school-id="{{$schoolId}}"/> {{__('All Group')}}</label>
												</div>
												<div class="form-row pl-3">
												@foreach($school['group'] as $group)
												<div class="col-md-1 col-lg-1 col-sm-1">
													<label><input type="checkbox" value="{{$group['groupId']}}" name="groupNameIds[]" class="selectGroup selectSchoolGroup_{{$schoolId}}" data-school-id="{{$schoolId}}"/> {{$group['groupName']}} </label>
												</div>
												@endforeach
												</div>
											@endif
										@endif
									@endif
								@endforeach
							@endif
							</div>
							<div class="modal-footer">
								<button type="button" name="exportPerformaceReport"  class="btn-search" id="exportPerformanceReport"><i class="fa fa-download" aria-hidden="true"> {{ __('languages.report.export_performance_report') }} </i></button>
								<button type="button" class="btn btn-default closeAdminPerformanceReportPopup" data-dismiss="modal">{{__('languages.close')}}</button>
							</div>
						</form>
					</div>
				</div>
			</div>
		@endif

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

		<!-- Start Performance Analysis Popup -->
		<div class="modal" id="class-ability-analysis-report" tabindex="-1" aria-labelledby="class-ability-analysis-report" aria-hidden="true" data-backdrop="static">
			<div class="modal-dialog modal-lg">
				<div class="modal-content">
					<form method="post">
						<div class="modal-header">
							<h4 class="modal-title w-100">{{__('languages.class_ability_analysis')}}</h4>
							<button type="button" class="close class-ability-analysis-report-close-pop" data-dismiss="modal" aria-hidden="true">&times;</button>
						</div>
						<div class="modal-body">
							<div class="row pb-3">
								<div class="col-md-4 text-center">
									<button type="button" class="btn btn-primary class-ability-graph-btn" data-graphtype="my-class" data-classAbilityIsGroup="false">
										<span class="my_class_group_button">{{__('languages.my_class.my_classes')}}</span>
									</button>
								</div>
								<div class="col-md-4 text-center">
									<button type="button" class="btn btn-primary class-ability-graph-btn" data-graphtype="my-school" data-classAbilityIsGroup="false">{{__('languages.my_school')}}</button>
								</div>
								<div class="col-md-4 text-center">
									<button type="button" class="btn btn-primary class-ability-graph-btn" data-graphtype="all-school" data-classAbilityIsGroup="false">{{__('languages.all_schools')}}</button>
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

		<!-- Student Result Summary Report -->
		<div class="modal" id="StudentSummaryReportModal" tabindex="-1" aria-labelledby="StudentSummaryReportModal" aria-hidden="true" data-backdrop="static">
			<div class="modal-dialog modal-lg">
				<div class="modal-content">
					<form method="post">
						<div class="modal-header embed-responsive">
							<h4 class="modal-title w-100">{{__('languages.student_summary_report')}}</h4>
						</div>
						<div class="modal-body student-report-summary-data">
							
						</div>
						<div class="modal-footer">
							<button type="button" class="btn btn-default close-student-report-summary-popup" data-dismiss="modal">{{__('languages.close')}}</button>
						</div>
					</form>
				</div>
			</div>
		</div>
		<!-- End Result Summary Popup -->

		<!-- End Admin Export Performance Report -->
		<script type="text/javascript">
			var isSchoolLogin = 0;
			var isTeacherLogin = 0;
			var isPrincipalLogin = 0;
			var isAdmin = 0;
			@if(App\Helpers\Helper::isSchoolLogin())
				isSchoolLogin = 1;
			@elseif(App\Helpers\Helper::isTeacherLogin())
				isTeacherLogin = 1;
			@elseif(App\Helpers\Helper::isPrincipalLogin())
				isPrincipalLogin = 1;
			@elseif(App\Helpers\Helper::isAdmin())
				isAdmin = 1;
			@endif
		</script>
		@include('backend.layouts.footer')
		<script>
			$(function() {
				/**
				 * USE : Check School Year reminder is enabled or not
				 */
				@if($isRemainderEnable)
					$('#remainder-upgrade-school-data-popup').show('');
				@endif
				/**
				 * End : Check School Year reminder is enabled or not
				 */

				if($('#exam_id').val()==''){
					$('.exportPerformanceReportPopup').hide();
				}

				//Admin Performance Report
				$(document).on('click','.selectAllClassSchool',function(e){
					var schoolId = $(this).data('school-id');
					if($(this).is(":checked")){
						$(".selectSchoolClass_"+schoolId).prop("checked", true);
					}else{
						$(".selectSchoolClass_"+schoolId).prop("checked", false);
					}
				});

				$(document).on('click','.selectClass',function(e){
					var schoolId = $(this).data('school-id');
					if ($(".selectSchoolClass_"+schoolId+":checked").length == $(".selectSchoolClass_"+schoolId).length){
						$('#selectAllClassSchool_'+schoolId).prop("checked", true);
					}else{
						$('#selectAllClassSchool_'+schoolId).prop("checked", false);
					}
				});

				//Admin Select All Groups
				$(document).on('click','.selectAllGroupSchool',function(e){
					var schoolId = $(this).data('school-id');
					if($(this).is(":checked")){
						$(".selectSchoolGroup_"+schoolId).prop("checked", true);
					}else{
						$(".selectSchoolGroup_"+schoolId).prop("checked", false);
					}
				});

				$(document).on('click','.selectGroup',function(e){
					var schoolId = $(this).data('school-id');
					if ($(".selectSchoolGroup_"+schoolId+":checked").length == $(".selectSchoolGroup_"+schoolId).length){
						$('#selectAllGroupSchool_'+schoolId).prop("checked", true);
					}else{
						$('#selectAllGroupSchool_'+schoolId).prop("checked", false);
					}
				});
				
				// Open Export Performance Report Modal
				$(document).on('click','.exportPerformanceReportPopup',function(){
					$examType = $(this).attr('data-exam_type');
					if($examType==1){
						let classIds = [];
						let peerGroupids = [];
						$examID = $('#exam_id').val();
						ExportClassPerformanceReport($examID,classIds,peerGroupids);
					}else{
						@if(Auth::user()->role_id == 1)
						$('#adminExportPerformanceReportPopupModal').modal('show');
						@else
						$('#exportPerformanceReportPopupModal').modal('show');
						@endif
					}
					
				});

				// All Class check and uncheck to change to check all
				$(document).on("change",".getCheckedClass",function () {
					if ($(".getCheckedClass:checked").length == $(".getCheckedClass").length){
						$("#checkAllClasses").prop("checked", true);
					}else {
						$("#checkAllClasses").prop("checked", false);
					}
				});

				//Check All Classes
				$(document).on('click','#checkAllClasses',function(){
					if($('#checkAllClasses').is(":checked")){
						$(".getCheckedClass").prop("checked", true);
					}else{
						$(".getCheckedClass").prop("checked", false);
					}
				});

				// All Group check and uncheck to change to check all
				$(document).on("change",".getCheckedGroup",function () {
					if ($(".getCheckedGroup:checked").length == $(".getCheckedGroup").length){
						$("#checkAllPeerGroup").prop("checked", true);
					}else {
						$("#checkAllPeerGroup").prop("checked", false);
					}
				});
				
				//Check All Groups
				$(document).on('click','#checkAllPeerGroup',function(){
					if($('#checkAllPeerGroup').is(":checked")){
						$(".getCheckedGroup").prop("checked", true);
					}else{
						$(".getCheckedGroup").prop("checked", false);
					}
				});

				//Export Performance Report in Csv Format
				$(document).on('click','#exportPerformanceReport',function(){
					let classIds = [];
					let peerGroupids = [];
					$examID = $('#exam_id').val();
					$('input[name="classNameIds[]"]:checked').each(function(){
						classIds.push($(this).val());
					});

					$('input[name="groupNameIds[]"]:checked').each(function(){
						peerGroupids.push($(this).val())
					});

					//For  Check Test Is selected or Not 
					if(classIds.length !=0 && $examID =="" || classIds.length ==0 && $examID ==""){
						toastr.error('Please Select Test');
					}else if((classIds.length !=0 && $examID !="") || (peerGroupids.length !=0 && $examID !="")){
						ExportClassPerformanceReport($examID,classIds,peerGroupids);
					}else if(classIds.length == 0 && peerGroupids.length==0){
						toastr.error(PLEASE_SELECT_CLASS_OR_GROUP);
					}
					
				});

				//Performance Graph
				$(document).on('click', '.performance_graph', function(e) {
					$("#cover-spin").show();
					$ExamId = $(this).attr('data-examid');
					$StudentId = $(this).attr('data-studentid')
					if($ExamId && $StudentId){
						$.ajax({
							url: BASE_URL + '/report/getPerformanceGraphCurrentStudent',
							type: 'post',
							data : {
								'_token': $('meta[name="csrf-token"]').attr('content'),
								'exam_id' : $ExamId,
								'student_id' : $StudentId
							},
							success: function(response) {
								var ResponseData = JSON.parse(JSON.stringify(response));
								if(ResponseData.data.length != 0){
									$('#graph-image').attr('src','data:image/jpg;base64,'+ ResponseData.data);
									$('#studentPerformanceGraph').modal('show');
								}else{
									toastr.error(STUDENT_PERFORMANCE_DATA_NOT_FOUND);
								}
								$("#cover-spin").hide();
							},
							error: function(response) {
								ErrorHandlingMessage(response);
							}
						});
					}else{
						$("#cover-spin").hide();
						toastr.error(DATA_NOT_FOUND);
					}
				});

				$(document).on('click', '.question_graph', function(e) {
					$("#cover-spin").show();
					$ExamId = $(this).attr('data-examid');
					$StudentId = $(this).attr('data-studentid');					
					$QuestionId = $(this).attr('data-questionid');
					if($ExamId && $StudentId){
						$.ajax({
							url: BASE_URL + '/report/getQuestionGraphCurrentStudent',
							type: 'post',
							data : {
								'_token': $('meta[name="csrf-token"]').attr('content'),
								'exam_id' : $ExamId,
								'student_id' : $StudentId,
								'question_id' : $QuestionId
							},
							success: function(response) {
								var ResponseData = JSON.parse(JSON.stringify(response));
								if(ResponseData.data.length != 0){
									$('#question-graph-image').attr('src','data:image/jpg;base64,'+ ResponseData.data);
									$('#studentQuestionAnalysisGraph').modal('show');
								}else{
									toastr.error(STUDENT_PERFORMANCE_DATA_NOT_FOUND);
								}
								$("#cover-spin").hide();
							},
							error: function(response) {
								ErrorHandlingMessage(response);
							}
						});
					}else{
						$("#cover-spin").hide();
						toastr.error(DATA_NOT_FOUND);
					}
				});

				$(".Top-ScrollBar").scroll(function(){
				$(".Bottom-ScrollBar").scrollLeft($(".Top-ScrollBar").scrollLeft());
				});

				$(".Bottom-ScrollBar").scroll(function(){
					$(".Top-ScrollBar").scrollLeft($(".Bottom-ScrollBar").scrollLeft());
				});

				/**
				 * Click on the class performance progress reports
				 */
				$(document).on('click', '.class-performance-progress-report', function(e){
					var ExamId = $('#exam_id').val();
					var SchoolId = $('#exam_school_id').val();
					var GradeId = $('#student_performance_grade_id').val();
					var ClassIds = $('#classType-select-option').val();
					var PeerGroupId = $('#group_id').val();
					// find the progress reports
					if(ExamId!=""){
						$("#cover-spin").show();
						$.ajax({
							url: BASE_URL + '/reports/progress-report',
							type: 'GET',
							data : {
								'ExamId' : ExamId,
								'SchoolId' : SchoolId,
								'GradeId' : GradeId,
								'ClassIds' : ClassIds,
								'PeerGroupId' : PeerGroupId
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
									toastr.error(VALIDATIONS.DATA_NOT_FOUND);
								}
								$("#cover-spin").hide();
							},
							error: function(response) {
								ErrorHandlingMessage(response);
							}
						});
					}else{
						toastr.error('Please select exams');
					}
				});

				/**
				 * Use : Show class ability reports
				 */
				$(document).on('click', '.class-performance-class-ability-report', function(e){
					var ExamId = $('#exam_id').val();
					var SchoolId = $('#exam_school_id').val();
					var GradeId = $('#student_performance_grade_id').val();
					var ClassIds = $('#classType-select-option').val();
					var PeerGroupId = $('#group_id').val();
					var isGroup = false;
					if(PeerGroupId){
						isGroup = true;
						$('.my_class_group_button').html('My Group');
					}else{
						$('.my_class_group_button').html('My Class');
					}
					$('.class-ability-graph-btn').attr('data-classAbilityIsGroup',isGroup);
					
					// find the progress reports
					if(ExamId!=""){
						$("#cover-spin").show();
						$.ajax({
							url: BASE_URL + '/reports/class-ability-analysis',
							type: 'GET',
							data : {
								'ExamId' : ExamId,
								'SchoolId' : SchoolId,
								'GradeId' : GradeId,
								'ClassIds' : ClassIds,
								'PeerGroupId' : PeerGroupId,
								'graph_type' : 'my-class',
								'isGroup' : isGroup
							},
							success: function(response) {
								var ResposnseData = JSON.parse(JSON.stringify(response));
								if(ResposnseData.data != 0){
									// Append image src attribute with base64 encode image
									$('#class-ability-analysis-report-image').attr('src','data:image/jpg;base64,'+ ResposnseData.data);
									$('#class-ability-analysis-report').modal('show');
								}else{
									toastr.error(VALIDATIONS.DATA_NOT_FOUND);
								}
								$("#cover-spin").hide();
							},
							error: function(response) {
								ErrorHandlingMessage(response);
							}
						});
					}else{
						toastr.error('Please select exams');
					}
				});
				
				$(document).on('click', '.class-ability-graph-btn', function(e){
					var ExamId = $('#exam_id').val();
					var SchoolId = $('#exam_school_id').val();
					var GradeId = $('#student_performance_grade_id').val();
					var ClassIds = $('#classType-select-option').val();
					var PeerGroupId = $('#group_id').val();
					var isGroup = false;
					if(PeerGroupId){
						isGroup = true;
					}
					// find the progress reports
					if(ExamId!=""){
						$("#cover-spin").show();
						$.ajax({
							url: BASE_URL + '/reports/class-ability-analysis',
							type: 'GET',
							data : {
								'ExamId' : ExamId,
								'SchoolId' : SchoolId,
								'GradeId' : GradeId,
								'ClassIds' : ClassIds,
								'PeerGroupId' : PeerGroupId,
								'graph_type' : $(this).attr('data-graphtype'),
								'isGroup' : isGroup
							},
							success: function(response) {
								var ResposnseData = JSON.parse(JSON.stringify(response));
								if(ResposnseData.data != 0){
									// Append image src attribute with base64 encode image
									$('#class-ability-analysis-report-image').attr('src','data:image/jpg;base64,'+ ResposnseData.data);
									$('#class-ability-analysis-report').modal('show');
								}else{
									toastr.error(VALIDATIONS.DATA_NOT_FOUND);
								}
								$("#cover-spin").hide();
							},
							error: function(response) {
								ErrorHandlingMessage(response);
							}
						});
					}else{
						toastr.error('Please select exams');
					}
				});

				/**
				 * USE: In Teacher Panel Display Student Result Summary Report in modal
				 */
				$(document).on("click", ".class-performance-student-summary-report", function () {
					var examId = $('#exam_id').val();
					var SchoolId = $('#exam_school_id').val();
					var GradeId = $('#student_performance_grade_id').val();
					var ClassIds = $('#classType-select-option').val();
					var PeerGroupId = $('#group_id').val();
					$("#cover-spin").show();
					$.ajax({
						url: BASE_URL + "/report/test-summary-report",
						type: "GET",
						data: {
							'examId': examId,
							'SchoolId' : SchoolId,
							'GradeId' : GradeId,
							'ClassIds' : ClassIds,
							'PeerGroupId' : PeerGroupId
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
			});

			$(window).on('load', function (e) {
				$('.scrollbar-top').width($('table').width());
				$('.scrollbar-bottom').width($('table').width());
			});
		</script>
@endsection
