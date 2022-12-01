@extends('backend.layouts.app')
@section('content')
@php
$permissions = [];
$user_id = auth()->user()->id;
if($user_id){
	$module_permission = App\Helpers\Helper::getPermissions($user_id);
	if($module_permission && !empty($module_permission)){
		$permissions = $module_permission;
	}
}else{
	$permissions = [];
}
@endphp
<div class="wrapper d-flex align-items-stretch sm-deskbord-main-sec">
	@include('backend.layouts.sidebar')
	<div id="content" class="pl-2 pb-5">
		@include('backend.layouts.header')
		<div class="sm-right-detail-sec pl-5 pr-5">
			<div class="container-fluid">
				<div class="row">
					<div class="col-md-12">
						<div class="col-md-12 col-lg-12 col-sm-12 sec-title student-test-list-cls">
							<h2 class="mb-2 main-title">{{__('languages.testing_zone')}}</h2>							
						</div>
						<div class="col-md-12 col-lg-12 col-sm-12 test-color-info" style="display:none;">
							<div class="exercise-clr">
								<div class="first-clr"></div>
								<p>{{__('languages.my_studies.exercise')}}</p>
							</div>
							<div class="test-exam-clr">
								<div class="second-clr"></div>
								<p>{{__('languages.my_studies.test')}}</p>
							</div>
						</div>
						<div class="sec-title back-button-margin">
							<a href="javascript:void(0);" class="btn-back" id="backButton">{{__('languages.back')}}</a>
						</div>
						<hr class="blue-line">
					</div>
				</div>
				@if (session('error'))
				<div class="alert alert-danger">{{ session('error') }}</div>
				@endif
				@if(session()->has('success_msg'))
				<div class="alert alert-success">
					{{ session()->get('success_msg') }}
				</div>
				@endif
				@if(session()->has('error_msg'))
				<div class="alert alert-danger">
					{{ session()->get('error_msg') }}
				</div>
				@endif

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
				
				<div class="row question_difficulty_level_colors">
					<div class="question_difficulty_level_colors_sec">
						<strong>{{__('languages.question_difficulty_levels')}}:</strong>
					</div>
					@if(!empty($difficultyLevels))
						@foreach($difficultyLevels as $difficultLevel)
						<div class="question_difficulty_level_colors_sec">
							<span class="dot-color" style="background-color: {{$difficultLevel->difficulty_level_color}};border-radius: 50%;display: inline-block;"></span>
							<label>{{$difficultLevel->{'difficulty_level_name_'.app()->getLocale()} }}</label>
						</div>
						@endforeach
					@endif
				</div>
				
				<!-- new structure -->
	            {{-- <div class="row study-learning-tab">
					<div class="col-lg-12 col-md-12 col-sm-12">
						<div class="study-learning-inner">
							<div class="col-lg-9 col-md-9 col-sm-12">
								<div class="tab-study study-exercise">
									<a href="#exercise" class="test-tab" id="tab-exercise" data-id="exercise">{{__('languages.exercise')}}</a>
								</div>
								<div class="tab-study study-test">
									<a href="#test" class="test-tab" id="tab-test" data-id="test">{{__('languages.test_text')}}</a>
								</div>
							</div>
							<div class="col-lg-3 col-md-3 col-sm-12">
								<div class="study-setting">
									<!-- <a href="#" class="setting-button" id="my-study-config-btn"><i class="fa fa-cogs"></i></a> -->
								</div>
							</div>
						</div>
					</div>
				</div> --}}
				<div class="row main-my-study">
	               	<div class="col-lg-12 col-md-12 col-sm-12">
	                  	<div id="mystudytable" class="my-study-table">
	                     	<div class="tab-content">
								{{-- Start For The Self Learning Test Type List --}}
								<div role="tabpanel" id="test">
									<table id="test-table">
										<thead>
											<tr>
												<th class="selec-opt"><span>{{__('languages.publish_date_time')}}</span></th>
												<th>{{__('languages.reference_number')}}</th>
												<th>{{__('languages.report.accuracy')}}</th>
												<th>{{__('languages.study_status')}}</th>
												<th>{{__('languages.question_difficulties')}}</th>
												<th>{{__('languages.action')}}</th>
											</tr>
										</thead>
										<tbody class="scroll-pane">
											@if(isset($ExamsData['test_list']) && !empty($ExamsData['test_list']))
												@foreach($ExamsData['test_list'] as $selfLearningTest)
													@php $examArray = $selfLearningTest->toArray(); @endphp
													<tr>
														<td>{{ date('d/m/Y H:i:s',strtotime($selfLearningTest->created_at)) }}</td>
														<td>{{$selfLearningTest->reference_no}}</td>
														@if(isset($examArray['attempt_exams']) && in_array(Auth::id(),array_column($examArray['attempt_exams'],'student_id')))
															@php
															$accuracy = App\Helpers\Helper::getAccuracy($selfLearningTest->id, Auth::id());
															$ability = $examArray['attempt_exams'][0]['student_ability'] ?? 0;
															$accuracy_type  = App\Helpers\Helper::getAbilityType($ability);
															$abilityPr = App\Helpers\Helper::getNormalizedAbility($ability);
															@endphp
															<td>
																@php
																$total_correct_answers = $examArray['attempt_exams'][0]['total_correct_answers'];
																$question_id_size = $examArray['question_ids'];
																if($question_id_size != ""){
																	$question_id_size = sizeof(explode(',',$question_id_size));
																}
																echo '<div class="progress"><div class="progress-bar" role="progressbar" data-toggle="tooltip" data-placement="top" title="'.$accuracy.'% ('.$total_correct_answers.'/'.$question_id_size.')" style="width: '.$accuracy.'%;display: -webkit-box !important;display: -ms-flexbox !important;display: flex !important;" aria-valuenow="'.$accuracy.'" aria-valuemin="0" aria-valuemax="100">'.$accuracy.'%</div></div>';
																@endphp
															</td>
															<td align="center">
																<span class="dot-color" data-toggle="tooltip" data-placement="top"  title="{{round($ability,2)}} ({{$abilityPr}}%)" style="background-color: {{App\Helpers\Helper::getGlobalConfiguration($accuracy_type)}};border-radius: 50%;display: inline-block;"></span>
															</td>
														@else
															<td align="center">-----</td>
															<td align="center">-----</td>
														@endif
														<td>
															@php
															$progressQuestions = App\Helpers\Helper::getQuestionDifficultiesLevelPercent($selfLearningTest->id,Auth::id());
															@endphp
															<div class="progress" style="height: 1rem">
																@php
																if($progressQuestions['Level1'] !=0) {
																	echo '<div class="progress-bar" data-toggle="tooltip" data-placement="top" title="'.$progressQuestions['Level1'].'%" style="width:'.$progressQuestions['Level1'].'%;background-color: '.$progressQuestions['Level1_color'].';">'.$progressQuestions['Level1'].'%'.'</div>';
																}
																if($progressQuestions['Level2'] !=0) {
																	echo '<div class="progress-bar" data-toggle="tooltip" data-placement="top" title="'.$progressQuestions['Level2'].'%" style="width:'.$progressQuestions['Level2'].'%;background-color: '.$progressQuestions['Level2_color'].';">'.$progressQuestions['Level2'].'%'.'</div>';																
																}
																if($progressQuestions['Level3'] !=0) {
																	echo '<div class="progress-bar" data-toggle="tooltip" data-placement="top" title="'.$progressQuestions['Level3'].'%" style="width:'.$progressQuestions['Level3'].'%;background-color: '.$progressQuestions['Level3_color'].';">'.$progressQuestions['Level3'].'%'.'</div>';																
																}
																if($progressQuestions['Level4'] !=0) {
																	echo '<div class="progress-bar" data-toggle="tooltip" data-placement="top" title="'.$progressQuestions['Level4'].'%" style="width:'.$progressQuestions['Level4'].'%;background-color: '.$progressQuestions['Level4_color'].';">'.$progressQuestions['Level4'].'%'.'</div>';																
																}
																if($progressQuestions['Level5'] !=0) {
																	echo '<div class="progress-bar" data-toggle="tooltip" data-placement="top" title="'.$progressQuestions['Level5'].'%" style="width:'.$progressQuestions['Level5'].'%;background-color: '.$progressQuestions['Level5_color'].';">'.$progressQuestions['Level5'].'%'.'</div>';																
																}
																@endphp
															</div>
														</td>
														<td class="btn-edit">
															@if(in_array('attempt_exam_update', $permissions))
																@if(!isset($examArray['attempt_exams']) || (isset($examArray['attempt_exams']) && !in_array(Auth::id(),array_column($examArray['attempt_exams'],'student_id'))) && $selfLearningTest->status == 'publish' && (App\Helpers\Helper::CheckExamStudentMapping($selfLearningTest->id) == false))
																<a href="{{ route('studentAttemptExam', $selfLearningTest->id) }}" class="" title="{{__('languages.test_text')}}">
																	<i class="fa fa-book" aria-hidden="true"></i>
																</a>
																@endif
															@endif

															@if(in_array('result_management_read', $permissions))
																@if((isset($examArray['attempt_exams']) && in_array(Auth::id(),array_column($examArray['attempt_exams'],'student_id'))) && ($examArray['status'] == "publish") && date('Y-m-d',strtotime($examArray['result_date'])) <= date('Y-m-d'))
																<a href="{{route('exams.result',['examid' => $selfLearningTest->id, 'studentid' => Auth::user()->id])}}" class="view-result-btn" title={{__('languages.result_text')}}>
																	<i class="fa fa-eye" aria-hidden="true" ></i>
																</a>
																@endif
															@endif

															@if((isset($examArray['attempt_exams']) && in_array(Auth::id(),array_column($examArray['attempt_exams'],'student_id'))) && ($examArray['status'] == "publish") && date('Y-m-d',strtotime($examArray['result_date'])) <= date('Y-m-d'))
																{{-- Test Difficulty Analysis Link --}}
																<a href="javascript:void(0);" title="{{__('languages.difficulty_analysis')}}" class="getTestDifficultyAnalysisReport" data-examid="{{$selfLearningTest->id}}">
																	<i class="fa fa-bar-chart" aria-hidden="true"></i>
																</a>
															@endif
															<a href="javascript:void(0);" class="exam_info ml-2" data-examid="{{$selfLearningTest->id}}" title="{{__('languages.config')}}"><i class="fa fa-gear" aria-hidden="true"></i></a>
														</td>
													</tr>
												@endforeach
											@endif
										</tbody>
									</table>
									<div class="row pt-2">
										<div class="col-md-3 col-lg-6 col-sm-2">
											<!-- <button type="button" class="btn btn-success" id="student_create_self_learning_test" data-self_learning_type="2">{{__('languages.create_self_learning_test')}}</button> -->
											<a href="{{route('student.create.self-learning-test')}}">
												<button type="button" class="btn btn-success">{{__('languages.create_self_learning_test')}}</button>
											</a>
										</div>
									</div>
								</div>
								{{-- End For The Self Learning Test Type List --}}
							</div>
	                  	</div>
	               	</div>
	            </div>
         </div>
      </div>
   </div>
