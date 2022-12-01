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
	.text-geay
	{
		color: gray;
	}
</style>
		<div class="wrapper d-flex align-items-stretch sm-deskbord-main-sec">
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
                <form class="mySubjects" id="mySubjects" method="get">	
                  <div class="row">
                    <div class="col-lg-3 col-md-3">
                      <div class="select-lng pt-2 pb-2">
                        <select name="learningReportStrand" class="form-control select-option" id="learningReportStrand">
                          <option value="">{{__("languages.select_strand")}}</option>
                          @if(!empty($strandData))
                            @foreach($strandData as $strand)
                            <option value="{{$strand->id}}" {{ request()->get('learningReportStrand') == $strand->id ? 'selected' : '' }}>{{ $strand->{'name_'.app()->getLocale()} }}</option>
                            @endforeach
                          @endif
                        </select>
                      </div>
                    </div>
                    <div class="select-lng pt-2 pb-2 col-lg-2 col-md-4">                            
                      <select name="reportLearningType" class="form-control select-option" id="reportLearningType">
                        <option value="">{{__("languages.select_learning_type")}}</option>
                        <option value="1" {{ request()->get('reportLearningType') == 1 ? 'selected' : '' }}>{{__("languages.self_learning")}}</option>
                        <option value="2" {{ request()->get('reportLearningType') == 2 ? 'selected' : '' }}>{{__("languages.excercise")}}</option>
                        <option value="3" {{ request()->get('reportLearningType') == 3 ? 'selected' : '' }}>{{__("languages.self_learning")}} & {{__('languages.exercise')}}</option>
                      </select>
                    </div>
                    <div class="col-lg-2 col-md-3">
                      <div class="select-lng pt-2 pb-2">
                        <button type="submit" name="filter" value="filter" class="btn-search">{{ __('languages.search') }}</button>
                      </div>
                    </div>
                  </div>
                </form>
                <div class="row">
									<div class="col-xl-4 col-md-6 mb-4">
                    <div class="card border-left-info shadow py-2">
                        <div class="card-body p-1">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col-auto p-1 m-1 bg-info text-white">
                                            <i class="fa fa-cogs fa-3x text-gray-300"></i>
                                        </div>
                                        <div class="col">
				                                    <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Tasks
				                                    </div>
                                            <div class="progress progress-sm mr-2">
                                                <div class="progress-bar bg-info" role="progressbar" style="width: 50%" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100"></div>
                                            </div>
                                            <span style="font-size: 12px;" class="text-info font-weight-bold">03 <span class="text-geay">/ 21 Lorem Ipsum is simply</span> </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card border-left-info shadow py-2">
                        <div class="card-body p-1">
                        	<div class="row">
                        		<div class="col-md-7">
                        			<div class="row mt-4">
	                        			<div class="col-md-8 p-0">
	                        				<div id="chart2"></div>
	                        			</div>
	                        			<div class="col-md-4 p-0">
	                        					<button class="btn btn-danger btn-sm mt-4">Very Low</button>
	                        			</div>
	                        		</div>
                        		</div>
                        		<div class="col-md-5">
                        			<div id="chart"></div>
                        		</div>
                        	</div>
                       	</div>
                    </div>
                    <div class="card border-left-info shadow">
                        <div class="card-body p-2" style="position:relative;">
                        	<div class="row">
                        		<div class="col-md-10 mt-1">
                        			<div class="progress mb-4">
                                  <div class="progress-bar  cm-progress-bar bg-warning" role="progressbar" style="width: 40%" aria-valuenow="40" aria-valuemin="0" aria-valuemax="100"></div>
                              </div>
                            </div>
                            <div class="col-md-2 p-0">
                              <small>5/12</small>
                            </div>
                            <div class="col-md-12 mb-3">
                            	<h5 class="font-weight-bold">Interpreting Graphs</h5>
                            	<span class="text-geay">Lorem Ipsum is simply dummy text </span>
                            </div>
                            <div class="col-md-12">
                            		<button class="btn btn-default text-uppercase"><span class="fa fa-tv"></span> 23 lessions</button>
                            		<button class="btn btn-warning text-uppercase"><span class="fa fa-star-o"></span> 72 xp</button>
                            		<button class="btn btn-info text-uppercase">View <span class="fa fa-arrow-right"></span></button>
                            </div>
                        	</div>
                        </div>
                    </div>
                    <div class="card border-left-info shadow">
                        <div class="card-body" style="position:relative;">
                        	<div class="row">
                        			<div class="col-md-12 font-weight-bold mb-2"><h4>Study status - <span class="text-warning">Average</span></h4></div>
                        			<div class="col-md-4 border-right p-2">
                        					<img src="https://picsum.photos/100">
                        			</div>
                        			<div class="col-md-8">
                        					<div class="row">
                        							<div class="col-md-6">
                        								<span class="text-geay">Attempted</span>
                        								<h5>2151 <small>qm</small></h5>
                        							</div>
                        							<div class="col-md-6">
                        								<span class="text-geay">Score</span>
                        								<h5>24 / <small>24</small></h5>
                        							</div>
                        							<div class="col-md-6">
                        								<span class="text-geay">Speed</span>
                        								<h5>30 <small>qr/hr</small></h5>
                        							</div>
                        							<div class="col-md-6">
                        								<span class="text-geay">Accuracy</span>
                        								<h5>68 <small>%</small></h5>
                        							</div>
                        					</div>
                        			</div>
                        	</div>
                        </div>
                    </div>
                    <div class="card shadow mb-4">
                          <div class="card-body m-0">
                          		<div class="font-weight-bold mb-4"><h4>Questions by difficulty</h4></div>
                              <h4 class="small font-weight-bold">Easy <span class="float-right">20%</span></h4>
                              <div class="progress mb-4">
                                  <div class="progress-bar  cm-progress-bar bg-danger" role="progressbar" style="width: 20%" aria-valuenow="20" aria-valuemin="0" aria-valuemax="100"><span class="mr-2">20%</span></div>
                              </div>
                              <h4 class="small font-weight-bold">Medium <span class="float-right">40%</span></h4>
                              <div class="progress mb-4">
                                  <div class="progress-bar  cm-progress-bar bg-warning" role="progressbar" style="width: 40%" aria-valuenow="40" aria-valuemin="0" aria-valuemax="100"><span class="mr-2">40%</span></div>
                              </div>
                              <h4 class="small font-weight-bold">Hard <span class="float-right">60%</span></h4>
                              <div class="progress mb-4">
                                  <div class="progress-bar cm-progress-bar" role="progressbar" style="width: 60%" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100"><span class="mr-2">60%</span></div>
                              </div>
                          </div>
                      </div>
                  </div>
									<!-- 
											col-1
									 -->
									<div class="col-xl-4 col-md-6 mb-4">
                    <div class="card border-left-info shadow py-2">
                        <div class="card-body p-1">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col-auto p-1 m-1 bg-info text-white">
                                            <i class="fa fa-cogs fa-3x text-gray-300"></i>
                                        </div>
                                        <div class="col">
				                                    <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Tasks
				                                    </div>
                                            <div class="progress progress-sm mr-2">
                                                <div class="progress-bar bg-info" role="progressbar" style="width: 50%" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100"></div>
                                            </div>
                                            <span style="font-size: 12px;" class="text-info font-weight-bold">03 <span class="text-geay">/ 21 Lorem Ipsum is simply</span> </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card border-left-info shadow py-2">
                        <div class="card-body p-1">
                        	<div class="row">
                        		<div class="col-md-7">
                        			<div class="row mt-4">
	                        			<div class="col-md-8 p-0">
	                        				<div id="chart4"></div>
	                        			</div>
	                        			<div class="col-md-4 p-0">
	                        					<button class="btn btn-danger btn-sm mt-4">Very Low</button>
	                        			</div>
	                        		</div>
                        		</div>
                        		<div class="col-md-5">
                        			<div id="chart3"></div>
                        		</div>
                        	</div>
                       	</div>
                    </div>
                    <div class="card border-left-info shadow">
                        <div class="card-body p-2" style="position:relative;">
                        	<div class="row">
                        		<div class="col-md-10 mt-1">
                        			<div class="progress mb-4">
                                  <div class="progress-bar  cm-progress-bar bg-warning" role="progressbar" style="width: 40%" aria-valuenow="40" aria-valuemin="0" aria-valuemax="100"></div>
                              </div>
                            </div>
                            <div class="col-md-2 p-0">
                              <small>5/12</small>
                            </div>
                            <div class="col-md-12 mb-3">
                            	<h5 class="font-weight-bold">Interpreting Graphs</h5>
                            	<span class="text-geay">Lorem Ipsum is simply dummy text </span>
                            </div>
                            <div class="col-md-12">
                            		<button class="btn btn-default text-uppercase"><span class="fa fa-tv"></span> 23 lessions</button>
                            		<button class="btn btn-warning text-uppercase"><span class="fa fa-star-o"></span> 72 xp</button>
                            		<button class="btn btn-info text-uppercase">View <span class="fa fa-arrow-right"></span></button>
                            </div>
                        	</div>
                        </div>
                    </div>
                    <div class="card border-left-info shadow">
                        <div class="card-body" style="position:relative;">
                        	<div class="row">
                        			<div class="col-md-12 font-weight-bold mb-2"><h4>Study status - <span class="text-warning">Average</span></h4></div>
                        			<div class="col-md-4 border-right p-2">
                        					<img src="https://picsum.photos/100">
                        			</div>
                        			<div class="col-md-8">
                        					<div class="row">
                        							<div class="col-md-6">
                        								<span class="text-geay">Attempted</span>
                        								<h5>2151 <small>qm</small></h5>
                        							</div>
                        							<div class="col-md-6">
                        								<span class="text-geay">Score</span>
                        								<h5>24 / <small>24</small></h5>
                        							</div>
                        							<div class="col-md-6">
                        								<span class="text-geay">Speed</span>
                        								<h5>30 <small>qr/hr</small></h5>
                        							</div>
                        							<div class="col-md-6">
                        								<span class="text-geay">Accuracy</span>
                        								<h5>68 <small>%</small></h5>
                        							</div>
                        					</div>
                        			</div>
                        	</div>
                        </div>
                    </div>
                    <div class="card shadow mb-4">
                          <div class="card-body m-0">
                          		<div class="font-weight-bold mb-4"><h4>Questions by difficulty</h4></div>
                              <h4 class="small font-weight-bold">Easy <span class="float-right">20%</span></h4>
                              <div class="progress mb-4">
                                  <div class="progress-bar  cm-progress-bar bg-danger" role="progressbar" style="width: 20%" aria-valuenow="20" aria-valuemin="0" aria-valuemax="100"><span class="mr-2">20%</span></div>
                              </div>
                              <h4 class="small font-weight-bold">Medium <span class="float-right">40%</span></h4>
                              <div class="progress mb-4">
                                  <div class="progress-bar  cm-progress-bar bg-warning" role="progressbar" style="width: 40%" aria-valuenow="40" aria-valuemin="0" aria-valuemax="100"><span class="mr-2">40%</span></div>
                              </div>
                              <h4 class="small font-weight-bold">Hard <span class="float-right">60%</span></h4>
                              <div class="progress mb-4">
                                  <div class="progress-bar cm-progress-bar" role="progressbar" style="width: 60%" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100"><span class="mr-2">60%</span></div>
                              </div>
                          </div>
                      </div>
                  </div>
									<!-- 
											col-1
									 -->
									<div class="col-xl-4 col-md-6 mb-4">
                    <div class="card border-left-info shadow py-2">
                        <div class="card-body p-1">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col-auto p-1 m-1 bg-info text-white">
                                            <i class="fa fa-cogs fa-3x text-gray-300"></i>
                                        </div>
                                        <div class="col">
				                                    <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Tasks
				                                    </div>
                                            <div class="progress progress-sm mr-2">
                                                <div class="progress-bar bg-info" role="progressbar" style="width: 50%" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100"></div>
                                            </div>
                                            <span style="font-size: 12px;" class="text-info font-weight-bold">03 <span class="text-geay">/ 21 Lorem Ipsum is simply</span> </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card border-left-info shadow py-2">
                        <div class="card-body p-1">
                        	<div class="row">
                        		<div class="col-md-7">
                        			<div class="row mt-4">
	                        			<div class="col-md-8 p-0">
	                        				<div id="chart6"></div>
	                        			</div>
	                        			<div class="col-md-4 p-0">
	                        					<button class="btn btn-danger btn-sm mt-4">Very Low</button>
	                        			</div>
	                        		</div>
                        		</div>
                        		<div class="col-md-5">
                        			<div id="chart5"></div>
                        		</div>
                        	</div>
                       	</div>
                    </div>
                    <div class="card border-left-info shadow">
                        <div class="card-body p-2" style="position:relative;">
                        	<div class="row">
                        		<div class="col-md-10 mt-1">
                        			<div class="progress mb-4">
                                  <div class="progress-bar  cm-progress-bar bg-warning" role="progressbar" style="width: 40%" aria-valuenow="40" aria-valuemin="0" aria-valuemax="100"></div>
                              </div>
                            </div>
                            <div class="col-md-2 p-0">
                              <small>5/12</small>
                            </div>
                            <div class="col-md-12 mb-3">
                            	<h5 class="font-weight-bold">Interpreting Graphs</h5>
                            	<span class="text-geay">Lorem Ipsum is simply dummy text </span>
                            </div>
                            <div class="col-md-12">
                            		<button class="btn btn-default text-uppercase"><span class="fa fa-tv"></span> 23 lessions</button>
                            		<button class="btn btn-warning text-uppercase"><span class="fa fa-star-o"></span> 72 xp</button>
                            		<button class="btn btn-info text-uppercase">View <span class="fa fa-arrow-right"></span></button>
                            </div>
                        	</div>
                        </div>
                    </div>
                    <div class="card border-left-info shadow">
                        <div class="card-body" style="position:relative;">
                        	<div class="row">
                        			<div class="col-md-12 font-weight-bold mb-2"><h4>Study status - <span class="text-warning">Average</span></h4></div>
                        			<div class="col-md-4 border-right p-2">
                        					<img src="https://picsum.photos/100">
                        			</div>
                        			<div class="col-md-8">
                        					<div class="row">
                        							<div class="col-md-6">
                        								<span class="text-geay">Attempted</span>
                        								<h5>2151 <small>qm</small></h5>
                        							</div>
                        							<div class="col-md-6">
                        								<span class="text-geay">Score</span>
                        								<h5>24 / <small>24</small></h5>
                        							</div>
                        							<div class="col-md-6">
                        								<span class="text-geay">Speed</span>
                        								<h5>30 <small>qr/hr</small></h5>
                        							</div>
                        							<div class="col-md-6">
                        								<span class="text-geay">Accuracy</span>
                        								<h5>68 <small>%</small></h5>
                        							</div>
                        					</div>
                        			</div>
                        	</div>
                        </div>
                    </div>
                    <div class="card shadow mb-4">
                          <div class="card-body m-0">
                          		<div class="font-weight-bold mb-4"><h4>Questions by difficulty</h4></div>
                              <h4 class="small font-weight-bold">Easy <span class="float-right">20%</span></h4>
                              <div class="progress mb-4">
                                  <div class="progress-bar  cm-progress-bar bg-danger" role="progressbar" style="width: 20%" aria-valuenow="20" aria-valuemin="0" aria-valuemax="100"><span class="mr-2">20%</span></div>
                              </div>
                              <h4 class="small font-weight-bold">Medium <span class="float-right">40%</span></h4>
                              <div class="progress mb-4">
                                  <div class="progress-bar  cm-progress-bar bg-warning" role="progressbar" style="width: 40%" aria-valuenow="40" aria-valuemin="0" aria-valuemax="100"><span class="mr-2">40%</span></div>
                              </div>
                              <h4 class="small font-weight-bold">Hard <span class="float-right">60%</span></h4>
                              <div class="progress mb-4">
                                  <div class="progress-bar cm-progress-bar" role="progressbar" style="width: 60%" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100"><span class="mr-2">60%</span></div>
                              </div>
                          </div>
                      </div>
                  </div>

								</div>
							</div>
						</div>
          </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
    <script>
      window.Promise ||
        document.write(
          '<script src="https://cdn.jsdelivr.net/npm/promise-polyfill@8/dist/polyfill.min.js"><\/script>'
        )
      window.Promise ||
        document.write(
          '<script src="https://cdn.jsdelivr.net/npm/eligrey-classlist-js-polyfill@1.2.20171210/classList.min.js"><\/script>'
        )
      window.Promise ||
        document.write(
          '<script src="https://cdn.jsdelivr.net/npm/findindex_polyfill_mdn"><\/script>'
        )
    </script>
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script type="text/javascript">
   	jQuery(function($) {


     

      var options = {
          series: [76],
          chart: {
          type: 'radialBar',
          offsetY: -10,
          sparkline: {
            enabled: true
          }
        },
        plotOptions: {
          radialBar: {
            startAngle: -160,
            endAngle: 160,
            track: {
              background: "#e7e7e7",
              strokeWidth: '97%',
              margin: 5, // margin is in pixels
              dropShadow: {
                enabled: true,
                top: 2,
                left: 0,
                color: '#999',
                opacity: 1,
                blur: 2
              }
            },
            dataLabels: {
              name: { offsetY: 18, color: "#A3A5AD", fontSize: "8px", fontWeight: 700, fontFamily: "'Muli', sans-serif" },
			        value: { offsetY: -18, color: "#4D4F5C", fontSize: "16px", fontWeight: 900, show: true, fontFamily: "'Muli', sans-serif" },
            }
          }
        },
        grid: {
          padding: {
            top: -10
          }
        },
        fill: {
          type: 'gradient',
          gradient: {
            shade: 'light',
            shadeIntensity: 0.4,
            inverseColors: false,
            opacityFrom: 1,
            opacityTo: 1,
            stops: [0, 50, 53, 91]
          },
        },
        labels: ['Your score'],
        };

        var chart = new ApexCharts(document.querySelector("#chart2"), options);
        chart.render();

        var chart = new ApexCharts(document.querySelector("#chart4"), options);
        chart.render();

        var chart = new ApexCharts(document.querySelector("#chart6"), options);
        chart.render();

        

      var options = {
			    chart: { height: 150, type: "radialBar" },
			    series: [67],
			    colors: ["#6DD4B1"],
			    plotOptions: {
			        radialBar: {
			            hollow: { margin: 0, size: "55%" },
			            track: { dropShadow: { enabled: false, top: 0, left: 0, opacity: 0.15 } },
			            style: { fontSize: "14px", fontFamily: "'Muli', sans-serif", fontWeight: "700", colors: "#000" },
			            dataLabels: {
			                name: { offsetY: 18, color: "#A3A5AD", fontSize: "8px", fontWeight: 700, fontFamily: "'Muli', sans-serif" },
			                value: { offsetY: -18, color: "#4D4F5C", fontSize: "16px", fontWeight: 900, show: true, fontFamily: "'Muli', sans-serif" },
			            },
			        },
			    },
			    fill: { type: "gradient", gradient: { shade: "dark", type: "vertical", gradientToColors: ["#4D71EC"], stops: [0, 100] } },
			    stroke: { lineCap: "round" },
			    labels: ["Your score"],
			};

        /*var chart = new ApexCharts(document.querySelector("#chart"), options);
        chart.render();*/

        var chart = new ApexCharts(document.querySelector("#chart3"), options);
        chart.render();

        var chart = new ApexCharts(document.querySelector("#chart5"), options);
        chart.render();

        var options = {
          series: [44, 55, 41, 17, 15],
          chart: {
          width: 150,
          type: 'donut',
          offsetY: 12,
          offsetX: -20,
        },
         stroke: {
          width: 0,
        },
        plotOptions: {
          pie: {
            startAngle: -90,
            endAngle: 270,
          }
        },
        dataLabels: {
          enabled: true,
        },
        fill: {
          type: 'gradient',
        },
        
        legend: {
            show: false,
          /*formatter: function(val, opts) {
            return val + " - " + opts.w.globals.series[opts.seriesIndex]
          }*/
        },
        title: {
         // text: '27/57'
        },
        responsive: [{
          breakpoint: 480,
          options: {
            chart: {
              width: 200
            },
            /*legend: {
              position: 'bottom'
            }*/
          }
        }]
        };

        var chart = new ApexCharts(document.querySelector("#chart"), options);
        chart.render();
      
      

         });

    </script>
    <style type="text/css">
        

.apexcharts-canvas {
    margin: 0 auto;
}
</style>
@endsection