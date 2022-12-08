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
					<div class="col-md-12"> {{--test-titles--}}
						<div class="col-md-12 col-lg-12 col-sm-12 sec-title student-test-list-cls">
							{{-- <h2 class="mb-2 main-title">{{__('languages.self_learning')}}</h2> --}}
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
					</div>
                    <div class="sec-title">
                        <a href="javascript:void(0);" class="btn-back" id="backButton">{{__('languages.back')}}</a>
                    </div>
				</div>
                <hr class="blue-line">
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
				
				<form class="displayStudentStudyForm" id="displayStudentStudyForm" method="GET">
					<input type="hidden" name="school_id" id="student_study_school_id" value="{{ $schoolId }}">
					<div class="row">
						<div class="col-lg-4 col-md-4">
							<div class="select-lng pb-2">
								<label for="users-list-role">{{ __('languages.user_management.grade') }}</label>
								<select class="form-control" data-show-subtext="true" data-live-search="true" name="grade_id[]" id="student_multiple_grade_id" multiple required >
									@if(!empty($gradesList))
									@foreach($gradesList as $grade)
									<option value="{{$grade->getClass->id}}" @if($grade_id){{ in_array($grade->getClass->id,$grade_id) ? 'selected' : '' }} @endif>{{ $grade->getClass->name}}</option>
									@endforeach
									@endif
								</select>
							</div>
						</div>
						<div class="col-lg-2 col-md-3">
                            <div class="select-lng pb-2">
                            	<label for="users-list-role">{{ __('languages.class') }}</label>
                                <select name="class_type_id[]" class="form-control" id="classType-select-option" multiple >
                                	@if(!empty($GradeClassListData))
										@foreach($GradeClassListData as $GradeClassId => $GradeClassValue)
										<option value="{{$GradeClassId}}" @if(!empty($class_type_id)) {{ in_array($GradeClassId,$class_type_id) ? 'selected' : '' }} @endif>{{ $GradeClassValue }}</option>
										@endforeach
									@endif
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-2 col-md-3">
							<div class="select-lng pt-4 pb-2">
								<button type="submit" name="filter" value="filter" class="btn-search mt-2">{{ __('languages.search') }}</button>
							</div>
						</div>
					</div>
				</form>
				<div class="row study_status_colors" >
					<div class="study_status_colors-sec">
						<strong>{{__('languages.study_status')}}:</strong>
					</div>
					<div class="study_status_colors-sec">
						<span class="dot-color" style="background-color: {{ App\Helpers\Helper::getGlobalConfiguration('struggling_color')}};border-radius: 50%;display: inline-block;"></span>
						<span>{{__('languages.struggling')}}</span>
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
                <div class="row">
                    <div class="col-md-12">
                        <div class="question-bank-sec">
                            <table id="DataTable" class="display" style="width:100%">
                                <thead>
                                    <tr>
                                        <th class="selec-opt"><span>{{__('languages.publish_date_time')}}</span></th>
                                        <th><span>{{__('languages.report.student_name')}}</span></th>
                                        <th>{{__('languages.reference_number')}}</th>
                                        <th>{{__('languages.grade')}} - {{__('languages.class')}}</th>
                                        <th>{{__('languages.progress')}}</th>
                                        <th>{{__('languages.report.accuracy')}}</th>
                                        <th>{{__('languages.study_status')}}</th>
                                        <th>{{__('languages.question_difficulties')}}</th>
                                        <th>{{__('languages.action')}}</th>
                                    </tr>
                                </thead>
                                <tbody class="scroll-pane">
                                     @if(!empty($SelfLearningTestList))
                                        @foreach($SelfLearningTestList as $selflearningTest)
                                        <tr>
                                            <td>{{ date('d/m/Y H:i:s',strtotime($selflearningTest->date_time)) }}</td>
                                            <td>{{ App\Helpers\Helper::decrypt($selflearningTest->user->{'name_'.app()->getLocale()}) }}</td>
                                            <td>{{$selflearningTest->exams->reference_no}}</td>
                                            <td>
                                                @if(!empty($selflearningTest->peerGroup)) 
                                                    {{ $selflearningTest->peerGroup->group_name }}
                                                @else 
                                                    {{ $selflearningTest->grade_with_class }}
                                                @endif
                                            </td>
                                            @php																
                                                $progress = json_decode($selflearningTest->student_progress, true);
                                                $accuracy = json_decode($selflearningTest->average_accuracy, true);
                                            @endphp
                                            <td>
                                                <div class="progress student-progress-report" data-examid="{{$selflearningTest->exam_id}}"  data-studentids="{{$selflearningTest->student_ids}}">
                                                    <div class="progress-bar" role="progressbar" data-toggle="tooltip" data-placement="top" title="{{$progress['progress_tooltip']}}"style="width:{{$progress['progress_percentage']}}%;display: -webkit-box !important;display: -ms-flexbox !important;display: flex !important;" aria-valuenow="{{$progress['progress_percentage']}}" aria-valuemin="0" aria-valuemax="100">{{$progress['progress_percentage']}}%</div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="progress">
                                                    <div class="progress-bar" role="progressbar" data-toggle="tooltip" data-placement="top" title="{{$accuracy['average_accuracy_tooltip']}}" style="width: {{$accuracy['average_accuracy']}}%;display: -webkit-box !important;display: -ms-flexbox !important;display: flex !important;" aria-valuenow="{{$accuracy['average_accuracy']}}" aria-valuemin="0" aria-valuemax="100">{{$accuracy['average_accuracy']}}%</div>
                                                </div>
                                            </td>
                                            
                                            @php
                                                if(!empty($selflearningTest->attempt_exams)){
                                                    $ability =  $selflearningTest->attempt_exams[0]->student_ability ?? 0;
                                                    $accuracy_type  = App\Helpers\Helper::getAbilityType($ability);
                                                    $abilityPr = App\Helpers\Helper::getNormalizedAbility($ability);
                                                }
                                            @endphp
                                            <td align="center">
                                                @if(!empty($accuracy_type))
                                                    <span class="dot-color" data-toggle="tooltip" data-placement="top"  title="{{round($ability,2)}} ({{$abilityPr}}%) "  style="border-radius: 50%;display: inline-block;position: relative;background-color: {{ App\Helpers\Helper::getGlobalConfiguration($accuracy_type)}};"></span>
                                                @else
                                                    -----
                                                @endif
                                            </td>
                                            
                                            @php
                                                $progressQuestions = App\Helpers\Helper::getQuestionDifficultiesLevelPercent($selflearningTest->exam_id, $selflearningTest->student_ids);
                                            @endphp
                                            <td>
                                                <div class="progress" style="height: 1rem">
                                                    @php
                                                    if($progressQuestions['Level1'] !=0) {
                                                        echo '<div class="progress-bar p-1" data-toggle="tooltip" data-placement="top" title="'.$progressQuestions['Level1'].'%" style="width:'.$progressQuestions['Level1'].'%;background-color: '.$progressQuestions['Level1_color'].';">'.$progressQuestions['Level1'].'%'.'</div>';																
                                                    }
                                                    if($progressQuestions['Level2'] !=0) {
                                                        echo '<div class="progress-bar p-1" data-toggle="tooltip" data-placement="top" title="'.$progressQuestions['Level2'].'%" style="width:'.$progressQuestions['Level2'].'%;background-color: '.$progressQuestions['Level2_color'].';">'.$progressQuestions['Level2'].'%'.'</div>';																
                                                    }
                                                    if($progressQuestions['Level3'] !=0) {
                                                        echo '<div class="progress-bar p-1" data-toggle="tooltip" data-placement="top" title="'.$progressQuestions['Level3'].'%" style="width:'.$progressQuestions['Level3'].'%;background-color: '.$progressQuestions['Level3_color'].';">'.$progressQuestions['Level3'].'%'.'</div>';																
                                                    }
                                                    if($progressQuestions['Level4'] !=0) {
                                                        echo '<div class="progress-bar p-1" data-toggle="tooltip" data-placement="top" title="'.$progressQuestions['Level4'].'%" style="width:'.$progressQuestions['Level4'].'%;background-color: '.$progressQuestions['Level4_color'].';">'.$progressQuestions['Level4'].'%'.'</div>';																
                                                    }
                                                    if($progressQuestions['Level5'] !=0) {
                                                        echo '<div class="progress-bar p-1" data-toggle="tooltip" data-placement="top" title="'.$progressQuestions['Level5'].'%" style="width:'.$progressQuestions['Level5'].'%;background-color: '.$progressQuestions['Level5_color'].';">'.$progressQuestions['Level5'].'%'.'</div>';																
                                                    }
                                                    @endphp
                                                </div>
                                            </td>
                                            <td class="btn-edit">
                                                <a href="{{ route('report.class-test-reports.correct-incorrect-answer', ['exam_id' => $selflearningTest->exam_id,'filter' => 'filter']) }}" title="{{__('languages.performance_report')}}"><i class="fa fa-bar-chart" aria-hidden="true"></i></a>
                                                {{-- <a href="javascript:void(0);" title="Class Ability Analysis" class="getClassAbilityAnalysisReport" data-examid="{{$selfLearningTest['id']}}" data-studentids="{{$selfLearningTest['student_ids']}}">
                                                    <i class="fa fa-bar-chart" aria-hidden="true"></i>
                                                </a> --}}
                                                <a href="javascript:void(0);" title="{{__('languages.difficulty_analysis')}}" class="getTestDifficultyAnalysisReport" data-examid="{{$selflearningTest->exam_id}}">
                                                    <i class="fa fa-bar-chart ml-2" aria-hidden="true"></i>
                                                </a>

                                                @php
                                                    if(isset($selflearningTest->grade_with_class) && !empty($selflearningTest->grade_with_class)){
                                                        $gradesClass=explode('-',$selflearningTest->grade_with_class);
                                                    }
                                                @endphp
                                                <a href="javascript:void(0);" class="exam_info ml-2" data-examid="{{$selflearningTest->exam_id}}" data-grade-id="{{ $gradesClass[0] }}" title="{{__('languages.config')}}"><i class="fa fa-gear" aria-hidden="true"></i></a>
                                                <a href="javascript:void(0);" class="exam_questions-info ml-2" data-examid="{{$selflearningTest->exam_id}}" title="{{__('languages.preview')}}"><i class="fa fa-book" aria-hidden="true"></i></a>
                                                <a href="javascript:void(0);" class="result_summary ml-2" data-examid="{{$selflearningTest->exam_id}}" data-studentids="{{$selflearningTest->student_ids}}" title="{{__('languages.result_summary')}}"><i class="fa fa-bar-chart" aria-hidden="true"></i></a>
                                            </td>
                                        </tr>
                                        @endforeach
                                    @else
                                    <tr><td>{{__('languages.no_data_found')}}</td></tr>
                                    @endif
                            </tbody>
                            </table>
                            <div>{{__('languages.showing')}} {{!empty($SelfLearningTestList->firstItem()) ? $SelfLearningTestList->firstItem() : 0 }} {{__('languages.to')}} {{!empty($SelfLearningTestList->lastItem()) ? $SelfLearningTestList->lastItem() : 0}}
                                {{__('languages.of')}}  {{$SelfLearningTestList->total()}} {{__('languages.entries')}}
                            </div>
                            <div class="pagination-data">
                                <div class="col-lg-9 col-md-9 pagintn">
                                    {{$SelfLearningTestList->appends(request()->input())->links()}} 
                                </div>
                                <div class="col-lg-3 col-md-3 pagintns">
                                    <form>
                                        <label for="pagination" id="per_page">{{__('languages.per_page')}}</label>
                                        <select id="pagination" >
                                            <option value="10" @if(app('request')->input('items') == 10) selected @endif >10</option>
                                            <option value="20" @if(app('request')->input('items') == 20) selected @endif >20</option>
                                            <option value="25" @if(app('request')->input('items') == 25) selected @endif >25</option>
                                            <option value="30" @if(app('request')->input('items') == 30) selected @endif >30</option>
                                            <option value="40" @if(app('request')->input('items') == 40) selected @endif >40</option>
                                            <option value="50" @if(app('request')->input('items') == 50) selected @endif >50</option>
                                            <option value="{{$SelfLearningTestList->total()}}" @if(app('request')->input('items') == $SelfLearningTestList->total()) selected @endif >{{__('languages.all')}}</option>
                                        </select>
                                    </form>
                                </div>
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

