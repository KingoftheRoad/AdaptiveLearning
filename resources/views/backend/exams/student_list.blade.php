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
								<h4 class="mb-4">{{__('languages.test.test_name')}} : {{(!empty($ExamData) ? $ExamData->title : '')}}</h4>
							</div>
						</div>
					</div>

					<div class="row">
						<div class="col-md-12">
							<div class="sec-title">
								<h4 class="mb-4">{{__('languages.student_list')}}</h4>
							</div>
						</div>
					</div>

                    <div class="row">
						<div class="col-md-12">
							<div class="sec-title">
                            <a href="{{route('exams.index')}}" class="btn-back">{{__('languages.back')}}</a>
							</div>
							<hr class="blue-line">
						</div>
					</div>
                    <!-- Start Question list search -->
                    <form class="addStudentFilterForm" id="addStudentFilterForm" method="get">
                        <div class="row">
                            @if(App\Helpers\Helper::isAdmin())
                            <div class="col-lg-2 col-md-3">
                                <div class="select-lng pt-2 pb-2">
                                    <select name="school_id" class="form-control select-search select-option" id="school-id" onchange="$('#addStudentFilterForm').submit();">
                                        @if(!empty($Schools))
                                            <option value="">{{ __('languages.schools') }}</option>
                                            @foreach($Schools as $school)
                                            <option value={{ $school->id }} {{ request()->get('school_id') == $school->id ? 'selected' : '' }} >
                                                @if(app()->getLocale()=='ch')
                                                    {{ ucfirst($school->DecryptSchoolNameCh) }}
                                                @else
                                                    {{ ucfirst($school->DecryptSchoolNameEn) }}
                                                @endif
                                            </option> 
                                            @endforeach
                                        @else
                                            <option value="">{{ __('languages.no_available_school') }}</option>
                                        @endif
                                    </select>
                                    @if($errors->has('school_id'))
                                        <span class="validation_error">{{ $errors->first('school_id') }}</span>
                                    @endif
                                </div>
                            </div>
                            @endif
                            <div class="col-lg-2 col-md-3">
                                <div class="select-lng pt-2 pb-2">
                                    <select name="student_grade_id" class="form-control select-search select-option" id="student_grade_id" >
                                        @if(!empty($Grades))                                        
                                            <option value="">{{ __('languages.grade') }}</option>
                                            @foreach($Grades as $grade)
                                            <option value={{ $grade->id }} {{ request()->get('student_grade_id') == $grade->id ? 'selected' : '' }} >{{ ucfirst($grade->name ?? '') }}</option> 
                                            @endforeach
                                        @else
                                            <option value="">{{ __('languages.no_grade_available') }}</option>
                                        @endif
                                    </select>
                                    @if($errors->has('student_grade_id'))
                                        <span class="validation_error">{{ $errors->first('student_grade_id') }}</span>
                                    @endif
                                </div>
                            </div>
                            <div class="col-lg-2 col-md-3">
                                <div class="select-lng pt-2 pb-2">
                                    <select name="class_type_id[]" class="form-control select-option" id="classType-select-option" multiple >
                                        {!!$classTypeOptions!!}
                                    </select>
                                @if($errors->has('class_type_id'))<span class="validation_error">{{ $errors->first('class_type_id') }}</span>@endif
                                </div>
                            </div>
                            <div class="col-lg-2 col-md-3">
                                <div class="select-lng pt-2 pb-2">
                                    <button type="submit" name="filter" value="filter" class="btn-search">{{ __('languages.search') }}</button>
                                </div>
                            </div>
                        </div>
                    </form>
                    <!-- End Student Filter Form -->

                    <!-- Start Student List -->
                    @if($errors->has('student_ids'))<span class="validation_error">{{ $errors->first('student_ids') }}</span>@endif
					<div class="sm-add-user-sec card">
						<div class="select-option-sec pb-2 card-body">
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
                            <div class="row">
                                <div class="sm-que-list pl-4">
                                    <div class="sm-que">
                                    <input type="checkbox" name="select-all-student" id="select-all-student" class="checkbox" data-examid="{{$ExamData->id}}" {{$checked}}/>
                                        <span class="font-weight-bold pl-2"> {{__('languages.check_all')}}</span><br>
                                    </div>
                                </div>
                            </div>
                            <hr>
                            @if(!empty($studentList))
                            @foreach($studentList as $student)
                            @php
                            $assignedstudent = [];
                            if(!empty($assignStudent)){
                                $assignedstudent = explode(',', $assignStudent->student_ids);
                            }
                            @endphp
                            <div class="row">
                                <div class="sm-que-list pl-4">
                                    <div class="sm-que">
                                        <input type="checkbox" name="student_ids" class="checkbox student-ids" value="{{$student->id}}" data-examid="{{$ExamData->id}}" @if(in_array($student->id,$assignedstudent)) checked @endif/>
                                        <input type="hidden" name="exam_id" value= "{{request()->route('id')}}" />
                                        <span class="font-weight-bold pl-2">{{($student->name_en) ?  App\Helpers\Helper::decrypt($student->name_en) : $student->name }}</span>
                                    </div>
                                    <div class="pt5 pl-4">
                                        <div class="row">
                                            <div class="col-lg-4 col-md-4 col-sm-12">
                                                <label for="email">{{__('languages.grade')}} : {{$student->grades->name  ?? 'N/A'}} : {{ App\Helpers\Helper::getSingleClassName($student->class_id)}}</label>
                                            </div>
                                            <div class="col-lg-4 col-md-4 col-sm-12">
                                                <label for="email">{{__('languages.email')}} : {{$student->email ?? 'N/A'}}</label>
                                            </div>
                                            <div class="col-lg-4 col-md-4 col-sm-12">
                                                <label for="email">{{__('languages.gender')}} : 
                                                    @if($student->gender == 'male')
                                                        <span class="badge badge-success">Male</span>
                                                    @else
                                                        <span class="badge badge-info">Female</span>
                                                    @endif
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <hr>
                            @endforeach
                            @endif
                           
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
                        </div> 
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
            <script>
                 //for per Page on filteration hidden 
                    var TotalFilterData = "{!! $TotalFilterData !!}";
                        if((TotalFilterData > 0 && TotalFilterData <= 10)){
                            document.getElementById("pagination").style.visibility = "hidden";
                            document.getElementById("per_page").style.visibility = "hidden";
                        }
				/*for pagination add this script added by mukesh mahanto*/ 
				document.getElementById('pagination').onchange = function() {
						window.location = "{!! $studentList->url(1) !!}&items=" + this.value;	
				}; 
		</script>
@include('backend.layouts.footer')
@endsection