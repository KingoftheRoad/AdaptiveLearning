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
								<h2 class="mb-4 main-title">{{__('languages.module_management.module_detail')}}</h2>
								<div class="btn-sec">
								@if (in_array('modules_management_create', $permissions))
									<a href="javascript:void(0);" class="btn-back dark-blue-btn btn btn-primary mb-4" id="backButton">{{__('languages.back')}}</a>
									<a href="{{ route('modulesmanagement.create') }}" class="dark-blue-btn btn btn-primary mb-4">{{__('languages.module_management.add_new_module')}}</a>
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
										<th><input type="checkbox" name="" class="checkbox" id="group-module-ids"></th>
                                        <th class="first-head"><span>@sortablelink('module_name',__('languages.module_management.module_name'))</span></th>
                                        <th class="first-head"><span>@sortablelink('created_at',__('languages.created_at'))</span></th>
                                        <th class="first-head"><span>@sortablelink('status',__('languages.status'))</span></th>
                                        <th>{{__('languages.action')}}</th>
                                    </tr>
                                </thead>
                                <tbody class="scroll-pane">
                                @if(!empty($ModuleList))
								@foreach($ModuleList as $module)
                                    <tr>
										<td><input type="checkbox" name="examids" class="checkbox module-id" value="{{$module->id}}"></td>
                                        <td>{{$module->module_name}}</td>
                                        <td>{{($module->created_at) ? str_replace('-','/',date('d-m-Y', strtotime($module->created_at))) : __('languages.not_available')}}</td>
                                        <td>
											@if($module->status == 'active')
												<span class="badge badge-success">{{__('languages.active')}}</span>
											@else
												<span class="badge badge-danger">{{__('languages.inactive')}}</span>
											@endif
										</td>
                                        <td class="btn-edit">
											@if (in_array('modules_management_update', $permissions))
													<a href="{{ route('modulesmanagement.edit', $module->id) }}" class="" title="{{__('languages.edit')}}"><i class="fa fa-pencil" aria-hidden="true"></i></a>
											@endif
											@if (in_array('modules_management_delete', $permissions))
													<a href="javascript:void(0);" class="pl-2" id="deleteModule" data-id="{{$module->id}}" title="{{__('languages.delete')}}"><i class="fa fa-trash" aria-hidden="true"></i></a>
											@endif
										</td>
                                    </tr>
                                @endforeach
                                @endif
                                </tbody>
							</table>
                            <div>{{__('languages.showing')}} {{!empty($ModuleList->firstItem()) ? $ModuleList->firstItem() : 0}} {{__('languages.to')}} {{!empty($ModuleList->lastItem()) ? $ModuleList->lastItem() : 0}}
								{{__('languages.of')}}  {{$ModuleList->total()}} {{__('languages.entries')}}
							</div>
								<div class="pagination-data">
									<div class="col-lg-9 col-md-9 pagintn">
										@if((app('request')->input('items'))=== null)
											{{$ModuleList->appends(request()->input())->links()}}
										@else
											{{$ModuleList->appends(compact('items'))->links()}}
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
												<option value="{{$ModuleList->total()}}" @if(app('request')->input('items') == $ModuleList->total()) selected @endif >{{__('languages.all')}}</option>
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
					// window.location = window.location.href + "&items=" + this.value;			
					window.location = "{!! $ModuleList->url(1) !!}&items=" + this.value;
				}; 
		</script>
		@include('backend.layouts.footer')
@endsection