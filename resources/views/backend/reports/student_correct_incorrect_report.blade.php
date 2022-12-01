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
								<h5 class="mb-4">{{__('languages.performance')}} {{__('languages.sidebar.reports')}}</h5>
							</div>
							<hr class="blue-line">
						</div>
					</div>
					@if (session('error'))
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
								<select name="exam_id"  id="exam_id" class="form-control select-option">
									<option value="">{{ __('languages.report.tests') }}</option>
										@if(!empty($ExamList))
										@foreach($ExamList as $exams)
										@php
											$school_id=explode(',',$exams->school_id);
											if(isset($school_id) && !empty($school_id)){
												$school_id=$school_id[0];
											}
										@endphp
										<option value="{{$exams->id}}" data-school-id="{{ $school_id }}" {{ request()->get('exam_id') == $exams->id ? 'selected' : '' }}>{{ $exams->title}}</option>
										@endforeach
										@endif
										@if(!empty($GroupTest))
										@foreach($GroupTest as $group)
										<option value="{{$group->exam_ids}}" {{ request()->get('exam_id') == $group->exam_ids ? 'selected' : '' }}>{{$group->name}}</option>
										@endforeach
										@endif
								</select>
								@if($errors->has('exam_id'))
									<span class="validation_error">{{ $errors->first('exam_id') }}</span>
								@endif
							</div>
							{{-- <div class="pt-2 pb-2 col-lg-3 col-md-3">
								<div class="select-lng pb-2">
									<!-- <label for="users-list-role">{{ __('languages.user_management.grade') }}</label> -->
									<select class="form-control" data-show-subtext="true" data-live-search="true" name="grade_id" id="student_performance_grade_id" >
										<option value="">{{ __('languages.select_grade') }}</option>
										@if(!empty($GradeList))
										@foreach($GradeList as $grade)
										<option value="{{$grade->id}}" {{ ( $grade->id==$grade_id ? 'selected' : '') }}>{{ $grade->name}}</option>
										@endforeach
										@endif
									</select>
								</div>
							</div>
							<div class="pt-2 pb-2 col-lg-3 col-md-3">
	                            <div class="select-lng pb-2">
	                            	<!-- <label for="users-list-role">{{ __('languages.class') }}</label> -->
	                                <select name="class_type_id[]" class="form-control" id="classType-select-option" multiple >
	                                	@if(!empty($GradeClassListData))
											@foreach($GradeClassListData as $GradeClassId => $GradeClassValue)
											<option value="{{$GradeClassId}}" {{ in_array($GradeClassId,$class_type_id) ? 'selected' : '' }}>{{$grade_id}}{{ $GradeClassValue }}</option>
											@endforeach
										@endif
	                                </select>
	                            </div>
	                        </div> --}}
							<div class="col-lg-2 col-md-2">
								<div class="select-lng pt-2 pb-2">
									<button type="submit" name="filter" value="filter" class="btn-search" id="filterReportClassTestResult">{{ __('languages.search') }}</button>
								</div>
							</div> 
							</form>
						</div>
						{{-- <div class="col-md-3 col-lg-3 class-report-form-right">
							<div class="pt-2 pb-2 col-md-12">
								<select name="exam_id" class="form-control select-option">
									<option value="">{{ __('languages.report.my_school') }}</option>
									<option value="">{{ __('languages.report.all_school') }}</option>
								</select>
							</div>
						</div> --}}
					</div>
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
					<div class="row correct-incorrect-row mt-2 mb-2">
						<div class="col-md-12 correct-incorrect-col">
							<div class="select-lng">
							<button type="submit" name="filter" value="filter" class="btn-search remove-radius active">{{ __('languages.report.class_performance') }}</a>
							</div>
							{{-- <form id="exam-details-reports" action="{{ route('report.exams.student-test-performance')}}" method="get">
							<input type="hidden" name="details_report_exam_id" id="details_report_exam_id" value="{{ request()->get('exam_id')}}">
							<div class="select-lng">
								<input type="submit" class=" btn-search remove-radius class-test-report-detail-btn" value="{{ __('languages.report.details') }}">
							</div>
							</form> --}}
							<?php if(Auth::user()->role_id == 1){ ?>
							<form id="exam-details-reports" action="{{ route('report.school-comparisons')}}" method="get">
							<input type="hidden" name="exam_id" id="exam_id" value="{{ request()->get('exam_id')}}">
							<input type="hidden" name="grade_id" value="{{ $grade_id }}">
							@if(isset($class_type_id) && !empty($class_type_id))
								@foreach($class_type_id as $class_type)
									<input type="hidden" name="class_type_id[]" value="{{ $class_type }}">
								@endforeach
							@endif
							<div class="select-lng">
								<input type="submit" class=" btn-search remove-radius school-comparison-btn" value="{{ __('languages.report.school_comparison_result') }}">
							</div>
							</form>
							<?php } ?>
							{{--<form id="group-skill-weekness-reports" action="{{ route('report.groups-skill-weekness')}}" method="get">
							<input type="hidden" name="exam_id" id="exam_id" value="{{ request()->get('exam_id')}}">
							<div class="select-lng">
								<input type="submit" class=" btn-search remove-radius school-comparison-btn" value="{{ __('Skills Weekness') }}">
							</div>--}}
							</form>
						</div>
					</div>
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
											<th>{{__('languages.class_student_number')}}</th>
											<th>{{ __('languages.performance_graph') }}</th>
											{{-- <th class="sec-head selec-opt"><span>{{__('languages.status')}}</span></th> --}}
											<th class="selec-opt"><span>{{__('languages.report.no_of_correct_answers')}}</span></th>
											<th class="selec-opt"><span>{{__('languages.report.ability')}}</span></th>
											{{-- <th class="selec-opt"><span>{{__('languages.report.exam_status')}}</span></th> --}}
											<th class="selec-opt"><span>{{__('languages.report.completion_time')}} ({{__('languages.report.h_m_s')}})</span></th>
											<th class="selec-opt sorting_column" data-sort-type="student_rank" data-sort="<?php if(isset($_GET['sort_by_type']) && $_GET['sort_by_type'] == 'student_rank'){ echo $_GET['sort_by_value'];}?>">
												<span>{{__('languages.report.ranking')}}</span>
												<span class="student-rank-sorting-icon">
												@if(isset($_GET['sort_by_type']) && $_GET['sort_by_type'] == 'student_rank')
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
											<td class="report-student-name-result plus-icon" data-id="{{$result['student_number']}}" data-examid="{{$result['exam_id']}}">
												<a href="javascript:void(0);" style="color:black;">{{ $result['student_name'] }}</a>
											</td>
											<td>{{$result['class_student_number']}}</td>
											<td>
												<a href="javascript:void(0);" title="Performance Graph" class="performance_graph" data-graphtype="currentstudent" data-studentid="{{ $result['student_number'] }}" data-examid="{{$result['exam_id']}}">
													<i class="fa fa-bar-chart" aria-hidden="true"></i>
												</a>
											</td>
											{{-- <td><span class="badge badge-success">{{ $result['student_status']}}</span></td> --}}
											<td>{{ $result['total_correct_answer'] }}/{{$result['countQuestions']}}</td>
											<?php
											// $accuracy = 0;
											// $accuracy = round((($result['total_correct_answer'] / $data['countQuestions']) * 100), 2);
											?>
											<td>
												{{-- <?php echo App\Helpers\Helper::getAbility($accuracy); ?> --}}
												{{round($result['student_ability'],2)}} ({{$result['student_normalize_ability']}}%)
											</td>
											{{-- <td><span class="badge badge-success">{{ $result['exam_status'] }}</span></td> --}}
											<td>{{$result['completion_time']}}</td>
											<?php
												$rank = 0;
												$rank = array_search($result['total_correct_answer'], $studentsRanks);
												$rank = (number_format($rank) + number_format(1));
											?>
											{{-- <td>{{ $rank }}</td> --}}
											<td>{{$result['student_ranking']}}</td>
											@for($i=0; $i < $result['countQuestions']; ++$i)
												@if(isset($result[$i]) && $result[$i] == 'true')
												@php  $CorrectAnswer[$i] = isset($CorrectAnswer[$i]) ? ($CorrectAnswer[$i] + 1) : 1; @endphp
												<td class="reports-result correct-icon" style={{$bg_correct_color}}>
													<span style="visibility: hidden;">{{__("languages.report.correct")}}</span>
													<i class="fa fa-check" aria-hidden="true"></i>
												</td>
												@elseif(isset($result[$i]) && $result[$i] == 'false')
												<td class="reports-result incorrect-icon" style={{$bg_incorrect_color}}>
													<span style="visibility: hidden;">{{__("languages.report.incorrect")}}</span>
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
											{{-- <td></td> --}}
											<td></td>
											<td></td>
											{{-- <td></td> --}}
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
											<td>{{__('languages.report.accuracy')}}</td>
											<td></td>
											<td></td>
											{{-- <td></td> --}}
											<td></td>
											<td></td>
											{{-- <td></td> --}}
											<td></td>
											<td></td>
											@for($i=0; $i < $result['countQuestions']; ++$i)
												@if(isset($CorrectAnswer[$i]) && !empty($CorrectAnswer[$i]))
													<td class="text-center-table">{{ round(((100 * $CorrectAnswer[$i]) / $result['countStudent']), 2); }}%</td>
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
							<h4 class="modal-title w-100">Student Question Analysis Graph</h4>
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

		@include('backend.layouts.footer')
		{{-- <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/chart.js"></script> --}}
		<script>
			$(function() {
				$(document).on('click', '.performance_graph', function(e) {
					$("#cover-spin").show();
					$examid = $(this).attr('data-examid');
					$studentid = $(this).attr('data-studentid')
					if($examid && $studentid){
						$.ajax({
							url: BASE_URL + '/report/getPerformanceGraphCurrentStudent',
							type: 'post',
							data : {
								'_token': $('meta[name="csrf-token"]').attr('content'),
								'exam_id' : $examid,
								'student_id' : $studentid
							},
							success: function(response) {
								var ResposnseData = JSON.parse(JSON.stringify(response));
								if(ResposnseData.data.length != 0){
									$('#graph-image').attr('src','data:image/jpg;base64,'+ ResposnseData.data);
									$('#studentPerformanceGraph').modal('show');
								}else{
									toastr.error('Student performance data not found');
								}
								$("#cover-spin").hide();
							},
							error: function(response) {
								ErrorHandlingMessage(response);
							}
						});
					}else{
						$("#cover-spin").hide();
						toastr.error('Data not found');
					}
				});
				$(document).on('click', '.question_graph', function(e) {
					$("#cover-spin").show();
					$examid = $(this).attr('data-examid');
					$studentid = $(this).attr('data-studentid');					
					$questionid = $(this).attr('data-questionid');
					if($examid && $studentid){
						$.ajax({
							url: BASE_URL + '/report/getQuestionGraphCurrentStudent',
							type: 'post',
							data : {
								'_token': $('meta[name="csrf-token"]').attr('content'),
								'exam_id' : $examid,
								'student_id' : $studentid,
								'question_id' : $questionid
							},
							success: function(response) {
								var ResposnseData = JSON.parse(JSON.stringify(response));
								if(ResposnseData.data.length != 0){
									$('#question-graph-image').attr('src','data:image/jpg;base64,'+ ResposnseData.data);
									$('#studentQuestionAnalysisGraph').modal('show');
								}else{
									toastr.error('Student performance data not found');
								}
								$("#cover-spin").hide();
							},
							error: function(response) {
								ErrorHandlingMessage(response);
							}
						});
					}else{
						$("#cover-spin").hide();
						toastr.error('Data not found');
					}
				});
			});
		</script>
@endsection
