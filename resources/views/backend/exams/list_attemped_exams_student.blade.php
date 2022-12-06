@extends('backend.layouts.app')
    @section('content')
		<div class="wrapper d-flex align-items-stretch sm-deskbord-main-sec">
            @include('backend.layouts.sidebar')
	        <div id="content" class="pl-2 pb-5">
            @include('backend.layouts.header')
            @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
            @endif
            <div class="sm-right-detail-sec pl-5 pr-5">
				<div class="container-fluid">
                    <div class="row">
						<div class="col-md-12">
							<div class="sec-title">
								<h4 class="mb-4">{{__('languages.test.test_name')}} : {{(!empty($examsData) ? $examsData->title : '')}} ({{$examsData->reference_no}})</h4>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-md-12">
							<div class="sec-title">
								<h2 class="mb-4 ">{{__('languages.test.student_list')}}</h2>
							</div>
						</div>
					</div>
                    <div class="row">
						<div class="col-md-12">
							<div class="sec-title">
                                <a href="javascript:void(0);" class="btn-back" id="backButton">{{__('languages.back')}}</a>
							</div>
							<hr class="blue-line">
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
                                        <span class="font-weight-bold pl-2">{{($student->name) ? $student->name : App\Helpers\Helper::decrypt($student->name_en) }}</span>
                                    </div>
                                    <div class="pt5 pl-4">
                                        <div class="row">
                                            <div class="col-lg-2 col-md-2 col-sm-12">
                                                <label for="grade">{{__('languages.grade')}} : {{$student->grades->name ?? 'N/A'}}</label>
                                            </div>
                                            <div class="col-lg-3 col-md-3 col-sm-12">
                                                <label for="email">{{__('languages.email')}} : {{$student->email ?? 'N/A'}}</label>
                                            </div>
                                            <div class="col-lg-3 col-md-3 col-sm-12">
                                                <label for="class_student_number">{{__('languages.class_student_number')}} : {{$student->CurriculumYearData->class_student_number ?? 'N/A'}}</label>
                                            </div>
                                            <div class="col-lg-2 col-md-2 col-sm-12">
                                                <label for="test_status">{{__('languages.test_status')}} :
                                                    @if(in_array($student->id,$attemptedExamStudentIds))
                                                        <span class="badge badge-success">{{__('languages.test.complete')}}</span>
                                                    @else
                                                        <span class="badge badge-warning">{{__('languages.test.pending')}}</span>
                                                    @endif
                                                </label>
                                            </div>
                                            @php
                                            if(App\Helpers\Helper::isAdmin()){
                                                $examId = App\Helpers\Helper::getAttemptedChildExamResultStudent($examsData->id,$student->id);
                                            }else{
                                                $examId = $examsData->id;
                                            }
                                            @endphp
                                            @if(in_array($student->id,$attemptedExamStudentIds))
                                            <div class="col-lg-2 col-md-2 col-sm-12">
                                                <a href="{{route('adminexams.result',['examid' => $examId, 'studentid' => $student->id])}}" class="btn btn-primary btn-sm view-result-btn" data-examid="{{$examsData->id}}" data-studentid={{$student->id}}>{{__('languages.view_result')}}</a>
                                            </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <hr>
                            @endforeach
                            <div>{{__('languages.showing')}} {{!empty($studentList->firstItem()) ? $studentList->firstItem() : 0}} {{__('languages.to')}} {{!empty($studentList->lastItem()) ? $studentList->lastItem() : 0}}
								{{__('languages.of')}}  {{$studentList->total()}} {{__('languages.entries')}}
							</div>
                            <div class="pagination-data">
									<div class="col-lg-9 col-md-9 pagintn">
										@if((app('request')->input('items'))=== null)
											{{$studentList->appends(request()->input())->links()}}
										@else
											{{$studentList->appends(compact('items'))->links()}}
										@endif
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
                                            <option value="{{$studentList->total()}}" @if(app('request')->input('items') == $studentList->total()) selected @endif >{{__('languages.all')}}</option>
                                        </select>
                                    </form>
									</div>
								</div>
                            @else
                            <p>{{__('languages.test.no_attempt_exam')}}</p>
                            @endif
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
            <div class="modal-body">{{__('languages.my_studies.in_this_section_will_be_displayed_test_result')}} </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">{{__('languages.close')}}</button>
                <!-- <button type="button" class="btn btn-primary">Save changes</button> -->
            </div>
        </div>
    </div>
</div>
@if($studentList)
<script>
/*for pagination add this script added by mukesh mahanto*/ 
document.getElementById('pagination').onchange = function() {
    window.location = "{!! $studentList->url(1) !!}&items=" + this.value;	
};
</script>
@endif
@include('backend.layouts.footer')
@endsection