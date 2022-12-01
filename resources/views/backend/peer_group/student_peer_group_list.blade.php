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
								<h2 class="mb-4 main-title">{{__('languages.peer_group.peer_group')}}</h2>
							</div>
							<div class="sec-title">
								<a href="javascript:void(0);" class="btn-back" id="backButton">{{__('languages.back')}}</a>
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
					<form class="StudentPeerGroupFilterForm" id= "StudentPeerGroupFilterForm" method="GET">
						<div class="row">
							<div class="col-lg-2 col-md-2">
								<div class="select-lng pt-2 pb-2">
									<input type="text" class="input-search-box mr-2" name="searchName" id="searchName" value="{{request()->get('searchName')}}" placeholder="{{__('languages.search_by_name')}}">
									@if($errors->has('searchName'))
										<span class="validation_error">{{ $errors->first('searchName') }}</span>
									@endif
								</div>
							</div>
							<div class="col-lg-3 col-md-3">
								<div class="select-lng pt-2 pb-2">
									<select name="status" class="form-control" id="filterStatus">
										<option value="">{{__('languages.select')}}</option>
										<option value="1" {{(1 == request()->get('status')) ? 'selected' : ''}}>{{__('languages.active')}}</option>
										<option value="0" {{('0' == request()->get('status')) ? 'selected' : ''}}>{{__('languages.inactive')}}</option>
									</select>
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
											<th class="first-head"><span>{{__('languages.peer_group.sr_no')}}</span></th>
											<th class="first-head"><span>@sortablelink('group_name',__('languages.peer_group.group_name'))</span></th>
											<th class="selec-opt"><span>{{__('languages.peer_group.no_of_members')}}</span></th>
											<th>{{__('languages.created_user')}}</th>
                                            <th>@sortablelink('status',__('languages.peer_group.status'))</th>
											<th>{{__('languages.peer_group.action')}}</th>
							        	</tr>
							    	</thead>
							    	<tbody class="scroll-pane">
                                    @if(!empty($PeerGroupList))
										@foreach($PeerGroupList as $peerGroup)
							        	<tr>
											<td><input type="checkbox" name="" class="checkbox"></td>
											<td>{{$loop->iteration}}</td>
											<td>{{$peerGroup->group_name}}</td>
											<td>{{($peerGroup->members) ? count($peerGroup->members) : 'N/A'}}</td>
											<td>{{App\Helpers\Helper::FindRoleByUserId($peerGroup->created_by_user_id) ?? ''}}</td>
                                            <td>
												@if($peerGroup->status=="1")
													<span class="badge badge-success">{{__('languages.active')}}</span>
												@else
													<span class="badge badge-danger">{{__('languages.inactive')}}</span>
												@endif
											</td>
											<td class="btn-edit">
                                                <a href="javascript:void(0);" class="alp_chat_icon" data-AlpChatGroupId="{{$peerGroup->dreamschat_group_id}}" title="{{__('languages.peer_group.alp_chat')}}">
													<img src="{{asset('images/alp_chat.png')}}"/>
												</a>
											</td>
										</tr>
										@endforeach
										@endif
							        </tbody>
								</table>
								<div>{{__('languages.showing')}} {{!empty($PeerGroupList->firstItem()) ? $PeerGroupList->firstItem() : 0 }} {{__('languages.to')}} {{!empty($PeerGroupList->lastItem()) ? $PeerGroupList->lastItem() : 0}}
									{{__('languages.of')}}  {{$PeerGroupList->total()}} {{__('languages.entries')}}
								</div>
								<div class="pagination-data">
									<div class="col-lg-9 col-md-9 pagintn">
										{{$PeerGroupList->appends(request()->input())->links()}}
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
												<option value="{{$PeerGroupList->total()}}" @if(app('request')->input('items') == $PeerGroupList->total()) selected @endif >{{__('languages.all')}}</option>
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
				window.location = "{!! $PeerGroupList->url(1) !!}&items=" + this.value;	
			}; 
		</script>
		@include('backend.layouts.footer')
@endsection