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
                            <h4 class="mb-4">{{__('languages.group_management.group_name')}} : {{(!empty($StudentGroupData) ? $StudentGroupData->name : '')}}</h4>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="sec-title">
                            <h2 class="mb-4 main-title">{{__('languages.group_management.student_list')}}</h2>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="sec-title">
                            <a href="javascript:void(0);" class="btn-back" id="backButton">{{__('languages.group_management.back')}}</a>
                        </div>
                        <hr class="blue-line">
                    </div>
                </div>
                <form class="addGroupFilterForm" id="addGroupFilterForm" method="get">	
                    <div class="row">
						<div class="col-lg-2 col-md-4">
                            <div class="select-lng pt-2 pb-2">
                                <span>{{__('languages.group_management.search_by_school')}}</span>
								<select class="selectpicker form-control" data-show-subtext="true" data-live-search="true" name="School" id="School">
									<option value=''>{{ __('languages.group_management.select_school') }}</option>
									@if(!empty($SchoolData))
                                    @foreach($SchoolData as $school)
                                    <option value="{{$school['id']}}" {{ request()->get('School') == $school['id'] ? 'selected' : '' }}>{{ $school['school_name']}}</option>
                                    @endforeach
                                    @endif
                                </select>
                                @if($errors->has('School'))
                                <span class="validation_error">{{ $errors->first('School') }}</span>
                                @endif
                            </div>
                        </div>
                        <div class="col-lg-2 col-md-3">
                            <div class="select-lng pt-2 pb-2">
                                <span>{{__('languages.group_management.search_by_name')}}</span>
                                <input type="text" class="input-search-box mr-2" name="name" value="{{request()->get('name')}}" placeholder="{{__('languages.group_management.search_by_student_name')}}">
                            </div>
                        </div>
                        <div class="col-lg-2 col-md-3">
                            <div class="select-lng pt-2 pb-2">
                                <span> </span>
                                <button type="submit" name="filter" value="filter" class="btn-search" style="margin-top: 23px;">{{ __('languages.group_management.search') }}</button>
                            </div>
                        </div>
                    </div>
                </form>
                <!-- Start Student List -->
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
                                    <input type="checkbox" name="select-all-student-groups" id="select-all-student-groups" data-groupid="{{ request()->id}}" class="checkbox" {{$checked}}/>
                                    <span class="font-weight-bold pl-2"> {{__('languages.group_management.check_all')}}</span><br>
                                </div>
                            </div>
                        </div>
                        <hr>
                        @if(!empty($studentList))
                        @foreach($studentList as $student)
                        @php
                        $assignedstudent = [];
                        if(!empty($StudentGroupData)){
                            $assignedstudent = explode(',', $StudentGroupData->student_ids);
                        }
                        @endphp
                        <div class="row">
                            <div class="sm-que-list pl-4">
                                <div class="sm-que">
                                    <input type="checkbox" name="student_ids" class="checkbox group-student-ids" value="{{$student->id}}" data-groupid="{{ request()->id}}" @if(in_array($student->id,$assignedstudent)) checked @endif/>
                                    <input type="hidden" name="exam_id" value= "{{request()->route('id')}}" />
                                    <span class="font-weight-bold pl-2">{{($student->name_en) ? App\Helpers\Helper::decrypt($student->name_en) : $student->name  }}</span>
                                </div>
                                <div class="pt5 pl-4">
                                    <div class="row">
                                        <div class="col-lg-4 col-md-4 col-sm-12">
                                            <label for="grade">{{__('languages.group_management.grade')}} : {{$student->grades->name ?? 'N/A'}}</label>
                                        </div>
                                        <div class="col-lg-4 col-md-4 col-sm-12">
                                            <label for="email">{{__('languages.group_management.email')}} : {{$student->email ?? 'N/A'}}</label>
                                        </div>
                                        <div class="col-lg-4 col-md-4 col-sm-12">
                                            @if(!empty($student->gender))
                                            <label for="email">{{__('languages.group_management.gender')}} :
                                                @if($student->gender == 'male')
                                                <span class="badge badge-success">{{__('languages.group_management.male')}}</span>
                                                @else
                                                <span class="badge badge-info">{{__('languages.group_management.female')}}</span>
                                                @endif
                                            </label>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <hr>
                        @endforeach
                        @endif
                        <div>{{__('languages.showing')}} {{($studentList->currentpage()-1)*$studentList->perpage()+1}} {{__('languages.to')}} {{$studentList->currentpage()*$studentList->perpage()}} {{__('languages.of')}}  {{$studentList->total()}} {{__('languages.entries')}} </div>
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
                                <label for="pagination" id="per_page">{{__('languages.group_management.per_page')}}</label>
                                <select id="pagination">
                                    <option value="10" @if(app('request')->input('items') == 10) selected @endif >10</option>
                                    <option value="20" @if(app('request')->input('items') == 20) selected @endif >20</option>
                                    <option value="25" @if(app('request')->input('items') == 25) selected @endif >25</option>
                                    <option value="30" @if(app('request')->input('items') == 30) selected @endif >30</option>
                                    <option value="40" @if(app('request')->input('items') == 40) selected @endif >40</option>
                                    <option value="50" @if(app('request')->input('items') == 50) selected @endif >50</option>
                                    <option value="{{$TotalStudentData}}" @if(app('request')->input('items') == $TotalStudentData) selected @endif >{{__('languages.all')}}</option>
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
var TotalData = {!! $TotalStudentData !!};
if(TotalData == 0 || (TotalData >= 0 && TotalData <= 10)){
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