<!-- Student Result Summary Report -->
<div class="modal" id="StudentSummaryReportModal" tabindex="-1" aria-labelledby="StudentSummaryReportModal" aria-hidden="true" data-backdrop="static">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<form method="post">
				<div class="modal-header embed-responsive">
					<h4 class="modal-title w-100">{{__('languages.student_summary_report')}}</h4>
				</div>
				<div class="modal-body student-report-summary-data">
					
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default close-student-report-summary-popup" data-dismiss="modal">{{__('languages.close')}}</button>
				</div>
			</form>
		</div>
	</div>
</div>
<!-- End Result Summary Popup -->

<!-- Start Performance Analysis Popup -->
<!-- <div class="modal" id="class-ability-analysis-report" tabindex="-1" aria-labelledby="class-ability-analysis-report" aria-hidden="true" data-backdrop="static">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<form method="post">
				<input type="hidden" name="grade_ids" id="grade_ids" value="">
				<input type="hidden" name="exam_ids" id="exam_ids" value="">
				<input type="hidden" name="student_ids" id="student_ids" value="">
				<div class="modal-header">
					<h4 class="modal-title w-100">{{__('languages.class_ability_analysis')}}</h4>
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				</div>
				<div class="modal-body">
					<div class="row pb-2">
						<div class="col-md-4">
							<button type="button" class="btn btn-primary class-ability-graph-btn" data-graphtype="my-class">{{__('languages.my_class.my_classes')}}</button>
						</div>
						<div class="col-md-4">
							<button type="button" class="btn btn-primary class-ability-graph-btn" data-graphtype="my-school">{{__('languages.my_school')}}</button>
						</div>
						<div class="col-md-4">
							<button type="button" class="btn btn-primary class-ability-graph-btn" data-graphtype="all-school">{{__('languages.all_schools')}}</button>
						</div>
					</div>
					<div class="row">
						<img src="" id="class-ability-analysis-report-image" class="img-fluid">
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">{{__('languages.close')}}</button>
				</div>
			</form>
		</div>
	</div>
