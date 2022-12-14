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
						<div class="sec-title">
                            <a href="javascript:void(0);" class="btn-back" id="backButton">{{__('languages.back')}}</a>
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
										<table class="table table-bordered learning-progress-report-table">
											<thead>
												<tr>
													<th style="width: 12%;min-width: 12%;">Student Name</th>
													@foreach($learningObjectivesList as $learningObjectives)
														<th style="width: 10% !important;min-width: 10% !important;">{{ $learningObjectives->foci_number }}</th>
													@endforeach
												</tr>
											</thead>
											<tbody>
												<tr>
													<td style="width: 12%;min-width: 12%;">
														{{ (auth()->user()->name_en) ? App\Helpers\Helper::decrypt(auth()->user()->name_en) : auth()->user()->name }}
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
																	<span class="position-absolute mt-1 ml-1 h6">{{ ($normalizedAbility!=0 ? $normalizedAbility.'%' : 'N/A') }}</span>
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
						@endforeach
					</div>
				@endforeach
    			</div>
			</div>
        </div>
    </div>
	@include('backend.layouts.footer')
<script type="text/javascript">
	/**
	 * USE : Get Learning Units from multiple strands
	 * **/
	$(document).on('change', '#strand_id', function() {
		$strandIds = new Array();
		$strandIds.push($('#strand_id').val())
		if($strandIds != ""){
			$.ajax({
				url: BASE_URL + '/getLearningUnitFromMultipleStrands',
				type: 'POST',
				data: {
					'_token': $('meta[name="csrf-token"]').attr('content'),
					'grade_id': $('#grade-id').val(),
					'subject_id': $('#subject-id').val(),
					'strands_ids': $strandIds
				},
				success: function(response) {
					$('#learning_unit').html('');
					$("#cover-spin").hide();
					var data = JSON.parse(JSON.stringify(response));
					if(data){
						if(data.data){
							$(data.data).each(function() {
								var option = $('<option />');
								option.attr('value', this.id).text(this.name);
								option.attr('selected', 'selected');
								$('#learning_unit').append(option);
							});
						}else{
							$('#learning_unit').html('<option value="">'+LEARNING_UNITS_NOT_AVAILABLE+'</option>');
						}
					}else{
						$('#learning_unit').html('<option value="">'+LEARNING_UNITS_NOT_AVAILABLE+'</option>');
					}
					$('#learning_unit').multiselect("rebuild");
					$('#learning_unit').trigger("change");
				},
				error: function(response) {
					ErrorHandlingMessage(response);
				}
			});
		}else{
			$('#learning_unit, #learning_objectives').html('');
			$('#learning_unit, #learning_objectives').multiselect("rebuild");
		}
	});
</script>
@endsection