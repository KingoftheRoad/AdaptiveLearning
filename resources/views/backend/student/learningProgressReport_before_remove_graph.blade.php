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
							<label>{{__('languages.incomplete')}}</label>
						</div>
					</div>
					<div class="row study_status_colors" >
						<div class="study_status_colors-sec">
							<strong>{{__('Accomplished Objective Status')}}:</strong>
						</div>
						<div class="study_status_colors-sec">
							<span class="dot-color" style="background-color: {{ App\Helpers\Helper::getGlobalConfiguration('accomplished_objective')}};border-radius: 50%;display: inline-block;"></span>
							<label>{{__('Accomplished')}}</label>
						</div>
						<div class="study_status_colors-sec">
							<span class="dot-color" style="background-color: {{ App\Helpers\Helper::getGlobalConfiguration('not_accomplished_objective')}};border-radius: 50%;display: inline-block;"></span>
							<label>{{__('Not Accomplished')}}</label>
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
									<button type="submit" name="filter" value="filter" class="btn-search">{{ __('languages.search') }}</button>
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
                    				<div class="col-xl-4 col-md-6 mb-4">
                                        <div class="card border-left-info shadow py-2">
                                            <div class="card-body p-1">
                                            	<div class="row">
                                                    <div class="col-md-12">
                                                        <h4 class="text-center font-weight-bold">
                                                            @if(isset($LearningsUnitsLbl[$reportTitle]) && !empty($LearningsUnitsLbl[$reportTitle]))
                                                                {{ $LearningsUnitsLbl[$reportTitle] }}
                                                            @endif
                                                        </h4>
														<div class="display_learning_result text-center font-weight-bold">
															<p class="objectives_title">Objectives Accomplished:</p>
															<span class="result_count_pass_objectives">{{$reportInfo['no_of_passed_learning_objectives']}}/{{$reportInfo['no_of_learning_objectives']}}</span>
														</div>
                                                    </div>
                                            		<div class="col-md-12 text-center">
													@php
													$bigGraphColorArray = [];
													$smallGraphColorArray = [];
													$abilityAvg = 0;
													$abilityRatioHtml = '';
                                                    $abilityNotZeroCount=0;
													if(!empty($reportInfo)){
														if(isset($reportDataAbilityArray[$strandTitle][$reportTitle])){
															$abilityRatioHtml.='<div class="main-project-ratio">';
																foreach($reportDataAbilityArray[$strandTitle][$reportTitle]['graphdata'] as $abilityData){
																	$ability = $abilityData['ability'];
																	$abilityAvg = $abilityAvg+$ability;
																	$accuracy_type = App\Helpers\Helper::getAbilityType($ability);
																	$abilityPr = App\Helpers\Helper::getNormalizedAbility($ability);
																	$bgColor = App\Helpers\Helper::getGlobalConfiguration('incomplete_color');
																	if($ability != 0){
																		$abilityNotZeroCount++;
																		$bgColor = App\Helpers\Helper::getGlobalConfiguration($accuracy_type);
																		$smallGraphColorArray[] = $bgColor;
																	}else{
																		$smallGraphColorArray[] = $bgColor;
																	}

																	$abilityRatioHtml.='<div class="ratio">
																		<div class="project-ratio">
																			<div class="project-ratio-inner" data-toggle="tooltip" data-placement="top"  title="'.$abilityData['LearningsObjectives'].'" style="background:'.$bgColor.';">
																				<!-- <h3>NAME</h3> -->
																				<p class="mt-3">'.round($ability,2).'('.$abilityPr.'%)</p>
																			</div>
																		</div>
																	</div>';
																}

																if(!empty($abilityAvg) && !empty($abilityNotZeroCount)){
																	$abilityAvg = round($abilityAvg/$abilityNotZeroCount,2);
																	$abilityAveragePercentage = App\Helpers\Helper::getNormalizedAbility($abilityAvg);
																}else{
																	$abilityAvg = 0;
																	$abilityAveragePercentage = 0;
																}
																
																$abilityRatioHtml.='</div>';
														}

														foreach($reportInfo['graphdata'] as $graphdata){
															if($graphdata['learning_objectives_result'] == 'pass'){
																$bigGraphColorArray[] = $accomplished_objective_color;
															}else{
																$bigGraphColorArray[] = $not_accomplished_objective_color;
															}
														}

														@endphp
														<div id="chartdiv_{{ $reportTitle }}" class="chartdiv"></div>
														@php
                                                        $learningObjectivesTitle = $abilityData['LearningsObjectives'];
                                                        
														$data.='var root = am5.Root.new("chartdiv_'.$reportTitle.'");
																root.setThemes([
																	am5themes_Animated.new(root)
																]);

                                                                var chart = root.container.children.push(
																	am5percent.PieChart.new(root, {
																		startAngle: 160, endAngle: 380
																	})
                                                                );


                                                                var series0 = chart.series.push(
																	am5percent.PieSeries.new(root, {
																		valueField: "learning_objectives_percentage",
																		categoryField: "LearningsObjectives",
																		startAngle: 160,
																		endAngle: 380,
																		radius: am5.percent(70),
																		innerRadius: am5.percent(65)
																	})
                                                                );

																var SmallGraphColorSet = am5.ColorSet.new(root, {
																				colors: '.json_encode($smallGraphColorArray).',
																				passOptions: {
																					lightness: -0.05,
																					hue: 0
																				}
																				});

                                                                series0.set("colors", SmallGraphColorSet);
																
                                                                series0.ticks.template.set("forceHidden", true);
                                                                series0.labels.template.set("forceHidden", true);
																series0.slices.template.set("tooltipText", "{LearningsObjectives}: {bottles}%");

                                                                var series1 = chart.series.push(
																	am5percent.PieSeries.new(root, {
																		startAngle: 160,
																		endAngle: 380,
																		valueField: "learning_objectives_percentage",
																		innerRadius: am5.percent(80),
																		categoryField: "LearningsObjectives"
																	})
                                                                );

                                                                series1.ticks.template.set("forceHidden", true);
                                                                series1.labels.template.set("forceHidden", true);
																<!-- series1.slices.template.set("tooltipText", "{LearningsObjectives}: {bottles}%"); -->
																series1.slices.template.set("tooltipText", "{LearningsObjectives}");

																var BigGraphColorSet = am5.ColorSet.new(root, {
																				colors: '.json_encode($bigGraphColorArray).',
																				passOptions: {
																					lightness: -0.05,
																					hue: 0
																				}
																				});
																series1.set("colors", BigGraphColorSet);

                                                                var label = chart.seriesContainer.children.push(
																	am5.Label.new(root, {
																		textAlign: "center",
																		centerY: am5.p100,
																		centerX: am5.p50,
																		text: "[fontSize:18px] Overall Ability \n on Started Objectives [/]:\n[bold fontSize:30px]'.$abilityAvg.'('.$abilityAveragePercentage.'%)'.'[/]"
																	})
                                                                );

                                                                var data = '.json_encode($reportInfo['graphdata'],JSON_PRETTY_PRINT).';
																series0.data.setAll(data);
																series1.data.setAll(data);';
																echo $abilityRatioHtml;
													}else{
														echo  __('languages.data_not_found');
													}
													@endphp
												</div>
											</div>
										</div>
									</div>
								</div>
                                @endforeach
                                <div class="col-md-12">
                                    <hr>
                                </div>
    				        </div>
                        @endforeach
                    @endif
    			</div>
			</div>
        </div>
    </div>