</div> -->
<!-- End Performance Analysis Popup -->

<!-- Start list of difficulties of the questions in the test Analysis Popup -->
<div class="modal" id="test-difficulty-analysis-report" tabindex="-1" aria-labelledby="test-difficulty-analysis-report" aria-hidden="true" data-backdrop="static">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<form method="post">
				<div class="modal-header">
					<h4 class="modal-title w-100">{{__('languages.question_difficulty_analysis')}}</h4>
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				</div>
				<div class="modal-body Graph-body">
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

<!-- Start Student create self learning test Popup -->
<!-- <div class="modal" id="studentCreateSelfLearningTestModal" tabindex="-1" aria-labelledby="studentCreateSelfLearningTestModal" aria-hidden="true" data-backdrop="static">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<form class="student-generate-test-form" method="get" id="student-generate-test-form">
				<div class="modal-header">
					<h4 class="modal-title w-100">{{__('languages.generate_self_learning')}} {{__('languages.excercise')}} & {{__('languages.generate_self_learning')}} {{__('languages.test_text')}}</h4>
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				</div>
				<div class="modal-body">
					<input type="hidden" name="grade_id" value="{{ Auth::user()->grade_id }}" id="grade-id">
					<input type="hidden" name="subject_id" value="1" id="subject-id">
					<input type="hidden" name="question_ids" value="" id="question-ids">
					<input type="hidden" name="self_learning_test_type" value="" id="self_learning_test_type">
					<div class="form-row">
						<div class="form-group col-md-6 mb-50">
							<label>{{__('languages.upload_document.strands')}}</label>
							<select name="strand_id[]" class="form-control select-option" id="strand_id" multiple>
								@if(isset($strandsList) && !empty($strandsList))
									@foreach ($strandsList as $strandKey => $strand)
										<option value="{{ $strand->id }}" <?php //if($strandKey == 0){echo 'selected';}?>>{{ $strand->{'name_'.app()->getLocale()} }}</option>
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
								<option value="manual">{{__('languages.manual')}}</option>
								<option value="auto" disabled >{{__('languages.auto')}}</option>
							</select>
						</div>
						<div class="form-group col-md-6 mb-50">
							<label>{{__('languages.questions.difficulty_level')}}</label>
							<select name="difficulty_lvl[]" class="form-control select-option" id="difficulty_lvl" multiple>
								@if(!empty($difficultyLevels))
								@foreach($difficultyLevels as $difficultyLevel)
								<option value="{{$difficultyLevel->difficulty_level}}">{{$difficultyLevel->difficulty_level_name}}</option>
								@endforeach
								@endif								
							</select>
							<span name="err_difficulty_level"></span>
						</div>
						<div class="form-group col-md-6 mb-50">
							<label>{{__('languages.no_of_question')}}</label>
							<input type="text" class="form-control" id="no_of_questions" name="no_of_questions" onkeyup="getTestTimeDuration()" value="" placeholder="{{__('languages.no_of_question')}}">
						</div>
						<div class="form-group col-md-6 mb-50 test_time_duration_section" style=display:none;>
							<label>{{__('languages.test_time_duration')}} ({{__('languages.hh_mm_ss')}})</label>
							<input type="text" class="form-control" id="test_time_duration" name="test_time_duration" value="" placeholder="{{__('languages.hh_mm_ss')}}">
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
</div> -->
<!-- End Student create self learning test Popup -->