</div>

<!-- Modal -->
<div class="modal fade" id="student-exam-result" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title" id="myModalLabel">{{__('languages.my_studies.test_result')}}</h4>
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
			</div>
			<div class="modal-body">{{__('languages.my_studies.in_this_section_will_be_displayed_test_result')}}</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">{{__('languages.close')}}</button>
			</div>
		</div>
	</div>
</div>

<!-- My study Configuration Popup -->
<!-- <div class="modal fade" id="my-study-config" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<form method="post">
				<div class="modal-header">
					<h4 class="modal-title w-100">{{__('languages.my_study_configuration')}}</h4>
					<button type="submit" class="btn btn-primary float-right">{{__('languages.submit')}}</button>
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				</div>
				<div class="modal-body">
					@csrf
					<div class="row m-0">
						<div class="col-md-12 pl-4">
							<input type="checkbox" id="AllTabs"> <label class="ml-1">{{__('languages.all')}}</label>
						</div>
						<div class="col-md-12 categories-main-list">
							@if(!empty($studyFocusTreeOption))
							{!! $studyFocusTreeOption !!}
							@endif
						</div>
						<div class="col-md-4 categories-progess-list" style="display: none;">
							@if(!empty($studyFocusTreeOption))
							{!! $studyFocusTreeOption !!}
							@endif
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">{{__('languages.close')}}</button>
					<button type="submit" class="btn btn-primary">{{__('languages.submit')}}</button>
				</div>
			</div>
		</form>
	</div>
