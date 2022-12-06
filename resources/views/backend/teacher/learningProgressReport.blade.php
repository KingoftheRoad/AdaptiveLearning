@extends('backend.layouts.app')
@section('content')
<div class="wrapper d-flex align-items-stretch sm-deskbord-main-sec student-learning-report">
	@include('backend.layouts.sidebar')
	<div id="content" class="pl-2 pb-5">
		@include('backend.layouts.header')
		<div class="sm-right-detail-sec pl-5 pr-5">
			<div class="container-fluid">
				<div class="row">
					<div class="col-md-12">
						<div class="sec-title">
							<h2 class="mb-4 main-title">{{__('languages.progress_report')}}</h2>
						</div>
						<hr class="blue-line">
					</div>
				</div>
				<div class="row study_status_colors">
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
						<label>{{__('languages.not_available')}}</label>
					</div>
				</div>
				<form class="mySubjects" id="mySubjects" method="get">
					<input type="hidden" name="isFilter" value="true">
					<div class="row">
						<div class="col-lg-2 col-md-2">
							<div class="select-lng pb-2">
								<label for="users-list-role">{{ __('languages.user_management.grade') }}</label>
								<select class="form-control" data-show-subtext="true" data-live-search="true" name="grade_id[]" id="student_multiple_grade_id" multiple required >
									@if(!empty($GradesList))
									@foreach($GradesList as $grade)
									<option value="{{$grade['id']}}" @if(null !== request()->get('grade_id') && in_array($grade['id'],request()->get('grade_id'))) selected @elseif(null == request()->get('grade_id')) selected @endif>{{$grade['name']}}</option>
									@endforeach
									@endif
								</select>
							</div>
						</div>
						<div class="col-lg-2 col-md-3">
							<div class="select-lng pb-2">
								<label for="users-list-role">{{ __('languages.class') }}</label>
								<select name="class_type_id[]" class="form-control" id="classType-select-option" multiple>
									@if(!empty($teachersClassList))
									@foreach($teachersClassList as $class)
									<option value="{{$class['class_id']}}" @if(null !== request()->get('class_type_id') && in_array($class['class_id'],request()->get('class_type_id'))) selected @elseif(null == request()->get('class_type_id')) selected @endif>{{$class['class_name']}}</option>
									@endforeach
									@endif
								</select>
							</div>
						</div>
						<div class="col-lg-4 col-md-4">
							<div class="select-lng pb-2">
								<label for="users-list-role">{{ __('languages.strands') }}</label>
								<select name="learningReportStrand[]" multiple class="form-control select-option" id="learningReportStrandMuti">
									@if(!empty($strandData))
									@foreach($strandData as $strand)
									<option value="{{$strand->id}}" 
									@if(null !== request()->get('learningReportStrand') && in_array($strand->id,request()->get('learningReportStrand'))) 
										selected
									@elseif(null == request()->get('learningReportStrand')) 
									selected 
									@endif
									>{{ $strand->{'name_'.app()->getLocale()} }}</option>
									@endforeach
									@endif
								</select>
							</div>
						</div>
						<div class="select-lng pb-2 col-lg-2 col-md-4">
							<label for="users-list-role">{{ __('languages.report_type') }}</label>
							<select name="reportLearningType" class="form-control select-option" id="reportLearningType">
								<option value="">{{__("languages.all")}}</option>
								<option value="1" {{ request()->get('reportLearningType') == 1 ? 'selected' : '' }}>{{__("languages.self_learning")}}{{__("languages.test_text")}}</option>
								<option value="3" {{ request()->get('reportLearningType') == 3 ? 'selected' : '' }}>{{__("languages.test-only")}}</option>
							</select>
						</div>
						<div class="col-lg-2 col-md-3">
							<label for="users-list-role"></label>
							<div class="select-lng pt-2 pb-2">
								<button type="submit" name="filter" value="filter" class="btn-search" onclick="showCoverSpinLoader()">{{ __('languages.search') }}</button>
							</div>
						</div>
					</div>
				</form>
				@foreach($progressReportArray as $strandTitle => $strands)
				<div class="row">
					<div class="col-md-12">
						<h3>@if(isset($strandDataLbl[$strandTitle]) && !empty($strandDataLbl[$strandTitle]))
							{{$strandDataLbl[$strandTitle]}}
							@endif
						</h3>
					</div>
					@foreach($strands as $reportTitle => $learningUnits)
					<div class="col-xl-12 col-md-12 mb-4">
						<div class="card border-left-info shadow py-2 learning-unit-secion teacher-progress-report">
							<div class="card-body ml-2">
								<div class="row">
									<div class="col-md-12">
										<h5 class="font-weight-bold">
											@if(isset($LearningsUnitsLbl[$reportTitle]) && !empty($LearningsUnitsLbl[$reportTitle]))
											{{$LearningsUnitsLbl[$reportTitle]}}
											@endif
										</h5>
									</div>
									@foreach($learningUnits as $class_title => $classes)
										<div class="col-md-12 progress-report-class-title font-weight-bold">{{$class_title}}</div>
										<hr class="blue-line">
										@foreach($classes as $students)
										<div class="col-md-12">
											<div class="main-project-ratio">
												<hr class="blue-line">
												<?php $studentName = $students['student_data'][0]['name_'.app()->getLocale()]; ?>
												<p class="progress-report-student-name font-weight-bold">{{App\Helpers\Helper::decrypt($studentName)}}</p>												
												@foreach($students['report_data'] as $report_data)
													<div class="ratio text-center">
														<div class="project-ratio">
															<div class="project-ratio-inner" data-toggle="tooltip" data-placement="top"  title="{{$report_data['LearningsObjectives']}}" style="background:{{$report_data['studyStatusColor']}};">
															<p class="mt-3">
																@if(!empty($report_data['normalizedAbility']))
																	{{$report_data['normalizedAbility']}}%
																@else
																	{{'N/A'}}
																@endif
															</p>
														</div>
													</div>
													<span class="font-weight-bold" title="{{$report_data['LearningsObjectives']}}">{{$report_data['learning_objective_number']}}</span>
												</div>
												@endforeach
											</div>
										</div>
										@endforeach
									@endforeach
								</div>
							</div>
						</div>
					</div>
					@endforeach
				</div>
				@endforeach
			</div>
		</div>
	</div>
</div>
@endsection