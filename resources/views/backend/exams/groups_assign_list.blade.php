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
								<h4 class="mb-4">{{__('languages.group_list')}}</h4>
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

                    <!-- Start Student Group List -->
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
                                    <input type="checkbox" name="select-all-student" id="select-all-groups-exams" class="checkbox" data-examid="{{$ExamData['id'] ?? ''}}" {{$checked}}/>
                                        <span class="font-weight-bold pl-2"> {{__('languages.check_all')}}</span><br>
                                    </div>
                                </div>
                            </div>
                            <hr>
                            @if(!empty($Groups))
                                @foreach($Groups as $group)
                                    @php
                                    $assignedGroups = [];
                                    if(!empty($ExamData)){
                                        $assignedGroups = explode(',', $ExamData->group_ids);
                                    }
                                    @endphp
                                    <div class="row">
                                        <div class="sm-que-list pl-4">
                                            <div class="sm-que">
                                                <input type="checkbox" name="group_ids" class="checkbox exams-assign-group-ids" value="{{$group->id}}" data-examid="{{$ExamData->id}}" @if(in_array($group->id,$assignedGroups)) checked @endif/>
                                                <span class="font-weight-bold pl-2">{{$group->name ?? ''}}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <hr>
                                @endforeach
                            @endif
                            <div>{{__('languages.showing')}} {{!empty($Groups->firstItem()) ? $Groups->firstItem() : 0 }} {{__('languages.to')}} {{!empty($Groups->lastItem()) ? $Groups->lastItem() : 0}}
								{{__('languages.of')}}  {{$Groups->total()}} {{__('languages.entries')}}
							</div>
                            <div class="row">
                                <div class="col-lg-10 col-md-10 ">
                                    @if((app('request')->input('items'))=== null)
                                        {{$Groups->appends(request()->input())->links()}}
                                    @else
                                        {{$Groups->appends(compact('items'))->links()}}
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
                                            <option value="{{$Groups->total()}}" @if(app('request')->input('items') == $Groups->total()) selected @endif >{{__('languages.all')}}</option>
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
    /*for pagination add this script added by mukesh mahanto*/ 
    document.getElementById('pagination').onchange = function() {
        window.location = "{!! $Groups->url(1) !!}&items=" + this.value;			
    }; 
</script>
@include('backend.layouts.footer')
@endsection