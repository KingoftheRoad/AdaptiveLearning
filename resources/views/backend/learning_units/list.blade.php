@extends('backend.layouts.app')
    @section('content')
	@php
	$permissions = [];
	$user_id = auth()->user()->id;
	if($user_id){
		$learning_units_permission = App\Helpers\Helper::getPermissions($user_id);
		if($learning_units_permission && !empty($learning_units_permission)){
			$permissions = $learning_units_permission;
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
								<h2 class="mb-4 main-title">{{__('languages.learning_units_management.learning_units_detail')}}</h2>
								<div class="btn-sec">
									<a href="javascript:void(0);" class="btn-back dark-blue-btn btn btn-primary mb-4" id="backButton">{{__('languages.back')}}</a>
								@if (in_array('learning_units_management_create', $permissions))
									<a href="{{ route('learning_units.create') }}" class="dark-blue-btn btn btn-primary mb-4">{{__('languages.learning_units_management.add_new_learning_units')}}</a>
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
					<form class="addNodeFilterForm" id="addNodeFilterForm" method="get">
					<div class="row">
 						<div class="col-lg-4 col-md-3">
                            <div class="select-lng pt-2 pb-2">
                                <input type="text" class="input-search-box mr-2" name="Search" value="{{request()->get('Search')}}" placeholder="{{__('languages.learning_units_management.name')}}">
								@if($errors->has('Search'))
                                	<span class="validation_error">{{ $errors->first('Search') }}</span>
                            	@endif
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-4">
                            <div class="select-lng pt-2 pb-2">
                                <select name="strand_id" class="form-control select-search select-option">
                                    <option value="">{{ __('languages.strands') }}</option>
                                    @if(!empty($strands))
                                    @foreach($strands as $strand)
                                    <option value="{{$strand->id}}" {{ request()->get('strand_id') == $strand->id ? 'selected' : '' }}>{{$strand->name}}</option>
                                    @endforeach
                                    @endif
                                </select>
                                @if($errors->has('strand_id'))
                                    <span class="validation_error">{{ $errors->first('strand_id') }}</span>
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
							<div class="question-bank-sec">
								<table id="DataTable" class="display" style="width:100%">
                                <thead>
                                    <tr>
										<th><input type="checkbox" name="" class="checkbox" id="group-learning_units-ids"></th>
                                        <th class="first-head"><span>@sortablelink('name',__('languages.learning_units_management.name'))</span></th>
                                        <th class="first-head"><span>@sortablelink('name',__('languages.learning_units_management.strand_id'))</span></th>
                                        <th class="first-head"><span>@sortablelink('created_at',__('languages.created_at'))</span></th>
                                        <th class="first-head"><span>@sortablelink('status',__('languages.status'))</span></th>
                                        <th>{{__('languages.action')}}</th>
                                    </tr>
                                </thead>
                                <tbody class="scroll-pane">
                                @if(!empty($LearningsUnitsList))
								@foreach($LearningsUnitsList as $learning_units)
                                    <tr>
										<td><input type="checkbox" name="examids" class="checkbox learning_units-id" value="{{$learning_units->id}}"></td>
                                        <td>{{$learning_units->name}}</td>
                                        <td>{{$learning_units->Strands->name}}</td>
                                        <td>{{ str_replace('-','/',date('d-m-Y', strtotime($learning_units->created_at))) }}</td>
                                        <td>
												@if($learning_units->status == '1')
													<span class="badge badge-success">{{__('languages.active')}}</span>
												@else
													<span class="badge badge-danger">{{__('languages.inactive')}}</span>
												@endif
											</td>
                                        <td class="btn-edit">
											@if (in_array('learning_units_management_update', $permissions))
													<a href="{{ route('learning_units.edit', $learning_units->id) }}" class="" title="{{__('languages.edit')}}"><i class="fa fa-pencil" aria-hidden="true"></i></a>
											@endif
											@if (in_array('learning_units_management_delete', $permissions))
													<a href="javascript:void(0);" class="pl-2" id="deleteLearning_units" data-id="{{$learning_units->id}}" title="{{__('languages.delete')}}"><i class="fa fa-trash" aria-hidden="true"></i></a>
											@endif
										</td>
                                    </tr>
                                @endforeach
                                @endif
                                </tbody>
							</table>
                            <div>{{__('languages.showing')}} {{!empty($LearningsUnitsList->firstItem()) ? $LearningsUnitsList->firstItem() : 0}} {{__('languages.to')}} {{!empty($LearningsUnitsList->lastItem()) ? $LearningsUnitsList->lastItem() : 0}}
								{{__('languages.of')}}  {{$LearningsUnitsList->total()}} {{__('languages.entries')}}
							</div>
								<div class="pagination-data">
									<div class="col-lg-9 col-md-9 pagintn">
										{{$LearningsUnitsList->appends(request()->input())->links()}}
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
												<option value="{{$LearningsUnitsList->total()}}" @if(app('request')->input('items') == $LearningsUnitsList->total()) selected @endif >{{__('languages.all')}}</option>
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
				window.location = "{!! $LearningsUnitsList->url(1) !!}&items=" + this.value;
			};
		</script>
		@include('backend.layouts.footer')
@endsection
