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
								<h2 class="mb-4 main-title">{{__('languages.principal_management.principal_details')}}</h2>
								<div class="btn-sec">
									<a href="javascript:void(0);" class="btn-back dark-blue-btn btn btn-primary mb-4" id="backButton">{{__('languages.back')}}</a>
								@if (in_array('principal_management_create', $permissions))
									<a href="{{ route('principal.create') }}" class="dark-blue-btn btn btn-primary mb-4">{{__('languages.principal_management.add_new_principal')}}</a>
								@endif
									{{-- <a href="{{ route('users.import') }}" class="dark-blue-btn btn btn-primary mb-4">{{__('languages.user_management.import_users')}}</a> --}}
									{{-- <a href="{{ route('users.export') }}" class="dark-blue-btn btn btn-primary mb-4">{{__('languages.user_management.export_users')}}</a> --}}
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
					<form class="addPrincipalFilterForm" id="addPrincipalFilterForm" method="get">	
						<div class="row">
							
							<div class="col-lg-3 col-md-3">
								<div class="select-lng pt-2 pb-2">
									<input type="text" class="input-search-box mr-2" name="principalname" value="{{request()->get('principalname')}}" placeholder="{{__('languages.search_by_principal_name')}}">
									@if($errors->has('principalname'))
										<span class="validation_error">{{ $errors->first('principalname') }}</span>
									@endif
								</div>
							</div>

							<div class="col-lg-2 col-md-3">
								<div class="select-lng pt-2 pb-2">
									<input type="text" class="input-search-box mr-2" name="email" value="{{request()->get('email')}}" placeholder="{{__('languages.search_by_email')}}">
									@if($errors->has('email'))
										<span class="validation_error">{{ $errors->first('email') }}</span>
									@endif
								</div>
							</div>
							
							<div class="select-lng pt-2 pb-2 col-lg-2 col-md-4">                            
								<select name="status" class="form-control select-option" id="status">
									<option value="">{{__('languages.select_status')}}</option>
									<option value="active" {{ request()->get('status') == "active" ? 'selected' : '' }}>{{__("languages.active")}}</option>
									<option value="pending" {{ request()->get('status') == "pending" ? 'selected' : '' }}>{{__("languages.pending")}}</option>
									<option value="inactive" {{ request()->get('status') == "inactive" ? 'selected' : '' }}>{{__("languages.inactive")}}</option>
								</select>
							</div>
							<div class="col-lg-2 col-md-3">
								<div class="select-lng pt-2 pb-2">
									<button type="submit" name="filter" value="filter" class="btn-search">{{ __('languages.search') }}</button>
								</div>
							</div>
						</div>
					</form>

					<div class="row">
						<div class="col-md-12">
							<div class="question-bank-sec">
								<table id="DataTable" class="display" style="width:100%">
							    	<thead>
							        	<tr>
							          		<th>
										  		<input type="checkbox" name="" class="checkbox">
											</th>
											<th class="first-head"><span>@sortablelink('role_id',__('languages.role'))</span></th>
							          		<th class="first-head"><span>@sortablelink('name_en',__('languages.name_english'))</span></th>
											<th class="first-head"><span>@sortablelink('name_ch',__('languages.name_chinese'))</span></th>
											<th class="sec-head selec-opt"><span>@sortablelink('email',__('languages.email'))</span></th>
											<th class="selec-head">@sortablelink('status',__('languages.status'))</th>
											<th class="selec-head">{{__('languages.action')}}</th>
							        	</tr>
							    	</thead>
							    	<tbody class="scroll-pane">
										@if(!empty($principalData))
										@foreach($principalData as $User)
							        	<tr>
											<td><input type="checkbox" name="" class="checkbox"></td>
											<td>{{($User->roles->role_name) ? ($User->roles->role_name) : 'N/A'}}</td>
											<td>{{ ($User->name_en) ? App\Helpers\Helper::decrypt($User->name_en) : $User->name}}</td>
											<td>{{ ($User->name_ch) ? App\Helpers\Helper::decrypt($User->name_ch) : 'N/A' }}</td>
											<td>{{$User->email }}</td>
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
												@if (in_array('principal_management_update', $permissions))
													<a href="{{ route('principal.edit', $User->id) }}" class="" title="{{__('languages.edit')}}"><i class="fa fa-pencil" aria-hidden="true"></i></a>
												@endif
												@if (in_array('principal_management_delete', $permissions))
													<a href="javascript:void(0);" class="pl-2" id="deletePrincipal" data-id="{{$User->id}}" title="{{__('languages.delete')}}"><i class="fa fa-trash" aria-hidden="true"></i></a>
												@endif
												@if(Auth::user()->role_id == 5)
													@if (in_array('change_password_update', $permissions))
														<a href="javascript:void(0);" class="pl-2 changeUserPassword" data-id="{{$User->id}}" title="{{__('languages.change_password')}}"><i class="fa fa-unlock" aria-hidden="true"></i></a>
													@endif
												@endif
											</td>
										</tr>
										@endforeach
										@endif
							  </tbody>
							</table>
							<div>{{__('languages.showing')}} {{!empty($principalData->firstItem()) ? $principalData->firstItem() : 0}} {{__('languages.to')}} {{!empty($principalData->lastItem()) ? $principalData->lastItem() : 0}}
								{{__('languages.of')}}  {{$principalData->total()}} {{__('languages.entries')}}
							</div>
								<div class="pagination-data">
									<div class="col-lg-9 col-md-9 pagintn">
										@if((app('request')->input('items'))=== null)
											{{$principalData->appends(request()->input())->links()}}
										@else
											{{$principalData->appends(compact('items'))->links()}}
										@endif 
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
												<option value="{{$principalData->total()}}" @if(app('request')->input('items') == $principalData->total()) selected @endif >{{__('languages.all')}}</option>
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
						window.location = "{!! $principalData->url(1) !!}&items=" + this.value;	
				}; 
		</script>

		@include('backend.layouts.footer')
@endsection