</div> -->
<!-- My study Configuration Popup -->

<!-- Play Video Popup -->
<div class="modal fade" id="videoModal" tabindex="-1"  data-keyboard="false" aria-labelledby="videoModalLabel"  aria-hidden="true" data-backdrop="static">
	<div class="modal-dialog modal-dialog-centered">
		<div class="modal-content">
			<div class="modal-body embed-responsive embed-responsive-16by9">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close" style="position: absolute;top: 0;right: 0;background-color: white;height: 30px;width: 30px;z-index: 9;opacity: 1;border-radius: 50%;padding-bottom: 4px;">
					<span aria-hidden="true">&times;</span>
				</button>
				<iframe class="embed-responsive-item" src="" id="videoDis" frameborder="0" allowtransparency="true" allowfullscreen ></iframe>
			</div>
		</div>
	</div>
</div>

<!-- Start Play Video Popup -->
<div class="modal fade" id="imgModal" tabindex="-1" aria-labelledby="imgModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered">
		<div class="modal-content">
			<div class="modal-body">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close" style="position: absolute;top: 0;right: 0;background-color: white;height: 30px;width: 30px;z-index: 9;opacity: 1;border-radius: 50%;padding-bottom: 4px;">
					<span aria-hidden="true">&times;</span>
				</button>
				<img id="imgDis"  style="width: 100%;height: 100%;" src="">
			</div>
		</div>
	</div>
</div>
<!-- End Play Video Popup -->