@include('backend.layouts.footer')

<script>
    $(function(){
        /*for pagination add this script added by mukesh mahanto*/ 
		document.getElementById('pagination').onchange = function() {
			window.location = "{!! $SelfLearningTestList->url(1) !!}&items=" + this.value;
		};

        // $(document).on('click', '.test-tab', function() {
        //     $('.test-tab').removeClass('active');
        //     $('.tab-pane').removeClass('active');
        //     $('#'+$(this).attr('data-id')).addClass('active');
        //     $(this).addClass('active');
        //     $('#documentbtn form .active_tab').val($(this).attr('data-id'));
        //     $.cookie("PreviousTab", $(this).attr('data-id'));
        // });
        
        //Defalut remember tab selected into student panel and teacher panel
        // $('.test-tab').removeClass('active');
        // $('.tab-pane').removeClass('active');
        // if($.cookie("PreviousTab")){
        //     $('#tab-'+$.cookie("PreviousTab")).addClass('active');
        //     $('#'+$.cookie("PreviousTab")).addClass('active');
        // }else{
        //     $('#tab-self-learning').addClass('active');
        //     $('#self_learning').addClass('active');
        // }
        
        // $(document).on('change', '#AllTabs', function() {
        //     if($(this).prop('checked')){
        //         $(".categories-main-list .categories-list input[type=checkbox]").prop('checked',true);
        //     }else{
        //         $(".categories-main-list .categories-list input[type=checkbox]").prop('checked',false);
        //     }
        // });
    });
    
    $(function() {
        /**
         * USE : Display on graph Get Class APerformance Analysis
         * Trigger : On click Performance graph icon into exams list action table
         * **/
        // $(document).on('click', '.getClassAbilityAnalysisReport', function(e) {
        //     $("#cover-spin").show();
        //     $('#class-ability-analysis-report').modal('show');
        //     $studentIds = $(this).attr('data-studentids');
        //     $examId = $(this).attr('data-examid');
        //     $('#exam_ids').val($examId);
        //     $('#student_ids').val($studentIds);
        //     if($studentIds && $examId){
        //         $.ajax({
        //             url: BASE_URL + '/my-teaching/get-class-ability-analysis-report',
        //             type: 'post',
        //             data : {
        //                 '_token': $('meta[name="csrf-token"]').attr('content'),
        //                 'examid' : $examId,
        //                 'studentIds' : $studentIds,
        //                 'graph_type' : 'my-class'
        //             },
        //             success: function(response) {
        //                 var ResposnseData = JSON.parse(JSON.stringify(response));
        //                 if(ResposnseData.data != 0){
        //                     // Append image src attribute with base64 encode image
        //                     $('#class-ability-analysis-report-image').attr('src','data:image/jpg;base64,'+ ResposnseData.data);
        //                     $('#class-ability-analysis-report').modal('show');
        //                 }else{
        //                     toastr.error('Data not found');
        //                 }
        //                 $("#cover-spin").hide();
        //             },
        //             error: function(response) {
        //                 ErrorHandlingMessage(response);
        //             }
        //         });
        //     }
        // });
    
        /**
         * USE : Click on the diffrent button like this 'my-class', 'my-school', 'all-school'
         * **/
        $(document).on('click', '.class-ability-graph-btn', function(e) {
            $("#cover-spin").show();
            $studentIds = $('#student_ids').val();
            $examId = $('#exam_ids').val();
            if($studentIds && $examId){
                $.ajax({
                    url: BASE_URL + '/my-teaching/get-class-ability-analysis-report',
                    type: 'post',
                    data : {
                        '_token': $('meta[name="csrf-token"]').attr('content'),
                        'examid' : $examId,
                        'studentIds' : $studentIds,
                        'graph_type' : $(this).attr('data-graphtype')
                    },
                    success: function(response) {
                        var ResposnseData = JSON.parse(JSON.stringify(response));
                        if(ResposnseData.data != 0){
                            // Append image src attribute with base64 encode image
                            $('#class-ability-analysis-report-image').attr('src','data:image/jpg;base64,'+ ResposnseData.data);
                            $('#class-ability-analysis-report').modal('show');
                        }else{
                            toastr.error('Data not found');
                        }
                        $("#cover-spin").hide();
                    },
                    error: function(response) {
                        ErrorHandlingMessage(response);
                    }
                });
            }
        });
    
        // This click event to display exam details
        $(document).on('click', '.exam_info', function() {
            $("#cover-spin").show();
            var examid=$(this).attr('data-examid');		
            var grade_id=$(this).attr('data-grade-id');	
            $("#studentCreateSelfLearningTestModal #grade-id").val(grade_id);
            
            $.ajax({
                url: BASE_URL + '/get-exam-info/'+examid,
                type: 'GET',
                success: function(response) {
                    var data_id=$(".study-learning-tab .test-tab.active").attr('data-id');
                    if(data_id=='test'){
                        $('.test_time_duration_section').show();
                    }else{
                        $('.test_time_duration_section').hide();
                    }
                    
                    if(response.data.length!=0){
                        var strand_ids = response.data.strand_ids;
                        $("#studentCreateSelfLearningTestModal #student-generate-test-form input,select").prop('disabled',true);
                        if(strand_ids != ""){
                            strand_ids = strand_ids.split(',');
                            $("#studentCreateSelfLearningTestModal #strand_id").val(strand_ids);
                            var strand_id = $("#studentCreateSelfLearningTestModal #strand_id").multiselect("rebuild");
                            $("#studentCreateSelfLearningTestModal #strand_id").trigger("change");
                        }
                        var learning_units = response.data.learning_unit_ids;
                        if(learning_units != ""){
                            learning_units = learning_units.split(',');
                            $("#studentCreateSelfLearningTestModal #learning_unit").val(learning_units);
                            $("#studentCreateSelfLearningTestModal #learning_unit").multiselect("rebuild");
                            $("#studentCreateSelfLearningTestModal #learning_unit").trigger("change");
                        }
                        var learning_objectives = response.data.learning_objectives_ids;
                        if(learning_objectives != ""){
                            learning_objectives = learning_objectives.split(',');
                            $("#studentCreateSelfLearningTestModal #learning_objectives").val(learning_objectives);
                            $("#studentCreateSelfLearningTestModal #learning_objectives").multiselect("rebuild");
                        }
                        var difficulty_lvls = response.data.difficulty_mode;
                        if(difficulty_lvls != ""){
                            difficulty_lvls = difficulty_lvls.split(',');
                            $("#studentCreateSelfLearningTestModal #difficulty_lvl").val(difficulty_lvls);
                            $("#studentCreateSelfLearningTestModal #difficulty_lvl").multiselect("rebuild");
                        }
                        var difficulty_levels = response.data.difficulty_levels;
                        if(difficulty_levels != ""){
                            $("#studentCreateSelfLearningTestModal #difficulty_mode").val(difficulty_levels);
                        }
                        var no_of_questions=response.data.no_of_questions;
                        if(no_of_questions != ""){
                            $("#studentCreateSelfLearningTestModal #no_of_questions").val(no_of_questions);
                        }
                        var time_duration=response.data.time_duration;
                        if(time_duration != ""){
                            $("#studentCreateSelfLearningTestModal #test_time_duration").val(time_duration);
                        }
                        $("#studentCreateSelfLearningTestModal").modal('show');
                    }else{
                        toastr.error('Configuration Data not found');
                    }
                    $("#cover-spin").hide();
                },
                error: function(response) {
                    ErrorHandlingMessage(response);
                }
            });
            $("#studentCreateSelfLearningTestModal #generate_test").hide();
        });
    
        /**
         * USE : Get Learning Units from multiple strands
         * **/
        // $(document).on('change', '#strand_id', function() {
        //     $strandIds = $('#strand_id').val();
        //     if($strandIds != ""){
        //         $.ajax({
        //             url: BASE_URL + '/getLearningUnitFromMultipleStrands',
        //             type: 'POST',
        //             data: {
        //                 '_token': $('meta[name="csrf-token"]').attr('content'),
        //                 'grade_id': $('#grade-id').val(),
        //                 'subject_id': $('#subject-id').val(),
        //                 'strands_ids': $strandIds
        //             },
        //             success: function(response) {
        //                 $('#learning_unit').html('');
        //                 $("#cover-spin").hide();
        //                 var data = JSON.parse(JSON.stringify(response));
        //                 if(data){
        //                     if(data.data){
        //                         $(data.data).each(function() {
        //                             var option = $('<option />');
        //                             option.attr('value', this.id).text(this["name_"+APP_LANGUAGE]);
        //                             option.attr('selected', 'selected');
        //                             $('#learning_unit').append(option);
        //                         });
        //                     }else{
        //                         $('#learning_unit').html('<option value="">'+LEARNING_UNITS_NOT_AVAILABLE+'</option>');
        //                     }
        //                 }else{
        //                     $('#learning_unit').html('<option value="">'+LEARNING_UNITS_NOT_AVAILABLE+'</option>');
        //                 }
        //                 $('#learning_unit').multiselect("rebuild");
        //                 $('#learning_unit').trigger("change");
        //             },
        //             error: function(response) {
        //                 ErrorHandlingMessage(response);
        //             }
        //         });
        //     }else{
        //         $('#learning_unit, #learning_objectives').html('');
        //         $('#learning_unit, #learning_objectives').multiselect("rebuild");
        //     }
        // });
    
        /**
         * USE : Get Multiple Learning units based on multiple learning units ids
         * **/
        // $(document).on('change', '#learning_unit', function() {
        //     $strandIds = $('#strand_id').val();
        //     $learningUnitIds = $('#learning_unit').val();
        //     if($learningUnitIds != ""){
        //         $.ajax({
        //             url: BASE_URL + '/getLearningObjectivesFromMultipleLearningUnits',
        //             type: 'POST',
        //             data: {
        //                 '_token': $('meta[name="csrf-token"]').attr('content'),
        //                 'grade_id': $('#grade-id').val(),
        //                 'subject_id': $('#subject-id').val(),
        //                 'strand_id': $strandIds,
        //                 'learning_unit_id': $learningUnitIds
        //             },
        //             success: function(response) {
        //                 $('#learning_objectives').html('');
        //                 $("#cover-spin").hide();
        //                 var data = JSON.parse(JSON.stringify(response));
        //                 if(data){
        //                     if(data.data){
        //                         $(data.data).each(function() {
        //                             var option = $('<option />');
        //                             option.attr('value', this.id).attr('selected','selected').text(this.foci_number + ' ' + this.title);
        //                             $('#learning_objectives').append(option);
        //                         });
        //                     }else{
        //                         $('#learning_objectives').html('<option value="">'+LEARNING_OBJECTIVES_NOT_AVAILABLE+'</option>');
        //                     }
        //                 }else{
        //                     $('#learning_objectives').html('<option value="">'+LEARNING_OBJECTIVES_NOT_AVAILABLE+'</option>');
        //                 }
        //                 $('#learning_objectives').multiselect("rebuild");
        //             },
        //             error: function(response) {
        //                 ErrorHandlingMessage(response);
        //             }
        //         });
        //     }else{
        //         $('#learning_objectives').html('');
        //         $('#learning_objectives').multiselect("rebuild");
        //     }
        // });
    });
    </script>
@endsection