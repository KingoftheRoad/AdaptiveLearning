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
								<h5 class="mb-4">{{__('languages.report.school_comparisons_report')}}</h5>
							</div>
							<hr class="blue-line">
						</div>
					</div>
					@if (session('error'))
					<div class="alert alert-danger">{{ session('error') }}</div>
					@endif
					{{-- <form class="class-test-report" id="class-test-report" action="{{route('report.class-test-reports.correct-incorrect-answer')}}" method="get">
						<div class="row">
							<div class="select-lng pt-2 pb-2 col-lg-4 col-md-4">
								<label>{{ __('languages.select_test') }}</label>
								<select name="exam_id"  id="exam_id" class="form-control select-option performance_exam_id">
									<option value="">{{ __('languages.report.tests') }}</option>
									@if(!empty($ExamList))
										@foreach($ExamList as $exams)
											<option value="{{$exams->id}}" data-examtype="{{$exams->exam_type}}"  {{ request()->get('exam_id') == $exams->id ? 'selected' : '' }}>{{$exams->title}} @if(isset($exams->reference_no)) ({{$exams->reference_no}}) @endif</option>
										@endforeach
									@endif
								</select>
								@if($errors->has('exam_id'))
									<span class="validation_error">{{ $errors->first('exam_id') }}</span>
								@endif
							</div>
							<div class="col-lg-2 col-md-3">
								<div class="select-lng pt-2 pb-2">
									<button type="submit" name="filter" value="filter" class="btn-search button-margin-manage" id="filterReportClassTestResult">{{ __('languages.search') }}</button>
								</div>
							</div>
						</div>
					</form> --}}
					<div class="row main-date-sec">
						@if(!empty($ExamData->publish_date))
						<div class="col-lg-3 col-md-3 ">
							<label><b>{{__("languages.report.date_of_release")}}: </b><span> {{!empty($ExamData->publish_date) ? date('d/m/Y H:i:s',strtotime($ExamData->publish_date)) : ''}}</span></label>
						</div>
						@endif
						<div class="col-lg-3 col-md-3">
							<label><b>{{__("languages.report.start_date")}}: </b> <span>{{!empty($ExamData->from_date) ? date('d/m/Y',strtotime($ExamData->from_date)) : ''}}</span></label>
						</div>
						<div class="col-lg-3 col-md-3">
							<label><b>{{__("languages.report.end_date")}}: </b> <span>{{!empty($ExamData->to_date) ? date('d/m/Y',strtotime($ExamData->to_date)): ''}}</span></label>
						</div>
						<div class="col-lg-3 col-md-3">
							<label><b>{{__("languages.report.result_date")}}: </b> <span>{{ !empty($ExamData->result_date) ? date('d/m/Y',strtotime($ExamData->result_date)) : ''}}</span></label>
						</div>
					</div>
					
					<div class="row correct-incorrect-row mt-2 mb-2">
						<div class="col-md-12 correct-incorrect-col">
						<form id="exam-details-reports" action="{{ route('report.class-test-reports.correct-incorrect-answer')}}" method="get">
                            <input type="hidden" name="exam_id" id="exam_id" value="{{ request()->get('exam_id')}}">
							<input type="hidden" name="exam_school_id" value="{{request()->get('exam_school_id')}}">
							<input type="hidden" name="grade_id" value="{{request()->get('grade_id')}}">
							@if(isset($class_type_id) && !empty($class_type_id))
								@foreach($class_type_id as $class_type)
									<input type="hidden" name="class_type_id[]" value="{{ $class_type }}">
								@endforeach
							@endif
							<div class="select-lng">
								<!-- <a href="javascript:void(0);" class="btn-search remove-radius {{ (request()->is('report/class-test-reports/correct-incorrect-answer?')) ? 'active': ''  }}">{{ __('Correct/Incorrect Test Report') }}</a> -->
                                <button type="submit" name="filter" value="filter" class="btn-search remove-radius {{ (request()->is('report/class-test-reports/correct-incorrect-answer')) ? 'active': ''  }}">{{ __('languages.report.class_performance') }}</a>
							</div>
                            </form>
							{{-- <form id="exam-details-reports" action="{{ route('report.exams.student-test-performance')}}" method="get">
							<input type="hidden" name="details_report_exam_id" id="details_report_exam_id" value="{{ request()->get('exam_id')}}">
							<div class="select-lng">
								<input type="submit" class=" btn-search remove-radius class-test-report-detail-btn" value="{{ __('languages.report.details') }}">
							</div>
							</form> --}}
							<?php if(Auth::user()->role_id == 1){ ?>
							<form id="exam-details-reports" action="{{ route('report.school-comparisons')}}" method="get">
							<input type="hidden" name="exam_id" id="exam_id" value="{{ request()->get('exam_id')}}">
							<div class="select-lng">
								<input type="submit" class=" btn-search remove-radius active school-comparison-btn " value="{{ __('languages.report.school_comparison_result') }}">
							</div>
							</form>
							<?php } ?>
						</div>
					</div>
				
					@if($reportType == 'singleTest')
					<div class="row">
						<div class="col-md-12">
						<div class="question-bank-sec class-test-report-scroll @if(empty($ResultList)) remove-overflow-scroll @endif">
							@if(!empty($SchoolReportsData))
								<table id="school-comparision-report-datatable" class="display" style="width:100%">
							    	<thead>
							        	<tr>
							          		<th class="first-head"><span>#{{__('languages.report.school_id')}}</span></th>
											<th class="sec-head selec-opt"><span>{{__('languages.report.school_name')}}</span></th>
											<th class="selec-opt"><span>{{__('languages.report.total_no_of_students_in_class')}}</span></th>
											<th class="selec-opt"><span>{{__('languages.report.no_of_students_who_attempted_test')}}</span></th>
											<th class="selec-opt"><span>{{__('languages.report.total_no_of_questions')}}</span></th>
											<th class="selec-opt"><span>{{__('languages.report.average_correct_answers')}}</span></th>
                                            <th class="selec-opt"><span>{{__('languages.report.average_accuracy')}}(%)</span></th>
							        	</tr>
							    	</thead>
							    	<tbody class="scroll-pane">
										@foreach($SchoolReportsData as $key => $school)
							        	<tr class="report-header-tr">
											<td>#{{$school['school_id']}}</td>
											<td>{{$school['school_name']}}</td>
											<td>{{$school['total_students']}}</td>
											<td>{{$school['total_attempted_exams_students']}}</td>
											<td>{{$school['no_of_total_questions']}}</td>
                                            <td>{{ $school['no_of_correct_answers'] }}</td>
                                            <td class="school-comparision-progress">
                                                <div class="progress">
                                                    <div class="progress-bar ans-correct" role="progressbar" aria-valuenow="{{$school['average_of_correct_answers']}}" aria-valuemin="0" aria-valuemax="100" style="width:{{$school['average_of_correct_answers']}}">
                                                        <div class="anser-detail pl-2">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="answer-progress">
                                                    <p class="progress-percentage">{{ $school['average_of_correct_answers'] }}</p>
                                                </div>
                                            </td>
										</tr>
                                        @endforeach
							  		</tbody>
								</table>
								@else
								<p style="text-align: center;">{{__('languages.report.no_data_found')}}</p>
							@endif
							</div>
						</div>
					</div>
					@endif
				</div>
			</div>
	      </div>
		</div>
		@include('backend.layouts.footer')
@endsection
