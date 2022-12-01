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
								<h2 class="mb-4 main-title">{{__('languages.group_management.group_detail')}}</h2>
								<div class="btn-sec">
								@if (in_array('group_management_create', $permissions))
									<button class="dark-blue-btn btn btn-primary mb-4" id="add-group">{{__('languages.group_management.create_new_group')}}</button>
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
								<table>
							    	<thead>
							        	<tr>
							          		<th>
										  		<input type="checkbox" name="" class="checkbox">
											</th>
											<th class="first-head">
												<span class="sec-head">@sortablelink('name', __('languages.group_management.group_name'))</span>
											</th>
											<!-- <th>Exams</th> -->
											<th>{{__('languages.action')}}</th>
							        	</tr>
							    	</thead>
							    	<tbody class="scroll-pane">
										@if(!empty($GroupList))
										@foreach($GroupList as $Group)
							        	<tr>
											<td><input type="checkbox" name="" class="checkbox"></td>
											<td>{{ $Group->name }}</td>
											<!-- <td>@php echo \App\Helpers\Helper::getExamNames($Group->exam_ids); @endphp</td> -->
											<td class="btn-edit">
											@if (in_array('group_management_update', $permissions))
												<a href="javascript:void(0);" class="edit-group-btn" data-id="{{$Group->id}}" title="Edit Group">
													<i class="fa fa-pencil" aria-hidden="true"></i>
												</a>
												<a href="{{ route('studentgroup.create.student',$Group->id) }}" class="" data-id="{{$Group->id}}" title="Add / Remove Student Current Group">
													<i class="fa fa-users" aria-hidden="true"></i>
												</a>
											@endif
											@if (in_array('group_management_delete', $permissions))
												<a href="javascript:void(0);" class="pl-2" id="deleteStudentGroup" data-id="{{$Group->id}}">
													<i class="fa fa-trash" aria-hidden="true"></i>
												</a>
											@endif
											</td>
										</tr>
										@endforeach
										@endif
							  </tbody>
							</table>
							<div>{{__('languages.showing')}} {{!empty($GroupList->firstItem()) ? $GroupList->firstItem() : 0}} {{__('languages.to')}} {{!empty($GroupList->lastItem()) ? $GroupList->lastItem() : 0}} 
								{{__('languages.of')}}  {{$GroupList->total()}} {{__('languages.entries')}} </div>
								<div class="pagination-data">
									<div class="col-lg-9 col-md-9 pagintn">
										@if((app('request')->input('items'))=== null)
											{{$GroupList->appends(request()->input())->links()}}
										@else
											{{$GroupList->appends(compact('items'))->links()}}
										@endif 
									</div>
									<div class="col-lg-3 col-md-3 pagintns">
										<form>
											<label for="pagination">{{__('languages.per_page')}}</label>
											<select id="pagination" >
												<option value="10" @if(app('request')->input('items') == 10) selected @endif >10</option>
												<option value="20" @if(app('request')->input('items') == 20) selected @endif >20</option>
												<option value="25" @if(app('request')->input('items') == 25) selected @endif >25</option>
												<option value="30" @if(app('request')->input('items') == 30) selected @endif >30</option>
												<option value="40" @if(app('request')->input('items') == 40) selected @endif >40</option>
												<option value="50" @if(app('request')->input('items') == 50) selected @endif >50</option>
												<option value="{{$GroupList->total()}}" @if(app('request')->input('items') == $GroupList->total()) selected @endif >{{__('languages.all')}}</option>
											</select>
										</form>
									</div>
								</div>
								<div id="table_box_bootstrap">
									<div class="table-export-table">
										<div class="export-table setting-table">
											<i class="fa fa-download"></i>
											<p>Exported Selected</p>
										</div>
										<div class="configure-table setting-table">
											<i class="fa fa-cog"></i>
											<p>Exported Selected</p>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
	      </div>
		</div>
		@include('backend.layouts.footer')
		<!-- Create a new student group modal-->
		<div class="modal fade" id="create-student-group" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static">
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-header">
						<h4 class="modal-title" id="myModalLabel">{{__('languages.group_management.create_student_group')}}</h4>
						<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					</div>
					<form name="create-student-group" id="AddStudentGroupForm">
					@csrf()
					<div class="modal-body">
						<div class="form-row">
							<div class="form-group col-md-12 mb-50">
								<label class="text-bold-600">{{__('languages.group_management.group_name')}}</label>
								<input type="text" class="form-control" name="name" placeholder="{{__('languages.group_management.enter_group_name')}}" value="">
							</div>
						</div>

						<div class="form-row">
							<div class="form-group col-md-12">
								<label for="users-list-role">{{__('languages.group_management.grade')}}</label>
								@if($Grades)
								<select class="selectpicker form-control" name="grade_id" id='add_student_group_grade'>
									<option value="">{{__('languages.group_management.select_grade')}}</option>
									@foreach($Grades as $grade)
									<option value="{{$grade->id}}">{{$grade->name}}</option>
									@endforeach
								</select>
								@endif
							</div>
						</div>

						<div class="form-row">
							<div class="form-group col-md-12">
								<label for="users-list-role">{{__('languages.group_management.status')}}</label>
								<select class="selectpicker form-control" name="status" id='add_student_group_status'>
									<option value="">{{__('languages.group_management.select_status')}}</option>
									<option value="1" selected>{{__('languages.group_management.active')}}</option>
									<option value="0">{{__('languages.group_management.inactive')}}</option>
								</select>
							</div>
						</div>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-default" data-dismiss="modal">{{__('languages.group_management.close')}}</button>
						<button type="submit" class="btn btn-primary">{{__('languages.group_management.submit')}}</button>
					</div>
					</form>
				</div>
			</div>
		</div>
		<!-- End Create a new student group modal-->

		<!-- Update a new student group modal-->
		<div class="modal fade" id="update-student-group" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static">
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-header">
						<h4 class="modal-title" id="myModalLabel">{{__('languages.group_management.update_student_group')}}</h4>
						<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					</div>
					<form name="update-student-group" id="UpdateStudentGroupForm">
					<input type="hidden" name="group_id" id="group_id" value="">
					@csrf()
					<div class="modal-body">
						<div class="form-row">
							<div class="form-group col-md-12 mb-50">
								<label class="text-bold-600">{{__('languages.group_management.group_name')}}</label>
								<input type="text" class="form-control" name="name" id="group-name" placeholder="{{__('languages.group_management.enter_group_name')}}" value="">
							</div>
						</div>
						<div class="form-row">
							<div class="form-group col-md-12">
								<label for="users-list-role">{{__('languages.group_management.grade')}}</label>
								@if($Grades)
								<select class="selectpicker form-control" name="grade_id" id="grade_id">
									<option value="">{{__('languages.group_management.select_grade')}}</option>
									@foreach($Grades as $grade)
									<option value="{{$grade->id}}">{{$grade->name}}</option>
									@endforeach
								</select>
								@endif
							</div>
						</div>
						<div class="form-row">
							<div class="form-group col-md-12">
								<label for="users-list-role">{{__('languages.group_management.status')}}</label>
								<select class="selectpicker form-control" name="status" id="status">
									<option value="">{{__('languages.group_management.select_status')}}</option>
									<option value="1" selected>{{__('languages.group_management.active')}}</option>
									<option value="0">{{__('languages.group_management.inactive')}}</option>
								</select>
							</div>
						</div>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-default" data-dismiss="modal">{{__('languages.group_management.close')}}</button>
						<button type="submit" class="btn btn-primary">{{__('languages.group_management.submit')}}</button>
					</div>
					</form>
				</div>
			</div>
		</div>
		<!-- End Create a new student group modal-->
		<script>
				/*for pagination add this script added by mukesh mahanto*/ 
				document.getElementById('pagination').onchange = function() {
						window.location = "{!! $GroupList->url(1) !!}&items=" + this.value;	
				}; 
		</script>
@endsection