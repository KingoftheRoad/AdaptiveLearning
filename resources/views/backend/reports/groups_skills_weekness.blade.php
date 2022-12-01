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
								<h5 class="mb-4">{{__('Groups Skills Weakness')}}</h5>
							</div>
							<hr class="blue-line">
						</div>
					</div>
					@if (session('error'))
					<div class="alert alert-danger">{{ session('error') }}</div>
					@endif
					<form class="class-test-report" id="class-test-report" action="{{route('report.class-test-reports.correct-incorrect-answer')}}" method="get">
					<div class="row">
						<div class="select-lng pt-2 pb-2 col-lg-2 col-md-4">
							<select name="exam_id"  id="exam_id" class="form-control select-option">
								<option value="">{{ __('Exams') }}</option>
								@if(!empty($ExamList))
									@foreach($ExamList as $exams)
									<option value="{{$exams->id}}" {{ request()->get('exam_id') == $exams->id ? 'selected' : '' }}>{{ $exams->title}}</option>
									@endforeach
								@endif
							</select>
							@if($errors->has('exam_id'))
								<span class="validation_error">{{ $errors->first('exam_id') }}</span>
							@endif
						</div>
						<div class="col-lg-2 col-md-3">
							<div class="select-lng pt-2 pb-2">
								<button type="submit" name="filter" value="filter" class="btn-search" id="filterReportClassTestResult">{{ __('Search') }}</button>
							</div>
						</div>
					</div>
					</form>
					<div class="row main-date-sec">
						@if(!empty($ExamData->publish_date))
						<div class="col-lg-3 col-md-3 ">
							<label><b>{{__("Publish Date: ")}}</b><span> {{!empty($ExamData->publish_date) ? date('Y/m/d H:i:s',strtotime($ExamData->publish_date)) : ''}}</span></label>
						</div>
						@endif
						<div class="col-lg-3 col-md-3">
							<label><b>{{__("From Date: ")}}</b> <span>{{!empty($ExamData->from_date) ? date('Y/m/d',strtotime($ExamData->from_date)) : ''}}</span></label>
						</div>
						<div class="col-lg-3 col-md-3">
							<label><b>{{__("To Date: ")}}</b> <span>{{!empty($ExamData->to_date) ? date('Y/m/d',strtotime($ExamData->to_date)): ''}}</span></label>
						</div>
						<div class="col-lg-3 col-md-3">
							<label><b>{{__("Result Date: ")}}</b> <span>{{ !empty($ExamData->result_date) ? date('Y/m/d',strtotime($ExamData->result_date)) : ''}}</span></label>
						</div>
					</div>
					
					<div class="row correct-incorrect-row mt-2 mb-2">
						<div class="col-md-12 correct-incorrect-col">
						<form id="exam-details-reports" action="{{ route('report.class-test-reports.correct-incorrect-answer')}}" method="get">
                            <input type="hidden" name="exam_id" id="exam_id" value="{{ request()->get('exam_id')}}">
							<div class="select-lng">
                                <button type="submit" name="filter" value="filter" class="btn-search remove-radius {{ (request()->is('report/class-test-reports/correct-incorrect-answer')) ? 'active': ''  }}">{{ __('Correct/Incorrect Test Report') }}</a>
							</div>
                            </form>
							<form id="exam-details-reports" action="{{ route('report.exams.student-test-performance')}}" method="get">
							<input type="hidden" name="details_report_exam_id" id="details_report_exam_id" value="{{ request()->get('exam_id')}}">
							<div class="select-lng">
								<input type="submit" class=" btn-search remove-radius class-test-report-detail-btn" value="{{ __('Details') }}">
							</div>
							</form>
							<form id="exam-details-reports" action="{{ route('report.school-comparisons')}}" method="get">
							<input type="hidden" name="exam_id" id="exam_id" value="{{ request()->get('exam_id')}}">
							<div class="select-lng">
								<input type="submit" class=" btn-search remove-radius school-comparison-btn " value="{{ __('School Comparison Result') }}">
							</div>
							</form>
							<form id="group-skill-weekness-reports" action="{{ route('report.groups-skill-weekness')}}" method="get">
							<input type="hidden" name="exam_id" id="exam_id" value="{{ request()->get('exam_id')}}">
							<div class="select-lng">
								<input type="submit" class=" btn-search remove-radius active" value="{{ __('Skills Weakness') }}">
							</div>
							</form>
						</div>
					</div>
				
					<div class="row">
						<div class="col-md-12">
						<div class="question-bank-sec class-test-report-scroll @if(empty($ResultList)) remove-overflow-scroll @endif">
							@if(!empty($groupName))
								<table id="school-comparision-report-datatable" class="display" style="width:100%">
							    	<thead>
							        	<tr>
											@if(!empty($groupName))
											@foreach($groupName as $group)
											<th class="first-head"><span>{{__('Correct Percentage of Questions in')}} {{$group}}</span></th>
											@endforeach
											@endif
                                            <th class="selec-opt"><span>{{__('Consider Hints of Which Nodes')}}</span></th>
							        	</tr>
							    	</thead>
							    	<tbody class="scroll-pane">
										<tr class="report-header-tr">
										@foreach($SkillWeeknessData as $data1)
										<td>{{$data1['value']}}%</td>
                                        @endforeach
										<td>{{$weekGroupDetails['group_name']}}
											<b style="display: block;">Weekness Hints</b>
											<ul>
											@foreach($weekGroupDetails['weeknessHints'] as $hints)
												@if(!empty($hints)) <li><?php echo $hints; ?></li> @endif
											@endforeach
											</ul>
										</td>
										</tr>
							  		</tbody>
								</table>
								@else
								<p style="text-align: center;">No Data Found</p>
								@endif
							</div>
						</div>
					</div>
				</div>
			</div>
	      </div>
		</div>
		@include('backend.layouts.footer')
@endsection