<!-- Start Student create self learning test Popup -->
<div class="modal" id="studentCreateSelfLearningTestModal" tabindex="-1" aria-labelledby="studentCreateSelfLearningTestModal" aria-hidden="true" data-backdrop="static">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<form class="student-generate-test-form" method="get" id="student-generate-test-form">
				<div class="modal-header">
					<h4 class="modal-title w-100">{{__('languages.generate_self_learning')}} {{__('languages.test_text')}}</h4>
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				</div>
				<div class="modal-body">
					<input type="hidden" name="grade_id" value="{{ Auth::user()->grade_id }}" id="grade-id">
					<!-- <input type="hidden" name="subject_id" value="1" id="subject-id"> -->
					<input type="hidden" name="question_ids" value="" id="question-ids">
					<input type="hidden" name="self_learning_test_type" value="" id="self_learning_test_type">
					<div class="form-row">
						<div class="form-group col-md-6 mb-50">
							<label>{{__('languages.upload_document.strands')}}</label>
							<select name="strand_id[]" class="form-control select-option" id="strand_id" multiple>
								@if(isset($strandsList) && !empty($strandsList))
									@foreach ($strandsList as $strandKey => $strand)
										<option value="{{ $strand->id }}" <?php if($strandKey == 0){echo 'selected';}?>>{{ $strand->{'name_'.app()->getLocale()} }}</option>
									@endforeach
								@else
									<option value="">{{__('languages.no_strands_available')}}</option>
								@endif
							</select>
						</div>
						<div class="form-group col-md-6 mb-50">
							<label>{{__('languages.upload_document.learning_units')}}</label>
							<select name="learning_unit_id[]" class="form-control select-option" id="learning_unit" multiple>
								@if(isset($LearningUnits) && !empty($LearningUnits))
									@foreach ($LearningUnits as $learningUnitKey => $learningUnit)
										<option value="{{ $learningUnit->id }}" selected>{{ $learningUnit->{'name_'.app()->getLocale()} }}</option>
									@endforeach
								@else
									<option value="">{{__('languages.no_learning_units_available')}}</option>
								@endif
							</select>
						</div>
						<div class="form-group col-md-6 mb-50">
							<label>{{__('languages.upload_document.learning_objectives')}}</label>
							<select name="learning_objectives_id[]" class="form-control select-option" id="learning_objectives" multiple>
								@if(isset($LearningObjectives) && !empty($LearningObjectives))
									@foreach ($LearningObjectives as $learningObjectivesKey => $learningObjectives)
										<option value="{{ $learningObjectives->id }}" selected>{{ $learningObjectives->foci_number }} {{ $learningObjectives->{'title_'.app()->getLocale()} }}</option>
									@endforeach
								@else
									<option value="">{{__('languages.no_learning_objectives_available')}}</option>
								@endif
							</select>
						</div>
						<div class="form-group col-md-6 mb-50">
							<label>{{__('languages.difficulty_mode')}}</label>
							<select name="difficulty_mode" class="form-control select-option" id="difficulty_mode">
								<!-- <option value="manual">{{__('languages.manual')}}</option> -->
								<option value="auto" selected>{{__('languages.auto')}}</option>
							</select>
						</div>
						{{-- <div class="form-group col-md-6 mb-50">
							<label>{{__('languages.questions.difficulty_level')}}</label>
							<select name="difficulty_lvl[]" class="form-control select-option" id="difficulty_lvl" multiple>
								@if(!empty($difficultyLevels))
								@foreach($difficultyLevels as $difficultyLevel)
								<option value="{{$difficultyLevel->difficulty_level}}">{{$difficultyLevel->{'difficulty_level_name'.'_'.app()->getLocale()} }}</option>
								@endforeach
								@endif								
							</select>
							<span name="err_difficulty_level"></span>
						</div> --}}
						<div class="form-group col-md-6 mb-50">
							<label>{{__('languages.no_of_question')}}</label>
							<input type="text" class="form-control" id="no_of_questions" name="no_of_questions" onkeyup="getTestTimeDuration()" value="" placeholder="{{__('languages.no_of_question')}}">
						</div>
						<div class="form-group col-md-6 mb-50 test_time_duration_section" style=display:none;>
							<label>{{__('languages.test_time_duration')}} ({{__('languages.hh_mm_ss')}})</label>
							<input type="text" class="form-control mask time" id="test_time_duration" name="test_time_duration" value="" placeholder="{{__('languages.hh_mm_ss')}}">
							<span></span>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-primary" id="generate_test">{{__('languages.submit')}}</button>
					<button type="button" class="btn btn-default" data-dismiss="modal">{{__('languages.close')}}</button>
				</div>
			</form>
		</div>
	</div>
</div>
<!-- End Student create self learning test Popup -->

<!-- Start list of difficulties of the questions in the test Analysis Popup -->
<div class="modal" id="test-difficulty-analysis-report" tabindex="-1" aria-labelledby="test-difficulty-analysis-report" aria-hidden="true" data-backdrop="static">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<form method="post">
				<div class="modal-header">
					<h4 class="modal-title w-100">{{__('languages.question_difficulty_analysis')}}</h4>
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				</div>
				<div class="modal-body">
					<img src="" id="test-difficulty-analysis-report-image" class="img-fluid">
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">{{__('languages.close')}}</button>
				</div>
			</form>
		</div>
	</div>
</div>
<!-- End list of difficulties of the questions in the test Analysis Popup -->

@include('backend.layouts.footer')
<script>
/**
* USE : Get time duration for test created by student
**/
function getTestTimeDuration(){
	if($('#no_of_questions').val()){
		$.ajax({
			url: BASE_URL + '/test/getTimeDuration',
			type: 'GET',
			data: {				
				'no_of_questions': $('#no_of_questions').val()
			},
			success: function(response){
				var responseData = JSON.parse(JSON.stringify(response));
				if(responseData){
					if(responseData.data){
						$('#test_time_duration').val(responseData.data);
					}
				}
			}
		});
	}else{
		$('#test_time_duration').val('');
	}
	
}

