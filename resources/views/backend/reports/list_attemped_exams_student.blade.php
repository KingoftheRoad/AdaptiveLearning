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
								<h2 class="mb-4 main-title">{{__('languages.group_management.student_list')}}</h2>
							</div>
							<hr class="blue-line">
						</div>
					</div>
                    <div class="row">
						<div class="col-md-12 pb-2">
                            <div class="select-lng pt-2 pb-2 pr-2" style="float:left;">
								<a href="javascript:void(0);" class="btn-back" onclick="window.history.back();">{{ __('languages.group_management.back') }}</a>
							</div>
							<div class="select-lng pt-2 pb-2" style="float:left;">
								<a href="{{route('report.class-test-reports.correct-incorrect-answer')}}" class="btn-search remove-radius {{ (request()->is('report/class-test-reports/correct-incorrect-answer')) ? 'active': ''  }}">{{ __('languages.correct_incorrect_answer') }}</a>
							</div>
							<div class="select-lng pt-2 pb-2">
								<a href="{{ route('reports.exam-list')}}" class="btn-search remove-radius {{ (request()->is('reports/attempt-exams/student-list/*')) ? 'active': ''  }}" id="">{{ __('languages.result.student_result') }}</a>
							</div>
						</div>
					</div>
                    <!-- Start Student List -->
					<div class="sm-add-user-sec card">
						<div class="select-option-sec pb-2 card-body">
                            @if(!empty($studentList))
                            @foreach($studentList as $student)
                            <div class="row">
                                <div class="sm-que-list pl-4">
                                    <div class="sm-que">
                                        <input type="hidden" name="exam_id" value= "{{request()->route('id')}}" />
                                        <span class="font-weight-bold pl-2">{{$student->name ?? ''}}</span>
                                    </div>
                                    <div class="pt5 pl-4">
                                        <div class="row">
                                            <div class="col-lg-3 col-md-3 col-sm-12">
                                                <label for="email">{{__('languages.grade')}} : {{$student->grades->name ?? ''}}</label>
                                            </div>
                                            <div class="col-lg-3 col-md-3 col-sm-12">
                                                <label for="email">{{__('languages.email')}} : {{$student->email ?? ''}}</label>
                                            </div>
                                            <div class="col-lg-3 col-md-3 col-sm-12">
                                                <label for="email">{{__('languages.test_status')}} :
                                                    @if(in_array($student->id,$attemptedExamStudentIds))
                                                        <span class="badge badge-success">{{__('languages.complete')}}</span>
                                                    @else
                                                        <span class="badge badge-warning">{{__('languages.pending')}}</span>
                                                    @endif
                                                </label>
                                            </div>
                                            <form method="post" action="{{route('report.exams.student-test-performance')}}">
                                                @csrf()
                                                <input type="hidden" name="exam_id" value="{{$examsData->id}}">
                                                <input type="hidden" name="student_id" value="{{$student->id}}">
                                                @if(in_array($student->id,$attemptedExamStudentIds))
                                                <div class="col-lg-3 col-md-3 col-sm-12">
                                                    <button style="background: rgb(107, 105, 105); border-color: #4c4c51 !important;" type="submit" class="btn btn-primary btn-sm">{{__('languages.view_result')}}</button>
                                                </div>
                                            </form>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <hr>
                            @endforeach
                            @else
                            <p>{{__('languages.no_any_students_are_attempt_exams_please_wait_until_students_can_attempt_this_exams')}}</p>
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