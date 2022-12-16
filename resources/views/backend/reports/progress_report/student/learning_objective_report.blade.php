@extends('backend.layouts.app')
@section('content')
<style type="text/css">
.progress-sm {
  height: .5rem;
}
.position-center {
  left: 50%;
  top: 50%;
  -webkit-transform: translate(-50%,-50%);
  transform: translate(-50%,-50%);
  position: absolute !important;
  display: block;
  font-size: 20px;
}
.cm-progress-bar.progress-bar {
  text-align: right;
  color: #FFF;
  font-weight: bold;
}
.text-geay{
  color: gray;
}
</style>
<div class="wrapper d-flex align-items-stretch sm-deskbord-main-sec student-learning-report">
  @include('backend.layouts.sidebar')
	<div id="content" class="pl-2 pb-5">
		@include('backend.layouts.header')
		<div class="sm-right-detail-sec pl-5 pr-5">
			<div class="container-fluid">
				<div class="row">
					<div class="col-md-12">
						<div class="sec-title">
							{{-- <h2 class="mb-4 main-title">{{__('languages.sidebar.learning')}}</h2> --}}
							<h2 class="mb-4 main-title">{{ __('languages.learning_objectives') }} {{__('languages.report_text')}}</h2>
							
						</div>
						<div class="sec-title">
                            <a href="javascript:void(0);" class="btn-back" id="backButton">{{__('languages.back')}}</a>
						</div>
                        <hr class="blue-line">
                      </div>
                    </div>
					@include('backend.student.student_profile_menus')
					
					<!-- Include Study status color file -->
					@include('backend.reports.progress_report.study_status_color')
					<!-- End Include Study status color file -->

                    <form class="mySubjects" id="mySubjects" method="get">	
					<input type="hidden" name="isFilter" value="true">
                      	<div class="row">
							<div class="col-lg-3 col-md-3">
								<div class="select-lng pb-2">
									<label for="users-list-role">{{ __('languages.strands') }}</label>
									<select name="learningReportStrand[]" class="form-control select-option" id="strand_id">
									@if(!empty($strandData))
										@foreach($strandData as $strand)
										<option value="{{$strand->id}}" @if(null !== request()->get('learningReportStrand') && in_array($strand->id,request()->get('learningReportStrand'))) selected @elseif($loop->index == 0) selected @endif > {{ $strand->{'name_'.app()->getLocale()} }}</option>
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

                    @php
                        $data='';
                    @endphp
                    @foreach($reportDataArray as $strandTitle => $reportData)
					<div class="row">
						 @foreach($reportData as $reportTitle => $reportInfo)
						<div class="col-xl-12 col-md-12 mb-4">
							<div class="card border-left-info shadow py-2 learning-unit-secion teacher-progress-report">
								<div class="card-body ml-2">
									<div class="row">
										<div class="col-md-12">
											<h5 class="font-weight-bold">
												@if(isset($strandDataLbl[$strandTitle]) && !empty($strandDataLbl[$strandTitle]))
			                                        {{ $strandDataLbl[$strandTitle] }}
			                                    @endif
											</h5>
										</div>
										<div class="table-responsive learning-progress-report-table-wrapper">
											<table class="table table-bordered learning-progress-report-table">
												<thead>
													<tr>
														<th style="width: 12%;min-width: 12%;">{{__('languages.student_name')}}</th>
														<th>{{__('languages.mastered')}}</th>
														@foreach($learningObjectivesList as $learningObjectives)
															<th style="width: 10% !important;min-width: 10% !important;">{{ $learningObjectives->foci_number }}</th>
														@endforeach
													</tr>
												</thead>
												<tbody>
													<tr>
														<td style="width: 12%;min-width: 12%;">
															<!-- {{ (auth()->user()->name_en) ? App\Helpers\Helper::decrypt(auth()->user()->name_en) : auth()->user()->name }} -->
															@if(app()->getLocale()=='en')
																{{$studentData->DecryptNameEn}}
															@else
																{{$studentData->DecryptNameCh}}
															@endif
														</td>
														<td>
															<div class="progress objectives-report-progress">
																<div class="progress-bar p-1" data-toggle="tooltip" data-placement="top" title="{{$reportInfo['master_objectives']['accomplished_percentage']}}% ({{$reportInfo['master_objectives']['count_accomplished_learning_objectives']}}/{{$reportInfo['master_objectives']['no_of_learning_objectives']}})" style="width:{{$reportInfo['master_objectives']['accomplished_percentage']}}%;background-color:{{$ColorCodes['accomplished_color']}};">
																	@if(!empty($reportInfo['master_objectives']['accomplished_percentage']))
																	{{$reportInfo['master_objectives']['accomplished_percentage']}}%
																	@else
																	{{__('languages.not_available')}}
																	@endif
																</div>
																<div class="progress-bar p-1" data-toggle="tooltip" data-placement="top" title="{{$reportInfo['master_objectives']['not_accomplished_percentage']}}% ({{$reportInfo['master_objectives']['count_not_accomplished_learning_objectives']}}/{{$reportInfo['master_objectives']['no_of_learning_objectives']}})" style="width:{{$reportInfo['master_objectives']['not_accomplished_percentage']}}%;background-color:{{$ColorCodes['not_accomplished_color']}};">
																	@if(!empty($reportInfo['master_objectives']['not_accomplished_percentage']))
																	{{$reportInfo['master_objectives']['not_accomplished_percentage']}}%
																	@else
																	{{__('languages.not_available')}}
																	@endif
																</div>
															</div>
														</td>
														@if(!empty($reportInfo))
															@if(isset($reportDataAbilityArray[$strandTitle][$reportTitle]))
															@foreach($reportDataAbilityArray[$strandTitle][$reportTitle] as $abilityData)
																<td style="width: 10% !important;min-width: 10% !important;">
																	@php
																		$normalizedAbility=0;
																		$studyStatusColor='background:'.App\Helpers\Helper::getGlobalConfiguration('incomplete_color').';color:#FFF;';
																	@endphp
																	@if(!empty($abilityData['normalizedAbility']))
																		@php
																			$normalizedAbility=$abilityData['normalizedAbility'];
																			$studyStatusColor='';
																		@endphp
																	@endif
																	<div class="progress" data-toggle="tooltip" data-placement="top"  title="{{$abilityData['LearningsObjectives']}}" style="height: 2rem;{{ $studyStatusColor  }}">
																		<div class="progress-bar" style="background:{{ $abilityData['studyStatusColor']; }};width: {{ $normalizedAbility }}%" role="progressbar" aria-valuenow="{{ $normalizedAbility }}" aria-valuemin="0" aria-valuemax="100"></div>
																		<span class="mt-1 ml-1 h6">
																		<?php if($abilityData['ability'] !=0){
																			echo $abilityData['ShortNormalizedAbility'];
																		}else{
																			echo __('languages.not_available');
																		}?>
																		</span>
																	</div>
																</td>
															@endforeach
															@endif
														@else
															{{__('languages.data_not_found')}}
														@endif
													</tr>
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