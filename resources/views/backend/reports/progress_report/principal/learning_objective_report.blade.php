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
						<div class="sec-title">
                            <a href="javascript:void(0);" class="btn-back" id="backButton">{{__('languages.back')}}</a>
						</div>
						<hr class="blue-line">
					</div>
				</div>
				<!-- Include Study status color file -->
				@include('backend.reports.progress_report.study_status_color')
				<!-- End Include Study status color file -->
				
				<form class="mySubjects" id="mySubjects" method="get">
					<input type="hidden" name="isFilter" value="true">
					<div class="row">
						<div class="col-lg-2 col-md-2">
							<div class="select-lng pb-2">
								<label for="users-list-role">{{ __('languages.user_management.grade') }}</label>
								<select class="form-control" data-show-subtext="true" data-live-search="true" name="grade_id[]" id="student_multiple_grade_id" required >
									@if(!empty($GradesList))
									@foreach($GradesList as $grade)
									<option value="{{$grade['id']}}" @if(null !== request()->get('grade_id') && in_array($grade['id'],request()->get('grade_id'))) selected @elseif($loop->index==0) selected @endif>{{$grade['name']}}</option>
									@endforeach
									@endif
								</select>
							</div>
						</div>
						<div class="col-lg-2 col-md-3">
							<div class="select-lng pb-2">
								<label for="users-list-role">{{ __('languages.class') }}</label>
								<select name="class_type_id[]" class="form-control" id="classType-select-option" >
									@if(!empty($teachersClassList))
									@foreach($teachersClassList as $class)
									<option value="{{$class['class_id']}}" @if(null !== request()->get('class_type_id') && in_array($class['class_id'],request()->get('class_type_id'))) selected @elseif($classid == $class['class_id']) selected @endif>{{$class['class_name']}}</option>
									@endforeach
									@endif
								</select>
							</div>
						</div>
						<div class="col-lg-4 col-md-4">
							<div class="select-lng pb-2">
								<label for="users-list-role">{{ __('languages.strands') }}</label>
								<select name="learningReportStrand[]" class="form-control select-option" id="strand_id">
									@if(!empty($strandData))
									@foreach($strandData as $strand)
									<option value="{{$strand->id}}" 
									@if(null !== request()->get('learningReportStrand') && in_array($strand->id,request()->get('learningReportStrand'))) 
										selected
									@elseif($loop->index==0)
									selected 
									@endif
									>{{ $strand->{'name_'.app()->getLocale()} }}</option>
									@endforeach
									@endif
								</select>
							</div>
						</div>
						<div class="col-lg-4 col-md-4">
							<div class="select-lng pb-2">
								<label>{{__('languages.upload_document.learning_units')}}</label>
                                <select name="learning_unit_id" class="form-control select-option" id="learning_unit" >
                                    @if(isset($LearningUnits) && !empty($LearningUnits))
                                        @foreach ($LearningUnits as $learningUnitKey => $learningUnit)
                                            <option value="{{ $learningUnit->id }}" 
                                            	@if(null !== request()->get('learning_unit_id') && $learningUnit->id==request()->get('learning_unit_id')) 
													selected
												@elseif($loop->index==0)
												selected 
												@endif
											>{{ $learningUnit->{'name_'.app()->getLocale()} }}</option>
                                        @endforeach
                                    @else
                                        <option value="">{{__('languages.no_learning_units_available')}}</option>
                                    @endif
                                </select>
							</div>
						</div>
						<div class="select-lng pb-2 col-lg-2 col-md-4">
							<label for="users-list-role">{{ __('languages.report_type') }}</label>
							<select name="reportLearningType" class="form-control select-option" id="reportLearningType">
								<option value="">{{__("languages.all")}}</option>
								<option value="1" {{ request()->get('reportLearningType') == 1 ? 'selected' : '' }}>{{__("languages.testing_zone")}}</option>
								<option value="3" {{ request()->get('reportLearningType') == 3 ? 'selected' : '' }}>{{__("languages.tests")}}</option>
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
									<div class="table-responsive learning-progress-report-table-wrapper">
										<table class="table table-bordered learning-progress-report-table">
											<thead>
												<tr>
													<th>{{__('languages.student_name')}}</th>
													<th>{{__('languages.mastered')}}</th>
													@foreach($learningObjectivesList as $learningObjectives)
														<th>{{ $learningObjectives->foci_number }}</th>
													@endforeach
												</tr>
											</thead>
											<tbody>
												@foreach($learningUnits as $class_title => $classes)
													@foreach($classes as $students)
														<tr>
															<td><?php echo  App\Helpers\Helper::decrypt($students['student_data'][0]['name_'.app()->getLocale()]); ?></td>
															<td>
																<div class="progress objectives-report-progress" >
																	<div class="progress-bar p-1" data-toggle="tooltip" data-placement="top" title="{{$students['master_objectives']['accomplished_percentage']}}% ({{$students['master_objectives']['count_accomplished_learning_objectives']}}/{{$students['master_objectives']['no_of_learning_objectives']}})" style="width:{{$students['master_objectives']['accomplished_percentage']}}%;background-color:{{$ColorCodes['accomplished_color']}};">
																		@if(!empty($students['master_objectives']['accomplished_percentage']))
																		{{$students['master_objectives']['accomplished_percentage']}}%
																		@else
																		{{__('languages.not_available')}}
																		@endif
																	</div>
																	<div class="progress-bar p-1" data-toggle="tooltip" data-placement="top" title="{{$students['master_objectives']['not_accomplished_percentage']}}% ({{$students['master_objectives']['count_not_accomplished_learning_objectives']}}/{{$students['master_objectives']['no_of_learning_objectives']}})" style="width:{{$students['master_objectives']['not_accomplished_percentage']}}%;background-color:{{$ColorCodes['not_accomplished_color']}};">
																		@if(!empty($students['master_objectives']['not_accomplished_percentage']))
																		{{$students['master_objectives']['not_accomplished_percentage']}}%
																		@else
																		{{__('languages.not_available')}}
																		@endif
																	</div>
																</div>
															</td>
															@foreach($students['report_data'] as $report_data)
															<td>
																@php
																	$normalizedAbility=0;
																	$studyStatusColor='background:'.App\Helpers\Helper::getGlobalConfiguration('incomplete_color').';color:#FFF;';
																@endphp
																@if(!empty($report_data['normalizedAbility']))
																	@php
																		$normalizedAbility = $report_data['normalizedAbility'];
																		$studyStatusColor = '';
																	@endphp
																@endif
																<div class="progress" data-toggle="tooltip" data-placement="top"  title="{{$report_data['LearningsObjectives']}}" style="height: 2rem;{{ $studyStatusColor  }}">
																	<div class="progress-bar" style="background:{{ $report_data['studyStatusColor']; }};width: {{ $normalizedAbility }}%" role="progressbar" aria-valuenow="{{ $normalizedAbility }}" aria-valuemin="0" aria-valuemax="100"></div>
																	<span class="mt-1 ml-1 h6">
																	<?php if($report_data['ability'] !=0){
																		echo $report_data['ShortNormalizedAbility'];
																	}else{
																		echo __('languages.not_available');
																	}?></span>
																</div>
															</td>
															@endforeach		
														</tr>
													@endforeach
												@endforeach
											</tbody>
										</table>
									</div>
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
@include('backend.layouts.footer')
@include('backend.reports.progress_report.progress_report_js')
@endsection