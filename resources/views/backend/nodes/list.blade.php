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
								<h2 class="mb-4 main-title">{{__('languages.nodes.node_detail')}}</h2>
								<div class="btn-sec">
									<a href="javascript:void(0);" class="btn-back dark-blue-btn btn btn-primary mb-4" id="backButton">{{__('languages.back')}}</a>
								@if (in_array('node_management_create', $permissions))
									<a href="{{ route('nodes.create') }}" class="dark-blue-btn btn btn-primary mb-4">{{__('languages.nodes.add_new_node')}}</a>
								@endif
								<a href="{{ route('nodes.tree-view-list') }}" class="dark-blue-btn btn btn-primary mb-4">{{__('languages.nodes.tree_view')}}</a>
								<a href="{{ route('nodes.index') }}" class="dark-blue-btn btn btn-primary mb-4">{{__('languages.nodes.list_view')}}</a>
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
				 	<form class="addNodeFilterForm" id="addNodeFilterForm" method="get">	
					<div class="row">
 						<div class="col-lg-4 col-md-4">
                            <div class="select-lng pt-2 pb-2">
                                <input type="text" class="input-search-box mr-2" name="Search" value="{{request()->get('Search')}}" placeholder="{{__('languages.search_by_node_id')}} | {{__('languages.node_title')}} | {{__('languages.description')}}">
								@if($errors->has('Search'))
                                	<span class="validation_error">{{ $errors->first('Search') }}</span>
                            	@endif
                            </div>
                        </div>
						<div class="col-lg-3 col-md-3">
                            <div class="select-lng pt-2 pb-2">
                                <input type="text" class="input-search-box mr-2" name="SearchParentNode" value="{{request()->get('SearchParentNode')}}" placeholder="{{__('languages.nodes.parent_node_name')}}">
								@if($errors->has('SearchParentNode'))
                                	<span class="validation_error">{{ $errors->first('SearchParentNode') }}</span>
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
								<table class="display table-responsive" style="width:100%">
							    	<thead>
							        	<tr>
							          		<th>
										  		<input type="checkbox" name="" class="checkbox">
											</th>
                                            <th class="first-head"><span>@sortablelink('node_id',__('languages.nodes.node_id'))</span></th>
                                            <th class="first-head"><span>{{__('languages.nodes.parent_node_name')}}</span></th>
											<th class="first-head"><span>@sortablelink('node_title_en',__('languages.nodes.node_title_english'))</span></th>
											<th class="first-head"><span>@sortablelink('node_title_ch',__('languages.nodes.node_title_chinese'))</span></th>
							          		<!-- <th class="first-head"><span>@sortablelink('node_description','Description')</span></th> -->
											<th class="sec-head selec-opt"><span>@sortablelink('weakness_name_en',__('languages.nodes.weakness_name_english'))</span></th>
											<th class="sec-head selec-opt"><span>@sortablelink('weakness_name_ch',__('languages.nodes.weakness_name_chinese'))</span></th>
                                            <th>@sortablelink('status',__('languages.status'))</th>
											<th>{{__('languages.action')}}</th>
							        	</tr>
							    	</thead>
							    	<tbody class="scroll-pane">
                                    @if(!empty($nodeList))
										@foreach($nodeList as $node)
							        	<tr>
											<td><input type="checkbox" name="" class="checkbox"></td>
                                            <td>{{ $node->node_id}}</td>
											<td>{{ $node->getParentNameById()}}</td>
											<td>{{ $node->node_title_en ?? '' }}</td>
											<td>{{ $node->node_title_ch ?? '' }}</td>
											<!-- <td>{{ $node->node_description}}</td> -->
                                            <td>{{ $node->weakness_name_en ?? '' }}</td>
											<td>{{ $node->weakness_name_ch ?? '' }}</td>
                                            <td>
												@if($node->status=="active")
												<span class="badge badge-success">{{__('languages.active')}}</span>
												@else
												<span class="badge badge-danger">{{__('languages.inactive')}}</span>
												@endif
											</td>
											<td class="btn-edit">
											@if (in_array('node_management_update', $permissions))
												<a href="{{ route('nodes.edit', $node->id) }}" class="" title="{{__('languages.edit')}}"><i class="fa fa-pencil" aria-hidden="true"></i></a>
											@endif
											@if (in_array('node_management_delete', $permissions))
												<a href="javascript:void(0);" class="pl-2" id="deleteNode" data-id="{{$node->id}}" title="{{__('languages.delete')}}"><i class="fa fa-trash" aria-hidden="true"></i></a>
											@endif
											</td>
										</tr>
										@endforeach
										@endif
							        </tbody>
							</table>
							<div>{{__('languages.showing')}} {{!empty($nodeList->firstItem()) ? $nodeList->firstItem() : 0}} {{__('languages.to')}} {{!empty($nodeList->lastItem()) ? $nodeList->lastItem() : 0}}
								{{__('languages.of')}}  {{$nodeList->total()}} {{__('languages.entries')}}
							</div>
								<div class="pagination-data">
									<div class="col-lg-9 col-md-9 pagintn">
										{{$nodeList->appends(request()->input())->links()}}
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
												<option value="{{$nodeList->total()}}" @if(app('request')->input('items') == $nodeList->total()) selected @endif >{{__('languages.all')}}</option>
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
					window.location = "{!! $nodeList->url(1) !!}&items=" + this.value;	
			}; 
		</script>
		@include('backend.layouts.footer')
@endsection