<style type="text/css">
.chartdiv {
  width: 100%;
  height: 250px;
}
/*.chartdiv canvas:nth-child(2) {
    display: none !important;
}*/
</style>
<!-- <script src="https://cdn.amcharts.com/lib/5/index.js"></script>
<script src="https://cdn.amcharts.com/lib/5/percent.js"></script>
<script src="https://cdn.amcharts.com/lib/5/themes/Animated.js"></script> -->
<script src="{{asset('js/amc_charts/index.js')}}"></script>
<script src="{{asset('js/amc_charts/percent.js')}}"></script>
<script src="{{asset('js/amc_charts/Animated.js')}}"></script>
<!-- Chart code -->
<script>
am5.ready(function() {
    {!! $data !!}
/*
var root = am5.Root.new("chartdiv");
root.setThemes([
  am5themes_Animated.new(root)
]);

var chart = root.container.children.push(
  am5percent.PieChart.new(root, {
    startAngle: 160, endAngle: 380
  })
);


var series0 = chart.series.push(
  am5percent.PieSeries.new(root, {
    valueField: "litres",
    categoryField: "country",
    startAngle: 160,
    endAngle: 380,
    radius: am5.percent(70),
    innerRadius: am5.percent(65)
  })
);

var colorSet = am5.ColorSet.new(root, {
  colors: [series0.get("colors").getIndex(0)],
  passOptions: {
    lightness: -0.05,
    hue: 0
  }
});

series0.set("colors", colorSet);

series0.ticks.template.set("forceHidden", true);
series0.labels.template.set("forceHidden", true);

var series1 = chart.series.push(
  am5percent.PieSeries.new(root, {
    startAngle: 160,
    endAngle: 380,
    valueField: "bottles",
    innerRadius: am5.percent(80),
    categoryField: "country"
  })
);

series1.ticks.template.set("forceHidden", true);
series1.labels.template.set("forceHidden", true);

var label = chart.seriesContainer.children.push(
  am5.Label.new(root, {
    textAlign: "center",
    centerY: am5.p100,
    centerX: am5.p50,
    text: "[fontSize:18px]total[/]:\n[bold fontSize:30px]1647.9[/]"
  })
);

var data = [
  {
    country: "Lithuania",
    litres: 501.9,
    bottles: 1500
  },
  {
    country: "Czech Republic",
    litres: 301.9,
    bottles: 990
  },
  {
    country: "Ireland",
    litres: 201.1,
    bottles: 785
  },
  {
    country: "Germany",
    litres: 165.8,
    bottles: 255
  },
  {
    country: "Australia",
    litres: 139.9,
    bottles: 452
  },
  {
    country: "Austria",
    litres: 128.3,
    bottles: 332
  },
  {
    country: "UK",
    litres: 99,
    bottles: 150
  },
  {
    country: "Belgium",
    litres: 60,
    bottles: 178
  },
  {
    country: "The Netherlands",
    litres: 50,
    bottles: 50
  }
];

series0.data.setAll(data);
series1.data.setAll(data);
*/
});
</script>
@endsection