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
								<h2 class="mb-4 main-title">{{__('languages.school_management.school_detail')}}</h2>
								<div class="btn-sec">
									<a href="javascript:void(0);" class="btn-back dark-blue-btn btn btn-primary mb-4" id="backButton">{{__('languages.back')}}</a>
								@if (in_array('school_management_create', $permissions))
									<a href="{{ route('schoolmanagement.create') }}" class="dark-blue-btn btn btn-primary mb-4">{{__('languages.school_management.add_new_school')}}</a>
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
					<form class="addUserFilterForm" id="addUserFilterForm" method="get">	
					<div class="row">
 						<div class="col-lg-2 col-md-4">
                            <div class="select-lng pt-2 pb-2">
                                <input type="text" class="input-search-box mr-2" name="SchoolName" value="{{request()->get('SchoolName')}}" placeholder="{{__('languages.search_by_school_name')}}">
								@if($errors->has('SchoolName'))
                                	<span class="validation_error">{{ $errors->first('SchoolName') }}</span>
                            	@endif
                            </div>
                        </div>

						<div class="col-lg-2 col-md-4">
                            <div class="select-lng pt-2 pb-2">
                                <input type="text" class="input-search-box mr-2" name="SchoolCode" value="{{request()->get('SchoolCode')}}" placeholder="{{__('languages.search_by_school_code')}}">
								@if($errors->has('SchoolCode'))
                                	<span class="validation_error">{{ $errors->first('SchoolCode') }}</span>
                            	@endif
                            </div>
                        </div>

						<div class="col-lg-2 col-md-4">
                            <div class="select-lng pt-2 pb-2">
                                <input type="text" class="input-search-box mr-2" name="SchoolCity" value="{{request()->get('SchoolCity')}}" placeholder="{{__('languages.search_by_school_city')}}">
								@if($errors->has('SchoolCity'))
                                	<span class="validation_error">{{ $errors->first('SchoolCity') }}</span>
                            	@endif
                            </div>
                        </div>

						<div class="col-lg-2 col-md-4">
                            <div class="select-lng pt-2 pb-2">
								<select class="selectpicker form-control" data-show-subtext="true" data-live-search="true" name="Status" id="Status">
									<option value=''>{{ __('languages.select_status') }}</option>
									@if(!empty($statusList))
										@foreach($statusList as $status)
										<option value="{{$status['id']}}" {{ request()->get('Status') == $status['id'] ? 'selected' : '' }}>{{ $status['name']}}</option>
										@endforeach
                                	@endif
								</select>
								@if($errors->has('Status'))
                                	<span class="validation_error">{{ $errors->first('Status') }}</span>
                            	@endif
                            </div>
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
							<div  class="question-bank-sec">
								<table class="display" style="width:100%">
							    	<thead>
							        	<tr>
							          		<th>
										  		<input type="checkbox" name="" class="checkbox">
											</th>
											<th class="first-head"><span>@sortablelink('school_name',__('languages.school_management.school_name'))</span></th>
							          		<th class="first-head"><span>@sortablelink('school_code',__('languages.school_management.school_code'))</span></th>
											<th class="sec-head selec-opt"><span>{{__('languages.school_management.address_en')}}</span></th>
											<th class="sec-head selec-opt"><span>{{__('languages.school_management.address_ch')}}</span></th>
                                            <th class="selec-opt"><span>{{__('languages.school_management.city')}}</span></th>
                                            <th>@sortablelink('status',__('languages.status'))</th>
											<th>{{__('languages.action')}}</th>
							        	</tr>
							    	</thead>
							    	<tbody class="scroll-pane">
                                    @if(!empty($schoolList))
										@foreach($schoolList as $school)
							        	<tr>
											<td><input type="checkbox" name="" class="checkbox"></td>
											<td>{{ ($school->school_name_en) ? App\Helpers\Helper::decrypt($school->school_name_en) : $school->school_name}}</td>
											<td>{{ ($school->school_code) ? $school->school_code : '' }}</td>
                                            <td>{{ ($school->school_address_en) ? App\Helpers\Helper::decrypt($school->school_address_en) : 'N/A' }}</td>
											<td>{{ ($school->school_address_ch) ? App\Helpers\Helper::decrypt($school->school_address_ch) : 'N/A' }}</td>
                                            <td>{{ ($school->city) ? App\Helpers\Helper::decrypt($school->city) : 'N/A' }}</td>
                                            <td>
												@if($school->status=="active")
													<span class="badge badge-success">{{__('languages.active')}}</span>
												@else
													<span class="badge badge-danger">{{__('languages.inactive')}}</span>
												@endif
											</td>
											<td class="btn-edit">
											@if (in_array('school_management_update', $permissions))
												<a href="{{ route('schoolmanagement.edit', $school->id) }}" class="" title="{{__('languages.edit')}}"><i class="fa fa-pencil" aria-hidden="true"></i></a>
											@endif
											@if (in_array('school_management_delete', $permissions))
												<a href="javascript:void(0);" class="pl-2" id="deleteSchool" data-id="{{$school->id}}" title="{{__('languages.delete')}}"><i class="fa fa-trash" aria-hidden="true"></i></a>
											@endif
											</td>
										</tr>
										@endforeach
										@endif
							        </tbody>
							</table>
								<div>{{__('languages.showing')}} {{!empty($schoolList->firstItem()) ? $schoolList->firstItem() : 0}} {{__('languages.to')}} {{!empty($schoolList->lastItem()) ? $schoolList->lastItem() : 0}}
									{{__('languages.of')}}  {{$schoolList->total()}} {{__('languages.entries')}}
								</div>
								<div class="pagination-data">
									<div class="col-lg-9 col-md-9 pagintn">
										{{$schoolList->appends(request()->input())->links()}}
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
												<option value="{{$schoolList->total()}}" @if(app('request')->input('items') == $schoolList->total()) selected @endif >{{__('languages.all')}}</option>
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
					window.location = "{!! $schoolList->url(1) !!}&items=" + this.value;	
			}; 
		</script>
		@include('backend.layouts.footer')
@endsection