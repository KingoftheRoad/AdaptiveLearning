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
						<div class="col-md-12">
							<div class="sec-title">
								<h4 class="mb-4">{{__('languages.student_management.assign_students')}}</h4>
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

                    <!-- Start Question list search -->
                    <form class="StudentFilterForm" id="StudentFilterForm" method="get">
                    <div class="row">
                        <div class="col-lg-4 col-md-3">
                            <div class="select-lng pt-2 pb-2">
                                <input type="text" class="input-search-box mr-2" id="searchs" name="searchs" value="{{request()->get('searchs')}}" placeholder="{{__('languages.search_by_name')}}, {{__('languages.email')}}">
                            </div>
                        </div>
                        <div class="select-lng pt-2 pb-2 col-lg-2 col-md-3">                            
                            <select name="gender"  class="form-control select-option">
                                <option value="">{{ __('languages.user_management.gender') }}</option>
                                <option value="male" {{ request()->get('gender') == 'male' ? 'selected' : '' }}>{{__('languages.user_management.male')}}</option>
                                <option value="female" {{ request()->get('gender') == 'female' ? 'selected' : '' }}>{{__('languages.user_management.female')}}</option>
                                <option value="other" {{ request()->get('gender') == 'other' ? 'selected' : '' }}>{{__('languages.user_management.other')}}</option>
                            </select>
                            @if($errors->has('gender'))
                                <span class="validation_error">{{ $errors->first('gender') }}</span>
                            @endif
                        </div>
                        <div class="select-lng pt-2 pb-2 col-lg-2 col-md-3">                            
                            <select name="status"  class="form-control select-option">
                                <option value="">{{ __('languages.status') }}</option>
                                <option value="active" {{ request()->get('status') == 'active' ? 'selected' : '' }}>{{__('languages.active')}}</option>
                                <option value="inactive" {{ request()->get('status') == 'inactive' ? 'selected' : '' }}>{{__('languages.inactive')}}</option>
                                <option value="pending" {{ request()->get('status') == 'pending' ? 'selected' : '' }}>{{__('languages.pending')}}</option>
                            </select>
                            @if($errors->has('status'))
                                <span class="validation_error">{{ $errors->first('status') }}</span>
                            @endif
                        </div>
                      
                        <div class="col-lg-2 col-md-3">
                            <div class="select-lng pt-2 pb-2">
                                <button type="submit" name="filter" value="filter" class="btn-search">{{ __('languages.search') }}</button>
                            </div>
                        </div>
                    </div>
                    </form>

                    <div class="row">
                        
                        <div class="select-lng pt-2 pb-2 col-lg-2 col-md-3">                            
                            <select name="grades"  id="grades" class="form-control select-option">
                                <option value="">{{ __('languages.select_grade') }}</option>
                                @if(!empty($Grades))
                                    @foreach($Grades as $grade)
                                    <option value="{{$grade->id}}" >{{ $grade->name}}</option>
                                    @endforeach
                                @endif
                            </select>
                            @if($errors->has('grades'))
                                <span class="validation_error">{{ $errors->first('grades') }}</span>
                            @endif
                        </div>
                            
                    </div>

                    <!-- End Search form Question -->

                    <!-- Question form Listinf Start -->
					<div class="sm-add-user-sec card">
						<div class="select-option-sec pb-2 card-body">
                            @if($errors->has('question_ids'))<span class="validation_error">{{ $errors->first('question_ids') }}</span>@endif
                            <div class="row">
                                <div class="sm-que-list pl-4">
                                    <div class="sm-que">
                                    <input type="checkbox" name="select-all-students" data-studentid="" id="select-all-students" class="checkbox" />
                                        <span class="font-weight-bold pl-2"> {{__('languages.check_all')}}</span><br>
                                    </div>
                                </div>
                            </div>
                            <hr>
							@csrf()
                            @if(!empty($StudentList))
                            @php 
                                $iteration = $StudentList->perPage() * ($StudentList->currentPage() - 1);
                            @endphp
                            @foreach($StudentList as $student)
                            <div class="row">
                                <div class="sm-que-list pl-4">
                                    <div class="sm-que">
                                        <input type="checkbox" name="assign_student_id" id ="assign_student_id" class="checkbox" value="{{$student->id}}"  data-studentid=""/>
                                        <input type="hidden" name="exam_id" value= "{{request()->route('id')}}" />
                                        <span class="font-weight-bold pl-2">{{__('languages.s_id')}} : {{++$iteration}}</span>
                                        <span class="pl-2"><b>{{__('languages.student_name')}} : </b> {{($student->name_en) ? App\Helpers\Helper::decrypt($student->name_en) : $student->name}}</span>
                                    </div>
                                    <div class="sm-answer pl-4 pt-2">
                                        <?php echo $student->question_en; ?>
                                    </div>
                                    <div class="pt5 pl-4">
                                        <div class="row">
                                            <div class="col-lg-4 col-md-4 col-sm-12">
                                                <label for="email">{{__('languages.gender')}} : {{$student->gender}}</label>   
                                            </div>
                                            
                                            <div class="col-lg-4 col-md-4 col-sm-12">
                                                <label for="email">{{__('languages.email')}} : {{$student->email ?? ''}}</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <hr>
                            @endforeach
                            @endif
                            <div>{{__('languages.showing')}} {{!empty($StudentList->firstItem()) ? $StudentList->firstItem() : 0 }} {{__('languages.to')}} {{!empty($StudentList->lastItem()) ? $StudentList->lastItem() : 0}}
								{{__('languages.of')}}  {{$StudentList->total()}} {{__('entries')}}
							</div>
                            <div class="row">
                                        <div class="col-lg-10 col-md-10 ">
                                            @if((app('request')->input('items'))=== null)
                                                {{$StudentList->appends(request()->input())->links()}}
                                            @else
                                                {{$StudentList->appends(compact('items'))->links()}}
                                            @endif 
                                        </div>
                                        <div calss="col-lg-2 col-md-2">
                                            <form>
                                                <label for="pagination" id="per_page">{{__('languages.per_page')}}</label>
                                                <select id="pagination" >
                                                    <option value="10" @if(app('request')->input('items') == 10) selected @endif >10</option>
                                                    <option value="20" @if(app('request')->input('items') == 20) selected @endif >20</option>
                                                    <option value="25" @if(app('request')->input('items') == 25) selected @endif >25</option>
                                                    <option value="30" @if(app('request')->input('items') == 30) selected @endif >30</option>
                                                    <option value="40" @if(app('request')->input('items') == 40) selected @endif >40</option>
                                                    <option value="50" @if(app('request')->input('items') == 50) selected @endif >50</option>
                                                    <option value="{{$StudentList->total()}}" @if(app('request')->input('items') == $StudentList->total()) selected @endif >{{__('languages.all')}}</option>
                                                </select>
                                            </form>
                                        <div>
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
        /*For a get Grade id*/ 
        document.getElementById('grades').onchange = function(){
            var selectAllcheckbox = document.getElementById('select-all-students');
            var selectsinglecheckbox =document.getElementById('assign_student_id');
            var grade_id = document.getElementById('grades').value;
            if(grade_id!=""){
                selectAllcheckbox.setAttribute("data-studentid", grade_id); 
                selectsinglecheckbox.setAttribute("data-studentid",grade_id);
            }
            else{
                toastr.error(VALIDATIONS.PLEASE_SELECT_GRADE);
            }
        }

        /*for pagination add this script added by mukesh mahanto*/ 
        document.getElementById('pagination').onchange = function() {
            window.location = "{!! $StudentList->url(1) !!}&items=" + this.value;			
        }; 
</script>
@include('backend.layouts.footer') 
@endsection