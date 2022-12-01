@extends('backend.layouts.app')
    @section('content')
		<div class="wrapper d-flex align-items-stretch sm-deskbord-main-sec">
            @include('backend.layouts.sidebar')
	        <div id="content" class="pl-2 pb-5">
            @include('backend.layouts.header')
            @if (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
            @endif
            <div class="sm-right-detail-sec pl-5 pr-5">
				<div class="container-fluid">
					<div class="row">
						<div class="col-md-12">
                            <div class="sec-title">
								<h5 class="mb-4">{{__('languages.report.class_performance')}}</h5>
							</div>
							<hr class="blue-line">
						</div>
					</div>
                    <form class="class-test-report" id="class-test-report" action="{{route('report.class-test-reports.correct-incorrect-answer')}}" method="get">
						<!-- <div class="row">
							<div class="col-md-12">
								<div class="select-lng pt-2 pb-2" style="float:left;">
									<a href="#" class="btn-search remove-radius {{ (request()->is('report/class-test-reports/correct-incorrect-answer')) ? 'active': ''  }}">{{ __('Correct/Incorrect Test Report') }}</a>
								</div>
								<div class="select-lng pt-2 pb-2">
									<a href="{{ route('reports.exam-list')}}" class="btn-search remove-radius" id="">{{ __('Details') }}</a>
								</div>
							</div>
						</div> -->
						<div class="row">
							<div class="select-lng pt-2 pb-2 col-lg-2 col-md-4">
								<select name="exam_id"  id="exam_id" class="form-control select-option">
									<option value="">{{ __('languages.report.exams') }}</option>
									@if(!empty($ExamList))
										@foreach($ExamList as $exams)
										<option value="{{$exams->id}}" {{ request()->get('details_report_exam_id') == $exams->id ? 'selected' : '' }}>{{ $exams->title}}</option>
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
							<label><b>{{__('languages.report.date_of_release')}}:</b><span> {{!empty($ExamData->publish_date) ? date('d/m/Y H:i:s',strtotime($ExamData->publish_date)) : ''}}</span></label>
						</div>
						@endif
						<div class="col-lg-3 col-md-3">
							<label><b>{{__('languages.report.start_date')}}:</b> <span>{{!empty($ExamData->from_date) ? date('d/m/Y',strtotime($ExamData->from_date)) : ''}}</span></label>
						</div>
						<div class="col-lg-3 col-md-3">
							<label><b>{{__('languages.report.end_date')}}:</b> <span>{{!empty($ExamData->to_date) ? date('d/m/Y',strtotime($ExamData->to_date)): ''}}</span></label>
						</div>
						<div class="col-lg-3 col-md-3">
							<label><b>{{__('languages.report.result_date')}}:</b> <span>{{ !empty($ExamData->result_date) ? date('d/m/Y',strtotime($ExamData->result_date)) : ''}}</span></label>
						</div>
					</div>
                    <div class="row correct-incorrect-row mt-2 mb-2">
						<div class="col-md-12 correct-incorrect-col">
                            <form id="exam-details-reports" action="{{ route('report.class-test-reports.correct-incorrect-answer')}}" method="get">
                            <input type="hidden" name="exam_id" id="exam_id" value="{{request()->get('details_report_exam_id')}}">
							<div class="select-lng">
								<!-- <a href="javascript:void(0);" class="btn-search remove-radius {{ (request()->is('report/class-test-reports/correct-incorrect-answer?')) ? 'active': ''  }}">{{ __('Correct/Incorrect Test Report') }}</a> -->
                                <button type="submit" name="filter" value="filter" class="btn-search remove-radius {{ (request()->is('report/class-test-reports/correct-incorrect-answer')) ? 'active': ''  }}">{{ __('languages.report.class_performance') }}</a>
							</div>
                            </form>
							<form id="exam-details-reports" action="{{ route('report.exams.student-test-performance')}}" method="get">
							<input type="hidden" name="details_report_exam_id" id="details_report_exam_id" value="{{request()->get('details_report_exam_id')}}">
							<div class="select-lng">
								<input type="submit" class=" btn-search remove-radius active class-test-report-detail-btn" value="{{ __('languages.report.details') }}">
							</div>
							</form>
                            <?php if(Auth::user()->role_id == 1){ ?>
							<form id="exam-details-reports" action="{{ route('report.school-comparisons')}}" method="get">
							<input type="hidden" name="exam_id" id="exam_id" value="{{request()->get('details_report_exam_id')}}">
							<div class="select-lng">
								<input type="submit" class=" btn-search remove-radius school-comparison-btn" value="{{ __('languages.report.school_comparison_result') }}">
							</div>
							</form>
                            <?php } ?>
						</div>
					</div>

					<div class="sm-add-user-sec card">
						<div class="select-option-sec pb-2 card-body">
                            @if(!empty($Questions))
                                @foreach($Questions as $key => $question)
                                <div class="row">
                                    <div class="sm-que-option pl-3">
                                    <p class="sm-title bold">{{__('languages.result.q_no')}} : {{$loop->iteration}}</p>
                                        <div class="sm-que pl-2">
                                            <p><?php echo $question->{'question_en'}; ?></p>
                                        </div>
                                        @if(isset($question->answers->{'answer1_en'}))
                                        <div class="sm-ans pl-2 pb-2">
                                            <div class="answer-title mr-2 @if($question->answers->{'correct_answer_en'} === 1) correct-answer @else incorrect-answer @endif">A</div>
                                            <div class="progress">
                                                <div class="progress-bar @if($question->answers->{'correct_answer_en'} === 1) ans-correct @else ans-incorrect @endif  @if(empty($percentageOfAnswer[$question->id][1])) no-answer-selected @endif" role="progressbar" aria-valuenow="{{$percentageOfAnswer[$question->id][1]}}" aria-valuemin="0" aria-valuemax="100" style="width:{{$percentageOfAnswer[$question->id][1]}}%">
                                                    <div class="anser-detail pl-2">
                                                        <?php echo $question->answers->{'answer1_en'}; ?>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="answer-progress">
                                                <p class="progress-percentage">{{$percentageOfAnswer[$question->id][1]}}%</p>
                                            </div>
                                        </div>
                                        @endif

                                        @if(isset($question->answers->{'answer2_en'}))
                                        <div class="sm-ans pl-2 pb-2">
                                            <div class="answer-title mr-2 @if($question->answers->{'correct_answer_en'} === 2) correct-answer @else incorrect-answer @endif">B</div>
                                            <div class="progress">
                                                <div class="progress-bar @if($question->answers->{'correct_answer_en'} === 2) ans-correct @else ans-incorrect @endif  @if(empty($percentageOfAnswer[$question->id][2])) no-answer-selected @endif" role="progressbar" aria-valuenow="{{$percentageOfAnswer[$question->id][2]}}" aria-valuemin="0" aria-valuemax="100" style="width:{{$percentageOfAnswer[$question->id][2]}}%">
                                                    <div class="anser-detail pl-2">
                                                        <?php echo $question->answers->{'answer2_en'}; ?>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="answer-progress">
                                            <p class="progress-percentage">{{$percentageOfAnswer[$question->id][2]}}%</p>
                                            </div>
                                        </div>
                                        @endif

                                        @if(isset($question->answers->{'answer3_en'}))
                                        <div class="sm-ans pl-2 pb-2">
                                            <div class="answer-title mr-2 @if($question->answers->{'correct_answer_en'} === 3) correct-answer @else incorrect-answer @endif">C</div>
                                            <div class="progress">
                                                <div class="progress-bar @if($question->answers->{'correct_answer_en'} === 3) ans-correct @else ans-incorrect @endif  @if(empty($percentageOfAnswer[$question->id][3])) no-answer-selected @endif" role="progressbar" aria-valuenow="{{$percentageOfAnswer[$question->id][3]}}" aria-valuemin="0" aria-valuemax="100" style="width:{{$percentageOfAnswer[$question->id][3]}}%">
                                                    <div class="anser-detail pl-2">
                                                        <?php echo $question->answers->{'answer3_en'}; ?>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="answer-progress">
                                            <p class="progress-percentage">{{$percentageOfAnswer[$question->id][3]}}%</p>
                                            </div>
                                        </div>
                                        @endif

                                        @if(isset($question->answers->{'answer4_en'}))
                                        <div class="sm-ans pl-2 pb-2">
                                            <div class="answer-title mr-2 @if($question->answers->{'correct_answer_en'} === 4) correct-answer @else incorrect-answer @endif">D</div>
                                            <div class="progress">
                                                <div class="progress-bar @if($question->answers->{'correct_answer_en'} === 4) ans-correct @else ans-incorrect @endif  @if(empty($percentageOfAnswer[$question->id][4])) no-answer-selected @endif" role="progressbar" aria-valuenow="{{$percentageOfAnswer[$question->id][4]}}" aria-valuemin="0" aria-valuemax="100" style="width:{{$percentageOfAnswer[$question->id][4]}}%">
                                                    <div class="anser-detail pl-2">
                                                        <?php echo $question->answers->{'answer4_en'}; ?>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="answer-progress">
                                            <p class="progress-percentage">{{$percentageOfAnswer[$question->id][4]}}%</p>
                                            </div>
                                        </div>
                                        @endif
                                    </div>
                                </div>
                                <hr>
                                @endforeach
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