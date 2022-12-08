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
								<h2 class="mb-4 main-title">{{__('languages.user_management.user_detail')}}</h2>
								<div class="btn-sec">
									<a href="javascript:void(0);" class="btn-back dark-blue-btn btn btn-primary mb-4" id="backButton">{{__('languages.back')}}</a>
								@if (in_array('user_management_create', $permissions))
									<a href="{{ route('users.create') }}" class="dark-blue-btn btn btn-primary mb-4">{{__('languages.user_management.add_new_user')}}</a>
								@endif
									<a href="{{ route('users.import') }}" class="dark-blue-btn btn btn-primary mb-4">{{__('languages.user_management.import_users')}}</a>
									<a href="{{ route('users.export') }}" class="dark-blue-btn btn btn-primary mb-4">{{__('languages.user_management.export_users')}}</a>
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
					<form class="addUserFilterForm" id="addUserFilterForm" method="get">	
					<div class="row">
					<div class="select-lng pt-2 pb-2 col-lg-2 col-md-4">                            
                            <select name="school_id"  class="form-control select-option selectpicker"  data-show-subtext="true" data-live-search="true" id="user_filter_school">
                                <option value="">{{ __('languages.user_management.school') }}</option>
                                @if(!empty($schoolList))
                                    @foreach($schoolList as $school)
                                    <option value="{{$school->id}}" {{ request()->get('school_id') == $school['id'] ? 'selected' : '' }}>
                                    	@if(app()->getLocale()=='ch')
                                            {{ ucfirst($school->DecryptSchoolNameCh) }}
                                        @else
                                            {{ ucfirst($school->DecryptSchoolNameEn) }}
                                        @endif
                                    </option>
                                    @endforeach
                                @endif
                            </select>
                            @if($errors->has('school_id'))
                                <span class="validation_error">{{ $errors->first('school_id') }}</span>
                            @endif
                        </div>

						<div class="col-lg-2 col-md-4">
                            <div class="select-lng pt-2 pb-2">
								<select class="selectpicker form-control" data-show-subtext="true" data-live-search="true" name="Role" id="user_filter_role">
									<option value=''>{{ __('languages.user_management.select_role') }}</option>
									@if(!empty($roleList))
										@foreach($roleList as $role)
										<option value="{{$role['id']}}" {{ request()->get('Role') == $role['id'] ? 'selected' : '' }}>{{ $role['role_name']}}</option>
										@endforeach
                                	@endif
								</select>
								@if($errors->has('Role'))
                                	<span class="validation_error">{{ $errors->first('Role') }}</span>
                            	@endif
                            </div>
                        </div>

                        <div class="select-lng pt-2 pb-2 col-lg-2 col-md-4">                            
                            <select name="grade_id"  class="form-control select-option selectpicker" data-show-subtext="true" data-live-search="true" id="user_filter_grade">
                                <option value="">{{ __('languages.user_management.grade') }}</option>
                                @if(!empty($gradeList))
                                    @foreach($gradeList as $grade)
                                    <option value="{{$grade->id}}" {{ request()->get('grade_id') == $grade->id ? 'selected' : '' }}>{{ $grade->name}}</option>
                                    @endforeach
                                @endif
                            </select>
                            @if($errors->has('grade_id'))
                                <span class="validation_error">{{ $errors->first('grade_id') }}</span>
                            @endif
                        </div>
						<div class="col-lg-2 col-md-3">
                            <div class="select-lng pt-2 pb-2">
                                <input type="text" class="input-search-box mr-2" name="username" value="{{request()->get('username')}}" placeholder="{{__('languages.user_management.search_by_username')}}">
								@if($errors->has('username'))
                                	<span class="validation_error">{{ $errors->first('username') }}</span>
                            	@endif
                            </div>
                        </div>
						<div class="col-lg-2 col-md-3">
                            <div class="select-lng pt-2 pb-2">
                                <input type="text" class="input-search-box mr-2" name="email" value="{{request()->get('email')}}" placeholder="{{__('languages.user_management.search_by_email')}}">
								@if($errors->has('email'))
                                	<span class="validation_error">{{ $errors->first('email') }}</span>
                            	@endif
                            </div>
                        </div>
                        <div class="col-lg-2 col-md-3">
                            <div class="select-lng pt-2 pb-2">
                                <button type="submit" name="filter" value="filter" class="btn-search">{{ __('languages.user_management.search') }}</button>
                            </div>
                        </div>
                    </div>
				</form>
					<div class="row">
						<div class="col-md-12">
							<div class="question-bank-sec">
								<table id="DataTable" class="display table-responsive" style="width:100%">
							    	<thead>
							        	<tr>
							          		<th>
										  		<input type="checkbox" name="" class="checkbox">
											</th>
											<th class="first-head"><span>@sortablelink('role_id',__('languages.role'))</span></th>
							          		<th class="first-head"><span>@sortablelink('name_en',__('languages.name_english'))</span></th>
											<th class="first-head"><span>@sortablelink('name_ch',__('languages.name_chinese'))</span></th>
											<th class="sec-head selec-opt"><span>@sortablelink('email',__('languages.email'))</span></th>
											<th class="selec-head">@sortablelink('grade_id',__('languages.grade'))</th>
											<th class="selec-head">@sortablelink('status',__('languages.status'))</th>
											<th class="selec-head">{{__('languages.action')}}</th>
							        	</tr>
							    	</thead>
							    	<tbody class="scroll-pane">
										@if(!empty($UsersList))
										@foreach($UsersList as $User)
							        	<tr>
											<td><input type="checkbox" name="" class="checkbox"></td>
											<td>{{($User->roles->role_name) ? ($User->roles->role_name) : 'N/A'}}</td>
											<td>{{($User->name_en) ? App\Helpers\Helper::decrypt($User->name_en) : $User->name}}</td>
											<td>{{($User->name_ch) ? App\Helpers\Helper::decrypt($User->name_ch) : 'N/A' }}</td>
											<td>{{$User->email }}</td>
											<td>{{$User->grades->name ?? 'N/A'}}</td>
											<td>
												@if($User->status === 'pending')
													<span class="badge badge-warning">{{__('languages.pending')}}</span>
												@elseif($User->status == 'active')
													<span class="badge badge-success">{{__('languages.active')}}</span> 
												@else
													<span class="badge badge-primary">{{__('languages.inactive')}}</span> 
												@endif
											</td>
											<td class="btn-edit">
												@if (in_array('user_management_update', $permissions))
													<a href="{{ route('users.edit', $User->id) }}" class="" title="{{__('languages.edit')}}"><i class="fa fa-pencil" aria-hidden="true"></i></a>
												@endif
												@if (in_array('user_management_delete', $permissions))
													<a href="javascript:void(0);" class="pl-2" id="deleteUser" data-id="{{$User->id}}" title="{{__('languages.delete')}}"><i class="fa fa-trash" aria-hidden="true"></i></a>
												@endif
												@if(Auth::user()->role_id == 1 || Auth::user()->role_id == 5)
													@if (in_array('change_password_update', $permissions))
													<a href="javascript:void(0);" class="pl-2 changeUserPassword" data-id="{{$User->id}}" title="{{__('languages.change_password')}}"><i class="fa fa-unlock" aria-hidden="true"></i></a>
													@endif
													@if($User->role_id == 3)
													{{-- <a href="{{route('student.student-profiles',$User->id)}}" class="pl-2" title="{{__('languages.sidebar.profile')}}"><i class="fa fa-user" aria-hidden="true"></i></a> --}}
													<a href="{{route('student-profiles',$User->id)}}" class="pl-2" title="{{__('languages.sidebar.profile')}}"><i class="fa fa-user" aria-hidden="true"></i></a>
													@endif
												@endif
												
											</td>
										</tr>
										@endforeach
										@endif
							  </tbody>
							</table>
							<div>{{__('languages.showing')}} {{!empty($UsersList->firstItem()) ? $UsersList->firstItem() : 0}} {{__('languages.to')}} {{!empty($UsersList->lastItem()) ? $UsersList->lastItem() : 0}}
								{{__('languages.of')}}  {{$UsersList->total()}} {{__('languages.entries')}}
							</div>
								<div class="pagination-data">
									<div class="col-lg-9 col-md-9 pagintn">
										{{$UsersList->appends(request()->input())->links()}}
									</div>
									<div class="col-lg-3 col-md-3 pagintns">
										<form>
											<label for="pagination" id="per_page">{{__('languages.user_management.per_page')}}</label>
											<select id="pagination" >
												<option value="10" @if(app('request')->input('items') == 10) selected @endif >10</option>
												<option value="20" @if(app('request')->input('items') == 20) selected @endif >20</option>
												<option value="25" @if(app('request')->input('items') == 25) selected @endif >25</option>
												<option value="30" @if(app('request')->input('items') == 30) selected @endif >30</option>
												<option value="40" @if(app('request')->input('items') == 40) selected @endif >40</option>
												<option value="50" @if(app('request')->input('items') == 50) selected @endif >50</option>
												<option value="{{$UsersList->total()}}" @if(app('request')->input('items') == $UsersList->total()) selected @endif >{{__('languages.all')}}</option>
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
			/*for pagination add this script added by mukesh mahanto*/ 
			document.getElementById('pagination').onchange = function() {
					window.location = "{!! $UsersList->url(1) !!}&items=" + this.value;	
			}; 
		</script>

		@include('backend.layouts.footer')
@endsection