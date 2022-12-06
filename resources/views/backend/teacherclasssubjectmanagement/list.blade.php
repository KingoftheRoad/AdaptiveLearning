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
							<div class="sec-title">
								<h2 class="main-title">{{__('languages.sidebar.teacher_class_assignment')}}</h2>
								<div class="btn-sec">
									<a href="javascript:void(0);" class="btn-back dark-blue-btn btn btn-primary mb-4" id="backButton">{{__('languages.back')}}</a>
									@if (in_array('teacher_class_and_subject_assign_create', $permissions))
										{{-- <a href="{{ route('teacher-class-subject-assign.create') }}" class="dark-blue-btn btn btn-primary">{{__('languages.assign_teacher_to_class')}}</a> --}}
										<a href="{{ route('teacher-class-subject-assign.create') }}" class="dark-blue-btn btn btn-primary mb-4">{{__('languages.assign_teacher_to_class')}}</a>
									@endif
								</div>
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
					<div class="row">
						<div class="col-md-12">
							<div class="question-bank-sec">
								<table id="DataTable" class="display" style="width:100%">
							    	<thead>
							        	<tr>
							          		<th>
										  		<input type="checkbox" name="" class="checkbox">
											</th>
							          		<th><span>@sortablelink('teacher',__('languages.teacher'))</span></th>
							          		<th><span>@sortablelink('class',__('languages.grade'))</span></th>
											<th><span>{{__('languages.class')}}</span></th> 
							          		<!-- <th><span>@sortablelink('subject',__('languages.subject'))</span></th> -->
											<th>@sortablelink('status',__('languages.status'))</th>
											<th>{{__('languages.action')}}</th>
							        	</tr>
							    	</thead>
							    	<tbody class="scroll-pane">
										@if(!empty($List))
										@foreach($List as $data)
							        	<tr>
											<td><input type="checkbox" name="" value="{{$data->id}}" class="checkbox"></td>
											<td>
												@if(!empty($data->getTeacher->name_en))
												{{App\Helpers\Helper::decrypt($data->getTeacher->name_en) ?? 'N/A'}}
												@else
												{{$data->getTeacher->name ?? 'N/A'}}
												@endif
											</td>
											<td>{{ ($data->getClass->name) ?? 'N/A' }}</td>
											<td>{{ ( App\Helpers\Helper::getClassNames($data->class_name_id)) ?? 'N/A'}}</td>
											<!-- <td>{{ $data->getSubjectNameById() }}</td> -->
											<td>
												@if($data->status == 'active')
													<span class="badge badge-success">{{__('languages.active')}}</span> 
												@else
												<span class="badge badge-primary">{{__('languages.inactive')}}</span> 
												@endif
											</td>
											<td class="btn-edit">
											<!-- @if (in_array('teacher_class_and_subject_assign_update', $permissions))
												<a href="{{ route('teacher-class-subject-assign.edit', $data->id) }}" class="" title="{{__('languages.edit')}}"><i class="fa fa-pencil" aria-hidden="true"></i></a>
											@endif -->
											@if (in_array('teacher_class_and_subject_assign_delete', $permissions))
												<a href="javascript:void(0);" class="pl-2" id="deleteTeacherClassSubject" data-id="{{$data->id}}" title="{{__('languages.delete')}}"><i class="fa fa-trash" aria-hidden="true"></i></a>
											@endif
											</td>
										</tr>
										@endforeach
										@endif
							  	</tbody>
							</table>
							<div>{{__('languages.showing')}} {{!empty($List->firstItem()) ? $List->firstItem() : 0}} {{__('languages.to')}} {{!empty($List->lastItem()) ? $List->lastItem() : 0}}
								{{__('languages.of')}}  {{$List->total()}} {{__('languages.entries')}}
							</div>
								<div class="pagination-data">
									<div class="col-lg-9 col-md-9 pagintn">
										@if((app('request')->input('items'))=== null)
											{{$List->appends(request()->input())->links()}}
										@else
											{{$List->appends(compact('items'))->links()}}
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
												<option value="{{$List->total()}}" @if(app('request')->input('items') == $List->total()) selected @endif >{{__('languages.all')}}</option>
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
		if( (TotalFilterData > 0 && TotalFilterData < 11)){
				document.getElementById("pagination").style.visibility = "hidden";
				document.getElementById("per_page").style.visibility = "hidden";
		}
		/*for pagination add this script added by mukesh mahanto*/ 
		document.getElementById('pagination').onchange = function() {
				window.location = "{!! $List->url(1) !!}&items=" + this.value;	
		}; 
		</script>
		@include('backend.layouts.footer')
@endsection