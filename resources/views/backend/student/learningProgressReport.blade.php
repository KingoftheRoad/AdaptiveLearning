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
							<h2 class="mb-4 main-title">{{__('languages.sidebar.learning')}}</h2>
						</div>
                        <hr class="blue-line">
                      </div>
                    </div>
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
							<label>{{__('N/A')}}</label>
						</div>
					</div>
                    <form class="mySubjects" id="mySubjects" method="get">	
                      	<div class="row">
							<div class="col-lg-3 col-md-3">
								<div class="select-lng pt-2 pb-2">
									<select name="learningReportStrand[]" multiple class="form-control select-option" id="learningReportStrandMuti">
									@if(!empty($strandData))
										@foreach($strandData as $strand)
										<option value="{{$strand->id}}" @if(null !== request()->get('learningReportStrand') && in_array($strand->id,request()->get('learningReportStrand'))) selected @elseif(null == request()->get('learningReportStrand')) selected @endif > {{ $strand->{'name_'.app()->getLocale()} }}</option>
										@endforeach
									@endif
									</select>
								</div>
							</div>
							<div class="select-lng pt-2 pb-2 col-lg-2 col-md-4">                            
								<select name="reportLearningType" class="form-control select-option" id="reportLearningType">
									<option value="">{{__("languages.all")}}</option>
									<option value="1" {{ request()->get('reportLearningType') == 1 ? 'selected' : '' }}>{{__("languages.self_learning")}}{{__("languages.test_text")}}</option>
									<option value="3" {{ request()->get('reportLearningType') == 3 ? 'selected' : '' }}>{{__("languages.test-only")}}</option>
								</select>
							</div>
							<div class="col-lg-2 col-md-3">
								<div class="select-lng pt-2 pb-2">
									<button type="submit" name="filter" value="filter" class="btn-search" onclick="showCoverSpinLoader()">{{ __('languages.search') }}</button>
								</div>
							</div>
                      	</div>
                    </form>

                    @php
                        $data='';
                    @endphp
                    @if(!empty($reportDataArray))
                        @foreach($reportDataArray as $strandTitle => $reportData)
                            <div class="row">
                                <div class="col-md-12">
                                    <h3>@if(isset($strandDataLbl[$strandTitle]) && !empty($strandDataLbl[$strandTitle]))
                                        {{ $strandDataLbl[$strandTitle] }}
                                    @endif
                                    </h3>
                                </div>
                                @foreach($reportData as $reportTitle => $reportInfo)
                    				<div class="col-xl-12 col-md-12 mb-4">
                                        <div class="card border-left-info shadow py-2 learning-unit-secion">
                                            <div class="card-body ml-2">
                                            	<div class="row">
                                                    <div class="col-md-12">
                                                        <h5 class="font-weight-bold">
                                                            @if(isset($LearningsUnitsLbl[$reportTitle]) && !empty($LearningsUnitsLbl[$reportTitle]))
                                                                {{ $LearningsUnitsLbl[$reportTitle] }}
                                                            @endif
                                                        </h5>
                                                    </div>
                                            		<div class="col-md-12">													
													@if(!empty($reportInfo))
														@if(isset($reportDataAbilityArray[$strandTitle][$reportTitle]))
														<div class="main-project-ratio">
														@foreach($reportDataAbilityArray[$strandTitle][$reportTitle] as $abilityData)
															<div class="ratio text-center">
																<div class="project-ratio">
																	<div class="project-ratio-inner" data-toggle="tooltip" data-placement="top"  title="{{$abilityData['LearningsObjectives']}}" style="background:{{$abilityData['studyStatusColor']}};">
																		<p class="mt-3">
																			@if(!empty($abilityData['normalizedAbility']))
																				{{$abilityData['normalizedAbility']}}%
																			@else
																				{{'N/A'}}
																			@endif
																		</p>
																	</div>
																</div>
																<span class="font-weight-bold" title="{{$abilityData['LearningsObjectives']}}">{{$abilityData['learning_objective_number']}}</span>
															</div>
														@endforeach
														</div>
														@endif
													@else
														{{__('languages.data_not_found')}}
													@endif
												</div>
											</div>
										</div>
									</div>
								</div>
                                @endforeach
    				        </div>
                        @endforeach
                    @endif
    			</div>
			</div>
        </div>
    </div>
@endsection