function getYoutubeId(url) {
	const regExp = /^.*(youtu.be\/|v\/|u\/\w\/|embed\/|watch\?v=|&v=)([^#&?]*).*/;
	const match = url.match(regExp);
	return (match && match[2].length === 11) ? match[2] : null;
}

$(function() {
	var dataFrom=$("#studentCreateSelfLearningTestModal #student-generate-test-form").serializeArray();

	/**
	 * USE : Student can create own self-learning test
	*/
	$(document).on('click', '#student_create_self_learning_test, #student_create_self_learning_excercise', function(e) {
		$selfLearningType = $(this).attr('data-self_learning_type');
		$('#studentCreateSelfLearningTestModal').modal('show');
		if($selfLearningType=='2'){
			$('.test_time_duration_section').show();
		}else{
			$('.test_time_duration_section').hide();
		}
		$('#self_learning_test_type').val($selfLearningType);
		$("#studentCreateSelfLearningTestModal #generate_test").show();
		var jsn = {};
	    $.each(dataFrom, function() {
	        if (jsn[this.name]) {
	            if (!jsn[this.name].push) {
	                jsn[this.name] = [jsn[this.name]];
	            }
	            jsn[this.name].push(this.value || '');
	        } else {
	            jsn[this.name] = this.value || '';
	        }
	    });
	    $("#studentCreateSelfLearningTestModal #student-generate-test-form input,select").prop('disabled',false);
	    $.each(jsn, function(key, value) {
	    	if(key != 'self_learning_test_type'){
		    	if(key.indexOf("[]") != -1){
			    	$("#studentCreateSelfLearningTestModal *[name='"+key+"']").val(value);
			    	var idData = $("#studentCreateSelfLearningTestModal *[name='"+key+"']").attr("id");
			    	$("#studentCreateSelfLearningTestModal #"+idData).multiselect("rebuild");
		    	}else{
					if(key.indexOf("[]") != -1){
		    			$("#studentCreateSelfLearningTestModal *[name='"+key+"']").val(value);
		    		}else{
		    			$("#studentCreateSelfLearningTestModal *[name="+key+"]").val(value);	
		    		}
		    	}
		    }
	    });
	});

	/**
     * USE : Hide and show some option based on change difficulty mode
     */
    $(document).on('change', '#difficulty_mode', function(e) {
        if($(this).val() == 'manual'){
            // if manual mode then hide selection for difficulty level option
            $('#difficulty_lvl').multiselect('enable');
        }else{
            // if auto mode then hide selection for difficulty level option
            $("#difficulty_lvl").val('').multiselect("rebuild").multiselect('disable');            
        }
    });

	
	/**
	 * USE : Get Learning Units from multiple strands
	 * **/
	$(document).on('change', '#strand_id', function() {
		$strandIds = $('#strand_id').val();
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
								option.attr('value', this.id).text(this["name_"+APP_LANGUAGE]);
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

	/**
	 * USE : Get Multiple Learning units based on multiple learning units ids
	 * **/
	$(document).on('change', '#learning_unit', function() {
		$strandIds = $('#strand_id').val();
		$learningUnitIds = $('#learning_unit').val();
		if($learningUnitIds != ""){
			$.ajax({
				url: BASE_URL + '/getLearningObjectivesFromMultipleLearningUnits',
				type: 'POST',
				data: {
					'_token': $('meta[name="csrf-token"]').attr('content'),
					'grade_id': $('#grade-id').val(),
					'subject_id': $('#subject-id').val(),
					'strand_id': $strandIds,
					'learning_unit_id': $learningUnitIds
				},
				success: function(response) {
					$('#learning_objectives').html('');
					$("#cover-spin").hide();
					var data = JSON.parse(JSON.stringify(response));
					if(data){
						if(data.data){
							$(data.data).each(function() {
								var option = $('<option />');
								option.attr('value', this.id).attr('selected','selected').text(this.foci_number + ' ' + this.title);
								$('#learning_objectives').append(option);
							});
						}else{
							$('#learning_objectives').html('<option value="">'+LEARNING_OBJECTIVES_NOT_AVAILABLE+'</option>');
						}
					}else{
						$('#learning_objectives').html('<option value="">'+LEARNING_OBJECTIVES_NOT_AVAILABLE+'</option>');
					}
					$('#learning_objectives').multiselect("rebuild");
				},
				error: function(response) {
					ErrorHandlingMessage(response);
				}
			});
		}else{
			$('#learning_objectives').html('');
			$('#learning_objectives').multiselect("rebuild");
		}
	});
	
	/**
	 *  USE : Check form validation for create student self-learning test/Excercise
	  */
	  $("#student-generate-test-form").validate({
		ignore: [],
		rules: {
			title:{
				required: true,
			},
			strand_id:{
				required: true,
			},
			learning_unit_id: {
				required: true,
			},
			no_of_questions:{
				required:true,
				number:true,
				valueMustGreterThanZero:true,
			},
			'difficulty_lvl[]':{
				required :function(element){
					if($('#difficulty_mode').value == 'auto'){
						return false;
					}else{
						return true;
					}
				}
			},
			test_time_duration :{
				required :function(element){
					($('#self_learning_test_type').value ==2) ? true : false;
				}
			}
		},
		messages: {
			title:{
				required: VALIDATIONS.PLEASE_ENTER_TITLE,
			},
			strand_id:{
				required: VALIDATIONS.PLEASE_SELECT_STRAND,
			},
			learning_unit_id: {
				required: VALIDATIONS.PLEASE_SELECT_LEARNING_UNIT,
			},
			learning_objectives_id:{
				required: VALIDATIONS.PLEASE_SELECT_LEARNING_OBJECTIVES,
			},
			no_of_questions:{
				required:VALIDATIONS.PLEASE_ENTER_NO_OF_QUESTIONS,
				number:VALIDATIONS.PLEASE_ENTER_ONLY_NUMERIC_VALUE,
				valueMustGreterThanZero : VALIDATIONS.PLEASE_ENTER_THAN_ZERO_VALUE
			},
			'difficulty_lvl[]':{
				required:VALIDATIONS.PLEASE_SELECT_DIFFICULTY_LEVEL,
			},
			test_time_duration :{
				required : VALIDATIONS.PLEASE_ENTER_TIME_DURATION,
			}
		},
		errorPlacement: function(error, element) {
			if (element.attr("name") == "difficulty_lvl") {
				error.insertAfter('#err_difficulty_level');
			}else {
				error.insertAfter(element);
			}
		}
	});

	/**
	 * USE : Trigger on click student can create own self-learning test
	  */
	  $(document).on('click', '#generate_test', function() {
		if($("#student-generate-test-form").valid()){ // Check form is valid or not
			$("#cover-spin").show();
			$.ajax({
				url: BASE_URL + '/getQuestionsStudentSelfLearningTest',
				type: 'get',
				data: $('#student-generate-test-form').serialize(),
				success: function(response) {
					var data = JSON.parse(JSON.stringify(response));
					$("#cover-spin").hide();
					if (data.status === 'success') {
						window.location.replace(BASE_URL+'/'+data.data.redirectUrl);
					}else {
						toastr.error(data.message);
					}
				},
				error: function(response){
					ErrorHandlingMessage(response);
				}
			});	
		}
	});
		
	// $(document).on('click', '.test-tab', function() {
	// 	$('.test-tab').removeClass('active');
	// 	$('.tab-pane').removeClass('active');
	// 	$('#'+$(this).attr('data-id')).addClass('active');
	// 	$(this).addClass('active');
	// 	$('#documentbtn form .active_tab').val($(this).attr('data-id'));
	// 	$.cookie("PreviousTab", $(this).attr('data-id'));
	// });

	$(document).on('click', '.video-img-sec', function() {
		var videoSrc = $(this).data( "src" );
		var domain = videoSrc.replace('http://','').replace('https://','').split(/[/?#]/)[0];
		if (videoSrc.indexOf("youtube") != -1) {
			const videoId = getYoutubeId(videoSrc);
			$("#videoDis").attr('src','//www.youtube.com/embed/'+videoId);
		}else if (videoSrc.indexOf("vimeo") != -1) {
			const videoId = getYoutubeId(videoSrc);
			var matches = videoSrc.match(/vimeo.com\/(\d+)/);
			$("#videoDis").attr('src','https://player.vimeo.com/video/'+matches[1]);
		}else if (videoSrc.indexOf("dailymotion") != -1) {
			var m = videoSrc.match(/^.+dailymotion.com\/(video|hub)\/([^_]+)[^#]*(#video=([^_&]+))?/);
			if (m !== null) {
				if(m[4] !== undefined) {
					$("#videoDis").attr('src','https://geo.dailymotion.com/player/x5poh.html?video='+m[4]);
				}
				$("#videoDis").attr('src','https://geo.dailymotion.com/player/x5poh.html?video='+m[2]);
			}
		}else{
			$("#videoDis").attr('src',videoSrc);
		}
	});
	
	$(document).on('click', '.document-img-view', function() {
		var imgSrc = $(this).data( "src" );
		$("#imgDis").attr('src',imgSrc);
	});

	// This click event to display exam details
	$(document).on('click', '.exam_info', function() {
		$("#cover-spin").show();
		var examid=$(this).attr('data-examid');
		$.ajax({
			url: BASE_URL + '/get-exam-info/'+examid,
			type: 'GET',
			success: function(response) {
				var data_id = $(".study-learning-tab .test-tab.active").attr('data-id');
				if(data_id == 'test'){
					$('.test_time_duration_section').show();
				}else{
					$('.test_time_duration_section').hide();
				}
				
				if(response.data.length!=0){
					var strand_ids=response.data.strand_ids;
					$("#studentCreateSelfLearningTestModal #student-generate-test-form input,select").prop('disabled',true);
					if(strand_ids != ""){
						strand_ids = strand_ids.split(',');
						$("#studentCreateSelfLearningTestModal #strand_id").val(strand_ids);
						var strand_id = $("#studentCreateSelfLearningTestModal #strand_id").multiselect("rebuild");
					}
					var learning_units = response.data.learning_unit_ids;
					if(learning_units != ""){
						learning_units = learning_units.split(',');
						$("#studentCreateSelfLearningTestModal #learning_unit").val(learning_units);
						$("#studentCreateSelfLearningTestModal #learning_unit").multiselect("rebuild");
					}
					var learning_objectives = response.data.learning_objectives_ids;
					if(learning_objectives != ""){
						learning_objectives = learning_objectives.split(',');
						$("#studentCreateSelfLearningTestModal #learning_objectives").val(learning_objectives);
						$("#studentCreateSelfLearningTestModal #learning_objectives").multiselect("rebuild");
					}
					var difficulty_levels = response.data.difficulty_mode;
					if(difficulty_levels != ""){
						difficulty_levels = difficulty_levels.split(',');
						$("#studentCreateSelfLearningTestModal #difficulty_lvl").val(difficulty_levels);
						$("#studentCreateSelfLearningTestModal #difficulty_lvl").multiselect("rebuild");
					}
					var difficulty_levels = response.data.difficulty_levels;
					if(difficulty_levels != ""){
						$("#studentCreateSelfLearningTestModal #difficulty_mode").val(difficulty_levels);
					}
					var no_of_questions = response.data.no_of_questions;
					if(no_of_questions != ""){
						$("#studentCreateSelfLearningTestModal #no_of_questions").val(no_of_questions);
					}

					var time_duration=response.data.time_duration;
					if(time_duration != ""){
						$("#studentCreateSelfLearningTestModal #test_time_duration").val(time_duration);
					}
					$("#studentCreateSelfLearningTestModal").modal('show');
				}else{
					toastr.error(VALIDATIONS.CONFIGURATIONS_DATA_NOT_FOUND);
				}
				$("#cover-spin").hide();
			},
			error: function(response) {
				ErrorHandlingMessage(response);
			}
		});
		$("#studentCreateSelfLearningTestModal #generate_test").hide();
	});

	// if($("#self_learning.tab-pane > table > tbody > tr").length!=0){
	// 	$(".tab-study.study-self-learn > a").click();
	// }else if($("#exercise.tab-pane > table > tbody > tr").length!=0){
	// 	$(".tab-study.study-exercise > a").click();
	// }else if($("#test.tab-pane > table > tbody > tr").length!=0){
	// 	$(".tab-study.study-test > a").click();
	// }

	/*
	This change display document in exam id
	*/
	// var listExamIdDoc = new Array();
	// $.each($(".main-my-study input[type=checkbox]"), function() {
	// 	if($(this).val()!='on'){
	// 		listExamIdDoc.push($(this).val());
	// 	}
	// });
	
	// if(listExamIdDoc.length!=0){
	// 	$.ajax({
	// 		url: BASE_URL + '/study-documents',
	// 		type: 'POST',
	// 		data : {
	// 			'_token': $('meta[name="csrf-token"]').attr('content'),
	// 			'list_exam_id' : listExamIdDoc
	// 		},
	// 		success: function(response) {
	// 			$(".sm-vedio-pdf-doc-sec").html(response);
	// 		}
	// 	});
	// }else{
	// 	$(".sm-vedio-pdf-doc-sec").html('<div class="sec-title"><h3>Document List</h3></div><p class="text-center">No Any Documents Are Available</p>');
	// }

	$("#videoModal .close").click(function () {
		$("#videoModal #videoDis").attr('src','');
	});

	// $.each($(".categories-main-list input[type=checkbox][name='strands[]']"), function() {
	// 	var listConfigIdList = new Array();
	// 	listConfigIdList.push($(this).val());
	// 	var MainData = $(this);
	// 	$.ajax({
	// 		url: BASE_URL + '/estimate_student_competence_web',
	// 		type: 'POST',
	// 		data : {
	// 			'_token': $('meta[name="csrf-token"]').attr('content'),
	// 			'list_strands_id' : listConfigIdList
	// 		},
	// 		success: function(response) {
	// 			if(response.data.length!=0){
	// 				var mainDataVal = MainData.val();
	// 				var mainDataName = MainData.attr('name');
	// 				if($(".categories-main-list input[type=checkbox][name='"+mainDataName+"'][value="+mainDataVal+"]").length != 0){
	// 					var classAdd = 'up-50';
	// 					if(response <= 49){
	// 						classAdd = 'down-50';
	// 					}
	// 					var labelData = $(".categories-main-list input[type=checkbox][name='"+mainDataName+"'][value="+mainDataVal+"]").parent();
	// 					labelData.find('.label-percentage:eq(0)').text(response.data[0]+'%').show();
	// 					labelData.find('input[type=range]:eq(0)').val(response.data[0]).attr('class',classAdd).show();
	// 				}
	// 				$("#cover-spin").hide();
	// 			}else{
	// 				var mainDataVal = MainData.val();
	// 				var mainDataName = MainData.attr('name');
	// 				if($(".categories-main-list input[type=checkbox][name='"+mainDataName+"'][value="+mainDataVal+"]").length!=0){
	// 					var responseData=0;
	// 					var classAdd='up-50';
	// 					if(responseData<=49){
	// 						classAdd='down-50';
	// 					}
	// 					var labelData=$(".categories-main-list input[type=checkbox][name='"+mainDataName+"'][value="+mainDataVal+"]").parent();
	// 					labelData.find('.label-percentage:eq(0)').text('N/A').show();
	// 					labelData.find('input[type=range]:eq(0)').val(responseData).attr('class',classAdd).hide();
	// 				}
	// 			}
	// 		}
	// 	});
	// });
	
	// $(document).on('click',".categories-main-list a.collapse-category", function() {
	// 	if($(this).hasClass('open')){
	// 		$(this).parent().find(' > ul > li > input[type=checkbox]').each(function(){
	// 			$("#cover-spin").show();
	// 			var var_data = new Array($(this).val());
	// 			var var_name = $(this).attr('name').replace('[]','');
	// 			var MainData = $(this);
	// 			$.ajax({
	// 				url: BASE_URL + '/estimate_student_competence_web',
	// 				type: 'POST',
	// 				data : {
	// 					'_token': $('meta[name="csrf-token"]').attr('content'),
	// 					[var_name]: var_data
	// 				},
	// 				success: function(response) {
	// 					if(response.data.length!=0){
	// 						var mainDataVal = MainData.val();
	// 						var mainDataName = MainData.attr('name');
	// 						if($(".categories-main-list input[type=checkbox][name='"+mainDataName+"'][value="+mainDataVal+"]").length != 0){
	// 							var classAdd = 'up-50';
	// 							if(response <= 49){
	// 								classAdd = 'down-50';
	// 							}
	// 							var labelData = $(".categories-main-list input[type=checkbox][name='"+mainDataName+"'][value="+mainDataVal+"]").parent();
	// 							labelData.find('.label-percentage:eq(0)').text(response.data[0]+'%').show();
	// 							labelData.find('input[type=range]:eq(0)').val(response.data[0]).attr('class',classAdd).show();
	// 						}
	// 					}else{
	// 						var mainDataVal = MainData.val();
	// 						var mainDataName = MainData.attr('name');
	// 						if($(".categories-main-list input[type=checkbox][name='"+mainDataName+"'][value="+mainDataVal+"]").length!=0){
	// 							var responseData = 0;
	// 							var classAdd='up-50';
	// 							if(responseData <= 49){
	// 								classAdd='down-50';
	// 							}
	// 							var labelData = $(".categories-main-list input[type=checkbox][name='"+mainDataName+"'][value="+mainDataVal+"]").parent();
	// 							labelData.find('.label-percentage:eq(0)').text('N/A').show();
	// 							labelData.find('input[type=range]:eq(0)').val(responseData).attr('class',classAdd).hide();
	// 						}
	// 					}
	// 					$("#cover-spin").hide();
	// 				}
	// 			});
	// 		});
	// 	}
	// });
	
	$(document).on('change', '#AllTabs', function() {
		if($(this).prop('checked')){
			$(".categories-main-list .categories-list input[type=checkbox]").prop('checked',true);
		}else{
			$(".categories-main-list .categories-list input[type=checkbox]").prop('checked',false);
		}
	});
	
	// $(document).on('change', ".categories-main-list .categories-list input[type=checkbox]", function() {
	// 	var allCheckLength = $(".categories-main-list .categories-list input[type=checkbox][name='strands[]']:checked").length;
	// 	var allUnCheckLength = $(".categories-main-list .categories-list input[type=checkbox][name='strands[]']").length;
	// 	if(allCheckLength == allUnCheckLength){
	// 		$('#AllTabs').prop('checked',true);
	// 	}else{
	// 		$('#AllTabs').prop('checked',false);
	// 	}
	// });
});

function getRandomNumber(){
	return Math.floor(Math.random() * 101);
}
</script>
<!-- <script type="text/javascript">
$.fn.cascadeCheckboxes = function() {
	$.fn.checkboxParent = function() {
		//to determine if checkbox has parent checkbox element
		var checkboxParent = $(this).parent("li").parent("ul").parent("li").find('> input[type="checkbox"]');
		return checkboxParent;
	};
	$.fn.checkboxChildren = function() {
		//to determine if checkbox has child checkbox element
		var checkboxChildren = $(this).parent("li").find('> .subcategories > li > input[type="checkbox"]');
		return checkboxChildren;
	};
	$.fn.cascadeUp = function() {
		var checkboxParent = $(this).checkboxParent();
		if ($(this).prop("checked")) {
			if (checkboxParent.length) {
				//check if all children of the parent are selected - if yes, select the parent
				//these will be the siblings of the element which we clicked on
				var children = $(checkboxParent).checkboxChildren();
				var booleanChildren = $.map(children, function(child, i) {
					return $(child).prop("checked");
				});
				//check if all children are checked
				var allChecked = booleanChildren.filter(function(x) {return !x})
				//if there are no false elements, parent is selected
				if (!allChecked.length) {
					$(checkboxParent).prop("checked", true);
					$(checkboxParent).cascadeUp();
				}
			}
		} else {
			if (checkboxParent.length) {
				//if parent is checked, becomes unchecked
				$(checkboxParent).prop("checked", false);
				$(checkboxParent).cascadeUp();
			}
		}
	};
	$.fn.cascadeDown = function() {
		var checkboxChildren = $(this).checkboxChildren();
		if (checkboxChildren.length) {
			checkboxChildren.prop("checked", $(this).prop("checked"));
			checkboxChildren.each(function(index) {
				$(this).cascadeDown();
			});
		}
	}
	$(this).cascadeUp();
	$(this).cascadeDown();
};

$("input[type=checkbox]:not(:disabled)").on("change", function() {
	$(this).cascadeCheckboxes();
});
$(".category a").on("click", function(e) {
	e.preventDefault();
	$(this).parent().find("> .subcategories").slideToggle(function() {
		if ($(this).is(":visible")) $(this).css("display", "flex");
	});
});
$('.collapse-category').on("click", function(){
	if($(this).hasClass('close')){
		$(this).removeClass('close');
		$(this).addClass('open');
	}else{
		$(this).removeClass('open');
		$(this).addClass('close');
	}
});
</script> -->
@endsection